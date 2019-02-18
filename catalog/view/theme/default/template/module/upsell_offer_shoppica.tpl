<?php if ($show_offer) { ?>
<div class="content" id="upsell-offer" style="padding: 10px 20px 0 20px;">
	<?php if ($title) { ?>
	<div><h1><?php echo $title; ?></h1></div>
	<?php } ?>
	<?php if ($description) { ?>
	<p><?php echo $description; ?></p>
	<?php } ?>
	<div id="upsell-offer-notification"></div>
	<?php if ($products) { ?>
		<div class="s_listing s_grid_view size_1 clearfix">
		    <?php foreach ($products as $product) { ?>
		    <?php $tbSlot->start('product\category.products.each', array('products' => $products, 'product' => $product, 'data' => $this->data)); ?>
		    <div class="s_item upsell_product_<?php echo $product['product_id']; ?>" style="opacity: 1;">
		      <?php if ($product['thumb']) { ?>
		      <a class="s_thumb" href="javascript:void(0);"><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" /></a>
		      <?php } ?>
		      <div class="s_item_info">
				<h3><a href="javascript:void(0);"><?php echo $product['name']; ?></a></h3>
				<?php if ($product['price']) { ?>
					<div class="s_price_holder s_size_<?php echo $tbData->common['price_size']; ?> <?php echo 's_' . $tbData->common['price_design']; ?>">
					<?php if (!$product['special']) { ?>
					<p class="s_price"><?php echo $tbData->priceFormat($product['price']); ?></p>
					<?php } else { ?>
					<p class="s_price s_promo_price"><span class="s_old_price"><?php echo $tbData->priceFormat($product['price']); ?></span><?php echo $tbData->priceFormat($product['special']); ?></p>
					<?php } ?>
					</div>
				<?php } ?>
				<?php if ($tbData->common['checkout_enabled']) { ?>
			        <div class="s_actions">
					<a class="s_button_add_to_cart" href="javascript:;" onclick="addToCart('<?php echo $product['product_id']; ?>');">
					<span class="s_icon_16"><span class="s_icon"></span><?php echo $button_cart; ?></span>
					</a>
			        </div>
				<?php } ?>
		      </div>
		    </div>
		     <?php $tbSlot->stop(); ?>
		    <?php } ?>
		</div>
	<?php } ?>
	<div class="s_submit clearfix buttons" style="margin-top: 30px; height: 40px;">
		<a style="display: none;" href="<?php echo $cart; ?>" class="s_button_1 s_ddd_bgr left"><span class="s_text"><?php echo $button_to_cart; ?></span></a>
		<a style="display: none;" onclick="upsellRedirect('<?php echo $checkout; ?>');" class="s_button_1 s_main_color_bgr"><span class="s_text"><?php echo $button_checkout; ?></span></a>
        </div> 
</div>
<script type="text/javascript"><!--
$(document).bind('cbox_complete', function(){
	$.colorbox.resize();
	
	$("#upsell-offer .s_actions").show();
						
	$("#upsell-offer .buttons > a").delay(2000).show('slow');
});	
//--></script>
<?php } else { ?>
<script type="text/javascript"><!--
$(document).ready(function() {
	$('a[href*="<?php echo $selector; ?>"]').live('click', function(e) {
		e.preventDefault();
		var url = $(this).attr('href');

		$.ajax({
			url: 'index.php?route=module/upsell_offer/get',
			type: 'get',
			dataType: 'html',	
			success: function(html) {
				if (html) {					
					$.colorbox({
						overlayClose: false,
						opacity: 0.5,
						width: '700px',
						html: html
					});
				} else {
					location = url;
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				location = url;
			}
		});
	});	
});
//--></script>
<?php } ?>

<script type="text/javascript"><!--
function upsellRedirect(new_link){
	location = new_link;
}
//--></script>