<?php

include_once '../auth.php';

$message = false;

if(@$_POST['add']){
	$sku = $db->func_escape_string($_POST['sku']);
	$product = $db->func_query_first("select id from inv_product_skus where sku = '$sku'");
	if(!$product){
		$productSku = array();
		$productSku['sku'] = $sku;
		$productSku['description'] = $sku;
		$productSku['user_id'] = $_SESSION['user_id'];
		$productSku['date_added'] = date('Y-m-d H:i:s');
		
		$db->func_array2insert("inv_product_skus",$productSku);
		$_SESSION['message'] = "SKU Precursor created";
		
		echo "<script>window.close();parent.window.location.reload();</script>";
		exit;
	}
	else{
		$message = "SKU Precursor is already exist";
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
						<td>SKU Precursor:</td>
						<td><input type="text" name="sku" value="" maxlength="7" required /></td>					
					</tr>
					
					<tr>
						<td>Description:</td>
						<td><input type="text" name="description" value="" required /></td>					
					</tr>
					
					<tr>
						<td colspan="2" align="center"><input type="submit" name="add" value="Submit" /></td>					
					</tr>
				</table>
			</form>		
		</div>	
	</body>
</html>