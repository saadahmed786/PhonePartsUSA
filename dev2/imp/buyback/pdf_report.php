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
$shipment_number = $db->func_escape_string($_GET['shipment_number']);
$detail = $db->func_query_first("SELECT * FROM oc_buyback WHERE shipment_number='".$shipment_number."'");
$logo =   "https://phonepartsusa.com/image/" . oc_config("config_logo");
//$logo = "http://localhost/phone/image/".oc_config("config_logo");

if($detail['status']=='In QC')
{
	$detail['status'] = 'QC Completed';	
}
if(!$detail)
{
	if (!isset($_GET['authcode'])) {
	 header("Location:$host_path/buyback/shipments.php");
	} else {
		echo '<h1>LBB not found</h1>';
	}
    exit;
	
}

$products = $db->func_query("SELECT * FROM oc_buyback_products WHERE buyback_id='".$detail['buyback_id']."'");

$detail['total'] = 0.00;

foreach($products as $product)
{
	//$detail['total']+= $_product['total_oem_total']+$_product['total_non_oem_total'];
	
	
	$quantities = $db->func_query_first("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$product['buyback_product_id']."'");
			
			if($quantities)
			{
				$oem_qty_a = (int)$quantities['oem_qty_a'];
				$oem_qty_b = (int)$quantities['oem_qty_b'];
				$oem_qty_c = (int)$quantities['oem_qty_c'];
				$oem_qty_d = (int)$quantities['oem_qty_d'];
				$non_oem_qty_a = (int)$quantities['non_oem_qty_a'];
				$non_oem_qty_b = (int)$quantities['non_oem_qty_b'];
				$non_oem_qty_c = (int)$quantities['non_oem_qty_c'];
				$non_oem_qty_d = (int)$quantities['non_oem_qty_d'];
				$salvage_qty = (int)$quantities['salvage_qty'];
						}
			if($product['admin_updated']=='1')
			{
			$oem_qty_a = $product['admin_oem_qty'];			
			$non_oem_qty_a = $product['admin_non_oem_qty'];
			$salvage_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_salvage_qty']: $salvage_qty;
			$unacceptable_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_unacceptable']: $unacceptable_qty;
			$rejected_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_rejected']: $rejected_qty;
			}
			
			$admin_total = ($oem_qty_a * $product['oem_a_price']) + ($oem_qty_b * $product['oem_b_price'])+ ($oem_qty_c * $product['oem_c_price'])+ ($oem_qty_d * $product['oem_d_price']) + ($non_oem_qty_a * $product['non_oem_a_price']) + ($non_oem_qty_b * $product['non_oem_b_price']) + ($non_oem_qty_c * $product['non_oem_c_price']) + ($non_oem_qty_d * $product['non_oem_d_price'])+($salvage_qty * $product['salvage_price']);
			$admin_combine_total+=(float)$admin_total;
			
}
$cash_discount = $db->func_query_first_cell("SELECT cash_discount FROM inv_buy_back LIMIT 1");
	
if(!$cash_discount) $cash_discount = 0.00;

$discount = ((float)$detail['total'] * (float)$cash_discount) / 100;
$discount = round($discount,2);

$detail['total'] = (float)$admin_combine_total;

if($detail['payment_type']=='cash')
{
$detail['total'] = $detail['total'] - $discount;
}



if($detail['customer_id']==0)
					{
						
						$firstname = $detail['firstname'];
						$lastname = $detail['lastname'];
						$email = $detail['email'];
						$telephone = $detail['telephone'];
						$address_1 = $detail['address_1'];
						$city = $detail['city'];
						$postcode = $detail['postcode'];
						$zone_id = $detail['zone_id'];
					}
					else
					{
						//
						// $customer_detail = $db->func_query_first("SELECT email,telephone FROM oc_customer WHERE customer_id='".$detail['customer_id']."'");
						// $address = $db->func_query_first("SELECT * FROM oc_address WHERE address_id='".$detail['address_id']."'");
						
						
						// $firstname = $address['firstname'];
						// $lastname = $address['lastname'];
						// $email = $customer_detail['email'];
						// $telephone = $customer_detail['telephone'];
						// $address_1 = $address['address_1'];
						// $city = $address['city'];
						// $postcode = $address['postcode'];
						// $zone_id = $address['zone_id'];
						// 
						$customer_detail = $db->func_query_first("SELECT email,telephone FROM oc_customer WHERE customer_id='".$detail['customer_id']."'");
						$address = $db->func_query_first("SELECT * FROM oc_address WHERE address_id='".$detail['address_id']."'");
						
						$email = $customer_detail['email'];
						$telephone = $customer_detail['telephone'];

						if($detail['address_id']!='-1' && $detail['address_id']!='0')
						{
						$firstname = $address['firstname'];
						$lastname = $address['lastname'];
						
						$address_1 = $address['address_1'];
						$city = $address['city'];
						$postcode = $address['postcode'];
						$zone_id = $address['zone_id'];
					}
					else
					{
						$firstname = $detail['firstname'];
						$lastname = $detail['lastname'];
						
						$address_1 = $detail['address_1'];

						$city = $detail['city'];
						$postcode = $detail['postcode'];
						$zone_id = $detail['zone_id'];

					}
					}
				   $zone = $db->func_query_first_cell("SELECT name FROM oc_zone WHERE zone_id='".(int)$zone_id."'");
				   
				   $payment_detail = $db->func_query_first("SELECT * FROM inv_buyback_payments WHERE buyback_id='".$detail['buyback_id']."'");

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
		font-size:9px;	
	}
	.qc{
		text-align:center;
		font-size:10px;	
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


$html.='<page><page_footer>
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
	
	
	$html.='<td  style="font-size:30px;" class="right" >
	DETAIL
</td>';

$html.='
</tr>
<tr>
	<td style="font-weight:bold">PhonePartsUSA.com LLC</td>
	<td  class="bold right" style="font-size:12px">Shipment #: '.$detail['shipment_number'].'</td>
</tr>
<tr>
	<td class="grey">5145 South Arville Street Suite A</td>
	<td  class="right bold" style="font-size:12px">Total : $'.number_format($payment_detail['amount'],2).'</td>
</tr>
<tr>
	<td class="grey">Las Vegas NV 89118</td>
	<td  class="right bold" style="font-size:12px">Status: '.$detail['status'].'</td>
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
							<td>' . $firstname.' '.$lastname . '</td>
						</tr>
						<tr>
							<td>' . $address_1 . '</td>
						</tr>
						<tr>
							<td>' . $city . ', ' . $zone . ' ' . $postcode . '</td>
						</tr>


					</table>
				</td>
				<td valign="bottom" >
					<table border="0" >
						<tr>
							<td  class="bold dark-grey" >Other Details</td>
						</tr>
						<tr>
							<td>Tracking # '.($detail['tracking_no']?$detail['tracking_no']:'N/A').'</td>
						</tr>
						<tr>
							<td>Payment: ' . $detail['payment_type'].($payment_detail?($detail['payment_type']=='store_credit'?'('.$payment_detail['credit_code'].' - $'.number_format($payment_detail['amount'],2).')':'('.$payment_detail['transaction_id'].' - $'.number_format($payment_detail['amount'],2).')'):'') . '</td>
						</tr>
						<tr>
							<td> Procedure: ' . $detail['option'] . '</td>
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

		<table  cellpadding="0" cellspacing="1" border="0"     >
		<tr>
		
		<td width="50%" style="" valign="top">
		<strong class="normal">Customer Data</strong>
		<table  cellpadding="0" cellspacing="1" border="0"      >
			<tr style="background-color:#3C3D3A;color:#fff;" class="normal">
			<td style="width:200px;height:10px;padding:4px">LCD Type</td>
			<td  style="width:140px;text-align:right;padding:4px">Total</td>
			</tr>
			';
			foreach($products as $product)
			{
				if($product['data_type']!='customer') continue;

			$customer_quantity_total+= $product['qty'];
				$html.='<tr class="normal">
				<td>'.$product['description'].'</td>
				<td class="right">'.($product['qty']).'</td>
				</tr>';
				
			}


			
			
			$html.='
			<tr class="normal">
    
    <td>
    
    </td>
    <td class="right"><strong>'.$customer_quantity_total.'</strong></td>
    </tr>
			
			</table>
			</td>
			<td width="50%" valign="top">
			<strong class="normal">Receiving Data</strong>
		<table  cellpadding="0" cellspacing="1" border="0"      >
			<tr style="background-color:#3C3D3A;color:#fff;" class="normal">
			<td style="width:200px;height:10px;padding:4px">LCD Type</td>
			
			<td  style="width:150px;text-align:right;padding:3px">Total</td>
			</tr>';
			$_total_received = 0;
foreach($products as $product)
{
if($product['data_type']!='customer' and $product['data_type']!='received') continue;
$_total_received+=$product['total_received'];
$html.='<tr class="normal">
<td>'.$product['description'].'</td>
<td class="right">'.$product['total_received'].'</td>
</tr>';	
	
}

			$html.='
			<tr class="normal">
			<td> </td>
			<td class="right" ><strong>'.($detail['total_received']?$detail['total_received']:$_total_received).'</strong></td>
			</tr>
			
			</table>
			</td>
			</tr>
			
		</table>


	</td>

</tr>
<tr>
	<td colspan="2"  >
<br>
		<table  cellpadding="0" cellspacing="1" border="0"     >
		<tr>
		
		<td  style="" valign="top">
		<strong class="normal">QC Data</strong>
		<table  cellpadding="0" cellspacing="1" border="0"      >
			<tr style="background-color:#3C3D3A;color:#fff;" class="normal">
			<td style="width:140px;height:10px;padding:4px">LCD Type</td>
			<td style="width:31px;text-align:center;padding:2px" >O A</td>
			<td style="width:31px;text-align:center;padding:2px" >O A-</td>
			<td style="width:31px;text-align:center;padding:2px" >O B</td>
			<td style="width:31px;text-align:center;padding:2px" >O C</td>
			<td style="width:41px;text-align:center;padding:2px"  >N A</td>
			<td style="width:41px;text-align:center;padding:2px"  >N A-</td>
			<td style="width:41px;text-align:center;padding:2px"  >N B</td>
			<td style="width:41px;text-align:center;padding:2px"  >N C</td>
			<td style="width:50px;text-align:center;padding:2px"  >Salvage</td>
			
			<td style="width:70px;text-align:center;padding:2px"  >Damaged</td>
			<td  style="width:82px;text-align:right;padding:2px">Total</td>
			</tr>
			';
			$qc_quantity_total = 0;

$rejected_items = array();
			foreach($products as $product)
			{
			if($product['data_type']!='customer' and $product['data_type']!='qc') continue;
	$qc_quantity_total+=$product['total_qc_received'];
	$quantities = $db->func_query_first("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$product['buyback_product_id']."'");
			if($quantities)
			{
				$oem_qty_a = (int)$quantities['oem_qty_a'];
				$oem_qty_b = (int)$quantities['oem_qty_b'];
				$oem_qty_c = (int)$quantities['oem_qty_c'];
				$oem_qty_d = (int)$quantities['oem_qty_d'];
				$non_oem_qty_a = (int)$quantities['non_oem_qty_a'];
				$non_oem_qty_b = (int)$quantities['non_oem_qty_b'];
				$non_oem_qty_c = (int)$quantities['non_oem_qty_c'];
				$non_oem_qty_d = (int)$quantities['non_oem_qty_d'];
				$salvage_qty = (int)$quantities['salvage_qty'];
				$unacceptable_qty = (int)$quantities['unacceptable_qty'];
				$rejected_qty = (int)$quantities['rejected_qty'];
			}
					$html.='<tr class="normal">
				<td>'.$product['description'].'</td>
				<td class="qc">'.($oem_qty_a).'</td>
				<td class="qc">'.($oem_qty_b).'</td>
				<td class="qc">'.($oem_qty_c).'</td>
				<td class="qc">'.($oem_qty_d).'</td>
				<td class="qc">'.($non_oem_qty_a).'</td>
				<td class="qc">'.($non_oem_qty_b).'</td>
				<td class="qc">'.($non_oem_qty_c).'</td>
				<td class="qc">'.($non_oem_qty_d).'</td>
				<td class="qc">'.$salvage_qty.'</td>
				
				<td class="qc">'.$rejected_qty.'</td>
				
				<td class="right">'.$product['total_qc_received'].'</td>
				</tr>';
				
			}


			
			
			$html.='
			<tr class="normal">
    
    <td colspan="11">
    
    </td>
    <td class="right"><strong>'.$qc_quantity_total.'</strong></td>

    </tr>
			
			</table>
		
</td>
</tr>
<tr>
<td  style="" valign="top">
		<strong class="normal">Admin Data</strong>
		<table  cellpadding="0" cellspacing="1" border="0"      >
			<tr style="background-color:#3C3D3A;color:#fff;" class="normal">
			<td style="width:140px;height:10px;padding:4px">LCD Type</td>
			<td style="width:31px;text-align:center;padding:2px" >O A</td>
			<td style="width:31px;text-align:center;padding:2px" >O A-</td>
			<td style="width:31px;text-align:center;padding:2px" >O B</td>
			<td style="width:31px;text-align:center;padding:2px" >O C</td>
			<td style="width:41px;text-align:center;padding:2px"  >N A</td>
			<td style="width:41px;text-align:center;padding:2px"  >N A-</td>
			<td style="width:41px;text-align:center;padding:2px"  >N B</td>
			<td style="width:41px;text-align:center;padding:2px"  >N C</td>
			<td style="width:50px;text-align:center;padding:2px"  >Salvage</td>
			
			<td style="width:70px;text-align:center;padding:2px"  >Damaged</td>
			<td  style="width:82px;text-align:right;padding:2px">Total</td>
			</tr>
			';
			$qc_quantity_total = 0;
$admin_oem_total = 0.00;
$admin_non_oem_total = 0.00;
$admin_combine_total = 0.00;

						foreach($products as $product)
			{
		if($product['data_type']!='customer' and $product['data_type']!='qc' and $product['data_type']!='admin') continue;
			
			
			$quantities = $db->func_query_first("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$product['buyback_product_id']."'");
			
			if($quantities)
			{
				$oem_qty_a = (int)$quantities['oem_qty_a'];
				$oem_qty_b = (int)$quantities['oem_qty_b'];
				$oem_qty_c = (int)$quantities['oem_qty_c'];
				$oem_qty_d = (int)$quantities['oem_qty_d'];
				$non_oem_qty_a = (int)$quantities['non_oem_qty_a'];
				$non_oem_qty_b = (int)$quantities['non_oem_qty_b'];
				$non_oem_qty_c = (int)$quantities['non_oem_qty_c'];
				$non_oem_qty_d = (int)$quantities['non_oem_qty_d'];
				$salvage_qty = (int)$quantities['salvage_qty'];
				$unacceptable_qty = (int)$quantities['unacceptable_qty'];
				$rejected_qty = (int)$quantities['rejected_qty'];
						}
			if($product['admin_updated']=='1')
			{
			$oem_qty = $product['admin_oem_qty'];			
			$non_oem_qty = $product['admin_non_oem_qty'];
			$salvage_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_salvage_qty']: $salvage_qty;
			$unacceptable_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_unacceptable']: $unacceptable_qty;
			$rejected_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_rejected']: $rejected_qty;
			}
			
			$admin_oem_total+=(int)$oem_qty * (float)$product['oem_price'];
			$admin_non_oem_total+=(int)$non_oem_qty * (float)$product['non_oem_price'];
			
			$admin_total = ($oem_qty_a * $product['oem_a_price']) + ($oem_qty_b * $product['oem_b_price'])+ ($oem_qty_c * $product['oem_c_price'])+ ($oem_qty_d * $product['oem_d_price']) + ($non_oem_qty_a * $product['non_oem_a_price']) + ($non_oem_qty_b * $product['non_oem_b_price']) + ($non_oem_qty_c * $product['non_oem_c_price']) + ($non_oem_qty_d * $product['non_oem_d_price'])+($salvage_qty * $product['salvage_price']);
			
			$admin_combine_total+=(float)$admin_total;
			
			
					$html.='<tr class="normal">
				<td>'.$product['description'].'</td>
				<td class="center">'.(int)$oem_qty_a.' x '.$product['oem_a_price'].'</td>
				<td class="center">'.(int)$oem_qty_b.' x '.$product['oem_b_price'].'</td>
				<td class="center">'.(int)$oem_qty_c.' x '.$product['oem_c_price'].'</td>
				<td class="center">'.(int)$oem_qty_d.' x '.$product['oem_d_price'].'</td>
				<td class="center">'.(int)$non_oem_qty_a.' x '.$product['non_oem_a_price'].'</td>
				<td class="center">'.(int)$non_oem_qty_b.' x '.$product['non_oem_b_price'].'</td>
				<td class="center">'.(int)$non_oem_qty_c.' x '.$product['non_oem_c_price'].'</td>
				<td class="center">'.(int)$non_oem_qty_d.' x '.$product['non_oem_d_price'].'</td>
				<td class="center">'.(int)$salvage_qty.' x '.$product['salvage_price'].'</td>
				
				<td class="center">'.(int)$rejected_qty.' x 0.00</td>

				
				<td class="right">$'.number_format($admin_total,2).'</td>
				</tr>';
				
			}


			
			
			$html.='
		<tr class="normal">
    
    <td colspan="11">
    
    </td>
    <td class="right"><strong>$'.number_format($admin_combine_total,2).'</strong></td>

    </tr>	
			
			</table>
			</td>
		</tr>
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

	$filename =  $shipment_number.'-'.time();
	$file = $html2pdf->Output('../files/' . $filename . '.pdf', 'F');

	$db->db_exec("UPDATE oc_buyback SET file_pdf='".$filename.".pdf' WHERE shipment_number='".$shipment_number."'");

  } catch (HTML2PDF_exception $e) {
  	echo $e;
  	exit;
  }
header("Location: ../files/".$filename.'.pdf');
?>

