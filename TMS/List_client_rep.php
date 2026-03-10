<?php include 'db_connect.php';


 if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    include'db_connect.php';

 ?>
<div class="col-lg-12">
  	<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white">
          <?php if($_SESSION['login_type'] == 2): ?>
            <div class="card-tools">
                <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_client_rep"><i class="fa fa-plus"></i> Add Client Rep</a>
            </div>
            <?php endif; ?>
        </div>
        <div class="card-body">
         	<table class="table table-hover table-bordered table-condensed" id="list">
               	<thead style="background-color:#032033 !important; color:white">
                    <tr>
                        <th class="text-center">#</th>
                        <th>REP_ID</th>
                        <th>REP_NAME</th>
                        <th>Representing Company</th>
                        <th>Orbit Status</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $loginType = (int)($_SESSION['login_type'] ?? 0);
                    $loginId = (int)($_SESSION['login_id'] ?? 0);
              
                    if ($loginType == 2) {
                    
                    $qry = $conn->query("SELECT
                            cr.REP_ID,
                            MAX(cr.REP_NAME) AS REP_NAME,
                            GROUP_CONCAT(DISTINCT c.company_name ORDER BY c.company_name ASC SEPARATOR ', ') AS company_name,
                            CASE
                                WHEN MAX(CASE WHEN COALESCE(c.orbiter_id, 0) > 0 THEN 1 ELSE 0 END) = 1 THEN 'Orbited'
                                ELSE 'Not Orbited'
                            END AS orbit_status,
                            MAX(cr.ROLE) AS ROLE,
                            MAX(cr.REP_EMAIL) AS REP_EMAIL,
                            MAX(cr.REP_CONTACT) AS REP_CONTACT
                        FROM client_rep cr
                        INNER JOIN yasccoza_openlink_market.client c
                            ON cr.CLIENT_ID = c.CLIENT_ID
                        WHERE c.creator_id = {$loginId}
                        GROUP BY cr.REP_ID
                        ORDER BY cr.REP_ID DESC");
                    }
                    elseif($loginType == 3) {
                        
                    $qry = $conn->query("SELECT
                            cr.REP_ID,
                            MAX(cr.REP_NAME) AS REP_NAME,
                            GROUP_CONCAT(DISTINCT c.company_name ORDER BY c.company_name ASC SEPARATOR ', ') AS company_name,
                            CASE
                                WHEN MAX(CASE WHEN COALESCE(c.orbiter_id, 0) > 0 THEN 1 ELSE 0 END) = 1 THEN 'Orbited'
                                ELSE 'Not Orbited'
                            END AS orbit_status,
                            MAX(cr.ROLE) AS ROLE,
                            MAX(cr.REP_EMAIL) AS REP_EMAIL,
                            MAX(cr.REP_CONTACT) AS REP_CONTACT
                        FROM client_rep cr
                        INNER JOIN yasccoza_openlink_market.client c
                            ON cr.CLIENT_ID = c.CLIENT_ID
                        WHERE EXISTS (
                            SELECT 1
                            FROM users member_access
                            WHERE member_access.id = {$loginId}
                              AND member_access.creator_id = c.creator_id
                        )
                        GROUP BY cr.REP_ID
                        ORDER BY cr.REP_ID DESC");
                        
                    }else{
                        
                         $qry = $conn->query("SELECT
                            cr.REP_ID,
                            MAX(cr.REP_NAME) AS REP_NAME,
                            GROUP_CONCAT(DISTINCT c.company_name ORDER BY c.company_name ASC SEPARATOR ', ') AS company_name,
                            CASE
                                WHEN MAX(CASE WHEN COALESCE(c.orbiter_id, 0) > 0 THEN 1 ELSE 0 END) = 1 THEN 'Orbited'
                                ELSE 'Not Orbited'
                            END AS orbit_status,
                            MAX(cr.ROLE) AS ROLE,
                            MAX(cr.REP_EMAIL) AS REP_EMAIL,
                            MAX(cr.REP_CONTACT) AS REP_CONTACT
                        FROM client_rep cr
                        LEFT JOIN yasccoza_openlink_market.client c
                            ON cr.CLIENT_ID = c.CLIENT_ID
                        GROUP BY cr.REP_ID
                        ORDER BY cr.REP_ID DESC");
                        
                    }
                    while ($row = $qry->fetch_assoc()):
                    ?>
                    
                    <tr>
                        <th class="text-center"><?php echo $i++ ?></th>
                        <td style="font-weight: lighter;"><b><?php echo $row['REP_ID'] ?></b></td>
                        <td style="font-weight: lighter;"><b><?php echo $row['REP_NAME'] ?></b></td>
                        <td style="font-weight: lighter;"><b><?php echo $row['company_name'] ?></b></td>
                        <td style="font-weight: lighter;">
                            <b style="color: <?php echo ($row['orbit_status'] === 'Orbited') ? '#0f9d58' : '#c62828'; ?>">
                                <?php echo $row['orbit_status']; ?>
                            </b>
                        </td>
                        <td style="font-weight: lighter;"><b><?php echo $row['ROLE'] ?></b></td>
                        <td style="font-weight: lighter;"><b><?php echo $row['REP_EMAIL'] ?></b></td>
                        <td style="font-weight: lighter;"><b><?php echo $row['REP_CONTACT'] ?></b></td>
                        <td class="text-center">
                            <div class="btn-group">
                                  <?php if($_SESSION['login_type'] == 4 || $_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 3): ?>
                                 <?php echo "Not Authorised"?>
                                    <?php else: ?>
                                <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                    Action
                                </button>
                                <?php endif; ?>
                                <div class="dropdown-menu">
                               	<?php
                                    $secret = 'my_app_secret_key'; // put in config file ideally
                                    $payload = $row['REP_ID'];
                                    $hash = hash_hmac('sha256', $payload, $secret);
                                    $encoded = base64_encode($payload . ':' . $hash);
                                    ?>
                                 <?php if ($loginType == 2 && ($row['orbit_status'] ?? '') === 'Not Orbited'): ?>
                                 <a class="dropdown-item" href="./index.php?page=edit_rep&id=<?php echo urlencode($encoded); ?>">Edit</a>
                                 <div class="dropdown-divider"></div>
                                 <?php elseif ($loginType == 2): ?>
                                 <span class="dropdown-item-text text-muted">Orbited rep is view-only</span>
                                 <?php endif; ?>
                                 <!-- <a class="dropdown-item delete_rep" href="javascript:void(0)" data-id="<?php echo $row['REP_ID'] ?>">Delete</a> -->
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
		$('#list').dataTable()
	$('.view_client').click(function(){
		uni_modal("<i class='fa fa-id-card'></i> Client  Details","view_client.php?id="+$(this).attr('data-id'))
	})
	$('.delete_rep').click(function(){
	_conf("Are you sure to delete this client rep?","delete_rep",[$(this).attr('data-id')])
	})
	})
function delete_rep($id){
  start_load();
  $.ajax({
    url: 'ajax.php?action=delete_rep',
    method: 'POST',
    data: {
      id: $id,
      csrf_token: '<?php echo htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES); ?>'
    },
    success: function(resp){
      resp = String(resp).trim();
      if(resp === '1'){
        alert_toast("Data successfully deleted",'success');
        setTimeout(function(){
          location.reload();
        }, 1500);
      } else if(resp === 'csrf'){
        alert_toast("Session expired. Refresh and try again.",'warning');
        end_load();
      } else if(resp === 'unauthorized'){
        alert_toast("Unauthorized.",'warning');
        end_load();
      } else {
        alert_toast("Delete failed: " + resp,'danger');
        end_load();
      }
    },
    error: function(xhr){
      console.log(xhr.status, xhr.responseText);
      alert_toast("Request failed",'danger');
      end_load();
    }
  });
}
</script>
