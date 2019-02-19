<div id="boss_carousel<?php echo $module; ?>" class="b-es-carousel-wrapper boss-carousel">
	<div class="b-es-carousel">
		<ul class="jcarousel-skin-opencart">
		<?php foreach ($banners as $banner) { ?>
			<li><a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" title="<?php echo $banner['title']; ?>" /></a></li>
		<?php } ?>
		<?php foreach ($banners as $banner) { ?>
			<li><a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" title="<?php echo $banner['title']; ?>" /></a></li>
		<?php } ?>
		</ul>
	</div>
</div>

<script type="text/javascript">	
	$('#boss_carousel<?php echo $module; ?>').elastislide({
		imageW 		: 145,
		current		: 0,
		border		: 0,
		margin		: 3,
		minItems	: 2,
		onClick 	: true,
		disable_touch		: false
	});	
</script>