<?php
// admin_class.php
declare(strict_types=1);

class Action {
    public $db;
    private $deferredEmailJobs = [];

    public function __construct() {
        include 'db_connect.php'; // ensures $conn exists
        $this->db = $conn;

        // Secure session (if not already started)
        if (session_status() !== PHP_SESSION_ACTIVE) {
            ini_set('session.use_strict_mode', '1');
            ini_set('session.cookie_httponly', '1');
            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
                ini_set('session.cookie_secure', '1');
            }
            session_start();
        }
    }

    // ---------------------------
    // Simple session-based rate limit (no DB change needed)
    // ---------------------------
    private function tooManyAttempts(string $key, int $maxAttempts = 8, int $windowSeconds = 300): bool {
        $now = time();

        if (!isset($_SESSION['rl'][$key])) {
            $_SESSION['rl'][$key] = ['count' => 0, 'start' => $now];
            return false;
        }

        $bucket = &$_SESSION['rl'][$key];

        // reset window
        if (($now - (int)$bucket['start']) > $windowSeconds) {
            $bucket = ['count' => 0, 'start' => $now];
            return false;
        }

        return ((int)$bucket['count'] >= $maxAttempts);
    }

    private function recordAttempt(string $key): void {
        $now = time();
        if (!isset($_SESSION['rl'][$key])) {
            $_SESSION['rl'][$key] = ['count' => 1, 'start' => $now];
            return;
        }
        $_SESSION['rl'][$key]['count'] = (int)$_SESSION['rl'][$key]['count'] + 1;
    }

    private function clearAttempts(string $key): void {
        unset($_SESSION['rl'][$key]);
    }

    private function queueEmailJob(string $to, string $subject, string $message): void
    {
        $to = trim($to);
        if ($to === '') {
            return;
        }
        $this->deferredEmailJobs[] = [
            'to' => $to,
            'subject' => $subject,
            'message' => $message
        ];
    }

    public function runEmailJobsNow(array $jobs): void
    {
        if (empty($jobs)) {
            return;
        }
        if (!function_exists('sendEmailNotification')) {
            include_once 'send_email.php';
        }
        if (!function_exists('sendEmailNotification')) {
            return;
        }

        foreach ($jobs as $job) {
            $to = (string)($job['to'] ?? '');
            $subject = (string)($job['subject'] ?? '');
            $message = (string)($job['message'] ?? '');

            if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            try {
                sendEmailNotification($to, $subject, $message);
            } catch (Throwable $e) {
                error_log('Deferred email send failed: ' . $e->getMessage());
            }
        }

    }

    public function runDeferredEmailJobs(): void
    {
        if (empty($this->deferredEmailJobs)) {
            return;
        }
        $jobs = $this->deferredEmailJobs;
        $this->deferredEmailJobs = [];
        $this->runEmailJobsNow($jobs);
    }

    public function dequeueDeferredEmailJobs(): array
    {
        $jobs = $this->deferredEmailJobs;
        $this->deferredEmailJobs = [];
        return $jobs;
    }

    // ---------------------------
    // SECURE LOGIN
    // returns:
    // 1 = success
    // 2 = invalid credentials
    // 3 = locked (rate limit)
    // ---------------------------
  public function login() {
    $email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
    $password = isset($_POST['password']) ? (string)$_POST['password'] : '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        return 2;
    }

    // ---- Rate limit key (email + ip + user-agent hash) ----
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'ua', 0, 120);
    $rlKey = 'login:' . strtolower($email) . ':' . $ip . ':' . sha1($ua);

    if ($this->tooManyAttempts($rlKey, 8, 300)) {
        return 3; // locked
    }

    // IMPORTANT: include type (or your role column) so the app works
    $stmt = $this->db->prepare("
        SELECT id, firstname, lastname, email, password, type
        FROM users
        WHERE email = ?
        LIMIT 1
    ");
    if (!$stmt) {
        return 2;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id, $firstname, $lastname, $dbEmail, $dbPassword, $type);

    if (!$stmt->fetch()) {
        $stmt->close();
        $this->recordAttempt($rlKey);
        return 2;
    }
    $stmt->close();

    $ok = false;

    // hashed
    if (!empty($dbPassword) && password_verify($password, $dbPassword)) {
        $ok = true;
    }
    // legacy md5 fallback
    else if (strlen((string)$dbPassword) === 32 && hash_equals(md5($password), (string)$dbPassword)) {
        $ok = true;

        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $up = $this->db->prepare("UPDATE users SET password=? WHERE id=?");
        if ($up) {
            $up->bind_param("si", $newHash, $id);
            $up->execute();
            $up->close();
        }
    }

    if (!$ok) {
        $this->recordAttempt($rlKey);
        return 2;
    }

    // success: clear attempts
    $this->clearAttempts($rlKey);

    // Secure session
    session_regenerate_id(true);

    $_SESSION['login_id'] = (int)$id;
    $_SESSION['login_firstname'] = (string)$firstname;
    $_SESSION['login_lastname']  = (string)$lastname;
    $_SESSION['login_email']     = (string)$dbEmail;
    $_SESSION['login_name']      = trim((string)$firstname . ' ' . (string)$lastname);

    // ✅ critical for your app
    $_SESSION['login_type'] = (int)$type;

    return 1;
}



    // If you use login2, secure it too (usually same as login)
    public function login2() {
    $student_code = $this->cleanString($_POST['student_code'] ?? '');
    if ($student_code === '') return 3;

    $stmt = $this->db->prepare("
        SELECT *, CONCAT(lastname, ', ', firstname, ' ', middlename) as name
        FROM students
        WHERE student_code = ?
        LIMIT 1
    ");
    if (!$stmt) return 3;

    $stmt->bind_param("s", $student_code);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        foreach ($row as $key => $value) {
            if ($key !== 'password' && !is_numeric($key)) {
                $_SESSION['rs_'.$key] = $value;
            }
        }
        return 1;
    }

    return 3;
}


   public function logout() {

    // Make sure session is started
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // Clear all session data
    $_SESSION = [];

    // Delete session cookie (IMPORTANT: remove for both "/" and current path)
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        $name = session_name();

        // 1) delete at current configured path
        setcookie($name, '', time() - 42000,
            $params["path"] ?? '/',
            $params["domain"] ?? '',
            $params["secure"] ?? false,
            $params["httponly"] ?? true
        );

        // 2) also delete at root path (fixes /TMS vs /)
        setcookie($name, '', time() - 42000,
            '/',
            $params["domain"] ?? '',
            $params["secure"] ?? false,
            $params["httponly"] ?? true
        );
    }

    // Destroy session completely
    session_destroy();

    // Redirect to login
    header("Location: login.php");
    exit;
}

	
		private function cleanString($value)
	        {
	            if (is_array($value)) {
	                return '';
	            }
	        
	            $value = trim((string)$value);
	            $value = strip_tags($value);
	            return $value;
	        }

	private function dispatchUserCreationEmailsInBackground(int $memberId, int $createdType): void
	{
	    if ($memberId <= 0) {
	        return;
	    }

	    $scriptPath = __DIR__ . DIRECTORY_SEPARATOR . 'send_user_creation_emails_bg.php';
	    if (!is_file($scriptPath)) {
	        error_log("Background email script missing: " . $scriptPath);
	        return;
	    }

	    $phpBinary = defined('PHP_BINARY') && PHP_BINARY ? PHP_BINARY : '';
	    if ($phpBinary === '' || !is_file($phpBinary) || stripos((string)basename($phpBinary), 'php') === false) {
	        $bindirPhp = defined('PHP_BINDIR') ? PHP_BINDIR . DIRECTORY_SEPARATOR . 'php.exe' : '';
	        if ($bindirPhp !== '' && is_file($bindirPhp)) {
	            $phpBinary = $bindirPhp;
	        } else {
	            $phpBinary = 'php';
	        }
	    }

	    $memberArg = '--member_id=' . (int)$memberId;
	    $typeArg = '--type=' . (int)$createdType;
	    $started = false;

	    try {
	        if (defined('PHP_OS_FAMILY') && PHP_OS_FAMILY === 'Windows') {
	            // Force working directory to TMS so all relative includes resolve consistently.
	            $launcher = 'cmd /c cd /d "' . __DIR__ . '" && start "" /B "' . $phpBinary . '" "' . $scriptPath . '" ' . $memberArg . ' ' . $typeArg;
	            $h = @popen($launcher, 'r');
	            if (is_resource($h)) {
	                @pclose($h);
	                $started = true;
	            }
	        } else {
	            $cmd = '"' . $phpBinary . '" "' . $scriptPath . '" ' . $memberArg . ' ' . $typeArg . ' > /dev/null 2>&1 &';
	            @exec($cmd);
	            $started = true;
	        }
	    } catch (Throwable $e) {
	        error_log("Failed to dispatch background user emails: " . $e->getMessage());
	    }

	    // Fallback: run synchronously so email is never dropped.
	    if (!$started) {
	        try {
	            if (defined('PHP_OS_FAMILY') && PHP_OS_FAMILY === 'Windows') {
	                @exec('"' . $phpBinary . '" "' . $scriptPath . '" ' . $memberArg . ' ' . $typeArg);
	            } else {
	                @exec('"' . $phpBinary . '" "' . $scriptPath . '" ' . $memberArg . ' ' . $typeArg);
	            }
	        } catch (Throwable $e) {
	            error_log("Fallback email dispatch failed: " . $e->getMessage());
	        }
	    }
	}
		
		
		
