<?php
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
include_once '../config.php';
require_once("../inc/functions.php");
require_once('GetResponseAPI3.class.php');
$api_key = 'ffd13f90bf959f9ad45ef3d043d0cfe9'; // For Development Only
$campaign_id = 'pJNGv';
$getresponse = new GetResponse($api_key);


$rows = $db->func_query("SELECT id,firstname,lastname,email FROM inv_customers WHERE getresponse_added = 0 and getresponse_ignored=0 LIMIT 10");

foreach($rows as $row)
{
	$name = trim($row['firstname']).' '.trim($row['lastname']);
	$email = trim($row['email']);
	$email = filter_var($email, FILTER_SANITIZE_EMAIL); // sanitize email from invalid characters


	$ignored = false;
	

	if (strpos($email, '@marketplace.amazon')) {
		$ignored=true;
	}

	
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	$ignored = true;
	}
	if($ignored)
	{
		$db->db_exec("UPDATE inv_customers SET getresponse_ignored=1 WHERE id='".(int)$row['id']."'");
		continue;
	}
	


$getresponse->addContact(array(
    'name'              => $name,
    'email'             => $email,
    'dayOfCycle'        => 0,
    'campaign'          => array('campaignId' => $campaign_id)
    
));
echo $getresponse->http_status."<Br>";
if( $getresponse->http_status == '202' || $getresponse->http_status == '200')
 {
 	$db->db_exec("UPDATE inv_customers SET getresponse_added=1 WHERE id='".(int)$row['id']."'");
 }
 else
 {
 	$db->db_exec("UPDATE inv_customers SET getresponse_ignored=1 WHERE id='".(int)$row['id']."'");
 }
}
?>