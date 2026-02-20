const searchBar = document.querySelector(".search input"),
searchIcon = document.querySelector(".search button"),
usersList = document.querySelector(".users-list");

loadusers();

searchIcon.onclick = ()=>{
  searchBar.classList.toggle("show");
  searchIcon.classList.toggle("active");
  searchBar.focus();
  if(searchBar.classList.contains("active")){
    searchBar.value = "";
    searchBar.classList.remove("active");
  }
}

searchBar.onkeyup = ()=>{
  let searchTerm = searchBar.value;
  if(searchTerm != ""){
    searchBar.classList.add("active");
  }else{
    searchBar.classList.remove("active");
    return;
  }
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "../Main/Main_MessagesSearch.php", true);
  xhr.onload = ()=>{
    if(xhr.readyState === XMLHttpRequest.DONE){
        if(xhr.status === 200){
          let data = xhr.response;
          usersList.innerHTML = data;
          console.log(data);
        }
    }
  }
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send("search=" + searchTerm);
}


var Loaduser;
    function callLoadusers(){
        Loaduser= setInterval(loadusers,5000)
    }
    callLoadusers();

function loadusers(){
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "../Main/Main_MessagesGetUsers.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
            let data = xhr.response;
            if(!searchBar.classList.contains("active")){
              if(usersList.innerHTML !== data){
                usersList.innerHTML = data;
            }
            }
          }
      }
    }
    xhr.send();
  }

