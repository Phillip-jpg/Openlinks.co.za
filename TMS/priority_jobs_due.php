<?php include'db_connect.php' ?>
<div class="col-lg-12">
		<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white">
	
          
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_job"><i class="fa fa-plus"></i> Add New Job</a>
			</div>
           
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
				<col width="5%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
				</colgroup>
				<thead style="background-color:#032033 !important; color:white">
					<tr>
						<th >Job_ID</th>
						<th>Job</th>
						<th>Job Type</th>
							<th>Team Name</th>
						<th>Project Manager</th>
						<th>Team Leader</th>
						<th>Member</th>
					    <th>Job Done at Activity Level</th>
						<th>Job Start Date</th>
						<th>Job End Date</th>
						<!--<th>Assigned</th>-->
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
                    
                   
                  
                        // $qry = $conn->query("SELECT project_list.*
                        // FROM project_list WHERE manager_id = {$_SESSION['login_id']} ORDER BY date_created desc");
                        // while ($row = $qry->fetch_assoc()):
                        
                        $pm_manager=$_SESSION['login_id'];
                    
                            $qry = $conn->query("SELECT DISTINCT
                                pl.name AS jobname,
                                ad.request_days, 
                                ad.request_done, 
                                ad.project_id, 
                                tl.task_name, 
                                pl.id,
                                pl.assigned,
                                pl.date_created,
                                pl.start_date as job_start_date,
                                pl.end_date AS job_due,
                                pl.JOB_TYPE,
                                ad.activity_id, 
                                up.name, 
                                CONCAT(u.firstname, ' ', u.lastname) AS manager,
                                CONCAT(u2.firstname, ' ', u2.lastname) AS member,
                                ad.duration, 
                                ad.start_date,
                                ad.done_date, 
                                ad.end_date, 
                                MONTHNAME(ad.end_date) AS month,
                                c.company_name, 
                                ad.days_left, 
                                pl.status,
                                ts.team_name,
                                CONCAT(u1.firstname, ' ', u1.lastname) AS ops_manager
                            FROM 
                                assigned_duties ad
                            JOIN 
                                user_productivity up ON ad.activity_id = up.id
                            JOIN 
                                task_list tl ON tl.id = up.task_id
                            JOIN 
                                project_list pl ON pl.id = ad.project_id
                            JOIN 
                                users u ON pl.manager_id = u.id
                            JOIN 
                                users u2 ON ad.user_id = u2.id
                            JOIN 
                                yasccoza_openlink_market.client c ON pl.client_id = c.client_id
                            LEFT JOIN 
                                team_schedule ts ON pl.team_ids = ts.team_id
                            LEFT JOIN 
                                users u1 ON ts.op_ids = u1.id
                            WHERE 
                                pl.manager_id = $pm_manager
                                AND pl.status != 'Done'
                                AND pl.end_date BETWEEN CURRENT_DATE AND CURRENT_DATE + INTERVAL 3 DAY
                            ORDER BY 
                                pl.id;
                        ");
                        
        
 while ($row = $qry->fetch_assoc()):
    // Your code inside the loop goes here
					
					   $words = explode(' ', $row['name']);
                            
                            // If there are at least two words, display them followed by '...'
                            $shortenedJobName = '';
                            if (count($words) >= 2) {
                                $shortenedJobName = implode(' ', array_slice($words, 0, 9)) . '...';
                            } else {
                                // If there are fewer than two words, just display the original content
                                $shortenedJobName = $row['name'];
                            }
		                //$prod = $conn->query("SELECT * FROM user_productivity where project_id = {$row['id']}")->num_rows;
						
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
							<p><b><?php echo ucwords($row['manager']) ?></b></p>
							
						</td>
						
							<td>
							<p><b><?php echo ucwords($row['ops_manager']) ?></b></p>
							
						</td>
						
							<td>
							<p><b><?php echo ucwords($row['member']) ?></b></p>
							
						</td>
					
						
							
							<?php if ($row['done_date']!= null){
 							echo "<td><p style='font-weight:bold; color:green'>Yes</p></td>";
						}else {
							echo "<td><p style='font-weight:bold; color:red'>No</p></td>";
						}
						   
						?>
							

                        <td>
							<p><b><?php echo ucwords($row['job_start_date']) ?></b></p>
							
							
							
						</td>
						<td>
							<p><b><?php echo ucwords($row['job_due']) ?></b></p>
							
						</td>
  
					
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
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" >
		                      <a class="dropdown-item view_project" href="./index.php?page=priority_jobs_due1&id=<?php echo $row['id'] ?>" data-id="<?php echo $row['id'] ?>">View</a>
						
		                      <?php if ($_SESSION['login_type'] == 2 || $_SESSION['login_type'] == 3): ?>
								<!--<a class="dropdown-item view_project" href="./index.php?page=assign_duties&id=<?php echo $row['id'] ?>" data-id="<?php echo $row['id'] ?>">Assign</a>-->
								<?php endif; ?>
							<?php if($_SESSION['login_type'] ==2): ?>
		                      <!--<a class="dropdown-item" href="./index.php?page=edit_job&id=<?php echo $row['id'] ?>">Edit</a>-->
		                      
		                      
		                      
		                      <!--<a  stlye="color:green"class="dropdown-item" href="./index.php?page=save_done_request&id=<?php echo $row['id'] ?>">Grant Done</a>-->
		                    
		                      <!--<a class="dropdown-item delete_project" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>-->
		                  <?php endif; ?>
		                    </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				
				</tbody>
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
    var dataTable = $('#list').DataTable();

    // Event listener for each filter dropdown
    $('#jobtype-filter').change(function(){
        filterTable();
    });
    
    $('#month-filter').change(function(){
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
        var selectedCreator = $('#created-filter').val();
        var selectedAssigned = $('#assigned-filter').val();
        var selectedStatus = $('#status-filter').val();

        // Apply filter for each column:
        dataTable.column(2).search(selectedJobType) 
         dataTable.column(3).search(selectedmonth) /// Job Type filter on 3rd column (index 2)
            .column(4).search(selectedCreator)      // Who Created it filter on 5th column (index 4)
            .column(5).search(selectedAssigned)     // Assigned filter on 6th column (index 5)
            .column(6).search(selectedStatus)       // Status filter on 7th column (index 6)
            .draw();  // Redraw the table with the new filters
    }

    // Handle deletion of projects

});


	$(document).ready(function(){
		$('#list').dataTable()
	
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

