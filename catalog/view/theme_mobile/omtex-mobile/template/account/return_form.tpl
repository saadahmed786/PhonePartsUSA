<?php echo $header; ?>
<?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>

<?php echo $content_top; ?>
<div data-role="content">
Our EZ-Returns Program is currently under development for mobile devices. To access our EZ-Returns Program, using a computer or tablet, scroll to the bottom of our website and select "Return Items"
</div>  
  <?php echo $content_bottom; ?>
<script type="text/javascript"><!--
$(document).ready(function() {
	$('.date').datepicker({dateFormat: 'yy-mm-dd'});
});
//--></script> 
<?php echo $footer; ?>