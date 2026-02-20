window.onload = ()=>{loadNotifications();}

setInterval(() =>{
    loadNotifications();
},20000);
// console.log("New Happy feet");
// document.getElementsByTagName("body")[0].addEventListener("load", () =>{loadNotifications();});


function loadNotifications(){//smmes view companies
    console.log("Happy feet");
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../Main/Main_Chams.php", true);
    xhr.onload = function (){
        if(this.status == 200){
            let list = this.responseText;
            if(list==="error"){
                document.getElementById('notifications').innerHTML = "error";
                
            }
            else {
                if(document.getElementById('notifications').innerHTML !== list){
                    document.getElementById('notifications').innerHTML = list;
                }
                $("body").append("<script src='../Javascript/updateNotifications.js'></script>");
        }
        }
        else if(this.status == 403){
            let list = "<Div class='row'>"
            +"<Div class='col-lg-3 col-md-6'>"
             +"<Div class='card'>"
              +"<Div class='card-body'>"
               +"<h5 class='card-title'>"
                +"Error Forbidden"
               +"</h5>"
              +"</Div>"
              +"</Div>"
             +"</Div>"
           +"</Div>";
           document.getElementById('notifications').innerHTML = list;
        }
        else if(this.status == 404){
            let list = "<Div class='row'>"
            +"<Div class='col-lg-3 col-md-6'>"
             +"<Div class='card'>"
              +"<Div class='card-body'>"
               +"<h5 class='card-title'>"
                +"Error, Not Found"
               +"</h5>"
              +"</Div>"
              +"</Div>"
             +"</Div>"
           +"</Div>";
           document.getElementById('notifications').innerHTML = list;
        }
    
        
    
    xhr.onerror = function(){
        let list = "<Div class='row'>"
                     +"<Div class='col-lg-3 col-md-6'>"
                      +"<Div class='card'>"
                       +"<Div class='card-body'>"
                        +"<h5 class='card-title'>"
                         +"Server Error"
                        +"</h5>"
                       +"</Div>"
                       +"</Div>"
                      +"</Div>"
                    +"</Div>";
             document.getElementById('notifications').innerHTML = list;
    }
    
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("tk="+document.getElementById('tk').value);
    }