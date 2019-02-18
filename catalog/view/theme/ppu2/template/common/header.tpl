<?php //echo '<pre>'; print_r($menus); exit; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
	<?php foreach ($styles as $style) { ?>
	<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
	<?php } ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link type="text/css" rel="stylesheet" href="catalog/view/theme/ppu2/stylesheet/bootstrap.min.css" />
	<link type="text/css" rel="stylesheet" href="catalog/view/theme/ppu2/stylesheet/bootstrap-theme.min.css" />
	<link type="text/css" rel="stylesheet" href="catalog/view/theme/ppu2/stylesheet/global_style.css" />
	<link type="text/css" rel="stylesheet" href="catalog/view/theme/ppu2/stylesheet/responsive_style.css" />
	<!-- <link rel="stylesheet" type="text/css" href="slick/slick.css"/> -->
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/ppu2/stylesheet/font-awesome/css/font-awesome.min.css"/>
	<!-- <link type="text/css" href="scroll_bar/perfect-scrollbar.css" rel="stylesheet"> -->
	<link type="text/css" href="catalog/view/theme/ppu2/stylesheet/owl.carousel.css" rel="stylesheet">


	<?php foreach ($scripts as $script) { ?>
	<!-- <script type="text/javascript" src="<?php echo $script; ?>"></script> -->
	<?php } ?>
	<script type="text/javascript" src="catalog/view/javascript/jquery/jquery1.11.1.min.js"></script>
	<script type="text/javascript" src="catalog/view/javascript/bootstrap.min.js"></script>
	<!-- <script type="text/javascript" src="catalog/view/javascript/bossthemes/bossthemes.js"></script>
	<script type="text/javascript" src="catalog/view/javascript/bossthemes/getwidthbrowser.js"></script> -->
	<!-- <script type="text/javascript" src="slick/slick.js"></script> -->
	<!-- <script type="text/javascript" src="catalog/view/javascript/ppu2/scripts.js"></script> -->
	<!-- <script type="text/javascript" src="scroll_bar/perfect-scrollbar.js"></script> -->
	<script type="text/javascript" src="catalog/view/javascript/owl.carousel.min.js"></script>
	<script>
		function boss_addToCart(product_id, quantity) {
			if (!quantity) {
				quantity =1;
			}

			$.ajax({
				url: 'index.php?route=bossthemes/cart/add',
				type: 'post',
				data: 'product_id=' + product_id + '&quantity=' + quantity,
				dataType: 'json',
				success: function(json) {

					if (json['redirect']) {
						location = json['redirect'];
					}

					if (json['error']) {
						if (json['error']['warning']) {
							addProductNotice(json['title'], json['thumb'], json['error']['warning'], 'failure');
						}
					}

					if (json['success']) {
						$('#cart').load('index.php?route=module/cart');
					}
				}
			});
		}
	</script>
	<script>
		$(window).scroll(function(){
			if  ($(window).scrollTop() >= 38){
				$('.top_bar').slideUp("slow");
				$(".header .col-lg-4, .header .col-lg-8").css({"padding":"9px 0"})
				$(".header").css({"height":"75px"});
				$('.cart_item_box').css({'top': '63px'});
				$('body').css({'padding-top': '179px'});
			}
			else {
				$('.top_bar').slideDown();
				$(".header .col-lg-4, .header .col-lg-8").css({"padding":"26px 0"})
				$(".header").css({"height":"110px"});
				$('.cart_item_box').css({'top': '90px'});
				$('body').css({'padding-top': '195px'});
			}
		});
	</script>
	<script>
		$(document).ready(function(){
			$(".light_box_menu").hide();
			var timer;

			function showBlack () {
				timer =	setTimeout(function () {$(".light_box_menu").fadeIn(200)}, 1000);	
			}

			$(".animate_navigation, .sticky-brand_btn").mouseenter(function(){
				showBlack();
			});
			$(".animate_navigation, .sticky-brand_btn").mouseleave(function(){
				clearTimeout(timer);
				$(".light_box_menu").fadeOut(10);
			});

		});
	</script> 

