<?php
include_once("../Helpers/exceptions.php");
include_once("../Helpers/config.php");
// try{
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["aVyRo117Q6mLL_6fGGo"])){
 $email = filter(filter_var($_POST["pP5uG8mCkqc9O0"], FILTER_SANITIZE_EMAIL));
 $name = filter($_POST["OjLmp0Jd38Tc"]);
 $surname = filter($_POST["Ui0Evh57GMMI80vv"]);

 if(empty($email) || empty($name) ||empty($surname)){
    // throw new Error('emptyfields');
    header("location: ../index.php?Error=emptyfields");
    exit();
 }

 if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // throw new Error('invalidEmail');
    header("location: ../index.php?Error=invalidEmail");
    exit();
}

$sql = "SELECT 1 FROM emails WHERE Email=?;";
$stmt=$conn->stmt_init();
if(!$stmt->prepare($sql)){
    // throw new Error('databaseerror');
    header("location: ../index.php?Error=databaseerror");
    exit();
}
else {
    $conn->real_escape_string($email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $numResults = $stmt->num_rows;
    if($numResults!==0){
        // throw new Error('emailtaken');
        header("location: ../index.php?Error=emailtaken");
        exit();
      }else{// insert
        $sql = "INSERT INTO emails (Name, Surname, Email) VALUES (?, ?, ?);";
        if(!$stmt->prepare($sql)){
            // throw new Error('databaseerror');
            header("location: ../index.php?Error=databaseerror");
            exit();
        }
        else {
            $conn->real_escape_string($name);
            $conn->real_escape_string($surname);
            $stmt->bind_param("sss", $name, $surname, $email);
            $stmt->execute();
            if($conn->insert_id){
                header("location: ../?s#signup");
                exit();
            } else{
                // throw new Error('databaseerror');
                header("location: ../index.php?Error=databaseerror");
                exit();
            }
        }
      }
}

}else{
//  throw new Error('unauthorizedAccess');
 header("location: ../index.php?Error=unauthorizedAccess");
 exit();
}
// }
// catch(Error $e){
//     $e->errorMessage();
// }
