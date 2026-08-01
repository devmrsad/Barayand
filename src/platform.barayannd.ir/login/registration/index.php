<?php
session_start();
require_once("../../addresses.php");
global $homepage;

if (isset($_SESSION["user_id"])) {
    header("Location: $homepage");
    exit("Already logged in");
}

if (empty($_SESSION["phone_number"])) {
    header("Location: $homepage");
    exit("Phone number not verified");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/favicon/favicon-sign-blue-bg-png-transparent.png">
    <link rel="stylesheet" href="../../common.css">
    <link rel="stylesheet" href="components/register.css">
    <title>برآیند | تکمیل ثبت نام</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>ثبت نام شما پس از این مرحله تکمیل می شود</h1>
        </div>
        <form class="form-container" action="signup_handler.php" method="post" name="form1">
            <h4>نام کاربری و کلمه ی ورود را خاطر بسپارید! از این پس آنها برای ورود به حساب کاربری خود استفاده خواهید کرد
            </h4>
            <div class="form-fields">
                <div class="form-field-name">
                    <div class="form-field-align">
                        <label for="first_name">نام کوچک </label>
                        <input type="text" id="first_name" name="first_name">
                    </div>
                    <div class="form-field-align">
                        <label for="last_name">نام خانوادگی </label>
                        <input type="text" id="last_name" name="last_name">
                    </div>
                </div>
                <div class="form-field-user-name">
                    <div class="form-field-align">
                        <label for="user_name">نام کاربری</label>
                        <input type="text" id="user_name" name="user_name">
                    </div>
                </div>
                <div class="form-field-password">
                    <div class="form-field-align">
                        <label for="password">رمز عبور</label>
                        <input type="password" id="password" name="password" placeholder="رمز عبور">
                    </div>
                    <div class="form-field-align">
                        <label for="re_password"></label>
                        <input type="password" id="re_password" name="re_password" placeholder="تکرار رمز عبور">
                    </div>
                </div>
                <div class="guide_text_box">
                    <img src="assets/exclamation-square.svg" alt="">
                    <h4 id="main_form_guide">در وارد کردن اطلاعات دقت کنید</h4>

                </div>
                <button id="sign_up_btn" type="button">تکمیل ثبت نام </button>
            </div>
            <input type="hidden" name="budget" id="mobile_budget_input">
            <div class="side-budget-card">
                <div class="side-budget-card-title">
                    <h4>سرمایه اولیه درخواستی</h4>
                </div>
                <h4 id="main_budget_prefference_guide">سرمایه اولیه کمتر باعث ایجاد دید واقع گرایانه تری در شما خواهد شد
                </h4>
                <hr>
                <div class="budget-amout-checkboxes">
                    <div class="budget-field-align">
                        <label for="">ده میلیون تومان </label>
                        <input type="radio" name="budget" id="budget_1" value="10000">
                    </div>
                    <div class="budget-field-align">
                        <label for="">پنجاه میلیون تومان </label>
                        <input type="radio" name="budget" id="budget_2" value="50000">
                    </div>
                    <div class="budget-field-align">
                        <label for="">صد میلیون تومان </label>
                        <input type="radio" name="budget" id="budget_3" value="100000">
                    </div>
                    <div class="budget-field-align">
                        <label for="">دویست و پنجاه میلیون تومان </label>
                        <input type="radio" name="budget" id="budget_4" value="250000">
                    </div>
                    <div class="budget-field-align">
                        <label for="">یک میلیارد تومان </label>
                        <input type="radio" name="budget" id="budget_5" value="1000000">
                    </div>
                </div>
            </div>

            <div class="mobile-budget-card">
                <div class="right-abstract"></div>
                <div class="left-abstract"></div>
                <div class="mobile-budget-form">
                    <h4>سرمایه اولیه درخواستی</h4>
                    <hr>
                    <p id="mobile_guide_text">سرمایه اولیه کمتر باعث ایجاد دید واقع گرایانه تری در شما خواهد شد</p>
                    <ul class="budget-wrapper" id="budget_wrapper">
                        <div class="wrapper-items" id="wrapper_items">
                            <li>ده میلیون تومان </li>
                            <li>پنجاه میلیون تومان</li>
                            <li>صد میلیون تومان </li>
                            <li>دویست و پنجاه میلیون تومان</li>
                            <li>یک میلیارد تومان</li>
                        </div>
                        <div class="wrapper-default">
                            <li id="preferred_budget">ده میلیون تومان </li>
                            <img src="../../assets/svg/arrow-down.svg" alt="">
                        </div>
                    </ul>
                    <button id="sign_up_btn_mobile" type="button">تکمیل ثبت نام</button>
                </div>
            </div>
    </div>
    <script type="module" src="../../check_session_status.js"></script>
    <script type="module" src="../../common.js"></script>
    <script type="module" src="components/register.js"></script>
</body>

</html>