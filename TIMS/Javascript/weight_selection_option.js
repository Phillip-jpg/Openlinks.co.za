var warning = "The weights need to add up to 100% and not over. Please re-adjust the weights.";
const form1 = document.getElementById("option_form");
const submit_btn = document.getElementById("OPTION_CREATE");
submit_btn.addEventListener("click", ()=>{
    form1.onsubmit = (e)=>{
        e.preventDefault();
    }
    validateweight();
});

function validateweight() {
    var choiceWeights = document.getElementsByClassName("weight");
    var weight = 0;
    for (var i = 0; i < choiceWeights.length; i++) {
        console.log(choiceWeights[i].value);
        weight += parseInt(choiceWeights[i].value);
    }
    if(weight > 100){
        warning = warning + " Your weight is "+ weight;
        alert(warning);
    }
}
function submitform(){
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../Main/Main_simple_search.php", true);
    xhr.onload = function (){
        if(this.status == 200){
            let list = this.responseText;
             console.log("List is -> "+list);
                document.getElementById('display_results').innerHTML = list;
        }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("simple_searchTerm="+Searchy.value);
}

