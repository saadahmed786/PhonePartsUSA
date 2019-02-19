<?php
require_once("../auth.php");
require_once("../inc/functions.php");
include_once '../inc/split_page_results.php';
$table = '`oc_buyback`';

page_permission("buyback");
//Deleteing Record

if($_GET['action']=='delete' and  $_SESSION['login_as'] == 'admin')
{
	$db->db_exec("DELETE FROM $table WHERE buyback_id='".(int)$_GET['id']."'");
	$_SESSION['message'] = 'Shipment has been deleted';
	$log = 'LBB Shipment#: ' . $_GET['sid'] . ' Deleted';
	actionLog($log);
	header("Location: shipments.php");
	exit;
}
if (isset($_GET['page'])) {
	$page = intval($_GET['page']);
}

if ($page < 1) {
	$page = 1;
}
//Setting PAgination Limits
$max_page_links = 10;
$num_rows = 30;
$start = ($page - 1) * $num_rows;
//Setting Search prameters
$where = '';
if ($_GET['keyword']) {
	$keyword = $_GET['keyword'];
	$where = "WHERE LCASE(ob.`shipment_number`) LIKE '%$keyword%' 
	OR LCASE(ob.`email`) LIKE LCASE('%$keyword%') 
	OR LCASE(oc.`email`) LIKE LCASE('%$keyword%') 
	OR LCASE(ob.`paypal_email`) LIKE LCASE('%$keyword%') 
	OR LCASE(CONCAT(ob.`firstname`, ' ', ob.`lastname` )) LIKE LCASE('%$keyword%') 
	OR LCASE(CONCAT(oc.`firstname`, ' ', oc.`lastname` )) LIKE LCASE('%$keyword%') ";
	$url = 'keyword='. $keywrod .'&';
}

//Filter

if ($_GET['status']) {
	if ($where != '') {
		$where .= " AND ob.status = '". $_GET['status'] ."'";
	} else {
		$where = " HAVING ob.status = '". $_GET['status'] ."'";
	}
}

//Sorting
$sortAs = ($_GET['sortas'] == 'ASC')? 'DESC': 'ASC';

$orderby = ' ORDER BY buyback_id DESC';
if ($_GET['sortby']) {
	$orderby = ' ORDER BY ' . $_GET['sortby'] . ' ' . $sortAs;
}

//Writing query 
$inv_query = "SELECT 
ob.buyback_id,
ob.paypal_email,
ob.`shipment_number`,
ob.payment_type,
ob.`status`,
ob.`email`,
CONCAT(
	ob.`firstname`,
	' ',
	ob.`lastname`
	) AS `name`,
ob.`date_added`,
ob.`date_received`,
ob.`date_qc`,
ob.`total`,
oc.`email` AS cemail,
CONCAT(
	oc.`firstname`,
	' ',
	oc.`lastname`
	) AS `cname` 
FROM
`oc_buyback` AS ob 
LEFT OUTER JOIN `oc_customer` AS oc 
ON oc.`customer_id` = ob.`customer_id` " . $where . $orderby;
//Using Split Page Class to make pagination
$splitPage = new splitPageResults($db, $inv_query, $num_rows, "shipments.php", $page);

//Getting All Messages
$rows = $db->func_query($splitPage->sql_query);
$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);

