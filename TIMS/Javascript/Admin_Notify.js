const tk = document.getElementById("tk2").value;
let name_ = "ADMIN_five_Day_wait";
var url_string = window.location.href
var url = new URL(url_string);
var result = url.searchParams.get("id");
make_request(tk, name_, result);
function  make_request(identifier, name, id){

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../Main/Main_Notify.php", true);

     xhr.onload = function (){
        if(this.status == 200){
           let xhr_results = this.response;
               console.log(xhr_results); 
        }else{
            console.log("Onload is working but the if statement is not");
        }
         
    }
    xhr.onerror = ()=>{
        console.log("XHR is flopping somewhere");
    }   

        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("ADMIN_five_Day_wait="+name+"&tk="+identifier+"&id="+id);
}