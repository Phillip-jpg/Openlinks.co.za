<?php
// ajax.php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

/* ---------------------------
   Security Headers
---------------------------- */
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Type: text/plain; charset=UTF-8');

date_default_timezone_set("Asia/Manila");

/* ---------------------------
   Bootstrap
---------------------------- */
require_once 'admin_class.php';
require_once 'print_function.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$crud = new Action();

/* ---------------------------
   Action allowlist
---------------------------- */
$action = $_GET['action'] ?? '';

$allowedActions = [
    'login',
    'login2',
    'logout',
    'signup',

    'save_assign',
    'save_configure',
    'save_user',
    'save_client',
    'save_rep',
    'update_user',
    'delete_user',
    'delete_client',
    'delete_rep',
    'delete_discount',
    

    'save_project',
    'save_task_new',
    'save_job_new',
    'delete_project',
    'delete_job_type',
    'save_task',
    'save_link',
    'delete_task',
    'save_progress',
    'delete_progress',
    'get_report',

    // print actions gated
    'print'
];

if (!in_array($action, $allowedActions, true)) {
    http_response_code(400);
    echo '0';
    exit;
}

/* ---------------------------
   POST-only enforcement
---------------------------- */
$postOnly = [
    'login','login2','logout','signup',
    'save_assign','save_configure','save_user','save_client','save_rep',
    'update_user','delete_user','delete_client','delete_rep','delete_discount',
    'save_project','save_task_new','save_job_new','delete_project','delete_job_type',
    'save_task','save_link','delete_task','save_progress','delete_progress',
    'get_report','print'
];

$isPost = ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';

if (in_array($action, $postOnly, true) && !$isPost) {
    http_response_code(405);
    echo '0';
    exit;
}

/* ---------------------------
   CSRF protection (POST)
---------------------------- */
if ($isPost) {
    $csrf = $_POST['csrf_token'] ?? '';
    if (
        empty($_SESSION['csrf_token']) ||
        empty($csrf) ||
        !hash_equals($_SESSION['csrf_token'], $csrf)
    ) {
        http_response_code(403);
        echo 'csrf';
        exit;
    }
}

/* ---------------------------
   Auth gate
   (only login/signup allowed without session)
---------------------------- */
$publicActions = ['login','login2','signup'];
if (!in_array($action, $publicActions, true) && empty($_SESSION['login_id'])) {
    http_response_code(401);
    echo 'unauthorized';
    exit;
}

/* ---------------------------
   Dispatch
---------------------------- */
switch ($action) {

    case 'login':
        echo $crud->login();
        exit;

    case 'login2':
        echo $crud->login2();
        exit;

    case 'logout':
        echo $crud->logout(); // should destroy session + redirect
        exit;

    case 'signup':
        echo $crud->signup();
        exit;

    case 'save_assign':
        echo $crud->save_assign();
        exit;

    case 'save_configure':
        echo $crud->save_configure();
        exit;

    case 'save_user':
        echo $crud->save_user();
        exit;

    case 'save_client':
        echo $crud->save_client();
        exit;

    case 'save_rep':
        echo $crud->save_rep();
        exit;

    case 'update_user':
        echo $crud->update_user();
        exit;

    case 'delete_user':
        echo $crud->delete_user();
        exit;

    case 'delete_client':
        echo $crud->delete_client();
        exit;

    case 'delete_rep':
        echo $crud->delete_rep();
        exit;

    case 'delete_discount':
        echo $crud->delete_discount();
        exit;

    case 'save_project':
        echo $crud->save_project();
        exit;

    case 'save_task_new':
        echo $crud->save_task_new();
        exit;

    case 'save_job_new':
        echo $crud->save_job_new();
        exit;

    case 'delete_project':
        echo $crud->delete_project();
        exit;

    case 'delete_job_type':
        echo $crud->delete_job_type();
        exit;

    case 'save_task':
        echo $crud->save_task();
        exit;

    case 'save_link':
        echo $crud->save_link();
        exit;

    case 'delete_task':
        echo $crud->delete_task();
        exit;

    case 'save_progress':
        echo $crud->save_progress();
        exit;

    case 'delete_progress':
        echo $crud->delete_progress();
        exit;

    case 'get_report':
        echo $crud->get_report();
        exit;

    /* ---------------------------
       PRINT ACTIONS (explicit)
    ---------------------------- */
    case 'print':

        if (isset($_POST["print_admins"])) {
            $crud->printAdminList();
            exit;
        }

        if (isset($_POST["print_jobs_to_smmes"])) {
            $crud->printSentJobs();
            exit;
        }

        if (isset($_POST["print_jobs_responses"])) {
            $crud->printResponses(
                $_POST['start_week'] ?? '',
                $_POST['end_week'] ?? ''
            );
            exit;
        }

        if (isset($_POST["print_claims_report"])) {
            $crud->printClaims(
                $_POST['start_week'] ?? '',
                $_POST['end_week'] ?? ''
            );
            exit;
        }

        if (isset($_POST["print_jobs_not_responses"])) {
            $crud->printResponsesnot(
                $_POST['start_week'] ?? '',
                $_POST['end_week'] ?? ''
            );
            exit;
        }

        if (isset($_POST["print_report"])) {
            $crud->printReportData(
                $_POST['start_week'] ?? '',
                $_POST['end_week'] ?? ''
            );
            exit;
        }

        if (isset($_POST["print_resources_report"])) {
            $crud->printResourcesData(
                $_POST['start_week'] ?? '',
                $_POST['end_week'] ?? ''
            );
            exit;
        }

        http_response_code(400);
        echo '0';
        exit;
}
