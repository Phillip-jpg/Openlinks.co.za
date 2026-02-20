    <?php include'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success shadow-sm">
	<div class="card-header bg-primary text-white">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=configure_rate_discount"><i class="fa fa-plus"></i> Add Claim Dicount</a>
			</div>
		</div>
		<div class="card-body">
				<table class="table table-hover table-bordered table-condensed" id="list">
			<thead style="background-color:#032033 !important; color:white">
					<tr>
						<th class="text-center">#</th>
						<th>Discount Name</th>
						<th style="width:5px !important">Min (day)</th>
						<th>Max (day)</th>
						<th>Discount %</th>
						<th>Work type</th>
						<th>Action</th>
					</tr>
				</thead>
			
				<tbody>
					<?php

					$i=1;
					$qry = $conn->query("SELECT
    cr.id, name, low, high, discount, -- Assuming you want to group by 'id'
    GROUP_CONCAT(tl.task_name SEPARATOR ', ') AS task_names_grouped
FROM
    configure_rate cr
LEFT JOIN
    yasccoza_tms_db.task_list tl ON tl.id = cr.worktype_ids
GROUP BY
    cr.id;
				");
					while ($row = $qry->fetch_assoc()):
    // Process each row

					?>
					
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td ><b><?php echo ucwords($row['name']) ?></b></td>
						<td ><b><?php echo $row['low'] ?></b></td>
						<td ><b><?php echo $row['high'] ?></b></td>
						<td ><b><?php echo $row['discount'] ?></b></td>
					    <td ><b><?php echo $row['task_names_grouped'] ?></b></td>
					
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" >
		                      <a class="dropdown-item view_user" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">View</a>
							  <?php if($_SESSION['login_type'] ==1): ?>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item" href="./index.php?page=edit_discount_rate&id=<?php echo $row['id'] ?>">Edit</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item delete_discount" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
							
		                    </div>
							<?php endif; ?>
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
		// Initialize DataTables for the list
		$('#list').dataTable();

		// Attach click event to delete buttons
		$('.delete_discount').click(function(){
			// Confirm action before proceeding with deletion
			_conf("Are you sure to delete this discount?", "delete_discount", [$(this).attr('data-id')]);
		});
	});

	// Function to handle discount deletion
	function delete_discount(id){
		start_load(); // Show loading spinner

		$.ajax({
			url: 'ajax.php?action=delete_discount',
			method: 'POST',
			data: {id: id},  // Send the ID via POST
			success: function(resp){
				if(resp == 1){
					// Success response: show success message and reload the page
					alert_toast("Data successfully deleted", 'success');
					setTimeout(function(){
						location.reload();  // Reload the page after 1.5 seconds
					}, 1500);
				} else {
					// Error handling: log unexpected response
					console.log("Unexpected response: ", resp);
					alert_toast("Something went wrong!", 'error');
				}
			},
			error: function(xhr, status, error){
				// Handle any AJAX request errors
				console.error("AJAX Error: ", status, error);
				alert_toast("An error occurred. Please try again.", 'error');
			}
		});
	}
</script>