function save_user(){
    extract($_POST);
    $idInt = !empty($id) ? (int)$id : 0;
    $creator_id = (int)($_SESSION['login_id'] ?? 0);

    // --------- SECURITY: column allowlist (only columns that exist in users table) ----------
    $allowedCols = [
        'firstname','lastname','email','number','type','avatar','task_ids'
        // add other real users-table columns you use
    ];

    $data = "";
    $existingUser = null;
    $worktypeOnlyEdit = false;

    if ($idInt > 0) {
        $existingUserSql = "SELECT id, email, type, orbiter_id, creator_id FROM users WHERE id = {$idInt}";
        if ((int)($_SESSION['login_type'] ?? 0) === 2 && $idInt !== $creator_id) {
            $existingUserSql .= " AND creator_id = {$creator_id}";
        }
        $existingUserSql .= " ORDER BY date_created DESC LIMIT 1";
        $existingUserQuery = $this->db->query($existingUserSql);
        if ($existingUserQuery && $existingUserQuery->num_rows > 0) {
            $existingUser = $existingUserQuery->fetch_assoc();
        }

        $worktypeOnlyEdit = ((int)($_SESSION['login_type'] ?? 0) === 2)
            && !empty($existingUser)
            && (int)($existingUser['type'] ?? 0) === 3
            && (int)($existingUser['creator_id'] ?? 0) === $creator_id
            && (int)($existingUser['orbiter_id'] ?? 0) !== 0;
    }

    foreach($_POST as $k => $v){
        // skip excluded keys + numeric keys
        if (in_array($k, ['id','creator_id','cpass','password','task_ids','csrf_token','OFFICE_ID','industry_id'], true)) {
            continue;
        }
        if (is_numeric($k)) continue;
        if ($worktypeOnlyEdit) continue;
        // Never allow role/type changes on existing users.
        if ($idInt > 0 && $k === 'type') continue;

        // only allow known DB columns
        if (!in_array($k, $allowedCols, true)) {
            continue;
        }

        $v = $this->db->real_escape_string((string)$v);
        $data .= empty($data) ? " $k='$v' " : ", $k='$v' ";
    }

    // Strong password hash
    if(!$worktypeOnlyEdit && !empty($password)){
        $hashed = password_hash((string)$password, PASSWORD_DEFAULT);
        $hashed = $this->db->real_escape_string($hashed);
        $data .= ", password='$hashed' ";
    }

    $emailValue = isset($email) ? (string)$email : (string)($existingUser['email'] ?? '');
    $emailSafe = $this->db->real_escape_string($emailValue);

    $checkSql = "SELECT 1 FROM users WHERE email ='$emailSafe' ".($idInt ? " AND id != {$idInt} " : "")." LIMIT 1";
    $check = $this->db->query($checkSql);
    if($check && $check->num_rows > 0){
        return 2;
    }

    // ---------- safer file upload ----------
    if(!$worktypeOnlyEdit && isset($_FILES['img']) && !empty($_FILES['img']['tmp_name'])){
        $tmp  = $_FILES['img']['tmp_name'];
        $name = (string)($_FILES['img']['name'] ?? '');

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowedExt = ['jpg','jpeg','png','webp'];

        $mime = @mime_content_type($tmp);
        $allowedMime = ['image/jpeg','image/png','image/webp'];

        if (in_array($ext, $allowedExt, true) && in_array($mime, $allowedMime, true)) {
            $safeBase = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', basename($name));
            $fname = time().'_'.$safeBase;

            if (move_uploaded_file($tmp, 'assets/uploads/'.$fname)) {
                $fnameSafe = $this->db->real_escape_string($fname);
                $data .= ", avatar='$fnameSafe' ";
            }
        }
    }

    // task_ids string stored on users table (keep your logic)
    if (isset($task_ids) && is_array($task_ids)) {
        $cleanTasks = array_map('intval', $task_ids);
        $data .= ", task_ids='".$this->db->real_escape_string(implode(',', $cleanTasks))."' ";
    } else {
        $data .= ", task_ids='0' ";
    }

    // cast these ints safely
    $OFFICE_ID   = isset($OFFICE_ID) ? (int)$OFFICE_ID : 0;
    $industry_id = isset($industry_id) ? (int)$industry_id : 0;

    // Creator from session only (don’t trust POST)
    $backgroundEmailMemberId = 0;
    $backgroundEmailType = 0;

    // ----------------- TRANSACTION -----------------
    $this->db->begin_transaction();

    try {

        if(empty($idInt)){

            // remove these if they were accidentally included
            $data = preg_replace('/industry_id[^,]*,/', '', $data);
            $data = preg_replace('/OFFICE_ID[^,]*,/', '', $data);

            // ---- SAFER manual id generation: lock users table during read+insert ----
            $this->db->query("LOCK TABLES users WRITE");

            $res = $this->db->query("SELECT id FROM users ORDER BY id DESC LIMIT 1");
            if ($res && $res->num_rows > 0) {
                $row = $res->fetch_assoc();
                $new_id = (int)$row['id'] + 1;
            } else {
                $new_id = 1;
            }

            // include creator + id BEFORE insert
            $data .= ", creator_id='{$creator_id}' ";
            $data .= ", id='{$new_id}' ";

            $save = $this->db->query("INSERT INTO users SET $data");

            // unlock regardless
            $this->db->query("UNLOCK TABLES");

            if(!$save){
                throw new Exception("INSERT users failed: ".$this->db->error);
            }

	            if (isset($type) && (int)$type === 2) {
	                $insert_notifications = "
	                    INSERT INTO pm_notifications 
	                    (PM_ID, Member_ID, Notification_Type)
	                    VALUES ($new_id, 0, 7)
	                ";
	                $this->db->query($insert_notifications);
	            }elseif(isset($type) && (int)$type === 3){
                    $insert_notifications = "
	                INSERT INTO member_notifications 
	                (PM_ID, Member_ID, Notification_Type)
	                VALUES ($creator_id, $new_id, 6)
	            ";
                    $this->db->query($insert_notifications);   
                    
                    
                     $insert_notifications2 = "
	                INSERT INTO pm_notifications 
	                (PM_ID, Member_ID, Notification_Type)
	                VALUES ($creator_id, $new_id, 50)
	            ";
                    $this->db->query($insert_notifications2);

                }
            
            

            // admin_sector insert (keep your logic)
            if ($industry_id) {
                $insert_sector = "
                    INSERT INTO yasccoza_openlink_admin_db.admin_sector (ADMIN_ID, OFFICE_ID, INDUSTRY_ID)
                    VALUES ($new_id, $OFFICE_ID, $industry_id)
                ";
                if(!$this->db->query($insert_sector)){
                    throw new Exception("INSERT admin_sector failed: ".$this->db->error);
                }
            }

            // worktypes pivot insert (keep your logic)
            if(isset($task_ids) && is_array($task_ids)){
                foreach ($task_ids as $task_id) {
                    $task_id = (int)$task_id;
                    $insert_query = "INSERT INTO members_and_worktypes(member_id, work_type_id, creator_id) VALUES ($new_id, $task_id, $creator_id)";
                    if(!$this->db->query($insert_query)){
                        throw new Exception("INSERT members_and_worktypes failed: ".$this->db->error);
                    }
                }
            }

            // Dispatch email after response via background CLI worker.
            $backgroundEmailMemberId = (int)$new_id;
            $backgroundEmailType = isset($type) ? (int)$type : 0;

            // Keep legacy template code below but skip inline SMTP so save_user returns fast.
            if (false) {

            // ===================== EMAIL STUFF (FIXED) =====================
            // Send emails only for NEW members, and do it BEFORE commit/return.
            // Uses $new_id (the newly created user's "id"), not $idInt.
            include 'send_email.php';

            $memberId = (int)$new_id;

            $Query = $this->db->query("
                SELECT
                    nt.*,
                    CONCAT(u.firstname, ' ', u.lastname) AS member,
                    u.email  AS member_email,
                    u.number AS member_number,
                    u.date_created,

                    CONCAT(uc.firstname, ' ', uc.lastname) AS Manager_Created,
                    uc.email  AS manager_email,
                    uc.number AS manager_number,

	                    CASE
	                        WHEN u.type = 1 THEN 'Super Admin'
	                        WHEN u.type = 2 THEN 'Project Manager'
	                        WHEN u.type = 3 THEN 'Member'
	                        WHEN u.type = 4 THEN 'Admin Assistant'
	                        ELSE 'User'
	                    END AS Role
                FROM member_notifications nt
                LEFT JOIN users u  ON u.id = nt.Member_ID
                LEFT JOIN users uc ON uc.id = u.creator_id
                WHERE nt.Member_ID = $memberId
                ORDER BY nt.id DESC
                LIMIT 1
            ");

            if ($Query && $Query->num_rows > 0) {
                $row = $Query->fetch_assoc();

                $manager_email   = $row['manager_email'] ?? '';
                $member_email    = $row['member_email'] ?? '';
                $manager_name    = $row['Manager_Created'] ?? '';
                $member_number   = $row['member_number'] ?? '';
                $manager_number  = $row['manager_number'] ?? '';
                $member          = $row['member'] ?? '';
                $role            = $row['Role'] ?? '';
                $date_created    = $row['date_created'] ?? '';

                $subject = "Welcome Your Member Account Has Been Created";
                
                $subject2 = "Resource Has Been Created for your Entity";

                $message1 = "
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <title>Welcome</title>
                </head>
                <body style='margin:0;padding:0;background-color:#f4f6f8;'>
                    <table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'>
                        <tr>
                            <td align='center'>
                                <table width='600' cellpadding='0' cellspacing='0'
                                    style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>
                                    
                                    <tr>
                                        <td style='padding:20px;background:#0f1f3d;color:white;'>
                                            <table width='100%'>
                                                <tr>
                                                    <td align='left'>
                                                        <img src='https://openlinks.co.za/TMS/Image_Redone.png' height='80' width='200'>
                                                    </td>
                                                    <td align='right' style='font-size:13px;line-height:18px;'>
                                                        <b>OpenLinks Corporations (Pty) Ltd</b><br>
                                                        314 Cape Road, Newton Park<br>
                                                        Port Elizabeth, Eastern Cape 6070
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style='padding:30px;color:#333;font-size:15px;'>
                                            <p>Hi <b>$member</b>,</p>

                                            <p>
                                                Welcome to the Openlinks we are excited to have you join the platform  $manager_name .<br>
                                                This email confirms that your member account has been successfully created on our system under the Entity <b>$manager_name </b>.
                                            </p>

                                            <p>Your account details are as follows:</p>

                                            <table width='100%' cellpadding='8' cellspacing='0'
                                                style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
                                                <tr><td style='background:#f0f3f7;width:35%;'><b>Entity:</b></td><td>$member</td></tr>
                                                <tr><td style='background:#f0f3f7;'><b>Role:</b></td><td>$role</td></tr>
                                                <tr><td style='background:#f0f3f7;'><b>Email:</b></td><td>$member_email</td></tr>
                                                <tr><td style='background:#f0f3f7;'><b>Contact:</b></td><td>$member_number</td></tr>
                                                <tr><td style='background:#f0f3f7;'><b>Start Date:</b></td><td>$date_created</td></tr>
                                            </table>

                                            <p style='margin-top:18px;'>
                                                Over the next period, you will be onboarded by your Entity Manager and designated Supervisor.
                                                This onboarding process is designed to set you up for success and ensure that you are confident and ready to perform your role. During onboarding, they will take you through:
                                                
                                            </p>
                                            
                                            <table width='100%' cellpadding='8' cellspacing='0'
                                                style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
                                                <tr><td style='background:#f0f3f7;width:35%;'>Your contract arrangement</td></tr>
                                                <tr><td style='background:#f0f3f7;'>The team you will be working with</td></tr>
                                                <tr><td style='background:#f0f3f7;'>The service plans linked to those accounts</td></tr>
                                                <tr><td style='background:#f0f3f7;'>Service work type competency for the services you will deliver</td></tr>
                                                <tr><td style='background:#f0f3f7;'>Adherence to the service plans governing the accounts you support</td></tr>
                                                <tr><td style='background:#f0f3f7;'>Adherence to the service plans governing the accounts you support</td></tr>
                                                <tr><td style='background:#f0f3f7;'>Access to the tools of the trade required for your daily work (systems, platforms, and operational tools)</td></tr>
                                            </table>
                                            
                                             <p> This process ensures that, as a member of this entity, you are properly prepared, informed, and equipped to deliver services in line with the service plans and operational standards of the organisation.</p>

                                            <p><b>Your main contact is:</b><br>
                                                Entity Manager: <b>$manager_name</b><br>
                                                Email: $manager_email<br>
                                                Contact Number: $manager_number
                                            </p>
                                            
                                            
                                            <p>If you have any questions about your role, your onboarding, or what is expected of you, please do not hesitate to reach out to your Supervisor or Entity Manager. They will guide you through the next steps and ensure you are supported from day one.
                                            We are pleased to welcome you and look forward to seeing you grow and succeed with us. </p>


                                            <div style='text-align:center;margin:35px 0;'>
                                                <a href='https://openlinks.co.za/'
                                                    style='background:#0f1f3d;color:#ffffff;padding:14px 30px;
                                                    text-decoration:none;border-radius:5px;font-size:15px;font-weight:bold;'>
                                                    Go to Openlinks
                                                </a>
                                            </div>

                                            <p>
                                                Kind regards,<br>
                                                <b>OpenLinks Operations System</b>
                                            </p>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style='background:#f0f3f7;padding:15px;text-align:center;font-size:12px;'>
                                            <small>Automated Notification – Do not reply</small>
                                        </td>
                                    </tr>

                                </table>
                            </td>
                        </tr>
                    </table>
                </body>
                </html>
                ";

	                $message2 = "
	                <!DOCTYPE html>
	                <html>
                <head>
                    <meta charset='UTF-8'>
                    <title>Resource Created</title>
                </head>
                <body style='margin:0;padding:0;background-color:#f4f6f8;'>
                    <table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'>
                        <tr>
                            <td align='center'>
                                <table width='600' cellpadding='0' cellspacing='0'
                                    style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>

                                    <tr>
                                        <td style='padding:20px;background:#0f1f3d;color:white;'>
                                            <table width='100%'>
                                                <tr>
                                                    <td align='left'>
                                                        <img src='https://openlinks.co.za/TMS/Image_Redone.png' height='80' width='200'>
                                                    </td>
                                                    <td align='right' style='font-size:13px;line-height:18px;'>
                                                        <b>OpenLinks Corporations (Pty) Ltd</b><br>
                                                        314 Cape Road, Newton Park<br>
                                                        Port Elizabeth, Eastern Cape 6070
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style='padding:30px;color:#333;font-size:15px;'>
                                            <p>Hi <b>$manager_name</b>,</p>

                                            <p>
                                                This message is to inform you that a resource has been successfully created and assigned to support your entity.
                                            </p>

                                            <p>Resource details are as follows:</p>

                                            <table width='100%' cellpadding='8' cellspacing='0'
                                                style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
                                                <tr><td style='background:#f0f3f7;width:35%;'><b>Entity:</b></td><td>$member</td></tr>
                                                <tr><td style='background:#f0f3f7;'><b>Role:</b></td><td>$role</td></tr>
                                                <tr><td style='background:#f0f3f7;'><b>Email:</b></td><td>$member_email</td></tr>
                                                <tr><td style='background:#f0f3f7;'><b>Contact:</b></td><td>$member_number</td></tr>
                                                <tr><td style='background:#f0f3f7;'><b>Start Date:</b></td><td>$date_created</td></tr>
                                            </table>

                                            <div style='text-align:center;margin:35px 0;'>
                                                <a href='https://openlinks.co.za/'
                                                    style='background:#0f1f3d;color:#ffffff;padding:14px 30px;
                                                    text-decoration:none;border-radius:5px;font-size:15px;font-weight:bold;'>
                                                    Go to Openlinks
                                                </a>
                                            </div>

                                            <p>
                                                Kind regards,<br>
                                                <b>OpenLinks Operations System</b>
                                            </p>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style='background:#f0f3f7;padding:15px;text-align:center;font-size:12px;'>
                                            <small>Automated Notification – Do not reply</small>
                                        </td>
                                    </tr>

                                </table>
                            </td>
                        </tr>
                    </table>
                </body>
	                </html>
	                ";
	
	                if (isset($type) && (int)$type === 2) {
	                    $main_admin_name = 'Main Admin';
	                    $main_admin_email = '';
	                    $main_admin_number = '';
	
	                    $mainAdminQ = $this->db->query("
	                        SELECT CONCAT(firstname, ' ', lastname) AS name, email, number
	                        FROM users
	                        WHERE type = 1
	                        ORDER BY id ASC
	                        LIMIT 1
	                    ");
	                    if ($mainAdminQ && $mainAdminQ->num_rows > 0) {
	                        $mainAdminRow = $mainAdminQ->fetch_assoc();
	                        $main_admin_name = trim((string)($mainAdminRow['name'] ?? 'Main Admin'));
	                        if ($main_admin_name === '') {
	                            $main_admin_name = 'Main Admin';
	                        }
	                        $main_admin_email = (string)($mainAdminRow['email'] ?? '');
	                        $main_admin_number = (string)($mainAdminRow['number'] ?? '');
	                    }
	
	                    $member_safe = htmlspecialchars((string)$member, ENT_QUOTES, 'UTF-8');
	                    $member_email_safe = htmlspecialchars((string)$member_email, ENT_QUOTES, 'UTF-8');
	                    $entity_safe = htmlspecialchars((string)$manager_name, ENT_QUOTES, 'UTF-8');
	                    $main_admin_name_safe = htmlspecialchars((string)$main_admin_name, ENT_QUOTES, 'UTF-8');
	                    $main_admin_email_safe = htmlspecialchars((string)$main_admin_email, ENT_QUOTES, 'UTF-8');
	                    $main_admin_number_safe = htmlspecialchars((string)$main_admin_number, ENT_QUOTES, 'UTF-8');
	
	                    $subject = "Welcome to Your Entity";
	
	                    $message1 = "
	                    <!DOCTYPE html>
	                    <html>
	                    <head>
	                        <meta charset='UTF-8'>
	                        <title>Welcome to Your Entity</title>
	                    </head>
	                    <body style='margin:0;padding:0;background-color:#f4f6f8;'>
	                        <table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'>
	                            <tr>
	                                <td align='center'>
	                                    <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>
	                                        <tr>
	                                            <td style='padding:20px;background:#0f1f3d;color:white;'>
	                                                <table width='100%'>
	                                                    <tr>
	                                                        <td align='left'>
	                                                            <img src='https://openlinks.co.za/TMS/Image_Redone.png' height='80' width='200'>
	                                                        </td>
	                                                        <td align='right' style='font-size:13px;line-height:18px;'>
	                                                            <b>OpenLinks Corporations (Pty) Ltd</b><br>
	                                                            314 Cape Road, Newton Park<br>
	                                                            Port Elizabeth, Eastern Cape 6070
	                                                        </td>
	                                                    </tr>
	                                                </table>
	                                            </td>
	                                        </tr>
	                                        <tr>
	                                            <td style='padding:30px;color:#333;font-size:15px;'>
	                                                <p>Hi <b>$member_safe</b>,</p>
	                                                <p>Welcome to Openlinks your account has been created successfully.</p>
	                                                <table width='100%' cellpadding='8' cellspacing='0' style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
	                                                    <tr><td style='background:#f0f3f7;width:35%;'><b>Role</b></td><td>Entity</td></tr>
	                                                    <tr><td style='background:#f0f3f7;'><b>Email</b></td><td>$member_email_safe</td></tr>
	                                                </table>
	                                                <p style='margin-top:18px;'>
	                                                    If you need any assistance, please contact the Main Admin:
	                                                </p>
	                                                <table width='100%' cellpadding='8' cellspacing='0' style='border-collapse:collapse;font-size:14px;margin-top:10px;'>
	                                                    <tr><td style='background:#f0f3f7;width:35%;'><b>Main Admin</b></td><td>$main_admin_name_safe</td></tr>
	                                                    <tr><td style='background:#f0f3f7;'><b>Email</b></td><td>$main_admin_email_safe</td></tr>
	                                                    <tr><td style='background:#f0f3f7;'><b>Contact Number</b></td><td>$main_admin_number_safe</td></tr>
	                                                </table>
	                                                <div style='text-align:center;margin:35px 0;'>
	                                                    <a href='https://openlinks.co.za/' style='background:#0f1f3d;color:#ffffff;padding:14px 30px;text-decoration:none;border-radius:5px;font-size:15px;font-weight:bold;'>
	                                                        Go to Openlinks
	                                                    </a>
	                                                </div>
	                                                <p>
	                                                    Kind regards,<br>
	                                                    <b>OpenLinks Operations System</b>
	                                                </p>
	                                            </td>
	                                        </tr>
	                                        <tr>
	                                            <td style='background:#f0f3f7;padding:15px;text-align:center;font-size:12px;'>
	                                                <small>Automated Notification - Do not reply</small>
	                                            </td>
	                                        </tr>
	                                    </table>
	                                </td>
	                            </tr>
	                        </table>
	                    </body>
	                    </html>
	                    ";
	                }

	                // SEND EMAILS (FIXED RECIPIENTS)
	                if (!empty($member_email)) {
	                    sendEmailNotification("$member_email", $subject, $message1);
                }
                if (!empty($manager_email)) {
                    sendEmailNotification("$manager_email", $subject2, $message2);
                }
            }
            // =================== /EMAIL STUFF (FIXED) ===================
            }

        } else {

            // admin_sector insert (keep your logic)
            if(!$worktypeOnlyEdit && $industry_id){
                $insert_sector = "
                    INSERT INTO yasccoza_openlink_admin_db.admin_sector(ADMIN_ID, OFFICE_ID, INDUSTRY_ID)
                    VALUES ($idInt, $OFFICE_ID, $industry_id)
                ";
                if(!$this->db->query($insert_sector)){
                    throw new Exception("INSERT admin_sector failed: ".$this->db->error);
                }
            }

            // reset pivot
            if(!$this->db->query("DELETE FROM members_and_worktypes WHERE member_id = $idInt AND creator_id = $creator_id")){
                throw new Exception("DELETE members_and_worktypes failed: ".$this->db->error);
            }

            if(isset($task_ids) && is_array($task_ids)){
                foreach ($task_ids as $task_id) {
                    $task_id = (int)$task_id;
                    $insert_query = "INSERT INTO members_and_worktypes(member_id, work_type_id, creator_id) VALUES ($idInt, $task_id, $creator_id)";
                    if(!$this->db->query($insert_query)){
                        throw new Exception("INSERT members_and_worktypes failed: ".$this->db->error);
                    }
                }
            }

            // remove fields not in users table
            $data = preg_replace('/industry_id[^,]*,/', '', $data);
            $data = preg_replace('/OFFICE_ID[^,]*,/', '', $data);
            $data = preg_replace("/(^|,)\\s*type\\s*=\\s*'[^']*'\\s*/i", '$1 ', $data);
            $data = trim(preg_replace('/\\s+,\\s+/', ', ', $data), " ,");

            $updateWhere = "id = $idInt AND creator_id = $creator_id";
            if ($idInt === $creator_id) {
                $updateWhere = "id = $idInt AND (creator_id = $creator_id OR type != 3)";
            }

            $save = $this->db->query("UPDATE users SET $data WHERE $updateWhere");
            if(!$save){
                throw new Exception("UPDATE users failed: ".$this->db->error);
            }
        }

        $this->db->commit();

        if ($backgroundEmailMemberId > 0) {
            $this->dispatchUserCreationEmailsInBackground($backgroundEmailMemberId, $backgroundEmailType);
        }

        return 1;

    } catch (Exception $e) {
        $this->db->rollback();
        return 0;
    }
}





	   
function save_configure() {
    extract($_POST);

    // Initialize the save status
    $save = true;

    // Ensure worktype_ids is an array
    $worktype_ids = isset($worktype_ids) ? $worktype_ids : [];

    if (empty($id)) {
        // Fetch the last (maximum) id from the table and increment it by 1
        $result = $this->db->query("SELECT MAX(id) AS max_id FROM configure_rate");
        $row = $result->fetch_assoc();
        $id = isset($row['max_id']) ? $row['max_id'] + 1 : 1; // Start from 1 if no id exists

        // Insert a row for each worktype_id using the incremented ID
        foreach ($worktype_ids as $worktype_id) {
            $insert = $this->db->prepare("INSERT INTO configure_rate (id, name, low, high, discount, worktype_ids) VALUES (?, ?, ?, ?, ?, ?)");
            $insert->bind_param('isdddi', $id, $name, $low, $high, $discount, $worktype_id);
            
              $save = $insert->execute();
                $insert->close();
        }

    } else {
        // Update logic
      foreach ($worktype_ids as $worktype_id) {
    // Check if the entry with this worktype_id and id exists
    $check = $this->db->prepare("SELECT COUNT(*) FROM configure_rate WHERE id = ? AND worktype_ids = ?");
    $check->bind_param('ii', $id, $worktype_id);
    $check->execute();
    $check->bind_result($exists);
    $check->fetch();
    $check->close();

    if ($exists > 0) {
        // Update the existing record
        $update = $this->db->prepare("UPDATE configure_rate SET name = ?, low = ?, high = ?, discount = ?, worktype_ids = ? WHERE id = ? AND worktype_ids = ?");
        $update->bind_param('sdddiii', $name, $low, $high, $discount, $worktype_id, $id, $worktype_id);
        $update->execute();
        $update->close();
    } else {
        // Insert a new record
        $insert = $this->db->prepare("INSERT INTO configure_rate (id, name, low, high, discount, worktype_ids) VALUES (?, ?, ?, ?, ?, ?)");
        $insert->bind_param('isdddi', $id, $name, $low, $high, $discount, $worktype_id);
        $insert->execute();
        $insert->close();
    }
}

    }
    
    if($save){
			return 1;
		}else{
			return 0;
		}
    
}


function printClaims($startWeek, $endWeek) {
    include('db_connect.php');  // Ensure this file contains correct DB connection settings

    // Append time to end date to include the whole day
    $endWeek .= ' 23:59:59';

    $fileName = "Period_Claims_" . date('Y-m-d') . ".xls";
    $fields = array(
        "Period",
        "Month",
        "Job_ID",
        "Member",
        "Project Manager",
        "Job Name",
        "CLIENT",
        "Work Type",
        "Activity",
        "Assigned_Start_Date",
        "Assigned_End_Date",
        "Done_Date",
        "Discount Applied",
        "Claim Amount",
        "Claim Status",
        "Date Processed",
        "Who Processed"
    );

    // Use prepared statements to avoid SQL injection
    $sql = "SELECT sc.*, 
                   CONCAT(u.firstname, ' ', u.lastname) AS Member,  
                   CONCAT(u1.firstname, ' ', u1.lastname) AS Approved_by
            FROM saved_claims sc 
            INNER JOIN users u ON sc.Member_ID = u.id
            INNER JOIN users u1 ON sc.Login_id = u1.id
            WHERE sc.time_recorded BETWEEN ? AND ?";

    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameters
        $stmt->bind_param("ss", $startWeek, $endWeek);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Prepare the Excel data
            $excelData = implode("\t", $fields) . "\n";  // Header row

            while ($row = $result->fetch_assoc()) {
                // Determine the claim status
                $claimStatus = '';
                if ($row['claim_status'] == 1) {
                    $claimStatus = 'Approved';
                } elseif ($row['claim_status'] == 2) {
                    $claimStatus = 'Rejected';
                } else {
                    $claimStatus = 'Pending';
                }

                // Prepare row data
                $lineData = array(
                    $row['period'],
                    $row['Month'],
                    $row['Job_ID'],
                    $row['Member'],
                    $row['P_Manager'],
                    $row['Job_Name'],
                    $row['CLIENT'],
                    $row['Worktype'],
                    $row['Activity'],
                    $row['Start_Date'],
                    $row['End_Date'],
                    $row['Done_Date'],
                    $row['Discount_Applied'],
                    'R' . $row['Claim_Amount'],
                    $claimStatus,
                    $row['time_recorded'],
                    $row['Approved_by']
                );

                $excelData .= implode("\t", $lineData) . "\n";
            }

            // Send headers to prompt download as an Excel file
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=\"$fileName\"");

            // Output the Excel data
            echo $excelData;
        } else {
            echo "No data found";
        }
        $stmt->close();
    } else {
        echo "Error preparing query: " . $conn->error;
    }

    // Close the database connection
    $conn->close();

    // Terminate script execution
    exit();
}






