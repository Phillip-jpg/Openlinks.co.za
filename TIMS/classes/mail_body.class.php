<?php
include_once($filepath.'/../helpers/token.php');
class mail_body{
    public function verifyaccount($link, $name, $surname, $who,$email){
        $body_contents = $this->verifyEmail($link, $name, $surname, $who,$email);
        $email = $this->email_layout($body_contents);
        return $email;            
    }
    public function newClient($member_num, $name){
        $body_contents = $this->client_body($member_num, $name);
        $email = $this->email_layout($body_contents);
        return $email;            
    }
    public function email_contents($event, $entity, $receiver, $n_id, $who, $result = [], $result1 = [], $result2 = []){
        $n_id = end($n_id);
        switch($event){
            case 1:
            case 2:
            case 3:
                $body_contents = $this->sentRequest($entity, $receiver, $n_id, $who);
                $email = $this->email_layout($body_contents);
                return $email;
                break;
            case 7:
            case 8:
            case 9:
            case 18:
            case 21:
            case 44:
                $body_contents = $this->rejectedRequest($entity, $receiver);
                $email = $this->email_layout($body_contents);
                return $email;
                break;
            case 10:
            case 12:
                    $body_contents = $this->smme_read_info($result, $receiver,$result1, $result2);
                    $email = $this->email_layout($body_contents);
                    return $email;
                    break;     
            case 11:
                $body_contents = $this->company_read_info($result, $receiver, $result1, $result2);
                $email = $this->email_layout($body_contents);
                return $email;
                break;
            case 19:
                $body_contents = $this->companyApproval($entity, $receiver, $n_id, $who);
                $email = $this->email_layout($body_contents);
                return $email;
                break;  
            case 22:
            case 25:
                $body_contents = $this->furtherCommunication($entity, $receiver, $n_id, $who);
                $email = $this->email_layout($body_contents);
                return $email;
                break;   
            case 29:
                $body_contents =  $this->shoot_shot($entity, $receiver);
                $email = $this->email_layout($body_contents);
                return $email;
                break; 
            case 30:
                $body_contents = $this->setDate($entity, $receiver, $n_id, $who);
                $email = $this->email_layout($body_contents);
                return $email;
                break;  
            case 32: 
                $date = $_POST["date"];
                $body_contents = $this->dateSet($entity, $date, $receiver, $n_id, $who);
                $email = $this->email_layout($body_contents);
                return $email;
                break; 
            case 35:
            case 38:
                $body_contents = $this->meeting($entity, $result, $receiver, $n_id, $who);
                $email = $this->email_layout($body_contents);
                return $email;
                break; 
            case 41:
                $body_contents = $this->dateWait($entity, $receiver, $n_id, $who);
                $email = $this->email_layout($body_contents);
                return $email;
                break;
            case 42:
                $body_contents = $this->finalized($entity, $receiver);
                $email = $this->email_layout($body_contents);
                return $email;
                break;
            case 45:
                $body_contents = $this->establish_connection($entity, $receiver, $n_id, $who);
                $email = $this->email_layout($body_contents);
                return $email;
                break;
            case 46:
                $body_contents = $this->consultant_can_control($entity, $receiver);
                $email = $this->email_layout($body_contents);
                return $email;
                break;
            case 47:
                $body_contents = $this->company_allowed_control($entity, $receiver);
                $email = $this->email_layout($body_contents);
                return $email;
                break;
            case 48:
                $body_contents = $this->consultant_control_revoked($entity, $receiver);
                $email = $this->email_layout($body_contents);
                return $email;
                break;
            case 49:
                $body_contents = $this->company_revoked_control($entity, $receiver);
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
            case 50:
                $subject = 'OpenLinks Account Verification.';
                return $subject;
                break;
        }
    }
    private function sentRequest($sender, $receiver, $n_id, $who){
        $token = token::encode($n_id);
        $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.$receiver.'</h3>
        <p style="font-size:17px">'.$sender.' is interested in your business and would like to initiate a link with you. </p>
        <p class="callout" style="color: white; padding:15px;background-color:#666;margin-bottom:15px">
            Complete this action <a style="font-weight:700;color:#2ba6cb;" 
            href="https://openlinks.co.za/TIMS/'.$who.'/notifications.php?url='.$token.'"> Here! &raquo;</a></p>';
            return $event_email;
    }
    private function rejectedRequest($sender, $receiver){
        $token = token::encode($this->n_id);
        $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.$receiver.'</h3>
        <p style="font-size:17px">Seems '.$sender.' has decided to no longer proceed with forging a connection with your organization. 
        Ensure that you engage more with the entity you are communcating with. Openlinks ensures that you can make suitable connections.
        Make more connections <a>here</a> </p>';
            return $event_email;
    }
    private function furtherCommunication($entity, $receiver, $n_id, $who){
        $token = token::encode($n_id);
        $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.$receiver.'</h3>
        <p style="font-size:17px">Have you received any further communication from '.$entity.' ? </p>
        <p class="callout" style="color: white; padding:15px;background-color:#666;margin-bottom:15px">
            Complete this action <a style="font-weight:700;color:#2ba6cb;" 
            href="https://openlinks.co.za/TIMS/'.$who.'/notifications.php?url='.$token.'"> Here! &raquo;</a></p>';
            return $event_email;
    }private function companyApproval($entity, $receiver, $n_id, $who){
        $token = token::encode($n_id);
        $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.$receiver.'</h3>
        <p style="font-size:17px">Do you wish to further connect with ' .$entity.', to establish a line of communication? </p>
        <p class="callout" style="color: white; padding:15px;background-color:#666;margin-bottom:15px">
            Complete this action <a style="font-weight:700;color:#2ba6cb;" 
            href="https://openlinks.co.za/TIMS/'.$who.'/notifications.php?url='.$token.'"> Here! &raquo;</a></p>';
            return $event_email;
    }
    private function smme_read_info(array $result, $receiver, $result1 = [], $result2 = []){//displays company information
        $email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.$receiver.'</h3>
        <div style=" text-align:center;">
        <h1 style="text-decoration: underline;">Information About '.$result['Legal_name'].'</h1>
        <h3 style="text-decoration: underline;">Company Information</h3>
         <table style="margin-left: auto;margin-right: auto; width: 100%;">
         <tr>
         <th width="35%" style="font-weight: bold; text-align: justify;">Location</th><td style="text-align: justify; padding-left: 10%;" width="75%">'.$result['city'].' '.$result['Province'].'</td>
         </tr>
         </table>
         <h3 style="text-decoration: underline;">Company Data</h3>
         <table style="margin-left: auto;margin-right: auto; width: 100%">
         <tr>
            <th width="35%" style="font-weight: bold; text-align: justify;">Vision</th><td style="text-align: justify; padding-left: 10%;" width="75%">'.$result['vision'].'</td>
         </tr>
         <tr>
            <th width="35%" style="font-weight: bold; text-align: justify;">Introduction</th><td style="text-align: justify; padding-left: 10%;" width="75%">'.$result['introduction'].'</td>
         </tr>
         <tr>
            <th width="35%" style="font-weight: bold; text-align: justify;">Mission</th><td style="text-align: justify; padding-left: 10%;" width="75%">'.$result['mission'].'</td>
         </tr>
         <tr>
            <th width="35%" style="font-weight: bold; text-align: justify;">Values</th><td style="text-align: justify; padding-left: 10%;" width="75%">'.$result['values_'].'</td>
         </tr>
         <tr>
            <th width="35%" style="font-weight: bold; text-align: justify;">Goals and Objectives</th><td style="text-align: justify; padding-left: 10%;" width="75%">'.$result['goals_objectives'].'</td>
         </tr></table>';
         for($i=0; $i<=count($result1)-1; $i++){
            if($i==0)$email .= '<h3 style="text-decoration: underline;">Products and/or Services</h3>
            <table style="margin-left: auto;margin-right: auto; width: 100%">';
            $email .= '<tr>
            <td style="text-align: justify; padding-left: 10%;" width="75%">'.$result1[$i]['products'].'</td>
         </tr></table>';
         if($i<=count($result1)-1)$email .= '</table>';
        }
        $email .= '</div></br><p>You can View more information once logged in </p>';
        return $email;
         
    }
    private function company_read_info($result, $receiver){
        $email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.$receiver.'</h3>
        <div style=" text-align:center;">
        <h1 style="text-decoration: underline;">Information About '.$result['Legal_name'].'</h1>
        <h3 style="text-decoration: underline;">Company Information</h3>
         <table style="margin-left: auto;margin-right: auto; width: 100%;">
         <tr>
         <th width="35%" style="font-weight: bold; text-align: justify;">Location</th><td style="text-align: justify; padding-left: 10%;" width="75%">'.$result['city'].' '.$result['Province'].'</td>
         </tr>
         <tr>
            <th width="35%" style="font-weight: bold; text-align: justify;" >BBBEE Level</th><td style="text-align: justify; padding-left: 10%;" width="75%">'.$result['BBBEE_Status'].'</td>
         </tr>
         </table>
         <h3 style="text-decoration: underline;">Company Data</h3>
         <table style="margin-left: auto;margin-right: auto; width: 100%">
         <tr>
            <th width="35%" style="font-weight: bold; text-align: justify;">Vision</th><td style="text-align: justify; padding-left: 10%;" width="75%">'.$result['vision'].'</td>
         </tr>
         <tr>
            <th width="35%" style="font-weight: bold; text-align: justify;">Introduction</th><td style="text-align: justify; padding-left: 10%;" width="75%">'.$result['introduction'].'</td>
         </tr>
         <tr>
            <th width="35%" style="font-weight: bold; text-align: justify;">Mission</th><td style="text-align: justify; padding-left: 10%;" width="75%">'.$result['mission'].'</td>
         </tr>
         <tr>
            <th width="35%" style="font-weight: bold; text-align: justify;">Values</th><td style="text-align: justify; padding-left: 10%;" width="75%">'.$result['values_'].'</td>
         </tr>
         <tr>
            <th width="35%" style="font-weight: bold; text-align: justify;">Goals and Objectives</th><td style="text-align: justify; padding-left: 10%;" width="75%">'.$result['goals_objectives'].'</td>
         </tr></table>';
         for($i=0; $i<=count($result)-1; $i++){
            if($i==0)$email .= '<h3 style="text-decoration: underline;">Products and/or Services</h3>
            <table style="margin-left: auto;margin-right: auto; width: 100%">';
            $email .= '<tr>
            <td style="text-align: justify; padding-left: 10%;" width="75%">'.$result[$i]['products'].'</td>
         </tr></table>';
         if($i<=count($result)-1)$email .= '</table>';
        }
        $email .= '</div></br><p>You can View more information once logged in </p>';
        return $email;
    }
    private function shoot_shot($entity, $receiver){
        $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.$receiver.'</h3>
        <p style="font-size:17px">Oops, it seems there has not been any movement after the connection with '.$entity.' .Please make sure you continue to connect with them</p>';
            return $event_email;
    }
    private function setDate($entity, $receiver, $n_id, $who){
        $token = token::encode($n_id);
        $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.$receiver.'</h3>
        <p style="font-size:17px">Good news, your link with' .$entity.'has been established and progress is underway. Set a date for a meeting to discuss further arrangements to complete the process.</p>
        <p class="callout" style="color: white; padding:15px;background-color:#666;margin-bottom:15px">
            Complete this action <a style="font-weight:700;color:#2ba6cb;" 
            href="https://openlinks.co.za/TIMS/'.$who.'/notifications.php?url='.$token.'"> Here! &raquo;</a></p>';
            return $event_email;
    }
    private function dateSet($entity, $date, $receiver, $who){
        $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.$receiver.'</h3>
        <p style="font-size:17px">Exciting news '.$entity.' has scheduled to have a meeting with you on '. $date.'.</p>';
            return $event_email;
    } private function meeting($entity, $date, $receiver, $n_id, $who){
        $token = token::encode($n_id);
        $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.$receiver.'</h3>
        <p style="font-size:17px">Seems like '.$entity.' had scheduled a meeting with you for '. $date.'. Has the meeting happened?.</p>
        <p class="callout" style="color: white; padding:15px;background-color:#666;margin-bottom:15px">
            Complete this action <a style="font-weight:700;color:#2ba6cb;" 
            href="https://openlinks.co.za/TIMS/'.$who.'/notifications.php?url='.$token.'"> Here! &raquo;</a></p>';
            return $event_email;
    }

