<style>
.btn-primary-modern {
    background: linear-gradient(135deg, #007bff, #00c6ff);
    color: white !important;
    border: none;
    border-radius: 12px;
    padding: 8px 18px;
    font-weight: 500;
    box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
    transition: all 0.25s ease-in-out;
    text-decoration: none;
}

.btn-primary-modern:hover {
    background: linear-gradient(135deg, #0062cc, #00a3e0);
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 123, 255, 0.4);
}

.btn-primary-modern:active {
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
}
</style>

<div class="container mt-5">
    <div class="card-tools">
				<a class="btn btn-primary-modern" href="./index.php?page=assign_to_client">
                  Assign Officer
                </a>
			</div>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="POST" action="index.php?page=client_officer_list" class="card shadow-sm p-4 border-primary" style="border-radius: 10px;">
                <h4 class="text-center text-primary mb-4">Select Client</h4>

                <?php
                include('db_connect.php');
                
                if($_SESSION['login_type'] == 2){
                    
                     $clients = $conn->query("SELECT * FROM yasccoza_openlink_market.client where creator_id={$_SESSION['login_id']}");
                     
                }elseif($_SESSION['login_type'] == 3){
                    
                }else{
                     $clients = $conn->query("SELECT * FROM yasccoza_openlink_market.client");
                }
                

                if ($clients && $clients->num_rows > 0): ?>
                    <div class="form-group">
                        <select name="client_id" class="form-control">
                            <?php while ($row = $clients->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($row['CLIENT_ID']) ?>" 
                                <?php echo ($selected_company_name == $row['company_name']) ? "selected" : ''; ?>>
                                    <?php echo ucwords(htmlspecialchars($row['company_name'])) . ' (' . htmlspecialchars($row['CLIENT_ID']) . ')'; 
                                    ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <p class="text-danger text-center">No clients available.</p>
                <?php endif; ?>

                <div class="d-flex justify-content-center mt-4">
                    <button class="btn btn-info btn-lg" id="view-link-claims" type="submit" style="width: 100%;">
                        View
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
