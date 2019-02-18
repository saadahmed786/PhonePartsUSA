<?php
require_once("config.php");
include_once 'inc/functions.php';
$success = false;
if(isset($_GET['popup']))
{
	$popup_param = 'popup=1';
}
if(isset($_REQUEST['check'])){
	$order_id = $db->func_escape_string(trim($_POST['order_id']));
	$email = strtolower($db->func_escape_string(trim($_REQUEST['email'])));
	$firstname = strtolower($db->func_escape_string(trim($_POST['firstname'])));
	$lastname  = strtolower($db->func_escape_string(trim($_POST['lastname'])));
	$city  = $db->func_escape_string(trim($_POST['city']));
	$zip   = $db->func_escape_string(trim($_POST['zip']));
	$phone = $db->func_escape_string(trim($_POST['phone']));
	$sku   = $db->func_escape_string(trim($_POST['sku']));
	
	$where = array();
	$where2 = '';
	if($order_id){
		$where['order_id'] = " LOWER(concat(o.prefix,o.order_id)) LIKE LOWER('%$order_id%') ";
	}
	if($email){
		$where['email'] = " LOWER(email) like '%$email%' ";
	}
	if($firstname){
		$where['first_name'] = " LOWER(first_name) like '%$firstname%' ";
	}
	if($lastname){
		$where['last_name'] = " LOWER(last_name) like '%$lastname%' ";
	}
	if($phone){
		$where['phone_number'] = " od.phone_number = '$phone' ";
	}
	if($city){
		$where['city'] = " LCASE(city) like LCASE('%$city%') ";
	}
	if($zip){
		$where['zip'] = " zip like '%$zip%' ";
	}
	if($sku){
		//$where2 = "AND product_sku like '%$sku%' ";
		// die($where2);
	}
	
	if($where){
		$where = implode('OR', $where);
		if ($zip) {
			// $where .= 'AND (' . $whereZip . ')';
		}
	} else if ($whereZip) {
		$where = $whereZip;
	}
	else{
		$where = "1 = 1";
	}
	if($sku)
	{
		$_query = "select o.*,od.* from inv_orders o inner join inv_orders_details od on (o.order_id = od.order_id) inner join inv_orders_items c on (od.order_id=c.order_id) where $where and c.product_sku like '%$sku%' group by o.order_id order by o.order_date desc ";
		
	}
	else
	{
		$_query = "select * from inv_orders o inner join inv_orders_details od on (o.order_id = od.order_id) where $where order by o.order_date desc";
	}
	
	//echo $_query;
	$orders = $db->func_query($_query);
	if(!$orders){
		$_SESSION['message'] = "Order is not found on given search filters. Please enter correct details and try again.";
	}
	else{
		$success = true;
		foreach($orders as $index => $order){
			 //echo "select * from inv_orders_items where order_id = '".$order['order_id']."' $where2";exit;
			$orders[$index]['order_products'] = $db->func_query("select * from inv_orders_items where order_id = '".$order['order_id']."'") ;
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Return products</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<style type="text/css">
		table td{text-align:center;}
	</style>
	<link href="<?php echo $host_path;?>/css/style.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript">
		function unlockBox(checked , order_product_id){
			if(checked){
				jQuery(".locked_"+order_product_id).find("select").removeAttr('disabled');
				jQuery(".locked_"+order_product_id).find("input").removeAttr('disabled');
			}
			else{
				jQuery(".locked_"+order_product_id).find("select").attr('disabled','disabled');
				jQuery(".locked_"+order_product_id).find("input").attr('disabled','disabled');
			}
		}
	</script>
</head>
<body>
	<div align="center" >
		<div <?php echo (isset($_GET['popup'])?'style="display:none"':''); ?>>
			<?php include_once 'inc/header.php';?>
		</div>
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		
		<h2>Customer Products Returns Search</h2>
		<form action="" method="post">
			<table border="0" cellpadding="10" cellspacing="0" width="83%">
				<tr>
					<td>First Name</td>
					<td><?php echo createField("firstname","firstname","text",$_POST['firstname'],null,"")?></td>
					<td>Last Name</td>
					<td><?php echo createField("lastname","lastname","text",$_POST['lastname'],null,"")?></td>
					
					<td>Telephone</td>
					<td><?php echo createField("phone","phone","text",$_POST['phone'],null,"")?></td>
					<td>SKU</td>
					<td><?php echo createField("sku","sku","text",$_POST['sku'],null,"")?></td>
				</tr>
				<tr>
					<td>City</td>
					<td><?php echo createField("city","city","text",$_POST['city'],null,"")?></td>
					<td>Zip Code</td>
					<td><?php echo createField("zip","zip","text",$_POST['zip'],null,"")?></td>
					<td>Email:</td>
					<td><?php echo createField("email","email","text",$_REQUEST['email'],null,(isset($_GET['popup'])?'readOnly':''))?></td>
					<td>Order ID:</td>
					<td><?php echo createField("order_id","order_id","text",$_POST['order_id'],null,"")?></td>
				</tr>
				<tr>
					<td align="center" colspan="8">
						<input type="submit" name="check" value="Submit" class="button" />
					</td>
				</tr>
			</table>
		</form>
		
		<?php if($success):?>
			<h2>Customer Products Returns Result</h2>
			<form action="" method="post">
				<table border="1" cellpadding="10" cellspacing="0" width="95%">
					<tr>
						<th>Order Date</th>
						<th>Order ID</th>
						<th>First Name</th>
						<th>Last Name</th>
						<th>Telephone</th>
						<th>Zip Code</th>
						<th>Email</th>
						<th>SKUs * Qty</th>
						<th>Action</th>
					</tr>
					<?php foreach($orders as $order):?>
						<tr>
							<td><?php echo $order['order_date'];?></td>
							<td><?php echo linkToOrder($order['order_id']);?></td>
							<td><?php echo $order['first_name'];?></td>
							<td><?php echo $order['last_name'];?></td>
							<td><?php echo $order['phone_number'];?></td>
							<td><?php echo $order['zip'];?></td>
							<td><?= linkToProfile($order['email']); ?></td>
							<td>
								<?php
								// if($sku)
								// {
								// 	$check_sku = $db->func_query_first_cell("SELECT product_sku from inv_orders_items where product_sku like '%$sku%'");
								// 	if(!$check_sku)
								// 	{
								// 		continue;
								// 	}
								// }
								?>
								<?php foreach($order['order_products'] as $product):?>
									<p><?php echo $product['product_sku'] ." * ". $product['product_qty']?></p>
								<?php endforeach;?>
							</td>
							<td>
								<?php echo RMAReturns($order['order_id']);?>
								<a onclick="if(!confirm('Are you sure?')){ return false; }" href="returns_create.php?order_id=<?php echo $order['order_id'];?>&<?=$popup_param;?>">Create</a>
							</td>
						</tr>
					<?php endforeach;?>
				</table>
				<br /><br />
				<a class="linkbutton" href="returns.php?<?=$popup_param;?>">Back</a>
			</form>		
		<?php endif;?>			
	</div>		
</body>
</html>