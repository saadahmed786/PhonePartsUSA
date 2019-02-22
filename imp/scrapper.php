<?php
require_once("auth.php");
require_once("inc/header.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = 'product_competitive_pricing';
$pageName = 'Scrape History';
$pageLink = 'scraping_dashboard.php';
$pageCreateLink = false;
$table = '`inv_product_price_scrap`';

if (!$_SESSION[$perission]) {
	exit;
}
if($_POST['delete']){
	if(count($_POST['delete_sku']) > 0){
		foreach($_POST['delete_sku'] as $delete_sku){
			$db->db_exec( "update inv_product_price_scrap set is_deleted = '1' where sku = '$delete_sku' " );
		}
		$_SESSION['message'] = "Items deleted successfully.";
	}	
	else{
		$_SESSION['message'] = "Select at least one sku to delete.";
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
	</div>
	<div align="center"><h2>Competitor Pricing</h2></div>
	
	<div align="center">
	<a class="button fancybox fancybox.iframe" href="<?php echo $host_path;?>popupfiles/import_products_scrap_url.php">Import Scrap Url(s)</a>
	<a class="button" href="export_competitor_pricing.php?m=1">Export Competitor Pricing</a>

	</div><br>
	<div align="center">
	<form method="post" action="">
	<input type="submit" class="button" name="delete" value="Delete">
	</div>
	<br>
	<div>
		<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			<thead>
				<tr>
					<th rowspan="2"></th>
					<th rowspan="2">SKU</th>
					<th rowspan="2">Title</th>
					<th rowspan="2">Price</th>
					<th rowspan="2">Cost</th>
					<th colspan="4">Mobile Sentrix</th>
					<th colspan="4">Fixez</th>
					<th colspan="4">Mengtor</th>
					<th colspan="4">Mobile Defenders</th>
					<th colspan="4">E-Trade Supply</th>
					<th colspan="4">Maya Cellular</th>
					<th colspan="4">LCD Loop</th>
					<th colspan="4">Parts 4 Cells</th>
					<th colspan="4">Cell Parts Hub</th>
				</tr>
				<tr>
					<th>Previous Price</th>
					<th>Current Price</th>
					<th>% Change</th>
					<th>In Stock</th>
					
					<th>Previous Price</th>
					<th>Current Price</th>
					<th>% Change</th>
					<th>In Stock</th>
					
					<th>Previous Price</th>
					<th>Current Price</th>
					<th>% Change</th>
					<th>In Stock</th>

					<th>Previous Price</th>
					<th>Current Price</th>
					<th>% Change</th>
					<th>In Stock</th>

					<th>Previous Price</th>
					<th>Current Price</th>
					<th>% Change</th>
					<th>In Stock</th>

					<th>Previous Price</th>
					<th>Current Price</th>
					<th>% Change</th>
					<th>In Stock</th>

					<th>Previous Price</th>
					<th>Current Price</th>
					<th>% Change</th>
					<th>In Stock</th>

					<th>Previous Price</th>
					<th>Current Price</th>
					<th>% Change</th>
					<th>In Stock</th>

					<th>Previous Price</th>
					<th>Current Price</th>
					<th>% Change</th>
					<th>In Stock</th>
				</tr>
			</thead>
			<tbody>
				<?php $scrapping_sites = array('mobile_sentrix', 'fixez', 'mengtor', 'mobile_defenders','etrade_supply','maya_cellular','lcd_loop','parts_4_cells','cell_parts_hub'); ?>
				<?php foreach ($db->func_query("SELECT * FROM $table where url<>'' AND is_deleted = '0' GROUP BY sku") as $i => $product) { ?>
				<?php $name = $db->func_query_first_cell('SELECT opd.name FROM oc_product op inner join oc_product_description opd on op.product_id = opd.product_id WHERE sku = "'. $product['sku'] .'"'); ?>
				<?php $price = $db->func_query_first_cell('SELECT opd.price FROM oc_product op inner join oc_product_discount opd on op.product_id = opd.product_id WHERE customer_group_id = "1633" AND opd.quantity = "1" AND sku = "'. $product['sku'] .'"'); ?>
				<tr>
					<td><input class="selection checkboxes" type="checkbox" name="delete_sku[<?php echo $product['sku'];?>]" value="<?php echo $product['sku'];?>" /></td>
					<td><?php echo linkToProduct($product['sku'],$host_path); ?></td>
					<td><?php echo $name; ?></td>
					<td><?php echo '$'.number_format($price,2); ?></td>
					<td><?php echo '$'.number_format(getTrueCost($product['sku']),2); ?></td>
					<?php 
					$iterator = 1;
					foreach ($scrapping_sites as $site) { ?>
					<?php $price = $db->func_query_first("select *, (SELECT price from inv_product_price_scrap_history where sku = ph.sku and type = ph.type order by added desc limit 1, 1) as old_price from inv_product_price_scrap_history ph where sku = '" . $product['sku'] . "' AND type = '$site' order by added DESC limit 1"); ?>
					<?php $change = number_format($price['price'] / $price['old_price'] * 100, 2); ?>
					<?php if ($change < 100.00 && $change > 0.00) {
						$change = '-' . (100 - $change).'%';
					} else if ($change == 0.00) {
						$change = (100 - $change).'%';
					} else {
						$change = '+' . ($change - 100).'%';
					}
					if($price['old_price']==0.00)
					{
						$change = 'N/A';
					}
					$scrap1=$db->func_query_first_cell('SELECT url from inv_product_price_scrap where url<>"" and type="mobile_sentrix" AND sku = "'. $product['sku'] .'"');
					$scrap2=$db->func_query_first_cell('SELECT url from inv_product_price_scrap where url<>"" and type="fixez" AND sku = "'. $product['sku'] .'"');
					$scrap3=$db->func_query_first_cell('SELECT url from inv_product_price_scrap where url<>"" and type="mengtor" AND sku = "'. $product['sku'] .'"');
					$scrap4=$db->func_query_first_cell('SELECT url from inv_product_price_scrap where url<>"" and type="mobile_defenders" AND sku = "'. $product['sku'] .'"');
					$scrap5=$db->func_query_first_cell('SELECT url from inv_product_price_scrap where url<>"" and type="etrade_supply" AND sku = "'. $product['sku'] .'"');
					$scrap6=$db->func_query_first_cell('SELECT url from inv_product_price_scrap where url<>"" and type="maya_cellular" AND sku = "'. $product['sku'] .'"');
					$scrap7=$db->func_query_first_cell('SELECT url from inv_product_price_scrap where url<>"" and type="lcd_loop" AND sku = "'. $product['sku'] .'"');
					$scrap8=$db->func_query_first_cell('SELECT url from inv_product_price_scrap where url<>"" and type="parts_4_cells" AND sku = "'. $product['sku'] .'"');
					$scrap9=$db->func_query_first_cell('SELECT url from inv_product_price_scrap where url<>"" and type="cell_parts_hub" AND sku = "'. $product['sku'] .'"');
					?>
					<td>$<?php echo number_format($price['old_price'], 2); ?></td>
					<td>$<?php echo number_format($price['price'], 2); ?></td>
					<td><?php echo $change; ?></td>
					<td>
					<?php
					if(${"scrap".$iterator}!='')
					{


					?>
					<a href="<?php echo ${"scrap".$iterator};?>" target="blank">
					
					<?php echo ($price['out_of_stock'])? 'No': 'Yes'; ?>

				</a>
					<?php
				}
				else
				{
					echo '-';
				}
				?>
					</td>
					<?php 
					$iterator++;
					} ?>
				</tr>
				<?php } ?>
				
			</tbody>

		</table>
		</div>
	</form>
</body>