<?php if (isset($_SERVER['HTTP_USER_AGENT']) && !strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6') && strpos($_SERVER['HTTP_USER_AGENT'], 'Opera')) echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n" ."<!-- This is here for the Old Opera mobile  -->"; ?>
<!DOCTYPE html>
<!-- OMFramework 2.3.0 Basic www.omframework.com -->
<!--[if IEMobile 7 ]>    <html class="no-js iem7"> <![endif]-->
<!--[if (gt IEMobile 7)|!(IEMobile)]><!--> <html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>"> <!--<![endif]-->
  <head>
    <meta charset="UTF-8" />
    <title><?php echo $title; ?></title>
    <base href="<?php echo $base; ?>" />

    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no' name='viewport' />

    <?php if ($description) { ?>
    <meta name="description" content="<?php echo $description; ?>" />
    <?php } ?>
    <?php if ($keywords) { ?>
    <meta name="keywords" content="<?php echo $keywords; ?>" />
    <?php } ?>
    <?php if ($icon) { ?>
    <link href="<?php echo $icon; ?>" rel="icon" />
    <?php } ?>
    <?php foreach ($links as $link) { ?>
    <link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
    <?php } ?>
    <?php if (file_exists(DIR_TEMPLATE . $this->config->get('config_mobile_theme') . '/stylesheet/mobile2.scss')) { ?>
    <link rel="stylesheet" type="text/css" href="<?php echo 'catalog/view/theme/' . $this->config->get('config_mobile_theme') ?>/s.php?p=mobile2.scss" >
    <?php } else { ?>
    <link rel="stylesheet" type="text/css" href="catalog/view/theme/omf2/s.php?p=mobile2.scss" >
    <?php } ?>
    <?php foreach ($styles as $style) { ?>
    <link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
    <?php } ?>
    <script>
    document.cookie='resolution='+Math.max(screen.width,screen.height)+'; path=/';
    setTimeout(scrollTo, 0, 0, 1);</script>
    <?php if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != '') { ?>
    <script type="text/javascript" src="catalog/view/javascript/jquery/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
    <?php } else { ?>
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
    <?php } ?>
    <link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />
<?php if (defined('VERSION') && (version_compare(VERSION, '1.5.5', '<') == true)) { ?>
    <script type="text/javascript" src="catalog/view/javascript/jquery/ui/external/jquery.cookie.js"></script>
    <script type="text/javascript" src="catalog/view/javascript/jquery/colorbox/jquery.colorbox.js"></script>
    <link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/colorbox/colorbox.css" media="screen" />
    <script type="text/javascript" src="catalog/view/javascript/jquery/tabs.js"></script>
<?php } ?>
    <script type="text/javascript" src="catalog/view/javascript/common.js"></script>
    <?php foreach ($scripts as $script) { ?>
    <script type="text/javascript" src="<?php echo $script; ?>"></script>
    <?php } ?>
    <?php echo $google_analytics; ?>
  </head>
  <body>
    <div id="container">
      <div id="header">
        <?php if ($this->config->get('config_mobile_logo') && file_exists(DIR_IMAGE . $this->config->get('config_mobile_logo'))) {
            $mobile_logo = 'image/' . $this->config->get('config_mobile_logo');
        } else {
            $mobile_logo = $logo;
        } ?>
        <div id="logo"><a href="<?php echo $home; ?>"><img src="<?php echo $mobile_logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" /></a></div>
        <ul>   
        <?php if (defined('VERSION') && (version_compare(VERSION, '1.5.2', '<') == true)) { ?>
                  <li><a href="<?php echo $cart; ?>" tabindex="2" id="cart" ><?php echo $text_cart; ?> (<?php echo $text_items_count; ?>)&#x200E; </a></li>

        <?php } else { ?>
                  <li><a href="<?php echo $shopping_cart; ?>" tabindex="2" id="cart" ><?php echo $text_shopping_cart; ?> (<?php echo $text_items_count; ?>)&#x200E; </a></li>
        <?php } ?>
        <?php if ( is_null($this->config->get('config_wishlist_disabled')) or (bool)$this->config->get('config_wishlist_disabled') == false) { ?>
            <li><a href="<?php echo $wishlist; ?>" id="wishlist-total"><?php echo $text_wishlist; ?></a></li>
        <?php } ?>
            <li><a href="#search" tabindex="3" id="search_link"><?php echo $text_search_link; ?></a></li>
        </ul>
      </div>
      <div id="main">
        <div id="notification"></div>
