<?php
require_once("auth.php");
require_once("inc/functions.php");
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = 'vendor_po';
$pageName = 'Vendor PO';
$pageLink = 'vendor_po.php';
$pageCreateLink = 'vendor_po_create.php';
$pageSetting = false;
$table = '`inv_vendor_po`';
$statuses = array(
	'pending' => 'Pending',
	'awaiting' => 'Awaiting',
	'received' => 'Received',
	'complete' => 'Complete'
	);
if (!$_SESSION[$perission] && !$_SESSION['user_id'] == 0) {
	exit;
}

if ($_SESSION['list']) {
	$product_ids = implode(",",array_keys($_SESSION['list']));

	$inv_query   = "select p.product_id , p.model, p.quantity, p.status, p.mps , p.image , pd.name from 
	oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id) 
	where p.product_id in ($product_ids)";

	$products = $db->func_query($inv_query);
}

if ($_POST['action'] == 'getVPO') {
	$vpos = $db->func_query("SELECT * FROM $table WHERE vendor = '" . (int)$_POST['vendor'] . "' ORDER by id DESC");
	$json['success'] = 1;
	$json['data'] = '<option value="">---Select---</option>';
	if ($vpos) {

		foreach ($vpos as $key => $row) {
			$json['data'] .= '<option value="' . $row['vendor_po_id'] . '">' . $row['vendor_po_id'] . '</option>';
		}

	} else {
		$json['data'] = '<option value="">No po found</option>';
	}

	echo json_encode($json);
	exit;
}

if ($_POST['action'] == 'getProduct') {
	//print_r($_POST);exit;
	$details = $db->func_query_first('SELECT * FROM oc_product a, oc_product_description b WHERE a.`product_id` = b.`product_id` AND (a.`main_sku` = "" OR a.`main_sku` IS NULL) AND model = "'. rtrim($_POST['sku']) .'"');
	
	$shipment = 0;
	if ($_POST['vpo']) {
		$shipment = $db->func_query_first_cell('SELECT SUM(`qty_shipped`) FROM `inv_shipment_items` a, `inv_shipments` b WHERE a.`shipment_id` = b.`id` AND b.`vendor_po_id` = "'. $_POST['vpo'] .'" AND a.`product_sku` = "'. rtrim($_POST['sku']) .'"');
	}

	$price = $db->func_query_first('SELECT * FROM `inv_product_costs` WHERE sku = "'. rtrim($_POST['sku']) .'" ORDER BY cost_date DESC');

	if ($details) {
		$json['success'] = 1;
		$json['name'] = $details['name'];

		$json['shipped'] = ($shipment)? $shipment: 0;
		// $json['pkg_name'] = ($shipment['shipment_id'])? linkToShipment($shipment['shipment_id'], $host_path, $shipment['qty_shipped'] . ' - ' . $shipment['package_number'], ' target="_blank" '): '' ;
		// $json['shipment_tracking'] = ($shipment['package_number'])? $shipment['package_number'] : '';
		// $json['shipment_id'] = ($shipment['shipment_id'])? $shipment['shipment_id'] : '';
		// $json['shipping_item_id'] = ($shipment['p_id'])? $shipment['p_id'] : '';

		$json['cost'] = ($price['raw_cost'])? $price['raw_cost'] : 0.00;
		$json['shipping_cost'] = ($price['shipping_fee'])? $price['shipping_fee'] : 0.00;
	} else {
		$json['error'] = 1;
		$json['name'] = 'No Product Found';
	}

	echo json_encode($json);
	exit;
}

