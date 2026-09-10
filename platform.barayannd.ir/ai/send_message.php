<?php

session_start();
header("Content-Type: application/json");
require_once("../functions.php");

global $pdo;

# محدودیت روزانه پردازش پیام های کاربر
$DAILY_CHAT_PROCESSES_LIMIT = getMaxDailyAiMessages();
# حداقل طول مجاز پیام دریافتی از سوی کاربر
const MIN_MESSAGE_CHARS = 15;

const OPENAI_MODEL = "gpt-5-nano";
const OPENAI_ENDPOINT = "https://api.openai.com/v1/chat/completions";
const OPENAI_TIMEOUT_SECONDS = 30;

const AI_SYSTEM_PROMPT = <<<PROMPT
Your name is "دستیار مخصوص سیستم برآیند" (Barayannd's special assistant).
You are a trading and financial markets advisor assistant embedded in a chat product.
Speak Persian unless the user writes in another language, then match their language.
Give clear, factual analysis of markets, instruments, trading concepts, risk management,
and strategy — enough for the user to make their own informed decision.
Do not present your output as personalized financial advice, and do not guarantee outcomes;
briefly note that you are not a licensed financial advisor when giving any concrete
recommendation-like statement.
Keep every response as short as possible. Never over-elaborate, never repeat yourself,
never explore multiple angles when one will do.
PROMPT;

$input = json_decode(file_get_contents('php://input'), true);

$message = $input['message'] ?? null;

$userID = $_SESSION['user_id'];
$now = date("H:i:s");
$today = date("Y-m-d");


# بستن چت های روز های قبل
chatCloser();


function setChatStatus($status, $chatID) {
    global $pdo;
    $updateChatStatusQuery = "UPDATE chats SET chat_status = ? WHERE chat_id = ?";
    $stmt = $pdo->prepare($updateChatStatusQuery);
    $stmt->execute([$status, $chatID]);
}

function checkLimit($userID, $today) {
    global $pdo, $DAILY_CHAT_PROCESSES_LIMIT;
    $countMessagesQuery = "SELECT COUNT(m.message_id) as c FROM messages m JOIN chats c ON m.chat_id = c.chat_id WHERE c.user_id = ? AND c.chat_date = ? AND (m.sender = 'ai' OR m.sender = 'system')";
    $stmt = $pdo->prepare($countMessagesQuery);
    $stmt->execute([$userID, $today]);
    $row = $stmt->fetch();
    if($row['c'] >= $DAILY_CHAT_PROCESSES_LIMIT) {
        return true;
    }
    return false;
}

function checkChat ($userID){
    # بررسی وضعیت آخرین چت کاربر
    global $pdo, $today;
    $selectChatQuery = "SELECT * FROM chats WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
    $stmt = $pdo->prepare($selectChatQuery);
    $stmt->execute([$userID]);
    $row = $stmt->fetch();

    if($row){
        $chatID = $row["chat_id"];
        $chatDate = $row["chat_date"];
        $assistantMessage = $row["assistant_message"];
        $chatStatus = $row["chat_status"];

        switch($chatStatus){
            case "pending_response": {
                return returnOutput("Processing");
            }
            case "limit_reached": {
                return returnOutput("Limit");
            }
            case "closed": {
                return returnOutput("Closed");
            }
            case "open": {
                return returnOutput(null, [
                    "chat_id" => $chatID,
                    "chat_date" => $chatDate,
                    "assistant_message" => $assistantMessage
                ]);
            }
        }
    }
    else{
        return returnOutput("No chats found");
    }
    return returnOutput("No chats found");
}

function callOpenAI($userMessage, $previousAssistantMessage) {
    /*

    Removed Api Key

    */
    $apiKey = "RemovedApiKey";
    /*

    Removed Api Key

    */

    $messages = [
        ["role" => "system", "content" => AI_SYSTEM_PROMPT],
    ];

    # اضافه کردن آخرین پیام دستیار به عنوان context مکالمه، در صورت وجود
    if(!empty($previousAssistantMessage)){
        $messages[] = ["role" => "assistant", "content" => $previousAssistantMessage];
    }

    $messages[] = ["role" => "user", "content" => $userMessage];

    $payload = json_encode([
        "model" => OPENAI_MODEL,
        "messages" => $messages,
    ]);

    $ch = curl_init(OPENAI_ENDPOINT);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_TIMEOUT => OPENAI_TIMEOUT_SECONDS,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Authorization: Bearer " . $apiKey,
        ],
    ]);

    $rawResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if($curlError){
        error_log("OpenAI cURL error: " . $curlError);
        return null;
    }

    if($httpCode !== 200){
        error_log("OpenAI API returned HTTP $httpCode: " . $rawResponse);
        return null;
    }

    $decoded = json_decode($rawResponse, true);
    $content = $decoded["choices"][0]["message"]["content"] ?? null;

    if(!$content || strlen(trim($content)) === 0){
        error_log("OpenAI API returned empty content: " . $rawResponse);
        return null;
    }

    return trim($content);
}

