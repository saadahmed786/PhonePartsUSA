<?php
include_once 'auth.php';
include_once 'inc/functions.php';
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
include_once 'trello/trellocard.php';
include_once 'inventory/class.php';
unset($_SESSION['paid_order']);
$trello = new trello();

if(isset($_POST['action']) and $_POST['action']=='apply_voucher')
{
	$json = array();
	if(isset($_POST['vouchers']))
	{

	$codes = explode(',', $_POST['vouchers']);
	$_order_id = $_POST['order_id'];
				// $orderTotal = ($_POST['orders']['store_type'] == 'web')? (float)$xtotal: (float)$po_order_total;
				$orderTotal = $db->func_query_first_cell("SELECT sub_total+tax+shipping_amount FROM inv_orders WHERE order_id='".$_order_id."'");
				$orderPaid = $db->func_query_first_cell("SELECT SUM(amount) from inv_transactions where order_id='".$_order_id."'");
				if(!$orderPaid)
				{
					$orderPaid = $db->func_query_first_cell("SELECT paid_price FROM inv_orders where order_id='".$_order_id."'");
				}
				$orderTotal = $orderTotal - $orderPaid;
				$orderTotal = $orderTotal + (float)$db->func_query_first_cell("SELECT SUM(amount) FROM oc_voucher_history WHERE order_id='".$_order_id."'");
				$totalUsed = 0;
				foreach ($codes as $code) {
					$voucher = $db->func_query_first("SELECT a.*, (a.`amount` + SUM(b.`amount`)) balance FROM `oc_voucher` a LEFT OUTER JOIN `oc_voucher_history` b ON a.`voucher_id` = b.`voucher_id` WHERE a.code = '". $code ."'");
					if (!$voucher['balance']) {
						$voucher['balance'] = $voucher['amount'];
					}

					if ($orderTotal > 0) {

						if ($orderTotal > $voucher['balance']) {
							$orderTotal = $orderTotal - $voucher['balance'];
							$used = $voucher['balance'];
						} else if ($orderTotal <= $voucher['balance']) {
							$used = $voucher['balance'] - $orderTotal;
							$used = $voucher['balance'] - $used;
							$orderTotal = 0;
						}

						$voucher_array = array();
						$voucher_array['voucher_id'] = $voucher['voucher_id'];
						
							$voucher_array['order_id'] = $_order_id;
						
						$totalUsed += $used;
						$voucher_array['amount'] = '-'.$used;
						$voucher_array['date_added'] = date('Y-m-d h:i:s');
						$db->func_array2insert("oc_voucher_history",$voucher_array);

						$db->db_exec("UPDATE inv_orders SET paid_price=paid_price+".(float)$used." WHERE order_id='".$order_id."'");
					}
				}

				$json['success']=1;
			}
			else
			{
				$json['error'] = 1;
			}
			echo json_encode($json);exit;

}
if(isset($_GET['action']) && $_GET['action']=='removeVoucher')
{
	$check = $db->func_query_first("SELECT * FROM oc_voucher_history where order_id='".$db->func_escape_string($_GET['order'])."' and voucher_history_id='".(int)$_GET['voucher_history_id']."'");

	$db->db_exec("DELETE FROM oc_voucher_history WHERE order_id='".$db->func_escape_string($_GET['order'])."' and voucher_history_id='".(int)$_GET['voucher_history_id']."'");
	orderTotal($_GET['order'], true);

	$hdata = array();
		$hdata['order_id'] = $_GET['order'];
		$hdata['comment'] = '<a href="'.$host_path.'vouchers_create.php?edit='.$check['voucher_id'].'">'.$db->func_query_first_cell("SELECT code FROM oc_voucher WHERE voucher_id='".$check['voucher_id']."'").'</a> of Amount $'.number_format($check['amount']*(-1),2).' has been removed';
		$hdata['user_id'] = $_SESSION['user_id'];
		$hdata['date_added'] = date('Y-m-d H:i:s');
		$db->func_array2insert("inv_order_history", $hdata);

		$_SESSION['message'] = 'Voucher Removed Successfully';
		header("Location: viewOrderDetail.php?order=".$_GET['order']);
		exit;

}

if ($_POST['action'] == 'addItemQty') {
$order_id = $_POST['order_id'];
$item_id = $_POST['item_id'];
$product_quantity = (int)$_POST['product_quantity'];
$json = array();
if(!$order_id || !$item_id)
{
	$json['error'] = 'Problem updating the record, please try agian or contact admin';
	echo json_encode($json);
	exit;

}
$db->func_query("UPDATE inv_orders_items SET product_qty=product_qty+".$product_quantity." WHERE order_id='".$db->func_escape_string($order_id)."' and id='".(int)$item_id."'");
$db->func_query("UPDATE inv_orders_items SET product_price=product_qty*product_unit WHERE order_id='".$db->func_escape_string($order_id)."' and id='".(int)$item_id."'");

$db->func_query("UPDATE inv_orders SET is_picked=0,is_packed=0 WHERE order_id='".$order_id."'");

orderTotal($order_id, true);


$data = $db->func_query_first("SELECT product_sku, product_qty,product_price FROM inv_orders_items WHERE order_id='".$db->func_escape_string($order_id)."' and id='".(int)$item_id."'");

$oc_order_id = $db->func_query_first_cell("SELECT order_id FROM oc_order  where cast(order_id as char(50)) = '".$order_id."' OR ref_order_id='".$order_id."'" );

$db->func_query("UPDATE  oc_order_product SET quantity=quantity+".$product_quantity." WHERE order_id='".$oc_order_id."' AND lower(model)='".strtolower($data['product_sku'])."'");
$db->func_query("UPDATE  oc_order_product SET total=quantity*price WHERE order_id='".$oc_order_id."' AND lower(model)='".strtolower($data['product_sku'])."'");

$json = array();
$json['success'] = 1;
$json['product_quantity'] = $data['product_qty'];
$json['product_price'] = $data['product_price'];


$hdata = array();
		$hdata['order_id'] = $order_id;
		$hdata['comment'] = 'Added '.$product_quantity.' quantity to '.$data['product_sku'];
		$hdata['user_id'] = $_SESSION['user_id'];
		$hdata['date_added'] = date('Y-m-d H:i:s');
		$db->func_array2insert("inv_order_history", $hdata);

echo json_encode($json);
exit;
}


// Putting Email info in a Array
if($_POST['follow']) {
	$order_id = $db->func_escape_string($_GET['order']);
	$follow = array();
	$follow['is_followed'] = '1';
	$follow['followed_by'] = $_SESSION['user_id'];
	$db->func_array2update("inv_orders",$follow,"order_id = '$order_id'");
	$_SESSION['message'] = 'Order Followed';
	header("Location:$host_path/viewOrderDetail.php?order=$order_id");
	exit;
}
if($_POST['reset']) {
	$order_id = $db->func_escape_string($_GET['order']);
	$reset = array();
	$reset['shipstation_added'] = '0';
	$db->func_array2update("inv_orders",$reset,"order_id = '$order_id'");
	$_SESSION['message'] = 'Shipstation Reset Successfully';
	header("Location:$host_path/viewOrderDetail.php?order=$order_id");
	exit;
}
if($_POST['reset_fb']) {
	
	$order_id = $db->func_escape_string($_GET['order']);
	$reset = array();
	$reset['fishbowl_uploaded'] = '0';
	$db->func_array2update("inv_orders",$reset,"order_id = '$order_id'");
	$_SESSION['message'] = 'Fishbowl Reset Successfully';
	header("Location:$host_path/viewOrderDetail.php?order=$order_id");
	exit;
}
if($_POST['unfollow']) {
	$order_id = $db->func_escape_string($_GET['order']);
	$follow = array();
	$follow['is_followed'] = '0';
	$follow['followed_by'] = '';
	$db->func_array2update("inv_orders",$follow,"order_id = '$order_id'");
	$_SESSION['message'] = 'Order Un-Followed';
	header("Location:$host_path/viewOrderDetail.php?order=$order_id");
	exit;
}
	
if ($_POST['action'] == 'addp') {
	unset($_POST['action']);
	$item_sku = $db->func_escape_string($_POST['product_sku']);
	$_POST['product_true_cost'] = getTrueCost($item_sku);
	
	$order_d =  $db->func_query_first("SELECT * FROM inv_orders as o, inv_orders_details as od WHERE od.order_id = o.order_id AND o.order_id = '". $_POST['order_id'] ."'");
	$db->db_exec("UPDATE inv_orders SET is_picked=0,is_packed=0 WHERE order_id='".$_POST['order_id']."'");
	if ($order_d['store_type'] == 'web') {
		$p_tax = 0.00;
		if($order_d['bill_state'] == 'Nevada') {
			$tax_detail = $db->func_query_first("SELECT * FROM oc_tax_rate WHERE geo_zone_id=10");
			$p_tax = ($_POST['product_price']*(float)$tax_detail['rate'])/100;
		}
		$product_detail = $db->func_query_first('SELECT * FROM oc_product where sku = "'. $item_sku .'"');
		$product_detail2 = $db->func_query_first('SELECT * FROM oc_product_description where product_id = "'. $product_detail['product_id'] .'"');
		$oc_order_item = array();
		$oc_order_item['order_id'] = $_POST['order_id'];
		$oc_order_item['product_id'] = $product_detail['product_id'];
		$oc_order_item['name'] = $product_detail2['name'];
		$oc_order_item['model'] = $product_detail['model'];
		$oc_order_item['quantity'] = $_POST['product_qty'];
		$oc_order_item['price'] = $_POST['product_unit'];
		$oc_order_item['total'] = $_POST['product_price'];
		$oc_order_item['total'] = $_POST['product_price'];
		$oc_order_item['tax'] = $p_tax;
		$oc_order_item['location_id'] = '1';
		$db->func_array2insert("oc_order_product",$oc_order_item);
	}
	
	if(strlen($item_sku) > 5) {
		$kit_skus = $db->func_query_first("select * from inv_kit_skus where kit_sku = '$item_sku'");
		if($kit_skus) {
			$kit_skus_array = explode(",",$kit_skus['linked_sku']);
			$z=0;
			foreach($kit_skus_array as $kit_skus_row){
				if($z>0)
				{
					$_POST['product_true_cost'] = 0.00;  
				}
				$_POST['product_sku']  = $kit_skus_row;
				$db->func_array2insert("inv_orders_items",$_POST);
				$t_data = array( 'order_id' => $_POST['order_id'],
					'type' => 'added an item to',
					'user_name' => $_SESSION['login_as'],
					'sku' => $_POST['product_sku'],
					'qty' => $_POST['product_qty'],
					'url' => $host_path . 'viewOrderDetail.php?order=' . $_POST['order_id']);
				$template_T = $trello->makeTemplate($t_data);
				if ($db->func_query_first_cell("SELECT LCASE(order_status) from inv_orders WHERE order_id = '". $_POST['order_id'] ."'") != 'estimate') {
					// $trello->addCard($template_T['order_id'], $template_T['name'], $template_T['desc'], 'purple');
				}
				$z++;
			}
			$db->db_exec("update inv_kit_skus SET need_sync = 1 where kit_sku = '$item_sku'");
			$db->func_query();
			$log = "Product ". linkToProduct($item_sku) ." quantity ". $_POST['product_qty'] ." added to Order #". linkToOrder($_POST['order_id']);
			actionLog($log);
			orderTotal($_POST['order_id'], true);
			$json = array('success' => 1);
			echo json_encode($json);
			exit;
		} else {
			$db->func_array2insert("inv_orders_items",$_POST);
			$t_data = array( 'order_id' => $_POST['order_id'],
				'type' => 'added an item to',
				'user_name' => $_SESSION['login_as'],
				'sku' => $_POST['product_sku'],
				'qty' => $_POST['product_qty'],
				'url' => $host_path . 'viewOrderDetail.php?order=' . $_POST['order_id']);
			$template_T = $trello->makeTemplate($t_data);
			if ($db->func_query_first_cell("SELECT LCASE(order_status) from inv_orders WHERE order_id = '". $_POST['order_id'] ."'") != 'estimate') {
				// $trello->addCard($template_T['order_id'], $template_T['name'], $template_T['desc'], 'purple');
			}
			$log = "Product ". linkToProduct($item_sku) ." quantity ". $_POST['product_qty'] ." added to Order #". linkToOrder($_POST['order_id']);
			actionLog($log);
			orderTotal($_POST['order_id'], true);
			$json = array('success' => 1);
			echo json_encode($json);
			exit;
		}
	}
	$json = array('error' => 1);
	echo json_encode($json);
	exit;
}

