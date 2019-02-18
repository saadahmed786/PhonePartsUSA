<div class="secure">
	<h2>256-Bit Secure Payment</h2>
</div>
<div class="credit_form" id="payment">
	<form>
		<h5>Credit Card Information</h5>
		<p>
			<label>Full Name</label>
			<input type="text" name="cc_owner" value="" />
		</p>
		<p>
			<label>Number</label>
			<input type="text" name="cc_number" value="" />
		</p>
		<p style="width: 22%;">
			<label>Expires</label>
		</p>
			<div style="width: 105px; float: left; padding-right:5px;">
				<select name="cc_expire_date_month">
				<?php foreach ($months as $month) { ?>
				<option value="<?php echo $month['value']; ?>"><?php echo $month['value']; ?></option>
				<?php } ?>
				</select>
			</div>
			<div style="width: 105px; float: left;">
				<select name="cc_expire_date_year">
				<?php foreach ($year_expire as $year) { ?>
				<option value="<?php echo $year['value']; ?>"><?php echo $year['value']; ?></option>
				<?php } ?>
				</select>
			</div>

		<p>
			<label>CCV Code</label>
			<input type="text" name="cc_cvv2" value="" size="3"  class="cc_ww"/>
			<img src="/catalog/view/theme_mobile/omtex-mobile/image/ccv_img.png">
		</p>
	</form>
</div>
<!--<h2><?php echo $text_credit_card; ?></h2>
<div id="payment">
  <table class="form">
    <tr>
      <td><?php echo $entry_cc_owner; ?></td>
      <td><input type="text" name="cc_owner" value="" /></td>
    </tr>
    <tr>
      <td><?php echo $entry_cc_number; ?></td>
      <td><input type="text" name="cc_number" value="" /></td>
    </tr>
    <tr>
      <td><?php echo $entry_cc_expire_date; ?></td>
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
      <td><?php echo $entry_cc_cvv2; ?></td>
      <td><input type="text" name="cc_cvv2" value="" size="3" /></td>
    </tr>
  </table>
</div>-->
<div class="buttons" style="float: left; width: 100%;">
  <div class="right"><input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button" data-theme="a" /></div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
	$.ajax({
		url: 'index.php?route=payment/authorizenet_aim/send',
		type: 'post',
		data: $('#payment :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#button-confirm').attr('disabled', true);
			
			$('#payment').before('<div class="attention" style="width: 85%; padding: 20px; background: rgb(72, 162, 72); float: left; color: #fff;"><img src="https://fragiledevelopment.files.wordpress.com/2009/11/loading2.gif" alt="" style="width: 18px;margin-right: 20px;"/><?php echo 'Payment Processing'; ?></div>');
		},
		success: function(json) {

			if (json['error']) {
				alert(json['error']);
				
				$('#button-confirm').attr('disabled', false);
			} 
		
			$('.attention').remove();
			
			if (json['success']) {
				location = json['success'];
			}
		}
	});
});
//--></script>
