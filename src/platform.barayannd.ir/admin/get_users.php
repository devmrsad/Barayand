<?php

session_start();
header("Content-type: application/json");
require_once "../functions.php";
require_once "../jdf.php";
global $pdo;

if(!isset($_SESSION['user_id'])) {
    echo jn(returnOutput("User not logged in"));
    exit;
}

# بررسی ادمین بودن کاربر فعلی
$getUserInfoQuery = "SELECT user_type FROM users WHERE user_id = ?";
$stmt = $pdo->prepare($getUserInfoQuery);
$stmt->execute([$_SESSION["user_id"]]);
$row = $stmt->fetch();

if(!$row) {
    http_response_code(404);
    echo jn(returnOutput("User not found"));
    exit;
}

if($row["user_type"] !== "admin") {
    http_response_code(403);
    echo jn(returnOutput("Not allowed"));
    exit;
}

# انتخاب دیتای کاربران
$selectUsersQuery = "SELECT user_id, user_type, phone_number, user_first_name, user_last_name FROM users";
$stmt = $pdo->prepare($selectUsersQuery);
$stmt->execute();
$users = $stmt->fetchAll();

# ساخت خروجی نهایی
$usersData = [];

foreach($users as $user) {
    $userData = [];

    $userData["user_id"] = $user["user_id"];
    $userData["user_name"] = $user["user_first_name"]." ".$user["user_last_name"];
    $userData["user_type"] = $user["user_type"];
    $userData["phone_number"] = $user["phone_number"];

    # دریافت وضعیت آخرین فعالیت کاربر در سایت
    $getUserActivityQuery = "SELECT session_last_active FROM sessions WHERE user_id = ?";
    $stmt = $pdo->prepare($getUserActivityQuery);
    $stmt->execute([$user["user_id"]]);
    $row = $stmt->fetch();

    if(!$row) {
        $userData["last_active"] = "نامشخص";
    }
    else{
        $timestamp = strtotime($row["session_last_active"]);
        $date = date("Y-m-d", $timestamp);
        $time = date("H:i", $timestamp);
        $dateParts = explode("-", $date);
        $jDate = gregorian_to_jalali($dateParts[0], $dateParts[1], $dateParts[2], '/');

        $userData["last_active"] = $jDate." | ".$time;
    }

    $usersData[] = $userData;
}

echo jn(returnOutput(null, $usersData));
exit;