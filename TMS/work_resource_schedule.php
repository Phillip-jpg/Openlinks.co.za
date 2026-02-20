<?php include'db_connect.php' ?>

<div class="col-lg-12">
	<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white">
			<h4 class="card-title">Job List</h4>
            <?php if($_SESSION['login_type'] != 3): ?>
			<div class="card-tools">
				<a class="btn btn-sm btn-light border-primary" href="./index.php?page=new_job">
					<i class="fa fa-plus"></i> Add New Job
				</a>
			</div>
            <?php endif; ?>
		</div>
		<div class="card-body">
		    <div class="form-row mb-3">
		         <div class="col-md-3">
                <label for="jobtype-filter">Filter by Month:</label>
                <select id="jobtype-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $job_qry = $conn->query("SELECT DISTINCT JOB_TYPE
FROM project_list pl
WHERE JOB_TYPE IS NOT NULL AND JOB_TYPE != '';");
                    while($job_row = $job_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $job_row['JOB_TYPE']; ?>"><?php echo $job_row['JOB_TYPE']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
             <div class="col-md-3">
                <label for="jobtype-filter">Filter by Week:</label>
                <select id="jobtype-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $job_qry = $conn->query("SELECT DISTINCT JOB_TYPE
FROM project_list pl
WHERE JOB_TYPE IS NOT NULL AND JOB_TYPE != '';");
                    while($job_row = $job_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $job_row['JOB_TYPE']; ?>"><?php echo $job_row['JOB_TYPE']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
		    <div class="col-md-3">
                <label for="jobtype-filter">Filter by Team:</label>
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
                <label for="created-filter">Filter by Member:</label>
                <select id="created-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                        $work_qry = $conn->query("SELECT 
                                wwp.Month, 
                                wwp.start_week AS start_date, 
                                wwp.end_week AS end_date,
                                wwp.period, 
                                CONCAT(u.firstname, ' ', u.lastname) AS PM, 
                                ts.Team_Name
                            FROM 
                                working_week_periods wwp
                            LEFT JOIN 
                                schedule_work_team swt ON swt.Periods = wwp.period
                            LEFT JOIN 
                                team_schedule ts ON ts.team_id = swt.Work_Team
                            LEFT JOIN 
                                users u ON u.id = ts.pm_manager
                            WHERE 
                                wwp.period IS NOT NULL 
                                AND ts.Team_Name IS NOT NULL 
                                AND u.id IS NOT NULL
                            ORDER BY 
                                wwp.period ASC;
                            ");
                                                while($row = $work_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $creator_row['P_name']; ?>"><?php echo $creator_row['P_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
         
          
         
               	</div>
               	<div class="table-responsive">
			<table class="table table-hover table-bordered table-condensed" id="list">
			     <br>
				<colgroup>
					<col width="10%">
					<col width="15%">
					<col width="15%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead style="background-color:#032033 !important; color:white">
					<tr>
						<th>Schedule_ID</th>
						<th>Month</th>
						<th>Start Date Cycles</th>
						<th>End Date Cycles</th>
						<th>Week</th>
						<th>PM</th>
					    <th>Team_Name</th>
						<th>Members</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$work_qry = $conn->query("SELECT 
                                wwp.Month, 
                                wwp.start_week AS start_date, 
                                wwp.end_week AS end_date,
                                wwp.period, 
                                CONCAT(u.firstname, ' ', u.lastname) AS PM, 
                                ts.Team_Name
                            FROM 
                                working_week_periods wwp
                            LEFT JOIN 
                                schedule_work_team swt ON swt.Periods = wwp.period
                            LEFT JOIN 
                                team_schedule ts ON ts.team_id = swt.Work_Team
                            LEFT JOIN 
                                users u ON u.id = ts.pm_manager
                            WHERE 
                                wwp.period IS NOT NULL 
                                AND ts.Team_Name IS NOT NULL 
                                AND u.id IS NOT NULL
                            ORDER BY 
                                wwp.period ASC;
                            ");
                   
					while($row=$work_qry->fetch_assoc()):
			
					?>
					<tr>
					    <td><p><b>Number</b></p></td>
						<td><p><b><?php echo ucwords($row['Month']) ?></b></p></td>
						<td><p><b><?php echo ucwords($row['start_date']) ?></b></p></td>
						<td><p><b><?php echo ucwords($row['end_date']) ?></b></p></td>
						<td><p><b><?php echo ucwords($row['period']) ?></b></p>
							</td>
													<td><p><b><?php echo ucwords($row['PM']) ?></b></p>
							</td>
						<td><p><b><?php echo ucwords($row['Team_Name']) ?></b></p>
							</td>
							<td><p><b>Members</b></p>
							</td>
					
			
					

						<!-- Action Dropdown -->
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
								Action
							</button>
							<div class="dropdown-menu">
								<a type="button" class="dropdown-item view_project" href="./index.php?page=view_job&id=<?php echo $row['id'] ?>" data-id="<?php echo $row['id'] ?>">View</a>
								<?php if($_SESSION['login_type'] ==1): ?>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item view_project" href="./index.php?page=assign_duties&id=<?php echo $row['id'] ?>" data-id="<?php echo $row['id'] ?>">Assign</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="./index.php?page=edit_job&id=<?php echo $row['id'] ?>">Edit</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item delete_project" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
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
<!-- Custom CSS -->
<style>
    .table-responsive {
        overflow-x: auto;
    }
    table p {
        margin: unset !important;
    }
    table td, table th {
        vertical-align: middle !important;
    }
    .card-header {
        background-color: #007bff;
        color: white;
    }
    .table-hover tbody tr:hover {
        background-color: #f9f9f9;
    }
    .badge {
        font-size: 0.875rem;
    }
    .btn-default {
        background-color: white;
        border-color: #ddd;
    }
</style>

<!-- Script to initialize DataTables and handle deletion -->
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
         dataTable.column(3).search(selectedmonth) // Job Type filter on 3rd column
            .column(5).search(selectedCreator)      // Creator filter on 6th column
            .column(7).search(selectedAssigned)     // Assigned filter on 8th column
            .column(8).search(selectedStatus)       // Status filter on 9th column
            .draw();  // Redraw the table with the new filters
    }

    // Handle deletion of projects
    $('.delete_project').click(function(){
        _conf("Are you sure to delete this job?", "delete_project", [$(this).attr('data-id')]);
    });
});

// Function to delete the project with confirmation
function delete_project(id){
    start_load();
    $.ajax({
        url: 'ajax.php?action=delete_project',
        method: 'POST',
        data: {id: id},
        success: function(resp) {
            if (resp == 1) {
                alert_toast("Data successfully deleted", 'success');
                setTimeout(function(){
                    location.reload();
                }, 1500);
            }
        }
    });
}

</script>
