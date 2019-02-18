<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = false;
$pageName = 'RJ Items';
$pageLink = 'rj_items.php';
$pageCreateLink = false;
$pageSetting = false;
$table = '`inv_return_shipment_box_items`';
if ($_SESSION['login_as'] != 'admin') {
	header("Location: index.php");
	exit;
}

// Getting Page information
if (isset($_GET['page'])) {
	$page = intval($_GET['page']);
}

if ($page < 1) {
	$page = 1;
}

$parameters = '&page='.$page;
//Setting PAgination Limits
$max_page_links = 10;
$num_rows = 30;
$start = ($page - 1) * $num_rows;

// Search Setup
$where = array();

if ($_GET['filter'] == 'Search') {
	
	
	if($_GET['filter_package_number'])
	{
		$where[] = " package_number LIKE '%".$db->func_escape_string($_GET['filter_package_number'])."%'";
		$parameters.='&filter_package_number='.$_GET['filter_package_number'];
	}


	if($_GET['filter_product_sku'])
	{
		$where[] = " product_sku LIKE '%".$db->func_escape_string($_GET['filter_product_sku'])."%'";
		$parameters.='&filter_product_sku='.$_GET['filter_product_sku'];
	}

	// if($_GET['filter_order_date'])
	// {
	// 	$where[] = " DATE(ship_date) = '".date('Y-m-d',strtotime($_GET['filter_order_date']))."'";
	// 	$parameters.='&filter_order_date='.$_GET['filter_order_date'];
	// }

	// if(isset($_GET['filter_orderType']) and $_GET['filter_orderType']!='' )
	// {
	// 	$where[] = " is_mapped = '".$db->func_escape_string($_GET['filter_orderType'])."'";
	// 	$parameters.='&filter_orderType='.$_GET['filter_orderType'];
	// }

	// if(isset($_GET['filter_voided']) and $_GET['filter_voided']!='' )
	// {
	// 	$where[] = " voided = '".$db->func_escape_string($_GET['filter_voided'])."'";
	// 	$parameters.='&filter_voided='.$_GET['filter_voided'];

	// }

	
}

if(!$where)
{
	$where[] = " 1 = 1 ";
}
$where = implode(" AND ", $where);

$sort = $_GET['sort'];
$order_by = $_GET['order_by'];

$sort_array  = array('package_number','product_sku','cost','vendor','date_completed','date_recived');

if(!in_array($sort, $sort_array))
{
	$sort = $sort_array[0];
	$order_by = 'desc';
}



$orderby = ' ORDER BY `'.$sort.'` '.$order_by;

if($order_by=='asc') $order_by='desc'; else $order_by = 'asc';


//Writing query 
$inv_query = "SELECT irs.*, iss.vendor, iss.`date_received`, iss.`date_completed`, iss.package_number FROM `inv_rejected_shipment_items` `irs`, `inv_shipments` `iss` WHERE irs.`shipment_id` =  iss.`id` and $where
$orderby";

//die($inv_query);
//Using Split Page Class to make pagination
$splitPage = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);

//Getting All Messages

$rows = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<script type="text/javascript" src="js/jquery.min.js"></script>

	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	<link rel="stylesheet" type="text/css" href="include/xtable.css" media="screen" />

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<script>
		$(document).ready(function (e) {
			$('.fancybox3').fancybox({width: '90%', 'height': 800, autoCenter: true, autoSize: false});
		});

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
		<h2>Manage <?= $pageName; ?>s</h2>
		<form action="" method="get">
			<table width="70%" cellpadding="5">
				<tr>
					<th>Shipment No:</th>
					<td><input type="text" name="filter_package_number" value="<?=$_GET['filter_package_number'];?>"></td>
					<th>Product Sku:</th>
					<td><input type="text" name="filter_product_sku" value="<?=$_GET['filter_product_sku'];?>"></td>
					<!-- <th>Order Date:</th>
					<td><input type="text" class="datepicker" readOnly name="filter_order_date" value="<?=$_GET['filter_order_date'];?>"></td> -->
				</tr>
				<tr>
					<td colspan="4" align="center"><input class="button" type="submit" name="filter" value="Search"></td>
				</tr>
			</table>
		</form>
		<?php if ($pageCreateLink) { ?>
		<p><a href="<?php echo $host_path . $pageCreateLink; ?>">Add <?= $pageName; ?></a></p>
		<?php } ?>
		<table  class="xtable"   align="center" style="width:98%;margin-top: 30px;">
			<thead>
				<tr>
					<th width="3%">#</th>
					<th width="10%"><a <?=($sort=='package_number'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=package_number&order_by=<?=$order_by;?>&<?=$parameters;?>">Shipment #</a> </th>
					<th width="15%"><a <?=($sort=='product_sku'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=product_sku&order_by=<?=$order_by;?>&<?=$parameters;?>">Product Sku</a></th>
					

					<th width="12%"><a <?=($sort=='vendor'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=vendor&order_by=<?=$order_by;?>&<?=$parameters;?>">Vendor</a></th>

					<th width="15%"><a <?=($sort=='date_received'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=date_received&order_by=<?=$order_by;?>&<?=$parameters;?>">Date Received</a></th>
					<th width="15%"><a <?=($sort=='date_completed'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=date_completed&order_by=<?=$order_by;?>&<?=$parameters;?>">Date Completed</a></th>

					<th width="10%"><a <?=($sort=='cost'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=cost&order_by=<?=$order_by;?>&<?=$parameters;?>">Cost</a></th>
				</tr>
			</thead>
			<tbody>
				<!-- Showing All REcord -->
				<?php
				$total = $db->func_query_first_cell('SELECT  SUM(cost) FROM `inv_rejected_shipment_items` `irs`, `inv_shipments` `iss`  WHERE irs.`shipment_id` = iss.`id`');
				foreach($rows as $i=> $row)
				{
					//$total += $row['cost'];
					?>
					<tr>
						<td><?=($i + 1);?></td>
						<td><?=linkToShipment($row['shipment_id'], $host_path, $row['package_number']);?></td>
						<td><?=$row['product_sku'];?></td>


						<td><?= get_username($row['vendor']);?></td>

						<td><?=americanDate($row['date_received']);?></td>
						<td><?=americanDate($row['date_completed']);?></td>

						<td align="right">$<?=number_format($row['cost'],2);?></td>
					</tr>
					<?php
				}
				?>
				<tr>
					<td colspan="6" align="right">Total:</td>
					<td align="right">$<?=number_format($total,2);?></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<?php
					$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
					?>
					<td colspan="11">
						<em><?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?></em>
						<div class="pagination" style="float:right">
							<?php echo $splitPage->display_links(10,$parameters);?>
						</div>
					</td>


				</tr>
			</tfoot>
		</table>

		<br /><br />
		<table class="footer" border="0" style="border-collapse:collapse;" width="95%" align="center" cellpadding="3">
			
		</table>
		<br />
	</div>
</body>