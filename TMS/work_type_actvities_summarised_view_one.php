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
				<col width=10%">
				<col width="10%">
				<col width="10%">
				<col width="10%">
				<col width="10%">
				</colgroup>
				<thead style="background-color:#032033 !important; color:white">
					<tr>
						<th >Activities</th>
						<th>Sum per Activity</th>
						<th>Done</th>
						<th>WIP</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					 
					 $team_id=$_GET['team_id'];
					 $task_id=$_GET['task_id'];
					
                            $qry = $conn->query("SELECT
                                sub.upid,
                                sub.upname,
                                COUNT(*) AS total_count,
                                COUNT(CASE WHEN sub.done IS NOT NULL THEN 1 END) AS done_count,
                                COUNT(CASE WHEN sub.done IS NULL THEN 1 END) AS wip_count
                            FROM (
                                SELECT DISTINCT
                                    ad.project_id,
                                    ad.user_id,
                                    up.id AS upid,
                                    ad.Done_Date AS done,
                                    up.name AS upname,
                                 	tl.start_time,
                                 	tl.end_time,
                                	pl.Job_Done
                                FROM
                                    project_list pl
                                LEFT JOIN assigned_duties ad ON
                                    pl.id = ad.project_id
                                LEFT JOIN task_list tl ON
                                    tl.id = ad.task_id
                                LEFT JOIN user_productivity up ON
                                    up.id = ad.activity_id
                                WHERE
                                    pl.team_ids = $team_id
                                    AND tl.id = $task_id
                            ) AS sub
                            GROUP BY
                                sub.upid
                            ORDER BY
                                total_count DESC;
                        ");
					while ($row = $qry->fetch_assoc()):
					
					?>
					<tr>
						<td>
							<p><?php echo ucwords($row['upname']) ?></p>
						</td>
						<td>
							<p><?php echo ucwords($row['total_count']) ?></p>
						</td>
						<td>
							<p><?php echo ucwords($row['done_count']) ?></p>
						</td>
						<td>
							<p><?php echo ucwords($row['wip_count']) ?></p>
						</td>
						
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" >
		                      <a class="dropdown-item view_project" href="./index.php?page=work_type_actvities_summarised_view_two&task_id=<?php echo $task_id ?>&team_id=<?php echo $team_id ?>&activity_id=<?php echo ucwords($row['upid']) ?>">View</a>
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