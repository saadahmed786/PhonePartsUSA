<?php
include_once '../auth.php';

include_once '../inc/functions.php';
include("../phpmailer/class.smtp.php");
include("../phpmailer/class.phpmailer.php");
if(!$_SESSION['approve_return_shipp'] and !$_SESSION['approve_send_label'])
{
	echo 'Permission Denied';
	exit;
}



function sendLbbLabel($email,$data,$filename)
{
	
	sendEmailDetails ($data, $email,array(),$filename);
}

$message = false;
$buyback_id = (int)$_GET['buyback_id'];
$detail = $db->func_query_first("SELECT * FROM oc_buyback WHERE buyback_id='".(int)$buyback_id."'");

if($detail['weight']==0.00)
{
	$lbb_products = $db->func_query("SELECT sku FROM oc_buyback_products WHERE buyback_id='".(int)$buyback_id."'");
	$_weight = 0.00;
	foreach($lbb_products as $_lbb)
	{
		$original_weight = $db->func_query_first_cell("select weight from inv_buy_back WHERE sku='".$lbb['sku']."'");
		$_weight = (float)$_weight + (float)$original_weight;
	}
	$detail['weight'] = $_weight;
}
$detail['weight_lb'] = $detail['weight'] / 16;

if(!$detail)
{
	echo 'Error found! Please try again';
	exit;
}
$firstname = base64_decode($_GET['firstname']);
$lastname = base64_decode($_GET['lastname']);
$email = base64_decode($_GET['email']);

$emailInfo['customer_name'] = $firstname.' '.$lastname;

$emailInfo['total_amount'] = $detail['total'];

$emailInfo['email'] = $email;

$adminInfo = array('name' => $_SESSION['login_as'], 'company_info' => $_SESSION['company_info'] );



if(@$_POST['update']){
	//print_r($_POST);exit;
	$shipping_carrier = $_POST['shipping_carrier'];
	$shipping_carrier = explode("~",$shipping_carrier);
	$carrier_code = $shipping_carrier[0];
	$service_code = $shipping_carrier[1];
	$data = array();
	$data['carrier_code'] = $carrier_code;
	$data['service_code'] = $service_code;
	$data['weight'] = (float)$_POST['weight'];
	$data['approval_count'] = (int)$_POST['approval_count'] + 1;

	if($_SESSION['approve_send_label'])	{
		$data['approval_count'] = 2;
	}
	
	
	
	if($data['approval_count']==1) {
		$db->db_exec("UPDATE oc_buyback SET approved_user_id1 = '".$_SESSION['user_id']."' where buyback_id='".$detail['buyback_id']."'");
	}
	else if($data['approval_count']==2) {
		$db->db_exec("UPDATE oc_buyback SET approved_user_id2 = '".$_SESSION['user_id']."' where buyback_id='".$detail['buyback_id']."'");
	}

	$db->func_array2update('oc_buyback', $data,"buyback_id='".$detail['buyback_id']."'");

	if($_SESSION['approve_send_label'] or $detail['approval_count']==1)
	{
		// Shipstation Create Label
		include "../shipstation/create_shipment_label.php";
		if($_label_created==true)
		{
			$_SESSION['message'] = 'Shipping Label Created Successfully';
			
			$_email = array();
			$data = array();
			$_email['title'] = $db->func_escape_string($_POST['title']);
			$_email['number']['title'] = 'Shipment #';
			$_email['number']['value'] = $detail['shipment_number'];
			// $_email['message'] = 'Dear '.$firstname.' '.$lastname.'!<br>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.<br><br>';
			$_email['message'] = $db->func_escape_string($_POST['comment']);
			// $_email['subject'] = 'LBB Shipping Label has been created - PhonePartsUSA';
			$_email['subject'] = $db->func_escape_string($_POST['subject']);
			$_email['image']= 'https://phonepartsusa.com/image/buyback_email.png';
			$data['email'] = $email;

		// $data['email'] = 'xaman.riaz@gmail.com';
			$data['customer_name'] = $firstname.' '.$lastname;
			
			sendLbbLabel($_email,$data,'../image/labels'.$filename);


		}
		else
		{
			$_SESSION['message']= 'Error: Shipping Label not Created! Please verify if your order has been synced into Shipstation or contact admin';
			if($_SESSION['approve_send_label'] && $detail['approval_count']==0)	{
				$approval_count = 'approval_count - 2';
			} else {
				$approval_count = 'approval_count - 1';
			}
			$db->db_exec("UPDATE oc_buyback SET approved_user_id2 = 0, approval_count = $approval_count where buyback_id='".$detail['buyback_id']."'");
		}

	}
	else
	{
		
		
		$_SESSION['message'] = "Shipping Approved.";

	}

	
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
}

