<?php
include('db_connect.php');
session_start();

if (isset($_SESSION['login_id']) && is_numeric($_SESSION['login_id'])) {
    $login_id = $_SESSION['login_id'];

    // Calculate the start date of the current week (Monday)
    $current_date = date('Y-m-d');
    $day_of_week = date('N', strtotime($current_date));
    $monday_date = date('Y-m-d', strtotime($current_date . ' -' . ($day_of_week - 1) . ' days'));

    echo "<p>Start of the current week (Monday): $monday_date</p>";
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="POST" action="ajax.php?action=print_resources_report" class="card shadow-sm p-4 border-primary" style="border-radius: 10px;">
                <h4 class="text-center text-primary mb-4">Select Period to Process Claims</h4>
                
                <div class="form-group">
                    <label for="start_week_claims" class="font-weight-bold">Start Date:</label>
                    <input type="date" id="start_week_claims" name="start_week_claims" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="end_week_claims" class="font-weight-bold">End Date:</label>
                    <input type="date" id="end_week_claims" name="end_week_claims" class="form-control" required>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    <button class="btn btn-info btn-lg" id="view-link-claims" type="button" style="width: 100%;">
                        View
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('view-link-claims').addEventListener('click', function(event) {
        var startWeek = document.getElementById('start_week_claims').value;
        var endWeek = document.getElementById('end_week_claims').value;
        if (startWeek && endWeek) {
            var href = './index.php?page=claims&start=' + startWeek + '&end=' + endWeek;
            window.location.href = href;
        } else {
            event.preventDefault();
            alert('Please select both start and end dates.');
        }
    });
</script>

<!-- Bootstrap and CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f7f7f7;
    }
    .container {
        margin-top: 50px;
    }
    .card {
        border: 2px solid #337ab7;
        border-radius: 10px;
        background-color: #fff;
    }
    .card h4 {
        font-weight: bold;
        color: #337ab7;
    }
    .form-control {
        border-radius: 5px;
    }
    .btn-info {
        background-color: #337ab7;
        border-color: #337ab7;
        border-radius: 5px;
    }
    .btn-info:hover {
        background-color: #285e8e;
        border-color: #285e8e;
    }
</style>
