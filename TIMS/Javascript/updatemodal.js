keywordUpdate();
adminUpdate();
registerUpdated();
linksUpdate();
function adminUpdate(){

    takenmsg="adminupdated";
     var url_string = window.location.href
     var url = new URL(url_string);
     var result = url.searchParams.get("result");
    
    
     if(result.localeCompare(takenmsg)==0) {
    
        $(function() { 
            $('#clicked').trigger('click');
            document.getElementById("textmodal").innerHTML = "Admin successfully updated!";
         });
     }
    
}
function keywordUpdate(){

    takenmsg="keywordupdated";
     var url_string = window.location.href
     var url = new URL(url_string);
     var result = url.searchParams.get("result");
    
    
     if(result.localeCompare(takenmsg)==0) {
    
        $(function() { 
            $('#clicked').trigger('click');
            document.getElementById("textmodal").innerHTML = "Keywords successfully updated!";
         });
     }
    
}
function registerUpdated(){

    takenmsg="compinfo";
     var url_string = window.location.href
     var url = new URL(url_string);
     var result = url.searchParams.get("result");
    
    
     if(result.localeCompare(takenmsg)==0) {
    
        $(function() { 
            $('#clicked').trigger('click');
            document.getElementById("textmodal").innerHTML = "Company information successfully updated!";
         });
     }
    
}


function linksUpdate(){

    takenmsg="linksupdated";
     var url_string = window.location.href
     var url = new URL(url_string);
     var result = url.searchParams.get("result");
    
    
     if(result.localeCompare(takenmsg)==0) {
    
        $(function() { 
            $('#clicked').trigger('click');
            document.getElementById("textmodal").innerHTML = "Business Links successfully updated!";
         });
     }
    
}
    