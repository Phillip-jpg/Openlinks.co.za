<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../helpers/token.php');
class notification_body{
    //generates the body of the notification with the to and from details
    //uses a switch statement to call the relevant private function  
   Public $toID;
   Public $fromID;
   public function notification_contents($notify_id, $event, $from, $toID, $fromID, $time, $result = [], $result1 = [], $date = null){
        $this->toID=$toID;
        $this->fromID= token::encode($fromID);
        switch($event){
            case 1:
            case 2:
            case 3:
              ($event == 1 ) ? $thingy = 'company' : (($event == 2) ? $thingy = 'smme' : $thingy = 'npo');
              $subject = "$from initiated a link with you";
                $body_contents = $this->sentRequest($from, $thingy, $notify_id);
                $email = $this->layout($notify_id, $body_contents, $subject, $time);
                return $email;
               
            case 7:
            case 8:
            case 9:
            case 18:
            case 21:
            case 44:
              $subject ="$from ended your connection";
                $body_contents = $this->rejectedRequest($from);
                $email = $this->layout($notify_id,$body_contents,$subject, $time);
                return $email;
                
                case 10:
                case 12:
                  $subject ="Information about $from";
                    $body_contents = $this->smme_read_info($result, $result1, $notify_id);
                    $email = $this->layout($notify_id,$body_contents,$subject, $time);
                    return $email;
                    
                case 11:
                  $subject ="Information about $from";
                    $body_contents = $this->company_read_info($result, $notify_id);
                    $email = $this->layout($notify_id,$body_contents,$subject,$time);
                    return $email;
                   
            case 19:
              $subject ="Further your connection with $from";
                $body_contents = $this->companyApproval($from, $notify_id);
                $email = $this->layout($notify_id,$body_contents,$subject,$time);
                return $email;
                
            case 22:
            case 25:
              $subject ="Are you communicating with $from";
                $body_contents = $this->furtherCommunication($from, $notify_id);
                $email = $this->layout($notify_id,$body_contents,$subject,$time);
                return $email;
                
             
            case 29:
              $subject ="Are you communicating with $from";
                $body_contents = $this->Shoot_Shot($from);
                $email = $this->layout($notify_id,$body_contents, $subject, $time);
                return $email;
                 
                case 31: 
                  $subject ="Meeting date with $from";
                    $body_contents = $this->DateReject($from);
                    $email = $this->layout($notify_id,$body_contents, $subject, $time);
                    return $email;
                     
            case 30:
              $subject ="Meeting date with $from";
                $body_contents =  $this->setDate($from, $notify_id);
                $email = $this->layout($notify_id,$body_contents, $subject, $time);
                return $email;
                
                case 32: 
                  $subject ="Meeting date with $from";
                    $body_contents = $this->dateSet($from, $date);
                    $email = $this->layout($notify_id,$body_contents, $subject, $time);
                    return $email;
                    
            // case 7:
 
            //     $date = $_POST['date'];
            //     $body_contents = $this->meeting($to, $from, $date);
            //     $email = $this->layout($notify_id,$body_contents);
            //     return $email;
            //     break;
            case 35:
            case 38:
              $subject ="Meeting results with $from";
                $body_contents = $this->meeting($from, $date, $notify_id);
                $email = $this->layout($notify_id,$body_contents, $subject, $time);
                return $email;
                
            case 41:
              $subject ="Have you finalized your connection with $from";
                $body_contents = $this->dateWait($from, $notify_id);
                $email = $this->layout($notify_id,$body_contents, $subject, $time);
                return $email;
                   
            case 42:
                $subject ="Your connection is finalized with $from";
                $body_contents = $this->finalized($from);
                $email = $this->layout($notify_id,$body_contents, $subject, $time);
                return $email;
                break;
            case 45:
                $subject = 'Enable '.$from.' as your consultant';
                $body_contents = $this->establish_connection($from);
                $email = $this->layout($notify_id,$body_contents, $subject, $time);
                return $email;
                break;
            case 46:
              $subject ="You have consultant rights for $from";
              $body_contents = $this->consultant_can_control($from);
              $email = $this->layout($notify_id,$body_contents, $subject, $time);
              return $email;
              break;
            case 47:
              $subject ="You allowed ".$result[0]." to have consultant rights";
              $body_contents = $this->company_allowed_control($from);
              $email = $this->layout($notify_id,$body_contents, $subject, $time);
              return $email;
              break;
            case 48:
              $subject ="$from revoked your consultant rights";
              $body_contents = $this->consultant_control_revoked($from);
              $email = $this->layout($notify_id,$body_contents, $subject, $time);
              return $email;
              break;
            case 49:
              $subject ="You revoked ".$result[0]."'s consultant rights";
              $body_contents = $this->company_revoked_control($from);
              $email = $this->layout($notify_id,$body_contents, $subject, $time);
              return $email;
              break;
        }
    }
    private function sentRequest($from, $who,$id){
      // print_r($who);
      // exit();
        $notification = '
              <form class="form-horizontal form-label-left" method="Post" action="../Main/Main_Notify.php?id='.$this->fromID.'">
              <input type="text" name="tk" value="'.token::get_ne($who.'_request_YASC').'" required="" hidden>
               <input type="text" name="notify_id" value="'.$id.'" required="" hidden>
              '.$from.' has initiated to connect with you .<br>
                <div class="ln_solid"></div>
                <div class="form-group">
                  <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <button class="btn btn-success" type="submit" name="'.$who.'_requestAccept">Accept request</button>
                    <button class="btn btn-danger" type="submit" name="'.$who.'_requestReject">Reject request</button>
                  </div>
                </div>

              </form> ';
      return $notification;
    }
    private function rejectedRequest($from){
        $notification = '
              Seems '.$from.' has decided to no longer proceed with forging a connection with your organization. 
              Ensure that you engage more with the entity you are communcating with. Openlinks ensures that you can make suitable connections.
              Make more connections <a>here</a> <br>

                <div class="ln_solid"></div>';
        return $notification;
    }
    private function DateReject($from){
      $notification = '
            Seems '.$from.' has decided to no longer proceed with forging a connection with your organization. 
            Ensure that you engage more with the entity you are communcating with. Openlinks ensures that you can make suitable connections.
            Make more connections <a>here</a> <br>

              <div class="ln_solid"></div>';
      return $notification;
  }
    private function smme_read_info(array $result, $result1, $id){
      $address = $result['city'].", ".$result['Province'];
      // Current avatar --><i class="fa fa-angle-left"></i>
        $notification = '
                      <div class="row">
                          <div class="col-md-9 col-sm-9 col-lg-9 justify-content-center align-items-center">
                            <h2  class="text-capitalize  display-4 ">'.$result['Legal_name'].'</h2>
                          </div>
                      </div>
                      
                
                    ';
      $notification .= '<hr><div class="col-md-12 col-sm-12 col-lg-12 ">
      
                        <ul class="list-unstyled user_data">
                          <li class="text-capitalize">
                            <i class="fa fa-map-marker user-profile-icon"></i> Address -> '.$address.'
                          </li>
      
                          <li class="text-capitalize text-jusitfy">
                            <i class="fa fa-briefcase user-profile-icon"></i> Ownership -> '.$result['foo'].'
                          </li>
                          <li class="text-capitalize text-jusitfy">
                          <i class="fa fa-industry user-profile-icon"></i> Industry -> '.$result['title'].'
                          </li>
                          <li class="text-capitalize text-jusitfy">
                            <i class="fa fa-bullseye user-profile-icon"></i> Vision ->  '.$result['vision'].'
                          </li>
                          <li class="text-capitalize text-jusitfy">
                          <i class="fa fa-bullseye user-profile-icon"></i> mission -> '.$result['mission'].'
                          </li>
                          <li class="text-capitalize text-jusitfy">
                          <i class="fa fa-bullseye user-profile-icon"></i> values -> '.$result['values_'].'
                          </li>
                          <li class="text-capitalize text-jusitfy">
                            <i class="fa fa-briefcase user-profile-icon"></i> Email -> '.$result['Email'].'
                          </li>
                          <li class="text-capitalize text-jusitfy">
                          <i class="fa fa-phone user-profile-icon"></i> Contact -> '.$result['Contact'].'
                          </li>
                        </ul>
      
                      
                      </div>';
                      
        
        $notification .= "<form class='form-horizontal form-label-left' method='Post' action='../Main/Main_Notify.php?id=".$this->fromID."'>";
        $notification .= '<input type="text" name="tk" value="'.token::get_ne('smme_read_YASC').'" required="" hidden>
         <input type="text" name="notify_id" value="'.$id.'" required="" hidden>
        <div class="form-group">
        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
          <button class="btn btn-success" type="submit" name="smme_readAccept">Connect</button>
          <button class="btn btn-danger" type="submit" name="smme_readReject">Reject Connection</button>
        </div>
      </div>';
        $notification .= "</form>";
        return $notification;

    }

