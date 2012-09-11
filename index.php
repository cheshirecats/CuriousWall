<?php
if ((isset($_GET['_escaped_fragment_'])) && ($_GET['_escaped_fragment_'])){require_once 'noscript.php';die;}
else {require_once 'header.php';}
//die();
?>
<div id="left_container">
  <div id="left" class="box wall">
    <div class="button" id="refresh_button" style="float:right; margin-top:14px; margin-right:-1px"><i class="icon-refresh"></i>&nbsp;Refresh</div>
    <div class="button" id="prepare_post_button" style="float:right; margin-top:14px; margin-right:16px"><i class="icon-pencil"></i>&nbsp;Create</div>
    <div id="nav">
    </div>
    <div id="user_panel" style="margin-top:12px;margin-bottom:8px">
    <?php
    if ((isset($_SESSION['user_name'])) && (isset($_SESSION['permissions']))) {
      echo '<i class="icon-user"></i>&nbsp;'; theusername($_SESSION['user_name'],$_SESSION['permissions']);
        echo '<a href = "account.php?a=logout" class="hover" style="margin-left:26px"><i class="icon-signout"></i>&nbsp;Sign out</a>';}
    else {
      echo '<a href = "account.php?a=login" class="hover"><i class="icon-signin"></i>&nbsp;Sign in</a>';
      echo '<a href = "account.php?a=register" class="hover" style="margin-left: 18px"><i class="icon-trophy"></i>&nbsp;Sign up</a>';
    }
    ?>
    </div>
  </div>
</div>
<div id="mid_container">
  <div id="mid" class="box wall">
    <div class="button" id="page_bottom_button" style="float:right;margin-top:10px;margin-right:-36px;padding:4px 10px;"><i class="icon-chevron-down"></i></div>
    <div id="text_container">
    </div>
    <div id="post_container">
    <input type="text" id="post_title" style="width:100%; margin-bottom:11px;display:none" placeholder="Title"></input>
    <textarea id="post_text"  style="margin-bottom:13px" placeholder="Text"></textarea>
    <?php /* display mod tools only to mods */ if ((isset($_SESSION['permissions']) && ($_SESSION['permissions'] == 1))) { echo '<div id="stickingswitch" class="radioswitch"><input type="radio" id="nostick" class="inputsticky" name="stick" value="nostick"><label for="nostick">Unstick</label><input type="radio" id="ystick" class="inputsticky" name="stick" value="ystick"><label for="ystick">Stick</label></div>';}else {}?>
    <?php /* locking switch */ if ((isset($_SESSION['permissions']) && ($_SESSION['permissions'] == 1))) { echo '<div id="lockingswitch" class="radioswitch"><input type="radio" id="nolock" class="inputlocked" name="lock" value="nolock"><label for="nolock">Unlock</label><input type="radio" id="ylock" class="inputlocked" name="lock" value="ylock"><label for="ylock">Lock</label></div>';}else {} /**/?>
    <div class="button" id="post_button" style="display:inline"><i class="icon-flag"></i>&nbsp;Post&nbsp;</div>
    <div class="button" id="page_top_button" style="float:right;margin-top:-4px;margin-right:-36px;padding:4px 10px;"><i class="icon-chevron-up"></i></div>
    <div style="display:inline" id="post_msg"></div>
    </div>
  </div>
</div>
<script> var $IS_INDEX_PHP = true; </script>
<?php
    
  require_once 'footer.php'; 


?>
