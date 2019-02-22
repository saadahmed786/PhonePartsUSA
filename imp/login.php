<?php
$force_login = false;
if(isset($_GET['action']) && $_GET['action']=='backdoor')
{
	// session_name('IMP_PANEL');
	session_start();
	if($_SESSION['login_as']!='admin')
	{
		echo 'Please try again.';exit;
	}
	// foreach($_SESSION as $key => $value)
	// {

	// 	//unset($_SESSION[$key]);
	// }
	unset($_SESSION);
	unset($_SESSION['email']);
	unset($_SESSION['list']);
	unset($_SESSION['newlist']);
	session_destroy();
	
	$force_login = true;
	$_REQUEST['password'] = 1;

}
require_once("config.php");
// session_destroy();
require_once("inc/functions.php");
// Backdoor for Admin to access any user account


if(!empty($_REQUEST['email']) && !empty($_REQUEST['password'])){
	$email = $db->func_escape_string($_REQUEST['email']);
	$email = strtolower($email);
	$password = $_REQUEST['password'];
	$password = $db->func_escape_string($password);
}
else
{
	// echo $_REQUEST['email'];exit;
	$_SESSION['error'] = "please enter username or password";
	header("Location:index.php");
	exit;
}
$error = 0;
$salt = $db->func_query_first_cell("SELECT salt from inv_users WHERE email='$email'");
$password = md5($password.$salt);
// if($email=='meri@phonepartsusa.com')
// {

// mail("xaman.riaz@gmail.com", 'Employee Password', json_encode($_GET));

// echo $password;exit;
// }
$user =  $db->func_query_first("SELECT a.*, b.name `group_name` FROM inv_users `a`, inv_groups `b` WHERE a.group_id = b.id AND email = '". $email ."' AND password = '". $password ."' AND a.status=1");

if($force_login)
{
	// echo 
	// -- echo "SELECT a.*, b.name `group_name` FROM inv_users `a`, inv_groups `b` WHERE a.group_id = b.id AND email = '". $email ."' AND md5(salt) = '". ($_GET['salt']) ."'";exit;
	$user =  $db->func_query_first("SELECT a.*, b.name `group_name` FROM inv_users `a`, inv_groups `b` WHERE a.group_id = b.id AND email = '". $email ."' AND md5(salt) = '". ($_GET['salt']) ."' and a.status=1");	
	// print_r($user);exit;
}


if ($user) {
	$permissions = $db->func_query("SELECT b.`name`, b.`perm` FROM inv_group_perm `a`, inv_perm `b` WHERE a.group_id = '". $user['group_id'] ."' AND a.perm_id = b.id");
	$_SESSION['login_as'] = 'employee';
} else {
	$error = 1;
}
if ($user['group_name'] == 'Super Admin' || $user['group_name'] == 'Programmer' || $user['group_name'] == 'Admin') {
	$permissions = $db->func_query("SELECT b.`perm` FROM inv_perm `b`");
	$_SESSION['login_as'] = 'admin';
}
if ($user['group_name'] == 'Super Admin' || $user['group_name'] == 'Programmer' || $user['group_name'] == 'Admin') {
	$_SESSION['super_admin'] = 1;
}

if (!$error) {

	$_SESSION['email']    = $user['email'];
	$_SESSION['user_name'] = $user['name'];
	$_SESSION['id'] = $user['id'];
	$_SESSION['user_id'] = $user['id'];
	$_SESSION['group'] = $user['group_name'];
	$_SESSION['is_sales_agent'] = 0;
	// echo $_SESSION['user_name'];exit;
	foreach ($permissions as $permission) {
		$_SESSION[$permission['perm']] = 1;
	}
	
	logIP($_SESSION['user_id']);
	
		
	
	// print_r($_SESSION);exit;
	if($user['is_sales_agent']=='1')
	{
		$_SESSION['is_sales_agent'] = 1;
		$redirect = 'sales_dashboard_new.php';
	}
	else
	{
		$redirect = 'home.php';
	}
	header("Location:$redirect");
	exit;
} else {

	//$_SESSION['error'] = "Invalid Credentials";
	header("Location:index.php");
	exit;
}
?>