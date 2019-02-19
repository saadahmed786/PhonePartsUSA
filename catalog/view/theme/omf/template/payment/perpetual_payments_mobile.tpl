<h2><?php echo $text_credit_card; ?></h2>
<div class="content" id="payment">
  <ul>
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
    </li>        
    <li>
        <select name="cc_start_date_year" id="cc_start_date_year">
          <?php foreach ($year_valid as $year) { ?>
          <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
          <?php } ?>
        </select>
        <?php echo $text_start_date; ?>
    </li>
    <li>
      <label for="cc_expire_date_month"><?php echo $entry_cc_expire_date; ?></label>
      <select name="cc_expire_date_month" id="cc_expire_date_month">
          <?php foreach ($months as $month) { ?>
          <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
          <?php } ?>
        </select>
    </li>
    <li>        
        <select name="cc_expire_date_year" id="cc_expire_date_year">
          <?php foreach ($year_expire as $year) { ?>
          <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
          <?php } ?>
        </select>
    </li>
    <li>
      <label for="cc_cvv2"><?php echo $entry_cc_cvv2; ?></label>
      <input type="text" name="cc_cvv2" id="cc_cvv2" value="" size="3" />
    </li>
    <li>
      <label for="cc_issue"><?php echo $entry_cc_issue; ?></label>
      <input type="text" id="cc_issue" name="cc_issue" value="" size="1" />
        <?php echo $text_issue; ?>
    </li>
  </ul>
</div>
<div class="buttons">
  <div class="right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button" />
  </div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
	$.ajax({
		url: 'index.php?route=payment/perpetual_payments/send',
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
});
//--></script> 