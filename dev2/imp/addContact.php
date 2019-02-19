<?php
require_once("auth.php");
require_once("inc/functions.php");
$perission = 'inv_customer_contact';
$pageName = 'Contact';
$pageLink = 'addContact.php';
$table = '`inv_customer_contacts`';
$table2 = '`inv_contacts_ph`';
$customer_id = $_GET['customer_id'];
$contact_id = (int) $_GET['contact_id'];
$positions = array ('', 'Owner', 'Director', 'Manager', 'Purchasing', 'Sales');
$phTypes = array ('', 'Office Mobile', 'Personal Mobile', 'Landline', 'Office');
page_permission($perission);
if ($contact_id) {
	$data = $db->func_query_first("SELECT * FROM $table WHERE id = '$contact_id'");
	$customer_id = $data['customer_id'];
	$customer_contact_id = $data['id'];
	$dataContacts = $db->func_query("SELECT * FROM $table2 WHERE customer_contact_id = '$customer_contact_id'");
}

if ($_POST['action'] == 'deleteContact') {
	$db->db_exec("DELETE FROM $table2 WHERE id = '".$_POST['id']."'");
	exit;
}

if ($_POST['add']) {
	$contactArray = $_POST['contact'];
	$updateContact = $_POST['contactExt'];
	unset($_POST['contact'], $_POST['contactExt'], $_POST['add']);
	$array = $_POST;
	$array['customer_id'] = $customer_id;
	if ($contact_id) {
		$db->func_array2update($table ,$array, "id = '$contact_id'");
	} else {
		$contact_id = $db->func_array2insert($table, $array);
	}
	foreach ($updateContact as $key => $contact) {
		$contact['customer_contact_id'] = $contact_id;
		$db->func_array2update($table2, $contact, "id = '$key'");
	}
	foreach ($contactArray as $contact) {
		$contact['customer_contact_id'] = $contact_id;
		$db->func_array2insert($table2, $contact);
	}
	$_SESSION['message'] = 'Contact Updated';
	header("Location:$pageLink?customer_id=$customer_id" . "&" . "contact_id=$contact_id");
	exit;
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>

</head>
<body>
	<div align="center">
		<div align="center" style="display: none;"> 
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
			<h2>Add <?= $pageName; ?></h2>
			<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td>First Name</td>
					<td>
						<input required="" type="text" name="first_name" value="<?= $data['first_name']; ?>" onkeyup="checkWhiteSpace(this);" />
					</td>
				</tr>
				<tr>
					<td>Last Name</td>
					<td>
						<input required="" type="text" name="last_name" value="<?= $data['last_name']; ?>" onkeyup="checkWhiteSpace(this);" />
					</td>
				</tr>
				<tr>
					<td>Position</td>
					<td>
						<select required="" name="position">
							<?php foreach ($positions as $position) : ?>
								<option <?php echo ($data['position'] == $position && $position)? 'selected="selected"': ''; ?> value="<?php echo $position; ?>"><?php echo ($position)? $position: '--Select--'; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Email</td>
					<td>
						<input required="" type="text" name="email" value="<?= $data['email']; ?>" onkeyup="verifyEmail(this);" />
					</td>
				</tr>
				<tr>
					<td>Contact [<a href="javascript:void(0);" onclick="addRow();">+</a>]</td>
					<td>
						<table id="dataContacts" align="center" border="0" width="90%" cellpadding="10" cellspacing="0">
							<?php if ($dataContacts) { ?>
							<?php foreach ($dataContacts as $key => $dataContact) : ?>
								<tr>
									<td>
										<select required="" name="contactExt[<?= $dataContact['id']; ?>][type]">
											<?php foreach ($phTypes as $phType) : ?>
												<option <?php echo ($dataContact['type'] == $phType)? 'selected="selected"': ''; ?> value="<?php echo $phType; ?>"><?php echo ($phType)? $phType: '--Select--'; ?></option>
											<?php endforeach; ?>
										</select>
									</td>
									<td><input required="" type="text" name="contactExt[<?= $dataContact['id']; ?>][contact]" value="<?= $dataContact['contact']; ?>" /></td>
									<td><?php if ($key != 0) { ?><a href="javascript:void(0);" onclick="removeContact(this, <?= $dataContact['id']; ?>);">x</a><?php } ?></td>
								</tr>
							<?php endforeach; ?>
							<?php } else { ?>
							<tr>
								<td>
									<select required="" name="contact[0][type]">
										<?php foreach ($phTypes as $phType) : ?>
											<option <?php echo ($dataContact['type'] == $phType && $phType)? 'selected="selected"': ''; ?> value="<?php echo $phType; ?>"><?php echo ($phType)? $phType: '--Select--'; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td><input required="" type="text" name="contact[0][contact]" value="<?= $dataContact['contact']; ?>" /></td>
								<td></td>
							</tr>
							<?php } ?>
						</table>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input class="button" type="submit" name="add" value="Submit" /><a class="button" style="margin-left:10px;" href="<?= $pageLink; ?>">Back</a></td>
				</tr>
			</table>
		</form>
	</div>
	<script>
		var i = 1;
		$(document).ready(function(e) {
			$('.fancybox3').fancybox({ width: '90%' , autoCenter : true , autoSize : true });
			window.parent.loadContacts();
			});

		function checkWhiteSpace (t) {
			if ($(t).val() == ' ') {
				$(t).val('');
			}
		}

		function verifyEmail (t) {
			var email = $(t).val();
			var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
			if (!re.test(email)) {
				$(t).parent().find('.error').remove();
				$(t).parent().append('<span class="error">Enter Correct Email!<span>');
			} else {
				$(t).parent().find('.error').remove();
			}
		}
		function addRow () {
			$clone = $('#dataContacts tr:first-child').clone();
			$clone.find('select').attr('name', 'contact['+ i +'][type]').val('');
			$clone.find('input').attr('name', 'contact['+ i +'][contact]').val('');
			$clone.find('td:last-child').remove();
			$clone.append('<td><a href="javascript:void(0);" onclick="removeContact(this);">x</a></td>');
			$('#dataContacts').append($clone);
			i++;
		}
		function removeContact(t, contact_id) {
			if (contact_id) {
				$.ajax({
					url: '<?php echo $pageLink; ?>',
					type: 'POST',
					dataType: 'json',
					data: {id: contact_id, action: 'deleteContact'},
				}).always(function() {
					$(t).parent().parent().remove();
					window.parent.loadContacts();
				});
			} else {
				$(t).parent().parent().remove();
			}

		}
	</script>
</body>