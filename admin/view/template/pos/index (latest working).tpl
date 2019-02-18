<!DOCTYPE html>
<html>
<head>
	<title>POS</title>
	<link rel="stylesheet" href="view/stylesheet/pos/style.css">
	<link rel="stylesheet" href="view/stylesheet/pos/iconFont.css">
	<link rel="stylesheet" href="view/stylesheet/pos/metro-bootstrap.css">
	<link rel="stylesheet" href="view/stylesheet/pos/jquery.bxslider.css">
	<link rel="stylesheet" href="view/stylesheet/pos/themes/ui-lightness/jquery-ui-1.8.16.custom.css">
	<link rel="stylesheet" href="view/javascript/pos/tinyscrollbar/tinyscrollbar.css">
	<link rel="stylesheet" href="view/javascript/pos/fancybox/jquery.fancybox.css">
	<link rel="stylesheet" href="view/stylesheet/pos/new-style.css">
	<script type="text/javascript" src="view/javascript/pos/jquery.min.js"></script>
	<script type="text/javascript" src="view/javascript/pos/print/printThis.js"></script>
	<script type="text/javascript" src="view/javascript/pos/tinyscrollbar/jquery.tinyscrollbar.min.js"></script>    
	<script type="text/javascript" src="view/javascript/pos/jquery.bxslider.js"></script>
	<script type="text/javascript" src="view/javascript/pos/jquery.keyboard.min.js"></script>
	<script type="text/javascript" src="view/javascript/pos/jquery-ui-1.8.16.custom.min.js"></script>
	<script type="text/javascript" src="view/javascript/pos/jquery-ui-timepicker-addon.js"></script> 
	<script type="text/javascript" src="view/javascript/pos/fancybox/jquery.fancybox.pack.js"></script>   
	<script type="text/javascript" src="view/javascript/pos/jquery.maskedinput-1.3.js"></script>
	<style type="text/css">
		.general-popup .order-ids-title {
			margin-top: 20px;
		}
		.input.small {
			width: 70px;
		}
		.span7 .total_wrapper.green::after {
			content: "";
			display: block;
			width: 50PX;
			position: absolute;
			background: #008000;
			height: 100%;
		}
		.span7 .total_wrapper.red::after {
			content: "";
			display: block;
			width: 50PX;
			position: absolute;
			background: #D50000;
			height: 100%;
		}
		.total_wrapper {
			position: relative;
		}
		.total_wrapper .pull-right {
			padding-left: 50px;
		}
	</style>
</head>
<body class="metro page-pos-home">
	<div class="container">    
		<div class="grid">
			<div class="row">
				<div class="span7">
					<div class="top_menu_wrapper">  
						<div class="pull-left">
							<div class="balance">  
								Cash : <?= $cash; ?><br>
								Credit : <?= $card; ?><br>
								PayPal : <?= $paypal; ?><br>
							</div>  
						</div>    

						<div class="pull-right">
							<div class="top_menu">  
								<ul> 
									<li>
										<a class="fancybox.ajax orders_list" href="index.php?route=pos/pos/orders&token=<?= $token ?>&picked_up_orders=false">
											<i class="icon-list"></i><br>
											<span>Awaiting Pickup</span> 
										</a>                          
									</li> 
									<li>
										<a class="fancybox.ajax orders_list" href="index.php?route=pos/pos/orders&token=<?= $token ?>&picked_up_orders=true"  >
											<i class="icon-search"></i><br>
											<span>Picked Up</span> 
										</a>                          
									</li>                        
									<li style="display:none">
										<a onclick="print_link(); return false;" href="#">
											<i class="icon-printer"></i><br>
											<span>Print</span> 
										</a>                          
									</li>                      
									<li>
										<a href="index.php?route=pos/pos/index&token=<?= $token ?>">
											<i class="icon-cycle"></i><br>
											<span>Refresh</span> 
										</a>                          
									</li>                      
									<li style="display:none">
										<a href="index.php?route=pos/pos/logout&token=<?= $token ?>">
											<i class="icon-user-2"></i><br>
											<span>Logout</span>
										</a>
									</li>
								</ul> 
							</div>
							<!-- END .top_menu --> 
						</div>
						<div class="clear"></div>     
					</div>
					<!-- END .top_menu_wrapper -->
					<div class='input_wrapper' id="div_transaction" style="display:none">
						<input class='input-element' readonly="" type="text"  id="transaction_id" style="background-color:#313439;color:#fff" />
						<input class='input-element' readonly="" type="text"  id="transaction_amount" style="background-color:#313439;color:#fff"  />                  

					</div>


					<div class='input_wrapper'>
						<input class='input-element' placeholder="Enter SKU" type="text" name="barcode" id="barcode" />                  
						<a style="height:36px" href="javascript:void(0)" onClick="if(confirm('Are you sure?')){cleardata();}" class="pull-right btn_cart_hold_count">CLEAR</a>
						<a style="height:36px;"  class="pull-right btn_cart_hold_add fancybox.ajax orders_list"  >COMBINE</a>
					</div>

					<div class="scrollbar_wrapper" id="scrollbar2">  
						<div class="scrollbar">
							<div class="track">
								<div class="thumb">
									<div class="end"></div>
								</div>
							</div>
						</div>
						<!-- scrollbar -->
						<div class="viewport" style="overflow-x:hidden;overflow-y:scroll;">
							<div class="overview">  
								<div class="order_head">
									<div class="stor_logo pull-left">
										<?= $this->config->get('config_name'); ?>
									</div>
									<div class="order_id pull-right">
										Order: Order ID
									</div>
									<div class="clear"></div>
									<hr />
									<div class="order_customer_name">Customer name</div>
									<hr />
								</div>    

								<table class='table table-bordered cart_table'>
									<thead>
										<tr>
											<th>Name</th>
											<th>Qty</th>
											<th>Price</th>
											<th>Tax</th>
											<th>Total</th>
											<th></th>
										</tr>  
									</thead>  
									<tbody>
                          <!--
                           <tr>
                               <td>MacBook<br></td>
                               <td><span class="minus">-</span><span class="qty">1</span><span class="plus">+</span></td>
                               <td>$500.00</td>
                               <td>$587.50</td>
                               <td><a data-key="43::" class="cart_remove"><i class="icon-cancel-2"></i></a></td>
                           </tr>
                       -->
                   </tbody>
               </table>
           </div>
       </div>
   </div>
   <!-- END #scrollbar2 -->
   
   <div class="total_wrapper">
   	<div class="pull-right">
   		<div>
   			<b>Sub total</b><br>
   			<span id="cart-total"><?= $this->currency->format('0.00') ?></span>
   		</div>
   		<div>
   			<b>TAX</b><br>
   			<span id="cart-total"><?= $this->currency->format('0.00') ?></span>
   		</div>
   		<div>
   			<b>Order Totals</b><br>
   			<span id="cart-total"><?= $this->currency->format('0.00') ?></span>
   		</div>
   	</div>      
   </div>
   
   <div class="input_wrapper discount_wrapper">
   	<div class="column_1" style="width: 100%;">
   		<?php if($dicount_status){ ?> 
   		<div class="input_row" style="display:none">
   			<input class='pull-left input-element' placeholder="Enter Discount" type="text" name="discount_amount" id="discount_amount" />
   			<div class="pull-left css3-metro-dropdown">
   				<select name="discount_type" id="discount_type">
   					<option value="F">Fixed</option>
   					<option value="P">Percentage</option>
   				</select>
   			</div>    
   			<button onclick="discount()" class="button">Apply Discount</button><br>
   			<div class="clear"></div>
   		</div>    
   		<?php }else{ ?>
   		<div class="label alert">
   			Discount module not enabled!
   		</div>
   		<?php } ?>  
   		<div class="input_row" style="display:none">
   			<input class='input-element' placeholder="Enter Coupon Code" type="text" name="coupon" id="coupon" />
   			<button onclick="coupon()" class="button">Apply Coupon</button><br>
   		</div>   
   		<div class="input_row" style="margin-bottom:7px;display:none" id="apply_voucher" >  
   			<input class='input-element' placeholder="Apply Store Credit" type="text" name="voucher" id="voucher" />
   			<button onclick="voucher()" class="button">Apply</button>
   			<span>OR</span>
   			<a class="btn_applyVoucher fancyboxOpen btn_cart_hold_count fancybox.ajax" style="display: inline-block;">Find Voucher</a>
   		</div>  
   		
   		<div class="input_row" id="paid_buttons" style="display:none">
   			
   			<button onclick="issue_refund()" class="button" style="display:none">Issue Refund</button>
   			<div style="float:left" >
   				<select id="sc_reason" style="width: 225px;" >
   					<option value="">Please Select</option>
   					<?php
   					foreach($sc_reasons as $sc_reason)
   					{

   					?>
   					<option value="<?php echo $sc_reason['code'];?>"><?=$sc_reason['name'];?> (<?=$sc_reason['code'];?>)</option>
   					<?php

   				}
   				?>
   			</select>
   			<button onclick="issue_sc()" class="button">Issue SC</button>
   		</div>
   	</div>


   </div>  

   <div class="clear"></div>
   <br>
   <div style="">



   	<div class="column_2">
   		<button class="command-button" id="cancel_order" style="background: none repeat scroll 0 0 #d50000;
   		color: white;
   		padding: 35px 17px 19px !important;
   		width: 145px;">
   		<center>
   			<i class="icon-remove"></i><br>
   			Void
   		</center>
   	</button>  
   </div>

   <div class="column_2" id="div_map_order" style="display:none">
   	<button class="command-button fancybox.ajax" href="index.php?route=pos/pos/track_order&token=<?= $token ?>" id="map_order" style="background: none repeat scroll 0 0 #1a237e;
   		color: white;
   		padding: 35px 17px 19px !important;
   		width: 145px;">
   		<center>
   			<i class="icon-cabinet"></i><br>
   			Map Order
   		</center>
   	</button>  
   </div>

   <div class="column_2">
   	<button id="order" href="#order_wrapper" class="command-button">
   		<center>
   			<i class="icon-dollar"></i><br>
   			Complete
   		</center>
   	</button>  
   </div>
</div>

<div class="clear"></div>
</div>    
</div>

<!-- END .span4 -->

<!--=========================== top category list ==========================-->

