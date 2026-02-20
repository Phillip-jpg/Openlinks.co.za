
const button = document.getElementById("connection_generator");

button.addEventListener("click", ()=>{
    connection_init();
})

function connection_init(){
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "../Main/Main_connection.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
              let data = xhr.response;
              document.getElementById("link").value = data;
          }
      }
    }
    let b = document.getElementById('tk').value;
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("tk="+b);
}


const copy_button =  document.getElementById("copy_button");
copy_button.onclick = function() {
copy_text()
}
function copy_text(){
const span = document.getElementById("link");
  /* Select the text field */
  span.select();
  span.setSelectionRange(0, 99999); /* For mobile devices */

   /* Copy the text inside the text field */
  navigator.clipboard.writeText(span.value);


}

