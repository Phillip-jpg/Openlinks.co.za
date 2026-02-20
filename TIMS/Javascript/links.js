const button1 = document.getElementById("addItem");
button1.addEventListener("click", ()=>{
    addItem();
})

function addItem() {

  let html =  "<tr>"
  html+=    "<td id='move' >"
  html+=       "<input class='form-control col-md-7 col-xs-12 formz' style='margin-top:10px' type='text' name='links[]' placeholder='Enter you business links here...'>"
  html+=           "</td>"
  html+=     "</tr>"
  html+=            "<tr>"
  html+=               "<td id='move'>"
  html+=                  "<select class='form-control col-lg-1 col-md-7 col-xs-12 formz' style='margin-top:10px' name='ids[]'>"
  html+=                      "<option value='1'>WhatsApp</option>"
  html+=                      "<option value='2'>Facebook</option>"
  html+=                      "<option value='3'>Website</option>"
  html+=                      "<option value='4'>LinkedIn</option>"
  html+=                      "<option value='5'>Twitter</option>"
  html+=                      "</select>"
  html+=                  "</td>"
                        
  html+= "</tr>"

    let row = document.getElementById("tbody").insertRow();
    row.innerHTML = html;
}
