const warning = "The weights need to add up to 100% and not over. Please re-adjust the weights.";
const form1 = document.getElementById("CRITERIA_FORM");
const submit_btn = document.getElementById("CRITERIA_CREATE");
submit_btn.addEventListener("click", ()=>{
    form1.onsubmit = (e)=>{
        e.preventDefault();
    }
    calculateRemainingWeight();
});

function calculateRemainingWeight() {
    var choiceWeights = document.getElementsByClassName("weight");
    var weight =0;
    for (var i = 0; i < choiceWeights.length; i++) {
        console.log(choiceWeights[i].value);
        weight+=choiceWeights[i].value;
    }
    if(weight > 100){
        alert(warning);
    }else{
        
        form1.submit();
    }
 
}


