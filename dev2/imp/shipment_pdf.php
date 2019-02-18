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
function trimName($string)
{

	if(strlen($string)>64)
	{
		$string = substr($string,0,64).'...';
	}
	return $string;

}


$id = $db->func_escape_string($_GET['shipment_id']);

$rows = $db->func_query("SELECT a.*,b.* FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id AND a.po_business_id='" . $id . "' AND LOWER(order_status) IN ('shipped','unshipped') ORDER BY payment_detail_1 DESC , order_date DESC");

$shipment_details = $db->func_query_first("SELECT * FROM inv_shipments WHERE id='" . $id . "'");
$vendor_details = $db->func_query_first("SELECT * FROM inv_users WHERE id = '". $shipment_details['vendor'] ."'");
$shipment_items = $db->func_query("SELECT * FROM inv_shipment_items WHERE shipment_id = '$id'");
$total_items = $db->func_query_first_cell("SELECT SUM(qty_shipped) FROM inv_shipment_items WHERE shipment_id = '$id'");
$logo = $host_path . "../image/" . oc_config("config_logo");

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
	.center{
		text-align:center;	
	}
	.normal{
		font-size:10px;
		
	}
	.total{
		font-size:12px;
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
	.border-all{
		border: 1px solid #878D91;
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
</page_footer>
<table border="0" >
	<tr>
		<td style="width:435px">
			<table border="0">
				<tr>
					<td>
						<img style="width: 150px;" src="' . $host_path . (($vendor_details['image'])? $vendor_details['image']: '../image/data/0000png/phonepartsusalogo1-1.png') . '">
					</td>
					<td valign="top" style="height: 160px;">
						<table border="0">
							<tr>
								<td><strong>' . $vendor_details['company_name'] . '</strong></td>
							</tr>
							<tr>
								<td>' . $vendor_details['address_1'] . '</td>
							</tr>
							<tr>
								<td>' . $vendor_details['address_2'] . '</td>
							</tr>
							<tr>
								<td>' . $vendor_details['city'] . ', ' . $vendor_details['postal_code'] . ', ' . $vendor_details['providence'] . '</td>
							</tr>
							<tr>
								<td>' . $vendor_details['country'] . '</td>
							</tr>
							<tr>
								<td>Phone:&#160;' . $vendor_details['phone_no'] . '</td>
							</tr>
							<tr>
								<td>Email:&#160;' . $vendor_details['email'] . '</td>
							</tr>
							<tr>
								<td>Website:&#160;' . $vendor_details['website'] . '</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>';



		$header.='<td class="left" >
		<table border="0">
			<tr>
				<td valign="top" class="border-all" style="height: 160px;">
					<table border="0">
						<tr>
							<td colspan="2" class="center">
								STATEMENT
							</td>
						</tr>
						<tr>
							<td style="width:150px font-size:30px;" class="bold">
								Shipment Number:
							</td>

							<td>
								'. $shipment_details['package_number'] .'
							</td>
						</tr>
						<tr>
							<td style="width:150px" class="bold left">
								Shipment Issued:
							</td>

							<td>
								'.date('m/d/Y', strtotime($shipment_details['date_issued'])).'
							</td>
						</tr>
						<tr>
							<td style="width:150px" class="bold left">
								Shipment Completed:
							</td>

							<td>
								'.date('m/d/Y', strtotime($shipment_details['date_completed'])).'
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>';

$header.='
<tr>
	<td style="font-weight:bold">
		<div class="border-all" style="width: 400px;">Bill to / Ship to: &#160; Saad Ahmed</div>
	</td>
	<td></td>
</tr>
<tr>
	<td style="font-weight:bold">PhonePartsUSA.com LLC</td>
	<td></td>
</tr>
<tr>
	<td class="grey">5145 South Arville Street Suite A</td>
	<td></td>
</tr>
<tr>
	<td class="grey">Las Vegas NV 89118</td>
	<td></td>
</tr>
<tr>
	<td class="grey">US</td>
	<td></td>
</tr>';



$header.='
<tr>
	<td colspan="2"  >

		<table  cellpadding="0" cellspacing="1" border="0" class="item_table"     >
			<tr style="background-color:#3C3D3A;color:#fff;">
				<td style="width:55px;height:20px;padding:5px;text-align:center" class="no_border1" >SKU</td>
				<td style="width:320px;padding:5px;text-align:center" class="no_border1" >Name</td>
				<td style="width:60px;padding:5px;text-align:center" class="no_border1" >Shipped</td>
				<td style="width:60px;padding:5px;text-align:center" class="no_border1" >Received</td>
				<td style="width:60px;padding:5px;text-align:center" class="no_border1" >Cost</td>
				<td style="width:60px;padding:5px;text-align:center" class="no_border1" >Sub-Total</td>

			</tr>';
			


			$item_html = '';
			$item_bulk = array();
			$payment_detail = '';
			$counter = 0;
			$i_i = 1;
			$kk = 1;
			$gSubtotal = 0.00;
			$gts = 0;
			$gtr = 0;
			$totalShipment = $shipment_details['shipping_cost'] / $shipment_details['ex_rate'];
			foreach ($shipment_items as $key => $row) {
				$shipmentChar = ($shipment_details['shipping_cost'] / $total_items) * $row['qty_shipped'];
				$lineTotal = ($row['unit_price'] * $row['qty_shipped']) / $shipment_details['ex_rate'];
				$gSubtotal += $lineTotal;
				$gts += (int)$row['qty_shipped'];
				$gtr += (int)$row['qty_received'];

				if (!$row['product_name']) {
					$row['product_name'] = $db->func_query_first_cell("SELECT `name` FROM `oc_product_description` a, `oc_product` b WHERE a.`product_id` = b.`product_id` AND b.`model` = '" . $row['product_sku'] . "'");
				}

				// $name = explode(' ', $row['product_name']);
				// $index = (int)(count($name) / 3);
				// $index = $index * 2;
				// $name[$index] .= '<br>';
				// $row['product_name'] = implode(' ', $name);
				$_b = 19;


				if($i_i%$_b==0) {
					$kk++;
					$item_html.='</table></td></tr></table></page>'.$header;

				}

				$bottom_order = false;
				
				if(($i_i==(($_b*$kk)-1) or $i_i==count($shipment_items)) ) {
					$bottom_order = true;

				}
				
				$item_html.='	
				<tr>
					<td class="'.($bottom_order?'':'no_border').' center normal" style="height:10px;padding:5px" >
						'.$row['product_sku'].'
					</td>
					<td class="'.($bottom_order?'':'no_border').' center normal " style="height:10px;padding:5px">'. trimName($row['product_name']) .'</td>
					<td class="'.($bottom_order?'':'no_border').' center normal " style="height:10px;padding:5px">'.(int)$row['qty_shipped'].'</td>
					<td class="'.($bottom_order?'':'no_border').' center normal " style="height:10px;padding:5px">'.(int)$row['qty_received'].'</td>
					<td class="'.($bottom_order?'':'no_border').' center normal " style="height:10px;padding:5px;text-align:right;">$'.number_format($row['unit_price'] / $shipment_details['ex_rate'],2).'</td>
					<td class="'.($bottom_order?'':'no_border').' normal right " style="height:10px;padding:5px;text-align:right;">$'.number_format($lineTotal,2).'</td>

				</tr>';
				$i_i++;
			}
			if ($i_i > count($shipment_items)) {
				$item_html.='
				<tr>
					<td class="'.($bottom_order?'':'no_border').' center total" style="height:10px;padding:5px" colspan="2"></td>
					<td class="'.($bottom_order?'':'no_border').' center total " style="height:10px;padding:5px">'.(int)$gts.'</td>
					<td class="'.($bottom_order?'':'no_border').' center total " style="height:10px;padding:5px">'.(int)$gtr.'</td>
					<td class="'.($bottom_order?'':'no_border').' center total " style="height:10px;padding:5px;text-align:right;"></td>
					<td class="'.($bottom_order?'':'no_border').' total right " style="height:10px;padding:5px;text-align:right;"><strong>$'.number_format($gSubtotal,2).'</strong></td>

				</tr>
				<tr>
					<td class="'.($bottom_order?'':'no_border').' right total" style="height:10px;padding:5px" colspan="5"><strong>Shipping Fee:</strong></td>
					<td class="'.($bottom_order?'':'no_border').' total right " style="height:10px;padding:5px;text-align:right;"><strong>$'.number_format($totalShipment,2).'</strong></td>
				</tr>
				<tr>
					<td class="'.($bottom_order?'':'no_border').' right total" style="height:10px;padding:5px" colspan="5"><strong>Total:</strong></td>
					<td class="'.($bottom_order?'':'no_border').' total right " style="height:10px;padding:5px;text-align:right;"><strong>$'.number_format($totalShipment + $gSubtotal,2).'</strong></td>
				</tr>';
			}
			$html=$html.$header.$item_html;
			$html.='
		</table>


	</td>

</tr>

</table>
</page>
';
//die($html);
try {



	$html2pdf = new HTML2PDF('P', 'A4', 'en');

	$html2pdf->setDefaultFont('arial');
	$html2pdf->writeHTML($html);

	$filename = 'Shipment-Invoice-'.$shipment_details['package_number'].'-'.time();
	$file = $html2pdf->Output('files/' . $filename . '.pdf', 'F');

} catch (HTML2PDF_exception $e) {
	echo $e;
	exit;
}
$attachment = $host_path . 'files/' . $filename . '.pdf';
if ($_GET['action'] == 'view') {
	echo "<script>window.location='" . $attachment . "'</script>";
	exit;
}
header("Location: view_shipment.php?shipment_id=$id");
?>

