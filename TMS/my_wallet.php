<?php include 'db_connect.php'; ?>


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
                <label for="worktype-filter">Filter:</label>
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
                <label for="month-filter">Filter:</label>
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
                <label for="pm-filter">Filter:</label>
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
                             <th>Job_ID</th>
                            <th>Month</th>
                            <th>Team</th>
                            <th>Job Name</th>
                            <th>CLIENT</th>
                            <th>Work Type</th>
                            <th>Activity</th>
                            <th>Job Start Date</th>
                            <th>Job End Date</th>
                            <th>Job Done Date</th>
                            <!--<th>Working Days Left</th>-->
                            <th>Status</th>
                            <th>Processed</th>
                            <th>Payment Status</th>
                            <th>Date Processed</th>
                            <th>Who Processed</th>
                            <th>Quantities Done</th>
                            <th>Initial Rate</th>
                            <th>Total Claimable</th>
                            <th>Openlinks Service Fee</th>
                            <th>Production Team Fee</th>
                             <th>Adj Claimable</th>
                              <th>Lateness Penality Fee</th>
                                <th>Net_Pay Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        
                         $login_id = $_SESSION['login_id'];
                        $qry = $conn->query("SELECT DISTINCT
                                                pl.id,
                                                pl.name AS Job_Name,
                                                ad.user_id,
                                                ad.manager_id,
                                                ad.start_date,
                                                pl.start_date as Job_start_date,
                                                pl.start_date as Job_end_date,
                                                MONTHNAME(pl.Job_Done) as Month,
                                                pl.Job_Done as Job_done_date,
                                                pl.status as Job_done_status,
                                                ad.Date_Processed,
                                                ad.Discount_Applied,
                                                ad.Discount,
                                                ad.Pay_Out,
                                                ad.adj_claimable,
                                                ad.Openlinks_Serivce,
                                                ad.Production_Team,
                                                ad.my_discounted_rate,
                                                ad.pm_quantities,
                                                ad.end_date,
                                                ad.activity_id,
                                                ad.task_id,
                                                ts.team_name,
                                                ad.status,
                                                COALESCE(
                                                    c.company_name,
                                                    smme.Legal_name
                                                ) AS CLIENT,
                                                ad.start_date,
                                                tl.task_name AS Work_Type,
                                                ad.Done_Date,
                                                CONCAT(u.firstname, ' ', u.lastname) AS manager_name,
                                                CONCAT(u1.firstname, ' ', u1.lastname) AS member,
                                                CONCAT(u2.firstname, ' ', u2.lastname) AS Approved_by,
                                                up.name,
                                                ad.claim_status,
                                                up.rate,
                                                pl.JOB_TYPE
                                            FROM
                                                assigned_duties ad
                                            LEFT JOIN project_list pl ON
                                                pl.id = ad.project_id
                                            LEFT JOIN users u ON
                                                u.id = ad.manager_id
                                            LEFT JOIN user_productivity up ON
                                                up.id = ad.activity_id
                                            LEFT JOIN yasccoza_openlink_smmes.register smme
                                            ON
                                                pl.CLIENT_ID = smme.SMME_ID
                                            LEFT JOIN yasccoza_openlink_market.client c
                                            ON
                                                ad.CLIENT_ID = c.CLIENT_ID
                                            LEFT JOIN task_list tl ON
                                                ad.task_id = tl.id
                                            LEFT JOIN team_schedule ts ON
                                                pl.team_ids = ts.team_id
                                            LEFT JOIN users u1 ON
                                                u1.id = ad.user_id
                                             LEFT JOIN users u2 ON
                                                    u2.id = ad.approved_by  
                                            WHERE
                                                ad.user_id=$login_id");
                                        

                        while ($row = $qry->fetch_assoc()){
                            $shortenedJobName = implode(' ', array_slice(explode(' ', $row['Job_Name']), 0, 5)) . '...';
                            
                            $total_claims = round($row['pm_quantities'] * $row['rate'], 2);
                            $Total_income_contribution +=$total_claims;
                            
                            $value_lost+=round($row['my_discounted_rate'],2);
                            
                            $Adjusted_Earnings+=round($row['adj_claimable'],2);
                            
                            $total_fees += round($row['Openlinks_Serivce'], 2) + round($row['Production_Team'], 2);

                            
                            $claimed+=$row['Pay_Out'];
                            $claimable+=$row['rate'];
                            
                            $member=$row['member'];
                            
                            if($row['claim_status']==0){
                                
                                $unprocessed+=round($row['pm_quantities'] * $row['rate'], 2);
                            }
                            
                            
                             if($row['claim_status']==1){
                                
                                $processed+=round($row['pm_quantities'] * $row['rate'], 2);
                            }
                            
                           
                            
                            
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars(ucwords($row['id'])); ?></td>
                            <td><?php echo htmlspecialchars($row['Month']); ?></td>
                            <td><?php echo htmlspecialchars(ucwords($row['team_name'])); ?></td>
                             <td><?php echo htmlspecialchars(ucwords($shortenedJobName)); ?></td>
                             <td><?php echo htmlspecialchars($row['CLIENT']); ?></td>
                              <td><?php echo htmlspecialchars($row['Work_Type']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($row['Job_start_date']))); ?></td>
                            <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($row['Job_end_date']))); ?></td>
                            <td><?php echo htmlspecialchars($row['Job_done_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['Job_done_status']); ?></td>
                            <td><?php 
                                if ($row['claim_status'] == 1) {
                                    echo '<p style="color:green"><b>Processed</b></p>';
                                } elseif ($row['claim_status'] == 2) {
                                    echo '<p style="color:red"><b>Rejected</b></p>';
                                } else {
                                    echo '<p style="color:orange"><b>Unprocessed</b></p>';
                                }
                            ?></td>
                             <td><?php 
                                if ($row['claim_status'] == 1) {
                                    echo '<p style="color:green"><b>Paid</b></p>';
                                } elseif ($row['claim_status'] == 2) {
                                    echo '<p style="color:red"><b>Rejected</b></p>';
                                } else {
                                    echo '<p style="color:orange"><b>Unpaid</b></p>';
                                }
                            ?></td>
                  
                              <td><?php echo htmlspecialchars($row['Date_Processed']); ?></td>
                            <td><?php echo htmlspecialchars($row['Approved_by']); ?></td>
                             <td><?php echo htmlspecialchars($row['pm_quantities']); ?></td>
                              <td>R <?php echo htmlspecialchars($row['rate'],2); ?></td>
                             <td>R <?php echo htmlspecialchars($total_claims); ?></td>
                               <td>R <?php echo htmlspecialchars($row['Openlinks_Serivce'],2); ?></td>
                                       <td>R <?php echo htmlspecialchars($row['Production_Team'],2); ?></td>
                              <td>R <?php echo htmlspecialchars($row['adj_claimable'],2); ?></td>
                             <td>R <?php echo htmlspecialchars($row['my_discounted_rate'],2); ?></td>
                                 <td>R <?php echo htmlspecialchars($row['Pay_Out']); ?></td>
                        </tr>
                         <?php } ?>
                    </tbody>
                    
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h2>Statement Summary of <?php echo $member ?> Income</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <p><strong>Total income contribution: </strong>R <?php echo number_format($Total_income_contribution); ?></p>
                </div>
                 <div class="col-md-3">
                    <p><strong>Adjusted Earning To Date: </strong>R <?php echo number_format($Adjusted_Earnings); ?></p>
                </div>
                <div class="col-md-3">
                    <p><strong>Processed: </strong> R <?php echo number_format($processed); ?></p>
                </div>
                <div class="col-md-3">
                    <p><strong>UnProcessed: </strong> R <?php echo number_format($unprocessed); ?></p>
                </div>
               
            </div>
             <div class="row">
                <div class="col-md-3">
                    <p><strong>Value Lost To Date: </strong> R <?php echo number_format($value_lost,2); ?></p>
                </div>
                 <div class="col-md-3">
                    <p><strong>Total Net Earnings To Date : </strong> R <?php echo number_format($claimed); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<br>
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
