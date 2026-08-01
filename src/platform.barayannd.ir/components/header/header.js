import {_q , _id , _qa } from '../../common.js'
const wrapper_preveiw = _q(".menu-wrapper");
const wrapper_items = _q(".wrapper-items-mobile");
const usdt_price_holderEL = _id("header_usdt_price");
const usdt_change_holderEL = _id("header_usdt_change");
wrapper_preveiw.addEventListener("click" , ()=>{
    console.log('clicked')
    wrapper_items.classList.toggle("opened");
});
addEventListener("DOMContentLoaded" , async ()=>{
    const usdt_price_req = await fetch("/platform.barayannd.ir/ends/get_prices.php?currency=usdt");
    if (usdt_price_req.ok){
        const result = await usdt_price_req.json();
        const usdt_data = result.data.usdt;
        const usdt_price = (usdt_data.price * 1000).toLocaleString();
        const usdt_change_data = usdt_data.change;
        const usdt_change_percentage = usdt_change_data.percentage;
        const usdt_change_type = usdt_change_data.type;
        usdt_price_holderEL.textContent=`1USDT = ${usdt_price} T`;
        if (usdt_change_type=='decrease'){
            usdt_change_holderEL.style.color="#ff5454";
            usdt_change_holderEL.style.backgroundColor="#dd0000";
            usdt_change_holderEL.textContent=`-${usdt_change_percentage}%`;
        }
        else {
            usdt_change_holderEL.style.color="#00FF11";
            usdt_change_holderEL.style.backgroundColor="rgba(0, 255, 17, 0.38)";
            usdt_change_holderEL.textContent=`+${usdt_change_percentage}%`;
        }
    }
    else {
        return;
    }
})
