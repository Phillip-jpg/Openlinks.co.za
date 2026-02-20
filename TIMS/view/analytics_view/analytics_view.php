<?php

class ANALYTICS_VIEW {

static function BBBEE_stats($requests_made, $requests_received, $connections, $finalised){
    $display = '<div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 tile_stats_count">
    <span class="count_top"><i class="fa fa-envelope"></i> Requests Made </span>
    <div class="count text-center">'.$requests_made["requests"].'</div>
  </div>

  <div class=" col-lg-3 col-md-6 col-sm-6 col-xs-6 tile_stats_count">
    <span class="count_top"><i class="fa fa-envelope-open"></i> Requests Received </span>
    <div class="count text-center"> '.$requests_received["requests"].' </div>
  </div>

  <div class=" col-lg-3 col-md-6 col-sm-6 col-xs-6 tile_stats_count">
    <span class="count_top"><i class="fa fa-hands"></i>Connections</span>
    <div class="count text-center"> '.$connections["connections"].' </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 tile_stats_count">
    <span class="count_top"><i class="fa fa-trophy"></i> Finalized Companies</span>
    <div class="count text-center"> '.$finalised['finalised']. '</div>
  </div>';
  return $display;
}

static function marketplace($view_more, $web_link_visits,$intention_to_engage, $enganged ){
  $display = '
<div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 tile_stats_count">
  <span class="count_top"><i class="fa fa-eye"></i> Profile Views</span>
  <div class="count text-center"> '.$view_more["views"].' </div>
</div>

<div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 tile_stats_count">
  <span class="count_top"><i class="fa fa-eye"></i> Website Visits</span>
  <div class="count text-center"> '.$web_link_visits['web_visits'].' </div>
</div>
<div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 tile_stats_count">
  <span class="count_top"><i class="fa fa-envelope"></i> Intention To Engage </span>
  <div class="count text-center">'.$intention_to_engage.'</div>
</div>

<div class=" col-lg-3 col-md-6 col-sm-6 col-xs-6 tile_stats_count">
  <span class="count_top"><i class="fa fa-envelope-open"></i> Engaged </span>
  <div class="count text-center">'.$enganged['engaged'].'</div>
</div>';
return $display;
}
static function company_profile_stats_view($result ){
  
  $incomplete = array_shift($result);
  $complete = $result[0];
  $display = "<ul class='not_completed_profile'>";
  for($i = 0 ; $i <= count($incomplete)-1; $i++){
    
      foreach($incomplete[$i] as $key => $value ){
      
        $display .= "<li>".strtoupper($key)."</li>";
      }
  }
  
  $display .= "</ul>";
  $display .= "<ul class='completed_profile'>";
  for($i = 0 ; $i <= count($complete)-1; $i++){
   
      foreach($complete[$i] as $key => $value ){
      
        $display .= "<li>".strtoupper($key)."</li>";
      }
  }
  $display .= "</ul>";
  return $display;
  
return $display;
}
static function smme_profile_stats_view($result){
  $incomplete = array_shift($result);
  $complete = $result[0];
  $display = "<ul class='not_completed_profile'>";
  for($i = 0 ; $i <= count($incomplete)-1; $i++){
    
      foreach($incomplete[$i] as $key => $value ){
      
        $display .= "<li>".strtoupper($key)."</li>";
      }
  }
  
  $display .= "</ul>";
  $display .= "<ul class='completed_profile'>";
  for($i = 0 ; $i <= count($complete)-1; $i++){
   
      foreach($complete[$i] as $key => $value ){
      
        $display .= "<li>".strtoupper($key)."</li>";
      }
  }
  $display .= "</ul>";
  return $display;
}
// static function profile_stats_view($result ){
//   $display .= "<ul>";
//   for($i=0; $i<=count($result)-1; $i++){
//     $status = $result[$i];
//     switch($status){
//       case 1:
//         $class = "completed_profile";
//         break;
//       case 0:
//         $class = "not_completed_profile";
//         break;
//     }
//       $display .= "<li></li>";
//   }
//   $display .= "</ul>";
// return $display;
// }


}