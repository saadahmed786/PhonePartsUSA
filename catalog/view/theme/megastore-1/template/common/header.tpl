<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<meta name="viewport" content="initial-scale=1.0, width=device-width"/>
<?php
$page = '';
	if(isset($this->request->get['route'])){
		$page = $this->request->get['route']; 
	}
?>
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
<link rel="stylesheet" href="catalog/view/theme/megastore/stylesheet/stylesheet.css" />
<?php foreach ($styles as $style) { ?>
<?php if($style['href'] != "catalog/view/theme/megastore/stylesheet/slideshow.css") :?>
<link rel="<?php echo $style['rel']; ?>" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php endif; ?>
<?php } ?>
<script src="catalog/view/javascript/jquery/jquery-1.7.1.min.js"></script>
<script src="catalog/view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
<link rel="stylesheet" href="catalog/view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />
<script src="catalog/view/javascript/jquery/ui/external/jquery.cookie.js"></script>
<script src="catalog/view/javascript/jquery/colorbox/jquery.colorbox.js"></script>
<script src="catalog/view/theme/megastore/js/cycle.js"></script>
<script src="catalog/view/theme/megastore/js/flexslider.js"></script>
<script src="catalog/view/theme/megastore/js/custom.js"></script>
<link rel="stylesheet" href="catalog/view/theme/megastore/stylesheet/flexslider.css" />
<link rel="stylesheet" href="catalog/view/javascript/jquery/colorbox/colorbox.css" />
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script src="catalog/view/javascript/common.js"></script>

