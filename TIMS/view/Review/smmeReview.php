<?php 

class smmeReview{
public function filterDsiplay($result){
        $disp = "<div class='row' id='admin-buttons'>";
        $disp .= "
            <div  style='width:20%' class='form-control col-md-7 col-xs-12'><select style='width:100%; ' id='office_input'>";
        for($i = 0; $i < count($result); $i ++){
            if($i == 0 || ($i > 0 && $result[$i]['office'] != $result[$i-1]['office'])){//only add the option that does not already exist, filter the ones that are the same
            $disp .= "
                <option value='".$result[$i]['INDUSTRY_ID']."'>".$result[$i]['office']."</option>
            ";
            }
        }
        $disp .="
            </select><br><button style='margin:0.5em;' class='btn btn-primary filter' id='office'>Go<button></div>
        ";
        $disp .= $this->industryFilter($result);
        $disp .= $this->roleFilter($result);
        // $disp .= $this->cityFilter($result);
        // $disp .= $this->provinceFilter($result);
        $disp .= "</div><br><div class='ln_solid'></div>";
        echo $disp;

    }
    private function industryFilter($result){
        $disp = "
            <div style='width:27%' class='form-control col-md-7 col-xs-12'><select style='width:100%;' id='indus_input'>";
        for($i = 0; $i < count($result); $i ++){
           if($i == 0 || ($i > 0 && $result[$i]['title'] != $result[$i-1]['title'])){//only add the option that does not already exist, filter the ones that are the same
            $disp .= "
                <option value='".$result[$i]['TITLE_ID']."'>".$result[$i]['title']."</option>
            ";
           }
        }
        $disp .="
            </select><br><button style='margin:0.5em;' class='btn btn-primary filter' id='industry'>Go<button></div>
        ";
        return $disp;

    }
    private function roleFilter($result){
        $disp = "
            <div style='width:15%' class='form-control col-md-7 col-xs-12'><select style='width:100%;'  id='role_input'>";
        for($i = 0; $i < count($result); $i ++){
           if($i == 0 || ($i > 0 && $result[$i]['type'] != $result[$i-1]['type'])){//only add the option that does not already exist, filter the ones that are the same
             $type ="";
            switch($result[$i]['type']){
                case 1:
                    $type = "Main Admin";
                    break;
                case 2:
                    $type = "Project Manager";
                    break;
                case 3:
                    $type = "Employee";
                    break;
            }
            $disp .= "
                <option value='".$result[$i]['type']."'>".$type."</option>
            ";
            }
        }
        $disp .="
            </select><br><button style='margin:0.5em;' class='btn btn-primary filter' id='role'>Go<button></div>
        ";
        return $disp;
    }
    private function cityFilter($result){
        $disp = "
            <div style='width:15%' class='form-control col-md-7 col-xs-12'><select style='width:100%; '  id='city_input'>";
        for($i = 0; $i < count($result); $i ++){
            if($i == 0 || ($i > 0 && $result[$i]['city'] != $result[$i-1]['city'])){//only add the option that does not already exist, filter the ones that are the same
            $disp .= "
                <option value='".$result[$i]['city']."'>".$result[$i]['city']."</option>
            ";
           }
        }
        $disp .="
            </select><br><button style='margin:0.5em;' class='btn btn-primary filter' id='city'>Go<button></div>
        ";
        return $disp;
    }
    private function provinceFilter($result){
        $disp = "
            <div style='width:13%' class='form-control col-md-7 col-xs-12'><select style='width:100%;' id='province_input'>";
        for($i = 0; $i < count($result); $i ++){
            if($i == 0 || ($i > 0 && $result[$i]['province'] != $result[$i-1]['province'])){//only add the option that does not already exist, filter the ones that are the same
            $disp .= "
                <option value='".$result[$i]['province']."'>".$result[$i]['province']."</option>
            ";
           }
        }
        $disp .="
            </select><br><button style='margin:0.5em;' class='btn btn-primary filter' id='province'>Go<button></div>
        ";
        return $disp;
    }

