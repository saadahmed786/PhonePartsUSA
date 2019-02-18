<?php


?>
<div class="clearfix"></div>
<footer id="footer">
	<div class="container">
		<a href="#" id="back-to-top" title="Back to top"><i class="fa fa-arrow-up"></i></a>
		<nav class="ftr-menu">
			<ul class="clearfix">
			<li><a href="<?php echo HTTPS_SERVER;?>index.php?route=wholesale/wholesale">Business Account</a></li>
				<li><a href="<?php echo HTTPS_SERVER;?>index.php?route=information/contact">Customer Support</a></li>
				<!-- <li><a href="<?php echo $this->url->link('account/return/insert');?>">EZ Returns</a></li> -->
				<li><a href="<?php echo HTTPS_SERVER;?>index.php?route=buyback/buyback">lcd buy back</a></li>
				<li><a href="<?php echo HTTPS_SERVER;?>/index.php?route=information/information&information_id=3">Returns &amp; Warranty</a></li>
				<li><a href="<?php echo HTTPS_SERVER;?>/index.php?route=information/information&information_id=6">Shipping Rates</a></li>
				<li><a href="<?php echo HTTPS_SERVER;?>/index.php?route=information/sitemap">sitemap</a></li>
				<!-- <li><a href="#">rewards program</a></li> -->
				<li><a href="<?php echo HTTPS_SERVER;?>/index.php?route=information/information&information_id=5">Terms &amp; Conditions</a></li>
				<li><a href="https://phonepartsusa.bamboohr.com/jobs/" target="_blank">We're Hiring</a></li>
				
				<!-- <li><a href="http://blog.phonepartsusa.com/">blog</a></li> -->
				<!-- <li><a href="<?php echo HTTPS_SERVER;?>/privacy-policy">privacy police</a></li> -->
			</ul>
		</nav>
		<div class="row ftr-connet">
			<div class="col-md-4 never-miss">
				<h4>Never Miss a Sale</h4>
				<p>Daily sales &amp; weekly specials. We'll also send coupons for even further discounts</p>
				<div class="subscribe">
				<form id="footer-newsletter-form" accept-charset="utf-8" action="<?php echo HTTPS_SERVER.'subscribe_mailchimp.php';?>" method="post" target="_blank">
					<input type="email" name="email" class="input wf-input wf-req wf-valid__email" placeholder="E-mail...">
					<button class="btn btn-info light" type="submit">Subscribe <i class="fa fa-angle-right"></i></button>
					<!-- <input type="hidden" name="webform_id" value="9408002" /> -->
				</form>
			</div>
			</div>

			<div class="col-md-4 follow-us text-center">
				<h4>Our Logistics Partners</h4>
				<p><img src="catalog/view/theme/ppusa2.0/images/footer-icons/shipping.png" alt="Logistics Partners" title="Logistics Partners" style="width:300px"  /> </p>
				
			</div>

			<div class="col-md-4 follow-us text-center">
				<h4>Preffered Payment Methods</h4>
				<p>
				<img src="catalog/view/theme/ppusa2.0/images/footer-icons/payment.png" style="width:100%" alt="Payment Partners" title="Payment Partners"  />
				</p>
				<!-- <p style="font-size:30px"><i class="pw pw-visa"></i> <i class="pw pw-mastercard"></i> <i class="pw pw-american-express"></i> <i class="pw pw-paypal"></i> <i class="pw pw-discover"></i></p> -->
				
			</div>

			
			
			
		</div>
		
		<div class="copyrights">
			<p>Copyright &copy; <?php echo date('Y');?> PhonePartsUSA. All Rights Reserved. PhonePartsUSA.com,LLC and its products are in no way endorsed, sponsored or affiliated with Apple, Google, HTC, Motorola, Blackberry(RIM), LG, Huawei, ZTE, Sony and Samsung or their subsidiaries.</p>
		</div>
		<?php $this->document->addScript('catalog/view/javascript/page_speed/jquery.viewport.min.js'); ?>
		<?php if ($this->config->get('config_gts_status') && 1==2) { // disabling google review widget ?>

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

			<?php } ?>

			<script type="text/javascript">
				var gr_goal_params = {
					param_0 : '',
					param_1 : '',
					param_2 : '',
					param_3 : '',
					param_4 : '',
					param_5 : ''
				};
			</script>
		</div>
	</footer><!-- @End of footer -->
</div>

<script>
  window.fcWidget.init({
    token: "deef3d83-449b-4868-896a-3fe921c9e8f2",
    host: "https://wchat.freshchat.com"
  });
</script>
<?php
if($this->customer->getId())
{

?>
<script>
  // Make sure fcWidget.init is included before setting these values

  // To set unique user id in your system when it is available
  window.fcWidget.setExternalId("<?php echo $this->customer->getEmail();?>");

  // To set user name
  window.fcWidget.user.setFirstName("<?php echo $this->customer->getFirstName();?>");

  // To set user email
  window.fcWidget.user.setEmail("<?php echo $this->customer->getEmail();?>");

  // To set user properties
  /*window.fcWidget.user.setProperties({
    plan: "Estate",                 // meta property 1
    status: "Active"                // meta property 2
  });*/
</script>
<?php
}


?>
	
</body>
</html>