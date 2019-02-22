<?php

$path = "/home/phonerep/public_html/imp/";

if($_SERVER['HTTP_HOST'] == 'localhost'){
	$path = "E:/xampp2/htdocs/InventoryManager/IMP/";
}

require_once($path."config_dev.php");

if(isset($_SESSION['email']) && !$_SESSION['email'] ){
	$_SESSION['error'] = "Please login to access other pages";
	
	header("Location:index.php");
	exit;
}