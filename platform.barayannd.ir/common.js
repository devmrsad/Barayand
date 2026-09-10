export const _q = q => document.querySelector(q);
export const _qa = q => document.querySelectorAll(q);
export const _id = id => document.getElementById(id);
export const _class = cl => document.getElementsByClassName(cl);
export const _na = name => document.getElementsByName(name);

export function modalMessageBox(message , color){
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