<?php
include_once '../auth.php';
include_once '../inc/functions.php';
$message = false;
$id = (int)$_GET['id'];
$shipment_id = (int)$_GET['shipment_id'];
$conditions = array(
	array('id'=>'oa','value'=>'OEM Grade A'),
	array('id'=>'ob','value'=>'OEM Grade A-'),
	array('id'=>'oc','value'=>'OEM Grade B'),
	array('id'=>'od','value'=>'OEM Grade C'),
	array('id'=>'na','value'=>'Non-OEM Grade A'),
	array('id'=>'nb','value'=>'Non-OEM Grade A-'),
	array('id'=>'nc','value'=>'Non-OEM Grade B'),
	array('id'=>'nd','value'=>'Non-OEM Grade C'),
	array('id'=>'salvage','value'=>'Salvage')
	);
$models = $db->func_query("SELECT * FROM inv_buy_back");
$i=0;
if($_POST['update']){
	if(!$_POST['qty'][$i]){
		$_SESSION['message'] = "No Items Added.";
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
	}
	while($_POST['qty'][$i]) {
	$data = array();
	$data['shipment_id'] = $shipment_id;
	if($_POST['condition'][$i]=='oa'){
		$price = $db->func_query_first_cell("SELECT oem_a FROM inv_buy_back WHERE sku = '".$_POST['model'][$i]."'");
		$data['oem_qty_a'] = $_POST['qty'][$i];
		$data['oem_price_a'] = $price;
	}
	else if($_POST['condition'][$i]=='ob'){
		$price = $db->func_query_first_cell("SELECT oem_b FROM inv_buy_back WHERE sku = '".$_POST['model'][$i]."'");
		$data['oem_qty_b'] = $_POST['qty'][$i];
		$data['oem_price_b'] = $price;
	}
	else if($_POST['condition'][$i]=='oc'){
		$price = $db->func_query_first_cell("SELECT oem_c FROM inv_buy_back WHERE sku = '".$_POST['model'][$i]."'");
		$data['oem_qty_c'] = $_POST['qty'][$i];
		$data['oem_price_c'] = $price;
	}
	else if($_POST['condition'][$i]=='od'){
		$price = $db->func_query_first_cell("SELECT oem_d FROM inv_buy_back WHERE sku = '".$_POST['model'][$i]."'");
		$data['oem_qty_d'] = $_POST['qty'][$i];
		$data['oem_price_d'] = $price;
	}
	else if($_POST['condition'][$i]=='na'){
		$price = $db->func_query_first_cell("SELECT non_oem_a FROM inv_buy_back WHERE sku = '".$_POST['model'][$i]."'");
		$data['non_oem_qty_a'] = $_POST['qty'][$i];
		$data['non_oem_price_a'] = $price;
	}
	else if($_POST['condition'][$i]=='nb'){
		$price = $db->func_query_first_cell("SELECT non_oem_b FROM inv_buy_back WHERE sku = '".$_POST['model'][$i]."'");
		$data['non_oem_qty_b'] = $_POST['qty'][$i];
		$data['non_oem_price_b'] = $price;
	}
	else if($_POST['condition'][$i]=='nc'){
		$price = $db->func_query_first_cell("SELECT non_oem_c FROM inv_buy_back WHERE sku = '".$_POST['model'][$i]."'");
		$data['non_oem_qty_c'] = $_POST['qty'][$i];
		$data['non_oem_price_c'] = $price;
	}
	else if($_POST['condition'][$i]=='nd'){
		$price = $db->func_query_first_cell("SELECT non_oem_d FROM inv_buy_back WHERE sku = '".$_POST['model'][$i]."'");
		$data['non_oem_qty_d'] = $_POST['qty'][$i];
		$data['non_oem_price_d'] = $price;
	}
	else {
		$price = $db->func_query_first_cell("SELECT salvage FROM inv_buy_back WHERE sku = '".$_POST['model'][$i]."'");
		$data['salvage_qty'] = $_POST['qty'][$i];
		$data['salvage_price'] = $price;
	}
	$data['item_condition'] = 'Manual LCD';
	$data['sku'] = $_POST['model'][$i];	
	$db->func_array2insert('inv_buyback_manual_box_items', $data);
	$to = $db->func_query_first_cell( "select package_number from inv_buyback_boxes where id = '$shipment_id'" );
	logLbbItem($_POST['qty'][$i].' x '.$_POST['model'][$i], 'Added Manually by ',' ', $to);
	$i++;
	}
	$_SESSION['message'] = "Manual Items Added.";
	
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
}
?>
<html>
	<body>
	<div style="display:none">
	<?php include("../inc/header.php");?>
	</div>
		<div align="center">
			<?php if($message):?>
				<h5 align="center" style="color:red;"><?php echo $message;?></h5>
			<?php endif;?>
			<h2>
			Add Manual LCD
			</h2>
			<form method="post" id="frm">
				<table width="60%" cellpadding="5" cellspacing="0">
				<thead>
					<tr>
						<th>QTY</th>
						<th>LCD Model</th>
						<th>Condition</th>
					</tr>
				</thead>
				<tbody>	
				<?php while ($i!=10) { ?>
				<tr  align="center">
				<td><?php echo $i+1;?>:&nbsp<input type="text" style="width:150px;" placeholder="Quantity" onchange="reqCheck(<?php echo $i ?>);" name= "qty[<?php echo $i ?>]" id="qty<?php echo $i ?>"></td>
				<td>
					<select style="width:150px;" id="model<?php echo $i ?>" name="model[<?php echo $i ?>]" >
					<option value="">Select One</option>
					<?php if ($models) { ?>
					<?php foreach ($models as $key => $row) { ?>
					<option value="<?php echo $row['sku']; ?>"><?php echo $row['sku']; ?></option>
					<?php } ?>
					<?php } ?>
				</select>
				</td>
				<td>
					<select id="condition<?php echo $i ?>" name="condition[<?php echo $i ?>]" style="width:150px;">
						<option value="">Select One</option>
						<?php foreach($conditions as $condition):?>
							<option value="<?php echo $condition['id']; ?>"><?php echo $condition['value']; ?></option>
						<?php endforeach;?>
					</select>
				</td>
				</tr>
				<?php $i++;
				 } ?>
				</tbody>
				<tr>
				<td colspan="3" align="center"><input type="Submit" class="button" name="update" value="Submit"></td>
				</tr>


				</table>

				
			</form>		
		</div>	
	</body>
</html>
<script>
function reqCheck($i)
{
	if($( "#qty"+$i ).val()){	
		$( "#condition"+$i ).attr( "required", " " );
		$( "#model"+$i ).attr( "required", " " );
	} else 
	{
		$( "#condition"+$i ).removeAttr("required");
		$( "#model"+$i ).removeAttr("required");
	}
}
function submitMe()
{
	if(document.getElementById('item').value=="")
	{
		alert('Please Select Item to Add');
		return false;
	}
	if(document.getElementById('reason').value=="")
	{
		alert('Please Select Reason to Proceed');
		return false;
	}
	if(document.getElementById('oem_qty').value=="")
	{
		alert('Please input OEM Quantity');
		return false;
	}
	if(document.getElementById('oem_price').value=="")
	{
		alert('Please input OEM Price');
		return false;
	}

	if(document.getElementById('non_oem_qty').value=="")
	{
		alert('Please input Non OEM Quantity');
		return false;
	}
	if(document.getElementById('non_oem_price').value=="")
	{
		alert('Please input Non OEM Price');
		return false;
	}
	if(confirm('Are you sure?'))
	{

	document.getElementById("frm").submit();
}

}
</script>