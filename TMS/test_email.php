<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$to = "phillipsibanda711@gmail.com";
$subject = "Test mail()";
$message = "If you see this, PHP mail() is working.";
$headers = "From: system@openlinks.co.za";

if (mail($to, $subject, $message, $headers)) {
    echo "mail() SENT ✔";
} else {
    echo "mail() FAILED ❌";
}
?>
