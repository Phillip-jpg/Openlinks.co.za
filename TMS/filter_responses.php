<?php
if (isset($_GET['start']) && isset($_GET['end'])) {
    $start = htmlspecialchars($_GET['start']);
    $end = htmlspecialchars($_GET['end']);
} else {
    echo "No dates provided.";
}
?>

<div class="col-lg-12">
      <div class="card card-outline card-success shadow-sm">
	<div class="card-header bg-primary text-white">
        <?php
        echo "Start Date: " . $start;
        echo "<br>";
        echo "End Date: " . $end;
        ?>
        <div class="card-header">
            <?php if($_SESSION['login_type'] != 3): ?>
                <!-- You can add content here if needed -->
            <?php endif; ?>
        </div>
         </div>
          </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-condensed" id="list">
                    <colgroup>
                        <col width="10%">
                        <col width="45%">
                        <col width="15%">
                        <col width="15%">
                        <col width="15%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead style="background-color:#032033 !important; color:white">
                        <tr>
                                <th>Month_Responded</th>
                                <th>Job_ID</th>
                                <th>Job_Name</th>
                                <th>Scorecard</th>
                                <th>Client</th>
                                <th>Created</th>
                            <th>Date_Closing</th>
                            <th>Date_Responded</th>
                            <th>Status</th>
                            <th>Respondent</th>
                            <th>Respondent_For</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $stat = array("Pending", "Started", "On-Progress", "On-Hold", "Over Due", "Done");
                        $where = "";
                        if($_SESSION['login_type'] == 2){
                            $where = " where manager_id = '{$_SESSION['login_id']}' ";
                        }elseif($_SESSION['login_type'] == 3){
                            $where = " where concat('[',REPLACE(user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
                        }
                         $end .= ' 23:59:59';
                        $qry = $conn->query("SELECT DISTINCT
                m.POST_ID AS Job_ID,
                m.Title AS Job_Name,
                s.title AS Scorecard,
                c.company_name AS Company,
                DATE_FORMAT(m.Created, '%Y-%m-%d') AS Date_Job_Created,
                DATE_FORMAT(m.EXPIRY, '%Y-%m-%d') AS Date_Closing,
                DATE_FORMAT(sr.created, '%Y-%m-%d') AS Date_Responded,
                MONTHNAME(sr.created) AS Month_Responded,
                CONCAT(u.firstname, ' ', u.lastname) AS Respondent,
                sr.COMPANY AS Responded_For
            FROM
                yasccoza_openlink_market.market_post m,
                yasccoza_openlink_market.scorecard_response sr,
                yasccoza_openlink_market.scorecard s,
                yasccoza_tms_db.users u,
                yasccoza_openlink_market.client c
            WHERE
                m.POST_ID = sr.POST_ID
                AND m.SCORECARD_ID = s.SCORECARD_ID
                AND u.id = sr.USER_ID
                AND c.CLIENT_ID = m.CLIENT_ID
                AND sr.created >= '$start' AND sr.created <= '$end'
                AND m.ASSIGNED_TO='{$_SESSION['login_id']}'
            ORDER BY
                Job_ID DESC");
                        while($row = $qry->fetch_assoc()):
                            $jobStatus = ($row['Date_Closing'] >= $row['Date_Responded']) ? "Responded on time" : "Responded late";
                            $words = explode(' ', $row['Job_Name']);
                            $shortenedJobName = count($words) >= 2 ? implode(' ', array_slice($words, 0, 5)) . '...' : $row['Job_Name'];
                        ?>
                 
                        <tr data-office="<?php echo $row['OFFICE']; ?>" data-client="<?php echo $row['CLIENT']; ?>">
                             <td class="text-center" style="color:green; font-weight:bold"><?php echo ($row['Month_Responded']) ?></td>
                             <td class="text-center "  style="color:red; "><?php echo ($row['Job_ID']) ?></td>
                                <td><p><b><?php echo($shortenedJobName) ?></b></p></td>
                                            <td><p><b><?php echo ucwords($row['Scorecard']) ?></b></p></td>
                                                    <td><p><b><?php echo ucwords($row['Company']) ?></b></p></td>
                           <td><p><b><?php echo ucwords($row['Date_Job_Created']) ?></b></p></td>
                                 <td><p><b><?php echo ucwords($row['Date_Closing']) ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['Date_Responded']) ?></b></p></td>
                                 <td><p><b><?php echo($jobStatus) ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['Respondent']) ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['Responded_For']) ?></b></p></td>
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

        $('#office-filter').change(function(){
            filterTable();
        });

        $('#client-filter').change(function(){
            filterTable();
        });
        
        $('#work-filter').change(function(){
            filterTable();
        });

        function filterTable() {
            var selectedOffice = $('#office-filter').val();
            var selectedClient = $('#client-filter').val();
            var selectedWorkType = $('#work-filter').val();

            dataTable.columns(6).search(selectedOffice).columns(7).search(selectedClient).columns(8).search(selectedWorkType).draw();
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
