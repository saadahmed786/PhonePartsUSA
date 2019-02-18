<!doctype html>
<html>
<style type="text/css">
	
</style>
<head>
	<title><?php echo $title; ?></title>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
	
	<base href="<?php echo $base; ?>" />
	<meta name="p:domain_verify" content="1e738f3103b9bf7b721347f7bef506af"/>
	
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

	<link rel="author" href="https://plus.google.com/LINK-TO-GOOGLE-PLUS-PROFILE">
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/ppusa2.0/stylesheet/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/ppusa2.0/stylesheet/slick.min.css">
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/ppusa2.0/stylesheet/font-awesome.css">
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/ppusa2.0/stylesheet/bootstrap-select.css">
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/ppusa2.0/stylesheet/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/ppusa2.0/stylesheet/fancybox.css">
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/ppusa2.0/stylesheet/style.css">
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/ppusa2.0/stylesheet/responsive.css">
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/ppusa2.0/stylesheet/tipsy.css">
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/ppusa2.0/stylesheet/side-menu.css">


	<?php foreach ($styles as $style) { ?>
	<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
	<?php } ?>


	<script  src="catalog/view/javascript/ppusa2.0/jquery-1.11.2.js"></script>
	<script  src="catalog/view/javascript/ppusa2.0/bootstrap.js"></script>
	<script  src="catalog/view/javascript/ppusa2.0/bootstrap-select.js"></script>
	<script  src="catalog/view/javascript/ppusa2.0/slick.min.js"></script>
	
	<script  src="catalog/view/javascript/ppusa2.0/elevatezoom.js"></script>
	<script  src="catalog/view/javascript/ppusa2.0/jquery.slimscroll.min.js"></script>
	<script  src="catalog/view/javascript/ppusa2.0/jquery.easing.min.js"></script>
	<script  src="catalog/view/javascript/ppusa2.0/jquery-ui.js"></script>
	<script  src="catalog/view/javascript/ppusa2.0/fancybox.js"></script>
	<script  src="catalog/view/javascript/ppusa2.0/custom.js"></script>
	<script  src="catalog/view/javascript/ppusa2.0/theme.js"></script>
	<script  src="catalog/view/javascript/ppusa2.0/jquery.tipsy.js"></script>
	<script  src="catalog/view/javascript/ppusa2.0/jquery.lazy.load.min.js"></script>

	<?php foreach ($scripts as $script) { ?>
	<script type="text/javascript" src="<?php echo $script; ?>"></script>
	<?php } ?>
	<?php

	// disable analytics code at checkout pages as we are using Analytics Pro for this purpose

	

	if($page_class != 'checkout_checkout')

	{



?>

<?php echo $google_analytics; ?>

<?php

}

