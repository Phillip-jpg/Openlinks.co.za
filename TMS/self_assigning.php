<?php
include 'db_connect.php';

// Check if the request method is GET and if project_id and task_id are present in the URL
if (isset($_GET['project_id']) && isset($_GET['task_id']) && isset($_GET['activity_id'])) {
    // Get the project ID and task ID from the URL
    $projectId = $_GET['project_id'];
    $taskId = $_GET['task_id'];
    $activityId = $_GET['activity_id'];
    // Now you have both the project ID and task ID, and you can use them for further processing

    // Fetch data from the 'user_productivity' table based on the given task ID
    $qry = $conn->query("SELECT * FROM user_productivity WHERE id = $activityId");
    
    // Check if the query returned any result
    if ($qry) {
        // Fetch the data as an associative array
        $row = $qry->fetch_assoc();
        
        // Dynamically create variables for each column in the fetched array
        foreach ($row as $k => $v) {
            $$k = $v;
        }

        // Now you can use the variables to access the data for the task
        // ... display other data or perform further actions as needed
    }
    
    $qry = $conn->query("SELECT user_ids, manager_id, CLIENT_ID FROM project_list WHERE id = " . $projectId)->fetch_array();
    $userIdsString = $qry['user_ids'];
    $managerId = $qry['manager_id'];
    $client_id = $qry['CLIENT_ID'];
    // Append manager_id to the user_ids string
    if (!empty($managerId)) {
        $userIdsString .= "," . $managerId;
    }

    
    // Convert user IDs string to an array of integers
$userIdsArray = array_map('intval', explode(',', $userIdsString));

// Prepare the IN clause for the user IDs
$userIdsClause = implode(',', $userIdsArray);

$query = "SELECT DISTINCT member_id FROM members_and_worktypes WHERE work_type_id = $taskId AND member_id IN ($userIdsClause)";

$result = $conn->query($query);

if ($result->num_rows == 0) {
    echo "<div style='color:red; font-weight:bold'>Non of the members you selected for the job qualify for this worktype !</div>";
    echo "<br>";
}


elseif($result->num_rows>0){

    $matchingMemberIds = array(); // Initialize an array to store matching member IDs

while ($row = $result->fetch_assoc()) {
    $matchingMemberIds[] = $row['member_id'];
}

// Prepare the IN clause for the member IDs

$query=$conn->query("SELECT user_id FROM assigned_duties WHERE activity_id is NOT NULL AND  task_id = $taskId AND project_id =$projectId");

if ($query) {
    $userIds = array();

    while ($row = $query->fetch_array()) {
        $userIds[] = $row['user_id'];
    }
} 

$differentUserIds = array_diff($matchingMemberIds, $userIds);


if (empty($differentUserIds)) {

    echo "<div style='color:red; font-weight: bold'><i class='fa fa-exclamation-triangle' style='font-size:20px' aria-hidden='true'></i>You have selected all your members. Members can only work on one work type !</div>"; 
   

   
    echo "<br>";

 

     


}else{

    $memberIdsClause = implode(',', $differentUserIds);

    $query2 = "SELECT CONCAT(firstname, ' ', lastname) AS full_name, id FROM users WHERE id IN ($memberIdsClause)";
 // Print the query for debugging

 //print_r($memberIdsClause);
$result2 = $conn->query($query2);

    $fullNames = array(); // Initialize an array to store full names

    while ($row = $result2->fetch_assoc()) {
        $fullNames[$row['id']] = $row['full_name'];
       
    }

    if (empty($fullNames)) {
        echo "No members";
    } else {
        // Generate the HTML <option> elements for the dropdown select field
        // Default option
        foreach ($fullNames as $userId => $fullName) {
            $selected = '';
            if (isset($_POST['user_id'])) {
                $userIdsArray = is_array($_POST['user_id']) ? $_POST['user_id'] : explode(',', $_POST['user_id']);
                $selected = in_array($userId, $userIdsArray) ? 'selected' : '';
            }
           
        }
    }

}
// Query to retrieve concatenated full names for matching member IDs

}


}


?>


