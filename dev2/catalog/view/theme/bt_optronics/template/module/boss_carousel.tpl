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
	
	if(getWidthBrowser() > 959){
		$('#boss_carousel<?php echo $module; ?>').elastislide({
			imageW 		: 124,
			current		: 0,
			border		: 0,
			margin		: 0,
			minItems	: 2,
			onClick 	: true,
			disable_touch		: false
		});	
	}else if(getWidthBrowser() > 766 && getWidthBrowser() < 960) {
		$('#boss_carousel<?php echo $module; ?>').elastislide({
			imageW 		: 130,
			current		: 0,
			border		: 0,
			margin		: 2,
			minItems	: 2,
			onClick 	: true,
			disable_touch		: false
		});	
	}else if(getWidthBrowser() > 479 && getWidthBrowser() < 767){
		$('#boss_carousel<?php echo $module; ?>').elastislide({
			imageW 		: 120,
			current		: 0,
			border		: 0,
			margin		: 3,
			minItems	: 2,
			onClick 	: true,
			disable_touch		: false
		});	
	}else{
		$('#boss_carousel<?php echo $module; ?>').elastislide({
			imageW 		: 124,
			current		: 0,
			border		: 0,
			margin		: 0,
			minItems	: 2,
			onClick 	: true,
			disable_touch		: false
		});	
	}
	
</script>