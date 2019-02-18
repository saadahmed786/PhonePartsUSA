<?php //echo '<pre>'; print_r($menus); exit;?>
<?php echo $header; ?>
<div class="container-fluid">
	<div class="container content_main_div">
		<!-- Printing Left Column -->
		<?php echo $column_left; ?>

		<div class="col-lg-9 right_bar_area">
			<?php if ($home_products1) { ?>
			<div class="interested_product_div">
				<h2>Weekly Specials</h2>
				<h4>Take a look at special proucts of this week.</h4>
				<div class="row interested_carousal_nav">
					<div class="col-md-12">
						<div class="controls pull-right hidden-xs">
							<a class="left" href="#carousel-example-generic" data-slide="prev"></a>
							<a class="right" href="#carousel-example-generic" data-slide="next"></a>
						</div>
					</div>
				</div>
				<div id="carousel-example-generic" class="carousel slide hidden-xs" data-ride="carousel">
					<!-- Wrapper for slides -->
					<div class="carousel-inner">
						<div class="item active">
							<div class="row">

								<?php foreach ($home_products1 as $i => $product) { ?>
								<!-- Escape if Products Get more than 4 -->
								<?php if (!($i%4) && $i > 0) { ?>
							</div>
						</div>
						<div class="item">
							<div class="row">
								<?php } ?>

								<div class="col-sm-3">
									<div class="col-item">
										<div class="photo">
											<a href="<?= $product['href']; ?>"><img src="<?= $product['thumb']; ?>" class="img-responsive" alt="<?= $product['name']; ?>" /></a>
										</div>
										<a href="<?= $product['href']; ?>"><?= $product['name']; ?></a>
										<p><?= $product['price']?> 
											<span style="color: #ffcd0e; font-size: 12px;">
												<?php for ($x=0; $x < 5 ; $x++) { ?>
												<i class="fa <?= ($x < $product['rating'])? 'fa-star': 'fa-star-o'; ?>"></i>
												<?php } ?>
											</span>
										</p>
									</div>
								</div>
								<?php } ?>

							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
			<div class="popular_device_div">
				<h2>Popular devices</h2>
				<h4>Take a look at our Popular Devices</h4>
				<?php
				$pHeader = array();
				$pRows = array();
				foreach ($menus as $i => $make) {
					$line = array( 'Apple', 'Samsung', 'Blackberry', 'HTC', 'LG' );
					foreach ($line as $i => $value) {
						if (strtolower($make['title']) == strtolower($value)) {
							$pHeader[$i] = $make['title'];
							foreach ($make['populer'] as $key => $device) {
								$pRows[$key][$i] = '<a href="' . $device['href'] . '">' . $device['name'] . '</a>';
							}
						}
					}
				}
				ksort($pHeader);
				?>
				<table cellpadding="0" cellspacing="0">
					<tr>
						<th><?= implode('</th><th>', $pHeader); ?></th>
					</tr>
					<tr>
						<td style="border:none;" colspan="5"></td>
					</tr>
					<?php foreach ($pRows as $pRow) { ?>
					<?php ksort($pRow); ?>
					<tr>
						<td>
							<?= implode('</td><td>', $pRow)?>
						</td>
					</tr>
					<?php } ?>
				</table>
			</div>
		</div>
		<div class="clearfix"></div>	
		<?php echo $content_top; ?>
	</div>
</div>
<?php echo $content_bottom; ?>
<?php //echo $column_right; ?>
<?php echo $footer; ?>