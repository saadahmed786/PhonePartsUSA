<?php

include("../phpmailer/class.smtp.php");
include("../phpmailer/class.phpmailer.php");
if (!isset($_GET['authcode'])) {
	require_once("../auth.php");
} else {
	require_once("../config.php");
}
require_once("../inc/functions.php");
require_once('../html2_pdf_lib/html2pdf.class.php');
$return_id = $db->func_escape_string($_GET['return_id']);
$detail = $db->func_query_first("SELECT * FROM `inv_returns` a, `inv_orders` b WHERE a.`order_id` = b.`order_id` AND a.`id` = '" . (int)$return_id . "'");
$logo = str_replace('imp/', '', $host_path) . "image/" . oc_config("config_logo");
//$logo = "http://localhost/phone/image/".oc_config("config_logo");

if($detail['rma_status']=='In QC')
{
	$detail['rma_status'] = 'QC Completed';	
}
if(!$detail)
{
	if (!isset($_GET['authcode'])) {
		$_SESSION['message'] = 'Return Not Found';
		header("Location:$host_path/manage_returns.php");
	} else {
		echo '<h1>Return not found</h1>';
	}
	exit;
	
}

$products = $db->func_query("SELECT * FROM `inv_return_items` WHERE return_id = '" . (int)$return_id . "'");

$replacements = $db->func_query("SELECT * FROM `inv_return_decision` WHERE return_id = '" . (int)$return_id . "' AND action = 'Issue Replacement'");

$credits = $db->func_query("SELECT * FROM `inv_return_decision` WHERE return_id = '" . (int)$return_id . "' AND action = 'Issue Credit'");

$refunds = $db->func_query("SELECT * FROM `inv_return_decision` WHERE return_id = '" . (int)$return_id . "' AND action = 'Issue Refund'");

$received_check = $db->func_query_first_cell("SELECT date_added FROM inv_return_history WHERE rma_number='" . $detail['rma_number'] . "' AND return_status='Received'");

$html = '
<style>
	@page {
		size: 8.27in 11.69in;
		margin: 27mm 16mm 27mm 16mm;
	}
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
		font-size:11px;
		
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


