import {_id, _qa, _q, modalMessageBox,} from "../../common.js";

document.addEventListener('DOMContentLoaded', async function() {
    const canvasElement = document.getElementById('assetChart');
    try {
        const data = await fetch("/platform.barayannd.ir/ends/daily_change.php" , {
            method: "GET",
            credentials: "include"
        });
        if (!data.ok){
            if (data.status==400){
                const message = _q(".guide_message");
                message.classList.add("active");
                throw new Error("پس از گذشت هفت روز از ثبت نام شما در سامانه، نمودار عملکردی فعال میشود");
            }
            throw new Error("خطا در ارتباط با سرور");
        }
        const result = await data.json();
        if (!result.success){
            throw new Error("خطایی رخ داد ");
        }
        const statistics = result.data;
        const days = ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنج‌شنبه', 'جمعه'];
        const labels = days.map(s=> s);
        const values = statistics.map(e=> e.balance*1000);
        console.log(values)
        if (result) {
            const ctx = canvasElement.getContext('2d');
            if (typeof Chart !== 'undefined') {
                const assetChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [
                            'شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنج‌شنبه', 'جمعه'
                        ],
                        datasets: [{
                            label: 'ارزش دارایی (میلیون تومان)',
                            data: values,
                            borderColor: '#007bff',
                            backgroundColor:'#001227',
                            fill: true,
                            pointRadius: 0,
                            tension: 0.3,
                        }]
                    },
                    options: {
                        responsive: true,
                        aspectRatio:false ,
                        plugins: {
                            legend: { position: 'top' }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                title: { display: true, text: 'میلیون تومان' }
                            },
                            x: {
                                title: { display: false, text: 'ماه' }
                            }
                        }
                    }
                });

                console.log('نمودار با موفقیت ایجاد شد!');
            } else {
                throw new Error('خطا: Chart.js بارگذاری نشده است!');
            }
        }
    }catch (e) {
         console.log(e.message)
    }
    try {
        const data = await fetch("/platform.barayannd.ir/ends/absolute_change.php");
        if (!data.ok){
            throw new Error("خطا در ارتباط با سرور");
        }
        const result = await data.json();
        if (!result.success){
            throw new Error("خطایی رخ داد مجددا تلاش کنید ")
        }
        const change_percentage = result.data.percentage;
        const change_type = result.data.type;
        const change_amount = result.data.value * 1000;
        const total_profit_EL = _id("total_profit");
        const total_profit_percentage_EL = _qa("#total_profit_percentage");
        if (change_type=="profit") {
            total_profit_EL.style.color="#0fff00";
            total_profit_percentage_EL[0].style.color="#0fff00";
            total_profit_percentage_EL[1].style.color="#0fff00";
        }else {
            total_profit_EL.style.color="red";
            total_profit_percentage_EL[0].style.color="red";
            total_profit_percentage_EL[1].style.color="red";
        }
        total_profit_EL.textContent = `${change_amount.toLocaleString("fa-IR")} تومان`;
        total_profit_percentage_EL.forEach(e=>{
            e.textContent =` ${change_percentage}%`;
        })
    }catch (e) {
        modalMessageBox(e.message , 'red');
    }

    try {
        const  data = await  fetch('/platform.barayannd.ir/ends/get_user.php',{
            method:"POST",
            headers:{'content-type' : 'application/json'}
        });
        if (!data.ok){
            throw new Error("خطا در ارتباط با سرور");
        }
        const result = await data.json();
        if (!result.success){
            throw new Error("خطایی رخ داد مجددا تلاش کنید ")
        }
        const starting_budget = result.data.starting_budget * 1000;
        const starting_budget_EL = _id("starting_budget");
        starting_budget_EL.textContent = `${starting_budget.toLocaleString("fa-IR")}تومان`
    }catch (e) {
      console.log(e.message);
    }
});