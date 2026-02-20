<?php
if (!isset($conn)) {
    include 'db_connect.php';
}

$scorecard_url_secret = 'my_app_secret_key';
$scorecardId = 0;

if (isset($_GET['id']) && $_GET['id'] !== '') {
    $rawId = (string)$_GET['id'];

    // Backward compatibility for old numeric URLs.
    if (ctype_digit($rawId)) {
        $scorecardId = (int)$rawId;
    } else {
        $decoded = base64_decode($rawId, true);
        if ($decoded !== false) {
            $parts = explode(':', $decoded, 2);
            if (count($parts) === 2 && ctype_digit($parts[0])) {
                $expected = hash_hmac('sha256', $parts[0], $scorecard_url_secret);
                if (hash_equals($expected, $parts[1])) {
                    $scorecardId = (int)$parts[0];
                }
            }
        }
    }
}

if ($scorecardId <= 0) {
    echo "<div class='alert alert-danger'>Invalid score card reference.</div>";
    return;
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


                <br>
                <br>
    <div class="card card-outline card-primary">
	          
	        <div class="card-body">
	              <p>Assign Project Manager(s) to Score Card: <span style="color:green; font-weight:bold"> <?php 
	                    $result = $conn->query("SELECT Title FROM yasccoza_openlink_market.scorecard WHERE SCORECARD_ID={$scorecardId}");
	                    $row = $result->fetch_assoc();
	                    echo isset($row['Title']) ? $row['Title'] : 'Unknown';
	                    ?>   </span>
	                   
	                </p>
	            <!-- Form for Managing Team Schedule -->
	            <form id="manage-schedule" action="./index.php?page=save_scorecard_assign" method="POST">
	            <input type="hidden" name="scorecard_id" value="<?php echo $scorecardId; ?>">
                <div class="row">
                    <!-- Team Name -->
                    <!-- Team Members Onboarding -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="control-label">Project Manager</label>
                            <select class="form-control form-control-sm select2" multiple="multiple" name="pm_ids[]" id="pm_ids">
                                <?php
                                 if($_SESSION['login_type'] == 1){
                					    	$employees = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE type = 2");
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
                </div>
             
                <div class="card-footer border-top border-info">
                    <div class="d-flex w-100 justify-content-center align-items-center">
                        <!-- Save Button -->
	                       <button id="btn-save-scorecard" class="btn btn-flat bg-gradient-primary mx-2" type="submit">Save</button>


                        <!-- Cancel Button -->
                        <button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href='index.php?page=score_cards'">Back</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="col-lg-12">
	<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white">
			<h4 class="card-title">ScoreCard and Assigned PMs</h4>
        
		</div>
		<div class="card-body">
			<table class="table table-hover table-bordered table-condensed" id="list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="25%">
					<col width="10%">
					<col width="15%">
				    <col width="5%">
				</colgroup>
				<thead style="background-color:#032033 !important; color:white">
					<tr>
						<th>Score_ID</th>
						<th>Score_Card</th>
						<th>ScoreCard Description</th>
						<th>Number of PM Assigned</th>
						<th>Project Manager Assigned</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					    	$scorecardetails = $conn->query("SELECT 
                                s.*,
                                sp.date_assigned,
                                COUNT(sp.project_manager_id) AS manager_count, 
                                GROUP_CONCAT(CONCAT(u.firstname, ' ', u.lastname) SEPARATOR ', ') AS manager_names
	                            FROM scorecards_project sp 
	                            JOIN yasccoza_openlink_market.scorecard s ON sp.scorecard_id = s.SCORECARD_ID 
	                            JOIN users u ON u.id = sp.project_manager_id
	                            WHERE sp.scorecard_id={$scorecardId} 
	                            GROUP BY s.SCORECARD_ID;");
					while($row=$scorecardetails->fetch_assoc()):
			
					?>
					<tr>
					    <td><p><?php echo ucwords($row['SCORECARD_ID']) ?></p></td>
						<td><p><?php echo ucwords($row['Title']) ?></p></td>
						<td><p><?php echo ucwords($row['Other']) ?></p></td>
						<td><p><?php echo ucwords($row['manager_count']) ?></p></td>
					    <td><p><?php echo ucwords($row['manager_names']) ?></p></td>
						<!-- Action Dropdown -->
					<td class="text-center">
    <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                            Action
                        </button>
                        <div class="dropdown-menu">
                            <!--<a class="dropdown-item view_project" href="./index.php?page=team&team_id=<?php echo $row['team_id'] ?>" data-id="<?php echo $row['team_id'] ?>">-->
                            <!--    View/Edit-->
                            <!--</a>-->
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
		$(document).ready(function(){
			const $saveForm = $('#manage-schedule');
			const $saveBtn = $('#btn-save-scorecard');

			$saveForm.on('submit', function(e){
				if ($saveBtn.prop('disabled')) {
					e.preventDefault();
					return false;
				}

				$saveBtn.prop('disabled', true).text('Saving...');
				if (typeof start_load === 'function') {
					start_load();
				}
			});

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



