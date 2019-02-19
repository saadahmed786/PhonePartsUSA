<?php
require_once("auth.php");
require_once("inc/header.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$pageName = 'News Feed';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
		$(document).ready(function (e) {
            setInterval(cron_dashboard, 1000 * 20);
        });
		function cron_dashboard() {
			$.ajax({
				url: '<?php echo $host_path; ?>crons/newsfeed_cron.php',
        type: 'post',
        dataType: 'json',
        		beforeSend: function () {
            },
            complete: function () {
            },
            success: function (json) {

            	if(json['paid_unshipped_orders'].length != $('#paid_unshipped_orders tbody tr').length){
            		var html;
            		for (var i = json['paid_unshipped_orders'].length - 1; i >= 0; i--) {
            			html += '<tr>';
            			html += '<td>';
            			html += json['paid_unshipped_orders'][i]['order_date'];
            			html += '</td>';
            			html += '<td align="center">';
            			html += '<a target="_blank" href="viewOrderDetail.php?order='+ json['paid_unshipped_orders'][i]['order_id'] +'">'+ json['paid_unshipped_orders'][i]['order_id'] + '</a>';
            			html += '</td>';
            			html += '<td>';
            			html += json['paid_unshipped_orders'][i]['email'];
            			html += '</td>';
            			html += '<td>$';
            			html += json['paid_unshipped_orders'][i]['order_price'];
            			html += '</td>';
            			html += '</tr>';
            		}
            		$('#paid_unshipped_orders tbody').html(html);
            	}

							if(json['hold_orders'].length != $('#hold_orders tbody tr').length){
            		var html;
            		for (var i = json['hold_orders'].length - 1; i >= 0; i--) {
            			html += '<tr>';
            			html += '<td>';
            			html += json['hold_orders'][i]['order_date'];
            			html += '</td>';
            			html += '<td align="center">';
            			html += '<a target="_blank" href="viewOrderDetail.php?order='+ json['hold_orders'][i]['order_id'] +'">'+ json['hold_orders'][i]['order_id'] + '</a>';
            			html += '</td>';
            			html += '<td>';
            			html += json['hold_orders'][i]['email'];
            			html += '</td>';
            			html += '<td>$';
            			html += json['hold_orders'][i]['order_price'];
            			html += '</td>';
            			html += '</tr>';
            		}
            		$('#hold_orders tbody').html(html);
            	}

            	if((json['shipment_comments'].length+json['buyback_comments'].length) != $('#comments tbody tr').length){
            		var html;
            		for (var i = json['shipment_comments'].length - 1; i >= 0; i--) {
            			html += '<tr>';
            			html += '<td>';
            			html += json['shipment_comments'][i]['date_added'];
            			html += '</td>';
            			html += '<td>';
            			html += json['shipment_comments'][i]['username'] +'- Shipment # <a target="_blank" href="addedit_shipment.php?shipment_id='+ json['shipment_comments'][i]['shipment_id'] +'">'+ json['shipment_comments'][i]['shipment_id'] + '</a>';
            			html += '</td>';
            			html += '<td>';
            			html += json['shipment_comments'][i]['comment'];
            			html += '</td>';
            			html += '</tr>';
            		}
            		for (var i = json['buyback_comments'].length - 1; i >= 0; i--) {
            			html += '<tr>';
            			html += '<td>';
            			html += json['buyback_comments'][i]['date_added'];
            			html += '</td>';
            			html += '<td>';
            			html += json['buyback_comments'][i]['username'] +'- LBB Shipment # <a target="_blank" href="buyback/shipment_detail.php?shipment='+ json['buyback_comments'][i]['lbb_name'] +'">'+ json['buyback_comments'][i]['lbb_name'] +'</a>';
            			html += '</td>';
            			html += '<td>';
            			html += json['buyback_comments'][i]['comment'];
            			html += '</td>';
            			html += '</tr>';
            		}
            		$('#comments tbody').html(html);
            	}

            	if(json['paypals'].length != $('#paypals tbody tr').length){

            		var html;
            		for (var i = json['paypals'].length - 1; i >= 0; i--) {
            			html += '<tr>';
            			html += '<td align="center">';
            			html += json['paypals'][i]['transaction_id'];
            			html += '</td>';
            			html += '<td align="center">';
            			html += 'Unmapped';
            			html += '</td>';
            			html += '</tr>';
            		}
            		$('#paypals tbody').html(html);
            	}
            	if((json['followed_orders'].length+json['followed_shipments'].length) != $('#followed tbody tr').length){
            		var html;
            		for (var i = json['followed_orders'].length - 1; i >= 0; i--) {
            			html += '<tr>';
            			html += '<td>';
            			html += json['followed_orders'][i]['username'];
            			html += '</td>';
            			html += '<td align="center">';
            			html += 'Order # <a target="_blank" href="viewOrderDetail.php?order='+ json['followed_orders'][i]['order_id'] +'">'+ json['followed_orders'][i]['order_id'] + '</a>';
            			html += '</td>';
            			html += '<td>';
            			html += json['followed_orders'][i]['email'];
            			html += '</td>';
            			html += '</tr>';
            		}
            		for (var i = json['followed_shipments'].length - 1; i >= 0; i--) {
            			html += '<tr>';
            			html += '<td>';
            			html += json['followed_shipments'][i]['username'];
            			html += '</td>';
            			html += '<td align="center">';
            			html += 'Shipment # <a target="_blank" href="shipment_detail.php?shipment='+ json['followed_shipments'][i]['id'] +'">'+ json['followed_shipments'][i]['id'] +'</a>';
            			html += '</td>';
            			html += '<td align="center">';
            			html += 'N/A';
            			html += '</td>';
            			html += '</tr>';
            		}
            		$('#followed tbody').html(html);
            	}

            }
        });
		}
	</script>

</head>
<body>
	<div align="center">
		<div align="center" style="display:none">
			<?php include_once '../inc/header.php';?>
		</div>
		<?php if($_SESSION['message']) { ?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
		<?php } ?>
	</div>
	<br><br><br>
	<table width="90%">
					<tr>
						<td>
							<span><h3 align="center"> Shipments</h3></span>
							<div style="height:250px;width:400px;overflow:auto;">
							<table id="trackers" align="center" border="1" width="90%" cellpadding="5" cellspacing="0">
								<thead>
									<th >Shipment #</th>
									<th >Status</th>
									<th>Location</th>
								</thead>
								<tbody id="trackers">
									<?php $trackers = $db->func_query("SELECT * FROM inv_tracker order by datetime desc limit 30 "); ?>
										<?php foreach ($trackers as $tracker ) { 
											$tracker_statuses = $db->func_query("SELECT * FROM inv_tracker_status WHERE tracker_id='".$tracker['tracker_id']."' order by id desc");
											foreach($tracker_statuses as $tracker_status)
											{
												$location = json_decode($tracker_status['tracking_location'],true);
												?>
												<tr>
												<td align="center"><?=$tracker['shipment_id'];?></td>
												<td align="center"><?=$tracker_status['status'];?></td>
												<td ><?php echo $location['city'].', '.$location['state'].', '.$location['zip'];?></td>
												</tr>
										<?php } ?>
									<?php } ?>		
										</tbody>
									</table>
									</div>
						</td>
						<td>
							<!-- Paid and Unshipped Orders -->
							<span><h3 align="center"> Paid and Unshipped Orders</h3></span>
							<div style="height:250px;width:450px;overflow:auto;">
							<table id="paid_unshipped_orders" align="center" border="1" width="60%" cellpadding="5" cellspacing="0">
								<thead>
									<th >Date/Time</th>
									<th >Order Id</th>
									<th >Email</th>
									<th >Price</th>
								</thead>
								<tbody id="paid_unshipped_orders">
									<?php $orders = $db->func_query("SELECT * FROM inv_orders where payment_source='Paid' AND (order_status='Unshipped' OR order_status='Processed') order by id desc limit 30"); ?>
									<?php foreach($orders as $order){ ?>
									<tr>
										<td>
											<?php echo americanDate($order['order_date']); ?>
										</td>
										<td align="center">
											<a target="_blank" href="viewOrderDetail.php?order=<?php echo $order['order_id']?>"><?php echo $order['order_id'];?></a>
										</td>
										<td>
											<?php echo linkToProfile($order['email']); ?>
										</td>
										<td>
											$<?php echo $order['order_price']; ?>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
							</div>
						</td>
						<td>
							<!-- On Hold Orders -->
							<span><h3 align="center"> On Hold Orders</h3></span>
							<div style="height:250px;width:450px;overflow:auto;">
								<table id="hold_orders" align="center" border="1" width="60%" cellpadding="5" cellspacing="0">
									<thead>
										<th >Date/Time</th>
										<th >Order Id</th>
										<th >Email</th>
										<th >Price</th>
									</thead>
									<tbody id="hold_orders">
										<?php $orders = $db->func_query("SELECT * FROM inv_orders  where LOWER(order_status)='on hold' order by id desc limit 30"); ?>
										<?php foreach($orders as $order){ ?>
										<tr>
										<td>
											<?php echo americanDate($order['order_date']); ?>
										</td>
										<td align="center">
											<a target="_blank" href="viewOrderDetail.php?order=<?php echo $order['order_id']?>"><?php echo $order['order_id'];?></a>
										</td>
										<td>
											<?php echo linkToProfile($order['email']); ?>
										</td>
										<td>
											$<?php echo $order['order_price']; ?>
										</td>
									</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</td>
					</tr>	
	</table>
	<br><br>
	<table width="90%">
					<tr>
						<td>
							<!-- Comments Section -->
							<?php $shipment_comments = $db->func_query("SELECT * FROM inv_shipment_comments where user_id<>'0' order by id desc limit 30");
										$buyback_comments = $db->func_query("SELECT * FROM inv_buyback_comments where user_id<>'0' order by id desc limit 30"); ?>
							<span><h3 align="center">Comments Feed</h3></span>
							<div style="height:250px;width:400px;overflow:auto;">
							<table id="comments" align="center" border="1" width="90%" cellpadding="5" cellspacing="0">
								<thead>
									<th>Date/Time</th>
									<th >Username/Type</th>
									<th >Comment</th>
								</thead>
								<tbody id="comments">
								<?php foreach($shipment_comments as $comment){ ?>
								<tr>
									<td><?php echo americanDate($comment['date_added']);?></td>
									<td><?php echo get_username($comment['user_id']);?> - Shipment # <a target="_blank" href="<?=$host_path;?>addedit_shipment.php?shipment_id=<?=$comment['shipment_id'];?>"><?php echo $comment['shipment_id'];?></a></td>
									<td><?php echo $comment['comment'];?></td>
								</tr>
								<?php } ?>
								<?php foreach($buyback_comments as $comment){ ?>
								<?php $lbb_ship_nbr = $db->func_query_first_cell("SELECT shipment_number FROM oc_buyback where buyback_id='".$comment['buyback_id']."'");?>
								<tr>
									<td><?php echo americanDate($comment['date_added']);?></td>
									<td><?php echo get_username($comment['user_id']);?> - LBB Shipment # <a target="_blank" href="<?=$host_path;?>buyback/shipment_detail.php?shipment=<?=$lbb_ship_nbr;?>"><?php echo $lbb_ship_nbr;?></a></td>
									<td><?php echo $comment['comment'];?></td>
								</tr>
								<?php } ?>
								</tbody>
							</table>
							</div>
						</td>
						<td>
							<!-- Unmapped Paypal -->
							<?php $paypals = $db->func_query("SELECT * FROM inv_transactions WHERE is_mapped = '0' order by id desc limit 30");?>
							<span><h3 align="center">Unmapped PayPal</h3></span>
							<div style="height:250px;width:450px;overflow:auto;">
							<table id="paypals" align="center" border="1" width="60%" cellpadding="5" cellspacing="0">
								<thead>
									<th >Transaction ID</th>
									<th >PayPal Status</th>
								</thead>
								<tbody id="paypals">
									<?php foreach($paypals as $paypal){ ?>
										<tr>
											<td align="center" >
												<?php echo $paypal['transaction_id']; ?>
											</td>
											<td  align="center" >
												Unmapped
											</td>
										</tr>
										<?php } ?>
								</tbody>
							</table>
							</div>
						</td>
						<td>
							<!-- Followed Orders -->
							<span><h3 align="center">Followed Orders & Shipments</h3></span>
							<div style="height:250px;width:450px;overflow:auto;">
							<table id="followed" align="center" border="1" width="60%" cellpadding="5" cellspacing="0">
								<thead>
										<th >Followed By</th>
										<th >Order/Shipment ID</th>
										<th >Email</th>
								</thead>
								<tbody id="followed">
									<?php $followed_orders = $db->func_query("SELECT * FROM inv_orders WHERE is_followed = '1' order by id desc limit 30");
									$followed_shipments = $db->func_query("SELECT * FROM inv_shipments WHERE is_followed = '1' order by id desc limit 30");
									foreach($followed_orders as $order){?>
									<tr>
										<td><?php echo get_username($order['followed_by']);?></td>
										<td align="center"> Order # 
											<a target="_blank" href="viewOrderDetail.php?order=<?php echo $order['order_id']?>"><?php echo $order['order_id'];?></a>
										</td>
										<td><?php echo linkToProfile($order['email']); ?></td>
									</tr>
									<?php } 
									foreach($followed_shipments as $shipment){ ?>
									<tr>
										<td><?php echo get_username($shipment['followed_by']);?></td>
										<td align="center">Shipment #  
											<a target="_blank" href="<?=$host_path;?>addedit_shipment.php?shipment_id=<?=$shipment['id'];?>"><?php echo $shipment['id'];?></a>
										</td>
										<td align="center">N/A</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
							</div>
						</td>
					</tr>	
	</table>
	
</body>