if(isset($_POST['action']) && $_POST['action']=='removeProductNew')
{
	// exit;
	$removedTableItems = explode(',', $_POST['removeProducts']);
	$counter = 0;
	$order_xid = $_GET['order'];
	while ($removedTableItems[$counter]) {
		$rdata = array();
		$rdata['order_id'] = $removedTableItems[$counter];
		$order_xid = $rdata['order_id'];
		$counter=$counter+1;
		$rdata['item_sku'] = $removedTableItems[$counter];
		$counter=$counter+1;
		$rdata['item_name'] = $removedTableItems[$counter];
		$counter=$counter+1;
		$rdata['date_removed'] = date('Y-m-d H:i:s');
		$rdata['reason'] = $removedTableItems[$counter];
		$counter=$counter+1;
		$rdata['removed_by'] = $removedTableItems[$counter];
		$counter=$counter+1;
		$rdata['item_price'] = $removedTableItems[$counter];
		$counter=$counter+1;
		$db->func_array2insert("inv_removed_order_items", $rdata);
		
		$__item_det = explode("*", $rdata['item_sku']);
		$__item_sku = trim($__item_det[0]);
		$__item_qty = trim($__item_det[1]);

		$db->db_exec("UPDATE inv_orders_items SET product_qty=product_qty-".(int)$__item_qty." WHERE order_id='".$rdata['order_id']."' and product_sku='".$__item_sku."'");
		
		$db->db_exec("UPDATE inv_orders_items SET product_price=product_qty*product_unit WHERE order_id='".$rdata['order_id']."' and product_sku='".$__item_sku."'");

		$db->db_exec("UPDATE oc_order_product SET quantity=quantity-".(int)$__item_qty." WHERE order_id='".$rdata['order_id']."' and model='".$__item_sku."'");

		$db->db_exec("UPDATE oc_order_product SET total=quantity*price WHERE order_id='".$rdata['order_id']."' and model='".$__item_sku."'");

		

		$voucher = $db->func_query_first('SELECT a.*,b.amount as total FROM oc_voucher_history a inner join oc_voucher b on (a.voucher_id = b.voucher_id) WHERE a.amount < 0 AND (a.order_id ="'.$rdata['order_id'].'" OR a.inv_order_id ="'.$rdata['order_id'].'") order by voucher_history_id desc ');
		if ($voucher) {
		 	$balance = $voucher['total'] + $voucher['amount'];
		 	if ($balance > $rdata['item_price'] || $voucher['amount']*-1 > $rdata['item_price']) {
		 		$vouch = array();
		 		$vouch['voucher_id'] = $voucher['voucher_id'];
		 		$vouch['order_id'] = $rdata['order_id'];
		 		$vouch['amount'] = $rdata['item_price'];
		 		$vouch['date_added'] = date('Y-m-d H:i:s');
		 		$db->func_array2insert("oc_voucher_history",$vouch);
		 		$vouch_product = array();
		 		$vouch_product['voucher_id'] = $voucher['voucher_id'];
		 		$vouch_product['order_id'] = $rdata['order_id'];
		 		$vouch_product['sku'] = $rdata['item_sku'];
		 		$vouch_product['price'] = $rdata['item_price'];
		 		$db->func_array2insert("inv_voucher_products",$vouch_product);
		 		$vouch_comment = array();
		 		$vouch_comment['user_id'] = $_SESSION['user_id'];
		 		$vouch_comment['comment'] = 'Consolidated for item '. $rdata['item_sku'].' in Order # '.linkToOrder($rdata['order_id']);
		 		$vouch_comment['date_added'] =  date('Y-m-d H:i:s');
		 		$vouch_comment['voucher_id'] =  $voucher['voucher_id'];
		 		$db->func_array2insert("inv_voucher_comments",$vouch_comment);
		 	}
		 } 
	}
	// $items = explode(',', $_POST['removeProducts']);
	// $order_xid = $db->func_query_first_cell('SELECT order_id FROM `inv_orders_items` WHERE `id` = "'. $items[0] .'"');
	
	$db->db_exec("DELETE FROM inv_orders_items WHERE order_id='".$order_xid."' and product_qty<=0");
	$db->db_exec("DELETE FROM oc_order_product WHERE order_id='".$order_xid."' and quantity<=0");
	orderTotal($order_xid, true);

	$db->func_query("UPDATE inv_orders SET is_adjusted=1 WHERE order_id='".$order_xid."'");
	$db->func_query("UPDATE inv_orders_items SET oadjusted=1 WHERE order_id='".$order_xid."'");
	echo json_encode(array('success'=>1));exit;
}
if (!empty($_POST['removeProducts_old'])) {
	$removedTableItems = explode(',', $_POST['removeTable']);
	$counter = 0;
	while ($removedTableItems[$counter]) {
		$rdata = array();
		$rdata['order_id'] = $removedTableItems[$counter];
		$counter=$counter+1;
		$rdata['item_sku'] = $removedTableItems[$counter];
		$counter=$counter+1;
		$rdata['item_name'] = $removedTableItems[$counter];
		$counter=$counter+1;
		$rdata['date_removed'] = date('Y-m-d H:i:s');
		$rdata['reason'] = $removedTableItems[$counter];
		$counter=$counter+1;
		$rdata['removed_by'] = $removedTableItems[$counter];
		$counter=$counter+1;
		$rdata['item_price'] = $removedTableItems[$counter];
		$counter=$counter+1;
		$db->func_array2insert("inv_removed_order_items", $rdata);
		
		$__item_det = explode("*", $rdata['item_sku']);
		$__item_sku = trim($__item_det[0]);
		$__item_qty = trim($__item_det[1]);

		$db->db_exec("UPDATE inv_orders_items SET product_qty=product_qty-".(int)$__item_qty." WHERE order_id='".$rdata['order_id']."' and product_sku='".$__item_sku."'");
		$db->db_exec("UPDATE inv_orders_items SET product_price=product_qty*product_unit WHERE order_id='".$rdata['order_id']."' and product_sku='".$__item_sku."'");

		

		$voucher = $db->func_query_first('SELECT a.*,b.amount as total FROM oc_voucher_history a inner join oc_voucher b on (a.voucher_id = b.voucher_id) WHERE a.amount < 0 AND (a.order_id ="'.$rdata['order_id'].'" OR a.inv_order_id ="'.$rdata['order_id'].'") order by voucher_history_id desc ');
		if ($voucher) {
		 	$balance = $voucher['total'] + $voucher['amount'];
		 	if ($balance > $rdata['item_price'] || $voucher['amount']*-1 > $rdata['item_price']) {
		 		$vouch = array();
		 		$vouch['voucher_id'] = $voucher['voucher_id'];
		 		$vouch['order_id'] = $rdata['order_id'];
		 		$vouch['amount'] = $rdata['item_price'];
		 		$vouch['date_added'] = date('Y-m-d H:i:s');
		 		$db->func_array2insert("oc_voucher_history",$vouch);
		 		$vouch_product = array();
		 		$vouch_product['voucher_id'] = $voucher['voucher_id'];
		 		$vouch_product['order_id'] = $rdata['order_id'];
		 		$vouch_product['sku'] = $rdata['item_sku'];
		 		$vouch_product['price'] = $rdata['item_price'];
		 		$db->func_array2insert("inv_voucher_products",$vouch_product);
		 		$vouch_comment = array();
		 		$vouch_comment['user_id'] = $_SESSION['user_id'];
		 		$vouch_comment['comment'] = 'Consolidated for item '. $rdata['item_sku'].' in Order # '.linkToOrder($rdata['order_id']);
		 		$vouch_comment['date_added'] =  date('Y-m-d H:i:s');
		 		$vouch_comment['voucher_id'] =  $voucher['voucher_id'];
		 		$db->func_array2insert("inv_voucher_comments",$vouch_comment);
		 	}
		 } 
	}
	$items = explode(',', $_POST['removeProducts']);
	$order_xid = $db->func_query_first_cell('SELECT order_id FROM `inv_orders_items` WHERE `id` = "'. $items[0] .'"');
	// $update_terllo = true;
	// if ($db->func_query_first_cell("SELECT LCASE(order_status) from inv_orders WHERE order_id = '". $order_xid ."'") == 'estimate') {
	// 	$update_terllo = false;
	// }
	// $t_products = array();
	
	// $t_data = array( 'order_id' => $order_xid,
	// 	'type' => 'removed Items from',
	// 	'user_name' => $_SESSION['login_as'],
	// 	'products' => implode('`, `', $t_products),
	// 	'url' => $host_path . 'viewOrderDetail.php?order=' . $order_xid);
	// $template_T = $trello->makeTemplate($t_data);
	// if ($update_terllo) {
		
	// }
	$db->db_exec("DELETE FROM inv_orders_items WHERE order_id='".$order_xid."' and product_qty<=0");
	orderTotal($order_xid, true);

	$db->func_query("UPDATE inv_orders SET is_adjusted=1 WHERE order_id='".$order_xid."'");
	$db->func_query("UPDATE inv_orders_items SET oadjusted=1 WHERE order_id='".$order_xid."'");
}
if ($_POST['action'] == 'removeProduct') {
	$data = $db->func_query_first('SELECT * FROM `inv_orders_items` WHERE `id` = "'. $_POST['product_id'] .'"');
	$t_data = array( 'order_id' => $data['order_id'],
		'type' => 'removed an item from',
		'user_name' => $_SESSION['login_as'],
		'xsku' => $data['product_sku'],
		'xqty' => $data['product_qty'],
		'url' => $host_path . 'viewOrderDetail.php?order=' . $data['order_id']);
	print_r($t_data);
	exit;
	$template_T = $trello->makeTemplate($t_data);
	if ($db->func_query_first_cell("SELECT LCASE(order_status) from inv_orders WHERE order_id = '". $data['order_id'] ."'") != 'estimate') {
		// $trello->addCard($template_T['order_id'], $template_T['name'], $template_T['desc'], 'purple');
	}
	$s_q = 'SELECT'
	. ' *'
	. ' FROM'
	. ' `oc_order_total`'
	. ' WHERE'
	. ' cast(`order_id` as char(50)) = "' . $data['order_id'] . '" order by sort_order ASC';
	$oc_totals = $db->func_query($s_q);
	$tax_key = array_search('tax', array_column($oc_totals, 'code'));
	if ($tax_key) {
		$s_q = 'SELECT '
		. '`rate` '
		. 'FROM '
		. 'oc_tax_rate '
		. 'WHERE '
		.'`name` = "' . $oc_totals[$tax_key]['title'] . '"';
		$taxrate = $db->func_query_first_cell($s_q);
	}
	$totalx = 0;
	foreach ($oc_totals as $key => $total) {
		if ($total['code'] == 'sub_total') {
			$sub_t = $total['value'] - $data['product_price'];
			$oc_totals[$key]['value'] = $sub_t;
			$oc_totals[$key]['text'] = '$' . number_format($sub_t, 2);
		}
		if ($total['code'] == 'tax' && $taxrate) {
			$tax_t = ($sub_t / 100) * $taxrate;
			$oc_totals[$key]['value'] = $tax_t;
			$oc_totals[$key]['text'] = '$' . number_format($tax_t, 2);
		}
		if ($total['code'] != 'total') {
			$totalx += $oc_totals[$key]['value'];
		}
	}
	$oc_totals[array_search('total', array_column($oc_totals, 'code'))]['value'] = $totalx;
	$oc_totals[array_search('total', array_column($oc_totals, 'code'))]['text'] = '$' . number_format($totalx, 2);
	foreach ($oc_totals as $total) {
		$db->func_array2update('oc_order_total', $total, 'order_total_id = "'. $total['order_total_id'] .'"');
	}
	$db->func_query('DELETE FROM `inv_orders_items` WHERE `id` = "'. $_POST['product_id'] .'"');
	$db->func_query('DELETE FROM `oc_order_product` WHERE `model` = "'. $data['product_sku'] .'" AND order_id = "'. $data['order_id'] .'"');
	$log = "Product ". linkToProduct($data['product_sku']) ." quantity ". $data['product_qty'] ." removed from Order #". linkToOrder($data['order_id']);
	
	actionLog($log);
	orderTotal($data['order_id'], true);
	$json = array('success' => 1);
	echo json_encode($json);
	exit;
}
if($_GET['action']=='cancelOrder')
{
	$order_id = $_GET['order'];
	$db->db_exec("UPDATE inv_orders SET order_status='Canceled' WHERE order_id='".$order_id."' and amazon_cancel_order=1");
	$db->db_exec("UPDATE inv_orders_items SET ostatus='canceled' WHERE order_id='".$order_id."'");
	$log = "Order #". linkToOrder($orderID) . " been canceled";
	
	actionLog($log);
	header("Location:manage_returns.php");
	exit;   
}
if ($_POST['action'] == 'save_item') {
	$item_id = (int) $_POST['item_id'];
	$sku = $_POST['sku'];
	$qty = (int) $_POST['qty'];
	$unit = (float) $_POST['item_unit'];
	$discount = (int) $_POST['discount'];
	$true_cost = (float)$_POST['true_cost'];
	if($true_cost=='undefined')
	{
		$true_cost = $db->func_query_first_cell("SELECT product_true_cost from inv_orders_items WHERE id='".$item_id."' ");
	}
	// $price = (float) $_POST['price'];
	$price = $unit * $qty;
	$xData = $db->func_query_first('SELECT * FROM inv_orders_items WHERE id="'. $item_id .'"');
	$t_data = array( 'order_id' => $_POST['order_id'],
		'type' => 'updated',
		'user_name' => $_SESSION['login_as'],
		'xsku' => $xData['product_sku'],
		'xqty' => $xData['product_qty'],
		'sku' => $sku,
		'qty' => $qty,
		'url' => $host_path . 'viewOrderDetail.php?order=' . $_POST['order_id']);
	$template_T = $trello->makeTemplate($t_data);
	$update_t = ($qty != $xData['product_qty'])? 1: 0;
	$json = array();
	$oc_record = $db->db_exec("UPDATE oc_order_product SET price='$unit', total = '".( $unit * $qty )."' WHERE cast(order_id as char(50))='". $_POST['order_id'] ."' AND model = '". $sku ."'");
	$s_q = 'SELECT'
	. ' *'
	. ' FROM'
	. ' `oc_order_total`'
	. ' WHERE'
	. ' cast(`order_id` as char(50)) = "' . $_POST['order_id'] . '" order by sort_order ASC';
	$oc_totals = $db->func_query($s_q);
	$tax_key = array_search('tax', array_column($oc_totals, 'code'));
	if ($tax_key) {
		$s_q = 'SELECT '
		. '`rate` '
		. 'FROM '
		. 'oc_tax_rate '
		. 'WHERE '
		.'`name` = "' . $oc_totals[$tax_key]['title'] . '"';
		$taxrate = $db->func_query_first_cell($s_q);
	}
	$totalx = 0;
	foreach ($oc_totals as $key => $total) {
		if ($total['code'] == 'sub_total') {
			$sub_t = ($total['value'] - $xData['product_price']) + $price;
			$oc_totals[$key]['value'] = $sub_t;
			$oc_totals[$key]['text'] = '$' . number_format($sub_t, 2);
		}
		if ($total['code'] == 'tax' && $taxrate) {
			$tax_t = ($sub_t / 100) * $taxrate;
			$oc_totals[$key]['value'] = $tax_t;
			$oc_totals[$key]['text'] = '$' . number_format($tax_t, 2);
		}
		if ($total['code'] != 'total') {
			$totalx += $oc_totals[$key]['value'];
		}
	}
	$oc_totals[array_search('total', array_column($oc_totals, 'code'))]['value'] = $totalx;
	$oc_totals[array_search('total', array_column($oc_totals, 'code'))]['value'] = '$' . number_format($totalx, 2);
	foreach ($oc_totals as $total) {
		$db->func_array2update('oc_order_total', $total, 'order_total_id = "'. $total['order_total_id'] .'"');
	}
	$record = $db->db_exec("UPDATE inv_orders_items SET product_sku='$sku',product_unit='$unit',product_price='$price',product_true_cost='".$true_cost."',dateofmodification='" . date('Y-m-d H:i:s') . "' WHERE id='$item_id'");
	if ($record) {
		$hdata = array();
		$hdata['order_id'] = $_POST['order_id'];
		$hdata['comment'] = $sku . ' unit price has been changed from '.number_format($xData['product_unit'],2).' to '.number_format($unit,2).' or true cost has been changed from '.number_format($xData['product_true_cost'],2).' to '.number_format($true_cost,2);
		$hdata['user_id'] = $_SESSION['user_id'];
		$hdata['date_added'] = date('Y-m-d H:i:s');
		$db->func_array2insert("inv_order_history", $hdata);
		if ($db->func_query_first_cell("SELECT LCASE(order_status) from inv_orders WHERE order_id = '". $_POST['order_id'] ."'") != 'estimate' && $update_t) {
			// $trello->addCard($template_T['order_id'], $template_T['name'], $template_T['desc'], 'purple');
		}
		$json['success'] = 'Record modified!';
		


	} else {
		$json['error'] = 'Warning: Record not modified, please try again or contact your administrator.';
	}
	orderTotal($_POST['order_id'], true);
	echo json_encode($json);
	exit;
}
$order_users =  $db->func_query('SELECT u.* FROM inv_users u INNER JOIN inv_group_perm g on (u.group_id = g.group_id) WHERE g.perm_id = "25" AND u.status = "1" AND u.group_id <>"1" ');
if (isset($_POST['update'])) {
	$orderID = $db->func_escape_string($_GET['order']);
	$order_user = $db->func_escape_string($_POST['order_user']);
	$_store_type = $db->func_query_first_cell("SELECT store_type FROM inv_orders WHERE order_id='".$orderID."'"); // detect store type
	$first_name = $db->func_escape_string($_POST['first_name']);
	$po_term = $db->func_escape_string($_POST['po_term']);
	$terms = $db->func_escape_string($_POST['terms']);

	if($_SESSION['can_lock_price'])
	{
		if(isset($_POST['lock_prices']))
		{
			$lock_prices=1;
		}
		else
		{
			$lock_prices = 0;
		}
	}

	$last_name = $db->func_escape_string($_POST['last_name']);
	$order_status = $db->func_escape_string($_POST['order_status']);
	$shipping_date = date('Y-m-d', strtotime($db->func_escape_string($_POST['shipping_date'])));
    //$po_payment_source = $db->func_escape_string($_POST['po_payment_source']);
    //$po_payment_source_detail = $db->func_escape_string($_POST['po_payment_source_detail']);
    //$po_payment_source_amount = (float)$db->func_escape_string($_POST['po_payment_source_amount']);
	$payment_method = $db->func_escape_string($_POST['payment_method']);
	$customer_po = $db->func_escape_string($_POST['customer_po']);
	$reference_no = $db->func_escape_string($_POST['reference_no']);
	$payment_detail_1 = $db->func_escape_string($_POST['payment_detail_1']);
	$payment_detail_2 = $db->func_escape_string($_POST['payment_detail_2']);
	$paid_price = (float) $db->func_escape_string($_POST['paid_price']);
	$shipping_method = $db->func_escape_string($_POST['shipping_method']);
	$shipping_cost = (float)$db->func_escape_string($_POST['shipping_cost']);
	$customer_fedex_code = $db->func_escape_string($_POST['customer_fedex_code']);
	$status_id = $db->func_query_first_cell('SELECT order_status_id FROM oc_order_status WHERE name = "' . $_POST['order_status'] . '"');
	$inputDetails = array(
		'shipping_firstname' => $db->func_escape_string($_POST['first_name']),
		'shipping_lastname' => $db->func_escape_string($_POST['last_name']),
		'address1' => $db->func_escape_string($_POST['address1']),
		'company' => $db->func_escape_string(htmlentities($_POST['company_shipping'])),
		'address2' => $db->func_escape_string($_POST['address2']),
		'city' => $db->func_escape_string($_POST['city']),
		'zone_id' => $db->func_escape_string($_POST['zone_id']),
		'state' => getState($_POST['zone_id']),
		'country_id' => $db->func_escape_string($_POST['country_id']),
		'country' => getCountry($_POST['country_id']),
		'zip' => $db->func_escape_string($_POST['zip']),
		'shipping_cost' => $shipping_cost,
		'other_shipping_name' => $db->func_escape_string($_POST['other_shipping_name']),
		'bill_firstname' => $db->func_escape_string($_POST['bill_firstname']),
		'bill_lastname' => $db->func_escape_string($_POST['bill_lastname']),
		'bill_address1' => $db->func_escape_string($_POST['bill_address1']),
		'bill_address2' => $db->func_escape_string($_POST['bill_address2']),
		'billing_company' => $db->func_escape_string(htmlentities($_POST['company_billing'])),
		'bill_city' => $db->func_escape_string($_POST['bill_city']),
		'bill_zone_id' => $db->func_escape_string($_POST['bill_zone_id']),
		'bill_state' => getState($_POST['bill_zone_id']),
		'bill_country_id' => $db->func_escape_string($_POST['bill_country_id']),
		'bill_country' => getCountry($_POST['bill_country_id']),
		'bill_zip' => $db->func_escape_string($_POST['bill_zip']),
		'po_term' => $db->func_escape_string($_POST['po_term']),
		'payment_method' => $db->func_escape_string($_POST['payment_method']),
		'shipping_date' => date('Y-m-d', strtotime($db->func_escape_string($_POST['shipping_date']))),
		'shipping_method' => $db->func_escape_string($_POST['shipping_method']),
		'customer_fedex_code' => $db->func_escape_string($_POST['customer_fedex_code']),
		'reference_no' => $db->func_escape_string($_POST['reference_no'])
		);
	//testObject($inputDetails);
if ($_POST['voucher_codes'] && (float)$_POST['amount_due'] > 0.00) {
	$codes = explode(',', $_POST['voucher_codes']);
	$orderTotal = (float)$_POST['amount_due'];
	$totalUsed = 0;
	foreach ($codes as $code) {
		$voucher = $db->func_query_first("SELECT a.*, (a.`amount` + SUM(b.`amount`)) balance FROM `oc_voucher` a LEFT OUTER JOIN `oc_voucher_history` b ON a.`voucher_id` = b.`voucher_id` WHERE a.code = '". $code ."'");
		if (!$voucher['balance']) {
			$voucher['balance'] = $voucher['amount'];
		}
		if ($orderTotal > 0) {
			if ($orderTotal > $voucher['balance']) {
				$orderTotal = $orderTotal - $voucher['balance'];
				$used = $voucher['balance'];
			} else if ($orderTotal <= $voucher['balance']) {
				$used = $voucher['balance'] - $orderTotal;
				$used = $voucher['balance'] - $used;
				$orderTotal = 0;
			}
			$voucher_array = array();
			$voucher_array['voucher_id'] = $voucher['voucher_id'];
			if($_store_type=='web')
			{
				$voucher_array['order_id'] = $orderID;
			}
			else
			{
				$voucher_array['order_id'] = 0;
				$voucher_array['inv_order_id'] = $orderID;
			}
			$totalUsed += $used;
			$voucher_array['amount'] = '-'.$used;
			$voucher_array['date_added'] = date('Y-m-d h:i:s');
			$db->func_array2insert("oc_voucher_history",$voucher_array);
		}
	}
}
if ($status_id) {
	$db->db_exec("update oc_order SET order_status_id = '$status_id',date_modified='".date('Y-m-d H:i:s')."' where cast(order_id as char(50)) = '$orderID' OR ref_order_id='".$orderID."'");
}
if($shipping_method)
{
	$db->db_exec("update oc_order SET shipping_method = '$shipping_method',shipping_code='".$_POST['shipping_code']."' where cast(order_id as char(50)) = '$orderID' OR ref_order_id='".$orderID."'");
}
if(isset($_POST['po_order_number']))
{
	
	$db->db_exec("UPDATE inv_orders SET po_order_number='".$db->func_escape_string($_POST['po_order_number'])."' WHERE order_id='".$orderID."'");
}
if ($_POST['order_status'] == 'Canceled') {
	$t_data = array( 'order_id' => $orderID,
		'type' => 'canceled the',
		'user_name' => $_SESSION['login_as'],
		'url' => $host_path . 'viewOrderDetail.php?order=' . $orderID);
	$template_T = $trello->makeTemplate($t_data);
	$trello->addCard($template_T['order_id'], $template_T['name'], $template_T['desc'], 'red');
	$_order_details = getOrder($orderID);
	$db->db_exec("insert into inv_return_orders (order_id,email,order_price,order_date,return_date,status,store_type,dateofmodification)
		values ('$orderID','".$_order_details['email']."','".$_order_details['total']."','".$_order_details['date_added']."','".date('Y-m-d H:i:s')."','open','web','" . date('Y-m-d H:i:s') . "')");
}
$db->func_array2update('inv_orders_details', $inputDetails, "order_id = '$orderID'");

	//$db->db_exec("update inv_orders_details SET shipping_firstname = '$first_name' , shipping_lastname = '$last_name',po_term='$po_term',payment_method='$payment_method',shipping_date='" . $shipping_date . "',shipping_method='".$shipping_method."',customer_fedex_code='".$customer_fedex_code."',reference_no='$reference_no' where order_id = '$orderID'");
$db->db_exec("UPDATE oc_order SET payment_method='".$payment_method."' WHERE order_id='".$orderID."'");
$old = $db->func_query_first("SELECT o.*, od.* FROM inv_orders o, inv_orders_details od  WHERE o.order_id = od.order_id AND o.order_id='".$orderID."'"); 
if ($order_status == 'Processed') {
	$sStation = "shipstation_added='0',";
}


$db->db_exec("update inv_orders SET shipping_amount='".(float)$inputDetails['shipping_cost']."', order_status='" . $order_status . "',order_user = '" . $order_user . "',  customer_po='". $customer_po ."', payment_detail_1='$payment_detail_1',payment_detail_2='$payment_detail_2',terms='".$terms."' ".($_SESSION['can_lock_price']?",lock_prices='".$lock_prices."'":'')." WHERE order_id='" . $orderID . "'");

$db->db_exec("UPDATE inv_orders_items SET ostatus='".strtolower($order_status)."' where order_id='".$orderID."'");
    // Add a Comment about status change
if($old['order_status']!=$order_status){
	$hdata = array();
	$hdata['order_id'] = $orderID;
	$hdata['comment'] = 'Order Status has been changed to "' . $order_status . '"';
	$hdata['user_id'] = $_SESSION['user_id'];
	$hdata['date_added'] = date('Y-m-d H:i:s');
	$db->func_array2insert("inv_order_history", $hdata);
	$log = "Order #". linkToOrder($orderID) . " Status has been changed to " . $order_status;
	actionLog($log);
	if ($order_status == 'Processed') {
			// UPdate Shipstation if previous status is changed to Prosessed
		$db->db_exec("UPDATE inv_orders SET shipstation_added=0 WHERE order_id='".$orderID."'");
		$db->db_exec("UPDATE oc_order SET order_status_id=15,date_modified='".date('Y-m-d H:i:s')."' WHERE cast(order_id as char(50)) = '".$orderID."' OR ref_order_id='".$orderID."'");
	}
}
	// Add Action Log if Name has changed
if ($old['first_name']!=$first_name || $old['last_name']!=$last_name) {
	$log = "Order #". linkToOrder($orderID) . " Name has been changed from "  . $old['first_name'] . ' ' . $old['last_name'] . " to "  . $first_name . ' ' . $last_name;
	actionLog($log);
}
if ($paid_price) {
	$checkOld = $db->func_query_first_cell("SELECT  paid_price FROM inv_orders WHERE order_id='$orderID'");
	if ($checkOld) {
		$db->db_exec("UPDATE inv_orders SET paid_price=paid_price+$paid_price WHERE order_id='$orderID'");
		$hdata = array();
		$hdata['order_id'] = $orderID;
		$hdata['comment'] = 'Payment has been made for the amount of $' . number_format($paid_price, 2) . ' via "' . $payment_method . '" method';
		$hdata['user_id'] = $_SESSION['user_id'];
		$hdata['date_added'] = date('Y-m-d H:i:s');
		$db->func_array2insert("inv_order_history", $hdata);
		$log = 'Amount of '. $paid_price .' was paid for Order # '. linkToOrder($orderID) .' Transaction Details are "'. $payment_method .'"';
		actionLog($log);
	} else {
		$db->db_exec("UPDATE inv_orders SET paid_price=$paid_price WHERE order_id='$orderID'");
		$hdata = array();
		$hdata['order_id'] = $orderID;
		$hdata['comment'] = 'Payment has been made for the amount of $' . number_format($paid_price, 2) . ' via "' . $payment_method . '" method';
		$hdata['user_id'] = $_SESSION['user_id'];
		$hdata['date_added'] = date('Y-m-d H:i:s');
		$db->func_array2insert("inv_order_history", $hdata);
		$log = 'Amount of '. $paid_price .' was paid for Order # '. linkToOrder($orderID) .' Transaction Details are "'. $payment_method .'"';
		actionLog($log);
	}
}
$skus = $db->func_query("SELECT product_sku,product_qty FROM inv_orders_items where order_id='".$orderID."'");
		$sort_array = array();
		foreach($skus as $sku)
		{
			if(isset($sort_array[$sku['product_sku']]))
			{
				$sort_array[$sku['product_sku']]+=(int)$sku['product_qty'];
			}
			else
			{
				$sort_array[$sku['product_sku']]=(int)$sku['product_qty'];
				
			}
			
		}
$bit = 'not_picked';
	if($old['is_picked']==1 && $old['is_packed']==0)
	{
		$bit = 'picked';
	}
	elseif($old['is_picked']==1 && $old['is_packed']==1)
	{
		$bit = 'packed';
	}

if(strtolower($order_status)=='on hold' &&  (strtolower($old['order_status'])=='processed' || strtolower($old['order_status'])=='estimate') ) 
{

	//$db->db_exec("UPDATE inv_orders SET is_picked=0,is_packed=0 WHERE order_id='".$orderID."'");
	

			// foreach($sort_array as $sort_sku => $sort_qty)
			// {
			// 	$db->db_exec("UPDATE oc_product SET ".$bit."=".$bit."-".(int)$sort_qty." WHERE trim(lower(model))='".trim(strtolower($sort_sku))."'");
			// 	$db->db_exec("UPDATE oc_product SET reserved=reserved+".(int)$sort_qty." WHERE trim(lower(model))='".trim(strtolower($sort_sku))."'");
			// }

			makeLedger($orderID,$sort_array,$_SESSION['user_id'],'not_picked_reserved',$old['order_status']. ' &rarr; On Hold');

}


if((strtolower($order_status)=='canceled' || strtolower($order_status)=='cancelled' ) &&  (strtolower($old['order_status'])=='processed' || strtolower($old['order_status'])=='on hold') || strtolower($old['order_status'])=='shipped' )
{
	$db->db_exec("UPDATE inv_orders SET is_picked=0,is_packed=0 WHERE order_id='".$orderID."'");
	$db->db_exec("UPDATE inv_orders_items SET is_picked=0,is_packed=0,picked_quantity=0,packed_quantity=0 WHERE order_id='".$orderID."'");
			// foreach($sort_array as $sort_sku => $sort_qty)
			// {
			// 	$db->db_exec("UPDATE oc_product SET quantity=quantity+".(int)$sort_qty." WHERE trim(lower(model))='".trim(strtolower($sort_sku))."'");

			// }

	if(strtolower($old['order_status'])=='shipped')
	{

		$inventory->updateInventoryCancel($orderID,'rollback');
	}
	else
	{
			makeLedger($orderID,$sort_array,$_SESSION['user_id'],'rollback',$old['order_status'].' &rarr; Canceled');	
		
	}

	
}

if((strtolower($order_status)=='shipped'  ) &&  strtolower($old['order_status'])=='processed')
{
			// foreach($sort_array as $sort_sku => $sort_qty)
			// {
			// 	$db->db_exec("UPDATE oc_product SET quantity=quantity-".(int)$sort_qty." WHERE trim(lower(model))='".trim(strtolower($sort_sku))."'");
			// }

	$inventory->updateInventoryShipped($orderID,'shipped');

			//makeLedger($orderID,$sort_array,$_SESSION['user_id'],'shipped',$old['order_status'].' &rarr; Shipped');	
	
}

if((strtolower($order_status)=='processed'  ) &&  strtolower($old['order_status'])=='on hold')
{
	//$db->db_exec("UPDATE inv_orders SET is_picked=0,is_packed=0 WHERE order_id='".$orderID."'");
			// foreach($sort_array as $sort_sku => $sort_qty)
			// {
			// 	$db->db_exec("UPDATE oc_product SET not_picked=not_picked+".(int)$sort_qty." WHERE trim(lower(model))='".trim(strtolower($sort_sku))."'");
			// 	$db->db_exec("UPDATE oc_product SET reserved=reserved-".(int)$sort_qty." WHERE trim(lower(model))='".trim(strtolower($sort_sku))."'");
			// }
			

			makeLedger($orderID,$sort_array,$_SESSION['user_id'],'reserved_not_picked','On Hold &rarr; Processed');	
	
}
orderTotal($orderID, true);
header("Location:viewOrderDetail.php?order=$orderID");
exit;
}
if ($_GET['action'] == 'delete' && (int) $_GET['fileid']) {
	$fileid = (int) $_GET['fileid'];
	$orderID = $db->func_escape_string($_GET['order']);
	$db->db_exec("Delete from inv_order_docs where id = '$fileid' and order_id = '$orderID'");
	$log = "Document Deleted from Order #". linkToOrder($orderID);
	actionLog($log);
	header("Location:viewOrderDetail.php?order=$orderID");
	exit;
}
//add comments
if (isset($_POST['addcomment'])) {
	$orderID = $db->func_escape_string($_GET['order']);
	$po_check = $db->func_query_first_cell("SELECT store_type FROM inv_orders WHERE order_id='$orderID'");
	if ($po_check != 'po_business') {
		$addcomment = array();
		$addcomment['date_added'] = date('Y-m-d H:i:s');
		$addcomment['user_id'] = $_SESSION['user_id'];
		$addcomment['comment'] = $db->func_escape_string($_POST['comment']);
		$addcomment['order_id'] = $orderID;
		$order_history_id = $db->func_array2insert("oc_order_history", $addcomment);
		$order_mod_logs = array();
		$order_mod_logs['order_history_id'] = $order_history_id;
		$order_mod_logs['order_id'] = $orderID;
		$order_mod_logs['user_id'] = $_SESSION['user_id'];
		$order_mod_logs['date_modified'] = date('Y-m-d H:i:s');
		$db->func_array2insert("oc_order_mod_logs", $order_mod_logs);
	} else {
		$addcomment = array();
		$addcomment['date_added'] = date('Y-m-d H:i:s');
		$addcomment['user_id'] = $_SESSION['user_id'];
		$addcomment['comment'] = $db->func_escape_string($_POST['comment']);
		$addcomment['order_id'] = $orderID;
		$order_history_id = $db->func_array2insert("inv_order_history", $addcomment);
	}
	$_SESSION['message'] = "New comment is added.";
	header("Location:$host_path/viewOrderDetail.php?order=$orderID");
	exit;
}
//upload return item item images
if ($_FILES['order_docs']['tmp_name']) {
	$imageCount = 0;
	$orderID = $db->func_escape_string($_GET['order']);
	$uniqid = uniqid();
	$name = explode(".", $_FILES['order_docs']['name']);
	$ext = end($name);
	$destination = $path . "files/" . $uniqid . ".$ext";
	$file = $_FILES['order_docs']['tmp_name'];
	if (move_uploaded_file($file, $destination)) {
		$orderDoc = array();
		$orderDoc['attachment_path'] = "files/" . basename($destination);
		$orderDoc['type'] = $_FILES['order_docs']['type'];
		$orderDoc['size'] = $_FILES['order_docs']['size'];
		$orderDoc['description'] = $_POST['description'];
		$orderDoc['date_added'] = date('Y-m-d H:i:s');
		$orderDoc['order_id'] = $orderID;
		$db->func_array2insert("inv_order_docs", $orderDoc);
		$imageCount++;
	}
	if ($imageCount > 0) {
		$_SESSION['message'] = "attachments are added successfully.";
	} else {
		$_SESSION['message'] = "attachments are not added.";
	}
	header("Location:$host_path/viewOrderDetail.php?order=$orderID");
	exit;
}
// if ($_FILES['order_docs']['tmp_name']) {
// 	$imageCount = 0;
// 	$orderID = $db->func_escape_string($_GET['order']);
// 	$uniqid = uniqid();
// 	$name = explode(".", $_FILES['order_docs']['name']);
// 	$ext = end($name);
// 	$destination = $path . "files/" . $uniqid . ".$ext";
// 	$file = $_FILES['order_docs']['tmp_name'];
// 	if (move_uploaded_file($file, $destination)) {
// 		$orderDoc = array();
// 		$orderDoc['attachment_path'] = "files/" . basename($destination);
// 		$orderDoc['type'] = $_FILES['order_docs']['type'];
// 		$orderDoc['size'] = $_FILES['order_docs']['size'];
// 		$orderDoc['description'] = $_POST['description'];
// 		$orderDoc['date_added'] = date('Y-m-d H:i:s');
// 		$orderDoc['order_id'] = $orderID;
// 		$db->func_array2insert("inv_order_docs", $orderDoc);
// 		$imageCount++;
// 	}
// 	if ($imageCount > 0) {
// 		$_SESSION['message'] = "attachments are added successfully.";
// 	} else {
// 		$_SESSION['message'] = "attachments are not added.";
// 	}
// 	header("Location:$host_path/viewOrderDetail.php?order=$orderID");
// 	exit;
// }
	
if ($_GET['order']) {
	$orderID = $db->func_escape_string($_GET['order']);
	$order = $db->func_query_first("select inv_orders.* , inv_orders_details.* from  inv_orders left join inv_orders_details on (inv_orders_details.order_id = inv_orders.order_id) where inv_orders.order_id = '$orderID' ");
	$old_transaction_fee = $order['transaction_fee'];

	$payment_response_data = $db->func_query_first("SELECT sum(transaction_fee) as transaction_fee,response_data FROM inv_transactions WHERE order_id='".$order['order_id']."' group by order_id");

	$order['transaction_fee'] = $payment_response_data['transaction_fee'];
	$order['payment_response_data'] = $payment_response_data['response_data'];

	
	if(!$order['transaction_fee'])
	{
		$order['transaction_fee'] = $old_transaction_fee;
	}
	$customer_group_id = $db->func_query_first_cell('SELECT customer_group_id FROM oc_customer WHERE email = "'. $order['email'] .'"');
	$order_items = $db->func_query("Select * from inv_orders_items where order_id = '$orderID'");
	if ($order['store_type'] == 'po_business' or $order['payment_method']=='Terms') {
		$is_po = true;
	} else {
		$is_po = false;
	}
	if($order['shipping_method']=='Local Las Vegas Store Pickup - 9:30am-4:30pm')
	{
		//print_r("here");exit;
		$is_local = true;
		$cash_paid = $db->func_query_first_cell('SELECT cash_paid FROM oc_order WHERE cast(`order_id` as char(50)) = "'. $orderID .'" OR ref_order_id="'.$orderID.'"');
		$card_paid = $db->func_query_first_cell('SELECT card_paid FROM oc_order WHERE cast(`order_id` as char(50)) = "'. $orderID .'" OR ref_order_id="'.$orderID.'"');
		$change_due = $db->func_query_first_cell('SELECT change_due FROM oc_order WHERE cast(`order_id` as char(50)) = "'. $orderID .'" OR ref_order_id="'.$orderID.'"');
		$paypal_paid = $db->func_query_first_cell('SELECT paypal_paid FROM oc_order WHERE cast(`order_id` as char(50)) = "'. $orderID .'" OR ref_order_id="'.$orderID.'"');
	}
	else
	{
		$is_local = false;
	}
	if (!$order) {
		$order = $db->func_query_first("select inv_return_orders.* , inv_orders_details.* from  inv_return_orders inner join inv_orders_details on (inv_orders_details.order_id = inv_return_orders.order_id) where inv_orders_details.order_id = '$orderID' ");
		$order_items = $db->func_query("Select * from inv_orders_items where order_id = '$orderID' ");
	}
	$sub_total = 0;
	foreach ($order_items as $zz => $order_item) {
		if($order['payment_method']=='Replacement')
		{
			//$order_items[$zz]['promotion_discount'] = $order_item['product_price'];
			//$order_item['promotion_discount'] = $order_item['product_price'];
		}
		$sub_total+=($order_item['product_price']-$order_item['promotion_discount']);
	}
	$business_fee = round($db->func_query_first_cell('SELECT SUM(`value`) FROM `oc_order_total` WHERE cast(`order_id` as char(50)) = "'. $order['order_id'] .'" AND `code` = "business_fee"'),2);
	$order_total = $order['shipping_cost'] + $sub_total + $business_fee;
	$_tax = round($db->func_query_first_cell('SELECT SUM(`value`) FROM `oc_order_total` WHERE cast(`order_id` as char(50)) = "'. $order['order_id'] .'" AND `code` = "tax"'),2);
	if($_tax<0.00)$_tax=0.00;
	$total_vouchers = $db->func_query_first_cell('SELECT SUM(`a`.`amount`) AS `used` FROM `oc_voucher_history` as a, `oc_voucher` as b WHERE a.`voucher_id` = b.`voucher_id` AND ' . (($order['store_type'] == 'web')? 'cast(a.order_id as char(50))': 'a.inv_order_id') . ' = "'. $order['order_id'] .'"');
	$total_vouchers_issued = $db->func_query_first_cell('SELECT SUM(amount) FROM `oc_voucher` WHERE  code LIKE "%'. $order['order_id'] .'%"');
	if(!$is_po)
	{
		$order_total = $order_total + $_tax;
	}
	$is_replacement_order = false;
	if($order['payment_method']=='Replacement')
	{
		$is_replacement_order = true;
		//$order_total = 0.00;
	}
	$order_total += $total_vouchers;
	//$order_total += $total_vouchers_issued;
	$comments = $db->func_query("SELECT oh.`date_added`,oh.`comment`,om.`user_id`,CONCAT(u.firstname,' ',u.lastname) AS name FROM oc_order_history oh LEFT JOIN oc_order_mod_logs om ON (om.order_history_id = oh.order_history_id)
		LEFT JOIN oc_user u ON (u.user_id = om.user_id)
		WHERE oh.order_id = '$orderID'
		UNION ALL
		SELECT i.date_added,i.`comment`,i.user_id,iu.name FROM inv_order_history i LEFT JOIN inv_users iu ON (i.user_id = iu.id) WHERE i.order_id='$orderID'
		");
	$attachments = $db->func_query("select * from inv_order_docs where order_id = '$orderID' AND is_invoice=0");
	$order_fraud = $db->func_query_first("SELECT * FROM oc_order_fraud WHERE cast(order_id as char(50)) = '$orderID'");
	
	$__query = "SELECT distinct a.*,b.date_added as date_received FROM inv_returns a LEFT JOIN inv_return_history b ON (a.`rma_number` = b.`rma_number` AND b.return_status = 'Received') WHERE a.order_id = '$orderID' ORDER BY a.id DESC";
	$order_rma = $db->func_query($__query);
	$__query = "SELECT a.*, b.rma_number FROM inv_return_decision a, inv_returns b WHERE a.return_id = b.id AND b.order_id = '$orderID' AND a.`action` = 'Issue Refund' ORDER BY b.order_id DESC";
	$order_refund = $db->func_query($__query);
	$__query = "SELECT b.* FROM inv_voucher_details a, oc_voucher b WHERE a.voucher_id = b.voucher_id AND a.order_id = '$orderID'";
	$order_voucher = $db->func_query($__query);
	$__query = "SELECT o.prefix, o.ppusa_sync, o.order_id, d.payment_method, o.ss_valid, o.order_date, o.email, o.order_price, o.store_type, o.order_status, o.fishbowl_uploaded, o.customer_name, o.match_status, o.bscheck, o.is_address_verified, o.avs_code, o.payment_source, d.address1, d.bill_address1, d.zip, d.bill_zip, o.transaction_fee FROM inv_orders o, inv_orders_details d WHERE o.order_id = d.order_id AND o.order_id LIKE '$orderID-%' AND o.order_id NOT LIKE '$orderID-%-%' GROUP BY o.order_id ORDER BY order_date DESC";
	$order_replacements = $db->func_query($__query);
} else {
	exit;
}
$order_fees = $db->func_query("select * from inv_order_fees where order_id = '$orderID'");
$order_totals = orderTotal($order['order_id']);
$order_shipments = $db->func_query("select * from inv_shipstation_transactions where order_id = '$orderID' ORDER BY voided DESC");
$emailInfo = array(
	'customer_name' => $order['customer_name'],
	'email' => $order['email'],
	'order_id' => $order['order_id'],
	'shipping_firstname' => $order['first_name'],
	'shipping_lastname' => $order['last_name'],
	'shipping_address_1' => $order['address1'],
	'shipping_address_2' => $order['address2'],
	'shipping_city' => $order['city'],
	'shipping_zone' => $order['state'],
	'shipping_postcode' => $order['zip'],
	'shipping_method' => $order['shipping_method'],
	'payment_firstname' => $order['first_name'],
	'payment_lastname' => $order['last_name'],
	'payment_address_1' => ($order['po_business_id']) ? $order['address1']: $order['bill_address1'],
	'payment_address_2' => ($order['po_business_id']) ? $order['address2']: $order['bill_address2'],
	'payment_city' => ($order['po_business_id']) ? $order['city']: $order['bill_city'],
	'payment_zone' => ($order['po_business_id']) ? $order['state']: $order['bill_state'],
	'payment_postcode' => ($order['po_business_id']) ? $order['zip']: $order['bill_zip'],
	'payment_method' => $order['payment_method'],
	'order_date' => $order['order_date'],
	'date_added' => $order['order_date'],
	'date_modified' => $order['dateofmodification'],
	'sub_total' => $order_totals['sub_total'],
	'shipping_fee' => $order_totals['shipping_fee'],
	'tax' => $order_totals['tax'],
	'vouchers' => $order_totals['vouchers'],
	'coupons' => $order_totals['coupons'],
	'order_total' => $order_totals['order_total']
	);
$adminInfo = array('name' => $_SESSION['user_name'], 'company_info' => $_SESSION['company_info'] );
$_SESSION['email_info'][$orderID] = $emailInfo;
if (isset($_POST['sendemail'])) {
	$email = array();
	$src = $path .'files/canned_' . $_POST['canned_id'] . ".png";
	if (file_exists($src)) {
		$email['image'] = $host_path .'files/canned_' . $_POST['canned_id'] . ".png?" . time();
	}
	$email['title'] = $_POST['title'];
	$email['subject'] = $_POST['subject'];
	$email['message'] = $_POST['comment'];
	$emailInfo['total_formatted'] = $_POST['total_formatted'];
	//print_r($_POST['comment']);exit;
	sendEmailDetails($emailInfo, $email);
	header("Location:$host_path/viewOrderDetail.php?order=$orderID");
	exit;
}
// Update Email
 
if ($_POST['action'] == 'update_email') {
	$tables = array("inv_customer_return_orders", "inv_orders","inv_return_orders", "inv_returns", "oc_order", "oc_return");
	$email = $_POST['email'];
	$oldEmail = $_POST['oldEmail'];
	$orderID = $db->func_escape_string($_GET['order']);
	$po_check = $db->func_query_first_cell("SELECT store_type FROM inv_orders WHERE order_id='$orderID'");
	if ($po_check != 'po_business') {
		$addcomment = array();
		$addcomment['date_added'] = date('Y-m-d H:i:s');
		$addcomment['user_id'] = $_SESSION['user_id'];
		$addcomment['comment'] = "Email updated from $oldEmail to $email";
		$addcomment['order_id'] = $orderID;
		$order_history_id = $db->func_array2insert("oc_order_history", $addcomment);
		$order_mod_logs = array();
		$order_mod_logs['order_history_id'] = $order_history_id;
		$order_mod_logs['order_id'] = $orderID;
		$order_mod_logs['user_id'] = $_SESSION['user_id'];
		$order_mod_logs['date_modified'] = date('Y-m-d H:i:s');
		$db->func_array2insert("oc_order_mod_logs", $order_mod_logs);
	} else {
		$addcomment = array();
		$addcomment['date_added'] = date('Y-m-d H:i:s');
		$addcomment['user_id'] = $_SESSION['user_id'];
		$addcomment['comment'] = $db->func_escape_string("Email updated from $oldEmail to $email");
		$addcomment['order_id'] = $orderID;
		$order_history_id = $db->func_array2insert("inv_order_history", $addcomment);
	}
	foreach ($tables as $table) {
		$db->db_exec("update `$table` set `email`='$email' WHERE `order_id`='$orderID'");
	}
	$log = "Order #". linkToOrder($_POST['order_id']) . " Email updated from $oldEmail to $email";
	actionLog($log);
	$_SESSION['message'] = 'Email Changed';
	$array = array('success'=> 1,'msg' => $orderID);
	echo json_encode($array);
	exit;
}
// Event Details
$eventData = $eventData . 'Name: ' . $order['shipping_firstname'] . ' ' . $order['shipping_lastname'] . "\n";
$eventData = $eventData . 'Phone: ' . $order['phone_number'] . "\n";
$eventData = $eventData . 'Email: ' . $order['email'] . "\n";
$eventData = $eventData . 'Address: ' . $order['address1'] . ' ' . $order['address2'] . "\n";
$eventData = $eventData . $order['city'] . ', ' . $order['state'] . "\n";
$eventData = $eventData . $order['country'] . ', ' . $order['zip'];
$_SESSION['event_details'][$orderID] = $eventData;
if($_POST['action']=='addpickedup') {
	$white_list = $_POST['chk'];
	
	if($white_list=='true') $white_list = 1; else $white_list = 0;
	$db->func_query('UPDATE oc_order set shipping_method="Local Las Vegas Store Pickup - 9:30am-4:30pm - Call Us", shipping_code="multiflatrate.multiflatrate_0" where order_id="'. $order['order_id'] .'"');
	exit;
	
}
//public function addpickedup(){
//	$db->func_query('UPDATE oc_order set shipping_method=" 	
//Local Las Vegas Store Pickup - 9:30am-4:30pm - Call Us" where order_id="'. $order['order_id'] .'"');
//}
//print_r($order['order_status']);exit;
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="include/xtable.css" media="screen" />
	
	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	
	<title>Order Detail</title>
	<style>
		.read_class{
			background-color: #eee;
			border: 1px solid #ccc;
		}
		.toggle_warehouse{
			display: none;
		}
		.toggle_true_cost{
			display:none;
		}
	</style>
	<script>
		$(document).ready(function (e) {
			$('.fancybox4').fancybox({width: '90%', autoCenter: true, autoSize: true});
			$('.fancybox2').fancybox({width: '500px', height: '500px', autoCenter: true, autoSize: false});

			
		});
	</script>
</head>
<body>
	<?php include_once 'inc/header.php'; ?>
	<?php if (@$_SESSION['message']): ?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message'];unset($_SESSION['message']); ?><br /></font>
		</div>
	<?php endif; ?>
	<?php
	if($order['order_id']!='')
	{
		?>
		<h2 align="center" style="float:left;margin-left:40%"> Order Details - Customer's Detail </h2>
		<?php
	}
	?>
	<?php if ($order['order_id']!=''): ?>
		<div style="float:left;width:100%;text-align:center;margin-top:9px">
			<form method="post" action="">
			<input align="right" type="submit" class="button" name="reset_fb" value="Reset Fishbowl" />
				<?php if ($order['is_manual']==1){ ?>
				<input align="right" type="submit" class="button" name="reset" value="Reset Shipstation" />
				<?php }?> 
				
				<?php if ($order['is_followed']==1){ ?>
				<input align="right" type="submit" class="button" name="unfollow" value="Unfollow Order" />
				<?php } else { ?>
				<input align="right" type="submit" class="button" name="follow" value="Follow Order" />
				<?php  } ?>
			<!-- <a href="email_invoice.php?order_id=<?= $order['order_id']; ?>&action=email" class="button">Email Invoice</a> -->
			<a class="button status_btn fancybox4 fancybox.iframe" href="sendemail.php?catid=1&order_id=<?= $orderID; ?>">Email Invoice</a>
			<a href="email_invoice.php?order_id=<?= $order['order_id']; ?>&action=view" target="_blank" class="button">Download PDF</a>
			<a href="<?php echo $host_path;?>download_csv.php?order_id=<?= $order['order_id']; ?>" target="_blank" class="button">Download CSV</a>

			<?php
			if(isset($_SESSION['manage_returns']))
			{
				?>

				<a href="returns_create.php?order_id=<?php echo $order['order_id'];?>" style="" class="button button-danger">Create RMA</a>
				<?php
			}

			?>

			<!--<a class="button status_btn fancybox4 fancybox.iframe" href="<?php echo $host_path;?>/popupfiles/customer_cart.php?cust_email=<?= $order['email']; ?>">Customer Cart</a>-->
						</form>
			
			</div>
		<?php endif; ?>
		<div align="center" style="clear:both">
			<?php 
			if($order['order_id']=='')
			{
				echo '<h2 style="font-size:14px">Order # '.$orderID.' does not yet exist in the IMP database. If this order was recently placed, it may take up to 10 minutes for it to import. Otherwise, please check the order number and try your search again.</h2>';exit;
			}
			if ($order) : ?>
			<form method="post" action="" id="xfrm">
					<?php
					// echo "SELECT a.name FROM inv_users a,inv_customers b where a.id=b.user_id and trim(b.email)='".trim($order['email'])."'";
						$sales_agent = $db->func_query_first_cell("SELECT a.name FROM inv_users a,inv_customers b where a.id=b.user_id and trim(b.email)='".trim($order['email'])."'");
						if ($sales_agent) { ?>
<h1 style="font-size:14px;color:#0059a0">AGENT: <?= strtoupper($sales_agent); ?></h1>
						<?php } ?>
						<?php
						if(strtolower($order['order_status'])!='canceled'  )
						{
						if($order['is_picked']==0)
						{
							$inv_status = 'Not Picked';
						}
						elseif($order['is_picked']==1 && $order['is_packed']==0)
						{
							$inv_status = 'Ready to be Packed';
						}
						elseif(($order['is_picked']==1 && $order['is_packed']==1)  && strtolower($order['order_status'])!='shipped')
						{
							$inv_status = 'Order Packed';
						}
					}
						if(strtolower($order['order_status'])=='shipped')
						{
							$inv_status='Order Shipped';
						}
						if($inv_status)
						{
							?>
							<h2 style="font-size:14px;color:#0059a0"><?php echo $inv_status;?></h2>
							<?php
						}

						?>
						<br>
						<strong>Created By:</strong>
						<select name="order_user">
							<option value="">Select</option>
							<?php foreach ($order_users as $user) { ?>
							<option value="<?php echo $user['id']; ?>" <?php if($order['order_user']==$user['id']):?> selected='selected' <?php endif;?>><?php echo $user['name']; ?></option>
						<?php } ?>								
						</select>

						<br><br>
				<table cellpadding="10" style="border:1px solid #ddd;border-collapse: collapse;" cellspacing="0" width="70%" border="1">
					<tr>
						<th>Order ID : </th>
						<td> <?= $order['prefix'].$order['order_id']; ?> </td>
						<th>Order Date : </th>
						<td> <?= americanDate($order['order_date']); ?> </td>
					</tr>
					<?php if ($is_po):?>
						<tr>
							<th>Customer PO #:</th>
							<td><input name="customer_po" size="15" value="<?= $order['customer_po']; ?>" type="text"></td>
							<th>Shipping Date:</th>
							<td><input type="text" class="datepick"  id="shipping_date" name="shipping_date" size="20" style="width: 150px;" readonly=""  value="<?php echo ($order['shipping_date'] == '' || $order['shipping_date'] == '0000-00-00 00:00:00' ? date('Y-m-d') : date('Y-m-d', strtotime($order['shipping_date']))); ?>" /></td>
						</tr>
					<?php endif;?>
					<tr>
						<?php if($_SESSION['display_order_price'] || $_SESSION['login_as'] == 'admin'){ ?>
						<th> Order Total</th>

						<td> $<?= number_format($order_total, 2) ?> </td>
						<?php } else { ?>
						<th></th>
						<td></td>
						<?php }?>
						<th>Store Type </th>
						<td> <?= $order['store_type'] ?> </td>
					</tr>
					<?php $paypal_check = $db->func_query_first("SELECT payment_code FROM oc_order WHERE cast(order_id as char(50))='" . $orderID . "' AND payment_code IN('pp_standard','paypal_express','paypal_express_new','pp_standard_new')"); ?>
					<?php $payflow_check = $db->func_query_first("SELECT payment_code FROM oc_order WHERE cast(order_id as char(50))='" . $orderID . "' AND payment_code IN('pp_payflow_pro')"); ?>
					<tr>
						<th>Sub Store Type </th>
						<td> <?= $order['sub_store_type'] ?> </td>
						<th>Order Status</th>
						<td> 
							<span id="order_status_span"><?= $order['order_status'] ?></span> 
							<?php if ($is_po and strtolower($order['order_status']) == 'estimate'):?>
								<input type="button" class="button status_btn"  value="Confirm Order" onclick="changeOrderStatus('Unshipped', '')" />
							<?php endif;?>
							<?php if (($_SESSION['cancel_order']) && (strtolower($order['order_status']) == 'estimate' || strtolower($order['order_status']) == 'processed' || strtolower($order['order_status']) == 'awaiting fulfillment' || strtolower($order['order_status']) == 'shipped' || strtolower($order['order_status']) == 'on hold' || $_SESSION['login_as'] == 'admin') && (strtolower($order['order_status']) != 'canceled')) { ?>
							<?php 
							if($order['store_type'] == 'web' || $order['store_type'] == 'bigcommerce' || $order['store_type'] == 'amazon' || $order['store_type'] == 'amazon_fba')
							{
								if($order['paid_price']>0.00 or strtolower($order['order_status'])=='shipped')
								{
									$order_paid_type='paid';
								}
								else
								{
									$order_paid_type='unpaid';
								}
							}
							else
							{
								$order_paid_type = 'unpaid';
							}
							?>
							<?php if ($order_paid_type=='paid' && ($order['store_type'] == 'amazon' || $order['store_type'] == 'amazon_fba')) { ?>
							<a class="button status_btn fancybox4 fancybox.iframe" href="order_payback.php?action=cancel&storetype=amazon&order_id=<?= $orderID; ?>">Cancel Order</a>
							<?php } else if ($order_paid_type=='paid') { ?>
							<a class="button status_btn fancybox4 fancybox.iframe" href="order_payback.php?action=cancel&<?= ((!(strpos(strtolower($order['payment_method']), 'cash') === false))?'type=cash&':'') ?>order_id=<?= $orderID; ?>">Cancel Order</a>
							<?php } else { ?>
							<input type="button" class="button status_btn"  value="Cancel Order" onclick="changeOrderStatus('Canceled', '')" />
							<?php }
							?>
							<?php } ?>
							<?php if (($_SESSION['onhold_order']) && (strtolower($order['order_status']) == 'processed' || strtolower($order['order_status']) == 'estimate')) { ?>
							<input type="button" class="button status_btn"  value="On Hold" onclick="changeOrderStatus('On Hold', '')" />
							<!-- <a class="button status_btn fancybox4 fancybox.iframe" href="order_on_hold.php?action=on_hold&order_id=<?= $orderID; ?>">On Hold</a> -->
							<?php } ?>
							<?php if (($_SESSION['shipped_order']) && (strtolower($order['order_status']) == 'processed' || strtolower($order['order_status']) == 'unshipped'  )) { ?>
							<a class="button status_btn fancybox4 fancybox.iframe" href="order_shipped.php?order_id=<?= $orderID; ?>">Shipped</a>
							<?php } ?>
							<?php if (($_SESSION['process_order']) && (strtolower($order['order_status']) == 'on hold')) { ?>
							<input type="button" class="button"  value="Process Order" onclick="changeOrderStatus('Processed', '')" />
							<!--<a class="button status_btn fancybox4 fancybox.iframe" href="order_on_hold.php?action=on_hold&order_id=<?= $orderID; ?>">"On Hold" Notice</a>-->
							<?php } ?>
							<?php if (($_SESSION['process_order']) && (strtolower($order['order_status']) == 'shipped')) { ?>
							<input type="button" class="button"  value="Void Shipment" onclick="changeOrderStatus('Processed', '*')" />
							<?php } ?>
							<script type="text/javascript">
								function changeOrderStatus(status, obj){
									if(obj=='' || obj=='*') {
										
										if(obj=='*')
										{
											if (!confirm('This will void any issued shipping label, and return order to Packed status.')) {
											return false;
										}
										}
										else
										{
										if (!confirm('Are you sure?')) {
											return false;
										}
										}
									}
									$('#order_status_span').html(status);
									$('#order_status').val(status);
									$('.status_btn').hide();


									if(obj=='*')
									{
										$.ajax({
										url: '<?php echo $host_path;?>inventory/ajax.php',
										type:"POST",
										dataType:"json",
										data:{'order_id':'<?php echo $orderID;?>', 'action':'void_label','type':'ajax'},
										success: function(json){
											window.location.reload();
											// $('input[name=update]').click();	
										}
									});
									}
									else
									{
										$('input[name=update]').click();	
									}
									
									
								}
							</script>
							<input type="hidden" name="order_status" id="order_status" value="<?php echo $order['order_status']; ?>">
						</td>
					</tr>
					<tr>
						<th>Fishbowl Uploaded</th>
						<td> <?= $order['fishbowl_uploaded'] ?> </td>
						<th>Customer Email </th>
						<td> <b><?= linkToProfile($order['email']) ?></b> (<?=$db->func_query_first_cell("SELECT ip FROM oc_order WHERE cast(order_id as char(50))='".$order['order_id']."'");?>) <?= ($_SESSION['update_email']) ? '<a href="javascript:void(0);" id="edit_email">Edit</a><a href="javascript:void(0)" id="update_email" style="display: none;" class="button">Update</a><a href="javascript:void(0)" id="cancle_email" style="display: none;" >cancel</a>': '';?> </td>
						<script type="text/javascript" charset="utf-8" async defer>
							$('#edit_email').on('click', function() {
								var container = $(this).parent().find('b');
								var link = container.find('a').attr('href');
								var oldEmail = container.text();
								container.text('');
								container.append('<input type="email" name="email" data-link="'+ link +'" data-oldemail="'+ oldEmail +'" value="'+ oldEmail +'" required/>');
								$(this).hide();
								$('#update_email').show();
								$('#cancle_email').show();
							});
							$('#cancle_email').on('click', function() {
								var container = $(this).parent().find('b');
								var input = container.find('input');
								var oldEmail = input.attr('data-oldemail');
								var link = input.attr('data-link');
								container.text('');
								$('#update_email').hide();
								$('#edit_email').show();
								container.append('<a href="'+ link +'">'+ oldEmail +'</a>');
							});
							$('#update_email').on('click', function() {
								var container = $(this).parent().find('b');
								var input = container.find('input');
								var oldEmail = input.attr('data-oldemail');
								var email = input.val();
								var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
								if (email == '' || !re.test(email)) {
									alert('Please Enter a Valid Email');
								} else {
									$.ajax({
										url: 'viewOrderDetail.php?order=<?= $orderID; ?>',
										type:"POST",
										dataType:"json",
										data:{'email':email,'oldEmail':oldEmail, 'action':'update_email'},
										success: function(json){
											if (json['success']) {
												alert('Email Changed');
												window.location.replace("viewOrderDetail.php?order=" + json['msg']);
											}
										}
									});
								}
							});
							function shippingCost()
							{
								var obj = document.getElementById('select_shipping').value;
								if (obj) {
									$('#shipping_method').attr('value', $('#select_shipping option:selected').text());
								} else {
									$('#shipping_method').attr('value', '');
								}
								<?php
								if(strtolower($order['order_status'])=='estimate')
								{
									?>
									if($('#shipping_method').val()=='Customer FedEx' || $('#shipping_method').val()=='Customer UPS' )
									{
										$('.customer_fedex_code').show();
									}
									else
									{
										$('.customer_fedex_code').hide();
									}
									<?php
								}
								?>
								if($('#shipping_method').val()=='Custom Shipping')
								{
									$('.customer_fedex_code').show();
									$('#shipping_cost').removeAttr('readonly');
								}
								else
								{
									$('.customer_fedex_code').hide();
									$('#shipping_cost').attr('readonly');	
								}
								if($('#shipping_method').val()=='Other Shipping')
								{	
									$('#other_shipping_name').show();
									$('#shipping_cost').removeAttr('readonly');
									$('#shipping_cost').attr('style','background-color:#ffffff');
								}
								else
								{
									$('#other_shipping_name').hide();
									$('#shipping_cost').attr('readonly');	
									$('#shipping_cost').attr('style','background-color:#d3d3d3');
								}
								var shipping = obj.split("-");
								<?php if (($order_paid_type=='paid') || $order['payment_source'] == 'Paid' || $order['payment_source'] == 'PayPal' || $order['payment_source'] == 'Payflow') { } else {?>

								$('#shipping_cost').attr('value', shipping[0]); 
								<?php
							}
							?>
								$('#shipping_code').attr('value', shipping[1]); 
							}
						</script>
					</tr>
					<tr>
						<th>Payment Method : </th>
						<td align="center"> <span id="span_payment_method"> <?= ($order['store_type'] == 'amazon' || $order['store_type']=='amazon_fba')? 'amazon': $order['payment_method']; ?></span>
							<?php
							if($_SESSION['edit_payment_method'] ):
							?>
							<select id="temp_payment_method" onchange="changePaymentMethod(this)">
								<option value="">Default Selection</option>
								<option value="Cash or Credit at Store Pick-Up">Cash or Credit at Store Pick-Up</option>
								<option value="Card">Card</option>
								<option value="PayPal">PayPal</option>
								<?php if ($order['payment_method'] == 'Cash On Delivery') { ?>
								<option selected="selected" value="Cash On Delivery">COD</option>
								<?php } else { ?>
								<option value="Cash On Delivery">COD</option>
								<?php } ?>
									<?php if ($order['payment_method'] == 'Behalf') { ?>
								<option selected="selected" value="Behalf">Behalf</option>
								<?php } else { ?>
								<option value="Behalf">Behalf</option>
								<?php } ?>

								<?php if ($order['payment_method'] == 'Wire Transfer') { ?>
								<option selected="selected" value="Wire Transfer">Wire Transfer</option>
								<?php } else { ?>
								<option value="Wire Transfer">Wire Transfer</option>
								<?php } ?>
							</select>

							<?php
							endif;
							?>
						</td>
						<th>Shipping Method : </th>
						<td style="font-weight:bold"> * <?= $order['shipping_method'] ?>
							<?php
							if ($order['shipping_method'] == 'Customer FedEx' || $order['shipping_method'] == 'Customer UPS' || $order['shipping_method'] == 'Custom Shipping' ) {
								echo '(' . $order['customer_fedex_code'] . ')';
							}
							?> * 
							<br>
							<?php if (($order_paid_type=='paid') || $order['payment_source'] == 'Paid' || $order['payment_source'] == 'PayPal' || $order['payment_source'] == 'Payflow') { ?>
							<select style="width:200px;margin-top:5px;<?php //echo ((strtolower($order['order_status'])!='estimate')? 'display:none': ''); ?>" disabled onchange="shippingCost()" id="select_shipping">
								<?php } else { ?>
								<select style="width:200px;margin-top:5px;<?php //echo ((strtolower($order['order_status'])!='estimate')? 'display:none': ''); ?>" onchange="shippingCost()" id="select_shipping">
									<?php } ?>
								
						
								<option value="">Select Shipping Method</option>
							</select>
							<input type="text" name="customer_fedex_code" class="customer_fedex_code" placeholder="Please provide Code " maxlength="10" style="width:150px;<?php if (($order['shipping_method'] == 'Customer FedEx' || $order['shipping_method'] == 'Customer UPS') && strtolower($order['order_status'])=='estimate' || ($order['shipping_method'] == 'Custom Shipping')) {}else{ echo 'display:none';}?>" value="<?php echo $order['customer_fedex_code'];?>" />
							<input type="hidden" name="shipping_method" id="shipping_method" />
							<input type="hidden" name="shipping_code" id="shipping_code" />
							<input type="text" size="6" readOnly style="background-color:#d3d3d3" name="shipping_cost" id="shipping_cost" value="<?=$order['shipping_cost'];?>" /><br>
							<input type="text" size="23" style="display: none;"  name="other_shipping_name" id="other_shipping_name" placeholder="Enter Other Shipping Method" value="<?=$order['other_shipping_name'];?>" />
							<?php if($user['name']== "admin") ?>
							<input type="checkbox" id="chk" name="chk" onchange="addpickedup(this)" /> Allow POS Return
						</td>
					</tr>
					<tr>
						<?php if (!$is_po && ($_SESSION['display_order_price'] || $_SESSION['login_as'] == 'admin')){?>
							<th>Shipping Cost : </th>
							<td> $<?= $order['shipping_cost'] ?> </td>
						<?php } else { ?>
						<?php if (!$is_po):?>
						<th></th>
						<td></td>
						<?php
						endif;
						?>
						<?php } ?>
						<th>Phone : </th>
						<td> <?= $order['phone_number'] ?> </td>
						<?php if ($is_po):?>
							<th>Reference :</th>
							<td><input name="reference_no" maxlength="35" size="30" placeholder="Max 35 Characters" value="<?= $order['reference_no']; ?>" type="text"></td>
						<?php endif;?>
					</tr>
					<?php if (($order['store_type'] == 'web' || $order['store_type']=='po_business')  && $order_total > $order['paid_price']) { ?>
					<tr class="voucher">
						<th>Voucher <b><span class="total"></span></b></th>
						<td colspan="3"><input type="text" name="voucher_codes" id="voucher_code"  placeholder="Add multiple voucher sapreted by comma (,)"  value="<?=$_POST['orders_details']['voucher_code'];?>" size="30"/><br><span class="error" style="color: #F00;"></span> <input type="button" class="button" value="Apply" onclick="verifyVoucher($('#voucher_code'));"></td>
					</tr>
					<?php } ?>
					<tr>
						<?php $ip = $db->func_query_first('SELECT ip,user_agent FROM oc_order WHERE cast(order_id as char(50)) = "'. $orderID .'"'); ?>
						
						<?php if ($ip['ip']!='') { ?>
						<?php
						$isp_details = curl('http://ip-api.com/json/'.$ip['ip']);
						// echo $isp_details;
						// $browser_details = curl('http://www.useragentstring.com/?uas='.urlencode($ip['user_agent']).'&getJSON=all');
						?>
						<th>IP Details : </th>
						<td><?= $ip['ip']; ?>
						<?php
						if($isp_details)
						{

							$isp_details = json_decode($isp_details,true);
							$browser_details = json_decode($browser_details,true);
							// print_r($isp_details);
						?>
						<br><?php echo $isp_details['isp'];?><br><?php echo ($isp_details['city']?$isp_details['city']:'N/A').($isp_details['region']?', '.$isp_details['region']:'').($isp_details['zip']?', '.$isp_details['zip']:'');?>.
						<br>
						<?php 
						// echo $browser_details['agent_name'].' '.$browser_details['agent_version'].' ('.$browser_details['os_name'].')';

						?>
						<?php
					}
					?>
						</td>
						<?php } ?>
						<?php
					//if($transaction_id=='')
					//{
						if(!$transaction_dets)
						{
							$transaction_dets = $db->func_query("SELECT transaction_id,amount FROM inv_transactions WHERE order_id = '".$orderID."'");
							//testobject($transaction_dets);
						}
						// if($paypal_check)
if(!$transaction_dets)
						{
							$transaction_dets = $db->func_query("SELECT transaction_id,amount FROM oc_paypal_admin_tools WHERE cast(order_id as char(50))='".$orderID."'");
						}
						// else if($payflow_check)
						if(!$transaction_dets)
						{
							$transaction_dets = $db->func_query("SELECT pp_transaction_id,transaction_id,amount FROM oc_payflow_admin_tools WHERE cast(order_id as char(50))='".$orderID."'");
						}
						
						// Payflow has 2 transaction ids, PNREF & PPREF, if PPREF exists, it interchanges the data
						//print_r($transaction_dets);
						if($transaction_dets)
						{
							$transaction_id = '';
								$t_amounts = 0.00;
							foreach($transaction_dets as $transaction_det)
							{
								if($transaction_det['pp_transaction_id'])
								{
									$transaction_det['transaction_id'] = $transaction_det['pp_transaction_id'];
								}
								$t_amounts+=$transaction_det['amount'];
								$transaction_id .= $transaction_det['transaction_id'].' ( $'. (($transaction_det['amount'])? number_format($transaction_det['amount'],2): number_format($order['paid_price'],2)).' )<br>';
						//print_r($transaction_det);
							}
						}
							if($t_amounts && $order['paid_price']==0.00)
							{
								$order['paid_price'] = $t_amounts;
							}
					//}
						if(!$transaction_id) $transaction_id = $order['transaction_id'];
						?>
						<?php if ($transaction_id && ($_SESSION['display_order_price'] || $_SESSION['login_as'] == 'admin')) { ?>
						<th>Transaction ID : </th>
						<td>
							<?php
							?>
							<?= $transaction_id; ?> </td>
							<?php } ?>
						</tr>
						<?php if ($is_po || $order['store_type'] == 'web'):?>
							<tr>
								<th colspan="4" align="center">--- PAYMENT DETAILS ---</th>
							</tr>
							<tr>
								<th>Payment Source :</th>
								<td>
									<br>	
										<?php if ($order['payment_method'] == 'Cash On Delivery' && $order['is_cod_wire'] == 1) {?>
										<button disabled="disabled" >Receive COD Payment</button><br></br>
										<table border="1">
										<tr><td>Ref #</td><td>Date Received</td><td>Amount</td></tr>
										<tr><td><?php echo $order['cod_wire_ref'];?></td><td><?php echo $order['cod_wire_date'];?></td><td>$<?php echo $order['paid_price'];?></td></tr>
									</table>
									<br></br>
									<?php	} else { ?>
									<div style="<?php echo ($order['payment_method'] == 'Cash On Delivery' && $order['is_cod_wire']==0?'':'display:none');?>" id="cod_div">
											<a href="popupfiles/cod_payment.php?order_id=<?= $orderID; ?>" class="fancybox4 fancybox.iframe button" >Receive COD Payment</a>
									</div>
										<?php } ?>
										
										<br>	
										<?php if ($order['payment_method'] == 'Wire Transfer' && $order['is_cod_wire'] == 1) {?>
										<button disabled="disabled" >Receive Wire Transfer</button><br></br>
										<table border="1">
											<tr><td>Ref #</td><td>Date Received</td><td>Amount</td></tr>
											<tr><td><?php echo $order['cod_wire_ref'];?></td><td><?php echo $order['cod_wire_date'];?></td><td>$<?php echo $order['paid_price'];?></td></tr>
										</table>
										<br></br>
										<?php	} else { ?>
										<div style="<?php echo ($order['payment_method'] == 'Wire Transfer' && $order['is_cod_wire']==0?'':'display:none');?>" id="wire_div">
											<a href="popupfiles/wire_transfer.php?order_id=<?= $orderID; ?>"  class="fancybox4 fancybox.iframe button" >Receive Wire Transfer</a>
										</div>
										<?php } ?>
									<?php
									if (round($order_total - $order['paid_price'], 2) > 0) {
										?>
										<a href="popupfiles/charge_card.php?order_id=<?= $orderID; ?><?= ($order['store_type'] == 'web')? '&payment=full': ''; ?>" class="fancybox4 fancybox.iframe button" >Charge Card</a> 
										<?php if ($is_po): ?>
											<a href="popupfiles/payment_status.php?order_id=<?= $orderID; ?>" class="fancybox4 fancybox.iframe button" >Other Method</a>
										<?php endif; ?>
										<?php
									}
									else
									{
										echo ucfirst($order['po_payment_source']);
									}
									?>
									<input type="hidden" name="po_payment_source" id="po_payment_source" value="<?php echo $order['po_payment_source']; ?>" />
									<input type="hidden" name="po_payment_source_detail" id="po_payment_source_detail" value="<?php echo $order['po_payment_source_detail']; ?>" />
									<input type="hidden" name="po_payment_source_amount" id="po_payment_source_amount" value="<?php echo $order['po_payment_source_amount']; ?>" />
									<!-- <span id="po_payment_source_name"><?php echo ucfirst($order['po_payment_source']); ?></span> -->
								</td> 
								<?php if ($is_po): ?>
									<th>Details :</th>
									<td>
										<?php
										echo $order['payment_detail_1'] . "<br>";
										if($order['payment_detail_2']!=$order['payment_detail_1'])
										{
											echo $order['payment_detail_2'] . "<br>";
										}
										?>
									</td>
									<?php
									else:
									?>
								<th>PO # :</th>
								<td><input type="text" name="po_order_number" id="po_order_number" value="<?php echo $order['po_order_number']; ?>" /></td>
								<?php endif; ?>
							</tr>
							
								<tr>
									<th>Terms</th>
									<td>
										
										<select name="terms" id="terms">
											<option value="Prepaid" <?php echo ($order['terms']=='Prepaid'?'selected':'');?>>Prepaid</option>
							<option value="Net 15" <?php echo ($order['terms']=='Net 15'?'selected':'');?>>Net 15</option>
							<option value="Net 30" <?php echo ($order['terms']=='Net 30'?'selected':'');?>>Net 30</option>
										</select>
									</td>
									<?php
									if($_SESSION['can_lock_price'])
									{
									?>
									<th>Lock Prices</th>
									<td>	<label class="switch"><input type="checkbox" <?php echo ($order['lock_prices']==1?'checked':'');?> name="lock_prices"><div class="slider round"><!--ADDED HTML --><span class="on">ON</span><span class="off">OFF</span><!--END--></div></label></td>
									<?php
								}
								else
								{
									?>
									<td></td>
									<td></td>
									<?php
								}
								?>
									</tr>
									
									
							
							<?php if ($is_po): ?>
								<tr>
									<th>Terms</th>
									<td>
										<?php $terms = array(5, 10, 15, 30, 45); ?>
										<select name="po_term" id="po_term">
											<option value="0">No Terms</option>
											<?php
											foreach ($terms as $term) {
												?>
												<option value="<?= $term; ?>" <?php if ($term == $order['po_term']) echo 'selected'; ?>>Net <?= $term; ?></option>
												<?php
											}
											?>
										</select>
									</td>
									<?php if ($order['shipping_date']):?>
										<th>Due Date:</th>
										<?php if ($order['po_term'] == 0):?>
											<td>
												No Terms
											</td>
										<?php else: ?>
											<td>
												<?php echo date('d M Y', strtotime($order['shipping_date'] . ' + ' . (int) $order['po_term'] . ' days')); ?>
											</td>
										<?php endif; ?>
									<?php endif; ?>
								</tr>
							<?php endif; ?>

							<?php
									if($order['payment_response_data']!='')
									{
									?>
							<tr>
									<th>Raw Details:</th>
									<td colspan="3" style="word-break:break-word"><?php echo urldecode($order['payment_response_data']);?></td>

									</tr>
									<?php
								}
								?>

								<?php
									if((strtolower($order['payment_method'])=='check' || strtolower($order['payment_method'])=='behalf' || strtolower($order['payment_method'])=='wire transfer' || strtolower(substr($order['payment_method'], 0,4))=='cash') && strtolower($order['order_status'])=='shipped' && $order['is_deposited']==0 )
									{
									?>
							<tr>
									<th>Undeposited Terms:</th>
									<td colspan="3" ><a href="<?php echo $host_path;?>popupfiles/make_payment_order.php?order=<?php echo $order['order_id']; ?>" class="fancybox2 fancybox.iframe button button-danger" >Make Payment</a></td>

									</tr>
									<?php
								}
								?>


						<?php endif; ?>
					</table>
					<br />
					<table cellpadding="5" style="border:1px solid #ddd; border-collapse: collapse;" cellspacing="0" width="70%" border="1">
						<tr>
							<td width="50%">
								<h3 align="center">Shipping</h3>
								<table width="100%" border="0" align="left">
									<?php
									if($order['shipping_firstname']=='')
									{
										$order['shipping_firstname'] = $order['first_name'];
										$order['shipping_lastname'] = $order['last_name'];
									}
									?>
									<?php
									
									$previous_addresses = $db->func_query("select distinct b.first_name,b.last_name,b.shipping_firstname,b.shipping_lastname,b.company,b.address1,b.address2,b.city,b.zone_id,b.country_id,b.zip from inv_orders_details b,inv_orders a where a.order_id=b.order_id and trim(lower(a.email))='".trim(strtolower($order['email']))."' and lower(a.order_status) in ('shipped','processed','on hold','completed') and a.order_id<>'".$order['order_id']."' order by a.order_date desc");
									?>

									<?php if ($_SESSION['edit_order_details']) { ?>
										<tr>
										<th >Previous Addresses : </th>
										<td>
											<select onchange="populateAddress('shipping',this)" style="width:75%">
												<option value="">Please Select</option>
												<?php
												foreach($previous_addresses as $previous_address)
												{
													?>
												<option value="<?php echo $db->func_escape_string(($previous_address['shipping_firstname']?$previous_address['shipping_firstname'].'~'.$previous_address['shipping_lastname']:$previous_address['first_name'].'~'.$previous_address['last_name']).'~'.$previous_address['company'].'~'.$previous_address['address1'].'~'.$previous_address['address2'].'~'.$previous_address['city'].'~'.$previous_address['zone_id'].'~'.$previous_address['country_id'].'~'.$previous_address['zip']);?>"><?php echo ($previous_address['shipping_firstname']?$previous_address['shipping_firstname'].' '.$previous_address['shipping_lastname']:$previous_address['first_name'].' '.$previous_address['last_name']).', '. $previous_address['address1'].', '.($previous_address['address2']?$previous_address['address2'].',':'').$previous_address['city'].', '.$db->func_query_first_cell("SELECT name from oc_zone where zone_id='".$previous_address['zone_id']."'").', '.$previous_address['zip'].'.';?></option>

													<?php
												}
												?>
											</select>
										</td>
									</tr>

									<?php
								}
								?>

									<tr>
										<th width="30%">Name : </th>
										<td>
											<?php if ($_SESSION['edit_order_details']) { ?>
											<input type="text" name="first_name" size="15" value="<?php echo $order['shipping_firstname']; ?>" /> 
											<input type="text" name="last_name" size="15" value="<?php echo $order['shipping_lastname']; ?>" /> 
											<?php } else { ?>
											<?php echo $order['shipping_firstname'] . ' ' . $order['shipping_lastname']; ?>
											<?php } ?>
										</td>
									</tr>
									<tr>
											<th>Company :</th>
											<td><input type="text" name="company_shipping" size="15" value="<?php echo html_entity_decode($order['company']); ?>" /> 
										 </td>
									<tr>
										<th>Address : </th>
										<td>
											<?php if ($_SESSION['edit_order_details']) { ?>
											<input type="text" name="address1" size="15" value="<?php echo $order['address1']; ?>" /> 
											<input type="text" name="address2" size="15" value="<?php echo $order['address2']; ?>" /> 
											<?php } else { ?>
											<input type="hidden" name="address1"   value="<?php echo $order['address1']; ?>" /> 
											<input type="hidden" name="address2"  value="<?php echo $order['address2']; ?>" />
											<?php echo $order['address1'] . " " . @$order['address2']; ?>
											<?php } ?>
										</td>
									</tr>
									<tr>
										<th>City : </th>
										<td>
											<?php if ($_SESSION['edit_order_details']) { ?>
											<input type="text" name="city" size="15" value="<?php echo $order['city']; ?>" /> 
											<?php } else { ?>
											<input type="hidden" name="city"  value="<?php echo $order['city']; ?>" /> 
											<?php echo $order['city']; ?>
											<?php } ?>
										</td>
									</tr>
									<tr>
										<th>State : </th>
										<td>
											<?php if ($_SESSION['edit_order_details']) { ?>
											<!-- <input type="text" name="state" size="15" value="<?php echo $order['state']; ?>" />  -->

											<?php

											$states = getStates();
											?>
											<select name="zone_id">
											<option value="0">Please Select</option>
											<?php
											foreach($states as $state)
											{
												?>
												<option value="<?php echo $state['zone_id'];?>" <?php echo ($state['zone_id']==$order['zone_id']?'selected':'');?> ><?php echo $state['name'];?></option>
												<?php
											}
											?>
											</select>
											<?php } else { ?>
											<input type="hidden" name="zone_id"  value="<?php echo $order['zone-id']; ?>" /> 
											<?php echo $order['state']; ?>
											<?php } ?>
										</td>
									</tr>
									<tr>
										<th>Country : </th>
										<td>

											<?php if ($_SESSION['edit_order_details']) { ?>
											<?php

											$countries = getCountries();
											?>
											<select name="country_id">
											<option value="0">Please Select</option>
											<?php
											foreach($countries as $country)
											{
												?>
												<option value="<?php echo $country['country_id'];?>" <?php echo ($country['country_id']==$order['country_id']?'selected':'');?> ><?php echo $country['name'];?></option>
												<?php
											}
											?>
											</select>
											<?php } else { ?>
											<input type="hidden" name="country_id"  value="<?php echo $order['country_id']; ?>" /> 
											<?php echo $order['country']; ?>
											<?php } ?>
										</td>
									</tr>
									<tr>
										<th>Zip : </th>
										<td>
											<?php if ($_SESSION['edit_order_details']) { ?>
											<input type="text" name="zip" size="15" value="<?php echo $order['zip']; ?>" /> 
											<?php } else { ?>
											<input type="hidden" name="zip"  value="<?php echo $order['zip']; ?>" /> 
											<?php echo $order['zip']; ?>
											<?php } ?>
										</td>
									</tr>
								</table>
							</td>
							<td width="50%">
								<?php if ($_SESSION['edit_order_details']) { ?>
								<input type="checkbox" onChange="copyShippingDetails(this)"> Same as Shipping?
								<?php
							}
							?>
							<h3 align="center">Billing</h3>
							<table width="100%" border="0" align="left">
								<?php
								if($order['bill_firstname']=='')
								{
									$order['bill_firstname'] = $order['first_name'];
									$order['bill_lastname'] = $order['lastname'];
								}
								?>

									<?php if ($_SESSION['edit_order_details']) { ?>
									<?php
									$previous_addresses = $db->func_query("select distinct b.first_name,b.last_name,b.bill_firstname,b.bill_lastname,b.billing_company,b.bill_address1,b.bill_address2,b.bill_city,b.bill_zone_id,b.bill_country_id,b.bill_zip from inv_orders_details b,inv_orders a where a.order_id=b.order_id and trim(lower(a.email))='".trim(strtolower($order['email']))."' and lower(a.order_status) in ('shipped','processed','on hold','completed') and a.order_id<>'".$order['order_id']."' and trim(b.bill_address1)<>'' order by a.order_date desc");
									?>

										<tr>
										<th >Previous Addresses : </th>
										<td>
											<select onchange="populateAddress('billing',this)" style="width:75%">
												<option value="">Please Select</option>
												<?php
												foreach($previous_addresses as $previous_address)
												{
													?>
												<option value="<?php echo $db->func_escape_string(($previous_address['bill_firstname']?$previous_address['bill_firstname'].'~'.$previous_address['bill_lastname']:$previous_address['first_name'].'~'.$previous_address['last_name']).'~'.$previous_address['billing_company'].'~'.$previous_address['bill_address1'].'~'.$previous_address['bill_address2'].'~'.$previous_address['bill_city'].'~'.$previous_address['bill_zone_id'].'~'.$previous_address['bill_country_id'].'~'.$previous_address['bill_zip']);?>"><?php echo ($previous_address['bill_firstname']?$previous_address['bill_firstname'].' '.$previous_address['bill_lastname']:$previous_address['first_name'].' '.$previous_address['last_name']).', '. $previous_address['bill_address1'].', '.($previous_address['bill_address2']?$previous_address['bill_address2'].',':'').$previous_address['bill_city'].', '.$db->func_query_first_cell("SELECT name from oc_zone where zone_id='".$previous_address['bill_zone_id']."'").', '.$previous_address['bill_zip'].'.';?></option>

													<?php
												}
												?>
											</select>
										</td>
									</tr>

									<?php
								}
								?>
								<tr>
									<th>Name : </th>
									<td>
										<?php if ($_SESSION['edit_order_details']) { ?>
										<input type="text" name="bill_firstname" size="15" value="<?php echo $order['bill_firstname']; ?>" /> 
										<input type="text" name="bill_lastname" size="15" value="<?php echo $order['bill_lastname']; ?>" /> 
										<?php } else { ?>
										<input type="hidden" name="bill_firstname"  value="<?php echo $order['bill_firstname']; ?>" /> 
										<input type="hidden" name="bill_lastname"  value="<?php echo $order['bill_lastname']; ?>" /> 
										<?php echo $order['bill_firstname'] . " " . @$order['bill_lastname']; ?>
										<?php } ?>
									</td>
								<tr>
											<th>Company</th>
											<td><input type="text" name="company_billing" size="15" value="<?php echo html_entity_decode($order['billing_company']); ?>" /> 
										<!--	<input type="text" name="address2" size="15" value="<?php echo $order['company']; ?>" /> --> </td>
								</tr>
								<tr>
									<th>Address : </th>
									<td>
										<?php if ($_SESSION['edit_order_details']) { ?>
										<input type="text" name="bill_address1" size="15" value="<?php echo $order['bill_address1']; ?>" /> 
										<input type="text" name="bill_address2" size="15" value="<?php echo $order['bill_address2']; ?>" /> 
										<?php } else { ?>
										<input type="hidden" name="bill_address1"  value="<?php echo $order['bill_address1']; ?>" /> 
										<input type="hidden" name="bill_address2"  value="<?php echo $order['bill_address2']; ?>" /> 
										<?php echo $order['bill_address1'] . " " . $order['bill_address2']; ?>
										<?php } ?>
									</td>
								</tr>
								<tr>
									<th>City : </th>
									<td>
										<?php if ($_SESSION['edit_order_details']) { ?>
										<input type="text" name="bill_city" size="15" value="<?php echo $order['bill_city']; ?>" /> 
										<?php } else { ?>
										<input type="hidden" name="bill_city"  value="<?php echo $order['bill_city']; ?>" /> 
										<?php echo $order['bill_city']; ?>
										<?php } ?>
									</td>
								</tr>
								<tr>
									<th>State : </th>
									<td>
										<?php if ($_SESSION['edit_order_details']) { ?>
										<!-- <input type="text" name="bill_state" size="15" value="<?php echo $order['bill_state']; ?>" />  -->

										<select name="bill_zone_id">
											<option value="0">Please Select</option>
											<?php
											foreach($states as $state)
											{
												?>
												<option value="<?php echo $state['zone_id'];?>" <?php echo ($state['zone_id']==$order['bill_zone_id']?'selected':'');?> ><?php echo $state['name'];?></option>
												<?php
											}
											?>
											</select>
										<?php } else { ?>
										<input type="hidden" name="bill_zone_id"  value="<?php echo $order['bill_zone_id']; ?>" /> 
										<?php echo $order['bill_state']; ?>
										<?php } ?>
									</td>
								</tr>
								<tr>
									<th>Country : </th>
									<td>
										<?php if ($_SESSION['edit_order_details']) { ?>
										
											<select name="bill_country_id">
											<option value="0">Please Select</option>
											<?php
											foreach($countries as $country)
											{
												?>
												<option value="<?php echo $country['country_id'];?>" <?php echo ($country['country_id']==$order['bill_country_id']?'selected':'');?> ><?php echo $country['name'];?></option>
												<?php
											}
											?>
											</select>

										<!-- <input type="text" name="bill_country" size="15" value="<?php echo $order['bill_country']; ?>" />  -->
										<?php } else { ?>
										<input type="hidden" name="bill_country_id"  value="<?php echo $order['bill_country_id']; ?>" /> 
										<?php echo $order['bill_country']; ?>
										<?php } ?>
									</td>
								</tr>
								<tr>
									<th>Zip : </th>
									<td>
										<?php if ($_SESSION['edit_order_details']) { ?>
										<input type="text" name="bill_zip" size="15" value="<?php echo $order['bill_zip']; ?>" /> 
										<?php } else { ?>
										<input type="hidden" name="bill_zip"  value="<?php echo $order['bill_zip']; ?>" /> 
										<?php echo $order['bill_zip']; ?>
										<?php } ?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<br />
				<br />
				<div align="center">
					<input type="button" class="button button-info" onclick="$('input[name=update]').click();" value="Update Orders" />
				</div>  
				<br />
				<br />
				<div align="left">
					<strong style="position: absolute;margin-left:38px;margin-top:10px">Show Warehouse Data? </strong><label class="switch" style="margin-left:190px"><input type="checkbox" id="warehouse_toggle" onchange="changeWarehouseToggle(this)"><div class="slider round"><!--ADDED HTML --><span class="on">ON</span><span class="off">OFF</span><!--END--></div></label>

					<?php
					if($_SESSION['login_as']=='admin')
					{
						?>
						<strong style="position: absolute;margin-left:80px;margin-top:10px">Show True Cost? </strong><label class="switch" style="margin-left:190px"><input type="checkbox" id="true_cost_toggle" onchange="changeTrueCostToggle(this)"><div class="slider round"><!--ADDED HTML --><span class="on">ON</span><span class="off">OFF</span><!--END--></div></label>

						<?php
					}
					?>

					</div>
					<br>

				<table align="center" border="1" cellspacing="0" style="border:1px solid #ddd;border-collapse:collapse;" cellpadding="5" width="95%" <?php echo (isset($_GET['debug'])?'class="xtable"':'');?>>
					<thead>
						<tr style="font-weight:bold">
							<td rowspan="2" align="center" width="8%">Item ID</td>
							<td rowspan="2" align="center" width="10%">SKU</td>
							<td rowspan="2" align="center" style="" width="27%">Item Name</td>
							<td colspan="2" align="center" width="13%" >Qty <?php if($_SESSION['vendor_po']){ ?> <a href="<?php echo $host_path;?>vendor_po_create.php?order=<?php echo $order['order_id']; ?>" class="fancybox3 fancybox.iframe" >Create PO</a> <?php } ?>
							</td>
							<?php if($_SESSION['display_order_price'] || $_SESSION['login_as'] == 'admin'){ ?>
							<td rowspan="2" class="toggle_prices" width="7%">Price</td>

							<?php }?>
							<?php
							if ($_SESSION['display_cost'] || $_SESSION['login_as'] == 'admin') {
								?>
								<td rowspan="2" style="display: none;">True Cost</td>
								<?php
							}
							?>
							<td rowspan="2" style="display: none;">% Discount</td>
							<td rowspan="2" style="display: none;"><?php echo ($is_replacement_order?'Replacement Amt':'P. Discount');?></td>
							<?php
							if ($_SESSION['login_as'] == 'admin') {
								?>
								<td rowspan="2" class="toggle_true_cost" >Line True Cost</td>
								<?php
							}
							?>
							<?php
							if ($_SESSION['display_profit_margin'] || $_SESSION['login_as'] == 'admin') {
								?>
							<td rowspan="2" align="center" class="toggle_prices" width="7%">% Profit Margin</td>
							<?php
						}
						?>
							<?php if($_SESSION['display_order_price'] || $_SESSION['login_as'] == 'admin'){ ?>
							<td rowspan="2" class="toggle_prices" width="9%">Line Total</td>
							<?php } ?>
							
							<td rowspan="2" class="toggle_warehouse" align="center">Picked</td>
							<td rowspan="2" class="toggle_warehouse" align="center">Packed</td>
							<td rowspan="2" align="center" width="19%">Action <?php if ((strtolower($order['order_status']) == 'estimate' || strtolower($order['order_status']) == 'processed' || strtolower($order['order_status']) == 'on hold' || $_SESSION['login_as'] == 'admin') && ($_SESSION['login_as'] == 'admin' || $_SESSION['add_product_order'])) { ?><a class="additem" href="javascript:void(0);" onclick="addRow();">Add Row</a> <?php } ?></td>
						</tr>
						<tr>
							<th>On Shelf</th>
							<th style="width: 60px;">Ordered</th>
						</tr>
					</thead>
					<tbody class="itemholder">
						<?php 
						$true_cost_total = 0.00;
						$i=0;
						$total_items = 0;
						foreach ($order_items as $order_item): ?>
						<?php

						$total_items+=$order_item['product_qty']; 
								//$stock=$db->func_query_first("SELECT a.quantity,(select b.picked+b.packed from inv_product_ledger b where a.model=b.sku order by id desc limit 1 ) as picked_packed from oc_product a WHERE a.model = '".$order_item['product_sku']."' ");


								$stock_quantity=$db->func_query_first_cell("select a.quantity FROM oc_product a WHERE TRIM(LOWER(a.model))='".trim(strtolower($order_item['product_sku']))."' ");
								$picked_qty = $db->func_query_first_cell("SELECT sum(b.picked_quantity) - sum(b.packed_quantity) FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','unshipped','on hold') and b.is_picked=1 and a.is_packed=0 and trim(lower(b.product_sku))='".strtolower(trim($order_item['product_sku']))."'");
		$packed_qty = $db->func_query_first_cell("SELECT sum(b.packed_quantity) FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','unshipped') and b.is_picked=1 and trim(lower(b.product_sku))='".strtolower(trim($order_item['product_sku']))."'");

								$item_count=$db->func_query_first_cell("SELECT COUNT(id) from inv_orders_items WHERE product_sku = '".$order_item['product_sku']."' AND order_id = '".$order_item['order_id']."' ");
								$image = $db->func_query_first_cell("SELECT image from oc_product  WHERE model = '".$order_item['product_sku']."' ");
								$image_arr = explode('.', $image);
								$cache_img = $image_arr[0].'-50x50.'.$image_arr[1];
						?>
						<tr id="pItem_<?= $order_item['id']; ?>">
							<td align="center" title="<?php echo ($order_item['product_name'])? $order_item['product_name']: $order_item['product_sku']; ?>"><?php echo ($order_item['order_item_id'])? $order_item['order_item_id']: $order_item['id']; ?></td>
							<td align="center"><div>
							<input type="hidden" id="xproduct_sku_<?= $i; ?>" value="<?php echo $order_item['product_sku']; ?>" class="read_class" style="width:110px;" readOnly > 
							
							<input type="hidden" id="order_item_sku_<?= $order_item['id']; ?>" value="<?php echo $order_item['product_sku']; ?>" class="read_class" style="width:110px;" readOnly >

							<a href="<?php echo $host_path;?>product/<?php echo $order_item['product_sku'];?>"><?php echo $order_item['product_sku'];?></a></div><br>
							<div>
								<a href="<?php echo $host_path;?>product/<?php echo $order_item['product_sku'];?>" target="_blank">
									<img style="width:60px;height:50px;" src="https://www.phonepartsusa.com/image/cache/<?php echo $cache_img;?>">
								</a>
							</div></td>
							<td style="width: 80px;"><?php echo getItemName($order_item['product_sku']);?></td>
							<td align="center" style="width: 30px;background-color: rgb(242, 242, 242);"><?php echo (int)$stock_quantity - (int)$picked_qty - (int)$packed_quantity; ?></td>
							<td align="center">	
							<input type="text" id="order_item_qty_<?= $order_item['id']; ?>" value="<?php echo $order_item['product_qty']; ?>" class="read_class" readOnly style="width:50px" onChange="calculateLineTotal('<?= $order_item['id']; ?>')" ></td>
							<?php if($_SESSION['display_order_price'] || $_SESSION['login_as'] == 'admin'){ ?>
							<td class="toggle_prices">
							<?php } else { ?>
							<td style="display: none;">
							<?php } ?>
								<input type="text" id="order_item_unit_<?= $order_item['id']; ?>" value="<?php echo ($order_item['product_unit']>0.00 ? $order_item['product_unit'] : round($order_item['product_price'] / $order_item['product_qty'], 2)); ?>" class="read_class" readOnly style="width:80px" onChange="calculateLineTotal('<?= $order_item['id']; ?>')">
							</td>
							<?php if ($_SESSION['login_as'] == 'admin'):
							$true_cost = $order_item['product_true_cost'];
							if($true_cost==0.00)
							{
								$true_cost = getTrueCost($order_item['product_sku']);
								$db->db_exec("UPDATE inv_orders_items SET product_true_cost='".(float)$true_cost."' WHERE id='".$order_item['id']."'");
							}
							?>
							<td class="toggle_true_cost" >
								$<?php echo number_format($true_cost,2);?>
								<input type="hidden" id="product_true_cost_<?= $order_item['id']; ?>" value="<?php echo number_format($true_cost,2); ?>">
							</td>
						<?php endif; ?>
						<td style="display: none;"><input type="text" id="order_item_discount_<?= $order_item['id']; ?>" value="<?php echo $order_item['product_discount']; ?>" class="read_class" readOnly style="width:40px" onChange="calculateLineTotal('<?= $order_item['id']; ?>')" ></td>
						<td style="display: none;">$<?php echo number_format($order_item['promotion_discount'],2);?></td>
						<?php
							if ($_SESSION['display_profit_margin'] || $_SESSION['login_as'] == 'admin') {
								?>
						<td align="center" class="toggle_prices"> <?php 
						$p = $order_item['product_price']-$order_item['promotion_discount'];
						$c = $true_cost*$order_item['product_qty'];
						$numerator = $p-$c;
						echo number_format(($numerator/$c)*100,2);


						?>%</td>
						<?php
					}
					?>
						<?php if($_SESSION['display_order_price'] || $_SESSION['login_as'] == 'admin'){ ?>
							<td class="toggle_prices" align="center">
							<?php } else { ?>
							<td style="display: none;">
							<?php } ?>
						<input type="text" id="order_item_price_<?= $order_item['id']; ?>" value="<?php echo round($order_item['product_price']-$order_item['promotion_discount'],2); ?>" class="read_class" readOnly style="width:80px" >
						</td>
						<?php if ($_SESSION['display_cost'] || $_SESSION['login_as'] == 'admin'):
						$true_cost = $true_cost*$order_item['product_qty'];
						$true_cost_total += $true_cost;
						?>
						<td style="width: 40px;display: none;" id="product_total_true_cost_<?= $order_item['id']; ?>">$<?php echo number_format($true_cost,2);?> </td>
					<?php endif; ?>
					<td class="toggle_warehouse" align="center"><?php
					$picked_data = $db->func_query("SELECT * from inv_product_ledger WHERE sku='".$order_item['product_sku']."' and order_id='".$order_item['order_id']."' and description='Marked as Picked.'");
					foreach($picked_data as $picked_info)
					{
						echo '<a href="javascript:void(0)" data-tooltip="Date Picked: '.americanDate($picked_info['date_added']).'">'.$picked_info['quantity'].' by '.get_username($picked_info['user_id'])."</a><br>";
					}
					?>
					</td>
					<td class="toggle_warehouse" align="center"><?php
					$picked_data = $db->func_query("SELECT * from inv_product_ledger WHERE sku='".$order_item['product_sku']."' and order_id='".$order_item['order_id']."' and description='Marked as Packed.'");
					foreach($picked_data as $picked_info)
					{
						echo '<a href="javascript:void(0)" data-tooltip="Date Packed: '.americanDate($picked_info['date_added']).'">'.$picked_info['quantity'].' by '.get_username($picked_info['user_id'])."</a><br>";
					}
					?>
					</td>

					<td align="center">
						<?php if ((strtolower($order['order_status']) == 'estimate' || strtolower($order['order_status']) == 'processed' || strtolower($order['order_status']) == 'on hold' || strtolower($order['order_status']) == 'awaiting fulfillment' || strtolower($order['order_status']) == 'shipped' || $_SESSION['login_as'] == 'admin') && ($_SESSION['login_as'] == 'admin' || $_SESSION['add_product_order'])): ?>
							<?php if ($_SESSION['login_as'] == 'admin' || $_SESSION['add_product_order'] || $_SESSION['order_price_override'] || $_SESSION['edit_order_details']) { ?>
							<a id="edit_btn_<?= $order_item['id']; ?>" href="javascript:void(0)" onClick="editThis('<?= $order_item['id']; ?>')">Update Price</a> |  
							<?php } ?>
							<a id="save_btn_<?= $order_item['id']; ?>" style="display: none;" href="javascript:void(0);" onClick="saveThis('<?= $order_item['id']; ?>')">Save Price</a>
							<!-- <?php if ((($paypal_check || $payflow_check || !(strpos(strtolower($order['payment_method']), 'cash') === false) || $order['payment_source'] == 'PayPal') && ($order['store_type'] == 'web' || $order['store_type'] == 'bigcommerce')) || ($order['store_type'] == 'amazon' || $order['store_type'] == 'amazon_fba')) { ?> -->
							<!-- <?php $linkToPay = "order_payback.php?action=remove". (($order['store_type'] == 'amazon' || $order['store_type'] == 'amazon_fba')? '&storetype=amazon': '') ."&". ((!(strpos(strtolower($order['payment_method']), 'cash') === false))?'type=cash&':'') ."order_id=" . $orderID . "&items="?> -->
							<!-- <a id="remv_btn_<?= $order_item['id']; ?>" href="javascript:void(0)" onclick="addRemoveProducts('<?= $order_item['id']; ?>')" >Remove</a> -->
							<!-- <?php } else  { ?> -->
							<!-- <a id="remv_btn_<?= $order_item['id']; ?>" href="javascript:void(0);" onClick="addRemoveProduct('<?= $order_item['id']; ?>')">Remove</a> -->
							<!-- <?php } ?> -->
							<a href="javascript:void(0);" onclick="$.fancybox.open( $('#popupReason<?= $order_item['id']; ?>'), {afterClose: function(){}} );">Remove Qty</a>
							<?php
							if($_SESSION['add_product_order'])
							{
							?>
							 | 	<a href="javascript:void(0);" onclick="addQty('<?php echo $order_item['id'];?>')">Add Qty</a>
							 <?php
							}
							?>
							<div align="center" id="popupReason<?= $order_item['id']; ?>" style="display:none;">
							<?php $order_removal_reasons = $db->func_query("Select * from inv_voucher_reasons where reason_type = 'Order' order by reason asc"); ?>
								<select id="removeReason<?= $order_item['id']; ?>" name="removeReason">
									<option value=''>Select</option>
									<?php foreach($order_removal_reasons as $reason){ ?>
									<option value='<?php echo $reason['reason'] ?>' ><?php echo $reason['reason'] ?></option>
									<?php } ?>
								</select>
								<select id="removeQty<?php echo $order_item['id'];?>">
								<?php
								for($_i=1;$_i<=$order_item['product_qty'];$_i++)
								{
									?>
									<option><?php echo $_i;?></option>
									<?php
								}
								?>
								</select>

								 <br><br>
								<div> Are you sure you want to remove this item from<br>
									the order and refund the item cost to the customer?<br>
									(Refund completed when Update Order button is clicked)</div><br><br>
									<input type="button" value="Proceed" onclick="updateRemovedTable('<?= $order_item['id']; ?>','<?php echo str_replace(","," ",getItemName($order_item['product_sku']));?>','<?= $order_item['product_sku']; ?>','<?php echo get_username($_SESSION['user_id'])?>','<?php echo date ( 'Y-m-d H:i:s' );?>','<?= $order['order_id']; ?>','<?= $order_item['product_sku']; ?> * '+$('#removeQty<?php echo $order_item['id'];?>').val())"/>
									<input type="button" value="Cancel" onclick='parent.$.fancybox.close();'/>
								</div>
								
							<?php endif;?>
						</td>
					</tr>

				<?php $i++; endforeach; ?>
				<tr>
				<td colspan="10" align="right"><strong>Total Item(s): <?php echo $total_items;?></strong></td>
				<!-- <td  align="center"><strong></strong></td> -->
				<!-- <td colspan="7"></td> -->
				</tr>
				<div align="center" id="divAddQty" style="display:none;">
				<input type="number" value="1" id="a_product_qty" min="1">
				<div> Note: Adding a new quantity roles back the item state to picking mode.
				</div><br><br>
									<input type="button" value="Proceed" onclick="addItemQty();"/>
									<input type="button" value="Cancel" onclick='parent.$.fancybox.close();'/>
				</div>
				<input type="hidden" id="new_row_index" value="<?php echo $i; ?>">
				<script type="text/javascript">
				var a_item_id = '';
				var a_product_qty = '';
				function addQty(item_id)
				{
					$('#a_product_qty').val('1');
					a_item_id = item_id;
					// a_product_qty = $('#order_item_qty_'+item_id).val();

					$.fancybox.open( $('#divAddQty'), {afterClose: function(){}} );

				}
				function addItemQty()
				{
					a_product_qty = $('#a_product_qty').val();
					$.ajax({
		url: 'viewOrderDetail.php',
		type: 'post',
		data:{order_id:'<?php echo $order['order_id'];?>',item_id:a_item_id,product_quantity:a_product_qty,action:'addItemQty'},
		dataType: 'json',       
		beforeSend: function() {
		},
		complete: function() {
		},              
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			}
			if (json['success']) {
				$('#order_item_qty_'+a_item_id).val(json['product_quantity']);
				
				$('#order_item_price_'+a_item_id).val(json['product_price']);
				parent.$.fancybox.close();

			}
		}
	});
				}
					function changePaymentMethod(obj)
					{
						if ($(obj).val()=='Cash On Delivery') {
							$('#wire_div').hide();
							$('#cod_div').show();
						} else if ($(obj).val()=='Wire Transfer') {
							$('#cod_div').hide();
							$('#wire_div').show();
						} else {
							$('#cod_div').hide();
							$('#wire_div').hide();
						}
						var payment_method = '<?= $order['payment_method']; ?>';
						if($(obj).val()=='')
						{
							payment_method='<?= $order['payment_method']; ?>';
						}
						else
						{
							payment_method = $(obj).val();
						}
						$('input[name=payment_method]').val(payment_method);
						$('#span_payment_method').html(payment_method);
					}
					rmProducts = [];
					rmReason = [];
					function addRemoveProducts (product_id,reason) {
						rmProducts.push(product_id);
						rmReason.push(reason);
						$('#refundProducts').val('1');
						$('input[name=removeProducts]').val(rmProducts);
						// if($('#removeQty'+product_id).val()==$('#order_item_qty_'+product_id).val())
						// {
						// 	$('#pItem_' + product_id).remove();
							
						// }
						// else
						// {
							$('#order_item_qty_'+product_id).val(
								parseInt($('#order_item_qty_'+product_id).val()) - parseInt($('#removeQty'+product_id).val())
								);
							$('#order_item_price_'+product_id).val(
								parseInt($('#order_item_qty_'+product_id).val()) * parseFloat($('#order_item_unit_'+product_id).val())
								);
						// }
					}
					function addRemoveProduct (product_id,reason) {
						rmProducts.push(product_id);
						rmReason.push(reason);
						$('input[name=removeProducts]').val(rmProducts);
						// if($('#removeQty'+product_id).val()==$('#order_item_qty_'+product_id).val())
						// {
						// 	$('#pItem_' + product_id).remove();
							
						// }
						// else
						// {
							$('#order_item_qty_'+product_id).val(
								parseInt($('#order_item_qty_'+product_id).val()) - parseInt($('#removeQty'+product_id).val())
								);
							$('#order_item_price_'+product_id).val(
								parseInt($('#order_item_qty_'+product_id).val()) * parseFloat($('#order_item_unit_'+product_id).val())
								);
						// }
						// $('#pItem_' + product_id).remove();
					}
					
					function updateRemovedTable (product_id,product_name,product_sku,user_name,date,order_id,sku_qty) {
						var rmTable = [];
						
						if($('#removeReason'+product_id).val()=='')
						{
							alert('Please select the removal reason before proceeding');
							return false;
						}
						addRowRemoved ();
						rmTable.push(order_id);
						$('input[name=removeTable]').val(rmTable);
						rmTable.push(sku_qty);
						$('input[name=removeTable]').val(rmTable);
						rmTable.push(product_name);
						$('input[name=removeTable]').val(rmTable);
						rmTable.push($('.fancybox-skin #removeReason'+product_id).val());
						$('input[name=removeTable]').val(rmTable);
						rmTable.push(user_name);
						$('input[name=removeTable]').val(rmTable);
						rmTable.push(parseFloat($('#order_item_unit_'+product_id).val())*parseFloat($('#removeQty'+product_id).val()));
						$('input[name=removeTable]').val(rmTable);
						$('#deletedSku'+rem_ind).val(sku_qty);
						$('#deletedItemName'+rem_ind).html(product_name);
						$('#deletedTime'+rem_ind).val(date);
						$('#deletedReason'+rem_ind).val($('.fancybox-skin #removeReason'+product_id).val());
						$('#deletedBy'+rem_ind).val(user_name);
						$('#deletedPrice'+rem_ind).val('$'+parseFloat($('#order_item_unit_'+product_id).val())*parseFloat($('#removeQty'+product_id).val()));
						// $('#deletedPrice'+rem_ind).val('$'+$('#order_item_price_'+product_id).val());
						var tt_rem_val = parseFloat($('#tot_rem_price_col').html());
						var new_total_removed_price = tt_rem_val + (parseFloat($('#order_item_unit_'+product_id).val())*parseFloat($('#removeQty'+product_id).val()));
						$('#tot_rem_price_col').html(new_total_removed_price.toFixed(2));
						rem_ind++;
						<?php if ((($paypal_check || $payflow_check || !(strpos(strtolower($order['payment_method']), 'cash') === false) || $order['payment_source'] == 'PayPal') && $order_paid_type == 'paid' && ($order['store_type'] == 'web' || $order['store_type'] == 'bigcommerce')) || ($order['store_type'] == 'amazon' || $order['store_type'] == 'amazon_fba')) { ?>
							<?php $linkToPay = "order_payback.php?action=remove&itemrem=1". (($order['store_type'] == 'amazon' || $order['store_type'] == 'amazon_fba')? '&storetype=amazon': '') ."&". ((!(strpos(strtolower($order['payment_method']), 'cash') === false))?'type=cash&':'') ."order_id=" . $orderID . "&items="?>
							addRemoveProducts(product_id,$('.fancybox-skin #removeReason'+product_id).val());
							<?php } else { ?>
								addRemoveProduct(product_id,$('.fancybox-skin #removeReason'+product_id).val());	
								<?php } ?>

								updateRemoveProduct();
								parent.$.fancybox.close();
							} 
							function rmRfProducts () {
								if ($('[value=addp]').attr('name') == 'action') {
									alert('Use Add to ad product');
									return false;
								}
								if ($('#refundProducts').val() == 1) {
									$('#remove_btn_selected').attr('href', '<?= $linkToPay; ?>' + rmProducts+ '&reasons='+rmReason);
									$('#remove_btn_selected').click();
									return false;
								} else {
									return true;
								}
								return false;
							}
						</script>
						<input type="hidden" id="refundProducts" value="">
						<input type="hidden" name="removeProducts" value="">
						<input type="hidden" name="removeTable" value="">
						<?php if ((($paypal_check || $payflow_check || !(strpos(strtolower($order['payment_method']), 'cash') === false) || $order['payment_source'] == 'PayPal') && ($order['store_type'] == 'web' || $order['store_type'] == 'bigcommerce')) || ($order['store_type'] == 'amazon' || $order['store_type'] == 'amazon_fba')) { ?>
						<a id="remove_btn_selected" class="fancybox4 fancybox.iframe" style="display: none;" href="" >Remove</a>
						<?php } ?>
					</tbody>
					<tr style="font-weight:bold">
						<td align="right" colspan="<?=($_SESSION['login_as'] == 'admin'?12:10);?>">
							<?php $fee_table = '';
							if($order_fees){
								$fee_table.='<table align="center" border="1" style="border:1px solid #ddd;border-collapse:collapse;" cellspacing="0" cellpadding="5" width="70%">';
								$fee_total = 0; 
								foreach($order_fees as $order_fee){
									$fee_table.='<tr>
									<td align="right">
										<b>'.$order_fee['fee_type'].':</b>
									</td>
									<td align="right">
										$'.$order_fee['fee'].'
									</td>
								</tr>';  
								$fee_total += $order_fee['fee']; 
							}
							$fee_table.='<tr>
							<td align="right" style="color:green"><b>Total:</b></td>
							<td align="right" style="color:green"><b>$'.$fee_total.'</b></td>
						</tr>     
					</table>';
				}
				?>
				<?php if($_SESSION['display_order_price'] || $_SESSION['login_as'] == 'admin'){ ?>
				<table cellpadding="5" width="20%" cellspacing="0" style="font-weight:bold" border="0">
				<?php } else {?>
				<table cellpadding="5" width="20%" cellspacing="0" style="font-weight:bold;display: none;" border="0">
				<?php }?>
					<tr>
						<td align="right">Sub Total:</td>
						<td>$<?= number_format($sub_total, 2); ?></td>
					</tr>
					<tr>
						<td align="right">Shipping:</td>
						<td>$<?= number_format($order['shipping_cost'], 2) ?></td>
					</tr>
					<?php
					$shipping_cost= 0.00;
					if(isset($order_shipments[0]['voided']) and $order_shipments[0]['voided']==0):
						?>
					<tr>
						<td align="right" style="color:blue">Shipping Charge:</td>
						<td style="color:blue">-$<?= number_format($order_shipments[0]['shipping_cost']+$order_shipments[0]['insurance_cost'], 2) ?></td>
					</tr>
					<?php
					$shipping_cost = $order_shipments[0]['shipping_cost']+$order_shipments[0]['insurance_cost'];
					endif;
					?>
					<tr>
						<td align="right">Tax / Extra:</td>
						<td>$<?= number_format($_tax, 2) ?></td>
					</tr>
					<tr>
						<td align="right">Service Fee:</td>
						<td>$<?= number_format($business_fee, 2) ?></td>
					</tr>
					<?php
					if($order['store_type']=='web')
					{
						$_voucher_query = 'cast(a.order_id as char(50)) = "'. $orderID .'"';
					}
					else
					{
						$_voucher_query = 'cast(a.inv_order_id as char(50)) = "'. $orderID .'"';
					}
					?>
					<?php $vouchers = $db->func_query('SELECT *, `a`.`amount` as `used`, `b`.`amount` as `remain` FROM `oc_voucher_history` as a, `oc_voucher` as b WHERE a.`voucher_id` = b.`voucher_id` AND '.$_voucher_query.' ');
					$total_vouchers_issued = $db->func_query('SELECT amount ,voucher_id,code FROM `oc_voucher` WHERE  code LIKE "%'. $order['order_id'] .'%"');
					 ?>
					<?php 
					$total_vouchers_used = 0.00;
					$voucher_html='';
					foreach ($vouchers as $key => $voucher) { 
						$total_vouchers_used = $total_vouchers_used+ $voucher['used'];

						?>
						<?php $totalUsed = $db->func_query_first_cell('SELECT SUM(`amount`) from oc_voucher_history where voucher_id = "'. $voucher['voucher_id'] .'"'); ?>
						<?php $remain = ($voucher['remain'] - str_replace('-', '', $totalUsed))? $voucher['remain'] - str_replace('-', '', $totalUsed): ''; ?>
					<?php
						
					$voucher_html.='<tr class="tr_total_paid" style="display:none;font-weight:normal">
						
						<td align="right">Voucher(<a target="_blank" href="vouchers_create.php?edit='. $voucher['voucher_id'] .'">'. $voucher['code'] .'</a>, ' . number_format($remain, 2) . '):'.($_SESSION['vouchers_update']==1?' <a href="'.$host_path.'viewOrderDetail.php?order='.$_GET['order'].'&action=removeVoucher&voucher_history_id='.$voucher['voucher_history_id'].'"><img src="images/cross.png" title="Remove Voucher" /></a>':'').'</td>
						<td>$'.number_format($voucher['used'], 2).'</td>
					</tr>';
					 } 
					 $total_vouchers_used = $total_vouchers_used * (-1);
					 ?>
					<?php

					$voucher_issued_amount = 0.00;
					foreach ($total_vouchers_issued as $key => $voucher) { 
						if($voucher['voucher_id']){
							$voucher_issued_amount+=$voucher['amount'];
							?>
					<tr>
						<td align="right">Voucher(<?= '<a target="_blank" href="vouchers_create.php?edit='. $voucher['voucher_id'] .'">'. $voucher['code'] .'</a>, ' . number_format($voucher['amount'], 2) . ''; ?>):</td>
						<td>$+<?= number_format($voucher['amount'], 2); ?></td>
					</tr>
					<?php  }
					} ?>
					<?php $coupons = $db->func_query('SELECT *, `a`.`amount` as `used` FROM `oc_coupon_history` as a, `oc_coupon` as b WHERE a.`coupon_id` = b.`coupon_id` AND cast(a.`order_id` as char(50)) = "'. $orderID .'"'); ?>
					<?php $total_coupons = 0.00; ?>
					<?php foreach ($coupons as $key => $coupon) { ?>
					<tr>
						<td align="right">Coupon(<?= $coupon['code']; ?>):</td>
						<td>$<?= number_format($coupon['used'], 2); ?></td>
						<?php $total_coupons += str_replace('-', '', $coupon['used']); ?>
					</tr>
					<?php } ?>
					<tr>
						<td align="right">Order Total:</td>
						<td>$<?= number_format(($order_total - $total_coupons), 2) ?></td>
					</tr>
					<?php if ($_SESSION['login_as'] == 'admin'): ?>
						<tr>
							<td align="right" style="color:blue">Order True Cost:</td>
							<td style="color:blue">$<?= number_format($true_cost_total, 2) ?></td>
						</tr>
						<?php
						if($order['transaction_fee']>0)
						{
							?>
							<tr>
								<td align="right" style="color:blue">Transaction Fee:</td>
								<td style="color:blue">-$<?= number_format($order['transaction_fee'], 2) ?></td>
							</tr>
							<?php
						}
						?>
						<?php
						if($fee_total*(-1)>0)
						{
							?>
							<tr>
								<td align="right" style="color:blue">Fee:</td>
								<td style="color:blue">-$<?= number_format($fee_total*(-1), 2) ?></td>
							</tr>
							<?php
						}
						?>
						<tr>
							<td align="right" style="color:green">Profit:</td>
							<!-- <td style="color:green">$<?= number_format($order_total + ($total_vouchers *-1) - $_tax - $true_cost_total - $order['transaction_fee'] - ($shipping_cost) + ($fee_total), 2) ?></td> -->
							<td style="color:green">$<?= number_format(($order['sub_total']+$order['shipping_amount'] +$business_fee) - $true_cost_total - $order['transaction_fee'] - ($shipping_cost) + ($fee_total), 2) ?></td>
						</tr>
					<?php endif; ?>
					<tr>
						<td colspan="2" style="border-bottom:1px dashed #000"> </td>
					</tr>
					<tr>
							<td align="right">
								Total Balance:
							</td>
							<td>$<?= number_format($order['sub_total']+$order['tax']+$order['shipping_amount']+$business_fee, 2); ?></td>
						</tr>

					<?php
					if(strpos(strtolower($order['payment_method']), 'cash') !== false && strtolower($order['order_status'])=='shipped' && $order['payment_method']!= 'Cash On Delivery' )
					{
						$order['paid_price']=$order_total;
					}
					if(strtolower($order['store_type'])!='po_business' and strtolower($order['order_status'])=='shipped')
					{
			// $order['paid_price'] = $order_total;
					}
					?>
					<?php
					$refunded_amount = 0.00; 
					$paid_amount = 0.00;
					if($transaction_dets)
					{
						foreach($transaction_dets as $transaction_det)
						{
							if($transaction_det['pp_transaction_id'])
							{
								$transaction_det['transaction_id'] = $transaction_det['pp_transaction_id'];
							}
							
							$paid_html.='<tr class="tr_total_paid" style="display:none;font-weight:normal">
								
								<td align="right">';
								 if ($transaction_det['amount']<0) { 
										$refunded_amount = $refunded_amount + $transaction_det['amount'];
											
									
									$paid_html.='Refund <font style="font-weight:normal;font-size:9px;">('.$transaction_det['transaction_id'].')</font>:';
								 } else { 
									$paid_html.='<font style="font-weight:normal;font-size:9px;">('.$transaction_det['transaction_id'].'):';
								 } 

								 	$paid_amount = $paid_amount + (($transaction_det['amount'])? $transaction_det['amount']: $order['paid_price']);


								$paid_html.='</td>
								<td>$'.(($transaction_det['amount'])? number_format($transaction_det['amount'],2): number_format($order['paid_price'],2)).'</td>
							</tr>';
							 }
							 ?>
							 	<tr style="cursor: pointer;background-color: #dfd"  onclick="$('.tr_total_paid').toggle()">
								
								<td align="right">Total Paid:</td>
								<td>$<?php echo number_format(($paid_amount+$total_vouchers_used)-$refunded_amount,2);?></td>
							 </tr>
							 <?php echo $paid_html.$voucher_html;?>

							 <?php $order['paid_price'] = ($paid_amount-$refunded_amount);?>
							 


							 <?php

						} else if ($is_local) { ?>
						<tr style="cursor: pointer;background-color: #dfd"  onclick="$('.tr_total_paid').toggle()">
							<td align="right">
								Total Paid:
							</td>
							<td>$<?= number_format($order['paid_price']+$total_vouchers_used, 2); ?></td>
						</tr>
						<?php if ($cash_paid != 0.0000){?>
						<tr class="tr_total_paid" style="display:none;font-weight: normal;">
							<td style="color: green;" align="right" colspan="2">
								(Cash Tendered: $<?= number_format($cash_paid, 2); ?>)
							</td>
						</tr>
						<?php }?>
						<?php if ($card_paid != 0.0000){?>
						<tr class="tr_total_paid" style="display:none;font-weight: normal;">
							<td style="color: green;" align="right" colspan="2">
								(Card Tendered: $<?= number_format($card_paid, 2); ?>)
							</td>
						</tr>
						<?php }?>
						<?php if ($change_due != 0.0000){?>
						<tr class="tr_total_paid" style="display:none;font-weight: normal;">
							<td style="color: red;" align="right" colspan="2">
								(Change Due: -$<?= number_format($change_due, 2); ?>)
							</td>
						</tr>

						<?php }?>
						<?php
						echo $voucher_html;
						?>
						<?php } else {?>
						<tr style="cursor: pointer;background-color: #dfd"  onclick="$('.tr_total_paid').toggle()">
							<td align="right" >
								Total Paid:
							</td>
							<td>$<?= number_format($order['paid_price']+$total_vouchers_used, 2); ?></td>
						</tr>
						<?php echo $voucher_html;?>
						<?php }?>
						<tr>
							<td align="right">Total Due:</td>
							<?php 
							$free_checkout = $db->func_query_first_cell('SELECT payment_method from oc_order where order_id = "'. $orderID .'"');
							if ($refunded_amount < 0) {
								      	$refunded_amount = $refunded_amount * -1;
								      }
							// $amountDue = ($order_total+$total_vouchers_used) - $order['paid_price'] - ($refunded_amount);

								      $amountDue = ($order['sub_total']+$order['tax']+$order['shipping_amount']+$business_fee) - ($order['paid_price']+$total_vouchers_used-$voucher_issued_amount);
							     if (round($order_total - $refunded_amount,2) == 0.00 || $free_checkout == 'Free Checkout') {
								      	$amountDue = 0;	
								      }
							?>
							<td>$<?= ($order['order_status'] == 'Estimate') ? '0.00': number_format(round($amountDue, 2), 2); ?><input type="hidden" value="<?php echo $amountDue; ?>" name="amount_due" />
						</td>
						</tr>
				</table>
			</td>
		</tr>
	</table>
	<br />
	<?php echo $fee_table;?>
	<div align="center">
		<input type="hidden" name="payment_method" value="<?= $order['payment_method']; ?>">
		<input type="hidden" name="paid_price" value="">
		<input type="hidden" name="payment_detail_1" value="<?= $order['payment_detail_1']; ?>">
		<input type="hidden" name="payment_detail_2" value="<?= $order['payment_detail_2']; ?>">
		<input type="submit" class="button button-info" name="update" onclick="return rmRfProducts();" value="Update Order" />
		<?php
		if($order['store_type']=='amazon' and strtolower($order['order_status']) == 'unshipped'):
			?>
		<!-- <input type="button" value="Cancel Order" onClick="if(confirm('Are you sure want to cancel this order?')){window.location='viewOrderDetail.php?order=<?php echo $_GET['order'];?>&action=cancelOrder';}"> -->
		<?php
		endif;
		?>
	</div>  
	<br />
	<br />
	<h2 align="center" style="font-size:14px;"> Removed Items </h2>
	<table align="center"  class="xtable"  width="90%">
		<thead>
			<tr>
				<th align="center">Sku * Qty</th>
				<th align="center">Item Name</th>
				<th align="center">Time & Date</th>
				<th align="center">Reason</th>
				<th align="center">Removed By</th>
				<th align="center">Price</th>
			</tr>
		</thead>
		<?php $removedItemsDB = $db->func_query('SELECT * from inv_removed_order_items where order_id = "'.$order['order_id'].'" ');
		$iter = 0; ?>
		<tbody id="itemholderremoved">
			<?php if($removedItemsDB) { 
				$total_removed_price = 0;?>
			
			<?php foreach($removedItemsDB as $removedItemDb) { ?>
			<tr>
				<td><input type="text" readOnly id="delSku<?php echo $iter;?>" value="<?php echo $removedItemDb['item_sku'];?>" class="read_class" style="background-color:#ffffff;border:none;" ></td>
				<td><div id="delItemName<?php echo $iter;?>"><?php echo $removedItemDb['item_name'];?></div></td>
				<td><input style="background-color:#ffffff;border:none" id="delTime<?php echo $iter;?>" readOnly type="text" value="<?php echo $removedItemDb['date_removed'];?>" class="read_class"></td>
				<td><input style="background-color:#ffffff;border:none" id="delReason<?php echo $iter;?>" readOnly type="text" value="<?php echo $removedItemDb['reason'];?>" class="read_class" ></td>
				<td><input style="background-color:#ffffff;border:none" id="delBy<?php echo $iter;?>" readOnly type="text" value="<?php echo $removedItemDb['removed_by'];?>" class="read_class"></td>
				<td><input style="background-color:#ffffff;border:none" id="delItemPrice<?php echo $iter;?>" readOnly type="text" value="$<?php echo $removedItemDb['item_price'];?>" class="read_class"></td>
				
			</tr>
			<?php $iter = $iter+1;
			$total_removed_price = $total_removed_price + $removedItemDb['item_price'];
		} ?>
		
		<?php } ?>
