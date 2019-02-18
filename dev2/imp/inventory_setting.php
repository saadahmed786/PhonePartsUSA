<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$pageName = 'Inventory Settings';
$pageLink = 'inventory_setting.php';
$pageSetting = false;
$table = '`oc_setting`';

if($_SESSION['login_as']!='admin'){
	echo 'You dont have permission to manage '. $pageName .'.';
	exit;
}

$data = oc_config('imp_inventory_setting');
//$data = str_replace("'", "", $data);
$values = unserialize($data);

if ($_POST['add']) {

	// $_POST['notification'] = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', "", $_POST['notification']);
	// $array = array(
			
			
	// 		'download_pdf' => $_POST['download_pdf'],
	// 		'download_label'=>$_POST['download_label'],
	// 		'download_report'=>$_POST['download_report']
			
	// 		);
	
	$serialize = serialize($_POST);

	$db->db_exec("UPDATE oc_setting SET value='$serialize',serialized=1 WHERE `key`='imp_inventory_setting'");	
	
	$_SESSION['message']='Setting Updated';
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
			<table align="center" border="1" width="" cellpadding="10" cellspacing="0" style="border:1px solid #ddd;border-collapse:collapse;">
				<tr>
					<td>Print documents via printnode? <br>
					<?php if ($values['download_pdf']) {
						?>
						<input type="checkbox" checked name="download_pdf" />
				<?php	} else {?>
				<input type="checkbox"  name="download_pdf" />
				<?php }?>
					</td>
				</tr>

				<tr>
					<td>Print labels via printnode? <br>
					<?php if ($values['download_label']) {
						?>
						<input type="checkbox" checked name="download_label" />
				<?php	} else {?>
				<input type="checkbox"  name="download_label" />
				<?php }?>
					</td>
				</tr>


					<tr>
					<td>Print Reports via printnode? <br>
					<?php if ($values['download_report']) {
						?>
						<input type="checkbox" checked name="download_report" />
				<?php	} else {?>
				<input type="checkbox"  name="download_report" />
				<?php }?>
					</td>
				</tr>

				

				
			
				<tr>
					<td align="center"><input class="button" type="submit" name="add" value="Update" /></td>
				</tr>
			</table>

			<br>


			<table align="center" border="1" width="" cellpadding="10" cellspacing="0" style="border:1px solid #ddd;border-collapse:collapse;">
			<thead>
			<tr>
			<th>Picking</th>
			<th>Packing</th>
			<th>Shipping</th>
			<th>History</th>
			<th>Adjustment</th>
			</tr>
			</thead>
			<tbody>
			<tr>
			<td>

					<input type="checkbox" name="picking_sku_col" value="1" <?php echo ($values['picking_sku_col']==1?'checked':'');?>>Item SKU<br>
					<input type="checkbox" name="picking_name_col" value="1" <?php echo ($values['picking_name_col']==1?'checked':'');?>>Item Name<br>
					<input type="checkbox" name="picking_service_col" value="1" <?php echo ($values['picking_service_col']==1?'checked':'');?>>Service<br>
					<input type="checkbox" name="picking_order_col" value="1" <?php echo ($values['picking_order_col']==1?'checked':'');?>>Order Total<br>
					<input type="checkbox" name="picking_qty_col" value="1" <?php echo ($values['picking_qty_col']==1?'checked':'');?>>Qty<br>
					<input type="checkbox" name="picking_status_col" value="1" <?php echo ($values['picking_status_col']==1?'checked':'');?>>Status<br>

			</td>
			<td>
			
					<input type="checkbox" name="packing_sku_col" value="1" <?php echo ($values['packing_sku_col']==1?'checked':'');?>>Item SKU<br>
					<input type="checkbox" name="packing_name_col" value="1" <?php echo ($values['packing_name_col']==1?'checked':'');?>>Item Name<br>
					<input type="checkbox" name="packing_service_col" value="1" <?php echo ($values['packing_service_col']==1?'checked':'');?>>Service<br>
					<input type="checkbox" name="packing_order_col" value="1" <?php echo ($values['packing_order_col']==1?'checked':'');?>>Order Total<br>
					<input type="checkbox" name="packing_qty_col" value="1" <?php echo ($values['packing_qty_col']==1?'checked':'');?>>Qty<br>
					<input type="checkbox" name="packing_status_col" value="1" <?php echo ($values['packing_status_col']==1?'checked':'');?>>Status<br>

			</td>

			<td>
			
					<input type="checkbox" name="shipping_sku_col" value="1" <?php echo ($values['shipping_sku_col']==1?'checked':'');?>>Item SKU<br>
					<input type="checkbox" name="shipping_name_col" value="1" <?php echo ($values['shipping_name_col']==1?'checked':'');?>>Item Name<br>
					<input type="checkbox" name="shipping_service_col" value="1" <?php echo ($values['shipping_service_col']==1?'checked':'');?>>Service<br>
					<input type="checkbox" name="shipping_order_col" value="1" <?php echo ($values['shipping_order_col']==1?'checked':'');?>>Order Total<br>
					<input type="checkbox" name="shipping_qty_col" value="1" <?php echo ($values['shipping_qty_col']==1?'checked':'');?>>Qty<br>
					<input type="checkbox" name="shipping_shipping_col" value="1" <?php echo ($values['shipping_shipping_col']==1?'checked':'');?>>Shipping Paid<br>

			</td>

			<td>
			
					<input type="checkbox" name="history_tracking_col" value="1" <?php echo ($values['history_tracking_col']==1?'checked':'');?>>Tracking<br>
					
					<input type="checkbox" name="history_service_col" value="1" <?php echo ($values['history_service_col']==1?'checked':'');?>>Service<br>
					<input type="checkbox" name="history_order_col" value="1" <?php echo ($values['history_order_col']==1?'checked':'');?>>Order Total<br>
					<input type="checkbox" name="history_qty_col" value="1" <?php echo ($values['history_qty_col']==1?'checked':'');?>>Qty<br>
					<input type="checkbox" name="history_shipping_col" value="1" <?php echo ($values['history_shipping_col']==1?'checked':'');?>>Shipping Paid<br>

			</td>

			<td>
			
					<input type="checkbox" name="adjustment_sku_col" value="1" <?php echo ($values['adjustment_sku_col']==1?'checked':'');?>>Item SKU<br>
					<input type="checkbox" name="adjustment_name_col" value="1" <?php echo ($values['adjustment_name_col']==1?'checked':'');?>>Item Name<br>
					<input type="checkbox" name="adjustment_service_col" value="1" <?php echo ($values['adjustment_service_col']==1?'checked':'');?>>Service<br>
					<input type="checkbox" name="adjustment_order_col" value="1" <?php echo ($values['adjustment_order_col']==1?'checked':'');?>>Order Total<br>
					<input type="checkbox" name="adjustment_qty_col" value="1" <?php echo ($values['adjustment_qty_col']==1?'checked':'');?>>Qty<br>
					<input type="checkbox" name="adjustment_status_col" value="1" <?php echo ($values['adjustment_status_col']==1?'checked':'');?>>Status<br>

			</td>



			</tr>
			</tbody>
			</table>
		</form>
	</div>
</body>