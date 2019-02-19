
<?php echo $header; ?>

<!-- WholeSale Form -->
<link rel="stylesheet" type="text/css" class="ui" href="catalog/view/theme/bt_optronics/stylesheet/global_style.css">
<link rel="stylesheet" type="text/css" class="ui" href="catalog/view/theme/bt_optronics/stylesheet/jquery.fancybox.css">
<!--<link rel="stylesheet" type="text/css" href="css/docs.css">-->
<link rel="stylesheet/less" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/form.less" />
<link rel="stylesheet/less" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/popup.less" />
<link rel="stylesheet/ess" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/accordian.less" />
<style type="text/css" media="screen">
	el {
		color: red;
	}
	.errorInput {
		border-color: red !important;
	}
</style>
<!-- WholeSale Form -->
<!-- WholeSale Form -->
<script src="catalog/view/javascript/jquery_min.js"></script>
<script src="catalog/view/javascript/easing_min.js"></script>
<script src="catalog/view/javascript/highlight_min.js"></script>
<script src="catalog/view/javascript/history_min.js"></script>
<script src="catalog/view/javascript/tablesort_min.js"></script>
<script src="catalog/view/javascript/semantic_min.js"></script>
<script src="catalog/view/javascript/docs.js"></script>
<script src="catalog/view/javascript/form_design.js"></script>
<script src="catalog/view/javascript/less_min.js"></script>
<script src="catalog/view/javascript/popup.js"></script>
<script type="text/javascript" src="http://cdn.transifex.com/live.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery.fancybox.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery.SimpleMask.js"></script>
<script type="text/javascript" src="catalog/view/javascript/scripts.js"></script>
<script type="text/javascript">
	function checkWhiteSpace (t) {
		if ($(t).val() == ' ') {
			$(t).val('');
		}
	}

	function allowNum (t) {
		var re = /^-?[0-9]+$/;
		var input = $(t).val();
		var valid = input.substring(0, input.length - 1);
		if (!re.test(input)) {
			$(t).val(valid);
		}
	}
</script>
<div class="damage_lcd_container">
	<h2 class="ui dividing header">LCD Buy Back Program</h2>
	<?php if ($error_warning) { ?>
	<div class="warning"><?php echo $error_warning; ?></div>
	<?php } ?>
	<?php if ($error_form) { ?>
	<div class="warning"><?php echo $error_form; ?></div>
	<?php } ?>
	<?php
