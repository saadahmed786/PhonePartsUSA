<?php
include_once '../auth.php';
include_once '../inc/functions.php';
include_once 'class.php';

$filter = '';
$date_filter=date('Y-m-d');
$carrier_filter='';

if(isset($_POST['filter']))
{
	$filter = trim($_POST['filter']);
}
if(isset($_POST['filter_date']))
{
	$date_filter = $_POST['filter_date'];
}


$processedOrders = $inventory->getShippedOrders($filter,$date_filter,$carrier_filter);;
$carriers = $inventory->listCarriers();
$carriers = json_decode($carriers,true);

$_settings = oc_config('imp_inventory_setting');

$setting = unserialize($_settings);

?>

<div class="row" style="margin-bottom: 10px">

<div class="col-md-2 text-left"><button class="btn btn-danger btn-sm  disabled" id="void_label">Void</button>
		<button class="btn btn-success btn-sm  disabled" id="reprint_label">Re-Print Label</button>

	</div>

<div class="col-md-2"><input type="text" class="form-control" id="shipped_order_id_manual"  placeholder="Search Orders, Customers, Emails &amp; Tracking #"></div>
<div class="col-md-2"><input type="date" class="form-control" id="manifest_date" value="<?php echo $date_filter ;?>"></div>

	<div class="form-group col-md-2 text-right">

															<select class="form-control" id="manifest_carrier" >
																<option value="">Please Select</option>
																<?php
																foreach($carriers['carriers'] as $carrier)
																{
																	?>
																	
																	<option value="<?php echo $carrier['carrier_id'];?>"><?php echo utf8_decode($carrier['friendly_name']);?></option>

																	
																
																<?php
																}
																?>
																<option value="Local Pickup">Local Pickup</option>
															</select>
														</div>

														<div class="col-md-1 text-center" style="margin-top:5px">
														<button class="btn btn-primary" id="history_apply_filter"><i class="fa fa-filter"></i></button>
														<button class="btn btn-danger " id="history_clear_filter"><i class="fa fa-eraser"></i></button>
														</div>
														<div class="col-md-3 text-center">
														<button class="btn btn-primary btn-sm " id="create_manifest">Download Manifest</button>
														
														<button class="btn btn-info btn-sm " id="end_of_day_report">EOD Totals</button>
														</div>


</div>
		<!-- <div class="col-md-4"></div> -->
		</div>
