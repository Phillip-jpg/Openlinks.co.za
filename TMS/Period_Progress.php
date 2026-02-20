<?php include'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
	
            <?php if($_SESSION['login_type'] != 3): ?>
			<div class="card-tools" style="float:left">
				<?php
				
$period = intval($_GET['p']);

$where = intval($_GET['w']);

if($where==1){
    
echo '<p style="font-weight:bold">Jobs created this week</p>';

    
}elseif($where==2){
    
     echo '<p style="font-weight:bold">Jobs due this week</p>';
     
}elseif($where==3){
    
     echo '<p style="font-weight:bold">Jobs done this week</p>';
}

$qry = $conn->query("SELECT start_week, end_week, period
                     FROM working_week_periods WHERE period = $period");

// Check if the query executed successfully
if ($qry) {
    // Check if there are rows returned
    if ($qry->num_rows > 0) {
        // Fetch each row
        while ($row = $qry->fetch_assoc()) {
            // Display the values of start_week and end_week
            echo 'Week: ' . ucwords($row['period']) . ' <p style="color:red;">' . ucwords($row['start_week']) . ' - ' . ucwords($row['end_week']) . '</p>';



            
            // If you only want to display the first result, you can break the loop here
            // break;
        }
    } else {
        echo 'No results found.';
    }
} else {
    echo 'Error executing query: ' . $conn->error;
}
?>

			</div>
            <?php endif; ?>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-condensed" id="list">
				<colgroup>
				<col width="10%">
					<col width="45%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>Job_ID</th>
						<th>Job</th>
						<th>Job Type</th>
						<th>Date Created</th>
					    <th>Who Created it</th>
						<th>Assigned</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$stat = array("Pending","Started","On-Progress","On-Hold","Over Due","Done");
					$where = "";
					if($_SESSION['login_type'] == 2){
						$where = " where manager_id = '{$_SESSION['login_id']}' ";
					}elseif($_SESSION['login_type'] == 3){
					    
					    
						$where = " where concat('[',REPLACE(user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
					}
					                 if ($_GET['w'] == 1) {
    // Sanitize the input before using it in the query to prevent SQL injection
                                    $period = intval($_GET['p']);
                                
                                    // Construct the SQL query
                                    $qry = $conn->query("SELECT CONCAT(u.firstname, ' ', u.lastname) AS c_name, pl.*, wwp.start_week, wwp.end_week
                                                         FROM project_list pl
                                                         LEFT JOIN users u ON pl.Creator_ID = u.id
                                                         LEFT JOIN working_week_periods wwp ON pl.date_created BETWEEN wwp.start_week AND wwp.end_week
                                                         WHERE wwp.period = $period
                                                         ORDER BY pl.assigned DESC");
                        }elseif($_GET['w'] == 3){
                            
                            $period = intval($_GET['p']);
                                
                                    // Construct the SQL query
                                    $qry = $conn->query("SELECT CONCAT(u.firstname, ' ', u.lastname) AS c_name, pl.*, wwp.start_week, wwp.end_week
                                                         FROM project_list pl
                                                         LEFT JOIN users u ON pl.Creator_ID = u.id
                                                         LEFT JOIN working_week_periods wwp ON pl.Job_Done BETWEEN wwp.start_week AND wwp.end_week
                                                         WHERE wwp.period = $period
                                                         ORDER BY pl.assigned DESC");
                        }
                            elseif($_GET['w'] == 2){
                                
                                $period = intval($_GET['p']);
                                
                                    // Construct the SQL query
                                    $qry = $conn->query("SELECT CONCAT(u.firstname, ' ', u.lastname) AS c_name, pl.*, wwp.start_week, wwp.end_week
                                                         FROM project_list pl
                                                         LEFT JOIN users u ON pl.Creator_ID = u.id
                                                         LEFT JOIN working_week_periods wwp ON pl.end_date BETWEEN wwp.start_week AND wwp.end_week
                                                         WHERE wwp.period = $period
                                                         ORDER BY pl.assigned DESC");
                            }
                        					
					while($row= $qry->fetch_assoc()):
					    
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
							<p><b><?php echo ucwords($row['date_created']) ?></b></p>
							
						</td>
                            <td>
                                <p><b>
                                    <?php 
                                    if (empty($row['c_name'])) {
                                        echo "N/A";
                                    } else {
                                        echo ucwords($row['c_name']);
                                    }
                                    ?>
                                </b></p>
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
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" >
		                      <a class="dropdown-item view_project" href="./index.php?page=view_job&id=<?php echo $row['id'] ?>" data-id="<?php echo $row['id'] ?>">View</a>
						
		                      <?php if($_SESSION['login_type'] ==1): ?>
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