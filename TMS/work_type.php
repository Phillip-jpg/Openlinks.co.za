<?php include'db_connect.php' ?>
<div class="col-lg-12">
		<div class="card card-outline card-success shadow-sm">
	<div class="card-header bg-primary text-white">
		<?php if($_SESSION['login_type'] == 3 || $_SESSION['login_type'] == 1): ?>
			
		<?php else: ?>
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_work_type"><i class="fa fa-plus"></i> Add New Work Type</a>
			</div>

			<?php endif ?>
			
		</div>
		<div class="card-body">
				<table class="table table-hover table-bordered table-condensed" id="list">
				<colgroup>
				<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
				    <col width="10%">
					<col width="10%">
				</colgroup>
					<thead style="background-color:#032033 !important; color:white">
					<tr>
						<th class="text-center">#</th>
						<th>Work Type</th>
					    <th>Type</th>
						<th>Price</th>
						<th>No of Resources</th>
						<th>Target</th>
						<th>Date Created</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php

					// echo $_SESSION['login_type'];
					$i = 1;
					if (isset($_SESSION['login_type']) && (int)$_SESSION['login_type'] === 2) {
				
						$qry = $conn->query("
							SELECT DISTINCT tl.*
							FROM task_list tl
							WHERE tl.creator_id = {$_SESSION['login_id']}
						");
					}elseif (isset($_SESSION['login_type']) && (int)$_SESSION['login_type'] === 3) {

						// $login_id = (int)($_SESSION['login_id'] ?? 0);

						$qry = $conn->query("
							SELECT DISTINCT tl.*
								FROM task_list tl
								INNER JOIN members_and_worktypes mw
								ON mw.work_type_id = tl.id
								INNER JOIN users u
								ON u.id = mw.member_id
								WHERE mw.member_id = {$_SESSION['login_id']}
								AND tl.creator_id = u.creator_id
								ORDER BY tl.task_name
								LIMIT 0, 25;
						");

					}else{
					    $qry = $conn->query("SELECT * FROM task_list order by id asc");
					}
					while($row= $qry->fetch_assoc()):
			
					?>
					<tr>
						<td class="text-center"><?php echo $i++ ?></td>
						<td>
							<p><b><?php echo ucwords($row['task_name']) ?></b></p>
						</td>
						
						<td>
							<p><b><?php echo ucwords($row['typeofw']) ?></b></p>
						</td>
						<td>
						<p><b><?php echo ucwords("R" . $row['price']); ?></b></p>

			
						</td>
						<td>
							<p><b><?php echo ucwords($row['resources']) ?></b></p>
			
						</td>
						<td>
							<p><b><?php echo ucwords($row['target']) ?></b></p>
			
						</td>

						<td>
							<p><b><?php echo ucwords(string: $row['date_created']) ?></b></p>
			
						</td>
					
						
						<td class="text-center">
							
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" >
		                        <?php
                                    $secret = 'my_app_secret_key'; // put in config file ideally
                                    $payload = $row['id'];
                                    $hash = hash_hmac('sha256', $payload, $secret);
                                    $encoded = base64_encode($payload . ':' . $hash);
                                    ?>
		                      <?php if($_SESSION['login_type'] == 3 || $_SESSION['login_type'] == 4 || $_SESSION['login_type'] == 1): ?>
								<a class="dropdown-item view_project" href="./index.php?page=view_work_type&id=<?php echo urlencode($encoded); ?>" data-id="<?php echo $row['id'] ?>">View</a>
								<?php else: ?>
								<a class="dropdown-item view_project" href="./index.php?page=view_work_type&id=<?php echo urlencode($encoded); ?>" data-id="<?php echo $row['id'] ?>">View</a>
		                      <a class="dropdown-item" href="./index.php?page=edit_work_type&id=<?php echo urlencode($encoded); ?>">Edit</a>
							  <!-- <a class="dropdown-item delete_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a> -->
		                      
		                      
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
