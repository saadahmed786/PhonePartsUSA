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
		$data['comment'] = $_SESSION['login_as'].' added a transaction id # '.$_POST['transaction_id'].' of amount $'.number_format((float)$_POST['price'],2);
		$data['date_added'] = date('Y-m-d H:i:s');


		$db->func_array2insert("inv_buyback_comments",$data);
		
	



	

	echo '<h1>Payment details have been saved!</h1>';

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
				<h2>Issue Payment</h2>
				<table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;width:90%">
					<tr>
						<td>Shipment #</td>
						<td><?php echo $detail['shipment_number'];;?></td>
					</tr>

					

					<tr>
						<td>Amount</td>
						<td><input type="text" required name="price" value="<?php echo round($detail['total'],2);?>" /></td>
					</tr>

					

					<tr>
						<td>Transaction ID</td>
						<td><input type="text" name="transaction_id" id="transaction_id" value="" required /></td>
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
			
				function submitForm()
				{
					if($('#transaction_id').val()=='')
					{
						alert('Please provide the transaction id');
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