    public function displayAdmins($result){
        

        $display = '<table class="table table-bordered">
        <thead>
            <th>Name</th>
            <th>Surname</th>
            <th>Type</th>
        </thead>';
        
        for($i = 0; $i < count($result); $i ++){
            $id = token::encode($result[$i]["id"]);
            $type ="";
            switch($result[$i]['type']){
                case 1:
                    $type = "Main Admin";
                    break;
                case 2:
                    $type = "Project Manager";
                    break;
                case 3:
                    $type = "Employee";
                    break;
            }
            $display .= '
            
            <tr>
            <td><a href="chat.php?id='.$id.'">'.$result[$i]["firstname"].'</a> </td>
            <td><a href="chat.php?id='.$id.'">'. $result[$i]["lastname"].'</a></td>
            <td><a href="chat.php?id='.$id.'">'.$type.' </a></td>
             </tr>
             
            
          ';
            }
            $display .= '</table>';
      echo $display;
    }
    public function display($admin_info, $company_info, $director, $statement, $links, $document,$products,$keywords,$names, $logo){
        $display = '<div class="container">
        <br>
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#home">Admin</a></li>
          <li><a data-toggle="tab" href="#menu4">Company </a></li>
          <li><a data-toggle="tab" href="#menu2">Directors</a></li>
          <li><a data-toggle="tab" href="#menu3">Statement</a></li>
          <li><a data-toggle="tab" href="#menu5">Documents</a></li>
          <li><a data-toggle="tab" href="#menu6">Products</a></li>
          <li><a data-toggle="tab" href="#menu7">Keywords</a></li>
          <li><a data-toggle="tab" href="#menu8">Links</a></li>
          <li><a data-toggle="tab" href="#menu9">Logo</a></li>
        </ul>
      
        <div class="tab-content">

          <div id="home" class="tab-pane fade in active">
            
            ';
            
            $display .= $this->admin($admin_info);
            $display .= '
          </div>

          <div id="menu4" class="tab-pane fade">
            
            '; 
            $display .= $this->register($company_info);
             
            $display .= '
          </div>

          <div id="menu2" class="tab-pane fade">
            
            ';
            $display .=  $this->director($director);
            
            $display .= '

          </div>

          <div id="menu3" class="tab-pane fade">
            
          ';
          $display .=  $this->statement($statement);
          
          $display .= '

        </div>

        <div id="menu5" class="tab-pane fade">
            
          ';
          $display .=  $this->document($document);
          
          $display .= '

        </div>

        <div id="menu6" class="tab-pane fade">
            
          ';
          $display .=   $this->products($products);
          
          $display .= '

        </div>

        <div id="menu7" class="tab-pane fade">
            
          ';
          $display .=   $this->keywords($keywords);
          
          $display .= '

        </div>

          <div id="menu8" class="tab-pane fade">
           
            '.$this->links($links, $names).'
          </div>

       
          <div id="menu9" class="tab-pane fade">
           
          '.$this->logo($logo).'
        </div>
        </div>
      </div>
      
        
        ';

        echo $display;
        
      
    }

    private function admin($admin_info){
        $display = "";
        $display .= '<div class="x_panel" >
            <h3 class="text-center">Admin Information</h3>';
            if(empty($admin_info) || $admin_info[0]["Active"] == 1){
                $display .= '<div class="text-center"><div class="col-6"><h3>No information filled in</h3> </div><div class="btn col-3"><a href="admin_info.php"><span>Fill Information Here</span> <i class="fa fa-pencil"></i></a> </div></div>
                ';
                
            }else{
                $display .= '<table class="table"><tbody>';
                $display .= '<tr><td>Name  </td><td>'.$admin_info[0]["first_name"].'</td></tr>
                <tr><td>Surname  </td><td>'.$admin_info[0]["Surname"].'</td></tr>';
                if($admin_info[0]["Identification_Type"] == "SA_ID"){
                    $display .= '<tr><td>ID Number</td><td>'.$admin_info[0]["ID_Number"].'</td></tr>';
                }else{
                    $display .= '<tr><td>Passport Number</td><td>'.$admin_info[0]["ID_Number"].'</td></tr>';
                }
                $display .='
                <tr><td>Email</td><td>'.$admin_info[0]["Email"].' </td></tr>
                <tr><td>Gender </td><td>'.$admin_info[0]["Gender"].'</td></tr>
                <tr><td>Ethnic Group </td><td>'.$admin_info[0]["Ethnic_Group"].' </td></tr></tbody></table>'
                ;
                $display .= '
                <div     class="text-center"><div class="btn col-3"><a href="admin_info_update.php?action=0"><span>Edit Information </span> <i class="fa fa-pencil"></i></a> </div>
                    <div class="btn col-3"><a href="admin_info_update.php?action=1"><span>Delete Information </span> <i class="fa fa-trash"></i></a> </div></div>';
            }
            
            $display .= '</div>';
            return $display;
    }

