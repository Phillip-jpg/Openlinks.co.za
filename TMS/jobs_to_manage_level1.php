<?php include'db_connect.php'?>


<div class="col-lg-12">
	<div class="mb-3">
		<a href="./index.php?page=jobs_to_manage" class="btn btn-primary btn-sm">Back to Jobs To Manage</a>
	</div>
		<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white">
	
          
			<!-- <div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_job"><i class="fa fa-plus"></i> Add New Job</a>
			</div> -->
           
		</div>
		<div class="card-body">
		    <div class="form-row mb-3">
		    <div class="col-md-3">
                <label for="jobtype-filter">Filter by Job type:</label>
                <select id="jobtype-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $job_qry = $conn->query("SELECT DISTINCT JOB_TYPE
FROM project_list pl");
                    while($job_row = $job_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $job_row['JOB_TYPE']; ?>"><?php echo $job_row['JOB_TYPE']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="jobtype-filter">Filter by Month:</label>
                <select id="month-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $month_qry = $conn->query("SELECT DISTINCT wwp.month
FROM project_list pl, working_week_periods wwp WHERE wwp.start_week>= pl.date_created AND wwp.end_week>=pl.date_created");
                    while($month_row = $month_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $month_row['month']; ?>"><?php echo $month_row['month']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="team-filter">Filter by Team:</label>
                <select id="team-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $team_qry = $conn->query("SELECT DISTINCT ts.team_name
FROM project_list pl
LEFT JOIN team_schedule ts ON pl.team_ids = ts.team_id
WHERE ts.team_name IS NOT NULL AND ts.team_name != ''
ORDER BY ts.team_name ASC");
                    while($team_row = $team_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $team_row['team_name']; ?>"><?php echo $team_row['team_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
             <div class="col-md-3">
                <label for="created-filter">Filter by Who created it:</label>
                <select id="created-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $creator_qry = $conn->query("SELECT DISTINCT CONCAT(u.firstname, ' ', u.lastname) as P_name FROM project_list pl
                                            LEFT JOIN users u ON pl.Creator_ID = u.id");
                    while($creator_row = $creator_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $creator_row['P_name']; ?>"><?php echo $creator_row['P_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="assigned-filter">Filter by Assigned:</label>
                <select id="assigned-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                  $assigned_qry = $conn->query("
    SELECT DISTINCT
        CASE 
            WHEN Assigned = 1 THEN 'yes'
            ELSE 'no'
        END AS assigned_status
    FROM project_list
");
                    while($assigned_row =  $assigned_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $assigned_row['assigned_status']; ?>"><?php echo $assigned_row['assigned_status']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status-filter">Filter by Status:</label>
                <select id="status-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                        $status_qry = $conn->query("SELECT DISTINCT status
                        FROM project_list pl
                        ");
                    while($status_row = $status_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $status_row['status']; ?>"><?php echo $status_row['status']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            	</div>
             <br>
             	<div class="table-responsive">
				<table class="table tabe-hover table-condensed" id="list">
			<colgroup>
				<col width="11.11%">
					<col width="11.11%">
					<col width="11.11%">
					<col width="11.11%">
					<col width="11.11%">
					<col width="11.11%">
					<col width="11.11%">
					<col width="11.11%">
					<col width="11.11%">
				</colgroup>
				<thead style="background-color:#032033 !important; color:white">
					<tr>
						<th>Job_ID</th>
						<th>Job</th>
						<th>Job Type</th>
						<th>Team Name</th>
						<th>Date Created</th>
						<th>Who Created it</th>
						<th>Assigned</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					
					 $login_id = $_SESSION['login_id'];
					
					$team_id = 0;
					if (isset($_GET['team_id'])) {
						$rawTeamId = (string)$_GET['team_id'];
						if (ctype_digit($rawTeamId)) {
							$team_id = (int)$rawTeamId;
						} else {
							$decodedTeamId = base64_decode($rawTeamId, true);
							if ($decodedTeamId !== false) {
								$teamParts = explode('|', $decodedTeamId, 2);
								if (count($teamParts) === 2) {
									list($teamPayload, $teamHash) = $teamParts;
									$expectedTeamHash = hash_hmac('sha256', $teamPayload, 'my_app_secret_key');
									if (hash_equals($expectedTeamHash, $teamHash) && ctype_digit($teamPayload)) {
										$team_id = (int)$teamPayload;
									}
								}
							}
						}
					}
                           
                            $total_jobs_done = 0;
                            $total_jobs_inprogress=0;
                            $qry = $conn->query("SELECT DISTINCT
                                pl.*,
                                CONCAT(u.firstname, ' ', u.lastname) AS c_name,
                                ts_min.team_id,
                                ts_min.team_name,
                                ts_min.team_members
                            FROM 
                                project_list pl
                            JOIN 
                                (SELECT DISTINCT team_id, team_name,team_members FROM team_schedule WHERE team_members=$login_id) ts_min
                                ON pl.team_ids = ts_min.team_id
                            LEFT JOIN users u ON u.id = pl.Creator_ID
                                WHERE pl.team_ids=$team_id
                            ORDER BY 
                                pl.id DESC;
                        ");
                        
        
        // {$_SESSION['login_id']}
        
        
 while ($row = $qry->fetch_assoc()):
    // Your code inside the loop goes here
					
					   $words = explode(' ', $row['name']);
					  
        
                            $shortenedJobName = '';
                            if (count($words) >= 2) {
                                $shortenedJobName = implode(' ', array_slice($words, 0, 9)) . '...';
                            } else {
                                
                                $shortenedJobName = $row['name'];
                            }
		                           $total_jobs+=$row['id'];
            		               if ($row['status'] == 'Done') {
                                        $total_jobs_done++;
                                    } elseif ($row['status'] == 'In-progress') {
                                        $total_jobs_inprogress++;
                                    }

                                
						            $total_jobs=$total_jobs_done+$total_jobs_inprogress;
					?>
					<tr>
						<th class="text-center" style="color:#007bff"><?php echo ($row['id']) ?></th>
						<td>
							<p><b><?php echo ucwords($shortenedJobName) ?></b></p>
							
						</td>
						<td>
							<p><b><?php echo ucwords($row['JOB_TYPE']) ?></b></p>
							
						</td>
							<td>
							<p><b><?php echo ucwords($row['team_name']) ?></b></p>
							
						</td>
                        <td>
							<p><b><?php echo ucwords($row['date_created']) ?></b></p>
							
						</td>
						<td>
							<p><b><?php echo empty($row['c_name']) ? 'N/A' : ucwords($row['c_name']) ?></b></p>
							
						</td>
						
                    
						<?php if ($row['assigned']== 1){
 							echo "<td><p style='font-weight:bold; color:green'>Yes</p></td>";
						}else {
							echo "<td><p style='font-weight:bold; color:red'>No</p></td>";
						}
						   
						?>
					
						<td class="text-center">
								<?php
								if ($row['status'] == 'In-progress') {
									echo "<span class='badge badge-info'>{$row['status']}</span>";
								} elseif ($row['status'] == 'On-Hold') {
									echo "<span class='badge badge-warning'>{$row['status']}</span>";
								} elseif ($row['status'] == 'Dropped') {
									echo "<span class='badge badge-danger'>{$row['status']}</span>";
								} elseif ($row['status'] == 'Done') {
									echo "<span class='badge badge-success'>{$row['status']}</span>";
								}
								?>
							</td>
							

						<td class="text-center">
							<?php
							$jobPayload = (string)((int)$row['id']);
							$jobHash = hash_hmac('sha256', $jobPayload, 'my_app_secret_key');
							$jobRef = urlencode(base64_encode($jobPayload . '|' . $jobHash));
							$backTeamRef = isset($_GET['team_id']) ? urlencode((string)$_GET['team_id']) : '';
							?>
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" >
		                      <a class="dropdown-item view_project" href="./index.php?page=view_job&job=<?php echo $jobRef ?>&back=jobs_to_manage_level1<?php echo $backTeamRef !== '' ? '&back_team=' . $backTeamRef : '' ?>" data-id="<?php echo $row['id'] ?>">View</a>
		                      
							<?php if($_SESSION['login_type'] ==2): ?>
						
		                      <a class="dropdown-item" href="./index.php?page=edit_job&id=<?php echo $row['id'] ?>">Edit</a>
		                       
		                  <?php endif; ?>
		                    </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				
				</tbody>
				 <div class="container mt-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h2>Job Management Summary</h2>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="font-weight:bold"><b>Total Jobs: </b><?php echo number_format($total_jobs); ?></p>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                    <p style="font-weight:bold" >Jobs Completed: <strong style="color:green"><?php echo number_format($total_jobs_done); ?></strong></p>
                                </div>
                                <div class="col-md-6">
                                    <p style="font-weight:bold">Jobs in progress : <strong style="color:red"><?php echo number_format($total_jobs_inprogress); ?></strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			</table>
		</div>
		</div>
	</div>
</div>
<style>
	table p{
		margin: unset !important;
	}
	table td{
		vertical-align: middle !important
	}
</style>
<script>
$(document).ready(function(){
    // Initialize DataTable
    var dataTable = $('#list').DataTable({
        order: [[0, 'desc']]
    });

    // Event listener for each filter dropdown
    $('#jobtype-filter').change(function(){
        filterTable();
    });
    
    $('#month-filter').change(function(){
        filterTable();
    });

    $('#team-filter').change(function(){
        filterTable();
    });

    $('#created-filter').change(function(){
        filterTable();
    });

    $('#assigned-filter').change(function(){
        filterTable();
    });

    $('#status-filter').change(function(){
        filterTable();
    });

    // Function to filter the DataTable
    function filterTable() {
        var selectedJobType = $('#jobtype-filter').val();
        var selectedmonth = $('#month-filter').val();
        var selectedTeam = $('#team-filter').val();
        var selectedCreator = $('#created-filter').val();
        var selectedAssigned = $('#assigned-filter').val();
        var selectedStatus = $('#status-filter').val();

        // Apply filter for each column:
        dataTable.column(2).search(selectedJobType)
            .column(3).search(selectedTeam)
            .column(5).search(selectedCreator)
            .column(6).search(selectedAssigned)
            .column(7).search(selectedStatus)
            .draw();  // Redraw the table with the new filters
    }

    // Handle deletion of projects

});


	$(document).ready(function(){
	$('.delete_project').click(function(){
	_conf("Are you sure to delete this job?","delete_project",[$(this).attr('data-id')])
	})
	})
	function delete_project($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_project',
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
