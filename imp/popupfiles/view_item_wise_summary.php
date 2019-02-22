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



$inv_query = "SELECT a.*,b.* FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id AND b.sku='$sku'  AND $condition_sql ORDER BY a.date_added DESC";



if(isset($_GET['page'])){

	$page = intval($_GET['page']);

}

if($page < 1){

	$page = 1;

}



$max_page_links = 10;

$num_rows = 50;

$start = ($page - 1)*$num_rows;

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "view_item_wise_summary.php",$page);



$returns = $db->func_query($splitPage->sql_query);


if(isset($_GET['start_date']) and $_GET['start_date']!='' and isset($_GET['end_date']) and $_GET['end_date']!='' )
{
	$order_condition = " (a.order_date BETWEEN '".$_GET['start_date']."' AND '".$_GET['end_date']."') ";
} 
else
{
	$order_condition = " 1 = 1 ";
}

$orders = $db->func_query("SELECT b.dateofmodification, b.order_id, b.product_qty, b.product_price  FROM `inv_orders_items` b,inv_orders a WHERE a.order_id=b.order_id and $order_condition and  `product_sku` = '$sku'  ORDER BY `dateofmodification` DESC");





?>



<div style="display:none"><?php include_once '../inc/header.php';?></div>

<?php
//if(!isset($_GET['show_return']))
//{

getWeeklyReturnsBySKUNew($sku,$condition_sql);
//}

?>




<?php
if(!isset($_GET['no_orders']) && !isset($_GET['show_return']) )
{
?>
<br><br>

<h2>Orders</h2>

<div style="height:400px;overflow-y:scroll; border: solid 1px #000;">

	<h2><?php echo $sku;?> - <?php   echo $db->func_query_first_cell("SELECT

		b.`name`

		FROM

		`oc_product` a

		INNER JOIN `oc_product_description` b

		ON (a.`product_id` = b.`product_id`) WHERE a.sku='".$sku."'");?></h2>

		<table width="100%" cellspacing="0" cellpadding="5px" border="1" align="center">

			<thead>

				<tr style="background-color:#e5e5e5;">

					<th>SN</th>

					<th>Date</th>

					<th>Order Id</th>

					<th>QTY</th>

					<th>Sale Price</th>

				</tr>

			</thead>



			<tbody>



				<?php foreach ($orders as $i => $order) {

					if($order['dateofmodification']=='0000-00-00 00:00:00')
					{
						$order['dateofmodification'] = $db->func_query_first_cell("SELECT order_date FROM inv_orders WHERE order_id='".$order['order_id']."'");
					}
				 ?>



				<tr>



					<td><?= ($i) + 1 ; ?></td>

					<td><?= americanDate($order['dateofmodification']); ?></td>

					<td><?= linkToOrder($order['order_id'], $host_path); ?></td>

					<td><?= $order['product_qty']; ?></td>

					<td><?= $order['product_price']; ?></td>



				</tr>    



				<?php } ?>



			</tbody>

		</table>

	</div>



	<br><br>
	<?php
}
?>

	<h2>Returns</h2>

	<div style="height:400px;overflow-y:scroll; border: solid 1px #000;">

		<table width="100%" cellspacing="0" cellpadding="5px" border="1" align="center">

			<thead>

				<tr style="background-color:#e5e5e5;">

					<th>SN</th>

					<th>Order Date</th>

					<th>Date QC</th>

					<th>RMA #</th>

					<th>Order ID</th>



					<th>Email</th>

					<th>Store Type</th>

					<th>Status</th>

					<th>Source</th>

					<th>Price</th>

					<th>Return Reason</th>

					<th>Condition</th>

					<th>Decision</th>



				</tr>

			</thead>

			<?php $i = $splitPage->display_i_count();

			foreach($returns as $return)

			{

				$comments = $db->func_query("SELECT comments FROM inv_return_comments WHERE return_id='".$return['return_id']."'");

				$item_comments = '';

				foreach($comments as $comment)

				{

					$_comment = explode("-",$comment['comments']);



					$item_comments.=stripslashes($_comment[3])."\n";



				}



				$orderDate = $db->func_query_first_cell("SELECT `order_date` FROM `inv_orders` WHERE `order_id` = '" . $return['order_id'] . "'");

				?>

				<tr>

					<td align="center"><?=$i;?></td>

					<td align="center"><?=americanDate($orderDate);?></td>

					<td><?php echo ($return['date_qc']) ? americanDate($return['date_qc']): ''; ?></td>

					<td align="center"><a href="javascript:void(0);" onClick="window.open('../return_detail.php?rma_number=<?php echo $return['rma_number']?>')"><?=$return['rma_number'];?></a></td>

					<td align="center"><a href="javascript:void(0);" onClick="window.open('../viewOrderDetail.php?order=<?php echo $return['order_id']?>')"><?=$return['order_id'];?></a></td>

					<td align="center"><?= linkToProfile($return['email'], $host_path) ;?></td>

					<td align="center"><?=$return['store_type'];?></td>

					<td align="center"><?=$return['rma_status'];?></td>

					<td align="center"><?=$return['source'];?></td>

					<td align="center"><?='$'.number_format($return['price'],2);?></td>



					<?php

					if($item_comments)

					{

						?>

						<td align="center"><a href="javascript:void(0);" data-tooltip="<?php echo $item_comments;?>"><?=$return['return_code'];?></a></td>

						<?php

					}

					else

					{

						?>

						<td align="center"><?=$return['return_code'];?></td>

						<?php  

					}

					?>

					<td align="center"><?=$return['item_condition'];?></td>

					<td align="center"><?=$return['decision'];?></td>











				</tr>

				<?php $i++;  ?>

				<?php

			}

			?>

			<tr>

				<td colspan="6" align="left">

					<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>

				</td>



				<td colspan="7" align="right">

					<?php echo $splitPage->display_links(10,$parameters);?>

				</td>

			</tr>

		</table>

	</div>

</body>

</html>