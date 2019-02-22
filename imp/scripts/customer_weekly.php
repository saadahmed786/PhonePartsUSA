<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';
$start    = new DateTime('-2 years');
$start    = new DateTime('Monday this week');
$end      = new DateTime('Sunday this week');

// $start    = new DateTime('Monday last week');
// $end      = new DateTime('Sunday last week');
// print_r($end);exit;
$interval = new DateInterval('P1W');
$period   = new DatePeriod($start, $interval, $end);
// print_r($period);exit;
// echo date('w');exit;
// foreach($period as $date)
// {
//     echo $date->format('Y-m-d')."<br>";
// }
// exit;
// echo date('W');exit;
foreach ($period as $date) {

// echo 'here';exit;
  // echo $date->format('Y').'-'.$date->format('m').'-'.$date->format('d');exit;
	// echo $date->format('Y').'-'.$date->format('W');exit;
  // echo "SELECT SUM(a.sub_total+a.shipping_amount+a.tax) as total,count(*) as my_count,trim(lower(a.email)) as email FROM inv_orders a,inv_customers b where trim(lower(a.email))=trim(lower(b.email)) and  lower(a.order_status) in ('shipped','processed','completed','paid','awaiting fulfillment') AND  yearweek(a.order_date,3)='".$date->format('Y').$date->format('W')."' and b.email not like '%@marketplace.amazon%'  group by trim(lower(a.email))";exit;
	$db->db_exec("delete from inv_customer_data_summary WHERE YEARWEEK(date,3)='".$date->format('Y').$date->format('W')."'");

    // $query = $db->func_query("SELECT SUM(a.sub_total+a.shipping_amount+a.tax) as total,count(*) as my_count,trim(lower(a.email)) as email FROM inv_orders a,inv_customers b where trim(lower(a.email))=trim(lower(b.email)) and  lower(a.order_status) in ('shipped','processed','completed','paid','awaiting fulfillment') AND  yearweek(a.order_date,3)='".$date->format('Y').$date->format('W')."' and b.email not like '%@marketplace.amazon%'  group by trim(lower(a.email))");

  $query = $db->func_query("SELECT SUM(a.sub_total+a.shipping_amount+a.tax) as total,count(*) as my_count,trim(lower(a.email)) as email FROM inv_orders a where   lower(a.order_status) in ('shipped','processed','completed','paid','awaiting fulfillment') AND  yearweek(a.order_date,3)='".$date->format('Y').$date->format('W')."' and a.email not like '%@marketplace.amazon%'  group by trim(lower(a.email))");
   // print_r($query);exit;
    foreach($query as $row)
    {	
    	$db->db_exec("INSERT INTO inv_customer_data_summary SET  email='".$db->func_escape_string($row['email'])."',no_record='".(int)$row['my_count']."',total='".(float)$row['total']."',date='".$date->format('Y').'-'.$date->format('m').'-'.$date->format('d')."'");
    }
    /*
    $query = $db->func_query("SELECT SUM(a.order_price) as price,count(*) as my_count,trim(lower(a.email)) as email FROM inv_orders a,inv_customers b where trim(lower(a.email))=trim(lower(b.email)) and  lower(a.order_status) in ('shipped','processed','completed','paid','awaiting fulfillment') AND  yearweek(a.order_date,3)='".$date->format('Y').$date->format('W')."'  and LCASE(a.payment_source) = 'replacement' group by a.email");

	foreach($query as $row)
    {	
    	$db->db_exec("INSERT INTO inv_customer_data_summary SET  email='".$db->func_escape_string($row['email'])."',no_record='".(int)$row['my_count']."',total='".(float)$row['price']."',date='".$date->format('Y').'-'.$date->format('m').'-'.$date->format('d')."',type='replacement'");
    }

    $query = $db->func_query("SELECT sum(b.price) as price,count(*) as my_count,a.email FROM inv_customers a, inv_return_decision b,inv_returns c WHERE c.id=b.return_id and b.action='Issue Refund' and TRIM(lower(c.email))=trim(lower(a.email))  and yearweek(c.date_completed,3)='".$date->format('Y').$date->format('W')."'   group by a.email");

	foreach($query as $row)
    {	
    	$db->db_exec("INSERT INTO inv_customer_data_summary SET  email='".$db->func_escape_string($row['email'])."',no_record='".(int)$row['my_count']."',total='".(float)$row['price']."',date='".$date->format('Y').'-'.$date->format('m').'-'.$date->format('d')."',type='return_refund'");
    }

    $query = $db->func_query("SELECT sum(b.`amount`) as price,count(*) as my_count,a.email FROM oc_voucher b,inv_customers a WHERE b.status=1 and TRIM(lower(b.to_email))=trim(lower(a.email)) and left(b.code,3)<>'LBB'  and yearweek(b.date_added,3)='".$date->format('Y').$date->format('W')."'   group by a.email");

	foreach($query as $row)
    {	
    	$db->db_exec("INSERT INTO inv_customer_data_summary SET  email='".$db->func_escape_string($row['email'])."',no_record='".(int)$row['my_count']."',total='".(float)$row['price']."',date='".$date->format('Y').'-'.$date->format('m').'-'.$date->format('d')."',type='return_store_credit'");
    }
    */
}


