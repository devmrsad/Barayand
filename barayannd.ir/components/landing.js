import {_id , _qa , _q , _class} from  '../common.js';
const btc_price_holder = _id("btc_price_holder");
const xrp_price_holder = _id("xrp_price_holder");
const eth_price_holder = _id("eth_price_holder");
const trx_price_holder = _id("trx_price_holder");
const change_holders = _qa(".price-card-currency-price-change");
const prices_info = [];
const get_price = async ()=>{
    try {
        const data = await fetch("/platform.barayannd.ir/ends/get_prices.php", {
            method: "GET",
            headers: {
                "Content-Type": "application/json"
            }
        });
        if (!data.ok){
            throw new Error("خطا در دریافت اطلاعات");
        }
        const currencies = ["btc" , "xrp" , "eth" , "tron"];
        const result = await data.json();
        const prices = result.data
        for (let i = 0 ; i < 4 ; i++){
            const price = prices[currencies[i]];
            const change = prices[currencies[i]].change.percentage;
            const change_type = prices[currencies[i]].change.type;
            if (change_type=='decrease'){
                change_holders[i].style.backgroundColor="#8d0000";
                change_holders[i].style.Color="#ff6060";
                change_holders[i].textContent = `${change}%-`;
            }else {
                change_holders[i].style.backgroundColor="#57ff5d64";
                change_holders[i].style.Color="var(--clr-2)";
                change_holders[i].textContent = `${change}%+`;
            }
            console.log(change)
            prices_info.push(price);
        }
        console.log(prices_info);
        btc_price_holder.textContent =`${(prices_info[0].price * 1000).toLocaleString('fa-IR')} تومان`;
        xrp_price_holder.textContent =`${(prices_info[1].price * 1000).toLocaleString('fa-IR')} تومان`;
        eth_price_holder.textContent =`${(prices_info[2].price * 1000).toLocaleString('fa-IR')} تومان`;
        trx_price_holder.textContent =`${(prices_info[3].price * 1000).toLocaleString('fa-IR')} تومان`;
    }catch (e){
        console.log(e.error);

    }

}
get_price();
const home = _id("home_icon");
const panel = _id("panel_icon");
const about = _id("about_icon");

home.addEventListener("click",()=>{
    location.href="/barayannd.ir/";
})
about.addEventListener("click",()=>{
    location.href="/barayannd.ir/contact";
})
panel.addEventListener("click",()=>{
    location.href="/barayannd.ir/#";
})
