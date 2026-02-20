<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$to = "s220888914@mandela.ac.za";
$subject = "PHP mail() Test";
$message = "This is a test email sent using PHP mail()";
$headers = "From: phillipsibanda711@gmail.com\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "Mail function executed successfully!.";
} else {
    echo "Mail function failed.";
}
?>
