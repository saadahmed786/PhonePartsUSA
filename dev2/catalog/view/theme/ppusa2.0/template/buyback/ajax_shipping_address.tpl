





<script type="text/javascript">

  var recaptchaCallback = function () {

    console.log('recaptcha is ready'); // not showing

    grecaptcha.render("recaptcha", {

        sitekey: '6Lesqy8UAAAAAGTGe0AayW8a4WsZAcI7OhO5HdHV',

        callback: function () {

            console.log('recaptcha callback');

        }

    });

  }

</script>



<script src="https://www.google.com/recaptcha/api.js?onload=recaptchaCallback&render=explicit&hl=en" async defer></script>

<div class="white-box-inner">

	<h4 class="blue-title">Customer information</h4>

	<div class="border mt40">

	</div>

	<?php

	if(!$isLogged)

	{

	?>

	<div id='content' class="form-horizontal v-form field-space-40">

		<h3 class="form-title2 text-sm-center">Enter a new address</h3> <br>



			<div class="col-lg-3 col-md-5 col-xs-5">

		<div class="form-group labelholder" data-label="First Name">

				<!-- <label for="fname_not" class="control-label">First name</label> -->

				<input type="text" class="form-control" value="<?php echo $firstname;?>" name = "firstname" id="fname_not" placeholder="First Name" required>

			</div>

			</div>

			<div class="col-lg-1 col-md-1 col-xs-1"></div>

			<div class="col-lg-3 col-md-6 col-xs-5">

			<div class="form-group labelholder" data-label="Last Name">

				<!-- <label for="lname_not" class="control-label">Last name</label> -->

				<input value="<?php echo $lastname;?>" name = "lastname" type="text" class="form-control" id="lname_not" placeholder="Last Name" required>

			</div>

			</div>

			<div class="col-lg-1 col-md-1 col-xs-1"></div>

			<div class="col-lg-4 col-md-12 col-xs-12">

			<div class="form-group labelholder" data-label="Business Name">

				<!-- <label for="Bname_not" class="control-label">Business Name (Optional)</label> -->

				<input name= "businessname" type="text" class="form-control" id="Bname_not" placeholder="Business Name">

			</div>

		</div>



			<div class="col-lg-3 col-md-5 col-xs-12">

	<div class="form-group labelholder" data-label="Email Address">

				<!-- <label for="email" class="control-label">Email address</label> -->

				<input type="text" class="form-control" name="email" value="<?php echo $email;?>" id="email" placeholder="Email" required>

			</div>

			</div>

			<div class="col-lg-1 col-md-1"></div>

			<div class="col-lg-3 col-md-6 col-xs-12">

				<div class="form-group labelholder" data-label="Confirm Email">

				<!-- <label for="con_email_not" class="control-label">Confirm email address</label> -->

				<input type="text" class="form-control" id="con_email_not" placeholder="Confirm Email">

			</div>

			</div>

			<div class="col-lg-1"></div>

			<div class="col-lg-4 col-md-12 col-xs-12">

				<div class="form-group labelholder" data-label="Phone Number">

				<!-- <label for="phone_not" class="control-label">Phone</label> -->

				<input value="<?php echo $telephone;?>" name="telephone" type="text" class="form-control" id="phone_not" placeholder="Phone Number">

			</div>

		</div>

			<div class="col-md-6 col-xs-12">

		<div class="form-group labelholder" data-label="Street Address">

				<!-- <label for="staddress_not" class="control-label">Street Address</label> -->

				<input value="<?php echo $address_1;?>" name="address_1" type="text" class="form-control" id="staddress_not" placeholder="Street Address" required>

			</div>

			</div>

			<div class="col-md-1"></div>





			<div class="col-md-5 col-xs-12">

			<div class="form-group labelholder" data-label="Suite Number">

				<!-- <label for="apartment_not" class="control-label">Suite or Apartment address</label> -->

				<input type="text" class="form-control" id="apartment_not" placeholder="Suite Number">

			</div>

		</div>

			

			<div class="col-md-3 col-xs-5">

				<div class="form-group labelholder" data-label="City">

				<!-- <label for="city_not" class="control-label">City</label> -->

				<input name="city" value="<?php echo $city;?>" type="text" class="form-control" id="city_not" placeholder="City" required>

			</div>

			</div>

			<div class="col-md-1 col-xs-1"></div>

			<div class="col-md-4 col-xs-5">

				<!-- <label for="state" class="control-label">State</label> -->

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

		<div class="col-md-1 col-xs-1"></div>



		<div class="col-md-3 col-xs-12">

		<div class="form-group labelholder" data-label="Zip Code">

				<!-- <label for="zipcode_not" class="control-label">ZIP Code</label> -->

				<input value="<?php echo $postcode;?>" name="postcode" type="text" class="form-control" id="zipcode_not" placeholder="Zip Code" required>

			</div>

			</div>

		<div class="col-md-3" style="display:none">

			<!-- <label for="country" class="control-label">Country</label> -->

			<select data-size="10" data-live-search="true" class="selectpicker <?= ($error_form) ? 'errorInput': '';?>" name="country_id">

				<option value="">Country</option>

				<?php

				if(!isset($country_id))

				{

					$country_id = 223;

				}

				foreach($countries as $country)

				{

					if($country['country_id']==38 || $country['country_id']==223 )

									{





							 ?>

				?>

				<option value="<?php echo $country['country_id'];?>" <?php echo ($country['country_id']==$country_id?'selected="selected"': '');?>><?php echo $country['name'];?></option>

				<?php

			}

		}

			?>

		</select>

	</div>

	</div>



<div class="form-group">

	<div class="col-md-12 col-xs-12 password-click">

		<div class="creat-check v-center">

			<input type="checkbox" class="css-checkbox" name="create_account" value="1" id="ck1">

			<label for="ck1" class="css-label2">Create an Account</label>

		</div>

	</div>

	<div class="col-md-4 col-xs-12 password-toggle" style="margin-bottom:10px">

		<!-- <label for="password_not" class="control-label">Password</label> -->

		<input type="password" class="form-control" id="password_not" name="password" placeholder="Password...">

	</div>

	<div class="col-md-4 col-xs-12 password-toggle">

		<!-- <label for="cpassword_not" class="control-label">Confirm password</label> -->

		<input type="password" class="form-control" id="cpassword_not" name="confirm_password" placeholder="Confirm password...">

	</div>

</div>





<?php

}

