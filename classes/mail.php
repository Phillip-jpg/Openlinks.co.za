<?php
// /**
//  * This example shows making an SMTP connection with authentication.
//  */

// //Import the PHPMailer class into the global namespace
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
// use PHPMailer\PHPMailer\Exception;

// //SMTP needs accurate times, and the PHP time zone MUST be set
// //This should be done in your php.ini, but this is how to do it if you don't have access to that
// date_default_timezone_set('Etc/UTC');

// require '../vendor/autoload.php';

// //Create a new PHPMailer instance
// $mail = new PHPMailer();
// //Tell PHPMailer to use SMTP
// try {
// $mail->isSMTP();
// //Enable SMTP debugging
// //SMTP::DEBUG_OFF = off (for production use)
// //SMTP::DEBUG_CLIENT = client messages
// //SMTP::DEBUG_SERVER = client and server messages
// $mail->SMTPDebug = SMTP::DEBUG_SERVER;
// //Set the hostname of the mail server
// $mail->Host = 'corporate.vip4.noc401.com';
// //Set the SMTP port number - likely to be 25, 465 or 587
// $mail->Port = 465;

// $mail->SMTPSecure='ssl';
// //Whether to use SMTP authentication
// $mail->SMTPAuth = true;
// //Username to use for SMTP authentication
// $mail->Username = '_mainaccount@openlinks.co.za';
// //Password to use for SMTP authentication
// $mail->Password = 'a6[7bG5MY+eU0e';
// //Set who the message is to be sent from
// $mail->setFrom('info@openlinks.co.za', 'OpenLinks');


// $mail->addAddress('s220963851@mandela.ac.za', 'Papa Bless');
// //Set the subject line
// $mail->Subject = 'PHPMailer test';
// //Read an HTML message body from an external file, convert referenced images to embedded,
// //convert HTML into a basic plain-text alternative body
// $mail->msgHTML("<strong>SSL Test</strong><i>Does the ssl work</i>");
// //Replace the plain text body with one created manually
// $mail->AltBody = 'This is a plain-text message body';
// //Attach an image file

// if($mail->send());
// echo 'Message sent!';

// } catch (Exception $e) {
//     echo $e->errorMessage(); //Pretty error messages from PHPMailer
// } catch (\Exception $e) { //The leading slash means the Global PHP Exception class will be caught
//     echo $e->getMessage(); //Boring error messages from anything else!
// }
use PHPMailer\PHPMailer\Exception;

require 'mail.extend.php';
try {
    //Instantiate your new class, making use of the new `$body` parameter
    $mail = new Mailer(true);
    if($mail->send_single($_POST['email'], $_POST['name'], $_POST['subject'], "<strong>SSL Test 2</strong><i>Does the ssl really work</i>", "Email test does it work"))
    echo "MESSAGE SENT!";
    else echo "MESSAGE FAILED TO SEND!";
} catch (Exception $e) {
    //Note that this is catching the PHPMailer Exception class, not the global \Exception type!
    echo 'Caught a ' . get_class($e) . ': ' . $e->getMessage();
}






// $result=array(
//     0 => array(
//         'name' => 'Tesla',
//         'email' => 'blessingssinsamane@gmail.com',
//     ),
//     1 => array(
//         'name' => 'ArrayLiners',
//         'email' => 'phillipsibanda711@gmail.com',
//     ),
//     2 => array(
//         'name' => 'GenZCode',
//         'email' => 'kgnokanda@gmail.com',
//     )
//     );
//     $mail = new Mailer(true);
//     $fails=$mail->send_multiple(
//         $result, 
//         "This is test 7", 
//         "<strong>Just a test to see if the ! is gone </strong><i>papa bless</i>", 
//         "Email test does it work"
//     );
//     if(!empty($fails))
//     print_r($fails);
//     else echo "SUCCESS!, $mail->Host";