<tr id="tot_rem_price_row">
			<td colspan="5" align="right">
				<strong>Total:</strong>
			</td>
			<td id="tot_rem_price_col">
				<?php echo round($total_removed_price,2); ?>
			</td>
		</tr>
		
	</tbody>
</table>


<?php if($order_shipments):?>
	
	<h2 align="center" style="font-size:14px;"> Shipment Tracking </h2>
	<table align="center"  class="xtable"  width="70%">
		<tr>
			<th>Shipping Cost</th>
			<th>Insurance Cost</th>
			<th>Shipping Date</th>
			<th>Tracking Number</th>
			<th>Service Code</th>
			<th>Carrier Code</th>
			<th>Weight</th>
			<th>Voided</th>
		</tr>  
		<?php foreach($order_shipments as $order_shipment):
		if ($order_shipment['carrier_code'] == 'fedex') {
		  	$url = '<a href="https://www.fedex.com/apps/fedextrack/?action=track&language=english&tracknumbers='.$order_shipment['tracking_number'].'" target = "_blank">'.$order_shipment['tracking_number'].'</a>';
		  }
		  if ($order_shipment['carrier_code'] == 'ups') {
		  	$url = '<a href="http://wwwapps.ups.com/WebTracking/processRequest?HTMLVersion=5.0&Requester=NES&AgreeToTermsAndConditions=yes&loc=en_US&tracknum='.$order_shipment['tracking_number'].'" target = "_blank">'.$order_shipment['tracking_number'].'</a>';
		  }
		  if ($order_shipment['carrier_code'] == 'endicia' || $order_shipment['carrier_code'] == 'express_1') {
		  	$url = '<a href="https://tools.usps.com/go/TrackConfirmAction?tLabels='.$order_shipment['tracking_number'].'" target = "_blank">'.$order_shipment['tracking_number'].'</a>';
		  }
		  if (!$url) 
		  {
		  	$url =  $order_shipment['tracking_number'];
		  }


		  ?>
			<tr>
				<td>$<?php echo $order_shipment['shipping_cost'];?></td>
				<td>$<?php echo $order_shipment['insurance_cost'];?></td>
				<td><?php echo americanDate($order_shipment['ship_date']);?></td>
				<td><?php echo $url;?></td>
				<td><?php echo stripDashes($order_shipment['service_code']);?></td>
				<td><?php echo stripDashes($order_shipment['carrier_code']);?></td>
				<td><?php echo $order_shipment['weight']. " ". $order_shipment['units'];?></td>
				<td><?php echo $order_shipment['voided'];?></td>
			</tr> 
		<?php endforeach;?>     
	</table>
