
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 4 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Title -->
    <title>Openlinks | Contact</title>

    <!-- Favicon -->
    <link rel="icon" href="Images/fav.ico">

    <!--Font awesome-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">

    <!-- Core Stylesheet -->
    <link href="CSS/landing-page.css" rel="stylesheet">

    <!-- Responsive CSS -->
    <link href="CSS/Vendor/responsive.css" rel="stylesheet">
    <link href="CSS/contact.css" rel="stylesheet">
    <link href="CSS/spin.css" rel="stylesheet">
    
</head>

<body>

    <!-- ***** Header Area Start ***** -->
    <?php include 'inc/header.php'?>
    <!-- ***** Header Area End ***** -->

        <!-- ***** Breadcumb Area Start ***** -->
        <div class="mosh-breadcumb-area" style="background-color: #032033;">
        
      
    </div>
    <!-- ***** Breadcumb Area End ***** -->

            <!-- ***** Search Area Start ***** -->
           
        <section class="mosh-clients-area clearfix" id="searchanchor" style="padding-top:5em">
            <h2 class="text-center">Contact Us</h2><br>
            <div class="col-lg-12 col-md-12 col-sm-12 wow fadeInUp" data-wow-delay="1s" >
                <img src="Images/contactus.jpg" style="height: 45%; width:100%;" >
            </div>
            <p class="lead text-capitalize text-center">Send Us a message by filling in the form below and we will get back to you.</p>
            <br>
        </section>
        
       
     
        <section class="mosh-features-area-edited-3 " >
            <div class="container d-flex col-12"><!-- flex_container -->
                <div class=" col-lg-6 col-md-6 col-sm-6 d-flex justify-content-center" ><!-- flex_container1 -->
                    <img class="contact_img" src="Images/images.png">
                </div>
            
                <div class="col-lg-6 col-md-6 col-sm-6 "><!-- flex_container2  -->
                    <div class="subscribe-newsletter-content text-center wow fadeIn" >  
                        <form action="Main/Mainunauth.php" method="POST" class="" id="contact_form" name="contact_Form">
                            
                            <input type="text" name="tk" id="tk" value="<?php include_once('Helpers/token.php'); echo token::get_unauth("CONTACTFORMUNAUTH218621786YASC");?>" hidden>
                          
                            <div class="row">
                                <label class="col-lg-3 col-md-3 col-sm-3" style="font-weight:bold; color:#abadbe; text-align:justify">Name <span class="required" style="color:red"></span></label>
                                <input class="contact_input col-lg-8 col-md-8 col-sm-8" type="text" style="border-radius: 5px !important;" name="unauth_name" required><br>
                                <label class="col-lg-3 col-md-3 col-sm-3" style="font-weight:bold; color:#abadbe; text-align:justify" >Surname <span class="required"></span></label>
                                <input class="contact_input col-lg-8 col-md-8 col-sm-8" type="text" style="border-radius: 5px !important;" name="unauth_surname" required><br>
                                <label class="col-lg-3 col-md-3 col-sm-3" style="font-weight:bold; color:#abadbe; text-align:justify">Email <span class="required"></span></label>
                                <input class="contact_input col-lg-8 col-md-8 col-sm-8" type="email" style="border-radius: 5px !important;" name="unauth_email" required><br>
                                <label class="col-lg-3 col-md-3 col-sm-3" style="font-weight:bold; color:#abadbe; text-align:justify">Contact <span class="required"></span></label>
                                <input class="contact_input col-lg-8 col-md-8 col-sm-8" type="text" style="border-radius: 5px !important;" name="unauth_contact" required><br>
                                <label class="col-lg-3 col-md-3 col-sm-3" style="font-weight:bold; color:#abadbe; text-align:justify">Reason <span class="required"></span></label>
                                <select class="contact_input col-lg-8 col-md-8 col-sm-8" type="text" style="border-radius: 5px !important;" name="unauth_subject">
                                    <option value="I'm looking for ESD advice"> --select--</option>
                                    <option value="I'm looking for ESD advice"> I'm looking for ESD advice.</option>
                                    <option value="We need to drive enterprise and supplier development strategy into action">We need to drive enterprise and supplier development strategy into action.</option>
                                    <option value="We need to put socioeconomic development into action">We need to put socioeconomic development into action.</option>
                                    <option value="I'm looking for some ESD advice and a partner">I'm looking for some ESD advice and a partner.</option>
                                    <option value="I'm looking for a dependable supplier">I'm looking for a dependable supplier.</option>
                                    <option value="I'm looking supplier with qoutation  for my upcoming projects">I'm looking supplier with qoutation  for my upcoming projects</option>
                                    <option value="I'm in process of bringing the transformation plan into action">I'm in process of bringing the transformation plan into action.</option>
                                    <option value="I 'm Chief Procurement Officer & Procurement Manager">I 'm Chief Procurement Officer & Procurement Manager </option>
                                    <option value="I am a Procurement officer and buyer">I am a Procurement officer and buyer</option>
                                    <option value="Discover more about openlinks">Discover more about openlinks </option>
                                    <option value="I'm looking for a partner to work with as a subcontractor">I'm looking for a partner to work with as a subcontractor.</option>
                                    <option value="I'm looking for an outsourced service provider">I'm looking for an outsourced service provider.</option>
                                    <option value="For my next projects, I'm searching for a supplier that can provide a quote">For my next projects, I'm searching for a supplier that can provide a quote.</option>
                                    <option value="Other"> Other.</option>
                                </select>   
                                <br>
                                <label class="col-lg-3 col-md-3 col-sm-3"style="font-weight:bold; color:#abadbe; text-align:left">Type Your Message Here</label>
                                <textarea class="contact_input col-lg-8 col-md-8 col-sm-8" style="border-radius: 5px !important;" name="unauth_message" required>

                                </textarea><br>
                                <div class='text-center col-12' style='margin-top:1em; padding:1em; margin:auto' >
                                    <input style="background-color:#151a3c" type="submit" name="CONTACTFORM" Value="Send" class="btn text-white col-lg-4 col-md-4 col-sm-4" >
                                    <input style="background-color:#fbab05" type="reset" Value="Reset" class="btn text-white col-lg-4 col-md-4 col-sm-4">
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- <div class="d-flex justify-content-center" >
                        <h2 class="">Send Us A Whatsapp</h2>
                    </div>
                    <div class="d-flex justify-content-center">
                        <h2 style="color: #4a7aec; font-size: 3.5em;" >
                            <a href="https://wa.me/0812894652/?text=Hi, I want to find out more about OpenLinks" ><button style="background-color:#151a3c" class="btn text-white"><i class="fab fa-whatsapp"></i> Send Message</button></a> 
                        </h2>
                    </div> -->
                </div>
            </div>
        </section>

  
    <!-- ***** Search Results Start ***** -->
    <!-- <section class="mosh-clients-area section_padding_100 clearfix col-12">
        <div >
            <div class="d-flex justify-content-center" style="left:-147px;!important">
                <h2 class="">Send Us A Whatsapp</h2>
            </div>
            <div class="d-flex justify-content-end">
                <h2 style="color: #4a7aec; font-size: 3.5em;" >
                    <a href="https://wa.me/0812894652/?text=Hi, I want to find out more about OpenLinks" ><button style="background-color:#151a3c" class="btn text-white"><i class="fab fa-whatsapp"></i> Send Message</button></a> 
                </h2>
            </div>
        </div>

    </section> -->
    <!-- ***** Search Results End ***** -->

    <!-- ***** Search Area End ***** -->



    <!-- ***** Footer Area Start ***** -->
    <?php include 'inc/footer.php'?>
    <!-- ***** Footer Area End ***** -->

    <!-- jQuery-2.2.4 js -->
    <script src="Javascript/Vendor/jquery-2.2.4.min.js"></script>
    <!-- Popper js -->
    <script src="Javascript/Vendor/popper.min.js"></script>
    <!-- Bootstrap js -->
    <script src="Javascript/landing-page/bootstrap.min.js"></script>
    <!-- All Plugins js -->
    <script src="Javascript/Vendor/plugins.js"></script>
    <!-- Active js -->
    <script src="Javascript/active.js"></script>
    <!-- search js -->
    <script src="Javascript/Unauth.ajax.js"></script>

</body>

</html>