<?php
include_once '../config.php';
include_once '../inc/functions.php';
$email = $db->func_escape_string($_POST['email']);

$customer_group_id = $db->func_query_first_cell('SELECT customer_group_id FROM oc_customer WHERE email = "'.$email.'"');
if($customer_group_id)
{
	$group = $customer_group_id;
}
else
{
	echo json_encode(array('error'=>'No Customer Data Found'));exit;
}
$group = 1633;
echo json_encode(array('success'=>$group));
?>