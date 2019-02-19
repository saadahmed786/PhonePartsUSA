<?php
include_once '../auth.php';
include_once '../inc/functions.php';
include_once '../inc/split_page_results.php';
$carriers = array(
	array('id'=>'USPS','value'=>'USPS'),
	array('id'=>'UPS','value'=>'UPS'),
	array('id'=>'FedEx','value'=>'FedEx'),
	array('id'=>'DHL Express','value'=>'DHL Express'),
	array('id'=>'EMS','value'=>'EMS'),
	array('id'=>'HK Post','value'=>'HK Post'),
	array('id'=>'TNT','value'=>'TNT')
	);
$statuses = array(
	array('id'=>'Pending','value'=>'Pending'),
	array('id'=>'Shipped','value'=>'Shipped'),
	array('id'=>'Received','value'=>'Received'),
	// array('id'=>'QCd','value'=>'QCd'),
	array('id'=>'Completed','value'=>'Completed'),
	);
$servicers = $db->func_query("select id , name as value from inv_users where group_id = 13 or is_servicer= 1 ");
//print_r($_SESSION['group']);exit;

$pageViewLink = 'addedit_boxes.php?shipment_id=' . $_GET['shipment_id'];
$shipment_id = (int)$_GET['shipment_id'];
//print_r('helo');exit;
if ($_POST['action'] == 'save_item') {
	$item_id = (int) $_POST['item_id'];
	$update_qty = array(
		'oem_received_a' => $_POST['oem_a_qty'],
		'oem_received_b' => $_POST['oem_b_qty'],
		'oem_received_c' => $_POST['oem_c_qty'],
		'oem_received_d' => $_POST['oem_d_qty'],
		'non_oem_received_a' => $_POST['non_oem_a_qty'],
		'non_oem_received_b' => $_POST['non_oem_b_qty'],
		'non_oem_received_c' => $_POST['non_oem_c_qty'],
		'non_oem_received_d' => $_POST['non_oem_d_qty'],
		'salvage_received' => $_POST['salvage_qty']
		);
	$db->func_array2update("inv_buyback_box_items", $update_qty, "id = '". $item_id ."'");
	$buyback_product_id = (int) $_POST['buyback_product_id'];
	$update_price = array(
		'oem_a_price' => $_POST['oem_a_price'],
		'oem_b_price' => $_POST['oem_b_price'],
		'oem_c_price' => $_POST['oem_c_price'],
		'oem_d_price' => $_POST['oem_d_price'],
		'non_oem_a_price' => $_POST['non_oem_a_price'],
		'non_oem_b_price' => $_POST['non_oem_b_price'],
		'non_oem_c_price' => $_POST['non_oem_c_price'],
		'non_oem_d_price' => $_POST['non_oem_d_price'],
		'salvage_price' => $_POST['salvage_price']
		);
	$db->func_array2update("oc_buyback_products", $update_price, "buyback_product_id = '". $buyback_product_id ."'");
	$json['success'] = 'Record modified!';
	echo json_encode($json);
	exit;
} else if ($_POST['action'] == 'save_manual_item') {

	$item_id = (int) $_POST['item_id'];
	$update_manual = array(
		'oem_qty_a' => $_POST['oem_a_qty'],
		'oem_price_a' => $_POST['oem_a_price'],
		'oem_qty_b' => $_POST['oem_b_qty'],
		'oem_price_b' => $_POST['oem_b_price'],
		'oem_qty_c' => $_POST['oem_c_qty'],
		'oem_price_c' => $_POST['oem_c_price'],
		'oem_qty_d' => $_POST['oem_d_qty'],
		'oem_price_d' => $_POST['oem_d_price'],
		'non_oem_qty_a' => $_POST['non_oem_a_qty'],
		'non_oem_price_a' => $_POST['non_oem_a_price'],
		'non_oem_qty_b' => $_POST['non_oem_b_qty'],
		'non_oem_price_b' => $_POST['non_oem_b_price'],
		'non_oem_qty_c' => $_POST['non_oem_c_qty'],
		'non_oem_price_c' => $_POST['non_oem_c_price'],
		'non_oem_qty_d' => $_POST['non_oem_d_qty'],
		'non_oem_price_d' => $_POST['non_oem_d_price'],
		'salvage_qty' => $_POST['salvage_qty'],
		'salvage_price' => $_POST['salvage_price']
		);
	$db->func_array2update("inv_buyback_manual_box_items", $update_manual, "id = '". $item_id ."'");
	$json['success'] = 'Record modified!';
	echo json_encode($json);
	exit;
}
//Add to shipment button by haris

// add yo shipment button by haris
if($_POST['deleterow']){

	foreach ($_POST['lbb_check'] as $i => $prod) {

		if (strpos($i, 'm') === false) {
			$db->db_exec("update inv_buyback_box_items SET is_deleted = 1  where id = '$i'");
		} else {
			$db->db_exec("update inv_buyback_manual_box_items SET is_deleted = 1  where id = '". str_replace('m', '', $i) . "'");
		}

	}
}
if(!$shipment_id){
	$shipment_id = $db->func_query_first_cell("select id from inv_buyback_boxes where status != 'Completed'");
}

if(!$shipment_id){
	//$_SESSION['message'] = "No new sku added in rejected list";
	header("Location:box_shipments.php");
	exit;
}
// print_r($shipment_id);exit;
$shipments = $db->func_query("SELECT * FROM inv_shipments WHERE lbb_shipment = '$shipment_id'");

foreach ($shipments as $key => $shipment) {
	$itemsx = $db->func_query("SELECT * FROM inv_shipment_items WHERE  shipment_id = '". $shipment['id'] ."'");
	$items = array();

	foreach ($itemsx as $ix => $itemx) {
		$items[$itemx['product_sku']] = $itemx;
	}

	$shipments[$key]['items'] = $items;
	unset($items);
}

$shipment_detail = $db->func_query_first("SELECT * from inv_buyback_boxes where id = '$shipment_id'");
$url = $host_path . 'easypost/tracker_api.php';
$data = array(
	'tracking_number=' . $shipment_detail['tracking_number'],
	'carrier=' . $shipment_detail['carrier']
	);
$data_string = implode('&', $data);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
curl_exec($ch);
curl_close($ch);
//print_r($shipment_detail['servicer']);exit;
//print_r($shipment_detail['servicer']);exit;
if($_POST['addToShip']){
	if(count($_POST['lbb_ids']) > 0){
		$createdShipmentID = 0;
		foreach ($_POST['shipment_id'] as $lbb_item_sku => $lbb_ids) {
			
			$xxshipment_id = $lbb_ids;

			if (!$xxshipment_id && !$createdShipmentID) {
				$shipment = array(
					'package_number' => 'lbb' . rand(),
					'status' => 'Pending',					
					'is_lbb' => 1,
					'lbb_shipment' => $shipment_id,
					'user_id' => $_SESSION['user_id'],
					'vendor' => $shipment_detail['servicer'],
					'servicer' => $shipment_detail['servicer'],
					'ex_rate' => '0.00',
					'date_added' => date('Y-m-d H:i:s')
					);
				$xxshipment_id = $db->func_array2insert("inv_shipments", $shipment);
				$createdShipmentID = $xxshipment_id;
				$log = 'Shipment #: ' . linkToShipment($xxshipment_id, $host_path, $shipment['package_number']) . ' is Created From LBB Shipment ' . $shipment_detail['shipment_number'];
				actionLog($log);
			} else if (!$xxshipment_id && $createdShipmentID) {
				$xxshipment_id = $createdShipmentID;
			}
			$package_number = $db->func_query_first_cell("SELECT package_number FROM `inv_shipments` WHERE id = '".$xxshipment_id."'");

			$rejected_item = $db->func_query_first("SELECT * FROM inv_buyback_box_items WHERE buyback_product_id = '". $_POST['lbb_ids'][$lbb_item_sku] ."'");

			$mapped_sku=$db->func_query_first("SELECT * FROM inv_lbb_sku_mapping WHERE id ='".$_POST['lbb_sku'][$lbb_item_sku]."' ");
			$xproduct = $db->func_query_first("SELECT * FROM inv_shipment_items WHERE shipment_id = '$xxshipment_id' AND product_sku = '" . $mapped_sku['product_sku'] . "'");
			if ($xproduct) {
				$xproduct['cu_po'] =  $_POST['lbb_ids'][$lbb_item_sku];
				$xproduct['qty_shipped']=$xproduct['qty_shipped']+ $_POST['lbb_qty_ship'][$lbb_item_sku];
				$xproduct['mapped_sku'] = $lbb_item_sku;
				$xproduct['product_sku'] = $mapped_sku['product_sku'];
				$xproduct['box_id']= $shipment_id;
				$db->func_array2update("inv_shipment_items", $xproduct, "id = '". $xproduct['id'] ."'");
			} else {
				$item = array(
					'shipment_id' => $xxshipment_id,
					'product_id' => $db->func_query_first_cell("SELECT product_id FROM oc_product WHERE model = '". $mapped_sku['product_sku'] ."'"),
					'product_name'=> '',
						'product_sku' => $mapped_sku['product_sku'], //$item_sku['sku'],
						'qty_shipped' => $_POST['lbb_qty_ship'][$lbb_item_sku],
						'rejected_product' => 1,
						'cu_po' => $_POST['lbb_ids'][$lbb_item_sku],
						'mapped_sku' => $lbb_item_sku,
						'box_id' => $shipment_id
						);
				$db->func_array2insert("inv_shipment_items", $item);
			}
			$db->db_exec("update inv_buyback_box_items set added_to_ship = '".$xxshipment_id."' where id = '".$rejected_item['id']."'");
			$from = $db->func_query_first_cell("select package_number from inv_buyback_boxes where id = '". $shipment_id."'");
			$to = $package_number;

			$_link1 = '<a href="'.$host_path.'addedit_shipment.php?shipment_id='.$xxshipment_id.'">'.$to.'</a>';
			$_link2 = '<a href="'.$host_path.'buyback/addedit_boxes.php?shipment_id='.$shipment_id.'">'.$from.'</a>';
			logLbbItem(linkToProduct($item_sku['sku']),'Moved To '.$_link1.' from '.$_link2.' by ',$to,$from);
		}
	} else {
		$_SESSION['message'] = "Select at least one sku to Add to Shipment.";
		header("Location:addedit_boxes.php?shipment_id=$shipment_id");
		exit;
	}	
}
//print_r($lbb_sku['lbb_sku']);exit;

if ($_POST['getLbbSku']) {
	$item_skus=$db->func_query("SELECT id,product_sku FROM inv_lbb_sku_mapping WHERE lbb_sku='".$_POST['lbb_sku']."'");
	echo json_encode($item_skus);
	exit;
}



if ($_POST['action'] == 'getBoxs') {
	$shipments = $db->func_query("SELECT * FROM `inv_buyback_boxes` WHERE id != '$shipment_id'");
	$json['data'] = '<div class="blackPage">';
	$json['data'] .= '<div class="whitePage">';
	$json['data'] .= '<div class="form">';
	$json['data'] .= '<select id="mergeBoxId">';
	$json['data'] .= '<option value="">--Select--</option>';
	if ($shipments) {
		foreach ($shipments as $key => $row) {
			$json['data'] .= '<option value="'. $row['id'] .'">'. (($row['package_number'])? $row['package_number']: 'No Package #') .'</option>';
		}
	}
	$json['data'] .= '</select>';
	$json['data'] .= '</div>';
	$json['data'] .= '<div class="form">';
	
	$json['data'] .= '<input class="button" type="button" class="shipmentBtn" value="Add" onclick="mergeBox();" />';
	$json['data'] .= '<input class="button" type="button" value="Cancel" onclick="$(\'.blackPage\').remove();" />';

	$json['data'] .= '</div>';
	$json['data'] .= '</div>';
	$json['data'] .= '</div>';

	echo json_encode($json);
	exit;
}


if ($_POST['action'] == 'mergeBox') {
	$id = (int)$_POST['id'];
	$to = (int)$_POST['to'];
	$tables = array(
		'inv_shipments' =>'lbb_shipment',
		'inv_buyback_box_items' => 'shipment_id',
		'inv_buyback_manual_box_items' => 'shipment_id',
		//  'inv_buyback_shipment_box_comments'=>'buyback_shipment_box_id',
		
		);
	
	if ($id && $to) {
		foreach ($tables as $table => $key) {
			//echo "UPDATE $table SET $key = '$to' WHERE $key = '$id'\n";
			$db->db_exec("UPDATE $table SET $key = '$to' WHERE $key = '$id'");
		}
		$db->db_exec("UPDATE inv_buyback_boxes SET status='Merged' WHERE id='$to'");

		$_data = array();
		$_data['id'] = $to;
		$_data['comment'] = 'Merged with: <a href="'.$host_path.'buyback/addedit_boxes.php?shipment_id='.$id.'">'.$db->func_query_first_cell("SELECT package_number FROM inv_buyback_boxes WHERE id='".$id."'").'</a>';
		$msg = addComment('buyback_shipment_box',$_data);


		exit;
		$_SESSION['message'] = 'Box Merged';
	} else {
		$_SESSION['message'] = 'Wrong Box ID';
	}


	echo json_encode($json['to'] = $to);
	exit;
}

if ($_POST['action'] == 'getShipment') {
	$shipments = $db->func_query('SELECT * FROM `inv_shipments` WHERE vendor_po_id = "'. $_SESSION['user_id'] .'" AND is_lbb = 1 AND status = "Pending"');
	$json['data'] = '<div class="blackPage">';
	$json['data'] .= '<div class="whitePage">';
	$json['data'] .= '<div class="form">';

	$json['data'] .= '<select id="vendor_shipment_id">';
	$json['data'] .= '<option value="">--Create New--</option>';
	if ($shipments) {
		foreach ($shipments as $key => $row) {
			$json['data'] .= '<option value="'. $row['id'] .'">'. $row['package_number'] .'</option>';
		}
	}
	$json['data'] .= '</select>';
	$json['data'] .= '</div>';
	$json['data'] .= '<div class="form">';
	
	$json['data'] .= '<input class="button" type="button" class="shipmentBtn" value="Add" onclick="addShipment();" />';
	$json['data'] .= '<input class="button" type="button" value="Cancel" onclick="$(\'.blackPage\').remove();" />';

	$json['data'] .= '</div>';
	$json['data'] .= '</div>';
	$json['data'] .= '</div>';

	echo json_encode($json);
	exit;
}

if ($_POST['action'] == 'addShipment') {
	//print_r($_POST);
	//print_r('hehehe');exit;
	$shipment = array();
	if ($_POST['shipment_id']) {

		$shipment['id'] = $_POST['shipment_id'];
	}
	foreach ($_POST['product'] as $productx) {
		$item = array();
		if ($productx['update'] && $productx['qty_shipped']) {
			unset($productx['update']);
			$item = $productx;
		}

		if ($item) {
			$log = 'Shipment #: ' . linkToShipment($shipment['id'], $host_path, $shipment['package_number']) . ' is Updated From '. linkToVPO($details['id'], $host_path, $vendor_po_id);
			if (!isset($shipment['id'])) {
				$shipment = array(
					'package_number' => 'lbb' . rand(),
					'status' => 'Pending',					
					'is_lbb' => 1,
					'lbb_shipment' => $shipment_id,
					'vendor_po_id' => $_SESSION['user_id'],
					'date_added' => date('Y-m-d H:i:s')
					);
				$shipment['id'] = $db->func_array2insert("inv_shipments", $shipment);
				$log = 'Shipment #: ' . linkToShipment($shipment['id'], $host_path, $shipment['package_number']) . ' is Created From '. linkToVPO($details['id'], $host_path, $vendor_po_id);
			}

			$shipment_item = $db->func_query_first('SELECT * FROM `inv_shipment_items` WHERE `shipment_id` = "'. $shipment['id'] .'" AND product_sku = "'. $item['product_sku'] .'" ');
			if ($shipment_item) {
				$db->db_exec('UPDATE inv_shipment_items SET qty_shipped = (qty_shipped + '. $item['qty_shipped'] .') WHERE id = "'. $shipment_item['id'] .'"');
				$plog .= "<br><br> Product " . linkToProduct($item['product_sku']) . ' Updated';
				$plog .= "<br> Shipped " . $item['qty_shipped'] . " items Added";
				$shipping_item_id = $shipment_item['id'];
			} else {
				$array = array(
					'shipment_id'	=> $shipment['id'],
					'product_id'	=> $item['product_id'],
					'product_name'	=> $item['product_name'],
					'is_lbb'		=> '1',
					'product_sku'	=> $item['product_sku'],
					'qty_shipped'	=> $item['qty_shipped']
					);
				$shipping_item_id = $db->func_array2insert("inv_shipment_items", $array);
				$plog .= "<br><br> Product " . linkToProduct($item['product_sku']) . ' Added';
				$plog .= "<br> Shipped " . $item['toShip'] . " items Added";
				unset($array);
			}
		}
	}
	actionLog($log . $plog);
	exit;
}

