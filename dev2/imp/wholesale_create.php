<?php
require_once("config.php");
require_once("inc/functions.php");
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
$table = "`oc_wholesale_account`";

if ($_POST['add']) {

	$check1 = $db->func_query_first_cell('SELECT id FROM oc_wholesale_account WHERE `email` = "'.$db->func_escape_string($_POST['personal_email']).'"');
	if ($check1) {
		$_SESSION['message'] = 'Customer Already Exists';
		header("Location:wholesale_create.php");
		exit;
	}
	$password = $db->func_escape_string($_POST['pass']);
	unset($_POST['pass']);
	unset($_POST['conf_password']);
	unset($_POST['add']);
	unset($_POST['attachments']);
	if ($_FILES['attachments']['tmp_name']) {
			$imageCount = 0;
			$count = count($_FILES['attachments']['tmp_name']);

			for ($i = 0; $i < $count; $i++) {
				$uniqid = uniqid();
				$name = explode(".", $_FILES['attachments']['name'][$i]);
				$ext = end($name);

				$destination = str_replace('imp/', 'image/', $host_path) . $uniqid . ".$ext";
				$file = $_FILES['attachments']['tmp_name'][$i];
				if (move_uploaded_file($file, $destination)) {
					$image =  $uniqid . ".$ext";
				}
				

					
			}
		}
	$_POST['date_added'] = date('Y-m-d H:i:s');
	if ($image) { 
		$_POST['business_license'] = $image;
	}
	
	$user_id = $db->func_array2insert($table, $_POST);


	$check = $db->func_query_first_cell('SELECT customer_id FROM oc_customer WHERE `email` = "'.$db->func_escape_string($_POST['personal_email']).'"');
	if (!$check) {
		$oc_cust = array();
		$oc_cust['firstname'] =	$db->func_escape_string($_POST['first_name']);
		$oc_cust['lastname'] =	$db->func_escape_string($_POST['last_name']);
		$oc_cust['email'] =	$db->func_escape_string($_POST['personal_email']);
		$oc_cust['telephone'] =	$db->func_escape_string($_POST['office']);
		$oc_cust['password'] =	md5($password);
		$oc_cust['customer_group_id'] =	'6';
		$oc_cust['status'] = '1';
		$oc_cust['approved'] =	'1';
		$oc_cust['date_added'] =date('Y-m-d H:i:s'); 
		$oc_cust['business_name'] =	$db->func_escape_string($_POST['company_name']);
		$oc_cust_id = $db->func_array2insert("oc_customer", $oc_cust);

		$oc_add = array();
		$oc_add['customer_id'] = $oc_cust_id;
		$oc_add['firstname'] = $db->func_escape_string($_POST['first_name']);
		$oc_add['lastname'] = $db->func_escape_string($_POST['last_name']);
		$oc_add['company'] = $db->func_escape_string($_POST['company_name']);
		$oc_add['address_1'] = $db->func_escape_string($_POST['address']);
		$oc_add['city'] = $db->func_escape_string($_POST['city']);
		$oc_add['postcode'] = $db->func_escape_string($_POST['zip_code']);
	//$oc_add['suite'] = $db->func_escape_string($_POST['suite']);
		$oc_add['contact_telephone_1'] = $db->func_escape_string($_POST['office']);
		$oc_add['contact_telephone_2'] = $db->func_escape_string($_POST['mobile']);
		$oc_add_id = $db->func_array2insert("oc_address", $oc_add);


		$db->db_exec("update oc_customer set address_id = '$oc_add_id' where customer_id = '".$oc_cust_id."'");

		$inv_cust = array();
		$inv_cust['firstname'] =	$db->func_escape_string($_POST['first_name']);
		$inv_cust['lastname'] =	$db->func_escape_string($_POST['last_name']);
		$inv_cust['email'] =	$db->func_escape_string($_POST['personal_email']);
		$inv_cust['city'] =	$db->func_escape_string($_POST['city']);
		$inv_cust['state'] =	$db->func_escape_string($_POST['state']);
		$inv_cust['customer_group'] =	'Wholesale Small';
		$inv_cust['customer_id'] =	$oc_cust_id;
		$inv_cust['telephone'] =	$db->func_escape_string($_POST['office']);
		$inv_cust['address1'] =	$db->func_escape_string($_POST['address']);
		$inv_cust['zip'] =	$db->func_escape_string($_POST['zip_code']);
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
		$emailMessage['number']['value'] = $password;
		$emailMessage['image'] = $host_path . 'images/passwordreset.png' ;
		$emailMessage['message'] = $canned_mail['message'];
		$emailMessage['subject'] = $canned_mail['subject'];
		$emailMessage['title'] = $canned_mail['title'];
		$dataMessage['email'] = $db->func_escape_string($_POST['personal_email']);
		$dataMessage['customer_name'] = $inv_cust['firstname'] . ' ' . $inv_cust['lastname'];

		if (sendEmailDetails ($dataMessage, $emailMessage)) {	
			$_SESSION['message'] = 'Account Created Successfully & Email Sent to Customer';
		} else {
			$_SESSION['message'] = 'Account Created Successfully & Email Not Sent';
		}
		$db->db_exec("update oc_wholesale_account set is_account_created = '1' where id = '".$user_id."'");
	} else {
		$_SESSION['message'] = 'Customer Already Registered';
	}


	header("Location:wholesale_create.php");
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
		<title>Create Wholesale Account | PhonePartsUSA</title>
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
				<h2>Wholesale Account Form</h2>
				<form method="post" action="" enctype="multipart/form-data">
				<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
					<tr>
						<th colspan="4">Personal Information</th>
					</tr>
					<tr>
						<th >First Name <small style="color: red">*</small></th>
						<td ><input required type="text" name="first_name"></td>
						<th >Last Name <small style="color: red">*</small></th>
						<td ><input required  type="text" name="last_name"></td>
					</tr>
					<tr>
						<th>Telephone Office: <small style="color: red">*</small></th>
						<td><input required  type="text" name="office"></td>
						<th>Mobile:</th>
						<td><input type="text" name="mobile"></td>
					</tr>
					<tr>
						<th>Email (Login): <small style="color: red">*</small></th>
						<td><input required  type="text" name="personal_email"></td>
						<th></th>
						<td></td>
					</tr>
					<tr>
						<th colspan="4">Company Information</th>
					</tr>
					<tr>
						<th>Company Name: <small style="color: red">*</small></th>
						<td><input required  type="text" name="company_name"></td>
						<th>Position: <small style="color: red">*</small></th>
						<td><input required  type="text" name="position"></td>
					</tr>
					<tr>
						<th>Address: <small style="color: red">*</small></th>
						<td><input required type="text" name="address"></td>
						<th>Retail Point?</th>
						<td>
							<select name="retail_point">
								<option value="1">Yes</option>
								<option value="0">No</option>
							</select>
						</td>
					</tr>
					<tr>
						<th>Suite: <small style="color: red">*</small></th>
						<td><input required type="text" name="suite"></td>
						<th>Zip Code: <small style="color: red">*</small></th>
						<td><input required type="text" name="zip_code"></td>
					</tr>
					<tr>
						<th>City: <small style="color: red">*</small></th>
						<td><input required type="text" name="city"></td>
						<th>State: <small style="color: red">*</small></th>
						<td><input required type="text" name="state"></td>
					</tr>
					<tr>
						<th>Repairs:</th>
						<td>
						<select name="repairs">
							<option value="1-10 Phones">1-10 Phones</option>
							<option value="20-50 Phones">20-50 Phones</option>
							<option value="50-200 Phones">50-200 Phones</option>
							<option value="200+ Phones">200+ Phones</option>
						</select>
						</td>
						<th>Intrested In:</th>
						<td>
						<select name="intrested">
							<option value="LCD Screens">LCD Screens</option>
							<option value="Touch">Touch</option>
							<option value="Flex Cables">Flex Cables</option>
							<option value="Accessories">Accessories</option>
						</select>
						</td>
					</tr>
					<tr>
						<th>License No:</th>
						<td><input type="text" name="license_no"></td>
						<th>License Image:</th>
						<td><input type="file" name="attachments[]" multiple /></td>
					</tr>
					<tr>
						<th>Password: <small style="color: red">*</small></th>
						<td><input type="password" required name="pass" id="password" value=""></td>
						<th>Confirm Password: <small style="color: red">*</small></th>
						<td><input onblur="checkPass()" required type="password" id="conf_password" name="conf_password" value=""></td>
					</tr>
				</table>
				<br>
				<input type="submit" name="add" value="Submit" class="button" />
				</form>
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