<?php



include_once '../auth.php';

include_once '../inc/functions.php';

$printers = array(

	array('id' => QC1_PRINTER, 'value' => 'QC1'),

	array('id' => QC2_PRINTER, 'value' => 'QC2'),

	array('id' => REC_PRINTER, 'value' => 'Receiving'),

	array('id' => STOREFRONT_PRINTER, 'value' => 'Storefront')

	);

if ($_POST['action'] == 'rma-sku') {

	$rma_number = $db->func_escape_string($_POST['rma_number']);

	$rma = $db->func_query_first('SELECT * FROM inv_returns WHERE rma_number = "' . $rma_number . '"');

	if ($rma) {

		$products = $db->func_query('SELECT * FROM inv_return_items WHERE return_id = "' . $rma['id'] . '" AND quantity > 0 ORDER BY sku ASC');

		if ($products) {

			$json['success'] = 1;

			$json['order_id'] = $rma['order_id'];

			foreach ($products as $key => $product) {

				if ($product['quantity']) {

					for ($i=0; $i < $product['quantity']; $i++) { 

						$json['data'] .= '<tr>';

						$json['data'] .= '<td><input type="checkbox" name="product['. $product['sku'] .'][]" onclick="makeReq(this);" value="'. $product['sku'] .'"></td>';

						$json['data'] .= '<td>'. $product['sku'] .'</td>';

						$json['data'] .= '<td><input type="text" name="reason['. $product['sku'] .'][]" value=""></td>';

						$json['data'] .= '</tr>';

					}

				}



			}

		} else {

			$json['error'] = 1;

			$json['data'] = '<tr><td colspan="3" style="color: #f00;">Sku not found</td></tr>';

		}

	} else {

		$json['error'] = 1;

		$json['data'] = '<tr><td colspan="3" style="color: #f00;">Wrong Shipment No</td></tr>';

	}



	echo json_encode($json);

	exit;

}



if ($_POST['action'] == 'shipment-sku') {

	$package_number = $db->func_escape_string($_POST['package_number']);

	$shipment = $db->func_query_first('SELECT * FROM inv_shipments WHERE package_number = "' . $package_number . '"');

	if ($shipment) {

		$products = $db->func_query('SELECT * FROM inv_shipment_items WHERE shipment_id = "' . $shipment['id'] . '" AND qty_received > 0 ');

		if ($products) {

			$json['success'] = 1;

			$json['shipment_id'] = $shipment['id'];

			foreach ($products as $key => $product) {

				$qr = $db->func_query_first_cell('SELECT (`ntr` + `rejected`) FROM `inv_shipment_qc` WHERE shipment_id = "'. $shipment['id'] .'" AND product_sku = "'. $product['product_sku'] .'"');

				$product['qty_received'] = $product['qty_received'] - $qr;

				if ($product['qty_received']) {

					for ($i=0; $i < $product['qty_received']; $i++) { 

						$json['data'] .= '<tr>';

						$json['data'] .= '<td><input type="checkbox" name="product['. $product['product_sku'] .'][]" onclick="makeReq(this);" value="'. $product['product_sku'] .'"></td>';

						$json['data'] .= '<td>'. $product['product_sku'] .'</td>';

						$json['data'] .= '<td><input type="text" name="reason['. $product['product_sku'] .'][]" value=""></td>';

						$json['data'] .= '</tr>';

					}

				}



			}

		} else {

			$json['error'] = 1;

			$json['data'] = '<tr><td colspan="3" style="color: #f00;">Sku not found</td></tr>';

		}

	} else {

		$json['error'] = 1;

		$json['data'] = '<tr><td colspan="3" style="color: #f00;">Wrong Shipment No</td></tr>';

	}



	echo json_encode($json);

	exit;

}

$message = false;

$return_shipment_box_id = $_GET['return_shipment_box_id'];



