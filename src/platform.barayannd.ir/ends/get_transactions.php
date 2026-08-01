<?php

session_start();
require_once "../functions.php";
require_once "../jdf.php";
global $pdo;

if(!isset($_SESSION["user_id"])){
    http_response_code(401);
    echo jn(returnOutput("User not found - Invalid session"));
    exit;
}

$userID = $_SESSION["user_id"];

$getTransactionsQuery = "
    SELECT * FROM transactions WHERE portfolio_id =
    (SELECT portfolio_id FROM portfolios WHERE user_id = ? ORDER BY created_at ASC LIMIT 1)
    ORDER BY created_at DESC";

$stmt = $pdo->prepare($getTransactionsQuery);
$stmt->execute([$userID]);
$transactions = $stmt->fetchAll();

if(!$transactions){
    # دیتای خالی بدون ارور به کلاینت برگردانده شود
    echo jn(returnOutput(null, []));
    exit;
}

$transactionsData = [];

foreach ($transactions as $transaction) {
    $transactionItem = [];

    # محاسبه تاریخ و ساعت تراکنش
    $timestamp = $transaction['created_at'];
    $tnDate = date("Y-m-d", $timestamp);
    $tnTime = date("H:i", $timestamp); # زمان
    $tnDateParts = explode("-", $tnDate);
    $tnDateJalali = gregorian_to_jalali($tnDateParts[0], $tnDateParts[1], $tnDateParts[2], "/"); # تاریخ

    $transactionItem["type"] = $transaction['type'];
    $transactionItem["currency"] = $transaction["currency"];
    $transactionItem["amount"] = $transaction["amount"];
    $transactionItem["worth_toman"] = $transaction["worth_toman"];
    $transactionItem["date"] = $tnDateJalali;
    $transactionItem["time"] = $tnTime;

    $transactionsData[] = $transactionItem;
}

echo jn(returnOutput(null, $transactionsData));
exit;