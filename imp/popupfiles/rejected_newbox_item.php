<?php



include_once '../auth.php';

include_once '../inc/functions.php';

$printers = array(

	array('id' => QC1_PRINTER, 'value' => 'QC1'),

	array('id' => QC2_PRINTER, 'value' => 'QC2'),

	array('id' => REC_PRINTER, 'value' => 'Receiving'),

	array('id' => STOREFRONT_PRINTER, 'value' => 'Storefront')

	);

if ($_POST['get'] == 'reasons') {

	$reasons = $db->func_query("SELECT * FROM inv_rj_reasons WHERE classification_id = (SELECT classification_id FROM oc_product where model = '". $_POST['sku'] ."')");

	if ($reasons) {

		$html = '<option value="">Select</option>';

		foreach ($reasons as $reason) {

			$html .= '<option value="' . $reason['id'] . '">' . $reason['name'] . '</option>';

		}

		echo $html;

	}

	exit;

}

$shipment_id = (int)$_GET['shipment_id'];

if(!$shipment_id){

	$shipment_number = $db->func_query_first_cell("select package_number from inv_rejected_shipments where status != 'Completed'");

}

$shipment_number = $db->func_query_first_cell("select package_number from inv_rejected_shipments where status != 'Completed'");





$inv_query  = 



