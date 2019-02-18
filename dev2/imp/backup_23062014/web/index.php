<?php

date_default_timezone_set("America/Los_Angeles");

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'config.php';

//imp config file
include_once '../config.php';

//opencart config file
include_once '../../config.php';

$startDate  = getLastCronDate();
$startDate  = date('Y-m-d H:i:s',(strtotime($startDate) - (24*60*60)));

//connect opencart db and fetch orders
$conn2 = mysql_connect(DB_HOSTNAME , DB_USERNAME , DB_PASSWORD) or die(mysql_error() . " opencart db connect error");
mysql_select_db(DB_DATABASE , $conn2);

$orderArray  = array();
$order_query = "Select * from oc_order where ( oc_order.order_status_id IN( 15 , 3 , 16 ) ) AND oc_order.date_modified >= '$startDate'";

$orderQuery = mysql_query($order_query , $conn2) or die(mysql_error() . $order_query);

$k = 0;
while($order = mysql_fetch_assoc($orderQuery)){
	if($order['order_status_id'] == 15){
		$status = "Processed";
	}
	elseif($order['order_status_id'] == 3){
		$status = "Shipped";
	}
	elseif($order['order_status_id'] == 16){
		$status = "Voided";
	}
	else{
		$status = "Completed";
		continue;
	}

	$shipping_cost_query  = mysql_query("Select `value` from oc_order_total where `code` = 'shipping' and order_id = '".$order['order_id']."'");
	$shipping_cost_result = mysql_fetch_assoc($shipping_cost_query);
	$shipping_cost = $shipping_cost_result['value'];

	$orderArray[$k] = array('order_id' => $order['order_id'] ,
                            'order_price' =>  $order['total'],
                            'order_date' =>  $order['date_added'],
                            'date_modified' =>  $order['date_modified'],
                            'status' => $status,
                            'firstname' => $order['firstname'],
                            'email' => $order['email'],     
                            'lastname' => $order['lastname'],
                            'telephone' => $order['telephone'],
                            'add1' => $order['shipping_address_1'],
                            'add2' => $order['shipping_address_2'],
                            'city' => $order['shipping_city'],
                            'state' => $order['shipping_zone'],
                            'country' => $order['shipping_country'],
                            'zip' => $order['shipping_postcode'],
                            'payment_method' => $order['payment_method'],
							'shipping_cost'  => $shipping_cost
	);

	$order_items_query = mysql_query("select * from oc_order_product Where oc_order_product.order_id = '".$order['order_id']."'");
	while($order_items_row = mysql_fetch_assoc($order_items_query)){
		$orderArray[$k]['Items'][] = array('order_item_id' =>  $order['order_id']."-".$order_items_row['product_id']  ,
                                           'product_sku' => $order_items_row['model'],
                                           'product_qty' => $order_items_row['quantity'],
                                           'product_price' => $order_items_row['price'],
		);
	}

	$k++;
}

mysql_close($conn2);

$db = new Database();

insertOrderinImp($orderArray);

