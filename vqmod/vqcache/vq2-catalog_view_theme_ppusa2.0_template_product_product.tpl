<?php echo $header;?>
<?php
$my_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<script>

  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-24721193-1', 'auto'); // Insert your GA Web Property ID here, e.g., UA-12345-1
  ga('set','ecomm_prodid','<?php echo $product_id;?>_us'); // REQUIRED Product ID value, e.g., 12345, 67890
  ga('set','ecomm_pagetype','product'); // Optional Page type value, e.g., home, cart, purchase
  ga('set','ecomm_totalvalue',<?php echo ($sale_price?$product_info['sale_price']:$product_info['price']);?>); // Optional Total value, e.g., 99.95, 5.00, 1500.00
  ga('send', 'pageview');
  <?php
  if($this->customer->getId())
      {
        ?>
  ga('set', 'userId', <?php echo $this->customer->getId(); ?>);
        <?php
      }
      ?>

</script>
<style type="text/css">
	.hide-questions{
		display: none;
	}
	#back-to-top.show{
		display:none !important;
	}
</style>
<div id="review-pop" class="popup">
	<div class="popup-head">
		<h2 class="blue-title uppercase subtitle"><?php echo $heading_title;?></h2>
		<span>SKU:<?php echo $model;?></span>
	</div>
	<div class="popup-body">
		<p>My Rating</p>
		<fieldset class="rating clearfix">
			<input type="radio" id="star5" name="rating" value="5" /><label class = "full" for="star5" title="5 stars"></label>
			<!-- <input type="radio" id="star4half" name="rating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label> -->
			<input type="radio" id="star4" name="rating" value="4" /><label class = "full" for="star4" title="4 stars"></label>
			<!-- <input type="radio" id="star3half" name="rating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label> -->
			<input type="radio" id="star3" name="rating" value="3" /><label class = "full" for="star3" title="3 stars"></label>
			<!-- <input type="radio" id="star2half" name="rating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label> -->
			<input type="radio" id="star2" name="rating" value="2" /><label class = "full" for="star2" title="2 stars"></label>
			<!-- <input type="radio" id="star1half" name="rating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label> -->
			<input type="radio" id="star1" name="rating" value="1" /><label class = "full" for="star1" title="1 star"></label>
			<!-- <input type="radio" id="starhalf" name="rating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label> -->
		</fieldset>
		<p></p>
		<h5 class="blue-title" style="clear:both">Name</h5>
		<input type="text"  placeholder="Name" name="name" class="form-control" />
		<h5 class="blue-title" style="clear:both">My Review</h5>
		<textarea class="form-control" name="text" placeholder="Please Enter Your Review"></textarea>
		<h5 class="blue-title" style="clear:both">Captcha</h5>
		<input type="text" name="captcha" class="form-control" value="" />
		<br />
		<img src="index.php?route=product/product/captcha" alt="" id="captcha" /><br />
		<br />
		<div class="text-right popup-btns">
			<button class="btn btn-primary" id="button-review" type="submit">Post Rating &amp; Review</button>
		</div>
	</div>
</div>
<div style="width: 100%" id="quality-pop" class="popup">
	<div align="center" class="popup-head">
		<h2 class="blue-title uppercase subtitle">Quality</h2>
	</div>
	<div class="popup-body">
	<p style="font-size: 12px;"><img src="catalog/view/theme/ppusa2.0/images/icons/premium_logo.png" height="42" width="42" alt=""> Premium: OEM LCD, OEM Touchscreen, OEM Flex Cable</p>
	<?php
		if (strpos(strtolower($heading_title), 'iphone') !== false) {
    		
		}
		else
		{

	?>

	<p style="font-size: 12px;"><img src="catalog/view/theme/ppusa2.0/images/icons/standard_logo.png" height="42" width="42" alt=""> Standard : OEM LCD, Aftermarket Touchscreen, Aftermarket Flex Cable</p>
	<?php
		}
	?>
	<p style="font-size: 12px;"><img src="catalog/view/theme/ppusa2.0/images/icons/economy_logo.png" height="42" width="42" alt=""> Economy Plus: Highest Quality Fully Aftermarket Part</p>

	</div>
