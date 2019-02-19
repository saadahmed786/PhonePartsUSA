<?php
require_once("auth.php");
require_once("inc/functions.php");
page_permission("competitor_dashboard");
// unset($_SESSION['comp_name']);
$sku_groups = $db->func_query("select sku from inv_product_skus order by sku asc");
function main_dashboard($last_100)
{
	global $db;

	foreach ($last_100 as $key => $data) {

	$price = $db->func_query_first("select *, (SELECT price from inv_product_price_scrap_history where sku = ph.sku and type = ph.type order by added desc limit 1, 1) as old_price from inv_product_price_scrap_history ph where sku = '" . $data['sku'] . "' AND type = '".$data['type']."' order by added DESC limit 1");
	$change = number_format($price['price'] / $price['old_price'] * 100, 2);
				if ($change < 100.00 && $change > 0.00) {
					$change = '-' . (100 - $change);
				} else if ($change == 0.00) {
					$change = 100 - $change;

				} else {
					$change = '+' . ($change - 100);
				}
				if((float)$price['old_price']==0.00)
				{
					$change = 0.00;
				}
	$ppusa_price = getOCItemPrice($db->func_query_first_cell("SELECT product_id FROM oc_product WHERE model='".$data['sku']."'"));
	$perc_diff = number_format(($ppusa_price - $price['price'])/ $price['price'] * 100, 2);
	if ($perc_diff>0) {
		$perc_diff = '+'.$perc_diff;
	}
	
	$last_100[$key]['price'] =$price['price'];
	$last_100[$key]['old_price'] =$price['old_price'];
	$last_100[$key]['change'] =$change;
	$last_100[$key]['our_price'] = $ppusa_price;
	$last_100[$key]['perc_diff'] = $perc_diff;			
}
return $last_100;
}
$scrapping_sites = array('mobile_sentrix', 'fixez', 'mengtor', 'mobile_defenders','etrade_supply','maya_cellular','lcd_loop','parts_4_cells','cell_parts_hub');
if ($_POST['update_prices']) {
	
	if ($_POST['items']) {
		$price_csv_header  = array();
		$price_csv_header[] = 'Date Updated';
		$price_csv_header[] = 'User';
		$price_csv_header[] = 'SKU';
		$price_csv_header[] = 'Item Name';
		$price_csv_header[] = 'Price Type';
		$price_csv_header[] = 'Old Price';
		$price_csv_header[] = 'New Price';
		$filename = "price_report/PriceUpdateReport-".date("Y-m-d").".csv";
		if (!file_exists($_SERVER['DOCUMENT_ROOT']."/imp/price_report/PriceUpdateReport-".date("Y-m-d").".csv")) {

			$file = fopen($filename,"w");
			fputcsv($file , $price_csv_header,',');
		} else {
			$file = fopen($filename,"a");
		}
		foreach ($_POST['items'] as $product_id) {

			$sku = $db->func_escape_string($_POST['sku'][$product_id]);
			$title = $db->func_escape_string($_POST['title'][$product_id]);
			$our_price = $db->func_escape_string($_POST['our_price'][$product_id]);
			$expected_price = $db->func_escape_string($_POST['expected_price'][$product_id]);

			$db->func_query("UPDATE oc_product set sale_price = '".$expected_price."' WHERE product_id = '".$product_id."'");

			$date = date('Y-m-d H:i:s');

			$rowData = array($date,get_username($_SESSION['user_id']),$sku,$title,'Sale',$our_price,$expected_price);
			fputcsv($file , $rowData,',');
		}
		fclose($file);
		$_SESSION['message'] = 'Products Sale Price Updated successfully.';
		header("Location:competitor_dashboard.php");
		exit;
	} else {
		$_SESSION['message'] = 'Please Select Products to Update.';
		header("Location:competitor_dashboard.php");
		exit;
	}
}

if ($_POST['update_wrong_link']) {
	
	if ($_POST['items']) {
		
		foreach ($_POST['items'] as $product_id) {

		$sku = $db->func_query_first_cell("SELECT TRIM(LOWER(model)) FROM oc_product WHERE product_id='".(int)$product_id."'");

			$db->db_exec("UPDATE inv_product_price_scrap SET http_code='999' WHERE TRIM(LOWER(sku))='".$sku."' AND type='".$_SESSION['comp_name']."'");

		
		}
	}
		
		$_SESSION['message'] = 'Link(s) Updated successfully.';
		header("Location:competitor_dashboard.php");
		exit;

}

if(isset($_GET['export_csv']))
{
	
	if(!isset($_GET['competitor']))
	{
		$csv_filename = "files/Tier_".$_GET['tier_type']."_Report-".date("Y-m-d").".csv";

		$header  = array();
	$header[] = 'Last Fetch';
	$header[] = 'Tier';
	$header[] = 'SKU';
	$header[] = 'Item Name';
	$header[] = 'Cost';
	$header[] = 'Our Price';
	$header[] = 'Avg Price';
	$header[] = 'Lowest Price';
	$header[] = 'URL';
	$header[] = '% Difference';
	}
	else
	{

		$csv_filename = "files/".$_GET['competitor']."_CSV_Export_Report-".date("Y-m-d").".csv";

		$header  = array();
	$header[] = 'Last Fetch';
	$header[] = 'Tier';
	$header[] = 'SKU';
	$header[] = 'Item Name';
	$header[] = 'Cost';
	$header[] = 'Our Price';
	$header[] = 'Old Price';
	$header[] = 'New Price';
	$header[] = 'URL';
	$header[] = '% Change';
	$header[] = '% Difference';
	}

	$tier_file = fopen($csv_filename,"w");
	fputcsv($tier_file , $header,',');

	

	$hide_zero_diff = $_GET['hide_zero_diff'];
	$avg_chk = $_GET['avg_chk'];
	$low_chk = $_GET['low_chk'];
	$avg_cost_chk = $_GET['avg_cost_chk'];
	$low_cost_chk = $_GET['low_cost_chk'];

	$avg_select = $_GET['avg_select'];
	$low_select = $_GET['low_select'];
	$avg_cost_select = $_GET['avg_cost_select'];
	$low_cost_select = $_GET['low_cost_select'];
	
	$tier_type = $_GET['tier_type'];
	$sku_group = $_GET['sku_group'];

	$where_query_filter = array();


	$contains_chk = $_GET['contains_chk'];
	$contains_select = $_GET['contains_select'];
	$contains_field = $_GET['contains_field'];


	// echo $hide_zero_diff;exit;


	if($avg_chk=='true')
	{
	switch($avg_select)
	{
		case 'avg_less_price':
			$where_query_filter[] = "avg_price<our_price";
		break;

		case 'avg_above_price':
			$where_query_filter[] = "avg_price>our_price";
		break;

		

		default:
			$where_query_filter[]='';
		break;
	}
}
// var_dump($low_chk);exit;
	if($low_chk=='true')
	{

	switch($low_select)
	{
		case 'low_less_price':
			$where_query_filter[] = "min_price<our_price ";
		break;

		case 'low_above_price':
			$where_query_filter[] = "min_price>our_price ";
		break;
		default:
		$where_query_filter[]='';
		break;
	}
}

if($avg_cost_chk=='true')
	{

	switch($avg_cost_select)
	{
		case 'avg_less_cost':
			$where_query_filter[] = "avg_price<true_cost ";
		break;

		case 'avg_above_cost':
			$where_query_filter[] = "avg_price>true_cost ";
		break;
		default:
		$where_query_filter[]='';
		break;
	}
}


if($low_cost_chk=='true')
	{

	switch($low_cost_select)
	{
		case 'low_less_cost':
			$where_query_filter[] = "min_price<true_cost ";
		break;

		case 'low_above_cost':
			$where_query_filter[] = "min_price>true_cost ";
		break;
		default:
		$where_query_filter[]='';
		break;
	}
}

if($contains_chk=='true')
	{

	switch($contains_select)
	{
		case 'contains_less_field':
			$where_query_filter[] = "COUNT(url)<'".(int)$contains_field."'";
		break;

		case 'contains_above_field':
			$where_query_filter[] = "COUNT(url)>'".(int)$contains_field."'";
		break;
		default:
		$where_query_filter[]='';
		break;
	}
}


if($hide_zero_diff=='true')
{
	$where_query_filter[] = '((our_price-MIN(s.recent_price))/MIN(s.recent_price)*100)<>0.00';
}

	if($where_query_filter)
	{
		$where_query_filter = ' HAVING '.implode(" AND ", $where_query_filter);
	}
	else
	{
		$where_query_filter = ' ';
	}



	$query = "SELECT s.id, 
       s.sku, 
       p.tier, 
       s.date_updated, 
       s.type, 
       s.url,
       AVG(s.recent_price) AS avg_price, 
       MIN(s.recent_price) AS min_price, 
       (SELECT price 
        FROM   inv_product_price_scrap_history b 
        WHERE  b.sku = s.sku ".(isset($_GET['competitor'])?" AND b.type='".$_GET['competitor']."'":'')."
        ORDER  BY b.added DESC 
        LIMIT  1)          AS new_price, 
       (SELECT price 
        FROM   inv_product_price_scrap_history c 
        WHERE  c.sku = s.sku ".(isset($_GET['competitor'])?" AND c.type='".$_GET['competitor']."'":'')."
        ORDER  BY c.added DESC 
        LIMIT  1, 1)       AS old_price, 
        (
        	select ((raw_cost+shipping_fee)/ex_rate)+refurb_cost from inv_product_costs where sku=s.sku order by id desc limit 1

        ) as true_cost,
       (
       CASE 
         WHEN (SELECT sale_price 
               FROM   oc_product d 
               WHERE  d.model = s.sku 
               LIMIT  1) > 0.00 THEN (SELECT d.sale_price 
                                   FROM   oc_product d 
                                   WHERE  d.model = s.sku 
                                   LIMIT  1) 
          
         ELSE (SELECT price 
               FROM   oc_product d 
               WHERE  d.model = s.sku 
               LIMIT  1) 
       end
       ) AS our_price 
FROM   inv_product_price_scrap s 
       INNER JOIN oc_product p 
               ON ( s.sku = p.sku )  WHERE p.status<>0 ";

               if($_GET['tier_type']!=0)
               {
                $query.=" AND p.tier = '".(int)$_GET['tier_type']."'"; 
               }

               if(isset($_GET['competitor']))
               {
               	$query.=" AND s.type='".$_GET['competitor']."' ";
               }

               
      if($sku_group!='' and $sku_group!='undefined')
      {
      	$query.=" AND LEFT(s.sku,".strlen($sku_group).")='".$sku_group."'";
      }

               $query.=" AND s.url <> '' AND s.type <> '' AND s.http_code='200'   group by s.sku $where_query_filter   order by s.date_updated desc"; 
               
$csv_skus = $db->func_query($query);
	foreach($csv_skus as $data)
	{

		$price['price'] = $data['new_price'];
    	$price['old_price'] = $data['old_price'];
    	
    	$change = number_format($price['price'] / $price['old_price'] * 100, 2);
    	if ($change < 100.00 && $change > 0.00) {
    		$change = '-' . (100 - $change);
    	} else if ($change == 0.00) {
    		$change = 100 - $change;
    	} else {
    		$change = '+' . ($change - 100);
    	}
    	if((float)$price['old_price']==0.00)
    	{
    		$change = 0.00;
    	}

    	$lowest_price = $data['min_price'];
    	$avg_price = $data['avg_price'];
    	$lowest_competitor_url = $db->func_query_first_cell("SELECT url FROM inv_product_price_scrap WHERE recent_price='".$lowest_price."' AND sku  = '".$data['sku']."'");

    	// $product_id = $db->func_query_first_cell("SELECT product_id FROM oc_product WHERE model='".$data['sku']."'");
    	// $ppusa_price = getOCItemPrice($product_id);
    	$ppusa_price = $data['our_price'];
    	$perc_diff = number_format(($ppusa_price - $price['price'])/ $price['price'] * 100, 2);
    	if ($perc_diff>0) {
    		$perc_diff = '+'.$perc_diff;
    	}
    	
    		$perc_diff = '';
    		$perc_diff = number_format(($ppusa_price - $lowest_price)/ $lowest_price * 100, 2);
    		if ($perc_diff>0) {
    			$perc_diff = '+'.$perc_diff;
    		}
    		if ($lowest_price == '99999') {
    			$lowest_price_link = '$0.00';
    			$lowest_price == '0';
    		} else {
    			$lowest_price_link = '<a href="'.$lowest_competitor_url.'" target="_blank">$'.$lowest_price.'</a>';
    		}
    	
    		if(isset($_GET['competitor']))
    		{
    			$rowData = array($data['date_updated'],$data['tier'],$data['sku'],getItemName($data['sku']),number_format($ppusa_price,2),number_format($data['old_price'],2),number_format($data['new_price'],2),$data['url'],$change.'%',$perc_diff.'%');

    		}
    		else
    		{
		$rowData = array($data['date_updated'],$data['tier'],$data['sku'],getItemName($data['sku']),number_format($data['true_cost'],2),number_format($ppusa_price,2),number_format($data['avg_price'],2),number_format($data['min_price'],2),$lowest_competitor_url,$perc_diff.'%');
    			
    		}

    	
    		fputcsv($tier_file , $rowData,',');
    	


	}
fclose($tier_file);
    header('Content-type: application/csv');
    header('Content-Disposition: attachment; filename="'.$csv_filename.'"');
    readfile($csv_filename);
    @unlink($csv_filename);
    exit;
	



}


