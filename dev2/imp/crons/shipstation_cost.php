<?php

set_time_limit(0);
include "../config.php";
include "../inc/functions.php";

$last_cron_time = $db->func_query_first_cell("select config_value from configuration where config_key = 'SHIPSTATION_LAST_TIME'");

getOrderShipments($last_cron_time , 1);

function getOrderShipments($start_date , $page = 1, $page_size = 100){
	global $db;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://ssapi.shipstation.com/shipments?createDateStart=".urlencode($start_date)."&page=$page");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	$authtoken = base64_encode("0d50ba42240844269473de9ba065873e:771f86ef07aa47b29e275175d00e6481");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  		"Authorization:Basic $authtoken"
	));

	$response = curl_exec($ch);
	if($response){
		$response = json_decode($response,true);
		$createDate = '';
		foreach($response['shipments'] as $shipment){
			$isExist = $db->func_query_first_cell("select id from inv_order_shipments where shipment_id = '".$shipment['shipmentId']."'");
			if(!$isExist){
				$order_shipment = array();
				$order_shipment['shipment_id'] = $shipment['shipmentId'];
				$order_shipment['order_id']    = $shipment['orderNumber'];
				$order_shipment['shipping_cost'] = $shipment['shipmentCost'];
				$order_shipment['ship_date'] = $shipment['shipDate'];
				$order_shipment['insurance_cost']  = $shipment['insuranceCost'];
				$order_shipment['tracking_number'] = $shipment['trackingNumber'];
				$order_shipment['carrier_code'] = $shipment['carrierCode'];
				$order_shipment['service_code'] = $shipment['serviceCode'];
				$order_shipment['package_code'] = $shipment['packageCode'];
				$order_shipment['confirmation'] = $shipment['confirmation'];
				$order_shipment['voided'] = $shipment['voided'];
				$order_shipment['weight'] = $shipment['weight']['value'];
				$order_shipment['units']  = $shipment['weight']['units'];
				$order_shipment['date_added'] = date("Y-m-d H:i:s");
				
				$db->func_array2insert("inv_order_shipments", $order_shipment);
			}
			
			$createDate = $shipment['createDate'];
		}
		
		if($createDate){
			$createDate = substr($createDate,0,19);
			$db->db_exec("update configuration SET config_value = '$createDate' where config_key = 'SHIPSTATION_LAST_TIME'");
		}
		
		if($response['pages'] > $page){
			 getOrderShipments($start_date , $page + 1);
		}
		
		return;
	}
	else{
		$error = curl_error($ch);
		print_r(curl_getinfo($ch));
	}
	curl_close($ch);
}

echo "success";