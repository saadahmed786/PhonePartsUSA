<?php
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
require_once("auth.php");
require_once("inc/functions.php");
require_once('html2_pdf_lib/html2pdf.class.php');
$order_id = $db->func_escape_string($_GET['vendor_po_id']);
$invoice_check = $db->func_query_first("SELECT * FROM inv_order_docs WHERE vendor_po_id='$order_id' AND is_invoice=1");
$row = $db->func_query_first("SELECT * from inv_vendor_po where id='".(int)$order_id."'");

$shipment_data = $db->func_query_first("SELECT a.ex_rate, SUM(b.qty_received) as qty_received,sum(b.unit_price * b.qty_received) as unit_price,(select sum(c.shipping_cost) from inv_shipments c where c.vendor_po_id=a.vendor_po_id) as shipping_cost from inv_shipments a,inv_shipment_items b where a.id=b.shipment_id and a.vendor_po_id='".$row['vendor_po_id']."'");

$user_detail = $db->func_query_first("SELECT * FROM inv_users WHERE id='".$row['vendor']."'");

// if($row['store_type']=='po_business')
// {
// 	$is_po = true;	
// }
// else
// {
	$is_po = true;	
// }

// $po_detail = $db->func_query_first("SELECT * FROM inv_po_customers WHERE id='" . $row['po_business_id'] . "'");
	$po_detail = array();
$logo =  "https://phonepartsusa.com/image/" . oc_config("config_logo");
//$logo = "http://localhost/phone/image/".oc_config("config_logo");
$html = '
<style>
	.grey{
		color:#878D91;	
	}
	.dark-grey{
		color:#817D7D;	
	}
	.bold{
		font-weight:bold;	
	}
	.right{
		text-align:right;	
	}
	.normal{
		font-size:10px;
	}
	.detail{
		font-size:10px;
		color:#878D91;	
	}
	.nobreak {
		page-break-before: always;
	}
</style>';
// $vouchers = $db->func_query('SELECT *, `a`.`amount` as `used` FROM `oc_voucher_history` as a, `oc_voucher` as b WHERE a.`voucher_id` = b.`voucher_id` AND a.`' . (($row['store_type']=='web')? 'order_id': 'inv_order_id') . '` = "'. $order_id .'"');
// $total_vouchers = 0.00;
// $coupons = $db->func_query('SELECT *, `a`.`amount` as `used` FROM `oc_coupon_history` as a, `oc_coupon` as b WHERE a.`coupon_id` = b.`coupon_id` AND a.`order_id` = "'. $orderID .'"');
// $total_coupons = 0.00;
// Totaling
// foreach ($vouchers as $key => $voucher) {
// 	$total_vouchers += str_replace('-', '', $voucher['used']);
// }
// foreach ($coupons as $key => $coupon) {
// 	$total_coupons += str_replace('-', '', $coupon['used']);
// }
$items = $db->func_query("SELECT * FROM inv_vendor_po_items WHERE vendor_po_id='" . $row['vendor_po_id'] . "'");
// $sub_total = 0.00;
// foreach($items as $item)
// {
// 	$sub_total+=(float) $item['product_price'] - $item['promotion_discount'];	
// }
// $tax = round($db->func_query_first_cell('SELECT SUM(`value`) FROM `oc_order_total` WHERE `order_id` = "'. $order_id .'" AND `code` = "tax"'),2);
// if($tax<0) $tax = 0.00;


$header='<page><page_footer>
<table class="page_footer" align="right">
	<tr>
		<td align="right" style="width: 100%; text-align: right">
			Page [[page_cu]] of [[page_nb]]
		</td>
	</tr>
</table>
</page_footer><table border="0" >
<tr>
	<td style="width:560px">
		<img src="' . $logo . '">
	</td>';
	if($is_po)
	{
		$header.='<td  style="font-size:34px;" class="right" >
		' . ($row['status'] == 'estimate' ? 'ESTIMATE' : 'INVOICE') . '
	</td>';
}
else
{
	$header.='<td  style="font-size:34px;" class="right" >
	INVOICE
</td>';
}
$header.='
</tr>
<tr>
	<td style="font-weight:bold">PhonePartsUSA.com LLC</td>
	<td  class="dark-grey bold right" style="font-size:12px"># ' . $row['vendor_po_id'] . '</td>
