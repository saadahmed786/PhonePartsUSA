<?php

define("DEBUG", true);

$_SERVER['SERVER_PORT'] = 80;
$_GET['route'] = 'wx/getimages';

//echo realpath(dirname(__FILE__) . '/../') . "\n";

//ini_set('include_path', ini_get('include_path') . ':' . realpath(dirname(__FILE__) . '/../'));

require_once('index.php');