const button1 = document.getElementById("addchoicebtn");
button1.addEventListener("click", ()=>{
  adjustWeight();
})

function adjustWeight() {
    let weights = document.querySelectorAll(".weight");
    let currentWeight = document.getElementById("weight");
    let weightLeft = document.getElementById("weightLeft");
    let avaialbe = currentWeight.value - currentWeight; 

}
