<?php
// Include the database connection file
include 'db_connect.php';

$result = null;

if (
    isset($_GET['task_id'], $_GET['activity_id'], $_GET['project_id'], $_GET['user_id'])
) { 
    
    $start = htmlspecialchars($_GET['start']);
    $end = htmlspecialchars($_GET['end']);
        $userId = $_GET['user_id'];
        $projectId = $_GET['project_id'];
        $task_id = $_GET['task_id']; // Assuming 'login_id' is passed as 'id'
        $activityId = $_GET['activity_id'];
        
         $stmt = $conn->prepare("SELECT CONCAT(u.firstname, ' ', u.lastname) AS fullname, 
        ts.team_name, 
        pl.id , 
        pl.start_date, 
        pl.end_date,ad.user_id,
        CONCAT(u1.firstname, ' ', u1.lastname) AS who_closed,
        ad.activity_id, 
        ad.project_id, 
        pl.name as jobname, 
        ad.request_days, 
        ad.request_done, 
        ad.days_left,
        ad.my_closing_date,
        ad.done_days,
        ad.Done_Date,
        tl.task_name,
        ad.Final_Date,
        up.name as task, 
        ad.duration,
        ad.my_quantities,
        ad.my_comment,
        ad.pm_quantities,
        ad.pm_comment,
        ad.start_date, 
        ad.days_left, 
        ad.status
        FROM assigned_duties ad
        JOIN user_productivity up ON ad.activity_id = up.id
        JOIN task_list tl ON tl.id = up.task_id
        JOIN users u ON u.id = ad.user_id
        LEFT JOIN users u1 ON u1.id = ad.who_closed
         LEFT JOIN team_schedule ts ON ts.team_id = ad.team_id
        JOIN project_list pl ON ad.project_id = pl.id
        WHERE ad.user_id = ? AND ad.activity_id = ? AND ad.project_id = ?");

        // Bind parameters and execute the query
        $stmt->bind_param("iii", $userId, $activityId, $projectId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the query was successful
        if ($result->num_rows > 0) {
            // Fetch the first row (you can loop through all rows if needed)
            $row = $result->fetch_assoc();

            // Now you can access the data from the $row array
        } else {
            echo "No results found.";
        }
    
} else {
    echo "<div class='alert alert-danger'>Missing required parameters.</div>";
}
?>
  
<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-body">
            <form id="manage-days" method="post" action="./index.php?page=save_edit">
                 <input type="hidden" name="project_id" value="<?php echo isset($projectId) ? $projectId : '' ?>">
                <input type="hidden" name="activity_id" value="<?php echo isset($activityId) ? $activityId : '' ?>">
                <input type="hidden" name="user_id" value="<?php echo isset($userId) ? $userId : '' ?>">
                <input type="hidden" name="end" value="<?php echo isset($end) ? $end : '' ?>">
                <input type="hidden" name="start" value="<?php echo isset($start) ? $start : '' ?>">
                
               <div class="row">
                    <div class="col-md-6">
                        <label class="control-label" style="text-decoration: underline;">Member Name</label>
                        <p><?php echo isset($row['fullname']) ? htmlspecialchars($row['fullname']) : 'N/A'; ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label" style="text-decoration: underline;">Team Name</label>
                        <p><?php echo isset($row['team_name']) ? htmlspecialchars($row['team_name']) : 'N/A'; ?></p>
                    </div>
                
                    <div class="col-md-6">
                        <label class="control-label" style="text-decoration: underline;">Job</label>
                        <p><?php echo isset($row['jobname']) ? htmlspecialchars($row['jobname']) : 'N/A'; ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label" style="text-decoration: underline;">Job ID</label>
                        <p><?php echo isset($row['id']) ? $row['id'] : 'N/A'; ?></p>
                    </div>
                
                    <div class="col-md-6">
                        <label class="control-label" style="text-decoration: underline;">Work Type</label>
                        <p><?php echo isset($row['task']) ? htmlspecialchars($row['task']) : 'N/A'; ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label" style="text-decoration: underline;">Activity</label>
                        <p><?php echo isset($row['task_name']) ? htmlspecialchars($row['task_name']) : 'N/A'; ?></p>
                    </div>
                
                    <div class="col-md-6">
                        <label class="control-label" style="text-decoration: underline;">Closed Quantity</label>
                        <p style="color:green;"><?php echo isset($row['pm_quantities']) ? $row['pm_quantities'] : 'N/A'; ?></p>
                    </div>
                     <div class="col-md-6">
                        <label class="control-label" style="text-decoration: underline;">Who Closed</label>
                        <p style="color:green;"><?php echo isset($row['who_closed']) ? $row['who_closed'] : 'N/A'; ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label" style="text-decoration: underline;">Final Closed Off Date</label>
                        <p style="color:green;"><?php echo isset($row['Final_Date']) ? $row['Final_Date'] : 'N/A'; ?></p>
                    </div>
                
                    <div class="col-md-6">
                        <label class="control-label" style="text-decoration: underline;">Comment</label>
                        <p><?php echo isset($row['pm_comment']) ? htmlspecialchars($row['pm_comment']) : 'N/A'; ?></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Revised Quantity Done</label>
                            <input type="number" class="form-control form-control-sm" name="pm_quantity" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Comment</label>
                            <textarea name="pm_comment" cols="2" rows="2" class="form-control" required></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer border-top border-info">
            <div class="d-flex w-100 justify-content-center align-items-center">
                <button class="btn btn-flat bg-gradient-primary mx-2" type="submit" form="manage-days">Save</button>
                <button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href='index.php?page=period_claims&id=10526&start= <?php echo $start ?>&end=<?php echo $end ?>'">Back</button>
            </div>
        </div>
    </div>
</div>

