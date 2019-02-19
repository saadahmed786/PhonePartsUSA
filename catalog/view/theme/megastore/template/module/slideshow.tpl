	<!-- Slideshow -->
	
	<?php $margin = false; if($width>940) { $margin = ' style="position:relative;margin-left:-'.ceil(($width-940)/2).'px"'; } ?>

	<div class="flexlider" id="slideshow<?php echo $module; ?>"<?php echo $margin; ?>>
		
		<ul class="slides">
			
			<?php foreach ($banners as $banner) { ?>
    		<?php if ($banner['link']) { ?>
    		<li><a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" /></a></li>
    		<?php } else { ?>
    		<li><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" /></li>
    		<?php } ?>
    		<?php } ?>
			
		</ul>
		
		<?php $speed = 4000; if($this->config->get('slideshow_speed') > 0 && $this->config->get('general_status') == '1') { $speed = $this->config->get('slideshow_speed')*1000; } ?>
		
		<script type="text/javascript">
			$(window).load(function() {
				$('#slideshow<?php echo $module; ?>').flexslider({slideshowSpeed: <?php echo $speed; ?>});
			});
		</script>

	</div>
	
	<!-- End slideshow -->
