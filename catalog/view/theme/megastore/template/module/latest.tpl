
		<!-- Box -->
		
		<div class="box">
			
			<!-- Title -->
			
			<div class="box-heading"><?php echo $heading_title; ?></div>
			
			<!-- Content -->
			
			<div class="box-content">
			
					<!-- Products -->
					
					<div class="box-product<?php if($this->config->get('product_per_pow') == '1' && $this->config->get('general_status') == '1') { echo ' version-two'; } ?>">
					
					<?php foreach ($products as $product) {  ?>	
					<!-- Product -->
						
						<div>
							
							<!-- Hover PRODUCT -->
							
							<div class="absolute-hover-product">
								
								<?php if ($product['thumb']) { ?>
								<div class="image"><div class="banner-square"><a href="<?php echo $product['href']; ?>">
								<?php echo $product['promo_tag_top_right']; ?>
								<?php echo $product['promo_tag_top_left']; ?>
								<?php echo $product['promo_tag_bottom_left']; ?>
								<?php echo $product['promo_tag_bottom_right']; ?>
								<img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></div></a></div>
								<?php } ?>
								
								<div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
								
					        <?php if ($product['price']) { ?>
					        <div class="price">
					          <?php if (!$product['special']) { ?>
					          <?php echo $product['price']; ?>
					          <?php } else { ?>
					          <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
					          <?php } ?>
					        </div>
					        <?php } ?>
					        
					        <?php if ($product['rating']) { ?>
					        <div class="ratings"><img src="catalog/view/theme/megastore/images/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
					        <?php } ?>
																										
								<div class="cart"><a onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button"><span><?php echo $button_cart; ?></span></a></div>
								
								<div class="wish-list"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');"><?php echo $button_wishlist; ?></a><br /><a onclick="addToCompare('<?php echo $product['product_id']; ?>');"><?php echo $button_compare; ?></a></div>
							
							</div>
							
							<!-- End Hover PRODUCT -->
							
							<div class="left">
								
								<?php if ($product['thumb']) { ?>
								<div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
								<?php } ?>
							
							</div>
													
							<div class="right">

								<div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>

					        <?php if ($product['price']) { ?>
					        <div class="price">
					          <?php if (!$product['special']) { ?>
					          <?php echo $product['price']; ?>
					          <?php } else { ?>
					          <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
					          <?php } ?>
					        </div>
					        <?php } ?>
								
					        <?php if ($product['rating']) { ?>
					        <div class="ratings"><img src="catalog/view/theme/megastore/images/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
					        <?php } ?>
								
							</div>

						</div>
						
						<!-- End Product -->
						<?php } ?>
												
					</div>
					
					<!-- End Products -->
					
					<p class="clear"></p>
			
			</div>
		
		</div>
		
		<!-- End Box -->