$html.='
<page>
	<page_footer>
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
			<td style="width:560px">
				<img src="' . $logo . '">
			</td>';


			$html.='
			<td  style="font-size:30px;" class="right" >
				DETAIL
			</td>
		</tr>';

		$html.='
		<tr>
			<td style="font-weight:bold">PhonePartsUSA.com LLC</td>
			<td  class="bold right" style="font-size:12px">RMA Number #: '.$detail['rma_number'].'</td>
		</tr>
		<tr>
			<td class="grey">5145 South Arville Street Suite A</td>
			<td  class="right bold" style="font-size:12px">Order ID : '.$detail['order_id'].'</td>
		</tr>
		<tr>
			<td class="grey">Las Vegas NV 89118</td>
			<td  class="right bold" style="font-size:12px">Status: '.$detail['rma_status'].'</td>
		</tr>




		';

		$html.='
		<tr>
			<td colspan="2">
				<table  border="0">
					<tr>


						<td   valign="bottom">
							<table border="0" >
								<tr>
									<td style="width:400px" class="bold dark-grey" >Customer</td>
								</tr>
								<tr>
									<td>' . $detail['customer_name'] . '</td>
								</tr>
								<tr>
									<td>' . $detail['street_address'] . '</td>
								</tr>
								<tr>
									<td>' . $detail['zipcode'] . '</td>
								</tr>

								<tr>
									<td>Order Date: ' . americanDate($detail['order_date']) . '</td>
								</tr>


							</table>
						</td>
						<td valign="bottom" >
							<table border="0" >
								<tr>
									<td  class="bold dark-grey" >Other Details</td>
								</tr>
								<tr>
									<td>Date Added: '. americanDate($detail['date_added']) .'</td>
								</tr>
								<tr>
									<td>Date Received: ' . americanDate($received_check) . '</td>
								</tr>
								<tr>
									<td> QC Date: ' . americanDate($detail['date_qc']) . '</td>
								</tr>

								<tr>
									<td> Payment Method: ' . $detail['payment_method'] . '</td>
								</tr>


							</table>
						</td>
					</tr>
				</table>

			</td>
		</tr>
		<tr>
			<td colspan="2"  >
				<br><br>

				<table style="width:100%;" cellpadding="0" cellspacing="1" border="0"     >
					<tr>

						<td style="" valign="top">
							<strong class="normal">All Products</strong>
							<table style="width: 100%;" cellpadding="0" cellspacing="1" border="0"      >
								<tr style="background-color:#3C3D3A;color:#fff;" class="normal">
									<td style="width:30%;text-align:center;padding:4px">Model</td>
									<td style="width:15%;text-align:center;padding:4px">Quantity</td>
									<td style="width:20%;text-align:center;padding:4px">Reason</td>
									<td style="width:20%;text-align:center;padding:4px">Action</td>
									<td  style="width:15%;text-align:center;padding:4px">Price</td>
								</tr>
								';
								$total = 0;
								foreach($products as $product)
								{


									$html.='<tr class="normal">
									<td class="center">'.$product['sku'].'</td>
									<td class="center">'.$product['quantity'].'</td>
									<td class="center">'.$product['return_code'].'</td>
									<td class="center">'.$product['decision'].'</td>
									<td class="right">$'.number_format($product['price'],2).'</td></tr>';
									$total += $product['price'];
								}




								$html.='
								<tr class="normal">

									<td colspan="3">

									</td>
									<td class="right"><strong>Total Amount:</strong></td>
									<td class="right"><strong>$'.number_format($total,2).'</strong></td>
								</tr>

							</table>
						</td>
					</tr>

				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<br><br>
				<div style="width:100%; position: relative;">
					<div style="width: 32%; position: absolute;">
						<strong>Replacement</strong>
						<table style="width: 100%;" cellpadding="0" cellspacing="1" border="0">
							<tr style="background-color:#3C3D3A;color:#fff;" class="normal">
								<td style="width:70%;text-align:center;padding:4px">Model</td>
								<td  style="width:30%;text-align:center;padding:4px">Price</td>
							</tr>';
							if ($replacements) {
								$total = 0;
								foreach ($replacements as $product) {
									$html .= '
									<tr class="normal">
										<td class="center">
											'. $product['sku'] .'
										</td>
										<td class="right">
											$'. number_format($product['price'],2) .'
										</td>
									</tr>
									';
									$total += $product['price'];
								}
								$html .= '
								<tr class="normal">
									<td></td>
									<td class="right"><strong>$'.number_format($total,2).'</strong></td>
								</tr>
								';
							} else {
								$html .= '<tr class="normal"><td colspan="2">No replacements yet!</td></tr>';
							}
							$html .='
						</table>
					</div>

					<div style="width: 32%; left: 34%; position: absolute;">
						<strong>Credits</strong>
						<table style="width: 100%;" cellpadding="0" cellspacing="1" border="0">
							<tr style="background-color:#3C3D3A;color:#fff;" class="normal">
								<td style="width:70%;text-align:center;padding:4px">Model</td>
								<td  style="width:30%;text-align:center;padding:4px">Price</td>
							</tr>';
							if ($credits) {
								$total = 0;
								foreach ($credits as $product) {
									$html .= '
									<tr class="normal">
										<td class="center">
											'. $product['sku'] .'
										</td>
										<td class="right">
											$'. number_format($product['price'],2) .'
										</td>
									</tr>
									';
									$total += $product['price'];
								}
								$html .= '
								<tr class="normal">
									<td></td>
									<td class="right"><strong>$'.number_format($total,2).'</strong></td>
								</tr>
								';
							} else {
								$html .= '<tr class="normal"><td colspan="2">No credits yet!</td></tr>';
							}
							$html .='
						</table>
					</div>

					<div style="width: 32%; right: 0%; position: absolute;">
						<strong>Refunds</strong>
						<table style="width: 100%;" cellpadding="0" cellspacing="1" border="0">
							<tr style="background-color:#3C3D3A;color:#fff;" class="normal">
								<td style="width:70%;text-align:center;padding:4px">Model</td>
								<td  style="width:30%;text-align:center;padding:4px">Price</td>
							</tr>';
							if ($refunds) {
								$total = 0;
								foreach ($refunds as $product) {
									$html .= '
									<tr class="normal">
										<td class="center">
											'. $product['sku'] .'
										</td>
										<td class="right">
											$'. number_format($product['price'],2) .'
										</td>
									</tr>
									';
									$total += $product['price'];
								}
								$html .= '
								<tr class="normal">
									<td></td>
									<td class="right"><strong>$'.number_format($total,2).'</strong></td>
								</tr>
								';
							} else {
								$html .= '<tr class="normal"><td colspan="2">No refunds yet!</td></tr>';
							}
							$html .='
						</table>
					</div>
				</div>
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

	$filename =  $detail['rma_number'].'-'.time();
	$file = $html2pdf->Output('../files/' . $filename . '.pdf', 'F');

} catch (HTML2PDF_exception $e) {
	echo $e;
	exit;
}
header("Location: ../files/".$filename.'.pdf');
?>

