<?php
require_once("auth.php");
include_once 'inc/functions.php';

$title = 'Locate Customer';
$second_parameter = 0;
$where = array();
$where2 = array();
$fetch = 0;
if($_GET['firstname']){
	$firstname = strtolower($db->func_escape_string($_GET['firstname']));
	$where[] = " lower(firstname) LIKE '%$firstname%' ";
	$where2[] = " lower(od.first_name) LIKE '%$firstname%' ";
	$fetch = 1;
	$second_parameter = 1;
}

if($_GET['lastname']){
	$lastname = strtolower($db->func_escape_string($_GET['lastname']));
	$where[] = " lower(lastname) LIKE '%$lastname%' ";
	$where2[] = " lower(od.last_name) LIKE '%$lastname%' ";
	$fetch = 1;
	$second_parameter = 1;
}

if($_GET['email']){
	$email = strtolower($db->func_escape_string($_GET['email']));
	$where[] = " email LIKE '%$email%' ";
	$where2[] = " o.email LIKE '%$email%' ";
	$fetch = 1;
	$second_parameter = 1;
}

if($_GET['telephone']){
	$telephone = strtolower($db->func_escape_string($_GET['telephone']));
	$where[] = " telephone LIKE '%$telephone%' ";
	$where2[] = " od.phone_number LIKE '%$telephone%' ";
	$fetch = 1;
	$second_parameter = 1;
}

if($_GET['city']){
	$city = strtolower($db->func_escape_string($_GET['city']));
	$where[] = " city LIKE '%$city%' ";
	$where2[] = " od.city LIKE '%$city%' ";
	$fetch = 1;
	$second_parameter = 1;
}

if($_GET['zipcode']){
	$zipcode = strtolower($db->func_escape_string($_GET['zipcode']));
	$where[] = " zip LIKE '%$zipcode%' ";
	$where2[] = " od.zip LIKE '%$zipcode%' ";
	$fetch = 1;
	$second_parameter = 1;
}
if($where){
	$where = implode(" AND ",$where);
}
else{
	$where = ' 1 = 1';
}
if($where2){
	$where2 = implode(" AND ",$where2);
}
else{
	$where2 = ' 1 = 1';
}
if($_GET['order_id'] && $second_parameter == 1 ){
	//print_r('here');exit;
	$order_id = strtolower($db->func_escape_string($_GET['order_id']));
	$_query = 'SELECT  od.first_name as firstname, od.last_name as lastname,o.email as email, od.phone_number as telephone,od.city,od.state,od.zip from inv_orders_details od inner join inv_orders o on (o.order_id = od.order_id) where od.order_id = "'.$order_id.'" AND '.$where2.' ';
	$fetch = 1;
} else if($_GET['order_id'] && $second_parameter == 0) {
	$order_id = strtolower($db->func_escape_string($_GET['order_id']));
	$_query = 'select  od.first_name as firstname, od.last_name as lastname,o.email as email, od.phone_number as telephone,od.city,od.state,od.zip from inv_orders_details od inner join inv_orders o on (o.order_id = od.order_id) where od.order_id = "'.$order_id.'"' ;
	$fetch = 1;
} else if (!$_GET['order_id'] && $fetch == 1){
	$_query = 'SELECT * FROM inv_customers  WHERE  '.$where.' order by firstname';
}


if ($fetch == 1) {
$rows = $db->func_query($_query);
} 
if (!$rows && $second_parameter == 1) {
	$_query = 'SELECT  od.first_name as firstname, od.last_name as lastname,o.email as email, od.phone_number as telephone,od.city,od.state,od.zip from inv_orders_details od inner join inv_orders o on (o.order_id = od.order_id) where '.$where2.' ';
	$rows = $db->func_query($_query);
}
if ($rows) {
	$title = 'Select Customer';

} else if ($fetch == 1)
{
	$_SESSION['message'] = 'No record found';
   	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Create Return</title>
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
			<form method="get" enctype="multipart/form-data" >
				<h2><?php echo $title; ?><br /></h2><br>
					<div align="center">
						
							<label for="firstname">First Name:</label>
							<input type="text" name="firstname" value="<?php echo $_GET['firstname']; ?>">
				
							<label for="firstname">Last Name:</label>
							<input type="text" name="lastname" value="<?php echo $_GET['lastname']; ?>">
					
							<label for="telephone">Telephone:</label>
							<input type="text" name="telephone" value="<?php echo $_GET['telephone']; ?>">
						
					</div>
					<br><br>
				
						<div align="center">
							<label for="city">City:</label>
							<input type="text" name="city" value="<?php echo $_GET['city']; ?>">
						
							<label for="zipcode">Zip Code:</label>
							<input type="text" name="zipcode" value="<?php echo $_GET['zipcode']; ?>">
						
							<label for="email">Email:</label>
							<input type="text" name="email" value="<?php echo $_GET['email']; ?>">
						
							<label for="order_id">Order ID:</label>
							<input type="text" name="order_id" value="<?php echo $_GET['order_id']; ?>">
						</div>
							
						<br>
						<input class="button" type="submit" name="submit" value="Submit">
					
					<br><br>

					<?php if ($rows){ ?>
					<table width="70%" cellspacing="0" cellpadding="5px" border="1" align="center">
						<thead>
							<tr style="background-color:#e5e5e5;">
							
								<th>First Name</th>
								<th>Last Name</th>
								<th>Email</th>
								<th>Telephone</th>
								<th>City</th>
								<th>State</th>
								<th>Zip Code</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						<?php 
						 foreach ($rows as $customer) { ?>
							<tr>
								<td><?php echo $customer['firstname']; ?></td>
								<td> <?php echo $customer['lastname']; ?></td>
								<td> <?php echo $customer['email']; ?></td>
								<td> <?php echo $customer['telephone']; ?></td>
								<td> <?php echo $customer['city']; ?></td>
								<td> <?php echo $customer['state']; ?></td>
								<td> <?php echo $customer['zip']; ?></td>
								<td> <a href="<?php echo $host_path ?>select_return_items.php?action=filter_products&email=<?php echo $customer['email']; ?>">Select</a></td>
							</tr>
							<?php 
							 } ?>
						</tbody>
					</table> 
					<br><br>
					<?php } ?> 
				</form>
         </div>
     </body>
     </html>					
     