<?php echo $header; ?>
<style>
.pricing-table-ok td{
padding:4px !important;
font-size:11px !important;
}
</style>
<!-- @End of header -->
<main class="main">
	<div class="container shoping-cart-page">
		<h1 class="page-title"><img src="catalog/view/theme/ppusa2.0/images/icons/newcart.png" alt="" width="25px" style="margin-right: 15px;"><a href="javascript:void(0);">Shopping cart</a></h1>

		
		<div class="alert alert-success alert-dismissible" style="<?php echo ($success?'':'display:none');?>" role="alert"><?php echo $success; ?></div>
		
		
		<div class="alert alert-danger alert-dismissible" style="<?php echo ($error_warning?'':'display:none');?>" role="alert"><?php echo $error_warning; ?></div>
	

	<div id='parent_container_div' class="row shoping-cart-box cart-product-small">
		<div class="col-md-7">
			<?php foreach ($products as $kr => $product) { ?>
			<div class="product-detail row pr<?php echo $kr; ?>">
				<div class="product-detail-inner clearfix"  style="<?php if(in_array($product['model'],$xxProducts)) { echo 'border:1px solid red;'; } ?>">
					<a href="javascript:void(0);" class="cart-close" ><img src="catalog/view/theme/ppusa2.0/images/icons/cross2.png" alt="" /></a>
					<span class="hidden-xs hidden-sm hidden-md hidden-lg removeProduct" product-id="<?php echo $product['key']; ?>">remove</span>

					<div class="col-md-4 col-xs-4 hidden-xs hidden-sm product-detail-img" >
						<div class="image" style="text-align: center;font-weight:bold"><?php if ($product['thumb']) { ?>
							<a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" /><br><?php echo $product['model'];?></a>
							<?php } ?>
						</div>
					</div>
					<div class="col-md-8 product-detail-text" >

						<h2 style="margin-bottom:2px"><span class="hidden-md hidden-lg" style="font-weight:400"><?php echo $product['model'];?> </span><br class="hidden-md hidden-lg" /><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h2>
						<div class="row">
							<div class="col-md-7">
							<?php
							if($product['old_price']=='$0.00')
							{
							?>
								<table class="pricing-table table pricing-table-ok" style="margin-top:5px">
									<tbody>
										<tr>
											<td>Quantity</td>
											<?php foreach ($product['discounts'] as $discount) : ?>
												<td><?php echo $discount['quantity']; ?></td>
											<?php endforeach; ?>
										</tr>
										<tr>
											<td>Our Price</td>
											<?php foreach ($product['discounts'] as $discount) : ?>
												<td><?php echo $discount['price']; ?></td>
											<?php endforeach; ?>
										</tr>
									</tbody>
								</table>
								<?php
								}
								?>
							</div>
							<div class="col-md-5 cart-total-wrp" style="padding-top:0px">
							<input type="hidden" class="product_id" value="<?php echo $product['key']; ?>" />
								<div class="cart-total">
									<div class="qtyt-box col-xs-8" style="max-width:100%;margin-left:25px">
										<div class="input-group spinner">
											<span class="txt hidden-xs" style="font-size: 12px;">QTY</span>
											<input type="text" style="width:35px; text-align: center; background: transparent; font-size: 14px; color:#303030; height: 24px; border:2px solid #4986fe;" class="form-control" value="<?php echo $product['quantity']; ?>">
											<div class="input-group-btn-vertical" style="margin-top:-4px;margin-left:5px">
												<button class="btn " type="button"><i class="fa fa-plus"></i></button>
												<button class="btn" type="button"><i class="fa fa-minus"></i></button>
											</div>

										</div>
									</div>
									
									<h2 class="cartPPrice text-center col-xs-12 text-center" style="<?php echo ($product['old_price']!='$0.00'?'color:red':'');?>" ><?php echo $product['total']; ?><br>
									<?php
									if($product['old_price']=='$0.00')
									{
									?>

									<small>(<?php echo $product['price']; ?> ea)</small>
									<?php
									}
									else
									{
									?>
									<small style="text-decoration: line-through;font-size:10px !important">(<?php echo $product['old_price']; ?> ea)</small>
									<small style="color:red">(<?php echo $product['price']; ?> ea)</small>
									<?php
									}
									?>

									</h2>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
			<?php }?>
		</div>
		<div class="col-md-5">
		<div class="col-md-12 panel" style="background: #4986fe;padding: 20px 17px;" id="cart_right_div">
		<div id="cart_overlay">
  <img src="catalog/view/theme/default/image/loader_white.gif" style="width:115px;margin-left:40%;margin-top:40%" 
    id="cart-img-load" />
