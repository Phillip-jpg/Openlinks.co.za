<?php 

class consultantReview{

    public function display($admin, $logo){
       
        $display = '<div class="container">
        <h2>Dynamic Tabs</h2>
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#home">Admin</a></li><li><a data-toggle="tab" href="#menu2">Password</a></li>
          <li><a data-toggle="tab" href="#menu1">Profile Image</a></li>
          
        </ul>
      
        <div class="tab-content">
          <div id="home" class="tab-pane fade in active">
            
            ';

             $display .= $this->admin($admin);
            $display .= '
          </div>
          
          <div id="menu1" class="tab-pane fade">
           
          '.$this->logo($logo).'
        </div>
        
        <div id="menu2" class="tab-pane fade">
           
        '.$this->password().'
      </div>
        </div>
      </div>
      
        
        ';
        print_r($display);
        // $this->admin($admin);
        //$this->register($register);
       //$this->keywords($keywords);
        //$this->links($links, $names);
    }

    private function admin($admin){
        $display = "";
        $display .= '<div class="x_panel" ">
            <h3 class="text-center">Admin Information</h3>';
            if(empty($admin) || $admin[0]["Active"] == 1){
                $display .= '<div class="text-center"><div class="col-6"><h3>No information filled in</h3> </div><div class="btn col-3"><a href="admin_info.php"><span>Fill Information Here</span> <i class="fa fa-pencil"></i></a> </div>
                </div>';
            }else{
                $display .= '<table class="table"><tbody>';
                $display .= '<tr><td>Name  </td><td>'.$admin[0]["First_Name"].'</td></tr>
                <tr><td>Surname  </td><td>'.$admin[0]["Surname"].'</td></tr>';
                if($admin[0]["Identification_Type"] == "SA_ID"){
                    $display .= '<tr><td>ID Number</td><td>'.$admin[0]["ID_Number"].'</td></tr>';
                }else{
                    $display .= '<tr><td>Passport Number</td><td>'.$admin[0]["ID_Number"].'</td></tr>';
                }
                $display .='
                <tr><td>Email</td><td>'.$admin[0]["Email"].' </td></tr>
                <tr><td>Gender </td><td>'.$admin[0]["Gender"].'</td></tr>
                <tr><td>Ethnic Group </td><td>'.$admin[0]["Ethnic_Group"].' </td></tr></tbody></table>'
                ;

                $display .= '
                <div class="text-center"><div class="btn col-3"><a href="admin_info_update.php?action=2"><span>Edit Information </span> <i class="fa fa-pencil"></i></a> </div>
                    <div class="btn col-3"><a href="admin_info_update.php?action=1"><span>Delete Information </span> <i class="fa fa-trash"></i></a> </div></div>';


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
            $display .= '<div class="text-center"><div class="btn col-3"><a href="company_logo.php"><span>Edit Logo </span> <i class="fa fa-pencil"></i></a> </div>
                    <div class="btn col-3 "><a href="business_links_update.php?action=1"><span>Delete Logo </span> <i class="fa fa-trash"></i></a> </div></div></form>';
        }
        
        $display .= '</div>';
        return $display;
        
    }

    private function password(){
        
        $display = "";
        $display .= '<div class="align-self-center tab-content" style="display: flex; margin:auto ;">
        <div  class="tab-pane fade in active" style="width: 50vw !important; margin:auto">
            <form id="behave" action="../Main/Main.php" method="POST" >';

            $filepath = realpath(dirname(__FILE__));
            include_once($filepath.'/../../helpers/token.php');

         $display .=  '<input type="text" name="tk" value=';
        $display .=  token::get_ne("CONSULTANTPASSWORDUPDATEYASC");
        $display .= ' required hidden>

                    <h3>Reset Password</h3>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Old Password<span class="required">*</span></label>

                        
                            <input style="width:35vw" type="password" id="first-name" name="old_pwd" required="required" class="form-control col-md-7 col-xs-12">
                        
                    </div>

                      <div class="form-group">
                        
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">New Password<span class="required">*</span></label>
                            
                                <input style="width:35vw" type="password" id="first-name" name="new_pwd" required="required" class="form-control col-md-7 col-xs-12">
                                                   
                      </div>

                      <div class="form-group">
                        
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Retype password:<span class="required">*</span>
                        </label>

                        
                        <input style="width:35vw" type="password" id="first-name" name="pwd_repeat" required="required" class="form-control col-md-7 col-xs-12">
                      
                      </div>
                      <div class="form-group">
                      <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <button class="btn btn-primary" type="reset" >Cancel</button>
                                    <button name="CONSULTANTPASSWORDUPDATE" type="submit" class="btn btn-success">Submit</button>
                      </div>
                    </div>
                    
                    
            </form>
        </div>
        
        </div>';
        return $display;
        
    }


    
    
}