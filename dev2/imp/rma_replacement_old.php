<?php
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
require_once("auth.php");
require_once("inc/functions.php");


function refOrderId($order_id,$n=1)
{
	global $db;
		//When i made a replacement of this replacement order the order id was 318940-1. This is wrong, the replacement order id should have been: 318657-2.
	$_check = $db->func_query_first("SELECT order_id,ref_order_id FROM oc_order WHERE order_id='".$order_id."'");
	if($_check['ref_order_id'])
	{
		$order_id = explode("-",$_check['ref_order_id']);
		$order_id = $order_id[0];
	}

	$ref_order_id = $order_id.'-'.$n;
	$check = $db->func_query_first("SELECT ref_order_id FROM oc_order WHERE ref_order_id='".$ref_order_id."'");	
	$row = $check;
	
	
	
	
	if($row)
	{
		$n = $n+1;
		return refOrderId($order_id,$n);
		
	}
	else
	{
		return $ref_order_id;
	}

}

/*if($_SESSION['login_as'] != 'admin'){
	$_SESSION['message'] = 'You dont have permission to manage rma replacements.';
	exit;
}*/
$rma_number = $_GET['rma_number'];
$items = rtrim($_GET['items'],",");


$return_info = $db->func_query("SELECT
	a.*,
	b.sku,b.title,b.`quantity`,b.price,b.`return_code`,b.`reason`,b.`decision`,b.return_id
	FROM
	`inv_returns` a
	INNER JOIN `inv_return_items`  b
	ON (a.`id` = b.`return_id`) 

	WHERE a.rma_number='".$rma_number."' AND b.id IN($items)");
$order_info = $db->func_query_first("SELECT * FROM oc_order WHERE order_id='".(int)$return_info[0]['order_id']."'");


$emailInfo = $_SESSION['rma_info'];
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
foreach ($return_info as $return_item) {
	$price = ($return_item['item_condition'] == 'Customer Damage' && $return_item['decision'] == 'Denied')? '0' : $return_item['price'];
	$productPrice += (float) $price;
	$productNames .= '<tr><td>'. $return_item['sku'] . ' - ' . $return_item['title'] .'</td></tr>';
	$productDetails .= '<tr>';
	$productDetails .= '<td>'. $return_item['sku'] . ' - ' . $return_item['title'] .'</td>';
	$productDetails .= '<td>'. $return_item['return_code'] . '</td>';
	$productDetails .= '<td>'. $return_item['item_condition'] . ' - ' . $return_item['item_issue'] .'</td>';
	$productDetails .= '<td>Issue Replacement</td>';
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

}
$productDetails .= '</tbody></table>';
$productNames .= '</tbody></table>';

$emailInfo['rma_products_names'] = $productNames;
$emailInfo['rma_products_Details'] = $productDetails;
$emailInfo['total_price'] = $productPrice;


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Issue Replacement</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
</head>
<?php
if(isset($_POST['claim']))
{

	$ref_order_id = refOrderId($order_info['order_id']);
	$xx=0;
	foreach(explode(",",$items) as $item)
	{



		$data = array();
		$data['return_id'] = $return_info[$xx]['return_id'];
		$data['order_id'] = $order_info['order_id'];
		$data['sku'] = $return_info[$xx]['sku'];
		$data['price'] = $return_info[$xx]['price'];
		$data['action'] = 'Issue Replacement';
		$data['date_added'] = date('Y-m-d H:i:s');

		$db->func_array2insert("inv_return_decision",$data);

		$data = array();

		$data['decision'] = 'Issue Replacement';


		$db->func_array2update("inv_return_items",$data,'id="'.$item.'"');

		$xx++;
	}



	$order_info['payment_method'] = 'Replacement';
	$order_info['payment_code'] = 'cod';
	$data = array();	
	$data = $order_info;
	$data['order_product'] = array();
	$price = 0;
	$k=0;
	foreach(explode(",",$items) as $item)
	{
	//	$product_infos = $db->func_query("SELECT * FROM oc_order_product WHERE model='".$return_info[$k]['sku']."' AND order_id='".$return_info[$k]['order_id']."'");
		$product_infos = $db->func_query("SELECT a.*,b.name FROM oc_product a,oc_product_description b WHERE a.product_id=b.product_id AND a.model='".$return_info[$k]['sku']."'");
		
		foreach($product_infos as $product_info)
		{
			$_price = $db->func_query_first_cell("SELECT product_price/product_qty FROM inv_orders_items WHERE order_id='".$order_info['order_id']."' AND product_sku='".$product_info['model']."'");
			if(!$_price)
			{
				$_price = $product_info['price'];	
			}
			$_price = round($_price,2);
			$data['order_product'][] = array(

				'product_id'	=>$product_info['product_id'],
				'name'		=>	$product_info['name'],
				'model'		=>	$product_info['model'],
				'quantity'	=>	1,
				'price'		=>	$_price,
				'total'		=>	$_price,
				'tax'		=>	0.0000

				);
		}
		$k++;
	}

	$query = $db->func_query("SELECT * FROM oc_order_total WHERE order_id='".$return_info[0]['order_id']."'");
	foreach($query as $row)
	{
		if($row['code']=='total' or $row['code']=='sub_total')
		{
			$value = 	$price;
		}
		else
		{

			$value = 0;	
		}

		$data['order_total'][] = array(
			'code'=>$row['code'],
			'title'=>$row['title'],
			'text'=>'$0.00',
			'value'=>0.00,
			'sort_order'=>$row['sort_order']
			)	;


	}
	$data['admin_view_only']=1;
		$data['order_status_id'] = 21 ; //default on hold status

		
		$array = array();
		$array['invoice_prefix'] = $data['invoice_prefix'];
		$array['ref_order_id'] = $ref_order_id;
		$array['store_id'] = $data['store_id'];
		$array['store_name'] = $data['store_name'];
		$array['store_url'] = $data['store_url'];
		$array['customer_id'] = $data['customer_id'];
		$array['customer_group_id'] = $data['customer_group_id'];
		$array['firstname'] = $data['firstname'];
		$array['lastname'] = $data['lastname'];
		$array['email'] = $data['email'];
		$array['telephone'] = $data['telephone'];
		$array['fax'] = $data['fax'];
		$array['payment_firstname'] = $data['payment_firstname'];
		$array['payment_lastname'] = $data['payment_lastname'];
		$array['payment_company'] = $data['payment_company'];
		$array['payment_company_id'] = $data['payment_company_id'];
		$array['payment_tax_id'] = $data['payment_tax_id'];
		$array['payment_address_1'] = $data['payment_address_1'];
		$array['payment_address_2'] = $data['payment_address_2'];
		$array['payment_city'] = $data['payment_city'];
		$array['payment_postcode'] = $data['payment_postcode'];
		$array['payment_country'] = $data['payment_country'];
		$array['payment_country_id'] = $data['payment_country_id'];
		$array['payment_zone'] = $data['payment_zone'];
		$array['payment_zone_id'] = $data['payment_zone_id'];
		$array['payment_address_format'] = $data['payment_address_format'];
		$array['payment_method'] = $data['payment_method'];
		$array['payment_code'] = $data['payment_code'];
		$array['shipping_firstname'] = $data['shipping_firstname'];
		$array['shipping_lastname'] = $data['shipping_lastname'];
		$array['shipping_company'] = $data['shipping_company'];
		$array['shipping_address_1'] = $data['shipping_address_1'];
		$array['shipping_address_2'] = $data['shipping_address_2'];
		$array['shipping_city'] = $data['shipping_city'];
		$array['shipping_postcode'] = $data['shipping_postcode'];
		$array['shipping_country'] = $data['shipping_country'];
		$array['shipping_country_id'] = $data['shipping_country_id'];
		$array['shipping_zone'] = $data['shipping_zone'];
		$array['shipping_zone_id'] = $data['shipping_zone_id'];
		$array['shipping_address_format'] = $data['shipping_address_format'];
		$array['shipping_method'] = $_POST['shipping_method'];
		$array['shipping_code'] = $_POST['shipping_code'];
		$array['comment'] = $_POST['message'];
		$array['order_status_id'] = $data['order_status_id'];
		$array['affiliate_id'] = $data['affiliate_id'];
		$array['language_id'] = $data['language_id'];
		$array['currency_id'] = $data['currency_id'];
		$array['currency_code'] = $data['currency_code'];
		$array['currency_value'] = $data['currency_value'];
		$array['admin_view_only'] = $data['admin_view_only'];
		$array['date_added'] = date('Y-m-d H:i:s');
		$array['date_modified'] = date('Y-m-d H:i:s');
		
		$xorder_id = $db->func_array2insert("oc_order",$array);

		$xskus = $db->func_query('SELECT sku FROM inv_return_items WHERE id in ('. $items .')');
		$logsku = '';
		foreach ($xskus as $val) {
			$logsku .= linkToProduct($val['sku']) . ', ';
		}
		
		$log = 'Order No# ' . linkToOrder($xorder_id) . ' has been issued for '. linkToRma($rma_number) . ' against '. rtrim($logsku, ',');

		actionLog($log);
		
		if (isset($data['order_product'])) {		
			foreach ($data['order_product'] as $order_product) {	
				$db->db_exec("INSERT INTO oc_order_product SET order_id = '" . (int)$xorder_id . "', product_id = '" . (int)$order_product['product_id'] . "', name = '" . $order_product['name'] . "', model = '" . ($order_product['model']) . "', quantity = '" . (int)$order_product['quantity'] . "', price = '" . (float)$order_product['price'] . "', total = '" . (float)$order_product['total'] . "', tax = '" . (float)$order_product['tax'] . "', reward = '" . (int)$order_product['reward'] . "'");

			}
		}
		
		
		if (isset($data['order_total'])) {		
			foreach ($data['order_total'] as $order_total) {	
				$db->db_exec("INSERT INTO oc_order_total SET order_id = '" . (int)$xorder_id . "', code = '" . ($order_total['code']) . "', title = '" . ($order_total['title']) . "', text = '" . ($order_total['text']) . "', `value` = '" . (float)$order_total['value'] . "', sort_order = '" . (int)$order_total['sort_order'] . "'");
			}
			
			$total += $order_total['value'];
		}
		
		$array = array();
		$array['order_id'] = $return_info[0]['order_id'];
		$array['resolution'] = 'replacement';
		$array['date_added'] = date('Y-m-d H:i:s');;
		$array['user_id'] = 0;
		
		
		$xreturn_id = $db->func_array2insert("oc_return_program_mt",$array);
		
		foreach($data['order_product'] as  $product_info)
		{



			$sql="INSERT INTO oc_return_program_dt SET reason_id='".$_POST['claim']."', return_id='".(int)$xreturn_id."',product_id='".(int)$product_info['product_id']."',amount='".(float)$product['price']."'";

			$db->db_exec($sql);
		}


	//$productsx = '<ol style="text-align:left;background-color:#DCDCDC;font-weight:bold;line-height:1.5em">';
		$emailInfo['replacement_order_id'] = $ref_order_id;
		if ($_POST['canned_id']) {

			$email = array();

			$src = $path .'files/canned_' . $_POST['canned_id'] . ".png";

			if (file_exists($src)) {
				$email['image'] = $host_path .'files/canned_' . $_POST['canned_id'] . ".png?" . time();
			}

			$email['title'] = $_POST['title'];
			$email['subject'] = $_POST['subject'];
			$email['number'] = array('title' => 'Replacement Order ID', 'value' => $emailInfo['replacement_order_id']);
			$email['message'] = shortCodeReplace($emailInfo, $_POST['comment']);

			sendEmailDetails($emailInfo, $email);

		} else {
			$_SESSION['message'] = 'Email not sent';
		}



		if($xreturn_id)
		{
			echo '<h1>Replacement Order # '.$ref_order_id.' is made</h1>';
			echo '<script> $("input[name=save]", window.parent.document).click();</script>';exit;

		}

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
					<h2>Issue Replacement</h2>
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
							<td>Amount</td>
							<td>$<?php echo number_format($amount,2);?><input type="hidden" name="price" value="<?php echo $amount;?>"</td>
						</tr>

						<tr>
							<td>Shipping Method</td>
							<td><select name="shipping" style="width:400px">
								<option value="">Loading Shipping Methods...</option>
							</select>

							<input type="hidden" name="shipping_method" /><input type="hidden" name="shipping_code" />
						</td>

					</tr>

					<tr>
						<td>Claim</td>
						<td><select id="claim" name="claim" >
							<option value="">Select Claim</option>
							<?php
							$credit_reasons = $db->func_query("SELECT * FROM oc_return_reason WHERE language_id=1");
							foreach($credit_reasons as $reason)
							{
								?>
								<option value="<?php echo $reason['return_reason_id'];?>"><?php echo $reason['name'];?></option>
								<?php 

							}
							?>

						</select></td>
					</tr>

					<tr>
						<td>Message:</td>
						<td>
							<?php $canned_message = $db->func_query_first('SELECT * FROM `inv_canned_message` WHERE `catagory` = "2" AND `type` = "Issue Replacement"'); ?>
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
			function makeCode(obj)
			{
				if(obj.value=='')
				{
					$('#claim').val('');	 
					return false; 
				}

				var code = '';

				code = '<?php echo $return_info['order_id'];?>'+obj.value;
				$('#credit_code').val(code);
			}
			function submitForm()
			{
				if($('#claim').val()=='')
				{
					alert('Please select the Claim');
					return false;	
				}
				/*if($('#message').val()=='')
				{
					alert("Please write some message");
					return false;
					
				}*/
				
				$('#myFrm').submit();

			}
			$(document).ready(function(e) {
				$('#shipping_method_div').html('Loading...');
				
				$.ajax({
					url: "<?php echo $local_path;?>../../index.php?route=checkout/manual/shipping_method&order_id=<?php echo $return_info[0]['order_id'];?>",

					type:"POST",

					dataType:"json",
					success: function(json) {
						if (json['error']) {
							alert('error');
							return false;
						}
						if (json['shipping_method']) {
				//html = '<option value=""><?php echo $text_select; ?></option>';
				html='';
				for (i in json['shipping_method']) {
					html += '<optgroup label="' + json['shipping_method'][i]['title'] + '">';

					if (!json['shipping_method'][i]['error']) {
						for (j in json['shipping_method'][i]['quote']) {
							if (json['shipping_method'][i]['quote'][j]['code'] == $('input[name=\'shipping_code\']').attr('value')) {
								html += '<option value="' + json['shipping_method'][i]['quote'][j]['code'] + '" selected="selected">' + json['shipping_method'][i]['quote'][j]['title'] + '</option>';
							} else {
								html += '<option value="' + json['shipping_method'][i]['quote'][j]['code'] + '">' + json['shipping_method'][i]['quote'][j]['title'] + '</option>';
							}
						}		
					} else {
						html += '<option value="" style="color: #F00;" disabled="disabled">' + json['shipping_method'][i]['error'] + '</option>';
					}
					
					html += '</optgroup>';
				}

				$('select[name=\'shipping\']').html(html);	
				
				if ($('select[name=\'shipping\'] option:selected').attr('value')) {
					$('input[name=\'shipping_method\']').attr('value', $('select[name=\'shipping\'] option:selected').text());
				} else {
					$('input[name=\'shipping_method\']').attr('value', '');
				}
				
				$('input[name=\'shipping_code\']').attr('value', $('select[name=\'shipping\'] option:selected').attr('value'));	
			}
		}
	});
});
$('select[name=\'shipping\']').bind('change', function() {
	if (this.value) {
		$('input[name=\'shipping_method\']').attr('value', $('select[name=\'shipping\'] option:selected').text());
	} else {
		$('input[name=\'shipping_method\']').attr('value', '');
	}
	
	$('input[name=\'shipping_code\']').attr('value', this.value);
});
</script> 


</div>		     
</body>
</html>