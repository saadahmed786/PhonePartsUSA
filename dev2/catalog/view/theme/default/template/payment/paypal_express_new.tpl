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
 <div class="buttons">
  <div class="center" style="width:55%;"><input type="button" id="button-confirm" class="btn4" onclick="confirmSubmit()" value="Pay & Complete Order"></div>
</div>
<?php } else { ?>
<div class="buttons">
  <div class="center" style="width:55%;"><input type="button" id="button-confirm" class="btn4" onclick="confirmSubmit()" value="Pay & Complete Order"></div>
</div>
<?php } ?>
<div style="text-align:center">
<span style="font-size:10px">Pressing the button above will transfer you to Paypal. After completing the payment, you will be directed back to our website.<br>An order confirmation page will be displayed showing your Order ID and Shipping Address.</span></div>
<script type="text/javascript"><!--
function confirmSubmit() {
	<?php if ($text_ppx_agree) { ?>
	/*if (!$('input[name="agree"]').is(':checked')) {
		alert('<?php echo html_entity_decode($error_agree); ?>');
		$('input[name="agree"]').focus();
		return false;
	}*/
	<?php } ?>

	$.ajax({
		type: 'GET',
		url: 'index.php?route=payment/paypal_express_new/confirm',
		success: function() {
			$('#checkout-form').submit();
		}
	});
}
//--></script>