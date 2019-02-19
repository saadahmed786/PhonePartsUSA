<div class="clearfix"></div>
<footer id="footer">
	<div class="container">
		<a href="#" id="back-to-top" title="Back to top"><i class="fa fa-arrow-up"></i></a>
		<nav class="ftr-menu">
			<ul class="clearfix">
				<li><a href="<?php echo HTTPS_SERVER;?>index.php?route=information/contact">Customer Support</a></li>
				<li><a href="<?php echo HTTPS_SERVER;?>shipping-information">Shipping Rates</a></li>
				<li><a href="<?php echo HTTPS_SERVER;?>returnpolicy">Returns &amp; Exchanges</a></li>
				<li><a href="<?php echo HTTPS_SERVER;?>index.php?route=buyback/buyback">lcd buy back</a></li>
				<li><a href="#">rewards program</a></li>
				<li><a href="<?php echo HTTPS_SERVER;?>index.php?route=wholesale/wholesale">whole sale</a></li>
				<li><a href="http://blog.phonepartsusa.com/">blog</a></li>
				<li><a href="<?php echo HTTPS_SERVER;?>/privacy-policy">privacy police</a></li>
				<li><a href="">sitemap</a></li>
			</ul>
		</nav>
		<div class="row ftr-connet">
			<div class="col-md-3 never-miss">
				<h4>Never Miss a Sale</h4>
				<p>Daily sales &amp; weekly specials. We'll also send coupons for even further discounts</p>
			</div>

			<div class="col-md-4 subscribe">
				<form id="footer-newsletter-form" accept-charset="utf-8" action="https://app.getresponse.com/add_contact_webform.html?u=jgEp" method="post">
					<input type="email" name="email" class="input wf-input wf-req wf-valid__email" placeholder="E-mail...">
					<button class="btn btn-info light" type="submit">Subscribe <i class="fa fa-angle-right"></i></button>
					<input type="hidden" name="webform_id" value="9408002" />
				</form>
			</div>
			<div class="col-md-3 follow-us">
				<h4>Follow Us On Social Media</h4>
				<ul class="social-shares text-left">
					<li class="twitter">
						<a href="http://twitter.com/PhonePartsUSA" target="_blank"><i class="fa fa-twitter"></i></a>
					</li>
					<li class="play">
						<a href="http://www.youtube.com/user/phonepartsusa" target="_blank"><i class="fa fa-play"></i></a>
					</li>
					<li class="insta">
						<a href="http://instagram.com/phonepartsusa" target="_blank">
							<img src="image/new_site/icons/instagram.png" alt="">
						</a>
					</li>
					<li class="pinterest">
						<a href="https://www.pinterest.com/PhonePartsUSA/" target="_blank"><i class="fa fa-pinterest"></i></a>
					</li>
					<li class="facebook">
						<a href="http://www.facebook.com/PhonepartsUSA" target="_blank"><i class="fa fa-facebook"></i></a>
					</li>
					<li class="google-plus">
						<a href="#"><img src="image/new_site/icons/googleplus.png" alt=""></a>
					</li>
					<li class="rss">
						<a href="#"><i class="fa fa-rss"></i></a>
					</li>
				</ul>
			</div>
			<div class="col-md-2 ftr-settings">
				<a href="javascript:void(0);"><img src="image/new_site/ftr-setting.png" alt=""></a>
			</div>
		</div>
		<div class="ftr-media">
			<ul>
				<li><a href="javascript:void(0);"><img src="image/new_site/google-trusted.jpg" alt=""></a></li>
				<li><a href="http://www.stellaservice.com/profile/phonepartsusa.com/"><img src="image/new_site/stella.png" alt=""></a></li>
				<li><a href="javascript:void(0);"><img src="image/new_site/paypal.png" alt=""></a></li>
			</ul>
		</div>
		<div class="copyrights">
			<p>Copyright &copy; <?php echo date('Y');?> PhonePartsUSA. All Rights Reserved. PhonePartsUSA.com,LLC and its products are in no way endorsed, sponsored or affiliated with Apple, Google, HTC, Motorola, Blackberry(RIM), LG, Huawei, ZTE, Sony and Samsung or their subsidiaries.</p>
		</div>
		<?php $this->document->addScript('catalog/view/javascript/page_speed/jquery.viewport.min.js'); ?>
		<?php if ($this->config->get('config_gts_status')) { ?>

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
</body>
</html>