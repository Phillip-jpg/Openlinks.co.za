loginmodal();


function loginmodal(){

        takenmsg="InvalidUserNameOrPassword";
     var url_string = window.location.href
     var url = new URL(url_string);
     var result = url.searchParams.get("error");
    
    let account = "emailsent";
    let exists = "usernametaken";
    let passwordR = "emailpsent";
    let Repeat = "passwordcheck";
    let passwordS = "passwordsuccess";
     if(result.localeCompare(takenmsg)===0)
     {
        $(function() { 
            $('#clicked').trigger('click');
            document.getElementById("textmodal").innerHTML = "Username or Password incorrect try again!";
            //Username or Password incorrect try again
         });
     }else if(result.localeCompare(account)===0) {
    
        $(function() { 
            $('#clicked').trigger('click');
            document.getElementById("textmodal").innerHTML = "Email has been sent, please verify your account and then login!";
         });
     }
     else if(result.localeCompare(exists)===0) {
    
        $(function() { 
            $('#clicked').trigger('click');
            document.getElementById("textmodal").innerHTML = "Username already exists, please try a different one!";
         });
     }else if(result.localeCompare(Repeat)===0) {
    
        $(function() { 
            $('#clicked').trigger('click');
            document.getElementById("textmodal").innerHTML = "The passwords do not match please try again!";
         });
     }
      else if(result.localeCompare(passwordR)===0) {
    
        $(function() { 
            $('#clicked').trigger('click');
            document.getElementById("textmodal").innerHTML = "An email with a password recovery link has been sent !";
         });
     }
     else if(result.localeCompare(passwordS)===0) {
    
        $(function() { 
            $('#clicked').trigger('click');
            document.getElementById("textmodal").innerHTML = "You have successfully reset your password !";
         });
     }
     
    }
    
    
           