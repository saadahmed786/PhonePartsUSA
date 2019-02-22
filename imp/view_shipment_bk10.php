<?php



include_once 'auth.php';

include_once 'inc/functions.php';

include_once 'inc/split_page_results.php';



$shipment_id = (int)$_GET['shipment_id'];

if(!$shipment_id){

	header("Location:shipments.php");

	exit;

}

if($_POST['addcomment']) {

	

	$_SESSION['message'] = addComment('shipment',array('id' => $shipment_id, 'comment' => $_POST['comment']));



	header("Location:view_shipment.php?shipment_id=$shipment_id");

	exit;

}



$shipment_detail = array();

if($shipment_id){

	$shipment_detail = $db->func_query_first("select * from inv_shipments where id = '$shipment_id'");

	$list = array();

	$newlist = array();

	

	$shipment_items = $db->func_query("select * from inv_shipment_items si left join inv_shipment_qc sq on (si.shipment_id = sq.shipment_id AND si.product_sku = sq.product_sku) 

		where si.shipment_id = '$shipment_id' and is_new = 0","product_id");



	foreach($shipment_items as $product_id => $shipment_item){

		$list[$product_id] = $shipment_item;

	}

	unset($shipment_items);

	

	$shipment_items = $db->func_query("select * , si.product_sku as sku from inv_shipment_items si left join inv_shipment_qc sq on (si.shipment_id = sq.shipment_id AND si.product_sku = sq.product_sku)

		where si.shipment_id = '$shipment_id' and is_new = 1");



$_list = array();
	foreach($shipment_items as $product_id => $shipment_item){

		$newlist[] = $shipment_item;
		$_list[$product_id] = $shipment_item;
	}

	unset($shipment_items);

}



if(isset($_GET['page'])){

	$page = intval($_GET['page']);

}



if($page < 1){

	$page = 1;

}



$parameters = "shipment_id=$shipment_id";



$max_page_links = 10;

$num_rows = 500;

$start = ($page - 1)*$num_rows;



$product_ids = implode(",",array_keys($list));

if(empty($product_ids))
{
	$product_ids = implode(",",(array_keys($_list)));
}

if ($shipment_detail['is_lbb']) {

	$inv_query   = "SELECT buyback_product_id as product_id, sku as model, image_path as image, description as name FROM oc_buyback_products WHERE buyback_product_id in ($product_ids)";

} else {

	$inv_query   = "select p.product_id , p.model, p.quantity, p.status, p.mps , p.image , pd.name from 

	oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id) 

	where p.product_id in ($product_ids)";

}



$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "view_shipment.php",$page);

$products   = $db->func_query($splitPage->sql_query);

?>

<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<title>View Shipment</title>



	<script type="text/javascript" src="js/jquery.min.js"></script>

	<script type="text/javascript" src="js/jquery.lazyload.min.js"></script>

	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>

	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />



	<script type="text/javascript">

		$(document).ready(function() {

			$('.fancybox').fancybox({ width: '400px', height : '200px' , autoCenter : true , autoSize : true });

			$('.fancybox2').fancybox({ width: '800px', height : '800px' , autoCenter : true , autoSize : true });

			$('.fancybox3').fancybox({ width: '1200px', height : '800px' , autoCenter : true , autoSize : false });



			$("img.lazy").lazyload({

				effect : "fadeIn"

			});

		});

	</script>	

</head>

