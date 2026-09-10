<?php
# از این endpoint برای دریافت تغییرات روزانه مجموع کل دارایی های فرد استفاده میشود
# این کار از طریق مقایسه دارایی فعلی با آخرین اسکرین شات (روزانه یا اولیه) انجام میشود.

session_start();
header("Content-Type: application/json");
require_once "../functions.php";
require_once "../jdf.php";
global $pdo;

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo jn(returnOutput("User not found - Invalid session"));
    exit;
}

$userID = $_SESSION["user_id"];

# دریافت وضعیت دارایی ها در 7 روز آخر
$query = "SELECT created_at, total_balance_toman FROM screenshots WHERE user_id = ? AND type = 'daily' ORDER BY created_at ASC LIMIT 7";
$stmt = $pdo->prepare($query);
$stmt->execute([$userID]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

# اگر هنوز 7 روز از ثبت اسکرین شات ها نگذشته
if (count($rows) < 7) {
    http_response_code(400);
    echo jn(returnOutput("Not enough daily screenshots. Need 7 days of data."));
    exit;
}

$dailyData = [];

foreach ($rows as $index => $row) {
    $date = date("Y-m-d", strtotime($row['created_at']));
    $dateParts = explode("-", $date);
    # تاریخ شمسی
    $jdate = gregorian_to_jalali($dateParts[0], $dateParts[1], $dateParts[2], "/");
    $balance = (float)$row['total_balance_toman'];

    # روز اول
    if ($index === 0) {
        $dailyData[] = [
            "date"           => $jdate,
            "balance"        => $balance,
            "changeAmount"   => 0,
            "changePercent"  => 0,
            "changeType"     => "equal"
        ];
        continue;
    }

    $prevBalance = (float)$rows[$index - 1]['total_balance_toman'];
    $diff = $balance - $prevBalance;
    $changeAmount = abs($diff);
    $changeType = $diff > 0 ? "increase" : ($diff < 0 ? "decrease" : "equal");

    if ($prevBalance == 0) {
        $percentageChange = ($diff != 0) ? 100.0 : 0.0;
    } else {
        $percentageChange = abs($diff / $prevBalance) * 100;
    }

    $dailyData[] = [
        "date"           => $jdate,
        "balance"        => $balance,
        "changeAmount"   => round($changeAmount, 3),
        "changePercent"  => round($percentageChange, 1),
        "changeType"     => $changeType
    ];
}

echo jn(returnOutput(null, $dailyData));
exit;