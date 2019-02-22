<?php
include_once '../auth.php';
include_once '../inc/functions.php';
include_once 'class.php';
$customers = $db->func_query("select a.customer_id,b.firstname,b.lastname,a.telephone,b.company,b.address_1,b.city,b.postcode from oc_customer a,oc_address b where a.customer_id=b.customer_id and b.city LIKE '%las vegas%' limit 5");

foreach($customers as $customer)
{
	$body = array(
    "name"=> $customer['firstname'].' '.$customer['lastname'],
    "phone"=> $customer['telephone'],
    "company_name"=> $customer['company'],
    "address_line1"=> $customer['address_1'],
    "city_locality"=> "Las Vegas",
    "state_province"=> "NV",
    "postal_code"=> $customer['postcode'],
    "country_code"=> "USA");
  ;
  $body = json_encode($body);
  // echo $body;exit;
	$response = $inventory->validateAddress($body);
	print_r($response)."<br>";exit;
}

?>