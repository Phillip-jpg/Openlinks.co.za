<?php 

class consultantEdit{

   

    public function adminForm($admin){
        $display = "";
        
   
  
        if(empty($admin)){
            $display .= '<h4>No information filled in yet.</h4>';
        }else{
        $display .=
        '
        <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left" action="../Main/Main.php" Method="POST">
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

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Name:<span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input style="width:35vw" type="text" id="first-name" name="Name" value="'.$admin["First_Name"].'" required="required" class="form-control col-md-7 col-xs-12">
                </div>
            </div>

            

            <div class="form-group">
                <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Surname</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input style="width:35vw" id="middle-name" value="'.$admin["Surname"].'"  name="Surname"class="form-control col-md-7 col-xs-12" type="text" >
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Identification Type</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <select style="width:35vw" class="form-control" name="IDType">
                    ';
                      if($admin["Identification_Type"] == "SA_ID"){
                        $display .= '
                        <option selected value="SA_ID">South Africa ID</option>
                          <option value="Passport">Passport</option>
                        ';
                      }else{
                        $display .= '
                          <option  value="SA_ID">South Africa ID</option>
                            <option selected value="Passport">Passport</option>
                          ';
                      }
                    $display .= '</select>
                </div>
            </div>

            <div class="form-group">
                <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">ID Number/Passport</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input style="width:35vw" id="middle-name" value="'.$admin["ID_Number"].'" class="form-control col-md-7 col-xs-12" type="text" name="IDNumber">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Ethinic Group</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    
    
                    <select  name="Race" class="form-control col-md-7 col-xs-12" style="width:35vw" id="Ethnicc1">
                    ';
                    if($admin["Ethnic_Group"] == "Black"){
                      $display .= '
                      <option selected value="Black">Black</option>
                      <option value="White">White</option>
                        <option value="Coloured">Coloured</option>
                        <option value="Indian">Indian</option>
                      ';
                  }else if($admin["Ethnic_Group"] == "White"){
                    $display .= '
                    <option value="Black">Black</option>
                    <option selected value="White">White</option>
                      <option value="Coloured">Coloured</option>
                      <option value="Indian">Indian</option>
                      ';
                  }
                  else if($admin["Ethnic_Group"] == "Coloured"){
                    $display .= '
                    <option value="Black">Black</option>
                    <option value="White">White</option>
                      <option selected value="Coloured">Coloured</option>
                      <option value="Indian">Indian</option>
                      ';
                  }
                  else{
                    $display .= '
                    <option value="Black">Black</option>
                    <option value="White">White</option>
                      <option value="Coloured">Coloured</option>
                      <option selected value="Indian">Indian</option>
                      ';
                  }
                    $display .='
                    </select><br/>
                </div>
            </div>
            
            <div class="form-group">
                <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Email</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input style="width:35vw" id="middle-name" value="'.$admin["Email"].'" class="form-control col-md-7 col-xs-12" type="text" name="Email">
                </div>
            </div>

            <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12">Gender</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
            <select style="width:35vw" class="form-control col-md-7 col-xs-12" name="Gender" size="1">
            ';
            if($admin["Gender"] == "Male"){
              $display .= '
              <option selected value="Male">Male</option>
              <option value="Female">Female</option>
              ';
          }else{
            $display .= '
            <option value="Male">Male</option>
            <option selected value="Female">Female</option>
              ';
          }
            $display .='
            </select><br/>
            </div>
            </div>

            <div class="ln_solid"></div>
            
           
            <div class="form-group">
            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                
                <button class="btn btn-primary" type="reset" >Reset</button>
                <button type="submit" class="btn btn-success" name="CONSULTANTADMINUPDATE">Submit</button>
            </div>
            </div>

        ';
        }
        echo $display;
    }



 
  
}