if (isset($_GET['get_logs'])) {
	$start = $db->func_escape_string(trim($_GET['start']));
	$end = $db->func_escape_string(trim($_GET['end']));
	$json = array();
	$logs = $db->func_query("SELECT * from inv_scrapper_log where DATE(date_added) >= '$start' AND DATE(date_added) <= '$end' order by date_added desc");
	foreach ($logs as $key => $log) {
		$json['logs'][$key]['datetime'] = americanDate($log['date_added']);
		$json['logs'][$key]['competitor'] = $log['competitor'];
		$json['logs'][$key]['sku'] = linkToProduct($log['sku'],"","target='_blank'");
		if (strpos($log['comment'],'</a>')) {
			$json['logs'][$key]['comment'] = $log['comment'];
		} else {
			$url = $db->func_query_first_cell("SELECT url from inv_product_price_scrap where sku = '".$log['sku']."' AND type = '".$log['competitor']."'");
			$json['logs'][$key]['comment'] = '<a href="'.$url.'" target="_blank">'.$url.'</a>';
		}
		$json['logs'][$key]['user'] = get_username($log['user_id']);
	}
	if ($logs) {
		$json['success'] = 1;
	} else {
		$json['error'] = 1;
	}
	echo json_encode($json);
	exit;
}
if (isset($_GET['get_new_urls'])) {
	$start = $db->func_escape_string(trim($_GET['start']));
	$end = $db->func_escape_string(trim($_GET['end']));
	$json = array();
	$new_links = $db->func_query("SELECT s.* , p.tier FROM inv_product_price_scrap s inner join oc_product p ON (s.sku = p.sku) WHERE s.url <> '' AND s.is_new = '1' AND DATE(s.date_updated) >= '$start' AND DATE(s.date_updated) <= '$end' order by s.date_updated desc");
	foreach ($new_links as $key => $data) {
	$price = $db->func_query_first("select *, (SELECT price from inv_product_price_scrap_history where sku = ph.sku and type = ph.type order by added desc limit 1, 1) as old_price from inv_product_price_scrap_history ph where sku = '" . $data['sku'] . "' AND type = '".$data['type']."' order by added DESC limit 1");
	$change = number_format($price['price'] / $price['old_price'] * 100, 2);
				if ($change < 100.00 && $change > 0.00) {
					$change = '-' . (100 - $change);
				} else if ($change == 0.00) {
					$change = 100 - $change;
				} else {
					$change = '+' . ($change - 100);
				}
				if((float)$price['old_price']==0.00)
				{
					$change = 0.00;
				}
	$ppusa_price = getOCItemPrice($db->func_query_first_cell("SELECT product_id FROM oc_product WHERE model='".$data['sku']."'"));
	$perc_diff = number_format(($ppusa_price - $price['price'])/ $price['price'] * 100, 2);
	if ($perc_diff>0) {
		$perc_diff = '+'.$perc_diff;
	}
	  $json['urls'][$key]['last_fetch'] = americanDate($data['date_updated']);
  	  $json['urls'][$key]['tier'] = $data['tier'];
  	  $json['urls'][$key]['sku'] = linkToProduct($data['sku'],'','target="_blank"');
  	  $json['urls'][$key]['title'] = getItemName($data['sku']);
  	  $json['urls'][$key]['competitor'] = $data['type'];
 	  $json['urls'][$key]['our_price'] = number_format($ppusa_price,2);
 	  $json['urls'][$key]['old_price'] =	number_format($price['old_price'],2);
  	  $json['urls'][$key]['new_price'] =	number_format($price['price'],2);
  	  $json['urls'][$key]['perc_change'] =	number_format($change,2);
  	  $json['urls'][$key]['perc_diff'] =	number_format($perc_diff,2);		
}
	if ($new_links) {
		$json['success'] = 1;
	} else {
		$json['error'] = 1;
	}
	echo json_encode($json);
	exit;
}
if(isset($_GET['need_links']))
{
	$_SESSION['comp_name'] = 'need_links';
	$page = (int)$_GET['page'];
	$sort_by = $_GET['sort_by'];
	$sort_order = $_GET['sort_order'];

	$start = (int)($page-1)*50;
	$end = 50;

	$query = "SELECT  a.sku,b.date_added,b.price,b.sale_price,b.tier,(select c.name from oc_product_description c where b.product_id=c.product_id) as name FROM inv_product_price_scrap a,oc_product b WHERE a.sku=b.model and b.is_kit=0 and b.is_blowout=0 and b.is_main_sku=1 and b.status<>0 AND LEFT(b.model,4) in ('APL-','BTY-','FLX-','SRN-','TAB-') AND a.url='' GROUP BY a.sku HAVING COUNT(a.url)=9 order by $sort_by $sort_order "; // HAVING ALL THE LINKS EMPTY
	// echo $query;exit;
	$rows = $db->func_query($query." limit $start,$end ");

	$data = array();
	foreach($rows as $key=> $row)
	{
		$data[$key]['sku'] = linkToProduct($row['sku'],$host_path,'target="_blank"');
		$data[$key]['name'] = utf8_decode($row['name']);
		// $data[$key]['name'] = '';
		$data[$key]['tier'] = $row['tier'];
		$data[$key]['date_added'] = americanDate($row['date_added']);
		$data[$key]['price'] = '$'.number_format($row['price'],2);
		$data[$key]['sale_price'] = '$'.number_format($row['sale_price'],2);
	}
	
	$count_skus = $db->func_query($query);

	$count_skus = count($count_skus);

	$json = array();
	$json['data'] = $data;
	$pages = ceil($count_skus/$end);
    // echo $pages;exit;

   
    for($i=1;$i<=$pages;$i++)
    {
    	$href_start='<strong>';
    	$href_end='</strong>';
    	if($i!=$page)
    	{
    		$href_start = '<a href="javascript:void(0)" class="pagination_link2"  data-page="'.$i.'" data-sort_order="'.$sort_order.'" data-sort_by="'.$sort_by.'">';
    		$href_end = '</a>';
    	}

    	$footer_data.=$href_start.$i.$href_end.' | ';
    }
    $footer_data.='<br>(Showing '.$page.' of '.$pages.' Pages) / '.$count_skus;
    // $footer_data = rtrim($footer_data,"|");
    $json['footer_data'] = $footer_data;
    $json['query'] = $query;
    if($rows)
    {
    	$json['success'] =1;
    }
    else
    {
    	$json['error'] = 1;
    }

	echo json_encode($json);
	exit;
}
if (isset($_GET['comp_name'])) {
	$competitor = $_GET['comp_name'];
	$last_item_date = $_GET['last_item_date'];
	$_SESSION['comp_name'] = $competitor;
	$page = (int)$_GET['page'];
	$start = (int)($page-1)*50;
	$end = 50;

	$sort_by = $_GET['sort_by'];
	$sort_order = $_GET['sort_order'];

	$hide_zero_diff = $_GET['hide_zero_diff'];
	$avg_chk = $_GET['avg_chk'];
	$low_chk = $_GET['low_chk'];
	$avg_cost_chk = $_GET['avg_cost_chk'];
	$low_cost_chk = $_GET['low_cost_chk'];

	$avg_select = $_GET['avg_select'];
	$low_select = $_GET['low_select'];
	$avg_cost_select = $_GET['avg_cost_select'];
	$low_cost_select = $_GET['low_cost_select'];



	$tier_val = $_GET['tier_val'];
	$sku_group = $_GET['sku_group'];


	$contains_chk = $_GET['contains_chk'];
	$contains_select = $_GET['contains_select'];
	$contains_field = $_GET['contains_field'];



	$where_query_filter = array();


	if($avg_chk=='true')
	{
	switch($avg_select)
	{
		case 'avg_less_price':
			$where_query_filter[] = "avg_price<our_price";
		break;

		case 'avg_above_price':
			$where_query_filter[] = "avg_price>our_price";
		break;

		

		default:
			$where_query_filter[]='';
		break;
	}
}
// var_dump($low_chk);exit;
	if($low_chk=='true')
	{

	switch($low_select)
	{
		case 'low_less_price':
			$where_query_filter[] = "min_price<our_price ";
		break;

		case 'low_above_price':
			$where_query_filter[] = "min_price>our_price ";
		break;
		default:
		$where_query_filter[]='';
		break;
	}
}

if($avg_cost_chk=='true')
	{

	switch($avg_cost_select)
	{
		case 'avg_less_cost':
			$where_query_filter[] = "avg_price<true_cost ";
		break;

		case 'avg_above_cost':
			$where_query_filter[] = "avg_price>true_cost ";
		break;
		default:
		$where_query_filter[]='';
		break;
	}
}


if($low_cost_chk=='true')
	{

	switch($low_cost_select)
	{
		case 'low_less_cost':
			$where_query_filter[] = "min_price<true_cost ";
		break;

		case 'low_above_cost':
			$where_query_filter[] = "min_price>true_cost ";
		break;
		default:
		$where_query_filter[]='';
		break;
	}
}
	

	if($contains_chk=='true')
	{

	switch($contains_select)
	{
		case 'contains_less_field':
			$where_query_filter[] = "COUNT(url)<'".(int)$contains_field."'";
		break;

		case 'contains_above_field':
			$where_query_filter[] = "COUNT(url)>'".(int)$contains_field."'";
		break;
		default:
		$where_query_filter[]='';
		break;
	}
}


if($hide_zero_diff=='true')
{
	$where_query_filter[] = '((our_price-MIN(s.recent_price))/MIN(s.recent_price)*100)<>0.00';
}

	if($where_query_filter)
	{
		$where_query_filter = ' HAVING '.implode(" AND ", $where_query_filter);
	}
	else
	{
		$where_query_filter = ' ';
	}


	if ($last_item_date == '0') {
		$last_item_date = $db->func_query_first_cell("SELECT date_updated from inv_product_price_scrap order by date_updated desc");
		$op = '<=';
	} else {
		$op = '<';
	}
	if ($competitor == 'tier_1' || $competitor == 'tier_2' || $competitor == 'tier_3') {
		if ($competitor == 'tier_1') {
			$competitor = '1';
		} else if ($competitor == 'tier_2') {
			$competitor = '2';
		} else {
			$competitor = '3';
		}

		$query = "SELECT s.id, 
		p.product_id,
       s.sku, 
       p.tier, 
       s.date_updated, 
       s.type, 
       s.url,
       AVG(s.recent_price) AS avg_price, 
       MIN(s.recent_price) AS min_price, 
       (SELECT price 
        FROM   inv_product_price_scrap_history b 
        WHERE  b.sku = s.sku 
        ORDER  BY b.added DESC 
        LIMIT  1)          AS new_price, 
       (SELECT price 
        FROM   inv_product_price_scrap_history c 
        WHERE  c.sku = s.sku 
        ORDER  BY c.added DESC 
        LIMIT  1, 1)       AS old_price,

        (
        	select ((raw_cost+shipping_fee)/ex_rate)+refurb_cost from inv_product_costs where sku=s.sku order by id desc limit 1

        ) as true_cost,
       (
       CASE 
         WHEN (SELECT sale_price 
               FROM   oc_product d 
               WHERE  d.model = s.sku 
               LIMIT  1) > 0.00 THEN (SELECT d.sale_price 
                                   FROM   oc_product d 
                                   WHERE  d.model = s.sku 
                                   LIMIT  1) 
          
         ELSE (SELECT price 
               FROM   oc_product d 
               WHERE  d.model = s.sku 
               LIMIT  1) 
       end
       ) AS our_price 
FROM   inv_product_price_scrap s 
       INNER JOIN oc_product p 
               ON ( s.sku = p.sku )  WHERE p.status<>0 and  p.tier = '".$competitor."' AND s.url <> '' AND s.type <> '' AND s.http_code='200'  group by s.sku $where_query_filter   order by $sort_by $sort_order ";
               // echo $query;exit;

               $__c = md5(http_build_query($_GET));

               $skus = $cache->get('competitor.competitor_data.'.$__c);
               if(!$skus)
               {

					$skus = $db->func_query($query." limit $start,$end");
               		$cache->set('competitor.competitor_data.'.$__c,$skus);
               }



		$count_skus = $db->func_query($query);

		$count_skus = count($count_skus);
		$days_30 = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and s.url <> '' AND p.tier = '".$competitor."' AND s.date_updated BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() order by s.date_updated desc");
		$days_7 = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> '' AND p.tier = '".$competitor."' AND s.date_updated BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() order by s.date_updated desc");
		$hrs_24 = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> '' AND p.tier = '".$competitor."' AND s.date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE() order by s.date_updated desc");
		$total = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE  p.status<>0 and s.url <> '' AND p.tier = '".$competitor."' order by s.date_updated desc");
		$diff = $total - $days_7;



	} else if($competitor == 'all'){
		$skus = $db->func_query("SELECT s.id ,s.sku , p.tier , s.date_updated , s.type FROM inv_product_price_scrap s inner join oc_product p on (s.sku = p.sku) WHERE (p.tier = '1' OR p.tier = '2' OR p.tier = '3') AND s.url <> '' AND s.type <> '' AND s.date_updated $op '".$last_item_date."' order by s.date_updated desc limit 250");
		$days_30 = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE  s.url <> '' AND (p.tier = '1' OR p.tier = '2' OR p.tier = '3') AND s.date_updated BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() order by s.date_updated desc");
		$days_7 = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE  s.url <> '' AND (p.tier = '1' OR p.tier = '2' OR p.tier = '3') AND s.date_updated BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() order by s.date_updated desc");
		$hrs_24 = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE  s.url <> '' AND (p.tier = '1' OR p.tier = '2' OR p.tier = '3') AND s.date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE() order by s.date_updated desc");
		$total = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE  s.url <> '' AND (p.tier = '1' OR p.tier = '2' OR p.tier = '3') order by s.date_updated desc");
		$diff = $total - $days_7;
	} else {
		$where_query_filter = array();
		if($hide_zero_diff=='true')
{
	$where_query_filter[] = '((our_price-MIN(s.recent_price))/MIN(s.recent_price)*100)<>0';
}
if($where_query_filter)
{
	$where_query_filter = 'HAVING '.implode(" AND ", $where_query_filter);
}
else
{
	$where_query_filter='';
}
		
		$query = "SELECT 
		p.product_id,
		s.id, 
       s.sku, 
       p.tier, 
       s.date_updated, 
       s.type, 
       s.url,
       AVG(s.recent_price) AS avg_price, 
       MIN(s.recent_price) AS min_price, 
       

        (
        	select ((raw_cost+shipping_fee)/ex_rate)+refurb_cost from inv_product_costs where sku=s.sku order by id desc limit 1

        ) as true_cost,

       (SELECT price 
        FROM   inv_product_price_scrap_history b 
        WHERE  b.sku = s.sku and b.type='".$competitor."' 
        ORDER  BY b.added DESC 
        LIMIT  1)          AS new_price, 
       (SELECT price 
        FROM   inv_product_price_scrap_history c 
        WHERE  c.sku = s.sku  and c.type='".$competitor."' 
        ORDER  BY c.added DESC 
        LIMIT  1, 1)       AS old_price, 
       (
       CASE 
         WHEN (SELECT sale_price 
               FROM   oc_product d 
               WHERE  d.model = s.sku 
               LIMIT  1) > 0.00 THEN (SELECT d.sale_price 
                                   FROM   oc_product d 
                                   WHERE  d.model = s.sku 
                                   LIMIT  1) 
          
         ELSE (SELECT price 
               FROM   oc_product d 
               WHERE  d.model = s.sku 
               LIMIT  1) 
       end
       ) AS our_price 
FROM   inv_product_price_scrap s 
       INNER JOIN oc_product p 
               ON ( s.sku = p.sku ) 
WHERE  s.type = '".$competitor."' 
       AND s.url <> '' and p.status<>0
       AND s.type <> '' AND s.http_code='200' ";
      if($tier_val!='' and $tier_val!='undefined')
      {
      	$query.=" AND p.tier='".(int)$tier_val."'";
      }
      if($sku_group!='' and $sku_group!='undefined')
      {
      	$query.=" AND LEFT(s.sku,".strlen($sku_group).")='".$sku_group."'";
      }

$query.=" GROUP  BY s.sku $where_query_filter   order by $sort_by $sort_order ";
// echo $query;exit;

			
			$__c = md5(http_build_query($_GET));

               $skus = $cache->get('competitor.competitor_data.'.$__c);
               if(!$skus)
               {

					$skus = $db->func_query($query." limit $start,$end");
               		$cache->set('competitor.competitor_data.'.$__c,$skus);
               }

               
    	$count_skus =  $db->func_query($query);
    	$count_skus = count($count_skus);
    	$days_30 = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> ''  AND (p.tier = '1' OR p.tier='2' OR p.tier = '3') AND s.type = '".$competitor."' AND  s.date_updated BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() order by date_updated desc");
    	$days_7 = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> ''  AND (p.tier = '1' OR p.tier='2' OR p.tier = '3') AND s.type = '".$competitor."' AND  s.date_updated BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() order by date_updated desc");
    	$hrs_24 = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> ''  AND (p.tier = '1' OR p.tier='2' OR p.tier = '3') AND s.type = '".$competitor."' AND  s.date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE() order by date_updated desc");
    	$total = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and s.url <> ''  AND (p.tier = '1' OR p.tier='2' OR p.tier = '3') AND s.type = '".$competitor."' order by date_updated desc");
    	$diff = $total - $days_7;
	}
    $json = array();
    foreach ($skus as $key => $data) { 
    	if ($competitor == '1' || $competitor == '2' || $competitor == '3' || $competitor == 'all') {
    		$lowest_price  = 99999;
    		$lowest_competitor_url = '';
    		$avg_price = 0;
    		$avg_counter = 0;
    		/*foreach ($scrapping_sites as $site) {
    			$check = $db->func_query_first("SELECT * FROM inv_product_price_scrap WHERE sku='" . $data['sku'] . "' AND `type`='" . $site . "'");
    			$price = $db->func_query_first("select *, (SELECT price from inv_product_price_scrap_history where sku = ph.sku and type = ph.type order by added desc limit 1, 1) as old_price from inv_product_price_scrap_history ph where sku = '" . $data['sku'] . "' AND type = '$site' order by added DESC limit 1");
    			if ($price['price'] && $price['price']<$lowest_price && $price['price'] != 0.00) {
    				$lowest_price = $price['price'];
    				$lowest_competitor_url = $check['url'];
    			}
    			if ($price['price'] && $price['price'] != '0.00') {
    				$avg_price = $avg_price + $price['price'];
    				$avg_counter++;
    			}
    		}*/
    	}
    	

    	// $price = $db->func_query_first("select *, (SELECT price from inv_product_price_scrap_history where sku = ph.sku and type = ph.type order by added desc limit 1, 1) as old_price from inv_product_price_scrap_history ph where sku = '" . $data['sku'] . "' AND type = '".$data['type']."' order by added DESC limit 1");

    	$price['price'] = $data['new_price'];
    	$price['old_price'] = $data['old_price'];
    	
    	$change = number_format($price['price'] / $price['old_price'] * 100, 2);
    	if ($change < 100.00 && $change > 0.00) {
    		$change = '-' . (100 - $change);
    	} else if ($change == 0.00) {
    		$change = 100 - $change;
    	} else {
    		$change = '+' . ($change - 100);
    	}
    	if((float)$price['old_price']==0.00)
    	{
    		$change = 0.00;
    	}

    	$lowest_price = $data['min_price'];
    	$avg_price = $data['avg_price'];
    	$lowest_competitor_url = $db->func_query_first_cell("SELECT url FROM inv_product_price_scrap WHERE recent_price='".$lowest_price."' AND sku  = '".$data['sku']."'");

    	// $product_id = $db->func_query_first_cell("SELECT product_id FROM oc_product WHERE model='".$data['sku']."'");
    	// $ppusa_price = getOCItemPrice($product_id);
    	$ppusa_price = $data['our_price'];
    	$perc_diff = number_format(($ppusa_price - $price['price'])/ $price['price'] * 100, 2);
    	if ($perc_diff>0) {
    		$perc_diff = '+'.$perc_diff;
    	}
    	if ($competitor == '1' || $competitor == '2' || $competitor == '3' || $competitor == 'all') {
    		$perc_diff = '';
    		$perc_diff = number_format(($ppusa_price - $lowest_price)/ $lowest_price * 100, 2);
    		if ($perc_diff>0) {
    			$perc_diff = '+'.$perc_diff;
    		}
    		if ($lowest_price == '99999') {
    			$lowest_price_link = '$0.00';
    			$lowest_price == '0';
    		} else {
    			$lowest_price_link = '<a href="'.$lowest_competitor_url.'" target="_blank">$'.$lowest_price.'</a>';
    		}
    	}       
   	  $json['competitor_data'][$key]['last_fetch'] = americanDate($data['date_updated']);
  	  $json['competitor_data'][$key]['tier'] = $data['tier'];
  	  $json['competitor_data'][$key]['product_id'] = $data['product_id'];
  	  $json['competitor_data'][$key]['sku'] = linkToProduct($data['sku'],'','target="_blank"');
  	  $json['competitor_data'][$key]['simple_sku'] = $data['sku'];
  	  $json['competitor_data'][$key]['title'] = getItemName($data['sku']);
 	  $json['competitor_data'][$key]['our_price'] = number_format($ppusa_price,2);
 	  $json['competitor_data'][$key]['true_cost'] = '$'.number_format($data['true_cost'],2);
 	  $json['competitor_data'][$key]['old_price'] =	number_format($price['old_price'],2);
  	  $json['competitor_data'][$key]['new_price'] =	number_format($price['price'],2);
  	  $json['competitor_data'][$key]['url'] =	$data['url'];

  	  if ($competitor == '1' || $competitor == '2' || $competitor == '3' || $competitor == 'all') {
  	  	$exp = number_format($lowest_price,2);
  	  } else {
  	  	$exp = number_format($price['price'],2);
  	  }
  	  
  	  $exp_arr  = explode('.', $exp);
  	  $last  = $exp_arr[1];

  	  if ((int)$last == 99) {
  	  	$expected_number  = $exp;
  	  } 
  	   
  	  if ( (int)$last[1] >= 0 && (int)$last[1] <= 4){
  	  	$expected_number  = (int)$exp_arr[0] .'.'.((int)$last[0] - 1) .'9';
  	  }
  	  if ((int)$last[1] > 4){
  	  	$expected_number  = (int)$exp_arr[0] .'.'.((int)$last[0]) .'9';
  	  } 
  	  if ( (int)$last[0] == 0 ){
  	  	$expected_number  = ((int)$exp_arr[0] - 1) .'.99';
  	  }
  	  if ($exp == '0.00') {
  	  	$expected_number = '0.00';
  	  }
  	  $json['competitor_data'][$key]['expected_price'] = $expected_number;
  	  $json['competitor_data'][$key]['perc_change'] = $change;
 	  $json['competitor_data'][$key]['perc_diff'] = $perc_diff;
 	  $json['competitor_data'][$key]['price_trend'] = 'N/A';
 	  if ($competitor == '1' || $competitor == '2' || $competitor == '3' || $competitor == 'all') {
 	  	$json['competitor_data'][$key]['lowest_price'] = $lowest_price_link;
 	  	$json['competitor_data'][$key]['avg_price'] =number_format($avg_price,2);
 	  }

 	  $last_item_date = $data['date_updated'];
    }
    $json['days_30'] = $days_30;
    $json['days_7'] = $days_7;
    $json['hrs_24'] = $hrs_24;
    $json['remaining'] = $diff;
    $json['total'] = $total;
    $json['last_item_date'] = $last_item_date;


    //footer data calculation 
    // echo $count_skus;exit;
    $pages = ceil($count_skus/$end);
    // echo $pages;exit;

   
    for($i=1;$i<=$pages;$i++)
    {
    	$href_start='<strong>';
    	$href_end='</strong>';
    	if($i!=$page)
    	{
    		$href_start = '<a href="javascript:void(0)" class="pagination_link" data-competitor="'.$_GET['comp_name'].'" data-last_item_date="'.$last_item_date.'" data-page="'.$i.'" data-sort_order="'.$sort_order.'" data-sort_by="'.$sort_by.'">';
    		$href_end = '</a>';
    	}

    	$footer_data.=$href_start.$i.$href_end.' | ';
    }
    $footer_data.='<br>(Showing '.$page.' of '.$pages.' Pages) / '.$count_skus;
    // $footer_data = rtrim($footer_data,"|");
    $json['footer_data'] = $footer_data;
    // $json['query'] =  "SELECT s.id , s.sku , p.tier , s.date_updated , s.type FROM inv_product_price_scrap s inner join oc_product p on (s.sku = p.sku) WHERE p.tier = '".$_GET['comp_name']."' AND s.url <> '' AND s.type <> '' AND s.date_updated $op '".$last_item_date."' order by s.date_updated desc limit $start,$end";

    if ($skus) {
        $json['success'] = 1;
    } else {
        $json['error'] = 1;
    }
    echo json_encode($json); 
    exit;   
}
if (isset($_GET['add_fix_comment'])) {
	$fixed_sku = $_GET['fixed_sku'];
	$comp_type = $_GET['comp_type'];
	$item_id = $_GET['item_id'];
	$url = $db->func_query_first_cell("SELECT url FROM inv_product_price_scrap WHERE id='".$item_id."'");
	$addcomment = array();
	$addcomment['date_added'] = date('Y-m-d H:i:s');
	$addcomment['user_id'] = $_SESSION['user_id'];
	$addcomment['comment'] = '<a href="'.$url.'" target="_blank">'.$url.'</a>';
	//$addcomment['comment'] = 'URL for '.$fixed_sku.' has been fixed by '. get_username($_SESSION['user_id']);
	$addcomment['sku'] = $fixed_sku;
	$addcomment['competitor'] = $comp_type;
	$db->func_array2insert("inv_scrapper_log", $addcomment);
	$json = array();
	$db->db_exec("UPDATE inv_product_price_scrap SET http_code=200 WHERE id='".$item_id."'");
	$json['success'] = 1;
	echo json_encode($json);
	exit;
}


