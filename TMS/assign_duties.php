<?php
include 'db_connect.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* -----------------------------
   Decode encrypted job ID (job=)
   and map it back to $_GET['id']
   so your existing SQL stays the same
------------------------------ */
if (isset($_GET['job']) && $_GET['job'] !== '') {

    $decoded = base64_decode($_GET['job'], true);
if ($decoded === false) {
    die("Invalid job reference");
}

$parts = explode('|', $decoded);
if (count($parts) !== 2 || !ctype_digit($parts[0])) {
    die("Invalid job reference");
}



    $_GET['id'] = (int)$parts[0]; // ✅ now the rest of your code works unchanged
}

/* -----------------------------
   Your existing code (unchanged)
------------------------------ */

// Retrieve task IDs from project_list table
$projectId = $_GET['id'];
$qry = $conn->query("SELECT task_ids, name, team_ids, manager_id FROM project_list WHERE id = " . $projectId)->fetch_array();
$manager_id = isset($qry['manager_id']) ? $qry['manager_id'] : null;
$taskIdsString = $qry['task_ids'];

$team_id = $qry['team_ids'];

// Convert task IDs string to an array of integers
$taskIdsArray = array_map('intval', explode(',', $taskIdsString));

// Retrieve task names from task_list table based on task IDs
$taskIdsString = implode(',', $taskIdsArray); // Convert task IDs array back to a comma-separated string

// Fetch contents from user_productivity related to the tasks in the project
$taskContents = array(); // An array to store the contents related to tasks

if (!empty($taskIdsString)) {
    $productivityQry = $conn->query("SELECT id, name, duration, comment, resources, task_id FROM user_productivity WHERE task_id IN ($taskIdsString)");
    $work_typename = $conn->query("SELECT id, task_name FROM task_list WHERE id IN ($taskIdsString)");
    
    // Create an associative array to store the task names with their task IDs as keys
    $taskNamesMap = array();
    while ($row = $work_typename->fetch_assoc()) {
        $taskNamesMap[$row['id']] = $row['task_name'];
    }

    $previousWorkTypeName = null; // To keep track of the previous work type name

    while ($row = $productivityQry->fetch_assoc()) {
        // Check if the task_id exists in the taskNamesMap before adding to taskContents
        if (isset($taskNamesMap[$row['task_id']])) {
            // Add the row to the $taskContents array and include the 'worktype_name' field from the task_names map
            $row['task_name'] = $taskNamesMap[$row['task_id']];
            $taskContents[] = $row;

            // Check if the current work type name is the same as the previous one
            if ($row['task_name'] !== $previousWorkTypeName) {
                // Display the work type name as a label for each set of tasks
            }

            // Update the previous work type name to the current one
            $previousWorkTypeName = $row['task_name'];
        }
    }
}
?>



