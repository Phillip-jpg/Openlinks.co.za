adminformcheck();
companydataformcheck();
productUpload();
resultDeleted();
function adminformcheck(){

    takenmsg="usernametaken";
    sucessmsg="success";
     var url_string = window.location.href
     var url = new URL(url_string);
     var result = url.searchParams.get("result");

     if(result.localeCompare(takenmsg)==0) {
    
        $(function() { 
            $('#clicked').trigger('click');
            document.getElementById("textmodal").innerHTML = "It seems you have already filled in this information !";
         });
     }
    
    else if(result.localeCompare(sucessmsg)==0) {
    
        $(function() { 
            $('#clicked').trigger('click');
            document.getElementById("textmodal").innerHTML = "Sucessfully Submitted!";
         });
     }
    
    }
    
    
    
    function companydataformcheck(){
    
        takenmsg="exists";
        sucessmsg="success";
         var url_string = window.location.href
         var url = new URL(url_string);
         var result = url.searchParams.get("result");
        
        
         if(result.localeCompare(takenmsg)==0) {
        
            $(function() { 
                $('#clicked').trigger('click');
                document.getElementById("textmodal").innerHTML = "It seems you have already filled in this information !";
             });
         }
    
        }
    
    
        function companydataformcheck(){
    
            takenmsg="exists";
            sucessmsg="success";
             var url_string = window.location.href
             var url = new URL(url_string);
             var result = url.searchParams.get("result");
            
            
             if(result.localeCompare(takenmsg)==0) {
            
                $(function() { 
                    $('#clicked').trigger('click');
                    document.getElementById("textmodal").innerHTML = "It seems you have already filled in this information !";
                 });
             }
             
            }
            function productUpload(){
    
                const takenmsg= ["too large", "file error", "not right file"];
                sucessmsg="success";
                 var url_string = window.location.href
                 var url = new URL(url_string);
                 var result = url.searchParams.get("result");
                let text = "This is not the correct file type, please submit a correct file type. JPG, JPEG, PNG";
                let text2 = "This file is too large, please ensure the file is 2MB or less.";
                for(i = 0; i < takenmsg.length; i++){
                    if(result.localeCompare(takenmsg[i])==0) {
                
                        $(function() { 
                            $('#clicked').trigger('click');
                            if(result.localeCompare("too large")==0){
                                document.getElementById("textmodal").innerHTML =text2;
                            }else if(result.localeCompare("not right file")==0){
                                document.getElementById("textmodal").innerHTML = text;
                            }else{
                                document.getElementById("textmodal").innerHTML = "It seems there was an error uploading your file, please try again later.";
                            }
                            
                         });
                     }
                }
                 
                 
                }

                
    function resultDeleted(){
    
        takenmsg="deleted";
         var url_string = window.location.href
         var url = new URL(url_string);
         var result = url.searchParams.get("result");
        
        
         if(result.localeCompare(takenmsg)==0) {
        
            $(function() { 
                $('#clicked').trigger('click');
                document.getElementById("textmodal").innerHTML = "Successfully removed !";
             });
         }
    
        }
    
           