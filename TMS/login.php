<?php
// login.php
declare(strict_types=1);

// ---------------------------
// Security headers (basic)
// ---------------------------
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

// ---------------------------
// Secure session config
// ---------------------------
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', '1');
}

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'httponly' => true,
    'samesite' => 'Lax',
]);

session_start();

require_once('./db_connect.php');

// Load system settings safely
$system = $conn->query("SELECT * FROM system_settings LIMIT 1");
if ($system) {
    $systemRow = $system->fetch_assoc();
    if ($systemRow) {
        foreach ($systemRow as $k => $v) {
            $_SESSION['system'][$k] = $v;
        }
    }
}

// If already logged in
if (isset($_SESSION['login_id'])) {
    header("Location: index.php?page=home");
    exit;
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../Images/favicon.ico">
    <style>
        body { background-color: #032033 !important; font-family: 'Arial', sans-serif; }
        .login-box { width: 400px; margin: 7% auto; }
        .login-card-body {
            background-color: #ffffff; border-radius: 15px; padding: 30px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease-in-out;
        }
        .login-card-body:hover { transform: scale(1.02); }
        .login-logo a {
            font-size: 36px; text-align: center; background-color: #032033; color: #ffffff !important;
            padding: 15px; display: block; font-weight: bold; border: 2px solid white !important;
            border-radius: 10px; transition: all 0.3s ease-in-out;
        }
        .login-logo a.morphis:hover {
            background-color: #ffffff; color: #032033 !important; border-radius: 15px;
            transform: scale(1.05); box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
        }
        .input-group { margin-bottom: 20px; }
        .form-control { border-radius: 10px; border: 1px solid #ccc; background-color: #f9f9f9 !important; }
        .input-group-text { background-color: #f9f9f9; border: none; }
        .btn-primary {
            background-color: #1f8ef1; border: none; padding: 10px 20px; font-size: 16px;
            font-weight: bold; transition: all 0.3s ease-in-out; width: 100%; border-radius: 10px;
        }
        .btn-primary:hover { background-color: #0361A1; color: white; }
        .btn-primary:focus { outline: none; box-shadow: none; }
        .btn-primary:active { background-color: #034c72; }
        .fas.fa-eye, .fas.fa-eye-slash { color: #1f8ef1; cursor: pointer; }
        .login-logo img { transition: transform 0.3s ease-in-out; }
        .login-logo img:hover { transform: scale(1.1); }
        .alert-danger {
            color: #721c24; background-color: #f8d7da; border-color: #f5c6cb;
            padding: 10px; margin-bottom: 15px; border-radius: 5px;
        }
        @media screen and (max-width: 576px) {
            .login-box { width: 100%; padding: 0 20px; }
            .login-logo a { font-size: 28px; }
            .login-card-body { padding: 15px; }
        }
    </style>
</head>

<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="#" class="text-white morphis"><b><?php echo htmlspecialchars($_SESSION['system']['name'] ?? 'System'); ?></b></a>
        <a href="../../index.php">
            <img src="opl_logo.png" alt="Logo" width="100%" height="250px">
        </a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
           <form action="" id="login-form" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            
                <div class="input-group mb-3">
                    <input type="email" class="form-control" name="email" required placeholder="Email">
                    <div class="input-group-append"><div class="input-group-text"></div></div>
                </div>
            
                <div class="input-group mb-3">
                    <input type="password" class="form-control" id="passwordInput" name="password" required placeholder="Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-eye" id="togglePassword"></span>
                        </div>
                    </div>
                </div>
            
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Sign In</button>
                    </div>
                </div>

        </div>
    </div>
</div>

<script>
$(function () {
  $('#login-form').on('submit', function (e) {
    e.preventDefault();

    if (typeof start_load === "function") start_load();

    const $form = $(this);
    $form.find('.alert').remove();

    $.ajax({
      url: 'ajax.php?action=login',
      method: 'POST',
      data: $form.serialize(),
      success: function (resp) {
        resp = String(resp).trim();
        console.log('LOGIN RESPONSE:', resp);

        if (resp === "1") {
          window.location.href = 'index.php?page=home';
          return;
        }

        let msg = 'Invalid email or password.';
        if (resp === "3") msg = 'Too many attempts. Please wait and try again.';
        else if (resp !== "2") msg = 'Something went wrong. Please try again.';

        $form.prepend(
          '<div class="alert alert-danger">' + msg + '</div>'
        );

        if (typeof end_load === "function") end_load();
      },
      error: function (xhr, status, error) {
        console.error('AJAX ERROR:', status, error);

        $form.prepend(
          '<div class="alert alert-danger">Server error. Please refresh and try again.</div>'
        );

        if (typeof end_load === "function") end_load();
      }
    });
  });
});


// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordInput = document.getElementById('passwordInput');
    const icon = this;

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
</script>

<?php include 'footer.php'; ?>
</body>
</html>
