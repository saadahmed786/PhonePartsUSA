<?php

include("../phpmailer/class.smtp.php");
include("../phpmailer/class.phpmailer.php");
require_once("../auth.php");
require_once("../inc/functions.php");
require_once('../html2_pdf_lib/html2pdf.class.php');
$shipment_number = $db->func_escape_string($_GET['shipment_number']);
$detail = $db->func_query_first("SELECT * FROM oc_buyback WHERE shipment_number='".$shipment_number."'");
$logo = $host_path . "../image/" . oc_config("config_logo");
//$logo = "http://localhost/phone/image/".oc_config("config_logo");

if($detail['status']=='In QC')
{
	$detail['status'] = 'QC Completed';	
}
if(!$detail)
{
	 header("Location:$host_path/buyback/shipments.php");
    exit;
	
}

$products = $db->func_query("SELECT * FROM oc_buyback_products WHERE buyback_id='".$detail['buyback_id']."'");

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
						
						$customer_detail = $db->func_query_first("SELECT email,telephone FROM oc_customer WHERE customer_id='".$detail['customer_id']."'");
						$address = $db->func_query_first("SELECT * FROM oc_address WHERE address_id='".$detail['address_id']."'");
						
						
						$firstname = $address['firstname'];
						$lastname = $address['lastname'];
						$email = $customer_detail['email'];
						$telephone = $customer_detail['telephone'];
						$address_1 = $address['address_1'];
						$city = $address['city'];
						$postcode = $address['postcode'];
						$zone_id = $address['zone_id'];
					}
				   $zone = $db->func_query_first_cell("SELECT name FROM oc_zone WHERE zone_id='".(int)$zone_id."'");

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
	SUMMARY
</td>';

$header.='
</tr>
<tr>
	<td style="font-weight:bold">PhonePartsUSA.com LLC</td>
	<td  class="bold right" style="font-size:12px">Shipment #: '.$detail['shipment_number'].'</td>
</tr>
<tr>
	<td class="grey">5145 South Arville Street Suite A</td>
	<td class="bold right" style="font-size:12px" ></td>
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
				
				<td  align="right">
<table border="0" >
						<tr>
							<td style="width:530px" class="bold dark-grey" >Other Details</td>
						</tr>
						<tr>
							<td>Tracking #: '.$detail['tracking_no'].'</td>
						</tr>
						<tr>
							<td>Payment Type: '.$detail['payment_type'].'</td>
						</tr>
						<tr>
							<td>Procedure: '.$detail['option'].'</td>
						</tr>


					</table>
				</td>
			</tr>
		</table>

	</td>
	
</tr>
<tr>
	<td colspan="2"  >

		<table  cellpadding="0" cellspacing="1" border="0" class="item_table"     >
			';
			


			foreach ($rows as $key => $row) {}
			$html=$html.$header;
			$html.='
		</table>


	</td>

</tr>

</table></page>

';

try {



	$html2pdf = new HTML2PDF('P', 'A4', 'en');

	$html2pdf->setDefaultFont('arial');
	$html2pdf->writeHTML($html);

	$filename = time();
	$file = $html2pdf->Output('../files/' . $filename . '.pdf', 'F');


  } catch (HTML2PDF_exception $e) {
  	echo $e;
  	exit;
  }
header("Location: ../files/".$filename.'.pdf');
?>