<?php endif;?>

<?php
	if(!$order_shipments)
	{
		$order_shipments = $db->func_query("SELECT * FROM inv_label_data WHERE order_id='".$orderID."'");
	}
?>
<?php

foreach($order_shipments as $_oshipment)
{
	$tracker = $db->func_query_first("SELECT * FROM inv_tracker WHERE tracking_code='".$_oshipment['tracking_number']."'");
	if($tracker)
	{

		if (strtolower($tracker['carrier']) == 'fedex') {
		  	$track_url = '<a href="https://www.fedex.com/apps/fedextrack/?action=track&language=english&tracknumbers='.$_oshipment['tracking_number'].'" target = "_blank">'.$_oshipment['tracking_number'].'</a>';
		  }
		  if (strtolower($tracker['carrier']) == 'ups') {
		  	$track_url = '<a href="http://wwwapps.ups.com/WebTracking/processRequest?HTMLVersion=5.0&Requester=NES&AgreeToTermsAndConditions=yes&loc=en_US&tracknum='.$_oshipment['tracking_number'].'" target = "_blank">'.$_oshipment['tracking_number'].'</a>';
		  }
		  if (strtolower($tracker['carrier']) == 'usps') {
		  	$track_url = '<a href="https://tools.usps.com/go/TrackConfirmAction?tLabels='.$_oshipment['tracking_number'].'" target = "_blank">'.$_oshipment['tracking_number'].'</a>';
		  }
		  if (!$track_url) 
		  {
		  	$track_url =  $_oshipment['tracking_number'];
		  }


		?>
		<h1><?php echo $track_url;?>
			

		</h1>
		<?php
			$_combined = array();
		if($_oshipment['combined_orders'])
		{
			$combined_orders = $_oshipment['combined_orders'];
			// echo '<strong>Combined With: ';
			foreach(explode(",", $combined_orders) as $combined_order)
			{
				if($combined_order!=$orderID)
				{
				$_combined[] =  linkToOrder($combined_order);
					
				}
			}
			echo '<h2>Combined with: '. implode(",", $_combined)."</h2>";

			// echo '</strong>'; 
		}
		?>

		<table align="center"  class="xtable"  width="70%">
			<!-- <tr>
				<th colspan="2">Tracking ID: <?=$tracker['tracker_id'];?></th>
				<th colspan="2" align="right">Code: <?=$tracker['tracking_code'];?></th>
			</tr> -->
			<tr>
				<th>Date Time</th>
				<th>Tracking Update</th>
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
		<br>
		<?php
	}
	?>
	<?php
}
?>
</form>
<?php else : ?>
	<h4> No Order Found</h4>
