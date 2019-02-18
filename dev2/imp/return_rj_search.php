<?php

require_once("auth.php");
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';

$page = (int)$_GET['page'];
if(!$page){
	$page = 1;
}

$where = '1 = 1';
$where2 = '1 = 1';
$keyword = $db->func_escape_string(trim($_GET['keyword']));
if($keyword){
	$where = " LCASE(rma_number) like '%".strtolower($keyword)."%' OR LCASE(return_item_id) like '%".strtolower($keyword)."%' ";
	$where2 = " LCASE(reject_item_id) like '%".strtolower($keyword)."%' ";
	$parameters[] = "keyword=$keyword";
}


$_query = "select si.*, s.box_number from inv_return_shipment_box_items si inner join inv_return_shipment_boxes s on (s.id = si.return_shipment_box_id)
where $where order by date_added desc";
$splitPage   = new splitPageResults($db , $_query , 25 , "return_rj_search.php",$page);
$rma_returns = $db->func_query($splitPage->sql_query);
// testObject($rma_returns);
$inv_query  = "select si.* , irs.package_number from inv_rejected_shipment_items si inner join inv_rejected_shipments as irs on (si.rejected_shipment_id = irs.id)
where $where2 and si.deleted=0 order by date_added desc";

$splitPageProduct  = new splitPageResults($db , $inv_query , 25 , "return_rj_search.php",$page);
$products = $db->func_query($splitPageProduct->sql_query);

