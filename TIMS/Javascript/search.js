const form1 = document.querySelector(".search_area1");
Searchy = form1.querySelector(".simple_searchTerm");

const search_btn = document.getElementById("search_btn");
const search_btn2 = document.getElementById("search_btn2");




const form2 = document.querySelector(".search_area2"),
legal = form2.querySelector(".legaly"),
industry = form2.querySelector(".industryy"),
products = form2.querySelector(".producty"),
office = form2.querySelector(".officey"),
foo = form2.querySelector(".fooy");

const display_screen = document.getElementById("results");

if(document.getElementById("simple_searchTerm").value !== ""){
    load_search();
}
//search ajax

search_btn.addEventListener("click", ()=>{
    form1.onsubmit = (e)=>{
        e.preventDefault();
    }
    
    load_search();
        console.log(Searchy.value);
});
//search button 2 for the advanced search submission
search_btn2.addEventListener("click", ()=>{
    form2.onsubmit = (e)=>{
        e.preventDefault();
    }
    var entity = [];
    
    if(document.getElementById('entity1').checked == true){
        entity.push(document.getElementById('entity1').value);
    }
    if(document.getElementById('entity2').checked == true){
        entity.push(document.getElementById('entity2').value);
    }
    if(document.getElementById('entity3').checked == true){
        entity.push(document.getElementById('entity3').value);
    }
if(entity.length == 0){
    console.log("error");
}
    load_advanced_search(entity);

});
//function that sends the form data containing the search input and goes to the php file represented by location parameter and then displays the results
function load_search(){
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../Main/Main_simple_search.php", true);
    xhr.onload = function (){
        if(this.status == 200){
            let list = this.responseText;
             console.log("List is -> "+list);
                document.getElementById('display_results').innerHTML = list;
        }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("simple_searchTerm="+Searchy.value);
}

function load_advanced_search(entity){
    console.log("Particular disconnect");
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../Main/Main_advanced_search.php", true);
    xhr.onload = function (){
        if(this.status == 200){
            let list = this.responseText;
             console.log("List is -> "+list);
                document.getElementById('display_results').innerHTML = list;
        }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("legalname="+legal.value+"&industry="+industry.value+"&products="+products.value+"&foo="+foo.value+"&office="+office.value+"&entity="+JSON.stringify(entity));

}



document.getElementById("offices").onchange = ()=>{
    if(document.getElementById("offices").value !== ""){
      let id = document.getElementById("offices").value;
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "../Main/Main_offices.php", true);
      xhr.onload = ()=>{
        if(xhr.readyState === XMLHttpRequest.DONE){
            if(xhr.status === 200){
                let data = xhr.response;
                if (data === '')
                {
                  document.getElementById("industries").disabled = true;
                }
                else{
                  document.getElementById("industries").disabled = false;
                  document.getElementById("industries").innerHTML = data;
                  console.log(data);
                }
            }
        }
      }
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send("OFFICE_ID="+id+"&notrequired="+true);
  }else
  {
    document.getElementById("industries").innerHTML = "<option value='' selected> --blank-- </option>";
    document.getElementById("industries").disabled = true;
  }
  }