<?php echo $header; ?>
<div id="main" role="main">
	<?php echo $content_top; ?>	
	<h1><?php echo $text_checkout_option; ?></h1>
	
	<section>
		<h2><?php echo $text_new_customer; ?></h2>			
		<ul>
	<?php if ($guest_checkout) { ?>		
			<li><a href="<?php echo $guest_checkout_url; ?>" class="button"><?php echo $text_guest; ?></a></li>			
	<?php } ?>			
			<li><a href="<?php echo $register_checkout_url; ?>" class="button"><?php echo $text_register; ?></a></li>
		</ul>
		<p><?php echo $text_register_account; ?></p>
	</section>
	
	<section>
		<h2><?php echo $text_returning_customer; ?></h2>
		<?php if(isset($errors['warning'])) echo '<span class="warning">'. $errors['warning'] . '</span>';?>			
		<form action="index.php?route=checkout/login" method="post">
			<ul>
				<li>
					<label for="email"><?php echo $entry_email; ?></label>
					<input type="email" id="email" name="email" value="" />
				</li>
				<li>
					<label for="password"><?php echo $entry_password; ?></label>
					<input type="password" id="password" name="password" value="" />	
				</li>
			</ul>
			<a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a>
			<input type="submit" value="<?php echo $button_login; ?>" />				
		</form>
	</section>	
	<?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>