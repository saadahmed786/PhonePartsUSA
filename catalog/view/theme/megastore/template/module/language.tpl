<?php if (count($languages) > 1) { ?>
		<!-- Language -->
		
		<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="language_form">
		
			<div class="switcher">
				
				<?php foreach ($languages as $language) { ?>
				<?php if ($language['code'] == $language_code) { ?>
				<p><span><?php echo $text_language; ?>:</span> <img src="image/flags/<?php echo $language['image']; ?>" width="16px" height="11px" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></p>				
				<?php } ?>
				<?php } ?>
				<div class="option">
					<ul>
					
						<?php foreach ($languages as $language) { ?>
						<li><a onclick="$('input[name=\'language_code\']').attr('value', '<?php echo $language['code']; ?>'); $('#language_form').submit();"><?php echo $language['name']; ?></a></li>
						<?php } ?>
					
					</ul>
				</div>
				
			</div>
			
	    <input type="hidden" name="language_code" value="" />
	    <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
			
		</form><!-- End currency form -->
		
		<!-- End language -->
<?php } ?>
