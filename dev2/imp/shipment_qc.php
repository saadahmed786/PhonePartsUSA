<?php

include_once 'auth.php';

include_once 'inc/functions.php';

include_once 'inc/split_page_results.php';
function updateStockLog($sku)
{
	global $db;
		$check = $db->func_query_first("SELECT * FROM inv_product_inout_stocks where lower(product_sku)='".trim(strtolower($sku))."' order by date_modified desc limit 1");
		if($check['instock_date']=='0000-00-00 00:00:00')
		{
			$db->db_exec("update inv_product_inout_stocks SET product_sku='".trim($sku)."',instock_date='".date('Y-m-d H:i:s')."',date_modified='".date('Y-m-d H:i:s')."' where product_sku='".trim($sku)."' ");
			
		}
}
$shipment_id = (int) $_GET['shipment_id'];
$shipping_cost = $db->func_query_first_cell("select shipping_cost from inv_shipments where id = '$shipment_id'");
$exchange_rate = $db->func_query_first_cell("select ex_rate from inv_shipments where id = '$shipment_id'");
$shipment_item_total = $db->func_query_first_cell("select sum(qty_received) from inv_shipment_items where shipment_id = '$shipment_id'");
$per_item_shipping_cost = $shipping_cost/$shipment_item_total;

//save shipment qc details

