<!-- Quick Checkout quickcheckout/login.tpl -->
  <?php	$config = $this->config->get('quickcheckout'); ?>
<div id="login" class="box box-border" style="display:<?php if(!$config['checkout_display_login']){ echo 'none'; } ?>">
    <div class="box-heading"><?php echo $text_returning_customer; ?></div>
    <div class="box-content">
        <div class="block-row email">
            <label><?php echo $entry_email; ?></label>
            <input type="text" name="email" value="" />
        </div>
        <div class="block-row password">
            <label><?php echo $entry_password; ?></label>
            <input type="password" name="password" value="" />
        </div>
        <div class="form-inline">
        	<input type="button" value="<?php echo $button_login; ?>" id="button-login" class="button btn btn-primary" /> <a class="btn" href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div id="register_option" class="box box-border <?php if ($account == 'register') { ?> selected <?php } ?>" style="display:<?php if(!$config['checkout_display_register']){ echo 'none'; } ?>">
    <div class="box-heading"><?php echo $text_new_customer; ?></div>
    <div class="box-content">
        <div class="block-row register">
            <label for="register">
                <?php if ($account == 'register') { ?>
                    <input type="radio" name="account" value="register" id="register" checked="checked" class="styled" />
                <?php } else { ?>
                    <input type="radio" name="account" value="register" id="register" class="styled" />
                <?php } ?>
                <b><?php echo $text_register; ?></b>
            </label>
        </div>
        <p class="clear"><?php echo $text_register_account; ?></p>
    </div>
</div>
<?php if ($guest_checkout) { ?>
<div id="guest_option" class="box box-border <?php if ($account == 'guest') { ?> selected <?php } ?>" style="display:<?php if(!$config['checkout_display_guest']){ echo 'none'; } ?>">
    <div class="box-heading"><?php echo $text_new_customer; ?></div>
    <div class="box-content">
        <div class="block-row guest">
            <label for="guest">
                <?php if ($account == 'guest') { ?>
                    <input type="radio" name="account" value="guest" id="guest" checked="checked" class="styled" />
                <?php } else { ?>
                    <input type="radio" name="account" value="guest" id="guest" class="styled" />
                <?php } ?>
                <b><?php echo $text_guest; ?></b>
            </label>
        </div>
        <br class="clear"/>
    </div>
</div>
<?php } ?> 
<script><!--
$(function(){
	if($.isFunction($.fn.uniform)){
		$(" .styled, input:radio.styled").uniform().removeClass('styled');
	}
});
//--></script>