<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

require_once("config.php");

if($_REQUEST['check']=='test'){
	$HTTP_RAW_POST_DATA = file_get_contents("test.xml");
}
else{
	// mail("vipin.garg12@gmail.com","best offer alert",$HTTP_RAW_POST_DATA);
	// file_put_contents("bestoffers_alert.txt",$HTTP_RAW_POST_DATA, FILE_APPEND);
}

$start_pos = strpos($HTTP_RAW_POST_DATA,"<GetItemTransactionsResponse");
$end_pos   = strpos($HTTP_RAW_POST_DATA,"</GetItemTransactionsResponse>");
if(!$start_pos OR !$end_pos){
	$start_pos = strpos($HTTP_RAW_POST_DATA,"<GetBestOffersResponse");
	$end_pos   = strpos($HTTP_RAW_POST_DATA,"</GetBestOffersResponse>");
}
$HTTP_RAW_POST_DATA = substr($HTTP_RAW_POST_DATA,$start_pos,$end_pos);
$HTTP_RAW_POST_DATA = str_replace(array('</soapenv:Envelope>','</soapenv:Body>'),"",$HTTP_RAW_POST_DATA);



$responseObj = simplexml_load_string($HTTP_RAW_POST_DATA) or die($HTTP_RAW_POST_DATA);
$NotificationEventName = $responseObj->NotificationEventName;

$eventArr = array('ReturnCreated');
if(!in_array($NotificationEventName, $eventArr)){
	return;
}

if($NotificationEventName == 'ReturnCreated'){
	
}

echo "success";
?>