//$general['upper_text'] = str_replace(array('<p>','</p>'), "", $general['upper_text']);
$general['lower_text'] = str_replace(array('<p>'), "", $general['lower_text']);
$general['lower_text'] = str_replace(array('</p>'), "<br>", $general['lower_text']);
	?>
	<?php if($general['upper_text']):
	
	?>
	<!--<p class="ui ignored info message">--><?php echo stripslashes($general['upper_text']);?><!--</p>-->
	<?php
	endif;?>
  
	<form class="ui form" action="<?php echo $action;?>" method="post" onSubmit="$('button[type=submit]').attr('disabled','disabled')">
		
          <?php
				if(!$isLogged)
				{
					?>
					<div class="field" >
                   <strong> Login to Retrieve Shipping Details and Link the record to your account.</strong><br><br>
						<div style="text-align:center"><button id="teal_login_button" class="ui large teal button" style="width:150px;">Login</button></div>
					</div>
					<?php
				}
				?>
        <div class=" fields">
			
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
 <div class="field hidden_shipping_form" style="display:none">
				<input name="firstname" placeholder="First Name"  type="text" value="<?php echo $firstname;?>" xrequired="true" <?= ($error_form) ? 'class="errorInput"': '';?>>
				<input name="lastname" placeholder="Last Name" xrequired="true"  type="text" value="<?php echo $lastname;?>" <?= ($error_form) ? 'class="errorInput"': '';?> >
				<input name="email" xrequired="true" placeholder="Email"  type="email" value="<?php echo $email;?>" <?= ($error_form) ? 'class="errorInput"': '';?>>
				<input name="telephone"  maxlength="15" placeholder="Telephone #" class="phone" type="tel" value="<?php echo $telephone;?>">
				<input name="address_1" xrequired="true"  placeholder="Address" type="text" value="<?php echo $address_1;?>" <?= ($error_form) ? 'class="errorInput"': '';?>>
				<input name="city" xrequired="true"  placeholder="City" type="text" value="<?php echo $city;?>" <?= ($error_form) ? 'class="errorInput"': '';?>>
				<input name="postcode" xrequired="true"  placeholder="Zip Code" type="text" value="<?php echo $postcode;?>" <?= ($error_form) ? 'class="errorInput"': '';?>>
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

				
			</div>
          
		<h3 class="ui dividing header">Damaged Glass QTY</h3>
		<table class="ui striped table form custome_lcd_table">
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th style="padding-left:40px;">OEM</th>
					<th>&nbsp;</th>
					<th style="padding-left:25px;">NON-OEM</th>
					<th>SUB-TOTAL</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$i=1;
				foreach($products as $product):

					?>
				<tr>
					<?php
					if($product['image']=='')
					{
						$image_path = HTTPS_SERVER."image/cache/no_image-100x100.jpg";
					}
					else
					{
						$image_path = HTTPS_SERVER."imp/files/".$product['image'];
					}
					?>
					<td><img src="<?php echo $image_path;?>" width="100" height="100">
						<input type="hidden" name="image_path[]" value="<?php echo $image_path;?>"></td>
						<td><input type="hidden" name="sku[]" value="<?php echo $product['sku'];?>" >
							<?php echo $product['description'];?>
							<input type="hidden" name="description[]" value="<?php echo $product['description'];?>" ></td>
							<td><?php echo $this->currency->format($product['oem']);?>
								<input type="hidden" name="oem_price[]" id="oem_price_<?php echo $i;?>" value="<?php echo $product['oem'];?>"></td>
								<td><div class="field">
									<input name="oem[]" onkeyup="allowNum(this);" id="oem_<?php echo $i;?>" placeholder="QTY" onchange="updateSubTotal( <?php echo $i; ?> );" type="text" >
								</div></td>
								<td><?php echo $this->currency->format($product['non_oem']);?></td>
								<td><div class="field">
									<input name="non_oem[]" id="non_oem_<?php echo $i;?>" onchange="updateSubTotal( <?php echo $i; ?> );" placeholder="QTY" type="text">
									<input type="hidden" onkeyup="allowNum(this);" name="non_oem_price[]" id="non_oem_price_<?php echo $i;?>" value="<?php echo $product['non_oem'];?>">
								</div></td>
								<td id="sub_total_<?php echo $i;?>"><span>$0.00</span>
									<input class="sub_total" type="hidden" value="0.00" name="sub_total[]"></td>
								</tr>
								<?php
								$i++;
								endforeach;?>
								<tr>
									<td colspan="7">
									<div class="ui grid">
										<div class="six wide column"></div>
										<div class="ten wide column">
											<div class="ui aligned basic segment">
												<div class="inline fields">
													<div class="field" style="width:200px;">
														<div class="ui radio checkbox">
															<input class="hidden" tabindex="0" name="payment_type" value="store_credit" onchange="populateTotal()" id="credit_payment" type="radio" checked="">
															<label style="font-size:18px;">Total Store Credit:</label>
														</div>
													</div>
													<div class="field">
														<input name="credit_total" type="text" id="credit_total" readonly>
													</div>
												</div>
											</div>
											<div class="ui horizontal divider" style="width:150px;"> Or </div>
											<div class="ui aligned basic segment">
												<div class="inline fields">
													<div class="field" style="width:200px;">
														<div class="ui radio checkbox">
															<input class="hidden" tabindex="0" name="payment_type" value="cash" onchange="populateTotal();"  type="radio" id="cash_payment">
															<label style="font-size:18px;">Cash:<span style="font-size:12px; color:#666; display:block;">(Via Paypal Transfer)</span></label>
														</div>
													</div>
													<div class="field">
														<input name="cash_total" id="cash_total" type="text" readOnly>
														<input type="hidden" id="temp_total" value="0.00">
													</div>
												</div>
                                                
											</div>
                                            
                                            <div id="paypal_email" style="display:none">
                                            <div class="ui horizontal divider" style="width:150px;">&nbsp;</div>
											<div class="ui aligned basic segment">
												<div class="inline fields">
													<div class="field" style="width:200px;">
														<div class="ui">
															
															<label style="font-size:18px;color:#000">PayPal Email:</label>
														</div>
													</div>
													<div class="field">
														<input name="paypal_email" id="cash_total" type="text" >
														
													</div>
												</div>
                                                
											</div>
                                            </div>
                                            
                                            
										</div>
                                        
                                        
									</div>
                                    
                                    
									</td>
								</tr>
							</tbody>
						</table>
 

<div class="field" style="margin-bottom:15px;font-weight:bold;font-size:15px">
If any LCD Screens are rejected by our Inspection Team, how should we proceed?
</div>
 <div class="field" style="margin-bottom:15px">
      <div class="ui radio checkbox">
        <input class="hidden" tabindex="0" name="option" checked="" type="radio" value="Dispose">
        <label>Dispose non-functional LCD Screens safely on my behalf.</label>
      </div>
    </div>
    
    <div class="field">
      <div class="ui radio checkbox">
        <input class="hidden" tabindex="0" name="option"  type="radio" value="Return">
        <label>Return non-functional LCD Screens to me.</label>
      </div>
    </div>
						
						<p class="" style="display:none" id="terms_and_conditions"><?php echo stripslashes($general['lower_text']);?></p>
						
                       <div class="field">
        <div class="ui checkbox">
          <input type="checkbox" id="xcheck" required />
          <label>I agree to the <a href="javascript:void()" onClick="$('#terms_and_conditions').toggle(200)">Terms and Conditions</a></label>
        </div>
      </div>
						<div style="text-align:center;">
							<button type="submit" class="ui large teal button" style="width:50%;">Submit Broken LCDs</button>
						</div>
					</form>
				</div>
				<script>
					function phoneMask(e){
						var s=e.val();
						var n = (s.length)-6;
						if(n==4){var p=n;}else{var p=5;}
						var regex = new RegExp('(\\d{2})(\\d{'+p+'})(\\d{4})');
						var text = s.replace(regex, "($1) $2-$3");
						e.val(text);

	//$(e).mask(m);
}

// Type
$('.hidden_shipping_form').on('keyup','.phone',function(){ 
	phoneMask($(this));
});

// On load
$('.phone').keyup();
$('select.dropdown')
.dropdown()
;</script> 
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
		$('#paypal_email input').attr('required','required');
		if(returnVal<0.00)
		{
			returnVal = 0.00;
		}
		$('#cash_total').val(returnVal.toFixed(2));
	}
	else
	{
		$('#paypal_email input').removeAttr('required');
$('#paypal_email').hide(500);
		returnVal = parseFloat(total);
           //alert('here');
           $('#credit_total').val(returnVal.toFixed(2));
           
       }


   }
// On load
$('.phone').keyup();</script> 
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
<?php echo $footer; ?>