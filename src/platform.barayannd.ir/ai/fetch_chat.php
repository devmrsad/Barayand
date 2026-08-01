<?php

session_start();
require_once "../functions.php";
global $pdo;

$now = date("H:i:d");
$today = date("Y-m-d");

if(!isset($_SESSION["user_id"])){
    http_response_code(401);
    echo jn(returnOutput("User not found - Invalid session"));
    exit;
}

$selectLastChatQuery = "SELECT * FROM chats WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $pdo->prepare($selectLastChatQuery);
$stmt->execute([$_SESSION["user_id"]]);
$row = $stmt->fetch();

if($row && $row["chat_date"] === $today){
    $selectChatMessagesQuery = "SELECT `message_id`, `content`, `sender`, `time`, `saved` FROM messages WHERE chat_id = ? AND deleted = 0 ORDER BY created_at ASC";
    $stmt = $pdo->prepare($selectChatMessagesQuery);
    $stmt->execute([$row["chat_id"]]);
    $rows = $stmt->fetchAll();

    if (count($rows) === 0) {
        # اگر چت برای امروز وجود دارد اما پیامی در آن ارسال نشده
        echo jn(returnOutput(null, []));
        exit;
    }

    # ساخت دیتای خروجی پیام ها
    $messages = [];

    foreach ($rows as $msg) {
        $messages[] = [
            "id" => $msg["message_id"],
            "message" => $msg["content"],
            "sender"  => $msg["sender"],
            "time"    => substr($msg["time"], 0, 5), // فقط ساعت و دقیقه
            "saved"   => (bool)$msg["saved"],
        ];
    }

    echo jn(returnOutput(null, $messages));
}
# اگر برای امروز چتی وجود نداشت، یکی ایجاد میکنیم و یک آرایع خالی بعنوان پیام هاش برمیگردونیم
else{
    $insertNewChatQuery = "INSERT INTO chats (user_id, chat_date) VALUES (?, ?)";
    $stmt = $pdo->prepare($insertNewChatQuery);
    $stmt->execute([$_SESSION["user_id"], $today]);

    echo jn(returnOutput(null, []));
}
exit;