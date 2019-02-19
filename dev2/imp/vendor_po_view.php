<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = 'vendor_po';
$pageName = 'Vendor PO';
$pageLink = 'vendor_po.php';
$pageCreateLink = 'vendor_po_create.php';
$pageViewLink = 'vendor_po_view.php?vpo_id=' . $_GET['vpo_id'];
$pageSetting = false;
$table = '`inv_vendor_po`';


$comp_shortcodes = array(
	'cell_parts_hub'=>'CPH',
	'etrade_supply'=>'ES',
	'fixez'=>'FZ',
	'lcd_loop'=>'LL',
	'maya_cellular'=>'MC',
	'mengtor'=>'MG',
	'mobile_defenders'=>'MD',
	'mobile_sentrix'=>'MS',
	'parts_4_cells'=>'P4C',

	);

function generateRandomString($length = 7) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ=_';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
// error_reporting(E_ALL);
// ini_set("display_errors",1);
function getVendorShipmentNo($shipment_no,$i=1)
{
	global $db;
	$check = $db->func_query_first_cell("SELECT id FROM inv_shipments WHERE trim(lower(package_number))='".trim(strtolower($shipment_no)).'-'.$i."' ");
	if($check)
	{
		$i = $i+1;

		// echo $shipment_no.'-'.$i;exit;
		return getVendorShipmentNo($shipment_no,$i);
	}
	else
	{
		return $shipment_no.'-'.$i;
	}	
}
$vpo_id = (int) $_GET['vpo_id'];
$comments =$db-> func_query("SELECT * FROM inv_vendor_po_comments WHERE vendor_po_id='$vpo_id' ");
$details = $db->func_query_first("SELECT * FROM $table WHERE id = '$vpo_id'");
$applied_credits = $db->func_query_first_cell("SELECT SUM(amount) from inv_vendor_credit_data where vendor_id='".$details['vendor']."' and vendor_po_id='".$details['id']."'");
if(!$_SESSION['view_estimate_po'] and $deails['status']=='estimate')
{
	echo 'You do not have sufficient permissions to view this PO, please contact admin';
	exit;
}



if ($_POST['action'] == 'changeVendor') {
	$db->db_exec("UPDATE $table SET vendor='".$_POST['vendor']."' WHERE id='$vpo_id'");
	exit;
}
$vendor_po_id = $details['vendor_po_id'];
$shipment_data = $db->func_query_first("SELECT a.ex_rate, SUM(b.qty_received) as qty_received,sum(b.unit_price * b.qty_received) as unit_price,(select sum(c.shipping_cost) from inv_shipments c where c.vendor_po_id=a.vendor_po_id) as shipping_cost from inv_shipments a,inv_shipment_items b where a.id=b.shipment_id and a.vendor_po_id='".$vendor_po_id."'");
$items = $db->func_query("SELECT a.*, b.`image`, b.`product_id`,b.quantity FROM `inv_vendor_po_items` a, `oc_product` b WHERE a.`sku` = b.`model` AND vendor_po_id = '$vendor_po_id' ORDER BY a.`sku` ASC");
$shipments = $db->func_query('SELECT * FROM `inv_shipments` WHERE `vendor_po_id` = "'. $vendor_po_id .'"');
$rj_breakdown =  $db->func_query("SELECT * from inv_shipments a,inv_rejected_shipment_items b where a.id=b.shipment_id and a.vendor_po_id='".$vendor_po_id."'");
foreach ($shipments as $i => $shipment) {
	$shipments[$i]['items'] = $db->func_query('SELECT * FROM `inv_shipment_items` WHERE `shipment_id` = "'. $shipment['id'] .'"');
	$totalItems = $db->func_query_first_cell('SELECT SUM(qty_shipped) FROM `inv_shipment_items` WHERE `shipment_id` = "'. $shipment['id'] .'"');
	
	foreach ($items as $key => $item) {

		foreach ($shipments[$i]['items'] as $sKey => $sItem) {
			$sItem['shipmentCost'] = $shipment['shipping_cost'] / $totalItems;
			
			$shipments[$i]['items'][$sKey]['image'] = $db->func_query_first_cell("SELECT `image` FROM `oc_product` WHERE `model` = '". $sItem['product_sku'] ."'");
			$shipments[$i]['items'][$sKey]['package_number'] = $shipment['package_number'];
			$shipments[$i]['items'][$sKey]['shipmentCost'] = $sItem['shipmentCost'];

			if ($sItem['product_sku'] == $item['sku']) {

				$items[$key]['shipments'][] = array(
					'shipment_id' => $shipment['id'],
					'qty_shipped' => $sItem['qty_shipped'],
					'package_number' => $shipment['package_number'],
					'shipmentCost' => $sItem['shipmentCost'],
					'price' => $sItem['unit_price'] / $details['ex_rate'],
					'qty_received'=>$sItem['qty_received']
					);
				$shipments[$i]['items'][$sKey]['exist'] = 1;
				$items[$key]['qty_S'] += $sItem['qty_shipped'];
				
				if ($items[$key]['qty_S'] > $item['req_qty']) {
					//$shipments[$i]['items'][$sKey]['exist'] = 0;
					$items[$key]['extra'] = $items[$key]['qty_S'] - $item['req_qty'];
				}
			}

		}

	}
	
}
$amount_to_be_paid = 0.00;


foreach ($items as $key => $item) {
	
		//
	$amount_to_be_paid+=$item['new_cost']*$item['qty_shipped'];
	$my_cost+=$item['new_cost']*$item['req_qty'];
	
	//

	$items[$key]['cost'] = $item['cost'] / $details['ex_rate'];
	$tShiped = 0;
	foreach ($item['shipments'] as $i => $shipment) {
		$tShiped += $shipment['qty_shipped'];
	}
	if ($tShiped) {
		$items[$key]['qty_shipped'] = $tShiped;
		$items[$key]['needed'] = $item['req_qty'] - $tShiped;
	}
	else
	{
		$items[$key]['needed'] = $item['req_qty'];
	}
	$items[$key]['avg_comp_price'] = $db->func_query_first_cell("SELECT AVG(recent_price) FROM inv_product_price_scrap WHERE sku='".$item['sku']."' AND url<>''");
	$items[$key]['low_comp_price'] = $db->func_query_first_cell("SELECT MIN(recent_price) FROM inv_product_price_scrap WHERE sku='".$item['sku']."' AND url<>''");
	$items[$key]['low_comp_name'] = $db->func_query_first_cell("SELECT type FROM inv_product_price_scrap WHERE sku='".$item['sku']."' AND url<>'' and recent_price='".$items[$key]['low_comp_price']."'");
	$items[$key]['low_comp_url'] = $db->func_query_first_cell("SELECT url FROM inv_product_price_scrap WHERE sku='".$item['sku']."' AND url<>'' and recent_price='".$items[$key]['low_comp_price']."'");
	
}

$extraItems = array();
foreach ($shipments as $i => $shipment) {
	foreach ($shipments[$i]['items'] as $sKey => $sItem) {
		if (!$sItem['exist']) {
			$sItem['unit_price'] = $sItem['unit_price'] / $shipment['ex_rate'];
			$extraItems[] = $sItem;
		}

	}
}


$statuses = array(
	'estimate' => 'Estimate',
	'issued' => 'Issued',
	'shipped' => 'Completed',
	// 'paid' => 'Paid',
	);

