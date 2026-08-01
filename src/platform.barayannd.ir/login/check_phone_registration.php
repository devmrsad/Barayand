<?php

session_start();
header("content-type: application/json");
require_once("../functions.php");
global $pdo;

$input = json_decode(file_get_contents('php://input'), true);

$_SESSION["sessionDevice"] = $input["sessionDevice"] ?? null;
$_SESSION["sessionBrowser"] = $input["sessionBrowser"] ?? null;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo jn(returnOutput("Method not allowed"));
    exit;
}

if(!isset($input["phone_number"])){
    http_response_code(400);
    echo jn(returnOutput("phone number is required"));
    exit;
}

if(!preg_match('/^09\d{9}$/', $input["phone_number"])){
    http_response_code(400);
    echo jn(returnOutput("phone number is invalid"));
    exit;
}

# بررسی وجود شماره موبایل - کوئری پارامتریک برای جلوگیری از SQL Injection
$numberSearchQuery = "SELECT COUNT(user_id) AS count FROM users WHERE phone_number = ?";
$stmt = $pdo->prepare($numberSearchQuery);
$stmt->execute([$input["phone_number"]]);
$result = $stmt->fetch();

if($result["count"] > 0){
    echo jn(returnOutput("already_registered"));
    exit;
}


$_SESSION['phone_number'] = $input["phone_number"];

echo jn(returnOutput(null, ["status" => "ok"]));
exit;