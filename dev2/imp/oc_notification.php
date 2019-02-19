<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$pageName = 'OC Notification';
$pageLink = 'oc_notification.php';
$pageSetting = false;
$table = '`oc_setting`';

if($_SESSION['login_as']!='admin'){
	echo 'You dont have permission to manage '. $pageName .'.';
	exit;
}

$data = oc_config('oc_notification');
//$data = str_replace("'", "", $data);
$values = unserialize($data);

if ($_POST['add']) {
	$_POST['notification'] = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', "", $_POST['notification']);
	$array = array(
			'start_date' => $_POST['start_date'],
			'finish_date' => $_POST['finish_date'],
			'show' => $_POST['show'],
			'notification' => $_POST['notification']);
	$serialize = serialize($array);

	$db->db_exec("UPDATE oc_setting SET value='$serialize',serialized=1 WHERE `key`='oc_notification'");	
	
	$_SESSION['message']='Notification Updated';
	header("Location:" . $pageLink);
	exit;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<script>
		$(document).ready(function(e) {
			$('.fancybox3').fancybox({ width: '90%' , autoCenter : true , autoSize : true });
		});
		function checkWhiteSpace (t) {
			if ($(t).val() == ' ') {
				$(t).val('');
			}
		}
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
			<h2>Edit <?= $pageName; ?></h2>
			<table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td>Show ON Website?: <br>
					<?php if ($values['show']) {
						?>
						<input type="checkbox" checked name="show" />
				<?php	} else {?>
				<input type="checkbox"  name="show" />
				<?php }?>
					</td>
				</tr>
				<tr>
					<td>Notification<br>
						<textarea cols="30" rows="3" style="width: 95%" id="notification" onblur="checkWhiteSpace(this)" name="notification"><?php echo trim($values['notification']); ?></textarea>
					</td>
				</tr>
				<tr>
					<td>
					Start Date: <br>
						<input type="text" data-type="date" value="<?php echo $values['start_date']; ?>" name="start_date" id="start_date" />
					</td>
				</tr>
				<tr>
					<td>
					Finish Date: <br>
						<input type="text" data-type="date" value="<?php echo $values['finish_date']; ?>" name="finish_date" id="finish_date" />
					</td>
				</tr>
				<tr>
					<td align="center"><input class="button" type="submit" name="add" value="Update" /></td>
				</tr>
			</table>
		</form>
	</div>
</body>