window.onload = ()=>{connect();}

// console.log("New Happy feet");
// document.getElementsByTagName("body")[0].addEventListener("load", () =>{loadNotifications();});


function connect(){
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "../Main/Main_connection.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
              let data = xhr.response;
              document.getElementById("links").innerHTML = data;
          }
      }
    }
    let b = document.getElementById('tk').value;
    let variable= document.getElementById("idsl").value;
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("tk="+b+"&url="+variable);
}
       