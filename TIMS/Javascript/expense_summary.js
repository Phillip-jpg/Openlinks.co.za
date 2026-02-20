const button1 = document.getElementById("addItem");
button1.addEventListener("click", ()=>{
    addItem();
})
const button2 = document.getElementById("addItem1");
button2.addEventListener("click", ()=>{
    addItem1();
})
function addItem() {
 
    let html = "<tr>";
   html +=  "<td class='added_expense'><input id='move' required=required'  class='form-control col-md-7 col-xs-12' type='text' name='serviceprovider[]' placeholder='Service provider...' ></td>"
   html += "<td class='added_expense'><input id='move' required=required'   class='form-control col-md-7 col-xs-12' type='text' name='productname[]' placeholder='Product name...'></td>"
   html += "<td class='added_expense'><input id='move' required=required'  class='form-control col-md-7 col-xs-12' type='text' name='productspecification[]' placeholder='Enter product specification...'></td>"
   html += "<td class='added_expense'><input id='move' required=required'  class='form-control col-md-7 col-xs-12' type='text' name='randvalue[]' placeholder='Rand Value'></td>"
   html += "<td class='added_expense'><input id='move' required=required'  class='form-control col-md-7 col-xs-12' type='text' name='frequency[]' placeholder='Frequency of expense...'></td>"
   html += "</tr>"

    let row = document.getElementById("tbody").insertRow();
    row.innerHTML = html;
}
function addItem1() {

    let html = "<tr>";
    html +=  "<td class='added_expense'><input id='move'  required=required'  class='form-control col-md-7 col-xs-12' type='text' name='serviceprovider[]' placeholder='Service provider...' ></td>"
    html += "<td class='added_expense'><input id='move' required=required'   class='form-control col-md-7 col-xs-12' type='text' name='productname[]' placeholder='Product name...'></td>"
    html += "<td class='added_expense'><input id='move' required=required'  class='form-control col-md-7 col-xs-12' type='text' name='productspecification[]' placeholder='Enter product specification...'></td>"
    html += "<td class='added_expense'><input id='move' required=required'  class='form-control col-md-7 col-xs-12' type='text' name='randvalue[]' placeholder='Rand Value...'></td>"
    html += "<td class='added_expense'><input id='move' required=required'  class='form-control col-md-7 col-xs-12' type='text' name='frequency[]' placeholder='Frequency of expense...'></td>"
    html += "</tr>"

let row = document.getElementById("tbody1").insertRow();
row.innerHTML = html;
}