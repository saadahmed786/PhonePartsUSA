<?php
require_once("../config.php");
require_once("../inc/functions.php");

function curl_proxy_fixez($url) {
	global $db;
	$proxy_array = $db->func_query_first("SELECT * FROM inv_proxy_list where status=1 and is_auth=0 and is_socks5=0 and attempt<3 ORDER BY RAND() LIMIT 1");
	$proxy = $proxy_array['ip_port'];
	// print_r($proxy_array);exit;
	if(!$proxy_array)
	{
		echo 'No Proxy';
		// echo 'here';exit;
		mail('xaman.riaz@gmail.com', 'Proxies not working - PPUSA', 'there is no proxy left, please update db');
		exit;
	}
	// if($proxy_array['is_auth']==1)
	// {
	// 	$proxy = $proxy_array['ip_port'].':SaadAhmed:PhonePartsUSA';
	// }
	// echo $proxy."<br>";
	 // userAgents
			$useragents = array(
				'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36',
				'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36',
				'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1',
				'Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0',
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10; rv:33.0) Gecko/20100101 Firefox/33.0',
				'Opera/9.80 (X11; Linux i686; Ubuntu/14.10) Presto/2.12.388 Version/12.16',
				'Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14',
				'Opera/12.80 (Windows NT 5.1; U; en) Presto/2.10.289 Version/12.02',
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A',
				'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25',
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2',
				'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1a2pre) Gecko/2008073000 Shredder/3.0a2pre ThunderBrowse/3.2.1.8'
				);
			
	$ch = curl_init (); // Initialising cURL
	$options = Array(
	CURLOPT_RETURNTRANSFER => TRUE, // Setting cURL's option to return the webpage data
	CURLOPT_FOLLOWLOCATION => TRUE, // Setting cURL to follow 'location' HTTP headers
	CURLOPT_AUTOREFERER => TRUE, // Automatically set the referer where following 'location' HTTP headers
	CURLOPT_CONNECTTIMEOUT => 120, // Setting the amount of time (in seconds) before the request times out
	CURLOPT_TIMEOUT => 120, // Setting the maximum amount of time for cURL to execute queries
	CURLOPT_MAXREDIRS => 10, // Setting the maximum number of redirections to follow
	CURLOPT_PROXY => $proxy,
	CURLOPT_COOKIEJAR => COOKIE_FILE,
	CURLOPT_COOKIEFILE=> COOKIE_FILE,
	CURLOPT_POST => 1,
	CURLOPT_POSTFIELDS => "ajax=login&email=support@ifixlv.com&password=ifixlv3909",
	CURLOPT_USERAGENT => $useragents[array_rand($useragents, 1)], // Setting the useragent
	CURLOPT_URL => $url ); // Setting cURL's URL option with the $url variable passed into the function
	if($proxy_array['is_socks5']==1)
	{
		// array_push($options,array(CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5));
	}
	// print_r($proxy_array);
	// echo "<pre>";
	// print_r($options);
	// curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt_array ( $ch, $options ); // Setting cURL's options using the previously assigned array data in $options
	$data = curl_exec ( $ch ); // Executing the cURL request and assigning the returned data to the $data variable
	$error = curl_error($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close ( $ch ); // Closing cURL
	
	// echo $data;exit;
	// echo $httpCode.'zaman';
	$db->func_query("UPDATE inv_product_price_scrap SET http_code='".$httpCode."' WHERE url='".$url."'");
	if($error or $httpCode!=200 )
	{
		echo $error;
		
		// echo $error;exit;
		// mail('xaman.riaz@gmail.com', 'Proxy attempt failed', $proxy.' attempt failed.'.$error.'----'.$httpCode.'-----'.$url);
		// if($httpCode!=500 or $httpCode!=503)
		// {
		$db->func_query("UPDATE inv_product_price_scrap SET updated=1 WHERE url='".$url."'");
		// $db->db_exec("UPDATE inv_proxy_list SET attempt=attempt+1 where id='".$proxy_array['id']."'");
		// exit;
		// }
		// curl_proxy($url);
		
	}

	return $data; // Returning the data from the function
}

