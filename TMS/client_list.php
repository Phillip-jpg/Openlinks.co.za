<?php include 'db_connect.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<div class="col-lg-12">
   	<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white">
            <div class="card-tools d-flex justify-content-start">
            <?php if ($_SESSION['login_type'] != 3 ): ?>
                <a class="btn btn-sm btn-default btn-flat border-primary mx-1" href="./index.php?page=new_client">
                    <i class="fa fa-plus"></i> Add New Client
                </a>
                <a class="btn btn-sm btn-default btn-flat border-primary mx-1" href="./index.php?page=new_client">
                    <i class="fa fa-envelope"></i> Generate Invite
                </a>
            <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
    	<table class="table table-hover table-bordered table-condensed" id="list">
                <thead style="background-color:#032033 !important; color:white">
                    <tr>
                        <th class="text-center">#</th>
                        <th>Client_ID</th>
                        <th>Company Name</th>
                        <?php if ($_SESSION['login_type'] == 3 || $_SESSION['login_type'] == 1): ?>
                        <th>Entity</th>
                        <?php endif; ?>
                        <th>Sector</th>
                        <th>Company Rep</th>
                        <th>City</th>
                        <th>Contact</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                 <?php
                    $i = 1;
                    $type = ['', "Admin", "Project Manager", "Employee"];
                    
                    if ($_SESSION['login_type'] == 2) {
                    
                        // Project Manager: Fetch only clients they created
                        $qry = $conn->query("
                            SELECT
                                client.*,
                                CONCAT(pm.firstname, ' ', pm.lastname) AS entity_name,
                                title,
                                office,
                                GROUP_CONCAT(DISTINCT CONCAT('(', client_rep.REP_NAME, ')') ORDER BY client_rep.REP_NAME ASC) AS reps
                            FROM yasccoza_openlink_market.client
                            LEFT JOIN users pm
                                ON pm.id = yasccoza_openlink_market.client.creator_id
                            LEFT JOIN yasccoza_openlink_association_db.industry_title
                                ON client.industry_id = industry_title.TITLE_ID
                            LEFT JOIN yasccoza_openlink_association_db.industry
                                ON client.office_id = industry.INDUSTRY_ID
                            LEFT JOIN client_rep
                                ON client_rep.CLIENT_ID = yasccoza_openlink_market.client.CLIENT_ID
                            WHERE yasccoza_openlink_market.client.creator_id = {$_SESSION['login_id']}
                            GROUP BY client.CLIENT_ID, title, office
                        ");
                    
                    } elseif ($_SESSION['login_type'] == 3) {
                    
                        // Employee: Define their access scope if needed
                        $qry = $conn->query("
                                     SELECT
                                client.*,
                                CONCAT(pm.firstname, ' ', pm.lastname) AS entity_name,
                                title,
                                office,
                                GROUP_CONCAT(DISTINCT CONCAT('(', client_rep.REP_NAME, ')') ORDER BY client_rep.REP_NAME ASC) AS reps
                            FROM yasccoza_openlink_market.client
                            LEFT JOIN users pm
                                ON pm.id = yasccoza_openlink_market.client.creator_id
                            LEFT JOIN yasccoza_openlink_association_db.industry_title
                                ON client.industry_id = industry_title.TITLE_ID
                            LEFT JOIN yasccoza_openlink_association_db.industry
                                ON client.office_id = industry.INDUSTRY_ID
                            LEFT JOIN client_rep
                                ON client_rep.CLIENT_ID = yasccoza_openlink_market.client.CLIENT_ID
                             LEFT JOIN users
                                ON users.creator_id = yasccoza_openlink_market.client.creator_id
                            WHERE users.id ={$_SESSION['login_id']}
                            GROUP BY client.CLIENT_ID, title, office;
                        ");
                    
                    } else {
                    
                        // Admin: Fetch all clients
                        $qry = $conn->query("
                            SELECT
                                client.*,
                                CONCAT(pm.firstname, ' ', pm.lastname) AS entity_name,
                                title,
                                office,
                                GROUP_CONCAT(DISTINCT CONCAT('(', client_rep.REP_NAME, ')') ORDER BY client_rep.REP_NAME ASC) AS reps
                            FROM yasccoza_openlink_market.client
                            LEFT JOIN users pm
                                ON pm.id = yasccoza_openlink_market.client.creator_id
                            LEFT JOIN yasccoza_openlink_association_db.industry_title
                                ON client.industry_id = industry_title.TITLE_ID
                            LEFT JOIN yasccoza_openlink_association_db.industry
                                ON client.office_id = industry.INDUSTRY_ID
                            LEFT JOIN client_rep
                                ON client_rep.CLIENT_ID = yasccoza_openlink_market.client.CLIENT_ID
                            GROUP BY client.CLIENT_ID, title, office
                        ");
                    }
                    
                    while ($row = $qry->fetch_assoc()):
                    ?>

                    <tr>
                        <th class="text-center"><?php echo $i++ ?></th>
                        <td style="font-weight: lighter;"><b><?php echo $row['CLIENT_ID'] ?></b></td>
                        <td style="font-weight: lighter;"><b><?php echo $row['company_name'] ?></b></td>
                        <?php if($_SESSION['login_type'] == 3 || $_SESSION['login_type'] == 1): ?>
                        <td style="font-weight: lighter;"><b><?php echo $row['entity_name'] ?: 'N/A' ?></b></td>
                        <?php endif; ?>
                       
                        <td>Industry: <b><?php echo $row['title']; ?></b><br>Office: <b style="color:#007BFF"><?php echo $row['office']; ?></b></td>
                        <td style="font-weight: lighter;"><b><?php echo $row['reps'] ?></b></td>
                        <td style="font-weight: lighter;"><b><?php echo $row['city'] ?></b></td>
                        <td style="font-weight: lighter;"><b><?php echo $row['Contact'] ?></b></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                    Action
                                </button>
                                <div class="dropdown-menu">
                                     <?php if($_SESSION['login_type'] == 4 || $_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 3): ?>
                                    <a class="dropdown-item view_client" href="javascript:void(0)" data-id="<?php echo $row['CLIENT_ID'] ?>">View more info</a>
                                    <?php else: ?>
                                    <a class="dropdown-item view_client" href="javascript:void(0)" data-id="<?php echo $row['CLIENT_ID'] ?>">View more info</a>
                                    <div class="dropdown-divider"></div>
                                    	<?php
                                    $secret = 'my_app_secret_key'; // put in config file ideally
                                    $payload = $row['CLIENT_ID'];
                                    $hash = hash_hmac('sha256', $payload, $secret);
                                    $encoded = base64_encode($payload . ':' . $hash);
                                    ?>
                                    <a class="dropdown-item" href="./index.php?page=edit_client&id=<?php echo urlencode($encoded); ?>">Edit</a>
                                    <div class="dropdown-divider"></div>
                                    <!-- <a class="dropdown-item delete_client" href="javascript:void(0)" data-id="<?php echo $row['CLIENT_ID'] ?>">Delete</a> -->
                                         <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
  $('#list').dataTable();

  $('.view_client').click(function(){
    uni_modal("<i class='fa fa-id-card'></i> Client Details","view_client.php?id="+$(this).attr('data-id'));
  });

  $('.delete_client').click(function(){
    _conf("Are you sure to delete this client?","delete_client",[$(this).attr('data-id')]);
  });
});

function delete_client($id){
  start_load();
  $.ajax({
    url: 'ajax.php?action=delete_client',
    method: 'POST',
    data: {
      id: $id,
      csrf_token: '<?php echo htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES); ?>'
    },
    success: function(resp){
      resp = String(resp).trim();
      if(resp === '1'){
        alert_toast("Data successfully deleted",'success');
        setTimeout(function(){ location.reload(); }, 1500);
      } else if(resp === 'csrf'){
        alert_toast("Session expired. Refresh and try again.",'warning');
      } else if(resp === 'unauthorized'){
        alert_toast("Unauthorized.",'warning');
      } else {
        alert_toast("Delete failed: " + resp,'danger');
      }
      end_load();
    },
    error: function(xhr){
      console.log(xhr.status, xhr.responseText);
      alert_toast("Request failed",'danger');
      end_load();
    }
  });
}
</script>
