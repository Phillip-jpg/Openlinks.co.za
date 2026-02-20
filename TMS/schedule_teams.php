<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Monthly Period Calendar</title>
    <style>
        /* General container styling */
        .container {
            margin: 20px auto;
            max-width: 1200px;
        }

        /* Row styling */
        .row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        /* Month block styling */
        .month-block {
            width: 22%; /* Adjust size to fit 4 months per row */
            margin: 15px 0;
            background-color: #f4f4f4;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            padding: 20px;
            text-align: center;
        }

        .month-header {
            background-color: #172D44;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 18px;
        }

        /* Period block styling */
        .period-block {
            display: inline-block;
            width: 22%; /* Adjust size for smaller blocks */
            margin: 5px;
            background-color: #67b7d1;
            color: white;
            border-radius: 5px;
            padding: 10px;
            font-size: 12px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .period-block:hover {
            background-color: #0dc0ff;
            cursor: pointer;
        }
    </style>
</head>
<body>
<a class="btn btn-sm btn-default btn-flat border-primary mx-1" href="./index.php?page=work_resource_schedule" style="background-color:blue">
                    Work Resource Schedule
                </a>
<?php
$months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

// Define the number of weeks for each month
$weeks_per_month = [4, 4, 4, 4, 5, 4, 5, 5, 4, 5, 4, 5];

$period = 1; // Start with Period 1


$display = '<div class="container">';



for ($i = 0; $i < 12; $i++) {
    if ($i % 4 == 0) {
        $display .= '<div class="row">'; // Start a new row for every 4 months
    }

    $display .= '<div class="month-block">
                    <div class="month-header">' . $months[$i] . '</div>
                    <div class="periods">';
    
    for ($j = 0; $j < $weeks_per_month[$i]; $j++) {
        if ($period <= 52) { // Ensure we don't exceed 52 periods
       $display .= '<div class="period-block"><a href="./index.php?page=schedule_teams_lvl2&period=' . $period . '" style="color:white">Period ' . $period . '</a></div>';
            $period++;
        }
    }

    $display .= '</div></div>';

    if ($i % 4 == 3 || $i == 11) {
        $display .= '</div>'; // Close the row after every 4 months or at the last month
    }
}

$display .= '</div>';

echo $display;
?>

</body>
</html>