<?php endif; ?>
</div>
  
<div align="center">
	<table width="70%">
		<tr>
			<td width="50%" valign="top">
				<form method="post" action="">
					<table border="1" cellspacing="0" style="border:1px solid #ddd;border-collapse:collapse;" cellpadding="10" width="90%">
						<tr>
							<td>
								<textarea rows="5" cols="50" name="comment" required></textarea>
							</td>
						</tr>
						<tr>
							<td align="center">
								<input type="submit" class="button" name="addcomment" value="Add Comment" />                 
							</td>
						</tr>   
					</table>
					<input type="hidden" name="order_id" value="<?php echo $orderID ?>" />
				</form>
				<h2>Order Status Updates</h2>
				<table  class="xtable"  width="90%">
					<tr>
						<th>Date</th>
						<th>User</th>
						<th>PPUSA</th>
						<th>Comment</th>
					</tr>
					<?php
					$cxs = $db->func_query_first_cell('SELECT comment FROM oc_order WHERE order_id = "' . $_GET['order'].'"');
					$comments[] = array('date_added' => '', 'user_id' => NULL, 'comment' => $cxs );
					?>
					<?php foreach ($comments as $comment): ?>
						<?php if ($comment['comment']) {
							if (stristr($comment['comment'],'Order Status')) {
							
						 ?>
						<tr>
							<td><?php echo americanDate($comment['date_added']); ?></td>
							<?php if ($comment['user_id'] && !$comment['name']) {
								$comment['name'] = get_username($comment['user_id']);	
							} ?>
							<td><?php echo ($comment['user_id']) ? $comment['name'] : 'admin'; ?></td>
							<td><?php echo ($comment['user_id'] === NULL) ? 'YES' : 'N/A'; ?></td>
							<td><?php echo $comment['comment']; ?></td>
						</tr>
						<?php } }?>
					<?php endforeach; ?>
				</table>
				<h2>Comment History</h2>
				<table  class="xtable" width="90%">
					<tr>
						<th>Date</th>
						<th>User</th>
						<th>PPUSA</th>
						<th>Comment</th>
					</tr>
					<?php
					$cxs = $db->func_query_first_cell('SELECT comment FROM oc_order WHERE order_id = "' . $_GET['order'].'"');
					$comments[] = array('date_added' => '', 'user_id' => NULL, 'comment' => $cxs );
					?>
					<?php foreach ($comments as $comment): ?>
						<?php if ($comment['comment']) {
							if (!stristr($comment['comment'],'Package shipped on') && !stristr($comment['comment'],'Order Status')) {
							
						 ?>
						<tr>
							<td><?php echo americanDate($comment['date_added']); ?></td>
							<?php //if ($comment['user_id'] && !$comment['name']) {
								$comment['name'] = get_username($comment['user_id']);	
							//} 
								?>
							<td><?php echo ($comment['user_id']) ? $comment['name'] : 'admin'; ?></td>
							<td><?php echo ($comment['user_id'] === NULL) ? 'YES' : 'N/A'; ?></td>
							<td>
								<?php
                                        //parse usps , ups or fedex tracking number and make them as link
								preg_match("/Tracking No\\s*([a-zA-Z0-9]+)\\s*/is", $comment['comment'], $matches);
								if ($matches) {
									if (stristr($comment['comment'], "USPS")) {
										$comment['comment'] = preg_replace("/Tracking No\\s*([a-zA-Z0-9]+)\\s*/is", sprintf($usps_link, $matches[1], $matches[1]), $comment['comment']);
									} elseif (stristr($comment['comment'], "UPS")) {
										$comment['comment'] = preg_replace("/Tracking No\\s*([a-zA-Z0-9]+)\\s*/is", sprintf($ups_link, $matches[1], $matches[1]), $comment['comment']);
									} else {
										$comment['comment'] = preg_replace("/Tracking No\\s*([a-zA-Z0-9]+)\\s*/is", sprintf($fedex_link, $matches[1], $matches[1]), $comment['comment']);
									}
								}
								?>
								<?php echo $comment['comment']; ?>
							</td>
						</tr>
						<?php } }?>
					<?php endforeach; ?>
				</table>       
			</td>
			<td width="50%" valign="top">
				<form method="post" action="" enctype="multipart/form-data">
					<table border="1" cellspacing="0" style="border:1px solid #ddd;border-collapse:collapse;" cellpadding="10" width="100%">
						<tr>
							<td>
								<input type="file" name="order_docs" required />
							</td>
						</tr>
						<tr>
							<td>
								<textarea rows="2" cols="50" name="description" style="resize:none"></textarea>
							</td>
						</tr>
						<tr>
							<td align="center">
								<input type="submit" class="button" name="upload" value="Upload" />              
							</td>
						</tr>   
					</table>
					<input type="hidden" name="order_id" value="<?php echo $orderID ?>" />
				</form>
				<h2>Attachments</h2>
				<table  class="xtable" width="100%">
					<tr>
						<th>Date</th>
						<th>File</th>
						<th>Description</th>
						<th>Action</th>
					</tr>
					<?php foreach ($attachments as $attachment): ?>
						<tr>
							<td><?php echo $attachment['date_added']; ?></td>
							<td><?php echo $attachment['type']; ?></td>
							<td><?php echo $attachment['description']; ?></td>
							<td>
								<a href="<?php echo $host_path . "" . $attachment['attachment_path']; ?>">download</a>
								|
								<a href="viewOrderDetail.php?action=delete&fileid=<?php echo $attachment['id'] ?>&order=<?php echo $orderID; ?>" onclick="if (!confirm('Are you sure, You want to delete this file?')) { return false; }">delete</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>        
			</td>           
		</tr>
	</table>
