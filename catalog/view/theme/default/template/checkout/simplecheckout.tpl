<?php if ($template == 'shoppica' || $template == 'shoppica2') { ?>
<?php echo $header; ?>
<div id="content">
<?php } else { ?>
<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
<?php } ?>
    <link rel="stylesheet" type="text/css" href="catalog/view/theme/<?php echo $template ?>/stylesheet/simple.css" />
    <script type="text/javascript" src="catalog/view/javascript/simplecheckout.js"></script>
    <script type="text/javascript" src="catalog/view/javascript/jquery/jquery.maskedinput-1.3.min.js"></script>
    <?php if ($template == 'shoppica' || $template == 'shoppica2') { ?>
    <?php if ($template == 'shoppica') { ?>
    <script type="text/javascript" src="catalog/view/theme/<?php echo $this->config->get('config_template') ?>/js/jquery/jquery.prettyPhoto.js"></script>
    <link rel="stylesheet" type="text/css" href="catalog/view/theme/<?php echo $this->config->get('config_template') ?>/stylesheet/prettyPhoto.css" media="all" />
    <?php } elseif ($template == 'shoppica2') { ?>
    <script type="text/javascript" src="catalog/view/theme/<?php echo $this->config->get('config_template') ?>/javascript/prettyphoto/js/jquery.prettyPhoto.js"></script>
    <link rel="stylesheet" type="text/css" href="catalog/view/theme/<?php echo $this->config->get('config_template') ?>/javascript/prettyphoto/css/prettyPhoto.css" media="all" />
    <?php } ?>
    <div id="intro">
    <div id="intro_wrap">
        <div class="container_12 s_wrap">
            <div id="breadcrumbs" class="grid_12 s_col_12">
            <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
            <?php } ?>
            </div>
            <h1><?php echo $heading_title; ?></h1>
        </div>
    </div>
    </div>
    <?php } else { ?>
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <h1><?php echo $heading_title; ?></h1>
    <?php } ?>
    <!-- simplecheckout form -->
    <div class="simplecheckout">
        <?php 
            $replace = array(
	  			'{left_column}' => '<div class="simplecheckout-left-column">',
	  			'{/left_column}' => '</div>',
	  			'{right_column}' => '<div class="simplecheckout-right-column">',
      			'{/right_column}' => '</div>',
      			'{customer}' => '<div class="simplecheckout-block" id="simplecheckout_customer">'. $simplecheckout_customer .'</div>',
     			'{cart}' => '<div class="simplecheckout-block" id="simplecheckout_cart">' . $simplecheckout_cart . '</div>',
      			'{shipping}' => $has_shipping ? '<div class="simplecheckout-block" id="simplecheckout_shipping">' . $simplecheckout_shipping . '</div>' : '',
      			'{payment}' => '<div class="simplecheckout-block" id="simplecheckout_payment">' . $simplecheckout_payment . '</div>',
                '{agreement}' => '<div class="simplecheckout-block" id="simplecheckout_agreement"></div>'
			);
            
            if ($simple_common_view_agreement_text && isset($information_title) && isset($information_text)) { 
                $replace['{agreement}'] = '<div class="simplecheckout-block" id="simplecheckout_agreement">';
                $replace['{agreement}'] .= '<div class="simplecheckout-block-heading">' . $information_title . '</div>';
                $replace['{agreement}'] .= '<div class="simplecheckout-block-content simplecheckout-scroll">' . $information_text . '</div>';
                $replace['{agreement}'] .= '</div>';
            }
        
            $find = array(
	  			'{left_column}',
	  			'{/left_column}',
	  			'{right_column}',
      			'{/right_column}',
      			'{customer}',
     			'{cart}',
      			'{shipping}',
      			'{payment}',
                '{agreement}'
			);	
			
            echo trim(str_replace($find, $replace, $simple_common_template));
        ?>
        <!-- order button block -->
        <div class="simplecheckout-button-block" <?php if ($block_order) { ?>style="display:none;"<?php } ?>>
            <?php if ($template == 'shoppica' || $template == 'shoppica2') { ?>
                <?php if ($simple_common_view_agreement_checkbox) { ?><label><input type="checkbox" name="agree" id="agree" value="1" <?php if ($simple_common_view_agreement_checkbox_init == 1) { ?>checked="checked"<?php } ?> />&nbsp;<?php echo $text_agree; ?></label><?php } ?><a class="s_button_1 s_main_color_bgr" id="simplecheckout_button_order"><span class="s_text"><?php echo $button_order; ?></span></a><a class="s_button_1 s_main_color_bgr" onclick="javascript:history.back()"><span class="s_text"><?php echo $button_back; ?></span></a>
            <?php } else { ?>
                <?php if ($simple_common_view_agreement_checkbox) { ?><label><input type="checkbox" name="agree" id="agree" value="1" <?php if ($simple_common_view_agreement_checkbox_init == 1) { ?>checked="checked"<?php } ?> /><?php echo $text_agree; ?></label>&nbsp;<?php } ?><a class="simplecheckout-button" onclick="javascript:history.back()"><span><?php echo $button_back; ?></span></a>&nbsp;<a class="simplecheckout-button" id="simplecheckout_button_order"><span><?php echo $button_order; ?></span></a>
            <?php } ?>
        </div>
    </div>
    <!-- payment form block -->
    <div id="simplecheckout_payment_form" style="height:1px;overflow:hidden;" class="payment"></div>
    <!-- content bottom -->
    <div class="simplecheckout-proceed-payment" id="simplecheckout_proceed_payment" style="display:none;"><?php echo $text_proceed_payment ?></div>
    <?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>