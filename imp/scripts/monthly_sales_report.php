<?php
require_once("../config.php");
require_once("../inc/functions.php");

if(!isset($_GET['month']))
{
	$month = date('m');
}
else
{
	$month = $_GET['month'];
}

if(!isset($_GET['year']))
{
	$year = date('Y');
}
else
{
	$year = $_GET['year'];
}



$filename = 'monthly_sales_report_'.$month.$year.'.csv';
$fp = fopen($filename, "w");
$headers = array("Date","Customer Name","Email","Agent", "Source","Business Type","Email Count","Calls Count","Last Ordered","# of Orders","Total Purchased","Parent","Child");
fputcsv($fp, $headers,',');


$rows = $db->func_query("select  email,count(*) as no_of_orders,SUM(sub_total+shipping_amount) as order_price from inv_orders where lower(order_status) in ('processed','shipped','completed','unshipped') and month(order_date)='".$month."' and year(order_date)='".$year."' and store_type in ('web','po_business') group by email");

$check_email = array();
foreach($rows as $row)
{

	if(!in_array($row['email'], $check_email))
	{

	$customer_data = $db->func_query_first("SELECT id,firstname, lastname, email,user_id,last_order,no_of_orders,total_amount,freshsales_contact_data,parent_id FROM inv_customers where trim(lower(email))='".trim(strtolower($row['email']))."' and is_test_account=0");
	$source = $db->func_query_first_cell("SELECT source FROM oc_customer_source where trim(lower(email))='".trim(strtolower($row['email']))."'  and type='hear'");
	$business_type = $db->func_query_first_cell("SELECT source FROM oc_customer_source where trim(lower(email))='".trim(strtolower($row['email']))."'  and type='business_type'");

	// $last_ordered = $db->func_query_first_cell("SELECT order_date from inv_orders where trim(lower(email))='".trim(strtolower($row['email']))."' order by order_date desc limit 1");

	// $total_orders_count = $db->func_query_first_cell("SELEC")


	$contact_details = explode("-", $customer_data['freshsales_contact_data']);

	$email_count = (int)$contact_details[0];
    $calls_count = (int)$contact_details[1]+(int)$contact_details[2];

	
	$parent = '';
	$child = '';
	if($customer_data)
	{
	if($customer_data['parent_id']==0)
	{

		$_temps = $db->func_query("SELECT distinct id,firstname, lastname, email,user_id,last_order,no_of_orders,total_amount,freshsales_contact_data,parent_id from inv_customers where parent_id='".$customer_data['id']."' and is_test_account=0 ");
		
		$parent = '';
		$child = '';
		foreach($_temps as $_temp)
		{
			$check_email[] = $_temp['email'];
			
			$child.=','.$_temp['email'];

			$_customer_detail = $db->func_query_first("select  email,count(*) as no_of_orders,SUM(sub_total+shipping_amount) as order_price from inv_orders where lower(order_status) in ('processed','shipped','completed','unshipped') and month(order_date)='".$month."' and year(order_date)='".$year."' and store_type in ('web','po_business') and email='".$_temp['email']."' ");
			if($_customer_detail)
			{
				$_contact = explode("-", $_customer_detail['freshsales_contact_data']);

				$email_count+= (int)$_contact[0];
    			$calls_count+= (int)$_contact[1]+(int)$_contact[2];
    			$row['no_of_orders'] = $row['no_of_orders'] + $_customer_detail['no_of_orders'];
    			$row['order_price'] = $row['order_price'] + $_customer_detail['order_price'];

    			if(strtotime($_temp['last_order'])<strtotime($customer_data['last_order']))
    			{
    				$customer_data['last_order'] = $_temp['last_order'];
    			}
			}
		}	
	}
	else
	{

		$_temp = $db->func_query_first("SELECT distinct id,firstname, lastname, email,user_id,last_order,no_of_orders,total_amount,freshsales_contact_data,parent_id from inv_customers where id='".$customer_data['parent_id']."' and is_test_account=0");
		if($_temp)
		{
			// echo $customer_data['email'].'-'.$_temp['email'];exit;
		}
		$check_email[] = $_temp['email'];
		$parent = $_temp['email'];
			$child.='';

		$_customer_detail = $db->func_query_first("select  email,count(*) as no_of_orders,SUM(sub_total+shipping_amount) as order_price from inv_orders where lower(order_status) in ('processed','shipped','completed','unshipped') and month(order_date)='".$month."' and year(order_date)='".$year."' and store_type in ('web','po_business') and email='".$_temp['email']."' ");
			if($_customer_detail)
			{
				$_contact = explode("-", $_customer_detail['freshsales_contact_data']);

				$email_count+= (int)$_contact[0];
    			$calls_count+= (int)$_contact[1]+(int)$_contact[2];
    			$row['no_of_orders'] = $row['no_of_orders'] + $_customer_detail['no_of_orders'];
    			$row['order_price'] = $row['order_price'] + $_customer_detail['order_price'];

    			$customer_data['firstname']  = $_temp['firstname'];
    			$customer_data['lastname']  = $_temp['lastname'];
    			// $row['email']  = $_temp['email'];


    			if(strtotime($_temp['last_order'])<strtotime($customer_data['last_order']))
    			{
    				$customer_data['last_order'] = $_temp['last_order'];
    			}
			}



	}
}


    // $revenue = $db->func_query_first_cell("SELECT SUM(sub_total+tax+shipping_amount) from inv_orders where trim(lower(email))='".trim(strtolower($row['email']))."' and month(order_date)='$month' and year(order_date)='$year'");
	$last_order = $db->func_query_first_cell("SELECT order_date from inv_orders where trim(lower(email))='".trim(strtolower($row['email']))."' and lower(order_status) in ('processed',  'shipped',  'completed',  'unshipped') order by order_date desc limit 1");
// $last_order = $customer_data['last_ordered'];
	$rowData = array();
	$rowData = array($month.'-'.$year,$customer_data['firstname'].' '.$customer_data['lastname'],$row['email'],get_username($customer_data['user_id']),$source,$business_type,$email_count,$calls_count,americanDate($last_order),(int)$row['no_of_orders'],(float)round($row['order_price'],2),$parent,ltrim($child,','));
	fputcsv($fp, $rowData,',');		
	}

}

fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);


?>