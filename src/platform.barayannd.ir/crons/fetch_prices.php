<?php
date_default_timezone_set('Asia/Tehran');

try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=barayannd;charset=utf8", "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $err) {
    error_log($err->getMessage());
    die("Connection failed: " . $err->getMessage());
}

$maxRetries = 5;
$timeout = 10;

$cryptoData = null;
for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://Api.BrsApi.ir/Market/Cryptocurrency.php?key=BCBnDNYSSZs1CI4HiFtd3adRgzRG1atd");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    $response = curl_exec($ch);
    curl_close($ch);
    if ($response !== false) {
        $cryptoData = json_decode($response, true);
        break;
    }
    if ($attempt < $maxRetries - 1) sleep(1);
}

$marketData = null;
for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, "https://Api.BrsApi.ir/Market/Gold_Currency.php?key=BCBnDNYSSZs1CI4HiFtd3adRgzRG1atd");
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_TIMEOUT, $timeout);
    $response2 = curl_exec($ch2);
    curl_close($ch2);
    if ($response2 !== false) {
        $marketData = json_decode($response2, true);
        break;
    }
    if ($attempt < $maxRetries - 1) sleep(1);
}


function mapPricesToDesired($cryptoData, $marketData) {
    $mapping = [
        "Bitcoin"       => "btc",
        "Ethereum"      => "eth",
        "Tether"        => "usdt",
        "XRP"           => "xrp",
        "Binance Coin"  => "bnb",
        "Solana"        => "sol",
        "TRON"          => "tron",
        "Dogecoin"      => "doge",
        "Chainlink"     => "link",
        "Toncoin"       => "ton",
        "Cardano"       => "ada",
        "Tether Gold"   => "xaut",
        "Euro"          => "eur",
        "UAE Dirham"    => "dhm",
        "British Pound" => "gbp",
        "18K Gold"      => "grade_18_gold_gram",
        "24K Gold"      => "grade_24_gold_gram",
    ];

    $desiredKeys = [
        "usdt","btc","grade_18_gold_gram","grade_24_gold_gram","silver_gram",
        "gbp","eur","dhm","xaut","sol","eth","xrp","tron","link",
        "doge","ton","bnb","ada"
    ];

    $desiredPrices = array_fill_keys($desiredKeys, null);

    if (is_array($cryptoData)) {
        foreach ($cryptoData as $item) {
            $nameEn = $item['name_en'] ?? null;
            $price  = isset($item['price_toman']) ? floatval($item['price_toman'] / 1000) : null;
            if ($nameEn && isset($mapping[$nameEn])) {
                $desiredPrices[$mapping[$nameEn]] = $price;
            }
        }
    }

    $goldItems     = (is_array($marketData) && isset($marketData['gold'])) ? $marketData['gold'] : [];
    $currencyItems = (is_array($marketData) && isset($marketData['currency'])) ? $marketData['currency'] : [];

    foreach ($goldItems as $item) {
        $nameEn = $item['name_en'] ?? null;
        $price  = isset($item['price']) ? floatval($item['price'] / 1000) : null;
        if ($nameEn && isset($mapping[$nameEn])) {
            $desiredPrices[$mapping[$nameEn]] = $price;
        }
    }

    foreach ($currencyItems as $item) {
        $nameEn = $item['name_en'] ?? null;
        $price  = isset($item['price']) ? floatval($item['price'] / 1000) : null;
        if ($nameEn && isset($mapping[$nameEn])) {
            $desiredPrices[$mapping[$nameEn]] = $price;
        }
    }

    return $desiredPrices;
}

$prices = mapPricesToDesired($cryptoData, $marketData);

$columns = implode(", ", array_keys($prices));
$placeholders = implode(", ", array_fill(0, count($prices), "?"));

# ایجاد رکورد برای دیتا های دریافت شده
$sql = "INSERT INTO prices ($columns) VALUES ($placeholders)";
$stmt = $pdo->prepare($sql);
$stmt->execute(array_values($prices));

# پیام موفقیت آمیز
echo "Prices inserted successfully at " . date('Y-m-d H:i:s');
exit;