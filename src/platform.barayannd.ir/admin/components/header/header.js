import {_id} from "../../../common.js";
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
const now = new Date(); // تاریخ و زمان فعلی سیستم (میلادی)
const options_fa = {
    month: 'long',
    day: 'numeric',
    weekday: 'long',
};
const persianDate = now.toLocaleString("fa-IR", options_fa);
persian_date.textContent = persianDate;
