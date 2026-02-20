let links = document.querySelectorAll(".links");
//console.log(links.length);

let edit = [];
let link_ids = [];
let urls = [];
const submit_button = document.getElementById("COMPANYLINKSUPDATEBUTTON");
const form1 = document.getElementById("COMPANYLINKSUPDATEFORM");
const urlClasses = document.querySelectorAll(".urls");
let i = 0;
document.querySelectorAll('.links').forEach(item => {
  item.addEventListener('click', event => {
    //handle click
        edit[i] = links[i];
        urls[i] = urlClasses[i].value;
        edit[i].checked = true;
        link_ids[i] = links[i].value;
        i++;
  })
})
// for(let i = 0; i < links.length; i++){
//     links[i].addEventListener("click", ()=>{
//         edit[i] = links[i];
//         urls[i] = urlClasses[i].value;
//         edit[i].checked = true;
//         link_ids[i] = links[i].value;
//         //console.log(edit[i]);
//     });
    
    
// }
submit_button.addEventListener("click", ()=>{
  
    submit(submit_form, edit, link_ids,urls);
});
//console.log(edit);

    
function submit(func, edit, link_ids,urlList){
    //console.log("called");
    form1.onsubmit = (e)=>{
        e.preventDefault();
    }
    
    urls = validateChecked(edit, urls);
    
    func(edit, link_ids,urlList);
}

function submit_form(data, ids,urlList){
    
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../Main/Main.php", true);
        xhr.onload = ()=>{
            
          if(xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200){
              if(xhr.status === 200){
                  let data = xhr.response;
                  if(data == 1){
                    window.location.href="http://localhost/BBBEE_Project/Project One/COMPANY/edit.php?result=linksupdated";
                  }
              }
          }
        }
        // data = validateChecked(data, urlList);
        // ids = validateChecked(data, ids);
         console.log("changed");
        let token = document.getElementById("tk2").value;
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        
        xhr.send("identifier=COMPANYLINKSUPDATE&tk="+token+"&links="+data+"&ids="+data+"&linkIDS="+ids);
}

function validateChecked(data, urlList){
    let final =[];
    let count = 0;
    for(let i = 0; i < data.length; i++){
        
        if(data[i] === null){ 
           // urlList.splice(i);
        }else{
            final[count] = urlList[i];
            count++;
        }
        
    }
    return final;
}
