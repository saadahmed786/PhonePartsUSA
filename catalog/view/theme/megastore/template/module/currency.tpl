<?php if (count($currencies) > 1) { ?>

		<!-- Currency -->
		
		<form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" enctype="multipart/form-data" id="currency_form">
		
			<div class="switcher">
			
							<?php foreach ($currencies as $currency) { ?>
							<?php if ($currency['code'] == $currency_code) { ?>
							<p><span><?php echo $text_currency; ?>:</span> <?php echo $currency['title']; ?></p>
							<?php } ?>
							<?php } ?>
				<div class="option">
					<ul>
				
								<?php foreach ($currencies as $currency) { ?>
	            			<li><a href="javascript:;" onclick="$('input[name=\'currency_code\']').attr('value', '<?php echo $currency['code']; ?>'); $('#currency_form').submit();"><?php echo $currency['title']; ?></a></li>
								<?php } ?>
						
					</ul>
				</div>
				
			</div>
			
      				<input type="hidden" name="currency_code" value="" />
      				<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
			
		</form><!-- End currency form -->
		
		<!-- End currency -->
		
<?php } ?>
