import {_qa , _id , _na} from "../../common.js";
const message_box = _id("admin-message-box");
function messageBoxShow(message , color){
    message_box.classList.add("shown");
    message_box.textContent = message;
    message_box.style.color = color;
    setTimeout(()=>{
        message_box.classList.remove("shown")
    },3000)
}
const input = {
    aiAvailability : false,
    trading : false,
    dailyAiMessages : 15
}
addEventListener("DOMContentLoaded" , async ()=>{
    try {
        const data = await fetch("/platform.barayannd.ir/admin/get_config.php");
        if (!data.ok){
            if (data.status == 403){
                throw new Error(" برای ایجاد تغییرات میبایست در نقش ادمین باشید ");
            }
            throw new Error("خطا در دریافت اطلاعات")
        }
        const result = await data.json();
        if (!result.success){
            switch (result.error){
                case "User not logged in":
                    throw new Error("ابتدا باید وارد حساب کاربری شوید");
                    break
                case "Not allowed":
                    throw new Error("برای دسترسی به این صفحه میبایست در نقش ادمین باشید");
                    break
                case "User not found":
                    throw new Error("کاربر یافت نشد");
                    break
            }
        }
        const adminstration_details = result.data;
        const ai_limit = adminstration_details.daily_ai_limit;
        const ai_availablity = adminstration_details.ai_enabled;
        const trading_availibilty = adminstration_details.trading_feature_enabled;
        input.aiAvailability = ai_availablity;
        input.trading = trading_availibilty;
        input.dailyAiMessages = ai_limit;
        const AIactiveRadio = _qa('input[name="aiAvailability"][value="true"]');
        const AIinactiveRadio = _qa('input[name="aiAvailability"][value="false"]');
        const TradingActiveRadio = _qa('input[name="trading"][value="true"]');
        const TradingInactiveRadio = _qa('input[name="trading"][value="false"]');
        let Ai_feature_enabled =false;
        let Trading_feature_enabled =false;
        if (ai_availablity){
            AIactiveRadio[0].checked=true;
            Ai_feature_enabled = true;

        }else{
            AIinactiveRadio[0].checked=true;
            Ai_feature_enabled = false;
        }
        if(trading_availibilty){
            TradingActiveRadio[0].checked = true;
            Trading_feature_enabled = true;
        }else {
            TradingInactiveRadio[0].checked = true;
            Trading_feature_enabled = false;
        }

        const ai_limit_input = _id("dailyAiMessages");
        ai_limit_input.value = ai_limit;
    }catch (e){
        messageBoxShow(e.message , "red");
    }
});
const CLASS_OF_YOUR_INPUTS = "inputs";
document.body.addEventListener("change" , async (e)=> {
    const tg = e.target
    if(tg.classList.contains(CLASS_OF_YOUR_INPUTS)){
        const name = tg.name;
        const value = tg.value;
        const value_boolain = JSON.parse(value);
        if(name in input) {
            if(tg.type === "checkbox"){
                input.name = tg.checked
            }
            else{
                input[name] = value_boolain;
            }
        }
        try {
            const data = await fetch("/platform.barayannd.ir/admin/insert_config.php", {
                method: "POST",
                body: JSON.stringify(input)
            });

            if (!data.ok) {
                if (data.status == 403) {
                    throw new Error(" برای ایجاد تغییرات میبایست در نقش ادمین باشید ");
                }
                throw new Error("خطا در انجام اطلاعات لطفا مجددا تلاش کنید");
            }
            const result = await data.json();
        }
        catch (err) {
            messageBoxShow(err.message, "red");
        }

    }
})

