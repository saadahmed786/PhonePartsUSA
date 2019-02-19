<?php
require_once("auth.php");
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';
if ($_POST['action'] == 'loginCheck') {
	$permission = 0;
	$isExist  = $db->func_query_first("select * from admin where email = '". $_POST['uname'] ."' && password = '". $_POST['upass'] ."'");
	if ($isExist) {
		$permission = 1;
	} else {
		$isExist  = $db->func_query_first("select * from inv_users where email = '". $_POST['uname'] ."' && password = '". $_POST['upass'] ."' and status = 1");
		if ($isExist) {
			$permission = (int)$db->func_query_first_cell("select order_price_override from inv_group_permissions where group_id = '". $isExist['group_id'] ."'");
		}
	}

	if ($permission) {
		$json['success'] = 1;
	} else {
		$json['error'] = 'Please contact your Admin for permission';
	}

	echo json_encode($json);
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Payment Status</title>
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />


	
	<style type="text/css">
		.data td,.data th {
			border: 1px solid #e8e8e8;
			text-align:center;
			width: 150px;
		}
		.div-fixed{
			position:fixed;
			top:0px;
			left:8px;
			background:#fff;
			width:98.8%; 
		}
		.red td{ box-shadow:1px 2px 5px #990000;}
	</style>
</head>
<body>
	<div align="center" style="display:none"> 
		<?php include_once '../inc/header.php';?>
	</div>

	<?php if($_SESSION['message']):?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
	<?php else:?>
		<br /><br />
	<?php endif;?>

	<div align="center">
		<div id="aim_div">
		<table border="1" width="60%" cellpadding="5" cellspacing="0" align="center" id="aim_table">
			<tr>
				<td><label for="uname">User Name</label></td>
				<td><input id="uname" type="text" placeholder="User Name" /></td>
			</tr>
			<tr>
				<td><label for="upass">Password</label></td>
				<td><input id="upass" type="password" placeholder="Password" /></td>
			</tr>
			<tr>
				<td colspan="2"><button id="buttonx" onclick="verifyUser();" index="<?php echo $_GET['index']; ?>" price="<?php echo $_GET['price']; ?>" type="button">Verify</button></td>
			</tr> 
		</table>
		</div>
	</div>			
	<br />
<script type="text/javascript">
	function verifyUser () {
		var uname = $('#uname').val();
		var upass = $('#upass').val();
		var index = $('#buttonx').attr('index');
		var price = $('#buttonx').attr('price');
		$.ajax({
			url: 'priceChangeReq.php',
			type: 'POST',
			dataType: 'json',
			data: {uname: uname, upass: upass, action: 'loginCheck'},
		}).always(function(json) {
			if (json['success']) {
				window.parent.ChangePrice(index, price);
				window.parent.parent.$.fancybox.close();
			} else {
				alert(json['error']);
			}
		});
		
	}
</script>

</body>
</html>