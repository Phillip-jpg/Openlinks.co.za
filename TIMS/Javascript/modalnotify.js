adminformcheck();

function adminformcheck(){

    let takenmsg="exists";
   let  sucessmsg="success";
    let failed ="failed";
     var url_string = window.location.href
     var url = new URL(url_string);
     var result = url.searchParams.get("info");

     if(result.localeCompare(takenmsg)==0) {
    
        $(function() { 
            $('#clicked').trigger('click');
            document.getElementById("textmodal").innerHTML = "It seems you have already made a connection with this entity !";
         });
     }
    
    else if(result.localeCompare(sucessmsg)==0) {
    
        $(function() { 
            $('#clicked').trigger('click');
            document.getElementById("textmodal").innerHTML = "Sucessfully Sent Email!";
         });
     }
     else if(result.localeCompare(failed)==0) {
    
        $(function() { 
            $('#clicked').trigger('click');
            document.getElementById("textmodal").innerHTML = "Oops email, seems to have failed. We will look into this!";
         });
     }
    
    }
    

    
    
        
           