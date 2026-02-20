
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SMME PROFILE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styleBBBEE.css">
</head>

<body style="background-image: url(Images/officeback.jpg);">
<body>
<?php session_start();?>
    <section>
        <header>
            <button class="logInBtnNavBar" title="MENU" onclick="showSide();">MENU
            </button>
        </header>
        <section id="loginArea" class="area">
            <a href="javascript:void(0)" title="Close" class="closebtn" onclick="closeSide()">&times;</a>
            <a href="Home.html"><button class="panelBtn" title="HOME" id="B1">HOME</button></a>
            <br />
            <a href="Contact.html"><button class="panelBtn" title="Login On Employee Panel" id="B2">CONTACT US</button></a>
        </section><!--Navigation-->
        <header>
            <h1>COMPANY PROFILE</h1>
        </header>
        <div style="display: inline-block;">
            <div>
    <?php
    echo "<img src=".$_SESSION['ext']." style='max-width: 150px; border-radius: 100%; border: solid 1px;'>";
    ?>
    <form action="Main/Main.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="file">
                <input type="submit" name="CompUploadProfilePic">
                </form>
            </div>
            <div style="float: right;">
                <p id="users_name"></p>
                <p id="users_surname"></p>
            </div>
        </div>
        <div>
            <!--Short description of what the company offers(products/services)-->
        </div>
        <div id="flexbox">
            <div id="flex1" onclick="changePage_registration()">
                <p style="text-align: center;">Complete Registration</p>
                <img src="Images/edit_profile.png" height="150px" width="150" title="Complete Registration">
            </div>
            <div id="flex2" onclick="changePage_settings()">
                <p style="text-align: center;">Settings</p>
                <img src="Images/settings.jpg" height="150px" width="150" title="Settings">
            </div>
            <div id="flex3" onclick="changePage_Expense()">
                <p style="text-align: center;">Business Admin</p>
                <img src="Images/edit_profile.png" height="150px" width="150" title="Expense Summary">
            </div>
            <div id="flex4" onclick="changePage_account()">
                <p style="text-align: center;">Account</p>
                <img src="Images/account.png" height="150px" width="150" title="Account">
            </div>
            <div id="flex5" onclick="changePage_SMME()">
                <p style="text-align: center;">mySMME</p>
                <img src="Images/mySMME.png" height="150px" width="150" title="mySMME">
            </div>
            <a id="flex1" href="messages.php">
            Read Messages
            </a>
        </div >
        <script>
            function changePage_registration(){
                    setTimeout(function () {
                        location = 'company_registration.php'
                    }, 1000)
                }
            function changePage_settings(){
                    setTimeout(function () {
                        location = 'settings.html'
                    }, 1000)
            }
            function changePage_Expense(){
                    setTimeout(function () {
                        location = 'BusinessAdminInformation.php'
                    }, 1000)
            }
            function changePage_account(){
                    setTimeout(function () {
                        location = 'account.html'
                    }, 1000)
            }
            function changePage_SMME(){
                    setTimeout(function () {
                        location = 'mySMME.php'
                    }, 1000)
            }
           
            
        </script>

    </section>
    <script src="jquery-3.5.1.js"></script>
    <script src="scriptBBBEE.js"></script>
</body>