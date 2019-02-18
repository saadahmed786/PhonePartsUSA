<?php
require_once("auth.php");
require_once("inc/functions.php");
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
$table = "`oc_wholesale_account`";
if ($_GET['id']) {
	$id = $_GET['id'];
	$account = $db->func_query_first('SELECT * FROM '. $table .' WHERE `id` = "'. $id .'"');
	$account['intrested'] = explode(', ', $account['intrested']);
	if ($account['business_license']) {
		$href = 'https://phonepartsusa.com/image/' . $account['business_license'];
		$account['business_license'] = '<a href="'. $href .'" target="_blank">' . $account['business_license'] . '</a>';
	}
	if (!$account) {
		$_SESSION['message'] = "Message Not Found";
		header("Location:wholesale.php");
		exit;
	}
}

$is_account = $db->func_query_first_cell('SELECT id FROM inv_customers WHERE `email` = "'.$account['personal_email'].'"');

if ($_POST['create_account']) {
	//testObject($_POST);

	$check = $db->func_query_first('SELECT customer_id FROM oc_customer WHERE `email` = "'.$db->func_escape_string($_POST['email']).'"');
	if (!$check) {
	// 	$oc_cust = array();
	// 	$oc_cust['firstname'] =	$db->func_escape_string($_POST['firstname']);
	// 	$oc_cust['lastname'] =	$db->func_escape_string($_POST['lastname']);
	// 	$oc_cust['email'] =	$db->func_escape_string($_POST['email']);
	// 	$oc_cust['telephone'] =	$db->func_escape_string($_POST['telephone']);
	// 	$oc_cust['password'] =	md5($db->func_escape_string($_POST['pass']));
	// 	$oc_cust['customer_group_id'] =	$db->func_escape_string($_POST['customer_group_id']);
	// 	$oc_cust['status'] = '1';
	// 	$oc_cust['approved'] =	'1';
	// 	$oc_cust['date_added'] =date('Y-m-d H:i:s'); 
	// 	$oc_cust['business_name'] =	$db->func_escape_string($_POST['business_name']);
	// 	$oc_cust_id = $db->func_array2insert("oc_customer", $oc_cust);

	// 	$oc_add = array();
	// 	$oc_add['customer_id'] = $oc_cust_id;
	// 	$oc_add['firstname'] = $db->func_escape_string($_POST['firstname']);
	// 	$oc_add['lastname'] = $db->func_escape_string($_POST['lastname']);
	// 	$oc_add['company'] = $db->func_escape_string($_POST['business_name']);
	// 	$oc_add['address_1'] = $db->func_escape_string($_POST['address']);
	// 	$oc_add['city'] = $db->func_escape_string($_POST['city']);
	// 	$oc_add['postcode'] = $db->func_escape_string($_POST['zipcode']);
	// //$oc_add['suite'] = $db->func_escape_string($_POST['suite']);
	// 	$oc_add['contact_telephone_1'] = $db->func_escape_string($_POST['telephone']);
	// 	$oc_add['contact_telephone_2'] = $db->func_escape_string($_POST['mobile']);
	// 	$oc_add_id = $db->func_array2insert("oc_address", $oc_add);


	// 	$db->db_exec("update oc_customer set address_id = '$oc_add_id' where customer_id = '".$oc_cust_id."'");

		$oc_cust_id  = $db->func_query_first_cell("SELECT customer_id FROM oc_customer WHERE lower(email)='".strtolower($_POST['email'])."'");

		$inv_cust = array();
		$inv_cust['firstname'] =	$db->func_escape_string($_POST['firstname']);
		$inv_cust['lastname'] =	$db->func_escape_string($_POST['lastname']);
		$inv_cust['email'] =	$db->func_escape_string($_POST['email']);
		$inv_cust['city'] =	$db->func_escape_string($_POST['city']);
		$inv_cust['state'] =	$db->func_escape_string($_POST['state']);
		$inv_cust['zone_id'] =	$db->func_query_first_cell("SELECT zone_id from oc_zone WHERE code='".$_POST['state']."' and country_id=223");
		$inv_cust['customer_group'] =	$db->func_escape_string($_POST['customer_group']);
		$inv_cust['customer_id'] =	(int)$oc_cust_id;
		$inv_cust['telephone'] =	$db->func_escape_string($_POST['telephone']);
		$inv_cust['address1'] =	$db->func_escape_string($_POST['address']);
		$inv_cust['zip'] =	$db->func_escape_string($_POST['zipcode']);
		$inv_cust['date_added'] =	date('Y-m-d H:i:s');
		$inv_cust_id = $db->func_array2insert("inv_customers", $inv_cust); 

		$canned_mail = $db->func_query_first("Select * from inv_canned_message where type = 'Account Creation' ");
		$dataRep = array();
		$dataRep['customer_name'] = $inv_cust['firstname'] . ' ' . $inv_cust['lastname'];
		$dataRep['email'] = $inv_cust['email'];
		$canned_mail['subject'] = shortCodeReplace($dataRep, $canned_mail['subject']);
		$canned_mail['title'] = shortCodeReplace($dataRep, $canned_mail['title']);
		$canned_mail['message'] = shortCodeReplace($dataRep, $canned_mail['message']);

		$emailMessage['number']['title'] = 'Password';
		$emailMessage['number']['value'] = $db->func_escape_string($_POST['pass']);
		$emailMessage['image'] = $host_path . 'images/passwordreset.png' ;
		$emailMessage['message'] = $canned_mail['message'];
		$emailMessage['subject'] = $canned_mail['subject'];
		$emailMessage['title'] = $canned_mail['title'];
		$dataMessage['email'] = $db->func_escape_string($_POST['email']);
		$dataMessage['customer_name'] = $inv_cust['firstname'] . ' ' . $inv_cust['lastname'];

		// if (sendEmailDetails ($dataMessage, $emailMessage)) {	
		// 	$_SESSION['message'] = 'Account Created Successfully & Email Sent to Customer';
		// } else {
		// 	$_SESSION['message'] = 'Account Created Successfully & Email Not Sent';
		// }

		$_SESSION['message'] = 'Account Created Successfully & Email Not Sent';
		$db->db_exec("update oc_wholesale_account set is_account_created = '1' where id = '".$id."'");
	} else {
		$_SESSION['message'] = 'Customer Already Registered';
	}
	

	header("Location:wholesale_request.php?id=$id");
	exit;
	

}

