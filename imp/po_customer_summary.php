<?php

include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
require_once("auth.php");
require_once("inc/functions.php");
require_once('html2_pdf_lib/html2pdf.class.php');
function date_compare($a, $b)
{
    $t1 = strtotime($a['order_date']);
    $t2 = strtotime($b['order_date']);
    return $t1 - $t2;
}  

$id = $db->func_escape_string($_GET['customer_id']);
$invoice_check = $db->func_query_first("SELECT * FROM inv_po_customer_docs WHERE po_customer_id='$id' AND is_summary=1");
$rows = $db->func_query("SELECT a.*,b.* FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id AND a.po_business_id='" . $id . "' AND LOWER(order_status) IN ('shipped','unshipped') ORDER BY payment_detail_1 DESC , order_date DESC");

$po_detail = $db->func_query_first("SELECT * FROM inv_po_customers WHERE id='" . $id . "'");
$po_address = $db->func_query_first("SELECT * FROM inv_po_address WHERE po_customer_id='".$id."' ORDER BY address_id ASC LIMIT 1");
$logo = $host_path . "../image/" . oc_config("config_logo");
//$logo = "http://localhost/phone/image/".oc_config("config_logo");


$temp_row = array();
$k = 0;
foreach($rows as $row)
{
$temp_row[$k]['order_id'] = $row['order_id'];
$temp_row[$k]['shipping_cost'] = $row['shipping_cost'];
$temp_row[$k]['paid_price'] = $row['paid_price'];
$temp_row[$k]['order_date'] = $row['order_date'];
$temp_row[$k]['po_term'] = $row['po_term'];
$temp_row[$k]['shipping_date'] = $row['shipping_date'];
$temp_row[$k]['customer_po'] = $row['customer_po'];
$temp_row[$k]['reference_no'] = $row['reference_no'];
$temp_row[$k]['manual'] = 0;
$k++;
}
$applied_vouchers = $db->func_query("SELECT voucher_id, amount as paid_price,date_added as order_date from oc_voucher_history WHERE manual=1 and customer_email='".$po_detail['email']."'");


foreach($applied_vouchers as $row)
{
$temp_row[$k]['order_id'] = 0;
$temp_row[$k]['shipping_cost'] = 0.00;
$temp_row[$k]['paid_price'] = $row['paid_price']*(-1);
$temp_row[$k]['order_date'] = $row['order_date'];
$temp_row[$k]['po_term'] = 0;
$temp_row[$k]['shipping_date'] = '';
$temp_row[$k]['customer_po'] = '';
$temp_row[$k]['reference_no'] = 'Voucher # '.$db->func_query_first_cell("SELECT code FROM oc_voucher WHERE voucher_id='".$row['voucher_id']."'");
$temp_row[$k]['manual'] = 1;
$k++;
}

usort($temp_order, "date_compare");

