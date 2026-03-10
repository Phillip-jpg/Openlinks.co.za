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


<style>
    .job-view-modern {
        --surface: #ffffff;
        --ink: #0f172a;
        --muted: #64748b;
        --line: #dbe7f5;
        --brand-1: #0f4c81;
        --brand-2: #0b7db5;
        --brand-3: #5eb3f3;
        margin-bottom: 1.25rem;
    }

    .job-view-modern .back-nav-btn {
        border: 0;
        border-radius: 999px;
        padding: 0.44rem 1rem;
        font-size: 0.8rem;
        font-weight: 600;
        background: linear-gradient(125deg, var(--brand-1), var(--brand-2));
        box-shadow: 0 8px 18px rgba(11, 125, 181, 0.26);
    }

    .job-view-modern .back-nav-btn:hover {
        transform: translateY(-1px);
    }

    .job-view-modern .job-panel {
        border: 1px solid var(--line);
        border-radius: 18px;
        background: linear-gradient(140deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
        padding: 1.2rem 1.1rem;
    }

    .job-view-modern .job-id-title {
        margin-bottom: 0.7rem;
        color: var(--brand-2);
        font-weight: 700;
        font-size: 0.98rem;
    }

    .job-view-modern dl {
        margin-bottom: 0.95rem;
    }

    .job-view-modern dd {
        color: #334155;
        margin-bottom: 0.45rem;
    }

    .job-view-modern .border-bottom.border-primary {
        border-bottom: 1px solid var(--line) !important;
        border-color: var(--line) !important;
        color: #1e3a5f;
        display: inline-block;
        font-size: 0.76rem;
        letter-spacing: 0.05em;
        padding-bottom: 0.2rem;
        text-transform: uppercase;
    }

    .job-view-modern a {
        color: var(--brand-2);
    }

    .job-view-modern a:hover {
        color: var(--brand-1);
        text-decoration: none;
    }

    .job-view-modern .badge {
        border-radius: 999px;
        font-size: 0.73rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        padding: 0.38em 0.72em;
    }

    .job-view-modern .badge-info {
        background: #dff3ff;
        color: #075985;
    }

    .job-view-modern .manager-avatar {
        width: 56px;
        height: 56px;
        border: 2px solid #dbeafe !important;
        object-fit: cover;
    }

    .job-view-modern .team-members {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        margin-top: 0.25rem;
    }

    .job-view-modern .member-chip {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 999px;
        color: #1e3a8a;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0.24rem 0.62rem;
    }

    .job-view-modern .file-link {
        align-items: center;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 12px;
        box-shadow: 0 5px 14px rgba(15, 23, 42, 0.08);
        display: inline-flex;
        height: 58px;
        justify-content: center;
        margin-right: 0.45rem;
        width: 58px;
    }

    .job-view-modern .file-link img {
        height: 34px;
        width: 34px;
    }

    .job-view-modern .description-text {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-top: 0.35rem;
        padding: 0.85rem 0.95rem;
        white-space: pre-wrap;
        word-break: break-word;
    }

    .job-view-modern .respond-job-btn {
        display: inline-flex;
        align-items: center;
        border: 0;
        border-radius: 999px;
        padding: 0.5rem 1rem;
        font-size: 0.83rem;
        font-weight: 600;
        margin-top: 0.4rem;
        background: linear-gradient(120deg, var(--brand-1), var(--brand-2));
        box-shadow: 0 8px 20px rgba(11, 125, 181, 0.28);
        color: #fff !important;
    }

    .job-view-modern .client-rep-pending {
        color: #dc2626;
        font-weight: 600;
    }

    .job-view-modern .activity-group-title {
        color: var(--ink);
        display: block;
        font-size: 0.95rem;
        font-weight: 700;
        margin-bottom: 0.45rem;
    }

    .job-view-modern .activity-card {
        border: 1px solid var(--line);
        border-radius: 14px;
        background: #f8fbff;
        margin-bottom: 0.95rem;
        padding: 0.75rem 0.9rem;
    }

    .job-view-modern .activity-label {
        color: var(--muted);
        display: block;
        font-size: 0.74rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        margin-bottom: 0.45rem;
        text-transform: uppercase;
    }

    .job-view-modern .activity-text {
        color: #334155;
        margin: 0;
    }

    .job-view-modern .members-title {
        color: #1e293b;
        font-weight: 700;
        margin: 0.3rem 0 0.85rem;
    }

    .job-view-modern .member-activity-item {
        border: 1px solid var(--line);
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
        margin-bottom: 0.6rem;
        padding: 0.55rem 0.75rem;
    }

    .job-view-modern .member-activity-item .activity-name {
        color: var(--brand-1);
        font-weight: 700;
        margin-right: 0.22rem;
    }

    .job-view-modern .empty-note {
        color: #94a3b8;
        font-style: italic;
        margin-bottom: 0.6rem;
    }

    .job-view-modern .users-list > li img {
        border-radius: 50%;
        height: 67px;
        object-fit: cover;
        width: 67px;
    }

    .job-view-modern .users-list > li {
        width: 33.33% !important;
    }

    .job-view-modern .truncate {
        -webkit-line-clamp: 1 !important;
    }

    @media (max-width: 768px) {
        .job-view-modern .job-panel {
            border-radius: 14px;
            padding: 0.95rem 0.85rem;
        }

        .job-view-modern .activity-group-title {
            font-size: 0.88rem;
        }

        .job-view-modern .member-activity-item {
            margin-bottom: 0.5rem;
            padding: 0.5rem 0.65rem;
        }
    }

    /* Readability overrides */
    .job-view-modern {
        font-size: 0.98rem;
    }

    .job-view-modern .border-bottom.border-primary {
        font-size: 0.82rem;
    }

    .job-view-modern dd,
    .job-view-modern .activity-text,
    .job-view-modern .description-text {
        font-size: 0.94rem;
    }

    .job-view-modern .activity-label,
    .job-view-modern .member-chip {
        font-size: 0.84rem;
    }

    .job-view-modern .badge,
    .job-view-modern .respond-job-btn,
    .job-view-modern .back-nav-btn {
        font-size: 0.84rem;
    }
</style>

<div class="col-lg-12 job-view-modern">
    <?php if ($backPage !== ''): ?>
    <div class="mb-3">
        <a href="./index.php?page=<?php echo htmlspecialchars($backPage, ENT_QUOTES, 'UTF-8'); ?><?php echo (in_array($backPage, ['jobs_to_manage_level1', 'my_team_jobs_to_manage_lvl_1'], true) && $backTeamRef !== '') ? '&team_id=' . rawurlencode($backTeamRef) : ''; ?>" class="btn btn-primary btn-sm back-nav-btn">
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
			<div class="callout callout-info job-panel">
				<div class="col-md-12">
					<div class="row">
						<div class="col-sm-6">
							<dl>
							  <p class="job-id-title">Job ID: <?php echo ucwords($id) ?></p>
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
    <p class="empty-note">No work types available!</p>
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
        echo "<a class='file-link' href='$filePath' target='_blank' rel='noopener noreferrer'>
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
										<img class="img-circle img-thumbnail p-0 shadow-sm border-info img-sm mr-3 manager-avatar" src="assets/uploads/<?php echo $manager['avatar'] ?>" alt="Avatar">
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
								<div class="team-members"><?php foreach ($userNames as $userName): ?>
									<span class="member-chip"><?php echo html_entity_decode($userName); ?></span>
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
								
									echo "<dd class='client-rep-pending'>Available upon job completion !</dd>";
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
								<dd class="description-text"><?php echo html_entity_decode($description) ?></dd>

				</dl>
                 
				</div>
				<a class="respond-job-btn" href="../TIMS/ADMIN/job_order_info.php?d=10017&q=1&p=14&userid=<?php echo $login_id?>">Respond to Job</a>
				</div>
			</div>
		</div>
	</div>
	
	
</div>

<div class="col-lg-12 job-view-modern">
    <div class="row">
        <div class="col-md-12">
            <div class="callout callout-info job-panel">
                <div class="col-md-12">
                    <div class="row">
                        <?php if (isset($taskIdz) && is_array($taskIdz) && count($taskIdz) > 0): ?>
                            <?php foreach ($taskIdz as $index => $taskId): ?>
                                <div class="col-md-6">
                                    <label class="activity-group-title"><?php echo html_entity_decode($taskNames[$index]); ?></label>
									<div class="form-group activity-card">
    <label class="activity-label">Activities for work type</label>
    <?php
    $qry = $conn->query("SELECT name FROM user_productivity WHERE task_id = $taskId");
    if ($qry) {
        $names = array(); // Store names in an array

        while ($row = $qry->fetch_assoc()) {

			
            $names[] = $row['name'];
        }

        $namesString = implode(', ', $names); // Join names with commas
        echo "<p class='form-control-plaintext activity-text'>$namesString</p>";
    } else {
        echo "Query failed: " . $conn->error;
    }
    ?>
</div>

                                </div>
                            <?php endforeach; ?>

                            <div class="col-md-12">
                                <br>
                                <p class="form-control-plaintext members-title">Members Assigned to those work activities</p>

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

							echo "<div class='form-group col-6 member-activity-item'><p class='form-control-plaintext'><span class='activity-name'>" . $row['name'] . "</span> " . $row['full_name'] . "</p></div>";

                    }
                } else {
                    die('Query error: ' . mysqli_error($conn));
                }
            }
          
?>




                            </div>

                        <?php else: ?>
                            <p class="empty-note">No duties Assigned !</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

	</div>
	
	
</div>
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
