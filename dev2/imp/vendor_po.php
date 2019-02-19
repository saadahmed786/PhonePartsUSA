<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = 'vendor_po';
$pageName = 'Vendor PO';
$pageLink = 'vendor_po.php';
$pageCreateLink = 'vendor_po_create.php';
$pageSetting = false;
$table = '`inv_vendor_po`';
$statuses = array(
	'estimate' => 'Estimate',
	'issued' => 'Issued',
	'shipped' => 'Completed',
	// 'paid' => 'Paid'
	);
// print_r($_SESSION);
page_permission('vendor_po_page');
if((int)$_GET['vpo_id'] and $_GET['action'] == 'replicate'){
	$vpo_id = (int)$_GET['vpo_id'];
	$vpo_name = $_GET['vpo_name'];
	$max_vendor_id = $db->func_query_first_cell('SELECT MAX(id) FROM `inv_vendor_po`');
	$vendor_po_id = ($max_vendor_id)? 'PO' . (20001 + $max_vendor_id) : 'PO' . 20001;
	$vpo = $db->func_query_first("SELECT * FROM inv_vendor_po WHERE id = '$vpo_id'");
	if ($vpo) {
		$insert_vpo = array();
		$insert_vpo['vendor_po_id'] = $vendor_po_id;
		$insert_vpo['vendor'] = $vpo['vendor'];
		$insert_vpo['status'] = 'estimate';
		$insert_vpo['ex_rate'] = $vpo['ex_rate'];
		$insert_vpo['date_added'] = date('Y-m-d H:i:s');
		$db->func_array2insert("inv_vendor_po", $insert_vpo);
		$vpo_items = $db->func_query("SELECT * FROM inv_vendor_po_items WHERE vendor_po_id = '$vpo_name'");
		foreach ($vpo_items as $item) {
			$insert = array();
			$insert['vendor_po_id'] = $vendor_po_id;
			$insert['sku'] = $item['sku'];
			$insert['name'] = $item['name'];
			$insert['req_qty'] = $item['req_qty'];
			$insert['cost'] = $item['cost'];
			$insert['new_cost'] = $item['new_cost'];
			$insert['qty_shipped'] = $item['qty_shipped'];
			$insert['needed'] = $item['needed'];
			$insert['date_added'] = date('Y-m-d H:i:s');
			$db->func_array2insert("inv_vendor_po_items", $insert);
		}
	}
	$_SESSION['message'] = "Vendor PO Replicated ";
	header("Location:vendor_po.php");
	exit;

}
if((int)$_GET['vpo_id'] and $_GET['action'] == 'delete'){
	$vpo_id = (int)$_GET['vpo_id'];
	$vpo_name = $db-> func_query_first_cell("SELECT vendor_po_id FROM $table WHERE id = '$vpo_id'");
	$order_check = $db->func_query_first_cell("SELECT order_id FROM inv_vendor_po_items WHERE vendor_po_id = '$vpo_name'");
	$db->db_exec("delete from inv_vendor_po where id = '$vpo_id'");
	$db->db_exec("delete from inv_vendor_po_items where vendor_po_id = '$vpo_name'");
	if($order_check){		
	$comment = $vpo_name.' has been deleted originated from Order #:<a href="viewOrderDetail.php?order='.$order_check.'">'.$order_check.'</a>';
	} else {
	$comment = $vpo_name.' has been deleted';
	}
	addComment('vendor_po',array('id' => (int)$_GET['vpo_id'], 'comment' => $comment));
	$_SESSION['message'] = "Vendor PO is deleted ";
	header("Location:vendor_po.php");
	exit;
}
$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
// if (!$_SESSION[$perission]) {
// 	exit;
// }
//Deleteing Record
/*
if ($_GET['delete']) {
	if ($_SESSION['user_id'] == 0) {
		$delete = $_GET['delete'];
		$char = $db->func_query_first('SELECT * FROM '. $table .' WHERE id = "'. (int) $delete .'"');
		$log = 'A charge Back of ' . linkToProfile($char['to_email']) . ' Order #' . linkToOrder($char['order_id']) . ' for Amount ' . $char['amount'] . ' was deleted';
		actionLog($log);

		$db->db_exec("delete from $table where id = '" . (int) $delete . "'");
		$_SESSION['message'] = $pageName . ' Deleted';
		header("Location:" . $pageLink);
		exit;
	}
}
*/

// Getting Page information
if (isset($_GET['page'])) {
	$page = intval($_GET['page']);
}

if ($page < 1) {
	$page = 1;
}
//Setting PAgination Limits
$max_page_links = 10;
$num_rows = 50;
$start = ($page - 1) * $num_rows;

// Search Setup
$where = '';

$filter = array();
if ($_GET['submit'] == 'Search') {
	unset($_GET['submit']);
	foreach ($_GET as $key => $value) {
		if ($value && $key!= 'page') {
			
				$val = strtolower($value);
				if($key=='payment_status_new' or $key=='vendor')
				{
					$filter[] = 'lower('. str_replace('`_`', '`.`', $key) .') = "'. $val .'"';
				}
				else
				{
				$filter[] = 'lower('. str_replace('`_`', '`.`', $key) .') LIKE "%'. $val .'%"';
					
				}
			
		}
	}
}

