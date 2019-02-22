<?php
include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';
page_permission('price_modification');

if ($_POST['updatePrice']) {
	foreach ($_POST['item'] as $key => $info) {
		$shipment = $db->func_query_first("SELECT * FROM `inv_shipments` WHERE id = '". $info['shipment_id'] ."'");

		$total_products = $db->func_query_first_cell("SELECT SUM(`qty_received`) FROM `inv_shipment_items` WHERE `shipment_id` = '". $info['shipment_id'] ."'");

		
		$item_shipping_cost = round($shipment['shipping_cost'] / $total_products,4);

		$sku = explode('--', $key);
		$sku = $sku[0];
				
        addToPriceChangeReport($info['shipment_id'],$sku,$info['price'],$shipment['ex_rate'], $item_shipping_cost);

        addUpdateProductCost($sku, $info['price'], $shipment['ex_rate'],$item_shipping_cost );

        $array['unit_price'] = $info['price'];
        $db->func_array2update("inv_shipment_items",$array,"product_sku='$sku' AND shipment_id = '". $info['shipment_id'] ."'");
        unset($array);
	}
	$_SESSION['message'] = "Raw Cost is Updated";
	header('location: price_modification.php');
	exit;
}

$sku  = $db->func_escape_string($_REQUEST['sku']); 

$where = array();
$having = array();

if($sku){
	$where[] = " LOWER(a.product_sku)=LOWER('$sku') ";	
}

if ($where) {
	$where = implode(" AND ", $where);
} else {
	$where = "1 = 1";
}

$inv_query = "SELECT a.*, b.date_added FROM inv_shipment_items a, inv_shipments b WHERE b.id = a.shipment_id AND b.status = 'Completed' AND a.`unit_price` = 0 AND $where ORDER BY b.date_added DESC";

$_prices  = $db->func_query($inv_query);
$prices = array();
$k=0;
foreach($_prices as $_price)
{
$shipment = $db->func_query_first("SELECT * FROM `inv_shipments` WHERE id = '". $_price['shipment_id'] ."'");
// echo "SELECT SUM(`qty_received`) FROM `inv_shipment_items` WHERE `shipment_id` = '". $_price['shipment_id'] ."'";exit;
		$total_products = $db->func_query_first_cell("SELECT SUM(`qty_received`) FROM `inv_shipment_items` WHERE `shipment_id` = '". $_price['shipment_id'] ."'");

		
		$item_shipping_cost = round($shipment['shipping_cost'] / $total_products,4);


$prices[$k] = $_price;
$prices[$k]['shipping_fee'] = $item_shipping_cost;
$prices[$k]['ex_rate'] = $shipment['ex_rate'];
$k++;
}
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="include/calendar.css" rel="stylesheet" type="text/css" />
	<link href="include/calendar-blue.css" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="js/jquery.min.js"></script>
	<title>Price Change Report</title>
</head>
<body>
	<?php include_once 'inc/header.php';?>

	<?php if(@$_SESSION['message']):?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
	<?php endif;?>

	<div align="center">
		<form name="order" action="" method="get" >
			<table cellpadding="10" border="0"  align="center">
				<tr>
					<td>SKU</td>
					<td>
						<input type="text" style="width:150px" name="sku" value="<?php echo @$_REQUEST['sku'];?>" />
					</td>

					<td>
						<input type="submit" name="search" value="Search" class="button" />
					</td>
				</tr>   
			</table>
		</form>
		<form action="" method="post" >
			<table border="1" cellpadding="10" cellspacing="0" width="80%">
				<tr style="background:#e5e5e5;">
					<th>S.N.</th>
					<th>Date Added</th>
					<th>SKU</th>
					<th>Vendor</th>
					<!-- <th>Item Name</th> -->
					<th>Shipment No.</th>
					<th>Raw Cost</th>
					<th>Shipping Fee</th>
					<th> Ex. Rate</th>



				</tr>
				<?php if($prices):?>
					<?php 
					$k=0;
					foreach($prices as $i => $price):?>

					<?php
					$previous_cost = $db->func_query_first("SELECT DISTINCT  DATE(cost_date) as cost_date,raw_cost FROM inv_product_costs WHERE sku='".$price['product_sku']."' ORDER BY id DESC limit 1,1");
					$cost_difference = $previous_cost['raw_cost'] - (float)$price['raw_cost'];
					$true_cost = ($price['raw_cost']+$price['shipping_fee']) / $price['ex_rate'];
					//if($cost_difference!=0)
					//{
					?>
					<tr>
						<td align="center"><?php echo $k+1;?></td>
						<td align="center"><?php echo americanDate($price['date_added'])?></td>
						<td align="center"><a href="<?=$host_path;?>/product/<?=$price['product_sku'];?>"><?php echo $price['product_sku']?></a></td>
						<td align="center"><?= getAdmin($db->func_query_first_cell("SELECT `vendor` FROM `inv_shipments` WHERE id = '". $price['shipment_id'] ."'")); ?></td>
						<!-- <td align="center"><?php echo $db->func_query_first_cell("SELECT b.name FROM oc_product a,oc_product_description b WHERE a.product_id=b.product_id AND  a.sku='".$price['product_sku']."'");?></td> -->
						<td align="center"><a href="view_shipment.php?shipment_id=<?php echo $price['shipment_id'];?>"><?php echo $db->func_query_first_cell("SELECT package_number FROM inv_shipments WHERE id='".$price['shipment_id']."'");?></a></td>
						<td align="center"><input onkeyup="allowNum(this)" type="text" required="" value="<?= $price['unit_price']; ?>" name="item[<?= $price['product_sku'] . '--' . $price['shipment_id']; ?>][price]" /></td>
                        <td align="center"><?=number_format($price['shipping_fee'],2);?></td>
                        <td align="center">$<?=number_format($price['ex_rate'],2);?></td>
                        <input type="hidden" value="<?= $price['shipment_id']; ?>" name="item[<?= $price['product_sku'] . '--' . $price['shipment_id']; ?>][shipment_id]" />

					</tr>
					<?php
					$k++;
					//}
					?>
				<?php endforeach;?>       
			<?php endif;?>

			<tr>
				<td colspan="8" align="center">
					<input type="submit" name="updatePrice" value="Update">
				</td>
			</tr>
		</table>
	</form>
</div>
</body>
</html>
<script>
	$(document).ready(function(e) {
		$('.fancybox3').fancybox({    width: '90%',
			height: 600,
			fitToView : false,
			autoSize : false
		});
	});
	function allowNum (t) {
		var input = $(t).val();
		var valid = input.substring(0, input.length - 1);
		if (isNaN(input)) {
			$(t).val(valid);
		}
	}
</script>