$(document).ready(function() {
    $('#dataTable').DataTable();
    $(".table-cell").click(function() {
        window.document.location = $(this).data("href");
    });
  });



// document.getElementById("Refresh1").addEventListener("click", Refresh("Main/Main_Async_C_ALL.php", "ALL"));
// document.getElementById("Refresh2").addEventListener("click", Refresh("Main/Main_Async_C_S_R.php", "smmerequest"));
// document.getElementById("Refresh3").addEventListener("click", Refresh("Main/Main_Async_C_C_R.php", "companyrequest"));



  function Refresh(File, Tab){
    const xhr = new XMLHttpRequest();
    xhr.open("Get", File, true); // mybbbee for smmes, smmes view companies
    xhr.onload = function (){
        if(this.status == 200){
            let list = this.responseText;
            if(list==="error"){
                document.getElementById(Tab).innerHTML = "error";
                console.log("error");
            }
            else {
                if(list==1)
                {
                console.log("No Change");
                }else{
                    document.getElementById(Tab).innerHTML = list;
                    $('#dataTable').DataTable();
                    // console.log(list);
                }
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
           document.getElementById(Tab).innerHTML = list;
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
           document.getElementById(Tab).innerHTML = list;
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
             document.getElementById(Tab).innerHTML = list;
    }
    
    }
    xhr.send();
}