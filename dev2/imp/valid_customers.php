<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';

if (!$_SESSION['whitelist_add']) {
	exit;
}
//Deleteing Record
/*
if ($_GET['delete']) {
	$delete = $_GET['delete'];
	$db->db_exec("delete from inv_canned_message where canned_message_id = '" . (int) $delete . "'");
	header("Location:valid_customers.php");
	exit;
}
*/

if ($_POST['action'] == 'checkEmail') {
	$name = $db->func_query_first_cell('SELECT CONCAT(firstname, " ", lastname) FROM `inv_customers` WHERE email = "' . $_POST['email'] . '"');
	if ($name) {
		echo json_encode(array('success' => 1, 'name' => $name));
	} else {
		echo json_encode(array('error' => 1));
	}
	exit;
}

if ($_POST['action'] == 'updateVerify') {
	$customers = $_POST['customers'];
	foreach($customers as $customer) {
		$db->func_query('UPDATE `inv_customers` set white_list = "1" WHERE email="'. $customer .'"');

		$array['type'] = 'customer';
		$array['user'] = $_SESSION['user_id'];
		$array['details'] = $customer;
		$array['details'] = $_POST['reason'];
		$array['date_added'] = date('Y-m-d H:i:s');
		$db->func_array2insert("inv_whitelist_history", $array);
		unset($array);
	}
	echo json_encode(array('success' => 1));
	exit;
}

// Getting Page information
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Valid Customers | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>

</head>
<body>
	<div align="center">
		<div align="center" style="display:none;"> 
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
		<h2>Manage Customers</h2>
		<table>
			<tr>
				<td>
					<input type="text" id="addEmail" name="email" onchange="" onkeydown="$(this).css('border', '1px solid #000'); if (event.keyCode == 13) {addSelected();}" placeholder="Email" />
				</td>
				<td>
					<input class="button" type="button" name="submit" onclick="addSelected();" value="Add"/>
				</td>
				<td>
				<?php $reasons = $db->func_query('SELECT * FROM `inv_whitelist_reasons`'); ?>
					<select class="orderReason">
						<?php foreach ($reasons as $i => $reas) { ?>
						<option value="<?= $reas['id']?>"><?= $reas['name']?></option>
						<?php } ?>
					</select>
				</td>
				<td>
					<input class="button" type="button" name="verify" onclick="verifySelected();" value="Verify"/>
				</td>
			</tr>
		</table>
		<table width="90%" cellpadding="10" align="center">
			<thead>
				<tr>
					<th align="center">Name</th>
					<th align="center">Email</th>
					<th width="10%" align="center">Action</th>
				</tr>
			</thead>
			<tbody class="emails" align="center">
				<!-- Showing All REcord -->
			</tbody>
		</table>
		<br /><br />
	</div>
	<script type="text/javascript">
		var customers = [];
		$(document).ready(function () {
			customers = [];
		});
		function removeEmail (t) {
			var index = customers.indexOf($(t).attr('data-email'));
			customers.splice(index, 1);
			$(t).parent().parent().remove();
			//console.log(customers);
		}
		function addSelected () {
			var email = $('#addEmail').val();
			var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
			if (email == '' || !re.test(email)) {
				$('#addEmail').css('border', '1px solid #f00');
			} else {
				$.ajax({
					url: 'valid_customers.php',
					type: 'POST',
					dataType: 'json',
					data: {'email': email, 'reason': $('.orderReason').val() 'action': 'checkEmail'},
					success: function(json){
						if (json['success']) {
							customers.push(email);
							$('#addEmail').val('');
							$('.emails').append('<tr><td>'+ json['name'] +'</td><td>'+ email +'</td><td><a class="remove" onclick="removeEmail(this);" href="javascript:void(0)" data-email="'+ email +'">remove</a></td></tr>');
						}
						if (json['error']) {
							$('#addEmail').css('border', '1px solid #f00');
						}
					}
				});
			}
		}
		function verifySelected () {
			if(customers.length==0)
			{
				alert('You must add atleast 1 Customer to process');
				return false;
			}
			$.ajax({
				url: 'valid_customers.php',
				type: 'POST',
				dataType: 'json',
				data: {'customers': customers, 'action': 'updateVerify'},
				success: function(json){
					if (json['success']) {
						alert('Successfully verified the customers');
						customers = [];
						$('.emails').html('');
					}
				}
			});
		}
	</script>
</body>