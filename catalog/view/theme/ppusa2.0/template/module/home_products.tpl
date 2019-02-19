<?php foreach ($home_products2 as $product) : ?>
								<div class="col-md-2 listing-items large-row product_<?php echo $product['product_id']; ?>" style="width:14.285%">
									<article class="related-product">
										<div class="image">
											<img  src="<?php echo $product['thumb']; ?>"  height="150" width="150" alt="<?php echo $product['name']; ?>" style="cursor:pointer" onClick="window.location='<?php echo $product['href'];?>'">
										</div>
										<h4 style="height: 80px"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
										
										<?php
					if((int)$product['quantity']>0)
						{
							$in_cart= (isset($this->session->data['cart'][$product['product_id']])?true:false);
							?>
										<div class="qtyt-box">
											<div class="input-group spinner">
												<span class="txt">QTY</span>
												<input type="text" class="form-control qty" value="1" style="color:#303030">
												<div class="input-group-btn-vertical">
													<button class="btn" type="button"><i class="fa fa-plus"></i></button>
													<button class="btn" type="button"><i class="fa fa-minus"></i></button>
												</div>
											</div>
										</div>
										<?php if($product['sale_price']){ ?>
										
										<p class="price"><span style="font-size: 13px;margin-right:5px;text-decoration:line-through;"><?php echo $product['price']; ?></span><span style="color: red;"><?php echo $product['sale_price']; ?></span></p>
										<?php } else {?>
										<p class="price"><span><?php echo $product['price']; ?></span></p>
										<?php } ?>
										<button onclick="addToCartpp2(<?php echo $product['product_id']; ?>, $(this).parent().find('.qty').val())" class="btn <?php echo ($in_cart?'btn-success2':'btn-info');?>"><?php echo ($in_cart?'In Cart ('.$this->session->data['cart'][$product['product_id']].')':'Add to cart');?></button>
										<?php
									}
									else
									{
										?>
										<div class="qtyt-box">
											<div class="input-group spinner">
												<span class="txt">QTY</span>
												<input type="text" class="form-control qty" disabled="" value="1" style="color:#303030">
												<div class="input-group-btn-vertical">
													<button class="btn" type="button" disabled><i class="fa fa-plus"></i></button>
													<button class="btn" type="button" disabled><i class="fa fa-minus"></i></button>
												</div>
											</div>
										</div>
											<!-- <div >
			<span class="oos_qty_error_<?php echo $product['product_id'];?>" style="font-size:11px;color:red"></span>
			<input type="text" class="form-control customer_email_<?php echo $product['product_id'] ?>" style="margin-bottom:48px" placeholder="Enter your Email" value="<?php echo $this->customer->getEmail();?>">
	</div> -->
	<?php if($product['sale_price']){ ?>
										
										<p class="price"><span style="font-size: 13px;margin-right:5px;text-decoration:line-through;"><?php echo $product['price']; ?></span><span style="color: red;"><?php echo $product['sale_price']; ?></span></p>
										<?php } else {?>
										<p class="price"><span><?php echo $product['price']; ?></span></p>
										<?php } ?>
	<button class="btn btn-danger" id="notify_btn_<?php echo $product['product_id'];?>" >Out of Stock</button>
										<?php
									}
									?>
										<?php

									if ((strtolower($product['class']['name']) == 'screen-lcdtouchscreenassembly' || strtolower($product['class']['name']) == 'screen-touchscreen' || strtolower($product['class']['name']) == 'battery-phone' || strtolower($product['class']['name']) == 'battery-tablet') && strtolower($product['quality'])=='premium') {
									?>

									<span class="overlay-x"></span>
									<?php
									}
									?>
		</article>
										
									</article>
								</div>
							<?php endforeach; ?>