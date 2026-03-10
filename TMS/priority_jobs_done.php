<?php include'db_connect.php' ?>
<div class="col-lg-12 priority-done-modern">
		<div class="card card-outline card-success shadow-sm priority-done-card">
		<div class="card-header bg-primary text-white priority-done-header">
	
          
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary add-job-btn" href="./index.php?page=new_job"><i class="fa fa-plus"></i> Add New Job</a>
			</div>
           
		</div>
		<div class="card-body">
		    <div class="form-row mb-3 priority-done-filters">
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
				<table class="table tabe-hover table-condensed priority-done-table" id="list">
			<colgroup>
				<col width="5%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th >Job_ID</th>
						<th>Job</th>
						<th>Job Type</th>
						<th>Team Name</th>
						<th>Project Manager</th>
						<th>Team Leader</th>
					    <th>Job Done at Activities Level</th>
					       <!--<th>No of Members</th>-->
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
                                        tl.task_name, 
                                        pl.id,
                                        pl.name,
                                        COUNT(ad.project_id) AS total_assignments,
                                        pl.assigned,
                                        pl.date_created,
                                        pl.manager_id,
                                        ad.team_id,
                                        pl.start_date AS job_start_date,
                                        pl.end_date AS job_due,
                                        pl.JOB_TYPE,
                                        CONCAT(u.firstname, ' ', u.lastname) AS manager,
                                        MONTHNAME(pl.end_date) AS month,
                                        c.company_name, 
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
                                        yasccoza_openlink_market.client c ON pl.client_id = c.client_id
                                    LEFT JOIN 
                                        team_schedule ts ON ts.team_id = ad.team_id
                                    LEFT JOIN 
                                        users u1 ON ts.op_ids = u1.id
                                    WHERE 
                                        pl.manager_id = $pm_manager
                                        AND pl.status != 'Done'
                                        AND pl.assigned=1
                                        AND ad.project_id IN (
                                            SELECT ad2.project_id
                                            FROM assigned_duties ad2
                                            GROUP BY ad2.project_id
                                            HAVING COUNT(*) = COUNT(ad2.done_date)
                                        )
                                    GROUP BY 
                                        pl.id
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
						<th class="text-center job-id-cell"><?php echo ($row['id']) ?></th>
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
							<p class="activity-level-done"><b>Yes</b></p>
							
						</td>
					
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
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle action-btn" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu action-menu" >
		                      <a class="dropdown-item view_project" href="./index.php?page=view_job&id=<?php echo $row['id'] ?>" data-id="<?php echo $row['id'] ?>">View</a>
						
		                      <?php if ($_SESSION['login_type'] == 2 || $_SESSION['login_type'] == 3): ?>
								<!--<a class="dropdown-item view_project" href="./index.php?page=assign_duties&id=<?php echo $row['id'] ?>" data-id="<?php echo $row['id'] ?>">Assign</a>-->
								<?php endif; ?>
							<?php if($_SESSION['login_type'] ==2): ?>
		                      <!--<a class="dropdown-item" href="./index.php?page=edit_job&id=<?php echo $row['id'] ?>">Edit</a>-->
		                      
		                      <hr>
		                      
		                      <a class="dropdown-item grant-done-link grant-done-btn" href="./index.php?page=save_done_request&id=<?php echo $row['id'] ?>&manager_id=<?php echo $row['manager_id'] ?>&team_id=<?php echo $row['team_id'] ?>">Grant Done</a>
		                    
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
	.priority-done-modern {
		--surface: #ffffff;
		--ink: #0f172a;
		--muted: #64748b;
		--line: #dbe7f5;
		--brand-1: #0f4c81;
		--brand-2: #0b7db5;
		--brand-3: #5eb3f3;
	}

	.priority-done-modern .priority-done-card {
		border: 1px solid var(--line);
		border-radius: 18px;
		box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
		background: var(--surface);
		overflow: hidden;
	}

	.priority-done-modern .priority-done-header {
		background: linear-gradient(120deg, #0f172a 0%, #1e3a5f 45%, #2563eb 100%);
		border: 0;
		padding: 0.86rem 1rem;
	}

	.priority-done-modern .add-job-btn {
		background: linear-gradient(125deg, var(--brand-1), var(--brand-2));
		border: 0 !important;
		border-radius: 999px;
		box-shadow: 0 8px 18px rgba(11, 125, 181, 0.28);
		color: #fff !important;
		font-size: 0.78rem;
		font-weight: 600;
		padding: 0.42rem 0.95rem;
	}

	.priority-done-modern .add-job-btn:hover {
		transform: translateY(-1px);
		color: #fff !important;
	}

	.priority-done-modern .card-body {
		padding: 1rem 1rem 0.9rem;
	}

	.priority-done-modern .priority-done-filters {
		background: #f8fbff;
		border: 1px solid var(--line);
		border-radius: 14px;
		margin-bottom: 0.9rem;
		padding: 0.8rem 0.65rem 0.2rem;
	}

	.priority-done-modern .priority-done-filters label {
		color: #1e3a5f;
		font-size: 0.73rem;
		font-weight: 600;
		letter-spacing: 0.05em;
		margin-bottom: 0.28rem;
		text-transform: uppercase;
	}

	.priority-done-modern .priority-done-filters .form-control {
		border: 1px solid #c9dcf3;
		border-radius: 10px;
		color: #334155;
		font-size: 0.82rem;
		height: calc(2rem + 2px);
		padding: 0.28rem 0.6rem;
	}

	.priority-done-modern .priority-done-filters .form-control:focus {
		border-color: #93c5fd;
		box-shadow: 0 0 0 0.17rem rgba(96, 165, 250, 0.16);
	}

	.priority-done-modern .table-responsive {
		border: 1px solid var(--line);
		border-radius: 14px;
		overflow-x: auto;
		overflow-y: visible;
	}

	.priority-done-modern .priority-done-table {
		margin: 0;
	}

	.priority-done-modern .priority-done-table thead th {
		background: #0f172a;
		border: 0;
		color: #dbeafe;
		font-size: 0.71rem;
		font-weight: 600;
		letter-spacing: 0.05em;
		padding: 0.66rem 0.48rem;
		text-transform: uppercase;
		white-space: nowrap;
	}

	.priority-done-modern .priority-done-table tbody td,
	.priority-done-modern .priority-done-table tbody th {
		border-top: 1px solid #edf2f7;
		color: #334155;
		font-size: 0.8rem;
		padding: 0.56rem 0.48rem;
		vertical-align: middle !important;
	}

	.priority-done-modern .priority-done-table tbody tr:hover {
		background: #f8fafc;
	}

	.priority-done-modern .priority-done-table p {
		margin: unset !important;
	}

	.priority-done-modern .job-id-cell {
		color: var(--brand-2) !important;
		font-weight: 700;
	}

	.priority-done-modern .activity-level-done {
		color: #047857 !important;
		font-weight: 700;
	}

	.priority-done-modern .badge {
		border-radius: 999px;
		font-size: 0.69rem;
		font-weight: 600;
		letter-spacing: 0.02em;
		padding: 0.37em 0.66em;
	}

	.priority-done-modern .action-btn {
		background: #ffffff;
		border: 1px solid #bfd8f8 !important;
		border-radius: 999px;
		color: #0f4c81 !important;
		font-size: 0.72rem;
		font-weight: 600;
		padding: 0.3rem 0.82rem;
	}

	.priority-done-modern .action-btn:hover {
		background: #eff6ff;
		border-color: #93c5fd !important;
		color: #1d4ed8 !important;
	}

	.priority-done-modern .action-menu {
		border: 1px solid #d8e6f7;
		border-radius: 12px;
		box-shadow: 0 12px 28px rgba(15, 23, 42, 0.14);
		padding: 0.25rem;
	}

	.priority-done-modern .action-menu .dropdown-item {
		border-radius: 8px;
		color: #334155;
		font-size: 0.8rem;
		padding: 0.45rem 0.6rem;
	}

	.priority-done-modern .action-menu .dropdown-item:hover {
		background: #eff6ff;
		color: #0f4c81;
	}

	.priority-done-modern .grant-done-btn {
		color: #0f9f6e !important;
		font-weight: 700;
	}

	.priority-done-modern .grant-done-btn:hover {
		background: #ecfdf3 !important;
		color: #047857 !important;
	}

	.priority-done-modern .dataTables_wrapper .dataTables_length label,
	.priority-done-modern .dataTables_wrapper .dataTables_filter label,
	.priority-done-modern .dataTables_wrapper .dataTables_info,
	.priority-done-modern .dataTables_wrapper .dataTables_paginate {
		color: #64748b;
		font-size: 0.78rem;
	}

	.priority-done-modern .dataTables_wrapper .dataTables_filter input,
	.priority-done-modern .dataTables_wrapper .dataTables_length select {
		border: 1px solid #c9dcf3;
		border-radius: 8px;
		color: #334155;
		font-size: 0.78rem;
		padding: 0.2rem 0.45rem;
	}

	@media (max-width: 768px) {
		.priority-done-modern .priority-done-filters {
			padding: 0.7rem 0.52rem 0.12rem;
		}

		.priority-done-modern .priority-done-table thead th,
		.priority-done-modern .priority-done-table tbody td,
		.priority-done-modern .priority-done-table tbody th {
			font-size: 0.74rem;
			padding: 0.48rem 0.36rem;
		}
	}

	/* Readability overrides */
	.priority-done-modern {
		font-size: 0.98rem;
	}

	.priority-done-modern .priority-done-filters label {
		font-size: 0.82rem;
	}

	.priority-done-modern .priority-done-filters .form-control {
		font-size: 0.92rem;
	}

	.priority-done-modern .priority-done-table thead th {
		font-size: 0.8rem;
	}

	.priority-done-modern .priority-done-table tbody td,
	.priority-done-modern .priority-done-table tbody th {
		font-size: 0.9rem;
	}

	.priority-done-modern .badge,
	.priority-done-modern .action-btn,
	.priority-done-modern .action-menu .dropdown-item {
		font-size: 0.82rem;
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

	$('.grant-done-link').on('click', function(e){
		if(!confirm('Are you sure you want to Grant Done for this job?')){
			e.preventDefault();
		}
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

