<?php

session_start();
header("Content-type: application/json");
require_once("../functions.php");
global $pdo;

$input = json_decode(file_get_contents('php://input'), true);

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

# اکر قابلیت ترید کردن غیرفعال بود
if(!tradingFeature()){
    http_response_code(401);
    echo jn(returnOutput("Trading not allowed"));
    exit;
}

$userID = $_SESSION["user_id"];
# دریافت وضعیت دارایی ها
$getPortfolioID = $pdo->prepare("SELECT portfolio_id FROM portfolios WHERE user_id = ? ORDER BY portfolio_id DESC LIMIT 1");
$getPortfolioID->execute([$userID]);

$row = $getPortfolioID->fetch();
$portfolioID = $row["portfolio_id"] ?? null;

if(!$portfolioID){
    # رکورد دارایی ها برای کاربر ایجاد شود
    $insertNewPortfolioQuery = "INSERT INTO portfolios(user_id, toman_amount) VALUES (?, ?);";
    $stmt = $pdo->prepare($insertNewPortfolioQuery);
    $stmt->execute([$userID, getstartingBudget($userID)]);

    # تلاش مجدد برای دریافت آیدی دارایی ها
    $portfolioID = $pdo->lastInsertId() ?? null;
}

if(!$portfolioID){
    http_response_code(500);
    echo jn(returnOutput("Failed to create portfolio"));
    exit;
}

$allowedCurrencies = ['usdt','btc','grade_18_gold_gram','grade_24_gold_gram','silver_gram','gbp','eur','dhm','xaut','sol','eth','xrp','tron','link','doge','ton','bnb','ada'];

$currency = $input["currency"] ?? null;
$amount = $input["amount"] ?? null;

if(!$amount || !is_numeric($amount) || $amount <= 0 || !$currency || !is_string($currency) || !in_array($currency, $allowedCurrencies)){
    http_response_code(400);
    echo jn(returnOutput("Invalid or incomplete parameters"));
    exit;
}

$currencyPrice = getLatestPrices()[$currency];

if(!$currencyPrice){
    http_response_code(400);
    echo jn(returnOutput("No price for this currency"));
    exit;
}

# محاسبه قیمت مقدار ارز خریداری شده
$priceInToman = $amount * $currencyPrice;

# دریافت و مقایسه موجودی ارز در کیف پول کاربر با مقدار فروش
$checkTomanAvailabilityQuery = "SELECT toman_amount FROM portfolios WHERE portfolio_id = ?;";
$stmt = $pdo->prepare($checkTomanAvailabilityQuery);
$stmt->execute([$portfolioID]);
$row = $stmt->fetch();

if(!$row){
    http_response_code(400);
    echo jn(returnOutput("Invalid currency"));
    exit;
}

if($row["toman_amount"] < $priceInToman){
    # موجودی ناکافی تومان
    http_response_code(400);
    echo jn(returnOutput("Insufficient balance"));
    exit;
}

# مقدار جدید موجودی تومان پس از خرید
$newBalance = $row["toman_amount"] - $priceInToman;

# ست کردن موجودی جدید ارز
$buyQuery = "UPDATE portfolios SET ".$currency."_amount"." = ".$currency."_amount"." + ? WHERE portfolio_id = ?;";
$stmt = $pdo->prepare($buyQuery);
$stmt->execute([$amount, $portfolioID]);

# کاهش از موجودی تومان
$reduceTomanQuery = "UPDATE portfolios SET toman_amount = toman_amount - ? WHERE portfolio_id = ?;";
$stmt = $pdo->prepare($reduceTomanQuery);
$stmt->execute([$priceInToman, $portfolioID]);

# ذخیره کردن تراکنش در دیتابیس
$insertTransactionQuery = "INSERT INTO transactions(portfolio_id, type, currency, amount, worth_toman) VALUES (?, ?, ?, ?, ?);";
$stmt = $pdo->prepare($insertTransactionQuery);
$stmt->execute([$portfolioID, 'buy', $currency, $amount, $priceInToman]);

$transactionID = $pdo->lastInsertId() ?? null;

echo jn(returnOutput(null, ["transaction_id" => $transactionID]));
exit;