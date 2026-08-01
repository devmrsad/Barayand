<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="../assets/favicon/favicon-sign-blue-bg-png-transparent.png">
    <link rel="stylesheet" href="components/admin.css">
    <link rel="stylesheet" href="../components/homePage.css">
    <link rel="stylesheet" href="../common.css">
    <title>برآیند | ادمین</title>
</head>
<body>
     <div class="admin-modal">
         <div class="admin-modal-card">
             <div class="user-information">
                 <img src="assets/person-fill.svg" alt="" width="80px" height="80px">
                 <div class="user-details-sec">
                     <h2 id="modal_user_name"></h2>
                     <h4 id="modal_user_phone"></h4>
                 </div>
                 <img src="assets/x-circle.png" alt="" id="close-admin-modal">
             </div>
             <div class="user-opreations">
                 <button id="delete_user">
                     حذف کاربر
                     <img src="assets/person-fill-x.svg" alt="">
                 </button>
                 <button id="promote_user">
                     ارتقا به ادمین
                     <img src="assets/person-fill-up.svg" alt="">
                 </button>
             </div>
         </div>
     </div>
<?php include('components/header/header.php') ?>
     <div class="admin-tab-options">
       <a href="/platform.barayannd.ir/admin" class="tab-option">
        <img src="assets/grid-fill.svg" alt="" width="50px" height="50px">
        <h4>کنترل ویژگی ها </h4>
    </a>
       <a href="#" class="tab-option active">
        <img src="assets/person.svg" alt="" width="60px" height="60px">
        <h4>کاربران</h4>
    </a>
     </div>
     <div class="container-users">
         <div class="users-cards">
         <div class="info-column header">
             <h3>نام کاربر</h3>
             <h3>شماره تلفن</h3>
             <h3>نوع کاربری</h3>
         </div>
         </div>
     </div>

</body>
<script type="module" src="../check_session_status.js"></script>
<script type="module" src="components/users.js"></script>
<script type="module" src="components/header/header.js"></script>
<script type="module" src="../common.js"></script>
</html>