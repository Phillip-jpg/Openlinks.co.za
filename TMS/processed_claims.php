<?php include 'db_connect.php'; ?>

<?php
if (isset($_GET['start']) && isset($_GET['end'])) {
    $start = htmlspecialchars($_GET['start']);
    $end = htmlspecialchars($_GET['end']);
} else {
    echo "No dates provided.";
    exit();
}
?>

<div class="col-lg-12">
    <div class="card card-outline card-success shadow-sm">
	<div class="card-header bg-primary text-white">
            <?php if ($_SESSION['login_type'] != 3): ?>
                <!-- Additional content for other login types if needed -->
            <?php endif; ?>
        </div>
        <div class="card-body">
             <div class="form-row mb-3">
            <div class="col-md-3">
                <label for="client-filter">Client:</label>
                <select id="client-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $client_qry = $conn->query("SELECT DISTINCT CLIENT FROM saved_claims");
                    while($client_row = $client_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo htmlspecialchars($client_row['CLIENT']); ?>"><?php echo htmlspecialchars($client_row['CLIENT']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="worktype-filter">Work Type:</label>
                <select id="worktype-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $worktype_qry = $conn->query("SELECT DISTINCT Worktype FROM saved_claims");
                    while($worktype_row = $worktype_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo htmlspecialchars($worktype_row['Worktype']); ?>"><?php echo htmlspecialchars($worktype_row['Worktype']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="month-filter">Month:</label>
                <select id="month-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $month_qry = $conn->query("SELECT DISTINCT Month FROM saved_claims");
                    while($month_row = $month_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo htmlspecialchars($month_row['Month']); ?>"><?php echo htmlspecialchars($month_row['Month']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="pm-filter">Project Manager:</label>
                <select id="pm-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $pm_qry = $conn->query("SELECT DISTINCT P_Manager FROM saved_claims");
                    while($pm_row = $pm_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo htmlspecialchars($pm_row['P_Manager']); ?>"><?php echo htmlspecialchars($pm_row['P_Manager']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
    </div>
      </div>
    
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-condensed" id="list">
                    <colgroup>
                        <col width="5%">
                        <col width="5%">
                        <col width="5%">
                        <col width="15%">
                        <col width="15%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead style="background-color:#032033 !important; color:white">
                        <tr>
                            <th>Period</th>
                            <th>Month</th>
                            <th>Job_ID</th>
                            <th>Member</th>
                            <th>Project Manager</th>
                            <th>Job Name</th>
                            <th>CLIENT</th>
                            <th>Work Type</th>
                            <th>Activity</th>
                            <th>Assigned_Start_Date</th>
                            <th>Assigned_End_Date</th>
                            <th>Done_Date</th>
                            <th>Discount Applied</th>
                            <th>Claim Amount</th>
                            <th>Claim Status</th>
                            <th>Date Processed</th>
                            <th>Who Processed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $conn->prepare("SELECT sc.*, 
                                                CONCAT(u.firstname, ' ', u.lastname) AS Member,  
                                                CONCAT(u1.firstname, ' ', u1.lastname) AS Approved_by
                                         FROM saved_claims sc 
                                         INNER JOIN users u ON sc.Member_ID = u.id
                                         INNER JOIN users u1 ON sc.Login_id = u1.id
                                         WHERE sc.time_recorded BETWEEN ? AND ?");
                                         
                                            $end .= ' 23:59:59';
                        
                        $stmt->bind_param("ss", $start, $end);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        while ($row = $result->fetch_assoc()):
                            $shortenedJobName = implode(' ', array_slice(explode(' ', $row['Job_Name']), 0, 5)) . '...';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['period']); ?></td>
                            <td><?php echo htmlspecialchars(ucwords($row['Month'])); ?></td>
                            <td><?php echo htmlspecialchars($row['Job_ID']); ?></td>
                            <td><?php echo htmlspecialchars(ucwords($row['Member'])); ?></td>
                            <td><?php echo htmlspecialchars(ucwords($row['P_Manager'])); ?></td>
                            <td><?php echo htmlspecialchars(ucwords($shortenedJobName)); ?></td>
                            <td><?php echo htmlspecialchars($row['CLIENT']); ?></td>
                            <td><?php echo htmlspecialchars($row['Worktype']); ?></td>
                            <td><?php echo htmlspecialchars($row['Activity']); ?></td>
                            <td><?php echo htmlspecialchars($row['Start_Date']); ?></td>
                            <td><?php echo htmlspecialchars($row['End_Date']); ?></td>
                            <td><?php echo htmlspecialchars($row['Done_Date']); ?></td>
                            <td><?php echo htmlspecialchars($row['Discount_Applied']); ?></td>
                            <td>R <?php echo htmlspecialchars($row['Claim_Amount']); ?></td>
                            <td><?php 
                                if ($row['claim_status'] == 1) {
                                    echo '<p style="color:green"><b>Approved</b></p>';
                                } elseif ($row['claim_status'] == 2) {
                                    echo '<p style="color:red"><b>Rejected</b></p>';
                                } else {
                                    echo '<p style="color:orange"><b>Pending</b></p>';
                                }
                            ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($row['time_recorded']))); ?></td>
                            <td><?php echo htmlspecialchars($row['Approved_by']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .table-responsive {
        overflow-x: auto;
    }
    table p {
        margin: unset !important;
    }
    table td {
        vertical-align: middle !important;
    }
</style>

<script>
$(document).ready(function(){
    var dataTable = $('#list').DataTable();

    $('#client-filter, #worktype-filter, #month-filter, #pm-filter').change(function(){
        filterTable();
    });

    function filterTable() {
        var selectedClient = $('#client-filter').val();
        var selectedWorkType = $('#worktype-filter').val();
        var selectedMonth = $('#month-filter').val();
        var selectedPM = $('#pm-filter').val();

        // Apply the filters to the DataTable
        dataTable.column(6).search(selectedClient)
                 .column(7).search(selectedWorkType)
                 .column(1).search(selectedMonth)
                 .column(4).search(selectedPM)
                 .draw();
    }

    $('.delete_project').click(function(){
        _conf("Are you sure to delete this job?", "delete_project", [$(this).attr('data-id')]);
    });
});

function delete_project(id){
    start_load();
    $.ajax({
        url: 'ajax.php?action=delete_project',
        method: 'POST',
        data: {id: id},
        success: function(resp){
            if(resp == 1){
                alert_toast("Data successfully deleted", 'success');
                setTimeout(function(){
                    location.reload();
                }, 1500);
            }
        }
    });
}
</script>
