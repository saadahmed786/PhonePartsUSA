<?php
require_once("auth.php");
require_once('inc/functions.php');
if($_SESSION['login_as'] != 'admin'){
	$_SESSION['message'] = 'You dont have permission to see this page.';
	header("Location:$host_path/home.php");
	exit;
}

$ebayLastCronDate   = $db->func_query_first_cell("select last_cron_date  from ebay_credential ORDER BY last_cron_date DESC");
$bigcommerceLastCronDate   = $db->func_query_first_cell("select last_cron_date  from bigcommerce_credential ORDER BY last_cron_date DESC");
$caLastCronDate     = $db->func_query_first_cell("select last_cron_date  from ca_credential ORDER BY last_cron_date DESC");
$amazonLastCronDate = $db->func_query_first_cell("select last_cron_date  from  amazon_credential where id = 2 ");
$amazonCALastCronDate = $db->func_query_first_cell("select last_cron_date  from  amazon_credential where id = 4 ");
$amazonMXLastCronDate = $db->func_query_first_cell("select last_cron_date  from  amazon_credential where id = 3 ");
$amazonPGLastCronDate = $db->func_query_first_cell("select last_cron_date  from  amazon_credential where id = 1 ");
$amazonPGCALastCronDate = $db->func_query_first_cell("select last_cron_date  from  amazon_credential where id = 6 ");
$amazonPGMXLastCronDate = $db->func_query_first_cell("select last_cron_date  from  amazon_credential where id = 7 ");
$webLastCronDate    = $db->func_query_first("select config_value  from configuration where config_key = 'WEB_LAST_CRON_TIME'");

$countOrders = $db->func_query_first("Select count(*) as orders from inv_orders");
$countReturnOrders = $db->func_query_first("Select count(*) as orders from inv_return_orders");

$todayOrders = $db->func_query_first("Select count(*) as orders from inv_orders where order_date like '%".date('Y-m-d')."%'");
$todayReturnOrders = $db->func_query_first("Select count(*) as orders from inv_return_orders where order_date like '%".date('Y-m-d')."%'");

$today_sales = $db->func_query("Select sum(order_price) as total , store_type , sub_store_type from inv_orders where order_date like '%".date('Y-m-d')."%' group by store_type , sub_store_type order by store_type asc","store_type");
$total_sales = $db->func_query("Select sum(order_price) as total , store_type , sub_store_type from inv_orders group by store_type order by store_type , sub_store_type asc","store_type");

