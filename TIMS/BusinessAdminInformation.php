<?php session_start();?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <title>Business Admin Information</title>
    <link rel="stylesheet" href="styleBBBEE.css">
</head>
</head>
<body style="background-image: url(Images/background\ 2.jpg);">
<div>
    <header style="background-color:lightsteelblue; padding: -15px;" >
        <img src="Images/Admin1.png" width="120px" height="120px" style="padding:3px;">
        <button class="logInBtnNavBar" title="MENU" onclick="showSide();"style="margin:10px">MENU</button>
        <p style="top:-1px; right:400px; text-align: center;position: absolute; color: white;font-size:45px;">Business Admin Information<p>
    </header>
<section id="loginArea" class="area">
    <a href="javascript:void(0)" title="Close" class="closebtn" onclick="closeSide()">&times;</a>
    <a href="Home.html"><button class="panelBtn" title="HOME" id="B1">HOME</button></a>
    <br />
    <a href="Contact.html"><button class="panelBtn" title="Login On Employee Panel" id="B2">CONTACT US</button></a>
</section><!--Navigation-->
<section>
    <form action="Main/Main.php" method="POST" style="width: 700px;">
        <section >
            <section>
                <label for="Name">Name</label>
                <input  type="text" name="Name"><br>

                <label for="IDType">Identification Type</label>
                <input  type="text" name="IDType"><br>
                <label for="Race">Ethnic group</label>
                <input name="Race" list="Ethnic">
            <datalist>
             <option value="Black">Black</option>
             <option value="White">White</option>
             <option value="Coloured">Coloured</option>
             <option value="Indian">Indian</option>
            </datalist>
                    <label for="Email">Email address</label>
                    <input type="email" name="Email"><br>

            </section>
            <section>
                <label for="Surname">Surname</label>
                <input  type="text" name="Surname"><br>

                <label for="IDNumber">ID Number</label>
                <input  type="text" name="IDNumber"><br>

                <label for="Gender">Gender</label>
                <select name="Gender" size="1">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <!--Other not relavent-->
                </select><br/>
            </section>
        </section>
        <footer>
            <div>
                <input class="logInBtnNavBar" type="submit" value="Submit" name="COMPANYADMIN"><!--Location-->
            </div>
            <div>
                <Input class="logInBtnNavBar" type="reset" value="Clear"><!--Location-->
            </div>
        </footer>
        
    </form>
</section>



</div>
<script src="jquery-3.5.1.js"></script>
<script src="scriptBBBEE.js"></script>

</script>

</body>
</html>
