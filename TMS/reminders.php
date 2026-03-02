<?php if(!isset($conn)){ include 'db_connect.php'; }

$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
if ($id > 0) {
  $stmt = $conn->prepare("SELECT * FROM reminders WHERE id = ?");
  if ($stmt) {
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
      $editReminder = $stmt->get_result()->fetch_assoc();
      if ($editReminder) {
        foreach ($editReminder as $k => $v) {
          ${$k} = $v;
        }
      } else {
        $id = 0;
      }
    }
    $stmt->close();
  }
}

// Selected PM (project manager) ID (drives dependent dropdown queries)
$project_manager_id = 0;
if (isset($manager_id) && $manager_id !== '') {
  $project_manager_id = (int)$manager_id;
} elseif (isset($_GET['pm_id'])) {
  $project_manager_id = (int)$_GET['pm_id'];
} elseif (isset($_POST['manager_id'])) {
  $project_manager_id = (int)$_POST['manager_id'];
}

$currentDate = new DateTime();
$currentDate->setISODate($currentDate->format('o'), $currentDate->format('W'), 1);
$startOfWeek = $currentDate->format('Y-m-d');

// Calculate the end date of the current week (Friday)
$endOfWeekObj = clone $currentDate;
$endOfWeekObj->modify('+4 days');
$endOfWeek = $endOfWeekObj->format('Y-m-d');

?>

