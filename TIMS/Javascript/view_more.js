
window.onload = ()=>{loadchart();}
// document.getElementById('shareholder_chart').innerHTML = create_chart();
function loadchart(){
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../Main/Main_view_more.php", true);

     xhr.onload = function (){
        if(this.status == 200){
            let xhr_results = JSON.parse(this.response);
            // console.log(xhr_results); 
            if(xhr_results == -1){
                document.getElementById("shareholder_chart_1").innerHTML = "";
                console.log("error");
            }else{
                console.log("error");
                create_chart(xhr_results);
            }
            
            // console.log(" Pino im going to console log you here."); 
            // return xhr_results;
        }else{
            console.log("Onload is working but the if statement is not")
        }
         
    }
    xhr.onerror = ()=>{
        console.log("XHR is flopping somewhere");
    }
    
        let b = document.getElementById('tk').value;
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("tk="+b+"&id="+document.getElementById('entity').value);
    }
    

function create_chart(result){
   
    
    const ctx1 = document.getElementById('shareholder_chart_1').getContext('2d');
    const ctx2 = document.getElementById('shareholder_chart_2').getContext('2d');
    let indexes = ['Number_Shareholders', 'White_Ownership_Percentage', 'Black_Ownership_Percentage', 'Black_Female_Percentage'];
    let percentages = [];
    
   for(var x = 1; x<4; x++){
       percentages.push(result[indexes[x]]);
       
        // console.log(indexes[x]);
   }
    // let percentages_1 = percentages.shift();
    // let percentages_2 = percentages;
       console.log(percentages);
       
    // console.log(percentages);
    let chart2_percentages = percentages.pop();
    const myChart = new Chart(ctx1, {
        type: 'pie',
        data: {
            datasets: [{
                label: '% of Shareholders',
                data: percentages,
                backgroundColor: [
                    '#0dc0ff',
                    '#032033'
                ],
                borderColor: [
                    'rgb(54, 162, 235)',
                    'rgb(54, 162, 235)'
                ],
                borderWidth: 1,
                hoverOffset: 4
            }],
            labels: ['White Ownership %','Black ownership %']
        },
        options: {
            responsive : true,
            legend:{
                position: 'top'
            }
        }
    });
    
       let percentages_2 = [chart2_percentages,100-chart2_percentages];
    const myChart2 = new Chart(ctx2, {
        type: 'pie',
        data: {
            datasets: [{
                label: '% of Shareholders',
                data: percentages_2,
                backgroundColor: [
                    '#0dc0ff',
                    '#032033'
                ],
                borderColor: [
                    'rgb(54, 162, 235)',
                    'rgb(54, 162, 235)'
                ],
                borderWidth: 1,
                hoverOffset: 4
            }],
            labels: ['Black Female ownership %', 'Black Male ownership %']
        },
        options: {
            responsive : true,
            legend:{
                position: 'top'
            }
        }
    });
    
    
// var myChart = new Chart(ctx1, {
//   type: 'bar',
//   data: {
//     labels: ["<  1","1 - 2","3 - 4","5 - 9","10 - 14","15 - 19","20 - 24","25 - 29","> - 29"],
//     datasets: [{
//       label: 'Employee',
//       backgroundColor: "#caf270",
//       data: [12, 59, 5, 56, 58,12, 59, 87, 45],
//     }, {
//       label: 'Engineer',
//       backgroundColor: "#45c490",
//       data: [12, 59, 5, 56, 58,12, 59, 85, 23],
//     }, {
//       label: 'Government',
//       backgroundColor: "#008d93",
//       data: [12, 59, 5, 56, 58,12, 59, 65, 51],
//     }, {
//       label: 'Political parties',
//       backgroundColor: "#2e5468",
//       data: [12, 59, 5, 56, 58, 12, 59, 12, 74],
//     }],
//   },
// options: {
//     tooltips: {
//       displayColors: true,
//       callbacks:{
//         mode: 'x',
//       },
//     },
//     scales: {
//       xAxes: [{
//         stacked: true,
//         gridLines: {
//           display: false,
//         }
//       }],
//       yAxes: [{
//         stacked: true,
//         ticks: {
//           beginAtZero: true,
//         },
//         type: 'linear',
//       }]
//     },
//     responsive: true,
//     maintainAspectRatio: false,
//     legend: { position: 'bottom' },
//   }
// });
    
}

