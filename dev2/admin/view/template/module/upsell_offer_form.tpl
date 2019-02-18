<div class="content" style="width: 640px; height: 660px;">
	<div id="content-top" style="position: relative;">	
		<div id="form-tabs" class="htabs" style="margin-bottom: 0;">
			<a href="#tab-general"><?php echo $tab_general; ?></a>
			<a href="#tab-products"><?php echo $tab_products; ?></a>
			<a href="#tab-conditions"><?php echo $tab_conditions; ?></a>
		</div>
		<a class="button" id="save-form" style="position: absolute; top: 0; right: 10px; color:#FFF;"><span><?php echo $button_save_upsell_offer; ?></span></a>
	</div>
	
	<div id="tab-general" style="background: #FFF;">
	  <table class="form">
		<tr>
			<td class="left"><span class="required">*</span><?php echo $entry_name; ?></td>
			<td><input type="text" name="name" size="40" value="<?php echo $name; ?>" /><span class="error" id="error_name"></span></td>
		</tr>
          </table>
	  <div id="languages" class="htabs">
		<?php foreach ($languages as $language) { ?>
		<a href="#language<?php echo $language['language_id']; ?>"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
		<?php } ?>
          </div>
          <?php foreach ($languages as $language) { ?>
          <div id="language<?php echo $language['language_id']; ?>">
		<table class="form">
			<tr>
				<td><?php echo $entry_title; ?></td>
				<td><input type="text" name="upsell_offer_description[<?php echo $language['language_id']; ?>][title]" size="40" value="<?php echo isset($upsell_offer_description[$language['language_id']]) ? $upsell_offer_description[$language['language_id']]['title'] : ''; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo $entry_description; ?></td>
				<td><textarea name="upsell_offer_description[<?php echo $language['language_id']; ?>][description]" cols="40" rows="5"><?php echo isset($upsell_offer_description[$language['language_id']]) ? $upsell_offer_description[$language['language_id']]['description'] : ''; ?></textarea></td>
			</tr>
		</table>
          </div>
	  <?php } ?>
	</div>
	
	<div id="tab-products" style="background: #FFF;">
		<table class="form">
		  <tr>
		    <td><?php echo $entry_upsell_products; ?></td>
		    <td><input type="text" name="upsell_product" value="" /><span class="error" id="error_upsell_offer_product"></span></td>
		  </tr>
		  <tr>
		    <td><?php echo $text_upsell_products_help; ?></td>
		    <td><div id="upsell-offer-product" class="scrollbox">
			<?php $class = 'odd'; ?>
			<?php foreach ($upsell_products as $product) { ?>
			<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
			<div id="upsell-offer-product<?php echo $product['product_id']; ?>" class="<?php echo $class; ?>"><?php echo $product['name']; ?> <img src="view/image/delete.png" />
			  <input type="hidden" value="<?php echo $product['product_id']; ?>" />
			</div>
			<?php } ?>
		      </div>
		      <input type="hidden" name="upsell_offer_product" value="<?php echo $upsell_offer_product; ?>" /></td>
		  </tr>
		</table>	
	</div>
	  
	<div id="tab-conditions" style="background: #FFF;">
		<table class="form">
			<tr>
				<td class="left"><?php echo $entry_date_start; ?></td>
				<td><input type="text" class="date" name="date_start" value="<?php echo $date_start; ?>" /></td>
			</tr>
			<tr>
				<td class="left"><?php echo $entry_date_end; ?></td>
				<td><input type="text" class="date" name="date_end" value="<?php echo $date_end; ?>" /></td>
			</tr>
			<tr>
				<td class="left"><?php echo $entry_total_price_min; ?></td>
				<td><input type="text" name="total_price_min" value="<?php echo $total_price_min; ?>" /></td>
			</tr>
			<tr>
				<td class="left"><?php echo $entry_total_price_max; ?></td>
				<td><input type="text" name="total_price_max" value="<?php echo $total_price_max; ?>" /></td>
			</tr>
			<tr>
			    <td><?php echo $entry_product; ?></td>
			    <td><input type="text" name="product" value="" /></td>
			  </tr>
			  <tr>
			    <td><?php echo $text_cart_products_help; ?></td>
			    <td><div id="cart-product" class="scrollbox">
				<?php $class = 'odd'; ?>
				<?php foreach ($cart_products as $product) { ?>
				<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
				<div id="cart-product<?php echo $product['product_id']; ?>" class="<?php echo $class; ?>"><?php echo $product['name']; ?> <img src="view/image/delete.png" />
				  <input type="hidden" value="<?php echo $product['product_id']; ?>" />
				</div>
				<?php } ?>
			      </div>
			      <input type="hidden" name="cart_product" value="<?php echo $cart_product; ?>" /></td>
			  </tr>
			<tr>
			      <td><?php echo $entry_store; ?></td>
			      <td><div class="scrollbox">
				  <?php $class = 'even'; ?>
				  <div class="<?php echo $class; ?>">
				    <?php if (in_array(0, $upsell_offer_store)) { ?>
				    <input type="checkbox" name="upsell_offer_store[]" value="0" checked="checked" />
				    <?php echo $text_default; ?>
				    <?php } else { ?>
				    <input type="checkbox" name="upsell_offer_store[]" value="0" />
				    <?php echo $text_default; ?>
				    <?php } ?>
				  </div>
				  <?php foreach ($stores as $store) { ?>
				  <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
				  <div class="<?php echo $class; ?>">
				    <?php if (in_array($store['store_id'], $upsell_offer_store)) { ?>
				    <input type="checkbox" name="upsell_offer_store[]" value="<?php echo $store['store_id']; ?>" checked="checked" />
				    <?php echo $store['name']; ?>
				    <?php } else { ?>
				    <input type="checkbox" name="upsell_offer_store[]" value="<?php echo $store['store_id']; ?>" />
				    <?php echo $store['name']; ?>
				    <?php } ?>
				  </div>
				  <?php } ?>
				</div></td>
		        </tr>
		</table>		
	</div> 
