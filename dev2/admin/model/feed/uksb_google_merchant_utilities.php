<?php
require_once('../../config.php');

if(isset($_GET['run'])){
	require_once(DIR_SYSTEM . 'startup.php');
	
	// Registry
	$registry = new Registry();
	
	// Loader
	$loader = new Loader($registry);
	$registry->set('load', $loader);
	
	// Config
	$config = new Config();
	$registry->set('config', $config);
	
	// Database 
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$registry->set('db', $db);
}

if(isset($_GET['run'])&&$_GET['run']==1){
	$query = $db->query("UPDATE `" . DB_PREFIX . "uksb_google_merchant_products` SET `g_on_google` = '1'");
	$completed = 1;
}
if(isset($_GET['run'])&&$_GET['run']==2){
	$query = $db->query("UPDATE `" . DB_PREFIX . "uksb_google_merchant_products` SET `g_on_google` = '0'");
	$completed = 1;
}
if(isset($_GET['run'])&&$_GET['run']==3){
	$query = $db->query("UPDATE `" . DB_PREFIX . "uksb_google_merchant_products` SET `g_identifier_exists` = '1'");
	$completed = 1;
}
if(isset($_GET['run'])&&$_GET['run']==4){
	$query = $db->query("UPDATE `" . DB_PREFIX . "uksb_google_merchant_products` SET `g_identifier_exists` = '0'");
	$completed = 1;
}
if(isset($_GET['run'])&&$_GET['run']==5){
	$query = $db->query("UPDATE `" . DB_PREFIX . "uksb_google_merchant_products` SET `google_category_gb` = '', `google_category_us` = '', `google_category_au` = '', `google_category_fr` = '', `google_category_de` = '', `google_category_it` = '', `google_category_nl` = '', `google_category_es` = '', `google_category_pt` = '', `google_category_cz` = '', `google_category_jp` = '', `google_category_dk` = '', `google_category_no` = '', `google_category_pl` = '', `google_category_ru` = '', `google_category_sv` = '', `google_category_tr` = ''");
	$completed = 1;
}
if(isset($_GET['run'])&&$_GET['run']==6){
	$query = $db->query("UPDATE `" . DB_PREFIX . "uksb_google_merchant_categories` SET `google_category_gb` = '', `google_category_us` = '', `google_category_au` = '', `google_category_fr` = '', `google_category_de` = '', `google_category_it` = '', `google_category_nl` = '', `google_category_es` = '', `google_category_pt` = '', `google_category_cz` = '', `google_category_jp` = '', `google_category_dk` = '', `google_category_no` = '', `google_category_pl` = '', `google_category_ru` = '', `google_category_sv` = '', `google_category_tr` = ''");
	$completed = 1;
}
if(isset($_GET['run'])&&$_GET['run']==7){
	$query = $db->query("UPDATE `" . DB_PREFIX . "uksb_google_merchant_products` SET `g_condition` = 'new'");
	$completed = 1;
}
if(isset($_GET['run'])&&$_GET['run']==8){
	$query = $db->query("UPDATE `" . DB_PREFIX . "uksb_google_merchant_products` SET `g_condition` = 'used'");
	$completed = 1;
}
if(isset($_GET['run'])&&$_GET['run']==9){
	$query = $db->query("UPDATE `" . DB_PREFIX . "uksb_google_merchant_products` SET `g_condition` = 'refurbished'");
	$completed = 1;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>UKSB Google Merchant Utilities</title>
<link rel="stylesheet" type="text/css" href="../../view/stylesheet/stylesheet.css" />
<?php if($completed){ ?>
<script>
setTimeout(function(){
    self.close();
},2000);
</script>
<?php } ?>
</head>

<body>
<?php if($completed){ ?>
<p>Operation Complete</p>
<p><a href="javascript:self.close();">Close Window</a></p>
<?php } ?>
</body>
</html>