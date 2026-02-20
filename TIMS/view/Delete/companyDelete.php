<?php 

class companyDelete{

    public function registerForm(){
        $display = "";
            $display .='
        
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

          

            ';
        
        echo $display;
    }

    public function adminForm(){
        $display = "";

        $display .=
        '
        
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
            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                
                <button class="btn btn-primary" type="reset" >Reset</button>
                <button type="submit" class="btn btn-success" name="COMPANYADMINUPDATE">Submit</button>
            </div>
            </div>

        ';
        
        echo $display;
    }

    public function keywordsForm(){
        $display = "";
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
                        <div class="ln_solid"></div>
                        <div class="form-group">
                          <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            <button class="btn btn-primary" type="reset" >Cancel</button>
                            <button type="submit" class="btn btn-success" name="COMPANYKEYWORDS">Submit</button>
                          </div> 
                        </div> 
            ';

        
        echo $display;
    }

    public function linksForm(){
        $display = "";

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

            
            <button class="btn" type="button" id="addItem">Add Item</button>
            <input class="btn btn-success" type="submit" name="company_business_links" >
            ';
        
        echo $display;
    }
}
