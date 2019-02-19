<?php

include_once '../auth.php';

$message = false;
if(@$_POST['add']){
	$package_number = $db->func_escape_string($_POST['package_number']);
	$package_number_exist = $db->func_query_first("select id from inv_buyback_boxes where package_number = '$package_number'");
	if(!$package_number_exist){
		$rejcetedShipment = array();
		$rejcetedShipment ['package_number'] = $package_number;
		$rejcetedShipment ['status'] = 'Issued';
		$rejcetedShipment ['date_added'] = date ( 'Y-m-d H:i:s' );
		$rejcetedShipment ['user_id'] = $_SESSION ['user_id'];
		$last_id = $db->func_array2insert ( 'inv_buyback_boxes', $rejcetedShipment );
		
		$_SESSION['message'] = "New Box is created";
		echo "<script>window.close();parent.window.location.reload();</script>";
		exit;
	}
	else{
		$message = "Package is already exist.";
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
						<td>Package Number:</td>
						<td><input type="text" name="package_number" value="" required /></td>					
					</tr>
					
					<tr>
						<td colspan="2" align="center"><input type="submit" name="add" value="Submit" /></td>					
					</tr>
				</table>
			</form>		
		</div>	
	</body>
</html>