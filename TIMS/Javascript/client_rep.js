 $('#client').on('input', function (){
    
    let id = document.getElementById("client").value;
    console.log(id)
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "../MARKET/ROUTE.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200){
          if(xhr.status === 200){
              let data = xhr.response;
              
              if (data === '')
              {
                document.getElementById("client_rep").disabled = true;
              } 
              else{
                document.getElementById("client_rep").disabled = false;
                document.getElementById("client_rep").innerHTML = data;
                console.log(data);
              }
          }
      }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
   
    xhr.send("CLIENT_ID="+id+"&ACTION=CLIENT_REP_LIST");

  
});


