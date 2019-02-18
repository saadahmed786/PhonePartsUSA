<style>
#xcontent2{width: 100%;
  height: 100%;
  top: 0px;
  left: 0px;
  position: fixed;
  display: block;
  opacity: 0.8;
  background-color: #000;
  z-index: 99;}


</style>
<div id="xcontent2" style="display:none"><div style="color:#fff;
top:40%;
position:fixed;
left:40%;
font-weight:bold;font-size:25px"><img src="catalog/view/theme/default/image/loader_white.gif" /><span style="margin-left: 11%;
    margin-top: 33%;
    position: absolute;
    
    width: 270px;">Confirming Order...</span></div></div>  
<div id="pp_payflow">

<div class="messages"> </div>
  
  <div style="  margin:0 auto; position:relative; border:1px solid #ccc; background:#fff; padding:20px;">
    	<h2 style="margin:0; padding:22px 0px 0px 140px;font-family:'Myriad Pro'; font-size:28px; background:url(catalog/view/theme/default/image/payflow/lock_icon_2.png) no-repeat 0 0; height:88px;">Secure Credit Card Payment<br />
        	<span style="margin:0; padding:0; font-family:'Myriad Pro'; font-size:16px; font-weight:normal;">This is a Secure 256-bit SSL encrypted payment</span>
        </h2>        
        <form style="margin:20px 0 0 0;">
        <fieldset style="border:none; margin:15px 0; margin-left:185px; padding:0;">
        	
            <img style="position:relative; top:9px;" src="catalog/view/theme/default/image/payflow/cards_img.png" alt="cards" />
        </fieldset>
        <fieldset style="border:none; margin:10px 0; padding:0;">
        	<label style="font-weight:bold;margin:0; padding:0; font-family:'Myriad Pro'; font-size:14px; width:150px; display:inline-block;">Card Number 
            <span style="color:#F00">*</span></label>
            <input style="margin:0; width:250px; color:#000; border:1px solid #ccc; height:28px" type="text" name="cc_number" />
            <label style="margin:5px 0 0 155px; padding:0; font-family:'Myriad Pro'; font-size:12px; display:block;">No Spaces/Dashes<br />
            	   Example: 1234123412341234
            </label>
        </fieldset>
        <fieldset style="border:none; margin:10px 0; padding:0;">
        	<label style="font-weight:bold;margin:0; padding:0; font-family:'Myriad Pro'; font-size:14px; width:150px; display:inline-block;">Expiration Date 
            <span style="color:#F00">*</span></label>
            <select style="margin:0; width:125px; color:#000; border:1px solid #ccc; height:28px" name="cc_expire_date_month">
            	<?php foreach ($months as $month) { ?>
          <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
          <?php } ?>
            </select>
            <select style="margin:0; width:125px; color:#000; border:1px solid #ccc; height:28px" name="cc_expire_date_year">
            	 <?php foreach ($year_expire as $year) { ?>
          <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
          <?php } ?>
            </select>
        </fieldset>
        <fieldset style="border:none; margin:10px 0; padding:0;">
        	<label style="font-weight:bold;margin:0; padding:0; font-family:'Myriad Pro'; font-size:14px; width:150px; display:inline-block;">CVV2 
            <span style="color:#F00">*</span></label>
            <input style="margin:0; width:75px; color:#000; border:1px solid #ccc; height:28px;" type="text" name="cc_cvv2" />
            <img style="position:relative; top:9px;" src="catalog/view/theme/default/image/payflow/captcha.png" alt="captcha" />
        </fieldset>
        <p style="margin:0 0 0 155px; padding:0; font-family:'Myriad Pro'; font-size:12px; width:500px;"><span style="font-weight:bold;">Note:</span> The security code, also known as the CID or CVV, is the three digit number on the back of a VISA, MasterCard, or Discover card (often following a four digit number representing the last 4 digits of your credit card number). </p>
        </form>
        <img style="position: absolute; float:right; display:inline; top:100px; right:100px; width:300px;" src="catalog/view/theme/default/image/payflow/secure_img.jpg" alt="image" />
    </div>
    <br>
<div class="buttons">
  <div class="center">
   <input onclick="return confirmSubmit();" type="button" class="pp_payflow_button btn4" value="Confirm Order" />
  </div>
</div>
</div>

<script type="text/javascript"><!--
function helpboxClick(){
$('.helpbox').toggle();
return false;
}
function confirmSubmit() {
	function isNumeric(n){
		return !isNaN(parseFloat(n)) && isFinite(n);
	}
	$.ajax({
		type: 'POST',
		url: 'index.php?route=payment/pp_payflow_pro/send',
		data: $('#pp_payflow :input'),
		dataType: 'json',
		beforeSend: function() {
			if ( $('.pp_payflow_button').attr('disabled')) {
				return false;
			}

			var error=false;
			$('#pp_payflow .pp_payflow_error').remove();

			var cc_number=$('#pp_payflow input[name=cc_number]');
			cc_number.val(cc_number.val().replace(/[ -]/g,''));
			var length=cc_number.val().length;
			if (length<13 || length>16 || !isNumeric(cc_number.val())) {
				cc_number.after('<span class="error pp_payflow_error"><?php echo $entry_cc_number_error; ?></span>');
				error=true;
			}

			var cc_cvv2=$('#pp_payflow input[name=cc_cvv2]');
			var length=cc_cvv2.val().length;
			if (length<3 || length>4 || !isNumeric(cc_cvv2.val())) {
				cc_cvv2.next().after('<span class="error pp_payflow_error"><?php echo $entry_cc_cvv2_error; ?></span>');
				error=true;
			}

			if (error) return false;

			$('.pp_payflow_button').attr('disabled', 'disabled').after('<div class="wait"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
			$('#xcontent2').show();
		},
		success: function(data) {
			if (data.error) {
				//alert(data.error);
                $('#pp_payflow .messages').append('<div class="warning">'+data.error+'<img src="catalog/view/theme/default/image/close.png" alt="" class="close"></div>');
				//$('.pp_payflow_button').data('disabled',false).attr('disabled', '');
				$('.pp_payflow_button').removeAttr('disabled');
				$('#xcontent2').hide();
			}

			$('.wait').remove();
			if (data.success) {
                $('#pp_payflow .messages').empty();
				location = data.success;
			}
		}
	});
}
//--></script>
<style type="text/css">
.close {
    float:right;
}
.helpbox{
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 11px;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.3);
    display: none;
    text-align: left;
    white-space: normal;
    max-width:525px;
    margin-bottom:15px;
}
.cclogos{
    margin:10px 0 0;
}
</style>
