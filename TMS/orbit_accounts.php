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


               
                       
            <a href="./index.php?page=orbit_member"
               class="btn btn-flat bg-gradient-primary mx-2">
               <span class="text">Orbit Member</span>

            </a>
             <br>
            <div class="card-body">
        
    <div class="card card-outline card-primary">
            <!-- Form for Managing Team Schedule -->
            <form id="manage-schedule" action="./index.php?page=save_orbit_client" method="POST">
                <div class="row">
                    <!-- Team Name -->
                    <!-- Team Members Onboarding -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="control-label">Orbit Account to</label>
                            <select class="form-control form-control-sm select2" name="client_id">
                                <?php
                                 if($_SESSION['login_type'] == 1){
                					    	$employees = $conn->query("SELECT
                                client.*,
                                title,
                                office,
                                GROUP_CONCAT(DISTINCT CONCAT('(', client_rep.REP_NAME, ')') ORDER BY client_rep.REP_NAME ASC) AS reps
                            FROM yasccoza_openlink_market.client
                            LEFT JOIN yasccoza_openlink_association_db.industry_title
                                ON client.industry_id = industry_title.TITLE_ID
                            LEFT JOIN yasccoza_openlink_association_db.industry
                                ON client.office_id = industry.INDUSTRY_ID
                            LEFT JOIN client_rep
                                ON client_rep.CLIENT_ID = yasccoza_openlink_market.client.CLIENT_ID
                            GROUP BY client.CLIENT_ID, title, office");
                					}else{
                					   //   $employees = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE type = 3 OR type =2 ORDER BY name ASC");
                					}
                                
                                while ($row = $employees->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $row['CLIENT_ID']; ?>">
                                        <?php echo ucwords($row['company_name']); ?>
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
			<h4 class="card-title">Account Orbit Summary</h4>
        
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
						<th>Client Orbited</th>
						<th>Client ID</th>
						<th>From Entity</th>
						<th>To Entity</th>
					</tr>
				</thead>
				<tbody>
					<?php
					
					if($_SESSION['login_type'] == 1) {
					    
					    	$work_qry = $conn->query("
                            WITH all_client_rows AS (
                                SELECT
                                    c.client_pri_id,
                                    c.CLIENT_ID,
                                    c.company_name,
                                    c.created,
                                    c.creator_id AS to_entity_id,
                                    c.orbiter_id,
                                    LAG(c.creator_id) OVER (
                                        PARTITION BY c.CLIENT_ID
                                        ORDER BY c.client_pri_id
                                    ) AS from_entity_id
                                FROM yasccoza_openlink_market.client c
                            ),
                            orbit_events AS (
                                SELECT *
                                FROM all_client_rows
                                WHERE COALESCE(orbiter_id, 0) > 0
                            )
                            SELECT
                                oe.client_pri_id,
                                oe.created AS date_orbited,
                                oe.CLIENT_ID,
                                oe.company_name,
                                CONCAT_WS(' ', ob.firstname, ob.lastname) AS orbiter_name,
                                CONCAT_WS(' ', fe.firstname, fe.lastname) AS from_entity_name,
                                CONCAT_WS(' ', te.firstname, te.lastname) AS to_entity_name
                            FROM orbit_events oe
                            LEFT JOIN users ob ON ob.id = oe.orbiter_id
                            LEFT JOIN users fe ON fe.id = oe.from_entity_id
                            LEFT JOIN users te ON te.id = oe.to_entity_id
                            ORDER BY oe.client_pri_id DESC");
					}
					while($row=$work_qry->fetch_assoc()):
			
					?>
					<tr>
					    <td><p><?php echo (int)$row['client_pri_id'] ?></p></td>
					    <td><p><?php echo htmlspecialchars((string)$row['date_orbited']) ?></p></td>
						<td><p><?php echo htmlspecialchars(ucwords((string)($row['orbiter_name'] ?: 'N/A'))) ?></p></td>
						<td><p><?php echo htmlspecialchars(ucwords((string)$row['company_name'])) ?></p></td>
						<td><p><?php echo (int)$row['CLIENT_ID'] ?></p></td>
						<td><p><?php echo htmlspecialchars(ucwords((string)($row['from_entity_name'] ?: 'N/A'))) ?></p></td>
						<td><p><?php echo htmlspecialchars(ucwords((string)($row['to_entity_name'] ?: 'N/A'))) ?></p></td>

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