if($_POST['saveRMA']){

	

	foreach ($_POST['product'] as $sku => $products) {

		foreach ($products as $no => $product) {

			$productSku = array();

			$productSku['product_sku'] = $sku;

			$productSku['quantity'] = 1;

			$productSku['reason']   = $db->func_escape_string($_POST['reason'][$sku][$no]);

			$productSku['order_id'] = $db->func_escape_string($_POST['order_id']);

			$productSku['rma_number'] = $db->func_escape_string($_POST['rma_number']);

			$productSku ['return_item_id'] = getReturnItemId($_POST ['rma_number'] , $sku);

			$productSku['date_added'] = date('Y-m-d H:i:s');

			$productSku['cost'] = getInvoiceCost($sku, $db->func_escape_string($_POST['order_id']));

			$productSku['source'] = 'manual';

			$productSku['return_shipment_box_id'] = $return_shipment_box_id;



			printLabel($productSku ['return_item_id'], $sku, $db->func_query_first_cell( "select box_number from inv_return_shipment_boxes where id = '". $return_shipment_box_id ."'" ), $productSku['reason'], $productSku['rma_number'], $_POST['printerid']);

			

			$db->func_array2insert("inv_return_shipment_box_items", $productSku);



		}

	}



	$_SESSION['message'] = "SKU is added to box";

	echo "<script>window.close();parent.window.location.reload();</script>";

	exit;

}



if($_POST['save']){

	$shipment_id = $db->func_escape_string($_POST['shipment_id']);

	foreach ($_POST['product'] as $sku => $products) {

		foreach ($products as $no => $product) {

			//print_r(getRejectId('NTR'));

			$productSku = array();

			$productSku['product_sku'] = $sku;

			$productSku['quantity'] = 1;

			$productSku['reason']   = $db->func_escape_string($_POST['reason'][$sku][$no]);

			$productSku ['return_item_id'] = getRejectId('NTR-');

			$productSku['date_added'] = date('Y-m-d H:i:s');

			$productSku['cost'] = getTrueCost($sku);

			$productSku['source'] = 'manual';

			$productSku['shipment_id'] = $shipment_id;

			$productSku['return_shipment_box_id'] = $return_shipment_box_id;

			

			printLabel($productSku ['return_item_id'], $sku, $db->func_query_first_cell( "select box_number from inv_return_shipment_boxes where id = '". $return_shipment_box_id ."'" ), $productSku['reason'], $productSku['shipment_id'], $_POST['printerid']);



			$db->func_array2insert("inv_return_shipment_box_items", $productSku);



		}

		$db->func_query('UPDATE inv_shipment_qc set `ntr` = (`ntr` + "'. count($products) .'") WHERE shipment_id = "'. $shipment_id .'" AND product_sku = "' . $sku . '"');

	}



	$_SESSION['message'] = "SKU is added to box";

	echo "<script>window.close();parent.window.location.reload();</script>";

	exit;

}



if(@$_POST['add']){

	$sku = $db->func_escape_string($_POST['product_sku']);

/*	$product = $db->func_query_first("select id from inv_return_shipment_box_items where product_sku = '$sku' and return_shipment_box_id = '$return_shipment_box_id'");

	if(!$product){*/

		$productSku = array();

		$productSku['product_sku'] = $sku;

		$productSku['quantity'] = 1;

		$productSku['reason']   = $db->func_escape_string($_POST['reason']);

		$productSku['order_id'] = $db->func_escape_string($_POST['order_id']);

		$productSku['rma_number'] = $db->func_escape_string($_POST['rma_number']);

		$productSku ['return_item_id'] = getReturnItemId($_POST ['rma_number'] , $sku);

		$productSku['date_added'] = date('Y-m-d H:i:s');

		$productSku['cost'] = getInvoiceCost($sku, $db->func_escape_string($_POST['order_id']));

		$productSku['source'] = 'manual';

		$productSku['return_shipment_box_id'] = $return_shipment_box_id;

		

		$db->func_array2insert("inv_return_shipment_box_items",$productSku);

		$_SESSION['message'] = "SKU is added to box";

		

		echo "<script>window.close();parent.window.location.reload();</script>";

		exit;



/*	} else{

		$message = "SKU is already exist";

	}*/

}

?>

<html>

