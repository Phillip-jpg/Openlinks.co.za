<?php
include_once('Helpers/token.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 4 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Title -->
    <title>Openlinks | Article</title>

    <!-- Favicon -->
    <link rel="icon" href="/../Images/fav.ico">

    <!--Font awesome-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">

    <!-- Core Stylesheet -->
    <link href="/../CSS/landing-page.css" rel="stylesheet">

    <!-- Responsive CSS -->
    <link href="/../CSS/Vendor/responsive.css" rel="stylesheet">

    <link href="/../CSS/spin.css" rel="stylesheet">
    
    <link rel="stylesheet" href="/../CSS/Articy.css">

    <link rel="stylesheet" type="text/css" href="/../CSS/splide.min.css">
<!-- <link rel="stylesheet" href="/CSS/bootstrap.min.css"> -->
</head>
    
<body>
    <!-- ***** Header Area Start ***** -->
   <header class="header_area clearfix">
    <div class="container-fluid h-100">
        <div class="row h-100">
            <!-- Menu Area Start -->
            <div class="col-12 h-100">
                <div class="menu_area h-100">
     
                <div href=""><div style="display:flex;position:relative; width: 200px; margin-top:-13px;">
                                <div id="con1"
                                    style="z-index:3; flex:1; position:absolute;flex:1; padding-left:12px;padding-top: 15px !important">
                                    <img src="/../Images/con1.png" class="rotate"
                                        style="width:80px; height:80px">
                                </div>
                                <div id="con2"
                                    style="flex:1; position:absolute; padding:29px 12px 0px 25.5px; z-index: 4; height:100px;padding-top: 27px !important">
                                    <img src="/../Images/con2.png" style="width:53px;height:53px;" />
                                </div>
                                <div id="con3" style="flex:1;position:absolute;padding:30px 0px 0px 73px;z-index: 2;">
                                    <img src="/../Images/con3.png" style="height:52px;width:155px;" />
                                </div>
                            </div>
                            <br>
                            <i class="fa fa-bars bars" data-toggle="collapse" data-target="#mosh-navbar" style="float:right; color:white; font-size:20px; margin-top:12px" aria-controls="mosh-navbar" aria-expanded="false" aria-label="Toggle navigation"><span 
                                ></span></i>
                            <!-- <button class="navbar-toggler float-right"  data-toggle="collapse" data-target="#mosh-navbar" style="background:transparent; height:40px;" 
                            aria-controls="mosh-navbar" aria-expanded="false" aria-label="Toggle navigation"><span style="background-color: transparent;"
                                class="navbar-toggler-icon"></span></button> -->
                        </div>
                    <nav class="navbar h-100 navbar-expand-lg align-items-center">
                        <!-- Logo -->
                            

                        <!-- Menu Area -->
                        

                        <div class="collapse navbar-collapse justify-content-end new" id="mosh-navbar">
                            <ul class="navbar-nav animated" id="nav">
                                <li class="nav-item"><a class="nav-link word" href="/../index.php">Home</a></li>
                                <li class="nav-item"><a class="nav-link word" style="white-space: nowrap;"href="/../company.php">Company</a></li>
                                <li class="nav-item"><a class="nav-link word" href="/../solution.php">Our Solution</a></li>
                                <li class="nav-item"><a class="nav-link word" style="white-space: nowrap;"href="/../technology.php">Our Technology</a></li>
                                <li class="nav-item"><a class="nav-link word" style="white-space: nowrap;"href="/../index.php#Articles">Articles</a></li>
                                <li class="nav-item"><a class="nav-link word" style="white-space: nowrap;"href="/../contact.php">Contact Us</a></li>
                                <div class="login-register-btn">

                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="moshDropdown2" role="button"
                                        data-toggle="dropdown" style="white-space: nowrap; color:aqua" aria-haspopup="true"
                                        aria-expanded="false">Login</a>
                                    <div class="dropdown-menu" aria-labelledby="moshDropdown2">
                                        <a href="/../TIMS/SMME/login.php" class="dropdown-item">SMME</a>
                                        <a href="/../TIMS/COMPANY/login.php" class="dropdown-item">Company</a>
                                        <a href="/../TIMS/CONSULTANT/login.php" class="dropdown-item">Consultant</a>
                                    </div>
                                </li>
                            </div>
                 </ul>

                          
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
    <!-- ***** Header Area End ***** -->
    <div class="mosh-breadcumb-area" style="background-color: #032033;">
        
      
        </div>
    <section class="section_padding_100 clearfix" >
        <div class="container">
            
                <div class="parent">
                    <?php
                        include_once("Helpers/article.class.php");
                        $temp= new article();
                        $article=$temp->get($_GET['id']);
                        include_once("Helpers/view.class.php");
                        echo view::header($article['img'], $article['date_published'], $article['author'], $article['heading']);
                        echo view::article($article['article']);
                        $articles=$temp->getnext($_GET['id']);
                    ?>

                </div>
                <h1 id="padding"> Read More </h1>
            <div class="articles_page"><?php if(isset($articles))echo view::next($articles); ?></div>
        
        </div>
    </section>
    

        <!-- ***** Footer Area Start ***** -->
    <footer class="footer-area clearfix">
        <!-- Top Fotter Area -->
        <div class="top-footer-area section_padding_100_0">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-sm-6 col-lg-3 footer_logo" style="left:-47px;!important">
                        <div class="single-footer-widget mb-100">
                            <div style="display:flex; position:relative;">
                                <div id="con1"
                                    style="z-index:3; flex:1; position:absolute;flex:1; padding-left:12px;padding-top: 15px !important">
                                    <img src="/../Images/con1.png" class="rotate"
                                        style="width:80px; height:80px">
                                </div>
                                <div id="con2"
                                    style="flex:1; position:absolute; padding:29px 12px 0px 25.5px; z-index: 4; height:100px;padding-top: 27px !important">
                                    <img src="/../Images/con2.png" style="width:53px;height:53px;" />
                                </div>
                                <div id="con3" style="flex:1;position:absolute;padding:30px 0px 0px 69px;z-index: 2;">
                                    <img src="/../Images/con3.png" style="height:50px;width:150px;" />
                                </div>
                            </div>
                            <!-- <a href="#" class="mb-50 d-block"><img src="Images/logo.png" style="width: 90%;" alt=""></a> -->
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="single-footer-widget mb-100">
                            <h5>Fast links</h5>
                            <ul>
                                <li><a href="/../index.php">Home</a></li>
                                <li><a href="/../company.php">Company</a></li>
                                <li><a href="/../solution.php">Our Solution</a></li>
                                <li><a href="/../technology.php">Our Technology</a></li>
                                <li><a href="/../index.php#Articles">Articles</a></li>
                                <li><a href="/../contact.php">Contact Us</a></li>
                            </ul>

                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="single-footer-widget mb-100">
                            <h5>Login</h5>
                            <ul>
                                <li><a href="/../TIMS/SMME/login.php">SMME</a></li>
                                <li><a href="/../TIMS/COMPANY/login.php">Company</a></li>
                                <li><a href="/../TIMS/CONSULTANT/login.php">Consultant</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="single-footer-widget mb-100">
                            <h5>Contact Info</h5>
                            <div class="footer-single-contact-info d-flex">
                                <div class="contact-icon">
                                    <img src="/../Images/landing-page/core-img/map.png" alt="">
                                </div>
                                <p>Gqeberha, Eastern Cape</p>
                            </div>
                            <div class="footer-single-contact-info d-flex">
                                <div class="contact-icon">
                                    <img src="/../Images/landing-page/core-img/call.png" alt="">
                                </div>
                                <p><a href="tel:0679357717" style="color:#abadbe !important;">067 935 7717</a></p>
                            </div>
                            <div class="footer-single-contact-info d-flex">
                                <div class="contact-icon">
                                    <img src="/../Images/landing-page/core-img/message.png" alt="">
                                </div>
                                <p><a href="mailto: info@openlinks.co.za" style="color:#abadbe !important;">info@openlinks.co.za</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fotter Bottom Area -->
        <div class="footer-bottom-area">
            <div class="container h-100">
                <div class="row h-100">
                    <div class="col-12 h-100">
                        <div class="footer-bottom-content h-100 d-md-flex justify-content-between align-items-center">
                            <div class="copyright-text">
                                <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved: <a href="https://openlinks.co.za/" target="_blank">OpenLinks</a>
<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
</p>
                            </div>
                            <div class="footer-social-info">

                                <a href="support@openlinks.co.za" title="Support" data-toggle="tooltip" aria-hidden="true"><i class="fa fa-life-ring"></i></a>
                                <a href="info@openlinks.co.za" title="Info" data-toggle="tooltip" aria-hidden="true"><i class="fa fa-info"></i></a>
                                <a href="https://wa.me/0812894652" title="Whatsapp" data-toggle="tooltip" aria-hidden="true"><i class="fab fa-whatsapp"></i></a>
                                <a href="https://www.facebook.com/OpenLinksSA" title="Facebook" data-toggle="tooltip" aria-hidden="true"><i class="fab fa-facebook-f"></i></a>
                                <a href="https://twitter.com/OpenLinksSA" title="Twitter" data-toggle="tooltip" aria-hidden="true"><i class="fab fa-twitter"></i></a>
                                <a href="https://www.linkedin.com/company/open-links-sa/about/?viewAsMember=true" data-toggle="tooltip" aria-hidden="true" title="Linkedin"><i class="fab fa-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- ***** Footer Area End ***** -->
    
    <!-- Popper js -->
    <script src="/../Javascript/Vendor/popper.min.js"></script>
    <!-- Bootstrap js -->
    <script src="/../Javascript/landing-page/bootstrap.min.js"></script>
    <!-- All Plugins js -->
    <script src="/../Javascript/Vendor/plugins.js"></script>
    <!-- Active js -->
    <script src="/../Javascript/active.js"></script>

        <script src="/../Javascript/jquery-3.5.1.js"></script>
        <script src="/../Javascript/splide.min.js"></script>
        <script src="/../Javascript/bootstrap.bundle.min.js"></script>
        <script src="/../Javascript/article.js"></script>
</body>
</html>