</tr>
<tr>
	<td class="grey">5145 South Arville Street Suite A</td>
	<td class="bold right" ></td>
</tr>
<tr>
	<td class="grey">Las Vegas NV 89118</td>
	<td > </td>
</tr>
<tr>
	<td class="grey">U.S.A</td>';
	if($is_po)
	{
		$header.='<td class="bold right" style="font-size:10px;">' . ($row['payment_status'] != 'paid' ? 'Balance Due' : '') . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
	}
	else
	{
		$header.='<td class="bold right" style="font-size:10px;">Invoice Total&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
	}
	$header.='</tr>';
	if($is_po)
	{
		if ($row['payment_status'] != 'paid') {
			$header.='
			<tr>
				<td> </td>
				<td class="right bold" style="font-size:17px">$'. number_format(($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate'],2) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			</tr>';
		}
	}
	else
	{
		$header.='
		<tr>
			<td> </td>
			<td class="right bold" style="font-size:17px">$' . number_format($sub_total + $row['shipping_cost'] + $tax - ($total_coupons + $total_vouchers), 2) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		</tr>';
	}
	$header.='
	<tr>
		<td colspan="2">
			<table  border="0">
				<tr>
					<td valign="top" >
						<table border="0" >
							<tr>
								<td style="width:250px" class="grey" >Bill To</td>
							</tr>
							<tr>
								<td>' . $user_detail['name'] . '</td>
							</tr>
							<tr>
								<td>' . $user_detail['company_name'].'</td>
							</tr>
							<tr>
								<td>' . $user_detail['address1'].' '.$user_detail['address2']. '</td>
							</tr>
							<tr>
								<td>' . $user_detail['city'] . ', ' . $user_detail['providence'] . ' ' . $user_detail['postal_code'] . '</td>
							</tr>
							<tr>
								<td>' . $user_detail['country'] . '</td>
							</tr>
						</table>
					</td>
					<td valign="top" >
						<table border="0" >
							<tr>
								<td style="width:250px" class="grey" >Ship To</td>
							</tr>
							<tr>
								<td>' . $user_detail['name'] . '</td>
							</tr>
							<tr>
								<td>' . $user_detail['company_name'].'</td>
							</tr>
							<tr>
								<td>' . $user_detail['address1'].' '.$user_detail['address2']. '</td>
							</tr>
							<tr>
								<td>' . $user_detail['city'] . ', ' . $user_detail['providence'] . ' ' . $user_detail['postal_code'] . '</td>
							</tr>
							<tr>
								<td>' . $user_detail['country'] . '</td>
							</tr>
						</table>
					</td>
					<td  align="right">
						<table align="left" border="0" cellspacing="10" >
							<tr>
								<td class="right grey">Invoice Date :</td>
								<td class="right" >' . date('d M Y', strtotime($row['date_added'])) . '
								</td>
							</tr>';
							if($is_po)
							{
								$header.='<tr>
								<td class="right grey" >Terms :</td>
								<td class="right" >Net</td>
							</tr>';
							$header.='<tr>
							<td class="right grey" >Due Date :</td>
							<td class="right" >Due on Receipt</td>
						</tr>
						';
					}
					else
					{
							/*$html.='<tr>
							<td class="right grey" ></td>
							<td class="right" >' . $row['order_status']  . '</td>
						</tr>';*/
						if($row['payment_method']=='Cash or Credit at Store Pick-Up')
						{
							$row['payment_method']='Cash / Credit';
						}
						if($row['payment_method']=='Credit or Debit Card (Processed securely by PayPal)')
						{
							$row['payment_method']='Credit or Debit Card';
						}
						$header.='<tr>
						<td class="right grey" >Payment Method :</td>
						<td class="right" style="font-size:10px" >' . $row['payment_method']  . '</td>
					</tr>';
					$header.='<tr>
					<td class="right grey" >Channel :</td>
					<td class="right" style="font-size:10px" >' . ucfirst($row['store_type'])  . '</td>
				</tr>';
				if($row['shipping_method']=='Local Las Vegas Store Pickup - 9:30am-4:30pm')
				{
					$row['shipping_method']='Local Order';
				}
				if($row['other_shipping_name'])
					
				{
					$row['shipping_method']='Other Shipping - '.$row['other_shipping_name'];
				}
				$header.='<tr>
				<td colspan="2" class="right" style="font-size:10px;word-wrap: break-word;width:150px" ><strong>' . $row['shipping_method']  . '</strong></td>
			</tr>';
		}
		$header.='
	</table>
