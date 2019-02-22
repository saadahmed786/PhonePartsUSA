<?php
include("../phpmailer/class.smtp.php");
include("../phpmailer/class.phpmailer.php");
include_once '../inc/Barcode.php';
require_once("../auth.php");
require_once("../inc/functions.php");
// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

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
$carriers = array(
	array('id'=>'USPS','value'=>'USPS'),
	array('id'=>'In House','value'=>'In House'),
	array('id'=>'UPS','value'=>'UPS'),
	array('id'=>'FedEx','value'=>'FedEx'),
	array('id'=>'DHL Express','value'=>'DHL Express'),
	array('id'=>'EMS','value'=>'EMS'),
	array('id'=>'HK Post','value'=>'HK Post'),
	array('id'=>'TNT','value'=>'TNT')
	);
// $Barcode = new Barcode();
// $Barcode->setType('C128');
// $Barcode->setSize(60,140);
// $Barcode->hideCodeType();

if (!$shipment_number) {
	header("Location:$host_path/buyback/shipments.php");
	exit;
}
$detail = $db->func_query_first("SELECT * FROM oc_buyback WHERE shipment_number='".$shipment_number."'");
$v_shipment = $db->func_query_first_cell("select id from inv_shipments where package_number = '".$shipment_number."'");
//testObject($v_shipment);

$total_products = $db->func_query_first_cell("SELECT SUM(oem_a_qty) + SUM(oem_b_qty) + SUM(oem_c_qty) + SUM(oem_d_qty) + sum(non_oem_a_qty) + sum(non_oem_b_qty) + sum(non_oem_c_qty) + sum(non_oem_d_qty) FROM oc_buyback_products WHERE buyback_id='".$detail['buyback_id']."'");

