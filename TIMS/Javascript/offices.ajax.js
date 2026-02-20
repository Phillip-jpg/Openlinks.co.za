document.getElementById("offices").onchange = ()=>{
  if(document.getElementById("offices").value !== ""){
    let id = document.getElementById("offices").value;
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "../Main/Main_offices.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200){
          if(xhr.status === 200){
              let data = xhr.response;
              if (data === '')
              {
                document.getElementById("industries").disabled = true;
              } 
              else{
                document.getElementById("industries").disabled = false;
                document.getElementById("industries").innerHTML = data;
                console.log(data);
              }
          }
      }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("OFFICE_ID="+id);
}else
{
  document.getElementById("industries").disabled = true;
}
}
