<?php

// Sanitize the CLIENT_ID to prevent SQL injection
$CLIENT_ID = isset($_GET['CLIENT_ID']) ? intval($_GET['CLIENT_ID']) : 0;
$WORKTYPE_ID = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;

// Check if CLIENT_ID is valid
if ($CLIENT_ID > 0) {
    // Query the database for the company name
    $Client = $conn->query("SELECT c.company_name, tl.task_name FROM yasccoza_openlink_market.client c, task_list tl WHERE c.CLIENT_ID = $CLIENT_ID AND tl.id=$WORKTYPE_ID");

    // Check if the query returned any results
    if ($Client && $Client->num_rows > 0) {
        // Fetch the result
        $row = $Client->fetch_assoc();
        $company_name = htmlspecialchars($row['company_name']);
        $worktype_name = htmlspecialchars($row['task_name']); // Sanitize for output
    } else {
        $company_name = "Unknown";
        $worktype_name = "Unknown";
    }
} else {
    $company_name = "Invalid CLIENT_ID";
    $worktype_name = "Unknown";
}
?>

<div class="container mt-5">
    <div class="card shadow-lg p-4">
        <h4 class="text-center text-primary">Client: <?php echo $company_name; ?></h4>
        <h6 class="text-center text-secondary">WorkType: <?php echo $worktype_name; ?></h6>
        <div class="card-body">
            <form id="manage-account" method="post" action="./index.php?page=save_link_2">
                <div class="row">
                    <!-- Link Input -->
                    <div class="col-md-6 border-right">
                        <div class="form-group">
                            <label class="control-label">Link</label>
                            <textarea class="form-control" name="LINK" placeholder="Enter the link here" rows="3"></textarea>
                        </div>
                        <input type="hidden" name="CLIENT_ID" value="<?php echo $CLIENT_ID; ?>">
                         <input type="hidden" name="WORKTYPE_ID" value="<?php echo $WORKTYPE_ID; ?>">
                    </div>
                    <div class="col-md-6 border-left">
                        <div class="form-group">
                            <label class="control-label">Name</label>
                            <input type="text" name="link_name" class="form-control form-control-sm" placeholder="Enter the link name">
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button class="btn btn-primary mr-2" type="submit" form="manage-account">Save</button>
                    <button class="btn btn-secondary" type="button" onclick="location.href = 'index.php?page=client_management_lvl_2&CLIENT_ID=<?php echo $CLIENT_ID; ?>'">Back</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Additional Styling -->
<style>
    .container {
        max-width: 800px;
    }
    .card {
        border-radius: 12px;
    }
    .card h4, .card h6 {
        margin: 0 0 10px;
    }
    .btn {
        border-radius: 50px;
        padding: 10px 20px;
        font-size: 16px;
    }
    .form-group label {
        font-weight: bold;
        color: #6c757d;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid #ddd;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }
    .text-center {
        margin-top: 20px;
    }
</style>