<form id="payment_method_paypal" style="display:none">
	<table>
		<tr>
			<td>Action:</td>
			<td>
				<select name="ppat_action" onchange="$('#ppat_response').html(''); if (this.value == 'Partial') { $('input[name=ppat_amount]').show(); } else { $('input[name=ppat_amount]').hide(); }">
					
					<option value="Partial" selected>Partial Refund</option>
				</select>
				<input  type="text" size="5" name="ppat_amount" value="0" />
				<input type="hidden" name="ppat_order_id" value="" />
				<input type="button" value="Submit" id="ppat_submit" />
			</td>
		</tr>
		<tr>
			<td>Environment:</td>
			<td><select name="ppat_env"><option value="live" <?php if($ppat_env == 'live'){ echo 'selected="selected"'; } ?> >Live</option><option value="sandbox" <?php if($ppat_env == 'sandbox'){ echo 'selected="selected"'; } ?>>Sandbox</option></td>
		</tr>
		<tr>
			<td>API User:</td>
			<td><input type="text" name="ppat_api_user" value="<?php echo $ppat_api_user; ?>" /></td>
		</tr>
		<tr>
			<td>API Pass:</td>
			<td><input type="text" name="ppat_api_pass" value="<?php echo $ppat_api_pass; ?>" /></td>
		</tr>
		<tr>
			<td>API Signature:</td>
			<td><input type="text" name="ppat_api_sig" value="<?php echo $ppat_api_sig; ?>" /></td>
		</tr>
	</table>
	<script type="text/javascript">
		$('#ppat_submit').live('click', function() {
			if (!confirm('Are you sure?')) {
				return false;
			}
			$.ajax({
				url: 'index.php?route=sale/order/ppat_doaction&token=<?php echo $token; ?>',
				type: 'post',
				data: $('#payment_method_paypal').serialize(),
				dataType: 'json',
				beforeSend: function() {
					$('#ppat_submit').attr('disabled', 'disabled');
				},
				complete: function() {
					$('#ppat_submit').removeAttr('disabled');
				},
				success: function(json) {
					$('.success, .warning').remove();

					if (json['error']) {
						alert(json['error']);
					}

					if (json['success']) {
						alert(json['success']);
						$('#order_update').removeAttr('disabled').click();
					}
					
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		});
	</script>
</form>

<div class="category_container span2 bg-black" style="">
	<span class="category_top_title">Category</span>
	<ul class="top_category_list">
		
		<?php 
		
		$color_array = array('bg-lightBlue','bg-darkViolet','bg-darkCyan','bg-violet','bg-indigo','bg-magenta');
		$i = 0;
		$length = sizeof($color_array);
		
		foreach($categories as $category){ ?>
		
		<li data-category-id="<?= $category['category_id'] ?>" class="<?= $color_array[$i] ?>">
			<center><img src="<?= $category['image'] ?>" width="70" /></center>
			<span><?= $category['name'] ?></span>
		</li>
		
		<?php $i++; if($i == $length) $i=0; } ?>
		
	</ul>
	
</div>         


<!-- END .span1 -->

<!---=========================  product list ============================-->
<div class="product_container" style="display:none">
	<div class="search_bar_wrapper">
		<div class="logged pull-left">
			<div class="label info"><?= $logged; ?></div>
		</div>    
		<div class="search_bar pull-right">
			<div class="input-control text size3 margin10 nrm">
				<input type="text" placeholder="Search..." name="q" id="q" />
				<button type="button" class="btn-search"></button>
			</div>
		</div>            
		<div class="logo pull-right">
			<h3>PPUSA POS</h3>
		</div>  
		<div class='clear'></div>  
	</div>
	<!-- END .search_bar_wrapper -->          
	
	<div class="product_list">  
		<div class="scrollbar_wrapper" id="scrollbar1">  
			<div class="scrollbar">
				<div class="track">
					<div class="thumb">
						<div class="end"></div>
					</div>
				</div>
			</div>
			<!-- scrollbar -->
			<div class="viewport">
				<div class="overview">                      
				</div>
			</div>
		</div>  
	</div>
	<!-- END .product_list -->
	
	<div class="product_pager hide">
		<button class="button info large pull-right">Load more...</button>
	</div>
	<div class="footer_timer">
		<span></span>
	</div>
	<div class="clear"></div>
	<div class="product-info product_list_bottom hide">
		<input type="hidden" name="product_id" class="product_id" />
		<div id="option"></div>              
	</div>
</div>
<!-- END .span6 -->
</div>
</div>   
<!-- END .grid -->
</div>
<!-- END .page -->   

<!--========================================= hold cart list pop up ============================================-->
<div class="hide">    
	<div id="hold_carts_wrapper">
		<h3>Holded Cart</h3><hr>
		<table class="table striped">
			<thead>  
				<tr>
					<th>Name</th>
					<th>Date created</th>
					<th>Action</th>
				</tr>
			</thead>  
			<tbody>
				<?php foreach($hold_carts as $cart){ ?>  
				<tr>
					<td><?= $cart['name'] ?></td>
					<td align="center"><?= $cart['date_created'] ?></td>
					<td align="center">
						[<a data_cart_holder_id='<?= $cart["cart_holder_id"] ?>' href="#" class="select">Select</a>]&nbsp;
						[<a data_cart_holder_id='<?= $cart["cart_holder_id"] ?>' href="#" class="delete">Delete</a>]
					</td>
				</tr>
				<?php } ?>  
			</tbody>
		</table>
	</div>
	<!-- END .hold_wrapper -->   

	<!--========================== cart to hold pop up =========================================-->
	<div id="hold_wrapper">
		<div class="hold_form">
			<h3>Put Current Cart to Hold</h3><hr>        
			<div class="message_wrapper"></div>        
			<div class="grid">
				<div class="row">                
					<div class="span4">
						<div data-role="input-control" class="input-control text">
							<input id="hold_name" type="text" name="hold_name" placeholder="Enter Hold Name">
							<button id="hold_confirm" class="button">Apply</button>
						</div>
					</div>
					<!-- END .span4 -->
				</div>
				<!-- END .row -->
			</div>
			<!-- END .grid -->
		</div>
		<!-- END .hold_form -->
	</div>
	<!-- END .hold_wrapper -->    
	
	<!--========================== order pop up =========================================-->
	<div id="order_wrapper" class="general-popup">
		<div style="display: none;">
			<input type="text" name="paid" placeholder="paid" readOnly />
			<input id="customer_name" type="text" name="customer_name" placeholder="Type and Select Customer ">
			<input type="hidden" name="customer_id" />
			<input type="hidden" name="order_id" />
			<input type="hidden" id="xpayment_method" value="">
			<input type="hidden" id="is_cancel" value="">
			<input type="hidden" id="xpayment_type" value="">
			<input type="hidden" id="payment_type" value="">
			<input type="hidden" id="item_selected" value="">
			<input type="hidden" id="removed_items" name="removed_items" value="">
			<input type="hidden" id="all_items" name="all_items" value="">
			<input type="hidden" id="pickup_status" value="">
			<input type="hidden" id="xtotal" value="0.00">
			<input type="checkbox" name="split_payment" value="1" /> 
			<input name="is_guest" value="1" type="checkbox" />
			<input type="checkbox" name="round_value"   onChange="roundFunction()" /> 
		</div>
		<div class="pos-row">
			<div class="pos-left">
				<div class="pos-left-inner">
					<div class="order-ids">
						<p class="order-ids-title">Order ID (s):</p>
						<ul class="order-ids-list">
							
						</ul>
					</div>
				</div>
				<table class="pos-customer-table">
					<tbody>
						<tr>
							<td>Date/time:</td>
							<td id="pickup-date"></td>
						</tr>
						<tr>
							<td>Name:</td>
							<td id="customer-name"></td>
						</tr>
						<tr>
							<td>Email:</td>
							<td id="customer-email"></td>
						</tr>
						<tr>
							<td>Tax Paid:</td>
							<td id="tax-paid">$0.00</td>
						</tr>
						<tr>
							<td>Amount Due:</td>
							<td id="amount-due">$0.00</td>
						</tr>
					</tbody>
				</table>
				<table class="pos-total">
					<tbody>
						<tr class="paid-amount">
							<td>Amount Paid:</td>
							<td id="paid-amount">0.00</td>
							<input type="hidden" name="paid-amount">
						</tr>
						<tr class="changed-due">
							<td>Change Due:</td>
							<td id="changed-due">0.00</td>
							<input type="hidden" name="changed-due">
						</tr>
					</tbody>
				</table>
			</div>
			<div class="pos-right">
				<div class="payment-method">
					<div class="form-list">
						<div class="form-row">
							<div class="form-lbl">
								Select Payment Method:
							</div>
							<div class="form-data">
								<select class="custom-dropdown" name="payment_method" onChange="changePMethod();">
									<option>Cash</option>
									<option>Card</option>
									<option>Card/Cash</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="pos-right-inner">
					<div class="form-list">
						<div class="form-row div_card_payment" style="display: none;">
							<div class="form-lbl">
								Card paid:
							</div>
							<div class="form-data clearfix last-digit">
								<div class="form-col2">
									<!-- <input type="number" class="input small" data-part="1" min="0" placeholder="00"  onkeyup="mergeAmount($(this).parent())" onchange="mergeAmount($(this).parent())"/>
									.
									<input type="number" class="input small" data-part="2" max="99" min="0" placeholder="00"  onkeyup="mergeAmount($(this).parent())" onchange="mergeAmount($(this).parent())"/> -->
									<strong>$</strong> <input type="text" class="input datainput" name="card-payment" style="width: 85%;" readonly placeholder="0.00"/>
								</div>
								<div class="form-col2">
									<div class="form-row">
										<div class="form-lbl">
											Last 4 Digits:
										</div>
										<div class="form-data">
											<input type="text" class="mask-card input" name="card" class="input" placeholder="1234">
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="form-row div_split_payment d_s_pay" style="display: none;">
							<div class="form-lbl">
								Card paid:
							</div>
							<div class="form-data clearfix last-digit">
								<div class="form-col2">
									<strong>$</strong> 
									<input type="number" class="input small split_cardx" data-part="1" min="0" placeholder="00"  onkeyup="mergeAmount($(this).parent())" onchange="mergeAmount($(this).parent())"/>
									<strong>.</strong>
									<input type="number" class="input small split_cardx" data-part="2" max="99" min="0" placeholder="00"  onkeyup="mergeAmount($(this).parent())" onchange="mergeAmount($(this).parent())"/>
									<input type="hidden" class="input datainput" name="split_card" placeholder="0.00"/>
								</div>
								<div class="form-col2">
									<div class="form-row">
										<div class="form-lbl">
											Last 4 Digits:
										</div>
										<div class="form-data">
											<input type="text" class="mask-card input" name="card_split" class="input" placeholder="1234">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-row div_split_payment" style="display: none;">
							<div class="form-lbl">
								Cash paid:
							</div>
							<div class="form-data">
								<strong>$</strong> 
								<input type="number" class="input small" data-part="1" min="0" placeholder="00"  onkeyup="mergeAmount($(this).parent())" onchange="mergeAmount($(this).parent())"/>
								<strong>.</strong>
								<input type="number" class="input small" data-part="2" max="99" min="0" placeholder="00"  onkeyup="mergeAmount($(this).parent())" onchange="mergeAmount($(this).parent())"/>
								<input type="hidden" class="input datainput" name="split_cash" placeholder="0.00" />
							</div>
						</div>
						<div class="form-row div_cash_payment" style="display: none;">
							<div class="form-lbl">
								Cash paid:
							</div>
							<div class="form-data">
								<strong>$</strong> 
								<input type="number" class="input small" data-part="1" min="0" placeholder="00"  onkeyup="mergeAmount($(this).parent())" onchange="mergeAmount($(this).parent())"/>
								<strong>.</strong>
								<input type="number" class="input small" data-part="2" max="99" min="0" placeholder="00"  onkeyup="mergeAmount($(this).parent())" onchange="mergeAmount($(this).parent())"/>
								<input type="hidden" class="input datainput" name="cash_paid_cash" placeholder="0.00" />
							</div>
						</div>
						<div class="form-row">
							<div class="form-lbl">
								Comment:
							</div>
							<div class="form-data">
								<textarea class="ta"></textarea>
							</div>
						</div>
						<div class="form-btn text-center">
							<button id="order_confirm" disabled="disabled" class="btngreen">Complete Order</button>
						</div>
					</div>	
				</div>
			</div>
		</div>
	</div>
	<!-- END order_wrapper -->


	<div id="refund_popup">
		
	</div>
	<!--========================== order pop up =========================================-->
	<div id="removal_items">
		<div >
			<h3>Provide Removal Reason</h3><hr>        
			<div class="message_wrapper"></div>        
			<div class="grid">
				
				<!-- END .row -->
				
				<!-- END .row -->
				<div class="row">
					<div class="span1">
						<span class="label2">Reason</span>
					</div>
					<div class="span3">
						<div style="float: left !important" class="css3-metro-dropdown">
							<select id="return_reason">
								<?php
								foreach($reasons as $reason)
								{
								?>
								<option value="<?php echo $reason['reason_id'];?>" <?php if($reason['reason_id']==$reason_id) echo 'selected';?>><?php echo $reason['name'];?></option>
								<?php

							}

							?>
						</select>
					</div>                        


				</div>

			</div>



			<div class="row">
				<div class="span6" style="text-align:center">
					<button onClick="removeItem()">Remove Item</button>
				</div>

				<!-- END .span4 -->
			</div>

		</div>
		<!-- END .grid --> 
	</div>
</div>
<!-- END order_wrapper -->


<div id="refund_items">
	<div >
		<h3>Refund</h3><hr>        
		<div class="message_wrapper"></div>        
		<div class="grid">

			<!-- END .row -->

			<!-- END .row -->


			<iframe style="width:1000px;height:720px"></iframe>



		</div>
		<!-- END .grid --> 
	</div>
</div>

</div>
<!-- END .hide -->
</body>
</html>

<script type="text/javascript">
	function applyVouchers() {

	}

	function mergeAmount (parentHolder) {
		var pricePart1 = parentHolder.find('input[data-part="1"]');
		var pricePart2 = parentHolder.find('input[data-part="2"]');
		var bAmount = pricePart1.val();
		var dAmount = pricePart2.val();
		var inputHolder = parentHolder.find('.datainput');
		
		var cardPaid = bAmount + '.' + dAmount;
		var amountPaid = parseFloat((cardPaid) ? cardPaid : 0);
		
		var due = $('input[name="paid"]').val();
		var amountDue = parseFloat((due) ? due : 0);
		var brokerAmount = due.split(".");
		if (amountPaid < 0) {
			amountPaid = 0.00;
			pricePart1.val(00);
			pricePart2.val(00);
		}
		inputHolder.val(amountPaid);
		if (pricePart1.hasClass('split_cardx')) {
			if (amountPaid > amountDue) {
				pricePart1.val(brokerAmount[0]);
				pricePart2.val(brokerAmount[1]);
				if ($('.d_s_pay .error').length == 0) {
					$('.d_s_pay').prepend('<div class="error">You can\'t put more amount than Due</div>');
				}
			} else {
				$('.d_s_pay .error').remove();
			}
		}
		cashChangeDue();
	}

	function cashChangeDue () {

		var textChangeDue = $('#changed-due');
		var inputChangeDue = $('input[name="changed-due"]');
		var textPaidAmount = $('#paid-amount');
		var inputPaidAmount = $('input[name="paid-amount"]');
		textChangeDue.text('0.00');
		inputChangeDue.val('0.00');

		$('#order_update').attr('disabled', 'disabled');

		var cp = $('input[name="split_card"]').val();
		var due = $('input[name="paid"]').val();
		var amountDue = parseFloat((due) ? due : 0);
		var apC = 'cash_paid_cash';
		cxp = 0;
		if ($('select[name="payment_method"]').val() == 'Card/Cash') {
			cxp = parseFloat((cp) ? cp : 0);
			apC = 'split_cash';
		}
		var ap = $('input[name="'+ apC +'"]').val();
		var amountPaid = parseFloat((ap) ? ap : 0) + cxp;
		var changeDue = amountPaid - amountDue;
		textPaidAmount.text(amountPaid.toFixed(2));
		inputPaidAmount.val(amountPaid.toFixed(2));
		if (changeDue >= 0 && $('select[name="payment_method"]').val() == 'Cash' && amountPaid > 0) {
			textChangeDue.text(changeDue.toFixed(2));
			inputChangeDue.val(changeDue.toFixed(2));
			$('#order_update').removeAttr('disabled');
		} else if ($('select[name="payment_method"]').val() == 'Card') {
			textChangeDue.text('0.00');
			inputChangeDue.val('0.00');
			textPaidAmount.text(amountDue.toFixed(2));
			inputPaidAmount.val(amountDue.toFixed(2));
			$('input[name="card-payment"]').val(amountDue.toFixed(2));
			$('#order_update').removeAttr('disabled');
		} else if ($('select[name="payment_method"]').val() == 'Card/Cash' && changeDue >= 0) {
			textPaidAmount.text(amountPaid.toFixed(2));
			inputPaidAmount.val(amountPaid.toFixed(2));
			textChangeDue.text(changeDue.toFixed(2));
			inputChangeDue.val(changeDue.toFixed(2));
			$('#order_update').removeAttr('disabled');
		}

		if(amountDue==0)
		{
			$('#order_update').removeAttr('disabled');
			
		}

	}

	function changePMethod() {
		$('input[name=split_payment]').prop('checked', false);
		$('#changed-due').text('0.00');
		$('#paid-amount').text('0.00');
		$('input[name="card-payment"]').val('0.00');
		$('input[name=split_cash]').val('');
		$('input[name=split_card]').val('');
		$('.div_cash_payment').hide();
		$('.div_card_payment').hide();
		$('.div_split_payment').hide();
		var e = $('select[name="payment_method"]');
		$('input[name="cash_paid_cash"]').val('');
		if ($(e).val() == 'Card') {
			$('.div_card_payment').fadeIn();
			$('#order_update').removeAttr('disabled');
		} else if ($(e).val() == 'Cash') {
			$('.div_cash_payment').fadeIn();
		} else if ($(e).val() == 'Card/Cash') {
			$('.div_split_payment').fadeIn();
			$('input[name=split_payment]').prop('checked', true);
		}
		cashChangeDue();
	}
	
	function splitPayment()
	{
		$('select[name="payment_method"]').val('Cash');
		var delay = 500;
		if($('input[name=split_payment]').is(':checked'))
		{
			$('#div_payment_method').hide(delay);
		}
		else
		{
			$('#div_payment_method').show(delay);	
		}
		changePMethod();
		cashChangeDue();
	}

	function roundFunction()
	{
		// Zaman Commented to depreciate the round thing


		/*if($('select[name=payment_method]').val()=='Cash'){
			$('#tr-round').show(500);	

		}
		else
		{
			$('input[name=round_value]').attr('checked',false);
			$('#tr-round').hide(500);	

		}
		if($('input[name=round_value]').is(':checked')){
			var roundValue = 0.00;
			if($('#xtotal').val()<=5.00)
			{
				roundValue = Math.ceil($('#xtotal').val());
			}
			else
			{
				roundValue = Math.round($('#xtotal').val());

			}
			$('input[name=paid]').val(roundValue);
		}
		else
		{
			$('input[name=paid]').val($('#xtotal').val());   
		}*/


	}

	function changeSplitAmount()
	{

		var bAmount = $('input[data-part="1"]').val();
		var dAmount = $('input[data-part="2"]').val();
		$('input[name=split_card]').val(bAmount + '.' + dAmount);
		var cardPaid = parseFloat($('input[name=split_card]').val());
		var total = parseFloat($('input[name=paid]').val());
		var cashDue = total - cardPaid;
		//$('input[name=split_cash]').val(cashDue.toFixed(2));
		cashChangeDue();

                    	// $('input[name=round_value]').attr('checked',false);
                    	// var cash = parseFloat($('input[name=split_cash]').val());
                    	// var total = $('input[name=paid]').val();
                    	
                    	// var card = parseFloat(total) - parseFloat(cash);
                    	// card = card.toFixed(2);
                    	// $('input[name=split_card]').val(card);
                    	
		//amount = cash+card;
		//if(amount=='NaN') amount = 0.00;
		
		//$('#xtotal').val(amount);
		
		
		
		//$('input[name=paid]').val(amount);
		
		
		
	}

	var x = new Date();

	var total_hold = '<?= sizeof($hold_carts); ?>';

	var total = 0;

	$('input[name="paid"]').keyup(function(){    
		$.get('index.php?route=pos/pos/get_total&token=<?php echo $token; ?>',function(data){
			var paid = parseFloat($('input[name="paid"]').val()) || 0;     
			var total = data;     
			var change = paid - total;
			$('.change_amount').html('- Total = '+ change);
		});
	});

//put cart to hold on 
$('#hold_carts_wrapper .select').live('click',function(){  
	$this = $(this);  
	$.post('index.php?route=pos/pos/hold_cart_select&token=<?php echo $token; ?>',{ cart_holder_id: $this.attr('data_cart_holder_id') }, function(data){
		var json = JSON.parse(data);
		
     //delete from db
     $.post('index.php?route=pos/pos/hold_cart_delete&token=<?php echo $token; ?>',{ cart_holder_id: $this.attr('data_cart_holder_id') }, function(data){
     	$this.parent().parent().remove();
     	$('.btn_cart_hold_count').html('HOLD: '+ --total_hold);
     });     
     
     //update cart from hold
     update_cart(json['products'], json['total_data']);      
     
     //close fancybox 
     $('.fancybox-close').trigger('click');
 });    
});

$('#hold_carts_wrapper .delete').live('click',function(){
	$this = $(this);  
	$.post('index.php?route=pos/pos/hold_cart_delete&token=<?php echo $token; ?>',{ cart_holder_id: $this.attr('data_cart_holder_id') }, function(data){
		$this.parent().parent().remove();
		$('.btn_cart_hold_count').html('HOLD: '+ --total_hold);
	});     
});

$('#hold_confirm').click(function(){
	$.post('index.php?route=pos/pos/hold_cart&token=<?php echo $token; ?>',{ name: $('#hold_name').val() }, function(data){
		var data = JSON.parse(data);
		
		if(data['error']){
			$('.message_wrapper').html('<div class="warning">'+data['error']+'</div>');
		}
		
		if(data['success']){
			$('.fancybox-close').trigger('click');
			$('#hold_carts_wrapper table tr').last().after(data['html']);
			total_hold++;
			$('.btn_cart_hold_count').html('HOLD: '+total_hold);
		}
		
		$('#hold_name').val('');
	});    
});

/*$(".btn_cart_hold_add").fancybox({
    maxWidth	: 370,
    maxHeight	: 420,
    autoSize	: true,
});*/

/*$(".btn_cart_hold_count").fancybox({
    maxWidth	: 470,
    maxHeight	: 420,
    autoSize	: false,
});*/

//autocomplete attribute name 
$("#customer_name").autocomplete({
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=pos/pos/searchCustomer&token=<?php echo $token; ?>&q=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {	
				response($.map(json, function(item) {
					return {
						label: item.firstname +' '+item.lastname,
						value: item.customer_id
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('input[name=\'customer_name\']').attr('value', ui.item.label);
		$('input[name=\'customer_id\']').attr('value', ui.item.value);

		return false;
	},
	focus: function(event, ui) {
		return false;
	}
});

$(".mask-card").mask("9999");     

/* add order 
customer_id
is_guest 
payment_method
card
    $('input[name="is_guest"]:checked')
    */
    $('#order').click(function(e){
    //$('input[name="paid"]').val('');
    $('.change_amount').html('- Total = '+ 0);
    $('.message_wrapper').html('');
    $('input[name="card"]').val('');
    roundFunction();
    if($('#xpayment_method').val()=='paid')
    {
    	
    	
    	
    	if($('#xpayment_type').val().toLowerCase()=='paypal' || $('#xpayment_type').val().toLowerCase()=='paypal express')
    	{
    		
    		if($('#removed_items').val()=='')
    		{
    			if(confirm('Are you sure to complete this transaction?'))
    			{
    				
    				$('select[name=payment_method]').append('<option>'+$('#xpayment_type').val()+'</option>');
    				//$('select[name=payment_method] option:eq(2)').attr('selected','selected');
    				$('select[name=payment_method]').val($('#xpayment_type').val());
    				//roundFunction();
    				$('#order_update').removeAttr('disabled').click();
    				
    				
    			}
    		}
    		else
    		{
    			
			//$.fancybox.open( $('#refund_popup') );	
			refundPaid();
			
		}
		return false;
	}
	
		/*if(parseFloat($('#xtotal').val())<parseFloat($('input[name=paid]').val()))
		{	
		 e.preventDefault();
		$('#paid_buttons').show('slow');
		
		return false;
	}	*/	
}
});
    function refundPaid(type)
    {
    	type = typeof type !== 'undefined' ? type : 'complete';

    	if(type=='complete')
    	{

    		$items = 	$('#removed_items').val();
    	}
    	else
    	{
    		$items = 	$('#all_items').val();

    	}

    	$.ajax({
    		url: 'index.php?route=pos/pos/refund_paid&token=<?php echo $token; ?>',
    		type: 'post',
    		data: {items:$items,type:type,order_id:$('input[name="order_id"]').val()},

    		beforeSend: function() {
		//	$('#button-confirm-gv').attr('disabled', true);
		$('.command-button').prop('disabled','disabled');
	},
	complete: function() {
			//$('#button-confirm-gv').attr('disabled', false);
		//	$('.attention').remove();
		$('.command-button').prop('disabled','');
	},				
	success: function(data) {
		$('#refund_popup').html(data);
		$.fancybox.open( $('#refund_popup') );
	}
});


    }
    function refundAndProceed(order_id,total,type)
    {
    	$.ajax({
    		url: 'index.php?route=sale/order/ppat_doaction&token=<?php echo $token; ?>',
    		type: 'post',
    		data: {ppat_api_user:'<?php echo $ppat_api_user;?>',ppat_api_pass:'<?php echo $ppat_api_pass;?>',ppat_api_sig:'<?php echo $ppat_api_sig;?>',ppat_env:'<?php echo $ppat_env;?>',ppat_order_id:order_id,ppat_action:'Partial',ppat_amount:total},
    		dataType:"json",
    		beforeSend: function() {
		//	$('#button-confirm-gv').attr('disabled', true);
		$('#refundAndProcess').prop('disabled','disabled');
	},
	complete: function() {
			//$('#button-confirm-gv').attr('disabled', false);
		//	$('.attention').remove();
		$('#refundAndProcess').prop('disabled','');
	},				
	success: function(json) {
			//if(json['success'])
			{
				if(type=='complete')
				{
					$('select[name=payment_method]').append('<option>'+$('#xpayment_type').val()+'</option>');
					$('select[name=payment_method] option:eq(2)').attr('selected','selected');
					roundFunction();
					$('#order_update').removeAttr('disabled').click();	
				}
				else
				{
					$('select[name=payment_method]').append('<option>'+$('#xpayment_type').val()+'</option>');
					$('select[name=payment_method] option:eq(2)').attr('selected','selected');
					roundFunction();
					
					cancelIt();
					
				}
			}
			/*else if(json['error'])
			{
				alert(json['error']);
				return false;
				
			}*/
		}
	});

    }
    function issue_sc()
    {

    	if(!confirm('Are you sure to issue a store credit and complete order'))
    	{
    		return false;	
    	}

    	if($('#sc_reason').val()=='')
    	{
    		alert('Please select the Reason for Store Credit');
    		return false;	

    	}


    	$.ajax({
    		url: 'index.php?route=sale/voucher/voucher_payment&token=<?php echo $token; ?>&order_code_type='+$('#sc_reason').val()+'&order_id='+$('input[name="order_id"]').val(),
    		type: 'post',
    		data: {generate_gv:parseFloat($('#xtotal').val()) ,message:'Store Credit issued from POS'},
    		dataType: 'json',		
    		beforeSend: function() {
		//	$('#button-confirm-gv').attr('disabled', true);
		
	},
	complete: function() {
			//$('#button-confirm-gv').attr('disabled', false);
		//	$('.attention').remove();
	},				
	success: function(json) {
		if (json['error']) {
					//$('#aat_response').html('<div class="warning"  style="display: none;color:red">' + json['error'] + '</div>');
					alert(json['error']);return false;
					//$('.warning').fadeIn('slow');
				}

				if (json['success']) {
					alert(json['success']);
					$('#order_update').removeAttr('disabled').click();
				  // location.reload(true);
				}
			}
		});

    }

    function issue_refund()
    {
    	var amount = parseFloat($('#xtotal').val()) - parseFloat($('input[name=paid]').val());
    	$('input[name=ppat_amount]').val(amount.toFixed(2));
    	$('input[name=ppat_order_id]').val($('input[name="order_id"]').val());
    	$('#ppat_submit').click();
    }


    $('#order_confirm').live('click',function(){
    	$(this).val('Sending data...');
    	$.post('index.php?route=pos/pos/addOrder&token=<?php echo $token; ?>', 
    		{ card_no: $('.mask-card').val(), customer_id: $('input[name="customer_id"]').val(), is_guest: $('input[name="is_guest"]').is(':checked') , payment_method: $('select[name="payment_method"]').val(), comment: $('textarea[name="order_comment"]').val() }, function(data){
    			var data = JSON.parse(data);
    			var html = '';

    			if(data['errors']){
    				$('.message_wrapper').html("<div class='warning'>"+data['errors']+"</div>");             
    			}
    			if(data['success']){
            //$('.message_wrapper').html("<div class='success'>"+data['success']+"</div>");             
            $('.fancybox-close').trigger('click');
            //alert('New order placed with ID: '+data.order_id);
            $('#order_confirm').val('Done');            
            $('textarea[name="order_comment"]').val('').html('');
            $('.order_head .order_id').html('Order: '+data['order_id']);
            $('.balance').html('Cash : '+data['cash']+'<br>Card : '+data['card']);
            $('.order_customer_name').html(data['customer_name']+'<span class="pull-right">'+x.toDateString() + ', ' +  x.toLocaleTimeString()+'</span>');
            print();
        }
    });
    });

    $('#order_update').live('click',function(){
    	$(this).val('Sending data...');
    	$.post('index.php?route=pos/pos/editOrder&token=<?php echo $token; ?>', 
    		{ card_no: $('.mask-card').val(), order_id: $('input[name="order_id"]').val(), amountPaid: $('input[name="paid-amount"]').val(), changeDue: $('input[name="changed-due"]').val(), customer_id: $('input[name="customer_id"]').val(), is_guest: $('input[name="is_guest"]').is(':checked') , payment_method: $('select[name="payment_method"]').val(),comment: $('textarea[name="order_comment"]').val(),removed_items:$('#removed_items').val(),pos_total:$('input[name=paid]').val(),split_payment:$('input[name=split_payment]').is(':checked'),split_cash:$('input[name=split_cash]').val(),split_card:$('input[name=split_card]').val(),card_split:$('input[name=card_split]').val(),is_cancel:$('#is_cancel').val(),transaction_id:$('#transaction_id').val(),transaction_amount:$('#transaction_amount').val()}, function(data){
    			var data = JSON.parse(data);
    			var html = '';

    			if(data['errors']){
    				$('.message_wrapper').html("<div class='warning'>"+data['errors']+"</div>");             
    			}

    			if(data['success']){
            //$('.message_wrapper').html("<div class='success'>"+data['success']+"</div>");             
            $('.fancybox-close').trigger('click');
            //alert('New Order Placed with ID: '+data.order_id);
            $('#order_update').val('Done');   
            $('.order_head .order_id').html('Order: '+data['order_id']);
            $('.balance').html('Cash : '+data['cash']+'<br>Card : '+data['card']);     
            $('.order_customer_name').html(data['customer_name']+'<span class="pull-right">'+x.toDateString() + ', ' +  x.toLocaleTimeString()+'</span>');
            print();    
            
            //change to new order mode 
            $('textarea[name="order_comment"]').val('').html('');
            $('input[name="order_id"]').val('');
            $('#order_update').attr('id','order_confirm').html('Order Now');
            $('.order_form h3').html('Place New Order');
            $("#paid_buttons").hide('slow');
            $('select[name=payment_method] option:eq(2)').remove();
            $('#xpayment_method').val('');
            $('#xpayment_type').val('');
            window.location='index.php?route=pos/pos/index&token=<?= $token ?>';
        }
    });
    });
    $('#cancel_order').live('click',function(){


    	if($('#xpayment_method').val()=='paid')
    	{



    		if($('#xpayment_type').val().toLowerCase()=='paypal' || $('#xpayment_type').val().toLowerCase()=='paypal express')
    		{




    			if(confirm('Are you sure to cancel the order(s)?'))
    			{

    				if($('#all_items').val()!='')
    				{

    					refundPaid('void');
    				}
    				else
    				{
    					cancelIt();

    				}
    			}




    		}
    	}
    	else
    	{

    		if(!confirm('Are you sure to cancel the order(s)?'))
    		{

    			return false;
    		}


    		$(this).val('Sending data...');
    		cancelIt();
    	}
    });



    function cancelIt()
    {
	/*	
	 $.post('index.php?route=pos/pos/cancelOrders&token=<?php echo $token; ?>', 
      { order_ids: $('input[name="order_id"]').val()}, function(data){
         
        
         
         
			 cleardata();
			 location.reload(true);
			 
		
			});*/

			$('#is_cancel').val('cancel');
			$('#order_update').removeAttr('disabled').click();

		}
		function cleardata(){    
    //update total 
    $html  = '<div class="pull-right"><div><b>Sub total</b><br><span id="cart-total">'; 
    $html += '<?= $this->currency->format("0.00") ?>';
    $html += '</span></div><div><b>Order Totals</b><br><span id="cart-total">';
    $html += '<?= $this->currency->format("0.00") ?>';
    $html += '</span></div></div>';    
    $('.total_wrapper').html($html);
    
    //remove cart
    $('.cart_table tbody tr').remove();
    $('.btn_cart_hold_add').removeAttr('href');
}

var oScrollbar1, oScrollbar2 = null;

//$(".scrollbar_wrapper").tinyscrollbar();
//oScrollbar1.tinyscrollbar_update();

$(document).ready(function(){
	
	oScrollbar1 = $("#scrollbar1");
	oScrollbar1.tinyscrollbar();
	
	oScrollbar2 = $("#scrollbar2");
	oScrollbar2.tinyscrollbar();

	$('.top_category_list').bxSlider({
		mode: 'vertical',
		minSlides: 6,
		infiniteLoop: false, 
		pager: false,     
	});
});

getItems("<?= $categories[0]['category_id'] ?>",1);


$("#order").fancybox({
	padding:0,
	wrapCSS:'general-fancy'
});
/*
$("#order").fancybox({
	maxWidth	: 820,
	maxHeight	: 485,
	fitToView	: false,
	width		: '80%',
	height		: '70%',
	autoSize	: false,
	closeClick	: false,
	openEffect	: 'none',
	closeEffect	: 'none',
	padding:0,
	wrapCSS:'general-fancy'
});
*/




$(".orders_list,#map_order,.fancyboxOpen").fancybox({
	maxWidth	: 1200,
	maxHeight	: 620,
	fitToView	: false,
	autoSize	: true,
	closeClick	: false,
	openEffect	: 'none',
	closeEffect	: 'none'
});




//list category products 
$('.product_list .product').live('click',function(){
	
	$('.product_list .selected').removeClass('selected');
	$(this).find('.tile').addClass('selected');        
	$('.product-info .product_id').val($(this).attr('data-product-id'));        
	
    var has_option = $(this).attr('data-has-option');//getProductOptions
    
    if(has_option==1){
    	$('.product_list_bottom').removeClass('hide');
    	get_option($(this).attr('data-product-id'));
    }else{        
    	$('.product_list_bottom').addClass('hide');
    	addToCart();
    }
    
});

//list category products 
$('.top_category_list li, .product_list .category').live('click',function(){
	getItems($(this).attr('data-category-id'),1);
});

//cart qty update 
$('.cart_table .minus').live('click',function(){
	$qty = $(this).parent().find('.qty');
	$qty_value = parseInt($qty.html());
	$key = $qty.attr('data-key');
	
	if($qty_value == 1) return false;
	
	$qty.html($qty_value--);
	
	$.post('index.php?route=pos/pos/updateCart&token=<?php echo $token; ?>',{ key: $key , quantity: $qty_value }, function(data){
		var json = JSON.parse(data);
		update_cart(json['products'], json['total_data']); 
	});
});

$('.cart_table .plus').live('click',function(){
	$qty = $(this).parent().find('.qty');
	$qty_value = parseInt($qty.html());
	$key = $qty.attr('data-key');
	$qty.html($qty_value++);
	
	$.post('index.php?route=pos/pos/updateCart&token=<?php echo $token; ?>',{ key: $key , quantity: $qty_value }, function(data){
		var json = JSON.parse(data);
		update_cart(json['products'], json['total_data']);        
	});

});

function get_option($id){
	$.post('index.php?route=pos/pos/getProductOptions&token=<?php echo $token; ?>',{ product_id: $id }, function(data){
		var html = '';
		var data= JSON.parse(data);
		var product_option = data['option_data'];
		
		for (var i = 0; i < product_option.length; i++) {
			var option = product_option[i];

			if (option['type'] == 'select') {
				html += '<div id="option-' + option['product_option_id'] + '">';

				if (option['required']) {
					html += '<span class="required">*</span> ';
				}

				html += option['name'] + '<br />';
				html += '<div class="css3-metro-dropdown">';
				html += '<select name="option[' + option['product_option_id'] + ']">';
                    // html += '<option value=""><?php echo $text_select; ?></option>';

                    for (j = 0; j < option['option_value'].length; j++) {
                    	option_value = option['option_value'][j];

                    	html += '<option value="' + option_value['product_option_value_id'] + '">' + option_value['name'];

                    	if (option_value['price']) {
                    		html += ' (' + option_value['price_prefix'] + option_value['price'] + ')';
                    	}

                    	html += '</option>';
                    }

                    html += '</select>';
                    html += '</div></div>';
                    html += '<br />';
                }

                if (option['type'] == 'radio') {
                	html += '<div id="option-' + option['product_option_id'] + '">';

                	if (option['required']) {
                		html += '<span class="required">*</span> ';
                	}

                	html += option['name'] + '<br />';
                	html += '<div class="css3-metro-dropdown">';
                	html += '<select name="option[' + option['product_option_id'] + ']">';
                    //html += '<option value=""><?php echo $text_select; ?></option>';

                    for (j = 0; j < option['option_value'].length; j++) {
                    	option_value = option['option_value'][j];

                    	html += '<option value="' + option_value['product_option_value_id'] + '">' + option_value['name'];

                    	if (option_value['price']) {
                    		html += ' (' + option_value['price_prefix'] + option_value['price'] + ')';
                    	}

                    	html += '</option>';
                    }

                    html += '</select>';
                    html += '</div></div>';
                    html += '<br />';
                }

                if (option['type'] == 'checkbox') {
                	html += '<div id="option-' + option['product_option_id'] + '">';

                	if (option['required']) {
                		html += '<span class="required">*</span> ';
                	}

                	html += option['name'] + '<br />';

                	for (j = 0; j < option['option_value'].length; j++) {
                		option_value = option['option_value'][j];
                		
                		html += '<div data-role="input-control" class="input-control checkbox"><label>';
                		html += '<input type="checkbox" name="option[' + option['product_option_id'] + '][]" value="' + option_value['product_option_value_id'] + '" id="option-value-' + option_value['product_option_value_id'] + '" />';    
                		html += '<span class="check"></span>';
                		html += option_value['name'];
                		
                		if (option_value['price']) {
                			html += ' (' + option_value['price_prefix'] + option_value['price'] + ')';
                		}
                		
                		html += '</label></div>';
                		html += '<br />';
                	}

                	html += '</div>';
                	html += '<br />';
                }

                if (option['type'] == 'image') {
                	html += '<div id="option-' + option['product_option_id'] + '">';

                	if (option['required']) {
                		html += '<span class="required">*</span> ';
                	}

                	html += option['name'] + '<br />';
                	html += '<select name="option[' + option['product_option_id'] + ']">';
                    // html += '<option value=""><?php echo $text_select; ?></option>';

                    for (j = 0; j < option['option_value'].length; j++) {
                    	option_value = option['option_value'][j];

                    	html += '<option value="' + option_value['product_option_value_id'] + '">' + option_value['name'];

                    	if (option_value['price']) {
                    		html += ' (' + option_value['price_prefix'] + option_value['price'] + ')';
                    	}

                    	html += '</option>';
                    }

                    html += '</select>';
                    html += '</div>';
                    html += '<br />';
                }

                if (option['type'] == 'text') {
                	html += '<div id="option-' + option['product_option_id'] + '">';

                	if (option['required']) {
                		html += '<span class="required">*</span> ';
                	}

                	html += option['name'] + '<br />';
                	html += '<input type="text" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" />';
                	html += '</div>';
                	html += '<br />';
                }

                if (option['type'] == 'textarea') {
                	html += '<div id="option-' + option['product_option_id'] + '">';

                	if (option['required']) {
                		html += '<span class="required">*</span> ';
                	}

                	html += option['name'] + '<br />';
                	html += '<textarea name="option[' + option['product_option_id'] + ']" cols="40" rows="5">' + option['option_value'] + '</textarea>';
                	html += '</div>';
                	html += '<br />';
                }


                if (option['type'] == 'date') {
                	html += '<div id="option-' + option['product_option_id'] + '">';

                	if (option['required']) {
                		html += '<span class="required">*</span> ';
                	}

                	html += option['name'] + '<br />';
                	html += '<input type="text" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" class="date" />';
                	html += '</div>';
                	html += '<br />';
                }

                if (option['type'] == 'datetime') {
                	html += '<div id="option-' + option['product_option_id'] + '">';

                	if (option['required']) {
                		html += '<span class="required">*</span> ';
                	}

                	html += option['name'] + '<br />';
                	html += '<input type="text" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" class="datetime" />';
                	html += '</div>';
                	html += '<br />';						
                }

                if (option['type'] == 'time') {
                	html += '<div id="option-' + option['product_option_id'] + '">';

                	if (option['required']) {
                		html += '<span class="required">*</span> ';
                	}

                	html += option['name'] + '<br />';
                	html += '<input type="text" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" class="time" />';
                	html += '</div>';
                	html += '<br />';						
                }
                
    }//foreach option
    
    html += '<button onclick="addToCart();"class="button">Add to cart<span class="icon-cart on-right"></span></button>';

    $('#option').html(html);
    
    $('.date').datepicker({dateFormat: 'yy-mm-dd'});
    $('.datetime').datetimepicker({
    	dateFormat: 'yy-mm-dd',
    	timeFormat: 'h:m'
    });
    $('.time').timepicker({timeFormat: 'h:m'});	
    
  });//end $.post 
}


function removeItem()
{
	$item_selected = $('#item_selected').val();
	
	$key = $('#'+$item_selected).attr('data-key');
	$order_id = $('#'+$item_selected).attr('data-order');
	$product_id = $key.split(":");
	$product_id = $product_id[0]
	$qty = $('#'+$item_selected).parent().parent().find('span.qty').html();
	$item_total = $('#'+$item_selected).parent().parent().find('td.item_total').html();
	$reason_id = $('#return_reason').val();
	
	
	
	removeFromCart($key,$order_id);
	
	$('#'+$item_selected).parentsUntil('tbody').remove(); 
	
	$('#removed_items').val($('#removed_items').val()+$product_id+','+$reason_id+','+$qty+','+$item_total+','+$order_id+'-');
	
	$('.fancybox-close').trigger('click');
	
	//
	
}
//remove items from cart   
$('.cart_remove').live('click',function(){
	
	//if($('#pickup_status').val()=='Not Picked Up' && $('#payment_type').val()=='Unpaid')
	//{
		
		$('#item_selected').val($(this).attr('id'));
		$('.fancybox-close').trigger('click');
		
		$.fancybox.open( $('#removal_items') );
	/*}
	else
	{
    removeFromCart($(this).attr('data-key'),$(this).attr('data-order'));
	$(this).parentsUntil('tbody').remove();    
}*/

oScrollbar2.tinyscrollbar_update('top');
});

$( "#barcode" ).on( "keydown", function(event) {
	if(event.which == 13 && $(this).val().length > 0) {
		$.post('index.php?route=pos/pos/getProductByBarcode&token=<?php echo $token; ?>',{ barcode: $(this).val() }, function(data){
			
			var data = JSON.parse(data);
			
			$('.product-info .product_id').val(data.product_id);        
			
			if(data.has_option==1){
				$('.product_list_bottom').removeClass('hide');
				get_option(data.product_id);
			}else{        
				$('.product_list_bottom').addClass('hide');
				addToCart();
			}
			
			$("#barcode").val('');
			
		});
	}
});

$( "#q" ).on( "keydown", function(event) {
	if(event.which == 13) 
		search($(this).val(),1);
});

$('.btn-search').click(function(){
	search($('#q').val(),1);
});
var $_order_id = '';
function update_cart($products, $total_data,is_combine,order_id,data){	
	var html  = '';
	var all_products = '';
	html+='<tr><td colspan="6" style="background-color:#CCC">'+order_id+' - '+data['date_added']+' - '+data['customer_name']+'</td></tr>';
	for(var i=0; i< $products.length; i++){
		html += '<tr><td>'+$products[i]['name']+'<br />';
        //option
        for(var j=0; j < $products[i]['option'].length; j++) {
        	html += '- <small>'+$products[i]['option'][j]['name']+' '+ $products[i]['option'][j]['value']+ '</small><br />';
        }
        html += '</td><td class="qty"><span class="minus">-</span><span data-key="'+$products[i]['key']+'" class="qty">'+$products[i]['quantity']+'</span><span class="plus">+</span></td>';
        html += '<td>'+$products[i]['price']+'</td>';
        html += '<td>'+$products[i]['tax']+'</td>';
        html += '<td class="item_total">'+$products[i]['total']+'</td>';
        html += '<td><?php echo (($this->user->userHavePermission('pos_can_edit_order') || $this->user->getUserName() == 'admin')? '<a class="cart_remove" id="product-\'+order_id+\'-\'+i+\'" data-key="\'+$products[i][\'key\']+\'" data-order="\'+order_id+\'"><i class=" icon-cancel-2"></i></a>': ''); ?></td>';
        html += '</tr>';
        
        
        if($('#xpayment_method').val()=='paid')
        {
        	$product_id = $products[i]['key'].split(":");
        	$product_id = $product_id[0];
        	all_products = all_products + $product_id+',1,'+$products[i]['quantity']+','+$products[i]['total']+','+order_id+'-';
        	
        }
        
    }

    if( $_order_id!=order_id)
    {
    	$('#all_items').val(all_products);	
    	$_order_id=order_id;
    }
    


    if(is_combine==0)
    {
    	$('.cart_table tbody').html(html);
    	$('#apply_voucher').show();
    	$('input[name=split_payment]').prop('checked', false);
    	$('input[name=split_payment]').removeAttr('disabled');
    	$('select[name="payment_method"]').removeAttr('disabled');

    	splitPayment();
    }
    else
    {
    	$('.cart_table tbody').append(html);
    	$('#div_map_order').hide();
    	$('#apply_voucher').hide();
    	$('input[name=split_payment]').prop('checked', false);
    	$('input[name=split_payment]').attr('disabled','disabled');
    	$('select[name="payment_method"] option:last-child').remove();
    	
    	splitPayment();
    }

    //total data
    var html = '<div class="pull-right" style="'+(is_combine==1?'clear:both':'')+'">';
    var total_amount = 0.00;
    
    for(var i=0; i < $total_data.length; i++){
    	
    	
    	
    	if($total_data[i].code=='total')
    	{
    		total_amount = $total_data[i].value;
    	}
    	if($total_data[i].code=='tax')
    	{
    		$('#tax-paid').text($total_data[i].text);
    	}
    	if($total_data[i].code=='sub_total')
    		{ total_sub_amount = $total_data[i].value;
    		}
		//alert($total_data[i].title);
		html += '<div>';
		html += '<b>'+$total_data[i].title +'</b>';
		html += '<br><span id="'+($total_data[i].code=='total'?'main_total':($total_data[i].code=='sub_total'?'main_sub_total':''))+'">'+$total_data[i].text+'</span>';
		html += '</div>';
	}
	html += '</div>';
	//alert(html);
	/*if($('#xpayment_method').val()=='unpaid')
	{
	Math.round(total_amount);
}*/
$('input[name=paid]').val(parseFloat(total_amount).toFixed(2));
$('#amount-due').text('$' + parseFloat(total_amount).toFixed(2));
$('.total_wrapper').html(html);
if(is_combine != 0) {
	/*$('.total_wrapper').append(html);
	var main_total =  $('.total_wrapper #main_total').html();
	var main_sub_total =  $('.total_wrapper #main_total').html();
	main_total = parseFloat(main_total.replace("$",""));
	main_sub_total = parseFloat(main_sub_total.replace("$",""));

	main_total = (parseFloat(main_total)+parseFloat(total_amount)).toFixed(2);
	main_sub_total = (parseFloat(main_sub_total)+parseFloat(total_sub_amount)).toFixed(2);



	$('#main_sub_total').html('$'+total_sub_amount.toFixed(2));
	$('#main_total').html('$'+total_amount.toFixed(2));
	$('input[name=paid]').val(total_amount.toFixed(2));
	$('#xtotal').val(total_sub_amount.toFixed(2));*/
}
}

function discount(){
	$.post('index.php?route=pos/pos/discount&token=<?php echo $token; ?>',{ discount_type: $('#discount_type').val(), discount_amount: $('#discount_amount').val() }, function(data){        
		var json = JSON.parse(data);
		
		var html = '<div class="pull-right">';
		for(var i=0; i < json['total_data'].length; i++){
			html += '<div><b>'+json['total_data'][i].title +'</b><br><span id="cart-total">'+json['total_data'][i].text+'</span></div>';
		}
		html += '</div>';
		$('.total_wrapper').html(html);
	});
}

function coupon(){
	$.post('index.php?route=pos/pos/coupon&token=<?php echo $token; ?>',{ coupon: $('#coupon').val() }, function(data){        
		var json = JSON.parse(data);
		
		var html = '<div class="pull-right">';
		for(var i=0; i < json['total_data'].length; i++){
			html += '<div><b>'+json['total_data'][i].title +'</b><br><span id="cart-total">'+json['total_data'][i].text+'</span></div>';
		}
		html += '</div>';
		$('.total_wrapper').html(html);
	});
}

function applySVoucher (voucherCode) {
	voucher(voucherCode);
	$('.fancybox-close').trigger('click');
}

function applyVouchers () {
	$('.voucher_list').find('.voucherCode').each(function() {
		if ($(this).is(':checked')) {
			voucher($(this).val());
		}
	});

	$('.fancybox-close').trigger('click');
}

function voucher(voucherCode){
	if (!voucherCode) {
		voucherCode = $('#voucher').val();
	}
	$.post('index.php?route=pos/pos/voucher&token=<?php echo $token; ?>',{ voucher: voucherCode,order_id: $('input[name="order_id"]').val() }, function(data){        
		var json = JSON.parse(data);
		
		var html = '<div class="pull-right">';
		$total_data = json['total_data'];
		var total_amount = 0.00
		for(var i=0; i < json['total_data'].length; i++){
			
			if($total_data[i].title=='total') total_amount = $total_data[i].value;
			
			html += '<div><b>'+json['total_data'][i].title +'</b><br><span id="'+($total_data[i].code=='total'?'main_total':($total_data[i].code=='sub_total'?'main_sub_total':''))+'">'+json['total_data'][i].text+'</span></div>';
		}
		html += '</div>'; 
		
		total_amount = total_amount.toFixed(2);
		$('#xtotal').val(total_amount);
		$('input[name=paid]').val(total_amount);
		$('#amount-due').text('$' + parseFloat(total_amount).toFixed(2));
		$('.total_wrapper').html(html);
	});
}

//load next page 
$('.product_pager button').click(function(){
	$q = $(this).attr('data-q');
	$category_id = $(this).attr('data-category-id');
	$is_search = $(this).attr('data-is-search');
	$page = $(this).attr('data-page');
	
	if($is_search == 'true'){
		search($q,$page); 
	}else{
		getItems($category_id,$page);
	}
});

function search($q, $page){
	var html = '';
	
    //get category list
    $.post('index.php?route=pos/pos/searchProducts&token=<?php echo $token; ?>',{ q: $q, page: $page }, function(data){
    	
    	var data = JSON.parse(data);
    	
    	for(var i = 0; i < data.products.length; i++){
    		html += '<div data-product-id="'+data.products[i]['id']+'"  data-has-option="'+data.products[i]['hasOptions']+'"  class="product">';
    		html += '<div class="tile" data-title="'+data.products[i]['name']+'" data-price="'+data.products[i]['price_text']+'"><div class="tile-content image">';
    		html += '<img src="'+data.products[i]['image']+'">';
    		html += '</div><div class="brand bg-dark opacity"><span class="text">';                                                   
    		html += data.products[i]['name'];
    		html += '</span></div></div></div>'; 
    	}
    	
    	$page++;
    	
    	if(data['has_more']){
            //set attribute 
            $button = $('.product_pager button');
            $button.attr('data-q',$q);
            $button.attr('data-category-id','');
            $button.attr('data-is-search',true);
            $button.attr('data-page',$page);
            $('.product_pager').removeClass('hide');
        }else{
        	$('.product_pager').addClass('hide');
        }
        
        //check is start page 
        if($page == 2){
        	$('.product_list .overview').html(html);
        	oScrollbar1.tinyscrollbar_update('top');
        }else{
        	$('.product_list .overview').append(html);
        	oScrollbar1.tinyscrollbar_update('bottom');
        }        

    });
}

function getItems($id, $page){
	
	var html = '';
	
    //get category list
    $.post('index.php?route=pos/pos/getCategoryItems&token=<?php echo $token; ?>',{ category_id: $id, page: $page }, function(data){
    	
    	var data = JSON.parse(data);
    	
    	for(var i = 0; i < data.categories.length; i++){
    		html += '<div data-category-id="'+data.categories[i]['id']+'" class="category"><div class="tile"><div class="tile-content image">';
    		html += '<img src="'+data.categories[i]['image']+'">';
    		html += '</div><div class="brand bg-dark opacity"><span class="text">'; 
    		html += data.categories[i]['name'];
    		html += '</span></div></div></div>'; 
    	}
    	
    	for(var i = 0; i < data.products.length; i++){
    		html += '<div data-product-id="'+data.products[i]['id']+'"  data-has-option="'+data.products[i]['hasOptions']+'"  class="product">';
    		html += '<div class="tile" data-title="'+data.products[i]['name']+'" data-price="'+data.products[i]['price_text']+'"><div class="tile-content image">';
    		html += '<img src="'+data.products[i]['image']+'">';
    		html += '</div><div class="brand bg-dark opacity"><span class="text">'; 
    		html += data.products[i]['name'];
    		html += '</span></div></div></div>'; 
    	}
    	
    	$page++;
    	
    	if(data['has_more']){
            //set attribute 
            $button = $('.product_pager button');
            $button.attr('data-q','');
            $button.attr('data-category-id',$id);
            $button.attr('data-is-search',false);
            $button.attr('data-page',$page);
            $('.product_pager').removeClass('hide');
        }else{
        	$('.product_pager').addClass('hide');
        }
        
        //check is start page 
        if($page == 2){
        	$('.product_list .overview').html(html);
        	oScrollbar1.tinyscrollbar_update('top');
        }else{
        	$('.product_list .overview').append(html);
        	oScrollbar1.tinyscrollbar_update('bottom');
        }        
    });
}

function removeFromCart($key,$order_id){
	$.ajax({
		url: 'index.php?route=pos/pos/removeFromCart&token=<?php echo $token; ?>',
		type: 'post',
		data: { remove: $key,order_id:$order_id },
		dataType: 'json',
		success: function(json) {
			update_list();
			
		}
	});
}

function addToCart(){
	$.ajax({
		url: 'index.php?route=pos/pos/addToCart&token=<?php echo $token; ?>',
		type: 'post',
		data: $('.product-info input[type=\'text\'], .product-info input[type=\'hidden\'], .product-info input[type=\'radio\']:checked, .product-info input[type=\'checkbox\']:checked, .product-info select, .product-info textarea'),
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, information, .error').remove();

			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						$('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
					}
				}
			}

			if (json['success']) {                            
				
				update_cart(json['products'], json['total_data']); 
				
				oScrollbar2.tinyscrollbar_update('bottom');
				$('.total_wrapper .pull-right div').fadeOut().delay(50).fadeIn('slow');
				$('.product_list_bottom').addClass('hide');                    
			}	                        
		}
	});
}

function clearCart(){    
	$.ajax({
		url: 'index.php?route=pos/pos/clearCart&token=<?php echo $token; ?>',
		type: 'post'
	});
}

function print_link() {    
   /*   $(".order_head,.cart_table, .total_wrapper").printThis({
       debug: false, // show the iframe for debugging
       importCSS: true, // import parent page css
       printContainer: true, // print outer container/$.selector
       //loadCSS: "view/javascript/pos/print/print.css", // load an additional css file
       pageTitle: "INVOICE", // add title to print page
       removeInline: false, // remove all inline styles
       cleardata: false
   });*/
   var order_id = prompt('Please provide the order to be printed or leave blank for existing order','');

   if(order_id=='')
   {

   	order_id = $('input[name=order_id]').val();   

   }
   if(order_id=='')
   {
   	alert("Please select the order to print first");   
   	return false;
   }

   $.ajax({
   	url: 'index.php?route=sale/order/invoice&token=<?php echo $token; ?>&order_id='+order_id,
   	type: 'post',


   	beforeSend: function() {

   	},
   	complete: function() {

   	},
   	success: function(data) {

   		var newWindow = window.open("","Invoice Print","width=800,height=500,scrollbars=1,resizable=1")

    //read text from textbox placed in parent window
    


    newWindow.document.open()
    newWindow.document.write(data);
    newWindow.window.print();
    newWindow.document.close()

},
error: function(xhr, ajaxOptions, thrownError) {
	alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
}
});




}

function print() {    
      /*$(".order_head,.cart_table, .total_wrapper").printThis({
       debug: false, // show the iframe for debugging
       importCSS: true, // import parent page css
       printContainer: true, // print outer container/$.selector
       //loadCSS: "view/javascript/pos/print/print.css", // load an additional css file
       pageTitle: "INVOICE", // add title to print page
       removeInline: false, // remove all inline styles
       cleardata: true
   });*/




   clearCart();
   cleardata();
 //  window.location='index.php?route=pos/dashboard&token=<?php echo $token;?>'
 
 return true;
}
</script> 

<script type="text/javascript"><!--
	
	$('.pagination a').live('click',function(){
		get_orders($(this).attr('href'));
		return false;
	}); 

	$('.pagination2 a').live('click',function(){
		map_filter($(this).attr('href'));
		return false;
	}); 

	$('.order_list .edit').live('click', function(){
		var order_id = $(this).attr('data-order-id');
		var is_combine = $(this).attr('data-combine');
		$.get('index.php?route=pos/pos/getOrder&order_id='+$(this).attr('data-order-id')+'&is_combine='+is_combine+'&token=<?php echo $token; ?>',function(data){
			var data = JSON.parse(data);
			$("#xpayment_method").val((data['payment_method']=='Cash at Store Pick-Up'?'unpaid':'paid'));
			$("#xpayment_type").val(data['payment_method']);
			
			if($('#xpayment_method').val()=='unpaid')
			{

				//data['xtotal'] = Math.round(data['xtotal']);	
			}
			$('#xtotal').val(data['xtotal']);




			$('input[name=split_payment]').removeAttr('disabled');
			$('input[name=split_payment]').prop('checked',false);

			update_cart(data['products'], data['total_data'],is_combine,order_id,data);

			$('.product_container').hide('slow');
		//$('.product_list').hide('slow');
		//$('.footer_timer').hide('slow');
		



		$('#div_transaction').hide();
		$('#transaction_id').val('');
		$('#transaction_amount').val('');
		
        //change pop up to order edit mode 
        $('.order_form h3').html('Update Order');
        if(is_combine==0)
        {
        	$('#removed_items').val('');
        	
        	$('#item_selected').val('');
        	$('input[name="order_id"]').val(data['order_id']);
        	$('.order-ids-list').html('<li>' + data['order_id'] + '</li>');
        }
        else
        {
        	$('input[name="order_id"]').val($('input[name="order_id"]').val()+','+data['order_id']);
        	$('.order-ids-list').append('<li>' + data['order_id'] + '</li>');
        }
        $('textarea[name="order_comment"]').val(data['comment']);        
        $('#order_confirm').attr('id','order_update').html('Complete Order');
        
        if(data['customer']['customer_id']){
        	$('input[name="customer_name"]').val(data['customer']['customer_name']);
        	$('input[name="customer_id"]').val(data['customer']['customer_id']);
        	
        	$('.btn_cart_hold_add').attr('href','index.php?route=pos/pos/orders&token=<?= $token ?>&picked_up_orders=false&combine=true&customer_id='+(data['customer']['customer_id']?data['customer']['customer_id']:0)+'&payment_method='+encodeURIComponent(data['payment_method']));	
        }else{
        	$('.btn_cart_hold_add').attr('href','index.php?route=pos/pos/orders&token=<?= $token ?>&picked_up_orders=false&combine=true&customer_email='+data['order_id']+'&payment_method='+encodeURIComponent(data['payment_method']));	
        	$('input[name="is_guest"]').prop('checked', true);
        }
        
        $('#pickup-date').text(data['customer_order_info']['date']);
        $('#customer-name').text(data['customer_order_info']['customer_name']);
        $('#customer-email').text(data['customer_order_info']['customer_email']);
        $('.total_wrapper').attr('class', 'total_wrapper');
        if (data['paid_status'] == 'paid') {
        	$('.total_wrapper').addClass('green');
        } else {
        	$('.total_wrapper').addClass('red');
        }
        
        $('#payment_type').val(data['payment_type']);
        $('#pickup_status').val(data['pickup_status']);
        if(data['payment_type']=='Paid' && data['pickup_status']=='Not Picked Up'){
        	$('.minus,.plus').hide();
        	$('.cart_remove').show();
        	$('#apply_voucher').hide();
        	$('#barcode').attr('disabled','disabled');
        	
        }
        else if(data['payment_type']=='Unpaid' && data['pickup_status']=='Not Picked Up'){
        	
        	$('.minus,.plus').hide();
        	$('#barcode').attr('disabled','disabled');
        	if (is_combine==0) {
        		$('.btn_applyVoucher').attr('href','index.php?route=pos/pos/vouchers&token=<?= $token ?>&email=' + data['customer']['customer_email']);
        		$('#apply_voucher').show();
        	}
        	
        }
        else if (data['pickup_status']=='Picked Up')
        {
        	$('#refund_items iframe').attr('src','index.php?route=sale/rma&order_id='+data['order_id']+'&token=<?php echo $token;?>')
        	$.fancybox.open( $('#refund_items') );
        	cleardata();
        	
        }
        
        if($('#xpayment_method').val()=='paid')
        {
        	$('.btn_cart_hold_add').hide();	
        	$('#map_order').hide();
        	
        	$('#div_map_order').hide();

        }
        else
        {
        	$('.btn_cart_hold_add').show();
      //$('#map_order').show();
      $('#div_map_order').show();	
  }
  
  $('.fancybox-close').trigger('click');
});    
});
function sort_it()
{


}
function update_list(index)
{
	index = typeof index !== 'undefined' ? index : 0;
	order_ids = $('input[name=order_id]').val();
	order_ids = order_ids.split(",");
	is_combine = (index==0?0:1);

	$.post('index.php?route=pos/pos/updateList&token=<?php echo $token; ?>',{ order_id: order_ids[index] },function(data){
		var data = JSON.parse(data);
		update_cart(data['products'], data['total_data'],is_combine, order_ids[index],data);
		$('.minus,.plus').hide();
		if (typeof order_ids[parseInt(index)+1]!=='undefined')
		{
			update_list(parseInt(index)+1);
		}
	});
}
function get_orders($url){
	$.get($url, function(data){
		var data = JSON.parse(data);
		var html = '';
		if ($('.not_found')) {
			$('.not_found').remove();
		}
		if(data['rows'].length ==0){
			html += '<tr class="not_found"><td colspan="7">No order(s) found!</td></tr>';
		}
		var picked_up = 'Picked Up';
		for($i = 0; $i < data['rows'].length; $i++){

			if(data['rows'][$i]['order_status_id']==<?php echo $this->config->get('config_complete_status_id');?>)
			{
				picked_up = 'Picked Up';

			}else
			{

				picked_up ='Not Picked Up';

			}
				// if(data['rows'][$i]['ref_order_id']!=null)
				// {
				// 	data['rows'][$i]['order_id'] = data['rows'][$i]['ref_order_id']
				
				// }
				html += "<tr class='data_row'>";
				html += "<td align='center'><input type='checkbox' value='"+data['rows'][$i]['order_id']+"' name='selected[]'></td>";
				if(data['rows'][$i]['ref_order_id']!=null)
				{
					html += "<td align='right'>"+data['rows'][$i]['ref_order_id']+"</td>";
				}
				else
				{
					html += "<td align='right'>"+data['rows'][$i]['order_id']+"</td>";
				}
				html += "<td>"+data['rows'][$i]['customer']+"</td>"; 
				html += "<td>"+data['rows'][$i]['status']+"</td>"; 
				html += "<td align='right' class='td_total'>"+data['rows'][$i]['total']+"</td>"; 
				html += " <td>"+data['rows'][$i]['payment_method']+(data['rows'][$i]['payment_method']=='Cash at Store Pick-Up'?' <small>(Unpaid)</small>':' <small>(Paid)</small>')+"</td>";
				html += " <td>";
				
				for($j = 0; $j < data['rows'][$i]['products'].length; $j++){
					
					html+= data['rows'][$i]['products'][$j]['model']+ ' * '+data['rows'][$i]['products'][$j]['quantity']+"<br>";
				}
				html += "</td>";
				html += "<td>"+data['rows'][$i]['date_added']+"</td>";
				html += "<td>"+data['rows'][$i]['date_modified']+"</td>";
				html += "<td align='center'> [<a class='edit' data-order-id="+data['rows'][$i]['order_id']+" data-combine='0' href='#'>"+(picked_up=='Picked Up'?'Return':'Select')+"</a>]</td>";
				html += "</tr>";
			}
			
			$('.pagination').html(data['pagination']);
			$('.data_row').remove();
			$('.order_list table .filter').after(html);
		});
}

function filter($page) {


	url = 'index.php?route=pos/pos/ordersAJAX&token=<?php echo $token; ?>';

	var filter_order_id = $('input[name=\'filter_order_id\']').attr('value');

	if (filter_order_id) {
		url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
	}

	var filter_customer = $('input[name=\'filter_customer\']').attr('value');

	if (filter_customer) {
		url += '&filter_customer=' + encodeURIComponent(filter_customer);
	}

	var filter_order_status_id = $('select[name=\'filter_order_status_id\']').attr('value');

	if (filter_order_status_id != '*') {
		url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
	}	

	var filter_total = $('input[name=\'filter_total\']').attr('value');

	if (filter_total) {
		url += '&filter_total=' + encodeURIComponent(filter_total);
	}	

	var filter_date_added = $('input[name=\'filter_date_added\']').attr('value');

	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}

	var filter_date_modified = $('input[name=\'filter_date_modified\']').attr('value');

	if (filter_date_modified) {
		url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
	}
	url += '&picked_up_orders=' + encodeURIComponent($('#picked_up_orders').val());

	url += '&sort='+ encodeURIComponent($('#sort_by').val());		
	url += '&order='+ encodeURIComponent($('#order_by').val());		


	get_orders(url);
}

function map_filter(xurl) {

	xurl = xurl || '';
	url = 'index.php?route=pos/pos/track_order&token=<?php echo $token; ?>';

	var filter_email = $('#filter_email').attr('value');

	if (filter_email) {
		url += '&filter_email=' + encodeURIComponent(filter_email);
	}

	var filter_name = $('#filter_name').attr('value');

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

	var filter_transaction_id = $('#filter_transaction_id').attr('value');

	if (filter_transaction_id) {
		url += '&filter_transaction_id=' + encodeURIComponent(filter_transaction_id);
	}





	var filter_date_from = $('#filter_date_from').attr('value');

	if (filter_date_from) {
		url += '&filter_date_from=' + encodeURIComponent(filter_date_from);
	}

	var filter_date_to = $('#filter_date_to').attr('value');

	if (filter_date_to) {
		url += '&filter_date_to=' + encodeURIComponent(filter_date_to);
	}
	url+='&page=1';



	if ($('.not_found')) {
		$('.not_found').remove();
	}
	if ($('.map_tr')) {
		$('.map_tr').remove();
	}
	$('#map_order_list table .map_filter').after('<tr class="not_found"><td colspan="7" align="center">Looking up for data, please wait...</td></tr>');

	if(xurl) url = xurl;
	$.get(url, function(data){
		var data = JSON.parse(data);
		var html = '';


		if ($('.not_found')) {
			$('.not_found').remove();
		}
		if ($('.map_tr')) {
			$('.map_tr').remove();
		}


		if(data['rows'].length ==0){
			html += '<tr class="not_found"><td colspan="7">No record(s) found!</td></tr>';
		}

		for($i = 0; $i < data['rows'].length; $i++){
			html+='<tr class="map_tr">';
			html+='<td>'+data['rows'][$i]['firstname']+' '+data['rows'][$i]['lastname'] +'</td>';
			html+='<td>'+data['rows'][$i]['email']+'</td>';
			html+='<td>'+data['rows'][$i]['transaction_id']+'</td>';
			html+='<td>$'+parseFloat(data['rows'][$i]['amount']).toFixed(2)+'</td>';
			html+='<td>$'+parseFloat(data['rows'][$i]['net_amount']).toFixed(2)+'</td>';
			html+='<td>'+data['rows'][$i]['order_date']+'</td>';
			html+='<td><a href="javascript:void(0);" onClick="map_transaction_id(\''+data['rows'][$i]['transaction_id']+'\',\''+data['rows'][$i]['amount']+'\')">Map</a></td>';
			html+='</tr>'
		}
        //alert(html);
        $('#map_order_list table .map_filter').after(html);
        $('.pagination2').html(data['pagination']);

    });
}

function map_transaction_id(transaction_id,amount)
{
  //amount = amount.replace("$","");
 // amount = amount.replace(",","");
 if(!confirm('Are you sure want to map the transaction?'))
 {
 	return false;
 }
 amount = parseFloat(amount).toFixed(2);
 var order_total = $('#xtotal').val();
 order_total = parseFloat(order_total).toFixed(2);
 var less_amount = parseFloat(order_total) - parseFloat(.10);
 var more_amount = parseFloat(order_total) + parseFloat(.10);

 less_amount = parseFloat(less_amount).toFixed(2);
 more_amount = parseFloat(more_amount).toFixed(2);
//alert(order_total);
//alert(less_amount);
//alert(more_amount);
//console.log('Order Total: '+order_total+' Less amount: '+less_amount+' More amount: '+more_amount);
if ( (amount >= less_amount && amount <= more_amount) ) {
	


	$('#div_transaction').show();
	$('#transaction_id').val(transaction_id);
	$('#transaction_amount').val(amount);
	$('input[name=split_payment]').attr('disabled','disabled');
	$('input[name=split_payment]').prop('checked',false);

	$('#xpayment_method').val('paid');
	$('#xpayment_type').val('Paypal Express');

	$('.fancybox-close').trigger('click');
}
else
{

	alert('Either Transaction Order amount is less than original order or higher than it.');
	return false;
}
}

//--></script>  
<script type="text/javascript"><!--
	$(document).ready(function() {
		$('.date').datepicker({dateFormat: 'yy-mm-dd'});
  //map_filter();
});
	//--></script> 
	<script type="text/javascript"><!--
		$('#form input').keydown(function(e) {
			if (e.keyCode == 13) {
				filter();
			}
		});

//timer 
function display_cf() {
    var refresh = 1000; // Refresh rate in milli seconds
    mytime = setTimeout('display_ctf()', refresh)
}

function display_ctf() {
	var x = new Date();
	$('.footer_timer span').html(x.toDateString() + ', ' +  x.toLocaleTimeString());
	tt = display_cf();
}

display_ctf();
//--></script> 