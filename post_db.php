<?php
require_once 'connect.php';
require_once 'post_func.php';

if (isset($_POST['title'])) $_POST['title'] = trim($_POST['title']);
if (isset($_POST['text'])) $_POST['text'] = trim($_POST['text']);

if ($_POST['method'] == 'get')
{
  if (!isset($_POST['topic'])) $_POST['topic'] = '';
  if (!isset($_POST['begin'])) $_POST['begin'] = '';
  post_get($db, $_POST['topic'], $_POST['begin']); die();
}
else if (($_POST['method'] == 'new') && is_numeric($_POST['topic']))
{
  if (!isset($_SESSION['user_id'])) die('Sign in first.');
  if ($_POST['topic'] < 0)
  {
    if (!$_POST['title'])
    {
      die('Fill in title.');
    }
    $title_len = mb_strlen($_POST['title'],'UTF8');
    if (($title_len < 1) || ($title_len > 200))
    {
      die('Your title can\'t be longer than 200 characters. Right now your title has '.$title_len .'.');
    }
    $_POST['title'] = nl2br(htmlspecialchars($_POST['title']));
  }
  if (!$_POST['text'])
  {
    die('Fill in content.');
  }
  $text_len = mb_strlen($_POST['text'],'UTF8');
  if (($text_len < 1) || ($text_len > 5000))
  {
    die('Your post can\'t be longer than 5000 characters. Right now your post has '.$text_len .'.');
  }

  if (isset($_POST['stick']) || isset($_POST['lock'])) {
     if  ((!isset($_SESSION['permissions']) || ($_SESSION['permissions'] == 0))) {die('You need to be a moderator to play with sticky or locked topics!');}
     else {}
  }

  $_POST['text'] = nl2br(htmlspecialchars($_POST['text']));
  $_POST['text'] = str_replace("  ", "&nbsp;&nbsp;", $_POST['text']);

  if (isset($_POST['stick']) && $_POST['stick'] == 'ystick') {$_POST['stick'] = '1';} elseif (isset($_POST['stick'])) {$_POST['stick'] = '0';} else{}
  if (isset($_POST['lock']) && $_POST['lock'] == 'ylock') {$_POST['lock'] = '1';} elseif (isset($_POST['lock'])) {$_POST['lock'] = '0';} else{}

  if ($_POST['topic'] < 0)
  {
    if (isset($_POST['stick']) && isset($_POST['lock'])) {
        $query = $db->prepare("INSERT INTO topics(topic_title,topic_text,topic_date,topic_by,topic_score,sticky,locked) VALUES(?,?,NOW(),?,UNIX_TIMESTAMP((NOW())),?,?)");
        $query->execute(array($_POST['title'], $_POST['text'], $_SESSION['user_id'],$_POST['stick'],$_POST['lock']));
         if($query->rowCount() < 1)
         {
           die('Cannot create topic.');
         }
         $_POST['topic'] = $db->lastInsertId();
    }
    elseif (isset($_POST['stick']) && !isset($_POST['lock'])) {
        $query = $db->prepare("INSERT INTO topics(topic_title,topic_text,topic_date,topic_by,topic_score,sticky) VALUES(?,?,NOW(),?,UNIX_TIMESTAMP((NOW())),?)");
        $query->execute(array($_POST['title'], $_POST['text'], $_SESSION['user_id'],$_POST['stick']));
         if($query->rowCount() < 1)
         {
           die('Cannot create topic.');
         }
         $_POST['topic'] = $db->lastInsertId();
    }
    elseif (!isset($_POST['stick']) && isset($_POST['lock'])) {
        $query = $db->prepare("INSERT INTO topics(topic_title,topic_text,topic_date,topic_by,topic_score,locked) VALUES(?,?,NOW(),?,UNIX_TIMESTAMP((NOW())),?)");
        $query->execute(array($_POST['title'], $_POST['text'], $_SESSION['user_id'],$_POST['lock']));
         if($query->rowCount() < 1)
         {
           die('Cannot create topic.');
         }
         $_POST['topic'] = $db->lastInsertId();
    }
    else {
        $query = $db->prepare("INSERT INTO topics(topic_title,topic_text,topic_date,topic_by,topic_score,locked) VALUES(?,?,NOW(),?,UNIX_TIMESTAMP((NOW())))");
        $query->execute(array($_POST['title'], $_POST['text'], $_SESSION['user_id']));
         if($query->rowCount() < 1)
         {
           die('Cannot create topic.');
         }
         $_POST['topic'] = $db->lastInsertId();
    }
  }
  else
  {
    if (isset($_SESSION['permissions']) && ($_SESSION['permissions'] == '1')) {
        if (isset($_POST['stick'])) {
            $query = $db->prepare("UPDATE topics SET sticky = ? WHERE topic_id = ?");
	    $query->execute(array($_POST['stick'],$_POST['topic']));
        }
        if (isset($_POST['lock'])) {
	    $query = $db->prepare("UPDATE topics SET locked = ? WHERE topic_id = ?"); //max(((topic_score + NOW() )/2),(NOW() - 3600))
	    $query->execute(array($_POST['lock'],$_POST['topic']));
        }
    }
    // can't post in locked topics
    $query = mysql_query("SELECT locked FROM topics WHERE topic_id = " . $_POST['topic'] . " LIMIT 1") or die(mysql_error()); 
    $row = mysql_fetch_assoc($query);
    if ($row['locked'] == 1) {
        if ((!isset($_SESSION['permissions']) || ($_SESSION['permissions'] == 0))) die("You can not post in a locked topic unless you are a moderator!");
        else {}
    }else{}

    $query = $db->prepare("INSERT INTO posts(post_text,post_date,post_by,post_topic) VALUES(?,NOW(),?,?)");
    $query->execute(array($_POST['text'], $_SESSION['user_id'], $_POST['topic']));
    if($query->rowCount() < 1)
    {
      die('Cannot reply.');
    }
    $query = $db->prepare("UPDATE topics SET topic_replies = topic_replies + 1 WHERE topic_id = ?");
    $query->execute(array($_POST['topic']));
    $query = $db->prepare("UPDATE topics SET topic_score = UNIX_TIMESTAMP(NOW()) WHERE topic_id = ?"); //max(((topic_score + NOW() )/2),(NOW() - 3600))
    $query->execute(array($_POST['topic']));
  }

  die('SUCCESS'.$_POST['topic']);
}
?>