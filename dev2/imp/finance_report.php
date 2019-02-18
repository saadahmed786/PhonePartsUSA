<?php

include_once 'auth.php';

include_once 'inc/functions.php';

include_once 'inc/split_page_results.php';

set_time_limit ( 0 );

ini_set ( "memory_limit", "1024M" );

page_permission('finance_report');

if (isset($_GET['page'])) {

	$page = intval($_GET['page']);

}



if($_POST['action'] and $_POST['action']=='show_orders')

{

	$store_type = $db->func_escape_string($_POST['store_type']);

	

	

	

	

	$query = http_build_query(array(

		'ordertype' => 'Completed',

		'start_date' => date('Y-m-d'),

		'end_date' => date('Y-m-d'),

		'order' => $store_type,

		'submit'=>1,

		'is_popup' => 1

		

		

		));

	echo curl($host_path.'order.php?'.$query);

	echo $result;exit;

	

	

}



if ($page < 1) {

	$page = 1;

}



$max_page_links = 10;

$num_rows = 50;

$start = ($page - 1) * $num_rows;



$filter_date_range = $_GET['filter_date_range'];



if(!$filter_date_range or $filter_date_range=='Today')

{

	$filter_date_range = 'Today';	

}



switch($filter_date_range)

{

	case 'Today':

	$where1 = " DATE(a.order_date) = '".date('Y-m-d')."' ";

	$where2 = " DATE(dateofmodification) = '".date('Y-m-d')."' ";

	$where3 = " DATE(date_added) = '".date('Y-m-d')."' ";

	break;



	case 'Current Week':

	$where1 = " WEEKOFYEAR(a.order_date)=WEEKOFYEAR(NOW()) AND YEAR(a.order_date) = YEAR(NOW()) ";

	$where2 = " WEEKOFYEAR(dateofmodification) = WEEKOFYEAR(NOW()) AND YEAR(dateofmodification) = YEAR(NOW())";

	$where3 = " WEEKOFYEAR(date_added)=WEEKOFYEAR(NOW()) AND YEAR(date_added) = YEAR(NOW()) ";

	break;



	case '15 Days':

	$where1 = " a.order_date between   DATE_SUB(NOW(), INTERVAL 15 DAY) and now()   ";

	$where2 = " dateofmodification between DATE_SUB(NOW(), INTERVAL 15 DAY)  and now() ";

	$where3 = " date_added between DATE_SUB(NOW(), INTERVAL 15 DAY)  and now() ";

	break;



	case 'Whole Month':

	$where1 = " MONTH(a.order_date) =  MONTH(now()) and YEAR(a.order_date)= YEAR(now())  ";

	$where2 = " MONTH(dateofmodification) =  MONTH(now()) and YEAR(dateofmodification)= YEAR(now()) ";

	$where3 = " MONTH(date_added) =  MONTH(now()) and YEAR(date_added)= YEAR(now()) ";

	break;



	case 'Custom':

	$where1 = " DATE(a.order_date) between  '".date('Y-m-d',strtotime($_GET['filter_date_range1']))."' and '".date('Y-m-d',strtotime($_GET['filter_date_range2']))."' ";

	$where2 = " DATE(dateofmodification) between  '".date('Y-m-d',strtotime($_GET['filter_date_range1']))."' and '".date('Y-m-d',strtotime($_GET['filter_date_range2']))."' ";

	

	$where3 = " DATE(date_added) between  '".date('Y-m-d',strtotime($_GET['filter_date_range1']))."' and '".date('Y-m-d',strtotime($_GET['filter_date_range2']))."' ";

	break;



	default:



	break;	

	

}









