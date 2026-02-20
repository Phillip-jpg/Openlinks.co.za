<?php
include('db_connect.php');

if (isset($_SESSION['login_id']) && is_numeric($_SESSION['login_id'])) {
    $login_id = $_SESSION['login_id'];

    // Calculate the start date of the current week (Monday
}
?>




<?php
       $weekAssignments = [];
          // Query for projects within the current week
          $query = "SELECT name,scorecard,status, start_date, end_date, manager_id, user_ids, task_ids, COUNT(*) as row_count
                    FROM project_list
                    WHERE date_created >= startOfWeek AND date_created <= endOfWeek
                    AND manager_id IS NOT NULL
                    GROUP BY startOfWeek, endOfWeek;
                    ";
              $result = $conn->query($query);
                            // Check if there are any projects found
              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                                    // Store project names in the array for the current week
             $weekAssignments[] = $row['row_count'];
              }
      } 
      ?>

<?php
       $weekDue = [];
          // Query for projects within the current week
          $query = "SELECT name,scorecard,status, start_date, end_date, manager_id, user_ids, task_ids, COUNT(*) as row_count
                    FROM project_list
                    WHERE date_created >= startOfWeek AND date_created <= endOfWeek
                    AND DATE(end_date) = CURDATE()
                    GROUP BY startOfWeek, endOfWeek;
                    ";
              $result = $conn->query($query);
                            // Check if there are any projects found
              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                                    // Store project names in the array for the current week
                $weekDue[] = $row['row_count'];
              }
      } 
      ?>


<?php
        $weekDone = [];
          // Query for projects within the current week
          $query = "SELECT name,scorecard,status, start_date, end_date, manager_id, user_ids, task_ids, COUNT(*) as row_count
                    FROM project_list
                    WHERE date_created >= startOfWeek AND date_created <= endOfWeek
                    AND Status = 'Done'
                    GROUP BY startOfWeek, endOfWeek;
                    ";
              $result = $conn->query($query);
                            // Check if there are any projects found
              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                                    // Store project names in the array for the current week
                $weekDone[] = $row['row_count'];
              }
      } 
      ?>

<?php
        $weekonHold = [];
          // Query for projects within the current week
          $query = "SELECT name,scorecard,status, start_date, end_date, manager_id, user_ids, task_ids,  COUNT(*) as row_count
                    FROM project_list
                    WHERE date_created >= startOfWeek AND date_created <= endOfWeek
                    AND Status = 'On-Hold'
                    GROUP BY startOfWeek, endOfWeek;
                    ";
              $result = $conn->query($query);
                            // Check if there are any projects found
              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                                    // Store project names in the array for the current week
                  $weekonHold[] = $row['row_count'];
                  
              }
      } 
      ?>

<?php
        $weekDropped = [];
          // Query for projects within the current week
          $query = "SELECT name,scorecard,status, start_date, end_date, manager_id, user_ids, task_ids, startOfWeek, COUNT(*) as row_count
                    FROM project_list
                    WHERE date_created >= startOfWeek AND date_created <= endOfWeek
                    AND Status = 'Dropped'
                    GROUP BY startOfWeek, endOfWeek;
                    ";
              $result = $conn->query($query);
                            // Check if there are any projects found
              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                                    // Store project names in the array for the current week
                $weekDropped[] = $row['row_count'];
              }
      } 
      ?>


