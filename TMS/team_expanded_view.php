<?php include'db_connect.php' ?>
<div class="col-lg-12">
		<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white">
			<div class="card-tools">
				
			</div>
           
		</div>
		<div class="card-body">
		   
             <br>
             	<div class="table-responsive">
				<table class="table tabe-hover table-condensed" id="list">
			<colgroup>
				<col width="5%">
					<col width="20%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead style="background-color:#032033 !important; color:white">
					<tr>
						<th >POST_ID</th>
						<th>Assigned Resources</th>
						<th>Project_name</th>
						<th>Work Type</th>
						<th>Activity</th>
						<th>Client Served</th>
						<th>Assigned</th>
						<th>Start Date</th>
						<th>Expired by date</th>
						<th>Duration by Expiry C</th>
						<th>Activity Duration in Wortype</th>
						<th>start_time</th>
						<th>Hour in work day</th>
						<th>end_time</th>
						<th>Target</th>
						<th>Activity Duration in Mins</th>
						<th>Activity Duration_By Mins</th>
						<th>Total Minutes by D of Worktype</th>
						<th>Expery sum of activity</th>
						 <th>Activities_Done</th>
					    <th>Job_Done</th>
					    <th>Sum of Actual time</th>
					     <th>Time_Utilization</th>
					     <th>Time Utilization Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					      
					       $worktype_id=$_GET['worktype_id'];
					        $team_id=$_GET['team_id'];
                            $qry = $conn->query("SELECT DISTINCT
                                    pl.id AS project_id,
                                    ad.user_id,
                                    pl.team_ids,
                                    pl.Job_Done,
                                    ts.team_name,
                                    ad.Done_Date,
                                    pl.name AS project_name,
                                    tl.start_time,
                                    tl.end_time,
                                    tl.task_name AS worktype,
                                    pl.start_date,
                                    pl.end_date,
                                    DATEDIFF(pl.end_date, pl.start_date) AS Duration_by_Expiry_condition, 
                                    DATEDIFF(pl.end_date, pl.start_date) * 24 * 60 AS Experiation_Min,
                                    up.name AS activity,
                                    c.company_name AS CLIENT,
                                    CONCAT(u.firstname, ' ', u.lastname) AS member,
                                    CONCAT(u1.firstname, ' ', u1.lastname) AS manager,
                                    CONCAT(u2.firstname, ' ', u2.lastname) AS operations_manager,
                                    up.duration,
                                    tl.target,
                                    CONCAT(
                                        FLOOR(
                                            TIME_TO_SEC(
                                                TIMEDIFF(tl.end_time, tl.start_time)
                                            ) / 3600
                                        ),
                                        '.',
                                        LPAD(
                                            FLOOR(
                                                MOD(
                                                    TIME_TO_SEC(
                                                        TIMEDIFF(tl.end_time, tl.start_time)
                                                    ),
                                                    3600
                                                ) / 60
                                            ),
                                            2,
                                            '0'
                                        )
                                    ) AS work_hours,
                                    TIME_TO_SEC(TIMEDIFF(tl.end_time, tl.start_time)) / 60 * up.duration AS Activity_Duration_in_Min,
                                    TIME_TO_SEC(TIMEDIFF(tl.end_time, tl.start_time)) / 60 * DATEDIFF(pl.end_date, pl.start_date) AS Total_Minutes_by_duration_worktype
                                FROM
                                    project_list pl
                                LEFT JOIN team_schedule ts ON
                                    ts.team_id = pl.team_ids
                                LEFT JOIN assigned_duties ad ON
                                    pl.id = ad.project_id
                                LEFT JOIN users u ON
                                    u.id = ad.user_id
                                LEFT JOIN task_list tl ON
                                    tl.id = ad.task_id
                                LEFT JOIN users u1 ON
                                    u1.id = ts.pm_manager
                                LEFT JOIN users u2 ON
                                    u2.id = ts.op_ids
                                LEFT JOIN user_productivity up ON
                                    up.id = ad.activity_id
                                LEFT JOIN yasccoza_openlink_market.client c
                                ON
                                    c.CLIENT_ID = pl.CLIENT_ID
                                WHERE
                                    pl.team_ids = $team_id
                                AND
                                    tl.id=$worktype_id
                                ORDER BY
                                    `project_id` ASC;
                        ");
                      $sumoftotal = 0;
                      $Experirysum=0;
                      $countofpost=0;
                      $donecount=0;
                      $timespent=0;
                      $target=0;
                      $perfect_utilisation=0;
                      $took_longer=0;
                      $still_counting=0;
                      $not_fully_utlisated=0;
                      
                      
					while ($row = $qry->fetch_assoc()):
						$total = $row['activity'] * $row['target'];
                        
                        $workhours=$row['work_hours'];
                        $team_name=$row['team_name'];
                        
                        $team_id=$row['team_ids'];
                     
                        
                        $manager= $row['manager'];
                        $operations_manager=$row['operations_manager'];
                        
                        
                        $worktype=$row['worktype'];
                        
                        $taskname= $row['task_name'];
                        
                        
                        $Total_Min_by_duration_wt+=$row['Activity_Duration_in_Min'] / $row['target'];
                        
                        $Experiry_sum_of_activity+=$row['Duration_by_Expiry_condition'];


						if ($row['target'] != 0) {
							$sumoftotal += $row['duration_worktype'] / $row['target'];
						}
					
							$Experirysum +=$row['Experiation_Min']/$row['target'];
							
							$countofpost++;
							
							if($row['Done_Date'] != null){
							     $donecount++;
							}
							
							if($row['target'] != null){
							     $target+=$row['target'];
							}
					?>
					<tr>
						<td>
							<p><?php echo ucwords($row['project_id']) ?></p>
						</td>
							<td>
								<p><?php echo ucwords($row['member']) ?></p>
						</td>
                        <td>
                            <p>
                                <?php 
                                    $words = explode(' ', ucwords($row['project_name']));
                                    echo implode(' ', array_slice($words, 0, 3));
                                ?>
                            </p>
                        </td>
                        <td>
								<p><?php echo ucwords($row['worktype']) ?></p>
						</td>
						<td>
							<p><?php echo ucwords($row['activity']) ?></p>
						</td>
						<td>
							<p><?php echo ucwords($row['CLIENT']) ?></p>
						</td>
						<td>
							<p>Yes</p>
						</td>
							<td>
							<p><?php echo ucwords($row['start_date']) ?></p>
						</td>
						<td>
							<p><?php echo ucwords($row['end_date']) ?></p>
						</td>
					    <td>
							<p><?php echo ucwords($row['Duration_by_Expiry_condition']) ?></p>
						</td>
					    <td>
							<p><?php echo ucwords($row['duration']) ?></p>
						</td>
                        <td>
							<p>
                                <?php 
                                    $time = new DateTime($row['start_time']);
                                    echo $time->format('H:i');
                                ?>
                            </p>
						</td>
					    <td>
						<p><?php echo ucwords($row['work_hours']) ?></p>
					    </td>
						<td>
								<p>
                                <?php 
                                    $time = new DateTime($row['end_time']);
                                    echo $time->format('H:i');
                                ?>
                            </p>
						</td>
							<td>
							<p><?php echo ucwords($row['target']) ?></p>
						</td>
						<td>
							<p>
                              <?php echo rtrim(rtrim($row['Activity_Duration_in_Min'], '0'), '.'); ?>
                            </p>
                        </td>
						<td>
							<p>
                              <?php echo rtrim(rtrim($row['Total_Minutes_by_duration_worktype'], '0'), '.'); ?>
                            </p>
                        </td>
						<td>
                            <p>
                                <?php echo $row['Activity_Duration_in_Min'] / $row['target']; ?>
                                </p>
                        </td>
                        <td>
                            <p>
                                <?php echo $row['Total_Minutes_by_duration_worktype'] / $row['target']; ?>
                                </p>
                        </td>
                        	<td>
							<p><?php 
							if(empty($row['Done_Date']))
							{
							    echo "Activity Not_Done";
							}
							else
							{
							echo $row['Done_Date'];
							} 
							
							?></p>
						</td>
                        	<td>
							<p><?php 
							if(empty($row['Job_Done']))
							{
							    echo "Not Done";
							}
							else
							{
							echo $row['Job_Done'];
							} 
							
							?></p>
						</td>
						<td>
                            <p>
                                <?php 
                                    $work_start_hour = $row['start_time'];
                                    $work_end_hour = $row['end_time'];
                                    
                                    $time_spend_on_job="No time";
                                    
                                    if (empty($row['Job_Done']) || $row['Job_Done'] == '0000-00-00 00:00:00') {
                                        
                                        $time_spend_on_job="Not finished";
                                        
                                        echo $time_spend_on_job;
                                    } else {
                                        $start = new DateTime($row['start_date']);
                                        $end = new DateTime($row['Job_Done']);
                                    
                                        if ($start > $end) {
                                            echo "0 hours";
                                        } else {
                                            $total_hours = 0;
                                            $current = clone $start;
                                    
                                            while ($current->format('Y-m-d') <= $end->format('Y-m-d')) {
                                                $weekday = $current->format('N');
                                                if ($weekday < 6) {
                                                    $work_start = clone $current;
                                                    $work_start->setTime($work_start_hour, 0);
                                    
                                                    $work_end = clone $current;
                                                    $work_end->setTime($work_end_hour, 0);
                                    
                                                    if ($current->format('Y-m-d') == $start->format('Y-m-d')) {
                                                        $actual_start = max($start, $work_start);
                                                        $actual_end = min($end, $work_end);
                                                    } elseif ($current->format('Y-m-d') == $end->format('Y-m-d')) {
                                                        $actual_start = $work_start;
                                                        $actual_end = min($end, $work_end);
                                                    } else {
                                                        $actual_start = $work_start;
                                                        $actual_end = $work_end;
                                                    }
                                    
                                                    $interval = $actual_end->getTimestamp() - $actual_start->getTimestamp();
                                                    $hours = max(0, $interval / 3600);
                                                    $total_hours += $hours;
                                                }
                                                $current->modify('+1 day');
                                            }
                                    
                                            $time_spend_on_job = round($total_hours * 60);
                                            echo $time_spend_on_job;
                                    
                                            $timespent += $time_spend_on_job;
                                        }
                                    }
                                    ?>
                            </p>
                        </td>
	                    <td>
                            <p><?php echo number_format($time_spend_on_job / ($row['Activity_Duration_in_Min'] / $row['target']), 2); ?>
                            </p>
                        </td>
                         <td>
                            <p><?php 
                                $time_status = $time_spend_on_job / ($row['Activity_Duration_in_Min'] / $row['target']);
                                if ($time_status >= 0.76 && $time_status <= 1.2) {
                                    echo "Perfect Utilization";
                                    $perfect_utilisation++;
                                    
                                } elseif ($time_status >= 1.3) {
                                    echo "Took longer than expected";
                                    $took_longer++;
                                    
                                } elseif ($time_status < 0.75 && $time_status > 0.01) {
                                    echo "Time was not fully used";
                                    $not_fully_utlisated++;
                                } elseif ($time_spend_on_job="Not finished"){
                                    echo "Time is still counting";
                                    $still_counting++;
                                }
                                ?>
                            </p>
                        </td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" >
		                      <a class="dropdown-item view_project" href="./index.php?page=view_job&id=<?php echo $row['project_id'] ?>" data-id="<?php echo $row['project_id'] ?>">View</a>
		                      
						
		                    </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
				 <div class="container mt-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5>Team: <?php echo $team_name ?></h5>
                            <hr style="border:2px solid white">
                            <h5>Work type: <?php echo $worktype ?></h5>
                            <hr style="border:2px solid white">
                            <h6>Production Manager: <?php echo $manager ?></h6>
                            <hr style="border:2px solid white">
                            <h6>Operational Leader: <?php echo $operations_manager ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <p>Total Activity: <span style="font-weight:bold"><?php echo number_format($countofpost); ?></span></p>
                                </div>
                                 <div class="col-md-4">
                                    <p>Total Minutes by duration of work type : <span style="font-weight:bold"> <?php echo number_format($Total_Min_by_duration_wt,2); ?></span></p>
                                </div>
                                <div class="col-md-4">
                                     <p>Expiry sum of activity : <span style="font-weight:bold"> <?php echo number_format($Experiry_sum_of_activity); ?></span></p>
                                </div>
                              </div>
                              <div class="row">     
                              <div class="col-md-4">
                                    <p >Total Done Activities :<span style="font-weight:bold"> <?php echo number_format($donecount); ?></span></p>
                                </div>
                                <div class="col-md-4">
                                     <p >Sum of actual time :<span style="font-weight:bold"> <?php echo number_format($timespent); ?></span></p>
                                </div>
                                 <div class="col-md-4">
                                    <p>Average time to complete activity : <span style="font-weight:bold"> <?php echo number_format($timespent/$donecount); ?></span></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <p> WIP : <span style="font-weight:bold"> <?php echo number_format($countofpost-$donecount); ?></span></p>
                                </div>
                                <div class="col-md-4">
                                     <p >Member of Contribution to Target : <span style="font-weight:bold"> <?php echo number_format($countofpost/($target/$countofpost)*100,2); ?>%</span></p>
                                </div>
                            </div>
                        </div>
                            <p style="background-color:#007bff; width:400px; height:25px; text-align:center; color:white; border-radius:10px; line-height:25px; margin: 0 auto;">
                          <a href="./index.php?page=work_type_actvities_summarised_view_one&team_id=<?= $team_id ?>&task_id=<?= $worktype_id ?>" 
                             style="color:white; text-decoration:none;">
                            Work Type Activities Summarised View
                          </a>
                        </p>
                        <br>

                      <div class="card-header" style="background-color: #032033; color: white; padding: 20px; border-radius: 10px; text-align: center;">
                            <?php $totalz = $perfect_utilisation + $fully_utlisated + $took_longer + $still_counting; ?>
                        
                            <h2 style="margin-bottom: 10px;">
                                Time Utilization Status Total: <?php echo $totalz; ?> --> 100%
                            </h2>
                            <hr style="border: 1px solid white; margin: 10px 0;">
                            <p style="margin: 10px 0; font-size: 16px;">
                                ✅ Perfect utilization: <strong><?php echo number_format($perfect_utilisation); ?></strong>
                                --> <span><?php echo number_format(($perfect_utilisation / $totalz) * 100); ?>%</span>
                            </p>
                        
                            <p style="margin: 10px 0; font-size: 16px;">
                                ⚠️ Time was not fully used: <strong><?php echo number_format($fully_utlisated); ?></strong>
                                --> <span><?php echo number_format(($fully_utlisated / $totalz) * 100); ?>%</span>
                            </p>
                        
                            <p style="margin: 10px 0; font-size: 16px;">
                                🕒 Task took longer than expected: <strong><?php echo number_format($took_longer); ?></strong>
                                --> <span><?php echo number_format(($took_longer / $totalz) * 100); ?>%</span>
                            </p>
                        
                            <p style="margin: 10px 0; font-size: 16px;">
                                ⏳ Time still Counting: <strong><?php echo number_format($still_counting); ?></strong>
                                --> <span><?php echo number_format(($still_counting / $totalz) * 100); ?>%</span>
                            </p>
                        </div>
                    </div>
                </div>
			</table>
		</div>
		</div>
	</div>
</div>
<style>
	table p{
		margin: unset !important;
	}
	table td{
		vertical-align: middle !important
	}
</style>
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
         dataTable.column(3).search(selectedmonth) /// Job Type filter on 3rd column (index 2)
            .column(4).search(selectedCreator)      // Who Created it filter on 5th column (index 4)
            .column(5).search(selectedAssigned)     // Assigned filter on 6th column (index 5)
            .column(6).search(selectedStatus)       // Status filter on 7th column (index 6)
            .draw();  // Redraw the table with the new filters
    }

    // Handle deletion of projects

});


	$(document).ready(function(){
		$('#list').dataTable()
	
	$('.delete_project').click(function(){
	_conf("Are you sure to delete this job?","delete_project",[$(this).attr('data-id')])
	})
	})
	function delete_project($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_project',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>