<?php
$is_popup = (isset($_GET['is_popup'])?1:0);
if($is_popup==0){
	include_once 'auth.php';
} else {
	include_once 'config.php';	
}
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';
if($is_popup==0)
	
{
	page_permission('order_history');
}
$order_users =  $db->func_query('SELECT u.* FROM inv_users u INNER JOIN inv_group_perm g on (u.group_id = g.group_id) WHERE g.perm_id = "25" AND u.status = "1" AND u.group_id <>"1" ');
if ($_POST['action'] == 'updateVerify') {
	$orders = $_POST['orders'];
	$customers = array_unique($_POST['customers']);
	foreach($orders as $order) {
		$db->func_query('UPDATE `inv_orders` set ss_valid = "1" WHERE order_id="'. $order .'"');
		$array['type'] = 'order';
		$array['user'] = $_SESSION['user_id'];
		$array['details'] = $order;
		$array['date_added'] = date('Y-m-d H:i:s');
		$db->func_array2insert("inv_whitelist_history",$array);
		unset($array);
	}
	if ($customers) {
		foreach($customers as $customer) {
			$db->func_query('UPDATE `inv_customers` set white_list = "1" WHERE email="'. $customer .'"');
			$array['type'] = 'customer';
			$array['user'] = $_SESSION['user_id'];
			$array['details'] = $customer;
			$array['date_added'] = date('Y-m-d H:i:s');
			$db->func_array2insert("inv_whitelist_history", $array);
			unset($array);
		}
	}
	echo json_encode(array('success' => 1));
	exit;
}
if(isset($_REQUEST['submit'])){
	$inv_query   = '';
	$orderType   = $_REQUEST['ordertype'];
	if($orderType == "Completed" || $orderType == "On Hold" || $orderType == "All" || $orderType == "Processed" || $orderType=="Unpaid" || $orderType=="Unpaid2" || $orderType=="Unpaid3" || $orderType=="Replacement"){
		$conditions = array();
		$start_date = $db->func_escape_string($_REQUEST['start_date']);
		$end_date   = $db->func_escape_string($_REQUEST['end_date']);
		$filterBy   = $db->func_escape_string($_REQUEST['order']);
		$order_number = $db->func_escape_string(trim($_REQUEST['order_number']));
		$order_user = $db->func_escape_string($_REQUEST['order_user']);
		$email = strtolower($db->func_escape_string(trim($_REQUEST['email'])));
		$n_ss = $_REQUEST['n_ss'];
		if(@$start_date){
			$conditions[] =  " DATE(order_date) >= '$start_date' ";
		}
		if($orderType == 'On Hold'){
			$conditions[] =  " o.order_status = 'On Hold' ";
		}
		if($orderType == 'Processed'){
			$conditions[] =  " o.order_status = 'Processed' ";
		}
		if($orderType == 'Unpaid'){
			$conditions[] =  "  ((
			LOWER( d.payment_method ) LIKE  '%cash%'
			and lower(o.order_status) not in ('shipped','canceled')
			AND (
			payment_source =  'Unpaid'
			)
			)
			AND (
			o.is_manual =0
			) )   ";
		}
		if($orderType == 'Unpaid2'){
			$conditions[] =  " (o.is_manual =1 and o.paid_price=0.00 and d.payment_method<>'Replacement' and lower(o.order_status) not in ('canceled') and o.order_price>0.00 ) ";
		}
		if($orderType == 'Unpaid3'){
			$conditions[] =  " ( o.paid_price=0.00 and d.payment_method='Cash on Delivery' and lower(o.order_status) not in ('canceled') and o.order_price>0.00 ) ";
		}
		if($orderType == 'Replacement'){
			$conditions[] =  " (d.payment_method ='Replacement') ";
		}
        if($order_user){
			$conditions[] = " o.order_user = '".$order_user."' ";
		}

		if(@$end_date){
			$conditions[] =  " DATE(order_date) <= '$end_date' ";
		}
		if(@$filterBy !='all'){
			$conditions[] =  " store_type = '$filterBy' ";
		}
		if(@$email){
			$conditions[] =  " LOWER(email) LIKE '%$email%' ";
		}
		if($n_ss){
			$conditions[] =  " `shipstation_added` IS NULL AND ss_valid = '0' ";
		}
		if($order_number){
			$condition_sql = " concat(o.prefix,o.order_id) LIKE '%$order_number%'  ";
		}
		else{
			$condition_sql = implode(" AND " , $conditions);
		}
		if(!$condition_sql){
			$condition_sql = ' 1 = 1';
		}
		$inv_query = "Select o.whitelist,o.is_manual,o.paid_price,o.prefix, o.ppusa_sync, o.order_id,d.payment_method, d.shipping_method , o.order_date , o.email, o.order_price , o.store_type, o.order_status, o.fishbowl_uploaded,o.customer_name,
		o.match_status,o.bscheck, o.is_address_verified, o.avs_code,o.payment_source,d.address1,d.bill_address1,d.zip,d.bill_zip,o.transaction_fee,o.shipstation_added,o.ss_valid,(SELECT a.transaction_fee FROM inv_transactions a WHERE o.order_id=a.order_id) as transaction_fee
		from inv_orders o,inv_orders_details d where o.order_id=d.order_id  and $condition_sql  order by order_date DESC";
