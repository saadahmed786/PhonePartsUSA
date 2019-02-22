<?php
include_once '../config.php';
include_once '../inc/functions.php';
$email = $db->func_escape_string($_GET['email']);
$hide_header = $db->func_escape_string($_GET['hide_header']);

$customer_group_id =  $db->func_escape_string($_GET['customer_group_id']);
//order_create.php?action=customer_order&firstname=Desert&lastname=Wireless&email=desert.wireless@yahoo.com&telephone=702-339-0992
$customer_data = array();

if($email)
{
	//echo "SELECT firstname,lastname,email,telephone from oc_customer where email='".$email."'";exit;
$customer_data = $db->func_query_first("SELECT firstname,lastname,email,telephone from oc_customer where email='".$email."'");
}

header("Location: $host_path/order_create.php?action=customer_order&firstname=".$customer_data['firstname'].'&lastname='.$customer_data['lastname'].'&email='.$email.'&telephone='.$customer_data['telephone'].'&hide_header='.$hide_header);
?>