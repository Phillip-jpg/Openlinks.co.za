window.onload = ()=>{controllable();}

// console.log("New Happy feet");
// document.getElementsByTagName("body")[0].addEventListener("load", () =>{loadNotifications();});


function controllable(){
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "../Main/Main_connection.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
              let data = xhr.response;
              document.getElementById("companies_list").innerHTML = data;
              activate();
          }
      }
    }
    let b = document.getElementById('gctk').value;
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("tk="+b);
}

function activate(){

if(document.getElementById("link_control") != null){

  console.log(' not floppy');

 document.getElementById("link_control").addEventListener('click', ()=>{

  control();

});

    function control(){

    console.log("data2");
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "../Main/Main_connection.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
              let data = xhr.response;
              if(data == 1){
                window.location.replace('../P_COMPANY/index.php');
              }else{
                console.log(data);
              }
          }
      }
    }
    let b = document.getElementById('link_control').getAttribute("data-link-control");
    let c = document.getElementById('link_control').getAttribute("data-credz");
    let d = document.getElementById('cctk').value;
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("P_COMPANY_LINK="+ b + "&id=" + c + "&tk=" + d);
};
console.log("data1");
// $("#link_control").click(function(){
//     console.log("data2");
//     let xhr = new XMLHttpRequest();
//     xhr.open("POST", "../Main/Main_connection.php", true);
//     xhr.onload = ()=>{
//       if(xhr.readyState === XMLHttpRequest.DONE){
//           if(xhr.status === 200){
//               let data = xhr.response;
//               console.log(data);
//               if(data === "TRUE"){
//                 window.location.replace('../P_COMPANY/index.php');
//               }
//           }
//       }
//     }
//     let b = document.getElementById('link_control').getAttribute("data-link-control");
//     let c = document.getElementById('link_control').getAttribute("data-credz");
//     let d = document.getElementById('cctk').value;
//     xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//     xhr.send("P_COMPANY_LINK="+ b + "&id=" + c + "&tk=" + d);
//   });

 }else{
   console.log('floppy');
 }
}