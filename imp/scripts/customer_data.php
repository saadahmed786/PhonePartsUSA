<?php
ini_set('max_execution_time', 900); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';
$rows = $db->func_query("SELECT * FROM temp_customer WHERE email not like '%@marketplace.amazon%' and is_synced=1 limit 19727,7000
");
$filename = "customer_data-" . date("Y-m-d") . ".csv";
$fp = fopen($filename, "w");
$headers = array("Firstname", "Lastname", "Phone", "Email", "Address1", "City", "State", "Zip", 
			"July # of Orders", "July Total", "July # of Returns",
			"Aug # of Orders", "Aug Total", "Aug # of Returns",
			"Sept # of Orders", "Sept Total", "Sept # of Returns",
			"Oct # of Orders", "Oct Total", "Oct # of Returns",
			"Nov # of Orders", "Nov Total", "Nov # of Returns",
			"Dec # of Orders", "Dec Total", "Dec # of Returns",
			"Jan # of Orders", "Jan Total", "Jan # of Returns",
			"Feb # of Orders", "Feb Total", "Feb # of Returns",
			"Mar # of Orders", "Mar Total", "Mar # of Returns",
			"Apr # of Orders", "Apr Total", "Apr # of Returns"

			);
		fputcsv($fp, $headers,',');
		// $dates = array(
		// 	array('07','2015'),
		// 	array('08','2015'),
		// 	array('09','2015'),
		// 	array('10','2015'),
		// 	array('11','2015'),
		// 	array('12','2015'),
		// 	array('01','2016'),
		// 	array('02','2016'),
		// 	array('03','2016'),
		// 	array('04','2016')
		// 	);
foreach($rows as $row)
	{
		// $july_no = 0; $july_total = 0.00;$july_returns = 0;
		// $aug_no = 0; $aug_total = 0.00;$aug_returns = 0;
		// $sept_no = 0; $sept_total = 0.00;$sept_returns = 0;
		// $oct_no = 0; $oct_total = 0.00;$oct_returns = 0;
		// $nov_no = 0; $nov_total = 0.00;$nov_returns = 0;
		// $dec_no = 0; $dec_total = 0.00;$dec_returns = 0;
		// $jan_no = 0; $jan_total = 0.00;$jan_returns = 0;
		// $feb_no = 0; $feb_total = 0.00;$feb_returns = 0;
		// $mar_no = 0; $mar_total = 0.00;$mar_returns = 0;
		// $apr_no = 0; $apr_total = 0.00;$apr_returns = 0;
		
		$dates = $db->func_query("SELECT DISTINCT `month`,`year`,email,no_of_orders,total as order_price,no_of_returns FROM temp_customer_dt WHERE email='".$row['email']."'");
		
		foreach($dates as $date)
		{
			$month = $date['month'];
			$year = $date['year'];
			// $order_data = $db->func_query_first("SELECT COUNT(*) as no_of_orders,sum(a.order_price) as order_price,sum(a.paid_price) as paid_price,(select count(*) from inv_returns where lower(trim(email))=lower(trim(a.email))  and month(date_added)='$month' and year(date_added)='$year'  ) as no_of_returns from inv_orders a where lower(trim(email))='".$row['email']."' and month(order_date)='".$month."' and year(order_date)='".$year."'");
			//$return_data = $db->func_query_first("select count(*) as no_of_returns from inv_returns where lower(trim(email))='".$row['email']."' and month(date_added)='$month' and year(date_added)='$year'");
			
			switch($month)
			{
				case '07':
					$july_no = $date['no_of_orders'];
					$july_total = $date['order_price'];
					$july_returns = $date['no_of_returns'];
				break;
				case '08':
					$aug_no = $date['no_of_orders'];
					$aug_total = $date['order_price'];
					$aug_returns = $date['no_of_returns'];
				break;
				case '09':
					$sept_no = $date['no_of_orders'];
					$sept_total = $date['order_price'];
					$sept_returns = $date['no_of_returns'];
				break;
				case '10':
					$oct_no = $date['no_of_orders'];
					$oct_total = $date['order_price'];
					$oct_returns = $date['no_of_returns'];
				break;
				case '11':
					$nov_no = $date['no_of_orders'];
					$nov_total = $date['order_price'];
					$nov_returns = $date['no_of_returns'];
				break;
				case '12':
					$dec_no = $date['no_of_orders'];
					$dec_total = $date['order_price'];
					$dec_returns = $date['no_of_returns'];
				break;
				case '01':
					$jan_no = $date['no_of_orders'];
					$jan_total = $date['order_price'];
					$jan_returns = $date['no_of_returns'];
				break;
				case '02':
					$feb_no = $date['no_of_orders'];
					$feb_total = $date['order_price'];
					$feb_returns = $date['no_of_returns'];
				break;
				case '03':
					$mar_no = $date['no_of_orders'];
					$mar_total = $date['order_price'];
					$mar_returns = $date['no_of_returns'];
				break;
				case '04':
					$apr_no = $date['no_of_orders'];
					$apr_total = $date['order_price'];
					$apr_returns = $date['no_of_returns'];
				break;

			}	
		}

		$rowData = array(
			$row['first_name'],
			$row['last_name'],
			$row['phone_number'],
			$row['email'],
			$row['address1'],
			$row['city'],
			$row['state'],
			$row['zip'],
			$july_no,$july_total,$july_returns,
			$aug_no,$aug_total,$aug_returns,
			$sept_no,$sept_total,$sept_returns,
			$oct_no,$oct_total,$oct_returns,
			$nov_no,$nov_total,$nov_returns,
			$dec_no,$dec_total,$dec_returns,
			$jan_no,$jan_total,$jan_returns,
			$feb_no,$feb_total,$feb_returns,
			$mar_no,$mar_total,$mar_returns,
			$apr_no,$apr_total,$apr_returns

			
			);
		 fputcsv($fp, $rowData,',');

	}
	fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile($filename);
@unlink($filename);

?>