    $('#office').on('input', function (){
    
    let id = document.getElementById("office").value;
    console.log(id)
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "../MARKET/ROUTE.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200){
          if(xhr.status === 200){
              let data = xhr.response;
              
              if (data === '')
              {
                document.getElementById("industries").disabled = true;
              } 
              else{
            
                document.getElementById("industries").innerHTML = data;
                console.log(data);
              }
          }
      }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
   
    xhr.send("OFFICE_ID="+id+"&ACTION=INDUSTRY_LIST");

  
});


