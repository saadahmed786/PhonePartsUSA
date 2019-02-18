<?php

include_once 'auth.php';

include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';

if (!$_SESSION['whitelist_add'] && !$_SESSION['login_as'] == 'admin') {
	exit;
}

$strquery = explode('&', $_SERVER['QUERY_STRING']);

foreach ($strquery as $kq => $v) {
	if (strpos($v, 'limit') !== false) {
		unset($strquery[$kq]);
	}
}

$strquery = implode('&', $strquery);

if ($_POST['action'] == 'updateVerify') {

	$orders = $_POST['orders'];
	
	$customers = array_unique($_POST['customers'], SORT_REGULAR);
	if ($_POST['type'] != 'ignore') {
		foreach($orders as $order) {
			$db->func_query('UPDATE `inv_orders` set ss_ignore = "0", ss_valid = "1" WHERE order_id="'. $order['order_id'] .'"');
			$db->db_exec("UPDATE inv_orders SET order_status='Processed',shipstation_added=0 WHERE order_id='".$order['order_id']."'");
			$db->db_exec("UPDATE oc_order SET order_status_id='15' WHERE cast(`order_id` as char(50))='".$order['order_id']."' OR ref_order_id='".$order['order_id']."'");
			addOrderComment('Order Status has been changed to Processed', $order['order_id'])	;
			$array['type'] = 'order';
			$array['user'] = $_SESSION['user_id'];
			$array['details'] = $order['order_id'];
			$array['reason'] = $order['reason'];
			$array['date_added'] = date('Y-m-d H:i:s');
			$db->func_array2insert("inv_whitelist_history",$array);
			unset($array);

			$log = 'Order # '. linkToOrder($order['order_id']) .' was verified for the Reason "' . $db->func_query_first_cell('SELECT name from inv_whitelist_reasons where id = "'. $order['reason'] .'"') . '"';
			actionLog($log);
		}
		if ($customers) {
			foreach($customers as $customer) {
				$db->func_query('UPDATE `inv_customers` set white_list = "1" WHERE email="'. $customer['email'] .'"');
				$array['type'] = 'customer';
				$array['user'] = $_SESSION['user_id'];
				$array['details'] = $customer['email'];
				$array['reason'] = $customer['reason'];
				$array['date_added'] = date('Y-m-d H:i:s');
				$db->func_array2insert("inv_whitelist_history", $array);
				unset($array);

				$log = linkToProfile($customer['email']) .' was white listed for the Reason "' . $db->func_query_first_cell('SELECT name from inv_whitelist_reasons where id = "'. $customer['reason'] .'"') . '"';
				actionLog($log);
			}
		}
	} else {
		foreach($orders as $order) {
			$db->func_query('UPDATE `inv_orders` set ss_ignore = "1", ss_valid = "0" WHERE order_id="'. $order['order_id'] .'"');

			$hdata = array();
			$hdata['order_id'] = $order['order_id'];
			$hdata['comment'] = 'Order was ignored';
			$hdata['user_id'] = $_SESSION['user_id'];
			$hdata['date_added'] = date('Y-m-d H:i:s');
			$db->func_array2insert("inv_order_history", $hdata);

			unset($hdata);

			$log = 'Order # '. linkToOrder($order['order_id']) .' was ignored.';
			actionLog($log);
		}
	}
	echo json_encode(array('success' => 1));
	exit;
}

