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
$table2 = '`oc_order`';
$order_id = $_GET['order_id'];

$where = " WHERE order_id = '$order_id'";
//Writing query 
$inv_query = 'SELECT * FROM ' . $table . ' ' . $where;
$inv_query2 = 'SELECT * FROM ' . $table2 . ' ' . $where;

$inv_queryItems = 'SELECT * FROM `oc_temp_order_product` ' . $where . ' ORDER BY model ASC';
$inv_query2Items = 'SELECT * FROM `oc_order_product` ' . $where . ' ORDER BY model ASC';

$inv_queryTotal = 'SELECT * FROM `oc_temp_order_total` ' . $where . ' ORDER BY sort_order ASC';
$inv_query2Total = 'SELECT * FROM `oc_order_total` ' . $where . ' ORDER BY sort_order ASC';

$order = $db->func_query_first($inv_query);
$orderItems = $db->func_query($inv_queryItems);
$orderTotal = $db->func_query($inv_queryTotal);

$order2 = $db->func_query_first($inv_query2);
$order2Items = $db->func_query($inv_query2Items);
$order2Total = $db->func_query($inv_query2Total);


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
		<h2><?= $pageName; ?>s</h2>
		<?php if ($_SESSION[$perission]) : ?>
			<?php if ($pageCreateLink) : ?>
				<p><a href="<?php echo $host_path . $pageCreateLink; ?>">Add <?= $pageName; ?></a></p>
			<?php endif; ?>
		<?php endif; ?>
		<table width="90%" cellpadding="10" border="0"  align="center">
			<thead>
				<tr>
					<th width="50%">Before</th>
					<th width="50%">After</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<table width="95%" cellpadding="10" border="1"  align="center">
							<tr>
								<td>Order ID</td> <td><?php echo linkToOrder(($order['ref_order_id']!=0.0000)? $order['ref_order_id']: $order['order_id'], $host_path); ?></td>
							</tr>
							<tr>
								<td>Email</td> <td><?php echo linkToProfile($order['email'], $host_path); ?></td>
							</tr>
							<tr>
								<td>Customer</td> <td><?php echo $order['firstname'] . ' ' . $order['lastname']; ?></td>
							</tr>
							<tr>
								<td>Payment Method</td> <td><?php echo $order['payment_method']; ?></td>
							</tr>
							<tr>
								<td>Order Total</td> <td><?php echo $orderTotal[count($orderTotal) - 1]['text']; ?></td>
							</tr>
						</table>
						<br>
						<table width="95%" cellpadding="10" border="1"  align="center">
							<thead>
								<tr>
									<th>
										SKU
									</th>
									<th>
										Name
									</th>
									<th>
										Qty
									</th>
									<th>
										Unit Price
									</th>
									<th>
										Line Total
									</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($orderItems as $key => $row) { ?>
								<tr>
									<td><?php echo linkToProduct($row['model'], $host_path); ?></td>
									<td><?php echo $row['name']; ?></td>
									<td><?php echo $row['quantity']; ?></td>
									<td>$<?php echo number_format($row['price'], 2); ?></td>
									<td>$<?php echo number_format($row['total'], 2); ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						<br>
						<table width="95%" cellpadding="10" border="1"  align="center">
							<tbody>
								<?php foreach ($orderTotal as $key => $row) { ?>
								<tr>
									<td width="80%" align="right"><?php echo ucfirst($row['title']); ?></td>
									<td width="20%" align="right">$<?php echo number_format(round($row['value'],2), 2); ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</td>
					<td>
						<table width="95%" cellpadding="10" border="1"  align="center">
							<tr>
								<td>Order ID</td> <td><?php echo linkToOrder(($order2['ref_order_id'])? $order2['ref_order_id']: $order2['order_id'], $host_path); ?></td>
							</tr>
							<tr>
								<td>Email</td> <td><?php echo linkToProfile($order2['email'], $host_path); ?></td>
							</tr>
							<tr>
								<td>Customer</td> <td><?php echo $order2['firstname'] . ' ' . $order2['lastname']; ?></td>
							</tr>
							<tr>
								<td>Payment Method</td> <td><?php echo $order2['payment_method']; ?></td>
							</tr>
							<tr>
								<td>Order Total</td> <td><?php echo $order2Total[count($order2Total) - 1]['text']; ?></td>
							</tr>
						</table>
						<br>
						<table width="95%" cellpadding="10" border="1"  align="center">
							<thead>
								<tr>
									<th>
										SKU
									</th>
									<th>
										Name
									</th>
									<th>
										Qty
									</th>
									<th>
										Unit Price
									</th>
									<th>
										Line Total
									</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($order2Items as $key => $row) { ?>
								<tr>
									<td><?php echo linkToProduct($row['model'], $host_path); ?></td>
									<td><?php echo $row['name']; ?></td>
									<td><?php echo $row['quantity']; ?></td>
									<td>$<?php echo number_format($row['price'], 2); ?></td>
									<td>$<?php echo number_format($row['total'], 2); ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						<br>
						<table width="95%" cellpadding="10" border="1"  align="center">
							<tbody>
								<?php foreach ($order2Total as $key => $row) { ?>
								<tr>
									<td width="80%" align="right"><?php echo ucfirst($row['title']); ?></td>
									<td width="20%" align="right">$<?php echo number_format(round($row['value'],2), 2) ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<br />
	</div>
</body>