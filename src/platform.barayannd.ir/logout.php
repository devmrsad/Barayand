<?php

session_start();
require_once "./base.php";
require_once("./addresses.php");
global $pdo, $homepage;

$sessionToken = $_SESSION["session_token"] ?? null;

if(isset($_SESSION["session_token"])){
    $terminateSessionQuery = "UPDATE sessions SET session_active = 0 WHERE session_token = ?";
    $stmt = $pdo->prepare($terminateSessionQuery);
    $stmt->execute([$sessionToken]);
}

session_unset();
session_destroy();

header("Location: $homepage");
exit;