</td>
</tr>
</table>
</td>
</tr>
<tr>
	<td colspan="2"  >
		<table  cellpadding="0" cellspacing="1" border="0"     >
			<tr style="background-color:#3C3D3A;color:#fff;">
				<td style="width:25px;height:20px;padding:0px;text-align:center">#</td>
				<td style="width:450px;height:20px;padding:5px">Item Description</td>
				<td style="width:60px;text-align:right;padding:5px" >Req Qty</td>
				<td style="width:60px;text-align:right;padding:5px"  >Rcd Qty</td>
				<td  style="width:80px;text-align:right;padding:5px">Cost</td>
			</tr>';
			$item_html = '';
			$i_i = 1;
			// $zam_counter = 1;
			$amount_to_be_paid = 0.00;
			foreach ($items as $item) {
				$amount_to_be_paid+=$item['new_cost']*$item['qty_shipped'];
				if(count($items)<=15)
				{
					$_b = 14;	
				}
				else
				{
					$_b = 14;
				}
				if($i_i%$_b==0)
				{
					$item_html.='</table></td></tr></table></page>'.$header;	
				}
				$product_name = $item['name'];
				if(strlen($product_name)>82)
				{
					$product_name = substr($product_name,0,82).'...';	
				}
				$item_html.='	
				<tr>
				<td style="height:20px;padding:0px;text-align:center" class="normal">'.$i_i.'</td>
					<td style="height:20px;padding:5px" >
						<span class="normal">' . $item['sku'] . '</span><br />
						<span class="detail">' .$product_name . '</span>
					</td>
					<td class="right" style="height:20px;padding:5px">
						<span class="normal" >' . number_format($item['req_qty'], 2) . '</span><br />
						
					</td>
					<td  class="normal right" style="height:20px;padding:5px">' . number_format($item['qty_shipped'], 2) . '</td>
					<td  class="normal right" style="height:20px;padding:5px">' . number_format($item['new_cost'], 2) . '</td>
				</tr>';
				$i_i++;
			}
			$html=$html.$header.$item_html;
			$html.='
		</table>
		<table  cellspacing="10">
		<tr>
				<td class="bold" style="text-align:right;width:626px">Total</td>
				<td class="bold" style="text-align:right;width:100px">$' . number_format(($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate'],2) . '</td>
			</tr>
		';
			
			if($is_po)
			{
			// 	$html.='<tr>
			// 	<td class="right bold">Total</td>
			// 	<td class="right bold">$' . number_format($amount_to_be_paid, 2) . '</td>
			// </tr>';
			}
			else
			{
				$html.='<tr>
				<td  class="right">Tax / Extra</td>';
				$html.='<td class="right">$'.number_format($tax,2).'</td>';
				$html.='</tr>';
				$html.='<tr>
				<td  class="right bold">Total</td>';
				if ($row['payment_method'] == 'Cash' and strtolower($row['order_status'])=='processed')
				{
					$html.='<td class="right bold">$' . number_format(($sub_total + $row['shipping_cost'] + $tax) - ($total_vouchers + $total_coupons), 2) . '</td>';
				}
				else
				{
					$html.='<td class="right bold">$' . number_format(($sub_total + $row['shipping_cost'] + $tax) - ($total_vouchers + $total_coupons), 2) . '</td>';
				}
				$html.='</tr>';
			}

				


			if($is_po)
			{
				// if ($row['order_status'] != 'Estimate') {
					$html.='
					<tr>
						<td  class="right bold" style="height:30px;">Paid</td>
						<td class="right bold" style="">$' . number_format($amount_to_be_paid, 2) . '</td>
					</tr>';
					$html.='
					<tr>
						<td class="right bold" style="height:30px;">Balance Due</td>
						<td class="right bold" style="">$' . number_format((($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate']) - $amount_to_be_paid, 2) . '</td>
					</tr>';
				// }
			}
			else
			{
				if (($row['payment_method'] == 'Cash' or strtolower($row['payment_method']) == 'paypal' or strtolower($row['payment_method']) == 'credit/debit card' or strtolower($row['payment_method']) == 'paypal express' ) and strtolower($row['order_status'])=='processed') {
					$amount_due = 	($sub_total + $row['shipping_cost'] + $tax) - ($total_vouchers + $total_coupons) - $row['paid_price'];
					$html.='
					<tr>
						<td class="right bold" style="height:30px;">Amount Paid</td>
						<td class="right bold" style="">$' . number_format($row['paid_price'], 2) . '</td>
					</tr>';
					$html.='
					<tr >
						<td class="right bold" style="height:30px;">Amount Due</td>
						<td class="right bold" style="">$' . number_format($amount_due, 2) . '</td>
					</tr>';
				}
			}
			$html.='
		</table>
	</td>
