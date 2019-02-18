<?php define('WX_PREVENT_CACHING', true); ?><?php if (isset($_SERVER['HTTP_USER_AGENT']) && !strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6') && strpos($_SERVER['HTTP_USER_AGENT'], 'Opera')) echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n" ."<!-- This is here for the Old Opera mobile  -->"; ?> 
<!DOCTYPE html>
<!--[if IEMobile 7 ]>    <html class="no-js iem7"> <![endif]-->
<!--[if (gt IEMobile 7)|!(IEMobile)]><!--> <html> <!--<![endif]--> 
	<head>
		<meta charset="utf-8">
		
		<title><?php echo $title; ?></title>		
		<?php if ($description) { ?><meta name="description" content="<?php echo $description; ?>"><?php } ?>
		<meta name="author" content="">
		
		<base href="<?php echo $base; ?>" >
		
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<meta name="apple-touch-fullscreen" content="YES">		
		
		<meta http-equiv="cleartype" content="on">
		
		<?php if ($keywords) { ?>
		<meta name="keywords" content="<?php echo $keywords; ?>" >
		<?php } ?>
		<?php if ($icon) { ?>
		<link href="<?php echo $icon; ?>" rel="icon" >
		<?php } ?>
		<?php foreach ($links as $link) { ?>
		<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" >
		<?php } ?>		
		<script>
		window.location.hash = '#container';
		(function(w,d,u){w.readyQ=[];w.bindReadyQ=[];function p(x,y){if(x=="ready"){w.bindReadyQ.push(y);}else{w.readyQ.push(x);}};var a={ready:p,bind:p};w.$=function(f){if(f===d||f===u){return a}else{p(f)}}})(window,document)</script> 		
		<?php if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/stylesheet/mobile.css')) { ?>
		<link rel="stylesheet" type="text/css" href="<?php echo 'catalog/view/theme/' . $this->config->get('config_template') ?>/stylesheet/mobile.css" >
		<?php } else {?>
		<link rel="stylesheet" type="text/css" href="catalog/view/theme/omf/stylesheet/mobile.css" >
		<?php } ?>		
	</head>
	<body>
		<div id="container">
			<header id="header">
				<?php if ($logo) { ?>
				<a href="<?php echo $home; ?>" id="logo"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>"></a>
				<?php } ?>					
				<ul>
					<li><a href="<?php echo $shopping_cart; ?>" tabindex="2" id="cart" ><?php echo $text_shopping_cart; ?> (<?php echo $text_items_count; ?>) </a></li>
					<li><a href="#search" tabindex="3" id="search_link"><?php echo $text_search; ?></a></li>					
				</ul>				
			</header>