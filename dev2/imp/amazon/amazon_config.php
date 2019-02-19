<?php

/************************************************************************
 * REQUIRED
 *
 * Access Key ID and Secret Acess Key ID, obtained from:
 * http://aws.amazon.com
 ***********************************************************************/
/*define('AWS_ACCESS_KEY_ID', 'AKIAJ3SZIKSHGP2YCXRQ');
 define('AWS_SECRET_ACCESS_KEY', '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');*/

//define('MERCHANT_ID', 'A2F9CYJ8SEM063');
//define('MARKET_PLACE_ID', 'ATVPDKIKX0DER');
 
define ('DATE_FORMAT', 'Y-m-d\TH:i:s\Z');

global $db;
$configurations = $db->func_query("select * from configuration",'config_key');
if($configurations){
	define('AWS_ACCESS_KEY_ID',$configurations['AWS_ACCESS_KEY_ID']['config_value']);
	define('AWS_SECRET_ACCESS_KEY',$configurations['AWS_SECRET_ACCESS_KEY']['config_value']);
}
else{
	define('AWS_ACCESS_KEY_ID', 'AKIAIRY7JGVESX55L62A');
	define('AWS_SECRET_ACCESS_KEY', '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');
}

/************************************************************************
 * REQUIRED
 *
 * All MWS requests must contain a User-Agent header. The application
 * name and version defined below are used in creating this value.
 ***********************************************************************/
define('APPLICATION_NAME', '<Your Application Name>');
define('APPLICATION_VERSION', '<Your Application Version or Build Number>');



/************************************************************************
 * OPTIONAL ON SOME INSTALLATIONS
 *
 * Set include path to root of library, relative to Samples directory.
 * Only needed when running library from local directory.
 * If library is installed in PHP include path, this is not needed
 ***********************************************************************/

$amz_path = $path."amazon/";
set_include_path(get_include_path() . PATH_SEPARATOR . $amz_path);

/************************************************************************
 * OPTIONAL ON SOME INSTALLATIONS
 *
 * Autoload function is reponsible for loading classes of the library on demand
 *
 * NOTE: Only one __autoload function is allowed by PHP per each PHP installation,
 * and this function may need to be replaced with individual require_once statements
 * in case where other framework that define an __autoload already loaded.
 *
 * However, since this library follow common naming convention for PHP classes it
 * may be possible to simply re-use an autoload mechanism defined by other frameworks
 * (provided library is installed in the PHP include path), and so classes may just
 * be loaded even when this function is removed
 ***********************************************************************/
function __autoload($className){
	$filePath = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
	$includePaths = explode(PATH_SEPARATOR, get_include_path());
	foreach($includePaths as $includePath){
		if(file_exists($includePath . DIRECTORY_SEPARATOR . $filePath)){
			require_once $filePath;
			return;
		}
	}
}
?>