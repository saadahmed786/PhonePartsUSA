<?php

include_once 'auth.php';
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';
page_permission('purchase_orders_view');
if($_GET['action']=='delete')
{
	$po_orders = explode(",",$_GET['po_orders']);
	foreach($po_orders as $order)
	{

		$db->db_exec("DELETE FROM inv_orders WHERE order_id='".$order."'");	
		$db->db_exec("DELETE FROM inv_orders_details WHERE order_id='".$order."'");	
		$db->db_exec("DELETE FROM inv_orders_items WHERE order_id='".$order."'");	
		$db->db_exec("DELETE FROM inv_order_docs WHERE order_id='".$order."'");
		$db->db_exec("DELETE FROM inv_order_history WHERE order_id='".$order."'");

	}
	unset($_GET['action']);
	unset($_GET['po_orders']);
	$_SESSION['message'] = 'Purchase Order(s) deleted.';
	header("Location: po_orders.php");
	exit;
}
if(isset($_REQUEST['submit'])){
	$inv_query   = '';
	$parameters  = $_SERVER['QUERY_STRING'];

	$conditions = array();

	$start_date = $db->func_escape_string($_REQUEST['start_date']);
	$end_date   = $db->func_escape_string($_REQUEST['end_date']);
	$order_number = $db->func_escape_string(trim($_REQUEST['order_number']));

	$conditions[] =  " store_type = 'po_business' ";

	if(@$start_date){
		$conditions[] =  " order_date >= '$start_date' ";
	}

	if(@$end_date){
		$conditions[] =  " order_date <= '$end_date' ";
	}

	if($order_number){
		$condition_sql = " Lower(o.order_id) = Lower('$order_number') OR Lower(o.so_number) = Lower('$order_number') ";
	}
	else{
		$condition_sql = implode(" AND " , $conditions);
	}

	$inv_query = "Select o.* , od.* from inv_orders o inner join inv_orders_details od on o.order_id = od.order_id where $condition_sql  
	group by o.order_id order by order_date DESC";
}
else{
	$inv_query = "Select o.order_id , o.order_date , o.email, o.order_price,o.paid_price,od.shipping_cost , o.store_type, o.order_status, o.fishbowl_uploaded,
	od.first_name , od.last_name, o.shipstation_added from inv_orders o inner join inv_orders_details od on o.order_id = od.order_id 
	where store_type = 'po_business' group by o.order_id order by order_date DESC";
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

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "po_orders.php",$page);
$inv_orders = $db->func_query($splitPage->sql_query);

?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="include/calendar.css" rel="stylesheet" type="text/css" />
	<link href="include/calendar-blue.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="include/calendar.js"></script>
	<script type="text/javascript" src="include/calendar-en.js"></script>
	<script type="text/javascript" src="include/calhelper.js"></script>
	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<script>
		$(document).ready(function (e) {
			$('.fancyboxX3').click(function ()  {
				if ($(this).attr('href') == 'javascript:void(0);') {
					alert('Please Select Products');
					return false;
				}
			});
		});
	</script>
	<title>Purchase Orders</title>
