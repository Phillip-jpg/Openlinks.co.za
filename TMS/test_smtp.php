<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // SMTP settings
    $mail->isSMTP();
    $mail->Host       = 'mail.openlinks.co.za';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'system@openlinks.co.za';
    $mail->Password   = '50Banbury!';

    // Use TLS on port 587 (NOT SSL/465)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Debug output
    $mail->SMTPDebug = 3;
    $mail->Debugoutput = 'html';

    // Recipients
    $mail->setFrom('system@openlinks.co.za', 'System Mailer');
    $mail->addAddress('phillipsibanda711@gmail.com');

    // Content
    $mail->isHTML(true);
    $mail->Subject = "SMTP Test Email";
    $mail->Body    = "<h3>SMTP is working over TLS (587)!</h3>";

    $mail->send();
    echo "Email sent successfully!";
} catch (Exception $e) {
    echo "Error: " . $mail->ErrorInfo;
}
?>
