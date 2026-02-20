post();


    
    
    function post(){
      
         var url_string = window.location.href
         var url = new URL(url_string);
         var result = url.searchParams.get("result");
        
        if(result == 4){
            $(function() { 
                $('#clicked').trigger('click');
                document.getElementById("textmodal").innerHTML = "Post has been successfully submitted!";
             });
        }
          
          
        }
    
    
    
           