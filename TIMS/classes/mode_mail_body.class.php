<?php
class mode_mail_body{
    public function email_body($EVENT_ID, $FROM){
        switch($EVENT_ID){
                case 1:
                  $body_contents = $this->requestBody($FROM);
                  $email = $this->email_layout($body_contents);
                  return $email;
                  break;
                case 2:
                  $body_contents = $this->candidate_email($FROM);
                  $email = $this->email_layout($body_contents);
                  return $email;
                  break;
                case 3:
                  $body_contents = $this->filled_posting($FROM);
                  $email = $this->email_layout($body_contents);
                  return $email;
                  break;
                case 4:
                    $body_contents = $this->selection_process($FROM);
                    $email = $this->email_layout($body_contents);
                    return $email;
                    break;
                case 5:
                      $body_contents = $this->accepted_request($FROM);
                      $email = $this->email_layout($body_contents);
                      return $email;
                      break; 
                case 0:
                  $body_contents = $this->rejected_request($FROM);
                  $email = $this->email_layout($body_contents);
                  return $email;
                  break;    
        }
    }
    private function accepted_request($sender){
      $event_email = '<div style="color: whitesmoke; text-align:center;">';
      $event_email .= $sender.' has seen your request and has accepted. '.$sender.' has been added to your selection list on your dashboard.<br>';
      $event_email .= '</div>';
      return $event_email;
}
    private function requestBody($sender){
          $event_email = '<div style="color: whitesmoke; text-align:center;">';
          $event_email .= $sender.' has seen your job post and is interested in engaging in business with you.<br>';
          $event_email .= '<form method="POST" action=" https://www.openlinks.co.za/Main/Main_Consult_Mode.php?id1=....&id2=...">';//main
          $event_email .= '<input style="padding: 5px;margin: 5px;"  type="submit" value="Accept" /> <input style="padding: 5px;margin: 5px"   type="submit" value="Reject" />';
          $event_email .= '</form>';
          $event_email .= '</div>';
          return $event_email;
    }
    private function candidate_email($sender){
      $event_email = '<div style="color: whitesmoke; text-align:center;">';
      $event_email .= 'Congratulations! We are pleased to inform you that you have been chosen by '. $sender.' for the job post that you applied for. <br>You can now operate on behalf of '. $sender.' on the mySMME portal on your dashboard. You can access the companies dashboard on the CONSULTANT MODE tab on your dashboard.<br>';
      $event_email .= '</div>';
      return $event_email;
  }
  private function selection_process($sender){
    $event_email = '<div style="color: whitesmoke; text-align:center;">';
    $event_email .= 'Great News! We are pleased to inform you that you have been chosen by '. $sender.' for the job post that you applied for to go through to the selection process. <br>'.$sender.' will make a decision and you will be informed of the outcome when stipulated end date is';
    $event_email .= '</div>';
    return $event_email;
}
  private function filled_posting($sender){
      $event_email = '<div style="color: whitesmoke; text-align:center;">';
          $event_email .= 'We regret to inform you that the job posting that you had applied for with '.$sender.' has been filled.';
          $event_email .= '</div>';
          return $event_email;
  }
  private function rejected_request($sender){
      $event_email = '<div style="color: whitesmoke; text-align:center;">';
          $event_email .= 'We regret to inform you that your application has not been accepted by '.$sender.' for the job posting that you had applied for.';
          $event_email .= '</div>';
          return $event_email;
  }
    private function email_layout($event_email){
      $body = '<div style="background-color: rgb(0,0,0,0.5);letter-spacing: 1.5px;font-family: sans-serif;">';
      $body .= '<div style="display: flex;">';
      $body .= '<img src="" width="160" height="120" alt="Openlinks logo" style="margin-left: 30px;">';
      $body .= '<h1 style="text-align:center;color: whitesmoke;">OPENLINKS</h1>';
      $body .= '</div>';
      $body .= '<div>';
      $body .= $event_email;
      $body .= '</div>';
      $body .= '<footer style="text-align:center; color: whitesmoke;">';
      $body .= '<h3>&copy OPENLINKS 2021</h3>';
      $body .= '<p>';
      $body .= 'East Rand<br>';
      $body .= 'Gauteng<br>';
      $body .= '<a href="openlinks.co.za" style="text-decoration:none; color: whitesmoke;">openlinks.co.za</a>';
      $body .= '</p>';
      $body .= '</footer>';
      $body .= '</div>';
      return $body;
  }
}