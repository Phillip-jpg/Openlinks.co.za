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


<div class="col-lg-12">
	<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white">
			<h4 class="card-title">Work Schedule List</h4>
         
		</div>
		<div class="card-body">
		    <div class="form-row mb-3">
		         <div class="col-md-3">
                <label for="month-filter">Filter by Month:</label>
                <select id="month-filter" class="form-control">
                    <option value="">All</option>
                </select>
            </div>
             <div class="col-md-3">
                <label for="week-filter">Filter by Week:</label>
                <select id="week-filter" class="form-control">
                    <option value="">All</option>
                </select>
            </div>
		    <div class="col-md-3">
                <label for="team-filter">Filter by Team:</label>
                <select id="team-filter" class="form-control">
                    <option value="">All</option>
                </select>
            </div>
             <div class="col-md-3">
                <label for="member-filter">Filter by Member:</label>
                <select id="member-filter" class="form-control">
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
						<th>Entity</th>
					    <th>Team_Name</th>
						<th>Members</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$work_qry = $conn->query("SELECT
                    MONTHNAME(swt.startweek) AS Month,
                    MONTH(swt.startweek) AS month_number,
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
                    AND swt.Activated = 0
                    AND ts.team_members ={$_SESSION['login_id']}
                GROUP BY 
                    swt.id, ts.team_name, u.id, swt.Time_Scheduled
                ORDER BY 
                    MONTH(swt.startweek) ASC,
                    swt.startweek ASC,
                    swt.Time_Scheduled ASC;

                            ");
					while($row=$work_qry->fetch_assoc()):
					?>
					<tr>
					    <td><p><?php echo ucwords($row['Date_Scheduled']) ?></p></td>
						<td data-order="<?php echo (int)$row['month_number']; ?>"><p><?php echo ucwords($row['Month']) ?></p></td>
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
    var dataTable = $('#list').DataTable({
        order: [[1, 'asc'], [2, 'asc']]
    });

    function addOptions($select, values) {
        values.forEach(function(value) {
            if (value === '') {
                return;
            }
            $select.append(
                $('<option>', {
                    value: value,
                    text: value
                })
            );
        });
    }

    function uniqueSortedColumnValues(columnIndex) {
        var seen = {};
        return dataTable.column(columnIndex).data().toArray().map(function(value) {
            return normalizeCell(value);
        }).filter(function(value) {
            if (value === '' || seen[value]) {
                return false;
            }
            seen[value] = true;
            return true;
        }).sort(function(a, b) {
            return a.localeCompare(b, undefined, { numeric: true, sensitivity: 'base' });
        });
    }

    function uniqueSortedMemberValues() {
        var seen = {};
        var members = [];

        dataTable.column(7).data().toArray().forEach(function(value) {
            $('<div>').html(value).find('span').each(function() {
                var member = $(this).text();
                var cleanMember = member.trim();
                if (cleanMember === '') {
                    return;
                }
                if (!seen[cleanMember]) {
                    seen[cleanMember] = true;
                    members.push(cleanMember);
                }
            });
        });

        return members.sort(function(a, b) {
            return a.localeCompare(b, undefined, { sensitivity: 'base' });
        });
    }

    function normalizeCell(value) {
        return $('<div>').html(value || '').text().replace(/\s+/g, ' ').trim();
    }

    addOptions($('#month-filter'), uniqueSortedColumnValues(1));
    addOptions($('#week-filter'), uniqueSortedColumnValues(4));
    addOptions($('#team-filter'), uniqueSortedColumnValues(6));
    addOptions($('#member-filter'), uniqueSortedMemberValues());

    $.fn.dataTable.ext.search.push(function(settings, data) {
        if (settings.nTable !== dataTable.table().node()) {
            return true;
        }

        var selectedMonth = $('#month-filter').val();
        var selectedWeek = $('#week-filter').val();
        var selectedTeam = $('#team-filter').val();
        var selectedMember = $('#member-filter').val();

        var monthValue = normalizeCell(data[1]);
        var weekValue = normalizeCell(data[4]);
        var teamValue = normalizeCell(data[6]);
        var memberValue = normalizeCell(data[7]).toLowerCase();

        if (selectedMonth && monthValue !== selectedMonth) {
            return false;
        }

        if (selectedWeek && weekValue !== selectedWeek) {
            return false;
        }

        if (selectedTeam && teamValue !== selectedTeam) {
            return false;
        }

        if (selectedMember && memberValue.indexOf(selectedMember.toLowerCase()) === -1) {
            return false;
        }

        return true;
    });

    $('#month-filter, #week-filter, #team-filter, #member-filter').on('change', function(){
        dataTable.draw();
    });

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