    private function dateWait($entity, $receiver, $n_id, $who){
        $token = token::encode($n_id);
        $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.$receiver.'</h3>
        <p style="font-size:17px">Seems like your scheduled meeting date with 
        '.$entity.' has passed. Were the conclusions of the meeting successful in completing the link with'.$entity.'?.</p>
        <p class="callout" style="color: white; padding:15px;background-color:#666;margin-bottom:15px">
            Complete this action <a style="font-weight:700;color:#2ba6cb;" 
            href="https://openlinks.co.za/TIMS/'.$who.'/notifications.php?url='.$token.'"> Here! &raquo;</a></p>';
            return $event_email;
    }
    private function verifyEmail($link, $name, $surname, $who, $email){
        //$token = token::encode($n_id);
        $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.strtoupper($name).', '.strtoupper($surname).'</h3>
        <p style="font-size:17px">Click the button bellow to verify your account on OpenLinks
        </p>
        <p class="callout" style="color: white; padding:15px;background-color:#666;margin-bottom:15px">
            Complete this action <a style="font-weight:700;color:#2ba6cb;" 
            href="https://openlinks.co.za/TIMS/'.$who.'/email_verification.php?url='.$link.'&u='.$email.'">Here! &raquo;</a></p>';
            return $event_email;
    }
    private function client_body($member_number, $name){
        //$token = token::encode($n_id);
        $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.strtoupper($name).'</h3>
        <p style="font-size:17px">Your have requested OpenLinks services and your account has been registered. Your member number is <strong>'.$member_number.'</strong></br>
        Use this number when you contact a OpenLinks representative for any of your services.
        </p>
        ';
            return $event_email;
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
                                                        <p class=""><a style="padding:3px 7px;font-size:12px;margin-bottom:10px;text-decoration:none;color:#fff;font-weight:700;display:block;text-align:center; background-color:#3b5998!important;width:100%" href="http://www.facebook.com/OpenLinksSA" class="soc-btn fb">Facebook</a> <a style="padding:3px 7px;font-size:12px;margin-bottom:10px;text-decoration:none;color:#fff;font-weight:700;display:block;text-align:center;width:100%;background-color:#1daced!important;" href="http://twitter.com/OpenLinksSA" class="soc-btn tw">Twitter</a> <a href="http://www.linkedin.com/company/open-links-sa/about/" style="padding:3px 7px;font-size:12px;margin-bottom:10px;text-decoration:none;color:black;font-weight:700;display:block;text-align:center;background-color:#9ed9f5!important;width:100%" class="soc-btn gp">Linkedin</a></p>
                                
                                                        
                                                    </td>
                                                </tr>
                                            </table><!-- /column 1 -->	
                                            
                                            <!-- column 2 -->
                                            <table align="left" style="width:300px;float:left; padding:15px;width:100%; width:280px;min-width:279px;float:left">
                                                <tr>
                                                    <td>				
                                                                                    
                                                        <h5 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:900;font-size:17px">Contact Info:</h5>												
                                                        <p>Phone: <strong><a style="color:#2ba6cb" href="tel:+27 67 935 7717">+27 67 935 7717</a></strong><br/>
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
                                    Openlinks
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
        // <table style="width:100%">
        //                 <tr>
        //                     <td align="center">
        //                         <p>
        //                             <a href="#" style="color:#2ba6cb">Terms</a> |
        //                             <a href="#" style="color:#2ba6cb">Privacy</a> |
        //                             <a href="#" style="color:#2ba6cb"><unsubscribe>Unsubscribe</unsubscribe></a>
        //                         </p>
        //                     </td>
        //                 </tr>
        //             </table>
    }
    private function finalized($entity, $receiver){
        $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.$receiver.'</h3>
        <p style="font-size:17px">Congratulations! You and '.$entity.' have successfully completed your connection! We look foward to more connections that 
        you have.</p>';
            return $event_email;
    }

