<?php
ini_set('max_execution_time', 900); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';

$rows = $db->func_query("SELECT * from temp_customer where is_synced=0 and email not like '%@marketplace.amazon%' limit 200");
$dates = array(
			array('07','2015'),
			array('08','2015'),
			array('09','2015'),
			array('10','2015'),
			array('11','2015'),
			array('12','2015'),
			array('01','2016'),
			array('02','2016'),
			array('03','2016'),
			array('04','2016')
			);
foreach($rows as $row)
{
	foreach($dates as $date)
		{
			$month = $date[0];
			$year = $date[1];
			$order_data = $db->func_query_first("SELECT COUNT(*) as no_of_orders,sum(a.order_price) as order_price,sum(a.paid_price) as paid_price,(select count(*) from inv_returns where lower(trim(email))=lower(trim(a.email))  and month(date_added)='$month' and year(date_added)='$year'  ) as no_of_returns from inv_orders a where lower(trim(email))='".$row['email']."' and month(order_date)='".$month."' and year(order_date)='".$year."'");
			//$return_data = $db->func_query_first("select count(*) as no_of_returns from inv_returns where lower(trim(email))='".$row['email']."' and month(date_added)='$month' and year(date_added)='$year'");
			if($order_data['order_price']==0 and $order_data['paid_price']>0)
			{
				$order_data['order_price'] = $order_data['paid_price'];
			}
			$db->db_exec("INSERT INTO temp_customer_dt SET month='$month',year='$year',email='".$row['email']."',no_of_orders='".(int)$order_data['no_of_orders']."',total='".(float)$order_data['order_price']."',no_of_returns='".(int)$order_data['no_of_returns']."'");
		}
		echo $row['email']."<br>";
		$db->db_exec("UPDATE temp_customer SET is_synced=1 WHERE email='".$row['email']."'");
}
?>