if(isset($_POST) and $_POST['action']=='approve_it')
{
	$db->db_exec("UPDATE oc_buyback SET is_approved=1,approved_by='".$_SESSION['user_id']."',approved_date='".date('Y-m-d H:i:s')."' where shipment_number='".$shipment_number."'");

		actionLog('LBB approved for QC');
}

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
	header("Location:$host_path/buyback/shipments.php");
	exit;
	
}
if($_SESSION['login_as']=='admin')
{
	$_SESSION['buyback_qc_shipments'] = 1;
	$_SESSION['buyback_receive_shipments'] = 1;
}
if($_SESSION['buyback_qc_shipments'] and in_array($detail['status'],array('Received')))
{
	$flag_change_qty = true;	
}
else
{
	$flag_change_qty = false;
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

if($_POST['received'] || $_POST['qcdone'] || $_POST['completed'] || $_POST['save'] || $_POST['reprint'])
{

	


	if($_POST['save'])
	{
		$db->db_exec("UPDATE oc_buyback SET shipping_cost='".$_POST['shipping_cost']."' WHERE shipment_number='".$shipment_number."'");
		if($_POST['change_status']=='In QC')
		{
			$change_status = 'QC Completed';
		}
		else
		{
			$change_status = $_POST['change_status'];
		}

		if($detail['status']!=$change_status && isset($_POST['change_status']))
		{
			$db->db_exec("UPDATE oc_buyback SET status='".$_POST['change_status']."' WHERE shipment_number='".$shipment_number."'");

			actionLog('Force LBB Status is changed from '.$detail['status'].' to '.$change_status);
		}

		if($detail['payment_type']!= $_POST['payment_type'] && isset($_POST['payment_type']))
		{
			$db->db_exec("UPDATE oc_buyback SET payment_type='".$_POST['payment_type']."' WHERE shipment_number='".$shipment_number."'");

			actionLog('Force LBB Payment Type is changed from '.$detail['payment_type'].' to '.$_POST['payment_type']);
		}
		


		if($detail['option']!= $_POST['option'] && isset($_POST['option']))
		{
			$db->db_exec("UPDATE oc_buyback SET `option`='".$_POST['option']."' WHERE shipment_number='".$shipment_number."'");

			actionLog('Force LBB Customer Decision is changed from '.$detail['option'].' to '.$_POST['option']);
		}


	}
	
	if($_POST['received'])
	{
		$db->db_exec("UPDATE oc_buyback SET status='Received',date_received='".date('Y-m-d H:i:s')."' WHERE shipment_number='".$shipment_number."'");
		
		
		addBBComment($detail['buyback_id'],$_SESSION['login_as'].' has changed the status to Received.');
		
	}

	else if($_POST['completed'])
	{
		$db->db_exec("UPDATE oc_buyback SET status='Completed',date_completed='".date('Y-m-d H:i:s')."' WHERE shipment_number='".$shipment_number."'");	
		addBBComment($detail['buyback_id'],$_SESSION['login_as'].' has changed the status to Completed.');
		$log = 'LBB Shipment# ' . linkToLbbShipment($shipment_number) . ' status changed from QC Completed to Completed';

		// make shipment when completed
			
		if (!$v_shipment) {
			$shipment_data = array();
			$shipment_data['package_number'] = $_POST['shipment'];
			$shipment_data['shipping_cost'] = $_POST['shipping_cost'];
			$shipment_data['carrier'] = $_POST['carrier'];
			$shipment_data['tracking_number'] = $_POST['tracking_no'];
			$shipment_data['ex_rate'] = '1';
			$shipment_data['status'] = 'Issued';
			$shipment_data['date_added'] = date('Y-m-d H:i:s');
			$shipment_data['date_issued'] = date('Y-m-d H:i:s');	
			$inv_shipment_id = $db->func_array2insert("inv_shipments",$shipment_data);
			foreach ($_POST['admin_oem_a_qty'] as $buyback_product_id => $qty) {
				if ($qty>0) {
					$main_sku = $db->func_query_first_cell("SELECT sku FROM oc_buyback_products WHERE buyback_product_id='".$buyback_product_id."'");
					$sku_data = $db->func_query_first("SELECT * FROM inv_buy_back WHERE sku='".$main_sku."'");
					$product_id = $db->func_query_first_cell("SELECT product_id FROM oc_product WHERE sku='".$sku_data['oem_a_desc']."'");
					$shipment_item = array();
					$shipment_item['product_id']  = $product_id;
					$shipment_item['product_sku'] = $sku_data['oem_a_desc'];
					$shipment_item['unit_price']  = $sku_data['oem_a'];
					$shipment_item['qty_shipped']  = $qty;
					$shipment_item['shipment_id'] = $inv_shipment_id;
					$db->func_array2insert("inv_shipment_items",$shipment_item);
				}
				
			}
			foreach ($_POST['admin_oem_b_qty'] as $buyback_product_id => $qty) {
				if ($qty>0) {
					$main_sku = $db->func_query_first_cell("SELECT sku FROM oc_buyback_products WHERE buyback_product_id='".$buyback_product_id."'");
					$sku_data = $db->func_query_first("SELECT * FROM inv_buy_back WHERE sku='".$main_sku."'");
					$product_id = $db->func_query_first_cell("SELECT product_id FROM oc_product WHERE sku='".$sku_data['oem_b_desc']."'");
					$shipment_item = array();
					$shipment_item['product_id']  = $product_id;
					$shipment_item['product_sku'] = $sku_data['oem_b_desc'];
					$shipment_item['unit_price']  = $sku_data['oem_b'];
					$shipment_item['qty_shipped']  = $qty;
					$shipment_item['shipment_id'] = $inv_shipment_id;
					$db->func_array2insert("inv_shipment_items",$shipment_item);
				}
				
			}
			foreach ($_POST['admin_oem_c_qty'] as $buyback_product_id => $qty) {
				if ($qty>0) {
					$main_sku = $db->func_query_first_cell("SELECT sku FROM oc_buyback_products WHERE buyback_product_id='".$buyback_product_id."'");
					$sku_data = $db->func_query_first("SELECT * FROM inv_buy_back WHERE sku='".$main_sku."'");
					$product_id = $db->func_query_first_cell("SELECT product_id FROM oc_product WHERE sku='".$sku_data['oem_c_desc']."'");
					$shipment_item = array();
					$shipment_item['product_id']  = $product_id;
					$shipment_item['product_sku'] = $sku_data['oem_c_desc'];
					$shipment_item['unit_price']  = $sku_data['oem_c'];
					$shipment_item['qty_shipped']  = $qty;
					$shipment_item['shipment_id'] = $inv_shipment_id;
					$db->func_array2insert("inv_shipment_items",$shipment_item);
				}
				
			}
			foreach ($_POST['admin_oem_d_qty'] as $buyback_product_id => $qty) {
				if ($qty>0) {
					$main_sku = $db->func_query_first_cell("SELECT sku FROM oc_buyback_products WHERE buyback_product_id='".$buyback_product_id."'");
					$sku_data = $db->func_query_first("SELECT * FROM inv_buy_back WHERE sku='".$main_sku."'");
					$product_id = $db->func_query_first_cell("SELECT product_id FROM oc_product WHERE sku='".$sku_data['oem_d_desc']."'");
					$shipment_item = array();
					$shipment_item['product_id']  = $product_id;
					$shipment_item['product_sku'] = $sku_data['oem_d_desc'];
					$shipment_item['unit_price']  = $sku_data['oem_d'];
					$shipment_item['qty_shipped']  = $qty;
					$shipment_item['shipment_id'] = $inv_shipment_id;
					$db->func_array2insert("inv_shipment_items",$shipment_item);
				}
				
			}
			foreach ($_POST['admin_non_oem_a_qty'] as $buyback_product_id => $qty) {
				if ($qty>0) {
					$main_sku = $db->func_query_first_cell("SELECT sku FROM oc_buyback_products WHERE buyback_product_id='".$buyback_product_id."'");
					$sku_data = $db->func_query_first("SELECT * FROM inv_buy_back WHERE sku='".$main_sku."'");
					$product_id = $db->func_query_first_cell("SELECT product_id FROM oc_product WHERE sku='".$sku_data['non_oem_a_desc']."'");
					$shipment_item = array();
					$shipment_item['product_id']  = $product_id;
					$shipment_item['product_sku'] = $sku_data['non_oem_a_desc'];
					$shipment_item['unit_price']  = $sku_data['non_oem_a'];
					$shipment_item['qty_shipped']  = $qty;
					$shipment_item['shipment_id'] = $inv_shipment_id;
					$db->func_array2insert("inv_shipment_items",$shipment_item);
				}
				
			}
			foreach ($_POST['admin_non_oem_b_qty'] as $buyback_product_id => $qty) {
				if ($qty>0) {
					$main_sku = $db->func_query_first_cell("SELECT sku FROM oc_buyback_products WHERE buyback_product_id='".$buyback_product_id."'");
					$sku_data = $db->func_query_first("SELECT * FROM inv_buy_back WHERE sku='".$main_sku."'");
					$product_id = $db->func_query_first_cell("SELECT product_id FROM oc_product WHERE sku='".$sku_data['non_oem_b_desc']."'");
					$shipment_item = array();
					$shipment_item['product_id']  = $product_id;
					$shipment_item['product_sku'] = $sku_data['non_oem_b_desc'];
					$shipment_item['unit_price']  = $sku_data['non_oem_b'];
					$shipment_item['qty_shipped']  = $qty;
					$shipment_item['shipment_id'] = $inv_shipment_id;
					$db->func_array2insert("inv_shipment_items",$shipment_item);
				}
				
			}
			foreach ($_POST['admin_non_oem_c_qty'] as $buyback_product_id => $qty) {
				if ($qty>0) {
					$main_sku = $db->func_query_first_cell("SELECT sku FROM oc_buyback_products WHERE buyback_product_id='".$buyback_product_id."'");
					$sku_data = $db->func_query_first("SELECT * FROM inv_buy_back WHERE sku='".$main_sku."'");
					$product_id = $db->func_query_first_cell("SELECT product_id FROM oc_product WHERE sku='".$sku_data['non_oem_c_desc']."'");
					$shipment_item = array();
					$shipment_item['product_id']  = $product_id;
					$shipment_item['product_sku'] = $sku_data['non_oem_c_desc'];
					$shipment_item['unit_price']  = $sku_data['non_oem_c'];
					$shipment_item['qty_shipped']  = $qty;
					$shipment_item['shipment_id'] = $inv_shipment_id;
					$db->func_array2insert("inv_shipment_items",$shipment_item);
				}
				
			}
			foreach ($_POST['admin_non_oem_d_qty'] as $buyback_product_id => $qty) {
				if ($qty>0) {
					$main_sku = $db->func_query_first_cell("SELECT sku FROM oc_buyback_products WHERE buyback_product_id='".$buyback_product_id."'");
					$sku_data = $db->func_query_first("SELECT * FROM inv_buy_back WHERE sku='".$main_sku."'");
					$product_id = $db->func_query_first_cell("SELECT product_id FROM oc_product WHERE sku='".$sku_data['non_oem_d_desc']."'");
					$shipment_item = array();
					$shipment_item['product_id']  = $product_id;
					$shipment_item['product_sku'] = $sku_data['non_oem_d_desc'];
					$shipment_item['unit_price']  = $sku_data['non_oem_d'];
					$shipment_item['qty_shipped']  = $qty;
					$shipment_item['shipment_id'] = $inv_shipment_id;
					$db->func_array2insert("inv_shipment_items",$shipment_item);
				}
				
			}
			foreach ($_POST['admin_salvage_qty'] as $buyback_product_id => $qty) {
				if ($qty>0) {
					$main_sku = $db->func_query_first_cell("SELECT sku FROM oc_buyback_products WHERE buyback_product_id='".$buyback_product_id."'");
					$sku_data = $db->func_query_first("SELECT * FROM inv_buy_back WHERE sku='".$main_sku."'");
					$product_id = $db->func_query_first_cell("SELECT product_id FROM oc_product WHERE sku='".$sku_data['salvage_desc']."'");
					$shipment_item = array();
					$shipment_item['product_id']  = $product_id;
					$shipment_item['product_sku'] = $sku_data['salvage_desc'];
					$shipment_item['unit_price']  = $sku_data['salvage'];
					$shipment_item['qty_shipped']  = $qty;
					$shipment_item['shipment_id'] = $inv_shipment_id;
					$db->func_array2insert("inv_shipment_items",$shipment_item);
				}
				
			}
			foreach ($_POST['admin_unacceptable'] as $buyback_product_id => $qty) {
				if ($qty>0) {
					$main_sku = $db->func_query_first_cell("SELECT sku FROM oc_buyback_products WHERE buyback_product_id='".$buyback_product_id."'");
					$sku_data = $db->func_query_first("SELECT * FROM inv_buy_back WHERE sku='".$main_sku."'");
					$product_id = $db->func_query_first_cell("SELECT product_id FROM oc_product WHERE sku='".$sku_data['unacceptable_desc']."'");
					$shipment_item = array();
					$shipment_item['product_id']  = $product_id;
					$shipment_item['product_sku'] = $sku_data['unacceptable_desc'];
					$shipment_item['unit_price']  = '0.00';
					$shipment_item['qty_shipped']  = $qty;
					$shipment_item['shipment_id'] = $inv_shipment_id;
					$db->func_array2insert("inv_shipment_items",$shipment_item);
				}
				
			}
			foreach ($_POST['admin_rejected'] as $buyback_product_id => $qty) {
				if ($qty>0) {
					$main_sku = $db->func_query_first_cell("SELECT sku FROM oc_buyback_products WHERE buyback_product_id='".$buyback_product_id."'");
					$sku_data = $db->func_query_first("SELECT * FROM inv_buy_back WHERE sku='".$main_sku."'");
					$product_id = $db->func_query_first_cell("SELECT product_id FROM oc_product WHERE sku='".$sku_data['damaged_desc']."'");
					$shipment_item = array();
					$shipment_item['product_id']  = $product_id;
					$shipment_item['product_sku'] = $sku_data['damaged_desc'];
					$shipment_item['unit_price']  = '0.00';
					$shipment_item['qty_shipped']  = $qty;
					$shipment_item['shipment_id'] = $inv_shipment_id;
					$db->func_array2insert("inv_shipment_items",$shipment_item);
				}
				
			}
		}
	}
	else if($_POST['reprint']){

		foreach ($products as $product) {

			$quantities = $db->func_query_first("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$product['buyback_product_id']."'");

			$mapped_sku=$db->func_query_first("SELECT * FROM inv_buy_back WHERE sku='".$product['sku']."'");

			$oem_a_qty=$quantities['oem_qty_a'];
			$oem_b_qty=$quantities['oem_qty_b'];
			$oem_c_qty=$quantities['oem_qty_c'];
			$oem_d_qty=$quantities['oem_qty_d'];
			$non_oem_a_qty=$quantities['non_oem_qty_a'];
			$non_oem_b_qty=$quantities['non_oem_qty_b'];
			$non_oem_c_qty=$quantities['non_oem_qty_c'];
			$non_oem_d_qty=$quantities['non_oem_qty_d'];	
			$salvage_qty=$quantities['salvage_qty'];
			$unacceptable=$quantities['unacceptable_qty'];
			$damaged=$quantities['rejected_qty'];

			if($_POST['printerid']){
				while($oem_a_qty>0){
					printLabel('',$mapped_sku['oem_a_desc'],'','','',$_POST['printerid'],'','25','1');
					$oem_a_qty--;
				}
				while($oem_b_qty>0){
					printLabel('',$mapped_sku['oem_a_desc'],'','','',$_POST['printerid'],'','25','1');
					$oem_b_qty--;	
				}

				while($oem_c_qty>0){
					printLabel('',$mapped_sku['oem_a_desc'],'','','',$_POST['printerid'],'','25','1');
					$oem_c_qty--;	
				}
				while($oem_d_qty>0){
					printLabel('',$mapped_sku['oem_a_desc'],'','','',$_POST['printerid'],'','25','1');
					$oem_d_qty--;		
				}
				while($non_oem_a_qty>0){
					printLabel('',$mapped_sku['oem_a_desc'],'','','',$_POST['printerid'],'','25','1');
					$non_oem_a_qty--;
				}
				while($non_oem_b_qty>0){
					printLabel('',$mapped_sku['oem_a_desc'],'','','',$_POST['printerid'],'','25','1');
					$non_oem_b_qty--;
				}
				while($non_oem_c_qty>0){
					printLabel('',$mapped_sku['oem_a_desc'],'','','',$_POST['printerid'],'','25','1');
					$non_oem_c_qty--;
				}
				while($non_oem_d_qty>0){
					printLabel('',$mapped_sku['oem_a_desc'],'','','',$_POST['printerid'],'','25','1');
					$non_oem_d_qty--;
				}
				while($salvage_qty>0){
					printLabel('',$mapped_sku['oem_a_desc'],'','','',$_POST['printerid'],'','25','1');
					$salvage_qty--;
				}
				while($unacceptable>0) {
					printLabel('',$mapped_sku['oem_a_desc'],'','','',$_POST['printerid'],'','25','1');
					$unacceptable--;
				}
				while($damaged>0) {
					printLabel('',$mapped_sku['oem_a_desc'],'','','',$_POST['printerid'],'','25','1');
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


		/*$last_id = $db->func_query_first_cell ( "select id from inv_buyback_boxes where status = 'Pending'" );
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
		logLbbItem($product['sku'], 'Moved to '.$_link2.' from LBB Shipment # '. $_link1 . ' by ', $from, $to);	
		*/

		$db->db_exec("UPDATE oc_buyback_products SET total_oem_a_total='".(float)$_POST['total_oem_a_total'][$product['buyback_product_id']]."', total_oem_b_total='".(float)$_POST['total_oem_b_total'][$product['buyback_product_id']]."', total_oem_c_total='".(float)$_POST['total_oem_c_total'][$product['buyback_product_id']]."', total_oem_d_total='".(float)$_POST['total_oem_d_total'][$product['buyback_product_id']]."', total_non_oem_a_total='".(float)$_POST['total_non_oem_a_total'][$product['buyback_product_id']]."', total_non_oem_b_total='".(float)$_POST['total_non_oem_b_total'][$product['buyback_product_id']]."', total_non_oem_c_total='".(float)$_POST['total_non_oem_c_total'][$product['buyback_product_id']]."', total_non_oem_d_total='".(float)$_POST['total_non_oem_d_total'][$product['buyback_product_id']]."', total_rejected_total='".(float)$_POST['total_rejected_total'][$product['buyback_product_id']]."' WHERE buyback_product_id='".$product['buyback_product_id']."'");

				// Shipment Box Preparation Ends


	}

}

if($_POST['completed']) {
// die('here');
	$products = $db->func_query("SELECT * FROM oc_buyback_products WHERE buyback_id='".$detail['buyback_id']."'");

	foreach($products as $product) {
		// $db->db_exec("DELETE FROM inv_buyback_shipments WHERE buyback_product_id='".$product['buyback_product_id']."'");

		$data = array();
		$data['oem_qty_a'] = (int)$_POST['admin_oem_a_qty'][$product['buyback_product_id']];
		$data['oem_qty_b'] = (int)$_POST['admin_oem_b_qty'][$product['buyback_product_id']];
		$data['oem_qty_c'] = (int)$_POST['admin_oem_c_qty'][$product['buyback_product_id']];
		$data['oem_qty_d'] = (int)$_POST['admin_oem_d_qty'][$product['buyback_product_id']];

		$data['non_oem_qty_a'] = (int)$_POST['admin_non_oem_a_qty'][$product['buyback_product_id']];
		$data['non_oem_qty_b'] = (int)$_POST['admin_non_oem_b_qty'][$product['buyback_product_id']];
		$data['non_oem_qty_c'] = (int)$_POST['admin_non_oem_c_qty'][$product['buyback_product_id']];
		$data['non_oem_qty_d'] = (int)$_POST['admin_non_oem_d_qty'][$product['buyback_product_id']];

		$data['salvage_qty'] = (int)$_POST['admin_salvage_qty'][$product['buyback_product_id']];
		$data['unacceptable_qty'] = (int)$_POST['admin_unacceptable_qty'][$product['buyback_product_id']];
		$data['rejected_qty'] = (int)$_POST['admin_rejected'][$product['buyback_product_id']];
		
		$data['buyback_product_id'] = $product['buyback_product_id'];

		$data['date_added'] = date('Y-m-d H:i:s');

		// $db->func_array2insert('inv_buyback_shipments',$data);

		// $db->db_exec("UPDATE oc_buyback_products SET total_qc_received = '".(int)$_POST['total_qc_received'][$product['buyback_product_id']]."' WHERE buyback_product_id='".$product['buyback_product_id']."'");

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
		logLbbItem($product['sku'], 'Moved to '.$_link2.' from LBB Shipment # '. $_link1 . ' by ', $from, $to);	
	}

}



if($_POST['qcdone'])
{



		//print_r(count($products));exit;
	foreach ($products as $product) {

		$quantities = $db->func_query_first("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$product['buyback_product_id']."'");

		$mapped_sku=$db->func_query_first("SELECT * FROM inv_buy_back WHERE sku='".$product['sku']."'");


		$oem_a_qty=$quantities['oem_qty_a'];
		$oem_b_qty=$quantities['oem_qty_b'];
		$oem_c_qty=$quantities['oem_qty_c'];
		$oem_d_qty=$quantities['oem_qty_d'];
		$non_oem_a_qty=$quantities['non_oem_qty_a'];
		$non_oem_b_qty=$quantities['non_oem_qty_b'];
		$non_oem_c_qty=$quantities['non_oem_qty_c'];
		$non_oem_d_qty=$quantities['non_oem_qty_d'];	
		$salvage_qty=$quantities['salvage_qty'];
		$unacceptable=$quantities['unacceptable_qty'];
		$damaged=$quantities['rejected_qty'];

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

	$db->db_exec("UPDATE oc_buyback SET status='In QC',date_qc='".date('Y-m-d H:i:s')."' WHERE shipment_number='".$shipment_number."'");
	addBBComment($detail['buyback_id'],$_SESSION['login_as'].' has changed the status to QC Completed.');
	$log = 'LBB Shipment# ' . linkToLbbShipment($shipment_number) . ' status changed from Received to QC Completed';
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

// echo 'here 2';exit;

$_SESSION['message'] = 'Modification Made!';
header("Location:shipment_detail.php?shipment=$shipment_number");
exit;
}
if ($_POST['make_shipment']) {
	//testObject($_POST);exit;
	
	$_SESSION['message'] = 'Vendor Shipment Made!';
	header("Location:shipment_detail.php?shipment=$shipment_number");
	exit;
}
if($_POST['addcomment'])
{
	
	addBBComment($detail['buyback_id'],$_POST['comment']);
	
	$_SESSION['message'] = 'Comment has been added';
	header("Location:shipment_detail.php?shipment=$shipment_number");
	exit;
}
if($detail['carrier_code']!= $_POST['carrier'] && isset($_POST['carrier']))
{
	$db->db_exec("UPDATE oc_buyback SET carrier_code='".$_POST['carrier']."' WHERE shipment_number='".$shipment_number."'");
	$detail['carrier_code'] = $_POST['carrier'];
}
if($detail['tracking_no']!= $_POST['tracking_no'] && isset($_POST['tracking_no']))
{
	$db->db_exec("UPDATE oc_buyback SET tracking_no='".$_POST['tracking_no']."' WHERE shipment_number='".$shipment_number."'");
	$detail['tracking_no'] = $_POST['tracking_no'];
}

if($detail['tracking_no']!='' && strlen($detail['tracking_no'])>5 && $detail['carrier_code']!='' && $detail['carrier_code']!='In House' && $detail['is_easypost'] != 1) {
    //echo "here";exit;
	global $host_path;
	$post = array(
		"tracking_number" => $detail['tracking_no'],
		"shipment_number" => $shipment_number,
		"carrier" => $detail['carrier_code']);
	$ch = curl_init($host_path . 'easypost/tracker_api.php');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$response = curl_exec($ch);
	curl_close($ch);
	$response = json_decode($response);
	//print_r ($response);exit;
	if ($response->success)  
	{
		//$db->db_exec("UPDATE oc_buyback SET is_easypost='1' WHERE shipment_number='".$shipment_number."'");
	}

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


	<script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox({ width: '700px' , autoCenter : true , height : '500px'});
			$('.fancybox2').fancybox({ width: '700px' , height: 'auto' , autoCenter : true , autoSize : true });
		});
		var items = '';
		<?php
		$_items = $db->func_query("SELECT sku,description FROM inv_buy_back ORDER BY sort");
		foreach($_items as $_item)
		{
			?>
			items = items + '<option value="<?php echo $_item['sku'];?>"><?=$_item['description'];?></option>';

			<?php
		}
		?>
		items = items + '<option value="-1">Other</option>';
		function addRowReceived(){

			var current_row = $('#received_table tr').length+1;	
			var row = "<tr>"+
			"<td><select name='new_received_lcd[]' onChange='otherLCD(this)'>"+items+"</select><div class='other_lcd' style='display:none'><input type='text'  name='other_lcd[]' placeholder='Provide the name of LCD' ></div></td>"+
			"<td class='light-grey'><input class='total_received' type='text' id='' value='0' name='new_total_received[]' style='width:80px'  /> <a href='javascript:void(0);' onClick='$(this).parent().parent().remove();'>X</a></td>"+
			"</tr>";
		//$("#received_table").append(row);	
		
		$(row).insertBefore('#received_table tr:nth-last-child(2)');	
		current_row++;	 
	}
	function otherLCD(obj)
	{
		if($(obj).val()=='-1')
		{
			$(obj).parent().find('.other_lcd').show();
		}
		else
		{
			$(obj).parent().find('.other_lcd').hide();
		}
	}
	function allowNum (t) {
		var input = $(t).val();
		var valid = input.substring(0, input.length - 1);
		if (input == '') {
			$(t).val(0);
		}
		if (isNaN(input)) {
			$(t).val(valid);
		}
	}
	function addRowQC(){

		var current_row = $('#qc_table tr').length+1;	
		var row = "<tr>"+
		"<td><select name='new_qc_lcd[]'>"+items+"</select></td>"+
		"<td align='center'><input type='text' name='new_oem_qty_a[]' style='width:80px' value='0'></td>"+
		"<td align='center'><input type='text' name='new_oem_qty_b[]' style='width:80px' value='0'></td>"+
		"<td align='center'><input type='text' name='new_oem_qty_c[]' style='width:80px' value='0'></td>"+
		"<td align='center'><input type='text' name='new_oem_qty_d[]' style='width:80px' value='0'></td>"+
		"<td align='center'><input type='text' name='new_non_oem_qty_a[]' style='width:80px' value='0'></td>"+
		"<td align='center'><input type='text' name='new_non_oem_qty_b[]' style='width:80px' value='0'></td>"+
		"<td align='center'><input type='text' name='new_non_oem_qty_c[]' style='width:80px' value='0'></td>"+
		"<td align='center'><input type='text' name='new_non_oem_qty_d[]' style='width:80px' value='0'></td>"+
		"<td align='center'><input type='text' name='salvage[]' style='width:80px' value='0'></td>"+
		"<td align='center' style='display:none'><input type='text' name='unacceptable[]' style='width:80px' value='0'></td>"+
		"<td align='center'><input type='text' name='new_rejected_qty[]' style='width:80px' value='0'></td>"+
		"<td align='center' class='light-grey'><input type='text' name='new_total_qc_received[]' style='width:80px' value='0'> <a href='javascript:void(0);' onClick='$(this).parent().parent().remove();'>X</a></td>"+
		"<td align='center' ></td>"+
		"</tr>";
		//$("#received_table").append(row);	
		
		$(row).insertBefore('#qc_table tr:nth-last-child(1)');	
		current_row++;	 
	}
	
</script>	

<style>
	.light-grey{
		background-color:#CCC;	
	}
</style>
</head>
<body>
	<div class="div-fixed">
		<div align="center"> 
			<?php include_once '../inc/header.php'; ?>
		</div>

		<?php if ($_SESSION['message']): ?>
			<div align="center"><br />
				<font color="red"><?php
					echo $_SESSION['message'];
					unset($_SESSION['message']);
					?><br /></font>
				</div>
			<?php endif; ?>

			<div align="center" style="width:90%;margin:0 auto;">
				<form method="post" action="" id="returnForm" enctype="multipart/form-data">
					<h2>LBB Shipment Details (<a href="pdf_report.php?shipment_number=<?=$shipment_number;?>" target="_blank">Print PDF</a>)</h2><br>
					<h1 style="font-size:14px;color:#0059a0">Created By: 
						<?php if($detail['added_by']==0){
							$user_email = $db->func_query_first_cell("SELECT email FROM oc_customer WHERE customer_id='".$detail['customer_id']."'");
							echo linkToProfile($user_email,$host_path);
						}else{
							echo get_username($detail['added_by']);
						}
						?> (<?php echo americanDate($detail['date_added']); ?>)</h1>

						<?php
						if($detail['is_approved'])
						{
							?>
							<h1 style="font-size:14px;color:red">Approved By: 
						<?php 
							echo get_username($detail['approved_by']);
						
						?> (<?php echo americanDate($detail['approved_date']); ?>)</h1>
							<?php
						}
						?>


						<?php
						if($_SESSION['approve_send_label']==1 && $detail['is_void']==0 && $detail['pdf_label']!='')
						{
							?>
							<input type="button" class="button" value="Re-Print Shipping Label" onclick="rePrintLabel();"><br><br>
							<?php
						}

						?>


						<?php
						if($detail['customer_id']==0)
						{
							$email = $detail['email'];
							$telephone = $detail['telephone'];
							$firstname = $detail['firstname'];
							$lastname = $detail['lastname'];

							$address_1 = $detail['address_1'];
							$city = $detail['city'];
							$postcode = $detail['postcode'];
							$zone_id = $detail['zone_id'];
						}
						else
						{

							$customer_detail = $db->func_query_first("SELECT email,telephone FROM oc_customer WHERE customer_id='".$detail['customer_id']."'");
							$address = $db->func_query_first("SELECT * FROM oc_address WHERE address_id='".$detail['address_id']."'");

							$email = $customer_detail['email'];
							$telephone = $customer_detail['telephone'];

							if($detail['address_id']!='-1' && $detail['address_id']!='0')
							{

								$firstname = $address['firstname'];
								$lastname = $address['lastname'];

								$address_1 = $address['address_1'];
								$city = $address['city'];
								$postcode = $address['postcode'];
								$zone_id = $address['zone_id'];
							}
							else
							{
								$firstname = $detail['firstname'];
								$lastname = $detail['lastname'];

								$address_1 = $detail['address_1'];

								$city = $detail['city'];
								$postcode = $detail['postcode'];
								$zone_id = $detail['zone_id'];

							}
						}
						$zone = $db->func_query_first_cell("SELECT name FROM oc_zone WHERE zone_id='".(int)$zone_id."'");
						?>
						<table border="1" cellpadding="10" cellspacing="0" width="90%">
							<tr>
								<td>
									<table cellpadding="5">
										<caption><b>Shipping</b> <?php if($_SESSION['login_as']=='admin')
											{
												?>
												<small style="font-size:8px">(<a class="fancybox3 fancybox.iframe" href="buyback_customer_lookup.php?shipment=<?=$_GET['shipment'];?>">Map Customer</a>)</small>
												<?php
											}
											?></caption>
											<tr>	
												<td><b>Full Name:</b></td>
												<td><?php echo $firstname . " " . $lastname; ?></td>
												<?php
												if( ($_SESSION['approve_return_shipp']==1 || $_SESSION['approve_send_label']==1) && $product_total>=25 )
												{
													?>
													<td><strong><?=$detail['approval_count'];?> of 2 Required Approvals</strong></td>
													<?php

												}
												else
												{
													?>
													<td></td>
													<?php
												}
												?>
											</tr>
											<tr>	
												<td><b>Telephone:</b></td>
												<td><?=$telephone;?></td>
												<?php

												if( ($_SESSION['approve_return_shipp']==1 || $_SESSION['approve_send_label']==1) && $product_total>=25 )
												{
													if($detail['is_label_created']==1)
													{

														if($detail['is_voided']==1)
														{
															?>
															<td>Shipment Voided</td>
															<?php
														}
														else 
														{
															?>
															<td><a class="button" href="javascript:void(0);" onclick="voidShippingLabel();">Void Shipping Label</a>
																<?php
															}
														}
														else if ($_SESSION['user_id'] != $detail['approved_user_id1'] && $detail['approved_user_id2'] != $_SESSION['user_id'])
														{
															?>


															<td><a class="fancybox2 fancybox.iframe button" href="approve_shipping_label.php?buyback_id=<?=$detail['buyback_id'];?>&firstname=<?php echo base64_encode($firstname);?>&lastname=<?php echo base64_encode($lastname);?>&email=<?php echo base64_encode($email);?>">Approve Shipping Label</a>



																<?php
															}
														}
														else
														{
															?>	
															<td></td>
															<?php
														}
														?>
													</tr>

													<tr>	
														<td><b>Email:</b></td>
														<td><?php echo linkToProfile($email,$host_path); ?></td>
														<td></td>
													</tr>

													<tr>	
														<td><b>Address 1:</b></td>
														<td><?php echo $address_1 ?></td>
														<td></td>
													</tr>


													<tr>	
														<td><b>City:</b> <?php echo $city; ?></td>
														<td><b>State:</b> <?php echo $zone; ?></td>
														<td><b>Zip:</b> <?php echo $postcode; ?></td>
													</tr>
												</table>	    
											</td>

											<td>
												<table cellpadding="5">
													<caption><b>Payment</b></caption>
													<tr>	
														<td><b>Payment Type:</b></td>
														<td>
															<?php $payment_type = array('store_credit' => 'Store Credit', 'cash' => 'Cash');?>
															<?php if ($_SESSION['login_as'] == 'admin') { ?>
															<select name="payment_type">
																<?php foreach ($payment_type as $valx => $nam) { ?>
																<option value="<?php echo $valx; ?>" <?php echo ($detail['payment_type'] == $valx)? 'selected="selected"':'';?>><?php echo $nam; ?></option>
																<?php } ?>
															</select>
															<?php } else { ?>
															<?php echo $payment_type[$detail['payment_type']]; ?>
															<?php }?>
														</td>
														<td></td>
													</tr>

													<tr>	
														<td><b>PayPal Email:</b></td>
														<td><?php echo $detail['paypal_email'];?></td>
														<td></td>
													</tr>



													<tr>	
														<td><strong>Total</strong></td>
														<td>$<?=number_format($detail['total'],2);?></td>
														<td></td>
													</tr>

												<!-- <tr>	
													<td><strong>Tracking #</strong></td>
													<td colspan="2"><?=$detail['tracking_no'];?> </td>

												</tr> -->
											</table>	    
										</td>

										<td>
											<table cellpadding="5">
												<?php 

												if($detail['customer_id']==0){

													$inv_customer=$detail['email'];
												}else{

													$inv_customers=$db->func_query_first("select firstname,lastname from inv_customers where id='".$detail['customer_id']."' ");

													if(!$inv_customers){
														$inv_customers=$db->func_query_first("select firstname,lastname from oc_customer where customer_id='".$detail['customer_id']."' ");
													}
												}
											//print_r($inv_customer);exit;
												?>
												<caption><b>Other Detail</b></caption>
												<!--<tr>
												<td><b>Created By: </b>-->
												<?php //if($detail['added_by']==0){
													//$user_email = $db->func_query_first_cell("SELECT email FROM oc_customer WHERE customer_id='".$detail['customer_id']."'");
														//echo linkToProfile($user_email,$host_path);
													//}else{
														//echo get_username($detail['added_by']);
													//}
													?>
													<!--</td>
												</tr>-->
												<tr>
													<td><b>LBB ID: </b> <?=$detail['buyback_id'];?></td>
													<td>|</td>
													<td><b>Shipment #: <?=$detail['shipment_number'];?></a></b></td>


												</tr>


												<tr>
													<td><b>Added: </b><?php echo americanDate($detail['date_added']); ?></td>
													<td>|</td>
													<td><b>Date Received: </b><?php echo americanDate($detail['date_received']); ?></td>	    	       
												</tr>

												<tr>
													<?php
													if($detail['date_qc']):

														?>
													<td><b>QC Date: </b><?php echo americanDate($detail['date_qc']); ?></td>
													<td>|</td>
													<?php
													endif;
													if($detail['status']=='Completed'):

														?>
													<td><b>Completed Date:  </b> <?php echo americanDate($detail['date_completed']); ?></td>	 
													<?php
													endif;
													?>   	       
												</tr>

												<tr>
													<td><b>Status: </b> <?php echo  $detail['status']; ?></td>
													<td>| </td>	  
													<td><strong>Procedure:</strong> <?php echo ($_SESSION['buyback_decision'])? '<select name="option"><option value="Return">Return</option><option value="Dispose" ' . (($detail['option'] == 'Dispose')? 'selected="selected"': '') . '>Dispose</option></select>': $detail['option']; ?> </td>
												</tr>
												<?php
												if($detail['fb_added']==1)
												{
													$fb_status = 'Uploaded';

												}
												else
												{
													$fb_status = 'Not Uploaded';
												}
												if($detail['ignored']==1)
												{
													$fb_status = 'Ignored';
												}
												?>
												<tr>
													<td><b>FishBowl Status: </b> <?php echo  $fb_status; ?></td>
													<td> </td>	  
													<td></td>	    
												</tr>	



											</table>
										</td>
									</tr> 
									<?php
									$payment_detail = $db->func_query_first("SELECT * FROM inv_buyback_payments WHERE buyback_id='".$detail['buyback_id']."'");
									?>
									<tr>
										<td colspan="3" align="left"> <table cellpadding="5" style="width:100%" >
											<tr>
												<td style="font-weight:bold">Payment Details: </td>
												<td style="font-weight:bold" ><?php
													if($payment_detail)
													{
														if($detail['payment_type']=='store_credit')
														{
															?>
															<b>Store Credit # <?php echo $payment_detail['credit_code'];?> of amount $<?php echo number_format($payment_detail['amount'],2);?> </b>
															<?php	

														}
														else
														{
															?>
															<b>PayPal Transaction ID # <?php echo $payment_detail['transaction_id'];?> of amount $<?php echo number_format($payment_detail['amount'],2);?> </b>
															<?php	
														}
														?>

														<?php	

													}
													else
													{
														echo 'Not Found';	
													}

													?>
												</td>
												<td><?php
										if($_SESSION['lbb_qc_approvals']=='1' && $detail['is_approved']==0 && $detail['status']=='Received')
										{


										?>
										<input type="button" class="button button-danger" id="approve_it" value="Approvals" onclick="approveIt();">
										<span></span>
										<?php
									}
									?></td>
												<td></td>
											</tr>

											<?php
											if($_SESSION['login_as']=='admin')
											{
												$bb_statuses = array('Awaiting'=>'Awaiting','Received'=>'Received','In QC'=>'QC Completed','Completed'=>'Completed');
												?>
												<tr>
													<td style="font-weight:bold">Change Status: </td>
													<td style="font-weight:bold" >
														<select name="change_status" class="change_status">
															<?php
															foreach($bb_statuses as $key => $value)
															{
																?>
																<option value="<?=$key;?>" <?=($detail['status']==$value?'selected':'');?>><?=$value;?></option>
																<?php
															}
															?>

														</select></td>

														<td></td>
												<td></td>

													</tr>
													<?php
												}

												?>
												<tr>
													<td style="font-weight:bold">Shipping Cost: </td>
													<td>
														<input type="text" id="shipping_cost" name="shipping_cost" value="<?php echo $detail['shipping_cost'];?>">
													</td>

													<td></td>
												<td></td>
												</tr>
											</table>
										</td>
									</tr>


								</table>

								<br />

								<br />
								<table border="0" >
									<tr>
										<td align="center" style="width: 500px;">
											<br><br>
											<form method="post">
												<table border="1" cellpadding="10" width="50%">
													<tr>
														<?php if($detail['status'] == 'Received'){ ?>
														<td align="center">
															Tracking No:<input type="text" class="tracker" required="" name="tracking_no" value="<?php echo $detail['tracking_no']; ?>">
														</td>
														<td align="center">
															Carrier:<?php echo createField("carrier", "carrier" , "select" , $detail['carrier_code'] , $carriers,'required="" class="carrier"');?>
														</td>
														<?php }else { ?>
														<td align="center">
															Tracking No:<input type="text" class="tracker" name="tracking_no" value="<?php echo $detail['tracking_no']; ?>">
														</td>
														<td align="center">
															Carrier:<?php echo createField("carrier", "carrier" , "select" , $detail['carrier_code'] , $carriers,'class="carrier"');?>
														</td>
														<?php }?>
														<td>
															<button type="submit" class="submit">Submit</button>
														</td>
													</tr>	
												</table>
											</form>
										</td>
										<?php
										$tracker = $db->func_query_first("SELECT * FROM inv_tracker WHERE shipment_id='".$shipment_number."'");
										if($tracker)
										{
											?>
											<td align="center">
												<div style="height:250px;overflow:auto;">
													<table align="center" border="1" cellspacing="0" cellpadding="5" width="100%">
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
												</div>
												<br>
											</td>
											<?php
										} 
										?>
									</tr>
								</table>
								<br><br>
								<table border="1" cellpadding="10" cellspacing="0" width="90%">
									<tr>
										<td valign="top" width="50%"><form method="post" action="">
											<table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
												<tr>
													<td>

														<b>Comment</b>
													</td>
													<td>
														<textarea rows="5" cols="50" name="comment" required></textarea>


													</td>
												</tr>

												<tr>
													<td colspan="2" align="center"> <input type="submit" class="button" name="addcomment" value="Add Comment" />	</td>
												</tr> 	   
											</table>
										</form></td><td width="50%">
										<h2 align="center">Comment History</h2>
										<table width="98%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
											<tr>
												<th>Date</th>
												<th>Comment</th>
												<th>Added By</th>
											</tr>
											<?php
											if($detail['tracking_no'])
											{
												?>
												<tr>
													<td><?=americanDate($detail['date_received']);?></td>
													<td>Tracking # <?=$detail['tracking_no'];?></td>
													<td> - </td>
												</tr>
												<?php
											}

											?>
											<?php
											$comments = $db->func_query("SELECT * FROM inv_buyback_comments WHERE buyback_id='".$detail['buyback_id']."'");
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
							<br /><br />
							<table border="0" cellpadding="10" cellspacing="0" width="90%">

								<tr>
									<td valign="top">
										<h1 style="font-size:12px">Customer Data</h1>
										<table border="1" cellpadding="10" cellspacing="0" width="60%">
											<tr style="background-color:#999;font-weight:bold;font-size:10px">
												<th>LCD Type</th>
												<th>Qty</th>
												<th>Total</th>
												<!-- <th>Price</th> -->
											</tr>
											<?php
											$customer_total = 0;
											$customer_quantity_total = 0;
											foreach($products as $product)
											{

												if($product['data_type']!='customer') continue;

													// $total = ($product['oem_price'] * $product['oem_quantity']);


												$customer_total+=$total;
												$customer_quantity_total+= $product['qty'];


												?>

												<tr >
													<td><?=$product['description'];?></td>

													<td align="center">
														<?php
														if($product['qty']>0)
														{
															?>
															<strong><?php echo $product['qty'];?></strong>
															<?php
														}
														else
														{
															echo '-';	
														}
														?>
													</td>


													<td align="center" class="light-grey"><strong><?=$product['qty'];?></strong></td>
													<!-- <td align="right" class="light-grey">$<?=number_format($total,2);?></td> -->

												</tr>

												<?php
											}
											?>
											<tr>

												<td colspan="2">

												</td>
												<td class="light-grey" align="center"><strong><?=$customer_quantity_total;?></strong></td>
												<!-- <td align="right" class="light-grey"><strong>$<?=number_format($customer_total,2);?></strong></td> -->
											</tr>


										</table>
									</td>
									<td valign="top">
										<h1 style="font-size:12px">Receiving Data</h1>
										<table border="1" id="received_table" cellpadding="5" cellspacing="0" width="70%">
											<tr style="background-color:#999;font-weight:bold;font-size:10px">
												<th>LCD Type</th>


												<th>Total</th>

											</tr>
											<?php
											$_total_received = 0;
											foreach($products as $product)
											{
												if($product['data_type']!='customer' and $product['data_type']!='received') continue;


												$_total_received+=$product['total_received'];
												?>
												<tr>
													<td><?php echo $product['description'];?></td>
													<?php
													if($flag_received_qty)
													{
														?>
														<td align="center" class="light-grey"><input type="text" class="total_received" id="total_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$product['total_received'];?>" name="total_received[<?php echo $product['buyback_product_id'];?>]" style="width:50px"  /></td>
														<?php
													}
													else
													{
														?>
														<td align="center" class="light-grey"><?php echo $product['total_received'];?></td>
														<?php	

													}
													?>
												</tr>
												<?php
											}
											?>
											<tr>
												<td> </td>
												<?php
												if($flag_received_qty)
												{
													?>
													<td align="center" class="light-grey" ><input type="text" name="received_total" value="<?=$detail['total_received'];?>"  style="width:50px"/></td>
													<?php
												}
												else
												{
													?>
													<td align="center" class="light-grey" ><?=($detail['total_received']?$detail['total_received']:$_total_received);?></td>
													<?php	
												}
												?>

											</tr>
											<?php
											if($flag_received_qty)
											{
												?>
												<tr>
													<td colspan="2" align="center"><input type="button" class="button" value="Add LCD" onclick="addRowReceived();" /></td>
												</tr>
												<?php	
											}
											?>


										</table>
									</td>
								</tr>
								<tr>
									<td valign="top">


										<h1 style="font-size:12px">QC Data
											<?php if($flag_change_qty){
												?>
												(<a href="javascript:void(0);" onclick="addRowQC();">Add LCD</a>)
												<?php

											}?>
										</h1>
										<table border="1" id="qc_table" cellpadding="10" cellspacing="0" width="70%">
											<tr style="background-color:#999;font-weight:bold;font-size:10px">
												<th rowspan="2">LCD Type</th>
												<th colspan="4">OEM</th>
												<th colspan="4">Non-OEM</th>
												<th rowspan="2">Salvage</th>
												<th rowspan="2" style="display: none">Unacceptable</th>
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
														<?php
														if($flag_change_qty)
														{
															?>
															<input type="text" id="oem_a_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$oem_a_qty;?>" name="oem_qty_a[<?php echo $product['buyback_product_id'];?>]" style="width:50px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')" />
															<input type="hidden" id="oem_a_price_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['oem_a_price'];?>" />
															<input type="hidden" name="total_oem_a_total[<?php echo $product['buyback_product_id'];?>]" id="total_oem_a_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo (float)$product['total_oem_a_total'];?>"  />
															<?php
														}
														else
														{
															echo $oem_a_qty;	
														}
														?>
													</td>
													<td align="center">
														<?php
														if($flag_change_qty)
														{
															?>
															<input type="text" id="oem_b_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$oem_b_qty;?>" name="oem_qty_b[<?php echo $product['buyback_product_id'];?>]" style="width:50px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')" />
															<input type="hidden" id="oem_b_price_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['oem_b_price'];?>" />
															<input type="hidden" name="total_oem_b_total[<?php echo $product['buyback_product_id'];?>]" id="total_oem_b_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo (float)$product['total_oem_b_total'];?>"  />
															<?php
														}
														else
														{
															echo $oem_b_qty;	
														}
														?>
													</td>
													<td align="center">
														<?php
														if($flag_change_qty)
														{
															?>
															<input type="text" id="oem_c_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$oem_c_qty;?>" name="oem_qty_c[<?php echo $product['buyback_product_id'];?>]" style="width:50px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')" />
															<input type="hidden" id="oem_c_price_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['oem_c_price'];?>" />
															<input type="hidden" name="total_oem_c_total[<?php echo $product['buyback_product_id'];?>]" id="total_oem_c_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo (float)$product['total_oem_c_total'];?>"  />
															<?php
														}
														else
														{
															echo $oem_c_qty;	
														}
														?>
													</td>
													<td align="center">
														<?php
														if($flag_change_qty)
														{
															?>
															<input type="text" id="oem_d_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$oem_d_qty;?>" name="oem_qty_d[<?php echo $product['buyback_product_id'];?>]" style="width:50px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')" />
															<input type="hidden" id="oem_d_price_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['oem_d_price'];?>" />
															<input type="hidden" name="total_oem_d_total[<?php echo $product['buyback_product_id'];?>]" id="total_oem_d_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo (float)$product['total_oem_d_total'];?>"  />
															<?php
														}
														else
														{
															echo $oem_d_qty;	
														}
														?>
													</td>
													<td align="center"><?php
														if($flag_change_qty)
														{
															?>
															<input type="text" id="non_oem_a_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$non_oem_a_qty;?>" name="non_oem_qty_a[<?php echo $product['buyback_product_id'];?>]" style="width:50px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')" />
															<input type="hidden" id="non_oem_a_price_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['non_oem_a_price'];?>" />
															<input type="hidden" name="total_non_oem_a_total[<?php echo $product['buyback_product_id'];?>]" id="total_non_oem_a_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo (float)$product['total_non_oem_a_total'];?>"  />
															<?php
														}
														else
														{
															echo $non_oem_a_qty;	
														}
														?>
													</td>
													<td align="center"><?php
														if($flag_change_qty)
														{
															?>
															<input type="text" id="non_oem_b_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$non_oem_b_qty;?>" name="non_oem_qty_b[<?php echo $product['buyback_product_id'];?>]" style="width:50px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')" />
															<input type="hidden" id="non_oem_b_price_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['non_oem_b_price'];?>" />
															<input type="hidden" name="total_non_oem_b_total[<?php echo $product['buyback_product_id'];?>]" id="total_non_oem_b_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo (float)$product['total_non_oem_b_total'];?>"  />
															<?php
														}
														else
														{
															echo $non_oem_b_qty;	
														}
														?>
													</td>
													<td align="center"><?php
														if($flag_change_qty)
														{
															?>
															<input type="text" id="non_oem_c_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$non_oem_c_qty;?>" name="non_oem_qty_c[<?php echo $product['buyback_product_id'];?>]" style="width:50px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')" />
															<input type="hidden" id="non_oem_c_price_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['non_oem_c_price'];?>" />
															<input type="hidden" name="total_non_oem_c_total[<?php echo $product['buyback_product_id'];?>]" id="total_non_oem_c_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo (float)$product['total_non_oem_c_total'];?>"  />
															<?php
														}
														else
														{
															echo $non_oem_c_qty;	
														}
														?>
													</td>
													<td align="center"><?php
														if($flag_change_qty)
														{
															?>
															<input type="text" id="non_oem_d_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$non_oem_d_qty;?>" name="non_oem_qty_d[<?php echo $product['buyback_product_id'];?>]" style="width:50px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')" />
															<input type="hidden" id="non_oem_d_price_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['non_oem_d_price'];?>" />
															<input type="hidden" name="total_non_oem_d_total[<?php echo $product['buyback_product_id'];?>]" id="total_non_oem_d_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo (float)$product['total_non_oem_d_total'];?>"  />
															<?php
														}
														else
														{
															echo $non_oem_d_qty;	
														}
														?>
													</td>

													<td align="center">
														<?php
														if($flag_change_qty)
														{
															?>
															<input type="text" id="salvage_qty_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$salvage_qty;?>" name="salvage_qty[<?php echo $product['buyback_product_id'];?>]" style="width:40px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')"  />
															<?php
														}
														else
														{
															echo $quantities['salvage_qty']; 
														}
														?>
													</td>

													<td align="center" style="display: none">
														<?php
														if($flag_change_qty)
														{
															?>
															<input type="text" id="unacceptable_qty_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$unacceptable_qty;?>" name="unacceptable_qty[<?php echo $product['buyback_product_id'];?>]" style="width:40px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')"  />
															<?php
														}
														else
														{
															echo $quantities['unacceptable_qty']; 
														}
														?>
													</td>

													<td align="center">
														<?php
														if($flag_change_qty)
														{
															?>
															<input type="text" id="rejected_qty_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$rejected_qty;?>" name="rejected_qty[<?php echo $product['buyback_product_id'];?>]" style="width:40px" onkeyup="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')"  />
															<?php
														}
														else
														{
															echo $quantities['rejected_qty']; 
														}
														?>
													</td>

													<td align="right" class="light-grey"><?php
														if($flag_change_qty)
														{
															?>
															<input type="text" data-received="<?php echo $product['total_received'];?>" id="total_qc_received_<?php echo $product['buyback_product_id'];?>" value="<?=(int)$product['total_qc_received'];?>" name="total_qc_received[<?php echo $product['buyback_product_id'];?>]" style="width:50px" />
															<span class="error"></span>
															<?php
														}
														else
														{
															echo $product['total_qc_received']; 
														}
														?></td>
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
													<td align="center" style="display:none">
														<?=$qc_unacceptable;?>
													</td>
													<td align="center">
														<?=$qc_rejected;?>
													</td>

													<td align="right"  class="light-grey"><strong><?php echo $qc_quantity_total;?></strong></td>
													<td align="center">-</td>
												</tr>


											</table>


										</td>
										<td valign="top">

											<?php
											if($_SESSION['login_as']=='admin'  and in_array($detail['status'],array('QC Completed','Completed')) )
											{
												$admin_data = true;
											}
											else
											{
												$admin_data = false;	
											}
											?>
											<?php if($admin_data) { ?>
											<h1 style="font-size:12px">Admin Data</h1>
											<table border="1" id="" cellpadding="10" cellspacing="0" width="70%">
												<?php } else { ?>
												<table border="1" id="" cellpadding="10" cellspacing="0" style="display: none;" width="70%">
													<?php } ?>
													

													<tr style="background-color:#999;font-weight:bold;font-size:10px">
														<th rowspan="2">LCD Type</th>
														<th colspan="4">OEM</th>
														<th colspan="4">Non-OEM</th>
														<th rowspan="2">Salvage</th>
														<th rowspan="2" style="display:none">Unacceptable</th>
														<th rowspan="2">Damaged</th>
														<th rowspan="2">Buyback Total</th>

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
													$admin_oem_a_total = 0.00;
													$admin_oem_b_total = 0.00;
													$admin_oem_c_total = 0.00;
													$admin_oem_d_total = 0.00;
													$admin_non_oem_a_total = 0.00;
													$admin_non_oem_b_total = 0.00;
													$admin_non_oem_c_total = 0.00;
													$admin_non_oem_d_total = 0.00;
													$admin_salvage_total = 0.00;
													foreach($products as $product)
													{
														if($product['data_type']!='customer' and $product['data_type']!='qc' and $product['data_type']!='admin') continue;


														$quantities = $db->func_query_first("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$product['buyback_product_id']."'");

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
														}

														if($product['admin_updated']=='1')
														{
															$oem_a_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_oem_a_qty']: $oem_a_qty;
															$oem_b_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_oem_b_qty']: $oem_b_qty;
															$oem_c_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_oem_c_qty']: $oem_c_qty;
															$oem_d_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_oem_d_qty']: $oem_d_qty;
															$non_oem_a_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_non_oem_a_qty']: $non_oem_a_qty;
															$non_oem_b_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_non_oem_b_qty']: $non_oem_b_qty;
															$non_oem_c_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_non_oem_c_qty']: $non_oem_c_qty;
															$non_oem_d_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_non_oem_d_qty']: $non_oem_d_qty;
															$salvage_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_salvage_qty']: $salvage_qty;
															$unacceptable_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_unacceptable']: $unacceptable_qty;
															$rejected_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_rejected']: $rejected_qty;
														}

														$admin_oem_a_total+=(int)$oem_a_qty * (float)$product['oem_a_price'];
														$admin_oem_b_total+=(int)$oem_b_qty * (float)$product['oem_b_price'];
														$admin_oem_c_total+=(int)$oem_c_qty * (float)$product['oem_c_price'];
														$admin_oem_d_total+=(int)$oem_d_qty * (float)$product['oem_d_price'];
														$admin_non_oem_a_total+=(int)$non_oem_a_qty * (float)$product['non_oem_a_price'];
														$admin_non_oem_b_total+=(int)$non_oem_b_qty * (float)$product['non_oem_b_price'];
														$admin_non_oem_c_total+=(int)$non_oem_c_qty * (float)$product['non_oem_c_price'];
														$admin_non_oem_d_total+=(int)$non_oem_d_qty * (float)$product['non_oem_d_price'];
														$admin_salvage_total+=(int)$salvage_qty * (float)$product['salvage_price'];

														$admin_total = ($oem_a_qty * $product['oem_a_price']) + ($oem_b_qty * $product['oem_b_price']) + ($oem_c_qty * $product['oem_c_price']) + ($oem_d_qty * $product['oem_d_price']) + ($non_oem_a_qty * $product['non_oem_a_price']) + ($non_oem_b_qty * $product['non_oem_b_price']) + ($non_oem_c_qty * $product['non_oem_c_price']) + ($salvage_qty * $product['salvage_price']);

														$admin_combine_total+=(float)$admin_total;
														?>

														<tr data-total-qty="<?php echo $product['total_received']; ?>" data-oem-a-price="<?=$product['oem_a_price'];?>" data-salvage-price="<?=$product['salvage_price'];?>" data-oem-b-price="<?=$product['oem_b_price'];?>" data-oem-c-price="<?=$product['oem_c_price'];?>" data-oem-d-price="<?=$product['oem_d_price'];?>" data-non-oem-a-price="<?=$product['non_oem_a_price'];?>" data-non-oem-b-price="<?=$product['non_oem_b_price'];?>" data-non-oem-c-price="<?=$product['non_oem_c_price'];?>" data-non-oem-d-price="<?=$product['non_oem_d_price'];?>">
															<td><?=$product['description'];?></td>

															<td align="">
																<?php
																//	if($admin_data) {
																?>
																<input type="text" style="width:25px" onblur="changeAdminQty('<?=$product['buyback_product_id'];?>', this)" class="admin_qty admin_qty<?php echo $product['buyback_product_id']; ?>" data-price="<?php echo $product['oem_a_price'];?>"	 id="admin_oem_a_qty_<?php echo $product['buyback_product_id'];?>" name="admin_oem_a_qty[<?php echo $product['buyback_product_id'];?>]" value="<?php echo $oem_a_qty;?>" /><br><small style="font-size:9px"> x <?php echo $product['oem_a_price'];?></small>
																<?php
																	//} else {
																		//echo (int)$oem_a_qty.' x '.$product['oem_a_price'];	
																//	}
																?>
															</td>
															<td align="">
																<?php
																	//if($admin_data) {
																?>
																<input type="text" style="width:25px" onblur="changeAdminQty('<?=$product['buyback_product_id'];?>', this)" class="admin_qty admin_qty<?php echo $product['buyback_product_id']; ?>" data-price="<?php echo $product['oem_b_price'];?>"	 id="admin_oem_b_qty_<?php echo $product['buyback_product_id'];?>" name="admin_oem_b_qty[<?php echo $product['buyback_product_id'];?>]" value="<?php echo $oem_b_qty;?>" /><br><small style="font-size:9px"> x <?php echo $product['oem_b_price'];?></small>
																<?php
																	//} else {
																	//	echo (int)$oem_b_qty.' x '.$product['oem_b_price'];	
																//	}
																?>
															</td>
															<td align="">
																<?php
																	//if($admin_data) {
																?>
																<input type="text" style="width:25px" onblur="changeAdminQty('<?=$product['buyback_product_id'];?>', this)" class="admin_qty admin_qty<?php echo $product['buyback_product_id']; ?>" data-price="<?php echo $product['oem_c_price'];?>"	 id="admin_oem_c_qty_<?php echo $product['buyback_product_id'];?>" name="admin_oem_c_qty[<?php echo $product['buyback_product_id'];?>]" value="<?php echo $oem_c_qty;?>" /><br><small style="font-size:9px"> x <?php echo $product['oem_c_price'];?></small>
																<?php
																	//} else {
																	//	echo (int)$oem_c_qty.' x '.$product['oem_c_price'];	
																	//}
																?>
															</td>
															<td align="">
																<?php
																	//if($admin_data) {
																?>
																<input type="text" style="width:25px" onblur="changeAdminQty('<?=$product['buyback_product_id'];?>', this)" class="admin_qty admin_qty<?php echo $product['buyback_product_id']; ?>" data-price="<?php echo $product['oem_d_price'];?>"	 id="admin_oem_d_qty_<?php echo $product['buyback_product_id'];?>" name="admin_oem_d_qty[<?php echo $product['buyback_product_id'];?>]" value="<?php echo $oem_d_qty;?>" /><br><small style="font-size:9px"> x <?php echo $product['oem_d_price'];?></small>
																<?php
																//	} else {
																	//	echo (int)$oem_d_qty.' x '.$product['oem_d_price'];	
																//	}
																?>
															</td>
															<td align="">
																<?php
																//	if($admin_data){
																?>
																<input type="text" class="admin_qty admin_qty<?php echo $product['buyback_product_id']; ?>" data-price="<?php echo $product['non_oem_a_price'];?>" style="width:25px" onblur="changeAdminQty('<?=$product['buyback_product_id'];?>', this)" id="admin_non_oem_a_qty_<?php echo $product['buyback_product_id'];?>" name="admin_non_oem_a_qty[<?php echo $product['buyback_product_id'];?>]" value="<?php echo $non_oem_a_qty;?>" /> <br><small style="font-size:9px">x <?php echo $product['non_oem_a_price'];?></small>
																<?php
																//	}
																//	else
																//	{

																//		echo (int)$non_oem_a_qty.' x '.$product['non_oem_a_price'];	
																//	}

																?>
															</td>
															<td align="">
																<?php
																	//if($admin_data)
																	//{
																?>
																<input type="text" class="admin_qty admin_qty<?php echo $product['buyback_product_id']; ?>" data-price="<?php echo $product['non_oem_b_price'];?>" style="width:25px" onblur="changeAdminQty('<?=$product['buyback_product_id'];?>', this)" id="admin_non_oem_b_qty_<?php echo $product['buyback_product_id'];?>" name="admin_non_oem_b_qty[<?php echo $product['buyback_product_id'];?>]" value="<?php echo $non_oem_b_qty;?>" /> <br><small style="font-size:9px">x <?php echo $product['non_oem_b_price'];?></small>
																<?php
																	//}
																	//else
																	//{

																	//	echo (int)$non_oem_b_qty.' x '.$product['non_oem_b_price'];	
																	//}

																?>
															</td>
															<td align="">
																<?php
																	//if($admin_data)
																	//{
																?>
																<input type="text" class="admin_qty admin_qty<?php echo $product['buyback_product_id']; ?>" data-price="<?php echo $product['non_oem_c_price'];?>" style="width:25px" onblur="changeAdminQty('<?=$product['buyback_product_id'];?>', this)" id="admin_non_oem_c_qty_<?php echo $product['buyback_product_id'];?>" name="admin_non_oem_c_qty[<?php echo $product['buyback_product_id'];?>]" value="<?php echo $non_oem_c_qty;?>" /> <br><small style="font-size:9px">x <?php echo $product['non_oem_c_price'];?></small>
																<?php
																	//}
																//	else
																//	{

																	//	echo (int)$non_oem_c_qty.' x '.$product['non_oem_c_price'];	
																//	}

																?>
															</td>
															<td align="">
																<?php
																	//if($admin_data)
																	//{
																?>
																<input type="text" class="admin_qty admin_qty<?php echo $product['buyback_product_id']; ?>" data-price="<?php echo $product['non_oem_d_price'];?>" style="width:25px" onblur="changeAdminQty('<?=$product['buyback_product_id'];?>', this)" id="admin_non_oem_d_qty_<?php echo $product['buyback_product_id'];?>" name="admin_non_oem_d_qty[<?php echo $product['buyback_product_id'];?>]" value="<?php echo $non_oem_d_qty;?>" /> <br><small style="font-size:9px">x <?php echo $product['non_oem_d_price'];?></small>
																<?php
																	//}
																//	else
																	//{

																	//	echo (int)$non_oem_d_qty.' x '.$product['non_oem_d_price'];	
																//	}

																?>
															</td>
															<td align="">
																<?php
																	//if($admin_data)
																//	{
																?>
																<input type="text" class="admin_qty admin_qty<?php echo $product['buyback_product_id']; ?>" data-price="0.00" style="width:25px" onblur="changeAdminQty('<?=$product['buyback_product_id'];?>', this)" id="admin_salvage_qty_<?php echo $product['buyback_product_id'];?>" name="admin_salvage_qty[<?php echo $product['buyback_product_id'];?>]" value="<?php echo $salvage_qty;?>" /> <br><small style="font-size:9px">x <?php echo $product['salvage_price'];?></small>
																<?php
																//	}
																	//else
																//	{

																	//	echo (int)$salvage_qty.' x ' . $product['salvage_price'];
																//	}

																?>
															</td>
															<td align="" style="display:none">
																<?php
																	//if($admin_data)
																	//{
																?>
																<input type="text" class="admin_qty admin_qty<?php echo $product['buyback_product_id']; ?>" data-price="0.00" style="width:25px" onblur="changeAdminQty('<?=$product['buyback_product_id'];?>', this)" id="admin_unacceptable_<?php echo $product['buyback_product_id'];?>" name="admin_unacceptable[<?php echo $product['buyback_product_id'];?>]" value="<?php echo $unacceptable_qty;?>" /> <br><small style="font-size:9px">x 0.00</small>
																<?php
																	//}
																	//else
																	//{

																	//	echo (int)$unacceptable_qty.' x 0.00';
																//	}

																?>
															</td>
															<td align="">
																<?php
																//	if($admin_data)
																	//{
																?>
																<input type="text" class="admin_qty admin_qty<?php echo $product['buyback_product_id']; ?>" data-price="0.00" style="width:25px" onblur="changeAdminQty('<?=$product['buyback_product_id'];?>', this)" id="admin_rejected_<?php echo $product['buyback_product_id'];?>" name="admin_rejected[<?php echo $product['buyback_product_id'];?>]" value="<?php echo $rejected_qty;?>" /> <br><small style="font-size:9px">x 0.00</small>
																<?php
																//	}
																//	else
																//	{
//
																//		echo (int)$rejected_qty.' x 0.00';
																	//}

																?>
															</td>
															<td align="center" style="padding-left: 2px; padding-right: 2px;">
																<?php
																	//if($admin_data)
																	//{

																?>
																$<input type="text" style="width:40px;" id="admin_total_total_<?php echo $product['buyback_product_id'];?>" name="admin_total_total[<?php echo $product['buyback_product_id'];?>]" value="<?php echo (float)$admin_total;?>" readonly="readonly" />
																<?php
																	//}
																	//else{
																	//	echo '$'.number_format($admin_total,2);	
																//	}
																?>
																<span class="error<?php echo $product['buyback_product_id']; ?>"></span>
															</td>



														</tr>

														<?php
													}
													?>

													<tr class="light-grey">
														<td colspan="11"></td>
														<td align="center" style="padding-left: 2px; padding-right: 2px;">
															<?php
																//if($admin_data)
																//{
															?>
															$<input type="text" readOnly class="light-grey" style="width:50px" id="admin_combine_total" value="<?php echo $admin_combine_total;?>" />
															<script type="text/javascript">
																function newPrice (t) {
																	$('.issueCredit').attr('href', $('.issueCredit').attr('href') + '&amount=' + $(t).val());
																}
															</script>
															<?php
															//	}
															//	else
															//	{
															//		echo '$'.number_format($admin_combine_total,2);	
																//}
															?>
														</td>
													</tr>

												</table>
											</td>
										</tr>
										<tr>
											<td colspan="2" align="center">

												<h1 style="font-size:12px;display:none" >Reject Totals</h1>
												<table border="1"  cellpadding="10" cellspacing="0" width="20%" style="display:none">
													<tr style="background-color:#999;font-weight:bold;font-size:10px">
														<th>LCD Type</th>
														<th>Reject</th>

													</tr>
													<?php
													$admin_rejected_total = 0.00;
													foreach($products as $product)
													{
														$admin_rejected_total+=$product['total_rejected_total'];
														$reject_items = array();
														foreach($rejected_items as $key => $value)
														{	
															$reject_items[] = $value['buyback_product_id'];
														}
	//print_r($reject_items);exit;
														if(!in_array($product['buyback_product_id'],$reject_items)) continue;
														?>
														<tr>
															<td><?=$product['description'];?></td>
															<td>
																<?php
																if($admin_data)
																{
																	?>
																	<input class="admin_rejected_total" type="text" style="width:50px" value="<?php echo $product['total_rejected_total'];?>" id="total_rejected_total_<?php echo $product['buyback_product_id'];?>" name="total_rejected_total[<?php echo $product['buyback_product_id'];?>]" /><?php
																}
																else
																{
																	echo '$'.number_format($product['total_rejected_total'],2);	
																}
																?>

															</tr>
															<?php	
														}
														?>
														<tr class="light-grey">
															<td></td>
															<td>
																<?php
																if($admin_data)
																{
																	?>
																	<input type="text" class="light-grey" readOnly value="<?php echo $admin_rejected_total;?>" id="admin_rejected_total" style="width:50px" />
																	<?php
																}
																else
																{
																	echo '$'.number_format($admin_rejected_total,2);	
																}

																?>
															</td>
														</tr>
													</table>
												</td>

											</tr>
										</table>
										<br />
										<br /><br />
										<?php if ($flag_received_qty): ?>
											<input type="submit" name="received" value="Received" onclick="" class="button" style="display:none" />
											<a class="fancybox2 fancybox.iframe button" href="shipment_received.php?buyback_id=<?php echo $detail['buyback_id'];?>">Received</a> 
										<?php endif; ?>



										<?php if ( in_array($detail['status'], array('Received'))): ?>
											<?php
											if($_SESSION['buyback_qc_shipments'])
											{
												?>
												Printer:
												<select name="printerid" id="printerid">
													<option value="">Do not print</option>
													<?php foreach ($printers as $printer): ?>
														<option value="<?php echo $printer['id'] ?>" <?php if ($printer['id'] == $return_item['printer']): ?> selected="selected" <?php endif; ?>>
															<?php echo $printer['value'] ?>
														</option>
													<?php endforeach; ?>
												</select>

												<br>
												<br>
												<input type="submit" name="qcdone" value="Complete QC" class="button"
												<?php
					echo ($detail['is_approved']==0?'disabled style="background-color:grey;border-color:grey"':'')
					?>
												 />
												<?php
											}
											?>
										<?php endif; ?>		

										<?php if ($detail['status'] == 'QC Completed'): ?>	

											<a href="<?php echo $host_path;?>buyback/reprint_labels.php?shipment=<?php echo $shipment_number; ?>" class="button fancybox3 fancybox.iframe" />Reprint Labels</a>

											<input type="submit" name="completed" value="Complete Shipment" class="button" />
											<?php if (!$v_shipment) {?>
											<!-- <input type="submit" name="make_shipment" value="Create Vendor Shipment" class="button" /> -->
											<?php } ?>
										<?php endif; ?>	



										<input type="submit" name="save" value="Save" class="button" />


										<br style="clear:both" /><br />
										<?php
										if($detail['status']=='Completed' and $rejected_items and $detail['for_shipstation']==0)
										{
											?>
											<a href="create_return_shipment.php?buyback_id=<?php echo $detail['buyback_id'];?>&rejected_items=<?php echo base64_encode(json_encode($rejected_items));?>&zone=<?=$zone;?>" id="create_return_shipment"  class="button fancybox2 fancybox.iframe "  > Create Return Shipment</a>
											<?php	

										}
										?>
										<?php
										if($detail['payment_type']=='store_credit' and $detail['status']=='QC Completed')
										{
											$checkQuery= $db->func_query("SELECT * FROM inv_buyback_payments WHERE buyback_id='".$detail['buyback_id']."'");
											if($checkQuery)
											{

											}
											else
											{
												?>
												<a class="fancybox2 fancybox.iframe button issueCredit" href="issue_credit.php?buyback_id=<?php echo $detail['buyback_id'];?>&firstname=<?php echo base64_encode($firstname);?>&lastname=<?php echo base64_encode($lastname);?>&email=<?php echo base64_encode($email);?>&amount=<?php echo $admin_combine_total;?>">Issue Store Credit</a>  
												<?php
											}
										}
										?>


										<?php
										if($detail['payment_type']=='cash' and $detail['status']=='QC Completed')
										{
											$checkQuery= $db->func_query("SELECT * FROM inv_buyback_payments WHERE buyback_id='".$detail['buyback_id']."'");
											if($checkQuery)
											{

											}
											else
											{
												?>
												<a class="fancybox2 fancybox.iframe button issueCredit" href="issue_cash.php?buyback_id=<?php echo $detail['buyback_id'];?>&firstname=<?php echo base64_encode($firstname);?>&lastname=<?php echo base64_encode($lastname);?>&email=<?php echo base64_encode($email);?>">Issue Payment</a>  
												<?php
											}
										}
										?>
										<input type="hidden" name="shipment" value="<?php echo $shipment_number;?>" />
										<input type="hidden" id="shipping_method" value="">
										
									</form>

									<br />
									
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
<script type="text/javascript">
	$(document).ready(function () {
		if ($(".carrier").val() == 'In House') {
			$(".tracker").removeAttr('required');
		} else if ($(".change_status").val() == 'Received') {
			$(".tracker").attr('required','');
		}

		
		$( ".carrier" ).change(function() {
			if ($(".carrier").val() == 'In House') {
				$(".tracker").removeAttr('required');
			} else if ($(".change_status").val() == 'Received') {
				$(".tracker").attr('required','');
			}
		});
	});

	function approveIt()
{
	if(!confirm('Are you sure want to approve this LBB?'))
	{
		return false;

	}


		$.ajax({
			url: "shipment_detail.php",
			type: "POST",
			data: {action:'approve_it',shipment:'<?=$shipment_number;?>',is_approved:1},
			beforeSend: function () {
				$('#approve_it').attr('disabled','disabled');

							$('#approve_it').next('span').html('<img src="images/loading.gif" width="15" height="15">');
						},
			complete: function () {
				$('#approve_it').next('span').html('');
				$('#approve_it').removeAttr('disabled');
			},
			success: function (data) {
										//alert(data);
										// alert('Saved!');
										location.reload(true);
									}
								});


}
</script>

</html>
