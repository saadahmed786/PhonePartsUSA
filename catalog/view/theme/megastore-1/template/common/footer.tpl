<div id="footer">
   <?php $footerImg =  $this->config->get('megastore_options'); ?>
   <?php if($footerImg['footerImage']): ?>
   <div class="footer-image">
   
   	<ul>
    	<li><?php if($footerImg['footerImage']): ?>
        	<a href="<?php echo $footerImg['footerImgHref']?>"><img src="<?php echo HTTP_IMAGE . $footerImg['footerImage']; ?>" /></a>
        <?php endif;?></li>
        <li><?php if($footerImg['footerImage1']): ?>
        	<a href="<?php echo $footerImg['footerImgHref1']?>"><img src="<?php echo HTTP_IMAGE . $footerImg['footerImage1']; ?>" /></a>
        <?php endif;?></li>
        <li><?php if($footerImg['footerImage2']): ?>
        	<a href="<?php echo $footerImg['footerImgHref2']?>"><img src="<?php echo HTTP_IMAGE . $footerImg['footerImage2']; ?>" /></a>
        <?php endif;?></li>
    </ul>
   </div>
   <?php endif; ?>


  <?php if ($informations) { ?>
  <div class="column">
    <h3><?php echo $text_information; ?></h3>
    <ul>
      <?php foreach ($informations as $information) { ?>
      <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
      <?php } ?>
    </ul>
  </div>
  <?php } ?>
  <div class="column">
    <h3><?php echo $text_service; ?></h3>
    <ul>
      <li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
      <li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
      <li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
    </ul>
  </div>
  <div class="column">
    <h3><?php echo $text_extra; ?></h3>
    <ul>
      <li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
      <li><a href="<?php echo $voucher; ?>"><?php echo $text_voucher; ?></a></li>
      <li><a href="<?php echo $affiliate; ?>"><?php echo $text_affiliate; ?></a></li>
      <li><a href="<?php echo $special; ?>"><?php echo $text_special; ?></a></li>
    </ul>
  </div>
  <div class="column">
    <h3><?php echo $text_account; ?></h3>
    <ul>
      <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
      <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
      <li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
      <li><a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>
    </ul>
  </div>
  <div class="column">
    <h3><?php echo $this->config->get('config_name')?></h3>
    <ul>
        <li><?php echo $this->config->get('config_address'); ?></li>
        <li><?php echo $this->config->get('config_telephone'); ?></li>
    	<li><a href="mailto:<?php echo $this->config->get('config_email'); ?>"><?php echo $this->config->get('config_email'); ?></a></li>
    </ul>
  </div>
</div>
<!--
OpenCart is open source software and you are free to remove the powered by OpenCart if you want, but its generally accepted practise to make a small donation.
Please donate via PayPal to donate@opencart.com
//-->
<div id="powered">
<?php echo $powered; ?>
<p class="credits">Theme by <a href="http://themeforest.net/user/raviG/portfolio?ref=raviG">raviG</a></p>
<div class="clear"></div>
</div>
<!--
OpenCart is open source software and you are free to remove the powered by OpenCart if you want, but its generally accepted practise to make a small donation.
Please donate via PayPal to donate@opencart.com
//-->
</div>
<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
</body></html>