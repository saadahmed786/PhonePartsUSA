<?php

include_once '../auth.php';
include_once '../inc/functions.php';

$message = false;
$oema= (int)$_GET['oema'];
$oemb= (int)$_GET['oemb'];
$oemc= (int)$_GET['oemc'];
$oemd= (int)$_GET['oemd'];
$n_oema= (int)$_GET['n_oema'];
$n_oemb= (int)$_GET['n_oemb'];
$n_oemc= (int)$_GET['n_oemc'];
$n_oemd= (int)$_GET['n_oemd'];
$tot_qty= $oema+$oemb+$oemc+$oemd+$n_oema+$n_oemb+$n_oemc+$n_oemd;
$id = (int)$_GET['id'];
$sku = $_GET['sku'];
$shipment_id= (int)$_GET['shipment_id'];
$buyback_product_id = (int)$_GET['buyback_product_id'];
$manual = (int)$_GET['manual'];

if(@$_POST['update']){
	$s_id=$newitemdata['shipment_id'];
	if($tot_qty<$_POST['TransferQty']){
		$_SESSION['message'] = "Item quantity to transfer exceeds items present in the box.";
		echo "<script>window.close();parent.window.location.reload();</script>";
		exit;
	}
	else if($tot_qty==$_POST['TransferQty']){
		$i=0;
		$inv_return_shipment_box_items = array();
		$inv_return_shipment_box_items['shipment_id'] = $_POST['new_shipment_id'];
		if($manual)
		{
			$db->func_array2update("inv_buyback_manual_box_items",$inv_return_shipment_box_items,"shipment_id = '$shipment_id'");	
		}
		else
		{
		$db->func_array2update("inv_buyback_box_items",$inv_return_shipment_box_items,"id = '$id'");
		}
	}
	else if($tot_qty>$_POST['TransferQty']){
		$remain_qty = array();
		$inv_return_shipment_box_items = array();
		if($manual)
		{
			if ($oema>$i){
				$remain_qty['oem_qty_a']=$oema-$_POST['TransferQty'];
				$inv_return_shipment_box_items['oem_qty_a'] = $_POST['TransferQty'];
			}
			else if ($oemb>$i) {
				$remain_qty['oem_qty_b']=$oemb-$_POST['TransferQty'];
				$inv_return_shipment_box_items['oem_qty_b'] = $_POST['TransferQty'];
			}
			else if ($oemc>$i) {
				$remain_qty['oem_qty_c']=$oemc-$_POST['TransferQty'];
				$inv_return_shipment_box_items['oem_qty_c'] = $_POST['TransferQty'];
			}
			else if ($oemd>$i) {
				$remain_qty['oem_qty_d']=$oemd-$_POST['TransferQty'];
				$inv_return_shipment_box_items['oem_qty_d'] = $_POST['TransferQty'];
			}
			else if ($n_oema>$i) {
				$remain_qty['non_oem_qty_a']=$n_oema-$_POST['TransferQty'];
				$inv_return_shipment_box_items['non_oem_qty_a'] = $_POST['TransferQty'];
			}
			else if ($n_oemb>$i) {
				$remain_qty['non_oem_qty_b']=$n_oemb-$_POST['TransferQty'];
				$inv_return_shipment_box_items['non_oem_qty_b'] = $_POST['TransferQty'];
			}
			else if ($n_oemc>$i) {
				$remain_qty['non_oem_qty_c']=$n_oemc-$_POST['TransferQty'];
				$inv_return_shipment_box_items['non_oem_qty_c'] = $_POST['TransferQty'];
			}
			else if ($n_oemd>$i) {
				$remain_qty['non_oem_qty_d']=$n_oemd-$_POST['TransferQty'];
				$inv_return_shipment_box_items['non_oem_qty_d'] = $_POST['TransferQty'];
			}
			else {
				$_SESSION['message'] = "No Quantity of desired item present in the box to transfer.";
			echo "<script>window.close();parent.window.location.reload();</script>";
			exit;
			}

			$db->func_array2update("inv_buyback_manual_box_items",$remain_qty," id = '$id'");	
			$inv_return_shipment_box_items['shipment_id'] = $_POST['new_shipment_id'];
			$inv_return_shipment_box_items['item_condition'] = 'Manual LCD';
			$inv_return_shipment_box_items['sku'] = $sku;
			$db->func_array2insert("inv_buyback_manual_box_items",$inv_return_shipment_box_items);
		}
		else
		{
			if ($oema>$i){
			$remain_qty['oem_received_a']=$oema-$_POST['TransferQty'];
			$inv_return_shipment_box_items['oem_received_a'] = $_POST['TransferQty'];
		}
		else if ($oemb>$i) {
			$remain_qty['oem_received_b']=$oemb-$_POST['TransferQty'];
			$inv_return_shipment_box_items['oem_received_b'] = $_POST['TransferQty'];
		}
		else if ($oemc>$i) {
			$remain_qty['oem_received_c']=$oemc-$_POST['TransferQty'];
			$inv_return_shipment_box_items['oem_received_c'] = $_POST['TransferQty'];
		}
		else if ($oemd>$i) {
			$remain_qty['oem_received_d']=$oemd-$_POST['TransferQty'];
			$inv_return_shipment_box_items['oem_received_d'] = $_POST['TransferQty'];
		}
		else if ($n_oema>$i) {
			$remain_qty['non_oem_received_a']=$n_oema-$_POST['TransferQty'];
			$inv_return_shipment_box_items['non_oem_received_a'] = $_POST['TransferQty'];
		}
		else if ($n_oemb>$i) {
			$remain_qty['non_oem_received_b']=$n_oemb-$_POST['TransferQty'];
			$inv_return_shipment_box_items['non_oem_received_b'] = $_POST['TransferQty'];
		}
		else if ($n_oemc>$i) {
			$remain_qty['non_oem_received_c']=$n_oemc-$_POST['TransferQty'];
			$inv_return_shipment_box_items['non_oem_received_c'] = $_POST['TransferQty'];
		}
		else if ($n_oemd>$i) {
			$remain_qty['non_oem_received_d']=$n_oemd-$_POST['TransferQty'];
			$inv_return_shipment_box_items['non_oem_received_d'] = $_POST['TransferQty'];
		}
		else {
			$_SESSION['message'] = "No Quantity of desired item present in the box to transfer.";
		echo "<script>window.close();parent.window.location.reload();</script>";
		exit;
		}
			$db->func_array2update("inv_buyback_box_items",$remain_qty,"id = '$id'");
			$inv_return_shipment_box_items['shipment_id'] = $_POST['new_shipment_id'];
			$inv_return_shipment_box_items['buyback_product_id'] = $buyback_product_id;
			$db->func_array2insert("inv_buyback_box_items",$inv_return_shipment_box_items);
		}
		
		// testObject($newitemdata);
	}
	$from = $db->func_query_first_cell("select package_number from inv_buyback_boxes where id = '$shipment_id'");
	$to = $db->func_query_first_cell( "select package_number from inv_buyback_boxes where id = '".$_POST['new_shipment_id']."'" );
			$_link1 = '<a href="'.$host_path.'buyback/addedit_boxes.php?shipment_id='.$_POST['new_shipment_id'].'">'.$to.'</a>';
			$_link2 = '<a href="'.$host_path.'buyback/addedit_boxes.php?shipment_id='.$shipment_id.'">'.$from.'</a>';
		logLbbItem($sku, 'Moved to '.$_link1.' from '. $_link2 . ' by ', $from, $to); // Log For Box in which item is sent
		logLbbItem($sku, 'Moved to '.$_link1.' from '. $_link2 . ' by ', $to, $from); // Log for box from which item is sent
	$_SESSION['message'] = "Item has been moved to another box.";
	
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
}

