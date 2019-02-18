<?php
require_once("auth.php");
require_once("inc/functions.php");
$table = "`oc_wholesale_account`";
if ($_GET['id']) {
	$id = $_GET['id'];
	$account = $db->func_query_first('SELECT * FROM '. $table .' WHERE `id` = "'. $id .'"');
	$account['intrested'] = explode(', ', $account['intrested']);
	if ($account['business_license']) {
		$href = str_replace('imp/', 'image/', $host_path) . $account['business_license'];
		$account['business_license'] = '<a href="'. $href .'" target="_blank">' . $account['business_license'] . '</a>';
	}
	if (!$account) {
		$_SESSION['message'] = "Message Not Found";
		header("Location:wholesale.php");
		exit;
	}
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
						<td><?= $account['personal_email']; ?></td>
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
						<td><?= $account['address']; ?></td>
						<th>Retail Point:</th>
						<td><?= ($account['retail_point']) ? 'Yes' : 'No'; ?></td>
					</tr>
					<tr>
						<th>Suite:</th>
						<td><?= $account['suite']; ?></td>
						<th>Zip Code:</th>
						<td><?= ($account['zip_code'])? $account['zip_code'] : ''; ?></td>
					</tr>
					<tr>
						<th>City:</th>
						<td><?= $account['city']; ?></td>
						<th>State:</th>
						<td><?= $account['state']; ?></td>
					</tr>
					<tr>
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
						<th>License No:</th>
						<td><?= $account['license_no']; ?></td>
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
				<p><a href="wholesale.php">Go Back</a></p>
		</div>
	</body>