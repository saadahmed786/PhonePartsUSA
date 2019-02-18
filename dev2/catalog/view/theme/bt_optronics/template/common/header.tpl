<!DOCTYPE html>

<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">

<head>

	<meta charset="UTF-8" />

	<title><?php echo $title; ?></title>

	<base href="<?php echo $base; ?>" />

	<meta name="p:domain_verify" content="1e738f3103b9bf7b721347f7bef506af"/>

	<?php if ($description) { ?>

	<meta name="description" content="<?php echo $description; ?>" />

	<?php } ?> 

	<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">

	<?php if ($keywords) { ?>

	<meta name="keywords" content="<?php echo $keywords; ?>" />

	<?php } ?>

	<?php if ($icon) { ?>

	<link href="<?php echo $icon; ?>" rel="icon" />

	<?php } ?>

	<?php foreach ($links as $link) { ?>

	<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />

	<?php } ?>

	<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/stylesheet.css" />

	<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/boss_add_cart.css" />

	<?php foreach ($styles as $style) { ?>

	<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />

	<?php } ?>

	<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/skeleton.css" />

	<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/responsive_menu.css" />

	<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/responsive.css" />



	<script type="text/javascript" src="catalog/view/javascript/jquery/jquery-1.7.1.min.js"></script>

	<script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>

	<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />

	<script type="text/javascript" src="catalog/view/javascript/jquery/ui/external/jquery.cookie.js"></script>

	<script type="text/javascript" src="catalog/view/javascript/jquery/colorbox/jquery.colorbox.js"></script>

	<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/colorbox/colorbox.css" media="screen" />

	<script type="text/javascript" src="catalog/view/javascript/jquery/tabs.js"></script>

	<script type="text/javascript" src="catalog/view/javascript/common.js"></script>

	<script type="text/javascript" src="catalog/view/javascript/bossthemes/getwidthbrowser.js"></script>

	<script type="text/javascript" src="catalog/view/javascript/bossthemes/bossthemes.js"></script>

	<script type="text/javascript" src="catalog/view/javascript/bossthemes/notify.js"></script>

	<script src="catalog/view/javascript/modernizr.custom.js"></script>





	<?php foreach ($scripts as $script) { ?>

	<script type="text/javascript" src="<?php echo $script; ?>"></script>

	<?php } ?>

<!--[if IE 8]>

<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/ie8.css" />

<![endif]-->



<!--[if IE 9]>

<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/ie9.css" />

<![endif]-->



<!--[if IE 7]>

<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/ie7.css" />

<![endif]-->



<!--[if lt IE 7]>

<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/ie6.css" />

<script type="text/javascript" src="catalog/view/javascript/DD_belatedPNG_0.0.8a-min.js"></script>

<script type="text/javascript">

DD_belatedPNG.fix('#logo img');

</script>

<![endif]-->

<?php

	// disable analytics code at checkout pages as we are using Analytics Pro for this purpose

	$route = $_GET['route'];

	$route_params = explode("/",$route);

	if($route_params[0]!='checkout')

	{



?>

<?php echo $google_analytics; ?>

<?php

}

?>



</head>

<?php 

//	$_SESSION['device']='mobile';

$array = (explode('/',$_SERVER['REQUEST_URI']));

$end = end($array);

if($end == "index.php" || $end == "home" || $end == ""){

	$home_page='home_page';

}else{

	$home_page='sub_page';

}



if(isset($this->session->data['is_home_page']) && $this->session->data['is_home_page']=='1' )

{

	$home_page = 'home_page';

}

else

{

	$home_page ='sub_page';

}

unset($this->session->data['is_home_page']);

?>

<!-- BEGIN: _GUARANTEE Seal -->  <script type="text/javascript" src="//nsg.symantec.com/private/rollover/rollover.js"></script> <script type="text/javascript"> /*if(window._GUARANTEE && _GUARANTEE.Loaded) {   _GUARANTEE.Hash = "RtiNXhVmmGldopYvlLgBjtsWG8OVMB7R9NRdc6LinBeSCl78Cn9SWBs9ez23aV7aqFJajjT7kThEHjBPYP4UMA%3D%3D";   _GUARANTEE.WriteSeal("_GUARANTEE_SealSpan", "GuaranteedSeal");  }*/ </script> <!-- END: _GUARANTEE Seal -->