if ($_POST['QCUpdate'] || $_POST['QcComplete']) {

	$package_number = $_POST['package_number'];

	unset($_POST['package_number']);

	$rejected = 0;

	$total_qty = 0;

	foreach ($_POST['products'] as $product) {

		$qc_shipment = array();

		$qc_shipment['shipment_id'] = $shipment_id;

		$qc_shipment['product_sku'] = $product['product_sku'];

		$qc_shipment['accept_all'] = ($product['accept_all']) ? 1 : 0;

		$qc_shipment['grade_a'] = $product['grade_a'];

		$qc_shipment['grade_a_qty'] = $product['grade_a_qty'];

		$qc_shipment['grade_b'] = $product['grade_b'];

		$qc_shipment['grade_b_qty'] = $product['grade_b_qty'];

		$qc_shipment['grade_c'] = $product['grade_c'];

		$qc_shipment['grade_c_qty'] = $product['grade_c_qty'];

		$qc_shipment['grade_d'] = $product['grade_d'];

		$qc_shipment['grade_d_qty'] = $product['grade_d_qty'];

		$qc_shipment['rejected'] = $product['rejected'];

		$qc_shipment['rejected_reason'] = ($qc_shipment['rejected'] > 0) ? $product['rejected_reason'] : '';

		$qc_shipment['ntr'] = $product['ntr'];

		$qc_shipment['ntr_reason'] = ($qc_shipment['ntr'] > 0) ? $product['ntr_reason'] : '';

		$qc_shipment['date_modified'] = date('Y-m-d H:i:s');

		$qc_shipment['user_id'] = $_SESSION['user_id'];

		$checkExist = $db->func_query_first_cell("select id from inv_shipment_qc where shipment_id = '$shipment_id' and product_sku = '" . $product['product_sku'] . "'");

		if (!$checkExist) {

			$db->func_array2insert("inv_shipment_qc", $qc_shipment);

		} else {

			$db->func_array2update("inv_shipment_qc", $qc_shipment, " id = '$checkExist'");

		}

    //now add rejected sku to shipments ANd update Product Qty
		if ($_POST['QcComplete']) {

			if ($qc_shipment['rejected'] > 0) {

				$rejected = 1;

				//New Cost updated by gohar on 10/31/2017
				$rtv_item_cost = $product['product_price'] / $exchange_rate;
				//$rtv_item_cost = ($product['product_price']+$per_item_shipping_cost) / $exchange_rate;

				addToRejectedShipment($product['product_sku'], $product['rejected'], $shipment_id, $product['rejected_reason'],$rtv_item_cost);

			} else {

				removeFromRejectedShipment($product['product_sku'], $shipment_id);

			}

			if ($qc_shipment['ntr'] > 0) {

				$ntr = 1;

				$ntr_p[] = needToRepairShipment($product['product_sku'], $product['ntr'], $shipment_id, $product['ntr_reason']);

			} else {

				removeFromNeedToRepairShipment($product['product_sku'], $shipment_id);

			}


			$total_qty_products = (int)$product['qty_received'];
			$total_recivedOther = (int)$product['grade_a_qty'] + (int)$product['grade_b_qty'] + (int)$product['grade_c_qty'] + (int)$product['grade_d_qty'] + (int)$product['rejected'] + (int)$product['ntr'];
			$received_qty = $total_qty_products - $total_recivedOther;

			if ($product['product_sku'] && $received_qty > 0) {
				$p_sku = $product['product_sku'];
				$productArray = array('quantity' => ($received_qty + $db->func_query_first_cell("SELECT quantity FROM oc_product WHERE model = '$p_sku'")),'prefill'=>0);
				$db->func_array2update("oc_product", $productArray, " sku = '". $p_sku ."'");
				makeLedger($shipment_id,array($p_sku=>(int)$received_qty),$_SESSION['user_id'],'adjustment','Shipment QC &rarr; Completed');
				updateStockLog($p_sku);
			}

			if ($product['grade_a'] && $product['grade_a_qty'] > 0) {
				$p_sku = $product['grade_a'];
				$productArray = array('quantity' => ($product['grade_a_qty'] + $db->func_query_first_cell("SELECT quantity FROM oc_product WHERE model = '$p_sku'")),'prefill'=>0);
				$db->func_array2update("oc_product", $productArray, " sku = '". $p_sku ."'");
				makeLedger($shipment_id,array($p_sku=>(int)$product['grade_a_qty']),$_SESSION['user_id'],'adjustment','Shipment QC &rarr; Completed');
				updateStockLog($p_sku);
			}

			if ($product['grade_b'] && $product['grade_b_qty'] > 0) {
				$p_sku = $product['grade_b'];
				$productArray = array('quantity' => ($product['grade_b_qty'] + $db->func_query_first_cell("SELECT quantity FROM oc_product WHERE model = '$p_sku'")),'prefill'=>0);
				$db->func_array2update("oc_product", $productArray, " sku = '". $p_sku ."'");
				makeLedger($shipment_id,array($p_sku=>(int)$product['grade_b_qty']),$_SESSION['user_id'],'adjustment','Shipment QC &rarr; Completed');
				updateStockLog($p_sku);
			}

			if ($product['grade_c'] && $product['grade_c_qty'] > 0) {
				$p_sku = $product['grade_c'];
				$productArray = array('quantity' => ($product['grade_c_qty'] + $db->func_query_first_cell("SELECT quantity FROM oc_product WHERE model = '$p_sku'")),'prefill'=>0);
				$db->func_array2update("oc_product", $productArray, " sku = '". $p_sku ."'");
				makeLedger($shipment_id,array($p_sku=>(int)$product['grade_c_qty']),$_SESSION['user_id'],'adjustment','Shipment QC &rarr; Completed');
				updateStockLog($p_sku);
			}

			if ($product['grade_d'] && $product['grade_d_qty'] > 0) {
				$p_sku = $product['grade_d'];
				$productArray = array('quantity' => ($product['grade_d_qty'] + $db->func_query_first_cell("SELECT quantity FROM oc_product WHERE model = '$p_sku'")),'prefill'=>0);
				$db->func_array2update("oc_product", $productArray, " sku = '". $p_sku ."'");
				makeLedger($shipment_id,array($p_sku=>(int)$product['grade_d_qty']),$_SESSION['user_id'],'adjustment','Shipment QC &rarr; Completed');
				updateStockLog($p_sku);
			}

			$db->db_exec("UPDATE oc_product SET prefill=0 WHERE model='".$product['product_sku']."'");


		}
		$total_qty += $product['qty_received'];
	}
  // $to = $db->func_query_first_cell( "select package_number from inv_shipments where id = '$shipment_id'" );
  // logRejectItem(count($_POST['products']),' items added from Shipment <a href="'.$host_path.'view_shipment.php?shipment_id='.$shipment_id.'">' . $to . '</a> by ','',$to);

	$db->db_exec("update inv_shipments SET date_qc = '" . date('Y-m-d H:i:s') . "'  where id = '$shipment_id'");

	if ($_POST['QCUpdate']) {

		$log = 'Shipment #: ' . linkToShipment($shipment_id, $host_path, $package_number) . ' QC is Updated';

	}

	if ($_POST['QcComplete']) {

		$log = 'Shipment #: ' . linkToShipment($shipment_id, $host_path, $package_number) . ' QC is Completed';

	}

	actionLog($log);

	$shipment_detail = $db->func_query_first("select * from inv_shipments where id = '$shipment_id'");

	if ($_POST['QcComplete'] && $_SESSION['qc_shipment']) {

		$db->db_exec("update inv_shipments SET status = 'Completed' , date_completed = '" . date('Y-m-d H:i:s') . "'  where id = '$shipment_id'");

		$ex_rate = $db->func_escape_string($shipment_detail['ex_rate']);

		if ($total_qty <= 0) {

			$total_qty = 1;

		}

		$item_shipping_cost = round($shipment_detail['shipping_cost'] / $total_qty, 4);

    //save shipment cost

		foreach ($_POST['products'] as $product) {

			$previous = $db->func_query_first("SELECT * FROM inv_product_costs WHERE sku='$product_sku' ORDER BY id DESC LIMIT 1");

			$product_sku = $db->func_escape_string($product['product_sku']);

			$product_price = $db->func_escape_string($product['product_price']);

			$refurb_cost = $db->func_escape_string($product['refurb_cost']);

			$landed_cost_old = getTrueCost($product['product_sku']);
			
				if($shipment_detail['vendor']!=97)
				{
			addUpdateProductCost($product_sku, $product_price, $ex_rate, $item_shipping_cost, $refurb_cost);
				}
			
			$current_cost = (float) $db->func_query_first_cell("SELECT cost FROM inv_avg_cost WHERE sku='" . $product['product_sku'] . "'");

			$new_qty = (int) $product['qty_received'] - (int) $product['rejected'];
			$current_qty = (int) $db->func_query_first_cell("SELECT quantity FROM oc_product WHERE sku='" . $product['product_sku'] . "'");
			$current_qty = $current_qty - $new_qty;
			$new_cost = (float) $db->func_query_first_cell("SELECT current_cost FROM inv_product_costs WHERE sku='" . $product['product_sku'] . "' ORDER BY id DESC LIMIT 1");

			if ($new_qty > 0 && $shipment_detail['vendor']!='97') {

				$avg_cost = (($current_qty * $landed_cost_old) + ($new_qty * getTrueCost($product['product_sku']))) / ($current_qty + $new_qty);

        // echo 'Sku: '.$product['product_sku']."<br>";

        // echo 'Current Qty: '.$current_qty.'<br>';

        // echo 'Current Cost: '.$landed_cost_old.'<br>';

        // echo 'New Qty: '.$new_qty.'<br>';

        // echo 'New Cost: '.getTrueCost($product['product_sku']).'<br><br>===============================<br>';;
        // echo 'Average Cost: '.$avg_cost.'<br><br>===============================<br>';exit;
				$db->db_exec("UPDATE inv_product_costs SET avg_cost = '".(float)$avg_cost."' WHERE sku = '".$product['product_sku']."'ORDER BY id DESC LIMIT 1");


				$avg_check = $db->func_query_first("SELECT * FROM inv_avg_cost WHERE sku='" . $product['product_sku'] . "'");

				if (!$avg_check) {

					$db->db_exec("INSERT INTO inv_avg_cost SET sku='" . $product['product_sku'] . "',cost='" . getTrueCost($product['product_sku']) . "',true_cost='" . getTrueCost($product['product_sku']) . "',date_added='" . date('Y-m-d H:i:s') . "'");} else {

						$db->db_exec("UPDATE inv_avg_cost SET sku='" . $product['product_sku'] . "',cost='" . $avg_cost . "',true_cost='" . getTrueCost($product['product_sku']) . "',date_updated='" . date('Y-m-d H:i:s') . "' WHERE sku='" . $product['product_sku'] . "'");

					}

				}

      //if($previous['raw_cost']!=$product_price and $previous['ex_rate']!=$ex_rate and $previous['shipping_fee']!=$item_shipping_cost)

      //{
				if($shipment_detail['vendor']!=97)
				{

				addToPriceChangeReport($shipment_id, $product_sku, $product_price, $ex_rate, $item_shipping_cost);
					}
      //}

			}

			$_SESSION['message'] = "Shipment status is Completed";

			if ($rejected || $ntr) {

				$last_id = $db->func_query_first_cell("select id from inv_rejected_shipments where vendor = '" . $shipment_detail['vendor'] . "' and status = 'Pending'");

				header("Location:update_rejectedshipment.php?id=$shipment_id&shipment_id=$last_id" . (($ntr) ? '&ntr=' . implode(',', $ntr_p) : ''));

				exit;

			} else {

				if($_POST['QcComplete'])
		{
			$_detail = $db->func_query_first("SELECT vendor,package_number FROM inv_shipments WHERE id='".$shipment_id."'");
			// print_r($_detail);exit;
			if($_detail['vendor']=='1')
			{
				$_products = $db->func_query("SELECT * FROM inv_shipment_items WHERE cu_po<>'' and shipment_id='".$shipment_id."'");
				foreach($_products as $_product)
				{
					$shipment_url = $host_path.'view_shipment.php?shipment_id='.$shipment_id;
					$_sku = $db->func_query_first_cell("SELECT sku from oc_product WHERE product_id='".$_product['product_id']."'");
					$fd_data = array(
						'action'=>'create',
						'description'=>$_sku.' has arrived in '.$_detail['package_number'].' for Reference: '.$_product['cu_po']."<br>Please inform customer and fulfill the customers order.<br>URL: <a href='".$shipment_url."'>".$shipment_url."</a>",

						'subject'=> $_sku.' has arrived in Shipment# '.$_detail['package_number'].' for Reference: '.$_product['cu_po'],
						'email'=>'incoming@ppusa.com',
						'name'=>'Incoming'


						);


					$ch = curl_init (); // Initialising cURL
					$options = Array(
	CURLOPT_RETURNTRANSFER => TRUE, // Setting cURL's option to return the webpage data
	CURLOPT_FOLLOWLOCATION => TRUE, // Setting cURL to follow 'location' HTTP headers
	CURLOPT_AUTOREFERER => TRUE, // Automatically set the referer where following 'location' HTTP headers
	CURLOPT_CONNECTTIMEOUT => 120, // Setting the amount of time (in seconds) before the request times out
	CURLOPT_TIMEOUT => 120, // Setting the maximum amount of time for cURL to execute queries
	CURLOPT_MAXREDIRS => 10, // Setting the maximum number of redirections to follow
	CURLOPT_COOKIEJAR => COOKIE_FILE,
	CURLOPT_COOKIEFILE=> COOKIE_FILE,
	CURLOPT_POST=>1,
	CURLOPT_POSTFIELDS =>http_build_query($fd_data),
	
	CURLOPT_URL => $host_path.'freshdesk/create_ticket.php?config=1' ); // Setting cURL's URL option with the $url variable passed into the function
	// curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt_array ( $ch, $options ); // Setting cURL's options using the previously assigned array data in $options
	$data = curl_exec ( $ch ); // Executing the cURL request and assigning the returned data to the $data variable
	curl_close ( $ch ); // Closing cURL

	// echo $data;exit;

}
}
}



				header("Location:shipments.php");

				exit;

			}

		}



$_SESSION['message'] = "QC Shipment is updated";

header("Location:shipment_qc.php?shipment_id=$shipment_id");

exit;

}

