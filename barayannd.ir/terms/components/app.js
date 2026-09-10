import {_qa} from "../../common.js"
const term_cards = _qa(".terms .term-card");
console.log(term_cards)
term_cards.forEach(e=>{
    e.addEventListener("click", ()=>{
        const text_content = e.querySelector(".term-text-content");
        text_content.classList.toggle("active");
    })
})