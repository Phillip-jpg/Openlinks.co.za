<?php

class VIEW {

    static function page_visits($min, $max, $average){
        echo '<div>
        <ul style>
          <li>Max Page Visits: '.$min['Visits'].'</li>
          <li>Min Page Visits: '.$max['Visits'].'</li>
          <li>Average Page Visits: '.$average['average_visits'].'</li>
        </ul>
      </div>';
    }

    static function total_users_stats($smme, $company, $total,$current_day_searches){
      $display = '
    
      <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3 tile_stats_count">
        <span class="count_top text-center"><i class="fa fa-link"></i> Total Users </span>
        <div class="count text-center">'.$total.'</div>
      </div>
      <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3 tile_stats_count">
        <span class="count_top text-center"><i class="fa fa-envelope"></i> SMME Users</span>
        <div class="count text-center">'.$smme.'</div>
      </div>
      <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3 tile_stats_count">
        <span class="count_top text-center"><i class="fa fa-envelope-open"></i> COMPANY Users </span>
        <div class="count text-center"> '.$company.'</div>
      </div>
      <div class="col-md-3 col-sm-3 col-xs-3  col-lg-3 tile_stats_count">
        <span class="count_top text-center"><i class="fa fa-trophy"></i> Current Days Searches</span>
        <div class="count text-center"> '.$current_day_searches.' </div>
      </div>';

      //   <div class="col-md-2 col-sm-4  tile_stats_count">
      //   <span class="count_top"><i class="fa fa-user"></i> Total Users</span>
      //   <div class="count text-center">'.$total.'</div>
        
      // </div>
      // <div class="col-md-2 col-sm-4  tile_stats_count">
      //   <span class="count_top"><i class="fa fa-clock-o"></i> SMME Users</span>
      //   <div class="count text-center">'.$smme.'</div>
        
      // </div>
      // <div class="col-md-2 col-sm-4  tile_stats_count">
      //   <span class="count_top"><i class="fa fa-user"></i> COMPANY Users</span>
      //   <div class="count text-center">'.$company.'</div>
        
      // </div>
      // <div class="col-md-2 col-sm-4  tile_stats_count">
      //   <span class="count_top"><i class="fa fa-user"></i> Current Days Searches</span>
      //   <div class="count text-center">'.$current_day_searches.'</div>
        
      // </div>
      // <div class="col-md-2 col-sm-4  tile_stats_count">
      //   <span class="count_top"><i class="fa fa-user"></i> Total Emails Sent</span>
      //   <div class="count text-center">0</div>
      // </div>
      // <div class="col-md-2 col-sm-4  tile_stats_count">
      //   <span class="count_top"><i class="fa fa-user"></i> Total Email links clicked</span>
      //   <div class="count text-center">0</div>
      // </div>
    
      echo $display;
    }

    static function search_stats($most_searched_name, $most_searched_industry, $most_searched_product){
        $display = '<div>
        <ul>
        ';
        if(!empty($most_searched_name) && !empty($most_searched_industry) && !empty($most_searched_product)){
          $display .= '<li>Most Searched Name: '.$most_searched_name[0]['term_name'].'</li>
          <li>Most Searched Industry: '.$most_searched_industry[0]['term_name'].'</li>
          <li>Most Searched Product: '.$most_searched_product[0]['term_name'].'</li>
        </ul>
      </div>';
        }
        if(!empty($most_searched_name) && !empty($most_searched_industry) && empty($most_searched_product)){
          $display .= '<li>Most Searched Name: '.$most_searched_name[0]['term_name'].'</li>
          <li>Most Searched Industry: '.$most_searched_industry[0]['term_name'].'</li>
        </ul>
      </div>';
        }
        if(!empty($most_searched_name) && empty($most_searched_industry) && !empty($most_searched_product)){
          $display .= '<li>Most Searched Name: '.$most_searched_name[0]['term_name'].'</li>
          <li>Most Searched Product: '.$most_searched_product[0]['term_name'].'</li>
        </ul>
      </div>';
        }
        if(empty($most_searched_name) && !empty($most_searched_industry) && !empty($most_searched_product)){
          $display .= '<li>Most Searched Industry: '.$most_searched_industry[0]['term_name'].'</li>
          <li>Most Searched Product: '.$most_searched_product[0]['term_name'].'</li>
        </ul>
      </div>';
        }
        if(empty($most_searched_name) && empty($most_searched_industry) && empty($most_searched_product)){
          $display .= '<p>No Stats Yet</p>';
        }
        echo $display;
          
    }

    static function myBBBEE($result, $stats){
      if(empty($result)){
        echo '<div>
        <p>No Stats Yet</p>
      </div>';
      }else{
        echo '<div>
        <ul>
          <li>Requests: '.$stats["requests"].'</li>
          <li>Connections: '.$stats["connections"].'</li>
          <li>Finalized: '.$stats["finalized"].'</li>
          <li>Average Time For Process Life Cycle: '.$result[0]["Average_time"].'</li>
        </ul>
      </div>';
      }
    }

    static function consultant($result){
        echo '<div class="x_title">
                <h2>Summary</h2>
                <div class="clearfix"></div>
            </div>
            
            <div class="col-md-12 col-sm-12 ">
                <div>
                <p>Number of searches: </p>
                
                </div>
                <div>
                <p>Average hits: </p>
                
                </div>
            </div>
            <div class="col-md-12 col-sm-12 ">
                <div>
                <p>Daily searches</p>
                
                </div>
                <div>
                <p>Most searched Term</p>
                
                </div>
                <div>
                <p>Searches Not found:</p>
                
                </div>
      </div>';
    }

