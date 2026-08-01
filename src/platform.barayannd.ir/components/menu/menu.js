import {_id , _q , _qa} from "../../common.js";
// تاریخ و زمان
function time() {
    const time_label = _id("time_label");
    const now = new Date();
    const options = {hour:'2-digit', minute:'2-digit' , second: '2-digit' , hour12:false , timeZone:'Asia/Tehran' }
    const string_time = now.toLocaleTimeString("fa-IR" , options);
    const timeParts = string_time.split(':');
    const hours = timeParts[0];
    const minutes = timeParts[1];
    const seconds = timeParts[2];
    time_label.innerHTML=`${hours}<span>:</span>${minutes}<span>:</span><span class="timer-sec-digit">${seconds}</span>`;
}
setInterval(time,1000);
time();
const persian_date = _id("persian_date");
const gregorain_date = _id ("gregorain_date");
const now = new Date(); // تاریخ و زمان فعلی سیستم (میلادی)
const options_fa = {
    month: 'long',
    day: 'numeric',
    weekday: 'long',
};
const options_en = {
    year : 'numeric',
    month: 'long',
    day: 'numeric',
}
const persianDate = now.toLocaleString("fa-IR", options_fa);
const grogoreianDate =  now.toLocaleString("en-US" , options_en);
persian_date.textContent = persianDate;
gregorain_date.textContent = `${grogoreianDate} , 15:00 GMT`;

// تب های منو
const menu_tabs = _qa(".menu-options-container div");
menu_tabs.forEach((e, index)=>{
    e.addEventListener("click" , ()=>{
        // ابتدا همه تب ها را غیرفعال کن
        for (let i = 0 ; i < menu_tabs.length ; i++) {
            menu_tabs[i].classList.remove("active");
            // همچنین محتوای مربوط به هر تب را پنهان کن (اگر این کار را انجام می دهید)
            // const tab_content = _id(menu_tabs[i].dataset.tabId); // فرض می کنیم data-tab-id دارید
            // tab_content.classList.add('hidden');
        }
        // تب کلیک شده را فعال کن
        e.classList.add("active");
        const wrapper_tabs = _q(".user_tabs");
        wrapper_tabs.classList.remove("active");

        // تابعی که محتوای تب را نمایش می دهد را صدا بزن
        // tabsChange(e.id); // فرض می کنیم div ها id دارند که با id محتوا یکی باشد
    })
})
const user_wrapper = _q(".menu-option-users");
user_wrapper.addEventListener("click" , ()=>{
    const wrapper_tabs = _q(".user_tabs");
    wrapper_tabs.classList.toggle("active");

})