$total_url_count = $db->func_query_first_cell("SELECT COUNT(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and s.url <> '' AND (p.tier = '1' OR p.tier = '2' OR p.tier = '3')");

$tier_1_skus = $cache->get('competitor.tier_1_skus');
if(!$tier_1_skus)
{

$tier_1_skus = $db->func_query("SELECT s.* FROM inv_product_price_scrap s inner join oc_product p on (s.sku = p.sku) WHERE p.status<>0 and s.date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE() AND p.tier = '1'");

$tier_2_skus = $db->func_query("SELECT s.* FROM inv_product_price_scrap s inner join oc_product p on (s.sku = p.sku) WHERE p.status<>0 and s.date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE() AND p.tier = '2'");
$tier_3_skus = $db->func_query("SELECT s.* FROM inv_product_price_scrap s inner join oc_product p on (s.sku = p.sku) WHERE p.status<>0 and s.date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE() AND p.tier = '3'");
$last_100 = $db->func_query("SELECT * FROM inv_product_price_scrap WHERE  url <> '' order by date_updated desc limit 100");



$cache->set('competitor.tier_1_skus',$tier_1_skus);	
$cache->set('competitor.tier_2_skus',$tier_2_skus);	
$cache->set('competitor.tier_3_skus',$tier_3_skus);	
$cache->set('competitor.last_100_skus',$last_100);	

}
else
{
	$tier_2_skus = $cache->get('competitor.tier_2_skus');
	$tier_3_skus = $cache->get('competitor.tier_3_skus');
	$last_100 = $cache->get('competitor.last_100_skus');
}



$tier_2_skus = $db->func_query("SELECT s.* FROM inv_product_price_scrap s inner join oc_product p on (s.sku = p.sku) WHERE p.status<>0 and s.date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE() AND p.tier = '2'");
$tier_3_skus = $db->func_query("SELECT s.* FROM inv_product_price_scrap s inner join oc_product p on (s.sku = p.sku) WHERE p.status<>0 and s.date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE() AND p.tier = '3'");
$last_100 = $db->func_query("SELECT * FROM inv_product_price_scrap WHERE  url <> '' order by date_updated desc limit 100");



$broken = $db->func_query("SELECT s.* , p.tier FROM inv_product_price_scrap s inner join oc_product p ON (s.sku = p.sku) WHERE p.status<>0 and s.url <> '' AND (s.http_code = '404' OR s.http_code = '502' OR s.http_code = '999')  order by s.date_updated desc");
$new_links = $db->func_query("SELECT s.* , p.tier FROM inv_product_price_scrap s inner join oc_product p ON (s.sku = p.sku) WHERE p.status<>0 and s.url <> '' AND s.is_new = '1'  order by s.date_updated desc");
$broken_logs = $db->func_query("SELECT * from inv_scrapper_log where DATE(date_added) = DATE (NOW())  order by date_added desc");

$tier_1 = array();
$tier_2 = array();
$tier_3 = array();
$tier_1_count = 0;
$tier_2_count = 0;
$tier_3_count = 0;

// tier_1
$tier_1_items = $db->func_query_first_cell("SELECT count(*) FROM oc_product WHERE status<>0 AND tier=1");
$tier_1_links = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> '' AND p.tier = '1'");
$tier_1_without_links = $db->func_query_first_cell("select count(*) from (SELECT COUNT(*) FROM inv_product_price_scrap a,oc_product b WHERE a.sku=b.model and b.is_kit=0 and b.is_blowout=0 and b.is_main_sku=1 and b.status<>0 and b.tier=1 AND LEFT(b.model,4) in ('APL-','BTY-','FLX-','SRN-','TAB-') AND a.url='' GROUP BY a.sku HAVING COUNT(a.url)=9) A");

$tier_2_items = $db->func_query_first_cell("SELECT count(*) FROM oc_product WHERE status<>0 AND tier=2");
$tier_2_links = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> '' AND p.tier = '2'");
$tier_2_without_links = $db->func_query_first_cell("select count(*) from (SELECT COUNT(*) FROM inv_product_price_scrap a,oc_product b WHERE a.sku=b.model and b.is_kit=0 and b.is_blowout=0 and b.is_main_sku=1 and b.status<>0 and b.tier=2 AND LEFT(b.model,4) in ('APL-','BTY-','FLX-','SRN-','TAB-') AND a.url='' GROUP BY a.sku HAVING COUNT(a.url)=9) A");

$tier_3_items = $db->func_query_first_cell("SELECT count(*) FROM oc_product WHERE status<>0 AND tier=3");
$tier_3_links = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> '' AND p.tier = '3'");
$tier_3_without_links = $db->func_query_first_cell("select count(*) from (SELECT COUNT(*) FROM inv_product_price_scrap a,oc_product b WHERE a.sku=b.model and b.is_kit=0 and b.is_blowout=0 and b.is_main_sku=1 and b.status<>0 and b.tier=3 AND LEFT(b.model,4) in ('APL-','BTY-','FLX-','SRN-','TAB-') AND a.url='' GROUP BY a.sku HAVING COUNT(a.url)=9) A");

$tier_2_url_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> '' AND p.tier = '2' order by s.date_updated desc");
$tier_3_url_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> '' AND p.tier = '3' order by s.date_updated desc");
//Last 100 Link Counters
$last_100_24hr_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> ''  AND (p.tier = '1' OR p.tier='2' OR p.tier='3') AND  s.date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE() order by date_updated desc");
$last_100_7d_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> ''  AND (p.tier = '1' OR p.tier='2' OR p.tier='3') AND  s.date_updated BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() order by date_updated desc");
$last_100_30d_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> ''  AND (p.tier = '1' OR p.tier='2' OR p.tier='3') AND  s.date_updated BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() order by date_updated desc");
//Tier 1 Link Counters
$tier_1_24hr_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> '' AND p.tier = '1' AND s.date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE() order by s.date_updated desc");
$tier_1_7d_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> '' AND p.tier = '1' AND s.date_updated BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() order by s.date_updated desc");
$tier_1_30d_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> '' AND p.tier = '1' AND s.date_updated BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() order by s.date_updated desc");
//Tier 2 Link Counter
$tier_2_24hr_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> '' AND p.tier = '2' AND s.date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE() order by s.date_updated desc");
$tier_2_7d_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and s.url <> '' AND p.tier = '2' AND s.date_updated BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() order by s.date_updated desc");
$tier_2_30d_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> '' AND p.tier = '2' AND s.date_updated BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() order by s.date_updated desc");
//Tier 3 Link Counter
$tier_3_24hr_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> '' AND p.tier = '3' AND s.date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE() order by s.date_updated desc");
$tier_3_7d_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> '' AND p.tier = '3' AND s.date_updated BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() order by s.date_updated desc");
$tier_3_30d_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE p.status<>0 and  s.url <> '' AND p.tier = '3' AND s.date_updated BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() order by s.date_updated desc");
$last_100 = main_dashboard($last_100);
$tier_1 = main_dashboard($tier_1_skus);
$tier_2 = main_dashboard($tier_2_skus);
$tier_3 = main_dashboard($tier_3_skus);

foreach ($broken as $key => $data) {
	$price = $db->func_query_first("select *, (SELECT price from inv_product_price_scrap_history where sku = ph.sku and type = ph.type order by added desc limit 1, 1) as old_price from inv_product_price_scrap_history ph where sku = '" . $data['sku'] . "' AND type = '".$data['type']."' order by added DESC limit 1");
	$change = number_format($price['price'] / $price['old_price'] * 100, 2);
				if ($change < 100.00 && $change > 0.00) {
					$change = '-' . (100 - $change);
				} else if ($change == 0.00) {
					$change = 100 - $change;
				} else {
					$change = '+' . ($change - 100);
				}
				if((float)$price['old_price']==0.00)
				{
					$change = 0.00;
				}
	$ppusa_price = getOCItemPrice($db->func_query_first_cell("SELECT product_id FROM oc_product WHERE model='".$data['sku']."'"));
	$perc_diff = number_format(($ppusa_price - $price['price'])/ $price['price'] * 100, 2);
	if ($perc_diff>0) {
		$perc_diff = '+'.$perc_diff;
	}
	$broken[$key]['price'] =$price['price'];
	$broken[$key]['old_price'] =$price['old_price'];
	$broken[$key]['change'] =$change;
	$broken[$key]['our_price'] = $ppusa_price;
	$broken[$key]['perc_diff'] = $perc_diff;			
}
foreach ($new_links as $key => $data) {
	$price = $db->func_query_first("select *, (SELECT price from inv_product_price_scrap_history where sku = ph.sku and type = ph.type order by added desc limit 1, 1) as old_price from inv_product_price_scrap_history ph where sku = '" . $data['sku'] . "' AND type = '".$data['type']."' order by added DESC limit 1");
	$change = number_format($price['price'] / $price['old_price'] * 100, 2);
				if ($change < 100.00 && $change > 0.00) {
					$change = '-' . (100 - $change);
				} else if ($change == 0.00) {
					$change = 100 - $change;
				} else {
					$change = '+' . ($change - 100);
				}
				if((float)$price['old_price']==0.00)
				{
					$change = 0.00;
				}
	$ppusa_price = getOCItemPrice($db->func_query_first_cell("SELECT product_id FROM oc_product WHERE model='".$data['sku']."'"));
	$perc_diff = number_format(($ppusa_price - $price['price'])/ $price['price'] * 100, 2);
	if ($perc_diff>0) {
		$perc_diff = '+'.$perc_diff;
	}
	$new_links[$key]['price'] =$price['price'];
	$new_links[$key]['old_price'] =$price['old_price'];
	$new_links[$key]['change'] =$change;
	$new_links[$key]['our_price'] = $ppusa_price;
	$new_links[$key]['perc_diff'] = $perc_diff;			
}
//testObject($tier_2);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Competitor Dashboard | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	
	<link href="include/calendar.css" rel="stylesheet" type="text/css" />
<link href="include/calendar-blue.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="include/calendar.js"></script>
<script type="text/javascript" src="include/calendar-en.js"></script>

<script type="text/javascript" src="js/jquery.min.js"></script>
	<style>
#xcontent{width: 100%;
			height: 100%;
			top: 0px;
			left: 0px;
			position: fixed;
			display: block;
			opacity: 0.8;
			background-color: #000;
			z-index: 99;}
			.makeTabs .button{
				padding: 5px 8px;
				background-color:#3F51B5;
			}

	</style>