?>
<script id="mcjs">!function(c,h,i,m,p){m=c.createElement(h),p=c.getElementsByTagName(h)[0],m.async=1,m.src=i,p.parentNode.insertBefore(m,p)}(document,"script","https://chimpstatic.com/mcjs-connected/js/users/416aed62b9593e8d0994eee2c/a7d0619a94f63aec703282fe1.js");</script>
</head>
<body class="body_<?php echo $page_class; ?>" id="wrapper">
<div id="blackoutBg" style="display:none"></div>
	<div class="wraper">
		<noscript class="noscript">
            <div id="div100">
               <table>
                  <tbody>
                     <tr>
                        <td>
                           <h1 class="blue-title">Looks Like We've  got a Javascript problem...</h1>
                           <p>Javascript seems to have been disabled in your browser. Please enable it and refresh <br> the page so that you can get the full functionality of our site.</p>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </noscript>
		<header id="header" class="header-desktop <?php echo $page_class; ?>">
			<div class="header-top">
				<div class="container">
				 <div class="row hidden" style="margin-top:-7px;font-size:12px">
					<div class="col-md-2"></div>
					<div class="col-md-9" style="color:red;font-weight:bold">Experiencing issues with our new website? email: <a href="mailto:feedback@phonepartsusa.com" >feedback@phonepartsusa.com</a> or <a href="https://phonepartsusa.com/old">Click Here</a> to revert back to our Old Site.</div>
					<div class="col-md-2"></div>

				</div>
				<?php
				$oc_today = strtotime(date('Y-m-d'));
				$oc_start = strtotime($notice['start_date']);
				$oc_end = strtotime($notice['finish_date']);

				if($oc_today>=$oc_start && $oc_today<=$oc_end)
				{
					if($notice['show']=='on' && $notice['notification'])
					{
				?>
				<div class="row" style="margin-top:-7px;font-size:12px">
					<div class="col-md-2"></div>
					<div class="col-md-9" style="text-align:center;color:red;font-weight:bold"><?php echo $notice['notification'];?></div>
					<div class="col-md-2"></div>

				</div>
				<?php
					}
				}
				?>


					<div class="row">
						<div class="col-md-6 col-xs-3 header-top-left">
							<a href="tel:855.213.5588" class="top-inquery col-xs-6">
								<i class="fa fa-phone"></i>
								<small>855.213.5588 </small>
							</a>
							<a href="<?php echo $this->url->link('information/information&amp;information_id=6');?>" class="top-inquery">
								<i class="fa"><img src="catalog/view/theme/ppusa2.0/images/icons/truck.png" alt=""></i>
								<small>Free Shipping Available</small>
							</a>
							<a href="#" class="top-inquery inquery-track top-dropdown-icon col-xs-6">
								<i class="fa"><img src="catalog/view/theme/ppusa2.0/images/icons/text-editor.png" alt=""></i>
								<small>Track An Order</small>
							</a>
							<div class="top-dropdowns account-login text-left">
								<div class="caret-up"></div>
								<div class="top-dropdown-inner">
									<h3 class="blue-title">Track An Order</h3>
									<p>
										<input type="text" class="form-control" id="track_order" placeholder="Enter Your Order Number">
									</p>
									<br>
									<div class="text-right">
										<button class="btn btn-info light" onclick="trackOrder();">Track</button>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-xs-9 header-top-right">
							<ul class="list-inline">
								<li>
									<a href="#" class="top-inquery top-dropdown-icon">
										<i class="fa"><img src="catalog/view/theme/ppusa2.0/images/icons/text-editor.png" alt=""></i>
										<small>Track An Order</small>
									</a>
									<div class="top-dropdowns account-login text-left">
										<div class="caret-up"></div>
										<div class="top-dropdown-inner">
											<h3 class="blue-title">Track An Order</h3>
											<p>
												<input type="text" class="form-control" id="track_order_2" placeholder="Enter Your Order Number">
											</p>
											<br>
											<div class="text-right">
												<button class="btn btn-info light" onclick="trackOrder2();">Track</button>
											</div>
										</div>
									</div>
								</li>
								<li>
									<a href="javascript:void();" class="top-inquery top-dropdown-icon">
										<i class="fa"><img src="catalog/view/theme/ppusa2.0/images/icons/speedometer.png" alt=""></i>
										<small>Quick Order</small>
									</a>
									<div class="top-dropdowns quick-order text-left">
										<form method="post" enctype="multipart/form-data" action="<?php echo $shopping_cart; ?>">
											<div class="caret-up"></div>
											<div class="top-dropdown-inner">
												<div class="track-container">
													<div class="row track-row track-head">
													<div class="text-center"><h3>Manually Enter</h3></div>
														<div class="col-xs-9 track-col">
															Sku Number
														</div>
														<div class="col-xs-2 track-col">
															Qty
														</div>
														<div class="col-xs-1 track-col text-right pl0 pr0">
														</div>
													</div>
													<div class="row track-row">
														<div class="col-xs-9 track-col">
															<input name="sku[]" type="text" class="form-control uppercase">
														</div>
														<div class="col-xs-2 track-col">
															<input name="qty[]" type="text" class="form-control">
														</div>
														<div class="col-xs-1 track-col text-right pl0 pr0">
															<a href="#" class="row-close"><i class="fa fa-times"></i></a>
														</div>
													</div>
												</div>
												<a href="#" class="addmore-track">
													<i class="fa fa-plus"></i>Add More
												</a>
												<div class="row track-row track-row-copy">
													<div class="col-xs-9 track-col">
														<input name="sku[]" type="text" class="form-control">
													</div>
													<div class="col-xs-2 track-col">
														<input name="qty[]" type="text" class="form-control">
													</div>
													<div class="col-xs-1 track-col text-right pl0 pr0">
														<a href="#" class="row-close"><i class="fa fa-times"></i></a>
													</div>
												</div>
												<br>
												<br>
												<div class="text-center"><strong>Upload Products via CSV File</strong><br>
													<a href="csv/sample_csv.csv" class="blue underline download-cv" style="font-size:10px">Download a sample CSV</a>
												</div>
												<br>
												<div class="text-center"><button type="button" class="btn btn-primary upload-btn">Browse...</button></div>
												<input name="quickordercsv" type="file" accept=".csv">
											</div>
											<div class="top-dropdown-ftr text-center">
												<p>
													<button class="btn btn-info light cart-checkout" name="quickOrder" value="submit" type="submit"><img src="catalog/view/theme/ppusa2.0/images/icons/basket2.png" alt=""> Checkout</button>
												</p>
												<br>
												<!-- <p>
													<button class="btn btn-primary red-btn">
														<i class="fa fa-times"></i> Close
													</button>
												</p> -->
											</div>
										</form>
									</div>
								</li>
								<?php
											if($this->customer->isLogged())
											{

												//str_replace("route=", "", $_SERVER['QUERY_STRING']);
												$logout_redirect_1 =  ($_SERVER['QUERY_STRING']);
												$logout_redirect = '';
												if($logout_redirect_1)
												{
												$logout_redirect = base64_encode($logout_redirect_1);
													
												}

											?>
								<!-- Old Dropdown signout and account control center 
								<li>
									<a href="javascript:void(0)" class="top-inquery top-dropdown-icon">
										<i class="fa"><img src="catalog/view/theme/ppusa2.0/images/icons/user.png" alt=""></i>
										<small>Welcome <?php echo trim($this->customer->getFirstName());?></small>
									</a>
									<div class="top-dropdowns account-login text-left">
										<div class="caret-up"></div>
										<div class="top-dropdown-inner">
											<p>
												<button class="btn btn-info btn-full light btn-big" onclick="javascript:window.location='<?php echo $this->url->link('account/account');?>'"><img src="catalog/view/theme/ppusa2.0/images/icons/person-edit.png" alt="">Account Control Center</button>
											</p>
											
											<br>
											<button class="btn btn-green btn-full btn-big" onclick="javascript:window.location='<?php echo $this->url->link('account/logout&amp;redirect='.$logout_redirect);?>'"><i class="fa fa-power-off"></i> Sign Out of <?php echo $this->customer->getFirstName();?>'s Account</button>
											
										</div>
									</div>
								</li> -->
								<li>
									<a href="<?php echo $this->url->link('account/account');?>" class="top-inquery">
										<i class="fa"><img src="catalog/view/theme/ppusa2.0/images/icons/user.png" alt=""></i>
										<small>Account</small>
									</a>
									&nbsp&nbsp
									<a  style="color: red;" href="<?php echo $this->url->link('account/logout&amp;redirect='.$logout_redirect);?>" class="top-inquery">
										<i class="fa fa-power-off"></i>
										<small>Sign Out (<?php echo $this->customer->getFirstName();?>)</small>
									</a>
									<!-- <div class="top-dropdowns account-login text-left">
										<div class="caret-up"></div>
										<div class="top-dropdown-inner">
											<p>
												<button class="btn btn-info btn-full light btn-big" onclick="javascript:window.location='<?php echo $this->url->link('account/account');?>'"><img src="catalog/view/theme/ppusa2.0/images/icons/person-edit.png" alt="">Account Control Center</button>
											</p>
											
											<br>
											<button class="btn btn-green btn-full btn-big" onclick="javascript:window.location='<?php echo $this->url->link('account/logout&amp;redirect='.$logout_redirect);?>'"><i class="fa fa-power-off"></i> Sign Out of <?php echo $this->customer->getFirstName();?>'s Account</button>
											
										</div>
									</div> -->
								</li>
								<?php
							}
							else
							{
								?>
								<li>
									<a href="<?php echo $this->url->link('account/login');?>" class="top-inquery top-dropdown-icon">
										<i class="fa"><img src="catalog/view/theme/ppusa2.0/images/icons/user.png" alt=""></i>
										<small>Login</small>
									</a>
									
								</li>

								<?php
							}
							?>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<!-- header-top -->
			<div class="header-center">
				<div class="container">
					<div class="catalog row">
						<div class="<?php if ($page_class == 'checkout_checkout') { ?>col-lg-9 <?php } else { ?>col-lg-3 <?php } ?> col-xs-12" >
							
							<a href="<?php echo $home; ?>" <?php if ($page_class == 'checkout_checkout') { ?>style="float:left" <?php } ?> class="logo"><img src="image/logo_new.png" title="<?php echo $name; ?>" alt="<?php echo $name; ?>"></a>
							<?php if ($page_class == 'checkout_checkout') { ?>
							<h2 class="blue-title uppercase" style="float: left; margin-left: 10px; margin-top: 10px;">Checkout</h2> <?php
						}
						?>
							
						</div>
						<?php if ($page_class == 'checkout_checkout') { ?>
							<div class="col-lg-3 col-md-12 text-center">
								<a class="btn btn-green" href="<?php echo $this->url->link('checkout/cart');?>"><i class="fa  fa-chevron-left"></i> Back to Cart</a>
							</div>
						<?php } ?>
						<div class="col-lg-9 col-md-12 serach-box">
							<div class="site-search input-group add-on">
                           <div class="input-group-btn">
                             <!-- <div class="btn btn-default" type="submit"><span class="icon icon-catalog"></span> Catalog</div> -->
                             <select  class="selectpicker" id="home_manufacturer2" data-size="10" style="width:20%">
								<option value="">All Brands</option>
							</select>   
                           </div>
                           <?php $filtering_name = $_GET['filter_name']; ?>
                           <input type="text" id="search_inp" onkeypress="handle(event)" class="input" value="<?php echo $filtering_name; ?>" placeholder="Search Catalog">
                           <a href="javascript:void();" id="search_inp_button" onclick="searcher();"><i class="fa fa-search"></i>
                        </div>
                        <div class="cart-nav">
								<div class="cart-icon cart_icon_btn" style="width:100%">
                              <a href="javascript:void(0);">
                                 
                                 <span class="icon icon-cart"></span>
                              </a>
                </div>
								<div id="cart" class="header_cart_icon">
									<?php echo $cart; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- header-center -->
			<div class="header-bottom">
				<div class="container">
					<div class="row">
						<div class="col-lg-8 header-bottom-left">
							<?php echo $catalog_menu; ?>
							
						</div>
						<div class="col-lg-4 col-md-7 col-xs-11  serach-box">
						<ul class="other-nav text-right">
								
								<li class="buy"><a href="<?php echo $lbb; ?>"><span class="img"><img src="catalog/view/theme/ppusa2.0/images/icons/lcd.png" alt=""></span><small><?php echo $text_lbb; ?></small></a></li>
								<li class="wholesale"><a href="<?php echo $whole; ?>"><span class="img"><img src="catalog/view/theme/ppusa2.0/images/icons/square-box.png" alt=""></span><small><?php echo $text_whole; ?></small></a></li>
							</ul>
							</div>
					</div>
				</div>
			</div>
			<!-- header-bottom -->
	</header> <!-- @End of header -->


		  <!-- Header Mobile -->
		  <header id="header" class="header-mobile">
		  	<?php echo $toggle_menu ;?>	
		  </header>
		  <!-- End Header Mobile -->
	<div id="cartRight">
  <div class="cart-menu" id="sideCartDiv">
  <?php echo $side_cart;?>	
  </div>
