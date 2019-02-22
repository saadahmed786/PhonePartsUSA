<?php

include_once '../auth.php';
include_once '../inc/functions.php';

$message = false;
$reject_id   = $_GET['reject_id'];
$shipment_id = (int)$_GET['id'];

if(@$_POST['update']){
	$inv_return_shipment_box_items = array();
	$inv_return_shipment_box_items['rejected_shipment_id'] = $_POST['new_shipment_id'];
	$inv_return_shipment_box_items['date_updated'] = date ( 'Y-m-d H:i:s' );
	$last_id = $inv_return_shipment_box_items['rejected_shipment_id'];
	$db->func_array2update("inv_rejected_shipment_items",$inv_return_shipment_box_items,"reject_item_id = '$reject_id'");
	$to = $db->func_query_first_cell( "select package_number from inv_rejected_shipments where id = '$last_id'" );
	$from = $db->func_query_first_cell( "select package_number from inv_rejected_shipments where id = '$shipment_id'" );
	logRejectItem($reject_id, 'Moved to <a href="'.$host_path.'addedit_rejectedshipment.php?shipment_id='.$last_id.'">' . $to . '</a> from <a href="'.$host_path.'addedit_rejectedshipment.php?shipment_id='.$shipment_id.'">' . $from . '</a> by ' ,$from ,$to);
	$_SESSION['message'] = "Reject Item is moved to another box.";
	
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
}

$boxes = $db->func_query("select id , package_number from inv_rejected_shipments where id != '$shipment_id' AND status = 'Pending' and is_hidden=0 order by date_added DESC");
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
						<td>Reject ID:</td>
						<td><?php echo $reject_id;?></td>					
					</tr>
					
					<tr>
						<td>Transfer to RTV Box:</td>
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