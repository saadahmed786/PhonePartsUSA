<?php
require_once("../auth.php");
include_once '../inc/split_page_results.php';
include_once '../inc/functions.php';
$parameters  = $_SERVER['QUERY_STRING'];
$sku = $_GET['sku'];
$conditions = base64_decode($_GET['conditions']);
if(!$conditions)
{
	$condition_sql = ' 1 = 1 '	;

}
else
{
	$condition_sql = str_replace(","," AND ",$conditions);	
	
}

  //$inv_query = 'Select b.quantity,b.price,b.order_id,a.date_added from oc_order a,oc_order_product b where a.order_id=b.order_id AND ( a.order_status_id IN( 15 , 24 , 3 , 16 , 7 , 21 , 11) ) and b.`model` = "' . $sku . '" ORDER BY b.order_id DESC';
$inv_query = "SELECT date_added,`product_sku`,date_completed, `vendor`, `qty_shipped`, `qty_received`, `package_number`, `date_issued`, `fb_added`, `status`, `ex_rate`, `unit_price`, `shipment_id`, (SELECT (s.`shipping_cost` / SUM(qty_shipped)) FROM inv_shipment_items WHERE shipment_id = st.`shipment_id`) AS ship_cost
from  inv_shipments s inner join inv_shipment_items st on (s.id = st.shipment_id)
where  date_issued >= '2015-01-30 00:00:00' AND product_sku = '$sku' order by date_completed desc";

if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}
if($page < 1){
	$page = 1;
}

$max_page_links = 10;
$num_rows = 50;
$start = ($page - 1)*$num_rows;

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "product_shipment_history.php",$page);
$shipments = $db->func_query($splitPage->sql_query);

?>

<div style="display:none"><?php include_once '../inc/header.php';?></div>
<h2><?php echo $sku;?> - <?php echo getItemName($sku);?></h2>


<table width="100%" cellspacing="0" cellpadding="5px" border="1" align="center">
	<thead>
		<tr style="background-color:#e5e5e5;">
			<th >#</th>
			<th>Date Completed</th>
			<th >Vendor Name</th>
			<th >Shipment ID</th>
			<th>Raw Cost</th>
			<th >Landed Cost</th>
			<!-- <th >Delivered</th>-->
			<th >QTY Shipped</th>
			<th >QTY</th>

			<th >Grade A</th>
			<th >Grade B</th>
			<th >Grade C</th>
			<th >Grade D</th>

			<th >RJ QTY</th>
			<th >NTR QTY</th>



		</tr>
	</thead>
	<?php $i = $splitPage->display_i_count();
	foreach($shipments as $row)
	{
		?>
		<?php $rowQ = $db->func_query_first("SELECT * from `inv_shipment_qc` WHERE shipment_id = '" . $row['shipment_id'] . "' AND product_sku = '$sku'");?>
		<tr>
			<td align="center">
				<?= ($i); ?>
			</td>
			<td align="center">
			<?php
			if($row['date_completed']=='0000-00-00 00:00:00')
			{
				echo americanDate($row['date_added']);
			}
			else
			{
				echo americanDate($row['date_completed']);
			}
			?>
			</td>
			<td align="center">
				<?= get_username($row['vendor']); ?>
			</td>
			<td align="center">
				<?= linkToShipment($row['shipment_id'], $host_path, $row['package_number'], ' target="_blank" '); ?>
			</td>

			<td align="center">
				<?= round(($row['unit_price'] / $row['ex_rate']), 2); ?>
			</td>
			<td align="center">
	<?= round(($row['unit_price']+$row['ship_cost']) / $row['ex_rate'], 2); ?>
</td>
			<!-- <td align="center">
				<?= round(($row['unit_price'] + $row['ship_cost']), 2); ?>
			</td> -->

			<td align="center">
				<?= $row['qty_shipped']; ?>
			</td>
			<td align="center">
				<?= $row['qty_received']; ?>
			</td>

			<td align="center">
				<?= $rowQ['grade_a_qty']; ?>
			</td>
			<td align="center">
				<?= $rowQ['grade_b_qty']; ?>
			</td>
			<td align="center">
				<?= $rowQ['grade_c_qty']; ?>
			</td>
			<td align="center">
				<?= $rowQ['grade_d_qty']; ?>
			</td>

			<td align="center">
				<?= $rowQ['rejected']; ?>
			</td>
			<td align="center">
				<?= $rowQ['ntr']; ?>
			</td>
		</tr>
		<?php $i++;  ?>
		<?php
	}
	?>
	<tr>
		<td colspan="5" align="left">
			<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
		</td>

		<td colspan="9" align="right">
			<?php echo $splitPage->display_links(10,$parameters);?>
		</td>
	</tr>
</table>
</body>
</html>