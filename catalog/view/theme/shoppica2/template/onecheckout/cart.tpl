<?php echo $header; ?>

  <!-- ---------------------- -->
  <!--     I N T R O          -->
  <!-- ---------------------- -->
  <div id="intro">
    <div id="intro_wrap">
      <div class="s_wrap">
        <div id="breadcrumbs" class="s_col_12">
          <?php foreach ($breadcrumbs as $breadcrumb): ?>
          <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
          <?php endforeach; ?>
        </div>
        <h1><?php echo $heading_title; ?><?php if ($weight): ?> (<?php echo $weight; ?>)<?php endif; ?></h1>
      </div>
    </div>
  </div>
  <!-- end of intro -->

  <!-- ---------------------- -->
  <!--      C O N T E N T     -->
  <!-- ---------------------- -->

  <div id="content" class="s_wrap">

    <?php if ($tbData->common['column_position'] == "left" && $column_right): ?>
    <div id="left_col" class="s_side_col">
    <?php echo $column_right; ?>
    </div>
    <?php endif; ?>

    <div id="shopping_cart" class="s_main_col">

      <?php echo $content_top; ?>

      <?php if ($error_warning): ?>
      <div class="s_server_msg s_msg_red"><p><?php echo $error_warning; ?></p></div>
      <?php endif; ?>

      <?php if ($attention): ?>
      <div class="s_server_msg s_msg_yellow"><p><?php echo $attention; ?></p></div>
      <?php endif; ?>

      <?php if ($success): ?>
      <div class="s_server_msg s_msg_green"><p><?php echo $success; ?></p></div>
      <?php endif; ?>

      <form id="cart_form" class="clearfix" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
        <table class="s_table_1 cart" width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <th width="65"><?php echo $column_remove; ?></th>
            <th width="60"><?php echo $column_image; ?></th>
            <th width="320"><?php echo $column_name; ?></th>
            <th><?php echo $column_model; ?></th>
            <th><?php echo $column_quantity; ?></th>
            <th><?php echo $column_price; ?></th>
            <th><?php echo $column_total; ?></th>
          </tr>
          <?php $class = 'odd'; ?>
          <?php foreach ($products as $product): ?>
          <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
          <tr class="<?php echo $class; ?>">
            <td valign="middle"><img src="catalog/view/theme/<?php echo $this->config->get('config_template'); ?>/image/cancel.png" class="removeimg" /><input type="hidden" name="remove[]" value="<?php echo $product['key']; ?>" /></td>
            <td valign="middle">
              <?php if ($product['thumb']): ?>
              <a href="<?php echo $product['href']; ?>">
                <img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" />
              </a>
              <?php endif; ?>
            </td>
            <td valign="middle"><a href="<?php echo $product['href']; ?>"><strong><?php echo $product['name']; ?></strong></a>
              <?php if (!$product['stock']): ?>
              <span style="color: #FF0000; font-weight: bold;" class="stock">***</span>
			  <?php else: ?>
              <span style="color: #FF0000; font-weight: bold;" class="stock"></span>
              <?php endif; ?>
              <div>
                <?php foreach ($product['option'] as $option): ?>
                - <small><?php echo $option['name']; ?> <?php echo $option['value']; ?></small><br />
                <?php endforeach; ?>
              </div>
            </td>
            <td valign="middle"><?php echo $product['model']; ?></td>
            <td valign="middle"><img src="catalog/view/theme/<?php echo $this->config->get('config_template'); ?>/image/plus.png" class="plusimg" align="absmiddle" style="display:inline;" />&nbsp;
			<input type="text" name="quantity[<?php echo $product['key']; ?>]" value="<?php echo $product['quantity']; ?>" size="1" style="width:24px;" />&nbsp;
		  	<img src="catalog/view/theme/<?php echo $this->config->get('config_template'); ?>/image/subtract.png" class="subtractimg" align="absmiddle" style="display:inline;" /></td>
            <td valign="middle" class="price"><?php echo $product['price']; ?></td>
            <td valign="middle" class="total"><?php echo $product['total']; ?></td>
          </tr>
          <?php endforeach; ?>
          <?php foreach ($vouchers as $voucher): ?>
          <tr>
            <td valign="middle"><img src="catalog/view/theme/<?php echo $this->config->get('config_template'); ?>/image/cancel.png" class="removeimg" /><input type="hidden" name="voucher[]" value="<?php echo $voucher['key']; ?>" /></td>
            <td valign="middle"></td>
            <td valign="middle"><?php echo $voucher['description']; ?></td>
            <td valign="middle"></td>
            <td valign="middle">1</td>
            <td valign="middle"><?php echo $voucher['amount']; ?></td>
            <td valign="middle"><?php echo $voucher['amount']; ?></td>
          </tr>
          <?php endforeach; ?>
        </table>

      </form>

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
    <a id="button-quote" class="onebutton"><span><?php echo $button_quote; ?></span></a>
    <div id="quote" style="display: none;"></div>
    <input type="hidden" name="shipping_method" value="<?php echo $code; ?>" />
  </div>
