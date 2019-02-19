<table class="table">
  <tbody>
    <tr>
      <td class="col-xs-2">
      	<h5>Show random bundles:</h5>
        <span class="help"><i class="fa fa-info-circle"></i>&nbsp;If enabled, ProductBundles will display existing bundles randomly on the <strong>layouts</strong> where the module is enabled. Applies only on the pages with no associated bundles.</span>
      </td>
	  <td class="col-xs-10">
		<div class="col-xs-4">
            <select name="ProductBundles[ShowRandomBundles]" class="ProductBundlesShowRandomBundles form-control">
                <option value="yes" <?php echo (isset($data['ProductBundles']['ShowRandomBundles']) && $data['ProductBundles']['ShowRandomBundles'] == 'yes') ? 'selected=selected' : '' ?>>Enabled</option>
               <option value="no" <?php echo (isset($data['ProductBundles']['ShowRandomBundles']) && $data['ProductBundles']['ShowRandomBundles'] == 'no') ? 'selected=selected' : '' ?>>Disabled</option>
            </select>
        </div>
      </td>
    </tr>
  </tbody>
</table>
<?php $token = $_GET['token']; ?>
<table id="module" class="table table-bordered table-hover" width="100%" >
	<thead>
		<tr class="table-header">
			<td class="left" width="20%"><strong>Product Bundles:</strong><br/><i class="icon-info-sign"></i> Here you should add the products that you want to offer in a bundle. <strong>NOTE:</strong> Products with <i class="fa fa-tags" style="color:#ab9a87;font-size:14px;"></i> have options.</td>
			<td class="left" width="17%"><strong>Bundle Details:</strong><br/><i class="icon-info-sign"></i> Choose the discount you want to apply to a given bundle.</td>
            <td class="left" width="50%"><strong>Display Positions:</strong><br/><i class="icon-info-sign"></i> Choose the products/categories where you want the bundle to be displayed. <br /><strong>NOTE:</strong> If there is more than one bundle associated with a product/category, they will show up randomly.</td>
            <td width="8%"><strong>Actions:</strong><br/><i class="icon-info-sign"></i> Remove or add bundles.</td>
		</tr>
	</thead>
	<?php $module_row = 0; ?>
	<?php if (isset($CustomBundles)) { 
			foreach ($CustomBundles as $module) { ?>
           	 <?php if (!isset($module['id'])) { $module['id']=mt_rand(10000, 99999);} ?>
				<tbody id="module-row<?php echo $module['id']; ?>">
					<tr>
						<td class="left">
                       		<input type="hidden" class="bundle_id" name="productbundles_custom[<?php echo $module['id']; ?>][id]" value="<?php echo $module['id']; ?>" />
                            <span style="vertical-align:middle;">Add product:</span> <input type="text" name="productsInput" class="form-control" style="width:240px;display: inline-block;margin-bottom:5px;" value="" />
							<div id="product-bundle_<?php echo $module['id']; ?>" class="scrollbox first">
								<?php $class1 = 'odd'; ?>
								<?php if (!empty($module['products'])) {
									foreach ($module['products'] as $pr) { ?>
										 <?php $class1 = ($class1 == 'even' ? 'odd' : 'even'); ?>
										 <?php $product = $this->model_catalog_product->getProduct($pr); ?>
										 <?php $product_options = $this->model_catalog_product->getProductOptions($pr); ?>
										 <?php $product_specials = $this->model_catalog_product->getProductSpecials($pr); 
										 $special = false;
										 foreach ($product_specials  as $product_special) {
											if (($product_special['date_start'] == '0000-00-00' || $product_special['date_start'] < date('Y-m-d')) && ($product_special['date_end'] == '0000-00-00' || $product_special['date_end'] > date('Y-m-d'))) {
												$special = $product_special['price'];
												break;
											}					
									}
								?>
								<?php $final_price = ($special) ? $special : $product['price'] ?>
								<div id="product-bundle_<?php echo $module['id']; ?>_<?php echo $pr; ?>" class="<?php echo $class1; ?>"> 
									<?php if (!empty($product_options)) echo '<i class="fa fa-tags" style="color:#ab9a87;font-size:13px;"></i> '; ?><?php echo $product['name']; ?> - <?php echo $this->currency->format($final_price); ?><i class="fa fa-minus-circle removeIcon" product_price="<?php echo $final_price ?>"></i>
									<input type="hidden" name="productbundles_custom[<?php echo $module['id']; ?>][products][]" value="<?php echo $pr; ?>" />
								</div>
									<?php }
                                } ?>
        					</div>
						</td>
          				<td class="left">
          					<div id="product-bundle-prices_<?php echo $module['id']; ?>">
              					<h5>Total Price: <?php if ($currencyAlignment=="L") {  echo $currency; } ?><span id="product-bundle-totalprice_<?php echo $module['id']; ?>"><?php echo (!empty($module['totalprice'])) ? $module['totalprice'] : '0' ; ?></span><input type="hidden" name="productbundles_custom[<?php echo $module['id']; ?>][totalprice]" value="<?php echo (!empty($module['totalprice'])) ? $module['totalprice'] : '0' ; ?>" /><?php if ($currencyAlignment=="R") {  echo $currency; } ?></h5>
								<h5>Bundle Price: <?php if ($currencyAlignment=="L") {  echo $currency; } ?><span id="product-bundle-price_<?php echo $module['id']; ?>"><?php echo (!empty($module['price'])) ? $module['price'] : '0' ; ?></span><input type="hidden" name="productbundles_custom[<?php echo $module['id']; ?>][price]" value="<?php echo (!empty($module['price'])) ? $module['price'] : '0' ; ?>" /><?php if ($currencyAlignment=="R") {  echo $currency; } ?></h5>
								<br />
                                <h5>Discount:</h5>
                                 <div class="col-xs-8" style="float:none;margin:0px;padding:0px;">
									<?php if ($currencyAlignment=="L") {  ?>
                                        <div class="input-group">
                                          <span class="input-group-addon"><?php echo $currency; ?></span>
                                          <input class="input-mini voucherPrice form-control" name="productbundles_custom[<?php echo $module['id']; ?>][voucherprice]" id="product-bundle-voucherprice<?php echo $module['id']; ?>" type="text" value="<?php echo (!empty($module['voucherprice'])) ? $module['voucherprice'] : '0' ; ?>">
                                        </div>
                                    <?php } else { ?>
                                        <div class="input-group">
                                          <input class="input-mini voucherPrice form-control" name="productbundles_custom[<?php echo $module['id']; ?>][voucherprice]" id="product-bundle-voucherprice<?php echo $module['id']; ?>" type="text" value="<?php echo (!empty($module['voucherprice'])) ? $module['voucherprice'] : '0' ; ?>">
                                          <span class="input-group-addon"><?php echo $currency; ?></span>
                                        </div>
                                    <?php } ?>
              					</div>
							</div>
   						</td>
   						<td class="left" style="vertical-align:middle;">
                        	<div style="float:left;padding-right:15px;padding-bottom:5px;">
                                <span style="vertical-align:middle;">&nbsp;Product:</span> <input type="text" name="productsShow_Input" class="form-control" style="width:213px;display: inline-block;margin-bottom:5px;" value="" />
                                <div id="product-bundle-productsShow_<?php echo $module['id']; ?>" class="scrollbox second" style="width:265px;">
                                    <?php $class1 = 'odd'; ?>
                                    <?php if (!empty($module['productsShow'])) {
                                        foreach ($module['productsShow'] as $pr) { ?>
                                             <?php $class1 = ($class1 == 'even' ? 'odd' : 'even'); ?>
                                             <?php $product = $this->model_catalog_product->getProduct($pr); ?>
                                             <?php $product_options = $this->model_catalog_product->getProductOptions($pr); ?>
                                             <?php $product_specials = $this->model_catalog_product->getProductSpecials($pr); 
                                             $special = false;
                                             foreach ($product_specials  as $product_special) {
                                                if (($product_special['date_start'] == '0000-00-00' || $product_special['date_start'] < date('Y-m-d')) && ($product_special['date_end'] == '0000-00-00' || $product_special['date_end'] > date('Y-m-d'))) {
                                                    $special = $product_special['price'];
                                                    break;
                                                }					
                                            }
                                    ?>
                                    <?php $final_price = ($special) ? $special : $product['price'] ?>
									<div id="product-bundle-productsShow_<?php echo $module['id']; ?>_<?php echo $pr; ?>" class="<?php echo $class1; ?>"><?php echo $product['name']; ?><i class="fa fa-minus-circle removeIcon" product_price="<?php echo $final_price ?>"></i>
                                        <input type="hidden" name="productbundles_custom[<?php echo $module['id']; ?>][productsShow][]" value="<?php echo $pr; ?>" />
                                    </div>
                                        <?php }
                                    } ?>
                                </div>
                            </div>
                            <div style="float:left;">
                                <span style="vertical-align:middle;">Category:</span> <input type="text" name="categoriesShow_Input" class="form-control" style="width:210px;display: inline-block;margin-bottom:5px;" value="" />
								<div id="product-bundle-categoriesShow_<?php echo $module['id']; ?>" class="scrollbox third" style="width:265px;">
									  <?php $class = 'odd'; ?>
                                      <?php if (!empty($module['categoriesShow'])) {
										  foreach ($module['categoriesShow'] as $product_category) { ?>
                                      		<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                                            <?php $category_info = $this->model_catalog_category->getCategory($product_category);
											$CategoryName = $category_info['path'] ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']; ?>
                                      		<div id="product-bundle-categoriesShow_<?php echo $module['id']; ?>_<?php echo $product_category; ?>" class="<?php echo $class; ?>"><?php echo $CategoryName; ?><i class="fa fa-minus-circle removeIcon"></i>
                                        		<input type="hidden" name="productbundles_custom[<?php echo $module['id']; ?>][categoriesShow][]" value="<?php echo $product_category; ?>" />
                                      		</div>
										  <?php }
									  } ?>
								</div>
                            </div>
   						</td>
         			<td class="left" style="vertical-align:bottom;"><a onclick="$('#module-row<?php echo $module['id']; ?>').remove();" class="btn btn-small btn-danger" style="text-decoration:none;"><i class="fa fa-times"></i>&nbsp;<?php echo $button_remove; ?></a></td>
       			</tr>
      		</tbody>
			<?php $module_row++; ?>
     		<?php } } ?>
    <tfoot>
        <tr>
            <td colspan="3"></td>
            <td class="left"><a onclick="addModule();" class="btn btn-small btn-primary"><i class="fa fa-plus"></i>&nbsp;Add New</a></td>
        </tr>
    </tfoot>