$stores  = array(

	'web'			=>	'PhonePartsUSA',

	'po_business'	=>	'Customer PO',

	'amazon'		=>	'Amazon',

	'amazon_fba'	=>	'Amazon FBA',

	'amazon'		=>	'Amazon',

	'ebay'			=>	'eBay',

	//'channel_advisor'	=>	'Channel Advisor',

	'bigcommerce'	=>	'Big Commerce',

	'wish'			=>	'Wish',

	//'bonanza'		=>	'Bonanza',

	'newegg'		=>	'NewEgg',

	'rakuten'		=>	'Rakuten',

	'newsears'		=>	'NewSears',

	'opensky'		=>	'OpenSky',

	'amazon_ca'		=>	'Amazon CA',

	'amazon_mx'		=>	'Amazon MX',

	'amazon_pg'		=>	'Amazon PG',

	'amazon_pgca'	=>	'Amazon PGCA',

	'amazon_pgmx'	=>	'Amazon PGMX'		

	);









	?>

	<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

	<html>

	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

		<link rel="stylesheet" href="include/pikaday.css">

		<link href="include/table_sorter.css" rel="stylesheet" type="text/css" />

		<script type="text/javascript" src="js/jquery.min.js"></script>

		<title>Finance Report</title>

	</head>

	<body>

		<?php include_once 'inc/header.php'; ?>



		<?php if (@$_SESSION['message']): ?>

			<div align="center"><br />

				<font color="red"><?php

					echo $_SESSION['message'];

					unset($_SESSION['message']);

					?><br /></font>

				</div>

			<?php endif; ?>



			<div align="center">

				<form name="order" action="" method="get">

					<table width="30%" cellpadding="10" border="1"  align="center">

						<tr>







							<td>Date</td>

							<td align="center">

								<select name="filter_date_range" style="margin-bottom:5px;width:150px" onChange="if ($(this).val() == 'Custom') {

								$('input[name=filter_date_range1]').show();

								$('input[name=filter_date_range2]').show();

							} else {

							$('input[name=filter_date_range1]').hide();

							$('input[name=filter_date_range2]').hide();

						}" >



						<option value="Today" <?php if ($_GET['filter_date_range'] == "Today") echo 'selected'; ?>>Today</option>

						<option value="Current Week" <?php if ($_GET['filter_date_range'] == "Current Week") echo 'selected'; ?>>Current Week</option>

						<option value="15 Days" <?php if ($_GET['filter_date_range'] == "15 Days") echo 'selected'; ?>>15 Days</option>

						<option value="Whole Month" <?php if ($_GET['filter_date_range'] == "Whole Month") echo 'selected'; ?>>Whole Month</option>

						<option value="Custom" <?php if ($_GET['filter_date_range'] == "Custom") echo 'selected'; ?>>Custom</option>

					</select><br />

					<input type="text" readOnly="" class="datepicker" style="width:120px;<?php

					if ($_GET['filter_date_range'] != 'Custom') {

						echo 'display:none';

					}

					?>" name="filter_date_range1"  value="<?php echo @$_GET['filter_date_range1']; ?>" >

					<input type="text" class="datepicker" readonly="" style="width:120px;<?php

					if ($_GET['filter_date_range'] != 'Custom') {

						echo 'display:none';

					}

					?>" name="filter_date_range2"  value="<?php echo @$_GET['filter_date_range2']; ?>" >





				</td>











				<td><input type="submit" name="search" value="Search" class="button" /></td>

			</tr>   

		</table>

	</form>










	<div style="float:right;margin-right:80px">
	<a href="popupfiles/finance_sales_tax.php" class="button fancybox3 fancybox.iframe">Sales Tax Detail</a>
	</div>	
	<br style="clear:both">





	<h1>Sales</h1>

	<table border="1" cellpadding="5" cellspacing="0" width="80%" class="tablesorter">

		<thead>

			<tr style="background:#e5e5e5;">

				<th class="{sorter: false}"> </th>

				<th class="{sorter: false}"> </th>

				<th>Orders</th>

				<th>Gross</th>

				<th>Cost of Goods</th>

				<th>Selling Fees</th>

				<th>Shipping Cost</th> 



				<th>Shipping Fee</th>

				<th>Vouchers</th>

				<th>Coupons</th>

				<th>Net</th>



			</tr>

		</thead>

		<tbody>

			<?php

			$order_data = '';



			foreach($stores as $store_type => $store)

			{

				switch($store_type)

				{

					case 'po_business':

					$order_price = "a.sub_total+a.shipping_amount";

					break;

					default:

					$order_price = "a.sub_total+a.shipping_amount";



					break;

				}



				$where = " and MONTH(a.order_date)='06' and year(a.order_date)='2015' ";

				$order_status_query_array = array('processed','shipped','completed','unshipped');



				$rows = $db->func_query("SELECT a.*, $order_price as total_price FROM inv_orders a WHERE a.store_type='".$store_type."' AND LOWER(a.order_status) IN (". "'" . implode("','", $order_status_query_array) . "'".") and  $where1  ");
				if(isset($_GET['debug']))
				{
					echo "SELECT a.*, $order_price as total_price FROM inv_orders a WHERE a.store_type='".$store_type."' AND LOWER(a.order_status) IN (". "'" . implode("','", $order_status_query_array) . "'".") and  $where1  ";
				}




				$order_ids = array();

				$total_price = 0.00;

				$shipping_cost = 0.00;

				$true_cost = 0.00;

				$shipping_paid = 0.00;

				$total = 0.00;

				$promotion_discount = 0.00;

				$i=0;

				$total_vouchers = 0.00;

				$transaction_fees = 0.00;

				foreach($rows as $key => $row) {

					$is_internal=$db->func_query_first_cell("SELECT is_internal FROM inv_customers WHERE email='".$row['email']."'");

					if ($is_internal == '0') {

						

					

					// selling fee

					switch($store_type)

					{

					case 'web':

					case 'po_business':

					case 'bigcommerce':

					

					$transaction_fee = (float)$db->func_query_first_cell("SELECT transaction_fee FROM inv_transactions WHERE order_id='".$row['order_id']."' OR transaction_id='".$row['transaction_id']."'");

					break;

					case 'ebay':

					

					$transaction_fee = $row['transaction_fee'];

					break;

					default:

					$transaction_fee = $db->func_query_first_cell("SELECT SUM(fee) FROM inv_order_fees WHERE order_id='".$row['order_id']."' ");

					break;

					}

					if($transaction_fee<0.00){

						$transaction_fee = $transaction_fee*(-1);

					}

					$transaction_fees+=$transaction_fee;



					$order_data .= '<tr>'

					.'<td>' . ($key + 1) . '</td>'

					.'<td>'. americanDate($row['order_date']) .'</td>'

					.'<td><a href="viewOrderDetail.php?order='. $row['order_id'] .'">'. $row['order_id'] .'</a></td>'

					.'<td>'. $row['customer_name'] . '</td>'

					.'<td>'. linkToProfile($row['email']) .'</td>'

					.'<td>'. $row['order_status'] .'</td>'

					// .'<td>$'. number_format($row['total_price'],2) .'</td>'

					.'</tr>';









					$order_items = $db->func_query("SELECT product_qty,product_true_cost,promotion_discount FROM inv_orders_items WHERE order_id='".$row['order_id']."'");

					foreach($order_items as $order_item) {

						$true_cost+=$order_item['product_true_cost']*$order_item['product_qty'];

						$promotion_discount+=(float)$order_item['promotion_discount']*(int)$order_item['product_qty'];	

					}

					// $true_cost += $row['items_true_cost'];

					



					$vouchers = $db->func_query('SELECT *, `a`.`amount` as `used` FROM `oc_voucher_history` as a, `oc_voucher` as b WHERE a.`voucher_id` = b.`voucher_id` AND a.`'.($store_type=='web'?'order_id':'inv_order_id').'` = "'. $row['order_id'] .'"');

					

					



					foreach ($vouchers as $key => $voucher) {

						$total_vouchers += str_replace('-', '', $voucher['used']);

					}



					$coupons = $db->func_query('SELECT *, `a`.`amount` as `used` FROM `oc_coupon_history` as a, `oc_coupon` as b WHERE a.`coupon_id` = b.`coupon_id` AND a.`order_id` = "'. $orderID .'"');

					$total_coupons = 0.00;



					foreach ($coupons as $key => $coupon) {

						$total_coupons += str_replace('-', '', $coupon['used']);

					}

					$row['total_price']-$promotion_discount;

					$order_ids[] = $row['order_id'];

					$total_price+=$row['total_price'];



					$shipping_cost+=(float)$db->func_query_first_cell("SELECT shipping_cost FROM inv_orders_details WHERE order_id='".$row['order_id']."'");

						//	$true_cost+=(float)$db->func_query_first_cell("SELECT SUM(product_true_cost) FROM inv_orders_items where order_id='".$row['order_id']."'");

					$shipping_paid+=(float)$db->func_query_first_cell("SELECT shipping_cost+insurance_cost FROM inv_shipstation_transactions WHERE order_id='".$row['order_id']."'");

							//$total= ($total_price-$true_cost-$shipping_paid+$shipping_cost);

					//$total = ($total_price+$shipping_paid) - ($true_cost+$shipping_cost + $total_vouchers + $total_coupons);

					//$total = $total_price-$true_cost-$shipping_paid-$total_vouchers-$total_coupons+$shipping_cost;

					$total = $total_price-$true_cost-$shipping_paid-$transaction_fees+$shipping_cost;



					$i++;





					}

				}



				$total_i+= $i;

				$total_total_price+= $total_price;

				$total_shipping_cost+= $shipping_cost;

				$total_true_cost+= $true_cost;

				$total_shipping_paid+= $shipping_paid;

				$total_total+= $total;

				$tt_vouchers += $total_vouchers;

				$tt_coupons += $total_coupons;

				$tt_transaction_fees+=$transaction_fees;

				?>

				<tr>

					<?php

					if(!isset($_GET['filter_date_range']) or $_GET['filter_date_range']=='Today')

					{





						?>

						<td id="td_<?php echo $store_type;?>" ><img style="cursor:pointer" onClick="showOrders('<?php echo $store_type;?>');" src="<?php echo $host_path;?>images/plus.png" height="18" width="18"></td>

						<?php

					}

					else

					{

						?>

						<td> </td>

						<?php	

					}

					?>

					<td><?=$store;?></td>

					<td><?php echo number_format($i);?></td>

					<td>$<?php echo number_format($total_price,2);?></td>

					<td style="color:blue">$<?php echo number_format($true_cost,2);?></td>

					<td style="color:blue">$-<?php echo number_format($transaction_fees,2);?></td>



					<td style="color:blue">-$<?php echo number_format($shipping_paid,2);?></td>

					<td>$<?php echo number_format($shipping_cost,2);?></td>



					<td>$<?php echo number_format($total_vouchers,2);?></td>

					<td>$<?php echo number_format($total_coupons,2);?></td>



					<td>$<?php echo number_format($total,2);?></td>

				</tr>

				<tr style="display:none" id="tr_<?php echo $store_type;?>">

					<td colspan="8"></td>

				</tr>

				<?php	



			}

			?>



		</tbody>

		<tfoot>

			<tr>

				<td colspan="2"><strong>Totals:</strong></td>

				<td style="font-weight:bold"><?php echo number_format($total_i);?></td>

				<td style="font-weight:bold">$<?php echo number_format($total_total_price,2);?></td>

				<td style="font-weight:bold">$<?php echo number_format($total_true_cost,2);?></td>

				<td style="font-weight:bold">$<?php echo number_format($tt_transaction_fees,2);?></td>

				<td style="font-weight:bold">$<?php echo number_format($total_shipping_paid,2);?></td>

				<td style="font-weight:bold">$<?php echo number_format($total_shipping_cost,2);?></td>

				<td style="font-weight:bold">$<?php echo number_format($tt_vouchers,2);?></td>

				<td style="font-weight:bold">$<?php echo number_format($tt_coupons,2);?></td>

				<td style="font-weight:bold">$<?php echo number_format($total_total,2);?></td>



			</tr>

		</tfoot>



	</table>



	<br>

	<br>

	<?php /*if ($order_data) { ?>

	<h2>Orders</h2>

	<table border="1" cellpadding="5" cellspacing="0" width="80%" class="tablesorter">

		<thead>

			<tr>

				<th>S</th>

				<th>Added</th>

				<th>Order ID</th>

				<th>Name</th>

				<th>Email</th>

				<th>Status</th>

				<!-- <th>Total</th> -->

			</tr>

		</thead>

		<tbody>



			<?= $order_data; ?>



		</tbody>

	</table>





	<br><br>

	<?php } */?>



	<?php



	$replacements = $db->func_query("SELECT a.* FROM inv_orders a,oc_order b WHERE a.order_id=b.order_id and  b.ref_order_id <> '' and b.order_status_id<>11  AND LOWER(a.order_status) IN (". "'" . implode("','", $order_status_query_array) . "'".") and $where1 ");



	



	$r_i = 0;

	// $r_a = 0.00;

	// $_t = 0.00;

	$vouchers = '';

	$voucher_i = 1;

	$refund_data = '';

	$r_ids = array();

	// foreach($replacements as $replacement)

	// {

	// 	$r_items = $db->func_query("SELECT * FROM inv_orders_items WHERE order_id='".$replacement['order_id']."'");

	// 	foreach($r_items as $_r)

	// 	{	

	// 		$r_a = $_r['product_price'];

	// 		$_t = $_r['product_true_cost'];	



	// 	}

	// }







	?>



	<?php



	//$returns = $db->func_query("SELECT a.* FROM inv_returns a WHERE $where3 ORDER BY date_added DESC");



	$return_total = 0.00;

	// $replacement_total = 0.00;

	// $refund_total = 0.00;

	// $credit_total = 0.00;



	// $replacement_true = 0.00;

	// $refund_true = 0.00;

	// $credit_true = 0.00;



	// $replacement_i = 0;

	// $refund_i = 0;

	// $credit_i = 0;

	//foreach($returns as $row)

	//{

		//$replacements = $db->func_query("SELECT order_id, price FROM inv_return_decision WHERE return_id ='".$row['id']."' and action='Issue Replacement' ");

		// foreach($replacements as $replacement){

		// 	if ($replacement['order_id']) {

		// 		$r_ids[] = $replacement['order_id'];



		// 		$replacement_total+= $replacement['price']; 



		// 		$replacement_i++;

		// 	}

		// }





		// $credits = $db->func_query("SELECT `date_added`,`voucher_id`,`code`,`amount` FROM oc_voucher WHERE code LIKE '%".$row['order_id']."%'");





		// foreach($credits as $credit)

		// {

		// 	$vouchers .= '<tr>'

		// 	.'<td>'. $voucher_i++ .'</td>'

		// 	.'<td>'. americanDate($credit['date_added'])	 .'</td>'

		// 	.'<td>'. $credit['code'] .'</td>'

		// 	.'<td>'. linkToVoucher($credit['voucher_id']) .'</td>'

		// 	.'<td>'. $credit['amount'] .'</td>';

		// 	$credit_total+=$credit['amount'];

		// 	$credit_i++;

		// }



		// $refunds_invoices = $db->func_query("SELECT order_id, price, date_added FROM inv_return_decision WHERE return_id ='".$row['id']."' and action='Issue Refund' ");

		// foreach($refunds_invoices as $refund_invoice)

		// {

		// 	$refund_i++;

		// 	$refund_total+=($refund_invoice['price']);

		// 	$refund_data .= '<tr>'

		// 	.'<td>'. $refund_i .'</td>'

		// 	.'<td>'. americanDate($refund_invoice['date_added']) .'</td>'

		// 	.'<td>'. linkToOrder($refund_invoice['order_id']) .'</td>'

		// 	.'<td>$'. number_format($refund_invoice['price'],2) .'</td>';



		// }



	//}



	

	$credits = $db->func_query("SELECT `date_added`,`voucher_id`,`code`,`amount` FROM oc_voucher WHERE $where3");



	$credit_total = 0.00;

	$credit_true = 0.00;

	$credit_i = 0;

	$vouchers = '';

	foreach($credits as $vi => $credit)

	{

		$vouchers .= '<tr>'

		.'<td>'. ($vi + 1) .'</td>'

		.'<td>'. americanDate($credit['date_added'])	 .'</td>'

		.'<td>'. $credit['code'] .'</td>'

		.'<td>'. linkToVoucher($credit['voucher_id']) .'</td>'

		.'<td>'. $credit['amount'] .'</td>';

		$credit_total+=$credit['amount'];

		$credit_i++;

	}



	$refunds_invoices = $db->func_query("SELECT order_id, price, date_added FROM inv_return_decision WHERE  action='Issue Refund' AND $where3");



	$refund_i = 0;

	$refund_total = 0.00;

	$refund_data = '';



	foreach($refunds_invoices as $refund_invoice) {

		$refund_i++;

		$refund_total+=($refund_invoice['price']);

		$refund_data .= '<tr>'

		.'<td>'. $refund_i .'</td>'

		.'<td>'. americanDate($refund_invoice['date_added']) .'</td>'

		.'<td>'. linkToOrder($refund_invoice['order_id']) .'</td>'

		.'<td>$'. number_format($refund_invoice['price'],2) .'</td>';



	}





	$replacement_orders = array();

	$replacement_orders = $db->func_query('SELECT a.* FROM inv_orders a WHERE LCASE(a.payment_source) = LCASE("Replacement") AND' . $where1);



	$replacement_data = '';

	$replacement_total = 0.00;

	$replacement_i = 0;



	foreach ($replacement_orders as $i => $row) {



		if ($row['order_id']) {

			$r_ids[] = $row['order_id'];



			$replacement_total+= $db->func_query_first_cell('select sum(product_price) from inv_orders_items where order_id = "'. $row['order_id'] .'"');



			$replacement_i++;

		}



		$replacement_data .= '<tr>'

		.'<td>' . ($i + 1) . '</td>'

		.'<td>'. americanDate($row['order_date']) .'</td>'

		.'<td>'. linkToOrder($row['order_id']) .'</td>'

		.'<td>'. $row['customer_name'] . '</td>'

		.'<td>'. linkToProfile($row['email']) .'</td>'

		.'<td>'. $row['order_status'] .'</td>'

		// .'<td>$'. number_format($row['total_price'],2) .'</td>'

		.'</tr>';

	}

	?>

	<?php if ($replacement_data) { ?>

	<h2>Replacements</h2>

	<table border="1" cellpadding="5" cellspacing="0" width="80%" class="tablesorter">

		<thead>

			<tr>

				<th>SN</th>

				<th>Added</th>

				<th>Order ID</th>

				<th>Name</th>

				<th>Email</th>

				<th>Status</th>

				<!-- <th>Total</th> -->

			</tr>

		</thead>

		<tbody>



			<?= $replacement_data; ?>



		</tbody>

	</table>





	<br><br>



	<?php } ?>

	<?php if ($vouchers) { ?>

	<h2>Vouchers</h2>

	<table border="1" cellpadding="5" cellspacing="0" width="80%" class="tablesorter">

		<thead>

			<tr>

				<th>SN</th>

				<th>Added</th>

				<th>Code</th>

				<th>Voucher ID</th>

				<th>Amount</th>

			</tr>

		</thead>

		<tbody>



			<?= $vouchers; ?>



		</tbody>

	</table>





	<br><br>

	<?php } ?>



	<?php if ($refund_data) { ?>

	<h2>Refund</h2>

	<table border="1" cellpadding="5" cellspacing="0" width="80%" class="tablesorter">

		<thead>

			<tr>

				<th>SN</th>

				<th>Added</th>

				<th>Order ID</th>

				<th>Amount</th>

			</tr>

		</thead>

		<tbody>



			<?= $refund_data; ?>



		</tbody>

	</table>





	<br><br>

	<?php } ?>





	<div align="center" style="width:40%">

		<table cellpadding="10" cellspacing="0" width="50%" border="0" style="background-color:#dcdcdc">



			<tr style="font-weight:bold">

				<td>Net Total:</td>

				<td>$<?php echo number_format($total_total,2);?></td>

			</tr>



			<tr style="font-weight:bold">

				<td>Replacement (<?php echo $replacement_i;?>):</td>

				<td>-$<?php echo number_format($replacement_total,2);?></td>

			</tr>



			<tr style="font-weight:bold">

				<td>Store Credit (<?php echo $credit_i;?>):</td>

				<td>-$<?php echo number_format($credit_total,2);?></td>

			</tr>



			<tr style="font-weight:bold">

				<td>Refund (<?php echo $refund_i;?>):</td>

				<td>-$<?php echo number_format($refund_total,2);?></td>

			</tr>



			<tr style="font-weight:bold">

				<td>Total Returns:</td>

				<td>$<?php echo number_format($refund_total+$credit_total+$replacement_total,2);?></td>

			</tr>







			<tr style="font-weight:bold;border-top:1px dotted black;" >

				<td>Total:</td>

				<td>$<?php echo number_format($total_total-$replacement_total-$credit_total-$refund_total,2);?></td>

			</tr>





		</table>



	</div>



	<br><br>

