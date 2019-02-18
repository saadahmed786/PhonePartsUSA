
	<p class="clear" style="height:20px"></p>
	
	<!-- Footer -->

	<div id="footer">
	
	<?php 
	
	$customfooter = $this->config->get('customfooter');
	
	if(isset($customfooter[$language_id])) { 
	
		if($customfooter[$language_id]['twitter_status'] == '1' || $customfooter[$language_id]['contact_status'] == '1' || $customfooter[$language_id]['aboutus_status'] == '1' || $customfooter[$language_id]['facebook_status'] == '1') {
		
$grids = 12; $test = 0;  if($customfooter[$language_id]['contact_status'] == '1' || $customfooter[$language_id]['aboutus_status'] == '1') { $test++; } if($customfooter[$language_id]['twitter_status'] == '1') { $test++; } if($customfooter[$language_id]['facebook_status'] == '1') { $test++; } $grids = 12/$test;
	
	?>	
		
		<!-- Separator --><p class="border"></p>
		
		<!-- Footer top outside -->
		
		<div class="footer-top-outside set-size">
		
			<?php if($customfooter[$language_id]['contact_status'] == '1' || $customfooter[$language_id]['aboutus_status'] == '1') { ?>
		
			<!-- About Us -->
			
			<div class="grid-<?php echo $grids; ?> float-left">
				
				<?php if($customfooter[$language_id]['aboutus_status'] == '1') { ?>
				
				<?php if($customfooter[$language_id]['aboutus_title'] != '') { ?>
				<h2><?php echo $customfooter[$language_id]['aboutus_title']; ?></h2>
				<?php } ?>
				
				<p>
				
					<?php echo html_entity_decode($customfooter[$language_id]['aboutus_text']); ?>
					
				</p>
				
				<?php } ?>
				
				<?php if($customfooter[$language_id]['contact_status'] == '1') { ?>
				
				<ul id="contact-us">

					<li>

						<?php if($customfooter[$language_id]['contact_phone'] != '') { ?>
						<ul id="tel"><li><?php echo $customfooter[$language_id]['contact_phone']; ?></li></ul>
						<?php } ?>
						<?php if($customfooter[$language_id]['contact_email'] != '') { ?>
						<ul id="mail"><li><?php echo $customfooter[$language_id]['contact_email']; ?></li></ul>
						<?php } ?>
						<?php if($customfooter[$language_id]['contact_skype'] != '') { ?>
						<ul id="skype"><li><?php echo $customfooter[$language_id]['contact_skype']; ?></li></ul>
						<?php } ?>

					</li>

				</ul>
				
				<?php } ?>
			
			</div>
			
			<!-- End About Us -->
			
			<?php } ?>
			
			<?php if($customfooter[$language_id]['twitter_status'] == '1') { ?>
			
			<!-- Twitter -->
			
			<div class="grid-<?php echo $grids; ?> float-left">
			
				<!-- ***** TWITTER API INTEGRATION STARTS HERE ***** -->
				<script type="text/javascript">
					jQuery(function($){
						$(".tweet").tweet({
							username: "<?php echo $customfooter[$language_id]['twitter_profile'] ; ?>",
							join_text: "auto",
							avatar_size: 0,
							count: 3,
							auto_join_text_default: "<b>:</b>",
							auto_join_text_ed: "<b>:</b>",
							auto_join_text_ing: "<b>:</b>",
							auto_join_text_reply: "<b>:</b>",
							auto_join_text_url: "<b>:</b>",
							loading_text: "Loading tweets..."
						});
					});
				</script> 
				<!-- ***** TWITTER API INTEGRATION ENDS HERE ***** -->
			
				<h2>Twitter</h2>
			
				<div id="twitter-updates"><div class="tweet"></div></div>
							
			</div>
			
			<!-- End twitter -->
			
			<?php } ?>
			
			<?php if($customfooter[$language_id]['facebook_status'] == '1') { ?>
			
			<!-- Facebook -->
			
			<div class="grid-<?php echo $grids; ?> float-left" id="facebook">
				
				<?php $facebook_css = ''.HTTP_SERVER.'catalog/view/theme/megastore/stylesheet/facebook.css.php';?>
							
				<h2>Facebook</h2>
				
				<!-- Facebook -->
							
				<fb:fan profile_id="<?php echo $customfooter[$language_id]['facebook_id']; ?>" stream="0" connections="8" logobar="0" height="180px" width="300px" css="<?php echo $facebook_css; ?>"></fb:fan>
				
				<!-- End Facebook -->
						
			
			</div>
			
			<!-- End facebook -->
			
			<?php } ?>
		
			<p class="clear"></p>
		
		</div>
		
		<!-- End footer top outside -->
		
		<?php } } ?>
		
		<!-- Separator --><p class="border"></p>
		
		<!-- Footer Navigation -->
		
		<div class="set-size footer-navigation">
				
			<?php if ($informations) { ?>
		    <div class="grid-3 float-left">
				
		      <h3><?php echo $text_information; ?></h3>

		      <ul class="no-active">

			      <?php foreach ($informations as $information) { ?>
			      <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
			      <?php } ?>
			
		      </ul>

		    </div>
			 <?php } ?>
			 
		    <div class="grid-3 float-left">

		      <h3><?php echo $text_service; ?></h3>

		      <ul class="no-active">

			      <li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
			      <li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
			      <li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>

		      </ul>

		    </div>

		    <div class="grid-3 float-left">

		      <h3><?php echo $text_extra; ?></h3>

		      <ul class="no-active">

			      <li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
			      <li><a href="<?php echo $voucher; ?>"><?php echo $text_voucher; ?></a></li>
			      <li><a href="<?php echo $affiliate; ?>"><?php echo $text_affiliate; ?></a></li>
			      <li><a href="<?php echo $special; ?>"><?php echo $text_special; ?></a></li>
				 
		      </ul>

		    </div>

		    <div class="grid-3 float-left">

				<h3><?php echo $text_account; ?></h3>

		    	<ul class="no-active">
		    	
			      <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
			      <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
			      <li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
			      <li><a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>

		    	</ul>

			</div>
			
			<p class="clear"></p>
		
		</div>
		
		<!-- End footer navigation -->
		
		<!-- Separator --><p class="border"></p>
		
		<!-- Copyright -->
		
		<div class="copyright set-size">
			
			<?php if($this->config->get('payment_status') != '0') { ?>
			<ul>
				
				<?php if($this->config->get('payment_mastercard_status') != '0') { ?>
				<?php if($this->config->get('payment_mastercard') != '') { ?>
				<li><img src="<?php echo $this->config->get('payment_mastercard'); ?>" alt="Mastercard" /></li>
				<?php } else { ?>
				<li><img src="catalog/view/theme/megastore/images/mastercard.png" alt="Mastercard" /></li>
				<?php } ?>
				<?php } ?>
				<?php if($this->config->get('payment_visa_status') != '0') { ?>
				<?php if($this->config->get('payment_visa') != '') { ?>
				<li><img src="<?php echo $this->config->get('payment_visa'); ?>" alt="Visa" /></li>
				<?php } else { ?>
				<li><img src="catalog/view/theme/megastore/images/visa.png" alt="Visa" /></li>
				<?php } ?>
				<?php } ?>
				<?php if($this->config->get('payment_moneybookers_status') != '0') { ?>
				<?php if($this->config->get('payment_moneybookers') != '') { ?>
				<li><img src="<?php echo $this->config->get('payment_moneybookers'); ?>" alt="MoneyBookers" /></li>
				<?php } else { ?>
				<li><img src="catalog/view/theme/megastore/images/moneybookers.png" alt="MoneyBookers" /></li>
				<?php } ?>
				<?php } ?>
				<?php if($this->config->get('payment_paypal_status') != '0') { ?>
				<?php if($this->config->get('payment_paypal') != '') { ?>
				<li><img src="<?php echo $this->config->get('payment_paypal'); ?>" alt="Paypal" /></li>
				<?php } else { ?>
				<li><img src="catalog/view/theme/megastore/images/paypal.png" alt="PayPal" /></li>
				<?php } ?>
				<?php } ?>
			
			</ul>
			<?php } ?>
			
			<p><?php echo $powered; ?></p>
			
			<div class="clear"></div>
		
		</div>
		
		<!-- End copyright -->

	</div>

	<!-- End footer -->
	
</div>

</body>
</html>
