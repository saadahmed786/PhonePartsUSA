<?php

include_once '../auth.php';
include_once '../inc/functions.php';

$message = false;
$return_id = $_GET['return_id'];

if(@$_POST['add']){
	$sku = $db->func_escape_string($_POST['sku']);
	$productSku = array();
	$productSku['sku'] = $sku;
	$productSku['title'] = getItemName($sku);
	$productSku['quantity'] = 1;
	$productSku['price'] = $_POST['price'];
	$productSku['return_code'] = $_POST['return_code'];
	$productSku['return_id']   = $return_id;
	
	$db->func_array2insert("inv_return_items",$productSku);
	$_SESSION['message'] = "SKU added to returns";
	
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
}

$reasons = $db->func_query("select title from inv_reasons","title");
if($reasons){
	$reasons = array_keys($reasons);
}
else{
	$reasons = array('R1. Do Not Return','R2. Change of Mind','R3. Non-Functional','R4. Item Not As Described','R5.Received Wrong Item');
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
						<td>Price:</td>
						<td><input type="text" name="price" value="" required /></td>					
					</tr>
					
					<tr>
						<td>Return Reason:</td>
						<td>
							<select name="return_code" required style="width:150px;">
					      		<option value="">Select One</option>
					      		<?php foreach($reasons as $reason):?>
					      			<option value="<?php echo $reason; ?>"><?php echo $reason; ?></option>
					      		<?php endforeach;?>
					      	</select>
						</td>					
					</tr>
					
					<tr>
						<td colspan="2" align="center"><input type="submit" name="add" value="Submit" /></td>					
					</tr>
				</table>
			</form>		
		</div>	
	</body>
</html>