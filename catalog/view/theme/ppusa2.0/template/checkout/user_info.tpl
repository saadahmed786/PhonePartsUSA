<div class="tab-inner pd100">
	<h3 class="blue-title uppercase mb40" style="margin-bottom:5px">Contact Information</h3>
	
	<div class="row">
		<div class="col-md-11">
			<form class="form-horizontal">
				<!-- <label for="firstname" class="col-xs-4 control-label">First Name</label> -->	
				<?php
				if($logged)
				{
					?>
					<div class="col-md-12 col-xs-12">
						<div class="form-group">
							<select class="selectpicker" id="contact_list">
								<option value="">Default Contact</option>
								<?php foreach ($infos as $info) {
									
									?>
									<option value="<?php echo $info['firstname']; ?>~<?php echo $info['lastname']; ?>~<?php echo $info['company']; ?>~<?php echo $info['telephone_1']; ?>~<?php echo $info['telephone_1']; ?>" ><?php echo $info['firstname']; ?> <?php echo $info['lastname']; ?>, <?php echo $info['business']; ?> (<?php echo $info['telephone_1']; ?>)</option>
									<?php 
								} 
								?>
							</select>
						</div>
					</div>
					<?php
				}
				?>
				<div class="col-md-12 col-xs-12 hidden">
					<div class="form-group">
						<select class="selectpicker" name="country_id" id="country_id">
							<option value="">Select Country</option>
							<?php foreach ($countries as $country) {
								if($country['country_id']==38 || $country['country_id']==223 )
								{
									?>
									<option value="<?php echo $country['country_id']; ?>" <?php echo ($country['country_id'] == $country_id)? 'selected="selected"': ''; ?>><?php echo $country['name']; ?></option>
									<?php 
								}
							} ?>
						</select>
					</div>
				</div>
				<div class="col-md-5 col-xs-12">
					<div class="form-group labelholder" data-label="First Name">
						<input type="text" id="firstname" name="firstname" class="form-control" id="firstname" name="firstname" value="<?php echo $firstname;?>" placeholder="First Name" required />  
					</div>
				</div>
				<div class="col-md-1"></div>
				<!-- <label for="lastname" class="col-xs-4 control-label">Last name</label> -->
				<div class="col-md-6 col-xs-12">
					<div class="form-group labelholder" data-label="Last Name">
						<input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $lastname;?>" placeholder="Last Name" required>
					</div>
				</div>
				<!-- <label for="company" class="col-xs-4 control-label">Business Name </label> -->
				<div class="col-xs-12 col-md-12">
					<div class="form-group labelholder" data-label="Business Name">
						<input type="text" class="form-control" id="company" name="company" value="<?php echo $company;?>" placeholder="Business Name">
					</div>
				</div>
				<!-- <div class="col-xs-1"></div> -->
				<!-- <label for="telephone" class="col-xs-4 control-label">Phone</label> -->
				<div class="col-md-5 col-xs-12">
					<div class="form-group labelholder" data-label="Telephone Cellular">
						<input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo $telephone;?>" placeholder="Telephone Cellular" required>
					</div>
				</div>
				<div class="col-md-1"></div>
				<div class="col-md-6 col-xs-12">
					<div class="form-group labelholder" data-label="Telephone-Office (Optional)">
						<input type="text" class="form-control" id="telephone_2" name="telephone_2" value="<?php echo $telephone_2;?>" placeholder="Telephone-Office (Optional)" required>
					</div>
				</div>
				<!-- <label for="email" class="col-xs-4 control-label">E-mail</label> -->
				<div class="col-md-12 col-xs-12">
					<div class="form-group labelholder" data-label="Email Address">
						<input type="text" class="form-control" id="email" name="email" placeholder="Email Address" value="<?php echo $email;?>" required <?php echo ($logged?'readOnly':'');?>>
					</div>
				</div>
				<?php

				$source_options = array('Google','Facebook','Bing','Other Online Ad','Magazine','Postcard','Referral','Blog','Discussion Board');
				sort($source_options);
				if($show_source)
				{
				?>
				<div class="col-md-12 col-xs-12">
						<div class="form-group">
							<select class="selectpicker" id="source" name="source"  data-size="6">
								<option value="">How Did You Hear About Us?</option>
								<?php
								foreach(($source_options) as $_source)
								{
								?>
									<option><?php echo $_source;?></option>
								<?php
								}

								?>
							</select>
						</div>
					</div>
					<?php

					}
					?>


					<?php

				$busines_type_options = array('Not a Business','Computer Repair Shop','Convenience/General Store','Government (School/School District/Agency)','Mobile Repair','Phone Repair Franchise','Phone Repair Shop','Refurbishing Company','Supplier/Distributor','Other Business Type');
				//sort($busines_type_options);
				if($show_business_type)
				{
				?>
				<div class="col-md-12 col-xs-12">
						<div class="form-group">
							<select class="selectpicker" id="business_type" name="business_type"  data-size="6">
								<option value="">Business Type</option>
								<?php
								foreach(($busines_type_options) as $_source)
								{
								?>
									<option><?php echo $_source;?></option>
								<?php
								}

								?>
							</select>
						</div>
					</div>
					<?php

					}
					?>
					
					


				<div class="form-group">
						<!-- <label class="col-xs-4 control-label"></label> -->
						<div class="col-xs-12" style="margin-top:10px">
													
							<?php if ($text_agree) { ?>
							<p style="vertical-align:top" id="agree_div">
								<input type="checkbox" class="css-checkbox" name="agree" value="1" id="agree"  />
								<label for="agree" class="css-label2"><?php echo $text_agree; ?></label>
							</p>
							<?php } ?>
						</div>
					</div>
				<!-- <div class="col-md-1"></div> -->
				<!-- <label for="confirmEmail" class="col-xs-4 control-label">Confirm Email</label> -->
					<!-- <div class="col-md-6 col-xs-12">
						<div class="form-group labelholder" data-label="Confirm Email Address">
						<input type="text" class="form-control" id="confirmEmail" name="confirmEmail" value="<?php echo $email;?>" placeholder="Confirm Email Address">
					</div>
				</div> -->
				<!-- <label for="address_1" class="col-xs-4 control-label">Address</label> -->
				<div class="col-md-7 col-xs-12 hidden">
					<div class="form-group labelholder" data-label="Street Address">
						<input type="text" class="form-control" id="address_1" name="address_1" value="<?php echo $address_1;?>" placeholder="Street Address">
					</div>
				</div>
				<div class="col-md-1 hidden"></div>
				<!-- <label for="address_2" class="col-xs-4 control-label">Suite / Appartment</label> -->
				<div class="col-md-4 col-xs-12 hidden">
					<div class="form-group labelholder" data-label="Suite Number">
						<input type="text" class="form-control" id="address_2" name="address_2" value="<?php echo $address_2;?>" placeholder="Suite Number">
					</div>
				</div>
				
				<div class="col-md-7 col-xs-12 hidden">
					<div class="form-group labelholder" data-label="City">
						<input type="text" class="form-control" id="city" name="city" value="<?php echo $city;?>" placeholder="City">
					</div>
				</div>
				<div class="col-md-1 hidden"></div>
				
				<!-- <label for="zone_id" class="col-xs-4 control-label">State</label> -->
				<div class="col-md-4 col-xs-12 hidden" style="">
					<div class="form-group">
						<select class="selectpicker" name="zone_id" id="zone_id">
							<option value="">Select State</option>
						</select>
					</div>
				</div>
				<div class="col-md-12 col-xs-12 hidden">
					<div class="form-group labelholder" data-label="Zip Code">
						<input type="text" class="form-control" id="zipcode" name="postcode" value="<?php echo $postcode;?>" placeholder="Zip Code">
					</div>
				</div>
				<!-- <div class="col-md-1 col-xs-1"></div> -->
				<!-- <div class="form-group labelholder" data-label="Zip Code">
				
					<div class="col-xs-12">
						<input type="text" class="form-control" id="zipcode" value="<?php echo $postcode;?>" name="postcode" placeholder="Zip Code">
					</div>
				</div> -->
				<!-- <label for="country_id" class="col-xs-4 control-label">Country</label> -->

				<div class="form-group <?php echo ($logged?'hidden':'');?>">
					<!-- <label class="col-xs-4 control-label"></label> -->
					<div class="col-md-12 col-xs-12">
						<p>
							<input type="checkbox" class="css-checkbox check-toggler" id="ck1">
							<label for="ck1" class="css-label2">Create Account <small class="small" style="margin-top:-4px;color:grey">- Tracking your Orders, and Checkout even faster in the future!</small></label>
						</p>
						<?php if ($shipping_required) { ?>
						<p>
							<input type="checkbox" class="css-checkbox" name="shipping_address" value="1" id="shipping" checked="checked" style="display:none" />
							<label for="shipping" class="css-label2" style="display:none"><?php echo $entry_shipping; ?></label>
						</p>
						<?php } ?>
					</div>
				</div>
				<div class="check-toggled mt-xs">
					<div class="col-md-5 col-xs-12">
						<div class="form-group labelholder" data-label="Password">
							<!-- <label for="password" class="col-xs-4 control-label">Password</label> -->

							<input type="password" class="form-control" id="password" name="password" placeholder="Password">
						</div>
					</div>
					<div class="col-md-1"></div>
					<div class=" col-md-6 col-xs-12">
						<div class="form-group labelholder" data-label="Confirm Password">
							<!-- <label for="confirm" class="col-xs-4 control-label">Confirm Password</label> -->

							<input type="password" name="confirm" class="form-control" id="confirm" placeholder="Confirm Password">
						</div>
					</div>
					<div class="form-group hidden">
						<!-- <label class="col-xs-4 control-label"></label> -->
						<div class="col-xs-12">
							<p>
								<input type="checkbox" name="newsletter" class="css-checkbox" value="1" id="newsletter" checked="checked" />
								<label for="newsletter" class="css-label2"><?php echo $entry_newsletter; ?></label>
							</p>
							
						</div>
					</div>
				</div>	
			</form>	
		</div>	
	</div>
	
</div>
<div class="row">
	<div class="col-md-12">
		<div class="text-right prev-next-btns greybg">
			<a id="<?php echo ($logged?'button-registered-user-info':'button-guest');?>" class="btn btn-info light">Next Step <i class="fa fa-angle-right"></i></a>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('#country_id').change(function(event) {
		$.ajax({
			url: 'index.php?route=checkout/checkout/country&country_id=' + $('#country_id').val(),
			type: 'POST',
			dataType: 'json',
			success: function (json) {
				var html = '<option value="">Select State</option>';
				if (json['zone'] != '') {
					for (i = 0; i < json['zone'].length; i++) {
						html += '<option value="' + json['zone'][i]['zone_id'] + '"';

						if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
							html += ' selected="selected"';
						}

						html += '>' + json['zone'][i]['name'] + '</option>';
					}
				} else {
					html += '<option value="0" selected="selected">No State Found</option>';
				}
				$('#zone_id').html(html);
				$('#zone_id').selectpicker('refresh');
			}
		});
		
	});
	$(document).ready(function() {
		$('#country_id').trigger('change');

	});
	$(document).ready(function(){
		$('.labelholder').labelholder()
	});
</script>