<?php
include("../phpmailer/class.smtp.php");
include("../phpmailer/class.phpmailer.php");
require_once("../auth.php");
require_once("../inc/functions.php");
$buyback_id = $db->func_escape_string($_GET['buyback_id']);
$amount = $db->func_escape_string($_GET['amount']);
$buyback_id = (int)$buyback_id;
$detail = $db->func_query_first("SELECT * FROM oc_buyback WHERE buyback_id='".$buyback_id."'");
$shipment_number = $db->func_query_first_cell("SELECT shipment_number FROM oc_buyback WHERE buyback_id='".$buyback_id."'");
$_products = $db->func_query("SELECT * FROM oc_buyback_products WHERE buyback_id='".$buyback_id."' and data_type IN ('customer','qc')");
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
$firstname = $db->func_escape_string(base64_decode($_GET['firstname']));
$lastname = $db->func_escape_string(base64_decode($_GET['lastname']));
$email = $db->func_escape_string(base64_decode($_GET['email']));
$myCode = 'LBB'.generateRandomString(6);
$code = $db->func_query_first_cell("SELECT code FROM oc_voucher WHERE code='".$myCode."'");
if($code)
{
	$CreditCode = $myCode.'-'.generateRandomString(2);
}
else
{
	$CreditCode = $myCode;
}
function generateRandomString($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}
$emailInfo['voucher_code'] = $CreditCode;
$emailInfo['customer_name'] = $firstname.' '.$lastname;
$emailInfo['total_amount'] = $amount;
$emailInfo['email'] = $email;
$adminInfo = array('name' => $_SESSION['login_as'], 'company_info' => $_SESSION['company_info'] );
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
if(isset($_POST['credit_code']))
{
	$i=0;
	$data = array();
	$data['buyback_id'] = $detail['buyback_id'];
	$data['payment_type'] = 'Store Credit';
	$data['amount'] = (float)$_POST['price'];
	$data['credit_code'] = $db->func_escape_string($_POST['credit_code']);
	$data['notes'] = 'Issue Credit';
	$data['date_added'] = date('Y-m-d H:i:s');
	$db->func_array2insert("inv_buyback_payments",$data);
	$data = array();
	$data['buyback_id'] = $detail['buyback_id'];
	$data['comment'] = $_SESSION['login_as'].' has generated a store credit '.$_POST['credit_code'].' of amount $'.number_format((float)$_POST['price'],2);
	$data['date_added'] = date('Y-m-d H:i:s');
	$data['user_id'] = $_SESSION['user_id'];
	$db->func_array2insert("inv_buyback_comments",$data);
	
	
	$data = array();
	$data['code'] = $_POST['credit_code'];
	$data['voucher_theme_id'] = 8;
	$data['message'] = '';
	$data['amount'] = $_POST['price'];
	$data['status'] = 1;
	//$data['order_id'] = $return_info[0]['order_id'];
	$data['date_added'] = date('Y-m-d h:i:s');
	$data['from_name'] = 'PhonePartsUSA.com';
	$data['from_email'] = 'sales@phonepartsusa.com';
	$data['to_name'] = $db->func_escape_string($firstname);
	$data['to_email'] = $db->func_escape_string($email);
	$voucher_id = 	$db->func_array2insert("oc_voucher",$data);

	$vouch_id = addVoucher('','store_credit',($_POST['price']),linkToVoucher($voucher_id,'', $data['code']));
    $db->db_exec("UPDATE inv_vouchers SET voucher_id='".(int)$voucher_id."',description='Credit Issued' where id='".$vouch_id."'");


    				$accounts = array();
					$accounts['description'] = $data['code'].' Issued against LBB';
					$accounts['credit'] = 0.00;
					$accounts['debit'] = $_POST['price'];
					$accounts['buyback_id'] = $detail['buyback_id'];
					$accounts['customer_email'] = $email;
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'store_credit';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // store credit issued


					$accounts = array();
					$accounts['description'] = $data['code'].' Issued against LBB';
					$accounts['debit'] = 0.00;
					$accounts['credit'] = $_POST['price'];
					$accounts['buyback_id'] = $detail['buyback_id'];
					$accounts['customer_email'] = $email;
					$accounts['type']='store_credit';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // store credit issued



	
	$vproduct = array();
		$vproduct['voucher_id'] = $voucher_id;
		$vproduct['lbb_number'] = $shipment_number;
		$vproduct['price'] = $_POST['price'];
		$vproduct['reason'] = 'LBB';
		$db->func_array2insert('`inv_voucher_products`', $vproduct);
	
	$log = linkToVoucher($voucher_id, $host_path, $data['code']) . ' of amount $' . number_format((float)$_POST['price'],2) . ' has been issued against LBB Shipment # ' . linkToLbbShipment($shipment_number);
	actionLog($log);
	
	$data = array();
	$data['order_id'] = 0;
	$data['voucher_id'] = $voucher_id;
	$data['description'] = '$'.number_format($_POST['price'],2)." Gift Certificate for ".$firstname;
	$data['code'] = $_POST['credit_code'];
	$data['from_name'] = 'PhonePartsUSA.com';
	$data['from_email'] = 'sales@phonepartsusa.com';
	$data['to_name'] = $firstname;
	$data['to_email'] = $email;
	$data['voucher_theme_id'] = 8;
	$data['message'] = '';
	$data['amount'] = $_POST['price'];
	$db->func_array2insert("oc_order_voucher",$data);
	$voucher_detail = array();
	$voucher_detail['order_id'] = $detail['shipment_number'];
	$voucher_detail['voucher_id'] = $voucher_id;
	$voucher_detail['is_lbb'] = 1;
	$voucher_detail['user_id'] = $_SESSION['user_id'];
	addVoucherDetail($voucher_detail);	
	$email = array();
	
	$email['title'] = $_POST['title'];
	$email['subject'] = $_POST['subject'];
	$email['message'] = $_POST['comment'];
	$email['number'] = array('title'=>'Credit Code','value'=>$_POST['credit_code']);
		//$email['order_id'] = $_POST['credit_code'];
	if($_POST['attach_pdf'])
	{
		$attach_pdf = '../files/'.$detail['file_pdf'];
	}
	else
	{
		$attach_pdf='';
	}
	sendEmailDetails($emailInfo, $email,array(),$attach_pdf);
	
	echo '<h1>Store Credit: '.$_POST['credit_code'].' has been generated</h1>';
	echo '<script> $("input[name=save]", window.parent.document).click();</script>';exit;
}
?>
<body>
	<div align="center">
		
		<div align="center"><br />
			<font color="red">In order to issue a Store Credit, please make sure to press "Save" button after making all of your changes.<br /></font>
		</div>
		
		<br clear="all" />
		<div align="center">
			<form action="" id="myFrm" method="post">
				<h2>Issue Credit</h2>
				<table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;width:90%">
					<tr>
						<td width="30%">Shipment #</td>
						<td width="70%"><?php echo $detail['shipment_number'];;?></td>
					</tr>
					
					<tr>
						<td>Amount</td>
						<td>
							<?php if ($_SESSION['login_as'] == 'admin') { ?>
							<input type="text" required name="price" value="<?php echo round($amount,2);?>" />
							<?php } else { ?>
							<input type="hidden" required name="price" value="<?php echo round($amount,2);?>" />
							$<?php echo round($amount,2);?>
							<?php } ?>
						</td>
					</tr>
					
					<tr>
						<td>Code</td>
						<td><input type="text" name="credit_code" id="credit_code" readonly="" value="<?php echo $CreditCode;?>" /></td>
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
<tr>
	<td colspan="2" align="center">
		<input type="button" name="add" value="Generate" onclick="submitForm()" />
	</td>
</tr>
</table>
</form>
</div>		
<script>
	function makeCode(obj)
	{
		if(obj.value=='')
		{
			$('#credit_code').val('');	 
			return false; 
		}
		var code = '';
		code = '<?php echo $detail['shipment_number'];?>'+obj.value;
				 //$('#credit_code').val(code);
				 checkForCode(code);
				}
				function checkForCode(code)
				{
					$.ajax({
						url: "issue_credit.php",
						type: "POST",
						data: {action:'getVoucherCode'},
						dataType: "json",
						beforeSend: function () {
							$('input[name=add]').prop('disabled', 'disabled');
						},
						complete: function () {
							$('input[name=add]').prop('disabled', '');
						},
						success: function (json) {
							if (json['error'])
							{
								alert(json['error']);
								return false;
							}
							if (json['success'])
							{
								$('#credit_code').val(json['success']);
							}
						}
					});
				}
				function submitForm()
				{
					if($('#credit_code').val()=='')
					{
						alert('Please provide the code');
						return false;	
					}
			/*	if($('#message').val()=='')
				{
					alert("Please write some message");
					return false;
					
				}*/
				
				$('#myFrm').submit();
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