<br>
<div class="buttons">
  <div class="center">
     <input type="button" value="Confirm Pick Up Order" id="button-confirm" class="btn4" />
  </div>
</div>
<div class="" style="text-align:center">
<span style="font-size:10px">Prices are subject to change for cash payment method orders. Please pay with PayPal to avoid prices changes. Orders may take up to 30 minutes until available for pickup. Please call, email or chat message us for faster processing. During pickup, we suggest customers test and inspect all parts. Our technicians test all popular brands for quality assurance during customer pickup. Parts which are tested working, cannot be exchanged or returned. Parts with defects are eligible for exchanges and returns. Defects identified by customers after pickup, are to be confirmed by our quality control department.</span></div>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
	$.ajax({ 
		type: 'get',
		url: 'index.php?route=payment/cod/confirm',
		success: function() {
			location = '<?php echo $continue; ?>';
		}		
	});
});
//--></script> 
