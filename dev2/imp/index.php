<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 'Off');
require_once("config.php");
require_once("inc/functions.php");
$security = true;
if($_POST['security_action']=='error_check')
{
	$passkey = oc_config('config_security_404_passkey');

	if($_POST['security_passkey']==$passkey) $security = false;
}
if(strpos($host_path,'dev2')==true) $security = false; // in case of dev server
if($security)
{
//require_once("admin_404_check.php");
}

if($_SERVER['SERVER_NAME']=='phonepartsusa.com')
{
	header("Location: http://imp.phonepartsusa.com/");
}
$is_firefox=false;
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $agent = $_SERVER['HTTP_USER_AGENT'];
}
if (strlen(strstr($agent, 'Firefox')) > 0) {
    $is_firefox = true;
}
if($is_firefox==false and !isset($_GET['browser']))
{
//echo '<h1>The System is only compatible with the Firefox browser.</h1>';exit;
}
if($_SESSION['email']){
	header("Location:home.php");
	exit;
}
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html>


<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta charset="utf-8" />
<title>PhonePartsUSA IMP - Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no" />
<link rel="apple-touch-icon" sizes="76x76" href="pages/ico/76.png">
<link rel="apple-touch-icon" sizes="120x120" href="pages/ico/120.png">
<link rel="apple-touch-icon" sizes="152x152" href="pages/ico/152.png">
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta content="" name="description" />
<meta content="" name="author" />
<link href="assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" media="screen" />
<link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
<link href="assets/plugins/switchery/css/switchery.min.css" rel="stylesheet" type="text/css" media="screen" />
<link href="pages/css/pages-icons.css" rel="stylesheet" type="text/css">
<link class="main-stylesheet" href="pages/css/themes/modern.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
    window.onload = function()
    {
      // fix for windows 8
      if (navigator.appVersion.indexOf("Windows NT 6.2") != -1)
        document.head.innerHTML += '<link rel="stylesheet" type="text/css" href="pages/css/windows.chrome.fix.css" />'
    }
    </script>
</head>
<body class="fixed-header ">
<div class="login-wrapper " style="background:none">

<div class="bg-pic">

<img src="https://phonepartsusa.com/image/cache/new_site/home/home-bg-960x391.jpg" data-src="https://phonepartsusa.com/image/cache/new_site/home/home-bg-960x391.jpg" data-src-retina="https://phonepartsusa.com/image/cache/new_site/home/home-bg-960x391.jpg" alt="" class="lazy">


<div class="bg-caption pull-bottom sm-pull-bottom text-black p-l-20 m-b-20">
<h2 class="semi-bold text-black">
Inventory Management System</h2>
<p class="small">
A Whole eCommerce Management Platform with Inventory, Finance, Return &amp; Customer Relationship Management. All work copyright of PhonePartsUSA &copy; 2015-<?php echo date('Y');?>.
</p>
</div>

</div>


<div class="login-container bg-white">
<div class="p-l-50 m-l-20 p-r-50 m-r-20 p-t-50 m-t-30 sm-p-l-15 sm-p-r-15 sm-p-t-40">
<img src="https://phonepartsusa.com/image/logo_new.png" alt="logo" data-src="https://phonepartsusa.com/image/logo_new.png" data-src-retina="https://phonepartsusa.com/image/logo_new.png" height="22" style="margin-left:15%" >
<p class="p-t-35">Sign into your PPUSA account</p>

<form id="form-login" class="p-t-15" role="form" action="login.php">

<div class="form-group form-group-default">
<label>Login</label>
<div class="controls">
<input type="text" name="email" placeholder="Email" class="form-control" required>
</div>
</div>


<div class="form-group form-group-default">
<label>Password</label>
<div class="controls">
<input type="password" class="form-control" name="password" placeholder="Credentials" required>
 </div>
</div>

<div class="row">
<div class="col-md-12 no-padding sm-p-l-10">
<div class="checkbox ">
<input type="checkbox" value="1" id="checkbox1">
<label for="checkbox1">Keep Me Signed in</label>
</div>
</div>

</div>

<button class="btn btn-primary btn-cons m-t-10" type="submit">Sign in</button>
</form>


</div>
</div>

</div>




<script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery/jquery-1.11.1.min.js" type="text/javascript"></script>
<script src="assets/plugins/modernizr.custom.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="assets/plugins/tether/js/tether.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery/jquery-easy.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>


<script>
    $(function()
    {
      $('#form-login').validate()
    })
    </script>
</body>


</html>