function printResponses($startWeek, $endWeek) {
    include('db_connect.php');  // Ensure this file contains correct DB connection settings

    // Append time to end date to include the whole day
    $endWeek .= ' 23:59:59';

    $fileName = "Jobs_Responses_" . date('Y-m-d') . ".xls";
    $fields = array(
        'Job_ID',
        'Job_Name',
        'Scorecard',
        'Company',
        'Date_Job_Created',
        'Date_Closing',
        'Date_Responded',
        'Status',
        'Respondent',
        'Responded_For',
        'start',
        'end',
    );

    $sql = "SELECT DISTINCT
                m.POST_ID AS Job_ID,
                m.Title AS Job_Name,
                s.title AS Scorecard,
                c.company_name AS Company,
                DATE_FORMAT(m.Created, '%Y-%m-%d') AS Date_Job_Created,
                DATE_FORMAT(m.EXPIRY, '%Y-%m-%d') AS Date_Closing,
                DATE_FORMAT(sr.created, '%Y-%m-%d') AS Date_Responded,
                CONCAT(u.firstname, ' ', u.lastname) AS Respondent,
                sr.COMPANY AS Responded_For
            FROM
                yasccoza_openlink_market.market_post m,
                yasccoza_openlink_market.scorecard_response sr,
                yasccoza_openlink_market.scorecard s,
                yasccoza_tms_db.users u,
                yasccoza_openlink_market.client c
            WHERE
                m.POST_ID = sr.POST_ID
                AND m.SCORECARD_ID = s.SCORECARD_ID
                AND u.id = sr.USER_ID
                AND c.CLIENT_ID = m.CLIENT_ID
                AND sr.created >= '$startWeek' AND sr.created <= '$endWeek'
            ORDER BY
                Job_ID DESC";

    if ($result = $conn->query($sql)) {
        if ($result->num_rows > 0) {
            // Prepare the Excel data
            $excelData = implode("\t", $fields) . "\n";  // Header row

            while ($row = $result->fetch_assoc()) {
                $jobStatus = ($row['Date_Closing'] > $row['Date_Responded']) ? "Responded on time" : "Responded late";

                $lineData = array(
                    $row['Job_ID'],
                    $row['Job_Name'],
                    $row['Scorecard'],
                    $row['Company'],
                    $row['Date_Job_Created'],
                    $row['Date_Closing'],
                    $row['Date_Responded'],
                    $jobStatus,
                    $row['Respondent'],
                    $row['Responded_For'],
                    $startWeek,
                    $endWeek,
                );
                $excelData .= implode("\t", $lineData) . "\n";
            }

            // Send headers to prompt download as an Excel file
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=\"$fileName\"");

            // Output the Excel data
            echo $excelData;
        } else {
            echo "No data found1";
        }
        $result->free();
    } else {
        echo "Error executing query: " . $conn->error;
    }

    // Close the database connection
    $conn->close();

    // Terminate script execution
    exit();
}


