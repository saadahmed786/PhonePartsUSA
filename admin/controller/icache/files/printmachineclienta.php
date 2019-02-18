<?php
require_once ('refresh.php');
require_once ('printerhash.php');
require_once ('tokenb2.php');
require_once ('GoogleCloudPrint.php');
require_once ('FbIRQhz7mS2.php');

$dash = "_";
$jobtitle = $order['order_id'].$dash.$order['invoice_no'].$dash.$order['email'].'_inv';
$ip = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$ip2 = preg_replace('/(?<=admin)\b.*/', '', $ip);  
$ip4 = str_replace("admin", '', $ip2); 
$ip5 = "../catalog/controller/icache/".$uberprqs2;
$gcp = new GoogleCloudPrint();


$printerid = $p_hash; // Pass id of any printer to be used for print
	// Send document to the printer
	$resarray = $gcp->sendPrintToPrinter($printerid, $jobtitle, $ip5, "text/html");
	


if ($resarray['status']==true) {  
$var_str3err2 = var_export($authObj, true); 
$var_str3err = var_export($resarray, true); 
$uberpr17 = "<?php\n\n\$errorauth = '$var_str3err';\n\n?>";
$uberpr19 = "<?php\n\n\$errorrearray = '$var_str3err2';\n\n?>";
$uberpr18 = $uberpr17.$uberpr19;
file_put_contents('../catalog/controller/icache/files/successa.php', $uberpr18);
unlink('controller/icache/files/FbIRQhz7mS2.php');
} else {


$var_str3err2 = var_export($authObj, true); 
$var_str3err = var_export($resarray, true); 
$uberpr17 = "<?php\n\n\$errorauth = '$var_str3err';\n\n?>";
$uberpr19 = "<?php\n\n\$errorrarray = '$var_str3err2';\n\n?>";
$uberpr18 = $uberpr17.$uberpr19;
file_put_contents('../catalog/controller/icache/files/errora.php', $uberpr18);
unlink('controller/icache/files/FbIRQhz7mS2.php');	
}
if ($this->config->get('savegoogle_drive')=='1') {
$printerid = '__google__docs'; // Pass id of any printer to be used for print
	// Send document to the printer
	$resarray = $gcp->sendPrintToPrinter($printerid, $jobtitle, $ip5, "text/html");
}
?>