</div>
<div id="condition-pop" class="popup">
	<div class="popup-body">
	<p align="left"><font style="font-size: 20px;font-weight: bold;color: cornflowerblue;">New - New Condition</font><br>New or Refurbished Item</p><br>
	<p align="left"><font style="font-size: 20px;font-weight: bold;color: cornflowerblue;">Grade A - Minor Cosmetic Issues</font><br>Item is fully functional, but has 1-2 scratches or minor blemishes</p><br>
	<p align="left"><font style="font-size: 20px;font-weight: bold;color: cornflowerblue;">Grade B - Moderate Cosmetic Issues</font><br>Item is fully functional, but has 3-5 scratches or minor blemishes</p><br>
	<p align="left"><font style="font-size: 20px;font-weight: bold;color: cornflowerblue;">Grade C - Major Cosmetic issues</font><br>Item is fully functional, but has several scratches or minor blemishes</p><br>
	<p align="left"><font style="font-size: 20px;font-weight: bold;color: cornflowerblue;">Grade D - Severe Cosmetic Issues</font><br>Item is fully functional, but has many severe scratches or minor blemishes</p><br>
	</div>
</div>
<main class="main">
	<div class="container cart-page">
		<div class="row">
			<div class="col-md-12 main-content">
				<h2 style="font-weight:500"><?php echo $heading_title; ?></h2>
				<div class="row cart-detail">
					<div id="product_item_container" class="cart-items col-md-5" >
						<div class="big-image">
							<img id="img_01" data-zoom-image="<?php echo $popup;?>" src="<?php echo $thumb;?>" alt="">
									<?php

									if ((strtolower($product_info['class']['name']) == 'screen-lcdtouchscreenassembly' || strtolower($product_info['class']['name']) == 'screen-touchscreen' || strtolower($product_info['class']['name']) == 'battery-phone' || strtolower($product_info['class']['name']) == 'battery-tablet') && strtolower($product_info['quality'])=='premium') {
									?>
							<span class="overlay-x" style="top:-22px;width:26%;height:26%"></span>
							<?php
							}
							?>
						</div>
						<div class="review-area" style="display: none;">
							<ul class="review-stars clearfix">
								<?php
								if($rating==0)
								{
								$rating=5;
							}
							for($i=1;$i<=$rating;$i++)
							{
							?>
							<li class="fill"><a href="javascript:void(0);"><i class="fa fa-star"></i></a></li>
							<?php
						}
						?>
						<?php
						for($j=$i;$j>$i;$j--)
						{
						?>
						<li><a href="javascript:void(0)"><i class="fa fa-star"></i></a></li>
						<?php
					}
					?>
				</ul>
				<a href="#" onClick="$('html,body').animate({scrollTop: $('#write-review').offset().top},'slow');" class="underline review-links"><?php   $explode_reviews = explode(" ", $reviews); echo $explode_reviews[0]." ".$explode_reviews[1];  ?></a>
				<a href="#review-pop"  class="underline review-links fancybox">Write a review</a>
				<!--@End review stars -->
			</div>
			<!--@End review area -->
			<div class="cart-thumbs row">
				<?php foreach ($images as $image) { ?>
				<div class="col-xs-2">
					<div style="background-color: #ffffff" class="image" data-bigImg="<?php echo $image['popup']; ?>"><img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></div>
				</div>

				<?php
			}
			?>
		</div>
		<?php
		if ($stock == 'In Stock') {
		?>
		<div class="instock">
			<!-- <h3><i class="fa fa-check"></i><?php echo $stock;?></h3> -->
			<ul class="instock-list checklist clearfix">
				<li><i class="fa fa-check"></i><span>Free shipping on orders above $500</span></li>
				<li><i class="fa fa-check"></i><span>Leaves Our US Warehouse Within 24 Hours</span></li>
				<li><i class="fa fa-check"></i><span>Lowest Prices Online Guaranteed</span></li>
				<li><i class="fa fa-check"></i><span>60 Day Return Policy</span></li>
			</ul>
		</div>
		<?php
	}
	else
	{
	?>
	<div class="outstock">
		<!-- <h3><i class="fa fa-close"></i><?php echo $stock;?></h3> -->

	</div>
	<?php
}
?>
<!--@End cart thumbs -->
<ul class="social-shares hidden">
	<li class="share-alt">
		<i class="fa fa-share-alt"></i>
		Share:
	</li>
	<li class="email">
		<a href="mailto:?Subject=  <?php echo $heading_title;?>&Body=<?php echo htmlentities($my_url);?>"><img src="catalog/view/theme/ppusa2.0/images/icons/envelope.png" alt=""></a>
	</li>
								<!-- <li class="print">
									<a href="#"><img src="catalog/view/theme/ppusa2.0/images/icons/printer.png" alt=""></a>
								</li> -->
								<li class="twitter login_popup">
									<a href="javascript:void(0)" data-href="https://twitter.com/share?url=<?php echo $my_url;?>&amp;text=<?php echo $heading_title;?>" ><i class="fa fa-twitter"></i></a>
								</li>
								<li class="facebook login_popup">
									<a href="javascript:void(0)" data-href="http://www.facebook.com/sharer.php?u=<?php echo $my_url;?>&title=<?php echo $heading_title;?>" ><i class="fa fa-facebook"></i></a>
								</li>
								<li class="google-plus login_popup">
									<a href="javascript:void(0)" data-href="https://plus.google.com/share?url=<?php echo $my_url;?>" ><img src="catalog/view/theme/ppusa2.0/images/icons/googleplus.png" alt=""></a>
								</li>
								<li class="pinterest">
									<a href="javascript:void((function()%7Bvar%20e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)%7D)());"><i class="fa fa-pinterest-p"></i></a>
								</li>
							</ul>
							<!--@End social shares -->
							<!-- <article class="about-cart">
								<?php echo $tab_description; ?>
							</article> -->
							<h3 class="icon-heading hidden">
								<img src="catalog/view/theme/ppusa2.0/images/icons/review-icon.png" alt="">
								<?php echo $tab_description; ?>
							</h3>	
						</div>
						<div class="cart-descp col-md-5">
							<div class="row">
								<div class="<?php echo ($stock=='In Stock'?'col-md-12':'col-md-6 col-xs-6');?>">
									<?php
									if ($stock == 'In Stock') {
									?>
									<div class="instock" style="text-align:center">
										<h3><i class="fa fa-check"></i>In Stock and Ready to Ship</h3>
									<?php if (strtolower($product_info['class']['main_category']) == 'replacement parts' ) { ?>
										<div class="row" style="margin-top:10px">

										<div class="col-md-3"></div>
										<div class="col-md-6" style="border: 1px solid #ddd;color:#FFF;background-color:#4986FE;padding:30px">
										<font style="font-weight: bolder;font-size: large;"><?php echo $product_info['item_grade']?></font> <?php echo $product_info['qualities'][$product_info['item_grade']];?><br>
										
										<?php
										$qualities[$product_info['item_grade']] = str_replace("Like new", "New", $qualities[$product_info['item_grade']]);

										?>
										<?php echo $qualities[$product_info['item_grade']]; ?>


										<a href="#condition-pop" style="color:#FFF" class="underline review-links fancybox"><i class="fa fa-question-circle"></i></a></div><div class="col-md-3"></div>

										</div>

										<?php if ((strtolower($product_info['class']['name']) == 'screen-lcdtouchscreenassembly' || strtolower($product_info['class']['name']) == 'screen-touchscreen')  ) { ?>
									<br>
										<font style="font-weight:bold;font-size: medium;"><?php echo (($product_info['quality']) ? $product_info['quality']: 'Standard') . ' Quality'; ?></font><br>
										<a href="#quality-pop"  class="underline review-links fancybox"><font style="font-size: smaller;">What does this mean?</font></a>
										<?php } ?>
										<?php } ?>
									</div>
									<?php
								}
								else
								{
								?>
								<div class="outstock" style="">
									
									<h3><span style="padding:10px;border:1px solid #dddddd"><i class="fa fa-close"></i>Out of Stock</span></h3>
									
								</div>
								<?php
							}
							?>
							
						</div>
						<?php
						if($stock!='In Stock')
						{
						?>
						<div class="col-md-6 col-xs-6">
							<?php
								if($sale_price)
								{
								?>
								<span style="font-weight:bold;text-decoration: line-through; margin-right:5px"><?php echo $price;?></span>

								<?php
							}
							?>

								<span class="cartPPrice" style="color: <?php echo ($sale_price?'red':'rgb(73, 134, 254)');?>;
								font-size: 30px;
								line-height: 100%;"><?php echo ($sale_price?$sale_price:$price);?>
								
						</div>
						<?php
						}
						?>
					</div>
					<div class="row favt-wrap" style="text-align:center">
						<div class="col-md-12 ">
							<?php if($sub_models){ ?>
							<select class="selectpicker" id="grade" onchange="updateGradeProductPrice()">
								<option value="<?php echo $product_info['product_id']; ?>" data-content="
									<img src='catalog/view/theme/ppusa2.0/images/icons/iphone-icon.png'>
								</span>
								<span style='display:inline-block; width:100px;'>  <?php echo $product_info['model']; ?> $<?php echo round($product_info['sale_price']?$product_info['sale_price']:$product_info['price'],2); ?></span>">
								<?php echo $product_info['model']; ?> $<?php echo round($product_info['sale_price']?$product_info['sale_price']:$product_info['price'],2); ?>
							</option>
							<?php foreach($sub_models as $mod){ ?>
							<option value="<?php echo $mod['product_id']; ?>" data-content="
								<img src='catalog/view/theme/ppusa2.0/images/icons/iphone-icon.png'>
							</span>
							<span style='display:inline-block; width:100px;'>  <?php echo $mod['item_grade']; ?> $<?php echo round($mod['price'],2); ?></span>">
							<?php echo $mod['item_grade']; ?> $<?php echo round($mod['price'],2); ?>
						</option>

						<?php } ?>

					</select>
					<?php } ?>
				</div>
				<div class="col-md-5 col-xs-6" style="display:none">
					<span class="favorite"><i class="fa fa-heart"></i><a href="#" class="underline">Favorite</a></span>
				</div>
			</div>
			<?php 
			if($stock == 'In Stock')
			{
			?>
			<div class="row">
				<div class="col-md-12 col-md-push-2" style="text-align:center">
									
									<?php if ($discounts and !$sale_price) { ?>
									<table class="pricing-table table" style="width:65%;font-size:12px">
										<tbody>
											<tr>
												<td>Quantity</td>
												<?php if ($discounts) { ?>
												<td>1</td>
												<?php
											}
											?>
											<?php foreach ($discounts as $key=> $discount) { ?>
											<td><?php echo $discount['quantity']. ($discount === end($discounts) ? '+' : ' - ' . ( $discounts[$key+1]['quantity'] - 1 )); ?></td>
											<?php } ?>
										</tr>
										<tr>
											<td>Our Price</td>
											<?php if ($discounts) { ?>
											<td><?php echo $price;?></td>
											<?php
										}
										?>
										<?php foreach ($discounts as $key=> $discount) { ?>
										<td><?php echo $discount['price']; ?></td>
										<?php } ?>
									</tr>
								</tbody>
							</table>
							<?php } ?>
						</div>
					</div>
					<?php

					}
					?>
					<div class="row cart-total-wrp">
						<div class="col-md-12 cart-total text-center">
							<?php
							if($stock=='In Stock')
							{
							?>
							<div class="col-md-6 hidden-xs" style="margin-top:25px">
								<div class="qtyt-box">
									<div class="input-group spinner">
										<span class="txt">QTY</span>
										<input type="text" name="quantity" style="z-index:0" class="form-control" value="<?php echo $minimum;?>" onChange="getProductPrice()">
										<input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
										<div class="input-group-btn-vertical" style="margin-top:0px">
											<button class="btn " type="button" onclick="QtyChange('+');"><i class="fa fa-plus" style="font-size:12px"></i></button>
											<button class="btn" type="button" ><i class="fa fa-minus" style="font-size:12px"></i></button>
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<?php
								if($sale_price)
								{
								?>
								<div style="font-weight:bold;margin-bottom:5px">
									<strong>Old Price:</strong> <?php echo $price;?> ea
								</div>
								<?php
							}
							?>
							<h3 class="cartPPrice" style="color: <?php echo ($sale_price?'red':'rgb(73, 134, 254)');?>;
								font-size: 30px;
								line-height: 100%;"><?php echo $price;?><br><small style="color: rgb(73, 134, 254);font-size:45%">(<?php echo $price;?> ea)</small></h3>
							</div>
							<?php 
							}
							?>
							<?php
							if($stock=='In Stock')
							{
							?>
							<button class="btn btn-success2 addtocart hidden-xs" onclick="addToCartpp2($('input[name=product_id]').val(), $('input[name=quantity]').val())" style="text-align:center;width:50%;margin-bottom:20px;"><img src="catalog/view/theme/ppusa2.0/images/icons/basket.png" alt="" ><?php if(isset($this->session->data['cart'][$product_id])) { echo 'In Cart ('.$this->session->data['cart'][$product_id].')'; } else { echo 'Add to Cart'; } ?></button>
							<?php
						}
						else
						{
						?>
						<div style="padding:10px;border:1px solid #dddddd;margin-bottom:20px;">
						<div  >
						<strong style="font-weight:bold">Notify me when this product becomes available</strong><br><br>
							<span class="oos_qty_error_<?php echo $product_info['product_id'];?>" style="font-size:11px;color:red"></span>
							<input type="text" class="form-control customer_email_<?php echo $product_info['product_id'];?>" style="margin-bottom:15px;margin-left:15%;width:70%" placeholder="Enter your Email" value="<?php echo $this->customer->getEmail();?>">
						</div>
						<button id="notify_btn_<?php echo $product_info['product_id'];?>" onclick="notifyMe('<?php echo $product_info['product_id'];?>')" class="btn btn-danger" style="font-family: 'Montserrat';padding: 13px 22px;border: 0;font-size: 24px;width:50%;margin-bottom:20px" >Notify Me</button>
						</div>
						<?php
					}
					?>
				</div>

			</div>
			<div class="row">

				<div class="col-md-6 item-features text-sm-center">
					<h4>Product specs:</h4>
					<ul>
						<li>
							<span class="lbl hidden-xs">SKU:</span>
							<span class="text"><span class="hidden-md hidden-lg" style="vertical-align: top">SKU: </span><?php echo $model;?></span>
						</li>
						<?php
						// print_r($attributes);exit;
						foreach($attributes as $attr)
						{
						?>
						<li>
							<span class="lbl"><?php echo $attr['main_name'];?>:</span>
							<span class="text"><?php echo $attr['name'];?></span>
						</li>

						<?php
					}
					?>
				</ul>
			</div>
			

			<div class="col-md-6 item-features text-sm-center">
				<?php
			if($compatibles)
			{
			?>
				<h4>Compatible phone  model(s):</h4>
									<!-- <ul>
										<?php foreach($compatibles as $compatible_model)
										{
										?>
										<li><?php echo $compatible_model['device'] ;?> <?php echo $compatible_model['sub_model']; ?> ( <?php echo $compatible_model['name']; ?> )
										<?php
										}
										?>
									</ul> -->
									<div class="scroll4" style="height:110px;overflow: hidden">
										<?php foreach($compatibles as $compatible_model)
										{
										?>
										<?php echo $compatible_model['device'] ;?> <?php echo $compatible_model['sub_model']; ?> ( <?php echo $compatible_model['name']; ?> )<br>
										<?php
									}
									?>
								</div>
								<?php 
					}
					?>
							</div>
						</div>
						
					<?php
					if(isset($this->request->get['beta']))
					{
					?>
					<div class="row">


						<div class="col-md-12 recent-order">
							hello
					</div>
					


				</div>
				<?php
			}
			?>

			<!--@End Instock -->


		</div>
	</div>
	<!-- <div class="border"></div> -->
	
	<h3 class="icon-heading text-sm-center" id="related_products_heading" style="margin-bottom:5px" >
		<img src="catalog/view/theme/ppusa2.0/images/icons/review-icon.png" alt="">
		Related Products
	</h3>
	<div class="text-right text-sm-center" id="div_related_href" style="margin-bottom:5px;font-weight:bold">
	<a href=""></a>
	</div>
	<div style="border:1px solid #dddddd;padding:5px;background-color:white;" class="row clearfix listing-row text-center" id="related_products_div" >
		<img src="catalog/view/theme/ppusa2.0/images/spinner.gif" style="width:20%">
	</div>
	


	<h3 class="icon-heading" style="display:none">
		<img src="catalog/view/theme/ppusa2.0/images/icons/query.png" alt="">
		Questions &amp; answers
	</h3>
	<div class="ask-question clearfix" style="display:none">
		<input type="text" id='product_question' class="input" placeholder="Ask a Question">
		<button class="btn btn-primary " onclick="saveQuestion()">
			<i class="fa fa-question"></i>Ask
		</button>
	</div>
	<div id='question_div' style="display:none">
		<?php if(isset($question)){ 
		foreach($question as $key => $value ) {?> 
		<?php if($key > 1) { ?>
		<div class="query-box hide-questions">
			<?php }else { ?>
			<div class="query-box">
				<?php } ?>
				<h5>Question:</h5>
				<p>
					<?php echo $value['question'] ?>
				</p>
				<?php if($value['answer']) { ?>
				<h5>Answer</h5>
				<p><?php echo $value['answer'] ?></p>
				<?php } ?>
			</div>
			<?php }}else{ ?>
			<div class="query-box no-question">
				<p class="content">There are no questions for this product.</p>
			</div>
			<?php }?>
		</div>
		<a href='javascript:void(0)' class="viewmore" style="display:none" onclick="toggleQuestion()">view more Questions &amp; answers</a>
		<!-- <div class="border"></div> -->
		<div class="row write-review hidden" id="write-review">
			<div class="col-sm-6">
				<h3 class="icon-heading">
					<img src="catalog/view/theme/ppusa2.0/images/icons/review-icon.png" alt="">
					customer reviews
				</h3>
			</div>
			<div class="col-sm-6 text-right">
				<a href="#review-pop" class="btn btn-primary fancybox">write a customer review</a>
			</div>
		</div>
		<!--@End write-review -->
		<div class="row customer-review hidden " id="review-checker">
			<div class="col-md-5">
				<ul class="review-stars clearfix">



				</ul>
				<p><?php echo $rating;?> out of 5 stars</p>
				<h4><a href="javascript:void(0)" class="underline">See all <?php echo $tab_review; ?></a></h4>
			</div>
			<div class="col-md-7">
				<ul class="progress-wrap">
					<li>
						<span class="satar-rate">5 Star</span>
						<div class="progress">
							<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $five_perc;?>"
								aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $five_perc;?>%">
							</div>
						</div>
						<span class="progress-percent"><?php echo (int)$five_perc;?> %</span>
					</li>
					<li>
						<span class="satar-rate">4 Star</span>
						<div class="progress">
							<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $four_perc;?>"
								aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $four_perc;?>%">
							</div>
						</div>
						<span class="progress-percent"><?php echo (int)$four_perc;?> %</span>
					</li>
					<li>
						<span class="satar-rate">3 Star</span>
						<div class="progress">
							<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $three_perc;?>"
								aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $three_perc;?>%">
							</div>
						</div>
						<span class="progress-percent"><?php echo (int)$three_perc;?> %</span>
					</li>
					<li>
						<span class="satar-rate">2 Star</span>
						<div class="progress">
							<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $two_perc;?>"
								aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $two_perc;?>%">
							</div>
						</div>
						<span class="progress-percent"><?php echo (int)$two_perc;?> %</span>
					</li>
					<li>
						<span class="satar-rate">1 Star</span>
						<div class="progress">
							<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $one_perc;?>"
								aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $one_perc;?>%">
							</div>
						</div>
						<span class="progress-percent"><?php echo (int)$one_perc;?> %</span>
					</li>
				</ul>
			</div>
		</div>
		<!--@End customer-review -->
		<div id="review" style="display:none">

		</div>
		<?php if(isset($steps)){ ?>
		<div class="border" style="margin-top: 10px;margin-bottom: 10px;"></div>
		<h3 class="icon-heading">
			<img src="catalog/view/theme/ppusa2.0/images/icons/repair-icon.png" alt="">
			step by step repair guide
		</h3>
		<?php if(isset($steps)){ 
		foreach($steps as $key => $value ) {?> 
		<article class="repair-guide">
			<h3><span>step <?php echo $value['order_number'] ?></span> - step <?php echo $value['step_name'] ?></h3>
			<div class="repair-box clearfix">
				<div class="image">
					<img src="<?php echo $value['image_path'] ?>" alt="">
				</div>
				<div class="text">
					<p><?php echo $value['step_description'] ?> </p>
				</div>
			</div>
		</article>
		<?php }}else{ ?>
		<div class="content">There are no steps for this product.</div>
		<?php }?>
		<?php } ?>
		<!-- <div class="border" style="margin-top: 10px;margin-bottom: 10px;"></div> -->

	</div>
	<!-- @End of main-content -->
	
			<!-- @End of sidebar -->
		</div>	
	</div>
	<?php
		if ($stock == 'In Stock') {
		?>
	<div class="sticky_add_cart row ">
		<div class="qty-changers">
			<span class="minus" onClick="QtyChange('-');">-</span>
			<input id="QtyNormal" name="QtyNormal" type="tel" size="2" maxlength="3" value="1" class="QtyNormal">
			<span class="plus" onClick="QtyChange('+');">+</span>
		</div>
		<div class="add-button">
			<div class="button btn btn-success addtocart" id="mobile_add_to_cart" onclick="addToCartpp2('<?php echo $product_id;?>', $('#QtyNormal').val())"><div class="content"><?php if(isset($this->session->data['cart'][$product_id])) { echo 'In Cart ('.$this->session->data['cart'][$product_id].')'; } else { echo 'Add to Cart'; } ?><span class="carrot">></span></div></div>
		</div>
	</div>
	<?php

	}
	?>
	</div>
