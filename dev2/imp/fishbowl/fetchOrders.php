<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");
include_once("../config.php");

$order_type = $_REQUEST['order_type'];
$limit = 25;

if($order_type == 'SAVESO'){
	if(isset($_REQUEST['shipped']) AND $_REQUEST['shipped'] == 1){
		$orders = $db->func_query("select order_id , store_type from inv_orders where fishbowl_uploaded = 1 and order_status = 'Shipped' 
								   and fb_shipped = 0 and ignored = 0 and order_date > '2014-12-15 15:00:00' order by order_date DESC limit $limit");
	}
	else{
		$_query = "select o.* , od.* from inv_orders o inner join inv_orders_details od on o.order_id = od.order_id 
								   where ((fullfill_type != 'ByAmazon' && fullfill_type != 'Amazon FBA US') OR fullfill_type is null or o.payment_source='Replacement') AND (fishbowl_uploaded = 0 OR is_updated =  1) and (ignored = 0 OR try_count < 5) and 
								   order_date > '2013-09-14 00:00:00' and order_status not in( 'On Hold','Estimate') group by o.order_id order by order_date DESC limit $limit";
		$orders = $db->func_query($_query);
	}

	if(count($orders) == 0){
		echo "NO";
		exit;
	}
	else{
		if(!isset($_REQUEST['shipped'])){
			foreach($orders as $index => $order){
				$items_array = array();
				$items_array = $db->func_query("select * from inv_orders_items where order_id = '".$order['order_id']."'");
				

				$is_fba_order = $db->func_query_first_cell("SELECT a.is_fbb FROM inv_po_customers a,inv_orders b where a.id=b.po_business_id AND b.order_id='".$order['order_id']."' ");				
				if(empty($items_array) )
					{
						unset($orders[$index]);
						continue;
					}


				$orders[$index]['Items'] = $db->func_query("select * from inv_orders_items where order_id = '".$order['order_id']."'");
			}
		}
	}
}
else{
	$orders = $db->func_query("select o.order_id , o.store_type from inv_return_orders o where fishbowl_uploaded = 0 and ignored = 0 and order_date > '2013-09-14 00:00:00' group by o.order_id order by order_date DESC limit 50");
	if(count($orders) == 0){
		echo "NO";
		exit;
	}
}

//print_r(json_encode($orders));
print_r(serialize($orders));