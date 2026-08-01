import {_q , _id , _qa , _class} from "../common.js";
const user_fullname = _id("user_fullname");
const user_name = _id("user_name");
const user_phone = _id("user_phone");

const userDataRequest = async ()=>{
    const  data = await  fetch('/platform.barayannd.ir/ends/get_user.php',{
        method:"POST",
        headers:{'content-type' : 'application/json'}
    });
    const res = await data.json()
    const userInformations = res.data;
    user_fullname.textContent=userInformations.user_name;
    user_name.innerHTML=`${userInformations.username}@`;
    user_phone.innerHTML=`شماره موبایل : ${userInformations.phone_number}`;

}
userDataRequest()
// مودال ویرایش اطلاعات کاربری
const modal_container = _q(".modal-container");
const edit_user_icon = _id("edit_user");
//مسیج باکس برای نشون دادن پیام به کاربر در مودال
function modalMessageBox(message , color){
    const modal_textbox_content = _id("modal_message_content");
    const modal_textbox_icon = _q(".modal_messagebox_icon");
    const modal_messagebox_container = _q(".modal-message-box");
    modal_textbox_content.textContent=message;
    modal_textbox_icon.style.fill=color;
    modal_messagebox_container.classList.add("active");
    setTimeout(()=>{
        modal_messagebox_container.classList.remove("active");
    },3000)

}
// نمایش مودال
edit_user_icon.addEventListener("click" , ()=>{
    modal_container.classList.add("shown");
    // بستن مودال
    const close_modal = _id("close_modal");
    close_modal.addEventListener("click" ,()=>{
        modal_container.classList.remove("shown");
        userDataRequest()
    });
    // ثبت ویرایش در سمت سرور
    const edit_submit_button = _id("edit_user_submit");
    edit_submit_button.addEventListener("click" , ()=>{
        const username = document.getElementById("username");
        const userpass = document.getElementById("pass");
        const new_pass = document.getElementById("new_pass");
        const repass = document.getElementById("repass");
        if(!username.value || !userpass.value || !new_pass.value || !repass.value){
            modalMessageBox("لطفا تمام اطلاعات خواسته شده را وارد کنید ","red");
            return;
        }
        if (new_pass.value != repass.value){
            modalMessageBox('رمز عبور با تکرار آن یکسان نیست ','red');
            return;
        }
        const editUserInfo = async () => {
            const data = await fetch("/platform.barayannd.ir/ends/edit_profile.php", {
                method:"POST",
                headers:{'content-type': 'application/json'},
                body: JSON.stringify({
                    username: username.value,
                    current_password: userpass.value,
                    password: new_pass.value,
                    repassword: repass.value
                })
            });
            const res = await data.json();
            const request_response_err = String(res.error || "");
            if (request_response_err.includes("User not found")) {
                return modalMessageBox("کاربر یافت نشد", "red");
            }
            if (request_response_err.includes("Passwords do not match")) {
                return modalMessageBox("رمز عبور با تکرار آن یکسان نیست", "red");
            }
            if (request_response_err.includes("Wrong current password")) {
                return modalMessageBox("رمز عبور نادرست است", "red");
            }
            if (request_response_err.includes("Username already in use")) {
                return modalMessageBox("نام کاربری جدید با نام کاربری شما یکسان است", "red");
            }
            modalMessageBox("ویرایش با موفقیت انجام شد","green");
            setTimeout(()=>{
                modal_container.classList.remove("shown");
                userDataRequest()
            },800);
        };
        editUserInfo();
    })
})
