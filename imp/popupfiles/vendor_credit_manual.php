<?php
include_once '../auth.php';
include_once '../inc/functions.php';

page_permission('create_vendor_credit_manual');

$vendor_id = (int)$_GET['vendor_id'];

// $inv_data = getInventoryDetail($sku);

if($_POST['save']){
	$vendor_id = (int)($_POST['vendor_id']);
	$amount  = (float)$_POST['amount'];
	$comment = trim($db->func_escape_string($_POST['comment']));

	if($comment)
	{
		$db->db_exec("INSERT INTO inv_vendor_credit_data SET vendor_id='".$vendor_id."',amount='".(float)$amount."',comment='".$comment."',credit_reason_id='".(int)$_POST['credit_reason_id']."',user_id='".$_SESSION['user_id']."',date_added='".date('Y-m-d H:i:s',strtotime($_POST['date']))."'");
		$_SESSION['message'] = "Vendor Credit has been added / deleted";
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
		<h2 align="center">Vendor Credit (Manual)</h2>
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
							<td>Date:</td>
							<td><input type="text" class="datepicker" value="<?php echo date('Y-m-d');?>" required="" name="date" style="width:90%"></td>
						</tr>

							<tr>
							<td>Amount:</td>
							<td><input type="number" step="0.01" value="0.00" required="" name="amount" style="width:90%"></td>
						</tr>

						<tr>
						<td>Reason:</td>
						<td>
						<select name="credit_reason_id" required="" style="width:90%;">
						<option value="">Please Select</option>
						<?php
						$reasons = $db->func_query("SELECT * FROM inv_vendor_credit_reasons ORDER BY LOWER(reason) ASC");
						foreach ($reasons as $reason) {
							?>
							<option value="<?php echo $reason['id'];?>"><?php echo $reason['reason'];?></option>
							<?php
						}
						?>
						</select>

						</td>
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
					</form>

					</div>
					
				</form>
			</div>
			
		
	</div>	
</body>
</html>