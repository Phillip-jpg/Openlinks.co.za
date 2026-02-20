<?php
include 'db_connect.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

include 'send_email.php';

function handle_error(string $error_message): void {
    echo "<p style='color:red;font-size:18px;font-weight:bold'>Error: " . htmlspecialchars($error_message) . "</p>";
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    handle_error("Invalid request method!");
    exit;
}

$pm_id     = (int)($_POST['pm_id'] ?? 0);        // Entity / PM to orbit the client to
$client_id = (int)($_POST['client_id'] ?? 0);    // CLIENT_ID from yasccoza_openlink_market.client
$orbiter   = (int)($_SESSION['login_id'] ?? 0);  // who performed the orbit

if ($pm_id <= 0 || $client_id <= 0 || $orbiter <= 0) {
    echo "<p style='color:red;font-size:18px;font-weight:bold'>Invalid PM, Client, or Orbiter.</p>";
    exit;
}

/**
 * 1) Fetch the source (latest) client record by CLIENT_ID
 */
$srcStmt = $conn->prepare("
    SELECT
        CLIENT_ID,
        company_name,
        city,
        province,
        office_id,
        industry_id,
        company_rep,
        Contact,
        Email
    FROM yasccoza_openlink_market.client
    WHERE CLIENT_ID = ?
    ORDER BY client_pri_id DESC
    LIMIT 1
");
$srcStmt->bind_param("i", $client_id);
$srcStmt->execute();
$srcClient = $srcStmt->get_result()->fetch_assoc();

if (!$srcClient) {
    echo "<p style='color:red;font-size:18px;font-weight:bold'>Client not found.</p>";
    echo "<a href='index.php?page=orbit_accounts' class='btn btn-info btn-lg'>⬅ Back</a>";
    exit;
}

/**
 * 2) Check if client already orbited under this PM
 *    FIXED: 2 placeholders + LIMIT 1
 */
$checkStmt = $conn->prepare("
    SELECT 1
    FROM yasccoza_openlink_market.client
    WHERE CLIENT_ID = ?
      AND creator_id = ?
    LIMIT 1
");
$checkStmt->bind_param("ii", $client_id, $pm_id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo "
        <p style='color:orange;font-size:18px;font-weight:bold'>
            Client already exists under this PM.
        </p>
        <a href='index.php?page=orbit_accounts' class='btn btn-info btn-lg'>⬅ Back</a>
    ";
    exit;
}

$conn->begin_transaction();

try {
    /**
     * 3) Insert orbited copy
     *    FIXED: correct bind_param types and count (11)
     */
    $insertStmt = $conn->prepare("
        INSERT INTO yasccoza_openlink_market.client (
            CLIENT_ID,
            company_name,
            city,
            province,
            office_id,
            industry_id,
            company_rep,
            created,
            Contact,
            Email,
            creator_id,
            orbiter_id
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?)
    ");

    $CLIENT_ID    = (int)$srcClient['CLIENT_ID'];
    $company_name = (string)$srcClient['company_name'];
    $city         = (string)$srcClient['city'];
    $province     = (string)$srcClient['province'];
    $office_id    = (int)$srcClient['office_id'];
    $industry_id  = (int)$srcClient['industry_id'];
    $company_rep  = (string)$srcClient['company_rep'];
    $Contact      = (int)$srcClient['Contact'];
    $Email        = (string)$srcClient['Email'];

    // 11 params: i s s s i i s i s i i  => "isssiisisi i"? (no spaces) => "isssiisisi i i" => "isssiisisi ii"
    // Correct final: "isssiisisii"
    $insertStmt->bind_param(
        "isssiisisii",
        $CLIENT_ID,
        $company_name,
        $city,
        $province,
        $office_id,
        $industry_id,
        $company_rep,
        $Contact,
        $Email,
        $pm_id,
        $orbiter
    );

    if (!$insertStmt->execute()) {
        throw new RuntimeException("Client orbit insert failed: " . $conn->error);
    }

    $conn->commit();

    // ----------------------------------------------------
    // 4) EMAILS AFTER SUCCESS (CLIENT + NEW PM)
    // ----------------------------------------------------
    $pmStmt = $conn->prepare("
        SELECT email, number, CONCAT(firstname,' ',lastname) AS full_name
        FROM users
        WHERE id = ?
        LIMIT 1
    ");
    $pmStmt->bind_param("i", $pm_id);
    $pmStmt->execute();
    $pmRow = $pmStmt->get_result()->fetch_assoc();

    $manager_email   = $pmRow['email'] ?? '';
    $manager_number  = $pmRow['number'] ?? '';
    $manager_name    = $pmRow['full_name'] ?? 'Entity Manager';

    $client_email   = $Email;
    $client_contact = $company_rep !== '' ? $company_rep : ($company_name !== '' ? $company_name : 'Client');
    $client_phone   = (string)$Contact;
    $effective_date = date('Y-m-d');

   $subject_client = "Assigned a New Entity that will Service Your Account";
    $subject_pm     = "Orbit Account Assigned to Entity To Service";

  $message_client = "
            <!DOCTYPE html>
            <html>
            <head><meta charset='UTF-8'><title>Account Orbit</title></head>
            <body style='margin:0;padding:0;background-color:#f4f6f8;'>
              <table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'>
                <tr><td align='center'>
                  <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>
                    <tr>
                      <td style='padding:20px;background:#0f1f3d;color:white;'>
                        <table width='100%'><tr>
                          <td align='left'><img src='https://openlinks.co.za/TMS/Image_Redone.png' height='80' width='200'></td>
                          <td align='right' style='font-size:13px;line-height:18px;'>
                            <b>OpenLinks Corporations (Pty) Ltd</b><br>
                            314 Cape Road, Newton Park<br>
                            Port Elizabeth, Eastern Cape 6070
                          </td>
                        </tr></table>
                      </td>
                    </tr>
                    <tr>
                      <td style='padding:30px;color:#333;font-size:15px;'>
                        <p>Hi <b>$client_contact</b>,</p>
            
                        <p>
                          This message is to let you know that a new entity has been assigned to service your account:
                          <b>$company_name</b>.
                        </p>
            
                        <p><b>Effective Date:</b> $effective_date</p>
            
                        <p><b>Assigned Entity:</b> $manager_name</p>
            
                        <p><b>Entity Contact Details</b><br>
                          Email: $manager_email<br>
                          Contact Number: $manager_number
                        </p>
            
                        <p>
                          Please use the contact details above should you need assistance regarding service requests or operational support.
                        </p>
            
                        <div style='text-align:center;margin:35px 0;'>
                          <a href='https://openlinks.co.za/'
                             style='background:#0f1f3d;color:#ffffff;padding:14px 30px;text-decoration:none;border-radius:5px;font-size:15px;font-weight:bold;'>
                            Go to Openlinks
                          </a>
                        </div>
            
                        <p>Kind regards,<br><b>OpenLinks Operations System</b></p>
                      </td>
                    </tr>
                    <tr>
                      <td style='background:#f0f3f7;padding:15px;text-align:center;font-size:12px;'>
                        <small>Automated Notification – Do not reply</small>
                      </td>
                    </tr>
                  </table>
                </td></tr>
              </table>
            </body>
            </html>
            ";


   $message_pm = "
<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'><title>Account Orbit</title></head>
<body style='margin:0;padding:0;background-color:#f4f6f8;'>
  <table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'>
    <tr><td align='center'>
      <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>
        <tr>
          <td style='padding:20px;background:#0f1f3d;color:white;'>
            <table width='100%'><tr>
              <td align='left'><img src='https://openlinks.co.za/TMS/Image_Redone.png' height='80' width='200'></td>
              <td align='right' style='font-size:13px;line-height:18px;'>
                <b>OpenLinks Corporations (Pty) Ltd</b><br>
                314 Cape Road, Newton Park<br>
                Port Elizabeth, Eastern Cape 6070
              </td>
            </tr></table>
          </td>
        </tr>

        <tr>
          <td style='padding:30px;color:#333;font-size:15px;'>
            <p>Hi <b>$manager_name</b>,</p>

            <p>
              This message is to inform you that an account has been orbited from another entity and assigned to your entity to service.
            </p>

            <p><b>Effective Date:</b> $effective_date</p>

            <table width='100%' cellpadding='8' cellspacing='0' style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
              <tr>
                <td style='background:#f0f3f7;width:35%;'><b>Account</b></td>
                <td>$company_name</td>
              </tr>
              <tr>
                <td style='background:#f0f3f7;'><b>Account Contact</b></td>
                <td>$client_contact</td>
              </tr>
              <tr>
                <td style='background:#f0f3f7;'><b>Account Email</b></td>
                <td>$client_email</td>
              </tr>
              <tr>
                <td style='background:#f0f3f7;'><b>Account Number</b></td>
                <td>$client_phone</td>
              </tr>
            </table>

            <p style='margin-top:20px;'>
              Please plan and allocate resources accordingly. If you require any supporting information, please engage the relevant stakeholders.
            </p>

            <div style='text-align:center;margin:35px 0;'>
              <a href='https://openlinks.co.za/'
                 style='background:#0f1f3d;color:#ffffff;padding:14px 30px;text-decoration:none;border-radius:5px;font-size:15px;font-weight:bold;'>
                Go to Openlinks
              </a>
            </div>

            <p>Kind regards,<br><b>OpenLinks Operations System</b></p>
          </td>
        </tr>

        <tr>
          <td style='background:#f0f3f7;padding:15px;text-align:center;font-size:12px;'>
            <small>Automated Notification – Do not reply</small>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
";


    if (!empty($client_email)) {
        sendEmailNotification($client_email, $subject_client, $message_client);
    }
    if (!empty($manager_email)) {
        sendEmailNotification($manager_email, $subject_pm, $message_pm);
    }

    echo "
        <p style='color:green;font-size:18px;font-weight:bold'>
            Client successfully orbited to Entity!
        </p>
        <a href='index.php?page=orbit_accounts' class='btn btn-info btn-lg'>⬅ Back</a>
    ";
    exit;

} catch (Throwable $e) {
    $conn->rollback();
    echo "
        <p style='color:red;font-size:18px;font-weight:bold'>
            Insert failed: " . htmlspecialchars($e->getMessage()) . "
        </p>
        <a href='index.php?page=orbit_accounts' class='btn btn-info btn-lg'>⬅ Back</a>
    ";
    exit;
}
?>