</div>
<br /> <br />
<!-- Adding Email Sending Function -->
<?php
$emailInfo['total_formatted'] = '$' . number_format($order_total, 2);
?>
<div align="center">
	<h3>Send Email</h3>
	<table width="70%" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
		<form method="post" action="" id="email_form">
			<tr>
				<td>Canned Message:</td>
				<td>
					<?php $canned_messages = $db->func_query('SELECT * FROM `inv_canned_message` WHERE `catagory` = "1"'); ?>
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
				<td><input type="text" name="title" id="canned_title" value=""/></td>
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
				<td><input class="button" name="sendemail" value="Send Email" type="submit"></td>
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
		var canned_messages = [<?php echo implode(',', $messages)?>];
		var msgs = {};
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
<?php if ($order_replacements) { ?>
<br /><br />
<div align="center"> 
	<h2>Replacements</h2>
	<br>
	<table border="1" style="border-collapse:collapse;" width="70%" cellpadding="10">
		<tr style="background:#e5e5e5;">
			<th>SN</th>
			<th>Order ID</th>
			<th>Order Date</th>
			<th>Order Price</th>
			<?php if ($_SESSION['login_as'] == 'admin'){ ?>
			<th>Profit</th>
			<?php } ?>
			<th>Store Type</th>
			<th>Order Status</th>
			<th>Payment</th>
		</tr>
		<?php foreach($order_replacements as $i => $order): ?>
			<?php $order['transaction_fee'] = $db->func_query_first_cell("SELECT transaction_fee FROM inv_transactions WHERE order_id='".$order['order_id']."' "); ?>
			<?php
			$order_fee = $db->func_query_first_cell("SELECT SUM(fee) as fee from inv_order_fees where order_id = '".$order['order_id']."' ");
			$order_true_cost = 0.00;
			$_order_items = $db->func_query("SELECT product_sku,product_qty,product_true_cost,promotion_discount,product_price FROM inv_orders_items WHERE order_id='".$order['order_id']."'");
			$sub_total = 0.00;
			$order_discount = 0.00;
			foreach($_order_items as $_item) {
				$order_true_cost+=($_item['product_true_cost'] * $_item['product_qty']);
				if($order['payment_method']=='Replacement') {
					//$promotion_discount = $_item['product_price'];
					//$order_discount += $_item['product_price'];
				}
				$sub_total+=($_item['product_price']-$promotion_discount);
			}
			$temp_shipping_cost = $db->func_query_first_cell("SELECT shipping_cost FROM inv_orders_details WHERE order_id='".$order['order_id']."'");
			$sub_total = $sub_total + $temp_shipping_cost;
			if($order['payment_method']=='Replacement') {
				//$order['order_price'] = 0.00;
			}
			$order_shipments = $db->func_query_first("select * from inv_shipstation_transactions where order_id = '".$order['order_id']."' ORDER BY voided DESC");
			$_shipping_cost = 0.00;	
			if(isset($order_shipments['voided']) and $order_shipments['voided']==0) {
				$_shipping_cost = $order_shipments['shipping_cost']+$order_shipments['insurance_cost'];
			}
			$order_type = $_REQUEST['ordertype'];
			$po_order_total = 0.00;
			if($order['store_type']=='po_business') {
				$po_order_total = $db->func_query_first_cell("SELECT SUM(product_price) from inv_orders_items WHERE order_id='".$order['order_id']."'");	
				$shipping_cost = $db->func_query_first_cell("SELECT shipping_cost FROM inv_orders_details WHERE order_id='".$order_id."'");
				$po_order_total = $po_order_total+$shipping_cost;
			}
			?>
			<tr id="<?php echo $order['order_id'];?>">
				<td align="center"><?php echo $i; ?></td>
				<td align="center" class="order_id">
					<a href="viewOrderDetail.php?order=<?php echo $order['order_id']?>"><?php echo @$order['prefix'].$order['order_id'];?></a>
				</td>
				<td align="center"><?php echo americanDate($order['order_date']);?></td>
				<td align="center">$<?php echo ($order['store_type']=='po_business'?$po_order_total:$order['order_price']);?></td>
				<?php if ($_SESSION['login_as'] == 'admin'){ ?>
				<?php $_order_price = ($order['store_type']=='po_business'?$po_order_total:$order['order_price']); ?>
				<?php $order_profit =  ($order['store_type']=='po_business'?((float)$po_order_total-$order_true_cost-$order['transaction_fee']+$order_fee):((float)$order['order_price']-$order_true_cost-$order['transaction_fee']-$_shipping_cost+$order_fee));?>
				<td align="center" style="color:<?=($order_profit>=0?'green':'red');?>">$<?=number_format($order_profit,2);?></td>
				<?php } ?>
				<td align="center"><?php echo @mapStoreType($order['store_type']);?></td>
				<?php if($order_type == "Return") :?>
					<td align="center">Return/Refund</td>
				<?php else : ?>
					<td align="center"><?php echo @$order['order_status'];?></td>
				<?php endif ;?>
				<?php
							// If cash order and status shipped, it should show Paid instead of Unpaid
							//var_dump(strpos($order['payment_method'], 'Cash'));
							//echo $order['payment_method'];
				if(strpos(strtolower($order['payment_method']), 'cash') !== false && strtolower($order['order_status'])=='shipped' )
				{
					$order['payment_source']='Paid';
				}
				else
				{
					$order['payment_source'] = $order['payment_source'];
				}
				?>
				<td align="center"><?php echo @$order['payment_source'];?></td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
<?php } ?>
<?php if ($order_voucher) { ?>
<br /><br />
<div align="center"> 
	<h2>Store Credit</h2>
	<br>
	<table border="1" style="border-collapse:collapse;" width="70%" cellpadding="10">
		<tr style="background:#e5e5e5;">
			<th width="2%">#</th>
			<th>Code</th>			
			<th>Amount</th>
			<th>Available</th>
			<th>Source</th>
			<th>PPUSA</th>
			<th>Status</th>
			<th>Date Added</th>
			<th>Action</th>
		</tr>
		<!-- Showing All REcord -->
		<?php foreach ($order_voucher as $i => $voucher) { ?>
		<?php
		$voucher_detail = $db->func_query_first("SELECT * FROM inv_voucher_details WHERE voucher_id='".$voucher['voucher_id']."' ORDER BY id DESC");
		$user_name = get_username($voucher_detail['user_id']);
		if($voucher_detail['oc_user_id'])
		{
			$user_name = $db->func_query_first_cell("SELECT username FROM oc_user WHERE user_id='".$voucher_detail['oc_user_id']."'");
		}
		?>
		<?php $balance = ((float) $voucher['amount']) + ((float) $db->func_query_first_cell("SELECT SUM(amount) FROM oc_voucher_history WHERE voucher_id='".$voucher['voucher_id']."'")); ?>
		<tr>
			<td><?= ($i) + 1 ?></td>
			<td>
				<?= $voucher['code'];?>
			</td>
			<td>
				$<?= number_format($voucher['amount'], 2);?>
			</td>
			<td>
				$<?= number_format($balance, 2);?>
			</td>
			<td>
				<?= ($voucher_detail['is_lbb'])? 'BuyBack': '';?>
				<?= ($voucher_detail['is_rma'])? 'RMA': '';?>
				<?= ($voucher_detail['is_order_cancellation'])? 'Cancellation': '';?>
				<?= ($voucher_detail['is_pos'])? 'POS': '';?>
			</td>
			<td>
				<?= ($voucher_detail['is_pos'])? 'YES': 'N/A';?>
			</td>
			<td>
				<?= ($voucher['status'] == '1')? 'Enabled': 'Disabled';?>
			</td>
			<td>
				<?= americanDate($voucher['date_added']);?>
			</td>
			<td>
				<a href="<?= $host_path . 'vouchers_create.php?edit=' . $voucher['voucher_id'];?>">Edit</a>
			</td>
		</tr>
		<?php } ?>
	</table>
</div>
<?php } ?>
<?php if ($order_rma) { ?>
<br /><br />
<div align="center"> 
	<h2>RMA</h2>
	<br>
	<table border="1" style="border-collapse:collapse;" width="70%" cellpadding="10">
		<tr style="background:#e5e5e5;">
			<th style="width:50px;">#</th>
			<th>Received</th>
			<th>QC</th>
			<th>Completed</th>
			<th>RMA Number</th>
			<th>Source</th>
			<th>Status</th>                    
		</tr>
		<?php foreach ($order_rma as $k => $rma_return): ?>
			<?php 
                //echo '<pre>';                print_r($rma_return); exit;
			?>
			<tr>
				<td style="width:50px;"><?php echo $k + 1; ?></td>			
				<td><?php echo americanDate($rma_return['date_received']); ?></td>
				<td><?php echo ($rma_return['date_qc']) ? americanDate($rma_return['date_qc']): ''; ?></td>
				<td><?php echo ($rma_return['rma_status'] == 'Completed') ? americanDate($rma_return['date_completed']): ''; ?></td>
				<td>
					<a href="return_detail.php?rma_number=<?php echo $rma_return['rma_number']; ?>">
						<?php echo $rma_return['rma_number']; ?>
					</a>
				</td>
				<td><?php echo $rma_return['source']; ?></td>				
				<td><?php echo ($rma_return['rma_status'] == 'In QC') ? 'QC Completed' : $rma_return['rma_status']; ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
<?php } ?>
<?php if ($order_refund) { ?>
<br /><br />
<div align="center"> 
	<h2>Refund</h2>
	<br>
	<table border="1" style="border-collapse:collapse;" width="70%" cellpadding="10">
		<tr style="background:#e5e5e5;">
			<th style="width:50px;">#</th>
			<th>RMA Number</th>
			<th>Date Refunded</th>
			<th>Action</th>
			<th>Amount</th>
		</tr>
		<?php foreach ($order_refund as $k => $rma_return): ?>
			<?php 
                //echo '<pre>';                print_r($rma_return); exit;
			?>
			<tr>
				<td style="width:50px;"><?php echo $k + 1; ?></td>			
				<td>
					<a href="return_detail.php?rma_number=<?php echo $rma_return['rma_number']; ?>">
						<?php echo $rma_return['rma_number']; ?>
					</a>
				</td>
				<td><?php echo ($rma_return['date_added']) ? americanDate($rma_return['date_added']): ''; ?></td>
				<td><?php echo $rma_return['action']; ?></td>
				<td>$<?php echo number_format($rma_return['price'], 2); ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
<?php } ?>
<br /><br /> 
<div align="center"> 
	<table  class="xtable" width="70%" >
		<tr>
			<th>Country Match</th>
			<th>Distance</th>
			<th>IP City</th>
			<th>IP Region</th>
			<th>ISP</th>
			<th>IP Organization</th>
			<th>IP User Type</th>
			<th>IP Domain</th>
			<th>IP Corporate Proxy</th>
			<th>Anonymous Proxy</th>
		</tr>
		<tr>          
			<td><?php echo $order_fraud['country_match'] ?></td>
			<td><?php echo $order_fraud['distance'] ?></td>  
			<td><?php echo $order_fraud['ip_city'] ?></td> 
			<td><?php echo $order_fraud['ip_region'] ?></td>
			<td><?php echo $order_fraud['ip_isp'] ?></td>
			<td><?php echo $order_fraud['ip_org'] ?></td>
			<td><?php echo $order_fraud['ip_user_type'] ?></td>
			<td><?php echo $order_fraud['ip_domain'] ?></td>
			<td><?php echo $order_fraud['ip_corporate_proxy'] ?></td> 
			<td><?php echo $order_fraud['anonymous_proxy'] ?></td> 
		</tr>
	</table>
