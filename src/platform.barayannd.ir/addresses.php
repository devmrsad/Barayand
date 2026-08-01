<?php
# آدرس دهی نسبی صفحاتی که نیاز به ریدایرکت شدن به آنها را داریم در این فایل ذخیره کردیم
# از این جهت که میتوان درصورت نیاز آنها را تغییر داد و نیازی و گشتن در فایل های پروژه نخواهد بود


# در صورت دیپلوی شدن روی هاست، / به
# https://
#  تغییر می یابد
const ADDRESSES_PREFIX = '/platform.barayannd.ir/';
const MAIN_DOMAIN_PREFIX = '/barayannd.ir/';

$landingPage = MAIN_DOMAIN_PREFIX;
$homepage = ADDRESSES_PREFIX;
$loginPage = ADDRESSES_PREFIX."login";
$connection_error_page = ADDRESSES_PREFIX . "connection_error";
$logoutPage = ADDRESSES_PREFIX . "logout.php";