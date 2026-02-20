submit_form();

function submit_form(){
    
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../Main/Main.php", true);
        xhr.onload = ()=>{
            
          if(xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200){
              if(xhr.status === 200){
                  let data = xhr.response;
                  if(data == 1){
                   
                  }else if(data == -2){
                    console.log(data);
                    //window.location.href="http://localhost/BBBEE_Project/Project One/COMPANY/login.php?failed";
                  }else{
                    console.log(data);
                  }
              }
          }
        }
        let url_string = window.location.href
        let url = new URL(url_string);
        let link = url.searchParams.get("url");
        let email = url.searchParams.get("u");
        let token = document.getElementById("tk").value;
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        
        xhr.send("identifier=EMAIL_VERIFICATION_ADMIN&tk="+token+"&email="+email+"&link="+link);
}