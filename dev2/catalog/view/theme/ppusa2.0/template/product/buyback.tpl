<?php echo $header;
?>
<main class="main">
	<form class="ui form" action="<?php echo $action;?>" method="post" onSubmit="$('button[type=submit]').attr('disabled','disabled')">
		<div class="container lcd-buy-page">
			<div class="row row-centered">
				<div class="col-md-10 intro-head col-centered">
					<div class="text-center">
						<h1 class="blue blue-title uppercase">LCD Buy Back program</h1>
						<h3 class="uppercase">Save the environment &amp; your bank account!</h3>
						<p>We will buy a wide range of LCD screens that have cracked or shattered glass. As long as thay still display on image and the touch sensor works, we can send you cash or store credit! If they are too far gone,we can safely recycle them for you,</p>
					</div>
				</div>
			</div>
			<div class="white-box overflow-hide">
				<div class="row">
					<div class="col-md-12 table-cell">
						<div class="row inline-block">
							<div class="col-md-3 white-box-left pr0 inline-block">
								<div class="white-box-inner panel-trigger-parent">
									<h4 class="blue-title mb40">program details <a href="#" class="panel-trigger"><i class="fa fa-chevron-down"></i></a></h4>
								</div>
								<div class="panel-triggered">
									<div class="border"></div>
									<div class="white-box-inner">
										<h3>Which Screen We Buy Back</h3>
										<p>We are only able to buy back LCD for specific models of phones. Please make sure your LCD model is listed our form before sending it to us.</p>
									</div>
									<div class="border"></div>
									<div class="white-box-inner">
										<h3>OEM vs Non-OEM Screens</h3>
										<p>OEM Screens 100% Original Screens removed from devices. Never Refurbished or Resurfaced.
											Non-OEM Screens: (contain any of the following Aftermarket LCD, Touch Screen, Polarizor, Glass, Touch Screen Flex Cable, LCD Flex Cable, or Frame</p>
										</div>
										<div class="border"></div>
										<div class="white-box-inner">
											<h3>Buy Back, Recycle, or Return</h3>
											<p>Upon receipt, all the LCD Screens will be tested carefully for functionality.
												Non-functional LCDs can either be returned to the customer or properly recycled. It is your choice, but unless you have some crazy art installation planed for the broken screens, it is probably best to let up recycle them.</p>
											</div>
											<div class="border"></div>
											<div class="white-box-inner">
												<h3>Payment Options</h3>
												<p>We are happy to procide either store credit, or a PayPal transfer if you prefer the cash. If you do want a PayPal transfer please ensure you have the correct e-mail address entered atthe end of this form, we would have to pay the wrong person.
													Store Credit is issued between 2-4 business days ofter receipt. PayPal transfer is submitted 3-6 business days  after receipt.</p>
												</div>
												<div class="border"></div>
												<div class="white-box-inner">
													<h3>Buy Back FAQ</h3>
													<p>Did you read the DaVinci Code or maybe see the movie? Did it get you interested in history and secret codes? You do not have to travel to Europe to see the true secrets from history; technology now lets us unlock the oldest secret code in the world, the bible code. For centuries </p>
												</div>
											</div>	
										</div>
										<div class="col-md-9 white-box-right inline-block overflow-hide">
											<div class="white-box-inner">
												<h4 class="blue-title">Customer information</h4>
												<div class="border mt40"></div>
												<?php
												if(!$isLogged)
												{
													?>
													<div style='display:none;'  id='content' class="form-horizontal v-form field-space-40">
														<h3 class="form-title2">Enter a new address</h3> <br>
													
														<div class="form-group">
															<div class="col-lg-4 col-md-6 mb-sm-15">
																<label for="fname_not" class="control-label">First name</label>
																<input type="text" class="form-control" value="<?php echo $firstname;?>" name = "firstname" id="fname_not" placeholder="First name..." required>
															</div>
															<div class="col-lg-4 col-md-6">
																<label for="lname_not" class="control-label">Last name</label>
																<input value="<?php echo $lastname;?>" name = "lastname" type="text" class="form-control" id="lname_not" placeholder="Last name..." required>
															</div>
															<div class="col-lg-4 col-md-12 mt-md-15">
																<label for="Bname_not" class="control-label">Business Name (Optional)</label>
																<input name= "businessname" type="text" class="form-control" id="Bname_not" placeholder="Business name...">
															</div>
														</div>
														<div class="form-group">
															<div class="col-lg-4 col-md-6 mb-sm-15">
																<label for="email" class="control-label">Email address</label>
																<input type="text" class="form-control" name= "email" value="<?php echo $email;?>" id="email" placeholder="Email..." required>
															</div>
															<div class="col-lg-4 col-md-6">
																<label for="con_email_not" class="control-label">Confirm email address</label>
																<input type="text" class="form-control" id="con_email_not" placeholder="Confirm email...">
															</div>
															<div class="col-lg-4 col-md-12 mt-md-15">
																<label for="phone_not" class="control-label">Phone</label>
																<input value="<?php echo $telephone;?>" name="telephone" type="text" class="form-control" id="phone_not" placeholder="Phone...">
															</div>
														</div>
														<div class="form-group">
															<div class="col-md-7 mb-sm-15">
																<label for="staddress_not" class="control-label">Street Address</label>
																<input value="<?php echo $address_1;?>" name="address_1" type="text" class="form-control" id="staddress_not" placeholder="Street Address..." required>
															</div>
															<div class="col-md-5">
																<label for="apartment_not" class="control-label">Suite or Apartment address</label>
																<input type="text" class="form-control" id="apartment_not" placeholder="Suite or Apartment...">
															</div>
														</div>
														<div class="form-group">
															<div class="col-md-3 mb-sm-15">
																<label for="zipcode_not" class="control-label">ZIP Code</label>
																<input value="<?php echo $postcode;?>" name="postcode" type="text" class="form-control" id="zipcode_not" placeholder="Zip code..." required>
															</div>
															<div class="col-md-3 mb-sm-15">
																<label for="city_not" class="control-label">City</label>
																<input name="city" value="<?php echo $city;?>" type="text" class="form-control" id="city_not" placeholder="City..." required>
															</div>
															<div class="col-md-3 mb-sm-15">
																<label for="state" class="control-label">State</label>
																<select class="selectpicker <?= ($error_form) ? 'errorInput': '';?>" name="zone_id1" required>
																	<option value="">State</option>
																	<?php
																	foreach($zones as $zone)
																	{
																		?>
																		<option value="<?php echo $zone['zone_id'];?>" <?php echo ($zone['zone_id']==$zone_id?'selected="selected"': '');?>><?php echo $zone['name'];?></option>
																		<?php
																	}
																	?>
																</select>
															</div>
															<div class="col-md-3">
																<label for="country" class="control-label">Country</label>
																<select data-size="10" data-live-search="true" class="selectpicker <?= ($error_form) ? 'errorInput': '';?>" name="country_id" required>
																	<option value="">Country</option>
																	<?php
																	foreach($countries as $country)
																	{
																		?>
																		<option value="<?php echo $country['country_id'];?>" <?php echo ($country['country_id']==$country_id?'selected="selected"': '');?>><?php echo $country['name'];?></option>
																		<?php
																	}
																	?>
																</select>
															</div>
														</div>
														<div class="form-group">
															<div class="col-md-4 password-click">
																<div class="creat-check v-center">
																	<input type="checkbox" class="css-checkbox" id="ck1">
																	<label for="ck1" class="css-label2">Create an Account</label>
																</div>
															</div>
															<div class="col-md-4 password-toggle">
																<label for="password_not" class="control-label">Password</label>
																<input type="password" class="form-control" id="password_not" placeholder="Password...">
															</div>
															<div class="col-md-4 password-toggle">
																<label for="cpassword_not" class="control-label">Confirm password</label>
																<input type="password" class="form-control" id="cpassword_not" placeholder="Confirm password...">
															</div>
														</div>
													
												</div>
												
														
												<div id='content2'>
													<div class="text-center mb40" >
													<!-- <input class="btn btn-primary big" type='button' value='Sign In' href="#"> -->
													
													<a class="btn btn-primary big" href="<?=$this->url->link('account/login', '&redirect=buyback/buyback', 'SSL');?>" >Sign In</a>
													
													</div>
													<div class="border or-divider"><span>OR</span></div>
													<div class="text-center mt40">
														<input class="btn btn-primary big" type='button' id='hideshow' value='Submit form as a guest'>
													</div>
													
													
												</div>
													<?php
												}
												?>	
											</div>		
										<div class="white-box-inner">	
												<?php
												if($isLogged)
												{
													?>
													<br>
													<h3 class="form-title2">Select Saved Address</h3>
														<br>
														
															<div class="fields" style="<?=(!$isLogged?'display:none':'');?>">
																<div class="eleven wide field" >
																	<select class="ui fluid dropdown" onchange="if(this.value=='-1'){$('.hidden_shipping_form').show(); $('input[xrequired=true]').attr('required','required');}else{$('.hidden_shipping_form').hide();$('input[xrequired=true]').removeAttr('required');}" name="address_id">
																		<?php
																		foreach($addresses as $address)
																		{
																			?>
																			<option value="<?php echo $address['address_id'];?>" <?php echo ($address['address_id']==$address_id?'selected':'');?>><?php echo $address['firstname'].' '.$address['lastname'].', '.$address['address_1'].', '.$address['city'].', '.$address['zone'] ;?></option>
																			<?php
																		}
																		?>
																		<option value="-1" <?php echo ($address_id=='-1'?'selected':'');?>>New Address</option>
																	</select>
																</div>
															</div>
															<div class="field hidden_shipping_form" style="display:none">
																<div class="two fields">
																	<div class="field">
																		<input name="firstname" placeholder="First Name"  type="text" value="<?php echo $firstname;?>" xrequired="true" <?= ($error_form) ? 'class="errorInput"': '';?>>
																	</div>
																	<div class="field">
																		<input name="lastname" placeholder="Last Name" xrequired="true"  type="text" value="<?php echo $lastname;?>" <?= ($error_form) ? 'class="errorInput"': '';?> >
																	</div>
																</div>
																<div class="two fields" style="margin-top:-17px">
																	<div class="field">
																		<input name="email" xrequired="true" placeholder="Email"  type="email" value="<?php echo $email;?>" <?= ($error_form) ? 'class="errorInput"': '';?>>
																	</div>
																	<div class="field">
																		<input name="telephone"  maxlength="15" placeholder="Telephone #" class="phone" type="tel" value="<?php echo $telephone;?>">
																	</div>
																</div>
																<div class="two fields" style="margin-top:-17px">
																	<div class="field">
																		<input name="address_1" xrequired="true"  placeholder="Address" type="text" value="<?php echo $address_1;?>" <?= ($error_form) ? 'class="errorInput"': '';?>>
																	</div>
																	<div class="field">
																		<input name="city" xrequired="true"  placeholder="City" type="text" value="<?php echo $city;?>" <?= ($error_form) ? 'class="errorInput"': '';?>>
																	</div>
																</div>
																<div class="two fields" style="margin-top:-17px">
																	<div class="field">
																		<input name="postcode" xrequired="true"  placeholder="Zip Code" type="text" value="<?php echo $postcode;?>" <?= ($error_form) ? 'class="errorInput"': '';?>>
																	</div>
																	<div class="field">
																		<select class="ui fluid dropdown <?= ($error_form) ? 'errorInput': '';?>" name="zone_id" xrequired="true"  >
																			<option value="">State</option>
																			<?php
																			foreach($zones as $zone)
																			{
																				?>
																				<option value="<?php echo $zone['zone_id'];?>" <?php echo ($zone['zone_id']==$zone_id?'selected="selected"': '');?>><?php echo $zone['name'];?></option>
																				<?php
																			}
																			?>
																		</select>
																	</div></div>
																</div></div> <br>
														<div class="border or-divider"><span>OR</span></div>
														<h3 class="form-title2">Enter a new address</h3> <br>
													<div class="form-horizontal v-form field-space-40">
														<div class="form-group">
															<div class="col-lg-4 col-md-6 mb-sm-15">
																<label for="fname" class="control-label">First name</label>
																<input name= "firstname" type="text" class="form-control" id="fname" placeholder="First name..." required>
															</div>
															<div class="col-lg-4 col-md-6">
																<label for="lname" class="control-label">Last name</label>
																<input name= "lastname" type="text" class="form-control" id="lname" placeholder="Last name..." required>
															</div>
															<div class="col-lg-4 col-md-12 mt-md-15">
																<label for="bname" class="control-label">Business Name (Optional)</label>
																<input name= "businessname" type="text" class="form-control" id="bname" placeholder="Business name...">
															</div>
														</div>
														<div class="form-group">
															<div class="col-lg-4 col-md-6 mb-sm-15">
																<label for="email" class="control-label">Email address</label>
																<input name= "email" type="text" class="form-control" id="email" placeholder="Email..." required>
															</div>
															<div class="col-lg-4 col-md-6">
																<label for="con_email" class="control-label">Confirm email address</label>
																<input type="text" class="form-control" id="con_email" placeholder="Confirm email...">
															</div>
															<div class="col-lg-4 col-md-12 mt-md-15">
																<label for="phone" class="control-label">Phone</label>
																<input name= "telephone" type="text" class="form-control" id="phone" placeholder="Phone...">
															</div>
														</div>
														<div class="form-group">
															<div class="col-md-7 mb-sm-15">
																<label for="staddress" class="control-label">Street Address</label>
																<input name= "address_1" type="text" class="form-control" id="staddress" placeholder="Street Address...">
															</div>
															<div class="col-md-5">
																<label for="apartment" class="control-label">Suite or Apartment address</label>
																<input type="text" class="form-control" id="apartment" placeholder="Suite or Apartment...">
															</div>
														</div>
														<div class="form-group">
															<div class="col-md-3 mb-sm-15">
																<label for="zipcode" class="control-label">ZIP Code</label>
																<input name="postcode" type="text" class="form-control" id="zipcode" placeholder="Zip code...">
															</div>
															<div class="col-md-3 mb-sm-15">
																<label for="city" class="control-label">City</label>
																<input name="city" type="text" class="form-control" id="city" placeholder="City...">
															</div>
															<div class="col-md-3 mb-sm-15">
																<label for="state" class="control-label">State</label>
																<select id="state" class="selectpicker <?= ($error_form) ? 'errorInput': '';?>" name="zone_id1" required>
																	<option value="">State</option>
																	<?php
																	foreach($zones as $zone)
																	{
																		?>
																		<option value="<?php echo $zone['zone_id'];?>" <?php echo ($zone['zone_id']==$zone_id?'selected="selected"': '');?>><?php echo $zone['name'];?></option>
																		<?php
																	}
																	?>
																</select>
															</div>
															<div class="col-md-3">
																<label for="country" class="control-label">Country</label>
																<select data-size="10" data-live-search="true" class="selectpicker <?= ($error_form) ? 'errorInput': '';?>" name="country_id" required>
																	<option value="">Country</option>
																	<?php
																	foreach($countries as $country)
																	{
																		?>
																		<option value="<?php echo $country['country_id'];?>" <?php echo ($country['country_id']==$country_id?'selected="selected"': '');?>><?php echo $country['name'];?></option>
																		<?php
																	}
																	?>
																</select>
															</div>
														</div>
														<div class="form-group">
															<div class="col-md-4 password-click">
																<div class="creat-check v-center">
																	<input type="checkbox" class="css-checkbox" id="ck1">
																	<label for="ck1" class="css-label2">Create an Account</label>
																</div>
															</div>
															<div class="col-md-4 password-toggle">
																<label for="password" class="control-label">Password</label>
																<input name= "password" type="password" class="form-control" id="password" placeholder="Password...">
															</div>
															<div class="col-md-4 password-toggle">
																<label for="cpassword" class="control-label">Confirm password</label>
																<input type="password" class="form-control" id="cpassword" placeholder="Confirm password...">
															</div>
														</div>
													
													<?php
												}
												?>
														<br><br>
														<div class="border mt40"></div>
														<h3 class="form-title">Damaged glass quantity</h3>
														<div class="border"></div>
														<section class="lcd-listing">
															<!-- <h3>Apple</h3> -->
															<?php
															$i=1;
															foreach($products as $product):
																?>
															<div class="lcd-listing-wrp">
																<div class="row">
																	<div class="col-sm-4 lcd-listing-col">
																		<div class="image">
																			<?php
																			if($product['image']=='')
																			{
																				$image_path = HTTPS_SERVER."image/cache/no_image-100x100.jpg";
																			}
																			else
																			{
																				$image_path = HTTPS_SERVER."image/new_site/cart-detail/iphone-big.png"/*.$product['image']*/;
																			}
																			?>
																			<!-- <img src="image/new_site/cart-detail/iphone-big.png" alt="" class="imgborder"> -->
																			<img src="<?php echo $image_path;?>" width="100" height="100">
																			<input type="hidden" name="image_path[]" value="<?php echo $image_path;?>">
																		</div>
																		<input type="hidden" name="sku[]" value="<?php echo $product['sku'];?>" >
																		<?php echo $product['description'];?>
																		<input type="hidden" name="description[]" value="<?php echo $product['description'];?>" >
																	</div>
																	<div class="col-sm-3 col-xs-4 lcd-listing-col">
																		<p class="oem-qty">Non-OEM Qty:</p>
																		<div class="qtyt-box">
																			<div class="input-group spinner">
																				<span class="txt">QTY</span>
																				<input type="hidden" name="non_oem_price[]" id="non_oem_price_<?php echo $i;?>" value="<?php echo $product['non_oem'];?>">
																				<input type="text" class="form-control" name="non_oem[]" id="non_oem_<?php echo $i;?>" value="0" onchange="updateSubTotal(<?php echo $i; ?>);">
																				<div class="input-group-btn-vertical">
																					<button class="btn" type="button"><i class="fa fa-plus"></i></button>
																					<button class="btn" type="button"><i class="fa fa-plus"></i></button>
																				</div>
																			</div>
																		</div>
																		<p class="price"><?php echo $this->currency->format($product['non_oem']);?></p>
																	</div>
																	<div class="col-sm-3 col-xs-4 lcd-listing-col">
																		<p class="oem-qty">OEM Qty:</p>
																		<div class="qtyt-box">
																			<div class="input-group spinner">
																				<span class="txt">QTY</span>
																				<input type="hidden" name="oem_price[]" id="oem_price_<?php echo $i;?>" value="<?php echo $product['oem'];?>">
																				<input type="text" class="form-control" name="oem[]" id="oem_<?php echo $i;?>" value="0" onchange="updateSubTotal( <?php echo $i; ?> );">
																				<div class="input-group-btn-vertical">
																					<button class="btn" type="button"><i class="fa fa-plus"></i></button>
																					<button class="btn" type="button"><i class="fa fa-plus"></i></button>
																				</div>
																			</div>
																		</div>
																		<p class="price"><?php echo $this->currency->format($product['oem']);?></p>
																	</div>
																	<div class="col-sm-2 col-xs-4 lcd-listing-col">
																		<div id="sub_total_<?php echo $i;?>" class="item-total text-center">
																			<p>Item total:</p>
																			<h3><span>$0.00</span></h3>
																			<input class="sub_total" type="hidden" value="0.00" name="sub_total[]">
																		</div>
																	</div>
																</div>
															</div>
															<?php
															$i++;
															endforeach;
															?>
														</section>
														<div class="estimate-total text-right">
															<!-- Estimated total:   <span>$ 420.00</span> -->
															Estimated total: $
															<input   style = "width:100px; border: 0px none;" name="cash_total" id="total" value="0.00" type="text" readOnly>
															<input type="hidden" id="temp_total" value="0.00">
														</div>
														<div class="sidebox full bb-box overflow-hide">
															<h4>Buy back options</h4>
															<span class="line full"></span>
															<p class="bb-subtitle">If any LCD Screens are rejected by our Inspection Team, how should we preceed?</p>
															<ul class="total-types mb40">
																<li>
																	<input checked="" type="radio" name="rejected" class="css-radio" id="rdo1"> 
																	<label for="rdo1" class="css-radio">Safely dispose of non-functional screens</label>
																</li>
																<li>
																	<input type="radio" name="rejected" class="css-radio" id="rdo2"> 
																	<label for="rdo2" class="css-radio">Return non-faunctional screend</label>
																</li>
															</ul>
															<p class="bb-subtitle">Choose how to recieve your funds:</p>
															<ul class="total-types mb40">
																<li>
																	<!-- <input type="radio" name="funds" class="css-radio" id="rdo3">  -->
																	<input class="css-radio check-toggler" tabindex="0" name="payment_type" value="store_credit" onchange="populateTotal()" id="credit_payment" type="radio" checked="">
																	<label for="credit_payment" class="css-radio">Store Credit</label>
																</li>
																<li>
																	<!-- <input type="radio" name="funds" class="css-radio check-toggler" id="rdo4">  -->
																	<input class="css-radio check-toggler" tabindex="0" name="payment_type" value="cash" onchange="populateTotal();"  type="radio" id="cash_payment">
																	<label for="cash_payment" class="css-radio ">PayPal Email Address:</label>
																</li>
															</ul>
															<div class="form-horizontal">
																<div class="form-group check-toggled">
																	<div class="col-md-1 bb-paypal">
																		<img src="images/icons/paypal2.png" alt="">
																	</div>
																	<div class="col-md-4 pl0">
																		<input id="paypal_email" type="email" class="form-control control-white" placeholder="Email...">
																	</div>
																	<div class="col-md-4">
																		<input onblur="confirmEmail()" id="paypal_email_confirm" type="email" class="form-control control-white" placeholder="Confirm Email...">
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="text-right mt30">
									<button type="submit" class="btn btn-secondary">Commence LCD Buy Back</button>
									<input type="hidden" name="theme" value="2">
								</div>
								</div>
							
						</form>
					</main><!-- @End of main -->
					<script type="text/javascript">
						function confirmEmail() {
							var email = document.getElementById("paypal_email").value
							var confemail = document.getElementById("paypal_email_confirm").value;
							if(email != confemail) {
								alert('Email Not Matching!');
								return false;
							}
						}
					</script>
					
				
					<script>
						function updateSubTotal($i)
						{
							var oem = parseInt($('#oem_' + $i).val());
							var oem_price = parseFloat($('#oem_price_' + $i).val());
							var non_oem = parseInt($('#non_oem_' + $i).val());
							var non_oem_price = parseFloat($('#non_oem_price_' + $i).val());
							if (oem == '' || isNaN(oem))
							{
								oem = 0;
							}
							if (non_oem == '' || isNaN(non_oem))
							{
								non_oem = 0;
							}
							var sub_total = 0.00;
							var oem_val = 0.00;
							oem_val = parseInt(oem) * parseFloat(oem_price);
							oem_val += parseInt(non_oem) * parseFloat(non_oem_price);
							$('#sub_total_'+$i+' input').val(oem_val);
							$('#sub_total_' + $i+' span').html('$' + oem_val.toFixed(2));
							updateTotal();
						}
						function signIn()
						{
							$('#sign_in').submit();
						}
						function updateTotal()
						{
							var total = 0.00;
							$('.sub_total').each(function(index,element)
							{
                                  //     alert('here');
                                  
                                  total+=parseFloat($(this).val());   
                                   // alert(total);
                               });
   // alert(total);
   $('#temp_total').val(total);
   populateTotal();
   
}
function populateTotal(total)
{
	var total = $('#temp_total').val();
	var discount = 0.00;
	var returnVal = total;
	$('#cash_total').val('');
	$('#credit_total').val('');
	if($('#cash_payment').is(':checked'))
	{
		discount = (parseFloat(total)* parseFloat(<?php echo $general['cash_discount'];?>)) / 100;
		returnVal  = parseFloat(total) - parseFloat(discount);
		$('#paypal_email').show(500);
		$('#paypal_email_confirm').show(500);
		$('#paypal_email input').attr('required','required');
		$('#paypal_email_confirm input').attr('required','required');
		if(returnVal<0.00)
		{
			returnVal = 0.00;
		}
		$('#total').val(returnVal.toFixed(2));
	}
	else
	{
		$('#paypal_email input').removeAttr('required');
		$('#paypal_email_confirm input').removeAttr('required');
		$('#paypal_email').hide(500);
		$('#paypal_email_confirm').hide(500);
		returnVal = parseFloat(total);
           //alert('here');
           $('#total').val(returnVal.toFixed(2));
           
       }
   }
// On load
$('.phone').keyup();
</script>
<script>
	$('#teal_login_button').click(function(e) {
		$('#content-login').show();
		$('#content-login input:first').focus();
		$('html, body').animate({
			scrollTop: 0
		}, 500);
		return false;
		e.preventDefault();
	});
	semantic.accordion = {};
// ready event
semantic.accordion.ready = function() {
            // selector cache
            var
            $accordion = $('.ui.accordion'),
            $menuAccordion = $('.ui.menu.accordion'),
            $checkbox = $('.ui.checkbox'),
                    // alias
                    handler
                    ;
                    $accordion
                    .accordion()
                    ;
                    $menuAccordion
                    .accordion({
                    	exclusive: true
                    })
                    ;
                    $checkbox
                    .checkbox()
                    ;
                };
// attach ready event
$(document)
.ready(semantic.accordion.ready)
;
</script>
<script type="text/javascript">
	jQuery(document).ready(function(){
        jQuery('#hideshow').on('click', function(event) {        
             jQuery('#content').toggle('show');
             jQuery('#content2').toggle('hide');
        });
    });
</script>
<script type="text/javascript"><!--
$("select[name=country_id]").bind('change', function() {
	if (this.value == '') return;
	$.ajax({
		url: 'index.php?route=checkout/checkout/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			// $('#payment-address select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			// $('.wait').remove();
		},			
		success: function(json) {
			// if (json['postcode_required'] == '1') {
			// 	$('#payment-postcode-required').show();
			// } else {
			// 	$('#payment-postcode-required').hide();
			// }
			
			html = '<option value="">State</option>';
			
			if (json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
        			html += '<option value="' + json['zone'][i]['zone_id'] + '"';
	    			
					if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
	      				html += ' selected="selected"';
	    			}
	
	    			html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}
			
			$('select[name=zone_id1]').html(html);
			$('select[name=zone_id2]').html(html);
			$('.selectpicker').selectpicker('refresh');
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});
</script>
<?php echo $footer; ?>