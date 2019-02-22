<?php

include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';
include_once 'trello/trellocard.php';
if(!$_SESSION['email']){
	header("Location:index.php");
	exit;
}


$vendors = $db->func_query("select id , name as value from inv_users where group_id = 1");
$carriers = array(
	array('id'=>'USPS','value'=>'USPS'),
	array('id'=>'In House','value'=>'In House'),
	array('id'=>'UPS','value'=>'UPS'),
	array('id'=>'FedEx','value'=>'FedEx'),
	array('id'=>'DHL Express','value'=>'DHL Express'),
	array('id'=>'EMS','value'=>'EMS'),
	array('id'=>'HK Post','value'=>'HK Post'),
	array('id'=>'TNT','value'=>'TNT')
	);
$shipment_id = (int)$_GET['shipment_id'];
if($_POST['follow']) {
	$follow = array();
	$follow['is_followed'] = '1';
	$follow['followed_by'] = $_SESSION['user_id'];
	$db->func_array2update("inv_shipments",$follow,"id = '$shipment_id'");
	$_SESSION['message'] = 'Shipment Followed';

	header("Location:addedit_shipment.php?shipment_id=$shipment_id");
	exit;
}
if($_POST['unfollow']) {
	$follow = array();
	$follow['is_followed'] = '0';
	$follow['followed_by'] = '';
	$db->func_array2update("inv_shipments",$follow,"id = '$shipment_id'");
	$_SESSION['message'] = 'Shipment Un-Followed';

	header("Location:addedit_shipment.php?shipment_id=$shipment_id");
	exit;
}
$comments = $db->func_query("SELECT * FROM inv_shipment_comments where shipment_id='".$shipment_id."'");
if($_POST['addcomment']) {
	
	$_SESSION['message'] = addComment('shipment',array('id' => $shipment_id, 'comment' => $_POST['comment']));

	header("Location:addedit_shipment.php?shipment_id=$shipment_id");
	exit;
}
//save shipment
if($_POST['save'] || $_POST['IssueShipment'] || $_POST['ReceiveIt']){
	unset($_POST['addcomment'], $_POST['comment']);
	$shipment = array();
	$shipment['package_number'] = $db->func_escape_string($_POST['package_number']);
	$shipment['tracking_number'] = $db->func_escape_string($_POST['tracking_number']);
	// $shipment['tracking_number'] = $db->func_escape_string($_POST['tracking_number']);
	$shipment['carrier'] = $db->func_escape_string($_POST['carrier']);
	if($_SESSION['display_exrate']){
		$shipment['ex_rate'] = $db->func_escape_string($_POST['ex_rate']);
	}
	
	if($_SESSION['display_cost']){
		$shipment['shipping_cost'] = $db->func_escape_string($_POST['shipping_cost']);
	}
	
	if($shipment_id){
		if( $_POST['IssueShipment'])
		{
			$shipment['vendor'] = $_SESSION['user_id'];	
		}
		$old_data = $db->func_query_first("SELECT * FROM inv_shipments WHERE id = '$shipment_id'");
		$db->func_array2update("inv_shipments",$shipment,"id = '$shipment_id'");
		if($_POST['vendor'])
		{
			$db->db_exec("UPDATE inv_shipments SET vendor='".(int)$_POST['vendor']."' where id='".$shipment_id."'");
		}
		if($_POST['eta'])
		{
			$db->db_exec("UPDATE inv_shipments SET eta='".date('Y-m-d',strtotime($_POST['eta']))."' where id='".$shipment_id."'");
		}
		if(isset($_POST['tracking_number']) && $_POST['carrier'])
		{
			$db->db_exec("UPDATE inv_shipments SET tracking_number='".$db->func_escape_string($_POST['tracking_number'])."',carrier='".$db->func_escape_string($_POST['carrier'])."' WHERE id='".$shipment_id."'");
		}
		$_SESSION['message'] = "Shipment is updated";
		if($_POST['IssueShipment'] && $_SESSION['edit_pending_shipment']) {
			$log = "Issued ";
		} else if ($_POST['ReceiveIt'] && $_SESSION['edit_received_shipment']) {
			$log = "Received ";
		}
		if ($shipment['ex_rate'] != $old_data['ex_rate'] && $_SESSION['display_exrate']) {
			$log = ($log)? $log: 'updated ';
			$log .= "<br> Exchange Rate From " . $old_data['ex_rate']. " to " . $shipment['ex_rate'];
		}

		if ($shipment['shipping_cost'] != $old_data['shipping_cost'] && $_SESSION['display_cost']) {
			$log = ($log)? $log: 'updated ';
			$log .= "<br> Shipment Cost From " . $old_data['shipping_cost']. " to " . $shipment['shipping_cost'];
		}

		if ((int)$_POST['vendor'] != $old_data['vendor']) {
			$log = ($log)? $log: 'updated ';
			$log .= "<br> Vendor Changed to: " . get_username($_POST['vendor']);
		}

		// if ($shipment['package_number'] != $old_data['package_number']) {
		// 	$log .= " Package Number From " . $old_data['package_number']. " to " . $shipment['package_number'];
		// }
	} else {
		$checkExist = $db->func_query_first_cell("select id from inv_shipments where package_number = '".$shipment['package_number']."'");
		if($checkExist){
			$_SESSION['message'] = "This package number is assigned to another shipment.";
			header("Location:addedit_shipment.php");
			exit;
		}
		else {
			$shipment['status'] = 'Pending';
			$shipment['date_added'] = date('Y-m-d H:i:s');
			$shipment['vendor'] = ($_POST['vendor'])? $_POST['vendor']: $_SESSION['user_id'];	
			$shipment_id = $db->func_array2insert("inv_shipments",$shipment);
			$_SESSION['message'] = "Shipment is created";
			$log = ' created';
			$log .= "<br> Exchange Rate " . $shipment['ex_rate'];
			$log .= "<br> Shipment Cost " . $shipment['shipping_cost'];
			$log .= "<br> From " . get_username($shipment['vendor']);
			// $log .= "<br> Package Number " . $shipment['package_number'];
		}
	}
	
	if($shipment_id) {
		$shipment_item_ids = array();
		foreach($_POST['products'] as $product) {
			$shipment_item = array();
			$shipment_item['product_id']  = $product['product_id'];
			$shipment_item['product_sku'] = $product['model'];
			$shipment_item['cu_po'] = $product['cu_po'];
			if($_SESSION['display_cost']){
				$shipment_item['unit_price']  = $product['price'];
			}

			
			if($_SESSION['edit_pending_shipment']){
				$shipment_item['qty_shipped']  = $product['qty'];
			}
			
			if($_SESSION['edit_received_shipment']){
				$shipment_item['qty_received']  = $product['qty_received'];
			}
			
			$shipment_item['shipment_id'] = $shipment_id;
			
			$checkExist = $db->func_query_first("SELECT * from inv_shipment_items where shipment_id = '$shipment_id' and product_sku = '".$product['model']."' AND rejected_product = '0'");


			if($checkExist){
				$db->func_array2update("inv_shipment_items",$shipment_item,"id = '" . $checkExist['id'] . "'");
				$shipment_item_ids[] = $checkExist['id'];
			}
			else{
				$shipment_item_ids[] = $db->func_array2insert("inv_shipment_items",$shipment_item);
			}
			
			$SKU   = $db->func_escape_string($product['model']);
			$raw_cost = $db->func_escape_string($product['price']);
			$ex_rate  = $db->func_escape_string($_POST['ex_rate']);
			
			$qty = ($product['qty_received']) ? $product['qty_received'] : $shipment_item['qty_shipped'];
			if($qty <= 0){
				$qty = 1;
			}
			
			$shipping_fee = $shipment['shipping_cost'] / $qty;


			

			if ($checkExist['qty_shipped'] != $product['qty']) {
				$plog .= "<br> Shipped " . $product['qty'] . " items";
			}
			
			if ($checkExist['qty_received'] != $product['qty_received']) {
				$plog .= "<br> Received " . $product['qty_received'] . ' items';
			}
			if ($checkExist['unit_price'] != $product['price']) {
				$plog .= '<br> Cost ' . $product['price'];
			}

			if ($plog) {
				$log .= "<br><br> Product " . linkToProduct($product['model']) . $plog;
			}


			//addUpdateProductCost($SKU , $raw_cost , $ex_rate , $shipping_fee);
		}
		
		//check for new products
		foreach($_POST['new_products'] as $product){
			$shipment_item = array();
			$shipment_item['product_id']  = 0;
			$shipment_item['product_name'] = $product['title'];
			$shipment_item['cu_po'] = $product['cu_po'];
			if($product['sku_type']){
				$last_id = getProductSkuLastID($product['sku_type']);
				$shipment_item['product_sku'] = getSKUFromLastId($product['sku_type'] , $last_id);
			}
			else{
				$shipment_item['product_sku'] = $product['model'];
			}
			
			if($_SESSION['edit_pending_shipment']){
				$shipment_item['qty_shipped']  = $product['qty'];
			}
			
			if($_SESSION['edit_received_shipment']){
				$shipment_item['qty_received']  = $product['qty_received'];
			}
			
			if($_SESSION['display_cost']){
				$shipment_item['unit_price']  = $product['price'];
			}
			
			$shipment_item['shipment_id'] = $shipment_id;
			$shipment_item['is_new']  = 1;
			
			$checkExist = $db->func_query_first("select * from inv_shipment_items where shipment_id = '$shipment_id' and product_name = '".$db->func_escape_string($product['title'])."'");

			if($checkExist){
				$db->func_array2update("inv_shipment_items",$shipment_item,"id = '" . $checkExist['id'] . "'");
				$shipment_item_ids[] = $checkExist['id'];
			}
			else{
				$shipment_item_ids[] = $db->func_array2insert("inv_shipment_items",$shipment_item);
			}
			
			if($shipment_item['product_sku']){
				
				createSKU($shipment_item['product_sku'] , $product['title'] , '' , $product['price'] , '' , 1,'','',0,$product['weight']);
				
				$vendor_id = $db->func_query_first_cell("SELECT vendor FROM inv_shipments WHERE id='$shipment_id'");
				if($vendor_id)
				{
					$db->db_exec("UPDATE oc_product SET vendor='".strtolower($db->func_query_first_cell("SELECT name FROM inv_users WHERE id='".(int)$vendor_id."'"))."' WHERE sku='".$shipment_item['product_sku']."'");	
					
				}
				$SKU   = $db->func_escape_string($shipment_item['product_sku']);
				$raw_cost = $db->func_escape_string($product['price']);
				$ex_rate  = $db->func_escape_string($_POST['ex_rate']);
				
				//addUpdateProductCost($SKU , $raw_cost , $ex_rate);
			}

			if ($checkExist['qty_shipped'] != $product['qty']) {
				$plog .= "<br> Shipped " . $product['qty'] . " items";
			}
			
			if ($checkExist['qty_received'] != $product['qty_received']) {
				$plog .= "<br> Received " . $product['qty_received'] . ' items';
			}
			if ($checkExist['unit_price'] != $product['price']) {
				$plog .= '<br> Cost ' . $product['price'];
			}

			if ($plog) {
				$log .= "<br><br> Product " . linkToProduct($product['model']) . $plog;
			}
		}
		
		//delete extrs rows
		if($shipment_item_ids){
			$shipment_item_ids = "'" . implode("', '",$shipment_item_ids) . "'";

			$db->db_exec("delete from inv_shipment_items where shipment_id = '$shipment_id' and not id in ($shipment_item_ids)");
		}
	}

	// $new_items = $db->func_query("SELECT * FROM inv_shipment_items WHERE shipment_id='$shipment_id' and is_new=1 and is_trello_updated=0 ");
	// if($new_items)
	// {
	// 	$sku_data = array();
	// 	foreach($new_items as $new_item )
	// 	{
	// 		$sku_data[] = array(
	// 			'sku'=>$db->func_escape_string($new_item['product_sku']),
	// 			'product_name' => $db->func_escape_string($new_item['product_name'])


	// 			);

	// 		$db->db_exec("UPDATE inv_shipment_items SET is_trello_updated=1 WHERE id='".$new_item['id']."'");
			
	// 	}
	// 	// $trello = new trello();
	// 	// $trello->newSKUImages($sku_data,$shipment['package_number'],$shipment_date['date_received']);
	// }

	if ($log) {
		$log = 'Shipment #: ' . linkToShipment($shipment_id, $host_path, $shipment['package_number']) . ' is ' . $log;
		actionLog($log);
	}
	if($_POST['IssueShipment'] && $_SESSION['edit_pending_shipment']){

		$db->db_exec("update inv_shipments SET status = 'Issued' , date_issued = '".date('Y-m-d H:i:s')."' where id = '$shipment_id'");
		
		$_SESSION['message'] = "Shipment status is Issued";
		header("Location:shipments.php");
		exit;
	}
	
	if($_POST['ReceiveIt'] && $_SESSION['edit_received_shipment']){
		$db->db_exec("update inv_shipments SET status = 'Received',received_by='".$_SESSION['user_id']."' , date_received = '".date('Y-m-d H:i:s')."'  where id = '$shipment_id'");
		$_SESSION['message'] = "Shipment status is Received";
		header("Location:shipments.php");
		$new_items = $db->func_query("SELECT * FROM inv_shipment_items WHERE shipment_id='$shipment_id' and is_new=1 and is_trello_updated=0 ");
		if($new_items)
	{
		$sku_data = array();
		foreach($new_items as $new_item )
		{
			$sku_data[] = array(
				'sku'=>$db->func_escape_string($new_item['product_sku']),
				'product_name' => $db->func_escape_string($new_item['product_name'])


				);

			$db->db_exec("UPDATE inv_shipment_items SET is_trello_updated=1 WHERE id='".$new_item['id']."'");
			
		}
		$trello = new trello();
		$trello->newSKUImages($sku_data,$shipment['package_number'],$shipment_date['date_received']);
	}
		exit;
	}

	unset($_SESSION['list']);
	unset($_SESSION['newlist']);
	header("Location:shipments.php");
	exit;
}

