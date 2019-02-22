<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';

function getMonthsInRange($startDate, $endDate) {
$months = array();
// echo $endDate;exit;
while (strtotime($startDate) <= strtotime($endDate)) {
    $months[] = array('year' => date('Y', strtotime($startDate)), 'month' => date('m', strtotime($startDate)), );
    $startDate = date('d M Y', strtotime($startDate.
        '+ 1 month'));
}

return $months;
}

//$month = date('m');
$year = date('Y');


if(isset($_GET['date']))
{
	$dates = explode(",",$_GET['date']);
	$first_date = '01-'.$dates[0];
	$second_date = '30-'.$dates[1];
	// echo 
	// echo $first_date;exit;
	$first = date('Y-m-01',strtotime($first_date));
	$second = date('Y-m-30',strtotime($second_date));

	// echo $second;exit;
	//$first = explode("-", $first_date);
	//$second = explode("-", $second_date);

}
else
{
	echo 'Please provide proper date range';exit;
}
if(isset($_GET['state']))
{
	$state = $db->func_escape_string($_GET['state']);
	$state = strtolower(trim($state));
}
else
{
	echo 'Please provide the state name';exit;
}
$months = (getMonthsInRange($first_date,$second_date));
$total = 100;
$page = 1;
if(isset($_GET['page']))
{
	$page = (int)$_GET['page'];
}
$limit_start = ($page-1)*$total;

$filename = 'state-sorted-customers-'.$page.'.csv';
$fp = fopen($filename, "w");
$headers = array("Sales Agent","First & Last Name","Email","City","State","Order Amount");
foreach($months as $month)
{
	$headers[] = ($month['month']).'-'.$month['year'];
}
$headers[] = '# of Return Items';
$headers[] = 'Return Amount';
$headers[] = 'LBB Amount';
// ,"# of Return Items","Return Amount","LBB Amount"
fputcsv($fp, $headers);

