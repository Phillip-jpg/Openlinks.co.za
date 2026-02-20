<?php
include 'db_connect.php';
$qry = $conn->query("SELECT * FROM task_list where id = 46")->fetch_array();
foreach($qry as $k => $v){
	$$k = $v;
}
$tprog = $conn->query("SELECT * FROM task_list where id = {$id}")->num_rows;

$file_path = isset($file_path) ? "work_type_docs/" . $file_path : '';
$file_name = isset($file_path) ? str_replace('work_type_docs/', '', $file_path) : '';
?>

<?php
include 'db_connect.php';
$qry = $conn->query("SELECT SUM(resources) FROM user_productivity WHERE task_id = 46")->fetch_object();

// Check if the query executed successfully
    $sumResources = $qry->{"SUM(resources)"};

    // Display the result within an HTML <dd> element

?>


<div class="col-lg-12">
	<div class="row">
		<div class="col-md-12">
			<div class="callout callout-info">
				<div class="col-md-12">
					<div class="row">
						<div class="col-sm-6">
							<dl>
								<dt><b class="border-bottom border-primary">Project Name</b></dt>
								<dd><?php echo ucwords($task_name) ?></dd>
								<dt><b class="border-bottom border-primary">Description</b></dt>
								<dd><?php echo html_entity_decode($description) ?></dd>
								<dt><b class="border-bottom border-primary">No of resources</b></dt>
								<dd><?php echo html_entity_decode($resources) ?></dd>

								<!-- Display the file_path and add the download button (if available) -->
                                <?php if (!empty($file_path)): ?>
                                    <dt><b class="border-bottom border-primary">File</b></dt>
									<dd><?php echo html_entity_decode($file_name) ?></dd>
                                    <dd>
                                        <!-- Add the download button -->
                                        <a href="<?php echo $file_path; ?>" download>Download File</a>
                                    </dd>
                                <?php endif; ?>
							
							</dl>
						</div>
						<div class="col-md-6">
							<dl>
                            <dt><b class="border-bottom border-primary">Customer Benefits</b></dt>
								<dd><?php echo ucwords($customer_benefits) ?></dd>
							</dl>
							<dl>
                            <dt><b class="border-bottom border-primary">Instructions</b></dt>
								<dd><?php echo ucwords($instructions) ?></dd>
							</dl>
							<dl>
                            <dt><b class="border-bottom border-primary">Duration</b></dt>
								<dd><?php echo ucwords($duration) ?></dd>
							</dl>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
	<div class="col-md-12">
			<div class="card card-outline card-primary">
				<div class="card-header">
					<span><b>Activities List for: <?php echo ucwords($task_name) ?></b></span>
					<div class="card-tools">	
						<button style="border-radius: 5px; background-color:#007BFF; color:white"><a style="color:white; background-color:#007BFF" class="dropdown-item new_productivity" data-tid = '<?php echo ($id) ?>'  data-task = '<?php echo ucwords($task_name) ?>'  href="javascript:void(0)">Add Activity</a></button>
					</div>
				
				</div>
				<div class="card-body p-0">
					<div class="table-responsive">
					<table class="table table-condensed m-0 table-hover">
						<colgroup>
							<col width="5%">
							<col width="25%">
							<col width="15%">
							<col width="15%">
							<col width="20%">
						</colgroup>
						<thead>
							<th>#</th>
							<th>Name</th>
							<th>Duration</th>
							<th>Comments</th>
							<th>resources</th>
							<th>Action</th>
						</thead>
						<tbody>
							<?php 
							$i = 1;
							$tasks = $conn->query("SELECT * FROM user_productivity where task_id = {$id} order by name asc");
							while($row=$tasks->fetch_assoc()):
							
							?>
								<tr>
			                        <td class="text-center"><?php echo $i++ ?></td>
			                        <td class=""><b><?php echo ucwords($row['name']) ?></b></td>
									<td class=""><b><?php echo ucwords($row['duration']) ?></b></td>
									<td class=""><b><?php echo ucwords($row['comment']) ?></b></td>
									<?php if ($sumResources > $resources): ?>
   										 <td class=""><b style="color:red; font-size: 13.5px">Your activities resources total exceeds work type allocated resources.</b><span style="font-size: 15px; color: black; font-weight: bold;">(<?php echo ucwords($row['resources']); ?>)</span> </td>
									<?php elseif ($sumResources < $resources):  ?>
										<td class=""><b style="color:red; font-size: 13.5px">Your activities resources total is less than work type allocated resources.</b><span style="font-size: 15px; color: black; font-weight: bold;">(<?php echo ucwords($row['resources']); ?>)</span> </td>
										<?php else: ?>
    									<td class=""><b style=" font-size: 17px"><?php echo ucwords($row['resources']); ?></b> <span style="font-size: 12px; color:#007BFF;margin-left: 5px"><i class="fa fa-check" aria-hidden="true"></i></span></td>
								  <?php endif; ?>
								<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" >
		                
		                      <?php if($_SESSION['login_type'] != 3): ?>
		                      <a class="dropdown-item" href="./index.php?page=edit_activity&id=<?php echo $row['id'] ?>">Edit</a>
		                    
		                      <a class="dropdown-item delete_progress" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
		                  <?php endif; ?>
		                    </div>
						</td>
			                      
		                    	</tr>
							<?php 
							endwhile;
							?>
						</tbody>
					</table>
					</div>
				</div>
			</div>
		</div>

	</div>

</div>
<style>
	.users-list>li img {
	    border-radius: 50%;
	    height: 67px;
	    width: 67px;
	    object-fit: cover;
	}
	.users-list>li {
		width: 33.33% !important
	}
	.truncate {
		-webkit-line-clamp:1 !important;
	}
</style>
<script>
	$(document).ready(function(){
		$('#list').dataTable()
	

	$('.delete_progress').click(function(){
	_conf("Are you sure to delete this activity?","delete_progress",[$(this).attr('data-id')])
	})

	$('.new_productivity').click(function(){
		uni_modal("<i class='fa fa-plus'></i> New Activity for: Work type-  "+$(this).attr('data-task'),"activity.php?tid="+$(this).attr('data-tid'),'large')
	})

	})
	


	function delete_progress($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_progress',
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