$shipment_detail = array();

if ($shipment_id) {

	$shipment_detail = $db->func_query_first("select * from inv_shipments where id = '$shipment_id'");

	$shipment_query = "select sq.* , si.product_sku , si.qty_received , si.unit_price , si.refurb_cost

	from inv_shipment_items si left join inv_shipment_qc sq on

	(si.product_sku = sq.product_sku and si.shipment_id = sq.shipment_id)

	where si.shipment_id = '$shipment_id' and si.product_sku != ''";

	$shipment_items = $db->func_query($shipment_query);

	foreach ($shipment_items as $index => $product) {

		$shipment_items[$index]['grade_skus'] = $db->func_query("select model,item_grade from oc_product where main_sku = '" . $product['product_sku'] . "'", "item_grade");

	}

}

//print "<pre>";print_r($shipment_items); exit;

if (!$shipment_detail) {

	header("Location:shipments.php");

	exit;

}

?>

<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<title>QC Shipment</title>

	<script type="text/javascript" src="js/jquery.min.js"></script>

	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>

	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />



	<script type="text/javascript">

		$(document).ready(function() {

			$('.fancybox').fancybox({ width: '400px', height : '200px' , autoSize : true });

			$('.fancybox3').fancybox({ width: '1200px', height : '800px' , autoCenter : true , autoSize : false });

		});



		function showDiv(val , product_sku){

			if(val){

				jQuery('.product_'+product_sku+'_row').hide();

			}

			else{

				jQuery('.product_'+product_sku+'_row').show();

			}

		}

	</script>

