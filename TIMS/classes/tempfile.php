<?php
private function products($id){
    $sql1 = $this->PRODUCTS[0];
    $types = $this->PRODUCTS[1];
    if($_SESSION['WHO'] == "SMME" ||  $_SESSION['WHO'] == "NPO"){
      $temp1=DB_NAME_1;
    }
    else{
      $temp1=$this->var2;
    }
    
    $query = $this->master->select_prepared_async($sql1, $temp1, $types, array($id));
    if(!$query){
      echo "error occured with products";
      exit();
    }else{
      $result = $this->master->getResult();
      return $result;
    }
  }
  
  private function fetchMore_info($id){//incomplete
    $sql1 = $this->MORE_INFO[0];
    $types = $this->MORE_INFO[1];
    $temp1=$this->var2;
      $query = $this->master->select_prepared_async($sql1, $temp1, $types, array($id));
      if(!$query){
      echo "sql ". $sql1;
      echo "<br>types ". $types;
      echo "<br>temp ". $temp1;
      echo "<br>value ". $id;
      echo $this->classname;
      // header("location: ../index.php?error=failedtofetchmoreinfo");
      exit();
    }else{
      $result = $this->master->getResult();
      if(is_numeric($result[0])){
        echo "<p class='text-center'>No information available yet, check in again later.</p>";
        exit();
    }else{
      return $result;
    }
      
    }
    
  }
  private function fetchMore_info2($id){//this one is for when an smme wants to view smme information
    $sql1 = $this->SMME_tO_SMME_MORE_INFO[0];
    $types = $this->SMME_tO_SMME_MORE_INFO[1];
      $query = $this->master->select_prepared_async($sql1, $this->var, $types, array($id));
      if(!$query){
      
      header("location: ../index.php?error=failedtofetchmoreinfo");
      exit();
    }else{
      $result = $this->master->getResult();
      return $result;
    }
    
  }
  private function fetchMore_info3($id){//this one is for when an company wants to view company information
    $sql1 = $this->COMPANY_TO_COMPANY_VIEW_MORE[0];
    $types = $this->COMPANY_TO_COMPANY_VIEW_MORE[1];
      $query = $this->master->select_prepared_async($sql1, $this->var, $types, array($id));
      if(!$query){
      
      header("location: ../index.php?error=failedtofetchmoreinfo");
      exit();
    }else{
      $result = $this->master->getResult();
      return $result;
    }
    
  }
  
  public function view_more_chart($id){
    $sql1 = $this->MORE_INFO_CHART[0];
    $types = $this->MORE_INFO_CHART[1];
    $query = $this->master->select("smmes",$sql1, $types, array($id, session::get($this->id)));
    if(!$query){
      print_r($sql1);
        print_r($types);
        echo $id;
        
      exit();
    }else{
      $result = $this->master->getResult();
      if(empty($result)){
        return -1;
      }else{
        echo json_encode($result);
      }
      
    }
  }
  
  private function smme_view($result, $products, $id){
    if(empty($result)&&empty($products)){
      echo "<p class='h3 text-center text-capitalize'>No information available</p>";
    }elseif(empty($result)){
      echo "<p class='h3 text-center text-capitalize'>No information available</p>";
    }else{
      
    $address = $result[0]['city'].", ".$result[0]['Province'];
    // Current avatar --><i class="fa fa-angle-left"></i>
    $display = '
                  <div class="row">
                      <div class="col-md-3 col-sm-3 col-lg-3">
                        <img class="img-responsive border-rounded " src="'.$result[0]['ext'].'" alt="Avatar" title="'.$result[0]['Legal_name'].'">
                      </div>
                      <div class="col-md-9 col-sm-9 col-lg-9 justify-content-center align-items-center">
                        <h2  class="text-capitalize profile_title  display-4 ">'.$result[0]['Legal_name'].'</h2>
                      </div>
                  </div>
                  
            
                ';
    $display .= '<hr><div class="col-md-12 col-sm-12 col-lg-12 ">
  
                    <ul class="list-unstyled user_data">
                      <li class="text-capitalize">
                        <i class="fa fa-map-marker user-profile-icon"></i> Address -> '.$address.'
                      </li>
  
                      <li class="text-capitalize text-jusitfy">
                        <i class="fa fa-briefcase user-profile-icon"></i> Ownership -> '.$result[0]['foo'].'
                      </li>
                      <li class="text-capitalize text-jusitfy">
                      <i class="fa fa-industry user-profile-icon"></i> Industry -> '.$result[0]['title'].'
                      </li>
                      <li class="text-capitalize text-jusitfy">
                      <i class="fa fa-envelope user-profile-icon"></i> Email -> '.$result[0]['Email'].'
                      </li>
                      <li class="text-capitalize text-jusitfy">
                      <i class="fa fa-phone user-profile-icon"></i> Contact -> '.$result[0]['Contact'].'
                      </li>
                    </ul>
  
                  
                  </div>
    <!-- start skills -->
      ';
      $display .= '<div class="row" style="width: 100% !important ">
  
        <h4 class="profile_title h2 col-lg-12 col-md-12 ">Company Statements</h4><br>
        <table class="col-lg-9 col-md-12 col-sm-12`" style="width: 100% !important ">
              <tbody style="width: 100% !important " >
                <tr class="border-bottom" style="width: 100%">
                  <td style="padding: 10px !important; margin:5px !important; "><p class="col-lg-3 col-md-3 col-sm-3" >Introduction</p></td>
                  <td style="padding: 10px !important; margin:5px !important;  "><p class="col-lg-9 col-md-9 col-sm-9" style="word-wrap: break-word !important;">'.$result[0]['introduction'].'</p></td>
                </tr>
                <tr style="width: 100%" >
                  <td  style="padding: 10px !important; margin:5px !important; "><p class="col-lg-3 col-md-3 col-sm-3" >Mission</p></td>
                  <td  style="padding: 10px !important; margin:5px !important; "><p class="col-lg-9 col-md-9 col-sm-9" style="word-wrap: break-word !important">'.$result[0]['mission'].'</p></td>
                </tr>
                <tr style="width: 100%" >
                  <td  style="padding: 10px !important; margin:5px !important; "><p class="col-lg-3 col-md-3 col-sm-3" >Vision</p></td>
                  <td  style="padding: 10px !important; margin:5px !important; "><p class="col-lg-9 col-md-9 col-sm-9" style="word-wrap: break-word !important">'.$result[0]['vision'].'</p></td>
                </tr>
                <tr style="width: 100%" >
                  <td  style="padding: 10px !important; margin:5px !important; "><p class="col-lg-3 col-md-3 col-sm-3 ">Values</p></td>
                  <td  style="padding: 10px !important; margin:5px !important; "><p class="col-lg-9 col-md-9 col-sm-9" style="word-wrap: break-word !important">'.$result[0]['values_'].'</p></td>
                </tr>
              </tbody>
        </table></div><br>
      ';
    //   <li class="text-capitalize text-jusitfy">
    //   
    // </li>
    // <li class="text-capitalize text-jusitfy">
    //  mission -> 
    // </li>
    // <li class="text-capitalize text-jusitfy">
    //  values -> '.$result[0]['values_'].'
    // </li>
  
      if($_SESSION['WHO'] == "COMPANY"){
        $COMP_ID = session::get($this->id);
        $SMME_ID = $id;
        $params = array($COMP_ID, $SMME_ID);
        $query = $this->master->select_prepared_async($this->VALIDATE_CONNECTION[0], DB_NAME_5, $this->VALIDATE_CONNECTION[1], $params);
        $connection = $this->master->getResult();
        $expenses = $this->display_expense($id, 2);//2 symbolising that it is the entity viewing the smme expenses
        // print_r($connection);
        // print_r($params);
        // exit();
          if($expenses !== -1 && !empty($connection)){
          $display .= '<hr><div class=" row ">
            <div class="col-sm-12 col-md-12 col-lg-12">
              <br><h4 class="h2 profile_title col-sm-12 col-md-12 col-lg-12 text-center">Expense Summary</h4><br>
            </div>
          </div>';
          $display .= '<div class="row">
            <h4 class="text-center">Direct Expenses</h4>
          <table class="table table-striped">
          ';
            $display .= $expenses;
            $display .= '
            <!-- start skills -->
              <hr><div class="profile_title row"><h4 class="h2 col-lg-12 col-md-12 text-center">Products</h4></section>
              <section><ul class="list-unstyled user_data">
              ';
            for($i=0; $i<=count($products)-1; $i++){
              $display.= '<li><i class="fa fa-shopping-cart"></i>  '.$products[$i]['product'].'</li>';
            }
            $display .= '</ul><hr>';
            $display .= '<!-- start of user-activity-graph -->
            
            <h2 class="profile_title">Shareholder Information</h2>
    
      <!-- end of user-activity-graph -->';
          }else{
            $display .= '
            <!-- start skills -->
              <hr><div class="profile_title row"><h4 class="h2 col-lg-12 col-md-12 text-center">Products</h4></section>
              <section><ul class="list-unstyled user_data">
              ';
            for($i=0; $i<=count($products)-1; $i++){
              $display.= '<li><i class="fa fa-shopping-cart"></i>  '.$products[$i]['product'].'</li>';
            }
            $display .= '</ul><hr>';
  
  
  
            if(!empty($connection)){
              $display .= '<!-- start of user-activity-graph -->
            
                  <h2 class="profile_title">Shareholder Information</h2>
          
            <!-- end of user-activity-graph -->';
            }
          }
      }else{
        $display .= '
        <!-- start skills -->
          <hr><div class="profile_title row"><h4 class="h2 col-md-6">Products</h4></section>
          <section><ul class="list-unstyled user_data">
          ';
        for($i=0; $i<=count($products)-1; $i++){
          $display.= '<li><i class="fa fa-shopping-cart"></i>  '.$products[$i]['product'].'</li>';
        }
        $display .= '</ul><hr>';
        
        // if(!empty($connection)){
        //   $display .= '<!-- start of user-activity-graph -->
        
        //       <h2 class="profile_title">Shareholder Information</h2>
      
          
          
        // <!-- end of user-activity-graph -->';
        // }
      }
  
      
  
  
  
  
  
  
    // $info .= "<form method='Post' action='../Main/main_notify.php?id=".$result[$i]["ID"]."'>";
    // if($this->classname == "SMME"){
    // $info .= "<button class='btn btn-primary' type='submit' name='SMME_request_notification' >";
    // }else if($this->classname == "NPO"){
    //   $info .= "<button class='btn btn-primary' type='submit' name='NPO_request_notification' >";
    // }
    // else{
    //   $info .= "<button class='btn btn-primary' type='submit' name='COMPANY_request_notification' >";
    // }
    // $info .= "Connect";
    // $info .="</button>";
    // $info .= "<button class='btn' type='submit'>Message</button>";
    // $info .= "</form>";
    // $info .= "</td>";
    // $info .= "</tr>";
    // $info .= "</table>";
    echo $display;
    }
  }

