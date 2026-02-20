<?php
?>
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <form id="manage-account" method="post" action="./index.php?page=save_assigned_client">

                <div class="row">
                    <!-- Accounting Officer Selection -->
                    <div class="col-md-6 border-right">
                        <div class="form-group">
                            <label for="manager_id" class="control-label">Accounting Officer</label>
                            <select id="manager_id" class="form-control form-control-sm select2" name="manager_id" required>
                                <option value="0"></option>
                                <?php 
                                
                                if($_SESSION['login_type']==2){
                                    $managers = $conn->query("SELECT *, concat(firstname,' ',lastname) as name FROM users where creator_id={$_SESSION['login_id']} OR id={$_SESSION['login_id']}");
                                }else{
                                    $managers = $conn->query("SELECT *, concat(firstname,' ',lastname) as name FROM users");
                                }
                               
                                while($row = $managers->fetch_assoc()):
                                ?>
                                <option value="<?php echo htmlspecialchars($row['id']); ?>" <?php echo isset($manager_id) && $manager_id == $row['id'] ? "selected" : ''; ?>>
                                    <?php echo ucwords(htmlspecialchars($row['name'])); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Client Selection -->
                    <div class="col-md-6 border-right">
                        <div class="form-group">
                            <label for="user_ids" class="control-label">Client</label>
                            <select id="user_ids" class="form-control form-control-sm select2" multiple="multiple" name="client_ids[]" required>
                                <?php 
                                   if($_SESSION['login_type']==2){
                                       $client = $conn->query("SELECT * FROM yasccoza_openlink_market.client where creator_id={$_SESSION['login_id']}");
                                   }else{
                                        $client = $conn->query("SELECT * FROM yasccoza_openlink_market.client");
                                   }
                                
                                while($row = $client->fetch_assoc()):
                                ?>
                                <option value="<?php echo htmlspecialchars($row['CLIENT_ID']); ?>" <?php echo isset($CLIENT_ID) && in_array($row['CLIENT_ID'], explode(',', $CLIENT_ID)) ? "selected" : ''; ?>>
                                    <?php echo ucwords(htmlspecialchars($row['company_name'])); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <hr>

            </form>
            <div class="col-lg-12 text-right justify-content-center d-flex">
                <button class="btn btn-primary mr-2" type="submit" form="manage-account">Save</button>
                <button class="btn btn-secondary" type="button" onclick="location.href = 'index.php?page=client_offices'">Back</button>
            </div>
        </div>
    </div>
</div>

<!-- Additional Styling -->
<style>
    img#cimg {
        height: 15vh;
        width: 15vh;
        object-fit: cover;
        border-radius: 100%;
    }
</style>
