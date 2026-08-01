<?php

session_start();
require_once "../functions.php";
require_once "../jdf.php";
global $pdo;

if(!isset($_SESSION["user_id"])){
    http_response_code(401);
    echo jn(returnOutput("User not found - Invalid session"));
    exit;
}

# دریافت همه چت‌ های کاربر
$getChatIDsQuery = "SELECT chat_id FROM chats WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $pdo->prepare($getChatIDsQuery);
$stmt->execute([$_SESSION["user_id"]]);
$chatIDs = $stmt->fetchAll(PDO::FETCH_COLUMN);

if(!$chatIDs || count($chatIDs) === 0){
    echo jn(returnOutput(null, []));
    exit;
}

$messageItems = [];
$getSavedMessagesQuery = "SELECT * FROM messages WHERE (chat_id = ? AND saved = 1) AND (deleted = 0 AND sender = 'ai')";

foreach($chatIDs as $chatID){
    $stmt = $pdo->prepare($getSavedMessagesQuery);
    $stmt->execute([$chatID]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(count($messages) > 0){
        foreach($messages as $msg){
            $messageItems[] = $msg;
        }
    }
}

if(count($messageItems) === 0){
    # آرایه خالی فاقد پیام، بدون ارور
    echo jn(returnOutput(null, []));
    exit;
}

$messagesData = [];

foreach($messageItems as $message){
    $temp = [];

    $temp["id"] = $message["message_id"];
    $temp["content"] = $message["content"];

    # دریافت تاریخ پیام و تبدیل آن به شمسی
    $timestamp = strtotime($message["created_at"]);
    $tempDate = date("Y-m-d", $timestamp);
    $dateParts = explode("-", $tempDate);
    $jalaliDate = gregorian_to_jalali($dateParts[0], $dateParts[1], $dateParts[2], '/');

    $temp["date"] = $jalaliDate;

    $messagesData[] = $temp;
}

echo jn(returnOutput(null, $messagesData));
exit;