$shipment_detail = array();
if($shipment_id){
	$shipment_detail = $db->func_query_first("select * from inv_shipments where id = '$shipment_id'");
	$list = array();
	$newlist = array();
	
	$shipment_items = $db->func_query("select * from inv_shipment_items where shipment_id = '$shipment_id' and is_new = 0","product_id");
	$_shipped_total = 0;
	$_received_total = 0;
	$_shipping_total = 0.00;
	$_rejected_total = 0.00;
	
	foreach($shipment_items as $product_id => $shipment_item){
		$list[$product_id] = array(	"cu_po"=>$shipment_item['cu_po'],
			"qty"=>$shipment_item['qty_shipped'],
			"qty_received" => $shipment_item['qty_received'],		
			"rejected" => $shipment_item['rejected_product'],		
			"price"=>$shipment_item['unit_price']);

		$_shipped_total+=$shipment_item['qty_shipped'];
		$_received_total+=$shipment_item['qty_received'];
		$_shipping_total+=$shipment_item['unit_price'];
		$_rejected_total+=$db->func_query_first_cell("SELECT rejected FROM inv_shipment_qc WHERE shipment_id='$shipment_id' AND product_sku='".$shipment_item['product_sku']."'");
	}
	
	unset($shipment_items);
	
	$shipment_items = $db->func_query("select * from inv_shipment_items where shipment_id = '$shipment_id' and is_new = 1");
	foreach($shipment_items as $product_id => $shipment_item){
		$newlist[] = array("title"=>$shipment_item['product_name'],
			"qty"=>$shipment_item['qty_shipped'],
			'sku' => $shipment_item['product_sku'],
			"qty_received" => $shipment_item['qty_received'],
			"price"=>$shipment_item['unit_price'],
			"weight"=>$shipment_item['weight']
			);

		
		$_shipped_total+=$shipment_item['qty_shipped'];
		$_received_total+=$shipment_item['qty_received'];
		$_shipping_total+=$shipment_item['unit_price'];
		$_rejected_total+=$db->func_query_first_cell("SELECT rejected FROM inv_shipment_qc WHERE shipment_id='$shipment_id' AND product_sku='".$shipment_item['product_sku']."'");
	}

	unset($shipment_items);
}
else {
	$list = $_SESSION['list'];
	$newlist = $_SESSION['newlist'];
}

