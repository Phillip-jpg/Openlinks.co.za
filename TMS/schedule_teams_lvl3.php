<?php
if (!isset($conn)) {
    include 'db_connect.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Team Schedule</title>
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h4>Add Team to Work Schedule</h4>
        </div>
        <div class="card-body">
            <!-- Form for Managing Team Schedule -->
            <form id="manage-schedule" action="./save_team_schedule.php" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="team_id" class="control-label">Team</label>
                            <select class="form-control form-control-sm select2" name="team_id" id="team_id" required>
                                <option value="">Please select here</option>
                                <?php
                                $team = $conn->query("SELECT DISTINCT team_name, team_id FROM team_schedule WHERE pm_manager= {$_SESSION['login_id']}");
                                while ($row = $team->fetch_assoc()): ?>
                                    <option value="<?php echo $row['team_id']; ?>">
                                        <?php echo ucwords($row['team_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <?php if (isset($_GET['warning'])): ?>
                        <div class="alert alert-warning">
                            <?php echo htmlspecialchars($_GET['warning']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="col-md-6">
                        <div class="form-group">
                                <label for="period_ids" class="control-label">Period Weeks</label>

                                   <select class="form-control form-control-sm select2"
                                            multiple
                                            name="period_ids[]"
                                            id="period_ids"
                                            required>
                                    
                                    <?php
                                    $today = new DateTime();
                                    
                                    // Start at CURRENT ISO week (Monday)
                                    $week = new DateTime();
                                    $week->setISODate(
                                        (int)$today->format('o'),
                                        (int)$today->format('W')
                                    );
                                    
                                    // CURRENT week + 51 following weeks = 52 total
                                    for ($i = 0; $i < 52; $i++) {
                                    
                                        $start = clone $week;      // Monday
                                        $end   = clone $week;
                                        $end->modify('+4 days');   // Friday
                                    
                                        // Store REAL dates in value
                                        $value = $start->format('Y-m-d') . '|' . $end->format('Y-m-d');
                                    
                                        // User-friendly label
                                        $label = $start->format('d M Y') . ' - ' . $end->format('d M Y');
                                    
                                        echo "<option value=\"$value\">$label</option>";
                                    
                                        // Move to next week
                                        $week->modify('+1 week');
                                    }
                                    ?>
                                    </select>

                        </div>
                    </div>
                </div>
                <div class="card-footer border-top border-info mt-3">
                    <div class="d-flex w-100 justify-content-center align-items-center">
                        <!-- Save Button -->
                        <button id="btn-save" class="btn btn-flat bg-gradient-primary mx-2" type="submit" style="color:white">Save</button>
                        <!-- Cancel Button -->
                        <button class="btn btn-flat bg-gradient-secondary mx-2" style="color:white" type="button" onclick="location.href='index.php?page=schedule_teams_lvl2'">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="col-lg-12">
	<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white">
			<h4 class="card-title">Work Schedule List</h4>
         
		</div>
		<div class="card-body">
		    <div class="form-row mb-3">
		         <div class="col-md-3">
                <label for="jobtype-filter">Filter by Month:</label>
                <select id="jobtype-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $job_qry = $conn->query("SELECT DISTINCT JOB_TYPE
FROM project_list pl
WHERE JOB_TYPE IS NOT NULL AND JOB_TYPE != '';");
                    while($job_row = $job_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $job_row['JOB_TYPE']; ?>"><?php echo $job_row['JOB_TYPE']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
             <div class="col-md-3">
                <label for="jobtype-filter">Filter by Week:</label>
                <select id="jobtype-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $job_qry = $conn->query("SELECT DISTINCT JOB_TYPE
FROM project_list pl
WHERE JOB_TYPE IS NOT NULL AND JOB_TYPE != '';");
                    while($job_row = $job_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $job_row['JOB_TYPE']; ?>"><?php echo $job_row['JOB_TYPE']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
		    <div class="col-md-3">
                <label for="jobtype-filter">Filter by Team:</label>
                <select id="month-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $month_qry = $conn->query("SELECT DISTINCT wwp.month
FROM project_list pl, working_week_periods wwp WHERE wwp.start_week>= pl.date_created AND wwp.end_week>=pl.date_created");
                    while($month_row = $month_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $month_row['month']; ?>"><?php echo $month_row['month']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
             <div class="col-md-3">
                <label for="created-filter">Filter by Member:</label>
                <select id="created-filter" class="form-control">
                    <option value="">All</option>
                  
                </select>
            </div>
         
          
         
               	</div>
               	<div class="table-responsive">
			<table class="table table-hover table-bordered table-condensed" id="list">
			     <br>
				<colgroup>
					<col width="10%">
					<col width="15%">
					<col width="15%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead style="background-color:#032033 !important; color:white">
					<tr>
						<th>Schedule_ID</th>
						<th>Month</th>
						<th>Start Date Cycles</th>
						<th>End Date Cycles</th>
						<th>Week</th>
						<th>PM</th>
					    <th>Team_Name</th>
						<th>Members</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$work_qry = $conn->query("SELECT
                    MONTHNAME(swt.startweek) AS Month,
                    swt.id,
                    swt.startweek AS start_date,
                    swt.endweek AS end_date,
                    WEEK(swt.Time_Scheduled, 1) AS period,
                    CONCAT(u.firstname, ' ', u.lastname) AS PM,
                    ts.team_name AS Team_Name,
                    swt.Time_Scheduled,
                    DATE_FORMAT(swt.Time_Scheduled, '%m%d') AS Date_Scheduled,
                    GROUP_CONCAT(
                        DISTINCT CONCAT(u1.firstname, ' ', u1.lastname)
                        ORDER BY u1.firstname, u1.lastname
                        SEPARATOR ', '
                    ) AS Members
                FROM schedule_work_team swt
                LEFT JOIN team_schedule ts ON ts.team_id = swt.Work_Team
                LEFT JOIN users u ON u.id = ts.pm_manager
                LEFT JOIN users u1 ON FIND_IN_SET(u1.id, ts.team_members)
                WHERE 
                    swt.Time_Scheduled IS NOT NULL
                    AND ts.team_name IS NOT NULL
                    AND ts.pm_manager = {$_SESSION['login_id']}
                    AND swt.Activated = 0
                GROUP BY 
                    swt.id, ts.team_name, u.id, swt.Time_Scheduled
                ORDER BY 
                    swt.Time_Scheduled ASC;

                            ");
					while($row=$work_qry->fetch_assoc()):
					?>
					<tr>
					    <td><p><?php echo ucwords($row['Date_Scheduled']) ?></p></td>
						<td><p><?php echo ucwords($row['Month']) ?></p></td>
						<td><p><?php echo ucwords($row['start_date']) ?></p></td>
						<td><p><?php echo ucwords($row['end_date']) ?></p></td>
						<td><p><?php echo ucwords($row['period']) ?></p>
							</td>
													<td><p><?php echo ucwords($row['PM']) ?></p>
							</td>
						<td><?php echo ucwords($row['Team_Name']) ?></p>
							</td>
                            <td>
                                <?php 
                                // Explode members by the separator (comma) and display one member per line
                                $members = explode(', ', $row['Members']); 
                                foreach ($members as $member) {
                                    echo '<span style="text-transform: lowercase; display: block; margin-bottom: 5px;">' . strtolower($member) . '</span>';
                                }
                                ?>
                            </td>

						<!-- Action Dropdown -->
						<td class="text-center">
						<a
                           href="index.php?page=hide&hide_id=<?php echo $row['id']; ?>"
                           onclick="return confirm('Are you sure you want to hide this item?');">
                           Hide
                        </a>
						</td>
					</tr>	
					<?php endwhile; ?>
				</tbody>
			</table>
			
	
	</div>
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
    /*.card-header {*/
    /*    background-color: #007bff;*/
    /*    color: white;*/
    /*}*/
    /*.table-hover tbody tr:hover {*/
    /*    background-color: #f9f9f9;*/
    /*}*/
    /*.badge {*/
    /*    font-size: 0.875rem;*/
    /*}*/
    /*.btn-default {*/
    /*    background-color: white;*/
    /*    border-color: #ddd;*/
    /*}*/
</style>

<!-- Script to initialize DataTables and handle deletion -->
<script>
$(document).ready(function(){
    // Initialize DataTable
    var dataTable = $('#list').DataTable();

    // Event listener for each filter dropdown
    $('#jobtype-filter').change(function(){
        filterTable();
    });
    
      $('#month-filter').change(function(){
        filterTable();
    });

    $('#created-filter').change(function(){
        filterTable();
    });

    $('#assigned-filter').change(function(){
        filterTable();
    });

    $('#status-filter').change(function(){
        filterTable();
    });
    
    

    // Function to filter the DataTable
    function filterTable() {
        var selectedJobType = $('#jobtype-filter').val();
           var selectedmonth = $('#month-filter').val();
        var selectedCreator = $('#created-filter').val();
        var selectedAssigned = $('#assigned-filter').val();
        var selectedStatus = $('#status-filter').val();
     

        // Apply filter for each column:
       
        dataTable.column(2).search(selectedJobType)
         dataTable.column(3).search(selectedmonth) // Job Type filter on 3rd column column
            .column(7).search(selectedAssigned)     // Assigned filter on 8th
            .column(5).search(selectedCreator)      // Creator filter on 6th column
            .column(8).search(selectedStatus)       // Status filter on 9th column
            .draw();  // Redraw the table with the new filters
    }

    // Handle deletion of projects
    $('.delete_project').click(function(){
        _conf("Are you sure to delete this job?", "delete_project", [$(this).attr('data-id')]);
    });
});

// Function to delete the project with confirmation
function delete_project(id){
    start_load();
    $.ajax({
        url: 'ajax.php?action=delete_project',
        method: 'POST',
        data: {id: id},
        success: function(resp) {
            if (resp == 1) {
                alert_toast("Data successfully deleted", 'success');
                setTimeout(function(){
                    location.reload();
                }, 1500);
            }
        }
    });
}

</script>

<!-- Select2 init -->
<script>
$(document).ready(function () {
    $('#period_ids').select2({
        placeholder: "Select week periods",
        width: "100%"
    });

    const $form = $('#manage-schedule');
    const $saveBtn = $('#btn-save');
    const saveBtnLabel = $saveBtn.text();

    function setSaveButtonState(isSaving) {
        $saveBtn.prop('disabled', isSaving);
        $saveBtn.text(isSaving ? 'Saving...' : saveBtnLabel);
    }

    $form.on('submit', function (e) {
        e.preventDefault();

        const teamId = $('#team_id').val();
        const selectedPeriods = $('#period_ids').val();

        if (!teamId || !Array.isArray(selectedPeriods) || selectedPeriods.length === 0) {
            if (typeof alert_toast === 'function') {
                alert_toast('Please select team and at least one period week.', 'warning');
            } else {
                alert('Please select team and at least one period week.');
            }
            return;
        }

        setSaveButtonState(true);

        $.ajax({
            url: 'save_team_schedule.php',
            method: 'POST',
            data: $form.serialize(),
            timeout: 45000,
            success: function (resp) {
                const cleanResp = String(resp || '').trim();

                if (cleanResp === 'OK') {
                    if (typeof alert_toast === 'function') {
                        alert_toast('Team schedule saved successfully.', 'success');
                    }
                } else if (cleanResp === 'OK_WITH_DUPLICATES') {
                    if (typeof alert_toast === 'function') {
                        alert_toast('Saved. Some selected weeks were already assigned and were skipped.', 'warning');
                    }
                } else {
                    setSaveButtonState(false);
                    if (typeof alert_toast === 'function') {
                        alert_toast('Save failed: ' + cleanResp, 'danger');
                    }
                    return;
                }

                setTimeout(function () {
                    window.location.href = 'index.php?page=schedule_teams_lvl3';
                }, 900);
            },
            error: function (xhr, status) {
                setSaveButtonState(false);

                if (status === 'timeout') {
                    if (typeof alert_toast === 'function') {
                        alert_toast('Save timed out. Refreshing to verify status...', 'warning');
                    }
                    setTimeout(function () {
                        window.location.href = 'index.php?page=schedule_teams_lvl3';
                    }, 800);
                    return;
                }

                if (typeof alert_toast === 'function') {
                    alert_toast('Request failed: ' + (xhr.responseText || xhr.status), 'danger');
                }
            }
        });
    });
});
</script>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Include Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Include Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    // Initialize Select2 for dropdowns
    $(document).ready(function () {
        $('.select2').select2();
    });
</script>
</body>
</html>
