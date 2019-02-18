<?php
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
require_once("auth.php");
require_once("inc/functions.php");
$rma_number = $db->func_escape_string($_REQUEST['rma_number']);
$returns=$db->func_query_first("SELECT * FROM inv_returns WHERE rma_number='$rma_number'");
//print_r($returns['store_type']);exit;
//$ret_id=$detail['rma_number'];
$qc_id=$db->func_query_first("SELECT auth_qc FROM inv_returns WHERE id='".$returns[id]."'");
$manager_id=$db->func_query_first("SELECT auth_manager FROM inv_returns WHERE id='".$returns[id]."'");
$qc_name=$db->func_query_first("SELECT name FROM inv_users WHERE id='".$qc_id[auth_qc]."'");
$manager_name=$db->func_query_first("SELECT name FROM inv_users WHERE id='".$manager_id[auth_manager]."'");
if (!$rma_number) {
	header("Location:$host_path/manage_returns.php");
	exit;
}
if($_POST['action']=='update_item_price')
{
	$item_id = (int)$_POST['item_id'];
	$price = (float)$_POST['price'];
	$old_item = $db->func_query_first("SELECT price FROM inv_return_items WHERE id='".$item_id."'");
	if($old_item)
	{
	$db->db_exec("UPDATE inv_return_items SET price='".$price."' WHERE id='".$item_id."' AND return_id='".$returns['id']."'");

	$addcomment = array();
	$addcomment['comment_date'] = date('Y-m-d H:i:s');
	$addcomment['user_id'] = $_SESSION['user_id'];
	$addcomment['comments'] = 'Price changed from '.$old_item['price'].' to '.$price;
	$addcomment['return_id'] = $returns['id'];
	$db->func_array2insert("inv_return_comments", $addcomment);
	}
	echo 'success';exit;
}
if($_POST['action']=='void_shipment')
{
	$detail = $db->func_query_first("SELECT * FROM inv_returns WHERE rma_number='$rma_number'");
	$do_void = true;
	include_once '../shipstation/create_rma_label.php';
	if($is_voided)
	{
		$db->db_exec("UPDATE inv_returns SET is_voided=1 WHERE rma_number='".$detail['rma_number']."'");
		echo 'success';
	}
	else
	{
		echo 'failed';
	}
	exit;
}
//upload return item item images
if ($_FILES['image_path']['tmp_name']) {
	foreach ($_POST['item_condition'] as $return_item_id => $condition) {
		if ($condition == 'Item Issue' && $_POST['item_issue'][$return_item_id]) {
            //check exist
			$isExist = $db->func_query_first("select id from  inv_product_issues where product_sku = '" . $_POST['product_sku'][$return_item_id] . "' and item_id = '$return_item_id' and issue_from = 'returns'");
			if (!$isExist) {
				$itemIssue = array();
				$itemIssue['username'] = $_SESSION['login_as'];
				$itemIssue['product_sku'] = $_POST['product_sku'][$return_item_id];
				$itemIssue['item_issue'] = $_POST['item_issue'][$return_item_id];
				$itemIssue['issue_from'] = 'returns';
				$itemIssue['shipment_id'] = $_POST['return_id'];
				$itemIssue['item_id'] = $return_item_id;
				$itemIssue['date_added'] = date('Y-m-d H:i:s');
				$product_issue_id = $db->func_array2insert("inv_product_issues", $itemIssue);
			} else {
				$product_issue_id = $isExist['id'];
                //$db->db_exec("");
				$db->db_exec("update inv_product_issues SET item_issue = '" . $_POST['item_issue'][$return_item_id] . "' where product_sku = '" . $_POST['product_sku'][$return_item_id] . "' and item_id = '$return_item_id' and issue_from = 'returns'");
			}
		}
		if ($condition == 'Item Issue - RTV' && $_POST['item_issue_rtv_'][$return_item_id]) {
            //check exist
			$isExist = $db->func_query_first("select id from  inv_product_issues where product_sku = '" . $_POST['product_sku'][$return_item_id] . "' and item_id = '$return_item_id' and issue_from = 'returns'");
			if (!$isExist) {
				$itemIssue = array();
				$itemIssue['username'] = $_SESSION['login_as'];
				$itemIssue['product_sku'] = $_POST['product_sku'][$return_item_id];
				$itemIssue['item_issue'] = $_POST['item_issue_rtv_'][$return_item_id];
				$itemIssue['issue_from'] = 'returns';
				$itemIssue['shipment_id'] = $_POST['return_id'];
				$itemIssue['item_id'] = $return_item_id;
				$itemIssue['date_added'] = date('Y-m-d H:i:s');
				$product_issue_id = $db->func_array2insert("inv_product_issues", $itemIssue);
			} else {
				$product_issue_id = $isExist['id'];
                //$db->db_exec("");
				$db->db_exec("update inv_product_issues SET item_issue = '" . $_POST['item_issue'][$return_item_id] . "' where product_sku = '" . $_POST['product_sku'][$return_item_id] . "' and item_id = '$return_item_id' and issue_from = 'returns'");
			}
		}
	}
	$imageCount = 0;
	$isDenied = false;
	foreach ($_FILES['image_path']['tmp_name'] as $return_item_id => $files) {
		$count = count($files);
		for ($i = 0; $i < $count; $i++) {
			if($_FILES['image_path']['size'][$return_item_id][$i]<'4145750'){
				$uniqid = uniqid();
				$destination = "images/returns/" . $uniqid . ".jpg";
				$destination_thumb = "images/returns/" . $uniqid . "_thumb.jpg";
				if (move_uploaded_file($files[$i], $destination)) {
					resizeImage($destination, $destination_thumb, 50, 50);
					if ($_POST['decision'][$return_item_id] != 'Denied') {
						resizeImage($destination, $destination, 2000, 4000);
						$isDenied = false;
					}
					if ($_POST['decision'][$return_item_id] == 'Denied') {
						$isDenied = true;
					}
					$itemImage = array();
					$itemImage['image_path'] = $destination;
					$itemImage['thumb_path'] = $destination_thumb;
					$itemImage['date_added'] = date('Y-m-d H:i:s');
					$itemImage['user_id'] = $_SESSION['user_id'];
					$itemImage['return_item_id'] = $return_item_id;
					$image_id = $db->func_array2insert("inv_return_item_images", $itemImage);
					$imageCount++;
					if ($_POST['item_condition'][$return_item_id] == 'Item Issue') {
	                    //insert into product issue images
						$itemImage = array();
						$itemImage['image_path'] = $destination;
						$itemImage['thumb_path'] = $destination_thumb;
						$itemImage['image_id'] = $image_id;
						$itemImage['product_issue_id'] = $product_issue_id;
						$db->func_array2insert("inv_product_issue_images", $itemImage);
					}
				}
			} 
		}
	}
	if ($imageCount > 0) {
		$rma_return = $db->func_query_first("select r.* ,o.email, o.order_date, od.first_name,od.last_name,od.address1,od.address2,od.payment_method,
			od.city,od.state,od.zip,od.country,od.phone_number, o.store_type,o.order_status
			from inv_returns r 
			inner join inv_orders o on (r.order_id = o.order_id) 
			inner join inv_orders_details od on (r.order_id = od.order_id)
			where rma_number  = '$rma_number'");
		$images = $db->func_query("SELECT c.* FROM  inv_returns a INNER JOIN `inv_return_items` b ON (a.`id` = b.`return_id`)
			INNER JOIN `inv_return_item_images` c
			ON (b.`id` = c.`return_item_id`) WHERE a.id='" . $rma_return['id'] . "'");
		$_SESSION['message'] = "Image Uploaded.";
		header("Location:return_detail.php?rma_number=$rma_number");
		exit;
	}
}
//delete item images
if ($_GET['action'] == 'remove' && $_GET['image_id']) {
	$return_item_id = (int) $_GET['return_item_id'];
	$db->db_exec("delete from inv_return_item_images where return_item_id = '$return_item_id' and id = '" . (int) $_GET['image_id'] . "'");
	$db->db_exec("delete from inv_product_issue_images where image_id = '" . (int) $_GET['image_id'] . "'");
	$_SESSION['message'] = "Image Deleted.";
	header("Location:return_detail.php?rma_number=$rma_number");
	exit;
}
//add comments
if (isset($_POST['addcomment'])) {
	$addcomment = array();
	$addcomment['comment_date'] = date('Y-m-d H:i:s');
	$addcomment['user_id'] = $_SESSION['user_id'];
	$addcomment['comments'] = $db->func_escape_string($_POST['comments']);
	$addcomment['return_id'] = $_POST['return_id'];
	$db->func_array2insert("inv_return_comments", $addcomment);
	$_SESSION['message'] = "New comment is added.";
	header("Location:$host_path/return_detail.php?rma_number=$rma_number");
	exit;
}
//Tracking Number and Carrier Update Gohar
$tracking_number = $db->func_query_first_cell("SELECT tracking_number FROM inv_returns WHERE rma_number='".$rma_number."'");
$carrier = $db->func_query_first_cell("SELECT carrier FROM inv_returns WHERE rma_number='".$rma_number."'");
$is_easypost = $db->func_query_first_cell("SELECT is_easypost FROM inv_returns WHERE rma_number='".$rma_number."'");
if($tracking_number!=$_POST['tracking_number'])
{
	$db->db_exec("UPDATE inv_returns SET tracking_number='".$_POST['tracking_number']."' WHERE id='".(int)$_POST['return_id']."'");
}
if($carrier!=$_POST['carrier'])
{
	$db->db_exec("UPDATE inv_returns SET carrier='".$_POST['carrier']."' WHERE id='".(int)$_POST['return_id']."'");
}
//Hit to Add tracker
if($tracking_number!='' && strlen($tracking_number)>5 && $carrier!='' && $carrier!='In House' && $is_easypost != 1) {
    
	global $host_path;
	$post = array(
			"tracking_number" => $tracking_number,
			"shipment_number" => $rma_number,
			"carrier" => $carrier);
	$ch = curl_init($host_path . 'easypost/tracker_api.php');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$response = curl_exec($ch);
	
	curl_close($ch);
	$response = json_decode($response);
	if ($response->success) 
	{
		//$db->db_exec("UPDATE inv_returns SET is_easypost='1' WHERE rma_number='".$rma_number."'");
	}
	
}
if (isset($_POST['save']) || isset($_POST['completed']) || isset($_POST['qcdone'])) {
	if($_POST['change_status'] and $_POST['save'])
	{
		$rma_status = $db->func_query_first_cell("SELECT rma_status FROM inv_returns WHERE id='".(int)$_POST['return_id']."'");
		if($rma_status!=$_POST['change_status'])
		{
			$db->db_exec("UPDATE inv_returns SET rma_status='".$_POST['change_status']."' WHERE id='".(int)$_POST['return_id']."'");
			actionLog('Force RMA Status is changed from '.$rma_status.' to '.$_POST['change_status']);
		}
	}
    //update sku if changed
	$return_id = $_POST['return_id'];
	$printer = $db->func_escape_string($_POST['printerid']);
	foreach ($_POST['product_sku'] as $return_item_id => $product_sku) {
		$return_items = array();
		$returned_item = $db->func_query_first("SELECT * FROM inv_return_items WHERE id = '$return_item_id' AND return_id = '$return_id'");
		if ($product_sku != $_POST['new_sku'][$return_item_id]) {
			$return_items['sku'] = $_POST['new_sku'][$return_item_id];
			$return_items['title'] = $db->func_query_first_cell("select name from oc_product_description where product_id = (select product_id from oc_product where sku = '" . $_POST['new_sku'][$return_item_id] . "' limit 1)");
		}
		if($_POST['restocking'][$return_item_id])
		{
			$return_items['restocking'] = 1;
			$return_items['restocking_grade'] = $_POST['restocking_grade'][$return_item_id];
			$return_items['discount_amount'] = (float)$_POST['discount_amount'][$return_item_id];
			$return_items['discount_per'] = (int)$_POST['discount_per'][$return_item_id];
			$db->db_exec("INSERT INTO inv_return_comments SET comment_date='".date('Y-m-d H:i:s')."',user_id='".$_SESSION['user_id']."',comments='".$product_sku." - ($".number_format($return_items['discount_amount'],2).") ".$return_items['discount_per']."% restocking fee assessed',return_id='".$_POST['return_id']."',sku='".$product_sku."'");		
		}
		else
		{
			$return_items['restocking'] = 0;
			$return_items['restocking_grade'] = '';	
			$return_items['discount_amount'] = 0.00;
			$return_items['discount_per'] = 0;
		}
        //$return_items['returnable'] = $_POST['returnable'][$return_item_id];
		$return_items['item_condition'] = $_POST['item_condition'][$return_item_id];
		$return_items['item_exception'] = ($_POST['item_exception'][$return_item_id]?1:0);
		$return_items['how_to_process'] = $_POST['how_to_process'][$return_item_id];
		$return_items['comment'] = $db->func_escape_string($_POST['return_comment'][$return_item_id]);
		$return_items['qc_comment'] = $db->func_escape_string($_POST['return_comment_qc'][$return_item_id]);
		// $return_items['printer'] = $db->func_escape_string($_POST['printer'][$return_item_id]);
		$return_items['printer'] = $printer;
		if($_POST['item_condition'][$return_item_id] == 'Item Issue - RTV'){		
		$return_items['item_issue'] = $_POST['item_issue_rtv_'][$return_item_id];
		$return_items['rtv_vendor_id'] = $_POST['item_vendor_rtv_'][$return_item_id];
		$return_items['rtv_shipment_id'] = $_POST['vendor_rtv_ship_'][$return_item_id];
		$return_items['production_date'] = $_POST['production_date_'][$return_item_id];
		} else {
		$return_items['item_issue'] = $_POST['item_issue'][$return_item_id];	
		}
		
		$return_items['rtv_vendor_id'] = $_POST['item_vendor_rtv_'][$return_item_id];
		
		$return_items['price'] = $_POST['product_price'][$return_item_id];
		$return_items['add_to_box'] = $_POST['add_to_box'][$return_item_id];
		if ($_SESSION['return_decision'] and $_POST['decision_save'] == 1) {
			$return_items['decision'] = $_POST['decision'][$return_item_id];
		}
		$return_email = $db->func_escape_string($db->func_query_first_cell("SELECT email FROM inv_returns WHERE id='".(int)$return_id."'"));
		if(!isset($_POST['item_exception'][$return_item_id]))
		{
			$db->db_exec("DELETE FROM inv_exception_list WHERE return_id='".(int)$return_id."' AND sku='".$product_sku."' AND email='".$return_email."'");
		}
		else
		{
			$check_exception = $db->func_query_first("SELECT * FROM inv_exception_list WHERE return_id='".(int)$return_id."' AND sku='".$product_sku."' AND email='".$return_email."' ");	
			if(!$check_exception)
			{
				$db->db_exec("INSERT INTO inv_exception_list SET return_id='".$return_id."',sku='".$product_sku."',date_added='".date("Y-m-d H:i:s")."',email='".$return_email."'");	
			}
		}
		if ($returned_item['item_condition'] != $return_items['item_condition']) {
			if ($returned_item['item_condition']) {
				$logQC .= '<br><br> Product ' . linkToProduct($product_sku) . ' <br>Condition is updated from '. $returned_item['item_condition'] .' to ' . $return_items['item_condition'] . (($returned_item['item_issue']) ? $returned_item['item_issue']: '');
			} else {
				$logQC .= '<br><br> Product ' . linkToProduct($product_sku) . ' <br>Condition: ' . $return_items['item_condition'] . (($returned_item['item_issue']) ? $returned_item['item_issue']: '');
			}
		}
		if ($return_items['decision']) {
			if ($returned_item['decision'] != $return_items['decision'] ) {
				if ($returned_item['decision']) {
					$logQC .= '<br> Decision is Updated from "'. $returned_item['decision'] .'" to "' . $return_items['decision'] . '"';
				} else {
					$logQC .= '<br> Decision: "' . $return_items['decision'] . '"';
				}
			}
		}
		$db->func_array2update("inv_return_items", $return_items, "id = '$return_item_id' AND return_id = '$return_id'");
	}
	$source = $db->func_escape_string($_POST['source']);
	$shipping_cost = $db->func_escape_string($_POST['shipping_cost']);
	$db->db_exec("UPDATE inv_returns SET shipping_cost='".$shipping_cost."' WHERE id='".(int)$_POST['return_id']."'");
	if (isset($_POST['qcdone'])) {
		//testobject($_POST);
		
		$db->db_exec("update inv_returns SET rma_status = 'In QC' , source = '$source' , date_qc = '" . date('Y-m-d H:i:s') . "' where rma_number = '$rma_number'");
		$transfer_return = $db->func_query ( "select * from inv_returns where po_created = 0 and rma_number = '$rma_number'" );
		if ($transfer_return) {
			$returns_po_items = array ();
			$return_shipment_box_items = array ();
			foreach ($transfer_return as $return ) {
				$transfer_return_id = $return ['id'];
				$transfer_return_items = $db->func_query ( "select * from inv_return_items where return_id = '$transfer_return_id'" );
				if (! $transfer_return_items) {
					continue;
				}
				foreach ( $transfer_return_items as $return_item ) {
					if (!$return_item['add_to_box']) {
						continue;
					}
					$return_item ['order_id'] = $return ['order_id'];
					$return_item ['rma_number'] = $return ['rma_number'];
					$return_item ['source'] = $return ['source'];
					$return_item ['rma_creation_date'] = $return ['date_added'];
					if ($return_item ['item_condition'] == 'Good For Stock') {
						$return_shipment_box_items ['GFSBox'] [] = $return_item;
					}
					elseif ($return_item ['item_condition'] == 'Item Issue') {
						$return_shipment_box_items ['ItemIssueBox'] [] = $return_item;
					}
					elseif ($return_item ['item_condition'] == 'Item Issue - RTV') {
						$return_shipment_box_items ['RTV'] [] = $return_item;
					}
					elseif ($return_item ['item_condition'] == 'Not Tested') {
						$return_shipment_box_items ['NotTestedBox'] [] = $return_item;
					}
					elseif ($return_item ['item_condition'] == 'Customer Damage' || $return_item ['item_condition'] == 'Over 60 days' || $return_item ['item_condition'] == 'Not PPUSA Part') {
						$return_shipment_box_items ['Customer Damage'] [] = $return_item;
					}
					elseif ($return_item ['item_condition'] == 'Shipping Damage') {
						$return_shipment_box_items ['SDBox'] [] = $return_item;
					}
					elseif ($return_item ['item_condition'] == 'Need To Repair') {
						$return_shipment_box_items ['NTRBox'] [] = $return_item;
					}
				}
				$db->db_exec ( "update inv_returns SET po_created = 1 where id = '$transfer_return_id'" );
			}
			if ($returns_po_items) {
				$returns_po_insert = array ();
				$returns_po_insert ['box_number'] = getReturnBoxNumber ( 1 );
				$returns_po_insert ['date_added'] = date ( 'Y-m-d H:i:s' );
				$returns_po_id = $db->func_array2insert ( "inv_returns_po", $returns_po_insert );
				foreach ( $returns_po_items as $returns_po_item ) {
					$returns_po_item_insert = array ();
					$returns_po_item_insert ['product_sku'] = $returns_po_item ['sku'];
					$returns_po_item_insert ['quantity'] = $returns_po_item ['quantity'];
					$returns_po_item_insert ['price'] = $returns_po_item ['price'];
					$returns_po_item_insert ['returns_po_id'] = $returns_po_id;
					$returns_po_item_insert ['return_id'] = $returns_po_item ['return_id'];
					$db->func_array2insert ( "inv_returns_po_items", $returns_po_item_insert );
				}
			}
			if ($return_shipment_box_items) {
				foreach ( $return_shipment_box_items as $box_type => $return_shipment_box_item ) {
					if ($box_type != 'RTV') {
						$inv_return_shipment_box_id = $db->func_query_first_cell("select id from inv_return_shipment_boxes where box_number LIKE '%$box_type%' and status = 'Issued' order by date_added DESC");
						$inv_return_shipment_box_number = $db->func_query_first_cell("select box_number from inv_return_shipment_boxes where box_number LIKE '%$box_type%' and status = 'Issued' order by date_added DESC");
					}
					if(!$inv_return_shipment_box_id && $box_type!=='RTV'){
						$return_shipment_boxes_insert = array ();
						$return_shipment_boxes_insert ['box_number'] = getReturnBoxNumber ( 0, $box_type );
						$return_shipment_boxes_insert ['box_type']   = $box_type;
						$return_shipment_boxes_insert ['date_added'] = date ( 'Y-m-d H:i:s' );
						$inv_return_shipment_box_number = $return_shipment_boxes_insert ['box_number'];
						$inv_return_shipment_box_id = $db->func_array2insert ( "inv_return_shipment_boxes", $return_shipment_boxes_insert );
					}
					$rtv_ship_num = 0;
					foreach ( $return_shipment_box_item as $returns_po_item ) {
						if ($box_type == 'RTV'){
							if (!$returns_po_item ['rtv_shipment_id']) {
								$rejcetedShipment = array();
								$rejcetedShipment ['package_number'] = 'RTV-' . rand();
								$rejcetedShipment ['vendor'] = $returns_po_item ['rtv_vendor_id'];
								$rejcetedShipment ['status'] = 'Pending';
								$rejcetedShipment ['date_added'] = date ( 'Y-m-d H:i:s' );
								$rejcetedShipment ['user_id'] = $_SESSION['user_id'];
								$rtv_ship_num = $db->func_array2insert ( 'inv_rejected_shipments', $rejcetedShipment );
							} else if ($returns_po_item ['rtv_shipment_id']){
								$rtv_ship_num = $db->func_query_first_cell('SELECT id FROM inv_rejected_shipments WHERE package_number = "'. $returns_po_item ['rtv_shipment_id'] .'"');
							}
							$returns_po_item_insert = array ();
							$returns_po_item_insert ['rejected_shipment_id'] = $rtv_ship_num;
							$returns_po_item_insert ['product_sku'] = $returns_po_item ['sku'];
							$returns_po_item_insert ['qty_rejected'] = $returns_po_item ['quantity'];
							$returns_po_item_insert ['reject_reason'] = $db->func_query_first_cell("select id from inv_rj_reasons where name='".$returns_po_item['item_issue']."' limit 1");
							// $returns_po_item_insert ['cost'] = $returns_po_item['price'];
							$returns_po_item_insert ['cost'] = getTrueCost($returns_po_item ['sku']);
							$returns_po_item_insert ['reject_item_id'] = getReturnItemId($returns_po_item ['rma_number'] , $returns_po_item ['sku'],1);
							$returns_po_item_insert ['order_id'] = $returns_po_item ['order_id'];
							$returns_po_item_insert ['production_date'] = $returns_po_item ['production_date'];
							if ($returns_po_item['source'] == 'storefront') {
								$returns_po_item_insert ['date_added'] = $returns_po_item['rma_creation_date'];
								$returns_po_item_insert ['original_box_added'] = date ( 'Y-m-d H:i:s' );
							} else {
								$returns_po_item_insert ['date_added'] = date ( 'Y-m-d H:i:s' );
								$returns_po_item_insert ['original_box_added'] = date ( 'Y-m-d H:i:s' );
							}
							
							$returns_po_item_insert ['rma_number'] = $returns_po_item ['rma_number'];
							$source = ($returns_po_item['source'] == 'storefront')? 'SF': 'RC';
							if ($db->func_escape_string($_POST['printerid'])) {
								printLabel($returns_po_item_insert['reject_item_id'], $returns_po_item_insert['product_sku'], $returns_po_item ['rtv_shipment_id'], $returns_po_item['item_issue'], $returns_po_item_insert['rma_number'], $db->func_escape_string($_POST['printerid']), $source);
							}
							$db->db_exec("UPDATE inv_return_items SET return_item_id='" . $returns_po_item_insert ['reject_item_id'] . "' WHERE id='" . (int) $returns_po_item ['id']. "'");
							$db->func_array2insert ( "inv_rejected_shipment_items", $returns_po_item_insert);
						} else {
							$returns_po_item_insert = array ();
							$returns_po_item_insert ['product_sku'] = $returns_po_item ['sku'];
							$returns_po_item_insert ['quantity'] = $returns_po_item ['quantity'];
							$returns_po_item_insert ['price'] = $returns_po_item ['price'];
							$returns_po_item_insert ['source'] = $returns_po_item['source'];
							$returns_po_item_insert ['order_id'] = $returns_po_item ['order_id'];
							$returns_po_item_insert ['cost'] = $db->func_query_first_cell('SELECT product_true_cost FROM inv_orders_items WHERE order_id = "'. $returns_po_item ['order_id'] .'" AND product_sku = "'. $returns_po_item ['sku'] .'"');
							$returns_po_item_insert ['rma_number'] = $returns_po_item ['rma_number'];
							$returns_po_item_insert ['reason'] = $db->func_escape_string($returns_po_item ['qc_comment']);
							$returns_po_item_insert ['return_item_id'] = getReturnItemId($returns_po_item ['rma_number'] , $returns_po_item ['sku']);
							if ($returns_po_item['source'] == 'storefront') {
								$returns_po_item_insert ['date_added'] = $returns_po_item['rma_creation_date'];
								$returns_po_item_insert ['original_box_added'] = date ( 'Y-m-d H:i:s' );
							} else {
								$returns_po_item_insert ['date_added'] = date ( 'Y-m-d H:i:s' );
								$returns_po_item_insert ['original_box_added'] = date ( 'Y-m-d H:i:s' );
							}
							
							$returns_po_item_insert ['return_shipment_box_id'] = $inv_return_shipment_box_id;
							$source = ($returns_po_item['source'] == 'storefront')? 'SF': 'RC';
							if ($db->func_escape_string($_POST['printerid'])) {
								$returns_po_item_insert['label_pdf'] = printLabel($returns_po_item_insert['return_item_id'], $returns_po_item_insert['product_sku'], $inv_return_shipment_box_number, $returns_po_item ['item_condition'], $returns_po_item_insert['rma_number'], $db->func_escape_string($_POST['printerid']), $source);
							}
							$db->db_exec("UPDATE inv_return_items SET return_item_id='" . $returns_po_item_insert ['return_item_id'] . "', box_id = '".$inv_return_shipment_box_number."' WHERE id='" . (int) $returns_po_item ['id']. "'");
							$db->func_array2insert ( "inv_return_shipment_box_items", $returns_po_item_insert );
						}
					}
				}
			}
		}
$log = 'QC is completed for RMA # ' . linkToRma($rma_number) . $logQC;
actionLog($log);
$db->db_exec("INSERT INTO inv_return_history SET user_id='" . $_SESSION['user_id'] . "',return_status='In QC',date_added='" . date('Y-m-d H:i:s') . "',rma_number='" . $rma_number . "'");
$_SESSION['message'] = "Rma verified from QC";
} elseif (isset($_POST['completed'])) {
	$db->db_exec("update inv_returns SET rma_status = 'Completed' , source = '$source' , date_completed = '" . date('Y-m-d H:i:s') . "' where rma_number = '$rma_number'");
		$db->db_exec("INSERT INTO inv_return_history SET user_id='" . $_SESSION['user_id'] . "',return_status='Completed',date_added='" . date('Y-m-d H:i:s') . "',rma_number='" . $rma_number . "'");
		$log = 'RMA Updated ' . linkToRma($rma_number) . $logQC;
		actionLog($log);
		
		$_SESSION['message'] = "Rma status is completed.";
	} else {
		$db->db_exec("update inv_returns SET source = '$source' where rma_number = '$rma_number'");
		if ($logQC) {
			$log = 'RMA Updated ' . linkToRma($rma_number) . $logQC;
			actionLog($log);
		}
		$_SESSION['message'] = "Rma changes are saved.";
	}
	header("Location:$host_path/return_detail.php?rma_number=$rma_number");
	exit;
}
$rma_return = $db->func_query_first("select r.* ,o.email, o.order_date, od.first_name,od.last_name,od.address1,od.address2,od.payment_method,
	od.city,od.state,od.zip,od.country,od.phone_number from inv_returns r 
	left join inv_orders o on (r.order_id = o.order_id) 
	left join inv_orders_details od on (r.order_id = od.order_id)
	where rma_number  = '$rma_number'");
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
if (!$rma_return['order_id']) {
	if ($rma_return['email'] != '') {
	 
	 $last_order_id = $db->func_query_first_cell('SELECT order_id from inv_orders where email = "'.$rma_return['email'].'" order by order_date desc');
	 if ($last_order_id) {	
	 	$last_order_details = $db->func_query_first('SELECT * from inv_orders_details where order_id = "'.$last_order_id.'"');
	 } else {
	 	$last_order_details = $db->func_query_first('SELECT firstname as first_name, lastname as last_name,address1,address2,city,state,zip,telephone as phone_number FROM inv_customers  WHERE email = "'.$rma_return['email'].'"');
	 }
	 $rma_return['first_name'] = $last_order_details['first_name'];
	 $rma_return['last_name'] = $last_order_details['last_name'];
	 $rma_return['address1'] = $last_order_details['address1'];
	 $rma_return['address2'] = $last_order_details['address2'];
	 $rma_return['city'] = $last_order_details['city'];
	 $rma_return['state'] = $last_order_details['state'];
	 $rma_return['country'] = $last_order_details['country'];
	 $rma_return['zip'] = $last_order_details['zip'];
	 $rma_return['phone_number'] = $last_order_details['phone_number'];
	} else {
	 $email = $db->func_query_first_cell('SELECT email from inv_returns where rma_number = "'.$rma_number.'"');
	 $last_order_id = $db->func_query_first_cell('SELECT order_id from inv_orders where email = "'.$email.'" order by order_date desc');
	 if ($last_order_id) {	
	 	$last_order_details = $db->func_query_first('SELECT * from inv_orders_details where order_id = "'.$last_order_id.'"');
	 } else {
	 	$last_order_details = $db->func_query_first('SELECT firstname as first_name, lastname as last_name,address1,address2,city,state,zip,telephone as phone_number FROM inv_customers  WHERE email = "'.$email.'"');
	 }
	 $rma_return['first_name'] = $last_order_details['first_name'];
	 $rma_return['email'] = $email;
	 $rma_return['last_name'] = $last_order_details['last_name'];
	 $rma_return['address1'] = $last_order_details['address1'];
	 $rma_return['address2'] = $last_order_details['address2'];
	 $rma_return['city'] = $last_order_details['city'];
	 $rma_return['state'] = $last_order_details['state'];
	 $rma_return['country'] = $last_order_details['country'];
	 $rma_return['zip'] = $last_order_details['zip'];
	 $rma_return['phone_number'] = $last_order_details['phone_number'];
	}
}
if (!$rma_return) {
	header("Location:$host_path/manage_returns.php");
	exit;
}
if($_POST['action']=='create_return_shipment')
{
	
	$rejected_items = json_decode(stripslashes($_POST['rejected_items']));
	$shipping_method = $db->func_escape_string($_POST['shipping_method']);
	$shipping_cost = (float)$_POST['shipping_cost'];
	
	// print_r($rma_return);exit;
	$customer_return_order = array();
	$customer_return_order['order_number'] = $rma_return['rma_number'] . "-" . $rma_return['order_id'];
	$customer_return_order['order_id'] = $rma_return['order_id'];
	$customer_return_order['rma_number'] = $rma_return['rma_number'];
	$customer_return_order['date_added'] = date('Y-m-d H:i:s');
	$customer_return_order['order_status'] = 'Created';
	$customer_return_order['user_id'] = $_SESSION['user_id'];
	$customer_return_order['email'] = $rma_return['email'];
	$customer_return_order['first_name'] = $rma_return['first_name'];
	$customer_return_order['last_name'] = $rma_return['last_name'];
	$customer_return_order['phone_number'] = $rma_return['phone_number'];
	$customer_return_order['address1'] = $rma_return['address1'];
	$customer_return_order['city'] = $rma_return['city'];
	$customer_return_order['state'] = $rma_return['state'];
	$customer_return_order['zip'] = $rma_return['zip'];
	$customer_return_order['shipping_method'] = $shipping_method;
	$customer_return_order['shipping_cost'] = $rma_return['shipping_cost'];
	$customer_return_order['country'] = 'US';
	$customer_return_order['date_modified'] = date('Y-m-d H:i:s');
	$customer_return_order_id = $db->func_array2insert("inv_customer_return_orders", $customer_return_order);
	foreach ($rejected_items as $key => $value) {
		$_product_detail = $db->func_query_first("SELECT * FROM inv_return_items WHERE id='".$value->id."'");
		$customer_return_order_item = array();
		$customer_return_order_item['customer_return_order_id'] = $customer_return_order_id;
		$customer_return_order_item['order_item_id'] = $rma_return['rma_number'] . "-" . $_product_detail['sku'] . "-" . $value->id;
		$customer_return_order_item['product_sku'] = $_product_detail['sku'];
		$customer_return_order_item['product_qty'] = 1;
		$customer_return_order_item['product_price'] = $_product_detail['price'];
		$db->func_array2insert(" inv_customer_return_order_items", $customer_return_order_item);
	}
	$db->db_exec("UPDATE inv_returns SET for_shipstation=1,denied_order_created = 1 WHERE rma_number='".$rma_number."'");
	exit;
	
}
if ($_POST['deniedReturnOrder']) {
	if (count($_POST['return_item']) > 0) {
		$customer_return_order = array();
		$customer_return_order['order_number'] = $rma_return['rma_number'] . "-" . $rma_return['order_id'];
		$customer_return_order['order_id'] = $rma_return['order_id'];
		$customer_return_order['rma_number'] = $rma_return['rma_number'];
		$customer_return_order['date_added'] = date('Y-m-d H:i:s');
		$customer_return_order['order_status'] = 'Created';
		$customer_return_order['user_id'] = $_SESSION['user_id'];
		$customer_return_order['email'] = $rma_return['email'];
		$customer_return_order['first_name'] = $rma_return['first_name'];
		$customer_return_order['last_name'] = $rma_return['last_name'];
		$customer_return_order['phone_number'] = $rma_return['phone_number'];
		$customer_return_order['address1'] = $rma_return['address1'];
		$customer_return_order['city'] = $rma_return['city'];
		$customer_return_order['state'] = $rma_return['state'];
		$customer_return_order['zip'] = $rma_return['zip'];
		$customer_return_order['country'] = 'US';
		$customer_return_order['date_modified'] = date('Y-m-d H:i:s');
		$customer_return_order_id = $db->func_array2insert("inv_customer_return_orders", $customer_return_order);
		foreach ($_POST['product_sku'] as $return_item_id => $product_sku) {
			$customer_return_order_item = array();
			$customer_return_order_item['customer_return_order_id'] = $customer_return_order_id;
			$customer_return_order_item['order_item_id'] = $rma_return['rma_number'] . "-" . $product_sku . "-" . $return_item_id;
			$customer_return_order_item['product_sku'] = $product_sku;
			$customer_return_order_item['product_qty'] = 1;
			$customer_return_order_item['product_price'] = $_POST['product_price'][$order_item_id];
			$db->func_array2insert(" inv_customer_return_order_items", $customer_return_order_item);
		}
		$db->db_exec("update inv_returns SET denied_order_created = 1 where rma_number = '$rma_number'");
		$_SESSION['message'] = "Customer Denied order created successfully.";
	} else {
		$_SESSION['message'] = "Please select at least 1 item to create order.";
	}
	header("Location:return_detail.php?rma_number=$rma_number");
	exit;
}
$return_items = $db->func_query("select * from inv_return_items where return_id = '" . $rma_return['id'] . "' and removed = 0");
$removed_items = $db->func_query("select * from inv_return_items where return_id = '" . $rma_return['id'] . "' and removed = 1");
$comments = $db->func_query("select * from inv_return_comments c left join inv_users u on (c.user_id = u.id) where return_id = '" . $rma_return['id'] . "'");
$productPrice = 0;
$productNames = '<table><tbody>';
$productDetails = '<table width="100%">';
$productDetails .= '<thead><tr>';
$productDetails .= '<th width="35%">Name</th>';
$productDetails .= '<th width="10%">Return Reason</th>';
$productDetails .= '<th width="10%">Condition</th>';
$productDetails .= '<th width="10%">Decision</th>';
$productDetails .= '<th width="10%">Amount</th>';
$productDetails .= '<th width="35%">Images</th>';
$productDetails .= '<th width="35%"></th>';
$productDetails .= '</tr></thead><tbody>';
foreach ($return_items as $return_item) {
	$price = ($return_item['item_condition'] == 'Customer Damage' && $return_item['decision'] == 'Denied')? '0' : $return_item['price'];
	$productPrice += (float) $price;
	$productNames .= '<tr><td>'. $return_item['sku'] . ' - ' . $return_item['title'] .'</td></tr>';
	$productDetails .= '<tr>';
	$productDetails .= '<td>'. $return_item['sku'] . ' - ' . $return_item['title'] .'</td>';
	$productDetails .= '<td>'. $return_item['return_code'] . '</td>';
	$productDetails .= '<td>'. $return_item['item_condition'] . ' - ' . $return_item['item_issue'] .'</td>';
	$productDetails .= '<td>'. $return_item['decision'] .'</td>';
	$productDetails .= '<td>'. $price .'</td>';
	$images = $db->func_query("select * from inv_return_item_images where return_item_id = '" . $return_item['id'] . "'");
	$productDetails .= '<td>';
	if ($images) {
		$productDetails .= '<table> <tr>';
		foreach ($images as $image) {
			$productDetails .= '<td><a href="' . $host_path . str_ireplace("../", "", $image['image_path']) . '">';
			$productDetails .= '<img src="' . $host_path . str_ireplace("../", "", $image['thumb_path']) . '" width="25" height="25" />';
			$productDetails .= '</a></td>';
		}
		$productDetails .= '</tr></table>';
	}
	$productDetails .= '</td></tr>';
}
$productDetails .= '</tbody></table>';
$productNames .= '</tbody></table>';
$emailInfo = array(
	'customer_name' => $rma_return['first_name'] .' '. $rma_return['last_name'],
	'email' => $rma_return['email'],
	'rma_number' => $rma_number,
	'order_id' => $rma_return['order_id'],
	'shipping_firstname' => $rma_return['first_name'],
	'shipping_lastname' => $rma_return['last_name'],
	'shipping_address_1' => $rma_return['address1'],
	'shipping_address_2' => $rma_return['address2'],
	'shipping_city' => $rma_return['city'],
	'shipping_zone' => $rma_return['state'],
	'shipping_postcode' => $rma_return['zip'],
	'rma_status' => $rma_return['rma_status'],
	'order_date' => americanDate($rma_return['order_date']),
	'rma_recived' => americanDate($rma_return['date_completed']),
	'rma_qc' => americanDate($rma_return['date_qc'])
    // 'rma_products_names' => $productNames,
    // 'rma_products_Details' => $productDetails,
    // 'total_price' => $productPrice
	);
$_SESSION['rma_info' . $rma_number] = $emailInfo;
$adminInfo = array('name' => $_SESSION['login_as'], 'company_info' => $_SESSION['company_info'] );
if (isset($_POST['sendemail'])) {
	if ($_POST['selected_products'] != '') {
		$slProduct = explode(',', $_POST['selected_products']);
		$productPrice = 0;
		$productNames = '<table><tbody>';
		$productDetails = '<table width="100%">';
		$productDetails .= '<thead><tr>';
		$productDetails .= '<th width="35%">Name</th>';
		$productDetails .= '<th width="10%">Return Reason</th>';
		$productDetails .= '<th width="10%">Condition</th>';
		$productDetails .= '<th width="10%">Decision</th>';
		$productDetails .= '<th width="10%">Amount</th>';
        $productDetails .= '<th width="35%">Images</th>';
		$productDetails .= '<th width="35%"></th>';
		$productDetails .= '</tr></thead><tbody>';
		foreach($slProduct as $val) {
			foreach ($return_items as $return_item) {
				if ($return_item['id'] == $val) {
					$price = ($return_item['item_condition'] == 'Customer Damage' && $return_item['decision'] == 'Denied')? '0' : $return_item['price'];
					$productPrice += (float) $price;
					$productNames .= '<tr><td>'. $return_item['sku'] . ' - ' . $return_item['title'] .'</td></tr>';
					$productDetails .= '<tr>';
					$productDetails .= '<td>'. $return_item['sku'] . ' - ' . $return_item['title'] .'</td>';
					$productDetails .= '<td>'. $return_item['return_code'] . '</td>';
					$productDetails .= '<td>'. $return_item['item_condition'] . ' - ' . $return_item['item_issue'] .'</td>';
					$productDetails .= '<td>'. $return_item['decision'] .'</td>';
					$productDetails .= '<td>'. $price .'</td>';
					$images = $db->func_query("select * from inv_return_item_images where return_item_id = '" . $return_item['id'] . "'");
					$productDetails .= '<td>';
					if ($images) {
						$productDetails .= '<table> <tr>';
						foreach ($images as $image) {
							$productDetails .= '<td><a href="' . $host_path . str_ireplace("../", "", $image['image_path']) . '">';
							$productDetails .= '<img src="' . $host_path . str_ireplace("../", "", $image['thumb_path']) . '" width="25" height="25" />';
							$productDetails .= '</a></td>';
						}
						$productDetails .= '</tr></table>';
					}
					$productDetails .= '</td></tr>';
				}
			}
		}
		$productDetails .= '</tbody></table>';
		$productNames .= '</tbody></table>';
	}
	$emailInfo['rma_products_names'] = $productNames;
	$emailInfo['rma_products_Details'] = $productDetails;
	$emailInfo['total_price'] = $productPrice;
	$email = array();
	$src = $path .'files/canned_' . $_POST['canned_id'] . ".png";
	if (file_exists($src)) {
		$email['image'] = $host_path .'files/canned_' . $_POST['canned_id'] . ".png?" . time();
	}
	$email['title'] = $_POST['title'];
	$email['subject'] = $_POST['subject'];
	$email['number'] = array('title' => 'RMA No #', 'value' => $emailInfo['rma_number']);
	$email['message'] = shortCodeReplace($emailInfo, $_POST['comment']);
	sendEmailDetails($emailInfo, $email);
	header("Location:$host_path/return_detail.php?rma_number=$rma_number");
	exit;
}
//echo '<pre>'; print_r($emailInfo['rma_products_Details']); exit;
if ($rma_return['date_added']<'2017-11-12 00:00:00') {
	$item_conditions = array(array('id' => 'Good For Stock', 'value' => 'Good For Stock'),
	array('id' => 'Item Issue', 'value' => 'Item Issue'),
	array('id' => 'Item Issue - RTV', 'value' => 'Item Issue - RTV'),
	array('id' => 'Customer Damage', 'value' => 'Customer Damage'),
	array('id' => 'Not Tested', 'value' => 'Not Tested'),
	array('id' => 'Not PPUSA Part', 'value' => 'Not PPUSA Part'),
	array('id' => 'Over 60 days', 'value' => 'Over 60 days'),
	array('id' => 'Shipping Damage', 'value' => 'Shipping Damage'),
	array('id' => 'Need To Repair', 'value' => 'Need To Repair')
	);
} else {
	$item_conditions = array(array('id' => 'Good For Stock', 'value' => 'Good For Stock'),
	/*array('id' => 'Item Issue', 'value' => 'Item Issue'),*/
	array('id' => 'Item Issue - RTV', 'value' => 'Item Issue - RTV'),
	array('id' => 'Customer Damage', 'value' => 'Customer Damage'),
	array('id' => 'Not Tested', 'value' => 'Not Tested'),
	array('id' => 'Not PPUSA Part', 'value' => 'Not PPUSA Part'),
	array('id' => 'Over 60 days', 'value' => 'Over 60 days'),
	array('id' => 'Shipping Damage', 'value' => 'Shipping Damage'),
	array('id' => 'Need To Repair', 'value' => 'Need To Repair')
	);
}
$printers = array(
	// array('id' => '157967', 'value' => 'QC1'),
	array('id' => QC1_PRINTER, 'value' => 'QC1'),
	array('id' => QC2_PRINTER, 'value' => 'QC2'),
	array('id' => REC_PRINTER, 'value' => 'Receiving'),
	array('id' => STOREFRONT_PRINTER, 'value' => 'Storefront')
	);
$decisionsx = array(array('id' => 'Issue Credit', 'value' => 'Issue Credit'),
	array('id' => 'Issue Refund', 'value' => 'Issue Refund'),
	array('id' => 'Issue Replacement', 'value' => 'Issue Replacement')
	);
$item_issues = $db->func_query("select * from inv_reasonlist");
if (isset($_POST['reprint_labels'])) {
				//testObject($_POST);
	foreach ($return_items as $item) {
		if ($_POST['add_to_box'][$item['id']]) {
			if ($_POST['item_condition'][$item['id']] == 'Item Issue' ) {
				printLabel($_POST['return_item_id'][$item['id']], $item['sku'], $_POST['return_box_id'][$item['id']], $_POST['item_issue'][$item['id']], $rma_number, $_POST['printerid'], 'RC',24);
			} else if ($_POST['item_condition'][$item['id']] == 'Item Issue - RTV') {
				printLabel($_POST['return_item_id'][$item['id']], $item['sku'], $_POST['vendor_rtv_ship_'][$item['id']], $_POST['item_issue_rtv_'][$item['id']], $rma_number, $_POST['printerid'], 'RC',24);
			} else {
				printLabel($_POST['return_item_id'][$item['id']], $item['sku'], $_POST['return_box_id'][$item['id']], $_POST['decision'][$item['id']], $rma_number, $_POST['printerid'], 'RC',24);
			}
		}
	}
	$_SESSION['message'] = "Labels Reprinted. Please Check Printer.";
header("Location:$host_path/return_detail.php?rma_number=$rma_number");
}
if (isset($_POST['received'])) {
	$db->db_exec("update inv_returns SET rma_status = 'Received' , date_completed = '" . date('Y-m-d H:i:s') . "' where rma_number = '$rma_number'");
	/*foreach ($return_items as $item) {
		printLabel(0, $item['sku'], 0, 0, $rma_number, $_POST['printerid'], 'RC', 24);
	}*/
	$log = 'RMA Received ' . linkToRma($rma_number);
	actionLog($log);
	$db->db_exec("INSERT INTO inv_return_history SET user_id='" . $_SESSION['user_id'] . "',return_status='Received',date_added='" . date('Y-m-d H:i:s') . "',rma_number='" . $rma_number . "'");
	$templete = $db->func_query_first('SELECT * FROM inv_canned_message WHERE `catagory` = "2"  AND `type` = "Recived"');
	$email = array();
	if ($templete) {
		$src = $path .'files/canned_' . $templete['canned_message_id'] . ".png";
		if (file_exists($src)) {
			$email['image'] = $host_path .'files/canned_' . $templete['canned_message_id'] . ".png?" . time();
		}
		$email['title'] = shortCodeReplace($emailInfo, $templete['title']);
		$email['subject'] = shortCodeReplace($emailInfo, $templete['subject']);
		$email['number'] = array('title' => 'RMA No #', 'value' => $emailInfo['rma_number']);
		$email['message'] = shortCodeReplace($emailInfo, $templete['message']);
	}
	sendEmailDetails($emailInfo, $email);
	header("Location:$host_path/return_detail.php?rma_number=$rma_number");
	exit;
}
// Event Details
$eventData = $eventData . 'Name: ' . $rma_return['first_name'] . ' ' . $rma_return['last_name'] . "\n";
$eventData = $eventData . 'Phone: ' . $order['phone_number'] . "\n";
$eventData = $eventData . 'Email: ' . $rma_return['email'] . "\n";
$eventData = $eventData . 'Address: ' . $rma_return['address1'] . ' ' . $rma_return['address2'] . "\n";
$eventData = $eventData . $rma_return['city'] . ', ' . $rma_return['state'] . "\n";
$eventData = $eventData . $rma_return['country'] . ', ' . $rma_return['zip'];
$_SESSION['event_details'][$rma_number] = $eventData;
if ($_GET['send_pdf']) {
	if ($rma_return['file'] && file_exists($rma_return['file'])) {
		$html2 = 'Thank you for submitting your return request. Your RMA # is ' . $rma_return['rma_number'] . "<br>";
		$html2.='Please read our returns policy (<a href="http://phonepartsusa.com/returns-or-exchanges">http://phonepartsusa.com/returns-or-exchanges</a>) and note the following:';
		$html2.='<ol>';
		$html2.='<li>Print and Affix RMA Label on the exterior of the Package</li>';
		$html2.='<li>Returns <span style="color:red"><strong>must be postmarked within 30 days after</strong> ' . americanDate($rma_return['date_added']) . '</span> . If the returned package is sent after this time, we reserve the right to refuse refunds and exchanges.</li>';
		$html2.='<li>Exchanges and refunds will only be offered for items in their original unused condition. Damaged items will <strong>NOT</strong> be refunded.</li>';
		$html2.='<li>Please allow 24-48 business hours for return processing.</li>';
		$html2.='</ol>';
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->CharSet = 'UTF-8';
		$mail->Host = MAIL_HOST; 
    	// SMTP server example
		$mail->SMTPDebug = 0;                     
	    // enables SMTP debug information (for testing)
		$mail->SMTPAuth = true;                  
	    // enable SMTP authentication
		$mail->Port = 25;                    
	    // set the SMTP port for the GMAIL server
		$mail->Username = MAIL_USER; 
	    // SMTP account username example
		$mail->Password = MAIL_PASSWORD;        
	    // SMTP account password example
		$mail->SetFrom(MAIL_USER, 'PhonePartsUSA');
		$mail->addAddress($emailInfo['email'], $emailInfo['customer_name']);
		$mail->Subject = 'RMA Request Recived';
		$mail->Body = $html2;
		$mail->IsHTML(true);
		if($rma_return['file']){
			$mail->addAttachment($rma_return['file'], 'RMA_Return.pdf');
		}
		$dataEmail = array();
		$dataEmail['customer_name'] = $emailInfo['customer_name'];
		$dataEmail['customer_email'] = $emailInfo['email'];
		$dataEmail['order_id'] = $emailInfo['order_id'];
		$dataEmail['return_id'] = $emailInfo['order_id'] . 'R';
		$dataEmail['email_subject'] = 'RMA Request Recived';
		$dataEmail['email_body'] = $html2;
		$dataEmail['resolution'] = 'RMA Request Recived';
		$dataEmail['notes'] = '';
		$dataEmail['date_sent'] = date('Y-m-d h:i:s');
		$dataEmail['sent_by'] = $_SESSION['user_id'];
		if ($mail->send()) {
			$dataEmail['is_sent'] = 1;
			$db->func_array2insert('inv_email_report', $dataEmail);
			$_SESSION['message'] = "Email sent";
		} else {
			$_SESSION['message'] = "Email Not Sent Please Try Some Other Time";
		}
		header("Location:$host_path/return_detail.php?rma_number=$rma_number");
		exit;
	} else {
		$_SESSION['message'] = "File Not Fuound";
	}
}
//$returns=$db->func_query_first("SELECT * FROM inv_returns WHERE rma_number='$rma_number'");
//$ret_id=$detail['rma_number'];
//$qc_id=func_query_first("SELECT auth_qc FROM inv_returns WHERE rma_number=='".$ret_id."'");
//$manager_id=func_query_first("SELECT auth_manager FROM inv_returns WHERE rma_number=='".$ret_id."'");
//$qc_name=func_query_first("SELECT name FROM inv_users WHERE id=='".$qc_id."'");
//$manager_name=func_query_first("SELECT name FROM inv_users WHERE id=='".$manager_id."'");
//print_r($return_item['sku']);
//					exit;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Return Inventory Page</title>
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
  <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
  <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
  <script type="text/javascript">
  	$(document).ready(function () {
  		$('.fancybox').fancybox({width: '450px', autoCenter: true, autoSize: true});
  		$('.fancybox2').fancybox({width: '680px', autoCenter: true, autoSize: true});
  		$('.fancybox3').fancybox({width: '980px', autoCenter: true, autoSize: true});
  	});
  </script>	
	<script type="text/javascript">
		function unlockBox(condition, order_product_id) {
			if (condition == 'Item Issue') {
				jQuery("#item_issue_" + order_product_id).show();
			}
			else {
				jQuery("#item_issue_" + order_product_id).hide();
			}
			exceptionBox(condition,order_product_id);
		}
		function unlockBoxRTV(condition, order_product_id) {
			if (condition == 'Item Issue - RTV') {
				jQuery("#item_issue_rtv_" + order_product_id).show();
				jQuery("#item_vendor_rtv_" + order_product_id).show();
				jQuery("#vendor_rtv_ship_" + order_product_id).show();
				jQuery("#production_date_" + order_product_id).show();

				
			}
			else {
				jQuery("#item_issue_rtv_" + order_product_id).hide();
				// jQuery("#item_vendor_rtv_" + order_product_id).hide();
				jQuery("#vendor_rtv_ship_" + order_product_id).hide();
				jQuery("#production_date_" + order_product_id).hide();
			}
			exceptionBox(condition,order_product_id);
		}
		function exceptionBox(condition,order_product_id)
		{
			//
			if( condition == 'Customer Damage' || condition == 'Not PPUSA Part' || condition == 'Over 60 days')
			{
				$('#exception_box_'+order_product_id).show();
				if($('#decision_'+order_product_id))
				{	
					//$('#decision_'+order_product_id).hide();
					changeDecisionBox(false,order_product_id);
				}
				$('.xdecision').hide();
			}
			else
			{
				$('#exception_box_'+order_product_id).hide();
				if($('#decision_'+order_product_id))
				{
				//$('#decision_'+order_product_id).show();
				changeDecisionBox(true,order_product_id);
			}
			$('.xdecision').show();
			$('#item_exception_'+order_product_id).prop('checked',false);
		}
		showException($('#item_exception_'+order_product_id),order_product_id);
	}
	function showException(obj,order_product_id)
	{
		var condition = $('#item_condition_'+order_product_id).val();
		if( condition == 'Customer Damage' || condition == 'Not PPUSA Part' || condition == 'Over 60 days')
		{
			if($(obj).is(':checked'))
			{
				if($('#decision_'+order_product_id))
				{
				//$('#decision_'+order_product_id).show();	\
				changeDecisionBox(true,order_product_id);
			}
			$('.xdecision').show();
		}
		else
		{
			if($('#decision_'+order_product_id))
			{
				//$('#decision_'+order_product_id).hide();	
				changeDecisionBox(false,order_product_id);
			}
			$('.xdecision').hide();
		}
	}
	else
	{
		if($('#decision_'+order_product_id))
		{
				//$('#decision_'+order_product_id).show();	
				changeDecisionBox(true,order_product_id);
			}
			$('.xdecision').show();
		}
	}
	$(document).on('click','.shrinkToFit',function(e){
		window.open($(this).attr('src'),'_blank');
	});
	$('.shrinkToFit').click(function(e){
		alert('here');
	});

	$(document).on('click','.img_damaged',function(e) {
   
    $.fancybox({
        content: '<img src="'+$(this).attr('data-href')+'" style="width:500px;cursor:pointer" onclick="window.open(\''+$(this).attr('data-href')+'\',\'target=_blank\')">', 
        modal: false,
        width: '600px', autoCenter: true, autoSize: false,
        type: 'iframe'
    });
    return false;
});
	function changeDecisionBox(is_normal,order_product_id)
	{
			//alert(is_normal);
			//var option = '<option value="">Select One</option>';
			if(is_normal)
			{
				<?php
				$option = '';
				$__d = $decisionsx;
				
				
				?>
				<?php
				$option = '';
				foreach($__d as $_d)
				{
					$option.='<option value="'.$_d['id'].'" '.($_d['id'] == $return_item['decision']?'selected':'').'>'.$_d['value'].'</option>';
				}
				?>
				$('#decision_'+order_product_id).html('<option value="">Select One</option>'+'<?php echo $option;?>');		
			}
			else
			{
				<?php
				$__d = array(array('id' => 'Denied', 'value' => 'Denied'));
				?>
				<?php
				$option = '';
				foreach($__d as $_d)
				{
					$option.='<option value="'.$_d['id'].'" '.($_d['id'] == $return_item['decision']?'selected':'').'>'.$_d['value'].'</option>';
				}
				?>
				$('#decision_'+order_product_id).html('<option value="">Select One</option>'+'<?php echo $option;?>');	
			}
		}
		function updateStockingPrice(return_item_id,obj)
		{
			var grade = $(obj).val();
			var discount = $(obj).attr('data-discount');
			var price = $('#product_price_'+return_item_id).attr('data-price');	
			var discount_amount = (parseFloat(price)*parseInt(discount)) / 100;
			var return_amount = parseFloat(price) - parseFloat(discount_amount);
			$('#product_price_'+return_item_id).val(return_amount.toFixed(2));
			$('#discount_amount_'+return_item_id).val(discount_amount.toFixed(2));
			$('#discount_per_'+return_item_id).val(discount);
		}
		function populateRestocking(return_item_id,obj)
		{
			if($(obj).is(':checked')){
				$('#div_'+return_item_id).show(500);
				$('#div_'+return_item_id+' input[type=radio]:eq(0)').click();
				updateStockingPrice(return_item_id,$('#div_'+return_item_id+' input[type=radio]:eq(0)'));
			}else{
				$('#div_'+return_item_id).hide(500);
				var price = parseFloat($('#product_price_'+return_item_id).attr('data-price'));	
				$('#product_price_'+return_item_id).val(price.toFixed(2));
				$('#discount_amount_'+return_item_id).val(0.00);
				$('#discount_per_'+return_item_id).val(0);
			}	
		}
	</script>
	 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  
  
  <script>
  function loadRTVship(obj,return_id){
  		var default_box = $('#item_selected_box_'+return_id).val();
		// console.log(default_box);
		$.ajax({
			url: "rejected_shipments.php",
			method: "POST",
			data: {action:'load_rtv_shipments',vendor_id_shipments_loader:obj},
			dataType: 'json',
			success: function (json) {
				var html = '';
				for (var i = json['loaded_rtv_shipments'].length - 1; i >= 0; i--) {
							html += '<option value="'+json['loaded_rtv_shipments'][i]['package_number']+'" '+(json['loaded_rtv_shipments'][i]['package_number']==default_box?'selected':'')+'>'+json['loaded_rtv_shipments'][i]['package_number']+'</option>';
            		}
            		html += '<option value="">Create New Box</option>';
          $('#vendor_rtv_ship_load'+return_id).html(html);
				}
			});
	}
  $( function() {
    $.widget( "custom.iconselectmenu", $.ui.selectmenu, {
      _renderItem: function( ul, item ) {
        var li = $( "<li>" ),
          wrapper = $( "<div>", { text: item.label } );
 
        if ( item.disabled ) {
          li.addClass( "ui-state-disabled" );
        }
 
        $( "<span>", {
          style: item.element.attr( "data-style" ),
          "class": "ui-icon " + item.element.attr( "data-class" )
        })
          .appendTo( wrapper );
 
        return li.append( wrapper ).appendTo( ul );
      }
    });
 
   
 
    
 
    $( ".vendor_div" )
      .iconselectmenu()
      .iconselectmenu( "menuWidget")
        .addClass( "ui-menu-icons avatar" );
        $('.vendor_div').iconselectmenu({
    change: function( event, ui) {
    loadRTVship(this.value,$(this).attr('data-return-id'));
    }
});
  } );
   // $( ".vendor_div" ).selectmenu({ change: function( event, ui ) { alert('x'); }});
  </script>
  <style>
  .ui-menu-item
  {
  		float:none !important;
  }
.ui-selectmenu-menu .ui-menu.customicons .ui-menu-item-wrapper {
      padding: 0.5em 0 0.5em 3em;
    }
    .ui-selectmenu-menu .ui-menu.customicons .ui-menu-item .ui-icon {
      height: 24px;
      width: 24px;
      top: 0.1em;
    }
    .ui-icon.video {
      background: url("images/24-video-square.png") 0 0 no-repeat;
    }
    .ui-icon.podcast {
      background: url("images/24-podcast-square.png") 0 0 no-repeat;
    }
    .ui-icon.rss {
      background: url("images/24-rss-square.png") 0 0 no-repeat;
    }
 
    /* select with CSS avatar icons */
    option.avatar {
      background-repeat: no-repeat !important;
      padding-left: 20px;
    }
    .avatar .ui-icon {
      background-position: left top;
    }
  </style>
</head>
<body>
	<div class="div-fixed">
		<div align="center"> 
			<?php include_once 'inc/header.php'; ?>
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
				<form method="post" action="" id="returnForm" onsubmit="return verifyupdates();" enctype="multipart/form-data">
					<h2>RMA Return Details</h2><br>
					<h1 style="font-size:14px;color:#0059a0">Created By: 
						<?php if($rma_return['oc_user_id'] == 0){
							echo linkToProfile($rma_return['email'],$host_path);
						}else if ($rma_return['source'] == 'manual'){
							echo get_username($rma_return['oc_user_id']);
						} else {
							echo get_username($rma_return['oc_user_id'],true);
						}
						?> (<?php echo americanDate($rma_return['date_added']); ?>)</h1>
					<?php if ($_SESSION['issue_types']){ ?>
					<table cellspacing="0" cellpadding="10" width="90%" border="0">
						<tr>
							<td align="right">
								<a class="fancybox3 fancybox.iframe" href="<?=$host_path;?>settings/item_issues.php?popup=1">Add Item Issues</a>
								<?php if ($rma_return['file']) { ?>
								<a class="button" href="<?="$host_path/return_detail.php?rma_number=$rma_number";?>&send_pdf=1">Send PDF Again</a>
								<a class="button" target="_blank" href="<?= str_replace('imp.', 'www.',str_replace(str_replace('imp/', '', $path), str_replace('imp/', '', $host_path), $rma_return['file']) );?>">View PDF</a>
								<?php } ?>
							</td>
						</tr>
					</table>
					<?php

				}
				?>
				<?php if ($rma_return['rma_status'] == 'Awaiting'): ?>
					<a href="<?php echo $host_path; ?>/popupfiles/rma_addsku.php?return_id=<?php echo $rma_return['id'] ?>" class="fancybox fancybox.iframe">Add SKU</a>
				<?php endif; ?>		
				<br /><br />
				<table border="1" cellpadding="10" cellspacing="0" width="90%">
					<tr>
						<td>
							<table cellpadding="5">
								<caption><b>Shipping</b></caption>
								<br>
								<b><?=$rma_return['approval_count'];?> to 2 Approvals</b>
								<tr>	
									<td><b>Full Name:</b></td>
									<td><?php echo $rma_return['first_name'] . " " . $rma_return['last_name']; ?></td>
									<td></td>
								</tr>
								<tr>	
									<td><b>Email:</b></td>
									<td><?php echo linkToProfile($rma_return['email'], $host_path); ?></td>
									<?php
									if( ($_SESSION['approve_return_shipp']==1 || $_SESSION['approve_send_label']==1) ) { ?>
									<?php if($rma_return['is_label_created']==1) { ?>
									<?php if($rma_return['is_voided']==1) { ?>
									<td>Shipment Voided</td>
									<?php } else { ?>
									<td><a class="" href="javascript:void(0);" onclick="voidShippingLabel();">Void  Label</a></td>
									<?php } ?>
									<?php } else { ?>
									<td><a class="fancybox2 fancybox.iframe " href="approve_rma_label.php?rma_number=<?=$rma_number;?>">Approve  Label</a></td>
									<?php } ?>
									<?php } else { ?>	
									<td></td>
									<?php } ?>
								</tr>
								<tr>	
									<td><b>Address 1:</b></td>
									<td><?php echo $rma_return['address1'] ?></td>
									<td></td>
								</tr>
								<tr>	
									<td><b>Address 2:</b></td>
									<td><?php echo $rma_return['address2']; ?></td>
									<td></td>
								</tr>
								<tr>	
									<td>City: <?php echo $rma_return['city']; ?></td>
									<td>State: <?php echo $rma_return['state']; ?></td>
									<td>Zip: <?php echo $rma_return['zip']; ?></td>
								</tr>
							</table>	    
						</td>
						<td>
							<table cellpadding="5">
								<caption><b>Billing</b></caption>
								<tr>	
									<td><b>Full Name:</b></td>
									<td><?php echo $rma_return['first_name'] . " " . $rma_return['last_name']; ?></td>
									<td></td>
								</tr>
								<tr>	
									<td><b>Email:</b></td>
									<td><?php echo linkToProfile($rma_return['email'], $host_path); ?></td>
									<td></td>
								</tr>
								<tr>	
									<td><b>Address 1:</b></td>
									<td><?php echo $rma_return['address1'] ?></td>
									<td></td>
								</tr>
								<tr>	
									<td><b>Address 2:</b></td>
									<td><?php echo $rma_return['address2']; ?></td>
									<td></td>
								</tr>
								<tr>	
									<td>City: <?php echo $rma_return['city']; ?></td>
									<td>State: <?php echo $rma_return['state']; ?></td>
									<td>Zip: <?php echo $rma_return['zip']; ?></td>
								</tr>
							</table>	    
						</td>
						<td>
							<table cellpadding="5">
								<caption><b>Other Detail</b></caption>
								<tr>
									<td><b>Order ID: <a href="viewOrderDetail.php?order=<?php echo $rma_return['order_id']; ?>"><?php echo $rma_return['order_id']; ?></a></b></td>
									<td>|</td>
									<td><b>RMA # <?php echo $rma_return['rma_number']; ?></b></td>	    	       
								</tr>
								<tr>
									<td><b>Order Date: <?php echo americanDate($rma_return['order_date']); ?></b></td>
									<td>|</td>
									<td>
										<?php
										$received_check = $db->func_query_first("SELECT * FROM inv_return_history WHERE rma_number='" . $rma_return['rma_number'] . "' AND return_status='Received' order by return_history_id desc");
										if($received_check)
										{
											?>
											<b>Date Received: <?php echo americanDate($received_check['date_added']); ?>
												<?php 
												echo '(';
												echo get_username($received_check['user_id'], ($received_check['oc_user_id']));
												echo ')';
												?>
											</b>
											<?php
										}
										?>
									</td>	    	       
								</tr>
								<tr>
									<?php
									if($rma_return['date_qc']):
										?>
									<td>
										<b>QC Date: <?php echo americanDate($rma_return['date_qc']); ?>
											<?php if ($rma_return['date_qc']) {
												$xuser_id = $db->func_query_first("SELECT user_id, oc_user_id FROM inv_return_history WHERE rma_number='" . $rma_return['rma_number'] . "' AND return_status='In QC' order by return_history_id desc");
												echo '(';
												echo get_username($xuser_id['user_id'], ($xuser_id['oc_user_id']));
												echo ')';
											}
											?>
										</b>
									</td>
									<td>|</td>
									<?php
									endif;
									if($rma_return['rma_status']=='Completed'):
										?>
									<td><b>Completed Date: <?php echo americanDate($rma_return['date_completed']); ?> <?php
										if ($rma_return['date_completed']) {
											$xuser_id = $db->func_query_first("SELECT user_id, oc_user_id FROM inv_return_history WHERE rma_number='" . $rma_return['rma_number'] . "' AND return_status='Completed' order by return_history_id desc");
											echo '(';
											echo get_username($xuser_id['user_id'], ($xuser_id['oc_user_id']));
											echo ')';
										}
										?></b></td>	 
										<?php
										endif;
										?>   	       
									</tr>
									<tr>
										<td><b>Status:</b> <?php echo ($rma_return['rma_status'] == 'In QC') ? 'QC Completed' : $rma_return['rma_status']; ?></td>
										<td>|</td>	  
										<td><strong>RMA Created</strong>: <?php echo americanDate($rma_return['date_added']);?></td>	    
									</tr>	
									<tr>
										<td><b>Payment Method:</b></td>
										<td>|</td>	  
										<td><?php echo $rma_return['payment_method']; ?></td>	    
									</tr>
									<?php if($_SESSION['login_as']=='admin') { ?>
									<?php $bb_statuses = array('Awaiting'=>'Awaiting','Received'=>'Received','In QC'=>'QC Completed','Completed'=>'Completed'); ?>
									<tr>
										<td style="font-weight:bold">Change Status: </td>
										<td>|</td>
										<td style="font-weight:bold" >
											<select class="change_status" name="change_status">
												<?php
												foreach($bb_statuses as $key => $value)
												{
													?>
													<option value="<?=$key;?>" <?=($rma_return['rma_status']==$key?'selected':'');?>><?=$value;?></option>
													<?php
												}
												?>
											</select>
										</td>
									</tr>
									<?php } ?>
								</table>
							</td>
						</tr> 
						<tr>
							<td colspan="3"> <table cellpadding="5" style="width:100%">
								<tr>
									<td style="font-weight:bold" colspan="4">Extra Details:</td>
									<td>Storefront Approvals</td>
									<tr>
										<td>
											Manager: <?=$manager_name['name'];?>
										</td>
									</tr>
									<tr>
										<td>
											QC Lead: <?=$qc_name['name'];?>
										</td>
									</tr>
								</tr>
								<tr>
									<td style="font-weight:bold">Browser Details:</td>
									<td><?php echo ($rma_return['extra'] ? $rma_return['extra'] : 'Not Found'); ?></td>
									<td style="font-weight:bold">Transaction ID:</td>
									<td style="width: 350px;"><?php
										if (strtolower($rma_return['payment_method']) == 'paypal' or strtolower($rma_return['payment_method']) == 'paypal express') {
											$transactions =  $db->func_query('SELECT transaction_id,amount FROM inv_transactions WHERE order_id="' . $rma_return['order_id'] . '" order by id asc');
											foreach ($transactions as $transaction) {
												if ($transaction['amount']<0) {
													echo 'Refund Transaction ID:'.$transaction['transaction_id'].' ($'.number_format($transaction['amount'],2).')<br>';		
												} else {
													echo 'Order Payment Transaction ID:'.$transaction['transaction_id'].' $'.number_format($transaction['amount'],2).'<br>';
												}
											
											}
										} else if (strtolower($rma_return['payment_method']) == 'credit card / debit card (authorize.net)') {
											echo 'Tran ID: (' . $db->func_query_first_cell('SELECT trans_id FROM oc_authnetaim_admin WHERE order_id="' . $rma_return['order_id'] . '"') . ')';
										} else {
											echo "N/A";
										}
										?>
									</td>
								</tr>
							</table>
						</td>
					</tr>	
				</table>
				<br />
				<table border="0" >
					<tr>
						<td align="center" style="width: 500px;">
						<br><br>
						<form method="post">
							<table border="1" cellpadding="10" width="50%">
								<tr>
									<?php if ($rma_return['rma_status'] == 'Received'){ ?>
									<td align="center">
										Tracking No:<input type="text" class="tracker" required="" name="tracking_number" value="<?php echo $rma_return['tracking_number']; ?>">
									</td>
									<td align="center">
										Carrier:<?php echo createField("carrier", "carrier" , "select" , $rma_return['carrier'] , $carriers,'required="" class="carrier"');?>
									</td>

									<?php } else {?>
									<td align="center">
										Tracking No:<input type="text" class="tracker" name="tracking_number" value="<?php echo $rma_return['tracking_number']; ?>">
									</td>
									<td align="center">
										Carrier:<?php echo createField("carrier", "carrier" , "select" , $rma_return['carrier'] , $carriers,'class="carrier"');?>
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
					$tracker = $db->func_query_first("SELECT * FROM inv_tracker WHERE shipment_id='".$rma_return['rma_number']."'");
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
			<p>
				Return Source:
				<select name="source">
					<option value="mail">Mail</option>
					<option value="manual" <?php if ($rma_return['source'] == 'manual'): ?> selected="selected" <?php endif; ?>>Manual</option>
						<option value="storefront" <?php if ($rma_return['source'] == 'storefront'): ?> selected="selected" <?php endif; ?>>Storefront</option>
					</select>
					&nbsp&nbsp&nbsp
					Shipping Cost:
					<input type="text" name= "shipping_cost" id="shipping_cost" value="<?php echo $rma_return['shipping_cost']; ?>">
				</p>
				<br />
				<p>
					Printer:
					<select name="printerid" id="printerid">
						<option value="">Do Not Print</option>
						<?php foreach ($printers as $printer): ?>
							<option value="<?php echo $printer['id'] ?>" <?php if ($printer['id'] == $return_items[0]['printer']): ?> selected="selected" <?php endif; ?>>
								<?php echo $printer['value'] ?>
							</option>
						<?php endforeach; ?>
					</select>
					<?php if ($rma_return['rma_status'] == 'Completed' ||  $rma_return['rma_status'] == 'In QC'){ ?>
					&nbsp&nbsp&nbsp&nbsp
					<input type="submit" name="reprint_labels" value="Reprint Labels" class="button" />
				<?php } ?>
				</p>
				<br />
				<table border="0" >
					<tr>
						<td style="width: 500px;">
						<br><br>
							<table border="1" cellpadding="10" width="50%">
								<tr>
									<td align="center">
										<textarea rows="5" cols="50" name="comments"></textarea>
									</td>
								</tr>
								<tr>
									<td align="center">
										<input type="submit" class="button" name="addcomment" onclick="removeTrackerRequired();" value="Add Comment" />	  		  	 
									</td>
								</tr>	
							</table>
							<input type="hidden" name="return_id" value="<?php echo $rma_return['id'] ?>" />
							<input type="hidden" name="return_number" value="<?php echo $rma_return['return_number'] ?>" />
						</td>
						<td align="center">
						    <div style="height:250px;width:600px;overflow:auto;">
							<table style="width: 500px;" cellpadding="10" border="1" width="50%">
							<h2>Comment History</h2>
								<tr>
									<th>Date</th>
									<th>User</th>
									<th>Comment</th>
								</tr>
								<?php foreach ($comments as $comment): ?>
									<tr>
										<td><?php echo americanDate($comment['comment_date']); ?></td>
										<?php if($comment['user_id'] == '-2'){?>
										<td>	<?php echo linkToProfile($rma_return['email'], $host_path); ?></td>
										<?php	} else { ?>
										<td><?php echo ($comment['oc_user_id']!= 0) ? get_username($comment['oc_user_id'],true) : get_username($comment['user_id']); ?></td>
										<?php } ?>
										<td><?php echo $comment['comments']; ?></td>
									</tr>
								<?php endforeach; ?>
							</table>
							</div>
						</td>
					</tr>
				</table>
				<br><br>
				
				
				<table border="1" cellpadding="10" cellspacing="0" width="100%">
					<tr>
						<th>#<input type="checkbox" onclick="toggleCheck(this)" /></th>
						
						<th>RB<input type="checkbox" data-class="rbCheck" onclick="selectAllCheck(this)" /></th>
						
						<th>SKU</th>
						<th>Title</th>
						<th>QTY</th>
						<th>Return Reason</th>
						<?php if ($_SESSION['manage_returns']): ?>
							<th>How to Process</th>
							<th>Part Vendor</th>
							<th>Condition</th>
						<?php endif; ?>		
						<?php if ($_SESSION['return_decision'] == '1' && ($rma_return['rma_status'] == 'In QC' || $rma_return['rma_status'] == 'Completed')): ?>
							<th>Decision</th>
						<?php endif; ?>	
						<?php if ($_SESSION['complete_return'] == '1' && ($rma_return['rma_status'] == 'In QC' || $rma_return['rma_status'] == 'Completed')): ?>
							<th>Amount</th>
						<?php endif; ?>	
						<th>Comment</th>
						<!-- <th>Printer</th> -->
                        <th>Images</th> 
							
						<?php if ($rma_return['rma_status'] == 'Awaiting'): ?>
							<th>Remove</th>
						<?php endif; ?>		
					</tr>
					<?php $decisions = array(); ?>
					<?php
					$rejected_items = array();
					?>
					<?php foreach ($return_items as $return_item): ?>
						<?php
						if( $return_item['item_condition'] == 'Customer Damage' || $return_item['item_condition'] == 'Not PPUSA Part' || $return_item['item_condition'] == 'Over 60 days')
						{
							$rejected_items[] = array(
								'id'=>$return_item['id'],
								'qty'=>1
								);	
						}
						?>
						<?php
						$images = $db->func_query("select * from inv_return_item_images where return_item_id = '" . $return_item['id'] . "'");
						$decisionCheckQuery = $db->func_query_first("SELECT * FROM inv_return_decision WHERE return_item_id='" . $return_item['id'] . "'");
						?>
						<tr>
							<?php $decisions[] = $return_item['item_condition']; ?>
							<?php $den = ($return_item['decision'] == 'Denied' && $return_item['item_condition'] == 'Customer Damage') ? 1 : 0; ?>
							<td>
								<?php //if ($rma_return['rma_status'] == 'Completed' && in_array($return_item['item_condition'], array('Customer Damage'))): ?>
								<?php if ($rma_return['rma_status'] == 'Completed' ): ?>
									<input  type="checkbox" name="return_item[<?php echo $return_item['id']; ?>]" class="item_checkbox" value="<?php echo $return_item['id']; ?>" onchange="checkSelectBoxes()" />
								<?php elseif ($rma_return['rma_status'] != 'Completed'): ?>
									<input  type="checkbox" name="return_item[<?php echo $return_item['id']; ?>]" class="item_checkbox" value="<?php echo $return_item['id']; ?>" onchange="checkSelectBoxes()" <?php echo ($decisionCheckQuery ? 'disabled=""' : ''); ?> />
								<?php endif; ?> 		
							</td>
							
							<td class="rbCheck">
								<input type="checkbox" name="add_to_box[<?php echo $return_item['id']; ?>]" <?php echo ($return_item['add_to_box'])? 'checked="checked"': '';?> value="1" />
							</td>
					
							<td width="70">
								<input type="hidden" name="product_sku[<?php echo $return_item['id']; ?>]" value="<?php echo $return_item['sku'] ?>" />
								<input type="hidden" name="new_sku[<?php echo $return_item['id']; ?>]" value="<?php echo $return_item['sku']; ?>" />
								<input type="hidden" name="return_item_id[<?php echo $return_item['id']; ?>]" value="<?php echo $return_item['return_item_id']; ?>" />
								<input type="hidden" name="return_box_id[<?php echo $return_item['id']; ?>]" value="<?php echo $return_item['box_id']; ?>" />
								<?= linkToProduct($return_item['sku'], $host_path); ?>
							</td>
							<td width="150"><?php echo $return_item['title']; ?></td>
							<td><?php echo $return_item['quantity']; ?></td>
							<td width="150"><?php echo $return_item['return_code']; ?></td>
							<?php if ($_SESSION['manage_returns']): ?>
								<td>
									<input type="text" name="how_to_process[<?php echo $return_item['id']; ?>]" value="<?php echo $return_item['how_to_process'] ?>"  />
								</td>
								 <td>
                                    
                                    <div id="item_vendor_rtv_<?php echo $return_item['id']; ?>">
                                    <?php $vendors = $db->func_query("SELECT id , name  from inv_users where group_id = '1' order by lower(name)");
                                    $default_vendor_id = $db->func_query_first_cell("SELECT vendor FROM inv_product_vendors WHERE product_sku='".$return_item['sku']."' limit 1");
                                     ?>
                                        <select  name="item_vendor_rtv_[<?php echo $return_item['id']; ?>]" id="vendor_rtv_<?php echo $return_item['id']; ?>" class="vendor_div" data-return-id="<?php echo $return_item['id']; ?>" style="width:135px;">
                                            <option value="">Select One</option>
                                            <?php foreach ($vendors as $vendor): ?>
                                                <option  value="<?php echo $vendor['id'] ?>" <?php if ($vendor['id'] == $return_item['rtv_vendor_id'] || $vendor['id']==$default_vendor_id): ?> selected="selected" <?php endif; ?> data-class="avatar" data-style="background-image: url(&apos;<?php echo getVendorPic($vendor['id']) ;?>?d=monsterid&amp;r=g&amp;s=16&apos;);background-size:16px">
                                                    <?php echo $vendor['name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php
                                    if($return_item['rtv_vendor_id'])
                                    {
                                    	$default_vendor_id = $return_item['rtv_vendor_id'];
                                    }
                                    ?>
                                    <input type="hidden" id="item_selected_box_<?php echo $return_item['id']; ?>" value="<?php echo $return_item['rtv_shipment_id'];?>">
                                    
                            </td> 
								<td>
									<select class="condition" name="item_condition[<?php echo $return_item['id']; ?>]" onchange="unlockBox(this.value,<?php echo $return_item['id']; ?>);unlockBoxRTV(this.value,<?php echo $return_item['id']; ?>)" id="item_condition_<?php echo $return_item['id'];?>">
										<option value="">Select One</option>
										<?php foreach ($item_conditions as $item_condition): ?>
											<option value="<?php echo $item_condition['id'] ?>" <?php if ($item_condition['id'] == $return_item['item_condition']): ?> selected="selected" <?php endif; ?>>
												<?php echo $item_condition['value']; ?>
											</option>
										<?php endforeach; ?>
									</select>
									<br /><br />
									<!-- For Item Issue Only -->
									<div id="item_issue_<?php echo $return_item['id']; ?>" <?php if ($return_item['item_condition'] != 'Item Issue'): ?> style="display:none;" <?php endif; ?>>
										<select name="item_issue[<?php echo $return_item['id']; ?>]" style="width:135px;">
											<option value="">Select One</option>
											<?php foreach ($item_issues as $item_issue): ?>
												<option value="<?php echo $item_issue['name'] ?>" <?php if ($item_issue['name'] == $return_item['item_issue']): ?> selected="selected" <?php endif; ?>>
													<?php echo $item_issue['name']; ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
									<!-- For Item Issue RTV Only -->
									<div id="item_issue_rtv_<?php echo $return_item['id']; ?>" <?php if ($return_item['item_condition'] != 'Item Issue - RTV'): ?> style="display:none;" <?php endif; ?>>
									<?php $reasons = $db->func_query("SELECT * FROM inv_rj_reasons WHERE classification_id = (SELECT classification_id FROM oc_product where model = '". $return_item['sku'] ."' limit 1)"); ?>
										<select name="item_issue_rtv_[<?php echo $return_item['id']; ?>]"  style="width:135px;">
											<option value="">Select One</option>
											<?php foreach ($reasons as $reason): ?>
												<option value="<?php echo $reason['name'] ?>" <?php if ($reason['name'] == $return_item['item_issue']): ?> selected="selected" <?php endif; ?>>
													<?php echo $reason['name']; ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
									
									<br>
									<div id="vendor_rtv_ship_<?php echo $return_item['id']; ?>" <?php if ($return_item['item_condition'] != 'Item Issue - RTV'): ?> style="display:none;" <?php endif; ?>>
										<select id="vendor_rtv_ship_load<?php echo $return_item['id']; ?>" name="vendor_rtv_ship_[<?php echo $return_item['id']; ?>]" style="width:135px;">
										<?php if ($return_item['rtv_shipment_id'])  { ?>
										<option value="">Create New Box</option>
											<option value=""><?php echo $return_item['rtv_shipment_id'] ?></option>
											<?php } else if ($return_item['rtv_vendor_id']) { ?>
											<option value="">Create New Box</option>
											<?php 
												$pre_pop_rtvs = $db->func_query("SELECT * FROM inv_rejected_shipments where status = 'Pending' AND vendor = '".$return_item['rtv_vendor_id']."'");
												foreach ($pre_pop_rtvs as $value) {
											?>	
												<option value="<?php echo $value['package_number'] ?>"><?php echo $value['package_number'] ?></option>
											<?php } 
											} else { ?>
											<option value="">Create New Box</option>
											<?php } ?> 
										</select>
									</div>
									<br>
									<input type="text" data-type="monthyear" name="production_date_[<?php echo $return_item['id']; ?>]" id="production_date_<?php echo $return_item['id']; ?>" <?php if ($return_item['item_condition'] != 'Item Issue - RTV'): ?> style="display:none;" <?php endif; ?>" value="<?php echo $return_item['production_date']; ?>" <?php
									if(!$_SESSION['edit_production_date'])
									{
										echo 'readOnly';
									}
									?>
									>
									
								</td>
								<script>
                                   loadRTVship('<?php echo $default_vendor_id;?>','<?php echo $return_item['id'];?>');
                                    </script>
								<?php if ($_SESSION['return_decision'] == '1' && ($rma_return['rma_status'] == 'In QC' || $rma_return['rma_status'] == 'Completed')): ?>
									<td>
										<?php if ($return_item['item_condition'] != 'Not PPUSA Part' && $return_item['item_condition'] != 'Over 60 days'): ?>
											<?php
											if($decisionCheckQuery)
											{
												echo $decisionCheckQuery['action'];
											}
											else
											{
												?>
												<select class="decision" id="decision_<?php echo $return_item['id'];?>" name="decision[<?php echo $return_item['id']; ?>]" >
													<option value="">Please Select</option>
													<?php
													foreach ($decisionsx as $decision) {
														?>
														<option value="<?php echo $decision['id']; ?>" <?php if ($decision['id'] == $return_item['decision']) echo 'selected'; ?>><?php echo $decision['value']; ?></option>
														<?php
													}
													?>
												</select>
												<?php
											}
											?>
											<br />
											<div id="exception_box_<?php echo $return_item['id'];?>" style="display:none"  >
												<div <?php if($decisionCheckQuery)
												{ echo 'style="display:none"'; } ?> >
												<input type="checkbox" id="item_exception_<?php echo $return_item['id'];?>" onchange="showException(this,'<?php echo $return_item['id'];?>')" name="item_exception[<?php echo $return_item['id'];?>]" value="1" <?php if($return_item['item_exception']==1) echo 'checked';?>  /> Exception
											</div>
										</div>
									<?php else: ?>
										<?php echo createField("decision[" . $return_item['id'] . "]", "decision_".$return_item['id']."", "select", $return_item['decision'], array(array('id' => 'Denied', 'value' => 'Denied'))); ?>
										<div id="exception_box_<?php echo $return_item['id'];?>"   >
											<div <?php if($decisionCheckQuery)
											{ echo 'style="display:none"'; } ?> >
											<input type="checkbox" id="item_exception_<?php echo $return_item['id'];?>" onchange="showException(this,'<?php echo $return_item['id'];?>')" name="item_exception[<?php echo $return_item['id'];?>]" value="1" <?php if($return_item['item_exception']==1) echo 'checked';?>  /> Exception
										</div>
									</div>
								<?php endif; ?>
							</td>
						<?php endif; ?>
						<?php if ($_SESSION['complete_return'] == '1' && ($rma_return['rma_status'] == 'In QC' || $rma_return['rma_status'] == 'Completed')): ?>
							<td style="width: 80px;">
							<?php if ($return_item['manual_amount_comment']){ ?>
							<?php echo $return_item['manual_amount_comment'];
							$current_price = $db->func_query_first('SELECT price,sale_price from oc_product WHERE sku = "'.$return_item['sku'].'"'); if ($current_price['sale_price'] != 0.0000) {
								$cur_price = $current_price['sale_price'];
							} else {
								$cur_price = $current_price['price'];
							}
								 ?><br><br> Current Price: $<?php echo number_format($cur_price,2);?><br><br>
								<?php } ?>
								<input class="price"  type="text" id="product_price_<?php echo $return_item['id'];?>" name="product_price[<?php echo $return_item['id']; ?>]" value="<?= ($den) ? '0' : $return_item['price']; ?>" data-price="<?= $return_item['price']+$return_item['discount_amount']; ?>" data-id="<?php echo $return_item['id'];?>" onkeypress="updateItemPrice(this,event)" />
								<span></span><br>

								<input type="checkbox" name="restocking[<?php echo $return_item['id']; ?>]" value="1" onchange="populateRestocking(<?php echo $return_item['id'];?>,this)" <?php echo ($return_item['restocking']==1)?'checked':''; ?>/> Re-stocking
								<div id="div_<?php echo $return_item['id'];?>" style="background-color:#D0D0D0;<?php echo ($return_item['restocking']==1)?'':'display:none'; ?>">
									<input type="radio" name="restocking_grade[<?php echo $return_item['id'];?>]" value="A" data-discount="10" data-item-id="<?php echo $return_item['id'];?>" onchange="updateStockingPrice('<?php echo $return_item['id'];?>',this)" <?php echo ($return_item['restocking_grade']=='A')?'checked':''; ?> /> A<br />
									<input type="radio" name="restocking_grade[<?php echo $return_item['id'];?>]"  value="B" data-discount="20" data-item-id="<?php echo $return_item['id'];?>"  onchange="updateStockingPrice('<?php echo $return_item['id'];?>',this)" <?php echo ($return_item['restocking_grade']=='B')?'checked':''; ?> /> B<br />
									<input type="radio" name="restocking_grade[<?php echo $return_item['id'];?>]" value="C" data-discount="30" data-item-id="<?php echo $return_item['id'];?>"  onchange="updateStockingPrice('<?php echo $return_item['id'];?>',this)" <?php echo ($return_item['restocking_grade']=='C')?'checked':''; ?> /> C<br />
									<input type="radio" name="restocking_grade[<?php echo $return_item['id'];?>]" value="D" data-discount="50" data-item-id="<?php echo $return_item['id'];?>"  onchange="updateStockingPrice('<?php echo $return_item['id'];?>',this)" <?php echo ($return_item['restocking_grade']=='D')?'checked':''; ?> /> D<br />
									<input type="radio" name="restocking_grade[<?php echo $return_item['id'];?>]" value="Other" data-discount="<?php echo $return_item['discount_per'];?>" data-item-id="<?php echo $return_item['id'];?>" onchange="$(this).parent().find('input[type=\'text\']').toggle(); updateStockingPrice('<?php echo $return_item['id'];?>',this)" <?php echo ($return_item['restocking_grade']=='Oth')?'checked':''; ?> /> Other<br />
									<input type="text" <?php echo ($return_item['restocking_grade']=='Oth')?'':'style="display: none;"'; ?> value="<?php echo $return_item['discount_per'];?>" onchange="$(this).parent().find('input[value=\'Other\']').attr('data-discount', $(this).val()).trigger('change');" />
								</div>
								<input type="hidden" name="discount_amount[<?php echo $return_item['id'];?>]" id="discount_amount_<?php echo $return_item['id'];?>" value="<?php echo $return_item['discount_amount'];?>" />
								<input type="hidden" name="discount_per[<?php echo $return_item['id'];?>]" id="discount_per_<?php echo $return_item['id'];?>" value="<?php echo $return_item['discount_per'];?>" />
							</td>
							<?php
							else:
								?>
							<td style="display:none">
								<input  type="hidden" id="product_price_<?php echo $return_item['id'];?>" name="product_price[<?php echo $return_item['id']; ?>]" value="<?=$return_item['price']; ?>" data-price="<?= $return_item['price']+$return_item['discount_amount']; ?>" />
								<input type="checkbox" name="restocking[<?php echo $return_item['id']; ?>]" value="1" onchange="populateRestocking(<?php echo $return_item['id'];?>,this)" /> Re-stocking
								<div id="div_<?php echo $return_item['id'];?>" style="background-color:#D0D0D0;display:none"><input type="radio" name="restocking_grade[<?php echo $return_item['id'];?>]" value="A" data-discount="10" data-item-id="<?php echo $return_item['id'];?>" onchange="updateStockingPrice('<?php echo $return_item['id'];?>',this)" /> A<br />
									<input type="radio" name="restocking_grade[<?php echo $return_item['id'];?>]"  value="B" data-discount="20" data-item-id="<?php echo $return_item['id'];?>"  onchange="updateStockingPrice('<?php echo $return_item['id'];?>',this)" /> B<br />
									<input type="radio" name="restocking_grade[<?php echo $return_item['id'];?>]" value="C" data-discount="30" data-item-id="<?php echo $return_item['id'];?>"  onchange="updateStockingPrice('<?php echo $return_item['id'];?>',this)" /> C<br />
									<input type="radio" name="restocking_grade[<?php echo $return_item['id'];?>]" value="D" data-discount="50" data-item-id="<?php echo $return_item['id'];?>"  onchange="updateStockingPrice('<?php echo $return_item['id'];?>',this)" /> D<br /></div>
									<input type="hidden" name="discount_amount[<?php echo $return_item['id'];?>" id="discount_amount_<?php echo $return_item['id'];?>" value="<?php echo $return_item['discount_amount'];?>" />
									<input type="hidden" name="discount_per[<?php echo $return_item['id'];?>" id="discount_per_<?php echo $return_item['id'];?>" value="<?php echo $return_item['discount_per'];?>" />
								</td>
								<?php
								endif;
								?>
								<td>
									Customer:<input type="text" size="20" name="return_comment[<?php echo $return_item['id']; ?>]" value="<?php echo $return_item['comment'] ?>"  /><br><br>
									QC:<input type="text" size="20" name="return_comment_qc[<?php echo $return_item['id']; ?>]" value="<?php echo $return_item['qc_comment'] ?>"  />
								</td>
								<!-- <td>
									<select class="printer" required="" name="printer[<?php echo $return_item['id']; ?>]" id="printer_<?php echo $return_item['id'];?>">
										<option value="">Select One</option>
										<?php foreach ($printers as $printer): ?>
											<option value="<?php echo $printer['id'] ?>" <?php if ($printer['id'] == $return_item['printer']): ?> selected="selected" <?php endif; ?>>
												<?php echo $printer['value'] ?>
											</option>
										<?php endforeach; ?>
									</select>
								</td> -->
								<td>
									<input style="display:none;" onchange="document.forms['returnForm'].submit();" type="file" id="image_path_<?php echo $return_item['id']; ?>" name="image_path[<?php echo $return_item['id']; ?>][]" multiple="multiple" value="" />
									<a href="javascript://" onclick="jQuery('#image_path_<?php echo $return_item['id']; ?>').click();">Upload</a>
									<br /><br />
									<?php if ($images): ?>
										<table align="left">
											<tr>
												<?php foreach ($images as $image): ?>
													<td>
														<a href="javascript:void(0)" data-href="<?php echo str_ireplace("../", "", $image['image_path']); ?>" class="img_damaged">
															<img src="<?php echo str_ireplace("../", "", $image['thumb_path']); ?>" width="25" height="25"  />
														</a>	
														<a onclick="if (!confirm('Are you sure?')) {
															return false;
														}" href="return_detail.php?rma_number=<?php echo $rma_number ?>&action=remove&image_id=<?php echo $image['id'] ?>&return_item_id=<?php echo $return_item['id'] ?>">X</a>
													</td>
												<?php endforeach; ?>
											</tr>
										</table>
									<?php endif; ?>
								</td>
							<?php endif; ?>  
                           
                            
							<?php if ($rma_return['rma_status'] == 'Awaiting'): ?>
								<td>
									<a class="fancybox fancybox.iframe" href="<?php echo $host_path; ?>/popupfiles/rma_rmsku.php?return_id=<?php echo $rma_return['id'] ?>&id=<?php echo $return_item['id'] ?>" onclick="if (!confirm('Are you sure?')) {
										return false;
									}">X</a>
								</td>
							<?php endif; ?>
						</tr>
						<script>
							unlockBox($('#item_condition_<?php echo $return_item['id'];?>').val(),<?php echo $return_item['id']; ?>);
							$('#decision_<?php echo $return_item['id'];?> option[value="<?php echo $return_item['decision'];?>"]').attr("selected", "selected");
						</script>
					<?php endforeach; ?>
				</table>
				<a href="" id="decision-anchor" class="fancybox3 fancybox.iframe" style="display:none"></a>
				<br /><br />
				<?php if ($_SESSION['return_decision'] && $rma_return['rma_status'] == 'In QC'): ?>
					<?php
					if (($_SESSION['complete_storefront_return'] == 1 and $rma_return['source'] == 'storefront') or ( $_SESSION['complete_mail_return'] == 1 and $rma_return['source'] == 'mail') or ( $_SESSION['complete_return'] or $_SESSION['login_as'] == 'admin')) {
						?>
						<div>
							<input type="button" id="issue_replacement" value="Issue Replacement" class="button xdecision" /> 
							<?php
							if ($rma_return['store_type'] == 'amazon'):
								?>	
							<input type="button" value="Amazon Refund"  class="button xdecision" id="issue_amazon_refund" /><br /> <br />
							<?php
							else:
								?>
							<input type="button" id="issue_sc" value="Issue Store Credit" class="button xdecision" /> 
							<input type="button" id="issue_refund" value="Issue Refund" class="button xdecision" />
							<input type="button" id="denied" value="Deny" class="button xdecision" /> <br /><br />
							<?php
							endif;
							?>
						</div>
						<?php
					}
					?>
				<?php endif; ?>
				<input type="hidden" name="order_id" value="<?php echo $rma_return['order_id'] ?>" />
				<input type="hidden" name="return_id" value="<?php echo $rma_return['id'] ?>" />
				<input type="hidden" name="return_number" value="<?php echo $rma_return['return_number'] ?>" />
				<input type="hidden" id="selected_items" value="" />
				<input type="hidden" name="decision_save" value="1" />
				<input type="hidden" id="act" value="">
				<?php if (in_array($rma_return['rma_status'], array('Awaiting'))): ?>
					<input type="submit" name="received" value="Received" onmouseover="$('#act').val($(this).val())" onclick="if (!confirm('Are you sure?')) {
						return false;
					}" class="button" />
				<?php endif; ?>
				<?php //if ($rma_return['rma_status'] != 'Completed'): ?>
				<input type="submit" name="save" onmouseover="$('#act').val($(this).val())" value="Save" class="button" />
				<?php if ($rma_return['rma_status'] == 'Completed' ||  $rma_return['rma_status'] == 'In QC'){ ?>
				<?php } ?>
				<?php //endif; ?>
				<?php
				if($returns['store_type']=='amazon_fba')
				{
					?>
					<a  class="button fancybox2 fancybox.iframe " name="amazon_return" id="amazon_return" href="amazon_fba_return_label.php?rma_number=<?=$rma_number;?>"> Return Label on RMA</a>
					<?php	
				}
				?>
				<?php
				if($rma_return['rma_status']=='Completed' and $rejected_items and $rma_return['denied_order_created']==0)
				{
					?>
					<a href="create_return_shipment.php?rma_number=<?php echo $rma_number;?>&rejected_items=<?php echo base64_encode(json_encode($rejected_items));?>&zone=<?=$rma_return['state'];?>" id="create_return_shipment"  class="button fancybox2 fancybox.iframe "  > Create Return Shipment</a>
					<?php	
				}
				?>
				<?php if ($_SESSION['manage_returns'] && in_array($rma_return['rma_status'], array('Received'))): ?>
					<input type="submit" name="qcdone" onmouseover="$('#act').val($(this).val())" value="Complete QC" class="button" />
				<?php endif; ?>		
				<?php if ($_SESSION['return_decision'] && $rma_return['rma_status'] == 'In QC'): ?>		
					<?php
                        //if($_SESSION['complete_return'] or $_SESSION['login_as'] == 'admin' or $_SESSION['complete_storefront_return'] or $_SESSION['complete_mail_return'])
                        //{
					if (($_SESSION['complete_storefront_return'] == 1 and $rma_return['source'] == 'storefront') or ( $_SESSION['complete_mail_return'] == 1 and $rma_return['source'] == 'mail') or ( $_SESSION['complete_return'] or $_SESSION['login_as'] == 'admin')) {
						?>
						<input type="submit" name="completed" onmouseover="$('#act').val($(this).val())" value="Complete Return" class="button" />
						<?php
					}
					?>
				<?php endif; ?>		
				<?php if ($rma_return['rma_status'] == 'Completed' && $rma_return['denied_order_created'] == 0 && in_array('Customer Damage', $decisions)): ?>
	<!-- <input type="submit" name="deniedReturnOrder" value="Denied Return order"  onclick="if (!confirm('Are you sure?')) {
	return false;
}" class="button" /> -->
<?php endif; ?>
<input type="hidden" id="shipping_method" value="">

</form>
<script type="text/javascript">
	function verifyupdates () {
		var error = true;
		var act = $('#act').val();
		if (act == 'Complete QC' || act == 'Complete Return') {
			$('.condition').each(function() {
				if ($(this).val() == '') {
					error = false;
				}
			});
		}
		if (!error) {
			alert('Please Select Condition');
		}
		return error;
	}
</script>
<?php if ($rma_return['rma_status'] != 'Awaiting') { ?>
<br /><br />
<div align="center">
	<h3>Send Email</h3>
	<table width="70%" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
		<form method="post" action="" id="email_form">
			<tr>
				<td>Canned Message:</td>
				<td>
					<?php $canned_messages = $db->func_query('SELECT * FROM `inv_canned_message` WHERE `catagory` = "2" AND `type` = "Canned"'); ?>
					<select name="canned_id" id="canned_message">
						<option value=""> --- Custom --- </option>
						<?php foreach ($canned_messages as $canned_message) { ?>
						<option value="<?= $canned_message['canned_message_id']; ?>"><?= $canned_message['name']; ?></option>
						<?php } ?>                        
					</select>
					<input type="hidden" name="total_formatted" value="<?= $emailInfo['total_formatted']; ?>"/>
				</td>
			</tr>
			<tr>
				<td>Title</td>
				<td>
					<input type="text" name="title" id="canned_title" value=""/>
					<input type="hidden" id="selected_products" value="" name="selected_products" />
				</td>
			</tr>
			<tr>
				<td>Subject</td>
				<td><input type="text" name="subject" id="canned_subject" value=""/></td>
			</tr>
			<tr>
				<td>Message:</td>
				<td><textarea name="comment" id="comment" class="comment-box" cols="40" rows="8" style="width: 99%"></textarea></td>
				<script>
					CKEDITOR.replace( 'comment' );
				</script>
			</tr>
			<tr>
				<td></td>
				<td><label class="addsd" for="signature_check"><input type="checkbox" id="signature_check" /> Add Signature</label><label class="addsd" for="disclaimer_check"><input type="checkbox" id="disclaimer_check" /> Add Disclaimer</label></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="button" name="sendemail" onclick="return sendEmailForm(this);" value="Send Email" type="submit"></td>
				<script type="text/javascript">
					function sendEmailForm(t) {
						if ($('#canned_subject').val() == '' || $('#canned_title').val() == '') {
							alert('Please select canned message Or write your own');
							return false;
						} else {
							if ($('#selected_products').val() == '') {
								if (confirm('Press Cancel to select products or Ok to send Email')) {
									return true;
								}
							} else {
								return true;
							}
							return false;
						}
						return false;
					}
				</script>
			</tr>
		</form>
	</table>
	<ul style="display:none;">
		<textarea id="disclaimer"><div contenteditable="false"><?= $db->func_query_first_cell('SELECT `signature` FROM `inv_signatures` WHERE `type` = 1'); ?></div></textarea>
		<?php $src = $path .'files/sign_' . $_SESSION['user_id'] . ".png"; ?>
		<textarea id="signature"><div contenteditable="false"><?= shortCodeReplace($adminInfo, $db->func_query_first_cell('SELECT `signature` FROM `inv_signatures` WHERE `user_id` = "'. $_SESSION['user_id'] .'" AND type = 0')); ?> <?= (file_exists($src))? '<img src="'. $host_path .'files/sign_' . $_SESSION['user_id'] . '.png?'. time() .'" />': ''; ?></div></textarea>
		<?php foreach ($canned_messages as $canned_message) { ?>
		<textarea id="canned_<?= $canned_message['canned_message_id']; ?>"><?= shortCodeReplace($emailInfo, $canned_message['message']); ?></textarea>
		<li id="title_<?= $canned_message['canned_message_id']; ?>"><?= shortCodeReplace($emailInfo, $canned_message['title']); ?></li>
		<li id="subject_<?= $canned_message['canned_message_id']; ?>"><?= shortCodeReplace($emailInfo, $canned_message['subject']); ?></li>
		<?php } ?>
	</ul>
	<script type="text/javascript">
		$(function() {
			$('#email_form').submit(function () {
				if ($('#canned_title').val() == '' || $('#canned_subject').val() == '') {
					alert('Please Enter Your Message');
					return false;
				}
			});
			$('#canned_message').change(function() {
				var id = $(this).val();
				var message = '';
				if(id > 0) {
					message = $('#canned_' + id).text();
				}
				message = message + '<div id="customeData">';
				if ($('#signature_check').is(':checked')) {
					message = message + $('#signature').text();
				}
				if ($('#disclaimer_check').is(':checked')) {
					message = message + $('#disclaimer').text();
				}
				message = message + '</div>';
				$('#canned_title').val($('#title_' + id).text());
				$('#canned_subject').val($('#subject_' + id).text());
				CKEDITOR.instances.comment.setData(message);
			});
			$('.addsd').click(function() {
				if (!CKEDITOR.instances.comment.document.getById('customeData')) {
					message = CKEDITOR.instances.comment.getData() + '<div id="customeData"></div>';
					CKEDITOR.instances.comment.setData(message);
				}
                //CKEDITOR.instances.comment.document.getById('customeData');
                var message = '';
                if ($('#signature_check').is(':checked')) {
                	message = message + $('#signature').text();
                }
                if ($('#disclaimer_check').is(':checked')) {
                	message = message + $('#disclaimer').text();
                }
                CKEDITOR.instances.comment.document.getById('customeData').setHtml(message);
                //CKEDITOR.instances.comment.setData(message);
            });
			$('#canned_message').keyup(function() {
				$(this).change();
			});
		});
	</script>
</div>
<?php  } ?>
<br /><br />
<?php if ($removed_items): ?>
	<table border="1" cellpadding="10" width="50%">
		<tr>
			<td>SKU</td>
			<td>Remove Reason</td>
		</tr>
		<?php foreach ($removed_items as $removed_item): ?>
			<tr>
				<td><?php echo $removed_item['sku'] ?></td>
				<td><?php echo $removed_item['remove_reason'] ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
	<br /><br />		
<?php endif; ?>
		
<br /><br />  
</div>
</div>		
<script type="text/javascript">
	$('.decision').on('change', function () {
		var whole = $(this).parent().parent();
		var checkBox = whole.find('.item_checkbox');
		var price = whole.find('input.price');
		var condition = whole.find('.condition').val();
		var discVal = $(this).val();
		if (discVal == 'Denied' && condition == 'Customer Damage') {
			if (checkBox.is(':checked')) {
				checkBox.removeAttr('checked');
			}
			//checkBox.attr('disabled', 'disabled');
			//price.val('0');
		} else {
			price.val(price.attr('data-price'));
			//checkBox.removeAttr('disabled');
		}
	});
	$('.conditon').on('change', function () {
		var whole = $(this).parent().parent();
		var checkBox = whole.find('.item_checkbox');
		var price = whole.find('input.price');
		var discVal = whole.find('.decision').val();
		var condition = $(this).val();
		if (discVal == 'Denied' && condition == 'Customer Damage') {
			if (checkBox.is(':checked')) {
				checkBox.removeAttr('checked');
			}
			// checkBox.attr('disabled', 'disabled');
			// price.val('0');
		} else {
			price.val(price.attr('data-price'));
			// checkBox.removeAttr('disabled');
		}
	});
	function viewDecision(obj, return_id) {
		if (obj.value == 'Issue Credit') {
			$('a#decision-anchor').attr('href', 'rma_credit.php?return_id=' + return_id);
			$('a#decision-anchor').click();
		}
		else if (obj.value == 'Issue Replacement') {
			$('a#decision-anchor').attr('href', 'rma_replacement.php?return_id=' + return_id);
			$('a#decision-anchor').click();
		}
		else if (obj.value == 'Issue Refund') {
			if (confirm('Are you sure want to refund?')) {
				setTimeout(function () {
					alert('Error: Problem Communicating with Server, Please try again later');
				}, 5000);
			}
		}
	}
	function toggleCheck(obj) {
		$('.item_checkbox').not(':disabled').prop('checked', obj.checked);
		checkSelectBoxes()
	}
	function checkSelectBoxes() {
		var item_value = '';
		$('.item_checkbox').each(function (index, element) {
			if ($(element).is(':checked')) {
				item_value += $(element).val() + ',';
			}
		});
		$('#selected_items').val(item_value);
		$('#selected_products').val(item_value);
	}
	$('#issue_sc').click(function (e) {
		if ($('#selected_items').val() == '') {
			alert('Please select item first');
			return false;
		}
		$('a#decision-anchor').attr('href', 'rma_credit.php?rma_number=<?php echo $_GET['rma_number']; ?>&email=<?php echo $rma_return['email'];?>&items=' + $('#selected_items').val());
		$('a#decision-anchor').click();
                /*$.ajax({
                 url: "ajax_store_credit.php",
                 type:"POST",
                 data: {order_id: $('input[name=order_id]').val(),items:$('#selected_items').val()},
                 success: function(data) {
                 alert(data);
                 }
             });*/
         });
	$('#issue_replacement').click(function (e) {
		if ($('#selected_items').val() == '') {
			alert('Please select item first');
			return false;
		}
                /*if(!confirm('Are you sure want to proceed?')){
                 return false;	
                 }
                 
                 $.ajax({
                 url: "ajax_replacement.php",
                 type:"POST",
                 data: {order_id: $('input[name=order_id]').val(),items:$('#selected_items').val()},
                 success: function(data) {
                 alert(data);
                 }
             });*/
             $('a#decision-anchor').attr('href', 'rma_replacement.php?rma_number=<?php echo $_GET['rma_number']; ?>&items=' + $('#selected_items').val());
             $('a#decision-anchor').click();
         });
	$('#issue_refund').click(function (e) {
		if ($('#selected_items').val() == '') {
			alert('Please select item first');
			return false;
		}
		$('a#decision-anchor').attr('href', 'rma_refund.php?rma_number=<?php echo $_GET['rma_number']; ?>&items=' + $('#selected_items').val());
		$('a#decision-anchor').click();
	});
	$('#denied').click(function (e) {
		if ($('#selected_items').val() == '') {
			alert('Please select item first');
			return false;
		}
		$('a#decision-anchor').attr('href', 'rma_denied.php?rma_number=<?php echo $_GET['rma_number']; ?>&items=' + $('#selected_items').val());
		$('a#decision-anchor').click();
	});
	$('#issue_amazon_refund').click(function (e) {
		if ($('#selected_items').val() == '') {
			alert('Please select item first');
			return false;
		}
		$('a#decision-anchor').attr('href', 'rma_amazon_refund.php?rma_number=<?php echo $_GET['rma_number']; ?>&items=' + $('#selected_items').val());
		$('a#decision-anchor').click();
	});
	function completeReturn()
	{
		var total_checkboxes = $('.item_checkbox').not(':disabled').length;
		var total_checked = $('.item_checkbox:checked').length;
		if (total_checkboxes == total_checked)
		{
			$('input[name=decision_save]').val(0);
			$('input[name=completed]').click();
		}
		else
		{
			location.reload();
		}
	}
	function createReturnShipment()
	{
		$.ajax({
			url: "return_detail.php",
			type: "POST",
			data: {action:'create_return_shipment',rma_number:'<?=$rma_number;?>',rejected_items:'<?=json_encode($rejected_items);?>',shipping_method:$('#shipping_method').val(),shipping_cost:$('#shipping_cost').val()},
			success: function (data) {
										//alert(data);
										alert('Saved!');
										location.reload(true);
									}
								});
	}
	// function loadRTVship(obj){
		
	// 	$.ajax({
	// 		url: "rejected_shipments.php",
	// 		method: "POST",
	// 		data: {action:'load_rtv_shipments',vendor_id_shipments_loader:obj.value},
	// 		dataType: 'json',
	// 		success: function (json) {
	// 			var html = '';
	// 			html += '<option value="">Create New Box</option>';
	// 			for (var i = json['loaded_rtv_shipments'].length - 1; i >= 0; i--) {
	// 						html += '<option value="'+json['loaded_rtv_shipments'][i]['package_number']+'">'+json['loaded_rtv_shipments'][i]['package_number']+'</option>';
 //            		}
 //          $('#vendor_rtv_ship_load').html(html);
	// 			}
	// 		});
	// }
	<?php if($total_products>=25 && ($_SESSION['approve_return_shipp']==1 || $_SESSION['approve_send_label']==1) ) : ?>
	function voidShippingLabel() {
		if(!confirm('Are you sure want to void this Shipment?'))
		{
			return false;
		}
		$.ajax({
			url: "return_detail.php",
			type: "POST",
			data: {action:'void_shipment',shipment:'<?=$rma_number;?>'},
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
<?php endif; ?>
</script>
<script type="text/javascript">
function updateItemPrice(obj,e)
{
	 var keycode = event.keyCode || event.which;
    if(keycode == '13') {
        if(!confirm('Are you sure want to update the price of this item?'))
        {
        	e.preventDefault();
        	return false;
        }

        $.ajax({
			url: "return_detail.php",
			type: "POST",
			data: {action:'update_item_price',item_id:$(obj).attr('data-id'),price:$(obj).val(),rma_number:'<?php echo $rma_number;?>'},
			beforeSend: function () {
							$(obj).next('span').html('<img src="images/loading.gif" width="15" height="15">');
						},
			complete: function () {
				$(obj).next('span').html('<img src="images/check.png" width="15" height="15">');
			},
			success: function (data) {
				
									}
								});

        e.preventDefault();
    }
}
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
  	function removeTrackerRequired(){
  		$(".tracker").removeAttr('required');
  		$(".carrier").removeAttr('required');
  	}
</script>
</body>
</html>