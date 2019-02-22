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
	if(strlen($string)>64)
	{
		$string = substr($string,0,64).'...';
	}
	return $string;
}

$sku = $db->func_escape_string($_REQUEST['sku']);
$store_type   = $db->func_escape_string($_REQUEST['store_type']);
$source   = $db->func_escape_string($_REQUEST['source']);
$return_code = $db->func_escape_string($_REQUEST['return_code']);
$decision = $db->func_escape_string($_REQUEST['decision']);
$item_condition = $db->func_escape_string($_REQUEST['item_condition']);
$start_date = $db->func_escape_string($_REQUEST['start_date']);
if (!$start_date) {
    $start_date = date('y-m-d', strtotime('-30 days'));
}
$end_date = $db->func_escape_string($_REQUEST['end_date']);
if (!$end_date) {
    $end_date = date('Y-m-d');
}
$items = $db->func_escape_string($_REQUEST['items']);
$rma_status = $db->func_escape_string($_REQUEST['rma_status']);
if(@$sku){
	$conditions[] =  " LCASE(b.sku)=LCASE('".$sku."') ";
}
if(@$items)
{
	$items = "'" . str_replace(",", "','", $items) . "'";
	$conditions[] = " b.sku IN (".$items.")";
}
if(@$store_type){
	$conditions[] =  " a.store_type='$store_type' ";
}
if(@$rma_status){
	$conditions[] =  " a.rma_status='$rma_status' ";
}
if(@$source){
	$conditions[] =  " a.source='".$source."' ";
}

if(@$return_code){
	$conditions[] =  " b.return_code='$return_code' ";
}

if(@$decision){
	$conditions[] =  " b.decision='$decision' ";
}

if(@$item_condition){
	$conditions[] =  " b.item_condition='$item_condition' ";
}
if(@$start_date && $end_date)
{
	$conditions[] =  " (a.date_added BETWEEN '$start_date' and '$end_date') ";
}

$condition_sql = implode(" AND " , $conditions);


if(!$condition_sql){
	$condition_sql = ' 1 = 1';
}

