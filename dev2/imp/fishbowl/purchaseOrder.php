<?php

date_default_timezone_set ( "America/Los_Angeles" );
echo "started at - " . date ( 'Y-m-d H:i:s' ) . "<br />";

require_once ("applicationTop.php");

set_time_limit ( 0 );
ini_set ( "memory_limit", "20000M" );

include_once ("../config.php");

include_once 'db.php';
$sql_host = "localhost";
$sql_user = "root";
$sql_password = "";
$sql_db = "inv_manager";
$db = new Database ();

$script = $db->func_query_first ( "select * from scripts where name != 'purchaseOrder' and status = 1" );
if ($script and $script ['status'] == 1 and (time () - strtotime ( $script ['last_time'] )) < 300) {
	echo "other script running";
	exit ();
}

$order_url = $host_path . "/fishbowl/fetchPoOrders.php";
$ch = curl_init ();
curl_setopt ( $ch, CURLOPT_URL, $order_url );
curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
$orders = curl_exec ( $ch );

if ($orders == 'NO') {
	echo "no new orders";
	exit ();
}

$db->db_exec ( "update scripts SET status = 1 , last_time = '" . date ( 'Y-m-d H:i:s' ) . "' where name = 'purchaseOrder'" );

// $orders = json_decode($orders, true);
$orders = unserialize ( $orders );

$successIds = array ();
$successPOIds = array ();
$OrderSoNumbers = array ();
$nomappingIds = array ();
$nomappingPOIds = array ();
$errorMessage = array ();

// print_r($orders); exit;

foreach ( $orders as $order ) {
	// add order
	$result = $fbapi->POSaveRq ( $order );
	// print_R($result);
	// exit;
	
	$FbiMsgsRsStatus = $result ['FbiMsgsRs'] ['@attributes'] ['statusCode'];
	$SaveSORsStatus = $result ['FbiMsgsRs'] ['SavePORs'] ['@attributes'] ['statusCode'];
	if (@$result ['FbiMsgsRs'] [0]) {
		$attributes = $result ['FbiMsgsRs'] [0]->attributes ();
		$SaveSORsStatus = $attributes ['statusCode'];
		$SaveSORsMessage = $attributes ['statusMessage'];
	}
	
	print $FbiMsgsRsStatus . " -- " . $SaveSORsStatus . "<br />";
	
	if ($FbiMsgsRsStatus == 1000 && $SaveSORsStatus == 1000) {
		if ($order ['order_type'] == 'shipment') {
			$successIds [] = "'" . $order ['id'] . "'";
		}
		else {
			$successPOIds [] = "'" . $order ['id'] . "'";
		}
	}
	else {
		if ($order ['order_type'] == 'shipment') {
			$nomappingIds [] = "'" . $order ['id'] . "'";
		}
		else {
			$nomappingPOIds [] = "'" . $order ['id'] . "'";
		}
		
		$errorMessage [$order ['id']] = array (
				$SaveSORsStatus,
				$SaveSORsMessage,
				$FbiMsgsRsStatus,
				$order_result 
		);
	}
}

if (($successIds and count ( $successIds ) > 0) || ($nomappingIds and count ( $nomappingIds ) > 0) || ($successPOIds and count ( $successPOIds ) > 0) || ($nomappingPOIds and count ( $nomappingPOIds ) > 0)) {
	$successIdsStr = implode ( ",", $successIds );
	$nomappingIdsStr = implode ( ",", $nomappingIds );
	
	$successPOIdsStr = implode ( ",", $successPOIds );
	$nomappingPOIdsStr = implode ( ",", $nomappingPOIds );
	
	$updateUrl = $host_path . "/fishbowl/updatePoOrders.php";
	
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $updateUrl );
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, array (
			'successIdsStr' => $successIdsStr,
			'nomappingIdsStr' => $nomappingIdsStr,
			'successPOIdsStr' => $successPOIdsStr,
			'nomappingPOIdsStr' => $nomappingPOIdsStr,
			'errorMessage' => json_encode ( $errorMessage ) 
	) );
	curl_setopt ( $ch, CURLOPT_TIMEOUT, 30 );
	curl_exec ( $ch );
}
// if(isset($_GET['action']) && $_GET['action']=='sync_rts')
// {}
$order_url = $host_path . "/fishbowl/fetchRTSBox.php";

$ch = curl_init();
curl_setopt($ch , CURLOPT_URL , $order_url);
curl_setopt($ch , CURLOPT_TIMEOUT, 10);
curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
$orders = curl_exec($ch);

if($orders == 'NO'){
	echo "no new orders";
	exit;

}
// print_r($orders);exit;

// $db->db_exec("update scripts SET status = 1 , last_time = '".date('Y-m-d H:i:s')."' where name = 'RTSBox'");

$orders = json_decode($orders,true );
// print_r($orders);exit;
// var_dump($orders);exit;
$successIds = array();
$successPOIds = array();
$OrderSoNumbers = array();
$nomappingIds = array();
$nomappingPOIds = array();
$errorMessage = array();

// print_r($orders); exit;