</div>
	<?php } ?>
	<?php echo $cartmodule; ?>
      
      <script type="text/javascript">
      $(function() {
        $("#cart_totals").accordion({
          autoHeight:  false,
          collapsible: true,
          active:      false
        });
      });
      </script>

      <span class="clear"></span>

      <?php if ($totals): $i = 0; $j = count($totals); ?>
        <div class="cart-total">
      	<table style="float: right; display: inline-block;">
		<?php foreach ($totals as $total): ?>
		<tr>
          <td colspan="5"></td>
          <td align="right"><b><?php echo $total['title']; ?>:</b></td>
          <td align="right"><?php echo $total['text']; ?></td>
        </tr>
        <?php $i++; endforeach; ?>
		  </table>
    	</div>
        <span class="clear"></span>
        <span class="clear s_mb_20"></span>
      <?php endif; ?>
	  
	  <div class="s_submit clearfix s_mb_25 buttons">
        <a class="s_button_1 s_ddd_bgr left " href="<?php echo $continue; ?>"><span class="s_text"><?php echo $button_shopping; ?></span></a>
        <a class="s_button_1 s_main_color_bgr" href="<?php echo $checkout; ?>"><span class="s_text"><?php echo $button_checkout; ?></span></a>
      </div>

      <?php echo $content_bottom; ?>

    </div>

    <?php if ($tbData->common['column_position'] == "right" && $column_right): ?>
    <div id="right_col" class="s_side_col">
    <?php echo $column_right; ?>
    </div>
    <?php endif; ?>
    
<style type="text/css">
a.onebutton {
	display: inline-block;
	padding-left: 6px;
	background: url('catalog/view/theme/shoppica/image/button-left.png') top left no-repeat;
	text-decoration: none;
	cursor: pointer;
}
a.onebutton span {
	color: #FFFFFF;
	line-height: 12px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	display: inline-block;
	padding: 6px 12px 8px 5px;
	background: url('catalog/view/theme/shoppica/image/button-right.png') top right no-repeat;
}
a.onebutton:hover {
	background: url('catalog/view/theme/shoppica/image/button-left-active.png') top left no-repeat;
}
a.onebutton:hover span {
	color: #FFFFFF;
	background: url('catalog/view/theme/shoppica/image/button-right-active.png') top right no-repeat;
}
</style>
<script type="text/javascript"><!--
$('.cart img').css('cursor','pointer');

$('.cart .removeimg').bind('click', function() {
	var imgobj = $(this);	
	$.ajax({
		url: 'index.php?route=onecheckout/cart/remove',
		type: 'post',
		data: imgobj.parent().find('input[type=\'hidden\']'),
		dataType: 'json',
		success: function(json) {
			$('#intro_wrap h1').html(json['cart_heading']);
			if(json['error_warning']){
				$('.warning').remove();
				$('#shopping_cart form').before('<div class="warning s_server_msg s_msg_red" style="display: none;"><p>'+json['error_warning']+'</p></div>');
				$('.warning').fadeIn('slow');
			}else{
				$('.warning').fadeOut('slow');
			}
				if(json['cart_total']){
					html='';
					for(i in json['totals']){
						html+='<tr>';
						html+='<td colspan="5"></td>';
						html+='<td align="right"><b>'+json['totals'][i]['title']+':</b></td>';
						html+='<td align="right">'+json['totals'][i]['text']+'</td>';
						html+='</tr>';
					}
					$('.cart-total table').fadeOut(1000,function(){$('.cart-total table tr').remove();$('.cart-total table').append(html).fadeIn(1000);});
					imgobj.parent().parent().fadeOut('slow');
				}else{
					$('.cart, .cart-module, .cart-total, .buttons .left').slideUp();
					$('.buttons .s_main_color_bgr').attr('href',json['continue']).children('span').html(json['button_continue']);
					$('.buttons').before(json['cart_empty']);
					$('#content .content').slideDown();					
				}
				$.ajax({
					url: 'index.php?route=tb/cartCallback',
					dataType: 'json',
					success: function(json) { 
						if (json['html']) {
        					$('#cart_menu span.s_grand_total').html(json['total_sum']);
        					$('#cart_menu div.s_cart_holder').html(json['html']);
     					}
      				}
    			});
			
		}
	});
});

