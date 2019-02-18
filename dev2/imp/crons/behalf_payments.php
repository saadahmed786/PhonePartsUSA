<?php
require_once("../config.php");
require_once("../inc/functions.php");
function getBehalfData()
{
	$data = array();
	if (oc_config('behalf_status')) {
		$data['email']= oc_config('behalf_server_email');
		$data['password']= oc_config('behalf_server_password');
		if(oc_config('behalf_account')=='production')
		{
			$data['url'] = 'https://api.behalf.com';
		}
		elseif(oc_config('behalf_account')=='sandbox')
		{
			$data['url'] = 'https://api.demo.behalf.com';
		}
		else
		{
			$data['url'] = 'https://api.demo.behalf.com';	
		}
	}
	return $data;
}
function getAccessToken()
{
	$server = getBehalfData();
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => $server['url']."/v4/auth/token",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_HTTPHEADER => array(
			"authorization: Basic ".base64_encode($server['email'].':'.$server['password']),
			"content-type: application/json",
			),
		));
	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);
	if ($err) {
		return false;
	} else {
		$data =  json_decode($response,true);
		if($data['accessToken'])
		{
			return $data['accessToken'];
		}
		else
		{
			return false;
		}
	}
}
function readBehalf($access_token,$order_id)
{
	global $db;
	$server = getBehalfData();
	// echo "SELECT * FROM oc_behalf_payment WHERE order_id='".(int)$order_id."'";exit;
	$behalf_data = $db->func_query_first("SELECT * FROM oc_behalf_payment WHERE order_id='".(int)$order_id."'");
	$order_data = $db->func_query_first("SELECT total FROM oc_order WHERE order_id='".(int)$order_id."'");
	// print_r($behalf_data);exit;
	if($behalf_data)
	{
			// print_r($behalf_data);exit;
		$curl = curl_init();
		// echo $server['url']."/v4/payments/".$behalf_data->row['payment_token']."/authorizations";exit;
		curl_setopt_array($curl, array(
			CURLOPT_URL => $server['url']."/v4/payments/".$behalf_data['payment_token'],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"x-behalf-accesstoken: ".$access_token,
				),
			));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		// echo $http_status;exit;
		if ($http_status!=201 && $http_status!=200  ) {
			return false;
		} else {
			// echo $response;exit;
			$data =  json_decode($response,true);
			if($data['paymentInfo']['status']=='authorized')
			{
				return capturePayment($access_token,$behalf_data['payment_token'],$order_data['total']);
				$log = "Payment captured. Order ID: ".$order_id."\n".PHP_EOL;
				file_put_contents('behalf_log.log', $log, FILE_APPEND);
			}
			elseif($data['paymentInfo']['status']=='declined' || $data['paymentInfo']['status']=='voided' )
			{
				$db->func_query("UPDATE oc_behalf_payment SET 
					payment_status='declined'
					WHERE payment_token='".$behalf_data['payment_token']."'");
				$log = "Payment ".$data['paymentInfo']['status']." . Order ID: ".$order_id."\n".PHP_EOL;
				file_put_contents('behalf_log.log', $log, FILE_APPEND);
				return false;
			}
			else
			{
				if($data['paymentInfo']['status']!='in_review')
				{
					$log = $response." . Order ID: ".$order_id."\n".PHP_EOL;
					file_put_contents('behalf_log.log', $log, FILE_APPEND);	
				}
				
				return false;
			}
					// return $data['authorizationInfo']['authorizationToken'];
		}
	}
	else
	{
		return false;
	}
}
function capturePayment($access_token,$payment_token,$order_total)
{
	global $db;
	$server = getBehalfData();
	$authorization_token = $db->func_query_first_cell("SELECT authorization_token FROM oc_behalf_payment WHERE payment_token='".$payment_token."'");
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => $server['url']."/v4/payments/".$payment_token."/captures",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => '{"amount": "'.$order_total.'", "authorizationToken": "'.$authorization_token.'"}',
		CURLOPT_HTTPHEADER => array(
			"content-type: application/json",
			"x-behalf-accesstoken: ".$access_token
			),
		));
	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);
	if ($err) {
		return false;
	} else {
		$data = json_decode($response,true);
		if($data['captureInfo'])
		{
			$db->func_query("UPDATE oc_behalf_payment SET 
				capture_date='".$data['captureInfo']['created']."',
				capture_status='".$data['captureInfo']['paymentStatus']."',
				captured_amount='".(float)$data['captureInfo']['capturedAmount']."',
				net_captured_amount='".(float)$data['captureInfo']['netCapturedAmount']."',
				capture_id='".$data['captureInfo']['captureId']."',
				payment_status='approved'
				WHERE payment_token='".$payment_token."'");
			return true;
		}
		else
		{
			return false;
		}
	}
}
$rows = $db->func_query("SELECT * FROM oc_behalf_payment WHERE payment_status='in_review' and authorization_token<>''");
foreach($rows as $row)
{
	$access_token = getAccessToken();
	if(!$access_token)
	{
		$log = "Problem retreiving access token from the server. Order ID: ".$row['order_id'].PHP_EOL;
		file_put_contents('behalf_log.log', $log, FILE_APPEND);
	}
	else
	{
		// echo $access_token;exit;
		$authorization_token = readBehalf($access_token,$row['order_id']);
		if(!$authorization_token)
		{
			// $log = "Payment not read / captured. Order ID: ".$row['order_id'].PHP_EOL;
			// file_put_contents('behalf_log.log', $log, FILE_APPEND);
		}
		else
		{
		}
	}
}
echo 1;
?>