<?php foreach ($scripts as $script) { ?>
<?php if($script != "catalog/view/javascript/jquery/nivo-slider/jquery.nivo.slider.pack.js" && $script != 'catalog/view/javascript/jquery/jquery.cycle.js') :?>
<script src="<?php echo $script; ?>"></script>
<?php endif; ?>
<?php } ?>
<?php echo $google_analytics; ?>
<?php $customStyles =  $this->config->get('megastore_options'); // Theme Options ?>
<?php if($customStyles): ?>
<style type="text/css">

	<?php if($customStyles['bgColor']): //Body Background ?>
			body{background:<?php echo '#' . $customStyles['bgColor']; ?>}
	<?php elseif(empty($customStyles['bgColor']) && $customStyles['bgImage']):?>
			body{background:url('<?php echo HTTP_IMAGE . $customStyles['bgImage']; ?>');}
	<?php endif; ?>
	<?php if($customStyles['primary']): //Primary Color default pink ?>
		#top .links li > ul li a:hover,#cart .content li.checkoutBtn a,.box-category > ul > li a.active,
		.drop > ul li a:hover,#top .links li > ul li a:hover,#cart .content li.checkoutBtn a,.box-category > ul > li:hover > a,.box-category > ul > li:hover > a.active,.box-category > ul > li a.active,a.button, input.button,	ul.cat-list li:hover,
		.view-cart,.box .box-heading,.box-heading,#search-cart a#button:hover,#imageNav a.activeSlide,.slideNav li.flex-active a,ul.product-tab li a.active,.menu2 .mainCat ul li a:hover,	#column-left .featured-categories  li:hover > a,#column-left .featured-categories li.cat-img a{background-color:#<?php echo $customStyles['primary']; ?>;}
		a.button, input.button {border:1px solid #<?php echo $customStyles['primary']; ?> !important;}	
		.box-product .price,.product-list .price, .featured-categories .parent-cat,.product-grid .price,.product-info .price-new,.product-info .discount ul li span.dPrice,.product-info .price{color:#<?php echo $customStyles['primary']; ?>;}
	<?php endif; ?>
	<?php if($customStyles['secondary']): //Background for search and categories menu ?>
		#search-cart,nav,#cart,#search-cart a#button,#menu > li:hover > a,#menu > li > div,#cart .content,.slideNav{background-color:#<?php echo $customStyles['secondary']; ?>;}
	<?php endif; ?>
	<?php if($customStyles['slideBottom']): ?> 
		#search-cart a#button,#cart,#search-cart,.view-cart,.mini-cart-total,#cart .content,#search-cart .searchBox{border-color:#<?php echo $customStyles['slideBottom']; ?>}
	<?php endif; ?>
	<?php if($customStyles['slideTop']): //Category Link Colour ?>
		#menu > li > a,#menu > li > div > ul > li > a,.mini-cart-total td.right,.mini-cart-total td{color:#<?php echo $customStyles['slideTop']; ?>}		
	<?php endif; ?>
	<?php if($customStyles['active']): //Category Link Colour ?>
		#menu > li > a.active{color:#<?php echo $customStyles['active']; ?>}		
	<?php endif; ?>
	
</style>
<?php endif; ?>
<script type="text/javascript">
	var slideSpeed = <?php if($customStyles['slideSpeed']){echo $customStyles['slideSpeed']; } else { echo 4000; } ?>;
	var slideAnim = <?php if($customStyles['slideAnim']){ echo '"' . $customStyles['slideAnim'] . '"' ; } else { echo '"slide"'; } ?>; 
</script>
</head>
<body <?php 			
	if($page == "common/home" || $page == ''){
		echo 'class="home"';
	}elseif($page == "product/category"){
		$titleName = explode(' ',$title);
		$page = $titleName[0];	
		echo 'class="' . strtolower($page) . " category" . '"';		
	}elseif($page == "product/product"){
		$titleName = explode(' ',$title);
		$page = $titleName[0];	
		echo 'class="' . strtolower($page) . " product_page" . '"';		
	}elseif($page == 'checkout/cart'){
		echo 'class="shopping_cart"';
	}elseif($page == 'product/search'){
		echo 'class="' . "search" . '"';
	}elseif($page == 'product/special'){
		echo 'class="' . "special_offers" . '"';
	}elseif($page == 'information/information'){
		echo 'class="' . "page" . '"';
	}elseif($page !== "common/home"){
		$titleName = explode(' ',$title);
		$page = $titleName[0];	
			if(isset($titleName[1])){
				$page = $titleName[0] . "_" . $titleName[1];
			}
		echo 'class="' . strtolower($page) . '"';				
	}
?>>
<div id="fb-root"></div>

<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div id="container">
	<div id="top">
      <div id="welcome">
            <?php if (!$logged) { ?>
                <p><?php echo $text_welcome; ?></p>
            <?php } else { ?>
                <p><?php echo $text_logged; ?></p>
            <?php } ?>
      </div>
      <ul class="links">      
          <li class="phone"><span></span><a href="tel:<?php echo $this->config->get('config_telephone'); ?>"><?php echo $this->config->get('config_telephone'); ?></a></li>
          <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a>
                <ul>
                    <li><a href="<?php echo $wishlist; ?>" id="wishlist-total"><?php echo $text_wishlist; ?></a></li>
                    <li><a href="<?php echo $shopping_cart; ?>"><?php echo $text_shopping_cart; ?></a></li>
                    <li><a href="<?php echo $checkout; ?>"><?php echo $text_checkout; ?></a></li>
                </ul>
          </li>
          <li class="lang"><?php echo $language; ?></li>
          <li class="curr"><?php echo $currency; ?></li>
      </ul>
      <div class="clear"></div>
  </div> 
  
<header>
	<?php if ($logo) { ?>
        <?php 
            if($page == "common/home" || $page == ''):
        ?>
            <h1 id="logo"><a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" /></a></h1>
        <?php else: ?>
            <div id="logo"><a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" /></a></div>
        <?php endif; ?>
    <?php } ?>
    <div class="hshare">
    	<span><?php echo $this->language->get('text_hshare') ?></span>
        <div class="fb-like" data-href="http://www.webiz.mu/themes/opencart/megastore" data-layout="button_count" data-width="350" data-show-faces="false" data-font="lucida grande"></div>
        
        <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.webiz.mu/themes/opencart/megastore" data-dnt="true">Tweet</a>
    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
    
        <div class="pin-it-button">
        <a href="http://pinterest.com/pin/create/button/?url=http://www.webiz.mu/themes/opencart/megastore&media=<?php echo $logo; ?>&description=<?php echo $description; ?>" class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>
        </div>
        <div class="clear"></div>
	</div>
	<div class="promo-img">    
    	<?php if($customStyles['headerImage']): ?>
        	<a href="<?php echo $customStyles['topImgHref']?>"><img src="<?php echo HTTP_IMAGE . $customStyles['headerImage']; ?>" /></a>
        <?php endif;?>
    </div>
    <div class="clear"></div>
</header>

<div id="search-cart">
      <div class="searchBox">
		  <?php if ($filter_name) { ?>
                <input id="search-input" type="text" name="filter_name" value="<?php echo $filter_name; ?>" />
          <?php } else { ?>
                <input id="search-input" type="text" name="filter_name" value="<?php echo $this->language->get('search_input') ?>"  />
        <?php } ?>
         <div class="selectCat">
         	<span class="selected-cat"><?php echo $this->language->get('search_all') ?></span>
         	<ul>
            	<?php $results = $this->model_catalog_category->getCategories(0); ?>            	
                	<ul class="cat-list">
                    <li>
                    	<span>
                        	<?php echo $this->language->get('search_all') ?>
                        </span>
						<?php foreach($results as $result): ?>
                                <li class="<?php echo $result["category_id"]; ?>"><?php echo $result['name']; ?></li>
                        <?php endforeach; ?>
                	</ul>
                </li>
            </ul>
            <input id="select-cat" type="hidden" name="filter_category_id" value="" />            
     	</div>
  </div>
  <a id="button"><?php echo $this->language->get('search_search') ?></a>
  
   <?php echo $cart; ?>
   <div class="clear"></div>
</div>
  
<?php if ($categories) { ?>
<nav>
  <ul id="menu">
  	<li><a href="<?php echo $home; ?>" class="home <?php if($page == "common/home" || $page == ""){ echo "active"; } ?>"><?php echo $text_home; ?></a></li>
    <?php foreach ($categories as $category) { ?>
    <li <?php if($category['children']){ echo "class='parent'";} ?>><a href="<?php echo $category['href']; ?>" <?php if($title==$category["name"]){echo 'class="active"';}?>><?php echo $category['name']; ?></a>
      <?php if ($category['children']) { ?>
      <div>
        <?php for ($i = 0; $i < count($category['children']);) { ?>
        <ul>
          <?php $j = $i + ceil(count($category['children']) / $category['column']); ?>
          <?php for (; $i < $j; $i++) { ?>
          <?php if (isset($category['children'][$i])) { ?>
          <li><a href="<?php echo $category['children'][$i]['href']; ?>"><?php echo $category['children'][$i]['name']; ?></a></li>
          <?php } ?>
          <?php } ?>
        </ul>
        <?php } ?>
      </div>
      <?php } ?>
    </li>
    <?php } ?>
  </ul>
</nav>
  <ul class="menu2">
  	<li class="mainCat"><a href="javascript:void(0);"><?php echo $this->language->get('main_cat'); ?><span>x</span></a>
         <ul>   
             <?php foreach ($categories as $category) { ?>
            <li <?php if($category['children']){ echo "class='parent'";} ?>><a href="<?php echo $category['href']; ?>" <?php if($title==$category["name"]){echo 'class="active"';}?>><?php echo $category['name']; ?></a></li>
            <?php } ?>
        </ul>
    </li>
</ul>

<?php } ?>
<div id="notification"></div>

