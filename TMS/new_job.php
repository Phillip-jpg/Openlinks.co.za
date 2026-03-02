<?php if(!isset($conn)){ include 'db_connect.php'; } 

$pm_url_secret = 'my_app_secret_key';

if (!function_exists('encodePmIdForUrl')) {
  function encodePmIdForUrl(int $pmId, string $secret): string
  {
    $payload = (string)$pmId;
    $hash = hash_hmac('sha256', $payload, $secret);
    return base64_encode($payload . ':' . $hash);
  }
}

if (!function_exists('decodePmIdFromUrl')) {
  function decodePmIdFromUrl($value, string $secret): int
  {
    if (!isset($value) || $value === '') {
      return 0;
    }

    $raw = (string)$value;

    // Backward compatibility for old plain numeric pm_id links.
    if (ctype_digit($raw)) {
      return (int)$raw;
    }

    $decoded = base64_decode($raw, true);
    if ($decoded === false) {
      return 0;
    }

    $parts = explode(':', $decoded, 2);
    if (count($parts) !== 2 || !ctype_digit($parts[0])) {
      return 0;
    }

    [$pid, $sig] = $parts;
    $expected = hash_hmac('sha256', $pid, $secret);

    if (!hash_equals($expected, $sig)) {
      return 0;
    }

    return (int)$pid;
  }
}

// -------------------------------
// NEW: Selected PM (project manager) ID (drives the other queries)
// Priority: existing $manager_id (edit) -> GET pm_id (page reload) -> POST manager_id (if any) -> 0
// -------------------------------
$project_manager_id = 0;
if (isset($manager_id) && $manager_id !== '') {
  $project_manager_id = (int)$manager_id;
} elseif (isset($_GET['pm_id'])) {
  $project_manager_id = decodePmIdFromUrl($_GET['pm_id'], $pm_url_secret);
} elseif (isset($_POST['manager_id'])) {
  $project_manager_id = (int)$_POST['manager_id'];
}

$project_manager_id = max(0, (int)$project_manager_id);
$has_selected_pm = $project_manager_id > 0;
$current_project_id = isset($id) ? (int)$id : 0;

$currentDate = new DateTime();
$currentDate->setISODate($currentDate->format('o'), $currentDate->format('W'), 1);
$startOfWeek = $currentDate->format('Y-m-d');

// Calculate the end date of the current week (Friday)
$endOfWeekObj = clone $currentDate;
$endOfWeekObj->modify('+4 days');
$endOfWeek = $endOfWeekObj->format('Y-m-d');

// Fetch task details based on the task ID (assuming the task ID is available in the $id variable)
if (isset($id) && !empty($id)) {
  $sql = "SELECT * FROM project_list WHERE id = $id";
  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
      $row = $result->fetch_assoc();

      if (!empty($row['file_path'])) {
          $existing_file = $row['file_path'];
      } else {
          $existing_file = "";
      }
  } 
}
?>

