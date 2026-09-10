import {_id, _q} from "../../common.js";
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
addEventListener("DOMContentLoaded" , async ()=>{
    try{
        const data = await fetch("/platform.barayannd.ir/ends/fetch_balance.php");
        if (!data.ok){
            throw new Error("خطا در دریافت اطلاعات ");
        }
        const response = await data.json();
        const balance = response.data;
        const formatted_balace = balance.total_balance*1000;
        const total_value_holder = _id("total_asset_value");
        total_value_holder.textContent=`${formatted_balace.toLocaleString("fa-IR")} تومان `;
    }catch (e){
        modalMessageBox( e.message , "red");
    }
    const total_change_holder = _id("total_change_value");
    try{
        const data = await fetch("/platform.barayannd.ir/ends/absolute_change.php");
        if (!data.ok){
            throw new Error("خطا در دریافت اطلاعات ");
        }
        const response = await data.json();
        const change = response.data;
        if (!change){
            total_change_holder.textContent="اطلاعات در دسترس نمی باشد ";
            return;
        }
        const formatted_change = change.value * 1000;
        total_change_holder.textContent=`${formatted_change.toLocaleString('fa-IR')} تومان ${change.type === "increase" ? "+" : "-"}`;
        if (change.type === 'increase'){
            total_change_holder.style.color="#00C864";
        }
        else{
            total_change_holder.style.color="#ff3346";
        }
    }catch (e){
        modalMessageBox( e.message , "red");
    }
});
async function portfolio_cards(){
    try {
        const data = await fetch("/platform.barayannd.ir/ends/fetch_portfolio.php");
        if (!data.ok){
            throw new Error("خطا در دریافت اطلاعات دارایی ها ");
        }
        const response = await data.json();

        return response;
    }
    catch (e) {
         modalMessageBox(e.message , "red");
    }
}
const currencyNamesFa = {
    toman: 'تومان',
    usdt: 'تتر',
    silver_gram: 'گرم نقره',
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
portfolio_cards().then(portfolio => {
    const portfolio_object = portfolio.data;
    const portfolio_assets = portfolio_object.portfolio_data;

    const validAssets = [];
    portfolio_assets.forEach((e) => {
        if (e.amount) {
            const translatedName = currencyNamesFa[e.name] || e.name;
            validAssets.push({ ...e, name: translatedName, nameEn:e.name });
        }
    });
    const asset_card_container = _q(".assets-cards-container");
    validAssets.forEach((c) => {
        const asset_value = c.amount * c.unitPrice * 1000;
        const asset_card = document.createElement("div");
        const image_name = c.nameEn;
        asset_card.classList.add("assets-card-holder");
        asset_card.innerHTML = `
            <div class="assets-card-holder">
                <div class="asset-icon-sec">
                    <img src="../assets/logo/currencies/${image_name.toUpperCase()}-icon-64.png" alt="" width="40px" height="40px">
                    <h4>${c.name}</h4>
                </div>
                <div class="asset-info">
                    <h4><strong>مقدار : </strong>${c.amount}</h4>
                    <h4> تومان ${asset_value.toLocaleString("fa-IR")}</h4>
                </div>
            </div>`;
        asset_card_container.append(asset_card);
    });
});
function getTop3ByPercentage(arr) {
    const top3 = [];
    arr.forEach(item => {
        const percent = Number(item.percentage) || 0;
        const current = { ...item, percentage: percent };
        if (current.percentage!=0) {
            top3.push(current);
            top3.sort((a, b) => b.percentage - a.percentage);
        }
        if (top3.length > 3) {
            top3.pop();
        }
    });
    return top3;
}

portfolio_cards().then(result=>{
    let total_percent = 0;
    const assets_data = result.data.portfolio_data;
    const assets = [...assets_data];
    const top3 = getTop3ByPercentage(assets);
    const top_shares = []
    top3.forEach(e=>{
        const translatedName = currencyNamesFa[e.name] || e.name;
        total_percent=total_percent + Number(e.percentage);
        top_shares.push({ ...e, name: translatedName, nameEn:e.name });
    })
    const ctx = document.getElementById('asset-verity-chart');
    Chart.defaults.font.family = 'VazirBold';
    Chart.defaults.font.size = 13;
    const labels = top_shares.map(e => e.name);
    const data = top_shares.map(e => e.percentage);

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                label: 'نمودار دایره‌ای',
                data: data,
                backgroundColor: [
                    '#0fff00',
                    '#005bed',
                    '#606060'
                ],
                borderColor: [
                    '#ffffff',
                    '#ffffff',
                    '#ffffff'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

})