<body>

	<?php include_once 'inc/header.php';?>



	<?php if(@$_SESSION['message']):?>

		<div align="center"><br />

			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>

		</div>

	<?php endif;?>



	<div align="center">

		<form method="post" action="">

			<br />

			<div>

				<b>Vendor:</b> <?php echo ($shipment_detail['vendor']==0?'Not Defined':$db->func_query_first_cell("SELECT name FROM inv_users WHERE id='".$shipment_detail['vendor']."'"));?>

				| 

				<b>Status:</b> <?php echo $shipment_detail['status'];?>

				| 

				<b>Shipment Number:</b> <?php echo $shipment_detail['package_number'];?>



				<?php if($_SESSION['display_cost']):?>

					|

					<b>Shipping Cost:</b> <?php echo $shipment_detail['shipping_cost'];?>



					|

					<b>Ex Rate:</b> <?php echo $shipment_detail['ex_rate'];?>

				<?php endif;?>		

			</div>

			<br />

			<div>

				<b>Created On:</b> <?php echo americanDate($shipment_detail['date_added']);?>

				| 

				<b>Issued On:</b> <?php echo americanDate($shipment_detail['date_issued']);?>

				| 

				<b>Received On:</b> <?php echo americanDate($shipment_detail['date_received']);?>



				|

				<b>Completed On:</b> <?php echo americanDate($shipment_detail['date_completed']);?>





			</div>

			<br />

			<div>

				<?php

				if(americanDate($shipment_detail['date_received'])!='N/A')

				{

					?>

					<b>Received By:</b> <?php echo get_username($shipment_detail['received_by']);?>

					<?php

				}

				?>



				<?php

				if(americanDate($shipment_detail['date_completed'])!='N/A')

				{

					?>

					<b>Completed By:</b> <?php echo get_username($db->func_query_first_cell("SELECT user_id FROM inv_shipment_qc WHERE shipment_id='".$shipment_detail['id']."'"));?>



					<?php

				}

				?>

			</div>

			<br>

			<?php if($_SESSION['shipment_admin_totals'] || $_SESSION['login_as']=='admin') { ?>

			<br>

			<table width="50%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">

				<tr>

					<th>&nbsp</th>

					<th align="center">Total</th>

					<th align="center">Total with Exchange Rate</th>

					<th align="center"># of Items</th>

				</tr>

				<tr>

					<td>Shipping Total</td>

					<td align="center" id="stt"></td>

					<td align="center" id="ster"></td>

					<td align="center" id="sti"></td>

				</tr>

				<tr>

					<td>Received Total</td>

					<td align="center" id="rtt"></td>

					<td align="center" id="rter"></td>

					<td align="center" id="rti"></td>

				</tr>

				<tr>

					<td>RJ</td>

					<td align="center" id="rjtt"></td>

					<td align="center" id="rjter"></td>

					<td align="center" id="rjti"></td>

				</tr>

			</table>

			<!-- <b>Shipping Total: <?=number_format($_shipping_total+ $shipment_detail['shipping_cost'],2);?> ($<?=number_format(($_shipping_total+ $shipment_detail['shipping_cost'])/$shipment_detail['ex_rate'],2);?>)</b> | <b>Total Shipped: <?=$_shipped_total;?> | <b>Total Received: <?=$_received_total;?> </b> | <b>Total Rejected: <?=$_rejected_total;?> </b> -->

			<br>

			<?php } ?>

			<br>

			<?php if(($shipment['status'] == 'Pending' && $_SESSION['edit_pending_shipment']) || ($shipment['status'] != 'Completed' || $_SESSION['login_as'] == 'admin')):?>

				<a href="addedit_shipment.php?shipment_id=<?php echo $shipment_id; ?>">Edit Shipment</a>

				<br>

				<br>

			<?php endif; ?>

			<div>	

				<?php if($products):?>

					<table width="90%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">

						<thead>

							<tr>

								<th>#</th>

							<!--	<th>New Item</th> -->

								<th>New Item</th>

								<th>Image</th>

								<th>Name</th>

								<th>Ref #</th>

								<th>SKU</th>

								<th>Qty Shipped</th>

								<th>Qty Received</th>

								<th>Lower grade SKUs</th>

								<th>RJ / NTR</th>



								<?php if($_SESSION['display_cost']):?>



									<th>Previous Cost(s)</th>

									<th>Price</th>

								<?php endif;?>

							</tr>

						</thead>

						<tbody>

							<?php $i = $splitPage->display_i_count();

							foreach($products as $product):



								$cost_queries = $db->func_query("SELECT DISTINCT  DATE(cost_date) as cost_date,raw_cost FROM inv_product_costs WHERE sku='".$product['model']."' ORDER BY id DESC LIMIT 3");

							?>





							<tr class="row_<?php echo $product['product_id'];?>">

								<td align="center"><?php echo $i; ?></td>

							<!--	<td align="center" id="sti"><?php echo ($shipment_items[$i]['is_new']?'X':'-');?></td> -->

							<td align="center" id="sti"><?php echo ($shipment_items[$i]['is_new']?'X':'-');?></td>

								<td align="center">

									<a href="http://cdn.phonepartsusa.com/image/<?php echo $product['image'];?>" class="fancybox2 fancybox.iframe">

										<img class="lazy" src="http://cdn.phonepartsusa.com/image/<?php echo $product['image'];?>" data-original="http://cdn.phonepartsusa.com/image/<?php echo $product['image'];?>" height="50" width="50" alt="" />

									</a>	

								</td>



								<td align="center" width="200px">

									<?php echo $product['name'];?>



									<br />

									<?php $issue_count = $db->func_query_first_cell("select count(id) from inv_product_issues where product_sku = '".$product['model']."'")?>

									<a class="fancybox3 fancybox.iframe" href="popupfiles/product_issues.php?product_sku=<?php echo $product['model'];?>"><?php echo $issue_count?> of item issues</a>

								</td>



								<td align="center">

									<?php echo $list[$product['product_id']]['cu_po'];?>

								</td>



								<td align="center">

									<a href="product/<?php echo $product['model'];?>"><?php echo $product['model'];?></td>

										<input type="hidden" name="products[<?php echo $i?>][model]" value="<?php echo $product['model'];?>" />	

										<input type="hidden" name="products[<?php echo $i?>][product_id]" value="<?php echo $product['product_id'];?>" />			

									</td>



									<td align="center"><?php echo $list[$product['product_id']]['qty_shipped']?></td>



									<td align="center"><?php echo $list[$product['product_id']]['qty_received']?></td>



									<td align="center">

										<?php if($product['accept_all'] == 0 || !is_null($product['accept_all'])):?>

											<?php if($list[$product['product_id']]['grade_a']):?>

												<?php echo $list[$product['product_id']]['grade_a'] ."--". $list[$product['product_id']]['grade_a_qty'];?><br />

											<?php endif;?>



											<?php if($list[$product['product_id']]['grade_b']):?>

												<?php echo $list[$product['product_id']]['grade_b'] ."--". $list[$product['product_id']]['grade_b_qty'];?><br />

											<?php endif;?>



											<?php if($list[$product['product_id']]['grade_c']):?>

												<?php echo $list[$product['product_id']]['grade_c'] ."--". $list[$product['product_id']]['grade_c_qty'];?><br />

											<?php endif;?>



											<?php if($list[$product['product_id']]['grade_d']):?>

												<?php echo $list[$product['product_id']]['grade_d'] ."--". $list[$product['product_id']]['grade_d_qty'];?><br />

											<?php endif;?>

										<?php endif;?>

									</td>



									<td align="center"><?php echo $list[$product['product_id']]['rejected']?> / <?php echo $list[$product['product_id']]['ntr']?></td>



									<?php if($_SESSION['display_cost']):?>

										<td align="center">

											<?php



											if($cost_queries)

											{



												foreach($cost_queries as $ik => $cost_query)

												{

													if ($ik == 0) {$previous_costx = $cost_query['raw_cost'];}

													echo americanDate($cost_query['cost_date']).' - '.$cost_query['raw_cost']."<br>";

												}



											}

											else

											{

												echo '-';

											}

											?>

											<?php $totalPrice += $previous_costx * $list[$product['product_id']]['qty_shipped']; ?>

											<?php $totalPriceR += $previous_costx * $list[$product['product_id']]['qty_received']; ?>

											<?php $totalItems += $list[$product['product_id']]['qty_shipped']; ?>

											<?php $totalItemsR += $list[$product['product_id']]['qty_received']; ?>

										</td>





										<td align="center"><?php echo $list[$product['product_id']]['unit_price']?></td>

									<?php endif;?>		

								</tr>

								<?php $i++; endforeach; ?>



								<?php foreach($newlist as $new_item_id => $newItem):



								$cost_queries = $db->func_query("SELECT * FROM inv_product_costs WHERE sku='".$newItem['sku']."' ORDER BY id DESC LIMIT 3");

								?>



								<tr id="row_<?php echo $new_item_id;?>">

									<td align="center"><?php echo $i; ?></td>

									<td align="center" >X</td>

									<td align="center">

										<?php $image = getItemImage($newItem['sku']);?>

										<a href="http://cdn.phonepartsusa.com/image/<?php echo $image;?>" class="fancybox2 fancybox.iframe">

											<img class="lazy" src="" data-original="http://cdn.phonepartsusa.com/image/<?php echo $image;?>" height="50" width="50" alt="" />

										</a>	

									</td>



									<?php $name = getItemName($newItem['sku']);?>

									<td align="center" width="200px">

										<?php echo ($name) ? $name : $newItem['product_name']; ?>



										<br />

										<?php $issue_count = $db->func_query_first_cell("select count(id) from inv_product_issues where product_sku = '".$newItem['sku']."'")?>

										<a class="fancybox3 fancybox.iframe" href="popupfiles/product_issues.php?product_sku=<?php echo $newItem['sku'];?>"><?php echo $issue_count?> of item issues</a>

									</td>

									<td align="center">-</td>

									<td align="center">

										<?php echo $newItem['sku'];?>

									</td>



									<td align="center"><?php echo $newItem['qty_shipped']?></td>



									<td align="center"><?php echo $newItem['qty_received']?></td>



									<td align="center">

										<?php if($product['accept_all'] == 0 || !is_null($product['accept_all'])):?>

											<?php if($newItem['grade_a']):?>

												<?php echo $newItem['grade_a'] ."--". $newItem['grade_a_qty'];?><br />

											<?php endif;?>



											<?php if($newItem['grade_b']):?>

												<?php echo $newItem['grade_b'] ."--". $newItem['grade_b_qty'];?><br />

											<?php endif;?>



											<?php if($newItem['grade_c']):?>

												<?php echo $newItem['grade_c'] ."--". $newItem['grade_c_qty'];?><br />

											<?php endif;?>



											<?php if($newItem['grade_d']):?>

												<?php echo $newItem['grade_d'] ."--". $newItem['grade_d_qty'];?><br />

											<?php endif;?>

										<?php endif;?>

									</td>



									<td align="center"><?php echo $newItem['rejected']?></td>



									<?php if($_SESSION['display_cost']):?>

										<td align="center"><?php



											if($cost_queries)

											{



												foreach($cost_queries as $cost_query)

												{

													echo date('m/d/Y',strtotime($cost_query['cost_date'])).' - '.$cost_query['raw_cost']."<br>";

												}



											}

											else

											{

												echo '-';

											}

											?></td>

											<td align="center"><?php echo $newItem['unit_price']?></td>

										<?php endif;?>		

									</tr>



									<?php $i++; endforeach;?>



									<tr>

										<td colspan="5" align="left">

											<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>

										</td>



										<td colspan="5" align="right">

											<?php  echo $splitPage->display_links(10,$parameters); ?>

										</td>

									</tr>

								</tbody>   

							</table>   



						<?php endif;?>

					</div>	

				</form>

				<br><br>

				<form method="post" action="">

					<table width="60%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">

						<tr>

							<td>



								<b>Comment</b>

							</td>

							<td>

								<textarea rows="5" style="width: 100%;" name="comment" required></textarea>





							</td>

						</tr>



						<tr>

							<td colspan="2" align="center"> <input type="submit" class="button" name="addcomment" value="Add Comment" />	</td>

						</tr> 	   

					</table>

				</form>

				<br><br>

				<h2 align="center">Comment History</h2>

				<table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">

					<tr>

						<th width="15%">Date</th>

						<th>Comment</th>





						<th>Added By</th>





					</tr>

					<?php

					$comments = $db->func_query("SELECT * FROM inv_shipment_comments WHERE shipment_id='".$shipment_id."'");



					foreach($comments as $comment)

					{

						?>

						<tr>

							<td><?php echo americanDate($comment['date_added']);?></td>

							<td><?php echo $comment['comment'];?></td>



							<td><?php echo get_username($comment['user_id']);?></td>



						</tr>

						<?php 



					}

					?> 



				</table>

				<br><br>

				<?php

				$tracker = $db->func_query_first("SELECT * FROM inv_tracker WHERE shipment_id='".$shipment_id."'");



				if($tracker)

				{

					?>

					<table align="center" border="1" cellspacing="0" cellpadding="5" width="70%">

						<tr>

							<th colspan="2">Tracking ID: <?=$tracker['tracker_id'];?></th>

							<th colspan="2" align="right">Code: <?=$tracker['tracking_code'];?></th>

						</tr>

						<tr>

							<th>Date Time</th>

							<th>Message</th>

							<th align="center">Status</th>

							<th>Location</th>



						</tr>  

						<?php

						$tracker_statuses = $db->func_query("SELECT * FROM inv_tracker_status WHERE tracker_id='".$tracker['tracker_id']."' order by id desc");

						foreach($tracker_statuses as $tracker_status)

						{

							$tracker_status['datetime'] = str_replace(array('T','Z'), ' ', $tracker_status['datetime']);

							$location = json_decode($tracker_status['tracking_location'],true);

							?>

							<tr>

								<td><?=americanDate($tracker_status['datetime']);?></td>

								<td><?=$tracker_status['message'];?></td>

								<td align="center"><?=$tracker_status['status'];?></td>

								<td><?php echo $location['city'].', '.$location['state'].', '.$location['zip'];?></td>

							</tr>

							<?php

						}?>



					</table>

					<br>

					<?php

				}

				?>

			</div>

			<?php if ($_SESSION['shipment_admin_totals'] || $_SESSION['login_as']=='admin') { ?>

			<?php

			$stt = $totalPrice + $shipment_detail['shipping_cost'];

			$ster = ($totalPrice + $shipment_detail['shipping_cost']) / $shipment_detail['ex_rate'];



			$rtt = $totalPriceR + $shipment_detail['shipping_cost'];

			$rter = ($totalPriceR + $shipment_detail['shipping_cost']) / $shipment_detail['ex_rate'];



			$rjItems = $db->func_query("SELECT product_sku, sum(qty_rejected) as qty FROM inv_rejected_shipment_items WHERE shipment_id = '". $shipment_id ."'group by product_sku");

			foreach ($rjItems as $key => $rjItem) {

				$cost = $db->func_query_first_cell("SELECT raw_cost FROM inv_product_costs WHERE sku='".$rjItem['product_sku']."' ORDER BY id DESC LIMIT 1");

				$totalItemsRj += $rjItem['qty'];

				$totalPriceRJ += $cost * $rjItem['qty'];

			}

			$gtTotalItems = $totalItems + $totalItemsR;

			$rjtt = $totalPriceRJ + (($shipment_detail['shipping_cost'] / $gtTotalItems) * $totalItemsRj);

			$rjter = ($totalPriceRJ + (($shipment_detail['shipping_cost'] / $gtTotalItems) * $totalItemsRj)) / $shipment_detail['ex_rate'];

			?>

			<script>

				$('#stt').text('$<?php echo number_format($stt, 2); ?>');

				$('#ster').text('$<?php echo number_format($ster, 2); ?>');

				$('#sti').text('<?php echo $totalItems; ?>');



				$('#rtt').text('$<?php echo number_format($rtt, 2); ?>');

				$('#rter').text('$<?php echo number_format($rter, 2); ?>');

				$('#rti').text('<?php echo $totalItemsR; ?>');



				$('#rjtt').text('$<?php echo number_format($rjtt, 2); ?>');

				$('#rjter').text('$<?php echo number_format($rjter, 2); ?>');

				$('#rjti').text('<?php echo $totalItemsRj; ?>');

			</script>

			<?php } ?>

		</body>

		</html>