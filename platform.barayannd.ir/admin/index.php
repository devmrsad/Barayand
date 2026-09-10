<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="../assets/favicon/favicon-sign-blue-bg-png-transparent.png">
    <link rel="stylesheet" href="components/admin.css">
    <link rel="stylesheet" href="../common.css">
    <title>برآیند | ادمین</title>
</head>

<body>
    <?php include('components/header/header.php') ?>
    <div class="container">
        <div class="admin-tab-options">
            <a href="#" class="tab-option active">
                <img src="assets/grid-fill.svg" alt="" width="50px" height="50px">
                <h4>کنترل ویژگی ها </h4>
            </a>
            <a href="/platform.barayannd.ir/admin/users.php" class="tab-option">
                <img src="assets/person.svg" alt="" width="60px" height="60px">
                <h4>کاربران</h4>
            </a>
        </div>
        <div class="admin-option-content">
            <div class="controller-card">
                <h3> قابلیت گفت و گو با هوش مصنوعی</h3>
                <hr>
                <div class="checkbox">
                    <label for="aiAvailability-active">
                        فعال
                    </label>
                    <input type="radio" name="aiAvailability" id="aiAvailability-active" value="true" class="inputs">

                    <label for="aiAvailability-inactive">
                        غیر فعال
                    </label>
                    <input type="radio" name="aiAvailability" id="aiAvailability-inactive" value="false" class="inputs">
                </div>
            </div>
            <div class="controller-card">
                <h3>تعداد پردازش روزانه هوش مصنوعی</h3>
                <hr>
                <div class="checkbox">
                    <label for="enable">
                        تعداد پردازش
                    </label>
                    <select name="dailyAiMessages" id="dailyAiMessages" class="inputs">
                        <option value="15" id="wrapper-preview">15 پیام</option>
                        <option value="25">25 پیام</option>
                        <option value="30">30 پیام</option>
                        <option value="35">35 پیام</option>
                    </select>
                </div>
            </div>
            <div class="controller-card">
                <h3>امکان خرید و فروش ( معاملات ) </h3>
                <hr>
                <div class="checkbox">
                    <label for="trading-active">
                        فعال
                    </label>
                    <input type="radio" name="trading" id="trading-active" value="true" class="inputs">

                    <label for="trading-inactive">
                        غیر فعال
                    </label>
                    <input type="radio" name="trading" id="trading-inactive" value="false" class="inputs">
                </div>
            </div>
            <h4 id="admin-message-box"></h4>
            <button><a href="/platform.barayannd.ir/">بازگشت به صفحه اصلی</a></button>
        </div>
    </div>
</body>
<script type="module" src="../check_session_status.js"></script>
<script type="module" src="components/admin.js"></script>
<script type="module" src="components/header/header.js"></script>
<script type="module" src="../common.js"></script>

</html>