    private function establish_connection($entity, $receiver, $n_id, $who){
        $token = token::encode($n_id);
        $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.$receiver.'</h3>
        <p style="font-size:17px">'.$entity.' has recieved your link to be your consultant. Do you want to allow the to have consultant rights on your account? 
        Please note that if you allow them to have consultant rights on your account they will be able to control search, use CSR Hub, send messages and view your notifications on your account.
        Do NOT allow anyone who you have not assigned as your consultant to have these rights.</p>
        <p class="callout" style="color: white; padding:15px;background-color:#666;margin-bottom:15px">
            Complete this action <a style="font-weight:700;color:#2ba6cb;" 
            href="https://openlinks.co.za/TIMS/'.$who.'/notifications.php?url='.$token.'"> Here! &raquo;</a></p>';
            return $event_email;
    }

    private function consultant_can_control($entity, $receiver){
        $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.$receiver.'</h3>
        <p style="font-size:17px">'.$entity.' has given you Consultant Rights, you are now able to access their search, use CSR Hub, send messages and view their notifications.</p>';
            return $event_email;
    }

    private function company_allowed_control($entity, $receiver){
        $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.$receiver.'</h3>
        <p style="font-size:17px">You have given '.$entity.'\'s Consultant Rights, they are now able to access your search, use CSR Hub, send messages and view your notifications.</p>';
            return $event_email;
    }

    private function consultant_control_revoked($entity, $receiver){
        $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.$receiver.'</h3>
        <p style="font-size:17px">'.$entity.' has revoked your consultant rights, you will not be able to access their search, use CSR Hub, send messages and view their notifications.</p>';
            return $event_email;
    }

    private function company_revoked_control($entity, $receiver){
        $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
        Hello, '.$receiver.'</h3>
        <p style="font-size:17px">You have revoked '.$entity.'\'s consultant rights, they will not be able to access your search, use CSR Hub, send messages and view your notifications.</p>';
            return $event_email;
    }
}