// testObject($products);
if($parameters){
	$parameters = implode("&",$parameters);
} else {
	$parameters = '';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Returns History</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

	<script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox({ width: '450px' , autoCenter : true , autoSize : true });
			$('.fancybox2').fancybox({ width: '680px' , autoCenter : true , autoSize : true });
		});
		function gfs_check(){
			if($('#gfs_checkbox'). prop("checked") == true){
				$('#gfs_value').val(1);
			}
			else if($('#gfs_checkbox'). prop("checked") == false){
				$('#gfs_value').val(0);
			}
		}
		function storefront_check(){
			if($('#storefront_checkbox'). prop("checked") == true){
				$('#storefront_value').val(1);
			}
			else if($('#storefront_checkbox'). prop("checked") == false){
				$('#storefront_value').val(0);
			}
		}
	</script>

	<style type="text/css">
		.data td,.data th{
			border: 1px solid #e8e8e8;
			text-align:center;
			width: 150px;
		}
		.div-fixed{
			position:fixed;
			top:0px;
			left:8px;
			background:#fff;
			width:98.8%; 
		}
		.red td{ box-shadow:1px 2px 5px #990000}
	</style>
</head>
<body>
	<div align="center"> 
		<?php include_once 'inc/header.php';?>
	</div>

	<?php if($_SESSION['message']):?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
	<?php else:?>
		<br /><br /> 
	<?php endif;?>

	<div align="center">
		<table>
			<tr>
				<td width="75%" align="center">
					<form action="" method="get">
						<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">
							<tr>
								<td>
									Return ID or RTV ID: <?php echo createField("keyword","keyword","text",$_GET['keyword']);?>
								</td>

								<td>
									<input type="submit" name="search" value="Search" class="button" />
								</td>
							</tr>
						</table>
					</form>
				</td>
				<td width="25%">
					<form target="_blank" action="daily_return_and_rejects_report.php" method="post">
						<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">
							<tr>
								<td colspan="2">
									<h2 align="center">Daily Return & RTV Report</h2>
								</td>
							</tr>
							<tr>
								<td>
									Start Date:
								</td>
								<td>
									<input type="text" data-type="date" value="<?php echo date('Y-m-d'); ?>" name="start" />
								</td>
							</tr>
							<tr>
								<td>
									End Date:
								</td>
								<td>
									<input type="text" data-type="date" value="<?php echo date('Y-m-d'); ?>" name="end" />
								</td>
							</tr>
							<tr>
								<td colspan="2" align="center">
									<input onclick="gfs_check()" type="checkbox" id="gfs_checkbox">Exclude Good For Stock items
									<input type="hidden" id="gfs_value" name="gfs_value" value="0">
								</td>
							</tr>
							<tr>
								<td colspan="2" align="center">
									<input onclick="storefront_check()" type="checkbox" id="storefront_checkbox">Only Storefront Orders
									<input type="hidden" id="storefront_value" name="storefront_value" value="0">
								</td>
							</tr>
							<tr>
								<td colspan="2" align="center">
									<input type="submit" name="create" value="Generate PDF" class="button" />
								</td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
		</table>
	</div>			
	<br />
	
	<div>
		<?php if ($rma_returns) { ?>
		<h5>Return ID's Result</h5>
		<table class="data" border="1" style="border-collapse:collapse;" width="94%" cellspacing="0" align="center" cellpadding="5">
			<tr style="background:#e5e5e5;">					
				<th>Date Added</th>
				<th>Date Modified</th>
				<th>Order ID / Shipment ID</th>
				<th>Rma</th>
				<th>Return ID</th>
				<th>Sku</th>
				<th>Reason</th>
				<th>Cost</th>
				<th>Item Location</th>
			</tr>
			<?php foreach($rma_returns as $k => $rma_return):?>
				<tr>
					<td><?php echo americanDate($rma_return['date_added']);?></td>
					<td><?php echo americanDate($rma_return['claim_date_modified']);?></td>
					<td><?php echo linkToOrder($rma_return['order_id'], $host_path); ?></td>
					<td><?php echo linkToRma($rma_return['rma_number'], $host_path); ?></td>
					<td><a class="fancyboxX3 fancybox.iframe" href="reject_item_log.php?reject_item_id=<?php echo $rma_return['return_item_id'];?>"><?php echo $rma_return['return_item_id'];?></a></td>
					<td><?php echo linkToProduct($rma_return['product_sku'], $host_path); ?></td>
					<td><?php echo $rma_return['reason']; ?></td>
					<td>$<?php echo $rma_return['price']; ?></td>
					<td><a href="boxes/boxes_edit.php?box_id=<?php echo $rma_return['return_shipment_box_id']; ?>"><?php echo $rma_return['box_number']; ?></a></td>
				</tr>
			<?php endforeach;?>
		</table>
		<table class="footer" border="0" style="border-collapse:collapse;" width="95%" align="center" cellpadding="3">
			<tr>
				<td colspan="7" align="left">
					<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
				</td>

				<td colspan="6" align="right">
					<?php echo $splitPage->display_links(10,$parameters);?>
				</td>
			</tr>
		</table>
		<br />
		<?php } ?>
		<?php if ($products) { ?>
		<h5>RTV ID's Result</h5>
		<table class="data" border="1" style="border-collapse:collapse;" width="94%" cellspacing="0" align="center" cellpadding="5">
			<tr style="background:#e5e5e5;">					
				<th>Date Added</th>
				<th>Date Modified</th>
				<th>Order ID / Shipment ID</th>
				<th>PO Number</th>
				<th>Rma</th>
				<th>Return ID</th>
				<th>Sku</th>
				<th>Reason</th>
				<th>Cost</th>
				<th>Item Location</th>
			</tr>
			<?php foreach($products as $k => $rma_return):?>
				<tr>
					<td><?php echo americanDate($rma_return['date_added']);?></td>
					<td><?php echo americanDate($rma_return['date_updated']);?></td>
					<!-- <td><?php echo '<a href="addedit_rejectedshipment.php?shipment_id='. $rma_return['rejected_shipment_id'] .'">' . $rma_return['package_number'] . '</a>'; ?></td> -->
					<td><?php echo linkToShipment($rma_return['shipment_id'], $host_path, $db->func_query_first_cell("select package_number from inv_shipments where id='".(int)$rma_return['shipment_id']."'"));?></td>
					<td><?php echo ($rma_return['vendor_po_id']) ? '<a href="vendor_po.php?vpo_id='. $db->func_query_first_cell("SELECT id FROM inv_vendor_po WHERE vendor_po_id = '". $rma_return['vendor_po_id'] ."'") .'">' . $rma_return['vendor_po_id'] . '</a>': 'N/A'; ?></td>
					<td><?php echo 'N/A'; ?></td>			   		   
					<td><a class="fancyboxX3 fancybox.iframe" href="reject_item_log.php?reject_item_id=<?php echo $rma_return['reject_item_id'];?>"><?php echo $rma_return['reject_item_id'];?></a></td>
					<td><?php echo linkToProduct($rma_return['product_sku'], $host_path); ?></td>
					<td><?php echo $db->func_query_first_cell("SELECT name FROM inv_rj_reasons WHERE id = '" . $rma_return['reject_reason'] . "'"); ?></td>
					<td>$<?php echo $rma_return['cost']; ?></td>
					<td><?php echo '<a href="addedit_rejectedshipment.php?shipment_id='. $rma_return['rejected_shipment_id'] .'">' . $rma_return['package_number'] . '</a>'; ?></td>
				</tr>
			<?php endforeach;?>
		</table>
		<table class="footer" border="0" style="border-collapse:collapse;" width="95%" align="center" cellpadding="3">
			<tr>
				<td colspan="7" align="left">
					<?php echo $splitPageProduct->display_count("Displaying %s to %s of (%s)");?>
				</td>

				<td colspan="6" align="right">
					<?php echo $splitPageProduct->display_links(10,$parameters);?>
				</td>
			</tr>
		</table>
		<?php } ?>
		<br />
	</div>		
</body>
</html>            			   