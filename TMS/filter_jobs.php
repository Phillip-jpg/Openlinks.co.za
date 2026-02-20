<?php
include('db_connect.php');
session_start();

if (isset($_GET['start']) && isset($_GET['end'])) {
    $start = htmlspecialchars($_GET['start']);
    $end = htmlspecialchars($_GET['end']);
} else {
    echo "No dates provided.";
    exit;
}
?>

<div class="col-lg-12">
    <div class="card card-outline card-success shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title">Jobs Report</h4>
            <div>
                <?php
                echo "Start Date: " . $start;
                echo "<br>";
                echo "End Date: " . $end;
                ?>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-condensed" id="list">
                    <colgroup>
                        <col width="5%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="15%">
                        <col width="15%">
                        <col width="15%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    	<thead style="background-color:#032033 !important; color:white">
                        <tr>
                            <th>Period</th>
                            <th>Month</th>
                            <th>Start Week</th>
                            <th>End Week</th>
                            <th>Job Name</th>
                            <th>Job Manager</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $qry = $conn->query("SELECT
                                                YEARWEEK(
                                                    DATE_SUB(pl.date_created, INTERVAL WEEKDAY(pl.date_created) DAY),
                                                    1
                                                ) AS period,
                                            
                                                /* Monday of the week */
                                                DATE_SUB(pl.date_created, INTERVAL WEEKDAY(pl.date_created) DAY) AS start_week,
                                            
                                                /* Friday of the week */
                                                DATE_ADD(
                                                    DATE_SUB(pl.date_created, INTERVAL WEEKDAY(pl.date_created) DAY),
                                                    INTERVAL 4 DAY
                                                ) AS end_week,
                                            
                                                MONTHNAME(pl.date_created) AS month,
                                            
                                                pl.name AS Job_Name,
                                                pl.id   AS Job_ID,
                                                CONCAT(u.firstname, ' ', u.lastname) AS Job_Manager,
                                                COALESCE(c.company_name, smme.Legal_name) AS CLIENT,
                                                pl.status
                                            
                                            FROM project_list pl
                                            
                                            LEFT JOIN users u 
                                                ON pl.manager_id = u.id
                                            
                                            LEFT JOIN yasccoza_openlink_market.client c 
                                                ON pl.CLIENT_ID = c.CLIENT_ID
                                            
                                            LEFT JOIN yasccoza_openlink_smmes.register smme 
                                                ON pl.CLIENT_ID = smme.SMME_ID
                                            
                                            WHERE
                                                pl.manager_id = '{$_SESSION['login_id']}'
                                                AND (
                                                    pl.date_created BETWEEN '$start' AND '$end'
                                                    OR pl.end_date     BETWEEN '$start' AND '$end'
                                                    OR pl.Job_Done     BETWEEN '$start' AND '$end'
                                                )
                                            
                                            GROUP BY
                                                period,
                                                start_week,
                                                end_week,
                                                month,
                                                pl.id,
                                                pl.name,
                                                pl.status,
                                                pl.manager_id
                                            
                                            ORDER BY
                                                start_week ASC;
                                            ");
                        while ($row = $qry->fetch_assoc()):
                            $words = explode(' ', $row['Job_Name']);
                            $shortenedJobName = count($words) >= 5 ? implode(' ', array_slice($words, 0, 5)) . '...' : $row['Job_Name'];
                        ?>
                        <tr>
                            <td class="text-center" style="color: red; font-weight: bold;"><?php echo ($row['period']) ?></td>
                            <td style="color: green;"><p><b><?php echo ucwords($row['month']) ?></b></p></td>
                            <td class="text-center"><?php echo ($row['start_week']) ?></td>
                            <td class="text-center"><?php echo ($row['end_week']) ?></td>
                            <td><p><b><?php echo ucwords($shortenedJobName) ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['Job_Manager']) ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['CLIENT']) ?></b></p></td>
                            <td class="text-center">
                                <?php
                                switch ($row['status']) {
                                    case 'In-progress':
                                        echo "<span class='badge badge-info'>{$row['status']}</span>";
                                        break;
                                    case 'On-Hold':
                                        echo "<span class='badge badge-warning'>{$row['status']}</span>";
                                        break;
                                    case 'Dropped':
                                        echo "<span class='badge badge-danger'>{$row['status']}</span>";
                                        break;
                                    case 'Done':
                                        echo "<span class='badge badge-success'>{$row['status']}</span>";
                                        break;
                                    default:
                                        echo "<span class='badge badge-secondary'>{$row['status']}</span>";
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Action</button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item view_project" href="./index.php?page=view_job&id=<?php echo $row['Job_ID'] ?>" data-id="<?php echo $row['Job_ID'] ?>">View</a>
                                </div>
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
    .card-header {
        background-color: #007bff;
        color: white;
        padding: 10px;
    }
    .table-responsive {
        overflow-x: auto;
    }
    table p {
        margin: unset !important;
    }
    table td, table th {
        vertical-align: middle !important;
    }
    .badge {
        font-size: 0.875rem;
    }
    .table-hover tbody tr:hover {
        background-color: #f9f9f9;
    }
    .table th {
        background-color: #343a40;
        color: white;
    }
    table thead th {
        position: sticky;
        top: 0;
        background-color: #343a40;
        z-index: 1;
    }
    .btn-default {
        background-color: white;
        border-color: #ddd;
    }
</style>

<!-- JavaScript -->
<script>
    $(document).ready(function(){
        var dataTable = $('#list').DataTable();

        $('#office-filter').change(function(){
            filterTable();
        });

        $('#client-filter').change(function(){
            filterTable();
        });

        function filterTable() {
            var selectedOffice = $('#office-filter').val();
            var selectedClient = $('#client-filter').val();
            dataTable.columns(6).search(selectedOffice).columns(7).search(selectedClient).draw();
        }
    });

    function delete_project(id) {
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