<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-body">
			<form action="" id="manage-project" enctype="multipart/form-data">
        <input type="hidden" name="creator_id" value="<?php echo (int)$_SESSION['login_id'] ?>">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="" class="control-label">PM In Charge of The Job</label>

              <!-- CHANGED: add id="manager_id" and make selection drive $project_manager_id -->
              <select required class="form-control form-control-sm select2" name="manager_id" id="manager_id">
                <option value="0"></option>

                <?php 
                  if($_SESSION['login_type']==3){
                    $managers = $conn->query("SELECT *,
                      CONCAT(firstname, ' ', lastname) AS name
                      FROM users
                      WHERE id IN (
                          SELECT creator_id
                          FROM users
                          WHERE id = {$_SESSION['login_id']}
                      );");
                  } elseif($_SESSION['login_type']==2){
                    $managers = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where type = 2 and users.id={$_SESSION['login_id']} order by concat(firstname,' ',lastname) asc ");
                  } else {
                    $managers = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users order by concat(firstname,' ',lastname) asc ");
                  }

	                  while($row= $managers->fetch_assoc()):
	                    $mid = (int)$row['id'];
                      $encoded_pm_id = encodePmIdForUrl($mid, $pm_url_secret);
	                ?>
	                  <option value="<?php echo $mid ?>"
                      data-enc-pm="<?php echo htmlspecialchars($encoded_pm_id, ENT_QUOTES, 'UTF-8') ?>"
	                    <?php echo ($project_manager_id === $mid) ? "selected" : '' ?>>
	                    <?php echo ucwords($row['name']) ?>
	                  </option>
	                <?php endwhile; ?>
              </select>
            </div>
          </div>
          
          
          

          <div class="col-md-6">
            <div class="form-group">
              <label for="">Status</label>
              <select name="status" id="status" class="custom-select custom-select-sm">
                <option value="In-progress" <?php echo isset($status) && $status == 'In-progress' ? 'selected' : '' ?>>In-progress</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="" class="control-label">Start Date</label>
              <input required type="date" class="form-control form-control-sm" autocomplete="off" name="start_date" value="<?php echo isset($start_date) ? date("Y-m-d",strtotime($start_date)) : '' ?>">
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label for="" class="control-label">End Date</label>
              <input required type="date" class="form-control form-control-sm" autocomplete="off" name="end_date" value="<?php echo isset($end_date) ? date("Y-m-d",strtotime($end_date)) : '' ?>">
            </div>
          </div>
        </div>

        <div class="row">

          
            <div class="col-md-6">
            <div class="form-group">
              <label for="" class="control-label">Name</label>
              <input hidden type="date" class="form-control form-control-sm" name="endOfWeek" id="endOfWeek" value="<?php echo isset($endOfWeek) ? $endOfWeek : '' ?>">
              <input hidden type="date" class="form-control form-control-sm" name="startOfWeek" id="startOfWeek" value="<?php echo isset($startOfWeek) ? $startOfWeek : '' ?>">
              <input required type="text" class="form-control form-control-sm" name="name" value="<?php echo isset($name) ? $name : '' ?>">
              <input type="text" class="form-control form-control-sm" hidden name="user_ids" value="0">
            </div>
          </div>
          
	          <div class="col-md-6">
	              <div class="form-group">
	                <label for="team_ids" class="control-label">Team Onboarding Job</label>
	                <select required class="form-control form-control-sm select2" name="team_ids">
	                  <?php
	                  
	                  $pmId = (int)$project_manager_id;
	                  // Fetch the selected team name for edit mode.
	                  $result = false;
	                  if ($current_project_id > 0) {
	                    $stmt = $conn->prepare("
	                      SELECT DISTINCT ts.team_name, ts.team_id
	                      FROM team_schedule ts
	                      JOIN project_list pl ON pl.team_ids = ts.team_id
	                      WHERE pl.id = ?
	                    ");
	                    if ($stmt) {
	                      $stmt->bind_param("i", $current_project_id);
	                      $stmt->execute();
	                      $result = $stmt->get_result();
	                      $stmt->close();
	                    }
	                  }
	            
	                  // Initialize variables
	                  $selected_team = "";
	                  $selected_team_id = "";
            
                  if ($result && $result->num_rows > 0) {
                    $wow = $result->fetch_object();
                    $selected_team = $wow->team_name;  // Store the selected team's name
	                    $selected_team_id = $wow->team_id; // Store the selected team's ID
	                  }

	                  if (!$has_selected_pm) {
	                    echo '<option value="">Select PM first</option>';
	                  } else {
	                    if ($selected_team_id !== '') {
	                      echo '<option value="' . htmlspecialchars($selected_team_id) . '" selected>' .
	                        htmlspecialchars($selected_team) .
	                        '</option>';
	                    } else {
	                      echo '<option value="">Select Team</option>';
	                    }

	                    // Fetch teams for the dropdown
	                    $employees = $conn->query("
	                    SELECT DISTINCT
	                        ts_pm.team_name AS t_n,
	                        ts_pm.team_id
	                      FROM team_schedule ts_pm
	                      JOIN team_schedule ts_me
	                        ON ts_me.team_id = ts_pm.team_id
	                      LEFT JOIN schedule_work_team swt
	                        ON swt.Work_Team = ts_pm.team_id
	                      WHERE ts_pm.team_members = $pmId
	                        AND ts_me.team_members = {$_SESSION['login_id']}
	                        AND CURDATE() BETWEEN swt.startweek AND swt.endweek;

	                    ");
	              
	                    if ($employees && $employees->num_rows > 0) {
	                      while ($row = $employees->fetch_assoc()){
	                        if ((string)$row['team_id'] === (string)$selected_team_id) {
	                          continue;
	                        }
	                        echo '<option value="' . htmlspecialchars($row['team_id']) . '">' .
	                          htmlspecialchars(ucwords($row['t_n'])) .
	                          '</option>';
	                      }
	                    }
	                  }
	                  ?>
	                </select>
	              </div>
	            </div>

        </div>

        <div class="row">

          <div class="col-md-6">
            <div class="form-group">
              <label for="" class="control-label">Offices</label>
              <select name="OFFICE_ID" id="officez" class="custom-select custom-select-sm">
                <option value="0">SELECT NONE</option>
                <?php 
                $result = $conn->query("SELECT a.office 
                  FROM project_list p
                  JOIN yasccoza_openlink_association_db.industry a ON a.INDUSTRY_ID = p.OFFICE_ID
                  WHERE p.id = $id");

                $selected_office = "";
                if ($result && $result->num_rows > 0) {
                  $car = $result->fetch_object();
                  $selected_office = $car->office;
                }

                $smmes = $conn->query("SELECT * FROM yasccoza_openlink_association_db.industry");
                if ($smmes && $smmes->num_rows > 0) {
                  while ($row = $smmes->fetch_assoc()) {
                    $selected_attr = ($selected_office == $row['office']) ? "selected" : '';
                    echo '<option value="' . htmlspecialchars($row['INDUSTRY_ID']) . '" ' . $selected_attr . '>';
                    echo htmlspecialchars(ucwords($row['office']));
                    echo '</option>';
                  }
                } else {
                  echo '<option disabled>No companies found</option>';
                }
                ?>
              </select>
            </div> 	
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label for="COMPANY" class="control-label">Send job to:</label>
              <select name="COMPANY" class="custom-select custom-select-sm">
                <?php 
                if($_SESSION['login_type']==1){
                  echo"<option value='0' style='color: red;'>All COMPANIES</option>";

                  $result = $conn->query("SELECT r.Legal_name 
                    FROM project_list p
                    JOIN yasccoza_openlink_smmes.register r ON r.SMME_ID = p.COMPANY
                    WHERE p.id = $id");

                  $selected_title = "";
                  if ($result && $result->num_rows > 0) {
                    $car = $result->fetch_object();
                    $selected_title = $car->Legal_name;
                  }

                  $smmes = $conn->query("SELECT * FROM yasccoza_openlink_smmes.register");
                }

                if ($smmes && $smmes->num_rows > 0) {
                  while ($row = $smmes->fetch_assoc()) {
                    $selected_attr = ($selected_title == $row['Legal_name']) ? "selected" : '';
                    echo '<option value="' . htmlspecialchars($row['SMME_ID']) . '" ' . $selected_attr . '>';
                    echo htmlspecialchars(ucwords($row['Legal_name']));
                    echo '</option>';
                  }
                } else {
                  echo '<option disabled>No companies found</option>';
                }
                ?>
              </select>
            </div>
          </div>

        </div>

        <div class="row">

	          <div class="col-md-6">
	            <div class="form-group">
	              <label for="" class="control-label">Scorecard</label>

	              <select required name="scorecard" id="scorecards" class="custom-select custom-select-sm">
	                <option value=""><?php echo $has_selected_pm ? 'Select scorecard' : 'Select PM first'; ?></option>
	                <?php 
	                $result = false;
	                if ($current_project_id > 0) {
	                  $result = $conn->query("SELECT s.Title 
	                    FROM project_list p
	                    JOIN yasccoza_openlink_market.scorecard s ON s.SCORECARD_ID = p.scorecard
	                    WHERE p.id = $current_project_id");
	                }

	                if ($result && $result->num_rows > 0) {
	                  $card = $result->fetch_object();
	                  $selected_title = $card->Title;
                } else {
                  $selected_title = "";
                }

	                // CHANGED: use selected PM id -> $project_manager_id
	                $pmId = (int)$project_manager_id;

	                $scorecards = false;
	                if ($pmId > 0) {
	                  if($_SESSION['login_type']==3){
	                    $scorecards = $conn->query("
	                      SELECT DISTINCT
	                        s.SCORECARD_ID,
	                        s.Title
	                      FROM yasccoza_openlink_market.scorecard s
	                      LEFT JOIN scorecards_project sp
	                        ON sp.scorecard_id = s.SCORECARD_ID
	                      WHERE sp.project_manager_id=$pmId
	                      
	                    ");
	                  } elseif($_SESSION['login_type']==2){
	                    $scorecards = $conn->query("SELECT s.SCORECARD_ID, s.Title FROM yasccoza_openlink_market.scorecard s join scorecards_project sp on sp.scorecard_id=s.SCORECARD_ID WHERE sp.project_manager_id={$_SESSION['login_id']}");
	                  } else {
	                    $scorecards = $conn->query("SELECT SCORECARD_ID, Title FROM yasccoza_openlink_market.scorecard");
	                  }
	                } else {
	                  $scorecards = false;
	                }

	                if ($scorecards && $scorecards->num_rows > 0) {
	                  while ($row = $scorecards->fetch_assoc()): ?>
                    <option value="<?php echo $row['SCORECARD_ID'] ?>" <?php echo ($selected_title == $row['Title']) ? "selected" : ''; ?>>
                      <?php echo ucwords($row['Title']) ?>
                    </option>
                  <?php endwhile;
                }
                ?>
              </select>
            </div>
          </div>
          
          

	          <div class="col-md-6">
	            <div class="form-group">
	              <label for="" class="control-label">Client</label>

	              <!-- CHANGED: give this select a stable id="client_id" (so PM can drive it cleanly if needed later) -->
	              <select name="CLIENT_ID" id="client_id" class="custom-select custom-select-sm">
	                <option value=""><?php echo $has_selected_pm ? 'Select client' : 'Select PM first'; ?></option>
	                <?php 
	                // Selected client (edit)
	                $result = false;
	                if ($current_project_id > 0) {
	                  $result = $conn->query("SELECT company_name 
	                    FROM project_list 
	                    INNER JOIN yasccoza_openlink_market.client ON project_list.CLIENT_ID = client.CLIENT_ID 
	                    WHERE project_list.id = $current_project_id and yasccoza_openlink_market.client.creator_id={$_SESSION['login_id']}");
	                }
	                if ($result && $result->num_rows > 0) {
	                  $client = $result->fetch_object();
	                  $selected_company_name = $client->company_name;
                } else {
                  $selected_company_name = "";
                }

	                // CHANGED: use selected PM id -> $project_manager_id for login_type 3
	                $pmId = (int)$project_manager_id;

	                $clients = false;
	                if ($pmId > 0) {
	                  if($_SESSION['login_type']==3){
	                    $clients = $conn->query("SELECT * FROM yasccoza_openlink_market.client where yasccoza_openlink_market.client.creator_id=$pmId");
	                  } elseif($_SESSION['login_type']==2){
	                    $clients = $conn->query("SELECT * FROM yasccoza_openlink_market.client where yasccoza_openlink_market.client.creator_id={$_SESSION['login_id']}");
	                  } else {
	                    $clients = $conn->query("SELECT * FROM yasccoza_openlink_market.client");
	                  }
	                } else {
	                  $clients = false;
	                }

	                if ($clients && $clients->num_rows > 0) {
	                  while ($row = $clients->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['CLIENT_ID']) ?>" <?php echo ($selected_company_name == $row['company_name']) ? "selected" : ''; ?>>
                      <?php echo ucwords(htmlspecialchars($row['company_name'])) . ' (' . htmlspecialchars($row['CLIENT_ID']) . ')'; ?>
                    </option>
                  <?php endwhile;
                }
                ?>
              </select>
            </div>
          </div>

        </div>

        <div class="row">

          <div class="col-md-6">
            <div class="form-group">
	              <label for="" class="control-label">Job Type</label>

	              <select class="form-control form-control-sm select2" name="JOB_TYPE" required>
	                <!-- <option value="0"><?php echo $has_selected_pm ? 'Select job type' : 'Select PM first'; ?></option> -->
	                <?php 
	                $login_id   = (int) $_SESSION['login_id'];
	                $login_type = (int) $_SESSION['login_type'];
	                $pmId = (int)$project_manager_id;

	                // CHANGED: for type 2 + type 3 cases that referenced project_manager_id / project_manager
	                if ($pmId > 0 && $login_type !== 1) {
	                  $sql = "
	                    SELECT * 
	                    FROM job_type 
	                    WHERE creator_id = $pmId
	                  ";
	                
	                } elseif ($pmId > 0 && $login_type === 1) {
	                  $sql = "SELECT * FROM job_type";
	                } else {
	                  $sql = "SELECT * FROM job_type WHERE 1=0";
	                }

                $job_type_query = $conn->query($sql);
                if ($job_type_query):
                  while ($row = $job_type_query->fetch_assoc()):
                ?>
                  <option 
                    value="<?php echo htmlspecialchars($row['job_type_name']); ?>" 
                    <?php echo (isset($JOB_TYPE) && $JOB_TYPE == $row['job_type_name']) ? "selected" : ""; ?>>
                    <?php echo htmlspecialchars($row['job_type_name']); ?>
                  </option>
                <?php 
                  endwhile;
                endif;
                ?>
              </select>

            <div class="form-group">
                <label for="" class="control-label">Add Work Type</label>

                <select required class="form-control form-control-sm select2" multiple="multiple" name="task_ids[]">
                  <option></option>
                  <?php 
                  $login_id = (int) $_SESSION['login_id'];
                  $login_type = (int) $_SESSION['login_type'];
                  $pmId = (int)$project_manager_id;

                  // CHANGED: use selected PM id -> $project_manager_id
	                  if ($pmId <= 0) {
	                    $query = "SELECT tl.* FROM task_list tl WHERE 1=0";
	                  } elseif ($login_type == 2) {
	                    $query = "
	                      SELECT tl.*
	                      FROM task_list tl
	                      WHERE tl.creator_id = $pmId
	                      ORDER BY tl.task_name;
	                    ";
	                  } elseif($login_type == 3) {
	                     $query = "
	                      SELECT DISTINCT tl.*
	                      FROM task_list tl
	                      INNER JOIN members_and_worktypes mw
	                        ON mw.work_type_id = tl.id
	                      WHERE mw.member_id = $login_id
	                        AND tl.creator_id = $pmId
	                      ORDER BY tl.task_name;
	                    ";
	                  } else {
	                    $query = "
	                      SELECT tl.*
	                      FROM task_list tl
	                      ORDER BY tl.task_name;
	                    ";
	                  }

                  $taskList = $conn->query($query);
                  while ($row = $taskList->fetch_assoc()):
                  ?>
                    <option 
                      value="<?php echo $row['id']; ?>" 
                      <?php echo isset($task_ids) && in_array($row['id'], explode(',', $task_ids)) ? 'selected' : ''; ?>>
                      <?php echo ucwords($row['task_name']); ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>

            </div>
          </div>

	          <div class="col-md-6">
	            <div class="form-group" id="industryDropdownDiv">
	              <label for="" class="control-label">CLIENT REP</label>
	              <select required name="CLIENT_REP" id="industries" class="custom-select custom-select-sm" data-selected-rep="<?php echo isset($CLIENT_REP) ? (int)$CLIENT_REP : 0; ?>">
	                <?php
	                $selectedClientId = isset($CLIENT_ID) ? (int)$CLIENT_ID : 0;
	                $selectedRepId = isset($CLIENT_REP) ? (int)$CLIENT_REP : 0;
	                ?>
	                <option value=""><?php echo $selectedClientId > 0 ? 'Select client rep' : 'Select client first'; ?></option>
	                <?php
	                if ($selectedClientId > 0) {
	                  $stmt = $conn->prepare("SELECT REP_ID, REP_NAME FROM client_rep WHERE CLIENT_ID = ? ORDER BY REP_NAME ASC");
	                  if ($stmt) {
	                    $stmt->bind_param("i", $selectedClientId);
	                    if ($stmt->execute()) {
	                      $result = $stmt->get_result();
	                      while ($row = $result->fetch_assoc()) {
	                        $repId = (int)$row['REP_ID'];
	                        $isSelectedRep = ($selectedRepId > 0 && $selectedRepId === $repId) ? 'selected' : '';
	                        echo "<option value='" . $repId . "' " . $isSelectedRep . ">" . htmlspecialchars($row['REP_NAME']) . "</option>";
	                      }
	                    }
	                    $stmt->close();
	                  }
	                }
	                ?>
	              </select>
	            </div>

            <div class="form-group">
              <label for="" class="control-label">Upload Job Files</label>
              <div id="file-input-container">
                <input type="file" id="files_input" name="files[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                <small class="form-text text-muted">Max size: 5MB per file.</small>
              </div>
              <button type="button" class="btn btn-primary mt-2" onclick="addFileInput()">Add Another File</button>
            </div>

            <div class="mt-3">
              <?php if (!empty($existing_files)): ?>
                <p><strong>Existing Files:</strong></p>
                <ul>
                  <?php foreach ($existing_files as $existing_file): ?>
                    <li><?php echo $existing_file; ?></li>
                  <?php endforeach; ?>
                </ul>
              <?php else: ?>
                <p><strong>Existing Files:</strong> <span style="color:red">No existing files</span></p>
              <?php endif; ?>
            </div>

          </div>
        </div>

        <div class="row">
          <div class="col-md-10">
            <div class="form-group">
              <label for="" class="control-label">Description</label>
              <textarea name="description" id="" cols="30" rows="10" class="summernote form-control">
                <?php echo isset($description) ? $description : '' ?>
              </textarea>
            </div>
          </div>
        </div>

      </form>
    </div>

    <div class="card-footer border-top border-info">
      <div class="d-flex w-100 justify-content-center align-items-center">
        <button class="btn btn-flat bg-gradient-primary mx-2" form="manage-project">Save</button>
        <button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href='index.php?page=Productivity_Pipeline'">Back</button>
      </div>
    </div>
  </div>
</div>

<script>
  // CHANGED: reload page with pm_id so PHP can use it in the queries (no ajax)
  $(document).ready(function () {
    $('#manager_id').on('change', function () {
      const pmId = $(this).val() || 0;
      const pmToken = $('#manager_id option:selected').data('enc-pm') || '';
      const url = new URL(window.location.href);

      if (parseInt(pmId, 10) > 0 && pmToken) {
        url.searchParams.set('pm_id', String(pmToken));
      } else {
        url.searchParams.delete('pm_id');
      }

      window.location.href = url.toString();
    });
  });
</script>

<script>
  // Function to load industries based on the selected office
  function loadIndustries(selectedRepId) {
    var clientSelect = document.getElementById('client_id');
    var officeId = clientSelect ? clientSelect.value : '';
    var industriesDropdown = document.getElementById('industries');
    var repId = selectedRepId || '';

    if (!industriesDropdown) return;

    if (!officeId || officeId === '0') {
      industriesDropdown.innerHTML = '<option value="">Select client first</option>';
      return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (xhttp.readyState == 4 && xhttp.status == 200) {
        var responseHtml = String(xhttp.responseText || '').trim();
        if (responseHtml === '') {
          industriesDropdown.innerHTML = '<option value="">No reps found</option>';
          return;
        }

        industriesDropdown.innerHTML = '<option value="">Select client rep</option>' + responseHtml;
        if (repId) {
          industriesDropdown.value = String(repId);
        }
      }
    };

    xhttp.open('GET', 'index.php?page=rep&officeId=' + encodeURIComponent(officeId), true);
    xhttp.send();
  }

  document.getElementById('client_id').addEventListener('change', function () {
    loadIndustries('');
  });

  (function initClientReps() {
    var clientSelect = document.getElementById('client_id');
    var industriesDropdown = document.getElementById('industries');
    var selectedRepId = industriesDropdown ? (industriesDropdown.getAttribute('data-selected-rep') || '') : '';
    var clientId = clientSelect ? clientSelect.value : '';

    if (clientId && clientId !== '0') {
      loadIndustries(selectedRepId);
    } else if (industriesDropdown) {
      industriesDropdown.innerHTML = '<option value="">Select client first</option>';
    }
  })();
</script>

<script>
  function addFileInput() {
    var fileInputContainer = document.getElementById('file-input-container');
    var newFileInput = document.createElement('input');
    newFileInput.type = 'file';
    newFileInput.className = 'form-control-file';
    newFileInput.name = 'files[]';
    fileInputContainer.appendChild(newFileInput);
  }
</script>

<script>
  $('#manage-project').submit(function(e){
    e.preventDefault();
    start_load();

    var formData = new FormData($(this)[0]);
    var fileInputs = document.querySelectorAll('input[name="files[]"]');
    var valid = true;

    if(fileInputs.length > 0){
      formData.delete('files[]');

      fileInputs.forEach(function(input) {
        if(input.files.length > 0){
          for (var i = 0; i < input.files.length; i++) {
            var file = input.files[i];

            if(file.size > 5242880) { 
              end_load();
              alert_toast("Error: File '" + file.name + "' is too big! Max 5MB.", "error");
              valid = false;
              return;
            }

            formData.append('files[]', file);
          }
        }
      });
    }

    if(!valid){
      return false;
    }

    $.ajax({
      url:'ajax.php?action=save_project',
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      method: 'POST',
      type: 'POST',
      success:function(resp){
        if(resp){
          alert_toast('Data successfully saved',"success");
          setTimeout(function(){
            if (<?php echo (int)$_SESSION['login_type']; ?> !== 1) {
              location.href = 'index.php?page=Productivity_Pipeline';
            } else {
              location.href = 'index.php?page=job_list';
            }
          },2000)
        } else {
          end_load();
          alert_toast('Result: ' + resp, "error");
          console.log('Server Response:', resp);
        }
      },
      error: function(xhr, status, error) {
        end_load();
        console.error(xhr);
        alert("Server Error: The file might still be too large for the server settings.\nCheck 'upload_max_filesize' in php.ini");
      }
    })
  })

  $(document).ready(function() {
    $("#user_ids").on("change", function() {
      if ($(this).val() === "no_member") {
        $(".form-control.select2 option:not(#selected)").hide();
        $(".form-control.select2").val(null).trigger("change");
      } else {
        $(".form-control.select2 option:not(#selected)").show();
      }
    });
  });
</script>
