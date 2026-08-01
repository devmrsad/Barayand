import {_id , _q , modalMessageBox} from "../../common.js";
const ai_messages = _q(".message-card");
const message_box = _id("message");
const message_send_btn = _q(".send-message");

let chatStartBadge = document.querySelector("#chat-start-badge")
function message_EL_creator(sender , content){
    const message = document.createElement("p");
    message.classList.add("ai-message");
    if (sender=="user"){
        message.classList.add("user");
        message.textContent=`${content}`;
    }else if(sender=="ai"){
        message.classList.add("ai");
        message.textContent=`${content}`;
    }
    ai_messages.append(message);
}
addEventListener("DOMContentLoaded",async ()=>{
    try {
        const data = await fetch("/platform.barayannd.ir/ai/fetch_chat.php");
        if (!data.ok){
            throw new Error("خطا در ارتباط با سرور ");
        }
        const res = await data.json();
        if (!res.success){
            throw new Error("کاربر پیدا نشد ، برای ثبت نام در برآیند اقدام کنید ");
        }
        const messages = res.data;
        if(messages.length > 0){
            messages.forEach(e=>{
                const message = e.message;
                const sender = e.sender;
                message_EL_creator(sender , message);
            })
        }
        else{
            chatStartBadge.classList.remove("disabled")
        }
    }catch (e){
        modalMessageBox(e.error , "red");
    }
});
message_send_btn.addEventListener("click" , async ()=>{
    message_send_btn.disabled=true;
    const user_message = document.createElement('p');
    const ai_message = document.createElement("p");
    user_message.classList.add("user");
    user_message.classList.add("ai-message");
    ai_message.classList.add("ai-message");
    ai_message.classList.add("ai");
    if (message_box.value.length < 15){
        modalMessageBox("پیام شما باید حداقل از 15 کاراکتر بیشتر باشد ");
        console.log("length error");
        setTimeout(() => {
            message_send_btn.disabled=false;
        }, 500)
        return;
    }
    user_message.textContent = message_box.value;
    chatStartBadge.classList.add("hidden")
    setTimeout(async () => {
        ai_messages.append(user_message);
        const chatArea = document.querySelector(".chat-area")
        chatArea.scrollTo({top : chatArea.scrollHeight, behavior : 'smooth'})

        try{
            const data = await fetch("/platform.barayannd.ir/ai/send_message.php",
                {
                    method:"POST",
                    body:JSON.stringify({
                        "message":message_box.value
                    })
                });
            if (!data.ok){
                throw new Error("خطا در ارسال پیام ");
            }
            const result = await data.json();
            if (result){
                setTimeout(() => {
                    message_send_btn.disabled=false;
                }, 500)
            }
            if (!result){
                modalMessageBox("دریافت پاسخ با خطا همراه بود ، مجددا تلاش کنید ","red");
                setTimeout(() => {
                    message_send_btn.disabled=false;
                }, 500)
                return;
            }
            if(result.error){
                switch (result.error){
                    case "Closed":
                        window.location.reload()
                        break;
                    case "Limit":
                        modalMessageBox("شما به سقف محدودیت پیام روزانه خود رسیدید.","red");
                        break;
                    case "Limited":
                        modalMessageBox("شما به سقف محدودیت پیام روزانه خود رسیدید.","red");
                        break;

                    case "Processing":
                        modalMessageBox("درحال پردازش....","red");
                        break;
                    case "AI unavailable":
                        modalMessageBox("در این لحظه امکان استفاده از دستیار هوش مصنوعی غیر فعال است");
                    default:
                        modalMessageBox("خطای ناشناخته")
                }
                setTimeout(() => {
                    message_send_btn.disabled=false;
                }, 500)

            }
            else{
                setTimeout(() => {
                    message_send_btn.disabled=false;
                }, 500)
                const response = result.data.response;
                ai_message.textContent=`${response.content}`;
                ai_messages.append(ai_message);
            }

        }catch (e) {
            console.log(e.message);
        }
        finally {
            chatArea.scrollTo({top : chatArea.scrollHeight, behavior : 'smooth'})
        }
        message_box.value=null;
    }, 300)
});
const back_btn_main = _id("back_btn");
const back_btn_mobile = _id("back_btn_mobile");
const back_btn =[ back_btn_mobile , back_btn_main];
back_btn.forEach((e=> {
    e.addEventListener("click", ()=>{
    location.href = "/platform.barayannd.ir/";
    })
}))

