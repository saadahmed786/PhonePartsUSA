<br>
<div class="buttons">
  <div class="center">
     <input type="button" value="Confirm Order" id="button-confirm" class="btn4" />
  </div>
</div>
<div class="" style="text-align:center;display:none">
<span style="font-size:10px"></span></div>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
	$.ajax({ 
		type: 'get',
		url: 'index.php?route=payment/terms/confirm',
		success: function() {
			
			location = '<?php echo $continue; ?>';
		}		
	});
});
//--></script> 