$inv_query = "SELECT b.sku,COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id  AND $condition_sql GROUP BY b.sku ORDER BY COUNT(b.sku) DESC";
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

					<td style="width:85px;padding:2px;text-align:center" class="no_border1 normal" >SKU</td>

					<td style="width:255px;padding:2px;text-align:center" class="no_border1 normal" >Item Name</td>
					<td style="width:60px;padding:2px;text-align:center" class="no_border1 normal" >Total</td>
					<td style="width:65px;padding:2px;text-align:center" class="no_border1 normal" >Awaiting</td>
					<td style="width:65px;padding:2px;text-align:center;" class="no_border1 normal"  >Completed</td>
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
				$total_awaiting = 0;
				$_b = 40;
				foreach($inv_orders as $row)
				{
					$item_array[] = $row['sku'];
					$exception += (int)$db->func_query_first_cell("SELECT COUNT(b.item_exception) as count_sku FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id  AND $condition_sql and b.item_exception=1 and b.sku='".$row['sku']."' GROUP BY b.sku");
					$item_issue += (int)$db->func_query_first_cell("SELECT COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id  AND $condition_sql and b.item_condition='Item Issue' and b.sku='".$row['sku']."' GROUP BY b.sku");
					$customer_damanged += (int)$db->func_query_first_cell("SELECT COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id  AND $condition_sql and b.item_condition='Customer Damage' and b.sku='".$row['sku']."' GROUP BY b.sku");
					$not_tested += (int)$db->func_query_first_cell("SELECT COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id  AND $condition_sql and b.item_condition='Not Tested' and b.sku='".$row['sku']."' GROUP BY b.sku");
					$not_ppusa += (int)$db->func_query_first_cell("SELECT COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id  AND $condition_sql and b.item_condition='Not PPUSA Part' and b.sku='".$row['sku']."' GROUP BY b.sku");
					$over_60 += (int)$db->func_query_first_cell("SELECT COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id  AND $condition_sql and b.item_condition='Over 60 days' and b.sku='".$row['sku']."' GROUP BY b.sku");
					$shipping_damange += (int)$db->func_query_first_cell("SELECT COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id  AND $condition_sql and b.item_condition='Shipping Damage' and b.sku='".$row['sku']."' GROUP BY b.sku");

					$awaiting = $db->func_query_first_cell("SELECT COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id ".($condition_sql?' AND ':'')."  $condition_sql AND b.sku='".$row['sku']."' AND a.rma_status='Awaiting'");

					$total_awaiting+=$awaiting;

				//if($i_i%$_b==0) {
					if($i_i==$_b+1)
					{
						$_b = $_b + 50;
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

				 	<td  class="'.($bottom_order?'':'no_border').' center normal " style="height:5px;padding:2px">'.($row['sku']).'</td>
				 	<td  class="'.($bottom_order?'':'no_border').' normal " style="height:5px;padding:2px">'.trimName(secureItemName(getItemName($row['sku']))).'</td>
				 	<td  class="'.($bottom_order?'':'no_border').' center normal " style="height:5px;padding:2px">'.$row['count_sku'].'</td>
				 	<td  class="'.($bottom_order?'':'no_border').' center normal " style="height:5px;padding:2px">'.$awaiting.'</td>
				 	<td  class="'.($bottom_order?'':'no_border').' center normal " style="height:5px;padding:2px">'.$db->func_query_first_cell("SELECT COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id ".($condition_sql?' AND ':'')."  $condition_sql AND b.sku='".$row['sku']."' AND a.rma_status='Completed'").'</td>
				 </tr>';
				 $i_i++;
				}

				$header2='
				<tr>
					<td align="center" border="0">
						<table  cellpadding="0" cellspacing="1" border="0"  align="center"  width=""   >
							<tr>
								<td  style="font-size:20px;" align="center" >Detailed Return Item Report</td>
							</tr>
							<tr>
								<td class="normal">Range: '.($start_date&&$end_date?americanDate($start_date).' - '.americanDate($end_date):'From Beginning').'</td>
							</tr>
							<tr>
								<td class="normal"></td>
							</tr>
							<tr>
								<td class="normal">Returned Items: '.count($inv_orders).'</td>
							</tr>
							<tr>
								<td class="normal">Unique Returns: '.count(array_unique($item_array)).'</td>
							</tr>
							<tr>
								<td class="normal">Awaitings: '.$total_awaiting.'</td>
							</tr>
							<tr>
								<td class="normal">Return Exception: '.$exception.'</td>
							</tr>
							<tr>
								<td class="normal">Item Issue: '.$item_issue.'</td>
							</tr>
							<tr>
								<td class="normal">Customer Damaged: '.$customer_damanged.'</td>
							</tr>
							<tr>
								<td class="normal">Not Tested: '.$not_tested.'</td>
							</tr>
							<tr>
								<td class="normal">Not PPUSA Part: '.$not_ppusa.'</td>
							</tr>
							<tr>
								<td class="normal">Over 60 Days: '.$over_60.'</td>
							</tr>
							<tr>
								<td class="normal">Shipping Damage: '.$shipping_damange.'</td>
							</tr>

						</table>
						<br>
					</td>
				</tr>';
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

	$items = $db->func_query("SELECT b.*,a.order_id FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id  AND $condition_sql and a.rma_status='Completed' and b.sku='".$row['sku']."' order by a.date_added DESC");
	if($items)
	{
		$html.='
		<h2 style="font-size:12px">'.$row['sku'].' '.secureItemName(getItemName($row['sku'])).'</h2>
		<table  cellpadding="0" cellspacing="1" border="0" class="item_table"     >
			<tr style="background-color:#3C3D3A;color:#fff;">
				<td style="width:80px;height:8px;padding:2px;text-align:center" class="no_border1 normal" >Order Date</td>
				<td style="width:70px;height:8px;padding:2px;text-align:center" class="no_border1 normal">Order ID</td>
				<td style="width:30px;height:8px;padding:2px;text-align:center" class="no_border1 normal">R.R</td>
				<td style="width:60px;height:8px;padding:2px;text-align:center" class="no_border1 normal">How to Process</td>
				<td style="width:80px;height:8px;padding:2px;text-align:center" class="no_border1 normal">Condition</td>
				<td style="width:80px;height:8px;padding:2px;text-align:center" class="no_border1 normal">Decision</td>
				<td style="width:80px;height:8px;padding:2px;text-align:center" class="no_border1 normal">Vendor</td>

				<td style="width:160px;height:8px;padding:2px;text-align:center" class="no_border1 normal">Comments</td>
			</tr>';
			$iterator = 0;
			foreach($items as $item)
			{
				if($decision = $item['decision']=='')
				{
					$decision =  $db->func_query_first_cell("SELECT action FROM inv_return_decision WHERE return_id='".$item['return_id']."' and sku='".$row['sku']."'");
					;
				}
				else
				{
					$decision = $item['decision'];
				}
				if ($item['rtv_vendor_id']) {
					$vendor = $db->func_query_first_cell("SELECT name  from inv_users where id = '".$item['rtv_vendor_id']."'");
				} else {
					$default_vendor_id = $db->func_query_first_cell("SELECT vendor FROM inv_product_vendors WHERE product_sku='".$row['sku']."' limit 1");
					$vendor = $db->func_query_first_cell("SELECT name from inv_users where id = '".$default_vendor_id."'");
				}
				$html.='<tr>
				<td class=" center normal " style="height:5px;padding:2px">'.americanDate($db->func_query_first_cell("SELECT order_date FROM inv_orders WHERE order_id='".$item['order_id']."'")).'</td>
				<td class=" center normal " style="height:5px;padding:2px">'.$item['order_id'].'</td>
				<td class=" center normal " style="height:5px;padding:2px">'.substr($item['return_code'], 0,2).'</td>
				<td class=" center normal " style="height:5px;padding:2px">'.$item['how_to_process'].'</td>
				<td class=" center normal " style="height:5px;padding:2px">'.$item['item_condition'].'</td>
				<td class=" center normal " style="height:5px;padding:2px">'.$decision.($item['item_exception']?'<br>Exception was made':'').'</td>
				<td class=" center normal " style="height:5px;padding:2px">'.$vendor.'</td>
				<td class=" center normal " style="height:5px;padding:2px">'.($item['comment']).'</td>
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
	$html2pdf = new HTML2PDF('P', array(215.9,279.4), 'en');
	$html2pdf->setDefaultFont('arial');
	$html2pdf->writeHTML($html);
	$filename = 'Return Report Summary-'.time();
	$file = $html2pdf->Output('files/' . $filename . '.pdf', 'F');
	
} catch (HTML2PDF_exception $e) {
	echo $e;
	exit;
}
header("Location: ".$host_path."files/".$filename . ".pdf");
?>