    private function company_read_info(array $result, $id){//when viewing more info about SMME
      $address = $result['city'].", ".$result['Province'];
      // Current avatar --><i class="fa fa-angle-left"></i>
        $notification = '
                      <div class="row">
                          <div class="col-md-9 col-sm-9 col-lg-9 justify-content-center align-items-center">
                            <h2  class="text-capitalize profile_title  display-4 ">'.$result['Legal_name'].'</h2>
                          </div>
                      </div>
                      
                
                    ';
      $notification .= '<hr><div class="col-md-12 col-sm-12 col-lg-12 ">
      
                        <ul class="list-unstyled user_data">
                          <li class="text-capitalize">
                            <i class="fa fa-map-marker user-profile-icon"></i> Address -> '.$address.'
                          </li>
      
                          <li class="text-capitalize text-jusitfy">
                            <i class="fa fa-briefcase user-profile-icon"></i> Ownership -> '.$result['foo'].'
                          </li>
                          <li class="text-capitalize text-jusitfy">
                          <i class="fa fa-industry user-profile-icon"></i> Industry -> '.$result['title'].'
                          </li>
                          <li class="text-capitalize text-jusitfy">
                            <i class="fa fa-bullseye user-profile-icon"></i> Vision ->  '.$result['vision'].'
                          </li>
                          <li class="text-capitalize text-jusitfy">
                          <i class="fa fa-bullseye user-profile-icon"></i> mission -> '.$result['mission'].'
                          </li>
                          <li class="text-capitalize text-jusitfy">
                          <i class="fa fa-bullseye user-profile-icon"></i> values -> '.$result['values_'].'
                          </li>
                          <li class="text-capitalize text-jusitfy">
                          <i class="fas fa-people-carry user-profile-icon"></i> BBBEE Level -> '.$result['BBBEE_Status'].'
                          </li>
                          <li class="text-capitalize text-jusitfy">
                            <i class="fa fa-briefcase user-profile-icon"></i> Email -> '.$result['Email'].'
                          </li>
                          <li class="text-capitalize text-jusitfy">
                          <i class="fa fa-phone user-profile-icon"></i> Contact -> '.$result['Contact'].'
                          </li>
                        </ul>
      
                      
                      </div>';
                      
                      $notification .= "<form class='form-horizontal form-label-left' method='Post' action='../Main/Main_Notify.php?id=".$this->fromID."'>";
                      $notification .= '  <input type="text" name="tk" value="'.token::get_ne("company_read_YASC").'" required="" hidden>
                       <input type="text" name="notify_id" value="'.$id.'" required="" hidden>
                                          <div class="form-group">
                                          <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <button class="btn btn-success" type="submit" name="company_readAccept">Connect</button>
                                            <button class="btn btn-danger" type="submit" name="company_readReject">Reject Connection</button>
                                          </div>
                                        </div>';
                      $notification .= "</form>";

      return $notification;
    }
    private function furtherCommunication($from, $id){

        $notification = "<form class='form-horizontal form-label-left' method='Post' action='../Main/Main_Notify.php?id=".$this->fromID."'>";
        $notification .= '<input type="text" name="tk" value="'.token::get_ne("smme_receivedCommunication_YASC").'" required="" hidden>
         <input type="text" name="notify_id" value="'.$id.'" required="" hidden>
        Have you received any further communication from <strong>'.$from.'?</strong></br></br>';
        $notification .= '<div class="form-group">
        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
          <button class="btn btn-success" type="submit" name="smme_receivedCommunication">Yes I have</button>
          <button class="btn btn-danger" type="submit" name="smme_notReceivedCommunication">No I have not</button>
        </div>
      </div>';
        $notification .= "</form>";
    return $notification;
    }