    private function register($company_info){
        
        $display = "<div class='x_panel'>";
        $display .= '
        <h3 class="text-center">Company Information </h3>';
        if(empty($company_info) || $company_info[0]["Active"] == 1 ){//deleted
            $display .= '<div class="text-center"><div class="col-6"><h3>No information filled in</h3> </div><div class="btn col-3"><a href="company_info.php"><span>Fill Information Here</span> <i class="fa fa-pencil"></i></a> </div></div>
            ';
        }else{
            
            $display .= '<table class="table"><tbody>';
            $display .= '<tr><td>Trade Name  </td><td>'.$company_info[0]["Trade_Name"].'</td></tr>
            <tr><td>Legal Name  </td><td>'.$company_info[0]["Legal_name"].'</td></tr>
            <tr><td>CIPC Registration </td><td>'.$company_info[0]["CC_Registration_Number"].' </td></tr>
            <tr><td>Ownership </td><td>'.$company_info[0]["foo"].'</td></tr>
            <tr><td>Financial Year </td><td>'.$company_info[0]["Financial_Year"].' </td></tr>
            <tr><td>Street </td><td>'.$company_info[0]["Address"].' </td></tr>
            <tr><td>City </td><td>'.$company_info[0]["city"].'</td></tr>
            <tr><td>Province </td><td>'.$company_info[0]["Province"].'</td></tr>
            <tr><td>Postal Code  </td><td>'.$company_info[0]["Post_Code"].'</td></tr>
            <tr><td>Contact </td><td>'.$company_info[0]["Contact"].' </td></tr>
            <tr><td>Email </td><td>'.$company_info[0]["Email"].' </td></tr></tbody></table>'
            ;
            $display .= '<div class="text-center"><div class="btn col-3"><a href="company_info_update.php?action=0"><span>Edit Information </span> <i class="fa fa-pencil"></i></a> </div>
        <div class="btn col-3"><a href="company_info_update.php?action=1"><span>Delete Information </span> <i class="fa fa-trash"></i></a> </div></div>
        
        ';
        }
        
        $display .= '</div>';
        return $display;
    }
   private function director($director){
        
        $display = "<div class='x_panel'>";
        $display .= '
        <h3 class="text-center">Director Information </h3>';
        if(empty($director) || $director[0]["Active"] == 1 ){//deleted
            $display .= '<div class="text-center"><div class="col-6"><h3></h3> </div><div class="btn col-3"><a href="company_dir.php"><span>You already have directors filled in you can add more Here</span> <i class="fa fa-pencil"></i></a> </div></div>
            ';
        }else{
            $display .= 'Name : '.$director[0]["Name"].' </br>
            Surname: '.$director[0]["Surname"].' </br>
            Identification_Type: '.$director[0]["Identification_Type"].' </br>
            ID Number: '.$director[0]["ID_Number"].' </br>
            Ethinic_Group : '.$director[0]["Gender"].'</br>'
            ;
            $display .= '<div class="text-center"><div class="btn col-3"><a href="company_dir_update.php?action=update"><span>Edit Information </span> <i class="fa fa-pencil"></i></a> </div>
        <div class="btn col-3"><a href="company_dir_update.php?action=1"><span>Delete Information </span> <i class="fa fa-trash"></i></a> </div></div>
        
        ';
        }
        
        $display .= '</div>';
        return $display;
    }
    
