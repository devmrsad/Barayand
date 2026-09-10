<?php
// این فایل یک توکن از سمت کاربر دریافت کرده و وضعیت را به فرانت بر میگرداند

session_start();
header('Content-Type: application/json');
require_once "../base.php";
global $pdo;

$input = json_decode(file_get_contents("php://input"), true);
$client_token = $input["session_token"] ?? null;

$server_session_token = $_SESSION["session_token"] ?? null;
$server_user_id = $_SESSION["user_id"] ?? null;

if ($client_token) {
    $searchSessionQuery = "SELECT session_id, user_id, session_expiry, session_active FROM sessions WHERE session_token = ? LIMIT 1";
    $stmt = $pdo->prepare($searchSessionQuery);
    $stmt->execute([$client_token]);
    $row = $stmt->fetch();

    if ($row) {
        $sessionID = $row["session_id"];
        $sessionExpiry = $row["session_expiry"];
        $session_active = $row["session_active"];
        $db_user_id = $row["user_id"];

        if ($sessionExpiry < time() || $session_active == 0) {
            # اگر سشن نامعتبر است
            if ($session_active == 1) {
                $disableSessionQuery = "UPDATE sessions SET session_active = 0 WHERE session_token = ?";
                $stmt = $pdo->prepare($disableSessionQuery);
                $stmt->execute([$client_token]);
            }

            session_unset();
            session_destroy();
            echo json_encode(["success" => false, "status" => "Session expired or inactive", "action" => "clear"]);
        } else {
            # اگر سشن کاملا معتبر است
            # آخرین فعالیت سشن آپدیت شود
            $updateSessionLastActiveQuery = "UPDATE sessions SET session_last_active = ? WHERE session_id = ?";
            $stmt = $pdo->prepare($updateSessionLastActiveQuery);
            $stmt->execute([date("Y-m-d H:i:s", time()), $sessionID]);

            if (!$server_session_token || $server_session_token != $client_token) {
                # اگر توکن سمت سشن سرور و سمت کاربر مچ نیستند
                $_SESSION["user_id"] = $db_user_id;
                $_SESSION["session_token"] = $client_token;
                echo json_encode(["success" => true, "status" => "active", "action" => "reload"]);
            } else {
                echo json_encode(["success" => true, "status" => "active", "action" => null]);
            }
        }
    } else {
        # توکن در دیتابیس وجود نداره یا اینکه غیرفعاله
        if ($server_session_token) {
            session_unset();
            session_destroy();
        }
        echo json_encode(["success" => false, "status" => "Wrong session token", "action" => "clear"]);
    }
    exit;
}
else {
    # اگه توکن سمت سرور معتبره
    if ($server_session_token && $server_user_id) {
        $fetchSessionQuery = "SELECT session_id, session_expiry, session_active FROM sessions WHERE session_token = ?";
        $stmt = $pdo->prepare($fetchSessionQuery);
        $stmt->execute([$server_session_token]);
        $row = $stmt->fetch();

        if ($row) {
            $session_id = $row["session_id"];
            $session_expiry = $row["session_expiry"];
            $session_active = $row["session_active"];

            # اگر سشن سمت سرور منقضی شده یا غیرفعاله
            if ($session_expiry < time() || $session_active == 0) {
                if ($session_active == 1) {
                    $disableSessionQuery = "UPDATE sessions SET session_active = 0 WHERE session_id = ?";
                    $stmt = $pdo->prepare($disableSessionQuery);
                    $stmt->execute([$session_id]);
                }

                session_unset();
                session_destroy();
                echo json_encode(["success" => false, "status" => "No Authentication", "action" => "reload"]);
            } else {
                # اگر سشن سمت سرور معتبر و فعاله
                $updateSessionLastActiveQuery = "UPDATE sessions SET session_last_active = ? WHERE session_id = ?";
                $stmt = $pdo->prepare($updateSessionLastActiveQuery);
                $stmt->execute([date("Y-m-d H:i:s", time()), $session_id]);

                $jsFormatExpiry = $session_expiry * 1000;
                echo json_encode(["success" => true, "status" => "active", "action" => "set & reload", "data" => ["expiry" => $jsFormatExpiry, "token" => $server_session_token]]);
            }
            exit;
        } else {
            # توکن سمت سرور در دیتابیس وجود نداره (پاک شده)
            session_unset();
            session_destroy();
            echo json_encode(["success" => false, "status" => "Authentication data inconsistency", "action" => "reload"]);
        }
    } else {
        echo json_encode(["success" => false, "status" => "No Authentication", "action" => null]);
        exit;
    }
}
