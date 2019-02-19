<?php if (is_array($banners) && count($banners) > 0) {

	$slideshow_id = 'slideshow-'. rand();
?>

<div id="<?php echo $slideshow_id; ?>" class="module">
	<a href="<?php echo $banners[0]['link']; ?>" id="slideLink" style="border:0;">
		<img src="<?php echo $banners[0]['image']; ?>" style="width: 100%" />
	</a>
</div>

<script>
$(document).ready(function(){

	$("#<?php echo $slideshow_id; ?>").each(function(){
		var slideshow = this;

		var slides = [];
		<?php foreach ($banners as $banner) { ?>
			slides.push({
				'link': '<?php echo  html_entity_decode($banner['link']); ?>',
				'src': '<?php echo $banner['image'] ?>'
			});
		<?php } ?>

		var speed = 8888;
		var current_slide = 0;
		var slideIt = function()
		{
			if ( typeof slides[current_slide] == "undefined")
			{
				current_slide = 0;
			}
			$("a", slideshow).attr("href", slides[current_slide]['link']);
			$("img", slideshow).attr("src", slides[current_slide]['src']);
			current_slide++;
			setTimeout(function(){slideIt()}, speed);
		}

		slideIt();
	})

});
</script>
<?php } ?>