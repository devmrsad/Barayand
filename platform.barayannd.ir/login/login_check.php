<?php

session_start();
header("Content-Type: application/json");
require_once "../functions.php";
global $pdo, $homepage;

if(isset($_SESSION["user_id"])){
    echo jn(returnOutput("Already logged in"));
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$_SESSION["sessionDevice"] = $input["sessionDevice"] ?? null;
$_SESSION["sessionBrowser"] = $input["sessionBrowser"] ?? null;

$identifier = $input["identifier"] ?? null;

# وارد کردن شماره موبایل ضروری میباشد
if(!$identifier){
    echo jn(returnOutput("Phone number is required!"));
    exit;
}

# بررسی وجود کاربر با شماره موبایل - بدون رمز عبور یا کد تایید
$checkUserQuery = "SELECT user_id FROM users WHERE phone_number = ? LIMIT 1";
$stmt = $pdo->prepare($checkUserQuery);
$stmt->execute([$identifier]);
$row = $stmt->fetch();

if($row){
    echo jn(returnOutput(null, "login successful"));
    exit;
}

echo jn(returnOutput("not_registered"));
exit;