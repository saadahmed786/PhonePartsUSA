<?php
require_once("auth.php");
require_once("inc/functions.php");
$table = "`inv_signatures`";
$user_id = $_SESSION['user_id'];
$signature = $db->func_query_first('SELECT * FROM '. $table .' WHERE `user_id` = "'. $user_id .'" AND `type` = 0');
if ($_POST['update']) {
	unset($_POST['update']);
	$_POST['date_updated'] = date('Y-m-d H:i:s');

	if ($_FILES['image']['tmp_name']) {

		$name = explode(".", $_FILES['image']['name']);

		$destination = $path . "files/sign_" . $user_id . ".png";
		$file = $_FILES['image']['tmp_name'];

		move_uploaded_file($file, $destination);
	}

	$db->func_array2update($table, $_POST, '`user_id` = "'. $user_id .'" AND `type` = 0');
	$_SESSION['message'] = "Signature Updated";
	header("Location:signature.php");
	exit;
}
if ($_POST['add']) {
	unset($_POST['add']);
	$_POST['date_added'] = date('Y-m-d H:i:s');
	$_POST['user_id'] = $user_id;
	$_POST['date_updated'] = date('Y-m-d H:i:s');

	if ($_FILES['image']['tmp_name']) {

		$name = explode(".", $_FILES['image']['name']);

		$destination = $path . "files/sign_" . $user_id . ".png";
		$file = $_FILES['image']['tmp_name'];

		move_uploaded_file($file, $destination);
	}

	$id = $db->func_array2insert($table, $_POST);

	if ($id) {
		$_SESSION['message'] = "Signature Added";
		header("Location:signature.php");
		exit;
	} else {
		$_SESSION['message'] = "Error";
	}
}
$shortcodes = array(
	'{{name}}',
	'{{company_info}}'
	);
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Signature | PhonePartsUSA</title>
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
			<form action="" method="post" enctype="multipart/form-data">
				<h2><?= ($signature) ? 'Edit': 'Add';?> Signature</h2>
				<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
					<tr>
						<td>Short Codes</td>
						<td>
							<select id="shortcodes">
								<option value="">Select to Add into Message </option>
								<?php foreach ($shortcodes as $code) { ?>
								<option value="<?= $code ?>"><?= $code; ?></option>
								<?php } ?>
							</select>
							<script type="text/javascript">
								$('#shortcodes').change(function () {
									var shortcode = $(this).val();
									var shortcode = CKEDITOR.dom.element.createFromHtml('<span contenteditable="false">' + $(this).val() + '</span>');
									if (shortcode) {
										CKEDITOR.instances.signature.insertElement(shortcode);
										$('#shortcodes').val('');
									}
								});
							</script>
						</td>
					</tr>
					<tr>
						<td>Signature</td>
						<td>
							<textarea name="signature" id="signature" rows="10" cols="80">
								<?= ($signature) ? $signature['signature']: '';?>
							</textarea>
							<script>
								CKEDITOR.replace( 'signature' );
							</script>
						</td>
					</tr>
					<tr>
						<td>Add Image</td>
						<td>
							<input type="file" name="image" accept="image/x-png"/>
							<?php $src = $path .'files/sign_' . $user_id . ".png"; ?>
							<?= (file_exists($src))? '<img src="'. $host_path .'files/sign_' . $user_id . '.png?'.date().'" />': ''; ?>
						</td>
					</tr>
					<tr>
						<td></td>
						<td><input class="button" type="submit" name="<?= ($signature) ? 'update': 'add';?>" value="<?= ($signature) ? 'Update': 'Submit';?>" /></td>
					</tr>
				</table>
			</form>
		</div>
	</body>