if($_POST['action']=='generate_link')
{
	$short_url = generateRandomString();
	$check = $db->func_query_first_cell("SELECT id from $table WHERE short_url='$short_url'");
	$json = array();
	if(!$check)
	{
		$db->db_exec("UPDATE $table SET short_url='$short_url' WHERE id='".$vpo_id."'");
		$json['success']  = '<a href="https://PhonePartsUSA.com/VendorPO/'.$short_url.'" target="_blank">https://PhonePartsUSA.com/VendorPO/'.$short_url.'</a>';
	}
	else
	{
		$json['error'] = 'Unable to create Short URL, please try again';

	}
	echo json_encode($json);
	exit;
}
if($_POST['action']=='load_vendor_credit')
{

	$vendor_id = (int)$_POST['vendor_id'];
	$vendor_po_id = (int)$_POST['vendor_po_id'];
	$vendor_po = $db->func_query_first_cell("SELECT  vendor_po_id FROM inv_vendor_po WHERE id='".$vendor_po_id."'");

	$shipment_data = $db->func_query_first("SELECT a.ex_rate, SUM(b.qty_received) as qty_received,sum(b.unit_price * b.qty_received) as unit_price,(select sum(c.shipping_cost) from inv_shipments c where c.vendor_po_id=a.vendor_po_id) as shipping_cost from inv_shipments a,inv_shipment_items b where a.id=b.shipment_id and a.vendor_po_id='".$vendor_po."'");
	$details = $db->func_query_first("SELECT * FROM $table WHERE id = '".$vendor_po_id."'");

	$json = array();
	if($vendor_id)
	{
			$json['success'] = 1 ;

			$json['amount'] = round($db->func_query_first_cell("SELECT SUM(amount) from inv_vendor_credit_data where vendor_id='".$vendor_id."'"),2);
			if($json['amount']>0.00)
			{
				$json['button_visible']  = 1;
			}
			else
			{
				$json['button_visible'] = 0;
			}
			$json['applied_credits']  = round($db->func_query_first_cell("SELECT SUM(amount) from inv_vendor_credit_data where vendor_id='".$vendor_id."' and vendor_po_id='".$vendor_po_id."'"),2);

			$total_balance = ($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate']-($details['amount_paid']-$json['applied_credits'])+$details['amount_refunded'];
			if($total_balance<=0.00)
			{
				//$json['button_visible']=0;
			}

	}
	else
	{
			$json['error']  = 'Vendor not defined, please make sure vendor is properly selected';
	}
	echo json_encode($json);exit;
}

if($_POST['action']=='apply_vendor_credit')
{
	// echo 'here';exit;

	$vendor_id = (int)$_POST['vendor_id'];
	$vendor_po_id = (int)$_POST['vendor_po_id'];
	$vendor_po = $db->func_query_first_cell("SELECT  vendor_po_id FROM inv_vendor_po WHERE id='".$vendor_po_id."'");

	$details = $db->func_query_first("SELECT * FROM inv_vendor_po WHERE id = '".(int)$_POST['vendor_po_id']."'");

	$shipment_data = $db->func_query_first("SELECT a.ex_rate, SUM(b.qty_received) as qty_received,sum(b.unit_price * b.qty_received) as unit_price,(select sum(c.shipping_cost) from inv_shipments c where c.vendor_po_id=a.vendor_po_id) as shipping_cost from inv_shipments a,inv_shipment_items b where a.id=b.shipment_id and a.vendor_po_id='".$vendor_po."'");

	$total_received = ($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate'];


	$json = array();
	
			

			$available_credits = round($db->func_query_first_cell("SELECT SUM(amount) from inv_vendor_credit_data where vendor_id='".$vendor_id."'"),2);
			// echo $available_credits;exit;
			// echo $total_received;exit;

			if($available_credits<=$total_received)
			{
				$amount_to_apply = $available_credits*(-1); 
			}
			else
			{
				if($total_received)
				{
				$amount_to_apply = $total_received*(-1);
					
				}
				else
				{
					$amount_to_apply = $available_credits*(-1); 
				}

			}
			// echo $amount_to_apply;exit;
			
			$db->db_exec("INSERT INTO inv_vendor_credit_data SET vendor_id='".(int)$vendor_id."',amount='".(float)$amount_to_apply."',comment='Vendor Credit has been applied',type='vendor_po',vendor_po_id='".$vendor_po_id."',user_id='".$_SESSION['user_id']."',date_added='".date('Y-m-d H:i:s')."'");

			addComment('vendor_po',array('id' => $vendor_po_id, 'comment' => 'Vendor credit has been applied'));


			// change the PO Payment status


			$applied_credits = $db->func_query_first_cell("SELECT SUM(amount) from inv_vendor_credit_data where vendor_id='".$details['vendor']."' and vendor_po_id='".$details['id']."'");
			
			if((int)$shipment_data['qty_received']==0)
		{
			$payment_status_new = 'Pre-Paid';
		}

		if((int)$shipment_data['qty_received']>0 && (round(($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate']-($details['amount_paid']-$applied_credits)+$details['amount_refunded']))==0)
		{
			$payment_status_new= 'Paid';
		}

		if((int)$shipment_data['qty_received']>0 && (round(($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate']-($details['amount_paid']-$applied_credits)+$details['amount_refunded']))>0)
		{
			$payment_status_new= 'Not Paid';
		}

		if((int)$shipment_data['qty_received']>0 && (round(($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate']-($details['amount_paid']-$applied_credits)+$details['amount_refunded']))<0)
		{
			$payment_status_new= 'Over-Paid';
		}
		$db->db_exec("UPDATE inv_vendor_po SET payment_status_new='".$payment_status_new."' WHERE id='".$vpo_id."'");


			
	
	echo json_encode(array('success'=>1));exit;
}


if($_GET['action']=='delete_selection')
{
	$list = rtrim($_GET['list'],",");
	$lists = explode(",", $list);
	// print_r($lists);exit;
	$comment = '';
	foreach($lists as $list)
	{
		$_sku = $db->func_query_first_cell("SELECT sku from inv_vendor_po_items WHERE id='".(int)$list."'");
		if($_sku)
		{

		$db->func_query("DELETE FROM inv_vendor_po_items WHERE id='".(int)$list."'");
			$comment.=$_sku." Removed<br>";
		}
	}
		// echo $comment;exit;
	addComment('vendor_po',array('id' => $_GET['vpo_id'], 'comment' => $comment));

	header("Location:vendor_po_view.php?vpo_id=".$_GET['vpo_id']);
	exit;
}
if ($_POST['action'] == 'removeProduct') {
	$item = $items[array_search($_POST['id'], array_column($items, 'id'))];
	if ($item['qty_shipped'] == 0) {
		$db->func_query("DELETE FROM `inv_vendor_po_items` WHERE id = '" . $_POST['id'] . "'");
		$comment = linkToProduct($_POST['sku']).' has been deleted.';
		addComment('vendor_po',array('id' => $vpo_id, 'comment' => $comment));
		$json['success'] = 1;
	} else {
		$json['error'] = 'Item is Under shipping';
	}

	echo json_encode($json);
	exit;
}

if ($_POST['action'] == 'getShipment') {
	$shipments = $db->func_query('SELECT * FROM `inv_shipments` WHERE vendor_po_id = "'. $vendor_po_id .'" AND status = "Pending"');
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
	// print_r($_POST);exit;
	$shipment = array();
	if ($_POST['shipment_id']) {
		$shipment['id'] = $_POST['shipment_id'];
	}
	foreach ($_POST['product'] as $productx) {
		$item = $items[array_search($productx['id'], array_column($items, 'id'))];
		if ($productx['update']) {
			$item['qty_shipped'] = $productx['qty_shipped'];
			$item['toShip'] = $productx['toShip'];
		}
		if ($item && $item['needed'] && $item['toShip']) {
			// if (!isset($shipment['id'])) {
			// 	$shipment = $db->func_query_first('SELECT * FROM `inv_shipments` WHERE vendor_po_id = "'. $vendor_po_id .'" AND status = "Pending"');
			// }
			
			if (!isset($shipment['id'])) {
				// echo 'here';exit;
				// echo  getVendorShipmentNo($vendor_po_id);exit;
				$shipment = array(
					'package_number' => getVendorShipmentNo($vendor_po_id),
					'status' => 'Pending',
					'vendor' => $details['vendor'],
					'ex_rate' => $details['ex_rate'],
					'vendor_po_id' => $vendor_po_id,
					'date_added' => date('Y-m-d H:i:s')
					);
				$shipment['id'] = $db->func_array2insert("inv_shipments", $shipment);

					$status_comment = 'Shipment has been created';

		addComment('shipment',array('id' => $shipment['id'], 'comment' => $status_comment));
				
			}

			$shipment_item = $db->func_query_first('SELECT * FROM `inv_shipment_items` WHERE `shipment_id` = "'. $shipment['id'] .'" AND product_sku = "'. $item['sku'] .'" ');
			if ($shipment_item) {
				$db->db_exec('UPDATE inv_shipment_items SET qty_shipped = (qty_shipped + '. $item['toShip'] .') WHERE id = "'. $shipment_item['id'] .'"');
				
				$shipping_item_id = $shipment_item['id'];
			} else {
				$array = array(
					'shipment_id'	=> $shipment['id'],
					'from_vpo'	=> '1',
					'product_id'	=> $item['product_id'],
					'product_name'	=> $item['name'],
					'product_sku'	=> $item['sku'],
					'qty_shipped'	=> $item['toShip'],
					'cu_po'			=> $item['reference'],
					'unit_price'	=> $item['new_cost']

					);
				$shipping_item_id = $db->func_array2insert("inv_shipment_items", $array);
				
				
				unset($array);
			}
			$qty_shipped = $item['qty_shipped'] + $item['toShip'];
			$product = array(
				'qty_shipped' => $qty_shipped,
				'needed' => $item['needed'] - $item['toShip'],
				'shipment' => (($qty_shipped >= $item['req_qty'])? 1 : 0),
				'shipment_added' => date('Y-m-d H:i:s')
				);
			$db->func_array2update("inv_vendor_po_items", $product, "id = '". $item['id'] ."'");
			unset($product);
		} else if (!$item['needed']) {
			$product = array(
				'shipment' => 1
				);
			$db->func_array2update("inv_vendor_po_items", $product, "id = '". $item['id'] ."'");
		}
	}
	
	exit;
}
if($_POST['addcomment']) {
	
	$_SESSION['message'] = addComment('vendor_po',array('id' => $vpo_id, 'comment' => $_POST['comment']));
	header("Location:" . $pageViewLink);
	exit;
}
if ($_POST['update']) {
	

	if ($_POST['status'] != $details['status']) {

		
		$array = array(
			'status' => $_POST['status'],
			'date_updated' => date('Y-m-d H:i:s')
			);

		$status_comment = '<strong style="color:red">Status changed from `'.ucfirst($details['status']).'` to `'.ucfirst($_POST['status']).'`</strong>';

		addComment('vendor_po',array('id' => $vpo_id, 'comment' => $status_comment));

	}

	if ($_POST['ex_rate'] != $details['ex_rate']) {
		
		$array['ex_rate'] = (float) $_POST['ex_rate'];
		$array['date_updated'] = date('Y-m-d H:i:s');
	}
	$array['shipping_cost'] = (float)$_POST['shipping_cost'];
	if($_POST['amount_paid'] and $_POST['status']=='paid')
	{
			// $array['amount_paid'] = (float)$_POST['amount_paid'];

	}
	if($_SESSION['update_payment_status'])
	{
		// $array['payment_status'] = $_POST['payment_status']; // if permission assigned to update the payment status
		// $array['payment_method'] = $_POST['payment_method']; // if permission assigned to update the payment status
	}
	// print_r($_POST);exit;

	if ($array) {
		$db->func_array2update("inv_vendor_po", $array, "id = '$vpo_id'");
	}
	if (($_SESSION[$perission])) {
		foreach ($_POST['product'] as $i => $product) {
			$id = (int) $product['id'];
			unset($product['id'], $product['toShip']);
			
				
				$product['date_updated'] = date('Y-m-d H:i:s');
				$product['shipment'] = 0;
				$product['reference'] = $product['reference'];
				if(isset($product['new_cost']))
				{

				$product['new_cost'] = (float)$product['new_cost'];
				}
				if(isset($product['update']))
				{
				unset($product['update']);
				}
				
				$db->func_array2update("inv_vendor_po_items", $product, "id = '$id'");
			
		}
	}




	$_SESSION['message'] = $pageName . ' Updated';

	header("Location:" . $pageViewLink);
	exit;
}

if ($details['vendor'] != $_SESSION['user_id'] && $_SESSION['login_as'] != 'admin' && !$_SESSION['vendor_po_page']){
	exit;
}
if(isset($_GET['debug']))
{
	echo "<pre>"; print_r($items); echo "</pre>";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />


	
	<script>
		function allowFloat (t) {
			var input = $(t).val();
			var valid = input.substring(0, input.length - 1);
			if (isNaN(input) || input == ' ') {
				$(t).val(valid);
			}
		}
		function checkWhiteSpace (t) {
			if ($(t).val() == ' ') {
				$(t).val('');
			}
		}

	</script>
	<style type="text/css" media="screen">
		.blackPage {
			position: fixed;
			background: rgba(0,0,0,.5);
			height: 100%;
			width: 100%;
			top: 0;
			left: 0;
		}
		.whitePage {
			background: rgb(255, 255, 255) none repeat scroll 0% 0%;
			padding: 50px;
			position: relative;
			left: 50%;
			transform: translate(-50%, -50%);
			max-width: 500px;
			text-align: center;
			top: 50%;
			border-radius: 20px;
			box-shadow: 0px 0px 5px 0px #000;
		}
		.form {
			padding: 10px;
		}
		.form input {
			margin: 10px;
		}
	</style>
</head>
<body>
	<div align="center">
		<div align="center"> 
			<?php include_once 'inc/header.php';?>
		</div>
		<?php if ($_SESSION['message']) { ?>
		<div align="center"><br />
			<font color="red">
				<?php
				echo $_SESSION['message'];
				unset($_SESSION['message']);
				?>
				<br />
			</font>
		</div>
		<?php } ?>
		<form id="vendorForm" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="update" value="Update">
			<h2><?= $pageName; ?></h2><a target="_blank" href="export_vendor_csv.php?vpo_id=<?=$vpo_id;?>">Download CSV</a><br><br>
			<a target="_blank" href="export_vendor_competitor_csv.php?vpo_id=<?=$vpo_id;?>">Download CSV with Competitor Pricing</a><br><br>
			<a target="_blank" href="vendor_po_pdf.php?vendor_po_id=<?=$vpo_id;?>">Download Invoice</a><br><br>

		
			<table align="center" border="1" width="80%" cellpadding="10" cellspacing="0" style="border:1px solid #ddd;border-collapse:collapse;font-weight:bold">
				<tr>
					<td>
						Status:
					</td>
					<td >
						 <select name="status">
							<?php foreach ($statuses as $i => $row) : ?>
								<option <?php echo ($details['status'] == $i)? 'selected="selected"': '';?> value="<?php echo $i; ?>"><?php echo ucfirst($row); ?></option>
							<?php endforeach; ?>
						</select>

					</td>
					<td>
						Vendor: 
					</td>
					<?php if($details['status'] =='estimate'){?>
					<td>
						<select id="vendor" onchange="changeVendor(this);">
								<option value="">---Select---</option>
								<?php foreach ($db->func_query('SELECT * FROM `inv_users` WHERE group_id = 1 order by lower(name) asc') as $i => $vendor) : ?>
									<option <?php echo ($details['vendor'] == $vendor['id'])? 'selected="selected"': '';?> value="<?php echo $vendor['id']; ?>"><?php echo ucfirst($vendor['name']); ?></option>
								<?php endforeach; ?>
							</select>
					</td>
						<?php } else { ?>
						<td>
						<?php
			$image_path = getVendorPic($details['vendor']);

			?>
			

						<?php echo get_username($details['vendor']); ?>
						<img src="<?php echo $image_path;?>" style="width:30px">
					</td>
						<?php }?>
				</tr>
				<tr>
					
					<td>
						Vendor PO ID: 
					</td>
					<td>
						<?php echo $details['vendor_po_id']; ?>
					</td>

					<td>
						Date Added: 
					</td>
					<td>
						<?php echo americanDate($details['date_added']); ?>
					</td>
				</tr>
				<tr>
					
					<td>
						Date Updated: 
					</td>
					<td>
						<?php echo americanDate($details['date_updated']); ?>
					</td>

					<td >
						Exchange Rate: 
					</td>
					<td >
						<input type="text" name="ex_rate" value="<?php echo $details['ex_rate']; ?>" placeholder="Exchange Rate">
					</td>
				</tr>

							<tr>

							<td >
						Shipping: 
					</td>
					<td >
						<input type="text" name="shipping_cost" value="<?php echo $details['shipping_cost']; ?>" placeholder="Shipping Amount">
					</td>
					
							<?php
							if($_SESSION['update_payment_status'])
							{
							?>
					<td >
						Payment Method:</td>
					</td>
					<td >

						<!-- <select name="payment_method" onchange="">		
						<option value="">Please Select</option>
						
						<option <?php echo ($details['payment_method'] == 'Bank Wire')? 'selected="selected"': '';?>>Bank Wire</option>
						<option <?php echo ($details['payment_method'] == 'ACH')? 'selected="selected"': '';?>>ACH</option>
						<option <?php echo ($details['payment_method'] == 'Cash')? 'selected="selected"': '';?>>Cash</option>
						
								
							</select>
							 -->
							 <!-- <input type="hidden" name="payment_status" value="unpaid"> -->
							<?php echo ($details['payment_method']?$details['payment_method']:'N/A');?>
					</td>

					<?php
				}
				?>



					
				</tr>
				<?php
				//if( $details['status']=='issued' )
				//{
				?>
				<tr>
					<td>
						Amount Paid: 
					</td>
					<td >

				<?php
										echo '$'.number_format($details['amount_paid'],2);
					

				?>


					</td>

					<td>
								Credit Applied
						</td>

						<td>
								 <span id="vendor_credit_applied" style="color:red">
								0.00
								</span>
						</td>
				</tr>
				<tr id="vendor_credit_row" style="display: none" >

						<td>
								Vendor Credit:
						</td>
						<td >
								 <span id="vendor_credit_span" style="color:green">
								0.00
								</span>
								<button class="button button-danger" id="apply_vendor_credit" onclick="applyVendorCredit();" style="display:none">Apply</button>
								
						</td>
						

				</tr>	
				<?php
				//}
				?>	


						
				
				<tr>


				<td colspan="4" align="center">

				<br>
				<?php
				if($details['status']=='estimate'){
				?>

				<button class="button button-info" id="generate_link">Request Vendor Pricing</button>

				<?php
			}
			?>

				<?php if ($_SESSION[$perission] || $_SESSION['user_id'] == $details['vendor']) : ?>
							<input class="button update_btn" type="button" name="update" value="Update" />

								<?php
									if($details['status']=='estimate')
									{
										?>
													<input class="button " type="button" onclick="changeStatus('issued');"  value="Save &amp; Issue" />

										<?php
									}

								?>

								<?php
									if($details['status']=='issued')
									{
										?>
													<input class="button " type="button" onclick="changeStatus('shipped');"  value="Save &amp; Ship" />

										<?php
									}

								?>

								<?php
									if($details['status']=='shipped' && $_SESSION['update_payment_status'])
									{
										?>
													<!-- <input class="button " type="button" onclick="changeStatus('paid');"  value="Save &amp; Pay" /> -->

										<?php
									}



								?>
								<?php
								if( $details['payment_status_new']=='No Payment Status' || $details['payment_status_new']=='Not Paid')
								// if(round(($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate']-($details['amount_paid']-$applied_credits)+$details['amount_refunded'],2)>0)
								{
								?>
								<a href="<?php echo $host_path;?>popupfiles/make_payment_vendorpo.php?vpo_id=<?php echo $_GET['vpo_id'];?>" class="button button-danger fancybox fancybox.iframe" >Make Payment</a>
								<?php
							}
							if($details['amount_refunded']==0.00 && $details['payment_status']=='paid')
							{
									?>
											<a href="<?php echo $host_path;?>popupfiles/make_refund_vendorpo.php?vpo_id=<?php echo $_GET['vpo_id'];?>" class="button button-danger fancybox fancybox.iframe" >Refund Payment</a>
									<?php
							}
							?>


						<?php endif; ?>
				<br>
				<br>
				<span class="short_url"><?php echo ($details['short_url']?'<a href="https://PhonePartsUSA.com/VendorPO/'.$details['short_url'].'" target="_blank">https://PhonePartsUSA.com/VendorPO/'.$details['short_url'].'</a>':'');?></span>

				</td>
				</tr>
				
				
			</table>
			<br>

			<?php
			$_items_ordered = 0;
			foreach ($items as $key => $item) {
				$_items_ordered+=$item['req_qty'];
			}	

			// echo "SELECT a.ex_rate, SUM(b.qty_received) as qty_received,sum(b.unit_price * b.qty_received) as unit_price,(select sum(c.shipping_cost) from inv_shipments c where c.vendor_po_id=a.vendor_po_id) as shipping_cost from inv_shipments a,inv_shipment_items b where a.id=b.shipment_id and a.vendor_po_id='".$vendor_po_id."'";exit;
			
			?>	
				<table width="80%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
						<tr>
								<td width="33%" style="vertical-align: top;">
										<table cellpadding="5" cellspacing="0" style="border:1px solid #ddd" width="90%">
												<tr>
														<td colspan="3" style="font-weight: bold;text-align: center">Shipment Totals</td>

												</tr>
												<tr>
														<td></td>
														<td>Ordered</td>
														<td>Received</td>
												</tr>
												<tr>
														<td>Items</td>
														<td><?php echo (int)$_items_ordered;?></td>
														<td><?php echo (int)$shipment_data['qty_received'];?></td>
												</tr>

												<tr>
														<td>Cost</td>
														<td><?php echo number_format($my_cost,2);?></td>
														<td><?php echo number_format($shipment_data['unit_price'],2);?></td>
												</tr>

												<tr>
														<td>Shipping</td>
														<td><?php echo number_format($details['shipping_cost'],2);?></td>
														<td><?php echo number_format($shipment_data['shipping_cost'],2);?></td>
												</tr>

												<tr>
														<td>Total (Vendor)</td>
														<td></td>
														<td><?php echo number_format($shipment_data['shipping_cost']+$shipment_data['unit_price'],2);?></td>
												</tr>

												<tr>
														<td>Ex. Rate</td>
														<td><?php echo number_format($details['ex_rate'],2);?></td>
														<td><?php echo number_format($shipment_data['ex_rate'],2);?></td>
												</tr>

												<tr>
														<td>Total (USD)</td>
														<td><?php echo '$'.number_format(($my_cost+$details['shipping_cost'])/$details['ex_rate'],2);?></td>
														<td><?php echo '$'. number_format(($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate'],2);?></td>
												</tr>

												<tr>
														<td>Total Paid (USD)</td>
														<td>-</td>
														<td><?php echo '$'. number_format($details['amount_paid']+$applied_credits*(-1),2);?></td>
												</tr>

												<tr>
														<td>Total Refund (USD)</td>
														<td>-</td>
														<td><?php echo '$'. number_format($details['amount_refunded'],2);?></td>
												</tr>

												<tr>
														<td>Balance (USD)</td>
														<td>-</td>
														<td><?php echo '$'. number_format(round(($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate']-($details['amount_paid']-$applied_credits)+$details['amount_refunded'],2),2);?></td>
												</tr>

												

										</table>


								</td>
								<?php
								$shipments_breakdown =  $db->func_query("SELECT a.id as shipment_id,a.package_number, SUM(b.qty_shipped) as qty_shipped,sum(b.qty_received) as qty_received,sum(b.unit_price * b.qty_received) as unit_price,a.ex_rate,a.shipping_cost from inv_shipments a,inv_shipment_items b where a.id=b.shipment_id and a.vendor_po_id='".$vendor_po_id."' group by a.id");
								?>
								<td width="33%" style="vertical-align: top">
									<table cellpadding="5" cellspacing="0" style="border:1px solid #ddd" width="90%">
											<tr>
														<td colspan="3" style="font-weight: bold;text-align: center">Shipment Breakdown</td>

												</tr>
												<tr>
												<td>Shipment #</td>
												<td>Ordered</td>
												<td>Received</td>
												<td>Total</td>
												</tr>
												<?php
												foreach($shipments_breakdown as $shipment_breakdown)
												{
													?>
														<tr>
														<td><?php echo linkToShipment($shipment_breakdown['shipment_id'],'',$shipment_breakdown['package_number'],'target="_blank"'); ?>(<?php echo linkToShipmentEdit($shipment_breakdown['shipment_id'],'','Edit','target="_blank"'); ?>)</td>
														<td><?php echo (int)$shipment_breakdown['qty_shipped'];?></td>
														<td><?php echo (int)$shipment_breakdown['qty_received'];?></td>
														<td><?php echo '$'. number_format(($shipment_breakdown['shipping_cost']+$shipment_breakdown['unit_price'])/$shipment_breakdown['ex_rate'],2);?></td>

														</tr>
													<?php
												}
												?>

									</table>
								</td>

								<?php
								$shipments_rj =  $db->func_query_first("SELECT sum(b.qty_rejected) as qty_rejected, sum(b.cost) as cost,ex_rate from inv_shipments a,inv_rejected_shipment_items b where a.id=b.shipment_id and a.vendor_po_id='".$vendor_po_id."'");
								?>

								<td width="33%" style="vertical-align: top">
									<table cellpadding="5" width="90%"  cellspacing="0" style="border:1px solid #ddd;">
										<tr>
														<td colspan="2" style="font-weight: bold;text-align: center">RJ</td>

												</tr>

												<tr>
												<td># of Items</td>
												<td><?php echo (int)$shipments_rj['qty_rejected'];?></td>
												</tr>
												<tr>
												<td>Cost (Vendor)</td>
												<td><?php echo number_format($shipments_rj['cost'],2);?></td>
												</tr>

												<tr>
												<td>Ex. Rate</td>
												<td><?php echo number_format($shipments_rj['ex_rate'],2);?></td>
												</tr>

												<tr>
												<td>Total (USD)</td>
												<td><?php echo '$'. number_format($shipments_rj['cost']/$shipments_rj['ex_rate'],2);?></td>
												</tr>
									</table>

								</td>

						</tr>
				</table>

			<br><br>
			
			<table width="80%" >
				<tr>
					<td>
						<table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;border:1px solid #ddd">
							<tr>
								<td>

									<b>Comment</b>
								</td>
								<td>
									<textarea rows="5" style="width: 90%;" name="comment" ></textarea>


								</td>
							</tr>

							<tr>
								<td colspan="2" align="center"> <input type="submit" class="button" name="addcomment" value="Add Comment" />	</td>
							</tr> 	   
						</table>
					</td>
					<td style="vertical-align:top">
						<h2>Comments History</h2>
						<table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse; vertical-align:top;border:1px solid #ddd">
							<tr>
								<th>Date Added</th>
								<th>Comment</th>
								<th>Added By</th>
							</tr>
							<?php
							foreach($comments as $comment) {	?>
							<tr>
								<td><?=americanDate($comment['date_added']);?></td>
								<td><?=$comment['comment'];?></td>
								<td><?=get_username($comment['user_id']);?></td>
							</tr>
							<?php
						}
						?>
					</table>
				</td>
			</tr>
			
		</table>
		<br><br>
		<table align="center" width="95%" cellpadding="10" cellspacing="0" style="border:0px solid #ddd;border-collapse:collapse;">
			<tr>
			<td align="left" width="33%">
				<?php if ($_SESSION['user_id'] == $details['vendor'] || $_SESSION[$perission]) : ?>
							<?php
							if($details['status']=='estimate')
							{
								?>
									<input class="button button-danger delete_selection" type="button" value="Delete Selection"  />
								<?php
							}

							endif;
							?>

			</td>
				<td align="center" width="33%">
				<h2 style="font-size:14px">PO Ordered Items</h2>
				</td>
					<td align="right" width="33%">
						
						<?php if ($_SESSION['user_id'] == $details['vendor'] || $_SESSION[$perission]) : ?>
							
							<input class="button button-info" type="button" class="shipmentBtn" value="Add to Shipment" onclick="selectGetShipment();" />
						<?php endif; ?>
						
					</td>
				</tr>
			</table>

			<table align="center" border="1" width="95%" cellpadding="10"  class="tablesorter" cellspacing="0" style="border:1px solid #ddd;border-collapse:collapse;">
				<thead>
					<tr>
						<?php if ($_SESSION['user_id'] == $details['vendor'] || $_SESSION[$perission]) : ?>
							<th width="2%">
								<input type="checkbox" data-class="select" onclick="selectAllCheck(this);">
							</th>
						<?php endif; ?>
						<th width="6%">
							Image
						</th>
						<th width="11%">
							SKU
						</th>
						<th width="35%">
							Name
						</th>
						<th width="8%%">
						Ref.
						</th>
						<?php if ($details['status'] == 'estimate') { ?>
						<th width="10%">
						Sale History
						</th>
						<?php }?>
						<th width="10%">
							Req.
						</th>
						<th <?php echo (!$_SESSION['v_po_price'])? 'style="display: none;"':''; ?>  width="10%">
							Prev. Cost
						</th>
						<?php
						if($details['status']=='estimate')
						{
							?>
							<th>Avg. Comp Price</th>
							<th>Lowest. Comp Price</th>
							<th>Margin</th>
							<?php
						}
						?>

						<?php
						if($details['status']!='estimate')
						{
							?>
						<th width="10%">
							Shipped
						</th>
						<th width="20%">
							Package
						</th>
						<?php
					}
					?>
					<?php
					if($details['status']=='estimate')
					{
					?>
						<th <?php echo (!$_SESSION['v_po_price'])? 'style="display: none;"':''; ?> colspan="2" width="9%">
							New Cost
						</th>
						<?php
					}
					?>
						<?php
						if($details['status']!='estimate')
						{
							?>
					
						<th width="6%">
							Completed
						</th>
						<?php
					}

					?>
					</tr>
				</thead>
				<tbody class="products">
					<?php $total_lineCost = 0.00; ?>
					<?php $total_shippingCost = 0.00; ?>
					<?php foreach ($items as $key => $row) : ?>
						<?php
							
						?>
						<?php
						$_qty = $db->func_query_first_cell("SELECT quantity from oc_product where sku='".$row['sku']."'");
						$p_id = $db->func_query_first_cell("SELECT product_id from oc_product where sku='".$row['sku']."'");
						?>
						<?php //$total_lineCost += $row['cost'] * $row['req_qty']; ?>
						<tr class="product-<?php echo $key; ?> pr-<?php echo $row['id']; ?>" <?php if($_qty<=0) { echo 'style="background-color:#ffb2b2"';}?>>
							<?php if ($_SESSION['user_id'] == $details['vendor'] || $_SESSION[$perission]) : ?>
								<td class="select">
									<input type="checkbox" name="product[<?php echo $key; ?>][update]" value="<?php echo $row['id']; ?>">
								</td>
							<?php endif; ?>
							<td class="image" align="center">
							<?php if (stripos(noImage($row['image'], $host_path, $path),'image-coming-soon')) {
								$thumb =   noImage($row['image'], $host_path, $path,0);
							}else {
								$thumb =   noImage($row['image'], $host_path, $path);
								} ?>
								<a class="fancybox2 fancybox.iframe" href="<?= noImage($row['image'], $host_path, $path,0); ?>" target="_blank">
									<img width="100%" src="<?= $thumb; ?>" alt="" />
								</a>
							</td>
							<td class="sku" align="center">
								<span><?php echo linkToProduct($row['sku'], $host_path); ?></span>
								<input type="hidden"  name="product[<?php echo $key; ?>][id]" value="<?php echo $row['id']; ?>" placeholder="Enter SKU">
								<input type="hidden" id="hidden_sku<?php echo $row['id']; ?>" name="product[<?php echo $key; ?>][sku]" value="<?php echo $row['sku']; ?>" placeholder="Enter SKU">
							</td>
							<td class="name">
								<span><?php echo $row['name']; ?></span>
							</td>
							<td> <input type="text" name="product[<?php echo $key; ?>][reference]"  value="<?php echo $row['reference']; ?>" style="width:100px"> </td>
							<?php if ($details['status'] == 'estimate') { ?>
							<td align="center">
								In Stock: <?php echo $_qty; ?> <br>
								15 Days: <?php echo getLast15DaysItemSale($row['sku']); ?> <br>
								30 Days: <?php echo getLast30DaysItemSale($row['sku']);?>
							 </td>
							<?php } ?>
							
							<td class="req" align="center">
								<span><?php echo $row['req_qty']; ?></span><br>
								<?php if ($_SESSION[$perission] && $details['status'] == 'estimate' ) : ?>
									<input type="number" style="width: 50px;"  onchange="updateTotal(this, '<?php echo $key; ?>');" name="product[<?php echo $key; ?>][req_qty]"  value="<?php echo $row['req_qty']; ?>">
								<?php endif; ?>
							</td>
							<td <?php echo (!$_SESSION['v_po_price'])? 'style="display: none;"':''; ?> class="cost">
							
							<?php
							$cost_queries = $db->func_query("SELECT DISTINCT  DATE(cost_date) as cost_date,raw_cost,shipping_fee,ex_rate FROM inv_product_costs WHERE sku='".$row['sku']."' ORDER BY id DESC LIMIT 3");
							?>
							<span style="font-weight: bold">Landed: <?php echo '$'.number_format($cost_queries[0]['raw_cost']/$cost_queries[0]['ex_rate'], 2); ?></span><br>
								<?php if ($details['status']=='estimate') {
									
									$_i=0;
									foreach($cost_queries as $cost_query)
												{
													//echo $cost_query['raw_cost']; 
													echo ($_i==0?'':', ').$cost_query['raw_cost'];
													$_i++;
												 } 

								} else { ?>
								<span><?php echo '$'.number_format($row['new_cost']/$details['ex_rate'], 2); ?></span>
								<?php } ?>
								<?php
								if($details['status']!='estimate')
								{
									?>
									<input type="hidden"   onchange="" name="product[<?php echo $key; ?>][new_cost]"  value="<?php echo $row['new_cost']; ?>">
									<?php
								}

								?>


								
							</td>
							
							<?php
						if($details['status']=='estimate')
						{
							?>
							<td><?php echo '$'.number_format($row['avg_comp_price'],2);?></td>
								<td>
								<?php
								if($row['low_comp_url'])
								{


								?>
								<a href="<?php echo $row['low_comp_url'];?>" target="_blank">
								<?php
							}
							?>
								<?php echo '$'.number_format($row['low_comp_price'],2);?>
								<?php
								if($row['low_comp_url'])
								{


								?>
								<?php echo $comp_shortcodes[$row['low_comp_name']];?>
								<?php
							}
							?>
								<?php
								if($row['low_comp_url'])
								{


								?>
								</a>
								<?php
							}
							?>

								</td>
								
								<td>
								<?php
								$p = getOCItemPrice($p_id);
								$c = $row['new_cost']/$details['ex_rate'];
								// $c = ($cost_queries[0]['raw_cost'] + $cost_queries[0]['shipping_fee']) ;
								// echo $c;
								$numerator = $p-$c;
								//$p = ($row['new_cost']/$details['ex_rate']);
						//$c = $cost_queries[0]['raw_cost'] + $cost_queries[0]['shipping_fee']/$cost_queries[0]['ex_rate'];
						//$numerator = $p-$c;
						if(!$p)
						{
							$numerator = 0.00;
						}
						// echo $p;
								echo number_format(($numerator/$p)*100,2).'%';
								?>
								</td>
								
							<td>	<input type="text" style="width: 50px;"  onchange="" name="product[<?php echo $key; ?>][new_cost]"  value="<?php echo $row['new_cost']; ?>">

							<?php
							if($details['status']=='estimate')
						{
							?>

								<span><?php echo '$'.number_format($row['new_cost']/$details['ex_rate'], 2); ?></span>
								<?php
							}
							?>
							</td>

							<?php

						}
						?>

						<?php
						if($details['status']!='estimate')
						{
							?>

							<td class="shipped">
								<span><?php echo $row['qty_shipped']; ?></span><br>
								<input type="hidden" name="product[<?php echo $key; ?>][qty_shipped]" value="<?php echo $row['qty_shipped']; ?>">
								<?php if ($_SESSION['user_id'] == $details['vendor'] || $_SESSION[$perission]) : ?>
									<input type="number" style="width: 50px;" onchange="selectShipment('<?php echo $key; ?>')" name="product[<?php echo $key; ?>][toShip]" min="<?php echo ($row['needed'] > 0)? '1': '0'; ?>" value="<?php echo ($row['needed'] > 0)? $row['needed']: '0'; ?>">
								<?php endif; ?>
							</td>
							<td class="pkg_name">
								<span>
									<?php $unit_price = 0; ?>
									<?php if ($row['shipments']) : ?>
										<?php foreach ($row['shipments'] as $shipment) : ?>
											<?php echo  linkToShipment($shipment['shipment_id'], $host_path,   $shipment['package_number']. '('.(int)$shipment['qty_received'].'/'.$shipment['qty_shipped'] .')', ' target="_blank"')."<br>"; ?>
											<?php $total_shippingCost += $shipment['shipmentCost'] * $shipment['qty_shipped']; ?>
											<?php $unit_price += $shipment['price']; ?>
											<br>
										<?php endforeach; ?>
									<?php else: ?>
										N/A
									<?php endif; ?>
								</span>
							</td>
							
							<td class="shipmentAdded">
								<span><img src="<?php echo $host_path . 'images/' . (($row['shipment'] || $row['shipments'])? 'check.png' : 'cross.png' )?>" alt=""></span><br>
								<?php if (($_SESSION[$perission]) && !$row['qty_shipped']) : ?>
									
									<?php if($details['status'] =='estimate'){?>
									<span>
										<a href="javascript:void(0);" onclick="removeProduct(<?php echo $row['id']; ?>);">Remove</a>
									</span>
									<?php
								}
								?>
								<?php endif; ?>
							</td>
							<?php

						}
						?>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<?php
				if(1==2)
				{
				?>
				<tr>
					<th colspan="6">
						&nbsp;
					</th>
					<th colspan="6">
						Order Total
					</th>
					<td colspan="6">
						<?php echo number_format($total_lineCost * $details['ex_rate'], 2); ?>
					</td>
				</tr>
				<tr>
					<th colspan="6">
						&nbsp;
					</th>
					<th colspan="6">
						Shipped Total
					</th>
					<td colspan="6">
						<?php echo number_format($total_shippingCost * $details['ex_rate'], 2); ?>
					</td>
				</tr>
				<tr>
					<th colspan="6">
						&nbsp;
					</th>
					<th colspan="6">
						Total
					</th>
					<td colspan="6">
						<?php $gt = $total_lineCost + $total_shippingCost; ?>
						<?php echo number_format($gt * $details['ex_rate'], 2); ?>
					</td>
				</tr>

				<tr>
					<th colspan="6">
						&nbsp;
					</th>
					<th colspan="6">
						Ex Order Total
					</th>
					<td colspan="6">
						$<?php echo number_format($total_lineCost, 2); ?>
					</td>
				</tr>
				<tr>
					<th colspan="6">
						&nbsp;
					</th>
					<th colspan="6">
						Ex Shipped Total
					</th>
					<td colspan="6">
						$<?php echo number_format($total_shippingCost, 2); ?>
					</td>
				</tr>
				<tr>
					<th colspan="6">
						&nbsp;
					</th>
					<th colspan="6">
						Ex Total
					</th>
					<td colspan="6">
						$<?php echo number_format($gt, 2); ?>
					</td>
				</tr>
				<?php
				}
				?>				
				<tr>
				
					<td colspan="11" align="right">
						<?php if ($_SESSION['user_id'] == $details['vendor']) : ?>
							<input class="button" type="button" class="shipmentBtn" value="Add to Shipments" onclick="selectGetShipment();" />
						<?php endif; ?>
						
					</td>
				</tr>
			</table>
		</form>
		<?php 
		$new_items =  $db->func_query("SELECT b.unit_price,b.product_sku,b.product_name, a.package_number,b.shipment_id from inv_shipments a,inv_shipment_items b where a.id=b.shipment_id and a.vendor_po_id='".$vendor_po_id."' AND b.from_vpo = '0'");
		if ($new_items && (int)$_GET['vpo_id'] > 361) {
		?>
		<br><br>
		<br><br>
		<h2 style="font-size:14px">New Shipment Added Items</h2>
		<table align="center" border="1" width="80%" cellpadding="10" cellspacing="0" style="border:1px solid #ddd;border-collapse:collapse;">
			<thead>
				<tr>
					<th width="10%">
						SKU
					</th>
					<th width="30%">
						Item Name
					</th>
					<th width="15%">
						Cost (USD)
					</th>
					<th width="15%">
						Shipment Package
					</th>
					
				</tr>
			</thead>
			<tbody class="">
				<?php foreach ($new_items as $i => $row) : ?>
					<tr>
						<td>
							<?php echo linkToProduct($row['product_sku']); ?>
						</td>
						<td>
							<?php echo getItemName($row['product_sku']); ?>
						</td>
						<td>
							<?php echo '$'.number_format($unit_price,2); ?>
						</td>
					
						<td>
							<?php echo linkToShipment($row['shipment_id'], $host_path, $row['package_number'] , ' target="_blank"'); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php } ?>
		<?php if ($rj_breakdown) { ?>
		<br><br>
		<br><br>
		<h2 style="font-size:14px">RJ Breakdown</h2>
		<table align="center" border="1" width="80%" cellpadding="10" cellspacing="0" style="border:1px solid #ddd;border-collapse:collapse;">
			<thead>
				<tr>
					<th width="10%">
						SKU
					</th>
					<th width="30%">
						Item Name
					</th>
					<th width="15%">
						Cost (USD)
					</th>
					<th width="15%">
						Shipment Package
					</th>
					
				</tr>
			</thead>
			<tbody class="">
				<?php foreach ($rj_breakdown as $i => $row) : ?>
					<tr>
						<td>
							<?php echo linkToProduct($row['product_sku']); ?>
						</td>
						<td>
							<?php echo getItemName($row['product_sku']); ?>
						</td>
						<td>
							<?php echo '$'.number_format($row['cost']/$row['ex_rate'],2); ?>
						</td>
					
						<td>
							<?php echo linkToShipment($row['shipment_id'], $host_path, 'View', ' target="_blank"'); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php }?>


		<?php if ($shipments) { ?>
		<br><br>
		<br><br>
		<h2 style="font-size:14px">Shipments</h2>
		<table align="center" border="1" width="80%" cellpadding="10" cellspacing="0" style="border:1px solid #ddd;border-collapse:collapse;">
			<thead>
				<tr>
					<th width="10%">
						Shipment Number
					</th>
					<th width="15%">
						Status
					</th>
					<th width="15%">
						Completed
					</th>
					<th width="15%">
						Issued
					</th>
					<th width="15%" style="display: none;">
						Action
					</th>
				</tr>
			</thead>
			<tbody class="extraProducts">
				<?php foreach ($shipments as $i => $row) : ?>
					<tr>
						<td>
							<?php echo linkToShipment($row['id'],'',$row['package_number'],'target="_blank"'); ?>
						</td>
						<td>
							<?php echo $row['status']; ?>
						</td>
						<td>
							<?php echo americanDate($row['date_qc']); ?>
						</td>
						<td>
							<?php echo 	americanDate($row['date_issued']); ?>
						</td>
						<td style="display: none;">
							<?php echo linkToShipment($row['id'], $host_path, 'View', ' target="_blank"'); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php }?>
		<?php if ($extraItems) { ?>
		<br><br>
		<br><br>
		<h2 style="font-size:14px">Extra Items</h2>
		<table align="center" border="1" width="80%" cellpadding="10" cellspacing="0" style="border:1px solid #ddd;border-collapse:collapse;">
			<thead>
				<tr>
					<th width="10%">
						Image
					</th>
					<th width="15%">
						SKU
					</th>
					<th width="15%">
						Name
					</th>
					<th colspan="2" width="15%">
						Cost
					</th>
					<th width="10%">
						Recived
					</th>
					<th width="10%">
						Shipped
					</th>
					<th width="15%">
						Package
					</th>
				</tr>
			</thead>
			<tbody class="extraProducts">				
				<?php $total_lineCost = 0.00; ?>
				<?php $total_shippingCost = 0.00; ?>
				<?php foreach ($extraItems as $i => $row) : ?>
					<?php $total_lineCost += $row['unit_price'] * $row['qty_shipped']; ?>
					<?php $total_shippingCost += $row['qty_shipped'] * $row['shipmentCost']; ?>
					<tr>
						<td>
							<a class="fancybox2 fancybox.iframe" href="<?= noImage($row['image'], $host_path, $path,0); ?>" target="_blank">
								<img width="100%" src="<?= noImage($row['image'], $host_path, $path); ?>" alt="" />
							</a>
						</td>
						<td>
							<?php echo linkToProduct($row['product_sku'],'','target="_blank"'); ?>
						</td>
						<td>
							<?php echo $row['product_name']; ?>
						</td>
						<td>
							$<?php echo number_format($row['unit_price'], 2); ?>
						</td>
						<td>
							$<?php echo number_format($row['unit_price'] * $row['qty_shipped'], 2); ?>
						</td>
						<td>
							<?php echo (int)$row['qty_received']; ?>
						</td>
						<td>
							<?php echo $row['qty_shipped']; ?>
						</td>
						<td>
							<?php echo linkToShipment($row['shipment_id'], $host_path, $row['package_number'], ' target="_blank"'); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php }?>
		<br><br>
		<form method="post">
		
		</form>
		<br><br>
	</div>
	<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
	<script>
		var productNo = <?php echo $key++; ?>;

		function updateTotal (t, no) {
			var container = $('.product-' + no);
			var costC = container.find('.cost');
			var line_costC = container.find('.line_cost');
			var shippedC = container.find('.shipped');
			var needC = container.find('.need');
			var qty = $(t).val();
			var cost = costC.find('input').val();
			var shipped = shippedC.find('span').text();
			
			if (!shipped) {
				shipped = 0;
			}
			var lineCost = 0;

			if (qty && cost) {
				lineCost = cost * qty;
			}

			line_costC.find('span').text('$' + lineCost.toFixed(2));
			line_costC.find('input').val(lineCost.toFixed(2));

			needC.find('span').text( qty - shipped );
			needC.find('input').val( qty - shipped );
		}

		function selectShipment (no) {
			var container = $('.product-' + no);
			var selectx = container.find('input[type=checkbox]');
			if (selectx.prop('checked') == false) {
				selectx.prop('checked', true)
			}
		}
		$(document).on('click','#generate_link',function(e){


			$.ajax({
				url: '<?php echo $pageViewLink; ?>',
				type: 'POST',
				dataType: 'json',
				data: {action:'generate_link'}
			})
			.always(function(json) {
				if (json['success']) {
					//window.location.reload();
					$('.short_url').html(json['success']);
				}
				if (json['error']) {
					alert(json['error']);
				}
			});
			return false;

		});
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
		function changeVendor(obj) {
			if(!confirm('Are you sure want to update Vendor?'))
		{
			return false;
		}
		var vendor = $(obj).val();
		$.ajax({
			url: '<?php echo $pageViewLink; ?>',
			type: 'post',
			data: {action: 'changeVendor',vendor:vendor},
			beforeSend: function () {
			},
			complete: function () {
			},
			success: function (data) {
				alert('Vendor Updated');
			}
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
			$('form').append('<input type="hidden" name="action" value="addShipment" />');
			$('form').append('<input type="hidden" name="shipment_id" value="'+ $('#vendor_shipment_id').val() +'" />');
			dataSend = $('form').serialize();
			$.ajax({
				url: '<?php echo $pageViewLink; ?>',
				type: 'POST',
				dataType: 'json',
				data: dataSend
			})
			.always(function() {
				$('.shipmentBtn').removeAttr('disabled');
				window.location.reload();
			});

		}

		function removeProduct (id) {
			if (!confirm('Do you want to delete selected item?')) {
				return false;
			}
			var sku = $('#hidden_sku'+id).val();
			$.ajax({
				url: '<?php echo $pageViewLink; ?>',
				type: 'POST',
				dataType: 'json',
				data: {id: id,sku: sku, action: 'removeProduct'}
			})
			.always(function(json) {
				if (json['success']) {
					window.location.reload();
				}
				if (json['error']) {
					alert(json['error']);
				}
			});
			
		}

		function loadVendorCredit()
		{
			var vendor_id = '<?php echo $details['vendor'];?>';
			$.ajax({
				url: '<?php echo $pageViewLink; ?>',
				type: 'POST',
				dataType: 'json',
				data: {vendor_po_id:'<?php echo $details['id'];?>',vendor_id: vendor_id, action: 'load_vendor_credit'}
			})
			.always(function(json) {
				if (json['success']) {
					$('#vendor_credit_span').html(json['amount'].toFixed(2));
					if(json['button_visible']==1)
					{
					$('#apply_vendor_credit').show();

					}
					else
					{
							
							$('#apply_vendor_credit').hide();
					}
					$('#vendor_credit_applied').html(json['applied_credits'].toFixed(2))

				}
				if (json['error']) {
					alert(json['error']);
				}
			});
		}

		function applyVendorCredit()
		{
			if(!confirm('Are you want to sure to apply vendor credit?'))
			{
				return false;
			}
			var vendor_id = '<?php echo $details['vendor'];?>';
			$.ajax({
				url: '<?php echo $pageViewLink; ?>',
				type: 'POST',
				dataType: 'json',
				data: {vendor_po_id:'<?php echo $details['id'];?>',vendor_id: vendor_id, action: 'apply_vendor_credit'}
			})
			.always(function(json) {
				// loadVendorCredit();
				location.reload(true);
				return false;
				
			});
			return false;
		}


		$(document).on('click','.update_btn',function(e)
		{

			if($('select[name=status]').val()=='issued' && parseFloat($('input[name="ex_rate"]').val())==0.00)
			{
				alert('Please provide exchange rate in order to issue this Purchase order');
				return false;
			}

			$('#vendorForm').submit();
		});

		$(document).on('click','.delete_selection',function(e)
		{
				if(!confirm('Are you sure want to delete the items?'))
				{
					return false;
				}

			var sList = "";
$('td.select input[type=checkbox]:checked').each(function () {
    sList +=  $(this).val() + ",";
    window.location='vendor_po_view.php?action=delete_selection&list='+sList+'&vpo_id=<?php echo $_GET['vpo_id'];?>';
});


			// $('#vendorForm').submit();
		});
		function changeStatus(status)
		{
			$('select[name=status]').val(status);
			$('.update_btn').trigger('click');
		}
	</script>
	 <script>

		 $(document).ready(function(e) {

             $(".tablesorter").tablesorter(); 
             loadVendorCredit();

        });

		 </script>
</body>
</html>