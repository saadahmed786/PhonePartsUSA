<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = false;
$pageName = 'Admin User';
$pageLink = 'admin_users.php';
$pageCreateLink = 'admin_users_edit.php';
$pageSetting = false;
$table = '`admin`';
$id = $_GET['id'];
$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
if($_SESSION['login_as'] != 'admin'){
	echo 'You dont have permission to manage users.';
	exit;
}

if ($id) {
	$reqData = $db->func_query_first("SELECT * FROM $table WHERE id = '$id'");
}

if ($_POST['add']) {
	$reqData = $_POST;
	unset($_POST['add']);
	if ($id) {
		$user = $db->func_query_first("SELECT * FROM $table WHERE id = '$id'");
		if (!$_POST['password']) {
			unset($_POST['password'], $user['password']);
		}
		unset($user['dateofmodifications'], $user['g_access_token'], $user['id']);
		$result = array_diff($user,$_POST);
		$_POST['dateofmodifications'] = date('Y-m-d H:i:s');

		$db->func_array2update($table, $_POST, "id = '$id'");

		foreach ($result as $key => $value) {
			$x = $_POST[$key];
			$log .= "<strong>$key</strong>: $value <strong>To</strong>: $x <br>";
		}
		if ($log) {
			$log = 'Admin User Details updated<br>' . $log;
		}
	} else {
		$_POST['dateofmodifications'] = date('Y-m-d H:i:s');
		$id = $db->func_array2insert($table, $_POST);
		$log = 'Admin User Created <strong>' . $_POST['name'] . '</strong>';
	}

	$_SESSION['message'] = $log;
	if ($log) {
		actionLog($log);
	}

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

		function allowFloat (t) {
			var input = $(t).val();
			var valid = input.substring(0, input.length - 1);
			if (isNaN(input) || input == ' ') {
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
			<h2><?php echo ($id)? 'Edit': 'Add';?> <?= $pageName; ?></h2>
			<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td>Name</td>
					<td>
						<input required="" type="text" name="name" value="<?= $reqData['name']; ?>" onkeyup="checkWhiteSpace(this);" />
					</td>
				</tr>
				<tr>
					<td>Email</td>
					<td>
						<input required="" type="text" name="email" value="<?= $reqData['email']; ?>" onkeyup="checkWhiteSpace(this);" />
					</td>
				</tr>
				<tr>
					<td>Password</td>
					<td>
						<input <?php echo ($id)?'required=""':''; ?> type="text" name="password" value="" onkeyup="checkWhiteSpace(this);" autocomplete="off"/>
					</td>
				</tr>
				<tr>
					<td>Gmail</td>
					<td>
						<input type="text" name="gmail" value="<?= $reqData['gmail']; ?>" onkeyup="checkWhiteSpace(this);" />
					</td>
				</tr>
				<tr>
					<td>Status</td>
					<td>
						<?php $select = array('' => '--Select--', 'Enable' => 'Enable', 'Disable' => 'Disable')?>
						<select name="status" required="">
							<?php foreach ($select as $key => $value) { ?>
							<option <?php echo ($reqData['status'] == $key) ? 'selected="selected"': ''; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input class="button" type="submit" name="add" value="Submit" /><a class="button" style="margin-left:10px;" href="<?= $pageLink; ?>">Back</a></td>
				</tr>
			</table>
		</form>
	</div>
</body>