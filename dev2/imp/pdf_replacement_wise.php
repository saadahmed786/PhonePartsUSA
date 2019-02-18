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

	if(strlen($string)>50)
	{
		$string = substr($string,0,50).'...';
	}
	return $string;

}

$sku = $db->func_escape_string($_REQUEST['sku']);
    
		$start_date = $db->func_escape_string($_REQUEST['start_date']);
    $end_date = $db->func_escape_string($_REQUEST['end_date']);

    if(!$start_date && !$end_date)
    {
    		$start_date = date('Y')."-".date('m')."-01";
$end_date = date('Y')."-".date('m')."-".date('t');
    }
        

       if(@$sku){
            $conditions[] =  " LCASE(b.product_sku)=LCASE('".$sku."') ";
        }

        if(@$start_date && $end_date)
        {
          $conditions[] =  " (a.order_date BETWEEN '$start_date' and '$end_date') ";
        }

        
            $condition_sql = implode(" AND " , $conditions);
        
        
        if(!$condition_sql){
            $condition_sql = " (a.order_date BETWEEN '$start_date' and '$end_date') ";

        }
        
            $condition_sql = implode(" AND " , $conditions);
        
        
      if(!$condition_sql){
            $condition_sql = " (a.order_date BETWEEN '$start_date' and '$end_date') ";

        }
        
        $inv_query = "SELECT b.product_sku,COUNT(b.product_sku) as count_sku,sum(b.product_price) as product_price,sum(b.product_true_cost*b.product_qty) as cost FROM inv_orders a,inv_orders_items b WHERE a.order_id=b.order_id  and lower(a.payment_source)='replacement'  AND $condition_sql  GROUP BY b.product_sku ORDER BY COUNT(b.product_sku) DESC";

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
				<td style="width:35px;height:7px;padding:5px;text-align:center" class="no_border1 normal" >#</td>
				
				<td style="width:85px;padding:2px;text-align:center" class="no_border1 normal" >Item SKU</td>
					<td style="width:180px;padding:2px;text-align:center" class="no_border1 normal" >Item Name</td>
				<td style="width:65px;padding:2px;text-align:center" class="no_border1 normal" ># of Replacements</td>
				<td style="width:60px;padding:2px;text-align:center" class="no_border1 normal" >Amt Replaced</td>
				<td style="width:65px;padding:2px;text-align:center" class="no_border1 normal" >Cost</td>
				<td style="width:65px;padding:2px;text-align:center;" class="no_border1 normal"  >Shipping Cost</td>

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
			foreach($inv_orders as $row)
			{
				$item_array[] = $row['product_sku'];

				$orders = $db->func_query("SELECT a.order_id FROM inv_orders a,inv_orders_items b WHERE a.order_id=b.order_id  and lower(a.payment_source)='replacement' and b.product_sku='".$row['product_sku']."'  AND $condition_sql  GROUP BY a.order_id ");
                              $shipping_cost = 0.00;
                              $_order_ids = '';
                              foreach($orders as $order)
                              {
                                $shipping_cost += $db->func_query_first_cell("SELECT sum(shipping_cost)+sum(insurance_cost) FROM inv_shipstation_transactions WHERE order_id='".$order['order_id']."'");
                              //  $_order_ids.=$order['order_id'].',';
                              }
                           //   $_order_ids = rtrim($_order_ids,',');
				
				

				//if($i_i%$_b==0) {
					if($i_i==$_b+1)
					{

						$_b = $_b + 65;
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

				
				$item_html.='	
				<tr>
					<td class="'.($bottom_order?'':'no_border').' center normal" style="height:5px;padding:2px" >
						'.$i_i.'
					</td>
					
					<td  class="'.($bottom_order?'':'no_border').' center normal " style="height:5px;padding:2px">'.($row['product_sku']).'</td>
					<td  class="'.($bottom_order?'':'no_border').' normal " style="height:5px;padding:2px">'.trimName(secureItemName(getItemName($row['product_sku']))).'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal " style="height:5px;padding:2px">'.$row['count_sku'].'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal " style="height:5px;padding:2px">$'.number_format($row['product_price'],2).'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal " style="height:5px;padding:2px">$'.number_format($row['cost'],2).'</td>
					<td  class="'.($bottom_order?'':'no_border').' center normal " style="height:5px;padding:2px">$'.number_format($shipping_cost,2).'</td>

				</tr>';
				$i_i++;
			}
			
$header2='';

			$html=$html.$header.$header2.$header3.$item_html;
		
			$html.='
		
		</table>


	</td>

</tr>
';

$html.='
</table></page>

';
foreach($inv_orders as $row)
{
 
	$items = $db->func_query("SELECT a.order_id,a.order_date FROM inv_orders a,inv_orders_items b WHERE a.order_id=b.order_id  and lower(a.payment_source)='replacement' and b.product_sku='".$row['product_sku']."'  AND $condition_sql  GROUP BY a.order_id ");
                             
	if($items)
	{
	$html.='
	<h2 style="font-size:12px">'.$row['product_sku'].' '.secureItemName(getItemName($row['product_sku'])).'</h2>
		<table  cellpadding="0" cellspacing="1" border="0" class="item_table"     >
			<tr style="background-color:#3C3D3A;color:#fff;">
				<td style="width:130px;height:8px;padding:2px;text-align:center" class="no_border1 normal" >Customer Name</td>
	<td style="width:90px;height:8px;padding:2px;text-align:center" class="no_border1 normal">Order Date</td>
	<td style="width:90px;height:8px;padding:2px;text-align:center" class="no_border1 normal">Order ID</td>
	<td style="width:150px;height:8px;padding:2px;text-align:center" class="no_border1 normal">Amt Replacement</td>
	<td style="width:60px;height:8px;padding:2px;text-align:center" class="no_border1 normal">Cost</td>
	<td style="width:120px;height:8px;padding:2px;text-align:center" class="no_border1 normal">Shipping Cost</td>
	
	</tr>';

$iterator = 0;
foreach($items as $item)
{
	$shipping_cost = 0.00;

	$shipping_cost += $db->func_query_first_cell("SELECT sum(shipping_cost)+sum(insurance_cost) FROM inv_shipstation_transactions WHERE order_id='".$item['order_id']."'");
	$customer_name = $db->func_query_first_cell("SELECT concat(first_name,' ',last_name) FROM inv_orders_details WHERE order_id='".$item['order_id']."'");
	$amount_replacement = $db->func_query_first_cell("SELECT SUM(product_price) FROM inv_orders_items WHERE order_id='".$item['order_id']."' and product_sku='".$row['product_sku']."'");
	$cost = $db->func_query_first_cell("SELECT SUM(product_true_cost) FROM inv_orders_items WHERE order_id='".$item['order_id']."' and product_sku='".$row['product_sku']."'");
	$html.='<tr>
	<td class=" center normal " style="height:5px;padding:2px">'.$customer_name.'</td>
	<td class=" center normal " style="height:5px;padding:2px">'.americanDate($item['order_date']).'</td>
	<td class=" center normal " style="height:5px;padding:2px">'.$item['order_id'].'</td>
	<td class=" center normal " style="height:5px;padding:2px">$'.number_format($amount_replacement,2).'</td>
	<td class=" center normal " style="height:5px;padding:2px">$'.number_format($cost,2).'</td>
	<td class=" center normal " style="height:5px;padding:2px">$'.number_format($shipping_cost,2).'</td>
	
	</tr>';
	$i++;
	$iterator++;
}

	$html.='</table>
<br>';
}
}
//die($html);
try {



	$html2pdf = new HTML2PDF('P', 'A4', 'en');

	$html2pdf->setDefaultFont('arial');
	$html2pdf->writeHTML($html);

	$filename = 'Replacement Report Summary-'.time();
	$file = $html2pdf->Output('files/' . $filename . '.pdf', 'F');


	
  } catch (HTML2PDF_exception $e) {
  	echo $e;
  	exit;
  }

 
	// echo "<script>window.location='" . $files.'/'.$filename . ".pdf'</script>";
	// exit;

header("Location: ".$host_path."files/".$filename . ".pdf");
?>