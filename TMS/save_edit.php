<?php
// Include the database connection file
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if the necessary POST parameters are set
    if (isset($_POST['activity_id'],$_POST['user_id'] , $_POST['project_id']) ) {
        
        
        $login_id = $_SESSION['login_id'];
        
        $pm_comment = $_POST['pm_comment'];
        $pm_quantity=$_POST['pm_quantity'];
        
        $end=$_POST['end'];
        $start=$_POST['start'];
        
       

        $userId = $_POST['user_id'];
        $projectId = $_POST['project_id'];
        $activityId = $_POST['activity_id'];
      
        if (!empty($pm_quantity)) {

    $updateQuery = "UPDATE assigned_duties
    SET
        pm_quantities = $pm_quantity,
        pm_comment = '$pm_comment',
        Final_Date =NOW(),
        who_closed = $login_id
    WHERE 
        user_id = $userId AND
        project_id = $projectId AND
        activity_id = $activityId
";

    if ($conn->query($updateQuery) === TRUE) {
        
        echo "<p style='color:red; font-size:20px; font-weight:bold'>Changes have been successfully made ! </p>";
            echo "<a href='index.php?page=period_claims&id=$projectId&start=$start&end=$end'><span class='badge badge-info' style='font-size:18px; font-family: segoe ui'> Back</span></a>";

                
    }

       
} 

}
}
?>


