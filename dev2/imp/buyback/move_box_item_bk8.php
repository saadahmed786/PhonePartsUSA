<?php

include_once '../auth.php';

$message = false;
$id = (int)$_GET['id'];
$shipment_id = (int)$_GET['shipment_id'];
$manual = (int)$_GET['manual'];

if(@$_POST['update']){
	$inv_return_shipment_box_items = array();
	$inv_return_shipment_box_items['shipment_id'] = $_POST['new_shipment_id'];
	
	if($manual)
	{
$db->func_array2update("inv_buyback_manual_box_items",$inv_return_shipment_box_items,"id = '$id'");
	
}
else
{
	$db->func_array2update("inv_buyback_box_items",$inv_return_shipment_box_items,"id = '$id'");
}
	$_SESSION['message'] = "Item has been moved to another box.";
	
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
}

$boxes = $db->func_query("select id , package_number from inv_buyback_boxes where id != '$shipment_id' order by date_added DESC");
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
						<td colspan="2" align="center"><input type="submit" name="update" value="Submit" /></td>					
					</tr>
				</table>
			</form>		
		</div>	
	</body>
</html>