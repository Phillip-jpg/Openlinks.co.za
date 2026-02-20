deleteData();
DataDeleted();


function deleteData(){
    var url_string = window.location.href
    var url = new URL(url_string);
    var result = url.searchParams.get("action");
    if(result >= 1) {
        console.log("worked");
    $(function() { 
        $('#clicked').trigger('click');
        document.getElementById("textmodal").innerHTML = "Are you sure you want to delete? All data will be lost!";
        });
    }
     
}

function DataDeleted(){
    takenmsg="deleted";
    
    var url_string = window.location.href
    var url = new URL(url_string);
    var result = url.searchParams.get("result");
    
    
    if(result.localeCompare(takenmsg)==0) {
    
        $(function() { 
            $('#clicked').trigger('click');
            document.getElementById("textmodal").innerHTML = "Information has been deleted succefully!";
        });
    }
 
     
}