<?php
require_once("auth.php");
include_once 'inc/functions.php';

$title = 'Customer Purchase History';
$email = $db->func_escape_string($_GET['email']);
$_order_ids = $db->func_query('SELECT order_id FROM inv_orders WHERE email = "'.$email.'"') ;
$order_ids = array();
foreach($_order_ids as $o)
{
	$order_ids[] = $o['order_id'];
}
$order_ids = "'" . implode("','", $order_ids). "'";

if ($order_ids) {
		
	// $orders_skus = $db->func_query('SELECT DISTINCT product_sku FROM inv_orders_items WHERE order_id IN ('.$order_ids.')'); 
	$orders_skus = $db->func_query("SELECT DISTINCT b.product_sku FROM inv_orders_items b,inv_orders a WHERE a.order_id=b.order_id and trim(lower(a.email))='".trim(strtolower($email))."'"); 
} else{

	$_SESSION['message'] = "No Order History Found";
}



//print_r($r_ids);exit;
if (!$orders_skus)
{
	$_SESSION['message'] = 'No Order History Found';
   	
}



if(isset($_POST['create'])){
	//testObject($_POST['return_item']);exit;
	if(count($_POST['return_item']) > 0){ 
		foreach($_POST['return_item'] as $product_sku => $items){
			if ($_POST['data'][$product_sku]['qty']<=0) {
				$_SESSION['message'] = "Please enter quantity for $product_sku to return.";
				header("Location:select_return_items.php?action=filter_products&email=$email");
   				exit;
			}
		}

		//print "<pre>";
		///print_R($_POST); exit;

		$check_sales_agent = $db->func_query_first_cell("SELECT user_id from inv_customers where trim(email)='".trim($email)."'");
		if($check_sales_agent)
		{
			$sales_agent = $check_sales_agent;
		}
		else
		{
			$sales_agent = 0;
		}
		
		$returns = array();
		$returns['email'] = $email;
		//$returns['order_id']   = $db->func_escape_string($_POST['order_id']);
		$returns['date_added'] = date('Y-m-d H:i:s');
		$returns['store_type'] = 'web';
		$returns['source'] = 'manual';
		$returns['sales_user'] = $sales_agent;
		$returns['oc_user_id'] = $_SESSION['user_id'];
		$returns['rma_status'] = 'Awaiting';

		$rma_number = getRMANumber($returns['store_type']);
		$returns['rma_number'] = $rma_number;
		$return_id = $db->func_array2insert("inv_returns",$returns);
	
		//insert rma items
		foreach($_POST['return_item'] as $product_sku => $items){
			$qty = $_POST['data'][$product_sku]['qty'];

			for($i=0; $i<$qty;$i++){
				$return_items = array();
				$return_items['sku']   = $product_sku;
				$return_items['title'] = $_POST['data'][$product_sku]['title'];
				$return_items['price'] = (float)$_POST['data'][$product_sku]['price'];
				$return_items['quantity']    = 1;
				$return_items['return_code'] = $_POST['data'][$product_sku]['return_code'];
				$return_items['return_id'] = $return_id;
				$date = new DateTime($_POST['data'][$product_sku]['last_ordered_date']);
				$return_items['manual_amount_comment'] ='Last Purchased: '.$date->format('Y-m-d').' at $'.number_format($_POST['data'][$product_sku]['purchased_price'],2);
				$db->func_array2insert("inv_return_items",$return_items);
			}
		}
		
		    $addcomment = array();
			$addcomment['comment_date'] = date('Y-m-d H:i:s');
			$addcomment['user_id']   = $_SESSION['user_id'];
			$addcomment['comments']  = 'Manual RMA has been created.';
			$addcomment['return_id'] = $return_id;
			
			$db->func_array2insert("inv_return_comments",$addcomment);
			
		$_SESSION['message'] = "RMA # <a href=".$host_path."return_detail.php?rma_number=$rma_number>$rma_number</a> is generated successfully.";	
		//actionLog("RMA # " . linkToRma($rma_number) . " is generated.");
		
	}
	else{
		$_SESSION['message'] = "Please select at least 1 item to return.";	
	}
}
$return_ids = $db->func_query('SELECT id FROM inv_returns WHERE email = "'.$email.'"') ;
$r_ids = array();
foreach ($return_ids as $r) {
	$r_ids[] = $r['id'];
}
$r_ids = implode(",", $r_ids);
$reasons = $db->func_query("select title from inv_reasons","title");
if($reasons){
	$reasons = array_keys($reasons);
}
else{
	$reasons = array('R1. Do Not Return','R2. Change of Mind','R3. Non-Functional','R4. Item Not As Described','R5.Received Wrong Item','R6. Unknown');
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Customer Purchase History</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	<style type="text/css">
		.reqprc {display: none;}
	</style>	
	</head>
	<body>
		<div align="center"> 
			<?php include_once 'inc/header.php';?>
		</div>
		
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		
		<div align="center">
			<form method="post" enctype="multipart/form-data" >
				<h2><?php echo $title; ?><br /></h2><br>
					
					<button type="sumbit" class="button" name="create">Create RMA</button><br><br>
					<table width="85%" cellspacing="0" cellpadding="5px" border="1" align="center">
						<thead>
							<tr style="background-color:#e5e5e5;">
								<th width="5%"></th>
								<th width="10%">SKU</th>
								<th width="35%">Product Name</th>
								<th width="5%">RMA Qty</th>
								<th width="15%">Last Ordered</th>
								<?php
								if($_SESSION['display_cost'])
								{
									?>
									<th width="5%">Purchased Price</th>
									<th width="5%">Return Price</th>
									<?php
								}
								?>

								<th width="5%">Total Qty Ordered</th>
								<th width="5%">Total Qty Returned</th>
								<th width="10%">Reject Reason</th>
							</tr>
						</thead>
						<?php if ($orders_skus){ ?>
						<tbody>
						<?php
						 foreach ($orders_skus as $sku) { 
						 	
						 	$last_ordered = $db->func_query_first_cell('SELECT o.order_date from inv_orders o inner join inv_orders_items oi on (o.order_id = oi.order_id) where o.email = "'.$email.'"  AND oi.product_sku = "'.$sku['product_sku'].'" order by o.order_date desc');
								$days_ago = date('Y-m-d', strtotime('-14 days', strtotime(date('Y-m-d'))));


								$now = date('Y-m-d');
								$then = $last_ordered;
								$datetime1 = date_create($now);
    							$datetime2 = date_create($then);
								$difference = date_diff($datetime2, $datetime1);							

								$difference = $difference->format('%a');
								$difference = (int)$difference;

								// echo $difference."<br>";

							// $qty_ordered = $db->func_query_first_cell('SELECT SUM(product_qty) as qty from inv_orders_items WHERE order_id IN ('.$order_ids.') AND product_sku = "'.$sku['product_sku'].'"');
							$qty_ordered = $db->func_query_first_cell('SELECT SUM(b.product_qty) as qty from inv_orders_items b,inv_orders a WHERE a.order_id=b.order_id and trim(lower(a.email))="'.trim(strtolower($email)).'" AND b.product_sku = "'.$sku['product_sku'].'"');
						 	$product_price = $db->func_query_first_cell('SELECT MIN(product_unit) from inv_orders_items b,inv_orders a WHERE a.order_id=b.order_id and trim(lower(a.email))="'.trim(strtolower($email)).'" AND b.product_sku = "'.$sku['product_sku'].'" limit 1');
						 	//$purchased_price = $product_price;
						 	$purchased_price = $db->func_query_first_cell('SELECT (product_price/product_qty) as product_unit from inv_orders_items b,inv_orders a WHERE a.order_id=b.order_id and trim(lower(a.email))="'.trim(strtolower($email)).'" AND b.product_sku = "'.$sku['product_sku'].'" order by b.id desc limit 1');
						 	$product_price = $purchased_price;

						 	$current_price = $db->func_query_first('SELECT price,sale_price from oc_product WHERE sku = "'.$sku['product_sku'].'"');
						 	if ($difference>14){
							 	if ($current_price['sale_price']!=0.0000) {
							 		if ($current_price['sale_price'] < $product_price ) {
							 			//exit;
							 			$product_price = $current_price['sale_price'];
							 		}else if ($current_price['price'] < $product_price ) {
							 			$product_price = $current_price['price'];
							 		}
							 	} else{
							 		if ($current_price['price'] < $product_price ) {
							 			$product_price = $current_price['price'];
							 		}
							 		// $product_price = $purchased_price;
							 		// if(!$product_price)
							 		// {
							 		// 		$product_price = $current_price['price'];
							 		// }
							 	}
							 }
						 	if ($r_ids) {
						 		$qty_returned = $db->func_query_first_cell('SELECT SUM(quantity) as qty from inv_return_items WHERE return_id IN ('.$r_ids.') AND sku = "'.$sku['product_sku'].'"');
						 	} 
						 	if (!$qty_returned){
						 		$qty_returned = 0;
						 	}
						 	$remaining_allowed = $qty_ordered - $qty_returned;
						 	?>
							<tr>
								<td><input type="checkbox" id="return_item_<?php echo $sku['product_sku'];?>" name="return_item[<?php echo $sku['product_sku'];?>]">
								
								</td>
								<td><?php echo $sku['product_sku']; ?></td>
								<td> <?php echo getItemName($sku['product_sku']); ?></td>
								<td> <input style="width:30px" type="text" name="data[<?php echo $sku['product_sku'];?>][qty]" id="rma_qty_<?php echo $sku['product_sku']; ?>" value="0" data-sku="<?php echo $sku['product_sku']; ?>" onchange="checkThis(this)"></td>
								<td> <?php echo americanDate($last_ordered); ?></td>
								<?php
								if($_SESSION['display_cost'])
								{
									?>
									<td><?php echo '$'.number_format($purchased_price,2);?></td>
									<td><?php echo '$'.number_format($product_price,2);?></td>
									<?php
								}
								?>

								<td> <?php echo $qty_ordered; ?></td>
								<td> <?php echo $qty_returned; ?></td>
								<td>
							      	<select name="data[<?php echo $sku['product_sku'];?>][return_code]">
							      		<option value="">Select One</option>
							      		<?php foreach($reasons as $reason):?>
							      			<option value="<?php echo $reason; ?>"><?php echo $reason; ?></option>
							      		<?php endforeach;?>
							      	</select>
							   </td>
								<input type="hidden" id="validate_value_<?php echo $sku['product_sku']; ?>" value="<?php echo $remaining_allowed; ?>" >
								<input type="hidden" name="data[<?php echo $sku['product_sku'];?>][title]" value="<?php echo getItemName($sku['product_sku']); ?>">
								<input type="hidden" name="data[<?php echo $sku['product_sku'];?>][price]" value="<?php echo $product_price; ?>">
								<input type="hidden" name="data[<?php echo $sku['product_sku'];?>][purchased_price]" value="<?php echo $purchased_price; ?>">
								<input type="hidden" name="data[<?php echo $sku['product_sku'];?>][last_ordered_date]" value="<?php echo $last_ordered; ?>">
							</tr>
							<?php 
							 } ?>
						</tbody>
					</table> 
					<br><br>
					<?php }  ?>
						
				</form>
         </div>
         
     </body>

<script type="text/javascript">
         	function checkThis(obj){
         		var sku = $(obj).attr('data-sku');
         		var check = parseInt($('#validate_value_'+sku).val());
         		var current =  parseInt($('#rma_qty_'+sku).val());
         		//alert(current);return false;
         		if (current > check) {
         			alert('Error: More Items are being returned, than ordered. Check Customer details.');
         			$('#rma_qty_'+sku).val(0);
         			$('#return_item_'+sku).attr('checked', false);
         			return false; 
         		}
         	
         	}
         </script>