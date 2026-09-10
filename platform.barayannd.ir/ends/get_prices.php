<?php

require_once "../functions.php";
header('Content-Type: application/json');
global $pdo;

# پارامتر های قابل دریافت توسط متد GET
$allowed_columns = [
    'usdt', 'btc', 'grade_18_gold_gram', 'grade_24_gold_gram', 'silver_gram',
    'gbp', 'eur', 'dhm', 'xaut', 'sol', 'eth', 'xrp', 'tron', 'link',
    'doge', 'ton', 'ada' , 'bnb'
];

$param = $_GET['currency'] ?? null;
$getPricesQuery = null;
$getPrices = null;

$error = $data = null;
function calculate_change($current_price, $previous_price){
    if ($previous_price === null || $current_price === null) {
        return ["percentage" => 0, "type" => "increase"];
    }

    # درصد تغییر
    if ($previous_price == 0) {
        if ($current_price > 0) {
            return ["percentage" => ($current_price > 0 ? 999 : 0), "type" => "increase"];
        } else {
            return ["percentage" => 0, "type" => "increase"];
        }
    }

    $difference = $current_price - $previous_price;
    $percentage = round(($difference / $previous_price) * 100, 1);

    if ($percentage >= 0) {
        return ["percentage" => abs($percentage), "type" => "increase"];
    } else {
        return ["percentage" => abs($percentage), "type" => "decrease"];
    }
}


try {
    $fetchStartTime = date('Y-m-d H:i:s', strtotime('-48 hours'));

    if ($param) {
        if (!in_array($param, $allowed_columns, true)) {
            $error = "Invalid currency parameter.";
            throw new Exception("Invalid currency parameter");
        }
        $currencies = [$param];
    } else {
        $currencies = $allowed_columns;
    }

    $columnsList = implode(', ', $currencies);
    $query = "SELECT retrieval_timestamp, {$columnsList} FROM prices WHERE retrieval_timestamp >= ? ORDER BY retrieval_timestamp DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$fetchStartTime]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $currentPrices     = [];
    $currentTimestamps = [];
    foreach ($currencies as $col) {
        $currentPrices[$col]     = null;
        $currentTimestamps[$col] = null;
    }

    foreach ($rows as $row) {
        $ts = $row['retrieval_timestamp'];
        foreach ($currencies as $col) {
            if ($currentPrices[$col] === null) {
                $val = $row[$col] ?? null;
                if ($val !== null) {
                    $currentPrices[$col]     = (float)$val;
                    $currentTimestamps[$col] = $ts;
                }
            }
        }
    }

    foreach ($currencies as $col) {
        $currentPrice = $currentPrices[$col];
        $currentTs    = $currentTimestamps[$col];

        if ($currentPrice === null) {
            $data[$col] = [
                "price" => null,
                "retrieval_timestamp" => null,
                "change" => calculate_change(null, null)
            ];
            continue;
        }

        $cutoff = date('Y-m-d H:i:s', strtotime($currentTs . ' -24 hours'));
        $previousPrice = null;

        foreach ($rows as $row) {
            $val = $row[$col] ?? null;
            if ($val !== null && $row['retrieval_timestamp'] <= $cutoff) {
                $previousPrice = (float)$val;
                break;
            }
        }

        if ($previousPrice === null) {
            $oldestPrice = null;
            for ($i = count($rows) - 1; $i >= 0; $i--) {
                $val = $rows[$i][$col] ?? null;
                if ($val !== null) {
                    $oldestPrice = (float)$val;
                    break;
                }
            }
            $previousPrice = $oldestPrice;
        }

        $change = calculate_change($currentPrice, $previousPrice);

        $data[$col] = [
            "price" => $currentPrice,
            "retrieval_timestamp" => $currentTs,
            "change" => $change
        ];
    }

    if (empty($data)) {
        $error = "No price data found";
    }

} catch (Exception $e) {
    error_log("Input validation error: " . $e->getMessage());
}

echo jn(returnOutput($error, $data));
exit;