<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = 'charge_back';
$pageName = 'Charge Back';
$pageLink = 'chargeback_manage.php';
$pageCreateLink = 'chargeback_create.php';
$pageSetting = 'chargeback_settings.php';
$table = '`inv_chargeback`';
$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
if (!$_SESSION[$perission]) {
	exit;
}
//Deleteing Record
if ($_GET['delete']) {
	if ($_SESSION['login_as'] == 'admin') {
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

// Getting Page information
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

// Search Setup
$where = '';

if ($_GET['submit'] == 'Search') {
	unset($_GET['submit']);
	$filter = array();
	foreach ($_GET as $key => $value) {
		if ($value) {
			$filter[] = 'LCASE('. str_replace('`_`', '`.`', $key) .') LIKE LCASE("'. trim($value) .'")';
		}
	}
	if ($filter) {
		$where = ' WHERE ' . implode(' AND ', $filter);
	}
}
$orderby = ' ORDER BY `ic`.`date_added` DESC';

//Writing query 
$inv_query = 'SELECT 
`io`.*, `ic`.`reason`, `ic`.`id` as `cbid`
FROM
`inv_chargeback` AS `ic`
INNER JOIN
`inv_orders` AS `io` ON `ic`.`order_id` = `io`.`order_id`' . $where . $orderby;

//Using Split Page Class to make pagination
$splitPage = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);

//Getting All Messages
$chargeBacks = $db->func_query($splitPage->sql_query);
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
						<td><input type="text" name="`ic`_`order_id`" value="<?= (isset($_GET['`ic`_`order_id`']))? $_GET['`ic`_`order_id`']: '';?>" placeholder="Order id" /></td>
						<td><input type="text" name="`io`_`email`" value="<?= (isset($_GET['`io`_`email`']))? $_GET['`io`_`email`']: '';?>" placeholder="email" /></td>
						<td><input class="button" type="submit" name="submit" value="Search"/></td>
						<?php if ($pageSetting) { ?>
						<td><a href="<?= $pageSetting; ?>" class="fancybox3 fancybox.iframe button" style="">Settings</a></td>
						<?php } ?>
					</tr>
				</table>
			</form>
			<p><a href="<?php echo $host_path . $pageCreateLink; ?>">Add <?= $pageName; ?></a></p>
			<table width="90%" cellpadding="10" border="1"  align="center">
				<thead>
					<tr>
						<th width="2%">#</th>
						<th width="15%">Date of Order</th>
						<th width="9%">Order Id</th>
						<th width="10%">Name</th>
						<th width="14%">Email</th>
						<th width="15%">Sku x Qty</th>
						<th width="5%">Order Amount</th>
						<th width="10%">Verification Options</th>
						<th width="10%">Reason</th>
						<th width="10%">Action</th>
					</tr>
				</thead>
				<tbody>
					<!-- Showing All REcord -->
					<?php foreach ($chargeBacks as $i => $chargeBack) { ?>
					<?php $products = $db->func_query('SELECT `product_sku`, `product_qty` FROM `inv_orders_items` WHERE `order_id` = "'. $chargeBack['order_id'] .'"'); ?>
					<?php
					$productShow = '<table></tbody>';
					foreach ($products as $product) {
						$productShow .= '<tr>';
						$productShow .= '<td>'. $product['product_sku'] .'</td>';
						$productShow .= '<td>x</td>';
						$productShow .= '<td>'. $product['product_qty'] .'</td>';
						$productShow .= '</td>';
					}
					$productShow .= '</tbody></table>';
					?>
					<tr>
						<td><?= ($i) + 1; ?></td>
						<td><?= americanDate($chargeBack['order_date']); ?></td>
						<td><?= $chargeBack['order_id']; ?></td>
						<td><?= $chargeBack['customer_name']; ?></td>
						<td><?= $chargeBack['email']; ?></td>
						<td><?= $productShow; ?></td>
						<td><?= $chargeBack['order_price']; ?></td>
						<td></td>
						<td><?= $chargeBack['reason']; ?></td>
						<td><?= ($_SESSION['login_as'] == 'admin')? '<a href="'. $pageLink .'?delete='. $chargeBack['cbid'] .'">Delete</a>': '';?></td>
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