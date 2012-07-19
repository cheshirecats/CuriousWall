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
	if (!isset($_SESSION['user_id'])) die('请先登陆。');
	if ($_POST['topic'] < 0)
	{
		if (!$_POST['title'])
		{
			die('请填写标题。');
		}
		$title_len = mb_strlen($_POST['title'],'UTF8');
		if (($title_len < 1) || ($title_len > 200))
		{
			die('标题最短1字，最长200字。现为'.$title_len .'字。');
		}
		$_POST['title'] = nl2br(htmlspecialchars($_POST['title']));
	}
	if (!$_POST['text'])
	{
		die('请填写内容。');
	}
	$text_len = mb_strlen($_POST['text'],'UTF8');
	if (($text_len < 1) || ($text_len > 5000))
	{
		die('内容最短1字，最长5000字。现为'.$text_len .'字。');
	}
	$_POST['text'] = nl2br(htmlspecialchars($_POST['text']));
	$_POST['text'] = str_replace("  ", "&nbsp;&nbsp;", $_POST['text']);
	
	if ($_POST['topic'] < 0)
	{
		$query = $db->prepare("INSERT INTO topics(topic_title,topic_text,topic_date,topic_by,topic_score) VALUES(?,?,NOW(),?,UNIX_TIMESTAMP(NOW()))");
		$query->execute(array($_POST['title'], $_POST['text'], $_SESSION['user_id']));
		if($query->rowCount() < 1)
		{
			die('无法添加主题。');
		}
		$_POST['topic'] = $db->lastInsertId();
	}
	else
	{
		$query = $db->prepare("INSERT INTO posts(post_text,post_date,post_by,post_topic) VALUES(?,NOW(),?,?)");
		$query->execute(array($_POST['text'], $_SESSION['user_id'], $_POST['topic']));
		if($query->rowCount() < 1)
		{
			die('无法添加回复。');
		}
		$query = $db->prepare("UPDATE topics SET topic_replies = topic_replies + 1 WHERE topic_id = ?");
		$query->execute(array($_POST['topic']));
		$query = $db->prepare("UPDATE topics SET topic_score = UNIX_TIMESTAMP(NOW()) WHERE topic_id = ?");
		$query->execute(array($_POST['topic']));
	}

	die('SUCCESS'.$_POST['topic']);
}
?>