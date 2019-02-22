<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
include_once '../config.php';

function generateRandomString($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$total = 13;
$name = 'New Coupons';
$date_start = '2016-04-27';
$date_end = '2016-05-09';

for($i=1;$i<=$total;$i++)
{
	$data = array();
	$data['name'] = $name;
	$data['code'] = generateRandomString();
	$data['type'] = 'P';
	$data['discount'] = 5.0000;
	$data['logged'] = 0;
	$data['shipping'] = 0;
	$data['total'] = 0;
	$data['maximum_amount'] = 25;
	$data['date_start'] = $date_start;
	$data['date_end'] = $date_end;
	$data['uses_total'] = 1;
	$data['uses_customer'] = 1;
	$data['status'] = 1;
	$data['date_added'] = date('Y-m-d H:i:s');
	$db->func_array2insert('oc_coupon',$data);
}
?>