private function company_view_more($result){

    $address = $result[0]['city'].", ".$result[0]['Province'];
  
    $display = '<div class="col-md-3 col-sm-3  profile_left">
    <div class="profile_img">
      <div id="crop-avatar">
        <!-- Current avatar -->
        <img class="img-responsive avatar-view" src="'.$result[0]['ext'].'" alt="Avatar" title="Change the avatar">
      </div>
    </div>
    
    </div>
    <div class="col-md-9 col-sm-9 ">
    <h2 class="text-capitalize display-4">'.$result[0]['Legal_name'].'</h2>
  
    <ul class="list-unstyled user_data">
                      <li class="text-capitalize">
                        <i class="fa fa-map-marker user-profile-icon"></i> Address -> '.$address.'
                      </li>
  
                      <li class="text-capitalize text-jusitfy">
                        <i class="fa fa-briefcase user-profile-icon"></i> Ownership -> '.$result[0]['foo'].'
                      </li>
                      <li class="text-capitalize text-jusitfy">
                      <i class="fa fa-industry user-profile-icon"></i> Industry -> '.$result[0]['title'].'
                      </li>
                      <li class="text-capitalize text-jusitfy">
                        <i class="fa fa-envelope user-profile-icon"></i> Email -> '.$result[0]['Email'].'
                      </li>
                      <li class="text-capitalize text-jusitfy">
                      <i class="fa fa-phone user-profile-icon"></i> Contact -> '.$result[0]['Contact'].'
                      </li>
                    </ul>
  
      ';
   
  
    echo $display;
  }

  private function insert_views($id){
    $insertsql = $this->VIEWS_INSERT[0];
    $inserttypes = $this->VIEWS_INSERT[1];
    
    $insert = array("VIEW MORE", session::get($this->id), $id);
    
    $selectAndInsertTable = "entity_clicks";
    $query=$this->master->insert($selectAndInsertTable, $insertsql, $inserttypes, $insert);
    if(!$query){
      echo "this is what is wrong";
      exit();
    }
    
  }

  public function view_moreInfo($id){
    //Function view more: used to view a users profile
    //makes a call to functions insert views, fetchmore info, products, smme_view and company_view
    //Insert view -> this is for analytics, everytime user views profile, record in databse
    //fetch more info -> fetches the users information
    //products -> if it is an smme that you are viewing, then fetch products information 
    //smme_view -> if you are viewing smme, call the display that runs for smme
    //company_view more -> if you are viewing company, call the display that runs for company
    $this->insert_views($id);
    $result = $this->fetchMore_info($id);//fetch all data here including who so we can identify who they are and work with conditions in the code
    $products = array();
    if($this->classname == "COMPANY"){
      $products = $this->products($id);
      $this->smme_view($result,$products,$id);
    }else{    
        $this->company_view_more($result); 
    }
  }
  
  public function SMME_TO_SMME_view_moreInfo($id){
    $this->insert_views($id);
    $result = $this->fetchMore_info2($id);
    
    $products = array();
      $products = $this->products($id);
      $this->smme_view($result,$products,$id);
    
  }
  
  public function COMPANY_TO_COMPANY_view_moreInfo($id){
    $this->insert_views($id);
    $result = $this->fetchMore_info3($id);
    $this->company_view_more($result);
    
  }
  