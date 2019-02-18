<?php
set_time_limit(200);
include_once '../config.php';
include_once '../inc/functions.php';

$rows =$db->func_query("select distinct a.last_order, a.user_id, a.firstname,a.lastname,a.email,a.company,b.source,(select count(*) from inv_orders c where a.email=c.email and lower(order_status) in ('processed','shipped','completed')) as count_order,(select sum(c.sub_total+c.tax+c.shipping_amount) from inv_orders c where a.email=c.email and lower(c.order_status) in ('processed','shipped','completed')) as total_ordered,d.order_id,d.email,d.order_status,d.order_date  from inv_customers a,oc_customer_source b,inv_orders d where (a.email)=(b.email) and d.email=a.email and b.type='business_type'  and lower(d.order_status) in ('processed','shipped','completed') and a.is_freshsale_sync=0  group by a.email order by a.last_order limit 10 ");
if(!$rows)
{
	$db->db_exec("UPDATE inv_customers SET is_freshsale_sync=0 WHERE is_freshsale_sync=1");
	exit;
}

foreach($rows as $row)
{

	
	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://phonepartsusa.freshsales.io/api/lookup?f=email&entities=lead%2Ccontact&q=".urlencode($row['email']),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_POSTFIELDS => '{"user":{"email":"saad@phonepartsusa.com","password":"ppusa12345"}}',
		CURLOPT_HTTPHEADER => array(
			"auth: Token token=RMKYt6rcgwHUAw3-wcSo7A",
			"cache-control: no-cache",
			"content-type: application/json"

			),
		));

	$row['freshsales_data'] = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
  // echo "cURL Error #:" . $err;
	} else {
  // echo $response;
	}
$fs_data = json_decode($row['freshsales_data'],true);
	

	if($fs_data['leads']['leads'])
	{
		$f_data = $fs_data['leads']['leads'][0];
		$is_lead = true;

	}
	else
	{
		$f_data = $fs_data['contacts']['contacts'][0];
		$is_lead = false;


	}
	
	if(($f_data))
	{
		// echo 'here';exit;
		
			$db->func_query("UPDATE inv_customers SET freshsales_data='".$db->func_escape_string(json_encode($fs_data))."' WHERE TRIM(LOWER(email))='".trim(strtolower($row['email']))."'");
		

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://phonepartsusa.freshsales.io/api/".($is_lead?'leads':'contacts')."/".$f_data['id'].'/activities',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_POSTFIELDS => '{"user":{"email":"saad@phonepartsusa.com","password":"ppusa12345"}}',
			CURLOPT_HTTPHEADER => array(
				"auth: Token token=RMKYt6rcgwHUAw3-wcSo7A",
				"cache-control: no-cache",
				"content-type: application/json"

				),
			));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {

		} else {

		}
		$activities = json_decode($response,true);
		// print_r($activities);exit;
		$total_emails = 0;
		$total_outbound = 0;
		$total_inbound = 0;
		$total_outbound_count = 0;
		foreach($activities['activities'] as $activity)
		{
			switch($activity['action_type'])
			{
				case 'EMAIL_SENT';
				$total_emails++;
				break;

				case 'INCOMING_CALL';
				$total_inbound++;
				break;

				case 'OUTGOING_CALL';
				$total_outbound_count++;
				$total_outbound+=$activity['action_data']['recording_duration'];
				break;

			}
		}
	}
	else
	{
		$total_emails = 0;
		$total_outbound = 0;
		$total_inbound = 0;
		$total_outbound_count = 0;
	}

		$db->func_query("UPDATE inv_customers SET freshsales_contact_data='".(int)$total_emails.'-'.(int)$total_inbound.'-'.(int)$total_outbound_count.'-'.(int)$total_outbound."',is_freshsale_sync=1 WHERE TRIM(LOWER(email))='".trim(strtolower($row['email']))."'");
}
	echo 1;
	?>