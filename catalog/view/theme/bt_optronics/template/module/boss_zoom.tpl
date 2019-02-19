<?php if($status){ ?>
<div id="boss_zoom">
<div class="bosszoomtoolbox" >
	<?php if ($thumb) { ?>
	<div class="image"><a href="<?php echo $popup; ?>" title="<?php echo $heading_title; ?>" class="cloud-zoom" id='zoom1' rel="adjustX: <?php echo $adjustX; ?>, adjustY:<?php echo $adjustY; ?>, tint:'<?php echo $tint; ?>', tintOpacity:<?php echo $tintOpacity; ?>, softFocus:<?php echo $softfocus; ?>, lensOpacity:<?php echo $lensOpacity; ?>,  <?php if($zoom_area_width){ ?>zoomWidth:<?php echo $zoom_area_width; ?>, <?php } if($zoom_area_heigth){ ?> zoomHeight:<?php echo $zoom_area_heigth; ?>, <?php } ?> position:'<?php echo $position_zoom_area; ?>', showTitle:<?php echo $title_image; ?>, titleOpacity:<?php echo $title_opacity; ?>, smoothMove:'<?php echo $smoothMove; ?>'">
		<img src="<?php echo $thumb; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" id="image" /></a>
		<br>
		<span class="text-zoom">Hover | <a href="<?php echo $popup; ?>" title="<?php echo $heading_title; ?>" class="colorbox" rel="colorbox">Large</a></span>
	</div>
	<?php } ?>
	<?php if ($images) { ?>
	<div class="image-additional a_bossthemes">
		<div class="es-carousel">
			<ul  class="skin-opencart">
			<?php foreach ($images as $image) { ?>
			<li><div class="boss-image-add"><a href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>" class="cloud-zoom-gallery" rel="useZoom: 'zoom1', smallImage: '<?php echo $image['thumb']; ?>' "><img src="<?php echo $image['addition']; ?>"  title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a></div></li>
			<?php } ?>
			</ul>
		</div>
	</div>
	<?php foreach ($images as $image) { ?>
		<a href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>" class="colorbox" rel="colorbox"></a>
	<?php } ?>
	<?php } ?>
</div>
</div>
<script type="text/javascript"><!--
$(document).ready(function() {
	if (jQuery("#boss_zoom").html()) {
		jQuery(".product-info .left").html(jQuery("#boss_zoom").html());
		jQuery("#boss_zoom").remove();
		jQuery('.colorbox').colorbox({
			overlayClose: true,
			opacity: 0.5,
			slideshow:true
		});
	}
});
//--></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/cloud-zoom/cloud-zoom.1.0.3.js"></script>
<?php } ?>