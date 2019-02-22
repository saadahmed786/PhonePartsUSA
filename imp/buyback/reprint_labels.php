<?php
include("../phpmailer/class.smtp.php");
include("../phpmailer/class.phpmailer.php");
require_once("../auth.php");
require_once("../inc/functions.php");


function addBBComment($buyback_id,$comment)
{
	
	global $db;
	$data = array();
//	$data['customer_id'] = $customer_id;
	$data['comment'] = $db->func_escape_string($comment);
	$data['buyback_id'] = $buyback_id;
	$data['user_id'] = $_SESSION['user_id'];
	//$data['email'] = $oldEmail;
	$data['date_added'] = date('Y-m-d H:i:s');
	$db->func_array2insert("inv_buyback_comments",$data);	

}
function sendLbbLabel($email,$data,$filename)
{

	
	sendEmailDetails ($data, $email,array(),$filename);
}
$shipment_number = $db->func_escape_string($_REQUEST['shipment']);

$printers = array(
	array('id' => QC1_PRINTER, 'value' => 'QC1'),
	array('id' => QC2_PRINTER, 'value' => 'QC2'),
	array('id' => REC_PRINTER, 'value' => 'Receiving'),
	array('id' => STOREFRONT_PRINTER, 'value' => 'Storefront')
	);

// $Barcode = new Barcode();
// $Barcode->setType('C128');
// $Barcode->setSize(60,140);
// $Barcode->hideCodeType();

if (!$shipment_number) {
	// header("Location:$host_path/buyback/shipments.php");
	echo 'Please reload the page and try again';
	exit;
	//$shipment_number='LBB00116';

}
$detail = $db->func_query_first("SELECT * FROM oc_buyback WHERE shipment_number='".$shipment_number."'");

$total_products = $db->func_query_first_cell("SELECT SUM(oem_a_qty) + SUM(oem_b_qty) + SUM(oem_c_qty) + SUM(oem_d_qty) + sum(non_oem_a_qty) + sum(non_oem_b_qty) + sum(non_oem_c_qty) + sum(non_oem_d_qty) FROM oc_buyback_products WHERE buyback_id='".$detail['buyback_id']."'");

if($_POST['action']=='void_shipment')
{
	$do_void = true;
	include_once '../shipstation/create_shipment_label.php';
	if($is_voided)
	{
		$db->db_exec("UPDATE oc_buyback SET is_voided=1 WHERE buyback_id='".$detail['buyback_id']."'");
		echo 'success';
	}
	else
	{
		echo 'failed';
	}
	exit;
}
if($_POST['action']=='reprint_email')
{
	$_email = array();
	$data = array();
	$_email['title'] = 'Shipment Label has been created!';
	$_email['number']['title'] = 'Shipment #';
	$_email['number']['value'] = $detail['shipment_number'];
	$_email['message'] = 'Dear '.urldecode($_POST['firstname']).' '.urldecode($_POST['lastname']).'!<br>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.<br><br>';
	$_email['subject'] = 'LBB Shipping Label has been created - PhonePartsUSA';
	$_email['image']= 'https://phonepartsusa.com/image/buyback_email.png';
	$data['email'] = urldecode($_POST['email']);

		// $data['email'] = 'xaman.riaz@gmail.com';
	$data['customer_name'] = urldecode($_POST['firstname']).' '.urldecode($_POST['lastname']);

	sendLbbLabel($email,$data,'../image/labels'.$detail['pdf_label']);
	echo 'success';
	exit;
}

if($_POST['action']=='create_return_shipment')
{
	
	$rejected_items = json_decode(stripslashes($_POST['rejected_items']));
	$shipping_method = $db->func_escape_string($_POST['shipping_method']);
	$shipping_cost = (float)$_POST['shipping_cost'];
	
	foreach($rejected_items as $key => $value)
	{
		
		$db->db_exec("UPDATE oc_buyback_products SET for_shipstation=1 WHERE buyback_product_id='".$value->buyback_product_id."'");	
		
	}
	
	$db->db_exec("UPDATE oc_buyback SET for_shipstation=1,shipping_method='".$shipping_method."',shipping_cost='".$shipping_cost."' WHERE shipment_number='".$shipment_number."'");
	exit;
	
}
if($detail['status']=='In QC')
{
	$detail['status'] = 'QC Completed';	
}
if(!$detail)
{
	// header("Location:$host_path/buyback/shipments.php");
	echo 'Please reload the page and try again';
	exit;
	
}
if($_SESSION['login_as']=='admin')
{
	$_SESSION['buyback_qc_shipments'] = 1;
	$_SESSION['buyback_receive_shipments'] = 1;
}

if($_SESSION['buyback_receive_shipments'] && in_array($detail['status'],array('Awaiting')))
{
	$flag_received_qty = true;	
}
else
{
	$flag_received_qty = false;
}

//print_r($detail['buyback_id']);exit;
$products = $db->func_query("SELECT * FROM oc_buyback_products WHERE buyback_id='".$detail['buyback_id']."'");
$product_total = $db->func_query_first_cell("SELECT SUM(oem_a_qty) + SUM(oem_b_qty) + SUM(oem_c_qty) + SUM(oem_d_qty) + sum(non_oem_a_qty) + sum(non_oem_b_qty) + sum(non_oem_c_qty) + sum(non_oem_d_qty) from oc_buyback_products WHERE buyback_id='".$detail['buyback_id']."' and data_type='customer'");