// if(count($list) == 0){
// 	$_SESSION['message'] = "Please add at leaat 1 product to list to create a shipment";
// 	header("Location:sales.php");
// 	exit;
// }

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

$product_ids = "'".implode("','",array_keys($list))."'";

	$inv_query   = "select p.product_id , p.model, p.quantity, p.status, p.mps , p.image , pd.name from 
	oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id) 
	where p.product_id in ($product_ids)";
// echo $inv_query;exit;

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "addedit_shipment.php",$page);
$products   = $db->func_query($splitPage->sql_query);

$product_skus = $db->func_query("select sku from inv_product_skus");


$servicers = $db->func_query("select id , name as value from inv_users where group_id = 13 or is_servicer= 1 ");
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Add / Edit Shipment</title>

	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.lazyload.min.js"></script>
	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

	<script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox2').fancybox({ width: '800px', height : '800px' , autoCenter : true , autoSize : false });
			$('.fancybox3').fancybox({ width: '700px', height : '500px' , autoCenter : true , autoSize : false });

			$("img.lazy").lazyload({
				effect : "fadeIn"
			});
		});

		function removeFromList(product_id , is_new){
			jQuery.ajax({
				url: 'inc/ajax.php?action=removeFromList&product_id='+product_id+'&is_new='+is_new,
				success: function(data){
					re = new RegExp(/Error.*?/gi);
					if(re.test(data)){
						alert("Product is not removed from list, try again");
					}
					else{
					    	//alert("Product is removed from list");
					    	jQuery(".row_"+product_id).remove();
					    	jQuery("#row_"+product_id).remove();
					    }

					    updateCart();
					}
				});
		}

		function updateCart(){
			jQuery.ajax({
				url: 'list_items.php',
				success: function(data){
					jQuery('#cart_items').html(data);
				}
			});
		}
	</script>	
	<style type="text/css">
		.cart{
			position:absolute;
			top:15%;
			right:15%;
			text-decoration:underline;
			cursor:pointer;
		}
		.rejected{
			background-color: rgb(255, 202, 202);
		}
	</style>