?>	

</div>		

<?php if($isLogged) {?>

<br>

<h3 class="form-title2 text-sm-center">Select Saved Address</h3>

<br>



<div class="fields" style="<?=(!$isLogged?'display:none':'');?>">

	

		<select class="selectpicker" onchange="populateAddress($(this).val())" name="address_id">

		

			<?php

			foreach($addresses as $address)

			{

			?>

			<option value="<?php echo $address['address_id'];?>" <?php echo ($address['address_id']==$address_id?'selected':'');?>><?php echo $address['firstname'].' '.$address['lastname'].', '.$address['address_1'].', '.$address['city'].', '.$address['zone'] ;?></option>

			<?php

		}

		?>

		<!-- <option value="-1" <?php echo ($address_id=='-1'?'selected':'');?>>New Address</option> -->

	</select>

</div>

</div>

</div>

<br>

<div class="border or-divider"><span>OR</span>

</div>

<h3 class="form-title2 text-sm-center">Enter a new address</h3> <br>

<div class="form-horizontal v-form field-space-40">

		<div class="col-lg-3 col-md-5 col-xs-5">

		<div class="form-group labelholder" data-label="First Name">

				<!-- <label for="fname_not" class="control-label">First name</label> -->

				<input type="text" class="form-control" value="<?php echo $firstname;?>" name = "firstname" id="fname_not" placeholder="First Name" required>

			</div>

			</div>

			<div class="col-lg-1 col-md-1 col-xs-1"></div>

			<div class="col-lg-3 col-md-6 col-xs-5">

			<div class="form-group labelholder" data-label="Last Name">

				<!-- <label for="lname_not" class="control-label">Last name</label> -->

				<input value="<?php echo $lastname;?>" name = "lastname" type="text" class="form-control" id="lname_not" placeholder="Last Name" required>

			</div>

			</div>

			<div class="col-lg-1 col-md-1"></div>

			<div class="col-lg-4 col-md-12 col-xs-12">

			<div class="form-group labelholder" data-label="Business Name">

				<!-- <label for="Bname_not" class="control-label">Business Name (Optional)</label> -->

				<input name= "businessname" type="text" class="form-control" id="Bname_not" placeholder="Business Name">

			</div>

		</div>



			<div class="col-lg-3 col-md-5 col-xs-12">

	<div class="form-group labelholder" data-label="Email Address">

				<!-- <label for="email" class="control-label">Email address</label> -->

				<input type="text" class="form-control" name="email" value="<?php echo $email;?>" id="email" placeholder="Email" required>

			</div>

			</div>

			<div class="col-lg-1 col-md-1"></div>

			<div class="col-lg-3 col-md-6 col-xs-12">

				<div class="form-group labelholder" data-label="Confirm Email">

				<!-- <label for="con_email_not" class="control-label">Confirm email address</label> -->

				<input type="text" class="form-control" id="con_email_not" placeholder="Confirm Email">

			</div>

			</div>

			<div class="col-lg-1"></div>

			<div class="col-lg-4 col-md-12 col-xs-12">

				<div class="form-group labelholder" data-label="Phone Number">

				<!-- <label for="phone_not" class="control-label">Phone</label> -->

				<input value="<?php echo $telephone;?>" name="telephone" type="text" class="form-control" id="phone_not" placeholder="Phone Number">

			</div>

		</div>

			<div class="col-md-6 col-xs-12">

		<div class="form-group labelholder" data-label="Street Address">

				<!-- <label for="staddress_not" class="control-label">Street Address</label> -->

				<input value="<?php echo $address_1;?>" name="address_1" type="text" class="form-control" id="staddress_not" placeholder="Street Address" required>

			</div>

			</div>

			<div class="col-md-1"></div>





			<div class="col-md-5 col-xs-12">

			<div class="form-group labelholder" data-label="Suite Number">

				<!-- <label for="apartment_not" class="control-label">Suite or Apartment address</label> -->

				<input type="text" class="form-control" id="apartment_not" placeholder="Suite Number">

			</div>

		</div>

			

			<div class="col-md-3 col-xs-6">

				<div class="form-group labelholder" data-label="City">

				<!-- <label for="city_not" class="control-label">City</label> -->

				<input name="city" value="<?php echo $city;?>" type="text" class="form-control" id="city_not" placeholder="City" required>

			</div>

			</div>

			<div class="col-md-1 col-xs-1"></div>

			<div class="col-md-3 col-xs-5">

				<!-- <label for="state" class="control-label">State</label> -->

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

		<div class="col-md-1 col-xs-1"></div>



		<div class="col-md-4 col-xs-12">

		<div class="form-group labelholder" data-label="Zip Code">

				<!-- <label for="zipcode_not" class="control-label">ZIP Code</label> -->

				<input value="<?php echo $postcode;?>" name="postcode" type="text" class="form-control" id="zipcode_not" placeholder="Zip Code" required>

			</div>

			</div>

		<div class="col-md-3" style="display:none">

			<!-- <label for="country" class="control-label">Country</label> -->

			<select data-size="10" data-live-search="true" class="selectpicker <?= ($error_form) ? 'errorInput': '';?>" name="country_id">

				<option value="">Country</option>

				<?php

				if(!isset($country_id))

				{

					$country_id = 223;

				}

				foreach($countries as $country)

				{

					if($country['country_id']==38 || $country['country_id']==223 )

									{





							 ?>

				?>

				<option value="<?php echo $country['country_id'];?>" <?php echo ($country['country_id']==$country_id?'selected="selected"': '');?>><?php echo $country['name'];?></option>

				<?php

			}

		}

			?>

		</select>

	</div>

</div>

<?php } ?>

