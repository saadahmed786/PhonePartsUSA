<?php
require_once("auth.php");
require_once("inc/functions.php");
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$permission = false;
$pageName = 'Short Code';
$pageLink = 'shortcodes.php?category='. $_GET['category'] . '&';
$pageCreateLink = false;
$pageSetting = false;
$admin = false;
if ($_SESSION['login_as'] == 'admin') {
	$admin = true;
}
$table = '`inv_canned_shortcode`';

if ($permission && !$_SESSION[$permission]) {
	exit;
}

//Deleteing Record
if ($_GET['delete']) {
	$delete = $_GET['delete'];
	$db->db_exec("delete from $table where id = '" . (int) $delete . "'");
	$_SESSION['message'] = $pageName . ' Deleted';
	header("Location:" . $pageLink);
	exit;
}
if ($_GET['edit']) {
	$editId = $_GET['edit'];
	$editData = $db->func_query_first('SELECT * FROM '. $table .' WHERE `id` = "'. $editId .'"');
}
if ($_POST['update']) {
	unset($_POST['update']);
	$db->func_array2update($table, $_POST, '`id` = "'. $_GET['edit'] .'"');
	$_SESSION['message'] = $pageName . 'Updated';
	header("Location:" . $pageLink);
}
if ($_POST['add']) {
	unset($_POST['add']);
	$_POST['date_added'] = date('Y-m-d H:i:s');
	$id = $db->func_array2insert($table, $_POST);
	if ($id) {
		$_SESSION['message'] = $pageName . 'Created';
		header("Location:" . $pageLink);
	} else {
		$_SESSION['message'] = 'Error Try Again';
		header("Location:" . $pageLink);
	}
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

		function allowFloat (t) {
			var input = $(t).val();
			var valid = input.substring(0, input.length - 1);
			if (isNaN(input)) {
				$(t).val(valid);
			}
		}
		function checkWhiteSpace (t) {
			if ($(t).val() == ' ') {
				$(t).val('');
			}
		}

	</script>

</head>
<body>
	<div align="center">
		<div align="center" style="display:none">
			<?php include_once 'inc/header.php';?>
		</div>
		<?php if($_SESSION['message']) { ?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
		<?php } ?>

		<h2><?= $pageName; ?></h2>

		<br>

		<form action="" method="post" enctype="multipart/form-data" <?= ($_GET['edit'])? '' : 'style="display: none;"' ; ?>>
			<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td>Code</td>
					<td>
						<b>{{<?= ($_GET['edit'])? $editData['name'] : '' ; ?>}}</b>
					</td>
				</tr>
				<tr>
					<td>Name</td>
					<td>
						<b><?= ucfirst(str_replace('_', ' ', $editData['name'])); ?></b>
					</td>
				</tr>
				<tr>
					<td>Description</td>
					<td>
						<textarea name="description" onkeyup="checkWhiteSpace(this);"><?= ($_GET['edit'])? $editData['description'] : '' ; ?></textarea>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input class="button" type="submit" name="<?= ($_GET['edit']) ? 'update': 'add';?>" value="<?= ($_GET['edit']) ? 'Update': 'Submit';?>" /></td>
				</tr>
			</table>
		</form>
	</div>
	<div>
		<table align="center" border="1" width="80%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			<thead>
				<tr>
					<th width="5%">#</th>
					<th width="20%">Name</th>
					<th width="20%">Code</th>
					<th width="55%">Description</th>
					<?php if ($admin) { ?>
					<th>Action</th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($db->func_query('SELECT * FROM '. $table .' WHERE `type` = "code" AND `catagory` = "'. $_GET['category'] .'"') as $i => $shortcode) { ?>
				<tr>
					<td><?= ($i) + 1; ?></td>
					<td><?= ucfirst(str_replace('_', ' ', $shortcode['name'])); ?></td>
					<td>{{<?= $shortcode['name']; ?>}}</td>
					<td><?= $shortcode['description']; ?></td>
					<?php if ($admin) { ?>
					<td>
						<a href="<?= $pageLink .'edit='. $shortcode['id']; ?>">Edit</a>
						<!-- <a href="<?= $pageLink .'?delete='. $shortcode['id']; ?>">Delete</a> -->
					</td>
					<?php } ?>
				</tr>
				<?php } ?>
				
			</tbody>
		</table>
	</div>
</body>
</html>