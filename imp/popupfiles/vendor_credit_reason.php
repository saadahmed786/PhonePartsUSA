<?php
include_once '../auth.php';
include_once '../inc/functions.php';

page_permission('create_vendor_credit_manual');

$vendor_id = (int)$_GET['vendor_id'];

// $inv_data = getInventoryDetail($sku);

if($_POST['save']){
	$reason = $db->func_escape_string($_POST['reason']);
	
	$comment = trim($db->func_escape_string($_POST['comment']));

	if($reason)
	{
		$db->db_exec("INSERT INTO inv_vendor_credit_reasons SET reason='".$reason."',comment='".$comment."',user_id='".$_SESSION['user_id']."',date_added='".date('Y-m-d H:i:s')."'");
		$_SESSION['message'] = "Vendor Credit Reason has been added";
	echo "<script>window.location='".$host_path."popupfiles/vendor_credit_reason.php';</script>";
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
		<h2 align="center">Vendor Credit (Reason)</h2>
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
							<td>Reason:</td>
							<td><input type="text" placeholder="Reason Name" required="" name="reason" style="width:90%"></td>
						</tr>

						
							<tr>
							<td>Comment:</td>
							<td><textarea name="comment" style="width:90%;height:100px"></textarea></td>
						</tr>
						<tr>
						<td colspan="2" align="center"><input type="submit" value="Save" class="button" name="save"> <input type="button" class="button button-danger" value="Close" onclick="parent.$.fancybox.close();"></td>
						</tr>
						
					</table>
					
					</form>

					</div>
					
				</form>
			</div>
			
		
	</div>	
</body>
</html>