$product   = $db->func_query("select si.* , s.package_number,s.id as ShipmentId from inv_rejected_shipment_items si left join inv_shipments s on (si.shipment_id = s.id)

	where rejected_shipment_id = '$shipment_id' and si.deleted=0 order by shipment_id");

















$message = false;



if(@$_POST['add'] && $shipment_id){

	for($i=0;$i<10;$i++){

		if($_POST['product_sku'][$i] || $_POST['reason'][$i]){

			$rejcetedShipmentItem = array();

			

			





			$prod=getProduct($rejcetedShipmentItem ['product_sku']);

			$prod_id=$prod['product_id'];



			$pkg_num=($_POST['package_number'][$i]);

			//print_r($pkg_num);

			//exit;

			$shipments_id=$db->func_query_first_cell('select id from inv_shipments WHERE package_number="' . $pkg_num . '"');

			//print_r($shipments_id);

			//exit;



			$shipmentName = $db->func_query_first_cell( "select package_number from inv_rejected_shipments where id = '$shipment_id'" );

			if($shipments_id){



				$rejcetedShipmentItem ['rejected_shipment_id'] = $shipment_id;

				//print_r($rejcetedShipmentItem ['rejected_shipment_id']);

				//exit;

			//$rejcetedShipmentItem ['package_number']=($_POST['package_number'][$i]);

				$rejcetedShipmentItem ['shipment_id'] = $shipments_id;



				$rejcetedShipmentItem['product_sku']   = trim($_POST['product_sku'][$i]);

				$rejcetedShipmentItem['reject_reason'] = $_POST['reason'][$i];

				$rejcetedShipmentItem['qty_rejected']  = 1;

				$rejcetedShipmentItem['reject_item_id'] = getRejectId('RJ-' . trim($_POST['product_sku'][$i]) . '-');

				$rejcetedShipmentItem['cost'] = getTrueCost(trim($_POST['product_sku'][$i]));

				$rejcetedShipmentItem['date_added'] = date( 'Y-m-d H:i:s' );

			//print_r($rejcetedShipmentItem);exit;

				$db->func_array2insert ( 'inv_rejected_shipment_items', $rejcetedShipmentItem );



				$db->func_query('insert into inv_shipment_items (shipment_id,product_id,unit_price,rejected_product) values("' . $shipments_id .'","'.$prod_id.'","0.00","1") ');



				// printLabel($rejcetedShipmentItem['reject_item_id'], $rejcetedShipmentItem['product_sku'], $shipmentName, $db->func_query_first_cell( "select name from inv_rj_reasons where id = '". $rejcetedShipmentItem['reject_reason'] ."'" ), $rejcetedShipmentItem['shipment_id'], $_POST['printerid']);

				$_temp_reject_id = str_replace($rejcetedShipmentItem['product_sku'].'-', '', $rejcetedShipmentItem['reject_item_id']);
				printLabel('','','','','',$_POST['printerid'],'','15','','',$db->func_query_first_cell("SELECT id from inv_rejected_shipment_items where reject_item_id='".$rejcetedShipmentItem['reject_item_id']."'"),$rejcetedShipmentItem['reject_item_id'],$db->func_query_first_cell("SELECT id from inv_rejected_shipment_items where reject_item_id='".$rejcetedShipmentItem['reject_item_id']."'"));

				// printLabel('','','','','',$_POST['printerid'],'','15','','',$returns_po_item['id'],$returns_po_item_insert ['reject_item_id'],$returns_po_item['id']);

				

			}

			elseif ($shipments_id==null) {

				

				$rejcetedShipmentItem ['rejected_shipment_id'] = $shipment_id;

				//print_r($rejcetedShipmentItem ['rejected_shipment_id']);

				//exit;

			//$rejcetedShipmentItem ['package_number']=($_POST['package_number'][$i]);

				$rejcetedShipmentItem ['shipment_id'] = $shipments_id;



				$rejcetedShipmentItem ['product_sku']   = trim($_POST['product_sku'][$i]);

				$rejcetedShipmentItem ['reject_reason'] = $_POST['reason'][$i];

				$rejcetedShipmentItem ['qty_rejected']  = 1;

				$rejcetedShipmentItem['reject_item_id'] = getRejectId('RJ-' . trim($_POST['product_sku'][$i]) . '-');

				$rejcetedShipmentItem['cost'] = getTrueCost(trim($_POST['product_sku'][$i]));

				$rejcetedShipmentItem['date_added'] = date( 'Y-m-d H:i:s' );

			//print_r($rejcetedShipmentItem);exit;



				printLabel($rejcetedShipmentItem['reject_item_id'], $rejcetedShipmentItem['product_sku'], $shipmentName,  $db->func_query_first_cell( "select name from inv_rj_reasons where id = '". $rejcetedShipmentItem['reject_reason'] ."'" ), $rejcetedShipmentItem['shipment_id'], $_POST['printerid']);


				$_temp_reject_id = str_replace($rejcetedShipmentItem['product_sku'].'-', '', $rejcetedShipmentItem['reject_item_id']);
				printLabel('','','','','',$_POST['printerid'],'','15','','',$rejcetedShipmentItem['reject_item_id'],$_temp_reject_id,$rejcetedShipmentItem['product_sku']);



				$db->func_array2insert ( 'inv_rejected_shipment_items', $rejcetedShipmentItem );



				$db->func_query('insert into inv_shipment_items (shipment_id,product_id,unit_price,rejected_product) values("' . $shipments_id .'","'.$prod_id.'","0.00","1") ');



			}

			else{

				echo "please enter valid shipment number";

			}





		//	$db->func_query('insert into inv_shipment_items (shipment_id,product_id,unit_price,rejected_product) values( "$shipment_id","$prod_id","0.00","1")');



			//$shipment=$db->func_query('select * from inv_shipment_items where product_id= "$prod_id"' );

			//print_r($shipment);

			//exit;



			//$package_number=($_POST['package_number'][$i]);

			//print_r($package_number);

			//exit;



			



		}

	}

	

	$_SESSION['message'] = "New Rejected PO items is added.";

	echo "<script>window.close();parent.window.location.reload();</script>";

	exit;

}

?>

<html>

<body>

	<div align="center">

		<div style="display: none;"> 

			<?php include_once '../inc/header.php';?>

		</div>

		<?php if($message):?>

			<h5 align="center" style="color:red;"><?php echo $message;?></h5>

		<?php endif;?>



		<form method="post">

			<table>

				<?php for($i=0;$i<10;$i++):?>

					<tr>

						<td>S No:</td>

						<td>



							<input type="text" id="package_number" name="package_number[<?php echo $i;?>]" /> 

						</td>





						<td>SKU</td>

						<td>

							<input type="text" onkeyup="getReasons(this);" onchange="getReasons(this);" class="sku" name="product_sku[<?php echo $i;?>]" value="" <?php if($i == 0):?> required <?php endif;?> />

						</td>

						<td>Issue</td>

						<td>

							<select class="reason" name="reason[<?php echo $i;?>]">

								<option value="">Select</option>

							</select>

						</td>

					</tr>

				<?php endfor;?>	



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

		<script type="text/javascript">

			function getReasons (t) {

				var sku = $(t).val();

				$(t).parents('tr').attr('required', 'required');

				if (sku != '') {

					$.ajax({

						url: '',

						type: 'POST',

						dataType: 'html',

						data: {get: 'reasons', sku: sku},

					})

					.always(function(html) {

						if (html) {

							$(t).parents('tr').find('.reason').html(html);

						}

					});

				} else {

					$(t).parents('tr').removeAttr('required');

				}

			}



			

		</script>

	</div>	

</body>

</html>