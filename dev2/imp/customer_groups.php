<?php
require_once("auth.php");
require_once("inc/functions.php");
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = 'customers';
$pageName = 'Customers Group Settings';
$pageLink = 'customer_groups.php';
$pageCreateLink = false;
$pageSetting = false;
$table = '`oc_customer_group`';

if (!$_SESSION[$perission]) {
	exit;
}

$groups = $db->func_query("SELECT a.customer_group_id, b.name FROM $table AS a INNER JOIN `oc_customer_group_description` AS b ON (a.customer_group_id = b.customer_group_id) WHERE b.name <> '' order by sort_order ASC");

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
		<div align="center">
			<?php include_once 'inc/header.php';?>
		</div>
		<?php if($_SESSION['message']) { ?>
		<div align="center">
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
		<?php } ?>
		<h2><?= $pageName; ?></h2>
		<a class="fancyboxX3 fancybox.iframe button" href="addedit_privileges.php">Privileges Settings</a>
		<a class="fancyboxX3 fancybox.iframe button" href="addedit_privileges_types.php">Privileges Type Settings</a>
		<table align="center" border="0" width="50%" cellpadding="10" cellspacing="0" style="border:0px solid #585858;border-collapse:collapse;">
			<tr>
				<td>
					<div class="contain_list">
						<table id="list" align="center" border="0" width="100%" cellpadding="10" cellspacing="0" style="border:0px solid #585858;border-collapse:collapse;">
							<?php foreach ($groups as $k => $group) { ?>
							<tr group-id="<?php echo $group['customer_group_id']; ?>">
								<td><?php echo $group['name']; ?></td>
							</tr>
							<?php } ?>
						</table>
					</div>
					<br>
					<div align="center">
						<a href="" style="display: none;" class="linktoedit fancyboxX3 fancybox.iframe button">Edit Return Privileges</a>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<script type="text/javascript">
		$('#list').on('click', 'tr', function(event) {
			event.preventDefault();
			$('.active').removeClass('active');
			$(this).addClass('active');
			$('.linktoedit').attr('href', 'customer_groups_edit.php?id=' + $(this).attr('group-id') + '&name=' + $(this).find('td').text()).show();
		});
	</script>
</body>
</html>