<?php
if (!isset($conn)) {
    include 'db_connect.php';
}

?>
<div class="col-lg-12">
     <!--<a class="btn btn-sm btn-default btn-flat border-primary mx-1" href="./index.php?page=schedule_teams_lvl3">-->
     <!--               Work Resource Schedule-->
     <!--           </a>-->
     <!--           <br>-->
     <!--           <br>-->
    <div class="card card-outline card-primary">
          
        <div class="card-body">
              <p>Create Contract</p>
            <!-- Form for Managing Team Schedule -->
            <form id="manage-schedule" action="./index.php?page=save_contract" method="POST">
                <div class="row">
                    <!-- Team Name -->
                  
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="" class="control-label">Name of Contract</label>
                            <input type="text" class="form-control form-control-sm" name="contract_name" value="" required>
                            <!-- Hidden field to pass the period -->
                          
                        </div>
                    </div>
                    
                      <div class="col-md-4">
                        <div class="form-group">
                            <label for="" class="control-label">Work Type</label>
                             <select class="form-control form-control-sm select2" name="worktype">
                                <?php
                                $teams = $conn->query("SELECT DISTINCT id, task_name FROM task_list");
                                while ($row = $teams->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $row['id']; ?>">
                                        <?php echo ucwords($row['task_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <!-- Team Members Onboarding -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="" class="control-label">Applicable Teams</label>
                            <select class="form-control form-control-sm select2" multiple="multiple" name="team_id[]" id=team_id">
                                <?php
                                $teams = $conn->query("SELECT DISTINCT team_id, team_name FROM team_schedule");
                                while ($row = $teams->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $row['team_id']; ?>">
                                        <?php echo ucwords($row['team_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer border-top border-info">
                    <div class="d-flex w-100 justify-content-center align-items-center">
    <!-- Save Button -->
                    <button 
                        class="btn btn-flat bg-gradient-primary mx-2" 
                        type="submit" 
                        onclick="disableButton(this)">
                        Save
                    </button>
                
                    <!-- Cancel Button -->
                    <button 
                        class="btn btn-flat bg-gradient-secondary mx-2" 
                        type="button" 
                        onclick="location.href='index.php?page=contracts'">
                        Cancel
                    </button>
                </div>

                </div>
            </form>
        </div>
    </div>
</div>

<div class="col-lg-12">
	<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white">
			<h4 class="card-title">Teams</h4>
        
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
				</colgroup>
				<thead style="background-color:#032033 !important; color:white">
					<tr>
						<th>Contract_ID</th>
						<th>Contract Name</th>
						<th>Creation_Date</th>
						<th>Number of Teams</th>
						<th>Worktype</th>

						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$work_qry = $conn->query("
                    SELECT
                        cn.contract_id,
                        cn.name_of_contract,
                        cn.date_created,
                        tl.task_name,
                        COUNT(cn.team_id) AS No_of_Teams
                    FROM
                        contracts cn
                    JOIN task_list tl ON
                        tl.id = cn.work_type_billing
                    GROUP BY
                       cn.contract_id;
                            ");
                   
					while($row=$work_qry->fetch_assoc()):
			
					?>
					<tr>
					    <td><p><?php echo ucwords($row['contract_id']) ?></p></td>
					     <td><p><?php echo ucwords($row['name_of_contract']) ?></p></td>
						<td><p><?php echo ucwords($row['date_created']) ?></p></td>
						<td><p><?php echo ucwords($row['No_of_Teams']) ?></p></td>
						<td><p><?php echo ucwords($row['task_name']) ?></p></td>
	                
						<!-- Action Dropdown -->
					<td class="text-center">
    <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                            Action
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item view_project" href="./index.php?page=configure_contract&contract_id=<?php echo $row['contract_id'] ?>">
                                View
                            </a>
                            <hr>
                            <a class="dropdown-item view_project" 
                               href="./index.php?page=delete_contract&contract_id=<?php echo $row['contract_id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this contract?');">
                                Delete
                            </a>
                        </div>
                    </td>
                    </tr>	
					<?php endwhile; ?>
				</tbody>
			</table>
	</div>
</div>
</div>
<!-- Custom CSS -->
<style>
    .table-responsive {
        overflow-x: auto;
    }
    table p {
        margin: unset !important;
    }
    table td, table th {
        vertical-align: middle !important;
    }
    .card-header {
        background-color: #007bff;
        color: white;
    }
    .table-hover tbody tr:hover {
        background-color: #f9f9f9;
    }
    .badge {
        font-size: 0.875rem;
    }
    .btn-default {
        background-color: white;
        border-color: #ddd;
    }
</style>
<script>

function disableButton(btn) {
    btn.disabled = true;          // Prevent double-clicks
    btn.innerText = 'Saving...';  // Visual feedback
    btn.form.submit();            // Submit form
}

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



