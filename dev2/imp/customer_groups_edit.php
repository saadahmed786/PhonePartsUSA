<?php
require_once("auth.php");
require_once("inc/functions.php");
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = 'customers';
$pageName = 'Customers Group Settings';
$pageLink = 'customer_groups_edit.php';
$pageCreateLink = false;
$pageSetting = false;
$table = '`inv_customer_group_privilege`';

if (!$_SESSION[$perission]) {
	exit;
}

$id = (int)$_GET['id'];
$name = $_GET['name'];

if ($_POST['submit']) {
	$db->db_exec("delete from $table where `group_id` = '" . $id . "'");
	foreach ($_POST['permission'] as $value) {
		if (is_array($value)) {
			foreach ($value as $val) {
				$db->func_array2insert($table, array('group_id' => $id, 'privilege_id' => $val));
			}
		} else {
			$db->func_array2insert($table, array('group_id' => $id, 'privilege_id' => $value));
		}
	}
}

$permission = $db->func_query("SELECT privilege_id FROM $table");
$perm = array();
foreach ($permission as $value) {
	$perm[] = $value['privilege_id'];
}

$types = $db->func_query("SELECT * FROM inv_privilege_type order by privilege_type_id ASC");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<script>

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
	<style type="text/css">
		.contain_list {
			height: 300px;
			overflow-y: scroll;
			border: 2px solid #000;
		}
		.active {
			background-color: #ccc;
		}
	</style>
</head>
<body>
	<div id="main" align="center">
		<div align="center" style="display:none">
			<?php include_once 'inc/header.php';?>
		</div>
		<?php if($_SESSION['message']) { ?>
		<div align="center">
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
		<?php } ?>
		<h2><?= $name . ' Privileges Setting'; ?></h2>
		<form method="POST">
		<table id="list" align="center" border="0" width="100%" cellpadding="10" cellspacing="0" style="border:0px solid #585858;border-collapse:collapse;">
			<tbody valign="top">
				<tr>
					<?php foreach ($types as $k => $type) { ?>
					<td style="border-right: 1px solid;">
						<div>
							<h2><?php echo $type['name']; ?></h2>
							<table align="center" border="0" width="100%" cellpadding="10" cellspacing="0" style="border:0px solid #585858;border-collapse:collapse;">
								<tbody valign="top">
									<?php foreach ($db->func_query("SELECT * FROM inv_privilege WHERE privilege_type_id = '". $type['privilege_type_id'] ."'") as $pk => $privilege): ?>
										<tr>
											<td>
												<label>
													<?php if ($type['type'] == 'single') { ?>
													<input type="radio" name="permission[<?php echo $type['privilege_type_id']; ?>]" <?php echo (in_array($privilege['privilege_id'], $perm))? 'checked=""': ''; ?> value="<?php echo $privilege['privilege_id']; ?>">
													<?php } else { ?>
													<input type="checkbox" name="permission[<?php echo $type['privilege_type_id']; ?>][]" <?php echo (in_array($privilege['privilege_id'], $perm))? 'checked=""': ''; ?> value="<?php echo $privilege['privilege_id']; ?>">
													<?php } ?>
													<?php echo $privilege['name']; ?>
												</label>
											</td>
										</tr>
									<?php endforeach ?>
								</tbody>
							</table>
						</div>
					</td>
					<?php } ?>
				</tr>
			</tbody>
		</table>
		<div align="center">
			<input type="submit" class="button" name="submit" value="Update"></input>
		</div>
		</form>
	</div>
</body>
</html>