foreach($orders as $order){
	// add order
	$result = $fbapi->POSaveRq($order);
	//print_R($result);
	//exit;

	$FbiMsgsRsStatus = $result['FbiMsgsRs']['@attributes']['statusCode'];
	$SaveSORsStatus  = $result['FbiMsgsRs']['SavePORs']['@attributes']['statusCode'];
	if(@$result['FbiMsgsRs'][0]){
		$attributes = $result['FbiMsgsRs'][0]->attributes();
		$SaveSORsStatus  = $attributes['statusCode'];
		$SaveSORsMessage = $attributes['statusMessage'];
	}

	print $FbiMsgsRsStatus . " -- " . $SaveSORsStatus . "<br />";

	if ($FbiMsgsRsStatus == 1000 &&  $SaveSORsStatus == 1000) {
		if($order['order_type'] == 'shipment'){
			$successIds[] = "'" . $order['id'] . "'";
		}
		
	}
	else{
		if($order['order_type'] == 'shipment'){
			$nomappingIds[] = "'" . $order['id'] . "'";
		}
		

		$errorMessage[$order['id']] = array($SaveSORsStatus,$SaveSORsMessage,$FbiMsgsRsStatus,$order_result);
	}
}

if(($successIds and count($successIds) > 0) || ($nomappingIds and count($nomappingIds) > 0)){
	$successIdsStr   = implode(",",$successIds);
	$nomappingIdsStr = implode(",",$nomappingIds);
	
	
	$updateUrl = $host_path . "/fishbowl/updateRTSBox.php";

	$ch = curl_init();
	curl_setopt($ch , CURLOPT_URL , $updateUrl);
	curl_setopt($ch , CURLOPT_POST , 1);
	curl_setopt($ch , CURLOPT_POSTFIELDS , array('successIdsStr' => $successIdsStr ,
                                                 'nomappingIdsStr' => $nomappingIdsStr,
                                                 'errorMessage' => json_encode($errorMessage)
	));
	curl_setopt($ch , CURLOPT_TIMEOUT, 30);
	curl_exec($ch);
}


// LBB Boxes Sync
$order_url = $host_path . "/fishbowl/fetchLBB.php";

$ch = curl_init();
curl_setopt($ch , CURLOPT_URL , $order_url);
curl_setopt($ch , CURLOPT_TIMEOUT, 10);
curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
$orders = curl_exec($ch);

if($orders == 'NO'){
	echo "no new orders";
	exit;

}
// print_r($orders);exit;

// $db->db_exec("update scripts SET status = 1 , last_time = '".date('Y-m-d H:i:s')."' where name = 'RTSBox'");

$orders = json_decode($orders,true );
// print_r($orders);exit;
// var_dump($orders);exit;
$successIds = array();
$successPOIds = array();
$OrderSoNumbers = array();
$nomappingIds = array();
$nomappingPOIds = array();
$errorMessage = array();

// print_r($orders); exit;

foreach($orders as $order){
	// add order
	$result = $fbapi->POSaveRq($order);
	//print_R($result);
	//exit;

	$FbiMsgsRsStatus = $result['FbiMsgsRs']['@attributes']['statusCode'];
	$SaveSORsStatus  = $result['FbiMsgsRs']['SavePORs']['@attributes']['statusCode'];
	if(@$result['FbiMsgsRs'][0]){
		$attributes = $result['FbiMsgsRs'][0]->attributes();
		$SaveSORsStatus  = $attributes['statusCode'];
		$SaveSORsMessage = $attributes['statusMessage'];
	}

	print $FbiMsgsRsStatus . " -- " . $SaveSORsStatus . "<br />";

	if ($FbiMsgsRsStatus == 1000 &&  $SaveSORsStatus == 1000) {
		if($order['order_type'] == 'shipment'){
			$successIds[] = "'" . $order['id'] . "'";
		}
		
	}
	else{
		if($order['order_type'] == 'shipment'){
			$nomappingIds[] = "'" . $order['id'] . "'";
		}
		

		$errorMessage[$order['id']] = array($SaveSORsStatus,$SaveSORsMessage,$FbiMsgsRsStatus,$order_result);
	}
}

if(($successIds and count($successIds) > 0) || ($nomappingIds and count($nomappingIds) > 0)){
	$successIdsStr   = implode(",",$successIds);
	$nomappingIdsStr = implode(",",$nomappingIds);
	
	
	$updateUrl = $host_path . "/fishbowl/updateLBB.php";

	$ch = curl_init();
	curl_setopt($ch , CURLOPT_URL , $updateUrl);
	curl_setopt($ch , CURLOPT_POST , 1);
	curl_setopt($ch , CURLOPT_POSTFIELDS , array('successIdsStr' => $successIdsStr ,
                                                 'nomappingIdsStr' => $nomappingIdsStr,
                                                 'errorMessage' => json_encode($errorMessage)
	));
	curl_setopt($ch , CURLOPT_TIMEOUT, 30);
	curl_exec($ch);
}


$db->db_exec ( "update scripts SET status = 0 where name = 'purchaseOrder'" );

echo "success";