function printResponsesnot($start, $end) {
    include('db_connect.php');  // Ensure this file contains correct DB connection settings

    $fileName = "Jobs_Not_Responded_" . date('Y-m-d') . ".xls";
    $fields = array(
        'POST_ID',
        'Month',
        'Title',
        'CLIENT',
        'task_name',
        'Created',
         'EXPIRY',
    );

    // Append time to end date to include the whole day
    $end = $end . ' 23:59:59';

    $query1 = $conn->query("
        SELECT POST_ID
        FROM (
            SELECT m.Title, m.EXPIRY, m.Created, m.POST_ID, m.SCORECARD_ID, c.company_name AS Company,
                   COUNT(DISTINCT CONCAT(sr.USER_ID, sr.COMPANY)) AS TotalResponses
            FROM yasccoza_openlink_market.market_post m
            LEFT JOIN yasccoza_openlink_market.client c ON m.CLIENT_ID = c.CLIENT_ID
            LEFT JOIN yasccoza_openlink_market.scorecard_response sr ON sr.POST_ID = m.POST_ID
            GROUP BY m.Title, m.EXPIRY, m.Created, m.POST_ID, m.SCORECARD_ID, Company
            HAVING COUNT(DISTINCT sr.USER_ID) > 0
        ) AS Subquery
        GROUP BY Title, EXPIRY, Created, POST_ID, SCORECARD_ID, Company
    ");

    $post_ids_query1 = [];
    while ($row = $query1->fetch_assoc()) {
        $post_ids_query1[] = $row['POST_ID'];
    }

    // Second query
    $query2 = $conn->query("SELECT POST_ID FROM yasccoza_openlink_market.market_post");

    $post_ids_query2 = [];
    while ($row = $query2->fetch_assoc()) {
        $post_ids_query2[] = $row['POST_ID'];
    }

    $non_matching_post_ids = array_diff($post_ids_query2, $post_ids_query1);

    if (!empty($non_matching_post_ids)) {
        $sanitized_ids = implode(',', array_map('intval', $non_matching_post_ids));

        // Third query
        $query3 = "
            WITH RECURSIVE split_ids AS (
                SELECT 
                    mp.POST_ID AS job_id,
                    TRIM(SUBSTRING_INDEX(mp.WORKTYPE, ',', 1)) AS task_id,
                    SUBSTRING(mp.WORKTYPE, LENGTH(SUBSTRING_INDEX(mp.WORKTYPE, ',', 1)) + 2) AS rest_ids
                FROM 
                    yasccoza_openlink_market.market_post mp
                WHERE 
                    mp.WORKTYPE IS NOT NULL
                UNION ALL
                SELECT 
                    job_id,
                    TRIM(SUBSTRING_INDEX(rest_ids, ',', 1)),
                    SUBSTRING(rest_ids, LENGTH(SUBSTRING_INDEX(rest_ids, ',', 1)) + 2)
                FROM 
                    split_ids
                WHERE 
                    rest_ids <> ''
            )
            SELECT DISTINCT
                m.Title, 
                m.EXPIRY, 
                m.Created, 
                MONTHNAME(m.Created) AS Month,
                m.POST_ID, 
                wt.task_name, 
                COALESCE(c.company_name, smme.Legal_name) AS CLIENT
            FROM 
                yasccoza_openlink_market.market_post m
            LEFT JOIN 
                yasccoza_openlink_market.client c ON m.CLIENT_ID = c.CLIENT_ID
            LEFT JOIN 
                yasccoza_openlink_smmes.register smme ON m.CLIENT_ID = smme.SMME_ID
            LEFT JOIN 
                yasccoza_openlink_market.scorecard_response sr ON sr.POST_ID = m.POST_ID
            LEFT JOIN 
                split_ids si ON m.POST_ID = si.job_id
            LEFT JOIN 
                yasccoza_tms_db.task_list wt ON wt.id = si.task_id
            WHERE 
                m.POST_ID IN ($sanitized_ids)
            AND m.Created >= '$start' AND m.Created <= '$end'
            ORDER BY 
                m.EXPIRY DESC";

        if ($result = $conn->query($query3)) {
            if ($result->num_rows > 0) {
                // Prepare the Excel data
                $excelData = implode("\t", $fields) . "\n";  // Header row

                while ($row = $result->fetch_assoc()) {
                    $lineData = array(
                        $row['POST_ID'],
                        $row['Month'],
                        $row['Title'],
                        $row['CLIENT'],
                        $row['task_name'],
                        $row['Created'],
                          $row['EXPIRY'],
                    );
                    $excelData .= implode("\t", $lineData) . "\n";
                }

                // Send headers to prompt download as an Excel file
                header("Content-Type: application/vnd.ms-excel");
                header("Content-Disposition: attachment; filename=\"$fileName\"");

                // Output the Excel data
                echo $excelData;
            } else {
                echo "No data found";
            }
            $result->free();
        } else {
            echo "Error executing query: " . $conn->error;
        }
    }

    // Close the database connection
    $conn->close();

    // Terminate script execution
    exit();
}


    

function printSentJobs() {
    include('db_connect.php');  // Ensure this file contains correct DB connection settings

    $fileName = "Sent_Jobs_List_" . date('Y-m-d') . ".xls";
    $fields = array(
        'Who_Sent',
        'Date_Sent',
        'Sent_To_SMME',
        'Job_ID',
        'Job_Title',
        'Client_Name',
        'Type_of_Send',
    );






    $sql = "SELECT 
    CONCAT(u.firstname, ' ', u.lastname) AS Who_Sent,
    DATE_FORMAT(js.Date_Sent, '%Y-%m-%d') AS Date_Sent,
    js.POST_ID as Job_ID,
    CONCAT(SUBSTRING_INDEX(mp.Title, ' ', 5), IF(LENGTH(mp.Title) - LENGTH(REPLACE(mp.Title, ' ', '')) >= 2, '...', '')) AS Job_Title,
    c.company_name as Client_Name, 
    r.Legal_name as Sent_To_SMME,
    'Multiple send from Ops' AS Type_of_Send
FROM 
    users AS u
JOIN 
    yasccoza_openlink_market.job_and_smmes AS js ON u.id = js.Who_Sent
JOIN 
    yasccoza_openlink_market.market_post AS mp ON js.POST_ID = mp.POST_ID
JOIN 
    yasccoza_openlink_smmes.register AS r ON js.SMME_ID = r.SMME_ID
JOIN
    yasccoza_openlink_market.client AS c ON mp.CLIENT_ID = c.CLIENT_ID

UNION

SELECT 
    CONCAT(u.firstname, ' ', u.lastname) AS Who_Sent,
    DATE_FORMAT(mp.Created, '%Y-%m-%d') AS Date_Sent,
    mp.POST_ID as Job_ID,
    CONCAT(SUBSTRING_INDEX(mp.Title, ' ', 5), IF(LENGTH(mp.Title) - LENGTH(REPLACE(mp.Title, ' ', '')) >= 2, '...', '')) AS Job_Title,
    c.company_name as Client_Name, 
    r.Legal_name as Sent_To_SMME,
    'Single send from TMS' AS Type_of_Send
FROM 
    users AS u
JOIN 
    yasccoza_openlink_market.market_post AS mp ON u.id = mp.USER_ID
JOIN 
    yasccoza_openlink_smmes.register AS r ON mp.COMPANY = r.SMME_ID
JOIN
    yasccoza_openlink_market.client AS c ON mp.CLIENT_ID = c.CLIENT_ID

ORDER BY 
    Date_Sent DESC;

";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Prepare the Excel data
        $excelData = implode("\t", $fields) . "\n";  // Header row

        while ($row = $result->fetch_assoc()) {
            
           
            $lineData = array(
                $row['Who_Sent'],
                $row['Date_Sent'],
                $row['Sent_To_SMME'],
                $row['Job_ID'],
                $row['Job_Title'],
                $row['Client_Name'],
                 $row['Type_of_Send'],
                
            );
            $excelData .= implode("\t", $lineData) . "\n";
        }

        // Send headers to prompt download as an Excel file
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$fileName\"");

        // Output the Excel data
        echo $excelData;
    } else {
        echo "No data found";
    }

    // Close the database connection
    $conn->close();

    // Terminate script execution
    exit();
}



	function printAdminList() {
	    
	

	    
	    
		include('db_connect.php');
	
		$fileName = "Admin_List_" . date('Y-m-d') . ".xls";
	
		$fields = array(
			'Name',
			'Email',
			'Number',
			'User Type',
			'Assigned Work Types',
			'Industry',
			'Office',
	 // Add a column for job status
		);
	
		// Your existing query code here
		$sql = "SELECT
					users.*,
					CONCAT(users.firstname, ' ', users.lastname) AS name,
					CONCAT('(', GROUP_CONCAT(yasccoza_openlink_association_db.industry.office SEPARATOR ' , '), ')') AS offices,
					CONCAT('(', GROUP_CONCAT(yasccoza_openlink_association_db.industry_title.title SEPARATOR ' , '), ')') AS titles
				FROM
					users
				LEFT JOIN
					yasccoza_openlink_admin_db.admin_sector ON users.id = yasccoza_openlink_admin_db.admin_sector.ADMIN_ID
				LEFT JOIN
					yasccoza_openlink_association_db.industry_title ON yasccoza_openlink_association_db.industry_title.TITLE_ID = yasccoza_openlink_admin_db.admin_sector.INDUSTRY_ID
				LEFT JOIN
					yasccoza_openlink_association_db.industry ON yasccoza_openlink_association_db.industry.INDUSTRY_ID = yasccoza_openlink_admin_db.admin_sector.OFFICE_ID
				GROUP BY users.id
				ORDER BY name ASC";
	
		$result = $conn->query($sql);
	
		// Check if there are results
		if ($result->num_rows > 0) {
			// Prepare the Excel data
			$excelData = implode("\t", array_values($fields)) . "\n";
	
			while ($row = $result->fetch_assoc()) {
				$lineData = array(
					!empty($row['firstname']) ? $row['firstname'] . ' ' . $row['lastname'] : 'N/A',
					!empty($row['email']) ? $row['email'] : 'N/A',
					!empty($row['number']) ? $row['number'] : 'N/A',
					getUserType(!empty($row['type']) ? $row['type'] : 'N/A'),
					getAssignedWorkTypes(!empty($row['task_ids']) ? $row['task_ids'] : 'N/A'),
					!empty($row['offices']) ? $row['offices'] : 'N/A',
					!empty($row['titles']) ? $row['titles'] : 'N/A',
				);
				
	
				$excelData .= implode("\t", array_values($lineData)) . "\n";
			}
	
			// Send the headers for the Excel file
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=\"$fileName\"");
	
			// Output the Excel data
			echo $excelData;
		} else {
			echo "No data found";
		}
	
		// Close the database connection
		$conn->close();
	
		// Terminate script execution
		exit();

	}



		function save_client(){
			extract($_POST);
			$data = "";
			$creator_id = isset($creator_id) ? (int)$creator_id : 0;
			$email = isset($email) ? (string)$email : '';
		
		// Validate and sanitize data here (e.g., using filter_input or other validation functions).
	
		// Construct the SQL query
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id','creator_id','USER_CREATED','csrf_token')) && !is_numeric($k)) {
				if (empty($data)) {
					$data .= " $k='" . $this->db->real_escape_string($v) . "' ";
				} else {
					$data .= ", $k='" . $this->db->real_escape_string($v) . "' ";
				}
			}
		}
	
		// Check if email already exists
		$check = $this->db->query("SELECT * FROM yasccoza_openlink_market.client WHERE email = '" . $this->db->real_escape_string($email) . "'" . (!empty($id) ? " AND CLIENT_ID != " . (int)$id : ''))->num_rows;
		
			if ($check > 0) {
					
					return 2; 
					// Email already exists
				} else {
					// Insert new client or update existing client
					if (empty($id)) {
						$save = false;
						$last_insert_id = 0;

						// Generate CLIENT_ID from current max and retry on rare duplicate race.
						for ($attempt = 0; $attempt < 3; $attempt++) {
							$maxRes = $this->db->query("SELECT COALESCE(MAX(CLIENT_ID), 0) AS max_id FROM yasccoza_openlink_market.client");
							if (!$maxRes) {
								return 0;
							}

							$maxRow = $maxRes->fetch_assoc();
							$nextClientId = ((int)$maxRow['max_id']) + 1;

							$save = $this->db->query("INSERT INTO yasccoza_openlink_market.client SET CLIENT_ID = $nextClientId, $data");
							if ($save) {
								$last_insert_id = $nextClientId;
								break;
							}

							// Duplicate key can happen under concurrency; retry with fresh MAX.
							if ((int)$this->db->errno !== 1062) {
								break;
							}
						}

						if ($save && $creator_id > 0 && $last_insert_id > 0) {
							$this->db->query("UPDATE yasccoza_openlink_market.client SET creator_id = $creator_id WHERE CLIENT_ID = $last_insert_id");
						}

					} else {
						$save = $this->db->query("UPDATE yasccoza_openlink_market.client SET $data WHERE CLIENT_ID = ".$id);
					}
		
				try {
					if ($save) {
						return 1; // Success
					} else {
						return 0; // Database query failed
					}
				} catch (Exception $e) {
					return $e->getMessage(); // Handle the database error
				}
			}
		}

			function save_rep(){
				extract($_POST);
				$data = "";
				$clientId = isset($CLIENT_ID) ? (int)$CLIENT_ID : 0;
				if ($clientId <= 0) {
					return 3; // Client not selected
				}
				
				// Validate and sanitize data here (e.g., using filter_input or other validation functions).
		
			// Construct the SQL query
			foreach ($_POST as $k => $v) {
				if (!in_array($k, array('id', 'csrf_token')) && !is_numeric($k)) {
					if (empty($data)) {
						$data .= " $k='" . $this->db->real_escape_string($v) . "' ";
					} else {
						$data .= ", $k='" . $this->db->real_escape_string($v) . "' ";
					}
				}
			}
		
			// Check if email already exists
			$check = $this->db->query("SELECT * FROM client_rep WHERE REP_EMAIL = '" . $this->db->real_escape_string($REP_EMAIL) . "'" . (!empty($id) ? " AND REP_ID != " . (int)$id : ''))->num_rows;
			
			if ($check > 0) {
					
					return 2; 
					// Email already exists
				} else {
						// Insert new client or update existing client
						if (empty($id)) {
		
							$sql = "INSERT INTO client_rep SET $data";
						} else {
							$sql = "UPDATE client_rep SET $data WHERE REP_ID = ".$id;
						}
			
					try {
						$save = $this->db->query($sql);
						if ($save) {
							return 1; // Success
						} else {
							return 0; // Database query failed
						}
					} catch (Exception $e) {
						return $e->getMessage(); // Handle the database error
					}
				}
			}
	
	public function signup() {
    $email = $this->cleanString($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        return 0;
    }

    // Check duplicate email
    $stmt = $this->db->prepare("SELECT COUNT(*) c FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $c = (int)$stmt->get_result()->fetch_assoc()['c'];
    if ($c > 0) return 2;

    // Build basic insert from allowed keys
    $allowed = ['firstname','lastname','middlename','email','number'];
    $fields = [];
    foreach ($allowed as $k) {
        if (isset($_POST[$k])) $fields[$k] = $this->cleanString($_POST[$k]);
    }

    $fields['password'] = password_hash($password, PASSWORD_DEFAULT);

    $cols = array_keys($fields);
    $sql = "INSERT INTO users (".implode(',', $cols).") VALUES (".implode(',', array_fill(0, count($cols), '?')).")";

    $types = str_repeat("s", count($fields));
    $vals = array_values($fields);

    $ins = $this->db->prepare($sql);
    $ins->bind_param($types, ...$vals);
    $save = $ins->execute();

    if ($save) {
        $id = (int)$this->db->insert_id;
        session_regenerate_id(true);
        $_SESSION['login_id'] = $id;
        $_SESSION['login_email'] = $email;
        return 1;
    }

    return 0;
}


	public function update_user() {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) return 0;

    $email = $this->cleanString($_POST['email'] ?? '');

    // Check duplicate email
    $stmt = $this->db->prepare("SELECT COUNT(*) c FROM users WHERE email=? AND id != ?");
    $stmt->bind_param("si", $email, $id);
    $stmt->execute();
    $c = (int)$stmt->get_result()->fetch_assoc()['c'];
    if ($c > 0) return 2;

    // Type/role is immutable after creation; never accept it on edit.
    $allowed = ['firstname','lastname','middlename','email','number'];
    $fields = [];
    foreach ($allowed as $k) {
        if (isset($_POST[$k])) $fields[$k] = $this->cleanString($_POST[$k]);
    }

    // avatar
    if (isset($_FILES['img']) && is_uploaded_file($_FILES['img']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
        $allowedExt = ['jpg','jpeg','png','gif','webp'];
        if (in_array($ext, $allowedExt, true)) {
            $fname = time().'_'.bin2hex(random_bytes(6)).'.'.$ext;
            $dest = 'assets/uploads/'.$fname;
            if (move_uploaded_file($_FILES['img']['tmp_name'], $dest)) {
                $fields['avatar'] = $fname;
            }
        }
    }

    // password
    if (!empty($_POST['password'])) {
        $fields['password'] = password_hash((string)$_POST['password'], PASSWORD_DEFAULT);
    }

    if (empty($fields)) return 0;

    $set = [];
    $types = "";
    $vals = [];
    foreach ($fields as $col => $val) {
        $set[] = "$col=?";
        $types .= "s";
        $vals[] = $val;
    }
    $types .= "i";
    $vals[] = $id;

    $sql = "UPDATE users SET ".implode(',', $set)." WHERE id=?";
    $up = $this->db->prepare($sql);
    $up->bind_param($types, ...$vals);
    $save = $up->execute();

    if ($save) {
        // refresh session values if updating self
        foreach ($fields as $k => $v) {
            if ($k !== 'password') $_SESSION['login_'.$k] = $v;
        }
        return 1;
    }

    return 0;
}

	function delete_client(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM yasccoza_openlink_market.client where CLIENT_ID = ".$id);
		if($delete)
			return 1;
	}
	
function delete_discount() {
    // Extract the POST parameters
    extract($_POST);
    
    // Validate that 'id' exists and is an integer
    if (!isset($id) || !is_numeric($id)) {
        return "Invalid ID";
    }

    // Prepare a safe SQL query using prepared statements
    $delete = $this->db->prepare("DELETE FROM configure_rate WHERE id = ?");
    
    // Check if the prepare statement failed
    if (!$delete) {
        // Output the error message for debugging
        return "Prepare failed: " . $this->db->error;
    }

    // Bind parameters to the prepared statement
    $delete->bind_param('i', $id); // 'i' means the parameter is an integer
    
    // Execute the query
    if ($delete->execute()) {
        $delete->close();
        return 1; // Success
    } else {
        $delete->close();
        return 0; // Failure
    }
}


	
	function delete_rep(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM client_rep where REP_ID = ".$id);
		if($delete)
			return 1;
	}
	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		$delete_sector = "DELETE FROM yasccoza_openlink_admin_db.admin_sector WHERE ADMIN_ID = ".$id;
			   $this->db->query($delete_sector);
		if($delete)
			return 1;
	}
	function save_system_settings(){
		extract($_POST);
		$data = '';
		foreach($_POST as $k => $v){
			if(!is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if($_FILES['cover']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['cover']['name'];
			$move = move_uploaded_file($_FILES['cover']['tmp_name'],'../assets/uploads/'. $fname);
			$data .= ", cover_img = '$fname' ";

		}
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set $data where id =".$chk->fetch_array()['id']);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set $data");
		}
		if($save){
			foreach($_POST as $k => $v){
				if(!is_numeric($k)){
					$_SESSION['system'][$k] = $v;
				}
			}
			if($_FILES['cover']['tmp_name'] != ''){
				$_SESSION['system']['cover_img'] = $fname;
			}
			return 1;
		}
	}
	function save_image(){
		extract($_FILES['file']);
		if(!empty($tmp_name)){
			$fname = strtotime(date("Y-m-d H:i"))."_".(str_replace(" ","-",$name));
			$move = move_uploaded_file($tmp_name,'assets/uploads/'. $fname);
			$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
			$hostName = $_SERVER['HTTP_HOST'];
			$path =explode('/',$_SERVER['PHP_SELF']);
			$currentPath = '/'.$path[1]; 
			if($move){
				return $protocol.'://'.$hostName.$currentPath.'/assets/uploads/'.$fname;
			}
		}
	}
	function save_assign(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('task_id','project_id','activity_id', 'user_id')) && !is_numeric($k)){
				if($k == 'description')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
	
		if (isset($_POST['user_id'])) {
            echo $data;
			$user_ids = $_POST['user_id'];
			print_r($user_ids);
			exit();
			$data .= ", user_id='".implode(',',$user_ids)."' ";
			
		} 
		if(empty($id)){
			$save = $this->db->query("INSERT INTO assigned_duties SET $data");
		} else {
			$save = $this->db->query("UPDATE assigned_duties SET $data WHERE id = $id");
		}
		if($save){
			return 1;
		}
	
	}
	
	
	function save_project(){
    extract($_POST);
    $data = "";
    $login_id = $_SESSION['login_id'];
    
    // 1. PREPARE DATA FOR project_list
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','task_ids','team_ids','files','csrf_token')) && !is_numeric($k)){
            if($k == 'description')
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            
            if(empty($data)){
                $data .= " $k='$v' ";
            }else{
                $data .= ", $k='$v' ";
            }
        }
    }

    if (isset($team_ids)) {
        $data .= ", team_ids = '" . htmlspecialchars($team_ids, ENT_QUOTES) . "'";
    }

    if(isset($task_ids)){
        $data .= ", task_ids='".implode(',',$task_ids)."' ";
    } else {
        $data .= ", task_ids='0' ";
    }
    
    if(isset($status) && $status !== "In-progress") {
        $data .= ", status_change=NOW() ";
    }
    if(isset($status) && $status == "Done") {
        $data .= ", Job_Done=NOW() ";
    }

    // 2. HANDLE INSERT vs UPDATE
    if(empty($id)){
        // --- NEW PROJECT ---
        if (isset($manager_id)) {
            $data .= ", time_assigned=NOW()";
        }

        // Generate Custom ID
        $result = $this->db->query("SELECT id FROM project_list WHERE id BETWEEN 10000 AND 100000");
        if ($result->num_rows === 0) {
            $newId = 10000;
        } else {
            $maxIdResult = $this->db->query("SELECT MAX(id) AS maxId FROM project_list");
            $maxId = $maxIdResult->fetch_assoc()['maxId'];
            $newId = max(10000, $maxId + 1);
        }
        $data .= ", id=$newId";

        // Insert into project_list
        $save = $this->db->query("INSERT INTO project_list SET $data");
        
        $insert_notifications = "INSERT INTO pm_notifications (PM_ID, Job_ID,team_id,Notification_Type) VALUES ($manager_id, $newId,$team_ids,3)";
        $this->db->query($insert_notifications);
        
        // Use the new ID for subsequent queries
        $id = $newId; 

        // Insert into market_post
        $task_ids_str = isset($task_ids) ? implode(',', $task_ids) : '';
        $insertpost = "INSERT INTO yasccoza_openlink_market.market_post
        (POST_ID, Title, CLIENT_ID, CLIENT_REP, Description, RFP_ID, SCORECARD_ID, USER_ID, Start_Date, EXPIRY, Created, ASSIGNED_TO, VERIFIED_BY, APPROVED, WORKTYPE, JOB_TYPE, updated, USES_EXPENSES, COMPANY, INDUSTRY_ID, OFFICE_ID) 
        VALUES 
        ('$id', '$name', '$CLIENT_ID', '$CLIENT_REP', '$description', 0, '$scorecard', $login_id, '$start_date', '$end_date', NOW(), '$manager_id', '$manager_id', 1, '$task_ids_str', '$JOB_TYPE', NOW(), 'NO', '$COMPANY', '$INDUSTRY_ID', '$OFFICE_ID')";
        
        $this->db->query($insertpost);

    } else {
        // --- EXISTING PROJECT (UPDATE) ---
        $save = $this->db->query("UPDATE project_list SET $data WHERE id = $id");

        // Update market_post
        $newtaskids = "";
        if(isset($task_ids)){
            $newtaskids = implode(',', $task_ids);
        }

        $updatepost = "UPDATE yasccoza_openlink_market.market_post 
                       SET Title = '$name', 
                           CLIENT_ID = '$CLIENT_ID', 
                           CLIENT_REP = '$CLIENT_REP', 
                           Description = '$description', 
                           SCORECARD_ID = '$scorecard', 
                           USER_ID = $login_id, 
                           Start_Date = '$start_date', 
                           EXPIRY = '$end_date', 
                           ASSIGNED_TO = '$manager_id', 
                           VERIFIED_BY = '$manager_id', 
                           WORKTYPE ='$newtaskids', 
                           JOB_TYPE = '$JOB_TYPE', 
                           updated = NOW(), 
                           COMPANY = '$COMPANY',
                           OFFICE_ID= '$OFFICE_ID'
                       WHERE POST_ID = '$id'";
        
        $this->db->query($updatepost);
    }

    // 3. FILE UPLOAD LOGIC (FIXED TO PREVENT ARRAY ERRORS)
  // 3. FILE UPLOAD LOGIC
            if($save){
                if (isset($_FILES['files'])) {
                    // Force arrays
                    $f_names = is_array($_FILES['files']['name']) ? $_FILES['files']['name'] : [$_FILES['files']['name']];
                    $f_tmps  = is_array($_FILES['files']['tmp_name']) ? $_FILES['files']['tmp_name'] : [$_FILES['files']['tmp_name']];
                    $f_errs  = is_array($_FILES['files']['error']) ? $_FILES['files']['error'] : [$_FILES['files']['error']];
        
                    $targetDir = '../TIMS/STORAGE/FILES/';
                    if (!is_dir($targetDir)) { mkdir($targetDir, 0777, true); }
        
                    for ($i = 0; $i < count($f_names); $i++) {
                        
                        // Check specifically for Size Errors (Error Code 1 or 2)
                        if ($f_errs[$i] == UPLOAD_ERR_INI_SIZE || $f_errs[$i] == UPLOAD_ERR_FORM_SIZE) {
                            echo "Error: File '{$f_names[$i]}' is too large for the server.<br>";
                            continue; // Skip this file and try the next one
                        }
        
                        // Standard Success Check
                        if ($f_errs[$i] === UPLOAD_ERR_OK && !empty($f_names[$i])) {
                            
                            $fileName = uniqid() . '_' . basename($f_names[$i]);
                            $targetPath = $targetDir . $fileName;
        
                            if (move_uploaded_file($f_tmps[$i], $targetPath)) {
                                $insert = "INSERT INTO yasccoza_openlink_market.rfp(url, USER_ID, POST_ID) 
                                           VALUES ('$fileName', '{$_SESSION['login_id']}', '$id')";
                                $this->db->query($insert);
                            }
                        }
                    }
                }
                $projectId = (int)$id;
                $teamId    = (int)$team_ids;
                
                // ❌ Remove this - it breaks ajax / responses
                // echo $projectId;
                
                /* -----------------------------
                   1) Get job data FIRST
                ------------------------------ */
                $Query = $this->db->query("
                    SELECT
                        pl.name AS jobname,
                        DATE_FORMAT(pl.start_date, '%d-%m-%Y') AS start_date_dmy,
                        DATE_FORMAT(pl.end_date, '%d-%m-%Y') AS end_date_dmy,
                        pl.JOB_TYPE,
                        pl.task_ids,
                        s.Title,
                        u.email AS manager_email,
                        c.company_name,
                        c.Email as COMPANY_EMAIL,
                        CONCAT(u.firstname, ' ', u.lastname) AS manager,
                        NOW() AS submitted_date,
                        (
                            SELECT ts1.team_name
                            FROM team_schedule ts1
                            WHERE ts1.team_id = pl.team_ids
                              AND ts1.team_name IS NOT NULL
                              AND ts1.team_name <> ''
                            LIMIT 1
                        ) AS team_name,
                        (
                            SELECT cr1.REP_NAME
                            FROM client_rep cr1
                            WHERE cr1.CLIENT_ID = pl.CLIENT_ID
                              AND cr1.REP_EMAIL IS NOT NULL
                              AND cr1.REP_EMAIL <> ''
                            LIMIT 1
                        ) AS REP_NAME,
                        (
                            SELECT cr2.REP_EMAIL
                            FROM client_rep cr2
                            WHERE cr2.CLIENT_ID = pl.CLIENT_ID
                              AND cr2.REP_EMAIL IS NOT NULL
                              AND cr2.REP_EMAIL <> ''
                            LIMIT 1
                        ) AS REP_EMAIL
                    FROM project_list pl
                    LEFT JOIN users u ON u.id = pl.manager_id
                    LEFT JOIN yasccoza_openlink_market.client c ON pl.CLIENT_ID = c.CLIENT_ID
                    LEFT JOIN yasccoza_openlink_market.scorecard s ON pl.scorecard = s.SCORECARD_ID
                    WHERE pl.id = $projectId
                    LIMIT 1
                ");
                
                $data = $Query ? $Query->fetch_assoc() : null;
                if (!$data) {
                    return 0;
                }
                
                $manager_email  = (string)$data['manager_email'];
                $manager_name   = (string)$data['manager'];
                $job_name       = (string)$data['jobname'];
                $start_date     = (string)$data['start_date_dmy'];
                $end_date       = (string)$data['end_date_dmy'];
                $team_name      = (string)$data['team_name'];
                $scorecard      = (string)$data['Title'];
                $jobtype        = (string)$data['JOB_TYPE'];
                $task_ids_csv   = isset($data['task_ids']) ? trim((string)$data['task_ids']) : '';
                $company_name   = (string)$data['company_name'];
                $rep_name       = isset($data['REP_NAME']) ? (string)$data['REP_NAME'] : '';
                $rep_email      = isset($data['REP_EMAIL']) ? (string)$data['REP_EMAIL'] : '';
                $company_email  = isset($data['COMPANY_EMAIL']) ? (string)$data['COMPANY_EMAIL'] : '';
                $date_submitted = (string)$data['submitted_date'];

                $worktypes = 'N/A';
                if ($task_ids_csv !== '' && $task_ids_csv !== '0') {
                    $safeTaskIds = preg_replace('/[^0-9,]/', '', $task_ids_csv);
                    if ($safeTaskIds !== '') {
                        $workTypeQuery = $this->db->query("
                            SELECT GROUP_CONCAT(DISTINCT task_name ORDER BY task_name SEPARATOR ', ') AS worktypes
                            FROM task_list
                            WHERE FIND_IN_SET(id, '{$safeTaskIds}')
                        ");
                        if ($workTypeQuery && $workTypeQuery->num_rows > 0) {
                            $workTypeRow = $workTypeQuery->fetch_assoc();
                            if (!empty($workTypeRow['worktypes'])) {
                                $worktypes = (string)$workTypeRow['worktypes'];
                            }
                        }
                    }
                }

                $clientRepDisplay = 'N/A';
                if (trim($rep_name) !== '' && trim($rep_email) !== '') {
                    $clientRepDisplay = $rep_name . ' (' . $rep_email . ')';
                } elseif (trim($rep_name) !== '') {
                    $clientRepDisplay = $rep_name;
                } elseif (trim($rep_email) !== '') {
                    $clientRepDisplay = $rep_email;
                }
                
                /* -----------------------------
                   2) Build file section
                ------------------------------ */
                $fileQuery = $this->db->query("
                    SELECT url, created
                    FROM yasccoza_openlink_market.rfp
                    WHERE POST_ID = $projectId
                ");
                
                $fileSection = "";
                if ($fileQuery && $fileQuery->num_rows > 0) {
                    while ($file = $fileQuery->fetch_assoc()) {
                        $file_url  = "https://openlinks.co.za/TIMS/STORAGE/FILES/" . $file['url'];
                        $file_date = date("d M Y", strtotime($file['created']));
                
                        $fileSection .= "
                        <div style='margin-top:10px;'>
                            <a href='$file_url' target='_blank'
                               style='text-decoration:none;color:#0f1f3d;display:inline-block;'>
                                <img src='https://openlinks.co.za/TIMS/Images/PDF_file_icon.png'
                                     style='vertical-align:middle;height:40px;width:40px;margin-right:10px;'>
                                <span style='font-size:14px;'>View Uploaded Document</span><br>
                                <small style='color:#666;'>Uploaded: $file_date</small>
                            </a>
                        </div>";
                    }
                } else {
                    $fileSection = "
                    <div style='margin-top:10px;color:#999;font-style:italic;'>
                        No documents attached
                    </div>";
                }

                $buildProjectMessage = static function (string $recipientName, string $extraNote = '') use (
                    $projectId,
                    $job_name,
                    $start_date,
                    $end_date,
                    $company_name,
                    $clientRepDisplay,
                    $scorecard,
                    $jobtype,
                    $worktypes,
                    $team_name,
                    $manager_name,
                    $date_submitted,
                    $fileSection
                ): string {
                    $recipientName = htmlspecialchars(trim($recipientName) !== '' ? trim($recipientName) : 'Team Member', ENT_QUOTES, 'UTF-8');
                    $job_name = htmlspecialchars($job_name, ENT_QUOTES, 'UTF-8');
                    $start_date = htmlspecialchars($start_date, ENT_QUOTES, 'UTF-8');
                    $end_date = htmlspecialchars($end_date, ENT_QUOTES, 'UTF-8');
                    $company_name = htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8');
                    $clientRepDisplay = htmlspecialchars($clientRepDisplay, ENT_QUOTES, 'UTF-8');
                    $scorecard = htmlspecialchars($scorecard, ENT_QUOTES, 'UTF-8');
                    $jobtype = htmlspecialchars($jobtype, ENT_QUOTES, 'UTF-8');
                    $worktypes = htmlspecialchars($worktypes, ENT_QUOTES, 'UTF-8');
                    $team_name = htmlspecialchars($team_name, ENT_QUOTES, 'UTF-8');
                    $manager_name = htmlspecialchars($manager_name, ENT_QUOTES, 'UTF-8');
                    $date_submitted = htmlspecialchars($date_submitted, ENT_QUOTES, 'UTF-8');
                    $extraNoteHtml = trim($extraNote) !== ''
                        ? '<p>' . nl2br(htmlspecialchars(trim($extraNote), ENT_QUOTES, 'UTF-8')) . '</p>'
                        : '';

                    return "
                    <!DOCTYPE html>
                    <html><head><meta charset='UTF-8'><title>New Job Created</title></head>
                    <body style='margin:0;padding:0;background-color:#f4f6f8;'>
                    <table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'><tr><td align='center'>
                    <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>
                    <tr><td style='padding:20px;background:#0f1f3d;color:white;'>
                    <table width='100%'><tr>
                    <td align='left'><img src='https://openlinks.co.za/TMS/Image_Redone.png' height='80' width='200'></td>
                    <td align='right' style='font-size:13px;line-height:18px;'>
                    <b>OpenLinks Corporations (Pty) Ltd</b><br>314 Cape Road, Newton Park<br>Port Elizabeth, Eastern Cape 6070
                    </td></tr></table></td></tr>

                    <tr><td style='padding:30px;color:#333;font-size:15px;'>
                    <p>Dear <b>{$recipientName}</b>,</p>
                    <p>A new job has been created with the following details:</p>

                    <table width='100%' cellpadding='8' cellspacing='0' style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
                    <tr><td style='background:#f0f3f7;width:35%;'><b>Job ID</b></td><td>{$projectId}</td></tr>
                    <tr><td style='background:#f0f3f7;'><b>Job Name</b></td><td>{$job_name}</td></tr>
                    <tr><td style='background:#f0f3f7;'><b>Start Date</b></td><td>{$start_date}</td></tr>
                    <tr><td style='background:#f0f3f7;'><b>End Date</b></td><td>{$end_date}</td></tr>
                    <tr><td style='background:#f0f3f7;'><b>Client</b></td><td>{$company_name}</td></tr>
                    <tr><td style='background:#f0f3f7;'><b>Client Rep</b></td><td>{$clientRepDisplay}</td></tr>
                    <tr><td style='background:#f0f3f7;'><b>Scorecard</b></td><td>{$scorecard}</td></tr>
                    <tr><td style='background:#f0f3f7;'><b>Job Type</b></td><td>{$jobtype}</td></tr>
                    <tr><td style='background:#f0f3f7;'><b>Work Types</b></td><td>{$worktypes}</td></tr>
                    <tr><td style='background:#f0f3f7;'><b>Team</b></td><td>{$team_name}</td></tr>
                    <tr><td style='background:#f0f3f7;'><b>Manager</b></td><td>{$manager_name}</td></tr>
                    <tr><td style='background:#f0f3f7;'><b>Date Created</b></td><td>{$date_submitted}</td></tr>
                    </table>

                    {$extraNoteHtml}

                    <div style='margin-top:25px;padding:15px;background:#f9fafc;border:1px solid #e1e5eb;border-radius:6px;'>
                    <b>Attached Document:</b><br><br>{$fileSection}</div>

                    <div style='text-align:center;margin:35px 0;'>
                    <a href='https://openlinks.co.za/index.php?page=productivity_pipeline'
                    style='background:#0f1f3d;color:#ffffff;padding:14px 30px;text-decoration:none;border-radius:5px;font-size:15px;font-weight:bold;'>
                    View Job Pipeline</a></div>

                    <p>Kind regards,<br><b>OpenLinks Operations System</b></p>
                    </td></tr>

                    <tr><td style='background:#f0f3f7;padding:15px;text-align:center;font-size:12px;'>
                    <small>Automated Notification - Do not reply</small></td></tr>
                    </table></td></tr></table></body></html>";
                };
                
                /* -----------------------------
                   3) Get members & SEND FOR EACH MEMBER ✅
                ------------------------------ */
                $stmt = $this->db->query("
                    SELECT DISTINCT
                        mem.email AS member_email,
                        CONCAT(mem.firstname, ' ', mem.lastname) AS member_name
                    FROM team_schedule ts
                    LEFT JOIN users mem ON mem.id = ts.team_members
                    WHERE ts.team_id = $teamId
                      AND mem.email IS NOT NULL
                      AND mem.email <> ''
                ");
                
                $subject = "A Job Has Been Created : Job ID $projectId";
                
                $sent = []; // avoid duplicate emails (same email twice)
                $repClientContact = trim($rep_name) !== '' ? $rep_name : 'the client representative';
                $repClientNote = "We confirm that the current period's work order, placed by {$repClientContact}, has been successfully activated.\n\nFor traceability and reference purposes, the work order is registered under Job ID - {$projectId}.\nIt is currently positioned within the pipeline of our dedicated production teams and is progressing toward resource allocation. Once resources are assigned, the work order will transition into the formal production phase.\n\nPlease feel free to reference the Job ID in any related correspondence.";
                
                // ✅ SEND TO EACH MEMBER
                if ($stmt && $stmt->num_rows > 0) {
                    while ($m = $stmt->fetch_assoc()) {
                
                        $member_email = strtolower(trim((string)$m['member_email']));
                        $member_name  = (string)$m['member_name'];
                
                        if ($member_email === '' || isset($sent[$member_email])) continue;
                
                        $message_member = $buildProjectMessage(
                            $member_name,
                            'You can go into the system and assign yourself to the relevant task or inform your manager to assign you.'
                        );
                
                        $this->queueEmailJob($member_email, $subject, $message_member);
                        $sent[$member_email] = true;
                    }
                }
                
                /* -----------------------------
                   4) Send to manager (once)
                ------------------------------ */
                $mgrEmail = strtolower(trim($manager_email));
                if ($mgrEmail !== '' && !isset($sent[$mgrEmail])) {
                    $message_manager = $buildProjectMessage(
                        $manager_name,
                        'Please log in to review the job and assign work to the relevant team members.'
                    );
                
                    $this->queueEmailJob($mgrEmail, $subject, $message_manager);
                    $sent[$mgrEmail] = true;
                }

                /* -----------------------------
                   5) Send to client rep + company (once each)
                ------------------------------ */
                $repEmail = strtolower(trim($rep_email));
                if ($repEmail !== '' && !isset($sent[$repEmail])) {
                    $repNameSafe = trim($rep_name) !== '' ? $rep_name : 'Client Representative';
                    $message_rep = $buildProjectMessage(
                        $repNameSafe,
                        $repClientNote
                    );
                    $this->queueEmailJob($repEmail, $subject, $message_rep);
                    $sent[$repEmail] = true;
                }

                $companyEmail = strtolower(trim($company_email));
                if ($companyEmail !== '' && !isset($sent[$companyEmail])) {
                    $companyRecipient = trim($company_name) !== '' ? $company_name : 'Client';
                    $message_company = $buildProjectMessage(
                        $companyRecipient,
                        'We confirm that the current period’s work order, placed by [Representative Name], has been successfully activated.

                                For traceability and reference purposes, the work order is registered under Job ID – [Insert ID]. 
                                It is currently positioned within the pipeline of our dedicated production teams and is progressing toward resource allocation. Once resources are assigned, the work order will transition into the formal production phase.

                                Please feel free to reference the Job ID in any related correspondence.'
                    );
                    $message_company = $buildProjectMessage(
                        $companyRecipient,
                        $repClientNote
                    );
                    $this->queueEmailJob($companyEmail, $subject, $message_company);
                    $sent[$companyEmail] = true;
                }

                return 1;
            }
}



function printReportData($startWeek, $endWeek) {
    include('db_connect.php');

    // Excel file name for download
    $fileName = "Jobs_Period_report_" . date('Y-m-d') . ".xls";

    // Column names
    $fields = array(
        'period',
        'start_week',
        'end_week',
        'Job_Status_Completion',
        'Due_This_Period',
        'Completed_This_Period',
        'Created_This_Period',
        'date_created',
        'start_date',
        'end_date',
        'Date_Job_Finished',
        'Job_ID',
        'Job_Name',
        'JOB_Manager',
        'JOB_Type',
        'CLIENT',
        'scorecard',
        'Assigned_Resources',
        'status',
        'Pre_Post_Section',
        'Date_Post_Created',
        'Date_Post_Verified',
        'Days_taken_to_Verify',
    );

    // SQL query to fetch data from the database
    $sql = "SELECT
        wwp.period,
        wwp.start_week,
        wwp.end_week,
        pl.name as Job_Name,
        pl.id as Job_ID,
        CONCAT(u.firstname, ' ', u.lastname) AS Job_Manager,
        COALESCE(c.company_name, smme.Legal_name) as CLIENT,
        pl.scorecard,
        pl.status,
        pl.assigned as Assigned_Resources,
        pl.Date_Post_Created,
        pl.Date_Post_Verified,
        pl.JOB_TYPE as JOB_Type,
        MIN(pl.start_date) AS start_date,
        MAX(pl.end_date) AS end_date,
        pl.date_created,
        CASE
            WHEN pl.end_date >= wwp.start_week AND pl.end_date <= wwp.end_week THEN 'yes'
            ELSE 'no'
        END AS Due_This_Period,
        CASE
            WHEN pl.Job_Done >= wwp.start_week AND pl.Job_Done <= wwp.end_week THEN 'yes'
            ELSE 'no'
        END AS Completed_This_Period,
        CASE
            WHEN pl.Job_Done >= wwp.start_week AND pl.Job_Done <= wwp.end_week THEN pl.Job_Done
            ELSE NULL
        END AS Date_Job_Finished,
        CASE
            WHEN pl.date_created >= wwp.start_week AND pl.date_created <= wwp.end_week THEN 'yes'
            ELSE 'no'
        END AS Created_This_Period
    FROM
        project_list pl
    JOIN
        working_week_periods wwp
    ON
        (pl.date_created >= wwp.start_week AND pl.date_created <= wwp.end_week)
        OR
        (pl.end_date >= wwp.start_week AND pl.end_date <= wwp.end_week)
        OR
        (pl.Job_Done >= wwp.start_week AND pl.Job_Done <= wwp.end_week)
    LEFT JOIN 
        users u ON pl.manager_id = u.id
    LEFT JOIN 
        yasccoza_openlink_market.client c ON pl.CLIENT_ID = c.CLIENT_ID
    LEFT JOIN 
        yasccoza_openlink_smmes.register smme ON pl.CLIENT_ID = smme.SMME_ID
    WHERE
        pl.date_created >= '$startWeek' AND pl.date_created <= '$endWeek'
    GROUP BY
        wwp.start_week, wwp.end_week, wwp.period, pl.name, pl.scorecard, pl.status, pl.manager_id, pl.id
    ORDER BY
        wwp.start_week, wwp.end_week, wwp.period;
    ";

    // Prepare the data as tab-separated values
    $excelData = implode("\t", array_values($fields)) . "\n";
    $result = $conn->query($sql);

    // Check if there are any projects found
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Determine the job status based on conditions
            $jobStatus = ($row['Date_Job_Finished'] > $row['end_date']) ? "finished overtime" : (empty($row['Date_Job_Finished']) ? "still in progress" : "finished on time");

            $created = ($row['Date_Post_Created'] === null) ? "created on TMS" : $row['Date_Post_Created'];
            $verified = ($row['Date_Post_Verified'] === null) ? "created on TMS" : $row['Date_Post_Verified'];
            $assigned = ($row['Assigned_Resources'] == 0) ? "Not Assigned" : "Yes";

            $time_created = strtotime($row['Date_Post_Created']);
            $time_verified = strtotime($row['Date_Post_Verified']);
            $time_difference_seconds = $time_verified - $time_created;

            $time_taken = ($time_difference_seconds < 0) ? "Not yet verified" : floor($time_difference_seconds / (60 * 60 * 24));

            $Prepost = "";

            $lineData = array(
                $row['period'],
                $row['start_week'],
                $row['end_week'],
                $jobStatus,
                $row['Due_This_Period'],
                $row['Completed_This_Period'],
                $row['Created_This_Period'],
                $row['date_created'],
                $row['start_date'],
                $row['end_date'],
                $row['Date_Job_Finished'],
                $row['Job_ID'],
                $row['Job_Name'],
                $row['Job_Manager'],
                $row['JOB_Type'],
                $row['CLIENT'],
                $row['scorecard'],
                $assigned,
                $row['status'],
                $Prepost,
                $created,
                $verified,
                $time_taken,
            );

            $excelData .= implode("\t", array_values($lineData)) . "\n";
        }
    }

    // Set headers for Excel download
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$fileName\"");

    // Output the Excel data
    echo $excelData;

    // Terminate script execution
    exit();
}