</div>
<div align="center"> 
	<br />
	<a href="order.php" style="margin-left:20px;"> Back </a> 
</div> 
<div style="display:none;text-align:center" id="edit_this">

</div>  
</body>
</html>
<script>
	var $datepicker = $('#shipping_date').pikaday({
		firstDay: 1,
		minDate: new Date(2000, 0, 1),
		maxDate: new Date(2020, 12, 31),
		yearRange: [2000,2020]
	});
    // chain a few methods for the first datepicker, jQuery style!
    $datepicker.toString();
</script>

<script type="text/javascript">


$(document).ready(function(e) {
		getShipping();
	});
function getProduct (t) {
	var p = $(t).parent().parent();
	// alert(p.text());
	var unit = $(p).find('input[name="product_unit"]');
	var priceH  = $(p).find('input[name="product_price"]');
	var sku = $(p).find('input[name="product_sku"]').val();
	// alert(sku);
	var qty = $(p).find('input[name="product_qty"]').val();
	var discount = $(p).find('input[name="product_discount"]').val();
	var total_discount = 0;
	if(sku=='' || qty==''){ return false;}
	if (discount == '') {
		discount = 0;
	}
	$.ajax({
		url: 'ajax_product_price.php',
		type: 'post',
		data:{sku:sku,customer_group_id:'<?= $customer_group_id; ?>',qty:qty,store_type:'<?= $order['store_type']; ?>'},
		dataType: 'json',       
		beforeSend: function() {
		},
		complete: function() {
		},              
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			}
			if (json['success']) {
				var unit_price = (json['success']);
				price = parseFloat(unit_price) * parseInt(qty);
				total_discount = price*parseFloat(discount) / 100;
				price = price - total_discount;
				unit.val(parseFloat(unit_price).toFixed(2));
				priceH.val(price.toFixed(2));
			}
		}
	});
}

	
	function copyShippingDetails(obj)
	{
		
		if($(obj).is(":checked"))
		{
			$('input[name=bill_firstname]').val($('input[name=first_name]').val());
			$('input[name=bill_lastname]').val($('input[name=last_name]').val());
			$('input[name=bill_address1]').val($('input[name=address1]').val());
			$('input[name=bill_address2]').val($('input[name=address2]').val());
			$('input[name=bill_city]').val($('input[name=city]').val());
			// $('input[name=bill_state]').val($('input[name=state]').val());
			$('input[name=bill_zip]').val($('input[name=zip]').val());
			

			// $('select[name=bill_country_id]').val($('select[name=country_id]').val());


			$('select[name=bill_country_id] option[value="'+$('select[name=country_id]').val()+'"]').prop('selected','selected');
			$('select[name=bill_zone_id] option[value="'+$('select[name=zone_id]').val()+'"]').prop('selected','selected');
		}
		else
		{
			$('input[name=bill_firstname]').val('<?=$order['bill_firstname'];?>');
			$('input[name=bill_lastname]').val("<?=$order['bill_lastname'];?>");
			$('input[name=bill_address1]').val("<?=$order['bill_address1'];?>");
			$('input[name=bill_address2]').val("<?=$order['bill_address2'];?>");
			$('input[name=bill_city]').val("<?=$order['bill_city'];?>");
			// $('input[name=bill_state]').val("<?=$order['bill_state'];?>");
			$('input[name=bill_zip]').val("<?=$order['bill_zip'];?>");
			// $('input[name=bill_country]').val("<?=$order['bill_country'];?>");

			$('select[name=bill_country_id] option[value="<?=$order['bill_country_id'];?>"]').prop('selected','selected');
			$('select[name=bill_zone_id] option[value="<?=$order['bill_zone_id'];?>"]').prop('selected','selected');
		}
	}
	function getShipping()
	{
		var zone = '<?php echo $order['state'];?>';
		if(zone=='')
		{
			return false; 
		}
		else
		{
			$.ajax({
				url: 'https://phonepartsusa.com/index.php?route=checkout/manual/shipping_method_for_imp',
				// url: 'https://phonepartsusa.com/index.php?route=api/repairdesk/get_shipping_method',
        //url: '<?php echo $local_path;?>../phoneparts/index.php?route=checkout/manual/shipping_method_for_imp',
        type: 'post',
        
        data:{zone:encodeURIComponent(zone),sub_total:'<?php echo $order['sub_total'];?>'},
        dataType: 'json',       
        beforeSend: function() {
        },
        complete: function() {
        },              
        success: function(json) {
        	if (json['error']) {
        		//alert(json['error']);
        		$('#select_shipping').html('<option value="">Select Shipping Method</option><option value="0.00-Custom Shipping" <?php if($order['shipping_method']=='Custom Shipping') { echo 'selected'; } ?>>Custom Shipping</option>');
        		shippingCost();
        		return false;    
        	}
        	if (json['shipping_method']) {
        		html='<optgroup label="Custom Shippings">'+
        		'<option value="0.00-Free Shipping" <?php if($order['shipping_method']=='Free Shipping') { echo 'selected'; } ?>>Free Shipping</option>'+
        		'<option value="0.00-Customer FedEx" <?php if($order['shipping_method']=='Customer FedEx') echo 'selected';?>>Customer FedEx</option>'+
        		'<option value="0.00-Customer UPS" <?php if($order['shipping_method']=='Customer UPS') echo 'selected';?>>Customer UPS</option>'+
        		'<option value="<?php echo $order['shipping_cost'] ?>-Other Shipping-" <?php if($order['shipping_method']=='Other Shipping') echo 'selected';?>>Other Shipping</option>';
        		for (i in json['shipping_method']) {
        			html += '<optgroup label="' + json['shipping_method'][i]['title'] + '">';
        			if (!json['shipping_method'][i]['error']) {
        				for (j in json['shipping_method'][i]['quote']) {
        					html += '<option value="' + json['shipping_method'][i]['quote'][j]['cost'] + '-'+json['shipping_method'][i]['quote'][j]['code']+'" '+(json['shipping_method'][i]['quote'][j]['title']=='<?php echo $order['shipping_method'];?>'?'selected':'')+'>' + json['shipping_method'][i]['quote'][j]['title']+'</option>';
        				}      


        			} else {
        				html += '<option value="" style="color: #F00;" disabled="disabled">' + json['shipping_method'][i]['error'] + '</option>';
        			}

        			html += '<option value="0-multiflatrate.combine_shipping" '+('Combined Shipping'=='<?php echo $order['shipping_method'];?>'?'selected':'')+'>Combined Shipping</option>';

        			html += '</optgroup>';
        		}
        		$('#select_shipping').html(html); 
        		shippingCost();
        	}
        }
    });     
}
}
var index = $('#new_row_index').val();
function addRow () {
	data = '<tr id="itemAdd_'+ index +'">'
	+'<td title=""></td>'
	+'<td class="sku"><input name="product_sku" id="xproduct_sku_'+index+'" data-index="'+index+'" onChange="getProduct(this);duplicateSkuCheck(this);" style="width:110px;" type="text"><input name="order_id" value="<?=$order['order_id'];?>" type="hidden"/><input name="action" value="addp" type="hidden"/></td>'
	+'<td class="name"></td>'
	+'<td style="background-color: rgb(242, 242, 242);" class="in_stock"></td>'
	+'<td class="qty"><input name="product_qty" id="xproduct_qty_'+index+'" data-index="'+index+'" onChange="getProduct(this);duplicateSkuCheck(this);" style="width:50px" type="text"></td>'
	+'<td class="unit">'
	+'<input name="product_unit" onChange="xUpdateProductPrice('+index+')" id="xproduct_unit_'+index+'" style="width:110px" type="text" >'
	+'</td>'
	+'<td class="tcost"></td>'
	+'<td class="discount"><input name="product_discount" id="xproduct_discount_'+index+'" value="0" style="width:50px" type="text"></td>'
	+'<td>$0.00</td>'
	+'<td class="ltotal"><input name="product_price" id="xproduct_price_'+index+'"  style="width:110px" type="text"></td>'
	+'<td class="ltcost"></td>'
	+'<td><a href="javascript:void(0);" onClick="addThis(this)">Add</a></td>'
	+'</tr>';
	$('.itemholder').append(data);
	$('input[name=update]').hide();
	index++;
}
var rem_ind=1;
function addRowRemoved () {
	tt_rem_val = parseFloat($('#tot_rem_price_col').html());
	$('#tot_rem_price_row').remove();
	data = '<tr>'
	+'<td><input type="text" readOnly id="deletedSku'+rem_ind+'" value="" class="read_class" style="background-color:#ffffff;border:none;" ></td>'
	+'<td><div id="deletedItemName'+rem_ind+'"></div></td>'
	+'<td><input style="background-color:#ffffff;border:none" id="deletedTime'+rem_ind+'" type="text" readOnly value="" class="read_class"></td>'
	+'<td><input style="background-color:#ffffff;border:none" id="deletedReason'+rem_ind+'" type="text" readOnly value="" class="read_class" ></td>'
	+'<td><input style="background-color:#ffffff;border:none" id="deletedBy'+rem_ind+'" type="text" readOnly value="" class="read_class"></td>'
	+'<td><input style="background-color:#ffffff;border:none" id="deletedPrice'+rem_ind+'" type="text" readOnly value="" class="read_class"></td>'
	+'</tr>'
	+'<tr id = "tot_rem_price_row">'
	+'<td colspan ="5" align="right">'
	+'<strong>Total:</strong>'
	+'</td>'
	+'<td id="tot_rem_price_col">'
	+tt_rem_val
	+'</td>'
	+'</tr>';
	$('#itemholderremoved').append(data);
}
function xUpdateProductPrice(xx)
{
	var product_qty = ($('#xproduct_qty_'+xx).val());
	var product_unit = ($('#xproduct_unit_'+xx).val());
	var product_price = 0.00;
	if(product_qty=='')
	{
		product_qty = 0;
	}
	if(product_unit=='')
	{
		product_unit = 0.00;
	}
	product_price = parseInt(product_qty) * parseFloat(product_unit);
	$('#xproduct_price_'+xx).val(product_price.toFixed(2));
}
function addThis (t) {
	$.ajax({
		url: 'viewOrderDetail.php',
		type: 'post',
		data:$('#'+ $(t).parent().parent().attr('id') +' :input'),
		dataType: 'json',       
		beforeSend: function() {
			$(t).hide();
		},
		complete: function() {
		},              
		success: function(json) {
			if (json['error']) {
				$(t).show();
				alert('Try some other time');
			}
			if (json['success']) {
				window.location.reload();
			}
		}
	});
}
function duplicateSkuCheck(obj) {
     		var index = 0;
     		var check = $(obj).attr('data-index');
     		while(index < 50){
     			if (index != check ) {
     				if($('#xproduct_sku_'+index).val() == $('#xproduct_sku_'+check).val()){
     					if ($('#xproduct_sku_'+check).val() != '') {
     							$('#xproduct_sku_'+check).val('');
     							$('#xproduct_qty_'+check).val('');
     							$('#xproduct_unit_'+check).val('');
     								alert('Error: SKU has been added into multiple Order line items. Consolidate same SKUs into 1 line item.');
     							
     							return false;
     					}
     				}
     			}
     			index++;
     		}
     	}
