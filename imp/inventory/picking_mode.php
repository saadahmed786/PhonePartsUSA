<?php

include_once '../auth.php';

include_once '../inc/functions.php';

include_once 'class.php';
$filter = '';
if(isset($_POST['filter']))
{
	$filter = $_POST['filter'];
}

$_settings = oc_config('imp_inventory_setting');
//$data = str_replace("'", "", $data);
	$setting = unserialize($_settings);
// echo $filter;

$processedOrders = $inventory->getToBePickedOrders($filter);



?>

<div id="modal_manager_approval_picking" class="modal fade" role="dialog">

  <div class="modal-dialog">



    <!-- Modal content-->

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal">&times;</button>

        <h4 class="modal-title"></h4>

      </div>

      <div class="modal-body">

        <p><input type="password" class="form-control" id="manager_auth_key" placeholder="Authentication Key"> </p>

        

      </div>

      <div class="modal-footer">

      <button id="process_bulk_pick" class="btn btn-success">Process</button>

        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

      </div>

    </div>



  </div>

</div>

<div id="modal_manager_approval_closed_short" class="modal fade" role="dialog">

  <div class="modal-dialog">



    <!-- Modal content-->

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal">&times;</button>

        <h4 class="modal-title"></h4>

      </div>

      <div class="modal-body">

        <!-- <p><input type="password" class="form-control" id="manager_auth_key_closed_short" placeholder="Authentication Key"> </p> -->

        

      </div>

      <div class="modal-footer">

      <button id="process_closed_short" class="btn btn-success">Process</button>

        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

      </div>

    </div>



  </div>

</div>

<div class="row" style="margin-bottom:5px">

<div class="col-md-2 text-left"><button  class="btn btn-success btn_bulk_pick " style="">Bulk Pick</button></div>

<div class="col-md-3"><input type="text" class="form-control" id="picking_order_id_manual"  placeholder="Search Orders, Customers &amp; Emails"></div>



<div class="col-md-3 text-right"><button id="btn_manager_pick" class="btn btn-primary  disabled">Manager Approval</button></div>
<div class="col-md-4 text-right">	<button id="btn_print_picking_slip" class="btn btn-info disabled" style="">Print Pick List</button> <button id="btn_print_packing_slip" class="btn btn-info  disabled" style="">Print Packing Slip</button></div>

</div>


<div class="row">



	<div class="col-md-8" style="height:77vh;overflow: scroll">

	

			<table  class="xtable general_table"    align="center" style="width:98%;margin-top: 0px;">

				<thead>

					<tr>

					<th align="center" style="text-align: center"><input type="checkbox" id="picking_select_all"></th>

						<th  width=""  align="center">Order Date</th>

						<th width="" align="center">Order #</th>



						<th width="" align="center">Recipient</th>


						<?php
						if($setting['picking_sku_col']==1)
						{
						?>
						<th width="" align="center">Item SKU</th>
						<?php
						}
						?>
						<?php
						if($setting['picking_name_col']==1)
						{
						?>
						<th width="" align="center">Item Name</th>
						<?php
						}
						?>

						<?php
						if($setting['picking_service_col']==1)
						{
						?>

						<th width="" align="center">Service</th>
						<?php
						}
						?>

						<?php
						if($setting['picking_order_col']==1)
						{
						?>
						<th width="" align="center">Order Total</th>
						<?php
						}	
						?>
						
						<?php
						if($setting['picking_qty_col']==1)
						{
						?>
						<th width="" align="center">Qty</th>
						<?php
						}
					?>
						<?php
						if($setting['picking_status_col']==1)
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

						<td>

						<input type="checkbox" class="orderIds" value="<?php echo $row['order_id'];?>" >

						</td>

							<td><?php echo ($row['order_date']);?></td>

							<td><?php echo linkToOrder($row['order_id'],$host_path,'target="_blank"');?></td>

							<td><?php echo $row['customer_name'];?></td>

							<?php
						if($setting['picking_sku_col']==1)
						{
						?>

							<td><?php echo (count($row['items'])>1?'(Multiple Items)':linkToProduct($row['items'][0]['sku'],$host_path,'target="_blank"',''));?></td>
						<?php
						}
						?>

						<?php
						if($setting['picking_name_col']==1)
						{
						?>

							<td><?php echo (count($row['items'])>1?'(Multiple Items)':$row['items'][0]['name']);?></td>
							<?php
						}
						?>

						<?php
						if($setting['picking_service_col']==1)
						{
						?>

							<td><?php echo $row['shipping_method'];?></td>
							<?php
						}
						?>

						<?php
						if($setting['picking_order_col']==1)
						{
						?>

							<td><?php echo $row['order_price']; ?></td>
							<?php
						}


						?>

						<?php
						if($setting['picking_qty_col']==1)
						{
						?>
							<td style="<?php echo ($row['all_quantity']>1?'font-size:13px;font-weight:bold':'');?>"><?php echo $row['all_quantity'];?></td>
							<?php
						}
						?>

						<?php
						if($setting['picking_status_col']==1)
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
						<div class="col-md-12 combine_detail text-center" style="display:none;margin-top:33%">
						<button  class="btn btn-success btn_bulk_pick btn-lg " style="">Generate Bulk Pick</button>
						</div>
							<div class="col-md-12 picking_detail" style="">

						
									<div style="text-align: left;font-weight: bold; margin-bottom: 5px; background-color: #FAFAFA; border-color: #EEEEEE; padding: 3px;">ORDER # <span id="picking_order_id">N/A</span>
									<input type="hidden" id="close_short" value="0">



									

									</div> 



									<div class="col-md-12 text-center" style="margin-bottom:5px">

						

						<div class="form-group row">

							<div class="col-md-3"></div>

							<div class="col-sm-6 ">

								<input type="text" class="form-control" id="picking_scan_sku"  placeholder="SKU">

							</div>

							<div class="col-md-3"></div>

						</div>



					</div>



									

												<table class="xtable" id="table_picking_list" style="width:99%">

												<thead>

												<tr>

												<th>SKU</th>

												<th>Item Name</th>

												<th>Added</th>



												</tr>	



												</thead>

												<tbody>



												</tbody>



												</table>

											

										</div>



									


											<div class="col-md-2">
									

												<?php
												if($_SESSION['product_qty_update'])
												{
												?>

												<button type="button" class="btn btn-danger btn-xs  disabled btn_closed_short picking_detail" >Closed Short</button>
												<?php
											}
											?>

											</div>

											<div class="col-md-10">

												<button type="button" class="btn btn-primary  disabled save_record_btn picking_detail" >Save Record</button>

												</div>


											

										</div>

									</div>

								</div>



							</div>





</div>