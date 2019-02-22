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

$setting = unserialize($_settings);

$processedOrders = $inventory->getPackedOrders($filter);;
$carriers = $inventory->listCarriers();
$carriers = json_decode($carriers,true);

?>
<div class="row" style="margin-bottom:5px">

<div class="col-md-2"></div>

<div class="col-md-3"><input type="text" class="form-control" id="label_order_id_manual"  placeholder="Search Orders, Customers &amp; Emails"></div>
<div class="col-md-2"></div>

<div class="col-md-2 text-right" style="font-weight:bold" >Combine Order(s):</div>
<div class="col-md-2 text-right"> <input type="text" class="form-control" disabled id="label_combine_orders"  placeholder="Combine with Orders (Comma Seperated)">
</div>
<div class="col-md-1 ">
 <button class="btn btn-primary  disabled" id="label_combine_btn">Done</button></div>

</div>
<div class="row">

	<div class="col-md-8" style="">
			<table  class="xtable general_table"   align="center" style="width:98%;margin-top: 0px;">
				<thead>
					<tr>
						<th  width=""  align="center">Order Date</th>
						<th width="" align="center">Order #</th>

						<th width="" align="center">Recipient</th>

						<?php
						if($setting['shipping_sku_col']==1)
						{
						?>

						<th width="" align="center">Item SKU</th>
						<?php
					}
					?>

					<?php
						if($setting['shipping_name_col']==1)
						{
						?>
						<th width="" align="center">Item Name</th>
						<?php
					}
					?>
					<?php
						if($setting['shipping_service_col']==1)
						{
						?>
						<th width="" align="center">Service</th>
						<?php
					}
					?>

					<?php
						if($setting['shipping_order_col']==1)
						{
						?>
						<th width="" align="center">Order Total</th>
						<?php
					}
					?>
						<?php
						if($setting['shipping_qty_col']==1)
						{
						?>
						<th width="" align="center">Qty</th>
						<?php
					}
					?>

					<?php
						if($setting['shipping_status_col']==1)
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
						foreach($processedOrders as $row)
						{
								// print_r($row);exit;

					?>
						<tr data-order="<?php echo $row['order_id'];?>">
							<td><?php echo ($row['order_date']);?></td>
							<td><?php echo linkToOrder($row['order_id'],$host_path,'target="_blank"');?></td>
							<td><?php echo $row['customer_name'];?></td>

							<?php
						if($setting['shipping_sku_col']==1)
						{
						?>
							<td><?php echo (count($row['items'])>1?'(Multiple Items)':linkToProduct($row['items'][0]['sku'],$host_path,'target="_blank"',''));?></td>
							<?php
						}
						?>

						<?php
						if($setting['shipping_name_col']==1)
						{
						?>
							<td><?php echo (count($row['items'])>1?'(Multiple Items)':$row['items'][0]['name']);?></td>
							<?php
						}
						?>

						<?php
						if($setting['shipping_service_col']==1)
						{
						?>
							<td><?php echo $row['shipping_method'];?></td>
							<?php
						}
						?>

						<?php
						if($setting['shipping_order_col']==1)
						{
						?>
							<td><?php echo $row['order_price']; ?></td>
							<?php
						}
						?>

						<?php
						if($setting['shipping_qty_col']==1)
						{
						?>
							<td style="<?php echo ($row['all_quantity']>1?'font-size:13px;font-weight:bold':'');?>"><?php echo $row['all_quantity'];?></td>
							<?php
						}
						?>

						<?php
						if($setting['shipping_status_col']==1)
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
				
			</table>
			</div>

			<div class="col-md-4 panel list-group" style="height:77vh;overflow: scroll">
						<div class="row">


						</div>
							<div class="col-md-12" style="">
									<div style="text-align: left;font-weight: bold; margin-bottom: 5px; background-color: #FAFAFA; border-color: #EEEEEE; padding: 3px;">ORDER # <span data-map="order_id">N/A</span><br>COMMENT: <span data-map="comment">-</span></div> <div class="panel-group" id="accordion"  style="font-size:12px">

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
														<td class="col-md-2 text-right">Requested </td>
														<td style="color:green" class="col-md-10" data-map="shipping_method" >N/A</td>

													</tr>
													<tr>
														<td class="col-md-2 text-right">Ship From</td>
														<td class="col-md-10" style="color:green">Las Vegas, NV. <a class=" fancybox3 fancybox.iframe" href="<?php echo $host_path;?>/inventory/shipping_setting.php"><i class="fa fa-link" style="margin-left: 5px; border: 1px solid #DCDCDC; padding: 4px; border-radius: 5px;cursor: pointer;"></i></a></td>
													</tr>
													<tr>
														<td class="col-md-2 text-right">Weight</td>
														<td class="col-md-10" >
															<div class="col-md-3" style="float:left">
																<input type="number" class="form-control form-control-md col-md-3" id="weight_lb" onkeyup="" data-attr="lb"   placeholder="(lb)" > 
															</div>
															<div class="col-md-1" style="margin-top:5px">(lb)</div>
															<div class="col-md-3" style="float:left">
																<input type="number" class="form-control form-control-md col-md-3" id="weight_oz" onkeyup="" data-attr="oz"  placeholder="(oz)" > 
															</div>
															<div class="col-md-5" style="margin-top:5px">(oz)</div>
															<!-- <div class="col-md-6"></div> -->
														</td>
													</tr>
													<tr>
														<td class="col-md-2 text-right">Service</td>
														<td class="col-md-10"><div class="form-group">

															<select class="form-control" id="service" data-map="service_map" >
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
															<input type="hidden" id="default_package_type" value="package">
														</div></td>
													</tr>

													<tr>
														<td class="col-md-2 text-right">Size</td>
														<td class="col-md-10" >		
																<div class="col-md-3" style="float:left">
															<input type="number" class="form-control form-control" id="size_length"  placeholder="L"  > 

															</div>
															<div class="col-md-1" style="margin-top:5px">
																	(L)
															</div>




																<div class="col-md-3" style="float:left">
															<input type="number" class="form-control form-control" id="size_width" placeholder="W" style="margin-left:5px;"  > 
															</div>
															<div class="col-md-1" style="margin-top:5px">
																	(W)
															</div>


																<div class="col-md-3" style="float:left">
															<input type="number" class="form-control form-control" id="size_height" placeholder="H" style="margin-left:5px"  > 
															</div>

															<div class="col-md-1" style="margin-top:5px">
																	(H)
															</div>


														</td>

													</tr>

													<tr>
														<td class="col-md-2 text-right">Confirm</td>
														<td class="col-md-10"><div class="form-group">

															<select class="form-control" id="confimration" >
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
														<td class="col-md-3">
															<input type="number" class="form-control form-control-md col-md-3" id="insured_amount" placeholder="Leave Blank if not Insured"  value="0.00" > 

														</td>

														<td class="col-md-7" style="margin-top:5px">
														<input type="checkbox" id="saturday_delivery" style="float:left;margin-top:2px;margin-right:5px" disabled=""><span style="color:grey;font-weight: bold">Saturday Delivery</span>
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
													<div class="col-md-4"><span data-map="customer_name"></span><br>
														<span data-map="telephone"></span><br>
														<span data-map="email"></span></div>
														<div class="col-md-2" style="font-weight: bold;padding-top:2.55%">Ship To</div>
														<div class="col-md-4"><span data-map="shipping_name"></span><br>
															<span data-map="address1"></span><br>
															<span data-map="city"></span>, <span data-map="state"></span> <span data-map="zip"></span> <span data-map="country"></span><br> 
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
															<div class="col-md-4" data-map="order_date"></div>
															<div class="col-md-2" style="font-weight:bold">Product Total</div>
															<div class="col-md-4" data-map="product_total">$0.00</div>

														</div>
														<div class="row">
															<div class="col-md-2" style="font-weight:bold">Ship By</div>
															<div class="col-md-4"></div>
															<div class="col-md-2" style="font-weight:bold">Shipping Paid</div>
															<div class="col-md-4" data-map="shipping_cost">$0.00</div>
														</div>
														
														<div class="row">
														<div class="col-md-2" style="font-weight:bold"></div>
															<div class="col-md-4"></div>
														
															<div class="col-md-2" style="font-weight:bold">Total Order</div>
															<div class="col-md-4" data-map="order_price">$0.00</div>
														</div>
														<div class="row">
															<div class="col-md-2" style="font-weight:bold"></div>
															<div class="col-md-4"></div>
															<div class="col-md-2" style="font-weight:bold">Total Paid</div>
															<div class="col-md-4" data-map="paid_price">$0.00</div>
														</div>
													</div>

												</div>
												<button class="btn btn-primary" id="create_label">Create Label</button>
											</div>
										</div>
									</div>
								</div>

							</div>
								</div>

							</div>


</div>