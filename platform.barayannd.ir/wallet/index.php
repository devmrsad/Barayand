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
    <title>برآیند | کیف پول </title>
    <link rel="icon" href="../assets/favicon/favicon-sign-blue-bg-png-transparent.png">
    <link rel="stylesheet" href="../common.css">
    <link rel="stylesheet" href="../components/menu/menu.css">
    <link rel="stylesheet" href="../components/header/header.css">
    <link rel="stylesheet" href="../components/homePage.css">
    <link rel="stylesheet" href="components/wallet.css">
</head>

<body>
    <div class="container">
        <div class="side-menu">
            <div class="live-time-holder">
                <h1 id="time_label">13<span>:</span>46<span>:</span><span class="timer-sec-digit">12</span></h1>
                <div class="live-date-holder">
                    <p id="gregorain_date">2025 August 19, 15:00 GMT</p>
                    <h4 id="persian_date">پنجشنبه، 5 فروردین ماه</h4>
                </div>
            </div>
            <div class="menu-options-container">
                <div class="menu-option-users " data-tab="users_tab">
                    <div class="menu-option-users-ui">
                        <svg xmlns="http://www.w3.org/2000/svg" width="78" height="66" viewBox="0 0 78 66">
                            <g id="home_icon" transform="translate(-1627.106 -391)">
                                <rect id="Rectangle_4" data-name="Rectangle 4" width="78" height="66" rx="27"
                                    transform="translate(1627.106 391)" fill="#d1d1d1" stroke="#d1d1d1" />
                                <path id="house"
                                    d="M19.752,1.894a2.346,2.346,0,0,0-3.317,0L.843,17.483A1.174,1.174,0,1,0,2.5,19.144l1.515-1.518V30.042a3.519,3.519,0,0,0,3.519,3.519H28.649a3.519,3.519,0,0,0,3.519-3.519V17.626l1.515,1.518a1.174,1.174,0,0,0,1.661-1.661l-5.522-5.519V4.24a1.173,1.173,0,0,0-1.173-1.173H26.3A1.173,1.173,0,0,0,25.13,4.24V7.273Zm10.07,13.387V30.042a1.173,1.173,0,0,1-1.173,1.173H7.538a1.173,1.173,0,0,1-1.173-1.173V15.281L18.093,3.552Z"
                                    transform="translate(1648.027 406.318)" fill="#030303" stroke="#000"
                                    stroke-linecap="round" stroke-width="1" />
                            </g>
                        </svg>
                        <a href="/platform.barayannd.ir/">خانه</a>
                    </div>
                    <div class="user_tabs">
                        <a href="/platform.barayannd.ir/">
                            <p class="tab">اطلاعات من </p>
                        </a>
                        <hr>
                        <a href="/platform.barayannd.ir/">
                            <p class="tab"> عملکرد من </p>
                        </a>
                        <hr>
                        <a href="/platform.barayannd.ir/components/SaveMessage">
                            <p class="tab">پیام های ذخیره شده</p>
                        </a>
                    </div>
                </div>
                <div class="menu-option" data-tab="trade_tab" id="trade_tab">
                    <svg xmlns="http://www.w3.org/2000/svg" width="78" height="66" viewBox="0 0 78 66">
                        <g id="trade_icon" transform="translate(-1627 -808)">
                            <rect id="Rectangle_8" data-name="Rectangle 8" width="78" height="66" rx="27"
                                transform="translate(1627 808)" fill="#d1d1d1" stroke="#fff" />
                            <path id="arrow-left-right"
                                d="M1,21.2a.922.922,0,0,0,.922.922h21.74l-5.8,5.8a.923.923,0,0,0,1.305,1.305l7.374-7.374a.922.922,0,0,0,0-1.305l-7.374-7.374a.923.923,0,1,0-1.305,1.305l5.8,5.8H1.922A.922.922,0,0,0,1,21.2ZM26.81,8.3a.922.922,0,0,1-.922.922H4.148l5.8,5.8a.923.923,0,0,1-1.305,1.305L1.27,8.949a.922.922,0,0,1,0-1.305L8.644.27A.923.923,0,1,1,9.949,1.575l-5.8,5.8h21.74A.922.922,0,0,1,26.81,8.3Z"
                                transform="translate(1651.341 828.122)" stroke="#000" stroke-linecap="round"
                                stroke-width="1" fill-rule="evenodd" />
                        </g>
                    </svg>
                    <a href="/platform.barayannd.ir/trades/">معاملات</a>
                </div>
                <div class="menu-option">
                    <svg xmlns="http://www.w3.org/2000/svg" width="78" height="66" viewBox="0 0 78 66">
                        <g id="market_icon" transform="translate(-1627 -495)">
                            <rect id="Rectangle_5" data-name="Rectangle 5" width="78" height="66" rx="27"
                                transform="translate(1627 495)" fill="#d1d1d1" stroke="#fff" />
                            <path id="Path_1" data-name="Path 1"
                                d="M8.091,2a2.011,2.011,0,0,1,2.023-2h2.023a2.011,2.011,0,0,1,2.023,2V8a2.011,2.011,0,0,1-2.023,2H10.114A2.011,2.011,0,0,1,8.091,8ZM0,2A2.011,2.011,0,0,1,2.023,0H4.045A2.011,2.011,0,0,1,6.068,2V16a2.011,2.011,0,0,1-2.023,2H2.023A2.011,2.011,0,0,1,0,16ZM16.182,2A2.011,2.011,0,0,1,18.2,0h2.023A2.011,2.011,0,0,1,22.25,2V22a2.011,2.011,0,0,1-2.023,2H18.2a2.011,2.011,0,0,1-2.023-2Z"
                                transform="translate(1677.963 539.879) rotate(180)" fill="#060606" />
                        </g>
                    </svg>
                    <a href="/platform.barayannd.ir/market/">بازار</a>
                </div>
                <div class="menu-option active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="78" height="66" viewBox="0 0 78 66">
                        <g id="wallet" transform="translate(-1627 -704)">
                            <rect id="Rectangle_7" data-name="Rectangle 7" width="78" height="66" rx="27"
                                transform="translate(1627 704)" fill="#d1d1d1" stroke="#fff" />
                            <path id="wallet2"
                                d="M21.238.36A2.625,2.625,0,0,1,24.5,2.9V5.039h.875A2.625,2.625,0,0,1,28,7.664v15.75a2.625,2.625,0,0,1-2.625,2.625H2.625A2.625,2.625,0,0,1,0,23.414V7.664A2.625,2.625,0,0,1,2.506,5.041ZM9.733,5.039H22.75V2.9a.875.875,0,0,0-1.087-.847ZM2.625,6.789a.875.875,0,0,0-.875.875v15.75a.875.875,0,0,0,.875.875h22.75a.875.875,0,0,0,.875-.875V7.664a.875.875,0,0,0-.875-.875Z"
                                transform="translate(1652.816 722.84)" stroke="#000" stroke-linecap="round"
                                stroke-width="1" />
                        </g>
                    </svg>
                    <a href="/platform.barayannd.ir/wallet/">کیف پول</a>
                </div>
            </div>
            <div class="ai-cta">
                <div class="ai-icon">
                    <svg id="robot-ui-icon" xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                        viewBox="0 0 43.478 45">
                        <path id="Path_1485" data-name="Path 1485"
                            d="M11.152,23.876a1.359,1.359,0,0,1,1.359-1.359h8.152a1.359,1.359,0,1,1,0,2.717H12.511A1.359,1.359,0,0,1,11.152,23.876ZM3,11.816A6.224,6.224,0,0,1,9.875,5.9,72.228,72.228,0,0,0,23.3,5.9a6.224,6.224,0,0,1,6.875,5.913V14.96A2.535,2.535,0,0,1,28.1,17.5a68.263,68.263,0,0,1-11.508.94A68.556,68.556,0,0,1,5.079,17.5,2.535,2.535,0,0,1,3,14.96ZM15.342,9.569a.679.679,0,0,0-.59.185l-2.5,2.446q-2.551-.152-5.084-.5a.679.679,0,0,0-.185,1.345c1.495.207,3.348.4,5.489.524a.679.679,0,0,0,.514-.193l2.049-2,2.3,4.647a.679.679,0,0,0,1.1.168l2.533-2.636q2.62-.156,5.223-.511a.679.679,0,0,0-.185-1.345c-1.462.2-3.28.394-5.38.514a.679.679,0,0,0-.451.207l-2.049,2.133-2.288-4.62a.679.679,0,0,0-.495-.367Z"
                            transform="translate(5.152 14.331)" fill="#020202" />
                        <path id="Path_1486" data-name="Path 1486"
                            d="M23.1,5.6a3.137,3.137,0,0,0,1.266-3.375,2.661,2.661,0,0,0-5.25,0A3.137,3.137,0,0,0,20.38,5.6V9H14.945C8.192,9,2.717,15.044,2.717,22.5V24A2.869,2.869,0,0,0,0,27v6a2.869,2.869,0,0,0,2.717,3v3a5.738,5.738,0,0,0,5.435,6H35.326a5.738,5.738,0,0,0,5.435-6V36a2.869,2.869,0,0,0,2.717-3V27a2.869,2.869,0,0,0-2.717-3V22.5C40.76,15.044,35.286,9,28.532,9H23.1ZM38.043,22.5V39a2.869,2.869,0,0,1-2.717,3H8.152a2.869,2.869,0,0,1-2.717-3V22.5c0-5.8,4.258-10.5,9.511-10.5H28.532C33.785,12,38.043,16.7,38.043,22.5Z"
                            transform="translate(0 0)" fill="#020202" />
                        <path id="Path_1501" data-name="Path 1501"
                            d="M23.1,5.6a3.137,3.137,0,0,0,1.266-3.375,2.661,2.661,0,0,0-5.25,0A3.137,3.137,0,0,0,20.38,5.6V9H14.945C8.192,9,2.717,15.044,2.717,22.5V24A2.869,2.869,0,0,0,0,27v6a2.869,2.869,0,0,0,2.717,3v3a5.738,5.738,0,0,0,5.435,6H35.326a5.738,5.738,0,0,0,5.435-6V36a2.869,2.869,0,0,0,2.717-3V27a2.869,2.869,0,0,0-2.717-3V22.5C40.76,15.044,35.286,9,28.532,9H23.1ZM38.043,22.5V39a2.869,2.869,0,0,1-2.717,3H8.152a2.869,2.869,0,0,1-2.717-3V22.5c0-5.8,4.258-10.5,9.511-10.5H28.532C33.785,12,38.043,16.7,38.043,22.5Z"
                            transform="translate(0 0)" fill="#020202" />
                    </svg>
                </div>
                <a href="/platform.barayannd.ir/chatbot">هوش مصنوعی برآیند</a>
            </div>
        </div>
        <div class="header-and-content-container">
            <?php include("../components/header/header.php") ?>
            <div class="content-container">

                <div class="wallet-sec-container">
                    <div class="modal-message-box">
                        <h4 id="modal_message_content"></h4>
                        <svg xmlns="http://www.w3.org/2000/svg" width="15.998" height="15.999"
                            viewBox="0 0 15.998 15.999">
                            <path id="patch-exclamation-fill"
                                d="M10.067.87a2.89,2.89,0,0,0-4.134,0l-.622.638L4.421,1.5A2.89,2.89,0,0,0,1.5,4.421l.01.89-.636.622a2.89,2.89,0,0,0,0,4.134l.637.622-.011.89A2.89,2.89,0,0,0,4.421,14.5l.89-.01.622.636a2.89,2.89,0,0,0,4.134,0l.622-.637.89.011A2.89,2.89,0,0,0,14.5,11.579l-.01-.89.636-.622a2.89,2.89,0,0,0,0-4.134l-.637-.622.011-.89A2.89,2.89,0,0,0,11.579,1.5l-.89.01L10.067.871ZM8,4a.9.9,0,0,1,.9.995L8.55,8.5a.552.552,0,0,1-1.1,0L7.1,4.995A.9.9,0,0,1,8,4Zm0,6a1,1,0,1,1-1,1A1,1,0,0,1,8,10Z"
                                transform="translate(-0.001 0)" fill="#b90000" class="modal_messagebox_icon" />
                        </svg>
                    </div>
                    <div class="portfolio-cards">
                        <div class="asset-card">
                            <div class="assets-value-holder-sec">
                                <div class="assets-value-holder">
                                    <h4>ارزش دارایی ها : </h4>
                                    <div class="assets-value-reveiw">
                                        <h2 id="total_asset_value">0 تومان</h2>
                                        <h4 id="total_change_value">0 تومان</h4>
                                    </div>
                                </div>
                                <hr>
                            </div>
                            <div class="wallet-actions">
                                <a href="/platform.barayannd.ir/trades"><button type="button"
                                        id="buy_btn_wallet">خرید</button></a>
                                <a href="/platform.barayannd.ir/trades"><button type="button"
                                        id="sell_btn_wallet">فروش</button></a>
                            </div>
                        </div>
                        <div class="asset-verity">
                            <div class="asset-verity-container">
                                <canvas class="asset-verity-chart" id="asset-verity-chart">
                                </canvas>
                            </div>
                        </div>

                    </div>
                    <div class="assets-cards-container">
                        <img src="assets/chevron-compact-left.svg" alt="" id="left_chevron">
                    </div>
                    <img src="assets/chevron-compact-down.svg" alt="" id="down_chevron">
                </div>
            </div>
            <div class="bottom-mobile-nav">
                <a href="/platform.barayannd.ir/" class="mobile-nav-option">
                    <img src="/platform.barayannd.ir/assets/svg/house.svg" alt="">
                    <p>خانه</p>
                </a>
                <a class="mobile-nav-option" href="/platform.barayannd.ir/market/">
                    <img src="/platform.barayannd.ir/assets/svg/kanban.svg" alt="">
                    <p>بازار</p>
                </a>
                <a class="mobile-nav-option" href="/platform.barayannd.ir/trades/">
                    <img src="/platform.barayannd.ir/assets/svg/arrow-left-right1.svg" alt="">
                    <p>معاملات</p>
                </a>
                <a class="mobile-nav-option active" href="/platform.barayannd.ir/wallet/">
                    <img src="/platform.barayannd.ir/assets/svg/wallet3.svg" alt="">
                    <p>کیف پول</p>
                </a>
            </div>
        </div>
        <script src="../check_session_status.js"></script>
        <script type="module" src="../common.js."></script>
        <script type="module" src="../components/menu/menu.js"></script>
        <script type="module" src="../components/header/header.js"></script>
        <script type="module" src="../node_modules/chart.js/dist/chart.umd.min.js"></script>
        <script type="module" src="components/wallet.js"></script>
</body>

</html>