    private function five_day_email($entity, $receiver){
      $event_email = '<h3 style="font-family:HelveticaNeue-Light,Helvetica,Arial,sans-serif;line-height:1.1;margin-bottom:15px;color:#000;font-weight:500;font-size:27px">
      Hello, '.$receiver.'</h3>
      <p style="font-size:17px">Oops, it seems there has not been any movement after the connection with '.$entity.' .Please make sure you continue to connect with them</p>';
          return $event_email;
  }

    private function companyApproval($from, $id){
        $notification = "<form class='form-horizontal form-label-left' method='Post' action='../Main/Main_Notify.php?id=".$this->fromID."'>";
        $notification .= '<input type="text" name="tk" value="'.token::get_ne("connectFurther_YASC").'" required="" hidden>
         <input type="text" name="notify_id" value="'.$id.'" required="" hidden>
        Do you wish to further connect with ' .$from.', to establish a line of communication?<br>';
        $notification .= '<div class="form-group">
        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
          <button class="btn btn-success" type="submit" name="connectFurther">Accept</button>
          <button class="btn btn-danger" type="submit" name="notConnectFurther">Reject</button>
        </div>
      </div>';
        $notification .= "</form>";
        return $notification;
    }
    private function Shoot_Shot($from){
        $notification = 'Oops, it seems there has not been any movement after the connection with <strong>' .$from.'</strong>. Please make sure you continue to connect with them<br>';
        return $notification;
    }
    private function setDate($from, $id){
        $notification = "<form class='form-horizontal form-label-left' method='Post' action='../Main/Main_Notify.php?id=".$this->fromID."'>";
        $notification .= '<input type="text" name="tk" value="'.token::get_ne("smme_setDate_YASC").'" required="" hidden>
         <input type="text" name="notify_id" value="'.$id.'" required="" hidden>
        Good news, your link with <strong>' .$from.'</strong> has been established and progress is underway. Set a date for a meeting to discuss further arrangements to complete the process.<br>';
        $notification .= '<div class="form-group">
        <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Set A Date <span class="required"></span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
    <div class="form-group">
    <div class="input-group date" id="myDatepicker">
    <input type="datetime-local" class="form-control" name="date">
  </div>
  </div>
        </div>
      </div>
        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
          <button class="btn btn-success" type="submit" name="smme_setDate">Continue linking</button>
        </div></div>';
        $notification .= "</form>";
        $notification .= '<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">';
        $notification .= "<form class='form-horizontal form-label-left' method='Post' action='../Main/Main_Notify.php?id=".$this->fromID."'>";
        $notification .= '<input type="text" name="tk" value="'.token::get_ne("smme_setDate_YASC").'" required="" hidden> <input type="text" name="notify_id" value="'.$id.'" required="" hidden>';
        $notification .= '<button class="btn btn-danger" type="submit" name="smme_setDateReject">End Process</button>';
        $notification .= "</form>";
        $notification .= '</div>';


        return $notification;
    }
    private function dateSet($from, $date){
        $notification = '<div style="text-align:center;">';
        $notification .= 'Exciting news '.$from.' has scheduled to have a meeting with you on '. $date.'.<br>';
        $notification .= '</div>';
        return $notification;
    } private function meeting($from, $date,$id){
        $notification = "<form class='form-horizontal form-label-left' method='Post' action='../Main/Main_Notify.php?id=".$this->fromID."'>";
        $notification .= '<input type="text" name="tk" value="'.token::get_ne("meeting_happened_YASC").'" required="" hidden> <input type="text" name="notify_id" value="'.$id.'" required="" hidden>
        Seems like '.$from.' had scheduled a meeting with you for '. $date.'. Has the meeting happened?<br>';
        $notification .= '<div class="form-group">
        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
          <button class="btn btn-success" type="submit" name="meeting_happened">Yes</button>
          <button class="btn btn-danger" type="submit" name="meeting_not_happened">Not Yet</button>
        </div>
      </div>';
        $notification .= "</form>";
        return $notification;
    }
    private function dateWait($from, $id){
        $notification = "<form class='form-horizontal form-label-left' method='Post' action='../Main/Main_Notify.php?id=".$this->fromID."'>";
        $notification .= '<input type="text" name="tk" value="'.token::get_ne("finalized_YASC").'" required="" hidden>
        <input type="text" name="notify_id" value="'.$id.'" required="" hidden>
        Seems like your scheduled meeting date with 
        '.$from.' has passed. Were the conclusions of the meeting succeful in completing the link with '.$from.' ?<br>';
        $notification .= '<div class="form-group">
        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
          <button class="btn btn-success" type="submit" name="finalized">Yes</button>
          <button class="btn btn-danger" type="submit" name="Notfinalized">Set another date</button>
          <button class="btn btn-danger" type="submit" name="finalizedENDprocess">End Process</button>
        </div>
      </div>';
        $notification .= "</form>";
        return $notification;
    }
   
