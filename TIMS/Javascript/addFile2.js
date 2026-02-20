const button2 = document.getElementById("addFile2");
button2.addEventListener("click", ()=>{
    addItem();
})




function addItem() {           
  
  const label = document.createElement("label");
  label.classList.add("control-label", "col-md-3", "col-sm-3", "col-xs-12");
  label.textContent = "Upload file";

  // Create the required span element
  const requiredSpan = document.createElement("span");
  requiredSpan.classList.add("required");
  label.appendChild(requiredSpan);

  // Create the div element with the custom-file class
  const customFileDiv = document.createElement("div");
  customFileDiv.classList.add("col-md-6", "col-sm-6", "col-xs-12", "custom-file");

  // Create the input element
  const inputFile = document.createElement("input");
  inputFile.setAttribute("type", "file");
  inputFile.setAttribute("name", "file[]");
  inputFile.classList.add("form-control", "col-md-7", "col-xs-12", "custom-file-input", "formz");

  // Set inline styles for the input element
  inputFile.style.borderStyle = "none";
  inputFile.style.width = "35vw";

  // Append the input element to the custom-file div
  customFileDiv.appendChild(inputFile);

  // Append the label and custom-file div to the main form group div
  const parent = document.createElement("div");
  parent.classList.add("form-group");
  const formGroupDiv2 = document.getElementById("inputs");
  parent.appendChild(label);
  parent.appendChild(customFileDiv);
  formGroupDiv2.appendChild(parent);
  
}

// function remove(){
//   var remove = document.getElementById('move');
//   let row = document.getElementById("tbody").deleteRow();
//    remove.style.display= "none";
//    row.innerHTML = html;
// }
