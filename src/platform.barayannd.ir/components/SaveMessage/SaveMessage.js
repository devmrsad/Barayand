import {_q , _id , _qa , _class} from "../../common.js";
const saveMessage_container = _q(".save_messages_container");
function messageCard(content, date, id){
    const message_card = document.createElement("div");
    message_card.classList.add("message-card-border");
    message_card.dataset.id = id;   // <- این مهم است
    message_card.innerHTML = `
    <div class="message-card-top">
      <div class="barayannd-ai">
        <img src="../../assets/svg/ai-save-message.svg" alt="">
        <h4>هوش مصنوعی برآیند</h4>
      </div>
      <div class="actions">
        <p id="message_data">${date}</p>

        <div class="delete-message">
          <h4>حذف</h4>
          <img src="../../assets/svg/trash.svg" alt="">
        </div>
      </div>
    </div>

    <div class="message-text-content">
      <p id="message_content">${content}</p>
    </div>
  `;
    saveMessage_container.appendChild(message_card);
}
const SaveMessageRequset = async ()=>{
    const data = await fetch("/platform.barayannd.ir/ends/saved_messages.php" , {
        method:"GET",
        headers:{'content-type':'application/json'}
    });
    const res = await data.json()
    const Messages = res.data;
    console.log(res)
    Messages.forEach((i)=>{
        messageCard(i.content , i.date , i.id);
    })

}
SaveMessageRequset();
saveMessage_container.addEventListener("click", async (event)=>{
    const delete_btn = event.target.closest(".delete-message");
    if (!delete_btn) return;
    // کارت والد را با یک روش مطمئن بگیر
    const card = delete_btn.closest(".message-card-border");
    if (!card) return;
    const message_id = card.dataset.id;
    console.log("Card dataset ID:", card.dataset.id);
    // حذف سرور
    const data = await fetch('/platform.barayannd.ir/ai/modify_message.php',{
        method:"POST",
        headers:{'content-type':'application/json'},
        body:JSON.stringify({
            "message_id":card.dataset.id,
            "change_to": true,
            "option": "deleted"
        })
    });
    const res = await data.json();
    console.log(res)
    card.remove();
});