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
					
					
                           
                            $qry = $conn->query("
                            SELECT 
                                pl.team_ids,	
                                pl.manager_id,
                                ts_min.team_id,
                                ts_min.team_name,
                                ts_min.team_members,
                                SUM(CASE WHEN pl.status = 'In-progress' THEN 1 ELSE 0 END) AS Inprogress,
                                SUM(CASE WHEN pl.status = 'Done' THEN 1 ELSE 0 END) AS Done,
                                COUNT(pl.id) AS assigned
                            FROM 
                                project_list pl
                           JOIN 
                                (SELECT DISTINCT team_id, team_name,team_members FROM team_schedule WHERE team_members={$_SESSION['login_id']} ) ts_min
                                ON pl.team_ids = ts_min.team_id
                            GROUP BY 
                                pl.team_ids, pl.manager_id, ts_min.team_id, ts_min.team_name
                            ORDER BY 
                                pl.team_ids ASC;
                        ");
                     while ($row = $qry->fetch_assoc()):
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
							<?php
							$teamPayload = (string)((int)$row['team_ids']);
							$teamHash = hash_hmac('sha256', $teamPayload, 'my_app_secret_key');
							$teamRef = urlencode(base64_encode($teamPayload . '|' . $teamHash));
							?>
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" >
		                      <a class="dropdown-item view_project" href="./index.php?page=jobs_to_manage_level1&team_id=<?php echo $teamRef ?>" data-id="<?php echo $row['team_ids'] ?>">View Jobs</a>
		                      <hr>
						    <a class="dropdown-item view_project" href="./index.php?page=team_efficiency_summary&team_id=<?php echo $row['team_id'] ?>">Team Efficiency Summary </a>
						    <hr>
						    <a class="dropdown-item view_project" href="./index.php?page=team_ledger&team_id=<?php echo $row['team_id'] ?>">Team ledger</a>
		                    </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				
				</tbody>
				 <div class="container mt-4">
                    <div class="card">
                         <?php
                           
                    $qry1 = $conn->query("
                       SELECT 
                            CONCAT(u.firstname, ' ', u.lastname) AS Name,
                            COUNT(ad.user_id) AS assigned_duties_count,
                            COUNT(DISTINCT CASE WHEN pl.team_ids <> 0 THEN pl.team_ids END) AS number_of_teams_in,
                            GROUP_CONCAT(DISTINCT ts_min.team_name) AS team_ids_list,
                            SUM(CASE WHEN pl.status = 'In-progress' THEN 1 ELSE 0 END) AS In_progress,
                            SUM(CASE WHEN pl.status = 'Done' THEN 1 ELSE 0 END) AS Done
                        FROM assigned_duties ad
                        LEFT JOIN project_list pl ON pl.id = ad.project_id
                        LEFT JOIN users u ON u.id = ad.user_id
                        LEFT JOIN 
                        (SELECT DISTINCT team_id,team_name FROM team_schedule WHERE team_members={$_SESSION['login_id']} ) ts_min
                                                        ON pl.team_ids = ts_min.team_id
                        WHERE ad.user_id = {$_SESSION['login_id']}
                        AND pl.team_ids !=0;
                        ");
                     while ($row1 = $qry1->fetch_assoc()):
					?>
                        <div class="card-header bg-success text-white">
                            <h2>Job Management Summary: <?php echo $row1['Name'] ?></h2>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    
                                    <p style="font-weight:bold"><b>Total Jobs I am assigned to: </b><?php echo $row1['assigned_duties_count']; ?></p>
                                </div>
                                 <div class="col-md-6">
                                    <p style="font-weight:bold" >Number of Teams where I am assigned a Job: <span style=""><?php echo $row1['number_of_teams_in'] ?></span>
                                <span><br><?php echo $row1['team_ids_list']?></span></p>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                    <p style="font-weight:bold" >Number of Jobs In progress: <span style="color:red"><?php echo $row1['In_progress'] ?></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p style="font-weight:bold" >Number of Jobs Done: <span style="color:green"><?php echo $row1['Done'] ?></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
                
                 <!--<?php if ($_SESSION['login_type'] == 2 ): ?>-->
                	<!-- <p style="margin-left:300px; background-color:#007bff; width:400px; height:25px; text-align:center; color:white; border-radius:10px; line-height:25px;">-->
                 <!--         <a href="./index.php?page=priority_jobs_done" -->
                 <!--            style="color:white; text-decoration:none;">-->
                 <!--           Prority Jobs: Jobs Done-->
                 <!--         </a>-->
                 <!--       </p>-->
                        
                 <!--       	 <p style="margin-left:300px; background-color:#007bff; width:400px; height:25px; text-align:center; color:white; border-radius:10px; line-height:25px;">-->
                 <!--         <a href="./index.php?page=priority_jobs_due" -->
                 <!--            style="color:white; text-decoration:none;">-->
                 <!--           Prority Jobs: Jobs Due Soon-->
                 <!--         </a>-->
                 <!--       </p>-->
                 <!--   <?php endif; ?>-->
                
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