if ($_SESSION['group']=='Vendor') {
	$filter[] = 'vendor = ' . $_SESSION['user_id'];
}
if(!$_SESSION['view_estimate_po'])
{
	$filter[] ='status<>"Estimate"';
}

if ($filter) {
	$where = ' WHERE ' . implode(' AND ', $filter);
}

$orderby = ' ORDER BY `date_added` DESC';

//Writing query 
$inv_query = 'SELECT * FROM ' . $table . ' ' . $where . $orderby;
if(isset($_GET['debug']))
{
	echo $inv_query;
}
// echo $inv_query;exit;
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
			<table>
				<tr>
					<td><input type="text" name="vendor_po_id" value="<?= (isset($_GET['vendor_po_id']))? $_GET['vendor_po_id']: '';?>" placeholder="Vendor PO ID" /></td>
					<?php if ($_SESSION[$perission]) : ?>
						<td>
							<select name="vendor">
								<option value="">---Vendor---</option>
								<?php foreach ($db->func_query('SELECT * FROM `inv_users` WHERE group_id = 1 order by lower(name) asc') as $i => $vendor) : ?>
									<option value="<?php echo $vendor['id']; ?>" <?= ($_GET['vendor'] == $vendor['id'])? 'selected="selected"': '';?>><?php echo ucfirst($vendor['name']); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					<?php endif; ?>
					<td>
						<select name="status">
							<option value="">---Status---</option>
							<?php foreach ($statuses as $key => $row) : ?>
								<option value="<?php echo $key; ?>" <?= ($_GET['status'] == $key)? 'selected="selected"': '';?>><?php echo ucfirst($row); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<?php
					if($_SESSION['update_payment_status'])
	{
					?>

					<td>
						<select name="payment_status_new">
							
							<option value="">---Payment Status---</option>
							<option value="No Payment Status" <?php echo ($_GET['payment_status_new']=='No Payment Status'?'selected':'');?>>No Payment Status</option>
							<option value="Pre-Paid" <?php echo ($_GET['payment_status_new']=='Pre-Paid'?'selected':'');?>>Pre-Paid</option>
							<option value="Paid" <?php echo ($_GET['payment_status_new']=='Paid'?'selected':'');?>>Paid</option>
							<option value="Not Paid" <?php echo ($_GET['payment_status_new']=='Not Paid'?'selected':'');?>>Not Paid</option>
							<option value="Over-Paid" <?php echo ($_GET['payment_status_new']=='Over-Paid'?'selected':'');?>>Over-Paid</option>
						</select>
					</td>

					<?php
				}
				?>


					<td><input class="button" type="submit" name="submit" value="Search"/></td>
					<?php if ($pageSetting) { ?>
					<td><a href="<?= $pageSetting; ?>" class="fancybox3 fancybox.iframe button" style="">Settings</a></td>
					<?php } ?>
				</tr>
			</table>
		</form>
		<?php if ($_SESSION[$perission]) : ?>
			<p><a href="<?php echo $host_path . $pageCreateLink; ?>">Add <?= $pageName; ?></a></p>
		<?php endif; ?>
		<table width="90%" cellpadding="10" border="1" style="border-collapse: collapse;border:1px solid #ddd"  align="center">
			<thead>
				<tr>
					<th width="2%">#</th>
					<th width="12%">Date Added</th>
					<th width="12%">Date Updated</th>
					<th width="15%">Vendor PO ID</th>
					<th width="10%">Vendor</th>
					<th width="8%">Status</th>
					<?php
					if($_SESSION['update_payment_status'])
					{
					?>
					<th width="9%">Payment Status</th>
					<?php
					}
					?>
					<th width="15%">Reference</th>
					<th width="17%">Action</th>
				</tr>
			</thead>
			<tbody>

				<!-- Showing All REcord -->
				<?php foreach ($rows as $i => $row) { ?>
				<tr>
					<td><?= ($i) + 1; ?></td>
					<td><?= americanDate ($row['date_added']); ?></td>
					<td><?= americanDate ($row['date_updated']); ?></td>
					<td><?= linkToVPO($row['id'], $host_path, $row['vendor_po_id']); ?></td>
					<td><?= get_username($row['vendor']); ?></td>
					<td align="center"><?= ucfirst(($row['status']=='shipped'?'completed':$row['status'])); ?></td>

						<?php
					if($_SESSION['update_payment_status'])
					{
					?>
					<td align="center"><?php echo ($row['payment_status_new']);?></td>
					<?php
					}
					?>


					<?php $refs = $db->func_query('Select DISTINCT(reference) from inv_vendor_po_items where vendor_po_id = "'.$row['vendor_po_id'].'"');?> 
					<td align="center">

						<?php  foreach ($refs as $ref) {
							echo $ref['reference']; ?>
							<br>
					<?php	} ?>
					</td>
					<td align="center"><?= linkToVPO($row['id'], $host_path, 'View'); ?> | <a href="vendor_po.php?action=delete&vpo_id=<?php echo $row['id'];?>&vpo_name=<?php echo $row['vendor_po_id'];?>" onclick="if(!confirm('Are you sure ?')){ return false; }">Delete</a> | <a href="vendor_po.php?action=replicate&vpo_id=<?php echo $row['id'];?>&vpo_name=<?php echo $row['vendor_po_id'];?>" onclick="if(!confirm('Are you sure to replicate this VPO ?')){ return false; }">Replicate</a></td>
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