if(isset($_REQUEST['submit'])){
	$inv_query   = '';
	$orderType   = $_REQUEST['ordertype'];

	$conditions = array();

	$start_date = $db->func_escape_string($_REQUEST['start_date']);
	$end_date   = $db->func_escape_string($_REQUEST['end_date']);

	$filterBy   = $db->func_escape_string($_REQUEST['order']);
	$order_number = $db->func_escape_string(trim($_REQUEST['order_number']));
	$email = strtolower($db->func_escape_string(trim($_REQUEST['email'])));

	if(@$start_date){
		$conditions[] =  " DATE(o.order_date) >= '$start_date' ";
	}

	if(@$end_date){
		$conditions[] =  " DATE(o.order_date) <= '$end_date' ";
	}

	if(@$filterBy !='all'){
		$conditions[] =  " o.store_type = '$filterBy' ";
	}

	if(@$email){
		$conditions[] =  " LOWER(o.email) = '$email' ";
	}

	if($order_number){
		$condition_sql = " Lower(o.order_id) = Lower('$order_number')  ";
	}
	else{
		$condition_sql = implode(" AND " , $conditions);
	}

	if(!$condition_sql){
		$condition_sql = ' 1 = 1';
	}

	$inv_query = "SELECT * FROM inv_orders o, inv_orders_details b 
	WHERE o.order_status = 'On Hold' AND o.order_id = b.order_id AND `payment_method` NOT LIKE ('%cash%') AND LCASE(b.shipping_method) NOT LIKE ('%store%') AND o.order_date > '2015-11-20' AND LCASE(b.payment_method) <> LCASE('Replacement') AND (shipstation_added IS NULL OR shipstation_added <> '1') AND ss_valid = '0' AND ss_ignore = '0' AND store_type IN ('web', 'bigcommerce') AND is_order_verified <> '1' AND order_status in ('Processed','On Hold','Awaiting Fulfillment') AND ". $condition_sql ." GROUP BY o.order_id ORDER BY o.order_date DESC";

}
else{
	$inv_query = "SELECT * FROM inv_orders o, inv_orders_details b 
	WHERE o.order_status = 'On Hold' AND o.order_id = b.order_id AND `payment_method` NOT LIKE ('%cash%') AND `payment_method` <> 'In Store' AND LCASE(b.shipping_method) NOT LIKE ('%store%') AND o.order_date > '2016-07-01' AND LCASE(b.payment_method) <> LCASE('Replacement') AND (shipstation_added IS NULL OR shipstation_added <> '1') AND ss_valid = '0' AND ss_ignore = '0' AND store_type IN ('web', 'bigcommerce') AND is_order_verified <> '1' AND order_status in ('Processed','On Hold','Awaiting Fulfillment') GROUP BY o.order_id ORDER BY o.order_date DESC";
}

if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}
if($page < 1){
	$page = 1;
}

$max_page_links = 10;
$limit = (int) $_REQUEST['limit'];
if($limit)
{
	$num_rows = $limit;
}
else
{
	$num_rows = 10;	
}

$start = ($page - 1)*$num_rows;

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "verify_orders.php",$page);
$inv_orders = $db->func_query($splitPage->sql_query);

?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link rel="stylesheet" href="include/jquery-ui.css">
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery-ui.js"></script>
	<title>Order Verification</title>
	<style type="text/css">
		.floatcenter {
			position: fixed;
			background: rgba(0,0,0,.5);
			width: 100%;
			height: 100%;
			z-index: 1000;
		}
		.floatcenter .whitebox {
			background: rgb(255, 255, 255) none repeat scroll 0% 0%;
			width: 300px;
			transform: translate(-50%, -50%);
			top: 50%;
			left: 50%;
			position: fixed;
			text-align: center;
			border-radius: 10px;
		}
		.whitebox h2 {
			text-align: center;
			padding-top: 20px;
			font-size: 20px;
		}
		.whitebox button {
			padding: 3px 10px;
			margin: 40px 20px;
			display: inline-block;
		}
		.whitebox a {
			border-radius: 100%;
			background: #000;
			display: block;
			width: 20px;
			line-height: 20px;
			color: #fff;
			position: fixed;
			top: -10px;
			right: -10px;
		}
	</style>
