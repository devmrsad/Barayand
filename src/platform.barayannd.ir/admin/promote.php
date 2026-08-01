<?php

session_start();
require_once "../functions.php";
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

$input = json_decode(file_get_contents('php://input'), true);
$idToBePromoted = $input["user_id"];

$updateToAdminQuery = "UPDATE users SET user_type = 'admin' WHERE user_id = ?";
$stmt = $pdo->prepare($updateToAdminQuery);
$success = $stmt->execute([$idToBePromoted]);

if($success) {
    if ($stmt->rowCount() > 0) {
        # در صورت ارتقا
        echo jn(returnOutput(null, ["user_id" => $idToBePromoted]));
    } else {
        # در صورت عدم ارتقا (از قبل ادمین بوده یا هیچ یوزری با این آیدی وجود نداشته)
        http_response_code(404);
        echo jn(returnOutput("User not found or already an admin."));
    }
}
else{
    http_response_code(400);
    echo jn(returnOutput("Database error"));
}
exit;