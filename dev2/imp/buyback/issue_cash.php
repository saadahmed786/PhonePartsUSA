<?php
include("../phpmailer/class.smtp.php");
include("../phpmailer/class.phpmailer.php");
require_once("../auth.php");
require_once("../inc/functions.php");

if ($_POST['action'] == 'paypalpay') {
	$lbbDetails = $db->func_query_first("SELECT * FROM oc_buyback WHERE buyback_id='".$_POST['buyback_id']."'");

	$emailInfo = $_SESSION['lbbEmail'][$_POST['buyback_id']];
	$emailInfo['sending_amount'] = $_POST['price'];

	include("../paypal/paypal.php");

	$paypal = new payPal();
	$detail['email'] = $_POST['email'];
	$detail['amount'] = $_POST['price'];
	$detail['host'] = $host_path;
	$return = $paypal->payUser($detail);

	if ($return['responseEnvelope']['ack'] == 'Success') {
		$log = 'LBB Shipment# ' . linkToLbbShipment($lbbDetails['shipment_number']) . ' Payment has been made of amount $'.number_format((float)$_POST['price'],2) . ' Paypal Transaction ID is ' . $return['responseEnvelope']['correlationId'];
		actionLog($log);
		$email = array();
		$email['title'] = shortCodeReplace($emailInfo ,$_POST['title']);
		$email['subject'] = shortCodeReplace($emailInfo ,$_POST['subject']);
		$email['message'] = shortCodeReplace($emailInfo ,$_POST['comment']);
		$email['number'] = array('title'=>'Shipment No','value'=>$lbbDetails['shipment_number']);
		
		if($_POST['attach_pdf'])
		{
			$attach_pdf = '../files/'.$lbbDetails['file_pdf'];
		}
		else
		{
			$attach_pdf='';
		}
		sendEmailDetails($emailInfo, $email,array(),$attach_pdf);
		unset($_SESSION['lbbEmail'][$_POST['buyback_id']]);
		$json = array('success' => 1, 'transaction_id' => $return['responseEnvelope']['correlationId']);
	} else {
		$json = array('error' => 1, 'msg' => 'Try some other time');
	}
	echo json_encode($json);
	exit;
}

$buyback_id = $db->func_escape_string($_GET['buyback_id']);
$buyback_id = (int)$buyback_id;
$detail = $db->func_query_first("SELECT * FROM oc_buyback WHERE buyback_id='".$buyback_id."'");

$firstname = $db->func_escape_string(base64_decode($_GET['firstname']));
$lastname = $db->func_escape_string(base64_decode($_GET['lastname']));
$email = $db->func_escape_string(base64_decode($_GET['email']));




