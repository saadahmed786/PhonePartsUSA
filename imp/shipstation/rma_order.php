<?php
include_once '../config.php';
include_once '../inc/functions.php';

$action = $_GET ['action'];
if ($action == 'shipnotify') {
	$order_number    = $db->func_escape_string($_GET['order_number']);
	$tracking_number = $db->func_escape_string($_GET['tracking_number']);
	$carrier = $db->func_escape_string($_GET['carrier']);
	$service = $db->func_escape_string($_GET['service']);
	
	$updateOrder = array();
	$updateOrder['date_modified'] = date('Y-m-d H:i:s');
	$updateOrder['order_status']    = 'Shipped';
	$updateOrder['tracking_number'] = $tracking_number;
	$updateOrder['comments'] = "$carrier $service - Tracking No $tracking_number";
	
	$db->func_array2update("inv_customer_return_orders",$updateOrder, " order_number = '$order_number' ");
	
	    $_order = explode("-",$order_number);
    $_order_id = $_order[1];
    $_rma_number = $_order[0];
    $db->db_exec("DELETE FROM `inv_return_shipment_box_items` WHERE order_id='".$_order_id."' AND rma_number='".$_rma_number."'");

	
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
	
	$total = $db->func_query_first_cell ( "select count(id) from  inv_customer_return_orders where (shipstation_added = 0 OR shipstation_added is null) OR (date_added >= '$start_date' and date_added <= '$end_date')" );
	$pages = ceil ( $total / $limit );
	
	$_query = "select * from inv_customer_return_orders where (shipstation_added = 0 OR shipstation_added is null) OR (date_added >= '$start_date' and date_added <= '$end_date') order by date_added DESC limit $start , $limit";
	$orders = $db->func_query ( $_query );
	
	if (count ( $orders ) > 0) {
		foreach ( $orders as $index => $order ) {
			$orders [$index] ['Items'] = $db->func_query ( "select * from  inv_customer_return_order_items where customer_return_order_id = '" . $order ['id'] . "'" );
		}
	}
	
	$ordes_xml = '<?xml version="1.0" encoding = "utf-8"?>
			  <Orders pages="' . $pages . '">';
	
	foreach ( $orders as $order ) {
		$ordes_xml .= '<Order>
				  	  <OrderID><![CDATA[' . $order ['order_number'] . ']]></OrderID>
				  	  <OrderNumber><![CDATA[' . $order ['order_number'] . ']]></OrderNumber>
				      <OrderDate><![CDATA[' . date ( 'm/d/Y H:i A', strtotime ( $order ['date_added'] ) ) . ']]></OrderDate>
				      <OrderStatus><![CDATA[' . $order ['order_status'] . ']]></OrderStatus>
				      <LastModified><![CDATA[' . date ( 'm/d/Y H:i A', strtotime ( $order ['date_modified'] ) ) . ']]></LastModified>
				      <ShippingMethod><![CDATA[' . $order ['shipping_method'] . ']]></ShippingMethod>
				      <PaymentMethod><![CDATA[' . $order ['payment_method'] . ']]></PaymentMethod>
				      <OrderTotal>' . number_format ( $order ['order_price'], 2 ) . '</OrderTotal>
				      <TaxAmount>0.00</TaxAmount>
				      <ShippingAmount>' . number_format ( $order ['shipping_cost'], 2 ) . '</ShippingAmount>
				      <CustomerNotes></CustomerNotes>
				      <InternalNotes></InternalNotes>
                      <Customer>
			             <CustomerCode><![CDATA[' . $order ['email'] . ']]></CustomerCode>
						 <BillTo>
							<Name><![CDATA[' . $order ['first_name'] . ' ' . $order ['last_name'] . ']]></Name>
							<Company></Company>
							<Phone><![CDATA[' . $order ['phone_number'] . ']]></Phone>
							<Email><![CDATA[' . $order ['email'] . ']]></Email>
						 </BillTo>
						 <ShipTo>
							<Name><![CDATA[' . $order ['first_name'] . ' ' . $order ['last_name'] . ']]></Name>
							<Company><![CDATA[]]></Company>
							<Address1><![CDATA[' . $order ['address1'] . ']]></Address1>
							<Address2></Address2>
							<City><![CDATA[' . $order ['city'] . ']]></City>
							<State><![CDATA[' . $order ['state'] . ']]></State>
							<PostalCode><![CDATA[' . $order ['zip'] . ']]></PostalCode>
							<Country><![CDATA[' . substr ( $order ['country'], 0, 2 ) . ']]></Country>
							<Phone><![CDATA[' . $order ['phone_number'] . ']]></Phone>
						</ShipTo>
					</Customer>
					<Items>';
		
		foreach ( $order ['Items'] as $item ) {
			$name = replaceSpecial ( getItemName ( $item ['product_sku'] ) );
			$ordes_xml .= '<Item>
							<SKU><![CDATA[' . $item ['order_item_id'] . ']]></SKU>
							<Name><![CDATA[' . $name . ']]></Name>
							<ImageUrl></ImageUrl>
							<Weight>0</Weight>
							<WeightUnits>Ounces</WeightUnits>
							<Quantity>' . $item ['product_qty'] . '</Quantity>
							<UnitPrice>' . number_format ( $item ['product_price'], 2 ) . '</UnitPrice>
							<Location></Location>
							<Options />
						</Item>';
		}
		
		$ordes_xml .= '</Items></Order>';
		
		$db->db_exec ( "update inv_customer_return_orders SET shipstation_added = 1 where id = '" . $order ['id'] . "'" );
	}
	
	$ordes_xml .= '</Orders>';
	
	header ( "Content-type:text/xml" );
	echo $ordes_xml;
}