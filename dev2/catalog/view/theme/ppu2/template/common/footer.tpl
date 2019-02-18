<?php //echo '<pre>'; print_r($data); exit; ?>
<div class="footer">
	<div class="custome_footer_div">
		<div class="col-lg-2 footer_links"><img src="catalog/view/theme/ppu2/image/footer-logo.png" alt="ppusa" />
			<ul>
				<li><a href="<?php echo HTTPS_SERVER;?>index.php?route=information/contact">Customer Support</a></li>
				<li><a href="<?php echo HTTPS_SERVER;?>shipping-information">Shipping Rates</a></li>
				<li><a href="<?php echo HTTPS_SERVER;?>returns-or-exchanges">Returns & Exchanges</a></li>
				<li><a href="<?php echo HTTPS_SERVER;?>warranty">Warranty</a></li>
				<li><a href="<?php echo HTTPS_SERVER;?>index.php?route=account/return/insert">Return Items</a></li>
				<li><a href="<?php echo HTTPS_SERVER;?>index.php?route=account/faq">F.A.Q.</a></li>
				<li><a href="<?php echo HTTPS_SERVER;?>index.php?route=wholesale/wholesale">Business Accounts</a></li>
				<li><a href="<?php echo HTTPS_SERVER;?>index.php?route=buyback/buyback">LCD Buy Back</a></li>
				<li><a href="http://blog.phonepartsusa.com/">Blog</a></li>
				<li><a href="<?php echo HTTPS_SERVER;?>privacy-policy">Privacy Policy</a></li>
			</ul>
		</div>
		<div class="col-lg-4 socail_media">
			<h4>Never Miss a Sale</h4>
			<p>Daily sales & weekly specials. We'll also send coupons for even further discounts</p>
			<form id="footer-newsletter-form" accept-charset="utf-8" action="https://app.getresponse.com/add_contact_webform.html?u=jgEp" method="post">
				<input type="email" name="email" placeholder="E-mail Address" /> 
				<button onclick="$('#footer-newsletter-form').submit();">Subscribe</button>
			</form>
			<h5>Follow Us On Social Media</h5>
			<div class="social_media_icon">
				<a href="http://www.facebook.com/PhonepartsUSA" target="_blank"><span class="facebook"><i class="fa fa-facebook"></i></span></a>
				<a href="http://www.youtube.com/user/phonepartsusa" target="_blank"><span class="youtube"><i class="fa fa-youtube"></i></span></a>
				<a href="http://instagram.com/phonepartsusa" target="_blank"><span class="instagram"><i class="fa fa-instagram"></i></span></a>
				<a href="https://www.pinterest.com/PhonePartsUSA/" target="_blank"><span class="pinterest"><i class="fa fa-pinterest"></i></span></a>
				<a href="http://twitter.com/PhonePartsUSA" target="_blank"><span class="twitter"><i class="fa fa-twitter"></i></span></a>
			</div>
		</div>
		<div class="col-lg-4 google_widget">
			<img src="catalog/view/theme/ppu2/image/google_widget.jpg" alt="Google" />
		</div>
		<div class="col-lg-2 badges_div">
			<img src="catalog/view/theme/ppu2/image/badge_1.png" alt="badge" />
			<img src="catalog/view/theme/ppu2/image/badge_2.png" alt="badge" />
			<img src="catalog/view/theme/ppu2/image/badge_3.png" alt="badge" />
			<img src="catalog/view/theme/ppu2/image/badge_4.png" alt="badge" />
		</div>
		<div class="clearfix"></div>
		<div class="col-lg-12 bottom_last_text">
			<p>Copyright &copy; <?php echo date('Y');?> <a href="http://phonepartsusa.com" style="color:#d6d6d6">Phone Parts USA</a>. All Rights Reserved. PhonePartsUSA.com,LLC and its products are in no way endorsed, sponsored or affiliated with Apple, Google, HTC, Motorola, Blackberry(RIM), LG, Huawei, ZTE, Sony and Samsung or their subsidiaries.</p>
		</div>
	</div>
</div>
</body>
</html>