# تابع برای ارسال پیام کاربر به هوش مصنوعی و بازگردانی نتیجه
function sendMessage($message, $chatID, $assistantMessage, $userID) {
    global $pdo, $today, $now;

    $chatLimited = checkLimit($userID, $today);

    # در صورت رسیدن به محدودیت روزانه
    if($chatLimited){
        setChatStatus('limit_reached', $chatID);
        return returnOutput("Limited");
    }

    $insertUserMessageQuery = "INSERT INTO messages (content, chat_id, sender, time) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($insertUserMessageQuery);
    $stmt->execute([$message, $chatID, "user", $now]);

    $insertedUserMessageID = $pdo->lastInsertId();

    setChatStatus('pending_response', $chatID);

    $aiResponse = callOpenAI($message, $assistantMessage);

    # در صورت دریافت صحیح پاسخ از api
    if($aiResponse && strlen($aiResponse) > 0){
        # اضافه کردن پاسخ مدل هوش مصنوعی به دیتابیس
        $insertAiMessageQuery = "INSERT INTO messages (content, chat_id, sender, time) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($insertAiMessageQuery);
        $stmt->execute([$aiResponse, $chatID, "ai", $now]);

        $returnedMessageID = $pdo->lastInsertId() ?? null;

        # تنظیم بعنوان آخرین پیام دستیار چت جاری
        $updateAssistantMessageQuery = "UPDATE chats SET assistant_message = ? WHERE chat_id = ?";
        $stmt = $pdo->prepare($updateAssistantMessageQuery);
        $stmt->execute([$aiResponse, $chatID]);

        setChatStatus('open', $chatID);

        return returnOutput(null, ["response" => $aiResponse, "user_message_id" => $insertedUserMessageID, "returned_message_id" => $returnedMessageID]);
    }
    else{
        # درج پیام خطای api در دیتابیس و همچنین نمایش پیغام مناسب به کاربر
        $content = "مشکلی در پاسخ به پیام شما رخ داد! این مشکل ممکن است موقت باشد اما اگر برای شما پایدار است، لطفا با پشتیبانی ارتباط برقرار کنید";

        $systemMessage = "SYSTEM ERROR";

        $insertSystemMessageQuery = "INSERT INTO messages (content, system_message, chat_id, sender, time) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($insertSystemMessageQuery);
        $stmt->execute([$content, $systemMessage, $chatID, "system", $now]);

        $returnedMessageID= $pdo->lastInsertId() ?? null;

        setChatStatus('open', $chatID);

        return returnOutput(null, ["response" => $content, "user_message_id" => $insertedUserMessageID, "returned_message_id" => $returnedMessageID]);
    }
}

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

if(!isset($input["message"]) || strlen($input["message"]) < MIN_MESSAGE_CHARS || !is_string($input["message"])){
    http_response_code(400);
    echo jn(returnOutput("invalid message"));
    exit;
}

if(strlen($input["message"]) > 2000){
    http_response_code(400);
    echo jn(returnOutput("message too long"));
    exit;
}

$message = trim($input["message"]);

$aiAvailable = aiAvailability();

if(!$aiAvailable){
    http_response_code(400);
    echo jn(returnOutput("AI unavailable"));
    exit;
}

# دریافت وضعیت اخرین چت از دیتابیس
$chatStatus = checkChat($_SESSION["user_id"]);

if(!$chatStatus["success"]){
    global $pdo;
    switch ($chatStatus["error"]) {
        case "Processing": case "Limit": {
        echo jn(returnOutput($chatStatus["error"]));
        break;
    }
        case "Closed": {
            # اگر آخرین چت کاربر، مربوط به روز های گذشته است
            $insertNewChatQuery = "INSERT INTO chats (user_id, chat_date, chat_status) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($insertNewChatQuery);
            $stmt->execute([$_SESSION["user_id"], $today, "open"]);
            $newChatID = $pdo->lastInsertId();

            $requestResult = sendMessage($message, $newChatID, null, $_SESSION["user_id"]);

            if($requestResult["success"]){
                $response = $requestResult["data"]["response"];
                $userMessageID = $requestResult["data"]["user_message_id"];
                $returnedMessageID = $requestResult["data"]["returned_message_id"];
                echo jn(returnOutput(null, [
                    "your_message" => ["id" => $userMessageID, "content" => $message],
                    "response" => ["id" => $returnedMessageID, "content" => $response]
                ]));
            }
            else{
                echo jn(returnOutput($requestResult["error"]));
            }
            break;
            # *** صفحه چت باید سمت فرانت ریلود شود ***
        }
        case "No chats found": {
            # ایجاد چت جدید اگر اولین چت کاربر است
            $insertNewChatQuery = "INSERT INTO chats (user_id, chat_date) VALUES (?, ?)";
            $stmt = $pdo->prepare($insertNewChatQuery);
            $stmt->execute([$_SESSION["user_id"], $today]);

            $chatID = $pdo->lastInsertId();

            $requestResult = sendMessage($message, $chatID, null, $_SESSION["user_id"]);

            if($requestResult["success"]){
                $response = $requestResult["data"]["response"];
                $userMessageID = $requestResult["data"]["user_message_id"];
                $returnedMessageID = $requestResult["data"]["returned_message_id"];
                echo jn(returnOutput(null, [
                    "your_message" => ["id" => $userMessageID, "content" => $message],
                    "response" => ["id" => $returnedMessageID, "content" => $response]
                ]));
            }
            else{
                echo jn(returnOutput($requestResult["error"]));
            }
            break;
        }
        default: {
            echo jn(returnOutput("Unknown error"));
            break;
        }
    }
}
else{
    # در صورت اوکی بودن وضعیت چت
    $assistantMessage = $chatStatus["data"]["assistant_message"];
    $chatID = $chatStatus["data"]["chat_id"];

    $requestResult = sendMessage($message, $chatID, $assistantMessage, $_SESSION["user_id"]);

    if($requestResult["success"]){
        $response = $requestResult["data"]["response"];
        $userMessageID = $requestResult["data"]["user_message_id"];
        $returnedMessageID = $requestResult["data"]["returned_message_id"];
        echo jn(returnOutput(null, [
            "your_message" => ["id" => $userMessageID, "content" => $message],
            "response" => ["id" => $returnedMessageID, "content" => $response]
        ]));
    }
    else{
        echo jn(returnOutput($requestResult["error"]));
    }
}
exit;
