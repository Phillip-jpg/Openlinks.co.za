
<div>
    <select name="industry_id" id="industries" class="custom-select custom-select-sm">
    <?php
$officeId = $_GET['officeId'];
$stmt = $conn->prepare("SELECT DISTINCT REP_ID, REP_NAME FROM client_rep WHERE CLIENT_ID = ? ORDER BY REP_NAME ASC");
$stmt->bind_param("i", $officeId); // Assuming INDUSTRY_ID is an integer, use "i" for integers

if ($stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        // Add each industry as an option to the response
        echo "<option value='" . $row['REP_ID'] . "'>" . $row['REP_NAME'] . "</option>";
    }
}
?>
    </select>
</div>
