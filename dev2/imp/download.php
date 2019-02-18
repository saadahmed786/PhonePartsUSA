<?php



include_once 'auth.php';

include_once 'inc/functions.php';



if($_GET['action'] == 'belowropcsv'){

	$reorder_setting = $db->func_query_first("select * from inv_reorder_settings");

	$vendor = $db->func_escape_string(strtolower($_GET['vendor']));



	$vendor_query = "";

	if($vendor)

	{

		$vendor_query =" AND LOWER(p.vendor)='".$vendor."' ";

	}



$shipments = $db->func_query("select shipment_id, product_sku , qty_shipped , package_number , date_issued , fb_added

	from  inv_shipments s inner join inv_shipment_items st on (s.id = st.shipment_id)

	where fb_added != 1 and (date_issued >= '2018-02-28 00:00:00' or date_issued='0000-00-00 00:00:00') AND status IN ('Received', 'Pending', 'Issued')");

	$inv_query  = "select p.is_main_sku, p.product_id , p.model, p.quantity, p.image, pd.name, p.mps,(p.mps*(select lead_time+safety_stock from inv_reorder_settings )) as rop,((p.mps * (select lead_time from inv_reorder_settings )) + ((p.mps*(select lead_time+safety_stock from inv_reorder_settings ))-p.quantity)) as qty_to_be_shipped from oc_product p inner join oc_product_description pd on
	(p.product_id = pd.product_id) where Lower(model) not like Lower('BKB-MOD-%')  and status = 1 and discontinue = 0 and is_blowout=0 and is_kit = 0 and (Lower(p.model) like Lower('%$keyword%') OR Lower(pd.name) like Lower('%$keyword%')) $vendor_query  group by p.sku  HAVING p.quantity<rop AND p.is_main_sku=1 order by name asc";

	$products   = $db->func_query($inv_query);



	$filename = "belowROP.csv";

	$fp = fopen($filename,"w");



	$heading = array("SKU","Title","Current Qty","ROP","Needed","Vendor","Latest True Cost");

	fputcsv($fp, $heading,',');



	if($products){

		foreach($products as $product){

			$mps =  $product['mps'];

			//$rop = getRop($mps , $reorder_setting['lead_time'] , $reorder_setting['qc_time'] , $reorder_setting['safety_stock']);
			$rop = $product['rop'];

			$rop = ceil($rop);
			
			if($rop>$product['quantity'])
					{

					$qty = ceil($product['qty_to_be_shipped']);
					}
					else
					{
						$qty = 0;
					}



// 			$qty = getQtyToBeShipped($rop , $product['quantity'] , $mps , $reorder_setting['lead_time'] , $reorder_setting['qc_time'] , $reorder_setting['additional_days'],$reorder_setting['safety_stock']);

			$qty = ceil($qty);



			$outstock_date = $db->func_query_first_cell("select outstock_date from inv_product_inout_stocks where product_sku = '".$product['model']."' order by date_modified desc limit 1");

					$stock_days = (int)$_GET['outofstock'];



					if((strtotime($outofstock)<strtotime('-30 days')) and $qty==0)

					{

						$qty=1;

					}



			//if($qty == 0){

			//	$qty = 5; // default ship 5 qty

			//}

			$true_cost = getTrueCost($product['model']);



			$shipment_data = getShipmentDetail($shipments , $product['model'] , $qty);



// 			if($product['quantity'] <= $rop && $shipment_data[1]>0 && $product['is_main_sku']==1){

				$row = array( $product['model'],$product['name'] , $product['quantity'] , $rop , $shipment_data[1],$product['vendor'],$true_cost);

				fputcsv($fp, $row,',');

// 			}

		}

	}



	fclose($fp);

}

elseif($_GET['action'] == 'shipment'){

	$shipment_id = (int)$_GET['shipment_id'];



	$shipment_detail = $db->func_query_first("select package_number from inv_shipments where id = '$shipment_id'");



	$filename = str_replace(array('/','-',','), '', $shipment_detail['package_number']).".csv";

	$fp = fopen($filename,"w");



	if($_SESSION['display_cost']){

		$heading = array("Title","SKU","QTY Shipped","Qty Received","Price","Is New");

	}

	else{

		$heading = array("Title","SKU","QTY Shipped","Qty Received","Is New");

	}



	fputcsv($fp, $heading,',');



	$shipment_items = $db->func_query("select * from inv_shipment_items where shipment_id = '$shipment_id' order by product_sku","product_id");

	foreach($shipment_items as $product_id => $shipment_item){

		if($shipment_item['is_new']){

			if($_SESSION['display_cost']){

				$row = array($shipment_item['product_name'] , $shipment_item['product_sku'], $shipment_item['qty_shipped'], $shipment_item['qty_received'] ,$shipment_item['unit_price'],"Yes");

			}

			else{

				$row = array($shipment_item['product_name'] , $shipment_item['product_sku'], $shipment_item['qty_shipped'], $shipment_item['qty_received'] ,"Yes");

			}

		}

		else{

			$title = $db->func_query_first_cell("select name from oc_product_description where product_id = '$product_id'");



			if($_SESSION['display_cost']){

				$row = array($title , $shipment_item['product_sku'], $shipment_item['qty_shipped'], $shipment_item['qty_received'] ,$shipment_item['unit_price'],"No");

			}

			else{

				$row = array($title , $shipment_item['product_sku'], $shipment_item['qty_shipped'], $shipment_item['qty_received'] ,"No");

			}

		}



		fputcsv($fp, $row,',');

	}



	fclose($fp);

}

elseif($_GET['action'] == 'rejected_shipment'){

	$shipment_id = (int)$_GET['shipment_id'];



	$shipment_detail = $db->func_query_first("select package_number from  inv_rejected_shipments where id = '$shipment_id'");



	$filename = $shipment_detail['package_number'].".csv";

	$fp = fopen($filename,"w");



	$heading = array("Shipment Number", "Reject ID", "SKU ","Product Name", "Date Added", "Date Updated");

	fputcsv($fp, $heading,',');



	$inv_query  = "select si.* , s.package_number,s.id as ShipmentId from inv_rejected_shipment_items si left join inv_shipments s on (si.shipment_id = s.id) where rejected_shipment_id = '$shipment_id' and si.deleted=0 order by shipment_id";

	$shipment_items = $db->func_query($inv_query);



	foreach($shipment_items as $product){

		$row = array($product['package_number'], $product['reject_item_id'], $product['product_sku'],getItemName($product['product_sku']), $db->func_query_first_cell("SELECT name FROM inv_rj_reasons WHERE id = '". $product['reject_reason'] ."'"), americanDate($product['date_added']), americanDate($product['date_updated']));

		fputcsv($fp, $row,',');

	}



	fclose($fp);

}



header('Content-type: application/csv');

header('Content-Disposition: attachment; filename="'.$filename.'"');

readfile($filename);

@unlink($filename);

exit;