</head>
<body>
	<?php include_once 'inc/header.php';?>

	<?php if(@$_SESSION['message']):?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
	<?php endif;?>

	<br />
	<div align="center" style="width: 600px;">
		
	<a href="po_business_create.php" class="button">Create PO Account</a>
	&nbsp
	<a href="po_businesses.php" class="button">Manage PO Accounts</a>

	</div>
	<h2 align="center">Purchase Orders</h2>

	<form name="order" action="" method="get">
		<table width="90%" cellpadding="10" style="border: 1px solid #585858;"  align="center">
			<tbody>
				<tr>
					<td>
						<label for="start_date">Order Number:</label>
						<input type="text" name="order_number" value="<?php echo @$_REQUEST['order_number'];?>" />
					</td>

					<td>
						<label for="start_date">Start Date:</label>
						<input type="text" class="datepicker" value="<?php echo @$_REQUEST['start_date'];?>" name="start_date" size="20" style="width: 110px;" readonly="readonly" />
					</td>

					<td>
						<label for="end_date" style="margin-left: 30px;" valign="top">End Date:</label> 
						<input type="text" class="datepicker" value="<?php echo @$_REQUEST['end_date'];?>" name="end_date" size="20" style="width: 110px;" readonly="readonly" />
					</td>

					<td>
						<input type="submit" value="Search" name="submit" style="margin: 10px 0 0 60px">
						<input  type="button" value="Delete" onClick="deleteRecords();" >
						<a href="javascript:void(0);" class="fancyboxX3 fancybox.iframe button charge_card" >Charge Card</a>
						<a href="javascript:void(0);" class="fancyboxX3 fancybox.iframe button payment_status" >Other Method</a>
					</td>
				</tr>
				<tr>
					<td colspan="4" style="height:20px; padding-top:0; padding-bottom:0;">
						<b style="color: red;">Note:</b> Only the same type of orders will be paid in bulk. <b style="color: white; background-color: #000;">White: Not Paid</b> , <b style="color: red;">Red: 25% Paid</b> , <b style="color: green;">Green: Paid 100%</b>
					</td>
				</tr>

				<tr>
					<?php if($inv_orders):?>
						<td colspan=6>

							<table width="100%" cellspacing="0" cellpadding="5px" border="1" align="center">
								<thead>
									<tr>
										<?php
										if($_SESSION['login_as'] == 'admin')
										{
											?>
											<th><input type="checkbox" id="toggle_check" onChange="toggleCheck(this)"></th>
											<?php
										}
										?>
										<th>SN</th>
										<th>Order ID</th>
										<th>Order Date</th>
										<th>Email</th>
										<th>Customer</th>
										<th>Order Price</th>
										<th>Paid Price</th>
										<th>Vouchers</th>
										<th>Order Status</th>
										<th>FB Uploaded</th>
										<th>ShipStation Uploaded</th>
										<th>Action</th>
									</tr>
								</thead>
								<?php $i = $splitPage->display_i_count();
								foreach($inv_orders as $order):?>
								<?php $order_type = $_REQUEST['ordertype'];?>
								<?php
								$sub_total = $db->func_query_first_cell("SELECT SUM(product_price) FROM inv_orders_items WHERE order_id='".$order['order_id']."'");
								$vouchers = $db->func_query_first_cell('SELECT SUM(`a`.`amount`) AS `used` FROM `oc_voucher_history` as a, `oc_voucher` as b WHERE a.`voucher_id` = b.`voucher_id` AND a.`' . (($order['store_type'] == 'po_business')? 'inv_order_id': 'order_id') . '` = "'. $order['order_id'] .'"');
								$order['order_price'] = $sub_total + $order['shipping_cost'] + $vouchers;

								$color = '';
								$color = ($order['paid_price'] > 0.00 && $order['paid_price'] < $order['order_price'])? 'style="background-color: lightpink;"': $color;
								$color = ($order['paid_price'] == $order['order_price'])? 'style="background-color: lightgreen;"': $color;

								$paid = 0;
								$paid = ($order['paid_price'] > 0.00 && $order['paid_price'] < $order['order_price'])? 2: $paid;
								$paid = ($order['paid_price'] == $order['order_price'])? 1: $paid;
								?>
								<tr id="<?php echo $order['id'];?>" <?= $color; ?>>
									<?php
									if($_SESSION['login_as'] == 'admin')
									{
										?>
										<th><input type="checkbox" data-paid="<?= $paid; ?>" <?= ($paid == 1) ? 'disabled="disabled"': '';?> name="orders[]" class="order_checks" onChange="selectedChecks();" value="<?=$order['order_id'];?>"></th>
										<?php
									}
									?>
									<td align="center"><?php echo $i; ?></td>

									<td align="center"><?php echo @$order['order_id'];?></td>

									<td align="center"><?php echo @date('d-M-Y H:i:s' , strtotime($order['order_date']));?></td>

									<td align="center"><?php echo @$order['email'];?></td>

									<td align="center"><?php echo @$order['first_name'] . " " . $order['last_name'];?></td>

									<td align="center">$<?php echo @round($order['order_price'], 2);?></td>
									<td align="center">$<?php echo @round($order['paid_price'], 2);?></td>
									<td align="center">$<?php echo round($vouchers, 2);?></td>


									<?php if($order_type == "Return") :?>
										<td align="center">Return/Refund</td>
									<?php else : ?>
										<td align="center"><?php echo @$order['order_status'];?></td>
									<?php endif ;?>

									<td align="center"><?php echo (@$order['fishbowl_uploaded']) ? 'Yes' : 'No';?></td>

									<td align="center"><?php echo (@$order['shipstation_added']) ? 'Yes' : 'No';?></td>

									<td align="center" class="showorder">
										<a href="viewOrderDetail.php?order=<?php echo $order['order_id']?>"><b style="color: red;"><u>View</u></b></a>
									</td>
								</tr>
								<?php $i++; endforeach; ?>
							</table>
							<input type="hidden" id="po_orders" value="">

						</td>  

					<?php else : ?> 

						<td colspan=4><label style="color: red; margin-left: 600px;">Order Doesn't Exist</label></td>

					<?php endif;?>
				</tr>

				<tr>
					<td colspan="3" align="right">
						<br />
						<?php echo $display = $splitPage->display_count("Displaying %s to %s of (%s)");
						print "&nbsp;";
						$display_links_string = $splitPage->display_links(10,$parameters);
						echo $display_links_string;
						?>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</body>
</html>
<script>
	function toggleCheck(obj)
	{
		if($(obj).is(':checked'))
		{
			$('.order_checks').each(function () {
				if (!$(this).attr('disabled')) {
					$(this).prop('checked',true);
				}
			});

		}
		else
		{
			$('.order_checks').prop('checked',false);

		}
		selectedChecks();
	}
	function selectedChecks()
	{
		var arr = new Array();
		var ord = new Array();
		var qhalf = new Array();
		$('.order_checks').each(function(index, element) {
			if($(this).is(':checked')) {
				arr.push($(this).val());
			}
			if($(this).is(':checked') && $(this).attr('data-paid') != 1) {
				if ($(this).attr('data-paid') == 0) {
					ord.push($(this).val());
				} else if ($(this).attr('data-paid') == 2) {
					qhalf.push($(this).val());
				}
			}
		});
		$('#po_orders').val(arr.join());

		if (ord.length > 0) {
			$('.charge_card').attr('href', 'popupfiles/charge_card.php?payOrderIds=' + ord.join());
			$('.payment_status').attr('href', 'popupfiles/payment_status.php?payOrderIds=' + ord.join());
		} else if (qhalf.length > 0) {
			$('.charge_card').attr('href', 'popupfiles/charge_card.php?payOrderIds=' + qhalf.join());
			$('.payment_status').attr('href', 'popupfiles/payment_status.php?payOrderIds=' + qhalf.join()); 
		} else {
			$('.charge_card').attr('href', 'javascript:void(0);');
			$('.payment_status').attr('href', 'javascript:void(0);');
		}
	}
	function deleteRecords()
	{
		if(confirm('Are you sure want to delete those Purchase Orders?'))
		{
			window.location='po_orders.php?action=delete&po_orders='+$('#po_orders').val();


		}

	}

</script>