<?php

include_once 'auth.php';
$message = false;

if($_POST['add']){
	$sku = $db->func_escape_string($_POST['sku']);
	$shipment_id = (int)$_GET['shipment_id'];
	$product = $db->func_query_first("select product_id from oc_product where sku = '$sku'");
	if($product){
		if($shipment_id){
			$shipment_item = array();
			$shipment_item['product_sku'] = $sku;
			$shipment_item['product_id']  = $product['product_id'];
			$shipment_item['qty_shipped'] = $_POST['qty'];
			$shipment_item['shipment_id'] = $shipment_id;
			
			$checkExist = $db->func_query_first_cell("select id from inv_shipment_items where shipment_id = '$shipment_id' and product_sku = '".$sku."' AND rejected_product = '0'");
			if(!$checkExist){
				$db->func_array2insert("inv_shipment_items",$shipment_item);
			}
		}
		else{
			$_SESSION['list'][$product['product_id']] =  array("qty"=>$_POST['qty']);
		}
		
		echo "<script>window.close();parent.window.location.reload();</script>";
		exit;
	}
	else{
		$message = "SKU is not exist";
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
						<td><input type="text" name="sku" value="" required /></td>					
					</tr>
					
					<tr>
						<td>Qty Received:</td>
						<td><input type="text" name="qty" value="" required /></td>					
					</tr>
					
					<tr>
						<td colspan="2" align="center"><input type="submit" name="add" value="Submit" /></td>					
					</tr>
				</table>
			</form>		
		</div>	
	</body>
</html>