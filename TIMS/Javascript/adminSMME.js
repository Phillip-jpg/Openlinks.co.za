
loadSMME();

function loadSMME(){//companies view smmes
    const xhr = new XMLHttpRequest();
    xhr.open("Post", "../Main/Main_Mysmme_Mybbbee.php", true);
    xhr.onload = function (){
        if(this.status == 200){
            let list = this.responseText;
             //console.log(list);
            if(list==="error"){
                document.getElementById('mySMME').innerHTML = "error";
                console.log("error");
            }
            else {
                document.getElementById('mySMME').innerHTML = list;
                //$("body").append("<script src='Javascript/append.js'></script>");

                // console.log(list);
        }
             
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
    
