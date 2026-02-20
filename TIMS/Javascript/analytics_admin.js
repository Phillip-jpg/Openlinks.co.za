

window.onload = ()=>{
   ANALYTICS_SUMMARY_HEADER();
 SEARCH_SUMMARY();
   PROGRESS_PROCESS();
    
    PROCESS_AVERAGE_TIME();

//     // PAGE_VISITS_GRAPGH();

  // PAGE_VISITS();

    SEARCH_GRAPGH();

    
//     console.log("Im working 7");
//     CURRENT_DAY_SEARCHES();
//     console.log("Im working 8");
//    ALL_EMAILS_SENT();
//    console.log("Im working 9");
//     ALL_CLICKED_EMAILS();//display number
}
function progress_process_chart(percentages){
 
    var percentage=[];
    var names=[];
//     console.log(percentages);
// console.log(typeof(percentages));
    percentages.forEach( element => {
        percentage.push(element[1]);
        names.push(element[0]);

 });
    // console.table(percentages);
    console.log(names);
    console.log(percentages);

    let chart = document.getElementById("progress_process_chart").getContext("2d");//chart positioning in html
    let myChart = new Chart(chart, {
    type: "line",
    
    data: {
        labels: names,
        datasets: [{
            label: "Number Of ",//name at top of chart describing the labels on the x axis eg. SMMEs
            data: percentage,//the percentages for each smme(y axis)
            //backgroundColor: "rgba(13, 192, 255, 0.31)",//the background for the bars, same or individual colours
            borderColor: '#113382'//the border for the bars, same or individual colours
        }]
    }, 
    options: {
        responsive: true,
        scales: {
            yAxes:[{
                display:true,
                ticks:{
                  beginAtZero: true  
                }
            }]
        },
        title: {
            display: true,
            text: "Progress Process Life Cycle" //heading of chart
        }

    }
});
}
function search_chart(percentages){
 
    var percentage=[];
    var names=[];
console.log(percentages);
    percentages.forEach( element => {
        percentage.push(element[1][1]);
        names.push(element[1][0]);

    });

    // console.table(percentages);
    console.log(names);
    console.log(percentage);

    let chart = document.getElementById("search_chart").getContext("2d");//chart positioning in html
    let myChart = new Chart(chart, {
    type: "bar",
    data: {
        labels: names,
        datasets: [{
            label: "Searched Terms",//name at top of chart describing the labels on the x axis eg. SMMEs
            data: percentage,//the percentages for each smme(y axis)
            backgroundColor: '#113382'
    
        //the background for the bars, same or individual colours

        }],

    }, 
    options: {
        responsive: true,
        scales: {
            yAxes:[{
                display:true,
                ticks:{
                  beginAtZero: true  
                }
            }]
        },
        title: {
            display: true,
            text: "Search Terms By their hits " //heading of chart
        }

    }
});
}

function create_page_vivits_chart(percentages){
 
    var percentage=[];
    var names=[];

    percentages.forEach( element => {
        percentage.push(element[1]);
        names.push(element[0]);

    });

    // console.table(percentages);
    // console.log(names);
    // console.log(percentage);

    let chart = document.getElementById("page_visits_chart").getContext("2d");//chart positioning in html
    let myChart = new Chart(chart, {
    type: "bar",
    data: {
        labels: names,//smme names/company names(x axis)
        datasets: [{
            label: "Entity Comparison based on compliance",//name at top of chart describing the labels on the x axis eg. SMMEs
            data: percentage,//the percentages for each smme(y axis)
            backgroundColor: [
            '#8A0984',
            '#113382',
            '#959EB3',
    
        ]//the background for the bars, same or individual colours

        }],

    }, 
    options: {
        responsive: true,
        scales: {
            yAxes:[{
                ticks:{
                  beginAtZero: true  
                }
            }]
        },
        title: {
            display: true,
            text: "Comparitive Chart " //heading of chart
        }

    }
});
}


function  make_request(identifier, token,id, output){
    
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../Main/Main_ADMIN.php", true);

     xhr.onload = function (){
        if(this.status == 200){
            
            // console.log(typeof(xhr_results));
            // return;
            // if(typeof(xhr_results)==='string'){

            // }
           if(typeof(output)==='function'){
            let xhr_results = JSON.parse(this.response);
            //    console.log(progress_process_percentages);
            //    console.log(xhr_results);
            //    new_array = xhr_results.split("[ ]");
            //    console.log(new_array);
               output(Object.entries(xhr_results));
           }else{
                let xhr_results = this.response;
               document.getElementById(id).innerHTML = xhr_results;
              // console.log(typeof(output));
           }
        //    console.log(xhr_results);
           
            
          
        }else{
            console.log("Onload is working but the if statement is not")
        }
         
    }
    xhr.onerror = ()=>{
        console.log("XHR is flopping somewhere");
    }   
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("tk="+token+"&identifier="+identifier);
}

function  PROGRESS_PROCESS(){//chart

    let token = document.getElementById("tk").value;
    let identifier = "PROGRESS_PROCESS";
    make_request(identifier, token, identifier, progress_process_chart);
}


function PROCESS_AVERAGE_TIME(){//display number
    let token = document.getElementById("tk").value;
    let identifier = "PROCESS_AVERAGE_TIME";
    let display_id = "process_summary";
    make_request(identifier, token, display_id );
}

function ANALYTICS_SUMMARY_HEADER(){//display number
    let token = document.getElementById("tk").value;
    let identifier = "SUMMARY_ANALYTICS_HEADER";
    let display_id = "summary_analytics_header";
    make_request(identifier, token, display_id );
}
function PAGE_VISITS_GRAPGH(){//chart
    let token = document.getElementById("tk").value;
    let identifier = "PAGE_VISITS_GRAPGH";
    make_request(identifier, token, identifier, create_page_vivits_chart);
}


function PAGE_VISITS(){//display number
    let token = document.getElementById("tk").value;
    let identifier = "PAGE_VISITS";
    let display_id = "visits_summary"
    make_request(identifier, token, display_id );
}


function SEARCH_GRAPGH(){//chart
    let token = document.getElementById("tk").value;
    let identifier = "SEARCH_GRAPGH";
    make_request(identifier, token, identifier, search_chart);
}


function SEARCH_SUMMARY(){//display name
    let token = document.getElementById("tk").value;
    let identifier = "SEARCH_SUMMARY";
    let display_id = "search_summary"
    make_request(identifier, token, display_id);
}


// function CURRENT_DAY_SEARCHES(){//display number
//     let token = document.getElementById("tk").value;
//     let identifier = "CURRENT_DAY_SEARCHES";
//     make_request(identifier, token, identifier );
// }


// function ALL_EMAILS_SENT(){//display number
//     let token = document.getElementById("tk").value;
//     let identifier = "ALL_SENT_EMAILS";
//     make_request(identifier, token, identifier );
// }


// function ALL_CLICKED_EMAILS(){//display number
//     let token = document.getElementById("tk").value;
//     let identifier = "ALL_CLICKED_EMAILS";
//     make_request(identifier, token, identifier );
// }