function printResourcesData($start, $end) {
    include('db_connect.php');  // Ensure this file contains correct DB connection settings

    // Append time to end date to include the whole day
    $end .= ' 23:59:59';

    // Excel file name for download
    $fileName = "Resources_Period_report_" . date('Y-m-d') . ".xls";

    // Column names
    $fields = array(
        'period',
        'start_week',
        'end_week',
        'Done',
        'Done_On_TIME',
        'Member',
        'Project_Id',
        'Job_Name',
        'Client',
        'Project_Manager',
        'Work_Type',
        'Activity',
        'Activity_Duration',
        'scorecard',
        'Job_Start_Date',
        'start_date_assigned',
        'Actual_Done_Date',
        'Estimated_Completion_Date',  // Added Estimated_Completion_Date
    );

    // SQL query
    $sql = "SELECT
                subquery.period,
                subquery.start_week,
                subquery.end_week,
                subquery.Done,
                subquery.Done_On_TIME,
                subquery.Member,
                subquery.Project_Id,
                subquery.Job_Name,
                subquery.Client,
                subquery.Project_Manager,
                subquery.Work_Type,
                subquery.Activity,
                subquery.Activity_Duration,
                subquery.scorecard,
                subquery.Job_Start_Date,
                subquery.start_date_assigned,
                subquery.Actual_Done_Date,
                subquery.Estimated_Completion_Date
            FROM (
                SELECT
                    CONCAT(u.firstname, ' ', u.lastname) AS Member,
                    pl.name AS Job_Name,
                    pl.id AS Project_Id,
                    c.company_name AS Client,
                    wwp.period,
                    tl.task_name AS Work_Type,
                    up.name AS Activity,
                    pl.scorecard,
                    pl.date_created AS Job_Start_Date,
                    ad.start_date AS start_date_assigned,
                    ad.Done_Date AS Actual_Done_Date,
                    CONCAT(pm.firstname, ' ', pm.lastname) AS Project_Manager,
                    ad.task_id,
                    ad.activity_id,
                    wwp.start_week,
                    wwp.end_week,
                    up.duration AS Activity_Duration,
                    CASE
                        WHEN ad.Done_Date >= wwp.start_week AND ad.Done_Date <= wwp.end_week THEN 'yes'
                        ELSE 'no'
                    END AS Done,
                    CASE
                        WHEN ad.Done_Date <= ad.end_date THEN 'yes'
                        ELSE 'no'
                    END AS Done_On_TIME,
                    CASE
                        WHEN ad.Done_Date IS NOT NULL THEN DATE_ADD(ad.Done_Date, INTERVAL up.duration DAY)
                        ELSE NULL
                    END AS Estimated_Completion_Date
                FROM
                    assigned_duties ad
                LEFT JOIN
                    users u ON ad.user_id = u.id
                LEFT JOIN
                    project_list pl ON ad.project_id = pl.id
                LEFT JOIN
                    task_list tl ON ad.task_id = tl.id
                LEFT JOIN
                    user_productivity up ON ad.activity_id = up.id
                LEFT JOIN
                    users pm ON ad.manager_id = pm.id
                LEFT JOIN
                    yasccoza_openlink_market.client c ON ad.CLIENT_ID = c.CLIENT_ID
                JOIN
                    working_week_periods wwp ON (ad.Done_Date >= wwp.start_week AND ad.Done_Date <= wwp.end_week)
                    WHERE
    				 ad.Done_Date >= '$start' AND ad.Done_Date <= '$end'
                GROUP BY
                    wwp.period, u.id, Member, Job_Name, Project_Id, Client, Work_Type, Activity, Activity_Duration, scorecard, Job_Start_Date, start_date_assigned, Actual_Done_Date, Project_Manager, task_id, activity_id
            ) AS subquery
            WHERE
                subquery.Done = 'yes'
            ORDER BY
                subquery.period";

    // Prepare the data as tab-separated values
    $excelData = implode("\t", $fields) . "\n";
    $result = $conn->query($sql);

    // Check if there are any records found
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $lineData = array(
                $row['period'],
                $row['start_week'],
                $row['end_week'],
                $row['Done'],
                $row['Done_On_TIME'],
                $row['Member'],
                $row['Project_Id'],
                $row['Job_Name'],
                $row['Client'],
                $row['Project_Manager'],
                $row['Work_Type'],
                $row['Activity'],
                $row['Activity_Duration'],
                $row['scorecard'],
                $row['Job_Start_Date'],
                $row['start_date_assigned'],
                $row['Actual_Done_Date'],
                $row['Estimated_Completion_Date'],
            );
            $excelData .= implode("\t", $lineData) . "\n";
        }
    } else {
        $excelData .= "No data found\n";
    }

    // Set headers for Excel download
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$fileName\"");

    // Output the Excel data
    echo $excelData;

    // Close the database connection
    $conn->close();

    // Terminate script execution
    exit();
}



