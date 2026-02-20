<?php 

$filepath = realpath(dirname(__FILE__,2));

use PHPMailer\PHPMailer\Exception;

//require $filepath.'/classes/mail.extend.php';
//require $filepath.'/mail_body.class.php';
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../config/sql.config.php');
include_once($filepath.'/../config/config.php');
include_once($filepath.'/../helpers/token.php');
//include_once('notification_body.class.php');
include_once('SCORECARD.php');
include_once('CRITERIA.php');
include_once('PDF.php');
class USER{
    private $ID;
    private $TYPE_ENTITY;
    protected $master;
    protected $scorecards;
    protected $criteria;

    function __construct($id, $type){
        $this->ID = $id;
        $this->TYPE_ENTITY = $type;
        $this->master=new Master("yasccoza_openlink_market");
    }
  
    public function createScoreCard($title, $other, $date){
       
      
        //handle the next step -> send to criteria page
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $id = $this->saveScoreCard($title, $other, $date, "USER");
                $entity = 1;
                break;
            case "COMPANY":
                $id = $this->saveScoreCard($title, $other, $date, "USER");
                $entity = 2;
                break;
            case "ADMIN":
                $id = $this->saveScoreCard($title, $other, $date, "OPENLINKS");
                $entity = 3;
                break;
        }
        header("location: ../".$this->TYPE_ENTITY."/scorecard_criteria.php?w=".$id."&t=".$entity."&result=success");
        exit();
    }
    public function createCriteria($name,$description, $documents, $dest){
        

       
        $this->saveCriteria($name,$description, $documents);
        $card = $this->fetchScoreCard($this->ID);
        $scorecard = $card[0]['SCORECARD_ID'];
        $criteria_id = $this->fetchCriteria($this->ID);
         //handle the next step -> send to criteria page
         $entity = 0;
         switch($this->TYPE_ENTITY){
             case "SMME":
                 $entity = 1;
                 break;
             case "COMPANY":
                 $entity = 2;
                 break;
             case "ADMIN":
                 $entity = 3;
                 break;
         }
         
      
        if($dest == 1){
            $criteria = $criteria_id[0]['CRITERIA_ID'];
            $this->saveScorecardCriteria($scorecard, $criteria,100);

            header("location: ../".$this->TYPE_ENTITY."/scorecard_questions.php?t=".$entity."&result=success");
            exit();
        }else{
            header("location: ../".$this->TYPE_ENTITY."/criteria_view.php?t=".$entity."&result=success");
            exit();
        }
       
    }
    public function createQuestionEdit($question,  $weight, $criteria_id){      
        
        
        for($i = 0; $i < count($question); $i++){
            $this->saveQuestion($question[$i], $weight[$i], $criteria_id);
        }
        //handle the next step -> send to criteria page
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        header("location: ../".$this->TYPE_ENTITY."/criteria_information.php?t=".$entity."&w=".$criteria_id."&result=success");
        exit();
    }

    public function createQuestionWizard($question,  $weight){   
        

        
        $criteria=$this->fetchCriteria($this->ID);    
        for($i = 0; $i < count($question); $i++){
            $this->saveQuestion($question[$i], $weight[$i], $criteria[0]['CRITERIA_ID']);
        }
        //handle the next step -> send to criteria page
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        $scorecard = $this->fetchScoreCard($this->ID);
        header("location: ../".$this->TYPE_ENTITY."/scorecard_finalview2.php?t=".$entity."&w=".$scorecard[0]["SCORECARD_ID"]."&result=success");
        exit();
    }
    public function createOptions($question,  $weight, $id,$whereFrom,$scorecard_id){       
        for($i = 0; $i < count($question); $i++){
            $this->saveOption($question[$i], $weight[$i], $id);
        }
        //handle the next step -> send to criteria page
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        $card=$this->fetchQuestion($id);
        header("location: ../".$this->TYPE_ENTITY."/question_options.php?d=".$whereFrom."&s=".$scorecard_id."&t=".$entity."&w=".$id."&result=success");
        exit();
    }
    public function saveUpdate($title, $other, $date,$id, $criteria=null, $weight=null){
        //save scorecard criteria relationship
        if($criteria != null && $weight != null){
            for($i =0; $i < count($criteria); $i++){
                $this->saveScorecardCriteria($id, $criteria[$i], $weight);
            }
        }
        
        //update scorecard info
        $this->updateScoreCard($title,$other, $date, $id);
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        header("location: ../".$this->TYPE_ENTITY."/scorecard_finalview2.php?w=".$id."&t=".$entity."&result=success");
        exit();
        
    }
    public function saveCriteriaUpdate($name, $desc, $doc, $id){

        $this->updateCriteria($name, $desc, $doc, $id);
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        
        header("location: ../".$this->TYPE_ENTITY."/criteria_information.php?w=".$id."&t=".$entity."&result=success");
        exit();
        
    }
    public function saveWeightAdjust($weight, $scorecard, $criteria){

        $this->updateWeightAdjust($weight, $scorecard, $criteria);
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        header("location: ../".$this->TYPE_ENTITY."/scorecard_finalview2.php?w=".$scorecard."&t=".$entity."&result=success");
        exit();
        
    }
    public function saveQuestionUpdate($question, $weight, $id){

        $this->updateQuestion($question, $weight, $id);
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        $question = $this->fetchQuestion($id);
        $criteria_id = $question[0]['CRITERIA_ID'];
        header("location: ../".$this->TYPE_ENTITY."/criteria_information.php?t=".$entity."&w=".$criteria_id."&result=success");
        exit();
        
    }
    public function saveOptionUpdate($choice, $weight, $id, $scorecard, $from){
        
        $this->updateOption($choice, $weight, $id);
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        $option = $this->fetchOption($id);
        $question_id = $option[0]['QUESTION_ID'];
        $question = $this->fetchQuestion($question_id);
        $criteria_id = $question[0]['CRITERIA_ID'];
        header("location: ../".$this->TYPE_ENTITY."/question_options.php?d=".$from."&s=".$scorecard."&w=".$criteria_id."&t=".$entity."&result=success");
        exit();
        
    }
    public function createClient($company, $city, $province,$office, $industries){       
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        $member_number = $this->saveClient($company, $city, $province, $office, $industries);
        header("location: ../".$this->TYPE_ENTITY."/client_view.php?t=".$entity."&result=success");
        exit();
    }
    public function createRep($name, $surname, $email,$contact, $role, $client_id){       
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        $member_number = $this->saveRep($name, $surname, $email,$contact, $role, $client_id);
        header("location: ../".$this->TYPE_ENTITY."/client_view.php?t=".$entity."&result=success");
        exit();
    }
    private function saveClient($company, $city, $province, $office, $indusrty){
        $sql ="INSERT INTO `client`( `company_name`, `city`, `province`, office_id, industry_id) VALUES (?,?,?,?,?)";
        $types="sssii";
        $params = array($company, $city, $province, $office, $indusrty);
        $table = "client";
        $query = $this->save($sql, $types, $params, $table);
        return $query;
    }
    private function saveRep($name, $surname, $email,$contact, $role, $client_id){
        $sql ="INSERT INTO yasccoza_tms_db.client_rep( `REP_NAME`, `REP_EMAIL`, `REP_CONTACT`, ROLE, CLIENT_ID, USER_CREATED) VALUES (?,?,?,?,?,?)";
        $types="ssisii";
        $name = $name." ".$surname;
        $params = array($name, $email,$contact, $role, $client_id, $this->ID);
        $table = "client_rep";
        $query = $this->save($sql, $types, $params, $table);
        return $query;
    }
    public function createPost($office,$title, $client,$description, $start_date, $start_end,$worktype, $scorecard_id, $jobOrderType, $fileName,$fileTmpName,$fileSize,$fileError,$client_rep, $expenses=null){
        $empty="";
        $scorecard = $this->fetchCardById($scorecard_id);
        $client_fetched = $this->fetchClientByID($client);
        $fails = array("too large", "file error","not right file");
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $enity = 2;
                break;
            case "ADMIN":
                $enity = 3;
                break;
        }
        //case 1 : 1 admin found
        //case 2 : more than 1 found
        //case 3 : no admin found
        $return_value =0;
        if(strcmp($this->TYPE_ENTITY,"ADMIN" )==0){
            $admin_id = $this->fetchSectorAdmin($client);
            if(count($admin_id) > 1){
                $return_value = 2;
                $post_id = $this->savePost($office,$title,$client,$client_rep, $description,$start_date, $start_end,$worktype, $jobOrderType,$scorecard_id,1);
                $this->insertPostAdmins($post_id, $admin_id);
            }else if(count($admin_id) == 0){
                $return_value = 3;
                $post_id = $this->savePost($office,$title,$client,$client_rep, $description,$start_date, $start_end,$worktype, $jobOrderType,$scorecard_id,0);
            }
            else{
                $return_value=1;
                $admin_id = $admin_id[0]['Result'];
                $post_id = $this->savePost($office,$title,$client,$client_rep, $description,$start_date, $start_end,$worktype, $jobOrderType,$scorecard_id, $admin_id);
            }    
        }else{
            $id = $this->fetchSectorAdmin($this->ID);
            if(count($id) > 1){
                $post_id = $this->savePost($office,$title,$client,0, $description,$start_date, $start_end,$worktype,$jobOrderType, $scorecard_id, 1, $expenses);
                $this->insertPostAdmins($post_id, $id);
            }else{
                $id = $id[0]['Result'];
                $post_id = $this->savePost($office,$title,$client, 0,$description,$start_date, $start_end,$worktype,$jobOrderType, $scorecard_id, 1, $expenses);
            }
            
        }
        for($i =0; $i < count($fileName);$i++){
           $url = $this->UploadFile($jobOrderType,$fileName[$i],$fileTmpName[$i],$fileSize[$i],$fileError[$i]); 
           if(in_array($url, $fails)){
                header("location: ../".$this->TYPE_ENTITY."/market_posts.php?t=".$entity."&result=".$url."");
            }else{
                $rfp_id = $this->saveRFQ($empty,$url, $post_id);
            }
        }
        if(strcmp($expenses, "YES")==0){
                header("location: ../".$this->TYPE_ENTITY."/attachExpenses.php?w=".$post_id."&t=".$entity."&result=success");
                exit();
            }else{
                
                 if(strcmp($this->TYPE_ENTITY,"ADMIN" )==0){
                    header("location: ../".$this->TYPE_ENTITY."/post_verify.php?t=".$entity."&result=".$return_value."");
                    exit(); 
                }else{
                    header("location: ../".$this->TYPE_ENTITY."/market_posts.php?t=".$entity."&result=4");
                    exit(); 
                }
                
            }
        
        
    }
    
    
  public function saveResponseFile($company,$post_id, $fileName,$fileTmpName,$fileSize,$fileError){
    $fails = array("too large", "file error","not right file");
    switch($this->TYPE_ENTITY){
        case "SMME":
            $entity = 1;
            break;
        case "COMPANY":
            $enity = 2;
            break;
        case "ADMIN":
            $enity = 3;
            break;
    }

       $url = $this->UploadFile(("Response_document"),$fileName,$fileTmpName,$fileSize,$fileError); 
       if(in_array($url, $fails)){
            header("location: ../".$this->TYPE_ENTITY."/market_posts.php?t=".$entity."&result=".$url."");
        }else{
            $rfp_id = $this->saveRFQ($company,$url, $post_id);
        }


    
}

