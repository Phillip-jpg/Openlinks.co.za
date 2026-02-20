<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../lib/master.php');
include_once($filepath.'/../config/sql.search.config.php');
include_once($filepath.'/../config/config.php');
include_once($filepath.'/../helpers/token.php');


class search {
protected $master;
protected $id;
protected $db;
protected $who;
protected $SEARCH_INSERT;

function __construct(){
    if(isset($_SESSION['WHO'])){
        if($_SESSION['WHO'] == "SMME"){

            $this->who = 'SMME';
            $this->db = DB_NAME_1;
            $this->id=$_SESSION['SMME_ID'];
            $this->SEARCH_INSERT = SMME_SEARCH_INSERT;

        }elseif($_SESSION['WHO'] == "COMPANY"){

            $this->who = 'COMPANY';
            $this->db = DB_NAME_3;
            $this->id=$_SESSION['COMPANY_ID'];
            $this->SEARCH_INSERT = COMPANY_SEARCH_INSERT;

        }elseif($_SESSION['WHO'] == "NPO"){

            $this->who = 'NPO';
            $this->db = DB_NAME_1;
            $this->id=$_SESSION['SMME_ID'];
            $this->SEARCH_INSERT = SMME_SEARCH_INSERT;

        }elseif($_SESSION['WHO'] == "P_COMPANY"){

            $this->who = 'P_COMPANY';
            $this->db = DB_NAME_3;
            $this->id=$_SESSION['P_COMPANY_ID'];
            $this->SEARCH_INSERT = P_COMPANY_SEARCH_INSERT;

        }elseif($_SESSION['WHO'] == "CONSULTANT"){// "To be or not to be, that is the question." - William Shakespere

            $this->who = 'CONSULTANT';
            $this->db = DB_NAME_4;
            $this->id=$_SESSION['CONSULTANT_ID'];
            $this->SEARCH_INSERT = P_COMPANY_SEARCH_INSERT;

        }elseif($_SESSION['WHO'] == "M_ADMIN"){

            $this->who = 'M_ADMIN';
            $this->db = DB_NAME_6;
            $this->id=$_SESSION['ADMIN_ID'];
            $this->SEARCH_INSERT = ADMIN_SEARCH_INSERT;

        }else{

            echo $_SESSION['WHO'];
            exit();

        }
    }else{
        echo "technical error";
        exit();
    }
    $this->master = new Master($this->db);
}
private function category($term){
    $selectParams = array(array($term,$term,$term,$term),array($term,$term),array($term,$term),array($term,$term));
    
    $data = $this->master->transactionSelect(array("yasccoza_openlink_companies","yasccoza_openlink_companies","yasccoza_openlink_companies","yasccoza_openlink_companies"), array(NAME_SIMPLE_SEARCH_SELECT[0],INDUSTRY_SIMPLE_SEARCH_SELECT[0],PRODUCTS_SIMPLE_SEARCH_SELECT[0],KEYWORDS_SIMPLE_SEARCH_SELECT[0]),array(NAME_SIMPLE_SEARCH_SELECT[1],INDUSTRY_SIMPLE_SEARCH_SELECT[1],PRODUCTS_SIMPLE_SEARCH_SELECT[1],KEYWORDS_SIMPLE_SEARCH_SELECT[1]), $selectParams);
    
   
    return $data;
    
    
    
}

private function simple_search($term){
    $params = array($term, $term, $term, $term,$term, $term);
    $query=$this->master->select_prepared_async(SIMPLE_SEARCH_SELECT[0], DB_NAME_1, SIMPLE_SEARCH_SELECT[1], $params);
    if(!$query){
    echo "error error error";
    exit();
    }else{
        
        
   return $this->master->getResult();
    }
}

private function advanced_search($legalname, $industry, $office, $products, $ownership, $Entity){
  
     $sql = "";
     $union = " UNION ";
     $params = [];
     $smmenpoparams = [$legalname, $products, $industry, $ownership, $office];
     $companyparams = [$legalname, $industry, $ownership, $office];

    // merge the sql, types and params for each respectively
    //According to the 7 options available, either smmes, companies, npos, smmes and npos,
    // smmes and companies, npos and companies as well as npos, smmes and companies
    if(count($Entity)== 1){
        if($Entity[0] == "SMME"){
            $sql = SMME_SEARCH_SELECT[0];
            $type = SMME_SEARCH_SELECT[1];
            $params = $smmenpoparams;
        }elseif($Entity[0] == "COMPANY" && $products == ''){
            $sql = COMPANY_SEARCH_SELECT[0];
            $type = COMPANY_SEARCH_SELECT[1];
            $params = $companyparams;
        }elseif($Entity[0] == "NPO"){
            $sql = NPO_SEARCH_SELECT[0];
            $type = NPO_SEARCH_SELECT[1];
            $params = $smmenpoparams;
        }else{
            echo "No results found, try again.";
            exit();
        }
    }
    elseif(count($Entity)== 2){
        $first = ["SMME", "COMPANY"];
        $second = ["SMME", "NPO"];
        $third = ["NPO", "COMPANY"];

        if($products == ''){
            if(empty(array_diff($Entity, $first))){
                $sql = SMME_SEARCH_SELECT[0].$union.COMPANY_SEARCH_SELECT[0];
                $type = SMME_SEARCH_SELECT[1].COMPANY_SEARCH_SELECT[1];
                $params = array_merge($smmenpoparams, $companyparams);
            }elseif(empty(array_diff($Entity, $second))){
                $sql = SMME_SEARCH_SELECT[0].$union.NPO_SEARCH_SELECT[0];
                $type = SMME_SEARCH_SELECT[1].NPO_SEARCH_SELECT[1];
                $params = array_merge($smmenpoparams, $smmenpoparams);
            }elseif(empty(array_diff($Entity, $third))){
                $sql = NPO_SEARCH_SELECT[0].$union.COMPANY_SEARCH_SELECT[0];
                $type = NPO_SEARCH_SELECT[1].COMPANY_SEARCH_SELECT[1];
                $params = array_merge($smmenpoparams, $companyparams);
            }else{
                echo "Processing error, please try again 2";
                exit();
            }
        }else{
            if(empty(array_diff($Entity, $first))){
                $sql = SMME_SEARCH_SELECT[0];
                $type = SMME_SEARCH_SELECT[1];
                $params = $smmenpoparams;
            }elseif(empty(array_diff($Entity, $second))){
                $sql = SMME_SEARCH_SELECT[0].$union.NPO_SEARCH_SELECT[0];
                $type = SMME_SEARCH_SELECT[1].NPO_SEARCH_SELECT[1];
                $params = array_merge($smmenpoparams, $smmenpoparams);
            }elseif(empty(array_diff($Entity, $third))){
                $sql = NPO_SEARCH_SELECT[0];
                $type = NPO_SEARCH_SELECT[1];
                $params = $smmenpoparams;
            }else{
                echo "Processing error, please try again 2";
                exit();
            }
        }
    }
    elseif(count($Entity)== 3){
        if($products == ''){
        $sql = SMME_SEARCH_SELECT[0].$union.NPO_SEARCH_SELECT[0].$union.COMPANY_SEARCH_SELECT[0];
        $type = SMME_SEARCH_SELECT[1].NPO_SEARCH_SELECT[1].COMPANY_SEARCH_SELECT[1];
        $params = array_merge($smmenpoparams, $smmenpoparams, $companyparams);
        }
        else{
            $sql = SMME_SEARCH_SELECT[0].$union.NPO_SEARCH_SELECT[0];
            $type = SMME_SEARCH_SELECT[1].NPO_SEARCH_SELECT[1];
            $params = array_merge($smmenpoparams, $smmenpoparams);
        }
    }else{
        echo "Processing error, please try again 3";
        exit();
    }
    $query=$this->master->select_prepared_async($sql, DB_NAME_1, $type, $params);
    if(!$query){
    echo "error error error";
    echo "<b>$sql</b>";
    exit();
    }else{
    return $this->master->getResult();
    }
}

private function insert($hits, $simple, $legal_name, $industry, $products_services, $foo,$keyword){
    $this->master->changedb($this->db);
    if($this->who ==  'P_COMPANY'){
        $params=array($hits, $this->id, $this->who);
    }else{
        $params=array($hits, $this->id);
    }
    $query=$this->master->Insert('search', $this->SEARCH_INSERT[0], $this->SEARCH_INSERT[1], $params);
    if(!$query){
        echo "<strong>Broom! 1 ".$this->SEARCH_INSERT[0]." || ".$hits." || ".$this->db."</strong>";
        exit();
    }
    $idarray = $this->master->getResult();
    $id = $idarray[0];
    if($simple!==''){

        $params=array($simple, "Legal Name", $id);
        $query=$this->master->Insert('search_terms', SEARCH_TERM_INSERT[0], SEARCH_TERM_INSERT[1], $params);
        if(!$query){
            echo "<strong>Broom! 2/1</strong>";
            exit();
          }
    }
    if($legal_name!==''){
        $params=array($legal_name, "Legal Name", $id);
        $query=$this->master->Insert('search_terms', SEARCH_TERM_INSERT[0], SEARCH_TERM_INSERT[1], $params);
        if(!$query){
            echo "<strong>Broom! 2</strong>";
            exit();
          }
    }
    if($industry!==''){
        $params=array($industry, "Industry", $id);
        $query=$this->master->Insert('search_terms', SEARCH_TERM_INSERT[0], SEARCH_TERM_INSERT[1], $params);
        if(!$query){
            echo "<strong>Broom! 3</strong>";
            exit();
          }
    }
    if($products_services!==''){
        $params=array($products_services, "Products", $id);
        $query=$this->master->Insert('search_terms', SEARCH_TERM_INSERT[0], SEARCH_TERM_INSERT[1], $params);
        if(!$query){
            echo "<strong>Broom! 4</strong>";
            exit();
          }
    }
    if($foo!==''){
        $params=array($foo, "Form of Ownership", $id);
        $query=$this->master->Insert('search_terms', SEARCH_TERM_INSERT[0], SEARCH_TERM_INSERT[1], $params);
        if(!$query){
            echo "<strong>Broom! 5</strong>";
            exit();
          }
    }
    if($keyword!==''){
        $params=array($keyword, "keyword", $id);
        $query=$this->master->Insert('search_terms', SEARCH_TERM_INSERT[0], SEARCH_TERM_INSERT[1], $params);
        if(!$query){
            echo "<strong>Broom! 5</strong>";
            exit();
          }
    }
}

private function data($result){
    
    $output = '';
    for($i=0; $i<=count($result)-1; $i++){
        $output .= '<div class="col-md-4 col-sm-4 col-xs-12 profile_details search" style="width:500px;">
        <div class="well profile_view col-md-12" >
            <div class="col-sm-12">
            <h2 class="brief text-center">'. $result[$i]['Legal_name'].'</h2>
            <div class="left col-xs-7">
                <h4><i>'. $result[$i]['typeOfEntity'] .'</i></h4>
                <br>
                <p><strong>Industry: </strong> '. $result[$i]['title'] .' </p>
                <ul class="list-unstyled">
                <br>
                <li><strong> Address:</strong> '. $result[$i]['Address'] .' </li>
                </ul>
                <ul class="list-unstyled">
                <br>
                <li><strong> Form of onwership:</strong> '. $result[$i]['foo'] .' </li>
                </ul>
               
                
            </div>
            <div class="right col-xs-5 text-center">
                <img src="'. $result[$i]['ext'] .'" alt="" class="img-circle img-responsive" style="height:150px !important; width: 150px !important">
            </div>
            </div>
            ';if($result[$i]['typeOfEntity']=="SMME"){
                $output .= '<div class="col-xs-12 bottom text-center" style="background-color: #337AB7;">';
            }elseif($result[$i]['typeOfEntity'] == "NPO"){
                $output .= '<div class="col-xs-12 bottom text-center" style="background-color: rgb(173, 199, 195);">';
            }else{
                $output .= '<div class="col-xs-12 bottom text-center" style="background-color:#337AB7;">';
            }
            if($result[$i]['typeOfEntity']=="SMME" || $result[$i]['typeOfEntity']=="NPO"){
                $rating = $result[$i]['rating'];
                if($rating == 100){
                    $output .= '<div class="col-xs-12 col-sm-6 emphasis" style="color:green">
                    <p class="ratings" >
                    <a style="color:white">5.0</a>
                    <a href="#"><span class="fa fa-star" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star" style="color:white"></span></a>
                    </p>
                </div>
                ';
                    $id = token::encode($result[$i]["ID"]);
                    $output .=  "<form method='POST' action='view_more.php?t=".token::encode($result[$i]['typeOfEntity'])."&id=".$id."'>";
                    $output .= "<input type='text' name='tk' value='".token::get_ne("VIEW_MORE_YASC")."' required='' hidden>";
                }else if($rating >= 80){
                    $output .= '<div class="col-xs-12 col-sm-6 emphasis">
                    <p class="ratings">
                    <a style="color:white">4.0</a>
                    <a href="#"><span class="fa fa-star" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star-o" style="color:white"></span></a>
                    </p>
                </div>
                ';
                    
                }else if($rating >= 60){
                    $output .= '<div class="col-xs-12 col-sm-6 emphasis">
                    <p class="ratings">
                    <a style="color:white">3.0</a>
                    <a href="#"><span class="fa fa-star" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star-o" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star-o" style="color:white"></span></a>
                    </p>
                </div>
                ';
                }else if($rating >= 40){
                    $output .= '<div class="col-xs-12 col-sm-6 emphasis">
                    <p class="ratings">
                    <a style="color:white">2.0</a>
                    <a href="#"><span class="fa fa-star" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star-o" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star-o" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star-o" style="color:white"></span></a>
                    </p>
                </div>
                ';
                }else if($rating >= 20){
                    $output .= '<div class="col-xs-12 col-sm-6 emphasis">
                    <p class="ratings">
                    <a style="color:white">1.0</a>
                    <a href="#"><span class="fa fa-star" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star-o" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star-o" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star-o" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star-o" style="color:white"></span></a>
                    </p>
                </div>
                ';
                }else{
                    $output .= '<div class="col-xs-12 col-sm-6 emphasis d-flex">
                    <p class="ratings">
                    <a style="color:white">0</a>
                    <a href="#"><span class="fa fa-star-o" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star-o" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star-o" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star-o" style="color:white"></span></a>
                    <a href="#"><span class="fa fa-star-o" style="color:white"></span></a>
                    </p>
                    </div>
                 ';
                }
                
                
            }
                    // <i class="fa fa-comments-o"></i> 
                    $id = token::encode($result[$i]["ID"]);
                    $output .=  "<div class='row'>
                    <div class='col-xs-12 col-sm-6 emphasis' style='display:flex'>
                    <div style='flex:1; margin-left:80px'>

                    <a href='chat.php?id=".$id."' class='btn btn-success btn-xs' style='background-color:#0DC0ff; font-size:15px; marigin-left:20px;  height:30px !important; width: 33px !important'>

                    <i class='fa fa-envelope' style='font-size:15px;' ></i>
                     </a>
                    </div>

                   
                   
                    <form method='POST' action='view_more.php?t=".token::encode($result[$i]['typeOfEntity'])."&id=".$id."'>";
                    
                    $output .= "<input type='text' name='tk' value='".token::get_ne("VIEW_MORE_YASC")."' required='' hidden>";
                    
                    $output .= "<div style='flex:0.2; margin-right:50px'>  <button type='submit' name='VIEW_MORE' class='btn ' data-toggle='tooltip' 
                    data-placement='top' title='View More Information'><i class='fa fa-address-card'></i></button></form>
                    </div>
                    </div>
                    </div>";
                    $output .= ' 
                </div>
            </div>
        </div>
        </div>
        </div>';
    }
    return $output;
}