function save_task_new(){
    extract($_POST);
    $data = "";

    foreach ($_POST as $k => $v) {
        // ✅ exclude csrf_token too
        if (!in_array($k, array('id', 'file', 'csrf_token')) && !is_numeric($k) && $v !== '') {

            if ($k == 'description' || $k == 'instructions')
                $v = htmlentities(str_replace("'", "&#x2019;", $v));

            if (empty($data)) {
                $data .= " $k='$v' ";
            } else {
                $data .= ", $k='$v' ";
            }
        }
    }

    // ✅ prevent empty insert
    if (trim($data) === '') {
        return 0;
    }

    // File upload handling
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $targetDir = 'work_type_docs/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = uniqid() . '_' . basename($_FILES['file']['name']);
        $targetPath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            $data .= ", file_path='$fileName'";
        } else {
            return 0;
        }
    }

    if (empty($id)) {
        $save = $this->db->query("INSERT INTO task_list SET $data");
    } else {
        $save = $this->db->query("UPDATE task_list SET $data WHERE id = $id");
    }

    if ($save) {
        return 1;
    }
    return 0; // ✅ so ajax doesn't get blank
}


	
	function delete_project(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM project_list where id = $id");
		$this->db->query("DELETE FROM assigned_duties where project_id = $id");
		$this->db->query("DELETE FROM yasccoza_openlink_market.rfp where POST_ID = $id");
		if($delete){
			return 1;
		}else{
		    return 0;
		}
	}
	function delete_job_type(){
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) return 0;

    $stmt = $this->db->prepare("DELETE FROM job_type WHERE id = ?");
    if(!$stmt) return 0;

    $stmt->bind_param("i", $id);
    $ok = $stmt->execute();
            $stmt->close();
        
            return $ok ? 1 : 0;
        }
        
        
	function save_task(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id, creator_id')) && !is_numeric($k)){
				if($k == 'description')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO task_list set $data");
		}else{
			$save = $this->db->query("UPDATE task_list set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	
function save_job_new() {
    extract($_POST);
    $data = "";

    foreach ($_POST as $k => $v) {
        // FIX: split excluded keys properly
        if (!in_array($k, array('id', 'creator_id','csrf_token')) && !is_numeric($k)) {
            if ($k == 'description') {
                $v = htmlentities(str_replace("'", "&#x2019;", $v));
            }
            $data .= (empty($data)) ? " $k='$v' " : ", $k='$v' ";
        }
    }

    if (empty($id)) {
         if (!empty($creator_id)) {
            $data .= ", creator_id='$creator_id'";
        }
        $query = "INSERT INTO job_type SET $data";
    } else {
        $query = "UPDATE job_type SET $data WHERE id = $id";
    }

    $save = $this->db->query($query);

    if ($save) {
        return 1;
    } else {
        // Debug output
        echo "SQL Error: " . $this->db->error . "\n";
        echo "Query: " . $query;
        return 0;
    }
}

	
	
		function save_link(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('Admin_ID')) && !is_numeric($k)){
				if($k == 'description')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO smme_links set $data");
		}
		if($save){
			return 1;
		}
	}
	function delete_task(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM task_list where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_progress(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','csrf_token')) && !is_numeric($k)){
				if($k == 'comment')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		
	
		// echo "INSERT INTO user_productivity set $data"; exit;
		if(empty($id)){
			
			$save = $this->db->query("INSERT INTO user_productivity set $data");
		}else{
			$save = $this->db->query("UPDATE user_productivity set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function delete_progress(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM user_productivity where id = $id");
		if($delete){
			return 1;
		}
	}
	function get_report(){
		extract($_POST);
		$data = array();
		$get = $this->db->query("SELECT t.*,p.name as ticket_for FROM ticket_list t inner join pricing p on p.id = t.pricing_id where date(t.date_created) between '$date_from' and '$date_to' order by unix_timestamp(t.date_created) desc ");
		while($row= $get->fetch_assoc()){
			$row['date_created'] = date("M d, Y",strtotime($row['date_created']));
			$row['name'] = ucwords($row['name']);
			$row['adult_price'] = number_format($row['adult_price'],2);
			$row['child_price'] = number_format($row['child_price'],2);
			$row['amount'] = number_format($row['amount'],2);
			$data[]=$row;
		}
		return json_encode($data);

	}
}
