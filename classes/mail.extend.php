<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once('../vendor/autoload.php');

class Mailer extends PHPMailer
{
    /**
     * myPHPMailer constructor.
     *
     * @param bool|null $exceptions
     * @param string    $body A default HTML message body
     */
    
    public function __construct($exceptions)
    {
        //Don't forget to do this or other things may not be set correctly!
        parent::__construct($exceptions);
        //Set a default 'From' address
        $this->setFrom('supportteam@openlinks.co.za', 'OpenLinks');
        //Send via SMTP
        $this->isSMTP();
        //Equivalent to setting `Host`, `Port` and `SMTPSecure` all at once
        $this->Host = 'cp30-za1.host-ww.net';
        //Set an HTML and plain-text body, import relative image references
        $this->Port = 465;

        $this->SMTPSecure='ssl';

        $this->SMTPAuth = true;
        //Username to use for SMTP authentication
        $this->Username = 'supportteam@openlinks.co.za';
        //Password to use for SMTP authentication
        $this->Password = '[6.mEKo6S1mc4A';
        //Show debug output
        $this->SMTPDebug = SMTP::DEBUG_OFF; //SMTP::DEBUG_SERVER DEBUG_OFF; 
        //Inject a new debug output handler

        //This should be the same as the domain of your From address
        $this->DKIM_domain = $this->From;
        //See the DKIM_gen_keys.phps script for making a key pair -
        //here we assume you've already done that.
        //Path to your private key:
        $this->DKIM_private = '../Test/dkim_private.pem';
        //Set this to your own selector
        $this->DKIM_selector = 'openlinks';
        //Put your private key's passphrase in here if it has one
        $this->DKIM_passphrase = '';
        //The identity you're signing as - usually your From address
        $this->DKIM_identity = $this->From;
        //Suppress listing signed header fields in signature, defaults to true for debugging purpose
        $this->DKIM_copyHeaderFields = false;
    }

    public function send_single($address, $name, $subject, $htmlbody = '', $altbody = ''){
        $address = "info@openlinks.co.za";
        $this->msgHTML($htmlbody);
        $this->addAddress($address, "OpenLinks");
        $this->AltBody=$altbody;
        $this->Subject = $subject;
        $this->AddEmbeddedImage("../Images/logo.png", "logo", "logo.png");
        return $this->send();
    }

    public function send_multiple(array $result,array $names, array $subject, array $htmlbody, $altbody = ''){
        $fails=array();
        $this->SMTPKeepAlive = true;
        for ($x = 0; $x < count($result); $x++){
            try {
                $this->addAddress($result[$x], $names[$x]);
            } catch (Exception $e) {
                echo 'Invalid address skipped: ' . htmlspecialchars($result[$x]) . '<br>';
                continue;
            }
            
        $this->msgHTML($htmlbody[$x]);
        $this->AltBody=$altbody;
        $this->Subject = $subject[$x];
        $this->AddEmbeddedImage("../Images/logo.png", "logo", "logo.png");
        try {
            if(!$this->send())
            $fails = array_push($fails, $result[$x]);
        } catch (Exception $e) {
            echo 'Mailer Error (' . htmlspecialchars($result[$x]) . ') ' . $this->ErrorInfo . '<br>';
            $this->getSMTPInstance()->reset();
        }
        $this->clearAddresses();
        $this->clearAttachments();
        }
        return $fails;
    }
}

