<?php
include_once '../auth.php';
include_once '../inc/functions.php';

page_permission('vendor_po');

$vpo_id = (int)$_GET['vpo_id'];

// $inv_data = getInventoryDetail($sku);

if($_POST['save']){
	$vpo_id = (int)($_POST['vpo_id']);
	$vendor_id = $db->func_query_first_cell("SELECT vendor FROM inv_vendor_po WHERE id='".$vpo_id."'");
	$amount  = (float)$_POST['amount'];
	
	$reference = trim($db->func_escape_string($_POST['reference']));
	
		$db->db_exec("UPDATE inv_vendor_po SET amount_refunded='".$amount."' WHERE id='".$vpo_id."'");



		$status_comment = 'Refund $'.number_format($amount,2).' has been against ref # '.($reference?$reference:'N/A').'';

		addComment('vendor_po',array('id' => $vpo_id, 'comment' => $status_comment));


		$db->db_exec("INSERT INTO inv_vendor_credit_data SET vendor_po_id='".$vpo_id."', vendor_id='".$vendor_id."',amount='".(float)$amount."',comment='".$status_comment."',user_id='".$_SESSION['user_id']."',date_added='".date('Y-m-d H:i:s')."'");
		$_SESSION['message'] = "PO refund has been made";
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
	
	
}

?>
<html>
<body>
	<div align="center" style="display:none"> 
		<?php include_once '../inc/header.php';?>
	</div>
	<div align="center">
		<h2 align="center">Vendor PO (Refund)</h2>
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
							<td><input type="number" step="0.01" value="0.00" required="" name="amount" style="width:90%"></td>
						</tr>

						

						

						
							<tr>
							<td>Reference #:</td>
							<td><input type="text" name="reference" placeholder="Reference # against the Payment"  style="width:90%"></textarea></td>
						</tr>
						<tr>
						<td colspan="2" align="center"><input type="submit" value="Save" class="button" name="save"> <input type="button" class="button button-danger" value="Close" onclick="parent.$.fancybox.close();"></td>
						</tr>
						
					</table>
					<input type="hidden" name="vpo_id" value="<?php echo $vpo_id;?>">
					</form>

					</div>
					
				</form>
			</div>
			
		
	</div>	
</body>
</html>