</head>

<body>

	<?php include_once 'inc/header.php';?>



	<?php if (@$_SESSION['message']): ?>

		<div align="center"><br />

			<font color="red"><?php echo $_SESSION['message'];unset($_SESSION['message']); ?><br /></font>

		</div>

	<?php endif;?>



	<div align="center">

		<form id = "form" method="post" onsubmit="return confirmQuantity();" action="">

			<br />

			<div>

				<h2>Shipment Number: <?php echo $shipment_detail['package_number']; ?></h2>

			</div>



			<div>

				<?php if ($shipment_items): ?>

					<table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">

						<thead>

							<tr>

								<th>#</th>

								<th>SKU</th>

								<th>QTY Received</th>

								<th>Accept All</th>

								<th>Grade A</th>

								<th>Grade B</th>

								<th>Grade C</th>

								<th>Grade D</th>

								<th>Reject</th>

								<th>NTR</th>

							</tr>

						</thead>

						<tbody>

							<?php foreach ($shipment_items as $i => $product): ?>

								<tr class="row_<?php echo $product['product_sku']; ?> products" data-sku="<?php echo $product['product_sku']; ?>">

									<td align="center"><?php echo $i + 1; ?></td>



									<td align="center">

										<?php echo $product['product_sku']; ?>

										<input type="hidden" name="products[<?php echo $i ?>][product_sku]" value="<?php echo $product['product_sku']; ?>" />

										<input type="hidden" name="products[<?php echo $i ?>][product_price]" value="<?php echo $product['unit_price']; ?>" />

										<input type="hidden" name="products[<?php echo $i ?>][refurb_cost]" value="<?php echo $product['refurb_cost']; ?>" />

										<input type="hidden" class="qty_received" name="products[<?php echo $i ?>][qty_received]" value="<?php echo $product['qty_received']; ?>" />



										<br />

										<?php $issue_count = $db->func_query_first_cell("select count(id) from inv_product_issues where product_sku = '" . $product['product_sku'] . "'")?>

										<a class="fancybox3 fancybox.iframe" href="popupfiles/product_issues.php?product_sku=<?php echo $product['product_sku']; ?>"><?php echo $issue_count ?> of item issues</a>

									</td>



									<?php $qty_left = $product['qty_received'] - $product['grade_a_qty'] - $product['grade_b_qty'] - $product['grade_c_qty'];?>



									<td align="center">

										<?php if (!is_null($product['accept_all']) && $product['accept_all'] != 1): ?>



											<p><?php echo $qty_left . " -- " . $product['product_sku']; ?></p>



											<?php if ($product['grade_a_qty'] > 0): ?>

												<p><?php echo $product['grade_a_qty'] . " -- " . $product['grade_a']; ?></p>

											<?php endif;?>



											<?php if ($product['grade_b_qty'] > 0): ?>

												<p><?php echo $product['grade_b_qty'] . " -- " . $product['grade_b']; ?></p>

											<?php endif;?>



											<?php if ($product['grade_c_qty'] > 0): ?>

												<p><?php echo $product['grade_c_qty'] . " -- " . $product['grade_c']; ?></p>

											<?php endif;?>



										<?php else: ?>

											<?php echo $product['qty_received']; ?>

										<?php endif;?>

									</td>



									<td align="center">

										<input type="checkbox" onclick="showDiv(this.checked,'<?php echo $product['product_sku'] ?>');" name="products[<?php echo $i ?>][accept_all]" value="1" <?php if ($product['accept_all'] == 1 || is_null($product['accept_all'])): ?> checked="checked" <?php endif;?> />

									</td>



									<td align="center">

										<div class="product_<?php echo $product['product_sku']; ?>_row" <?php if ($product['accept_all'] == 1 || is_null($product['accept_all'])): ?> style="display:none;" <?php endif;?>>

											<?php if (!$product['grade_a']) {

												$product['grade_a'] = $product['grade_skus']['Grade A']['model'];

											}

											?>

											<?php if ($product['grade_a']): ?>

												<input type="text" readonly="readonly" size="15" name="products[<?php echo $i ?>][grade_a]" value="<?php echo $product['grade_a'] ?>" />

											<?php else: ?>

												<a class="fancybox fancybox.iframe" href="create_sku.php?main_sku=<?php echo $product['product_sku'] ?>&grade=A">Create SKU</a>

											<?php endif;?>

											<br />



											<br />

											<input type="text" class="grade_a" size="5" name="products[<?php echo $i ?>][grade_a_qty]" value="<?php echo ($product['grade_a_qty']) ? $product['grade_a_qty']: '0'; ?>" />

										</div>

									</td>



									<td align="center">

										<div class="product_<?php echo $product['product_sku']; ?>_row" <?php if ($product['accept_all'] == 1 || is_null($product['accept_all'])): ?> style="display:none;" <?php endif;?>>

											<?php if (!$product['grade_b']) {

												$product['grade_b'] = $product['grade_skus']['Grade B']['model'];

											}

											?>

											<?php if ($product['grade_b']): ?>

												<input type="text" readonly="readonly" size="15" name="products[<?php echo $i ?>][grade_b]" value="<?php echo $product['grade_b'] ?>" />

											<?php else: ?>

												<a class="fancybox fancybox.iframe" href="create_sku.php?main_sku=<?php echo $product['product_sku'] ?>&grade=B">Create SKU</a>

											<?php endif;?>

											<br />



											<br />

											<input type="text" class="grade_b" size="5" name="products[<?php echo $i ?>][grade_b_qty]" value="<?php echo ($product['grade_b_qty']) ? $product['grade_b_qty']: '0'; ?>" />

										</div>

									</td>



									<td align="center">

										<div class="product_<?php echo $product['product_sku']; ?>_row" <?php if ($product['accept_all'] == 1 || is_null($product['accept_all'])): ?> style="display:none;" <?php endif;?>>

											<?php if (!$product['grade_c']) {

												$product['grade_c'] = $product['grade_skus']['Grade C']['model'];

											}

											?>

											<?php if ($product['grade_c']): ?>

												<input type="text" readonly="readonly" size="15" name="products[<?php echo $i ?>][grade_c]" value="<?php echo $product['grade_c'] ?>" />

											<?php else: ?>

												<a class="fancybox fancybox.iframe" href="create_sku.php?main_sku=<?php echo $product['product_sku'] ?>&grade=C">Create SKU</a>

											<?php endif;?>

											<br />



											<br />

											<input type="text" class="grade_c" size="5" name="products[<?php echo $i ?>][grade_c_qty]" value="<?php echo ($product['grade_c_qty']) ? $product['grade_c_qty']: '0'; ?>" />

										</div>

									</td>



									<td align="center">

										<div class="product_<?php echo $product['product_sku']; ?>_row" <?php if ($product['accept_all'] == 1 || is_null($product['accept_all'])): ?> style="display:none;" <?php endif;?>>

											<?php if (!$product['grade_d']) {

												$product['grade_d'] = $product['grade_skus']['Grade D']['model'];

											}

											?>

											<?php if ($product['grade_d']): ?>

												<input type="text" readonly="readonly" size="15" name="products[<?php echo $i ?>][grade_d]" value="<?php echo $product['grade_d'] ?>" />

											<?php else: ?>

												<a class="fancybox fancybox.iframe" href="create_sku.php?main_sku=<?php echo $product['product_sku'] ?>&grade=D">Create SKU</a>

											<?php endif;?>

											<br />



											<br />

											<input type="text" size="5" class="grade_d" name="products[<?php echo $i ?>][grade_d_qty]" value="<?php echo ($product['grade_d_qty']) ? $product['grade_d_qty']: '0'; ?>" />

										</div>

									</td>



									<td align="center">

										<div class="product_<?php echo $product['product_sku']; ?>_row" <?php if ($product['accept_all'] == 1 || is_null($product['accept_all'])): ?> style="display:none;" <?php endif;?>>

											<input class="rj" type="text" size="5" id="rejectField<?php echo $i ?>" name="products[<?php echo $i ?>][rejected]" value="<?php echo ($product['rejected'])? $product['rejected']: '0'; ?>" /><br><br>

											<input placeholder="Reason" type="hidden"  name="products[<?php echo $i ?>][rejected_reason]" value="<?php echo $product['rejected_reason'] ?>" />

										</div>

									</td>


									<input type="hidden" id="productQty<?php echo $i ?>" value="<?php echo $qty_left ?>">


									<td align="center">

										<div class="product_<?php echo $product['product_sku']; ?>_row" <?php if ($product['accept_all'] == 1 || is_null($product['accept_all'])): ?> style="display:none;" <?php endif;?>>

											<input class="ntr" type="text" size="5" id="ntrField<?php echo $i ?>" name="products[<?php echo $i ?>][ntr]" value="<?php echo ($product['ntr']) ? $product['ntr']: '0'; ?>" /><br><br>

											<input type="hidden" name="package_number" value="<?php echo $shipment_detail['package_number']; ?>">

											<input placeholder="Reason" type="hidden"  name="products[<?php echo $i ?>][ntr_reason]" value="<?php echo $product['ntr_reason'] ?>" />



										</div>

									</td>

								</tr>

								<?php $i++;endforeach;?>

							</tbody>

						</table>



						<br />

						<div align="center" style="margin-right:10%;margin-top:5px;">

							<?php if ($shipment_detail['status'] != 'Completed' && $_SESSION['qc_shipment']): ?>

								<button type="submit" id="QcUpdate" name="QCUpdate" value="QC Update" />
								QC Update
							</button>


							<button type="submit" id="QcComplete" name="QcComplete" value="QcComplete" onclick="if(!confirm('Are you sure?')){ return false; }">

								Save And Complete Shipment

							</button>

						<?php endif;?>

					</div>



				<?php endif;?>

			</div>

		</form>

	</div>
	<script type="text/javascript">
		function confirmQuantity() {
			var error = '';
			$('.products').each(function(index, el) {
				if ($(el).find('[type=checkbox]').prop('checked') == false) {
					var grade_a = parseInt(($(el).find('.grade_a').val()) ? $(el).find('.grade_a').val(): 0);
					var grade_b = parseInt(($(el).find('.grade_b').val()) ? $(el).find('.grade_b').val(): 0);
					var grade_c = parseInt(($(el).find('.grade_c').val()) ? $(el).find('.grade_c').val(): 0);
					var grade_d = parseInt(($(el).find('.grade_d').val()) ? $(el).find('.grade_d').val(): 0);
					var rj = parseInt(($(el).find('.rj').val()) ? $(el).find('.rj').val(): 0);
					var ntr = parseInt(($(el).find('.ntr').val()) ? $(el).find('.ntr').val(): 0);
					var qty_received = parseInt(($(el).find('.qty_received').val()) ? $(el).find('.qty_received').val(): 0);
					var qc_qty = parseInt(rj) + parseInt(ntr) + parseInt(grade_a) + parseInt(grade_b) + parseInt(grade_c) + parseInt(grade_d);
					if (qty_received < qc_qty) {
						error += $(el).data('sku') + ' QC quantity exceeds received quantity\n';
					}
				}
			});
			if(error != '') {
				alert(error);
				return false;
			} else {
				return true;
			}
		}
	</script>
</body>

</html>