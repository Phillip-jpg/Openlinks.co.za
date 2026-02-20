    <?php 

class smmeEdit{

    public function registerForm($register){
        $display = "";
        if(empty($register)){
            $display .= '<h4>No information filled in yet.</h4>';   
        }else{
            $display .='
            <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Legal Name<span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
              <input style="width:35vw" type="text" id="first-name" required="required" value="'.$register[0]["Trade_Name"].'" name="Tradename" class="form-control col-md-7 col-xs-12 formz">
            </div>
          </div> 
            
            <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Legal Name<span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
              <input style="width:35vw" type="text" id="first-name" required="required" value="'.$register[0]["Legal_name"].'" name="legalname" class="form-control col-md-7 col-xs-12 formz">
            </div>
          </div> 

          <div class="form-group">
            <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Email</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
              <input style="width:35vw" id="middle-name" value="'.$register[0]["Email"].'" class="form-control col-md-7 col-xs-12 formz" type="text" name="email">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Address<span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
              <input style="width:35vw" type="text" id="last-name" value="'.$register[0]["Address"].'" name="address" required="required" class="form-control col-md-7 col-xs-12 formz">
            </div>
          </div>
          
          <div class="form-group">
            <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Province</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
              <input style="width:35vw" id="middle-name" value="'.$register[0]["Province"].'" class="form-control col-md-7 col-xs-12 formz" type="text" name="province">
            </div>
          </div>

            <div class="form-group">
            <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">City</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
              <input style="width:35vw" id="middle-name" value="'.$register[0]["city"].'" class="form-control col-md-7 col-xs-12 formz" type="text" name="city">
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
          <button type="button" id="clicked" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal" style="display:none"></button>
      <!-----end of content---->

          <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="number">Postal Code<span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
              <input style="width:35vw" type="number" id="number" value="'.$register[0]["Post_Code"].'" name="postal" required="required" data-validate-minmax="10,100" class="form-control col-md-7 col-xs-12 formz">
            </div>
          </div>

          <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="number">CC Registration<span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
              <input style="width:35vw" type="number" id="number" value="'.$register[0]["CC_Registration_Number"].'" name="regnum" required="required" data-validate-minmax="10,100" class="form-control col-md-7 col-xs-12 formz">
            </div>
          </div>

          <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="number">Company contact<span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
              <input style="width:35vw" type="number" id="number" value="'.$register[0]["Contact"].'" name="contact" required="required" data-validate-minmax="10,100" class="form-control col-md-7 col-xs-12 formz">
            </div>
          </div>

          <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="offices">Business Offices<span class="required">*</span></label>

            <div class="col-md-6 col-sm-6 col-xs-12">
                <select style="width:35vw" name="offices" class="form-control col-md-7 col-xs-12 formz"  id="offices" required>
                  <option value="" disabled selected>Select your specific office</option>
                  <option value="1">Office of Life Sciences</option>
                  <option value="2">Office of Energy & Transportation</option>
                  <option value="3">Office of Real Estate & Construction</option>
                  <option value="4">Office of Manufacturing</option>
                  <option value="5">Office of Technology</option>
                  <option value="6">Office of Trade & Services</option>
                  <option value="7">Office of Finance</option>
                  <option value="8">Office of Structured Fincance</option>
                  <option value="9">Office of International Corporate Finance</option>
                </select>
            </div>
          </div>

          <div class="item form-group">
           <label class="control-label col-md-3 col-sm-3 col-xs-12" for="industries">Business Industries<span class="required">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12">
              <select style="width:35vw"  class="form-control col-md-7 col-xs-12 formz" name="industries" value="'.$register[0]["INDUSTRY_ID"].'" id="industries" disabled required></select>          
            </div>
          </div>
          <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="offices">Form of ownership<span class="required">*</span></label>
           
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <select style="width:35vw"  class="form-control col-md-7 col-xs-12 formz" value="'.$register[0]["foo"].'"  name="foo" value required>
                            <option value="Public">Public </option>
                              <option value="State Owned">State Owned</option>
                              <option value="Private">Private</option>
                              <option value="Incorporated/ limited Liability">Incorporated/ limited Liability</option>
                            </select>
                        </div>
                      </div>

                      <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="number">Financial Year Start<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input style="width:35vw" type="date" id="number" name="financial" required="required" data-validate-minmax="10,100" value="'.$register[0]["Financial_Year"].'"  class="form-control col-md-7 col-xs-12 formz">
                        </div>
                      </div>

          <div class="ln_solid"></div>

            <div class="form-group">
            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
              <button class="btn btn-primary" type="reset">Cancel</button>
              <button name="SMMEREGISTERUPDATE" type="submit" class="btn btn-success">Submit</button>
            </div>
          </div>

            ';
        }
        echo $display;
    }

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
                    <input style="width:35vw" type="text" id="first-name" name="Name" value="'.$admin[0]["first_name"].'" required="required" class="form-control col-md-7 col-xs-12 formz">
                </div>
            </div>

            

