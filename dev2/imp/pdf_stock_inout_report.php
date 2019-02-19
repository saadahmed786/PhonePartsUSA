<?php

include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
require_once("auth.php");
require_once("inc/functions.php");
require_once('html2_pdf_lib/html2pdf.class.php');

function secureItemName($string) {

   //$string = str_replace(' ', '-', $data); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.

}
function trimName($string)
{

	if(strlen($string)>25)
	{
		$string = substr($string,0,25).'...';
	}
	return $string;

}


 $condition_sql = " (cron_date BETWEEN '$start_date' and '$end_date') ";
  

 $sku = $db->func_escape_string($_REQUEST['sku']);
   	
   	if(!isset($_REQUEST['start_date']))
   	{
   		$start_date = date('Y-m-d',strtotime('-1 days'));

		$end_date = date('Y-m-d');
   	}
   	else
   	{
   	$start_date = $db->func_escape_string($_REQUEST['start_date']);
    $end_date = $db->func_escape_string($_REQUEST['end_date']);
    }   


        if(@$sku){
            $conditions[] =  " LCASE(sku)=LCASE('".$sku."') ";
        }

        if(@$start_date && $end_date)
        {
          $conditions[] =  " (cron_date BETWEEN '$start_date' and '$end_date') ";
        }

        
            $condition_sql = implode(" AND " , $conditions);
        
        
        if(!$condition_sql){
            $condition_sql = " (cron_date BETWEEN '$start_date' and '$end_date') ";

        }
        
        
    
$inv_query = "SELECT sku,sum(total_sold) as total_sold,SUM(qty_received) as qty_received,sum(qty_sold) as qty_sold, avg(avg_cost) as avg_cost,avg(avg_price) as avg_price from inv_inout_report WHERE sku<>'SIGN'  AND $condition_sql   GROUP BY sku ORDER BY sku asc";

//die($inv_query);
$logo = $host_path . "../image/" . oc_config("config_logo");
//$logo = "http://localhost/phone/image/".oc_config("config_logo");

