<?php
require_once("auth.php");
require_once("inc/functions.php");
if ($_SESSION['login_as'] != 'admin') {
	echo "You dont have the permssion to access this page.";
	exit;
	
}
$table = "`inv_reporting_emails`";

$competitor_prices = $db->func_query("Select * from $table where report_type = 'competitor_prices' order by id desc");
$store_credits = $db->func_query("Select * from $table where report_type = 'store_credit' order by id desc");
$paypals = $db->func_query("Select * from $table where report_type = 'paypal' order by id desc");
$sales = $db->func_query("Select * from $table where report_type = 'sales_report' order by id desc");
$shipments = $db->func_query("Select * from $table where report_type = 'shipment_report' order by id desc");
if ($_POST['submit']) {
	$insert = array();
	$insert['first_name'] = $_POST['first_name'];
	$insert['last_name'] = $_POST['last_name'];
	$insert['email'] = $_POST['email'];
	$insert['report_type'] = $_POST['category'];
	//print_r($insert);exit;
	if ($_POST['update'] == '1') {
		$db->func_array2update($table,$insert,"id = '".$_POST['update_user_id']."'");
		$_SESSION['message'] = "List Updated successfully.";
		header("Location:emailing_lists.php");
		exit;
	} else {
		$id = $db->func_array2insert($table, $insert);
		$_SESSION['message'] = "User Added to List successfully.";
		header("Location:emailing_lists.php");
		exit;
	}

}
if((int)$_GET['id'] and $_GET['action'] == 'delete'){
	$delete_email = (int)$_GET['id'];
	$db->db_exec("delete from $table where id = '$delete_email'");
	$_SESSION['message'] = "User Deleted from list successfully.";
	header("Location:emailing_lists.php");
	exit;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Reports Emailing Lists | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>

</head>
<body>
	<div align="center">
		<div align="center"> 
			<?php include_once 'inc/header.php';?>
		</div>
		<?php if ($_SESSION['message']) { ?>
		<div align="center"><br />
			<font color="red">
				<?php
				echo $_SESSION['message'];
				unset($_SESSION['message']);
				?>
				<br />
			</font>
		</div>
		<?php } ?>
		<form action="" method="post" enctype="multipart/form-data">
			<h2>Reports Emailing Lists</h2>
			<table align="center" border="1" width="40%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">

				
				<tr>
					<th>Report Category</th>
					<td>
						<select required id="category" name="category">
							<option value="">Select</option>
							<option value="competitor_prices">Daily Competitor Pricing Report</option>
							<option value="store_credit">Daily Store Credits Report</option>
							<option value="paypal">Daily PayPal Refund Report</option>
							<option value="sales_report">Daily Sales Report</option>
							<option value="shipment_report">Daily Shipping Report</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>First name</th>
					<td>
						<input required id="first_name" style="width: 300px;" type="text" name="first_name" value="">
					</td>
				</tr>
				<tr>
					<th>Last name</th>
					<td>
						<input required id="last_name" style="width: 300px;" type="text" name="last_name" value="">
					</td>
				</tr>
				<tr>
					<th>Email</th>
					<td>
						<input required id="email" style="width: 300px;" type="text" name="email" value="">
						<input type="hidden" id="update" name="update" value="0">
						<input type="hidden" id="update_user_id" name="update_user_id" value="0">
					</td>
				</tr>
				<tr >
					<td align="center" colspan="2"><input class="button" type="submit" name="submit" value="Submit" /></td>
				</tr>
			</table>
		</form>
		<br><br>
		<table align="center" width="100%" >
			<tr>
				<td align="center"><h2>Competitor CSV List</h2></td>
				<td align="center"> <h2>Store Credit List</h2></td>
				<td align="center"> <h2>PayPal Refunds List</h2></td>
			</tr>
			<tr>
				<td>
					<div style="height:250px;width:400px;overflow:auto;">
					<table align="center" border="1" width="90%" cellpadding="5" cellspacing="0" >
						<thead>
							<tr>		
								<td>#</td>
								<th>Name</th>
								<th>Email</th>
								<th>Action</th>

							</tr>
						</thead>
						<tbody>
							<?php $i =1; foreach ($competitor_prices as $user) { ?>
							<tr>
								<td align="center"><?php echo $i; ?></td>
								<td align="center">
									<input type="hidden" id="email<?php echo $user['id']; ?>" value= "<?php echo $user['email'];?>">
									<input type="hidden" id="first_name<?php echo $user['id']; ?>" value= "<?php echo $user['first_name'];?>">
									<input type="hidden" id="last_name<?php echo $user['id']; ?>" value= "<?php echo $user['last_name'];?>">
									<input type="hidden" id="user_category<?php echo $user['id']; ?>" value="<?php echo $user['report_type'];?>">
									<?php echo $user['first_name'] ?> <?php echo $user['last_name']?></td>
									<td><?php echo $user['email'] ?></td>
									<td align="center"><a href="emailing_lists.php?action=delete&id=<?php echo $user['id']?>" onclick="if(!confirm('Are you sure ?')){ return false; }">Delete</a>
										|
										<a href="javascript:void(0);" onclick="updateThis(<?php echo $user['id']; ?>)">Edit</a>
									</td>
								</tr>
								<?php $i++; } ?>
							</tbody>
					</table>
					</div>
				</td>
				<td>
						<div style="height:250px;width:400px;overflow:auto;">
						<table align="center" border="1" width="90%" cellpadding="5" cellspacing="0" >
							<thead>
								<tr>		
									<td>#</td>
									<th>Name</th>
									<th>Email</th>
									<th>Action</th>

								</tr>
							</thead>
							<tbody>
								<?php $i =1; foreach ($store_credits as $user) { ?>
								<tr>
									<td align="center"><?php echo $i; ?></td>
									<td align="center">
										<input type="hidden" id="email<?php echo $user['id']; ?>" value= "<?php echo $user['email'];?>">
										<input type="hidden" id="first_name<?php echo $user['id']; ?>" value= "<?php echo $user['first_name'];?>">
										<input type="hidden" id="last_name<?php echo $user['id']; ?>" value= "<?php echo $user['last_name'];?>">
										<input type="hidden" id="user_category<?php echo $user['id']; ?>" value="<?php echo $user['report_type'];?>">
										<?php echo $user['first_name'] ?> <?php echo $user['last_name']?></td>
										<td><?php echo $user['email'] ?></td>
										<td align="center"><a href="emailing_lists.php?action=delete&id=<?php echo $user['id']?>" onclick="if(!confirm('Are you sure ?')){ return false; }">Delete</a>
											|
											<a href="javascript:void(0);" onclick="updateThis(<?php echo $user['id']; ?>)">Edit</a>
										</td>
									</tr>
									<?php $i++;} ?>
								</tbody>
						</table>
						</div>
				</td>
				<td>
					<div style="height:250px;width:400px;overflow:auto;">
					<table align="center" border="1" width="90%" cellpadding="5" cellspacing="0" >
							<thead>
									<tr>		
										<td>#</td>
										<th>Name</th>
										<th>Email</th>
										<th>Action</th>

									</tr>
								</thead>
								<tbody>
									<?php $i =1;
									foreach ($paypals as $user) { ?>
									<tr>
										<td align="center"><?php echo $i; ?></td>
										<td align="center">
											<input type="hidden" id="email<?php echo $user['id']; ?>" value= "<?php echo $user['email'];?>">
											<input type="hidden" id="first_name<?php echo $user['id']; ?>" value= "<?php echo $user['first_name'];?>">
											<input type="hidden" id="last_name<?php echo $user['id']; ?>" value= "<?php echo $user['last_name'];?>">
											<input type="hidden" id="user_category<?php echo $user['id']; ?>" value="<?php echo $user['report_type'];?>">
											<?php echo $user['first_name'] ?> <?php echo $user['last_name']?></td>
											<td><?php echo $user['email'] ?></td>
											<td align="center"><a href="emailing_lists.php?action=delete&id=<?php echo $user['id']?>" onclick="if(!confirm('Are you sure ?')){ return false; }">Delete</a>
												|
												<a href="javascript:void(0);" onclick="updateThis(<?php echo $user['id']; ?>)">Edit</a>
											</td>
										</tr>
										<?php $i++;
									} ?>
								</tbody>
					</table>
					</div>
				</td>
			</tr>
			<tr>
				<td align="center"><h2>Daily Sales Report List</h2></td>
				<td align="center"> <h2>Daily Shipping Report List</h2></td>
			</tr>
			<tr>
				<td>
					<div style="height:250px;width:400px;overflow:auto;">
					<table align="center" border="1" width="90%" cellpadding="5" cellspacing="0" >
						<thead>
							<tr>		
								<td>#</td>
								<th>Name</th>
								<th>Email</th>
								<th>Action</th>

							</tr>
						</thead>
						<tbody>
							<?php $i =1; foreach ($sales as $user) { ?>
							<tr>
								<td align="center"><?php echo $i; ?></td>
								<td align="center">
									<input type="hidden" id="email<?php echo $user['id']; ?>" value= "<?php echo $user['email'];?>">
									<input type="hidden" id="first_name<?php echo $user['id']; ?>" value= "<?php echo $user['first_name'];?>">
									<input type="hidden" id="last_name<?php echo $user['id']; ?>" value= "<?php echo $user['last_name'];?>">
									<input type="hidden" id="user_category<?php echo $user['id']; ?>" value="<?php echo $user['report_type'];?>">
									<?php echo $user['first_name'] ?> <?php echo $user['last_name']?></td>
									<td><?php echo $user['email'] ?></td>
									<td align="center"><a href="emailing_lists.php?action=delete&id=<?php echo $user['id']?>" onclick="if(!confirm('Are you sure ?')){ return false; }">Delete</a>
										|
										<a href="javascript:void(0);" onclick="updateThis(<?php echo $user['id']; ?>)">Edit</a>
									</td>
								</tr>
								<?php $i++; } ?>
							</tbody>
					</table>
					</div>
				</td>
				<td>
						<div style="height:250px;width:400px;overflow:auto;">
						<table align="center" border="1" width="90%" cellpadding="5" cellspacing="0" >
							<thead>
								<tr>		
									<td>#</td>
									<th>Name</th>
									<th>Email</th>
									<th>Action</th>

								</tr>
							</thead>
							<tbody>
								<?php $i =1; foreach ($shipments as $user) { ?>
								<tr>
									<td align="center"><?php echo $i; ?></td>
									<td align="center">
										<input type="hidden" id="email<?php echo $user['id']; ?>" value= "<?php echo $user['email'];?>">
										<input type="hidden" id="first_name<?php echo $user['id']; ?>" value= "<?php echo $user['first_name'];?>">
										<input type="hidden" id="last_name<?php echo $user['id']; ?>" value= "<?php echo $user['last_name'];?>">
										<input type="hidden" id="user_category<?php echo $user['id']; ?>" value="<?php echo $user['report_type'];?>">
										<?php echo $user['first_name'] ?> <?php echo $user['last_name']?></td>
										<td><?php echo $user['email'] ?></td>
										<td align="center"><a href="emailing_lists.php?action=delete&id=<?php echo $user['id']?>" onclick="if(!confirm('Are you sure ?')){ return false; }">Delete</a>
											|
											<a href="javascript:void(0);" onclick="updateThis(<?php echo $user['id']; ?>)">Edit</a>
										</td>
									</tr>
									<?php $i++;} ?>
								</tbody>
						</table>
						</div>
				</td>
				
			</tr>
		</table>
				<br>
	</div>
			<script type="text/javascript">
				function updateThis(id){
					$('#first_name').val($('#first_name'+id).val());
					$('#last_name').val($('#last_name'+id).val());
					$('#email').val($('#email'+id).val());
					$("#category option[value=" + $('#user_category'+id).val() + "]").prop("selected",true);
					$("#update").val(1);
					$("#update_user_id").val(id);			
				}
			</script>
</body>
