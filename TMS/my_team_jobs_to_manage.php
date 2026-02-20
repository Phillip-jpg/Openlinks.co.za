<?php include'db_connect.php' ?>
<div class="col-lg-12">
		<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white">
	
          
			<div class="card-tools">
				
			</div>
           
		</div>
		<div class="card-body">
		   
             <br>
             	<div class="table-responsive">
				<table class="table tabe-hover table-condensed" id="list">
			<colgroup>
				<col width="5%">
					<col width="20%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead style="background-color:#032033 !important; color:white">
					<tr>
						<th >Team_ID</th>
						<th>Team Names</th>
						<th>Total Assigned</th>
						<th>In progress</th>
						<th>Completed</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					
					    $total_assigned=0;
					    $total_inprogress=0;
					    $total_completed=0;
					    
					     $pm_id=$_SESSION['login_id'];
                           
                            $qry = $conn->query("
                          SELECT
                            pl.team_ids,
                            pl.manager_id,
                            (
                                SELECT ts.team_name
                                FROM team_schedule ts
                                WHERE ts.team_id = pl.team_ids
                                ORDER BY ts.id ASC
                                LIMIT 1
                            ) AS team_name,
                            SUM(CASE WHEN pl.status = 'In-progress' THEN 1 ELSE 0 END) AS Inprogress,
                            SUM(CASE WHEN pl.status = 'Done' THEN 1 ELSE 0 END) AS Done,
                            COUNT(pl.id) AS assigned
                        FROM
                            project_list pl
                        WHERE EXISTS (
                            SELECT 1 FROM team_schedule ts WHERE ts.team_id = pl.team_ids
                        )
                        AND pl.manager_id=$pm_id
                        GROUP BY
                            pl.team_ids, pl.manager_id
                        ORDER BY
                            pl.team_ids ASC;
                        ");
                     while ($row = $qry->fetch_assoc()):
                         
                         $total_assigned+=$row['assigned'];
                         $total_completed+=$row['Done'];
                         $total_inprogress+=$row['Inprogress'];
					?>
					<tr>
						<td>
							<p><?php echo ucwords($row['team_ids']) ?></p>
						</td>
							<td>
							<p><?php echo ucwords($row['team_name']) ?></p>
						</td>
                        <td>
							<p><?php echo ucwords($row['assigned']) ?></p>
						</td>
						<td>
							<p><?php echo ucwords($row['Inprogress']) ?></p>
						</td>
						<td>
							<p><?php echo ucwords($row['Done']) ?></p>
						</td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" >
		                      <a class="dropdown-item view_project" href="./index.php?page=my_team_jobs_to_manage_lvl_1&team_id=<?php echo $row['team_ids'] ?>" data-id="<?php echo $row['team_ids'] ?>">View Jobs</a>
		                      <hr>
						    <a class="dropdown-item view_project" href="./index.php?page=team_efficiency_summary&team_id=<?php echo $row['team_ids'] ?>">Team Efficiency Summary </a>
						    <hr>
						    <a class="dropdown-item view_project" href="./index.php?page=team_ledger&team_id=<?php echo $row['team_ids'] ?>">Team ledger</a>
		                    </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				
				</tbody>
				
				 <div class="container mt-4">
                    <div class="card">
                   
                        <div class="card-header bg-success text-white">
                            <h2>Job Management Summary PM</h2>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    
                                    <p style="font-weight:bold"><b>Total Jobs Assigned: </b><?php echo $total_assigned ?></p>
                                </div>
                                 <div class="col-md-6">
                                    <p style="font-weight:bold" >Total Jobs Completed: <span style="color:green"><?php echo $total_completed ?></span>
                                <span><br></span></p>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                    <p style="font-weight:bold" >Total Jobs In progress: <span style="color:red"><?php echo $total_inprogress ?></span></p>
                                </div>
                            </div>
                        </div>
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