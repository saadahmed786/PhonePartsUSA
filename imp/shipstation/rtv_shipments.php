<?php
include_once '../config.php';
include_once '../inc/functions.php';
include_once '../inventory/class.php';
	

$action = $_GET ['action'];
if ($action == 'shipnotify') {
	
	$order_number    = $db->func_escape_string($_GET['order_number']);
	$tracking_number = $db->func_escape_string($_GET['tracking_number']);
	$carrier = $db->func_escape_string($_GET['carrier']);
	$service = $db->func_escape_string($_GET['service']);
	
	$db->db_exec("update inv_rejected_shipments set status = 'Shipped' where rejected_shipment_id = '$order_number'");
	
	// $addcomment = array();
	// $addcomment['date_added'] = date('Y-m-d H:i:s');
	// $addcomment['user_id']   = 0;
	// $addcomment['comment']   = "$carrier $service - Tracking No $tracking_number";
	// $addcomment['order_id']  = $order_number;
	
	// $order_history_id = $db->func_array2insert("oc_order_history",$addcomment);
	
	// $order_mod_logs = array();
	// $order_mod_logs['order_history_id'] = $order_history_id;
	// $order_mod_logs['order_id'] = $order_number;
	// $order_mod_logs['user_id']  = 0;
	// $order_mod_logs['date_modified']  = date('Y-m-d H:i:s');
	
	// $db->func_array2insert("oc_order_mod_logs",$order_mod_logs);
	// $inventory->updateInventoryShipped($order_number,'shipped');

	
	echo "done";
	exit;
}
else {

	$_query = "select * from inv_rejected_shipments where (shipstation_added = 0 OR shipstation_added is null) and status = 'Issued' order by date_added DESC limit 10";
	$shipments = $db->func_query ($_query);
	if (count ( $shipments ) > 0) {
		foreach ( $shipments as $index => $shipment ) {
			$shipments [$index] ['Items'] = $db->func_query ( "select * from inv_rejected_shipment_items where rejected_shipment_id = '" . $shipment['id'] . "' and deleted = '0'" );
			$shipments [$index] ['total_cost'] = $db->func_query_first_cell ( "select sum(cost) from inv_rejected_shipment_items where rejected_shipment_id = '" . $shipment['id'] . "' and deleted = '0'" );
			$shipments [$index] ['Address'] = $db->func_query_first( "select * from inv_users where id = '" . $shipment['vendor'] . "'" );
		}
	}
	
	
	$shipments_xml = '<?xml version="1.0" encoding = "utf-8"?>
	<Orders pages="1">';

		foreach ( $shipments as $shipment ) {
			if(strtolower(trim($shipment['Address']['country'])) == 'united states')
			{
				$shipment['Address']['country'] = 'US';
			}
			if(substr(strtolower($shipment['Address']['country']),0,2)=='un')
			{
				$shipment['Address']['country'] = 'US';
			}
			
			$shipments_xml .= '<Order>
			<OrderID><![CDATA[' . $shipment ['package_number'] . ']]></OrderID>
			<OrderNumber><![CDATA[' . $shipment ['package_number'] . ']]></OrderNumber>
			<OrderDate>' . date ( 'm/d/Y H:i A', strtotime ( $shipment ['date_added'] ) ) . '</OrderDate>
			<OrderStatus><![CDATA[' . strtolower($shipment ['status']) . ']]></OrderStatus>
			<LastModified>' . date ( 'm/d/Y H:i A', strtotime ( $shipment ['date_issued'] ) ) . '</LastModified>
			<ShippingMethod><![CDATA[' . $shipment ['carrier'] . ']]></ShippingMethod>
			<PaymentMethod><![CDATA[CASH]]></PaymentMethod>
			<OrderTotal>' . round ( $shipment ['total_cost'], 2 ) . '</OrderTotal>
			<TaxAmount>0.00</TaxAmount>
			<ShippingAmount>' . round ( $shipment ['shipping_cost'], 2 ) . '</ShippingAmount>
			<CustomerNotes></CustomerNotes>
			<InternalNotes></InternalNotes>
			<Customer>
				<CustomerCode><![CDATA[' . $shipment['Address']['email'] . ']]></CustomerCode>
				<BillTo>
					<Name><![CDATA[' . $shipment['Address']['name'] . ']]></Name>
					<Company>'.$shipment['Address']['company_name'].'</Company>
					<Phone><![CDATA[' . $shipment['Address']['phone_no'] . ']]></Phone>
					<Email><![CDATA[' . $shipment['Address']['email'] . ']]></Email>
				</BillTo>
				<ShipTo>
					<Name><![CDATA[' . $shipment['Address']['name'] . ']]></Name>
					<Company>'.$shipment['Address']['company_name'].'</Company>
					<Address1><![CDATA[' . $shipment['Address']['address_1'] . ']]></Address1>
					<Address2><![CDATA[' . $shipment['Address']['address_2'] . ']]></Address2>
					<City><![CDATA[' . $shipment['Address']['city'] . ']]></City>
					<State><![CDATA[' . $shipment['Address']['providence'] . ']]></State>
					<PostalCode><![CDATA[' . $shipment['Address']['postal_code'] . ']]></PostalCode>
					<Country><![CDATA[' . substr ( $shipment['Address']['country'], 0, 2 ) . ']]></Country>
					<Phone><![CDATA[' . $shipment['Address']['phone_no'] . ']]></Phone>
				</ShipTo>
			</Customer>
			<Items>';

				foreach ( $shipment['Items'] as $item ) {
					$name = replaceSpecial(getItemName($item['product_sku']));
					$shipments_xml .= '<Item>
					<SKU><![CDATA[' . $item ['product_sku'] . ']]></SKU>
					<Name><![CDATA[' . $name . ']]></Name>
					<ImageUrl></ImageUrl>
					<Weight>0</Weight>
					<WeightUnits>Ounces</WeightUnits>
					<Quantity>' . $item['qty_rejected'] . '</Quantity>
					<UnitPrice>' . round ($item['cost'],2) . '</UnitPrice>
					<Location></Location>
					<Options />
					</Item>';
			}

			$shipments_xml .= '</Items></Order>';

			//$db->db_exec ("update inv_rejected_shipments SET shipstation_added = 1 where id = '" . $shipment['id'] . "'");
		}

		$shipments_xml .= '</Orders>';

		header ( "Content-type:text/xml" );
		echo $shipments_xml;
	}