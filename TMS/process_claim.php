<?php
// Include the database connection file
include 'db_connect.php';

$result = null; // Initialize the $result variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Check if the necessary POST parameters are set
    if (isset( $_POST['activity_id'], $_POST['login_id'], $_POST['user_id'])) {
        
    
  
   
    $login_id=$_POST['login_id'];
        $userId = $_POST['user_id'];
        $activity_id = $_POST['activity_id'];
        $pm_id = $_POST['pm_id'];
        $period = $_POST['period'];
        
        $pm_quantities=$_POST['pm_quantities'];
        $team_id=$_POST['team_id'];
        
        $loginId = $_POST['login_id']; 
          $job_id=$_POST['job_id'];
          $worktype_id=$_POST['worktype_id'];
        $month=$_POST['month'];
        
        $Job_start_date=$_POST['job_start_date'];
        $Job_end_date=$_POST['job_end_date'];
        
        
        $actual_job_done=$_POST['actual_job_done'];
      
         $team_name=$_POST['team_name'];
         
         $Activity=$_POST['Activity'];
          $rate=$_POST['rate'];
             $client=$_POST['client'];
             $member=$_POST['member'];
             $manager=$_POST['manager'];
             $done=$_POST['done'];
             $should_done=$_POST['end_date'];
             
             $actual_done=$_POST['actual_done'];
             
             $jobname=$_POST['jobname'];
             $worktype=$_POST['worktype'];
              $start=$_POST['start'];
             $end=$_POST['end'];
            $claim_status=$_POST['claim_status'];
             

    
    
$qry3 = $conn->query("
    SELECT COUNT(up.id) AS number_of_activities
    FROM task_list tl, user_productivity up
    WHERE tl.id = up.task_id
    AND tl.id = $worktype_id
");

$row3 = $qry3->fetch_assoc();
$numberofactivities = $row3['number_of_activities'];
    
$qry1 = $conn->query("
    SELECT *
    FROM contracts c
    JOIN billing_configuration bc ON bc.contract_id = c.contract_id
    WHERE c.team_id = $team_id AND c.work_type_billing = $worktype_id
");

if ($qry1) {

        $totalopenlinks_serivce = 0;
        $openlinks_updated1 = 0;
        $openlinks_updated2 = 0;
        $openlinks_updated3 = 0;
        $openlinks_updated4 = 0;
        $openlinks_updated5 = 0;
        $openlinks_updated6 = 0;
        
        $total_production_team = 0;
        $production_updated1 = 0;
        $production_updated2 = 0;
        $production_updated3 = 0;
        $production_updated4 = 0;
        $production_updated5 = 0;
        $production_updated6 = 0;


while ($row = $qry1->fetch_assoc()) {
    
     if ($row['application'] == 20 && $row['conditions'] == 124) {
            if ($row['Billing_Type'] == 31) {
                $openlinks_services1 = ($row['Rate'] / $numberofactivities) * $pm_quantities;
                $openlinks_updated1 += $openlinks_services1;
            } elseif ($row['Billing_Type'] == 32) {
                $openlinks_services2 = ($row['Rate'] / $numberofactivities) * $pm_quantities;
                $openlinks_updated2 += $openlinks_services2;
            } elseif ($row['Billing_Type'] == 33) {
                $openlinks_services3 = (($row['Rate'] / $numberofactivities) * $rate) * $pm_quantities;
                $openlinks_updated3 += $openlinks_services3;
            }
        }

        if ($row['application'] == 20 && $row['conditions'] == 123) {
            if ($row['Billing_Type'] == 31) {
                $openlinks_services4 = $row['Rate'] * $pm_quantities;
                $openlinks_updated4 += $openlinks_services4;
            } elseif ($row['Billing_Type'] == 32) {
                $openlinks_services5 = $row['Rate'] * $pm_quantities;
                $openlinks_updated5 += $openlinks_services5;
            } elseif ($row['Billing_Type'] == 33) {
                $openlinks_services6 = ($row['Rate'] * $rate) * $pm_quantities;
                $openlinks_updated6 += $openlinks_services6;
            }
        }
    

    $totalopenlinks_serivce = $openlinks_updated1 + $openlinks_updated2+$openlinks_updated3+$openlinks_updated4+$openlinks_updated5+$openlinks_updated6 ;
    
    
    if ($row['application'] == 21 && $row['conditions'] == 124 && $row['Billing_Type'] == 31) {
        $production_team1 = ($row['Rate'] / $numberofactivities)*$pm_quantities;
        $production_updated1 += $production_team1;
    }
    
    if ($row['application'] == 21 && $row['conditions'] == 124 && $row['Billing_Type'] == 32) {
        $production_team2 = ($row['Rate'] / $numberofactivities)*$pm_quantities;
        $production_updated2 += $production_team2;
    }
    
    if ($row['application'] == 21 && $row['conditions'] == 124 && $row['Billing_Type'] == 33) {
        $production_team3 = (($row['Rate'] / $numberofactivities)*$rate)*$pm_quantities;
        $production_updated3 += $production_team3;
    }

    if ($row['application'] == 21 && $row['conditions'] == 123 && $row['Billing_Type'] == 31) {
        $production_team4 = $row['Rate']*$pm_quantities;
        $production_updated4 += $production_team4;
    }
    
     if ($row['application'] == 21 && $row['conditions'] == 123 && $row['Billing_Type'] == 32) {
        $production_team5 = $row['Rate']*$pm_quantities;
        $production_updated5 += $production_team5;
    }
    
    
     if ($row['application'] == 21 && $row['conditions'] == 123 && $row['Billing_Type'] == 33) {
        $production_team6 = ($row['Rate']*$rate)*$pm_quantities;
        $production_updated6 += $production_team6;
        
    }

    $total_production_team = $production_updated1 + $production_updated2+$production_updated3 + $production_updated4+$production_updated5 + $production_updated6;
    
    $contract_name=$row['name_of_contract'];
    
    // echo $contract_name;
}

// echo "Total OpenLinks Service Fee: R " . number_format($totalopenlinks_serivce, 2);
// echo "<br>";
// echo "Total Production Team Fee: R " . number_format($total_production_team, 2);

$total_fees=$totalopenlinks_serivce+$total_production_team;

$total_claims=$rate*$pm_quantities;
$Billiable_Deductable=$total_claims-$total_fees;



}

$actual_done_date_obj = new DateTime($actual_job_done);
$should_done_date_obj = new DateTime($Job_end_date);



// Calculate the difference between the two dates
$interval = $actual_done_date_obj->diff($should_done_date_obj);

// Get the difference in days
$days_exceeded = $interval->days;


// Check if the actual done date is after the should done date
if ($actual_done_date_obj > $should_done_date_obj) {
    
     $qry = $conn->query("
    SELECT discount, name 
    FROM configure_rate 
    WHERE low <= $days_exceeded 
    AND $days_exceeded <= high 
    AND worktype_ids = $worktype_id
");

// Fetch the result from the query
if ($qry && $row = $qry->fetch_assoc()) {
    $discount = $row['discount'];
    $discount_name = $row['name'];

    // Calculate the new rate after applying the discount
    
    $total_discounted_rate=($Billiable_Deductable * ($discount / 100));
    
    $discounted_rate= $Billiable_Deductable-((40/100)*$total_discounted_rate);
    
    $member_discounted_rate= ((40/100)*$total_discounted_rate);
    
    $team_discounted_rate= ((60/100)*$total_discounted_rate);
 

    // Output the discount name and the new discounted rate
    // echo "Discount Name: " . $discount_name . "<br>";
    // echo "Billable Deductable: R" . number_format($Billiable_Deductable, 2) . "<br>";
    // echo "Discount Percentage: " . $discount . "%<br>";
    // echo "New Billable Deductable After Discountand 40% applied: R" . number_format($discounted_rate, 2);
} else {

     $discount="No discount";
    $discounted_rate=$Billiable_Deductable;
    $days_exceeded="No days exceeded";
    $discount_name="N/A";
    
    
}
             
    } else {
        

     $discount="No discount";
    $discounted_rate=$Billiable_Deductable;
    $days_exceeded="No days exceeded";
    $discount_name="N/A";
    
       
       
    }
    
}

}
?>


<br>
<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-body">
            

        <form id="manage-claim" method="post" action="./index.php?page=save_claim">
    <!-- Hidden inputs on top -->
    <input type="hidden" name="job_id" value="<?php echo isset($job_id) ? $job_id : '' ?>">
    <input type="hidden" name="login_id" value="<?php echo isset($loginId) ? $loginId : '' ?>">
    <input type="hidden" name="activity_id" value="<?php echo isset($activity_id) ? $activity_id : '' ?>">
    <input type="hidden" name="worktype_id" value="<?php echo isset($worktype_id) ? $worktype_id : '' ?>">
    <input type="hidden" name="user_id" value="<?php echo isset($userId) ? $userId : '' ?>">
    <input type="hidden" name="member" value="<?php echo isset($member) ? ucwords($member) : '' ?>">
    <input type="hidden" name="month" value="<?php echo isset($month) ? ucwords($month) : '' ?>">
    <input type="hidden" name="jobname" value="<?php echo isset($jobname) ? $jobname : '' ?>">
    <input type="hidden" name="team_name" value="<?php echo isset($team_name) ? $team_name : '' ?>">
    
    <input type="hidden" name="start_date" value="<?php echo isset($Job_start_date) ? ucwords($Job_start_date) : '' ?>">
    <input type="hidden" name="end_date" value="<?php echo isset($Job_end_date) ? ucwords($Job_end_date) : '' ?>">
    
    <input type="hidden" name="manager" value="<?php echo isset($manager) ? ucwords($manager) : '' ?>">
    <input type="hidden" name="client" value="<?php echo isset($client) ? ucwords($client) : '' ?>">
    <input type="hidden" name="worktype" value="<?php echo isset($worktype) ? ucwords($worktype) : '' ?>">
    <input type="hidden" name="Activity" value="<?php echo isset($Activity) ? ucwords($Activity) : '' ?>">
    
    <input type="hidden" name="actual_done" value="<?php echo isset($actual_job_done) ? ucwords($actual_job_done) : '' ?>">
    
    
    <input type="hidden" name="done" value="<?php echo isset($done) ? ucwords($done) : '' ?>">
    
    <input type="hidden" name="days_exceeded" value="<?php echo isset($days_exceeded) ? $days_exceeded : '' ?>">
    <input type="hidden" name="rate" value="<?php echo isset($rate) ? $rate : '' ?>">
    
    <input type="hidden" name="discount_name" value="<?php echo isset($discount_name) ? $discount_name : '' ?>">
    <input type="hidden" name="discount" value="<?php echo isset($discount) ? $discount : '' ?>">
  
    <input type="hidden" name="pm_id" value="<?php echo isset($pm_id) ? $pm_id : '' ?>">
    <input type="hidden" name="period" value="<?php echo isset($period) ? $period : '' ?>">
     <input type="hidden" name="start" value="<?php echo isset($start) ? $start : '' ?>">
    <input type="hidden" name="end" value="<?php echo ($end) ? $end : '' ?>">
    
  <input type="hidden" name="member_discounted_rate" step="0.01" value="<?php echo isset($member_discounted_rate) ? number_format($member_discounted_rate, 2) : '' ?>">
<input type="hidden" name="team_discounted_rate" step="0.01" value="<?php echo isset($team_discounted_rate) ? number_format($team_discounted_rate, 2) : '' ?>">
<input type="hidden" name="discounted_rate" step="0.01" value="<?php echo isset($discounted_rate) ? number_format($discounted_rate, 2) : '' ?>">
<input type="hidden" name="Billiable_Deductable" step="0.01" value="<?php echo isset($Billiable_Deductable) ? number_format($Billiable_Deductable, 2) : '' ?>">
<input type="hidden" name="totalopenlinks_serivce" step="0.01" value="<?php echo isset($totalopenlinks_serivce) ? number_format($totalopenlinks_serivce, 2) : '' ?>">
<input type="hidden" name="total_production_team" step="0.01" value="<?php echo isset($total_production_team) ? number_format($total_production_team, 2) : '' ?>">


 

  
    

    <!-- Visible inputs and information inside the form group -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label style="text-decoration: underline;" for="" class="control-label">Member</label>
                <input type="text" class="form-control" readonly value="<?php echo ucwords($member) ?>">
                
                
                <label style="text-decoration: underline;" for="" class="control-label">Team Name</label>
                <input type="text" class="form-control" readonly value="<?php echo ucwords($team_name) ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">Team Id</label>
                <input type="text" class="form-control" readonly value="<?php echo ucwords($team_id) ?>">
                
                
                
                <label style="text-decoration: underline;" for="" class="control-label">Month</label>
                <input type="text" class="form-control" readonly value="<?php echo ucwords($month) ?>">
                
                   <label style="text-decoration: underline;" for="" class="control-label">Period</label>
                <input type="text" class="form-control" readonly value="<?php echo ucwords($period) ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">Job_ID</label>
                <input type="text" class="form-control" readonly style="color:blue; font-weight:bold;" value="<?php echo $job_id ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">Job_Name</label>
                <input type="text" class="form-control" readonly value="<?php echo $jobname ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">Start Date</label>
                <input type="text" class="form-control" readonly value="<?php echo date('d-m-Y', strtotime($Job_start_date)) ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">End Date</label>
                <input type="text" class="form-control" readonly value="<?php echo date('d-m-Y', strtotime($Job_end_date)) ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">Project Manager</label>
                <input type="text" class="form-control" readonly value="<?php echo ucwords($manager) ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">Client</label>
                <input type="text" class="form-control" readonly value="<?php echo ucwords($client) ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">Work Type</label>
                <input type="text" class="form-control" readonly value="<?php echo ucwords($worktype) ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">Activity</label>
                <input type="text" class="form-control" readonly value="<?php echo ucwords($Activity) ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">Actual Date Done</label>
                <input type="text" class="form-control" readonly value="<?php echo $actual_job_done ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">Days Exceeded</label>
                <input type="text" class="form-control" readonly value="<?php echo $days_exceeded ?>">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">

                
                <label style="text-decoration: underline;" for="" class="control-label">Original Rate</label>
                <input type="text" class="form-control" readonly value="R <?php echo number_format ($rate,2) ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">Quantity Done</label>
                <input type="text" class="form-control" readonly value="<?php echo $pm_quantities ?>">
                
                 <label style="text-decoration: underline;" for="" class="control-label">Total Claimable</label>
                <input type="text" class="form-control" readonly value="R <?php echo number_format($total_claims, 2)  ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">Contract </label>
                <input type="text" class="form-control" readonly value="<?php echo $contract_name ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">Openlinks Service Fee</label>
                <input type="text" class="form-control" readonly value="R <?php echo number_format($totalopenlinks_serivce,2) ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">Prodction Team Fee</label>
                <input type="text" class="form-control" readonly value="R <?php echo number_format($total_production_team,2) ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">Updated Claimable</label>
                <input type="text" class="form-control" readonly value="R <?php echo number_format($Billiable_Deductable,2) ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">Discount Applied</label>
                <input type="text" class="form-control" readonly value="<?php echo $discount_name ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">Discount Percentage</label>
                <input type="text" class="form-control" readonly value="<?php echo $discount ?>% applied">
                
                <label style="text-decoration: underline;" for="" class="control-label">Total To Be Discounted</label>
                <input type="text" class="form-control" readonly value="R <?php echo number_format($total_discounted_rate,2) ?>">
                
                  <label style="text-decoration: underline;" for="" class="control-label">60% Deduction from Team</label>
                <input type="text" class="form-control" readonly value="R <?php echo number_format($team_discounted_rate,2) ?>">
                
                   <label style="text-decoration: underline;" for="" class="control-label">40% Deduction from Member</label>
                <input type="text" class="form-control" readonly value="R <?php echo number_format($member_discounted_rate,2) ?>">
                
                 <label style="text-decoration: underline;" for="" class="control-label">New Claimable After 40% Deduction</label>
                <input type="text" class="form-control" readonly value="R <?php echo number_format($discounted_rate,2) ?>">
                
                <label style="text-decoration: underline;" for="" class="control-label">Final Claimable Amount</label>
                <input type="text" class="form-control" readonly style="color:red; font-weight:bold;font-size:18px" value="R <?php echo number_format($discounted_rate,2) ?>">

              
                   <?php 
if ($claim_status == 1 || $claim_status == 2) { 
?>
    <br>
    <input type="text" class="form-control" readonly style="color:red; font-weight:bold; font-size:18px" value="Claim Processed">
     <?php $qry = $conn->query("SELECT DISTINCT CONCAT(u.firstname, ' ', u.lastname) as approver, Date_Processed 
                     FROM assigned_duties ad 
                     JOIN users u ON u.id = ad.approved_by 
                     WHERE ad.user_id = $userId 
                     AND ad.project_id = $job_id 
                     AND ad.task_id = $worktype_id 
                     AND ad.activity_id = $activity_id 
                     AND ad.claim_status = 1");

// Check if the query was successful
if ($qry) {
    // Fetch the result as an object
    while ($row = $qry->fetch_object()) {
        // Echo the approver's name
        echo "<p style='color:blue'> Processed By: " . $row->approver . " </p>";
         echo "<p style='color:blue'> Date Processed: " . $row->Date_Processed . " </p>";
    }
} else {
    // Handle the error if the query fails
    echo "Query failed: " . $conn->error;
} ?>
<?php 
} else { 
    // Show options to approve or reject
?>
    <label style="text-decoration: underline;" for="" class="control-label">Process Claim</label>
    <select name="claim_status" class="custom-select custom-select-sm">
        <option value="1">Approve</option>
        <option value="2">Reject</option>
    </select>
<?php 
} 
?>

</select>

            </div>
        </div>
    </div>
</form>

        </div>
        <div class="card-footer border-top border-info">
            <div class="d-flex w-100 justify-content-center align-items-center">
         <?php 
if ($claim_status == 1 || $claim_status == 2) {
    // Do nothing or handle processed claims here if needed
} else { 
?>
    <button class="btn btn-flat bg-gradient-primary mx-2" type="submit" form="manage-claim">Save</button>
<?php 
}
?>

               
                <button class="btn btn-flat bg-gradient-secondary mx-2" type="button"
  onclick="location.href='index.php?page=period_claims&id=<?php echo $job_id ?>&start=<?php echo $start ?>&end=<?php echo $end ?>'">
  Back
</button>

            </div>
        </div>
    </div>
</div>