</head>
<body>
<div id="xcontent" style="display:none"><div style="color:#fff;
			top:40%;
			position:fixed;
			left:40%;
			font-weight:bold;font-size:25px"><img src="https://phonepartsusa.com/catalog/view/theme/default/image/loader_white.gif" /><span style="margin-left: 11%;
			margin-top: 33%;
			position: absolute;

			width: 201px;">Please wait...</span></div></div>  
		<div align="center"> 
			<?php include_once 'inc/header.php';?>
		</div>
		<?php if ($_SESSION['message']) { ?>
		<div align="center"><br />
			<font color="red">
				<?php
				echo $_SESSION['message'];
				unset($_SESSION['message']);
				?>
				<br />
			</font>
		</div><br><br>
		<?php } ?>
		<div align="center">
		<div class="tabMenu" >
			<input type="button" class="toogleTab" data-my="dashboard" data-tab="tabCompetitor" value="Competitor Dashboard">
			<input type="button" class="toogleTab" data-my="broken" data-tab="tabBroken" value="Broken Links">
			<input type="button" class="toogleTab" data-my="new_links" data-tab="tabNew" value="New Links">
			<input type="button" class="toogleTab" data-my="need_links" onclick="fetchNeedLinks();" data-tab="tabNeed" value="Need Links">
			<!-- <input type="button" class="toogleTab" onclick="fetchData('all');" id="all_clicker" data-tab="tabAll" value="All Tiers"> -->
			<input type="button" class="toogleTab" data-my="tier_1" onclick="fetchData('tier_1');" id="tier1_clicker" data-tab="tabTier1" value="Tier 1">
			<input type="button" class="toogleTab" data-my="tier_2" onclick="fetchData('tier_2');" id="tier2_clicker" data-tab="tabTier2" value="Tier 2">
			<input type="button" class="toogleTab" data-my="tier_3" onclick="fetchData('tier_3');" id="tier3_clicker" data-tab="tabTier3" value="Tier 3">
			<input type="button" class="toogleTab" data-my="mobile_sentrix" onclick="fetchData('mobile_sentrix');" data-tab="tabMobileSentrix" value="Mobile Sentrix">
			<input type="button" class="toogleTab" data-my="fixez" onclick="fetchData('fixez');" data-tab="tabFixez" value="FixEZ">
			<input type="button" class="toogleTab" data-my="mengtor" onclick="fetchData('mengtor');" data-tab="tabMengtor" value="Mengtor">
			<input type="button" class="toogleTab" data-my="mobile_defenders" onclick="fetchData('mobile_defenders');" data-tab="tabMobileDefenders" value="Mobile Defenders">
			<input type="button" class="toogleTab" data-my="etrade_supply" onclick="fetchData('etrade_supply');" data-tab="tabEtrade" value="eTrade Supply">
			<input type="button" class="toogleTab" data-my="maya_cellular" onclick="fetchData('maya_cellular');" data-tab="tabMaya" value="Maya Cellular">
			<input type="button" class="toogleTab" data-my="lcd_loop" onclick="fetchData('lcd_loop');" data-tab="tabLcdLoop" value="LCD Loop">
			<input type="button" class="toogleTab" data-my="parts_4_cells" onclick="fetchData('parts_4_cells');" data-tab="tabParts4Cell" value="Parts 4 Cell">
			<input type="button" class="toogleTab" data-my="cell_parts_hub" onclick="fetchData('cell_parts_hub');" data-tab="tabCellPartsHub" value="Cell Parts Hub">
		</div>
		<div class="tabHolder">
			<div id="tabCompetitor" class="makeTabs">
				<h3 style="color: #337ab7">Competitor Dashboard</h3><br><br><br>		
				<table align="center" width="100%" >	
					<tr>
						<td align="center">
							<table>
								<tr>
									<td colspan=2><font style="font-size:xx-large;">Total Links</font></td>
								</tr>
								<tr>
									<td align="center" colspan=2><font style="font-size:x-large;"><?php echo $total_url_count; ?></font></td>
								</tr>	
							</table>
						</td>
					</tr>
				</table>
				<br>
				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table>
								<tr>
									<td align="center">
										<font style="font-size:x-large;">30 Days:<?php echo $last_100_30d_count;?> Links</font><br>
										<font style="font-size:x-large;" >7 Days:<?php echo $last_100_7d_count;?> Links</font><br>
										<font style="font-size:x-large;" >24 Hours:<?php echo $last_100_24hr_count;?> Links</font>
									</td>
								</tr>	
							</table>
						</td>
					</tr>
				</table>
				<br>
				<table align="center" >
					<tr>
						<td align="center">
							<table>
								<tr>
									<td align="center">
										<font style="font-size:x-large;">Last 100 Scraps</font><br>
										<a href="javascript:void(0);" onclick="$('#all_clicker').trigger('click');">View All Scraps</a>
									</td>
								</tr>
								<tr>
									<td>
									<div style="height:300px;width:1200px;overflow:auto;">
									<table  border="1"  cellpadding="5" cellspacing="0" style="width: 1180px;" class="tablesorter">
										<thead>
										<tr style="background:#e5e5e5;">
											<th align="center" style="width: 110px !important;" >Date/Time</th>
											<th style="width: 110px !important;" align="center">Sku</th>
											<th align="center">Item Name</th>
											<th align="center">Competitor</th>
											<th align="center" style="width: 110px !important;">Our Price</th>
											<th align="center" style="width: 110px !important;">Old Price</th>
											<th align="center" style="width: 110px !important;">New Price</th>
											<th align="center" style="width: 110px !important;">Change %</th>
											<th align="center">% Diff</th>
										</tr>									
										</thead>
										<tbody>
										<?php foreach ($last_100 as $key => $data) { 
											if($data['http_code']=='404')  { $data['fixer'] = 1;?>
											<tr id="row_<?php echo $key;?>_last100" bgcolor="#FF6347">
											<?php } else if($data['price']=='0.00'){ $data['fixer'] = 1;?>
											<tr id="row_<?php echo $key;?>_last100" bgcolor="#FFFFB0">
											<?php } else { $data['fixer'] = 0;?>
											<tr id="row_<?php echo $key;?>_last100">
											<?php } ?>
												<td align="center"><?php echo americanDate($data['date_updated']); ?></td>
												<td align="center"><?php echo linkToProduct($data['sku'],'','target="_blank"'); ?>
												<?php if($data['fixer']==1){ ?><br>
												<input type="checkbox" id="<?php echo $key; ?>_last100" onclick="fixLink(<?php echo $key; ?>,'last100');">
														<span style="display: none;" id="<?php echo $key; ?>_last100_fixed">&#10004; Link Fixed</span>
												<?php } ?>
												</td>
												<td align="center"><?php echo getItemName($data['sku']); ?></td>
												<td align="center"><?php echo $data['type']; ?></td>
												<td align="center"><?php echo '$'.number_format($data['our_price'],2); ?></td>
												<td align="center"><?php echo '$'.number_format($data['old_price'],2); ?></td>
												<td align="center"><?php echo '$'.number_format($data['price'],2); ?></td>
												<td align="center"><?php echo $data['change']; ?>%</td>
												<td align="center"><?php echo $data['perc_diff']; ?>%</td>
											</tr>
										<?php	} ?>
										</tbody>
									</table>
									</div>
									</td>
								</tr>	
							</table>
						</td>
					</tr>
				</table>

				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table width="30%">
								<tr>
									<td colspan="4" align="center"><font style="font-size:xx-large;">Tier 1</font></td>
								</tr>
								<tr>
									<td align="center">
										<font style="font-size:x-large;"><?php echo ($tier_1_items); ?></font><br>
										Items
									</td>
									<td align="center">
										<font style="font-size:x-large;"><?php echo $tier_1_links; ?></font><br>
										Product Links
									</td>
									<td align="center">
										<font style="font-size:x-large;"><?php echo $tier_1_without_links; ?></font><br>
										Product w/o Links
									</td>
									<td align="center">
										<font style="font-size:x-large;"><?php echo $tier_1_items + $tier_1_without_links; ?></font><br>
										Total Products
									</td>
								</tr>	
							</table>
						</td>			
					</tr>
				</table>
				<br>
				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table>
								<tr>
									<td align="center">
										<font style="font-size:x-large;">30 Days:<?php echo $tier_1_30d_count;?> Links</font><br>
										<font style="font-size:x-large;" >7 Days:<?php echo $tier_1_7d_count;?> Links</font><br>
										<font style="font-size:x-large;" >24 Hours:<?php echo $tier_1_24hr_count;?> Links</font>
									</td>
								</tr>	
							</table>
						</td>
					</tr>
				</table>
				<br>
				<table align="center">
					<tr>
						<td align="center">
							<table>
								<tr>
									<td align="center">
										<font style="font-size:x-large;">Tier 1 Price Changes</font><br>
										<a href="javascript:void(0);" onclick="$('#tier1_clicker').trigger('click');">View All Scraps</a>
									</td>
								</tr>
								<tr>
									<td>
										<div style="height:300px;width:1200px;overflow:auto;">
											<table border="1"  cellpadding="5" cellspacing="0" style="width: 1180px;" class="tablesorter">
												<thead>
												<tr style="background:#e5e5e5;">
												<th align="center" style="width: 110px !important;" >Date/Time</th>
												<th style="width: 110px !important;" align="center">SKU</th>
												<th align="center">Item Name</th>
												<th align="center">Competitor</th>
												<th align="center" style="width: 110px !important;">Our Price</th>
												<th align="center" style="width: 110px !important;">Old Price</th>
												<th align="center" style="width: 110px !important;">New Price</th>
												<th align="center" style="width: 110px !important;">Change %</th>
												<th align="center">% Diff</th>
												</tr>									
												</thead>
												
												<tbody>
										<?php foreach ($tier_1 as $key => $data) { 
											if($data['http_code']=='404')  { $data['fixer'] = 1;?>
											<tr id="row_<?php echo $key;?>_last100" bgcolor="#FF6347">
											<?php } else if($data['price']=='0.00'){ $data['fixer'] = 1;?>
											<tr id="row_<?php echo $key;?>_last100" bgcolor="#FFFFB0">
											<?php } else { $data['fixer'] = 0;?>
											<tr id="row_<?php echo $key;?>_last100">
											<?php } ?>
												<td align="center"><?php echo americanDate($data['date_updated']); ?></td>
												<td align="center"><?php echo linkToProduct($data['sku'],'','target="_blank"'); ?>
												<?php if($data['fixer']==1){ ?><br>
												<input type="checkbox" id="<?php echo $key; ?>_last100" onclick="fixLink(<?php echo $key; ?>,'last100');">
														<span style="display: none;" id="<?php echo $key; ?>_last100_fixed">&#10004; Link Fixed</span>
												<?php } ?>
												</td>
												<td align="center"><?php echo getItemName($data['sku']); ?></td>
												<td align="center"><?php echo $data['type']; ?></td>
												<td align="center"><?php echo '$'.number_format($data['our_price'],2); ?></td>
												<td align="center"><?php echo '$'.number_format($data['old_price'],2); ?></td>
												<td align="center"><?php echo '$'.number_format($data['price'],2); ?></td>
												<td align="center"><?php echo $data['change']; ?>%</td>
												<td align="center"><?php echo $data['perc_diff']; ?>%</td>
											</tr>
										<?php	} ?>
												</tbody>
											</table>
										</div>
									</td>
								</tr>	
							</table>
						</td>
					</tr>
				</table>

				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table width="30%">
								<tr>
									<td colspan="4" align="center"><font style="font-size:xx-large;">Tier 2</font></td>
								</tr>
								<tr>
									<td align="center">
										<font style="font-size:x-large;"><?php echo ($tier_2_items); ?></font><br>
										Items
									</td>
									<td align="center">
										<font style="font-size:x-large;"><?php echo $tier_2_links; ?></font><br>
										Product Links
									</td>
									<td align="center">
										<font style="font-size:x-large;"><?php echo $tier_2_without_links; ?></font><br>
										Product w/o Links
									</td>
									<td align="center">
										<font style="font-size:x-large;"><?php echo $tier_2_items + $tier_2_without_links; ?></font><br>
										Total Products
									</td>
								</tr>	
							</table>
						</td>
					</tr>
				</table>
				<br>
				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table>
								<tr>
									<td align="center">
										<font style="font-size:x-large;">30 Days:<?php echo $tier_2_30d_count;?> Links</font><br>
										<font style="font-size:x-large;" >7 Days:<?php echo $tier_2_7d_count;?> Links</font><br>
										<font style="font-size:x-large;" >24 Hours:<?php echo $tier_2_24hr_count;?> Links</font>
									</td>
								</tr>	
							</table>
						</td>
					</tr>
				</table>
				<br>
				<table align="center" >
					<tr>
					<td align="center">
						<table>
							<tr>
								<td align="center">
									<font style="font-size:x-large;">Tier 2 Price Changes</font><br>
									<a href="javascript:void(0);" onclick="$('#tier2_clicker').trigger('click');">View All Scraps</a>
								</td>
							</tr>
							<tr>
								<td>
									<div style="height:300px;width:1200px;overflow:auto;">
										<table border="1" cellpadding="5" cellspacing="0" style="width: 1180px;" class="tablesorter">
											<thead>
												<tr style="background:#e5e5e5;">
												<th align="center" style="width: 110px !important;" >Date/Time</th>
												<th style="width: 110px !important;" align="center">Sku</th>
												<th align="center">Item Name</th>
												<th align="center">Competitor</th>
												<th align="center" style="width: 110px !important;">Our Price</th>
												<th align="center" style="width: 110px !important;">Old Price</th>
												<th align="center" style="width: 110px !important;">New Price</th>
												<th align="center" style="width: 110px !important;">Change %</th>
												<th align="center">% Diff</th>
												</tr>									
												</thead>
											<tbody>
											<tbody>
										<?php foreach ($tier_2 as $key => $data) { 
											if($data['http_code']=='404')  { $data['fixer'] = 1;?>
											<tr id="row_<?php echo $key;?>_last100" bgcolor="#FF6347">
											<?php } else if($data['price']=='0.00'){ $data['fixer'] = 1;?>
											<tr id="row_<?php echo $key;?>_last100" bgcolor="#FFFFB0">
											<?php } else { $data['fixer'] = 0;?>
											<tr id="row_<?php echo $key;?>_last100">
											<?php } ?>
												<td align="center"><?php echo americanDate($data['date_updated']); ?></td>
												<td align="center"><?php echo linkToProduct($data['sku'],'','target="_blank"'); ?>
												<?php if($data['fixer']==1){ ?><br>
												<input type="checkbox" id="<?php echo $key; ?>_last100" onclick="fixLink(<?php echo $key; ?>,'last100');">
														<span style="display: none;" id="<?php echo $key; ?>_last100_fixed">&#10004; Link Fixed</span>
												<?php } ?>
												</td>
												<td align="center"><?php echo getItemName($data['sku']); ?></td>
												<td align="center"><?php echo $data['type']; ?></td>
												<td align="center"><?php echo '$'.number_format($data['our_price'],2); ?></td>
												<td align="center"><?php echo '$'.number_format($data['old_price'],2); ?></td>
												<td align="center"><?php echo '$'.number_format($data['price'],2); ?></td>
												<td align="center"><?php echo $data['change']; ?>%</td>
												<td align="center"><?php echo $data['perc_diff']; ?>%</td>
											</tr>
										<?php	} ?>
											</tbody>
										</table>
									</div>
								</td>
							</tr>	
						</table>
					</td>
					</tr>
				</table>

				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table width="30%">
								<tr>
									<td colspan="4" align="center"><font style="font-size:xx-large;">Tier 3</font></td>
								</tr>
								<tr>
									<td align="center">
										<font style="font-size:x-large;"><?php echo ($tier_3_items); ?></font><br>
										Items
									</td>
									<td align="center">
										<font style="font-size:x-large;"><?php echo $tier_3_links; ?></font><br>
										Product Links
									</td>
									<td align="center">
										<font style="font-size:x-large;"><?php echo $tier_3_without_links; ?></font><br>
										Product w/o Links
									</td>
									<td align="center">
										<font style="font-size:x-large;"><?php echo $tier_3_items + $tier_3_without_links; ?></font><br>
										Total Products
									</td>
								</tr>	
							</table>
						</td>
					</tr>
				</table>
				<br>
				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table>
								<tr>
									<td align="center">
										<font style="font-size:x-large;">30 Days:<?php echo $tier_3_30d_count;?> Links</font><br>
										<font style="font-size:x-large;" >7 Days:<?php echo $tier_3_7d_count;?> Links</font><br>
										<font style="font-size:x-large;" >24 Hours:<?php echo $tier_3_24hr_count;?> Links</font>
									</td>
								</tr>	
							</table>
						</td>
					</tr>
				</table>
				<br>
				<table align="center" >
					<tr>
					<td align="center">
						<table>
							<tr>
								<td align="center">
									<font style="font-size:x-large;">Tier 3 Price Changes</font><br>
									<a href="javascript:void(0);" onclick="$('#tier3_clicker').trigger('click');">View All Scraps</a>
								</td>
							</tr>
							<tr>
								<td>
									<div style="height:300px;width:1200px;overflow:auto;">
										<table border="1" cellpadding="5" cellspacing="0" style="width: 1180px;" class="tablesorter">
											<thead>
												<tr style="background:#e5e5e5;">
												<th align="center" style="width: 110px !important;" >Date/Time</th>
												<th style="width: 110px !important;" align="center">Sku</th>
												<th align="center">Item Name</th>
												<th align="center">Competitor</th>
												<th align="center" style="width: 110px !important;">Our Price</th>
												<th align="center" style="width: 110px !important;">Old Price</th>
												<th align="center" style="width: 110px !important;">New Price</th>
												<th align="center" style="width: 110px !important;">Change %</th>
												<th align="center">% Diff</th>
												</tr>									
												</thead>
											<tbody>
											<tbody>
										<?php foreach ($tier_3 as $key => $data) { 
											if($data['http_code']=='404')  { $data['fixer'] = 1;?>
											<tr id="row_<?php echo $key;?>_last100" bgcolor="#FF6347">
											<?php } else if($data['price']=='0.00'){ $data['fixer'] = 1;?>
											<tr id="row_<?php echo $key;?>_last100" bgcolor="#FFFFB0">
											<?php } else { $data['fixer'] = 0;?>
											<tr id="row_<?php echo $key;?>_last100">
											<?php } ?>
												<td align="center"><?php echo americanDate($data['date_updated']); ?></td>
												<td align="center"><?php echo linkToProduct($data['sku'],'','target="_blank"'); ?>
												<?php if($data['fixer']==1){ ?><br>
												<input type="checkbox" id="<?php echo $key; ?>_last100" onclick="fixLink(<?php echo $key; ?>,'last100');">
														<span style="display: none;" id="<?php echo $key; ?>_last100_fixed">&#10004; Link Fixed</span>
												<?php } ?>
												</td>
												<td align="center"><?php echo getItemName($data['sku']); ?></td>
												<td align="center"><?php echo $data['type']; ?></td>
												<td align="center"><?php echo '$'.number_format($data['our_price'],2); ?></td>
												<td align="center"><?php echo '$'.number_format($data['old_price'],2); ?></td>
												<td align="center"><?php echo '$'.number_format($data['price'],2); ?></td>
												<td align="center"><?php echo $data['change']; ?>%</td>
												<td align="center"><?php echo $data['perc_diff']; ?>%</td>
											</tr>
										<?php	} ?>
											</tbody>
										</table>
									</div>
								</td>
							</tr>	
						</table>
					</td>
					</tr>
				</table>
			</div>
			<div id="tabBroken" class="makeTabs" >
				<h3>Fixed Url Logs</h3>				
				<div id="broken_log_div" style="height:300px;width:1200px;overflow:auto;">
					From: <input type="text" style="width: 70px; border:none" onblur="fetchLogs();" data-type="date" value="<?php echo date('Y-m-d'); ?>" name="log_start_date" id="log_start_date" />  To: <input style="width: 70px; border:none" type="text" data-type="date" value="<?php echo date('Y-m-d'); ?>"  onblur="fetchLogs();" name="log_end_date" id="log_end_date" />
					<table border="1" id="broken_log_table" cellpadding="5" cellspacing="0"  style="width: 1180px;">
						<thead>
							<tr style="background:#e5e5e5;">
								<th align="center" >Date/Time</th>
								<th align="center">Sku</th>
								<th align="center">Competitor</th>
								<th align="center">URL</th>
								<th align="center">User</th>
							</tr>									
						</thead>
						
						<tbody>
							<?php foreach ($broken_logs as $comment) { ?>
								<tr>
									<td><?php echo americanDate($comment['date_added']); ?></td>
									<td><?php echo linkToProduct($comment['sku'],'',"target='_blank'"); ?></td>
									<td><?php echo $comment['competitor']; ?></td>
									<td><?php echo $comment['comment']; ?></td>
									<td><?php echo get_username($comment['user_id']); ?></td>
								</tr>		
							<?php } ?>
						</tbody>
					</table>
				</div>
				<br>
				<h1 style="color: #337ab7">Broken Links</h1><br>
				
				<div id="broken_div" style="height:500px;width:1200px;overflow:auto;">
					<table border="1" id="broken_table" cellpadding="5" cellspacing="0" style="width: 1180px;" class="tablesorter">
						<thead>
							<tr style="background:#e5e5e5;">
								<th></th>
								<th align="center" style="width: 110px !important;" >Last Fetch</th>
								<th align="center">Tier</th>
								<th style="width: 110px !important;" align="center">Sku</th>
								<th style="width: 170px !important;" align="center">Item Title</th>
								<th style="width: 170px !important;" align="center">Competitor</th>
								<th align="center" style="width: 110px !important;">Our Price</th>
								<th align="center" style="width: 110px !important;">Old Price</th>
								<th align="center" style="width: 110px !important;">New Price</th>
								<th align="center" style="width: 110px !important;">% Change</th>
								<th align="center" style="width: 110px !important;">60 Price Trend</th>
								<th align="center">% Diff</th>
							</tr>									
						</thead>

						
						<tbody id="broken_tbody">
						<?php foreach ($broken as $key => $data) { $sku = $data['sku']; $type = $data['type']; $id = $data['id'];?>
							<tr>
								<td><input type="checkbox" id="<?php echo $sku; ?>_<?php echo $type; ?>_broken" onclick="fixBrokenLink(<?php echo $key; ?>);">
								<input type="hidden" id="<?php echo $key; ?>_sku_broken" value = "<?php echo $sku;?>" >
								<input type="hidden" id="<?php echo $key; ?>_type_broken" value = "<?php echo $type;?>" >
								<input type="hidden" id="<?php echo $key; ?>_item_id_broken" value = "<?php echo $id;?>" >

								</td>
								<td align="center"><?php echo americanDate($data['date_updated']); ?></td>
								<td align="center"><?php echo $data['tier']; ?></td>
								<td align="center"><?php echo linkToProduct($data['sku'],'','target="_blank"'); ?>
									<br>
								<span style="display: none;" id="<?php echo $sku; ?>_<?php echo $type; ?>_broken_fixed">&#10004; Link Fixed</span>
								</td>
								<td align="center"><?php echo getItemName($data['sku']); ?></td>
								<td align="center"><?php echo $data['type']; ?><br>(<?php echo ($data['http_code']=='999'?'Invalid URL':$data['http_code']);?>)</td>
								<td align="center"><?php echo '$'.number_format($data['our_price'],2); ?></td>
								<td align="center"><?php echo '$'.number_format($data['old_price'],2); ?></td>
								<td align="center"><a href="<?php echo $data['url'];?>" target="_blank" ><?php echo '$'.number_format( $data['price'],2); ?></a></td>
								<td align="center"><?php echo $data['change']; ?>%</td>
								<td align="center">N/A</td>
								<td align="center"><?php echo $data['perc_diff']; ?>%</td>
							</tr>
						<?php	} ?>
						</tbody>
					</table>
				</div>

			</div>
			<div id="tabNew" class="makeTabs" >
				<h1 style="color: #337ab7">New Links</h1><br>
				From: <input type="text" style="width: 70px; border:none" onblur="fetchNewUrls();" data-type="date" value="<?php echo date('Y-m-d'); ?>" name="url_start_date" id="url_start_date" />  To: <input style="width: 70px; border:none" type="text" data-type="date" value="<?php echo date('Y-m-d'); ?>"  onblur="fetchNewUrls();" name="url_end_date" id="url_end_date" />
				
				<div id="new_div" style="height:500px;width:1200px;overflow:auto;">
					<table border="1" id="new_table" cellpadding="5" cellspacing="0" style="width: 1180px;" class="tablesorter">
						<thead>
							<tr style="background:#e5e5e5;">
								<th align="center" style="width: 110px !important;" >Last Fetch</th>
								<th align="center">Tier</th>
								<th style="width: 110px !important;" align="center">Sku</th>
								<th style="width: 170px !important;" align="center">Item Title</th>
								<th style="width: 170px !important;" align="center">Competitor</th>
								<th align="center" style="width: 110px !important;">Our Price</th>
								<th align="center" style="width: 110px !important;">Old Price</th>
								<th align="center" style="width: 110px !important;">New Price</th>
								<th align="center" style="width: 110px !important;">% Change</th>
								<th align="center" style="width: 110px !important;">60 Price Trend</th>
								<th align="center">% Diff</th>
							</tr>									
						</thead>
						<tbody id="new_tbody">
						<?php foreach ($new_links as $key => $data) { ?>
							<tr>
								<td align="center"><?php echo americanDate($data['date_updated']); ?></td>
								<td align="center"><?php echo $data['tier']; ?></td>
								<td align="center"><?php echo linkToProduct($data['sku'],'','target="_blank"'); ?>
								</td>
								<td align="center"><?php echo getItemName($data['sku']); ?></td>
								<td align="center"><?php echo $data['type']; ?></td>
								<td align="center"><?php echo $data['our_price']; ?></td>
								<td align="center"><?php echo $data['old_price']; ?></td>
								<td align="center"><?php echo $data['price']; ?></td>
								<td align="center"><?php echo $data['change']; ?>%</td>
								<td align="center">N/A</td>
								<td align="center"><?php echo $data['perc_diff']; ?>%</td>
							</tr>
						<?php	} ?>
						</tbody>
					</table>
				</div>

			</div>
			<div id="tabNeed" class="makeTabs">
				<table border="1" id="need_links_table" cellpadding="5" cellspacing="0" style="width: 90%;">
				<thead>
				<th align="center"><a href="#" onclick="fetchNeedLinks(1,'b.tier');">Tier</a></th>
				<th align="center"><a href="#" onclick="fetchNeedLinks(1,'b.date_added');">Date Created</a></th>
				<th align="center"><a href="#" onclick="fetchNeedLinks(1,'b.model');">SKU</a></th>
				<th align="center"><a href="#" onclick="fetchNeedLinks(1,'c.name');">Item Name</a></th>
				<th align="center"><a href="#" onclick="fetchNeedLinks(1,'b.price');">Default Price</a></th>
				<th align="center"><a href="#" onclick="fetchNeedLinks(1,'b.sale_price');">Sale Price</a></th>

				</thead>
				<tbody>


				</tbody>

				</table>
			</div>
			
			<div id="tabTier1" class="makeTabs" >
				<h3 style="color: #337ab7">Tier 1</h3><br>
				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table id="tier_1_counter_table">			
							</table>
						</td>
					</tr>
				</table>
				<?php

				$_competitor = 'tier_1';
				?>
				<form method="post" action="" enctype="multipart/form-data">
				

				
				<input style="width: 80px; margin-bottom: 10px;margin-right: 25px;" type="submit" class="button" name="update_prices" value="Update">
				<a style="margin-bottom: 10px;margin-right: 25px;" class="button" href="#" onClick="window.location='competitor_dashboard.php?export_csv=1&tier_type=1&hide_zero_diff='+$('#<?php echo $_competitor;?>_hide_diff').is(':checked')+'&avg_chk='+$('#<?php echo $_competitor;?>_avg_chk').is(':checked')+'&low_chk='+$('#<?php echo $_competitor;?>_low_chk').is(':checked')+'&avg_select='+$('#<?php echo $_competitor;?>_avg_select').val()+'&low_select='+$('#<?php echo $_competitor;?>_low_select').val()+'&contains_chk='+$('#<?php echo $_competitor;?>_contains_chk').is(':checked')+'&contains_select='+$('#<?php echo $_competitor;?>_contains_select').val()+'&contains_field='+$('#<?php echo $_competitor;?>_contains_text').val()+'&avg_cost_chk='+$('#<?php echo $_competitor;?>_avg_cost_chk').is(':checked')+'&avg_cost_select='+$('#<?php echo $_competitor;?>_avg_cost_select').val()+'&low_cost_chk='+$('#<?php echo $_competitor;?>_low_cost_chk').is(':checked')+'&low_cost_select='+$('#<?php echo $_competitor;?>_low_cost_select').val()">Export CSV</a>


				<br>
				
					<table border="1"  cellpadding="2" cellspacing="0" style="clear:both">
					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_hide_diff">Hide 0% Differnces</td>
					</tr>

					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_contains_chk"> Contains 
					<select id="<?php echo $_competitor;?>_contains_select">
					<option value="contains_above_field">More Than</option>
					<option value="contains_less_field">Less Than</option>
					</select> <input type="text" value="1" id="<?php echo $_competitor;?>_contains_text" style="width:60px"></td>
					</tr>

					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_avg_chk"> Avg Price 
					<select id="<?php echo $_competitor;?>_avg_select">
					<option value="avg_less_price">Less Than</option>
					<option value="avg_above_price">More Than</option>
					</select> Our Price</td>
					</tr>
					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_low_chk"> Low Price 
					<select id="<?php echo $_competitor;?>_low_select">
					<option value="low_less_price">Less Than</option>
					<option value="low_above_price">More Than</option>
					</select> Our Price
					</td>
					</tr>


					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_avg_cost_chk"> Avg Price 
					<select id="<?php echo $_competitor;?>_avg_cost_select">
					<option value="avg_less_cost">Less Than</option>
					<option value="avg_above_cost">More Than</option>
					</select> Cost
					</td>
					</tr>

					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_low_cost_chk"> Low Price 
					<select id="<?php echo $_competitor;?>_low_cost_select">
					<option value="low_less_cost">Less Than</option>
					<option value="low_above_cost">More Than</option>
					</select> Cost
					</td>
					</tr>

					<tr>
					<td align="center"><input type="button" class="button" value="Search" onClick="fetchData('<?php echo $_competitor;?>',0,1);"></td>


					</table>

					<br><br>


				
					<table border="1" id="<?php echo $_competitor;?>_table" cellpadding="5" cellspacing="0" style="width: 1180px;">
						<thead>
							<tr style="background:#e5e5e5;">
								<th align="center" style="width: 110px !important;" ><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'s.date_updated');">Last Fetch</a></th>
								<th align="center">Tier</th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'s.sku');">Sku</a></th>
								<th style="width: 170px !important;" align="center">Item Title</th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'true_cost');">Cost</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'our_price');">Our Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'avg_price');">Avg Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'min_price');">Lowest Price</a></th>
								<th align="center" style="width: 110px !important;">60 Price Trend</th>
								<th align="center"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'((our_price-MIN(s.recent_price))/MIN(s.recent_price)*100)');" >% Diff</a></th>
								<th></th>
								<th align="center">Expected</th>
							</tr>									
						</thead>
						<tbody id="<?php echo $_competitor;?>_tbody">
						</tbody>
					</table>
				
				</form>
			</div>
			<div id="tabTier2" class="makeTabs" >
				<h3 style="color: #337ab7">Tier 2</h3><br>
				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table id="tier_2_counter_table">			
							</table>
						</td>
					</tr>
				</table>
				<?php

				$_competitor = 'tier_2';
				?>
				<form method="post" action="" enctype="multipart/form-data">
				

				
				<input style="width: 80px; margin-bottom: 10px;margin-right: 25px;" type="submit" class="button" name="update_prices" value="Update">
				<a style="margin-bottom: 10px;margin-right: 25px;" class="button" href="#" onClick="window.location='competitor_dashboard.php?export_csv=1&tier_type=2&hide_zero_diff='+$('#<?php echo $_competitor;?>_hide_diff').is(':checked')+'&avg_chk='+$('#<?php echo $_competitor;?>_avg_chk').is(':checked')+'&low_chk='+$('#<?php echo $_competitor;?>_low_chk').is(':checked')+'&avg_select='+$('#<?php echo $_competitor;?>_avg_select').val()+'&low_select='+$('#<?php echo $_competitor;?>_low_select').val()+'&contains_chk='+$('#<?php echo $_competitor;?>_contains_chk').is(':checked')+'&contains_select='+$('#<?php echo $_competitor;?>_contains_select').val()+'&contains_field='+$('#<?php echo $_competitor;?>_contains_text').val()+'&avg_cost_chk='+$('#<?php echo $_competitor;?>_avg_cost_chk').is(':checked')+'&avg_cost_select='+$('#<?php echo $_competitor;?>_avg_cost_select').val()+'&low_cost_chk='+$('#<?php echo $_competitor;?>_low_cost_chk').is(':checked')+'&low_cost_select='+$('#<?php echo $_competitor;?>_low_cost_select').val()">Export CSV</a>


				<br>
				
					<table border="1"  cellpadding="2" cellspacing="0" style="clear:both">
					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_hide_diff">Hide 0% Differnces</td>
					</tr>

					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_contains_chk"> Contains 
					<select id="<?php echo $_competitor;?>_contains_select">
					<option value="contains_above_field">More Than</option>
					<option value="contains_less_field">Less Than</option>
					</select> <input type="text" value="1" id="<?php echo $_competitor;?>_contains_text" style="width:60px"></td>
					</tr>

					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_avg_chk"> Avg Price 
					<select id="<?php echo $_competitor;?>_avg_select">
					<option value="avg_less_price">Less Than</option>
					<option value="avg_above_price">More Than</option>
					</select> Our Price</td>
					</tr>
					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_low_chk"> Low Price 
					<select id="<?php echo $_competitor;?>_low_select">
					<option value="low_less_price">Less Than</option>
					<option value="low_above_price">More Than</option>
					</select> Our Price
					</td>
					</tr>
					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_avg_cost_chk"> Avg Price 
					<select id="<?php echo $_competitor;?>_avg_cost_select">
					<option value="avg_less_cost">Less Than</option>
					<option value="avg_above_cost">More Than</option>
					</select> Cost
					</td>
					</tr>

					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_low_cost_chk"> Low Price 
					<select id="<?php echo $_competitor;?>_low_cost_select">
					<option value="low_less_cost">Less Than</option>
					<option value="low_above_cost">More Than</option>
					</select> Cost
					</td>
					</tr>

					<tr>
					<td align="center"><input type="button" class="button" value="Search" onClick="fetchData('<?php echo $_competitor;?>',0,1);"></td>


					</table>

					<br><br>


				
					<table border="1" id="<?php echo $_competitor;?>_table" cellpadding="5" cellspacing="0" style="width: 1180px;">
						<thead>
							<tr style="background:#e5e5e5;">
								<th align="center" style="width: 110px !important;" ><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'s.date_updated');">Last Fetch</a></th>
								<th align="center">Tier</th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'s.sku');">Sku</a></th>
								<th style="width: 170px !important;" align="center">Item Title</th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'true_cost');">Cost</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'our_price');">Our Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'avg_price');">Avg Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'min_price');">Lowest Price</a></th>
								<th align="center" style="width: 110px !important;">60 Price Trend</th>
								<th align="center"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'((our_price-MIN(s.recent_price))/MIN(s.recent_price)*100)');" >% Diff</a></th>
								<th></th>
								<th align="center">Expected</th>
							</tr>									
						</thead>
						<tbody id="<?php echo $_competitor;?>_tbody">
						</tbody>
					</table>
				
			</div>
			<div id="tabTier3" class="makeTabs" >
				<h3 style="color: #337ab7">Tier 3</h3><br>
				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table id="tier_3_counter_table">			
							</table>
						</td>
					</tr>
				</table>
				<?php

				$_competitor = 'tier_3';
				?>
				<form method="post" action="" enctype="multipart/form-data">
				

				
				<input style="width: 80px; margin-bottom: 10px;margin-right: 25px;" type="submit" class="button" name="update_prices" value="Update">
				<a style="margin-bottom: 10px;margin-right: 25px;" class="button" href="#" onClick="window.location='competitor_dashboard.php?export_csv=1&tier_type=3&hide_zero_diff='+$('#<?php echo $_competitor;?>_hide_diff').is(':checked')+'&avg_chk='+$('#<?php echo $_competitor;?>_avg_chk').is(':checked')+'&low_chk='+$('#<?php echo $_competitor;?>_low_chk').is(':checked')+'&avg_select='+$('#<?php echo $_competitor;?>_avg_select').val()+'&low_select='+$('#<?php echo $_competitor;?>_low_select').val()+'&contains_chk='+$('#<?php echo $_competitor;?>_contains_chk').is(':checked')+'&contains_select='+$('#<?php echo $_competitor;?>_contains_select').val()+'&contains_field='+$('#<?php echo $_competitor;?>_contains_text').val()+'&avg_cost_chk='+$('#<?php echo $_competitor;?>_avg_cost_chk').is(':checked')+'&avg_cost_select='+$('#<?php echo $_competitor;?>_avg_cost_select').val()+'&low_cost_chk='+$('#<?php echo $_competitor;?>_low_cost_chk').is(':checked')+'&low_cost_select='+$('#<?php echo $_competitor;?>_low_cost_select').val()">Export CSV</a>


				<br>
				
					<table border="1"  cellpadding="2" cellspacing="0" style="clear:both">
					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_hide_diff">Hide 0% Differnces</td>
					</tr>

					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_contains_chk"> Contains 
					<select id="<?php echo $_competitor;?>_contains_select">
					<option value="contains_above_field">More Than</option>
					<option value="contains_less_field">Less Than</option>
					</select> <input type="text" value="1" id="<?php echo $_competitor;?>_contains_text" style="width:60px"></td>
					</tr>

					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_avg_chk"> Avg Price 
					<select id="<?php echo $_competitor;?>_avg_select">
					<option value="avg_less_price">Less Than</option>
					<option value="avg_above_price">More Than</option>
					</select> Our Price</td>
					</tr>
					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_low_chk"> Low Price 
					<select id="<?php echo $_competitor;?>_low_select">
					<option value="low_less_price">Less Than</option>
					<option value="low_above_price">More Than</option>
					</select> Our Price
					</td>
					</tr>

					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_avg_cost_chk"> Avg Price 
					<select id="<?php echo $_competitor;?>_avg_cost_select">
					<option value="avg_less_cost">Less Than</option>
					<option value="avg_above_cost">More Than</option>
					</select> Cost
					</td>
					</tr>

					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_low_cost_chk"> Low Price 
					<select id="<?php echo $_competitor;?>_low_cost_select">
					<option value="low_less_cost">Less Than</option>
					<option value="low_above_cost">More Than</option>
					</select> Cost
					</td>
					</tr>


					<tr>
					<td align="center"><input type="button" class="button" value="Search" onClick="fetchData('<?php echo $_competitor;?>',0,1);"></td>


					</table>

					<br><br>


				
					<table border="1" id="<?php echo $_competitor;?>_table" cellpadding="5" cellspacing="0" style="width: 1180px;">
						<thead>
							<tr style="background:#e5e5e5;">
								<th align="center" style="width: 110px !important;" ><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'s.date_updated');">Last Fetch</a></th>
								<th align="center">Tier</th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'s.sku');">Sku</a></th>
								<th style="width: 170px !important;" align="center">Item Title</th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'true_cost');">Cost</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'our_price');">Our Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'avg_price');">Avg Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'min_price');">Lowest Price</a></th>
								<th align="center" style="width: 110px !important;">60 Price Trend</th>
								<th align="center"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'((our_price-MIN(s.recent_price))/MIN(s.recent_price)*100)');" >% Diff</a></th>
								<th></th>
								<th align="center">Expected</th>
							</tr>									
						</thead>
						<tbody id="<?php echo $_competitor;?>_tbody">
						</tbody>
					</table>
				
				</form>
			</div>
			
			<div id="tabMobileSentrix" class="makeTabs" >
				<h3 style="color: #337ab7">Mobile Sentrix</h3><br>
				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table id="mobile_sentrix_counter_table">			
							</table>
						</td>
					</tr>
				</table>
				<form method="post" action="" enctype="multipart/form-data">
				
				
				<input style="width: 80px; margin-bottom: 10px;margin-right: 20px;" type="submit" class="button" name="update_wrong_link" value="Wrong Link">


				<?php
			$_competitor = 'mobile_sentrix';
			?>

				<input style="width: 80px; margin-bottom: 10px;margin-right: 10px;" type="submit" class="button" name="update_prices" value="Update">

				<a style="margin-bottom: 10px;margin-right: 10px;" class="button" href="#" onClick="window.location='competitor_dashboard.php?export_csv=1&competitor=<?php echo $_competitor;?>&hide_zero_diff='+$('#<?php echo $_competitor;?>_hide_diff').is(':checked')+'&sku_group='+$('#<?php echo $_competitor;?>_sku_select').val()+'&tier_type='+$('#<?php echo $_competitor;?>_tier_select').val()">Export CSV</a>




				<br><br>
				<table border="1"  cellpadding="2" cellspacing="0" style="clear:both">
					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_hide_diff">Hide 0% Differnces</td>
					</tr>
					<tr>
					<td>
					Tier  
					<select id="<?php echo $_competitor;?>_tier_select">
					<option value="">Show All</option>
					<option value="1">Tier 1</option>
					<option value="2">Tier 2</option>
					<option value="3">Tier 3</option>
					</select> </td>
					</tr>
					<tr>
					<td>
					SKU Group 
					<select id="<?php echo $_competitor;?>_sku_select">
						<option value="">Show All</option>
						<?php
						foreach($sku_groups as $sku_group)
						{
							?>
							<option value="<?php echo $sku_group['sku'];?>"><?php echo $sku_group['sku'];?></option>
							<?php
						}
						?>
					</select> 
					</td>
					</tr>
					<tr>
					<td align="center"><input type="button" class="button" value="Search" onClick="fetchData('<?php echo $_competitor;?>',0,1);"></td>


					</table>
					<br>

				
					<table border="1" id="mobile_sentrix_table" cellpadding="5" cellspacing="0" style="width: 1180px;">
						<thead>
						
							<tr style="background:#e5e5e5;">
								<th align="center" style="width: 110px !important;" ><a href="#" onclick="fetchData('mobile_sentrix',0,1,'s.date_updated');">Last Fetch</a></th>
								<th align="center"><a href="#" onclick="fetchData('mobile_sentrix',0,1,'p.tier');">Tier</a></th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('mobile_sentrix',0,1,'s.sku');">Sku</a></th>
								<th style="width: 170px !important;" align="center">Item Title</th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'true_cost');">Cost</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('mobile_sentrix',0,1,'our_price');">Our Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('mobile_sentrix',0,1,'old_price');">Old Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('mobile_sentrix',0,1,'new_price');">New Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('mobile_sentrix',0,1,'((new_price/old_price*100)-100)');">% Change</a></th>
								<th align="center" style="width: 110px !important;">60 Price Trend</th>
								<th align="center"><a href="#" onclick="fetchData('mobile_sentrix',0,1,'((our_price-new_price)/new_price)');">% Diff</a></th>
								<th></th>
								<th align="center">Expected</th>
							</tr>									
						</thead>
						<tbody id="mobile_sentrix_tbody">
						</tbody>
					</table>
				
				</form>
			</div>
			<div id="tabFixez" class="makeTabs" >
				<h3 style="color: #337ab7">FixEZ</h3><br>
				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table id="fixez_counter_table">			
							</table>
						</td>
					</tr>
				</table>
				<form method="post" action="" enctype="multipart/form-data">
				
				

				<input style="width: 80px; margin-bottom: 10px;margin-right: 20px;" type="submit" class="button" name="update_wrong_link" value="Wrong Link">

				<?php
			$_competitor = 'fixez';
			?>

				<input style="width: 80px; margin-bottom: 10px;margin-right: 10px;" type="submit" class="button" name="update_prices" value="Update">

				<a style="margin-bottom: 10px;margin-right: 10px;" class="button" href="#" onClick="window.location='competitor_dashboard.php?export_csv=1&competitor=<?php echo $_competitor;?>&hide_zero_diff='+$('#<?php echo $_competitor;?>_hide_diff').is(':checked')+'&sku_group='+$('#<?php echo $_competitor;?>_sku_select').val()+'&tier_type='+$('#<?php echo $_competitor;?>_tier_select').val()">Export CSV</a>



				<br><br>
				<table border="1"  cellpadding="2" cellspacing="0" style="clear:both">
					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_hide_diff">Hide 0% Differnces</td>
					</tr>
					<tr>
					<td>
					Tier  
					<select id="<?php echo $_competitor;?>_tier_select">
					<option value="">Show All</option>
					<option value="1">Tier 1</option>
					<option value="2">Tier 2</option>
					<option value="3">Tier 3</option>
					</select> </td>
					</tr>
					<tr>
					<td>
					SKU Group 
					<select id="<?php echo $_competitor;?>_sku_select">
						<option value="">Show All</option>
						<?php
						foreach($sku_groups as $sku_group)
						{
							?>
							<option value="<?php echo $sku_group['sku'];?>"><?php echo $sku_group['sku'];?></option>
							<?php
						}
						?>
					</select> 
					</td>
					</tr>
					<tr>
					<td align="center"><input type="button" class="button" value="Search" onClick="fetchData('<?php echo $_competitor;?>',0,1);"></td>


					</table>
					<br>

				
					<table border="1" id="fixez_table" cellpadding="5" cellspacing="0" style="width: 1180px;">
						<thead>
							<tr style="background:#e5e5e5;">
								<th align="center" style="width: 110px !important;" ><a href="#" onclick="fetchData('fixez',0,1,'s.date_updated');">Last Fetch</a></th>
								<th align="center"><a href="#" onclick="fetchData('fixez',0,1,'p.tier');">Tier</a></th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('fixez',0,1,'s.sku');">Sku</a></th>
								<th style="width: 170px !important;" align="center">Item Title</th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'true_cost');">Cost</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('fixez',0,1,'our_price');">Our Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('fixez',0,1,'old_price');">Old Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('fixez',0,1,'new_price');">New Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('fixez',0,1,'((new_price/old_price*100)-100)');">% Change</a></th>
								<th align="center" style="width: 110px !important;">60 Price Trend</th>
								<th align="center"><a href="#" onclick="fetchData('fixez',0,1,'((our_price-new_price)/new_price)');">% Diff</a></th>
								<th></th>
								<th align="center">Expected</th>
							</tr>									
						</thead>
						<tbody id="fixez_tbody">
						</tbody>
					</table>
				
				</form>
			</div>
			<div id="tabMengtor" class="makeTabs" >
				<h3 style="color: #337ab7">Mengtor</h3><br>
				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table id="mengtor_counter_table">			
							</table>
						</td>
					</tr>
				</table>
				<form method="post" action="" enctype="multipart/form-data">
				
				

				<input style="width: 80px; margin-bottom: 10px;margin-right: 20px;" type="submit" class="button" name="update_wrong_link" value="Wrong Link">

				<?php
			$_competitor = 'mengtor';
			?>

				<input style="width: 80px; margin-bottom: 10px;margin-right: 10px;" type="submit" class="button" name="update_prices" value="Update">

				<a style="margin-bottom: 10px;margin-right: 10px;" class="button" href="#" onClick="window.location='competitor_dashboard.php?export_csv=1&competitor=<?php echo $_competitor;?>&hide_zero_diff='+$('#<?php echo $_competitor;?>_hide_diff').is(':checked')+'&sku_group='+$('#<?php echo $_competitor;?>_sku_select').val()+'&tier_type='+$('#<?php echo $_competitor;?>_tier_select').val()">Export CSV</a>



				<br><br>
				<table border="1"  cellpadding="2" cellspacing="0" style="clear:both">
					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_hide_diff">Hide 0% Differnces</td>
					</tr>
					<tr>
					<td>
					Tier  
					<select id="<?php echo $_competitor;?>_tier_select">
					<option value="">Show All</option>
					<option value="1">Tier 1</option>
					<option value="2">Tier 2</option>
					<option value="3">Tier 3</option>
					</select> </td>
					</tr>
					<tr>
					<td>
					SKU Group 
					<select id="<?php echo $_competitor;?>_sku_select">
						<option value="">Show All</option>
						<?php
						foreach($sku_groups as $sku_group)
						{
							?>
							<option value="<?php echo $sku_group['sku'];?>"><?php echo $sku_group['sku'];?></option>
							<?php
						}
						?>
					</select> 
					</td>
					</tr>
					<tr>
					<td align="center"><input type="button" class="button" value="Search" onClick="fetchData('<?php echo $_competitor;?>',0,1);"></td>


					</table>
					<br>

				
					<table border="1" id="mengtor_table" cellpadding="5" cellspacing="0" style="width: 1180px;">
						<thead>
						<tr style="background:#e5e5e5;">
								<th align="center" style="width: 110px !important;" ><a href="#" onclick="fetchData('mengtor',0,1,'s.date_updated');">Last Fetch</a></th>
								<th align="center"><a href="#" onclick="fetchData('mengtor',0,1,'p.tier');">Tier</a></th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('mengtor',0,1,'s.sku');">Sku</a></th>
								<th style="width: 170px !important;" align="center">Item Title</th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'true_cost');">Cost</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('mengtor',0,1,'our_price');">Our Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('mengtor',0,1,'old_price');">Old Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('mengtor',0,1,'new_price');">New Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('mengtor',0,1,'((new_price/old_price*100)-100)');">% Change</a></th>
								<th align="center" style="width: 110px !important;">60 Price Trend</th>
								<th align="center"><a href="#" onclick="fetchData('mengtor',0,1,'((our_price-new_price)/new_price)');">% Diff</a></th>
								<th></th>
								<th align="center">Expected</th>
							</tr>					
						</thead>
						<tbody id="mengtor_tbody">
						</tbody>
					</table>
				
				</form>
			</div>
			<div id="tabMobileDefenders" class="makeTabs" >
				<h3 style="color: #337ab7">Mobile Defenders</h3><br>
				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table id="mobile_defenders_counter_table">			
							</table>
						</td>
					</tr>
				</table>
				<form method="post" action="" enctype="multipart/form-data">
				
				

				<input style="width: 80px; margin-bottom: 10px;margin-right: 20px;" type="submit" class="button" name="update_wrong_link" value="Wrong Link">

				<?php
			$_competitor = 'mobile_defenders';
			?>


				<input style="width: 80px; margin-bottom: 10px;margin-right: 10px;" type="submit" class="button" name="update_prices" value="Update">
				<a style="margin-bottom: 10px;margin-right: 10px;" class="button" href="#" onClick="window.location='competitor_dashboard.php?export_csv=1&competitor=<?php echo $_competitor;?>&hide_zero_diff='+$('#<?php echo $_competitor;?>_hide_diff').is(':checked')+'&sku_group='+$('#<?php echo $_competitor;?>_sku_select').val()+'&tier_type='+$('#<?php echo $_competitor;?>_tier_select').val()">Export CSV</a>



				<br><br>
				<table border="1"  cellpadding="2" cellspacing="0" style="clear:both">
					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_hide_diff">Hide 0% Differnces</td>
					</tr>
					<tr>
					<td>
					Tier  
					<select id="<?php echo $_competitor;?>_tier_select">
					<option value="">Show All</option>
					<option value="1">Tier 1</option>
					<option value="2">Tier 2</option>
					<option value="3">Tier 3</option>
					</select> </td>
					</tr>
					<tr>
					<td>
					SKU Group 
					<select id="<?php echo $_competitor;?>_sku_select">
						<option value="">Show All</option>
						<?php
						foreach($sku_groups as $sku_group)
						{
							?>
							<option value="<?php echo $sku_group['sku'];?>"><?php echo $sku_group['sku'];?></option>
							<?php
						}
						?>
					</select> 
					</td>
					</tr>
					<tr>
					<td align="center"><input type="button" class="button" value="Search" onClick="fetchData('<?php echo $_competitor;?>',0,1);"></td>


					</table>
					<br>

				
					<table border="1" id="mobile_defenders_table" cellpadding="5" cellspacing="0" style="width: 1180px;">
						<thead>
							<tr style="background:#e5e5e5;">
								<th align="center" style="width: 110px !important;" ><a href="#" onclick="fetchData('mobile_defenders',0,1,'s.date_updated');">Last Fetch</a></th>
								<th align="center"><a href="#" onclick="fetchData('mobile_defenders',0,1,'p.tier');">Tier</a></th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('mobile_defenders',0,1,'s.sku');">Sku</a></th>
								<th style="width: 170px !important;" align="center">Item Title</th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'true_cost');">Cost</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('mobile_defenders',0,1,'our_price');">Our Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('mobile_defenders',0,1,'old_price');">Old Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('mobile_defenders',0,1,'new_price');">New Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('mobile_defenders',0,1,'((new_price/old_price*100)-100)');">% Change</a></th>
								<th align="center" style="width: 110px !important;">60 Price Trend</th>
								<th align="center"><a href="#" onclick="fetchData('mobile_defenders',0,1,'((our_price-new_price)/new_price)');">% Diff</a></th>
								<th></th>
								<th align="center">Expected</th>
							</tr>								
						</thead>
						<tbody id="mobile_defenders_tbody">
						</tbody>
					</table>
				
				</form>
			</div>
			<div id="tabEtrade" class="makeTabs" >
				<h3 style="color: #337ab7">E-Trade Supply</h3><br>
				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table id="etrade_supply_counter_table">			
							</table>
						</td>
					</tr>
				</table>
				<form method="post" action="" enctype="multipart/form-data">
				
				

				<input style="width: 80px; margin-bottom: 10px;margin-right: 20px;" type="submit" class="button" name="update_wrong_link" value="Wrong Link">

				<?php
			$_competitor = 'etrade_supply';
			?>


				<input style="width: 80px; margin-bottom: 10px;margin-right: 10px;" type="submit" class="button" name="update_prices" value="Update">
				<a style="margin-bottom: 10px;margin-right: 10px;" class="button" href="#" onClick="window.location='competitor_dashboard.php?export_csv=1&competitor=<?php echo $_competitor;?>&hide_zero_diff='+$('#<?php echo $_competitor;?>_hide_diff').is(':checked')+'&sku_group='+$('#<?php echo $_competitor;?>_sku_select').val()+'&tier_type='+$('#<?php echo $_competitor;?>_tier_select').val()">Export CSV</a>



				<br><br>
				<table border="1"  cellpadding="2" cellspacing="0" style="clear:both">
					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_hide_diff">Hide 0% Differnces</td>
					</tr>
					<tr>
					<td>
					Tier  
					<select id="<?php echo $_competitor;?>_tier_select">
					<option value="">Show All</option>
					<option value="1">Tier 1</option>
					<option value="2">Tier 2</option>
					<option value="3">Tier 3</option>
					</select> </td>
					</tr>
					<tr>
					<td>
					SKU Group 
					<select id="<?php echo $_competitor;?>_sku_select">
						<option value="">Show All</option>
						<?php
						foreach($sku_groups as $sku_group)
						{
							?>
							<option value="<?php echo $sku_group['sku'];?>"><?php echo $sku_group['sku'];?></option>
							<?php
						}
						?>
					</select> 
					</td>
					</tr>
					<tr>
					<td align="center"><input type="button" class="button" value="Search" onClick="fetchData('<?php echo $_competitor;?>',0,1);"></td>


					</table>
					<br>

				
					<table border="1" id="etrade_supply_table" cellpadding="5" cellspacing="0" style="width: 1180px;">
						<thead>
							<tr style="background:#e5e5e5;">
								<th align="center" style="width: 110px !important;" ><a href="#" onclick="fetchData('etrade_supply',0,1,'s.date_updated');">Last Fetch</a></th>
								<th align="center"><a href="#" onclick="fetchData('etrade_supply',0,1,'p.tier');">Tier</a></th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('etrade_supply',0,1,'s.sku');">Sku</a></th>
								<th style="width: 170px !important;" align="center">Item Title</th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'true_cost');">Cost</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('etrade_supply',0,1,'our_price');">Our Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('etrade_supply',0,1,'old_price');">Old Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('etrade_supply',0,1,'new_price');">New Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('etrade_supply',0,1,'((new_price/old_price*100)-100)');">% Change</a></th>
								<th align="center" style="width: 110px !important;">60 Price Trend</th>
								<th align="center"><a href="#" onclick="fetchData('etrade_supply',0,1,'((our_price-new_price)/new_price)');">% Diff</a></th>
								<th></th>
								<th align="center">Expected</th>
							</tr>									
						</thead>
						<tbody id="etrade_supply_tbody">
						</tbody>
					</table>
				
				</form>
			</div>
			<div id="tabMaya" class="makeTabs" >
				<h3 style="color: #337ab7">Maya Cellular</h3><br>
				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table id="maya_cellular_counter_table">			
							</table>
						</td>
					</tr>
				</table>
				<form method="post" action="" enctype="multipart/form-data">
				
				

				<input style="width: 80px; margin-bottom: 10px;margin-right: 20px;" type="submit" class="button" name="update_wrong_link" value="Wrong Link">

				<?php
			$_competitor = 'maya_cellular';
			?>


				<input style="width: 80px; margin-bottom: 10px;margin-right: 10px;" type="submit" class="button" name="update_prices" value="Update">
				<a style="margin-bottom: 10px;margin-right: 10px;" class="button" href="#" onClick="window.location='competitor_dashboard.php?export_csv=1&competitor=<?php echo $_competitor;?>&hide_zero_diff='+$('#<?php echo $_competitor;?>_hide_diff').is(':checked')+'&sku_group='+$('#<?php echo $_competitor;?>_sku_select').val()+'&tier_type='+$('#<?php echo $_competitor;?>_tier_select').val()">Export CSV</a>



				<br><br>
				<table border="1"  cellpadding="2" cellspacing="0" style="clear:both">
					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_hide_diff">Hide 0% Differnces</td>
					</tr>
					<tr>
					<td>
					Tier  
					<select id="<?php echo $_competitor;?>_tier_select">
					<option value="">Show All</option>
					<option value="1">Tier 1</option>
					<option value="2">Tier 2</option>
					<option value="3">Tier 3</option>
					</select> </td>
					</tr>
					<tr>
					<td>
					SKU Group 
					<select id="<?php echo $_competitor;?>_sku_select">
						<option value="">Show All</option>
						<?php
						foreach($sku_groups as $sku_group)
						{
							?>
							<option value="<?php echo $sku_group['sku'];?>"><?php echo $sku_group['sku'];?></option>
							<?php
						}
						?>
					</select> 
					</td>
					</tr>
					<tr>
					<td align="center"><input type="button" class="button" value="Search" onClick="fetchData('<?php echo $_competitor;?>',0,1);"></td>


					</table>
					<br>

				
					<table border="1" id="maya_cellular_table" cellpadding="5" cellspacing="0" style="width: 1180px;">
						<thead>
							<tr style="background:#e5e5e5;">
								<th align="center" style="width: 110px !important;" ><a href="#" onclick="fetchData('maya_cellular',0,1,'s.date_updated');">Last Fetch</a></th>
								<th align="center"><a href="#" onclick="fetchData('maya_cellular',0,1,'p.tier');">Tier</a></th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('maya_cellular',0,1,'s.sku');">Sku</a></th>
								<th style="width: 170px !important;" align="center">Item Title</th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'true_cost');">Cost</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('maya_cellular',0,1,'our_price');">Our Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('maya_cellular',0,1,'old_price');">Old Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('maya_cellular',0,1,'new_price');">New Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('maya_cellular',0,1,'((new_price/old_price*100)-100)');">% Change</a></th>
								<th align="center" style="width: 110px !important;">60 Price Trend</th>
								<th align="center"><a href="#" onclick="fetchData('maya_cellular',0,1,'((our_price-new_price)/new_price)');">% Diff</a></th>
								<th></th>
								<th align="center">Expected</th>
							</tr>								
						</thead>
						<tbody id="maya_cellular_tbody">
						</tbody>
					</table>
				
				</form>
			</div>
			<div id="tabLcdLoop" class="makeTabs" >
				<h3 style="color: #337ab7">LCD Loop</h3><br>
				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table id="lcd_loop_counter_table">			
							</table>
						</td>
					</tr>
				</table>
				<form method="post" action="" enctype="multipart/form-data">
				
				

				<input style="width: 80px; margin-bottom: 10px;margin-right: 20px;" type="submit" class="button" name="update_wrong_link" value="Wrong Link">

				<?php
			$_competitor = 'lcd_loop';
			?>

				<input style="width: 80px; margin-bottom: 10px;margin-right: 10px;" type="submit" class="button" name="update_prices" value="Update">

				<a style="margin-bottom: 10px;margin-right: 10px;" class="button" href="#" onClick="window.location='competitor_dashboard.php?export_csv=1&competitor=<?php echo $_competitor;?>&hide_zero_diff='+$('#<?php echo $_competitor;?>_hide_diff').is(':checked')+'&sku_group='+$('#<?php echo $_competitor;?>_sku_select').val()+'&tier_type='+$('#<?php echo $_competitor;?>_tier_select').val()">Export CSV</a>



				<br><br>
				<table border="1"  cellpadding="2" cellspacing="0" style="clear:both">
					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_hide_diff">Hide 0% Differnces</td>
					</tr>
					<tr>
					<td>
					Tier  
					<select id="<?php echo $_competitor;?>_tier_select">
					<option value="">Show All</option>
					<option value="1">Tier 1</option>
					<option value="2">Tier 2</option>
					<option value="3">Tier 3</option>
					</select> </td>
					</tr>
					<tr>
					<td>
					SKU Group 
					<select id="<?php echo $_competitor;?>_sku_select">
						<option value="">Show All</option>
						<?php
						foreach($sku_groups as $sku_group)
						{
							?>
							<option value="<?php echo $sku_group['sku'];?>"><?php echo $sku_group['sku'];?></option>
							<?php
						}
						?>
					</select> 
					</td>
					</tr>
					<tr>
					<td align="center"><input type="button" class="button" value="Search" onClick="fetchData('<?php echo $_competitor;?>',0,1);"></td>


					</table>
					<br>


				
					<table border="1" id="lcd_loop_table" cellpadding="5" cellspacing="0" style="width: 1180px;">
						<thead>
							<tr style="background:#e5e5e5;">
								<th align="center" style="width: 110px !important;" ><a href="#" onclick="fetchData('lcd_loop',0,1,'s.date_updated');">Last Fetch</a></th>
								<th align="center"><a href="#" onclick="fetchData('lcd_loop',0,1,'p.tier');">Tier</a></th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('lcd_loop',0,1,'s.sku');">Sku</a></th>
								<th style="width: 170px !important;" align="center">Item Title</th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'true_cost');">Cost</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('lcd_loop',0,1,'our_price');">Our Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('lcd_loop',0,1,'old_price');">Old Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('lcd_loop',0,1,'new_price');">New Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('lcd_loop',0,1,'((new_price/old_price*100)-100)');">% Change</a></th>
								<th align="center" style="width: 110px !important;">60 Price Trend</th>
								<th align="center"><a href="#" onclick="fetchData('lcd_loop',0,1,'((our_price-new_price)/new_price)');">% Diff</a></th>
								<th></th>
								<th align="center">Expected</th>
							</tr>									
						</thead>
						<tbody id="lcd_loop_tbody">
						</tbody>
					</table>
				
				</form>
			</div>
			<div id="tabParts4Cell" class="makeTabs" >
				<h3 style="color: #337ab7">Parts 4 Cell</h3><br>
				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table id="parts_4_cells_counter_table">			
							</table>
						</td>
					</tr>
				</table>
				<form method="post" action="" enctype="multipart/form-data">
				
				

				<input style="width: 80px; margin-bottom: 10px;margin-right: 20px;" type="submit" class="button" name="update_wrong_link" value="Wrong Link">

				<?php
			$_competitor = 'parts_4_cells';
			?>


				<input style="width: 80px; margin-bottom: 10px;margin-right: 10px;" type="submit" class="button" name="update_prices" value="Update">
				<a style="margin-bottom: 10px;margin-right: 10px;" class="button" href="#" onClick="window.location='competitor_dashboard.php?export_csv=1&competitor=<?php echo $_competitor;?>&hide_zero_diff='+$('#<?php echo $_competitor;?>_hide_diff').is(':checked')+'&sku_group='+$('#<?php echo $_competitor;?>_sku_select').val()+'&tier_type='+$('#<?php echo $_competitor;?>_tier_select').val()">Export CSV</a>





				<br><br>
				<table border="1"  cellpadding="2" cellspacing="0" style="clear:both">
					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_hide_diff">Hide 0% Differnces</td>
					</tr>
					<tr>
					<td>
					Tier  
					<select id="<?php echo $_competitor;?>_tier_select">
					<option value="">Show All</option>
					<option value="1">Tier 1</option>
					<option value="2">Tier 2</option>
					<option value="3">Tier 3</option>
					</select> </td>
					</tr>
					<tr>
					<td>
					SKU Group 
					<select id="<?php echo $_competitor;?>_sku_select">
						<option value="">Show All</option>
						<?php
						foreach($sku_groups as $sku_group)
						{
							?>
							<option value="<?php echo $sku_group['sku'];?>"><?php echo $sku_group['sku'];?></option>
							<?php
						}
						?>
					</select> 
					</td>
					</tr>
					<tr>
					<td align="center"><input type="button" class="button" value="Search" onClick="fetchData('<?php echo $_competitor;?>',0,1);"></td>


					</table>
					<br>

				
					<table border="1" id="parts_4_cells_table" cellpadding="5" cellspacing="0" style="width: 1180px;">
						<thead>
							<tr style="background:#e5e5e5;">
								<th align="center" style="width: 110px !important;" ><a href="#" onclick="fetchData('parts_4_cells',0,1,'s.date_updated');">Last Fetch</a></th>
								<th align="center"><a href="#" onclick="fetchData('parts_4_cells',0,1,'p.tier');">Tier</a></th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('parts_4_cells',0,1,'s.sku');">Sku</a></th>
								<th style="width: 170px !important;" align="center">Item Title</th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'true_cost');">Cost</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('parts_4_cells',0,1,'our_price');">Our Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('parts_4_cells',0,1,'old_price');">Old Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('parts_4_cells',0,1,'new_price');">New Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('parts_4_cells',0,1,'((new_price/old_price*100)-100)');">% Change</a></th>
								<th align="center" style="width: 110px !important;">60 Price Trend</th>
								<th align="center"><a href="#" onclick="fetchData('parts_4_cells',0,1,'((our_price-new_price)/new_price)');">% Diff</a></th>
								<th></th>
								<th align="center">Expected</th>
							</tr>									
						</thead>
						<tbody id="parts_4_cells_tbody">
						</tbody>
					</table>
				
				</form>
			</div>
			<div id="tabCellPartsHub" class="makeTabs" >
				<h3 style="color: #337ab7">Cell Parts Hub</h3><br>
				<table align="center" width="100%" >
					<tr>
						<td align="center">
							<table id="cell_parts_hub_counter_table">			
							</table>
						</td>
					</tr>
				</table>
				<form method="post" action="" enctype="multipart/form-data">
				
				

				<input style="width: 80px; margin-bottom: 10px;margin-right: 20px;" type="submit" class="button" name="update_wrong_link" value="Wrong Link">

				<?php
			$_competitor = 'cell_parts_hub';
			?>


				<input style="width: 80px; margin-bottom: 10px;margin-right: 10px;" type="submit" class="button" name="update_prices" value="Update">
				<a style="margin-bottom: 10px;margin-right: 10px;" class="button" href="#" onClick="window.location='competitor_dashboard.php?export_csv=1&competitor=<?php echo $_competitor;?>&hide_zero_diff='+$('#<?php echo $_competitor;?>_hide_diff').is(':checked')+'&sku_group='+$('#<?php echo $_competitor;?>_sku_select').val()+'&tier_type='+$('#<?php echo $_competitor;?>_tier_select').val()">Export CSV</a>



				<br><br>
				<table border="1"  cellpadding="2" cellspacing="0" style="clear:both">
					<tr>
					<td>
					<input type="checkbox" id="<?php echo $_competitor;?>_hide_diff">Hide 0% Differnces</td>
					</tr>
					<tr>
					<td>
					Tier  
					<select id="<?php echo $_competitor;?>_tier_select">
					<option value="">Show All</option>
					<option value="1">Tier 1</option>
					<option value="2">Tier 2</option>
					<option value="3">Tier 3</option>
					</select> </td>
					</tr>
					<tr>
					<td>
					SKU Group 
					<select id="<?php echo $_competitor;?>_sku_select">
						<option value="">Show All</option>
						<?php
						foreach($sku_groups as $sku_group)
						{
							?>
							<option value="<?php echo $sku_group['sku'];?>"><?php echo $sku_group['sku'];?></option>
							<?php
						}
						?>
					</select> 
					</td>
					</tr>
					<tr>
					<td align="center"><input type="button" class="button" value="Search" onClick="fetchData('<?php echo $_competitor;?>',0,1);"></td>


					</table>
					<br>



				
					<table border="1" id="cell_parts_hub_table" cellpadding="5" cellspacing="0" style="width: 1180px;">
						<thead>
							<tr style="background:#e5e5e5;">
								<th align="center" style="width: 110px !important;" ><a href="#" onclick="fetchData('cell_parts_hub',0,1,'s.date_updated');">Last Fetch</a></th>
								<th align="center"><a href="#" onclick="fetchData('cell_parts_hub',0,1,'p.tier');">Tier</a></th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('cell_parts_hub',0,1,'s.sku');">Sku</a></th>
								<th style="width: 170px !important;" align="center">Item Title</th>
								<th style="width: 110px !important;" align="center"><a href="#" onclick="fetchData('<?php echo $_competitor;?>',0,1,'true_cost');">Cost</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('cell_parts_hub',0,1,'our_price');">Our Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('cell_parts_hub',0,1,'old_price');">Old Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('cell_parts_hub',0,1,'new_price');">New Price</a></th>
								<th align="center" style="width: 110px !important;"><a href="#" onclick="fetchData('cell_parts_hub',0,1,'((new_price/old_price*100)-100)');">% Change</a></th>
								<th align="center" style="width: 110px !important;">60 Price Trend</th>
								<th align="center"><a href="#" onclick="fetchData('cell_parts_hub',0,1,'((our_price-new_price)/new_price)');">% Diff</a></th>
								<th></th>
								<th align="center">Expected</th>
							</tr>								
						</thead>
						<tbody id="cell_parts_hub_tbody">
						</tbody>
					</table>
				
				</form>
			</div>

		</div>
			
	</div>		
	<script type="text/javascript">
		function fixLink(key,table){
			 $('#'+key+'_'+table).hide();
			 $('#'+key+'_'+table+'_fixed').show();
			 $('#row_'+key+'_'+table).css("background-color", "#ffffff");
		}
	</script>
	<script type="text/javascript">
		function fixBrokenLink(key){
			var sku  = $('#'+key+'_sku_broken').val();
			var type  = $('#'+key+'_type_broken').val();
			var id  = $('#'+key+'_item_id_broken').val();

			$.ajax({
				url: 'competitor_dashboard.php?add_fix_comment=1&fixed_sku='+sku+'&comp_type='+type+'&item_id='+id,
				type: 'GET',
				dataType : 'json',
				beforeSend: function () {

				},
				complete: function () {

				},
				success: function(json){
					$('#'+sku+'_'+type+'_broken').attr('disabled','');
					$('#'+sku+'_'+type+'_broken_fixed').show();
					$('#'+sku+'_'+type+'_broken_fixed').parent().parent().hide(500);
				}
			});
		}
	</script>
	<script type="text/javascript">
	var sort_order = 'ASC';
	// var show_data = 'all';
	$(document).on('click','.pagination_link',function(){
		fetchData($(this).attr('data-competitor'),$(this).attr('data-last_item_date'),$(this).attr('data-page'),$(this).attr('data-sort_by'),$(this).attr('data-sort_order'));
	});

	$(document).on('click','.pagination_link2',function(){
		fetchNeedLinks($(this).attr('data-page'),$(this).attr('data-sort_by'),$(this).attr('data-sort_order'));
	});

	$(document).on('change','.tier_filter',function(){
		// show_data = $(this).val();
		// alert(show_data);
		fetchData($(this).attr('data-comp'),0,1);
	});
	
		function fetchData(competitor,last_item_date,page,sort_by,temp_bit){
			 last_item_date = last_item_date || 0;
			 page = page || 1;
			 sort_by = sort_by || 's.date_updated';
			 
			 hide_zero_diff = $('#'+competitor+'_hide_diff').is(':checked');
			 avg_chk = $('#'+competitor+'_avg_chk').is(':checked');
			 low_chk = $('#'+competitor+'_low_chk').is(':checked');
			 avg_cost_chk = $('#'+competitor+'_avg_cost_chk').is(':checked');
			 low_cost_chk = $('#'+competitor+'_low_cost_chk').is(':checked');


			avg_select = $('#'+competitor+'_avg_select').val();			 
			low_select = $('#'+competitor+'_low_select').val();	
			avg_cost_select = $('#'+competitor+'_avg_cost_select').val();	
			low_cost_select = $('#'+competitor+'_low_cost_select').val();	

			tier_val = $('#'+competitor+'_tier_select').val();	 
			sku_group = $('#'+competitor+'_sku_select').val();

			contains_chk = $('#'+competitor+'_contains_chk').is(':checked');	
			contains_select = $('#'+competitor+'_contains_select').val();	 
			contains_field = $('#'+competitor+'_contains_text').val();	 

			 
			 if(sort_order=='ASC')
			 {
			 	sort_order = 'DESC';
			 }
			 else
			 {
			 	sort_order = 'ASC';
			 }
			 temp_bit = temp_bit || sort_order;

			// if($.trim($("#"+competitor+"_tbody").html())=='' || last_item_date!=0 ) {
			$.ajax({
				url: 'competitor_dashboard.php?comp_name='+competitor+'&last_item_date='+last_item_date+"&page="+page+'&sort_by='+sort_by+'&sort_order='+temp_bit+'&hide_zero_diff='+hide_zero_diff+'&avg_chk='+avg_chk+'&low_chk='+low_chk+'&avg_select='+avg_select+'&low_select='+low_select+'&tier_val='+tier_val+'&sku_group='+sku_group+'&contains_chk='+contains_chk+'&contains_select='+contains_select+'&contains_field='+contains_field+'&avg_cost_chk='+avg_cost_chk+'&low_cost_chk='+low_cost_chk+'&avg_cost_select='+avg_cost_select+'&low_cost_select='+low_cost_select,
				type: 'GET',
				dataType : 'json',

				// async:false,
				beforeSend: function () {
					$('#xcontent').show();
					if (last_item_date == 0) {
						// $('#'+competitor+'_div').hide();
						// $('#'+competitor+'_loader').show();
					}
				},
				complete: function () {
					$('#xcontent').hide();
					if (last_item_date == 0) {	
						// $('#'+competitor+'_loader').hide();
						// $('#'+competitor+'_div').show();
					}
					// $('#'+competitor+'_table').addClass('tablesorter');
			        // $('#'+competitor+'_table').tablesorter();
				},
				success: function(json){
					if (json['success']) {
						var html = '';
						for (var i = 0; i < json['competitor_data'].length; i++) {
							html+='<tr>';
							html+='<td align="center">'+json['competitor_data'][i]['last_fetch']+'</td>';
							html+='<td align="center">'+json['competitor_data'][i]['tier']+'</td>';
							html+='<td align="center">'+json['competitor_data'][i]['sku']+'<input type="hidden" style="width:40px;" id="sku_'+json['competitor_data'][i]['product_id']+'" value="'+json['competitor_data'][i]['simple_sku']+'"></td>';
							html+='<td align="center">'+json['competitor_data'][i]['title']+'<input type="hidden" style="width:40px;" id="title_'+json['competitor_data'][i]['product_id']+'" value="'+json['competitor_data'][i]['title']+'"></td>';
							html+='<td align="center">'+json['competitor_data'][i]['true_cost']+'</td>';
							html+='<td align="center">'+'$'+json['competitor_data'][i]['our_price']+'<input type="hidden" style="width:40px;" id="our_price_'+json['competitor_data'][i]['product_id']+'" value="'+json['competitor_data'][i]['our_price']+'">'+'</td>';
							if (competitor == 'tier_1' || competitor == 'tier_2' || competitor == 'tier_3' || competitor == 'all') {
								html+='<td align="center">$'+json['competitor_data'][i]['avg_price']+'</td>';
								html+='<td align="center">'+json['competitor_data'][i]['lowest_price']+'</td>';
							} else {
								html+='<td align="center">$'+json['competitor_data'][i]['old_price']+'</td>';
								html+='<td align="center">'+((competitor!='tier_1' && competitor!='tier_1' &&  competitor!='tier_3')?'<a href="'+json['competitor_data'][i]['url']+'" target="_blank">':'' )+'$'+json['competitor_data'][i]['new_price']+((competitor!='tier_1' && competitor!='tier_1' &&  competitor!='tier_3')?'</a>':'' )+'</td>';	
							}
							if (competitor != 'tier_1' && competitor != 'tier_2' && competitor != 'tier_3' && competitor != 'all') {
								html+='<td align="center">'+json['competitor_data'][i]['perc_change']+'%</td>';
							}
							html+='<td align="center">'+json['competitor_data'][i]['price_trend']+'</td>';
							html+='<td align="center">'+json['competitor_data'][i]['perc_diff']+'%</td>';
							html+='<td align="center"><input type="checkbox" onclick="addNameAttribute(this,'+json['competitor_data'][i]['product_id']+')" class="item_checkbox" name="items['+json['competitor_data'][i]['product_id']+']" value="'+json['competitor_data'][i]['product_id']+'"></td>';
							html+='<td align="center"><input type="text" style="width:40px;" id="expected_price_'+json['competitor_data'][i]['product_id']+'" value="'+json['competitor_data'][i]['expected_price']+'"></td>';
							html+='</tr>';
						}
						html+='<tr><td colspan="12" align="center">'+json['footer_data']+'</td></tr>';
						if (last_item_date == '0') {
							$('#'+competitor+'_tbody').html(html);
							$('#'+competitor+'_last_item_date').val(json['last_item_date']);
						} else {
							$('#'+competitor+'_tbody').html(html);
							$('#'+competitor+'_last_item_date').val(json['last_item_date']);
			        		// $('#'+competitor+'_table').tablesorter();
						}
						// $('#'+competitor+'_tbody').parent().next('');
						var html2 = '';
						html2+='<tr>';
						html2+='<td>';
						html2+='<font style="font-size:15px;">Total Links: '+json['total']+'</font><br>';
						html2+='<font style="font-size:15px;">30 Days: '+json['days_30']+' Links</font><br>';
						html2+='<font style="font-size:15px;">7 Days: '+json['days_7']+' Links</font><br>';
						html2+='<font style="font-size:15px;">Difference: '+json['remaining']+' Links Remaining</font><br>';
						html2+='<font style="font-size:15px;">24 Hours: '+json['hrs_24']+' Links</font><br>';
						html2+='</td>';
						html2+='</tr>';
						$('#'+competitor+'_counter_table').html(html2);
					} else {
						var html = '';
						html+='<tr>';
						html+='<td align="center" colspan="12"><b> No Data Found</b></td>';
						html+='</tr>';
						if (last_item_date == '0') {
						$('#'+competitor+'_tbody').html(html);
						}
					}
				}
			}); 
			 
			 //}
		}

		function fetchNeedLinks(page,sort_by,temp_bit)
		{
			page = page || 1;
			sort_by = sort_by || 'a.sku';

			 if(sort_order=='ASC')
			 {
			 	sort_order = 'DESC';
			 }
			 else
			 {
			 	sort_order = 'ASC';
			 }
			 temp_bit = temp_bit || sort_order;

			$.ajax({
				url: 'competitor_dashboard.php?need_links=1&page='+page+'&sort_by='+sort_by+'&sort_order='+sort_order,
				type: 'GET',
				dataType : 'json',
				// async:false,
				beforeSend: function () {
					$('#xcontent').show();
					
				},
				complete: function () {
					$('#xcontent').hide();
					
					
				},
				success: function(json){

						if(json['success']==1)
						{
					 		var html = '';
						for (var i = 0; i < json['data'].length; i++) {
							html+='<tr>';
							html+='<td>'+json['data'][i]['tier']+'</td>'
							html+='<td>'+json['data'][i]['date_added']+'</td>'
							html+='<td>'+json['data'][i]['sku']+'</td>'
							html+='<td>'+json['data'][i]['name']+'</td>'
							html+='<td>'+json['data'][i]['price']+'</td>'
							html+='<td>'+json['data'][i]['sale_price']+'</td>'
							html+='</tr>';

						}
						html+='<tr><td colspan="12" align="center">'+json['footer_data']+'</td></tr>';

						$('#need_links_table tbody').html(html);
						

						}
					}
			}); 
		}
	</script>
	<script type="text/javascript">
		function addNameAttribute(obj,product_id){
			if( $(obj).is(':checked')){
				$('#sku_'+product_id).attr("name","sku["+product_id+"]");
				$('#title_'+product_id).attr("name","title["+product_id+"]");
				$('#our_price_'+product_id).attr("name","our_price["+product_id+"]");
				$('#expected_price_'+product_id).attr("name","expected_price["+product_id+"]");
			} else {
				$('#sku_'+product_id).removeAttr("name");
				$('#title_'+product_id).removeAttr("name");
				$('#our_price_'+product_id).removeAttr("name");
				$('#expected_price_'+product_id).removeAttr("name");
			}
		}
	</script>
	<script type="text/javascript">
		function fetchLogs(){
			var start = $('#log_start_date').val();
			var end = $('#log_end_date').val();
			$.ajax({
			url: 'competitor_dashboard.php?get_logs=1&start='+start+'&end='+end,
			type: 'get',
			dataType: 'json',
			beforeSend: function () {
				$('#broken_log_table tbody').html('');
				$('#broken_log_loader').show();
			},
			complete: function () {
				$('#broken_log_loader').hide();
			},
			success: function (json) {

				if (json['success']) {
					var html = '';
					for (var i = 0; i < json['logs'].length; i++) {
						html+='<tr>';
						html+='<td align="center">'+json['logs'][i]['datetime']+'</td>';
						html+='<td align="center">'+json['logs'][i]['sku']+'</td>';
						html+='<td align="center">'+json['logs'][i]['competitor']+'</td>';
						html+='<td align="center">'+json['logs'][i]['comment']+'</td>';
						html+='<td align="center">'+json['logs'][i]['user']+'</td>';
						html+='</tr>';
					}
					$('#broken_log_table tbody').html(html);
				} else {
					var html = '';
					html+='<tr>';
					html+='<td align="center" colspan="6"><b> No Logs Found</b></td>';
					html+='</tr>';
					$('#broken_log_table tbody').html(html);
				}
			}
		});
		}
	</script>
	<script type="text/javascript">
		function fetchNewUrls(){
			var start = $('#url_start_date').val();
			var end = $('#url_end_date').val();
			$.ajax({
			url: 'competitor_dashboard.php?get_new_urls=1&start='+start+'&end='+end,
			type: 'get',
			dataType: 'json',
			beforeSend: function () {
				$('#new_table tbody').html('');
				$('#new_loader').show();
			},
			complete: function () {
				$('#new_loader').hide();
			},
			success: function (json) {

				if (json['success']) {
					var html = '';
					for (var i = 0; i < json['urls'].length; i++) {
						html+='<tr>';
						html+='<td align="center">'+json['urls'][i]['last_fetch']+'</td>';
						html+='<td align="center">'+json['urls'][i]['tier']+'</td>';
						html+='<td align="center">'+json['urls'][i]['sku']+'</td>';
						html+='<td align="center">'+json['urls'][i]['title']+'</td>';
						html+='<td align="center">'+json['urls'][i]['competitor']+'</td>';
						html+='<td align="center">$'+json['urls'][i]['our_price']+'</td>';
						html+='<td align="center">$'+json['urls'][i]['old_price']+'</td>';
						html+='<td align="center">$'+json['urls'][i]['new_price']+'</td>';
						html+='<td align="center">'+json['urls'][i]['perc_change']+'%</td>';
						html+='<td align="center">N/A</td>';
						html+='<td align="center">'+json['urls'][i]['perc_diff']+'%</td>';
						html+='</tr>';
					}
					$('#new_table tbody').html(html);

				} else {
					var html = '';
					html+='<tr>';
					html+='<td align="center" colspan="11"><b> No Record Found</b></td>';
					html+='</tr>';
					$('#new_table tbody').html(html);


				}


			}
		});
		}
	</script>

</body>
 
 <script>
    $(document).ready(function(e) {
    // $(".tablesorter").tablesorter(); 
    $('.toogleTab[data-my="<?php echo $_SESSION['comp_name'];?>"]').click();
     		$('.makeTabs .button[value="Wrong Link"]').css('background-color','#F44336');
     		$('.makeTabs .button:contains("Export CSV")').css('background-color','#795549');
     		

     		$('.makeTabs .button[value="Wrong Link"]').css('float','left');
     		$('.makeTabs .button[value="Wrong Link"]').css('margin-left','20px');
     		$('.makeTabs .button:contains("Export CSV")').css('float','right');

     		$('.makeTabs .button[value="Update"]').css('float','right');
     		// $('.makeTabs .button:contains("Export CSV")').css('background-color','#795549');

     });
</script>
