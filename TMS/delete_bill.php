<?php
include 'db_connect.php';

// Validate required GET params
if (isset($_GET['delete_id'], $_GET['contract_id'])) {
    $delete_id = $_GET['delete_id'];
    $contract_id = $_GET['contract_id']; // Safe for URL
    

    if ($delete_id > 0) {
        $stmt = $conn->prepare("DELETE FROM billing_configuration WHERE id = ?");
        
        if ($stmt) {
            $stmt->bind_param("i", $delete_id);

            if (!$stmt->execute()) {
                echo "<p style='color:red;'>DELETE Error (ID: $delete_id): {$stmt->error}</p>";
                exit;
            }

            $stmt->close();
        } else {
            echo "<p style='color:red;'>Failed to prepare DELETE query.</p>";
            exit;
        }

        // Redirect after successful deletion
        header("Location: index.php?page=billing_configuration&contract_id=$contract_id");
        exit;
    } else {
        echo "<p style='color:red;'>Invalid contract ID.</p>";
    }
} else {
    echo "<p style='color:red;'>Missing required parameters.</p>";
}
?>
