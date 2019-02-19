<?php
set_time_limit(0);
ini_set("memory_limit", "20000M");
include_once("../config.php");
// function getLBBSKU($sku)
// {
// 	global $db;
// 	$data = $db->func_query_first("SELECT * FROM inv_buyback_skus WHERE sku='".$sku."'");
// 	if($data)
// 	{
// 		return $data;
// 	}
// 	else
// 	{
// 		return false;
// 	}
// }
function getLBBSKU($sku)
{
	global $db;
	$data = $db->func_query_first("SELECT * FROM inv_buy_back WHERE sku='".$sku."'");
	if($data)
	{
		return $data;
	}
	else
	{
		return false;
	}
}
$limit = 25;
//No LBB transfer into fishbowl Gohar
/*$orders = $db->func_query("select buyback_id as id,shipment_number as package_number,date_added,date_received as date_issued,0.00 as shipping_cost from oc_buyback where status in ('In QC','Completed') and fb_added = 0 and ignored = 0 order by date_completed DESC limit $limit");*/
if(count($orders) == 0){
	//echo "NO";
	//exit;
}

foreach($orders as $index => $order){
	$shipment_id = $order['id'];
	$shipment_query  = "SELECT * from oc_buyback_products WHERE buyback_id='".$order['id']."'";
	$shipment_result = $db->func_query($shipment_query);
	$orders[$index]['order_type']  = 'shipment';
	foreach($shipment_result as $shipment_item){
		
		$shipment_details = $db->func_query_first("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$shipment_item['buyback_product_id']."' and fb_added=0");
		
		$lbb_sku = getLBBSKU($shipment_item['sku']);
		
		if(!$lbb_sku or !$shipment_details){
			// exit;
		}
		else
		{
			// $oem = $lbb_sku['oem'];
			// $non_oem = $lbb_sku['non_oem'];
			// $reject_lcd_ok = $lbb_sku['reject_lcd_ok'];
			// $reject_lcd_blemish = $lbb_sku['reject_lcd_blemish'];
			// $reject_lcd_damanged = $lbb_sku['reject_lcd_damanged'];
			$oem_a =$lbb_sku['oem_a_desc'];
			$oem_b =$lbb_sku['oem_b_desc'];
			$oem_c =$lbb_sku['oem_c_desc'];
			$oem_d =$lbb_sku['oem_d_desc'];
			$non_oem_a =$lbb_sku['non_oem_a_desc'];
			$non_oem_b =$lbb_sku['non_oem_b_desc'];
			$non_oem_c =$lbb_sku['non_oem_c_desc'];
			$non_oem_d =$lbb_sku['non_oem_d_desc'];
			$salvage =$lbb_sku['salvage_desc'];
		}
		$fb_product_added = false;
		if($shipment_details['oem_qty_a'] && $oem_a)
		{
			$fb_product_added = true;
			$orders[$index]['Items'][] = array(
				'product_sku' => $oem_a ,
				'qty_received' => $shipment_details['oem_qty_a'] ,
				'unit_price' => $shipment_item['oem_a_price'] ,
				);
		}
		if($shipment_details['oem_qty_b'] && $oem_b)
		{
			$fb_product_added = true;
			$orders[$index]['Items'][] = array(
				'product_sku' => $oem_b ,
				'qty_received' => $shipment_details['oem_qty_b'] ,
				'unit_price' => $shipment_item['oem_b_price'] ,
				);
		}
		if($shipment_details['oem_qty_c'] && $oem_c)
		{
			$fb_product_added = true;
			$orders[$index]['Items'][] = array(
				'product_sku' => $oem_c ,
				'qty_received' => $shipment_details['oem_qty_c'] ,
				'unit_price' => $shipment_item['oem_c_price'] ,
				);
		}
		if($shipment_details['oem_qty_d'] && $oem_d)
		{
			$fb_product_added = true;
			$orders[$index]['Items'][] = array(
				'product_sku' => $oem_d ,
				'qty_received' => $shipment_details['oem_qty_d'] ,
				'unit_price' => $shipment_item['oem_d_price'] ,
				);
		}
		if($shipment_details['non_oem_qty_a'] && $non_oem_a)
		{
			$fb_product_added = true;
			$orders[$index]['Items'][] = array(
				'product_sku' => $non_oem_a ,
				'qty_received' => $shipment_details['non_oem_qty_a'] ,
				'unit_price' => $shipment_item['non_oem_a_price'] ,
				);
		}
		if($shipment_details['non_oem_qty_b'] && $non_oem_b)
		{
			$fb_product_added = true;
			$orders[$index]['Items'][] = array(
				'product_sku' => $non_oem_b ,
				'qty_received' => $shipment_details['non_oem_qty_b'] ,
				'unit_price' => $shipment_item['non_oem_b_price'] ,
				);
		}
		if($shipment_details['non_oem_qty_c'] && $non_oem_c)
		{
			$fb_product_added = true;
			$orders[$index]['Items'][] = array(
				'product_sku' => $non_oem_c ,
				'qty_received' => $shipment_details['non_oem_qty_c'] ,
				'unit_price' => $shipment_item['non_oem_c_price'] ,
				);
		}
		if($shipment_details['non_oem_qty_d'] && $non_oem_d)
		{
			$fb_product_added = true;
			$orders[$index]['Items'][] = array(
				'product_sku' => $non_oem_d ,
				'qty_received' => $shipment_details['non_oem_qty_d'] ,
				'unit_price' => $shipment_item['non_oem_d_price'] ,
				);
		}
		if($shipment_details['salvage_qty'] && $salvage)
		{
			$fb_product_added = true;
			$orders[$index]['Items'][] = array(
				'product_sku' => $salvage ,
				'qty_received' => $shipment_details['salvage_qty'] ,
				'unit_price' => $shipment_item['salvage_price'] ,
				);
		}
		if($fb_product_added)
		{
			$db->db_exec("UPDATE inv_buyback_shipments SET fb_added=1 WHERE id='".$shipment_details['id']."'");
		}
	}
}
// echo 'here';exit;
// var_dump($orders);exit;
print_r(serialize($orders));