    loadCompany();

// document.getElementsByTagName("body")[0].addEventListener("load", loadCompany);
function loadCompany(){//smmes view companies
    const xhr = new XMLHttpRequest();
    xhr.open("Post", "../Main/Main_Mysmme_Mybbbee.php", true);
    xhr.onload = function (){
        if(this.status == 200){
            
            let list = this.responseText;
            if(list==="error"){
                document.getElementById('myBBBEE').innerHTML = "error";
                console.log("error");
            }
            else {
                document.getElementById('myBBBEE').innerHTML = list;
                
                $("body").append("<script src='Javascript/appendmyBBBEE.js'></script>");
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
           document.getElementById('myBBBEE').innerHTML = list;
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
           document.getElementById('myBBBEE').innerHTML = list;
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
             document.getElementById('myBBBEE').innerHTML = list;
    }
    
    }
    var url_string = window.location.href
     var url = new URL(url_string);
     var page;
     if( typeof url.searchParams.get("page") !== undefined){
        page =  url.searchParams.get("page");
     }else{
        page = 1;
     }
    let start = (page -1) * 10;
 
    let b = document.getElementById('tk').value;
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("tk="+b+"&page="+start);
    }