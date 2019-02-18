<div class="product_carousal">
	<div class="container">
		<div class="row">
			<div class="col-md-11">
				<h3><?= $heading_title; ?></h3>
			</div>
			<div class="col-md-1">
				<!-- Controls -->
				<div class="controls pull-left hidden-xs">
					<a class="left fa fa-chevron-left btn btn-success" href="#carousel-example"
					data-slide="prev"></a><a class="right fa fa-chevron-right btn btn-success" href="#carousel-example"
					data-slide="next"></a>
				</div>
			</div>
		</div>
		<div id="carousel-example" class="carousel slide hidden-xs" data-ride="carousel">
			<!-- Wrapper for slides -->
			<div class="carousel-inner">
				<div class="item active">
					<div class="row">
						<?php foreach ($products as $i => $product) { ?>
						<!-- Escape if Products Get more than 4 -->
						<?php if (!($i%6) && $i > 0) { ?>
					</div>
				</div>
				<div class="item">
					<div class="row">
						<?php } ?>
						<div class="col-sm-2">
							<div class="col-item">
								<div class="photo">
									<a href="<?= $product['href']; ?>"><img src="<?= $product['thumb']; ?>" class="img-responsive" alt="<?= $product['name']; ?>" /></a>
								</div>
								<div class="info">
									<div class="row">
										<div class="price col-md-12">
											<h5><a href="<?= $product['href']; ?>"><?= $product['name']; ?></a></h5>
											<h6 class="price-text-color"><?= $product['price']?> 
												<span style="color: #ffcd0e; font-size: 12px;">
													<?php for ($x=0; $x < 5 ; $x++) { ?>
													<i class="fa <?= ($x < $product['rating'])? 'fa-star': 'fa-star-o'; ?>"></i>
													<?php } ?>
												</span>
											</h6>
										</div>
									</div>
									<div class="clearfix">
									</div>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<a href="#" class="see_more_carousal">See all your recently viewed items</a>
	</div>
</div>