function curl_proxy($url) {
	global $db;
	$proxy_array = $db->func_query_first("SELECT * FROM inv_proxy_list where status=1 and is_auth=0 and is_socks5=0 and attempt<3 ORDER BY RAND() LIMIT 1");
	$proxy = $proxy_array['ip_port'];
	// print_r($proxy_array);exit;
	if(!$proxy_array)
	{
		echo 'No Proxy';
		// echo 'here';exit;
		mail('xaman.riaz@gmail.com', 'Proxies not working - PPUSA', 'there is no proxy left, please update db');
		exit;
	}
	// if($proxy_array['is_auth']==1)
	// {
	// 	$proxy = $proxy_array['ip_port'].':SaadAhmed:PhonePartsUSA';
	// }
	// echo $proxy."<br>";
	 // userAgents
			$useragents = array(
				'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36',
				'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36',
				'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1',
				'Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0',
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10; rv:33.0) Gecko/20100101 Firefox/33.0',
				'Opera/9.80 (X11; Linux i686; Ubuntu/14.10) Presto/2.12.388 Version/12.16',
				'Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14',
				'Opera/12.80 (Windows NT 5.1; U; en) Presto/2.10.289 Version/12.02',
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A',
				'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25',
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2',
				'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1a2pre) Gecko/2008073000 Shredder/3.0a2pre ThunderBrowse/3.2.1.8'
				);
			
	$ch = curl_init (); // Initialising cURL
	$options = Array(
	CURLOPT_RETURNTRANSFER => TRUE, // Setting cURL's option to return the webpage data
	CURLOPT_FOLLOWLOCATION => TRUE, // Setting cURL to follow 'location' HTTP headers
	CURLOPT_AUTOREFERER => TRUE, // Automatically set the referer where following 'location' HTTP headers
	CURLOPT_CONNECTTIMEOUT => 120, // Setting the amount of time (in seconds) before the request times out
	CURLOPT_TIMEOUT => 120, // Setting the maximum amount of time for cURL to execute queries
	CURLOPT_MAXREDIRS => 10, // Setting the maximum number of redirections to follow
	CURLOPT_PROXY => $proxy,
	CURLOPT_COOKIEJAR => COOKIE_FILE,
	CURLOPT_COOKIEFILE=> COOKIE_FILE,
	
	CURLOPT_USERAGENT => $useragents[array_rand($useragents, 1)], // Setting the useragent
	CURLOPT_URL => $url ); // Setting cURL's URL option with the $url variable passed into the function
	if($proxy_array['is_socks5']==1)
	{
		// array_push($options,array(CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5));
	}
	// print_r($proxy_array);
	// echo "<pre>";
	// print_r($options);
	// curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt_array ( $ch, $options ); // Setting cURL's options using the previously assigned array data in $options
	$data = curl_exec ( $ch ); // Executing the cURL request and assigning the returned data to the $data variable
	$error = curl_error($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close ( $ch ); // Closing cURL
	
	$db->func_query("UPDATE inv_product_price_scrap SET http_code='".$httpCode."' WHERE url='".$url."'");
	 //echo $httpCode.'zaman';
	if($error or $httpCode!=200 )
	{
		echo $error;
		
		// echo $error;exit;
		// mail('xaman.riaz@gmail.com', 'Proxy attempt failed', $proxy.' attempt failed.'.$error.'----'.$httpCode.'-----'.$url);
		// if($httpCode!=500 or $httpCode!=503)
		// {
		$db->func_query("UPDATE inv_product_price_scrap SET updated=1 WHERE url='".$url."'");
		// $db->db_exec("UPDATE inv_proxy_list SET attempt=attempt+1 where id='".$proxy_array['id']."'");
		// exit;
		// }
		// curl_proxy($url);
		
	}
	return $data; // Returning the data from the function
}

if ($db->func_query_first_cell('SELECT COUNT(a.id) FROM inv_product_price_scrap a,oc_product b WHERE a.sku=b.model and b.status<>0 and  updated = "0" and url<>""') == 0) {
	// exit;
	$db->db_exec ("UPDATE inv_product_price_scrap SET updated = '0' where url<>''");
}
if(!isset($_POST['sku']))
{
	$rows = $db->func_query("SELECT s.* FROM inv_product_price_scrap s, oc_product b where s.sku=b.model and b.status<>0  and s.updated = '0' and s.url<>'' LIMIT 20");
}
else
{
	$rows = $db->func_query("SELECT * FROM inv_product_price_scrap where sku='".$_POST['sku']."' and url<>''");
}