$_products = $db->func_query("SELECT * FROM oc_buyback_products WHERE buyback_id='".$buyback_id."'");
$detail['total'] = 0.00;
foreach($_products as $_product)
{
	//$detail['total']+= $_product['total_oem_total']+$_product['total_non_oem_total'];
	
	
	$quantities = $db->func_query_first("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$_product['buyback_product_id']."'");

	if($quantities)
	{
		$oem_qty = (int)$quantities['oem_received'];
		$non_oem_qty = (int)$quantities['non_oem_received'];
	}
	if($_product['admin_updated']=='1')
	{
		$oem_qty = $_product['admin_oem_qty'];

		$non_oem_qty = $_product['admin_non_oem_qty'];
	}

	$admin_total = ($oem_qty * $_product['oem_price']) + ($non_oem_qty * $_product['non_oem_price']);;

	$admin_combine_total+=(float)$admin_total;

}
$detail['total'] = (float)$admin_combine_total;

$emailInfo['customer_name'] = $firstname.' '.$lastname;
$emailInfo['total_amount'] = $detail['total'];


$emailInfo['email'] = $email;
$adminInfo = array('name' => $_SESSION['login_as'], 'company_info' => $_SESSION['company_info'] );
$cash_discount = $db->func_query_first_cell("SELECT cash_discount FROM inv_buy_back LIMIT 1");

if(!$cash_discount) $cash_discount = 0.00;

$discount = ((float)$detail['total'] * (float)$cash_discount) / 100;
$discount = round($discount,2);
$detail['total'] = $detail['total'] - $discount;

//$emailInfo['sending_amount'] = $detail['total'];
$_SESSION['lbbEmail'][$buyback_id] = $emailInfo;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Store Credit</title>
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
</head>
<?php
if(isset($_POST['transaction_id']))
{
	$i=0;
	



	$data = array();
	$data['buyback_id'] = $detail['buyback_id'];
	$data['payment_type'] = 'Cash';
	$data['amount'] = (float)$_POST['price'];
	$data['transaction_id'] = $db->func_escape_string($_POST['transaction_id']);
	$data['notes'] = '';
	$data['date_added'] = date('Y-m-d H:i:s');

	$db->func_array2insert("inv_buyback_payments",$data);


	$data = array();
	$data['buyback_id'] = $detail['buyback_id'];
	$data['comment'] = $_SESSION['login_as'].' added a transaction id # '.$_POST['transaction_id'].' of amount $'.number_format((float)$_POST['price'],2) . ' for email ' . $_POST['email'];
	$data['date_added'] = date('Y-m-d H:i:s');


	$db->func_array2insert("inv_buyback_comments",$data);

	



	

	echo '<h1>Payment details have been saved!</h1>';

	echo '<script> $("input[name=save]", window.parent.document).click();</script>';exit;
}

?>
<body>
	<div align="center">


		<div align="center"><br />
			<font color="red">In order to get updated result, please make sure to press "Save" button after making all of your changes.<br /></font>
		</div>

		<br clear="all" />



		<div align="center">
			<form action="" id="myFrm" method="post">
				<h2>Issue Payment</h2>
				<table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;width:90%">
					<tr>
						<td>Shipment #</td>
						<td><?php echo $detail['shipment_number'];;?></td>
					</tr>

					<tr>
						<td>Email</td>
						<td><input type="email" required name="email" value="<?= $detail['paypal_email'];?>" /><input type="hidden" name="buyback_id" value="<?= $buyback_id;?>" /></td>
					</tr>					

					<tr>
						<td>Amount</td>
						<td>
							<?php if ($_SESSION['login_as'] == 'admin') { ?>
							<input type="text" required name="price" value="<?php echo round($detail['total'],2);?>" />
							<?php } else { ?>
							<input type="hidden" required name="price" value="<?php echo round($detail['total'],2);?>" />
							$<?php echo round($detail['total'],2);?>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<input type="checkbox" name="attach_pdf" value="1" onchange="generatePDF(this);"> Attach PDF to customer email
						</td>
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



<tr style="display: none;">
	<td>Transaction ID</td>
	<td><input type="hidden" name="transaction_id" id="transaction_id" value="" required /></td>
</tr>

<tr>
	<td colspan="2" align="center">
		<input type="button" name="add" value="Generate" onclick="paypalpay()" />
	</td>
</tr>


</table>
</form>
</div>		

<script>

		// function submitForm() {
		// 		if($('#transaction_id').val()=='')
		// 		{
		// 			alert('Please provide the transaction id');
		// 			return false;	
		// 		}
		// 		$('#myFrm').submit();
		// 	}
		function paypalpay() {
			var email = $('input[name="email"]').val();
			var price = $('input[name="price"]').val();
			var buyback_id = $('input[name="buyback_id"]').val();
			var attach_pdf = 1;
			if($('input[name="attach_pdf"]').is(':checked')==false) {
				attach_pdf = 0;
			}
			$.ajax({
				url: 'issue_cash.php',
				type:"POST",
				dataType:"json",
				data:{'email':email,'price':price,'buyback_id':buyback_id, 'attach_pdf':attach_pdf, 'action':'paypalpay', title: $('input[name="title"]').val(), subject: $('input[name="subject"]').val(), canned_id: $('input[name="canned_id"]').val(), comment: CKEDITOR.instances.comment.getData()},
				success: function(json){
					if (json['success']) {
						$('input[name="transaction_id"]').val(json['transaction_id']);
						alert('Payment Success Please Wait To Complete Process');
						$('#myFrm').submit();
					}
					if (json['error']) {
						alert(json['msg']);
					}
				}
			});
		}

		function generatePDF(obj)
			{

				if($(obj).is(':checked')==false) return false;

				$.ajax({

					url: "pdf_report.php?shipment_number=<?=$detail['shipment_number'];?>",
					type: "GET",



					success: function (data) {
							/*if (json['error'])
							{
								alert(json['error']);
								return false;

							}

							if (json['success'])
							{

								$('#credit_code').val(json['success']);
							}
						}*/
						console.log('PDF Generated');
					}
				});

			}
	</script> 
</div>		     
</body>
</html>