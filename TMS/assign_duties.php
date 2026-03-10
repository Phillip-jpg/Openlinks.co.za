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



<div class="col-lg-12 assign-modern">
    <div class="mb-3">
        <a href="./index.php?page=productivity_pipeline" class="btn btn-primary btn-sm assign-back-btn">Back to Productivity Pipeline</a>
    </div>
    <?php echo "<p class='assign-title'>Job being assigned: <span class='assign-title-name'>" . htmlspecialchars($qry['name']) . "</span></p>"; ?>
    <div class="card card-outline card-primary assign-card">
        <form action="save_assign.php" method="post" id="save-assign">
            <input type="hidden" name="project_id" value="<?php echo $projectId; ?>">
            <div class="row">
                <?php 
                if (empty($manager_id)) {
                    echo '<div class="col-md-12 assign-empty">No Manager assigned to this job.</div>';
                } else {
                    // Check if there are any task contents to display
                    if (!empty($taskContents)) {
                        foreach ($taskContents as $content) {
                ?>
                <div class="col-md-6">
                    <p class="form-control-plaintext assign-task-name"><?php echo htmlspecialchars($content['task_name']); ?></p>
                    <div class="form-group border assign-block">
                        <label for="task_name_<?php echo $content['task_id']; ?>">Activity Name</label>
                        <p class="form-control-plaintext"><?php echo htmlspecialchars($content['name']); ?></p>
                    </div>

                    <div class="form-group border assign-block">
                        <label for="duration_<?php echo $content['task_id']; ?>">Duration (in days)</label>
                        <p class="form-control-plaintext"><?php echo htmlspecialchars($content['duration']); ?></p>
                    </div>
                    <div class="form-group border assign-block">
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
                    <div class="form-group border assign-block">
                        <label for="resources_<?php echo $content['task_id']; ?>">Activity Resources information</label>
                        <label class="form-control-plaintext">(Required Resources: <?php echo $content['resources']; ?>)</label>

                        <?php if (!empty($userNames)): ?>
                            <label class="assign-resource-label">Assigned Resources:</label>
                            <?php foreach ($userNames as $userName): ?>
                                <span class="assign-resource-chip"><?php echo html_entity_decode($userName); ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <label class="assign-resource-empty">No resources assigned yet</label>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($userNames)): ?>
                    <div class="card-footer border-top border-info assign-footer">
                        <div class="d-flex w-100 justify-content-center align-items-center">
                            <a class="dropdown-item view_project assign-status-btn">Resources Assigned</a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="card-footer border-top border-info assign-footer">
                        <div class="d-flex w-100 justify-content-center align-items-center">
                            <?php
                                $jobRef      = urlencode(base64_encode((string)$projectId));
                                $activityRef = urlencode(base64_encode((string)$content['id']));
                                $taskRef     = urlencode(base64_encode((string)$content['task_id']));
                                $teamRef     = urlencode(base64_encode((string)$team_id));
                                ?>
                                
                                <a class="dropdown-item view_project assign-cta-btn"
                                   href="./index.php?page=save_assign&job=<?php echo $jobRef; ?>&activity=<?php echo $activityRef; ?>&task=<?php echo $taskRef; ?>&team=<?php echo $teamRef; ?>">
                                   Assign Resources
                                </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <hr class="assign-divider">
                    <br>
                </div>
                <?php
                        }
                    } else {
                        echo '<div class="col-md-12 assign-empty">No work types assigned to this job.</div>';
                    }
                }
                ?>
            </div>
        </form>
    </div>  
</div>