<div class="col-lg-12">
    <?php echo "<p style='font-size:20px'>Job being assigned: <span style='color:red'>" . $qry['name'] . "</span></p>"; ?>
    <div class="card card-outline card-primary">
        <form action="save_assign.php" method="post" id="save-assign">
            <input type="hidden" name="project_id" value="<?php echo $projectId; ?>">
            <div class="row">
                <?php 
                if (empty($manager_id)) {
                    echo '<div class="col-md-12">No Manager assigned to this job.</div>';
                } else {
                    // Check if there are any task contents to display
                    if (!empty($taskContents)) {
                        foreach ($taskContents as $content) {
                ?>
                <div class="col-md-6">
                    <p class="form-control-plaintext" style="font-size: 20px"><?php echo $content['task_name']; ?></p>
                    <div class="form-group border" style="padding-left: 20px;">
                        <label for="task_name_<?php echo $content['task_id']; ?>">Activity Name</label>
                        <p class="form-control-plaintext"><?php echo $content['name']; ?></p>
                    </div>

                    <div class="form-group border" style="padding-left: 20px;">
                        <label for="duration_<?php echo $content['task_id']; ?>">Duration (in days)</label>
                        <p class="form-control-plaintext"><?php echo $content['duration']; ?></p>
                    </div>
                    <div class="form-group border" style="padding-left: 20px;">
                        <label for="duration_<?php echo $content['task_id']; ?>">Description</label>
                        <p class="form-control-plaintext"><?php echo html_entity_decode($content['comment']); ?></p>
                    </div>

                    <?php
                    // Fetch assigned duties for the current activity
                    $qry = $conn->query("SELECT user_id FROM assigned_duties WHERE activity_id = " . $content['id'] . " AND project_id = " . $projectId);
                        
                    // Check if the query result is not empty
                    if ($qry !== false && $qry->num_rows > 0) {
                        $userIds = array();
                        
                        while ($row = $qry->fetch_assoc()) {
                            $userIds[] = $row['user_id'];
                        }
                        
                        $userIdsString = implode(",", $userIds); // Convert array to comma-separated string
                        $qry2 = $conn->query("SELECT DISTINCT CONCAT(firstname, ' ', lastname) AS full_name FROM users WHERE id IN ($userIdsString)");
                        
                        $userNames = array();
                        while ($row = $qry2->fetch_assoc()) {
                            $userNames[] = $row['full_name'];
                        }
                    } else {
                        // If 'user_id' field is empty or the query result is null, set $userNames as an empty array
                        $userNames = array();
                        
                    }
                    ?>
                    <div class="form-group border" style="padding-left: 20px;">
                        <label for="resources_<?php echo $content['task_id']; ?>">Activity Resources information</label>
                        <label class="form-control-plaintext">(Required Resources: <?php echo $content['resources']; ?>)</label>

                        <?php if (!empty($userNames)): ?>
                            <label style="color: #007BFF">Assigned Resources:</label>
                            <?php foreach ($userNames as $userName): ?>
                                <span><?php echo html_entity_decode($userName) . ", "; ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <label style="color: red">No resources assigned yet</label>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($userNames)): ?>
                    <div class="card-footer border-top border-info">
                        <div class="d-flex w-100 justify-content-center align-items-center">
                            <a class="dropdown-item view_project" style="background-color: #14A44D; color: white; width: 160px; border-radius: 3px ">Resources Assigned</a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="card-footer border-top border-info">
                        <div class="d-flex w-100 justify-content-center align-items-center">
                            <?php
                                $jobRef      = urlencode(base64_encode((string)$projectId));
                                $activityRef = urlencode(base64_encode((string)$content['id']));
                                $taskRef     = urlencode(base64_encode((string)$content['task_id']));
                                $teamRef     = urlencode(base64_encode((string)$team_id));
                                ?>
                                
                                <a class="dropdown-item view_project"
                                   style="background-color:#007BFF; color:white; width:145px; border-radius:3px"
                                   href="./index.php?page=save_assign&job=<?php echo $jobRef; ?>&activity=<?php echo $activityRef; ?>&task=<?php echo $taskRef; ?>&team=<?php echo $teamRef; ?>">
                                   Assign Resources
                                </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <hr style="border-top: 2px dashed #007BFF">
                    <br>
                </div>
                <?php
                        }
                    } else {
                        echo '<div class="col-md-12">No work types assigned to this job.</div>';
                    }
                }
                ?>
            </div>
        </form>
    </div>  
</div>



<style>
    /* Card container styling */
    .card {
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        padding: 20px;
        background-color: #f8f9fa;
    }

    /* Header styling */
    .card-header {
        background-color: #007bff;
        color: white;
        font-size: 1.25rem;
        padding: 10px 15px;
        border-radius: 10px 10px 0 0;
    }

    /* Task name and other field styling */
    .form-control-plaintext {
        font-size: 18px;
        font-weight: 500;
        margin-bottom: 10px;
    }

    /* Form group border styling */
    .form-group {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 15px;
        background-color: white;
    }

    /* Label styling */
    .form-group label {
        font-weight: 600;
        color: #555;
        font-size: 16px;
    }

    /* Hover effect for resources buttons */
    .dropdown-item.view_project {
        font-size: 16px;
        padding: 10px;
        text-align: center;
        transition: background-color 0.3s ease;
    }

    .dropdown-item.view_project:hover {
        background-color: #0056b3;
        color: white;
    }

    /* Text styling for required and assigned resources */
    .form-control-plaintext span,
    .form-control-plaintext label {
        font-size: 16px;
        color: #333;
    }

    /* Assigned resources label */
    .form-control-plaintext label[style="color: #007BFF"] {
        font-weight: bold;
        margin-top: 10px;
    }

    /* Error message for unassigned resources */
    .form-control-plaintext label[style="color: red"] {
        font-size: 16px;
        font-weight: bold;
    }

    /* Card footer */
    .card-footer {
        background-color: #f1f1f1;
        padding: 10px;
        text-align: center;
        border-radius: 0 0 10px 10px;
    }

    /* Dashed line divider */
    hr {
        border-top: 2px dashed #007BFF;
        margin: 20px 0;
    }

    /* General spacing */
    .col-md-6 {
        margin-bottom: 30px;
    }

    /* Responsiveness */
    @media (max-width: 768px) {
        .col-md-6 {
            width: 100%;
        }

        .dropdown-item.view_project {
            width: 100%;
        }
    }
</style>


