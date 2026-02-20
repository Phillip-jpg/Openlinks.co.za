<?php include'db_connect.php' ?>
<?php
$scorecard_url_secret = 'my_app_secret_key';
?>
<div class="col-lg-12">
		<div class="card card-outline card-success shadow-sm">
	<div class="card-header bg-primary text-white">
		<?php if($_SESSION['login_type'] == 3): ?>
			
		<?php else: ?>
			<div class="card-tools">
				<!--<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_work_type"><i class="fa fa-plus"></i> Add New Score Card</a>-->
			</div>

			<?php endif ?>
			
		</div>
		<div class="card-body">
				<table class="table table-hover table-bordered table-condensed" id="list">
				<colgroup>
				    <col width="5%">
					<col width="25%">
					<col width="40%">
					<col width="10%">
				    <col width="10%">
				</colgroup>
					<thead style="background-color:#032033 !important; color:white">
					<tr>
						<th class="text-center">#</th>
					    <th>Title</th>
						<th>Description</th>
					    <th>Date of Expiry</th>
					     <th>Criteria</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					if($_SESSION['login_type'] == 1){
					    	$qry = $conn->query("SELECT 
                                            s.*, 
                                            COUNT(sc.CRITERIA_ID) as criteria_count
                                        FROM yasccoza_openlink_market.scorecard s
                                        LEFT JOIN yasccoza_openlink_market.scorecard_criteria sc ON s.SCORECARD_ID = sc.SCORECARD_ID
                                        GROUP BY s.SCORECARD_ID");
					}
					while($row= $qry->fetch_assoc()):
			
					?>
					<tr>
						<td class="text-center"><?php echo $i++ ?></td>
						<td>
							<p><b><?php echo ucwords($row['Title']) ?></b></p>
						</td>
						<td>
							<p><b><?php echo ucwords($row['Other']) ?></b></p>
						</td>
						<td>
						<p><b><?php echo ucwords($row['Date_of_Expiry']); ?></b></p>
						</td>
							<td>
						<p><b><?php echo ucwords($row['criteria_count']); ?></b></p>
						</td>
			
						<td class="text-center">
							
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" >
									<?php
									$scorecardPayload = (string)$row['SCORECARD_ID'];
									$scorecardHash = hash_hmac('sha256', $scorecardPayload, $scorecard_url_secret);
									$encodedScorecardId = urlencode(base64_encode($scorecardPayload . ':' . $scorecardHash));
									?>
									<a class="dropdown-item view_project" href="./index.php?page=assign_scorecard&id=<?php echo $encodedScorecardId; ?>">Assign Score Card</a>
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
	table p {
		margin: unset !important;
	}
	table td {
		vertical-align: middle !important;
	}
	.table-hover tbody tr:hover {
		background-color: #f5f5f5;
	}
	.badge {
		font-size: 0.875rem;
	}
	.card-header {
		background-color: #007bff;
		color: white;
	}
</style>
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
