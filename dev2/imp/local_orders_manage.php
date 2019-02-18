<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = 'compare_local_orders';
$pageName = 'Compare Local Order';
$pageLink = 'local_orders_manage.php';
$pageCreateLink = false;
$pageViewLink = 'local_orders_view.php';
$pageSetting = false;
$table = '`oc_temp_order`';

$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);

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
	unset($_GET['page']);
	foreach ($_GET as $key => $value) {
		if ($value) {
			if ($key == 'oc_order_status') {
				$filter[] = $key . ' = "' . $value . '"';
			} else {
				$filter[] = 'LCASE('. str_replace('`_`', '`.`', $key) .') LIKE LCASE("'. $value .'")';
			}
		}
	}
}

if ($filter) {
	$where = ' WHERE ' . implode(' AND ', $filter);
}

$orderby = ' ORDER BY `date_added` DESC';

//Writing query 
$inv_query = 'SELECT * FROM ' . $table . ' ' . $where . $orderby;

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
					<td><input type="text" name="order_id" value="<?= (isset($_GET['order_id']))? $_GET['order_id']: '';?>" placeholder="Order ID" /></td>
					<td><input type="text" name="email" value="<?= (isset($_GET['email']))? $_GET['email']: '';?>" placeholder="Email" /></td>
					<td>
						<select name="order_status_id">
							<option value="">---Status---</option>
							<?php foreach ($db->func_query('SELECT * FROM `oc_order_status` ') as $i => $row) : ?>
								<option value="<?php echo $row['order_status_id']; ?>" <?= ($_GET['order_status_id'] == $row['order_status_id'])? 'selected="selected"': '';?>><?php echo ucfirst($row['name']); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td><input class="button" type="submit" name="submit" value="Search"/></td>
					<?php if ($pageSetting) { ?>
					<td><a href="<?= $pageSetting; ?>" class="fancybox3 fancybox.iframe button" style="">Settings</a></td>
					<?php } ?>
				</tr>
			</table>
		</form>
		<?php if ($_SESSION[$perission]) : ?>
			<?php if ($pageCreateLink) : ?>
				<p><a href="<?php echo $host_path . $pageCreateLink; ?>">Add <?= $pageName; ?></a></p>
			<?php endif; ?>
		<?php endif; ?>
		<table width="90%" cellpadding="10" border="1"  align="center">
			<thead>
				<tr>
					<th width="2%">#</th>
					<th width="15%">Order ID</th>
					<th width="24%">Order Date</th>
					<th width="15%">Email</th>
					<th width="10%">Customer</th>
					<th width="15%">Order Status</th>
					<th width="10%">Action</th>
				</tr>
			</thead>
			<tbody>
				<!-- Showing All REcord -->
				<?php foreach ($rows as $i => $row) { ?>
				<tr>
					<td><?= ($i) + 1; ?></td>
					<td><?= linkToOrder(($row['ref_order_id']!=0.0000)? $row['ref_order_id']: $row['order_id'], $host_path); ?></td>
					<td><?= americanDate ($row['date_added']); ?></td>
					<td><?= linkToProfile($row['email'], $host_path); ?></td>
					<td><?= $row['firstname'] . ' ' . $row['lastname']; ?></td>
					<td><?= $db->func_query_first_cell("SELECT `name` FROM `oc_order_status` WHERE order_status_id = '" . $row['order_status_id'] . "' "); ?></td>
					<td><a href="<?php echo $pageViewLink; ?>?order_id=<?= $row['order_id']; ?>">View</a></td>
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