</main><!-- @End of main -->
<script>
	function QtyChange(xtype)
	{
		var my_val = $('#QtyNormal').val();
		if(xtype=='+')
		{
			my_val = parseInt(my_val)+1;
		}
		else
		{
			my_val = parseInt(my_val)-1;
		}
		if(my_val<=1)
		{
			my_val = 1;
		}
		$('#QtyNormal').val(my_val);
  // getProductPrice();
}
function getProductPrice()  {
	var product_id = $('input[name=product_id]').val();
	var quantity = $('input[name=quantity]').val();
	if (quantity === '0') {
		quantity = 1;
		$('input[name=quantity]').val('1');
	}
	$.ajax({
		url: 'index.php?route=product/product/getUpdatedPrice',
		type: 'post',
		data: {product_id:product_id,quantity:quantity},
		dataType: 'json',
		beforeSend: function() {
     // $('.success, .warning').remove();
      //$('#button-review').attr('disabled', true);
      $('.cart-total-wrp .qty').html('<small>Updating...</small>');
  },
  complete: function() {
     // $('#button-review').attr('disabled', false);
      //$('.attention').remove();
  },
  success: function(json) {
  	$('information, .error').remove();
  	if (json['error']) {
  	}  
  	if (json['success']) {
  		$('.cart-total-wrp .cartPPrice').html(json['success']+'<br><small style="color: rgb(73, 134, 254);font-size:45%">(' + json['unit_price'] + ' ea)</small>'); 
  	}
  }
});
}
function checkReviews (){
	//var gohar = $('.reviews2 .content').text();
	//alert(gohar);
	if ($('.reviews2 .content').text() == 'There are no reviews for this product.') {
		$('#review-checker').hide();
	}
}
function updateGradeProductPrice(){
	var new_product_id = $('#grade').val();
	$.ajax({
		url: 'index.php?route=product/product/gradeDiscounts',
		type: 'get',
		data: {product_id:new_product_id},
		dataType: 'json',
		beforeSend: function() {
     // $('.success, .warning').remove();
      //$('#button-review').attr('disabled', true);

  },
  complete: function() {
     // $('#button-review').attr('disabled', false);
      //$('.attention').remove();
  },
  success: function(json) {  
  	if (json['success']) {
  		var html = '';
  		html+= '<tr>';
  		html+= '<td>Quantity</td>';
  		html+= '<td>1-2</td>';
  		for (var i = 0; i <= json['discounts'].length -1; i++){
  			html+= '<td>'+json['discounts'][i]['quantity']+'</td>';
  		}
  		html+= '</tr>';
  		html+= '<tr>';
  		html+= '<td>Our Price</td>';
  		html+= '<td>'+json['price']+'</td>';
  		for (var i = 0; i <= json['discounts'].length -1; i++){
  			html+= '<td>'+json['discounts'][i]['price']+'</td>';
  		}
  		html+= '</tr>';
  	} else if (json['error']) {

  		var html = '';
  		html+= '<tr>';
  		html+= '<td>Quantity</td>';
  		for (var i = 0; i <= json['discounts'].length -1; i++){
  			html+= '<td>'+json['discounts'][i]['quantity']+'</td>';
  		}
  		html+= '</tr>';
  		html+= '<tr>';
  		html+= '<td>Our Price</td>';
  		for (var i = 0; i <= json['discounts'].length -1; i++){
  			html+= '<td>'+json['discounts'][i]['price']+'</td>';
  		}
  		html+= '</tr>';
  	}	

  	$('.pricing-table tbody').html(html);
  }

});
	$('input[name=product_id]').val(new_product_id);
	getProductPrice();
}
$(document).ready(function(){
	getProductPrice();
});
$('.viewmore').click(function(){
	var page = parseInt($('#reviewmorebtn').attr('data-page'));
	page = page + 1;
	var response;
	$.ajax({ type: "GET",   
		url: "index.php?route=product/product/review&product_id=<?php echo $product_id;?>&page="+page,   
		async: false,
		success : function(text)
		{		
			response= text;
		}
	});
	$('#review2').prepend(response);
});
$.ajax({ type: "GET",   
	url: "index.php?route=product/product/review&product_id=<?php echo $product_id;?>",   
	async: false,
	success : function(text)
	{		
		$('#review').html(text);
		checkReviews(); 
	}
});
//$('#review').load('index.php?route=product/product/review&product_id=<?php echo $product_id; ?>');
$('#review .matrialPagination a').on('click',document, function() {
	$('#review').fadeOut('slow');
	$('#review').load(this.href);
	$('#review').fadeIn('slow');
	return false;
});
$(document).ready(function() {
	$('ul.social-shares li.login_popup a').on('click',function(e) {

		e.preventDefault();
		window.open($(this).attr('data-href'), 'fbShareWindow', 'height=450, width=550, top=' + ($(window).height() / 2 - 275) + ', left=' + ($(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
		return false;
	});
});
$('#button-review').on('click', function() {
	$.ajax({
		url: 'index.php?route=product/product/write&product_id=<?php echo $product_id; ?>',
		type: 'post',
		dataType: 'json',
		data: 'name=' + encodeURIComponent($('input[name=\'name\']').val()) + '&text=' + encodeURIComponent($('textarea[name=\'text\']').val()) + '&rating=' + encodeURIComponent($('input[name=\'rating\']:checked').val() ? $('input[name=\'rating\']:checked').val() : '') + '&captcha=' + encodeURIComponent($('input[name=\'captcha\']').val()),
		beforeSend: function() {
     // $('.success, .warning').remove();
     // $('#button-review').attr('disabled', true);
     // $('#review-title').after('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
 },
 complete: function() {
 	$('#button-review').attr('disabled', false);
     // $('.attention').remove();
 },
 success: function(data) {
 	if (data['error']) {
      // $('#review-title').after('<div class="warning">' + data['error'] + '</div>');
      alert(data['error']);
  }
  if (data['success']) {
      // $('#review-title').after('<div class="success">' + data['success'] + '</div>');
      alert(data['success']);
      $.fancybox.close();
      $('input[name=\'name\']').val('');
      $('textarea[name=\'text\']').val('');
      $('input[name=\'rating\']:checked').attr('checked', '');
      $('input[name=\'captcha\']').val('');
  }
}
});
});
function saveQuestion()
{
	var question = $('#product_question').val();
	if(question.length < 5)
	{
		alert("Kindly provide a proper question.");
		return;
	}
	$.ajax({
		url: 'index.php?route=product/product/saveQuestion',
		type: 'post',
		data:{'question':question,'product_id':'<?php echo $product_id; ?>','product_title':'<?php echo $heading_title; ?>','product_sku':'<?php echo $model;?>'},
		dataType: 'json',
		success : function(response){
			$('#question_div').append(response);
			$('#product_question').val('');
			$('.no-question').hide();
		}
	});
}
function toggleQuestion()
{	
	$('.hide-questions').show();
}
checkReviews();
// $(document).ready(function() {
   
//     var showChar = 500;  // How many characters are shown by default
//     var ellipsestext = "...";
//     var moretext = "Show more";
//     var lesstext = "Show less";
    
//     $('.more').each(function() {
//     	var content = $(this).html();

//     	if(content.length > showChar) {

//     		var c = content.substr(0, showChar);
//     		var h = content.substr(showChar, content.length - showChar);

//     		var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

//     		$(this).html(html);
//     	}

//     });

//     $(".morelink").click(function(){
//     	if($(this).hasClass("less")) {
//     		$(this).removeClass("less");
//     		$(this).html(moretext);
//     	} else {
//     		$(this).addClass("less");
//     		$(this).html(lesstext);
//     	}
//     	$(this).parent().prev().toggle();
//     	$(this).prev().toggle();
//     	return false;
//     });
// });
function loadRelatedProducts()
	{
		$.ajax({
			url: '?route=product/product/getRelatedProducts',
			type: 'POST',
			dataType: 'json',
			data: {product_id: '<?php echo $product_info['product_id'];?>',my_page:1,main_sku:'<?php echo $product_info['main_sku'];?>'},
			beforeSend: function() {
				
            }
		}).always(function(json) {
				
				if(json['products'])
				{

				$('#related_products_div').html(json['products']);
				$('#div_related_href a').attr('href',json['href']['href']);
				$('#div_related_href a').text('See all Products for '+json['href']['name']);
				}
				else
				{
				$('#related_products_div').hide();
				$('#related_products_heading').hide();
				$('#div_related_href').hide();
			}

$('#related_products_div').addClass('scroll2');
$('#related_products_div').css('height','430px','important');
$('#related_products_div').css('overflow','hidden');
			$(".scroll2").slimScroll({
        height: "430px",
        size: "10px",
        color: "#4986fe",
        railVisible: !0,
        railColor: "#f4f4f4",
        alwaysVisible: !0
    })
			
		});
	}
$(document).ready(function(){
	
		loadRelatedProducts();
		//loadProducts('home_products3','popular_accessories');
	});
$(".scroll4").slimScroll({
        height: "110px",
        size: "3px",
        color: "#4986fe",
        railVisible: true,
        railColor: "#f4f4f4",
        alwaysVisible: true
    })
</script>				
<script type="text/javascript">
$('#notify_btn').click(function(){
	$('#msg').html('');
	$('#loadingmessage').show();
	$.ajax({
		  type: 'post',
		  url: 'index.php?route=product/product/notify',
		  data: {data: $('#notifyemail').val(),product_id: '<?php echo $product_id; ?>'},
		  dataType: 'json',
		  success: function(json) {
				if (json['success']) {
					  $('#msg').html(json['success']);
					  $('#loadingmessage').hide();
				}
		  }
	 });

 });

</script>
                
<?php echo $footer; ?>