</head>
<body>
	<div class="light_box_menu"></div>
	<div class="sticky_header_div">
		<div class="container-fluid top_bar">
			<div class="container">
				<div class="col-lg-3 iClients"><p><img src="catalog/view/theme/ppu2/image/global_icon.png" alt="global icon" />International Clients</p></div>
				<div class="col-lg-9 links">
					<ul>
						<li><?= ($logged)? $text_logged: $text_welcome;?></li>
						<li><a href="<?= $wishlist; ?>"><?= $text_wishlist; ?></a></li>
						<li><a href="<?= $contact; ?>"><?= $text_nav_contact; ?></a></li>
						<li><a href="<?= $faq; ?>"><?= $text_faq; ?></a></li>
						<li><a href="http://blog.phonepartsusa.com/">Blog</a></li>
					</ul>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="container-fluid header">
			<div class="container">
				<div class="col-lg-4">
					<?php if ($logo) { ?>
					<a href="<?php echo $home; ?>">
						<img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" />
					</a>
					<?php } ?>
				</div>
				<div class="col-lg-8">
					<input type="search" class="search_bar" placeholder="So, What are you wishing for today?" />
					<input type="button" class="search_btn" value="Search" />
					<span id="cart">
						<?= $cart; ?>
					</span>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="container-fluid navigation_bar">
			<div class="container">
				<ul>
					<?php foreach ($menus as $i => $menu) { ?>
					<li class="animate_navigation">
						<a href="<?= $menu['href']; ?>"><?= $menu['title']; ?></a>
						<?php if ($menu['options']) { ?>
						<div class="child_drag_drop">
							<div class="col-lg-12 nav_child_pdt_ul">
								<?php $n = 0; ?>
								<?php foreach ($menu['options'] as $j => $title) { ?>
								<?php if (!($j % 2)) { ?>
								<?php $color = (!($j % 4))? 'white': 'gray';?>
								<div class="col-lg-2">
									<ul class="<?= $color; ?>">
										<?php } ?>
										<li><a class="title" href="<?php echo $title['parent']['href']; ?>"><?php echo $title['parent']['name']; ?></a></li>
										<?php if ($title['categories']) { ?>
										<?php foreach ($title['categories'] as $k => $category) { ?>
										<li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
										<?php $n++;?>
										<?php 
										if ($n > 17 && $k != (count($title['categories']) - 1 )) {
											$n = 0;
											echo '</ul></div>';
											echo '<div class="col-lg-2"><ul class="' . $color . '">';
										}
										?>
										<?php } ?>
										<?php } ?>
										<?php if (($j % 2)) { ?>
										<?php $n = 0; ?>
									</ul>
								</div>
								<?php } ?>

								<?php }?>
								<!-- <div class="col-lg-4 nav_product_img">
									<a href="javascript:void(0)"><img src="catalog/view/theme/ppu2/image/nav_img.jpg" alt="product" /></a>
								</div> -->
							</div>
						</div>
						<?php } ?>
					</li>
					<?php } ?>					
				</ul>
				<div class="sticky-brand_btn"><i class="fa fa-bars"></i> <span>All Brand</span>
					<div class="sticky_brand_list">
						<!--<h3>ALL BRANDS</h3>-->
						<ul>
							<?php foreach ($menus as $i => $menu) { ?>
							<li>
								<a href="<?= $menu['href']; ?>"><?= $menu['title']; ?></a>
								<?php if ($menu['options']) { ?>
								<div class="brand_nav_child">
									<h4><?= $menu['title']; ?></h4>
									<?php foreach ($menu['options'] as $j => $title) { ?>
									<div class="col-lg-3" style="overflow: hidden;">
										<div style="width: 125%; padding-right: 28px; height: 100%; overflow: auto;">
											<h5><a class="title" href="<?php echo $title['parent']['href']; ?>"><?php echo $title['parent']['name']; ?></a></h5>
											<?php if ($title['categories']) { ?>
											<ul>
												<?php foreach ($title['categories'] as $k => $category) { ?>
												<li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
												<?php } ?>
											</ul>
											<?php } ?>
										</div>
									</div>
									<?php } ?>
								</div>
								<?php } ?>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>

			</div>
		</div>
	</div>
	<?php echo $header_bottom; ?>