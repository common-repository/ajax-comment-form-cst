<?php
	session_start();
	include("captcha.class.php");
	if(isset($_GET['width']))
    $width = trim($_GET['width']);

	if(isset($_GET['height']))
    $height = trim($_GET['height']);

	$capthaOBJ = new Captcha();
	$capthaOBJ->OutputCaptcha($width=400,$height=230,$length=6) // can be call also $capthaOBJ->OutputCaptcha(100,30,6) // param width, height, length respectively
?>