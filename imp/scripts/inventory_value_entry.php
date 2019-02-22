<?php

include_once '../config.php';

include_once '../inc/functions.php';

ini_set('memory_limit','2048M');

ini_set('max_execution_time', 500); //300 seconds = 5 minutes

if ($_GET['date']) {

 $date = $_GET['date'];

} else {	

 $date = date('Y-m-d');

}



//print_r($date);exit;



$total_inventory_cost = 0.00;

$total_sale = 0.00;

$received_total = 0.00;





$inventory_cost_query = "Select p.sku, p.weight, p.quantity, p.price ,  pd.name from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id) Where p.sku != '' and p.sku<>'SIGN' and p.quantity>0 AND p.sku NOT IN (SELECT kit_sku FROM inv_kit_skus) group by p.sku order by p.sku asc ";

$results = $db->func_query($inventory_cost_query);

if($results){



		foreach($results as $i => $result){

			$true_cost = getTrueCost($result['sku']);

			if($result['quantity']<0) {

				$qty=0;	

			}

			else

			{

				$qty=$result['quantity'];	

			}

			$total_inventory_cost+=$qty * $true_cost;

		}

}

$sale_query = $db->func_query("Select oi.order_id, oi.product_qty, oi.product_sku, oi.product_true_cost from inv_orders_items oi inner join inv_orders o on (oi.order_id = o.order_id) where o.fishbowl_uploaded <> '0' AND date(o.order_date) = '$date' ");

if ($sale_query) {



	foreach ($sale_query as $sale) {

		$total_sale += $sale['product_qty'] * $sale['product_true_cost'];

	}

}

//$shipment_query = $db->func_query("Select s.status, s.shipping_cost, si.product_sku, si.qty_received, si.qty_shipped ,si.unit_price from inv_shipment_items si inner join inv_shipments s on (si.shipment_id = s.id) where (s.status = 'Issued' OR s.status = 'Received') AND date_added LIKE '%$date%' ");

$shipment_query = $db->func_query("Select id, ex_rate, shipping_cost,status from inv_shipments where  status = 'Completed' AND date(date_completed)=  '$date' ");

	$total_rec = 0;

if ($shipment_query) {

	foreach ($shipment_query as $shipment) {

		$shipment_data = $db->func_query("Select product_sku, qty_received, qty_shipped ,unit_price from inv_shipment_items where shipment_id = '".$shipment['id']."' ");

		$received_total = 0;

		foreach ($shipment_data as $data) {

			if ($shipment_query['status'] == 'Issued') {

				$received_total += $data['qty_shipped'] * $data['unit_price'];

			} else {

				$received_total += $data['qty_received'] * $data['unit_price'];

			}

		}

		$received_total += $shipment['shipping_cost'];

		$received_total = $received_total/$shipment['ex_rate'];

		$total_rec += $received_total; 

	}

}



$check = $db->func_query_first_cell("Select id from inv_daily_inventory_value where date_added =  '$date' ");



$update_data = array();

$update_data['inventory_cost'] = round($total_inventory_cost,2);

$update_data['total_sale'] = round($total_sale,2);

$update_data['total_received'] = round($total_rec,2);

if ($check!='') {

	$db->func_array2update("inv_daily_inventory_value",$update_data,"id = '$check'");

} else {

	$update_data['date_added'] = $date;
	

	$check = $db->func_array2insert('inv_daily_inventory_value', $update_data);

}

echo 'Value entered for '.$date .'DB Entry = '.$check;



?>