$boxes = $db->func_query("select id , package_number from inv_buyback_boxes where id != '$shipment_id' AND status = 'Pending' order by date_added DESC");
?>
<html>
	<body>
		<div align="center">
			<?php if($message):?>
				<h5 align="center" style="color:red;"><?php echo $message;?></h5>
			<?php endif;?>
			
			<form method="post">
				<input name="sku" type="hidden" value="<?php echo $sku;?>" />
				<input name="sku" type="hidden" value="<?php echo $count;?>" />
				
				<table>
					<tr>
						<td>Current Shipment:</td>
						<td><?php echo $db->func_query_first_cell("SELECT package_number FROM inv_buyback_boxes WHERE id='".$shipment_id."'");?></td>					
					</tr>
					
					<tr>
						<td>Boxes:</td>
						<td>
							<select name="new_shipment_id" required style="width:150px;">
					      		<option value="">Select One</option>
					      		<?php foreach($boxes as $box):?>
					      			<option value="<?php echo $box['id']; ?>"><?php echo $box['id'] . " -- ". $box['package_number']; ?></option>
					      		<?php endforeach;?>
					      	</select>
						</td>				
					</tr>
					<tr>
						<td>Qty to Transfer:</td>
						<td><input required type="text" name="TransferQty" style="width:150px;"/></td>					
					</tr>
					
					<tr>
						<td colspan="2" align="center"><input type="submit" name="update" value="Submit" /></td>					
					</tr>
				</table>
			</form>		
		</div>	
	</body>
</html>