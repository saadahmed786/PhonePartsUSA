<?php if ($show_offer) { ?>
<div class="content" id="upsell-offer" style="padding: 10px 20px 0 20px;">
	<?php if ($title) { ?>
	<div> <img src='http://dev.phonepartsusa.com/image/data/0000png/banner_red2.png'style="margin-left: 150px;" > <h1><?php echo $titles; ?></h1></div>
	<?php } ?>
	<?php if ($description) { ?>
	<p><?php echo $descriptions; ?></p>
	<?php } ?>
	<div id="upsell-offer-notification"></div>
	<?php if ($products) { ?>
		<?php if ($product_nr == 1) { ?>		
		<div class="product-info">
		    <?php if ($products[0]['thumb']) { ?>
		    <div class="left">
			<div class="image"><a href="javascript:void(0);"><img src="<?php echo $products[0]['thumb']; ?>" title="" alt="" id="image" /></a></div>
		    </div>
		    <?php } ?>
		    <div class="right">
		      <h2><?php echo $products[0]['name']; ?></h2><br />
		      <?php if ($products[0]['price']) { ?>
		      <div class="price"><?php echo $text_price; ?>
			<?php if (!$products[0]['special']) { ?>
			<?php echo $products[0]['price']; ?>
			<?php } else { ?>
			<span class="price-old"><?php echo $products[0]['price']; ?></span> <span class="price-new"><?php echo $products[0]['special']; ?></span>
			<?php } ?>
			<br />
			<?php if ($products[0]['tax']) { ?>
			<span class="price-tax"><?php echo $text_tax; ?> <?php echo $products[0]['tax']; ?></span><br />
			<?php } ?>
		      </div>
		      <?php } ?>
		      <div class="cart">
			<div><?php echo $text_qty; ?>
			  <input type="text" name="upsell_offer_quantity" size="2" value="<?php echo $products[0]['minimum']; ?>" />
			  <input type="hidden" name="upsell_offer_product_id" size="2" value="<?php echo $products[0]['product_id']; ?>" />
			  &nbsp;
			  <input type="button" value="<?php echo $button_cart; ?>" id="upsell-offer-button-cart" class="button" />
			</div>
			<?php if ($products[0]['minimum'] > 1) { ?>
			<div class="minimum"><?php echo $products[0]['text_minimum']; ?></div>
			<?php } ?>
		      </div>
		    </div>
		</div>		
		<?php } else { ?>
		<div class="product-grid">
		    <?php foreach ($products as $product) { ?>
		    <div class="cus-multi-product">
		      <?php if ($product['thumb']) { ?>
		      <div class="image"><a href="javascript:void(0);"><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
		      <?php } ?>
		      <div class="name"><a href="javascript:void(0);"><?php echo $product['name']; ?></a></div>
		      <?php if ($product['price']) { ?>
		      <div class="price">
			<?php if (!$product['special']) { ?>
			<?php echo $product['price']; ?>
			<?php } else { ?>
			<span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
			<?php } ?>
			<?php if ($product['tax']) { ?>
			<br />
			<span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
			<?php } ?>
		      </div>
		      <?php } ?>
		      <div class="cart">
			  <span class="button_pink">
			<input type="button" value="<?php echo $button_cart; ?>" onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button cus-button-vif" />
		      </span>
			  </div>
		    </div>
		    <?php } ?>
		</div>
		<?php } ?>
	<?php } ?>

<div class="buttons" style="/* margin-top: 30px; */ height: 80px;margin-bottom: 0px;padding-top: 38px;">
	    <div class="right" style="display: none;"><a onclick="upsellRedirect('<?php echo $checkout; ?>');" class="button button_pink"><span><?php echo $button_checkout; ?></span></a></div>
	    <div class="left" style="display: none;"><a href="<?php echo $cart; ?>" class="button button_black"><span><?php echo $button_to_cart; ?></span></a></div>
	</div> 
	
</div>
<script type="text/javascript"><!--
$(document).bind('cbox_complete', function(){
	$.colorbox.resize();
	
	$("#upsell-offer .cart > input").live("click", function() {
		$(this).val('<?php echo $text_added; ?>');
	});
						
	$("#upsell-offer .buttons > div").delay(2000).show('slow');
	
	<?php if ($product_nr == 1) { ?>
		$('#upsell-offer-button-cart').click(function() {
			addToCart( $('input[name=\'upsell_offer_product_id\']').val(), $('input[name=\'upsell_offer_quantity\']').val() );
			$(this).val('Added');
		});
	<?php } ?>
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