<?php

//echo "hello, it looks like you don't have javascript enabled. You should do something about that :)";

?>

<?php
  require_once 'connect.php';require_once 'post_func.php'; 
  if (isset($SHALL_LOG_OUT))
  {
    $_SESSION = array(); setcookie(session_name(), '', time() - 42000); session_destroy(); 
    header('location: index.php');
  }

// Regex: I have no idea how this works, I just got it from http://www.txt2re.com/ 

  $txt=$_GET['_escaped_fragment_'];

  $re1='(topic)';	# Word 1
  $re2='(\\/)';	# Any Single Character 1
  $re3='(\\d+)';	# Integer Number 1
  $re4='(\\/)';	# Any Single Character 2
  $re5='(\\d+)';	# Integer Number 2

  if ($c=preg_match_all ("/".$re1.$re2.$re3.$re4.$re5."/is", $txt, $matches))
  {
//      $word1=$matches[1][0];
//      $c1=$matches[2][0];
      $getopic=$matches[3][0];
 //     $c2=$matches[4][0];
      $getpagination=$matches[5][0];
 //     print "($word1) ($c1) ($int1) ($c2) ($int2) \n";
  }elseif ($c=preg_match_all ("/".$re1.$re2.$re3."/is", $txt, $matches)) {

/*  $re1='(topic)';	# Word 1
  $re2='(\\/)';	# Any Single Character 1
  $re3='(\\d+)';	# Integer Number 1

  if ($c=preg_match_all ("/".$re1.$re2.$re3."/is", $txt, $matches))
  {
/*      $word1=$matches[1][0];
      $c1=$matches[2][0];*/
      $getopic=$matches[3][0];
      $getpagination='1';
 //     print "($word1) ($c1) ($int1) \n";
  }else {die("Error: unable to prossess _escaped_fragment_.");}

//if (!$getpagination) {$getpagination = '1';}
//echo $getopic .'.'.$getpagination;die;
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
<div id="left_container">
  <div id="left" class="box wall" style="display: block;"><noscript>Please enable javascript, this site can not function properly without it.</noscript>
  </div></div>
<div id="mid_container">
<div id="mid" class="box wall" style="display: block;"><div id="text_container"><?php post_get($db, $getopic, $getpagination);?></div>
</div></div>
</div>
</div>

<?php //require_once 'post_func.php'; post_get($db, 25, 1); die();?>
</body>
</html>
