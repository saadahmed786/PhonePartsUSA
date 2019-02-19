<?php
require_once("auth.php");
function generateRandomString($length = 12) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()=-';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}
if($_SESSION['login_as'] != 'admin'){
	if ($_SESSION['user_id'] != (int)$_GET['id'] || $_GET['mode'] != 'edit') {
		echo 'You dont have permission to manage users.';
		exit;
	}
}
$mode = $_GET['mode'];
if($mode == 'edit'){
	$user_id = (int)$_GET['id'];
	$user = $db->func_query_first("select * from inv_users where id = '$user_id'");
}
if ($_POST['action'] == 'load_providence') {
	$cId = (int)$_POST['id'];
	$zones = $db->func_query("SELECT * FROM `oc_zone` WHERE `country_id` = '$cId'");
	$json['data'] = '<option value="">--Select--</option>';
	foreach ($zones as $i => $zone) {
		$json['data'] .= '<option value="'. $zone['name'] .'">' . $zone['name'] . '</option>';
	}
	echo json_encode($json);
	exit;
}
if($_POST['add']){
	unset($_POST['add']);
	$allowed = array('png', 'jpeg', 'jpg');
	$uploaded = 0;
	if ($_FILES['upFile']['tmp_name']) {
		$uniqid = uniqid();
		$name = explode(".", $_FILES['upFile']['name']);
		$ext = end($name);
		$fileName = $uniqid . '-' . $_POST['email'] . '.' . $ext;
		$dir = 'images/user/';
		$destination = $path . $dir . $fileName;
		$file = $_FILES['upFile']['tmp_name'];
		if (in_array($ext, $allowed)) {
			if (move_uploaded_file($file, $destination)) {
				$message .= 'Logo Uploaded <br>';
				$uploaded = 1;
			}
		} else {
			$message .= 'Logo Not Uploaded <br>';
		}
	}
	$_POST['image'] = ($uploaded)? $dir . $fileName : '';
	$email = $db->func_escape_string($_POST['email']);
	$isExist = $db->func_query_first("select id from inv_users where email = '$email'");
	if(!$isExist || $user_id){
		$user_arr = array();
		$error = array();
		
		//unset($_POST['status']);
		$user_arr = $_POST;
		$user_arr['country'] = $db->func_query_first_cell("SELECT name FROM `oc_country` WHERE country_id = '". $user_arr['country'] ."'");
		$user_arr['salt'] = generateRandomString();
		if(!$user_arr['password']){
			unset($user_arr['password']);
			unset($user_arr['salt']);
			$user_arr['salt'] = $db->func_query_first_cell("SELECT salt FROM inv_users WHERE email = '$email'");
		}
		else
		{
			$user_arr['password'] = md5($user_arr['password'].$user_arr['salt']);
		}
		if ($_SESSION['login_as'] == 'admin' || $user['is_manager'] == 1) {
			$where = '';
			if ($user_id) {
				$where = ' AND id != "' . $user_id . '"';
			}
			if ($user_arr['manager_pin'] && !$db->func_query_first_cell("SELECT id FROM inv_users WHERE manager_pin = md5(concat(email,'". $user_arr['manager_pin'] ."',salt)) $where")) {
				$user_arr['manager_pin'] = md5($user_arr['email'].$user_arr['manager_pin'].$user_arr['salt']);
			} else {
				if ($user_arr['manager_pin']) {
					$error['manager_pin'] = 'Already Exist Please Change the pin';
				}
				unset($user_arr['manager_pin']);
			}
			if ($user_arr['qc_lead_pin'] && !$db->func_query_first_cell("SELECT id FROM inv_users WHERE qc_lead_pin = md5(concat(email,'". $data['qc_lead_pin'] ."',salt)) $where")) {
				$user_arr['qc_lead_pin'] = md5($user_arr['email'].$user_arr['qc_lead_pin'].$user_arr['salt']);
			} else {
				if ($user_arr['qc_lead_pin']) {
					$error['qc_lead_pin'] = 'Already Exist Please Change the pin';
				}
				unset($user_arr['qc_lead_pin']);
			}
		}
// echo $_POST['status'];exit;
if(isset($_POST['status']))
		{
			$user_arr['status']=1;
		}
		else
		{
			$user_arr['status']=0;	
		}
		if(isset($_POST['is_sales_agent']))
		{
			$user_arr['is_sales_agent']=1;
		}
		else
		{
			$user_arr['is_sales_agent']=0;	
		}
		if(isset($_POST['is_servicer']))
		{
			$user_arr['is_servicer']=1;
		}
		else
		{
			$user_arr['is_servicer']=0;	
		}
		$user_arr['dateofmodification'] = date('Y-m-d H:i:s');
		$user_arr['weekly_target'] = $_POST['weekly_target'];
		$_user_id = $user_id;
		if($user_id){
			$db->func_array2update("inv_users",$user_arr,"id = '$user_id'");
		} else {
			$_user_id = $db->func_array2insert("inv_users",$user_arr);
		}
		$db->db_exec("DELETE FROM inv_coa WHERE code='V".$_user_id."'");
		$db->db_exec("INSERT INTO inv_coa SET code='V".$_user_id."'");
		$_SESSION['message'] = $message . "User Updated";
		if (!$error) {
			if ($_SESSION['user_id'] == (int)$_GET['id']) {
			header("Location:user.php?id=" . $_GET['id'] . '&mode=edit');
			} else {
			header("Location:users.php");
			}
			exit;
		}
	} else {
		$_SESSION['message'] = $message . "This email is already assigned to another email";
		$user = $_POST;
	}
}
$user['country'] = $db->func_query_first_cell("SELECT country_id FROM `oc_country` WHERE name = '". $user['country'] ."'");
$user['providence'] = $db->func_query_first_cell("SELECT zone_id FROM `oc_zone` WHERE name = '". $user['providence'] ."' AND country_id = '". $user['country'] ."'");
$groups = $db->func_query("SELECT id , name FROM inv_groups WHERE lower(name) NOT IN ('super admin'". ((!$_SESSION['super_admin'])? ", 'programmer', 'admin'": '') .")");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Add/Edit User</title>
	<style type="text/css" media="all">
		.upMain {
			position: relative;
		}
		.ui.blue.button.upMain {
			display: inline-block;
		}
	</style>
	<script type="text/javascript">
		function allowInt (t, e) {
			var re = /^-?[0-9]+$/;
			var input = $(t).val();
			var valid = input.substring(0, input.length - 1);
			if (input.length > 6 || input.length < 4) {
				e.target.setCustomValidity("Pin should be 4-6 numbers");
			} else {
				e.target.setCustomValidity("");
			}
			if (!re.test(input)) {
				$(t).val(valid);
			}
		}
	</script>
