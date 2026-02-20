<?php
// Include the database connection file
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the required fields are present in the form submission
    if (isset($_POST['project_id']) && isset($_POST['task_id']) && isset($_POST['activity_id']) && isset($_POST['user_id'])) {
        // Get the project ID, task ID, activity ID, and selected user IDs from the form
        $projectId = $_POST['project_id'];
        $taskId = $_POST['task_id'];
        $activityId = $_POST['activity_id'];
        $userIdsArray = $_POST['user_id'];

        // Convert user IDs array to a comma-separated string
        $userIdsString = implode(',', $userIdsArray);

        // Check if assigned duties already exist for the given activity
        $checkQuery = "SELECT * FROM assigned_duties WHERE project_id = '$projectId' AND task_id = '$taskId' AND activity_id = '$activityId'";
        $result = $conn->query($checkQuery);

        if ($result && $result->num_rows > 0) {
            // Assigned duties already exist, so update the existing record
            $updateQuery = "UPDATE assigned_duties SET user_id = '$userIdsString' WHERE project_id = '$projectId' AND task_id = '$taskId' AND activity_id = '$activityId'";

            // Execute the update query
            if ($conn->query($updateQuery) === TRUE) {
                // Data updated successfully
                echo "Data updated successfully!";
                sleep(2);
                // Redirect to another page (change the URL to your desired page)
                header("Location: index.php?page=assign_duties&id=$projectId");
                exit; // Make sure to exit to prevent further execution of the script
            } else {
                // Error handling if the query fails
                echo "Error updating data: " . $conn->error;
            }
        } else {
            // Assigned duties do not exist, so insert a new record
            $insertQuery = "INSERT INTO assigned_duties (project_id, task_id, activity_id, user_id) 
                            VALUES ('$projectId', '$taskId', '$activityId', '$userIdsString')";

            // Execute the insert query
            if ($conn->query($insertQuery) === TRUE) {
                // Data saved successfully
                echo "Data saved successfully!";
                sleep(2);
                // Redirect to another page (change the URL to your desired page)
                header("Location: index.php?page=assign_duties&id=$projectId");
                exit; // Make sure to exit to prevent further execution of the script
            } else {
                // Error handling if the query fails
                echo "Error inserting data: " . $conn->error;
            }
        }
    } else {
        // Handle the case when required fields are missing in the form submission
        echo "Error: Missing required fields!";
    }
}
