<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> Company Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styleBBBEE.css">
</head>
<header>
    <h1 style="margin-top: 2%;"> company REGISTRATION</h1>
    <hr>
</header>
<body>
    <header>
        <button class="logInBtnNavBar" title="MENU" onclick="showSide();">MENU</button>
    </header>
    <section id="loginArea" class="area">
        <a href="javascript:void(0)" title="Close" class="closebtn" onclick="closeSide()">&times;</a>
        <button class="panelBtn" title="COMPANY INFORMATION" onclick="OpenPanel1()">COMPANY INFORMATION</button>
        <br />
        <button class="panelBtn" title="COMPANY STATEMENTS" id="B3" onclick="OpenPanel2()">COMPANY STATEMENTS</button>
       
    </section><!--Navigation-->
    <!--Company Information-->
    <section class="PanelForm" id="Panel1">
    <a href="javascript:void(0)" title="Close" class="closebtn" onclick="closeSideOfPanel1()">&times;</a>
    <form action="Main/Main.php" method="POST">
        <fieldset>
            <div>
                <label for="name">Legal Name</label>
                <input class="input" name="legalname" onfocus="this.className += ' inputfocus'" onblur="this.className='input'" type="text" size="40" width="100" id="name" />
                <br />
                <label for="sur">Address</label>
                <input class="input" name="address" onfocus="this.className += ' inputfocus'" onblur="this.className='input'" type="text" size="40" width="100" id="sur" />
                <br/>
                
                <label for="age">Province</label>
                <input class="input" name="province" onfocus="this.className += ' inputfocus'" onblur="this.className='input'" type="text" size="40" width="100" id="age" />
                <br />
                
                <label for="gen">Company Email</label>
                <input class="input" name="email" onfocus="this.className += ' inputfocus'" onblur="this.className='input'" type="text" size="40" width="100" id="gen" />
            </div>
            <div>
                <label for="name">CC Registration</label>
                <input class="input" name="regnum"  onfocus="this.className += ' inputfocus'" onblur="this.className='input'" type="number" size="40" width="100" id="name" />
                <br />
                <label for="sur">Post Code:</label>
                <input class="input" name="postal"  onfocus="this.className += ' inputfocus'" onblur="this.className='input'" type="number" size="40" width="100" id="sur" />
                <br />
                <label for="age">City</label>
                <input class="input" name="city"  onfocus="this.className += ' inputfocus'" onblur="this.className='input'" type="text" size="40" width="100" id="age" />
                <br />
                <label for="gen">Company contact</label>
                <input class="input" name="contact" onfocus="this.className += ' inputfocus'" onblur="this.className='input'" type="number" size="40" width="100" id="gen" />
            </div>
            <label for="offices">Business Offices</label>
                    <select name="offices" id="offices" required>
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
                    <label for="industries">Business Industries</label>
                    <select name="industries" id="industries" disabled required>

                    </select>
            <input type="submit" value="Register" name="COMPANYREGISTER" class="submitBtn">
          </fieldset>
        
    </form>
    </section>
    <section class="PanelForm" id="Panel2">
    <a href="javascript:void(0)" title="Close" class="closebtn" onclick="closeSideOfPanel2()">&times;</a>
        <form>
            <fieldset>
                <textarea placeholder="Vision..." cols="100" rows="10"></textarea><br>
                <textarea placeholder="Mission..." cols="100" rows="10"></textarea><br>
                <textarea placeholder="Values..." cols="100" rows="10"></textarea><br>
                <textarea placeholder="Goals & Objectives..." cols="100" rows="10"></textarea><br>
                <textarea placeholder="Products & Services" cols="100" rows="10"></textarea>
            </fieldset>
        </form>
    </section>
    <script src="jquery-3.5.1.js"></script>
    <script src="scriptBBBEE.js"></script>
    <script src="Javascript/offices.ajax.js"></script>
</body>
</html>