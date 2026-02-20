window.onload = ()=>{
    unseen();
    unseentime();
}
var unseenvar;
function unseentime(){
    unseenvar= setInterval(unseen,30000)
}



function unseen(){
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "../Main/Main_Header.php", true);
        xhr.onload = ()=>{
          if(xhr.readyState === XMLHttpRequest.DONE){
              if(xhr.status === 200){
                let data = xhr.response;
                document.getElementById("unrdmssgs").innerHTML = data;
              }
          }
        }
        xhr.send();
    }