    private function statement($statement){
        
        $display = "<div class='x_panel'>";
        $display .= '
        <h3 class="text-center">Profile Information </h3>';
        if(empty($statement) || $statement[0]["Active"] == 1 ){//deleted
            $display .= '<div class="text-center"><div class="col-6"><h3>No information filled in</h3> </div><div class="btn col-3"><a href="company_statement.php"><span>Fill Information Here</span> <i class="fa fa-pencil"></i></a> </div></div>
            ';
        }else{
            $display .= '<table class="table">';
            $display .= '<tr><td>Introduction </td><td>'.$statement[0]["introduction"].'</td></tr>
            <tr><td>Vision </td><td>'.$statement[0]["vision"].'</td></tr> 
            <tr><td>Mission </td><td>'.$statement[0]["mission"].'</td></tr>
            <tr><td>Values </td><td>'.$statement[0]["values_"].' </td></tr>
            <tr><td>Goal Objectives </td><td>'.$statement[0]["goals_objectives"].'</td></tr></table>'
            ;
            $display .= '<div class="text-center"><div class="btn col-3"><a href="company_statement_update.php?action=0"><span>Edit Information </span> <i class="fa fa-pencil"></i></a> </div>
        <div class="btn col-3"><a href="company_statement_update.php?action=1"><span>Delete Information </span> <i class="fa fa-trash"></i></a> </div></div>
        
        ';
        }
        
        $display .= '</div>';
        return $display;
    }
    
   private function document($document){
        
       ;
        $display = "<div class='x_panel'>";
        $display .= '
        <h3 class="text-center">Company documentation </h3>';
        if(empty($document) || $document[0]["Active"] == 1 ){//deleted
            $display .= '<div class="text-center"><div class="col-6"><h3>No information filled in</h3> </div><div class="btn col-3"><a href="company_documentation.php"><span>Fill Information Here</span> <i class="fa fa-pencil"></i></a> </div></div>
            ';
           
        }else{
            $display .= '<table class="table">';
            $display .= '<tr><td>No_Shareholders : </td><td>'.$document[0]["Number_Shareholders"].'</td></tr>
            <tr><td>No_Black_Shareholders:</td><td>'.$document[0]["Number_Black_Shareholders"].'</td></tr>
            <tr><td>No_White_Shareholders:</td><td> '.$document[0]["Number_White_Shareholders"].'</td></tr>
            <tr><td>N Black_Ownership_Percentage:</td><td>  '.$document[0]["Black_Ownership_Percentage"].'</td></tr>
            <tr><td>Black_Female_Percentage : </td><td>'.$document[0]["Black_Female_Percentage"].'</td></tr>
            <tr><td>White _Ownership_Percentage: </td><td>'.$document[0]["White_Ownership_Percentage"].'</td></tr>
            <tr><td>BBBEE_Status : </td><td>'.$document[0]["BBBEE_Status"].'</td></tr>
            <tr><td>Date_Of_Issue: </td><td>'.$document[0]["Date_Of_Issue"].'</td></tr>
            <tr><td>Expiry_Date: </td><td>'.$document[0]["Expiry_Date"].'</td></tr> </table>'
            ;
            $display .= '<div class="text-center"><div class="btn col-3"><a href="company_documentation_update.php?action=0"><span>Edit Information </span> <i class="fa fa-pencil"></i></a> </div>
        <div class="btn col-3"><a href="company_documentation_update.php?action=1"><span>Delete Information </span> <i class="fa fa-trash"></i></a> </div></div>
        
        ';
        }
        
        $display .= '</div>';
        return $display;
    }

    private function products($products){

        $Active = 0;
        $SMME_ID =0;

        for($i = 0; $i < count($products); $i++){

            if($products[$i]["Active"] == 1){
                $Active++;
            }
        }

        for($i = 0; $i < count($products); $i++){

            if(!empty($products[$i]["SMME_ID"])){
                $SMME_ID++;
            }
             
        }
      
      
        $display = "<div class='x_panel propic2'>";
        $display .= '
        <h3 class="text-center">Products </h3>';
        if($Active == $SMME_ID){//deleted
            $display .= '<div class="text-center"><div class="col-6"><h3>No information filled in</h3> </div><div class="btn col-3"><a href="products_services.php"><span>Fill Information Here</span> <i class="fa fa-pencil"></i></a> </div></div>
            ';
            
        }else{
        
            $display .= '<table class="table table-bordered"><thead><th></th><th>Name</th><th>Description</th><th>Price (R)</th></thead><tbody>';
            for($i = 0; $i < count($products); $i++){

                if($products[$i]["Active"] == 0){
                    $display .= '<tr> <td><img class="img_products"  src="../STORAGE/IMAGES/'.$products[$i]['image'].'" height="100" width="120"></td>
                <td>'.$products[$i]["product_name"].'</td>
            <td style="padding-left:5px !important">'.$products[$i]["product_description"].' </td>
            <td style="padding-left:5px !important">'.$products[$i]["price"].' </td>
            <td style="padding-left:5px !important"><div class="btn col-3"><a href="products_services_update.php?action='.$products[$i]["PRODUCT_ID"].'"><span>Delete </span> <i class="fa fa-trash"></i></a></div></td>
            </tr>';
                    
                }
               
               
            }
            
            $display .= '</tbody></table>';
            $display .= '<div class="text-center"><div class="btn col-3"><a href="products_services_update.php?action=0"><span>Edit Information </span> <i class="fa fa-pencil"></i></a> </div>
        <div class="btn col-3"> </div></div>
        
        ';
        }
        
        $display .= '</div>';
        return $display;
    }


