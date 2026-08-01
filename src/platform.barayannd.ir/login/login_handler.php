<?php
session_start();
require_once "../addresses.php";
require_once "../functions.php";
global $pdo, $homepage;

if(isset($_SESSION["user_id"])){
    header("Location: $homepage");
    exit("Already logged in");
}

# منظور از identifier شماره موبایل کاربر می باشد
$identifier = $_POST["identifier"] ?? null;

$sessionDevice = $_SESSION["sessionDevice"] ?? null;
$sessionBrowser = $_SESSION["sessionBrowser"] ?? null;

# وارد کردن شماره موبایل ضروری میباشد
if(!$identifier){
    session_unset();
    session_destroy();
    header("Location: $homepage");
    exit;
}

# بررسی وجود کاربر با شماره موبایل - بدون رمز عبور یا کد تایید
$checkUserQuery = "SELECT user_id FROM users WHERE phone_number = ? LIMIT 1";
$stmt = $pdo->prepare($checkUserQuery);
$stmt->execute([$identifier]);
$row = $stmt->fetch();

if($row){
    $userID = $row["user_id"];
    # سشن جدید در دیتابیس ایجاد شود
    $sessionToken = generateSessionToken($userID, $sessionDevice, $sessionBrowser) ?? null;
    if($sessionToken){
        $_SESSION["user_id"] = $userID;
        $_SESSION["session_token"] = $sessionToken;
    }
    else{
        session_unset();
        session_destroy();
    }
}

header("location: $homepage");
exit;