<?php
if (!isset($conn)) {
    include 'db_connect.php';
}

$contract_id = $_GET['contract_id'] ?? null;

if ($contract_id) {
    $stmt = $conn->prepare("
       SELECT DISTINCT
            c.*,
            ts.team_name
        FROM 
            contracts c
        JOIN 
            team_schedule ts ON ts.team_id = c.team_id
        WHERE 
            c.contract_id = $contract_id;
    ");
    $stmt->bind_param('i', $contract_id);
    $stmt->execute();
    $work_qry = $stmt->get_result();
}
?>

<div class="col-lg-12">
    <div class="card-body">
                <!-- Form for Adding Team Members -->
                <form action="./index.php?page=add_contract_member" method="POST">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                               
                                    <?php
                                    
                                   
                           
                            $stmt = $conn->prepare("
                                SELECT DISTINCT
                                    c.*
                                FROM 
                                    contracts c
                                WHERE 
                                    c.contract_id = $contract_id;
                            ");
                            $stmt->bind_param('i', $contract_id); // Bind the $team_id parameter
                            $stmt->execute();
                            $qry = $stmt->get_result(); // Execute and get results
                            
                            // Fetch a single row from the results (if applicable)
                            $row1 = $qry->fetch_assoc();
                            ?>
                               
                               
                            <!-- Hidden Inputs for Form -->
                             <input type="hidden" name="contract_id" value="<?php echo htmlspecialchars($contract_id); ?>">
                            <input type="hidden" name="name_of_contract" value="<?php echo $row1['name_of_contract']; ?>">
                            <input type="hidden" name="date_created" value="<?php echo $row1['date_created'];?>">
                            <input type="hidden" name="work_type" value="<?php echo $row1['work_type_billing']; ?>">
                                <!-- Add Team Members Dropdown -->
                                <label for="" class="control-label">Add Applicable Teams</label>
                            <select class="form-control form-control-sm select2" multiple="multiple" name="new_team_id[]">
                                 <option value=""></option>
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
                      <div class="col-md-4">
                            <div class="form-group">
                               
                                    <?php
                                    
                                   
                           
                            $stmt = $conn->prepare("
                                SELECT DISTINCT
                                    c.*
                                FROM 
                                    contracts c
                                WHERE 
                                    c.contract_id = $contract_id;
                            ");
                            $stmt->bind_param('i', $contract_id); // Bind the $team_id parameter
                            $stmt->execute();
                            $qry = $stmt->get_result(); // Execute and get results
                            
                            // Fetch a single row from the results (if applicable)
                            $row1 = $qry->fetch_assoc();
                            ?>
                               
                                <!-- Add Team Members Dropdown -->
                                <label for="" class="control-label">Delete Applicable Team</label>
                            <select class="form-control form-control-sm select2" name="delete_team">
                                <option value=""></option>
                                <?php
                                $teams = $conn->query("SELECT DISTINCT ts.team_id, ts.team_name, c.name_of_contract FROM contracts c,team_schedule ts WHERE c.team_id= ts.team_id AND c.contract_id=$contract_id");
                                while ($row = $teams->fetch_assoc()):
                                    
                                    $name_of_contract= $row['name_of_contract'];
                                ?>
                                    <option value="<?php echo $row['team_id']; ?>">
                                        <?php echo ucwords($row['team_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            </div>
                        </div>
                         <div class="col-md-4">
                        <div class="form-group">
                            <label for="" class="control-label">Change Contract Name</label>
                            <input type="text" class="form-control form-control-sm" name="new_contract_name" value="">
                            <!-- Hidden field to pass the period -->
                          
                        </div>
                    </div>
        
                    </div>
                    <div class="card-footer border-top border-info">
                       <div class="d-flex justify-content-center">
                            <button 
                                class="btn btn-flat bg-gradient-primary mx-2" 
                                type="submit" 
                                onclick="disableButton(this)">
                                Save
                            </button>
                            <button 
                                class="btn btn-flat bg-gradient-secondary mx-2" 
                                type="button" 
                                onclick="location.href='index.php?page=contracts'">
                                Back
                            </button>
                        </div>
                    </div>
                </form>
            </div>
    <div class="card card-outline card-success shadow-sm">
        	<div class="card-header bg-primary text-white">
			<h4 class="card-title">Contract Id: <?php echo $_GET['contract_id'].'<br>'; echo $name_of_contract ?></h4>
        
		</div>
        
        <div class="card-body">
            <div class="table-responsive" class="centered-div">
            
             <p style="background-color:#007bff; width:400px; height:25px; text-align:center; color:white; border-radius:10px; line-height:25px; margin: 0 auto;">
                  <a href="./index.php?page=billing_configuration&contract_id=<?php echo $contract_id; ?>" 
   style="color:white; text-decoration:none; display:inline-block; width:100%; height:100%;">
                    Billing Configuration
                  </a>
                </p>
                <table class="table table-hover table-bordered table-condensed" id="list">
                    <colgroup>
                        <col width="50%">
                        <col width="50%">
                    </colgroup>
                    <thead style="background-color:#032033; color:white;">
                        <tr>
                            <th>Team_ID</th>
                            <th>Team_Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($work_qry)): ?>
                            <?php while ($row = $work_qry->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['team_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['team_name']); ?></td>
                                </tr>
 <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No team members found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>

<style>
    .table-responsive {
        overflow-x: auto;
    }
    table td, table th {
        vertical-align: middle !important;
    }
    .table-hover tbody tr:hover {
        background-color: #f9f9f9;
    }
</style>
<script>

function disableButton(btn) {
    btn.disabled = true;          // Prevent further clicks
    btn.innerText = 'Saving...';  // Optional visual feedback
    btn.form.submit();            // Submit the form
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
