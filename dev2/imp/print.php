<?php



require_once("auth.php");

include_once 'inc/Barcode.php';

include_once 'inc/functions.php';





$shipment_id = $db->func_escape_string($_GET['shipment_id']);

$shipment_detail = array();

if($shipment_id){

	$shipment_detail = $db->func_query_first("select * from inv_shipments where id = '$shipment_id'");

	

	$shipment_query  = "select sq.* , si.product_sku , si.cu_po , si.qty_received,si.is_new

	from inv_shipment_items si left join inv_shipment_qc sq on 

	(si.product_sku = sq.product_sku and si.shipment_id = sq.shipment_id) 

	where si.shipment_id = '$shipment_id' and si.product_sku != '' order by si.product_sku ASC";

	

	$shipment_result = $db->func_query($shipment_query);



	$shipment_items = array();

	

	foreach($shipment_result as $index => $shipment_item){

		$qty_received = $shipment_item['qty_received'];



		if($shipment_item['grade_a'] AND $shipment_item['grade_a_qty']){

			$shipment_items[] = array(

				'product_sku' => $shipment_item['grade_a'] ,

				'cu_po' => $shipment_item['cu_po'],

				'qty_received' => $shipment_item['grade_a_qty'] ,

				'item_name' => getItemName($shipment_item['grade_a']) ,

				'qty_rejected' => ($shipment_item['rejected']) 

				);



			$qty_received -= $shipment_item['grade_a_qty'];

		}



		if($shipment_item['grade_b'] AND $shipment_item['grade_b_qty']){

			$shipment_items[] = array(

				'product_sku' => $shipment_item['grade_b'] ,

				'cu_po' => $shipment_item['cu_po'],

				'qty_received' => $shipment_item['grade_b_qty'] ,

				'item_name' => getItemName($shipment_item['grade_b']) ,

				'qty_rejected' => ($shipment_item['rejected']) 

				);



			$qty_received -= $shipment_item['grade_b_qty'];

		}



		if($shipment_item['grade_c'] AND $shipment_item['grade_c_qty']){

			$shipment_items[] = array(

				'product_sku' => $shipment_item['grade_c'] ,

				'cu_po' => $shipment_item['cu_po'],

				'qty_received' => $shipment_item['grade_c_qty'] ,

				'item_name' => getItemName($shipment_item['grade_c']) ,

				'qty_rejected' => ($shipment_item['rejected']) 

				);



			$qty_received -= $shipment_item['grade_c_qty'];

		}



		$qty_received -= $shipment_item['rejected'];

		$qty_received -= $shipment_item['ntr'];



		$shipment_items[] = array(

			'product_sku' => $shipment_item['product_sku'] ,

			'cu_po' => $shipment_item['cu_po'],

			'qty_received' => $qty_received ,

			'is_new'=>$shipment_item['is_new'],

			'item_name' => getItemName($shipment_item['product_sku']) ,

			'qty_rejected' => ($shipment_item['rejected']),

			'qty_ntr' => ($shipment_item['ntr']) 

			);

	}

}

$RTV_ship_id = $db->func_query_first_cell("SELECT rejected_shipment_id from inv_rejected_shipment_items where shipment_id = '$shipment_id'");

$RTV_ship_name = $db->func_query_first("SELECT * from inv_rejected_shipments where id = '$RTV_ship_id'");

$Barcode = new Barcode();

$Barcode->setType('C128');

$Barcode->setSize(60,140);

$Barcode->hideCodeType();

?>

<html>

<head>

	<title>Print Shipment</title>

	<style>

		* { font-family: Verdana, Geneva, sans-serif; font-size:12px; }

	</style>

	<link href="<?php echo $host_path?>/include/style.css" rel="stylesheet" type="text/css" />

	<style type="text/css" media="print">

		a{display:none;}

		table {border-collapse: collapse;border-width:1px;}

	</style>


</head>

<body>

	<center>

		<br />



		<table border="0" width="816px" cellpadding="10" cellspacing="0">

			<tr>

				<td>Purchase Order #: </td>

				<td>

					<?php 

					$code = $shipment_detail['package_number'];

					$Barcode->setCode($code); 

					$file = 'images/barcode/'.$code.'.png';

					$Barcode->writeBarcodeFile($file);

					?>

					<!-- <img id="barcode" src="" alt="<?php echo $code;?>" /> -->
					<div id="barcode"></div>
				</td>

				<td></td>

				<td>Vendor: </td>

				<?php if($shipment_detail['vendor']!=0){ ?>

				<td><?php echo get_username($shipment_detail['vendor']);?></td>

				<?php } else { ?>

				<td>N/A</td>

				<?php } ?>

				<td></td>

			</tr>

			<tr>

				<td>Issue Date: </td>

				<td><?php echo americanDate($shipment_detail['date_issued']);?></td>

				<td></td>

				<td>RTV Shipment: </td>

				<?php if($RTV_ship_name['package_number']){ ?>

				<td><?php echo $RTV_ship_name['package_number'];?></td>

				<?php } else { ?>

				<td>N/A</td>

				<?php } ?>

				<td></td>

			</tr>

			<tr>

				<td>Receive Date: </td>

				<td><?php echo americanDate($shipment_detail['date_received']);?></td>

				<td></td>

			</tr>

			<tr>

				<td>QC Date: </td>

				<td><?php echo americanDate($shipment_detail['date_qc']);?></td>

				<td></td>

			</tr>

		</table>



		<table border="1" width="816px" cellpadding="10" cellspacing="0" style="border-collapse: collapse;">

			<tr style="background-color: #ddd">

			    <th width="5%">New?</th>

				<th width="15%">SKU</th>

				<th width="55%">Item Name</th>

				<th width="10%">PO</th>

				<th width="5%">Qty</th>

				<th width="5%">RTV</th>

				<th width="5%">NTR</th>

			</tr>

			<?php foreach($shipment_items as $shipment_item):?>



				<?php



				?>

				<tr>



					<td align="center"><?php echo ($shipment_item['is_new']?'X':'-');?></td>

					<td><?php echo $shipment_item['product_sku'];?></td>



					<td><?php echo $shipment_item['item_name'];?></td>



					<td><?php echo $shipment_item['cu_po'];?></td>



					<td><?php echo $shipment_item['qty_received'];?></td>

					<td><?php echo ($db->func_query_first_cell("SELECT is_main_sku FROM oc_product WHERE sku = '".$shipment_item['product_sku']."'"))? $shipment_item['qty_rejected']: 'N/A';?></td>



					<td><?php echo ($db->func_query_first_cell("SELECT is_main_sku FROM oc_product WHERE sku = '".$shipment_item['product_sku']."'"))? $shipment_item['qty_ntr']: 'N/A';?></td>

				</tr>

			<?php endforeach;?>

		</table>



		<br />

		<a href="javascript://" onClick="window.print();"><b>Print Now</b></a>

		<br /><br />



	</center>
	<script type="text/javascript" src="js/jquery.min.js"></script>

	<script type="text/javascript" src="js/jquery-barcode.min.js"></script>  
<script>
$("#barcode").barcode("<?php echo $code;?>", "code128");    

</script>
</body>

</html>