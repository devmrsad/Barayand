<?php

require_once "base.php";

# تابعی برای انجام کار تابع json_encode
function jn($expression){
    return json_encode($expression);
}

# تابعی برای برگرداندن یک ساختار ثابت و تعریف شده برای تعاملات
function returnOutput($error = null, $data = null)
{
    if($error){
        return ["success" => false, "error" => $error, "data" => null];
    }
    else if(!$data && $data != []){
        return ["success" => false, "error" => "Unreachable data", "data" => null];
    }
    else{
        return ["success" => true, "error" => null, "data" => $data];
    }
}

function getMaxDailyAiMessages() {
    # دریافت حداکثر پیام های قابل پردازش توسط هوش مصنوعی در هر روز برای هر کاربر
    global $pdo;
    $getMaxDailyAiMessagesQuery = "SELECT daily_ai_messages FROM configs WHERE daily_ai_messages IS NOT NULL ORDER BY created_at DESC LIMIT 1";
    $stmt = $pdo->prepare($getMaxDailyAiMessagesQuery);
    $stmt->execute();

    $row = $stmt->fetch();
    return $row["daily_ai_messages"] ?? 15;
}

function aiAvailability() {
    # دریافت در دسترس بودن یا نبودن قابلیت پیام به هوش مصنوعی
    global $pdo;
    $getAiAvailabilityQuery = "SELECT ai FROM configs WHERE ai IS NOT NULL ORDER BY created_at DESC LIMIT 1";
    $stmt = $pdo->prepare($getAiAvailabilityQuery);
    $stmt->execute();

    $row = $stmt->fetch();
    return (bool)$row["ai"] ?? true;
}

function tradingFeature() {
    # دریافت وضعیت فعال بودن قابلیت خرید و فروش
    global $pdo;
    $getAiAvailabilityQuery = "SELECT trading_feature FROM configs WHERE trading_feature IS NOT NULL ORDER BY created_at DESC LIMIT 1";
    $stmt = $pdo->prepare($getAiAvailabilityQuery);
    $stmt->execute();

    $row = $stmt->fetch();
    return (bool)$row["trading_feature"] ?? true;
}

function getUserIP() {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
}

function generateSessionToken($userID, $sessionDevice = null, $sessionBrowser = null) {
    if(!$userID) return null;
    $token = bin2hex(random_bytes(32));

    // انقضای توکن های لاگین، 20 روزه می باشد
    $expiryTimestamp = time() + (60 * 60 * 24 * 20);
    global $pdo;

    $insertSessionQuery = "INSERT INTO sessions (user_id, session_token, session_expiry, session_device, session_browser) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($insertSessionQuery);
    $stmt->execute([$userID, $token, $expiryTimestamp, $sessionDevice, $sessionBrowser]);

    if($stmt->rowCount() > 0){
        return $token;
    }
    return null;
}

function getLatestPrices() {
    global $pdo;
    $getPricesQuery = "SELECT * FROM prices ORDER BY retrieval_timestamp DESC LIMIT 1";
    $stmt = $pdo->prepare($getPricesQuery);
    $stmt->execute();
    return $stmt->fetch() ?? null;
}

# تابع جهت فرمت کردن خروجی نهایی و ایجاد یک ساختار مجتمع از دارایی ها و آخرین قیمت ارز ها
function formatPortfolio($portfolio) {
    $latestPrices = getLatestPrices();

    $priceFieldMap = [
        'toman_amount' => 'toman',
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
        'ada_amount' => 'ada',
    ];

    $formattedPortfolio = [];
    $totalValue = 0;

    foreach ($priceFieldMap as $portfolioAmountField => $priceField) {
        if (!isset($portfolio[$portfolioAmountField])) {
            continue;
        }

        $amount = is_numeric($portfolio[$portfolioAmountField])
            ? floatval($portfolio[$portfolioAmountField])
            : 0;

        $unitPrice = (isset($latestPrices[$priceField]) && is_numeric($latestPrices[$priceField]))
            ? floatval($latestPrices[$priceField])
            : null;

        if($priceField === "toman"){
            $unitPrice = 1;
        }

        $assetName = str_replace('_amount', '', $portfolioAmountField);

        if ($assetName === "toman") {
            $valueToman = $amount; # 1 تومن = 1 تومن
        } else {
            $valueToman = ($unitPrice !== null) ? $unitPrice * $amount : 0;
        }

        $formattedPortfolio[] = [
            'name' => $assetName,
            'amount' => $amount,
            'unitPrice' => $unitPrice,
            'valueToman' => $valueToman, # یک کلید موقت
        ];

        $totalValue += $valueToman;
    }

    # محاسبه درصد دارایی نسبت به کل دارایی ها
    foreach ($formattedPortfolio as $i => $asset) {
        $percentage = ($totalValue > 0) ? ($asset['valueToman'] / $totalValue) * 100 : 0;

        $formattedPortfolio[$i]['percentage'] = round($percentage, 2);

        # حذف کلید موقت آرایه
        unset($formattedPortfolio[$i]['valueToman']);
    }

    return $formattedPortfolio;
}

# تابع جهت دریافت وضعیت دارایی ها. خروجی این تابع به تابع formatPortfolio فرستاده میشود.
function getPortfolio($userID) {
    global $pdo;
    $getPortfolioQuery = "SELECT * FROM portfolios WHERE user_id = ?";
    $stmt = $pdo->prepare($getPortfolioQuery);
    $stmt->execute([$userID]);
    $portfolio = $stmt->fetch() ?? null;

    if ($portfolio) {
        $portfolio = formatPortfolio($portfolio);
    }
    return $portfolio;
}

# دریافت میزان سرمایه انتخابی کاربر در زمان ثبت نام
function getStartingBudget($userID){
    global $pdo;
    $getStartingBudgetQuery = "SELECT starting_budget_toman FROM users WHERE user_id = ?;";
    $stmt = $pdo->prepare($getStartingBudgetQuery);
    $stmt->execute([$userID]);
    $row = $stmt->fetch();

    return $row["starting_budget_toman"] ?? null;
}

function chatCloser() {
    # بستن چت های روز های قبل
    global $pdo;
    $closeYesterdayChatsQuery = "UPDATE chats SET chat_status = 'closed' WHERE chat_date < ?";
    $stmt = $pdo->prepare($closeYesterdayChatsQuery);
    $stmt->execute([date("Y-m-d")]);
}