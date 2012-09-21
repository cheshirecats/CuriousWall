<?php
// Report all PHP errors (see changelog)
error_reporting(E_ALL);
ini_set('display_errors', 'On');

session_name('cwHello'); session_set_cookie_params(10*365*24*60*60); session_start();

$db_host    = 'localhost';
$db_user    = 'christofian';
$db_pass    = 'password';
$db_name    = 'test';

$db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass
      , array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
);

mysql_select_db("test") or die(mysql_error());

// I'm defining some functions here because it's easy that way and every file uses connect.php so every file has access to them.

// display diamond next to admins/mods name, and output the username

function theusername($name,$permissions) {
if ($permissions == 1) {echo $name . "♦".((isset($bio))?"<div class='bio'>".$bio."</div>":'');}
else echo $name . ((isset($bio))?"<div class='bio'>".$bio."</div>":'');
}


// display if a topic is locked in the topics title
function displaytopiclocked($topicname,$islocked) {
  if ($islocked == 1) echo $topicname . ' <i class="icon-lock"></i>';
else echo $topicname;
}

function displaymenu($current) {
if ($current == 'topics') {echo "<div id='navbar'><div id='topicsnav currentnav'>Topics</div><div id='usernav'>Users</div></div>";}
else {echo "<div id='navbar'><div id='topicsnav'>Topics</div><div id='usernav currentnav'>Users</div></div>";}
}

?>