<?php
include_once($filepath.'/../helpers/token.php');
class mail_body{
    public function email_contents($event,$reason, $entity, $surname,$email, $contact, $message){     
        switch($event){
            case 1:
                $body_contents = $this->contact_form($entity,$surname,$email, $contact, $reason, $message);
                $email = $this->email_layout($body_contents);
                return $email;
                break;
        }
    }
    public function subject($event, $entity){
        switch($event){
            case 1:
            case 2:
            case 3:
                $subject = 'Link with '.$entity;
                return $subject;
                break;
            case 7:
            case 8:
            case 9:
            case 18:
            case 21:
            case 44:
                $subject = 'Link with '.$entity.' is terminated';
                return $subject;
                break;
            case 10:
            case 12:
                    $subject = 'Information about '.$entity;
                    return $subject;
                    break;     
            case 11:
                $subject = 'Information about '.$entity;
                return $subject;
                break;
            case 19:
                $subject = 'Continue with '.$entity.'?';
                return $subject;
                break;  
            case 22:
            case 25:
                $subject = 'Continue with '.$entity.'?';
                return $subject;
                break;   
            case 29:
                $subject = 'Don\'t miss this opportunity with '.$entity;
                return $subject;
                break; 
            case 30:
                $subject = 'Set a meeting date with '.$entity;
                return $subject;
                break;  
            case 32: 
                $subject = 'A meeting Date is set with '.$entity;
                return $subject;
                break; 
            case 35:
            case 38:
                $subject = 'Has your meeting with '.$entity.' happened?';
                return $subject;
                break; 
            case 41:
                $subject = 'Has your meeting with '.$entity.' been finalized';
                return $subject;
                break;
            case 42:
                $subject = 'You and '.$entity.' have finalized!!!';
                return $subject;
                break;
            case 45:
                $subject = 'Enable '.$entity.' as your consultant';
                return $subject;
                break;
            case 46:
                $subject = 'You have consultant rights for '.$entity;
                return $subject;
                break;
            case 47:
                $subject = 'You allowed '.$entity.' to have consultant rights';
                return $subject;
                break;
            case 48:
                $subject = $entity.' revoked your consultant control';
                return $subject;
                break;
            case 49:
                $subject = 'You revoked '.$entity.'\'s consultant rights';
                return $subject;
                break;
        }
    }
    private function email_layout($event_email){
        $body = '<div bgcolor="#f2f2f2" style="margin:0;padding:0; font-family:Helvetica,Helvetica,Arial,sans-serif; -webkit-font-smoothing:antialiased;-webkit-text-size-adjust:none;width:100%!important;height:100%">


        <!-- HEADER -->
        <table class="head-wrap" bgcolor="#999999" style="width:100%">
            <tr>
                <td></td>
                <td style="display:block!important;max-width:600px!important;margin:0 auto!important;clear:both!important">
                        
                        <div  style="padding:15px;max-width:600px;margin:0 auto;display:block">
                        <table bgcolor="#999999" style="width:100%">
                            <tr>
                                <td><img style="max-width:50%" src="cid:logo" /></td>
                            </tr>
                        </table>
                        </div>
                        
                </td>
                <td></td>
            </tr>
        </table><!-- /HEADER -->
        
        
        <!-- BODY -->
        <table class="body-wrap" style="width:100%">
            <tr>
                <td></td>
                <td style="display:block!important;max-width:600px!important;margin:0 auto!important;clear:both!important" bgcolor="#f2f2f2">
        
                    <div style="padding:15px;max-width:600px;margin:0 auto;display:block" >
                    <table>
                        <tr>
                            <td>'.$event_email.'           
                                <!-- social & contact -->
                                <table class="social" width="100%" style="background-color:#11437cfa">
                                    <tr>
                                        <td>
                                            
                                            <!-- column 1 -->
                                            <table align="left" style="width:300px;float:left; padding:15px; width:100%;width:280px;min-width:279px;float:left">
                                                <tr>
                                                    <td>				
                                                        
                                                        <h5 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:900;font-size:17px">Connect with Us:</h5>
                                                        <p class=""><a style="padding:3px 7px;font-size:12px;margin-bottom:10px;text-decoration:none;color:#fff;font-weight:700;display:block;text-align:center; background-color:#3b5998!important;width:100%" href="https://www.facebook.com/OpenLinksSA" class="soc-btn fb">Facebook</a> <a style="padding:3px 7px;font-size:12px;margin-bottom:10px;text-decoration:none;color:#fff;font-weight:700;display:block;text-align:center;width:100%;background-color:#1daced!important;" href="https://twitter.com/OpenLinksSA" class="soc-btn tw">Twitter</a> <a href="https://www.linkedin.com/company/open-links-sa/about/" style="padding:3px 7px;font-size:12px;margin-bottom:10px;text-decoration:none;color:black;font-weight:700;display:block;text-align:center;background-color:#9ed9f5!important;width:100%" class="soc-btn gp">Linkedin</a></p>
                                
                                                        
                                                    </td>
                                                </tr>
                                            </table><!-- /column 1 -->	
                                            
                                            <!-- column 2 -->
                                            <table align="left" style="width:300px;float:left; padding:15px;width:100%; width:280px;min-width:279px;float:left">
                                                <tr>
                                                    <td>				
                                                                                    
                                                        <h5 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:900;font-size:17px">Contact Info:</h5>												
                                                        <p>Phone: <strong><a style="color:#2ba6cb" href="tel:+27 41 492 4146">+27 41 492 4146</a></strong><br/>
                        Email: <strong><a style="color:#2ba6cb" href="mailto:info@openlinks.co.za">info@openlinks.co.za</a></strong></p>
                        
                                                    </td>
                                                </tr>
                                            </table><!-- /column 2 -->
                                            
                                            <span class="clear" style="display:block;clear:both"></span>	
                                            
                                        </td>
                                    </tr>
                                </table><!-- /social & contact -->
                                
                            </td>
                        </tr>
                    </table>
                    </div><!-- /content -->
                                            
                </td>
                <td></td>
            </tr>
        </table><!-- /BODY -->
        
        <!-- FOOTER -->
        <table class="footer-wrap" style="width:100%;clear:both!important">
            <tr>
                <td></td>
                <td style="display:block!important;max-width:600px!important;margin:0 auto!important;clear:both!important">
                    
                        <!-- content -->
                        <div style="padding:15px;max-width:600px;margin:0 auto;display:block">
                        <table style="width:100%">
                        <tr>
                            <td align="center">
                                <p>
                                    <a href="#" style="color:#2ba6cb">Terms</a> |
                                    <a href="#" style="color:#2ba6cb">Privacy</a> |
                                    <a href="#" style="color:#2ba6cb"><unsubscribe>Unsubscribe</unsubscribe></a>
                                </p>
                            </td>
                        </tr>
                    </table>
                        </div><!-- /content -->
                        
                </td>
                <td></td>
            </tr>
        </table><!-- /FOOTER -->
        
        </div>';
        return $body;
    }
   
    private function contact_form($entity, $surname,$email, $contact, $reason, $message){
        $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello Admin.</h3>
        <p style="font-size:17px">'.$entity.' '.$surname.', sent this email in relation to: <b>'.$reason.'.</b></p><br><br><p style="font-size:17px"></p>';
        $event_email .= '<ul>
                            <li>Name: '.$entity.'</li>
                            <li>Surname: '.$surname.'</li>
                            <li>Email: '.$email.'</li>
                            <li>Contact: '.$contact.'</li>
                            <li>Reason: '.$reason.'</li>
                        </ul>';
        $event_email .= '<p style="font-size:17px">'.$message.'</p>';
            return $event_email;
    }
}
