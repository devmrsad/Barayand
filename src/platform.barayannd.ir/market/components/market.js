import {_id , _q , _qa , _class , modalMessageBox} from "../../common.js";

const container = _q(".market-container");
async function getPrices(){
    try {
        const data = await fetch('/platform.barayannd.ir/ends/get_prices.php');
        if (!data.ok){
            throw new Error("خطا در دیافت اطلاعات");
        }
        const response = await data.json();
        return response
    }
    catch (e) {
        modalMessageBox(e.message , "red");
    }


}
const currencyNamesFa = {
    toman: 'تومان',
    usdt: 'تتر',
    gbp: 'پوند انگلستان',
    dhm: 'درهم امارات',
    doge: 'دوج‌کوین',
    eth : 'اتریوم',
    grade_18_gold_gram:'طلای 18 عیار',
    grade_24_gold_gram:'طلای 24 عیار',
    xaut:'تتر گلد',
    xrp:'ریپل',
    ton:'تون کیپر',
    tron:'ترون',
    ada:'کاردانو',
    sol:'سولانا',
    link:'لینک',
    eur:'یورو',
    btc:'بیت کوین',
    bnb:'بایننس کوین'
};
getPrices().then(result=>{
    const prices = result.data;
    delete prices['silver_gram']
    Object.entries(prices).forEach((key , value)=>{
        const priceDetail = key;
        let currency_name = priceDetail[0];
        const currency_price = priceDetail[1].price;
        const currency_change = priceDetail[1].change;
        currency_name = currencyNamesFa[currency_name];
        const priceCard = document.createElement("div");
        priceCard.classList.add("market-price-card");
        if (currency_change.type == 'increase'){
            priceCard.innerHTML=`                   
          <div class="currency-preveiw">
             <img src="/platform.barayannd.ir/assets/logo/currencies/${priceDetail[0].toUpperCase()}-icon-64.png" alt="" width="50px" height="44px">
             <h4>${currency_name}</h4>
          </div>
             <h4 id="price">${(currency_price * 1000).toLocaleString("fa-IR")}</h4>
             <h4 id="price_change" style="color: #00FF11">${currency_change.percentage}%+</h4>`;
        }else {
            console.log("red");
            priceCard.innerHTML=`                   
          <div class="currency-preveiw">
             <img src="/platform.barayannd.ir/assets/logo/currencies/${priceDetail[0].toUpperCase()}-icon-64.png" alt="" width="50px" height="44px">
             <h4>${currency_name}</h4>
          </div>
             <h4 id="price">${(currency_price * 1000).toLocaleString("fa-IR")}</h4>
             <h4 id="price_change" style="color: red">${currency_change.percentage}%-</h4>`;
        }
        container.append(priceCard)
    });
})