// $items = $db->func_query("select sku,description,oem,non_oem from inv_buy_back ");
?>
<html>
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<title>Store Credit</title>

	<script type="text/javascript" src="../js/jquery.min.js"></script>

	<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>

</head>

<style>
	*{
		font-size:17px !important;
	}
	body
	{
		margin-top:25% !important;
	}

</style>
<body>
	<div style="display:none">
		<?php include("../inc/header.php");?>
	</div>
	<div align="center">
		<?php if($message):?>
			<h5 align="center" style="color:red;"><?php echo $message;?></h5>
		<?php endif;?>

		<form method="post" id="frm">
			<table width="70%" cellpadding="5" cellspacing="0">
				<tr>
					<td>Shipping Carrier:</td>
					<td><select name="shipping_carrier" id="shipping_carrier" >

						<option value="fedex~fedex_ground" <?=($detail['carrier_code']=='fedex'?'selected':'');?>>FedEx Ground</option>
						<option value="express_1~usps_priority_mail_express" <?=($detail['carrier_code']=='express_1'?'selected':'');?>>USPS Priority</option>

					</select></td>
				</tr>
				<tr>
					<td>Shipment Weight:</td>
					<td><input type="text" style="width:70px"  id="weight_lb" onkeyup="changeWeight(this);" data-attr="lb"  value="<?php echo round($detail['weight_lb'],4);?>" /> lb <strong>OR</strong> <input type="text" style="width:70px" id="weight_oz" name="weight" onkeyup="changeWeight(this);" data-attr="oz"  value="<?=round($detail['weight'],4);?>" /> oz
						<br><span> (Enter lb OR oz, not both) </span></td>
					</tr>
					<tr>

						<td colspan="2">

							<div align="center">

								<table width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">

									<tr>

										<td width="30%">Canned Message:</td>

										<td width="70%">

											<?php $canned_messages = $db->func_query('SELECT * FROM `inv_canned_message` WHERE `catagory` = "3"'); ?>

											<select name="canned_id" id="canned_message">

												<option value=""> --- Custom --- </option>

												<?php foreach ($canned_messages as $canned_message) { ?>

												<option value="<?= $canned_message['canned_message_id']; ?>"><?= shortCodeReplace($emailInfo, $canned_message['name'])  ; ?></option>

												<?php } ?>                        

											</select>

										</td>

									</tr>

									<tr>

										<td>Title</td>

										<td><input type="text" name="title" id="canned_title" value=""/></td>

									</tr>

									<tr>

										<td>Subject</td>

										<td><input type="text" name="subject" id="canned_subject" value=""/></td>

									</tr>

									<tr>

										<td>Message:</td>

										<td><textarea name="comment" id="comment" class="comment-box" cols="40" rows="8" style="width: 99%"></textarea></td>

										<script>

											CKEDITOR.replace( 'comment' );

										</script>

									</tr>

									<tr>

										<td></td>

										<td><label class="addsd" for="signature_check"><input type="checkbox" id="signature_check" /> Add Signature</label><label class="addsd" for="disclaimer_check"><input type="checkbox" id="disclaimer_check" /> Add Disclaimer</label></td>

									</tr>

								</table>

								<ul style="display:none;">

									<textarea id="disclaimer"><div contenteditable="false"><?= $db->func_query_first_cell('SELECT `signature` FROM `inv_signatures` WHERE `type` = 1'); ?></div></textarea>

									<?php $src = $path .'files/sign_' . $_SESSION['user_id'] . ".png"; ?>

									<textarea id="signature"><div contenteditable="false"><?= shortCodeReplace($adminInfo, $db->func_query_first_cell('SELECT `signature` FROM `inv_signatures` WHERE `user_id` = "'. $_SESSION['user_id'] .'" AND type = 0')); ?> <?= (file_exists($src))? '<img src="'. $host_path .'files/sign_' . $_SESSION['user_id'] . '.png?'. time() .'" />': ''; ?></div></textarea>

									<?php foreach ($canned_messages as $canned_message) { ?>

									<textarea id="canned_<?= $canned_message['canned_message_id']; ?>"><?= shortCodeReplace($emailInfo, $canned_message['message']); ?></textarea>

									<li id="title_<?= $canned_message['canned_message_id']; ?>"><?= shortCodeReplace($emailInfo, $canned_message['title']); ?></li>

									<li id="subject_<?= $canned_message['canned_message_id']; ?>"><?= shortCodeReplace($emailInfo, $canned_message['subject']); ?></li>

									<?php } ?>

								</ul>



								<script type="text/javascript">

									var canned_messages = [<?php echo implode(',', $messages)?>];

									var msgs = {};

									$(function() {

										$('#email_form').submit(function () {

											if ($('#canned_title').val() == '' || $('#canned_subject').val() == '') {

												alert('Please Enter Your Message');

												return false;

											}

										});

										$('#canned_message').change(function() {

											var id = $(this).val();

											var message = '';

											if(id > 0) {

												message = $('#canned_' + id).text();

											}

											message = message + '<div id="customeData">';

											if ($('#signature_check').is(':checked')) {

												message = message + $('#signature').text();

											}

											if ($('#disclaimer_check').is(':checked')) {

												message = message + $('#disclaimer').text();

											}

											message = message + '</div>';

											$('#canned_title').val($('#title_' + id).text());

											$('#canned_subject').val($('#subject_' + id).text());

											CKEDITOR.instances.comment.setData(message);

										});

										$('.addsd').click(function() {

											if (!CKEDITOR.instances.comment.document.getById('customeData')) {

												message = CKEDITOR.instances.comment.getData() + '<div id="customeData"></div>';

												CKEDITOR.instances.comment.setData(message);

											}



											var message = '';

											if ($('#signature_check').is(':checked')) {

												message = message + $('#signature').text();

											}

											if ($('#disclaimer_check').is(':checked')) {

												message = message + $('#disclaimer').text();

											}

											CKEDITOR.instances.comment.document.getById('customeData').setHtml(message);





										});

										$('#canned_message').keyup(function() {

											$(this).change();

										});

									});

