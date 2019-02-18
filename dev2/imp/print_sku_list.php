<?php
require_once("auth.php");
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';

if ($_GET['print'] == 1) {

	$date = date("Y-m-d", strtotime( '-1 days' ) );
	//echo $_SERVER['DOCUMENT_ROOT']."/price_report/PriceUpdateReport-".$date.".csv";exit;
	if (file_exists($_SERVER['DOCUMENT_ROOT']."/price_report/PriceUpdateReport-".$date.".csv")){
		$logo =  "https://phonepartsusa.com/image/" . oc_config("config_logo");
		$html = '<div align="center"><h3 align="center"><img src="' . $logo . '"><br>Updated Prices SKU listing<br>'.americanDate($date,false).'</h3><br><br>';
		$html .= '<table cellpadding="10" cellspacing="0" border="1" align="center" >
		<thead>
		<tr>
		<th style="padding:5px 5px 5px 5px;" align="center">SKU</th>
		<th style="padding:5px 5px 5px 5px;" align="center">Item Name</th>
		<th style="padding:5px 5px 5px 5px;" align="center">Previous Price</th>
		<th style="padding:5px 5px 5px 5px;" align="center">Current Price</th>
		</tr>
		</thead><tbody>';
		$filename = $_SERVER['DOCUMENT_ROOT']."/price_report/PriceUpdateReport-".$date.".csv";
		$handle   = fopen("$filename", "r");
		while ($data = fgetcsv($handle,1000,",","'")) {
			$check = $db->func_query_first( "select * from oc_product where is_print = '1' AND sku = '".$data[2]."'" );
			if ($check) {
				$print = true;
				if ($data[2] != 'SKU') {
					$data[3] = str_replace('"', '', $data[3]);
					$html .= '<tr>
					<td style="padding:5px 5px 5px 5px;" align="center">'.$data[2].'</td>
					<td style="padding:5px 5px 5px 5px;" align="center">'.$data[3].'</td>
					<td style="padding:5px 5px 5px 5px;" align="center">'.$data[5].'</td>
					<td style="padding:5px 5px 5px 5px;" align="center">'.$data[6].'</td>
					</tr>';	
			}

			}
		}
		$html .= '</tbody></table></div>';  

	}
	$data = array();
	$data['html'] = $html;
	if ($print) {
		//$file  = createPdf($data,$_SERVER['DOCUMENT_ROOT']."/price_report/",true);
		//$pdf = createPdf(array('html' => $html, 'font' => 'arial', 'orientation' => 'L', 'pageWidth' => 2*75.4, 'pageHeight' => 25.4), 'price_report/',true);
		$pdf = createPdf(array('html' => $html, 'orientation' => 'L', 'pageWidth' => '210', 'pageHeight' => '297', 'margin' => array('10px', '0', '10px', '0')), 'price_report/', true);
		printNodePDF($pdf,'Price Change SKU List',QC1_PRINTER);
		echo $pdf;exit;
		
	}

}
if ($_POST['reset']) {
	$db->db_exec ( "update oc_product SET is_print = 0 " );
}


if(isset($_POST['sku']) and count($_POST['sku'])>0)
{
	$db->db_exec ( "update oc_product SET is_print = 0 " );
	foreach($_POST['sku'] as $sku)
	{	
		$db->db_exec ( "update oc_product SET is_print = 1 where sku = '$sku'" );
	}
	
}
$skus = $db->func_query ( "select * from oc_product where is_print = '1'" );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	 <title>Printable Sku List</title>
	 <style type="text/css">
	 	table td{text-align:center;}
	 </style>
	 
	 <script type="text/javascript" src="js/jquery.min.js"></script>
     
	
	
     <link rel="stylesheet" type="text/css" href="include/jquery.autocomplete.css" media="screen" />
	 
  </head>
  <body>
		<div align="center"> 
		   <?php include_once 'inc/header.php';?>
		</div>
		
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		
		
		<br />
        <div class="search" align="center">
		 		 SKU: 
		 		 <input type="text" id="autocomplete" value="<?php echo $keyword;?>"  /><div style="float:left" id="loader"></div>		 	
		</div>
		<br />
		 <form method="post" >
		<table id="data_table"  border="1" style="border-collapse:collapse;clear:both" width="80%" align="center" cellpadding="3">
			<tr style="background:#e5e5e5;">
			    
			    <th width="450px">Item</th>
			    <th width="100px">Action</th>
			</tr>
			<?php foreach ($skus as $sku) { ?>
			<tr>
				<td><?php echo $sku['sku']; ?> - <?php echo getItemName($sku['sku']); ?></td>
				<td>
				<img style='cursor:pointer' onClick='$(this).parent().parent().remove();' src='images/cross.png'>
				<input type='hidden' value="<?php echo $sku['sku']; ?>" name='sku[]'>
				</td>
			</tr>
			<?php } ?>
			
		</table>
        
		<br />       
        <div align="center" >
        <input type="submit" class="button" name="reset" value="Add SKU" /> 
        
        </div>
        <input type="hidden" id="customer_group_id" name="customer_group_id" value="-1">
        </form>
    </body>
</html>
<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
<script>
$(document).ready(function(e) {

$('#autocomplete').autocomplete({
    serviceUrl: 'popupfiles/search_products.php',
	onSearchStart: function(){
		$('.loading').remove();
		$('#loader').after('<img src="images/loading.gif" height="42" width="42" class="loading">');
	},
	onSearchComplete: function(){
		$('.loading').remove();
	},
    onSelect: function (suggestion) {
		html="";
		html+="<tr><td>"+(suggestion.value)+"</td><td><img style='cursor:pointer' onClick='$(this).parent().parent().remove();' src='images/cross.png'><input type='hidden' name='sku[]' value='"+suggestion.data+"'></td></tr>";
       $('#data_table').append(html);
	   $('#autocomplete').val('');
	      }
});

    
});
</script>	 	
