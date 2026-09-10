<?php

session_start();
require_once("../functions.php");
header("Content-type: application/json");
global $pdo;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo jn(returnOutput("method not allowed"));
    exit;
}

if(!isset($_SESSION['user_id'])) {
    echo jn(returnOutput("User not logged in"));
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$aiAvailability = $input['aiAvailability'] ?? null;
$dailyAiMessages = $input['dailyAiMessages'] ?? null;
$trading = $input['trading'] ?? null;

# اگه همه ورودی ها نامشخص هستند
if($aiAvailability === null && $dailyAiMessages === null && $trading === null) {
    echo jn(returnOutput("All fields are missing!"));
    exit;
}

if($trading !== null) $trading = $trading === true ? 1 : 0;
if($aiAvailability !== null) $aiAvailability = $aiAvailability === true ? 1 : 0;

# ساخت اطلاعات مورد نیاز برای ثبت کانفیگ در سیستم **********

$getUserInfoQuery = "SELECT user_type, user_first_name, user_last_name FROM users WHERE user_id = ?";
$stmt = $pdo->prepare($getUserInfoQuery);
$stmt->execute([$_SESSION["user_id"]]);
$userInfo = $stmt->fetch();

if(!$userInfo) {
    echo jn(returnOutput("User not found"));
    exit;
}

if($userInfo["user_type"] !== "admin") {
    http_response_code(403);
    echo jn(returnOutput("Not allowed"));
    exit;
}

$firstName = $userInfo['user_first_name'];
$lastName = $userInfo['user_last_name'];


$createdByFieldValue = $userInfo['user_first_name']." ".$userInfo['user_last_name']." (".$_SESSION['user_id'].")";

# ***********

# ساخت کوئری و درج رکورد کانفیگ برای سایت

$cols = [];
$values = [];
$params = [];

if ($aiAvailability !== null) { $cols[] = 'ai'; $values[] = '?'; $params[] = $aiAvailability; }
if ($dailyAiMessages !== null) { $cols[] = 'daily_ai_messages'; $values[] = '?'; $params[] = $dailyAiMessages; }
if ($trading !== null) { $cols[] = 'trading_feature'; $values[] = '?'; $params[] = $trading; }

$cols[] = 'created_by';
$values[] = '?';
$params[] = $createdByFieldValue;

$sql = "INSERT INTO configs (".implode(',', $cols) . ") VALUES (". implode(',', $values) .")";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$configId = $pdo->lastInsertId();

echo jn(returnOutput(null, ["config_id" => $configId]));
exit;