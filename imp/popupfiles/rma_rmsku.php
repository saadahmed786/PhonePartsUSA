<?php

include_once '../auth.php';

$message = false;
$return_id = $_GET['return_id'];

if(@$_POST['add']){
	$return_item_id = (int)$_GET['id'];
	$remove_reason  = $db->func_escape_string($_POST['remove_reason']);
	$db->db_exec("update inv_return_items set removed = 1 , remove_reason = '$remove_reason'  where id = '$return_item_id'");

	$_SESSION['message'] = "SKU removed from returns";
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
						<td>Remove Reason:</td>
						<td>
							<textarea rows="3" cols="20" name="remove_reason" required></textarea>
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