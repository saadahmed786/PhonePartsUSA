<?php

include_once '../auth.php';
include_once '../inc/functions.php';
$vendors = $db->func_query("select id , name as value from inv_users where group_id = 1");
$message = false;
if(@$_POST['add']){
	$vendor = $db->func_escape_string($_POST['vendor']);
	$rejcetedShipment = array();
	$rejcetedShipment ['package_number'] = 'RTV-' . rand();
	$rejcetedShipment ['vendor'] = $vendor;
	$rejcetedShipment ['status'] = 'Pending';
	$rejcetedShipment ['date_added'] = date ( 'Y-m-d H:i:s' );
	$rejcetedShipment ['user_id'] = $_SESSION ['user_id'];
	$last_id = $db->func_array2insert ( 'inv_rejected_shipments', $rejcetedShipment );

	$_SESSION['message'] = "New Rejected PO is created";
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
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
					<td>Vendor:</td>
					<td><?php echo createField("vendor", "vendor" , "select" , '', $vendors);?></td>
				</tr>

				<tr>
					<td colspan="2" align="center"><input type="submit" name="add" value="Submit" /></td>					
				</tr>
			</table>
		</form>		
	</div>	
</body>
</html>