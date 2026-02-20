<?php
// Include the database connection file
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['project_id']) && isset($_POST['task_id']) && isset($_POST['activity_id'])) {
        $projectId = $_POST['project_id'];
        $taskId = $_POST['task_id'];
        $activityId = $_POST['activity_id'];



        $query2=$conn->query( "DELETE FROM assigned_duties WHERE task_id = $taskId and activity_id = $activityId");
        sleep(2);
        header("Location: index.php?page=assign_duties&id=$projectId");
            exit;
        }
}

