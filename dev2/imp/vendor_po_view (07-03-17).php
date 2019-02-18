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
//VPO Details
$vpo_id = (int) $_GET['vpo_id'];
$comments =$db-> func_query("SELECT * FROM inv_vendor_po_comments ");
$details = $db->func_query_first("SELECT * FROM $table WHERE id = '$vpo_id'");
$vendor_po_id = $details['vendor_po_id'];
$items = $db->func_query("SELECT a.*, b.`image`, b.`product_id`,b.quantity FROM `inv_vendor_po_items` a, `oc_product` b WHERE a.`sku` = b.`model` AND vendor_po_id = '$vendor_po_id' ORDER BY a.`sku` ASC");
$shipments = $db->func_query('SELECT * FROM `inv_shipments` WHERE `vendor_po_id` = "'. $vendor_po_id .'"');

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
					'price' => $sItem['unit_price'] / $details['ex_rate']
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

foreach ($items as $key => $item) {
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
	'issued' => 'Issued',
	'completed' => 'Completed'
	);

if ($_POST['action'] == 'removeProduct') {
	$item = $items[array_search($_POST['id'], array_column($items, 'id'))];
	if ($item['qty_shipped'] == 0) {
		$db->func_query("DELETE FROM `inv_vendor_po_items` WHERE id = '" . $_POST['id'] . "'");
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
			$log = 'Shipment #: ' . linkToShipment($shipment['id'], $host_path, $shipment['package_number']) . ' is Updated From '. linkToVPO($details['id'], $host_path, $vendor_po_id);
			if (!isset($shipment['id'])) {
				$shipment = array(
					'package_number' => 'vpo' . rand(),
					'status' => 'Pending',
					'vendor' => $details['vendor'],
					'ex_rate' => $details['ex_rate'],
					'vendor_po_id' => $vendor_po_id,
					'date_added' => date('Y-m-d H:i:s')
					);
				$shipment['id'] = $db->func_array2insert("inv_shipments", $shipment);
				$log = 'Shipment #: ' . linkToShipment($shipment['id'], $host_path, $shipment['package_number']) . ' is Created From '. linkToVPO($details['id'], $host_path, $vendor_po_id);

			}

			$shipment_item = $db->func_query_first('SELECT * FROM `inv_shipment_items` WHERE `shipment_id` = "'. $shipment['id'] .'" AND product_sku = "'. $item['sku'] .'" ');
			if ($shipment_item) {
				$db->db_exec('UPDATE inv_shipment_items SET qty_shipped = (qty_shipped + '. $item['toShip'] .') WHERE id = "'. $shipment_item['id'] .'"');
				$plog .= "<br><br> Product " . linkToProduct($item['sku']) . ' Updated';
				$plog .= "<br> Shipped " . $item['toShip'] . " items Added";
				$shipping_item_id = $shipment_item['id'];
			} else {
				$array = array(
					'shipment_id'	=> $shipment['id'],
					'product_id'	=> $item['product_id'],
					'product_name'	=> $item['name'],
					'product_sku'	=> $item['sku'],
					'qty_shipped'	=> $item['toShip'],
					'cu_po'			=> $item['order_id']
					);
				$shipping_item_id = $db->func_array2insert("inv_shipment_items", $array);
				$plog .= "<br><br> Product " . linkToProduct($item['sku']) . ' Added';
				$plog .= "<br> Shipped " . $item['toShip'] . " items Added";
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
	actionLog($log . $plog);
	exit;
}
if($_POST['addcomment']) {
	
	$_SESSION['message'] = addComment('vendor_po',array('id' => $vpo_id, 'comment' => $_POST['comment']));
	header("Location:" . $pageViewLink);
	exit;
}
if ($_POST['update']) {
	$log = '';

	if ($_POST['status'] != $details['status']) {

		$log .= '<br>Status: ' . $details['status'] . ' to ' . $_POST['status'];

		$array = array(
			'status' => $_POST['status'],
			'date_updated' => date('Y-m-d H:i:s')
			);
	}

	if ($_POST['ex_rate'] != $details['ex_rate']) {
		$log .= '<br>Ex Rate: ' . $details['ex_rate'] . ' to ' . $_POST['ex_rate'];
		$array['ex_rate'] = (float) $_POST['ex_rate'];
		$array['date_updated'] = date('Y-m-d H:i:s');
	}

	if ($array) {
		$db->func_array2update("inv_vendor_po", $array, "id = '$vpo_id'");
	}
	if (($_SESSION[$perission])) {
		foreach ($_POST['product'] as $i => $product) {
			$id = (int) $product['id'];
			unset($product['id'], $product['toShip']);
			if ($product['req_qty'] != $items[$i]['req_qty'] || $product['reference'] != $items[$i]['reference']) {
				$log .= '<br><br>Product: ' . linkToProduct($product['sku']) . ' Updated';
				$log .= '<br>Quantity: ' . $items[$i]['req_qty'] . ' to ' . $product['req_qty'];
				$log .= '<br>Quantity: ' . $items[$i]['needed'] . ' to ' . $product['needed'];
				$product['date_updated'] = date('Y-m-d H:i:s');
				$product['shipment'] = 0;
				$product['reference'] = $product['reference'];
				$db->func_array2update("inv_vendor_po_items", $product, "id = '$id'");
			}
		}
	}

	if ($log) {
		$log = 'Vendor PO '. linkToVPO($details['id'], $host_path, $vendor_po_id, ' target="_blank" ') . ' is Updated' . $log;
		actionLog($log);
	}

	$_SESSION['message'] = $pageName . ' Updated';

	header("Location:" . $pageViewLink);
	exit;
}

if ($details['vendor'] != $_SESSION['user_id'] && $_SESSION['login_as'] != 'admin' && !$_SESSION['vendor_po_page']){
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<script>
		function allowFloat (t) {
			var input = $(t).val();
			var valid = input.substring(0, input.length - 1);
			if (isNaN(input) || input == ' ') {
				$(t).val(valid);
			}
		}order
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
		<form action="" method="post" enctype="multipart/form-data">
			<h2><?= $pageName; ?></h2><a target="_blank" href="export_vendor_csv.php?vpo_id=<?=$vpo_id;?>">Download CSV</a><br>
			<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td colspan="2">
						Status:
					</td>
					<td colspan="2">
						<select name="status">
							<?php foreach ($statuses as $i => $row) : ?>
								<option <?php echo ($details['status'] == $i)? 'selected="selected"': '';?> value="<?php echo $i; ?>"><?php echo ucfirst($row); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						Vendor: 
					</td>
					<td>
						<?php echo get_username($details['vendor']); ?>
					</td>
					<td>
						Vendor PO ID: 
					</td>
					<td>
						<?php echo $details['vendor_po_id']; ?>
					</td>
				</tr>
				<tr>
					<td>
						Date Added: 
					</td>
					<td>
						<?php echo americanDate($details['date_added']); ?>
					</td>
					<td>
						Date Updated: 
					</td>
					<td>
						<?php echo americanDate($details['date_updated']); ?>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						Exchange Rate: 
					</td>
					<td colspan="2">
						<input type="text" name="ex_rate" value="<?php echo $details['ex_rate']; ?>" placeholder="Exchange Rate">
					</td>
				</tr>
			</table>
			<br><br>
			<?php
			$image_path = getVendorPic($details['vendor']);

			?>
			<img src="<?php echo $image_path;?>" height="100" width="100">
			<table align="center" width="80%" cellpadding="10" cellspacing="0" style="border:0px solid #585858;border-collapse:collapse;">
				<tr>
					<td align="left">
						<?php if ($_SESSION[$perission] || $_SESSION['user_id'] == $details['vendor']) : ?>
							<input class="button" type="submit" name="update" value="Update" />
						<?php endif; ?>
					</td>
					<td align="right">
						<?php if ($_SESSION['user_id'] == $details['vendor'] || $_SESSION[$perission]) : ?>
							<input class="button" type="button" class="shipmentBtn" value="Add to Shipment" onclick="selectGetShipment();" />
						<?php endif; ?>
						<a class="button" style="margin-left:10px;" href="<?= $pageLink; ?>">Back</a>
					</td>
				</tr>
			</table>

			<table align="center" border="1" width="80%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<thead>
					<tr>
						<?php if ($_SESSION['user_id'] == $details['vendor'] || $_SESSION[$perission]) : ?>
							<th width="4%">
								<input type="checkbox" data-class="select" onclick="selectAllCheck(this);">
							</th>
						<?php endif; ?>
						<th width="10%">
							Image
						</th>
						<th width="8%">
							SKU
						</th>
						<th width="15%">
							Name
						</th>
						<th>
						added by orders
						</th>
						<th width="10%">
							Requested
						</th>
						<th <?php echo (!$_SESSION['v_po_price'])? 'style="display: none;"':''; ?> colspan="2" width="11%">
							Previous Cost
						</th>
						<th width="15%">
							Shipped / Add to Shipment
						</th>
						<th width="15%">
							Package
						</th>
						<th <?php echo (!$_SESSION['v_po_price'])? 'style="display: none;"':''; ?> colspan="2" width="9%">
							New Cost
						</th>
						<th width="4%">
							Needed
						</th>
						<th width="4%">
							Extra
						</th>
						<th width="4%">
							Reference
						</th>
						<th width="6%">
							Shipment Added
						</th>
					</tr>
				</thead>
				<tbody class="products">
					<?php $total_lineCost = 0.00; ?>
					<?php $total_shippingCost = 0.00; ?>
					<?php foreach ($items as $key => $row) : ?>
						<?php
						$_qty = $db->func_query_first_cell("SELECT quantity from oc_product where sku='".$row['sku']."'");
						?>
						<?php //$total_lineCost += $row['cost'] * $row['req_qty']; ?>
						<tr class="product-<?php echo $key; ?> pr-<?php echo $row['id']; ?>" <?php if($_qty<=0) { echo 'style="background-color:#ffb2b2"';}?>>
							<?php if ($_SESSION['user_id'] == $details['vendor'] || $_SESSION[$perission]) : ?>
								<td class="select">
									<input type="checkbox" name="product[<?php echo $key; ?>][update]" value="<?php echo $row['id']; ?>">
								</td>
							<?php endif; ?>
							<td class="image">
								<a class="fancybox2 fancybox.iframe" href="<?= noImage($row['image'], $host_path, $path); ?>" target="_blank">
									<img width="100%" src="<?= noImage($row['image'], $host_path, $path); ?>" alt="" />
								</a>
							</td>
							<td class="sku">
								<span><?php echo linkToProduct($row['sku'], $host_path); ?></span>
								<input type="hidden" name="product[<?php echo $key; ?>][id]" value="<?php echo $row['id']; ?>" placeholder="Enter SKU">
								<input type="hidden" name="product[<?php echo $key; ?>][sku]" value="<?php echo $row['sku']; ?>" placeholder="Enter SKU">
							</td>
							<td class="name">
								<span><?php echo $row['name']; ?></span>
							</td>
							<td> <a href="viewOrderDetail.php?order=<?php echo $row['order_id']?>"><?php echo $row['order_id'];?></a> </td>
							<td class="req">
								<span><?php echo $row['req_qty']; ?></span>
								<?php if ($_SESSION[$perission]) : ?>
									<input type="number" style="width: 50px;"  onchange="updateTotal(this, '<?php echo $key; ?>');" name="product[<?php echo $key; ?>][req_qty]"  value="<?php echo $row['req_qty']; ?>">
								<?php endif; ?>
							</td>
							<td <?php echo (!$_SESSION['v_po_price'])? 'style="display: none;"':''; ?> class="cost">
								<span><?php echo number_format($row['cost'], 2); ?></span>
								<input type="hidden" value="<?php echo $row['cost']; ?>">
							</td>
							<td <?php echo (!$_SESSION['v_po_price'])? 'style="display: none;"':''; ?> class="line_cost">
								<span><?php echo number_format($row['cost'] * $row['req_qty'], 2); ?></span>
							</td>
							<td class="shipped">
								<span><?php echo $row['qty_shipped']; ?></span>
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
											<?php echo  linkToShipment($shipment['shipment_id'], $host_path, $shipment['qty_shipped'] . '-' . $shipment['package_number'], ' target="_blank"'); ?>
											<?php $total_shippingCost += $shipment['shipmentCost'] * $shipment['qty_shipped']; ?>
											<?php $unit_price += $shipment['price']; ?>
											<br>
										<?php endforeach; ?>
									<?php else: ?>
										N/A
									<?php endif; ?>
								</span>
							</td>
							<td <?php echo (!$_SESSION['v_po_price'])? 'style="display: none;"':''; ?> class="cost">
								<span>$<?php echo number_format($unit_price / count($row['shipments']), 2); ?></span>
							</td>
							<td <?php echo (!$_SESSION['v_po_price'])? 'style="display: none;"':''; ?> class="line_cost">
								<?php $tlineCost = (($unit_price / count($row['shipments'])) * $row['req_qty']); ?>
								<?php $total_lineCost += $tlineCost; ?>
								<span>$<?php echo number_format($tlineCost, 2); ?></span>
							</td>
							<td class="need">
								<span><?php echo ($row['needed'] > 0)? $row['needed']: '0'; ?></span>
								<?php if ($_SESSION[$perission]) : ?>
									<input type="hidden" name="product[<?php echo $key; ?>][needed]" value="<?php echo $row['needed']; ?>">
								<?php endif; ?>
							</td>
							<td class="need">
								<span><?php echo (int)$row['extra']; ?></span>
							</td>
							<td>
								<input type="text" name="product[<?php echo $key; ?>][reference]" value="<?php echo $row['reference']; ?>">
							</td>
							<td class="shipmentAdded">
								<span><img src="<?php echo $host_path . 'images/' . (($row['shipment'])? 'check.png' : 'cross.png' )?>" alt=""></span>
								<?php if (($_SESSION[$perission]) && !$row['qty_shipped']) : ?>
									<span>
										<a href="javascript:void(0);" onclick="removeProduct(<?php echo $row['id']; ?>);">Remove</a>
									</span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
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
				<tr>
					<td colspan="6">
						<?php if ($_SESSION[$perission] || $_SESSION['user_id'] == $details['vendor']) : ?>
							<input class="button" type="submit" name="update" value="Update" />
						<?php endif; ?>
					</td>
					<td colspan="11" align="right">
						<?php if ($_SESSION['user_id'] == $details['vendor']) : ?>
							<input class="button" type="button" class="shipmentBtn" value="Add to Shipments" onclick="selectGetShipment();" />
						<?php endif; ?>
						<a class="button" style="margin-left:10px;" href="<?= $pageLink; ?>">Back</a>
					</td>
				</tr>
			</table>
		</form>
		<?php if ($shipments) { ?>
		<br><br>
		<br><br>
		<h2>Shipments</h2>
		<table align="center" border="1" width="80%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
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
					<th width="15%">
						Action
					</th>
				</tr>
			</thead>
			<tbody class="extraProducts">
				<?php foreach ($shipments as $i => $row) : ?>
					<tr>
						<td>
							<?php echo $row['package_number']; ?>
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
						<td>
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
		<h2>Extra Items</h2>
		<table align="center" border="1" width="80%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
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
							<a class="fancybox2 fancybox.iframe" href="<?= noImage($row['image'], $host_path, $path); ?>" target="_blank">
								<img width="100%" src="<?= noImage($row['image'], $host_path, $path); ?>" alt="" />
							</a>
						</td>
						<td>
							<?php echo $row['product_sku']; ?>
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
		<table width="80%">
					<tr>
						<td>
							<table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
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
							<table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse; vertical-align:top">
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
		</form>
		<br><br>
	</div>
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
			if (!confirm('Do you want to delete selected item!!')) {
				return false;
			}
			$.ajax({
				url: '<?php echo $pageViewLink; ?>',
				type: 'POST',
				dataType: 'json',
				data: {id: id, action: 'removeProduct'}
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
	</script>
</body>
</html>