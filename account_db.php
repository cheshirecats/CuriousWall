<?php
require_once 'connect.php';

$_POST['user'] = trim($_POST['user']);
$_POST['pass'] = trim($_POST['pass']);

$pass_salt = 'just_for_demo';

if ($_POST['method'] == 'login')
{
  if(!$_POST['user'] || !$_POST['pass']) die('请填写用户名及密码。');
  
  $query = $db->prepare("SELECT user_id, user_name FROM users WHERE ((user_name LIKE ?) AND (user_pass = UNHEX(?)))");
  $query->execute(array($_POST['user'], hash('sha256', $_POST['pass'].$pass_salt)));
  $row = $query->fetch(PDO::FETCH_ASSOC);

  if(!$row['user_name'])
  {
    die('用户名或密码错误。');
  }
  
  $_SESSION['user_name']=$row['user_name'];
  $_SESSION['user_id']=$row['user_id'];
  die('登陆成功。');
}
else if ($_POST['method'] == 'register') 
{
  if(!$_POST['user'] || !$_POST['pass']) die('请填写用户名及密码。');
  if (!preg_match("/^[a-zA-Z0-9]+$/u",$_POST['user']))
  {
    die('用户名只能包括英文字母、数字。'); 
  }
  if (strlen($_POST['user']) >= 8)
  {
    die('用户名长度不能超过8字。');
  }
  
  $query = $db->prepare("SELECT user_id FROM users WHERE user_name LIKE ?");
  $query->execute(array($_POST['user']));
  if ($query->rowCount() > 0)
  {
    die('用户名已被使用。');
  }
  
  $query = $db->prepare("INSERT INTO users(user_name,user_pass) VALUES(?,UNHEX(?))");
  $query->execute(array($_POST['user'], hash('sha256', $_POST['pass'].$pass_salt)));
  if ($query->rowCount() < 1)
  {
    die('用户名已被使用。');
  }

  $_SESSION['user_id'] = $db->lastInsertId();
  $_SESSION['user_name'] = $_POST['user'];
  
  die('注册成功。<a href = "index.php">[点击此处进入首页]</a>');
}
else
{
  header('location: index.php');
}
?>