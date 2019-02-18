<?php if ( ($data['ProductBundles']['Enabled'] != 'no') && ($ShowPage==true) ) { ?>
<span class="PB_heading_text"><?php echo $text_option_heading; ?></span>  
    
    <?php if ($products) { ?>
    	<form method="post" id="ProductBundlesOptionsForm">
    	<?php $i=0; $max = sizeof($products); foreach ($products as $product) { ?>
       
        	<div data-product-index="<?php echo $i; ?>" class="<?php if (($i+1) == $max) echo "PB_options_product_item_last"; else echo "PB_options_product_item"; ?>">
        		
           		<?php if ($product['thumb']) { ?>
					<div class="PB_options_image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
				<?php } ?>
                 	<?php if ($product['price']) { ?>
                      <?php if (!$product['special']) { ?>
                     	 <?php $Pprice = $product['price']; ?>
                      <?php } else { ?>
                     	 <?php $Pprice = $product['special']; ?>
                      <?php } ?>
              	  	<?php } ?>
					<div class="PB_options_product_field"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                    <strong><?php echo $Pprice; ?></strong></div>
                    
					<?php if ($product['options']) { ?>
                 		<div class="PB_options">
                            <?php foreach ($product['options'] as $option) { ?>
                                <?php if ($option['type'] == 'select') { ?>
                                    <div data-option-id="bundle_option-<?php echo $option['product_option_id']; ?>" class="option">
                                        <?php if ($option['required']) { ?>
                                            <span class="required">*</span>
                                        <?php } ?>
                                        <b><?php echo $option['name']; ?>:</b><br />
                                        <select name="option[<?php echo $i; ?>][<?php echo $option['product_option_id']; ?>]">
                                            <option value=""><?php echo $text_select; ?></option>
                                            <?php foreach ($option['option_value'] as $option_value) { ?>
                                                <option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                                <?php if ($option_value['price']) { ?>
                                                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                                <?php } ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php } ?>
                                <?php if ($option['type'] == 'radio') { ?>
                                    <div data-option-id="bundle_option-<?php echo $option['product_option_id']; ?>" class="option">
                                        <?php if ($option['required']) { ?>
                                            <span class="required">*</span>
                                        <?php } ?>
                                        <b><?php echo $option['name']; ?>:</b><br />
                                        <?php foreach ($option['option_value'] as $option_value) { ?>
                                            <input type="radio" name="option[<?php echo $i; ?>][<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
                                            <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                                <?php if ($option_value['price']) { ?>
                                                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                                 <?php } ?>
                                            </label>
                                            <br />
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <?php if ($option['type'] == 'checkbox') { ?>
                                    <div data-option-id="bundle_option-<?php echo $option['product_option_id']; ?>" class="option">
                                        <?php if ($option['required']) { ?>
                                            <span class="required">*</span>
                                        <?php } ?>
                                        <b><?php echo $option['name']; ?>:</b><br />
                                        <?php foreach ($option['option_value'] as $option_value) { ?>
                                            <input type="checkbox" name="option[<?php echo $i; ?>][<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
                                            <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                                <?php if ($option_value['price']) { ?>
                                                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                                <?php } ?>
                                            </label>
                                            <br />
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <?php if ($option['type'] == 'image') { ?>
                                    <div data-option-id="bundle_option-<?php echo $option['product_option_id']; ?>" class="option">
                                        <?php if ($option['required']) { ?>
                                            <span class="required">*</span>
                                        <?php } ?>
                                        <b><?php echo $option['name']; ?>:</b><br />
                                        <table class="option-image">
                                        <?php foreach ($option['option_value'] as $option_value) { ?>
                                            <tr>
                                                <td style="width: 1px;"><input type="radio" name="option[<?php echo $i; ?>][<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" /></td>
                                                <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" /></label></td>
                                                <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                                <?php if ($option_value['price']) { ?>
                                                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                                 <?php } ?>
                                                </label></td>
                                            </tr>
                                        <?php } ?>
                                        </table>
                                    </div>
                                <?php } ?>
                                <?php if ($option['type'] == 'text') { ?>
                                    <div data-option-id="bundle_option-<?php echo $option['product_option_id']; ?>" class="option">
                                        <?php if ($option['required']) { ?>
                                            <span class="required">*</span>
                                        <?php } ?>
                                        <b><?php echo $option['name']; ?>:</b><br />
                                        <input type="text" name="option[<?php echo $i; ?>][<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" />
                                    </div>
                                <?php } ?>
                                <?php if ($option['type'] == 'textarea') { ?>
                                    <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
                                        <?php if ($option['required']) { ?>
                                            <span class="required">*</span>
                                        <?php } ?>
                                        <b><?php echo $option['name']; ?>:</b><br />
                                        <textarea name="option[<?php echo $i; ?>][<?php echo $option['product_option_id']; ?>]" cols="16" rows="5"><?php echo $option['option_value']; ?></textarea>
                                    </div>
                                <?php } ?>
                                <?php if ($option['type'] == 'file') { ?>
                                    <div data-option-id="bundle_option-<?php echo $option['product_option_id']; ?>" class="option">
                                        <?php if ($option['required']) { ?>
                                            <span class="required">*</span>
                                        <?php } ?>
                                        <b><?php echo $option['name']; ?>:</b><br />
                                        <input type="button" value="<?php echo $button_upload; ?>" id="button-option-<?php echo $option['product_option_id']; ?>" class="button">
                                        <input type="hidden" name="option[<?php echo $i; ?>][<?php echo $option['product_option_id']; ?>]" value="" />
                                    </div>
                                <?php } ?>
                                <?php if ($option['type'] == 'date') { ?>
                                    <div data-option-id="bundle_option-<?php echo $option['product_option_id']; ?>" class="option">
                                        <?php if ($option['required']) { ?>
                                            <span class="required">*</span>
                                        <?php } ?>
                                        <b><?php echo $option['name']; ?>:</b><br />
                                        <input type="text" name="option[<?php echo $i; ?>][<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="PB_date" />
                                    </div>
                                <?php } ?>
                                <?php if ($option['type'] == 'datetime') { ?>
                                    <div data-option-id="bundle_option-<?php echo $option['product_option_id']; ?>" class="option">
                                        <?php if ($option['required']) { ?>
                                            <span class="required">*</span>
                                        <?php } ?>
                                        <b><?php echo $option['name']; ?>:</b><br />
                                        <input type="text" name="option[<?php echo $i; ?>][<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="PB_datetime" />
                                    </div>
                                <?php } ?>
                                <?php if ($option['type'] == 'time') { ?>
                                    <div data-option-id="bundle_option-<?php echo $option['product_option_id']; ?>" class="option">
                                        <?php if ($option['required']) { ?>
                                            <span class="required">*</span>
                                        <?php } ?>
                                        <b><?php echo $option['name']; ?>:</b><br />
                                        <input type="text" name="option[<?php echo $i; ?>][<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="PB_time" />
                                    </div>
                                <?php } ?>
                            <?php } ?>
                 		</div> <!-- <div class="options"> -->
                  <?php } ?>
			</div>
    	<?php $i++; } ?>
        <input id="ProductBundlesProducts" type="hidden" name="products" value="<?php echo $BundleProducts; ?>" />
		<input id="ProductBundlesDiscount" type="hidden" name="discount" value="<?php echo $VoucherData; ?>" />
		<input id="ProductBundlesBundleID" type="hidden" name="bundle" value="<?php echo $BundleNumber; ?>" />
        <div style="clear:both"></div> </form><br />
       
       	<div class="PB_colorbox_footer"><div class="PB_continue">
        <a id="ProductBundlesOptionsSubmitButton" class="button"><?php echo $Continue; ?></a></div></div>

		<script>
        $('#ProductBundlesOptionsSubmitButton').live('click', function(e){
           	     $.ajax({
                    url: 'index.php?route=module/productbundles/bundletocartoptions',
                    type: 'post',
                    data: $('#ProductBundlesOptionsForm').serialize(),
					dataType: 'json',
					success: function(json) {
						
						$('.error').remove();
						
						if (json['error']) {
							if (json['error']['option']) {
								for (i in json['error']['option']) {
									for (n in json['error']['option'][i]) {
										$('div[data-product-index="' + json['error']['option'][i][n].key + '"]').find('div[data-option-id=bundle_option-' + i + ']').after('<span class="error">' + json['error']['option'][i][n].message + '</span>');
									}
								}
							}
						}
						if (json['duplicate'] && !json['error']) {
							parent.location = "<?php echo html_entity_decode($this->url->link('checkout/cart', 'duplicated=true')); ?>";	
						}
						if (json['success']) {
							parent.location = "<?php echo html_entity_decode($this->url->link('checkout/cart')); ?>";	

						}
					}
                });
        });
        </script>
        
        <!-- Remove the line below if you have the error 'Maximum call stack size exceeded' -->
		<!--<script type="text/javascript" src="catalog/view/javascript/productbundles/pb_timepicker.js"></script> -->
        
        <script type="text/javascript"><!--
        $(document).ready(function() {
            if ($.browser.msie && $.browser.version == 6) {
                $('.PB_date, .PB_datetime, .PB_time').bgIframe();
            }
        
            $('.PB_date').datepicker({dateFormat: 'yy-mm-dd'});
            $('.PB_datetime').datetimepicker({
                dateFormat: 'yy-mm-dd',
                timeFormat: 'h:m'
            });
            $('.PB_time').timepicker({timeFormat: 'h:m'});
        });
        //--></script>
        
		<?php if ($options) { ?>
            <?php foreach ($options as $option) { ?>
                <?php if ($option['type'] == 'file') { ?>
                <script type="text/javascript"><!--
                new AjaxUpload('#button-option-<?php echo $option['product_option_id']; ?>', {
                    action: 'index.php?route=product/product/upload',
                    name: 'file',
                    autoSubmit: true,
                    responseType: 'json',
                    onSubmit: function(file, extension) {
                        $('#button-option-<?php echo $option['product_option_id']; ?>').after('<img src="catalog/view/theme/default/image/loading.gif" class="loading" style="padding-left: 5px;" />');
                        $('#button-option-<?php echo $option['product_option_id']; ?>').attr('disabled', true);
                    },
                    onComplete: function(file, json) {
                        $('#button-option-<?php echo $option['product_option_id']; ?>').attr('disabled', false);
                        
                        $('.error').remove();
                        
                        if (json['success']) {
                            alert(json['success']);
                            
                            $('input[name=\'option[<?php echo $option['product_option_id']; ?>]\']').attr('value', json['file']);
                        }
                        
                        if (json['error']) {
                            $('#option-<?php echo $option['product_option_id']; ?>').after('<span class="error">' + json['error'] + '</span>');
                        }
                        
                        $('.loading').remove();	
                    }
                });
                //--></script>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    <?php } ?>
<?php } ?>