<?php

include("phpmailer/class.smtp.php");

include("phpmailer/class.phpmailer.php");

require_once("auth.php");

require_once("inc/functions.php");

require_once('html2_pdf_lib/html2pdf.class.php');

$shipment_id = (int)$_GET['shipment_id'];

$row = $db->func_query_first("SELECT * FROM inv_shipments WHERE id='$shipment_id' ");

$items = $db->func_query("SELECT * FROM inv_shipment_items WHERE shipment_id='$shipment_id' order by product_sku");

$vendor = $db->func_query_first("SELECT * FROM inv_users WHERE id='".$row['vendor']."' ");

$buyer = $db->func_query_first("SELECT * FROM inv_users WHERE id='".(int)$row['received_by']."'");
//testObject($vendor);exit;
$row['shipping_cost'] = (float)$row['shipping_cost'] / (float)$row['ex_rate'];
$sub_total = 0.00;

foreach($items as $item)

{

	$sub_total=$sub_total + (((float) $item['qty_received'] * (float)$item['unit_price'])/$row['ex_rate']);	

}

$logo = "http://phonepartsusa.com/image/" . oc_config("config_logo");

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

	.left{

		text-align:left;	

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

	<td>



	<table border = "1" cellspacing="20">

	<tr>

	<td valign="top" align="left">



		<img src="' . $logo . '" style="margin-top:-5px">

		<br><br><br><br><br><br>
		<div ><font style="font-weight:bold;font-size:12px;">Supplier :</font><br>

			<font style="font-size:12px;">' . $vendor['company_name'] . '</font><br>

			<font style="font-size:12px;">' . $vendor['address_1'] . '</font><br>

			<font style="font-size:12px;">' . $vendor['city'] . ', '. $vendor['providence'] .' '. $vendor['postal_code'] .'</font><br>

			<font style="font-size:12px;">' . $vendor['country'] . '</font><br>

			<font style="font-size:12px;">(P) ' . $vendor['phone_no'] . '</font><br>

			<font style="font-weight:bold;font-size:12px;">Supplier Contact :</font><br>

			<font style="font-size:12px;font-weight:normal;">' . $vendor['name'] . '</font><br>

			<font style="font-size:12px;font-weight:normal;">' . $vendor['email'] . '</font><br>

			<font style="font-size:12px;font-weight:normal;">(P) ' . $vendor['phone_no'] . '</font><br>

		</div>

	</td>

	<td valign="top"  align="left">



	<font style="font-weight:bold;font-size:20px;margin-top:-2px;">PhonePartsUSA.com</font><br>

	<font style="font-size:12px;">5145 South Arville Street, Suite A</font><br>

	<font style="font-size:12px;">Las Vegas, Nevada 89118</font><br>

	<font style="font-size:12px;">United States</font><br>

	<font style="font-size:12px;">http:/www.phonepartsusa.com</font><br>

	<font style="font-size:12px;">(P) 855-213-5588</font>

	<br><br>

	<div ><font style="font-weight:bold;font-size:12px;">Ship To:</font><br>

			<font style="font-size:12px;">PhonePartsUSA.com</font><br>

			<font style="font-size:12px;">5145 South Arville Street, Suite A</font><br>

			<font style="font-size:12px;">Las Vegas, Nevada 89118</font><br>

			<font style="font-size:12px;">United States</font><br>

			<font style="font-size:12px;">http:/www.phonepartsusa.com</font><br>

			<font style="font-size:12px;">(P) 855-213-5588</font>

		</div>

	</td>

	</tr>

	</table>

		

	</td>';





	



	$header.='<td class="left" valign="top" style="padding-top:20px;">

	<table valign="top" cellspacing="0" cellpadding="0"  >

	<tr><td align="left" padding:5px; style="color:#ffffff;background-color:#878D91;border-left: #878D91;border-left-width: 10px;" colspan="2"><font style="font-weight:bold;font-size:18px;">Purchase Order<br> (Submitted)</font></td></tr>

	<tr><td align="left" style="border:0.5px solid #878D91;padding:5px;border-left: #878D91;border-left-width: 10px;" colspan="2"><font style="font-weight:bold;font-size:12px;">Date</font><br><font style="font-size:12px;">' . americanDate($row['date_added']) . ' PST</font></td></tr>

	<tr>

	<td align="left" style=" border:0.5px solid #878D91;padding:5px;border-left: #878D91;border-left-width: 10px;"><font style="font-weight:bold;font-size:12px;">Modified Date</font><br><font style="font-size:12px;">' . americanDate($row['date_completed']) . ' PST</font></td>

	<td align="left" style="border:0.5px solid #878D91;padding:5px;"><font style="font-weight:bold;font-size:10px;">Follow Up<br></font><font style="font-size:12px;">N/A</font></td>

	</tr>

	<tr><td style="border:0.5px solid #878D91;padding:5px;border-left: #878D91;border-left-width: 10px;" align="left" colspan="2"><font style="font-weight:bold;font-size:12px;">PO#</font><br><font style="font-size:12px;">' . $row['package_number'] . '</font></td></tr>

	

	

	</table>

</td>

</tr>

';

	

					

		$header.='

<tr>

	<td colspan="2"  >

	<br><br><br>

		<table  cellpadding="0" cellspacing="0" border="0"     >

			<tr style="background-color:#878D91;color:#fff;">

				<td style="width:10px;height:7px;padding:2px;font-size:10px;font-weight: bold;">#</td>

				<td style="width:445px;height:7px;padding:2px;font-size:10px;font-weight: bold;">Description</td>

				<td style="width:65px;text-align:right;padding:2px;font-size:10px;font-weight: bold;" >Part #</td>

				<td style="width:50px;text-align:right;padding:2px;font-size:10px;font-weight: bold;" >Qty</td>

				<td style="width:60px;text-align:right;padding:2px;font-size:10px;font-weight: bold;"  >Unit Price</td>

				<td  style="width:70px;text-align:right;padding:2px;font-size:10px;font-weight: bold;">Total</td>

			</tr>';

			$item_html = '';

			$i_i = 1;

			foreach ($items as $item) {

				if(count($items)<=32)

				{

					$_b = 31;	

				}

				else

				{

					$_b = 31;

				}

				if($i_i%$_b==0)

				{

					$item_html.='</table></td></tr></table></page>'.$header;	

				}

				$product_name = $db->func_query_first_cell("SELECT b.name FROM oc_product a,oc_product_description b WHERE a.product_id=b.product_id AND a.sku='" . $item['product_sku'] . "'");

				if(strlen($product_name)>82)

				{

					$product_name = substr($product_name,0,82).'...';	

				}

				$item_html.='	

				<tr>

					<td style="width:10px;height:10px;padding:2px;font-size:10px;">'.$i_i.'</td>

					<td style="height:7px;padding:2px" >

						<span class="detail">' .$product_name . '</span>

					</td>

					<td>

					<span class="normal">' . $item['product_sku'] . '</span>

					</td>

					<td class="right" style="height:10px;padding:2px">

						<span class="normal" >' . (int)$item['qty_received'] . '</span><br />

						

					</td>

					<td  class="normal right" style="height:10px;padding:2px">$' . number_format($item['unit_price']/$row['ex_rate'], 2) . '</td>

					<td  class="normal right" style="height:10px;padding:2px">$' . number_format(($item['unit_price']*$item['qty_received'])/$row['ex_rate'], 2) . '</td>

				</tr>';

				$i_i++;

			}

			$html=$html.$header.$item_html;

			$html.='

		</table>

		<table style="margin-top:40px;" cellspacing="1">

			<tr>

				<td class="normal" style="text-align:right;width:640px">Sub Total:</td>

				<td class="normal" style="text-align:right;width:100px">$' . number_format($sub_total, 2) . '</td>

			</tr>

			<tr>

				<td class="normal" style="text-align:right;width:640px">Tax (0.000%):</td>

				<td class="normal" style="text-align:right;width:100px">$0.00</td>

			</tr>

			<tr>

				<td  class="right normal">Shipping:</td>

				<td class="right normal">$' . number_format($row['shipping_cost'], 2) . '</td>

			</tr>

			<tr>

				<td class="normal" style="text-align:right;width:640px">Handling Fee:</td>

				<td class="normal" style="text-align:right;width:100px">$0.00</td>

			</tr>

			<tr>

				<td class="right normal">Exchange Rate:</td>

				<td class="right normal">' . number_format($row['ex_rate'], 2) . '</td>

			</tr>

			<tr>

				<td class="right bold normal">Total:</td>

				<td class="right bold normal">$' . number_format(($sub_total + $row['shipping_cost']), 2) . '</td>

			</tr>';

			$html.='

		</table>

	</td>

</tr>

</table></page>

';


//echo $html;exit;


try {

	$html2pdf = new HTML2PDF('P', 'A4', 'en');

	$html2pdf->setDefaultFont('arial');

	$html2pdf->writeHTML($html);

	$filename =  str_replace("/","-",$row['package_number']);

	$file = $html2pdf->Output('files/' . $filename . '.pdf', 'F');

	if (!$invoice_check) {

		$db->db_exec("INSERT INTO inv_order_docs set order_id='$shipment_id',is_invoice=1,attachment_path='files/" . $filename . ".pdf',date_added='" . date('Y-m-d H:i:s') . "'");

	} else {

		unlink($invoice_check['attachment_path']);

		$db->db_exec("UPDATE inv_order_docs set attachment_path='files/" . $filename . ".pdf',date_added='" . date('Y-m-d H:i:s') . "' WHERE order_id='$shipment_id' and is_invoice=1");

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

  $attachment = $db->func_query_first_cell("SELECT attachment_path FROM inv_order_docs WHERE order_id='" . $shipment_id . "' AND is_invoice=1 order by date_added desc");



if ($_GET['action'] == 'view') {

	echo "<script>window.location='" . $attachment . "'</script>";

	exit;

}

header("Location: addedit_shipment.php?shipment_id=" . $shipment_id);

?>

