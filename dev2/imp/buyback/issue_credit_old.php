<?php
include("../phpmailer/class.smtp.php");
include("../phpmailer/class.phpmailer.php");
require_once("../auth.php");
require_once("../inc/functions.php");

$buyback_id = $db->func_escape_string($_GET['buyback_id']);
$buyback_id = (int)$buyback_id;
$detail = $db->func_query_first("SELECT * FROM oc_buyback WHERE buyback_id='".$buyback_id."'");
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
	
	$emailInfo['order_id'] = $_POST['credit_code'];
$emailInfo['customer_name'] = $firstname.' '.$lastname;
$emailInfo['email'] = $email;

	

		$email = array();

		
		$email['title'] = 'Store Credit issued of $'.number_format($_POST['price'],2);
		$email['subject'] = 'A Store Credit has been generated - PhonePartsUSA';
		$email['message'] = $_POST['comment'];
		$email['number'] = array('title'=>'Credit Code','value'=>$_POST['credit_code']);
		//$email['order_id'] = $_POST['credit_code'];
		

		sendEmailDetails($emailInfo, $email);
	

	echo '<h1>Store Credit: '.$_POST['credit_code'].' has been generated</h1>';

	echo '<script> $("input[name=save]", window.parent.document).click();</script>';exit;
}

?>
<body>
	<div align="center">


		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>

		<br clear="all" />



		<div align="center">
			<form action="" id="myFrm" method="post">
				<h2>Issue Credit</h2>
				<table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;width:90%">
					<tr>
						<td>Shipment #</td>
						<td><?php echo $detail['shipment_number'];;?></td>
					</tr>

					

					<tr>
						<td>Amount</td>
						<td>$<?php echo number_format($detail['total'],2);?><input type="hidden" name="price" value="<?php echo $detail['total'];?>" /></td>
					</tr>

					

					<tr>
						<td>Code</td>
						<td><input type="text" name="credit_code" id="credit_code" value="<?php echo $CreditCode;?>" /></td>
					</tr>
					<tr>
						<td>Message:</td>
						<td>
							
							<textarea name="comment" id="comment" class="comment-box" cols="40" rows="8" style="width: 99%"><p></p><div id="customeData"></div></textarea>
						</td>
						<script>
							CKEDITOR.replace( 'comment' );
						</script>
					</tr>
					<tr style="display: none;">
						<td>
							<textarea id="disclaimer"><div contenteditable="false"><?= $db->func_query_first_cell('SELECT `signature` FROM `inv_signatures` WHERE `type` = 1'); ?></div></textarea>
							<?php $src = $path .'files/sign_' . $_SESSION['user_id'] . ".png"; ?>
							<textarea id="signature"><div contenteditable="false"><?= shortCodeReplace($adminInfo, $db->func_query_first_cell('SELECT `signature` FROM `inv_signatures` WHERE `user_id` = "'. $_SESSION['user_id'] .'" AND type = 0')); ?> <?= (file_exists($src))? '<img src="'. $host_path .'files/sign_' . $_SESSION['user_id'] . '.png?'. time() .'" />': ''; ?></div></textarea>
						</td>
						<script type="text/javascript">
							$(function() {
								$('.addsd').click(function() {
									var message = '';
									if ($('#signature_check').is(':checked')) {
										message = message + $('#signature').text();
									}
									if ($('#disclaimer_check').is(':checked')) {
										message = message + $('#disclaimer').text();
									}
									CKEDITOR.instances.comment.document.getById('customeData').setHtml(message);
								});
							});
						</script>
					</tr>
					<tr>
						<td></td>
						<td>
						<label class="addsd" for="signature_check"><input type="checkbox" id="signature_check" /> Add Signature</label><label class="addsd" for="disclaimer_check"><input type="checkbox" id="disclaimer_check" /> Add Disclaimer</label>
						
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
		</script> 
	</div>		     
</body>
</html>