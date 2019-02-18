<div class="row">
	<div class="col-md-5 ">
		<div class="form-horizontal v-form">
			<div class="billing-info address-box">
				<h3 class="form-title text-sm-center">Billing address</h3>
				<p class="text-center note" style="color:grey">Note: A shipping address which does not match the credit card billing address may require additional verification. This may delay the processing of your order. </p>
				<br>
				<div class="form-group">
					<div class="col-md-12">
						<input type="checkbox" class="css-checkbox" id="same_shipping_radio">
						<label for="same_shipping_radio" class="css-label2">Same as Shipping Address</label>
					</div>
				</div>

				<div class="col-sm-5 ">
					<div class="form-group labelholder" data-label="First Name">
						<input type="text" class="form-control" id="inputFirstName" name="inputFirstName" value="<?php echo (isset($this->session->data['newcheckout']['firstname'])?$this->session->data['newcheckout']['firstname']:'');?>" placeholder="First Name">
					</div>
				</div>
				<div class="col-sm-1"></div>
				<div class="col-sm-5 ">
					<div class="form-group labelholder" data-label="Last Name">
						<input type="text" class="form-control" id="inputLastName" name="inputLastName" value="<?php echo (isset($this->session->data['newcheckout']['lastname'])?$this->session->data['newcheckout']['lastname']:'');?>" placeholder="Last Name">
					</div>
				</div>
				<div class="col-ms-1"></div>

				<div class="col-sm-12 ">
					<div class="form-group labelholder" data-label="Company">
						<input type="text" class="form-control" id="inputCompany" name="inputCompany" value="<?php echo (isset($this->session->data['newcheckout']['company'])?$this->session->data['newcheckout']['company']:'');?>" placeholder="Company">
					</div>
				</div>


				<div class="col-sm-12 ">
					<div class="form-group labelholder" data-label="Street Address">
						<input type="text" class="form-control" id="inputStreet" name="inputStreet" value="<?php echo (isset($this->session->data['newcheckout']['address_1'])?$this->session->data['newcheckout']['address_1']:'');?>" placeholder="Street Address">
					</div>
				</div>

				<div class="col-sm-12 ">
					<div class="form-group labelholder" data-label="Suite or Apartment">
						<input type="text" class="form-control" id="inputSuite" name="inputSuite" placeholder="Suite or Apartment" value="<?php echo (isset($this->session->data['newcheckout']['address_2'])?$this->session->data['newcheckout']['address_2']:'');?>">
					</div>
				</div>

				

				<div class="col-md-4 mb-sm-15">
					<div class="form-group labelholder" data-label="City">
						<input type="text" class="form-control" id="inputCity" name="inputCity" value="<?php echo (isset($this->session->data['newcheckout']['city'])?$this->session->data['newcheckout']['city']:'');?>" placeholder="City">
					</div>
				</div>
				<div class="col-md-1"></div>

				<div class="col-md-6 ">
					<div class="form-group" >
						<select class="selectpicker" id="inputState" name="inputState">

						</select>
					</div>
				</div>

				<div class="col-md-4 mb-sm-15 ">
					<div class="form-group labelholder" data-label="Zip Code">
						<input type="text" class="form-control" id="inputZip" value="<?php echo (isset($this->session->data['newcheckout']['postcode'])?$this->session->data['newcheckout']['postcode']:'');?>" name="inputZip" placeholder="Zip Code">
					</div>
				</div>
				<div class="col-md-1"></div>

				
				
				<!-- <div class="form-group"> -->
					<!-- <label for="inputCountry" class="col-sm-12 control-label">Country</label> -->
					<div class="col-sm-6">
					<div class="form-group">
						<select class="selectpicker" id="inputCountry" name="inputCountry">
							<option value="">Select Country</option>
							<?php foreach ($countries as $country) { ?>
							<?php
								if($country['country_id']==38 || $country['country_id']==223 )
									{
								?>
							<option value="<?php echo $country['country_id']; ?>" <?php echo ($country['country_id'] == $payment_address['country_id'])? 'selected="selected"': ''; ?>><?php echo $country['name']; ?></option>
							<?php } ?>
							<?php } ?>
						</select>	
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-7 credit-payment sidebox">
		<div class="row text-sm-center">
 		<div class="col-md-1 col-xs-1"><img src="catalog/view/theme/ppusa2.0/images/icons/lock-icon.png" width="32" height="32" /></div>
		<div class="col-md-11 col-xs-10">
		<h3 style="margin-top:0px;margin-bottom: 0px" class="fontsize13">secure credit card payment </h3>
		<p class="subtitle" style="margin-bottom:10px">256-Bit SSL Encrypted Payment</p></div>
		</div>
		<div class="row payment-images" style="margin-bottom:10px">
			<div class="col-xs-2">
			</div>
			<div class="col-xs-2">
				<div class="text-left">
					<img src="catalog/view/theme/ppusa2.0/images/payment/Payment.png" width="100%" alt="">
				</div>
			</div>
			<div class="col-xs-2">
				<div class="text-center">
					<img src="catalog/view/theme/ppusa2.0/images/payment/Payment1.png" width="100%" alt="">
				</div>
			</div>
			<div class="col-xs-2">
				<div class="text-center">
					<img src="catalog/view/theme/ppusa2.0/images/payment/Payment2.png" width="100%" alt="">
				</div>
			</div>
			<div class="col-xs-2">
				<div class="text-right">
					<img src="catalog/view/theme/ppusa2.0/images/payment/Payment3.png" width="100%" alt="">
				</div>
			</div>
			<div class="col-xs-2">
			</div>
		</div>
		<span class="line"></span> 	
		<form role="form">
			<div class="form-group">
				<label for="cartName">Name on Card</label>
				<input type="text" class="form-control" id="cartName" name="cc_name" placeholder="Name" value="<?php echo (isset($this->session->data['newcheckout']['cc_name'])?$this->session->data['newcheckout']['cc_name']:'');?>">
			</div>
			<div class="form-group">
			    <script src="catalog/view/javascript/jquery/ccFormat.js"></script>
				<label for="cardNum">Card Number</label>
				<input type="text" class="form-control" id="cardNum" maxlength="20" name="cc_number" value="<?php echo (isset($this->session->data['newcheckout']['cc_number'])?$this->session->data['newcheckout']['cc_number']:'');?>">
			</div>
			<div class="form-group">
				<label for="expiryDate">Expiration Date</label>
				<div class="row">
					<div class="col-md-5 pr0 mb-sm-15">
						<select class="selectpicker" name="cc_expire_date_month">
							<?php foreach ($months as $month) { ?>
							<option value="<?php echo $month['value']; ?>" <?php echo (($this->session->data['newcheckout']['cc_expire_date_month']==$month['value'])?'selected':'');?>><?php echo $month['text']; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-md-4">
						<select class="selectpicker" name="cc_expire_date_year">
							<?php foreach ($year_expire as $year) { ?>
							<option value="<?php echo $year['value']; ?>" <?php echo (($this->session->data['newcheckout']['ccPexpire_date_year']==$month['value'])?'selected':'');?>><?php echo $year['text']; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="securityCod">Security Code</label>
				<div class="row">
					<div class="col-md-5 pr0">
						<input type="text" class="form-control" id="securityCod" name="cc_cvv2" placeholder="CVV2" value="<?php echo (isset($this->session->data['newcheckout']['cc_cvv2'])?$this->session->data['newcheckout']['cc_cvv2']:'');?>">
					</div>
				</div>
			</div>
		</form>
		<span class="line"></span>
		<div class="row security-img">
			<div class="col-xs-4">
				<div class="text-center">
					<img src="catalog/view/theme/ppusa2.0/images/security/security.png" alt="">
				</div>
			</div>
			<div class="col-xs-4">
				<div class="text-center">
					<img src="catalog/view/theme/ppusa2.0/images/security/security1.png" alt="">
				</div>
			</div>
			<div class="col-xs-4">
				<div class="text-center">
					<img src="catalog/view/theme/ppusa2.0/images/security/security2.png" alt="">
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	var firstname = '<?php echo $this->db->escape($payment_address["firstname"]);?>';
	var lastname = '<?php echo $this->db->escape($payment_address["lastname"]);?>';
	var company = '<?php echo $this->db->escape($payment_address["company"]);?>';

	var address_1 = '<?php echo $this->db->escape($payment_address["address_1"]);?>';
	var address_2 = '<?php echo $this->db->escape($payment_address["address_2"]);?>';
	var zip = '<?php echo $this->db->escape($payment_address["postcode"]);?>';
	var city = '<?php echo $this->db->escape($payment_address["city"]);?>';
	var zone_id = '<?php echo $this->db->escape($payment_address["zone_id"]);?>';
	var country_id = '<?php echo $this->db->escape($payment_address["country_id"]);?>';
						        	// alert('<?php print_r($_SESSION['shipping']);?> ');
						        	$(document).on('click','#same_shipping_radio',function(){
						        		$('#inputState option').removeAttr('selected');

						        		if($(this).is(":checked")==true)
						        		{
						        			$('#inputFirstName').val(firstname);
						        			$('#inputLastName').val(lastname);
						        			$('#inputCompany').val(company);
						        			$('input[name=cc_name]').val(firstname+' '+lastname);

						        			$('#inputStreet').val(address_1);
						        			$('#inputSuite').val(address_2);
						        			$('#inputZip').val(zip);
						        			$('#inputCity').val(city);
						        			$('#inputCountry').val(country_id);
						        			
						        			$('#inputState').val(zone_id);
			 	// $('#shippingInfo #addressaddress_2').change();
			 	
						        			// $('#inputCountry').val(country_id);

						        		}
						        		else
						        		{
						        			$('#inputFirstName').val('');
						        			$('#inputLastName').val('');
						        			$('#inputCompany').val('');
						        			$('input[name=cc_name]').val('');

						        			$('#inputStreet').val('');
						        			$('#inputSuite').val('');
						        			$('#inputZip').val('');
						        			$('#inputCity').val('');
						        			$('#inputState option').removeAttr('selected');
						        		}
						        		$('.selectpicker').selectpicker('refresh');


						        	});

						        	$('#inputCountry').change(function(event) {
						        		$.ajax({
						        			url: 'index.php?route=checkout/checkout/country&country_id=' + $(this).val(),
						        			type: 'POST',
						        			dataType: 'json',
						        			success: function (json) {
						        				var html = '<option value="">Select State</option>';
						        				if (json['zone'] != '') {
						        					for (i = 0; i < json['zone'].length; i++) {
						        						html += '<option value="' + json['zone'][i]['zone_id'] + '"';

						        						if ( json['zone'][i]['zone_id'] == zone_id && $('#inputStreet').val()!=''  ) {
						        							html += ' selected="selected"';
						        						}

						        						html += '>' + json['zone'][i]['name'] + '</option>';
						        					}
						        				} else {
						        					html += '<option value="0" selected="selected">No State Found</option>';
						        				}

						        				$('#inputState').html(html);
						        				$('#inputState').selectpicker('refresh');
						        			}
						        		});

						        	});
$(document).ready(function() {
	$('#inputCountry').trigger('change');
});
$(document).ready(function(){
$('.labelholder').labelholder()
});
</script>
