const criteria = document.getElementById("criteria");
const weight = document.getElementById("weight");
const actual_weight = document.getElementById("weightTotal");
if(actual_weight.value == 100){
    weight.disabled = true;
    criteria.disabled = true;
}


