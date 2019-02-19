<?php

include "../config.php";

$RequestInput = array();
$RequestInput['issue_from'] = 'shipment';
$RequestInput['product_sku'] = 'TAB-SRN-152';
$RequestInput['item_issue'] = 'test';
$RequestInput['shipment_id'] = '1';
$RequestInput['username'] = 'admin';

$request = array("RequestTime" => date("Y-m-d H:i:s"),"RequestOs"=> "Android", "RequestInput" => $RequestInput);
$requestJson = json_encode($request);

//print $requestJson; exit;
sendRequest('addItemIssue',$requestJson);
exit;

function sendRequest($methodName,$requestJson){
	$file      = 'profile.png';       // image file to read and upload
	$picNameIn = 'my_pic';
	$handle = fopen($file,'r');         // do a binary read of image
	$multiPartImageData = fread($handle,filesize($file));
	fclose($handle);

	$file      = 'test.mp3';       // image file to read and upload
	$picNameIn = 'my_pic';
	$handle = fopen($file,'r');         // do a binary read of image
	$multiPartMp3Data = fread($handle,filesize($file));
	fclose($handle);

	$boundary = "MIME_boundary";
	$CRLF = "\r\n";

	// The complete POST consists of an XML request plus the binary image separated by boundaries
	$firstPart   = '';
	$firstPart  .= "--" . $boundary . $CRLF;
	$firstPart  .= 'Content-Disposition: form-data; name="Json"' . $CRLF;
	$firstPart  .= 'Content-Type: text/json;charset=utf-8' . $CRLF . $CRLF;
	$firstPart  .= $requestJson;
	$firstPart  .= $CRLF;

	$firstPart  .= "--" . $boundary . $CRLF;
	$firstPart  .= 'Content-Disposition: form-data; name="MethodName"' . $CRLF;
	$firstPart  .= 'Content-Type: text/html;charset=utf-8' . $CRLF . $CRLF;
	$firstPart  .= $methodName;
	$firstPart  .= $CRLF;

	$firstPart .= "--" . $boundary . $CRLF;
	$firstPart .= 'Content-Disposition: form-data; name="photos[0]"; filename="profile.png"' . $CRLF;
	$firstPart .= "Content-Transfer-Encoding: binary" . $CRLF;
	$firstPart .= "Content-Type: application/octet-stream" . $CRLF . $CRLF;
	$firstPart .= $multiPartImageData;
	$firstPart .= $CRLF;
	
	$firstPart .= "--" . $boundary . $CRLF;
	$firstPart .= 'Content-Disposition: form-data; name="photos[1]"; filename="profile.png"' . $CRLF;
	$firstPart .= "Content-Transfer-Encoding: binary" . $CRLF;
	$firstPart .= "Content-Type: application/octet-stream" . $CRLF . $CRLF;
	$firstPart .= $multiPartImageData;
	$firstPart .= $CRLF;

	$firstPart .= "--" . $boundary . "--" . $CRLF;
	$fullPost = $firstPart;

	$ch  = curl_init();
	$url = "http://localhost/phonepartsusa/api/request.php";
	//$url = "http://www.phonepartsusa.com/imp/api/request.php";

	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_TIMEOUT,60);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

	curl_setopt($ch,CURLOPT_POST,1);
	//curl_setopt($ch,CURLOPT_POSTFIELDS,"MethodName=$methodName&Json=".$requestJson);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$fullPost);
	curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: multipart/form-data; boundary=' . $boundary,'Content-Length: ' . strlen($fullPost)));

	$response = curl_exec($ch);
	$error    = curl_error($ch);

	print "<pre>";
	print_r($error);
	///print_r(curl_getinfo($ch));

	print_r(json_decode($response,true));
	print "<hr />";
	print_r(($response));
}