</div>
<script>
$(document).ready(function () {
  var trigger = $('.hamburger'),
      overlay = $('.overlay'),
     isClosed = false;

     loadManufacturers('<?php echo $this->request->get['brand_id'];?>');

    trigger.click(function () {
      hamburger_cross();      
    });

    function hamburger_cross() {

      if (isClosed == true) {          
        overlay.hide();
        trigger.removeClass('is-open');
        trigger.addClass('is-closed');
        isClosed = false;
      } else {   
        overlay.show();
        trigger.removeClass('is-closed');
        trigger.addClass('is-open');
        isClosed = true;
      }
  }
  
  $('[data-toggle="offcanvas"]').click(function () {
        $('#wrapper').toggleClass('toggled');
        if($('#wrapper').hasClass('toggled'))
        {
            $('#wrapper').attr('style', 'position:fixed');
            $('.logo').attr('style','z-index: 99;');
        }
        else
        {
            $('#wrapper').attr('style', 'position:inherit');
            $('.logo').attr('style','');
        }
  });  
});


</script>

<script src="https://wchat.freshchat.com/js/widget.js"></script>
<!-- <script type='text/javascript'>var fc_CSS=document.createElement('link');fc_CSS.setAttribute('rel','stylesheet');var isSecured = (window.location && window.location.protocol == 'https:');var lang = document.getElementsByTagName('html')[0].getAttribute('lang'); var rtlLanguages = ['ar','he']; var rtlSuffix = (rtlLanguages.indexOf(lang) >= 0) ? '-rtl' : '';fc_CSS.setAttribute('type','text/css');fc_CSS.setAttribute('href',((isSecured)? 'https://d36mpcpuzc4ztk.cloudfront.net':'http://assets1.chat.freshdesk.com')+'/css/visitor'+rtlSuffix+'.css');document.getElementsByTagName('head')[0].appendChild(fc_CSS);var fc_JS=document.createElement('script'); fc_JS.type='text/javascript'; fc_JS.defer=true;fc_JS.src=((isSecured)?'https://d36mpcpuzc4ztk.cloudfront.net':'http://assets.chat.freshdesk.com')+'/js/visitor.js';(document.body?document.body:document.getElementsByTagName('head')[0]).appendChild(fc_JS);window.freshchat_setting= 'eyJ3aWRnZXRfc2l0ZV91cmwiOiJwaG9uZXBhcnRzdXNhLmZyZXNoZGVzay5jb20iLCJwcm9kdWN0X2lkIjpudWxsLCJuYW1lIjoiUGhvbmVQYXJ0c1VTQS5jb20iLCJ3aWRnZXRfZXh0ZXJuYWxfaWQiOm51bGwsIndpZGdldF9pZCI6IjkwOTM2ZmE0LWQ1ODUtNGQ0NS1iNTZiLWRlOGJhMWE0MWJlZiIsInNob3dfb25fcG9ydGFsIjpmYWxzZSwicG9ydGFsX2xvZ2luX3JlcXVpcmVkIjpmYWxzZSwiaWQiOjkwMDAwMjMzNjYsIm1haW5fd2lkZ2V0Ijp0cnVlLCJmY19pZCI6ImM2ZjczMjI5NTljNDlkNGI5OTU1N2NiMjk1ZjBmZGZlIiwic2hvdyI6MSwicmVxdWlyZWQiOjIsImhlbHBkZXNrbmFtZSI6IlBob25lUGFydHNVU0EuY29tIiwibmFtZV9sYWJlbCI6Ik5hbWUiLCJtYWlsX2xhYmVsIjoiRW1haWwiLCJtZXNzYWdlX2xhYmVsIjoiTWVzc2FnZSIsInBob25lX2xhYmVsIjoiUGhvbmUgTnVtYmVyIiwidGV4dGZpZWxkX2xhYmVsIjoiVGV4dGZpZWxkIiwiZHJvcGRvd25fbGFiZWwiOiJEcm9wZG93biIsIndlYnVybCI6InBob25lcGFydHN1c2EuZnJlc2hkZXNrLmNvbSIsIm5vZGV1cmwiOiJjaGF0LmZyZXNoZGVzay5jb20iLCJkZWJ1ZyI6MSwibWUiOiJNZSIsImV4cGlyeSI6MTQ1NzY0MzkwNTAwMCwiZW52aXJvbm1lbnQiOiJwcm9kdWN0aW9uIiwiZGVmYXVsdF93aW5kb3dfb2Zmc2V0IjozMCwiZGVmYXVsdF9tYXhpbWl6ZWRfdGl0bGUiOiJDaGF0IGluIHByb2dyZXNzIiwiZGVmYXVsdF9taW5pbWl6ZWRfdGl0bGUiOiJMZXQncyB0YWxrISIsImRlZmF1bHRfdGV4dF9wbGFjZSI6IllvdXIgTWVzc2FnZSIsImRlZmF1bHRfY29ubmVjdGluZ19tc2ciOiJXYWl0aW5nIGZvciBhbiBhZ2VudCIsImRlZmF1bHRfd2VsY29tZV9tZXNzYWdlIjoiSGkhIEhvdyBjYW4gd2UgaGVscCB5b3UgdG9kYXk/IiwiZGVmYXVsdF93YWl0X21lc3NhZ2UiOiJPbmUgb2YgdXMgd2lsbCBiZSB3aXRoIHlvdSByaWdodCBhd2F5LCBwbGVhc2Ugd2FpdC4iLCJkZWZhdWx0X2FnZW50X2pvaW5lZF9tc2ciOiJ7e2FnZW50X25hbWV9fSBoYXMgam9pbmVkIHRoZSBjaGF0IiwiZGVmYXVsdF9hZ2VudF9sZWZ0X21zZyI6Int7YWdlbnRfbmFtZX19IGhhcyBsZWZ0IHRoZSBjaGF0IiwiZGVmYXVsdF9hZ2VudF90cmFuc2Zlcl9tc2dfdG9fdmlzaXRvciI6IllvdXIgY2hhdCBoYXMgYmVlbiB0cmFuc2ZlcnJlZCB0byB7e2FnZW50X25hbWV9fSIsImRlZmF1bHRfdGhhbmtfbWVzc2FnZSI6IlRoYW5rIHlvdSBmb3IgY2hhdHRpbmcgd2l0aCB1cy4gSWYgeW91IGhhdmUgYWRkaXRpb25hbCBxdWVzdGlvbnMsIGZlZWwgZnJlZSB0byBwaW5nIHVzISIsImRlZmF1bHRfbm9uX2F2YWlsYWJpbGl0eV9tZXNzYWdlIjoiT3VyIGFnZW50cyBhcmUgdW5hdmFpbGFibGUgcmlnaHQgbm93LiBTb3JyeSBhYm91dCB0aGF0LCBidXQgcGxlYXNlIGxlYXZlIHVzIGEgbWVzc2FnZSBhbmQgd2UnbGwgZ2V0IHJpZ2h0IGJhY2suIiwiZGVmYXVsdF9wcmVjaGF0X21lc3NhZ2UiOiJXZSBjYW4ndCB3YWl0IHRvIHRhbGsgdG8geW91LiBCdXQgZmlyc3QsIHBsZWFzZSB0ZWxsIHVzIGEgYml0IGFib3V0IHlvdXJzZWxmLiIsImFnZW50X3RyYW5zZmVyZWRfbXNnIjoiWW91ciBjaGF0IGhhcyBiZWVuIHRyYW5zZmVycmVkIHRvIHt7YWdlbnRfbmFtZX19IiwiYWdlbnRfcmVvcGVuX2NoYXRfbXNnIjoie3thZ2VudF9uYW1lfX0gcmVvcGVuZWQgdGhlIGNoYXQiLCJ2aXNpdG9yX3NpZGVfaW5hY3RpdmVfbXNnIjoiVGhpcyBjaGF0IGhhcyBiZWVuIGluYWN0aXZlIGZvciB0aGUgcGFzdCAyMCBtaW51dGVzLiIsImFnZW50X2Rpc2Nvbm5lY3RfbXNnIjoie3thZ2VudF9uYW1lfX0gaGFzIGJlZW4gZGlzY29ubmVjdGVkIiwic2l0ZV9pZCI6ImM2ZjczMjI5NTljNDlkNGI5OTU1N2NiMjk1ZjBmZGZlIiwiYWN0aXZlIjp0cnVlLCJ3aWRnZXRfcHJlZmVyZW5jZXMiOnsid2luZG93X2NvbG9yIjoiIzc3Nzc3NyIsIndpbmRvd19wb3NpdGlvbiI6IkJvdHRvbSBSaWdodCIsIndpbmRvd19vZmZzZXQiOiIzMCIsIm1pbmltaXplZF90aXRsZSI6IkxldCdzIHRhbGshIiwibWF4aW1pemVkX3RpdGxlIjoiQ2hhdCBpbiBwcm9ncmVzcyIsInRleHRfcGxhY2UiOiJZb3VyIE1lc3NhZ2UiLCJ3ZWxjb21lX21lc3NhZ2UiOiJIaSEgSG93IGNhbiB3ZSBoZWxwIHlvdSB0b2RheT8iLCJ0aGFua19tZXNzYWdlIjoiVGhhbmsgeW91IGZvciBjaGF0dGluZyB3aXRoIHVzLiBJZiB5b3UgaGF2ZSBhZGRpdGlvbmFsIHF1ZXN0aW9ucywgZmVlbCBmcmVlIHRvIHBpbmcgdXMhIiwid2FpdF9tZXNzYWdlIjoiT25lIG9mIHVzIHdpbGwgYmUgd2l0aCB5b3UgcmlnaHQgYXdheSwgcGxlYXNlIHdhaXQuIiwiYWdlbnRfam9pbmVkX21zZyI6Int7YWdlbnRfbmFtZX19IGhhcyBqb2luZWQgdGhlIGNoYXQiLCJhZ2VudF9sZWZ0X21zZyI6Int7YWdlbnRfbmFtZX19IGhhcyBsZWZ0IHRoZSBjaGF0IiwiYWdlbnRfdHJhbnNmZXJfbXNnX3RvX3Zpc2l0b3IiOiJZb3VyIGNoYXQgaGFzIGJlZW4gdHJhbnNmZXJyZWQgdG8ge3thZ2VudF9uYW1lfX0iLCJjb25uZWN0aW5nX21zZyI6IldhaXRpbmcgZm9yIGFuIGFnZW50In0sInJvdXRpbmciOm51bGwsInByZWNoYXRfZm9ybSI6dHJ1ZSwicHJlY2hhdF9tZXNzYWdlIjoiV2UgY2FuJ3Qgd2FpdCB0byB0YWxrIHRvIHlvdS4gQnV0IGZpcnN0LCBwbGVhc2UgdGVsbCB1cyBhIGJpdCBhYm91dCB5b3Vyc2VsZi4iLCJwcmVjaGF0X2ZpZWxkcyI6eyJuYW1lIjp7InRpdGxlIjoiTmFtZSIsInNob3ciOiIyIn0sImVtYWlsIjp7InRpdGxlIjoiRW1haWwiLCJzaG93IjoiMiJ9LCJwaG9uZSI6eyJ0aXRsZSI6IlBob25lIE51bWJlciIsInNob3ciOiIwIn0sInRleHRmaWVsZCI6eyJ0aXRsZSI6IlRleHRmaWVsZCIsInNob3ciOiIwIn0sImRyb3Bkb3duIjp7InRpdGxlIjoiRHJvcGRvd24iLCJzaG93IjoiMCIsIm9wdGlvbnMiOlsibGlzdDEiLCJsaXN0MiIsImxpc3QzIl19fSwiYnVzaW5lc3NfY2FsZW5kYXIiOm51bGwsIm5vbl9hdmFpbGFiaWxpdHlfbWVzc2FnZSI6eyJ0ZXh0IjoiT3VyIGFnZW50cyBhcmUgdW5hdmFpbGFibGUgcmlnaHQgbm93LiBTb3JyeSBhYm91dCB0aGF0LCBidXQgcGxlYXNlIGxlYXZlIHVzIGEgbWVzc2FnZSBhbmQgd2UnbGwgZ2V0IHJpZ2h0IGJhY2suIiwidGlja2V0X2xpbmtfb3B0aW9uIjoiMCIsImN1c3RvbV9saW5rX3VybCI6IiJ9LCJwcm9hY3RpdmVfY2hhdCI6ZmFsc2UsInByb2FjdGl2ZV90aW1lIjoxNSwic2l0ZV91cmwiOiJwaG9uZXBhcnRzdXNhLmZyZXNoZGVzay5jb20iLCJleHRlcm5hbF9pZCI6bnVsbCwiZGVsZXRlZCI6ZmFsc2UsIm9mZmxpbmVfY2hhdCI6eyJzaG93IjoiMSIsImZvcm0iOnsibmFtZSI6Ik5hbWUiLCJlbWFpbCI6IkVtYWlsIiwibWVzc2FnZSI6Ik1lc3NhZ2UifSwibWVzc2FnZXMiOnsidGl0bGUiOiJMZWF2ZSB1cyBhIG1lc3NhZ2UhIiwidGhhbmsiOiJUaGFuayB5b3UgZm9yIHdyaXRpbmcgdG8gdXMuIFdlIHdpbGwgZ2V0IGJhY2sgdG8geW91IHNob3J0bHkuIiwidGhhbmtfaGVhZGVyIjoiVGhhbmsgeW91ISJ9fSwibW9iaWxlIjp0cnVlLCJjcmVhdGVkX2F0IjoiMjAxNi0wMi0yM1QwMToxOTowOC4wMDBaIiwidXBkYXRlZF9hdCI6IjIwMTYtMDItMjNUMDE6MTk6MTEuMDAwWiJ9';</script> -->