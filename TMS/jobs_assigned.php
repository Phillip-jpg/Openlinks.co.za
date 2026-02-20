<?php include'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white">
	
        
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_job"><i class="fa fa-plus"></i> Add New Job</a>
			</div>
        
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-condensed" id="list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="15%">
                    <col width="25%">
					<col width="25%">
					<col width="25%">
					<col width="25%">
					<col width="25%">
				</colgroup>
				<thead>
					<tr>
					    <thead style="background-color:#032033 !important; color:white">
						<th class="text-center">Job ID</th>
						<th>Job</th>
						<th>Job Type</th>
                        <th>Date Created</th>
						<th>Date Started</th>
						<th>Due Date</th>
						<th>Assigned</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
                    
                   
                        $i = 1;
					
						$qry = $conn->query("SELECT DISTINCT project_list.*
						FROM project_list
						INNER JOIN assigned_duties ON project_list.id = assigned_duties.project_id
						WHERE assigned_duties.user_id = {$_SESSION['login_id']} ORDER BY date_created DESC");
						while ($row = $qry->fetch_assoc()):
		// Your code inside the loop goes here

	 $words = explode(' ', $row['name']);
                            
                            // If there are at least two words, display them followed by '...'
                            $shortenedJobName = '';
                            if (count($words) >= 2) {
                                $shortenedJobName = implode(' ', array_slice($words, 0, 5)) . '...';
                            } else {
                                // If there are fewer than two words, just display the original content
                                $shortenedJobName = $row['name'];
                            }
                    
    // Your code inside the loop goes here
					
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
							<p><b><?php echo ucwords($row['date_created']) ?></b></p>
							
						</td>
						<td><b><?php echo date("M d, Y",strtotime($row['start_date'])) ?></b></td>
						<td><b><?php echo date("M d, Y",strtotime($row['end_date'])) ?></b></td>
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
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" >
		                      <a class="dropdown-item view_project" href="./index.php?page=view_job&id=<?php echo $row['id'] ?>" data-id="<?php echo $row['id'] ?>">View</a>
						
		                      <?php if($_SESSION['login_type'] ==2): ?>
								<a class="dropdown-item view_project" href="./index.php?page=assign_duties&id=<?php echo $row['id'] ?>" data-id="<?php echo $row['id'] ?>">Assign</a>
		                      <a class="dropdown-item" href="./index.php?page=edit_job&id=<?php echo $row['id'] ?>">Edit</a>
		                    
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