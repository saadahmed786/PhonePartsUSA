<?php if (isset($_SERVER['HTTP_USER_AGENT']) && !strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6')) echo '<?xml version="1.0" encoding="UTF-16"?>'. "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1" />
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
<?php if(isset($icon_mobile)){ ?>
<link rel="apple-touch-icon-precomposed" href="<?php echo $icon_mobile; ?>"/>
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<link rel="stylesheet" href="catalog/view/theme_mobile/<?php echo $this->config->get('config_template_mobile'); ?>/jquery/jquery-mobile/jquery.mobile-1.2.0.min.css" />
<script src="catalog/view/theme_mobile/<?php echo $this->config->get('config_template_mobile'); ?>/jquery/jquery-mobile/jquery.js" type="text/javascript"></script>
<script src="catalog/view/theme_mobile/<?php echo $this->config->get('config_template_mobile'); ?>/jquery/jquery-mobile/jquery.mobile-1.2.0.min.js" type="text/javascript"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/external/jquery.cookie.js"></script>
<script type="text/javascript" src="catalog/view/theme_mobile/<?php echo $this->config->get('config_template_mobile'); ?>/jquery/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="catalog/view/theme_mobile/<?php echo $this->config->get('config_template_mobile'); ?>/jquery/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript" src="catalog/view/javascript/jquery/colorbox/jquery.colorbox.js"></script>
<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/colorbox/colorbox.css" media="screen" />
<!--[if IE]>
<script type="text/javascript" src="catalog/view/javascript/jquery/fancybox/jquery.fancybox-1.3.4-iefix.js"></script>
<![endif]--> 
<link rel="stylesheet" href="catalog/view/theme_mobile/<?php echo $this->config->get('config_template_mobile'); ?>/jquery/flexslider/flexslider.css" type="text/css" media="screen" />
<script src="catalog/view/theme_mobile/<?php echo $this->config->get('config_template_mobile'); ?>/jquery/flexslider/jquery.flexslider-min.js"></script>
<script type="text/javascript" src="catalog/view/theme_mobile/<?php echo $this->config->get('config_template_mobile'); ?>/jquery/common.js"></script>
<link rel="stylesheet" href="catalog/view/theme_mobile/<?php echo $this->config->get('config_template_mobile'); ?>/stylesheet/stylesheet.css" type="text/css" media="screen"> 

<link rel="stylesheet" type="text/css" href="catalog/view/theme_mobile/<?php echo $this->config->get('config_template_mobile'); ?>/jquery/jquery-mobile/jqm-datebox.min.css" /> 
<script type="text/javascript" src="catalog/view/theme_mobile/<?php echo $this->config->get('config_template_mobile'); ?>/jquery/jquery-mobile/jqm-datebox.core.min.js"></script>
<script type="text/javascript" src="catalog/view/theme_mobile/<?php echo $this->config->get('config_template_mobile'); ?>/jquery/jquery-mobile/jqm-datebox.mode.calbox.min.js"></script>


<script type="text/javascript"><!--
$(document).ready(function() {
   	 $("a").attr("rel","external");
	 $("#text_i_agree").html('<?php echo $text_i_agree; ?>');
	 $("#search_form").hide();
	 $("#more_info").hide();
	 $("#more_customer_service").hide();
	 $("#more_extras").hide();
	 $("#more_account").hide();
	 $("#more_categories").hide(); 
	 $('#click_search').click(function() {
          $('#search_form').animate({
               height: 'toggle'
               }, 200
          );
     });
	 
	 $('#click_categories').click(function() {
          $('#more_categories').animate({
               height: 'toggle'
               }, 200
          );
     });
	 
	 $('#click_info').click(function() {
          $('#more_info').animate({
               height: 'toggle'
               }, 200
          );
     });
	 
	 $('#click_customer_service').click(function() {
          $('#more_customer_service').animate({
               height: 'toggle'
               }, 200
          );
     });
	 
	 $('#click_extras').click(function() {
          $('#more_extras').animate({
               height: 'toggle'
               }, 200
          );
     });
	 
	 $('#click_account').click(function() {
          $('#more_account').animate({
               height: 'toggle'
               }, 200
          );
     });
	 
	 	 // hide #back-top first
	$("#back-top").hide();
	
	// fade in #back-top
	$(function () {
		$(window).scroll(function () {
			if ($(this).scrollTop() > 100) {
				$('#back-top').fadeIn();
			} else {
				$('#back-top').fadeOut();
			}
		});

		// scroll body to 0px on click
		$('#back-top a').click(function () {
			$('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	});
	 
});
//--></script> 
</head>
<body id="top" class="omtex-mobile">
<?php echo $google_analytics; ?>
<div data-role="page" id="page">
	<div data-role="header" data-theme="c" data-backbtn="false">
		<div style="height:35px;background-color:#FFF;">
        <div class="header_logo" style="float:left; margin-top:5px; margin-left:5px;">
        <a href="<?php echo $home; ?>" rel="external">
       <img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" height="25"/>
        </a>
        </div>
        <div style="float:right; margin-top:5px;" >
        <!--<a href="<?php echo $home; ?>" rel="external"><img src="catalog/view/theme_mobile/<?php echo $this->config->get('config_template_mobile'); ?>/image/home-icon.png" style="margin-right:5px;" height="25"/></a>
        <a href="tel:<?php echo $telephone; ?>" rel="external"><img src="catalog/view/theme_mobile/<?php echo $this->config->get('config_template_mobile'); ?>/image/phone-icon.png" style="margin-right:5px;" height="25"/></a>
        <a href="<?php echo $shopping_cart; ?>" rel="external" style="text-decoration:none;"><img src="catalog/view/theme_mobile/<?php echo $this->config->get('config_template_mobile'); ?>/image/cart-icon.png" style="margin-right:5px;" height="25"/></a>-->
		<a href="<?php echo $shopping_cart; ?>" rel="external" style="text-decoration:none;"><img src="catalog/view/theme_mobile/<?php echo $this->config->get('config_template_mobile'); ?>/image/shopping-purse-128.png" style="margin-right:5px;" height="25" width="25"/></a><span class="count"><a href="<?php echo $this->url->link('checkout/cart'); ?>" style="text-decoration: none;">(<?php echo $this->cart->countProducts().' Items' ?>)</a></span>
        </div>
        </div>
        
        <div data-role="navbar" data-position="inline">
		    <ul>
				<li><a data-theme="a" id="click_categories"><?php //echo $text_nav_categories; ?>Category</a></li>
				<li><a href="<?php echo $account; ?>" rel="external" data-theme="a"><?php echo $text_account; ?></a></li>
				<li><a data-theme="a" id="click_search" ><?php echo $text_search; ?></a></li>
				<li><a href="index.php?route=information/contact" rel="external" data-theme="a"><?php //echo $text_nav_contact; ?>Contact</a></li>
		    </ul>
        </div>
       
       <div data-role="fieldcontain" class="ui-hide-label" id="search_form" data-theme="a">
	<form action="index.php" method="get" enctype="multipart/form-data" id="search_form" data-ajax="false">
		<input type="hidden" name="route" value="product/search"/>
		<input type="search" name="filter_name" id="searc-basic" value="" data-theme="c" placeholder="<?php echo $text_search; ?>"/>
	</form>
		</div> 
</div>

<div id="notification" data-rel="dialog"></div>

<?php if ($categories) { ?>
       <div data-role="content" id="more_categories">
				<?php foreach ($categories as $category) { ?>
                   <?php if ($category['children']) { ?>
                     <div data-role="collapsible" data-theme="b" data-content-theme="d" data-collapsed-icon="arrow-r" data-expanded-icon="arrow-d" data-inset="false">
                      <h2><?php echo $category['name']; ?></h2>
                     <ul data-role="listview">
                     <li><a href="<?php echo $category['href']; ?>" rel="external"><?php echo $category['name']; ?></a></li>
                     <?php for ($i = 0; $i < count($category['children']);) { ?>
                      <?php $j = $i + ceil(count($category['children']) / $category['column']); ?>
                      <?php for (; $i < $j; $i++) { ?>
                      <?php if (isset($category['children'][$i])) { ?>
                      <li><a href="<?php echo $category['children'][$i]['href']; ?>" rel="external"> - <?php echo $category['children'][$i]['name']; ?></a></li>
                      <?php } ?>
                      <?php } ?>
                   <?php } ?>
                   </ul>
                 	</div>
                 <?php } else { ?>
                 <ul data-role="listview" style="margin-top:5px; margin-bottom:5px;">
                 <li><a href="<?php echo $category['href']; ?>" rel="external"><?php echo $category['name']; ?></a></li>
                 </ul>
                 <?php } ?>
              <?php } ?>
			</div>
       <?php } ?>
	   
<script type="text/javascript">
(function() {var s = document.createElement('script');s.type = 'text/javascript';s.async = true;
s.src = document.location.protocol + '//wisepops.com/default/index/get-loader?user_id=5244';
var s2 = document.getElementsByTagName('script')[0];s2.parentNode.insertBefore(s, s2);})();
</script>


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
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-24721193-1', 'auto');
  ga('send', 'pageview');

</script>
