import { _id, _qa, _q } from "../../common.js";

const container = document.getElementById("container");
const card = document.getElementById("card");
const form = document.getElementById("form-card");
const guid = document.getElementById("guid-text");

setTimeout(() => {
    card.classList.add("visible");
    form.classList.add("visible");
}, 100);

function guid_text(text, color) {
    guid.innerHTML = text;
    guid.style.color = `${color}`;
}

const sign_up_tab = document.getElementById("signUp-tab");
const login_tab = document.getElementById("login-tab");
const user_phone_number = document.getElementById("phoneNumber");
const submit_btn = document.getElementById("submit_sign_up");

user_phone_number.addEventListener("keypress", () => {
    guid.style.color = "white";
});

const submit_btn_detail = (button_value, button_id) => {
    submit_btn.innerText = button_value;
    submit_btn.id = button_id;
};

const form_visible = () => {
    card.classList.add("visible");
    form.classList.add("visible");
};

const active_tab = (active_tab, non_active_tab) => {
    active_tab.classList.add("active");
    non_active_tab.classList.remove("active");
};

const main_form = document.getElementById("main-form");

login_tab.addEventListener("click", () => {
    card.classList.remove("visible");
    form.classList.remove("visible");
    setTimeout(() => {
        form_visible();
        active_tab(login_tab, sign_up_tab);
        submit_btn_detail("ورود به برآیند", "submit_login");
    }, 400);
});

sign_up_tab.addEventListener("click", () => {
    card.classList.remove("visible");
    form.classList.remove("visible");
    setTimeout(() => {
        form_visible();
        active_tab(sign_up_tab, login_tab);
        submit_btn_detail("ادامه ثبت نام", "submit_sign_up");
    }, 400);
});

container.addEventListener("click", (e) => {
    if (e.target.id == "submit_sign_up") {
        if (!user_phone_number.value) {
            guid_text("لطفا شماره تلفن خود را وارد کنید ", "red");
            return;
        }
        if (user_phone_number.value.length != 11) {
            guid_text("در وارد کردن شماره تلفن دقت کنید", "red");
            return;
        }
        const check_phone_for_registration = async () => {
            const data = await fetch(
                "/platform.barayannd.ir/login/check_phone_registration.php",
                {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        sessionDevice: navigator.userAgent,
                        sessionBrowser: navigator.platform,
                        phone_number: user_phone_number.value,
                    }),
                },
            );
            const res = await data.json();
            if (!res.success) {
                if (res.error === "already_registered") {
                    guid_text(
                        "شما قبلا ثبت نام کرده اید، لطفا برای ورود اقدام کنید",
                        "red",
                    );
                } else {
                    guid_text("مشکلی پیش آمد، لطفا دوباره تلاش کنید", "red");
                }
                return;
            }
            window.location.href = "registration/index.php";
        };
        check_phone_for_registration();
    }

    if (e.target.id == "submit_login") {
        if (!user_phone_number.value) {
            guid_text("لطفا شماره تلفن خود را وارد کنید ", "red");
            return;
        }
        if (user_phone_number.value.length != 11) {
            guid_text("در وارد کردن شماره تلفن خود دقت کنید ", "red");
            return;
        }
        const login_check = async () => {
            const data = await fetch(
                "/platform.barayannd.ir/login/login_check.php",
                {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        sessionDevice: navigator.userAgent,
                        sessionBrowser: navigator.platform,
                        identifier: user_phone_number.value,
                    }),
                },
            );
            const res = await data.json();
            switch (res.error) {
                case "Already logged in":
                    guid_text("شما وارد حساب کاربری شده اید !", "red");
                    break;
                case "not_registered":
                    guid_text(
                        "این شماره ثبت نام نشده است! لطفا ابتدا ثبت نام کنید",
                        "red",
                    );
                    break;
                default:
                    document.login_form.submit();
                    break;
            }
        };
        login_check();
    }
});
