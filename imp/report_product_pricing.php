<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
page_permission("product_pricing");
$table = "inv_price_change_history";
$page = 'update_product_pricing.php';
$title = "Update Pricing";

$inv_query = "SELECT a.* FROM $table a, oc_product op WHERE a.sku = op.sku";

if (isset($_GET['sku'])) {

	$inv_query.= " AND a.sku like '%" . $_GET['sku'] . "%'";
}

if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}
if($page < 1){
	$page = 1;
}

$max_page_links = 10;
$num_rows = 50;	
$start = ($page - 1)*$num_rows;

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "report_product_pricing.php",$page);
$lists = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title><?= $title; ?></title>
	<link href="include/style.css" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="js/jquery.min.js"></script>




	<style>
		input[type="text"]{
			border:1px solid;
			text-align:center;
			font-size:9pt;	
		}

	</style>
</head>
<body onmousemove="$(this).removeAttr('style').attr('style', 'overflow-x: auto;');">

	<div style="display: none;">
		<?php include_once 'inc/header.php';?>
	</div>


	<div style="margin-top:20px">

		<?php if (@$_SESSION['message']): ?>
			<div align="center"><br />
				<font color="red"><?php
					echo $_SESSION['message'];
					unset($_SESSION['message']);
					?><br /><br /></font>
				</div>
			<?php endif; ?>

			<form name="search_frm" method="get">
				<table class="data" border="1" style="border-collapse:collapse;margin-bottom:20px" width="65%" cellspacing="0" align="center" cellpadding="5">
					<tr>
						<td>
							<label for="start_date">Product Sku:</label>
							<input type="text" style="width:150px" name="sku" value="<?php echo @$_REQUEST['sku'];?>" />

						</td>

						<td colspan="3">
							<input type="submit" class="button" value="Search" />

						</td>
					</tr>
				</table>    
			</form>

			<table class="data" border="1" style="border-collapse:collapse;" width="95%" cellspacing="0" align="center" cellpadding="5">
				<tr style="background:#e5e5e5;">



					<th align="center">SKU</th>
					<th align="center">Date</th>
					<th align="center">Raw Cost</th>

					<th align="center">True Cost</th>
					<th align="center">General Price</th>

					<th align="center">D1</th>
					<th align="center">D3</th>
					<th align="center">D10</th>

					<th align="center">L1</th>
					<th align="center">L3</th>
					<th align="center">L10</th>

					<th align="center">W1</th>
					<th align="center">W3</th>
					<th align="center">W10</th>

					<th align="center">S1</th>
					<th align="center">S3</th>
					<th align="center">S10</th>

					<th align="center">G1</th>
					<th align="center">G3</th>
					<th align="center">G10</th>

					<th align="center">P1</th>
					<th align="center">P3</th>
					<th align="center">P10</th>

					<th align="center">D1</th>
					<th align="center">D3</th>
					<th align="center">D10</th>

					<th align="center">G-A</th>
					<th align="center">G-B</th>
					<th align="center">G-C</th>

					<th align="center">Kit</th>

					<th align="center"></th>

				</tr>
				<?php
				$i = 1;
				foreach ($lists as $list):

					$old_cost = $db->func_query_first("SELECT * FROM inv_product_costs WHERE sku='" . $list['sku'] . "' ORDER BY id DESC limit 1,1");
				$oTrueCost =($old_cost['raw_cost'] + $old_cost['shipping_fee']) / $old_cost['ex_rate'];
				$oTrueCost = round($oTrueCost, 2);
				$oMarkup = $db->func_query_first("SELECT * FROM  inv_product_pricing WHERE  $oTrueCost BETWEEN COALESCE(`range_from`,$oTrueCost) AND COALESCE(`range_to`,$oTrueCost)");


				$true_cost = ($list['raw_cost'] + $list['shipping_fee']) / $list['ex_rate'];
				$true_cost = round($true_cost, 2);
				$markup = $db->func_query_first("SELECT * FROM  inv_product_pricing WHERE  $true_cost BETWEEN COALESCE(`range_from`,$true_cost) AND COALESCE(`range_to`,$true_cost)");

                            // Getting Kit Sku related to this product(sku)
				$sql = 'SELECT 
				iks.`kit_sku`, op.`price`
				FROM
				`inv_kit_skus` AS `iks`
				INNER JOIN
				`oc_product` AS `op` ON op.`sku` = iks.`kit_sku`
				WHERE
				iks.`kit_sku` = "' . $list['sku'] . 'K"
				';

				$kitSku = $db->func_query_first($sql);
				$kitSkuPrice = 0;
				if ($kitSku) {


					$kitSkuPrice = ($true_cost * $markup['markup_d1'])+$markup['kit_price'];

					$_temp_kit_sku = explode('.',(float)$kitSkuPrice);

					if((int)$_temp_kit_sku[1]==0)
					{
						$kitSkuPrice = $_temp_kit_sku[0].'.0000';	
					}
					else
					{

						$kitSkuPrice = $_temp_kit_sku[0].'.9500';	
					}

					$oldKitSkuPrice = ($oTrueCost * $oMarkup['markup_d1'])+$oMarkup['kit_price'];

					$_temp_kit_sku = explode('.',(float)$oldKitSkuPrice);

					if((int)$_temp_kit_sku[1]==0)
					{
						$oldKitSkuPrice = $_temp_kit_sku[0].'.0000';	
					}
					else
					{

						$oldKitSkuPrice = $_temp_kit_sku[0].'.9500';	
					}
				}
				else
				{
					$oldKitSkuPrice = 0;
				}

				?>
				<tr>
					<td align="center" rowspan="2"><?= $list['sku']; ?></td>
					<td align="center"><?php echo americanDate($list['date_added']) ?></td>
					<td align="center"><?= number_format($list['raw_cost'], 2); ?></td>
					<td align="center">$<?= number_format($true_cost, 2); ?></td>
					<td align="center"><?= round($true_cost * $markup['markup_general'], 2); ?></td>
					<td align="center"><?= round($true_cost * $markup['markup_d1'], 2); ?></td>
					<td align="center"><?= round($true_cost * $markup['markup_d3'], 2); ?></td>
					<td align="center"><?= round($true_cost * $markup['markup_d10'], 2); ?></td>

					<td align="center"><?= round($true_cost * $markup['markup_l1'], 2); ?></td>
					<td align="center"><?= round($true_cost * $markup['markup_l3'], 2); ?></td>
					<td align="center"><?= round($true_cost * $markup['markup_l10'], 2); ?></td>

					<td align="center"><?= round($true_cost * $markup['markup_w1'], 2); ?></td>
					<td align="center"><?= round($true_cost * $markup['markup_w3'], 2); ?></td>
					<td align="center"><?= round($true_cost * $markup['markup_w10'], 2); ?></td>

					<td align="center"><?= round($true_cost * $markup['markup_silver1'], 2); ?></td>
					<td align="center"><?= round($true_cost * $markup['markup_silver3'], 2); ?></td>
					<td align="center"><?= round($true_cost * $markup['markup_silver10'], 2); ?></td>

					<td align="center"><?= round($true_cost * $markup['markup_gold1'], 2); ?></td>
					<td align="center"><?= round($true_cost * $markup['markup_gold3'], 2); ?></td>
					<td align="center"><?= round($true_cost * $markup['markup_gold10'], 2); ?></td>

					<td align="center"><?= round($true_cost * $markup['markup_platinum1'], 2); ?></td>
					<td align="center"><?= round($true_cost * $markup['markup_platinum3'], 2); ?></td>
					<td align="center"><?= round($true_cost * $markup['markup_platinum10'], 2); ?></td>

					<td align="center"><?= round($true_cost * $markup['markup_diamond1'], 2); ?></td>
					<td align="center"><?= round($true_cost * $markup['markup_diamond3'], 2); ?></td>
					<td align="center"><?= round($true_cost * $markup['markup_diamond10'], 2); ?></td>

					<td align="center"><?= round($true_cost * $markup['grade_a'], 2); ?></td>
					<td align="center"><?= round($true_cost * $markup['grade_b'], 2); ?></td>
					<td align="center"><?= round($true_cost * $markup['grade_c'], 2); ?></td>

					<td align="center"><?= round($kitSkuPrice, 2); ?></td>
					<td rowspan="2" align="center"><?= ($true_cost >= $oTrueCost)? '<img height="50px" src="'. str_replace('imp/', '', $host_path) .'image/greenA.png" alt="Increase">': '<img height="50px" src="'. str_replace('imp/', '', $host_path) .'image/redA.png" alt="Increase">'; ?></td>

				</tr>
				<tr>
					<!-- <td align="center" rowspan="2"><?= $list['sku']; ?></td> -->
					<td align="center"><?php echo americanDate($old_cost['cost_date']) ?></td>
					<td align="center"><?= number_format($old_cost['raw_cost'], 2); ?></td>
					<td align="center">$<?= number_format($oTrueCost, 2); ?></td>
					<td align="center"><?= round($oTrueCost * $oMarkup['markup_general'], 2); ?></td>
					<td align="center"><?= round($oTrueCost * $oMarkup['markup_d1'], 2); ?></td>
					<td align="center"><?= round($oTrueCost * $oMarkup['markup_d3'], 2); ?></td>
					<td align="center"><?= round($oTrueCost * $oMarkup['markup_d10'], 2); ?></td>

					<td align="center"><?= round($oTrueCost * $oMarkup['markup_l1'], 2); ?></td>
					<td align="center"><?= round($oTrueCost * $oMarkup['markup_l3'], 2); ?></td>
					<td align="center"><?= round($oTrueCost * $oMarkup['markup_l10'], 2); ?></td>

					<td align="center"><?= round($oTrueCost * $oMarkup['markup_w1'], 2); ?></td>
					<td align="center"><?= round($oTrueCost * $oMarkup['markup_w3'], 2); ?></td>
					<td align="center"><?= round($oTrueCost * $oMarkup['markup_w10'], 2); ?></td>

					<td align="center"><?= round($oTrueCost * $oMarkup['markup_silver1'], 2); ?></td>
					<td align="center"><?= round($oTrueCost * $oMarkup['markup_silver3'], 2); ?></td>
					<td align="center"><?= round($oTrueCost * $oMarkup['markup_silver10'], 2); ?></td>

					<td align="center"><?= round($oTrueCost * $oMarkup['markup_gold1'], 2); ?></td>
					<td align="center"><?= round($oTrueCost * $oMarkup['markup_gold3'], 2); ?></td>
					<td align="center"><?= round($oTrueCost * $oMarkup['markup_gold10'], 2); ?></td>

					<td align="center"><?= round($oTrueCost * $oMarkup['markup_platinum1'], 2); ?></td>
					<td align="center"><?= round($oTrueCost * $oMarkup['markup_platinum3'], 2); ?></td>
					<td align="center"><?= round($oTrueCost * $oMarkup['markup_platinum10'], 2); ?></td>

					<td align="center"><?= round($oTrueCost * $oMarkup['markup_diamond1'], 2); ?></td>
					<td align="center"><?= round($oTrueCost * $oMarkup['markup_diamond3'], 2); ?></td>
					<td align="center"><?= round($oTrueCost * $oMarkup['markup_diamond10'], 2); ?></td>

					<td align="center"><?= round($oTrueCost * $oMarkup['grade_a'], 2); ?></td>
					<td align="center"><?= round($oTrueCost * $oMarkup['grade_b'], 2); ?></td>
					<td align="center"><?= round($oTrueCost * $oMarkup['grade_c'], 2); ?></td>

					<td align="center"><?= round($oldKitSkuPrice, 2); ?></td>

				</tr>
				<?php
				$i++;
				endforeach;
				?>
				<tr>
					<td colspan="16">
						<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
					</td>
					<td colspan="15">
						<?php echo $splitPage->display_links(10, str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']));?>
					</td>
				</tr>
			</table>

		</div>

	</body>
	</html>