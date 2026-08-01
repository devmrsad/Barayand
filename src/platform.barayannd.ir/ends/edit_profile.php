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

$userID = $_SESSION["user_id"];
$input = json_decode(file_get_contents('php://input'), true);

$username = $input["username"] ?? null;
$currentPassword = $input["current_password"] ?? null;
$password = $input["password"] ?? null;
$rePassword = $input["repassword"] ?? null;

if($currentPassword && $password && $rePassword){
    $getPasswordQuery = "SELECT password FROM users WHERE user_id = ? LIMIT 1";
    $stmt = $pdo->prepare($getPasswordQuery);
    $stmt->execute([$userID]);
    $row = $stmt->fetch();
    $currentPasswordHash = $row["password"];

    if(password_verify($currentPassword, $currentPasswordHash)){
        if($password === $rePassword){
            $updatePasswordQuery = "UPDATE users SET password = ? WHERE user_id = ? LIMIT 1";
            $stmt = $pdo->prepare($updatePasswordQuery);
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt->execute([$passwordHash, $userID]);
        }
        else{
            echo jn(returnOutput("Passwords do not match"));
            exit;
        }
    }
    else{
        echo jn(returnOutput("Wrong current password"));
        exit;
    }
}

if($username){
    $usernameAvailableQuery = "SELECT count(username) as count FROM users WHERE username = ? LIMIT 1";
    $stmt = $pdo->prepare($usernameAvailableQuery);
    $stmt->execute([$username]);
    $userNameUsed = $stmt->fetch()["count"];

    # بررسی در دسترس بودن یوزرنیم درخواستی
    if($userNameUsed > 0){
        echo jn(returnOutput("Username already in use"));
        exit;
    }

    $updateUsernameQuery = "UPDATE users SET username = ? WHERE user_id = ? LIMIT 1";
    $stmt = $pdo->prepare($updateUsernameQuery);
    $stmt->execute([$username, $userID]);
}

echo jn(returnOutput(null, "Successfully updated"));
exit;