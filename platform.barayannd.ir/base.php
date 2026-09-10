<?php

date_default_timezone_set('Asia/Tehran');
require_once "addresses.php";
global $pdo, $connection_error_page;

# درصورتی که کانفیگی برای سایت در دیتابیس نباشد، یک کانفیگ ایجاد میشود
function initiateConfig(){
    global $pdo;

    # شمارش تعداد رکورد های جدول configs
    $configsCountQuery = "SELECT COUNT(*) AS count FROM configs";
    $stmt = $pdo->prepare($configsCountQuery);
    $stmt->execute();

    $row = $stmt->fetch();
    $configsCount = $row["count"];

    if($configsCount == 0){
        # اضافه کردن اولین کانفیگ به دیتابیس با نقش سیستم
        $defaultConfigQuery = "INSERT INTO configs (created_by) VALUES (?)";
        $stmt = $pdo->prepare($defaultConfigQuery);
        $stmt->execute(["SYSTEM"]);
    }
}

# اگر هیچ کاربری در سایت بعنوان ادمین انتخاب نشده باشد، اولین کاربر در دیتابیس بعنوان ادمین ست میشود
function initiateAdmin(){
    global $pdo;

    # شمارش تعداد ادمین ها
    $adminsCountQuery = "SELECT COUNT(*) as count FROM users WHERE user_type = 'admin'";
    $stmt = $pdo->prepare($adminsCountQuery);
    $stmt->execute();
    $adminsCount = $stmt->fetch();

    $adminsCount = $adminsCount["count"];

    # اگر اولین ادمین قبلا اضافه شده
    if($adminsCount == 0) {
        # ادمین کردن اولین یوزر موجود در دیتابیس
        $setAsAdminQuery = "UPDATE users SET user_type = 'admin' WHERE user_id = (SELECT user_id FROM users ORDER BY created_at ASC LIMIT 1)";
        $stmt = $pdo->prepare($setAsAdminQuery);
        $stmt->execute();
    }
}

try{
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=barayannd;charset=utf8", "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,]);
    initiateAdmin();
    initiateConfig();
} catch(PDOException $err){
    header("location: $connection_error_page");
    exit();
}