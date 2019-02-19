<!--
//-----------------------------------------
// Author: Qphoria@gmail.com
// Web: http://www.OpenCartGuru.com/
//-----------------------------------------
-->
<?php if (isset($error)) { ?>
<div class="warning"><?php echo $error; ?></div>
<?php } ?>
<form action="<?php echo $action; ?>" method="post" id="checkout-form">
  <?php foreach (@$fields as $key => $value) { ?>
    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>" />
  <?php } ?>
</form>
<?php if ($text_ppx_agree) { ?>
<div class="buttons">
  <div class="right" style="width:55%;"><?php echo $text_ppx_agree; ?>
    <?php if ($agree) { ?>
    <input type="checkbox" name="agree" value="1" checked="checked" />
    <?php } else { ?>
    <input type="checkbox" name="agree" value="1" />
    <?php } ?>
    <a id="button-confirm" class="button_pink" onclick="confirmSubmit();"><span><?php echo $button_confirm; ?></span></a></div>
</div>
<?php } else { ?>
<div class="buttons">
  <div class="right" style="width:55%;"><a id="button-confirm" class="button_pink" onclick="confirmSubmit();"><span><?php echo $button_confirm; ?></span></a></div>
</div>
<?php } ?>
<script type="text/javascript"><!--
function confirmSubmit() {
	<?php if ($text_ppx_agree) { ?>
	if (!$('input[name="agree"]').is(':checked')) {
		alert('<?php echo html_entity_decode($error_agree); ?>');
		$('input[name="agree"]').focus();
		return false;
	}
	<?php } ?>

	$.ajax({
		type: 'GET',
		url: 'index.php?route=payment/paypal_express/confirm',
		success: function() {
			$('#checkout-form').submit();
		}
	});
}
//--></script>