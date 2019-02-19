<?php

include_once 'auth.php';
$message = false;

if($_POST['add']){
	if($_POST['title']){
		$_POST['title'] = $db->func_escape_string($_POST['title']);
		if ($_POST['sku_type']) {
			// print_r((explode('-', $db->func_query_first_cell('select model from oc_product where model like "'. $_POST['sku_type'] .'%" order by model DESC;'))));exit;
			$_POST['sku'] = end(explode('-', $db->func_query_first_cell('select model from oc_product where model like "'. $_POST['sku_type'] .'%" order by model DESC;')));
			// echo $_POST['sku'];exit;
			$_POST['sku'] = (int)$_POST['sku']+1;
			$_POST['sku'] = $_POST['sku_type'] . '-' . $_POST['sku'];
		}
		$shipment_id = (int)$_GET['shipment_id'];
		if($shipment_id){
			$shipment_item = array();
			$shipment_item['product_id']   = 0;
			$shipment_item['product_name'] = $_POST['title'];
			$shipment_item['product_sku']  = $_POST['sku'];
			$shipment_item['qty_shipped']  = $_POST['qty'];
			$shipment_item['shipment_id']  = $shipment_id;
			$shipment_item['is_new']  = 1;
			$shipment_item['weight'] = (float)$_POST['weight'];
			
			$checkExist = $db->func_query_first_cell("select id from inv_shipment_items where shipment_id = '$shipment_id' and product_name = '".$db->func_escape_string($_POST['title'])."'");
			if(!$checkExist){
				$db->func_array2insert("inv_shipment_items",$shipment_item);
			}
		}
		else{
			$_SESSION['newlist'][] = array("qty"=>$_POST['qty'] , 'title' => $_POST['title'] , 'sku' => $_POST['sku']);
		}
		
		echo "<script>window.close();parent.window.location.reload();</script>";
		exit;
	}
	else{
		$message = "title can not be empty.";
	}
}
$product_skus = $db->func_query("select sku from inv_product_skus order by sku asc");
?>
<html>
<head>
	<script type="text/javascript" src="<?php echo $host_path ?>/js/jquery.min.js"></script>
</head>
<body>
	<div align="center">
		<?php if($message):?>
			<h5 align="center" style="color:red;"><?php echo $message;?></h5>
		<?php endif;?>
		
		<form method="post">
			<table>
				<tr>
					<td>Title:</td>
					<td><input type="text" name="title" value="" required /></td>					
				</tr>
				
				<tr>
					<td>SKU:</td>
					<td>
						<select name="sku_type" style="width: 100%;" required>
							<option value="">Please Select</option>
							<?php foreach ($product_skus as $product_sku): ?>
								<option value="<?php echo $product_sku['sku']; ?>"><?php echo $product_sku['sku']; ?></option>
							<?php endforeach; ?>
							<option value="-1">Custom</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<td>Qty To Shipped:</td>
					<td><input type="text" name="qty" value="" required /></td>					
				</tr>
				<tr>
					<td>Weight:</td>
					<td>
						<input type="text" style="width:50px" name="weight" id="weight_lb" onkeyup="changeWeight(this);" data-attr="lb"  value="0.0000" /> lb <strong>OR</strong> <input type="text" style="width:50px" id="weight_oz" onkeyup="changeWeight(this);" data-attr="oz"  value="0.0000" /> oz
						<br><span> (Enter lb OR oz, not both) </span>
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
<script>
	function changeWeight(obj)
	{
		var conversion = 'oz';
		var weight_oz = 0.0000;
		var weight_lb = 0.0000;
		if($(obj).attr('data-attr')=='oz')
		{
			var conversion = 'lb';
		}
		
		
		if(conversion =='oz')
		{
			var weight_lb = $('#weight_lb').val();
			
			if(weight_lb=='') weight_lb = 0.0000;


			weight_oz = parseFloat(weight_lb) * 16;
			weight_oz = weight_oz.toFixed(4);
			$('#weight_oz').val(weight_oz);
		}
		else
		{
			var weight_oz = $('#weight_oz').val();
			if(weight_oz=='') weight_oz = 0.0000;
			weight_lb = parseFloat(weight_oz) / 16;
			weight_lb = weight_lb.toFixed(4);
			$('#weight_lb').val(weight_lb);
		}
	}
</script>