$balance = 0.00;
foreach($temp_row as $row)
{
	$items = $db->func_query("SELECT * FROM inv_orders_items WHERE order_id='" . $row['order_id'] . "'");	
	$sub_total = 0.00;
	foreach($items as $item)
	{
		$sub_total+=(float) $item['product_price'];	
	}

	$charge = $sub_total + $row['shipping_cost'];


	$credit = $row['paid_price'];	
	$balance += $charge - $credit;
}

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
	.center{
		text-align:center;	
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
	.border-right{
		border-right:1px solid #878D91;
	}
	.border-left{
		border-left:1px solid #878D91;
	}

	.border-bottom{
		border-bottom:1px solid #878D91;
	}
	.red{
		color:red;	
	}


	.item_table {
		border-collapse: collapse;
	}
	.item_table td, .item_table th {
		border: 1px solid #878D91;
	}
	.item_table tr:first-child th {
		border-top: 0;
	}


	.item_table tr:last-child td {
		border-bottom: 0;
	}
	.item_table tr td:first-child,
	.item_table tr th:first-child {
		border-left: 0;
	}
	.item_table tr td:last-child,
	.item_table tr th:last-child {
		border-right: 0;
	}
	.no_border{
		border-bottom: none;	
	}
	.no_border1{
		border-left: none;
		border-right: none;
		border-top: none;	
		border-bottom: none;
	}
</style>';


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
	
	
	$header.='<td  style="font-size:30px;" class="right" >
	STATEMENT
</td>';

$header.='
</tr>
<tr>
	<td style="font-weight:bold">PhonePartsUSA.com LLC</td>
	<td  class="bold right" style="font-size:12px">Statement Date: '.date('m/d/Y').'</td>
</tr>
<tr>
	<td class="grey">5145 South Arville Street Suite A</td>
	<td class="bold right" style="font-size:12px" >Balance Due: <span class="'.($balance>0?'red':'').'">$'.number_format($balance,2).'</span></td>
</tr>
<tr>
	<td class="grey">Las Vegas NV 89118</td>
	<td > </td>
</tr>';



$header.='
<tr>
	<td colspan="2">
		<table  border="0">
			<tr>
				<td valign="bottom" >
					<table border="0" >
						<tr>
							<td style="width:530px" class="bold dark-grey" >Customer</td>
						</tr>
						<tr>
							<td>' . $po_detail['firstname'].' '.$po_detail['lastname'] . '</td>
						</tr>
						<tr>
							<td>' . $po_address['address'] . '</td>
						</tr>
						<tr>
							<td>' . $po_address['city'] . ', ' . $po_address['state'] . ' ' . $po_address['zip'] . '</td>
						</tr>


					</table>
				</td>
				
				<td  align="right">

				</td>
			</tr>
		</table>

	</td>
	
</tr>
<tr>
	<td colspan="2"  >

		<table  cellpadding="0" cellspacing="1" border="0" class="item_table"     >
			<tr style="background-color:#3C3D3A;color:#fff;">
				<td style="width:55px;height:20px;padding:5px;text-align:center" class="no_border1" >Inv Date</td>
				'.($_GET['due_date']?'<td style="width:60px;padding:5px;text-align:center" class="no_border1" >Due Date</td>':'').'
				<td style="width:70px;padding:5px;text-align:center" class="no_border1" >Order No.</td>
				<td style="width:85px;padding:5px;text-align:center" class="no_border1" >PO #</td>
				<td style="width:185px;padding:5px;text-align:center" class="no_border1" >Reference</td>
				<td style="width:70px;padding:5px;text-align:right" class="no_border1" >Charge</td>
				<td style="width:70px;padding:5px;text-align:right;" class="no_border1"  >Credit</td>

			</tr>';
			


			$item_html = '';
			$item_bulk = array();
			$payment_detail = '';
			$counter = 0;
			$i_i = 1;
			$kk = 1;
			foreach ($temp_row as $key => $row) {
				
				$_b = 23;


				if($i_i%$_b==0) {
					$kk++;
					$item_html.='</table></td></tr></table></page>'.$header;

				}

				$items = $db->func_query("SELECT * FROM inv_orders_items WHERE order_id='" . $row['order_id'] . "'");	
				$sub_total = 0.00;
				foreach($items as $item) {
					$sub_total+=(float) $item['product_price'];	
				}

				$charge = $sub_total + $row['shipping_cost'];


				$credit = $row['paid_price'];	

				$bottom_order = false;
				// if(($i_i==($_b-1) or $i_i==(($_b*2)-1) or $i_i==count($temp_row)) ) {
				// 	$bottom_order = true;

				// }
				
				if(($i_i==(($_b*$kk)-1) or $i_i==count($temp_row)) ) {
					$bottom_order = true;

				}
				
				$item_html.='	
				<tr>
					<td class="'.($bottom_order?'':'no_border').' center normal" style="height:10px;padding:5px" >
						'.date('m/d/Y',strtotime($row['order_date'])).'
					</td>
					'.($_GET['due_date']?'<td class="'.($bottom_order?'':'no_border').' center normal " style="height:10px;padding:5px">
						' . ($row['po_term']==0?'':date('m/d/Y', strtotime($row['shipping_date'] . ' + ' . (int) $row['po_term'] . ' days'))) . '
					</td>':'').'
					<td  class="'.($bottom_order?'':'no_border').' center normal " style="height:10px;padding:5px">'.($row['order_id']?$row['order_id']:'').'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal " style="height:10px;padding:5px">'.$row['customer_po'].'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal " style="height:10px;padding:5px">'.$row['reference_no'].'</td>
					<td  class="'.($bottom_order?'':'no_border').' normal right " style="height:10px;padding:5px">$'.number_format($charge,2).'</td>
					<td  class="'.($bottom_order?'':'no_border').' normal right " style="height:10px;padding:5px">'.($credit>0.00?'$'.number_format($credit,2).'-':'').'</td>

				</tr>';
				$i_i++;
			}
			$html=$html.$header.$item_html;
			$html.='
		</table>


	</td>

</tr>

</table></page>

';
//die($html);
try {



	$html2pdf = new HTML2PDF('P', 'A4', 'en');

	$html2pdf->setDefaultFont('arial');
	$html2pdf->writeHTML($html);

	$filename = 'Summary-'.$po_detail['email'].'-'.time();
	$file = $html2pdf->Output('files/' . $filename . '.pdf', 'F');


	if (!$invoice_check) {

		$db->db_exec("INSERT INTO inv_po_customer_docs set po_customer_id='$id',is_summary=1,attachment_path='files/" . $filename . ".pdf',date_added='" . date('Y-m-d H:i:s') . "',`type`='application/pdf'");
	} else {
		unlink($invoice_check['attachment_path']);
		$db->db_exec("UPDATE inv_po_customer_docs set po_customer_id='$id',is_summary=1,attachment_path='files/" . $filename . ".pdf',date_added='" . date('Y-m-d H:i:s') . "',`type`='application/pdf' WHERE po_customer_id='$id' and is_summary=1");
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

  $attachment = $db->func_query_first_cell("SELECT attachment_path FROM inv_po_customer_docs WHERE po_customer_id='" . $id . "' AND is_summary=1");
/*if ($_GET['action'] == 'email') {
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


    if ($mail->send()) {

        $_SESSION['message'] = 'Email has been sent';
    } else {
        $_SESSION['message'] = 'Email not sent';
    }
}*/
if ($_GET['action'] == 'view') {
	echo "<script>window.location='" . $attachment . "'</script>";
	exit;
}
header("Location: po_business_create.php?id=$id&mode=edit");
?>