</table>
 
<script>
function addModule() {
	var module_row=Math.floor(Math.random() * 99999) + 10000;
	html  = '<tbody style="display:none;" id="module-row' + module_row + '">';
	html += '  <tr>';
	html += '    <td class="left">';
	html += '<input type="hidden" class="bundle_id" name="productbundles_custom[' + module_row + '][id]" value="' + module_row + '" />';
	html += '<span style="vertical-align:middle;">Add product:</span> <input type="text" name="productsInput" class="form-control" style="width:240px;display: inline-block;margin-bottom:5px;" value="" />';
	html += '<div id="product-bundle_' + module_row + '" class="scrollbox first">';
	html += '</div>';	
	html += ' ';
	html += '    </td>';
	html += '    <td class="left">';
	html += '<h5>Total Price: <?php if ($currencyAlignment=="L") {  echo $currency; } ?><span id="product-bundle-totalprice_' + module_row + '">0.0</span><input type="hidden" name="productbundles_custom[' + module_row + '][totalprice]" value="0.0" /><?php if ($currencyAlignment=="R") {  echo $currency; } ?></h5>';
	html += '<h5>Bundle Price: <?php if ($currencyAlignment=="L") {  echo $currency; } ?><span id="product-bundle-price_' + module_row + '">0.0</span><input type="hidden" name="productbundles_custom[' + module_row + '][price]" value="0" /><?php if ($currencyAlignment=="R") {  echo $currency; } ?></h5>';
	html += '<br /><h5>Discount:</h5>';
	<?php if ($currencyAlignment=="L") {  ?>
	html += '<div class="input-group">';
	html += '<span class="input-group-addon"><?php echo $currency; ?></span>';
	html += '<input class="input-mini voucherPrice form-control" name="productbundles_custom[' + module_row + '][voucherprice]" id="product-bundle-voucherprice' + module_row + '" type="text" value="0">';
	html += '</div>';
<?php } else { ?>
	html += '<div class="input-group">';
	html += '<input class="input-mini voucherPrice form-control" name="productbundles_custom[' + module_row + '][voucherprice]" id="product-bundle-voucherprice' + module_row + '" type="text" value="0">';
	html += '<span class="input-group-addon"><?php echo $currency; ?></span>';
	html += '</div>';
<?php } ?>
	html += '    </td>';
	html += '    <td class="left">';
	html += '		<div style="float:left;padding-right:15px;padding-bottom:5px;"><span style="vertical-align:middle;">Product:</span> <input type="text" name="productsShow_Input" class="form-control" style="width:213px;display: inline-block;margin-bottom:5px;" value="" />';
	html += '			<div id="product-bundle-productsShow_' + module_row + '" class="scrollbox second" style="width:265px;"></div>';
	html += '		</div>';	
	html += '		<div style="float:left;padding-right:12px;padding-bottom:5px;"><span style="vertical-align:middle;">Category:</span> <input type="text" name="categoriesShow_Input" class="form-control" style="width:210px;display: inline-block;margin-bottom:5px;" value="" />';
	html += '			<div id="product-bundle-categoriesShow_' + module_row + '" class="scrollbox third" style="width:265px;"></div>';
	html += '		</div>';	
	html += '    </td>';
	html += '    <td class="left" style="vertical-align:bottom;"><a onclick="$(\'#module-row' + module_row + '\').remove();" class="btn btn-small btn-danger" style="text-decoration:none;"><i class="fa fa-times"></i>&nbsp;<?php echo $button_remove; ?></a></td>';
	html += '  </tr>';
	html += '</tbody>';
	
	$('#module tfoot').before(html);
	$('#module-row' + module_row).fadeIn();
	initializeAutocomplete();
}