<body <?php echo 'class='.$home_page; ?>>

	<div class="frame_container">

		<div id="container" class="container">

			<!--<span id="_GUARANTEE_SealSpan"></span>-->

			<div id="header" class="sixteen columns">

				<div id="mb-links"></div>

				<div id="dl-menu" class="dl-menuwrapper" style="z-index:9999999;">

					<button class="dl-trigger">Open Menu</button>

					<ul class="dl-menu">

						<?php

						foreach($categories as $menu_cat)

						{

							?>

							<li>

								<a href="<?php echo $menu_cat['href'];?>"><?php echo $menu_cat['name'];?></a>

								<?php

								if(count($menu_cat['children'])>0)

								{

									?>

									<ul class="dl-submenu">

										<?php foreach($menu_cat['children'] as $submenu)

										{

											?>

											<li>

												<a href="<?php echo $submenu['href'];?>"><?php echo $submenu['name'];?></a>



											</li>

											<?php

										}

										?>





									</ul>

									<?php

								}

								?>

							</li>

							<?php

						}

						?>	





					</ul>

				</div>

				<div id="mb-logo"></div>



				<div id="res-cart">

					<a class="mailbox" href="<?php echo $shopping_cart;?>">

						<div class="mailcircle">

							<span class="mailnumber"><?php echo $total_cart_items;?></span>

						</div>

						<img src="image/shopcart.png" title="Click to view your cart" style="-moz-transform: scaleX(-1);

						-o-transform: scaleX(-1);

						-webkit-transform: scaleX(-1);

						transform: scaleX(-1);top:80px;left:93%;position:absolute;">

					</a>



				</div>

				<div id="mb-search">

					<input type="text" name="filter_name" placeholder="<?php echo $text_search; ?>" value="<?php echo $filter_name; ?>" />

					<div class="button-search" title="<?php echo $text_search; ?>"></div>

				</div>

				<div id="added_to_cart" style="display:none">

				</div>

				<div id="mb-login"></div>

				<div id="mb-cart"></div>

				<div class="header-top">

					<div id="pc-login">

						<?php echo $boss_login; ?> 

						<?php echo $language; ?>

						<?php echo $currency; ?>

					</div>

					<?php

					$warranty = HTTPS_SERVER."index.php?route=catalog/catalog";

					$text_warranty = "Check Out Our NEW Interactive Product Catalog";


					?>

					<div id="pc-links"><div class="links"> <a class="custom-links-vif" href="<?php echo $warranty; ?>" id="warranty"><?php echo $text_warranty; ?></a>

						<a class="custom-links-vif" href="<?php echo $RETURNPOLICY; ?>" id="RETURNPOLICY"><?php echo $text_RETURNPOLICY; ?></a>

						<a class="custom-links-vif" href="<?php echo $shippinginformation; ?>" id="shippinginformation"><?php echo $text_shippinginformation; ?></a><a href="<?php echo $wishlist; ?>" id="wishlist-total"><?php echo $text_wishlist; ?></a><a class="no-need" href="<?php echo $account; ?>"><?php echo $text_account; ?></a><a class="no-need" href="<?php echo $checkout; ?>"><?php echo $text_checkout; ?></a></div></div>

					</div>

					<div class="header-middle">		





						<?php if ($logo) { ?>

						<div id="logo"><a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" /></a></div>

						<?php } ?>

						<?php echo $header_top;?>

					</div>



					<div class="header-bottom">



						<?php echo $boss_megamenu; ?>



						<div id="pc-search"><?php echo $boss_search; ?></div>



						<div id="pc-cart"><?php echo $cart; ?></div>



						<?php echo $header_bottom; ?>

					</div>



				</div>



				<script type="text/javascript">

					$(document).ready(function() {

						boss_header_move_mobile();

					});

					$(window).resize(function() {

						boss_header_move_mobile();

					});



					function boss_header_move_mobile()	{



						if(getWidthBrowser() < 800){



							$('body').addClass('mobile');        

							$('#pc-search').css("display", "none");

							$('#mb-search').css("display", "block");        

							if ($("#pc-links").html()) {

								$("#mb-links").html($("#pc-links").html());

								$("#pc-links").html("");

								$("#pc-links").css("display", "none");

							}

							if ($("#logo").html()) {

								$("#mb-logo").html($("#logo").html());

								$("#logo").html("");

								$("#pc-links").css("display", "none");

							}

							if ($("#pc-login").html()) {

								$("#mb-login").html($("#pc-login").html());

								$("#pc-login").html("");

								$("#pc-login").css("display", "none");

							}

							if ($("#pc-cart").html()) {

								$("#mb-cart").html($("#pc-cart").html());

								$("#pc-cart").html("");

								$("#pc-cart").css("display", "none");

								$('#cart > .heading a').live('click', function() {

									if($('#cart').hasClass('my-active')){

										$('#cart').removeClass('active');

										$('#cart').removeClass('my-active');

									} else {

										$('#cart').addClass('my-active');

									}

									$('#cart').addClass('active');



									$('#cart').load('index.php?route=module/cart #cart > *');



									$('#cart').live('mouseleave', function() {

										$(this).removeClass('active');

									});

								});

							}

						}else {

							$('body').removeClass('mobile');    

							$('#mb-search').css("display", "none");

							$('#pc-search').css("display", "block");

							if ($("#mb-links").html()) {

								$("#pc-links").html($("#mb-links").html());

								$("#mb-links").html("");

								$("#pc-links").css("display", "block");

							}

							if ($("#mb-logo").html()) {

								$("#logo").html($("#mb-logo").html());

								$("#mb-logo").html("");

								$("#logo").css("display", "block");

							}

							if ($("#mb-login").html()) {

								$("#pc-login").html($("#mb-login").html());

								$("#mb-login").html("");

								$("#pc-login").css("display", "block");

							}

							if ($("#mb-cart").html()) {

								$("#pc-cart").html($("#mb-cart").html());

								$("#mb-cart").html("");

								$("#pc-cart").css("display", "block");

							}

						}

					}

				</script>



				<script type="text/javascript">

					(function() {var s = document.createElement('script');s.type = 'text/javascript';s.async = true;

						s.src = document.location.protocol + '//wisepops.com/default/index/get-loader?user_id=5244';

						var s2 = document.getElementsByTagName('script')[0];s2.parentNode.insertBefore(s, s2);})();

					</script>



					<script type='text/javascript'>var fc_CSS=document.createElement('link');fc_CSS.setAttribute('rel','stylesheet');var isSecured = (window.location && window.location.protocol == 'https:');var lang = document.getElementsByTagName('html')[0].getAttribute('lang'); var rtlLanguages = ['ar','he']; var rtlSuffix = (rtlLanguages.indexOf(lang) >= 0) ? '-rtl' : '';fc_CSS.setAttribute('type','text/css');fc_CSS.setAttribute('href',((isSecured)? 'https://d36mpcpuzc4ztk.cloudfront.net':'http://assets1.chat.freshdesk.com')+'/css/visitor'+rtlSuffix+'.css');document.getElementsByTagName('head')[0].appendChild(fc_CSS);var fc_JS=document.createElement('script'); fc_JS.type='text/javascript'; fc_JS.defer=true;fc_JS.src=((isSecured)?'https://d36mpcpuzc4ztk.cloudfront.net':'http://assets.chat.freshdesk.com')+'/js/visitor.js';(document.body?document.body:document.getElementsByTagName('head')[0]).appendChild(fc_JS);window.freshchat_setting= 'eyJ3aWRnZXRfc2l0ZV91cmwiOiJwaG9uZXBhcnRzdXNhLmZyZXNoZGVzay5jb20iLCJwcm9kdWN0X2lkIjpudWxsLCJuYW1lIjoiUGhvbmVQYXJ0c1VTQS5jb20iLCJ3aWRnZXRfZXh0ZXJuYWxfaWQiOm51bGwsIndpZGdldF9pZCI6IjkwOTM2ZmE0LWQ1ODUtNGQ0NS1iNTZiLWRlOGJhMWE0MWJlZiIsInNob3dfb25fcG9ydGFsIjpmYWxzZSwicG9ydGFsX2xvZ2luX3JlcXVpcmVkIjpmYWxzZSwiaWQiOjkwMDAwMjMzNjYsIm1haW5fd2lkZ2V0Ijp0cnVlLCJmY19pZCI6ImM2ZjczMjI5NTljNDlkNGI5OTU1N2NiMjk1ZjBmZGZlIiwic2hvdyI6MSwicmVxdWlyZWQiOjIsImhlbHBkZXNrbmFtZSI6IlBob25lUGFydHNVU0EuY29tIiwibmFtZV9sYWJlbCI6Ik5hbWUiLCJtYWlsX2xhYmVsIjoiRW1haWwiLCJtZXNzYWdlX2xhYmVsIjoiTWVzc2FnZSIsInBob25lX2xhYmVsIjoiUGhvbmUgTnVtYmVyIiwidGV4dGZpZWxkX2xhYmVsIjoiVGV4dGZpZWxkIiwiZHJvcGRvd25fbGFiZWwiOiJEcm9wZG93biIsIndlYnVybCI6InBob25lcGFydHN1c2EuZnJlc2hkZXNrLmNvbSIsIm5vZGV1cmwiOiJjaGF0LmZyZXNoZGVzay5jb20iLCJkZWJ1ZyI6MSwibWUiOiJNZSIsImV4cGlyeSI6MTQ1NzY0MzkwNTAwMCwiZW52aXJvbm1lbnQiOiJwcm9kdWN0aW9uIiwiZGVmYXVsdF93aW5kb3dfb2Zmc2V0IjozMCwiZGVmYXVsdF9tYXhpbWl6ZWRfdGl0bGUiOiJDaGF0IGluIHByb2dyZXNzIiwiZGVmYXVsdF9taW5pbWl6ZWRfdGl0bGUiOiJMZXQncyB0YWxrISIsImRlZmF1bHRfdGV4dF9wbGFjZSI6IllvdXIgTWVzc2FnZSIsImRlZmF1bHRfY29ubmVjdGluZ19tc2ciOiJXYWl0aW5nIGZvciBhbiBhZ2VudCIsImRlZmF1bHRfd2VsY29tZV9tZXNzYWdlIjoiSGkhIEhvdyBjYW4gd2UgaGVscCB5b3UgdG9kYXk/IiwiZGVmYXVsdF93YWl0X21lc3NhZ2UiOiJPbmUgb2YgdXMgd2lsbCBiZSB3aXRoIHlvdSByaWdodCBhd2F5LCBwbGVhc2Ugd2FpdC4iLCJkZWZhdWx0X2FnZW50X2pvaW5lZF9tc2ciOiJ7e2FnZW50X25hbWV9fSBoYXMgam9pbmVkIHRoZSBjaGF0IiwiZGVmYXVsdF9hZ2VudF9sZWZ0X21zZyI6Int7YWdlbnRfbmFtZX19IGhhcyBsZWZ0IHRoZSBjaGF0IiwiZGVmYXVsdF9hZ2VudF90cmFuc2Zlcl9tc2dfdG9fdmlzaXRvciI6IllvdXIgY2hhdCBoYXMgYmVlbiB0cmFuc2ZlcnJlZCB0byB7e2FnZW50X25hbWV9fSIsImRlZmF1bHRfdGhhbmtfbWVzc2FnZSI6IlRoYW5rIHlvdSBmb3IgY2hhdHRpbmcgd2l0aCB1cy4gSWYgeW91IGhhdmUgYWRkaXRpb25hbCBxdWVzdGlvbnMsIGZlZWwgZnJlZSB0byBwaW5nIHVzISIsImRlZmF1bHRfbm9uX2F2YWlsYWJpbGl0eV9tZXNzYWdlIjoiT3VyIGFnZW50cyBhcmUgdW5hdmFpbGFibGUgcmlnaHQgbm93LiBTb3JyeSBhYm91dCB0aGF0LCBidXQgcGxlYXNlIGxlYXZlIHVzIGEgbWVzc2FnZSBhbmQgd2UnbGwgZ2V0IHJpZ2h0IGJhY2suIiwiZGVmYXVsdF9wcmVjaGF0X21lc3NhZ2UiOiJXZSBjYW4ndCB3YWl0IHRvIHRhbGsgdG8geW91LiBCdXQgZmlyc3QsIHBsZWFzZSB0ZWxsIHVzIGEgYml0IGFib3V0IHlvdXJzZWxmLiIsImFnZW50X3RyYW5zZmVyZWRfbXNnIjoiWW91ciBjaGF0IGhhcyBiZWVuIHRyYW5zZmVycmVkIHRvIHt7YWdlbnRfbmFtZX19IiwiYWdlbnRfcmVvcGVuX2NoYXRfbXNnIjoie3thZ2VudF9uYW1lfX0gcmVvcGVuZWQgdGhlIGNoYXQiLCJ2aXNpdG9yX3NpZGVfaW5hY3RpdmVfbXNnIjoiVGhpcyBjaGF0IGhhcyBiZWVuIGluYWN0aXZlIGZvciB0aGUgcGFzdCAyMCBtaW51dGVzLiIsImFnZW50X2Rpc2Nvbm5lY3RfbXNnIjoie3thZ2VudF9uYW1lfX0gaGFzIGJlZW4gZGlzY29ubmVjdGVkIiwic2l0ZV9pZCI6ImM2ZjczMjI5NTljNDlkNGI5OTU1N2NiMjk1ZjBmZGZlIiwiYWN0aXZlIjp0cnVlLCJ3aWRnZXRfcHJlZmVyZW5jZXMiOnsid2luZG93X2NvbG9yIjoiIzc3Nzc3NyIsIndpbmRvd19wb3NpdGlvbiI6IkJvdHRvbSBSaWdodCIsIndpbmRvd19vZmZzZXQiOiIzMCIsIm1pbmltaXplZF90aXRsZSI6IkxldCdzIHRhbGshIiwibWF4aW1pemVkX3RpdGxlIjoiQ2hhdCBpbiBwcm9ncmVzcyIsInRleHRfcGxhY2UiOiJZb3VyIE1lc3NhZ2UiLCJ3ZWxjb21lX21lc3NhZ2UiOiJIaSEgSG93IGNhbiB3ZSBoZWxwIHlvdSB0b2RheT8iLCJ0aGFua19tZXNzYWdlIjoiVGhhbmsgeW91IGZvciBjaGF0dGluZyB3aXRoIHVzLiBJZiB5b3UgaGF2ZSBhZGRpdGlvbmFsIHF1ZXN0aW9ucywgZmVlbCBmcmVlIHRvIHBpbmcgdXMhIiwid2FpdF9tZXNzYWdlIjoiT25lIG9mIHVzIHdpbGwgYmUgd2l0aCB5b3UgcmlnaHQgYXdheSwgcGxlYXNlIHdhaXQuIiwiYWdlbnRfam9pbmVkX21zZyI6Int7YWdlbnRfbmFtZX19IGhhcyBqb2luZWQgdGhlIGNoYXQiLCJhZ2VudF9sZWZ0X21zZyI6Int7YWdlbnRfbmFtZX19IGhhcyBsZWZ0IHRoZSBjaGF0IiwiYWdlbnRfdHJhbnNmZXJfbXNnX3RvX3Zpc2l0b3IiOiJZb3VyIGNoYXQgaGFzIGJlZW4gdHJhbnNmZXJyZWQgdG8ge3thZ2VudF9uYW1lfX0iLCJjb25uZWN0aW5nX21zZyI6IldhaXRpbmcgZm9yIGFuIGFnZW50In0sInJvdXRpbmciOm51bGwsInByZWNoYXRfZm9ybSI6dHJ1ZSwicHJlY2hhdF9tZXNzYWdlIjoiV2UgY2FuJ3Qgd2FpdCB0byB0YWxrIHRvIHlvdS4gQnV0IGZpcnN0LCBwbGVhc2UgdGVsbCB1cyBhIGJpdCBhYm91dCB5b3Vyc2VsZi4iLCJwcmVjaGF0X2ZpZWxkcyI6eyJuYW1lIjp7InRpdGxlIjoiTmFtZSIsInNob3ciOiIyIn0sImVtYWlsIjp7InRpdGxlIjoiRW1haWwiLCJzaG93IjoiMiJ9LCJwaG9uZSI6eyJ0aXRsZSI6IlBob25lIE51bWJlciIsInNob3ciOiIwIn0sInRleHRmaWVsZCI6eyJ0aXRsZSI6IlRleHRmaWVsZCIsInNob3ciOiIwIn0sImRyb3Bkb3duIjp7InRpdGxlIjoiRHJvcGRvd24iLCJzaG93IjoiMCIsIm9wdGlvbnMiOlsibGlzdDEiLCJsaXN0MiIsImxpc3QzIl19fSwiYnVzaW5lc3NfY2FsZW5kYXIiOm51bGwsIm5vbl9hdmFpbGFiaWxpdHlfbWVzc2FnZSI6eyJ0ZXh0IjoiT3VyIGFnZW50cyBhcmUgdW5hdmFpbGFibGUgcmlnaHQgbm93LiBTb3JyeSBhYm91dCB0aGF0LCBidXQgcGxlYXNlIGxlYXZlIHVzIGEgbWVzc2FnZSBhbmQgd2UnbGwgZ2V0IHJpZ2h0IGJhY2suIiwidGlja2V0X2xpbmtfb3B0aW9uIjoiMCIsImN1c3RvbV9saW5rX3VybCI6IiJ9LCJwcm9hY3RpdmVfY2hhdCI6ZmFsc2UsInByb2FjdGl2ZV90aW1lIjoxNSwic2l0ZV91cmwiOiJwaG9uZXBhcnRzdXNhLmZyZXNoZGVzay5jb20iLCJleHRlcm5hbF9pZCI6bnVsbCwiZGVsZXRlZCI6ZmFsc2UsIm9mZmxpbmVfY2hhdCI6eyJzaG93IjoiMSIsImZvcm0iOnsibmFtZSI6Ik5hbWUiLCJlbWFpbCI6IkVtYWlsIiwibWVzc2FnZSI6Ik1lc3NhZ2UifSwibWVzc2FnZXMiOnsidGl0bGUiOiJMZWF2ZSB1cyBhIG1lc3NhZ2UhIiwidGhhbmsiOiJUaGFuayB5b3UgZm9yIHdyaXRpbmcgdG8gdXMuIFdlIHdpbGwgZ2V0IGJhY2sgdG8geW91IHNob3J0bHkuIiwidGhhbmtfaGVhZGVyIjoiVGhhbmsgeW91ISJ9fSwibW9iaWxlIjp0cnVlLCJjcmVhdGVkX2F0IjoiMjAxNi0wMi0yM1QwMToxOTowOC4wMDBaIiwidXBkYXRlZF9hdCI6IjIwMTYtMDItMjNUMDE6MTk6MTEuMDAwWiJ9';</script>
							<script src="catalog/view/javascript/jquery.dlmenu.js"></script>

							<script>

								$(function() {

									$( '#dl-menu' ).dlmenu({

										animationClasses : { classin : 'dl-animate-in-5', classout : 'dl-animate-out-5' }

									});

								});

							</script>





							<div id="notification"></div>



							<div class="sixteen columns">

							<?php

							if($home_page=='sub_page')

					

							?>