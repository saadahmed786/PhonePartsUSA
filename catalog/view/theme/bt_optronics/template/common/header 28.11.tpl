<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
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
<?php echo $google_analytics; ?>
</head>
<?php 
	$array = (explode('/',$_SERVER['REQUEST_URI']));
	$end = end($array);
	if($end == "index.php" || $end == "home" || $end == ""){
		$home_page='home_page';
	}else{
		$home_page='sub_page';
	}
?>
<body <?php echo 'class='.$home_page; ?>>
<div class="frame_container">
<div id="container" class="container">
<div id="header" class="sixteen columns">
	<div id="mb-links"></div>
	<div id="mb-logo"></div>
	<div id="mb-search">
		<input type="text" name="filter_name" placeholder="<?php echo $text_search; ?>" value="<?php echo $filter_name; ?>" />
		<div class="button-search" title="<?php echo $text_search; ?>"></div>
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
		$warranty = "http://phonepartsusa.com/warranty";
        $text_warranty = "Warranty";
		$RETURNPOLICY = "http://phonepartsusa.com/returns-or-exchanges";
        $text_RETURNPOLICY = "Return policy";
		$shippinginformation = "http://www.phonepartsusa.com/shipping-information";
        $text_shippinginformation= "Shipping information";
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

<!--Start of Zopim Live Chat Script-->
<script type="text/javascript">
window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
$.src='//v2.zopim.com/?2MSGWWLMuHrkICHKPgiDOdhfcN2sWsMl';z.t=+new Date;$.
type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
</script>
<!--End of Zopim Live Chat Script-->

<script type="text/javascript">
adroll_adv_id = "AFEUTS27RZF7TAP2NM5TLE";
adroll_pix_id = "RYQGPPWVJJEEZDMWH4FFSN";
(function () {
var oldonload = window.onload;
window.onload = function(){
   __adroll_loaded=true;
   var scr = document.createElement("script");
   var host = (("https:" == document.location.protocol) ? "https://s.adroll.com" : "http://a.adroll.com");
   scr.setAttribute('async', 'true');
   scr.type = "text/javascript";
   scr.src = host + "/j/roundtrip.js";
   ((document.getElementsByTagName('head') || [null])[0] ||
    document.getElementsByTagName('script')[0].parentNode).appendChild(scr);
   if(oldonload){oldonload()}};
}());
</script>



<div id="notification"></div>
<div class="sixteen columns">
