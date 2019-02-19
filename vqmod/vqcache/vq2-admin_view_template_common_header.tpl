<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>">
<head>
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<link rel="stylesheet" type="text/css" href="view/stylesheet/stylesheet.css" />
<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>

		<script type="text/javascript" src="view/javascript/jquery/jquery-1.7.1.min.js"></script>
		<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
		<script src="https://code.jquery.com/jquery-migrate-1.4.0.min.js"></script> -->
		<style type="text/css">
	#menu > ul ul li.divider { padding: 0px; }
	#menu > ul ul li.dropdown-header { padding: 2px 7px; }
	li.divider {
		background-color: #3b3b3b;
		height: 1px;
		margin: 7.5px 0;
		overflow: hidden;
		padding: 0px;
		width: 100%;
	}

	li.dropdown-header {
		color: #777;
		display: block;
		font-size: 11px;
		line-height: 1.42857;
		padding: 3px 20px;
		white-space: nowrap;
	}	
		</style>
		
<script type="text/javascript" src="view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />
<script type="text/javascript" src="view/javascript/jquery/tabs.js"></script>
<script type="text/javascript" src="view/javascript/jquery/superfish/js/superfish.js"></script>
<script type="text/javascript" src="view/javascript/common.js"></script>
<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>
<script type="text/javascript">
//-----------------------------------------
// Confirm Actions (delete, uninstall)
//-----------------------------------------
$(document).ready(function(){
    // Confirm Delete
    $('#form').submit(function(){
        if ($(this).attr('action').indexOf('delete',1) != -1) {
            if (!confirm('<?php echo $text_confirm; ?>')) {
                return false;
            }
        }
    });
    	
    // Confirm Uninstall
    $('a').click(function(){
        if ($(this).attr('href') != null && $(this).attr('href').indexOf('uninstall', 1) != -1) {
            if (!confirm('<?php echo $text_confirm; ?>')) {
                return false;
            }
        }
    });
});
</script>
</head>
<body>
<div id="container">
<div id="header" <?php if($this->request->get['embed']==1) echo 'style="display:none";';?>>

  <div class="div1">
    <div class="div2"><img src="view/image/logo.png" title="<?php echo $heading_title; ?>" onclick="location = '<?php echo $home; ?>'" /></div>
    <?php if ($logged) { ?>
    <div class="div3"><img src="view/image/lock.png" alt="" style="position: relative; top: 3px;" />&nbsp;<?php echo $logged; ?></div>
    <?php } ?>
  </div>
  <?php if ($logged) { ?>
  <div id="menu">
    <ul class="left" style="display: none;">
      <li id="dashboard"><a href="<?php echo $home; ?>" class="top"><?php echo $text_dashboard; ?></a></li>
      <li id="catalog"><a class="top"><?php echo $text_catalog; ?></a>
        <ul>
          <li><a href="<?php echo $category; ?>"><?php echo $text_category; ?></a></li>
          <li><a href="<?php echo $product; ?>"><?php echo $text_product; ?></a></li>
          <li><a href="<?php echo $lower_grade; ?>">Lower Grade</a></li>
          <li><a class="parent"><?php echo $text_attribute; ?></a>
            <ul>
              <li><a href="<?php echo $attribute; ?>"><?php echo $text_attribute; ?></a></li>
              <li><a href="<?php echo $attribute_group; ?>"><?php echo $text_attribute_group; ?></a></li>
            </ul>
          </li>
          <li><a href="<?php echo $option; ?>"><?php echo $text_option; ?></a></li>
          <li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
          <li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>
          <li><a href="<?php echo $review; ?>"><?php echo $text_review; ?></a></li>
          <li><a href="<?php echo $information; ?>"><?php echo $text_information; ?></a></li>
        </ul>
      </li>
      <li id="extension"><a class="top"><?php echo $text_extension; ?></a>
        <ul>
          <li><a href="<?php echo $module; ?>"><?php echo $text_module; ?></a></li>

                <?php if($this->user->hasPermission('access','common/elmanager')) { ?>
				<li><a onclick="dqis_image_manager('<?php echo $image_manager_token; ?>');"><?php echo $text_image_manager; ?></a></li>
				<?php } ?>
          <li><a href="<?php echo $shipping; ?>"><?php echo $text_shipping; ?></a></li>
          <li><a href="<?php echo $payment; ?>"><?php echo $text_payment; ?></a></li>
          <li><a href="<?php echo $total; ?>"><?php echo $text_total; ?></a></li>
<li><a href="<?php echo $oosnotify_link; ?>"><?php echo $text_oosnotify; ?></a></li>
                
          <li><a href="<?php echo $feed; ?>"><?php echo $text_feed; ?></a></li>
          <li><a href="<?php echo $vqmod_manager; ?>"><?php echo $text_vqmod_manager; ?></a></li>

			<li><a class="parent"><?php echo $text_openbay_extension; ?></a>
                <ul>
                    <li><a href="<?php echo $openbay_link_extension; ?>"><?php echo $text_openbay_dashboard; ?></a></li>
                    <li><a href="<?php echo $openbay_link_orders; ?>"><?php echo $text_openbay_orders; ?></a></li>
                    <li><a href="<?php echo $openbay_link_items; ?>"><?php echo $text_openbay_items; ?></a></li>

                    <?php if($openbay_markets['ebay'] == 1){ ?>
                    <li><a class="parent" href="<?php echo $openbay_link_ebay; ?>"><?php echo $text_openbay_ebay; ?></a>
                        <ul>
                            <li><a href="<?php echo $openbay_link_ebay_settings; ?>"><?php echo $text_openbay_settings; ?></a></li>
                            <li><a href="<?php echo $openbay_link_ebay_links; ?>"><?php echo $text_openbay_links; ?></a></li>
                            <li><a href="<?php echo $openbay_link_ebay_orderimport; ?>"><?php echo $text_openbay_order_import; ?></a></li>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($openbay_markets['amazon'] == 1){ ?>
                    <li><a class="parent" href="<?php echo $openbay_link_amazon; ?>"><?php echo $text_openbay_amazon; ?></a>
                        <ul>
                            <li><a href="<?php echo $openbay_link_amazon_settings; ?>"><?php echo $text_openbay_settings; ?></a></li>
                            <li><a href="<?php echo $openbay_link_amazon_links; ?>"><?php echo $text_openbay_links; ?></a></li>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($openbay_markets['amazonus'] == 1){ ?>
                    <li><a class="parent" href="<?php echo $openbay_link_amazonus; ?>"><?php echo $text_openbay_amazonus; ?></a>
                        <ul>
                            <li><a href="<?php echo $openbay_link_amazonus_settings; ?>"><?php echo $text_openbay_settings; ?></a></li>
                            <li><a href="<?php echo $openbay_link_amazonus_links; ?>"><?php echo $text_openbay_links; ?></a></li>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($openbay_markets['play'] == 1){ ?>
                    <li><a class="parent" href="<?php echo $openbay_link_play; ?>"><?php echo $text_openbay_play; ?></a>
                        <ul>
                            <li><a href="<?php echo $openbay_link_play_settings; ?>"><?php echo $text_openbay_settings; ?></a></li>
                            <li><a href="<?php echo $openbay_link_play_report_price; ?>"><?php echo $text_openbay_report_price; ?></a></li>
                        </ul>
                    </li>
                    <?php } ?>
                </ul>
            </li>
            

                        <?php if( isset($menu) && is_array($menu) && is_array($menu['extension'])) { ?>
							<?php foreach($menu['extension'] as $extension){ ?>
								<li><a href="<?php echo $extension['href']; ?>"><?php echo $extension['text']; ?></a></li>
							<?php } ?>
						<?php } ?>
                        
          <li><a href="<?php echo $googleprint; ?>"><?php echo $text_googleprint; ?></a></li>
        </ul>
      </li>
      <li id="sale"><a class="top"><?php echo $text_sale; ?></a>
        <ul>
          <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
          <li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
          <li><a class="parent"><?php echo $text_customer; ?></a>
            <ul>
              <li><a href="<?php echo $customer; ?>"><?php echo $text_customer; ?></a></li>
              <li><a href="<?php echo $customer_group; ?>"><?php echo $text_customer_group; ?></a></li>
              <li><a href="<?php echo $customer_blacklist; ?>"><?php echo $text_customer_blacklist; ?></a></li>
            </ul>
          </li>
          <li><a href="<?php echo $affiliate; ?>"><?php echo $text_affiliate; ?></a></li>
          <li><a href="<?php echo $coupon; ?>"><?php echo $text_coupon; ?></a></li>
          <li><a href="<?php echo $return_program; ?>">Return Program</a></li>
          <?php
          if($this->user->canIssueStoreCredit())
          {
          ?>
          <li><a class="parent"><?php echo $text_voucher; ?></a>
            <ul>
              <li><a href="<?php echo $voucher; ?>"><?php echo $text_voucher; ?></a></li>
              
              <li><a href="<?php echo $voucher_theme; ?>"><?php echo $text_voucher_theme; ?></a></li>
            </ul>
          </li>
 <?php
    }
    ?>
          <li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
        </ul>
      </li>
     
       <li id="pos" class=""><a class="top">Point Of Sale</a>
                <ul style="display: none; visibility: hidden;">
                  <li><a href="index.php?route=pos/pos/index&token=<?php echo $this->session->data['token'] ?>">POS</a></li>
                  <li><a href="index.php?route=pos/dashboard&token=<?php echo $this->session->data['token'] ?>">Dashboard</a></li><li><a href="<?php echo $voided_items_report; ?>">Voided Items Report</a></li>
                </ul>
              </li>
      <li id="system"><a class="top"><?php echo $text_system; ?></a>
        <ul>
          <li><a href="<?php echo $setting; ?>"><?php echo $text_setting; ?></a></li>
          <li><a class="parent"><?php echo $text_design; ?></a>
            <ul>
              <li><a href="<?php echo $layout; ?>"><?php echo $text_layout; ?></a></li>
              <li><a href="<?php echo $banner; ?>"><?php echo $text_banner; ?></a></li>
            </ul>
          </li>
          <li><a class="parent"><?php echo $text_users; ?></a>
            <ul>
              <li><a href="<?php echo $user; ?>"><?php echo $text_user; ?></a></li>
              <li><a href="<?php echo $user_group; ?>"><?php echo $text_user_group; ?></a></li>
            </ul>
          </li>
          <li><a class="parent"><?php echo $text_localisation; ?></a>
            <ul>
              <li><a href="<?php echo $canned_messages; ?>"><?php echo $text_canned_messages; ?></a></li>
              <li><a href="<?php echo $language; ?>"><?php echo $text_language; ?></a></li>
              <li><a href="<?php echo $currency; ?>"><?php echo $text_currency; ?></a></li>
              <li><a href="<?php echo $stock_status; ?>"><?php echo $text_stock_status; ?></a></li>
              <li><a href="<?php echo $order_status; ?>"><?php echo $text_order_status; ?></a></li>
              <li><a class="parent"><?php echo $text_return; ?></a>
                <ul>
                  <li><a href="<?php echo $return_status; ?>"><?php echo $text_return_status; ?></a></li>
                  <li><a href="<?php echo $return_action; ?>"><?php echo $text_return_action; ?></a></li>
                  <li><a href="<?php echo $return_reason; ?>"><?php echo $text_return_reason; ?></a></li>
                </ul>
              </li>
              <li><a href="<?php echo $country; ?>"><?php echo $text_country; ?></a></li>
              <li><a href="<?php echo $zone; ?>"><?php echo $text_zone; ?></a></li>
              <li><a href="<?php echo $geo_zone; ?>"><?php echo $text_geo_zone; ?></a></li>
              <li><a class="parent"><?php echo $text_tax; ?></a>
                <ul>
                  <li><a href="<?php echo $tax_class; ?>"><?php echo $text_tax_class; ?></a></li>
                  <li><a href="<?php echo $tax_rate; ?>"><?php echo $text_tax_rate; ?></a></li>
                </ul>
              </li>
              <li><a href="<?php echo $length_class; ?>"><?php echo $text_length_class; ?></a></li>
              <li><a href="<?php echo $weight_class; ?>"><?php echo $text_weight_class; ?></a></li>
            </ul>
          </li>
          <li><a href="<?php echo $error_log; ?>"><?php echo $text_error_log; ?></a></li>
          <li><a href="<?php echo $backup; ?>"><?php echo $text_backup; ?></a></li>
        </ul>
      </li>
      <li id="reports"><a class="top"><?php echo $text_reports; ?></a>
        <ul>
<li><a href="<?php echo HTTPS_SERVER . 'index.php?route=report/smartsearch&token=' . $this->session->data['token']; ?>">Smart Search History</a></li>
          <li><a class="parent"><?php echo $text_sale; ?></a>
            <ul>
              <li><a href="<?php echo $report_sale_order; ?>"><?php echo $text_report_sale_order; ?></a></li>
               
              <li><a href="<?php echo $report_sale_tax; ?>"><?php echo $text_report_sale_tax; ?></a></li>
              <li><a href="<?php echo $report_sale_shipping; ?>"><?php echo $text_report_sale_shipping; ?></a></li>
              <li><a href="<?php echo $report_sale_return; ?>"><?php echo $text_report_sale_return; ?></a></li>
              <li><a href="<?php echo $report_sale_coupon; ?>"><?php echo $text_report_sale_coupon; ?></a></li>
            </ul>
          </li>
          <li><a class="parent"><?php echo $text_product; ?></a>
            <ul>
              <li><a href="<?php echo $report_product_viewed; ?>"><?php echo $text_report_product_viewed; ?></a></li>
              <li><a href="<?php echo $report_product_purchased; ?>"><?php echo $text_report_product_purchased; ?></a></li>
            </ul>
          </li>
          <li><a class="parent"><?php echo $text_customer; ?></a>
            <ul>
              <li><a href="<?php echo $report_customer_order; ?>"><?php echo $text_report_customer_order; ?></a></li>
              <li><a href="<?php echo $report_customer_reward; ?>"><?php echo $text_report_customer_reward; ?></a></li>
              <li><a href="<?php echo $report_customer_credit; ?>"><?php echo $text_report_customer_credit; ?></a></li>
            </ul>
          </li>
          <li><a class="parent"><?php echo $text_affiliate; ?></a>
            <ul>
              <li><a href="<?php echo $report_affiliate_commission; ?>"><?php echo $text_report_affiliate_commission; ?></a></li>
            </ul>
          </li>
        </ul>
      </li>

				<?php if(isset($text_ne)) { ?>
				<li id="ne"><a class="top"><?php echo $text_ne; ?></a>
					<ul>
						<li><a href="<?php echo $ne_email; ?>"><?php echo $text_ne_email; ?></a></li>
						<li><a href="<?php echo $ne_draft; ?>"><?php echo $text_ne_draft; ?></a></li>
						<li><a href="<?php echo $ne_marketing; ?>"><?php echo $text_ne_marketing; ?></a></li>
						<li><a href="<?php echo $ne_subscribers; ?>"><?php echo $text_ne_subscribers; ?></a></li>
						<li><a href="<?php echo $ne_stats; ?>"><?php echo $text_ne_stats; ?></a></li>
						<li><a href="<?php echo $ne_robot; ?>"><?php echo $text_ne_robot; ?></a></li>
						<li><a href="<?php echo $ne_template; ?>"><?php echo $text_ne_template; ?></a></li>
						<li style="width:97%;"><hr/></li>
						<li><a class="parent"><?php echo $text_ne_support; ?></a>
							<ul>
								<li><a href="https://www.codersroom.com/support/register.php" target="_blank"><?php echo $text_ne_support_register; ?></a></li>
								<li><a href="https://www.codersroom.com/support/clientarea.php" target="_blank"><?php echo $text_ne_support_login; ?></a></li>
								<li><a href="https://www.codersroom.com/support/" target="_blank"><?php echo $text_ne_support_dashboard; ?></a></li>
							</ul>
						</li>
						<li><a href="<?php echo $ne_update_check; ?>"><?php echo $text_ne_update_check; ?></a></li>
					</ul>
				</li>
				<?php } ?>
				
      <li id="help"><a class="top"><?php echo $text_help; ?></a>
        <ul>
          <li><a onClick="window.open('http://www.opencart.com');"><?php echo $text_opencart; ?></a></li>
          <li><a onClick="window.open('http://www.opencart.com/index.php?route=documentation/introduction');"><?php echo $text_documentation; ?></a></li>
          <li><a onClick="window.open('http://forum.opencart.com');"><?php echo $text_support; ?></a></li>
        </ul>
      </li>
    </ul>
    <ul class="right">

		<li id="optimizer-main1" style="width: 45px; height: 34px; background-repeat: no-repeat; background-position: center;  background-image:url(<?php echo $branding_icon; ?>);">
			<a class="top" style="background-image: none;">&nbsp;</a>
			<ul style="margin: 0 0 0 -115px;">
				<li><a href="<?php echo $url_optimization_settings; ?>"><?php echo $text_settings; ?></a></li>
				<?php if($url_cdn_reports) { ?><li><a href="<?php echo $url_cdn_reports; ?>"><?php echo $text_cdn_usage; ?></a></li> <?php } ?>
				<li class="divider"></li>
				<li class="dropdown-header"><?php echo $text_clear_caches; ?> <i class="fa fa-trash"></i></li>
				<li><a href="<?php echo $url_clear_all;?>"><?php echo $text_clear_all; ?></a></li>
				<li><a href="<?php echo $url_clear_vqmod; ?>"><?php echo $text_clear_vqmod; ?></a></li>
				<li><a href="<?php echo $url_clear_system; ?>"><?php echo $text_clear_system; ?></a></li>
				<li><a href="<?php echo $url_clear_minify; ?>"><?php echo $text_clear_minify; ?></a></li>
				<li><a><?php echo $text_clear_sql; ?></a>
					<ul style="margin: -29px 0 0 -165px;" class="dropdown-menu dropdown-menu-right">
						<?php foreach ($sql_caches as $sql_cache) { ?>
						<li><a href="<?php echo $sql_cache['url']; ?>"><?php echo $sql_cache['text']; ?></a></li>
						<?php } ?>
					</ul>
				</li>
				<?php if (isset($url_clear_pagehome)) { ?>
				<li><a><?php echo $text_clear_page; ?></a>
					<ul style="margin: -29px 0 0 -165px;" class="dropdown-menu dropdown-menu-right">
						<li><a href="<?php echo $url_clear_page; ?>"><?php echo $text_clear_page_all; ?></a></li>
						<li><a href="<?php echo $url_clear_pagehome; ?>"><?php echo $text_clear_page_home; ?></a></li>
						<li><a href="<?php echo $url_clear_pageproduct; ?>"><?php echo $text_clear_page_product; ?></a></li>
						<li><a href="<?php echo $url_clear_pagecategory; ?>"><?php echo $text_clear_page_category; ?></a></li>
					</ul>
				</li>
				<?php } else { ?>
				<li><a href="<?php echo $url_clear_page; ?>"><?php echo $text_clear_page; ?></a></li>
				<?php } ?>
				<?php if($url_clear_cdn) { ?><li><a href="<?php echo $url_clear_cdn; ?>"><?php echo $text_flush_cdn; ?></a></li> <?php } ?>
			</ul>
		</li>
		
      <li id="store"><a onClick="window.open('<?php echo $store; ?>');" class="top"><?php echo $text_front; ?></a>
        <ul>
          <?php foreach ($stores as $stores) { ?>
          <li><a onClick="window.open('<?php echo $stores['href']; ?>');"><?php echo $stores['name']; ?></a></li>
          <?php } ?>
        </ul>
      </li>
      <li id="store"><a class="top" href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
    </ul>
    <script type="text/javascript"><!--
$(document).ready(function() {
	$('#menu > ul').superfish({
		hoverClass	 : 'sfHover',
		pathClass	 : 'overideThisToUse',
		delay		 : 0,
		animation	 : {height: 'show'},
		speed		 : 'normal',
		autoArrows   : false,
		dropShadows  : false, 
		disableHI	 : false, /* set to true to disable hoverIntent detection */
		onInit		 : function(){},
		onBeforeShow : function(){},
		onShow		 : function(){},
		onHide		 : function(){}
	});
	
	$('#menu > ul').css('display', 'block');
});
 
function getURLVar(urlVarName) {
	var urlHalves = String(document.location).toLowerCase().split('?');
	var urlVarValue = '';
	
	if (urlHalves[1]) {
		var urlVars = urlHalves[1].split('&');

		for (var i = 0; i <= (urlVars.length); i++) {
			if (urlVars[i]) {
				var urlVarPair = urlVars[i].split('=');
				
				if (urlVarPair[0] && urlVarPair[0] == urlVarName.toLowerCase()) {
					urlVarValue = urlVarPair[1];
				}
			}
		}
	}
	
	return urlVarValue;
} 

$(document).ready(function() {
	route = getURLVar('route');
	
	if (!route) {
		$('#dashboard').addClass('selected');
	} else {
		part = route.split('/');
		
		url = part[0];
		
		if (part[1]) {
			url += '/' + part[1];
		}
		
		$('a[href*=\'' + url + '\']').parents('li[id]').addClass('selected');
	}
});
//--></script> 
  </div>
  <?php } ?>
</div>
