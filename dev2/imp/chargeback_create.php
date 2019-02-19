<?php
require_once("auth.php");
require_once("inc/functions.php");
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = 'charge_back';
$pageName = 'Charge Back';
$pageLink = 'chargeback_manage.php';
$pageCreateLink = 'chargeback_create.php';
$pageSetting = 'chargeback_settings.php';
$table = '`inv_chargeback`';
$reqData = array( 'order_id' => '', 'amount' => '', 'reason' => '');
function setRequestData($req) {
	$data = array();
	foreach ($req as $key => $value) {
		$data[$key] = $req[$key];
	}
	return $data;
}
if (!$_SESSION[$perission]) {
	exit;
}

if ($_POST['submit'] == 'getAmount') {
	$info = $db->func_query_first('SELECT * FROM `inv_orders` WHERE `order_id` = "'. $_POST['order'] .'"');
	if ($info) {
		$datajson = array('success' => 1, 'amount' => $info['order_price']);
	} else {
		$datajson = array('error' => 1, 'msg' => 'Order Not Found');
	}
	echo json_encode($datajson);
	exit;
}
if ($_POST['add']) {
	unset($_POST['add']);
	$reqData = setRequestData($_POST);
	$orderData = $db->func_query_first('SELECT `io`.*, `iod`.`address1`, `iod`.`zip`, `oo`.`ip`, `oo`.`telephone` FROM `inv_orders` as `io` INNER JOIN inv_orders_details `iod` on `io`.`order_id` = `iod`.`order_id` left join `oc_order` as `oo` on `io`.`order_id` = `oo`.`order_id` WHERE `io`.`order_id` = "'. $_POST['order_id'] .'"');
	if ($orderData) {
		$_POST['email'] = $orderData['email'];
		$_POST['street_name'] = getfristNumaricChar($orderData['address1']);
		$_POST['zipcode'] = substr($orderData['zip'], 0, 5);
		$_POST['ip'] = $orderData['ip'];
		$_POST['amount'] = $orderData['order_price'];
		$_POST['telephone'] = $orderData['telephone'];
		$_POST['date_added'] = date('Y-m-d H:i:s');
		if (!$db->func_query_first('SELECT * FROM inv_chargeback WHERE order_id = "'. $_POST['order_id'] .'"')) {
			$id = $db->func_array2insert($table, $_POST);
			$log = 'A charge Back was created Against ' . linkToProfile($_POST['to_email']) . ' Order #' . linkToOrder($_POST['order_id']) . ' for Amount ' . $_POST['amount'];
			actionLog($log);
		}


		if ($id) {
			$_SESSION['message'] = $pageName . 'Created';
			$db->func_array2update('`inv_orders`', array('charge_back' => 1), '`order_id` = "'. $orderData['order_id'] .'"');
			if ($_GET['ajax'] == 1) {
				echo json_encode(array('success' => 1));
				exit;
			}
			header("Location:" . $pageLink);
			exit;
		} else {
			if ($_GET['ajax'] == 1) {
				echo json_encode(array('error' => 1));
				exit;
			}
			header("Location:" . $pageLink);
			exit;
		}
	} else {
		if ($_GET['ajax'] == 1) {
			echo json_encode(array('error' => 1));
			exit;
		}
		$_SESSION['message'] = 'Order id not found';
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
			if (isNaN(input) || input == ' ') {
				$(t).val(valid);
			}
		}
		function checkWhiteSpace (t) {
			if ($(t).val() == ' ') {
				$(t).val('');
			}
		}

		function getAmount (t) {
			var order_id = $(t).val();
			var main = $(t).parent();
			if (order_id != '') {
				$.ajax({
					url: '<?= $pageCreateLink; ?>',
					type: 'POST',
					dataType: 'json',
					data: {'order': order_id, 'order': order_id, 'submit': 'getAmount'},
					success: function(json){
						if (json['success']) {
							$('input[name="amount"]').val(json['amount']);
						}
						if (json['error']) {
							main.find('.error').remove();
							main.append('<span class="error">'+ json['msg'] +'</span>');
						}
					}
				});

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
			<h2>Add <?= $pageName; ?></h2>
			<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td>Order ID</td>
					<td>
						<input required="" type="text" name="order_id" value="<?= $reqData['order_id']; ?>" onchange="getAmount(this);" onkeyup="checkWhiteSpace(this);" />
					</td>
				</tr>
				<tr>
					<td>Amount</td>
					<td>
						<input required="" type="text" name="amount" value="<?= $reqData['amount']; ?>" onkeyup="allowFloat(this);" />
					</td>
				</tr>
				<tr>
					<td>Reason</td>
					<td>
						<select required="" name="reason">
							<option value="">-- Select --</option>
							<?php foreach ($db->func_query('SELECT `name` FROM `inv_chargeback_settings`') as $value) { ?>
							<option value="<?= $value['name']; ?>" <?= ($value['name'] == $reqData['reason'])? 'selected="selected"' : '' ; ?>><?= $value['name']; ?></option>
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