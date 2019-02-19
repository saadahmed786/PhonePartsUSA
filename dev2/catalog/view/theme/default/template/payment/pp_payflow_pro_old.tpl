<div id="pp_payflow">
<div class="buttons">
  <div class="right">
    <a onclick="return confirmSubmit();" class="pp_payflow_button button"><span><?php echo $button_confirm; ?></span></a>
  </div>
</div>
<div class="messages"> </div>
  <h2><?php echo $text_credit_card; ?></h2>
  <table class="form">
    <tr>
      <td></td>
      <td>
          <img alt="Visa MasterCard Amex Discover" class="cclogos" src="catalog/view/theme/default/image/payment/cclogos.png">
      </td>
    </tr>
    <tr>
      <td><span class="required">*</span> <?php echo $entry_cc_number; ?></td>
      <td><input type="text" name="cc_number" value="" autocomplete="off" class="large-field" /></td>
    </tr>
    <tr>
      <td><span class="required">*</span> <?php echo $entry_cc_expire_date; ?></td>
      <td><select name="cc_expire_date_month">
          <?php foreach ($months as $month) { ?>
          <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
          <?php } ?>
        </select>
        /
        <select name="cc_expire_date_year">
          <?php foreach ($year_expire as $year) { ?>
          <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
          <?php } ?>
        </select></td>
    </tr>
    <tr>
      <td><span class="required">*</span> <?php echo $entry_cc_cvv2; ?></td>
      <td><input type="text" name="cc_cvv2" value="" size="3" autocomplete="off" /> <label class="help_icon">
                            <a onclick="return helpboxClick();" id="openhelpbox" href="#" title="" class="button">?</a>
                                                </label>
      </td>
    </tr>
    <tr>
      <td></td>
      <td>
            <div id="helpbox" class="helpbox"><div class="content">
            <span class="close"><a onclick="return helpboxClick();" href="#" class="button"><i>×</i></a></span>
            <h3>How to find your credit card security code</h3>
              <table>
                <tr>
                    <td> <img alt="VISA, MasterCard, &amp; Discover" src="catalog/view/theme/default/image/payment/ccvisahelp.jpg"> </td>
                    <td> <h4>VISA, MasterCard, &amp; Discover</h4> <p>The security code, also known as the CID or CVV, is the three digit number on the back of a VISA, MasterCard, or Discover card (often following a four digit number representing the last 4 digits of your credit card number).</p>
                    </td>
                </tr>
                <tr>
                    <td> <img alt="American Express" src="catalog/view/theme/default/image/payment/ccamexhelp.jpg"> </td>
                    <td> <h4>American Express</h4> <p>For American Express, this number is the four digit number on the front of the card (often slightly above and to the right of the card number).</p> </td>
                </tr>
              </table>
            </div></div>

      </td>
    </tr>
  </table>
<div class="buttons">
  <div class="right">
    <a onclick="return confirmSubmit();" class="pp_payflow_button button"><span><?php echo $button_confirm; ?></span></a>
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
			if ( $('.pp_payflow_button').data('disabled')) {
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

			$('.pp_payflow_button').data('disabled',true).attr('disabled', 'disabled').after('<div class="wait"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		success: function(data) {
			if (data.error) {
				//alert(data.error);
                $('#pp_payflow .messages').append('<div class="warning">'+data.error+'<img src="catalog/view/theme/default/image/close.png" alt="" class="close"></div>');
				$('.pp_payflow_button').data('disabled',false).attr('disabled', '');
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
