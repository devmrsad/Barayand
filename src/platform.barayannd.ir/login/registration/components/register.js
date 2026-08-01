import {_q, _qa , _id} from '../../../common.js';
// دریافت اطلاعات فرم
const user_first_name  = _id("first_name");
const user_last_name = _id("last_name");
const user_name = _id("user_name");
const user_password = _id("password");
const user_re_password = _id("re_password");
const user_fullname_inputs = _qa(".form-field-name div input");
const prefered_budget_main = _qa(".budget-amout-checkboxes div input");
const user_name_inputs = _qa(".form-field-user-name div input");
const user_password_inputs = _qa(".form-field-password div input");
const form_text_box = _id("main_form_guide");
// دکمه ارسال فرم اصلی ( ساختار دستکتاپ )
const main_submit_btn = _id("sign_up_btn");
// فانکشنی که برای نمایش پیام های خطا به کاربر استفاده میشود ( در ساختار دستکتاپ )
function main_form_text_box(message , color){
    const guide_text_box_container = _q(".guide_text_box");
    form_text_box.textContent=message;
    form_text_box.style.color=color;
    guide_text_box_container.classList.add("show");
    setTimeout(()=>{
        guide_text_box_container.classList.remove("show");
    },3000);
}
// فانکشنی که برای نمایش پیام های خطا به کاربر استفاده میشه ( ساختار موبایل )
function mobile_form_text_box(message , color ){
    const mobile_text_guide = _id("mobile_guide_text");
    mobile_text_guide.textContent = message;
    mobile_text_guide.style.fontFamily="VazirLight";
    mobile_text_guide.style.color=color;
}
// فانکشنی که خالی نبودن ورودی های فرم را بررسی میکنه
function inputs_validation(input_group){
    let isValid = true;

    input_group.forEach((i)=> {
        if (!i.value) {
            i.style.borderColor = "red";
            isValid = false;
        }
    });

    input_group.forEach((i)=>{
        i.addEventListener("keypress" , ()=>{
            // i.style.borderColor="var(--bg-2)";
            for (let i =0 ; i < input_group.length; i++){
                input_group[i].style.borderColor="var(--bg-2)";
            }
        });
    });

    return isValid;
}
// فرایند بررسی و ارسال فرم ( ساختار دسکتاپ )
main_submit_btn.addEventListener("click", ()=> {
    const choosen_budget = _qa('.budget-amout-checkboxes div input[name="budget"]:checked');
    const g1 = inputs_validation(user_fullname_inputs);
    const g2 = inputs_validation(user_name_inputs);
    const g3 = inputs_validation(user_password_inputs);
    if (!g1 || !g2 || !g3) {
        return;
    }
    if (user_name.value.length < 3 || user_first_name.value.length < 3 || user_last_name.value.length < 3) {
        main_form_text_box("در وارد کردن اطلاعات کاربری دقت کنید", "red");
        return;
    }
    if (user_password.value != user_re_password.value) {
        main_form_text_box("رمز عبور با تکرار آن یکسان نیست !", "red");
        return;
    }
    if (!choosen_budget[0]) {
        main_form_text_box("بودجه مدنظر خود را برای شروع انتخاب کنید ", "red")
        return;
    }
    document.form1.submit();
});
// اسکریپت های مربوط به منوی کشویی انتخاب بودجه ( در ساختار موبایل )
const budget_wrapper = _id("budget_wrapper");
const wrapper_items = _id("wrapper_items");
const preferred_budget_mobile = _id("preferred_budget");
const mobile_budget_input = _id("mobile_budget_input");
const mobileBudgetValues = [10000, 50000, 100000, 250000, 1000000];
const items = _qa(".wrapper-items li");
budget_wrapper.addEventListener("click", () => {
    wrapper_items.classList.toggle("visible");
});
items.forEach((item, index) => {
    item.addEventListener("click", (e) => {
        e.stopPropagation();
        preferred_budget_mobile.innerHTML = item.textContent;
        mobile_budget_input.value = mobileBudgetValues[index];
        wrapper_items.classList.remove("visible");
    });
});
const mobile_submit_btn = _id("sign_up_btn_mobile");
// ارسال فرم (ساختار موبایل )
mobile_submit_btn.addEventListener("click", ()=> {
    const g1 = inputs_validation(user_fullname_inputs);
    const g2 = inputs_validation(user_name_inputs);
    const g3 = inputs_validation(user_password_inputs);
    if (!g1 || !g2 || !g3) {
        return;
    }
    if (user_name.value.length < 3 || user_first_name.value.length < 3 || user_last_name.value.length < 3) {
        mobile_form_text_box("در وارد کردن اطلاعات کاربری دقت کنید", "red");
        return;
    }
    if (user_password.value != user_re_password.value) {
        mobile_form_text_box("رمز عبور با تکرار آن یکسان نیست !", "red");
        return;
    }
    if (!mobile_budget_input.value) {
        mobile_form_text_box("بودجه مدنظر خود را برای شروع انتخاب کنید ", "red")
        return;
    }
    document.form1.submit();
});