<form action="delete_assigned.php" id="assign-delete" method="post">
<input type="hidden" name="task_id" value="<?php echo isset($taskId) ? $taskId : '' ?>"><input type="hidden" name="project_id" value="<?php echo isset($projectId) ? $projectId : '' ?>">
<input type="hidden" name="activity_id" value="<?php echo isset($activityId) ? $activityId : '' ?>">
</form>

<div class="col-lg-12">
    <form action="save_assigned_database.php" id="assign-save" method="post">
        <div class="row">
            <div class="col-md-12">
                <div class="callout callout-info">  
                    <div class="col-md-12">
                        <div class="row">
                            <input type="hidden" name="task_id" value="<?php echo isset($taskId) ? $taskId : '' ?>">
                            <input type="hidden" name="project_id" value="<?php echo isset($projectId) ? $projectId : '' ?>">
                            <input type="hidden" name="activity_id" value="<?php echo isset($activityId) ? $activityId : '' ?>">
                            <input type="hidden" name="manager_id" value="<?php echo isset($managerId ) ? $managerId  : '' ?>">
                            <input type="hidden" name="client_id" value="<?php echo isset($client_id) ? $client_id  : '' ?>">
                            <div class="col-sm-6">
                                <?php
                            $qry = $conn->query("SELECT task_name FROM task_list WHERE id = $taskId");
                            if ($qry) {
                                $row = $qry->fetch_assoc();

                                echo "<div style='color: #007BFF; font-weight:bold'>" . $row['task_name'] . "</div>";

                            
                            }
                            ?> 
                               <?php  echo "<br>" ?>
                                    <dt><b class="border-bottom border-primary">Activity Name</b></dt>
                                    <dd><?php echo ucwords($name) ?></dd>
                                    <dt><b class="border-bottom border-primary">Duration (in days)</b></dt>
                                    <dd><?php echo html_entity_decode($duration) ?></dd>
                                    <dt><b class="border-bottom border-primary">Comment</b></dt>
                                    <dd><?php echo html_entity_decode($comment) ?></dd>
                                    <dt><b class="border-bottom border-primary">No of Resources  </b></dt>
                                    <dd><?php echo html_entity_decode($resources) ?></dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <!-- Add a form element to wrap the "Select Members (Resources)" form -->
                                <div class="form-group">
                                                    <label for="user_id" class="control-label">
                        Select Members (Resources) <span style="font-weight:normal; color:#007BFF">select  <?php echo $resources; ?> at most</span>
                    </label>

                                    <select class="form-control form-control-sm select2" name="user_id[]" multiple="multiple">
                                        <option value="">Select Resources</option>
                                        <?php
                                        // Loop through the $fullNames array to create select options
                                        foreach ($fullNames as $userId => $fullName) {
                                            // Check if the user ID is in the array of previously selected user IDs
                                            $selected = '';
                                            if (isset($_POST['user_id']) && isset($task_ids)) {
                                                $userIdsArray = is_array($_POST['user_id']) ? $_POST['user_id'] : explode(',', $_POST['user_id']);
                                                $selected = in_array($userId, $userIdsArray) ? 'selected' : '';
                                            }
                                            echo "<option value=''>Select Non</option>";
                                            echo "<option value='$userId' $selected>$fullName</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        
    </form>
    <div class="card-footer border-top border-info">
            <div class="d-flex w-100 justify-content-center align-items-center">
            <button class="btn btn-flat bg-gradient-secondary mx-2" style="border-radius: 3px" onclick="redirectToAssignDuties(<?php echo $projectId; ?>)">Back</button>
            <button class="btn btn-flat bg-gradient-primary mx-2" style="border-radius: 3px" type="submit" form="assign-save">Save</button>
                

            </div>
        </div>
</div>

<script>
function redirectToAssignDuties(projectId) {
    // Construct the URL
    var url = 'index.php?page=assign_duties&id=' + projectId;
    
    // Redirect to the URL
    window.location.href = url;
}

function resetAllocations(taskId, activityId) {
    if (confirm("Are you sure you want to reset allocations?")) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "index.php?page=save_assign&task_id=" + taskId + "&activity_id=" + activityId, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Handle the response if needed
            }
        };
        xhr.send();
    }
}

</script>
