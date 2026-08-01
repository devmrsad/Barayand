<?php
    session_start();
    require_once("../addresses.php");
    global $homepage;

    if(isset($_SESSION["user_id"])){
        header("Location: $homepage");
        exit("Already logged in");
    }
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../common.css">
    <link rel="stylesheet" href="components/login.css">
    <link rel="icon" href="../assets/favicon/favicon-sign-blue-bg-png-transparent.png">
    <title>برآیند | ورود به حساب کاربری </title>
</head>
<body>
    <div class="container" id="container">
       <div class="top-welcome-card" id="card">
        <h2>به برآیند خوش آمدید</h2>
       </div>
       <div class="form-card" id="form-card">
        <button class="login-btn" id="login-tab">
            <p>ورود</p>
          </button>
        <button class="signUp-btn active" id="signUp-tab">
            <p>ثبت نام</p>
        </button>

        <form action="./login_handler.php" method="post" name="login_form" class="form-fields" id="main-form">
        <h3 class="guid-text" id="guid-text">برای ثبت نام در برآیند ، شماره موبایل خود را وارد کنید</h3>
        <input type="text" name="identifier" id="phoneNumber" placeholder="شماره موبایل">
        <button id="submit_sign_up" class="submit-btn" type="button">ادامه ثبت نام</button>
        </form>
        <div class="barayannd-footer">
            <hr>
            <img src="../assets/logo/logo-png-transparent.png" alt="">
            <hr>
        </div>
       </div>
    </div>
    <script src="../check_session_status.js"></script>
    <script type="module" src="components/login-ui.js"></script>

</body>
</html>