//print_r($total_sales); exit;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Home</title>
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
		 
		 <h2 align="center">Cron Summary</h2>
		 <table align="center"  style="border:1px solid #585858;border-collapse:collapse;" cellpadding="10px" width="60%" border="1" cellspacing="0">
			<tr>
				<th> eBay Last Cron Date : </th>
				<?php if($ebayLastCronDate) :?>
					<td> <?php echo americanDate($ebayLastCronDate) ;?> </td>
				<?php else :?>
					<td> Not Found.</td>
				<?php endif ;?>
			</tr>
			
			<tr>
				<th> Bigcommerce Last Cron Date : </th>
				<?php if($bigcommerceLastCronDate) :?>
					<td> <?php echo americanDate(($bigcommerceLastCronDate)) ;?> </td>
				<?php else :?>
					<td> Not Found.</td>
				<?php endif ;?>
			</tr>
			
			<tr>
				<th> Channel Advisor Last Cron Date : </th>
				<?php if($caLastCronDate) :?>
					<td> <?php echo americanDate(($caLastCronDate)) ;?> </td>
				<?php else :?>
					<td> Not Found.</td>
				<?php endif ;?>
			</tr>
			
			<tr>
				<th> Amazon Last Cron Date : </th>
				<?php if($amazonLastCronDate) :?>
					<td> <?php echo americanDate($amazonLastCronDate) ;?> </td>
				<?php else :?>
					<td> Not Found.</td>
				<?php endif ;?>
			</tr>
			<tr>
				<th> Amazon CA Last Cron Date : </th>
				<?php if($amazonCALastCronDate) :?>
					<td> <?php echo americanDate(($amazonCALastCronDate)) ;?> </td>
				<?php else :?>
					<td> Not Found.</td>
				<?php endif ;?>
			</tr>

			<tr>
				<th> Amazon MX Last Cron Date : </th>
				<?php if($amazonMXLastCronDate) :?>
					<td> <?php echo americanDate(($amazonMXLastCronDate)) ;?> </td>
				<?php else :?>
					<td> Not Found.</td>
				<?php endif ;?>
			</tr>

			<tr>
				<th> Amazon PG Last Cron Date : </th>
				<?php if($amazonPGLastCronDate) :?>
					<td> <?php echo americanDate(($amazonPGLastCronDate)) ;?> </td>
				<?php else :?>
					<td> Not Found.</td>
				<?php endif ;?>
			</tr>

			<tr>
				<th> Amazon PGCA Last Cron Date : </th>
				<?php if($amazonPGCALastCronDate) :?>
					<td> <?php echo americanDate(($amazonPGCALastCronDate)) ;?> </td>
				<?php else :?>
					<td> Not Found.</td>
				<?php endif ;?>
			</tr>
			
			<tr>
				<th> Amazon PGMX Last Cron Date : </th>
				<?php if($amazonPGMXLastCronDate) :?>
					<td> <?php echo americanDate(($amazonPGMXLastCronDate)) ;?> </td>
				<?php else :?>
					<td> Not Found.</td>
				<?php endif ;?>
			</tr>

			<tr>
				<th> Web Last Cron Date : </th>
				<?php if($webLastCronDate['config_value']) :?>
					<td> <?php echo americanDate(($webLastCronDate['config_value'])) ;?> </td>
				<?php else :?>
					<td> Not Found.</td>
				<?php endif ;?>
			</tr>
        </table>
        
        <br />
        <h2 align="center">Order Summary</h2>    
		<table align="center"  style="border:1px solid #585858;border-collapse:collapse;" cellpadding="10px" width="60%" border="1" cellspacing="0">
			<tr>
				<th>Today Completed Orders: </th>

				<?php if($todayOrders['orders']) :?>
					<td> <?php echo $todayOrders['orders'] ;?> </td>
				<?php else :?>
					<td> Not Found.</td>
				<?php endif ;?>
                
                <th> Today Voided Orders: </th>
                <?php if($todayReturnOrders['orders']) :?>
                    <td> <?php echo $todayReturnOrders['orders'] ;?> </td>
                <?php else :?>
                    <td> Not Found.</td>
                <?php endif ;?>
			</tr>
            
            <tr>
                <th>Total Completed Orders: </th>

                <?php if($countOrders['orders']) :?>
                    <td> <?php echo $countOrders['orders'] ;?> </td>
                <?php else :?>
                    <td> Not Found.</td>
                <?php endif ;?>
            
                <th> Total Voided Orders: </th>
                <?php if($countReturnOrders['orders']) :?>
                    <td> <?php echo $countReturnOrders['orders'] ;?> </td>
                <?php else :?>
                    <td> Not Found.</td>
                <?php endif ;?>
            </tr>
       </table>
       
       
       <br />
       <h2 align="center">Sales Summary</h2>
       <table align="center"  style="border:1px solid #585858;border-collapse:collapse;" cellpadding="10px" width="60%" border="1" cellspacing="0"> 
            <tr>
                <td></td>
                <th>eBay</th>
                <th>Amazon</th>
                <th>Web</th>
                <th>Channel Advisor</th>
                <th>Bigcommerce</th>
            </tr>
            
            <tr>
                 <th>Today Sales</th>
                 <td align="center">$<?php echo $today_sales['ebay']['total'] ;?></td>
                 <td align="center">$<?php echo $today_sales['amazon']['total'] ;?></td>
                 <td align="center">$<?php echo $today_sales['web']['total'] ;?></td>
                 <td align="center">$<?php echo $today_sales['bigcommerce']['total'] ;?></td>
                 <td align="center">$<?php echo $today_sales['channel_advisor']['total'] ;?></td>
            </tr>
            
            <tr>
                 <th>Total Sales</th>
                 <td align="center">$<?php echo $total_sales['ebay']['total'] ;?></td>
                 <td align="center">$<?php echo $total_sales['amazon']['total'] ;?></td>
                 <td align="center">$<?php echo $total_sales['web']['total'] ;?></td>
                 <td align="center">$<?php echo $total_sales['bigcommerce']['total'] ;?></td>
                 <td align="center">$<?php echo $total_sales['channel_advisor']['total'] ;?></td>
            </tr>
		</table>
    </body>
</html>