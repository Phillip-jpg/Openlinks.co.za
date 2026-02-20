console.log('gaf raf');
const search_btn = document.getElementById("search_btn_2");
const token = document.getElementById("tk");
const form1 = document.getElementById("search_area1");
const Searchy = document.getElementById("unauthSearchTerm");

if(Searchy.value !== ""){
    load_search();
}
//search ajax

search_btn.addEventListener("click", ()=>{
    form1.onsubmit = (e)=>{
        console.log('gaf gwans');
        e.preventDefault();
    }
    
    load_search();
        console.log(Searchy.value);
});

//function that sends the form data containing the search input and goes to the php file represented by location parameter and then displays the results
function load_search(){
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "Main/Mainunauth.php", true);
    xhr.onload = function (){
        if(this.status == 200){
            let list = this.responseText;
             console.log("List is -> "+list);
                document.getElementById('searchresultsanchor').innerHTML = list;
        }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("unauthsearchTerm="+Searchy.value + "&tk="+ token.value);
}
