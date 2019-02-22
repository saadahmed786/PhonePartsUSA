<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
include_once '../config.php';
include("../phpmailer/class.smtp.php");
include("../phpmailer/class.phpmailer.php");
include_once '../inc/functions.php';

function getMonthsInRange($startDate, $endDate) {
$months = array();

// echo $endDate;exit;
while (strtotime($startDate) <= strtotime($endDate)) {
    $months[] = array('year' => date('Y', strtotime($startDate)), 'month' => date('m', strtotime($startDate)), );
    $startDate = date('d M Y', strtotime($startDate.
        '+ 1 month'));
}
// print_r($months);exit;
return $months;
}

//$month = date('m');
$year = date('Y');


if(isset($_GET['date']))
{
	$dates = explode(",",$_GET['date']);
	$first = $dates[0];
	$second = $dates[1];



	// echo $firs
	// echo 
	// echo $first_date;exit;
	$first_date = date('01-m-Y',strtotime($first));
	$second_date = date('t-m-Y',strtotime($second));

	// echo $second;exit;
	//$first = explode("-", $first_date);
	//$second = explode("-", $second_date);

}
else
{
	echo 'Please provide proper date range';exit;
}
// echo $first_date;exit;
$months = (getMonthsInRange($first_date,$second_date));
$total = 100;
$page = 1;
if(isset($_GET['page']))
{
	$page = (int)$_GET['page'];
}
$limit_start = ($page-1)*$total;

$filename = 'top_ordering_customer-'.$page.'.csv';
$fp = fopen($filename, "w");

$headers2 = array();
foreach($months as $month)
{
	$headers2[] = ($month['month']).'-'.$month['year'];
}

$headers = array("Sales Agent","First & Last Name","Email","City","State","Order Amount");

$headers[] = '# of Return Items';
$headers[] = 'Return Amount';
$headers[] = 'LBB Amount';
$headers = array_merge($headers2,$headers);
// ,"# of Return Items","Return Amount","LBB Amount"
fputcsv($fp, $headers);
// echo "SELECT count(*) as count,lower(trim(email)) as email,sum(sub_total+tax+shipping_amount) as order_price FROM inv_orders  where date(order_date) between '".$first."' and '".$second."'  and lower(order_status) in ('processed','shipped') and store_type not like '%amazon%' GROUP by email having sum(sub_total+tax+shipping_amount)>=500  order by 3 desc limit ".$limit_start.", ".$total;exit;
$_rows = $db->func_query("SELECT count(*) as count,lower(trim(email)) as email,sum(sub_total+tax+shipping_amount) as order_price FROM inv_orders  where date(order_date) between '".$first."' and '".$second."'  and lower(order_status) in ('processed','shipped') and store_type not like '%amazon%' GROUP by email having sum(sub_total+tax+shipping_amount)>=500  order by 3 desc");


$i=0;
$check_email = array();
foreach($_rows as $_row)
{

	if(!in_array($_row, $check_email))
	{
	$check_email[] = $_row['email'];
	$parent_check = $db->func_query_first("SELECT id,email,parent_id from inv_customers where lower(trim(email))='".$_row['email']."'");
	if(!$parent_check['parent_id'])
	{
		$emails = $db->func_query("SELECT lower(trim(email)) as email from inv_customers where parent_id='".(int)$parent_check['id']."' ");

	}
	else
	{
			$parent_id = $db->func_query_first_cell("SELECT id from inv_customers where id='".$parent_check['parent_id']."'");

			$emails = $db->func_query("SELECT lower(trim(email)) as email from inv_customers where parent_id='".(int)$parent_id."' ");
	}
	$consolidated_emails = array();
	$consolidated_emails[] = $_row['email'];

	foreach($emails as $email)
	{
		if($email['email'])
		{
			$check_email[] = $email['email'];
			$consolidated_emails[] = $email['email'];
		}
	}
	$consolidated_emails = array_unique($consolidated_emails);
	$rows[$i]=$_row;
	$rows[$i]['consolidated_emails'] = "'" . implode("','", $consolidated_emails) . "'";
	$i++;
}
}

// echo "<pre>";
// print_r($rows);exit;
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
	

	$no_of_return_items = $db->func_query_first_cell("SELECT COUNT(*) as count FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id and lower(trim(a.email)) in (".$row['consolidated_emails'].") and date(date_added) between '".$first."' and '".$second."' ");
	// $no_of_return_items = $db->func_query_first_cell("SELECT (SELECT COUNT(*) FROM inv_return_items b where a.id=b.return_id) as count FROM inv_returns a WHERE  lower(trim(a.email))='".$row['email']."' and date(a.date_completed) between '".$first."' and '".$second."' ");

	$return_total = $db->func_query_first_cell("select sum(b.price) as price FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id and a.rma_status='Completed' and lower(trim(a.email)) in (".$row['consolidated_emails'].") and date(date_completed) between '".$first."' and '".$second."'  ");

	//$lbb_products = $db->func_query("SELECT (select email from oc_customer c where c.customer_id=a.customer_id) as customer_email,b.* FROM oc_buyback_products b,oc_buyback a  WHERE a.buyback_id=b.buyback_id and a.status='Completed' and date(a.date_completed) between '".$first."' and '".$second."' and (lower((select email from oc_customer c where c.customer_id=a.customer_id)) in (".$row['consolidated_emails'].") OR a.email in (".$row['consolidated_emails'].")) "); 

	$lbb_products = array();

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
	$rowData2 = array();
	$total = 0.00;
	foreach($months as $month)
	{
		$order_data =  $db->func_query_first("SELECT count(*) as count,sum(sub_total+tax+shipping_amount) as order_price FROM inv_orders  where month(order_date)='".$month['month']."' and year(order_date)='".$month['year']."' and lower(trim(email)) in (".$row['consolidated_emails'].")  and lower(order_status) in ('processed','shipped') and store_type not like '%amazon%' ");
		$rowData2[] = (int)$order_data['count'].' / '.(float)$order_data['order_price'];

		$total = (float)$total+(float)$order_data['order_price'];
	}
	$rowData = array($sales_agent,$customer_name,$row['email'],$city,$state,(float)$total);
	
	

	$rowData[] = (int)$no_of_return_items;

	$rowData[] = (float)$return_total;
	$rowData[] = (float)$admin_combine_total;
	
	$rowData = array_merge($rowData2,$rowData);
	// print_r($rowData);exit;
	fputcsv($fp, $rowData);

}
fclose($fp);

// header('Content-type: application/csv');
// header('Content-Disposition: attachment; filename="'.$filename.'"');
// readfile($filename);
// @unlink($filename);

$mail = new PHPMailer();
$mail->IsSMTP();
$mail->CharSet = 'UTF-8';
$mail->Host = MAIL_HOST; // SMTP server example
$mail->SMTPDebug = 0;                     // enables SMTP debug information (for testing)
$mail->SMTPAuth = true;                  // enable SMTP authentication
$mail->Port = 25;                    // set the SMTP port for the GMAIL server
$mail->Username = MAIL_USER; // SMTP account username example
$mail->Password = MAIL_PASSWORD;        // SMTP account password example
$mail->SetFrom(MAIL_USER, 'PhonePartsUSA');
$mail->addAddress('faheemm@phonepartsusa.com', 'Faheem Malok');
$mail->AddCC('mohsin@phonepartsusa.com');

$mail->Subject = ('Top Ordering Customers - PhonePartsUSA');
$mail->Body = 'Top Ordering Customers from '.$first.' to '.$second;
$mail->IsHTML(true);
$mail->addAttachment($filename);
$mail->send();




echo 'Email Sent';
// echo 1;
?>