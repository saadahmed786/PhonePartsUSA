<?php
require_once("auth.php");
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';
$types = array(
	'ItemIssueBox' => 'Item Issue Box',
	'NotTestedBox' => 'Not Tested Box',
	'GFSBox' => 'Good for Stock',
	'CIB' => 'Customer Damage',
	'SDBox' => 'Shipping Damage',
	'NTRBox' => 'Need To Repair',
	'rejected_items' => 'Rejects'
	);
if ($_POST['create']) {
	$gfs = $db->func_escape_string(trim($_POST['gfs_value']));
	$storefront = $db->func_escape_string(trim($_POST['storefront_value']));
	if ($storefront == '1') {
		$store_where = "AND si.source = 'storefront' ";
	} else{
		$store_where = '';
	}
	$start = $db->func_escape_string(trim($_POST['start']));
	$end = $db->func_escape_string(trim($_POST['end']));
	$head_start_date = date('m/d/Y',strtotime($_POST['start']));
	$head_end_date = date('m/d/Y',strtotime($_POST['end']));
	$today = date('m/d/Y');
	$_query = "SELECT si . *, s.box_number, s.box_type from inv_return_shipment_box_items si inner join inv_return_shipment_boxes s ON (s.id = si.return_shipment_box_id) WHERE date(si.date_added) >= '$start' $store_where AND date(si.date_added) <= '$end' AND box_type = 'ItemIssueBox' order by `source` DESC, si.date_added DESC";

	$_query1 = "SELECT si . *, s.box_number, s.box_type from inv_return_shipment_box_items si inner join inv_return_shipment_boxes s ON (s.id = si.return_shipment_box_id) WHERE date(si.date_added) >= '$start' $store_where AND date(si.date_added) <= '$end' AND box_type = 'ItemIssueBox' order by `source` DESC, si.date_added DESC";

	$ItemIssueBox = $db->func_query($_query);
	$_query = "SELECT si . *, s.box_number, s.box_type from inv_return_shipment_box_items si inner join inv_return_shipment_boxes s ON (s.id = si.return_shipment_box_id) WHERE date(si.date_added) >= '$start' $store_where AND date(si.date_added) <= '$end' AND box_type = 'NotTestedBox' order by `source` DESC, si.date_added DESC";
	$NotTestedBox = $db->func_query($_query);
	if($gfs=='0'){
		$_query = "SELECT si . *, s.box_number, s.box_type from inv_return_shipment_box_items si inner join inv_return_shipment_boxes s ON (s.id = si.return_shipment_box_id) WHERE date(si.date_added) >= '$start' $store_where AND date(si.date_added) <= '$end' AND box_type = 'GFSBox' order by `source` DESC, si.date_added DESC";
		$GFSBox = $db->func_query($_query);
	}
	$_query = "SELECT si . *, s.box_number, s.box_type from inv_return_shipment_box_items si inner join inv_return_shipment_boxes s ON (s.id = si.return_shipment_box_id) WHERE date(si.date_added) >= '$start' $store_where AND date(si.date_added) <= '$end' AND box_type = 'NTRBox' order by `source` DESC, si.date_added DESC";
	$NTRBox = $db->func_query($_query);
	$_query = "SELECT si . *, s.box_number, s.box_type from inv_return_shipment_box_items si inner join inv_return_shipment_boxes s ON (s.id = si.return_shipment_box_id) WHERE date(si.date_added) >= '$start' $store_where AND date(si.date_added) <= '$end' AND box_type = 'CIB' order by `source` DESC, si.date_added DESC";
	$CIB = $db->func_query($_query);
	$_query = "SELECT si . *, s.box_number, s.box_type from inv_return_shipment_box_items si inner join inv_return_shipment_boxes s ON (s.id = si.return_shipment_box_id) WHERE date(si.date_added) >= '$start' $store_where AND date(si.date_added) <= '$end' AND box_type = 'SDBox' order by `source` DESC, si.date_added DESC";
	$SDBox = $db->func_query($_query);
	$_query = "SELECT si.* , irs.package_number FROM inv_rejected_shipment_items si INNER JOIN inv_rejected_shipments AS irs ON (si.rejected_shipment_id = irs.id) WHERE DATE(si.date_added) >= '$start' AND DATE(si.date_added) <= '$end' AND si.deleted=0 ORDER BY si.date_added DESC";
	$rejected_items = $db->func_query($_query);
	if($gfs=='0'){
	$productsData = array('ItemIssueBox' => $ItemIssueBox, 'NotTestedBox' => $NotTestedBox, 'GFSBox' => $GFSBox, 'CIB' => $CIB, 'SDBox' => $SDBox, 'NTRBox' => $NTRBox, 'rejected_items' => $rejected_items );
	} else{
		$productsData = array('ItemIssueBox' => $ItemIssueBox, 'NotTestedBox' => $NotTestedBox, 'CIB' => $CIB, 'SDBox' => $SDBox, 'NTRBox' => $NTRBox, 'rejected_items' => $rejected_items );
	}
	// $productsData = array();
	$height = 10;
	$html .= '<style>';
	$html .= 'page{width: 1122px; height:793px; font-family: courier; }';
	$html .= '.container{padding: 10px 0px 0px 25px;}';
	$html .= '.head{font-size: 12px;}';
	$html .= '.data{font-size: 10px;}';
	$html .= 'table{border-collapse: collapse;}';
	$html .= 'td, th {width: 80px; border: 1px solid #000;padding: 0px 2px;}';
	$html .= '.return_id {width: 140px;}';
	$html .= '.small {width: 60px;}';
	$html .= '.item_vendor {width: 80px;}';
	$html .= '.order {width: 120px;}';
	$html .= '.title {width: 215px;}';
	$html .= '.reject .title {width: 215px;}';
	$html .= 'h4{margin: 10px 0px;}';
	$html .= '</style>';

	$html .= '<page>';
	$html .= '<div class="container">';
	$html .= '<div align="center">';
	$html .= $head_start_date.' through '.$head_end_date.', Generated: '.$today;
	$html .= '</div>';
	foreach ($productsData as $key => $products) {
		if ($height >= 735) {
			$html .= '<div align="center">';
			$html .= $head_start_date.' through '.$head_end_date.', Generated: '.$today;
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<page_footer>';
			$html .= '<div align="center">';
			$html .= 'Page [[page_cu]] of [[page_nb]]';
			$html .= '</div>';
			$html .= '</page_footer>';
			$html .= '</page>';
			$html .= '<page>';
			$html .= '<div class="container">';
			$height = 10;
		}
		$class = 'table';
		/*if ($key == 'rejected_items') {			
			$class = 'table reject';
			$html .= '</div>';
			$html .= '<page_footer>';
			$html .= '<div align="center">';
			$html .= 'Page [[page_cu]] of [[page_nb]]';
			$html .= '</div>';
			$html .= '</page_footer>';
			$html .= '</page>';
			$html .= '<page>';
			$html .= '<div class="container">';
			$height = 10;
		}
		if ($key == 'NTRBox') {			
			$class = 'table ntr';
			$html .= '</div>';
			$html .= '<page_footer>';
			$html .= '<div align="center">';
			$html .= 'Page [[page_cu]] of [[page_nb]]';
			$html .= '</div>';
			$html .= '</page_footer>';
			$html .= '</page>';
			$html .= '<page>';
			$html .= '<div class="container">';
			$height = 10;
		}*/
		$tableHead = '<tr class="head"> '. (($key != 'rejected_items' && $key != 'ItemIssueBox' && $key != 'GFSBox')? '<th class="small">Source</th>': '') .'' . (($key == 'rejected_items' || $key == 'ItemIssueBox' || $key == 'GFSBox' )? '<th class="item_vendor">Item Vendor</th>': '') . ' <th class="order">Order/Shipment ID</th> <th class="small">RMA</th> <th class="return_id">Return ID</th> <th>SKU</th> <th class="title">Title</th> <th class="return_id">Reason</th> <th>Released</th> <th>Received</th> </tr>';
		$html .= '<h4>'. $types[$key] .': '. count($products) .'</h4>';
		$height += 45;
		$html .= '<table class="'. $class .'">';
		$html .= $tableHead;
		$height += 17;
		foreach ($products as $it) {
			
				

				$name = $db->func_query_first_cell("SELECT `name` FROM oc_product_description a, oc_product b WHERE a.product_id = b.product_id AND sku = '". $it['product_sku'] ."'");
				if ($it['shipment_id']) {
					$rtv_ship_id=$db->func_query_first_cell("SELECT `rejected_shipment_id` FROM inv_rejected_shipment_items WHERE shipment_id = '". $it['shipment_id'] ."'");
					$vendor_id = $db->func_query_first_cell("SELECT `vendor` FROM inv_rejected_shipments WHERE id = '". $rtv_ship_id ."'");
					if ($vendor_id) {	
						$vendor=get_username($vendor_id);
					} else {	
						$vendor = 'N/A';
					}
				} else{
					$vendor = 'N/A';
				}
				if($it['source']!='')
				{
					$it['source']='('.$it['source'].')';
				}

			//$vendor = $db->func_query_first_cell("SELECT `vendor` FROM oc_product a, oc_product_description b WHERE a.product_id = b.product_id AND sku = '". $it['product_sku'] ."'");
				if ($key == 'rejected_items') {
					$it['return_item_id'] = $it['reject_item_id'];
					$it['reason'] = $db->func_query_first_cell( "SELECT name from inv_rj_reasons where id = '". $it['reject_reason'] ."'" );
					if ($it['rma_number']) {
					$it['source'] = $db->func_query_first_cell( "SELECT source from inv_returns where rma_number = '". $it['rma_number'] ."'" );
					$it['source']='('.$it['source'].')';	
					}
				}
				$name = str_split($name, ((int)(strlen($name) / 3)) + 1);
				$it['name'] = implode('<br>', $name);
				$it['return_item_id'] = str_split($it['return_item_id'], ((int)(strlen($it['return_item_id']) / 2)+1));
				$it['return_item_id'] = implode('<br>', $it['return_item_id']);
				$html .= '<tr class="data"> '. (($key != 'rejected_items' && $key != 'ItemIssueBox' && $key != 'GFSBox')? '<td class="small">'. $it['source'] .'</td>': '') .''. (($key == 'rejected_items' || $key=='ItemIssueBox' || $key=='GFSBox')? '<td class="small">'.$vendor.'<br><small>'.$it['source'].'</small></td>': '') . ' <td class="order">'. (($it['shipment_id'])? $db->func_query_first_cell("SELECT package_number FROM inv_shipments WHERE id = '". $it['shipment_id'] ."'"): (($it['order_id']) ? $it['order_id']: 'N/A')) .'</td> <td class="small">'. (($it['rma_number']) ? $it['rma_number']: 'N/A') .'</td> <td class="return_id">'. $it['return_item_id'] .'</td> <td>'. $it['product_sku'] .'</td> <td class="title">'. $it['name'] .'</td> <td class="return_id">'. $it['reason'] .'</td> <td>&nbsp;</td> <td>&nbsp;</td> </tr>';
				$height += 37;
				if ($height >= 735) {
					$html .= '</table>';
					$html .= '</div>';
					$html .= '<page_footer>';
					$html .= '<div align="center">';
					$html .= 'Page [[page_cu]] of [[page_nb]]';
					$html .= '</div>';
					$html .= '</page_footer>';
					$html .= '</page>';
					$html .= '<page>';
					$html .= '<div class="container">';
					$html .= '<table class="table">';
					$html .= $tableHead;
					$height = 10;
				}

			// if ($height >= 735) {
			// 	$html .= '</table>';
			// 	$html .= '</div>';
			// 	$html .= '<page_footer>';
			// 	$html .= '<div align="center">';
			// 	$html .= 'Page [[page_cu]] of [[page_nb]]';
			// 	$html .= '</div>';
			// 	$html .= '</page_footer>';
			// 	$html .= '</page>';
			// 	$html .= '<page>';
			// 	$html .= '<div class="container">';
			// 	$html .= '<table class="table">';
			// 	$html .= $tableHead;
			// 	$height = 10;
			// }
			
		}
		$html .= '</table>';
	}
	$html .= '</div>';
	if ($height < 735 && $height > 10) {
		$html .= '<page_footer>';
		$html .= '<div align="center">';
		$html .= 'Page [[page_cu]] of [[page_nb]]';
		$html .= '</div>';
		$html .= '</page_footer>';
		$height = 10;
	}	
	$html .= '</page>';
	$pdf = createPdf(array('html' => $html, 'orientation' => 'L', 'pageWidth' => '210', 'pageHeight' => '297', 'margin' => array('10px', '0', '10px', '0')), 'files/', true);
	header("Location: $host_path$pdf");

}