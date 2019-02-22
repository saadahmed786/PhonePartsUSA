<?php
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
require_once("auth.php");
require_once("inc/functions.php");
if($_POST['action']=='getVoucherCode')
{
	$code = $db->func_query_first_cell("SELECT code FROM oc_voucher WHERE code='".$_POST['code']."'");
	if($code)
	{
		$code = $code.'-'.generateRandomString(2);
	}
	else
	{
		$code = $_POST['code'];
	}
	$json = array();
	$json['success'] = $code;
	echo json_encode($json);exit;
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
/*if($_SESSION['login_as'] != 'admin'){
	$_SESSION['message'] = 'You dont have permission to manage rma Credit.';
	exit;
}*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Store Credit</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
</head>
<?php
$rma_number = $_GET['rma_number'];
$emailx = $_GET['email'];
$items = rtrim($_GET['items'],",");
$return_info = $db->func_query("SELECT
	a.*,
	b.sku,b.title,b.item_condition,b.item_issue,b.`quantity`,b.price,b.`return_code`,b.`reason`,b.`decision`,b.return_id, b.id as return_item_id
	FROM
	`inv_returns` a
	INNER JOIN `inv_return_items`  b
	ON (a.`id` = b.`return_id`) 
	WHERE a.rma_number='".$rma_number."' AND b.id IN($items)");
$order_info = $db->func_query_first("SELECT a.*,b.* FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id AND  a.order_id='".$return_info[0]['order_id']."'");
$emailInfo = $_SESSION['rma_info' . $rma_number];
$adminInfo = array('name' => $_SESSION['login_as'], 'company_info' => $_SESSION['company_info'] );
$productPrice = 0;
$productNames = '<table><tbody>';
$productDetails = '<table width="100%">';
$productDetails .= '<thead><tr>';
$productDetails .= '<th width="35%">Name</th>';
$productDetails .= '<th width="10%">Return Reason</th>';
$productDetails .= '<th width="10%">Condition</th>';
$productDetails .= '<th width="10%">Decision</th>';
$productDetails .= '<th width="10%">Amount</th>';
$productDetails .= '<th width="35%">Images</th>';
$productDetails .= '</tr></thead><tbody>';
$voucher_items_reasons = '';
foreach ($return_info as $return_item) {
	$price = ($return_item['item_condition'] == 'Customer Damage' && $return_item['decision'] == 'Denied')? '0' : $return_item['price'];
	$productPrice += (float) $price;
	$productNames .= '<tr><td>'. $return_item['sku'] . ' - ' . $return_item['title'] .'</td></tr>';
	$productDetails .= '<tr>';
	$productDetails .= '<td>'. $return_item['sku'] . ' - ' . $return_item['title'] .'</td>';
	$productDetails .= '<td>'. $return_item['return_code'] . '</td>';
	$productDetails .= '<td>'. $return_item['item_condition'] . ' - ' . $return_item['item_issue'] .'</td>';
	$productDetails .= '<td>Issue Credit</td>';
	$productDetails .= '<td>'. $price .'</td>';
	$images = $db->func_query("select * from inv_return_item_images where return_item_id = '" . $return_item['id'] . "'");
	$productDetails .= '<td>';
	if ($images) {
		$productDetails .= '<table> <tr>';
		foreach ($images as $image) {
			$productDetails .= '<td><a href="' . $host_path . str_ireplace("../", "", $image['image_path']) . '">';
			$productDetails .= '<img src="' . $host_path . str_ireplace("../", "", $image['thumb_path']) . '" width="25" height="25" />';
			$productDetails .= '</a></td>';
		}
		$productDetails .= '</tr></table>';
	}
	$productDetails .= '</td></tr>';
	$voucher_items_reasons .= $return_item['sku'].'('.$return_item['item_condition'].'-'.$return_item['item_issue'].');';


}
//testObject($voucher_item_reasons);exit;
$productDetails .= '</tbody></table>';
$productNames .= '</tbody></table>';
$emailInfo['rma_products_names'] = $productNames;
$emailInfo['rma_products_Details'] = $productDetails;
$emailInfo['total_price'] = $productPrice;
if(isset($_POST['credit_code']))
{
	$data = array();
	$data['code'] = $_POST['credit_code'];
	$data['voucher_theme_id'] = 8;
	$data['message'] = $_POST['message'];
	$data['amount'] = $_POST['price'];
	$data['voucher_items_reasons'] = $voucher_items_reasons;
	//$data['reason_id'] = $_POST['voucher_reason'];
	$data['status'] = 1;
	$data['order_id'] = $return_info[0]['order_id'];
	$data['date_added'] = date('Y-m-d h:i:s');
	$data['from_name'] = 'PhonePartsUSA.com';
	$data['from_email'] = 'sales@phonepartsusa.com';
	$data['to_name'] = $db->func_escape_string($order_info['first_name']);
	$data['to_email'] = $db->func_escape_string($emailx);
	$voucher_id = 	$db->func_array2insert("oc_voucher",$data);

	$vouch_id = addVoucher('','store_credit',($_POST['price']),linkToVoucher($voucher_id,'', $data['code']));
       $db->db_exec("UPDATE inv_vouchers SET voucher_id='".(int)$voucher_id."',description='Credit Issued' where id='".$vouch_id."'");

       				$accounts = array();
					$accounts['description'] = $data['code'].' Issued against RMA';
					$accounts['credit'] = 0.00;
					$accounts['debit'] = $_POST['price'];
					$accounts['order_id'] = $data['order_id'];
					$accounts['customer_email'] = $data['to_email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'store_credit';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // store credit issued


					$accounts = array();
					$accounts['description'] = $data['code'].' Issued against RMA';
					$accounts['debit'] = 0.00;
					$accounts['credit'] = $_POST['price'];
					$accounts['order_id'] = $data['order_id'];
					$accounts['customer_email'] = $data['to_email'];
					$accounts['type']='store_credit';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // store credit issued

	$i=0;
	foreach(explode(",",$items) as $item)
	{
		$data = array();
		$data['return_id'] = $return_info[$i]['return_id'];
		$data['return_item_id'] = $return_info[$i]['return_item_id'];
		$data['order_id'] = $order_info['order_id'];
		$data['sku'] = $return_info[$i]['sku'];
		$data['price'] = $return_info[$i]['price'];
		$data['action'] = 'Issue Credit';
		$data['date_added'] = date('Y-m-d H:i:s');
		$addcomment = array();
		$addcomment['comment_date'] = date('Y-m-d H:i:s');
		$addcomment['user_id'] = $_SESSION['user_id'];
		$addcomment['comments'] = linkToVoucher($voucher_id, $host_path, $_POST['credit_code']) . ' of amount $'. $data['price'] . ' Credit Issued For ' . linkToProduct($data['sku'], $host_path, 'target="_blank"');
		$addcomment['return_id'] = $return_info[$i]['return_id'];
		$db->func_array2insert("inv_return_comments", $addcomment);
		$db->func_array2insert("inv_return_decision",$data);
		$data = array();
		$data['decision'] = 'Issue Credit';
		$db->func_array2update("inv_return_items",$data,'id="'.$item.'"');


		$i++;

	}
	$xskus = $db->func_query('SELECT sku FROM inv_return_items WHERE id in ('. $items .')');
	$logsku = '';
	foreach ($xskus as $val) {
		$logsku .= linkToProduct($val['sku']) . ', ';
	}
	$log = linkToVoucher($voucher_id, $host_path, $data['code']) . ' of amount $' . number_format((float)$_POST['price'],2) . ' has been issued against '. rtrim($logsku, ',') .' / RMA # ' . linkToRma($rma_number);
	actionLog($log);
	$data = array();
	$data['order_id'] = $return_info[0]['order_id'];
	$data['voucher_id'] = $voucher_id;
	$data['description'] = '$'.number_format($return_info[0]['price'],2)." Gift Certificate for ".$db->func_escape_string($order_info['first_name']);
	$data['code'] = $_POST['credit_code'];
	$data['from_name'] = 'PhonePartsUSA.com';
	$data['from_email'] = 'sales@phonepartsusa.com';
	$data['to_name'] = $order_info['first_name'];
	$data['to_email'] = $emailx;
	$data['voucher_theme_id'] = 8;
	$data['message'] = $_POST['message'];
	$data['amount'] = $_POST['price'];
	$db->func_array2insert("oc_order_voucher",$data);
	
	$item_detail = '';
	foreach ($return_info as $return_item) {
		$_sku = $return_item['sku'];
		$_title = $db->func_escape_string($return_item['title']);
		$_quantity = $return_item['quantity'];
		$_price = $return_item['price'];
		$_price = ($return_item['item_condition'] == 'Customer Damage' && $return_item['decision'] == 'Denied')? '0' : $return_item['price'];
		$item_detail.='SKU: '.$_sku.', Title:'.$_title.', Qty: '.$_quantity.', Price: '.$_price."<br>";

		$vproduct = array();
		$vproduct['voucher_id'] = $voucher_id;
		$vproduct['rma_number'] = $rma_number;
		$vproduct['sku'] = $_sku;
		$vproduct['price'] = $_price;
		$vproduct['reason'] = '('.$return_item['item_condition'].'-'.$return_item['item_issue'].')';
		$db->func_array2insert('`inv_voucher_products`', $vproduct);
		unset($vproduct);
	}
	$voucher_detail = array();
	$voucher_detail['voucher_id'] = $voucher_id;
	$voucher_detail['order_id'] = $data['order_id'];
	$voucher_detail['rma_number'] = $rma_number;
	$voucher_detail['detail'] = $item_detail;
	$voucher_detail['is_rma'] = 1;
	$voucher_detail['user_id'] = $_SESSION['user_id'];
	addVoucherDetail($voucher_detail);
	
	$emailInfo['voucher_code'] = $_POST['credit_code'];
	if ($_POST['canned_id']) {
		$email = array();
		$src = $path .'files/canned_' . $_POST['canned_id'] . ".png";
		if (file_exists($src)) {
			$email['image'] = $host_path .'files/canned_' . $_POST['canned_id'] . ".png?" . time();
		}
		$email['title'] = $_POST['title'];
		$email['subject'] = $_POST['subject'];
		$email['number'] = array('title' => 'Voucher No', 'value' => $emailInfo['voucher_code']);
		$email['message'] = shortCodeReplace($emailInfo, $_POST['comment']);
		sendEmailDetails($emailInfo, $email);
	} else {
		$_SESSION['message'] = 'Email not sent';
	}
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
						<td>Order ID</td>
						<td><?php echo $return_info[0]['order_id'];?></td>
					</tr>
					<tr>
						<td>Item(s)</td>
						<td>
							<?php
							$amount = 0.00;
							foreach($return_info as $item)
							{
								echo $item['sku'].' - '.$item['title']."<br />";
								$amount+=$item['price'];
							}
							?>
						</td>
					</tr>
					
                        <tr>
                            <td>Refund Shipping</td>
                            <td>
                                <input type="checkbox" onchange="updatePrice(this);" />
                            </td>
                        </tr>
					<tr>
						<td>Amount</td>
						<td>$<span id="amount_span"><?php echo number_format($amount, 2); ?></span><input type="hidden" name="price" value="<?php echo $amount;?>"</td>
					</tr>
					<tr style="display: none">
						<td>Reason</td>
						<td><select id="credit_reason" name="credit_reason" onchange="makeCode(this);">
							<option value="">Select Reason</option>
							<?php
							$credit_reasons = $db->func_query("SELECT * FROM oc_store_credit_reason WHERE status=1");
							foreach($credit_reasons as $reason)
							{
								?>
								<option value="<?php echo $reason['code'];?>"><?php echo $reason['name'];?></option>
								<?php 
							}
							?>
						</select></td>
					</tr>
					<tr>
						<td>Code</td>
						<td><input type="text" name="credit_code" id="credit_code" value="" /></td>
					</tr>
					 <!--<tr>
					
						<td>Voucher Reason</td>
						<td><select id="voucher_reason" name="voucher_reason" >
							<option value="">Select Voucher Reason</option>
							<?php
							//$voucher_reasons = $db->func_query("SELECT * FROM inv_voucher_reasons WHERE reason_type='RMA'");
							foreach($voucher_reasons as $reason)
							{
								?>
								<option value="<?php echo $reason['id'];?>"><?php echo $reason['reason'];?></option>
								<?php 
							}
							?>
						</select></td>
					</tr>--> 
					<tr>
						<td>Message:</td>
						<td>
							<?php $canned_message = $db->func_query_first('SELECT * FROM `inv_canned_message` WHERE `catagory` = "2" AND `type` = "Issue Store Credit"'); ?>
							<?php if ($canned_message) { ?>
							<input type="hidden" name="canned_id" value="<?= $canned_message['canned_message_id']; ?>">
							<input type="hidden" name="title" value="<?= shortCodeReplace($emailInfo, $canned_message['title']); ?>">
							<input type="hidden" name="subject" value="<?= shortCodeReplace($emailInfo, $canned_message['subject']); ?>">
							<textarea name="comment" id="comment" class="comment-box" cols="40" rows="8" style="width: 99%"><?= shortCodeReplace($emailInfo, $canned_message['message']); ?><div id="customeData"></div></textarea>
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
							<?php } else { echo 'Email Templete is not Defined'; } ?>
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
		$(document).ready(function(){
			makeCode('R');
		});
		 function updatePrice(obj)
                    {
                        var amount = 0.00
                        if (obj.checked)
                        {
                            amount =<?php echo $amount + $order_info['shipping_cost']; ?>;
                        }
                        else
                        {
                            amount =<?php echo $amount; ?>;
                        }
                        $('input[name=price]').val(amount);
                        $('#amount_span').html(amount.toFixed(2));
                    }
			function makeCode(obj)
			{
				if(obj=='')
				{
					$('#credit_code').val('');	 
					return false; 
				}
				var code = '';
				code = '<?php echo $rma_number;?>'+obj;
				 //$('#credit_code').val(code);
				 checkForCode(code);
				}
				function checkForCode(code)
				{
					$.ajax({
						url: "rma_credit.php",
						type: "POST",
						data: {code:code,action:'getVoucherCode'},
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
					/*if($('#voucher_reason').val()=='')
					{
						alert('Please Select the Voucher Reason');
						return false;	
					}*/
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