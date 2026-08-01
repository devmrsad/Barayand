<?php

# جهت دریافت کل سرمایه موجود
session_start();
require_once "../functions.php";
global $pdo;

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo jn(returnOutput("User not found - Invalid session"));
    exit;
}

$userID = $_SESSION["user_id"];
# دریافت اطلاعات دارایی ها و فرمت کردن آنها
$portfolioData = getPortfolio($userID);
$totalBalance = 0;


if ($portfolioData) {
    foreach ($portfolioData as $item) {
        if (is_numeric($item['amount']) && is_numeric($item['unitPrice'])) {
            $totalBalance += $item['amount'] * $item['unitPrice'];
        }
    }
} else {
    # اگر رکورد دارایی ها وجود نداشت، سرمایه درخواستی کاربر در هنگام ثبتنام، بعنوان سرمایه کل نشان داده شود
    $startingBudget = getStartingBudget($userID);
    $totalBalance = is_numeric($startingBudget) ? floatval($startingBudget) : 0;
}


echo jn(returnOutput(null, ["total_balance" => $totalBalance]));
exit;