// $rows = $db->func_query("SELECT * FROM inv_product_price_scrap where sku='APL-003-1294' and url<>'' LIMIT 10");
$output = '';
$json = array();
foreach($rows as $row) {
	$output .= '<strong>' . $row['sku'] . '</strong><br>';
	
	if ($row['type'] == 'mobile_defenders') {
		$productHistory = $db->func_query_first('SELECT * FROM inv_product_price_scrap_history WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'" order by added DESC');
		$rowUpdate = array();
		$rowUpdate['sku'] = $row['sku'];
		$rowUpdate['type'] = $row['type'];
		$add = false;
		$row['url'] = str_replace("http://", "https://", $row['url']);
		$scrap = curl_proxy($row['url']);
		if (strpos($scrap, 'availability out-of-stock') !== false) {
			$rowUpdate['out_of_stock'] = 1;
		} else {
			$rowUpdate['out_of_stock'] = 0;
		}
		$scrap1 = scrape_between($scrap, '<div class="product-shop col-sm-6">', '<div class="clearer noborder">');
		$scrap = scrape_between($scrap1, '<span class="price">', '</span>');  
		$_price = scrapPrice($scrap);
		
		if($_price==0.00)
		{
			$scrap = scrape_between($scrap1, '<p class="special-price">', '</p>');  
			$scrap = scrape_between($scrap, '<span class="price"', '</span>');  
			$scrap = explode(">", $scrap);
			$_price = scrapPrice($scrap[1]);
		}
		$rowUpdate['price'] = $_price;
		// echo $_price;exit;
		
		if ($rowUpdate['price'] != $productHistory['price'] || (!$rowUpdate['out_of_stock'] && $productHistory['out_of_stock'])) {
			$add = true;
			$rowUpdate['added'] = date("Y-m-d H:i:s");
		}
		$rowUpdate['oos_date'] = date("Y-m-d H:i:s");
		if ($rowUpdate['price'] != $productHistory['price']){
			$updated = array();
			$updated['sku'] = $row['sku'];
			$updated['site'] = $row['type'];
			$updated['previous_price'] = $productHistory['price'];
			$updated['updated_price'] = $rowUpdate['price'];
			$updated['date_updated'] =  date("Y-m-d H:i:s");
			$check =$db->func_query_first_cell('SELECT id FROM inv_competitor_prices_history WHERE sku = "'. $row['sku'] .'" AND site="'. $row['type'] .'"');
			if ($check) {
				$db->func_array2update("inv_competitor_prices_history", $updated, "id = '". $check ."'");
			} else {
				$db->func_array2insert("inv_competitor_prices_history",$updated);
			}
		}
		$json[] = array('site'=>'mobile_defenders',
						'price'=>$rowUpdate['price']
						);
		if ($add) {
			$db->func_array2insert("inv_product_price_scrap_history",$rowUpdate);
		} else {
			$db->func_array2update("inv_product_price_scrap_history", $rowUpdate, "id = '". $productHistory['id'] ."'");
		}
		$output .= 'Mobile Defenders Price: $' . $rowUpdate['price'] . '<br>';
		$response_check = $db->func_query_first_cell('SELECT http_code FROM inv_product_price_scrap WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'"');
		if ($response_check != '404' &&  $rowUpdate['price'] == '0.00') {
			$db->func_query('UPDATE inv_product_price_scrap set http_code = "999" WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'"');
		}

	}
	if ($row['type'] == 'etrade_supply') {
		$productHistory = $db->func_query_first('SELECT * FROM inv_product_price_scrap_history WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'" order by added DESC');
		$rowUpdate = array();
		$rowUpdate['sku'] = $row['sku'];
		$rowUpdate['type'] = $row['type'];
		$add = false;
		$scrap = curl_proxy($row['url']);
		if (strpos($scrap, 'This product is out of stock') !== false) {
			$rowUpdate['out_of_stock'] = 1;
		} else {
			$rowUpdate['out_of_stock'] = 0;
		}
		$scrap1 = scrape_between($scrap, '<div class="price-box" rel="nofollow">', '<li class="no-tierprice"');
		$scrap = scrape_between($scrap1, '<span class="price">', '</span>');  
		$_price = scrapPrice($scrap);
		
		if($_price==0.00)
		{
			$scrap = scrape_between($scrap1, '<p class="special-price">', '</p>');  
			$scrap = scrape_between($scrap, '<span class="price"', '</span>');  
			$scrap = explode(">", $scrap);
			$_price = scrapPrice($scrap[1]);
		}
		$rowUpdate['price'] = $_price;
		 //echo $_price;exit;
		
		if ($rowUpdate['price'] != $productHistory['price'] || (!$rowUpdate['out_of_stock'] && $productHistory['out_of_stock'])) {
			$add = true;
			$rowUpdate['added'] = date("Y-m-d H:i:s");
		}
		$rowUpdate['oos_date'] = date("Y-m-d H:i:s");
		if ($rowUpdate['price'] != $productHistory['price']){
			$updated = array();
			$updated['sku'] = $row['sku'];
			$updated['site'] = $row['type'];
			$updated['previous_price'] = $productHistory['price'];
			$updated['updated_price'] = $rowUpdate['price'];
			$updated['date_updated'] =  date("Y-m-d H:i:s");
			$check =$db->func_query_first_cell('SELECT id FROM inv_competitor_prices_history WHERE sku = "'. $row['sku'] .'" AND site="'. $row['type'] .'"');
			if ($check) {
				$db->func_array2update("inv_competitor_prices_history", $updated, "id = '". $check ."'");
			} else {
				$db->func_array2insert("inv_competitor_prices_history",$updated);
			}
		}
		$json[] = array('site'=>'etrade_supply',
						'price'=>$rowUpdate['price']
						);
		if ($add) {
			$db->func_array2insert("inv_product_price_scrap_history",$rowUpdate);
		} else {
			$db->func_array2update("inv_product_price_scrap_history", $rowUpdate, "id = '". $productHistory['id'] ."'");
		}
		$output .= 'E-Trade Supply  Price: $' . $rowUpdate['price'] . '<br>';
		$response_check = $db->func_query_first_cell('SELECT http_code FROM inv_product_price_scrap WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'"');
		if ($response_check != '404' &&  $rowUpdate['price'] == '0.00') {
			$db->func_query('UPDATE inv_product_price_scrap set http_code = "999" WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'"');
		}
	}
	// if ($row['type'] == 'lcd_loop') {
	// 	$productHistory = $db->func_query_first('SELECT * FROM inv_product_price_scrap_history WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'" order by added DESC');
	// 	$rowUpdate = array();
	// 	$rowUpdate['sku'] = $row['sku'];
	// 	$rowUpdate['type'] = $row['type'];
	// 	$add = false;
	// 	$scrap = curl_proxy($row['url']);
	// 	//print_r($scrap);exit;
		
	// 	if (strpos($scrap, 'This product is out of stock') !== false) {
	// 		$rowUpdate['out_of_stock'] = 1;
	// 	} else {
	// 		$rowUpdate['out_of_stock'] = 0;
	// 	}
	// 	$scrap1 = scrape_between($scrap, '<p class="price">', '<form class="cart"');
	// 	$scrap = scrape_between($scrap1, '<span class="woocommerce-Price-amount amount">', '</p>');
	// 	$scrap = scrape_between($scrap1, '</span>', '</span>');  
	// 	$_price = scrapPrice($scrap);
		
	// 	if($_price==0.00)
	// 	{
	// 		$scrap = scrape_between($scrap1, '<p class="special-price">', '</p>');  
	// 		$scrap = scrape_between($scrap, '<span class="price"', '</span>');  
	// 		$scrap = explode(">", $scrap);
	// 		$_price = scrapPrice($scrap[1]);
	// 	}
	// 	$rowUpdate['price'] = $_price;
	// 	 //echo $_price;exit;
		
	// 	if ($rowUpdate['price'] != $productHistory['price'] || (!$rowUpdate['out_of_stock'] && $productHistory['out_of_stock'])) {
	// 		$add = true;
	// 		$rowUpdate['added'] = date("Y-m-d H:i:s");
	// 	}
	// 	$rowUpdate['oos_date'] = date("Y-m-d H:i:s");
	// 	if ($rowUpdate['price'] != $productHistory['price']){
	// 		$updated = array();
	// 		$updated['sku'] = $row['sku'];
	// 		$updated['site'] = $row['type'];
	// 		$updated['previous_price'] = $productHistory['price'];
	// 		$updated['updated_price'] = $rowUpdate['price'];
	// 		$updated['date_updated'] =  date("Y-m-d H:i:s");
	// 		$check =$db->func_query_first_cell('SELECT id FROM inv_competitor_prices_history WHERE sku = "'. $row['sku'] .'" AND site="'. $row['type'] .'"');
	// 		if ($check) {
	// 			$db->func_array2update("inv_competitor_prices_history", $updated, "id = '". $check ."'");
	// 		} else {
	// 			$db->func_array2insert("inv_competitor_prices_history",$updated);
	// 		}
	// 	}
	// 	$json[] = array('site'=>'lcd_loop',
	// 					'price'=>$rowUpdate['price']
	// 					);
	// 	if ($add) {
	// 		$db->func_array2insert("inv_product_price_scrap_history",$rowUpdate);
	// 	} else {
	// 		$db->func_array2update("inv_product_price_scrap_history", $rowUpdate, "id = '". $productHistory['id'] ."'");
	// 	}
	// 	$output .= 'LCD Loop  Price: $' . $rowUpdate['price'] . '<br>';
	// }

	if ($row['type'] == 'lcd_loop') {
		$productHistory = $db->func_query_first('SELECT * FROM inv_product_price_scrap_history WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'" order by added DESC');
		$rowUpdate = array();
		$rowUpdate['sku'] = $row['sku'];
		$rowUpdate['type'] = $row['type'];
		$add = false;
		$scrap = curl_proxy($row['url']);
		//print_r($scrap);exit;
		
		if (strpos($scrap, 'This product is out of stock') !== false) {
			$rowUpdate['out_of_stock'] = 1;
		} else {
			$rowUpdate['out_of_stock'] = 0;
		}
		$scrap1 = scrape_between($scrap, '<span id="ProductPrice-product-template" class="product-single__price" itemprop="price"', '</span>');
		// echo $scrap1;exit;
		$scrap = explode(">", $scrap1);
		$_price = scrapPrice($scrap[1]);
		// echo $_price;exit;
		// $_price = scrapPrice($scrap1);
		
		if($_price==0.00)
		{
			// $scrap = scrape_between($scrap1, '<p class="special-price">', '</p>');  
			// $scrap = scrape_between($scrap, '<span class="price"', '</span>');  
			// $scrap = explode(">", $scrap);
			// $_price = scrapPrice($scrap[1]);
		}
		$rowUpdate['price'] = $_price;
		 //echo $_price;exit;
		
		if ($rowUpdate['price'] != $productHistory['price'] || (!$rowUpdate['out_of_stock'] && $productHistory['out_of_stock'])) {
			$add = true;
			$rowUpdate['added'] = date("Y-m-d H:i:s");
		}
		$rowUpdate['oos_date'] = date("Y-m-d H:i:s");
		if ($rowUpdate['price'] != $productHistory['price']){
			$updated = array();
			$updated['sku'] = $row['sku'];
			$updated['site'] = $row['type'];
			$updated['previous_price'] = $productHistory['price'];
			$updated['updated_price'] = $rowUpdate['price'];
			$updated['date_updated'] =  date("Y-m-d H:i:s");
			$check =$db->func_query_first_cell('SELECT id FROM inv_competitor_prices_history WHERE sku = "'. $row['sku'] .'" AND site="'. $row['type'] .'"');
			if ($check) {
				$db->func_array2update("inv_competitor_prices_history", $updated, "id = '". $check ."'");
			} else {
				$db->func_array2insert("inv_competitor_prices_history",$updated);
			}
		}
		$json[] = array('site'=>'lcd_loop',
						'price'=>$rowUpdate['price']
						);
		if ($add) {
			$db->func_array2insert("inv_product_price_scrap_history",$rowUpdate);
		} else {
			$db->func_array2update("inv_product_price_scrap_history", $rowUpdate, "id = '". $productHistory['id'] ."'");
		}
		$output .= 'LCD Loop  Price: $' . $rowUpdate['price'] . '<br>';
		$response_check = $db->func_query_first_cell('SELECT http_code FROM inv_product_price_scrap WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'"');
		if ($response_check != '404' &&  $rowUpdate['price'] == '0.00') {
			$db->func_query('UPDATE inv_product_price_scrap set http_code = "999" WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'"');
		}
	}
	if ($row['type'] == 'parts_4_cells') {
		$productHistory = $db->func_query_first('SELECT * FROM inv_product_price_scrap_history WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'" order by added DESC');
		$rowUpdate = array();
		$rowUpdate['sku'] = $row['sku'];
		$rowUpdate['type'] = $row['type'];
		$add = false;
		$scrap = curl_proxy($row['url']);
		//print_r($scrap);exit;
		if (strpos($scrap, 'OutOfStock') !== false) {
			$rowUpdate['out_of_stock'] = 1;
		} else {
			$rowUpdate['out_of_stock'] = 0;
		}
		
		$scrap1 = scrape_between($scrap, '<div class="prices">', '<div id="product-variants"');
		//echo $scrap1;exit;
		$scrap = scrape_between($scrap1, '<span class="price" itemprop="price">', '</span>');  
		$_price = scrapPrice($scrap);
		
		if($_price==0.00)
		{
			/*$scrap = scrape_between($scrap1, '<p class="special-price">', '</p>');  
			$scrap = scrape_between($scrap, '<span class="price"', '</span>');  
			$scrap = explode(">", $scrap);
			$_price = scrapPrice($scrap[1]);*/
			//$scrap = scrape_between($scrap1, '<span class="compare-price">', '<span class="price on-sale"');  
			$scrap = scrape_between($scrap1, '<span class="price on-sale" itemprop="price">', '</span>'); 
			
			//$scrap = explode(">", $scrap);
			$_price = scrapPrice($scrap);
		
		}
		$rowUpdate['price'] = $_price;
		 //echo $_price;exit;
		
		if ($rowUpdate['price'] != $productHistory['price'] || (!$rowUpdate['out_of_stock'] && $productHistory['out_of_stock'])) {
			$add = true;
			$rowUpdate['added'] = date("Y-m-d H:i:s");
		}
		$rowUpdate['oos_date'] = date("Y-m-d H:i:s");
		if ($rowUpdate['price'] != $productHistory['price']){
			$updated = array();
			$updated['sku'] = $row['sku'];
			$updated['site'] = $row['type'];
			$updated['previous_price'] = $productHistory['price'];
			$updated['updated_price'] = $rowUpdate['price'];
			$updated['date_updated'] =  date("Y-m-d H:i:s");
			$check =$db->func_query_first_cell('SELECT id FROM inv_competitor_prices_history WHERE sku = "'. $row['sku'] .'" AND site="'. $row['type'] .'"');
			if ($check) {
				$db->func_array2update("inv_competitor_prices_history", $updated, "id = '". $check ."'");
			} else {
				$db->func_array2insert("inv_competitor_prices_history",$updated);
			}
		}
		$json[] = array('site'=>'parts_4_cells',
						'price'=>$rowUpdate['price']
						);
		if ($add) {
			$db->func_array2insert("inv_product_price_scrap_history",$rowUpdate);
		} else {
			$db->func_array2update("inv_product_price_scrap_history", $rowUpdate, "id = '". $productHistory['id'] ."'");
		}
		$output .= 'Parts 4 Cells  Price: $' . $rowUpdate['price'] . '<br>';
		$response_check = $db->func_query_first_cell('SELECT http_code FROM inv_product_price_scrap WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'"');
		if ($response_check != '404' &&  $rowUpdate['price'] == '0.00') {
			$db->func_query('UPDATE inv_product_price_scrap set http_code = "999" WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'"');
		}
	}
	if ($row['type'] == 'cell_parts_hub') {
		$productHistory = $db->func_query_first('SELECT * FROM inv_product_price_scrap_history WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'" order by added DESC');
		$rowUpdate = array();
		$rowUpdate['sku'] = $row['sku'];
		$rowUpdate['type'] = $row['type'];
		$add = false;
		//$row['url']  = str_replace("https://", "http://", $row['url']);
		$scrap = curl_proxy($row['url']);
		//print_r($scrap);exit;
		if (strpos($scrap, 'OutOfStock') !== false) {
			$rowUpdate['out_of_stock'] = 1;
		} else {
			$rowUpdate['out_of_stock'] = 0;
		}
		$scrap1 = scrape_between($scrap, '<div class="prices"', '<meta itemprop="priceCurrency"');
		//$scrap1= str_replace(' ', '', $scrap1);
		//$scrap2= scrape_between($scrap1, 'itemprop="price"', '</span>');
		$scrap2 = scrape_between($scrap1, 'itemprop="price"', 'class="price-value');  
		$scrap3 = scrape_between($scrap2, 'content="', '"');
		$_price = scrapPrice($scrap3);
		//print_r($_price);
		
		if($_price==0.00)
		{
			$scrap = scrape_between($scrap, '<div class="product-price discounted-price">', '<meta itemprop="priceCurrency"');  
			$scrap = scrape_between($scrap, '<span itemprop="price"', 'class="price-value');  
			$scrap = scrape_between($scrap, 'content="', '"');
			$_price = scrapPrice($scrap);
		}
		$rowUpdate['price'] = $_price;
		 //echo $_price;exit;
		
		if ($rowUpdate['price'] != $productHistory['price'] || (!$rowUpdate['out_of_stock'] && $productHistory['out_of_stock'])) {
			$add = true;
			$rowUpdate['added'] = date("Y-m-d H:i:s");
		}
		$rowUpdate['oos_date'] = date("Y-m-d H:i:s");
		if ($rowUpdate['price'] != $productHistory['price']){
			$updated = array();
			$updated['sku'] = $row['sku'];
			$updated['site'] = $row['type'];
			$updated['previous_price'] = $productHistory['price'];
			$updated['updated_price'] = $rowUpdate['price'];
			$updated['date_updated'] =  date("Y-m-d H:i:s");
			$check =$db->func_query_first_cell('SELECT id FROM inv_competitor_prices_history WHERE sku = "'. $row['sku'] .'" AND site="'. $row['type'] .'"');
			if ($check) {
				$db->func_array2update("inv_competitor_prices_history", $updated, "id = '". $check ."'");
			} else {
				$db->func_array2insert("inv_competitor_prices_history",$updated);
			}
		}
		$json[] = array('site'=>'cell_parts_hub',
						'price'=>$rowUpdate['price']
						);
		if ($add) {
			$db->func_array2insert("inv_product_price_scrap_history",$rowUpdate);
		} else {
			$db->func_array2update("inv_product_price_scrap_history", $rowUpdate, "id = '". $productHistory['id'] ."'");
		}
		$output .= 'Cell Parts Hub  Price: $' . $rowUpdate['price'] . '<br>';
		$response_check = $db->func_query_first_cell('SELECT http_code FROM inv_product_price_scrap WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'"');
		if ($response_check != '404' &&  $rowUpdate['price'] == '0.00') {
			$db->func_query('UPDATE inv_product_price_scrap set http_code = "999" WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'"');
		}
	}
	if ($row['type'] == 'maya_cellular') {
		$productHistory = $db->func_query_first('SELECT * FROM inv_product_price_scrap_history WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'" order by added DESC');
		$rowUpdate = array();
		$rowUpdate['sku'] = $row['sku'];
		$rowUpdate['type'] = $row['type'];
		$add = false;
		$scrap = curl_proxy($row['url']);
		if (strpos($scrap, 'Out of stock') !== false) {
			$rowUpdate['out_of_stock'] = 1;
		} else {
			$rowUpdate['out_of_stock'] = 0;
		}
		$scrap1 = scrape_between($scrap, '<div class="price-box">', '<div class="add-to-box"');
		$scrap = scrape_between($scrap1, '<span class="price">', '</span>');  
		$_price = scrapPrice($scrap);
		
		if($_price==0.00)
		{
			$scrap = scrape_between($scrap1, '<p class="special-price">', '</p>');  
			$scrap = scrape_between($scrap, '<span class="price"', '</span>');  
			$scrap = explode(">", $scrap);
			$_price = scrapPrice($scrap[1]);
		}
		$rowUpdate['price'] = $_price;
		 //echo $_price;exit;
		
		if ($rowUpdate['price'] != $productHistory['price'] || (!$rowUpdate['out_of_stock'] && $productHistory['out_of_stock'])) {
			$add = true;
			$rowUpdate['added'] = date("Y-m-d H:i:s");
		}
		$rowUpdate['oos_date'] = date("Y-m-d H:i:s");
		if ($rowUpdate['price'] != $productHistory['price']){
			$updated = array();
			$updated['sku'] = $row['sku'];
			$updated['site'] = $row['type'];
			$updated['previous_price'] = $productHistory['price'];
			$updated['updated_price'] = $rowUpdate['price'];
			$updated['date_updated'] =  date("Y-m-d H:i:s");
			$check =$db->func_query_first_cell('SELECT id FROM inv_competitor_prices_history WHERE sku = "'. $row['sku'] .'" AND site="'. $row['type'] .'"');
			if ($check) {
				$db->func_array2update("inv_competitor_prices_history", $updated, "id = '". $check ."'");
			} else {
				$db->func_array2insert("inv_competitor_prices_history",$updated);
			}
		}
		$json[] = array('site'=>'maya_cellular',
						'price'=>$rowUpdate['price']
						);
		if ($add) {
			$db->func_array2insert("inv_product_price_scrap_history",$rowUpdate);
		} else {
			$db->func_array2update("inv_product_price_scrap_history", $rowUpdate, "id = '". $productHistory['id'] ."'");
		}
		$output .= 'Maya Cellular Parts  Price: $' . $rowUpdate['price'] . '<br>';
		$response_check = $db->func_query_first_cell('SELECT http_code FROM inv_product_price_scrap WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'"');
		if ($response_check != '404' &&  $rowUpdate['price'] == '0.00') {
			$db->func_query('UPDATE inv_product_price_scrap set http_code = "999" WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'"');
		}
	}
	if ($row['type'] == 'mobile_sentrix') {
		$productHistory = $db->func_query_first('SELECT * FROM inv_product_price_scrap_history WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'" order by added DESC');
		$rowUpdate = array();
		$rowUpdate['sku'] = $row['sku'];
		$rowUpdate['type'] = $row['type'];
		$add = false;
		$scrap = curl_proxy($row['url']);
		if (strpos($scrap, 'Product Out of Stock') !== false) {
			$rowUpdate['out_of_stock'] = 1;
		} else {
			$rowUpdate['out_of_stock'] = 0;
		}
		$scrap = scrape_between($scrap, '"productviewcart">', '</div>');  
		// echo $scrap;exit;
		$scrap = scrape_between($scrap, 'class="price">', '</span>');  
		$rowUpdate['price'] = scrapPrice($scrap);
		if ($rowUpdate['price'] != $productHistory['price'] || (!$rowUpdate['out_of_stock'] && $productHistory['out_of_stock'])) {
			$add = true;
			$rowUpdate['added'] = date("Y-m-d H:i:s");
		}
		$rowUpdate['oos_date'] = date("Y-m-d H:i:s");
		if ($rowUpdate['price'] != $productHistory['price']){
			$updated = array();
			$updated['sku'] = $row['sku'];
			$updated['site'] = $row['type'];
			$updated['previous_price'] = $productHistory['price'];
			$updated['updated_price'] = $rowUpdate['price'];
			$updated['date_updated'] =  date("Y-m-d H:i:s");
			$check =$db->func_query_first_cell('SELECT id FROM inv_competitor_prices_history WHERE sku = "'. $row['sku'] .'" AND site="'. $row['type'] .'"');
			if ($check) {
				$db->func_array2update("inv_competitor_prices_history", $updated, "id = '". $check ."'");
			} else {
				$db->func_array2insert("inv_competitor_prices_history",$updated);
			}
		}
		$json[] = array('site'=>'mobile_sentrix',
						'price'=>$rowUpdate['price']
						);
		if ($add) {
			$db->func_array2insert("inv_product_price_scrap_history",$rowUpdate);
		} else {
			$db->func_array2update("inv_product_price_scrap_history", $rowUpdate, "id = '". $productHistory['id'] ."'");
		}
		$output .= 'Mobile Sentrix Price: $' . $rowUpdate['price'] . '<br>';
		$response_check = $db->func_query_first_cell('SELECT http_code FROM inv_product_price_scrap WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'"');
		if ($response_check != '404' &&  $rowUpdate['price'] == '0.00') {
			$db->func_query('UPDATE inv_product_price_scrap set http_code = "999" WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'"');
		}
	}
	
	if ($row['type'] == 'fixez') {
		$productHistory = $db->func_query_first('SELECT * FROM inv_product_price_scrap_history WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'" order by added DESC');
		$rowUpdate = array();
		$rowUpdate['sku'] = $row['sku'];
		$rowUpdate['type'] = $row['type'];
		$add = false;
		$_data = curl_proxy_fixez('http://www.fixez.com/ajaxlogin/ajax/index/');
		$scrap = curl_proxy($row['url']);
		if (strpos($scrap, 'glyphicon-exclamation-sign') !== false) {
			$rowUpdate['out_of_stock'] = 1;
		} else {
			$rowUpdate['out_of_stock'] = 0;
		}
		$scrap1 = scrape_between($scrap, '<div class="col-xs-6 mainPrice">', '<div class="col-xs-6">');  
		$scrap = scrape_between($scrap1, '<span class="price">', '</span>');  
		if(!$scrap)
		{
			$scrap = scrape_between($scrap1, 'Special Price</span>', '</p>');  
			$scrap = scrape_between($scrap,'">','</span>');
			if(!$scrap)
		{
			$scrap = scrape_between($scrap1, 'Price:</span>', '</p>');
			$scrap = scrape_between($scrap,'">','</span>');  

		}

		}
		$rowUpdate['price'] = scrapPrice($scrap);

		if ($rowUpdate['price'] != $productHistory['price'] || (!$rowUpdate['out_of_stock'] && $productHistory['out_of_stock'])) {
			$add = true;
			$rowUpdate['added'] = date("Y-m-d H:i:s");
		}
		$rowUpdate['oos_date'] = date("Y-m-d H:i:s");
		if ($rowUpdate['price'] != $productHistory['price']){
			$updated = array();
			$updated['sku'] = $row['sku'];
			$updated['site'] = $row['type'];
			$updated['previous_price'] = $productHistory['price'];
			$updated['updated_price'] = $rowUpdate['price'];
			$updated['date_updated'] =  date("Y-m-d H:i:s");
			$check =$db->func_query_first_cell('SELECT id FROM inv_competitor_prices_history WHERE sku = "'. $row['sku'] .'" AND site="'. $row['type'] .'"');
			if ($check) {
				$db->func_array2update("inv_competitor_prices_history", $updated, "id = '". $check ."'");
			} else {
				$db->func_array2insert("inv_competitor_prices_history",$updated);
			}
		}
		$json[] = array('site'=>'fixez',
						'price'=>$rowUpdate['price']
						);
		if ($add) {
			$db->func_array2insert("inv_product_price_scrap_history",$rowUpdate);
		} else {
			$db->func_array2update("inv_product_price_scrap_history", $rowUpdate, "id = '". $productHistory['id'] ."'");
		}
		$output .= 'Fixez Price: $' . $rowUpdate['price'] . '<br>';
		$response_check = $db->func_query_first_cell('SELECT http_code FROM inv_product_price_scrap WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'"');
		if ($response_check != '404' &&  $rowUpdate['price'] == '0.00') {
			$db->func_query('UPDATE inv_product_price_scrap set http_code = "999" WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'"');
		}
	}
	if ($row['type'] == 'mengtor') {
		$productHistory = $db->func_query_first('SELECT * FROM inv_product_price_scrap_history WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'" order by added DESC');
		$rowUpdate = array();
		$rowUpdate['sku'] = $row['sku'];
		$rowUpdate['type'] = $row['type'];
		$add = false;
		$scrap = curl_proxy($row['url']);
		if (strpos($scrap, 'Back In stock Reminder') !== false) {
			$rowUpdate['out_of_stock'] = 1;
		} else {
			$rowUpdate['out_of_stock'] = 0;
		}
		$scrap = scrape_between($scrap, '<div class="price">', '<div class="jtj">');  
		$scrap = scrape_between($scrap, '<span>', '</span>');  
		$rowUpdate['price'] = scrapPrice($scrap);
		if ($rowUpdate['price'] != $productHistory['price'] || (!$rowUpdate['out_of_stock'] && $productHistory['out_of_stock'])) {
			$add = true;
			$rowUpdate['added'] = date("Y-m-d H:i:s");
		}
		$rowUpdate['oos_date'] = date("Y-m-d H:i:s");
		if ($rowUpdate['price'] != $productHistory['price']){
			$updated = array();
			$updated['sku'] = $row['sku'];
			$updated['site'] = $row['type'];
			$updated['previous_price'] = $productHistory['price'];
			$updated['updated_price'] = $rowUpdate['price'];
			$updated['date_updated'] =  date("Y-m-d H:i:s");
			$check =$db->func_query_first_cell('SELECT id FROM inv_competitor_prices_history WHERE sku = "'. $row['sku'] .'" AND site="'. $row['type'] .'"');
			if ($check) {
				$db->func_array2update("inv_competitor_prices_history", $updated, "id = '". $check ."'");
			} else {
				$db->func_array2insert("inv_competitor_prices_history",$updated);
			}
		}
		$json[] = array('site'=>'mengtor',
						'price'=>$rowUpdate['price']
						);
		if ($add) {
			$db->func_array2insert("inv_product_price_scrap_history",$rowUpdate);
		} else {
			$db->func_array2update("inv_product_price_scrap_history", $rowUpdate, "id = '". $productHistory['id'] ."'");
		}
		$output .= 'Mengtor Price: $' . $rowUpdate['price'] . '<br>';
		$response_check = $db->func_query_first_cell('SELECT http_code FROM inv_product_price_scrap WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'"');
		if ($response_check != '404' &&  $rowUpdate['price'] == '0.00') {
			$db->func_query('UPDATE inv_product_price_scrap set http_code = "999" WHERE sku = "'. $row['sku'] .'" AND type="'. $row['type'] .'"');
		}
	}
	$db->func_array2update("inv_product_price_scrap", array('date_updated' => date("Y-m-d H:i:s"), 'updated' => '1','recent_price'=>$rowUpdate['price']), "id = '". $row['id'] ."'");
	$output .= '---------------------<br>';
}
if(isset($_POST['sku']))
{
		echo json_encode($json);
}
else
{
echo $output;
}
?>
