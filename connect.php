<?php
error_reporting(E_ALL);
session_name('cwHello'); session_set_cookie_params(10*365*24*60*60); session_start();

$db_host		= 'localhost';
$db_user		= 'test';
$db_pass		= 'test';
$db_name		= 'test';

$db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass
			, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
);
?>