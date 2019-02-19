<?php

	define('OPTIMIZATION_PREFIX', 'wx');
	if(!defined('OPTIMIZER_VERSION')) define('OPTIMIZER_VERSION', trim(file_get_contents(DIR_SYSTEM . '../' . OPTIMIZATION_PREFIX . '/.version')));
	require_once(\VQMod::modCheck(DIR_SYSTEM . '../' . OPTIMIZATION_PREFIX . '/library/minify.php'));
	require_once(\VQMod::modCheck(DIR_SYSTEM . '../' . OPTIMIZATION_PREFIX . '/library/blc.php'));
	





// Check Version

if (version_compare(phpversion(), '5.1.0', '<') == true) {

	exit('PHP5.1+ Required');

}



// Register Globals

if (ini_get('register_globals')) {

	ini_set('session.use_cookies', 'On');

	ini_set('session.use_trans_sid', 'Off');

		

	session_set_cookie_params(0, '/');

	session_start();

	

	$globals = array($_REQUEST, $_SESSION, $_SERVER, $_FILES);



	foreach ($globals as $global) {

		foreach(array_keys($global) as $key) {

			unset(${$key}); 

		}

	}

}



// Magic Quotes Fix

if (ini_get('magic_quotes_gpc')) {

	function clean($data) {

   		if (is_array($data)) {

  			foreach ($data as $key => $value) {

    			$data[clean($key)] = clean($value);

  			}

		} else {

  			$data = stripslashes($data);

		}

	

		return $data;

	}			

	

	$_GET = clean($_GET);

	$_POST = clean($_POST);

	$_REQUEST = clean($_REQUEST);

	$_COOKIE = clean($_COOKIE);

}



if (!ini_get('date.timezone')) {

	date_default_timezone_set('UTC');

}




			if (DB_DRIVER == 'mysql') {
				if (!$dblink = mysql_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD)) { trigger_error('Error: Could not make a database link using ' . DB_USERNAME . '@' . DB_HOSTNAME); }
				if (!mysql_select_db(DB_DATABASE, $dblink)) { trigger_error('Error: Could not connect to database ' . DB_DATABASE); }
				
				$time_query = mysql_query("SELECT value FROM `" . DB_PREFIX . "setting` WHERE store_id = 0 AND `key` = 'config_local_timezone'", $dblink);

				if ($time_query) {
					if (is_resource($time_query)) {
						$time_result = mysql_fetch_array($time_query);
					
						if (!empty($time_result['value'])) {
							date_default_timezone_set($time_result['value']);
						}
						mysql_free_result($time_query);
					}
				}
			} else {
				$mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
				if ($mysqli->connect_errno) {
					trigger_error('Error: Could not connect to database ' . DB_DATABASE);
				}

				if ($time_query = $mysqli->query("SELECT value FROM `" . DB_PREFIX . "setting` WHERE store_id = 0 AND `key` = 'config_local_timezone'")) {

					$time_row = $time_query->fetch_array(MYSQLI_ASSOC);
						if (!empty($time_row['value'])) {
							date_default_timezone_set($time_row['value']);
						}
					$time_query->close();
					
				}
			}
			
// Windows IIS Compatibility  

if (!isset($_SERVER['DOCUMENT_ROOT'])) { 

	if (isset($_SERVER['SCRIPT_FILENAME'])) {

		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));

	}

}



if (!isset($_SERVER['DOCUMENT_ROOT'])) {

	if (isset($_SERVER['PATH_TRANSLATED'])) {

		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));

	}

}



if (!isset($_SERVER['REQUEST_URI'])) { 

	$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1); 

	

	if (isset($_SERVER['QUERY_STRING'])) { 

		$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING']; 

	} 

}



// Helper

require_once(\VQMod::modCheck(DIR_SYSTEM . 'helper/json.php')); 

require_once(\VQMod::modCheck(DIR_SYSTEM . 'helper/utf8.php'));

require_once(\VQMod::modCheck(DIR_SYSTEM . 'helper/vat.php'));  



// Engine

require_once(\VQMod::modCheck(DIR_SYSTEM . 'engine/action.php')); 

require_once(\VQMod::modCheck(DIR_SYSTEM . 'engine/controller.php'));

require_once(\VQMod::modCheck(DIR_SYSTEM . 'engine/front.php'));

require_once(\VQMod::modCheck(DIR_SYSTEM . 'engine/loader.php')); 

require_once(\VQMod::modCheck(DIR_SYSTEM . 'engine/model.php'));

require_once(\VQMod::modCheck(DIR_SYSTEM . 'engine/registry.php'));



// Common

require_once(\VQMod::modCheck(DIR_SYSTEM . 'library/cache.php'));

require_once(\VQMod::modCheck(DIR_SYSTEM . 'library/url.php'));

require_once(\VQMod::modCheck(DIR_SYSTEM . 'library/config.php'));

require_once(\VQMod::modCheck(DIR_SYSTEM . 'library/db.php'));

require_once(\VQMod::modCheck(DIR_SYSTEM . 'library/document.php'));

require_once(\VQMod::modCheck(DIR_SYSTEM . 'library/encryption.php'));

require_once(\VQMod::modCheck(DIR_SYSTEM . 'library/image.php'));

require_once(\VQMod::modCheck(DIR_SYSTEM . 'library/language.php'));

require_once(\VQMod::modCheck(DIR_SYSTEM . 'library/log.php'));

require_once(\VQMod::modCheck(DIR_SYSTEM . 'library/mail.php'));

require_once(\VQMod::modCheck(DIR_SYSTEM . 'library/pagination.php'));

require_once(\VQMod::modCheck(DIR_SYSTEM . 'library/request.php'));

require_once(\VQMod::modCheck(DIR_SYSTEM . 'library/response.php'));

require_once(\VQMod::modCheck(DIR_SYSTEM . 'library/session.php'));

require_once(\VQMod::modCheck(DIR_SYSTEM . 'library/template.php'));
require_once(\VQMod::modCheck(DIR_SYSTEM . 'library/ebay.php'));

?>