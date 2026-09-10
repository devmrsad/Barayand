import {_id , _qa , _q} from "../../common.js";


const buy_transaction = _id("buy_transaction");
const sell_transaction = _id("sell_transaction");
const transaction_count = _id("count");
const transaction_value = _id("final_amount");
const main_price_holder = _id("selected_price");
const submit_transaction = _id("submit_transaction");
const en_currency_name_modal = _q(".en_currency_name");
const fa_currency_name_modal = _q("#fa_currency_name");
const close_modal_confirmation = _id("reject_button");
const modal_confirmation_btn = _id("confirm_button");
const confirmation_modal = _q(".transaction-confirm-modal");
const confirmation_button = _id("confirm_button");
const wrapper_image = _id("currency_img");
const wrapper_name = _id("wrapper_preveiw_name");
const confirmation_modal_img = _id("confirm_rrency_icon");
let transaction_type = true;
const balance_holder = _id("user_balance");

let userPortfolio = null;

function modalMessageBox(message , color){
    const modal_textbox_content = _id("modal_message_content");
    const modal_textbox_icon = _q(".modal_messagebox_icon");
    const modal_messagebox_container = _q(".modal-message-box");
    modal_textbox_content.textContent=message;
    modal_textbox_icon.style.fill=color;
    modal_messagebox_container.classList.add("active");
    setTimeout(()=>{
        modal_messagebox_container.classList.remove("active");
    },4000);
}
async function portfolio_data(){
    const portfolio_data = await fetch("/platform.barayannd.ir/ends/fetch_portfolio.php");
    if (portfolio_data.ok){
        const res = await portfolio_data.json();
        if (res.data){
            const balances = res.data.portfolio_data;
            userPortfolio = balances;
            const toman_available = balances[0];
            const user_balance = toman_available.amount * 1000;
            const formatted_balance = user_balance.toLocaleString("fa-IR");
            balance_holder.innerHTML=` موجودی شما : ${ formatted_balance} تومان`;
        }
        else {
            modalMessageBox("خطایی پیش آمد " , "red");
        }
    }
    else {
        modalMessageBox("ارتباط برقرار نشد" , "red");
    }
}
// تابع عوض کردن تب خرید و فروش
function changeTab(non_active,active_tab, color , btn_text){
    active_tab.style.backgroundColor=color;
    non_active.style.backgroundColor="transparent";
    submit_transaction.textContent=btn_text;

}
// تابع حذف علامت های غیر مرتبط با اعداد برای تمیز کردن و خالص کردن قیمت ها جهت محاسبات
function cleanPrice(str) {
    // ۱. تبدیل اعداد فارسی به انگلیسی (اگر لازم باشد)
    const persianToEnglish = {
        "۰": "0", "۱": "1", "۲": "2", "۳": "3", "۴": "4",
        "۵": "5", "۶": "6", "۷": "7", "۸": "8", "۹": "9"
    };
    let cleanedStr = str.replace(/[۰-۹]/g, (char) => persianToEnglish[char]);


    cleanedStr = cleanedStr.replace(/[^0-9]/g, '');

    return cleanedStr;
}
// عوض کردن پیش نمایش منو کشویی قیمت ها
function wrapper_preveiw(currency_name , currency_image){
    wrapper_name.textContent = currency_name;
    wrapper_image.src=`../assets/logo/currencies/${currency_image}-icon-64.png`;

}
// عوض کردن تب ها
buy_transaction.addEventListener("click",()=>{
    changeTab(sell_transaction , buy_transaction ,'#00C864' , "تایید خرید" );
    transaction_type=true;
    confirmation_button.style.backgroundColor="#00C864";
    confirmation_button.textContent=" خرید";

});
sell_transaction.addEventListener("click",async ()=>{
    changeTab( buy_transaction , sell_transaction ,'#fa2626' , 'تایید فروش' );
    transaction_type=false;
    confirmation_button.style.backgroundColor="#fa2626";
    confirmation_button.textContent=" فروش";
})
// مربوط به نمایش و غیر فعال کردن نمایش مودال قیمت ها
const modal_activator = _id("wrapper_arrow");
const currency_modal = _q(".currencies-modal");
modal_activator.addEventListener("click",()=>{
    currency_modal.classList.add("active");
    const close_modal = _q(".close_modal");
    close_modal.addEventListener("click",()=>{
        currency_modal.classList.remove("active");
    })
})



