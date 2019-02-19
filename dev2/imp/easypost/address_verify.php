<?php
set_time_limit(0);
include "../config.php";
include "../inc/functions.php";
// require_once("../easypost/lib/easypost.php");
// \EasyPost\EasyPost::setApiKey('ZMn4BcLGpdi3qSZzYbyxyw');

$customers = $db->func_query("select b.address_id, a.customer_id,b.firstname,b.lastname,a.telephone,b.company,b.address_1,b.address_2,b.city,b.postcode from oc_customer a,oc_address b where a.customer_id=b.customer_id and b.city LIKE '%las vegas%' and b.easypost_response='' and b.verify_status='' limit 100");


foreach($customers as $customer)
{


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"https://api.easypost.com/v2/addresses");
curl_setopt($ch, CURLOPT_USERPWD, 'ZMn4BcLGpdi3qSZzYbyxyw:');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,
            "verify[]=delivery&address[street1]=".$customer['address_1']."&address[street2]=".$customer['address_2']."&address[city]=Las Vegas&address[state]=NV&address[zip]=".$customer['postcode']."&address[country]=US&address[phone]=".$customer['telephone']);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec ($ch);

curl_close ($ch);

$array = json_decode($response,true);

$verify_status =  ($array['verifications']['delivery']['success']?'verified':'unverified');
echo $verify_status."<br>";
$db->func_query("UPDATE oc_address SET verify_status='".$verify_status."',easypost_response='".json_encode($array['verifications']['delivery']['errors'])."' WHERE address_id='".$customer['address_id']."'");

}

exit;
?>