//echo $inv_query;
	}
	elseif($orderType == "Return"){
		$conditions = array();
		$start_date = $db->func_escape_string($_REQUEST['start_date']);
		$end_date   = $db->func_escape_string($_REQUEST['end_date']);
		$filterBy   = $db->func_escape_string($_REQUEST['order']);
		$order_user = $db->func_escape_string($_REQUEST['order_user']);
		$order_number = $db->func_escape_string($_REQUEST['order_number']);
		if(@$start_date){
			$conditions[] =  " order_date >= '$start_date' ";
		}
		if(@$end_date){
			$conditions[] =  " order_date <= '$end_date' ";
		}
		if(@$filterBy !='all'){
			$conditions[] =  " store_type = '$filterBy' ";
		}
		if($order_user){
			$conditions[] = " o.order_user = '".$order_user."' ";
		}
		$condition_sql = implode(" AND " , $conditions);
		if(!$condition_sql){
			$condition_sql = ' 1 = 1';
		}
		$inv_query = "Select o.* , od.* from inv_return_orders o inner join inv_orders_details od on o.order_id  = od.order_id where $condition_sql order by order_date DESC";
	}
} else {
	$inv_query = "Select o.whitelist,o.prefix, o.ppusa_sync, o.order_id, o.ss_valid, o.order_date , o.email, o.order_price , o.store_type, o.order_status, o.fishbowl_uploaded,o.customer_name,
	o.match_status,o.bscheck, o.is_address_verified, o.avs_code,o.payment_source,o.transaction_fee
	from inv_orders o  order by order_date DESC";
}
$on_hold_orders = $cache->get('orders.on_hold_orders');
if(!$on_hold_orders)
{
$on_hold_orders = $db->func_query_first_cell("SELECT count(*) from inv_orders where LOWER(order_status)='on hold'");
$cache->set('orders.on_hold_orders',$on_hold_orders);	
}

$unmapped_orders = $cache->get('orders.unmapped_orders');
if(!$unmapped_orders)
{
$unmapped_orders = $db->func_query_first_cell("SELECT count(*) FROM inv_orders o,inv_orders_details d WHERE o.order_id=d.order_id and ((
	LOWER( d.payment_method ) LIKE  '%cash%'
	and lower(o.order_status) not in ('shipped','canceled')
	AND (
	payment_source =  'Unpaid'
	)
)
AND (
o.is_manual =0
) ) ");
$unmapped_orders = (int)$unmapped_orders + $db->func_query_first_cell("SELECT count(*) FROM inv_orders o,inv_orders_details d WHERE o.order_id=d.order_id and (
	o.is_manual =1
	AND o.paid_price = 0.00
	AND d.payment_method <>  'Replacement'
	AND LOWER( o.order_status ) NOT 
	IN (
	'canceled'
	)
	) ");
$cache->set('orders.unmapped_orders',$unmapped_orders);

}
if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}
if($page < 1){
	$page = 1;
}
$max_page_links = 10;
$limit = $_REQUEST['limit'];
if($limit)
{
	$num_rows = $limit;
}
else
{
	$num_rows = 50;	
}
if($is_popup)
{
	$num_rows = 1000;	
}
$start = ($page - 1)*$num_rows;
$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "order.php",$page);

$_cache = md5(http_build_query($_GET));
// $inv_orders = $cache->get('orders_new.'.$page.'.'.$_cache);
// if(!$inv_orders)
// {
$inv_orders = $db->func_query($splitPage->sql_query);
// $cache->set('orders_new.'.$page.'.'.$_cache,$inv_orders);
// }
$lastupdate = $db->func_query_first_cell("SELECT config_value FROM configuration WHERE config_key = 'WEB_LAST_CRON_TIME'");
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link rel="stylesheet" href="include/jquery-ui.css">
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery-ui.js"></script>
	<title>Orders Panel</title>
