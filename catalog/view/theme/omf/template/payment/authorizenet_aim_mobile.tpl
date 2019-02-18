<form action="index.php?route=payment/authorizenet_aim/send" method="post" id="payment">
	<h2><?php echo $text_credit_card; ?></h2>
	<ul class="form">
		<li>
			<label for="cc_owner"><?php echo $entry_cc_owner; ?></label>
			<input type="text" id="cc_owner" name="cc_owner" value="" />
		</li>
			<label for="cc_number"><?php echo $entry_cc_number; ?></label>
			<input type="text" id="cc_number" name="cc_number" value="" />
		</li>
		<li>
			<label for="cc_expire_date_month"><?php echo $entry_cc_expire_date; ?></label>
			<select id="cc_expire_date_month" name="cc_expire_date_month">
			<?php foreach ($months as $month) { ?>
			<option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
			<?php } ?>
			</select>
			<select id="cc_expire_date_year" name="cc_expire_date_year">
			<?php foreach ($year_expire as $year) { ?>
			<option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
			<?php } ?>
			</select>
		</li>
		<li>
			<label for="cc_cvv2"><?php echo $entry_cc_cvv2; ?></label>
			<input type="text" id="cc_cvv2" name="cc_cvv2" value="" size="3" />
		</li>
	</ul>
	<input type="submit" id="button-confirm" value="<?php echo $button_confirm; ?>"/>
</form>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
	$.ajax({
		url: 'index.php?route=payment/authorizenet_aim/send',
		type: 'post',
		data: $('#payment input, #payment select'),
		dataType: 'json',		
		beforeSend: function() {
			$('#button-confirm').attr('disabled', true);
			$('#payment').before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		complete: function() {
			$('#button-confirm').removeAttr('disabled');
			$('.attention').remove();
		},				
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			}
			
			if (json['success']) {
				location = json['success'];
			}
		}
	});
	return false;
});
//--></script>