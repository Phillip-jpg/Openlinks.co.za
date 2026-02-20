const button1 = document.getElementById("addItem");
button1.addEventListener("click", ()=>{
    addItem();
})

function addItem() {

  let html =  "<tr>"
  html+=    "<td id='move' >"
  html+=       "<input class='form-control col-md-7 col-xs-12 formz' style='margin-top:10px' type='text' name='questions[]' placeholder='Enter your question here...'>"
  html+=           "</td>"
  html+=     "</tr>"
  html+=            "<tr>"
  html+=               "<td id='move'>"
  html+=                  '<input class="form-control col-md-7 col-xs-12" type="number" name="weights[]"  required="">'
  html+=                  "</td>"
                        
  html+= "</tr>"

    let row = document.getElementById("tbody").insertRow();
    row.innerHTML = html;
}
