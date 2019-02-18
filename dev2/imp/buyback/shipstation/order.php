<?php
include_once '../../config.php';
include_once '../../inc/functions.php';

$action = $_GET ['action'];

function addBBComment($shipment_number,$comment)
{
	
	global $db;
	$buyback_id = $db->func_query_first_cell("SELECT buyback_id FROM oc_buyback WHERE shipment_number='".$shipment_number."'");
	$data = array();
//	$data['customer_id'] = $customer_id;
	$data['comment'] = $db->func_escape_string($comment);
	$data['buyback_id'] = $buyback_id;
	$data['user_id'] = 0;
	//$data['email'] = $oldEmail;
	$data['date_added'] = date('Y-m-d H:i:s');

	$db->func_array2insert("inv_buyback_comments",$data);	
		
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
	
	$total = $db->func_query_first_cell ( "select count(buyback_id) from oc_buyback where for_shipstation = 1 ");
	
	$pages = ceil ( ($total) / $limit );
	
	$_query = "select * FROM oc_buyback WHERE for_shipstation=1 ORDER BY buyback_id DESC";
	
	$orders = $db->func_query ( $_query );

	if (count ( $orders ) > 0) {
		foreach ( $orders as $index => $order ) {
			$orders [$index] ['Items'] = $db->func_query ( "select * from oc_buyback_products where buyback_id = '" . $order ['buyback_id'] . "' AND for_shipstation=1 and shipstation_uploaded=0" );
			$orders [$index] ['Company'] = '';
			
		}
	}
	// echo "<pre>";
	// print_r($orders);
	// exit;

	$ordes_xml = '<?xml version="1.0" encoding = "utf-8"?>'
	.'<Orders pages="' . $pages . '">';

	foreach ( $orders as $order ) {
		
		
		if($order['customer_id']==0)
					{
						
						$firstname = $order['firstname'];
						$lastname = $order['lastname'];
						$email = $order['email'];
						$telephone = $order['telephone'];
						$address_1 = $order['address_1'];
						$city = $order['city'];
						$postcode = $order['postcode'];
						$zone_id = $order['zone_id'];
					}
					else
					{
						
						$customer_detail = $db->func_query_first("SELECT email,telephone FROM oc_customer WHERE customer_id='".$order['customer_id']."'");
						$address = $db->func_query_first("SELECT * FROM oc_address WHERE address_id='".$order['address_id']."'");
						
						
						$firstname = $address['firstname'];
						$lastname = $address['lastname'];
						$email = $customer_detail['email'];
						$telephone = $customer_detail['telephone'];
						$address_1 = $address['address_1'];
						$city = $address['city'];
						$postcode = $address['postcode'];
						$zone_id = $address['zone_id'];
					}
		$zone = $db->func_query_first_cell("SELECT name FROM oc_zone WHERE zone_id='".(int)$zone_id."'");
			$ordes_xml .= '<Order>'
			.'<OrderID><![CDATA[' . $order ['shipment_number'] . ']]></OrderID>'
			.'<OrderNumber><![CDATA[' . $order ['shipment_number'] . ']]></OrderNumber>'
			.'<OrderDate>' . date ( 'm/d/Y H:i A', strtotime ( $order ['date_added'] ) ) . '</OrderDate>'
			.'<OrderStatus><![CDATA[lbb_return]]></OrderStatus>'
			.'<LastModified>' . date ( 'm/d/Y H:i A', strtotime ( $order ['date_completed'] ) ) . '</LastModified>'
			.'<ShippingMethod><![CDATA[' . $order ['shipping_method'] . ']]></ShippingMethod>'
			.'<PaymentMethod><![CDATA[' . $order ['payment_type'] . ']]></PaymentMethod>'
			.'<OrderTotal>' . round ( $order ['order_price'], 2 ) . '</OrderTotal>'
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
				
				$quantities = $db->func_query_first("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$item['buyback_product_id']."'");
			
			//if($item['buyback_product_id']=='159') print_r($quantities);exit;
			if($quantities)
			{
				//$oem_qty = (int)$quantities['oem_received'];
				//$non_oem_qty = (int)$quantities['non_oem_received'];
				$rejected_qty = (int)$quantities['rejected_qty'];	
			}
			//if(!$rejected_qty) $rejected_qty = 1;
			//$unit = $item['total_rejected_total'] / $rejected_qty;
				$ordes_xml .= '<Item>'
				.'<SKU><![CDATA[' . $item ['sku'] . ']]></SKU>'
				.'<Name><![CDATA[' . $item['description'] . ']]></Name>'
				.'<ImageUrl></ImageUrl>'
				.'<Weight>0</Weight>'
				.'<WeightUnits>Ounces</WeightUnits>'
				.'<Quantity>' . $rejected_qty . '</Quantity>'
				.'<UnitPrice>0.00</UnitPrice>'
				.'<Location></Location>'
				.'<Options />'
				.'</Item>';
				
				
			$db->db_exec ( "update oc_buyback_products SET shipstation_uploaded = 1 where buyback_product_id = '" . $item ['buyback_product_id'] . "'" );
			}

			$ordes_xml .= '</Items></Order>';
			$db->db_exec("UPDATE oc_buyback SET for_shipstation=0 WHERE buyback_id='".$order['buyback_id']."'");
		}
		
	

	$ordes_xml .= '</Orders>';

	header ( "Content-type:text/xml" );
	echo $ordes_xml;
}