<?php

session_start();
require_once("../addresses.php");
global $loginPage;

if (!isset($_SESSION["user_id"])) {
    header("Location: $loginPage");
    exit("Not logged in");
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="../assets/favicon/favicon-sign-blue-bg-png-transparent.png">
    <link rel="stylesheet" href="../common.css">
    <link rel="stylesheet" href="components/chatbot.css">
    <title>برآیند | دستیار هوش مصنوعی</title>
</head>

<body>
    <div class="container">
        <div class="barayannd-ai-profile-sec">
            <img src="assets/right-arrow.svg" alt="" id="back_btn">
            <div class="barayannd-ai-profile">
                <img src="assets/barayannd-ai.svg" alt="" id="barayannd-ai-icon">
            </div>
            <div class="barayand-title">
                <h2>دستیار اختصاصی برآیند</h2>
                <p>آنلاین</p>
            </div>
        </div>
        <div class="modal-message-box">
            <h4 id="modal_message_content"></h4>
            <svg xmlns="http://www.w3.org/2000/svg" width="15.998" height="15.999" viewBox="0 0 15.998 15.999">
                <path id="patch-exclamation-fill"
                    d="M10.067.87a2.89,2.89,0,0,0-4.134,0l-.622.638L4.421,1.5A2.89,2.89,0,0,0,1.5,4.421l.01.89-.636.622a2.89,2.89,0,0,0,0,4.134l.637.622-.011.89A2.89,2.89,0,0,0,4.421,14.5l.89-.01.622.636a2.89,2.89,0,0,0,4.134,0l.622-.637.89.011A2.89,2.89,0,0,0,14.5,11.579l-.01-.89.636-.622a2.89,2.89,0,0,0,0-4.134l-.637-.622.011-.89A2.89,2.89,0,0,0,11.579,1.5l-.89.01L10.067.871ZM8,4a.9.9,0,0,1,.9.995L8.55,8.5a.552.552,0,0,1-1.1,0L7.1,4.995A.9.9,0,0,1,8,4Zm0,6a1,1,0,1,1-1,1A1,1,0,0,1,8,10Z"
                    transform="translate(-0.001 0)" fill="#b90000" class="modal_messagebox_icon" />
            </svg>
        </div>
        <div class="mobile-baryannd-sec">
            <div class="mobile-barayannd-profile">
                <img src="assets/barayannd-ai.svg" alt="">
                <div class="mobile-barayannd-title">
                    <h4>دستیار اختصاصی برآیند</h4>
                    <p>آنلاین</p>
                </div>
            </div>
            <img src="assets/right-arrow.svg" alt="" id="back_btn_mobile">
        </div>
        <div class="chat-area">
            <div class="message-card">
                <div id="chat-start-badge" class="disabled">
                    <h3>روز بخیر!</h3>
                    <h5>
                        امروز چگونه میتوانیم به شما کمک کنیم؟
                    </h5>
                </div>
            </div>
        </div>
        <div class="message-textbox-holder">
            <div class="message-textbox">
                <textarea type="text" id="message" name="message" placeholder="پیام خود را اینجا بنویسید..."></textarea>
                <button class="send-message"><img src="assets/send-icon.svg" alt=""></button>
            </div>
        </div>
    </div>
    <script src="../check_session_status.js"></script>
    <script type="module" src="../common.js."></script>
    <script type="module" src="components/chat.js"></script>

</body>

</html>