<?php 
  session_start();
?>
<?php include_once "inc/MessagesHeader.php"; ?>
<body onload="loadchat()">
  <div class="wrapper">
    <section class="chat-area">
      <header>
        <?php 
        include_once "Main/Main_ChatUser.php";
          $id=$_GET['url'];
          $row = getUser($id, TRUE);
          seen();
        ?>
        <a href="messages.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
        <img src="<?php echo $row['ext']; ?>" alt="">
        <div class="details">
          <span><?php if (isset($row['Legal_name'])){
            echo $row['Legal_name'];
          }else{
            echo "<b>Error </b>";
          }
           ; ?></span>
        </div>
      </header>
      <div class="successy">
      </div>
      <div class="chat-box">
      </div>
      <form action="#" class="typing-area">
        <input type="text" class="To_" name="To_" value="<?php echo $id; ?>" hidden>
        <input type="text" name="message" id="testy" class="input-field" placeholder="Type a message here..." autocomplete="off">
        <button><i class="fab fa-telegram-plane"></i></button>
      </form>
    </section>
  </div>

  <script src="javaScript/chat/chat.js"></script>

</body>
</html>
