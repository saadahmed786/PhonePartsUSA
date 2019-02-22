<?php
include_once("config.php");
include_once("inc/functions.php");

$url = 'https://www.fixez.com';

$data = curl_proxy($url);
testObject($data);

function curl_proxy($url,$login_required = 0) {
	global $db;
	$proxy_array = $db->func_query_first("SELECT * FROM inv_proxy_list where status=1 and is_auth=0 and is_socks5=0 and attempt<3 ORDER BY RAND() LIMIT 1");

	$proxy = $proxy_array['ip_port'];
	// print_r($proxy_array);exit;
	if(!$proxy_array)
	{
		echo 'No Proxy';
		// echo 'here';exit;
		mail('gohar.chattha@gmail.com', 'Proxies not working - PPUSA', 'there is no proxy left, please update db');
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
	if ($login_required == 1) {
				$login_ch = curl_init (); // Initialising cURL
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
				CURLOPT_URL => 'https://www.fixez.com/customer/account/login/' );
				curl_setopt_array ( $login_ch, $options ); // Setting cURL's options using the previously assigned array data in $options
				$login_data = curl_exec ( $login_ch );
				curl_close ( $login_ch );
			}
			
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
	
	// echo $httpCode.'zaman';
	if($error or $httpCode!=200 )
	{
		echo $error;
		
		// echo $error;exit;
		mail('xaman.riaz@gmail.com', 'Proxy attempt failed', $proxy.' attempt failed.'.$error.'----'.$httpCode.'-----'.$url);
		// if($httpCode!=500 or $httpCode!=503)
		// {
		//$db->db_exec("UPDATE inv_product_price_scrap SET updated=1 WHERE url='".$url."'");
		// $db->db_exec("UPDATE inv_proxy_list SET attempt=attempt+1 where id='".$proxy_array['id']."'");
		// exit;
		// }
		// curl_proxy($url);
		
	}
	return $data; // Returning the data from the function
}

?>