<style>
    .assign-modern {
        --surface: #ffffff;
        --ink: #0f172a;
        --muted: #64748b;
        --line: #dbe7f5;
        --brand-1: #0f4c81;
        --brand-2: #0b7db5;
        --brand-3: #5eb3f3;
    }

    .assign-modern .assign-back-btn {
        border: 0;
        border-radius: 999px;
        padding: 0.42rem 0.96rem;
        font-size: 0.79rem;
        font-weight: 600;
        background: linear-gradient(125deg, var(--brand-1), var(--brand-2));
        box-shadow: 0 8px 18px rgba(11, 125, 181, 0.25);
    }

    .assign-modern .assign-back-btn:hover {
        transform: translateY(-1px);
    }

    .assign-modern .assign-title {
        color: var(--ink);
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0 0 0.8rem;
    }

    .assign-modern .assign-title-name {
        color: #ef4444;
        font-weight: 700;
    }

    .assign-modern .assign-card {
        border: 1px solid var(--line);
        border-radius: 18px;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
        margin-bottom: 0.8rem;
        padding: 0.9rem 0.9rem 0;
        background: linear-gradient(140deg, #ffffff 0%, #f8fbff 100%);
    }

    .assign-modern .col-md-6 {
        margin-bottom: 1.25rem;
    }

    .assign-modern .assign-task-name {
        color: #0f4c81;
        font-size: 1.02rem;
        font-weight: 700;
        margin-bottom: 0.55rem;
    }

    .assign-modern .assign-block {
        border: 1px solid var(--line) !important;
        border-radius: 12px;
        padding: 0.72rem 0.85rem !important;
        margin-bottom: 0.68rem;
        background: #ffffff;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
    }

    .assign-modern .assign-block label {
        color: #1e3a5f;
        font-size: 0.76rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin-bottom: 0.3rem;
    }

    .assign-modern .form-control-plaintext {
        color: #334155;
        font-size: 0.88rem;
        margin: 0;
    }

    .assign-modern .assign-resource-label {
        color: #0b7db5 !important;
        font-size: 0.78rem !important;
        font-weight: 700 !important;
        margin-top: 0.3rem;
    }

    .assign-modern .assign-resource-chip {
        display: inline-flex;
        margin: 0.18rem 0.25rem 0 0;
        padding: 0.2rem 0.58rem;
        border-radius: 999px;
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        color: #1e3a8a;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .assign-modern .assign-resource-empty {
        color: #dc2626 !important;
        font-size: 0.78rem !important;
        font-weight: 700 !important;
    }

    .assign-modern .assign-footer {
        background: #f8fbff;
        border-top: 1px solid #dbe7f5 !important;
        border-radius: 12px;
        margin-top: 0.25rem;
        padding: 0.56rem;
    }

    .assign-modern .assign-status-btn,
    .assign-modern .assign-cta-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        color: #fff !important;
        font-size: 0.79rem;
        font-weight: 600;
        min-width: 162px;
        padding: 0.42rem 0.96rem;
        text-align: center;
    }

    .assign-modern .assign-status-btn {
        background: linear-gradient(125deg, #0f9f6e, #34d399);
    }

    .assign-modern .assign-cta-btn {
        background: linear-gradient(125deg, var(--brand-1), var(--brand-2));
        box-shadow: 0 8px 18px rgba(11, 125, 181, 0.25);
    }

    .assign-modern .assign-cta-btn:hover {
        transform: translateY(-1px);
        color: #fff !important;
    }

    .assign-modern .assign-divider {
        border: 0;
        border-top: 2px dashed #93c5fd;
        margin: 0.85rem 0 0;
    }

    .assign-modern .assign-empty {
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
        color: #64748b;
        font-size: 0.9rem;
        font-style: italic;
        padding: 0.9rem 1rem;
    }

    @media (max-width: 768px) {
        .assign-modern .assign-card {
            border-radius: 14px;
            padding: 0.7rem 0.65rem 0;
        }

        .assign-modern .assign-status-btn,
        .assign-modern .assign-cta-btn {
            min-width: 100%;
        }
    }

    /* Readability overrides */
    .assign-modern {
        font-size: 0.98rem;
    }

    .assign-modern .assign-title {
        font-size: 1.2rem;
    }

    .assign-modern .assign-task-name {
        font-size: 1.08rem;
    }

    .assign-modern .assign-block label {
        font-size: 0.84rem;
    }

    .assign-modern .form-control-plaintext {
        font-size: 0.95rem;
    }

    .assign-modern .assign-resource-label,
    .assign-modern .assign-resource-empty,
    .assign-modern .assign-resource-chip {
        font-size: 0.86rem !important;
    }

    .assign-modern .assign-status-btn,
    .assign-modern .assign-cta-btn,
    .assign-modern .assign-back-btn {
        font-size: 0.9rem;
    }
</style>


