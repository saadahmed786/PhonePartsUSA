<?php echo $google_form; ?>
<div class="buttons">
	<div class="right"><a id="checkout" onclick="doSomething()"><span><?php echo $button_confirm; ?></span></a></div>
</div>

<script type="text/javascript"><!--

function doSomething() {

	$.ajax({ 

		type: 'GET',

		url: 'index.php?route=payment/google/confirm',

		success: function() {
		$('#payment').submit(); 

		}		

	});

};

//--></script>