<br>

<br>

<div  class="col-md-12" style="margin-top:10px;clear: both;" id="recaptcha"></div>











<div class="col-md-12 text-center"><button type="submit" class="btn btn-primary mt40 mb40" onclick="validateForm()" style="margin-top:90px">Proceed to Step 3</button></div>

<script type="text/javascript">

	$(document).ready(function() {

		$('.selectpicker').selectpicker('refresh');



	});

	function validateForm()

	{

		if($('#email').val()!=$('#con_email_not').val())

		{

			alert('Please provide valid email and confirm email addresses');

			event.preventDefault();

			return false;

			// event.preventDefault();

		}



		var response = grecaptcha.getResponse();



if(response.length == 0)

{

	alert('Please verify if you are not a bot');

			event.preventDefault();

			return false;

}

    



	}

	<?php

	if($isLogged)

	{

		?>

		populateAddress($('select[name=address_id]').val());

	



	function populateAddress(address_id)

	{

		$.ajax({

						url: 'index.php?route=buyback/buyback/getAddress',

						type: 'POST',

						dataType: 'json',

						data: {address_id: address_id},

						success: function(json){

							if (json['error']) {

								// alert(json['error']);

							} else {

								// enterShipment();

								$('input[name=firstname]').val(json['firstname']);

								$('input[name=lastname]').val(json['lastname']);

								$('input[name=company]').val(json['company']);

								$('input[name=address_1]').val(json['address_1']);

								$('input[name=address_2]').val(json['address_2']);

								$('input[name=city]').val(json['city']);

								$('input[name=postcode]').val(json['postcode']);

								// $('select[name=zone_id1]').find('option:contains('+json['zone_id']+')').attr('selected','selected');

								$('select[name=zone_id1] option[value="'+json['zone_id']+'"]').attr('selected','selected');

								$('.selectpicker').selectpicker('refresh');

								$('input[name=email]').val('<?php echo $this->customer->getEmail();?>');

								$('#con_email_not').val('<?php echo $this->customer->getEmail();?>');

								$('input[name=telephone]').val('<?php echo $this->customer->getTelephone();?>');

								// $('input[name=zone_id]').val(json['zond_id']);

								// $('input[name=firstname]').val(json['firstname']);

								$('.labelholder input').change();

							}

						}

					});

	}

	<?php



}

?>



	

</script>