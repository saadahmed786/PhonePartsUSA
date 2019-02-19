<div class="slideshow">
    <div class="flexslider" style="height: <?php echo $height; ?>px;">
          <ul class="slides">
            <?php foreach ($banners as $banner) { ?>
                <?php if ($banner['link']) { ?>
                    <li><a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" /></a></li>
                <?php } else { ?>
                    <li><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" /></li>
                <?php } ?>
            <?php } ?>
          </ul>  
    </div> 
</div>
<ul class="slideNav">
	<?php foreach ($banners as $banner) : ?>
        <li><a href="javscript:void(0);" class="bannerTitle"><?php echo $banner['title']; ?></a></li>
    <?php endforeach; ?>
</ul> 
  
<script type="text/javascript"><!--
     $(window).load(function() {
		  $('.slideshow .flexslider').flexslider({
			animation: slideAnim,
			slideshowSpeed: slideSpeed,
			directionNav: true, 
			manualControls: '.slideNav li',
			controlsContainer: '.flexslider',
			touch: true,
			pauseOnHover: true,
		  });
	  });
--></script>