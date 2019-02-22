<?php
include_once '../auth.php';
include_once '../inc/functions.php';
$type = $_GET['type'];
if($_POST['save']){
	$deposit_id_check = $db->func_query_first_cell("SELECT deposit_id from inv_deposits where trim(lower(name))='".trim(strtolower($_POST['name']))."' and deposit_type='".$_POST['deposit_type']."'");
		
	if(strlen(trim($_POST['name']))>='5' && !$deposit_id_check && (float)$_POST['amount']>0.00)
	{


		unset($_POST['save']);
		$data = $_POST;
		$data['status']='open';

		$data['user_id'] = $_SESSION['user_id'];
		$data['date_added'] = date("Y-m-d H:i:s");

		$db->func_array2insert("inv_deposits", $data);
	// $deposit_date = $_POST['deposit_date'];

		
		$_SESSION['message'] = ucfirst($_POST['deposit_type'])." Deposit is Added";
		

		echo "<script>window.close();parent.window.location.reload();</script>";
		exit;
	}
	else
	{
		$_GET['type']  = $_POST['deposit_type'];
		echo "<script>alert('Please provide a deposit details: Valid and Unique Deposit # or Valid Amount ');</script>";

	}
	
	
}

?>
<html>
<body>
	<div align="center" style="display:none"> 
		<?php include_once '../inc/header.php';?>
	</div>
	<div align="center">
		<h2 align="center">Add <?php echo ($_GET['type']?ucfirst($_GET['type']):'Bank');?> Deposit</h2>
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
						<td>Deposit Date:</td>
						<td><input type="date"  required="" value="<?php echo $_POST['deposit_date'];?>" name="deposit_date" style="width:90%"></td>
					</tr>
					<tr>
						<td>Deposit #:</td>
						<td><input type="text"  required="" value="<?php echo $_POST['name'];?>" name="name" style="width:90%"></td>
					</tr>

					<tr>
						<td>Amount:</td>
						<td><input type="number"   name="amount" value="<?php echo (float)$_POST['amount'];?>" step=".01" style="width:90%"></td>
					</tr>

					<tr>
						<td>Deposited By:</td>
						<td>
							<select name="deposited_by" required="">
								<option value="">Select User</option>
								<?php
								$users = $db->func_query("SELECT id,name from inv_users WHERE status=1 and group_id<>1 order by lower(name)");
								foreach($users as $user)
								{
									?>
									<option value="<?php echo $user['id'];?>" <?php echo ($user['id']==$_POST['deposited_by']?'selected':'');?>><?php echo $user['name'];?></option>
									<?php
								}
								?>
							</select>
							<input type="hidden" name="deposit_type" value="<?php echo $_GET['type'];?>">

						</td>
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