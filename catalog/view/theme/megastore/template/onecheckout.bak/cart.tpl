<?php echo $header; ?>
<div class="container"><?php echo $column_left; ?><?php echo $column_right; ?>
  <div id="content"><?php echo $content_top; ?>
    <div class="breadcrumb">
      <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
      <?php } ?>
    </div>
    <h1><?php echo $heading_title; ?>
      <?php if ($weight) { ?>
      &nbsp;(<?php echo $weight; ?>)
      <?php } ?>
    </h1>
    <?php if ($attention) { ?>
    <div class="attention"><?php echo $attention; ?></div>
    <?php } ?>    
    <?php if ($success) { ?>
    <div class="success"><?php echo $success; ?></div>
    <?php } ?>
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
      <div class="cart-info">
        <table>
          <thead>
            <tr>
              <td class="remove" align="center"><?php echo $column_remove; ?></td>
              <td class="image"><?php echo $column_image; ?></td>
              <td class="name"><?php echo $column_name; ?></td>
              <td class="model"><?php echo $column_model; ?></td>
              <td class="quantity"><?php echo $column_quantity; ?></td>
              <td class="price"><?php echo $column_price; ?></td>
              <td class="total"><?php echo $column_total; ?></td>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product) { ?>
            <tr>
              <td class="remove" align="center" valign="middle"><img src="catalog/view/theme/default/image/cancel.png" class="removeimg" /><input type="hidden" name="remove[]" value="<?php echo $product['key']; ?>" /></td>
              <td class="image"><?php if ($product['thumb']) { ?>
                <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" /></a>
                <?php } ?></td>
              <td class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                <?php if (!$product['stock']) { ?>
                <span class="stock">***</span>
                <?php } else { ?>
				<span class="stock"></span>
				<?php } ?>
                <div>
                  <?php foreach ($product['option'] as $option) { ?>
                  - <small><?php echo $option['name']; ?>: <?php echo $option['value']; ?></small><br />
                  <?php } ?>
                </div>
				<?php if (isset($product['points']) && $product['points']) { ?>
                <small class="points"><?php echo $product['points']; ?></small>
				<?php } ?>
				</td>
              <td class="model"><?php echo $product['model']; ?></td>
              <td class="quantity"><img src="catalog/view/theme/default/image/plus.png" class="plusimg"<?php echo $imgabsmiddle; ?> />&nbsp;
			  <input type="text" name="quantity[<?php echo $product['key']; ?>]" value="<?php echo $product['quantity']; ?>" size="1" style="width:24px;" />&nbsp;
			  <img src="catalog/view/theme/default/image/subtract.png" class="subtractimg"<?php echo $imgabsmiddle; ?> /></td>
              <td class="price"><?php echo $product['price']; ?></td>
              <td class="total"><?php echo $product['total']; ?></td>
            </tr>
            <?php } ?>
            <?php foreach ($vouchers as $voucher) { ?>
            <tr>
              <td class="remove" align="center" valign="middle"><img src="catalog/view/theme/default/image/cancel.png" class="removeimg" /><input type="hidden" name="voucher[]" value="<?php echo $voucher['key']; ?>" /></td>
              <td class="image"></td>
              <td class="name"><?php echo $voucher['description']; ?></td>
              <td class="model"></td>
              <td class="quantity">1</td>
              <td class="price"><?php echo $voucher['amount']; ?></td>
              <td class="total"><?php echo $voucher['amount']; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
	  <?php if($shipping_status) { ?>
    <div class="cart-module">
  <div class="cart-heading"><?php echo $shipping_heading_title; ?></div>
  <div class="cart-content">
    <p><?php echo $text_shipping; ?></p>
    <table id="shipping">
      <tr>
        <td><span class="required">*</span> <?php echo $entry_country; ?></td>
        <td><select name="country_id">
            <option value=""><?php echo $text_select; ?></option>
            <?php foreach ($countries as $country) { ?>
            <?php if ($country['country_id'] == $country_id) { ?>
            <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
            <?php } ?>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <td><span class="required">*</span> <?php echo $entry_zone; ?></td>
        <td><select name="zone_id">
          </select></td>
      </tr>
      <tr>
        <td><span id="postcode-required" class="required">*</span> <?php echo $entry_postcode; ?></td>
        <td><input type="text" name="postcode" value="<?php echo $postcode; ?>" /></td>
      </tr>
    </table>
    <a id="button-quote" class="button"><span><?php echo $button_quote; ?></span></a>
    <div id="quote" style="display: none;"></div>
    <input type="hidden" name="shipping_method" value="<?php echo $code; ?>" />
  </div>
</div>
	<?php } ?>
	<?php echo $cartmodule; ?>
    <div class="cart-total">
      <table>
        <?php foreach ($totals as $total) { ?>
        <tr>
          <td colspan="5"></td>
          <td class="right"><b><?php echo $total['title']; ?>:</b></td>
          <td class="right"><?php echo $total['text']; ?></td>
        </tr>
        <?php } ?>
      </table>
    </div>
    <div class="buttons">
      <div class="left"><a href="<?php echo $continue; ?>" class="button"><span><?php echo $button_shopping; ?></span></a></div>
      <div class="right"><a href="<?php echo $checkout; ?>" class="button"><span><?php echo $button_checkout; ?></span></a></div>
    </div>
    <?php echo $content_bottom; ?></div>
</div>
<script type="text/javascript"><!--
$('.cart-info img').css('cursor','pointer');

$('.cart-info .removeimg').bind('click', function() {
	var imgobj = $(this);	
	$.ajax({
		url: 'index.php?route=onecheckout/cart/remove',
		type: 'post',
		data: imgobj.parent().find('input[type=\'hidden\']'),
		dataType: 'json',
		success: function(json) {
			$('#content h1').html(json['cart_heading']);
			$('#cart_total').html(json['header_cart_total']);
			$('#cart-total').html(json['header_cart_total']);
			if(json['error_warning']){
				$('.warning').remove();
				$('.cart-info').before('<div class="warning" style="display: none;">'+json['error_warning']+'</div>');
				$('.warning').fadeIn('slow');
			}else{
				$('.warning').fadeOut('slow');
			}
				if(json['cart_total']){
					html='';
					for(i in json['totals']){
						html+='<tr>';
						html+='<td colspan="5"></td>';
						html+='<td class="right"><b>'+json['totals'][i]['title']+':</b></td>';
						html+='<td class="right">'+json['totals'][i]['text']+'</td>';
						html+='</tr>';
					}
					$('.cart-total table').fadeOut(1000,function(){$('.cart-total table tr').remove();$('.cart-total table').append(html).fadeIn(1000);});
					imgobj.parent().parent().fadeOut('slow');
				}else{
					$('.cart-info, .cart-module, .cart-total, .buttons .left').slideUp();
					$('.buttons .right a').attr('href',json['continue']).children('span').html(json['button_continue']);
					$('.buttons').before(json['cart_empty']);
					$('#content .content').slideDown();					
				}
			
		}
	});
});

$('.cart-info .plusimg').bind('click', function() {
	$(this).parent().find('input[name*=\'quantity\']').val(Number($(this).parent().find('input[name*=\'quantity\']').val())+1);	
	updatequantity(this);													
});
$('.cart-info .subtractimg').bind('click', function() {
	if(Number($(this).parent().find('input[name*=\'quantity\']').val())>1){
		$(this).parent().find('input[name*=\'quantity\']').val(Number($(this).parent().find('input[name*=\'quantity\']').val())-1);
		updatequantity(this);
	}
});

$('input[name*=\'quantity\']').change(function(){
	updatequantity(this);
});

$('input[name*=\'quantity\']').keydown(function(e){
	if(e.which!=8&&e.which!=0&&(e.which<48||e.which>57)&&(e.which<96||e.which>105)&&e.which!=46){
		return false;
	}
});

function updatequantity(thisobj){	
	var imgobj = $(thisobj);
	$.ajax({
		url:'index.php?route=onecheckout/cart/quantity',
		type:'post',
		data:imgobj.parent().find('input[name*=\'quantity\']'),
		dataType:'json',
		success:function(json){
			$('#content h1').html(json['cart_heading']);
			$('#cart_total').html(json['header_cart_total']);
			$('#cart-total').html(json['header_cart_total']);
			imgobj.parent().find('input[name*=\'quantity\']').val(json['quantity']);
			imgobj.parent().parent().find('.total').html(json['total']);
			imgobj.parent().parent().find('.price').html(json['price']);
			if(json['points']){
				imgobj.parent().parent().find('.points').html(json['points'])
			}
			if(!json['stock']){
				imgobj.parent().parent().find('.stock').html('***')
			}else{
				imgobj.parent().parent().find('.stock').html('')
			}
			if(json['error_warning']){
				$('.warning').remove();
				$('.cart-info').before('<div class="warning" style="display: none;">'+json['error_warning']+'</div>');				
				$('.warning').fadeIn('slow');
			}else{
				$('.warning').fadeOut('slow');
			}
				html='';
				for(i in json['totals']){
					html+='<tr>';
					html+='<td colspan="5"></td>';
					html+='<td class="right"><b>'+json['totals'][i]['title']+':</b></td>';
					html+='<td class="right">'+json['totals'][i]['text']+'</td>';
					html+='</tr>';
				}
				$('.cart-total table').fadeOut(1000,function(){$('.cart-total table tr').remove();$('.cart-total table').append(html).fadeIn(1000);});
			
		}
	});
}
$('#button-voucher').bind('click', function() {
	$.ajax({
		type: 'POST',
		url: 'index.php?route=onecheckout/cartmodule/validateVoucher',
		data: $('#voucher :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('.success, .warning').remove();
			$('#button-voucher').attr('disabled', true);
			$('#button-voucher').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('#button-voucher').attr('disabled', false);
			$('.wait').remove();
		},		
		success: function(json) {
			if (json['error']) {
				$('.cart-info').before('<div class="warning">' + json['error'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
			}
			
			if (json['redirect']) {
				location = json['redirect'];
			}
		}
	});
});
$('#button-reward').bind('click', function() {
	$.ajax({
		type: 'POST',
		url: 'index.php?route=onecheckout/cartmodule/validateReward',
		data: $('#reward :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('.success, .warning').remove();
			$('#button-reward').attr('disabled', true);
			$('#button-reward').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('#button-reward').attr('disabled', false);
			$('.wait').remove();
		},		
		success: function(json) {
			if (json['error']) {
				$('.cart-info').before('<div class="warning">' + json['error'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
			}
			
			if (json['redirect']) {
				location = json['redirect'];
			}
		}
	});
});
$('#button-coupon').bind('click', function() {
	$.ajax({
		type: 'POST',
		url: 'index.php?route=onecheckout/cartmodule/validateCoupon',
		data: $('#coupon :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('.success, .warning').remove();
			$('#button-coupon').attr('disabled', true);
			$('#button-coupon').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('#button-coupon').attr('disabled', false);
			$('.wait').remove();
		},		
		success: function(json) {
			if (json['error']) {
				$('.cart-info').before('<div class="warning">' + json['error'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
			}
			
			if (json['redirect']) {
				location = json['redirect'];
			}
		}
	});
});
//--></script>
<?php if($shipping_status) { ?>
<script type="text/javascript"><!--
$('#button-quote').bind('click', function() {
	$.ajax({
		type: 'POST',
		url: 'index.php?route=onecheckout/cartmodule/quote',
		data: 'country_id=' + $('select[name=\'country_id\']').val() + '&zone_id=' + $('select[name=\'zone_id\']').val() + '&postcode=' + encodeURIComponent($('input[name=\'postcode\']').val()),
		dataType: 'json',		
		beforeSend: function() {
			$('.success, .warning').remove();
			$('#button-quote').attr('disabled', true);
			$('#button-quote').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('#button-quote').attr('disabled', false);
			$('.wait').remove();
		},		
		success: function(json) {
			$('.error').remove();

			if (json['redirect']) {
				location = json['redirect'];
			}
						
			if (json['error']) {
				if (json['error']['warning']) {
					$('.cart-info').before('<div class="warning">' + json['error']['warning'] + '</div>');
				}
				
				if (json['error']['country']) {
					$('#shipping select[name=\'country_id\']').after('<span class="error">' + json['error']['country'] + '</span>');
				}	
				
				if (json['error']['zone']) {
					$('#shipping select[name=\'zone_id\']').after('<span class="error">' + json['error']['zone'] + '</span>');
				}
				
				if (json['error']['postcode']) {
					$('#shipping input[name=\'postcode\']').after('<span class="error">' + json['error']['postcode'] + '</span>');
				}					
			}
			
			if (json['shipping_methods']) {
				html  = '<br />';
				html += '<table width="100%" cellpadding="3">';
				
				for (i in json['shipping_methods']) {
					html += '<tr>';
					html += '  <td colspan="3"><b>' + json['shipping_methods'][i]['title'] + '</b></td>';
					html += '</tr>';
				
					if (!json['shipping_methods'][i]['error']) {
						for (j in json['shipping_methods'][i]['quote']) {
							html += '<tr>';
							
							if (json['shipping_methods'][i]['quote'][j]['code'] == $('input[name=\'shipping_method\']').attr('value')) {
								html += '<td width="1"><input type="radio" name="shipping_method" value="' + json['shipping_methods'][i]['quote'][j]['code'] + '" id="' + json['shipping_methods'][i]['quote'][j]['code'] + '" checked="checked" /></td>';
							} else {
								html += '<td width="1"><input type="radio" name="shipping_method" value="' + json['shipping_methods'][i]['quote'][j]['code'] + '" id="' + json['shipping_methods'][i]['quote'][j]['code'] + '" /></td>';
							}
								
							html += '  <td><label for="' + json['shipping_methods'][i]['quote'][j]['code'] + '">' + json['shipping_methods'][i]['quote'][j]['title'] + '</label></td>';
							html += '  <td width="1"><label for="' + json['shipping_methods'][i]['quote'][j]['code'] + '">' + json['shipping_methods'][i]['quote'][j]['text'] + '</label></td>';
							html += '</tr>';
						}		
					} else {
						html += '<tr>';
						html += '  <td colspan="3"><div class="error">' + json['shipping_methods'][i]['error'] + '</div></td>';
						html += '</tr>	';						
					}
				}
				
				html += '</table>';
				html += '<br /><a id="button-shipping" class="button"><span><?php echo $button_shipping; ?></span></a>';		
		
				$('#quote').html(html);	
			
				$('#quote').slideDown('slow');
			}
		}
	});
});

$('#button-shipping').live('click', function() {
	$.ajax({
		type: 'POST',
		url: 'index.php?route=onecheckout/cartmodule/validateShipping',
		data: 'shipping_method=' + $('input[name=\'shipping_method\']:checked').attr('value'),
		dataType: 'json',		
		beforeSend: function() {
			$('.warning').remove();
			$('#button-shipping').attr('disabled', true);
			$('#button-shipping').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('#button-shipping').attr('disabled', false);
			$('.wait').remove();
		},		
		success: function(json) {
			if (json['error']) {
				$('#shipping').before('<div class="warning">' + json['error'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
			}
			
			if (json['redirect']) {
				location = json['redirect'];
			}
		}
	});
});

$('select[name=\'country_id\']').bind('change', function() {
	$.ajax({
		url: 'index.php?route=onecheckout/checkout/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('.wait').remove();
		},			
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('#postcode-required').show();
			} else {
				$('#postcode-required').hide();
			}
			
			html = '<option value=""><?php echo $text_select; ?></option>';
			
			if (json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
        			html += '<option value="' + json['zone'][i]['zone_id'] + '"';
	    			
					if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
	      				html += ' selected="selected"';
	    			}
	
	    			html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}
			
			$('select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('select[name=\'country_id\']').trigger('change');
//--></script> 
<?php } ?>
<?php echo $footer; ?>