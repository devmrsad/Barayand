<?php

session_start();
header("Content-type: application/json");
require_once "../functions.php";
global $pdo;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo jn(returnOutput("method not allowed"));
    exit;
}

if(!isset($_SESSION["user_id"])){
    http_response_code(401);
    echo jn(returnOutput("User not found - Invalid session"));
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

$userIDToBeDeleted = $input["userID"];

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

# حذف کاربر درخواست شده
$deleteUserQuery = "DELETE FROM users WHERE user_id = ?";
$stmt = $pdo->prepare($deleteUserQuery);
$stmt->execute([$userIDToBeDeleted]);

echo jn(returnOutput(null, ["deleted_user_id" => $userIDToBeDeleted]));
exit;