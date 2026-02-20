<?php
// Include the database connection file
include 'db_connect.php';

$result = null; // Initialize the $result variable

 if (isset($_GET['activity_id'], $_GET['project_id'])){
    // Check if the necessary POST parameters are set
 
        

          $period=$_GET['period'];
        $where=$_GET['where'];
        $priority=$_GET['priority'];
 
        $projectId = $_GET['project_id'];
        $activityId = $_GET['activity_id'];
        
       $login_id = $_SESSION['login_id'];
       
    
       
        // Use prepared statements to protect against SQL injection
        $stmt = $conn->prepare("SELECT CONCAT(u.firstname, ' ', u.lastname) AS fullname, 
        ts.team_name,
        pl.start_date as job_start_date, 
        pl.id, pl.end_date as job_end_date, 
        ad.user_id, ad.activity_id,
         CONCAT(u1.firstname, ' ', u1.lastname) AS who_closed,
        ad.project_id, pl.name as jobname, 
        ad.request_days, ad.request_done, 
        ad.days_left, ad.done_days, 
        tl.task_name, up.name as task,
        ad.my_closing_date,
        ad.duration, 
        ad.my_quantities,
        ad.my_comment,
        ad.Done_Date,
        ad.Final_Date,
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
        $stmt->bind_param("iii", $login_id, $activityId, $projectId);
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

        // Close the statement
        $stmt->close();
    } else {
        echo "Error: Missing or incomplete parameters.";
    }

?>


<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-body">
            

        <form id="manage-days" method="post" action="./index.php?page=save_request">
                <input type="hidden" name="project_id" value="<?php echo isset($projectId) ? $projectId : '' ?>">
                <input type="hidden" name="activity_id" value="<?php echo isset($activityId) ? $activityId : '' ?>">
                <input type="hidden" name="login_id" value="<?php echo isset($_SESSION['login_id']) ? $_SESSION['login_id'] : '' ?>">
                  <input type="hidden" name="period" value="<?php echo isset($period) ? $period : '' ?>">
                    <input type="hidden" name="where" value="<?php echo isset($where) ? $where: '' ?>">
                <input type="hidden" name="done" value="111">
                
 
                
 
                <input type="hidden" name="period" value="<?php echo $period ?>">
                <input type="hidden" name="where" value="<?php echo $where ?>">
                <input type="hidden" name="priority" value="<?php echo $priority ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="text-decoration: underline;" for="" class="control-label">Member Name</label>
                            <p style><?php echo isset($row['fullname']) ? $row['fullname'] : 'N/A' ?></p>
                            <label  style="text-decoration: underline;" for="" class="control-label">Job</label>
                            <p><?php echo isset($row['jobname']) ? $row['jobname'] : 'N/A' ?></p>
                             <label  style="text-decoration: underline;" for="" class="control-label">Job_ID</label>
                            <p><?php echo isset($row['id']) ? $row['id'] : 'N/A' ?></p>
                               <label style="text-decoration: underline;" for="" class="control-label">Team</label>
                            <p><?php echo isset($row['team_name']) ? $row['team_name'] : 'N/A' ?></p>
                            <label style="text-decoration: underline;" for="" class="control-label">Work Type</label>
                            <p><?php echo isset($row['task']) ? $row['task'] : 'N/A' ?></p>
                            <label style="text-decoration: underline;" for="" class="control-label">Activity</label>
                            <p><?php echo isset($row['task_name']) ? $row['task_name'] : 'N/A' ?></p>
                        </div>
                           <br>
                            <label style="text-decoration: underline;" class="control-label">Closed quantity</label>
                            <p style="color:green"><?php echo isset($row['my_quantities']) ? $row['my_quantities'] : 'N/A'; ?></p>
                             <label style="text-decoration: underline;" class="control-label">Closed off Date</label>
                            <p style="color:green"><?php echo isset($row['my_closing_date']) ? $row['my_closing_date'] : 'N/A'; ?></p>
                            <label style="text-decoration: underline;" class="control-label">Comment</label>
                            <p><?php echo isset($row['my_comment']) ? htmlspecialchars($row['my_comment']) : 'N/A'; ?></p>
                    </div>
                    <div class="col-md-6">
				<div class="form-group">
				       <label style="text-decoration: underline;" for="" class="control-label">Job Start</label>
                            <p><?php echo isset($row['job_start_date']) ? $row['job_start_date'] : 'N/A' ?></p>
                              <label style="text-decoration: underline;" for="" class="control-label">Job Date</label>
                            <p><?php echo isset($row['job_end_date']) ? $row['job_end_date'] : 'N/A' ?></p>
                            
                </div> 
			
				
				 <br>
				  <br>
				  <label style="text-decoration: underline; color:red" class="control-label">Job Closed</label>
				 <br>
			 <label style="text-decoration: underline;" class="control-label">Final closed off Quantity</label>
                            <p style="color:green"><?php echo isset($row['pm_quantities']) ? $row['pm_quantities'] : 'N/A'; ?></p>
                            
                            	 <label style="text-decoration: underline;" class="control-label">Who Closed</label>
                            <p style="color:green"><?php echo isset($row['who_closed']) ? $row['who_closed'] : 'N/A'; ?></p>
                            
                             <label style="text-decoration: underline;" class="control-label">Final Closed off Date</label>
                            <p style="color:green"><?php echo isset($row['Final_Date']) ? $row['Final_Date'] : 'N/A'; ?></p>
                        
                            <label style="text-decoration: underline;" class="control-label">Final Comment</label>
                            <p><?php echo isset($row['pm_comment']) ? htmlspecialchars($row['pm_comment']) : 'N/A'; ?></p>
				</div>
			</div>
                </div>
            </form>
        </div>
        
<?php
if (!empty($period)) {
    echo '<button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href=\'index.php?page=my_progress_period&p=' . $period . '&w=' . $where . '\'">Back</button>';
} elseif (!empty($priority)) {
    echo '<button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href=\'index.php?page=my_priority_jobs_due\'">Back</button>';
} else {
    echo '<button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href=\'index.php?page=my_progress\'">Back</button>';
}
?>

        
    </div>
</div>