$('.cart .plusimg').bind('click', function() {
	$(this).parent().find('input[name*=\'quantity\']').val(Number($(this).parent().find('input[name*=\'quantity\']').val())+1);	
	updatequantity(this);													
});
$('.cart .subtractimg').bind('click', function() {
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
			$('#intro_wrap h1').html(json['cart_heading']);
			imgobj.parent().find('input[name*=\'quantity\']').val(json['quantity']);
			imgobj.parent().parent().find('.total').html(json['total']);
			imgobj.parent().parent().find('.price').html(json['price']);
			if(!json['stock']){
				imgobj.parent().parent().find('.stock').html('***')
			}else{
				imgobj.parent().parent().find('.stock').html('')
			}
			if(json['error_warning']){
				$('.warning').remove();
				$('#shopping_cart form').before('<div class="warning s_server_msg s_msg_red" style="display: none;"><p>'+json['error_warning']+'</p></div>');				
				$('.warning').fadeIn('slow');
			}else{
				$('.warning').fadeOut('slow');
			}
				html='';
				for(i in json['totals']){
					html+='<tr>';
					html+='<td colspan="5"></td>';
					html+='<td align="right"><b>'+json['totals'][i]['title']+':</b></td>';
					html+='<td align="right">'+json['totals'][i]['text']+'</td>';
					html+='</tr>';
				}
				$('.cart-total table').fadeOut(1000,function(){$('.cart-total table tr').remove();$('.cart-total table').append(html).fadeIn(1000);});
				$.ajax({
					url: 'index.php?route=tb/cartCallback',
					dataType: 'json',
					success: function(json) { 
						if (json['html']) {
        					$('#cart_menu span.s_grand_total').html(json['total_sum']);
        					$('#cart_menu div.s_cart_holder').html(json['html']);
     					}
      				}
    			});
			
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
			$('#button-voucher').after('<span class="wait">&nbsp;<img src="catalog/view/theme/<?php echo $this->config->get('config_template'); ?>/image/loading.gif" style="display:inline;" alt="" /></span>');
		},
		complete: function() {
			$('#button-voucher').attr('disabled', false);
			$('.wait').remove();
		},		
		success: function(json) {
			if (json['error']) {
				$('#shopping_cart form').before('<div class="warning s_server_msg s_msg_red" style="display: none;"><p>'+json['error']+'</p></div>');
				$('.warning').fadeIn('slow');
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
			$('#button-reward').after('<span class="wait">&nbsp;<img src="catalog/view/theme/<?php echo $this->config->get('config_template'); ?>/image/loading.gif" style="display:inline;" alt="" /></span>');
		},
		complete: function() {
			$('#button-reward').attr('disabled', false);
			$('.wait').remove();
		},		
		success: function(json) {
			if (json['error']) {
				$('#shopping_cart form').before('<div class="warning s_server_msg s_msg_red" style="display: none;"><p>'+json['error']+'</p></div>');
				$('.warning').fadeIn('slow');
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
			$('#button-coupon').after('<span class="wait">&nbsp;<img src="catalog/view/theme/<?php echo $this->config->get('config_template'); ?>/image/loading.gif" style="display:inline;" alt="" /></span>');
		},
		complete: function() {
			$('#button-coupon').attr('disabled', false);
			$('.wait').remove();
		},		
		success: function(json) {
			if (json['error']) {
				$('#shopping_cart form').before('<div class="warning s_server_msg s_msg_red" style="display: none;"><p>'+json['error']+'</p></div>');
				$('.warning').fadeIn('slow');
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
			$('#button-quote').after('<span class="wait">&nbsp;<img src="catalog/view/theme/<?php echo $this->config->get('config_template'); ?>/image/loading.gif" style="display:inline;" alt="" /></span>');
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
					$('#shopping_cart form').before('<div class="warning s_server_msg s_msg_red" style="display: none;"><p>'+json['error']['warning']+'</p></div>');
					$('.warning').fadeIn('slow');
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
				html += '<br /><a id="button-shipping" class="onebutton"><span><?php echo $button_shipping; ?></span></a>';				
		
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
			$('#button-shipping').after('<span class="wait">&nbsp;<img src="catalog/view/theme/<?php echo $this->config->get('config_template'); ?>/image/loading.gif" style="display:inline;" alt="" /></span>');
		},
		complete: function() {
			$('#button-shipping').attr('disabled', false);
			$('.wait').remove();
		},		
		success: function(json) {
			if (json['error']) {
				$('#shipping').before('<div class="warning s_server_msg s_msg_red"><p>' + json['error'] + '</p></div>');
				$('.warning').fadeIn('slow');
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
</div>
<?php echo $footer; ?>