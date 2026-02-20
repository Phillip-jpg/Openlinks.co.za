<?php

include 'db_connect.php';

$numberofactivities = 3; // Define this properly

$qry1 = $conn->query("
    SELECT *
    FROM contracts c
    JOIN billing_configuration bc ON bc.contract_id = c.contract_id
    WHERE c.team_id = 3088 AND c.work_type_billing = 8
");

if ($qry1) {
    echo "Processing matched contracts:<br>";


        $totalopenlinks_serivce = 0;
        $openlinks_updated1 = 0;
        $openlinks_updated2 = 0;
        
        $total_production_team = 0;
        $production_updated1 = 0;
        $production_updated2 = 0;

$numberofactivities = 3; // Set properly

while ($row = $qry1->fetch_assoc()) {
    if ($row['application'] == 20 && $row['conditions'] == 124) {
        $openlinks_services1 = ($row['Rate'] / $numberofactivities)*10;
        echo $openlinks_services1.'<br>';
        $openlinks_updated1 += $openlinks_services1;
    }

    if ($row['application'] == 20 && $row['conditions'] == 123) {
        $openlinks_services2 = $row['Rate']*10;
        echo $openlinks_services2.'<br>';
        $openlinks_updated2 += $openlinks_services2;
    }

    $totalopenlinks_serivce = $openlinks_updated1 + $openlinks_updated2;
    
    
    if ($row['application'] == 21 && $row['conditions'] == 124) {
        $production_team1 = $row['Rate'] / $numberofactivities;
        $production_updated1 += $production_team1;
    }

    if ($row['application'] == 21 && $row['conditions'] == 123) {
        $production_team2 = $row['Rate'];
        $production_updated2 += $production_team2;
    }

    $total_production_team = $production_updated1 + $production_updated2;
}

echo "Total OpenLinks Service Fee: " . number_format($totalopenlinks_serivce, 2);
echo "<br>";
echo "Total Production Team Fee: " . number_format($total_production_team, 2);


} else {
    echo "<p style='color:red;'>Query failed: " . $conn->error . "</p>";
}

?>
