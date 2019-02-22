<?php
include_once '../auth.php';
include_once '../inc/functions.php';

page_permission('create_vendor_credit_rtv');

$vendor_id = (int)$_GET['vendor_id'];

$shipment_id = (int)$_GET['shipment_id'];

$shipment_detail = $db->func_query_first("SELECT * FROM inv_rejected_shipments WHERE id='".$shipment_id."'");
// $inv_data = getInventoryDetail($sku);

if($_POST['save']){
	$shipment_id= (int)$_POST['shipment_id'];
	$vendor_id = (int)($_POST['vendor_id']);
	$amount  = (float)$_POST['amount'];
	$comment = trim($db->func_escape_string($_POST['comment']));
	if($comment)
	{
		$db->db_exec("INSERT INTO inv_vendor_credit_data SET vendor_id='".$vendor_id."',amount='".(float)$amount."',comment='".$comment."',user_id='".$_SESSION['user_id']."',credit_reason_id=1,date_added='".date('Y-m-d H:i:s')."',type='rtv'");

		$db->db_exec("UPDATE inv_rejected_shipments SET is_credit_added=1 WHERE id='".$shipment_id."'");
		$_SESSION['message'] = "Vendor Credit RTV has been added";
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
		<h2 align="center">Vendor Credit (RTV)</h2>
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
							<td>Amount:</td>
							<td><input type="number" step="0.01" value="<?php echo $shipment_detail['amount_credited'];?>" required="" name="amount" style="width:90%"></td>
						</tr>

						
							<tr>
							<td>Comment:</td>
							<td><textarea name="comment" required="" style="width:90%;height:100px"></textarea></td>
						</tr>
						<tr>
						<td colspan="2" align="center"><input type="submit" value="Save" class="button" name="save"> <input type="button" class="button button-danger" value="Close" onclick="parent.$.fancybox.close();"></td>
						</tr>
						
					</table>
					<input type="hidden" name="vendor_id" value="<?php echo $vendor_id;?>">
					<input type="hidden" name="shipment_id" value="<?php echo (int)$_GET['shipment_id'];?>">
					</form>

					</div>
					
				</form>
			</div>
			
		
	</div>	
</body>
</html>