<?php
if (!isset($conn)) {
    include 'db_connect.php';
}
?>

<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-body">
            <p>Create Billing Configuration</p>

            <!-- Form -->
            <form id="manage-schedule" action="./index.php?page=save_billing_configuration" method="POST">
                <input type="hidden" name="contract_id" value="<?php echo $_GET['contract_id']; ?>">

                <div class="row">
                    <!-- Applicable Billing -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Applicable Billing</label>
                            <select class="form-control form-control-sm select2" name="applicable_type" id="applicable_type" required>
                                <option value="20">Openlinks Services Fees</option>
                                <option value="21">Production Team Fees</option>
                            </select>
                        </div>
                    </div>

                    <!-- Billing Type -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Billing Type</label>
                            <select class="form-control form-control-sm select2" name="billing_type" id="billing_type" required>
                                <option value="31">Base Rate</option>
                                <option value="32">Pug Rate</option>
                                <option value="33">Percentage Base</option>
                            </select>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" class="form-control form-control-sm" name="description" required>
                        </div>
                    </div>

                    <!-- Cost -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Cost</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="cost" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Target -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Target of Job/Activities</label>
                            <input type="number" class="form-control form-control-sm" name="target" required>
                        </div>
                    </div>

                    <!-- Condition -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Condition</label>
                            <select class="form-control form-control-sm select2" name="condition" id="condition" required>
                                <option value="123">Number of Activities</option>
                                <option value="124">Number of Jobs</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Footer buttons -->
                <div class="card-footer border-top border-info">
                    <div class="d-flex w-100 justify-content-center align-items-center">
                       <button class="btn btn-flat bg-gradient-primary mx-2" type="submit">Save</button>

                        <!-- Back -->
                        <button class="btn btn-flat bg-gradient-secondary mx-2" type="button"
                            onclick="location.href='index.php?page=configure_contract&contract_id=<?php echo $_GET['contract_id']; ?>'">
                            Back
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Billing Configuration Table -->
<div class="col-lg-12">
    <div class="card card-outline card-success shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title">
                Contract Id: <?php echo $_GET['contract_id']; ?><br>
                <?php
                $contract_id = $_GET['contract_id'];
                $contract_name = $conn->query("SELECT name_of_contract FROM contracts WHERE contract_id=$contract_id")->fetch_assoc()['name_of_contract'];
                echo $contract_name;
                ?>
            </h4>
        </div>

        <div class="card-body">
            <table class="table table-hover table-bordered table-condensed" id="list">
                <thead style="background-color:#032033; color:white">
                    <tr>
                        <th>Application of Billable</th>
                        <th>Billing Type</th>
                        <th>Description</th>
                        <th>Cost</th>
                        <th>Target</th>
                        <th>Rate</th>
                        <th>Condition</th>
                        <th>Creation Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $work_qry = $conn->query("
                        SELECT DISTINCT bc.*, c.name_of_contract
                        FROM billing_configuration bc
                        JOIN contracts c ON c.contract_id = bc.contract_id
                        WHERE c.contract_id = $contract_id
                    ");
                    while ($row = $work_qry->fetch_assoc()):
                    ?>
                        <tr>
                            <!-- Application -->
                            <td>
                                <?php
                                if ($row['application'] == 20) {
                                    echo "<p style='font-weight:bold; color:#007bff'>Openlinks Services Fee</p>";
                                } elseif ($row['application'] == 21) {
                                    echo "<p style='font-weight:bold; color:green'>Production Team Fee</p>";
                                }
                                ?>
                            </td>

                            <!-- Billing Type -->
                            <td>
                                <?php
                                if ($row['Billing_Type'] == 31) {
                                    echo "<p style='font-weight:bold; color:green'>Base Rate</p>";
                                } elseif ($row['Billing_Type'] == 32) {
                                    echo "<p style='font-weight:bold; color:green'>Pug Rate</p>";
                                } elseif ($row['Billing_Type'] == 33) {
                                    echo "<p style='font-weight:bold; color:green'>Percentage Rate</p>";
                                }
                                ?>
                            </td>

                            <!-- Description -->
                            <td><p><?php echo ucwords($row['Description']); ?></p></td>

                            <!-- Cost -->
                            <td><p>R <?php echo ucwords($row['Cost']); ?></p></td>

                            <!-- Target -->
                            <td><p><?php echo ucwords($row['Target']); ?></p></td>

                            <!-- Rate -->
                            <td><p>R <?php echo ucwords($row['Rate']); ?></p></td>

                            <!-- Condition -->
                            <td>
                                <?php
                                if ($row['conditions'] == 123) {
                                    echo "<p style='font-weight:bold; color:green'>Number of Activities</p>";
                                } else {
                                    echo "<p style='font-weight:bold; color:green'>Number of Jobs</p>";
                                }
                                ?>
                            </td>

                            <!-- Creation Date -->
                            <td><p><?php echo ucwords($row['date_created']); ?></p></td>

                            <!-- Action Dropdown -->
                            <td class="text-center">
                                <button type="button" class="btn btn-default btn-sm btn-flat border-info dropdown-toggle" data-toggle="dropdown">
                                    Action
                                </button>
                                <div class="dropdown-menu">
                                    <!-- View -->
                                    <a class="dropdown-item" href="./index.php?page=configure_contract&contract_id=<?php echo $row['contract_id']; ?>">
                                        View
                                    </a>
                                    <!-- Edit -->
                                    <a class="dropdown-item" href="./index.php?page=edit_billing&edit_id=<?php echo $row['id']; ?>&contract_id=<?php echo $row['contract_id']; ?>">
                                        Edit
                                    </a>
                                    <!-- Delete -->
                                    <a class="dropdown-item" href="./index.php?page=delete_bill&delete_id=<?php echo $row['id']; ?>&contract_id=<?php echo $row['contract_id']; ?>" onclick="return confirm('Are you sure you want to delete this billing configuration? This action cannot be undone.');">
                                        Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
    .table-responsive { overflow-x: auto; }
    table p { margin: 0 !important; }
    table td, table th { vertical-align: middle !important; }
    .card-header { background-color: #007bff; color: white; }
    .table-hover tbody tr:hover { background-color: #f9f9f9; }
    .btn-default { background-color: white; border-color: #ddd; }
</style>

<!-- JS -->
<script>
document.getElementById('manage-schedule').addEventListener('submit', function () {
    const btn = this.querySelector('button[type="submit"]');
    if (this.checkValidity()) {
        btn.disabled = true;
        btn.innerText = 'Saving...';
    }
});
</script>
