
window.onload = function(){
    loadChart();
}
function loadChart(){
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../Main/Main_Mysmme_Mybbbee.php", true); // mybbbee for smmes, smmes view companies
    xhr.onload = function (){
        if(this.status == 200){
            let list = JSON.parse(this.response);
            console.log(list);
            
            
            if(list==="error"){
                document.getElementById("chart_cont").innerHTML = "<p class='text-capitalize text-center h1' >There seems to have been an error, we are doing our best to handle it.</p>";
                console.log("error");
            }
            else {
                if(list == -1){
                    document.getElementById("chart_cont").innerHTML = "<p class='text-capitalize text-center h1' >No Connections Yet</p>";
                }else{
                    console.log(list.length);
                    create_chart(list);
                }
                
                }
        }
    }
        let b = document.getElementById('tk').value;
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("tk="+b);
       
}


function create_chart(percentages){
 
    var percentage=[];
    var names=[];

    percentages.forEach( element => {
        percentage.push(element[1]);
        names.push(element[0]);

    });

    console.table(percentages);
    console.log(names);
    console.log(percentage);



    let chart = document.getElementById("comparitive_Chart").getContext("2d");//chart positioning in html
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
//let total = sum of all expenses
//let num_expenses = total/3000 divide by a factor of 3000
// round the number of expense to a int not a decimal number but round down
//this then means we have an estimated number of expenses
//then we must take,  estimated_expense_total = num_expenses * 3000
//now we compare, if( total >= estimated_expense_total) => 10points
//work out further what the actual percentage is based on the scale factor

//the BBBEE rating is tricky


