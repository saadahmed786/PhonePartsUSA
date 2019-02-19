<?php
$_allowed_ips = oc_config('config_security_ipgrants');
$_allowed_ips = unserialize($_allowed_ips);


$_my_ip = $_SERVER['REMOTE_ADDR'];
if (! isset ( $_SESSION ['email'] ) || ! $_SESSION ['email']) {
if(!in_array($_my_ip, $_allowed_ips))
{

	include("admin_404.php");
	exit;
}
}
?>