</div>
					<div id="cart_cart_right">
					<?php echo $cart_right_cart;?>
					</div>

				</div>
				
				<div class="col-md-12 text-center" style="margin-top:15px">
				<div>
					<a href="<?php echo $this->url->link('checkout/checkout','nc=1');?>"><img class="side-cart-bottom" src="catalog/view/theme/ppusa2.0/images/icons/newbasket2.png" alt="" style="width:85%"></a> 
					<h2 class="blue strike"> <span>OR</span> </h2> 

					<a href="index.php?route=payment/paypal_express_new/SetExpressCheckout"><img class="side-cart-bottom" src="catalog/view/theme/ppusa2.0/images/icons/newpaypal.png" alt="" style="width:85%"></a>
					</div>
					</div>
				</div>

				<div class="col-md-7"></div>
				<div class="col-md-5 text-center" style="margin-top:15px">


				</div>


				

	</div>
	<select name="country_id" style="display:none">
    <option value=""><?php echo $text_select; ?></option>
    <?php foreach ($countries as $country) { ?>
    <?php if ($country['country_id'] == $country_id) { ?>
    <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
    <?php } else { ?>
    <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
    <?php } ?>
    <?php } ?>
  </select>
  <select name="zone_id" id="zone_id" style="display:none">
  </select>
</main>
<script type="text/javascript" src="//maps.google.com/maps/api/js?key=AIzaSyCDBA_9lOZO0FkWHBvsmwhZdaWKALqJJsg&sensor=true"></script>
<script>
function loadxPopup()
{

  $.ajax({
    url: 'index.php?route=checkout/cart/quote',
    type: 'post',
    data: 'country_id=<?php echo $country_id;?>&zone_id=' + $('select[name=zone_id]').val() + '&postcode=' + encodeURIComponent($('#zip_cart').val()),
    dataType: 'json',		
    	beforeSend: function() {
		
		},
		complete: function() {
		},		
		success: function(json) {
			if (json['error']) {
				if (json['error']['warning']) { 
				}	

				if (json['error']['country']) {
				
      			}	
      
      if (json['error']['zone']) {
				
      }
      
      if (json['error']['postcode']) {
				
      }					
    }
    
    if (json['shipping_method']) {
      html  = '<ul class="total-types">';
     
      
      for (i in json['shipping_method']) {
					if (!json['shipping_method'][i]['error']) {
						html += '<ul class="first-list">';
						for (j in json['shipping_method'][i]['quote']) {

							if(json['shipping_method'][i]['quote'][j]['title']=='Local Las Vegas Store Pickup - 9:30am-4:30pm')
							{
								json['shipping_method'][i]['quote'][j]['title'] = 'Las Vegas Store Pick Up';
							}
							else if(json['shipping_method'][i]['quote'][j]['title']=='USPS First Class - Left at door or Mailbox')
							{
									json['shipping_method'][i]['quote'][j]['title']='USPS First Class';		
							}
							else if(json['shipping_method'][i]['quote'][j]['title']=='USPS Priority Mail - Left at door or Mailbox')
							{
									json['shipping_method'][i]['quote'][j]['title']='USPS Priority Mail';		
							}
							else if(json['shipping_method'][i]['quote'][j]['title']=='Fedex Next Day Saturday (Ships Fri 4:00 pm PST)')
							{
									json['shipping_method'][i]['quote'][j]['title']='Fedex Next Day Saturday';		
							}

							else if(json['shipping_method'][i]['quote'][j]['title']=='Fedex Next Business Day (Ships 4:00 pm PST)')
							{
									json['shipping_method'][i]['quote'][j]['title']='Fedex Next Business Day';		
							}
              
              
              if (json['shipping_method'][i]['quote'][j]['code'] == json['default_shipping_method']) {
                html += '<li '+(json['shipping_method'][i]['quote'][j]['title'] == 'Pick Up'?'style="margin-bottom:35px"':'')+' ><input type="radio" name="shipping_method" data-value="'+json['shipping_method'][i]['quote'][j]['text']+'" value="' + json['shipping_method'][i]['quote'][j]['code'] + '" id="' + json['shipping_method'][i]['quote'][j]['code'] + '" class="css-radio" checked="checked" />';
              } else {
                html += '<li '+(json['shipping_method'][i]['quote'][j]['title'] == 'Pick Up'?'style="margin-bottom:35px"':'')+' ><input type="radio" name="shipping_method" data-value="'+json['shipping_method'][i]['quote'][j]['text']+'" value="' + json['shipping_method'][i]['quote'][j]['code'] + '" id="' + json['shipping_method'][i]['quote'][j]['code'] + '" class="css-radio" />';
              }
              html +='<label for="'+json['shipping_method'][i]['quote'][j]['code']+'" class="css-radio" style="display:inherit">'+json['shipping_method'][i]['quote'][j]['title']+(json['shipping_method'][i]['quote'][j]['title'] == 'Pick Up'?'<br><small style="opacity:0.7;font-size:12px;line-height:14px">Warehouse Address:<br>99 Treutel Springs Suite 152</small>':'')+'</label>';
              	
            }	
            html +='</ul>';
          } else {
          
          }
        }
       		html += '  <input type="hidden" name="next" value="shipping" />';
				$('#cart-shipping-estimate').html(html);
				updateShippingCost();
				$("#cart_overlay").fadeOut();
				<?php if ($shipping_method) { ?>
				
				<?php } else { ?>
			
				<?php } ?>
				
			}
		}
	});	
}

