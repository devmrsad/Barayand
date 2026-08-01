<?php

session_start();
header("Content-type: application/json");
require_once "../functions.php";
global $pdo;

# تابع کمکی برای بررسی تعلق پیام به کاربری که در سشن ثبت شده ( یک لایه امنیتی اضافه )
function messageBelongingCheck($userID, $messageID) {
    global $pdo;
    # دریافت چت مربوط به پیام درخواست شده
    $chatIDSelectorQuery = "SELECT chat_id FROM messages WHERE message_id = ?";
    $stmt = $pdo->prepare($chatIDSelectorQuery);
    $stmt->execute([$messageID]);
    $row = $stmt->fetch();

    $chatID = $row["chat_id"] ?? null;

    if($chatID) {
        $userIDSelectorQuery = "SELECT user_id FROM chats WHERE chat_id = ?";
        $stmt = $pdo->prepare($userIDSelectorQuery);
        $stmt->execute([$chatID]);
        $row = $stmt->fetch();

        $ChatsUserID = $row["user_id"] ?? null;

        if($ChatsUserID) {
            return $ChatsUserID == $userID;
        }
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo jn(returnOutput("method not allowed"));
    exit;
}

if(!isset($_SESSION["user_id"])){
    http_response_code(401);
    echo jn(returnOutput("User not found - Invalid session"));
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

# هر سه پارامتر ورودی ضروری هستند
if(!isset($input["message_id"]) || !isset($input["option"]) || !isset($input["change_to"])){
    http_response_code(400);
    echo jn(returnOutput("Missing request parameters"));
    exit;
}

# بررسی تعلق داشتن پیام به کاربر
$messageBelongs = messageBelongingCheck($_SESSION["user_id"], $input["message_id"]);

if($messageBelongs) {
    $messageID = $input["message_id"];
    $changeTo = $input["change_to"] ? 1 : 0;
    $option = $input["option"];

    # اگر آپشن ارسال شده، خارج از موارد استاندارد است
    if(!in_array($option, array("saved", "deleted"))) {
        http_response_code(400);
        echo jn(returnOutput("Invalid modification option"));
        exit;
    }

    if($option == "saved") {
        # اگر کاربر درخواست ذخیره یک پیام را دارد
        $updateMessageQuery = "UPDATE messages SET saved = ? WHERE message_id = ?";
        $stmt = $pdo->prepare($updateMessageQuery);
        $stmt->execute([$changeTo, $messageID]);
    }
    else{
        # اگر کاربر درخواست حذف یک پیام را دارد
        $updateMessageQuery = "UPDATE messages SET deleted = 1 WHERE message_id = ?";
        $stmt = $pdo->prepare($updateMessageQuery);
        $stmt->execute([$messageID]);
    }

    echo jn(returnOutput(null, ["message_id" => $messageID]));
}
else{
    echo jn(returnOutput("Message not found"));
}

exit;