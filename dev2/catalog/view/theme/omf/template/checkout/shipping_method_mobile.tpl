	<?php if ($error_warning) { ?>
	<div class="warning"><?php echo $error_warning; ?></div>
	<?php } ?>		
	<form action="index.php?route=checkout/shipping_method/validate" method="post">
	<?php if (isset($shipping_methods)) { ?>
		<p><?php echo $text_shipping_method; ?></p>
		<ul>
			<?php foreach ($shipping_methods as $shipping_method) { ?>
			<li>
				<h2><?php echo $shipping_method['title']; ?></h2>
				<ul>
					<?php if (!$shipping_method['error']) { ?>
						<?php foreach ($shipping_method['quote'] as $quote) { ?>
					<li>
						<span class="shipping_price"><?php echo $quote['text']; ?></span>
						<label for="<?php echo $quote['code']; ?>">					
						<?php if ($quote['code'] == $code || !$code) { ?>
						<?php $code = $quote['code']; ?>
						<input type="radio" id="shipping_method" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" checked="checked" />
						<?php } else { ?>
						<input type="radio" id="shipping_method" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" />
						<?php } ?>					
						<?php echo $quote['title']; ?></label>						
					</li>
						<?php } ?>			
					<?php } else { ?>			
					<li class="s-error">
						<?php echo $shipping_method['error']; ?>
					</li>				
					<?php } ?>				
				</ul>
			</li>
			<?php } ?>
	<?php } ?>
			<li>
				<label for="comment"><?php echo $text_comments; ?></label>
				<textarea id="comment" name="comment" rows="8" ><?php echo $comment; ?></textarea>				
			</li>
		</ul>
		<input type="submit" value="<?php echo $button_continue; ?>"  id="button-shipping-method"/>		
	</form>