</head>
<body>
	<div <?php if($is_popup) echo 'style="display:none"'; ?>> <?php include_once 'inc/header.php';?></div>
	<?php if(@$_SESSION['message']):?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
	<?php endif;?>
	<br />
	<div align="center" <?php if($is_popup) echo 'style="display:none"'; ?>>
		<a href="Bigcommerce/fetch_orders.php?m=1">Import Bigcommerce Orders</a>
		|
		<!-- <a href="CA/ca_orders.php?m=1">Import CA Orders</a> -->
		<!-- | -->
		<?php
		if ($_SESSION['login_as'] == 'admin'){ ?>
		
		<a href="web/index.php?m=1">Import Web Orders</a>
		|
		<?php
	}
	?>
	<a href="inv_sync.php?m=1">Sync Qty</a>
	|
	<a href="ignore.php?m=1">Ignore Unmapped</a>
	|
	<a onclick="$('form[name=orders]').attr({action: 'exportProductsSS.php', target: '_blank'}).submit().removeAttr('target').attr('action', '');" href="javascript:void(0);">Export Selected For Shipstation</a> 
	
</div>
<br>
<br />
<h2 align="center" style="font-size:15px">Order Details</h2>
<div align="center" <?php if($is_popup) echo 'style="display:none"'; ?>><strong>Show Orders: </strong> <a href="order.php?<?php if($_REQUEST['order']) { ?>order=<?php echo $_REQUEST['order'];?>&ordertype=<?php echo $_REQUEST['ordertype'];?>&order_number=<?php echo $_REQUEST['order_number'];?>&email=<?php echo $_REQUEST['email'];?>&start_date<?php echo $_REQUEST['start_date'];?>&end_date<?php echo $_REQUEST['end_date'];?>&submit=Search&<?php } ?>limit=50<?php if($_REQUEST['page']) { ?>&page=<?php echo $_REQUEST['page'];?> <?php } ?>">50</a> | 
	<a href="order.php?<?php if($_REQUEST['order']) { ?>order=<?php echo $_REQUEST['order'];?>&ordertype=<?php echo $_REQUEST['ordertype'];?>&order_number=<?php echo $_REQUEST['order_number'];?>&email=<?php echo $_REQUEST['email'];?>&start_date<?php echo $_REQUEST['start_date'];?>&end_date<?php echo $_REQUEST['end_date'];?>&submit=Search&<?php } ?>limit=100<?php if($_REQUEST['page']) { ?>&page=<?php echo $_REQUEST['page'];?> <?php } ?>">100</a> |
	<a href="order.php?<?php if($_REQUEST['order']) { ?>order=<?php echo $_REQUEST['order'];?>&ordertype=<?php echo $_REQUEST['ordertype'];?>&order_number=<?php echo $_REQUEST['order_number'];?>&email=<?php echo $_REQUEST['email'];?>&start_date<?php echo $_REQUEST['start_date'];?>&end_date<?php echo $_REQUEST['end_date'];?>&submit=Search&<?php } ?>limit=250<?php if($_REQUEST['page']) { ?>&page=<?php echo $_REQUEST['page'];?> <?php } ?>">250</a> |
	<a href="order.php?<?php if($_REQUEST['order']) { ?>order=<?php echo $_REQUEST['order'];?>&ordertype=<?php echo $_REQUEST['ordertype'];?>&order_number=<?php echo $_REQUEST['order_number'];?>&email=<?php echo $_REQUEST['email'];?>&start_date<?php echo $_REQUEST['start_date'];?>&end_date<?php echo $_REQUEST['end_date'];?>&submit=Search&<?php } ?>limit=500<?php if($_REQUEST['page']) { ?>&page=<?php echo $_REQUEST['page'];?> <?php } ?>">500</a> |
	<a href="order.php?<?php if($_REQUEST['order']) { ?>order=<?php echo $_REQUEST['order'];?>&ordertype=<?php echo $_REQUEST['ordertype'];?>&order_number=<?php echo $_REQUEST['order_number'];?>&email=<?php echo $_REQUEST['email'];?>&start_date<?php echo $_REQUEST['start_date'];?>&end_date<?php echo $_REQUEST['end_date'];?>&submit=Search&<?php } ?>limit=1000<?php if($_REQUEST['page']) { ?>&page=<?php echo $_REQUEST['page'];?> <?php } ?>">1000</a>
	<p>Last Import: <?php echo americanDate($lastupdate) ?> / Next Import: <?php echo americanDate(date('Y-m-d, H:i:s', mktime(date('H', strtotime($lastupdate)), date('i', strtotime($lastupdate))+10, date('s', strtotime($lastupdate)), date('m', strtotime($lastupdate)), date('d', strtotime($lastupdate)), date('Y', strtotime($lastupdate))))); ?></p>
	<p> <strong><?=$on_hold_orders ;?> On-Hold Orders</strong> | <strong><?=$unmapped_orders;?> Unmapped Orders</strong>
	</div>
	<table width="90%" cellpadding="10" style="border: 0px solid #585858; "  align="center">
		<tbody>
			<form name="order" action="" method="get">
				<tr <?php if($is_popup) echo 'style="display:none"'; ?> >
					<td>
						<label for="order">Filter By Store Type :</label>
						<select id="order" name="order" style="width: 145px;">
							<option value="all">All</option>
							<option value="ebay" <?php if($_REQUEST['order']=='ebay'):?> selected='selected' <?php endif;?>>eBay</option>
							<option value="amazon" <?php if($_REQUEST['order']=='amazon'):?> selected='selected' <?php endif;?>>Amazon</option>
							<option value="amazon_fba" <?php if ($_REQUEST['order'] == 'amazon_fba'): ?> selected='selected' <?php endif; ?>>Amazon FBA</option>
							<option value="amazon_ca" <?php if ($_REQUEST['order'] == 'amazon_ca'): ?> selected='selected' <?php endif; ?>>Amazon.Ca</option>
							<option value="amazon_mx" <?php if ($_REQUEST['order'] == 'amazon_mx'): ?> selected='selected' <?php endif; ?>>Amazon MX</option>
							<option value="amazon_pg" <?php if ($_REQUEST['order'] == 'amazon_pg'): ?> selected='selected' <?php endif; ?>>Amazon PG</option>
							<option value="amazon_pgca" <?php if ($_REQUEST['order'] == 'amazon_pgca'): ?> selected='selected' <?php endif; ?>>Amazon PGCA</option>
							<option value="amazon_pgmx" <?php if ($_REQUEST['order'] == 'amazon_pgmx'): ?> selected='selected' <?php endif; ?>>Amazon PGMX</option>
							<option value="web" <?php if($_REQUEST['order']=='web'):?> selected='selected' <?php endif;?>>PPUSA</option>
							<!-- <option value="channel_advisor" <?php if($_REQUEST['order']=='channel_advisor'):?> selected='selected' <?php endif;?>>Channel Advisor</option> -->
							<option value="bigcommerce" <?php if($_REQUEST['order']=='bigcommerce'):?> selected='selected' <?php endif;?>>RLCDs</option>
							<option value="wish" <?php if($_REQUEST['order']=='wish'):?> selected='selected' <?php endif;?>>Wish</option>
							<!-- <option value="bonanza" <?php if($_REQUEST['order']=='bonanza'):?> selected='selected' <?php endif;?>>Bonanza</option> -->
							<option value="po_business" <?php if($_REQUEST['order']=='po_business'):?> selected='selected' <?php endif;?>>Po Business</option>
							<option value="newegg" <?php if($_REQUEST['order']=='newegg'):?> selected='selected' <?php endif;?>>Newegg</option>
							<option value="rakuten" <?php if($_REQUEST['order']=='rakuten'):?> selected='selected' <?php endif;?>>Rakuten</option>
							<option value="newsears" <?php if($_REQUEST['order']=='newsears'):?> selected='selected' <?php endif;?>>NewSears</option>
							<option value="opensky" <?php if($_REQUEST['order']=='opensky'):?> selected='selected' <?php endif;?>>OpenSky</option>
						</select>
					</td>
					<td>
						<label for="order">Order Type :</label>
						<select id="ordertype" name="ordertype" style="width: 130px;">
							<option value="All" <?php if($_REQUEST['ordertype']=='All'):?> selected='selected' <?php endif;?>>All</option>
							<option value="Completed" <?php if($_REQUEST['ordertype']=='Completed'):?> selected='selected' <?php endif;?>>Completed/Shipped</option>
							<option value="On Hold" <?php if($_REQUEST['ordertype']=='On Hold'):?> selected='selected' <?php endif;?>>On Hold</option>

							<option value="Processed" <?php if($_REQUEST['ordertype']=='Processed'):?> selected='selected' <?php endif;?>>Processed</option>
							
							<option value="Return" <?php if($_REQUEST['ordertype']=='Return'):?> selected='selected' <?php endif;?>>Return/Refund</option>
							<option value="Replacement" <?php if($_REQUEST['ordertype']=='Replacement'):?> selected='selected' <?php endif;?>>Replacement</option>
							<option value="Unpaid" <?php if($_REQUEST['ordertype']=='Unpaid'):?> selected='selected' <?php endif;?>>Unpaid - POS</option>
							<option value="Unpaid2" <?php if($_REQUEST['ordertype']=='Unpaid2'):?> selected='selected' <?php endif;?>>Unpaid - IMP</option>
							<option value="Unpaid3" <?php if($_REQUEST['ordertype']=='Unpaid3'):?> selected='selected' <?php endif;?>>Unpaid - COD</option>
						</select>
					</td>
					<td>
						<label for="start_date">Order Number:</label>
						<input type="text" name="order_number" value="<?php echo @$_REQUEST['order_number'];?>" />
					</td>
					<td>
						<label for="start_date">Email:</label>
						<input type="text" name="email" value="<?php echo @$_REQUEST['email'];?>" />
					</td>
					<td>
						<label for="order_user">Created By:</label>
						<select id="order_user" name="order_user">
						<option value="">Select</option>
						<?php foreach ($order_users as $user) { ?>
							<option value="<?php echo $user['id']; ?>" <?php if($_REQUEST['order_user']==$user['id']):?> selected='selected' <?php endif;?>><?php echo $user['name']; ?></option>
						<?php } ?>
							
						</select>
					</td>
					<td>
						<label for="start_date">Start Date:</label>
						<input type="text" class="datepicker" value="<?php echo @$_REQUEST['start_date'];?>" name="start_date" size="20" style="width: 110px;" readonly="readonly" />
					</td>
					<td>
						<label for="end_date" style="margin-left: 30px;" valign="top">End Date:</label> 
						<input type="text" class="datepicker" value="<?php echo @$_REQUEST['end_date'];?>" name="end_date" size="20" style="width: 110px;" readonly="readonly" />
					</td>
						<!-- <td style="width: 93px;">
							<label for="n_ss" valign="top">Not Updated SS:</label> 
							<input type="checkbox" <?= ($_GET['n_ss'])? 'checked=""':'';?> name="n_ss" value="1" id="n_ss" />
						</td> -->
						<td><input type="submit" value="Search" name="submit" style="margin: 10px 0 0 10px"></td>
						<?php if ($_SESSION['order_reasons']) { ?>
						<td>
							<a id="remove_btn_selected" class="fancyboxX3 fancybox.iframe button" href="order_settings.php" >Setting</a>
						</td>
						<?php } ?>
						<?php if ($_SESSION['order_report']) { ?>
						<td>
							<a class="fancyboxX3 fancybox.iframe button" href="orders_cancel_report.php" >Report</a>
						</td>
						<!-- <td>
						<a class="fancyboxX3 fancybox.iframe button" href="#" >CSV</a>
					</td> -->
					<?php } ?>
				</tr>
			</form>
			<tr>
				<?php if($inv_orders):
				?>
				<td colspan="11">
					<form name="orders" action="" method="post">
						<table id="table1" width="100%" cellspacing="0" cellpadding="5px" border="1" align="center">
							<thead>
								<tr style="background-color:#e5e5e5;">
									<th><input type="checkbox" title="Select All" onchange="if ($(this).prop('checked')) {$('input[name=orderIds]').prop('checked', true);} else {$('input[name=orderIds]').prop('checked', false);}" /></th>
									<th>SN</th>
									<th>Order ID</th>
									<th>Order Date</th>
									<th>Email</th>
									<th>Customer</th>
									<?php if($_SESSION['display_order_price'] || $_SESSION['login_as'] == 'admin'){ ?>
									<th>Order Price</th>
									<?php } ?>
									<?php if ($_SESSION['login_as'] == 'admin'){?>
									<th>Profit</th>
									<?php
								}
								?>
								<th>Store Type</th>
								<th>PPUSA</th>
								<th>Order Status</th>
								<th>Payment</th>
								<th>FB Added</th>
								<th>VERF</th>
							</tr>
						</thead>
						<?php $i = $splitPage->display_i_count();
						foreach($inv_orders as $order):?>
						<?php //$order_discount = $db->func_query_first_cell('SELECT SUM(promotion_discount) FROM inv_orders_items WHERE order_id = "'. $order['order_id'] .'"'); ?>
						<?php $order['order_price'] = $order['order_price'] ; ?>
						<?php
						$order['store_url'] = $db->func_query_first_cell("SELECT store_url FROM oc_order WHERE order_id='".$order['order_id']."' ");
						
						$order['transaction_fee'] = $db->func_query_first_cell("SELECT transaction_fee FROM inv_transactions WHERE order_id='".$order['order_id']."' ");
						
						
						?>
						<?php
							// Code snippet not to show estimate orders for finance report
						if($order['store_type']=='po_business' && $is_popup)
						{
							if(strtolower($order['order_status'])=='estimate') continue;
						}
							// end code snippet
						?>
						<?php
						
						$order_fee = $db->func_query_first_cell("SELECT SUM(fee) as fee from inv_order_fees where order_id = '".$order['order_id']."' ");
						
						
						$order_true_cost = 0.00;
						

						$_order_items = $db->func_query("SELECT product_sku,product_qty,product_true_cost,promotion_discount,product_price FROM inv_orders_items WHERE order_id='".$order['order_id']."'");
						
						$sub_total = 0.00;
						$order_discount = 0.00;
						foreach($_order_items as $_item)
						{
							$order_true_cost+=($_item['product_true_cost'] * $_item['product_qty']);
							if($order['payment_method']=='Replacement')
							{
								$promotion_discount = $_item['product_price'];
								$order_discount += $_item['product_price'];

							}
							$sub_total+=($_item['product_price']-$promotion_discount);
						}
						
						$temp_shipping_cost = $db->func_query_first_cell("SELECT shipping_cost FROM inv_orders_details WHERE order_id='".$order['order_id']."'");
						
						$sub_total = $sub_total + $temp_shipping_cost;
							//$order['order_price'] = $sub_total;
						if($order['payment_method']=='Replacement')
						{
							$order['order_price'] = 0.00;
						}

						

						$order_shipments = $db->func_query_first("select * from inv_shipstation_transactions where order_id = '".$order['order_id']."' ORDER BY voided DESC");



						$_shipping_cost = 0.00;	
						if(isset($order_shipments['voided']) and $order_shipments['voided']==0){
							$_shipping_cost = $order_shipments['shipping_cost']+$order_shipments['insurance_cost'];
						}
						?>
						<?php $order_type = $_REQUEST['ordertype'];?>
						<?php
						$po_order_total = 0.00;
						if($order['store_type']=='po_business')
						{
							

							$po_order_total = $db->func_query_first_cell("SELECT SUM(product_price) from inv_orders_items WHERE order_id='".$order['order_id']."'");
							
							$shipping_cost = $db->func_query_first_cell("SELECT shipping_cost FROM inv_orders_details WHERE order_id='".$order_id."'");

							$po_order_total = $po_order_total+$shipping_cost;
						}
						?>
						<?php
							// If cash order and status shipped, it should show Paid instead of Unpaid
							//var_dump(strpos($order['payment_method'], 'Cash'));
							//echo $order['payment_method'];
						if(strpos(strtolower($order['payment_method']), 'cash') !== false && strtolower($order['order_status'])=='shipped' )
						{
							$order['payment_source']='Paid';
						}
						else
						{
							$order['payment_source'] = $order['payment_source'];
							if($order['payment_source']=='Unpaid')
							{
								if(strpos(strtolower($order['payment_method']), 'cash') !== false)
								{
									$order['payment_source'] = 'Unpaid - POS';		
								}		

							}

							elseif($order['payment_source']=='' and $order['is_manual']=='1' )
							{
// 								if(strtolower($order['order_status'])=='shipped')
// 								{
// $order['payment_source'] = 'Paid - IMP';
// 								}
// 								else
// 								{
// 								$order['payment_source'] = 'Unpaid - IMP';	
// 								}
								if($order['paid_price']=='0.00')
								{
									$order['payment_source'] = 'Unpaid - IMP';
								}
								else
								{
									$order['paid_price'] = 'Paid - IMP';
								}

							}
						}
						if ($order['payment_method'] == 'Cash On Delivery') {
							if($order['paid_price']=='0.00')
								{
									$order['payment_source'] = 'Unpaid - COD';
								}
								else
								{
									$order['payment_source'] = 'Paid - COD';
								}
							
						}
						?>
						<tr class="list_items" id="<?php echo $order['order_id'];?>" <?php if($order['payment_source']=='Unpaid - POS' || $order['payment_source']=='Unpaid - IMP' ){ echo ' style="background-color:#FFFBCC;"'; }elseif($order['shipstation_added']==1 or $order['ss_valid']==1) { echo' style="background-color:#C4E1FF"';}?>>
							<!--                         <td><a href="javascript:void(0);" onclick="updateSSV('<?= $order['order_id']; ?>', this);">Verf</a></td> -->
							<td align="center"><input type="checkbox" class="checkboxes" name="orderIds[]" title="<?php echo $order['order_id'] . '-' . substr($order['customer_name'], 0, 1);?>" value="<?php echo $order['order_id'] . '-' . substr($order['customer_name'], 0, 1);?>"/></td>
							<td align="center"><?php echo $i; ?></td>
							<td align="center" class="order_id">
								<a href="viewOrderDetail.php?order=<?php echo $order['order_id']?>"><?php echo @$order['prefix'].$order['order_id'];?></a>
							</td>
							<td align="center"><?php echo americanDate($order['order_date']);?></td>
							<td align="center" style="word-wrap:break-word;width:160px;float:left;border-width:0 0 1px 0;"><?php echo linkToProfile($order['email']);?></td>
							<td align="center"><?php echo @$order['customer_name'];?></td>
							<?php if($_SESSION['display_order_price'] || $_SESSION['login_as'] == 'admin'){ ?>
							<td align="center">$<?php echo ($order['store_type']=='po_business'?$po_order_total:$order['order_price']);?></td>
							<?php } ?>
							<?php if ($_SESSION['login_as'] == 'admin'){?>
							<?php
							$_order_price = ($order['store_type']=='po_business'?$po_order_total:$order['order_price']);
								//if($_order_price<=0)
								//{
							?>
							<!--	<td align="center" style="color:green">$0.00</td>-->
							<?php 
							//	}
								//else
							//	{
							?>
							<?php $order_profit =  ($order['store_type']=='po_business'?((float)$po_order_total-$order_true_cost-$order['transaction_fee']+$order_fee):((float)$order['order_price']-$order_true_cost-$order['transaction_fee']-$_shipping_cost+$order_fee));?>
							<td align="center" style="color:<?=($order_profit>=0?'green':'red');?>">$<?=number_format($order_profit,2);?></td>
							<?php
								//}
						}
						?>
						<?php  
								if (strpos($order['store_url'],'old') == true) { ?>

						<td align="center" style="color: red;"><?php echo @mapStoreType($order['store_type']);?>
						<?php	}else { ?>
									<td align="center"  style="color: green;"><?php echo @mapStoreType($order['store_type']);?>
								<?php	}
								 ?>



							<?php if ($order['store_url'] && strpos($order['payment_source'],'IMP') == false) { 
								if (strpos($order['store_url'],'old') == true) { ?>
									<strong>(1.0)</strong>	
								<?php	}else { ?>
									<strong>(2.0)</strong>
								<?php	}
								} ?>
						</td>
						<td align="center"><?php echo ($order['ppusa_sync'])? 'YES': 'N/A';?></td>
						<?php
						if ($order['whitelist']!='') {
						 	$whitelistArr = unserialize($order['whitelist']);
						 } else {
							$whitelistArr = whiteList($order, 0, 1);
							$whiteListSerialize = serialize($whitelistArr);
							$db->db_exec("UPDATE inv_orders SET whitelist='".$whiteListSerialize."' WHERE order_id='".$order['order_id']."'");

						 }
						?>
						<?php if($order_type == "Return") {?>
							<td align="center">Return/Refund</td>
						<?php } else { ?>
						<?php if ($whitelistArr[0]=="Black List Customer" && $order['order_status'] != "On Hold") {
								$db->db_exec("UPDATE inv_orders SET order_status='On Hold' WHERE order_id='".$order['order_id']."'");
								$db->db_exec("UPDATE oc_order SET order_status_id='21' WHERE cast(`order_id` as char(50))='".$order['order_id']."' OR ref_order_id='".$order['order_id']."'"); }
							 ?>
							<td align="center"><?php echo @$order['order_status'];?></td>
						<?php } ?>

						<td align="center"><?php echo @$order['payment_source'];?></td>
						<td align="center"><?php echo (@$order['fishbowl_uploaded']) ? 'Yes' : 'No';?></td>
						<td class="ver" align="center">
						 
							<?php $whitelist = $whitelistArr['check']; ?>
							<?php unset($whitelistArr['check']); ?>
							<?php foreach ($whitelistArr as $key => $value) { ?>
							<?php if (strpos($value, 'Not') === false && strpos($value, 'Black') === false && strpos($value, 'Pending') === false && strpos($value, 'Above $30') === false && strpos($value, 'Un-Matched') === false) {
								echo "<a class='smallTooltip' data-tooltip='". $value ."'><img src='images/check.png' alt='Match' /></a>" ;
							} else if (strpos($value, 'Pending') != false) {
								echo "<a class='smallTooltip' data-tooltip='". $value ."'><img src='images/circle.png' alt='No Match' /></a> ";	
							}else if (strpos($value, 'Above $30') != false) {
								echo "<a class='smallTooltip' data-tooltip='". $value ."'><img src='images/gray_dash.png' alt='Above $30' /></a> ";	
							}


							 else {
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
							<?php
						/*
						if($order['address1']==$order['bill_address1'] and $order['zip']==$order['bill_zip'])
						{
							echo "<img src='images/check.png' alt='Match' />,";	
						}
						else
						{
							if($order['store_type']=='amazon')
							{
								echo "<img src='images/check.png' alt='Match' />";	
							}
							else
							{
								echo "<img src='images/cross.png' alt='No Match' />,";	
							}
						}
						?>
						<?php
						if($order['store_type']!='amazon')
						{
							echo getMatchStatus($order['match_status'] , $order['payment_source'] , $order['avs_code'] , $order['is_address_verified']);
						}
						*/
						?>
					</td>
				</tr>
				<?php $i++; endforeach; ?>
				<script type="text/javascript">
					function verifySelected () {
						var orders = [];
						var customers = [];
						$('.order_checkboxes').each(function(index, element) {
							if($(this).is(":checked"))
							{
								orders.push($(this).val());	
								customers.push($(this).attr('data-email'));
							}
						});
						if(orders.length==0)
						{
							alert('You must selected atleast 1 order to process');
							return false;
						}
						if (!confirm('Do you want to update customers to White list?')) {
							customers = [];
						}
						$.ajax({
							url: 'order.php?is_popup=1',
							type: 'POST',
							dataType: 'json',
							data: {'orders': orders, 'customers': customers, 'action': 'updateVerify'},
							success: function(json){
								if (json['success']) {
									alert('Successfully verified the orders');
									window.location.reload();
											// for (var i = orders.length - 1; i >= 0; i--) {
											// 	$('#' + orders[i]).css('background-color', '#C4E1FF');
											// }
										}
									}
								});
					}
					function updateSSVOld (oId, t) {
						$.ajax({
							url: 'order.php?is_popup=1',
							type: 'POST',
							dataType: 'json',
							data: {'order': oId, 'action': 'updateSSV'},
							success: function(json){
								$('input[type="submit"]').removeAttr('disabled');
								if (json['success']) {
									$(t).parent().parent().find('td.ver').css('background-color', 'lightgreen');
								}
							}
						});
					}
				</script>
			</table>
		</form>
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
	<td colspan=4><label style="color: red; margin-left: 600px;">Order Doesn't Exist</label></td>
<?php endif;?>
</tr>
</tbody>
</table>
<script type="text/javascript">
	function traverseCheckboxes()
	{
		var Val = '';
		$('.checkboxes').each(function (index, element) {
			$(element).parent().parent().removeClass('highlight');
			if ($(element).parent().parent().hasClass('highlightx') == true) {
				$(this).prop('checked', true);
			} else {
				$(this).prop('checked', false);
			}
			if ($(element).is(":checked"))
			{
				Val += $(element).val() + ',';
				$(element).parent().parent().addClass('highlight');

			}
		});
		$('#selected_items').val(Val);
	}
	$(function () {

		$('#table1').multiSelect({
			actcls: 'highlightx',
			selector: 'tbody .list_items',
			except: ['tbody'],
			callback: function (items) {
					// items.find('.checkboxes').prop('checked', true);
					traverseCheckboxes();
				}
			});
	})
</script>
<script type="text/javascript" src="js/multiselect.js"></script>
</body>
</body>
</html>