if($_POST['print']){ 
	$reject_ids = implode(",",$_POST['reject_ids']);
	header("Location:print_shipment.php?ids=$reject_ids");
	exit;
}
if(isset($_POST['addcomment']))
{
	$data = array();
	$data['id'] = $shipment_id;
	$data['comment'] = $_POST['comment'];
	$msg = addComment('buyback_shipment_box',$data);
	$_SESSION['message'] = $msg;
	header("Location: addedit_boxes.php?shipment_id=".$shipment_id);
	exit;
}

//save shipment
if($_POST['save'] || $_POST['RejectComplete'] || $_POST['SaveAndShip'] || $_POST['SaveAndReceive'] || $_POST['SaveAndComplete']){
 	//testObject($_POST['status']);exit;
	$shipment = array();
	$shipment['package_number'] = $db->func_escape_string($_POST['package_number']);
	$shipment['servicer'] = $db->func_escape_string($_POST['servicer']);
	$shipment['tracking_number'] = $db->func_escape_string($_POST['tracking_number']);
	$shipment['shipping_cost'] = $db->func_escape_string($_POST['shipping_cost']);
	$shipment['carrier'] = $db->func_escape_string($_POST['carrier']);
	$shipment['date_issued'] = $_POST['date_issued'];
	$shipment['handeling_fee'] = $db->func_escape_string($_POST['handeling_fee']);
	if($_POST['SaveAndShip']){
		$shipment['status'] = 'Shipped';
	} elseif ($_POST['SaveAndReceive']) {
		$shipment['status'] = 'Received';
	} elseif ($_POST['SaveAndComplete']) {
		$shipment['status'] = 'Completed';
	} else {
		$shipment['status'] = $db->func_escape_string($_POST['status']);
	}
	
	// if(isset($_POST['outbound_tracking']))
	// {
	// 	$shipment['outbound_tracking'] = $db->func_escape_string($_POST['outbound_tracking']);	
	// 	$shipment['inbound_tracking'] = $db->func_escape_string($_POST['inbound_tracking']);

	// }
	// if(isset($_POST['outbound_shipping_cost']))
	// {
	// 	$shipment['outbound_shipping_cost'] = (float)$_POST['outbound_shipping_cost'];
	// 	$shipment['inbound_shipping_cost'] = (float)$_POST['inbound_shipping_cost'];
	// }


	$checkExist = $db->func_query_first_cell("SELECT id from inv_buyback_boxes where id != '$shipment_id' 
		and package_number = '".$shipment['package_number']."'");
	if($checkExist){
		$_SESSION['message'] = "This package number is assigned to another shipment.";
		header("Location:addedit_boxes.php?shipment_id=$shipment_id");
		exit;
	}
	else{
		$db->func_array2update("inv_buyback_boxes",$shipment,"id = '$shipment_id'");
		$_SESSION['message'] = "Shipment is updated";
	}
	
	//now update shipment item reject reason
	$reasons = $_POST['reason'];
	
	foreach($reasons as $id => $reason){
		
		$text = $db->func_escape_string($reason);
		
		$db->db_exec("update inv_buyback_box_items SET  reason = '$text' where id = '$id'");
	}
	if(isset($_POST['list']))
	{
		foreach($_POST['list'] as $_buyback_product =>$key)
		{


			$db->db_exec("UPDATE inv_buyback_box_items SET working_qty='".(int)$key['working_qty']."',refurb_cost='".(float)$key['refurb_cost']."',non_working_qty='".(int)$key['non_working_qty']."',non_working_lcd_cost='".(float)$key['non_working_lcd_cost']."' WHERE buyback_product_id='".$_buyback_product."' AND shipment_id='".$shipment_id."'");
		}
		//$db->db_exec("update inv_buyback_boxes SET status = 'Completed'  where id = '$shipment_id'");


	}

	if(isset($_POST['manual_list']))
	{
		foreach($_POST['manual_list'] as $_buyback_product =>$key)
		{


			$db->db_exec("UPDATE inv_buyback_manual_box_items SET working_qty='".(int)$key['working_qty']."',refurb_cost='".(float)$key['refurb_cost']."',non_working_qty='".(int)$key['non_working_qty']."',non_working_lcd_cost='".(float)$key['non_working_lcd_cost']."' WHERE sku='".$_buyback_product."' AND shipment_id='".$shipment_id."'");
		}
		//$db->db_exec("UPDATE inv_buyback_boxes SET status = 'Completed'  where id = '$shipment_id'");


	}



	if($_POST['RejectComplete'] && $_SESSION['edit_received_shipment']){
		if(!$shipment['package_number']){
			$_SESSION['message'] = "Package number is required.";
			header("Location:addedit_boxes.php?shipment_id=$shipment_id");
			exit;
		}
		$log = '<a href="'.$host_path.'buyback/addedit_boxes.php?shipment_id='.$shipment_id.'">'.$shipment_detail['package_number'].'</a> Buyback Shipment Box has been Closed';
		actionLog($log);
		$db->db_exec("update inv_buyback_boxes SET status = 'Completed'  where id = '$shipment_id'");
		$_SESSION['message'] = "Shipment status is Completed";
	}
	
	header("Location:addedit_boxes.php?shipment_id=$shipment_id");
	exit;
}


if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}

if($page < 1){
	$page = 1;
}

$parameters = "shipment_id=$shipment_id";

$max_page_links = 10;
$num_rows = 500;
$start = ($page - 1)*$num_rows;

$inv_query  = "select si.* , s.package_number from inv_buyback_box_items si inner join inv_buyback_boxes s on (si.shipment_id = s.id)
where si.shipment_id = '$shipment_id' and is_deleted!=1 order by shipment_id";

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "addedit_boxes.php",$page);
$products   = $db->func_query($splitPage->sql_query);


