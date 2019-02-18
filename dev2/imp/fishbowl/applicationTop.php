<?php

ob_start();
set_time_limit('3600');
session_start();

// Fishbowl App Info
//define('APP_KEY', 'NLWWV-NA5H7-GVA5N-7UNMY-7NQRV-7NJJW');
define('APP_KEY', '12345');

define('APP_NAME', 'Fishbowl PHP Sample');
define('APP_DESCRIPTION', 'Fishbowl connection sample for PHP.');

require_once("fbErrorCodes.class.php");
require_once("fishbowlAPI.class.php");

// Create Fishbowl Connection
$fbapi = new FishbowlAPI("localhost", "28192");//28192 76.2.146.234

$_SESSION['username'] = 'admin';
$_SESSION['password'] = 'admin123';

if (isset($_SESSION['username'])) {
	$fbapi->Login($_SESSION['username'], $_SESSION['password']);
}
?>