$rows = $db->func_query("SELECT count(*) as count,lower(trim(a.email)) as email,sum(a.order_price) as order_price FROM inv_orders a LEFT JOIN inv_orders_details b on(a.order_id=b.order_id)  where date(a.order_date) between '".$first."' and '".$second."'  and lower(a.order_status) in ('processed','shipped') and a.store_type not like '%amazon%' and LOWER(TRIM(b.state))='".$state."' GROUP by email   order by 3 desc limit ".$limit_start.", ".$total);
foreach($rows as $row)
{

	// $customer_data = $db->func_query_first("SELECT b.name,concat(a.firstname,' ',a.lastname) as customer_name,a.city,a.state FROM inv_customers a left join inv_users b on (b.id=a.user_id) where   lower(trim(a.email))='".$row['email']."'");
	$customer_data = $db->func_query_first("SELECT concat(a.firstname,' ',a.lastname) as customer_name,a.city,a.state,(select name from inv_users b where a.user_id = b.id) as name FROM inv_customers a  where   lower(trim(a.email))='".$row['email']."'");

	$sales_agent = $customer_data['name'];
	$customer_name = $customer_data['customer_name'];
	$no_of_orders = $row['count'];
	$order_amount = (float)$row['order_price'];
	$city = $customer_data['city'];
	$state = $customer_data['state'];
	

	$no_of_return_items = $db->func_query_first_cell("SELECT COUNT(*) as count FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id and lower(trim(a.email))='".$row['email']."' and date(date_added) between '".$first."' and '".$second."' ");
	// $no_of_return_items = $db->func_query_first_cell("SELECT (SELECT COUNT(*) FROM inv_return_items b where a.id=b.return_id) as count FROM inv_returns a WHERE  lower(trim(a.email))='".$row['email']."' and date(a.date_completed) between '".$first."' and '".$second."' ");

	$return_total = $db->func_query_first_cell("select sum(b.price) as price FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id and a.rma_status='Completed' and lower(trim(a.email))='".$row['email']."' and date(date_completed) between '".$first."' and '".$second."'  ");
	// $return_total = $db->func_query_first_cell("select (select sum(price) from inv_return_items b where a.id=b.return_id) as price FROM inv_returns a WHERE a.rma_status='Completed' and lower(trim(a.email))='".$row['email']."' and date(a.date_completed) between '".$first."' and '".$second."'  ");

	// $return_refund = $db->func_query_first_cell("select sum(b.price) as price FROM inv_returns a,inv_return_decision b WHERE a.id=b.return_id and lower(trim(a.email))='".$row['email']."' and month(b.date_added)='".$month."' and year(b.date_added)='".$year."' and b.action='Issue Refund' ");

	// $lbb_completed =
	$lbb_products = $db->func_query("SELECT (select email from oc_customer c where c.customer_id=a.customer_id) as customer_email,b.* FROM oc_buyback_products b,oc_buyback a  WHERE a.buyback_id=b.buyback_id and a.status='Completed' and date(a.date_completed) between '".$first."' and '".$second."' and (lower((select email from oc_customer c where c.customer_id=a.customer_id))='".$row['email']."' OR a.email='".$row['email']."') "); 

	$qc_quantity_total = 0;
														$admin_oem_a_total = 0.00;
														$admin_oem_b_total = 0.00;
														$admin_oem_c_total = 0.00;
														$admin_oem_d_total = 0.00;
														$admin_non_oem_a_total = 0.00;
														$admin_non_oem_b_total = 0.00;
														$admin_non_oem_c_total = 0.00;
														$admin_non_oem_d_total = 0.00;
														$admin_salvage_total = 0.00;
														$admin_combine_total = 0.00;

														foreach($lbb_products as $product)
														{
															if($product['data_type']!='customer' and $product['data_type']!='qc' and $product['data_type']!='admin') continue;


															$quantities = $db->func_query_first("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$product['buyback_product_id']."'");
															
															if($quantities)
															{
																$oem_a_qty = (int)$quantities['oem_qty_a'];
																$oem_b_qty = (int)$quantities['oem_qty_b'];
																$oem_c_qty = (int)$quantities['oem_qty_c'];
																$oem_d_qty = (int)$quantities['oem_qty_d'];
																$non_oem_a_qty = (int)$quantities['non_oem_qty_a'];
																$non_oem_b_qty = (int)$quantities['non_oem_qty_b'];
																$non_oem_c_qty = (int)$quantities['non_oem_qty_c'];
																$non_oem_d_qty = (int)$quantities['non_oem_qty_d'];
																$salvage_qty = (int)$quantities['salvage_qty'];
																$unacceptable_qty = (int)$quantities['unacceptable_qty'];
																$rejected_qty = (int)$quantities['rejected_qty'];
															}

															if($product['admin_updated']=='1')
															{
																$oem_a_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_oem_a_qty']: $oem_a_qty;
																$oem_b_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_oem_b_qty']: $oem_b_qty;
																$oem_c_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_oem_c_qty']: $oem_c_qty;
																$oem_d_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_oem_d_qty']: $oem_d_qty;
																$non_oem_a_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_non_oem_a_qty']: $non_oem_a_qty;
																$non_oem_b_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_non_oem_b_qty']: $non_oem_b_qty;
																$non_oem_c_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_non_oem_c_qty']: $non_oem_c_qty;
																$non_oem_d_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_non_oem_d_qty']: $non_oem_d_qty;
																$salvage_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_salvage_qty']: $salvage_qty;
																$unacceptable_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_unacceptable']: $unacceptable_qty;
																$rejected_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_rejected']: $rejected_qty;
															}

															$admin_oem_a_total+=(int)$oem_a_qty * (float)$product['oem_a_price'];
															$admin_oem_b_total+=(int)$oem_b_qty * (float)$product['oem_b_price'];
															$admin_oem_c_total+=(int)$oem_c_qty * (float)$product['oem_c_price'];
															$admin_oem_d_total+=(int)$oem_d_qty * (float)$product['oem_d_price'];
															$admin_non_oem_a_total+=(int)$non_oem_a_qty * (float)$product['non_oem_a_price'];
															$admin_non_oem_b_total+=(int)$non_oem_b_qty * (float)$product['non_oem_b_price'];
															$admin_non_oem_c_total+=(int)$non_oem_c_qty * (float)$product['non_oem_c_price'];
															$admin_non_oem_d_total+=(int)$non_oem_d_qty * (float)$product['non_oem_d_price'];
															$admin_salvage_total+=(int)$salvage_qty * (float)$product['salvage_price'];

															$admin_total = ($oem_a_qty * $product['oem_a_price']) + ($oem_b_qty * $product['oem_b_price']) + ($oem_c_qty * $product['oem_c_price']) + ($oem_d_qty * $product['oem_d_price']) + ($non_oem_a_qty * $product['non_oem_a_price']) + ($non_oem_b_qty * $product['non_oem_b_price']) + ($non_oem_c_qty * $product['non_oem_c_price']) + ($salvage_qty * $product['salvage_price']);

															$admin_combine_total+=(float)$admin_total;

														}
														
	$rowData = array();
	$rowData = array($sales_agent,$customer_name,$row['email'],$city,$state,(float)$row['order_price']);
	
	foreach($months as $month)
	{
		$order_data =  $db->func_query_first("SELECT count(*) as count,sum(order_price) as order_price FROM inv_orders  where month(order_date)='".$month['month']."' and year(order_date)='".$month['year']."' and lower(trim(email))='".$row['email']."'  and lower(order_status) in ('processed','shipped') and store_type not like '%amazon%' ");
		$rowData[] = (int)$order_data['count'].' / '.(float)$order_data['order_price'];
	}

	$rowData[] = (int)$no_of_return_items;

	$rowData[] = (float)$return_total;
	$rowData[] = (float)$admin_combine_total;
	
	// print_r($rowData);exit;
	fputcsv($fp, $rowData);

}
fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);

// echo 1;
?>