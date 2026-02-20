
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
          <div class="left_col scroll-view">
    <div class="navbar nav_title" style="border: 0;">
    <img src="../Images/gaff.png" style="height:140px;width:200px;" />
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
                <h3><?php echo "<hr>";?></h3>
                <ul class="nav side-menu">

                  <li><a href="index.php"><i class="fa fa-home"></i> Home</a></li>

                  <?php
                  // if($_SESSION["WHO"] == "M_ADMIN"){
                  //   echo '<li><a><i class="fa fa-edit"></i> Admin Control  <span class="fa fa-chevron-down"></span></a>
                  //   <ul class="nav child_menu">
                  //     <li><a href="admin_info.php">Create Admin</a></li>
                  //     <li><a href="#">View Admin</a></li>
                  //     <li><a href="#">Delete Admin</a></li>
                  //   </ul>
                  // </li>';
                  // }
                  ?>
                  <!-- <li><a href="edit.php"><i class="fa fa-edit"></i> Edit Profile </a></li> 
                  -->
                <!--   <li><a><i class="fa fa-user"></i>Users <span class="fa fa-chevron-down"></span></a>-->
                <!--    <ul class="nav child_menu">-->
                <!--      <li><a href="mySMME_ALL.php?page=1">SMME</a></li>-->
                <!--      <li><a href="myBBBEE_ALL.php?page=1">COMPANY</a></li>-->
                <!--      <li><a href="consultants.php?page=1">CONSULTANTS</a></li>-->
                <!--    </ul>-->
                <!--</li> -->
                
                <!--<li class="tour-3"><a><i class="fa fa-id-card"></i>Client Management<span class="fa fa-chevron-down"></span></a>-->
                        <ul class="nav child_menu">
                            <li><a href="create_client.php?t=3">Create</a></li>
                            <li><a href="client_view.php?t=3">View</a></li>
                        </ul>
                    </li>
                <li class="tour-3"><a><i class="fa fa-id-card"></i>Market Place<span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="market_posts.php?t=3">All Posts</a></li>
                            <!--<li><a href="post_verify.php?t=3">Posts To Verify</a></li>-->
                            <li><a href="responses.php?page=1&sort=POST_ID&order=desc&number=5&t=3">Responses</a></li>
                            <li><a href="scorecard_view.php?t=3">Scorecards</a></li>
                            <!--<li><a href="criteria_view.php?t=3">Criteria</a></li>-->
                            <!--<li><a href="worktype_view.php?t=3">Work Types</a></li>-->
                            <!-- <li><a href="criteria_view.php?t=1">Post Responses</a></li> -->
                            <!-- <li><a href="myBBBEE_Chart.php">Comparative Charts</a></li> -->
                        </ul>
                    </li>
                    
                  <!--<li><a href="search.php"><i class="fa fa-search"></i> Search </a></li>-->
                  
                   <!--<li><a><i class="fa fa-user"></i>Verify<span class="fa fa-chevron-down"></span></a>-->
                   <!-- <ul class="nav child_menu">-->
                   <!--   <li><a href="verifySMME.php?page=1">SMME</a></li>-->
                   <!--   <li><a href="verifyCompany.php?page=1">COMPANY</a></li>-->
                   <!--   <li><a href="consultants.php?page=1">CONSULTANTS</a></li>-->
                   <!-- </ul>-->
                </li> 
                  

                  <!-- <li><a href="tempy.php"><i class="fa fa-bell"></i> Notifications </a></li> -->

                  <!--<li><a href="messages.php"><i class="fa fa-envelope"></i> Messages </a></li>-->

                  
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
</div>
