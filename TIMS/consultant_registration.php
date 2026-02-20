<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Registration</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="styleBBBEE.css">
    </head>
    <header>
        <h1 style="margin-top: 2%;"> consultant REGISTRATION</h1>
        <hr>
    </header>
    <body>
    <header>
    <button class="logInBtnNavBar" title="MENU" onclick="showSide();">MENU</button>
</header>
<section id="loginArea" class="area">
    <a href="javascript:void(0)" title="Close" class="closebtn" onclick="closeSide()">&times;</a>
    <button class="panelBtn" title="CONSULTANT INFORMATION" onclick="OpenPanel1()">CONSULTANT INFORMATION</button>
</section><!--Navigation-->
      <!-- consultant informaion -->
      <section class="PanelForm" id="Panel1">
      <form action="Main/Main.php" method="POST">
        <fieldset>
        <a href="javascript:void(0)" title="Close" class="closebtn" onclick="closeSideOfPanel1()">&times;</a>
            <section>
                <label for="Race">Ethnic group</label>
                <input name="Race" list="Ethnic">
            <datalist id="Ethnic">
             <option value="Black">Black</option>
             <option value="White">White</option>
             <option value="Coloured">Coloured</option>
             <option value="Indian">Indian</option>
            </datalist><br/>
                   
            </section>
            <section>
                <label for="idtype">Identification Type</label>
                <select name="idtype" size="1" onchange='CheckOther(this.value)'>
                    <option value="SA ID">SA ID</option>
                    <option value="PASSPORT">PASSPORT</option>
                    <!--Other not relavent-->
                </select><br/>
                <label for="IDNumber">ID Number</label>
                <input class="input"  type="number" name="IDNumber"  onfocus="this.className += ' inputfocus'" onblur="this.className='input'"><br>

                <label for="Gender">Gender</label>
                <select name="Gender" size="1" onchange='CheckOther(this.value)'>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <!--Other not relavent-->
                </select><br/>
            </section>
        
        <br />
        <input type="submit" name="CONSULTANTREGISTER" value="Register" class="submitBtn">
        
    </fieldset>
    </form>
    </section>
    <script src="jquery-3.5.1.js"></script>
    <script src="scriptBBBEE.js"></script>
</body>

</html>