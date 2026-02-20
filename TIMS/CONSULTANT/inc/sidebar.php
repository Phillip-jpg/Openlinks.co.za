
        <div class="col-md-3 left_col">
        <div class="left_col scroll-view">
    <div class="navbar nav_title" style="border: 0;">
        <div id="con" style="display:flex;position:relative;height:98px">
                <div id="con1"
                    style="z-index:3; flex:1; position:absolute;flex:1; padding-left:12px;padding-top: 10px !important">
                    <img src="../Images/con1.png" class="rotate" style="width:80px; height:80px">
                </div>
                <div id="con2"
                    style="flex:1; position:absolute; padding:22px 12px 0px 25.5px; z-index: 4; height:100px;padding-top: 23px !important">
                    <img src="../Images/con2.png" style="width:53px;height:53px;" />
                </div>
                <div id="con3" class="site_title" style="flex:1;position:absolute;padding:25px 0px 0px 66px;">
                    <img src="../Images/con3.png" style="height:50px;width:150px;" />
                </div>
            </div>
        </div>

            <div class="clearfix"></div>
            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_pic">
              </div>
              <div class="profile_info">
              
                <!-- place a name here -->
              </div>
              <div class="clearfix"></div>
            </div>
            <!-- /menu profile quick info -->

            <br />

<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
              
                <ul class="nav side-menu">

                  <li><a href="index.php"><i class="fa fa-home"></i> Home</a></li>

                  <li><a><i class="fa fa-edit"></i> Registration <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <!-- <li><a href="company_info.php">Company Information</a></li> -->
                      <li><a href="admin_info.php">Consultant Information</a></li>
                    </ul>
                  </li>
                  <li><a href="edit.php"><i class="fa fa-edit"></i>Edit </a></li>
                  <?php $filepath = realpath(dirname(__FILE__,3));
                        include_once($filepath.'/helpers/token.php');?>
                        <input type="text" name="gctk" id="gctk" value="<?php echo token::get("GET_CONTROLLABLE_YASC");?>" required="" hidden>
                        <input type="text" name="cctk" id="cctk" value="<?php echo token::get("P_COMPANY_CONTROL_YASC");?>" required="" hidden>

                  <li><a><i class="fa fa-briefcase"></i> All Companies <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu" id="companies_list">
                      <li><a href="#">~none~</a></li>
                    </ul>
                  </li> 

                  <!-- <li><a href="search.php"><i class="fa fa-search"></i> Search </a></li> -->

                  <li><a href="notifications.php"><i class="fa fa-bell"></i> Notifications </a></li>

                  <li><a href="messages.php"><i class="fa fa-envelope"></i> Messages </a></li>

             

                </ul>

            </div>
            </div>

            <div class="sidebar-footer hidden-small">
              <a href="messages.php" data-toggle="tooltip" data-placement="top" title="Settings">
                <span class="fa fa-envelope" aria-hidden="true"></span>
              </a>
              <a href="notifications.php" data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="fa fa-bell" aria-hidden="true"></span>
              </a>
              <a href="search.php" data-toggle="tooltip" data-placement="top" title="Lock">
                <span class="fa fa-search" aria-hidden="true"></span>
              </a>
              <a href="login.php" data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
                <span class="fa fa-power-off" aria-hidden="true"></span>
              </a>
            </div>
        <!-- /menu footer buttons -->
          </div>
</div>