// Add Products
var initializeAutocomplete = function () {
	// Calculate Bundle Price
	$('.voucherPrice').live('keyup',function(){
		var bundle_id = $(this).parents('tr').find('.bundle_id').val();
		if ($('#product-bundle-voucherprice' + bundle_id).val) {
			var VoucherPrice = parseFloat( $('#product-bundle-totalprice_' + bundle_id).html() ) - parseFloat($('#product-bundle-voucherprice'+ bundle_id).val()).toFixed(2);
			$('#product-bundle-price_' + bundle_id).html(VoucherPrice);
			$('input[name=\'productbundles_custom['+bundle_id+'][price]\']').val(VoucherPrice);
		}
	});

	$('input[name=\'productsInput\']').autocomplete({
		delay: 500,
		source: function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
				dataType: 'json',
				success: function(json) {		
					response($.map(json, function(item) {
						return {
							label: item.name,
							value: item.product_id,
							price: item.price,
							special: item.special,
							option: item.option
						}
					}));
				}
			});
		}, 
		select: function(event, ui) {
			var bundle_id = $(this).parents('tr').find('.bundle_id').val();
			
			var textOption="";
			if (ui.item.option!="")
			{
				textOption = '<i class="fa fa-tags" style="color:#ab9a87;font-size:13px;"></i> ';	
			}
			var real_price=0;
			if (ui.item.special==0) { real_price=ui.item.price } else { real_price=ui.item.special; }
			
		//	$(this).parent().find('#product-bundle_' + bundle_id + '_' + ui.item.value).remove();
			
			$(this).parent().find('.scrollbox').append('<div id="product-bundle_' + bundle_id + '_' + ui.item.value + '">' + textOption + ui.item.label + ' - <?php if ($currencyAlignment=="L") {  echo $currency; } ?>' + parseFloat(real_price).toFixed(2) + '<?php if ($currencyAlignment=="R") {  echo $currency; } ?><i class="fa fa-minus-circle removeIcon" product_price="' + real_price + '"></i><input type="hidden" name="productbundles_custom['+bundle_id+'][products][]" value="' + ui.item.value +'" /></div>');
			
			$(this).parent().find('#product-bundle_' + bundle_id + ' div:odd').attr('class', 'odd');
			$(this).parent().find('#product-bundle_' + bundle_id + ' div:even').attr('class', 'even');					
			
			var TotalPrice = ( parseFloat( $('#product-bundle-totalprice_' + bundle_id).html() ) + parseFloat(real_price) ).toFixed(2);
			$('#product-bundle-totalprice_' + bundle_id).html(TotalPrice);
			$('input[name=\'productbundles_custom[' + bundle_id + '][totalprice]\']').val(TotalPrice);
			
			return false;
		},
		focus: function(event, ui) {
		  return false;
		}
	});
	
	$('.scrollbox.first div .removeIcon').live('click', function() {
		var bundle_id = $(this).parents('tr').find('.bundle_id').val();
		var remove_price = ($(this).attr("product_price"));
		$(this).parent().remove();
		var RemovePrice = ( parseFloat( $('#product-bundle-totalprice_' + bundle_id).html() )-parseFloat(remove_price) ).toFixed(2);
		$('#product-bundle-totalprice_' + bundle_id).html(RemovePrice);
		$('input[name=\'productbundles_custom[' + bundle_id + '][totalprice]\']').val(RemovePrice);
		$(this).parent().find('#product-bundle_' + bundle_id + ' div:odd').attr('class', 'odd');
		$(this).parent().find('#product-bundle_' + bundle_id + ' div:even').attr('class', 'even');	
	});
	
	// Show in Products
	$('input[name=\'productsShow_Input\']').autocomplete({
		delay: 500,
		source: function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
				dataType: 'json',
				success: function(json) {		
					response($.map(json, function(item) {
						return {
							label: item.name,
							value: item.product_id,
							price: item.price,
							special: item.special
						}
					}));
				}
			});
		}, 
		select: function(event, ui) {
			var bundle_id = $(this).parents('tr').find('.bundle_id').val();
			$(this).parent().find('#product-bundle-productsShow_' + bundle_id + '_' + ui.item.value).remove();
			$(this).parent().find('.second').append('<div id="product-bundle-productsShow_' + bundle_id + '_' + ui.item.value + '">' + ui.item.label + '<i class="fa fa-minus-circle removeIcon"></i><input type="hidden" name="productbundles_custom[' + bundle_id + '][productsShow][]" value="' + ui.item.value +'" /></div>');
			$(this).parent().find('#product-bundle-productsShow_' + bundle_id + ' div:odd').attr('class', 'odd');
			$(this).parent().find('#product-bundle-productsShow_' + bundle_id + ' div:even').attr('class', 'even');		
			return false;
		},
		focus: function(event, ui) {
		  return false;
	   }
	});
				
	$('.scrollbox.second div .removeIcon').live('click', function() {
		var bundle_id = $(this).parents('tr').find('.bundle_id').val();
		$(this).parent().remove();
		$(this).parent().find('#product-bundle-productsShow_' + bundle_id + ' div:odd').attr('class', 'odd');
		$(this).parent().find('#product-bundle-productsShow_' + bundle_id + ' div:even').attr('class', 'even');	
	});
	
	// Show in Categories
	$('input[name=\'categoriesShow_Input\']').autocomplete({
		delay: 500,
		source: function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
				dataType: 'json',
				success: function(json) {		
					response($.map(json, function(item) {
						return {
							label: item.name,
							value: item.category_id
						}
					}));
				}
			});
		}, 
		select: function(event, ui) {
			var bundle_id = $(this).parents('tr').find('.bundle_id').val();
			$(this).parent().find('#product-bundle-categoriesShow_' + bundle_id + '_' + ui.item.value).remove();
			$(this).parent().find('.third').append('<div id="product-bundle-categoriesShow_' + bundle_id + '_' + ui.item.value + '">' + ui.item.label + '<i class="fa fa-minus-circle removeIcon"></i><input type="hidden" name="productbundles_custom[' + bundle_id + '][categoriesShow][]" value="' + ui.item.value +'" /></div>');
			$(this).parent().find('#product-bundle-categoriesShow_' + bundle_id + ' div:odd').attr('class', 'odd');
			$(this).parent().find('#product-bundle-categoriesShow_' + bundle_id + ' div:even').attr('class', 'even');	
			return false;
		},
		focus: function(event, ui) {
		  return false;
	   }
	});
	
	$('.scrollbox.third div .removeIcon').live('click', function() {
		var bundle_id = $(this).parents('tr').find('.bundle_id').val();
		$(this).parent().remove();
		$(this).parent().find('#product-bundle-categoriesShow_' + bundle_id + ' div:odd').attr('class', 'odd');
		$(this).parent().find('#product-bundle-categoriesShow_' + bundle_id + ' div:even').attr('class', 'even');	
	});
}
initializeAutocomplete();
</script>