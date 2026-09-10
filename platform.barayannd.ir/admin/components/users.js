import {_id , _qa , _q } from "../../common.js";
const user_card_contianer = _q(".users-cards");
const admin_modal = _q(".admin-modal");
const user_name_modal_holder = _id("modal_user_name");
const user_phone_modal_holder = _id("modal_user_phone");
const delete_user = _id("delete_user");
const promote_user = _id("promote_user");
const userDetail = [];
const user_el = [];
addEventListener("DOMContentLoaded" , async ()=>{
    try{
        const data = await fetch("/platform.barayannd.ir/admin/get_users.php");
        if (!data.ok){
            throw new Error("خطا در دریافت اطلاعات ");
        }
        const result = await data.json();
        if (data.error){
            if (data.error == "Not allowed") {
                throw new Error("برای دیدن کاربران بایستی در نقش ادمین باشید ");
            }else {
                throw new Error("ابتدا باید وارد حساب کاربری خود شوید");
            }
            return;
        }
        const users_data = result.data;
        for (let i = 0 ; i < users_data.length ; i++){
            userDetail.push(users_data[i]);
            let userType = "کاربر";
            if (users_data[i].user_type=="admin"){
                userType = "ادمین";
            }
            const user_card_element = document.createElement("div");
            user_card_element.classList.add("info-column");
            user_card_element.dataset.user_id = users_data[i].user_id;
            user_card_element.dataset.user_type = users_data[i].user_type;
            user_card_element.innerHTML=`
             <h3 id="user_name">${users_data[i].user_name}</h3>
             <h3 id="user_phone">${users_data[i].phone_number}</h3>
             <h3 id="user_type">${userType}</h3>`;
            user_el.push(user_card_element);
            user_card_contianer.append(user_card_element);
        }
    }catch (e) {
        console.log(e.error);
    }
})

user_card_contianer.addEventListener("click", (event) => {
    const card = event.target.closest(".info-column");
    const user_name = card.querySelector("#user_name");
    const user_phone = card.querySelector("#user_phone");
    const user_id = card.dataset.user_id;
    const user_type = card.dataset.user_type;
    if (!card) return;
    user_name_modal_holder.textContent = user_name.textContent;
    user_phone_modal_holder.textContent = user_phone.textContent;
    admin_modal.classList.add("active");
    console.log(user_type)
    if (user_type=="admin"){
        promote_user.disabled=true;
    }
    delete_user.addEventListener("click",async ()=>{
        console.log(user_id)
        try {
            const data = await fetch("/platform.barayannd.ir/admin/delete_user.php", {
                method: "POST",
                body: JSON.stringify({
                    "userID": user_id
                })
            });
            if (!data.ok) {
                throw new Error("خطا در حذف کاربر");
            }
            const result = await data.json();
            console.log(result);
            if (result.success) {
                setTimeout(()=>{
                    location.reload();
                },800);
            }
        }catch (e) {
            console.log(e.error);
        }
    })
    promote_user.addEventListener("click",async ()=>{
        console.log(user_id)
        try {
            const data = await fetch("/platform.barayannd.ir/admin/promote.php", {
                method: "POST",
                body: JSON.stringify({
                    "user_id": user_id
                })
            });
            if (!data.ok) {
                throw new Error("خطا در ارتقا کاربر");
            }
            const result = await data.json();
            console.log(result);
            if (result.success) {
                setTimeout(()=>{
                    location.reload();
                },800);
            }
        }catch (e) {
            console.log(e.error);
        }
    })
});
const close_admin_modal = _id("close-admin-modal");
close_admin_modal.addEventListener("click" , ()=>{
    admin_modal.classList.remove("active");
})