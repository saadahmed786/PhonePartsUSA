<?php echo $header; ?>
<?php echo $column_left; ?>
<?php echo $column_right; ?>
<div id="content">
    <?php echo $content_top; ?>
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
     </div>
    <?php if(!empty($data['ProductBundles']['ListingCustomCSS'])): ?>
        <style>
            <?php echo htmlspecialchars_decode($data['ProductBundles']['ListingCustomCSS']); ?>
        </style>
    <?php endif; ?>
	<h1 class="heading-title" itemprop="name"><?php echo $heading_title; ?></h1>
	<div class="pbListing-content">
		<?php foreach ($Bundles as $Bundle) { ?>
			<div class="pbListing-box box-productbundles">
				<div class="box-content">
					<?php $i=0; ?>
					<div class="box-products">
						<?php foreach ($Bundle['products'] as $product) { ?>
							<?php if ($i!=0) { ?> 
								<div class="PB_plusbutton">+</div>
							<?php } ?>
							<div class="PB_product">
                                <?php if ($product['thumb']) { ?>
                                    <div class="PB_product_image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
                                <?php } ?>
                                <div style="width:<?php echo (int)($data['ProductBundles']['PictureWidth'])+10; ?>px;"><a class="PB_product_name" href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
                                <div>
                                    <?php if ($product['price']) { ?>
                                        <?php if (!$product['special']) { ?>
                                            <?php $Pprice = $product['price']; ?>
                                        <?php } else { ?>
                                            <?php $Pprice = $product['special']; ?>
                                        <?php } ?>
                                    <?php } ?>
                                    <strong><?php echo $Pprice; ?></strong>
                                </div>
                     	 	</div>
                      	<?php $i++; } ?>
                        <div class="PB_bundle_info">
                        	<strong><span class="PB_bundle_total_price"><?php echo $ProductBundles_BundlePrice; ?> <?php echo $Bundle['FinalPrice']; ?></span></strong>
                            <br />
                            <strong><span><?php echo $ProductBundles_YouSave; ?> <?php echo $Bundle['VoucherPrice']; ?>!</span></strong>
                            <br /><br />
                            <center><a id="ProductBundlesSubmitButton" class="button"><?php echo $ProductBundles_AddBundleToCart; ?></a></center>
							<form method="post" id="ProductBundlesForm">
                                <input id="ProductBundlesOptions" type="hidden" name="products" value="<?php echo $Bundle['productOptions']; ?>" />
                                <input id="ProductBundlesProducts" type="hidden" name="products" value="<?php echo $Bundle['BundleProducts']; ?>" />
                                <input id="ProductBundlesDiscount" type="hidden" name="discount" value="<?php echo $Bundle['VoucherData']; ?>" />
                                <input id="ProductBundlesBundleID" type="hidden" name="bundle" value="<?php echo $Bundle['BundleNumber']; ?>" />
                            </form>                    
                        </div>
                    </div>
			</div>
		</div> 
		<?php } ?>
    	<div class="pbListing-pagination"><?php echo $pagination; ?></div>
	</div>  
	<script>
    jQuery(window).load(function () {
        $('#ProductBundlesSubmitButton').live('click', function(e){
            if ($(this).parents('.PB_bundle_info').find('#ProductBundlesOptions').val()==true) {
                $.fancybox.open({
                    href : 'index.php?route=module/productbundles/bundleproductoptions&bundle=' + $(this).parents('.PB_bundle_info').find('#ProductBundlesBundleID').val(),
                    type : 'ajax',
                    padding : 20,
                    openEffect : 'elastic',
                    openSpeed  : 150,
                    closeBtn  : false
                });
        } else { 
             $.ajax({
                url: 'index.php?route=module/productbundles/bundletocart',
                type: 'post',
                data: $(this).parents('.PB_bundle_info').find('#ProductBundlesForm').serialize(),
                dataType: 'json',
                success: function(json) {
                    if (json['error']) {
                        alert("There is a problem with the form. Please try again later.");
                    }
                    if (json['duplicate'] && !json['error']) {
                        window.location = "<?php echo html_entity_decode($this->url->link('checkout/cart', 'duplicated=true')); ?>";	
                    }
                    if (json['success']) {
                        window.location = "<?php echo html_entity_decode($this->url->link('checkout/cart')); ?>";	
                    }
                }
            });
        }
    });
    });
    </script>
	<?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>