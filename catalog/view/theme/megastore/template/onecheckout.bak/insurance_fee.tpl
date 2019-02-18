<?php if($insurance_required){ ?>
<div class="buttons">
  <div>
	<label for="insurance_fee"><?php echo $entry_insurance_fee; ?> - <?php echo $insurance_fee_text; ?></label>
	<?php if($insurance_selection==2){ ?>
	<input type="checkbox" name="insurance_fee" value="1" id="insurance_fee" checked="checked" disabled="disabled" />
	<?php } else { ?>
	<input type="checkbox" name="insurance_fee" value="1" id="insurance_fee" <?php if($insurance_fee){ ?>checked="checked"<?php } ?> />
	<?php } ?>
  </div>
</div>
<script type="text/javascript"><!--
<?php if($insurance_selection==2 && !$insurance_fee){ ?>
$.ajax({
		url: 'index.php?route=onecheckout/insurance_fee/saveinsurance',
		type: 'post',
		data: $('#shipping-method input[name=\'insurance_fee\']:checked'),
		success: function() {
			confirmorder();
		}
	});	
<?php } ?>
$('#shipping-method input[name=\'insurance_fee\']').change(function() {
	$.ajax({
		url: 'index.php?route=onecheckout/insurance_fee/saveinsurance',
		type: 'post',
		data: $('#shipping-method input[name=\'insurance_fee\']:checked'),
		success: function() {
			confirmorder();
		}
	});	
});
//--></script> 
<?php } else { ?>
<?php if($insurance_fee){ ?>
<script type="text/javascript"><!--
	$.ajax({
		url: 'index.php?route=onecheckout/insurance_fee/saveinsurance',
		success: function() {
			confirmorder();
		}
	});	
//--></script> 
<?php } ?>
<?php } ?>