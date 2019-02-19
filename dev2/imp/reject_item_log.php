<?php
require_once("auth.php");
require_once("inc/functions.php");
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = 'user_id';
$pageName = $_GET['reject_item_id'] . ' History';
$pageLink = 'reject_item_log.php';
$pageCreateLink = false;
$pageSetting = false;
$table = '`inv_rj_shipment_items_log`';
$logs = $db->func_query('SELECT * FROM ' . $table . ' WHERE reject_item_id = "'. $_GET['reject_item_id'] .'"');
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
	</div>
	<div>
		<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			<tbody>
				<?php foreach ($logs as $i => $row) { ?>
				<tr>
					<td><?= $row['log']; ?></td>
					<td><?= americanDate($row['date_added']); ?></td>
					<td><?= get_username($row['user_id']); ?></td>
				</tr>
				<?php } ?>
				
			</tbody>
		</table>
	</div>
</body>