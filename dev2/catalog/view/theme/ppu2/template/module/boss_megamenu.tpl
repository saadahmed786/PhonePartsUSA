<div class="brand_nav_div">
	<h3>ALL BRANDS</h3>
	<ul>
		<?php foreach ($menus as $key => $menu) { ?>
		<li><a href="<?= $menu['href']; ?>"><?= $menu['title']; ?></a></li>
		<?php } ?>
	</ul>
</div>
<div class="left_banner_div">
	<img src="catalog/view/theme/ppu2/image/left_banner.jpg" alt="banner" />
</div>