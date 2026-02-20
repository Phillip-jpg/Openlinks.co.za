<?php
include 'db_connect.php';

if (isset($_GET['hide_id'])) {

    $id = (int) $_GET['hide_id'];

    $result = $conn->query("
        UPDATE schedule_work_team
        SET Activated = 1
        WHERE id = $id
    ");

    // Redirect ONLY if query succeeded
    if ($result === TRUE) {
        
        echo "<a  href='index.php?page=schedule_teams_lvl3'><span class='badge badge-info' style='font-size:18px; font-family: segoe ui'> Info Saved <br> <br>Back</span></a>";
    } else {
        // Optional: handle failure
        echo "<p style='color:red;'>Update failed or no rows changed.</p>";
    }
}
?>
