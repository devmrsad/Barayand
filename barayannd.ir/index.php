<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="common.css">
    <link rel="stylesheet" href="components/landing.css">
    <link rel="icon" href="./assets/favicon/favicon-sign-blue-bg-png-transparent.png">
    <title>  برآیند  | شبیه ساز سرمایگذاری </title>
</head>
<body>
   <?php include "components/header.php"; ?>
   <section class="hero-card">
      <img src="assets/svg/right-absteract.svg" alt="" class="right-absteract">
      <div class="hero-card-main">
        <img src="assets/svg/hero-btc-icon.svg" alt="" class="btc-icon-hero">
        <h2>با اعتبار مجازی به صورت کاملا رایگان سرمایگذاری را تمرین کن + <strong> چت با هوش مصنوعی متخصص </strong></h2>
        <h4>چت با هوش مصنوعی متخصص</h4>
      </div>
      <img src="assets/svg/left-absteract.svg" alt="" class="left-absteract">
   </section>
   <section class="login-cta">
        <img src="assets/svg/left-absteract.svg" alt="" class="login-cta-left-absteract">
        <div class="cta-icon">
          <img src="assets/svg/user-cta-login.svg" alt="" class="user-icon">
          <img src="assets/svg/left-arrow.svg" alt="" class="login-cta-left-arrow">
        </div>
        <div class="cta-text-content">
          <hr>
          <p>با ثبت نام رایگان در برآیند به صورت شخصی سازی شده مقدار اعتبار اولیه ی مد نظر خود  را انتخاب کنید و وارد دنیای مالی برآیند شوید</p>
          <button>
            <a href="/platform.barayannd.ir/login"><h4>ثبت نام در برآیند</h4></a>
            <img src="assets/svg/person-circle.svg" alt="" width="30px" height="30px">
          </button>
        </div>
   </section>
   <section class="ai-cta">
    <img src="assets/svg/right-absteract.svg" alt="" class="ai-cta-right-absteract">
    <div class="ai-cta-icon">
     <img src="assets/svg/robot.svg" alt="" class="ai-icon">
     <img src="assets/svg/right-arrow.svg" alt="" class="ai-cta-right-arrow">
     </div>
    <div class="ai-cta-content">
      <h4>هوش مصنوعی شخصی سازی شده </h4>
      <hr>
      <p>با دستیار برآیند به صورت  آنلاین چت کنید و سوالات خود را در زمینه های مالی بپرسید</p>
      <button>
        <img src="assets/svg/robot-ui-icon.svg" alt="">
        <a href="/platform.barayannd.ir/chatbot">هوش مصنوعی برآیند</a>
      </button>
     </div>
   </section>
   <section class="live-prices-cta">
     <img src="assets/svg/left-absteract.svg" alt="" class="live-prices-cta-left-absteract">
     <div class="live-prices-icon">
      <img src="assets/svg/live-prices-icon.svg" alt="" class="prices-icon">
       <img src="assets/svg/left-arrow.svg" alt="" class="live-prices-left-arrow">
     </div>
     <div class="live-prices-cta-currencies">
      <div class="live-prices-card-container">
       <div class="price-card">
        <div class="price-card-curreny-info">
          <p class="price-card-currency-price-change">3.8%</p>
          <div class="price-card-currency-perview">
            <h4>BTC</h4>
            <img src="assets/logo/currencies/BTC-icon-64.png" alt="" class="price-card-currency-logo">
          </div>
        </div>
        <h3 class="price-card-currency-price" id="btc_price_holder">  70,104.1 $</h3>
        <hr>
        <h3 class="price-card-currency-name"  >بیت کوین</h3>
       </div>
       <hr>
       <div class="price-card">
        <div class="price-card-curreny-info">
          <p class="price-card-currency-price-change">0.8%</p>
          <div class="price-card-currency-perview">
            <h4>XRP</h4>
            <img src="assets/logo/currencies/XRP-icon-64.png" alt="" class="price-card-currency-logo">
          </div>
        </div>
        <h3 class="price-card-currency-price" id="xrp_price_holder" > 1.6456 $ </h3>
        <hr>
        <h3 class="price-card-currency-name" >ریپل</h3>
       </div>
       <hr>
       <div class="price-card">
        <div class="price-card-curreny-info">
          <p class="price-card-currency-price-change">1.5%</p>
          <div class="price-card-currency-perview">
            <h4>TRX</h4>
            <img src="assets/logo/currencies/TRX-icon-64.png" alt="" class="price-card-currency-logo">
          </div>
        </div>
       <h3 class="price-card-currency-price" id="trx_price_holder" > 0.6785 $ </h3>
        <hr>
        <h3 class="price-card-currency-name" >ترون</h3>
       </div>
       <hr>
       <div class="price-card">
        <div class="price-card-curreny-info">
          <p class="price-card-currency-price-change">3.2%</p>
          <div class="price-card-currency-perview">
            <h4>ETH</h4>
            <img src="assets/logo/currencies/ETH-icon-64.png" alt="" class="price-card-currency-logo">
          </div>
        </div>
        <h3 class="price-card-currency-price" id="eth_price_holder" >  2,132.4 </h3>
        <hr>
        <h3 class="price-card-currency-name" >اتریوم</h3>
       </div>
     </div>
     <button>
      <a href="/platform.barayannd.ir/market">سایر ارزها</a>
      <img src="assets/svg/graph-up.svg" alt="">
     </button>
     </div>
   </section>
   <?php include "components/footer.php";?>
</body>
<script type="module" src="common.js"></script>
<script type="module" src="components/landing.js"></script>
</html>