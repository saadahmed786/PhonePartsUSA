<div class="slideshow">
	<div id="bossslideshow<?php echo $module; ?>" class="flexslider">
		<ul class="slides">
		<?php foreach ($images as $image) { ?>
			<li>
				<?php if ($image['link']) { ?>
				<a href="<?php echo $image['link']; ?>"><img src="<?php echo $image['image']; ?>" title="image" alt="image" /></a>
				<?php } else { ?>
				<img src="<?php echo $image['image']; ?>" title="image" alt="image" />
				<?php } ?>
				<p class="flex-caption"><?php echo $image['description']; ?></p>
			</li>
		<?php } ?>
		</ul>
	</div>
</div>

<?php if (file_exists('catalog/view/theme/bt_optronics/stylesheet/boss_slideshow.css')) {
		echo '<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/boss_slideshow.css" media="screen" />';
	} else {
		echo '<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/boss_slideshow.css" media="screen" />';
	}
?>
<script type="text/javascript" src="catalog/view/javascript/bossthemes/jquery.flexslider.js"></script>
<script type="text/javascript" src="catalog/view/javascript/bossthemes/jquery.easing.js"></script>
<script type="text/javascript">
	$(window).load(function(){
	  $('#bossslideshow<?php echo $module; ?>').flexslider({
		animation: "slide",
		slideshowSpeed: 4000,
		animationSpeed: 600
		//start: function(slider){
		 // $('body').removeClass('loading');
		//}
	  });
	});
 </script>