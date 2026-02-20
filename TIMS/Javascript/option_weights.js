

function calculateRemainingWeight() {
    var choiceWeights = document.getElementsByClassName("weight");
    let remainingWeight = 100;
    for (var i = 0; i < choiceWeights.length; i++) {
        console.log(choiceWeights[i].value);
        remainingWeight -= choiceWeights[i].value;
    }
    console.log(remainingWeight);
    return remainingWeight;
  }

//   function updateWeightOptions() {
//     var remainingWeight = calculateRemainingWeight();
//     var weightInputs = document.getElementsByClassName("weight");
//     for (var i = 0; i < weightInputs.length; i++) {
//     weightInputs[i].max = remainingWeight;
//     }
//   }

 let btn = document.getElementById("addchoicebtn");
 btn.addEventListener("click", ()=>{
    addChoice();
 })

  function addChoice() {
    var choicesContainer = document.getElementById("choicesContainer");
    var remainingWeight = calculateRemainingWeight();

    if (remainingWeight <= 0) {
      alert("The total weight has reached 100%. No more choices can be added.");
      return;
    }

    var newChoice = document.createElement("tr");
    var option = document.createElement("td");
    var weight = document.createElement("td");
    newChoice.classList.add("choice");

    // var choiceLabel = document.createElement("label");
    // choiceLabel.textContent = "Choice " + (choiceCount + 1) + ":";
    // newChoice.appendChild(choiceLabel);

    var choiceText = document.createElement("input");
    choiceText.type = "text";
    choiceText.name = "choiceText[]";
    choiceText.required = true;
    choiceText.classList.add("form-control");
    choiceText.classList.add("col-md-7");
    choiceText.classList.add("col-xs-12");
    choiceText.placeholder = "Enter your option here ";
    option.appendChild(choiceText);

    var choiceWeight = document.createElement("input");
    choiceWeight.type = "number";
    choiceWeight.name = "choiceWeight[]";
    choiceWeight.min = "0";
    choiceWeight.max = remainingWeight;
    choiceWeight.required = true;
    choiceWeight.classList.add("weight");
    choiceWeight.classList.add("form-control");
    choiceWeight.classList.add("col-md-7");
    choiceWeight.classList.add("col-xs-12");
    choiceWeight.placeholder = "20%";
    weight.appendChild(choiceWeight);

    newChoice.appendChild(option);
    newChoice.appendChild(weight);
    choicesContainer.appendChild(newChoice);

    // Update the weight options for the existing and new choices
    //updateWeightOptions();
  }

  // Update the weight options initially
 // updateWeightOptions();