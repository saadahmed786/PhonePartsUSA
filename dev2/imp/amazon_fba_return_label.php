<?php
include_once 'auth.php';
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");

include_once 'inc/functions.php';
if(!$_SESSION['approve_return_shipp'] and !$_SESSION['approve_send_label'])
{
	echo 'Permission Denied';
}
$message = false;
$rma_number = $db->func_escape_string($_REQUEST['rma_number']);
$detail = $db->func_query_first("SELECT * FROM inv_returns WHERE rma_number='$rma_number'");
$detail['weight_lb'] = $detail['weight'] / 16;
if ($detail['weight'] == '0.0000') {
	$return_items = $db->func_query("select * from inv_return_items where return_id = '" . $detail['id'] . "' and removed = 0");
	foreach ($return_items as $item) {
		$detail['weight_lb'] += $db->func_query_first_cell("select weight from oc_product where model = '" . $item['sku'] . "'");
	}
	$detail['weight'] = $detail['weight_lb'] * 16;
}
if (!$detail['carrier_code']) {
	if ($detail['weight_lb'] <= 15) {
		$detail['carrier_code'] = 'express_1';
	} else {
		$detail['carrier_code'] = 'fedex';
	}
}
// testObject($detail);
if(!$detail)
{
	echo 'Error found! Please try again';
	exit;
}
/*
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
	
	$db->func_array2update('inv_returns', $data,"rma_number='".$detail['rma_number']."'");
	$detail = $db->func_query_first("SELECT * FROM inv_returns WHERE rma_number='$rma_number'");
	if($_SESSION['approve_send_label'] or $detail['approval_count']==2)
	{
		// Shipstation Create Label
		include "../shipstation/create_rma_label.php";
		if($_label_created==true)
		{
			$_SESSION['message'] = 'Shipping Label Created Successfully';
			
			$url = $host_path . 'freshdesk/ticket_buyback.php';

			$data = array(
				'email=' . $email,
				'name=' . $firstname.' '.$lastname,
				'subject=' . 'RMA Shipping Label has been created - Priority',
				'body=' . 'Tracking No: ' . $response_arr['trackingNumber'] . ' For RMA ' . $host_path . 'return_detail.php?rma_number=' . $rma_number,
				'method=' . 'g'
				);

			$data_string = implode('&', $data);

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);

			curl_setopt($ch,CURLOPT_POST, 1);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

			$output = curl_exec($ch);

			curl_close($ch);


			$_email = array();
			$data = array();
			$_email['title'] = 'RMA Label has been created!';
			$_email['number']['title'] = 'RMA Number #';
			$_email['number']['value'] = $detail['rma_number'];
			$_email['message'] = 'Dear '.$firstname.' '.$lastname.'!<br>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.<br><br>';
			$_email['subject'] = 'RMA Shipping Label has been created - PhonePartsUSA';
			$_email['image']= 'https://phonepartsusa.com/image/buyback_email.png';
			$data['email'] = $email;

		// $data['email'] = 'xaman.riaz@gmail.com';
			$data['customer_name'] = $firstname.' '.$lastname;

			sendEmailDetails ($_email, $data,array(),'../image/labels'.$filename);


		}
		else
		{
			$_SESSION['message']= 'Error: Shipping Label not Created! Please verify if your order has been synced into Shipstation or contact admin';

			$db->db_exec("UPDATE inv_returns SET approval_count = approval_count - 1 where rma_number='".$detail['rma_number']."'");
		}

	}
	else
	{
		$_SESSION['message'] = "Shipping Approved.";

	}

	
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
}
*/

$rma_return = $db->func_query_first("select r.* ,o.email, o.order_date, od.first_name,od.last_name,od.address1,od.address2,od.payment_method,
			od.city,od.state,od.zip,od.country,od.phone_number, o.store_type,o.order_status
			from inv_returns r 
			inner join inv_orders o on (r.order_id = o.order_id) 
			inner join inv_orders_details od on (r.order_id = od.order_id)
			where rma_number  = '$rma_number'");

$emailInfo = array(
	'customer_name' => $rma_return['first_name'] .' '. $rma_return['last_name'],
	'email' => $rma_return['email'],
	'rma_number' => $rma_number,
	'order_id' => $rma_return['order_id'],
	'shipping_firstname' => $rma_return['first_name'],
	'shipping_lastname' => $rma_return['last_name'],
	'shipping_address_1' => $rma_return['address1'],
	'shipping_address_2' => $rma_return['address2'],
	'shipping_city' => $rma_return['city'],
	'shipping_zone' => $rma_return['state'],
	'shipping_postcode' => $rma_return['zip'],
	'rma_status' => $rma_return['rma_status'],
	'order_date' => americanDate($rma_return['order_date']),
	'rma_recived' => americanDate($rma_return['date_completed']),
	'rma_qc' => americanDate($rma_return['date_qc'])
    // 'rma_products_names' => $productNames,
    // 'rma_products_Details' => $productDetails,
    // 'total_price' => $productPrice
	);



if (isset($_POST['update'])) {
	
	foreach ($return_items as $item) {

		$db->db_exec("INSERT INTO inv_return_history SET user_id='" . $_SESSION['user_id'] . "',return_status='Received',date_added='" . date('Y-m-d H:i:s', mktime(date("H"), date("i"), date("s"), date('m'), date('d') + 2, date('Y'))) . "',rma_number='" . $rma_number . "'");
		printLabel(0, $item['sku'], 0, 0, $rma_number, $_POST['printerid'], 'RC', 24);

	}

	$log = 'RMA Received ' . linkToRma($rma_number);
	actionLog($log);
	



	$templete = $db->func_query_first('SELECT * FROM inv_canned_message WHERE `catagory` = "2"  AND `type` = "Recived"');
	$email = array();
	if ($templete) {
		$src = $path .'files/canned_' . $templete['canned_message_id'] . ".png";
		if (file_exists($src)) {
			//print_r("Hello");
			//exit;
			$email['image'] = $host_path .'files/canned_' . $templete['canned_message_id'] . ".png?" . time();
		}

		$email['title'] = shortCodeReplace($emailInfo, $templete['title']);
		$email['subject'] = shortCodeReplace($emailInfo, $templete['subject']);
		$email['number'] = array('title' => 'RMA No #', 'value' => $emailInfo['rma_number']);
		$email['message'] = shortCodeReplace($emailInfo, $templete['message']);
	}

	sendEmailDetails($emailInfo, $email);
	//header("Location:$host_path/return_detail.php?rma_number=$rma_number");

	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
}






?>
<html>
<style>
	*{
		font-size:17px !important;
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