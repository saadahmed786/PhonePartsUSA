<?php
require_once("auth.php");
require_once("inc/functions.php");
$perission = 'shipped_order';
$pageName = 'Shipment Track';
$pageLink = 'order_shipped.php';
$table = '`inv_shipstation_transactions`';
$order_id = $_GET['customer_id'];
page_permission($perission);


if ($_POST['add']) {
	unset($_POST['add']);
	$_POST['date_added'] = date('Y-m-d H:i:s');
	$db->func_array2insert($table, $_POST);
	echo json_encode(array('success' => 1));
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
		<div align="center" style="display: none;"> 
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
					<td>Other</td>
					<td>
						<input type="checkbox" onchange="hideShow();" name="other" />
					</td>
				</tr>
				<tr>
					<td>Tracking Number</td>
					<td>
						<input type="text" name="tracking_number" onkeyup="checkWhiteSpace(this);" onchange="setValues(this);" />
					</td>
				</tr>
				<tr>
					<td>Shipping Cost</td>
					<td>
						<input type="text" name="shipping_cost" onkeyup="checkWhiteSpace(this);" onchange="setValues(this);" />
					</td>
				</tr>
				<tr>
					<td>Ship Date</td>
					<td>
						<div style="position: relative;">
							<input type="text" class="datepicker" name="ship_date" onkeyup="checkWhiteSpace(this);" onchange="setValues(this);" />
						</div>
					</td>
				</tr>
				<tr>
					<td>Carrier Code</td>
					<td>
						<select name="carrier_code" id="carrier_code" onchange="setValues(this); changeService(this); $('#service_code').trigger('change');">
							<?php $carrier_code = array("fedex", "ups", "express_1", "endicia"); ?>
							<?php foreach ($carrier_code as $cc) : ?>
								<option value="<?php echo $cc; ?>"><?php echo strtoupper(str_replace('_', ' ', $cc)); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Service Code</td>
					<td>
						<select id="service_code" name="service_code" onchange="setValues(this);">
							<?php $service_code = array (
								"fedex" => array("fedex_2day", "fedex_express_saver", "fedex_standard_overnight", "fedex_priority_overnight", "fedex_home_delivery", "fedex_ground", "fedex_international_economy", "fedex_international_priority"),
								"endicia" => array("usps_first_class_mail", "usps_priority_mail_international"),
								"ups" => array("ups_ground", "ups_next_day_air_saver", "ups_3_day_select"),
								"express_1" => array("usps_priority_mail", "usps_priority_mail_express", "usps_first_class_package_international")
								); ?>
								<?php foreach ($service_code as $k => $cx) : ?>
									<?php foreach ($cx as $cc) : ?>
										<option <?php echo ($k != 'fedex')? 'style="display: none;"': ''; ?> data-type="<?php echo $k; ?>" value="<?php echo $cc; ?>"><?php echo strtoupper(str_replace('_', ' ', $cc)); ?></option>
									<?php endforeach; ?>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td></td>
						<td><input class="button" type="button" value="Submit" onclick="submitForm();" /></td>
					</tr>
				</table>
			</form>
		</div>
		<script>
			function hideShow() {
				if ($('input[name="other"]').prop('checked') == true) {
					$('table tr').hide();
					$('table tr:last-child').show();
					$('table tr:first-child').show();
				} else {
					$('table tr').show();
				}
			}

			function checkWhiteSpace (t) {
				if ($(t).val() == ' ') {
					$(t).val('');
				}
			}

			var values = {};
			values['order_id'] = '<?php echo $_GET['order_id']; ?>';
			values['voided'] = '0';
			values['add'] = '1';
			values['units'] = 'ounces';
			values['is_mapped'] = '1';
			function setValues (t) {
				values[$(t).attr('name')] = $(t).val();
				console.log(values);
			}

			function changeService (t) {
				$('#service_code option').hide();
				$('#service_code option[data-type="'+ $(t).val() +'"]').show();
				$('#service_code').val($('option[data-type="'+ $(t).val() +'"]').attr('value')).trigger('change');
			}

			function submitForm () {
				if ($('input[name="other"]').prop('checked') == true) {
					window.parent.changeOrderStatus('Shipped', 'true');
				} else {
					$.ajax({
						url: '<?php echo $pageLink; ?>',
						type: 'POST',
						dataType: 'json',
						data: values,
					}).always(function(json) {
						if (json['success']) {
							window.parent.changeOrderStatus('Shipped', 'true');
						}
					});
					
				}
			}
			$('#carrier_code').trigger('change');
		</script>
	</body>