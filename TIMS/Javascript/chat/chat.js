const form = document.querySelector(".typing-area"),
To = form.querySelector(".To_").value,
inputField = form.querySelector(".input-field"),
sendBtn = form.querySelector("button"),
chatBox = document.querySelector(".chat-box");
success = document.querySelector(".successy");;




loadchat();
    form.onsubmit = (e)=>{
        e.preventDefault();
    }
    
    inputField.focus();
    inputField.onkeyup = ()=>{
        if(inputField.value != ""){
            sendBtn.classList.add("active");
        }else{
            sendBtn.classList.remove("active");
            sendBtn.preventDefault();
        }
    }

    
    sendBtn.onclick = ()=>{
        if(inputField.value == ""){
            return;
        }
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "../Main/Main_Insertchat.php", true);
        xhr.onload = ()=>{
          if(xhr.readyState === XMLHttpRequest.DONE){
              if(xhr.status === 200){
                let data = xhr.response;
                  loadchat();
                  inputField.value = data;
                  scrollToBottom();
              }
          }
        }
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("To="+To+"&message="+inputField.value);
    }
    chatBox.onmouseenter = ()=>{
        chatBox.classList.add("active");
    }
    
    chatBox.onmouseleave = ()=>{
        chatBox.classList.remove("active");
    }

    var LoadChat;
    function callLoadChat(){
        LoadChat= setInterval(loadchat,5000)
    }

    callLoadChat();//1

    function loadchat(){
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "../Main/Main_Getchat.php", true);
            xhr.onload = ()=>{
              if(xhr.readyState === XMLHttpRequest.DONE){
                  if(xhr.status === 200){
                    let data = xhr.response;
                    if(chatBox.innerHTML !== data){
                        chatBox.innerHTML = data;
                    }
                    if(!chatBox.classList.contains("active")){
                        scrollToBottom();
                      }
                  }
              }
            }
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("To="+To);
        }
        
        function scrollToBottom(){
            chatBox.scrollTop = chatBox.scrollHeight;
          }
    
    
    