</head>
<body>
	<?php include_once 'inc/header.php';?>

	<?php if(@$_SESSION['message']):?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
	<?php endif;?>

	<div class="cart" id="cart_items" align="right">
		<?php if(!$shipment_id):?>
			<?php //include_once 'list_items.php';?>
		<?php endif;?>	

	</div>

	<div align="center">
		<form id="frm" method="post" action="">
			<br />
			<div>
				<?php if($_SESSION['display_exrate']):?>
					Ex. Rate:
					<input type="text" name="ex_rate" value="<?php echo $shipment_detail['ex_rate'];?>" required />
				<?php endif;?>	

				&nbsp;

				Purchase Order #:
				<input type="text" name="package_number" value="<?php echo $shipment_detail['package_number'];?>" required />

				&nbsp;
				Tracking #:
				<input type="text" name="tracking_number" value="<?php echo $shipment_detail['tracking_number'];?>"  />


				&nbsp;
				Carrier:

				<?php echo createField("carrier", "carrier" , "select" , $shipment_detail['carrier'] , $carriers);?> &nbsp; &nbsp; 
				<?php if($_SESSION['display_cost']):?>
					Shipping Cost:
					<input type="text" name="shipping_cost" value="<?php echo $shipment_detail['shipping_cost'];?>"  />
				<?php endif;?>	
				<br><br>
				<?php if($_SESSION['display_vendor']):?>
					Vendor:
					<?php if($shipment_detail['is_lbb']==1){ ?>
					 <?php echo createField("servicer", "servicer" , "select" , $shipment_detail['servicer'] , $servicers, 'required=""');?> &nbsp; &nbsp;

					<?php //echo createField("servicer", "servicer" , "input" , get_username($shipment_detail['servicer']), $servicers, 'onclick="$(\'.servicer\').val($(this).val());" class="servicer" readonly name="servicer"');?> &nbsp; &nbsp; 
					<? }else{ ?>
						 <?php echo createField("vendor", "vendor" , "select" , $shipment_detail['vendor'] , $vendors, 'required=""');?> &nbsp; &nbsp;
						<?php }?>
					<?php
					if($_SESSION['display_vendor'])
					{
						?>
						ETA: <input type="date" name="eta" value="<?php echo ($shipment_detail['eta']?date('Y-m-d',strtotime($shipment_detail['eta'])) :'');?>" >
						<?php
					}
					?>

				<?php endif;?>

				<input type="submit" name="save" value="Save" /> &nbsp;
				<?php if($shipment_detail['is_followed']==1){?>
					<input type="submit" class="button" name="unfollow" value="Unfollow Order" />
					<?php } else {?>

				<input type="submit" class="button" name="follow" value="Follow Order" />
				<?php } ?>
			</div>
			
			<?php
			if($_SESSION['shipment_admin_totals'] || $_SESSION['login_as']=='admin')
			{
				?>
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
				<?php
			}
			?>
			<br />
			<?php if (!$shipment_detail['is_lbb']) { ?>
			<a href="addsku.php?shipment_id=<?php echo $shipment_id?>" class="fancybox2 fancybox.iframe">Add SKU</a>
			&nbsp;
			|
			&nbsp;
			<a href="add_newitem.php?shipment_id=<?php echo $shipment_id?>" class="fancybox2 fancybox.iframe">Add New Item</a>
			<?php } ?>

			<br /><br />

			<div>	
				<?php if($products):?>
					<table width="90%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
						<thead>
							<tr>
								<th>#</th>
							<!--	<th align="center">New Item</th>  -->
								<th align="center">New Item</th>
								<th>Image</th>
								<th>In Stock</th>
								<th>Name</th>
								<th>Ref #</th>
								<th>SKU</th>

								<th>Qty Shipped</th>
								<th>Qty Received</th>

								<?php if($_SESSION['display_cost']):?>


									<th>Previous Cost(s)</th>
									<th>Price</th>
									<th>Difference</th>
								<?php endif;?>

								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php $i = $splitPage->display_i_count(); ?>

							<?php foreach($products as $product):?>
								<tr class="row_<?php echo $product['product_id'];?> <?php echo ($list[$product['product_id']]['rejected'] == 1)? 'rejected': ''; ?>">




									<td align="center"><?php echo $i; ?></td>

								<!--	<td align="center" id="sti"><?php echo ($shipment_items['is_new'][$i]?'X':'-');?></td>  -->

									<td align="center" id="sti"><?php echo ($shipment_items['is_new'][$i]?'X':'-');?></td>
									<td align="center">
										<a href="<?php echo (($shipment_detail['is_lbb'])? $product['image']: 'http://cdn.phonepartsusa.com/image/' . $product['image']); ?>" class="fancybox2 fancybox.iframe">
											<img class="lazy" src="<?php echo (($shipment_detail['is_lbb'])? $product['image']: 'http://cdn.phonepartsusa.com/image/' . $product['image']); ?>" data-original="http://cdn.phonepartsusa.com/image/<?php echo $product['image'];?>" height="50" width="50" alt="" />
										</a>	
									</td>
									<td align="center">
										<?php echo $product['quantity'];?>
									</td>

									<td align="center" width="200px">
										<?php echo $product['name'];?>

										<br />
										<?php $issue_count = $db->func_query_first_cell("select count(id) from inv_product_issues where product_sku = '".$product['model']."'")?>
										<a class="fancybox3 fancybox.iframe" href="popupfiles/product_issues.php?product_sku=<?php echo $product['model'];?>"><?php echo $issue_count?> of item issues</a>
									</td>

									<td align="center">
										<input style="width: 50px;" type="text" name="products[<?php echo $i?>][cu_po]" value="<?php echo $list[$product['product_id']]['cu_po']?>" />
									</td>

									<td align="center">
										<a href="product/<?php echo $product['model'];?>"><?php echo $product['model'];?></a>
										<input type="hidden" name="products[<?php echo $i?>][model]" value="<?php echo $product['model'];?>" />	
										<input type="hidden" name="products[<?php echo $i?>][product_id]" value="<?php echo $product['product_id'];?>" />			
									</td>

									<td align="center">
										<?php if($_SESSION['edit_pending_shipment']):?>
											<input style="width: 50px;" required type="text" name="products[<?php echo $i?>][qty]" value="<?php echo $list[$product['product_id']]['qty']?>" />
										<?php else:?>
											<?php echo $list[$product['product_id']]['qty']?>
										<?php endif;?>			
									</td>

									<td align="center">
										<?php if($_SESSION['edit_received_shipment']):?>
											<input style="width: 50px;" type="text" name="products[<?php echo $i?>][qty_received]" value="<?php echo $list[$product['product_id']]['qty_received']?>" />
										<?php else:?>
											<?php echo $list[$product['product_id']]['qty_received']?>
										<?php endif;?>
									</td>

									<?php if($_SESSION['display_cost']):?>
										<?php $cost_queries = $db->func_query("SELECT DISTINCT  DATE(cost_date) as cost_date,raw_cost FROM inv_product_costs WHERE sku='".$product['model']."' ORDER BY id DESC LIMIT 3"); ?>
										<td align="center"> <?php

											if($cost_queries)
											{
												$kk = 0;
												foreach($cost_queries as $cost_query)
												{
													if($kk==0) $previous_costx = $cost_query['raw_cost'];
													echo americanDate($cost_query['cost_date']).' - '.$cost_query['raw_cost']."<br>";
													$kk++;
												}

											}
											else
											{
												$previous_costx = 0;
												echo '-';
											}
											?>
											<?php if ($list[$product['product_id']]['rejected'] == 0) { ?>
											<?php $totalPrice += $previous_costx * $list[$product['product_id']]['qty']; ?>
											<?php $totalPriceR += $previous_costx * $list[$product['product_id']]['qty_received']; ?>
											<?php $totalItems += $list[$product['product_id']]['qty']; ?>
											<?php $totalItemsR += $list[$product['product_id']]['qty_received']; ?>
											<?php } ?>
										</td>



										<td align="center"><input style="width: 50px;" type="text" name="products[<?php echo $i?>][price]" value="<?php echo $list[$product['product_id']]['price']?>" /></td>
										<?php $cost_difference = (float)$previous_costx - (float)$list[$product['product_id']]['price']; ?>
										<td align="center"><span class="tag <?php echo ($cost_difference<0?'red-bg':($cost_difference>0?'green-bg':''));?>"><?php echo number_format($cost_difference,2);?></span></td>
									<?php endif;?>		

									<td align="center">
										<?php if ($list[$product['product_id']]['rejected'] == 0) { ?>
										<a href="javascript://" onclick="removeFromList(<?php echo $product['product_id'];?>);">X</a>
										<?php } ?>
									</td>
								</tr>
								<?php $i++;?>
							<?php endforeach; ?>

							<?php foreach($newlist as $new_item_id => $newItem):?>

								<tr id="row_<?php echo $new_item_id;?>">
									<td align="center"><?php echo $i; ?></td>
									<td align="center" >X</td>
									<td align="center">
										<?php $image = getItemImage($newItem['sku']);?>
										<a href="http://cdn.phonepartsusa.com/image/<?php echo $image;?>" class="fancybox2 fancybox.iframe">
											<img class="lazy" src="" data-original="http://cdn.phonepartsusa.com/image/<?php echo $image;?>" height="50" width="50" alt="" />
										</a>	
									</td>
									<td align="center">
										<?php echo $db->func_query_first_cell("SELECT quantity FROM oc_product WHERE sku='".$newItem['sku']."'");?>

									</td>

									<?php $name = getItemName($newItem['sku']);?>
									<td align="center" width="200px">
										<?php echo ($name) ? $name : $newItem['title']; ?>

										<br />
										<?php $issue_count = $db->func_query_first_cell("select count(id) from inv_product_issues where product_sku = '".$newItem['sku']."'")?>
										<a class="fancybox3 fancybox.iframe" href="popupfiles/product_issues.php?product_sku=<?php echo $newItem['sku'];?>"><?php echo $issue_count?> of item issues</a>
									</td>
									<td align="center">-</td>
									<td align="center">
										<?php if($newItem['sku']):?>
											<input type="text" name="new_products[<?php echo $i?>][model]" value="<?php echo $newItem['sku'];?>" />
										<?php else:?>
											<select name="new_products[<?php echo $i?>][sku_type]">
												<option value="">Select SKU Type</option>
												<?php foreach($product_skus as $product_sku):?>
													<option value="<?php echo $product_sku['sku'];?>"><?php echo $product_sku['sku'];?></option>
												<?php endforeach;?>
											</select>
										<?php endif;?>		
										<input type="hidden" name="new_products[<?php echo $i?>][title]" value="<?php echo $newItem['title'];?>" />	
									</td>

									<td align="center">
										<?php if($_SESSION['edit_pending_shipment']):?>
											<input required type="text" name="new_products[<?php echo $i?>][qty]" value="<?php echo $newItem['qty']?>" />
										<?php else:?>
											<?php echo $newItem['qty']?>
										<?php endif;?>			
									</td>

									<td align="center">
										<?php if($_SESSION['edit_received_shipment']):?>
											<input type="text" name="new_products[<?php echo $i?>][qty_received]" value="<?php echo $newItem['qty_received']?>" />
										<?php else:?>
											<?php echo $newItem['qty_received']?>
										<?php endif;?>		
										<input type="hidden" name="new_products[<?=$i;?>][weight]" value="<?=$newItem['weight'];?>">	
									</td>

									<?php if($_SESSION['display_cost']):?>

										<?php
										$cost_queries = $db->func_query("SELECT * FROM inv_product_costs WHERE sku='".$newItem['sku']."' ORDER BY id DESC LIMIT 3");

										?>
										<td align="center"> <?php

											if($cost_queries)
											{
												foreach($cost_queries as $ik => $cost_query)
												{
													if($ik==0) $previous_costx = $cost_query['raw_cost'];
													echo date('m/d/Y',strtotime($cost_query['cost_date'])).' - '.$cost_query['raw_cost']."<br>";
												}

											}
											else
											{
												echo '-';
											}
											?>
											<?php $totalPrice += $previous_costx * $newItem['qty']; ?>
											<?php $totalPriceR += $previous_costx * $newItem['qty_received']; ?>
											<?php $totalItems += $newItem['qty']; ?>
											<?php $totalItemsR += $newItem['qty_received']; ?>
										</td>



										<td align="center"><input type="text" name="new_products[<?php echo $i?>][price]" value="<?php echo $newItem['price']?>" /></td>
									<?php endif;?>		
									<td></td>
									<td align="center">
										<a href="javascript://" onclick="removeFromList(<?php echo $new_item_id;?> , '1');">X</a>
									</td>
								</tr>

								<?php $i++; ?>
							<?php endforeach;?>

							<tr>
								<td colspan="5" align="left">
									<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
								</td>

								<td colspan="7" align="right">
									<?php  echo $splitPage->display_links(10,$parameters); ?>
								</td>
							</tr>
						</tbody>   
					</table>   

					<br />

					<?php if($shipment_id AND $shipment_detail['status'] == 'Pending' && $_SESSION['edit_pending_shipment']):?>
						<div align="center" style="margin-right:10%;margin-top:5px;">
							<button name="IssueShipment" value="IssueShipment" onclick="return ValidateIt();">
								Save And Issue Shipment
							</button>
						</div>
					<?php endif;?>

					<?php if($shipment_id AND $shipment_detail['status'] == 'Issued' && $_SESSION['edit_received_shipment']):?>
						<div align="center" style="margin-right:10%;margin-top:5px;">
							<button type="submit" name="ReceiveIt" value="ReceiveIt" onclick="if(!confirm('Are you sure?')){ return false; }">
								Save And Receive It
							</button>
						</div>
					<?php endif;?>

					<br /><br />

				<?php endif;?>
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
				<table width="90%">
					<tr>
						<td>
							<table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
								<tr>
									<td>

										<b>Comment</b>
									</td>
									<td>
										<textarea rows="5" style="width: 100%;" name="comment" ></textarea>


									</td>
								</tr>

								<tr>
									<td colspan="2" align="center"> <input type="submit" class="button" name="addcomment" value="Add Comment" />	</td>
								</tr> 	   
							</table>
						</td>
						<td style="vertical-align:top">
							<h2>Comments History</h2>
							<table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse; vertical-align:top">
								<tr>
									<th>Date Added</th>
									<th>Comment</th>
									<th>Added By</th>
								</tr>
								<?php
								foreach($comments as $comment)
								{
									// if($shipment_detail['package_number'] === NULL )
									// {

									?>
									<tr>
										<td><?=americanDate($comment['date_added']);?></td>
										<td><?=$comment['comment'];?></td>
										<td><?=get_username($comment['user_id']);?></td>
									</tr>
									<?php
								
								// }
								}
								?>
							</table>
						</td>
					</tr>
					
				</table>
				
			</div>
		</form>
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
	<script>
		function ValidateIt()
		{
			var ex_rate = ($('input[name=ex_rate]').val());
			if(ex_rate=='' || isNaN(ex_rate) || ex_rate<=0)
			{
				alert('In Order to proceed, first provide the exchange rate');
				return false;
			}
			else if($('input[name=tracking_number]').val().length<=4 )
			{
				alert('Please provide valid tracking number to issue the shipment');
				return false;
			}
			else if($('select[name=carrier]').val()=='' )
			{
				alert('Please select valid Carrier to proceed');
				return false;
			}
			else if($('input[name=shipping_cost]').val()=='' || $('input[name=shipping_cost]').val()<'0.00'  )
			{
				alert('Please provide valid shipping cost.');
				return false;
			}
			else
			{
				if(!confirm('Are you sure'))
				{
					return false;
				}
				$('#frm').submit();
				return true;
			}
			
			
		}
	</script>	
</body>
</html>        