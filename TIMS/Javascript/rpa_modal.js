post();


    
    
    function post(){
      
         var url_string = window.location.href
         var url = new URL(url_string);
         var result = url.searchParams.get("result");
        
        if(result == "success"){
            $(function() { 
                $('#clicked').trigger('click');
                document.getElementById("textmodal").innerHTML = "Post has been successfully submitted!";
             });
        }
         if(result==1) {
        
            $(function() { 
                $('#clicked').trigger('click');
                document.getElementById("textmodal").innerHTML = "Your post has been created successfully and been allocated to an admin!";
             });
         } 
         if(result==2) {
        
            $(function() { 
                $('#clicked').trigger('click');
                document.getElementById("textmodal").innerHTML = "Your post has been created successfully and been allocated to the relevant admins !";
             });
         } 
         if(result==3) {
        
            $(function() { 
                $('#clicked').trigger('click');
                document.getElementById("textmodal").innerHTML = "Your post has been created successfully and been sent to the hub !";
             });
         } 
          
        }
    
    
    
           