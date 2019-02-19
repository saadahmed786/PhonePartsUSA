</div>
</div>
</div>
<style>
    /* Large desktops and laptops */
    @media (min-width: 1200px) {

    }

    /* Portrait tablets and medium desktops */
    @media (min-width: 992px) and (max-width: 1199px) {

    }

    /* Portrait tablets and small desktops */
    @media (min-width: 768px) and (max-width: 800px) {
        .customer-service { float: left; margin: 0 8px 0 0 !important; width: 166px !important;}
        .social-area { float: left; margin: 0 19px 0 0 !important; padding: 20px 0 0; width: 174px !important;}
        .web-promise { float: left; margin: 0 11px 0 0 !important; padding: 20px 0 0; width: 178px !important;}
        .ranking-holder { float: left; padding: 20px 0 0 !important; width: 137px !important;}

    }
    .footer-holder a { color: #252c26; text-decoration: none; transition: all 0.5s; -webkit-transition: all 0.5s; -moz-transition: all 0.5s; outline: none; }
    .footer-holder a:hover { color: #252c26; text-decoration: underline; }
/* @phon cart css
********************************************************************************************
********************************************************************************************/
.container-footer{ max-width:958px; margin:0 auto; border-top:1px solid #e1e1e1; padding:27px 39px 102px 20px;}
.footer-holder{ overflow:hidden; margin:0 0 100px;}
.customer-service{ float:left; margin:0 57px 0 0; width:166px;}
.customer-service h1{ margin:0 0 20px;}
.servics-list{ margin:0; padding:0 0 0 8px; list-style:none;}
.servics-list li{ margin:0 0 0;}
/*.servics-list li:nth-child(7), .servics-list li:nth-child(11){ margin:0 0 15px;}*/
.servics-list li a{ font-size:12px; color:#252c26; font-weight:bold;}

.social-area{ width:239px; float:left; padding:20px 0 0; margin:0 67px 0 0;} 
.social-area h4{ margin:0 0 15px; color:#252c26; font-size:13px;}
.social-area p{ color:#6c706d; font-size:12px; margin:0 0 15px;}
.email-text-feild{ margin:0 0 10px; display:block;}
.email-text-feild input{ width:100%;}
.subscribe-btn{ width:142px; border-top:2px solid #1da0d1; text-align:center; font-size:12px; display:block; padding:10px 10px 10px 0; background:#30aedc; color:#fff; font-weight:bold; border-radius:4px; position:relative; margin:0 0 36px;}
.subscribe-btn:hover{ color:#fff; text-decoration:none !important; background:#1991bc;}
.subscribe-btn img{ margin:0 0 0 16px; position:absolute; top:12px;}
.center-bar{ height:20px; width:1px; background:#fff; position:absolute; right:40px; top:7px; display:inline-block;}
.icon-list{ margin:0; padding:0 0 0 10px; list-style:none;}
.icon-list li{ margin:0 0 0 5px; float:left;}
.icon-list li:first-child{ margin:0;}
.icon-list li{filter: url("data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\'><filter id=\'grayscale\'><feColorMatrix type=\'matrix\' values=\'0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0 0 0 1 0\'/></filter></svg>#grayscale"); /* Firefox 10+, Firefox on Android */
filter: gray; /* IE6-9 */
-webkit-filter: grayscale(100%); /* Chrome 19+, Safari 6+, Safari 6+ iOS */ padding:9px 0; display:block;}
.icon-list li:hover{ text-decoration:none; filter: none;  /* Chrome 19+, Safari 6+, Safari 6+ iOS */ -webkit-filter: none;}
.like-post li:last-child a{ filter: none;}
.social-area h5{ color:#000; font-weight:bold; font-size:13px; margin:0 0 12px;}

.web-promise{ width:214px; float:left; padding:20px 0 0; margin:0 78px 0 0;}
.web-promise h3{ font-size:12px; color:#000; font-weight:bold; margin:0;}
.web-promise p{ color:#999; font-size:12px; margin:0 0 20px;}

.ranking-holder{ width:137px; float:left; padding:20px 0 0;}
.ranking-inn{ padding:10px 10px 30px 10px; margin:0 0 5px; border:1px solid #ebebeb; border-radius:4px;}
.star-list{ margin:0 0 5px; padding:0; list-style:none; overflow:hidden;}
.star-list li{ float:left; margin:0 5px 0 0;}
.date-list{ margin:0; padding:0; list-style:none;}
.date-list li{ font-size:12px;}
.date-list li:nth-child(2){ margin:0 0 10px;}
.shoper-img{ float:right;}

.adevtisement-list{ margin:80px 0 0; padding:0; list-style:none; float:right;}
.adevtisement-list li{ float:left; margin:0 0 0 20px;}
.adevtisement-list li:nth-child(3){ margin-top:-20px;}
.adevtisement-list li:first-child{ margin:0;}

.p-holder{ max-width:661px; margin:0 auto;}
.p-holder p{ color:#d6d6d6; font-size:10px; margin:0; text-align:center;} 

</style>
<div style="clear: both;"></div>
<div class="wrapper" style="margin-top:50px">  
   <div class="container-footer">
       <div class="footer-holder">
           <div class="customer-service">
               <h1><img src="image/data/footer-images/footer-logo.jpg" alt="footer-logo"></h1>
               <ul class="servics-list">
				   <li><a href="<?php echo HTTPS_SERVER;?>index.php?route=catalog/catalog">Product Catalog</a></li>
                   <li><a href="<?php echo HTTPS_SERVER;?>index.php?route=information/contact">Customer Support</a></li>

                   <li><a href="<?php echo HTTPS_SERVER;?>shipping-information">Shipping Rates</a></li>
                   <li><a href="<?php echo HTTPS_SERVER;?>returnpolicy">Returns & Exchanges</a></li>
                   <li><a href="<?php echo HTTPS_SERVER;?>warranty">Warranty</a></li>
                   <li><a href="<?php echo HTTPS_SERVER;?>index.php?route=account/return/insert">Return Items</a></li>
               
                   <li><a href="<?php echo HTTPS_SERVER;?>index.php?route=wholesale/wholesale">Business Accounts</a></li>
                   <li><a href="<?php echo HTTPS_SERVER;?>index.php?route=buyback/buyback">LCD Buy Back</a></li>
                   <li><a href="<?php echo HTTPS_SERVER;?>index.php?route=refurb/refurb">LCD Refurbishment</a></li>
               
                <!-- <li><a href="http://blog.phonepartsusa.com/">Blog</a></li> -->
                <li><a href="/privacy-policy">Privacy Policy</a></li>
                <li><a href="http://phonepartsusa.applytojob.com/apply" target="_blank">We're Hiring</a></li>
                

            </ul>
        </div>
        <div class="social-area">
        	
            <form id="footer-newsletter-form" accept-charset="utf-8" action="https://app.getresponse.com/add_contact_webform.html?u=jgEp" method="post">
                <h4>Never Miss A Sale</h4>
                <p>Daily sales &amp; weekly specials. We'll also send coupons for even further discounts</p>
                <span class="email-text-feild"><input type="text" name="email" class="wf-input wf-req wf-valid__email" placeholder="E-mail Address"></span>
                <a class="subscribe-btn" href="javascript:void(0)" onclick="$('#footer-newsletter-form').submit();" style="color:#FFF">SUBSCRIBE<span class="center-bar">
                </span><img src="image/data/footer-images/arrow.png" alt="arrow">
            </a>
            <input type="hidden" name="webform_id" value="9408002" />
        </form>
        <h5>Follow Us on Social Media:</h5>
        <ul class="icon-list">
           <li><a href="http://instagram.com/phonepartsusa" target="_blank"><img src="image/data/footer-images/social-icon1.jpg" alt="phonepartsusa-instagram"></a></li>
           <li><a href="http://www.youtube.com/user/phonepartsusa" target="_blank"><img src="image/data/footer-images/social-icon2.jpg" alt="phonepartsusa-youtube"></a></li>
           <li><a href="http://www.facebook.com/PhonepartsUSA" target="_blank"><img src="image/data/footer-images/social-icon3.jpg" alt="phonepartsusa-facebook"></a></li>
           <li><a href="https://www.pinterest.com/PhonePartsUSA/" target="_blank"><img src="image/data/footer-images/social-icon4.jpg" alt="phonepartsusa-pinterest"></a></li>
           <li><a href="http://twitter.com/PhonePartsUSA" target="_blank"><img src="image/data/footer-images/social-icon5.jpg" alt="phonepartsusa-twitter"></a></li>
       </ul>
   </div>
   <div class="web-promise">
      <h3>The PhonePartsUSA Promise</h3>	
      <p>If you ever have problems with your Order, we'll do everything we can to make it right.</p>
      <p>As a small company (32 of us total) we aim to treat you like family. We're in business because of you.</p>
  </div>
  <div style="min-height: 100px; overflow: hidden;" class="shopperapproved_widget sa_rotate sa_vertical sa_count1 sa_rounded sa_colorBlack sa_borderGray sa_bgWhite sa_showdate sa_jMY sa_narrow"></div><script type="text/javascript">var sa_interval = 5000;function saLoadScript(src) { var js = window.document.createElement('script'); js.src = src; js.type = 'text/javascript'; document.getElementsByTagName("head")[0].appendChild(js); } if (typeof(shopper_first) == 'undefined') saLoadScript('//www.shopperapproved.com/widgets/testimonial/12000.js'); shopper_first = true; </script><div style="text-align:right;"><a href="http://www.shopperapproved.com/reviews/phonepartsusa.com/" target="_blank" rel="nofollow" onclick="return sa_openurl(this.href);"><img class="sa_widget_footer" alt="" src="https://www.shopperapproved.com/widgets/widgetfooter-darknarrow.png" style="border: 0;"></a></div>
  <ul class="adevtisement-list">
    <li><a href="http://www.stellaservice.com/profile/phonepartsusa.com/" target="_blank"><img src="image/data/footer-images/image03.jpg" alt="image03"></a></li>
    <li><a href="http://www.shopperapproved.com/reviews/phonepartsusa.com/" onclick="var nonwin=navigator.appName!='Microsoft Internet Explorer'?'yes':'no'; var certheight=screen.availHeight-90; window.open(this.href,'shopperapproved','location='+nonwin+',scrollbars=yes,width=620,height='+certheight+',menubar=no,toolbar=no'); return false;"><img alt="" oncontextmenu="var d = new Date(); alert('Copying Prohibited by Law - This image and all included logos are copyrighted by Shopper Approved \251 '+d.getFullYear()+'.'); return false;" src="https://c683207.ssl.cf2.rackcdn.com/12000-r.gif" style="border: 0" /></a></li>
    <li><a href="http://www.resellerratings.com/store/Phonepartsusa" target='_blank'><img src="image/data/footer-images/image01.jpg" alt="image01"></a></li>
    <li><a href="javascript:void(0);"><img src="image/data/footer-images/image04.jpg" alt="image04"></a></li>
</ul>
</div>
<div class="p-holder">
    <p>Copyright &copy; <?php echo date('Y');?> <a href="http://phonepartsusa.com" style="color:#d6d6d6">Phone Parts USA</a>. All Rights Reserved. PhonePartsUSA.com,LLC and its products are in no way endorsed, sponsored or affiliated with Apple, Google, HTC, Motorola, Blackberry(RIM), LG, Huawei, ZTE, Sony and Samsung or their subsidiaries.</p>
</div>
</div>
</div>
<?php $this->document->addScript('catalog/view/javascript/page_speed/jquery.viewport.min.js'); ?>
<?php if ($this->config->get('config_gts_status')) { ?>
<!-- BEGIN: Google Trusted Stores -->
<script type="text/javascript">
  var gts = gts || [];

  gts.push(["id", "<?php echo $this->config->get('config_gts_store_id');?>"]);
  gts.push(["badge_position", "<?php echo (($this->config->get('config_badge_position') == 1) ? 'BOTTOM_LEFT' : 'BOTTOM_RIGHT');?>"]);
  gts.push(["locale", "<?php echo $this->config->get('config_gts_locale');?>"]);
  <?php if (utf8_strlen($this->config->get('config_google_shopping_account_id')) > 0) { ?>
      gts.push(["google_base_subaccount_id", "<?php echo $this->config->get('config_google_shopping_account_id');?>"]);
      gts.push(["google_base_country", "<?php echo $this->config->get('config_google_shopping_country');?>"]);
      gts.push(["google_base_language", "<?php echo $this->config->get('config_google_shopping_language');?>"]);
      <?php } ?>	
      (function() {
       var gts = document.createElement("script");
       gts.type = "text/javascript";
       gts.async = true;
       gts.src = "https://www.googlecommerce.com/trustedstores/api/js";
       var s = document.getElementsByTagName("script")[0];
       s.parentNode.insertBefore(gts, s);
   })();
</script>
<!-- END: Google Trusted Stores -->
<?php } ?>

<script type="text/javascript">
var gr_goal_params = {
 param_0 : '',
 param_1 : '',
 param_2 : '',
 param_3 : '',
 param_4 : '',
 param_5 : ''
};</script>
<script type="text/javascript" src="https://app.getresponse.com/goals_log.js?p=668602&u=jgEp"></script>	
</body></html>
