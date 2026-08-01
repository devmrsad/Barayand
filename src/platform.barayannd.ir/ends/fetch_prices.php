<?php
date_default_timezone_set('Asia/Tehran');

try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=barayannd;charset=utf8", "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $err) {
    error_log($err->getMessage());
    die("Connection failed: " . $err->getMessage());
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://Api.BrsApi.ir/Market/Cryptocurrency.php?key=BCBnDNYSSZs1CI4HiFtd3adRgzRG1atd");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$cryptoData = json_decode($response, true);
if ($cryptoData === null && json_last_error() !== JSON_ERROR_NONE) {
    die("JSON decode error: " . json_last_error_msg());
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://Api.BrsApi.ir/Market/Gold_Currency.php?key=BCBnDNYSSZs1CI4HiFtd3adRgzRG1atd");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);


$marketData = json_decode($response, true);
echo $marketData; exit;


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
        "Euro" => "eur",
        "UAE Dirham" => "dhm",
        "British Pound" => "gbp",
        "18K Gold" => "grade_18_gold_gram",
        "24K Gold" => "grade_24_gold_gram",
    ];

    $desiredKeys = [
        "usdt","btc","grade_18_gold_gram","grade_24_gold_gram","silver_gram",
        "gbp","eur","dhm","xaut","sol","eth","xrp","tron","link",
        "doge","ton","bnb","ada"
    ];

    $desiredPrices = array_fill_keys($desiredKeys, null);

    foreach ($cryptoData as $item) {
        $nameEn = $item['name_en'] ?? $item->name_en ?? null;
        $price = floatval($item['price_toman'] / 1000) ?? $item->price_toman ?? null;
        if ($nameEn && isset($mapping[$nameEn])) {
            $desiredKey = $mapping[$nameEn];
            $desiredPrices[$desiredKey] = (float)$price;
        }
    }

    foreach ($marketData as $item) {
        $nameEn = $item['name_en'] ?? $item->name_en ?? null;
        $price = floatval($item['price'] / 1000) ?? $item->price ?? null;
        if ($nameEn && isset($mapping[$nameEn])) {
            $desiredKey = $mapping[$nameEn];
            $desiredPrices[$desiredKey] = (float)$price;
        }
    }

    return $desiredPrices;
}

$prices = mapPricesToDesired($cryptoData, $marketData);

$columns = implode(", ", array_keys($prices));
$placeholders = implode(", ", array_fill(0, count($prices), "?"));

$sql = "INSERT INTO prices ($columns) VALUES ($placeholders)";
$stmt = $pdo->prepare($sql);
$stmt->execute(array_values($prices));

echo "✅ Prices inserted successfully at " . date('Y-m-d H:i:s');
