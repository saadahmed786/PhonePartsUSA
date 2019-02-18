<?php
include_once '../config.php';
include_once '../inc/functions.php';
include 'paypal/paypal.php';


$api_username = 'paypal_api1.phonepartsusa.com';
$api_password = 'A3UTLAF89676LVFW';
$api_signature = 'AWEus9lWHhjbjG6qaUICKluU-eFdAZ2ufK7YWkgbrqeiaBiq1y7wOc0j';
$paypal = new PaypalPayment($api_username , $api_password , $api_signature);
$transactionDetail = $paypal->getTransctionDetails('833367712A2960022');
testObject($transactionDetail);






 ?>