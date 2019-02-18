<?php

session_name('IMP_PANEL');
session_start();

date_default_timezone_set("America/Los_Angeles");
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

$sql_db   = "phonerep_opencart_dev1";
$sql_user = "phonerep_dev1";
$sql_password = "[Parm4}9a?qG";
$sql_host = "localhost";

define('ROOT', dirname(dirname(dirname(__FILE__))));
$path = "/home/phonerep/public_html/imp/";

$host_path="http://phonepartsusa.com/imp/";

$debug_mode = 1;
include_once $path.'inc/db.php';

$db = new Database();

?>