$inv_orders = $db->func_query($inv_query);
$total_records = count($inv_orders);
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
		font-size:11px;
		
	}
	.detail{
		font-size:9px;
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
</page_footer>

<table border="0" >
';
	
	






	$header3='
<tr>
	<td    >

		<table  cellpadding="0" cellspacing="1" border="0" class="item_table"     >
			<tr style="background-color:#3C3D3A;color:#fff;">
				<td style="width:25px;height:7px;padding:5px;text-align:center" class="no_border1 normal" >#</td>
				<td style="width:55px;padding:2px;text-align:center" class="no_border1 normal" >Date</td>
				<td style="width:55px;padding:2px;text-align:center" class="no_border1 normal" >SKU</td>
					<td style="width:100px;padding:2px;text-align:center" class="no_border1 normal" >Item</td>
					<td style="width:45px;padding:2px;text-align:center" class="no_border1 normal" >Current</td>
				<td style="width:45px;padding:2px;text-align:center" class="no_border1 normal" >Received</td>
				<td style="width:45px;padding:2px;text-align:center" class="no_border1 normal" >Sold</td>
				<td style="width:50px;padding:2px;text-align:center" class="no_border1 normal" >Avg Cost</td>
				<td style="width:50px;padding:2px;text-align:center;" class="no_border1 normal"  >Avg Price</td>

				<td style="width:50px;padding:2px;text-align:center;" class="no_border1 normal"  >Total</td>

				<td style="width:50px;padding:2px;text-align:center;" class="no_border1 normal"  >Profit</td>

			</tr>';
			


			$item_html = '';
			$item_bulk = array();
			$payment_detail = '';
			$counter = 0;
			$i_i = 1;
			$kk = 1;
			$item_array = array();
			$exception = 0;
			$item_issue=0;
			$customer_damanged = 0;
			$not_tested = 0;
			$not_ppusa = 0;
			$over_60 = 0;
			$shipping_damange = 0;
			$_b = 55;
			$iterator = 1;
			foreach($inv_orders as $row)
			{
				
				
				

				//if($i_i%$_b==0) {
					if($i_i==$_b+1)
					{

						$_b = $_b + 58;
					$kk++;
					
					$item_html.='</table></td></tr></table></page>'.$header.$header3;
					

				}

				// $items = $db->func_query("SELECT * FROM inv_orders_items WHERE order_id='" . $row['order_id'] . "'");	
				// $sub_total = 0.00;
				// foreach($items as $item) {
				// 	$sub_total+=(float) $item['product_price'];	
				// }

				// $charge = $sub_total + $row['shipping_cost'];


				// $credit = $row['paid_price'];	

				 $bottom_order = false; // by zaman
				if(($i_i==(($_b)) or $i_i==count($inv_orders)) ) {
					$bottom_order = true;

				}
				$profit = ($row['avg_price']*$row['qty_sold']) - ($row['avg_cost']*$row['qty_sold']);  
										
				
				$item_html.='	
				<tr>
					<td class="'.($bottom_order?'':'no_border').' center normal bold" style="height:5px;padding:2px" >
						'.$iterator.'
					</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal bold " style="height:5px;padding:2px"></td>
					<td  class="'.($bottom_order?'':'no_border').' center normal bold " style="height:5px;padding:2px">'.($row['sku']).'</td>
					<td  class="'.($bottom_order?'':'no_border').' normal bold " style="height:5px;padding:2px">'.trimName(secureItemName(getItemName($row['sku']))).'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal bold " style="height:5px;padding:2px">'.$db->func_query_first_cell("SELECT quantity FROM oc_product WHERE model='".$row['sku']."'").'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal bold" style="height:5px;padding:2px">'.$row['qty_received'].'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal bold" style="height:5px;padding:2px">'.$row['qty_sold'].'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal bold" style="height:5px;padding:2px">$'.number_format($row['avg_cost'],2).'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal bold" style="height:5px;padding:2px">$'.number_format($row['avg_price'],2).'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal bold" style="height:5px;padding:2px">$'.number_format($row['total_sold'],2).'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal bold" style="height:5px;padding:2px">$'.number_format($profit,2).'</td>

				</tr>';
				$i_i++;
				$iterator++;
				 $_rows = $db->func_query("SELECT sku,total_sold,qty_received,qty_sold,avg_cost,avg_price,current_qty,cron_date from inv_inout_report WHERE sku='".$row['sku']."'  AND $condition_sql    ORDER BY cron_date desc");
                                  			       
                                            foreach($_rows as $_row)
                                            {


                                            		if($i_i==$_b+1)
					{

						$_b = $_b + 58;
					$kk++;
					
					$item_html.='</table></td></tr></table></page>'.$header.$header3;
					

				}

					

				 $bottom_order = false; // by zaman
				if(($i_i==(($_b)) ) ) {
					$bottom_order = true;

				}

                                            	$profit = ($_row['avg_price']*$_row['qty_sold']) - ($_row['avg_cost']*$_row['qty_sold']);  

                                            	$item_html.='	
				<tr>
					<td class="'.($bottom_order?'':'no_border').' center normal " style="height:5px;padding:2px" >
						
					</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal  " style="height:5px;padding:2px">'.date('m/d/Y',strtotime($_row['cron_date'])).'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal  " style="height:5px;padding:2px">-</td>
					<td  class="'.($bottom_order?'':'no_border').' normal  " style="height:5px;padding:2px">-</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal  " style="height:5px;padding:2px">'.$_row['current_qty'].'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal " style="height:5px;padding:2px">'.$_row['qty_received'].'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal " style="height:5px;padding:2px">'.$_row['qty_sold'].'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal " style="height:5px;padding:2px">$'.number_format($_row['avg_cost'],2).'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal " style="height:5px;padding:2px">$'.number_format($_row['avg_price'],2).'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal " style="height:5px;padding:2px">$'.number_format($_row['total_sold'],2).'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal " style="height:5px;padding:2px">$'.number_format($profit,2).'</td>

				</tr>';
				
				$i_i++;
                                            }
			}
			
$header2='<tr>
	<td    >

		<table  cellpadding="0" cellspacing="1" border="0"   >
		<tr>
<td class="bold center" style="font-size:16px">PhonePartsUSA - QTY Report</td>
		</tr>
		<tr>
		
		<td class="center bold" style="font-size:13px;width:750px">
		<br>SKU: '.($sku?$sku:'All').'<br>From: '.date('m/d/Y',strtotime($start_date)).' To: '.date('m/d/Y',strtotime($end_date)).'</td>
		
		</tr>

		</table></td></tr>';

			$html=$html.$header.$header2.$header3.$item_html;
		
			$html.='
		
		</table>


	</td>

</tr>
';

$html.='
</table></page>

';
 // die($html);
try {



	$html2pdf = new HTML2PDF('P', 'A4', 'en');

	$html2pdf->setDefaultFont('arial');
	$html2pdf->writeHTML($html);

	$filename = 'Stock Inout Report-'.time();
	$file = $html2pdf->Output('files/' . $filename . '.pdf', 'F');


	
  } catch (HTML2PDF_exception $e) {
  	echo $e;
  	exit;
  }

 
	// echo "<script>window.location='" . $files.'/'.$filename . ".pdf'</script>";
	// exit;

header("Location: ".$host_path."files/".$filename . ".pdf");
?>