            <div class="form-group">
                <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Surname</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input style="width:35vw" id="middle-name" value="'.$admin[0]["Surname"].'"  required="required" name="Surname"class="form-control col-md-7 col-xs-12 formz" type="text" >
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Identification Type</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <select style="width:35vw" class="form-control formz" name="IDType">
                        <option value="SA_ID">South Africa ID</option>
                        <option value="Passport">Passport</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">ID Number/Passport</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input style="width:35vw" required="required" id="middle-name" value="'.$admin[0]["ID_Number"].'" class="form-control col-md-7 col-xs-12 formz" type="text" name="IDNumber">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Ethinic Group</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input style="width:35vw" name="Race" list="Ethnicc1" required="required" class="form-control col-md-7 col-xs-12 formz">
    
                    <datalist style="width:35vw" required="required" id="Ethnicc1">
                    <option value="Black">Black</option>
                    <option value="White">White</option>
                    <option value="Coloured">Coloured</option>
                    <option value="Indian">Indian</option>
                    </datalist><br/>
                </div>
            </div>
            
            <div class="form-group">
                <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Email</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input style="width:35vw" required="required" id="middle-name" value="'.$admin[0]["Email"].'" class="form-control col-md-7 col-xs-12 formz" type="text" name="Email">
                </div>
            </div>

            <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12">Gender</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
            <select required="required" style="width:35vw" class="form-control col-md-7 col-xs-12 formz" name="Gender" size="1">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select><br/>
            </div>
            </div>

            <div class="ln_solid"></div>
            
           
            <div class="form-group">
            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                
                <button class="btn btn-primary" type="reset" >Reset</button>
                <button type="submit" class="btn btn-success" name="SMMEADMINUPDATE">Submit</button>
            </div>
            </div>

