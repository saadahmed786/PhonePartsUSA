	<?php if ($error_warning) { ?>
	<div class="warning"><?php echo $error_warning; ?></div>
	<?php } ?>
	<form action="index.php?route=checkout/payment_method/validate" method="post">
		<?php if ($payment_methods) { ?>
		<p><?php echo $text_payment_method; ?></p>				
		<ul>
			<li>
				<ul>
					<?php foreach ($payment_methods as $payment_method) { ?>
					<li>
						<label for="<?php echo $payment_method['code']; ?>">
						<?php if ($payment_method['code'] == $code || !$code) { ?>
						<?php $code = $payment_method['code']; ?>
						<input type="radio" id="payment_method" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" checked="checked" />
						<?php } else { ?>
						<input type="radio" id="payment_method" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" />
						<?php } ?>
						<?php echo $payment_method['title']; ?></label>
					</li>
					<?php } ?>
				</ul>
			</li>			
		<?php } ?>
			<li>
				<label for="comment"><?php echo $text_comments; ?></label>
				<textarea id="comment" name="comment" rows="8" ><?php echo $comment; ?></textarea>					
			</li>
		</ul>
		<?php if ($text_agree) { ?>		
		<label for="agree">			
			<?php if ($agree) { ?>
			<input type="checkbox" name="agree" id= "agree" value="1" checked="checked" />
			<?php } else { ?>
			<input type="checkbox" name="agree" id= "agree"  value="1" />
			<?php } ?>
			<?php echo $text_agree; ?>
		</label>		
		<input type="submit" value="<?php echo $button_continue; ?>" id="button-payment-method" />	
		<?php } else { ?>		
		<input type="submit" value="<?php echo $button_continue; ?>" id="button-payment-method" />		
		<?php } ?>
	</form>