$productss = $db->func_query("select si.* , s.package_number from inv_buyback_box_items si inner join inv_buyback_boxes s on (si.shipment_id = s.id)
	where si.shipment_id = '$shipment_id' AND si.is_deleted=0 order by shipment_id");



$products1 = $db->func_query("SELECT sum(a.oem_received_a) as oem_received_a,sum(a.oem_received_b) as oem_received_b,sum(a.oem_received_c) as oem_received_c,sum(a.oem_received_d) as oem_received_d,sum(a.non_oem_received_a) as non_oem_received_a,sum(a.non_oem_received_b) as non_oem_received_b,sum(a.non_oem_received_c) as non_oem_received_c,sum(a.non_oem_received_d) as non_oem_received_d,a.buyback_product_id FROM inv_buyback_box_items a,oc_buyback_products b WHERE a.buyback_product_id=b.buyback_product_id and  a.shipment_id='".$shipment_id."' GROUP BY b.sku");
$products2 = $db->func_query("SELECT a.*,b.sku,b.description,b.oem_a_price,b.oem_b_price,b.oem_c_price,b.oem_d_price,b.non_oem_a_price,b.non_oem_b_price,b.non_oem_c_price,b.non_oem_d_price,a.buyback_product_id,a.added_to_ship,sum(a.oem_received_a) as oem_received_a,sum(a.oem_received_b) as oem_received_b,sum(a.oem_received_c) as oem_received_c,sum(a.oem_received_d) as oem_received_d,sum(a.non_oem_received_a) as non_oem_received_a,sum(a.non_oem_received_b) as non_oem_received_b,sum(a.non_oem_received_c) as non_oem_received_c,sum(a.non_oem_received_d) as non_oem_received_d FROM inv_buyback_box_items a,oc_buyback_products b WHERE a.buyback_product_id=b.buyback_product_id and  a.shipment_id='".$shipment_id."' GROUP BY b.sku");
$manual_products2 = $db->func_query("SELECT a.*,b.sku,b.description,sum(a.oem_qty_a) as oem_received_a,sum(a.oem_qty_b) as oem_received_b,sum(a.oem_qty_c) as oem_received_c,sum(a.oem_qty_d) as oem_received_d,sum(a.non_oem_qty_a) as non_oem_received_a,sum(a.non_oem_qty_b) as non_oem_received_b,sum(a.non_oem_qty_c) as non_oem_received_c,sum(a.non_oem_qty_d) as non_oem_received_d FROM inv_buyback_manual_box_items a,inv_buy_back b WHERE a.sku=b.sku and  a.shipment_id='".$shipment_id."' GROUP BY a.id");

$manual_items = $db->func_query("SELECT * FROM inv_buyback_manual_box_items WHERE shipment_id='$shipment_id' AND is_deleted = '0'");
//die("SELECT a.*,b.sku,b.description,b.oem_a_price,b.oem_b_price,b.oem_c_price,b.oem_d_price,b.non_oem_a_price,b.non_oem_b_price,b.non_oem_c_price,b.non_oem_d_price,a.buyback_product_id,a.added_to_ship,sum(d.salvage_qty) as manual_salvage,sum(a.oem_received_a) as oem_received_a,sum(a.oem_received_b) as oem_received_b,sum(a.oem_received_c) as oem_received_c,sum(a.oem_received_d) as oem_received_d,sum(a.non_oem_received_a) as non_oem_received_a,sum(a.non_oem_received_b) as non_oem_received_b,sum(a.non_oem_received_c) as non_oem_received_c,sum(a.non_oem_received_d) as non_oem_received_d FROM inv_buyback_box_items a,oc_buyback_products b,inv_buyback_manual_box_items d WHERE a.buyback_product_id=b.buyback_product_id and  a.shipment_id='".$shipment_id."' and d.shipment_id='".$shipment_id."' GROUP BY b.sku");
//print_r($manual_products2);exit;
// Total OEM / Non-OEM Count
$oem_total = 0;
$non_oem_total = 0;
foreach ($products as $i => $product) {
	$oem_total+=$product['oem_received_a']+$product['oem_received_b']+$product['oem_received_c']+$product['oem_received_d'];
	$non_oem_total+=$product['non_oem_received_a']+$product['non_oem_received_b']+$product['non_oem_received_c']+$product['non_oem_received_d']+$product['salvage_received'];
}
foreach($manual_items as $manual)
{
	$oem_total+=$manual['oem_qty_a']+$manual['oem_qty_b']+$manual['oem_qty_c']+$manual['oem_qty_d'];
	$non_oem_total+=$manual['non_oem_qty_a']+$manual['non_oem_qty_b']+$manual['non_oem_qty_c']+$manual['non_oem_qty_d']+$manual['salvage_qty'];
}
//die("SELECT buyback_product_id from inv_buyback_box_items WHERE shipment_id= '$shipment_id'");
//$buyback_ids=$db->func_query("SELECT buyback_product_id from inv_buyback_box_items WHERE shipment_id= '$shipment_id' " );

//print_r($buyback_ids[0]['buyback_product_id']);exit;

// foreach ($buyback_ids as $key => $id) {
// 	//print_r($id['buyback_product_id']);exit;
// 	$sk=$db->func_query("SELECT sku from oc_buyback_products where buyback_product_id= '".$id['buyback_product_id']."' GROUP BY sku");
// 	//print_r($sk);

// }


/* This query fetches single line sku and sum of the sku's oem and non oem items by haris*/
if ($_POST['getShipments']) {
	$shipments = $db->func_query('SELECT * FROM `inv_shipments` WHERE servicer = "'. $shipment_detail['servicer'] .'" AND status = "Pending"');
	echo json_encode($shipments);
	exit;
}

$shipments = $db->func_query('SELECT * FROM `inv_shipments` WHERE servicer = "'. $shipment_detail['servicer'] .'" AND status = "Pending"');
$box_data=$db->func_query("SELECT oc_buyback_products.buyback_product_id,oc_buyback_products.sku,oc_buyback_products.description,oc_buyback_products.total_received,oc_buyback_products.oem_a_price,oc_buyback_products.oem_b_price,oc_buyback_products.oem_c_price,oc_buyback_products.oem_d_price,oc_buyback_products.non_oem_a_price,oc_buyback_products.non_oem_b_price,oc_buyback_products.non_oem_c_price,oc_buyback_products.non_oem_d_price,inv_buyback_box_items.working_qty,inv_buyback_box_items.refurb_cost,inv_buyback_box_items.non_working_qty,inv_buyback_box_items.buyback_product_id,sum(inv_buyback_box_items.salvage_received) AS salvagesum ,sum(inv_buyback_box_items.oem_received_a) AS oemsuma,sum(inv_buyback_box_items.oem_received_b) AS oemsumb,sum(inv_buyback_box_items.oem_received_c) AS oemsumc,sum(inv_buyback_box_items.oem_received_d) AS oemsumd, sum(inv_buyback_box_items.non_oem_received_a) AS nonoemsuma, sum(inv_buyback_box_items.non_oem_received_b) AS nonoemsumb, sum(inv_buyback_box_items.non_oem_received_c) AS nonoemsumc, sum(inv_buyback_box_items.non_oem_received_d) AS nonoemsumd from inv_buyback_box_items
	LEFT JOIN oc_buyback_products ON inv_buyback_box_items.buyback_product_id=oc_buyback_products.buyback_product_id Where shipment_id='".$shipment_id."' AND inv_buyback_box_items.is_deleted!=1 GROUP BY sku");

$manual_box_data=$db->func_query("SELECT sku,oem_price_a,oem_price_b,oem_price_c,oem_price_d,non_oem_price_a,non_oem_price_b,non_oem_price_c,non_oem_price_d,working_qty,refurb_cost,non_working_qty,sum(salvage_qty) AS salvagesum ,sum(oem_qty_a) AS oemsuma,sum(oem_qty_b) AS oemsumb,sum(oem_qty_c) AS oemsumc,sum(oem_qty_d) AS oemsumd, sum(non_oem_qty_a) AS nonoemsuma, sum(non_oem_qty_b) AS nonoemsumb, sum(non_oem_qty_c) AS nonoemsumc, sum(non_oem_qty_d) AS nonoemsumd from inv_buyback_manual_box_items
	Where shipment_id='".$shipment_id."' AND inv_buyback_manual_box_items.is_deleted!=1 GROUP BY sku");

//print_r($manual_box_data);exit;
// $servicer = false;
// print_r('hello');exit;
// if ($_SESSION['group'] == 'Servicer') {
// 	//if ($_SESSION['user_id'] == $shipment_detail['Servicer']) {
// 		$servicer = true;
// 	//} else {
// 		//exit;
// 	}
// }
//print_r($_SESSION['group']);exit;
	?>
	<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Add / Edit Shipment  Box</title>
		<style>
		.read_class{
			background-color: #fff;
			border: 0px solid #ccc;
		}
	</style>
		<script type="text/javascript" src="../js/jquery.min.js"></script>
		<script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>
		<link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

		<script type="text/javascript">
			$(document).ready(function() {
				$('.fancybox').fancybox({ width: '400px', height : '200px' , autoCenter : true , autoSize : true });
				$('.fancybox2').fancybox({ width: '500px', height : '500px' , autoCenter : true , autoSize : true });
			});
		</script>	
	</head>
	<body>
		<?php include_once '../inc/header.php';?>

		<?php if(@$_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>

		<div align="center">
			<form method="post" action="">
				<div class="blackPage" style="display: none;">
					<div class="whitePage">
						<div class="form list">
						</div>
						<div class="form">
							<input type="submit" name="addToShip" value="Submit" onclick="if(!confirm('Are you sure?')){ return false; }" />
							<input class="button" type="button" value="Cancel" onclick="$('.blackPage').hide();" />
							<!-- <input type="hidden" name="selected_items1" id="selected_items1" value=""> -->
						</div>
					</div>
				</div>
				<br />
				<div style="width: 80%;">
					<?php if($_SESSION['login_as']!='admin') {  ?>
						Shipment Number:
						<input type="text" readonly name="package_number" value="<?php echo $shipment_detail['package_number'];?>" required />
						<?php } else { ?>
							Shipment Number:
							<input type="text"  name="package_number" value="<?php echo $shipment_detail['package_number'];?>" <?php echo ($shipment_detail['status'] == 'Completed')? 'readonly':''; ?> required />
							<?php } ?>
							<?php if($shipment_detail['status'] != 'Completed' || $_SESSION['login_as']=='admin'):?>
								<input type="submit" name="save" value="Save" />
							<?php endif;?>
							<input style="float: right;" class="button" type="button" class="shipmentBtn" value="Merge Shipment" onclick="selectGetBoxs();" />
							<br /><br />
						</div>
						<?php
						if($shipment_detail['status']!='Completed' and ($_SESSION['login_as']=='admin' || $_SESSION['buyback_add_manual_lcd']))
						{
							?>
							<a class="fancybox3 fancybox.iframe" href="<?=$host_path;?>buyback/add_manual_lcd.php?shipment_id=<?=$shipment_id;?>">Add Manual LCD</a>
							<?php

						}
						?>
						<br /><br />
						<div>
							<?php if($shipment_detail['status'] == 'Completed' && $_SESSION['login_as']!='admin') {  ?>

								Shipment Date:
								<input readonly type="text" data-type="date" value="<?php echo $shipment_detail['date_issued']; ?>" name="date_issued" />

								Shipping Cost: 
								<input type="text" readonly style="width:80px;" name="shipping_cost" value="<?=$shipment_detail['shipping_cost'];?>"> 
								
								Servicer: 
								<?php echo createField("servicer", "servicer" , "input" , get_username($shipment_detail['servicer']), $servicers, 'onclick="$(\'.servicer\').val($(this).val());" class="servicer" readonly name="servicer"');?>
								
								Status: 
								<input type="text" readonly style="width:100px;" name="status" value="<?=$shipment_detail['status'];?>">
								
								
								Carrier: 
								<?php echo createField("carrier", "carrier" , "input" , $shipment_detail['carrier'] , $carriers, 'onclick="$(\'.carrier\').val($(this).val());" class="carrier" readonly name="carrier"');?>	
								
								Tracking #: 
								<input type="text" readonly class="tracking_number" name="tracking_number" onkeyup="$(\'.tracking_number\').val($(this).val());" value="<?=$shipment_detail['tracking_number'];?>" />
								<?php if($_SESSION['group']=='Admin' || $_SESSION['group']=='Super Admin'){ ?>
								<br>
								Per Item Handling Fee:
								<input type="text" readonly class="handeling_fee" name="handeling_fee" onkeyup="$(\'.handeling_fee\').val($(this).val());" value="<?=$shipment_detail['handeling_fee'];?>" />
								<?php } ?>	
									
									<br></br>
									<?php } else { ?>
									Shipment Date:
										<input type="text" data-type="date" value="<?php echo $shipment_detail['date_issued']; ?>" name="date_issued" />
										
										Shipping Cost: 
										<input type="text" <?php echo (($_SESSION['login_as']=='employee' && (!$_SESSION['boxes_edit'])) ? "readonly"	:"");?> <?php echo ($shipment_detail['status'] == 'Completed')? 'readonly':''; ?> name="shipping_cost" value="<?=$shipment_detail['shipping_cost'];?>"> 
										
										Servicer: 
										<?php if($shipment_detail['status'] == 'Completed' || ($_SESSION['login_as']=='employee' && (!$_SESSION['boxes_edit']))){?>
											<?php echo createField("servicer", "servicer" , "select" , $shipment_detail['servicer'], $servicers, 'onclick="$(\'.servicer\').val($(this).val());" class="servicer" name="servicer" disabled ');?>
											<?php }  else{?>
												<?php echo createField("servicer", "servicer" , "select" , $shipment_detail['servicer'], $servicers, 'onclick="$(\'.servicer\').val($(this).val());" class="servicer" name="servicer" ');?>
												<?php }?>
												
												Status: 
												<?php if($_SESSION['login_as']=='employee' && (!$_SESSION['boxes_edit'])) { ?>
													<select id="status" disabled="disabled" name="status" style="width:150px;">
														<option disabled="disabled" value="">Select One</option>
														<?php foreach($statuses as $status):?>
														<option disabled="disabled" value="<?php echo $status['id']; ?>" <?php echo ($status['id'] == $shipment_detail['status'])? 'selected=""':''; ?>><?php echo $status['value']; ?></option>
													<?php endforeach;?>
												</select>
												<?php } else{?>
													<select id="status" name="status" style="width:150px;">
														<option value="">Select One</option>
														<?php foreach($statuses as $status):?>
														<option value="<?php echo $status['id']; ?>" <?php echo ($status['id'] == $shipment_detail['status'])? 'selected=""':''; ?>><?php echo $status['value']; ?></option>
													<?php endforeach;?>
												</select>
												<?php }?>
												Carrier: 
												<?php if($shipment_detail['status'] == 'Completed' || ($_SESSION['login_as']=='employee' && (!$_SESSION['boxes_edit']))){?>
													<?php echo createField("carrier", "carrier" , "select" , $shipment_detail['carrier'] , $carriers, 'onclick="$(\'.carrier\').val($(this).val());" class="carrier" name="carrier" disabled');?>
													<?php }  else{?>
														<?php echo createField("carrier", "carrier" , "select" , $shipment_detail['carrier'] , $carriers, 'onclick="$(\'.carrier\').val($(this).val());" class="carrier" name="carrier"');?>
														<?php }?>	
														
														Tracking #: 
														<input type="text" class="tracking_number" name="tracking_number" onkeyup="$(\'.tracking_number\').val($(this).val());" value="<?=$shipment_detail['tracking_number'];?>" <?php echo ($shipment_detail['status'] == 'Completed' || ($_SESSION['login_as']=='employee' && (!$_SESSION['boxes_edit'])))? 'readonly':''; ?> />
														
														<?php if($_SESSION['group']=='Admin' || $_SESSION['group']=='Super Admin'){ ?>
														<br>
														Per Item Handling Fee:
														<input type="text" class="handeling_fee" name="handeling_fee" onkeyup="$(\'.handeling_fee\').val($(this).val());" value="<?=$shipment_detail['handeling_fee'];?>" <?php echo ($shipment_detail['status'] == 'Completed' || ($_SESSION['login_as']=='employee' && (!$_SESSION['boxes_edit'])))? 'readonly':''; ?> />
														<?php }?>	
															
															<br></br>
															<?php } ?>
															<?php if($_SESSION['group']=='Admin' || $_SESSION['group']=='Super Admin'){?>
																<input type="submit" name="deleterow" value="Delete" />
																<?php }?>
																<?php if($shipment_detail['status'] == 'Pending' && $_SESSION['group']!='Servicer') { ?>
																	<input type="submit" name="SaveAndShip" value="Save & Ship" />
																	<?php } else if($shipment_detail['status'] == 'Shipped') { ?>
																		<input type="submit" name="SaveAndReceive" value="Save & Receive" />
																		<?php } else if($shipment_detail['status'] == 'Received') { ?>
																			<input type="submit" name="SaveAndComplete" value="Save & Complete" onclick="if(!confirm('Are you sure?')){ return false; }" />
																			<?php } ?>
																			<?php if($shipment_detail['status'] == 'Received'){ ?> 
																				<input type="button" onclick="selectlbbs(); " value="Add to Shippment"  />
																				<?php } ?>
																			</div>
																			<br></br>
																			<div class="tabMenu" >
																				<input type="button" class="toogleTab" data-tab="tabMain" value="Main">
																				<?php if($_SESSION['login_as']=='admin'){ ?>
																					<input type="button" class="toogleTab" data-tab="tabSource" value="Source">
																					<input type="button" class="toogleTab" data-tab="tabFinance" value="Finance">
																					<?php } ?>
																				</div>
																				<div id="tabMain" class="makeTabs"> 
																					<?php
																					if($_SESSION['login_as']=='admin' || $_SESSION['login_as']=='employee' || $_SESSION['boxes_cost']==1)
																					{
																						?>
																						<tr>

																							<td colspan="8">


																								<?php
																								$sum = array();
																								$total_products = array();
																								foreach($products as $product)
																								{
																									$sku = $db->func_query_first("SELECT * FROM oc_buyback_products WHERE buyback_product_id='".$product['buyback_product_id']."'");
																									if(isset($sum['oem'][$sku['buyback_id']][$sku['sku']]))
																									{
																										$sum['oem'][$sku['buyback_id']][$sku['sku']]+=$product['oem_received'];

																									}	
																									else
																									{
																										$sum['oem'][$sku['buyback_id']][$sku['sku']]=$product['oem_received'];

																									}	

																									if(isset($sum['non_oem'][$sku['buyback_id']][$sku['sku']]))
																									{
																										$sum['non_oem'][$sku['buyback_id']][$sku['sku']]+=$product['non_oem_received'];

																									}	
																									else
																									{
																										$sum['non_oem'][$sku['buyback_id']][$sku['sku']]=$product['non_oem_received'];

																									}
																									$total_products[$sku['sku']] = $sku['description'];			   
																								}
	        	//echo "<pre>";
	        	//print_r($total_products);
																								?>
																								<table width="90%" cellpadding="5px" cellspacing="0" border="1" align="center" style="border-collapse:collapse;display:none">
																									<tr>
																										<th>OEM</th>
																										<?php
																										foreach($sum['oem'] as $buyback_id =>$buyback)
																										{
																											$lbb_number = $db->func_query_first_cell("select shipment_number from oc_buyback where buyback_id='".(int)$buyback_id."'")
																											?>
																											<th><?=linkToLbbShipment($lbb_number,$host_path);?></th>
																											<?php
																										}
																										?>
																									</tr>
																									<?php

																									foreach($total_products as $sku=>$description)
																									{
																										?>
																										<tr>
																											<td><strong><?=$sku.'-'.$description;?></strong></td>
																											<?php
																											foreach($sum['oem'] as $buyback_id=>$buyback)
																											{
																												?>
																												<td align="center"><?=(int)$buyback[$sku];?></td>
																												<?php
																											}
																											?>
																										</tr>
																										<?php
																									}
																									?>

																								</table>

																								<table width="90%" cellpadding="5px" cellspacing="0" border="1" align="center" style="border-collapse:collapse;display:none">
																									<tr>
																										<th>NON-OEM</th>
																										<?php
																										foreach($sum['non_oem'] as $buyback_id =>$buyback)
																										{
																											$lbb_number = $db->func_query_first_cell("select shipment_number from oc_buyback where buyback_id='".(int)$buyback_id."'")
																											?>
																											<th><?=linkToLbbShipment($lbb_number,$host_path);?></th>
																											<?php
																										}
																										?>
																									</tr>
																									<?php

																									foreach($total_products as $sku=>$description)
																									{
																										?>
																										<tr>
																											<td><strong><?=$sku.'-'.$description;?></strong></td>
																											<?php
																											foreach($sum['non_oem'] as $buyback_id=>$buyback)
																											{
																												?>
																												<td align="center"><?=(int)$buyback[$sku];?></td>
																												<?php
																											}
																											?>
																										</tr>
																										<?php
																									}
																									?>

																								</table>
																								<?php if($shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Received' || $shipment_detail['status'] == 'Completed'){ ?>
																									<table width="90%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
																										<tr>
																											<th>#</th>
																											<th>SKU</th>
																											<th>Total qty Shipped</th>
																											<th>Received Good</th>
																											<?php if($shipment_detail['status'] != 'Shipped') { ?>
																												<th>Total Cost</th>
																												<th>Repair Cost</th>
																												<?php }?>
																												<th>Received Bad</th>
																												<?php if($shipment_detail['status'] != 'Shipped') { ?>
																													<th>Non Working LCD(s) Cost</th>
																													<th> Cost Per Working LCD</th>
																													<?php } ?>
																													<?php if (($_SESSION['login_as'] == 'admin' || $_SESSION['login_as'] == 'employee')&& $shipment_detail['status'] == 'Received' || $shipment_detail['status'] == 'Completed' ) { ?>
																														<th>Added to Shipment</th>
																														<?php }?>

																													</tr>
																													<?php
																													$z=0;
																													$i=0;
																													$j=0;
																													foreach($products2 as $product)
																													{
																														$total_lcds = $oem_total+$non_oem_total;
																														$total_lcd_cost = ((float)$product['oem_a_price'] * (int)$product['oem_received_a'] + (float)$product['oem_b_price'] * (int)$product['oem_received_b'] + (float)$product['oem_c_price'] * (int)$product['oem_received_c'] + (float)$product['oem_d_price'] * (int)$product['oem_received_d']) + ((float)$product['non_oem_a_price'] * (int)$product['non_oem_received_a'] + (float)$product['non_oem_b_price'] * (int)$product['non_oem_received_b'] + (float)$product['non_oem_c_price'] * (int)$product['non_oem_received_c'] + (float)$product['non_oem_d_price'] * (int)$product['non_oem_received_d']);
	        		// Forumla by Saad Ahmed (http://prntscr.com/94res2)
																														$qty_shiped=$db->func_query("SELECT qty_shipped FROM `inv_shipment_items` WHERE cu_po = '".$product['buyback_product_id']."' ");
																														$sum_qty=0;
												//print_r($product['buyback_product_id']);
												//print_r($qty_shiped[0]['qty_shipped']);
																														foreach ($qty_shiped as $qty) {
																															$sum_qty+=$qty['qty_shipped'];
																														}
											//print_r($product['buyback_product_id']);exit;
																														$total_qty_shiped=$product['oem_received_a']+$product['oem_received_b']+$product['oem_received_c']+$product['oem_received_d']+$product['non_oem_received_a']+$product['non_oem_received_b']+$product['non_oem_received_c']+$product['non_oem_received_d']+$product['salvage_received'];
											//print_r($qty_shiped['qty_shipped']);exit;
																														$cost = ($shipment_detail['inbound_shipping_cost']+$shipment_detail['outbound_shipping_cost']) / $total_lcds;

																														$cost = $cost + ($product['working_qty']*$product['refurb_cost']);
																														$cost = $cost + ((float)$total_lcd_cost - (float)$product['non_working_lcd_cost']); 
																														if($cost)
																														{
																															$cost = $cost / ($product['oem_received_a']+$product['oem_received_b']+$product['oem_received_c']+$product['oem_received_d']+$product['non_oem_received_a']+$product['non_oem_received_b']+$product['non_oem_received_c']+$product['non_oem_received_d']+$product['salvage_received']);
																														}
																														else
																														{
																															$cost = 0.00;
																														}

											// $lbb_sku=$db->func_query_first("SELECT * FROM inv_lbb_sku_mapping WHERE product_sku='".$product['sku']."'");

																														?>
																														<tr class="select_lbbs" data-lbbsku="<?=$product['sku']; ?>" data-buy-back-id="<?php echo $product['buyback_product_id'];?>">
																															<td><input type="checkbox" name="lbb_ids[<?php echo $product['sku']; ?>]" value="<?php echo $product['buyback_product_id'];?>" /></td>
																															<td><?=$product['sku'].' - '.$product['description'];?></td>
																															<td>
																																<center><?php echo $total_qty_shiped;?></center>
																															</td>
																															<?php if($shipment_detail['status'] == 'Shipped'){?>
																																<td align="center"><input type="text" name="list[<?=$product['buyback_product_id'];?>][working_qty]" value="<?=$product['working_qty'];?>" size="4" ></td>
																																<?php }?>
																																<?php if($shipment_detail['status'] == 'Received' || $shipment_detail['status'] == 'Completed'){?>
																																	<td align="center"><input readonly type="text" name="list[<?=$product['buyback_product_id'];?>][working_qty]" value="<?=$product['working_qty'];?>" size="4" ></td>
																																	<?php }?>
																																	<?php if($shipment_detail['status'] != 'Shipped') { ?>
																																		<td align="center">$<?=number_format($total_lcd_cost,2);?></td>
																																		<td align="center"><input type="text" name="list[<?=$product['buyback_product_id'];?>][refurb_cost]" value="<?=$product['refurb_cost'];?>" <?php echo ($shipment_detail['status'] == 'Completed')? 'readonly':''; ?> size="6" ></td>
																																		<?php }?>
																																		<?php if($shipment_detail['status'] == 'Shipped'){?>
																																			<td align="center"><input type="text" name="list[<?=$product['buyback_product_id'];?>][non_working_qty]" value="<?=$product['non_working_qty'];?>" size="4" ></td>
																																			<?php }?>
																																			<?php if($shipment_detail['status'] == 'Received'|| $shipment_detail['status'] == 'Completed'){?>
																																				<td align="center"><input readonly type="text" name="list[<?=$product['buyback_product_id'];?>][non_working_qty]" value="<?=$product['non_working_qty'];?>" size="4" ></td>
																																				<?php }?>
																																				<?php if($shipment_detail['status'] != 'Shipped') { ?>
																																					<td align="center"><input type="text" name="list[<?=$product['buyback_product_id'];?>][non_working_lcd_cost]" value="<?=$product['non_working_lcd_cost'];?>   " <?php echo ($shipment_detail['status'] == 'Completed')? 'readonly':''; ?> size="4" ></td>
																																					<td>$<?=number_format($cost,2);?></td>
																																					<?php }?>
																																					<?php if (($_SESSION['login_as'] == 'admin' || $_SESSION['login_as'] == 'employee')&& $shipment_detail['status'] == 'Received' || $shipment_detail['status'] == 'Completed' ) { ?>
																																						<td>
																																							<?php if ($product['added_to_ship']) { ?>
																																								<center><img src="../images/check.png" alt="" /></center><br>
																																								<?php

																																								$qty_ship=$db->func_query("SELECT a.*, b.package_number from inv_shipment_items a inner join inv_shipments b on a.shipment_id = b.id WHERE a.mapped_sku = '". $product['sku'] ."' AND a.box_id = '". $product['shipment_id'] ."'");
														// print_r($qty_ship);exit;
																																								
													//$maped_sku=$db->func_query("SELECT * from inv_lbb_sku_mapping WHERE id = '". $qty_ship[$i]['mapped_sku_id']."'");
																																								$j++;
													//die("SELECT * from inv_shipment_items WHERE shipment_id = '". $product['added_to_ship'] ."'");
																																								
																																								?> 
																																								<?php foreach ($qty_ship as $shipmentAdded) { ?>
																																									<center><a href="<?php echo $host_path;?>addedit_shipment.php?shipment_id=<?php echo $shipmentAdded['shipment_id'] ;?>"><?php echo $shipmentAdded['package_number'] ?></a>
																																										<br>
																																										Maped Sku: <?php echo linkToProduct($shipmentAdded['product_sku'],$host_path);?>
																																										<br>
																																										Qty:<?php echo $shipmentAdded['qty_shipped'];$i++;?>
																																									</center>
																																									<?php } ?>
																																									<?php } else { ?>
																																										<center><img src="../images/cross.png" alt="" /></center>
																																										<?php } ?>

																																									</td>
																																									<?php }?>

																																								</tr>
																																								<?php
																																								$z++;
																																							}

																																							$z=0;
																																							foreach($manual_products2 as $product)
																																							{
																																								$total_lcds = $oem_total+$non_oem_total;
																																								$total_lcd_cost = ((float)$product['oem_price_a'] * (int)$product['oem_qty_a']) + ((float)$product['oem_price_b'] * (int)$product['oem_qty_b']) + ((float)$product['oem_price_c'] * (int)$product['oem_qty_c']) + ((float)$product['oem_price_d'] * (int)$product['oem_qty_d']) + ((float)$product['non_oem_price_a'] * (int)$product['non_oem_qty_a']) + ((float)$product['non_oem_price_b'] * (int)$product['non_oem_qty_b']) + ((float)$product['non_oem_price_c'] * (int)$product['non_oem_qty_c']) + ((float)$product['non_oem_price_d'] * (int)$product['non_oem_qty_d']);


	        		// Forumla by Saad Ahmed (http://prntscr.com/94res2)                                                                                        
																																								$total_qty_shiped=$product['oem_qty_a']+$product['oem_qty_b']+$product['oem_qty_c']+$product['oem_qty_d']+$product['non_oem_qty_a']+$product['non_oem_qty_b']+$product['non_oem_qty_c']+$product['non_oem_qty_d']+$product['salvage_qty'];                                                                       
																																								$cost = ($shipment_detail['inbound_shipping_cost']+$shipment_detail['outbound_shipping_cost']) / $total_lcds;

																																								$cost = $cost + ($product['working_qty']*$product['refurb_cost']);
																																								$cost = $cost + ((float)$total_lcd_cost - (float)$product['non_working_lcd_cost']); 
																																								if($cost)
																																								{
																																									$cost = $cost / ($product['oem_received_a']+$product['oem_received_b']+$product['oem_received_c']+$product['oem_received_d']+$product['non_oem_received_a']+$product['non_oem_received_b']+$product['non_oem_received_c']+$product['non_oem_received_d']);
																																								}
																																								else
																																								{
																																									$cost = 0.00;
																																								}
																																								?>
																																								<?php if(0){?>
																																								 <tr>
																																									<td><input type="checkbox" name="lbb_ids[<?php echo $product['sku']; ?>]" value="<?php echo $product['id'];?>" /></td>
																																									<td><?=$product['sku'].' - '.$product['description'];?></td>
																																									<td>
																																										<center><?php echo $total_qty_shiped;?></center>
																																									</td>
																																									<td align="center">$<?=number_format($total_lcd_cost,2);?></td>
																																									<td align="center"><input type="text" name="list[<?=$product['sku'];?>][refurb_cost]" value="<?=$product['refurb_cost'];?>" size="6" ></td>

																																									<td align="center"><input type="text" name="list[<?=$product['sku'];?>][non_working_qty]" value="<?=$product['non_working_qty'];?>" size="4" ></td>
																																									<td align="center"><input type="text" name="list[<?=$product['sku'];?>][non_working_lcd_cost]" value="<?=$product['non_working_lcd_cost'];?>"  size="4" ></td>
																																									<td>$<?=number_format($cost,2);?></td>


																																								</tr> 

																																								<?php }?>
																																								<tr class="select_lbbs" data-lbbsku="<?=$product['sku']; ?>" data-buy-back-id="<?php echo $product['buyback_product_id'];?>">
																															<td><input type="checkbox" name="lbb_ids[<?php echo $product['sku']; ?>]" value="<?php echo $product['buyback_product_id'];?>" /></td>
																															<td><?=$product['sku'].' - '.$product['description'];?></td>
																															<td>
																																<center><?php echo $total_qty_shiped;?></center>
																															</td>
																															<?php if($shipment_detail['status'] == 'Shipped'){?>
																																<td align="center"><input type="text" name="list[<?=$product['buyback_product_id'];?>][working_qty]" value="<?=$product['working_qty'];?>" size="4" ></td>
																																<?php }?>
																																<?php if($shipment_detail['status'] == 'Received' || $shipment_detail['status'] == 'Completed'){?>
																																	<td align="center"><input readonly type="text" name="list[<?=$product['buyback_product_id'];?>][working_qty]" value="<?=$product['working_qty'];?>" size="4" ></td>
																																	<?php }?>
																																	<?php if($shipment_detail['status'] != 'Shipped') { ?>
																																		<td align="center">$<?=number_format($total_lcd_cost,2);?></td>
																																		<td align="center"><input type="text" name="list[<?=$product['buyback_product_id'];?>][refurb_cost]" value="<?=$product['refurb_cost'];?>" <?php echo ($shipment_detail['status'] == 'Completed')? 'readonly':''; ?> size="6" ></td>
																																		<?php }?>
																																		<?php if($shipment_detail['status'] == 'Shipped'){?>
																																			<td align="center"><input type="text" name="list[<?=$product['buyback_product_id'];?>][non_working_qty]" value="<?=$product['non_working_qty'];?>" size="4" ></td>
																																			<?php }?>
																																			<?php if($shipment_detail['status'] == 'Received'|| $shipment_detail['status'] == 'Completed'){?>
																																				<td align="center"><input readonly type="text" name="list[<?=$product['buyback_product_id'];?>][non_working_qty]" value="<?=$product['non_working_qty'];?>" size="4" ></td>
																																				<?php }?>
																																				<?php if($shipment_detail['status'] != 'Shipped') { ?>
																																					<td align="center"><input type="text" name="list[<?=$product['buyback_product_id'];?>][non_working_lcd_cost]" value="<?=$product['non_working_lcd_cost'];?>   " <?php echo ($shipment_detail['status'] == 'Completed')? 'readonly':''; ?> size="4" ></td>
																																					<td>$<?=number_format($cost,2);?></td>
																																					<?php }?>
																																					<?php if (($_SESSION['login_as'] == 'admin' || $_SESSION['login_as'] == 'employee')&& $shipment_detail['status'] == 'Received' || $shipment_detail['status'] == 'Completed' ) { ?>
																																						<td>
																																							<?php if ($product['added_to_ship']) { ?>
																																								<center><img src="../images/check.png" alt="" /></center><br>
																																								<?php

																																								$qty_ship=$db->func_query("SELECT a.*, b.package_number from inv_shipment_items a inner join inv_shipments b on a.shipment_id = b.id WHERE a.mapped_sku = '". $product['sku'] ."' AND a.box_id = '". $product['shipment_id'] ."'");
														// print_r($qty_ship);exit;
																																								
													//$maped_sku=$db->func_query("SELECT * from inv_lbb_sku_mapping WHERE id = '". $qty_ship[$i]['mapped_sku_id']."'");
																																								$j++;
													//die("SELECT * from inv_shipment_items WHERE shipment_id = '". $product['added_to_ship'] ."'");
																																								
																																								?> 
																																								<?php foreach ($qty_ship as $shipmentAdded) { ?>
																																									<center><a href="<?php echo $host_path;?>addedit_shipment.php?shipment_id=<?php echo $shipmentAdded['shipment_id'] ;?>"><?php echo $shipmentAdded['package_number'] ?></a>
																																										<br>
																																										Maped Sku: <?php echo linkToProduct($shipmentAdded['product_sku'],$host_path);?>
																																										<br>
																																										Qty:<?php echo $shipmentAdded['qty_shipped'];$i++;?>
																																									</center>
																																									<?php } ?>
																																									<?php } else { ?>
																																										<center><img src="../images/cross.png" alt="" /></center>
																																										<?php } ?>

																																									</td>
																																									<?php }?>

																																								</tr>
																																								<?php
																																								$z++;
																																							}
																																							?>
																																						</table>
																																						<?php }?>
																																					</td>
																																				</tr>

																																				<?php
																																			}
																																			?>

																																			<hr>

																																			<div>	
																																				<?php if($products or $manual_items):?>
																																					<?php


																																					?>

																																					<!-- <table width="30%" cellspacing="0" cellpadding="5px" border="0" align="center" style="border-collapse:collapse;">
																																						<tr>
																																							<th>OEM Total: <?=$oem_total;?></th>
																																							<th>Non-OEM Total: <?=$non_oem_total;?></th>
																																						</tr>
																																					</table> -->
																																					<?php

																																					if($shipment_detail['status']==''):

																																						?>
																																					<br>

																																					<table width="40%" class="addToShipment" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
																																						<thead>
																																							<tr>
																																								<th>#</th>

																																								<th>SKU</th>
																																								<?php if ($_SESSION['login_as'] == 'admin') : ?>
																																									<th>OEM-A</th>
																																									<th>OEM-B</th>
																																									<th>OEM-C</th>
																																									<th>OEM-D</th>
																																									<th>N-OEM-A</th>
																																									<th>N-OEM-B</th>
																																									<th>N-OEM-C</th>
																																									<th>N-OEM-D</th>
																																								<?php endif; ?>
																																								<th>Package</th>
																																								<th>Needed</th>
																																								<th>Total</th>
																																							</tr>
																																						</thead>
																																						<tbody>
																																							<?php
																																							$c_oem = 0;
																																							$c_non_oem = 0;

																																							foreach ($products1 as $i => $product) {
																																								$sku = $db->func_query_first("SELECT sku,description FROM oc_buyback_products WHERE buyback_product_id='".$product['buyback_product_id']."'");
																																								?>
																																								<tr>
																																									<?php if ($_SESSION['add_lbb_shipment']) : ?>
																																									<td class="select">
																																										<input type="checkbox" onchange="selectx(this)" value="<?php echo $sku['sku']; ?>">
																																									</td>
																																								<?php else: ?>
																																								<td align="center"><?=$i+1;?></td>
																																							<?php endif; ?>
																																							<td align=""><?=$sku['sku'];?> - <?=$sku['description'];?><input type="hidden" name="product[<?php echo $sku['sku']; ?>][product_sku]" value="<?=$sku['sku'];?>"><input type="hidden" name="product[<?php echo $sku['sku']; ?>][product_id]" value="<?=$product['buyback_product_id'];?>"><input type="hidden" name="product[<?php echo $sku['sku']; ?>][product_name]" value="<?=$sku['description'];?>"></td>
																																							<?php if ($_SESSION['login_as'] == 'admin') : ?>
																																							<td align="center"><?=$product['oem_received_a'];?></td>
																																							<td align="center"><?=$product['oem_received_b'];?></td>
																																							<td align="center"><?=$product['oem_received_c'];?></td>
																																							<td align="center"><?=$product['oem_received_d'];?></td>
																																							<td align="center"><?=$product['non_oem_received_a'];?></td>
																																							<td align="center"><?=$product['non_oem_received_b'];?></td>
																																							<td align="center"><?=$product['non_oem_received_c'];?></td>
																																							<td align="center"><?=$product['non_oem_received_d'];?></td>
																																						<?php endif; ?>
																																						<td align="center">
																																							<?php $totalShipped = 0;?>
																																							<?php foreach ($shipments as $key => $shipment) { ?>
																																								<?php if ($shipment['items'][$sku['sku']]) { ?>
																																									<?php echo linkToShipment($shipment['id'], $host_path, $shipment['items'][$sku['sku']]['qty_shipped'] . '-' . $shipment['package_number'], ' target="_blank"'); ?>
																																									<?php $totalShipped += $shipment['items'][$sku['sku']]['qty_shipped']; ?>
																																									<br>
																																									<?php } ?>
																																									<?php } ?>
																																								</td>
																																								<td align="center"><?= ($product['non_oem_received_a'] + $product['non_oem_received_b'] + $product['non_oem_received_c'] + $product['non_oem_received_d'] + $product['oem_received_a'] + $product['oem_received_b'] + $product['oem_received_c'] + $product['oem_received_d']) - $totalShipped; ?><br><input type="number" style="width: 70px;" min="0" value="0" onchange="$(this).parent().parent().find('input[type=checkbox]').prop('checked', true).trigger('change');" name="product[<?php echo $sku['sku']; ?>][qty_shipped]"></td>
																																								<td align="center"><?=$product['non_oem_received_a'] + $product['non_oem_received_b'] + $product['non_oem_received_c'] + $product['non_oem_received_d'] + $product['oem_received_a'] + $product['oem_received_b'] + $product['oem_received_c'] + $product['oem_received_d'];?></td>
																																							</tr>
																																							<?php
																																						}
																																						?>


																																					</tbody>

																																				</table>
																																				<?php if ($_SESSION['add_lbb_shipment']) : ?>
																																					<table width="30%" cellspacing="0" cellpadding="5px" border="0" align="center" style="border-collapse:collapse;">
																																						<tr>
																																							<th><input class="button" type="button" class="shipmentBtn" value="Add to Shipment" onclick="selectGetShipment();" /></th>
																																						</tr>
																																					</table>
																																				<?php endif; ?>
																																				<?php
																																				endif;
																																				?>


																																				<br><br>
																																				<!-- This table shows single line sku and sum of oem and non oem by haris--> 
																																				<?php 

																																				if($shipment_detail['status'] != 'Received' && $shipment_detail['status'] != 'Shipped' && $shipment_detail['status'] != 'Completed'){
								// echo 'here';exit;
																																					
																																					?>
																																					<table width="50%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
																																						<thead>
																																							<tr>
																																								<th>#</th>
																																								<th>QTY</th>
																																								<!--	<th>Buyback Shipment #</th> -->
																																								<th>LCD Model</th>
																																								<?php if ($_SESSION['login_as'] == 'admin' || $_SESSION['login_as'] == 'employee') : ?>
																																								<th style="display:none;">OEM-A</th>
																																								<th style="display:none;">OEM-B</th>
																																								<th style="display:none;">OEM-C</th>
																																								<th style="display:none;">OEM-D</th>
																																								<th style="display:none;">N-OEM-A</th>
																																								<th style="display:none;">N-OEM-B</th>
																																								<th style="display:none;">N-OEM-C</th>
																																								<th style="display:none;">N-OEM-D</th>
																																							<?php endif; ?>
																																							
																																							<?php if (($_SESSION['login_as'] == 'admin' || $_SESSION['login_as'] == 'employee')&& $shipment_detail['status'] == 'Received' ) { ?>
																																								<th>Added to Shipment</th>
																																								<?php }?>
																																							</tr>
																																						</thead>
																																						<tbody>
																																							<?php 
																																							$i1 = 0;
																																							$i2 = 0;
																																							$i3 = 0;
																																							$i4 = 0;
																																							$i5 = 0;
																																							$i6 = 0;
																																							$i7 = 0;
																																							$i8 = 0;
																																							foreach($box_data as $data){

																																								$i1+=$data['oemsuma'];
																																								$i2+=$data['oemsumb'];
																																								$i3+=$data['oemsumc'];
																																								$i4+=$data['oemsumd'];
																																								$i5+=$data['nonoemsuma'];
																																								$i6+=$data['nonoemsumb'];
																																								$i7+=$data['nonoemsumc'];
																																								$i8+=$data['nonoemsumd'];
																																								$i9+=$data['salvagesum'];
																																								$total_qty=$data['oemsuma']+$data['oemsumb']+$data['oemsumc']+$data['oemsumd']+$data['nonoemsuma']+$data['nonoemsumb']+$data['nonoemsumc']+$data['nonoemsumd']+$data['salvagesum'];
											// $lbb_sku=$db->func_query_first("SELECT * FROM inv_lbb_sku_mapping WHERE lbb_sku='".$data['sku']."'");
											//print_r($lbb_sku[0]['lbb_sku']);exit;
																																								?>

																																								<tr>
																																									<td>
																																										<input type="checkbox" name="reject_id[]" value="<?php echo $data['buyback_product_id'];?>" />
																																									</td>
																																									<td align="center"> <?php echo $total_qty;	?> </td>

										<!--	<td align="center">
										<a href="<?php $host_path;?>shipment_detail.php?shipment=<?php echo $buyback_number;?>"><?php echo $buyback_number;?></a>
									</td> -->

									<td align="center">
										<?php echo $data['sku'];?>
									</td>
									<?php if ($_SESSION['login_as'] == 'admin' || $_SESSION['login_as'] == 'employee') : ?>
									<td style="display:none;" align="center">
										<?php echo $data['oemsuma'];?> x $<?=number_format($data['oem_a_price'],2);?>
									</td>
									<td style="display:none;" align="center">
										<?php echo $data['oemsumb'];?> x $<?=number_format($data['oem_b_price'],2);?>
									</td>
									<td style="display:none;" align="center">
										<?php echo $data['oemsumc'];?> x $<?=number_format($data['oem_c_price'],2);?>
									</td>
									<td style="display:none;" align="center">
										<?php echo $data['oemsumd'];?> x $<?=number_format($data['oem_d_price'],2);?>
									</td>

									<td style="display:none;" align="center">
										<?php echo $data['nonoemsuma'];?> x $<?=number_format($data['non_oem_a_price'],2);?>
									</td>

									<td style="display:none;" align="center">
										<?php echo $data['nonoemsumb'];?> x $<?=number_format($data['non_oem_b_price'],2);?>
									</td>

									<td style="display:none;" align="center">
										<?php echo $data['nonoemsumc'];?> x $<?=number_format($data['non_oem_c_price'],2);?>
									</td>

									<td style="display:none;" align="center">
										<?php echo $data['nonoemsumd'];?> x $<?=number_format($data['non_oem_d_price'],2);?>
									</td>
								<?php endif; ?>
								<?php if (($_SESSION['login_as'] == 'admin' || $_SESSION['login_as'] == 'employee')&& $shipment_detail['status'] == 'Received' ) { ?>
									<td>
										<?php if ($product['added_to_ship']) { ?>
											<img src="../images/check.png" alt="" /><br>
											<?php $xship = $db->func_query_first("SELECT * from inv_shipments WHERE id = '". $product['added_to_ship'] ."'"); ?>
											<?php echo linkToShipment($xship['id'], $host_path, $xship['package_number']) ?>
											<?php } else { ?>
												<center><img src="../images/cross.png" alt="" /></center>
												<?php } ?>

											</td>
											<?php }?>
										</tr>

										<?php } ?>
										<?php
										// print_r($manual_items);exit;
										foreach($manual_items as $manual)
										{
											$total_oem+=$manual['oem_qty_a']+$manual['oem_qty_b']+$manual['oem_qty_c']+$manual['oem_qty_d'];
											$total_non_oem+=$manual['non_oem_qty_a']+$manual['non_oem_qty_b']+$manual['non_oem_qty_c']+$manual['non_oem_qty_d'];
											$total_qty=$manual['oem_qty_a']+$manual['oem_qty_b']+$manual['oem_qty_c']+$manual['oem_qty_d']+$manual['non_oem_qty_a']+$manual['non_oem_qty_b']+$manual['non_oem_qty_c']+$manual['non_oem_qty_d']+$manual['salvage_qty'];
											$i1+=$manual['oem_qty_a'];
											$i2+=$manual['oem_qty_b'];
											$i3+=$manual['oem_qty_c'];
											$i4+=$manual['oem_qty_d'];
											$i5+=$manual['non_oem_qty_a'];
											$i6+=$manual['non_oem_qty_b'];
											$i7+=$manual['non_oem_qty_c'];
											$i8+=$manual['non_oem_qty_d'];
											$i9+=$manual['salvage_qty'];
											?>
											<tr>
												<td>
													<input type="checkbox" name="reject_id[m<?php echo $manual['id'];?>]" value="<?php echo $manual['id'];?>" />
												</td>

												<td align="center">
													<?php echo $total_qty;?>
												</td>

												<td align="center">
													<?php echo $manual['sku'];?>
												</td>

												<td style="display:none;" align="center">
													<?php echo $manual['oem_qty_a'];?> x $<?=number_format($manual['oem_price_a'],2);?>
												</td>

												<td style="display:none;" align="center">
													<?php echo $manual['oem_qty_b'];?> x $<?=number_format($manual['oem_price_b'],2);?>
												</td>

												<td style="display:none;" align="center">
													<?php echo $manual['oem_qty_c'];?> x $<?=number_format($manual['oem_price_c'],2);?>
												</td>
												
												<td style="display:none;" align="center">
													<?php echo $manual['oem_qty_d'];?> x $<?=number_format($manual['oem_price_d'],2);?>
												</td>
												
												<td style="display:none;" align="center">
													<?php echo $manual['non_oem_qty_a'];?> x $<?=number_format($manual['non_oem_price_a'],2);?>
												</td>

												<td style="display:none;" align="center">
													<?php echo $manual['non_oem_qty_b'];?> x $<?=number_format($manual['non_oem_price_b'],2);?>
												</td>
												
												<td style="display:none;" align="center">
													<?php echo $manual['non_oem_qty_c'];?> x $<?=number_format($manual['non_oem_price_c'],2);?>
												</td>
												
												<td style="display:none;" align="center">
													<?php echo $manual['non_oem_qty_d'];?> x $<?=number_format($manual['non_oem_price_d'],2);?>
												</td>
											</tr>
											<?php
										}
										?>
										<tr style="display:none;">
											<th colspan="2" align="right">Total: </th>
											<th align="center"><?=number_format($i1);?></th>
											<th align="center"><?=number_format($i2);?></th>
											<th align="center"><?=number_format($i3);?></th>
											<th align="center"><?=number_format($i4);?></th>
											<th align="center"><?=number_format($i5);?></th>
											<th align="center"><?=number_format($i6);?></th>
											<th align="center"><?=number_format($i7);?></th>
											<th align="center"><?=number_format($i8);?></th>
											<th colspan="2"></th>
											
										</tr>
<!-- 										<tr>
											<td colspan="10" align="left">
												<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
											</td>

											<td colspan="2" align="right">
												<?php  echo $splitPage->display_links(10,$parameters); ?>
											</td>
										</tr> -->
									</tbody>   
								</table>  
								<h3 >Total LCD Qty: <?php echo $oem_total+$non_oem_total;?></h3>
								<?php }?> 
								<!--Table for add to shipment-->
								<?php if($shipment_detail['status'] == ''){?>
									<table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
										<thead>
											<tr>
												<th>#</th>
												<th>Buyback Shipment #</th>
												<th>SKU</th>
												<?php if ($_SESSION['login_as'] == 'admin') : ?>
												<th>OEM-A</th>
												<th>OEM-B</th>
												<th>OEM-C</th>
												<th>OEM-D</th>
												<th>N-OEM-A</th>
												<th>N-OEM-B</th>
												<th>N-OEM-C</th>
												<th>N-OEM-D</th>
											<?php endif; ?>
											<th>Notes</th>
											<?php if (($_SESSION['login_as'] == 'admin' || $_SESSION['login_as'] == 'employee')&& $shipment_detail['status'] == 'Received' ) { ?>
												<th>Added to Shipment</th>
												<?php }?>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php $i = $splitPage->display_i_count();
											$count = 1; 
											$shipment_id = $products[0]['shipment_id'];
											$total_oem = 0;
											$total_non_oem = 0;
											foreach($products as $product):
												if($shipment_id != $product['shipment_id']){
													$count = 1; 
													$shipment_id = $product['shipment_id'];
												}
												$total_oem+=(int)$product['oem_received_a']+(int)$product['oem_received_b']+(int)$product['oem_received_c']+(int)$product['oem_received_d'];
												$total_non_oem+=(int)$product['non_oem_received_a']+(int)$product['non_oem_received_b']+(int)$product['non_oem_received_c']+(int)$product['non_oem_received_d'];

												$sku = $db->func_query_first("SELECT * FROM oc_buyback_products WHERE buyback_product_id='".$product['buyback_product_id']."'");
												$buyback_number = $db->func_query_first_cell("SELECT shipment_number FROM oc_buyback WHERE buyback_id='".$sku['buyback_id']."'");
												?>
												<?php $reason = $product['reason']; ?>

												<tr>
													<td>
														<input type="checkbox" name="lbb_id[]" value="<?php echo $product['buyback_product_id'];?>" />
													</td>

													<td align="center">
														<a href="<?php $host_path;?>shipment_detail.php?shipment=<?php echo $buyback_number;?>"><?php echo $buyback_number;?></a>
													</td>

													<td align="center">
														<?php echo $sku['sku'];?>
													</td>
													<?php if ($_SESSION['login_as'] == 'admin') : ?>
													<td align="center">
														<?php echo $product['oem_received_a'];?> x $<?=number_format($sku['oem_a_price'],2);?>
													</td>

													<td align="center">
														<?php echo $product['oem_received_b'];?> x $<?=number_format($sku['oem_b_price'],2);?>
													</td>

													<td align="center">
														<?php echo $product['oem_received_c'];?> x $<?=number_format($sku['oem_c_price'],2);?>
													</td>

													<td align="center">
														<?php echo $product['oem_received_d'];?> x $<?=number_format($sku['oem_d_price'],2);?>
													</td>
													<td align="center">
														<?php echo $product['non_oem_received_a'];?> x $<?=number_format($sku['non_oem_a_price'],2);?>
													</td>

													<td align="center">
														<?php echo $product['non_oem_received_b'];?> x $<?=number_format($sku['non_oem_b_price'],2);?>
													</td>

													<td align="center">
														<?php echo $product['non_oem_received_c'];?> x $<?=number_format($sku['non_oem_c_price'],2);?>
													</td>

													<td align="center">
														<?php echo $product['non_oem_received_d'];?> x $<?=number_format($sku['non_oem_d_price'],2);?>
													</td>
												<?php endif; ?>
												<td align="center">
													<input type="text" name="reason[<?php echo $product['id']?>]" value="<?php echo $reason; ?>" />
												</td>
												<?php if (($_SESSION['login_as'] == 'admin' || $_SESSION['login_as'] == 'employee')&& $shipment_detail['status'] == 'Received' ) { ?>
													<td>
														<?php if ($product['added_to_ship']) { ?>
															<img src="../images/check.png" alt="" /><br>
															<?php $xship = $db->func_query_first("SELECT * from inv_shipments WHERE id = '". $product['added_to_ship'] ."'"); ?>
															<?php echo linkToShipment($xship['id'], $host_path, $xship['package_number']) ?>
															<?php } else { ?>
																<center><img src="../images/cross.png" alt="" /></center>
																<?php } ?>

															</td>
															<?php }?>
															<td align="center">
																<a class="fancybox2 fancybox.iframe" href="<?php echo $host_path;?>buyback/move_box_item.php?id=<?php echo $product['id'];?>&shipment_id=<?php echo $product['shipment_id']?>&manual=0&buyback_product_id=<?php echo $product['buyback_product_id']?>&oema=<?php echo $product['oem_received_a'];?>&oemb=<?php echo $product['oem_received_b'];?>&oemc=<?php echo $product['oem_received_c'];?>&oemd=<?php echo $product['oem_received_d'];?>&n_oema=<?php echo $product['non_oem_received_a'];?>&n_oemb=<?php echo $product['non_oem_received_b'];?>&n_oemc=<?php echo $product['non_oem_received_c'];?>&n_oemd=<?php echo $product['non_oem_received_d'];?>&sku=<?php echo $sku['sku'];?>">Transfer</a>
															</td>
														</tr>

														<?php 

														$i++; endforeach; ?>
														<?php

														foreach($manual_items as $manual)
														{
															$total_oem+=$manual['oem_qty_a']+$manual['oem_qty_b']+$manual['oem_qty_c']+$manual['oem_qty_d'];
															$total_non_oem+=$manual['non_oem_qty_a']+$manual['non_oem_qty_b']+$manual['non_oem_qty_c']+$manual['non_oem_qty_d'];
															$total_qty=$total_oem+$total_non_oem;

															?>
															<tr>
																<td>
																	<input type="checkbox" name="lbb_id[m<?php echo $manual['id'];?>]" value="<?php echo $manual['id'];?>" />
																</td>

																<td align="center">
																	<?php echo $manual['item_condition'];?>
																</td>

																<td align="center">
																	<?php echo $manual['sku'];?>
																</td>

																<td align="center">
																	<?php echo $manual['oem_qty_a'];?> x $<?=number_format($manual['oem_price_a'],2);?>
																</td>

																<td align="center">
																	<?php echo $manual['oem_qty_b'];?> x $<?=number_format($manual['oem_price_b'],2);?>
																</td>

																<td align="center">
																	<?php echo $manual['oem_qty_c'];?> x $<?=number_format($manual['oem_price_c'],2);?>
																</td>
																
																<td align="center">
																	<?php echo $manual['oem_qty_d'];?> x $<?=number_format($manual['oem_price_d'],2);?>
																</td>
																
																<td align="center">
																	<?php echo $manual['non_oem_qty_a'];?> x $<?=number_format($manual['non_oem_price_a'],2);?>
																</td>

																<td align="center">
																	<?php echo $manual['non_oem_qty_b'];?> x $<?=number_format($manual['non_oem_price_b'],2);?>
																</td>
																
																<td align="center">
																	<?php echo $manual['non_oem_qty_c'];?> x $<?=number_format($manual['non_oem_price_c'],2);?>
																</td>
																
																<td align="center">
																	<?php echo $manual['non_oem_qty_d'];?> x $<?=number_format($manual['non_oem_price_d'],2);?>
																</td>

																<td align="center">
																	<?php $m_reason=$db->func_query_first_cell("SELECT name FROM inv_buyback_shipment_reasons WHERE id='".$manual['reason']."'");?>
																	<input type="text" name="reason[<?php echo $manual['id']?>]" value="<?php echo $m_reason; ?>" />
																</td>
																<td align="center">
																	<a class="fancybox2 fancybox.iframe" href="<?php echo $host_path;?>buyback/move_box_item.php?id=<?php echo $manual['id'];?>&shipment_id=<?php echo $manual['shipment_id']?>&manual=1&oema=<?php echo $manual['oem_qty_a'];?>&oemb=<?php echo $manual['oem_qty_b'];?>&oemc=<?php echo $manual['oem_qty_c'];?>&oemd=<?php echo $manual['oem_qty_d'];?>&n_oema=<?php echo $manual['non_oem_qty_a'];?>&n_oemb=<?php echo $manual['non_oem_qty_b'];?>&n_oemc=<?php echo $manual['non_oem_qty_c'];?>&n_oemd=<?php echo $manual['non_oem_qty_d'];?>&sku=<?php echo $manual['sku'];?>">Transfer</a>
																</td>
															</tr>
															<?php
														}
														?>
														<tr>
															<th colspan="3" align="right">Total: </th>
															<th align="center"><?=number_format($total_oem);?></th>
															<th align="center"><?=number_format($total_non_oem);?></th>
															<th colspan="2"></th>
														</tr>
														<tr>
															<td colspan="4" align="left">
																<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
															</td>

															<td colspan="3" align="right">
																<?php  echo $splitPage->display_links(10,$parameters); ?>
															</td>
														</tr>
													</tbody>   
												</table>
												<?php }?>
												<!--END of add to shipment table-->



												<div align="center">
													<br />
													<?php if($_SESSION['login_as']=='admin'):?>
														<input type="submit" name="save" value="Save" />
													<?php endif;?>
													<?php if($shipment_detail['status'] == 'Pending' && $_SESSION['group']!='Servicer') { ?>
														<input type="submit" name="SaveAndShip" value="Save & Ship" />
														<?php } else if($shipment_detail['status'] == 'Shipped') { ?>
															<input type="submit" name="SaveAndReceive" value="Save & Receive" />
															<?php } else if($shipment_detail['status'] == 'Received') { ?>
																<input type="submit" name="SaveAndComplete" value="Save & Complete" />
																<?php } ?>
															</div>

														<?php endif;?>

													</div>
													<div align="center">
														<table width="80%" border="0" cellpadding="5" cellspacing="0" style="border-collapse:collapse">
															<tr>
																<td>
																	<form method="post" action="">
																		<table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
																			<tr>
																				<td>

																					<b>Comment</b>
																				</td>
																				<td>
																					<textarea rows="5" cols="30" name="comment" ></textarea>


																				</td>
																			</tr>

																			<tr>
																				<td colspan="2" align="center"> <input type="submit" class="button" name="addcomment" value="Add Comment" />	</td>
																			</tr> 	   
																		</table>
																	</form>
																</td>
																<td valign="top">

																	<table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
																		<tr>
																			<th>Date</th>
																			<th>Comment</th>


																			<th>Added By</th>


																		</tr>
																		<?php
																		$comments = $db->func_query("SELECT * FROM inv_buyback_shipment_box_comments WHERE buyback_shipment_box_id='".$shipment_id."'");
																		foreach($comments as $comment)
																		{
																			?>
																			<tr>
																				<td><?php echo americanDate($comment['date_added']);?></td>
																				<td><?php echo $comment['comment'];?></td>


																				<td><?php echo get_username($comment['user_id']);?></td>

																			</tr>
																			<?php 

																		}
																		?> 

																	</table>

																</td>
															</tr>
														</table>
														<br><br>
														<?php
														$tracker = $db->func_query_first("SELECT * FROM inv_tracker WHERE tracking_code='".$shipment_detail['tracking_number']."'");
														if($tracker)
														{
															?>
															<table align="center" border="1" cellspacing="0" cellpadding="5" width="70%">
																<tr>
																	<th colspan="2">Tracking ID: <?=$tracker['tracker_id'];?></th>
																	<th colspan="2" align="right">Code: <?=$tracker['tracking_code'];?></th>
																</tr>
																<tr>
																	<th>Date Time</th>
																	<th>Message</th>
																	<th align="center">Status</th>
																	<th>Location</th>
																</tr>  
																<?php
																$tracker_statuses = $db->func_query("SELECT * FROM inv_tracker_status WHERE tracker_id='".$tracker['tracker_id']."' order by id desc");
																foreach($tracker_statuses as $tracker_status)
																{
																	$tracker_status['datetime'] = str_replace(array('T','Z'), ' ', $tracker_status['datetime']);
																	$location = json_decode($tracker_status['tracking_location'],true);
																	?>
																	<tr>
																		<td><?=americanDate($tracker_status['datetime']);?></td>
																		<td><?=$tracker_status['message'];?></td>
																		<td align="center"><?=$tracker_status['status'];?></td>
																		<td><?php echo $location['city'].', '.$location['state'].', '.$location['zip'];?></td>
																	</tr>
																	<?php
																}?>
															</table>
															<br><br>
															<?php
														}
														?>
													</div>
												</div>
												<?php 
												$log= $db->func_query("SELECT * FROM inv_lbb_items_log WHERE `to` = '".$shipment_detail['package_number']."'");
												?>
												<?php if($_SESSION['login_as']=='admin'){?>
													<div id="tabSource" class="makeTabs">
														<div align="center">
															
															
															<table id="table1" width="99%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
																<thead>
																	<tr>
																		<th>#</th>
																		<th>LCD Origin</th>
																		<th>SKU</th>
																		<?php if ($_SESSION['login_as'] == 'admin') : ?>
																		<th>OEM A</th>
																		<th>OEM A-</th>
																		<th>OEM B</th>
																		<th>OEM C</th>
																		<th>N-OEM A</th>
																		<th>N-OEM A-</th>
																		<th>N-OEM B</th>
																		<th>N-OEM C</th>
																		<th>Salvage</th>
																	<?php endif; ?>
																	<th>Notes</th>
																	<?php if ($_SESSION['login_as'] == '' ) { ?>
																		<th>Added to Shipment</th>
																		<?php }?>
																		<th>Action</th>
																	</tr>
																</thead>
																<tbody>
																	<?php $i = $splitPage->display_i_count();

																	$i1 = 0;
																	$i2 = 0;
																	$i3 = 0;
																	$i4 = 0;
																	$i5 = 0;
																	$i6 = 0;
																	$i7 = 0;
																	$i8 = 0;
																	$i9 = 0;

																	$count = 1; 
																	$shipment_id = $productss[0]['shipment_id'];
																	$total_oem = 0;
																	$total_non_oem = 0;
																	foreach($productss as $product):
																		if($shipment_id != $product['shipment_id']){
																			$count = 1; 
																			$shipment_id = $product['shipment_id'];
																		}
																		$total_oem+=(int)$product['oem_received_a']+(int)$product['oem_received_b']+(int)$product['oem_received_c']+(int)$product['oem_received_d'];
																		$total_non_oem+=(int)$product['non_oem_received_a']+(int)$product['non_oem_received_b']+(int)$product['non_oem_received_c']+(int)$product['non_oem_received_d'];

																		$sku = $db->func_query_first("SELECT * FROM oc_buyback_products WHERE buyback_product_id='".$product['buyback_product_id']."'");
																		$buyback_number = $db->func_query_first_cell("SELECT shipment_number FROM oc_buyback WHERE buyback_id='".$sku['buyback_id']."'");
																		if(!$buyback_number){
																			$box_num = $db->func_query_first_cell("SELECT description FROM oc_buyback_products WHERE buyback_product_id='".$sku['buyback_product_id']."'");
																		}

																		$i1+=$product['oem_received_a'];
																		$i2+=$product['oem_received_b'];
																		$i3+=$product['oem_received_c'];
																		$i4+=$product['oem_received_d'];
																		$i5+=$product['non_oem_received_a'];
																		$i6+=$product['non_oem_received_b'];
																		$i7+=$product['non_oem_received_c'];
																		$i8+=$product['non_oem_received_d'];
																		$i9+=$product['salvage_received'];

											// $lbb_sku=$db->func_query_first("SELECT * FROM inv_lbb_sku_mapping WHERE lbb_sku='".$sku['sku']."'");

																		?>
																		<?php $reason = $product['reason']; ?>

																		<tr class="list_items" >
																			<td>
																				<input type="checkbox" name="lbb_check[<?php echo $product['id']?>]" value="<?php echo $product['buyback_product_id'];?>" 
																				class="order_checkboxes"
																				/>
																			</td>

																			<td align="center">
																				<?php if($buyback_number){ ?>
																					<a href="<?php $host_path;?>shipment_detail.php?shipment=<?php echo $buyback_number;?>"><?php echo $buyback_number;?></a>
																					<?php } else if ($box_num) {?>
																						<?php echo $box_num;?>
																						<?php } ?>
																					</td>

																					<td align="center">
																						<?php echo $sku['sku'];?>
																						<input type="hidden" id="sku<?php echo $product['id'];?>" class="read_class" style="width:25px;text-align:right;" readonly value="<?php echo $sku['sku'];?>">
																						<input type="hidden" id="buyback_product_id<?php echo $product['id'];?>" class="read_class" style="width:25px;text-align:right;" readonly value="<?php echo $product['buyback_product_id'];?>">
																					</td>
																					<?php if ($_SESSION['login_as'] == 'admin') : ?>
																					<td style="width:100px;" align="center">
																					<input type="text" align="right" id="oem_a_qty<?php echo $product['id'];?>" class="read_class" style="width:25px;text-align:right;" readonly value="<?php echo $product['oem_received_a'];?>">x $<input id="oem_a_price<?php echo $product['id'];?>" type="text" class="read_class" style="width:40px;" readonly value="<?=number_format($sku['oem_a_price'],2);?>">
																					</td>

																					<td style="width:100px;" align="center">
																						<input type="text" id="oem_b_qty<?php echo $product['id'];?>" class="read_class" style="text-align:right;width:25px;" readonly value="<?php echo $product['oem_received_b'];?>">x $<input id="oem_b_price<?php echo $product['id'];?>" type="text" class="read_class" style="width:40px;" readonly value="<?=number_format($sku['oem_b_price'],2);?>">
																					</td>

																					<td style="width:100px;" align="center">
																						<input type="text" id="oem_c_qty<?php echo $product['id'];?>" class="read_class" style="text-align:right;width:25px;" readonly value="<?php echo $product['oem_received_c'];?>">x $<input id="oem_c_price<?php echo $product['id'];?>" type="text" class="read_class" style="width:40px;" readonly value="<?=number_format($sku['oem_c_price'],2);?>">
																					</td>

																					<td  style="width:100px;" align="center">
																						<input type="text" id="oem_d_qty<?php echo $product['id'];?>" class="read_class" style="text-align:right;width:25px;" readonly value="<?php echo $product['oem_received_d'];?>">x $<input id="oem_d_price<?php echo $product['id'];?>" type="text" class="read_class" style="width:40px;" readonly value="<?=number_format($sku['oem_d_price'],2);?>">
																					</td>
																					<td style="width:100px;" align="center">
																						<input type="text" id="non_oem_a_qty<?php echo $product['id'];?>" class="read_class" style="text-align:right;width:25px;" readonly value="<?php echo $product['non_oem_received_a'];?>">x $<input id="non_oem_a_price<?php echo $product['id'];?>" type="text" class="read_class" style="width:40px;" readonly value="<?=number_format($sku['non_oem_a_price'],2);?>">
																					</td>

																					<td style="width:100px;" align="center">
																						<input type="text" id="non_oem_b_qty<?php echo $product['id'];?>" class="read_class" style="text-align:right;width:25px;" readonly value="<?php echo $product['non_oem_received_b'];?>">x $<input id="non_oem_b_price<?php echo $product['id'];?>" type="text" class="read_class" style="width:40px;" readonly value="<?=number_format($sku['non_oem_b_price'],2);?>">
																					</td>

																					<td style="width:100px;" align="center">
																						<input type="text" id="non_oem_c_qty<?php echo $product['id'];?>" class="read_class" style="text-align:right;width:25px;" readonly value="<?php echo $product['non_oem_received_c'];?>">x $<input id="non_oem_c_price<?php echo $product['id'];?>" type="text" class="read_class" style="width:40px;" readonly value="<?=number_format($sku['non_oem_c_price'],2);?>">
																					</td>

																					<td style="width:100px;" align="center">
																						<input type="text" id="non_oem_d_qty<?php echo $product['id'];?>" class="read_class" style="text-align:right;width:25px;" readonly value="<?php echo $product['non_oem_received_d'];?>">x $<input id="non_oem_d_price<?php echo $product['id'];?>" type="text" class="read_class" style="width:40px;" readonly value="<?=number_format($sku['non_oem_d_price'],2);?>">
																					</td>

																					<td style="width:100px;" align="center">
																						<input type="text" id="salvage_qty<?php echo $product['id'];?>" class="read_class" style="text-align:right;width:25px;" readonly value="<?php echo $product['salvage_received'];?>">x $<input type="text" id="salvage_price<?php echo $product['id'];?>" class="read_class" style="width:40px;" readonly value="<?=number_format($sku['salvage_price'],2);?>">
																					</td>
																				<?php endif; ?>
																				<td align="center">

																					<input type="text" name="reason[<?php echo $product['id']?>]" value="<?php echo $reason; ?>" />
																				</td>
																				<?php if ($_SESSION['login_as'] == '' ) { ?>
																					<td>
																						<?php if ($product['added_to_ship']) { ?>
																							<img src="../images/check.png" alt="" /><br>
																							<?php $xship = $db->func_query_first("SELECT * from inv_shipments WHERE id = '". $product['added_to_ship'] ."'"); ?>
																							<?php echo linkToShipment($xship['id'], $host_path, $xship['package_number']) ?>
																							<?php } else { ?>
																								<center><img src="../images/cross.png" alt="" /></center>
																								<?php } ?>

																							</td>
																							<?php }?>
																							<td align="center">
																								<a class="fancybox2 fancybox.iframe" href="<?php echo $host_path;?>buyback/move_box_item.php?id=<?php echo $product['id'];?>&shipment_id=<?php echo $product['shipment_id']?>&manual=0&buyback_product_id=<?php echo $product['buyback_product_id']?>&oema=<?php echo $product['oem_received_a'];?>&oemb=<?php echo $product['oem_received_b'];?>&oemc=<?php echo $product['oem_received_c'];?>&oemd=<?php echo $product['oem_received_d'];?>&n_oema=<?php echo $product['non_oem_received_a'];?>&n_oemb=<?php echo $product['non_oem_received_b'];?>&n_oemc=<?php echo $product['non_oem_received_c'];?>&n_oemd=<?php echo $product['non_oem_received_d'];?>&sku=<?php echo $sku['sku'];?>">Transfer</a> | 
																								<a id="edit_btn_<?= $product['id']; ?>" href="javascript:void(0)" onClick="editThis('<?= $product['id']; ?>')">Edit</a> | 
																								<a id="save_btn_<?= $product['id']; ?>" style="display: none;" href="javascript:void(0);" onClick="saveThis('<?= $product['id']; ?>')">Save</a>
																							</td>
																						</tr>

																						<?php 

																						$i++; endforeach; ?>
																						<?php

																						foreach($manual_items as $manual)
																						{
																							$total_oem+=$manual['oem_qty_a']+$manual['oem_qty_b']+$manual['oem_qty_c']+$manual['oem_qty_d'];
																							$total_non_oem+=$manual['non_oem_qty_a']+$manual['non_oem_qty_b']+$manual['non_oem_qty_c']+$manual['non_oem_qty_d'];
																							$total_qty=$total_oem+$total_non_oem;
																							$i1+=$manual['oem_qty_a'];
																							$i2+=$manual['oem_qty_b'];
																							$i3+=$manual['oem_qty_c'];
																							$i4+=$manual['oem_qty_d'];
																							$i5+=$manual['non_oem_qty_a'];
																							$i6+=$manual['non_oem_qty_b'];
																							$i7+=$manual['non_oem_qty_c'];
																							$i8+=$manual['non_oem_qty_d'];
																							$i9+=$manual['salvage_qty'];
																							?>
																							<tr>
																								<td>
																									<input type="checkbox" id="manual_prod_id<?php echo $manual['id'];?>" name="lbb_check[m<?php echo $manual['id'];?>]" value="<?php echo $manual['id'];?>" />
																								</td>
																								
																								<td align="center">
																									<?php echo $manual['item_condition'];?>
																								</td>

																								<td align="center">
																									<?php echo $manual['sku'];?>
																								</td>

																					  		<td style="width:100px;" align="center">
																									<input type="text" align="right" id="manual_oem_a_qty<?php echo $manual['id'];?>" class="read_class" style="width:25px;text-align:right;" readonly value="<?php echo $manual['oem_qty_a'];?>">x $<input id="manual_oem_a_price<?php echo $manual['id'];?>" type="text" class="read_class" style="width:40px;" readonly value="<?=number_format($manual['oem_price_a'],2);?>">
																								</td>

																								<td style="width:100px;" align="center">
																									<input type="text" id="manual_oem_b_qty<?php echo $manual['id'];?>" class="read_class" style="text-align:right;width:25px;" readonly value="<?php echo $manual['oem_qty_b'];?>">x $<input id="manual_oem_b_price<?php echo $manual['id'];?>" type="text" class="read_class" style="width:40px;" readonly value="<?=number_format($manual['oem_price_b'],2);?>">
																								</td>

																								<td style="width:100px;" align="center">
																									<input type="text" id="manual_oem_c_qty<?php echo $manual['id'];?>" class="read_class" style="text-align:right;width:25px;" readonly value="<?php echo $manual['oem_qty_c'];?>">x $<input id="manual_oem_c_price<?php echo $manual['id'];?>" type="text" class="read_class" style="width:40px;" readonly value="<?=number_format($manual['oem_price_c'],2);?>">
																								</td>

																								<td  style="width:100px;" align="center">
																									<input type="text" id="manual_oem_d_qty<?php echo $manual['id'];?>" class="read_class" style="text-align:right;width:25px;" readonly value="<?php echo $manual['oem_qty_d'];?>">x $<input id="manual_oem_d_price<?php echo $manual['id'];?>" type="text" class="read_class" style="width:40px;" readonly value="<?=number_format($manual['oem_price_d'],2);?>">
																								</td>
																								<td style="width:100px;" align="center">
																									<input type="text" id="manual_non_oem_a_qty<?php echo $manual['id'];?>" class="read_class" style="text-align:right;width:25px;" readonly value="<?php echo $manual['non_oem_qty_a'];?>">x $<input id="manual_non_oem_a_price<?php echo $manual['id'];?>" type="text" class="read_class" style="width:40px;" readonly value="<?=number_format($manual['non_oem_price_a'],2);?>">
																								</td>

																								<td style="width:100px;" align="center">
																									<input type="text" id="manual_non_oem_b_qty<?php echo $manual['id'];?>" class="read_class" style="text-align:right;width:25px;" readonly value="<?php echo $manual['non_oem_qty_b'];?>">x $<input id="manual_non_oem_b_price<?php echo $manual['id'];?>" type="text" class="read_class" style="width:40px;" readonly value="<?=number_format($manual['non_oem_price_b'],2);?>">
																								</td>

																								<td style="width:100px;" align="center">
																									<input type="text" id="manual_non_oem_c_qty<?php echo $manual['id'];?>" class="read_class" style="text-align:right;width:25px;" readonly value="<?php echo $manual['non_oem_qty_c'];?>">x $<input id="manual_non_oem_c_price<?php echo $manual['id'];?>" type="text" class="read_class" style="width:40px;" readonly value="<?=number_format($manual['non_oem_price_c'],2);?>">
																								</td>

																								<td style="width:100px;" align="center">
																									<input type="text" id="manual_non_oem_d_qty<?php echo $manual['id'];?>" class="read_class" style="text-align:right;width:25px;" readonly value="<?php echo $manual['non_oem_qty_d'];?>">x $<input id="manual_non_oem_d_price<?php echo $manual['id'];?>" type="text" class="read_class" style="width:40px;" readonly value="<?=number_format($manual['non_oem_price_d'],2);?>">
																								</td>

																								<td style="width:100px;" align="center">
																									<input type="text" id="manual_salvage_qty<?php echo $manual['id'];?>" class="read_class" style="text-align:right;width:25px;" readonly value="<?php echo $manual['salvage_qty'];?>">x $<input type="text" id="manual_salvage_price<?php echo $manual['id'];?>" class="read_class" style="width:40px;" readonly value="<?=number_format($manual['salvage_price'],2);?>">
																								</td>
																								<td align="center">
																									<?php $m_reason=$db->func_query_first_cell("SELECT name FROM inv_buyback_shipment_reasons WHERE id='".$manual['reason']."'");?>
																									<input type="text" name="reason[<?php echo $manual['id']?>]" value="<?php echo $m_reason; ?>" />
																								</td>
																								<td align="center">
																									<a class="fancybox2 fancybox.iframe" href="<?php echo $host_path;?>buyback/move_box_item.php?id=<?php echo $manual['id'];?>&shipment_id=<?php echo $manual['shipment_id']?>&manual=1&oema=<?php echo $manual['oem_qty_a'];?>&oemb=<?php echo $manual['oem_qty_b'];?>&oemc=<?php echo $manual['oem_qty_c'];?>&oemd=<?php echo $manual['oem_qty_d'];?>&n_oema=<?php echo $manual['non_oem_qty_a'];?>&n_oemb=<?php echo $manual['non_oem_qty_b'];?>&n_oemc=<?php echo $manual['non_oem_qty_c'];?>&n_oemd=<?php echo $manual['non_oem_qty_d'];?>&sku=<?php echo $manual['sku'];?>">Transfer</a> |
																									<a id="edit_btn_<?= $manual['id']; ?>" href="javascript:void(0)" onClick="editThisManual('<?= $manual['id']; ?>')">Edit</a> | 
																								<a id="save_btn_<?= $manual['id']; ?>" style="display: none;" href="javascript:void(0);" onClick="saveThisManual('<?= $manual['id']; ?>')">Save</a>
																								</td>
																							</tr>
																							<?php
																						}
																						?>

																						<tr>
																							<th colspan="3" align="right">Total: </th>
																							<th align="center"><?=number_format($i1);?></th>
																							<th align="center"><?=number_format($i2);?></th>
																							<th align="center"><?=number_format($i3);?></th>
																							<th align="center"><?=number_format($i4);?></th>
																							<th align="center"><?=number_format($i5);?></th>
																							<th align="center"><?=number_format($i6);?></th>
																							<th align="center"><?=number_format($i7);?></th>
																							<th align="center"><?=number_format($i8);?></th>
																							<th align="center"><?=number_format($i9);?></th>
																							<th colspan="2"></th>
																							
																						</tr>


																						
																						<tr>
																							<td colspan="4" align="left">
																								<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
																							</td>

																							<td colspan="3" align="right">
																								<?php  echo $splitPage->display_links(10,$parameters); ?>
																							</td>
																						</tr>
																					</tbody>   
																				</table>

																				

																				<br><br><br><br><br>
																				<br><br><br><br><br>
																				<br><br><br><br><br>
																				<table align="center" border="1" cellspacing="0" cellpadding="5" width="50%">
																					<tr>
																						<h1>Source Of Items</h1>
																					</tr>
																					<thead>
																						<tr>
																							<th>Date & Time</th>
																							<th>Item Movement</th>
																						</tr>	
																					</thead>
																					<tbody>
																						<?php foreach ($log as $logg) { ?>
																							<tr>
																								<td><?php echo americanDate($logg['date_added']); ?></td>
																								<td><?php echo $logg['item_sku']; ?> <?php echo $logg['log']; ?><?php echo get_username($logg['user_id']); ?></td>
																							</tr>
																							<?php	}	?>
																						</tbody>
																					</table><br></br>
																				</div>
																			</div>
																			<div id="tabFinance" class="makeTabs">
																				<div align="center">
																					<table align="center" border="1" cellspacing="0" cellpadding="5" width="80%">
																						<tr>
																							<h1>Finance Table</h1>
																						</tr>
																						<thead>
																							<tr>
																								<th rowspan="2">QTY in Box</th>
																								<th rowspan="2">LBB Sku</th>
																								<th rowspan="2">Title</th>
																								<th rowspan="2">Avg Cost</th>
																								<th rowspan="2">Per Unit Shipping Cost</th>
																								<th rowspan="2">Repair Cost</th>
																								<th colspan="2">Bad</th>
																								<th colspan="2">Missing</th>
																							</tr>	
																						</thead>
																						<tbody>
																							<?php 
																							$total_qty_finance=0;
																							$total_avg_ship_refurb_cost=0;
																							$total_bad_percentage = 0;
																							$total_bad_qty = 0;
																							$total_missing_percentage = 0;
																							$total_missing_qty = 0;
																							$i=0;
																							foreach($box_data as $data) {?>
																								<?php $title=$db->func_query_first_cell("SELECT opd.name FROM oc_product op inner join oc_product_description opd on op.product_id = opd.product_id WHERE sku = '". $data['sku'] ."'");
																								$count= count($data);
																								$avg_price=($data['oemsuma']*$data['oem_a_price']+$data['oemsumb']*$data['oem_b_price']+$data['oemsumc']*$data['oem_c_price']+$data['oemsumd']*$data['oem_d_price']+$data['nonoemsuma']*$data['non_oem_a_price']+$data['nonoemsumb']*$data['non_oem_b_price']+$data['nonoemsumc']*$data['non_oem_c_price']+$data['nonoemsumd']*$data['non_oem_d_price'])/($data['oemsuma']+$data['oemsumb']+$data['oemsumc']+$data['oemsumd']+$data['nonoemsuma']+$data['nonoemsumb']+$data['nonoemsumc']+$data['nonoemsumd']);
																								
																								$bad_percent=(($data['non_working_qty'])/($data['non_working_qty']+$data['working_qty']))*100;
																								$missing=($data['oemsuma']+$data['oemsumb']+$data['oemsumc']+$data['oemsumd'])-($data['non_working_qty']+$data['working_qty']);
																								$missing_percent=($missing/($data['oemsuma']+$data['oemsumb']+$data['oemsumc']+$data['oemsumd']))*100;
																								$qty_in_box=$data['salvagesum']+$data['oemsuma']+$data['oemsumb']+$data['oemsumc']+$data['oemsumd']+$data['nonoemsuma']+$data['nonoemsumb']+$data['nonoemsumc']+$data['nonoemsumd'];
																								$per_unit_cost=$shipment_detail['shipping_cost']/$qty_in_box;
																								$total_qty_finance+=$qty_in_box;
																								$total_avg_ship_refurb_cost+=$avg_price+$data['refurb_cost'];
																								$total_bad_percentage+=$bad_percent;
																								$total_bad_qty+=$data['non_working_qty'];
																								$total_missing_percentage+=$missing_percent;
																								$total_missing_qty+=$missing;
																								$i++;

											// $lbb_sku=$db->func_query_first("SELECT * FROM inv_lbb_sku_mapping WHERE lbb_sku='".$data['sku']."'");
																								?>
																								<tr>
																									<td>
																										<?php echo $qty_in_box;?>
																									</td>
																									<td>
																										<?php echo $data['sku'];?>
																									</td>
																									<td>
																										<?php echo $data['description'];?>
																									</td>
																									<td>
																										<?php echo number_format($avg_price,2) ;?>
																									</td>
																									<td>
																										<?php echo number_format($per_unit_cost,2) ;?>
																									</td>
																									<td>
																										<?php echo number_format($data['refurb_cost'],2);?>
																									</td>
																									<td>
																										<?php echo number_format($bad_percent,2).'%';?>
																									</td>
																									<td>
																										<?php echo $data['non_working_qty'];?>
																									</td>
																									<td>
																										<?php echo number_format($missing_percent,2).'%';?>
																									</td>
																									<td>
																										<?php echo $missing;?>
																									</td>
																								</tr>
																								<?php }?>

																								<?php
																								foreach($manual_box_data as $data) {?>
																								<?php $title=$db->func_query_first_cell("SELECT opd.name FROM oc_product op inner join oc_product_description opd on op.product_id = opd.product_id WHERE sku = '". $data['sku'] ."'");
																								$count= count($data);
																								$avg_price=($data['oemsuma']*$data['oem_a_price']+$data['oemsumb']*$data['oem_b_price']+$data['oemsumc']*$data['oem_c_price']+$data['oemsumd']*$data['oem_d_price']+$data['nonoemsuma']*$data['non_oem_a_price']+$data['nonoemsumb']*$data['non_oem_b_price']+$data['nonoemsumc']*$data['non_oem_c_price']+$data['nonoemsumd']*$data['non_oem_d_price'])/($data['oemsuma']+$data['oemsumb']+$data['oemsumc']+$data['oemsumd']+$data['nonoemsuma']+$data['nonoemsumb']+$data['nonoemsumc']+$data['nonoemsumd']);
																								
																								$bad_percent=(($data['non_working_qty'])/($data['non_working_qty']+$data['working_qty']))*100;
																								$missing=($data['oemsuma']+$data['oemsumb']+$data['oemsumc']+$data['oemsumd'])-($data['non_working_qty']+$data['working_qty']);
																								$missing_percent=($missing/($data['oemsuma']+$data['oemsumb']+$data['oemsumc']+$data['oemsumd']))*100;
																								$qty_in_box=$data['salvagesum']+$data['oemsuma']+$data['oemsumb']+$data['oemsumc']+$data['oemsumd']+$data['nonoemsuma']+$data['nonoemsumb']+$data['nonoemsumc']+$data['nonoemsumd'];
																								$per_unit_cost=$shipment_detail['shipping_cost']/$qty_in_box;
																								$total_qty_finance+=$qty_in_box;
																								$total_avg_ship_refurb_cost+=$avg_price+$data['refurb_cost'];
																								$total_bad_percentage+=$bad_percent;
																								$total_bad_qty+=$data['non_working_qty'];
																								$total_missing_percentage+=$missing_percent;
																								$total_missing_qty+=$missing;
																								$i++;

											// $lbb_sku=$db->func_query_first("SELECT * FROM inv_lbb_sku_mapping WHERE lbb_sku='".$data['sku']."'");
																								?>
																								<tr>
																									<td>
																										<?php echo $qty_in_box;?>
																									</td>
																									<td>
																										<?php echo $data['sku'];?>
																									</td>
																									<td>
																										<?php echo $data['description'];?>
																									</td>
																									<td>
																										<?php echo number_format($avg_price,2) ;?>
																									</td>
																									<td>
																										<?php echo number_format($per_unit_cost,2) ;?>
																									</td>
																									<td>
																										<?php echo number_format($data['refurb_cost'],2);?>
																									</td>
																									<td>
																										<?php echo number_format($bad_percent,2).'%';?>
																									</td>
																									<td>
																										<?php echo $data['non_working_qty'];?>
																									</td>
																									<td>
																										<?php echo number_format($missing_percent,2).'%';?>
																									</td>
																									<td>
																										<?php echo $missing;?>
																									</td>
																								</tr>
																								<?php }?>



																								<tr>
																									<td colspan="3"><h3>Total Qty: <?php echo $total_qty_finance;?></h3></td>
																								
																									
																									<td colspan="3" align="center"><h3>Total Cost: <?php echo number_format(($total_avg_ship_refurb_cost+$shipment_detail['shipping_cost']+(($oem_total+$non_oem_total)*$shipment_detail['handeling_fee'])),2);?></h3></td>
																									<td  ><h3><?php echo number_format(($total_bad_percentage/$i),2).'%';?></h3></td>
																									<td ><h3><?php echo $total_bad_qty;?></h3></td>
																									<td ><h3><?php echo number_format(($total_missing_percentage/$i),2).'%';?></h3></td>
																									<td ><h3><?php echo $total_missing_qty;?></h3></td>
																								</tr>
																							</tbody>
																						</table><br></br>
																					</div>
																				</div>	
																				<?php }?>


																			</form>
																		</div>

																		<script>
																			function selectx (t) {

																				if ($(t).prop('checked') == true && !$('input[data-pr='+ $(t).val() +']').val()) {
																					$(t).parent().parent().append('<input data-pr="'+ $(t).val() +'" name="product['+ $(t).val() +'][update]" value="1" type="hidden">');
																				} else if ($(t).prop('checked') == false) {
																					$(t).parent().parent().find('input[data-pr='+ $(t).val() +']').remove();
																				}

																			}
																			function selectGetShipment() {
																				$('.shipmentBtn').attr('disabled', 'disabled');
																				$.ajax({
																					url: '<?php echo $pageViewLink; ?>',
																					type: 'POST',
																					dataType: 'json',
																					data: {'action': 'getShipment'}
																				})
																				.always(function(json) {
																					$('body').append(json['data']);
																					$('.shipmentBtn').removeAttr('disabled');
																				});
																			}

																			function selectGetBoxs() {
																				$('.shipmentBtn').attr('disabled', 'disabled');
																				$.ajax({
																					url: '<?php echo $pageViewLink; ?>',
																					type: 'POST',
																					dataType: 'json',
																					data: {'action': 'getBoxs'}
																				})
																				.always(function(json) {
																					$('body').append(json['data']);
																					$('.shipmentBtn').removeAttr('disabled');
																				});
																			}

																			function mergeBox () {

																				if (!$('#mergeBoxId').val()) {
																					alert('Please Select Box');
																					return false;
																				}

																				$('.shipmentBtn').attr('disabled', 'disabled');
																				$.ajax({
																					url: '<?php echo $pageViewLink; ?>',
																					type: 'POST',
																					dataType: 'json',
																					data: {'action':'mergeBox', 'to': $('#mergeBoxId').val(), 'id':'<?php echo $shipment_id;?>'}
																				})
																				.always(function() {
																					window.location.replace("addedit_boxes.php?shipment_id=" + $('#mergeBoxId').val());
																				});

																			}

																			function addShipment () {

																				$('.shipmentBtn').attr('disabled', 'disabled');
																				var products = [];
																				$('.select').each(function() {
																					var cBox = $(this).find('input[type=checkbox]');
																					if (cBox.is(':checked')) {
																						products.push(cBox.val());
																					}
																				});
																				$('.addToShipment').append('<input type="hidden" name="action" value="addShipment" />');
																				$('.addToShipment').append('<input type="hidden" name="shipment_id" value="'+ $('#vendor_shipment_id').val() +'" />');
																				$.ajax({
																					url: '<?php echo $pageViewLink; ?>',
																					type: 'POST',
																					dataType: 'json',
																					data: $('.addToShipment'+' :input')
																				})
																				.always(function() {
																					window.location.reload();
																				});
																			}

																			function selectlbbs() {
																				
																				$.ajax({
																					url: '',
																					type: 'POST',
																					dataType: 'json',
																					data: {getShipments: 'yes'},
																				})
																				.always(function(shipments) {


																					var html = '<table border="0" style="border-collapse:collapse;" width="90%" cellspacing="0" align="center" cellpadding="3">';
																					$('.select_lbbs').each(function(index, el) {
																						if ($(el).find('input[type="checkbox"]').prop('checked')) {

																							$.ajax({
																								url: '',
																								type: 'POST',
																								dataType: 'json',
																								data: {lbb_sku: $(el).attr('data-lbbsku'), getLbbSku: 'yes'},
																							})
																							.always(function(lbbskus) {

																								html += '<tr>';
																								html += '<td>' + $(el).attr('data-lbbsku') + '</td>';
																								html += '&nbsp';
																								html += '<td>';
																								html += '<select name="shipment_id[' + $(el).attr('data-lbbsku') + ']">';
																								html += '<option value="">Create New</option>';
																								for (var i = shipments.length - 1; i >= 0; i--) {
																									html += '<option value="'+ shipments[i]['id'] +'">'+ shipments[i]['package_number'] +'</option>';
																								}
																								html += '</select>';
																								html += '</td>';
																								html += '<td>';
																								html += '<input type="text" palceholder="Qty shipp" name="lbb_qty_ship[' + $(el).attr('data-lbbsku') + ']" id="lbb_qty_ship" />';
																								html += '</td>';


																								html += '<td>';
																								html += '<select name="lbb_sku[' + $(el).attr('data-lbbsku') + ']">';
																								html += '<option value="">Please Select</option>';
																								for (var i = lbbskus.length - 1; i >= 0; i--) {
																									html += '<option value="'+ lbbskus[i]['id'] +'">'+ lbbskus[i]['product_sku'] +'</option>';
																								}
																								html += '</select>';
																								html += '<br>';
																								html += '</td>';
																								html += '</tr>';
																							});
																						}
																					});
																					html += '</table>';
																					$(document).ajaxStop(function () {
																						if (html) {
																							$('.whitePage .form.list').html(html);
																							$('.blackPage').show();
																							html = '';
																						}
																					});
																				});

			//html += '<select required="" name="lbb_sku['+ $(el).attr('data-lbbsku') +']">';
			// var id = 0;
			// $('.select_lbbs').each(function(index, el) { 
				
			// 	if ($(el).find('input[type="checkbox"]').prop('checked')) {
			// 		id=id+1;
			
			// 	}
			
			// });
			// $(".blackPage").show();
			// //console.log(id);
			// while(id>0){
			// 	$(".form").show();
			// 	id--;
			// }
		}



	</script>
	<script type="text/javascript" src="../js/newmultiselect.js"></script>
	<script type="text/javascript">
		$(function () {
			$('#table1').multiSelect({
				actcls: 'highlightx',
				selector: 'tbody .list_items',
				except: ['form'],
				callback: function (items) {
					traverseCheckboxes('#table1', '.order_checkboxes');
				}
			});
		})
	</script>
	<script type="text/javascript">
		var orders = [];
		var customers = [];
		function allowOne (action, t) {
			var e = $(t).parent().parent().find('.order_checkboxes');
			e.prop('checked', true);
			newverifySelected(action);
		}

		function newverifySelected (action) {
			orders = [];
			customers = [];
			var error = false;
			$('.order_checkboxes').each(function(index, element) {
				if($(this).is(":checked")) {
					status = $(this).parent().parent().find('.orderReason').val();
					if (status == '' && action != 'ignore') {
						alert('Please Select Reason To process');
						error = true;
						return false;
					} else {
						orders.push({order_id:$(this).val(), reason:status});
						customers.push({email:$(this).attr('data-email'), reason:status});
					}
				}
			});
			if (error) {
				return false;
			}
			if(orders.length==0) {
				alert('You must selected atleast 1 order to process');
				return false;
			}
			updateSelected(action);
										// var confrimbox = '<div class="floatcenter">' +
										// '<div class="whitebox">' +
										// '<h2>Please Confrim</h2>' +
										// '<button type="button" onclick="updateSelected(\'customer\');">Allow Once</button>' +
										// '<button type="button" onclick="updateSelected();">White-List</button>' +
										// '<a href="javascript:void(0);"  onclick="updateSelected(\'close\');">X</a>' +
										// '</div>' +
										
										// '</div>';
										// $('body').prepend(confrimbox);
									}
									function editThis(item_id) {
										$oem_a_qty = $('#oem_a_qty' + item_id);
										$oem_a_price = $('#oem_a_price' + item_id);
										$oem_b_qty = $('#oem_b_qty' + item_id);
										$oem_b_price = $('#oem_b_price' + item_id);
										$oem_c_qty = $('#oem_c_qty' + item_id);
										$oem_c_price = $('#oem_c_price' + item_id);
										$oem_d_qty = $('#oem_d_qty' + item_id);
										$oem_d_price = $('#oem_d_price' + item_id);
										$non_oem_a_qty = $('#non_oem_a_qty' + item_id);
										$non_oem_a_price = $('#non_oem_a_price' + item_id);
										$non_oem_b_qty = $('#non_oem_b_qty' + item_id);
										$non_oem_b_price = $('#non_oem_b_price' + item_id);
										$non_oem_c_qty = $('#non_oem_c_qty' + item_id);
										$non_oem_c_price = $('#non_oem_c_price' + item_id);
										$non_oem_d_qty = $('#non_oem_d_qty' + item_id);
										$non_oem_d_price = $('#non_oem_d_price' + item_id);
										$salvage_qty = $('#salvage_qty' + item_id);
										$salvage_price = $('#salvage_price' + item_id);
										$('#save_btn_' + item_id).removeAttr('style');
										$oem_a_qty.removeClass('read_class').removeAttr('readOnly');
										$oem_a_price.removeClass('read_class').removeAttr('readOnly');
										$oem_b_qty.removeClass('read_class').removeAttr('readOnly');
										$oem_b_price.removeClass('read_class').removeAttr('readOnly');
										$oem_c_qty.removeClass('read_class').removeAttr('readOnly');
										$oem_c_price.removeClass('read_class').removeAttr('readOnly');
										$oem_d_qty.removeClass('read_class').removeAttr('readOnly');
										$oem_d_price.removeClass('read_class').removeAttr('readOnly');
										$non_oem_a_qty.removeClass('read_class').removeAttr('readOnly');
										$non_oem_a_price.removeClass('read_class').removeAttr('readOnly');
										$non_oem_b_qty.removeClass('read_class').removeAttr('readOnly');
										$non_oem_b_price.removeClass('read_class').removeAttr('readOnly');
										$non_oem_c_qty.removeClass('read_class').removeAttr('readOnly');
										$non_oem_c_price.removeClass('read_class').removeAttr('readOnly');
										$non_oem_d_qty.removeClass('read_class').removeAttr('readOnly');
										$non_oem_d_price.removeClass('read_class').removeAttr('readOnly');
										$salvage_qty.removeClass('read_class').removeAttr('readOnly');
										$salvage_price.removeClass('read_class').removeAttr('readOnly');
								}
								function editThisManual(item_id) {
										$oem_a_qty = $('#manual_oem_a_qty' + item_id);
										$oem_a_price = $('#manual_oem_a_price' + item_id);
										$oem_b_qty = $('#manual_oem_b_qty' + item_id);
										$oem_b_price = $('#manual_oem_b_price' + item_id);
										$oem_c_qty = $('#manual_oem_c_qty' + item_id);
										$oem_c_price = $('#manual_oem_c_price' + item_id);
										$oem_d_qty = $('#manual_oem_d_qty' + item_id);
										$oem_d_price = $('#manual_oem_d_price' + item_id);
										$non_oem_a_qty = $('#manual_non_oem_a_qty' + item_id);
										$non_oem_a_price = $('#manual_non_oem_a_price' + item_id);
										$non_oem_b_qty = $('#manual_non_oem_b_qty' + item_id);
										$non_oem_b_price = $('#manual_non_oem_b_price' + item_id);
										$non_oem_c_qty = $('#manual_non_oem_c_qty' + item_id);
										$non_oem_c_price = $('#manual_non_oem_c_price' + item_id);
										$non_oem_d_qty = $('#manual_non_oem_d_qty' + item_id);
										$non_oem_d_price = $('#manual_non_oem_d_price' + item_id);
										$salvage_qty = $('#manual_salvage_qty' + item_id);
										$salvage_price = $('#manual_salvage_price' + item_id);
										$('#save_btn_' + item_id).removeAttr('style');
										$oem_a_qty.removeClass('read_class').removeAttr('readOnly');
										$oem_a_price.removeClass('read_class').removeAttr('readOnly');
										$oem_b_qty.removeClass('read_class').removeAttr('readOnly');
										$oem_b_price.removeClass('read_class').removeAttr('readOnly');
										$oem_c_qty.removeClass('read_class').removeAttr('readOnly');
										$oem_c_price.removeClass('read_class').removeAttr('readOnly');
										$oem_d_qty.removeClass('read_class').removeAttr('readOnly');
										$oem_d_price.removeClass('read_class').removeAttr('readOnly');
										$non_oem_a_qty.removeClass('read_class').removeAttr('readOnly');
										$non_oem_a_price.removeClass('read_class').removeAttr('readOnly');
										$non_oem_b_qty.removeClass('read_class').removeAttr('readOnly');
										$non_oem_b_price.removeClass('read_class').removeAttr('readOnly');
										$non_oem_c_qty.removeClass('read_class').removeAttr('readOnly');
										$non_oem_c_price.removeClass('read_class').removeAttr('readOnly');
										$non_oem_d_qty.removeClass('read_class').removeAttr('readOnly');
										$non_oem_d_price.removeClass('read_class').removeAttr('readOnly');
										$salvage_qty.removeClass('read_class').removeAttr('readOnly');
										$salvage_price.removeClass('read_class').removeAttr('readOnly');
								}
								function saveThis(item_id)
								{		
										$sku = $('#sku' + item_id);
										$buyback_product_id = $('#buyback_product_id' + item_id);
										$oem_a_qty = $('#oem_a_qty' + item_id);
										$oem_a_price = $('#oem_a_price' + item_id);
										$oem_b_qty = $('#oem_b_qty' + item_id);
										$oem_b_price = $('#oem_b_price' + item_id);
										$oem_c_qty = $('#oem_c_qty' + item_id);
										$oem_c_price = $('#oem_c_price' + item_id);
										$oem_d_qty = $('#oem_d_qty' + item_id);
										$oem_d_price = $('#oem_d_price' + item_id);
										$non_oem_a_qty = $('#non_oem_a_qty' + item_id);
										$non_oem_a_price = $('#non_oem_a_price' + item_id);
										$non_oem_b_qty = $('#non_oem_b_qty' + item_id);
										$non_oem_b_price = $('#non_oem_b_price' + item_id);
										$non_oem_c_qty = $('#non_oem_c_qty' + item_id);
										$non_oem_c_price = $('#non_oem_c_price' + item_id);
										$non_oem_d_qty = $('#non_oem_d_qty' + item_id);
										$non_oem_d_price = $('#non_oem_d_price' + item_id);
										$salvage_qty = $('#salvage_qty' + item_id);
										$salvage_price = $('#salvage_price' + item_id);
									$('#save_btn_' + item_id).hide();
									$.ajax({
										url: 'addedit_boxes.php',
										type: 'post',
										data: {action: 'save_item', oem_a_qty: $oem_a_qty.val(), oem_a_price: $oem_a_price.val(), oem_b_qty: $oem_b_qty.val(), oem_b_price: $oem_b_price.val(), oem_c_qty: $oem_c_qty.val(), oem_c_price: $oem_c_price.val(), oem_d_qty: $oem_d_qty.val(), oem_d_price: $oem_d_price.val(), non_oem_a_qty: $non_oem_a_qty.val(), non_oem_a_price: $non_oem_a_price.val(), non_oem_b_qty: $non_oem_b_qty.val(), non_oem_b_price: $non_oem_b_price.val(), non_oem_c_qty: $non_oem_c_qty.val(), non_oem_c_price: $non_oem_c_price.val(), non_oem_d_qty: $non_oem_d_qty.val(), non_oem_d_price: $non_oem_d_price.val(),salvage_qty: $salvage_qty.val(),salvage_price: $salvage_price.val(), item_id: item_id, buyback_product_id: $buyback_product_id.val()},
										dataType: 'json',
										beforeSend: function () {
										},
										complete: function () {
										},
										success: function (json) {
											if (json['error']) {
												alert(json['error']);
											}
											if (json['success']) {
												$oem_a_qty.addClass('read_class').attr('readOnly');
												$oem_a_price.addClass('read_class').attr('readOnly');
												$oem_b_qty.addClass('read_class').attr('readOnly');
												$oem_b_price.addClass('read_class').attr('readOnly');
												$oem_c_qty.addClass('read_class').attr('readOnly');
												$oem_c_price.addClass('read_class').attr('readOnly');
												$oem_d_qty.addClass('read_class').attr('readOnly');
												$oem_d_price.addClass('read_class').attr('readOnly');
												$non_oem_a_qty.addClass('read_class').attr('readOnly');
												$non_oem_a_price.addClass('read_class').attr('readOnly');
												$non_oem_b_qty.addClass('read_class').attr('readOnly');
												$non_oem_b_price.addClass('read_class').attr('readOnly');
												$non_oem_c_qty.addClass('read_class').attr('readOnly');
												$non_oem_c_price.addClass('read_class').attr('readOnly');
												$non_oem_d_qty.addClass('read_class').attr('readOnly');
												$non_oem_d_price.addClass('read_class').attr('readOnly');
												$salvage_qty.addClass('read_class').attr('readOnly');
												$salvage_price.addClass('read_class').attr('readOnly');
												alert(json['success']);
											}
										}
									});
								}
								function saveThisManual(item_id)
								{		
										$oem_a_qty = $('#manual_oem_a_qty' + item_id);
										$oem_a_price = $('#manual_oem_a_price' + item_id);
										$oem_b_qty = $('#manual_oem_b_qty' + item_id);
										$oem_b_price = $('#manual_oem_b_price' + item_id);
										$oem_c_qty = $('#manual_oem_c_qty' + item_id);
										$oem_c_price = $('#manual_oem_c_price' + item_id);
										$oem_d_qty = $('#manual_oem_d_qty' + item_id);
										$oem_d_price = $('#manual_oem_d_price' + item_id);
										$non_oem_a_qty = $('#manual_non_oem_a_qty' + item_id);
										$non_oem_a_price = $('#manual_non_oem_a_price' + item_id);
										$non_oem_b_qty = $('#manual_non_oem_b_qty' + item_id);
										$non_oem_b_price = $('#manual_non_oem_b_price' + item_id);
										$non_oem_c_qty = $('#manual_non_oem_c_qty' + item_id);
										$non_oem_c_price = $('#manual_non_oem_c_price' + item_id);
										$non_oem_d_qty = $('#manual_non_oem_d_qty' + item_id);
										$non_oem_d_price = $('#manual_non_oem_d_price' + item_id);
										$salvage_qty = $('#manual_salvage_qty' + item_id);
										$salvage_price = $('#manual_salvage_price' + item_id);
									$('#save_btn_' + item_id).hide();
									$.ajax({
										url: 'addedit_boxes.php',
										type: 'post',
										data: {action: 'save_manual_item', oem_a_qty: $oem_a_qty.val(), oem_a_price: $oem_a_price.val(), oem_b_qty: $oem_b_qty.val(), oem_b_price: $oem_b_price.val(), oem_c_qty: $oem_c_qty.val(), oem_c_price: $oem_c_price.val(), oem_d_qty: $oem_d_qty.val(), oem_d_price: $oem_d_price.val(), non_oem_a_qty: $non_oem_a_qty.val(), non_oem_a_price: $non_oem_a_price.val(), non_oem_b_qty: $non_oem_b_qty.val(), non_oem_b_price: $non_oem_b_price.val(), non_oem_c_qty: $non_oem_c_qty.val(), non_oem_c_price: $non_oem_c_price.val(), non_oem_d_qty: $non_oem_d_qty.val(), non_oem_d_price: $non_oem_d_price.val(),salvage_qty: $salvage_qty.val(),salvage_price: $salvage_price.val(), item_id: item_id},
										dataType: 'json',
										beforeSend: function () {
										},
										complete: function () {
										},
										success: function (json) {
											if (json['error']) {
												alert(json['error']);
											}
											if (json['success']) {
												$oem_a_qty.addClass('read_class').attr('readOnly');
												$oem_a_price.addClass('read_class').attr('readOnly');
												$oem_b_qty.addClass('read_class').attr('readOnly');
												$oem_b_price.addClass('read_class').attr('readOnly');
												$oem_c_qty.addClass('read_class').attr('readOnly');
												$oem_c_price.addClass('read_class').attr('readOnly');
												$oem_d_qty.addClass('read_class').attr('readOnly');
												$oem_d_price.addClass('read_class').attr('readOnly');
												$non_oem_a_qty.addClass('read_class').attr('readOnly');
												$non_oem_a_price.addClass('read_class').attr('readOnly');
												$non_oem_b_qty.addClass('read_class').attr('readOnly');
												$non_oem_b_price.addClass('read_class').attr('readOnly');
												$non_oem_c_qty.addClass('read_class').attr('readOnly');
												$non_oem_c_price.addClass('read_class').attr('readOnly');
												$non_oem_d_qty.addClass('read_class').attr('readOnly');
												$non_oem_d_price.addClass('read_class').attr('readOnly');
												$salvage_qty.addClass('read_class').attr('readOnly');
												$salvage_price.addClass('read_class').attr('readOnly');
												alert(json['success']);
											}
										}
									});
								}						
								</script>
							</body>
							</html>