<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-body">
      <div class="d-flex justify-content-end mb-3">
        <a href="index.php?page=reminders_list" class="btn btn-sm btn-primary">
          <i class="fas fa-list mr-1"></i> Reminders List
        </a>
      </div>
				<form action="" id="manage-project" enctype="multipart/form-data">
	        <input type="hidden" name="creator_id" value="<?php echo (int)$_SESSION['login_id'] ?>">
	        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">

	        <div class="row">

	        <div class="col-md-6">
	            <div class="form-group">
	              <label for="" class="control-label">Entity</label>

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
                ?>
                  <option value="<?php echo $mid ?>"
                    <?php echo ($project_manager_id === $mid) ? "selected" : '' ?>>
                    <?php echo ucwords($row['name']) ?>
                  </option>
                <?php endwhile; ?>
	              </select>
	            </div>
	          </div>


            <div class="col-md-6">
	            <div class="form-group">
	              <label for="" class="control-label">Title of Reminder</label>
	              <input required type="text" class="form-control form-control-sm" name="reminder_name" value="<?php echo isset($reminder_name) ? htmlspecialchars($reminder_name) : '' ?>">
	            </div>
	          </div>
	        </div>

	
        <div class="row">


        
          <div class="col-md-6">
            <div class="form-group">
              <label for="" class="control-label">Account</label>

	              <!-- CHANGED: give this select a stable id="client_id" (so PM can drive it cleanly if needed later) -->
		              <select required name="CLIENT_ID" id="client_id" class="custom-select custom-select-sm">
	                <?php 
                  $selectedClientId = isset($account) ? (int)$account : 0;

                // CHANGED: use selected PM id -> $project_manager_id for login_type 3
                $pmId = (int)$project_manager_id;

                if($_SESSION['login_type']==3){
                  $clients = $conn->query("SELECT * FROM yasccoza_openlink_market.client where yasccoza_openlink_market.client.creator_id=$pmId");
                } elseif($_SESSION['login_type']==2){
                  $clients = $conn->query("SELECT * FROM yasccoza_openlink_market.client where yasccoza_openlink_market.client.creator_id={$_SESSION['login_id']}");
                } else {
                  $clients = $conn->query("SELECT * FROM yasccoza_openlink_market.client");
                }

	                if ($clients && $clients->num_rows > 0) {
	                  while ($row = $clients->fetch_assoc()):
                      $clientId = (int)$row['CLIENT_ID'];
                    ?>
	                    <option value="<?php echo htmlspecialchars($row['CLIENT_ID']) ?>" <?php echo ($selectedClientId === $clientId) ? "selected" : ''; ?>>
	                      <?php echo ucwords(htmlspecialchars($row['company_name'])) . ' (' . htmlspecialchars($row['CLIENT_ID']) . ')'; ?>
	                    </option>
	                  <?php endwhile;
                }
                ?>
              </select>
            </div>
          </div>


            <div class="col-md-6">
            <div class="form-group" id="industryDropdownDiv">
              <label for="" class="control-label">Account REP</label>
	              <select required name="CLIENT_REP" id="industries" class="custom-select custom-select-sm" data-selected="<?php echo isset($account_rep) ? (int)$account_rep : ''; ?>">
	                <?php
	                $officeId = isset($_GET['officeId']) ? (int)$_GET['officeId'] : (isset($selectedClientId) ? (int)$selectedClientId : 0);
                  $selectedRepId = isset($account_rep) ? (int)$account_rep : 0;
	                $stmt = $conn->prepare("SELECT REP_ID, REP_NAME FROM client_rep WHERE CLIENT_ID = ?");
	                $stmt->bind_param("i", $officeId);

	                if ($stmt->execute()) {
	                  $result = $stmt->get_result();
	                  while ($row = $result->fetch_assoc()) {
                      $repId = (int)$row['REP_ID'];
                      $selected = ($selectedRepId === $repId) ? "selected" : "";
	                    echo "<option value='" . $row['REP_ID'] . "' $selected>" . $row['REP_NAME'] . "</option>";
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
                <label for="team_ids" class="control-label">Team</label>
                <select required class="form-control form-control-sm select2" name="team_id">
                  <?php
                  
	                  $pmId = (int)$project_manager_id;
                    $selectedTeamId = isset($team) ? (int)$team : 0;
	            
	                  // Fetch teams for the dropdown
	                  $employees = $conn->query("
                  SELECT DISTINCT
                      ts_pm.team_name AS t_n,
                      ts_pm.team_id
                    FROM team_schedule ts_pm
                    JOIN team_schedule ts_me
                      ON ts_me.team_id = ts_pm.team_id
                    WHERE ts_pm.team_members = $pmId
                      AND ts_me.team_members = {$_SESSION['login_id']}
                  ");
            
	                  // Loop through teams and set the selected team for edit mode
	                  while ($row = $employees->fetch_assoc()){
                      $teamId = (int)$row['team_id'];
                      $isSelected = ($selectedTeamId === $teamId) ? 'selected' : '';
	                    echo '<option value="' . htmlspecialchars((string)$teamId) . '" ' . $isSelected . '>' .
	                      htmlspecialchars(ucwords($row['t_n'])) .
	                      '</option>';
	                  }
	                  ?>
                </select>
              </div>
            </div>

             <div class="col-md-6">
              <div class="form-group">
                <label for="" class="control-label">Add Work Type</label>

                <select required class="form-control form-control-sm select2" name="worktype_id">
                  <option></option>
                  <?php 
                  $login_id = (int) $_SESSION['login_id'];
                  $login_type = (int) $_SESSION['login_type'];
                  $pmId = (int)$project_manager_id;

                  // CHANGED: use selected PM id -> $project_manager_id
                  if ($login_type !== 1) {
                    $query = "
                      SELECT DISTINCT tl.*
							FROM task_list tl
							WHERE tl.creator_id = {$_SESSION['login_id']}
                    ";
                  } else {
                    $query = "
                      SELECT tl.*
                      FROM task_list tl
                      ORDER BY tl.task_name;
                    ";
                  }

	                  $taskList = $conn->query($query);
                    $selectedWorkTypeId = isset($work_type) ? (int)$work_type : 0;
	                  while ($row = $taskList->fetch_assoc()):
	                  ?>
	                    <option 
	                      value="<?php echo $row['id']; ?>" 
	                      <?php echo ($selectedWorkTypeId === (int)$row['id']) ? 'selected' : ''; ?>>
	                      <?php echo ucwords($row['task_name']); ?>
	                    </option>
	                  <?php endwhile; ?>
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
              <label for="" class="control-label">Scheduled End Date</label>
              <input required type="date" class="form-control form-control-sm" autocomplete="off" name="end_date" value="<?php echo isset($end_date) ? date("Y-m-d",strtotime($end_date)) : '' ?>">
            </div>
          </div>
      </div>

      <div class="row">

         <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Meeting Day</label>

                  <select required class="form-control form-control-sm" name="meeting_day">
                    <option value="" disabled <?php echo empty($meeting_day) ? 'selected' : ''; ?>>Select a day</option>

                    <?php
                      $meetingDayOptions = [
                        ['value' => 'after every day', 'label' => 'After every day'],
                        ['value' => 'after every two days', 'label' => 'After every two days'],
                        ['value' => 'after every four days', 'label' => 'After every four days'],
                        ['value' => 'after every five days', 'label' => 'After every five days'],
                        ['value' => 'monday', 'label' => 'Monday'],
                        ['value' => 'tuesday', 'label' => 'Tuesday'],
                        ['value' => 'wednesday', 'label' => 'Wednesday'],
                        ['value' => 'thursday', 'label' => 'Thursday'],
                        ['value' => 'friday', 'label' => 'Friday'],
                      ];

                      $selectedMeetingDay = isset($meeting_day) ? strtolower(trim((string)$meeting_day)) : '';
                      foreach ($meetingDayOptions as $opt) {
                        $val = (string)$opt['value'];
                        $label = (string)$opt['label'];
                        $selected = ($selectedMeetingDay === strtolower($val)) ? 'selected' : '';
                        echo "<option value=\"" . htmlspecialchars($val, ENT_QUOTES) . "\" $selected>" . htmlspecialchars($label) . "</option>";
                      }
                    ?>
                  </select>
                </div>
              </div>


      </div>


       <div class="row">

           <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Scheduled Meeting Time</label>
                <input required type="time" class="form-control form-control-sm" autocomplete="off"
                  name="meeting_time"
                  value="<?php echo isset($meeting_time) ? date('H:i', strtotime($meeting_time)) : '' ?>">
	            </div>
	          </div>
	          <div class="col-md-6">
	            <div class="form-group">
	              <label for="" class="control-label">Every (_days)</label>
	              <input required type="number" min="1" step="1" class="form-control form-control-sm" name="every_days" value="<?php echo isset($every_days) ? (int)$every_days : 1; ?>">
	            </div>
	          </div>
	        </div>



       <div class="row">

          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Online Meeting Platform</label>

	                <select required class="form-control form-control-sm" name="online_meeting">
	                  <option value="" disabled <?php echo empty($online_meeting) ? 'selected' : ''; ?>>
	                    Select platform
	                  </option>

                  <?php
                    $platforms = [
                      'zoom' => 'Zoom',
                      'google_meet' => 'Google Meet',
                      'microsoft_teams' => 'Microsoft Teams',
                      'whatsapp' => 'WhatsApp (Video Call)',
                      'Other' => 'Other Platform',
                    ];

	                    foreach ($platforms as $val => $label) {
	                      $selected = (isset($online_meeting) && $online_meeting === $val) ? 'selected' : '';
	                      echo "<option value=\"$val\" $selected>$label</option>";
	                    }
	                  ?>
                </select>
              </div>
        </div>



         <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Meeting Link</label>
              <input required type="url" class="form-control form-control-sm" autocomplete="off"
                name="meeting_link"
                placeholder="https://..."
                value="<?php echo isset($meeting_link) ? htmlspecialchars($meeting_link) : '' ?>">
            </div>
          </div>



      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">Status</label>
            <select required class="form-control form-control-sm" name="status">
              <option value="1" <?php echo (!isset($status) || (string)$status === '1') ? 'selected' : ''; ?>>Active</option>
              <option value="0" <?php echo (isset($status) && (string)$status === '0') ? 'selected' : ''; ?>>Deactive</option>
            </select>
          </div>
        </div>
      </div>

		      <div class="row">
		        <div class="col-md-10">
		          <div class="form-group">
		            <label for="" class="control-label">Description</label>
		            <textarea name="description" id="" cols="30" rows="10" class="summernote form-control"><?php echo isset($description) ? htmlspecialchars(trim((string)html_entity_decode(strip_tags((string)$description), ENT_QUOTES | ENT_HTML5, 'UTF-8')), ENT_QUOTES, 'UTF-8') : '' ?></textarea>
		          </div>
		        </div>
		      </div>
        
      </form>
    </div>

	    <div class="card-footer border-top border-info">
	      <div class="d-flex w-100 justify-content-center align-items-center">
	        <button class="btn btn-flat bg-gradient-primary mx-2" form="manage-project"><?php echo $id > 0 ? 'Update' : 'Save'; ?></button>
	        <button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href='index.php?page=job_list'">Cancel</button>
	      </div>
	    </div>
  </div>
</div>

<script>
  // CHANGED: reload page with pm_id so PHP can use it in the queries (no ajax)
  $(document).ready(function () {
    $('#manager_id').on('change', function () {
      const pmId = $(this).val() || 0;
      const url = new URL(window.location.href);
      url.searchParams.set('pm_id', pmId);
      window.location.href = url.toString();
    });
  });
</script>

<script>
  // Function to load industries based on the selected office
  function loadIndustries() {
    var officeId = document.getElementById('client_id').value; // CHANGED: was 'offices', now client_id
    var industriesDropdown = document.getElementById('industries');

	    var xhttp = new XMLHttpRequest();
	    xhttp.onreadystatechange = function () {
	      if (xhttp.readyState == 4 && xhttp.status == 200) {
	        industriesDropdown.innerHTML = xhttp.responseText;
          var selectedRep = industriesDropdown.getAttribute('data-selected');
          if (selectedRep) {
            industriesDropdown.value = selectedRep;
          }
	      }
	    };

    xhttp.open('GET', 'index.php?page=rep&officeId=' + officeId, true);
    xhttp.send();
  }

	  document.getElementById('client_id').addEventListener('change', function () {
      var industriesDropdown = document.getElementById('industries');
      industriesDropdown.setAttribute('data-selected', '');
      loadIndustries();
    });
	  loadIndustries();
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
      var isEdit = !!(formData.get('id') && parseInt(formData.get('id'), 10) > 0);

	    $.ajax({
	      url:'save_reminders.php',
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      method: 'POST',
      type: 'POST',
	      success:function(resp){
	        var response = (resp || '').toString().trim();
	        if(response === '1'){
	          end_load();
		          alert_toast(isEdit ? 'Reminder updated successfully' : 'Reminder saved successfully',"success");
		          setTimeout(function(){
		            location.href = 'index.php?page=reminders_list';
		          },300)
        } else {
          end_load();
          alert_toast(response ? response : 'Unable to save reminder', "error");
          console.log('Server Response:', response);
        }
      },
      error: function(xhr, status, error) {
        end_load();
        console.error(xhr);
        alert_toast("Server error: could not save reminder", "error");
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