    private function keywords($keywords){
        //print_r($keywords);
        $display = "";
        $display .= ' <div class="x_panel">
        <h3 class="text-center">Keywords </h3>';
        if(empty($keywords) || $keywords[0]["Active"] == 1){
            $display .= '<div class="text-center"><div class="col-6"><h3>No information filled in</h3> </div><div class="btn col-3"><a href="keywords.php"><span>Fill Information Here</span> <i class="fa fa-pencil"></i></a> </div></div>
            ';
        }else{
            for($i = 0; $i < count($keywords); $i++){
                $display .= $i + 1 .'. ' .strtoupper($keywords[$i]["keyword"]).'</br>';
            }
            $display .= '<div class="text-center"><div class="btn col-3"><a href="keywords_update.php?action=0"><span>Edit Information </span> <i class="fa fa-pencil"></i></a> </div>
                    <div class="btn col-3"><a href="keyword_update.php?action=1"><span>Delete Information </span> <i class="fa fa-trash"></i></a> </div></div>';
        }

        $display .= '</div>';
        return $display;
    }

    private function logo($link){
        
        $display = "";
        $display .= '<div class="x_panel">
        <h3 class="text-center">Business Links </h3>';
        if(empty($link)){
            $display .= '<div class="text-center"><div class="col-6"><h3>No information filled in</h3> </div><div class="btn col-3"><a href="admin_info.php"><span>Fill Information Here</span> <i class="fa fa-pencil"></i></a> </div></div>
            ';
        }else{
            $display .= '<div class="text-center"><img style="height:35% !important; width:35% !important" src="'.$link.'"></div>';
            $display .= '<div class="text-center"><div class="btn col-3"><a href="smme_logo.php"><span>Edit Logo </span> <i class="fa fa-pencil"></i></a> </div>
                    <div class="btn col-3 "><a href="business_links_update.php?action=1"><span>Delete Logo </span> <i class="fa fa-trash"></i></a> </div></div></form>';
        }
        
        $display .= '</div>';
        return $display;
        
    }
    private function links($links, $names){
        
        $display = "";
        $display .= '<div class="x_panel">
        <h3 class="text-center">Website Links </h3>';
        if(empty($links) || $links[0]["Active"] == 1){
            $display .= '<div class="text-center"><div class="col-6"><h3>No information filled in</h3> </div><div class="btn col-3"><a href="links.php"><span>Fill Information Here</span> <i class="fa fa-pencil"></i></a> </div></div>
            ';
        }else{
            $names = $this->Selectionsort($names);
            for($i = 0; $i < count($links); $i++){
                
                $display .= '<span><a href="'.$links[$i]["url"].'"><i class="'.$names[$i][0]["fav_icon_class"].'"></i></a></span>'.$links[$i]["url"].'</br>';
            }
            $display .= '<div class="text-center"><div class="btn col-3"><a href="business_links_update.php?action=0"><span>Edit Information </span> <i class="fa fa-pencil"></i></a> </div>
                    <div class="btn col-3 "><a href="business_links_update.php?action=1"><span>Delete Information </span> <i class="fa fa-trash"></i></a> </div></div>';
        }
        
        $display .= '</div>';
        return $display;
    }

    private function Selectionsort($data){
        $tempA = array();
       
        for($i = 0; $i < count($data); $i++){
            $minpos = $i;
            
            for($x = $i; $x < count($data); $x++){
                if($data[$minpos][0]["LINK_ID"] > $data[$x][0]["LINK_ID"]){
                    $minpos = $x;
                }
            }
            $tempA = $data[$i];
            $data[$i] = $data[$minpos];
            $data[$minpos] = $tempA;
            
        }
        
        return $data;
    }


    
    
}