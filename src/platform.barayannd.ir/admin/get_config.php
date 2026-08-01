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

# دریافت آخرین کانفیگ (تنظیم)
$getLatestConfigQuery = "SELECT * FROM configs ORDER BY created_at DESC LIMIT 1";
$stmt = $pdo->prepare($getLatestConfigQuery);
$stmt->execute();
$config = $stmt->fetch();

if(!$config){
    http_response_code(404);
    echo jn(returnOutput("Config not found"));
    exit;
}

# محاسبه تاریخ و ساعت تنظیم
$timestamp = $config['created_at'];
$timestamp = strtotime($timestamp);
$date = date("Y-m-d", $timestamp);
$time = date("H:i", $timestamp); # زمان
$dateParts = explode("-", $date);
$dateJalali = gregorian_to_jalali($dateParts[0], $dateParts[1], $dateParts[2], "/"); # تاریخ

$createdBy = $config['created_by'] === "SYSTEM" ? "سیستم" : $config['created_by']; #تنظیم کننده
$ai = $config['ai'] === 1; # قابلیت پیام دادن به هوش مصنوعی
$trading = $config['trading_feature'] === 1; # قابلیت انجام معاملات
$aiLimit = $config['daily_ai_messages']; # حداکثر تعداد پیام قابل ارسال به هوش مصنوعی

$output = ["last_edition_time" => $time, "last_edition_date" => $dateJalali, "last_edition_by" => $createdBy, "daily_ai_limit" => $aiLimit, "ai_enabled" => $ai, "trading_feature_enabled" => $trading];


echo jn(returnOutput(null, $output));
exit;