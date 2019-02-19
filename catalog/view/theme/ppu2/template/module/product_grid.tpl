<?php foreach ($categories as $key => $category) { ?>
<?php if (!$key % 2) { ?>
<div class="mobile_product_area">
	<div class="col-lg-4">
		<?php foreach ($category['products'] as $pk => $product) { ?>
		<?php if (!$pk % 2) { ?>
		<div class="small_product_div">
			<div class="sm_pd_img_div">
				<img src="<?= $product['thumb']; ?>" alt="<?= substr($product['name'], 0, 30); ?>" />
			</div>
			<div class="sm_pd_txt_div">
				<h3><a href="<?= $product['href']; ?>"><?= substr($product['name'], 0, 30); ?></a></h3>
				<p><a href="<?= $product['href']; ?>"><?= substr($product['short_description'], 0, 50); ?></a></p>
				<span><?= $product['price']; ?></span>
			</div>
			<div class="clearfix"></div>
		</div>
		<?php } else { ?>
		<div class="small_product_div margin_top_default">
			<div class="sm_pd_txt_div">
				<h3><a href="<?= $product['href']; ?>"><?= substr($product['name'], 0, 30); ?></a></h3>
				<p><a href="<?= $product['href']; ?>"><?= substr($product['short_description'], 0, 50); ?></a></p>
				<span><?= $product['price']; ?></span>
			</div>
			<div class="sm_pd_img_div sm-pd_right">
				<img src="<?= $product['thumb']; ?>" alt="<?= substr($product['name'], 0, 30); ?>" />
			</div>
			<div class="clearfix"></div>
		</div>
		<?php } } ?>
	</div>
	<div class="col-lg-8">
		<div class="big_product_div">
			<div class="big_pd_img_div">
				<img src="<?= $category['image']; ?>" alt="<?= $category['name']; ?>" />
			</div>
			<div class="big_pd_txt_div">
				<h3>
					<a href="<?= $category['href']; ?>"><?= $category['name']; ?></a>
				</h3>
				<p><?= $category['description']; ?></p>
						<!-- <ul>
							<li><a href="javascript:void(0)">Chargers</a></li>
							<li><a href="javascript:void(0)">Cases</a></li>
							<li><a href="javascript:void(0)">LCD Screens</a></li>
							<li><a href="javascript:void(0)">Battery</a></li>
							<li><a href="javascript:void(0)">Components & Modules</a></li>
						</ul> -->
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
		<?php } else { ?>
		<div class="mobile_product_area_2">
			<div class="col-lg-8">
				<div class="big_product_div">
					<div class="big_pd_img_div">
					<img src="<?= $category['image']; ?>" alt="<?= $category['name']; ?>" />
					</div>
					<div class="big_pd_txt_div">
						<h3><a href="<?= $category['href']; ?>"><?= $category['name']; ?></a></h3>
						<p><?= $category['description']; ?></p>
						<!-- <ul>
							<li><a href="javascript:void(0)">Chargers</a></li>
							<li><a href="javascript:void(0)">Cases</a></li>
							<li><a href="javascript:void(0)">LCD Screens</a></li>
							<li><a href="javascript:void(0)">Battery</a></li>
							<li><a href="javascript:void(0)">Components & Modules</a></li>
						</ul> -->
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
			<div class="col-lg-4">
				<?php foreach ($category['products'] as $pk => $product) { ?>
				<?php if (!$pk % 2) { ?>
				<div class="small_product_div">
					<div class="sm_pd_img_div">
						<img src="<?= $product['thumb']; ?>" alt="<?= substr($product['name'], 0, 30); ?>" />
					</div>
					<div class="sm_pd_txt_div">
						<h3><a href="<?= $product['href']; ?>"><?= substr($product['name'], 0, 30); ?></a></h3>
						<p><a href="<?= $product['href']; ?>"><?= substr($product['short_description'], 0, 50); ?></a></p>
						<span><?= $product['price']; ?></span>
					</div>
					<div class="clearfix"></div>
				</div>
				<?php } else { ?>
				<div class="small_product_div margin_top_default">
					<div class="sm_pd_txt_div">
						<h3><a href="<?= $product['href']; ?>"><?= substr($product['name'], 0, 30); ?></a></h3>
						<p><a href="<?= $product['href']; ?>"><?= substr($product['short_description'], 0, 50); ?></a></p>
						<span><?= $product['price']; ?></span>
					</div>
					<div class="sm_pd_img_div sm-pd_right">
						<img src="<?= $product['thumb']; ?>" alt="<?= substr($product['name'], 0, 30); ?>" />
					</div>
					<div class="clearfix"></div>
				</div>
				<?php } } ?>
			</div>
			<div class="clearfix"></div>
		</div>
		<?php } }?>