<?php
require_once("../auth.php");
require_once("../inc/functions.php");
$perission = 'product_classification';
$pageName = 'Main Classification';
$pageLink = 'main_classification.php';
$pageCreateLink = false;
$table = '`inv_main_classification`';

if (!$_SESSION[$perission]) {
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
	$_POST['user_id'] = $_SESSION['user_id'];
	$db->func_array2update($table, $_POST, '`id` = "'. $_GET['edit'] .'"');
	$_SESSION['message'] = $pageName . 'Updated';
	header("Location:" . $pageLink);
}
if ($_POST['add']) {
	unset($_POST['add']);
	$_POST['date_added'] = date('Y-m-d H:i:s');
	$_POST['user_id'] = $_SESSION['user_id'];
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
			<?php include_once '../inc/header.php';?>
		</div>
		<?php if($_SESSION['message']) { ?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
		<?php } ?>
		<form action="" method="post" enctype="multipart/form-data">
			<h2>Add <?= $pageName; ?></h2>
			<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td>Name</td>
					<td>
						<input required="" type="text" name="name" value="<?= ($_GET['edit'])? $editData['name'] : '' ; ?>" onkeyup="checkWhiteSpace(this);" />
					</td>
				</tr>
				<tr>
					<td>Status</td>
					<td>
						<select name="status">
							<option <?= ($_GET['edit'] && $editData['status'] == 0)? 'selected="selected"' : '' ; ?> value="0">Disable</option>
							<option <?= ($_GET['edit'] && $editData['status'] == 1)? 'selected="selected"' : '' ; ?> value="1">Enable</option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input class="button" type="submit" name="<?= ($_GET['edit']) ? 'update': 'add';?>" value="<?= ($_GET['edit']) ? 'Update': 'Submit';?>" />
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div>
		<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			<thead>
				<tr>
					<th>#</th>
					<th>Classification</th>
					<th>Date Added</th>
					<th>Status</th>
					<th>Created By</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($db->func_query("SELECT * FROM $table") as $i => $class) { ?>
				<tr>
					<td><?= ($i) + 1; ?></td>
					<td align="center"><?php echo $class['name']; ?></td>
					<td align="center"><?php echo americanDate($class['date_added']); ?></td>
					<td align="center"><?php echo (($class['status'])? 'Enabled': 'Disabled'); ?></td>
					<td align="center"><?php echo get_username($class['user_id']); ?></td>
					<td>
						<a href="<?= $pageLink .'?edit='. $class['id']; ?>">Edit</a>
						<a href="<?= $pageLink .'?delete='. $class['id']; ?>">Delete</a>
					</td>
				</tr>
				<?php } ?>
				
			</tbody>
		</table>
	</div>
</body>