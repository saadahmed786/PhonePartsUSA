<div class="container-fluid banner_div">
	<div class="col-lg-3 search_banner_form">
		<h3>PART FINDER</h3>
		<form>
			<input type="text" value="Device brand" />
			<input type="text" value="Device Model" />
			<input type="text" value="Part Type" />
			<button>Search</button>
		</form>
	</div>
	<div class="col-lg-12 my_custome_slider">
		<div id="myCarousel" class="carousel slide" data-ride="carousel">
			<!-- Wrapper for slides -->
			<div class="carousel-inner" role="listbox">
				<?php foreach ($images as $i => $image) { ?>
				<div class="item <?= (!$i)? 'active': '';?>">
					<?= ($image['link'])? '<a href="' . $image['link'] . '">': '';?><img src="<?php echo $image['image']; ?>" alt="banner"><?= ($image['link'])? '</a>': '';?>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>