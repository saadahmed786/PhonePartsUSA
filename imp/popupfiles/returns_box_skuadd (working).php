<?php

include_once '../auth.php';
include_once '../inc/functions.php';

$message = false;
$return_shipment_box_id = $_GET['return_shipment_box_id'];

if(@$_POST['add']){
	$sku = $db->func_escape_string($_POST['product_sku']);
	$product = $db->func_query_first("select id from inv_return_shipment_box_items where product_sku = '$sku' and return_shipment_box_id = '$return_shipment_box_id'");
	if(!$product){
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
            
	} else{
		$message = "SKU is already exist";
	}
}
?>
<html>
	<body>
		<div align="center">
			<?php if($message):?>
				<h5 align="center" style="color:red;"><?php echo $message;?></h5>
			<?php endif;?>
			
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
						<td colspan="2" align="center"><input type="submit" name="add" value="Submit" /></td>					
					</tr>
				</table>
			</form>		
		</div>	
	</body>
</html>