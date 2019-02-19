<?php
set_time_limit(200);
include_once '../config.php';
include_once '../inc/functions.php';

$date = date('Y-m-d');
// $date = '2018-04-16';
$users = $db->func_query("SELECT id,commission_date,commission FROM inv_users WHERE is_sales_agent=1 and status=1 AND commission_date<>'0000-00-00'");

// for($i=1;$i<=31;$i++)
// {
	// $date = '2018-01-'.str_pad($i, 2,'0',STR_PAD_LEFT);
	
foreach($users as $user)
{
	$commission_rate = $user['commission'];
	$commission_perc = $commission_rate/100;

	$commission_date = $user['commission_date'];


	$check = $db->func_query_first_cell("delete from inv_user_commission WHERE user_id='".$user['id']."' and date(date_updated)='".$date."' ");
	
	 $commission_total = $db->func_query_first_cell("SELECT SUM(b.total) AS total FROM inv_customer_data_summary b,inv_customers a where lower(trim(a.email))=lower(trim(b.email)) and date(b.date)>=date(a.sales_assigned_date)  and b.type='order' and a.user_id='".$user['id']."' and date(b.date) = '$date' and date(b.date)>='".$commission_date."'  order by b.date asc");

	  $commission_voucher = $db->func_query_first_cell("SELECT sum(c.`amount`) as total FROM `oc_voucher` b LEFT OUTER JOIN `oc_voucher_history` c ON b.`voucher_id` = c.`voucher_id` INNER JOIN inv_customers a on (b.to_email=a.email) where lower(b.code) NOT LIKE '%lbb%' and date(c.date_added) = '$date' and date(c.date_added)>='".$commission_date."'  and date(c.date_added)>=date(a.sales_assigned_date) AND a.user_id='".$user['id']."' and b.status=1  ");

       $net_commission = $commission_total+$commission_voucher;

       $commission_amount = $net_commission*$commission_perc;
      
   
   $db->db_exec("INSERT INTO inv_user_commission SET user_id='".$user['id']."',sale_amount='".(float)$commission_total."',voucher_amount='".(float)$commission_voucher."',rate='".(float)$commission_rate."',commission='".(float)$commission_amount."',date_updated='".$date."'");





}
// }
echo 1;
?>