


let white_ownership = document.getElementById("white_ownership_percentage");
let black_ownership = document.getElementById("black_ownership_percentage");
let black_female = document.getElementById("black_female_percentage");
black_female.onchange = () =>{
    if(white_ownership.value !== "" && black_ownership.value !== "" && black_female.value !== ""){
        let white_ownership_percentage = white_ownership.value;
        let black_ownership_percentage = black_ownership.value;
        let black_female_percentage = black_female.value;
        let sum = white_ownership_percentage + black_ownership_percentage;
        if(sum > 100){
            //then that means this is an invalid percentage
        }
        else{
            //this is valid and submission is allowed
        }
    }
    else if(white_ownership.value !== "" && black_ownership.value !== "" && black_female.value !== ""){

    }
    
}