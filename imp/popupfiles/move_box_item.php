<?php

include_once '../auth.php';

$message = false;
$reject_id   = $_GET['reject_id'];
$box_id = (int)$_GET['id'];

if(@$_POST['update']){
	$inv_return_shipment_box_items = array();
	$inv_return_shipment_box_items['return_shipment_box_id'] = $_POST['new_box_id'];
	
	$db->func_array2update("inv_return_shipment_box_items",$inv_return_shipment_box_items,"return_item_id = '$reject_id'");
	$_SESSION['message'] = "Reject Item is moved to another box.";
	
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
}

$boxes = $db->func_query("select id , box_number from inv_return_shipment_boxes where id != '$box_id' and status<>'Completed' and box_type not in ('NotTestedBox','NTRBox') order by box_type");
$boxes1 =$db->func_query("SELECT id, box_number from inv_return_shipment_boxes where id != '$box_id' and status<>'Completed' and  box_type  in ('NotTestedBox') order by id desc limit 1  ");
$boxes2 =$db->func_query("SELECT id, box_number from inv_return_shipment_boxes where id != '$box_id' and status<>'Completed' and  box_type  in ('NTRBox') order by id desc limit 1  ");
$boxes = array_merge($boxes,$boxes1,$boxes2);

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
						<td>Box:</td>
						<td>
							<select name="new_box_id" required style="width:150px;">
					      		<option value="">Select One</option>
					      		<?php foreach($boxes as $box):?>
					      			<option value="<?php echo $box['id']; ?>"><?php echo $box['box_number']; ?></option>
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