// if ($_POST['update']) {
// 	unset($_POST['update']);
// 	$_POST['date_modified'] = date('Y-m-d H:i:s');

// 	if ($_FILES['image']['tmp_name']) {

// 		$name = explode(".", $_FILES['image']['name']);

// 		$destination = $path . "files/canned_" . $id . ".png";
// 		$file = $_FILES['image']['tmp_name'];

// 		move_uploaded_file($file, $destination);
// 	}

// 	$db->func_array2update($table, $_POST, '`id` = "'. $id .'"');
// 	$_SESSION['message'] = "Message Updated";
// 	header("Location:wholesale.php");
// 	exit;
// }
// if ($_POST['add']) {
// 	unset($_POST['add']);
// 	$_POST['date_added'] = date('Y-m-d H:i:s');
// 	$_POST['date_modified'] = date('Y-m-d H:i:s');
// 	$id = $db->func_array2insert($table, $_POST);
// 	if ($id) {

// 		if ($_FILES['image']['tmp_name']) {

// 			$name = explode(".", $_FILES['image']['name']);

// 			$destination = $path . "files/canned_" . $id . ".png";
// 			$file = $_FILES['image']['tmp_name'];

// 			move_uploaded_file($file, $destination);
// 		}

// 		$_SESSION['message'] = "Message Added";
// 		header("Location:wholesale.php");
// 		exit;
// 	} else {
// 		$_SESSION['message'] = "Error";
// 	}
// }
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Wholesale Account | PhonePartsUSA</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

		<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
		<script>
			$(document).ready(function(e) {
				$('.fancybox3').fancybox({ width: '90%' , autoCenter : true , autoSize : true });
			});
		</script>
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
				<h2>Request Details</h2>
				<form method="post" action="">
				<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
					<tr>
						<th colspan="4">Personal Information</th>
					</tr>
					<tr>
						<th colspan="2">Name:</th>
						<td colspan="2"><?= $account['first_name'] . ' ' . $account['last_name']; ?></td>
					</tr>
					<tr>
						<th>Office:</th>
						<td><?= $account['office']; ?></td>
						<th>Mobile:</th>
						<td><?= $account['mobile']; ?></td>
					</tr>
					<tr>
						<th>Business Email:</th>
						<td><?= $account['email']; ?></td>
						<th>Personal Email:</th>
						<td><?= linkToProfile($account['personal_email']); ?></td>
					</tr>
					<tr>
						<th colspan="4">Company Information</th>
					</tr>
					<tr>
						<th>Company Name:</th>
						<td><?= $account['company_name']; ?></td>
						<th>Position:</th>
						<td><?= $account['position']; ?></td>
					</tr>
					<tr>
						<th>Address:</th>
						<?php
						if($is_account)
						{
							?>
						<td><?= $account['address']; ?></td>

							<?php
						}
						else
						{
							?>
							<td><input name="address" required value="<?php echo $account['address'];?>" type="text"></td>
							<?php
						}
						?>
						<th>Retail Point:</th>
						<td><?= ($account['retail_point']) ? 'Yes' : 'No'; ?></td>
					</tr>
					<tr>
						<th>Suite:</th>
						<?php
						if($is_account)
						{
							?>
						<td><?= $account['suite']; ?></td>

							<?php
						}
						else
						{
							?>
							<td><input name="suite" required value="<?php echo $account['suite'];?>"></td>
							<?php
						}
						?>
						<th>Zip Code:</th>
						<?php
						if($is_account)
						{
							?>
						<td><?= $account['zip_code']; ?></td>

							<?php
						}
						else
						{
							?>
							<td><input name="zipcode" required value="<?php echo $account['zip_code'];?>"></td>
							<?php
						}
						?>
					</tr>
					<tr>
						<th>City:</th>
						<?php
						if($is_account)
						{
							?>
						<td><?= $account['city']; ?></td>

							<?php
						}
						else
						{
							?>
							<td><input name="city" required value="<?php echo $account['city'];?>"></td>
							<?php
						}
						?>
						<th>State:</th>
						<?php
						if($is_account)
						{
							?>
						<td><?= $account['state']; ?></td>

							<?php
						}
						else
						{
							$_states = $db->func_query("SELECT * FROM oc_zone WHERE country_id=223 and status=1");
							?>
							
							<td><select name="state" required="">
							<option value="">Please Select</option>
							<?php
							foreach($_states as $_state)
							{
								?>
								<option value="<?php echo $_state['code'];?>" <?php echo ($account['state']==$_state['code']?'selected':'');?>><?php echo $_state['name'];?></option>
								<?php
							}
							?>
							</select></td>
							<?php
						}
						?>
					</tr>
					<tr style="display: none">
						<th>Repairs:</th>
						<td><?= $account['repairs']; ?></td>
						<th>Intrested In:</th>
						<td>
						<?php foreach ($account['intrested'] as $value) { ?>
						<div><?= $value; ?></div>
						<?php } ?>
						</td>
					</tr>
					<tr>
						<th>Business Type:</th>
						<td><?= ($account['type_of_business']); ?></td>
						<th># of Locations:</th>
						<td><?= ($account['no_of_locations']); ?></td>
					</tr>
					<tr>
						<th>Average Repairs (Weekly):</th>
						<td><?= $account['repairs']; ?></td>
						<th>Public Hearing:</th>
						<td><?= ($account['how_did_you_hear']); ?></td>
					</tr>
					<tr>
						<th style="display:none">License No:</th>
						<td style="display:none"><?= $account['license_no']; ?></td>
						<th>Intrested In:</th>
						<td>
						<?php foreach ($account['intrested'] as $value) { ?>
						<div><?= $value; ?></div>
						<?php } ?>
						</td>
						<th>License Image:</th>
						<td><?= $account['business_license']; ?></td>
					</tr>
					

					<tr>
						<th>Date Added:</th>
						<td><?= americanDate($account['date_added']); ?></td>
						<th>Date Updated:</th>
						<td><?= americanDate($account['date_updated']); ?></td>
					</tr>
				</table>

				<br>
				<?php 
					$is_account = $db->func_query_first_cell('SELECT id FROM inv_customers WHERE lower(`email`) = "'.strtolower($account['personal_email']).'"');
				if (!$is_account) { ?>
					
				
				<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
					<tr>
						<th colspan="4">Create Account</th>
						<input type="hidden" name="firstname" value="<?php echo $account['first_name']; ?>">
						<input type="hidden" name="lastname" value="<?php echo $account['last_name']; ?>" >
						<input type="hidden" name="email" value="<?php echo $account['personal_email']; ?>" >
						<input type="hidden" name="telephone" value="<?php echo $account['office']; ?>" >
						<input type="hidden" name="mobile" value="<?php echo $account['mobile']; ?>" >
						<input type="hidden" name="business_name" value="<?php echo $account['company_name']; ?>" >
						<!-- <input type="hidden" name="address" value="<?php echo $account['address']; ?>" > -->
						<!-- <input type="hidden" name="suite" value="<?php echo $account['suite']; ?>" >  -->
						<!-- <input type="hidden" name="zipcode" value="<?php echo $account['zip_code']; ?>" > -->
						<!-- <input type="hidden" name="city" value="<?php echo $account['city']; ?>" > -->
						<!-- <input type="hidden" name="state" value="<?php echo $account['state']; ?>" > -->
						<input type="hidden" name="customer_group" value="Wholesale Small"  >
						<input type="hidden" name="customer_group_id" value="6">
					</tr>
					<tr style="display: none">
						<th>Password:</th>
						<td><input type="password" required name="pass" id="password" value=""></td>
						<th>Confirm Password</th>
						<td><input onblur="checkPass()" required type="password" id="conf_password" name="conf_password" value=""></td>
					</tr>
					<tr>
						<th colspan="4">
							<input type="submit" name="create_account" value="Update Account" class="button" />
						</th>
					</tr>
					
				</table>
					
				</form><br>
				<?php } else { ?>
				<h2>Account Created</h2>
				<?php } ?>
				<p><a href="wholesale.php">Go Back</a></p>
		</div>
		<script type="text/javascript">
			function checkPass(){
				var pass = $('#password').val();
				var conf = $('#conf_password').val();
				if (pass == conf) {
					return true;
				} else {
					alert('Password do not match');
					$('#password').val('');
					$('#conf_password').val('');

				}
			}
		</script>
	</body>