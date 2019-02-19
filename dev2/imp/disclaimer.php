<?php
require_once("auth.php");
require_once("inc/functions.php");
$table = "`inv_signatures`";
$disclaimer = $db->func_query_first('SELECT * FROM '. $table .' WHERE `type` = 1');
if ($_POST['update']) {
	unset($_POST['update']);
	$_POST['date_updated'] = date('Y-m-d H:i:s');
	$db->func_array2update($table, $_POST, '`id` = "'. $disclaimer['id'] .'"');
	$_SESSION['message'] = "Disclaimer Updated";
	header("Location:disclaimer.php");
	exit;
}
if ($_POST['add']) {
	unset($_POST['add']);
	$_POST['date_added'] = date('Y-m-d H:i:s');
	$_POST['type'] = 1;
	$_POST['date_updated'] = date('Y-m-d H:i:s');
	$id = $db->func_array2insert($table, $_POST);
	if ($id) {
		$_SESSION['message'] = "Disclaimer Added";
		header("Location:disclaimer.php");
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
		<title>Disclaimer | PhonePartsUSA</title>
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
				<h2><?= ($disclaimer) ? 'Edit': 'Add';?> Disclaimer</h2>
				<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
					<tr>
						<td>Signature</td>
						<td>
							<textarea name="signature" id="signature" rows="10" cols="80">
								<?= ($disclaimer) ? $disclaimer['signature']: '';?>
							</textarea>
							<script>
								CKEDITOR.replace( 'signature' );
							</script>
						</td>
					</tr>
					<tr>
						<td></td>
						<td><input class="button" type="submit" name="<?= ($disclaimer) ? 'update': 'add';?>" value="<?= ($disclaimer) ? 'Update': 'Submit';?>" /></td>
					</tr>
				</table>
			</form>
		</div>
	</body>