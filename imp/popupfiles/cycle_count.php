<?php
include_once '../auth.php';
include_once '../inc/functions.php';
$sku = $_GET['sku'];

// $inv_data = getInventoryDetail($sku);
$count = $db->func_query_first_cell("SELECT quantity from oc_product where trim(lower(sku))='".$db->func_escape_string(trim(strtolower($sku)))."'");
// print_r($_POST);
if($_POST['save']){
	$shipment_id = $db->func_escape_string($_POST['shipment_id']);
	$quantity  = (int)$_POST['quantity'];
	$comment = trim($db->func_escape_string($_POST['comment']));
	if($comment)
	{
		// $picked_packed_qty = getPickedPackedQty($sku);
		// echo 'here';exit;
		// $db->db_exec("UPDATE oc_product SET quantity='".$quantity."' where trim(lower(sku))='".trim(strtolower($sku))."'");
		$is_updated = updateOnShelfQty($sku,$quantity);
		if($is_updated)
		{
		makeLedger('',array($sku=>(int)$quantity),$_SESSION['user_id'],'','Stock Adjustment (Cycle Count).',$comment);
			
		$_SESSION['message'] = "Cycle Count is updated";
		}
		else
		{

		$_SESSION['message'] = "Cycle Count NOT updated, please try again";
		}

	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
	}
	else
	{
		// $_SESSION['message'] = 'Cycle count not updated due to missing required fields';
		// echo "<script>window.location.reload();</script>";
		// exit;
	}
	
}

?>
<html>
<body>
	<div align="center" style="display:none"> 
		<?php include_once '../inc/header.php';?>
	</div>
	<div align="center">
		<h2 align="center">Cycle Count</h2>
		<?php
		if($_SESSION['message'])
		{
			?>
			<h5 align="center" style="color:red"><?php echo $_SESSION['message'];?></h5>
			<?php
			unset($_SESSION['message']);
		}
		?>
		
		
			<div id="" class="">
				<form method="post">
					<table width="100%" align="left" style="font-weight:bold">
						<tr>
							<td>On Shelf:</td>
							<td><?php echo $count;?></td>
						</tr>
							<tr>
							<td>On Shelf (Update):</td>
							<td><input type="number" value="<?php echo $count;?>" required="" name="quantity" style="width:90%"></td>
						</tr>

						
							<tr>
							<td>Comment:</td>
							<td><textarea name="comment" required="" style="width:90%;height:100px"></textarea></td>
						</tr>
						<tr>
						<td colspan="2" align="center"><input type="submit" value="Save" class="button" name="save"> <input type="button" class="button button-danger" value="Close" onclick="parent.$.fancybox.close();"></td>
						</tr>
						
					</table>
					<input type="hidden" name="sku" value="<?php echo $sku;?>">
					</form>

					</div>
					<script type="text/javascript">
						function getShipment (t) {
							if ($(t).val()) {
								$.ajax({
									url: 'returns_box_skuadd.php',
									type: 'post',
									dataType: 'json',
									data: {package_number: $(t).val(), action: 'shipment-sku'},
								})
								.always(function(json) {
									$('#shipment-sku').html(json['data']);
									$('input[name=shipment_id]').val(json['shipment_id']);
								});
							}
						}
						function makeReq (t) {
							if ($(t).is(':checked')) {
								$(t).parent().parent().find('input[type=text]').attr('required', 'required');
							} else {
								$(t).parent().parent().find('input[type=text]').removeAttr('required');
							}
						}
					</script>
				</form>
			</div>
			
		
	</div>	
</body>
</html>