<?php
if (!isset($conn)) {
    include 'db_connect.php';
}

$team_id = $_GET['team_id'] ?? null;

if ($team_id) {
    $stmt = $conn->prepare("
       SELECT DISTINCT
    ts.team_id,
    u.id AS member_id,
    ts.Date_created,
    ts.status,
    ts.count_deleted,
    CONCAT(u.firstname, ' ', u.lastname) AS member
FROM 
    team_schedule ts
JOIN 
    users u ON u.id = ts.team_members
WHERE 
    ts.team_id = ?;

    ");
    $stmt->bind_param('i', $team_id);
    $stmt->execute();
    $work_qry = $stmt->get_result();
}
?>

<div class="col-lg-12">
    <div class="card-body">
                <!-- Form for Adding Team Members -->
                <form action="./index.php?page=add_team_member" method="POST">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                               
                            <?php
                            // Ensure $team_id is sanitized
                            $team_id = intval($team_id); // Convert to integer to prevent injection
                            
                            // Use a prepared statement to fetch data
                            $stmt = $conn->prepare("
                                SELECT DISTINCT
                                    ts.team_id,
                                    ts.team_name,
                                    ts.*,
                                    CONCAT(u.firstname, ' ', u.lastname) AS member,
                                    CONCAT(u1.firstname, ' ', u1.lastname) AS pm,
                                    CONCAT(u2.firstname, ' ', u2.lastname) AS operations,
                                    u.id,
                                    tl.task_name AS work_type,
                                    COUNT(ts.worktype_ids) AS work_type_count
                                FROM
                                    team_schedule ts
                                LEFT JOIN users u  ON u.id  = ts.team_members
                                LEFT JOIN users u1 ON u1.id = ts.pm_manager
                                LEFT JOIN users u2 ON u2.id = ts.op_ids
                                LEFT JOIN task_list tl ON tl.id = ts.worktype_ids
                                WHERE
                                    ts.team_id = ?
                                GROUP BY 
                                    ts.team_id, member, pm, operations, u.id, work_type;
                            ");
                            $stmt->bind_param('i', $team_id); // Bind the $team_id parameter
                            $stmt->execute();
                            $qry = $stmt->get_result(); // Execute and get results
                            
                            // Fetch a single row from the results (if applicable)
                            $row1 = $qry->fetch_assoc()
                            ?>
                            
                            <!-- Hidden Inputs for Form -->
                            <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($team_id); ?>">
                            <input type="hidden" name="team_name" value="<?php echo htmlspecialchars($row1['team_name'] ?? ''); ?>">
                            <input type="hidden" name="manager_id" value="<?php echo htmlspecialchars($row1['pm_manager'] ?? ''); ?>">
                             <input type="hidden" name="op_ids" value="<?php echo htmlspecialchars($row1['op_ids'] ?? ''); ?>">
                                <!-- Add Team Members Dropdown -->
                                <label for="user_ids" class="control-label">Add Team Member</label>
                                <select class="form-control form-control-sm select2" name="user_ids" id="user_ids">
                                    <option value="0"></option>
                                    <?php
                                 if($_SESSION['login_type'] == 2 ){
                					    	$employees = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE type = 3 AND creator_id = {$_SESSION['login_id']} OR id={$_SESSION['login_id']}  ORDER BY name ASC;");
                					}else{
                					      $employees = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE type = 3 OR type =2 ORDER BY name ASC");
                					}
                                
                                while ($row = $employees->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $row['id']; ?>">
                                        <?php echo ucwords($row['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                                
                                </select>
                                <br>
                                <label for="" class="control-label">Add Work Type</label>
                            <select class="form-control form-control-sm select2" name="worktype_id" id="worktype_id">
                                <option value="0"></option>
                                  <?php
                                if($_SESSION['login_type'] == 2 ){
                					    	$worktypes = $conn->query("SELECT DISTINCT tl.*
                                                                    FROM
                                                                        task_list tl
                                                                    LEFT JOIN
                                                                        members_and_worktypes mw ON tl.id = mw.work_type_id
                                                                    WHERE
                                                                        mw.member_id = {$_SESSION['login_id']}
                                                                        OR tl.creator_id = {$_SESSION['login_id']}");
                					}else{
                					      $worktypes = $conn->query("SELECT * FROM task_list ORDER BY task_name ASC");
                					}
                                
                                while ($row = $worktypes->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $row['id']; ?>">
                                        <?php echo ucwords($row['task_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            </div>
                        </div>
       
                        <div class="col-md-4">
                            <div class="form-group">
                               
                                <!-- Add Team Members Dropdown -->
                                <label for="user_ids" class="control-label">Change Operational Manager</label>
                                <select class="form-control form-control-sm select2" name="changed_op_ids">
                                  	<option value="0"></option>
                                  	   <?php
                                         if($_SESSION['login_type'] == 2 ){
                        					    	$employees = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE type = 3 AND creator_id = {$_SESSION['login_id']} OR id={$_SESSION['login_id']}  ORDER BY name ASC;");
                        					}else{
                        					      $employees = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE type = 3 OR type =2 ORDER BY name ASC");
                        					}
                                        
                                        while ($row = $employees->fetch_assoc()):
                                        ?>
                                            <option value="<?php echo $row['id']; ?>">
                                                <?php echo ucwords($row['name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                  </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer border-top border-info">
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-flat bg-gradient-primary mx-2" type="submit">Save</button>
                            <button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href='index.php?page=schedule_teams_lvl2'">Back</button>
                        </div>
                    </div>
                </form>
            </div>
    <div class="card card-outline card-success shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title">Team: (<?php echo $row1['team_name'] ?>)  <br> <hr>  PM: (<?php echo $row1['pm'] ?>)   <br> <hr>  Operational Leader: (<?php echo $row1['operations'] ?>)  <br> 
            <hr>  Worktypes: (
    <?php 
    $works = $conn->query("SELECT 
        GROUP_CONCAT(DISTINCT tl.task_name SEPARATOR ', ') AS Worktypes
        FROM team_schedule ts
        LEFT JOIN task_list tl ON tl.id = ts.worktype_ids
        WHERE ts.team_id = $team_id");

    while ($row = $works->fetch_assoc()):
        echo $row['Worktypes'];
    endwhile;
    ?>
)  </h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-condensed" id="list">
                    <colgroup>
                        <col width="15%">
                        <col width="15%">
                        <col width="30%">
                        <col width="20%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead style="background-color:#032033; color:white;">
                        <tr>
                            <th>Team_ID</th>
                            <th>Member_ID</th>
                            <th>Name of Member</th>
                            <th>Added Date</th>
                            <th>Status</th>
                            <th>Number of Deactivated</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($work_qry)): ?>
                            <?php while ($row = $work_qry->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['team_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['member_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['member']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Date_created']); ?></td>
                                    <td>
                                        <?php echo $row['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                    </td>
                                                                        <td>
                                        <?php echo $row['count_deleted'] ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                            Action
                                        </button>
                                        <div class="dropdown-menu">
                                            <?php if ($row['status'] == 0): ?>
                                                <a class="dropdown-item" href="./index.php?page=active_member&id=<?php echo $row['member_id']; ?>&status=0&team_id=<?php echo $row['team_id']; ?>">Activate</a>
                                            <?php else: ?>
                                                <a class="dropdown-item" href="./index.php?page=active_member&id=<?php echo $row['member_id']; ?>&status=1&team_id=<?php echo $row['team_id']; ?>">Deactivate</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
 <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No team members found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>

<style>
    .table-responsive {
        overflow-x: auto;
    }
    table td, table th {
        vertical-align: middle !important;
    }
    .table-hover tbody tr:hover {
        background-color: #f9f9f9;
    }
</style>
<script>
	$(document).ready(function(){
		$('#list').dataTable()
	
	$('.delete_task').click(function(){
	_conf("Are you sure to delete this work type?","delete_task",[$(this).attr('data-id')])
	})

	$('.new_productivity').click(function(){
		uni_modal("<i class='fa fa-plus'></i> New Progress for: "+$(this).attr('data-task'),"manage_progress.php?tid="+$(this).attr('data-tid'),'large')
	})

	})
	function delete_task($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_task',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>
