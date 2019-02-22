<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';


if(isset($_GET['date']))
{
	$date= $_GET['date'];
	$first_date = '01-'.$date;
	$second_date = '30-'.$date;
	// echo 
	// echo $first_date;exit;
	$first = date('Y-m-01',strtotime($first_date));
	$second = date('Y-m-t',strtotime($first_date));

	// echo $second;exit;
	//$first = explode("-", $first_date);
	//$second = explode("-", $second_date);

}
else
{
	echo 'Please provide date month ie., mm-yyyy';exit;
}
// echo "SELECT distinct b.state FROM `inv_orders_details` b,inv_orders a WHERE a.order_id=b.order_id and date(a.order_date) between '".$first."' and '".$second."'  and lower(a.order_status) in ('processed','shipped') and a.store_type not like '%amazon%'";exit;

// $rows = $db->func_query("SELECT distinct b.state FROM `inv_orders_details` b,inv_orders a WHERE a.order_id=b.order_id and date(a.order_date) between '".$first."' and '".$second."'  and lower(a.order_status) in ('processed','shipped') and a.store_type not like '%amazon%'");
$rows = $db->func_query("SELECT distinct territory,zone_id,name FROM oc_zone WHERE country_id in (223,38) order by territory DESC,name ASC");

$filename = 'state-sorted-orders-'.$first_date.'.csv';
$fp = fopen($filename, "w");
$headers = array("Territory","State","Unique Customers","No. of Orders","Order Amount","Shipping Paid","Shipping Cost");
fputcsv($fp, $headers);


foreach($rows as $row)
{
	// $row['state'] = str_replace(array(",",";"), " ", $row['state']);
	
	$data = $db->func_query_first("select count( DISTINCT(a.email) ) as unique_customers, count(*) as no_of_orders,sum(a.order_price) as order_price,sum(b.shipping_cost) as shipping_paid, sum(c.shipping_cost+c.insurance_cost) as s_cost    FROM `inv_orders_details` b,inv_orders a left join inv_shipstation_transactions c on (a.order_id=c.order_id) WHERE a.order_id=b.order_id and date(a.order_date) between '".$first."' and '".$second."'  and lower(a.order_status) in ('processed','shipped') and a.store_type not like '%amazon%' and b.zone_id='".$row['zone_id']."' ");

$rowData = array($row['territory'],$row['name'],(int)$data['unique_customers'],(int)$data['no_of_orders'],(float)$data['order_price'],(float)$data['shipping_paid'],(float)$data['s_cost']);
	

	fputcsv($fp, $rowData);

}
fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);

// echo 1;
?>