<?php
$weekProjects = [];
$weekrange = []; // Initialize the weekrange array
// Query for projects within the current week
$query = "SELECT name,scorecard,status,start_date,end_date,manager_id,user_ids,task_ids, CONCAT(startOfWeek, ' - ', endOfWeek) AS week_range, COUNT(*) as row_count FROM project_list WHERE date_created >= startOfWeek AND date_created <= endOfWeek GROUP BY startOfWeek, endOfWeek;";
$result = $conn->query($query);
// Check if there are any projects found
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Store project data in the arrays
        $weekProjects[] = $row['row_count'];
        $weekrange[] = $row['week_range'];
    }
}
?>
<div style="display: flex;">
    <table style="display: inline-block;">
        <tbody>
            <?php
            $week = 0; // Initialize week outside the loop
            foreach ($weekProjects as $projectcount):
                ?>
                <tr>
                    <td>
                        <h3 style="text-decoration:dotted">Period <?php echo $week + 1; ?></h3>
                        <div class="small-box bg-light shadow-sm border">
                            <div class="inner">
                                <br>
                                <h3><?php echo $projectcount; ?></h3>
                                <p>Nm Started Jobs</p>
                                <h3><?php echo $weekrange[$week]; ?></h3> <!-- Display the week range here -->
                            </div>
                            <div class="icon">
                                <i class="fa fa-layer-group"></i>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php
                $week++; // Increment week within the loop
            endforeach;
            ?>
        </tbody>
    </table>




    <table style="display: inline-block;">
        <tbody>
            <?php
            $week = 1; // Initialize week outside the loop
            foreach ($weekAssignments as $assign): ?>
                <tr>
                    <td >
                        <h3 style="color:transparent">Period <?php echo $week; ?></h3>
                        <div class="small-box bg-light shadow-sm border">
                            <div class="inner">
                                <br>
                                <h3><?php echo $assign; ?></h3>
                                <p>Jobs Allocated</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-layer-group"></i>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php $week++; // Increment week within the loop
            endforeach; ?>
        </tbody>
    </table>

    <table style="display: inline-block;">
        <tbody>
            <?php
            $week = 1; // Initialize week outside the loop
            foreach ($weekDue as $assign): ?>
                <tr>
                    <td style="height: 100px;">
                        <h3 style="color:transparent">Period <?php echo $week; ?></h3>
                        <div class="small-box bg-light shadow-sm border">
                            <div class="inner">
                                <br>
                                <h3><?php echo $assign; ?></h3>
                                <p>Jobs Due Today</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-layer-group"></i>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php $week++; // Increment week within the loop
            endforeach; ?>
        </tbody>
    </table>

    <table style="display: inline-block;">
        <tbody>
            <?php
            $week = 1; // Initialize week outside the loop
            foreach ($weekDone as $assign): ?>
                <tr>
                    <td style="height: 100px;">
                        <h3 style="color:transparent">Period <?php echo $week; ?></h3>
                        <div class="small-box bg-light shadow-sm border">
                            <div class="inner">
                                <br>
                                <h3><?php echo $assign; ?></h3>
                                <p>Jobs Compeleted </p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-layer-group"></i>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php $week++; // Increment week within the loop
            endforeach; ?>
        </tbody>
    </table>

    <table style="display: inline-block;">
        <tbody>
            <?php
            $week = 1; // Initialize week outside the loop
            foreach ($weekonHold as $assign): ?>
                <tr>
                    <td style="height: 100px;">
                        <h3 style="color:transparent">Period <?php echo $week; ?></h3>
                        <div class="small-box bg-light shadow-sm border">
                            <div class="inner">
                                <br>
                                <h3><?php echo $assign; ?></h3>
                                <p>Jobs Put on-hold </p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-layer-group"></i>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php $week++; // Increment week within the loop
            endforeach; ?>
        </tbody>
    </table>

    <table style="display: inline-block;">
        <tbody>
            <?php
            $week = 1; // Initialize week outside the loop
            foreach ($weekDropped as $assign): ?>
                <tr>
                    <td style="height: 100px;">
                        <h3 style="color:transparent">Period <?php echo $week; ?></h3>
                        <div class="small-box bg-light shadow-sm border">
                            <div class="inner">
                                <br>
                                <h3><?php echo $assign; ?></h3>
                                <p>Jobs Dropped </p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-layer-group"></i>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php $week++; // Increment week within the loop
            endforeach; ?>
        </tbody>
    </table>
    
</div>








 
             

        