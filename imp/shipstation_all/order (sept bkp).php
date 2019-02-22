<?php
include_once '../config.php';
include_once '../inc/functions.php';
include("../phpmailer/class.smtp.php");
include("../phpmailer/class.phpmailer.php");

$action = $_GET ['action'];
if ($action == 'shipnotify') {
	
	$order_number    = $db->func_escape_string($_GET['order_number']);
	$tracking_number = $db->func_escape_string($_GET['tracking_number']);
	$carrier = $db->func_escape_string($_GET['carrier']);
	$service = $db->func_escape_string($_GET['service']);
	
	$db->db_exec("update inv_orders set order_status = 'Shipped' where order_id = '$order_number'");
	
	$addcomment = array();
	$addcomment['date_added'] = date('Y-m-d H:i:s');
	$addcomment['user_id']   = 0;
	$addcomment['comment']   = "$carrier $service - Tracking No $tracking_number";
	$addcomment['order_id']  = $order_number;
	
	$order_history_id = $db->func_array2insert("oc_order_history",$addcomment);
	
	$order_mod_logs = array();
	$order_mod_logs['order_history_id'] = $order_history_id;
	$order_mod_logs['order_id'] = $order_number;
	$order_mod_logs['user_id']  = 0;
	$order_mod_logs['date_modified']  = date('Y-m-d H:i:s');
	
	$db->func_array2insert("oc_order_mod_logs",$order_mod_logs);
	
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
	$limit = 50;
	
	// $total = $db->func_query_first_cell ( "select count(id) from inv_orders where (shipstation_added = 0 OR shipstation_added is null) and order_status<>'Estimate' and order_date <= '$end_date'" );
	// $pages = ceil ($total / $limit);
	
	$_query = "select o.* , od.* from inv_orders o inner join inv_orders_details od on o.order_id = od.order_id
	where (shipstation_added = 0 OR shipstation_added is null) and o.store_type <> 'amazon_fba' and od.shipping_method NOT LIKE 'Local%' and order_status NOT IN ('Estimate','On Hold') and ss_ignore=0 and (order_date <= '$end_date')
	group by o.order_id order by order_date DESC limit $start , $limit";
	
	$orders = $db->func_query ( $_query );

	if (count ( $orders ) > 0) {
		$total=0;
		foreach ( $orders as $index => $order ) {
			$orders [$index] ['Items'] = $db->func_query ( "select * from inv_orders_items where order_id = '" . $order ['order_id'] . "'" );
			$orders [$index] ['Company'] = '';
			if ($order['store_type'] == 'po_business') {
				$orders [$index] ['Company'] = $db->func_query_first_cell ( "SELECT company_name FROM inv_po_customers WHERE id='".$order['po_business_id']."'" );
			}
			$total++;
		}
	}
	$pages = ceil($total/$limit);

	$ordes_xml = '<?xml version="1.0" encoding = "utf-8"?>'
	.'<Orders pages="' . $pages . '">';

	foreach ( $orders as $order ) {
		
		if (whiteList($order, 1) == 3) {
			$ordes_xml .= '<Order>'
			.'<OrderID><![CDATA[' . $order ['order_id'] . ']]></OrderID>'
			.'<OrderNumber><![CDATA[' . $order ['order_id'] . ']]></OrderNumber>'
			.'<OrderDate>' . date ( 'm/d/Y H:i A', strtotime ( $order ['order_date'] ) ) . '</OrderDate>'
			.'<OrderStatus><![CDATA[' . strtolower($order ['order_status']) . ']]></OrderStatus>'
			.'<Source><![CDATA[' . strtolower($order ['store_type']) . ']]></Source>'
			.'<LastModified>' . date ( 'm/d/Y H:i A', strtotime ( $order ['dateofmodification'] ) ) . '</LastModified>'
			.'<ShippingMethod><![CDATA[' . $order ['shipping_method'] . ']]></ShippingMethod>'
			.'<PaymentMethod><![CDATA[' . $order ['payment_method'] . ']]></PaymentMethod>'
			.'<OrderTotal>' . round ( $order ['order_price'], 2 ) . '</OrderTotal>'
			.'<TaxAmount>0.00</TaxAmount>'
			.'<ShippingAmount>' . number_format ( $order ['shipping_cost'], 2 ) . '</ShippingAmount>'
			.'<CustomerNotes></CustomerNotes>'
			.'<InternalNotes></InternalNotes>'
			.'<Customer>'
			.'<CustomerCode><![CDATA[' . $order ['email'] . ']]></CustomerCode>'
			.'<BillTo>'
			.'<Name><![CDATA[' . $order ['first_name'] . ' ' . $order ['last_name'] . ']]></Name>'
			.'<Company>'.$order['Company'].'</Company>'
			.'<Phone><![CDATA[' . $order ['phone_number'] . ']]></Phone>'
			.'<Email><![CDATA[' . $order ['email'] . ']]></Email>'
			.'</BillTo>'
			.'<ShipTo>'
			.'<Name><![CDATA[' . $order ['first_name'] . ' ' . $order ['last_name'] . ']]></Name>'
			.'<Company><![CDATA['.$order['Company'].']]></Company>'
			.'<Address1><![CDATA[' . $order ['address1'] . ']]></Address1>'
			.'<Address2></Address2>'
			.'<City><![CDATA[' . $order ['city'] . ']]></City>'
			.'<State><![CDATA[' . $order ['state'] . ']]></State>'
			.'<PostalCode><![CDATA[' . $order ['zip'] . ']]></PostalCode>'
			.'<Country><![CDATA[' . substr ( $order ['country'], 0, 2 ) . ']]></Country>'
			.'<Phone><![CDATA[' . $order ['phone_number'] . ']]></Phone>'
			.'</ShipTo>'
			.'</Customer>'
			.'<Items>';

			foreach ( $order ['Items'] as $item ) {
				$name = replaceSpecial ( getItemName ( $item ['product_sku'] ) );
				$ordes_xml .= '<Item>'
				.'<SKU><![CDATA[' . $item ['product_sku'] . ']]></SKU>'
				.'<Name><![CDATA[' . $name . ']]></Name>'
				.'<ImageUrl></ImageUrl>'
				.'<Weight>0</Weight>'
				.'<WeightUnits>Ounces</WeightUnits>'
				.'<Quantity>' . $item ['product_qty'] . '</Quantity>'
				.'<UnitPrice>' . number_format ( $item ['product_unit'], 2 ) . '</UnitPrice>'
				.'<Location></Location>'
				.'<Options>'
				.'</Options>'
				.'</Item>';
			}

			$ordes_xml .= '</Items></Order>';

			$db->db_exec ( "update inv_orders SET shipstation_added = 1 where order_id = '" . $order ['order_id'] . "'" );
		}
		else
		{

		}
		
	}

	$ordes_xml .= '</Orders>';

	header ( "Content-type:text/xml" );
	echo $ordes_xml;
}