</div>

<br><br>

















</body>

</html>

<script src="js/moment.js"></script>

<script src="js/pikaday.js"></script>

    <script src="js/pikaday.jquery.js"></script>

     <script>



    var $datepicker = $('.datepicker').pikaday({

        firstDay: 1,

        minDate: new Date(2000, 0, 1),

        maxDate: new Date(2020, 12, 31),

        yearRange: [2000,2020]

    });

    // chain a few methods for the first datepicker, jQuery style!

    $datepicker.toString();



    </script>			

<script>

	$(document).ready(function(e) {});



</script>

<!--<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>-->

<script>

	function showOrders(store_type)

	{



		$.ajax({

			url: 'finance_report.php',

			type: 'post',

			data: {action: 'show_orders', store_type: store_type},



			beforeSend: function () {

				$('#td_'+store_type).html('<img src="<?php echo $host_path;?>images/loading.gif" height="18" width="18">');

				$('#tr_'+store_type).hide();

			},

			complete: function () {

				$('#td_'+store_type).html('<img onClick="showOrders(\''+store_type+'\')" style="cursor:pointer" src="<?php echo $host_path;?>images/plus.png" height="18" width="18">');

			},

			success: function (data) {

				if (data=='') {

					alert('Some Error Occured! Please refresh your page');

				}



				if (data) {

					$('#tr_'+store_type).show();

					$('#tr_'+store_type+' td').html(data)

				}

			}

		});

	}

	$(document).ready(function(e) {

			//$(".tablesorter").tablesorter();









		});

</script>