// commission



$start    = new DateTime('Monday this week');
$end      = new DateTime('Monday next week');


$interval = new DateInterval('P1D');
$period   = new DatePeriod($start, $interval, $end);



$users = $db->func_query("SELECT id,commission_date,commission FROM inv_users WHERE is_sales_agent=1 and status=1 AND commission_date<>'0000-00-00'");

$users = array();


foreach($users as $user)
{
    $commission_rate = $user['commission'];
    $commission_perc = $commission_rate/100;

    $commission_date = $user['commission_date'];
    foreach($period as $date)
    {
    


    $check = $db->func_query_first_cell("delete from inv_user_commission WHERE user_id='".$user['id']."' and date(date_updated)='".$date->format('Y-m-d')."' ");
    
     // $commission_total = $db->func_query_first_cell("SELECT SUM(b.total) AS total FROM inv_customer_data_summary b,inv_customers a where lower(trim(a.email))=lower(trim(b.email)) and date(b.date)>=date(a.sales_assigned_date)  and b.type='order' and a.user_id='".$user['id']."' and date(b.date) = '".$date->format('Y-m-d')."' and date(b.date)>='".$commission_date."'  order by b.date asc");
     $commission_total = $db->func_query_first_cell("SELECT SUM(b.sub_total+b.shipping_amount+b.tax) AS total FROM inv_orders b,inv_customers a where lower(trim(a.email))=lower(trim(b.email)) and date(b.order_date)>=date(a.sales_assigned_date)  and lower(b.order_status) in ('shipped','processed','completed','paid','awaiting fulfillment') and a.user_id='".$user['id']."' and date(b.order_date) = '".$date->format('Y-m-d')."' and date(b.order_date)>='".$commission_date."'  order by b.order_date asc");

      $commission_voucher = $db->func_query_first_cell("SELECT sum(c.`amount`) as total FROM `oc_voucher` b LEFT OUTER JOIN `oc_voucher_history` c ON b.`voucher_id` = c.`voucher_id` INNER JOIN inv_customers a on (b.to_email=a.email) where lower(b.code) NOT LIKE '%lbb%' and b.reason_id<>5 and date(c.date_added) = '".$date->format("Y-m-d")."' and date(c.date_added)>='".$commission_date."'  and date(c.date_added)>=date(a.sales_assigned_date) AND a.user_id='".$user['id']."' and b.status=1  ");

       $net_commission = $commission_total+$commission_voucher;

       $commission_amount = $net_commission*$commission_perc;
      
   
   $db->db_exec("INSERT INTO inv_user_commission SET user_id='".$user['id']."',sale_amount='".(float)$commission_total."',voucher_amount='".(float)$commission_voucher."',rate='".(float)$commission_rate."',commission='".(float)$commission_amount."',date_updated='".$date->format('Y-m-d')."'");

}





}

echo 1;
?>