    public function displayFILES($result){

      
      echo "<table class='table-responsive table table-striped smme_entity_table' id='dataTable' width='100%' cellspacing='0'>";
      echo  "<thead>";
      echo     "<tr>";

      for($i = 0; $i < count($result); $i++){
         echo "<th> ".$result[$i]['type']."</th>";
      }
        echo "<th>Physcial Location</th>";
      echo     "</tr>";
      echo   "</thead>";
      echo "<tr>";
      for($i=0; $i<=count($result)-1; $i++){//row       
        echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''><a href='../STORAGE/FILES/".$result[$i]["userID"]."' download='../STORAGE/FILES/".$result[$i]["userID"]."'>
        <img src='../Images/PDF_file_icon.png' height=50 width=50></a></br></td>";
        
      }
      echo "<td>".$result[0]['Address'].", ".$result[0]['city']."</td>";
      echo "</tr>";
      //buttons
      echo "<tr>";
      
      for($i = 0; $i < count($result); $i++){
        if($result[$i]['verified'] == 0){//not verified
          echo "<td><form method='POST' action='../Main/Main_ADMIN.php'>";
          echo "<input type='text' value='".$result[$i]['link']."' name='userID' hidden >";
          echo "<input type='text' value='".$result[$i]['type']."' name='type' hidden >" ;
          echo "<input value='Verify' class='btn btn-primary' type='submit' name='ADMIN_VERIFY' >";//add token
          echo "</form></td>";
        }else{
          echo "<td>Verified</td>";
        }
        
      }
      echo "</tr>";
      echo "</table>";

      // echo "<a href='view_more.php?t=".$type_of_entity."&i=".$id."' class='btn' type='button' >View More</button></td>";


      // $this->admin($admin);
      //$this->register($register);
     //$this->keywords($keywords);
      //$this->links($links, $names);
  }

  private function Ids($result){
    $display = "";
    $display .= '<div class="x_panel" ">
        ';
        if(empty($result) ){
            $display .= '<div class="text-center"><div class="col-6"><h3>No information filled in</h3> </div> 
            </div>';
        }else{
          $display .= '<table class="table">
          <thead>
          <th>File</th>
            <th>User</th>
            <th>Uploaded</th>
            <th>Verify</th>
          </thead>
          <tbody>';
          for($i = 0; $i < count($result); $i++){
            $time = date_create($result[$i]["date"]);
            $date = date_format($time, "Y/m/d");
            $display .= '<tr>
            <td><a href="../STORAGE/FILES/'.$result[$i]['link'].'" download="../STORAGE/FILES/'.$result[$i]['link'].'"><img src="../Images/PDF_file_icon.png" height=50 width=50> </a> </td>
            <td><strong>'.$result[$i]["Trade_Name"].'</strong>
           
            <td>'.$date.'</td>
            <td><button class="btn btn-success">Verify <i class="fa fa-tick"></i></button></td></tr>';
          }
          $display .= '</tbody></table>'
          ;
        }
        
        $display .= '</div>';
        return $display;
   

}
private function Registration($result){
  $display = "";
  $display .= '<div class="x_panel" ">
      ';
      if(empty($result) ){
          $display .= '<div class="text-center"><div class="col-6"><h3>No information filled in</h3> </div> 
          </div>';
      }else{
        $display .= '<table class="table">
        <thead>
        <th>File</th>
          <th>User</th>
          <th>Uploaded</th>
          <th>Verify</th>
        </thead>
        <tbody>';
        for($i = 0; $i < count($result); $i++){
          $time = date_create($result[$i]["date"]);
          $date = date_format($time, "Y/m/d");
          $display .= '<tr>
          <td><a href="../STORAGE/FILES/'.$result[$i]['link'].'" download="../STORAGE/FILES/'.$result[$i]['link'].'"><img src="../Images/PDF_file_icon.png" height=50 width=50> </a> </td>
          <td><strong>'.$result[$i]["Trade_Name"].'</strong>
         
          <td>'.$date.'</td>
          <td><button class="btn btn-success">Verify <i class="fa fa-tick"></i></button></td></tr>';
        }
        $display .= '</tbody></table>'
        ;
      }
      
      $display .= '</div>';
      return $display;
 

}
private function BBBEE($result){
  $display = "";
  $display .= '<div class="x_panel" ">
     ';
      if(empty($result)){
          $display .= '<div class="text-center"><div class="col-6"><h3>No information filled in</h3> </div> 
          </div>';
      }else{
        $display .= '<table class="table">
          <thead>
          <th>File</th>
            <th>User</th>
            <th>Uploaded</th>
            <th>Verify</th>
          </thead>
          <tbody>';
          for($i = 0; $i < count($result); $i++){
            $time = date_create($result[$i]["date"]);
            $date = date_format($time, "Y/m/d");
            $display .= '<tr>
            <td><a href="../STORAGE/FILES/'.$result[$i]['link'].'" download="../STORAGE/FILES/'.$result[$i]['link'].'"><img src="../Images/PDF_file_icon.png" height=50 width=50> </a> </td>
            <td><strong>'.$result[$i]["Trade_Name"].'</strong>
           
            <td>'.$date.'</td>
            <td><button class="btn btn-success">Verify <i class="fa fa-tick"></i></button></td></tr>';
          }
          $display .= '</tbody></table>'
          ;
      }
      
      $display .= '</div>';
      return $display;
 

}
}