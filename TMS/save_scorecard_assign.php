<?php
include 'db_connect.php';

$scorecard_url_secret = 'my_app_secret_key';

function handleError($message, $conn = null, $stmt = null) {
    echo "<p style='color:red; font-size:20px; font-weight:bold'>$message</p>";
    if ($stmt) $stmt->close();
    if ($conn) $conn->close();
    exit;
}

function redirectTo($url) {
    if (!headers_sent()) {
        header("Location: $url", true, 303);
        exit;
    }

    $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    echo "<script>window.location.href=" . json_encode($url) . ";</script>";
    echo "<noscript><meta http-equiv='refresh' content='0;url={$safeUrl}'></noscript>";
    exit;
}

function encodeScorecardIdForUrl(int $scorecardId, string $secret): string
{
    $payload = (string)$scorecardId;
    $hash = hash_hmac('sha256', $payload, $secret);
    return urlencode(base64_encode($payload . ':' . $hash));
}

// Ensure POST variables are set
if (isset($_POST['pm_ids']) && isset($_POST['scorecard_id'])) {
    
    $scorecard_id = intval($_POST['scorecard_id']); 
    $pm_ids = array_map('intval', $_POST['pm_ids']); 

    if ($scorecard_id > 0 && !empty($pm_ids)) {
        $conn->begin_transaction();

        // 1. Prepare TWO statements: one to CHECK, one to INSERT
        $check_stmt = $conn->prepare("SELECT 1 FROM scorecards_project WHERE scorecard_id = ? AND project_manager_id = ?");
        $insert_stmt = $conn->prepare("INSERT INTO scorecards_project (scorecard_id, project_manager_id) VALUES (?, ?)");

        if (!$check_stmt || !$insert_stmt) {
            handleError("Error preparing statements: {$conn->error}", $conn);
        }

        try {
            foreach ($pm_ids as $pm_id) {
                // 2. Check if it already exists
                $check_stmt->bind_param("ii", $scorecard_id, $pm_id);
                $check_stmt->execute();
                $check_stmt->store_result();

                // 3. Only Insert if NO rows were found (num_rows == 0)
                if ($check_stmt->num_rows == 0) {
                    $insert_stmt->bind_param("ii", $scorecard_id, $pm_id);
                    if (!$insert_stmt->execute()) {
                        throw new Exception("Error saving data: {$insert_stmt->error}");
                    }
                }
            }

            $conn->commit();
            
            // Close both statements
            $check_stmt->close();
            $insert_stmt->close();
            
            $encodedScorecardId = encodeScorecardIdForUrl($scorecard_id, $scorecard_url_secret);
            redirectTo("index.php?page=assign_scorecard&id=$encodedScorecardId");

        } catch (Exception $e) {
            $conn->rollback();
            // Close manually if error occurs
            if(isset($check_stmt)) $check_stmt->close();
            if(isset($insert_stmt)) $insert_stmt->close();
            handleError($e->getMessage(), $conn);
        }

    } else {
        handleError("Invalid team or periods selected.", $conn);
    }
} else {
    handleError("Invalid submission: Please select a team and periods.");
}

$conn->close();
?>
