<?php

# از این فایل برای محاسبه مقدار سود یا زیان (در مقایسه با سرمایه درخواستی اولیه) بهمراه درصد آن استفاده میشود
session_start();
require_once "../functions.php";
global $pdo;

if(!isset($_SESSION["user_id"])){
    http_response_code(401);
    echo jn(returnOutput("User not found - Invalid session"));
    exit;
}

$userID = $_SESSION["user_id"];
# دریافت اطلاعات دارایی ها و فرمت کردن آنها
$portfolioData = getPortfolio($userID);
$totalBalance = 0;

if(!$portfolioData){
    http_response_code(404);
    echo jn(returnOutput("Unable to load portfolio"));
    exit;
}

foreach ($portfolioData as $item) {
    if (is_numeric($item['amount']) && is_numeric($item['unitPrice'])) {
        $totalBalance += $item['amount'] * $item['unitPrice'];
    }
}

$getStartingBudgetQuery = "SELECT starting_budget_toman FROM users WHERE user_id = ?";
$stmt = $pdo->prepare($getStartingBudgetQuery);
$stmt->execute([$userID]);

$row = $stmt->fetch();

if(!$row){
    http_response_code(404);
    echo jn(returnOutput("Unable to load user Data (Starting Budget)"));
    exit;
}

$startingBudget = $row["starting_budget_toman"];

$balanceChange = $totalBalance - $startingBudget;
$percentageChange = 0;
$type = "";

$percentageChange = ($balanceChange / $startingBudget) * 100;
if ($balanceChange >= 0) {
    $type = "profit";
} else{
    $type = "loss";
}

$output = [
    "value" => abs($balanceChange),
    "type" => $type,
    "percentage" => abs(round($percentageChange, 2))
];

echo jn(returnOutput(null, $output));
exit;