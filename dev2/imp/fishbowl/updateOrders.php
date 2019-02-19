<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("../config.php");

if (get_magic_quotes_gpc()) {
	function undoMagicQuotes($array, $topLevel=true) {
		$newArray = array();
		foreach($array as $key => $value) {
			if (!$topLevel) {
				$key = stripslashes($key);
			}
			if (is_array($value)) {
				$newArray[$key] = undoMagicQuotes($value, false);
			}
			else {
				$newArray[$key] = stripslashes($value);
			}
		}
		return $newArray;
	}
	$_GET = undoMagicQuotes($_GET);
	$_POST = undoMagicQuotes($_POST);
	$_COOKIE = undoMagicQuotes($_COOKIE);
	$_REQUEST = undoMagicQuotes($_REQUEST);
}

$successIdsStr  = $_REQUEST['successIdsStr'];
$OrderSoNumbers = json_decode($_REQUEST['OrderSoNumbers'],true);

$nomappingIdsStr = $_REQUEST['nomappingIdsStr'];
$errorMessage = json_decode($_REQUEST['errorMessage'],true);

//$message = print_r($OrderSoNumbers,true);
//mail("vipin.garg12@gmail.com","Request IMP",$message);

if($_REQUEST['order_type'] == 'return'){
	if($successIdsStr){
		$db->db_exec("Update inv_return_orders SET fishbowl_uploaded = 1 , ignored = 0 , is_updated = 2 where order_id IN ($successIdsStr)");
	}

	if($nomappingIdsStr){
		$db->db_exec("Update inv_return_orders SET ignored = 1 , try_count = try_count + 1 where order_id IN ($nomappingIdsStr)");
	}
}
else{
	if($successIdsStr){
		$db->db_exec("Update inv_orders SET fishbowl_uploaded = 1 , ignored = 0 ,is_updated = 2 , dateofmodification = '".date('Y-m-d H:i:s')."' where order_id IN ($successIdsStr)");
	}

	if($nomappingIdsStr){
		$db->db_exec("Update inv_orders SET ignored = 1 , try_count = try_count + 1 where order_id IN ($nomappingIdsStr)");
	}

	if(is_array($OrderSoNumbers) and count($OrderSoNumbers) > 0){
		foreach($OrderSoNumbers as $order_id => $product_data){
			$so_number   = $product_data['SoNumber'];
			$Items       = $product_data['Items'];
			$db->db_exec("Update inv_orders SET so_number = '$so_number' where order_id = '$order_id'");

			foreach($Items as $Item){
				$product_qty   = $Item['qty'];
				$product_model = $Item['sku'];

				$db->db_exec("Update oc_product SET quantity = '$product_qty' where model = '$product_model' OR sku = '$product_model'");

				$check_result = $db->func_query_first("select * from inv_product_inout_stocks where product_sku = '$product_model' order by date_modified limit 1");
				if($product_qty <= 0){
					$insert = false;
					if(!$check_result){
						$insert = true;
					}
					elseif(intval($check_result['instock_date']) && strtotime($check_result['instock_date']) > strtotime($check_result['outstock_date'])){
						$insert = true;
					}

					if($insert){
						$inout_stock = array();
						$inout_stock['product_sku']   = $product_model;
						$inout_stock['outstock_date'] = date("Y-m-d H:i:s");
						$inout_stock['date_modified'] = date("Y-m-d H:i:s");
						// $db->func_array2insert("inv_product_inout_stocks", $inout_stock);
					}
				}
				else{
					if(!intval($check_result['instock_date'])){
						$inout_stock = array();
						$inout_stock['instock_date']  = date("Y-m-d H:i:s");
						$inout_stock['date_modified'] = date("Y-m-d H:i:s");
						// $db->func_array2update("inv_product_inout_stocks", $inout_stock , "product_sku = '$product_model'");
					}
				}
			}
		}
	}
}

if(is_array($errorMessage) and count($errorMessage) > 0){
	$message = "Hi Admin , <br />";
	$message .= "Some order with product models are not found.<br /><br />";

	foreach($errorMessage as $order_id => $error){
		$message .= "Order ID - $order_id - {$error[1][0]} <br />";

		$fb_error = array();
		$fb_error['error_code']     = $error[0][0];
		$fb_error['error_message']  = $error[1][0];
		$fb_error['request_name']   = 'saveSOOrder';
		$fb_error['order_id']       = $order_id;
		$fb_error['other_details']  = $db->func_escape_string(json_encode($error));
		$fb_error['dateofmodification'] = date('Y-m-d H:i:s');

		$db->func_array2insert("inv_fb_errors",$fb_error);
	}

	$message .= "<br /><br /> Thanks, <br /> Phonepartsusa Team";
	$headers = "From:no-reply@phonepartsusa.com\r\nFromName:phonepartsusa\r\nContent-type:text/html;charset=utf-8;";
	// mail("xaman.riaz@gmail.com","Unmapped Product model in Fishbowl",$message,$headers);

	// mail("saadahmed786@gmail.com","Unmapped Product models in Fishbowl",$message,$headers);
}

echo "success";