<?php
/*
set_time_limit(200);
include_once '../config.php';
include_once '../inc/functions.php';

$post = file_get_contents('php://input');
// echo 'here';exit;
$data = json_decode($post,true);

if($data['lead_id'])
{
	$is_lead = true;
	$lead_id = $data['lead_id'];
	$email = trim(strtolower($data['lead_email']));
	$last_contacted = $data['lead_last_contacted'];
}
else
{
	$is_lead = false;
	$lead_id = $data['contact_id'];
	$email = trim(strtolower($data['contact_email']));
	$last_contacted = $data['contact_last_contacted'];
}
if(!$email)
{
	exit;
}
// print_r($data);exit;
$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://phonepartsusa.freshsales.io/api/".($is_lead?'leads':'contacts')."/".$lead_id.'/activities',
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

		$db->func_query("UPDATE inv_customers SET freshsales_contact_data='".(int)$total_emails.'-'.(int)$total_inbound.'-'.(int)$total_outbound_count.'-'.(int)$total_outbound."',is_freshsale_sync=1 WHERE TRIM(LOWER(email))='".$email."'");

		echo json_encode(array('success'=>$lead_id));

		file_put_contents('freshsales.log',$post.PHP_EOL, FILE_APPEND);
*/

?>