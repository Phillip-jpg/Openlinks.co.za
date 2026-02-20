<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function buildConfiguredMailer()
{
    $mail = new PHPMailer(true);

    // SMTP CONFIG (Host Africa / cPanel)
    $mail->isSMTP();
    $mail->Host       = 'mail.openlinks.co.za';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'system@openlinks.co.za';
    $mail->Password   = '50Banbury!'; // <-- real password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->SMTPKeepAlive = true; // Reuse connection for multiple recipients in same request.

    // Email headers
    $mail->setFrom('system@openlinks.co.za', 'OpenLinks Operations System');
    $mail->isHTML(true);

    return $mail;
}

function sendEmailNotification($to, $subject, $htmlMessage)
{
    static $mail = null;

    try {
        if ($mail === null) {
            $mail = buildConfiguredMailer();
        }

        // Reset recipient/message state before each send.
        $mail->clearAddresses();
        $mail->clearCCs();
        $mail->clearBCCs();
        $mail->clearReplyTos();
        $mail->clearAttachments();

        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body    = $htmlMessage;

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);

        // Reset the cached mailer so next call re-connects cleanly.
        if ($mail !== null) {
            try {
                $mail->smtpClose();
            } catch (Exception $closeEx) {
                // Ignore close failures.
            }
            $mail = null;
        }
        return false;
    }
}
?>
