<?php
// Include database connection
include 'db_connect.php';

// Retrieve and validate input parameters
$id = isset($_GET['id']) ? intval($_GET['id']) : null; // Ensure id is an integer
$status = isset($_GET['status']) ? intval($_GET['status']) : null; // Ensure status is an integer
$team_id = isset($_GET['team_id']) ? intval($_GET['team_id']) : null; // Ensure team_id is an integer

if ($id === null || $status === null || $team_id === null) {
    die("Invalid input: ID, status, and team_id are required.");
}

try {
    // Determine the new status (toggle)
    $new_status = ($status === 1) ? 0 : 1;

    // Prepare the SQL query to update the status and conditionally increment count_deleted
    $updateQuery = $conn->prepare("
        UPDATE team_schedule 
        SET 
            status = ?, 
            count_deleted = CASE 
                WHEN ? = 0 THEN count_deleted + 1 
                ELSE count_deleted 
            END
        WHERE 
            team_members = ? 
            AND team_id = ?
    ");

    if (!$updateQuery) {
        throw new Exception("Failed to prepare query: " . $conn->error);
    }

    // Bind parameters (new_status, new_status for condition, member ID, team ID)
    $updateQuery->bind_param('iiii', $new_status, $new_status, $id, $team_id);

    // Execute the query
    if ($updateQuery->execute()) {
        // Redirect to the team page
        header("Location: ./index.php?page=team&team_id=" . urlencode($team_id));
        exit; // Terminate script after redirection
    } else {
        throw new Exception("Failed to execute query: " . $updateQuery->error);
    }
} catch (Exception $e) {
    // Handle errors gracefully
    echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
