window.onload = ()=>{
    ANALYTICS_SUMMARY_HEADER();
    KEYWORD_GRAPGH();
    ANALYTICS_MARKETPLACE_HEADER();
     CONNECTIONS_GRAPGH();
     PROFILE_STATS();
     SEARCH_CHART();
}


function keywords_chart(percentages){
 
    var percentage=[];
    var names=[];

    percentages.forEach( element => {
        console.log(element);
        percentage.push(element[1]);
        names.push(element[0])
    });
   
    
   // #0dc0ff, #032033
    let chart = document.getElementById("keyword_chart").getContext("2d");//chart positioning in html
    let myChart = new Chart(chart, {
    type: "doughnut",
    
    data: {
        labels:names,
        datasets: [{
            label: "Number Of ",//name at top of chart describing the labels on the x axis eg. SMMEs
            data: percentage,//the percentages for each smme(y axis)
            backgroundColor: ["#032033","#0dc0ff"],//the background for the bars, same or individual colours
            borderColor: 'rgba(13, 192, 255, 0.31)'//the border for the bars, same or individual colours
        }]
    }, 
    options: {
        legend: true,
        responsive: true,
        title: {
            display: true,
            text: "Keyword Performance" //heading of chart
        }

    }
});
}
function search_chart(percentages){
 
    var percentage=[];
    var names=[];
console.log(percentages);
    percentages.forEach( element => {
        percentage.push(element[1]);
        names.push(element[0])
    });
    // console.table(percentages);
    
   // #0dc0ff, #032033
    let chart = document.getElementById("company_search_chart").getContext("2d");//chart positioning in html
    let myChart = new Chart(chart, {
    type: "pie",
    
    data: {
        labels:names,
        datasets: [{
            label: "Number Of ",//name at top of chart describing the labels on the x axis eg. SMMEs
            data: percentage,//the percentages for each smme(y axis)
            backgroundColor: ["#113382","#6932a8", "#a83232", "#36a832"],//the background for the bars, same or individual colours
            borderColor: 'rgba(13, 192, 255, 0.31)'//the border for the bars, same or individual colours
        }]
    }, 
    options: {
        legend: true,
        responsive: true,
        title: {
            display: true,
            text: "Search Performance" //heading of chart
        }

    }
});
}
function entity_connections_chart(percentages){
 
    let percentage=[];
    let names=[];
    let system_connections=[];
    let new_array = [];
    console.log(percentages);
    new_array = percentages.shift();
    // new_array.shift();
    // percentages.shift();
    // console.log(new_array);
    // console.log(percentages);
    // if(new_array[1].length > percentages[1].length){
    //    let difference =  new_array[1].length - percentages[1].length;
    //    for(i = 1; i<=difference; i++){
    //     percentages[1]
    //    }
    // }
    new_array[1].forEach( element => {
        system_connections.push(element[1]);
        names.push(element[0]);
    });

    percentages.forEach( element => {
        percentage.push(element[1][1]);
        
 });
    // #0dc0ff, #032033
 let chart = document.getElementById("connections_chart").getContext("2d");//chart positioning in html



 let mybarChart = new Chart(chart, {
     type: 'bar',
     data: {
        labels: names,
        datasets: [{
          label: "My Connections",
          backgroundColor: "#0dc0ff",
          borderColor: "rgba(13, 192, 255, 0.7)",
          pointBorderColor: "rgba(13, 192, 255, 0.7)",
          pointBackgroundColor: "rgba(13, 192, 255, 0.7)",
          pointHoverBackgroundColor: "#fff",
          pointHoverBorderColor: "rgba(220,220,220,1)",
          pointBorderWidth: 2,
          data: percentage
        }, {
          label: "Average Connection",
          backgroundColor: "#032033",
          borderColor: "rgba(3, 32, 51, 0.70)",
          pointBorderColor: "rgba(3, 32, 51, 0.70)",
          pointBackgroundColor: "rgba(3, 32, 51, 0.70)",
          pointHoverBackgroundColor: "#fff",
          pointHoverBorderColor: "rgba(151,187,205,1)",
          pointBorderWidth: 1,
          data: system_connections
        }]
      },
//sponsorship requests
     options: {
         legend: true,
       scales: {
         yAxes: [{
           ticks: {
             beginAtZero: true
           }
         }]
       }
     }
   });
}

function system_connections_chart(percentages){
 
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
   // #0dc0ff, #032033
    let chart = document.getElementById("keyword_chart").getContext("2d");//chart positioning in html
    let myChart = new Chart(chart, {
    type: "doughnut",
    
    data: {
        labels:names,
        datasets: [{
            label: "Number Of ",//name at top of chart describing the labels on the x axis eg. SMMEs
            data: percentage,//the percentages for each smme(y axis)
            backgroundColor: ["#0dc0ff","#032033"],//the background for the bars, same or individual colours
            borderColor: 'rgba(13, 192, 255, 0.31)'//the border for the bars, same or individual colours
        }]
    }, 
    options: {
        plugins:{
            legend:{
                display:true,

            }
        },
        responsive: true,
        title: {
            display: true,
            text: "Keyword Performance" //heading of chart
        }

    }
});

}
function  make_request(identifier, token,id, output){
    console.log("This person called me " + identifier);
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../Main/Main_analytics.php", true);

     xhr.onload = function (){
        if(this.status == 200){
            
           if(typeof(output)==='function'){
            if(this.response == 1){
                document.getElementById(id).innerHTML = "No information available";
            }else{
                let xhr_results = JSON.parse(this.response);
         
               output(Object.entries(xhr_results));
            }
        //    console.log(this.response);
            
              
           }else{
            let xhr_results = this.response;
               document.getElementById(id).innerHTML = xhr_results;
               
           }
           
           
            
          
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
function ANALYTICS_SUMMARY_HEADER(){//display number
    let token = document.getElementById("tk").value;
    let identifier = "COMPANY_ANALYTICS_HEADER";
    let display_id = "company_analytics_header";
    make_request(identifier, token, display_id );
}
function ANALYTICS_MARKETPLACE_HEADER(){//display number
    let token = document.getElementById("tk").value;
    let identifier = "COMPANY_MARKETPLACE_HEADER";
    let display_id = "company_marketplace_header";
    make_request(identifier, token, display_id );
}
function KEYWORD_GRAPGH(){//chart
    let token = document.getElementById("tk").value;
    let identifier = "COMPANY_KEYWORD_GRAPGH";
    make_request(identifier, token, identifier, keywords_chart);
}
function CONNECTIONS_GRAPGH(){//chart
    let token = document.getElementById("tk").value;
    let identifier = "COMPANY_CONNECTIONS_GRAPGH";
    make_request(identifier, token, identifier, entity_connections_chart);
    
}
function SEARCH_CHART(){//chart
    let token = document.getElementById("tk").value;
    let identifier = "COMPANY_SEARCH_GRAPGH";
    let display_id = "company_search_chart";
    make_request(identifier, token, identifier, search_chart);
    
}
function PROFILE_STATS(){//chart
    let token = document.getElementById("tk").value;
    let identifier = "COMPANY_PROFILE_STATS";
    let display_id = "company_profile_stats";
    make_request(identifier, token, display_id );
    
}
