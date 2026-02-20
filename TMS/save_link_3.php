<?php
// Include the database connection file
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the required fields are present in the form submission
    if (isset($_POST['CLIENT_ID'])) {
        
        
        $CLIENT_ID=$_POST['CLIENT_ID'];
        $WORKTYPE_ID=$_POST['WORKTYPE_ID'];
        $JOB_ID=$_POST['JOB_ID'];
        $LINK=$_POST['LINK'];
        $LINK_NAME=$_POST['link_name'];
        
        
               $insertQuery = $conn->prepare("INSERT INTO level_three_links (CLIENT_ID,WorkType_ID, JOB_ID, LINK, LINK_NAME) VALUES (?, ?, ?,?,?)");
                $insertQuery->bind_param('iiiss', $CLIENT_ID,$WORKTYPE_ID,$JOB_ID, $LINK, $LINK_NAME);
                $insertQuery->execute();
            
          
if ($insertQuery) {
    echo "<p style='color:green; font-size:20px; font-weight:bold'>Data saved successfully!</p>";
   echo "<a href='index.php?page=add_link_lvl3&CLIENT_ID=" . $CLIENT_ID . "&task_id=".$WORKTYPE_ID."&job_id=".$JOB_ID."'>
        <span class='badge badge-info' style='font-size:18px; font-family:Segoe UI;'>Back</span>
      </a>";

} else {
    echo "<p style='color:red; font-size:20px; font-weight:bold'>Error: In database operation!</p>";
}

        
            
        
        
        
    } else {
        // Handle the case when required fields are missing in the form submission
        echo "Error: Missing required fields!";
    }
}
?>