    public function layout($notify_id,$body, $subject, $time){
      $time = date_create($time);
      $date = date_format($time, "Y/m/d H:i");
        $lay = '<div class="clearfix"></div>
        <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>'.$subject.'</h2><span style="float:right">'.$date.'</span>
              <ul class="nav navbar-right panel_toolbox">
                
                 
                  <li><a id="" type="button"  class="close-link update_notify "><i class="fa fa-close '.$notify_id.'"></i></a></li>
                
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <br />
              ';
    $lay .= $body;
    $lay .= '</div>
    </div>
  </div>
</div> ';
    return $lay;
}

private function finalized($from){
    $notification = '<div style="text-align:center;">';
    $notification .= 'Congratulations! You and '.$from.' have successfully completed your connection! We look foward to more connections that 
    you have. ';
    $notification .= '</div>';
    
    return $notification;
}

private function establish_connection($from){
  $notification = "<form class='form-horizontal form-label-left' method='Post' action='../Main/Main_connection.php?id=".$this->fromID."'>";
  $notification .= '<input type="text" name="tk" value="'.token::get_ne("establish_connection_YASC").'" required="" hidden>
  '.$from.' has recieved your link to be your consultant. Do you want to allow the to have consultant rights on your account? 
  Please note that if you allow them to have consultant rights on your account they will be able to control search, use CSR Hub, send messages and access notifications on your account.
  Do NOT allow anyone who you have not assigned as your consultant to have these rights.<br>';
  $notification .= '<div class="form-group">
  <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
    <button class="btn btn-success" type="submit" name="establish_connection_yes">Yes</button>
    <button class="btn btn-danger" type="submit" name="establish_connection_no">No</button>
  </div>
</div>';
  $notification .= "</form>";
  return $notification;
}

private function consultant_can_control($from){
  $notification = '<div style="text-align:center;">';
  $notification .= $from.' has given you Consultant Rights, you are now able to access their search, use CSR Hub, send messages and view their notifications.. ';
  $notification .= '</div>';
  
  return $notification;
}

private function company_allowed_control($from){
  $notification = '<div style="text-align:center;">';
  $notification .= 'You have given '.$from.' Consultant Rights, they are now able to access your search, use CSR Hub, send messages and view your notifications.';
  $notification .= '</div>';
  
  return $notification;
}

private function consultant_control_revoked($from){
  $notification = '<div style="text-align:center;">';
  $notification .= $from.' has revoked your consultant rights, you will not be able to access their search, use CSR Hub, send messages and view their notifications. ';
  $notification .= '</div>';
  
  return $notification;
}

private function company_revoked_control($from){
  $notification = '<div style="text-align:center;">';
  $notification .= 'You have revoked '.$from.'\'s consultant rights, they will not be able to access your search, use CSR Hub, send messages and view your notifications. ';
  $notification .= '</div>';
  
  return $notification;
}


}