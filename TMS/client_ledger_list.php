<?php 
include 'db_connect.php'; 

$team_id = isset($_GET['team_id']) ? intval($_GET['team_id']) : 0;
$special_team_id = 50000; // Magic number replaced with variable

if ($team_id === $special_team_id) {
    $qry = $conn->query("
        SELECT c.*, COUNT(ad.project_id) AS number_of_jobs
        FROM project_list pl
        LEFT JOIN yasccoza_openlink_market.client c 
            ON c.CLIENT_ID = pl.CLIENT_ID
        LEFT JOIN assigned_duties ad 
            ON pl.id = ad.project_id
        GROUP BY c.CLIENT_ID
    ");
} else {
    $qry = $conn->query("
        SELECT c.*, COUNT(ad.project_id) AS number_of_jobs
        FROM project_list pl
        LEFT JOIN yasccoza_openlink_market.client c 
            ON c.CLIENT_ID = pl.CLIENT_ID
        LEFT JOIN assigned_duties ad 
            ON pl.id = ad.project_id
        WHERE pl.team_ids = {$team_id}
        GROUP BY c.CLIENT_ID
    ");
}
?>
<div class="col-lg-12">
    <div class="card card-outline card-success shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="card-tools"></div>
        </div>
        <div class="card-body">
            <br>
            <div class="table-responsive">
                <table class="table tabe-hover table-condensed" id="list">
                    <thead style="background-color:#032033 !important; color:white">
                        <tr>
                            <th>Client ID</th>
                            <th>Client</th>
                            <th>Client Email</th>
                            <th>Number of Activities</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $qry->fetch_assoc()): ?>
                            <?php $client_id = $row['CLIENT_ID']; ?>
                            <tr>
                                <td><p><?= ucwords($row['CLIENT_ID']); ?></p></td>
                                <td><p><?= ucwords($row['company_name']); ?></p></td>
                                <td><p><?= ucwords($row['Email']); ?></p></td>
                                <td><p><?= ucwords($row['number_of_jobs']); ?></p></td>
                                <td class="text-center">
                                    <button type="button" 
                                            class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" 
                                            data-toggle="dropdown" aria-expanded="true">
                                        Action
                                    </button>
                                    <div class="dropdown-menu">
                                        <?php if ($team_id === $special_team_id): ?>
                                            <a class="dropdown-item view_project" 
                                               href="./index.php?page=individual_client&client_id=<?= $row['CLIENT_ID']; ?>">
                                                View
                                            </a>
                                        <?php else: ?>
                                            <a class="dropdown-item view_project" 
                                               href="./index.php?page=client_ledger&client_id=<?= $client_id; ?>&team_id=<?= $team_id; ?>">
                                                View
                                            </a>
                                        <?php endif; ?>
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

<style>
    table p { margin: unset !important; }
    table td { vertical-align: middle !important; }
</style>

<script>
$(document).ready(function(){
    $('#list').DataTable();

    $('.delete_project').click(function(){
        _conf("Are you sure to delete this job?", "delete_project", [$(this).attr('data-id')]);
    });
});

function delete_project(id){
    start_load();
    $.ajax({
        url: 'ajax.php?action=delete_project',
        method: 'POST',
        data: { id: id },
        success: function(resp){
            if(resp == 1){
                alert_toast("Data successfully deleted", 'success');
                setTimeout(function(){ location.reload(); }, 1500);
            }
        }
    });
}
</script>
