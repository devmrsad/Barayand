<?php

session_start();
require_once "../functions.php";
global $pdo;

if(!isset($_SESSION["user_id"])){
    http_response_code(401);
    echo jn(returnOutput("User not found - Invalid session"));
    exit;
}

$getUserInfoQuery = "SELECT username, phone_number, user_first_name, user_last_name, starting_budget_toman FROM users WHERE user_id = ?";
$stmt = $pdo->prepare($getUserInfoQuery);
$stmt->execute([$_SESSION["user_id"]]);
$userInfo = $stmt->fetch();

if(!$userInfo){
    http_response_code(404);
    echo jn(returnOutput("User not found"));
    exit;
}

$dataArray = [];

$dataArray["username"] = $userInfo["username"];
$dataArray["phone_number"] = $userInfo["phone_number"];
$dataArray["user_name"] = $userInfo["user_first_name"]. " " . $userInfo["user_last_name"];
$dataArray["starting_budget"] = $userInfo["starting_budget_toman"];

echo jn(returnOutput(null, $dataArray));
exit;