</div>
<script type="text/javascript"><!--
$('#save-form').bind('click', function(){
	$.ajax({
		type: 'POST',
		url: '<?php echo $action; ?>',
		data: $('#dialog-form input, #dialog-form select, #dialog-form textarea'),
		dataType: 'json',
		success: function(json){
			$('.warning').remove();
			$('.success').remove();
			
			if (json['error']){
				if (json['error']['warning']){
					$('#content-top').before('<div class="warning">' + json['error']['warning'] + '</div>');
					$('.warning').fadeIn('slow');
				}
								
				if (json['error']['name']) {
					$('#error_name').html(json['error']['name']);
				} else {
					$('#error_name').html('');
				}
				
				if (json['error']['upsell_offer_product']) {
					$('#error_upsell_offer_product').html(json['error']['upsell_offer_product']);
				} else {
					$('#error_upsell_offer_product').html('');
				}
			}
			
			if (json['success']){
				$('#content-top').before('<div class="success">' + json['success'] + '</div>');
				$('.success').fadeIn('slow');
				
				$('#upsell-offers-list').load('index.php?route=module/upsell_offer/getList&token=<?php echo $token; ?>');
				
				setTimeout(function() {
					$('#dialog-form').dialog("close");
				}, 1000);
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
});
//--></script>
<script type="text/javascript"><!--
$('input[name=\'upsell_product\']').autocomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=module/upsell_offer/autocompleteProduct&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
						label: item.name,
						value: item.product_id
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('#upsell-offer-product' + ui.item.value).remove();
		
		$('#upsell-offer-product').append('<div id="upsell-offer-product' + ui.item.value + '">' + ui.item.label + '<img src="view/image/delete.png" /><input type="hidden" value="' + ui.item.value + '" /></div>');

		$('#upsell-offer-product div:odd').attr('class', 'odd');
		$('#upsell-offer-product div:even').attr('class', 'even');
		
		data = $.map($('#upsell-offer-product input'), function(element){
			return $(element).attr('value');
		});
						
		$('input[name=\'upsell_offer_product\']').attr('value', data.join());
					
		return false;
	},
	focus: function(event, ui) {
      	return false;
   	}
});

$('#upsell-offer-product div img').live('click', function() {
	$(this).parent().remove();
	
	$('#upsell-offer-product div:odd').attr('class', 'odd');
	$('#upsell-offer-product div:even').attr('class', 'even');

	data = $.map($('#upsell-offer-product input'), function(element){
		return $(element).attr('value');
	});
					
	$('input[name=\'upsell_offer_product\']').attr('value', data.join());	
});
//--></script>
<script type="text/javascript"><!--
$('input[name=\'product\']').autocomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=module/upsell_offer/autocompleteProduct&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
						label: item.name,
						value: item.product_id
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('#cart-product' + ui.item.value).remove();
		
		$('#cart-product').append('<div id="cart-product' + ui.item.value + '">' + ui.item.label + '<img src="view/image/delete.png" /><input type="hidden" value="' + ui.item.value + '" /></div>');

		$('#cart-product div:odd').attr('class', 'odd');
		$('#cart-product div:even').attr('class', 'even');
		
		data = $.map($('#cart-product input'), function(element){
			return $(element).attr('value');
		});
						
		$('input[name=\'cart_product\']').attr('value', data.join());
					
		return false;
	},
	focus: function(event, ui) {
      	return false;
   	}
});

$('#cart-product div img').live('click', function() {
	$(this).parent().remove();
	
	$('#cart-product div:odd').attr('class', 'odd');
	$('#cart-product div:even').attr('class', 'even');

	data = $.map($('#cart-product input'), function(element){
		return $(element).attr('value');
	});
					
	$('input[name=\'cart_product\']').attr('value', data.join());	
});
//--></script>
<script type="text/javascript"><!--
$(document).ready(function() {
	$('.date').datepicker({dateFormat: 'yy-mm-dd'});
});
//--></script>
<script type="text/javascript"><!--
$('#form-tabs a').tabs();
$('#languages a').tabs(); 
//--></script>