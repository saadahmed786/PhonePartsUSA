<?php echo $header; ?>
<script type="text/javascript">
  var recaptchaCallback = function () {
  
    grecaptcha.render("recaptcha", {
        sitekey: '6Lesqy8UAAAAAGTGe0AayW8a4WsZAcI7OhO5HdHV',
        callback: function () {
          
        }
    });
  }
</script>
<script src="https://www.google.com/recaptcha/api.js?onload=recaptchaCallback&render=explicit&hl=en" async defer></script>
<script type="text/javascript" src="catalog/view/javascript/jquery.SimpleMask.js"></script>
<!-- @End of header -->
	<main class="main">
		<div class="container wholesale-page">
			<div class="row row-centered">
			
			<div class="row hidden-md hidden-lg">
			<h3>Wholesale Account</h3>
			</div>
				<div class="col-lg-10 col-xs-12 right-content col-centered bg-white">
			    	<div class="row-centered">
			    		<form class="form-horizontal" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
			    		<div class="col-md-12 hidden-xs" style="padding:0px">
					    			<img src="https://phonepartsusa.com/image/data/business-customer-benefits.jpg" >
					    			</div>
				    		<div class="col-md-9 col-xs-12 col-centered">
				    			<?php if ($error_warning) { ?>
				    			<div class="alert alert-danger alert-dismissible" role="alert"><?php echo $error_warning; ?></div>
				    			<?php } ?>

				    			<?php if ($error_form) { ?>
				    			<div class="alert alert-danger alert-dismissible" role="alert"><?php echo $error_form; ?></div>

				    			<?php if ($error_emailVerify) { ?>
				    			<div class="alert alert-danger alert-dismissible" role="alert"><?php echo $error_emailVerify; ?></div>
				    			<?php } ?>

				    			<?php if ($error_license_no) { ?>
				    			<div class="alert alert-danger alert-dismissible" role="alert"><?php echo $error_license_no; ?></div>
				    			<?php } ?>

				    			<?php } ?>

				    			<div class="row hidden-xs" style="margin-top:30px">
				<div class="col-md-12 intro-head">
					<div class="col-md-2 text-right  col-md-offset-2"><i class="fa fa-user fa-5x" style="color:#4986fe"></i></div> <div class="col-md-8"> <span class="blue blue-title uppercase" style="font-size:21.19px;font-weight:bold">WHOLESALE ACCOUNT</span><br><span class="blue blue-title uppercase" style="font-size:36px;font-weight:bold">APPLICATION</span></div>
					
				</div>
			</div>

				    			<div class="contact-info address-box">
					    			<h3 class="text-sm-center">contact information</h3>

								    <div class="col-md-5 col-xs-12">
									<div class="form-group labelholder <?php echo ($error_first_name?'has-error':''); ?>" data-label="First Name">
										<!-- <label for="inputFname" class="col-xs-4 control-label">First Name</label> -->
								      <input type="text" class="form-control" id="first_name" name="first_name"  value="<?= $first_name; ?>" placeholder="First Name">
								    </div>
									</div>
									<div class="col-md-1"></div>
								    <div class="col-md-6 col-xs-12">
									<div class="form-group labelholder <?php echo ($error_last_name?'has-error':''); ?>" data-label="Last Name">
										<!-- <label for="inputLname" class="col-xs-4 control-label">Last name</label> -->
								      <input type="text" class="form-control" id="last_name" name="last_name" value="<?= $last_name; ?>" placeholder="Last Name">
								    </div>
									</div>
									
								    <div class="col-md-5 col-xs-12">
									<div class="form-group labelholder <?php echo ($error_office?'has-error':''); ?>" data-label="Telephone-Office">
										<!-- <label for="inputPhone" class="col-xs-4 control-label">Phone</label> -->
								      <!-- <div class="phone-option" style="display:none">
								      	<select name="phoneselector[]" class="selectpicker">
								      		<option value="store">Store</option>
								      		<option value="office" selected="">Office</option>
								      		<option value="mobile">Mobile</option>
								      	</select>
								      </div> -->
								      <input type="tel" class="form-control phone" id="inputPhone" name="office" value="<?= $office; ?>" placeholder="Telephone-Office">
								      <!-- <div class="clearfix"></div>
								      <br> -->
								      <!-- <a href="#" class="blue underline more-phone">Add Phone</a> -->
								      <!-- <div class="phone-copy clearfix">
								      	<a href="#" class="remove-phone"><i class="fa fa-times"></i></a>
								      	<div class="phone-option">
								      		<select name="phoneselector[]" class="selectpicker">
								      			<option value="store">Store</option>
								      			<option value="office">Office</option>
								      			<option value="mobile">Mobile</option>
								      		</select>
								      	</div>
								      	<input type="tel" onblur="updatePhone();" class="form-control" id="inputPhone" name="phonenumber[]" placeholder="Phone...">
								      	<div class="clearfix"></div>
								     	<br>
								      </div> -->
								    </div>
								    <input type="hidden" name="phones" id="phones" value=""/>
								    <!-- <input type="hidden" name="theme" value="2"> -->
									</div>

									<div class="col-md-1"></div>
									<div class="col-md-6 col-xs-12">
									<div class="form-group labelholder <?php echo ($error_mobile?'has-error':''); ?>" data-label="Telephone-Cellular">
										<!-- <label for="inputPhone" class="col-xs-4 control-label">Phone</label> -->
								      <!-- <div class="phone-option" style="display:none">
								      	<select name="phoneselector[]" class="selectpicker">
								      		<option value="store">Store</option>
								      		<option value="office" >Office</option>
								      		<option value="mobile" selected="">Mobile</option>
								      	</select>
								      </div> -->
								      <input type="tel"  class="form-control phone" id="inputPhone" name="mobile" value="<?= $mobile; ?>" placeholder="Telephone-Cellular">
								      <!-- <div class="clearfix"></div>
								      <br> -->
								      <!-- <a href="#" class="blue underline more-phone">Add Phone</a> -->
								      <!-- <div class="phone-copy clearfix">
								      	<a href="#" class="remove-phone"><i class="fa fa-times"></i></a>
								      	<div class="phone-option">
								      		<select name="phoneselector[]" class="selectpicker">
								      			<option value="store">Store</option>
								      			<option value="office">Office</option>
								      			<option value="mobile">Mobile</option>
								      		</select>
								      	</div>
								      	<input type="tel" onblur="updatePhone();" class="form-control" id="inputPhone" name="phonenumber[]" placeholder="Phone...">
								      	<div class="clearfix"></div>
								     	<br>
								      </div> -->
								    </div>
								   
									</div>
								    <div class="col-md-5 col-xs-12">
									<div class="form-group labelholder <?php echo ($error_personal_email?'has-error':''); ?>" data-label="Email">
										<!-- <label for="inputEmail" class="col-xs-4 control-label">E-mail:</label> -->
								      <input type="email" onkeyup="confirmEmail();" class="form-control" id="wholeEmail" name="personal_email" value="<?= $personal_email; ?>" placeholder="Email">
										<span class="error">
										</span>
								    </div>
									</div>
									<div class="col-md-7"></div>
								   
									</div>
									
								</div>
							<div class="border"></div> 
							<div class="col-md-9 col-xs-12 col-centered">
								<div class="ship-info address-box">
									<h3 class="text-sm-center">company information</h3>

									 <div class="col-md-5 col-xs-12">
									 <div class="form-group labelholder <?php echo ($error_company_name?'has-error':''); ?>" data-label="Company Name">
										
								   
								      <input type="text" class="form-control" name="company_name" value="<?= $company_name; ?>" for="inputBusiness" placeholder="Company Name">
								      </div>
								    </div>
								    <div class="col-md-1"></div>
								     <div class="col-md-6 col-xs-12">
								     <div class="form-group labelholder <?php echo ($error_position?'has-error':''); ?>" data-label="Position">
								      <input type="text" class="form-control" name="position" value="<?= $position; ?>"  placeholder="Position">
								    </div>
								    </div>
									


									    <div class="col-md-5 col-xs-12">
									<div class="form-group labelholder <?php echo ($error_address?'has-error':''); ?>" data-label="Street Address">
										<!-- <label for="inputStreet" class="col-xs-4 control-label">Street Address</label> -->
									      <input type="text" class="form-control" id="inputStreet" name="address" value="<?= $address; ?>" placeholder="Street Address">
									    </div>
									</div>
									<div class="col-md-1"></div>
									    <div class="col-md-4 col-xs-12">
									<div class="form-group labelholder <?php echo ($error_suite?'has-error':''); ?>" data-label="Suite / Unit">
										<!-- <label for="inputSuite" class="col-xs-4 control-label pt0">Suite or Apartament</label> -->
									      <input type="text" name="suite" value="<?= $suite; ?>" class="form-control" id="inputSuite" placeholder="Suite / Unit">
									    </div>
									</div>

									<div class="col-md-2"></div>

									<div class="col-md-3 col-xs-12" >
									
										<!-- <label for="inputStreet" class="col-xs-4 control-label">Street Address</label> -->
									     <!-- <select data-live-search="true" class="selectpicker <?= ($error_form) ? 'errorInput': '';?>" id="city" name="city">
																	<option value="">City</option>
																	
													</select> -->

													<div class="form-group labelholder <?php echo ($error_city?'has-error':''); ?>" data-label="City">
										<!-- <label for="inputSuite" class="col-xs-4 control-label pt0">Suite or Apartament</label> -->
									      <input type="text" name="city" id="city" value="<?= $city; ?>" class="form-control" id="inputSuite" placeholder="City">
									    </div>
									    
									</div>
									<!-- <div class="col-md-1"></div> -->
									<div class="col-md-6 col-xs-5 <?php echo ($error_state?'has-error':''); ?>">
									
									<select data-live-search="true" class="selectpicker <?= ($error_form) ? 'errorInput': '';?>" id="zone_id1" name="state">
																	<option value="">State</option>
																	<?php
																	foreach($zones as $zone)
																	{
																		?>
																		<option value="<?php echo $zone['name'];?>" <?php echo ($zone['name']==$zone_id?'selected="selected"': '');?>><?php echo $zone['name'];?></option>
																		<?php
																	}
																	?>
																	
													</select>
									    
									</div>
									<div class="col-md-1 col-xs-1"></div>

									<div class="col-md-3 col-xs-6">
									<div class="form-group labelholder <?php echo ($error_zip_code?'has-error':''); ?>" data-label="Zip Code">
										<!-- <label for="inputStreet" class="col-xs-4 control-label">Street Address</label> -->
									      <input type="text" class="form-control" id="inputStreet" name="zip_code" value="<?= $zip_code; ?>" placeholder="Zip Code">
									    </div>
									</div>

									<div class="col-md-3"></div>



									
								</div>
							</div>
							<div class="border"></div> 
							<div class="col-md-9 col-xs-12 col-centered">
								<div class="additional-info address-box">
									<h3 class="text-sm-center">additional information</h3>
									<h4 class="text-sm-center">Do You Have a Retail Location?</h4>
									<!-- <div class="form-group labelholder" data-label="Provide your Reseller Tax ID">
										
									    <div class="col-xs-12">
									      <input type="text" class="form-control" name="reseller_tax_id" id="reseller_tax_id" placeholder="Tax id...">
									    </div>
									</div> -->
									<div class="col-md-3 col-xs-12 <?php echo ($error_retail_point?'has-error':''); ?>">
									
									<select  class="selectpicker <?= ($error_retail_point) ? 'errorInput': '';?>" id="retail_point" name="retail_point">
																	<option value="1" <?php echo ($retail_point==1?'selected':'');?>>Yes</option>
																	<option value="0" <?php echo ($retail_point==1?'selected':'');?>>No</option>

																
													</select>
									    
									</div>
									<div class="row"></div>
									<br>
									<h4 class="text-sm-center">Number of Weekly Repairs</h4>

									<div class="col-md-5 col-xs-12 <?php echo ($error_repairs?'has-error':''); ?>">
									
									<select  class="selectpicker <?= ($error_form) ? 'errorInput': '';?>" id="repairs" name="repairs">
																	<?php $repairsItems = array('1-10 Phones', '20-50 Phones', '50-200 Phones', '200+ Phones');?>
						<?php foreach ($repairsItems as $value) { ?>
						<option value="<?= $value; ?>" <?= ($value == $repairs) ? 'selected="selected"': '';?>><?= $value; ?></option>
						<?php } ?>

																
													</select>
									    
									</div>
									<div class="col-md-7"></div>

									<div class="row"></div>
									<br>
									<h4 class="text-sm-center">Which Parts Interest You Most?</h4>

									<div class="col-md-5 col-xs-12 <?php echo ($error_interested?'has-error':''); ?>">
									
									<select  class="selectpicker <?= ($error_form) ? 'errorInput': '';?>" id="intrested" name="intrested[]">
																	<?php $intrestedItems = array('LCD Screens', 'Touch', 'Flex Cables', 'Accessories');?>
						<?php foreach ($intrestedItems as $value) { ?>
						
						<option value="<?= $value; ?>" <?= ($value == $intrested) ? 'selected="selected"': '';?>><?= $value; ?></option>
						<?php } ?>

																
													</select>
									    
									</div>
									<div class="col-md-7"></div>


									<div class="row"></div>
									<br>
									<h4 class="text-sm-center">Upload Business License</h4>
									<div class="col-md-12 <?php echo ($error_business_license?'has-error':''); ?>"><input id="business_license" type="file" style="display:block" onchange="uploadFile(this)" name="image" id="business_license" accept="image/jpeg,image/png,application/pdf,application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoin, image/tiff"><a class="hidden" href="javascript:void(0);" onclick="$('#business_license').click();">Upload Business License</a></div><br>
									<span id="imageControle" <?= ($business_license == '')? 'style="display: none;"': '';?>>
								<a id="viewFile" target="_blank" href="<?= HTTP_IMAGE . $business_license; ?>">View File</a>
								<a id="removeFile" href="javascript:void(0)"></a>
							</span>
									<input type="hidden" name="business_license" value="<?= $business_license; ?>" id="ImageName">


									<!-- <div class="form-group labelholder" data-label="Provide your Website URL">
										
									    <div class="col-xs-12">
									      <input type="text" class="form-control" name="website" id="website" placeholder="Website...">
									    </div>
									</div> -->
									<!-- <div class="form-group labelholder" data-label="Provide your Number of Employees">
										
									    <div class="col-xs-12">
									    	<input type="text" class="form-control" name="no_of_employeers" id="no_of_employeers" placeholder="#">
									    
									    </div>
									</div> -->
									<!-- <div class="form-group labelholder" data-label="Provide your Number of ocations">
									
									    <div class="col-xs-12">
									    	<input type="text" class="form-control" name="no_of_locations" id="no_of_locations" placeholder="#">
									    </div>
									</div>    --> 
									<!-- <div class="form-group ">
										
									    <div class="col-xs-12">
									    	<select name="type_of_business" class="selectpicker">
					    						<option value="type1">Type...</option>
					    						<option value="type2">Type...</option>
					    						<option value="type3">Type...</option>
					    						<option value="type4">Type...</option>
					    					</select>	
									    </div>
									</div> -->
									<!-- <div class="form-group labelholder" data-label="Provide your Additional Comments">
									
									    <div class="col-xs-12">
									    	<textarea class="form-control" name="comments" placeholder="Comments..."></textarea>
									    </div>
									</div> -->
								</div>
							</div>
							<div class="border"></div> 
							<div class="col-md-9 col-xs-12 col-centered">
								<div class="address-box policy-area">
								<!-- 	<div class="form-group">
		  									<label for="inputLname" class="col-xs-4 control-label"></label>
			  								<div class="col-sm-8">
			  									<p>
				  									<input type="checkbox" class="css-checkbox" id="ck1">
													<label for="ck1" class="css-label2">I have read &amp; agree to the <a href="#" class="underline policy-click">Returns Policy</a></label>
												</p>
			  								</div>
		  								</div> -->
		  								<!-- <div class="form-group">
		  									<label for="inputLname" class="col-xs-4 control-label"></label>
		  									<div class="col-sm-8">
												<p>
				  									<input type="checkbox" class="css-checkbox" id="ck2" checked="checked">
													<label for="ck2" class="css-label2">Receive <span class="underline blue">Special Offers</span> from <span class="underline blue">PhonePortsUSA</span></a></label>
												</p>
			  								</div>
		  								</div> -->
		  								<div  class="" style="margin-bottom:10px;margin-left:28%" id="recaptcha"></div>
	  								<div class="form-group">
	  								    
	  									<div class="text-center form-submit">
	  										<input type="submit" value="submit application" class="btn btn-primary">
	  									</div>
	  								</div>
  								</div>
							</div>	
						</form>	
					</div>	  
				</div>
			</div>
		</div>
	</main><!-- @End of main -->
	<script type="text/javascript">
            function confirmEmail() {
              // var email = document.getElementById("inputEmail").value;
              // var confemail = document.getElementById("inputEmail2").value;
              var email = $("#wholeEmail").val();
              var confemail = $("#inputEmail2Reg").val();
              if(email !== confemail) {
                jQuery('#emailcheck').show();
              } else {
                jQuery('#emailcheck').hide();
              }
              // else if(email == confemail){
              //   jQuery('#emailcheck').toggle('hide');
              // }
            }
          </script>
          <script type="text/javascript">
            function confirmPassword() {
              var pw = $("#inputPassword").val();
              var confpw = $("#inputPassword2").val();
              if(pw !== confpw) {
                jQuery('#pwcheck').show();
              } else {
                jQuery('#pwcheck').hide();
              }
              // else if(pw == confpw){
              //   jQuery('#pwcheck').toggle('hide');
              // }
            }
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
			
			$('#zone_id1').html(html);
			$('.selectpicker').selectpicker('refresh');
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});
</script>
<script type="text/javascript"><!--
$("select[name=state]").bind('change', function() {
	if (this.value == '') return;
	$.ajax({
		url: 'index.php?route=checkout/checkout/city&state=' + this.value,
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
			
			html = '<option value="">City</option>';
			
			if (json != '') {
				for (i = 0; i < json.length; i++) {
        			html += '<option value="' + json[i]['city_name'] + '"';
	    			
					if (json[i]['city_id'] == '<?php echo $city_id; ?>') {
	      				html += ' selected="selected"';
	    			}
	
	    			html += '>' + json[i]['city_name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}
			
			$('#zone_id2').html(html);
			$('.selectpicker').selectpicker('refresh');
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

function uploadFile (e) {
									var file = $(e).val().split(".");
									var ext = file.pop();
									var allowed = ['png', 'tiff', 'tif', 'jpeg', 'jpg', 'doc', 'docx', 'xls', 'xlsx', 'pdf'];
									var img = 'img';
									if (ext == 'pdf') {
										img = 'pdf';
									}
									if (ext == 'doc' || ext == 'docx') {
										img = 'doc';
									}
									if (ext == 'xls' || ext == 'xlsx') {
										img = 'xls';
									}
									if ($.inArray(ext, allowed) >= 0) {
										var formData = new FormData();
										formData.append('file', $('#business_license')[0].files[0]);
										$.ajax({
											url: "index.php?route=wholesale/wholesale/uplaodFile",
											type: "POST",
											data:  formData,
											dataType: "json",
											contentType: false,
											cache: false,
											processData:false,
											success: function(json){
												if (json['success']) {
													$(e).parent().parent().find('.error').text('');
													$('#ImageName').val(json['file']);
													$('#viewFile').attr('href', json['msg']).css('background-image', 'url("http://phonepartsusa.com/dev2/image/'+ img +'.jpg")');;
													$('#imageControle').fadeIn();
												}
												if (json['error']) {
													$('#imageControle').hide();
													$(e).parent().parent().find('.error').text('').text(json['msg']);
												}
											},
											error: function(){} 	        
										});
									} else {
										alert('This File is not Allowed');
									}
								}
$(document).ready(function(){
$('.labelholder').labelholder()
});
// Function
function phoneMask(e){
	var s=e.val();
	var s=s.replace(/[_\W]+/g,'');
	var n=s.length;
	if(n<11){var m='(00) 0000-00000';}else{var m='(00) 00000-00000';}
	//$(e).mask(m);
}

// Type
$(document).ready(function() {
	
	
	$('input[type=tel]' ).simpleMask( { 'mask': 'tel9'    , 'nextInput': true } );
	
	//$('#frCep').focus();

});
</script>
<?php echo $footer;?> 
<!-- @End of footer -->