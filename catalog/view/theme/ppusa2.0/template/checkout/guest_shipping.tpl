<div class="payment-address pb0"> 
	<div class="row">
		<div class="col-md-5 mb0">
			<div class="form-horizontal v-form">
				<div class="billing-info address-box mt0">
					<div class="row">
					<div class="col-md-12" style="padding:0px">
					<h3 class="form-title">shipping address</h3>
					</div>
					</div>
					<div class="row hidden">
					<div class="col-sm-12">
					<input type="checkbox" class="css-checkbox" id="same_as_billing">
							<label for="same_as_billing" class="css-label2">Same as Billing Address</label>
					</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-5">
					<div class="form-group labelholder" data-label="First Name" >
						
							<input type="text" class="form-control" name="firstname" id="addressfirstname" placeholder="First Name" value="<?php echo $firstname;?>" >
						</div>
					</div>
					<div class="col-sm-1"></div>
						<div class="col-sm-6">
					<div class="form-group labelholder" data-label="Last Name" >
					
							<input type="text" class="form-control" name="lastname"  id="addresslastname" placeholder="Last Name" value="<?php echo $lastname;?>" >
						</div>
					</div>
					</div>
					<!-- <div class="col-sm-1"></div> -->

					<div class="row">
					<div class="col-sm-12">
					<div class="form-group labelholder" data-label="Company">
						<!-- <label for="addressaddress_1" class="col-sm-12 control-label">Street Address</label> -->
							<input type="text" class="form-control" id="addresscompany" name="company" value="<?php echo $company;?>" placeholder="Company">
						</div>
					</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
					<div class="form-group labelholder" data-label="Street Address">
						<!-- <label for="addressaddress_1" class="col-sm-12 control-label">Street Address</label> -->
							<input type="text" class="form-control" id="addressaddress_1" name="address_1" value="<?php echo $address_1;?>" placeholder="Street Address">
						</div>
					</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
					<div class="form-group labelholder" data-label="Suite or Apartment">
						<!-- <label for="addressaddress_2" class="col-sm-12 control-label pt0">Suite or Apartament</label> -->
							<input type="text" class="form-control" id="addressaddress_2" name="address_2" value="<?php echo $address_2;?>" placeholder="Suite or Apartment">
						</div>
					</div>

					</div>
					
						
						<!-- <div class="col-md-6 pl0">
					<div class="form-group labelholder" data-label="State">
							<div class="row">
								
								<div class="col-sm-12">
									<select name="zone_id" id="addresszone_id" class="selectpicker">
										<option value="">No State</option>
									</select> 
								</div>
							</div>
						</div>
							<div class="row">
						<div class="col-md-6 mb-sm-15">
								
								<div class="col-sm-12">
									<input type="text" class="form-control" id="addresscity" name="city" value="" placeholder="City">
								</div>
							</div>
						</div>
					</div> -->
						<div class="row">
					<div class="col-md-5 p10">
						<div class="form-group labelholder" data-label="City">
						<input type="text" class="form-control" id="addresscity" name="city"  value="<?php echo $city;?>" placeholder="City">
					</div>
				</div>
				<div class="col-md-1"></div>

				<div class="col-md-6 mb-sm-15" style="">
					<div class="form-group">
						<select name="zone_id" id="addresszone_id" class="selectpicker">
										<option value="">No State</option>
									</select> 
					</div>
				</div>

				</div>

				<!-- <div class="col-md-1"></div> -->
					<div class="row">
						<div class="col-md-5 p10">
					<div class="form-group labelholder" data-label="Zip Code">
						
							<div class="row margin5">
								<div class="col-md-12">
									<input type="text" class="form-control" id="addresspostcode" name="postcode" value="<?php echo $postcode;?>" placeholder="Zip Code">
								</div>
							</div>  
						</div>
					</div>
					<div class="col-sm-1"></div>
					<div class="col-md-6 mb-sm-15">
					<div class="form-group">
						<!-- <label for="addresscountry_id" class="col-sm-12 control-label">Country</label> -->
							<select id="addresscountry_id" name="country_id" class="selectpicker">
								<option value="">Select Country</option>
								<?php foreach ($countries as $country) { ?>
								<?php
								if($country['country_id']==38 || $country['country_id']==223 )
									{
								?>	
								<?php if ($country['country_id'] == $country_id) { ?>
								<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
								<?php } else { ?>
								<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
								<?php } ?>
								<?php } ?>
								<?php } ?>
							</select> 
						</div>
					</div>
					</div>
					<!-- <div class="col-sm-1"></div> -->
				</div>
			</div>
		</div>

		<!-- Shipping Method -->
		<div id="shippingMethods" class="col-md-7 credit-payment overflow-hide ship-box sidebox" >
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="text-right prev-next-btns greybg">
		<!-- <input type="button" value="<?php echo $button_continue; ?>" id="button-guest-shipping" style="display: none;" class="button" /> -->
			<a href="javascript:void(0)" id="button-guest-shipping" style="display:none" st class="btn btn-info light">Next Step <i class="fa fa-angle-right"></i></a>			
			<!-- <a href="#" class="btn btn-primary"><i class="fa fa-angle-left"></i>Previous Step</a> -->
			<a href="javascript:void(0)"  id="button-shipping-method" st class="btn btn-info light disabled">Next Step <i class="fa fa-angle-right"></i></a>
		</div>
	</div>