</head>
<body>
	<div> <?php include_once 'inc/header.php';?></div>
	<?php if(@$_SESSION['message']):?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
	<?php endif;?>

	<br />

	<h2 align="center">Order Details</h2>
	<form name="order" action="" method="get">
		<table width="90%" cellpadding="10" style="border: 0px solid #585858; "  align="center">
			<tbody>
				<tr>
					<td>
						<label for="order">Filter By Store Type :</label>
						<select id="order" name="order" style="width: 145px;">
							<option value="all">All</option>
							<option value="ebay" <?php if($_REQUEST['order']=='ebay'):?> selected='selected' <?php endif;?>>eBay</option>
							<option value="amazon" <?php if($_REQUEST['order']=='amazon'):?> selected='selected' <?php endif;?>>Amazon</option>
							<option value="amazon_fba" <?php if ($_REQUEST['order'] == 'amazon_fba'): ?> selected='selected' <?php endif; ?>>Amazon FBA</option>
							<option value="web" <?php if($_REQUEST['order']=='web'):?> selected='selected' <?php endif;?>>Web</option>
							<option value="channel_advisor" <?php if($_REQUEST['order']=='channel_advisor'):?> selected='selected' <?php endif;?>>Channel Advisor</option>
							<option value="bigcommerce" <?php if($_REQUEST['order']=='bigcommerce'):?> selected='selected' <?php endif;?>>Bigcommerce</option>
							<option value="wish" <?php if($_REQUEST['order']=='wish'):?> selected='selected' <?php endif;?>>Wish</option>
							<option value="bonanza" <?php if($_REQUEST['order']=='bonanza'):?> selected='selected' <?php endif;?>>Bonanza</option>
							<option value="po_business" <?php if($_REQUEST['order']=='po_business'):?> selected='selected' <?php endif;?>>Po Business</option>
							<option value="newegg" <?php if($_REQUEST['order']=='newegg'):?> selected='selected' <?php endif;?>>Newegg</option>
							<option value="rakuten" <?php if($_REQUEST['order']=='rakuten'):?> selected='selected' <?php endif;?>>Rakuten</option>
							<option value="newsears" <?php if($_REQUEST['order']=='newsears'):?> selected='selected' <?php endif;?>>NewSears</option>
							<option value="opensky" <?php if($_REQUEST['order']=='opensky'):?> selected='selected' <?php endif;?>>OpenSky</option>
						</select>
					</td>

						<!-- <td>
							<label for="order">Order Type :</label>
							<select id="ordertype" name="ordertype" style="width: 130px;">
								<option value="Completed" <?php if($_REQUEST['ordertype']=='Completed'):?> selected='selected' <?php endif;?>>Completed/Shipped</option>
								<option value="Return" <?php if($_REQUEST['ordertype']=='Return'):?> selected='selected' <?php endif;?>>Return/Refund</option>
							</select>
						</td> -->

						<td>
							<label for="start_date">Order Number:</label>
							<input type="text" name="order_number" value="<?php echo @$_REQUEST['order_number'];?>" />
						</td>

						<td>
							<label for="start_date">Email:</label>
							<input type="text" name="email" value="<?php echo @$_REQUEST['email'];?>" />
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
							<input type="submit" value="Search" name="submit" style="margin: 10px 0 0 10px">
						</td>

						<td>
							<?php if ($_SESSION['whitelist_add']) { ?>
							<a href="valid_customers.php" class="fancyboxX3 fancybox.iframe button" style="">Customers</a>
							<?php } ?>

						</td>
						<td>
							<?php if ($_SESSION['whitelist_history']) { ?>
							<a href="whitelist_history.php" class="fancyboxX3 fancybox.iframe button" style="">History</a>
							<?php } ?>

						</td>
						<td>
							<?php if ($_SESSION['whitelist_history']) { ?>
							<a href="ignored_orders.php" class="fancyboxX3 fancybox.iframe button" style="">Ignored</a>
							<?php } ?>
						</td>
						<td>
							<?php if ($_SESSION['whitelist_reason']) { ?>
							<a href="verify_orders_settings.php" class="fancyboxX3 fancybox.iframe button" style="">Settings</a>
							<?php } ?>
						</td>
					</tr>

					<tr>
						<?php if($inv_orders):

						?>
						<td colspan="11">
							<table width="100%" cellspacing="0" cellpadding="5px" border="1" align="center" id="table1">
								<thead>
									<tr style="background-color:#e5e5e5;">
										<th> </th>
										<th>SN</th>
										<th>Order ID</th>
										<th>Order Date</th>
										<th>Email</th>
										<th>Customer</th>
										<th>Order Price</th>
										<th>Store Type</th>
										<th>Order Status</th>
										<th>Payment</th>
										<th style="width: 200px;">Reason</th>
										<th>Verf</th>
									</tr>
								</thead>
								<tbody>
									<?php $i = $splitPage->display_i_count();
									$reasons = $db->func_query('SELECT * FROM `inv_whitelist_reasons`');
									foreach($inv_orders as $i => $order):?>

									<?php $whitelistArr = whiteList($order, 0, 1); ?>
									<?php $whitelist = $whitelistArr['check']; ?>
									<?php unset($whitelistArr['check']); ?>

									<?php //if ($whitelist == 3) { continue; } ?>

									<?php $order_type = $_REQUEST['ordertype'];?>
									<tr id="tr_<?= $i; ?>" style="<?php //echo ($whitelist == 3)? 'background-color: lightgreen;':''; ?><?= ($whitelistArr[0] == 'Black List Customer')? 'background-color: lightpink;':''; ?>" class="list_items">
										<td align="center"><?php //if ($whitelist != 3) { ?><input type="checkbox" value="<?=$order['order_id'];?>" data-email="<?= $order['email']; ?>" class="order_checkboxes" onclick="traverseCheckboxes();"> <?php //} ?></td>

										<!--                         <td><a href="javascript:void(0);" onclick="updateSSV('<?= $order['order_id']; ?>', this);">Verf</a></td> -->
										<td align="center"><?php echo ($i + 1); ?></td>

										<td align="center" class="order_id">
											<a href="viewOrderDetail.php?order=<?php echo $order['order_id']?>"><?php echo @$order['order_id'];?></a>
										</td>

										<td align="center"><?php echo americanDate($order['order_date']);?></td>

										<td align="center" style="word-wrap:break-word; border-width:0 0 1px 0;"><?php echo linkToProfile($order['email']);?></td>

										<td align="center"><?php echo @$order['customer_name'];?></td>

										<td align="center">$<?php echo $order['order_price']; ?></td>

										<td align="center"><?php echo @$order['store_type'];?></td>

										<td align="center"><?php echo @$order['order_status'];?><?php echo ($whitelistArr[0] == 'Black List Customer')? '<br>Black Listed':''; ?></td>

										<td align="center"><?php echo  ($order['store_type'] == 'amazon' || $order['store_type']=='amazon_fba')? 'amazon': $order['payment_method'] ?></td>
										<td align="center">
											<select style="width: 100%; height: 25px; margin-bottom: 5px;" class="orderReason">
												<option value="">Select Reason</option>
												<?php foreach ($reasons as $i => $reas) { ?>
												<option value="<?= $reas['id']?>"><?= $reas['name']?></option>
												<?php } ?>
											</select>
											<br>
											<input type="button" style="font-size: 10; padding: 2px; float: left;" class="button" value="Allow Once" onClick="if (confirm('Are you Sure!')) { allowOne('customer', this); }">
											<input type="button" style="font-size: 10; padding: 2px; margin-left: 2px; float: left;" class="button" value="White-List" onClick="if (confirm('Are you Sure!')) { allowOne('', this); }">
											<input type="button" style="font-size: 10; padding: 2px; float: right; background: #F00;" class="button" value="Black-List" onClick="if (confirm('Are you Sure!')) { blacklist('<?php echo @$order['order_id'];?>', this); }">
										</td>
										<td>
											<?php foreach ($whitelistArr as $key => $value) { ?>
											<?php if (strpos($value, 'Not') === false && strpos($value, 'Black') === false && strpos($value, 'Pending') === false && strpos($value, 'Un-Matched') === false) {
												echo "<a class='smallTooltip' data-tooltip='". $value ."'><img src='images/check.png' alt='Match' /></a>" ;
											} else if (strpos($value, 'Pending') != false) {
												echo "<a class='smallTooltip' data-tooltip='". $value ."'><img src='images/circle.png' alt='No Match' /></a> ";	
											} else {
												echo "<a class='smallTooltip' data-tooltip='". $value ."'><img src='images/cross.png' alt='No Match' /></a> ";	
											} ?>
											<?php } ?>
											<?php
											// for ($i=0; $i < 3; $i++) { 
											// 	if ($i < $whitelist) {
											// 		echo "<a class='smallTooltip' data-tooltip='". $whitelistArr[$i] ."'><img src='images/check.png' alt='Match' /></a>" ;
											// 	} else {
											// 		echo "<a class='smallTooltip' data-tooltip='". $whitelistArr[$i] ."'><img src='images/cross.png' alt='No Match' /></a> ";	
											// 	}
											// }
											?>
										</td>

									</tr>
									<?php $i++; endforeach; ?>
								</tbody>
								<script type="text/javascript">
									var orders = [];
									var customers = [];
									function allowOne (action, t) {
										var e = $(t).parent().parent().find('.order_checkboxes');
										e.prop('checked', true);
										newverifySelected(action);
									}

									function newverifySelected (action) {
										orders = [];
										customers = [];
										var error = false;
										$('.order_checkboxes').each(function(index, element) {
											if($(this).is(":checked")) {
												status = $(this).parent().parent().find('.orderReason').val();
												if (status == '' && action != 'ignore') {
													alert('Please Select Reason To process');
													error = true;
													return false;
												} else {
													orders.push({order_id:$(this).val(), reason:status});
													customers.push({email:$(this).attr('data-email'), reason:status});
												}
											}
										});
										if (error) {
											return false;
										}
										if(orders.length==0) {
											alert('You must selected atleast 1 order to process');
											return false;
										}
										updateSelected(action);
										// var confrimbox = '<div class="floatcenter">' +
										// '<div class="whitebox">' +
										// '<h2>Please Confrim</h2>' +
										// '<button type="button" onclick="updateSelected(\'customer\');">Allow Once</button>' +
										// '<button type="button" onclick="updateSelected();">White-List</button>' +
										// '<a href="javascript:void(0);"  onclick="updateSelected(\'close\');">X</a>' +
										// '</div>' +
										
										// '</div>';
										// $('body').prepend(confrimbox);
									}

									function blacklist(order_id, t) {
										var reason_id = $(t).parent().find('select').val();
										var reason = $(t).parent().find('option[value='+ reason_id +']').text();
										if (reason_id == '') {
											alert('Please select reason');
											return false;
										}
										$.ajax({
											url: 'chargeback_create.php?ajax=1',
											type: 'POST',
											dataType: 'json',
											data: {'order_id': order_id, 'reason': reason, 'add': 'submit'},
											success: function(json){
												if (json['success']) {
													alert('User is Black Listed For future');
													window.location.reload();
												}
											}
										});
									}

									function updateSelected (action) {
										if (action == 'close') {
											$('.floatcenter').remove();
											return false;
										}
										if (action == 'customer') {
											$('.floatcenter').remove();
											customers = [];
										}

										$.ajax({
											url: 'verify_orders.php?is_popup=1',
											type: 'POST',
											dataType: 'json',
											data: {'orders': orders, 'customers': customers, 'action': 'updateVerify', 'type': action},
											success: function(json){
												if (json['success']) {
													alert('Successfully verified the orders');
													window.location.reload();
												}
											}
										});
									}

									function reason (t) {
										$('.order_checkboxes').each(function(index, element) {
											if($(this).is(":checked")) {
												$(this).parent().parent().find('.orderReason').val($(t).val());
											}
										});
									}
								</script>
							</table>
							<table class="footer" border="0" style="border-collapse:collapse;" width="95%" align="center" cellpadding="3">
								<tr>
									<td colspan="7" align="left">
										<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
									</td>

									<td colspan="6" align="right">
										<?php echo $splitPage->display_links(10, str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']));?>
									</td>
								</tr>
							</table>
						</td>  

					<?php else : ?> 

						<td colspan="4"><label style="color: red; margin-left: 600px;">Order Doesn't Exist</label></td>

					<?php endif;?>
				</tr>
				<tr>
					<td colspan="4" align="center">
						No of Orders To Show: 
						<select name="limit" onchange="$('[name=submit]').click();">
							<?php for ($i=10; $i < 60; ($i = $i + 10)) { ?>
							<option <?php echo ($_GET['limit'] == $i)? 'selected="selected"': ''; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
							<?php } ?>
						</select>
					</td>
					<td colspan="7" align="center">
						Change the Reason For Selected:
						<select onchange="reason(this)">
							<option value="">Select Reason</option>
							<?php foreach ($reasons as $i => $reas) { ?>
							<option value="<?= $reas['id']?>"><?= $reas['name']?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="11" align="center">
						<input type="button" class="button" style="padding: 10px; 25px; font-size: 20px;" value="Ignore" onClick="if (confirm('Are you Sure!')) { newverifySelected('ignore'); }">	
						<input type="button" class="button" style="padding: 10px; 25px; font-size: 20px;" value="Allow Once" onClick="if (confirm('Are you Sure!')) { newverifySelected('customer'); }">
						<input type="button" class="button" style="padding: 10px; 25px; font-size: 20px;" value="White-List" onClick="if (confirm('Are you Sure!')) { newverifySelected(''); }">
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<script type="text/javascript" src="js/newmultiselect.js"></script>
	<script type="text/javascript">
		$(function () {
			$('#table1').multiSelect({
				actcls: 'highlightx',
				selector: 'tbody .list_items',
				except: ['form'],
				callback: function (items) {
					traverseCheckboxes('#table1', '.order_checkboxes');
				}
			});
		})
	</script>
</body>
</html>