public function search($simple, $legal_name = '', $industry = '', $products_services = '', $foo = '', $office = '', $array = []){
    $keyword ='';
    if(!empty($simple) && (empty($legal_name) || empty($industry) || empty($products_services) || empty($foo))){//simple

        $result = $this -> simple_search($simple);
        $data = $this -> category($simple);
        

        
        if(!empty($legal)){
            $simple = $simple;
        }if(!empty($indus)){
            $industry = $simple;
        }if(!empty($products)){
            $products_services = $simple;
        }if(!empty($keywords)){
            $keyword = $simple;
        }

    }elseif(!empty($array) && (!empty($legal_name) || !empty($industry) || !empty($products_services) || !empty($foo))){//advanced

        $result = $this -> advanced_search($legal_name, $industry, $office, $products_services, $foo, $array);

    }else{//empty inputs
        echo "No results found, try again.";
        exit();
    }
    
    $this -> insert (count($result), $simple, $legal_name, $industry, $products_services, $foo,$keyword);

    if(empty($result)){

        $output =  "No results found, try again.";

    }else{

        $output =  $this->data($result);
    }

    echo $output;

}

// function individual_rating($id){//works out a system rating for a single smme
//     $query = $this->master->select_prepared_async(ENTITY_RATING_SELECT[0], 'smmes', ENTITY_RATING_SELECT[1],array($id, $id));
//     // $query=$this->master->select_multiple_async($this->COMPARITIVE_CHART[0], 'smmes');
//     if(!$query){
//       echo "query 1 error";
//       echo implode("", $this->master->connresult);
//       exit();
//     }else{
//       $result=$this->master->getResult();
//         if(empty($result)){
//             return 0;
//         }
//             return $rating;
//     }
//     }
   


    }