var zone_id = '';
 $(document).on('click', '#apply_zip_cart', function() {
 	$("#cart_overlay").fadeIn();
	var zip = $('#zip_cart').val();
	var lat;
	var lng;
  	var geocoder = new google.maps.Geocoder();
  	geocoder.geocode({ 'address': zip }, function (results, status) {
    	if (status == google.maps.GeocoderStatus.OK) {
      	geocoder.geocode({'latLng': results[0].geometry.location}, function(results, status) {
        	if (status == google.maps.GeocoderStatus.OK) {
          		if (results[1]) {
            	var loc = getCityState(results);
            	
          }
        }
      });
      
    }
  }); 
 });

function getCityState(results)
{
	var a = results[0].address_components;
	var city;
	var state;
	for(i = 0; i <  a.length; ++i)
	{
		var t = a[i].types;
		if(compIsType(t, 'administrative_area_level_1'))
		{
			state = a[i].long_name;
		}
		else if(compIsType(t, 'locality')){
			city = a[i].long_name;
		}
	}
	console.log(state);
	$('select[name=zone_id]').find('option').removeAttr('selected');
	$('select[name=zone_id]').find('option:contains('+state+')').attr('selected','selected');
	
	setTimeout(function() { loadxPopup();$('#cart_overlay').fadeOut(); }, 4000);	
	
	
}

function compIsType(t, s) { 
	for(z = 0; z < t.length; ++z) 
	if(t[z] == s)
	return true;
	return false;
}



$(document).on('change','select[name=\'country_id\']', function() {
	$.ajax({
		url: 'index.php?route=checkout/cart/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			
		},
		complete: function() {
			
		},			
		success: function(json) {
			if (json['postcode_required'] == '1') {
			
			} else {
			
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
    loadxPopup();
    
  },
  error: function(xhr, ajaxOptions, thrownError) {
   alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
 }
});
});

$(document).on('change','input[name=shipping_method]',function(){
	 $('#shipping-text').html($(this).attr('data-value'));
	$.ajax({
    url: 'index.php?route=checkout/cart',
    type: 'post',
    data: 'shipping_method='+$(this).val(),
    dataType: 'json',		
		success: function(json) {
			
			$( ".cart-product-small #cart_right_div #cart_cart_right" ).load( "index.php?route=module/cart_right_cart" );
			 setTimeout(function() { loadxPopup(); }, 100);
     
      					
    }
    
 
	});

});

function updateShippingCost(){
var data_value = $('input[name=shipping_method]:checked').attr('data-value');
$('#shipping-text').html(data_value);
}
$('select[name=\'country_id\']').trigger('change');



</script>
<script type="text/javascript" src='catalog/view/javascript/scroller.js'></script>

<!-- @End of main -->
<?php echo $footer; ?>
<!-- @End of footer -->