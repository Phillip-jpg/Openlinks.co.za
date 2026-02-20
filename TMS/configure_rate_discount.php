<?php 
if(!isset($conn)){ 
    include 'db_connect.php'; 
}

?>

<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-body">
			<form action="" id="manage-project">
		        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		        <div class="row">
			        <div class="col-md-6">
				        <div class="form-group">
					        <label for="" class="control-label">Name</label>
					        <input type="text" class="form-control form-control-sm" name="name" value="<?php echo isset($name) ? $name : '' ?>" required>
				        </div>
			        </div>

			        <div class="col-md-6">
			            <div class="form-group">
			                <label for="" class="control-label">Discount %</label>
			                <input type="number" class="form-control form-control-sm" name="discount" value="<?php echo isset($discount) ? $discount : '' ?>" required>
			            </div>
			        </div>
		        </div>

		        <div class="row">
			        <div class="col-md-6">
			            <div class="form-group">
			                <label for="" class="control-label">Lowest day </label>
			                <input type="number" class="form-control form-control-sm" name="low" value="<?php echo isset($low) ? $low : '' ?>" required>
			            </div>
			        </div>
			        <div class="col-md-6">
			            <div class="form-group">
			                <label for="" class="control-label">Highest day</label>
			                <input type="number" class="form-control form-control-sm" name="high" value="<?php echo isset($high) ? $high : '' ?>" required>
			            </div>
			        </div>
		        </div>

		        <div class="row">
			        <div class="col-md-6">
			            <div class="form-group">
			                <label for="" class="control-label">Add Work Type</label>
			                <select class="form-control form-control-sm select2" multiple="multiple" name="worktype_ids[]">
			                    <option></option>
			                    <?php 
			                    $taskList = $conn->query("SELECT * FROM task_list ORDER BY task_name ASC");
			                    while ($row = $taskList->fetch_assoc()) :
			                    ?>
			                    <option value="<?php echo $row['id'] ?>" <?php echo isset($worktype_ids) && in_array($row['id'], explode(',', $worktype_ids)) ? "selected" : '' ?>>
			                        <?php echo ucwords($row['task_name']) ?>
			                    </option>
			                    <?php endwhile; ?>
			                </select>
			            </div>
			        </div>
		        </div>
	        </form>
    	</div>
    	<div class="card-footer border-top border-info">
    		<div class="d-flex w-100 justify-content-center align-items-center">
    			<button class="btn btn-flat  bg-gradient-primary mx-2" form="manage-project">Save</button>
    			<button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href='index.php?page=job_list'">Cancel</button>
    		</div>
    	</div>
	</div>
</div>

<script>
	$('#manage-project').submit(function(e){
		e.preventDefault();
		start_load();
		$.ajax({
			url: 'ajax.php?action=save_configure',
			data: new FormData($(this)[0]),
			cache: false,
			contentType: false,
			processData: false,
			method: 'POST',
			success: function(resp){
				if(resp == 1){
					alert_toast('Data successfully saved',"success");
					setTimeout(function(){
						location.href = 'index.php?page=configure_list';
					}, 2000);
				} else {
					alert_toast('something went wrong',"fail");
					setTimeout(function(){
						location.href = 'index.php?page=configure_list';
					}, 2000);
				}
			},
			error: function(xhr, status, error){
				alert_toast('An error occurred. Please check the console for details.', "error");
				console.error('AJAX Error:', status, error);
				console.error('Response Text:', xhr.responseText);
			}
		});
	});
</script>