</tr>
</table></page>
';
// echo $html;exit;
try {
	$html2pdf = new HTML2PDF('P', 'A4', 'en');
	$html2pdf->setDefaultFont('arial');
	$html2pdf->writeHTML($html);
	$filename =  $row['vendor_po_id'].'-'.time();
	$file = $html2pdf->Output('files/' . $filename . '.pdf', 'F');
	if (!$invoice_check) {
		$db->db_exec("INSERT INTO inv_order_docs set vendor_po_id='$order_id',is_invoice=1,attachment_path='files/" . $filename . ".pdf',date_added='" . date('Y-m-d H:i:s') . "'");
	} else {
		unlink($invoice_check['attachment_path']);
		$db->db_exec("UPDATE inv_order_docs set attachment_path='files/" . $filename . ".pdf',date_added='" . date('Y-m-d H:i:s') . "' WHERE order_id='$order_id' and is_invoice=1");
	}
//pdf creation
//now magic starts
// instantiate Imagick 
    /* $img_name = $filename.'.jpg';
      $im = new Imagick();
      $im->setResolution(500,500);
      $im->readimage(DIR_IMAGE.'returns/'.$filename.'.pdf');
      $im->setImageFormat('jpeg');
      $im->writeImage(DIR_IMAGE."returns/".$img_name);
      $im->clear();
      $im->destroy();
      //remove temp pdf
      //unlink('temp.pdf'); */
  } catch (HTML2PDF_exception $e) {
  	echo $e;
  	exit;
  }
  $attachment = $db->func_query_first_cell("SELECT attachment_path FROM inv_order_docs WHERE vendor_po_id='" . $order_id . "' AND is_invoice=1");
  if ($_GET['action'] == 'email') {
  	if($is_po)
  	{
  		$_body = 'Dear ' . $row['first_name'] . ',<br /><br />
  		Thanks for your business. <br />The invoice ' . $row['order_id'] . ' is attached with this email. You can choose the easy way out and pay online for this invoice. 
  		Here\'s an overview of the invoice for your reference.<br><br> 
  		Invoice Overview: <br />
  		Invoice # : ' . $row['order_id'] . '<br>
  		Date : ' . date('d M Y', strtotime($row['order_date'])) . ' <br>
  		Order Total : $' . number_format($sub_total + $row['shipping_cost'], 2) . ' <br>
  		' . ($row['order_status'] != 'Estimate' ? 'Amount Due : $' . number_format(($sub_total + $row['shipping_cost']) - $row['paid_price'], 2) . ' <br><br>' : '<br>') . '
  		It was great working with you. Looking forward to working with you again.<br><br><br>
  		Regards<br>
  		PhonePartsUSA.com LLC';
  	}
  	else
  	{
  		$_body = 'Dear ' . $row['first_name'] . ',<br /><br />
  		Thanks for your business. <br />The invoice ' . $row['order_id'] . ' is attached with this email. You can choose the easy way out and pay online for this invoice. 
  		Here\'s an overview of the invoice for your reference.<br><br> 
  		Invoice Overview: <br />
  		Invoice # : ' . $row['order_id'] . '<br>
  		Date : ' . date('d M Y', strtotime($row['order_date'])) . ' <br>
  		Order Total : $' . number_format($row['order_price'],2) . ' <br>
  		It was great working with you. Looking forward to working with you again.<br><br><br>
  		Regards<br>
  		PhonePartsUSA.com LLC';
  	}
  	if (isset($_POST['sendemail'])) {
  		$email = array();
  		$src = $path .'files/canned_' . $_POST['canned_id'] . ".png";
  		if (file_exists($src)) {
  			$email['image'] = $host_path .'files/canned_' . $_POST['canned_id'] . ".png?" . time();
  		}
  		$emailInfo = $_SESSION['email_info'][$row['order_id']];
  		$email['title'] = $_POST['title'];
  		$email['subject'] = $_POST['subject'];
  		$email['message'] = $_POST['comment'];
  		$emailInfo['total_formatted'] = $_POST['total_formatted'];
  		$sendm = sendEmailDetails($emailInfo, $email, array(), $attachment, 'Invoice # ' . $row['order_id']);
  		// echo "<script>window.location=viewOrderDetail.php?order='" . $row['order_id'] . "'</script>";
  		echo "<script>parent.location.reload(true);</script>";
		exit;
  		//header("Location:$host_path/viewOrderDetail.php?order=$orderID");
  		//exit;
  	} else {
  		$mail = new PHPMailer();
  		$mail->IsSMTP();
  		$mail->CharSet = 'UTF-8';
	    $mail->Host = "www.phonepartsusa.com"; // SMTP server example
	    $mail->SMTPDebug = 0;                     // enables SMTP debug information (for testing)
	    $mail->SMTPAuth = true;                  // enable SMTP authentication
	    $mail->Port = 25;                    // set the SMTP port for the GMAIL server
	    $mail->Username = "sales@phonepartsusa.com"; // SMTP account username example
	    $mail->Password = "pakistan";        // SMTP account password example
	    $mail->SetFrom('sales@phonepartsusa.com', 'PhonePartsUSA');
    	//$mail->AddAddress("xaman.riaz@gmail.com",'Zaman Riaz');
	    $mail->addAddress($row['email'], $row['first_name'] . ' ' . $row['last_name']);
	    $mail->Subject = ($row['order_status'] == 'Estimate' ? 'Estimate Number ' : 'Invoice Number ') . $row['order_id'] . ' - PhonePartsUSA';
	    $mail->Body = $_body;
	    $mail->IsHTML(true);
	    $mail->addAttachment($attachment, 'Invoice # ' . $row['order_id']);
	    $sendm = $mail->send();
	}
	if ($sendm) {
		$_SESSION['message'] = 'Email has been sent';
		$log = "Order #". linkToOrder($orderID) . " Invoice has sent to Customer";
		actionLog($log);
	} else {
		$_SESSION['message'] = 'Email not sent';
	}
}
// if ($_GET['action'] == 'view') {
	// $log = "Order #". linkToOrder($orderID) . " Invoice Downloaded";
	// actionLog($log);
	echo "<script>window.location='" . $attachment . "'</script>";
	exit;
// }
// header("Location: viewOrderDetail.php?order=" . $row['order_id']);
?>