function insertOrderinImp($orderArray){
	global $db;

	if(!$orderArray){
		$_SESSION['message'] = "No Orders found in open cart.";
		return;
	}

	foreach($orderArray as $order){
		$orderId = $order['order_id'];
		$orderTotal = $order['order_price'];
		$orderDate  = $order['order_date'];
		$orderStatus = $order['status'];

		$email = $order['email'];
		$firstname = mysql_escape_string($order['firstname']);
		$lastname  = mysql_escape_string($order['lastname']);
		$phone = mysql_escape_string($order['telephone']);
		$add1  = mysql_escape_string($order['add1']);
		$add2  = mysql_escape_string($order['add2']);
		$city  = mysql_escape_string($order['city']);
		$state = mysql_escape_string($order['state']);
		$country = mysql_escape_string($order['country']);
		$zip = mysql_escape_string($order['zip']);
		$paymentMethod = $order['payment_method'];
		$shipping_cost = $order['shipping_cost'];

		$isExist = $db->func_query_first_cell("select id from inv_orders where order_id = '$orderId'");
		if(!$isExist){
			if($orderStatus == 'Voided'){
				$db->db_exec("insert into inv_return_orders (order_id,order_date,return_date,status,store_type,dateofmodification)
                             values ('$orderId','$orderDate','$orderDate','open','web','".date('Y-m-d H:i:s')."')");
			}
			else{
				$db->db_exec("insert into inv_orders (order_id,email,order_date,order_price,order_status,status,store_type,dateofmodification)
                             values ('$orderId','$email','$orderDate','$orderTotal' ,'$orderStatus','open' , 'web','".date('Y-m-d H:i:s')."') ");
			}

			$db->db_exec("insert into inv_orders_details (order_id,first_name,last_name,phone_number,address1,address2,city,state,country,zip,payment_method,shipping_cost,dateofmodification)
                         values ('$orderId' , '$firstname' , '$lastname' , '$phone' , '$add1' , '$add2' ,'$city' , '$state' , '$country' , '$zip' , '$paymentMethod','$shipping_cost' ,'".date('Y-m-d H:i:s')."')");

			foreach($order['Items'] as $order_item){
				$qty = $order_item['product_qty'];
				$transaction_id  = $order_item['order_item_id'];
				$product_sku    = $order_item['product_sku'];
				$product_price   = $order_item['product_price'];
				$product_price   = str_replace(',','',(string)$product_price);

				$db->db_exec("insert into inv_orders_items(order_id,order_item_id,product_sku,product_price,product_qty,dateofmodification)
                              values ('$orderId','$transaction_id','$product_sku','$product_price','$qty','".date('Y-m-d H:i:s')."')");
			}
		}
		elseif($isExist and $orderStatus == 'Voided'){
			$isReturnExist = $db->func_query_first_cell("select id from inv_return_orders where order_id = '$orderId'");
			if(!$isReturnExist){
				$returnDate = $order['date_modified'];
				$db->db_exec("insert into inv_return_orders (order_id,email,order_price,order_date,return_date,status,store_type,dateofmodification)
                             values ('$orderId','$email','$orderTotal','$orderDate','$returnDate','open','web','".date('Y-m-d H:i:s')."')");

				$db->db_exec("insert into inv_orders_details (order_id,first_name,last_name,phone_number,address1,address2,city,state,country,zip,payment_method,shipping_cost,dateofmodification)
                         values ('$orderId' , '$firstname' , '$lastname' , '$phone' , '$add1' , '$add2' ,'$city' , '$state' , '$country' , '$zip' , '$paymentMethod' , '$shipping_cost' ,'".date('Y-m-d H:i:s')."')");

				foreach($order['Items'] as $order_item){
					$qty = $order_item['product_qty'];
					$transaction_id  = $order_item['order_item_id'];
					$product_sku    = $order_item['product_sku'];
					$product_price   = $order_item['product_price'];
					$product_price   = str_replace(',','',(string)$product_price);

					$db->db_exec("insert into inv_orders_items(order_id,order_item_id,product_sku,product_price,product_qty,dateofmodification)
								  values ('$orderId','$transaction_id','$product_sku','$product_price','$qty','".date('Y-m-d H:i:s')."')");
				}
			}
		}
		else{
			//$db->db_exec("Update inv_orders SET is_updated = 1 ,fishbowl_uploaded = 0 , order_status = '$orderStatus' Where id = '$orderExist'");
		}
	}

	$cronDate = date('Y-m-d H:i:s');
	$db->db_exec("update configuration set config_value = '$cronDate' where config_key = 'WEB_LAST_CRON_TIME' ");

	return "Orders inserted successfully.";
}

function getLastCronDate(){
	global $db;

	$startDate = $db->func_query_first_cell("select config_value  from configuration where config_key = 'WEB_LAST_CRON_TIME'");
	if(!intval($startDate)){
		$startDate = date('Y-m-d H:i:s');
	}
	else{
		return $startDate;
	}
}

if($_REQUEST['m'] == 1){
	$_SESSION['message'] = "Order imported successfully";
	header("Location:$host_path/order.php");
}

echo "success";
?>