$pending_shippings = $db->func_query("SELECT a.shipment_number, a.approval_count, SUM( b.oem_quantity ) + SUM( b.non_oem_quantity ) AS total_qty
FROM oc_buyback a, oc_buyback_products b
WHERE a.buyback_id = b.buyback_id
AND a.is_label_created =0
AND a.`status` ='Awaiting'
and b.data_type='customer'
GROUP BY a.shipment_number");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>LCD Buy Back Shipments | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>
    
    <link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	<script>
		$(document).ready(function(e) {
			$('.fancybox3').fancybox({ width: '90%' , autoCenter : true , autoSize : true });
		});

	</script>




</head>
<body>
	<div align="center">
		<div align="center"> 
			<?php include_once '../inc/header.php';?>
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
		<h2>Manage LBB Shipments</h2>
		<?php
		if($_SESSION['login_as']==='admin')
		{
			?>
			<a href="popup_csv_export.php" class="fancybox3 fancybox.iframe" >CSV Report</a>
			<?php
		}

		?>
		<?php
		if($_SESSION['login_as']=='admin' || $_SESSION['buyback_create_shipment'])
		{

			?>
	|
		<a href="create_shipment.php">Create BuyBack</a>
		<?php
	}
	?>
		<br>
		<br>
		<?php
		$statuses = array('Awaiting'=>'Awaiting','Received'=>'Received','In QC'=>'QC Completed','Completed'=>'Completed');
		?>
		<form action="" method="get">
			<table width="75%">
				<tr>
				<th>
				<div id="" style="overflow-y: scroll; height:100px;width:300px">
				<table width="100%">
				<tr>
				<td colspan="2" align="center" style="font-weight:bold;border:1px solid black">Pending LBB Shipping Approvals</td>

				</tr>
				<?php
				foreach($pending_shippings as $shippings)
				{
					if($shippings['total_qty']<25)
					{
						continue;
					}
					?>
					<tr>
					<td><?=linkToLbbShipment($shippings['shipment_number'],$host_path);?></td>
					<td><?=$shippings['approval_count'];?> to 2 Approvals</td>
					</tr>
					<?php
				}
				?>

				</table>

				</div>
				</th>
					<th>Filter:</th>
					<td><input type="text" placeholder="Keyword..." name="keyword" value="<?php echo $_GET['keyword'];?>" /></td>
					<td>
						<select name="status">
							<option value="">Select Status</option>
							<?php foreach ($statuses as $key => $value) { ?>
							<option value="<?= $key; ?>" <?= ($_GET['status'] == $key)? 'selected="selected"':''; ?>><?= $value; ?></option>
							<?php } ?>
						</select>
					</td>
					<td><input class="button" type="submit" name="submit" value="Search"/></td>
					<td></td>
					<!--<td><a class="button" href="wholesale_setting.php">Setting</td>-->
				</tr>
			</table>
		</form>
		<br><br>
		<table width="90%" cellpadding="10" border="1"  align="center">
			<thead>
				<tr>
					<th width="12%">
						<a href="shipments.php?sortas=<?= $sortAs; ?>&sortby=date_added&keyword=<?=$_GET['keyword'];?>&status=<?=$_GET['status'];?>&page=<?=$page;?>">Added</a>
					</th>
					<th width="12%">
						<a href="shipments.php?sortas=<?= $sortAs; ?>&sortby=date_received&keyword=<?=$_GET['keyword'];?>&status=<?=$_GET['status'];?>&page=<?=$page;?>">Received</a>
					</th>
					<th width="12%">
						<a href="shipments.php?sortas=<?= $sortAs; ?>&sortby=date_qc&keyword=<?=$_GET['keyword'];?>&status=<?=$_GET['status'];?>&page=<?=$page;?>">Date QC</a>
					</th>
					<th width="7%">
						Shipment #
					</th>
					<th width="10%">
						Name
					</th>
					<th width="20%">
						Customer
					</th>
					<th width="8%">
						<a href="shipments.php?sortas=<?= $sortAs; ?>&sortby=payment_type&keyword=<?=$_GET['keyword'];?>&status=<?=$_GET['status'];?>&page=<?=$page;?>">Payment Type</a>
					</th>
					<th width="7%">
						<a href="shipments.php?sortas=<?= $sortAs; ?>&sortby=total&keyword=<?=$_GET['keyword'];?>&status=<?=$_GET['status'];?>&page=<?=$page;?>">Total</a>
					</th>
					<th width="7%">
						<a href="shipments.php?sortas=<?= $sortAs; ?>&sortby=status&keyword=<?=$_GET['keyword'];?>&status=<?=$_GET['status'];?>&page=<?=$page;?>">Status</a>
					</th>
					<th colspan="2" width="15%">
						Action
					</th>
				</tr>
			</thead>
			<tbody>
				<!-- Showing All REcord -->
				<?php foreach ($rows as $i => $row) { ?>

				<?php $customer = (!empty($row['email']))? $row['email']: $row['cemail'];  ?>
				<?php $name = (!empty(rtrim($row['name'])))? $row['name']: $row['cname'];  ?>
				<tr>
					<td>
						<?php echo americanDate($row['date_added']);?>
					</td>
					<td>
						<?php echo americanDate($row['date_received']);?>
					</td>

					<td>
						<?php echo americanDate($row['date_qc']);?>
					</td>

					<td>
						<a href="shipment_detail.php?shipment=<?php echo $row['shipment_number'];?>"><?= $row['shipment_number']; ?></a>
					</td>
					<td>
						<?= $name; ?>
					</td>
					<td>
						<?=linkToProfile($customer,$host_path);?>
					</td>
					<td>
						<?php echo ($row['payment_type']);?>
					</td>
					<td>
						<?php echo '$'.(number_format($row['total'],2));?>
					</td>
					<td>
						<?php echo $row['status'];?>
					</td>
					<td>
						<a href="shipment_detail.php?shipment=<?php echo $row['shipment_number'];?>">View</a> <?php
						if($_SESSION['buyback_delete_shipment'])
						{
							?>| <a href="#" onClick="if(confirm('Are you sure want to delete this shipment?'))window.location='shipments.php?action=delete&id=<?php echo $row['buyback_id'];?>&sid=<?php echo $row['shipment_number'];?>'">Delete</a>
							<?php
						}
						?>

					</td>
					
				</tr>
				<?php } ?>
			</tbody>
		</table>

		<br /><br />
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
	</div>
</body>