if ($_POST['add']) {
	foreach ($_POST['product'] as $i => $product) {
		if (!$product['sku'] || !$product['name'] || !$product['needed']) {
			unset($_POST['product'][$i]);
		}
	}
	if (!$_POST['product']) {

		$_SESSION['message'] = 'No Products Found';
		header("Location:" . $pageCreateLink);
		exit;
	}
	if (!$_POST['vendor_po_id']) {
		$vendor_id = $db->func_query_first_cell('SELECT MAX(id) FROM `inv_vendor_po`');
		$vendor_po_id = ($vendor_id)? 'PO' . (20001 + $vendor_id) : 'PO' . 20001;
		$array = array(
			'vendor_po_id' => $vendor_po_id,
			'vendor' => $_POST['vendor'],
			'status' => $_POST['status'],
			'date_added' => date('Y-m-d H:i:s'),
			'date_updated' => date('Y-m-d H:i:s')
			);
		$log = 'Vendor PO '. linkToVPO($db->func_query_first_cell('SELECT id FROM `inv_vendor_po` WHERE vendor_po_id = "' . $vendor_po_id . '"'), $host_path, $vendor_po_id, ' target="_blank" ') .' is Created';
		$db->func_array2insert("inv_vendor_po", $array);
	} else {
		$log = 'Vendor PO '. linkToVPO($db->func_query_first_cell('SELECT id FROM `inv_vendor_po` WHERE vendor_po_id = "' . $vendor_po_id . '"'), $host_path, $vendor_po_id, ' target="_blank" ') .' is Merged';
		$vendor_po_id = $_POST['vendor_po_id'];
	}

	foreach ($_POST['product'] as $i => $product) {
		$product['vendor_po_id'] = $vendor_po_id;
		$product['date_added'] = date('Y-m-d H:i:s');
		$product['date_updated'] = date('Y-m-d H:i:s');
		$product['sku'] = rtrim($product['sku']);
		$pro = $db->func_query_first_cell('SELECT * FROM `inv_vendor_po_items` WHERE sku = "'. $product['sku'] .'" AND vendor_po_id = "'. $vendor_po_id .'"');
		$p_id = $pro['id'];
		if ($p_id) {
			$log .= '<br><br>Product: ' . linkToProduct($product['sku']) . ' Merged';
			if ($pro['req_qty'] != $product['req_qty']) {
				$log .= '<br>Quantity: ' . $pro['req_qty'] . ' to ' . $product['req_qty'];
				$log .= '<br>Needed: ' . $pro['needed'] . ' to ' . $product['needed'];
				$product['shipment'] = 0;
				$db->func_array2update("inv_vendor_po_items", $product, "id = '$p_id'");
			}
		} else {
			$log .= '<br><br>Product: ' . linkToProduct($product['sku']) . ' Added';
			$log .= '<br>Quantity: ' . $product['req_qty'];
			$log .= '<br>Needed: ' . $product['needed'];
			$db->func_array2insert("inv_vendor_po_items", $product);
		}
	}

	actionLog($log);
	unset($_SESSION['list']);
	$_SESSION['message'] = $pageName . ((!$_POST['vendor_po_id'])? ' Created': ' Merged');

	header("Location:" . $pageLink);
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
		}
		function checkWhiteSpace (t) {
			if ($(t).val() == ' ') {
				$(t).val('');
			}
		}

		function getAmount (t) {
			var order_id = $(t).val();
			var main = $(t).parent();
			if (order_id != '') {
				$.ajax({
					url: '<?= $pageCreateLink; ?>',
					type: 'POST',
					dataType: 'json',
					data: {'order': order_id, 'order': order_id, 'submit': 'getAmount'},
					success: function(json){
						if (json['success']) {
							$('input[name="amount"]').val(json['amount']);
						}
						if (json['error']) {
							main.find('.error').remove();
							main.append('<span class="error">'+ json['msg'] +'</span>');
						}
					}
				});

			}
		}

	</script>

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
		<form action="" onsubmit="if ($('select[name=vendor]').val() == ''){ alert('Please Select Vendor'); return false;}" method="post" enctype="multipart/form-data">
			<h2>Add <?= $pageName; ?></h2>
			<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td>Vendor</td>
					<td>
						<select name="vendor" onchange="getVPO(this);">
							<option value="">---Select---</option>
							<?php foreach ($db->func_query('SELECT * FROM `inv_users` WHERE group_id = 1') as $i => $vendor) : ?>
								<option value="<?php echo $vendor['id']; ?>"><?php echo ucfirst($vendor['name']); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr class="status">
					<td>Status</td>
					<td>
						<select name="status">
							<?php foreach ($statuses as $key => $row) : ?>
								<option value="<?php echo $key; ?>"><?php echo ucfirst($row); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>

				<tr>
					<td>Merge PO <input type="checkbox" onchange="mergePO(this);"></td>
					<td>
						<select name="vendor_po_id" id="vpo" onchange="loadAllProducts()" style="display: none;">
							<option value="">---Select---</option>
						</select>
					</td>
				</tr>
			</table>
			<br><br>
			<table align="center" border="1" width="80%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<thead>
					<tr>
						<th>
							SKU
						</th>
						<th>
							Name
						</th>
						<th>
							Requested
						</th>
						<th colspan="2">
							Cost
						</th>
						<th>
							Shipped
						</th>
						<th>
							Needed
						</th>
						<th>
							Action (<a href="javascript:void(0);" onclick="addProduct('add', this);">Add New</a>)
						</th>
					</tr>
				</thead>
				<tbody class="products">
					<?php $i = 0; ?>
					<?php $iEnd = (count($products))? count($products): 5; ?>
					<?php while ( $i < $iEnd) : ?>
						<?php $price = $db->func_query_first('SELECT * FROM `inv_product_costs` WHERE sku = "'. rtrim($products[$i]['model']) .'" ORDER BY cost_date DESC'); ?>
						<tr class="product-<?php echo $i; ?>">
							<td class="sku">
								<input type="text" name="product[<?php echo $i; ?>][sku]" onkeyup="getProduct('<?php echo $i; ?>')" value="<?php echo $products[$i]['model']; ?>" placeholder="Enter SKU">
							</td>
							<td class="name">
								<span><?php echo $products[$i]['name']; ?></span>
								<input type="hidden" name="product[<?php echo $i; ?>][name]" value="<?php echo $products[$i]['name']; ?>">
							</td>
							<td class="req">
								<input type="number" onchange="updateTotal(this, '<?php echo $i; ?>');" name="product[<?php echo $i; ?>][req_qty]" min="1" value="1">
							</td>
							<td class="cost">
								<span>$<?php echo ($price['raw_cost'])? $price['raw_cost'] : 0.00; ?></span>
								<input type="hidden" name="product[<?php echo $i; ?>][cost]" value="<?php echo ($price['raw_cost'])? $price['raw_cost'] : 0.00; ?>">
							</td>
							<td class="line_cost">
								<span>$<?php echo ($price['raw_cost'])? $price['raw_cost'] : 0.00; ?></span>
								<input type="hidden" value="<?php echo ($price['raw_cost'])? $price['raw_cost'] : 0.00; ?>">
							</td>
							<td class="shipped">
								<span></span>
								<input type="hidden" name="product[<?php echo $i; ?>][qty_shipped]" value="0">
							</td>
							<td class="need">
								<span>1</span>
								<input type="hidden" name="product[<?php echo $i; ?>][needed]" value="1">
							</td>
							<td class="action">
								<a href="javascript:void(0);" onclick="addProduct('remove', this);">Remove</a>
							</td>
						</tr>
						<?php $i++; ?>
					<?php endwhile; ?>
				</tbody>
				<tr>
					<td colspan="9">
						<input class="button" type="submit" name="add" value="Submit" />
						<a class="button" style="margin-left:10px;" href="<?= $pageLink; ?>">Back</a>
					</td>
				</tr>
			</table>
			<textarea id="templete" style="display: none;">
				<tr class="product-0">
					<td class="sku">
						<input type="text" name="product[0][sku]" onkeyup="getProduct('0')" value="" placeholder="Enter SKU">
					</td>
					<td class="name">
						<span></span>
						<input type="hidden" name="product[0][name]" value="">
					</td>
					<td class="req">
						<input type="number" onchange="updateTotal(this, '0');" name="product[0][req_qty]" min="1" value="1">
					</td>
					<td class="cost">
						<span></span>
						<input type="hidden" name="product[0][cost]" value="">
					</td>
					<td class="line_cost">
						<span></span>
						<input type="hidden" value="">
					</td>
					<td class="shipped">
						<span></span>
						<input type="hidden" name="product[0][qty_shipped]" value="0">
					</td>
					<td class="need">
						<span>1</span>
						<input type="hidden" name="product[0][needed]" value="1">
					</td>
					<td class="action">
						<a href="javascript:void(0);" onclick="addProduct('remove', this);">Remove</a>
					</td>
				</tr>
			</textarea>
		</form>
	</div>
	<script>
		var productNo = <?= $i; ?>;
		function getVPO (t) {
			var vendor = $(t).val();
			$.ajax({
				url: 'vendor_po_create.php',
				type: 'POST',
				dataType: 'json',
				data: {vendor: vendor, action: 'getVPO'},
			})
			.always(function(json) {
				if (json['success']) {
					$('#vpo').html(json['data']);
				}
			});
		}

		function loadAllProducts () {
			$('.products tr').each(function() {
				var t = $(this).find('.sku').find('input');
				if ($(t).val()) {
					var classN = $(this).attr('class');
					var no = classN.split('-')[1];
					getProduct(no);
				}
			});
		}

		function mergePO (t) {
			if ($(t).is(':checked')) {
				$('#vpo').show();
				$('.status').hide();
			} else {
				$('#vpo').hide();
				$('#vpo').val('');
				$('.status').show();
			}
		}
		function replaceAll(str, find, replace) {
			return str.replace(new RegExp(find, 'g'), replace);
		}

		function addProduct (action, t) {
			var container = $('.products');

			if (action == 'remove') {
				$(t).parent().parent().remove();
			}
			if (action == 'add') {
				var temp = $('#templete').text();
				temp = temp.replace("product-0", "product-" + productNo);
				temp = temp.replace(/getProduct\('0'\)/g, "getProduct('" + productNo + "')");
				temp = temp.replace(/updateTotal\(this, '0'\)/g, "updateTotal(this, '" + productNo + "')");
				temp = temp.replace(/product\[0\]/g, "product[" + productNo + "]");
				container.append(temp);
				productNo++;
			}
		}

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

		function getProduct (no) {
			var error = 0;
			var container = $('.product-' + no);
			var t = container.find('.sku').find('input');
			var sku = $(t).val();
			var nameC = container.find('.name');
			var reqC = container.find('.req');
			var costC = container.find('.cost');
			var line_costC = container.find('.line_cost');
			var shippedC = container.find('.shipped');
			// var pkg_nameC = container.find('.pkg_name');
			var needC = container.find('.need');

			$('.products tr').each(function() {
				var th = $(this).find('.sku').find('input');
				if ($(th).val() == sku && $(this).attr('class') != 'product-' + no) {
					nameC.find('span').text('Product Already Exist');
					error = 1;
				}
			});
			if (error) {
				return false;
			}

			$('input[type=submit]').attr('disabled', 'disabled');
			var vendor = $('select[name=vendor]').val()
			if (!vendor) {
				$('select[name=vendor]').css({
					border: 'red solid 1px'
				});
				return false;
			}

			$.ajax({
				url: 'vendor_po_createT.php',
				type: 'POST',
				dataType: 'json',
				data: {sku: sku, vendor: vendor, vpo: $('#vpo').val(), action: 'getProduct'},
			}).always(function(json) {
				if (json['success']) {
					console.log(nameC);
					nameC.find('span').text(json['name']);
					nameC.find('input').val(json['name']);
					costC.find('span').text('$'+json['cost']);
					costC.find('input').val(json['cost']);
					reqC.find('input').val(json['shipped']).attr('min', json['shipped']);;

					var rQty = reqC.find('input').val();
					var lineCost = 0;
					if (rQty) {
						lineCost = json['cost'] * rQty;
					}

					line_costC.find('span').text('$' + lineCost.toFixed(2));
					line_costC.find('input').val(lineCost.toFixed(2));
					shippedC.find('span').text(json['shipped']);
					shippedC.find('input').val(json['shipped']);
					// pkg_nameC.find('span').html(json['pkg_name']);
					// pkg_nameC.find('.shipment_tracking').val(json['shipment_tracking']);
					// pkg_nameC.find('.shipment_id').val(json['shipment_id']);
					// pkg_nameC.find('.shipping_item_id').val(json['shipping_item_id']);
					// pkg_nameC.find('.shipping_cost').val(json['shipping_cost']);

					needC.find('span').text( rQty - json['shipped'] );
					needC.find('input').text( rQty - json['shipped'] );
				} 
				if (json['error']) {
					nameC.find('span').text(json['name']);
				}
				$('input[type=submit]').removeAttr('disabled');
			});
}
</script>
</body>
</html>