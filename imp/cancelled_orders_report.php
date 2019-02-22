<?php

include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';

$date = $_GET['report_date'];
if(!$date)
{
    $date = date('Y-m-d');
}
if ($_GET['shipped']) {
    $status = "('canceled',  'cancelled','shipped')";
} else {
    $status = "('canceled',  'cancelled')";
}
if ($_GET['store_front']) {
    $rows = $db->func_query("SELECT * FROM  inv_orders WHERE order_date LIKE '%".$date."%' AND LOWER( order_status ) IN ".$status." AND store_type = 'web' GROUP BY order_id");
}else{
    $rows = $db->func_query("SELECT * FROM  inv_orders WHERE order_date LIKE '%".$date."%' AND LOWER( order_status ) IN ".$status." GROUP BY order_id");   
}



$rows = $db->func_query("SELECT * FROM  inv_orders WHERE order_date LIKE '%".$date."%' AND LOWER( order_status ) IN ".$status." GROUP BY order_id");

?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Cancelled Orders Report</title>

	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

	<script type="text/javascript">
		function updateDbtn () {
            $('#download_button').attr('href', 'scripts/daily_sale_report.php?date='+$('#report_date').val());
        }
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
        	
        	<form method="get">
            <table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">
                            <tr>
                                <td colspan="2">
                                    <h2 align="center">Cancelled Orders Report</h2>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Date:
                                </td>
                                <td>
                                    <input onblur="updateDbtn();" type="text" data-type="date" value="<?php echo $date; ?>" name="report_date" id="report_date" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="checkbox" name="shipped" <?php echo ($_GET['shipped']) ? 'checked' : ''; ?>>Shipped Orders
                                <br>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="checkbox" name="store_front" <?php echo ($_GET['store_front']) ? 'checked' : ''; ?>>Storefront
                                <br>
                                </td>
                            </tr>
                            <tr>   
                                <td colspan="2" align="center">
                                
                                
                                    <input type="submit" value="Generate Report" class="button" /><br>
                                    
                                </td>
                            </tr>
                        </table>
        	</form>
        </div>

        	<table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
        		<thead>
        			<tr>
                        <th>Order ID</th>
        				<th>Order Date</th>
                        <th>Email</th>
        				<th>Customer</th>
        				<th>Order Price</th>
        				<th>Store Type</th>
        				<th>Payment</th>
        				<th>Cancellation Reason</th>
        			</tr>
        		</thead>
        		<tbody>
                    <?php foreach ($rows as $row) { 
                        $reason_id = $db->func_query_first_cell('SELECT reason_id from inv_order_cancel_report where order_id = "'. $row['order_id'] .'"');
                        $reason = $db->func_query_first_cell('SELECT name from inv_order_reasons where id = "'. $reason_id .'"');

                        ?>
                    <tr>
                        <td align="center"><a target="_blank" href="viewOrderDetail.php?order=<?php echo $row['order_id']?>"><?php echo $row['prefix'].$row['order_id'];?></a></td>
                        <td align="center"><?php echo americanDate($row['order_date']); ?></td>
                        <td align="center"><?php echo linkToProfile($row['email']); ?></td>
                        <td align="center"><?php echo $row['customer_name']; ?></td>
                        
                        <td align="center">$<?php echo $row['order_price']; ?></td>
                        <td align="center"><?php echo @mapStoreType($row['store_type']); ?></td>
                        <td align="center"><?php echo $row['payment_source']; ?></td>
                        <td align="center"><?php echo $reason?></td>
                    </tr>
                <?php } ?>
        		</tbody>   
        	</table>   
    </body>
    </html>        