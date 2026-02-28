<?php
include_once 'db_connect.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* -----------------------------
   1) Read & decode safe job param
------------------------------ */
if (isset($_GET['job']) && $_GET['job'] !== '') {

$decoded = base64_decode($_GET['job'], true);
if ($decoded === false) {
    die("Invalid job reference");
}

$parts = explode('|', $decoded);
if (count($parts) !== 2 || !ctype_digit($parts[0])) {
    die("Invalid job reference");
}

$expectedHash = hash_hmac('sha256', $parts[0], 'my_app_secret_key');
if (!hash_equals($expectedHash, $parts[1])) {
    die("Invalid job reference");
}

$id = (int)$parts[0];

/**
 * ✅ IMPORTANT:
 * Your existing SQL uses $_GET['id'] and {$id}
 * So we set them from the decoded value (without changing your SQL).
 */
$id = (int)$parts[0];        // used in {$id}
$_GET['id'] = $id;           // used in "... ".$_GET['id']
$projectId = $id;            // optional alias (not required)
} elseif (isset($_GET['id']) && ctype_digit((string)$_GET['id'])) {
    $id = (int)$_GET['id'];
    $_GET['id'] = $id;
    $projectId = $id;
} else {
    die("Invalid request");
}

$login_id  = isset($_SESSION['login_id']) ? (int)$_SESSION['login_id'] : 0;
if ($login_id <= 0) {
    die("Session expired. Please login again.");
}

$backPage = '';
$backTeamRef = '';
if (isset($_GET['back'])) {
    $requestedBack = (string)$_GET['back'];
    if (in_array($requestedBack, ['productivity_pipeline', 'job_list', 'home', 'jobs_to_manage', 'jobs_to_manage_level1', 'my_team_jobs_to_manage', 'my_team_jobs_to_manage_lvl_1'], true)) {
        $backPage = $requestedBack;
    }
}
if (isset($_GET['back_team']) && $_GET['back_team'] !== '') {
    $backTeamRef = (string)$_GET['back_team'];
}

$stat = array("Pending","Started","On-Progress","On-Hold","Over Due","Done");

/* ✅ FIX: use decoded id via $_GET['id'] (now set above) */
$qry = $conn->query("SELECT * FROM project_list where id = ".$_GET['id'])->fetch_array();
foreach($qry as $k => $v){
	$$k = $v;
}

/* ✅ FIX: your SQL expects {$id} */
$tprog = $conn->query("SELECT * FROM task_list where project_id = {$id}")->num_rows;

//$cprog = $conn->query("SELECT * FROM task_list where project_id = {$id} and status = 3")->num_rows;

/* ✅ NOTE:
   You commented out $cprog query, but still use $cprog below.
   If $cprog is not set elsewhere, this avoids warnings.
*/
if (!isset($cprog)) {
    $cprog = 0;
}

$prog = $tprog > 0 ? ($cprog/$tprog) * 100 : 0;
$prog = $prog > 0 ?  number_format($prog,2) : $prog;

/* ✅ FIX: your SQL expects {$id} */
$filesResult = $conn->query("SELECT * FROM yasccoza_openlink_market.rfp p WHERE p.POST_ID = {$id}");

/* ✅ manager_id comes from the project_list row above */
$manager = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where id = $manager_id");
$manager = $manager->num_rows > 0 ? $manager->fetch_array() : array();
?>

<?php
include 'db_connect.php';

/* ✅ SQL stays the same, $_GET['id'] is already set from decoded job above */
$qry = $conn->query("SELECT task_ids FROM project_list WHERE id = ".$_GET['id'])->fetch_array();
$taskIdsString = $qry['task_ids'];
$taskIdsArray = array_map('intval', explode(',', $taskIdsString));

$taskIdsString = implode(',', $taskIdsArray);
$qry2 = $conn->query("SELECT task_name,id FROM task_list WHERE id IN ($taskIdsString)");

$taskNames = array();
$taskIdz   = array(); // ✅ avoid undefined warning
while ($row = $qry2->fetch_assoc()) {
    $taskNames[] = $row['task_name'];
	$taskIdz[]   = $row['id'];
}
?>


<?php
include 'db_connect.php';

