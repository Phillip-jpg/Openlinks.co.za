let links = document.querySelectorAll(".WebsiteLinks");
//console.log(links.length);


for(let i = 0; i < links.length; i++){
    links[i].addEventListener("click", ()=>{
        submit_form(links[i].value);
    });
    
    
}

//console.log(edit);


function submit_form(data){
    
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../Main/Main.php", true);
        xhr.onload = ()=>{
            
          if(xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200){
              if(xhr.status === 200){
                  let data = xhr.response;
                  if(data == -1){
                    window.location.href="http://localhost/BBBEE_Project/Project One/COMPANY/edit.php?result=linksupdated";
                  }else{
                     data = JSON.parse(data);
                    
                    console.log(data.url);
                    window.location.replace(data.url);
                  }
              }
          }
        }
        let url_string = window.location.href
        let url = new URL(url_string);
        let id = url.searchParams.get("id");
        let token = document.getElementById("tk2").value;
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        
        xhr.send("identifier=LINKVISITS&tk2="+token+"&link="+data+"&id="+id);
}