</div> 

<script type="text/javascript">
	$(document).ready(function () {
		$('.selectpicker').selectpicker();
	});
</script>

<script type="text/javascript"><!--
	$(document).on('change', '#shippingInfo select[name=\'country_id\']', function() {
		if (this.value == '') return;
		$.ajax({
			url: 'index.php?route=checkout/checkout/country&country_id=' + this.value,
			dataType: 'json',
			beforeSend: function() {
				$('#shippingInfo select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},
			complete: function() {
				$('.wait').remove();
			},			
			success: function(json) {
				if (json['postcode_required'] == '1') {
					$('#shipping-postcode-required').show();
				} else {
					$('#shipping-postcode-required').hide();
				}

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

				$('#shippingInfo select[name=\'zone_id\']').html(html);
				$('#shippingInfo select[name=\'zone_id\']').selectpicker('refresh');
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});

	$('#shippingInfo select[name=\'country_id\']').trigger('change');
//-->
</script>

<script type="text/javascript"><!--
$(document).on('change', '#shippingInfo select, #shippingInfo input[type=text]', function(event) {
	
	// $('#shippingInfo input[type=text]').each(function(index, el) {
	// 	if (!$(el).val() && $(el).attr('id') != 'addressaddress_2') {
	// 		error = 1;
	// 	}
	// });
	
		$('#button-guest-shipping').trigger('click');
	
});
$(document).ready(function(){
$('.labelholder').labelholder();

});
$(document).on('click','#same_as_billing',function(){
	if($(this).is(':checked'))
	{
		$('#shippingInfo input[name=address_1]').val('<?php echo $address_1;?>');
		$('#shippingInfo input[name=address_2]').val('<?php echo $address_2;?>');
		$('#shippingInfo input[name=city]').val('<?php echo $city;?>');
		$('#shippingInfo input[name=postcode]').val('<?php echo $postcode;?>');
	}
	else
	{
		$('#shippingInfo input[name=address_1]').val('');
		$('#shippingInfo input[name=address_2]').val('');
		$('#shippingInfo input[name=city]').val('');
		$('#shippingInfo input[name=postcode]').val('');	
		$('.labelholder').labelholder();
	}
	});
$('#shippingInfo select[name=\'country_id\']').trigger('change');
setTimeout(function() { $('#button-guest-shipping').trigger('click'); }, 2000);

//$('#addresspostcode').trigger('change');
//-->
</script>