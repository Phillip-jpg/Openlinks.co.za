

function update(id) {
    const xhr = new XMLHttpRequest();
    xhr.open("Post", "../Main/Main.php", true);
    xhr.onload = function () {
      if (this.status === 200) {
        location.reload();
        // console.log(this.response);
      }
    };
  
    let b = document.getElementById("tk2").value;
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("tk=" + b + "&id=" + id);
  }

  
  const buttons = document.getElementsByClassName("update_notify");
  for (let i = 0; i < buttons.length; i++) {
    buttons[i].addEventListener("click", function (e) {
    
      let id = e.target.getAttribute("class");
      var number = id.replace(/^\D+/g, '');
      
      update(number);
      console.log(number);
    });
  }
  
