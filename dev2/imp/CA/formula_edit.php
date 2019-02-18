<?php

include_once '../auth.php';

$message = false;
$ca_credential_id = (int)$_GET['id'];

if(@$_POST['update']){
	$formula = $_POST['formula'];
	$db->db_exec("update ca_credential SET formula = '$formula', datemodification = '".date('Y-m-d H:i:s')."' where id = '$ca_credential_id'");	
	$_SESSION['message'] = "Keys updated";
	
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
}

$ca_credential = $db->func_query_first("select * from ca_credential where id = '$ca_credential_id'");
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
						<td>Account ID:</td>
						<td><?php echo $ca_credential['prefix']. " ". $ca_credential['account_id'];?></td>					
					</tr>
					
					<tr>
						<td>markup:</td>
						<td><input type="text" name="formula" value="<?php echo $ca_credential['formula']?>" required /></td>					
					</tr>
					
					<tr>
						<td colspan="2" align="center"><input type="submit" name="update" value="Submit" /></td>					
					</tr>
				</table>
			</form>		
		</div>	
	</body>
</html>