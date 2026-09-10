<?php

date_default_timezone_set('Asia/Tehran');
global $pdo;

try{
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=barayannd;charset=utf8", "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,]);
} catch(PDOException $err){
    error_log($err->getMessage());
    die("Connection failed: " . $err->getMessage());
}

$stmt = $pdo->query("SELECT * FROM prices ORDER BY price_set_id DESC LIMIT 1");
$prices = $stmt->fetch(PDO::FETCH_ASSOC);

# اگر هیچ رکورد قیمتی وجود نداشت
if (!$prices) {
    $prices = [];
}

function priceOrZero($prices, $key) {
    return isset($prices[$key]) ? (float)$prices[$key] : 0;
}

# دریافت دارایی های همه کاربران
$portfolios = $pdo->query("SELECT * FROM portfolios")->fetchAll(PDO::FETCH_ASSOC);

$insertScreenshotQuery = "INSERT INTO screenshots (user_id, type, total_balance_toman, total_assets_worth_toman) VALUES (?, 'daily', ?, ?)";
$stmt = $pdo->prepare($insertScreenshotQuery);

foreach ($portfolios as $p) {
    # ارزش ارز های خریداری شده
    $assetsWorth = 0;

    $fields = [
        'usdt_amount' => 'usdt',
        'btc_amount' => 'btc',
        'grade_18_gold_gram_amount' => 'grade_18_gold_gram',
        'grade_24_gold_gram_amount' => 'grade_24_gold_gram',
        'silver_gram_amount' => 'silver_gram',
        'gbp_amount' => 'gbp',
        'eur_amount' => 'eur',
        'dhm_amount' => 'dhm',
        'xaut_amount' => 'xaut',
        'sol_amount' => 'sol',
        'eth_amount' => 'eth',
        'xrp_amount' => 'xrp',
        'tron_amount' => 'tron',
        'link_amount' => 'link',
        'doge_amount' => 'doge',
        'ton_amount' => 'ton',
        'bnb_amount' => 'bnb',
        'ada_amount' => 'ada'
    ];

    foreach ($fields as $portfolioField => $priceField) {
        $amount = (float)$p[$portfolioField];
        $price  = priceOrZero($prices, $priceField);
        $assetsWorth += $amount * $price;
    }

    # کل دارایی به تومان
    $tomanBalance = (float)$p['toman_amount'] + $assetsWorth;

    # ثبت اسکرین شات از وضعیت دارایی های کاربر مربوطه
    $stmt->execute([$p['user_id'], $tomanBalance, $assetsWorth]);
}

exit;