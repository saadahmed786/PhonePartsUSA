<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// HTTP
define('HTTP_SERVER', 'http://localhost/ppusa/admin/');
define('HTTP_CATALOG', 'http://localhost/ppusa/');
define('HTTP_IMAGE', 'http://localhost/ppusa/image/');

// HTTPS
define('HTTPS_SERVER', 'http://localhost/ppusa/admin/');
define('HTTPS_CATALOG', 'http://localhost/ppusa/');
define('HTTPS_IMAGE', 'http://localhost/ppusa/image/');

// DIR
define('DIR_APPLICATION', '/var/www/html/ppusa/admin/');
define('DIR_SYSTEM', '/var/www/html/ppusa/system/');
define('DIR_DATABASE', '/var/www/html/ppusa/system/database/');
define('DIR_LANGUAGE', '/var/www/html/ppusa/admin/language/');
define('DIR_TEMPLATE', '/var/www/html/ppusa/admin/view/template/');
define('DIR_CONFIG', '/var/www/html/ppusa/system/config/');
define('DIR_IMAGE', '/var/www/html/ppusa/image/');
define('DIR_CACHE', '/var/www/html/ppusa/system/cache/');
define('DIR_DOWNLOAD', '/var/www/html/ppusa/download/');
define('DIR_LOGS', '/var/www/html/ppusa/system/logs/');
define('DIR_CATALOG', '/var/www/html/ppusa/catalog/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '1234@root');
define('DB_DATABASE', 'phonerep_opencart');
define('DB_PREFIX', 'oc_');
?>
