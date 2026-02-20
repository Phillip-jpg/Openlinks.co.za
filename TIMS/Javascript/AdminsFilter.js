

const parent = document.getElementById("admin-buttons");
parent.addEventListener("click", function(e) {
    if(e.target.classList.contains("filter")){
        let type = e.target.id;
        let data = e.target.previousElementSibling.value;
        switch(type) {
            case "office":
                data = document.getElementById('office_input').value;
                break;
            case "industry":
                data = document.getElementById('indus_input').value;
                break;
            case "role":
                data = document.getElementById('role_input').value;
            break;
            case "city":
                data = document.getElementById('city_input').value;
                break;
            case "province":
                data = document.getElementById('province_input').value;
                break;
          }
        submitFilter(type, data);
    }
});

function submitFilter(type, data){
    const xhr = new XMLHttpRequest();
    xhr.open("Post", "../Main/Main.php", true);
    xhr.onload = function (){
        if(this.status == 200){
            let list = this.responseText;
            if(list==="error"){
                document.getElementById('admins').innerHTML = "error";
                console.log("error");
            }
            else {
                document.getElementById('admins').innerHTML = list;
                console.log(list);
            }
        }
    }
    let b = document.getElementById('tk').value;
    console.log(data);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("identifier=ADMINSDISPLAY&tk="+b+"&type="+type+"&data="+data);
}
