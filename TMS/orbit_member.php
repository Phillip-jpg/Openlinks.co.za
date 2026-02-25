<?php
if (!isset($conn)) {
    include 'db_connect.php';
}

?>
<style>
.btn-primary-modern {
    background: linear-gradient(135deg, #007bff, #00c6ff);
    color: white !important;
    border: none;
    border-radius: 12px;
    padding: 8px 18px;
    font-weight: 500;
    box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
    transition: all 0.25s ease-in-out;
    text-decoration: none;
}

.btn-primary-modern:hover {
    background: linear-gradient(135deg, #0062cc, #00a3e0);
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 123, 255, 0.4);
}

.btn-primary-modern:active {
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
}
</style>
            <a href="./index.php?page=orbit_accounts"
               class="btn btn-flat bg-gradient-primary mx-2">
               <span class="text">Orbit Accounts</span>
            </a>
             <br>
            <div class="card-body">
        
    <div class="card card-outline card-primary">
            <!-- Form for Managing Team Schedule -->
            <form id="manage-schedule" action="./index.php?page=save_orbit" method="POST">
                <div class="row">
                    <!-- Team Name -->
                    <!-- Team Members Onboarding -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="control-label">Orbit Member to</label>
                            <select class="form-control form-control-sm select2" name="member_id">
                                <?php
                                 if($_SESSION['login_type'] == 1){
                					    	$employees = $conn->query("SELECT DISTINCT id, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE type = 3");
                					}else{
                					   //   $employees = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE type = 3 OR type =2 ORDER BY name ASC");
                					}
                                
                                while ($row = $employees->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $row['id']; ?>">
                                        <?php echo ucwords($row['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    
               
            
                    <!-- Team Name -->
                    <!-- Team Members Onboarding -->
                   <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Entity</label>
                        
                            <select class="form-control form-control-sm select2" name="pm_id" id="pm_id" required>
                              <option value="">-- Select Entity (PM) --</option>
                              <?php
                              if($_SESSION['login_type'] == 1){
                                $employees = $conn->query("SELECT id, CONCAT(firstname,' ',lastname) AS name FROM users WHERE type = 2 ORDER BY name ASC");
                                while ($row = $employees->fetch_assoc()):
                              ?>
                                  <option value="<?php echo (int)$row['id']; ?>">
                                    <?php echo ucwords($row['name']); ?>
                                  </option>
                              <?php
                                endwhile;
                              }
                              ?>
                            </select>
                        
                            <label class="control-label mt-2">Work Types</label>
                            <select class="form-control form-control-sm select2" name="worktype_ids[]" id="worktype_ids" multiple required disabled>
                              <option value="">-- Select Work Type(s) --</option>
                            </select>
                        
                          </div>
                        </div>

             
              </div>
                
             
                <div class="card-footer border-top border-info">
                    <div class="d-flex w-100 justify-content-center align-items-center">
                        <!-- Save Button -->
                       <button class="btn btn-flat bg-gradient-primary mx-2" type="submit" onclick="disableButton(this)">Save</button>


                        <!-- Cancel Button -->
                        <button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href='index.php?page=score_cards'">Back</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    
    <div class="col-lg-12">
	<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white">
			<h4 class="card-title">People Orbit Summary</h4>
        
		</div>
		<div class="card-body">
			<table class="table table-hover table-bordered table-condensed" id="list">
				<colgroup>
					<col width="5%">
					<col width="5%">
					<col width="5%">
					<col width="5%">
					<col width="5%">
				    <col width="5%">
				    <col width="5%">
				</colgroup>
				<thead style="background-color:#032033 !important; color:white">
					<tr>
					    <th>Orbit_ID</th>
						<th>Date Orbited</th>
						<th>Orbiter</th>
						<th>Member</th>
						<th>Member ID</th>
						<th>From Entity</th>
						<th>To Entity</th>
					</tr>
				</thead>
				<tbody>
					<?php
					
					if($_SESSION['login_type'] == 1) {
					    
					    	$work_qry = $conn->query("
                            /* MySQL 8+ (recommended): correct dedupe without relying on DISTINCT) */
                            WITH people AS (
                              SELECT id,
                                     MAX(firstname) AS firstname,
                                     MAX(lastname)  AS lastname
                              FROM users
                              GROUP BY id
                            ),
                            mother AS (
                              SELECT x.*
                              FROM (
                                SELECT u0.*,
                                       ROW_NUMBER() OVER (PARTITION BY u0.id ORDER BY u0.pri_id DESC) AS rn
                                FROM users u0
                                WHERE u0.orbit = 0
                              ) x
                              WHERE x.rn = 1
                            ),
                            branches AS (
                              SELECT y.*
                              FROM (
                                SELECT b.*,
                                       /* Optional: remove duplicate branch events with same member+creator+orbiter */
                                       ROW_NUMBER() OVER (
                                         PARTITION BY b.id, b.creator_id, b.orbiter_id
                                         ORDER BY b.pri_id DESC
                                       ) AS rn
                                FROM users b
                                WHERE b.orbit = 1
                              ) y
                              WHERE y.rn = 1  -- comment this line if you want every orbit=1 event, even repeats
                            )
                            SELECT
                              b.date_created,
                              b.id,
                              b.pri_id,
                              CONCAT_WS(' ', ob.firstname, ob.lastname)  AS orbiter_name,
                              CONCAT_WS(' ', me.firstname, me.lastname)  AS member_name,
                              CONCAT_WS(' ', mom.firstname, mom.lastname) AS mother_creator_name, -- orbit=0 creator repeated
                              CONCAT_WS(' ', br.firstname, br.lastname)  AS branch_creator_name   -- orbit=1 creator varies
                            FROM branches b
                            JOIN mother m        ON m.id = b.id
                            JOIN people me       ON me.id = b.id
                            LEFT JOIN people mom ON mom.id = m.creator_id
                            LEFT JOIN people br  ON br.id = b.creator_id
                            LEFT JOIN people ob  ON ob.id = b.orbiter_id
                            ORDER BY me.firstname ASC, b.date_created ASC;");
					}
					while($row=$work_qry->fetch_assoc()):
			
					?>
					<tr>
					    <td><p><?php echo ucwords($row['pri_id']) ?></p></td>
					    <td><p><?php echo ucwords($row['date_created']) ?></p></td>
						<td><p><?php echo ucwords($row['orbiter_name']) ?></p></td>
						<td><p><?php echo ucwords($row['member_name']) ?></p></td>
						<td><p><?php echo ucwords($row['id']) ?></p></td>
						<td><p><?php echo ucwords($row['mother_creator_name']) ?></p></td>
						<td><p><?php echo ucwords($row['branch_creator_name']) ?></p></td>

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
    btn.disabled = true;
    btn.innerText = 'Saving...'; // Optional
    btn.form.submit(); // Submit the form
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

<script>
$(document).ready(function () {

  $('#pm_id').on('change', function () {
    const pmId = $(this).val();

    $('#worktype_ids').prop('disabled', true).empty()
      .append(`<option value="">-- Select Work Type(s) --</option>`)
      .trigger('change');

    if (!pmId) return;

    $.getJSON('get_pm_worktypes.php', { pm_id: pmId }, function (data) {

      $('#worktype_ids').empty()
        .append(`<option value="">-- Select Work Type(s) --</option>`);

      if (!data || data.length === 0) {
        $('#worktype_ids').append(`<option value="">No work types found</option>`).trigger('change');
        return;
      }

      data.forEach(function (w) {
        $('#worktype_ids').append(`<option value="${w.id}">${w.task_name}</option>`);
      });

      $('#worktype_ids').prop('disabled', false).trigger('change');
    });
  });

});
</script>






