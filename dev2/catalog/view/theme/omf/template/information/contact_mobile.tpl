<?php echo $header; ?>
<div id="main" role="main">
	<?php echo $content_top; ?>	
	<ul id="breadcrumbs">
	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<li><?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
	<?php } ?>
	</ul>
	<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="contact">
	<section>
		<h2><?php echo $text_location; ?></h2>    
		<address class="vcard">
			<strong><?php echo $text_address; ?></strong>
			<span class="sname"><?php echo $store; ?></span>
			<span class="address"><?php echo $address; ?></span>
			<?php if ($telephone) { ?>
				<strong><?php echo $text_telephone; ?></strong>
				<span class="telephone"><?php echo $telephone; ?></span>		
			<?php } ?>
			<?php if ($fax) { ?>
				<strong><?php echo $text_fax; ?></strong>
				<span class="fax"><?php echo $fax; ?></span>
			<?php } ?>	  
		</address>
	</section>
	<section>
		<h2><?php echo $text_contact; ?></h2>   
		<ul>
			<li>
				<label for="name"><?php echo $entry_name; ?></label>
				<input type="text" name="name" value="<?php echo $name; ?>" />
				<?php if ($error_name) { ?>
				<span class="s-error"><?php echo $error_name; ?></span>
				<?php } ?>
			</li>
			<li>
				<label for=""><?php echo $entry_email; ?></label>
				<input type="email" name="email" value="<?php echo $email; ?>" />
				<?php if ($error_email) { ?>
				<span class="s-error"><?php echo $error_email; ?></span>
				<?php } ?>
			</li>
			<li>
				<label for=""><?php echo $entry_enquiry; ?></label>
				<textarea name="enquiry" cols="40" rows="10"><?php echo $enquiry; ?></textarea>
				<?php if ($error_enquiry) { ?>
				<span class="s-error"><?php echo $error_enquiry; ?></span>
				<?php } ?>
			</li>
			<li>
				<label for=""><?php echo $entry_captcha; ?></label>
				<input type="text" name="captcha" value="<?php echo $captcha; ?>" />
				<img src="index.php?route=information/contact/captcha" alt="" />
				<?php if ($error_captcha) { ?>
				<span class="s-error"><?php echo $error_captcha; ?></span>
				<?php } ?>	
			</li>
		</ul>		
	</section>
	<input type="submit" value="<?php echo $button_continue; ?>" />				
  </form>
  <?php echo $content_bottom; ?>
  </div>
<?php echo $footer; ?>