<body>

	<div align="center" style="display:none"> 

		<?php include_once '../inc/header.php';?>

	</div>

	<div align="center">

		<?php if($message):?>

			<h5 align="center" style="color:red;"><?php echo $message;?></h5>

		<?php endif;?>

		<div class="tabMenu" >

			<input type="button" class="toogleTab" data-tab="tabShipment" value="Shipment">

			<input type="button" class="toogleTab" data-tab="tabRMA" value="RMA">

			<input type="button" class="toogleTab" data-tab="tabOther" value="Other">

		</div>

		<div class="tabHolder">

			<div id="loading">

				<h2>Loading...</h2>

			</div>

			<div id="tabShipment" class="makeTabs">

				<form method="post">

					<table>

						<tr>

							<td>Shipment Tracking:</td>

							<td><input type="text" onkeyup="getShipment(this);" value=""/><input type="hidden" name="shipment_id" value=""/></td>

						</tr>



						<tr>

							<table border="0">

								<thead>

									<tr>

										<th></th>

										<th>SKU</th>

										<th>Reason</th>

									</tr>

								</thead>

								<tbody id="shipment-sku">

									

								</tbody>

							</table>

						</tr>



						<tr>

							<td>

								Printer:

								<select required="" name="printerid" id="printerid">

									<option value="">Select One</option>

									<?php foreach ($printers as $printer): ?>

										<option value="<?php echo $printer['id'] ?>" <?php if ($printer['id'] == $return_item['printer']): ?> selected="selected" <?php endif; ?>>

											<?php echo $printer['value'] ?>

										</option>

									<?php endforeach; ?>

								</select>

							</td>

							<td align="center"><input type="submit" name="save" value="Submit" /></td>					

						</tr>

					</table>

					<script type="text/javascript">

						function getShipment (t) {

							if ($(t).val()) {

								$.ajax({

									url: 'returns_box_skuadd.php',

									type: 'post',

									dataType: 'json',

									data: {package_number: $(t).val(), action: 'shipment-sku'},

								})

								.always(function(json) {

									$('#shipment-sku').html(json['data']);

									$('input[name=shipment_id]').val(json['shipment_id']);

								});

							}

						}

						function makeReq (t) {

							if ($(t).is(':checked')) {

								$(t).parent().parent().find('input[type=text]').attr('required', 'required');

							} else {

								$(t).parent().parent().find('input[type=text]').removeAttr('required');

							}

						}

					</script>

				</form>

			</div>

			<div id="tabRMA" class="makeTabs">

				<form method="post">

					<table>

						<tr>

							<td>RMA:</td>

							<td><input type="text" name="rma_number" onkeyup="getRMA(this);" value=""/><input type="hidden" id="rma_order" name="order_id" onkeyup="getRMA(this);" value=""/></td>

						</tr>



						<tr>

							<table border="0">

								<thead>

									<tr>

										<th></th>

										<th>SKU</th>

										<th>Reason</th>

									</tr>

								</thead>

								<tbody id="rma-sku">

									

								</tbody>

							</table>

						</tr>



						<tr>

							<td>

								Printer:

								<select required="" name="printerid" id="printerid">

									<option value="">Select One</option>

									<?php foreach ($printers as $printer): ?>

										<option value="<?php echo $printer['id'] ?>" <?php if ($printer['id'] == $return_item['printer']): ?> selected="selected" <?php endif; ?>>

											<?php echo $printer['value'] ?>

										</option>

									<?php endforeach; ?>

								</select>

							</td>

							<td align="center"><input type="submit" name="saveRMA" value="Submit" /></td>					

						</tr>

					</table>

					<script type="text/javascript">

						function getRMA (t) {

							if ($(t).val()) {

								$.ajax({

									url: 'returns_box_skuadd.php',

									type: 'post',

									dataType: 'json',

									data: {rma_number: $(t).val(), action: 'rma-sku'},

								})

								.always(function(json) {

									$('#rma-sku').html(json['data']);

									$('#rma_order').val(json['order_id']);

								});

							}

						}

					</script>

				</form>

			</div>

			<div id="tabOther" class="makeTabs">

				<form method="post">

					<table>

						<tr>

							<td>SKU:</td>

							<td><input type="text" name="product_sku" value="" required /></td>					

						</tr>



						<tr>

							<td>Reason:</td>

							<td><input type="text" name="reason" value="" /></td>					

						</tr>



						<tr>

							<td>Order ID:</td>

							<td><input type="text" name="order_id" value="" /></td>					

						</tr>



						<tr>

							<td>RMA number:</td>

							<td><input type="text" name="rma_number" value="" /></td>					

						</tr>



						<tr>

							<td>

								Printer:

								<select required="" name="printerid" id="printerid">

									<option value="">Select One</option>

									<?php foreach ($printers as $printer): ?>

										<option value="<?php echo $printer['id'] ?>" <?php if ($printer['id'] == $return_item['printer']): ?> selected="selected" <?php endif; ?>>

											<?php echo $printer['value'] ?>

										</option>

									<?php endforeach; ?>

								</select>

							</td>

							<td align="center"><input type="submit" name="add" value="Submit" /></td>					

						</tr>

					</table>

				</form>

			</div>

		</div>

	</div>	

</body>

</html>