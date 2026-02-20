<?php


if (!isset($conn)) {
    include 'db_connect.php';
    
   	 $contract_id=$_GET['contract_id'];
    $edit_id=$_GET['contract_id'];			 
				
					$work_qry = $conn->query("
                SELECT DISTINCT bc.*, c.name_of_contract
                FROM
                    billing_configuration bc
                    JOIN
                    contracts c ON c.contract_id= bc.contract_id
                    WHERE c.contract_id=$contract_id AND id=$edit_id;
                            ");
                   
					while($row=$work_qry->fetch_assoc()){
					    
					    $contract_name=$row['name_of_contract'];
					}
		
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
              <p>Edit Billing Configuration</p>
            <!-- Form for Managing Team Schedule -->
            <form id="manage-schedule" action="./index.php?page=save_billing_configuration" method="POST">
                <div class="row">
                    <!-- Team Name -->
                  <input type="hidden" name="contract_id" value="<?php echo $_GET['contract_id']; $contract_name ?>">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="" class="control-label">Applicable Billing</label>
                             <select class="form-control form-control-sm select2" name="applicable_type" id=billing_id">
                                    <option value="20">
                                        Openlinks Services Fees
                                    </option>
                                     <option value="21">
                                        Production Team Fees
                                    </option>
                             </select>
                        </div>
                    </div>
                    
                      <div class="col-md-3">
                        <div class="form-group">
                            <label for="" class="control-label">Billing Type</label>
                            <select class="form-control form-control-sm select2" name="billing_type" id=billing_id">
                                    <option value="31">
                                        Base Rate
                                    </option>
                                     <option value="32">
                                        Pug Rate
                                    </option>
                                     <option value="33">
                                        Percentage Base
                                    </option>
                             </select>
                        </div>
                    </div>
                          
                     <div class="col-md-3">
                        <div class="form-group">
                            <label for="" class="control-label">Description</label>
                            <input type="text" class="form-control form-control-sm" name="description" value="" required>
                            <!-- Hidden field to pass the period -->
                          
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="" class="control-label">Cost</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="cost" value="" required>
                        </div>
                    </div>
                    
                </div>
                
                   <div class="row">
                    
                    
                     <div class="col-md-3">
                        <div class="form-group">
                            <label for="" class="control-label">Target of Job/Activites</label>
                            <input type="number" class="form-control form-control-sm" name="target" value="" required>
                          
                          
                        </div>
                    </div>
                    
                    <!-- <div class="col-md-3">-->
                    <!--    <div class="form-group">-->
                    <!--        <label for="" class="control-label">Rate</label>-->
                    <!--        <input type="decimal" class="form-control form-control-sm" name="rate" value="" required>-->
                          
                          
                    <!--    </div>-->
                    <!--</div>-->
            
              
                <div class="col-md-3">
                        <div class="form-group">
                            <label for="" class="control-label">Condition</label>
                            <select class="form-control form-control-sm select2" name="condition" id=billing_id">
                                    <option value="123">
                                        Number of Activties
                                    </option>
                                     <option value="124">
                                        Number of Jobs
                                    </option>
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
                        onclick="location.href='index.php?page=configure_contract&contract_id=<?php echo $_GET['contract_id']; ?>'">
                        Back
                    </button>
                </div>
            </div>
            </form>
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
    btn.disabled = true;          // Prevent multiple clicks
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



