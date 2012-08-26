<?php
  require_once 'connect.php';
  if (isset($SHALL_LOG_OUT))
  {
    $_SESSION = array(); setcookie(session_name(), '', time() - 42000); session_destroy(); 
    header('location: index.php');
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>A Simple Forum</title>
  <link rel="stylesheet" type="text/css" href="style.css" />
  <link rel="stylesheet" type="text/css" href="font-awesome.css" />
</head>
<body>
<div id="container" class="clearfix">