<?php

session_start();
require_once "../functions.php";
global $pdo;

if(!isset($_SESSION["user_id"])){
    http_response_code(401);
    echo jn(returnOutput("Not logged in"));
    exit;
}
$userID = $_SESSION["user_id"];

$portfolio = getPortfolio($userID);

if(!$portfolio){
    # رکورد دارایی ها برای کاربر ایجاد شود
    $insertNewPortfolioQuery = "INSERT INTO portfolios(user_id, toman_amount) VALUES (?, ?);";
    $stmt = $pdo->prepare($insertNewPortfolioQuery);
    $stmt->execute([$userID, getstartingBudget($userID)]);
}

# تلاش مجدد برای دریافت دارایی ها
$portfolio = getPortfolio($userID);

if(!$portfolio){
    echo jn(returnOutput("Portfolio not found"));
    exit;
}

echo jn(returnOutput(null, ["portfolio_data" => $portfolio]));
exit;