function removeProduct (id) {
	if (confirm('Are you sure?')) {
		$.ajax({
			url: 'viewOrderDetail.php?order=<?= $orderID; ?>',
			type: 'post',
			data:{product_id:id, action:'removeProduct'},
			dataType: 'json',       
			beforeSend: function() {
			},
			complete: function() {
			},              
			success: function(json) {
				if (json['error']) {
					alert('Try some other time');
				}
				if (json['success']) {
					window.location.reload();
				}
			}
		});
	} else {
		return false;
	}
}

function updateRemoveProduct()
{
		if(!confirm('Are you sure want to remove the item quantity?'))
		{
			return false;
		}
	$.ajax({
			url: 'viewOrderDetail.php?order=<?= $orderID; ?>',
			type: 'post',
			data:{removeProducts:$('input[name=removeTable]').val(), action:'removeProductNew'},
			dataType: 'json',       
			beforeSend: function() {
			},
			complete: function() {
			},              
			success: function(json) {
				
				console.log('removed successfully');
			}
		});

}
function editThis(item_id)
{

	$sku = $('#order_item_sku_' + item_id);
	$qty = $('#order_item_qty_' + item_id);
	$unit = $('#order_item_unit_' + item_id);
	$discount = $('#order_item_discount_' + item_id);
	$price = $('#order_item_price_' + item_id);
	$true_cost = $('#product_true_cost_' + item_id);

	
 $.fancybox({

     'width': '800px',
     
     
     
     
     'href' : '#edit_this',
     'content':'<h2>Update Product Price</h2><table style="width:400px" cellpadding="5" cellspacing="5" border="0"><tr><td>SKU:</td><td>'+$sku.val()+'</td></tr><tr><td>Old Unit Price:</td><td>'+$unit.val()+'</td></tr><tr><td>New Unit Price:</td><td><input type="text" class="new_unit_price"></td></tr><tr style="<?php echo ($_SESSION['login_as']!='admin'?'display:none':'');?>"><td>True Cost:</td><td><input type="text" class="new_true_cost" value="'+$true_cost.val()+'" ></td></tr><tr><td colspan="2" align="center"><br><input type="button" value="Submit" onClick="saveThis('+item_id+',this)"></td></tr></table>'
  });


}
function changeWarehouseToggle(obj)
{
	//console.log('here');
	if($(obj).is(":checked"))
	{
		$('.toggle_prices').hide();
		$('.toggle_warehouse').show();
	}
	else
	{
		$('.toggle_warehouse').hide();
		$('.toggle_prices').show();
	}
}

function changeTrueCostToggle(obj)
{
	//console.log('here');
	if($(obj).is(":checked"))
	{
		// $('.toggle_prices').hide();
		$('.toggle_true_cost').show();
	}
	else
	{
		$('.toggle_true_cost').hide();
		// $('.toggle_prices').show();
	}
}

 function saveThis(item_id,obj)
    {
    	if(!confirm('Please cross check the new unit price before proceeding, do you want to continue?'))
    	{
    		return false;
    	}



    	$sku = $('#order_item_sku_' + item_id);
	$qty = $('#order_item_qty_' + item_id);
	// $unit = $('#order_item_unit_' + item_id);
	$unit = $(obj).parent().parent().parent().find('.new_unit_price');
	$true_cost = $(obj).parent().parent().parent().find('.new_true_cost');
	$discount = $('#order_item_discount_' + item_id);
	$price = $('#order_item_price_' + item_id);
	console.log($unit.val());

	if(jQuery.trim($unit.val())=='')
	{
		alert('Please provide a valid unit price');
		return false;
	}

    	$.ajax({
    		url: 'viewOrderDetail.php',
    		type: 'post',
    		data: {action: 'save_item', order_id: '<?= $order['order_id']; ?>', sku: $sku.val(), qty: $qty.val(), item_unit: $unit.val(), discount: $discount.val(), price: $price.val(), item_id: item_id,true_cost:$true_cost.val()},
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
    				// $qty.addClass('read_class').attr('readOnly');
    				// $unit.addClass('read_class').attr('readOnly');
    				// $discount.addClass('read_class').attr('readOnly');
    				// alert(json['success']);
    				location.reload(true);
    			}
    		}
    	});


    }
/*
function editThis(item_id) {
	$sku = $('#order_item_sku_' + item_id);
	$qty = $('#order_item_qty_' + item_id);
	$unit = $('#order_item_unit_' + item_id);
	$discount = $('#order_item_discount_' + item_id);
	$price = $('#order_item_price_' + item_id);
	
	$('#save_btn_' + item_id).removeAttr('style');
	<?php if ($_SESSION['login_as'] == 'admin' || $_SESSION['order_price_override']):?>
    
            $unit.removeClass('read_class').removeAttr('readOnly');
            $discount.removeClass('read_class').removeAttr('readOnly');
        <?php endif; ?>
    }
    function saveThis(item_id)
    {
    	$sku = $('#order_item_sku_' + item_id);
    	$qty = $('#order_item_qty_' + item_id);
    	$unit = $('#order_item_unit_' + item_id);
    	$discount = $('#order_item_discount_' + item_id);
    	$price = $('#order_item_price_' + item_id);
    	$('#save_btn_' + item_id).hide();
    	$.ajax({
    		url: 'viewOrderDetail.php',
    		type: 'post',
    		data: {action: 'save_item', order_id: '<?= $order['order_id']; ?>', sku: $sku.val(), qty: $qty.val(), item_unit: $unit.val(), discount: $discount.val(), price: $price.val(), item_id: item_id},
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
    				$qty.addClass('read_class').attr('readOnly');
    				$unit.addClass('read_class').attr('readOnly');
    				$discount.addClass('read_class').attr('readOnly');
    				alert(json['success']);
    			}
    		}
    	});
    }*/

    function calculateLineTotal(item_id)
    {
    	$qty = $('#order_item_qty_' + item_id);
    	$unit = $('#order_item_unit_' + item_id);
    	$trueCost = $('#product_true_cost_' + item_id);
    	$trueTotalCost = $('#product_total_true_cost_' + item_id);
    	$discount = $('#order_item_discount_' + item_id);
    	$price = $('#order_item_price_' + item_id);
    	var sub_total = 0.00;
    	var total = 0.00;
    	var discount = 0.00;
    	var totalTureCost = 0.00;
    	sub_total = parseInt($qty.val()) * parseFloat($unit.val());
    	discount = (parseFloat(sub_total) * parseInt($discount.val())) / 100;
    	total = parseFloat(sub_total) - parseFloat(discount);
    	$price.val(total.toFixed(2));
    	totalTureCost = parseInt($qty.val()) * parseFloat($trueCost.val());
    	$trueTotalCost.text(totalTureCost.toFixed(2));
    }
    function calculateLineTotalx(t) {
    	$qty = $(t).find('input[type="product_qty"]');
    	$unit = $(t).find('input[type="product_unit"]');
    	$discount = $(t).find('input[type="product_discount"]');
    	$price = $(t).find('input[type="product_price"]');
    	var sub_total = 0.00;
    	var total = 0.00;
    	var discount = 0.00;
    	sub_total = parseInt($qty.val()) * parseFloat($unit.val());
    	discount = (parseFloat(sub_total) * parseInt($discount.val())) / 100;
    	total = parseFloat(sub_total) - parseFloat(discount);
    	$price.val(total.toFixed(2));
    }
    function addpickedup(obj)
    {
    	var status = $(obj).is(':checked');
    	$.ajax({
    		url: '',
    		type: 'POST',
    		dataType: 'json',
    		data: {action: 'addpickedup', chk:status},
    	})
    	.always(function() {
    		console.log("complete");
    	});
    }
    function verifyVoucher(t) {
    	if ($(t).val()) {
    		$.ajax({
    			url: 'order_create.php',
    			type: 'post',
    			dataType: 'json',
    			data: {action: 'verifyVoucher', vouchers: $(t).val()},
    		})
    		.always(function(data) {             				
    			if (data['error']) {
    				$('.voucher .error').html(data['msg']);
    				$('.voucher .total').html(data['total']);
    				applyVoucher($(t).val());
    				$('#voucher_code').val(data['valid']);
    			}
    			if (data['success']) {
    				$('.voucher .error').html('');
    				$('.voucher .total').html(data['total']);
    				applyVoucher($(t).val());
    				$('#voucher_code').val(data['valid']);
    			}
    		});
    	}
    }

    function applyVoucher(voucher_codes)
    {
    	$.ajax({
    			url: 'viewOrderDetail.php',
    			type: 'post',
    			dataType: 'json',
    			data: {action: 'apply_voucher', vouchers: voucher_codes,order_id:'<?php echo $_GET['order'];?>'},
    		})
    		.always(function(data) {             				
    			
    			if (data['success']) {
    				alert('Voucher(s) applied successfully, page will reload');
    				location.reload(true);
    			}
    		});
    }
    function populateAddress(category,obj)
    {
    	if(obj.value=='')
    	{
    		return false;
    	}
    	var str = obj.value;
    	var data = str.split("~");

    	if(category=='shipping')
    	{
    		var firstname = $('input[name=first_name]');
    		var lastname = $('input[name=last_name]');
    		var company = $('input[name=company_shipping]');
    		var address1 = $('input[name=address1]');
    		var address2 = $('input[name=address2]');
    		var city = $('input[name=city]');
    		var zone_id = $('select[name=zone_id]');
    		var country_id = $('select[name=country_id]');
    		var zip = $('input[name=zip]');
    		
    	}
    	else
    	{
    			var firstname = $('input[name=bill_firstname]');
    		var lastname = $('input[name=bill_lastname]');
    		var company = $('input[name=company_billing]');
    		var address1 = $('input[name=bill_address1]');
    		var address2 = $('input[name=bill_address2]');
    		var city = $('input[name=bill_city]');
    		var zone_id = $('select[name=bill_zone_id]');
    		var country_id = $('select[name=bill_country_id]');
    		var zip = $('input[name=bill_zip]');
    	}
    	firstname.val(data[0]);
    	lastname.val(data[1]);
    	company.val(data[2]);
    	address1.val(data[3]);
    	address2.val(data[4]);
    	city.val(data[5]);
    	zone_id.val(data[6]);
    	country_id.val(data[7]);
    	zip.val(data[8]);
    	







    }
    function captureBehalfPayment()
    {
    	$.ajax({
										url: 'viewOrderDetail.php',
										type:"POST",
										dataType:"json",
										data:{'order_id':'<?php echo $orderID;?>', 'action':'capture_behalf_payment'},
										success: function(json){
											window.location.reload();
											// $('input[name=update]').click();	
										}
									});
    }
</script>
