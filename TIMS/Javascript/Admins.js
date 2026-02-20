
 Admins();
function Admins(){
    const xhr = new XMLHttpRequest();
    xhr.open("Post", "../Main/Main.php", true);
    xhr.onload = function (){
        if(this.status == 200){
            
            let list = this.responseText;
            if(list==="error"){
                document.getElementById('admins').innerHTML = "error";
                console.log("error");
            }
            else {
                document.getElementById('admins').innerHTML = list;
               
                // $("body").append("<script src='../Javascript/AdminsFilter.js'></script>");
                
        }
             
        }
    }
   
    let b = document.getElementById('tk').value;
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("identifier=ADMINS&tk="+b);
    }

