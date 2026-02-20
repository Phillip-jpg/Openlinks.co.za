<?php
// Check if client_id is set in the POST data
if (isset($_POST['client_id'])) {
    // Retrieve the client_id from POST request
    $CLIENT_ID = $_POST['client_id'];
} else {
    echo "No client selected.";
    exit;
}
?>

<div class="col-lg-12">
    <div class="card card-outline card-success">
        <div class="card-header">
            <?php if ($_SESSION['login_type'] != 3): ?>
                <!-- Optional content for non-type 3 logins -->
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-condensed" id="list">
                    <colgroup>
                        <col width="20%">
                        <col width="20%">
                        <col width="20%">
                        <col width="20%">
                         <col width="20%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Assigned ID</th>
                            <th>Accounting Officer</th>
                             <th>Assigned Status</th>
                              <th>Month Assigned</th>
                            <th>Date Assigned</th>
                           
                           
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $qry = $conn->query("
                           SELECT 
    c.company_name,
    ao.Table_ID AS Assigned_No,
    DATE_FORMAT(c.created, '%d-%m-%Y') AS create_date,
    ind.office,
    indt.title,
    GROUP_CONCAT(cr.REP_NAME SEPARATOR ', ') AS Rep_Names,
    CONCAT(u.firstname, ' ', u.lastname) AS Accounting_Officer,
    ao.Date_assigned,
    MONTHNAME(ao.Date_assigned) AS Month_Assigned,
    CASE 
        WHEN ao.Table_ID = (
            SELECT MAX(ao2.Table_ID) 
            FROM accountng_officers ao2 
            WHERE ao2.CLIENT_ID = ao.CLIENT_ID
        ) THEN 'Current'
        ELSE 'Previous'
    END AS Record_Status
FROM 
    accountng_officers ao
LEFT JOIN 
    yasccoza_openlink_market.client c ON ao.CLIENT_ID = c.CLIENT_ID
LEFT JOIN 
    client_rep cr ON cr.CLIENT_ID = ao.CLIENT_ID
LEFT JOIN 
    users u ON u.id = ao.Accounting_Officer_ID
LEFT JOIN 
    yasccoza_openlink_association_db.industry ind ON ind.INDUSTRY_ID = c.office_id
LEFT JOIN 
    yasccoza_openlink_association_db.industry_title indt ON indt.TITLE_ID = c.industry_id
WHERE
    ao.CLIENT_ID = $CLIENT_ID
GROUP BY 
    c.company_name, ao.Table_ID, c.created, Accounting_Officer, ao.Date_assigned
ORDER BY 
    ao.Table_ID DESC;

                        ");
                        
                        while ($row = $qry->fetch_assoc()):
                            $Client = htmlspecialchars($row['company_name']);
                            $date_Created = htmlspecialchars($row['create_date']);
                            $rep_name = htmlspecialchars($row['Rep_Names']);
                            $office = htmlspecialchars($row['office']);
                            $industry = htmlspecialchars($row['title']);
                        ?>
                        <tr>
                            <td class="text-center" style="color:#007bff; font-weight:bold">As:<?php echo $row['Assigned_No'] ?></td>
                            <td><b><?php echo ucwords($row['Accounting_Officer']) ?></b></td>
                             <td class="text-center" style="color: <?php echo ($row['Record_Status'] == 'Current') ? 'green' : 'red'; ?>">
    <?php echo htmlspecialchars($row['Record_Status']); ?>
</td>

                            <td class="text-center"><?php echo htmlspecialchars($row['Month_Assigned']) ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($row['Date_assigned']) ?></td>
                            
                            
                             
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <div class="container mt-4">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h2>Client: <?php echo $Client ?></h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><b>Date Created: </b><?php echo $date_Created; ?></p>
                </div>
                <div class="col-md-6">
                    <p><b>Rep(s): </b><?php echo $rep_name; ?></p>
                </div>
                <div class="col-md-6">
                    <p><b>Office: </b><?php echo $office; ?></p>
                </div>
                <div class="col-md-6">
                    <p><b>Industry: </b><?php echo $industry; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
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
    table td, table th {
        vertical-align: middle !important;
    }
    table thead th {
        position: sticky;
        top: 0;
        background-color: #fff;
        z-index: 1;
    }
</style>

<script>
    $(document).ready(function(){
        var dataTable = $('#list').DataTable({
            "order": [[0, "desc"]] // Orders by the first column (Assigned ID) in descending order
        });

        $('#office-filter, #client-filter, #work-filter').change(function(){
            filterTable();
        });

        function filterTable() {
            dataTable
                .columns(6).search($('#office-filter').val())
                .columns(7).search($('#client-filter').val())
                .columns(8).search($('#work-filter').val())
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
