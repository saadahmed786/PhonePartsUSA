<?php
include_once '../auth.php';
include_once '../inc/functions.php';
include_once 'class.php';

$filter = '';
if(isset($_POST['filter']))
{
	$filter = $_POST['filter'];
}
$processedOrders = $inventory->getAdjustmentOrders($filter);
// print_r($processedOrders);exit;
$_settings = oc_config('imp_inventory_setting');

$setting = unserialize($_settings);

?>

<div class="row" style="margin-bottom:10px">

<div class="col-md-2"></div>

<div class="col-md-3"><input type="text" class="form-control" id="adjustment_order_id_manual"  placeholder="Search Orders, Customers &amp; Emails"></div>


<div class="col-md-3 text-right">
	<?php
	if($_SESSION['login_as']=='admin')
	{
		?>
		<!-- <button id="discard_removals" class="btn btn-danger disabled">Discard Removals Items</button> -->
		<?php
	}
	?>

</div>
<div class="col-md-4"></div>

</div>
<div class="row">

	<div class="col-md-8" style="height:77vh;overflow: scroll">

			<table  class="xtable general_table"   align="center" style="width:98%;margin-top: 0px;">
				<thead>
					<tr>
						<th  width=""  align="center">Order Date</th>
						<th width="" align="center">Order #</th>

						<th width="" align="center">Recipient</th>
						<?php
						if($setting['adjustment_sku_col']==1)
						{
						?>
						<th width="" align="center">Removed SKU</th>
						<?php
					}
					?>

					<?php
						if($setting['adjustment_name_col']==1)
						{
						?>
						<th width="" align="center">Item Name</th>
						<?php
					}
					?>

					<?php
						if($setting['adjustment_service_col']==1)
						{
						?>
						<th width="" align="center">Service</th>
							<?php
					}
					?>

					<?php
						if($setting['adjustment_order_col']==1)
						{
						?>
						<th width="" align="center">Order Total</th>
							<?php
					}
					?>

					<?php
						if($setting['adjustment_qty_col']==1)
						{
						?>
						
						<th width="" align="center">Qty</th>
							<?php
					}
					?>

					<?php
						if($setting['adjustment_status_col']==1)
						{
						?>
						<th width="" align="center">Status</th>
							<?php
					}
					?>


					</tr>
				</thead>
				<tbody>
					<?php
						foreach($processedOrders as $row)
						{
								// print_r($row);exit;

					?>
					<tr data-order="<?php echo $row['order_id'];?>">
							<td><?php echo ($row['order_date']);?></td>
							<td><?php echo linkToOrder($row['order_id'],$host_path,'target="_blank"');?></td>
							<td><?php echo $row['customer_name'];?></td>


					<?php
						if($setting['adjustment_sku_col']==1)
						{
						?>
							<td><?php echo (count($row['items'])>1?'(Multiple Items)':linkToProduct($row['items'][0]['sku'],$host_path,'target="_blank"',''));?></td>
								<?php
					}
					?>

					<?php
						if($setting['adjustment_name_col']==1)
						{
						?>
							<td><?php echo (count($row['items'])>1?'(Multiple Items)':$row['items'][0]['name']);?></td>
								<?php
					}
					?>

					<?php
						if($setting['adjustment_service_col']==1)
						{
						?>
							<td><?php echo $row['shipping_method'];?></td>
								<?php
					}
					?>

					<?php
						if($setting['adjustment_order_col']==1)
						{
						?>
							<td><?php echo $row['order_price']; ?></td>
								<?php
					}
					?>

					<?php
						if($setting['adjustment_qty_col']==1)
						{
						?>
							<td style="<?php echo ($row['all_quantity']>1?'font-size:13px;font-weight:bold':'');?>"><?php echo $row['all_quantity'];?></td>
								<?php
					}
					?>

					<?php
						if($setting['adjustment_status_col']==1)
						{
						?>
							<td><?php echo $row['order_status'];?></td>
								<?php
					}
					?>

				
					</tr>

					<?php
				}
				?>
				</tbody>
				
			</table>
			</div>

			<div class="col-md-4 " style="height:77vh;overflow: scroll">
						<div class="row">
							<div class="col-md-12" style="">
									<div style="text-align: left;font-weight: bold; margin-bottom: 5px; background-color: #FAFAFA; border-color: #EEEEEE; padding: 3px;">ORDER # <span id="adjustment_order_id">N/A</span></div> 

									<div class="col-md-12 text-center" style="margin-bottom:5px">
						
						<div class="form-group row">
							<div class="col-md-3"></div>
							<div class="col-sm-6 ">
								<input type="text" class="form-control" id="adjustment_scan_sku"  placeholder="SKU">
							</div>
							<div class="col-md-3"></div>
						</div>

					</div>

									
												<table class="xtable" id="table_adjustment_list" style="width:99%">
												<thead>
												<tr>
												<th>SKU</th>
												<th>Item Name</th>
												<th>Adj?</th>

												</tr>	

												</thead>
												<tbody>

												</tbody>

												</table>
											
										</div>

									

									
												
												<!-- checkbox_val<button type="button" class="btn btn-primary  disabled save_record_btn2" >Save Record</button> -->
											
										</div>
									</div>
								</div>

							</div>


</div>