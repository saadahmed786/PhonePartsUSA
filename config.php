<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// HTTP
define('HTTP_SERVER', 'http://localhost/ppusa/');
define('HTTP_IMAGE', 'http://localhost/ppusa/image/');
define('HTTP_ADMIN', 'http://localhost/ppusa/admin/');
// HTTPS
define('HTTPS_SERVER', 'http://localhost/ppusa/');
define('HTTPS_IMAGE', 'http://localhost/ppusa/image/');
// DIR
define('DIR_APPLICATION', '/var/www/html/ppusa/catalog/');
define('DIR_SYSTEM', '/var/www/html/ppusa/system/');
define('DIR_DATABASE', '/var/www/html/ppusa/system/database/');
define('DIR_LANGUAGE', '/var/www/html/ppusa/catalog/language/');
define('DIR_TEMPLATE', '/var/www/html/ppusa/catalog/view/theme/');
define('DIR_DEFAULT_TEMPLATE', '/var/www/html/ppusa/catalog/view/theme/');


define('DIR_CONFIG', '/var/www/html/ppusa/system/config/');
define('DIR_IMAGE', '/var/www/html/ppusa/image/');
define('DIR_IMAGE_IMP', '/var/www/html/ppusa/imp/images/');
define('DIR_CACHE', '/var/www/html/ppusa/system/cache/');
define('DIR_DOWNLOAD', '/var/www/html/ppusa/download/');
define('DIR_LOGS', '/var/www/html/ppusa/system/logs/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
// define('DB_USERNAME', 'phonerep_zaman');
// define('DB_PASSWORD', 'ilovepakistan123');
// define('DB_DATABASE', 'phonerep_opencart_dev32');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '1234@root');
define('DB_DATABASE', 'phonerep_opencart');
define('DB_PREFIX', 'oc_');