public function saveResponseFileADMIN($company,$post_id, $fileName,$fileTmpName,$fileSize,$fileError){



    
    $fails = array("too large", "file error","not right file");
    switch($this->TYPE_ENTITY){
        case "SMME":
            $entity = 1;
            break;
        case "COMPANY":
            $enity = 2;
            break;
        case "ADMIN":
            $enity = 3;
            break;
    }

      $url = $this->UploadFile(("Response_document"),$fileName,$fileTmpName,$fileSize,$fileError); 
      if(in_array($url, $fails)){
            header("location: ../".$this->TYPE_ENTITY."/market_posts.php?t=".$entity."&result=".$url."");
        }else{
            $rfp_id = $this->saveRFQADMIN($company,$url, $post_id);
        }


    
}



    public function createResponse($question,  $choice, $scorecard_id, $post_id, $companyz){
        
      
           
        for ($i = 0; $i < count($choice); $i++) {
                
                $this->saveResponse($question[$i], $choice[$i], $scorecard_id, $post_id, $companyz);
            // Additional code after the loop
        
        } 

        $this->responseAlgorithm($post_id, $scorecard_id, $question, $choice, $companyz);
        //handle the next step -> send to criteria page
     $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        header("location: ../".$this->TYPE_ENTITY."/market_posts.php?&t=".$entity."&result=success");
        exit();
    }
    public function responses($id){
        
        $type = $this->fetchtype($id);

        $singletype = is_array($type) ? reset($type) : $type;

        if (is_array($singletype)) {
            $singletype = reset($singletype);
        }
        $results = $this->fetchResponses($id,$singletype);
        // echo $id;
        // print_r($results);
        $this->displayResponses($results);
    }
    
    private function fetchResponses($id,$singletype){
        
        if($singletype==2){
            
        $sql="SELECT
    Title,
    EXPIRY,
    Created,
    POST_ID,
    SCORECARD_ID,
    Company,
    team_name,
    manager,
    SUM(TotalResponses) AS Responses
FROM
    (
    SELECT
        m.Title,
        m.EXPIRY,
        m.Created,
        m.POST_ID,
        m.SCORECARD_ID,
        c.company_name AS Company,
        CONCAT(u.firstname, ' ', u.lastname) AS manager,
        ts.team_name,
        COUNT(
            DISTINCT CONCAT(sr.USER_ID, sr.COMPANY)
        ) AS TotalResponses
    FROM
        yasccoza_openlink_market.market_post m
    LEFT JOIN yasccoza_openlink_market.client c
    ON
        m.CLIENT_ID = c.CLIENT_ID
    LEFT JOIN yasccoza_openlink_market.scorecard_response sr
    ON
        sr.POST_ID = m.POST_ID
      LEFT JOIN yasccoza_tms_db.project_list pl
    ON
        m.POST_ID = pl.id
    LEFT JOIN yasccoza_tms_db.team_schedule ts
    ON
      pl.team_ids=ts.team_id
       LEFT JOIN yasccoza_tms_db.users u
    ON
       pl.manager_id=u.id
     WHERE m.ASSIGNED_TO=$id
GROUP BY
    m.Title,
    m.EXPIRY,
    m.Created,
    m.POST_ID,
    m.SCORECARD_ID,
    Company
HAVING
    COUNT(DISTINCT sr.User_ID) > 0
) AS Subquery
GROUP BY
    Title,
    EXPIRY,
    Created,
    POST_ID,
    SCORECARD_ID,
    Company";
    
     $result = $this->fetchNoParms($sql, "yasccoza_openlink_market");
        return $result;
            
        }elseif($singletype==3){
            
    $sql = "SELECT
    Title,
    EXPIRY,
    Created,
    POST_ID,
    SCORECARD_ID,
    Company,
    team_name,
    manager,
    SUM(TotalResponses) AS Responses
FROM
    (
    SELECT
        m.Title,
        m.EXPIRY,
        m.Created,
        m.POST_ID,
        m.SCORECARD_ID,
        c.company_name AS Company,
        ts.team_name,
        CONCAT(u.firstname, ' ', u.lastname) AS manager,
        COUNT(
            DISTINCT CONCAT(sr.USER_ID, sr.COMPANY)
        ) AS TotalResponses
    FROM
        yasccoza_openlink_market.market_post m
    LEFT JOIN yasccoza_openlink_market.client c
    ON
        m.CLIENT_ID = c.CLIENT_ID
    LEFT JOIN yasccoza_openlink_market.scorecard_response sr
    ON
        sr.POST_ID = m.POST_ID
      LEFT JOIN yasccoza_tms_db.project_list pl
    ON
        m.POST_ID = pl.id
    LEFT JOIN yasccoza_tms_db.team_schedule ts
    ON
       pl.team_ids=ts.team_id
       LEFT JOIN yasccoza_tms_db.users u
    ON
       pl.manager_id=u.id
     WHERE ts.team_members=$id
GROUP BY
    m.Title,
    m.EXPIRY,
    m.Created,
    m.POST_ID,
    m.SCORECARD_ID,
    Company
HAVING
    COUNT(DISTINCT sr.User_ID) > 0
) AS Subquery
GROUP BY
    Title,
    EXPIRY,
    Created,
    POST_ID,
    SCORECARD_ID,
    Company; ";
        $result = $this->fetchNoParms($sql, "yasccoza_openlink_market");
        return $result;
            
        }elseif($singletype==1||$singletype=4){
            
            $sql = "SELECT
    Title,
    EXPIRY,
    Created,
    POST_ID,
    SCORECARD_ID,
    Company,
    team_name,
    manager,
    SUM(TotalResponses) AS Responses
FROM
    (
    SELECT
        m.Title,
        m.EXPIRY,
        m.Created,
        m.POST_ID,
        m.SCORECARD_ID,
        c.company_name AS Company,
        ts.team_name,
        CONCAT(u.firstname, ' ', u.lastname) AS manager,
        COUNT(
            DISTINCT CONCAT(sr.USER_ID, sr.COMPANY)
        ) AS TotalResponses
    FROM
        yasccoza_openlink_market.market_post m
    LEFT JOIN yasccoza_openlink_market.client c
    ON
        m.CLIENT_ID = c.CLIENT_ID
    LEFT JOIN yasccoza_openlink_market.scorecard_response sr
    ON
        sr.POST_ID = m.POST_ID
      LEFT JOIN yasccoza_tms_db.project_list pl
    ON
        m.POST_ID = pl.id
    LEFT JOIN yasccoza_tms_db.team_schedule ts
    ON
       pl.team_ids=ts.team_id
       LEFT JOIN yasccoza_tms_db.users u
    ON
       pl.manager_id=u.id
GROUP BY
    m.Title,
    m.EXPIRY,
    m.Created,
    m.POST_ID,
    m.SCORECARD_ID,
    Company
HAVING
    COUNT(DISTINCT sr.User_ID) > 0
) AS Subquery
GROUP BY
    Title,
    EXPIRY,
    Created,
    POST_ID,
    SCORECARD_ID,
    Company; ";
        $result = $this->fetchNoParms($sql, "yasccoza_openlink_market");
        return $result;
        }
        
    }
    

    private function fetchtype($id)
    {
    $sql = "SELECT type FROM yasccoza_tms_db.users WHERE id=?";
    $table = "yasccoza_tms_db";
    $types="i";
    $params=array($id);
    $result=$this->fetch($table,$sql,$types,$params);
    return $result;
    }
    
   private function displayResponses($responses) {
    $display = "";
    $responsesPerPage = $_GET['number'] ?? 10;
    $page = $_GET['page'] ?? 1;
    $start = ($page - 1) * $responsesPerPage;
    $end = $start + $responsesPerPage;

    $sortColumn = $_GET['sort'] ?? 'POST_ID';
    $sortOrder = $_GET['order'] ?? 'asc';

    // Sort logic
    usort($responses, function ($a, $b) use ($sortColumn, $sortOrder) {
        return ($sortOrder == 'asc') 
            ? strcmp($a[$sortColumn], $b[$sortColumn]) 
            : strcmp($b[$sortColumn], $a[$sortColumn]);
    });

    // --- Add Modern CSS ---
    $display .= '
    <style>
        .responsive-table-container {
            width: 100%;
            overflow-x: auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            padding: 15px;
            margin-top: 10px;
        }

        table.projects {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        table.projects thead {
            background: linear-gradient(135deg, #0d47a1, #1976d2);
            color: #fff;
            text-transform: uppercase;
            font-size: 0.85rem;
            position: sticky;
            top: 0;
            z-index: 5;
        }

        table.projects th, table.projects td {
            padding: 12px 15px;
            text-align: left;
            white-space: nowrap;
        }

        table.projects th a {
            color: #fff;
            text-decoration: none;
        }

        table.projects tbody tr {
            border-bottom: 1px solid #e0e0e0;
            transition: background 0.2s ease;
        }

        table.projects tbody tr:hover {
            background: #f1f7ff;
        }

        table.projects td small {
            color: #888;
            display: block;
            font-size: 0.85em;
        }

        table.projects td {
            color: #333;
        }

        .btn-xs {
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
        }

        /* Entry Selector */
        .entry-select {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
            font-size: 14px;
            color: #333;
        }

        .entry-select p {
            margin: 0;
            font-weight: 600;
            color: #1976d2;
        }

        .entry-select select {
            border-radius: 6px;
            padding: 5px 10px;
            border: 1px solid #ccc;
        }

        /* Pagination Buttons */
        .pagination-buttons {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }

        @media (max-width: 768px) {
            table.projects th, table.projects td {
                padding: 10px;
                font-size: 13px;
            }

            table.projects {
                font-size: 13px;
            }

            .responsive-table-container {
                padding: 10px;
            }

            .btn-xs {
                width: 100%;
                margin-bottom: 5px;
            }

            .pagination-buttons {
                flex-direction: column;
                gap: 10px;
            }

            .entry-select {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
    ';

    // --- Entry dropdown ---
    $entryPage = $this->TYPE_ENTITY == "SMME" ? "my_responses.php" : "responses.php";
    $display .= '
    <div class="entry-select">
        <p>Show entries</p>
        <select onchange="location = this.value;" class="form-control" style="width:170px">
            <option value="" style="color:red">Current entry: ' . $responsesPerPage . '</option>';

    foreach ([5,10,25,50,100] as $num) {
        $display .= '<option value="'.$entryPage.'?page=1&sort=POST_ID&order=desc&number='.$num.'">'.$num.'</option>';
    }

    $display .= '</select></div><br>';

    // --- Table Start ---
    $display .= '<div class="responsive-table-container">
    <table class="table table-striped projects">
        <thead>
            <tr>
                <th style="font-size:12px"><a href="?sort=POST_ID&order=' . ($sortOrder == 'asc' ? 'desc' : 'asc') . '&number='.$responsesPerPage.'">#</a></th>
                <th style="font-size:12px"><a href="?sort=Title&order=' . ($sortOrder == 'asc' ? 'desc' : 'asc') . '&number='.$responsesPerPage.'">Job Title</a></th>
                <th style="font-size:12px"><a href="?sort=manager&order=' . ($sortOrder == 'asc' ? 'desc' : 'asc') . '&number='.$responsesPerPage.'">Manager</a></th>
                <th style="font-size:12px"><a href="?sort=team_name&order=' . ($sortOrder == 'asc' ? 'desc' : 'asc') . '&number='.$responsesPerPage.'">Team</a></th>
                <th style="font-size:12px"><a href="?sort=Company&order=' . ($sortOrder == 'asc' ? 'desc' : 'asc') . '&number='.$responsesPerPage.'">Client</a></th>
                <th style="font-size:12px"><a href="?sort=Responses&order=' . ($sortOrder == 'asc' ? 'desc' : 'asc') . '&number='.$responsesPerPage.'">Respondents</a></th>
                <th style="font-size:12px"><a href="?sort=EXPIRY&order=' . ($sortOrder == 'asc' ? 'desc' : 'asc') . '&number='.$responsesPerPage.'">Closing Date</a></th>
                <th style="font-size:12px">Action</th>
            </tr>
        </thead>
        <tbody>';

    for ($i = $start; $i < min(count($responses), $end); $i++) {
        $r = $responses[$i];
        if (!empty($r['Title'])) {
            $display .= '
            <tr>
                <td style="color:#1976d2; font-weight:bold;font-size:12px">' . htmlspecialchars($r['POST_ID']) . '</td>
             <td style="font-size:12px">
                    ' . htmlspecialchars(implode(' ', array_slice(explode(' ', $r['Title']), 0, 3))) . (str_word_count($r['Title']) > 3 ? '...' : '') . '
                    <br><small>Created ' . htmlspecialchars($r['Created']) . '</small>
                </td>

                <td style="font-size:12px">' . htmlspecialchars($r['manager']) . '</td>
                <td style="font-size:12px">' . htmlspecialchars($r['team_name']) . '</td>
                <td style="font-size:12px">' . htmlspecialchars($r['Company']) . '</td>
                <td style="font-size:12px">' . htmlspecialchars($r['Responses']) . '</td>
                <td style="font-size:12px">' . htmlspecialchars($r['EXPIRY']) . '</td>
                <td style="font-size:12px"><a href="responsesInfo.php?p=' . urlencode($r['POST_ID']) . '" class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View</a></td>
            </tr>';
        }
    }

    $display .= '</tbody></table></div>';

    // --- Pagination ---
    $display .= '<div class="pagination-buttons">';
    if ($page > 1) {
        $display .= '<a class="btn btn-primary" href="?page=' . ($page - 1) . '&sort=' . $sortColumn . '&order=' . $sortOrder . '&number='.$responsesPerPage.'">Previous Page</a>';
    } else {
        $display .= '<span></span>';
    }
    if ($end < count($responses)) {
        $display .= '<a class="btn btn-success" href="?page=' . ($page + 1) . '&sort=' . $sortColumn . '&order=' . $sortOrder . '&number='.$responsesPerPage.'">Next Page</a>';
    }
    $display .= '</div>';

    echo $display;
}


    public function displayJobOrderResponses($post_id){
        $responses = $this->fetchPostResponsesInfo($post_id);
        //print_r($responses);
        //echo $post_id;
        $this->displayResponsesInfo($responses);
    }
    
    
     public function displayJobOrderResponsesIND($post_id){
        $responses = $this->fetchPostResponsesIND($post_id);
        //print_r($responses);
        //echo $post_id;
        $this->displayResponsesInfo($responses);
    }
    
    public function displayResponseSMME($id){

          $results = $this->fetchSMMEResponses($id);
          $this->displayResponses($results);
    }
    
   private function fetchSMMEResponses($id){
    $sql = "SELECT 
        m.Title, 
        m.EXPIRY, 
        m.Created,
        m.POST_ID, 
        m.SCORECARD_ID,
        c.company_name as Company,
        COUNT(DISTINCT sr.User_ID) as Responses
    FROM
        yasccoza_openlink_market.market_post m
   LEFT JOIN
        yasccoza_openlink_market.client c ON m.CLIENT_ID = c.CLIENT_ID
    LEFT JOIN
        yasccoza_openlink_market.scorecard_response sr ON sr.POST_ID = m.POST_ID
    WHERE 
        sr.User_ID = ?
    GROUP BY
        m.Title,
        m.EXPIRY,
        m.Created,
        m.POST_ID,
        m.SCORECARD_ID,
        Company";


        $table = "yasccoza_openlink_market";
        $params = array($id);
        $types="i";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;

}
    
    
    
    
    
    public function PrintResponseInfo($post_id){
        $responses = $this->fetchPostResponsesInfo($post_id);
        $this->printResponseExcel($responses);
    }
    public function PrintIndResponseInfo($post_id, $user_id){
        $responses = $this->fetchIndividualAnswers($user_id, $post_id, $c);
        $this->printIndResponseExcel($responses);
    }
    private function printResponseExcel($result){
    $fileName =  $result[0]['Client_Name']."_post_responses_" . date('Y-m-d') . ".xls"; 
       
      // Column names po
      $fields = array('Job ID', 'Job Title','Client','Respondent Company Name', 'Respondent Score', 'Response Date');  
      $excelData = implode("\t", array_values($fields)) . "\n"; 
       for($i =0; $i < count($result); $i++){
          $lineData = array($result[$i]['POST_ID'], $result[$i]['Title'], $result[$i]['Client_Name'],$result[$i]['Company'], $result[$i]['SCORE'], $result[$i]['Created']); 
          // array_walk($lineData, 'filterData'); 
          $excelData .= implode("\t", array_values($lineData)) . "\n"; 
        }
        // Headers for download 
      header("Content-Type: application/vnd.ms-excel"); 
      header("Content-Disposition: attachment; filename=\"$fileName\""); 
       
      // Render excel data 
      echo $excelData; 
      exit();
    }
    private function printIndResponseExcel($result){
        $fileName = $result[0]['Company']."_individual_post_responses_" . date('Y-m-d') . ".xls"; 
           
          // Column names po
          $fields = array('Job ID', 'Job Title','Client','Respondent Company Name', 'Question', 'Answer', 'Score','Date');  
          $excelData = implode("\t", array_values($fields)) . "\n"; 
           for($i =0; $i < count($result); $i++){
              $lineData = array($result[$i]['POST_ID'], $result[$i]['Title'],$result[$i]['Client_Name'], $result[$i]['Company'], $result[$i]['Question'], $result[$i]['choice'], $result[$i]['Weighting'], $result[$i]['created']); 
              // array_walk($lineData, 'filterData'); 
              $excelData .= implode("\t", array_values($lineData)) . "\n"; 
            }
            // Headers for download 
          header("Content-Type: application/vnd.ms-excel"); 
          header("Content-Disposition: attachment; filename=\"$fileName\""); 
           
          // Render excel data 
          echo $excelData; 
          exit();
        }
      
    private function responsesPDFPrint($responses){
       // Instantiation of FPDF class
        $pdf = new PDF();

        // Define alias for number of pages
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Times','',14);
        for($i = 0; $i < count($responses); $i++){
            $line_data =  "Company name: ".$responses[$i]['Company']."  Score:  ". $responses[$i]['SCORE'];
            $pdf->Cell(0, 10, $line_data, 0, 1);
        }
        $pdf->Output();
    }

    private function fetchPostResponsesInfo($id){
        $sql = "SELECT DISTINCT
    sc.USER_ID,
    sc.SCORECARD_ID,
    m.Title,
    m.EXPIRY,
    m.Created,
    m.POST_ID,
    c.Legal_name AS Company,
    sc.SCORE,
    sc.Created,
     sc.Company as Company_Ass,
    cl.company_name AS Client_Name
FROM
    yasccoza_openlink_market.market_post m
LEFT JOIN
    yasccoza_openlink_market.responsescore sc ON sc.POST_ID = m.POST_ID
LEFT JOIN
    yasccoza_openlink_market.client cl ON m.CLIENT_ID = cl.CLIENT_ID
LEFT JOIN
    yasccoza_openlink_smmes.register c ON sc.USER_ID = c.SMME_ID
WHERE
    sc.POST_ID = ? AND c.Legal_name IS NOT NULL
UNION
SELECT DISTINCT
    sc.USER_ID,
     sc.SCORECARD_ID,
    m.Title,
    m.EXPIRY,
    m.Created,
    m.POST_ID,
    c.firstname AS Company,
    sc.SCORE,
    sc.Created,
    sc.Company as Company_Ass,
    cl.company_name AS Client_Name
FROM
    yasccoza_openlink_market.market_post m
LEFT JOIN
    yasccoza_openlink_market.responsescore sc ON sc.POST_ID = m.POST_ID
LEFT JOIN
    yasccoza_openlink_market.client cl ON m.CLIENT_ID = cl.CLIENT_ID
LEFT JOIN
    yasccoza_tms_db.users c ON sc.USER_ID = c.id
WHERE
    sc.POST_ID = ? AND c.firstname IS NOT NULL
ORDER BY
    SCORE DESC;
 ";
        $types = "ii";
        $params = array($id, $id);
        $result = $this->fetch("yasccoza_openlink_market",$sql, $types, $params);
        return $result;
    }
    
    
 private function fetchPostResponsesIND($id){
        $sql = "SELECT DISTINCT
    sc.USER_ID,
    sc.SCORECARD_ID,
    m.Title,
    m.EXPIRY,
    m.Created,
    m.POST_ID,
    c.Legal_name AS Company,
    sc.SCORE,
    sc.Created,
    sc.Company as Company_Ass,
    cl.company_name AS Client_Name
FROM
    yasccoza_openlink_market.market_post m
LEFT JOIN
    yasccoza_openlink_market.responsescore sc ON sc.POST_ID = m.POST_ID
LEFT JOIN
    yasccoza_openlink_market.client cl ON m.CLIENT_ID = cl.CLIENT_ID
LEFT JOIN
    yasccoza_openlink_smmes.register c ON sc.USER_ID = c.SMME_ID
WHERE
    sc.POST_ID = ? AND c.Legal_name IS NOT NULL
    AND sc.USER_ID = ?
UNION
SELECT DISTINCT
    sc.USER_ID,
    sc.SCORECARD_ID,
    m.Title,
    m.EXPIRY,
    m.Created,
    m.POST_ID,
    c.firstname AS Company,
    sc.SCORE,
    sc.Created,
    sc.Company as Company_Ass,
    cl.company_name AS Client_Name
FROM
    yasccoza_openlink_market.market_post m
LEFT JOIN
    yasccoza_openlink_market.responsescore sc ON sc.POST_ID = m.POST_ID
LEFT JOIN
    yasccoza_openlink_market.client cl ON m.CLIENT_ID = cl.CLIENT_ID
LEFT JOIN
    yasccoza_tms_db.users c ON sc.USER_ID = c.id
WHERE
    sc.POST_ID = ? AND c.firstname IS NOT NULL
    AND sc.USER_ID = ?
ORDER BY
    SCORE DESC;

 ";
        $types = "iiii";
        $params = array($id,$this->ID, $id, $this->ID);
        $result = $this->fetch("yasccoza_openlink_market",$sql, $types, $params);
        return $result;
    }
    
    
   private function displayResponsesInfo($responses) {
    $display = '
    <style>
        /* ===== Modern Table & Button Styling ===== */
        .responses-container {
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            overflow-x: auto;
        }

        .responses-container h2 {
            color: #172d44;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .responses-container p {
            margin-bottom: 10px;
        }

        .responses-container table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        .responses-container th, 
        .responses-container td {
            padding: 12px 15px;
            text-align: left;
            font-size: 14px;
        }

        .responses-container th {
            background-color: #172d44;
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        .responses-container tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .responses-container tr:hover {
            background-color: #e6f2ff;
            transition: background-color 0.2s ease-in-out;
        }

        .responses-container td {
            color: #333;
            vertical-align: middle;
        }

        .responses-container td a.btn {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
        }

        .responses-container .btn-primary {
            background-color: #0d6efd;
            color: white;
        }

        .responses-container .btn-primary:hover {
            background-color: #0b5ed7;
        }

        .responses-container .btn {
            border: none;
            cursor: pointer;
        }

        .responses-container .btn i {
            margin-right: 5px;
        }

        /* ===== Print Button ===== */
        .responses-container .print-btn {
            background-color: #198754;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .responses-container .print-btn:hover {
            background-color: #157347;
        }

        /* ===== Responsive Styles ===== */
        @media (max-width: 768px) {
            .responses-container th, 
            .responses-container td {
                font-size: 12px;
                padding: 8px;
            }

            .responses-container table {
                font-size: 12px;
            }

            .responses-container .print-btn {
                width: 100%;
                margin-bottom: 10px;
            }

            .responses-container table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>

    <div class="responses-container">
    <form></form>
    <form action="../MARKET/ROUTE.php?p=' . $responses[0]['POST_ID'] . '" method="POST">
        <input type="text" name="tk" value="' . token::get_ne("PRINT_RESPONSES_INFO") . '" required hidden>';

    if (strcmp($this->TYPE_ENTITY, "ADMIN") == 0) {
        $display .= '<button class="print-btn" type="submit" name="ADMIN_PRINT_JOBORDER_INFO"><i class="fa fa-print"></i> Print Job Order Information</button>';
    } elseif (strcmp($this->TYPE_ENTITY, "SMME") == 0) {
        $display .= '<button class="print-btn" type="submit" name="COMPANY_PRINT_JOBORDER_INFO"><i class="fa fa-print"></i> Print Job Order Information</button>';
    } else {
        $display .= '<button class="print-btn" type="submit" name="SMME_PRINT_JOBORDER_INFO"><i class="fa fa-print"></i> Print Job Order Information</button>';
    }

    $display .= '
        </form>
        <br>
        <p style="font-weight:bold; font-size:14px;">JOB ID: 
            <span style="color:red; font-weight:bold;">' . $responses[0]['POST_ID'] . '</span>
        </p>   
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Respondent</th>
                    <th>Date Posted</th>
                    <th>Company</th>
                    <th>Score</th>
                    <th>Last PDF Attached Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>';

    for ($i = 0; $i < count($responses); $i++) {
        if (!empty($responses[$i]['USER_ID'])) {
            $displayCompany = !empty($responses[$i]['Company_Ass']) ? $responses[$i]['Company_Ass'] : $responses[$i]['Company'];
            $display .= '
                <tr>
                    <td>' . ($i + 1) . '</td>
                    <td>' . htmlspecialchars($responses[$i]['Company']) . '</td>
                    <td style="color:red;">' . date('Y-m-d', strtotime($responses[$i]['Created'])) . '</td>
                    <td>' . htmlspecialchars($displayCompany) . '</td>
                    <td>' . htmlspecialchars($responses[$i]['SCORE']) . '</td>
                    <td>' . htmlspecialchars($responses[$i]['Created']) . '</td>
                    <td>
                        <a href="individualAnswers.php?p=' . $responses[$i]['POST_ID'] . '&e=' . $responses[$i]['USER_ID'] . '&c=' . urlencode($displayCompany) . '&s=' . $responses[$i]['SCORECARD_ID'] . '" 
                           class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View Answers</a>
                    </td>
                </tr>';
        }
    }

    $display .= '
            </tbody>
        </table>
    </div>';

    echo $display;
}

    
    
    private function getLegalname(){
        
        $sql = "SELECT Legal_name FROM register WHERE SMME_ID=?";
        $types = "i";
        $params = array($this->ID);
        $table = "yasccoza_openlink_smmes";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
       
    }
    
    
     public function createlink($smme_ids, $post_ids){
         
       
        
        foreach ($post_ids as $post_id) {
    // Iterate over each SMME_ID for the current POST_ID
    foreach ($smme_ids as $smme_id) {
        // Bind parameters for each combination
        $sql = "INSERT INTO `job_and_smmes`( `POST_ID`, `SMME_ID`,  `Who_Sent`) VALUES (?,?,?)";
        $types="iii";
        $params = array($post_id,$smme_id, $this->ID);
        $table = "job_and_smmes";
        $query = $this->save($sql, $types, $params, $table);

                }
            }
                      
           
       header("location: ../".$this->TYPE_ENTITY."/send_post.php?t=3&result=success");
        exit();
    }
    
    
    
    private function saveResponse($question_id, $choice, $scorecard, $post_id, $companyz){
        
        if (!empty($companyz)){
            
        $sql = "INSERT INTO `scorecard_response`( `QUESTION_ID`, `CHOICE_ID`,  `User_ID`,SCORECARD_ID, POST_ID, COMPANY) VALUES (?,?,?,?,?,?)";
        $types="iiiiis";
        $params = array($question_id, $choice, $this->ID, $scorecard,$post_id, $companyz);
        $table = "scorecard_response";
        $query = $this->save($sql, $types, $params, $table);
            
            
        }else {
            
            
            $sql = "SELECT Legal_name FROM register WHERE SMME_ID=?";
            $types = "i";
            $params = array($this->ID);
            $table = "yasccoza_openlink_smmes";
            $result = $this->fetch($table, $sql, $types, $params);
            $legalName = $result[0]['Legal_name'];

            $this->master->changedb("yasccoza_openlink_market");
            $sql = "INSERT INTO `scorecard_response` (`QUESTION_ID`, `CHOICE_ID`, `User_ID`, `SCORECARD_ID`, `POST_ID`, `COMPANY`) VALUES (?,?,?,?,?,?)";
            $types = "iiiiis";
            $params = array($question_id, $choice, $this->ID, $scorecard, $post_id, $legalName);
            $table = "scorecard_response";
            $query = $this->save($sql, $types, $params, $table);
              
        }
        
       
    }
//     SELECT DISTINCT(sr.USER_ID), m.Title, m.EXPIRY,m.Created,m.POST_ID, c.Legal_name as Company, q.Question,qc.Choice, qc.Weighting, sc.Created
// FROM yasccoza_openlink_market.market_post m, yasccoza_openlink_smmes.register c, yasccoza_openlink_market.scorecard_response sr, yasccoza_openlink_market.responsescore sc, yasccoza_openlink_market.question q, openlink-market.question_choice qc
//                 WHERE sr.POST_ID = m.POST_ID
//                 AND qc.QUESTION_ID = q.QUESTION_ID
//                 AND q.QUESTION_ID = sr.QUESTION_ID
//                 AND c.SMME_ID = sr.User_ID
//                 AND sr.User_ID = ?
//                 AND m.POST_ID = ?
// UNION 
// SELECT DISTINCT(sr.USER_ID), m.Title, m.EXPIRY,m.Created,m.POST_ID, c.First_Name as Company, q.Question,qc.Choice, qc.Weighting, sc.Created
// FROM yasccoza_openlink_market.market_post m, yasccoza_openlink_admin_db.signup c, yasccoza_openlink_market.scorecard_response sr, yasccoza_openlink_market.responsescore sc, yasccoza_openlink_market.question q, openlink-market.question_choice qc
//                 WHERE sr.POST_ID = m.POST_ID
//                 AND qc.QUESTION_ID = q.QUESTION_ID
//                 AND q.QUESTION_ID = sr.QUESTION_ID
//                 AND c.SMME_ID = sr.User_ID
//                 AND sr.User_ID = ?
//                 AND m.POST_ID = ?
    private function fetchIndividualAnswers($user_id , $post_id, $c){
        
       
        $sql ="SELECT DISTINCT 
    sr.User_ID,
    sr.COMPANY,
    q.Question, 
    qc.choice, 
    qc.Weighting, 
    c.Legal_name as Company, 
    m.POST_ID, 
    m.Title, 
    sr.created,
    cl.company_name as Client_Name
FROM 
    scorecard_response sr
LEFT JOIN 
    question q ON sr.QUESTION_ID = q.QUESTION_ID
LEFT JOIN 
    question_choice qc ON sr.CHOICE_ID = qc.CHOICE_ID
LEFT JOIN 
    yasccoza_openlink_smmes.register c ON sr.User_ID = c.SMME_ID
LEFT JOIN 
    yasccoza_openlink_market.market_post m ON m.POST_ID = sr.POST_ID
LEFT JOIN 
    client cl ON m.CLIENT_ID = cl.CLIENT_ID
WHERE 
    sr.User_ID = ?
    AND sr.POST_ID = ?
    AND sr.COMPANY =?
    AND c.Legal_name IS NOT NULL  -- Exclude rows with Company name null
   
    
UNION
    
SELECT DISTINCT 
    sr.User_ID,
    sr.COMPANY,
    q.Question, 
    qc.choice, 
    qc.Weighting, 
    c.firstname as Company, 
    m.POST_ID, 
    m.Title, 
    sr.created,
    cl.company_name as Client_Name
FROM 
    scorecard_response sr
LEFT JOIN 
    question q ON sr.QUESTION_ID = q.QUESTION_ID
LEFT JOIN 
    question_choice qc ON sr.CHOICE_ID = qc.CHOICE_ID
LEFT JOIN  
    yasccoza_tms_db.users c ON sr.User_ID = c.id
LEFT JOIN 
    yasccoza_openlink_market.market_post m ON m.POST_ID = sr.POST_ID
LEFT JOIN 
    client cl ON m.CLIENT_ID = cl.CLIENT_ID
WHERE 
    sr.User_ID = ?
    AND sr.POST_ID = ?
    AND sr.COMPANY =?
    AND c.firstname IS NOT NULL; -- Exclude rows with Company name null

    ;
        ";
        $table = "yasccoza_openlink_market";
        $params = array($user_id, $post_id,$c,$user_id, $post_id, $c);
        $types="iisiis";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function fetchIndFiles($user_id, $post_id){
        $sql ="SELECT url,created FROM rfp WHERE USER_ID = ? AND POST_ID = ?";
        $table = "yasccoza_openlink_market";
        $params = array($user_id, $post_id);
        $types="ii";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    
     private function fetchIndFilesADMIN($user_id, $post_id, $c){
         
        
        $sql ="SELECT url,created FROM rfp WHERE POST_ID = ? AND COMPANY=?";
        $table = "yasccoza_openlink_market";
        $params = array ($post_id, $c);
        $types="is";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function displayAnswers($answers, $files){
        
           //print_r($files);
        
        $display ="";
        $display .= '
        
                <br>
        <p style="font-weight:bold; font-size:14px">COMPANY: <span style="font-weight:bold;color:red; font-size:14px">'.$answers[0]['COMPANY'].'<span/> </p>
        <br>
        <table class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th style="width: 1%">#</th>
                          <th>Question</th>
                          <th>Answer</th>
                          <th>Score %</th>
                        </tr>
                      </thead>
                      <tbody>';
                        for ($i=0; $i < count($answers); $i++) { 
                            # code...
                            $display .='
                                    <tr>
                                    <td>'.($i+1).'</td>
                                    <td>
                                        '.$answers[$i]['Question'].'
                                    </td>
                                    <td>'.$answers[$i]['choice'].'</td>
                                    <td>
                                    '.$answers[$i]['Weighting'].'
                                    </td>
                                </tr>
                            ';
                        }
                      $display .='
                      </tbody>
                    </table>
                    <div>
                    <h3>Uploaded Files </h3>
                    <table class="table table-striped projects">
                    <tbody><tr>';
                    for ($i = 0; $i < count($files); $i++) {
                            if (strpos($files[$i]["url"], 'Response_document') !== false) {
                                $display .= "<td style='width:10%'>File:</td>
                                     <td>
                                         <a href='../STORAGE/FILES/".$files[$i]['url']."' target='_blank'><p><span style='color:red'>Date uploaded:</span> " . $files[$i]["created"] . "</p>
                                            <img src='../Images/PDF_file_icon.png' height=50 width=50></a>
                                     </td>";

                            }
                        }
                    $display .='
                    </tbody>
                    </tr>
                  </table>
                    </div>
        ';
        echo $display;
    }
    public function DisplayIndAnswers($user_id, $post_id, $c){
        $result = $this->fetchIndividualAnswers($user_id, $post_id, $c);
        $files = $this->fetchIndFiles($user_id, $post_id);
        $this->displayAnswers($result, $files);
    }
    
    
    public function DisplayIndAnswersADMIN($user_id, $post_id, $c){
        $result = $this->fetchIndividualAnswers($user_id, $post_id, $c);
        $files = $this->fetchIndFilesADMIN($user_id, $post_id, $c);
        $this->displayAnswers($result, $files);
    }
    
    public function PostForm(){
        if(strcmp($this->TYPE_ENTITY,"ADMIN")==0){
            
             $this->adminpostFormDisplay();
            
        }else{
            
            $this->postFormDisplay();
            
        }
        
    }
    public function ResponsefilesForm($id){
        $criteria = $this->fetchScorecardCriteriaID($id);
        $this->filesformDisplay($criteria);
    }
    
    
 

    
    private function filesformDisplay($result, $post_id, $yess){
        
        
        //print_r($result);
    $entity = 0;
    
           switch($this->TYPE_ENTITY) {
                case "SMME":
                    $entity = 1;
                    break;
                case "ADMIN":
                    $entity = 3;
                    break;
            }
            
            
        if ($this->TYPE_ENTITY == "ADMIN"){
            
             $display ='<div style="padding-left:30px"><h3></h3><ol>';
        for($i = 0; $i<count($result);$i++ ){
            $criteria = $this->fetchCriteriaByCriteriaId($result[$i]['CRITERIA_ID']);
            $fileName = $criteria[0]['Document'];
           
            
               
 
    
                // $display .= '<p style="font-size: 20px">Files to Submit</p>';
                
                // $display .= '<a href="' . $criteria[0]['Document'] . '" target="_blank">Preview</a>';
                // $display .= '<a href="' . $criteria[0]['Document'] . '" download>Download</a>';
             
        
        $display .= '</ol>
        </div>';
            
        }
        
        
       
    
        if ($yess == 1) {
           
            
        } else {
            
        
            if (strcmp($this->TYPE_ENTITY, "SMME") == 1) {// admin
                $display .= '<button type="submit" class="btn btn-success" name="SMME_RESPONSEFILES_CREATE">Submit</button>';
            }
            if (strcmp($this->TYPE_ENTITY, "ADMIN") == 3) {// admin
                $display .= '<button type="submit" class="btn btn-success" name="ADMIN_RESPONSEFILES_CREATE">Submit</button>';
            }
            
            $display .= '</form>';
        }
        
    }else{
        
           
           $legalNamesArray = $this->getLegalname();
       $legalName = $legalNamesArray[0]['Legal_name'];
       
    
          $display ='<div style="padding-left:30px"><h3></h3><ol>';
          
    
          
        for($i = 0; $i<count($result);$i++ ){
            $criteria = $this->fetchCriteriaByCriteriaId($result[$i]['CRITERIA_ID']);
            
        
                $display .= '<p style="font-size: 20px">Files to Submit</p>';
             $display .= '<li style="font-size: 13px; color: black; padding-left:30px">  ' . $criteria[0]['Document'] . '<hr>
          
             </li>';
                $display .= '<form id="demo-form2" data-parsley-validate class="dropzone" action="../MARKET/ROUTE.php?e=' . $entity . '&p=' . $post_id . '&c=' .  $legalName . '" method="POST">';
        
        $display .= '</ol>
        </div>';
            
        }
        
        
       
    
        if ($yess == 1) {
           
            
        } else {
            // $display .= '<form id="demo-form2" data-parsley-validate class="dropzone" action="../MARKET/ROUTE.php?e=' . $entity . '&p=' . $post_id . '" method="POST">';
        
            if (strcmp($this->TYPE_ENTITY, "SMME") == 1) {// admin
                $display .= '<button type="submit" class="btn btn-success" name="SMME_RESPONSEFILES_CREATE">Submit</button>';
            }
            if (strcmp($this->TYPE_ENTITY, "ADMIN") == 3) {// admin
                $display .= '<button type="submit" class="btn btn-success" name="ADMIN_RESPONSEFILES_CREATE">Submit</button>';
            }
            
            $display .= '</form>';
        }
        
    }
        
        return $display;

    }
    
    
    private function postFormDisplay(){
        $display = "";
        $scorecards = $this->fetchScoreCards($this->ID);
        $worktype = $this->fetchWorkType();
        $jobtypes = $this->fetchJobTypes();
         $offices = $this->fetchOFFICES();
        $display .= '
       <form id="post_form" data-parsley-validate class="form-horizontal form-label-left" action="../MARKET/ROUTE.php" Method="POST" enctype="multipart/form-data">
        <div >
        <div class="form-group">
          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">OFFICE you want to purchase from:<span class="required"></span> </label>
          <div class="col-md-6 col-sm-6 col-xs-12">
            <select style="width:35vw" id="office" name="office" required="required" class="form-control col-md-7 col-xs-12 formz" list="clients" >
            ';
            for($i = 0; $i < count($offices); $i++){
                $display .= '<option value="'.$offices[$i]['INDUSTRY_ID'].'" >'.$offices[$i]['INDUSTRY_ID'].' - '.$offices[$i]['office'].'</option>';
            }
            $display.='
               <option value="10">All offices</option>
            </select>  
            </div>
        </div>
      
      </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Title:<span class="required"></span> </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
              <input style="width:35vw" type="text" id="first-name" name="title" required="required" class="form-control col-md-7 col-xs-12 formz">
            </div>
          </div>
           <div class="form-group">
        <label for="date" class="control-label col-md-3 col-sm-3 col-xs-12">Description:</label>
        
        <div class="col-md-6 col-sm-6 col-xs-12">
            <textarea name="description" cols="30" rows="10" class="form-control" id="editor1"></textarea>
        </div>
    </div>
        <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="work_type">Job Order Type:<span class="required"></span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
                <select style="width:35vw" id="jobType" name="jobOrderType" required="required"  class="form-control form-control-sm" ><option value="" selected>Select Job Type</option>';
                    for( $i = 0; $i < count($jobtypes); $i++){
                        $display.= '
                        <option value="'.$jobtypes[$i]["job_type_name"].'">'.$jobtypes[$i]["job_type_name"].'</option>
                     '   
                        ;
                    }
                
                    $display.= ' </select>
                    
            </div>

        </div>
        <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="work_type">Work Type:<span class="required"></span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
                <select style="width:35vw" id="work_type" name="work_type[]" required="required"  class="form-control form-control-sm tags" multiple="multiple"><option></option>';
                    
                    for ($i = 0; $i < count($worktype); $i++) {
                     $display.= '<option value="' . $worktype[$i]['id'] . '"  >' . $worktype[$i]['id'] . ' - ' . $worktype[$i]['task_name'] . '</option>';
                    }
                
                    $display.= ' </select>
                    
            </div>

        </div>
          <div class="form-group">
          <label for="date" class="control-label col-md-3 col-sm-3 col-xs-12">Start Date:</label>
          
          <div class="col-md-6 col-sm-6 col-xs-12">
              <input style="width:35vw" type="date" "date" name="StartDate" required="required" data-validate-minmax="10,100" class="form-control col-md-7 col-xs-12">
          </div>
        </div>
          <div class="form-group">
          <label for="date" class="control-label col-md-3 col-sm-3 col-xs-12">Date of Expiry:</label>
          
          <div class="col-md-6 col-sm-6 col-xs-12">
              <input style="width:35vw" type="date" "date" name="EndDate" required="required" data-validate-minmax="10,100" class="form-control col-md-7 col-xs-12">
          </div>
        </div>
       
          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Scorecard:<span class="required"></span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <select style="width:35vw" id="scorecards" name="scorecard_id" required="required" class="form-control col-md-7 col-xs-12 formz">
                ';
                for($i = 0; $i < count($scorecards); $i++){
                    $display .= '<option value="'.$scorecards[$i]['SCORECARD_ID'].'" >'.$scorecards[$i]['Title'].'</option>';
                }
                $display.='
                </select>
             
            </div>
          </div>

          <div class="item form-group" id="files">
            <div id="file">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" >Upload RFP/RFI file<span class="required"></span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12 custom-file">
                    <input style="border-style: none;" type="file" style="width:35vw" name="file[]" class="form-control col-md-7 col-xs-12 custom-file-input formz" required>
                </div>
            </div>
          </div>
        <div id="inputs">
          </div>

          
              

            <!-- Modal content-->
          <div class="modal fade" id="myModal" role="dialog">

          <div class="modal-dialog">
            
                <div class="modal-content">
              <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal">&times;</button>
                   </div>
                   <div class="modal-body">
                      <p id="textmodal" style="text-align:center"></p>
                   </div>
                  <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                   </div>
                </div>

               </div>
            </div>
          <button type="button" id="clicked" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal" style="display:none">Open Modal</button>
      <!-----end of content modal---->

<div class="ln_solid"></div>
 <div id="buttons"><button class="btn" type="button" id="addFile" style="color:red; border-radius:10px" >Add Another File + </button>
       
          <input type="text" name="tk" value="'.token::get_ne("POST_CREATION_OPENLINKS").'" required="" hidden>
             
       </div>   
       

        <div class="form-group">
            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <button class="btn btn-primary" type="reset" >Cancel</button>
        ';
                  switch($_GET['t']){
                    case 1://smme
                        $display .= '<button type="Submit" class="btn btn-success" name="SMME_POST_CREATE">Submit</button>';
                        break;
                    case 2: //company
                        $display .= '<button type="Submit" class="btn btn-success" name="COMPANY_POST_CREATE">Submit</button>';
                      break;
                    case 3://adimin
                        $display .= '<button type="Submit" class="btn btn-success" name="ADMIN_POST_CREATE">Submit</button>';
                      break;
                  }
        $display .='
            </div>
          </div> </div>
        </form>';
        echo $display;
    }
    private function fetchAdmins(){
        $sql = "SELECT * FROM yasccoza_tms_db.users WHERE type=2";
        $table ="yasccoza_openlink_admin_db";
        $query = $this->fetchNoParms($sql, $table);
        return $query;
    }
    private function adminpostFormDisplay(){
        $display = "";
        $scorecards = $this->fetchScoreCards($this->ID);
        $clients = $this->fetchClients();
        $worktype = $this->fetchWorkTypes();
        $admins = $this->fetchAdmins();
        $jobtypes = $this->fetchJobTypes();
        $offices = $this->fetchOFFICES();
        $display .= '
        
        <form id="post_form" data-parsley-validate class="form-horizontal form-label-left" action="../MARKET/ROUTE.php" Method="POST" enctype="multipart/form-data">
        <div id="inputs">
<div class="form-group">
          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">OFFICE you want to purchase from:<span class="required"></span> </label>
          <div class="col-md-6 col-sm-6 col-xs-12">
            <select style="width:35vw" id="office" name="office" required="required" class="form-control col-md-7 col-xs-12 formz" list="clients" >
           
         
            ';
            
            for($i = 0; $i < count($offices); $i++){
                $display .= '<option value="'.$offices[$i]['INDUSTRY_ID'].'" >'.$offices[$i]['office'].'</option>';
            }
            $display.='
               <option value="10">All offices</option>
            </select>  
            </div>
        </div>
        <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Member ID:<span class="required"></span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <input style="width:35vw" id="client" name="client" required="required" class="form-control col-md-7 col-xs-12 formz" list="clients" >
            <datalist id="clients">
            ';
            for($i = 0; $i < count($clients); $i++){
                $display .= '<option value="'.$clients[$i]['CLIENT_ID'].'" >'.$clients[$i]['CLIENT_ID'].' - '.$clients[$i]['company_name'].'</option>';
            }
            $display.='
            </datalist>         
        </div>
        
        </div>
        <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Client Rep:<span class="required"></span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <select style="width:35vw" id="client_rep" name="client_rep" required="required" class="form-control col-md-7 col-xs-12 formz">
            ';
            
            $display.='
            </select>
         
        </div>
      </div>

        <div class="form-group">
        
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Title:<span class="required"></span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
              <input style="width:35vw" type="text" id="first-name" name="title" required="required" class="form-control col-md-7 col-xs-12 formz">
            </div>
          </div>
         <div class="form-group">
        <label for="date" class="control-label col-md-3 col-sm-3 col-xs-12">Description:</label>
        
        <div class="col-md-6 col-sm-6 col-xs-12">
            <textarea name="Description" cols="30" rows="10" class="form-control" id="editor1"></textarea>
        </div>
    </div>
        <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="work_type">Work Type:<span class="required"></span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
                <select style="width:35vw" id="work_type" name="work_type[]" required="required"  class="form-control form-control-sm tags" multiple="multiple"><option></option>';
                    
                    for ($i = 0; $i < count($worktype); $i++) {
                     $display.= '<option value="' . $worktype[$i]['id'] . '"  >' . $worktype[$i]['id'] . ' - ' . $worktype[$i]['task_name'] . '</option>';
                    }
                
                    $display.= ' </select>
                    
            </div>

        </div>
         <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="work_type">Job Order Type:<span class="required"></span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
                <select style="width:35vw" id="jobType" name="jobOrderType" required="required"  class="form-control form-control-sm" ><option value="" selected>Select Job Type</option>';
                    for( $i = 0; $i < count($jobtypes); $i++){
                        $display.= '
                        <option value="'.$jobtypes[$i]["job_type_name"].'">'.$jobtypes[$i]["job_type_name"].'</option>
                     '   
                        ;
                    }
                    
                     
                    //  <div class="form-group">
                    //  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Admin Assigned To:<span class="required"></span>
                    //  </label>
                    //  <div class="col-md-6 col-sm-6 col-xs-12">
                    //      <input style="width:35vw" id="scorecards" name="admin" required="required" class="form-control col-md-7 col-xs-12 formz" list="admins" >
                    //      <datalist id="admins">
                    //      ';
                    //      for($i = 0; $i < count($admins); $i++){
                    //          $display .= '<option value="'.$admins[$i]['ADMIN_ID'].'" >'.$admins[$i]['ADMIN_ID'].' - '.$admins[$i]['First_Name'].'</option>';
                    //      }
                    //      $display.='
                    //      </datalist>         
                    //  </div>
                     
                    //  </div>
                
                    $display.= ' </select>
                    
            </div>

        </div>
        
          <div class="form-group">
          <label for="date" class="control-label col-md-3 col-sm-3 col-xs-12">Start Date:</label>
          
          <div class="col-md-6 col-sm-6 col-xs-12">
              <input style="width:35vw" type="date" "date" name="StartDate" required="required" data-validate-minmax="10,100" class="form-control col-md-7 col-xs-12">
          </div>
        </div>
       
          <div class="form-group">
          <label for="date" class="control-label col-md-3 col-sm-3 col-xs-12">Date of Expiry:</label>
          
          <div class="col-md-6 col-sm-6 col-xs-12">
              <input style="width:35vw" type="date" "date" name="EndDate" required="required" data-validate-minmax="10,100" class="form-control col-md-7 col-xs-12">
          </div>
        </div>
        
          

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Scorecard:<span class="required"></span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <select style="width:35vw" id="scorecards" name="scorecard_id" required="required" class="form-control col-md-7 col-xs-12 formz">
                ';
                for($i = 0; $i < count($scorecards); $i++){
                    $display .= '<option value="'.$scorecards[$i]['SCORECARD_ID'].'" >'.$scorecards[$i]['Title'].'</option>';
                }
                $display.='
                </select>
             
            </div>
          </div>
      <input type="text" name="tk" value="'.token::get_ne("POST_CREATION_OPENLINKS").'" required="" hidden>
          <div class="item form-group" id="files">
            <div id="file">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" >Upload RFP/RFI file<span class="required"></span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12 custom-file">
                    <input style="border-style: none;" type="file" style="width:35vw" name="file[]" class="form-control col-md-7 col-xs-12 custom-file-input formz" required >
                </div>
            </div>
          </div>
            <!-- Modal content-->
          <div class="modal fade" id="myModal" role="dialog">

          <div class="modal-dialog">
            
                <div class="modal-content">
              <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal">&times;</button>
                   </div>
                   <div class="modal-body">
                      <p id="textmodal" style="text-align:center"></p>
                   </div>
                  <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                   </div>
                </div>

               </div>
            </div>
          <button type="button" id="clicked" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal" style="display:none">Open Modal</button>
      <!-----end of content modal---->

       
    
          
       </div>   <div class="ln_solid"></div>
       <div id="buttons"><button class="btn" type="button" id="addFile" style="color:red">Add Another File + </button>
        <div class="form-group">
            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <button class="btn btn-primary" type="reset" >Cancel</button>
        ';
                  switch($_GET['t']){
                    case 1://smme
                        $display .= '<button type="Submit" class="btn btn-success" name="SMME_POST_CREATE">Submit</button>';
                        break;
                    case 2: //company
                        $display .= '<button type="Submit" class="btn btn-success" name="COMPANY_POST_CREATE">Submit</button>';
                      break;
                    case 3://adimin
                        $display .= '<button type="Submit" class="btn btn-success" name="ADMIN_POST_CREATE">Submit</button>';
                      break;
                  }
        $display .='
            </div>
          </div> </div>
        </form>';
        echo $display;
    }    private function fetchClients(){
        $sql = "SELECT * FROM client";
        $table = "yasccoza_openlink_market";
        $result = $this->fetchNoParms($sql, $table);
        return $result;
    }
    private function UploadFile($form,$fileName,$fileTmpName,$fileSize,$fileError){
      
        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));
       $allowed = array('jpg', 'jpeg', 'png', 'pdf', 'xls', 'xlsx', 'doc', 'docx');
        if(in_array($fileActualExt, $allowed)){
            if($fileError== 0){
                if($fileSize < 5000000){                
                    $fileNameDelete = token::encode1($fileName).token::encode1($this->ID)."_".$form.".".$fileActualExt;                   
                    $fileNameNew = token::encode1($fileName).token::encode1($this->ID)."_".$form.".".$fileActualExt;
                      $fileDestination = '../STORAGE/FILES/'.$fileNameNew;
                    if(file_exists($fileNameDelete)){
                      unlink($fileNameDelete);
                    }
                    move_uploaded_file($fileTmpName, $fileDestination);
                    return $fileNameNew;
                }
                else{
                    return "too large";
                }
            }
            else{
                return "file error" ;
            }
        }
        else{
            return "not right file";
        }
    }
    private function fetchClientById($id){
        $sql = "SELECT * FROM client WHERE CLIENT_ID=?";
        $table = "yasccoza_openlink_market";
        $params = array($id);
        $types="i";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    
       private function fetchSMMEById($id){
        $sql = "SELECT * FROM client WHERE CLIENT_ID=?";
        $table = "yasccoza_openlink_smmes";
        $params = array($id);
        $types="i";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    
    
    private function saveCriteria($name,$description, $documents){
         
        $params = array();
        $sql = "INSERT INTO criteria(Description, Name, Document, User_ID, OWNER) VALUES (?,?,?,?,?)";
        $types="sssis";
        if(strcmp($this->TYPE_ENTITY,"ADMIN") == 0){
            $params=array($description,$name, $documents, $this->ID,"OPENLINKS");
        }else{
            $params=array($description,$name, $documents, $this->ID,"USER");
        }
        $table = "CRITERIA";
        $query = $this->save($sql, $types, $params, $table);
    }

    public function viewOptions($id, $t,$scorecard_id,$from){
        $options = $this->fetchOptions($id);
        $question = $this->fetchQuestion($id);
        $display = "<h3>Question -> ".$question[0]['Question']."</h3>";
        
        if(empty($options)){
            $display .= '
            <form method="POST" id="option_form" action="../MARKET/ROUTE.php?d=2">
                <input style="display:none" type="text" name="tk" value="'.token::get_ne("OPTION_CREATION_OPENLINKS").'"  hidden>
                <input style="display:none" type="text" name="question_id" value="'.$id.'"  hidden>
                <table>
        
                    <tbody id="choicesContainer">
                    
                    <tr class="choice">
                        <td id="move">
                            <input class="form-control col-md-7 col-xs-12" type="text" name="choiceText[]" placeholder="Enter your option here" required>
                        </td>
                        <td id="move">
                        <input class="form-control col-md-7 col-xs-12 weight" type="number" name="choiceWeight[]" placeholder="20%" min="0" max="100" required>
                        </td>
                    </tr>

                    </tbody>
                </table>

                <br>
                
                <button class="btn" type="button" id="addchoicebtn">Add Choice</button>
                ';
                switch($t){
                        case 1://smme
                            $display .= '<button type="Submit" class="btn btn-success" id="OPTION_CREATE" name="SMME_OPTION_CREATE">Submi</button>';
                            break;
                        case 2: //company
                            $display .= '<button type="Submit" class="btn btn-success" id="OPTION_CREATE" name="COMPANY_OPTION_CREATE">Submit</button>';
                        break;
                        case 3://adimin
                            $display .= '<button type="Submit" class="btn btn-success" id="OPTION_CREATE" name="ADMIN_OPTION_CREATE">Submit</button>';
                        break;
                    }            
                    $display .= '<br>
                    <br>
                    ';
                   
                    if($from == 1){
                        $display .= '<a class="btn btn-primary" href="criteria_questions.php?s='.$scorecard_id.'&t='.$t.'&w='.$question[0]["CRITERIA_ID"].'">
                        Back
                        </a></form>';
                    }else{
                        $display .= '<a class="btn btn-primary" href="criteria_information.php?t='.$t.'&w='.$question[0]["CRITERIA_ID"].'">
                        Back
                        </a></form>';
                    }
                    
            echo $display;
        }else{
            $this->displayQuestionOptions($options, $scorecard_id, $from);
        }
    }
    public function optionsForm($id, $t,$scorecard_id,$from){
        $question = $this->fetchQuestion($id);
        $options = $this->fetchOptions( $id);
        $total = 0;
        for ($i=0; $i < count($options); $i++) { 
            # code...
            $total += $options[$i]['Weighting'];
        }
        $available = 100 - $total;
        $display = "<h3>Question -> ".$question[0]['Question']."</h3></br>Total weight used currently: <span id='weight'>".$total."%. </span> Left: <span id='weightLeft'>".$available."%</span>";
            $display .= '
            <form id="option_form" method="POST" action="../MARKET/ROUTE.php?d=2">
                <input style="display:none" type="text" name="tk" value="'.token::get_ne("OPTION_CREATION_OPENLINKS").'"  hidden>
                <input style="display:none" type="text" name="question_id" value="'.$id.'" hidden>
                <input style="display:none" type="text" name="scorecard_id" value="'.$scorecard_id.'" hidden>
                <table>
        
                    <tbody id="choicesContainer">
                    
                    <tr class="choice">
                        <td id="move">
                            <input class="form-control col-md-7 col-xs-12" type="text" name="choiceText[]" placeholder="Enter your option here" required>
                        </td>
                        <td id="move">
                        <input class="form-control col-md-7 col-xs-12 weight" type="number" name="choiceWeight[]" placeholder="20%" min="0" max="100" required>
                        </td>
                    </tr>

                    </tbody>
                </table>
<br>
<a class="btn btn-primary" href="question_options.php?s='.$scorecard_id.'&w='.$question[0]["QUESTION_ID"].'&t='.$t.'&d='.$from.'">Back</a>
                <button class="btn" type="button" id="addchoicebtn">Add Choice</button>
                ';
                    switch($t){
                        case 1://smme
                            $display .= '<button type="Submit" class="btn btn-success" id="OPTION_CREATE" name="SMME_OPTION_CREATE">Submit</button>';
                            break;
                        case 2: //company
                            $display .= '<button type="Submit" class="btn btn-success" id="OPTION_CREATE" name="COMPANY_OPTION_CREATE">Submit</button>';
                        break;
                        case 3://adimin
                            $display .= '<button type="Submit" class="btn btn-success" id="OPTION_CREATE" name="ADMIN_OPTION_CREATE">Submit</button>';
                        break;
                    }
            $display .= '</form>';
            echo $display;
    }
    private function fetchOptions($id){
        $sql = "SELECT * FROM `question_choice` WHERE `QUESTION_ID`=?";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function displayQuestionOptions($results, $scorecard_id,$from){
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        $question = $this->fetchQuestion($results[0]["QUESTION_ID"]);
        $display = "";
        $display .=' <h3 class="text-center"> Options for Question ->  '.$question[0]["Question"].'</h3> 
        <table class="table table-striped table-bordered"></br>
        <thead>
          <tr></tr>
            <th>#</th>
            <th>Choice </th>
            <th>Weighting</th>
            <th>Action</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>';
        $sum = 0;
        for($i = 0; $i < count($results); $i++){
            $sum += $results[$i]['Weighting'];
                $display .= '
                <tr>
                <th scope="row">'.($i+1).'</th>
                <td>'.$results[$i]['choice'].'</td>';
                if ($sum >100) {
                    # code...
                    $display .= ' <td style="background:red;color:white">'.$results[$i]['Weighting'].'%</br><small>Weights need to add up to 100%, adjust weights. Total : '.$sum.'%</small></td> ';
                }else{
                    $display .= '   <td>'.$results[$i]['Weighting'].'%</td> '; 
                }
                $display .=' <td><a href="option_edit.php?q='.$results[$i]['QUESTION_ID'].'&d='.$from.'&s='.$scorecard_id.'&t='.$entity.'&w='.$results[$i]['CHOICE_ID'].'"><span>Edit Information </span> <i class="fa fa-pencil"></i></a></td>
                <td><a href="option_delete.php?s='.$scorecard_id.'&q='.$results[$i]['QUESTION_ID'].'&t='.$entity.'&c='.$results[$i]['CHOICE_ID'].'"><span>Remove</span> <i class="fa fa-trash"></i></a></td>
                </tr>
            ';
            }
            $display .= '
            </tbody>
            
            </table>';
            if($from == 1){
                $display .= '<a class="btn btn-primary" href="criteria_questions.php?s='.$scorecard_id.'&t='.$entity.'&w='.$question[0]["CRITERIA_ID"].'">
                Back
                </a></form>';
            }else{
                $display .= '<a class="btn btn-primary" href="criteria_information.php?t='.$entity.'&w='.$question[0]["CRITERIA_ID"].'">
                Back
                </a></form>';
            }
            $display .='
            <a class="btn" href="new_question_options.php?s='.$scorecard_id.'&t='.$entity.'&w='.$results[0]['QUESTION_ID'].'&d='.$from.'"><span>Add More </span> <i class="fa fa-plus"></i></a>
          '; 
            echo $display;
    }
    private function saveQuestion($question, $weight, $criteria_id){
        
        $sql = "INSERT INTO `question`( `Question`, `Weighting`, `CRITERIA_ID`) VALUES (?,?,?)";
        $table = "QUESTION";
        $types="sii";
        $params=array($question, $weight, $criteria_id);
        $query = $this->save($sql, $types, $params, $table);
    }
    private function saveOption($option, $weight, $id){
        $sql = "INSERT INTO `question_choice`(`choice`, `Weighting`, `QUESTION_ID`) VALUES (?,?,?)";
        $table = "QUESTION";
        $types="sii";
        $params=array($option, $weight, $id);
        $query = $this->save($sql, $types, $params, $table);
    }
    private function saveScorecardCriteria($scorecard, $criteria, $weight){
        $sql = "INSERT INTO scorecard_criteria(SCORECARD_ID, CRITERIA_ID, Weighting) VALUES (?,?,?)";
        $table = "yasccoza_openlink_market";
        $types="iii";
        $params=array($scorecard, $criteria,$weight);
        $query = $this->save($sql, $types, $params, $table);
    }
    
    private function saveScoreCard($title, $other, $date, $owner){
        $sql = "INSERT INTO scorecard(Title,Other, Date_of_Expiry, User_ID, OWNER) VALUES (?,?,?,?,?)";
        $table = "SCORECARD";
        $types="sssis";
        $params=array($title, $other, $date, $this->ID, $owner);
        $query = $this->save($sql, $types, $params, $table);
        $id = $this->master->getLastID();
        return $id;

    }
    private function updateScoreCard($title, $other, $date, $id){
        $sql = "UPDATE scorecard SET Title=?,Other=?, Date_of_Expiry=? WHERE SCORECARD_ID= ?";
        $table = "SCORECARD";
        $types="sssi";
        $params=array($title, $other, $date, $id);
        $query = $this->update($sql, $types, $params, $table);
    }
    private function updateCriteria($name, $desc, $doc, $id){
        $sql = "UPDATE criteria SET Name=?, Description=?, Document=? WHERE CRITERIA_ID= ?";
        $table = "yasccoza_openlink_market";
        $types="sssi";
        $params=array($name, $desc, $doc, $id);
        $query = $this->update($sql, $types, $params, $table);
    }
    private function updateQuestion($question, $weight, $id){
        $sql = "UPDATE question SET Question=?, Weighting=? WHERE QUESTION_ID= ?";
        $table = "yasccoza_openlink_market";
        $types="sii";
        $params=array($question, $weight, $id);
        $query = $this->update($sql, $types, $params, $table);
    }
    public function updateWeightAdjust($weight, $scorecard, $criteria){
        $sql = "UPDATE scorecard_criteria SET Weighting=? WHERE SCORECARD_ID= ? AND CRITERIA_ID=?";
        $table = "yasccoza_openlink_market";
        $types="iii";
        $params=array($weight, $scorecard, $criteria);
        $query = $this->update($sql, $types, $params, $table);
    }
    private function updateOption($choice, $weight, $id){
        $sql = "UPDATE question_choice SET choice=?, Weighting=? WHERE CHOICE_ID= ?";
        $table = "yasccoza_openlink_market";
        $types="sii";
        $params=array($choice, $weight, $id);
        $query = $this->update($sql, $types, $params, $table);
    }
    private function fetchQuestion($id){
        $sql = "SELECT * FROM `question` WHERE QUESTION_ID = ?";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function fetchScoreCards($id){
        if(strcmp($this->TYPE_ENTITY,"ADMIN") == 0){
            $sql = "SELECT * FROM `scorecard` WHERE User_Id = ? OR OWNER = 'OPENLINKS'";
        }else{
            $sql = "SELECT * FROM `scorecard` WHERE User_Id = ? AND OWNER = 'USER' OR OWNER = 'OPENLINKS' ORDER BY OWNER asc";
        }
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
      private function fetchSMMEScoreCards($id){
       
        $sql = "SELECT * FROM `scorecard` WHERE User_Id = ? AND OWNER = 'USER'";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function fetchWorkTypz($id){
        $sql = "SELECT task_list.*, SUM(user_productivity.duration) AS durationz
        FROM yasccoza_tms_db.task_list
        JOIN yasccoza_tms_db.user_productivity ON task_list.id = user_productivity.task_id
        WHERE user_productivity.task_id IS NOT NULL
        AND task_list.id = $id
        GROUP BY user_productivity.task_id";
        $table = "yasccoza_tms_db";
        $result = $this->fetchNoParms($sql, $table);
        return $result;
    }
     private function fetchWorkType(){
        $sql = "SELECT * FROM yasccoza_tms_db.task_list";
        $table = "yasccoza_tms_db";
        $result = $this->fetchNoParms($sql, $table);
        return $result;
    }
    private function fetchScoreCard($id){
        $sql = "SELECT * FROM `scorecard` WHERE User_Id = ? ORDER BY SCORECARD_ID DESC LIMIT 1";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $result = $this->fetch($table, $sql, $types, $params);
        
        return $result;
    }
    public function displayCard($id){
        $card = $this->fetchCardById($id);
      
        $display = '<table class="table"><tbody>
        <tr><td>Title:  </td><td>'.$card[0]['Title'].'</td></tr>
        <tr><td>Other Information:  </td><td>'.$card[0]['Other'].'</td></tr>
           <tr><td>Date of Expiry:</td><td>'.$card[0]['Date_of_Expiry'].'</td></tr>
           
        </tbody></table>';
        echo $display;
    }
    public function displayCriteriabyId($id){
        $card = $this->fetchCriteriaById($id);
        $display = '<table class="table"><tbody>
        <tr><td>Title:  </td><td>'.$card[0]['Title'].'</td></tr>
        <tr><td>Other Information:  </td><td>'.$card[0]['Other'].'</td></tr>
           <tr><td>Date of Expiry:</td><td>'.$card[0]['Date_of_Expiry'].'</td></tr>
        </tbody></table>';
        echo $display;
    }
    public function displayAllCriteria($id){
        $criteria = $this->fetchAllCriteria($id);
        $this->Allcriteria($criteria);
    }
    public function SingleDisplayCriteria($id, $scorecard_id){
        
        $result = $this->getAllQuestions($id);
        $criteria = $this->fetchCriteriaByCriteriaId($id);
        $this->SingleCriteria($result, $criteria, $scorecard_id);
    }
    public function removeCriteria($scorecard,$id ){
        $this->removeFromScorecard($scorecard,$id);
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        header("location: ../".$this->TYPE_ENTITY."/scorecard_finalview2.php?w=".$scorecard."&t=".$entity."&result=deleted");
        exit();
    }
    public function removeQuestion($question,$criteria ){
        $this->removeQuestionCriteria($question,$criteria);
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        header("location: ../".$this->TYPE_ENTITY."/criteria_information.php?w=".$criteria."&t=".$entity."&result=deleted");
        exit();
    }
    public function removeOption($question,$choice, $scorecard){
        $this->removeQuestionOption($question,$choice);
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        header("location: ../".$this->TYPE_ENTITY."/question_options.php?d=2&s=".$scorecard."&w=".$question."&t=".$entity."&result=deleted");
        exit();
    }
    public function saveJobOrderExpenses($expenses, $post_id){
        $sql = "INSERT INTO yasccoza_openlink_market.job_order_expense( `POST_ID`, `EXPENSE_ID`, `USER_ID`) VALUES (?,?,?)";
        $types="iii";  
        $table = "yasccoza_openlink_market";
        for($i = 0; $i < count($expenses); $i++){
            $params=array($post_id, $expenses[$i], $this->ID);
            $query = $this->save($sql, $types, $params, $table);
        }
        header("location: ../".$this->TYPE_ENTITY."/market_posts.php?result=success");
        exit(); 
    }
    public function displayExepnses($post){
        $expenses = $this->fetchExpenses($this->ID);
        $this->expensesView($expenses, $post);
    }
    private function fetchExpenses($ID){
        $sql = "SELECT * FROM yasccoza_openlink_smmes.expense_summary WHERE SMME_ID = ?";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($ID);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function expensesView($result, $id){
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }

        $display = '<form hidden></form><form data-parsley-validate class="form-horizontal form-label-left" action="../MARKET/ROUTE.php" Method="POST">
        <table class="table table-striped table-bordered">
        <thead>
         
            <th>Expense</th>
            <th>Expense Type</th> 
            <th>Select</th>
        </thead>
        <tbody>
        ';
        for($i = 0; $i < count($result);$i++){
            $display .='
            <tr>
                
                <td>'.$result[$i]['product_name'].'</td>
                <td>';
                if($result[$i]['type_of_expense']==0){
                    $display .=' Direct ';
                }else{
                    $display .='Indirect ';
                }
                $display .='</td>
                <td><input type="checkbox" name="selected_expenses[]" value="'.$result[$i]['EXPENSE_NUMBER'].'"></td>
            </tr>
     ';
    
        }
       
        $display .='
        </tbody></table>';
            $display .='

            <input type="text" name="tk" value="'.token::get_ne("SMME_EXPENSE_JOBORDER_OPENLINKS").'" required="" hidden>
            <input type="text" name="post_id" value="'.$id.'" required="" hidden>
           <div class="ln_solid"></div>
            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                <button type="Submit" class="btn btn-success" name="SMME_EXPENSE_JOBORDER">Save</button>
                </div>
              </div>
            </form>';
       
        
        echo $display;
    }
    private function removeFromScorecard($s,$id){
        $sql ="DELETE FROM scorecard_criteria WHERE CRITERIA_ID= ? AND SCORECARD_ID=?";
        $types="ii";
        $params = array($id, $s);
        $table = "scorecard_criteria";
        $this->delete($sql, $table, $types, $params);
    }
    private function removeQuestionCriteria($q,$c){
        $sql ="DELETE FROM question WHERE CRITERIA_ID= ? AND QUESTION_ID=?";
        $types="ii";
        $params = array($c, $q);
        $table = "question";
        $this->delete($sql, $table, $types, $params);
    }
    private function removeQuestionOption($q,$c){
        $sql ="DELETE FROM question_choice WHERE CHOICE_ID= ? AND QUESTION_ID=?";
        $types="ii";
        $params = array($c, $q);
        $table = "question_choice";
        $this->delete($sql, $table, $types, $params);
    }

    private function fetchCardById($id){
        $sql = "SELECT * FROM `scorecard` WHERE SCORECARD_ID = ? ";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $result = $this->fetch($table, $sql, $types, $params);
        
        return $result;
    }
  
    private function fetchCriteria($ID){
        $sql = "";
        if(strcmp($this->TYPE_ENTITY, "ADMIN")==0){
            $sql = "SELECT * FROM criteria WHERE User_Id = ? OR OWNER='OPENLINKS' ORDER BY CRITERIA_ID DESC LIMIT 1";
        }else{
            $sql = "SELECT * FROM criteria WHERE User_Id = ? AND OWNER = 'USER' ORDER BY CRITERIA_ID DESC LIMIT 1";
        }
   
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($ID);
        $result = $this->fetch($table, $sql, $types, $params);
       
        return $result;
    }
    private function fetchOption($ID){
        $sql = "SELECT * FROM question_choice WHERE CHOICE_ID = ?";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($ID);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function fetchCriteriaById($ID){
        if(strcmp($this->TYPE_ENTITY,"ADMIN")==0){
            $sql = "SELECT * FROM criteria C WHERE C.OWNER = 'OPENLINKS' OR User_Id = ?";
        }else{
            $sql = "SELECT * FROM criteria C WHERE C.OWNER = 'USER' AND User_Id = ?";
        }
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($ID);
        $result = $this->fetch($table, $sql, $types, $params);
       
        return $result;
    }
    private function fetchCriteriaByCriteriaId($ID){
        $sql = "SELECT * FROM criteria WHERE CRITERIA_ID = ? ";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($ID);
        $result = $this->fetch($table, $sql, $types, $params);
       
        return $result;
    }
    private function fetchAllCriteria($id){
        $sql = "SELECT * FROM criteria c, scorecard_criteria sc WHERE sc.CRITERIA_ID = c.CRITERIA_ID AND sc.SCORECARD_ID = ?";
        $types="i";
        $params =array($id);
        $table = "yasccoza_openlink_market";
        $query = $this->fetch($table, $sql, $types, $params);
        return $query;
    }
    public function dislpayCriteria($id){
        $sql = "SELECT * FROM criteria, scorecard_criteria sc WHERE sc.CRITERIA_ID = c.CRITERIA_ID AND sc.SCORECARD_ID = ? ORDER BY CRITERIA_ID DESC LIMIT 1";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $results = $this->fetch($table, $sql, $types, $params);
        if(empty($results2)){
            $criteria = new CRITERIA($results[0]['Description'],$results[0]['Name'],$results[0]['Weighting']);
        }else{
            $criteria = new CRITERIA($results[0]['Description'],$results[0]['Name'],$results[0]['Weighting']);
        }
        
        $display = $criteria->getSimpleDisplay();
        echo $display;
    }

    private function InitialiseScoreCard(){
        $this->scorecards = array();
        if(strcmp($this->TYPE_ENTITY,"ADMIN")==0){
           $results = $this->fetchScoreCards($this->ID); 
        }else{
           $results = $this->fetchSMMEScoreCards($this->ID);  
        }
        for($i = 0; $i < count($results); $i++){
            $scorecard = new SCORECARD($results[$i]['Title'],$results[$i]['Other'],$results[$i]['Date_of_Expiry'],$results[$i]['SCORECARD_ID']);
            array_push($this->scorecards, $scorecard);
        }
    }
    private function InitialiseCriteria(){
        $this->criteria = array();
        $results = $this->fetchCriteriaById($this->ID);
        for($i = 0; $i < count($results); $i++){
            $crit = new CRITERIA($results[$i]['Description'],$results[$i]['Name'],$results[$i]['CRITERIA_ID']);
            array_push($this->criteria, $crit);
        }
    }
    public function displayScorecards(){
       $this->InitialiseScoreCard();
       $this->SCORECARD_VIEW();
    }
    public function displayWorkTypes(){
       $result = $this->fetchWorkTypes();
        $this->Worktype_VIEW($result);
    }
    public function displaySingleWorkType($id){
        $result = $this->fetchWorkTypz($id);
        $this->singleWorkTypeView($result);
     }
    public function displayMarketPosts($id){
        
        $type = $this->fetchtype($id);

        $singletype = is_array($type) ? reset($type) : $type;

        if (is_array($singletype)) {
            $singletype = reset($singletype);
        }
        $jobPeriods = $this->fetchJobPeriods($id,$singletype);
        $this->displayJobPeriods($jobPeriods);
    }
    
    
      public function displaymyMarketPosts(){
        $jobPeriods = $this->fetchmyJobPeriods();
        $this->displaymyJobPeriods($jobPeriods);
    }
    
    public function displayPeriodPosts($period,$id){
        
        $type = $this->fetchtype($id);

        $singletype = is_array($type) ? reset($type) : $type;

        if (is_array($singletype)) {
            $singletype = reset($singletype);
        }
        
        $jobs = $this->fetchPosts($period,$id,$singletype);
        $this->PostsView($jobs,$period);
    }
    
     public function displaymyPeriodPosts($period){
        $jobs = $this->fetchmyInduPosts($period);
        $this->PostsView($jobs,$period);
    }
    
     public function displayPostReceived(){
         
         $period="";
        $jobs = $this->wwwe();
        $this->PostsView($jobs,$period);
    }
    
    
 public function displaySMMEsandJobs(){
     

        $SMMEandJobs = $this->fetchAllPostAndSmmes();
        $this->hie($SMMEandJobs);
    }
    
    
    public function displayPostsToVerify(){
        $posts = $this->fetchPostsToVerify2();
        // if(empty($posts)){
        //     $posts = $this->fetchPostsToVerify2();
        // }
        $this->verifyPostView($posts);
    }
    private function fetchAdminAssign($id){
        $sql = "SELECT * FROM market_post WHERE USER_ID =?";
        $types = "i";
        $params = array($id);
        $table = "yasccoza_openlink_market";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    
  
    
    
   private function PostsView($result, $period)
{
    $entity = 0;
    switch ($this->TYPE_ENTITY) {
        case "SMME":
            $entity = 1;
            break;
        case "COMPANY":
            $entity = 2;
            break;
        case "ADMIN":
            $entity = 3;
            break;
    }

    $display = "";

    if (empty($result)) {
        $display .= '<h3 class="text-capitalize text-center">No Posts</h3>';
    } else {
        $display .= '<div class="post-grid">';

        foreach ($result as $row) {
            if ($row['APPROVED'] == 1) {
                // Worktype parsing
                $worktypes = $row['WORKTYPE'];
                $WworktypeIDS = explode(",", $worktypes);

                // Expiry date logic
                $expiryDateTime = new DateTime($row['EXPIRY']);
                $todayDateTime = new DateTime();
                $interval = $todayDateTime->diff($expiryDateTime);
                $daysLeft = $interval->days;
                $message = $daysLeft < 0 ? 'Expired'
                    : ($daysLeft === 0 ? 'Expires today'
                    : ($daysLeft === 1 ? 'Expires tomorrow'
                    : ($daysLeft <= 7 ? "Expires in $daysLeft days" : "Expires in " . ceil($daysLeft / 7) . " weeks")));

                // Time difference for created
                $mysqlTimestampUnix = strtotime($row['Created']);
                $timeDifferenceSeconds = abs(time() - $mysqlTimestampUnix);
                if ($timeDifferenceSeconds < 3600) {
                    $timeDifference = floor($timeDifferenceSeconds / 60);
                    $timeUnit = 'minutes';
                } elseif ($timeDifferenceSeconds < 86400) {
                    $timeDifference = floor($timeDifferenceSeconds / 3600);
                    $timeUnit = 'hours';
                } else {
                    $timeDifference = floor($timeDifferenceSeconds / 86400);
                    $timeUnit = 'days';
                }

                // Format dates
                $formattedStartDate = date("j F Y", strtotime($row['Start_Date']));
                $formattedEndDate = date("j F Y", strtotime($row['EXPIRY']));

                // Shorten title
                $titleWords = explode(' ', $row['Title']);
                $shortTitle = implode(' ', array_slice($titleWords, 0, 10));

                // Build job card
                $display .= '<div class="job-card">';
                $display .= '<div class="job-header">';

                if (!empty($row['FullName'])) {
                    $display .= '<p><strong><span style="color:red;">' . htmlspecialchars($row['FullName']) . '</span> sent this job to you</strong></p>';
                    $display .= '<p><strong>Sent on:</strong> <span style="color:#337ab7;">' . date('Y-m-d', strtotime($row['Date'])) . '</span></p>';
                    $display .= '<hr>';
                }

                $display .= '<p style="font-size:17px"><strong>Job ID:</strong> <span>' . $row['POST_ID'] . '</span></p>';
                $statusClass = ($row['status'] == "Done") ? "status status-done" : "status status-pending";
                $display .= '<p class="' . $statusClass . '">' . htmlspecialchars($row['status']) . '</p>';
                $display .= '<h3 class="text-capitalize" style="font-size:17px" >' . htmlspecialchars($shortTitle) . '...</h3>';
                $display .= '</div>'; // job-header

                $display .= '<div class="job-meta" style="font-size:13px">';
                $display .= $timeDifference . ' ' . $timeUnit . ' ago by ' . htmlspecialchars($row['Legal_name']);
                $display .= '</div>';

                $display .= '<table class="job-details">
                                <tr><td>Start date:</td><td>' . $formattedStartDate . '</td></tr>
                                <tr><td>End date:</td><td>' . $formattedEndDate . '</td></tr>
                             </table>';

                $display .= '<ul class="timeline">
                                <br>
                                <li>
                                    <a href="job_order_info.php?d=' . $row['POST_ID'] . '&q=' . $row['USER_ID'] . '&p=' . $period . '" class="view-btn">
                                        View More
                                    </a>
                                </li>
                             </ul>';

                if (empty($row['FullName'])) {
                    $display .= '<p style="font-size:12px;">Click View to see more details and respond to the Job</p>';
                }

                $display .= '</div>'; // job-card
            }
        }

        $display .= '</div>'; // post-grid
    }

    echo $display;
}

    private function verifyPostView($result){

        
           $display ="";
            if(empty($result)){
                $display .='<h3 class="text-capitalize text-center"> No Posts to verify</h3>';
            }else{
                for($i = 0; $i < count($result); $i++){
                    //print_r($result);
                    if($result[$i]['APPROVED'] ==0 &&($result[$i]['VERIFIED_BY'] ==0 ||$result[$i]['VERIFIED_BY']==$this->ID)){

                    // Convert the MySQL timestamp to a Unix timestamp
                    $mysqlTimestampUnix = strtotime($result[$i]['Created']);

                    // Get the current timestamp
                    $currentTimestamp = time();

                    // Calculate the time difference in seconds
                    $timeDifferenceSeconds = abs($currentTimestamp - $mysqlTimestampUnix);


                    // Convert the timestamp to a Unix timestamp
                    $startTime = strtotime($result[$i]['Start_Date']);
                    $endTime = strtotime($result[$i]['EXPIRY']);

                    // Format the date in the desired format
                    $formattedStartDate = date("j F Y", $startTime);
                    $formattedEndDate = date("j F Y", $endTime);
                    
                    $titleWords = explode(' ', $result[$i]['Title']);  // Split the title into an array of words
                    $firstFiveWords = array_slice($titleWords, 0, 10); // Get the first five words
                    $shortTitle = implode(' ', $firstFiveWords); 
                    
                    $display .=' <div class="col-lg-6 col-md-6 col-sm-6  ">
                        <div class="x_panel" style="height: 370px; border: 4px solid #337ab7; border-radius: 5px;">
                        <div class="x_title">
                            <h2 class="text-capitalize">Job Orders to verify</h2>
                            <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="#">Settings 1</a>
                                    <a class="dropdown-item" href="#">Settings 2</a>
                                </div>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <ul class="list-unstyled timeline">';
                    $display .='
                    <li>
                      <div class="block">
                        <div class="tags">
                       <a href="job_order_info.php?d='.$result[$i]['POST_ID'].'&q='.$result[$i]['USER_ID'].'"  class="tag">
                            <span>Verify</span>
                          </a>
                           </div>
                        <div class="block_content">
                          <h2 class="title">
                                          '.$result[$i]['Legal_name'].'
                                      </h2>
                          <div class="byline">
                            <span>';
                            if ($timeDifferenceSeconds < 3600) {
                                // If less than an hour ago, display in minutes
                                $timeDifference = floor($timeDifferenceSeconds / 60);
                                $timeUnit = 'minutes';
                            } elseif ($timeDifferenceSeconds < 86400) {
                                // If less than 24 hours ago, display in hours
                                $timeDifference = floor($timeDifferenceSeconds / 3600);
                                $timeUnit = 'hours';
                            } else {
                                // If more than 24 hours ago, display in days
                                $timeDifference = floor($timeDifferenceSeconds / 86400);
                                $timeUnit = 'days';
                            }
                            $display .=$timeDifference.' '.$timeUnit.' ago by '.$result[$i]['Legal_name'].'
                          </div>
                           <p class="excerpt"> Job ID:  <span style="color:red">
                             '.$result[$i]['POST_ID'].' </span>
                          </p>
                          <p class="excerpt"> Title:
                             '.$shortTitle.'
                          </p>
                          <table>
                          <tr>
                                <td>
                                    Start date: 
                                </td>
                                <td>
                                ' .$formattedStartDate.'
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    End date: 
                                </td>
                                <td>
                                ' .$formattedEndDate.'
                                </td>
                            </tr>
                            
    
                          </table>
                        </div>
                      </div>
                    <p style="font-size:15px"><br>Click Verify to view more information about this job, before final verification</p>
                    </li>';
                    $display .='
                            </ul>

                        </div>
                        </div>
                    </div>';
                }else if($result[$i]['APPROVED'] ==0 && $result[$i]['VERIFIED_BY'] ==1){
                    
                    $admin_id = $this->fetchPostAdmins($result[$i]['POST_ID']);
                    for( $j = 0; $j < count($admin_id);$j++)
                    {
                        
                        if($admin_id[$j]["ADMIN_ID"]== $this->ID){
                                // Convert the MySQL timestamp to a Unix timestamp
                                $mysqlTimestampUnix = strtotime($result[$i]['Created']);
            
                                // Get the current timestamp
                                $currentTimestamp = time();
            
                                // Calculate the time difference in seconds
                                $timeDifferenceSeconds = $currentTimestamp - $mysqlTimestampUnix;
            
            
                                // Convert the timestamp to a Unix timestamp
                                $startTime = strtotime($result[$i]['Start_Date']);
                                $endTime = strtotime($result[$i]['EXPIRY']);
            
                                // Format the date in the desired format
                                $formattedStartDate = date("j F Y", $startTime);
                                $formattedEndDate = date("j F Y", $endTime);
                                
                                  $titleWords = explode(' ', $result[$i]['Title']);  // Split the title into an array of words
                    $firstFiveWords = array_slice($titleWords, 0, 10); // Get the first five words
                    $shortTitle = implode(' ', $firstFiveWords); 
                                $display .=' <div class="col-lg-6 col-md-6 col-sm-6  ">
                                    <div class="x_panel"  style="height: 370px; border: 4px solid #337ab7; border-radius: 5px;">
                                    <div class="x_title">
                                        <h2 class="text-capitalize">Job Orders to verify</h2>
                                        <ul class="nav navbar-right panel_toolbox">
                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="#">Settings 1</a>
                                                <a class="dropdown-item" href="#">Settings 2</a>
                                            </div>
                                        </li>
                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                                        </li>
                                        </ul>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <ul class="list-unstyled timeline">';
                                $display .='
                                <li>
                                  <div class="block">
                                    <div class="tags">
                                      <a href="job_order_info.php?d='.$result[$i]['POST_ID'].'" class="tag">
                                        <span>Verify</span>
                                      </a>
                                    </div>
                                    <div class="block_content">
                                      <h2 class="title">
                                                      '.$result[$i]['Title'].'
                                                  </h2>
                                      <div class="byline">
                                        <span>';
                                        if ($timeDifferenceSeconds < 86400) {
                                            // If less than 24 hours ago, display in hours
                                            $timeDifference = floor($timeDifferenceSeconds / 3600);
                                            $timeUnit = 'hours';
                                        } else {
                                            // If more than 24 hours ago, display in days
                                            $timeDifference = floor($timeDifferenceSeconds / 86400);
                                            $timeUnit = 'days';
                                        }
                                        $display .=$timeDifference.' '.$timeUnit.' ago by '.$result[$i]['Legal_name'].'
                                      </div>
                                      <p class="excerpt">
                                        '.$result[$i]['Description'].'
                                      </p>
                                       <p class="excerpt">
                                        '.$result[$i]['Description'].'
                                      </p>
                                      <table>
                                      <tr>
                                            <td>
                                                Start date: 
                                            </td>
                                            <td>
                                            ' .$formattedStartDate.'
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                End date: 
                                            </td>
                                            <td>
                                            ' .$formattedEndDate.'
                                            </td>
                                        </tr>
                                        
                                        
                                      </table>
                                    </div>
                                  </div>
                                </li>';
                                $display .='
                                        </ul>
            
                                    </div>
                                    </div>
                                </div>';
                        }
                    }                    
                }
                
            }
                
            }
            
            
      echo $display;
    }
    private function fetchPostAdmins($post_id){ 
        $sql = "SELECT p.ADMIN_ID FROM yasccoza_openlink_market.post_admins p WHERE p.POST_ID =?";
        $types = "i";
        $params = array($post_id);
        $table = "yasccoza_openlink_market";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    public function ResponseForm($id,$post_id, $yess){
        
        $emailz = $this->fetchEmail();
        $checkresponse=$this->fetchResponsed($post_id);
        $criteria = $this->fetchScorecardCriteriaID($id);
        $this->responseWizard($criteria, $post_id, $checkresponse, $yess, $emailz);
        
    }
    
    
    private function fetchResponsed($post_id){
        $sql = "SELECT User_ID FROM responsescore WHERE User_ID=? AND POST_ID=?";
        $types = "ii";
        $params = array($this->ID,$post_id);
        $table = "yasccoza_openlink_market";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    
     private function fetchEmail(){
        $sql = "SELECT email FROM users WHERE id=?";
        $types = "i";
        $params = array($this->ID);
        $table = "yasccoza_tms_db";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    
    
private function hie($SMMEandJobs) {
    // Main container with flex display
    $display = '<div style="display: flex; justify-content: space-between; padding: 20px; background-color: #f9f9f9; border: 1px solid #ccc; border-radius: 6px;">';

    // Form with action and method
    $display .= '<form hidden></form><form method="POST" action="../MARKET/ROUTE.php" style="width: 100%;">';
    $display .='<input type="hidden" name="tk" value="'.token::get_ne("LINKS").'" required>';
            

    // Flex container for selects to ensure they are side by side
    $display .= '<div style="display: flex; justify-content: space-between; width: 100%;">';

    // Select for POST_ID
    $display .= '<div style="width: 48%;">'; // Slightly less than half to fit side by side
    $display .= '<h2>Jobs</h2>';
    $display .= '<select id="post_select" name="post_ids[]" class="select2_multiple form-control" multiple="multiple" style="height:400px">';
    foreach ($SMMEandJobs['posts'] as $post) {
        $words = explode(' ', $post['Title']);
        $shortTitle = implode(' ', array_slice($words, 0, 4)) . '...';
        $display .= '<option value="' . htmlspecialchars($post['POST_ID']) . '">' . htmlspecialchars($post['POST_ID']) . ' - ' . htmlspecialchars($shortTitle) . '</option>';
    }
    $display .= '</select>';
    $display .= '</div>';

    // Select for Legal_name
    $display .= '<div style="width: 48%;">'; // Slightly less than half to fit side by side
    $display .= '<h2>Companies</h2>';
    $display .= '<select id="smme_select" name="smme_ids[]" class="select2_multiple form-control" multiple="multiple" style="height:400px">';
    foreach ($SMMEandJobs['smmes'] as $smme) {
        $display .= '<option value="' . htmlspecialchars($smme['SMME_ID']) . '">' . htmlspecialchars($smme['Legal_name']) . '</option>';
    }
    $display .= '</select>';
    $display .= '</div>';

    $display .= '</div>'; // Close flex container for selects

    // Submit buttons
    $display .= '<div style="display: flex; justify-content: center; margin-top: 20px;">';
      $display .=' <a href="market_posts.php?t=3>  <button type="button" class="btn btn-primary"> <- Back
                                                    </button></a>';
    $display .= '<button type="Submit" class="btn btn-primary" name="JOB_SMME_LINK">Submit</button>';
    $display .= '</div>';

    $display .= '</form>';
    $display .= '</div>'; // Close main container

    echo $display;
}





    

    
    
   private function responseView($criteria, $post_id, $checkresponse, $yess, $emailz) {
       
      
    $display = '
    <!-- start accordion -->
    <div class="accordion" id="accordion1" role="tablist" aria-multiselectable="true" style="page-break-inside: avoid">
        <form method="POST" action="../MARKET/ROUTE.php?d=2">
            <input type="hidden" name="tk" value="'.token::get_ne("RESPONSE_CREATION_OPENLINKS").'" required>
            <input type="hidden" name="SCORECARD_ID" value="'.$criteria[0]['SCORECARD_ID'].'" required>
            <input type="hidden" name="POST_ID" value="'.$post_id.'" required>
            
            ';

             if ($this->TYPE_ENTITY=="ADMIN") {
                 
                  $display .= '<p style="color:red; font-size:15px; font-weight:bold; text-decoration:underline">Job ID: '.$post_id.'</p><br>';
                 
                 if($yess==1){
                     
                           $display .= '
    <p class="control-label col-md-3 col-sm-3 col-xs-3">SEND FORM TO: </p><label for="username" class="control-label col-md-3 col-sm-3 col-xs-3" style="color:red">' . $emailz[0]['email'] . '</label>
    <br>
    <br>
';
      
                 }
                 
                  
            
                $display .= '
                    <label for="username" class="control-label col-md-3 col-sm-3 col-xs-3" style="font-size:15px">Company you responding for:</label>
                    <input style="width:35vw" id="company" name="company" class="form-control col-md-7 col-xs-12" type="text" required>
                    <br>
                    <br>
                    <br>
                    <br>
                ';
            
                        
                 
            }
            
            
    if ($this->TYPE_ENTITY=="ADMIN") {
                
                 
        
        $number = 1;
        $current_choice_id = 0;

        foreach ($criteria as $z => $criterion) {
            $questions = $this->getAllQuestions($criterion['CRITERIA_ID']);
            $c = $this->fetchCriteriaByCriteriaId($criterion['CRITERIA_ID']);

            $display .= '
                <div class="panel" style="page-break-inside: avoid;">
                    <div class="panel-heading" style="background-color:#172d44" role="tab" id="headingOne'.($z+1).'" data-toggle="collap" data-parent="#accordion1" href="#collapseOne'.($z+1).'" aria-expanded="true" aria-controls="collapseOne">
                        <h4 style="color:white !important" class="panel-title">'.$number.'->'.$c[0]["Name"].'</h4>
                    </div>
                         
                    <div id="collapseOne'.($z+1).'" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne" style="page-break-inside: avoid">
                        <div class="panel-body">
                            <ul class="list-unstyled msg_list">
                            
            ';

            foreach ($questions as $i => $question) {
                $question_id = $question['QUESTION_ID'];
                $choices = $this->fetchOptions($question_id);

                if (!empty($choices)) {
                    $display .= '
                        <li style="background-color:transparent !important; page-break-inside: avoid">
                            <div><p style="color:blue !important; font-weight:bold;" class="hiey">'.$question["Question"].'</p>
                                <input type="hidden" name="question_id[]" value="'.$question["QUESTION_ID"].'" required>
                                <ol><hr style=" border-top: 2px dashed #8c8b8b;">
                    ';

                    foreach ($choices as $x => $choice) {
                        $display .= '
                            <li style="background-color:#337ab7; width:280px !important; page-break-inside: avoid">
                                <input type="radio" id="option'.$x.'" name="choice_'.$current_choice_id.'" value="'.$choice["CHOICE_ID"].'">
                                <label style="margin-top:10px; margin-left:5px; color:white" for="option'.$x.'">'.$choice["choice"].'</label>
                            </li>
                        ';
                    }
                    $current_choice_id++;
                    $display .= '</ol></div></li>';
                }
            }

            $display .= '
                            </ul>
                        </div>
                    </div>
                </div>
            ';
            $number++;
        }
} elseif ($this->TYPE_ENTITY=="SMME"){
    
    $display .= '<p style="color:red; font-size:15px; font-weight:bold; text-decoration:underline">Job ID: '.$post_id.'</p><br>';
    
    if (empty($checkresponse)){
        
        $number = 1;
        $current_choice_id = 0;

        foreach ($criteria as $z => $criterion) {
            $questions = $this->getAllQuestions($criterion['CRITERIA_ID']);
            $c = $this->fetchCriteriaByCriteriaId($criterion['CRITERIA_ID']);

            $display .= '
                <div class="panel">
                    <div class="panel-heading" style="background-color:#172d44" role="tab" id="headingOne'.($z+1).'" data-toggle="collap" data-parent="#accordion1" href="#collapseOne'.($z+1).'" aria-expanded="true" aria-controls="collapseOne">
                        <h4 style="color:white !important" class="panel-title">'.$number.'->'.$c[0]["Name"].'</h4>
                    </div>
                         
                    <div id="collapseOne'.($z+1).'" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body">
                            <ul class="list-unstyled msg_list">
                            
            ';

            foreach ($questions as $i => $question) {
                $question_id = $question['QUESTION_ID'];
                $choices = $this->fetchOptions($question_id);

                if (!empty($choices)) {
                    $display .= '
                        <li style="background-color:transparent !important">
                            <div><p style="color:#032033 !important">'.$question["Question"].'</p>
                                <input type="hidden" name="question_id[]" value="'.$question["QUESTION_ID"].'" required>
                                <ol><hr>
                    ';

                    foreach ($choices as $x => $choice) {
                        $display .= '
                            <li style="background-color:#337ab7;">
                                <input type="radio" id="option'.$x.'" name="choice_'.$current_choice_id.'" value="'.$choice["CHOICE_ID"].'">
                                <label style="margin-top:10px; margin-left:5px; color:white" for="option'.$x.'">'.$choice["choice"].'</label>
                            </li>
                        ';
                    }
                    $current_choice_id++;
                    $display .= '</ol></div></li>';
                }
            }

            $display .= '
                            </ul>
                        </div>
                    </div>
                </div>
            ';
            $number++;
        }
    } else{
        
        print($checkrepsonse);
         $display .= '
         
     
                <p style="color:red; font-size:15px">You have already responsed you can only upload files <br><br></p>
                  
            ';
        
    }
        
    }
    


    $display .='
        
         
         ';
         
         if($yess!=1) {
             
             
             if($this->TYPE_ENTITY=="SMME") {
                 
                 if (empty($checkresponse)){
              
               switch($this->TYPE_ENTITY){
            case "SMME"://smme
                $display .= '<button style="margin:auto; page-break-inside: avoid" type="Submit" class="btn btn-success" name="SMME_RESPONSE_CREATE">Submit</button>';
                break;
            case  "COMPANY": //company
                $display .= '<button style="margin:auto" type="Submit" class="btn btn-success" name="COMPANY_RESPONSE_CREATE">Submit</button>';
            break;
            case "ADMIN"://adimin
                $display .= '<button style="margin-left:0px !important; page-break-inside: avoid" type="Submit" class="btn btn-primary" name="ADMIN_RESPONSE_CREATE">Submit</button>';
            break;
        }
              
              
        }
                 
                 
        }
        
        if($this->TYPE_ENTITY=="ADMIN") {
            
              switch($this->TYPE_ENTITY){
            case "SMME"://smme
                $display .= '<button style="margin:auto; page-break-inside: avoid" type="Submit" class="btn btn-success" name="SMME_RESPONSE_CREATE">Submit</button>';
                break;
            case  "COMPANY": //company
                $display .= '<button style="margin:auto" type="Submit" class="btn btn-success" name="COMPANY_RESPONSE_CREATE">Submit</button>';
            break;
            case "ADMIN"://adimin
                $display .= '<button style="margin-left:0px !important; page-break-inside: avoid" type="Submit" class="btn btn-primary" name="ADMIN_RESPONSE_CREATE">Submit</button>';
            break;
        }
              
              
        
            
            
        }
             
            
             
         }else {
             
         }
          
                 
       
        $display .= '</form>';
         $display .='
       </div>
<hr>
       ';
    return $display;
    
    
}


 public function SHOWFILES_NEEDED($id,$post_id, $yess){
     
    $criteria = $this->fetchScorecardCriteriaID($id);
    $display .= $this->filesformDisplay($criteria, $post_id, $yess);
        echo $display;
    }



    
private function responseWizard($criteria, $post_id,$checkresponse, $yess, $email){

$display .= $this->responseView($criteria, $post_id, $checkresponse, $yess, $email);
$display .= $this->filesformDisplay($criteria, $post_id, $yess);
  // Include both sets of information in one tab

echo $display;

    }
    private function fetchScorecardCriteriaID($id){
        $sql = "SELECT * FROM scorecard_criteria WHERE SCORECARD_ID =?";
        $types = "i";
        $params = array($id);
        $table = "yasccoza_openlink_market";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function fetchCriteriaSum($id){
        $sql = "SELECT SUM(s.Weighting) as Sum FROM yasccoza_openlink_market.scorecard_criteria s WHERE s.SCORECARD_ID =?";
        $types = "i";
        $params = array($id);
        $table = "yasccoza_openlink_market";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }

    public function displayCriteria(){
        $this->InitialiseCriteria();
        $this->CRITERIA_VIEW();
    }
    public function displayClient(){
        $result = $this->fetchClients();
        $this->clientsDisplay($result);
    }
    
    public function displayJobOrder($id,$q,$noz){

      
        if($q > 1000000){
        
            $result =$this->fetchJobOrderSMME($id);

        }else{
            $result = $this->fetchJobOrderInfo($id);
            
        }
        $files = $this->fetchJobOrderFiles($result[0]['POST_ID']);
        // print_r($result);
        $this->displayJobOrderView($result, $files,$noz);
    }
    
    
     public function displayJobOrderSMME($id,$q, $noz){

      
        if($q > 1000000){
        
            $result =$this->fetchJobOrderSMME($id);

        }else{
            $result = $this->fetchJobOrderInfo($id);
            
        }
        $files = $this->fetchJobOrderFiles($result[0]['POST_ID']);
        // print_r($result);
        $this->displayJobOrderView($result, $files, $noz);
    }
    
    
    
    private function worktypeVerifyView($result){
        $display = '<tr><td>Work Types: </td><td>';
        $entity =0;
        switch($this->TYPE_ENTITY){
            case "ADMIN":
                $entity = 3;
                break;
            case "SMME":
                $entity = 1;
                break;
        }
        for($i = 0; $i < count($result); $i++){
            $worktype = $this->fetchWorkType();
            if(!empty($worktype)){
                if($i == count($result)-1){
                    $display .='<a href="worktype_information.php?t='.$entity.'&w='.$worktype[$i]["id"].'">'.$worktype[$i]["task_name"].'</a>';
                }else{
                    $display .='<a href="worktype_information.php?t='.$entity.'&w='.$worktype[$i]["id"].'">'.$worktype[$i]["task_name"].'</a>, ';
                }
            }
            
        }
        $display .= '</td></tr>';
        return $display;
    }
    private function fetchPosts($period,$id,$type){
        $sql = "";
        switch($this->TYPE_ENTITY){
            case "SMME":
                $sql = "SELECT
                    m.POST_ID, m.Title, m.Description, m.SCORECARD_ID, m.APPROVED, m.USER_ID, m.EXPIRY, m.Created, m.Start_Date, m.EXPIRY, m.WORKTYPE, r.Legal_name, tms.status
                FROM
                    market_post m
                    INNER JOIN yasccoza_openlink_smmes.register r ON m.CLIENT_ID = r.SMME_ID
                    INNER JOIN yasccoza_tms_db.working_week_periods w ON DATE_FORMAT(m.Created, '%Y-%m-%d') BETWEEN w.start_week AND w.end_week AND w.period = ?
                    INNER JOIN yasccoza_tms_db.project_list tms ON m.POST_ID = tms.id
                    WHERE m.COMPANY=0
                UNION
                SELECT
                    m.POST_ID, m.Title, m.Description, m.SCORECARD_ID, m.APPROVED, m.USER_ID, m.EXPIRY, m.Created, m.Start_Date, m.EXPIRY, m.WORKTYPE, r.company_name AS Legal_name,  tms.status
                FROM
                    market_post m
                    INNER JOIN yasccoza_openlink_market.client r ON m.CLIENT_ID = r.CLIENT_ID
                    INNER JOIN yasccoza_tms_db.working_week_periods w ON DATE_FORMAT(m.Created, '%Y-%m-%d') BETWEEN w.start_week AND w.end_week AND w.period = ?
                    INNER JOIN yasccoza_tms_db.project_list tms ON m.POST_ID = tms.id
                   WHERE m.COMPANY=0
                ORDER BY Created ASC;
                ";
                break;
            case "COMPANY":
                $sql = "SELECT
                m.Title, m.Description, m.SCORECARD_ID,m.APPROVED, m.USER_ID, m.EXPIRY, m.Created,m.POST_ID,m.Start_Date,m.EXPIRY,m.WORKTYPE, r.Legal_name
                FROM
                    market_post m, yasccoza_openlink_smmes.register r,yasccoza_tms_db.working_week_periods w, yasccoza_openlink_tms_db.project_list tms
                    WHERE m.CLIENT_ID = r.SMME_ID
                	AND DATE_FORMAT(m.Created, '%Y-%m-%d') BETWEEN w.start_week AND w.end_week
                    AND w.period = ?
                    AND tms.id = m.POST_ID
                UNION
                SELECT
                    m.Title, m.Description, m.SCORECARD_ID,m.APPROVED, m.USER_ID, m.EXPIRY, m.Created,m.POST_ID,m.Start_Date,m.EXPIRY,m.WORKTYPE, r.company_name AS Legal_name
                 FROM
                    market_post m, yasccoza_openlink_market.client r,yasccoza_tms_db.working_week_periods w
                    WHERE m.CLIENT_ID = r.CLIENT_ID
                	AND DATE_FORMAT(m.Created, '%Y-%m-%d') BETWEEN w.start_week AND w.end_week
                     AND w.period = ?
                ORDER BY Created desc;";
                break;
            case "ADMIN":
                if($type==2){
                     $sql = "SELECT
    m.Title,
    m.Description,
    m.SCORECARD_ID,
    m.APPROVED,
    m.USER_ID,
    m.EXPIRY,
    m.Created,
    m.POST_ID,
    m.Start_Date,
    m.WORKTYPE,
    r.company_name AS Legal_name,
    tms.status
FROM yasccoza_openlink_market.market_post AS m
LEFT JOIN yasccoza_openlink_market.client AS r
    ON m.CLIENT_ID = r.CLIENT_ID
LEFT JOIN yasccoza_tms_db.project_list AS tms
    ON tms.id = m.POST_ID
WHERE
    YEARWEEK(m.Created, 1) = ?
    AND m.COMPANY = 0
    AND m.ASSIGNED_TO = $id
ORDER BY
    m.Created ASC;";
                }elseif($type==3){
$sql = "SELECT
    m.Title,
    m.Description,
    m.SCORECARD_ID,
    m.APPROVED,
    m.USER_ID,
    m.EXPIRY,
    m.Created,
    m.POST_ID,
    m.Start_Date,
    m.WORKTYPE,
    r.company_name AS Legal_name,
    tms.status
FROM yasccoza_openlink_market.market_post AS m
LEFT JOIN yasccoza_openlink_market.client AS r
    ON m.CLIENT_ID = r.CLIENT_ID
LEFT JOIN yasccoza_tms_db.project_list AS tms
    ON tms.id = m.POST_ID
WHERE
    YEARWEEK(m.Created, 1) = ?
    AND m.COMPANY = 0
    AND m.ASSIGNED_TO = (
        SELECT creator_id
        FROM yasccoza_tms_db.users
        WHERE id = $id
    )
ORDER BY
    m.Created ASC;";
                }else{
                    
        $sql = "SELECT
    m.Title,
    m.Description,
    m.SCORECARD_ID,
    m.APPROVED,
    m.USER_ID,
    m.EXPIRY,
    m.Created,
    m.POST_ID,
    m.Start_Date,
    m.WORKTYPE,
    r.company_name AS Legal_name,
    tms.status
FROM yasccoza_openlink_market.market_post AS m
LEFT JOIN yasccoza_openlink_market.client AS r
    ON m.CLIENT_ID = r.CLIENT_ID
LEFT JOIN yasccoza_tms_db.project_list AS tms
    ON tms.id = m.POST_ID
WHERE
    YEARWEEK(m.Created, 1) = ?
    AND m.COMPANY = 0
ORDER BY
    m.Created ASC;";
                }
                
                break;
        }
        $params = array($period);
        $types="i";
        $table = "yasccoza_openlink_market";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    
    
    private function fetchmyInduPosts($period){
        $sql = "SELECT
                    m.POST_ID, m.Title, m.Description, m.SCORECARD_ID, m.APPROVED, m.USER_ID, m.EXPIRY, m.Created, m.Start_Date, m.EXPIRY, m.WORKTYPE, r.Legal_name, tms.status
                FROM
                      yasccoza_openlink_market.market_post m
                    INNER JOIN yasccoza_openlink_smmes.register r ON m.CLIENT_ID = r.SMME_ID
                    INNER JOIN yasccoza_tms_db.working_week_periods w ON DATE_FORMAT(m.Created, '%Y-%m-%d') BETWEEN w.start_week AND w.end_week AND w.period = ?
                    INNER JOIN yasccoza_tms_db.project_list tms ON m.POST_ID = tms.id
                    JOIN yasccoza_openlink_smmes.register rr ON rr.SMME_ID = ?
                    WHERE m.COMPANY=0
                    AND rr.OFFICE_ID = m.OFFICE_ID
                UNION
                SELECT
                    m.POST_ID, m.Title, m.Description, m.SCORECARD_ID, m.APPROVED, m.USER_ID, m.EXPIRY, m.Created, m.Start_Date, m.EXPIRY, m.WORKTYPE, r.company_name AS Legal_name,  tms.status
                FROM
                    yasccoza_openlink_market.market_post m
                    INNER JOIN yasccoza_openlink_market.client r ON m.CLIENT_ID = r.CLIENT_ID
                    INNER JOIN yasccoza_tms_db.working_week_periods w ON DATE_FORMAT(m.Created, '%Y-%m-%d') BETWEEN w.start_week AND w.end_week AND w.period = ?
                    INNER JOIN yasccoza_tms_db.project_list tms ON m.POST_ID = tms.id
                       JOIN yasccoza_openlink_smmes.register rr ON rr.SMME_ID = ?
                        WHERE m.COMPANY=0
                        AND rr.OFFICE_ID = m.OFFICE_ID
                ORDER BY Created ASC;";
      
        $params = array($period, $this->ID, $period, $this->ID);
        $types="iiii";
        $table = "yasccoza_openlink_market";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    
    
 private function wwwe() {
    // Combined query to fetch POST_IDs from both tables
    // $sql = "SELECT mp.POST_ID FROM yasccoza_openlink_market.market_post mp WHERE mp.COMPANY = ?
    //         UNION
    //         SELECT js.POST_ID FROM yasccoza_openlink_market.job_and_smmes js WHERE js.SMME_ID = ?";
    // $params = [$this->ID, $this->ID];
    // $result = $this->fetch('yasccoza_openlink_market', $sql, 'ii', $params);
    
    // print_r($result);

    // // Check and extract POST_IDs
    // if (!$result || empty($result)) {
    //     echo "No POST_IDs found"; // Adding an echo statement before return for debugging
    //     return []; // Return empty if no POST_IDs found
    // }

    // $post_ids = array_column($result, 'POST_ID');
    // if (empty($post_ids)) {
    //     return []; // Return empty array if no valid POST_IDs
    // }

    // // Prepare placeholders for IN clause in SQL query
    // $placeholders = implode(',', array_fill(0, count($post_ids), '?'));

    // SQL query to get detailed information using dynamic placeholders
    $sql1 = "SELECT 
    m.POST_ID, 
    m.Title, 
    m.Description, 
    m.SCORECARD_ID, 
    m.APPROVED, 
    m.USER_ID, 
    m.EXPIRY, 
    m.Start_Date, 
    m.WORKTYPE, 
    tms.status, 
    COALESCE(CONCAT(u.firstname, ' ', u.lastname), CONCAT(u2.firstname, ' ', u2.lastname)) AS FullName,
    COALESCE(js.Date_Sent, m.Created) AS Date
FROM 
    yasccoza_openlink_market.market_post m
LEFT JOIN 
    yasccoza_tms_db.project_list tms ON m.POST_ID = tms.id
LEFT JOIN 
    yasccoza_openlink_market.job_and_smmes js ON m.POST_ID = js.post_id
LEFT JOIN 
    yasccoza_tms_db.users u ON m.USER_ID = u.id
LEFT JOIN 
    yasccoza_tms_db.users u2 ON js.Who_Sent = u2.id
WHERE
    js.SMME_ID = ?
    OR m.COMPANY = ?;
";

    // Prepare types and parameters for query execution
    $types1 = 'ii'; // Correct type string for all integers including IN clause and two additional parameters
    $params1 = array_merge([$this->ID, $this->ID]);

    // Execute the query to get detailed information
    $results = $this->master->selectalot($sql1, $types1, $params1);
    return $results;
}




  private function fetchmyJobPeriods(){
        //fetch all the periods left join count of jobs in period
        $sql = "SELECT
    wwp.period,
    wwp.start_week,
    wwp.end_week,
    MONTH(wwp.start_week) AS Month_Created,
    SUM(CASE WHEN mp.APPROVED = 1 THEN 1 ELSE 0 END) AS Jobs
FROM
    yasccoza_tms_db.working_week_periods wwp
JOIN
    yasccoza_openlink_smmes.register r ON r.SMME_ID = ?
LEFT JOIN
    yasccoza_openlink_market.market_post mp 
    ON DATE_FORMAT(mp.Created, '%Y-%m-%d') BETWEEN wwp.start_week AND wwp.end_week
    AND mp.COMPANY = 0
    AND r.OFFICE_ID = mp.OFFICE_ID
GROUP BY
    wwp.start_week,
    wwp.end_week,
    wwp.period
ORDER BY
    wwp.start_week ASC,
    wwp.end_week ASC,
    wwp.period ASC;


    ";
    
     $params = array($this->ID);
    $types="i";
    $table = "yasccoza_openlink_market";
     $result = $this->fetch($table, $sql, $types, $params);
    return $result;
    }


    
    
    
    private function fetchJobPeriods($id,$type){
        
if($type==2){
    
     $sql = "WITH RECURSIVE weeks AS (
    -- first Monday for current year
    SELECT 
        DATE_SUB(DATE(CONCAT(YEAR(CURDATE()), '-01-01')), INTERVAL WEEKDAY(DATE(CONCAT(YEAR(CURDATE()), '-01-01'))) DAY) AS start_week
    UNION ALL
    SELECT DATE_ADD(start_week, INTERVAL 7 DAY)
    FROM weeks
    WHERE DATE_ADD(start_week, INTERVAL 7 DAY) <= DATE(CONCAT(YEAR(CURDATE()), '-12-31'))
)
SELECT
    YEARWEEK(w.start_week, 1) AS period,
    w.start_week,
    DATE_ADD(w.start_week, INTERVAL 4 DAY) AS end_week,
    MONTH(w.start_week) AS Month_Created,
    COALESCE(SUM(CASE WHEN mp.APPROVED = 1 THEN 1 ELSE 0 END), 0) AS Jobs
FROM weeks w
LEFT JOIN yasccoza_openlink_market.market_post mp
    ON mp.Created >= w.start_week
    AND mp.Created < DATE_ADD(w.start_week, INTERVAL 5 DAY)   -- Monday to Saturday 00:00 (covers Mon–Fri)
    AND mp.COMPANY = 0
    AND mp.ASSIGNED_TO = $id
GROUP BY
    w.start_week
ORDER BY
    w.start_week ASC;";
    
    
}elseif($type==3){
    
     $sql = "WITH RECURSIVE weeks AS (
    SELECT 
        DATE_SUB(DATE(CONCAT(YEAR(CURDATE()), '-01-01')),
                 INTERVAL WEEKDAY(DATE(CONCAT(YEAR(CURDATE()), '-01-01'))) DAY) AS start_week
    UNION ALL
    SELECT DATE_ADD(start_week, INTERVAL 7 DAY)
    FROM weeks
    WHERE DATE_ADD(start_week, INTERVAL 7 DAY) <= DATE(CONCAT(YEAR(CURDATE()), '-12-31'))
)
SELECT
    YEARWEEK(w.start_week, 1) AS period,
    w.start_week,
    DATE_ADD(w.start_week, INTERVAL 4 DAY) AS end_week,
    MONTH(w.start_week) AS Month_Created,
    COALESCE(SUM(CASE WHEN mp.APPROVED = 1 THEN 1 ELSE 0 END), 0) AS Jobs
FROM weeks w
LEFT JOIN yasccoza_openlink_market.market_post mp
    ON mp.Created >= w.start_week
    AND mp.Created < DATE_ADD(w.start_week, INTERVAL 5 DAY)   -- Mon–Fri
    AND mp.ASSIGNED_TO = (
        SELECT creator_id
        FROM yasccoza_tms_db.users
        WHERE id = $id
    )
GROUP BY
    w.start_week
ORDER BY
    w.start_week ASC";
    
}else{
    
     $sql = "WITH RECURSIVE weeks AS (
    -- First Monday for the current year
    SELECT 
        DATE_SUB(
            DATE(CONCAT(YEAR(CURDATE()), '-01-01')),
            INTERVAL WEEKDAY(DATE(CONCAT(YEAR(CURDATE()), '-01-01'))) DAY
        ) AS start_week
    UNION ALL
    -- Next Mondays
    SELECT DATE_ADD(start_week, INTERVAL 7 DAY)
    FROM weeks
    WHERE DATE_ADD(start_week, INTERVAL 7 DAY) <= DATE(CONCAT(YEAR(CURDATE()), '-12-31'))
)
SELECT
    YEARWEEK(w.start_week, 1) AS period,
    w.start_week,
    DATE_ADD(w.start_week, INTERVAL 4 DAY) AS end_week,
    MONTH(w.start_week) AS Month_Created,
    COALESCE(SUM(CASE WHEN mp.APPROVED = 1 THEN 1 ELSE 0 END), 0) AS Jobs
FROM weeks w
LEFT JOIN yasccoza_openlink_market.market_post mp
    ON mp.Created >= w.start_week
    AND mp.Created < DATE_ADD(w.start_week, INTERVAL 5 DAY)  -- Mon–Fri window
    AND mp.COMPANY = 0
GROUP BY
    w.start_week
ORDER BY
    w.start_week ASC;";
    
}
        $table = "yasccoza_openlink_market";
        $result = $this->fetchNoParms($sql, $table);
        return $result;
    }
    
  private function displayJobPeriods($jobPeriods){
    
    $month_jobs=0;
    $months = array("Janurary", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    $display = '<div style="background-color:white !important">'; 
    $start = 0;
    $looper = 0;
    
    // =========== ðŸš€ NEW DATA FIX ===========
    // This loop corrects the month for Period 1
    // before your main logic runs.
    for ($k = 0; $k < count($jobPeriods); $k++) {
        // If Period is 1 AND its month is 12 (December)
        if (isset($jobPeriods[$k]['period']) && $jobPeriods[$k]['period'] == 1 && $jobPeriods[$k]['Month_Created'] == 12) {
            // Manually re-assign it to Month 1 (January)
            $jobPeriods[$k]['Month_Created'] = 1;
            
            // We only need to do this once, so we can stop
            break; 
        }
    }
    // ========= END OF NEW DATA FIX =========

    $weeks_in_months = array();
   for ($i=0; $i < count($jobPeriods); $i++) { 
    # code...
        array_push($weeks_in_months, $jobPeriods[$i]["Month_Created"]);
   }
   $actual_counts = array_count_values($weeks_in_months);
   //print_r($actual_counts);
    for ($i=0; $i < 12; $i++) { 
        # code...
        // if($i !=0){
        //     $start = $start + 4;
        // }
        $total = 0;

        // This check prevents an error if a month has no periods
        $period_count_for_month = isset($actual_counts[$i+1]) ? $actual_counts[$i+1] : 0; // <-- MODIFIED

        for($j = 0; $j < $period_count_for_month; $j++){ // <-- MODIFIED
            
            // This check prevents an error if $jobPeriods[$start] doesn't exist
            if(isset($jobPeriods[$start])) { // <-- MODIFIED
                $total += $jobPeriods[$start]['Jobs'];
                $start++;
            }
        }
        // $total = $jobPeriods[$start]['Jobs'] +$jobPeriods[$start+1]['Jobs']+$jobPeriods[$start+2]['Jobs']+$jobPeriods[$start+3]['Jobs'];
            //light blue -> 0dc0ff
            //navy -> 172D44
        $display .= ' <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
<div class="pricing">
    <div class="title">
        <span>'.($months[$i]).'</span> <br><br>
        <h2> total Jobs: '.$total.'</h2>
    </div>
    <div class="x_content" >
        <div>
            <div class="pricing_features">
                <br><br>
                <p>Periods</p>
                <br>
                <div class="row">
                    
                    <ul class="list-unstyled periods-container">';
                    
                    // Use the same safe count
                    for($x = 0; $x < $period_count_for_month; $x++) { // <-- MODIFIED
                        
                        // This check prevents an error if $jobPeriods[$looper] doesn't exist
                        if(isset($jobPeriods[$looper])) { // <-- MODIFIED

                            // =======================================================
                            // ðŸŽ¨ NOTE: I removed your inline styles (width, height,
                            // font-size) from the <li> and <a> tags. 
                            // Those styles were overriding the CSS and causing 
                            // the "squashed" layout. The 'period-item' class
                            // (from our previous CSS) styles this correctly.
                            // =======================================================
                            
                            $display .= '<li style="width:45px;height:40px;background-color:#007bff; color:white">
                                <a href="period_posts.php?p='.$jobPeriods[$looper]['period'].'" class="period-item" style="color:white;font-size:10px">
                                    <i class="fa fa-calendar icon" style="color:white;font-size:10px"></i>
                                    <span class="number">'.$jobPeriods[$looper]['Jobs'].'</span>
                                </a>
                            </li>';
                            
                            $looper++;
                        }
                    }
                    $display .= '</ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div>';

    
    }
    $display .= '</div>';
    echo $display;
}
    
    
    private function displaymyJobPeriods($jobPeriods){
        
        
        //get list of 52 periods and count of job
        //loop through array
        //for each 4 periods, conclude month count of all period values
        //echo a dive with the split display
        $month_jobs=0;
        $months = array("Janurary", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $display = '<div style="background-color:white !important">'; 
        $start = 0;
        $looper = 0;
        $weeks_in_months = array();
       for ($i=0; $i < count($jobPeriods); $i++) { 
        # code...
            array_push($weeks_in_months, $jobPeriods[$i]["Month_Created"]);
       }
       $actual_counts = array_count_values($weeks_in_months);
       //print_r($actual_counts);
        for ($i=0; $i < 12; $i++) { 
            # code...
            // if($i !=0){
            //     $start = $start + 4;
            // }
            $total = 0;
            for($j = 0; $j < $actual_counts[$i+1]; $j++){
                $total +=$jobPeriods[$start]['Jobs'];
                $start++;
            }
            // $total = $jobPeriods[$start]['Jobs'] +$jobPeriods[$start+1]['Jobs']+$jobPeriods[$start+2]['Jobs']+$jobPeriods[$start+3]['Jobs'];
                   //light blue -> 0dc0ff
                   //navy -> 172D44
            $display .= ' <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
    <div class="pricing">
        <div class="title">
            <span>'.($months[$i]).'</span> <br><br>
            <h2> total Jobs: '.$total.'</h2>
        </div>
        <div class="x_content" >
            <div>
                <div class="pricing_features">
                    <br><br>
                    <p>Periods</p>
                    <br>
                    <div class="row">
                        <ul class="list-unstyled">';
                        for($x = 0; $x < $actual_counts[$i+1]; $x++) {
                            $display .= '<li style="display: inline-block; margin: auto; width:35px">
                                <a href="period_posts.php?p='.$jobPeriods[$looper]['period'].'&my=1">
                                    <button type="button" class="btn btn-primary btn-period">
                                        <i class="fa fa-calendar"></i><br><br>'.$jobPeriods[$looper]['Jobs'].'
                                    </button>
                                </a>
                            </li>';
                            $looper++;
                        }
                        $display .= '</ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';

       
        }
        $display .= '</div>';
        echo $display;
    }
    
    private function fetchPostsToVerify(){

        $sql = "SELECT m.Title,m.Description, m.SCORECARD_ID,m.APPROVED, m.USER_ID, m.EXPIRY, m.Created,m.POST_ID,m.ASSIGNED_TO,m.VERIFIED_BY,m.Start_Date,m.EXPIRY,m.WORKTYPE, r.Legal_name as Legal_name
        FROM market_post m, yasccoza_openlink_smmes.register r
        WHERE m.CLIENT_ID = r.SMME_ID
        AND m.APPROVED = 0
        AND m.VERIFIED_BY = ?
        UNION
        SELECT m.Title,m.Description, m.SCORECARD_ID,m.APPROVED, m.USER_ID, m.EXPIRY, m.Created,m.POST_ID,m.ASSIGNED_TO,m.VERIFIED_BY,m.Start_Date,m.EXPIRY,m.WORKTYPE, r.company_name as Legal_name
        FROM market_post m, yasccoza_openlink_market.client r
        WHERE m.CLIENT_ID = r.CLIENT_ID
        AND m.APPROVED = 0
        AND m.VERIFIED_BY = ?
        ORDER BY Created asc";
        
        $table = "yasccoza_openlink_market";
        $params = array($this->ID, $this->ID);
        $types="ii";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function fetchPostsToVerify2(){

        $sql = "SELECT m.Title,m.Description, m.SCORECARD_ID,m.APPROVED, m.USER_ID, m.EXPIRY, m.Created,m.POST_ID,m.ASSIGNED_TO,m.VERIFIED_BY,m.Start_Date,m.EXPIRY,m.WORKTYPE, r.Legal_name as Legal_name
        FROM market_post m, yasccoza_openlink_smmes.register r
        WHERE m.CLIENT_ID = r.SMME_ID
        AND m.APPROVED = 0
        AND (m.VERIFIED_BY = ? OR m.VERIFIED_BY = ? OR m.VERIFIED_BY = ? )
        UNION
        SELECT m.Title,m.Description, m.SCORECARD_ID,m.APPROVED, m.USER_ID, m.EXPIRY, m.Created,m.POST_ID,m.ASSIGNED_TO,m.VERIFIED_BY,m.Start_Date,m.EXPIRY,m.WORKTYPE, r.company_name as Legal_name
        FROM market_post m, yasccoza_openlink_market.client r
        WHERE m.CLIENT_ID = r.CLIENT_ID
        AND m.APPROVED = 0
        AND (m.VERIFIED_BY = ? OR m.VERIFIED_BY = ? OR m.VERIFIED_BY = ? )
        ORDER BY Created asc";
        
        $table = "yasccoza_openlink_market";
        $params = array(0, 1, $this->ID, 0, 1,$this->ID);
        $types="iiiiii";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function fetchJobOrderFiles($id){
        $sql = "SELECT * FROM  yasccoza_openlink_market.rfp p WHERE p.POST_ID = ?";
        $table = "yasccoza_openlink_market";
        $params = array($id);
        $types="i";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    
     private function fetchJobOrderSMME($id){

        $sql = "SELECT 
    m.POST_ID, 
    m.Title, 
    p.RFP_ID,
    m.Description,
    m.USES_EXPENSES, 
    m.SCORECARD_ID, 
    m.USER_ID, 
    m.EXPIRY, 
    m.Created,
    m.VERIFIED_BY,
    m.ASSIGNED_TO,
    m.APPROVED,
    m.Start_Date,
    m.EXPIRY,
    m.WORKTYPE,
    m.JOB_TYPE, 
    r.Legal_name AS Legal_name,
    r.Contact AS REP_CONTAT, 
    r.Email AS REP_EMAIL, 
    p.url,
    s.Title AS SCORECARD_TITLE, 
    s.SCORECARD_ID, 
    it.title AS Industry, 
    i.office AS Office,  
    cr.first_name AS REP_NAME, 
    cr.Email AS REP_EMAIL
FROM 
    yasccoza_openlink_market.market_post m
LEFT JOIN 
    rfp p ON p.POST_ID = m.POST_ID AND p.USER_ID = m.USER_ID
LEFT JOIN 
    yasccoza_openlink_smmes.register r ON m.CLIENT_ID = r.SMME_ID
LEFT JOIN 
    yasccoza_openlink_market.scorecard s ON m.SCORECARD_ID = s.SCORECARD_ID
LEFT JOIN 
    yasccoza_openlink_association_db.industry_title it ON r.INDUSTRY_ID = it.TITLE_ID
LEFT JOIN 
    yasccoza_openlink_association_db.industry i ON it.INDUSTRY_ID = i.INDUSTRY_ID
LEFT JOIN 
    yasccoza_openlink_smmes.admin cr ON m.CLIENT_ID = cr.SMME_ID
WHERE 
    m.POST_ID = ?; ";
        
        
        $table = "yasccoza_openlink_market";
        $params = array($id);
        $types="i";
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;

    }
    
    private function fetchAllPostAndSmmes() {
    // SQL query to fetch all posts from the market_post table
    $sqlPosts = "SELECT * FROM yasccoza_openlink_market.market_post";
    $table = "yasccoza_openlink_market";
    $resultPosts = $this->fetchNoParms($sqlPosts, $table);

    // SQL query to fetch all SMEs from the register table
    $sqlSmmes = "SELECT * FROM yasccoza_openlink_smmes.register";
    $table2 = "yasccoza_openlink_smmes";
    $resultSmmes = $this->fetchNoParms($sqlSmmes, $table2);

    // Return both results as an associative array
    return [
        'posts' => $resultPosts,
        'smmes' => $resultSmmes
    ];
}
    
    
    
    
    private function fetchJobOrderInfo($id){
        

        $sql = "SELECT 
    m.POST_ID,
    m.Title, 
    p.RFP_ID,
    m.Description,
    m.USES_EXPENSES, 
    m.SCORECARD_ID, 
    m.USER_ID, 
    m.EXPIRY, 
    m.Created,
    m.POST_ID,
    m.VERIFIED_BY,
    m.ASSIGNED_TO,
    m.APPROVED,
    m.Start_Date,
    m.EXPIRY,
    m.WORKTYPE,
    m.JOB_TYPE, 
    r.company_name as Legal_name,
    r.Contact, 
    r.Email, 
    p.url,
    s.Title as SCORECARD_TITLE, 
    s.SCORECARD_ID, 
    it.title as Industry, 
    i.office as Office,  
    cr.REP_NAME, 
    cr.REP_EMAIL
FROM 
    market_post m
LEFT JOIN 
    yasccoza_openlink_market.client r ON m.CLIENT_ID = r.CLIENT_ID
LEFT JOIN 
    rfp p ON p.POST_ID = m.POST_ID AND p.USER_ID = m.USER_ID
LEFT JOIN 
    yasccoza_openlink_market.scorecard s ON m.SCORECARD_ID = s.SCORECARD_ID
LEFT JOIN 
    yasccoza_openlink_association_db.industry i ON r.office_id = i.INDUSTRY_ID
LEFT JOIN 
    yasccoza_openlink_association_db.industry_title it ON r.industry_id = it.TITLE_ID
LEFT JOIN 
    yasccoza_tms_db.client_rep cr ON m.CLIENT_REP = cr.REP_ID
WHERE 
    m.POST_ID = ?;
";
                
        $table = "yasccoza_openlink_market";
        $params = array($id);
        $types="i";
        $result = $this->fetch($table, $sql, $types, $params);
       
     
        return $result;
    }
    private function fetchWorkTypes(){
           $sql = "SELECT task_list.*, SUM(user_productivity.duration) AS durationz
        FROM yasccoza_tms_db.task_list
        JOIN yasccoza_tms_db.user_productivity ON task_list.id = user_productivity.task_id
        WHERE user_productivity.task_id IS NOT NULL
        GROUP BY user_productivity.task_id;
        ";
        
        $table = "yasccoza_tms_db";
        $result = $this->fetchNoParms($sql,$table);
        return $result;
    }
    private function fetchJobTypes(){
        $sql = "SELECT * FROM yasccoza_tms_db.job_type;";
        $table = "yasccoza_tms_db";
        $result = $this->fetchNoParms($sql,$table);
        return $result;
    }
    
     private function fetchOFFICES(){
        $sql = "SELECT * FROM yasccoza_openlink_association_db.industry;";
        $table = "yasccoza_openlink_association_db";
        $result = $this->fetchNoParms($sql,$table);
        return $result;
    }
    
    private function savePost($office,$title,$client,$client_rep,$description,$start,$end,$worktype,$jobOrderType, $scorecard, $assigned, $expense=null){
        $ids ="";
        for($i =0; $i < count($worktype); $i++){
            if($i ==0 || $i < count($worktype)-1) $ids .= $worktype[$i].",";
            else $ids .= $worktype[$i];
        }
        if($expense != null){
            $sql = "INSERT INTO `market_post`(OFFICE_ID, `Title`, CLIENT_ID,CLIENT_REP,`Description`, `SCORECARD_ID`, `USER_ID`, `Start_Date`, `EXPIRY`, `ASSIGNED_TO`,VERIFIED_BY, JOB_TYPE,`WORKTYPE`, USES_EXPENSES) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $types="isiisiissiisss";
            $params=array($office,$title,$client,$client_rep,$description,$scorecard, $this->ID, $start, $end,$assigned,$assigned,$jobOrderType, $ids,$expense);
        }else{
            $sql = "INSERT INTO `market_post`( OFFICE_ID,`Title`, CLIENT_ID,CLIENT_REP,`Description`, `SCORECARD_ID`, `USER_ID`, `Start_Date`, `EXPIRY`, `ASSIGNED_TO`,VERIFIED_BY,JOB_TYPE, `WORKTYPE`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $types="isiisiissiiss";
            $params=array($office,$title,$client,$client_rep,$description,$scorecard, $this->ID, $start, $end,$assigned,$assigned,$jobOrderType, $ids);
        }
        $table = "market_post";
        $query = $this->save($sql, $types, $params, $table);
        $criteria = $this->fetchLastPost();
        return $criteria[0]["POST_ID"];
    }
    private function saveJob_tms($office,$id,$title,$description,$start,$end,$scorecard,$worktype,$admin_id, $client_id,$client_rep, $jobType,$created, $updated){
        
        $result = $this->fetchCardById($scorecard);
        $sql = "INSERT INTO yasccoza_tms_db.project_list (OFFICE_ID,id,name, scorecard, description,status, start_date, end_date, manager_id, task_ids,CLIENT_ID, CLIENT_REP,JOB_TYPE, Date_Post_Created,Date_Post_Verified) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $types="iissssssisiisss";
        $params=array($office,$id,$title,$scorecard,$description,"In-progress",$start, $end,$admin_id,$worktype,$client_id,$client_rep, $jobType,$created,$updated);
        $table = "yasccoza_tms_db.project_list";
        $query = $this->save($sql, $types, $params, $table);

        
    }
    private function fetchSectorAdmin($ID){
        $sql = "SELECT DISTINCT
        COALESCE(oa.admin_id, oa_office.admin_id, 0) AS Result
        FROM yasccoza_openlink_market.client omc
        LEFT JOIN yasccoza_openlink_admin_db.admin_sector oa ON oa.INDUSTRY_ID = omc.industry_id
        LEFT JOIN yasccoza_openlink_admin_db.admin_sector oa_office ON oa_office.OFFICE_ID = omc.office_id
        LEFT JOIN yasccoza_tms_db.users u ON oa_office.ADMIN_ID = u.id
        WHERE omc.CLIENT_ID =?
        AND u.type=2
        ;";
        $types = "i";
        $table = "yasccoza_openlink_market";
        $result = $this->fetch($table,$sql,$types,array($ID));
        return $result; 
    }
    private function fetchPostDistributor(){
        $sql = "SELECT * FROM yasccoza_openlink_admin_db.signup WHERE `Role`='Default Post Approver';";
        $table = "yasccoza_openlink_market";
        $result = $this->fetchNoParms($sql,$table);
        return $result;
    }
    private function fetchLastPost(){
        $sql = "SELECT * FROM yasccoza_openlink_market.market_post m WHERE m.USER_ID=? ORDER BY POST_ID desc;";
        $types = "i";
        $params = array($this->ID);
        $table = "yasccoza_openlink_market";
        $result = $this->fetch($table,$sql,$types,$params);
        return $result;
    }
   
    private function saveRFQ($company,$url,$POST){
        
        $sql = "INSERT INTO yasccoza_openlink_market.rfp(url, USER_ID, POST_ID, COMPANY) VALUES (?,?,?,?)";
        $types="siis";
        $params=array($url,$this->ID,$POST,$company);
        $table = "yasccoza_openlink_market.rfp";
        $query = $this->save($sql, $types, $params, $table);
        $id = $this->master->getLastID();
        return $id;
    }
    
 private function saveRFQADMIN($company,$url,$POST){
        
        $sql = "INSERT INTO yasccoza_openlink_market.rfp(url, USER_ID, COMPANY,POST_ID) VALUES (?,?,?,?)";
        $types="sisi";
        $params=array($url,$this->ID,$company,$POST);
        $table = "yasccoza_openlink_market.rfp";
        $query = $this->save($sql, $types, $params, $table);
        $id = $this->master->getLastID();
        return $id;
    }
    
    private function insertPostAdmins($post_id, $admins){
        for ($i=0; $i < count($admins); $i++) { 
            # code...
            $this->insertAdmin($post_id, $admins[$i]['Result']);
        }
       
    }
    private function insertAdmin($post_id, $admin_id){
        $sql = "INSERT INTO yasccoza_openlink_market.post_admins(ADMIN_ID, POST_ID) VALUES (?,?)";
        $types="ii";
        $params=array($admin_id,$post_id);
        $table = "yasccoza_openlink_market.post_admins";
        $query = $this->save($sql, $types, $params, $table);
        $id = $this->master->getLastID();
        return $id;
    }
    private function SCORECARD_VIEW(){
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        if(empty($this->scorecards)){
            echo '<h3 class="text-capitalize text-center"> No score cards yet</h3>';
        }
        else{
        for($i = 0; $i < count($this->scorecards); $i++){
            echo '<a href="../'.$this->TYPE_ENTITY.'/scorecard_finalview2.php?t='.$entity.'&w='.$this->scorecards[$i]->ID.'">
            <div class="col-xl-3 col-lg-4 col-md-4 col-sm-4 col-12" style="height:600px">
            <div class="pricing" >
                <div class="title" style="background-color: #032033; padding-top:1px">
                    <h1>'.($this->scorecards[$i]->title).'</h1>
                </div>
                
                <div class="x_content">
                    <div class="">
                        <div class="pricing_features" style="background-color: #efefef">
                            <ul class="list-unstyled text-left" style="font-size: 15px ! important;" >
                                <li>
                                    Title <strong>: '.$this->scorecards[$i]->title.'</strong>
                                  
                                </li>
                               <hr style="background-color:lightgrey !Important; height:2px">
                                <li>
                                    Description <strong>:'.$this->scorecards[$i]->description.'</strong>
                                </li>
                                <hr style="background-color:lightgrey !Important; height:2px">
                                <li>
                                    Date of Expiriy <strong>: '.$this->scorecards[$i]->date.'</strong>
                                </li>
                               <hr style="background-color:lightgrey !Important; height:2px">
                                <li>
                                    Criteria <strong>: '.$this->scorecards[$i]->getCriteria().'</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                </div>
            </div>
            </div>
            </a>';
            }
        }
        
    }
    private function CRITERIA_VIEW(){
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        if(empty($this->criteria)){
            echo '<h3 class="text-center text-capitalize">No criteria yet</h3>';
        }else{
            for($i = 0; $i < count($this->criteria); $i++){
            echo '<a href="../'.$this->TYPE_ENTITY.'/criteria_information.php?t='.$entity.'&w='.$this->criteria[$i]->ID.'">
            <div class="col-xl-3 col-lg-4 col-md-4 col-sm-4 col-12" style="height:480px">
        <div class="pricing">
            <div class="title" style="background-color: #032033;">
                <h2>'.$this->criteria[$i]->type.'</h2>
             
            </div>
            
            <div class="x_content">
                <div class="">
                    <div class="pricing_features" style="background-color: #efefef">
                        <ul class="list-unstyled text-left" style="font-size: 15px ! important;" >
                            <li>
                                Name <strong>: '.$this->criteria[$i]->type.'</strong>
                            </li>
                             <hr style="background-color:lightgrey !Important; height:2px">
                            <li>
                                Description <strong>:'.$this->criteria[$i]->description.'</strong>
                            </li>
                             <hr style="background-color:lightgrey !Important; height:2px">
                            <li>
                                Questions <strong>: '.$this->criteria[$i]->questions().'</strong>
                            </li>
                        </ul>
                    </div>
                </div>
                
            </div>
        </div>
        </div></a>';
        }
        }
        
        
    }
    private function Worktype_VIEW($result){
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        for($i = 0; $i < count($result); $i++){
            echo '<a href="../'.$this->TYPE_ENTITY.'/worktype_information.php?t='.$entity.'&w='.$result[$i]['id'].'"><div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
        <div class="pricing">
            <div class="title" style="background-color: #032033;">
                <h2>Work Type '.($i+1).'</h2>
             
            </div>
            
            <div class="x_content">
                <div class="">
                    <div class="pricing_features" style="background-color: #efefef">
                        <ul class="list-unstyled text-left" style="font-size: 15px ! important;" >
                            <li class="text-capitalize">
                                Name <strong>: '.$result[$i]['task_name'].'</strong>
                            </li>
                           <hr style="background-color:lightgrey !Important; height:2px">
                            <li>
                                Resources (# of workers)<strong>: '.$result[$i]['resources'].'</strong>
                            </li>
                            <hr style="background-color:lightgrey !Important; height:2px">
                           <li>
                                Duration (In Days)<strong>: '.$result[$i]['durationz'].'</strong>
                            </li>
                            <hr style="background-color:lightgrey !Important; height:2px">
                            <li>
                                Price <strong>: R'.$result[$i]['price'].'</strong>
                            </li>
                            
                        </ul>
                    </div>
                </div>
                
            </div>
        </div>
        </div></a>';
        // <li>
        //                         Youtube Link <strong>: <a href="'.$result[$i]['video_link'].'">'.$result[$i]['video_link'].'</a></strong>
        //                     </li>
        }
        
    }
    private function singleWorkTypeView($result){
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        $display = '<table class="table"><tbody>
        <tr><td>Name:  </td><td>'.$result[0]['task_name'].'</td></tr>
        <tr><td>Description:  </td><td>'.html_entity_decode($result[0]['description']).'</td></tr>
        <tr><td>Customer Benefits:  </td><td>'.$result[0]['customer_benefits'].'</td></tr>
        <tr><td>Resources:  </td><td>'.$result[0]['resources'].'</td></tr>
        <tr><td>Duration (In days):  </td><td>'.$result[0]['durationz'].'</td></tr>
        <tr><td>Price:  </td><td>'.$result[0]['price'].'</td></tr>
        <tr><td>Youtube Link:</td><td><a href="'.$result[0]['video_link'].'">'.$result[0]['video_link'].'</a></td></tr>
        <tr><td>Instructions:</td><td>'.html_entity_decode($result[0]['instructions']).'</td></tr>
        <tr><td>File:</td>';
    
    if (!empty($result[0]["file_path"])) {
        $display .= '<td><a style="padding:3.5px;" href="../../TMS/work_type_docs/'.$result[0]["file_path"].'" download="../../TMS/work_type_docs/'.$result[0]['file_path'].'">
            <img src="../Images/PDF_file_icon.png" height=50 width=50></a></td>';
    } else {
        $display .= '<td>No file available</td>';
    }
    
    $display .= '</tr></tbody></table>';
    echo $display;
    }
    public function displayLastScoreCard(){
        $results =$this->fetchScoreCard($this->ID);
        $scorecard = new SCORECARD($results[0]['Title'],$results[0]['Other'],$results[0]['Date_of_Expiry'],$results[0]['SCORECARD_ID']);
        $display = $scorecard->getSimpleDisplay();
        echo $display;
    }
    public function displayLastCriteria(){
        $results =$this->fetchCriteria($this->ID);
        $results2 = $this->fetchScorecardCriteria();
        if(empty($results2)){
            $criteria = new CRITERIA($results[0]['Description'],$results[0]['Name'],0); 
        }else{
            $criteria = new CRITERIA($results[0]['Description'],$results[0]['Name'],$results2[0]['SCORECARD_ID']);
        }
        $display = $criteria->getSimpleDisplay();
        echo $display;
    }
    public function displayLastCriteriaQuestions(){
        $results =$this->fetchCriteria($this->ID);
        $results2 = $this->getAllQuestions($results[0]['CRITERIA_ID']);
        $display = $this->questionDisplay($results2);
        echo $display;
    }
    public function displayJobOrderExpenses($id){
        $results =$this->fetchOrderExpenses($id);
        $this->displayOrderExpensesView($results);
        
    }
    private function fetchOrderExpenses($id){
        $sql = "SELECT * FROM yasccoza_openlink_smmes.expense_summary m, yasccoza_openlink_market.job_order_expense j WHERE j.EXPENSE_ID = m.EXPENSE_NUMBER AND j.POST_ID = ?";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function displayOrderExpensesView($result){
      
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        $display = '<table class="table table-bordered table-striped">
        <thead>
        <th>Product Name</th>
        <th>Product Specification</th>
        <th>Frequency</th>
        </thead>
        <tbody>
        ';
        for($i =0; $i < count($result); $i++){
            $display .= '<tr>
            <td>'.$result[0]['product_name'].'</td>
            <td>'.$result[0]['product_specification'].'</td>
            <td>'.$result[0]['frequency'].'</td>
            </tr>';
        }

        $display .='</tbody></table>';
        
        echo $display;
    }
    private function displayJobOrderView($result, $files, $noz){
        
       
      
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
          // Convert the timestamp to a Unix timestamp
          $startTime = strtotime($result[0]['Start_Date']);
          $endTime = strtotime($result[0]['EXPIRY']);

          // Format the date in the desired format
          $formattedStartDate = date("j F Y", $startTime);
          $formattedEndDate = date("j F Y", $endTime);
        $display = '<table class="table table-bordered table-striped"><tbody>
        <tr><td>Job ID:  </td><td style="color:red">'.$result[0]['POST_ID'].'</td></tr>
        <tr><td>Client:  </td><td>'.$result[0]['Legal_name'].'</td></tr>
        <tr><td>Client Sector:  </td><td>'.$result[0]['Office'].' -> '.$result[0]['Industry'].'</td></tr>
        <tr><td>Client Representitive:  </td><td>'.$result[0]['REP_NAME'].'</td></tr>
        <tr><td>Client Representitive(Email):  </td><td>'.$result[0]['REP_EMAIL'].'</td></tr>
        <tr><td>Title:  </td><td>'.$result[0]['Title'].'</td></tr>
        <tr><td>Scorecard:  </td><td> <a href="scorecard_finalview2.php?w='.$result[0]['SCORECARD_ID'].'&t=3">'.$result[0]['SCORECARD_ID'].' - '.$result[0]['SCORECARD_TITLE'].'</a></td></tr>
        <tr><td>Start Date:  </td><td> '.$formattedStartDate.'</td></tr>
        <tr><td>End Date:</td><td> '.$formattedEndDate.'</td></tr>
        <tr><td>Files</td><td>';
        
       for ($i = 0; $i < count($files); $i++) { 
            if (strpos($files[$i]["url"], 'Response_document') !== false) {
                continue; // Skip this iteration if the URL contains 'Response_document'
            }
        
            $display .= "<a style='padding:3.5px;' href='../STORAGE/FILES/" . $files[$i]["url"] . "' download='../STORAGE/FILES/" . $files[$i]["url"] . "'>
                         <img src='../Images/file.png' height=50 width=50></a>";
        }
        if(strcmp($result[0]['USES_EXPENSES'], "YES")==0){
            $display .='</td></tr><tr><td>Uses Expenses:</td><td> '.$result[0]['USES_EXPENSES'].', <span style="margin:2px"> <a class="" href="jobOrder_expenses.php?w='.$result[0]['POST_ID'].'"> <i class="fa fa-external-link-square"></i> View Expenses</a></span></td></tr>';
        }else{
            $display .='</td></tr><tr><td>Uses Expenses:</td><td> '.$result[0]['USES_EXPENSES'].'</td></tr>';
        }
        $worktypes = $result[0]['WORKTYPE'];
        $worktypeIDS = explode(",", $worktypes);
        $display .= $this->worktypeVerifyView($worktypeIDS);
        $admins = $this->fetchAdmins();
        $display .='<tr><td>Job Type: </td><td>'.$result[0]['JOB_TYPE'].'</td>
        <tr><td>Description:</td><td>'.$result[0]['Description'].'</td></tr>
        </tbody></table>';
        if(strcmp($this->TYPE_ENTITY, "ADMIN")==0 && ($result[0]['VERIFIED_BY'] == $this->ID||$result[0]['VERIFIED_BY'] == 1) && $result[0]['APPROVED'] == 0){

            $display .='
            <form style="display:none !important">
            </form>
        
           <form data-parsley-validate class="form-horizontal form-label-left" action="../MARKET/ROUTE.php" Method="POST">
            <input type="text" name="tk" value="'.token::get_ne("VERIFY_JOBORDER_OPENLINKS").'" required="" hidden>
            <input type="text" name="post_id" value="'.$result[0]['POST_ID'].'" required="" hidden>
            <input type="text" name="admin" value="'.$this->ID.'" required="" hidden>
           <div class="ln_solid"></div>
            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                <button type="Submit" class="btn btn-success" name="ADMIN_VERIFY_JOBORDER">Verify</button>

                </div>
              </div>
            </form>';
        }else if((strcmp($this->TYPE_ENTITY, "ADMIN")==0 && $result[0]['VERIFIED_BY'] == 0 && $result[0]['APPROVED'] == 0)){
            $display .='
            <form style="display:none !important">
            </form>
        
           <form data-parsley-validate class="form-horizontal form-label-left" action="../MARKET/ROUTE.php" Method="POST">
            <input type="text" name="tk" value="'.token::get_ne("VERIFY_JOBORDER_OPENLINKS").'" required="" hidden>
            <input type="text" name="post_id" value="'.$result[0]['POST_ID'].'" required="" hidden>
           <div class="ln_solid"></div>
           <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Assign to Admin:</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                <input style="width:35vw" id="scorecards" name="admin" required="required" class="form-control col-md-7 col-xs-12 formz" list="admins" >
            <datalist id="admins">
            ';
            for($i = 0; $i < count($admins); $i++){
                $display .= '<option value="'.$admins[$i]['id'].'" >'.$admins[$i]['id'].' - '.$admins[$i]['firstname'].' '.$admins[$i]['lastname'].'</option>';
            }
            $display.='
            </datalist> 
            </div>
                </div>
              </div>
            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                <button type="Submit" class="btn btn-success" name="ADMIN_VERIFY_JOBORDER">Verify</button>

                </div>
              </div>
            </form>'
            ;
        }
        else{
            
           
           if($noz==0){
                $display .= '<a class="btn btn-success" href="scorecard_response.php?p='.$result[0]['POST_ID'].'&s='.$result[0]['SCORECARD_ID'].'&t='.$entity.'">
            <span>Respond</span> 
            </a>';
               
           }
                
           
            
            
        }
        
        echo $display;
    }
    private function verifyJobOrderForm($result){
        $display = '';
        $display .= '<form data-parsley-validate class="form-horizontal form-label-left" action="../MARKET/ROUTE.php" Method="POST">
        <input type="text" name="tk" value="'.token::get_ne("VERIFY_JOBORDER_OPENLINKS").'" required="" hidden>
        <input type="text" name="post_id" value="'.$result.'" required="" hidden>
       <div class="ln_solid"></div>
        <div class="form-group">
            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
            ';
        $display .= '<button type="Submit" class="btn btn-success" name="ADMIN_VERIFY_JOBORDER">Verif</button>';
        $display .='
            </div>
          </div>
        </form>';
        return $display;
    }
    public function allQuestion($id,$scorecard_id){
        

        $result = $this->getAllQuestions($id);
        $display = $this->questionDisplay($result, $scorecard_id);
        echo $display;
    }
    public function verifyJobOrder($id, $admin){
        
        
        
        $sql ="UPDATE yasccoza_openlink_market.market_post m SET m.APPROVED=1,m.VERIFIED_BY=?, m.ASSIGNED_TO=?, m.updated=CURRENT_TIMESTAMP WHERE m.POST_ID=? ";
        $table = "yasccoza_openlink_market";
        $params = array($this->ID,$admin,$id);
        $types = "iii";
        $query = $this->update($sql, $types, $params, $table);
        //fetch all the post info and insert it
       
        $result = $this->fetchPostInfo($id);
        
        if($result[0]['CLIENT_ID'] > 1000000) {
        $this->saveJob_tms($result[0]['OFFICE_ID'],$result[0]['POST_ID'],$result[0]['Title'],$result[0]['Description'],$result[0]['Start_Date'],$result[0]['EXPIRY'],$result[0]['SCORECARD_ID'],$result[0]['WORKTYPE'], $admin, $result[0]['CLIENT_ID'],$result[0]['CLIENT_ID'],$result[0]['JOB_TYPE'],$result[0]['Created'],$result[0]['updated']);
        header("location: ../".$this->TYPE_ENTITY."/post_verify.php?result=success");
        exit();
        }else{
            
        $client = $this->fetchClientById($result[0]['CLIENT_ID']);
        $this->saveJob_tms($result[0]['OFFICE_ID'],$result[0]['POST_ID'],$result[0]['Title'],$result[0]['Description'],$result[0]['Start_Date'],$result[0]['EXPIRY'],$result[0]['SCORECARD_ID'],$result[0]['WORKTYPE'], $admin, $client[0]['CLIENT_ID'],$result[0]['CLIENT_REP'],$result[0]['JOB_TYPE'],$result[0]['Created'],$result[0]['updated']);
        header("location: ../".$this->TYPE_ENTITY."/post_verify.php?result=success");
        exit();
            
        }
    
    }
    private function fetchPostInfo($id){
        $sql = "SELECT * FROM yasccoza_openlink_market.market_post m WHERE m.POST_ID = ?";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function getAllQuestions($id){
        $sql = "SELECT * FROM question WHERE CRITERIA_ID = ?";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function fetchReps($id){
        $sql = "SELECT * FROM yasccoza_tms_db.client_rep WHERE CLIENT_ID = ?";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function clientsDisplay($result){
        $display ="<h3>Clients</h3>";
        $display .='<table class="table table-striped table-bordered"></br>
        <thead>
          <tr></tr>
            <th>ID</th>
            <th>Company </th>
            <th>City</th>
            <th>Province</th>
            <th>Representative</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>';
        for($i = 0; $i < count($result); $i++){
            $reps = $this->fetchReps($result[$i]['CLIENT_ID']);
                $display .= '
                <tr>
                <th scope="row">'.$result[$i]['CLIENT_ID'].'</th>
                <td>'.$result[$i]['company_name'].'</td>
                <td>'.$result[$i]['city'].'</td>
                <td>'.$result[$i]['province'].'</td> 
                <td>';
                if(!empty($reps)){
                for ($x=0; $x < count($reps); $x++) { 
                    # code...
                    if($x>0){
                        $display .= ",(".$reps[$x]["REP_NAME"].")";
                    }else{
                        $display .= "(".$reps[$x]["REP_NAME"].")";
                    }
                   
                }
            }else{
                $display .= "No representative";
            }
            $display.=   '</td>   
                <td><a class="btn btn-primary" href="add_rep.php?u='.$result[$i]['CLIENT_ID'].'">Add Rep</a></td>
                </tr>
            ';
            }
            $display .= '
            </tbody>
            
            </table>'; 
            echo $display;
    }
    private function questionDisplay($result,$scorecard_id){
        $display = '';
        if(!empty($result)){ 
        $criteria = $this->fetchCriteriaByCriteriaId($result[0]['CRITERIA_ID']);
        $display .= '<h3 class="text-center">Questions for Criteria -> '.$criteria[0]["Name"].'</h3>
        <table class="table"><tbody>
        ';
        $entity = 0;
            switch($this->TYPE_ENTITY){
                case "SMME":
                    $entity = 1;
                    break;
                case "COMPANY":
                    $entity = 2;
                    break;
                case "ADMIN":
                    $entity = 3;
                    break;
            }
 
            $display .='<table class="table table-striped table-bordered"></br>
            <thead>
            <tr></tr>
                <th>#</th>
                <th>Question </th>
                <th>Weighting</th>
                <th>Options</th>
                <th>Action</th>
          
            </tr>
            </thead>
            <tbody>';
            $sum = 0;
            for($i = 0; $i < count($result); $i++){
                    $display .= '
                    <tr>
                    <th scope="row">'.($i+1).'</th>';
               
                    $display .= '<td>'.$result[$i]['Question'].'</td>';
                   $display .=' <td>'.$result[$i]['Weighting'].'%</td>          
                    <td style="text-decoration:underline; color:blue"> <a href="../'.$this->TYPE_ENTITY.'/question_options.php?d=1&s='.$scorecard_id.'&t='.$entity.'&w='.$result[$i]["QUESTION_ID"].'" style="color:blue">All Options</a> </td>
                    <td><a href="question_edit.php?d=1&s='.$scorecard_id.'&c='.$result[0]['CRITERIA_ID'].'&t='.$entity.'&w='.$result[$i]['QUESTION_ID'].'"><span>Edit Information </span> <i class="fa fa-pencil"></i></a></td>
                    
        </tr>
                ';
                }
                $display .= '
                
                </tbody>
                
                </table>'; 
        
        $display .='
        <a href="create_questions.php?t='.$entity.'&w='.$criteria[0]["CRITERIA_ID"].'"><span>Add Questions </span> <i class="fa fa-pencil"></i></a>
        ';
    }
    else{
        $display .= '<h3 class="text-center">No Questions Yet</h3>';
    }
        return $display;
    }
    private function Allcriteria($results){
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        $display ='<h3 class="text-center text-capitalize">criteria </h3>';
        if(!empty($results)){   
        $display .='
        <table class="table table-striped table-bordered">
        <thead>
            <tr></tr>
            <th>#</th>
            <th>Name </th>
            <th>Supporting Document</th>
            <th>Weighting</th>
            <th>Questions</th>
            <th>Action</th>
            <th>Action</th>
            </tr>
        </thead>
        <tbody>';
        $sum=0;
        for($i = 0; $i < count($results); $i++){
            $criteria = new CRITERIA($results[$i]['Description'],$results[$i]['Name'],$results[$i]['Weighting']);
            $sum += $results[$i]['Weighting'];
            $display .= '
            <tr>
              <th scope="row">'.($i+1).'</th>
              <td>'.$results[$i]['Name'].'</td>
              <td>'.$results[$i]['Document'].'</td>
              ';
             if($sum <= 100){
                $display .= '<td>'.$results[$i]['Weighting'].'%</td>';
             }else{
                $display .='<td style="background-color:red; color:white">'.$results[$i]['Weighting'].'% !Weights need to add up to 100%. Total: '.$sum.'%</td>';
             } 
              $display .= '<td style="text-decoration:underline; color:blue"> <a href="../'.$this->TYPE_ENTITY.'/criteria_questions.php?s='.$results[$i]['SCORECARD_ID'].'&t='.$entity.'&w='.$results[$i]["CRITERIA_ID"].'" style="color:blue">All Questions</a> </td>
              <td><a href="weight_adjust.php?s='.$results[$i]['SCORECARD_ID'].'&t='.$entity.'&w='.$results[$i]['CRITERIA_ID'].'"><span>Edit</span></a></td>
              <td><a href="criteria_delete.php?s='.$results[$i]['SCORECARD_ID'].'&t='.$entity.'&w='.$results[$i]['CRITERIA_ID'].'"><span>Remove</span> <i class="fa fa-trash"></i></a></td>
            </tr>
         ';
        }
        $display .= '
        </tbody>
         
        </table>

 
        ';
    }else{
        $display .='<h3 class="text-center text-capitalize" style="font-size:15px; font-weight:normal">no criteria addded !</h3>';
    }
        echo $display;
    }
    private function SingleCriteria($results, $criteria, $scorecard_id){
    
        $entity = 0;
        switch($this->TYPE_ENTITY){
            case "SMME":
                $entity = 1;
                break;
            case "COMPANY":
                $entity = 2;
                break;
            case "ADMIN":
                $entity = 3;
                break;
        }
        $display ='<h3 class="text-center text-capitalize">criteria </h3>
            
        <div>
        <table class="table table-bordered">
            <tr>
                <td>Name </td><td>'.$criteria[0]["Name"].'</td>
            </tr>
            <tr>
                <td>Document Required </td><td>'.$criteria[0]["Description"].'</td>
            </tr>
            <tr>
                <td>Document Required </td><td>'.$criteria[0]["Document"].'</td>
            </tr>
            <tr>
                <td>Action </td><td><a href="criteria_edit.php?t='.$entity.'&w='.$criteria[0]["CRITERIA_ID"].'"><span>Edit Information </span> <i class="fa fa-pencil"></i></a> </td>
            </tr>
            <tr>
            <td>Action </td><td><a href="create_questions.php?t='.$entity.'&w='.$criteria[0]["CRITERIA_ID"].'"><span>Add Questions </span> <i class="fa fa-pencil"></i></a> </td>
        </tr>
        </table>';
      if(empty($results)){
        $display.= "<h3 class='text-center'> No Questions</h3>";
      }else{
        $display .=' <h3 class="text-center"> Questions <small>(Weights must add up to 100%)</small> </h3> <table class="table table-striped table-bordered"></br>
        <thead>
          <tr></tr>
            <th>#</th>
            <th>Question </th>
            <th>Weighting</th>
            <th>Options</th>
            <th>Action</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>';
        $sum = 0;
        for($i = 0; $i < count($results); $i++){
            $sum += $results[$i]['Weighting'];
                $display .= '
                <tr>
                <th scope="row">'.($i+1).'</th>
                <td>'.$results[$i]['Question'].'</td>';
                if($sum > 100){
                    $display .= '<td style="background:red; color:white">'.$results[$i]['Weighting'].'% <Ssmall>Weights need to be adjusted on edit. '.$sum.'%</Ssmall></td>    ';
                }else{
                    $display .= '<td>'.$results[$i]['Weighting'].'%</td>    ';
                }
                $display .='<td style="text-decoration:underline; color:blue"> <a href="../'.$this->TYPE_ENTITY.'/question_options.php?s='.$scorecard_id.'&t='.$entity.'&w='.$results[$i]["QUESTION_ID"].'&d=2" style="color:blue">All Options</a> </td>
                <td><a href="question_edit.php?c='.$results[$i]['CRITERIA_ID'].'&d=2&t='.$entity.'&w='.$results[$i]['QUESTION_ID'].'"><span>Edit Information </span> <i class="fa fa-pencil"></i></a></td>
                <td><a href="question_delete.php?q='.$results[$i]['QUESTION_ID'].'&t='.$entity.'&w='.$results[$i]['CRITERIA_ID'].'"><span>Remove</span> <i class="fa fa-trash"></i></a></td>
                </tr>
            ';
            }
            $display .= '
            </tbody>
            
            </table>'; 
            }
        echo $display;
    }
    private function fetchScorecardCriteria(){
        $sql = "SELECT * FROM criteria c,scorecard_criteria sc WHERE c.CRITERIA_ID = sc.CRITERIA_ID AND c.User_ID=? ORDER BY c.CRITERIA_ID DESC LIMIT 1";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($this->ID);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function fetchScorecardCriteriaByScorecard($id, $csorecard){
        $sql = "SELECT * FROM criteria c,scorecard_criteria sc WHERE c.CRITERIA_ID = sc.CRITERIA_ID AND c.CRITERIA_ID=? AND sc.SCORECARD_ID =? ";
        $table = "yasccoza_openlink_market";
        $types="ii";
        $params=array($id,$csorecard);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    
    private function fetchAllQuestionsByCriteria($id){
        $sql = "SELECT * FROM yasccoza_openlink_market.question q WHERE q.QUESTION_ID=?";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function fetchAllChoicesByQuestion($id){
        $sql = "SELECT * FROM yasccoza_openlink_market.question_choice qc WHERE qc.QUESTION_ID = ?";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function fetchPostResponses($id){
        $sql = "SELECT * FROM yasccoza_openlink_market.scorecard_response sr WHERE sr.POST_ID = ?";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function fetchChoiceById($id){
        $sql = "SELECT * FROM yasccoza_openlink_market.question_choice qc WHERE qc.CHOICE_ID = ?";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function responseAlgorithm($post_id, $scorecard_id, $questions, $choices, $companyz){
        //$responses = $this->fetchPostResponses($post_id);
        // $criterias = $this->fetchScorecardCriteriaByScorecard($scorecard_id);
        // foreach($criterias as $criteria){
        //     $questions = $this->fetchAllQuestionsByCriteria($criteria['CRITERIA_ID']);
        //     foreach ($questions as $question) {
        //         $choices =$this->fetchAllChoicesByQuestion($question['QUESTION_ID']);
        //     }
        // }
        
        $response_score = 0;
        $current_question_id = 0;
        $current_criteria_id = 0;
        $criteria_sum = 0;
        $user = 0;
        

        for ($i=0; $i < count($questions); $i++) { 
            # code...
             //print_r($choices);
            $current_question_id = $questions[$i];
            $choice_id = $choices[$i];
            $choice = $this->fetchChoiceById($choice_id);
            $question = $this->fetchQuestion($current_question_id);
            if(count($question) == 1){
                $user = $this->ID;
                $current_criteria_id = $question[0]['CRITERIA_ID'];
                $choice_weight = $choice[0]['Weighting'];
                $question_weight = $question[0]['Weighting'];
                $weighted_question = $choice_weight * ($question_weight/100);
                //echo "Q -->" .$weighted_question;
                $criteria_sum = $criteria_sum + $weighted_question;
                $criteria = $this->fetchScorecardCriteriaByScorecard($current_criteria_id, $scorecard_id);
                $response_score += $criteria_sum * ($criteria[0]['Weighting']/100);
                //$this->saveResponseScore($user, $response_score, $scorecard_id, $post_id);
            }else{
                if(($current_criteria_id == $question[0]['CRITERIA_ID'])&& $i != count($questions)-1){
                    #same criteria so add to the sum the weighted average
                    $user = $this->ID;
                    $current_criteria_id = $question[0]['CRITERIA_ID'];
                    $choice_weight = $choice[0]['Weighting'];
                    $question_weight = $question[0]['Weighting'];
                    //echo "CHOICE -->";
                    //print_r($choice_weight);
                   // echo "Q -->"; print_r($question_weight);
                    
                    $weighted_question = $choice_weight * ($question_weight/100);
                    //echo "Q -->" .$weighted_question;
                    $criteria_sum = $criteria_sum + $weighted_question;
                   
                }else{
    
                   
                    #different criteria so add sum to overall scorecard score and reset values
                    $criteria = $this->fetchScorecardCriteriaByScorecard($current_criteria_id, $scorecard_id);
                    $response_score += $criteria_sum * ($criteria[0]['Weighting']);
                    //$this->saveResponseScore($user, $response_score, $scorecard_id, $post_id);
                    $current_criteria_id = $question[0]['CRITERIA_ID'];
                    //echo "Criteria sum ".$i ." -> ".$criteria_sum;
                    //echo"<br>";
                    $criteria_sum = 0;
                    $choice_weight = $choice[0]['Weighting'];
                    $question_weight = $question[0]['Weighting'];
                    // $weighted_question = $choice_weight * $question_weight;
                    // $criteria_sum = $criteria_sum + $weighted_question;
                }
            }
        }
        $this->saveResponseScore($user, $response_score, $scorecard_id, $post_id, $companyz);
        //echo "Response score total = ".$response_score;
        //exit();
    }
    private function saveResponseScore($user, $score, $scorecard, $post_id, $companyz){
        
        if (!empty($companyz)){
            
            $sql = "INSERT INTO `responsescore`(`POST_ID`, `SCORECARD_ID`, `USER_ID`, `SCORE` , `COMPANY`) VALUES (?,?,?,?,?)";
        $types="iiiis";
        $params = array($post_id, $scorecard, $user, $score, $companyz);
        $table = "responseScore";
        $query = $this->save($sql, $types, $params, $table);
            
        }else{
            
            
            $sql = "SELECT Legal_name FROM register WHERE SMME_ID=?";
            $types = "i";
            $params = array($this->ID);
            $table = "yasccoza_openlink_smmes";
            $result = $this->fetch($table, $sql, $types, $params);
            $legalName = $result[0]['Legal_name'];
            
            
            $this->master->changedb("yasccoza_openlink_market");
            $sql = "INSERT INTO `responsescore`(`POST_ID`, `SCORECARD_ID`, `USER_ID`, `SCORE` , `COMPANY`) VALUES (?,?,?,?,?)";
            $types="iiiis";
            $params = array($post_id, $scorecard, $user, $score, $legalName);
            $table = "responseScore";
            $query = $this->save($sql, $types, $params, $table);
            
        }
        
    }
    public function editScoreCard($id){
        //fetch 
        $result = $this->fetchCardById($id);
        $criteria = $this->fetchCriteriaById($this->ID);
        $display = $this->editDisplay($result, $criteria);
        echo $display;
    }
    public function editCriteria($id){
        //fetch 
        $criteria = $this->fetchCriteriaByCriteriaId($id);
        $display = $this->editCriteriaDisplay($criteria);
        echo $display;
    }
    public function adjustScorecardWeights($scorecard, $criteria_id){
        $criteria = $this->fetchScorecardCriteriaByScorecard($criteria_id,$scorecard);
        $display = $this->editWeightDisplay($criteria, $scorecard, $criteria_id);
        echo $display;
    }
    public function editQuestion($id){
        //fetch 
        $question = $this->fetchQuestion($id);
        $display = $this->editQuestionDisplay($question);
        echo $display;
    }
    public function editOption($id, $scorecard,  $from){
        //fetch 
        $choice = $this->fetchOption($id);
        //display
        $display = $this->editOptionDisplay($choice,$scorecard, $from);
        echo $display;
    }
    public function clientReps($id){
        
        $sql = "SELECT * FROM yasccoza_tms_db.client_rep WHERE CLIENT_ID=?";
        $table = "yasccoza_tms_db";
        $params = array($id);
        $types = "i";
        $result = $this->fetch($table, $sql, $types, $params);
        $display = "";
        for($i=0; $i<=count($result)-1; $i++){
            if($i==0)$display.= "<option value='' selected> --blank-- </option>";
            $display.= "<option value='".$result[$i]["REP_ID"]."'>".$result[$i]["REP_NAME"]."</option>";
        }
         echo $display;
    }
    
    
     public function Indus($id){
         
        $sql = "SELECT * FROM yasccoza_openlink_association_db.industry_title WHERE INDUSTRY_ID=?";
        $table = "yasccoza_openlink_association_db";
        $params = array($id);
        $types = "i";
        $result = $this->fetch($table, $sql, $types, $params);
        $display = "";
        for($i=0; $i<=count($result)-1; $i++){
            if($i==0)$display.= "<option value='' selected> --blank-- </option>";
            $display.= "<option value='".$result[$i]["TITLE_ID"]."'>".$result[$i]["title"]."</option>";
        }
        echo $display;
    }
    
    
    
    private function editDisplay($result, $criteria){
       $sum = $this->fetchCriteriaSum($result[0]['SCORECARD_ID']);
       $left = 100 - $sum[0]['Sum'];
        $display = '
        <div class="form-group">
        <h3>Current criteria weights: '.$sum[0]['Sum'].'% . Left: '.$left.'%</h3>
        <input style="width:35vw; visibility:hidden " type="number" id="weightTotal" name="weightTotal" value="'.$sum[0]['Sum'].'"  class="form-control col-md-7 col-xs-12" hidden>
      </div></br>
      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Title:<span class="required"></span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="text" id="first-name" value="'.$result[0]['Title'].'" name="Title" required="required" class="form-control col-md-7 col-xs-12 formz">
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Other information:<span class="required"></span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="text" id="first-name" name="Other" value="'.$result[0]['Other'].'" required="required" class="form-control col-md-7 col-xs-12 formz">
        </div>
      </div>

      <div class="item form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="Date">Date of Expiry:<span class="required"></span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="date" id="number" name="Date" value="'.$result[0]['Date_of_Expiry'].'" required="required" data-validate-minmax="10,100" class="form-control col-md-7 col-xs-12">
        </div>
      </div>
      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Criteria:</label>
                        <div required="required" class="col-md-6 col-sm-6 col-xs-12">
                          <select id="criteria" style="width:35vw" class="form-control col-md-7 col-xs-12 formz" name="Criteria[]">
                          <option value="100">Please choose a criteria</option>
                          ';
                          for($i =0;$i<count($criteria);$i++){
                            $display .= '<option value="'.$criteria[$i]['CRITERIA_ID'].'">'.$criteria[$i]['Name'].'</option>';
                          }
                          $display .='
                          </select>
                        </div>
                      </div>
        <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Weighting:</label>
        <div required="required" class="col-md-6 col-sm-6 col-xs-12">
            <input style="width:35vw" type="number" id="weight" class="form-control col-md-7 col-xs-12 formz" name="weight" placeholder"20%" >
        </div>
    </div>
        ';
        return $display;
    }
    private function editCriteriaDisplay($result){
        $display = '<div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Name:<span class="required"></span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="text" id="first-name" value="'.$result[0]['Name'].'" name="Name" required="required" class="form-control col-md-7 col-xs-12 formz">
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Description:<span class="required"></span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="text" id="first-name" name="Description" value="'.$result[0]['Description'].'" required="required" class="form-control col-md-7 col-xs-12 formz">
        </div>
      </div>
      <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Supporting Documents:</label>
      <div class="col-md-6 col-sm-6 col-xs-12">
        <input id="middle-name" style="width:35vw"  required="required" value="'.$result[0]['Document'].'" name="Documents"class="form-control col-md-7 col-xs-12 formz" type="text" >
      </div>
        </div>

        ';
        return $display;
    }
    private function editWeightDisplay($result, $scorecard_id, $criteria){
        $scorecard = $this->fetchCardById($scorecard_id);
    
        $display = '<div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Scorecard:<span class="required"></span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="text" id="first-name" value="'.$scorecard[0]['Title'].'" name="Name" disabled required="required" class="form-control col-md-7 col-xs-12 formz">
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Criteria:<span class="required"></span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="text" id="first-name" name="Description" value="'.$result[0]['Name'].'" disabled required="required" class="form-control col-md-7 col-xs-12 formz">
        </div>
      </div>
      <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Weight:</label>
      <div class="col-md-6 col-sm-6 col-xs-12">
        <input id="middle-name" style="width:35vw"  required="required" value="'.$result[0]['Weighting'].'" name="weight" class="form-control col-md-7 col-xs-12 formz" type="number" >
      </div>
        </div>
        <input id="middle-name" style="width:35vw;display:none"   required="required" value="'.$scorecard_id.'" name="scorecard" class="form-control col-md-7 col-xs-12 formz" type="number" hidden>
        <input id="middle-name" style="width:35vw;display:none"  required="required" value="'.$criteria.'" name="criteria" class="form-control col-md-7 col-xs-12 formz" type="number" hidden>
        ';
        return $display;
    }
    private function editQuestionDisplay($result){
        
        $display = '<div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Question:<span class="required"></span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="text" id="first-name" value="'.$result[0]['Question'].'" name="Question" required="required" class="form-control col-md-7 col-xs-12 formz">
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Weighting:<span class="required"></span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="number" id="first-name" name="Weight" value="'.$result[0]['Weighting'].'" required="required" class="form-control col-md-7 col-xs-12 formz">
        </div>
      </div>
        ';
        return $display;
    }
    private function editOptionDisplay($result, $scorecard, $from){    
          
        $question = $this->fetchQuestion($result[0]['QUESTION_ID']);
        $display = "<h3>Option for question -> ".$question[0]["Question"]."</h3>";
        $display .= '
        
        <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left" action="../MARKET/ROUTE.php?s='.$scorecard.'&w='.$result[0]["CHOICE_ID"].'&d='.$from.'" Method="POST">
                  
            <div class="ln_solid"></div>
            

            <input type="text" name="tk" value="'.token::get_ne("OPTION_UPDATE_OPENLINKS").'" required="" hidden>
            <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Choice:<span class="required"></span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
            <input style="width:35vw" type="text" id="first-name" value="'.$result[0]['choice'].'" name="choiceText" required="required" class="form-control col-md-7 col-xs-12 formz">
            </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Weighting:<span class="required"></span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                <input style="width:35vw" type="number" id="first-name" name="choiceWeight" value="'.$result[0]['Weighting'].'" required="required" class="form-control col-md-7 col-xs-12 formz">
                </div>
            </div>
        
            <div class="form-group">
            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            <button class="btn btn-primary" type="reset" >Cancel</button>
                ';
                
        switch($this->TYPE_ENTITY){
            case "SMME":
                $display.= '<button type="Submit" class="btn btn-success" name="SMME_OPTIONS_UPDATE">Submit</button>';
                break;
            case "COMPANY":
                $display.= '<button type="Submit" class="btn btn-success" name="COMPANY_OPTIONS_UPDATE">Submit</button>';
                break;
            case "ADMIN":
                $display.= '<button type="Submit" class="btn btn-success" name="ADMIN_OPTIONS_UPDATE">Submit</button>';
                break;
        }
        $display.='  
            </div>
            </div>
        </form>
        
        
        
       
        ';
        return $display;
    }
    private function fetch($table, $sql, $types, $params){
        $query = $this->master->select_prepared_async($sql, $table, $types, $params);
        if(!$query){

        }else{
            $result = $this->master->getResult();
            return $result;
        }
    }
    private function fetchNoParms($sql, $db){
        $query = $this->master->select_multiple_async($sql, $db);
        if(!$query){

        }else{
            $result = $this->master->getResult();
            return $result;
        }
    }
    private function delete( $sql,$table, $types, $params){
        $query = $this->master->delete($table,$sql, $types, $params);
    }
    private function save($sql, $types, $params, $table){
        $query = $this->master->insert($table,$sql, $types, $params);
        if(!$query){
            return -1;
        }else{
            return 1;
        }
    }



    
    private function update($sql, $types, $params, $table){
        $query = $this->master->update($table,$sql, $types, $params);
        if(!$query){
            return -1;
        }else{
            return 1;
        }
    }
    //algorithm for scorecard scores
    private function fetchResponse($id){
        //fetches the response for all the users for that specific scorecard
        //input -> scorecard_id
        //output -> array of responses
        $sql = "SELECT * FROM `scorecard` WHERE User_Id = ? AND OWNER = 'USER' OR OWNER = 'OPENLINKS'";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function fetchQuestionWeights($id){
        //fetches the response for all the users for that specific scorecard
        //input -> scorecard_id
        //output -> array of responses
        $sql = "SELECT * FROM `scorecard` WHERE User_Id = ? AND OWNER = 'USER' OR OWNER = 'OPENLINKS'";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    private function fetchCriteriaWeights($id){
        //fetches the response for all the users for that specific scorecard
        //input -> criteria
        //output -> array of criteria weights
        $sql = "SELECT * FROM `scorecard` WHERE User_Id = ? AND OWNER = 'USER' OR OWNER = 'OPENLINKS'";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }

}
// <form id="demo-form2" data-parsley-validate class="dropzone" action="../MARKET/ROUTE.php" Method="POST">
// <div class="item form-group" id="files">
//     <div id="file">
//         <label class="control-label col-md-3 col-sm-3 col-xs-12" >Upload file<span class="required"></span>
//         </label>
//         <div class="col-md-6 col-sm-6 col-xs-12 custom-file">
//             <input style="border-style: none;" type="file" style="width:35vw" name="file[]" class="form-control col-md-7 col-xs-12 custom-file-input formz" >
//         </div>
//     </div>
// </div>
   

  
//   <div class="ln_solid"></div>
  

//   <input type="text" name="tk" value="'. token::get_ne("RESPONSEFILES_CREATE_OPENLINKS").'" required="" hidden>
  
// <div id="buttons">
// <button class="btn" type="button" id="addFile2">Add Another File + </button>
// <div class="form-group">
//     <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
//                   ';
   
//         if(strcmp($this->TYPE_ENTITY,"SMME" )==0){//adimin
//               $display .'<button type="Submit" class="btn btn-success" name="SMME_RESPONSEFILES_CREATE">Submit</button>';
//         }
//         if(strcmp($this->TYPE_ENTITY,"ADMIN" )==0){//adimin
//             $display .'<button type="Submit" class="btn btn-success" name="ADMIN_RESPONSEFILES_CREATE">Submit</button>';
//       }
        
//       $display .='
      
//     </div>
//   </div> 
//   </div>
// </form>';