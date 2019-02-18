<form action="index.php?route=payment/sagepay_direct/send" method="post" id="payment">
	<h2><?php echo $text_credit_card; ?></h2>
	<ul class="form">
		<li>
			<label for="cc_owner"><?php echo $entry_cc_owner; ?></label>
			<input type="text" id="cc_owner" name="cc_owner" value="" />
		</li>
		<li>
			<label for="cc_type"><?php echo $entry_cc_type; ?></label>
			<select id="cc_type" name="cc_type">
			<?php foreach ($cards as $card) { ?>
			<option value="<?php echo $card['value']; ?>"><?php echo $card['text']; ?></option>
			<?php } ?>
			</select>
		</li>
		<li>
			<label for="cc_number"><?php echo $entry_cc_number; ?></label>
			<input type="text" id="cc_number" name="cc_number" value="" />
		</li>
		<li>
			<label for="cc_start_date_month"><?php echo $entry_cc_start_date; ?></label>
			<select id="cc_start_date_month" name="cc_start_date_month">
			<?php foreach ($months as $month) { ?>
			<option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
			<?php } ?>
			</select>
			<select id="cc_start_date_year" name="cc_start_date_year">
			<?php foreach ($year_valid as $year) { ?>
			<option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
			<?php } ?>
			</select>
			<?php echo $text_start_date; ?>
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
		<li>
			<label for="cc_issue"><?php echo $entry_cc_issue; ?></label>
			<input type="text" id="cc_issue" name="cc_issue" value="" size="1" />
			<?php echo $text_issue; ?>
		</li>
  	</ul>
	<input type="submit" value="<?php echo $button_confirm; ?>" id="button-confirm"/>
</form>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
	$.ajax({
		url: 'index.php?route=payment/sagepay_direct/send',
		type: 'post',
		data: $('#payment input, #payment select'),
		dataType: 'json',		
		beforeSend: function() {
			$('#button-confirm').attr('disabled', true);
			$('#payment').before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		complete: function() {
			$('#button-confirm').attr('disabled', false);
			$('.attention').remove();
		},				
		success: function(json) {
			if (json['ACSURL']) {
				$('#3dauth').remove();
				
				html  = '<form action="' + json['ACSURL'] + '" method="post" id="3dauth">';
				html += '<input type="hidden" name="MD" value="' + json['MD'] + '" />';
				html += '<input type="hidden" name="PaReq" value="' + json['PaReq'] + '" />';
				html += '<input type="hidden" name="TermUrl" value="' + json['TermUrl'] + '" />';
				html += '</form>';
				
				$('#payment').after(html);
				
				$('#3dauth').submit();
			}
			
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