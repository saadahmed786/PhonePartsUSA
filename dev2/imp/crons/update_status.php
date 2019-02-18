<?php

include_once '../config.php';

//authnet orders
$_query  = "select avs_code , order_id from inv_orders where payment_source = 'Auth.net' and match_status = 0 order by order_id DESC";
$_result = $db->func_query($_query);

if($_result){
	foreach($_result as $order){
		if($order['avs_code'] == 'A'){
			$match1 = "Match";
			$match2 = "No Match";
		}
		elseif($order['avs_code'] == 'Z' || $order['avs_code'] == 'W'){
			$match1 = "No Match";
			$match2 = "Match";
		}
		elseif($order['avs_code'] == 'Y' || $order['avs_code'] == 'X'){
			$match1 = "Match";
			$match2 = "Match";
		}
		elseif($order['avs_code'] == 'N'){
			$match1 = "No Match";
			$match2 = "No Match";
		}
		else{
			$match1 = "No Match";
			$match2 = "No Match";
		}

		$code = "$match1 , $match2";
		$match_status = 0;
		switch($code){
			case "Match , Match":
				$match_status = 1;
				break;

			case "Match , No Match":
				$match_status = 2;
				break;

			case "No Match , Match":
				$match_status = 3;
				break;

			case "No Match , No Match":
				$match_status = 4;
				break;
		}

		if($match_status){
			$db->db_exec("Update inv_orders SET match_status = '$match_status' Where order_id = '".$order['order_id']."'");
		}
	}
}

//authnet orders update B/S check
$_query  = "select order_id from inv_orders where payment_source = 'Auth.net' and bscheck = 0 order by order_id DESC";
$_result = $db->func_query($_query);

if($_result){
	foreach($_result as $order){
		$order_id = intval($order['order_id']);
		$order_detail = $db->func_query_first("select address1 , bill_address1 , zip , bill_zip from  inv_orders_details where order_id = '$order_id'");

		if($order_detail){
			$match = "No Match";

			$order_detail['address1'] = strtolower(trim($order_detail['address1']));
			$order_detail['bill_address1']  = strtolower(trim($order_detail['bill_address1']));

			$order_detail['zip'] = strtolower(trim($order_detail['zip']));
			$order_detail['bill_zip']  = strtolower(trim($order_detail['bill_zip']));
				
			$shippingArr = explode(" ",trim($order_detail['address1']));
			$paymentArr = explode(" ",$order_detail['bill_address1']);

			if(stristr($shippingArr[0] , $paymentArr[0]) && stristr($order_detail['zip'] , $order_detail['bill_zip'])){
				$match = "Match";
			}

			$match_status = 0;
			switch($match){
				case "Match":
					$match_status = 1;
					break;

				case "No Match":
					$match_status = 2;
					break;
			}

			if($match_status){
				$db->db_exec("Update inv_orders SET bscheck = '$match_status' Where order_id = '".$db->func_escape_string($order['order_id'])."'");
			}
		}
	}
}


//paypal orders update AVS
$_query  = "select order_id , street_address , zipcode from inv_orders where payment_source = 'PayPal' and match_status = 0  order by order_id DESC";
$_result = $db->func_query($_query);

if($_result){
	foreach($_result as $order){
		$order_id = intval($order['order_id']);
		$order_detail = $db->func_query_first("select address1 , zip from  inv_orders_details where order_id = '$order_id'");

		if($order_detail){
			$match1 = "No Match";
			$match2 = "No Match";

			$order['street_address'] = strtolower(trim($order['street_address']));
			$address = strtolower(trim($order_detail['address1']));
			
			$addressArr1 = explode(" ",trim($order['street_address']));
			$addressArr2 = explode(" ",trim($address));

			if(stristr($addressArr1[0] , $addressArr2[0])){
				$match1 = "Match";
			}

			$order['zip'] = strtolower(trim($order['zipcode']));
			$zip = strtolower(trim($order_detail['zip']));

			if(stristr($order['zip'] , $zip)){
				$match2 = "Match";
			}

			$code = "$match1 , $match2";
			$match_status = 0;
			switch($code){
				case "Match , Match":
					$match_status = 1;
					break;

				case "Match , No Match":
					$match_status = 2;
					break;

				case "No Match , Match":
					$match_status = 3;
					break;

				case "No Match , No Match":
					$match_status = 4;
					break;
			}

			if($match_status){
				$db->db_exec("Update inv_orders SET match_status = '$match_status' Where order_id = '".$db->func_escape_string($order['order_id'])."'");
			}
		}
	}
}


//paypal orders update B/S check
$_query  = "select order_id from inv_orders where payment_source = 'PayPal' and bscheck = 0 order by order_id DESC";
$_result = $db->func_query($_query);

if($_result){
	foreach($_result as $order){
		$order_id = intval($order['order_id']);
		$order_detail = $db->func_query_first("select address1 , bill_address1 , zip , bill_zip from  inv_orders_details where order_id = '$order_id'");

		if($order_detail){
			$match = "No Match";

			$order_detail['address1'] = strtolower(trim($order_detail['address1']));
			$order_detail['bill_address1']  = strtolower(trim($order_detail['bill_address1']));

			$order_detail['zip'] = strtolower(trim($order_detail['zip']));
			$order_detail['bill_zip']  = strtolower(trim($order_detail['bill_zip']));
			
			$shippingArr = explode(" ",trim($order_detail['address1']));
			$paymentArr = explode(" ",$order_detail['bill_address1']);

			if(stristr($shippingArr[0] , $paymentArr[0]) && stristr($order_detail['zip'] , $order_detail['bill_zip'])){
				$match = "Match";
			}

			$match_status = 0;
			switch($match){
				case "Match":
					$match_status = 1;
					break;

				case "No Match":
					$match_status = 2;
					break;
			}

			if($match_status){
				$db->db_exec("Update inv_orders SET bscheck = '$match_status' Where order_id = '".$db->func_escape_string($order['order_id'])."'");
			}
		}
	}
}

echo "success";
exit;