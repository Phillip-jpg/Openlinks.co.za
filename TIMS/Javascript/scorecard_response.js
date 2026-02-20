//catch the form submission from the questionnaire form
//prevent default submission and send via ajax
//after successful, get back the display for the file upload


search_btn.addEventListener("click", ()=>{
    form1.onsubmit = (e)=>{
        e.preventDefault();
    }
    
    load_search();
        console.log(Searchy.value);
});