/* ✅ SQL stays the same, $_GET['id'] is already set from decoded job above */
$qry = $conn->query("SELECT user_ids FROM project_list WHERE id = ".$_GET['id'])->fetch_array();
$userIdsString = $qry['user_ids'];
$userIdsArray = array_map('intval', explode(',', $userIdsString));

$userIdsString = implode(',', $userIdsArray);
$qry2 = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS full_name FROM users WHERE id IN ($userIdsString)");

$userNames = array();
while ($row = $qry2->fetch_assoc()) {
    $userNames[] = $row['full_name'];
}
?>
<?php
include 'db_connect.php';

/* ✅ SQL stays the same, $_GET['id'] is already set from decoded job above */
$qry = $conn->query("SELECT user_ids FROM project_list WHERE id = ".$_GET['id'])->fetch_array();
$userIdsString = $qry['user_ids'];
$userIdsArray = array_map('intval', explode(',', $userIdsString));

$userIdsString = implode(',', $userIdsArray);
$qry2 = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS full_name FROM users WHERE id IN ($userIdsString)");

$userNames = array();
while ($row = $qry2->fetch_assoc()) {
    $userNames[] = $row['full_name'];
}

$qry4 = $conn->query("SELECT user_productivity.name FROM user_productivity JOIN assigned_duties ON assigned_duties.activity_id = user_productivity.id WHERE assigned_duties.project_id = ".$_GET['id']);

if ($qry4) {
    while ($row4 = $qry4->fetch_assoc()) {
        // kept as-is
    }
} else {
    // kept as-is
}
?>


<div class="col-lg-12">
    <?php if ($backPage !== ''): ?>
    <div class="mb-3">
        <a href="./index.php?page=<?php echo htmlspecialchars($backPage, ENT_QUOTES, 'UTF-8'); ?><?php echo (in_array($backPage, ['jobs_to_manage_level1', 'my_team_jobs_to_manage_lvl_1'], true) && $backTeamRef !== '') ? '&team_id=' . rawurlencode($backTeamRef) : ''; ?>" class="btn btn-primary btn-sm">
            <?php
                if ($backPage === 'productivity_pipeline') {
                    echo 'Back to Productivity Pipeline';
                } elseif ($backPage === 'job_list') {
                    echo 'Back to Job List';
                } elseif ($backPage === 'jobs_to_manage') {
                    echo 'Back to Jobs To Manage';
                } elseif ($backPage === 'jobs_to_manage_level1') {
                    echo 'Back to Jobs To Manage Level 1';
                } elseif ($backPage === 'my_team_jobs_to_manage') {
                    echo 'Back to My Team Jobs To Manage';
                } elseif ($backPage === 'my_team_jobs_to_manage_lvl_1') {
                    echo 'Back to My Team Jobs To Manage Level 1';
                } else {
                    echo 'Back to Home';
                }
            ?>
        </a>
    </div>