<div class="row">

	<div class="col-md-12" style="">
			<table  class="xtable general_table"   align="center" style="width:98%;margin-top: 0px;">
				<thead>
					<tr>
						<th  width=""  align="center">Order Date</th>
						<th width="" align="center">Order #</th>

						<th width="" align="center">Recipient</th>

						<?php
						if($setting['history_tracking_col']==1)
						{
						?>

						<th width="" align="center">Tracking</th>

						<?php
					}
					?>


						<?php
						if($setting['history_service_col']==1)
						{
						?>
						<th width="" align="center">Service</th>
						<?php
					}
					?>

					<?php
						if($setting['history_order_col']==1)
						{
						?>
						<th width="" align="center">Order Total</th>
						<?php
					}
					?>

					<?php
						if($setting['history_qty_col']==1)
						{
						?>
						
						<th width="" align="center">Qty</th>
						<?php
					}
					?>
					<?php
						if($setting['history_shipping_col']==1)
						{
						?>
						<th width="" align="center">Shipping Paid</th>
						<?php
					}
					?>

					</tr>
				</thead>
				<tbody>
					<?php
					// print_r($processedOrders);exit;
						foreach($processedOrders as $row)
						{
								// print_r($row);exit;

					?>
						<tr data-order="<?php echo $row['order_id'];?>">
							<td><?php echo ($row['order_date']);?></td>
							<!-- <td><?php echo linkToOrder($row['order_id'],$host_path,'target="_blank"');?></td> -->
							<td>
							<?php
							if($row['combined_orders'])
							{
								$label_orders = $row['combined_orders'];
							}
							else
							{
								$label_orders = $row['order_id'];
							}
							$order_total = $inventory->getOrderTotal($label_orders);
							// $order_total = 0.00;
							foreach(explode(",", $label_orders) as $label_order)
							{
								// $order_total+=$label_order['order_price'];
								echo linkToOrder($label_order,$host_path,'target="_blank"')."<br>";
							}
							?>
							</td>
							<td><?php echo $row['customer_name'];?></td>
							<?php
						if($setting['history_tracking_col']==1)
						{
						?>
							<td><?php echo $row['tracking_number'];?></td>
							<?php
						}
						?>

						<?php
						if($setting['history_service_col']==1)
						{
						?>
							<td><?php echo $row['shipping_method'];?></td>
							<?php
						}
						?>

						<?php
						if($setting['history_order_col']==1)
						{
						?>
							<!-- <td><?php echo $row['order_price']; ?></td> -->
							<td><?php echo $order_total; ?></td>
							<?php
						}
						?>

						<?php
						if($setting['history_qty_col']==1)
						{
						?>
							<td style="<?php echo ($row['all_quantity']>1?'font-size:13px;font-weight:bold':'');?>"><?php echo $row['all_quantity'];?></td>
							<?php
						}
						?>

						<?php
						if($setting['history_shipping_col']==1)
						{
						?>
							<td><?php echo $row['shipping_cost'];?></td>
							<?php
						}
						?>
					</tr>

					<?php
				}
				?>
				</tbody>
				<tfoot>
				<tr>
				<td colspan="10"><i># of Orders: <?php echo count($processedOrders);?></i></td>
				</tr>
				</tfoot>
				
			</table>
			</div>

			<div class="col-md-4 panel list-group hidden" style="height:77vh;overflow: scroll">
						<div class="row">
							<div class="col-md-12" style="">
									<div style="text-align: left;font-weight: bold; margin-bottom: 5px; background-color: #FAFAFA; border-color: #EEEEEE; padding: 3px;">ORDER # <span data-map1="order_id">N/A</span></div> <div class="panel-group" id="accordion"  style="font-size:12px">

									<div class="panel panel-default">
										<div class="panel-heading" role="tab" id="headingOne">
											<h4 class="panel-title">
												<a role="button" data-toggle="collapse"  href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
													<i class="more-less glyphicon glyphicon-minus"></i>
													SHIPPING
												</a>
											</h4>
										</div>
										<div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
											<div class="panel-body">
												<table class="table" >
													<tr>
														<td class="col-md-2 text-right">Requested</td>
														<td style="color:green" class="col-md-10" data-map1="shipping_method" >N/A</td>
													</tr>
													<tr>
														<td class="col-md-2 text-right">Ship From</td>
														<td class="col-md-10" style="color:green">Las Vegas, NV.</td>
													</tr>
													<tr>
														<td class="col-md-2 text-right">Weight</td>
														<td class="col-md-10" >
															<div class="col-md-3" style="float:left">
																<input type="number" class="form-control form-control-md col-md-3" id="weight_lb" onkeyup="" data-attr="lb"   placeholder="(lb)" > 
															</div>
															<div class="col-md-3" style="float:left">
																<input type="number" class="form-control form-control-md col-md-3" id="weight_oz" onkeyup="" data-attr="oz"  placeholder="(oz)" > 
															</div>
															<div class="col-md-6"></div>
														</td>
													</tr>
													<tr>
														<td class="col-md-2 text-right">Service</td>
														<td class="col-md-10"><div class="form-group">

															<select class="form-control" id="service" >
																<option value="">Please Select</option>
																<?php
																foreach($carriers['carriers'] as $carrier)
																{
																	?>
																	<optgroup label="<?php echo $carrier['friendly_name'];?>">
																	<?php
																	foreach($carrier['services'] as $service)
																	{
																	?>
																	<option value="<?php echo $carrier['carrier_id'];?>~<?php echo $service['service_code'];?>"><?php echo utf8_decode($service['name']);?></option>

																	<?php
																}
																?>
																</optgroup>
																<?php
																}
																?>
															</select>
														</div></td>
													</tr>

													<tr>
														<td class="col-md-2 text-right">Package</td>
														<td class="col-md-10"><div class="form-group">

															<select class="form-control" id="package" >
																<option value="">Please Select</option>
															</select>
														</div></td>
													</tr>

													<tr>
														<td class="col-md-2 text-right">Size</td>
														<td class="col-md-10" >

															<input type="number" class="form-control form-control-md col-md-3" id="size_length"  placeholder="L" style="width:25% !important" > 





															<input type="number" class="form-control form-control-md col-md-2" id="size_width" placeholder="W" style="width:25% !important;margin-left:5px;"  > 


															<input type="number" class="form-control form-control-md col-md-2" id="size_height" placeholder="H" style="width:25% !important;margin-left:5px"  > 


														</td>

													</tr>

													<tr>
														<td class="col-md-2 text-right">Confirm</td>
														<td class="col-md-10"><div class="form-group">

															<select class="form-control" id="confirmation" >
																<option value="none">Service Default</option>
																<option value="delivery">No Signature Required</option>
																<option value="signature">Indirect Signature Required</option>
																<option value="adult_signature">Adult Signature Required</option>
																<option value="direct_signature">Direct Signature Required</option>
															</select>
														</div></td>
													</tr>

													<tr>
														<td class="col-md-2 text-right">Insurance</td>
														<td class="col-md-10">
															<input type="number" class="form-control form-control-md col-md-3" id="insured_amount" placeholder="Leave Blank if not Insured" style="width:25% !important" value="0.00" > 

														</td>


													</tr>

													<tr>
														<td class="col-md-2 text-right">Delivery Time</td>
														<td style="color:green" class="col-md-10" id="delivery_time">N/A</td>
													</tr>
													<tr>
														<td class="col-md-2 text-right">Rate</td>
														<td style="color:green" class="col-md-10"><span style="font-size:14px;font-weight: bold;color:green" id="estimate_rate">$0.00</span><i class="fa fa-refresh" id="get_rate" style="margin-left: 5px; border: 1px solid #DCDCDC; padding: 4px; border-radius: 5px;cursor: pointer;"></i></td> </tr>



												</table>
											</div>
										</div>

										<div class="panel-heading" role="tab" id="headingTwo">
											<h4 class="panel-title">
												<a role="button" data-toggle="collapse"  href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
													<i class="more-less glyphicon glyphicon-plus"></i>
													ITEM(S) ORDERED
												</a>
											</h4>
										</div>
										<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
											<div class="panel-body">
												


											</div>
										</div>

										<div class="panel-heading" role="tab" id="headingThree">
											<h4 class="panel-title">
												<a role="button" data-toggle="collapse"  href="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
													<i class="more-less glyphicon glyphicon-plus"></i>
													CUSTOMER
												</a>
											</h4>
										</div>
										<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
											<div class="panel-body">
												<div class="row" style="">
													<div class="col-md-2" style="font-weight: bold;padding-top:2.55%">Sold To</div>
													<div class="col-md-4"><span data-map1="customer_name"></span><br>
														<span data-map1="telephone"></span><br>
														<span data-map1="email"></span></div>
														<div class="col-md-2" style="font-weight: bold;padding-top:2.55%">Ship To</div>
														<div class="col-md-4"><span data-map1="shipping_name"></span><br>
															<span data-map1="address1"></span><br>
															<span data-map1="city"></span>, <span data-map1="state"></span> <span data-map1="zip"></span> <span data-map1="country"></span><br> 
															</div>
														</div>
													</div>
												</div>

												<div class="panel-heading" role="tab" id="headingFour">
													<h4 class="panel-title">
														<a role="button" data-toggle="collapse"  href="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
															<i class="more-less glyphicon glyphicon-plus"></i>
															SUMMARY
														</a>
													</h4>
												</div>
												<div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
													<div class="panel-body">
														<div class="row">
															<div class="col-md-2" style="font-weight:bold">Order Date</div>
															<div class="col-md-4" data-map1="order_date"></div>
															<div class="col-md-2" style="font-weight:bold">Product Total</div>
															<div class="col-md-4" data-map1="product_total">$0.00</div>

														</div>
														<div class="row">
															<div class="col-md-2" style="font-weight:bold">Ship By</div>
															<div class="col-md-4"></div>
															<div class="col-md-2" style="font-weight:bold">Shipping Paid</div>
															<div class="col-md-4" data-map1="shipping_cost">$0.00</div>
														</div>
														
														<div class="row">
														<div class="col-md-2" style="font-weight:bold"></div>
															<div class="col-md-4"></div>
														
															<div class="col-md-2" style="font-weight:bold">Total Order</div>
															<div class="col-md-4" data-map1="order_price">$0.00</div>
														</div>
														<div class="row">
															<div class="col-md-2" style="font-weight:bold"></div>
															<div class="col-md-4"></div>
															<div class="col-md-2" style="font-weight:bold">Total Paid</div>
															<div class="col-md-4" data-map1="paid_price">$0.00</div>
														</div>
													</div>

												</div>
												<button class="btn btn-primary" id="recreate_label">Regenerate Label</button>
											</div>
										</div>
									</div>
								</div>

							</div>
								</div>

							</div>


</div>