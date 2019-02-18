<?php
require_once("auth.php");
require_once("inc/functions.php");
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = 'ignore_pricing';
$pageName = 'Ignore Pricing';
$pageLink = 'ignore_price_update.php';
$pageCreateLink = false;
$pageSetting = false;
$table = '`oc_product`';

if (!$_SESSION[$perission]) {
	exit;
}

//Deleteing Record
if ($_POST['remove']) {

	$update = $db->db_exec('UPDATE ' . $table . ' set ignore_up = "0" WHERE product_id = "'. (int)($_POST['id']) .'"');

	$sk = $db->func_query_first('SELECT * FROM oc_product where product_id = "'. (int)($_POST['id']) .'"');
	$log = linkToProduct($sk['sku']) . 'removed From' . ' Ignore List';
	actionLog($log);

	$json = array('success' => 1);

	echo json_encode($json);
	exit;
}
if ($_POST['ignore']) {
	if ($_POST['sku']) {
		$update = $db->db_exec('UPDATE ' . $table . ' set ignore_up = "1" WHERE sku = "'. $db->func_escape_string($_POST['sku']) .'"');

		$log = linkToProduct($_POST['sku']) . 'added to' . ' Ignore List';
		actionLog($log);
	}
	if ($update) {
		$json = array('success' => 1);
	} else {
		$json = array('error' => 1, 'msg' => 'Product not found');
	}

	echo json_encode($json);
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>

</head>
<body>
	<div align="center">
		<div align="center" style="display:none">
			<?php include_once 'inc/header.php';?>
		</div>
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
		<?php if($_SESSION['message']) { ?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
		<?php } ?>
		<form action="" method="post" enctype="multipart/form-data">
			<h2>Add <?= $pageName; ?></h2>
			<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td>SKU</td>
					<td>
						<input required="" id="p_sku" type="text" name="sku" onkeypress="checkWhiteSpace(this);" />
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input class="button" type="button" onclick="addProduct(this);" name="ignore" value="Add to Ignore" /></td>
				</tr>
			</table>
		</form>
	</div>
	<div>
		<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			<thead>
				<tr>
					<th>#</th>
					<th>SKU</th>
					<th>Name</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody id="p_data">
				<?php foreach ($db->func_query('SELECT a.product_id, a.sku, b.name FROM oc_product a, oc_product_description b WHERE a.product_id = b.product_id AND ignore_up = 1') as $i => $row) { ?>
				<tr>
					<td><?= ($i) + 1; ?></td>
					<td><?= $row['sku']; ?></td>
					<td><?= $row['name']; ?></td>
					<td>
						<a href="javascript:void(0);" onclick="removeProduct(this, '<?= $row['product_id']; ?>')">Remove</a>
					</td>
				</tr>
				<?php } ?>
				
			</tbody>
		</table>
	</div>
	<script type="text/javascript">
		function addProduct (t) {
			var sku = $('#p_sku').val();
			$.ajax({
				url: '<?= $pageLink; ?>',
				type: 'POST',
				dataType: 'json',
				data: {'sku': sku, 'ignore': 'ignore'},
				success: function(json){
					if (json['success']) {
						window.location.reload();
					}
					if (json['error']) {
						alert(json['msg']);
					}
				}
			});
		}

		function removeProduct (t, product_id) {
			$.ajax({
				url: '<?= $pageLink; ?>',
				type: 'POST',
				dataType: 'json',
				data: {'id': product_id, 'remove': 'remove'},
				success: function(json){
					if (json['success']) {
						$(t).parent().parent().remove();
					}
				}
			});
		}
	</script>
</body>