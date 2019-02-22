<?php
require_once("auth.php");
require_once("inc/functions.php");
$table = "`oc_wholesale_setting`";
$id = 1;
$setting = $db->func_query_first('SELECT * FROM '. $table .' WHERE `id` = "'. $id .'"');

if ($_POST['update']) {
	unset($_POST['update']);
	$_POST['date_updated'] = date('Y-m-d H:i:s');

	$db->func_array2update($table, $_POST, '`id` = "'. $id .'"');
	$_SESSION['message'] = "Setting Updated";
	header("Location:wholesale.php");
	exit;
}
if ($_POST['add']) {
	unset($_POST['add']);
	$_POST['date_added'] = date('Y-m-d H:i:s');
	$id = $db->func_array2insert($table, $_POST);
	if ($id) {
		$_SESSION['message'] = "Setting Added";
		header("Location:wholesale.php");
		exit;
	} else {
		$_SESSION['message'] = "Error";
	}
}
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
		<form action="" method="post" enctype="multipart/form-data">
			<h2><?= ($setting) ? 'Edit': 'Add';?> setting</h2>
			<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td>Text 1</td>
					<td>
						<textarea name="text_1" id="text_1" rows="10" cols="80">
							<?= ($setting) ? $setting['text_1']: '';?>
						</textarea>
						<script>
							CKEDITOR.replace( 'text_1' );
						</script>
					</td>
				</tr>
				<tr>
					<td>Text 2</td>
					<td>
						<textarea name="text_2" id="text_2" rows="10" cols="80">
							<?= ($setting) ? $setting['text_2']: '';?>
						</textarea>
						<script>
							CKEDITOR.replace( 'text_2' );
						</script>
					</td>
				</tr>
				<tr>
					<td>Text 3</td>
					<td>
						<textarea name="text_3" id="text_3" rows="10" cols="80">
							<?= ($setting) ? $setting['text_3']: '';?>
						</textarea>
						<script>
							CKEDITOR.replace( 'text_3' );
						</script>
					</td>
				</tr>
				
				<tr>
					<td></td>
					<td><input class="button" type="submit" name="<?= ($setting) ? 'update': 'add';?>" value="<?= ($setting) ? 'Update': 'Submit';?>" /></td>
				</tr>
			</table>
		</form>
		<p><a href="wholesale.php">Go Back</a></p>
	</div>
</body>