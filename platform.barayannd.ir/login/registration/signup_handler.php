<?php

session_start();
require_once "../../functions.php";
require_once "../../addresses.php";
global $pdo, $homepage;
# گزینه های مجاز برای درخواست سرمایه اولیه (این محدودیت در دیتابیس نیز اعمال شده است)
const BUDGET_OPTIONS = [10000, 50000, 100000, 250000, 1000000];

# اگر کاربر قبلا لاگین کرده
if(isset($_SESSION["user_id"])){
    header("Location: $homepage");
    exit;
}

# دیتای ورودی ناقص
if(empty($_POST["password"]) || empty($_POST["re_password"]) || empty($_POST["user_name"]) || empty($_POST["first_name"]) || empty($_POST["last_name"])){
    session_unset();
    session_destroy();
    header("Location: $homepage");
    exit("Incomplete Data");
}

$sessionDevice = $_SESSION["sessionDevice"] ?? null;
$sessionBrowser = $_SESSION["sessionBrowser"] ?? null;
$password = $_POST['password'];
$repassword = $_POST['re_password'];
$username = $_POST["user_name"];
$firstName = $_POST["first_name"];
$lastName = $_POST["last_name"];
$phoneNumber = $_SESSION['phone_number'] ?? null;
$startingBudget = $_POST["budget"] ?? null;

if($password != $repassword){
    session_unset();
    session_destroy();
    header("Location: $homepage");
    exit("Passwords do not match");
}

if(empty($phoneNumber)){
    session_unset();
    session_destroy();
    header("Location: $homepage");
    exit("Phone number not verified");
}

# اگر مقدار سرمایه اولیه خارج از انتخاب های استاندارد بود، کمترین مقدار اختصاص داده شود
if(!in_array($startingBudget, BUDGET_OPTIONS)){
    $startingBudget = BUDGET_OPTIONS[0];
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$insertUserQuery = "INSERT INTO users (username, password, user_first_name, user_last_name, phone_number, starting_budget_toman) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($insertUserQuery);

$stmt->execute([$username, $passwordHash, $firstName, $lastName, $phoneNumber, $startingBudget]);

if($stmt->rowCount() > 0){
    $userID = $pdo->lastInsertId();
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

# در نهایت به صفحه خانه منتقل شود
header("Location: $homepage");
exit;