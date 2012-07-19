<?php
  require_once 'header.php';
?>
<div id="left_container">
  <div id="left" class="box wall">
    <div class="button" id="refresh_button" style="float:right; margin-top:14px; margin-right:-1px"><i class="icon-refresh"></i>&nbsp;刷新</div>
    <div class="button" id="prepare_post_button" style="float:right; margin-top:14px; margin-right:16px"><i class="icon-pencil"></i>&nbsp;新帖</div>
    <div id="nav">
    </div>
    <div id="user_panel" style="margin-top:12px;margin-bottom:8px">
    <?php
    if (isset($_SESSION['user_name']))
      echo '<i class="icon-user"></i>&nbsp;'.$_SESSION['user_name']
        .'<a href = "account.php?a=logout" class="hover" style="margin-left:26px"><i class="icon-signout"></i>&nbsp;登出</a>';
    else {
      echo '<a href = "account.php?a=login" class="hover"><i class="icon-signin"></i>&nbsp;登录</a>';
      echo '<a href = "account.php?a=register" class="hover" style="margin-left: 18px"><i class="icon-trophy"></i>&nbsp;注册</a>';
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
    <input type="text" id="post_title" style="width:100%; margin-bottom:11px;display:none" placeholder="请填写标题"></input>
    <textarea id="post_text"  style="margin-bottom:13px" placeholder="请填写内容"></textarea>
    <div class="button" id="post_button" style="display:inline"><i class="icon-flag"></i>&nbsp;发送&nbsp;</div>
    <div class="button" id="page_top_button" style="float:right;margin-top:-4px;margin-right:-36px;padding:4px 10px;"><i class="icon-chevron-up"></i></div>
    <div style="display:inline" id="post_msg"></div>
    </div>
  </div>
</div>
<script> var $IS_INDEX_PHP = true; </script>
<?php
  require_once 'footer.php'; 
?>