<?php endif; ?>
	<div class="row">
		<div class="col-md-12">
			<div class="callout callout-info">
				<div class="col-md-12">
					<div class="row">
						<div class="col-sm-6">
							<dl>
							  <p style="color:#17a2b8; font-weight:bold">Job ID: <?php echo ucwords($id) ?></p>
								<dt><b class="border-bottom border-primary">Job Name</b></dt>
								<dd><?php echo ucwords($name) ?></dd>
								<br>
								<br>	
								
							
								<dt><b class="border-bottom border-primary">Scorecard</b></dt>
								
								<?php
                                    if (empty($scorecard)) {
                                        echo "None";
                                    } else {
                                        
                                        if(is_numeric($scorecard)){
                                            
                                             $qry = $conn->query("SELECT Title 
                                                            FROM yasccoza_openlink_market.scorecard 
                                                            WHERE SCORECARD_ID = $scorecard");
                                    
                                        if ($qry) {
                                            $row = $qry->fetch_object();  // Fetch a row from the result set as an object
                                            if ($row) {
                                                echo "<dd>" . html_entity_decode($row->Title) . "</dd>";
                                            }
                                        }
                                            
                                        }else {
                                            
                                              echo "<dd>" . html_entity_decode($scorecard) . "</dd>";
                                            
                                        }
                                       
                                    }
                                    ?>

								
							

								<br>
								<br>
								<dt><b class="border-bottom border-primary">Work Types</b></dt>
								<?php if (isset($taskIdz) && is_array($taskIdz) && count($taskIdz) > 0): ?>
    <?php foreach ($taskIdz as $index => $taskId): ?>
        <dd>
            <a href="index.php?page=view_work_type&id=<?php echo $taskId; ?>">
                <?php echo html_entity_decode($taskNames[$index]); ?>
            </a>
        </dd>
	
    <?php endforeach; ?>
<?php else: ?>
    <p>No work types available!</p>
<?php endif; ?>
							</dl>
							<!-- Display the file_path and add the download button (if available) -->
						<?php if (!empty($filesResult)): ?>
    <dt><b class="border-bottom border-primary">File</b></dt>
    <br>
    <dd>
        <!-- Add the download button -->

<?php
$files = $filesResult->fetch_all(MYSQLI_ASSOC);

foreach ($files as $file) {
    $url = $file["url"];
    $filePath = "../TIMS/STORAGE/FILES/$url";

    // Check if the URL contains "Response_document"
    if (strpos($url, "Response_document") === false) {
        echo "<a style='padding:3.5px;' href='$filePath' target='_blank'>
                <img src='../TIMS/Images/file.png' height='50' width='50'>
              </a>";
    }
}
?>

    </dd>
<?php endif; ?>

						</div>
						
						<div class="col-md-6">
							<dl>
								<dt><b class="border-bottom border-primary">Start Date</b></dt>
								<dd><?php echo date("F d, Y",strtotime($start_date)) ?></dd>
							</dl>
							<dl>
								<dt><b class="border-bottom border-primary">End Date</b></dt>
								<dd><?php echo date("F d, Y",strtotime($end_date)) ?></dd>
							</dl>
							<dl>
								<dt><b class="border-bottom border-primary">Status</b></dt>
								<dd>
									<?php echo "<span class='badge badge-info'>{$status}</span>";?>
								</dd>
							</dl>
							<dl>
								<dt><b class="border-bottom border-primary">Project Manager</b></dt>
								<dd>
									<?php if(isset($manager['id'])) : ?>
									<div class="d-flex align-items-center mt-1">
										<img class="img-circle img-thumbnail p-0 shadow-sm border-info img-sm mr-3" src="assets/uploads/<?php echo $manager['avatar'] ?>" alt="Avatar">
										<b><?php echo ucwords($manager['name']) ?></b>
									</div>
									<?php else: ?>
										<small><i>Manager Deleted from Database</i></small>
									<?php endif; ?>
								</dd>
								<br>
								<br>
								<div>
								<dt><b class="border-bottom border-primary">Team Members</b></dt>
								<div><?php foreach ($userNames as $userName): ?>
									<span><?php echo html_entity_decode($userName.","); ?></span>
								<?php endforeach; ?>
								</div>		
							
							</div>
							<br>
							<dl>
								<dt><b class="border-bottom border-primary">Client</b></dt>
								
								<?php
								
									$qry = $conn->query("SELECT DISTINCT COALESCE(c.company_name, smme.Legal_name) as company_name
FROM project_list pl
LEFT JOIN yasccoza_openlink_market.client c ON c.CLIENT_ID = pl.CLIENT_ID
LEFT JOIN yasccoza_openlink_smmes.register smme ON pl.CLIENT_ID = smme.SMME_ID WHERE pl.id = " . $_GET['id']);

									if ($qry) {
										$row = $qry->fetch_object();  // Fetch a row from the result set as an object
										if ($row) {
											echo "<dd>" . html_entity_decode($row->company_name) . "</dd>";
										}
									}
									?>
							</dl>

							<dl>
								<dt><b class="border-bottom border-primary">Client REP</b></dt>
								
								<?php

								if($status=="Done"){

									$qry = $conn->query("SELECT REP_NAME, REP_EMAIL, REP_CONTACT 
														FROM client_rep, project_list 
														WHERE client_rep.REP_ID = project_list.CLIENT_REP 
														AND project_list.ID = " . $_GET['id']);

									if ($qry) {
										$row = $qry->fetch_object();  // Fetch a row from the result set as an object
										if ($row) {
											echo "<dd>Name: " . html_entity_decode($row->REP_NAME) . "</dd>";
											echo "<dd>Email: " . html_entity_decode($row->REP_EMAIL) . "</dd>";
											echo "<dd>Phone: " . html_entity_decode($row->REP_CONTACT) . "</dd>";
										}
									}

								} else{
								
									echo "<dd style='color:red'>Available upon job completion !</dd>";
								}
									?>
							</dl>
								
							
								
							</dl>
						</div>
					</div>
				</div>
				<div class="col-md-12">
				<div class="row">
				<dl>

				<dt><b class="border-bottom border-primary">Description</b></dt>
								<dd style="word-wrap: break-word;"><?php echo html_entity_decode($description) ?></dd>

				</dl>
                 
				</div>
				<a href="../TIMS/ADMIN/job_order_info.php?d=10017&q=1&p=14&userid=<?php echo $login_id?>">Respond to Job</a>
				</div>
			</div>
		</div>
	</div>
	
	
</div>

<div class="col-lg-12">
    <div class="row">
        <div class="col-md-12">
            <div class="callout callout-info">
                <div class="col-md-12">
                    <div class="row">
                        <?php if (isset($taskIdz) && is_array($taskIdz) && count($taskIdz) > 0): ?>
                            <?php foreach ($taskIdz as $index => $taskId): ?>
                                <div class="col-md-6">
                                    <label><?php echo html_entity_decode($taskNames[$index]); ?></label>
									<div class="form-group" style="padding-left:20px; border:1px solid #17A2BB">
    <label>Activities for work type</label>
    <?php
    $qry = $conn->query("SELECT name FROM user_productivity WHERE task_id = $taskId");
    if ($qry) {
        $names = array(); // Store names in an array

        while ($row = $qry->fetch_assoc()) {

			
            $names[] = $row['name'];
        }

        $namesString = implode(', ', $names); // Join names with commas
        echo "<p class='form-control-plaintext'>$namesString</p>";
    } else {
        echo "Query failed: " . $conn->error;
    }
    ?>
</div>

                                </div>
                            <?php endforeach; ?>

                            <div class="col-md-12">
                                <br>
                                <p class="form-control-plaintext" style="font-weight: bold;">Members Assigned to those work activities</p>

								<?php
// Assuming you've already established a database connection and assigned it to $conn.
// Replace with your project ID using $_GET['id']
$projectId = $_GET['id'];

// Check if the project ID is set and not empty
if (!empty($projectId)) {
                // Query to select user last names and first names where activity_ids match
                $query = "SELECT DISTINCT CONCAT(firstname, ' ',lastname ) AS full_name, name FROM users 
                          INNER JOIN assigned_duties ON users.id = assigned_duties.user_id 
                          INNER JOIN user_productivity ON user_productivity.id = assigned_duties.activity_id
                          WHERE assigned_duties.project_id = $projectId";

                $result = $conn->query($query);


                if ($result) {

					
                    // Loop through the results and display the full names
                    while ($row = $result->fetch_assoc()) {

							echo "<div class='form-group col-6' style='border:0.5px solid #17A2BB'><p class='form-control-plaintext'><span style='font-weight:bold'>" . $row['name'] . "</span> " . $row['full_name'] . "</p></div>";

                    }
                } else {
                    die('Query error: ' . mysqli_error($conn));
                }
            }
          
?>




                            </div>

                        <?php else: ?>
                            <p>No duties Assigned !</p>
                        <?php endif; ?>
                    </div>
                </div>
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
	$('#new_task').click(function(){
		uni_modal("New Task For <?php echo ucwords($name) ?>","manage_task.php?pid=<?php echo $id ?>","mid-large")
	})
	$('.edit_task').click(function(){
		uni_modal("Edit Task: "+$(this).attr('data-task'),"manage_task.php?pid=<?php echo $id ?>&id="+$(this).attr('data-id'),"mid-large")
	})
	$('.view_task').click(function(){
		uni_modal("Task Details","view_task.php?id="+$(this).attr('data-id'),"mid-large")
	})
	$('#new_productivity').click(function(){
		uni_modal("<i class='fa fa-plus'></i> New Progress","manage_progress.php?pid=<?php echo $id ?>",'large')
	})
	$('.manage_progress').click(function(){
		uni_modal("<i class='fa fa-edit'></i> Edit Progress","manage_progress.php?pid=<?php echo $id ?>&id="+$(this).attr('data-id'),'large')
	})
	$('.delete_progress').click(function(){
	_conf("Are you sure to delete this progress?","delete_progress",[$(this).attr('data-id')])
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