        ';
        }
        echo $display;
    }
    public function Director($director){

        $display = "";
        if(empty($director)){
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
                    <input style="width:35vw" type="text" id="first-name" name="Name" value="'.$director[0]["Name"].'" required="required" class="form-control col-md-7 col-xs-12 formz">
                </div>
            </div>

            

            <div class="form-group">
                <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Surname</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input style="width:35vw" id="middle-name" value="'.$director[0]["Surname"].'"  name="Surname"class="form-control col-md-7 col-xs-12 formz" type="text" >
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Identification Type</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <select style="width:35vw" class="form-control formz" value="'.$director[0]["Identification_Type"].'" name="IDType">
                        <option value="SA_ID">South Africa ID</option>
                        <option value="Passport">Passport</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">ID Number/Passport</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input style="width:35vw" id="middle-name" value="'.$director[0]["ID_Number"].'" class="form-control col-md-7 col-xs-12 formz" type="text" name="IDNumber">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Ethinic Group</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input style="width:35vw" name="Race" list="Ethnicc1" value="'.$director[0]["Ethnic_Group"].'" class="form-control col-md-7 col-xs-12 formz">
    
                    <datalist style="width:35vw" id="Ethnicc1">
                    <option value="Black">Black</option>
                    <option value="White">White</option>
                    <option value="Coloured">Coloured</option>
                    <option value="Indian">Indian</option>
                    </datalist><br/>
                </div>
            </div>

            <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12">Gender</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
            <select style="width:35vw" class="form-control col-md-7 col-xs-12 formz" value="'.$director[0]["Gender"].'" name="Gender" size="1">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select><br/>
            </div>
            </div>

            <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" id="inputGroupFileAddon01" for="number">Upload ID/Passport<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12 custom-file">
                          <input style="border-style: none;" type="file" style="width:35vw" name="IDcopy" class="form-control col-md-7 col-xs-12 formz custom-file-input" id="inputGroupFile01" >
                        </div>
                      </div>

            <div class="ln_solid"></div>
            
           
            <div class="form-group">
            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                
                <button class="btn btn-primary" type="reset" >Reset</button>
                <button type="submit" class="btn btn-success" name="SMMEDIRECTORUPDATE">Submit</button>
            </div>
            </div>

        ';
        }
        echo $display;
    }

    public function Statement($statement){

        $display = "";
        if(empty($statement)){
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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Introduction...<span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input style="width:35vw" type="text" id="first-name" name="Introduction" value="'.$statement[0]["introduction"].'" required="required" class="form-control col-md-7 col-xs-12 formz">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Vision...<span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input style="width:35vw" type="text" id="first-name" name="Vision" value="'.$statement[0]["vision"].'" required="required" class="form-control col-md-7 col-xs-12 formz">
                </div>
            </div>
             <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Mission...<span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input style="width:35vw" type="text" id="first-name" name="Mission" value="'.$statement[0]["mission"].'" required="required" class="form-control col-md-7 col-xs-12 formz">
                </div>
            </div>
            
          
            <div class="form-group">
                <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Values...*</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input style="width:35vw" id="middle-name" value="'.$statement[0]["values_"].'"  name="Values"class="form-control col-md-7 col-xs-12 formz" type="text" >
                </div>
            </div>

            <div class="form-group">
            <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Goals & Objectives...*</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <input style="width:35vw" id="middle-name" value="'.$statement[0]["goals_objectives"].'"  name="Goals_Objectives"class="form-control col-md-7 col-xs-12 formz" type="text" >
            </div>
        </div>

        <div class="ln_solid"></div>
            
           
        <div class="form-group">
        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
            
            <button class="btn btn-primary" type="reset" >Reset</button>
            <button type="submit" class="btn btn-success" name="SMMESTATEMENTUPDATE">Submit</button>
        </div>
        </div>
        ';
        }
        echo $display;
    }


    public function documentation($documentation){

        $display = "";
        if(empty($documentation)){
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

        <div class="item form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="number">Total Number of Shareholders:<span class="required">*</span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="number" id="number" name="TotalNoShareholders" value="'.$documentation[0]["Number_Shareholders"].'" required="required" data-validate-minmax="10,100" class="form-control col-md-7 col-xs-12 formz">
        </div>
      </div>
      <div class="item form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="number">Total Number of White shareholders:<span class="required">*</span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="number" id="number" name="WhiteShareholders" value="'.$documentation[0]["Number_White_Shareholders"].'" required="required" data-validate-minmax="10,100" class="form-control col-md-7 col-xs-12 formz">
        </div>
      </div>
      <div class="item form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="number">Total Number of Black shareholders:<span class="required">*</span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="number" id="number" name="NoBlackShareholders" value="'.$documentation[0]["Number_Black_Shareholders"].'" required="required" data-validate-minmax="10,100" class="form-control col-md-7 col-xs-12 formz">
        </div>
      </div>

      
      <div class="item form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="number">White Ownership Percentage<span class="required">*</span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="number" id="white_ownership_percentage" value="'.$documentation[0]["Number_White_Shareholders"].'" name="WhiteOwnershipP" required="required" data-validate-minmax="10,100" class="form-control col-md-7 col-xs-12 formz">
        </div>
      </div>
      <div class="item form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="number">Black Ownership Percentage<span class="required">*</span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="number" id="black_ownership_percentage" value="'.$documentation[0]["Black_Ownership_Percentage"].'" name="BlackOwnershipP" required="required" data-validate-minmax="10,100" class="form-control col-md-7 col-xs- formz">
        </div>
      </div>
      <div class="item form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="number">Black Female Percentage<span class="required">*</span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="number" id="black_female_percentage" name="BlackFemaleP" value="'.$documentation[0]["Black_Female_Percentage"].'" required="required" data-validate-minmax="10,100" class="form-control col-md-7 col-xs-12 formz">
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
<button type="button" id="clicked" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal" style="display:none"></button>
<!-----end of content---->
      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">B-BBE Status<span class="required">*</span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="text" id="last-name" name="BBBEEStatus" value="'.$documentation[0]["BBBEE_Status"].'" required="required" class="form-control col-md-7 col-xs-12 formz">
        </div>
      </div>

        <div class="item form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="number">Black Female Ownership Percentage<span class="required">*</span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="number" id="number" name="BlackOwnershipP" value="'.$documentation[0]["Black_Female_Percentage"].'" required="required" data-validate-minmax="10,100" class="form-control col-md-7 col-xs-12 formz">
        </div>
      </div>
      

      <div class="item form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="number">Date of Isuue<span class="required">*</span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="date" id="number" name="DOI" value="'.$documentation[0]["Date_Of_Issue"].'" required="required" data-validate-minmax="10,100" class="form-control col-md-7 col-xs-12 formz">
        </div>
      </div>

      <div class="item form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="number">Date of expirng<span class="required">*</span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input style="width:35vw" type="date" id="number" name="ED" value="'.$documentation[0]["Expiry_Date"].'"required="required" data-validate-minmax="10,100" class="form-control col-md-7 col-xs-12 formz">
        </div>
      </div>

      

                     <div class="form-group">
        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
            
            <button class="btn btn-primary" type="reset" >Reset</button>
            <button type="submit" class="btn btn-success" name="SMMEDOCUPDATE">Submit</button>
        </div>
        </div>

        ';
        }
        echo $display;
    }

   public function products($products){

        $display = "";
        if(empty($products)){
            $display .= '<h4>No information filled in yet.</h4>';
            
        }else{
        $display .=
        ' <table>
        
                    <tbody id="tbody">';

                  for($i=0; $i<count($products); $i++){

                    if($products[$i]["Active"] == 0){

                      $display .=
                    '<tr>
                        <td id="move">
                            <input required="required"  value="'.$products[$i]["product_name"].'" class="form-control col-md-7 col-xs-12" type="text" name="productname[]" placeholder="Enter Product name here...">
                        </td>
                        <td >
                            <input required="required"  value="'.$products[$i]["product_description"].'"  class="form-control col-md-7 col-xs-12" type="text" name="productdes[]" placeholder="Enter product description here...">
                        </td>
                        <td >
                            <input required="required" style="width:150px ! important"  value="'.$products[$i]["price"].'"  class="form-control col-md-7 col-xs-12" type="text" name="productprice[]" placeholder="Enter product price here...">
                        </td>
                    </tr>
                    <input style="display:none !important;" class="form-control col-md-7 col-xs-12" type="text" value="'.$products[$i]["PRODUCT_ID"].'" name="productIDS[]" >
                    ';


                    }

                    

                  }
                  $display .='
                    </tbody>
                </table>
                <br>
                <input class="btn btn-success" type="submit" name="SMMEPRODUCTUPDATE" >
            
        ';
        }
        echo $display;
    }

    public function keywordsForm($keywords){
        $display = "";
        if(empty($keywords)){
            $display .= '<h4>No information filled in yet.</h4>';
        }else{
            $display .= '
            <div class="control-group row">
                            <label class="control-label col-md-3 col-sm-3 ">Enter 4 Keywords</label>
                            <div class="col-md-9 col-sm-9 ">
                                <input id="tags_1" type="text" class="tags form-control" value="';
                                for($i = 0; $i < count($keywords); $i++){
                                  $display .= $keywords[$i]["keyword"].',';
                                }
                                $display .='" name="keywords"/>
                                <input style="display:none" id="tags_1" type="text" class="tags form-control" value="';
                                for($i = 0; $i < count($keywords); $i++){
                                  $display .= $keywords[$i]["KEYWORD_ID"].',';
                                }
                                $display .='" name="ids" />
                                <div id="suggestions-container" style="position: relative; float: left; width: 250px; margin: 10px;">
                                  eg. Laptops ,Farming...</div>
                                </div>
                              </div>
                    
                        <div class="ln_solid"></div>
                        <div class="form-group">
                          <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            <button class="btn btn-primary" type="reset" >Cancel</button>
                            <button type="submit" class="btn btn-success" name="SMMEKEYWORDUPDATE">Submit</button>
                          </div> 
                        </div> 
            ';

        }
        echo $display;
    }

    public function linksForm($links){
        $display = "";
        if(empty($links)){
            $display .= '<h4>No information filled in yet.</h4>';
        }else{
            $display .= '
            
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
            <button type="button" id="clicked" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal" style="display:none"></button>
    <!-----end of content---->

            <table>
                <tbody id="tbody">
                    ';
                        for($i = 0; $i < count($links); $i++){
                          $display .= '
                          <tr>
                          <td id="move">
                          <input class="form-control col-md-7 col-xs-12" type="text" value="'.$links[$i]["url"].'" name="links[]" placeholder="Enter you business links here...">
                      </td>
                      <td id="move">
                          <select class="form-control col-lg-1 col-md-7 col-xs-12" name="ids[]">
                              <option value="1">WhatsApp</option>
                              <option value="2">Facebook</option>
                              <option value="4">Website</option>
                              <option value="3">LinkedIn</option>
                              <option value="5">Twitter</option>
                          </select>
                      </td>
                      </tr>';
                        }
                    $display .='
                </tbody>
            </table>
            <br>
            
            <button class="btn" type="button" id="addItem">Add Item</button>
            <input class="btn btn-success" type="submit" name="smme_business_links" >
            ';
        }
        echo $display;
    }
}
