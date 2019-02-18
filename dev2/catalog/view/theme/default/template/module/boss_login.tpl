<div id="boss-login">
<?php if (!$logged) { ?>
	<ul>
	<li><span><?php echo $text_welcome_1; ?></span></li>
	<li class="login-li">
		<span><?php echo $text_login; ?></span>
		<div id="content-login">
			<form action="<?php echo $action_login; ?>" method="post" enctype="multipart/form-data" id="logintop" >
				<div class="login-bor">
				<div class="login-frame">
					<input type="text" name="email" autocomplete="off"  onblur="if(this.value=='') this.value=this.defaultValue" onfocus="if(this.value==this.defaultValue) this.value=''" value="<?php echo $text_username; ?>" />
					<input type="password" name="password" onblur="if(this.value=='') this.value=this.defaultValue" onfocus="if(this.value==this.defaultValue) this.value=''" value="<?php echo $entry_password; ?>" />
					<a onclick="$('#logintop').submit();" class="button_pink"><span><?php echo $button_login; ?></span></a>
					<a class="forgotpass" href="<?php echo $forgotten; ?>"><?php  echo $text_forgotten; ?></a><br />
				</div>
				</div>
			</form>
		</div>
	</li>
	<li><span><?php echo $text_welcome_2; ?></span></li>
	</ul>
	
<?php } else { ?>
	<?php echo $text_logged; ?>

<?php } ?>
</div>