// مربوط به گرفتن قیمت ها و قراردادن آن ها در کارت های قیمتی مودال قیمت ها
const price_rows = _qa(".prices-row div");
function priceFill(crypto_prices) {

    let selectedCurrency = null;
    let selectedCurrencyName = null;

    // یک بار برای همیشه Submit را تنظیم می‌کنیم
    price_rows.forEach((row) => {
        const coin = row.dataset.coin
        const info = crypto_prices[coin];

        // اگر info نبود لیسنر کلیک ثبت نشود!
        if (!info) {
            return;
        }

        const priceEl = row.querySelector(".price");
        const changeEl = row.querySelector(".change");
        const formatted_price = Number(info.price * 1000).toLocaleString("fa-IR");
        priceEl.textContent = formatted_price;

        const change = info.change;

        if (change.type === 'increase') {
            changeEl.style.color = "#00C864";
            changeEl.textContent = `${change.percentage}%+`;
        } else {
            changeEl.style.color = "#eb0a36";
            changeEl.textContent = `${change.percentage}%-`;
        }

        // ثبت کلیک روی کارت
        row.onclick = () => {

            currency_modal.classList.remove("active");

            const currencyNameEl = row.querySelector(".currency_name_value");

            transaction_count.value = ""
            transaction_value.value = ""

            selectedCurrency = coin;
            selectedCurrencyName = currencyNameEl.textContent;

            main_price_holder.textContent = `قیمت ${selectedCurrencyName} : ${formatted_price} تومان`;

            const portfolioItem = userPortfolio.find(item => item.name === coin);
            const userBalance = portfolioItem ? portfolioItem.amount : 0;

            balance_holder.textContent = `موجودی ${selectedCurrencyName} شما: ${userBalance}`
            ////////////////////////////////////////////////////////////////////////////////////////////

            wrapper_preveiw(selectedCurrencyName, coin.toUpperCase());
            wrapper_name.dataset.coint=coin;
        };

    });
}
addEventListener("DOMContentLoaded",async ()=>{
    const total_balance = async ()=> {
        portfolio_data()
        const prices = await fetch("/platform.barayannd.ir/ends/get_prices.php");
        const result = await prices.json();
        const cryptoKeys = [
            "ada", "btc", "doge", "eth", "link","bnb","silver_gram" , "dhm" , "gbp",
            "sol", "ton", "usdt", "xrp" , "xaut" ,"tron" ,'grade_18_gold_gram','grade_24_gold_gram',
        ];
        const cryptos = {};
        for (const key in result.data){
           if (cryptoKeys.includes(key)){
               cryptos[key] = result.data[key];
           }
        }
        const gold_price = result.data['grade_18_gold_gram'].price * 1000;
        selected_price.innerHTML=`<h4 id='selected_price'>قیمت طلا: <span>${gold_price.toLocaleString("fa-IR")}</span> تومان </h4>`;
        priceFill(cryptos);

    }
    total_balance()
});

transaction_count.addEventListener("input" , (e)=>{
    const selected_currency_price = _id("selected_price");
    const cleaned_price = cleanPrice(selected_currency_price.textContent);
    const transaction_total_value = cleaned_price * transaction_count.value;
     transaction_value.value =`${transaction_total_value.toLocaleString('fa-IR')} تومان `;
})
submit_transaction.addEventListener("click",
    (e) => {
       if (!transaction_count.value){
           transaction_count.style.borderColor="red";
           return;
       }
        transaction_count.style.borderColor="var(--clr-1)";
        confirmation_modal.classList.add("active");
        const selected_currency_price = _id("selected_price");
        const cleaned_price = cleanPrice(selected_currency_price.textContent);
        const fa_currency_title = selected_currency_price.textContent.split(":")[0];
        fa_currency_name_modal.textContent = fa_currency_title;
        const selected_currency_img = _id("confirm_currency_icon");
        selected_currency_img.src = wrapper_image.src;
        const confirmation_modal_transaction_value = _id("transaction_value");
        const transaction_total_value = cleaned_price * transaction_count.value;
        confirmation_modal_transaction_value.textContent =`${transaction_total_value.toLocaleString('fa-IR')} تومان `;
        close_modal_confirmation.addEventListener("click", () => {
            confirmation_modal.classList.remove("active");
        });
    });
confirmation_button.addEventListener("click" , ()=>{
        const selected_currency = wrapper_name.dataset.coint;
        const transactionRequest = async ()=>{
            let url ="/platform.barayannd.ir/ends/buy.php";
            if (transaction_type){
                 url = "/platform.barayannd.ir/ends/buy.php";
            }else {
                 url = "/platform.barayannd.ir/ends/sell.php";
            }
            const res = await fetch( url , {
                method:"POST",
                headers:{"content-type":"application/json"} ,
                body:JSON.stringify({
                    "currency":selected_currency,
                    "amount":transaction_count.value
                })
            });
            const result = await res.json();
            console.log(result);
            if(result.error){
                switch (result.error){
                    case "Trading not allowed":
                        modalMessageBox(" قابلیت معامله غیر فعال می باشد" , "red");
                        confirmation_modal.classList.remove("active");
                        break;
                    case "Failed to create portfolio":
                        modalMessageBox( "خطا در ایجاد پورتفولیو","red");
                        confirmation_modal.classList.remove("active");
                        break;
                    case "Insufficient balance":
                        modalMessageBox("موجودی شما برای انجام این تراکنش کافی نمی باشد ");
                        confirmation_modal.classList.remove("active");
                        break;

                }
            }else {
                console.log("ok");
                modalMessageBox("تراکنش با موفقیت انجام شد !" , "green");
                setTimeout(()=>{
                    confirmation_modal.classList.remove("active");
                    location.reload()
                } , 300);
            }

        }
        transactionRequest();
});
