<?php
include 'db_connect.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* -----------------------------
   ✅ Decode ALL encrypted IDs (job, task, activity, team)
   and map them back to the expected $_GET keys
------------------------------ */
function decode_id_param($key) {
    if (!isset($_GET[$key]) || $_GET[$key] === '') {
        die("Missing parameter: " . htmlspecialchars($key));
    }
    $decoded = base64_decode($_GET[$key], true); // strict
    if ($decoded === false || !ctype_digit($decoded)) {
        die("Invalid parameter: " . htmlspecialchars($key));
    }
    return (int)$decoded;
}

/**
 * Your new encrypted URL uses:
 *  job, task, activity, team
 * but your existing code expects:
 *  project_id, task_id, activity_id, team_id
 */
if (isset($_GET['job']))      $_GET['project_id']  = decode_id_param('job');
if (isset($_GET['task']))     $_GET['task_id']     = decode_id_param('task');
if (isset($_GET['activity'])) $_GET['activity_id'] = decode_id_param('activity');
if (isset($_GET['team']))     $_GET['team_id']     = decode_id_param('team');

/* -----------------------------
   ✅ Your existing code below (only changed: remove "already assigned" restriction)
------------------------------ */

$fullNames = array();

// Check if the request method is GET and if project_id and task_id are present in the URL
if (isset($_GET['project_id']) && isset($_GET['task_id']) && isset($_GET['activity_id'])) {
    // Get the project ID and task ID from the URL
    $projectId = $_GET['project_id'];
    $taskId = $_GET['task_id'];
    $activityId = $_GET['activity_id'];
    $team_id = $_GET['team_id'];

    // Fetch data from the 'user_productivity' table based on the given task ID
    $qry = $conn->query("SELECT * FROM user_productivity WHERE id = $activityId");
    
    if ($qry) {
        $row = $qry->fetch_assoc();
        foreach ($row as $k => $v) {
            $$k = $v;
        }
    }
    
    $qry = $conn->query("SELECT 
        GROUP_CONCAT(ts.team_members SEPARATOR ', ') AS team_members, 
        pl.manager_id, 
        pl.CLIENT_ID, 
        ts.team_id 
    FROM project_list pl
    JOIN team_schedule ts ON ts.team_id = pl.team_ids
    WHERE pl.id = " . $projectId . "
      AND ts.status=1
    GROUP BY pl.manager_id, pl.CLIENT_ID, ts.team_id;
    ")->fetch_array();

    $userIdsString = $qry['team_members'];
    $managerId = $qry['manager_id'];
    $client_id = $qry['CLIENT_ID'];
                
    $userIdsArray = array_map('intval', explode(',', $userIdsString));
    $userIdsClause = implode(',', $userIdsArray);


    // Member qualification:
    // 1) normal members qualify via members_and_worktypes
    // 2) entity users (type=2) qualify when this work type belongs to their own task_list (creator_id)
    $query = "
        SELECT DISTINCT u.id AS member_id
        FROM users u
        LEFT JOIN members_and_worktypes mw
            ON mw.member_id = u.id
           AND mw.work_type_id = $taskId
        LEFT JOIN task_list tl
            ON tl.id = $taskId
           AND tl.creator_id = u.id
        WHERE u.id IN ($userIdsClause)
          AND (
                mw.member_id IS NOT NULL
                OR (u.type = 2 AND tl.id IS NOT NULL)
          )
    ";
    $result = $conn->query($query);

    if ($result->num_rows == 0) {
        echo "<div style='color:red; font-weight:bold'>Non of the members you selected for the job qualify for this worktype !</div>";
        echo "<br>";
    } elseif ($result->num_rows > 0) {

        $matchingMemberIds = array();

        while ($row = $result->fetch_assoc()) {
            $matchingMemberIds[] = $row['member_id'];
        }

        // ✅ UPDATED: Members can be assigned to other worktypes/activities
        $memberIdsClause = implode(',', array_map('intval', $matchingMemberIds));

        $query2 = "SELECT CONCAT(firstname, ' ', lastname) AS full_name, id FROM users WHERE id IN ($memberIdsClause)";
        $result2 = $conn->query($query2);

        $fullNames = array();

        while ($row = $result2->fetch_assoc()) {
            $fullNames[$row['id']] = $row['full_name'];
        }

        if (empty($fullNames)) {
            echo "No members";
        } else {
            foreach ($fullNames as $userId => $fullName) {
                $selected = '';
                if (isset($_POST['user_id'])) {
                    $userIdsArray = is_array($_POST['user_id']) ? $_POST['user_id'] : explode(',', $_POST['user_id']);
                    $selected = in_array($userId, $userIdsArray) ? 'selected' : '';
                }
            }
        }
    }
}
?>

<style>
    .save-assign-modern {
        --surface: #ffffff;
        --ink: #0f172a;
        --muted: #64748b;
        --line: #dbe7f5;
        --brand-1: #0f4c81;
        --brand-2: #0b7db5;
        --brand-3: #5eb3f3;
    }

    .save-assign-modern .assign-shell {
        border: 1px solid var(--line);
        border-radius: 18px;
        background: linear-gradient(145deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
        padding: 1rem 0.95rem 0.8rem;
    }

    .save-assign-modern .activity-worktype {
        color: var(--brand-2);
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.55rem;
    }

    .save-assign-modern dl,
    .save-assign-modern dt,
    .save-assign-modern dd {
        margin-bottom: 0.45rem;
    }

    .save-assign-modern dt b.border-bottom.border-primary {
        border-color: var(--line) !important;
        color: #1e3a5f;
        font-size: 0.74rem;
        letter-spacing: 0.05em;
        padding-bottom: 0.18rem;
        text-transform: uppercase;
    }

    .save-assign-modern dd {
        color: #334155;
        font-size: 0.9rem;
    }

    .save-assign-modern .form-group {
        border: 1px solid var(--line);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
        margin-bottom: 0.7rem;
        padding: 0.85rem 0.9rem;
    }

    .save-assign-modern .form-group label {
        color: #1e3a5f;
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        margin-bottom: 0.35rem;
        text-transform: uppercase;
    }

    .save-assign-modern .assign-help {
        color: var(--brand-2);
        font-size: 0.73rem;
        font-weight: 500;
        letter-spacing: 0;
        text-transform: none;
    }

    .save-assign-modern .assign-member-select {
        border: 1px solid #c9dcf3;
        border-radius: 10px;
        color: #334155;
        font-size: 0.84rem;
        min-height: calc(2rem + 2px);
    }

    .save-assign-modern .assign-member-select:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 0.18rem rgba(96, 165, 250, 0.16);
    }

    .save-assign-modern .assign-footer {
        background: #f8fbff;
        border-top: 1px solid var(--line) !important;
        border-radius: 14px;
        margin-top: 0.5rem;
        padding: 0.72rem 0.75rem;
    }

    .save-assign-modern .assign-save-btn {
        background: linear-gradient(125deg, var(--brand-1), var(--brand-2));
        border: 0;
        border-radius: 999px;
        box-shadow: 0 10px 20px rgba(11, 125, 181, 0.24);
        color: #fff;
        font-size: 0.82rem;
        font-weight: 600;
        min-width: 110px;
        padding: 0.45rem 1rem;
    }

    .save-assign-modern .assign-back-btn {
        background: #ffffff;
        border: 1px solid #bfd8f8;
        border-radius: 999px;
        color: #0f4c81;
        font-size: 0.82rem;
        font-weight: 600;
        min-width: 100px;
        padding: 0.45rem 1rem;
    }

    .save-assign-modern .assign-back-btn:hover {
        background: #eff6ff;
        color: #1d4ed8;
    }

    @media (max-width: 768px) {
        .save-assign-modern .assign-shell {
            border-radius: 14px;
            padding: 0.75rem 0.65rem 0.6rem;
        }
    }

    /* Readability overrides */
    .save-assign-modern {
        font-size: 0.98rem;
    }

    .save-assign-modern .activity-worktype {
        font-size: 1.1rem;
    }

    .save-assign-modern dt b.border-bottom.border-primary {
        font-size: 0.83rem;
    }

    .save-assign-modern dd {
        font-size: 0.96rem;
    }

    .save-assign-modern .form-group label {
        font-size: 0.84rem;
    }

    .save-assign-modern .assign-help {
        font-size: 0.84rem;
    }

    .save-assign-modern .assign-member-select {
        font-size: 0.94rem;
    }

    .save-assign-modern .assign-save-btn,
    .save-assign-modern .assign-back-btn {
        font-size: 0.9rem;
    }
</style>

<form action="delete_assigned.php" id="assign-delete" method="post">
    <input type="hidden" name="task_id" value="<?php echo isset($taskId) ? $taskId : '' ?>">
    <input type="hidden" name="project_id" value="<?php echo isset($projectId) ? $projectId : '' ?>">
    <input type="hidden" name="activity_id" value="<?php echo isset($activityId) ? $activityId : '' ?>">
</form>

<div class="col-lg-12 save-assign-modern">
    <form action="save_assigned_database.php" id="assign-save" method="post">
        <div class="row">
            <div class="col-md-12">
                <div class="callout callout-info assign-shell">
                    <div class="col-md-12">
                        <div class="row">
                            <input type="hidden" name="task_id" value="<?php echo isset($taskId) ? $taskId : '' ?>">
                            <input type="hidden" name="project_id" value="<?php echo isset($projectId) ? $projectId : '' ?>">
                            <input type="hidden" name="activity_id" value="<?php echo isset($activityId) ? $activityId : '' ?>">
                            <input type="hidden" name="manager_id" value="<?php echo isset($managerId) ? $managerId : '' ?>">
                            <input type="hidden" name="client_id" value="<?php echo isset($client_id) ? $client_id : '' ?>">
                            <input type="hidden" name="team_id" value="<?php echo isset($team_id) ? $team_id : '' ?>">

                            <div class="col-sm-6">
                                <?php
                                $qry = $conn->query("SELECT task_name FROM task_list WHERE id = $taskId");
                                if ($qry) {
                                    $row = $qry->fetch_assoc();
                                    echo "<div class='activity-worktype'>" . htmlspecialchars($row['task_name']) . "</div>";
                                }
                                ?>
                                <?php echo "<br>" ?>
                                <dt><b class="border-bottom border-primary">Activity Name</b></dt>
                                <dd><?php echo ucwords($name) ?></dd>
                                <dt><b class="border-bottom border-primary">Duration (in days)</b></dt>
                                <dd><?php echo html_entity_decode($duration) ?></dd>
                                <dt><b class="border-bottom border-primary">Comment</b></dt>
                                <dd><?php echo html_entity_decode($comment) ?></dd>
                                <dt><b class="border-bottom border-primary">No of Resources</b></dt>
                                <dd><?php echo html_entity_decode($resources) ?></dd>
                                </dl>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id" class="control-label">
                                        Select Members (Resources)
                                        <span class="assign-help">select <?php echo $resources; ?> at most</span>
                                    </label>

                                    <select class="form-control form-control-sm select2 assign-member-select" name="user_id" required>
                                        <option value="">Select Resources</option>
                                        <?php
                                        foreach ($fullNames as $userId => $fullName) {
                                            $selected = '';
                                            if (isset($_POST['user_id']) && isset($task_ids)) {
                                                $userIdsArray = is_array($_POST['user_id']) ? $_POST['user_id'] : explode(',', $_POST['user_id']);
                                                $selected = in_array($userId, $userIdsArray) ? 'selected' : '';
                                            }
                                            echo "<option value='$userId' $selected>$fullName</option>";
                                        }
                                        ?>
                                    </select>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card-footer border-top border-info assign-footer">
        <div class="d-flex w-100 justify-content-center align-items-center">
	            <button
                    id="btn-save-assign"
	                class="btn btn-flat bg-gradient-primary mx-2 assign-save-btn"
	                type="submit"
	                form="assign-save">
	                Save
	            </button>
            <button class="btn btn-flat bg-gradient-secondary mx-2 assign-back-btn" type="button" onclick="redirectToAssignDuties(<?php echo $projectId; ?>)">Back</button>
        </div>
    </div>
</div>

<script>
function redirectToAssignDuties(projectId) {
    var url = 'index.php?page=assign_duties&id=' + projectId;
    window.location.href = url;
}

$(document).ready(function(){
    const $assignForm = $('#assign-save');
    const $saveBtn = $('#btn-save-assign');
    const defaultSaveText = $saveBtn.text();

    $assignForm.on('submit', function(e){
        e.preventDefault();

        const selectedUser = String($assignForm.find('[name="user_id"]').val() || '').trim();
        if (!selectedUser) {
            if (typeof alert_toast === 'function') {
                alert_toast('Please select a member before saving.', 'warning');
            } else {
                alert('Please select a member before saving.');
            }
            return;
        }

        if ($saveBtn.prop('disabled')) {
            return;
        }

        $saveBtn.prop('disabled', true).text('Saving...');
        if (typeof start_load === 'function') {
            start_load();
        }

        $.ajax({
            url: 'save_assigned_database.php',
            method: 'POST',
            data: $assignForm.serialize(),
            timeout: 45000,
            success: function(resp){
                const text = String(resp || '').trim();
                if (text.indexOf('OK|') === 0) {
                    const redirectUrl = text.substring(3) || ('index.php?page=assign_duties&id=<?php echo isset($projectId) ? (int)$projectId : 0; ?>');
                    if (typeof alert_toast === 'function') {
                        alert_toast('Assignment saved successfully.', 'success');
                    }
                    setTimeout(function(){
                        window.location.href = redirectUrl;
                    }, 700);
                    return;
                }

                if (typeof end_load === 'function') {
                    end_load();
                }
                $saveBtn.prop('disabled', false).text(defaultSaveText);
                if (typeof alert_toast === 'function') {
                    alert_toast(text || 'Unable to save assignment.', 'danger');
                } else {
                    alert(text || 'Unable to save assignment.');
                }
            },
            error: function(xhr, status){
                if (typeof end_load === 'function') {
                    end_load();
                }
                $saveBtn.prop('disabled', false).text(defaultSaveText);

                if (status === 'timeout') {
                    if (typeof alert_toast === 'function') {
                        alert_toast('Save timed out. Refreshing to verify status...', 'warning');
                    }
                    setTimeout(function(){
                        window.location.href = 'index.php?page=assign_duties&id=<?php echo isset($projectId) ? (int)$projectId : 0; ?>';
                    }, 900);
                    return;
                }

                const err = String(xhr.responseText || xhr.status || 'Request failed');
                if (typeof alert_toast === 'function') {
                    alert_toast(err, 'danger');
                } else {
                    alert(err);
                }
            }
        });
    });
});

function resetAllocations(taskId, activityId) {
    if (confirm("Are you sure you want to reset allocations?")) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "index.php?page=save_assign&task_id=" + taskId + "&activity_id=" + activityId, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
            }
        };
        xhr.send();
    }
}
</script>
