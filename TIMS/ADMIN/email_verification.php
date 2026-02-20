<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Openlinks</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <link rel="icon" href="../Images/fav.ico">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- <link href="../CSS/Vendor/pnotify.css" rel="stylesheet"> -->
    <link href="../CSS/Vendor/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../CSS/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/chat.css">
    <link href="../CSS/spin.css" rel="stylesheet">

</head>

<body>

    <body class="nav-md">
        <div class="container body">
            <div class="main_container">


                <div class="right_col" role="main">
                    <div class="">
                        <div class="page-title">
                            <div class="title_left">
                                <h3>Email Verification</h3>
                            </div>


                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
                                    <div class="x_title">

                                        <h2>
                                            <small>
                                                Your Email has been verified, you can now
                                                <span><a href="login.php">login</a></span>
                                            </small>
                                        </h2>

                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <br />
                                        <form id="email_veri_c"
                                            data-parsley-validateclass="form-horizontal form-label-left"
                                            action="../Main/Main.php" Method=" POST">


                                            <div class="ln_solid"></div>
                                            <?php $filepath = realpath(dirname(__FILE__));
                                            include_once($filepath.'/../helpers/token.php');?>
                                            <input type="text" name="tk" id="tk"
                                                value=" <?php echo token::get_unauth("EMAIL_VERIFICATION_YASC");?>"
                                                required="" hidden>


                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
        require 'inc/footer.php';
      ?>
        </div>
        </div>

        <script src="../Javascript/Vendor/jquery-3.5.1.js"></script>
        <script src="../Javascript/Vendor/bootstrap.min.js"></script>

        <script src="../Javascript/Email Verification/email_verification_a.js" defer></script>
        <script src="../Javascript/custom.js"></script>

    </body>

</html>