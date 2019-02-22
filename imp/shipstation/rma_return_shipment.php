<?php
include_once '../config.php';
include_once '../inc/functions.php';

$action = $_GET ['action'];

function addBBComment($shipment_number,$comment)
{
	
	global $db;
	$return_id = $db->func_query_first_cell("SELECT id FROM inv_returns WHERE rma_number='".$shipment_number."'");
	$data = array();
//	$data['customer_id'] = $customer_id;
	$data['comments'] = $db->func_escape_string($comment);
	$data['comment_date'] = date('Y-m-d H:i:s');

	$data['return_id'] = $return_id;
	$data['user_id'] = 0;
	//$data['email'] = $oldEmail;
	

	$db->func_array2insert("inv_return_comments",$data);	
		
}
if ($action == 'shipnotify') {
	
	$order_number    = $db->func_escape_string($_GET['order_number']);
	$tracking_number = $db->func_escape_string($_GET['tracking_number']);
	$carrier = $db->func_escape_string($_GET['carrier']);
	$service = $db->func_escape_string($_GET['service']);
	
	//$db->db_exec("update inv_orders set order_status = 'Shipped' where order_id = '$order_number'");
	
	addBBComment($order_number,"$carrier $service - Tracking No $tracking_number");
	
	
	echo "done";
	exit;
}
else {



	$start_date = $db->func_escape_string ( $_GET ['start_date'] );
	$start_date = date ( "Y-m-d H:i:s", strtotime ( $start_date ) );
	
	$end_date = $db->func_escape_string ( $_GET ['end_date'] );
	$end_date = date ( "Y-m-d H:i:s", strtotime ( $end_date ) );
	
	$page = $db->func_escape_string ( $_GET ['page'] );
	
	$start = ($page - 1) * $limit;
	$limit = 10;
	
	$total = $db->func_query_first_cell ( "select count(rma_number) from inv_returns where for_shipstation = 1 ");
	
	$pages = ceil ( ($total) / $limit );
	
	$_query = "select * FROM inv_returns WHERE for_shipstation=1 ORDER BY id DESC";
	
	$orders = $db->func_query ( $_query );

	if (count ( $orders ) > 0) {
		foreach ( $orders as $index => $order ) {
			$orders [$index] ['Items'] = $db->func_query ( "select * from inv_return_items where return_id = '" . $order ['id'] . "' AND for_shipstation=1 and shipstation_uploaded=0" );
			$orders [$index] ['Company'] = '';
			
		}
	}
	// echo "<pre>";
	// print_r($orders);
	// exit;

	$ordes_xml = '<?xml version="1.0" encoding = "utf-8"?>'
	.'<Orders pages="' . $pages . '">';

	foreach ( $orders as $order ) {
		
		
		$order_detail = getOrder($order['order_id']);
						
						$firstname = $order_detail['firstname'];
						$lastname = $order_detail['lastname'];
						$email = $order_detail['email'];
						$telephone = $order_detail['phone'];
						$address_1 = $order_detail['address1'];
						$city = $order_detail['city'];
						$postcode = $order_detail['zip'];
						$zone = $order_detail['state'];
					
					
		//$zone = $db->func_query_first_cell("SELECT name FROM oc_zone WHERE zone_id='".(int)$zone_id."'");
			$ordes_xml .= '<Order>'
			.'<OrderID><![CDATA[' . $order ['rma_number'].'-'.$order['order_id'] . ']]></OrderID>'
			.'<OrderNumber><![CDATA[' . $order ['rma_number'] . ']]></OrderNumber>'
			.'<OrderDate>' . date ( 'm/d/Y H:i A', strtotime ( $order ['date_added'] ) ) . '</OrderDate>'
			.'<OrderStatus><![CDATA[rma_return]]></OrderStatus>'
			.'<LastModified>' . date ( 'm/d/Y H:i A', strtotime ( $order ['date_completed'] ) ) . '</LastModified>'
			.'<ShippingMethod><![CDATA[' . $order ['shipping_method'] . ']]></ShippingMethod>'
			.'<PaymentMethod><![CDATA[' . $order ['payment_type'] . ']]></PaymentMethod>'
			.'<OrderTotal>0.00</OrderTotal>'
			.'<TaxAmount>0.00</TaxAmount>'
			.'<ShippingAmount>' . round ( $order ['shipping_cost'], 2 ) . '</ShippingAmount>'
			.'<CustomerNotes></CustomerNotes>'
			.'<InternalNotes></InternalNotes>'
			.'<Customer>'
			.'<CustomerCode><![CDATA[' . $email . ']]></CustomerCode>'
			.'<BillTo>'
			.'<Name><![CDATA[' . $firstname . ' ' . $lastname . ']]></Name>'
			.'<Company></Company>'
			.'<Phone><![CDATA[' . $telephone . ']]></Phone>'
			.'<Email><![CDATA[' . $email. ']]></Email>'
			.'</BillTo>'
			.'<ShipTo>'
			.'<Name><![CDATA[' . $firstname . ' ' . $lastname . ']]></Name>'
			.'<Company><![CDATA[]]></Company>'
			.'<Address1><![CDATA[' . $address_1 . ']]></Address1>'
			.'<Address2></Address2>'
			.'<City><![CDATA[' . $city . ']]></City>'
			.'<State><![CDATA[' . $zone . ']]></State>'
			.'<PostalCode><![CDATA[' . $postcode . ']]></PostalCode>'
			.'<Country><![CDATA[US]]></Country>'
			.'<Phone><![CDATA[' . $telephone . ']]></Phone>'
			.'</ShipTo>'
			.'</Customer>'
			.'<Items>';

			foreach ( $order ['Items'] as $item ) {
				//$name = replaceSpecial ( getItemName ( $item ['product_sku'] ) );
				
			// 	$quantities = $db->func_query_first("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$item['buyback_product_id']."'");
			
			// //if($item['buyback_product_id']=='159') print_r($quantities);exit;
			// if($quantities)
			// {
			// 	//$oem_qty = (int)$quantities['oem_received'];
			// 	//$non_oem_qty = (int)$quantities['non_oem_received'];
			// 	$rejected_qty = (int)$quantities['rejected_qty'];	
			// }
			//if(!$rejected_qty) $rejected_qty = 1;
			//$unit = $item['total_rejected_total'] / $rejected_qty;
				$ordes_xml .= '<Item>'
				.'<SKU><![CDATA[' . $item ['sku'] . ']]></SKU>'
				.'<Name><![CDATA[' . replaceSpecial($item['title']) . ']]></Name>'
				.'<ImageUrl></ImageUrl>'
				.'<Weight>0</Weight>'
				.'<WeightUnits>Ounces</WeightUnits>'
				.'<Quantity>1</Quantity>'
				.'<UnitPrice>0.00</UnitPrice>'
				.'<Location></Location>'
				.'<Options />'
				.'</Item>';
				
				
			$db->db_exec ( "update inv_return_items SET shipstation_uploaded = 1 where id = '" . $item ['id'] . "'" );
			}

			$ordes_xml .= '</Items></Order>';
			$db->db_exec("UPDATE inv_returns SET for_shipstation=0 WHERE id='".$order['id']."'");
		}
		
	

	$ordes_xml .= '</Orders>';

	header ( "Content-type:text/xml" );
	echo $ordes_xml;
}