if($_POST['reprint'])
{
	
	
	
	if($_POST['reprint']){

  //testObject($_POST);
		foreach ($products as $product) {
			$quantities = $db->func_query_first("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$product['buyback_product_id']."'");
			

			$mapped_sku=$db->func_query_first("SELECT * FROM inv_buy_back WHERE sku='".$product['sku']."'");

			$oem_a_qty=$_POST['oem_qty_a'][$product['buyback_product_id']];
			$oem_b_qty=$_POST['oem_qty_b'][$product['buyback_product_id']];
			$oem_c_qty=$_POST['oem_qty_c'][$product['buyback_product_id']];
			$oem_d_qty=$_POST['oem_qty_d'][$product['buyback_product_id']];
			$non_oem_a_qty=$_POST['non_oem_qty_a'][$product['buyback_product_id']];
			$non_oem_b_qty=$_POST['non_oem_qty_b'][$product['buyback_product_id']];
			$non_oem_c_qty=$_POST['non_oem_qty_c'][$product['buyback_product_id']];
			$non_oem_d_qty=$_POST['non_oem_qty_d'][$product['buyback_product_id']];	
			$salvage_qty=$_POST['salvage_qty'][$product['buyback_product_id']];
			$unacceptable=$_POST['unacceptable_qty'][$product['buyback_product_id']];
			$damaged=$_POST['rejected_qty'][$product['buyback_product_id']];

			if($_POST['printerid']){
			while($oem_a_qty>0){
				printLabel('',$mapped_sku['oem_a_desc'],'','','',$_POST['printerid'],'','16','1');
				
				$oem_a_qty--;
			}
			while($oem_b_qty>0){
				printLabel('',$mapped_sku['oem_b_desc'],'','','',$_POST['printerid'],'','16','1');
				$oem_b_qty--;	
			}

			while($oem_c_qty>0){
				printLabel('',$mapped_sku['oem_c_desc'],'','','',$_POST['printerid'],'','16','1');
				$oem_c_qty--;	
			}
			while($oem_d_qty>0){
				printLabel('',$mapped_sku['oem_d_desc'],'','','',$_POST['printerid'],'','16','1');
				$oem_d_qty--;		
			}
			while($non_oem_a_qty>0){
				printLabel('',$mapped_sku['non_oem_a_desc'],'','','',$_POST['printerid'],'','16','1');
				$non_oem_a_qty--;
			}
			while($non_oem_b_qty>0){
				printLabel('',$mapped_sku['non_oem_b_desc'],'','','',$_POST['printerid'],'','16','1');
				$non_oem_b_qty--;
			}
			while($non_oem_c_qty>0){
				printLabel('',$mapped_sku['non_oem_c_desc'],'','','',$_POST['printerid'],'','16','1');
				$non_oem_c_qty--;
			}
			while($non_oem_d_qty>0){
				printLabel('',$mapped_sku['non_oem_d_desc'],'','','',$_POST['printerid'],'','16','1');
				$non_oem_d_qty--;
			}
			while($salvage_qty>0){
				printLabel('',$mapped_sku['salvage_desc'],'','','',$_POST['printerid'],'','16','1');
				$salvage_qty--;
			}
			while($unacceptable>0) {
				printLabel('',$mapped_sku['unacceptable_desc'],'','','',$_POST['printerid'],'','16','1');
				$unacceptable--;
			}
			while($damaged>0) {
				printLabel('',$mapped_sku['damaged_desc'],'','','',$_POST['printerid'],'','16','1');
				$damaged--;
			}
		}
		
			
	}
} 

	
	if ($log) {
		actionLog($log);
	}
	
	
	if($flag_received_qty)
	{
		foreach($products as $product)
		{
			$db->db_exec("UPDATE oc_buyback_products SET total_received ='".(int)$_POST['total_received'][$product['buyback_product_id']]."' WHERE buyback_product_id='".$product['buyback_product_id']."'");
			
			
		}
		if($_POST['new_received_lcd'])
		{
			foreach($_POST['new_received_lcd'] as $key => $sku)
			{
				if($sku=='-1')
				{
					$db->db_exec("INSERT INTO oc_buyback_products SET buyback_id='".$detail['buyback_id']."',sku='Other',image_path='',description='".$db->func_escape_string($_POST['other_lcd'][$key])."',total_received='".$_POST['new_total_received'][$key]."',data_type='received'");
					addBBComment($detail['buyback_id'],$_SESSION['login_as'].' added new Item "'.$db->func_escape_string($_POST['other_lcd'][$key]).'" of Qty:'.$_POST['new_total_received'][$key].' to received.');
				}
				else
				{
					$new_detail = $db->func_query_first("SELECT id,sku,image,description,oem,non_oem FROM inv_buy_back WHERE sku='".$sku."'");

					$db->db_exec("INSERT INTO oc_buyback_products SET buyback_id='".$detail['buyback_id']."',sku='".$new_detail['sku']."',image_path='".$host_path."files/".$new_detail['image']."',description='".$new_detail['description']."',total_received='".$_POST['new_total_received'][$key]."',data_type='received'");
				}
				addBBComment($detail['buyback_id'],$_SESSION['login_as'].' added new Item "'.$new_detail['description'].'" of Qty:'.$_POST['new_total_received'][$key].' to received.');
			}
			
		}
		
		$db->db_exec("UPDATE oc_buyback SET total_received='".(int)$_POST['received_total']."' WHERE shipment_number='".$shipment_number."'");
	}
	// Only when QC Done
	if($flag_change_qty)
	{
		
		if($_POST['new_qc_lcd'])
		{
			
			foreach($_POST['new_qc_lcd'] as $key => $sku)
			{
				$new_detail = $db->func_query_first("SELECT * FROM inv_buy_back WHERE sku='".$sku."'");
				
				$total_oem_a_total = (int)$_POST['new_oem_received_a'][$key] * (float)$new_detail['oem_a'];
				$total_oem_b_total = (int)$_POST['new_oem_received_b'][$key] * (float)$new_detail['oem_b'];
				$total_oem_c_total = (int)$_POST['new_oem_received_c'][$key] * (float)$new_detail['oem_c'];
				$total_oem_d_total = (int)$_POST['new_oem_received_d'][$key] * (float)$new_detail['oem_d'];
				$total_non_oem_a_total = (int)$_POST['new_non_oem_received_a'][$key] * (float)$new_detail['non_oem_a'];
				$total_non_oem_b_total = (int)$_POST['new_non_oem_received_b'][$key] * (float)$new_detail['non_oem_b'];
				$total_non_oem_c_total = (int)$_POST['new_non_oem_received_c'][$key] * (float)$new_detail['non_oem_c'];
				$total_non_oem_d_total = (int)$_POST['new_non_oem_received_d'][$key] * (float)$new_detail['non_oem_d'];
				$total_rejected_total = 0.00;
				
				
				/*$old_detail = $db->func_query_first("SELECT b.oem_price, b.non_oem_price FROM oc_buyback a,oc_buyback_products b  WHERE a.buyback_id=b.buyback_id AND b.sku='".$sku."' AND DATE(a.date_added) = '".date('Y-m-d',strtotime($detail['date_added']))."' and a.buyback_id<>'".$detail['buyback_id']."' ");
			if($old_detail)
			{
				$new_detail['oem'] = $old_detail['oem_price'];
				$new_detail['non_oem'] = $old_detail['oem_price']; 	
				
			}*/
			$xarray = array();
			$xarray['buyback_id'] = $detail['buyback_id'];
			$xarray['sku'] = $new_detail['sku'];
			if($new_detail['image'])
			{

				
				$xarray['image_path'] = $host_path."files/".$new_detail['image'];
			}
			$xarray['description'] = $new_detail['description'];
			$xarray['oem_a_price'] = $new_detail['oem_a'];
			$xarray['oem_b_price'] = $new_detail['oem_b'];
			$xarray['oem_c_price'] = $new_detail['oem_c'];
			$xarray['oem_d_price'] = $new_detail['oem_d'];
			$xarray['non_oem_a_price'] = $new_detail['non_oem_a'];
			$xarray['non_oem_b_price'] = $new_detail['non_oem_b'];
			$xarray['non_oem_c_price'] = $new_detail['non_oem_c'];
			$xarray['non_oem_d_price'] = $new_detail['non_oem_d'];
			$xarray['oem_a_qty'] = (int)$_POST['new_oem_a_received'][$key];
			$xarray['oem_b_qty'] = (int)$_POST['new_oem_b_received'][$key];
			$xarray['oem_c_qty'] = (int)$_POST['new_oem_c_received'][$key];
			$xarray['oem_d_qty'] = (int)$_POST['new_oem_d_received'][$key];
			$xarray['non_oem_a_qty'] = (int)$_POST['new_non_oem_a_received'][$key];
			$xarray['non_oem_b_qty'] = (int)$_POST['new_non_oem_b_received'][$key];
			$xarray['non_oem_c_qty'] = (int)$_POST['new_non_oem_c_received'][$key];
			$xarray['non_oem_d_qty'] = (int)$_POST['new_non_oem_d_received'][$key];
			$xarray['total_qc_received'] = (int)$_POST['new_total_qc_received'][$key];
			$xarray['data_type'] = 'qc';
			$xarray['total_oem_a_total'] = (float)$total_oem_a_total;
			$xarray['total_oem_b_total'] = (float)$total_oem_b_total;
			$xarray['total_oem_c_total'] = (float)$total_oem_c_total;
			$xarray['total_oem_d_total'] = (float)$total_oem_d_total;
			$xarray['total_non_oem_a_total'] = (float)$total_non_oem_a_total;
			$xarray['total_non_oem_b_total'] = (float)$total_non_oem_b_total;
			$xarray['total_non_oem_c_total'] = (float)$total_non_oem_c_total;
			$xarray['total_non_oem_d_total'] = (float)$total_non_oem_dotal;
			$xarray['total_rejected_total'] = (float)$total_rejected_total;

			$_id = $db->func_array2insert('oc_buyback_products',$xarray);

			/*$db->db_exec("INSERT INTO oc_buyback_products SET buyback_id='".$detail['buyback_id']."',sku='".$new_detail['sku']."',image_path='".$host_path."files/".$new_detail['image']."',description='".$new_detail['description']."',oem_price='".$new_detail['oem']."',non_oem_price='".$new_detail['non_oem']."',oem_quantity='".(int)$_POST['new_oem_received'][$key]."',non_oem_quantity='".(int)$_POST['new_non_oem_received'][$key]."',total_qc_received='".(int)$_POST['new_total_qc_received'][$key]."',data_type='qc',total_oem_total='".(float)$total_oem_total."',total_non_oem_total='".(float)$total_non_oem_total."',total_rejected_total='".(float)$total_rejected_total."'");*/


			addBBComment($detail['buyback_id'],$_SESSION['login_as'].' added new Item "'.$new_detail['description'].'" of Qty:'.$_POST['new_oem_received'][$key].' to qc.');

			$_POST['oem_qty_a'][$_id] = (int)$_POST['new_oem_qty_a'][$key];
			$_POST['oem_qty_b'][$_id] = (int)$_POST['new_oem_qty_b'][$key];
			$_POST['oem_qty_c'][$_id] = (int)$_POST['new_oem_qty_c'][$key];
			$_POST['oem_qty_d'][$_id] = (int)$_POST['new_oem_qty_d'][$key];
			$_POST['non_oem_qty_a'][$_id] = (int)$_POST['new_non_oem_qty_a'][$key];
			$_POST['non_oem_qty_b'][$_id] = (int)$_POST['new_non_oem_qty_b'][$key];
			$_POST['non_oem_qty_c'][$_id] = (int)$_POST['new_non_oem_qty_c'][$key];
			$_POST['non_oem_qty_d'][$_id] = (int)$_POST['new_non_oem_qty_d'][$key];
			$_POST['salvage_qty'][$_id] = (int)$_POST['new_salvage_qty'][$key] ;
			$_POST['unacceptable_qty'][$_id] = (int)$_POST['new_unacceptable_qty'][$key] ;
			$_POST['rejected_qty'][$_id] = (int)$_POST['new_rejected_qty'][$key] ;
			$_POST['total_qc_received'][$_id] = $_POST['new_total_qc_received'][$key];
		}

	}

	$products = $db->func_query("SELECT * FROM oc_buyback_products WHERE buyback_id='".$detail['buyback_id']."'");

	foreach($products as $product)
	{
		$db->db_exec("UPDATE oc_buyback_products SET admin_oem_a_qty='0', admin_oem_b_qty='0', admin_oem_c_qty='0', admin_oem_d_qty='0', admin_non_oem_a_qty='0', admin_non_oem_b_qty='0', admin_non_oem_c_qty='0', admin_non_oem_d_qty='0', admin_salvage_qty='0', admin_unacceptable='0', admin_rejected='0', admin_updated=1 WHERE buyback_product_id='".$product['buyback_product_id']."' ");

		$db->db_exec("DELETE FROM inv_buyback_shipments WHERE buyback_product_id='".$product['buyback_product_id']."'");

		$data = array();
		$data['oem_qty_a'] = (int)$_POST['oem_qty_a'][$product['buyback_product_id']];
		$data['oem_qty_b'] = (int)$_POST['oem_qty_b'][$product['buyback_product_id']];
		$data['oem_qty_c'] = (int)$_POST['oem_qty_c'][$product['buyback_product_id']];
		$data['oem_qty_d'] = (int)$_POST['oem_qty_d'][$product['buyback_product_id']];

		$data['non_oem_qty_a'] = (int)$_POST['non_oem_qty_a'][$product['buyback_product_id']];
		$data['non_oem_qty_b'] = (int)$_POST['non_oem_qty_b'][$product['buyback_product_id']];
		$data['non_oem_qty_c'] = (int)$_POST['non_oem_qty_c'][$product['buyback_product_id']];
		$data['non_oem_qty_d'] = (int)$_POST['non_oem_qty_d'][$product['buyback_product_id']];

		$data['salvage_qty'] = (int)$_POST['salvage_qty'][$product['buyback_product_id']];
		$data['unacceptable_qty'] = (int)$_POST['unacceptable_qty'][$product['buyback_product_id']];
		$data['rejected_qty'] = (int)$_POST['rejected_qty'][$product['buyback_product_id']];
		
		$data['buyback_product_id'] = $product['buyback_product_id'];

		$data['date_added'] = date('Y-m-d H:i:s');
		$db->func_array2insert('inv_buyback_shipments',$data);

		$db->db_exec("UPDATE oc_buyback_products SET total_qc_received = '".(int)$_POST['total_qc_received'][$product['buyback_product_id']]."' WHERE buyback_product_id='".$product['buyback_product_id']."'");

				// Shipment Box Preparation


		$last_id = $db->func_query_first_cell ( "select id from inv_buyback_boxes where status = 'Pending'" );
		if (!$last_id) {
			$rejcetedShipment = array();
			$rejcetedShipment ['package_number'] = 'LBB-' . date('Ymdhis');
			$rejcetedShipment ['status'] = 'Pending';
			$rejcetedShipment ['date_added'] = date ( 'Y-m-d H:i:s' );
			$rejcetedShipment ['user_id'] = $_SESSION ['user_id'];
			$last_id = $db->func_array2insert ( 'inv_buyback_boxes', $rejcetedShipment );
		}	

		$db->db_exec ( "delete from inv_buyback_box_items where buyback_product_id='".$product['buyback_product_id']."' and shipment_id = '".$last_id."'" );



		if($data['oem_qty_a']>0 || $data['oem_qty_b']>0 || $data['oem_qty_c']>0 || $data['oem_qty_d']>0 || $data['non_oem_qty_a']>0 || $data['non_oem_qty_b']>0 || $data['non_oem_qty_c']>0 || $data['non_oem_qty_d']>0 || $data['salvage_qty']>0)
		{
			$ShipmentItems = array();
			$ShipmentItems ['shipment_id'] = $last_id;
			$ShipmentItems ['oem_received_a'] = $data['oem_qty_a'];
			$ShipmentItems ['oem_received_b'] = $data['oem_qty_b'];
			$ShipmentItems ['oem_received_c'] = $data['oem_qty_c'];
			$ShipmentItems ['oem_received_d'] = $data['oem_qty_d'];
			$ShipmentItems ['non_oem_received_a'] = $data['non_oem_qty_a'];
			$ShipmentItems ['non_oem_received_b'] = $data['non_oem_qty_b'];
			$ShipmentItems ['non_oem_received_c'] = $data['non_oem_qty_c'];
			$ShipmentItems ['non_oem_received_d'] = $data['non_oem_qty_d'];
			$ShipmentItems ['salvage_received'] = $data['salvage_qty'];
			$ShipmentItems ['buyback_product_id'] = $data['buyback_product_id'];
			$lbbBoxId = $db->func_array2insert ( 'inv_buyback_box_items', $ShipmentItems);

		}
		// Log Maintenance of Transfer in Box
		$from = $db->func_escape_string($_REQUEST['shipment']);
		$to = $db->func_query_first_cell( "select package_number from inv_buyback_boxes where id = '".$last_id."'" );
		$_link1 = '<a href="'.$host_path.'buyback/shipment_detail.php?shipment='.$from.'">'.$from.'</a>';
		$_link2 = '<a href="'.$host_path.'buyback/addedit_boxes.php?shipment_id='.$last_id.'">'.$to.'</a>';
		logLbbItem($sku, 'Moved to '.$_link2.' from LBB Shipment # '. $_link1 . ' by ', $from, $to);	

		$db->db_exec("UPDATE oc_buyback_products SET total_oem_a_total='".(float)$_POST['total_oem_a_total'][$product['buyback_product_id']]."', total_oem_b_total='".(float)$_POST['total_oem_b_total'][$product['buyback_product_id']]."', total_oem_c_total='".(float)$_POST['total_oem_c_total'][$product['buyback_product_id']]."', total_oem_d_total='".(float)$_POST['total_oem_d_total'][$product['buyback_product_id']]."', total_non_oem_a_total='".(float)$_POST['total_non_oem_a_total'][$product['buyback_product_id']]."', total_non_oem_b_total='".(float)$_POST['total_non_oem_b_total'][$product['buyback_product_id']]."', total_non_oem_c_total='".(float)$_POST['total_non_oem_c_total'][$product['buyback_product_id']]."', total_non_oem_d_total='".(float)$_POST['total_non_oem_d_total'][$product['buyback_product_id']]."', total_rejected_total='".(float)$_POST['total_rejected_total'][$product['buyback_product_id']]."' WHERE buyback_product_id='".$product['buyback_product_id']."'");

				// Shipment Box Preparation Ends


	}
}

if(isset($_POST['admin_oem_a_qty']) or isset($_POST['admin_oem_b_qty']) or isset($_POST['admin_oem_c_qty']) or isset($_POST['admin_oem_c_qty']) or isset($_POST['admin_non_oem_a_qty']) or isset($_POST['admin_non_oem_b_qty']) or isset($_POST['admin_non_oem_c_qty']) or isset($_POST['admin_non_oem_d_qty']) or isset($_POST['admin_rejected']))
{

	foreach($_POST['admin_oem_a_qty'] as $key => $value)
	{
		$db->db_exec("UPDATE oc_buyback_products SET admin_oem_a_qty='".(int)$_POST['admin_oem_a_qty'][$key]."', admin_oem_b_qty='".(int)$_POST['admin_oem_b_qty'][$key]."', admin_oem_c_qty='".(int)$_POST['admin_oem_c_qty'][$key]."', admin_oem_d_qty='".(int)$_POST['admin_oem_d_qty'][$key]."', admin_non_oem_a_qty='".(int)$_POST['admin_non_oem_a_qty'][$key]."', admin_non_oem_b_qty='".(int)$_POST['admin_non_oem_b_qty'][$key]."', admin_non_oem_c_qty='".(int)$_POST['admin_non_oem_c_qty'][$key]."', admin_non_oem_d_qty='".(int)$_POST['admin_non_oem_d_qty'][$key]."', admin_salvage_qty='".(int)$_POST['admin_salvage_qty'][$key]."', admin_unacceptable='".(int)$_POST['admin_unacceptable'][$key]."', admin_rejected='".(int)$_POST['admin_rejected'][$key]."', admin_updated=1 WHERE buyback_product_id='".$key."' ");
	}


}
			//print_r($_POST['total_rejected_total']);exit;
if($_POST['total_rejected_total'])
{

	foreach($_POST['total_rejected_total'] as $buyback_product_id => $value)
	{

		$db->db_exec("UPDATE oc_buyback_products SET total_rejected_total='".(float)$value."' WHERE buyback_product_id='".$buyback_product_id."'");

	}

}



$_SESSION['message'] = 'Modification Made!';
// header("Location:shipment_detail.php?shipment=$shipment_number");
echo "<script>parent.location.reload(true);</script>";
exit;
}
if($_POST['addcomment'])
{
	
	addBBComment($detail['buyback_id'],$_POST['comment']);
	
	$_SESSION['message'] = 'Comment has been added';
	// header("Location:shipment_detail.php?shipment=$shipment_number");
	echo "<script>parent.location.reload(true);</script>";
	exit;
}






	

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Shipment Details</title>
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />


	

<style>
	.light-grey{
		background-color:#CCC;	
	}
</style>
</head>
<body>
			<form method="post" action="" enctype="multipart/form-data">
			<table border="1" id="qc_table" cellpadding="10" cellspacing="0" width="70%">
												<tr style="background-color:#999;font-weight:bold;font-size:10px">
													<th rowspan="2">LCD Type</th>
													<th colspan="4">OEM</th>
													<th colspan="4">Non-OEM</th>
													<th rowspan="2">Salvage</th>
													<th rowspan="2">Unacceptable</th>
													<th rowspan="2">Damaged</th>
													<th rowspan="2">Total</th>
													<th rowspan="2">FB Added</th>

												</tr>
												<tr style="background-color:#999;font-weight:bold;font-size:10px">
													<th>A</th>
													<th>A-</th>
													<th>B</th>
													<th>C</th>
													<th>A</th>
													<th>A-</th>
													<th>B</th>
													<th>C</th>
												</tr>
												<?php
												$qc_quantity_total = 0;
												$qc_oem_a_total = 0;
												$qc_oem_b_total = 0;
												$qc_oem_c_total = 0;
												$qc_oem_d_total = 0;
												$qc_non_oem_a_total = 0;
												$qc_non_oem_b_total = 0;
												$qc_non_oem_c_total = 0;
												$qc_non_oem_d_total = 0;
												$qc_salvage = 0;
												$qc_unacceptable = 0;
												$qc_rejected = 0;
												$qc_rejected_total = 0;
												$rejected_items = array();
												foreach($products as $product)
												{
													if($product['data_type']!='customer' and $product['data_type']!='qc') continue;

													$quantities = $db->func_query_first("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$product['buyback_product_id']."'");
													$fb_added = 0;
													if($quantities)
													{
														$oem_a_qty = (int)$quantities['oem_qty_a'];
														$oem_b_qty = (int)$quantities['oem_qty_b'];
														$oem_c_qty = (int)$quantities['oem_qty_c'];
														$oem_d_qty = (int)$quantities['oem_qty_d'];
														$non_oem_a_qty = (int)$quantities['non_oem_qty_a'];
														$non_oem_b_qty = (int)$quantities['non_oem_qty_b'];
														$non_oem_c_qty = (int)$quantities['non_oem_qty_c'];
														$non_oem_d_qty = (int)$quantities['non_oem_qty_d'];
														$salvage_qty = (int)$quantities['salvage_qty'];
														$unacceptable_qty = (int)$quantities['unacceptable_qty'];
														$rejected_qty = (int)$quantities['rejected_qty'];
														$fb_added = (int)$quantities['fb_added'];
													}
													if($rejected_qty>0 || $unacceptable_qty>0)
													{
															// to detect the shipment has a rejected qty
														$rejected_items[] = array(
															'buyback_product_id'=>$product['buyback_product_id'],
															'qty'=>$rejected_qty + $unacceptable_qty,
															);
													}
													$qc_quantity_total+=$product['total_qc_received'];
													$qc_oem_a_total+=$oem_a_qty;
													$qc_oem_b_total+=$oem_b_qty;
													$qc_oem_c_total+=$oem_c_qty;
													$qc_oem_d_total+=$oem_d_qty;
													$qc_non_oem_a_total+=$non_oem_a_qty;
													$qc_non_oem_b_total+=$non_oem_b_qty;
													$qc_non_oem_c_total+=$non_oem_c_qty;
													$qc_non_oem_d_total+=$non_oem_d_qty;
													$qc_salvage+=(int)$salvage_qty;	
													$qc_unacceptable+=(int)$unacceptable_qty;	
													$qc_rejected+=(int)$rejected_qty;	
													$qc_rejected_total+=$rejected_qty;	
													?>

													<tr >
														<td><?=$product['description'];?></td>

														<td align="center">
															
																<input type="text" id="oem_a_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$oem_a_qty;?>" name="oem_qty_a[<?php echo $product['buyback_product_id'];?>]" style="width:50px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')" />
																<input type="hidden" id="oem_a_price_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['oem_a_price'];?>" />
																<input type="hidden" name="total_oem_a_total[<?php echo $product['buyback_product_id'];?>]" id="total_oem_a_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo (float)$product['total_oem_a_total'];?>"  />
																
														</td>
														<td align="center">
															
																<input type="text" id="oem_b_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$oem_b_qty;?>" name="oem_qty_b[<?php echo $product['buyback_product_id'];?>]" style="width:50px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')" />
																<input type="hidden" id="oem_b_price_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['oem_b_price'];?>" />
																<input type="hidden" name="total_oem_b_total[<?php echo $product['buyback_product_id'];?>]" id="total_oem_b_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo (float)$product['total_oem_b_total'];?>"  />
																
														</td>
														<td align="center">
															
																<input type="text" id="oem_c_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$oem_c_qty;?>" name="oem_qty_c[<?php echo $product['buyback_product_id'];?>]" style="width:50px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')" />
																<input type="hidden" id="oem_c_price_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['oem_c_price'];?>" />
																<input type="hidden" name="total_oem_c_total[<?php echo $product['buyback_product_id'];?>]" id="total_oem_c_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo (float)$product['total_oem_c_total'];?>"  />
																
														</td>
														<td align="center">
															
																<input type="text" id="oem_d_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$oem_d_qty;?>" name="oem_qty_d[<?php echo $product['buyback_product_id'];?>]" style="width:50px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')" />
																<input type="hidden" id="oem_d_price_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['oem_d_price'];?>" />
																<input type="hidden" name="total_oem_d_total[<?php echo $product['buyback_product_id'];?>]" id="total_oem_d_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo (float)$product['total_oem_d_total'];?>"  />
																
														</td>
														<td align="center">
																<input type="text" id="non_oem_a_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$non_oem_a_qty;?>" name="non_oem_qty_a[<?php echo $product['buyback_product_id'];?>]" style="width:50px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')" />
																<input type="hidden" id="non_oem_a_price_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['non_oem_a_price'];?>" />
																<input type="hidden" name="total_non_oem_a_total[<?php echo $product['buyback_product_id'];?>]" id="total_non_oem_a_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo (float)$product['total_non_oem_a_total'];?>"  />
																
														</td>
														<td align="center">
																<input type="text" id="non_oem_b_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$non_oem_b_qty;?>" name="non_oem_qty_b[<?php echo $product['buyback_product_id'];?>]" style="width:50px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')" />
																<input type="hidden" id="non_oem_b_price_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['non_oem_b_price'];?>" />
																<input type="hidden" name="total_non_oem_b_total[<?php echo $product['buyback_product_id'];?>]" id="total_non_oem_b_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo (float)$product['total_non_oem_b_total'];?>"  />
																
														</td>
														<td align="center">
																<input type="text" id="non_oem_c_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$non_oem_c_qty;?>" name="non_oem_qty_c[<?php echo $product['buyback_product_id'];?>]" style="width:50px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')" />
																<input type="hidden" id="non_oem_c_price_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['non_oem_c_price'];?>" />
																<input type="hidden" name="total_non_oem_c_total[<?php echo $product['buyback_product_id'];?>]" id="total_non_oem_c_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo (float)$product['total_non_oem_c_total'];?>"  />
																
														</td>
														<td align="center">
																<input type="text" id="non_oem_d_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$non_oem_d_qty;?>" name="non_oem_qty_d[<?php echo $product['buyback_product_id'];?>]" style="width:50px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')" />
																<input type="hidden" id="non_oem_d_price_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['non_oem_d_price'];?>" />
																<input type="hidden" name="total_non_oem_d_total[<?php echo $product['buyback_product_id'];?>]" id="total_non_oem_d_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo (float)$product['total_non_oem_d_total'];?>"  />
																
														</td>

														<td align="center">
															
																<input type="text" id="salvage_qty_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$salvage_qty;?>" name="salvage_qty[<?php echo $product['buyback_product_id'];?>]" style="width:40px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')"  />
																
														</td>

														<td align="center">
															
																<input type="text" id="unacceptable_qty_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$unacceptable_qty;?>" name="unacceptable_qty[<?php echo $product['buyback_product_id'];?>]" style="width:40px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')"  />
																
														</td>

														<td align="center">
															
																<input type="text" id="rejected_qty_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$rejected_qty;?>" name="rejected_qty[<?php echo $product['buyback_product_id'];?>]" style="width:40px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')"  />
																
														</td>

														<td align="right" class="light-grey">
																<input type="text" data-received="<?php echo $product['total_received'];?>" id="total_qc_received_<?php echo $product['buyback_product_id'];?>" value="<?=(int)$product['total_qc_received'];?>" name="total_qc_received[<?php echo $product['buyback_product_id'];?>]" style="width:50px" />
																<span class="error"></span>
																</td>
															<td align="center"><img src="<?=$host_path;?>images/<?=($fb_added?'check.png':'cross.png');?>"></td>
														</tr>

														<?php
													}
													?>
													<tr>
														<td>	</td>
														<td align="center">
															<?=$qc_oem_a_total;?>
														</td>
														<td align="center">
															<?=$qc_oem_b_total;?>
														</td>
														<td align="center">
															<?=$qc_oem_c_total;?>
														</td>
														<td align="center">
															<?=$qc_oem_d_total;?>
														</td>
														<td align="center">
															<?=$qc_non_oem_a_total;?>
														</td>
														<td align="center">
															<?=$qc_non_oem_b_total;?>
														</td>
														<td align="center">
															<?=$qc_non_oem_c_total;?>
														</td>
														<td align="center">
															<?=$qc_non_oem_d_total;?>
														</td>
														<td align="center">
															<?=$qc_salvage;?>
														</td>
														<td align="center">
															<?=$qc_unacceptable;?>
														</td>
														<td align="center">
															<?=$qc_rejected;?>
														</td>

														<td align="right"  class="light-grey"><strong><?php echo $qc_quantity_total;?></strong></td>
														<td align="center">-</td>
													</tr>


												</table>

												<br><br><center>
												Printer:
											<select name="printerid" id="printerid">
												<option value="">Do not print</option>
												<?php foreach ($printers as $printer): ?>
													<option value="<?php echo $printer['id'] ?>" <?php if ($printer['id'] == $return_item['printer']): ?> selected="selected" <?php endif; ?>>
														<?php echo $printer['value'] ?>
													</option>
											<?php endforeach; ?>
											</select>

											<input type="submit" name="reprint" id="reprint" value="Reprint Labels" class="button" />
											</center>
											</form>

							</body>
							<script>
								var error = '';
								function rePrintLabel()
								{
									if(!confirm('Are you sure want to Re-Print / Re-Email the Customer?'))
									{
										return false;
									}
									$.ajax({
										url: "shipment_detail.php",
										type: "POST",
										data: {action:'reprint_email',shipment:'<?=$shipment_number;?>',firstname:encodeURIComponent('<?=$firstname;?>'),lastname:encodeURIComponent('<?=$lastname;?>'),email:encodeURIComponent('<?=$email;?>')},
										success: function (data) {
											if(data=='success')
											{
												alert('Email Sent Successfully!');
												location.reload(true);

											}
											else
											{
												alert('Error: Label not sent, please try again or contact administrator');
											// location.reload(true);	
										}

									}
								});	
								}
								<?php
								if($total_products>=25 && ($_SESSION['approve_return_shipp']==1 || $_SESSION['approve_send_label']==1) )
								{

									?>
									function voidShippingLabel()
									{

										if(!confirm('Are you sure want to void this Shipment?'))
										{
											return false;
										}
										$.ajax({
											url: "shipment_detail.php",
											type: "POST",
											data: {action:'void_shipment',shipment:'<?=$shipment_number;?>'},
											success: function (data) {
												if(data=='success')
												{
													alert('Shipment Voided');
													location.reload(true);

												}
												else
												{
													alert('Shipment Not voided, please try again');
											// location.reload(true);	
										}

									}
								});

									}
									<?php
								}
								?>
								function createReturnShipment()
								{

									$.ajax({
										url: "shipment_detail.php",
										type: "POST",
										data: {action:'create_return_shipment',shipment:'<?=$shipment_number;?>',rejected_items:'<?=json_encode($rejected_items);?>',shipping_method:$('#shipping_method').val(),shipping_cost:$('#shipping_cost').val()},
										success: function (data) {

											alert('Saved!');
											location.reload(true);

										}
									});

								}
								function changeAdminQty(product_id,t)
								{
									
									var $oem_a_qty = $('#admin_oem_a_qty_'+product_id);
									var $oem_b_qty = $('#admin_oem_b_qty_'+product_id);
									var $oem_c_qty = $('#admin_oem_c_qty_'+product_id);
									var $oem_d_qty = $('#admin_oem_d_qty_'+product_id);
									var $non_oem_a_qty = $('#admin_non_oem_a_qty_'+product_id);	
									var $non_oem_b_qty = $('#admin_non_oem_b_qty_'+product_id);	
									var $non_oem_c_qty = $('#admin_non_oem_c_qty_'+product_id);	
									var $non_oem_d_qty = $('#admin_non_oem_d_qty_'+product_id);	
									var $salvage_qty = $('#admin_salvage_qty_'+product_id);	
									var rowdata = $(t).parents('tr');

									var oem_a_price = rowdata.data('oem-a-price');
									var oem_b_price = rowdata.data('oem-b-price');
									var oem_c_price = rowdata.data('oem-c-price');
									var oem_d_price = rowdata.data('oem-d-price');
									var non_oem_a_price = rowdata.data('non-oem-a-price');	
									var non_oem_b_price = rowdata.data('non-oem-b-price');	
									var non_oem_c_price = rowdata.data('non-oem-c-price');	
									var non_oem_d_price = rowdata.data('non-oem-d-price');	
									var salvage_price = rowdata.data('salvage-price');	

									var oem_a_total = parseInt($oem_a_qty.val()) * parseFloat(oem_a_price);
									var oem_b_total = parseInt($oem_b_qty.val()) * parseFloat(oem_b_price);
									var oem_c_total = parseInt($oem_c_qty.val()) * parseFloat(oem_c_price);
									var oem_d_total = parseInt($oem_d_qty.val()) * parseFloat(oem_d_price);

									var oem_total = oem_a_total + oem_b_total + oem_c_total + oem_d_total;

									var non_oem_a_total = parseInt($non_oem_a_qty.val()) * parseFloat(non_oem_a_price);
									var non_oem_b_total = parseInt($non_oem_b_qty.val()) * parseFloat(non_oem_b_price);
									var non_oem_c_total = parseInt($non_oem_c_qty.val()) * parseFloat(non_oem_c_price);
									var non_oem_d_total = parseInt($non_oem_d_qty.val()) * parseFloat(non_oem_d_price);
									var non_oem_total = non_oem_a_total + non_oem_b_total + non_oem_c_total + non_oem_d_total;
									
									var salvage_total = parseInt($salvage_qty.val()) * parseFloat(salvage_price);

									var total = oem_total + non_oem_total + salvage_total;

									$('#admin_total_total_'+product_id).val(total.toFixed(2));
									var total_qty = 0;
									$('.admin_qty' + product_id).each(function(index, element) {
										total_qty += parseInt(element.value);
									});
									if (total_qty != rowdata.data('total-qty')) {
										$('.error' + product_id).text('QC Qty Don\'t match');
										if (!error.includes(',' + product_id)) {
											error += ','+ product_id;
										}
									} else {
										$('.error' + product_id).text('');
										error = error.replace(',' + product_id, '');
									}
								}
								function updateRejectedQty(product_id,total_received)
								{
									$oem_a = $('#oem_a_received_'+product_id);
									$oem_b = $('#oem_b_received_'+product_id);
									$oem_c = $('#oem_c_received_'+product_id);
									$oem_d = $('#oem_d_received_'+product_id);

									var total_oem = parseInt($oem_a.val()) + parseInt($oem_b.val()) + parseInt($oem_c.val()) + parseInt($oem_d.val());

									$non_oem_a = $('#non_oem_a_received_'+product_id);	
									$non_oem_b = $('#non_oem_b_received_'+product_id);	
									$non_oem_c = $('#non_oem_c_received_'+product_id);	
									$non_oem_d = $('#non_oem_d_received_'+product_id);	

									var total_non_oem = parseInt($non_oem_a.val()) + parseInt($non_oem_b.val()) + parseInt($non_oem_c.val()) + parseInt($non_oem_d.val());

									$salvage = $('#salvage_qty_'+product_id);
									$unacceptable = $('#unacceptable_qty_'+product_id);
									$rejected = $('#rejected_qty_'+product_id);

	/*if(parseInt($oem.val()) > parseInt(oem_qty))
	{
		
		alert('You are placing higher quantity than original');
		$oem.focus();
		return false;
		
	}
	if(parseInt($non_oem.val()) > parseInt(non_oem_qty))
	{
		alert('You are placing higher quantity than original');
		$non_oem.focus();
		return false;
		
	}*/
	
	// var rejected = (parseInt(total_received)) - ( total_oem + total_non_oem );
	var total_qty = total_oem + total_non_oem + parseInt($rejected.val()) + parseInt($salvage.val()) + parseInt($unacceptable.val());
	$('#total_qc_received_'+product_id).val(total_qty);
	if (total_qty != $('#total_qc_received_'+product_id).data('received')) {
		$('#total_qc_received_'+product_id).parent().find('.error').text('Received Qty Don\'t match');
		if (!error.includes(',' + product_id)) {
			error += ','+ product_id;
		}
	} else {
		$('#total_qc_received_'+product_id).parent().find('.error').text('');
			error = error.replace(',' + product_id, '');
	}
	console.log(error);
	//$('#rejected_qty_'+product_id).val(parseInt(rejected));
	updatePricing(product_id);
	



}
$('.admin_qty').keyup(function() {
	allowNum(this);
});
function updatePricing(product_id)
{
	$oem_qty = $('#oem_received_'+product_id);
	$non_oem_qty = $('#non_oem_received_'+product_id);
	$rejected_qty = $('#rejected_qty_'+product_id);

	$oem_price = $('#oem_price_'+product_id);
	$non_oem_price = $('#non_oem_price_'+product_id);
//alert($oem_price.val());
oem_val = parseInt($oem_qty.val()) * parseFloat($oem_price.val());
non_oem_val = parseInt($non_oem_qty.val()) * parseFloat($non_oem_price.val()); 	

$('#total_oem_total_'+product_id).val(oem_val.toFixed(2));
$('#total_non_oem_total_'+product_id).val(non_oem_val.toFixed(2));
}
$(document).on('change','.total_received',function(event) {


	var total = 0;
	$('.total_received').each(function(index, element) {
		total = total + parseInt($(this).val());
	});

	$('input[name=received_total]').val(total);
});

$(document).on('change','.admin_qty',function(event) {


	var total = 0;
	$('.admin_qty').each(function(index, element) {
		total = total + ( parseInt( $(this).val() ) * parseFloat( $(this).attr('data-price') ) ); 
	});

	$('#admin_combine_total').val(total.toFixed(2));
});

$(document).on('change','.admin_rejected_total',function(event) {


	var total = 0;
	$('.admin_rejected_total').each(function(index, element) {
		total = total + parseFloat($(this).val());
	});

	$('#admin_rejected_total').val(total.toFixed(2));
});

</script>

</html>