</script>

</div>

</td>

</tr>


					<tr>

						<td colspan="2" align="center"><br>
							<input type="hidden" name="approval_count" value="<?php echo $detail['approval_count'];?>">
							<input type="submit" class="button" name="update" value="<?php echo (($detail['approval_count']==1 || $_SESSION['approve_send_label'])?'Approve and Send':'Approve'); ?>" ></td>
						</tr>


					</table>


				</form>		
			</div>	
		</body>
		</html>
		<script>
			function changeWeight(obj)
			{
				var conversion = 'oz';
				var weight_oz = 0.0000;
				var weight_lb = 0.0000;
				if($(obj).attr('data-attr')=='oz')
				{
					var conversion = 'lb';
				}
				
				
				if(conversion =='oz')
				{
					var weight_lb = $('#weight_lb').val();
					
					if(weight_lb=='') weight_lb = 0.0000;


					weight_oz = parseFloat(weight_lb) * 16;
					weight_oz = weight_oz.toFixed(4);
					$('#weight_oz').val(weight_oz);
				}
				else
				{
					var weight_oz = $('#weight_oz').val();
					if(weight_oz=='') weight_oz = 0.0000;
					weight_lb = parseFloat(weight_oz) / 16;
					weight_lb = weight_lb.toFixed(4);
					$('#weight_lb').val(weight_lb);
				}
			}
		</script>