</head>
<body>
	<div align="center">
		<div align="center"> 
			<?php include_once 'inc/header.php';?>
		</div>
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		<form action="" method="post" enctype="multipart/form-data">
			<h2>User Details</h2>
			<table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td>Name</td>
					<td><input type="text" name="name" value="<?php echo @$user['name'];?>" required /></td>
				</tr>
				<tr>
					<td>Email</td>
					<td><input type="email" name="email" value="<?php echo @$user['email'];?>" required /></td>
				</tr>
				<?php if ($_SESSION['login_as'] == 'admin') { ?>
				<tr>
					<td>Calendar Email</td>
					<td><input type="email" name="gmail" value="<?php echo @$user['gmail'];?>" /></td>
				</tr>
				<?php } ?>
				<tr>
					<td>Password</td>
					<td><input type="password" name="password" value="" autocomplete="off" <?php echo ($mode != 'edit')? 'required': ''; ?> /></td>
				</tr>
				<tr style="<?php echo ($_SESSION['login_as']!='admin'?'display:none':'');?>">
					<td>Active</td>
					<td><input type="checkbox" name="status" value="1" <?php if($user['status']):?> checked="checked" <?php endif;?> /></td>
				</tr>
				
				<?php if($_SESSION['login_as'] == 'admin'): ?>
					<tr>
						<td>Group</td>
						<td>
							<select name="group_id">
								<?php foreach($groups as $group):?>
									<option value="<?php echo $group['id'];?>" <?php if($group['id'] == $user['group_id']):?> selected="selected" <?php endif;?>><?php echo $group['name'];?></option>
								<?php endforeach;?>
							</select>
						</td>
					</tr>
				<?php endif;?>
				<tr>
					<td>Telephone Number</td>
					<td><input type="text" name="phone_no" value="<?php echo @$user['phone_no'];?>" /></td>
				</tr>
				
				<tr style="<?php echo ($_SESSION['login_as']!='admin'?'display:none':'');?>">
					<td>Is Sales Agent?</td>
					<td><input type="checkbox" name="is_sales_agent" <?=($user['is_sales_agent']==1?'checked':'');?> /><br>
					Weekly:<input type="text" name="weekly_target" value="<?php echo $user['weekly_target'];?>" placeholder="Weekly Target"><br>
					Quarterly:<input type="text" name="quarter_target" value="<?php echo $user['quarter_target'];?>" placeholder="Quarterly Target"><br>
					Commission %:<input type="text" name="commission" value="<?php echo $user['commission'];?>" placeholder="Commission %"><br>
					Comm Date:<input type="text" name="commission_date" value="<?php echo @$user['commission_date'];?>" class="datepicker">
					</td>
				</tr>
				
				<tr style="<?php echo ($_SESSION['login_as']!='admin'?'display:none':'');?>">
					<td>Is Servicer?</td>
					<td><input type="checkbox" name="is_servicer" <?=($user['is_servicer']==1?'checked':'');?> /></td>
				</tr>
				<?php if($_SESSION['login_as'] == 'admin' || $user['is_manager'] == 1) { ?>
				<tr>
					<td><?php if ($_SESSION['login_as'] != 'admin' && $user['is_manager'] == 1) { ?>Manager Pin<?php } else { ?>Is Manager?<?php } ?></td>
					<td><?php if ($_SESSION['login_as'] == 'admin') { ?><input type="checkbox" name="is_manager" value="1" <?php echo (!$user['manager_pin'])? "onclick=\"($(this).is(':checked')) ? $(this).next('input').attr('required', 'required') : $(this).next('input').removeAttr('required');\"": ""; ?> <?= ($user['is_manager']==1?'checked':'');?> /> <?php } ?><?php if ($_SESSION['login_as'] == 'admin' || $user['is_manager'] == 1) { ?><input type="password" name="manager_pin" autocomplete="off" onkeyup="allowInt(this, event);" style="width: 70px;" /> <?php echo $error['manager_pin']; ?><?php } ?></td>
				</tr>
				<?php } ?>
				<?php if($_SESSION['login_as'] == 'admin' || $user['is_qc_lead'] == 1) { ?>
				<tr>
					<td><?php if ($_SESSION['login_as'] != 'admin' && $user['is_qc_lead'] == 1) { ?>QC Lead Pin<?php } else { ?>Is QC Lead?<?php } ?></td>
					<td><?php if ($_SESSION['login_as'] == 'admin') { ?><input type="checkbox" name="is_qc_lead" value="1" <?php echo (!$user['manager_pin'])? "onclick=\"($(this).is(':checked')) ? $(this).next('input').attr('required', 'required') : $(this).next('input').removeAttr('required');\"": ""; ?> <?=($user['is_qc_lead']==1?'checked':'');?> /> <?php } ?><?php if ($_SESSION['login_as'] == 'admin' || $user['is_qc_lead'] == 1) { ?><input type="password" name="qc_lead_pin" autocomplete="off" onkeyup="allowInt(this, event);" style="width: 70px;" /> <?php echo $error['qc_lead_pin']; ?><?php } ?></td>
				</tr>
				<?php } ?>
				<tr>
					<td>Date of Birth</td>
					<td><input type="text" name="dob" class="datepicker" value="<?php echo @$user['dob'];?>" /></td>
				</tr>
				<tr>
					<td>Website</td>
					<td><input type="text" name="website" value="<?php echo @$user['website'];?>" /></td>
				</tr>
				<tr>
					<td>Company Name</td>
					<td><input type="text" name="company_name" value="<?php echo @$user['company_name'];?>" /></td>
				</tr>
				<tr>
					<td>Company Logo</td>
					<td>
						<label class="ui blue button upMain" style="color: #fff;" for="mainimageup">
							<input onchange="validateFileUp(this);" type="file" style="opacity: 0; position: absolute; width: 100%; height: 23px; top: 0; left: 0;" name="upFile" accept="image/jpeg,image/png">
							Upload New
						</label>
						<div id="imagePrv" style="display: inline-block;">
							<?php if ($user['image']) { ?>
							<a class="fancybox2 fancybox.iframe" href="<?php echo $host_path . $user['image']; ?>" target="_blank">
								<img width="100px" src="<?php echo $host_path . $user['image']; ?>" alt="" />
							</a>
							<?php } ?>
						</div>
					</td>
				</tr>
				<tr>
					<td>Address Line 1</td>
					<td><input type="text" name="address_1" value="<?php echo @$user['address_1'];?>" /></td>
				</tr>
				<tr>
					<td>Address Line 2</td>
					<td><input type="text" name="address_2" value="<?php echo @$user['address_2'];?>" /></td>
				</tr>
				<tr>
					<td>Country</td>
					<td>
						<select name="country" onchange="loadForm('providence', this)" >
							<option value="">--Select--</option>
							<?php foreach($db->func_query("SELECT * FROM `oc_country`") as $country):?>
								<option value="<?php echo $country['country_id'];?>" <?php if($country['country_id'] == $user['country']):?> selected="selected" <?php endif;?>><?php echo $country['name'];?></option>
							<?php endforeach;?>
						</select>
					</td>
				</tr>
				<tr>
					<td>State</td>
					<td>
						<select name="providence" >
							<option value="">--Select--</option>
							<?php foreach($db->func_query("SELECT * FROM `oc_zone` WHERE `country_id` = '". $user['country'] ."'") as $zone):?>
								<option value="<?php echo $zone['name'];?>" <?php if($zone['zone_id'] == $user['providence']):?> selected="selected" <?php endif;?>><?php echo $zone['name'];?></option>
							<?php endforeach;?>
						</select>
					</td>
				</tr>
				<tr>
					<td>City</td>
					<td><input type="text" name="city" value="<?php echo @$user['city'];?>"  /></td>
				</tr>
				<tr>
					<td>Postal Code</td>
					<td><input type="text" name="postal_code" value="<?php echo ($user['postal_code'])? $user['postal_code']: '';?>"  /></td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="add" value="Submit" />
					</td>
				</tr>
			</table>
		</form>
	</div>
	<script>
		function loadForm (type, t) {
			$.ajax({
				url: 'user.php',
				type: 'POST',
				dataType: 'json',
				data: {id: $(t).val(), action:'load_'+type},
			})
			.always(function(json) {
				$('select[name='+type+']').html(json['data']);
			});
		}
		function validateFileUp (t) {
			var file = $(t).val().split(".");
			var ext = file.pop();
			var allowed = ['png', 'jpeg', 'jpg'];
			if ($.inArray(ext, allowed) >= 0) {
				if ($(t)[0].files[0]) {
					var reader = new FileReader();
					var src = '';
					reader.onload = function (e) {
						src = e.target.result;
						var data = '<a class="fancybox2 fancybox.iframe" href="'+ src +'" target="_blank">'
						+ '<img width="100px" src="'+ src +'" alt="" />'
						+ '</a>';
						$('#imagePrv').find('a').remove();
						$('#imagePrv').prepend(data);
					}
					reader.readAsDataURL($(t)[0].files[0]);
				}
			} else {
				alert('This File is not Allowed');
			}
		}
	</script>
</body>
</html>			 		 