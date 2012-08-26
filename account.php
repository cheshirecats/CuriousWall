<?php
  error_reporting(E_ALL);
  if ($_GET["a"] == 'logout') { $SHALL_LOG_OUT = true; }
  require_once 'header.php';
?>
<div style="width: 520px; margin: 100px auto; text-shadow: 0 0 1px rgba(0,0,0,0.1);">
  <div style="margin: 40px 1px; font-size:20px;">A Simple Forum</div>
  <div id="login_form">
    <input type="text" id="login_user" placeholder="Username" />
    <input type="password" id="login_pass" placeholder="Password" />
    <?php 
    if ($_GET["a"] == 'login')
    {
      echo '<span class="button" id="login_button">[Sign in]</span>';
    } 
    else if ($_GET["a"] == 'register')
    {
      echo '<span class="button" id="login_button">[Sign up]</span>';
    }
    ?>
  </div>
  <div style="margin: 20px 1px" id="message"></div>
</div>
<script> var $IS_ACCOUNT_PHP = true; </script>
<?php
  require_once 'footer.php'; 
?>