const button1 = document.getElementById("addItem");
button1.addEventListener("click", ()=>{
    addItem();
})




function addItem() {

  let html =  "<tr>"
  html+=    "<td>"
  html+=       "<input required='required' class='form-control col-md-7 col-xs-12' style='margin-top:10px' type='text' name='productname[]' placeholder='Enter product name here...'>"
  html+=     "</td>"
  html+=     "</tr>"
  html+=     "<tr>"
  html+=    "<td>"
  html+=       "<input required='required' class='form-control col-lg-7 col-md-3 col-xs-12' type='text' name='productdes[]' placeholder='Enter product description here...'>"
  html+=     "</td>"                
  html+= "</tr>"
  html+=     "<tr>"
  html+=    "<td>"
  html+=       "<input required='required' class='form-control col-lg-7 col-md-3 col-xs-12' type='text' name='productimg[]>"
  html+=     "</td>"                
  html+= "</tr>"

    let row = document.getElementById("tbody").insertRow();
    row.innerHTML = html;
  
}

// function remove(){
//   var remove = document.getElementById('move');
//   let row = document.getElementById("tbody").deleteRow();
//    remove.style.display= "none";
//    row.innerHTML = html;
// }
