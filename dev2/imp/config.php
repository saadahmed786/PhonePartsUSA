<?php
ini_set('session.gc_maxlifetime', 864000);
session_set_cookie_params(864000);
// session_name('IMP_PANEL');
@session_start();
// print_r($_SESSION);
date_default_timezone_set("America/Los_Angeles");
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

$sql_db   = "phonerep_opencart_dev32";
$sql_user = "phonerep_zaman";
$sql_password = "ilovepakistan123";
$sql_host = "localhost";

$path = "/home/phonerep/public_html/dev2/imp/";

define('ROOT', dirname(dirname(dirname(__FILE__))));
define('MAIL_HOST','www.phonepartsusa.com');
define('MAIL_USER','orders@phonepartsusa.com');
define('MAIL_PASSWORD','pakistan1');
define('EASYPOST_API','V5WdqWdeuglt9n7AB8fayw');
define("DIR_CACHE",$path.'cache/');



$host_path="http://dev2.phonepartsusa.com/imp";
$local_path = $host_path;
$debug_mode = 1;
include_once $path.'inc/db.php';
include_once $path.'library/cache.php';

$db = new Database();
$cache = new Cache();


?>