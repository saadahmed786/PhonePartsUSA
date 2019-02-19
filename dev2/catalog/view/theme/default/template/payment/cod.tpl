<br>
<div class="buttons">
  <div class="center">
    <input type="button" value="Confirm Pick Up Order" id="button-confirm" class="btn4" />
  </div>
</div>
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
