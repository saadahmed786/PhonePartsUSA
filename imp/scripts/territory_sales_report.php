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

$territories = $db->func_query("SELECT distinct territory,zone_id,name FROM oc_zone WHERE country_id=223 order by territory DESC,name ASC");


$filename = 'territory_sale-'.$first_date.'.csv';
$fp = fopen($filename, "w");
$headers = array("","State","Unique Customers","No. of Orders","Order Amount","Shipping Paid","Shipping Cost");
fputcsv($fp, $headers);
 fputcsv($fp,array());
 
 $old_territory = '';
 $i = 0;
foreach($territories as $territory)
{
	if($territory['territory']=='')
	{
		$territory['territory'] = 'Uncategorized';
	}
	if($old_territory!=$territory['territory'])
	{
		fputcsv($fp,array());
		$old_territory = $territory['territory'];
		

	}
	else
	{
		$territory['territory'] = '';
	}
	// echo "select count( DISTINCT(a.email) ) as unique_customers, count(*) as no_of_orders,sum(a.order_price) as order_price,sum(b.shipping_cost) as shipping_paid, sum(c.shipping_cost+c.insurance_cost) as s_cost    FROM `inv_orders_details` b,inv_orders a left join inv_shipstation_transactions c on (a.order_id=c.order_id) WHERE a.order_id=b.order_id and date(a.order_date) between '".$first."' and '".$second."'  and lower(a.order_status) in ('processed','shipped') and a.store_type not like '%amazon%' and b.zone_id='".$territory['zone_id']."' ";exit;

	$data = $db->func_query_first("select count( DISTINCT(a.email) ) as unique_customers, count(*) as no_of_orders,sum(a.order_price) as order_price,sum(b.shipping_cost) as shipping_paid, sum(c.shipping_cost+c.insurance_cost) as s_cost    FROM `inv_orders_details` b,inv_orders a left join inv_shipstation_transactions c on (a.order_id=c.order_id) WHERE a.order_id=b.order_id and date(a.order_date) between '".$first."' and '".$second."'  and lower(a.order_status) in ('processed','shipped') and a.store_type not like '%amazon%' and b.zone_id='".$territory['zone_id']."' ");

	$rowData = array($territory['territory'],$territory['name'],(int)$data['unique_customers'],(int)$data['no_of_orders'],(float)$data['order_price'],(float)$data['shipping_paid'],(float)$data['s_cost']);
	

	fputcsv($fp, $rowData);
	$i++;
}


fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);



?>