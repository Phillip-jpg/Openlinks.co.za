
<div>
    <select name="industry_idz" id="industriz" class="custom-select custom-select-sm">
     <?php
$officeId = $_GET['officeId'];
$stmt = $conn->prepare("SELECT TITLE_ID, title FROM yasccoza_openlink_association_db.industry_title WHERE INDUSTRY_ID = ?");
$stmt->bind_param("i", $officeId); // Assuming INDUSTRY_ID is an integer, use "i" for integers

if ($stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        // Add each industry as an option to the response
        echo "<option value='" . $row['TITLE_ID'] . "'>" . $row['title'] . "</option>";
    }
}
?>
    </select>
</div>

