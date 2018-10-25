<?php
require_once 'connect.php';

/*$_POST['id'] = $_GET['id'];
$_POST['parent-topic-id'] = $_GET['parent-topic-id'];
$_POST['content-type'] = $_GET['content-type'];/**/

if (!isset($_POST['parenttopicid']) && $_POST['postortopic'] == 'topic') {$_POST['parenttopicid'] = $_POST['id'];}

if (!is_numeric($_POST['id'])) die('Can not understand post data :(');

if ($_POST['postortopic'] == 'topic') {
  if (isset($_SESSION['permissions']) && ($_SESSION['permissions'] == '1')) {
     $query = $db->prepare('DELETE FROM topics WHERE topic_id = ?');
     $query->execute(array($_POST['id'])) or die ($query->errorInfo()[2]);
 
 	die("success, the topic was deleted!");
  }
  
  else {
    die("You need to be a moderator or the owner of the topic to delete posts");
  }
}

elseif ($_POST['postortopic'] == 'post') {
  if (isset($_SESSION['permissions']) && ($_SESSION['permissions'] == '1')) {
     
     $query = $db->prepare('DELETE FROM posts WHERE post_id = ?');
     $query->execute(array($_POST['id'])) or die ($query->errorInfo()[2]);
     
     $query = $db->prepare('UPDATE topics SET topic_replies = (topic_replies-1) WHERE topic_id = ?');
     $query->execute(array($_POST['parenttopicid'])) or die ($query->errorInfo()[2]);
     
     die("success");
  }
  
  else {
    die("You need to be a moderator or the owner of the topic to delete posts");
  }
}

else {die("Sorry, but we do not have the necessary post data to delete a post");}

//echo "everything works";

?>