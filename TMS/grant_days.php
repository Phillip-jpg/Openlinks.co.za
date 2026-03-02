<?php
// Include the database connection file
include 'db_connect.php';

$result = null; // Initialize the $result variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the necessary POST parameters are set
    if (isset($_POST['id'], $_POST['activity_id'], $_POST['project_id'], $_POST['user_id'])) {
        $userId = $_POST['user_id'];
        $projectId = $_POST['project_id'];
        $loginId = $_POST['id']; // Assuming 'login_id' is passed as 'id'
        $activityId = $_POST['activity_id'];
        $days_left=$_POST['days_left'];

        $period=$_POST['period'];
        $where=$_POST['where'];
        
        $priority=$_POST['priority'];
        
        // echo $priority;
       
        // Use prepared statements to protect against SQL injection
        $stmt = $conn->prepare("SELECT CONCAT(u.firstname, ' ', u.lastname) AS fullname, 
        ts.team_name, pl.id , 
        pl.start_date, 
        pl.end_date,ad.user_id,
        CONCAT(u1.firstname, ' ', u1.lastname) AS who_closed,
        ad.activity_id, 
        ad.project_id, pl.name as jobname, 
        ad.request_days, 
        ad.request_done, 
        ad.days_left,
        ad.my_closing_date,
        ad.done_days,
        ad.Done_Date,
        ad.Final_Date,
        tl.task_name, 
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
        WHERE ad.manager_id = ? AND ad.user_id = ? AND ad.activity_id = ? AND ad.project_id = ?");

        // Bind parameters and execute the query
        $stmt->bind_param("iiii", $loginId, $userId, $activityId, $projectId);
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
}
?>


<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-body">
            

        <form id="manage-days" method="post" action="./save_grant_days.php">
                <input type="hidden" name="project_id" value="<?php echo isset($projectId) ? $projectId : '' ?>">
                <input type="hidden" name="manager_id" value="<?php echo isset($loginId) ? $loginId: '' ?>">
                <input type="hidden" name="activity_id" value="<?php echo isset($activityId) ? $activityId : '' ?>">
                <input type="hidden" name="user_id" value="<?php echo isset($userId) ? $userId : '' ?>">
                <input type="hidden" name="days_left" value="<?php echo isset($days_left) ? $days_left : '' ?>">
                <input type="hidden" name="period" value="<?php echo $period ?>">
                <input type="hidden" name="where" value="<?php echo $where ?>">
                <input type="hidden" name="priority" value="<?php echo $priority ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="text-decoration: underline;" for="" class="control-label">Member Name</label>
                            <p style><?php echo isset($row['fullname']) ? $row['fullname'] : 'N/A' ?></p>
                              <label style="text-decoration: underline;" for="" class="control-label">Team Name</label>
                            <p style><?php echo isset($row['team_name']) ? $row['team_name'] : 'N/A' ?></p>
                            <label  style="text-decoration: underline;" for="" class="control-label">Job</label>
                            <p><?php echo isset($row['jobname']) ? $row['jobname'] : 'N/A' ?></p>
                              <label  style="text-decoration: underline;" for="" class="control-label">Job ID</label>
                            <p><?php echo isset($row['id']) ? $row['id'] : 'N/A' ?></p>
                            <label style="text-decoration: underline;" for="" class="control-label">Work Type</label>
                            <p><?php echo isset($row['task']) ? $row['task'] : 'N/A' ?></p>
                            <label style="text-decoration: underline;" for="" class="control-label">Activity</label>
                            <p><?php echo isset($row['task_name']) ? $row['task_name'] : 'N/A' ?></p>
                      
                            <br>
                            <label style="text-decoration: underline;" class="control-label">Closed quantity</label>
                            <p style="color:green"><?php echo isset($row['my_quantities']) ? $row['my_quantities'] : 'N/A'; ?></p>
                             <label style="text-decoration: underline;" class="control-label">Member Closed off Date</label>
                            <p style="color:green"><?php echo isset($row['my_closing_date']) ? $row['my_closing_date'] : 'N/A'; ?></p>
                            <label style="text-decoration: underline;" class="control-label">Comment</label>
                            <p><?php echo isset($row['my_comment']) ? htmlspecialchars($row['my_comment']) : 'N/A'; ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
				<div class="form-group">
				     <label style="text-decoration: underline;" for="" class="control-label">Job Start Date</label>
                            <p><?php echo isset($row['start_date']) ? $row['start_date'] : 'N/A' ?></p>
                            <label style="text-decoration: underline;" for="" class="control-label">Job End Date</label>
                            <p><?php echo isset($row['end_date']) ? $row['end_date'] : 'N/A' ?></p>
                
                <?php if ($row['request_days'] == 1 ): ?>
					<label for="">Add more Days</label>
					<select name="duration" id="status" class="custom-select custom-select-sm">
                        <option value="0" >0-Day</option>
						<option value="1" >1-Day</option>
						<option value="2" >2-Days</option>
						<option value="3" >3-Days</option>
                        <option value="4" >4-Days</option>
						<option value="5" >5-Days</option>
                        <option value="6" >6-Days</option>
						<option value="7" >7-Days</option>
                        <option value="8" >8-Days</option>
						<option value="9" >9-Days</option>
                        <option value="10">10-Days</option>
                        <option value="11">Deny Days</option>

					</select>
				<?php endif; ?>
				</div>
				
				 <?php if ($row['request_done'] == 1): ?>
				<div class="form-group">
					<label for="" class="control-label">Revised Quantity Done</label>
			
					<input type="number" class="form-control form-control-sm" name="pm_quantity">
					
				</div>
					<div class="form-group">
					<label for="" class="control-label">Comment</label>
					<textarea name="pm_comment" id="pm_comment" cols="2" rows="2" class="form-control" style="text-align:left;"></textarea>
				</div>
				   <?php endif; ?>
				   
				  <?php if ($row['request_done'] == 2): ?>
                    <!-- Nothing displayed when request_done == 2 -->
                <?php else: ?>
                    <div class="form-group">
                        <label for="status">Work Status</label>
                        <select name="status" id="status" class="custom-select custom-select-sm">
                            <option value="In-progress">In-progress</option>
                            <?php if ($row['request_done'] == 1): ?>
                                <option value="Done">Done</option>
                            <?php endif; ?>
                            <!-- <option value="On-Hold">On-Hold</option>
                            <option value="Denied">Deny done</option> -->
                        </select>
                     
                    </div>
                <?php endif; ?>

				 
				 <?php if ($row['request_done'] == 2): ?>
				 <br>
				  <br>
				  <label style="text-decoration: underline; color:red" class="control-label">Job Closed</label>
				 <br>
			 <label style="text-decoration: underline;" class="control-label">Final closed of Quantity</label>
                            <p style="color:green"><?php echo isset($row['pm_quantities']) ? $row['pm_quantities'] : 'N/A'; ?></p>
                        
                        	 <label style="text-decoration: underline;" class="control-label">Who Closed</label>
                            <p style="color:green"><?php echo isset($row['who_closed']) ? $row['who_closed'] : 'N/A'; ?></p>
                            
                             <label style="text-decoration: underline;" class="control-label">Closed off Date</label>
                            <p style="color:green"><?php echo isset($row['Final_Date']) ? $row['Final_Date'] : 'N/A'; ?></p>
                        
                            <label style="text-decoration: underline;" class="control-label">Final Comment</label>
                            <p><?php echo isset($row['pm_comment']) ? htmlspecialchars($row['pm_comment']) : 'N/A'; ?></p>
				   <?php endif; ?>
			</div>
                </div>
            </form>
        </div>
        <div class="card-footer border-top border-info">
            <div class="d-flex w-100 justify-content-center align-items-center">
                
                	  <?php if ($row['request_done'] == 2): ?>
                    <!-- Nothing displayed when request_done == 2 -->
                <?php else: ?>
                      <button class="btn btn-flat bg-gradient-primary mx-2" type="submit" form="manage-days" id="grant-save-btn">Save</button>
                <?php endif; ?>
               <?php
if (!empty($period)) {
                        echo '<button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href=\'index.php?page=my_team_progress_period&p=' . $period . '&w=' . $where . '\'">Back</button>';
                    } elseif (!empty($priority)) {
                        echo '<button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href=\'index.php?page=priority_requests\'">Back</button>';
                    } else {
                        echo '<button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href=\'index.php?page=all_my_teams_progress\'">Back</button>';
                    }
                    ?>
            </div>
            <div id="grant-save-status" class="mt-3" style="display:none;"></div>
        </div>
    </div>
</div>

<script>
$(function () {
    var $form = $('#manage-days');
    var $saveBtn = $('#grant-save-btn');
    var $status = $('#grant-save-status');
    var saveLabel = $.trim($saveBtn.text()) || 'Save';
    var isSubmitting = false;

    if (!$form.length || !$saveBtn.length) {
        return;
    }

    function setSaveState(disabled, label) {
        $saveBtn.prop('disabled', disabled).text(label);
    }

    function showStatus(type, message) {
        var statusClass = 'alert-info';
        if (type === 'success') {
            statusClass = 'alert-success';
        } else if (type === 'danger') {
            statusClass = 'alert-danger';
        }

        $status
            .removeClass('alert alert-info alert-success alert-danger')
            .addClass('alert ' + statusClass)
            .text(message)
            .show();
    }

    $form.off('submit.grantDays').on('submit.grantDays', function (e) {
        e.preventDefault();

        if (isSubmitting) {
            return false;
        }

        isSubmitting = true;
        setSaveState(true, 'Saving...');
        showStatus('info', 'Saving request, please wait...');

        if (typeof start_load === 'function') {
            start_load();
        }

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            timeout: 60000,
            success: function (resp) {
                var cleanResp = $.trim(String(resp || ''));
                var normalizedResp = cleanResp.toLowerCase();
                var isSaved =
                    normalizedResp.indexOf('changes have been successfully made') !== -1 ||
                    normalizedResp === 'ok';

                if (isSaved) {
                    showStatus('success', 'Changes saved successfully.');
                    if (typeof alert_toast === 'function') {
                        alert_toast('Changes saved successfully.', 'success');
                    }
                    setSaveState(true, 'Saved');
                    $form.find('input, textarea, select').not(':hidden').prop('disabled', true);
                    return;
                }

                var errMessage = cleanResp || 'Save failed.';
                showStatus('danger', errMessage);
                if (typeof alert_toast === 'function') {
                    alert_toast(errMessage, 'danger');
                }
                isSubmitting = false;
                setSaveState(false, saveLabel);
            },
            error: function (xhr, status) {
                var message = status === 'timeout'
                    ? 'Save timed out. Please try again.'
                    : $.trim(String(xhr.responseText || 'Request failed.'));

                showStatus('danger', message);
                if (typeof alert_toast === 'function') {
                    alert_toast(message, 'danger');
                }
                isSubmitting = false;
                setSaveState(false, saveLabel);
            },
            complete: function () {
                if (typeof end_load === 'function') {
                    end_load();
                }
            }
        });

        return false;
    });
});
</script>
