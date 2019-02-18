<div class="box">
  <div class="top"><img src="catalog/view/theme/default/image/logo_addthis.png" alt="" /><?php echo $heading_title; ?></div>
  <div class="middle">
	  <div class="addthis_toolbox addthis_default_style">
    <a class="addthis_button_twitter"></a>
    <a class="addthis_button_email"></a>
    <a class="addthis_button_facebook"></a>
    <a class="addthis_button_favorites"></a>
    <span class="addthis_separator">|</span>
    <a href="http://www.addthis.com/bookmark.php?v=250&amp;pub=<?php echo $addthis_username; ?>" class="addthis_button_expanded">More</a>
    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pub=<?php echo $addthis_username; ?>"></script>
    </div>
		<div style="padding-left;">
		<?php if ($addthis_twitter_username || $addthis_facebook_username) { ?>
		<h3 style="margin:10px 0 5px 0;"><?php echo $text_follow_us; ?></h3>
		<?php if ($addthis_twitter_username) { ?>
    <a href="http://twitter.com/<?php echo $addthis_twitter_username; ?>" target="_blank"><img src="catalog/view/theme/default/image/logo_twitter.jpg" alt="Twitter"/></a><br />
		<?php } ?>
		<?php if ($addthis_facebook_username) { ?>
    <a href="http://www.facebook.com/<?php echo $addthis_facebook_username; ?>" target="_blank"><img src="catalog/view/theme/default/image/logo_facebook.jpg" alt="Facebook"/></a>
		<?php } ?>
    <?php } ?>
		</div>
  </div>
  <div class="bottom">&nbsp;</div>
</div>
