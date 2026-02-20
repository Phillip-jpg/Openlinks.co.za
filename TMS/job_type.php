<?php 

 if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    include'db_connect.php';
   

?>
<div class="col-lg-12">
	<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white">
		<?php if($_SESSION['login_type'] == 3): ?>
			
			<?php else: ?>
				<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_job_type"><i class="fa fa-plus"></i> Add New Job Type</a>
			</div>
	
				<?php endif ?>
			
		</div>
		<div class="card-body">
	
	<table class="table table-hover table-bordered table-condensed" id="list">
				<colgroup>
					<col width="7%">
					<col width="15%">
					<col width="55%">
					<col width="7%">
				</colgroup>
				<thead style="background-color:#032033 !important; color:white">
					<tr>
						<th>Job Type ID</th>
					
						<th>Job Type Name</th>
						<th>Descrption</th>
						
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
				    if($_SESSION['login_type']==2){
				        
				        	$qry = $conn->query("SELECT * FROM job_type WHERE creator_id={$_SESSION['login_id']}");
				        
				    }elseif($_SESSION['login_type']==3){
				        
				        $qry = $conn->query("SELECT jt.* FROM job_type jt JOIN users u ON u.id = jt.creator_id WHERE jt.creator_id = (SELECT creator_id FROM users WHERE id = {$_SESSION['login_id']})");
				        
				    }else{
				        	$qry = $conn->query("SELECT * FROM job_type");
				    }
				    

					while($row= $qry->fetch_assoc()):
				

					?>
					<tr>
						<td>
							<p><b><?php echo ucwords($row['id']) ?></b></p>
						</td>
						
					
						<td>
                        <p><b><?php echo ucwords($row['job_type_name']) ?></b></p>
			
						</td>
                        <td>
                        <p><b><?php echo html_entity_decode($row['description']) ?></b></p>
			
						</td>
					
					
						
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" >
		                      <?php if($_SESSION['login_type'] == 3 ||$_SESSION['login_type']==4): ?>
                                        
								<?php else: ?>
								
								<?php
                                    $secret = 'my_app_secret_key'; // put in config file ideally
                                    $payload = $row['id'];
                                    $hash = hash_hmac('sha256', $payload, $secret);
                                    $encoded = base64_encode($payload . ':' . $hash);
                                    ?>
		                      <a class="dropdown-item" href="./index.php?page=edit_job_type&id=<?php echo urlencode($encoded); ?>">Edit</a>
							  <a class="dropdown-item delete_job_type" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>

		                      
		                      
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
  const CSRF_TOKEN = "<?php echo $_SESSION['csrf_token']; ?>";
</script>
<script>
	$(document).ready(function(){
		$('#list').dataTable()
	
	$('.delete_job_type').click(function(){
	_conf("Are you sure to delete this job type?","delete_job_type",[$(this).attr('data-id')])
	})

	})
	function delete_job_type($id){
  start_load()
  $.ajax({
    url:'ajax.php?action=delete_job_type',
    method:'POST',
    data:{
      id: $id,
      csrf_token: CSRF_TOKEN
    },
    success:function(resp){
      if(resp == 1){
        alert_toast("Data successfully deleted",'success')
        setTimeout(function(){
          location.reload()
        },1500)
      } else if(String(resp).trim() === 'csrf'){
        alert_toast("Session expired. Refresh and try again.",'warning')
        end_load()
      } else {
        alert_toast("Delete failed: " + resp,'danger')
        end_load()
      }
    }
  })
}

// </script>