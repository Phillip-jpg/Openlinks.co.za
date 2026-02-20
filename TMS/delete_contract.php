<?php
include 'db_connect.php';

// Validate required GET params
if (isset($_GET['contract_id'])) {
    $contract_id = $_GET['contract_id']; // Safe for URL
    


        $stmt = $conn->prepare("DELETE FROM contracts WHERE contract_id = ?");
        
        if ($stmt) {
            $stmt->bind_param("i", $contract_id);

            if (!$stmt->execute()) {
                echo "<p style='color:red;'>DELETE Error (ID: $contract_id): {$stmt->error}</p>";
                exit;
            }

            $stmt->close();
        } else {
            echo "<p style='color:red;'>Failed to prepare DELETE query.</p>";
            exit;
        }

        // Redirect after successful deletion
        header("Location: index.php?page=contracts");
        exit;
    
} else {
    echo "<p style='color:red;'>Missing required parameters.</p>";
}
?>
