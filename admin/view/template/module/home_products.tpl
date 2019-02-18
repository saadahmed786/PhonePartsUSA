<?php echo $header; ?>
<style>
.even , .odd{
height:45px !important;	
	
}

</style>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><?php echo $entry_product1; ?></td>
            <td><input type="text"  id="product-autofill1" value="" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><div id="featured-product1" class="scrollbox" style="width:850px;height:400px">
                <?php $class = 'odd'; ?>
                <?php foreach ($products1 as $product) { ?>
                <?php //$class = ($class == 'even' ? 'odd' : 'even'); ?>
                <div id="featured-product1<?php echo $product['product_id']; ?>" class="<?php echo $class; ?>"><img src="<?php echo $product['image'];?>" style="float:left" /> <?php echo $product['name']; ?> <img src="view/image/delete.png" />
                  <input type="hidden" value="<?php echo $product['product_id']; ?>" />
                </div>
                <?php } ?>
              </div>
              <input type="hidden" name="home_products1" value="<?php echo $home_products1; ?>" /></td>
          </tr>
        </table>
        
        
        
        
        
         <table class="form">
          <tr>
            <td><?php echo $entry_product2; ?></td>
            <td><input type="text"  id="product-autofill2" value="" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><div id="featured-product2" class="scrollbox" style="width:850px;height:400px">
                <?php $class = 'odd'; ?>
                <?php foreach ($products2 as $product) { ?>
                <?php //$class = ($class == 'even' ? 'odd' : 'even'); ?>
                <div id="featured-product2<?php echo $product['product_id']; ?>" class="<?php echo $class; ?>"><img src="<?php echo $product['image'];?>" style="float:left" /> <?php echo $product['name']; ?> <img src="view/image/delete.png" />
                  <input type="hidden" value="<?php echo $product['product_id']; ?>" />
                </div>
                <?php } ?>
              </div>
              <input type="hidden" name="home_products2" value="<?php echo $home_products2; ?>" /></td>
          </tr>
        </table>
        
        
        
        <table class="form">
          <tr>
            <td><?php echo $entry_product3; ?></td>
            <td><input type="text"  id="product-autofill3" value="" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><div id="featured-product3" class="scrollbox" style="width:850px;height:400px">
                <?php $class = 'odd'; ?>
                <?php foreach ($products3 as $product) { ?>
                <?php //$class = ($class == 'even' ? 'odd' : 'even'); ?>
                <div id="featured-product3<?php echo $product['product_id']; ?>" class="<?php echo $class; ?>"> <img src="<?php echo $product['image'];?>" style="float:left" /> <?php echo $product['name']; ?> <img src="view/image/delete.png" />
                  <input type="hidden" value="<?php echo $product['product_id']; ?>" />
                </div>
                <?php } ?>
              </div>
              <input type="hidden" name="home_products3" value="<?php echo $home_products3; ?>" /></td>
          </tr>
        </table>
        
        
        
        <table class="form">
          <tr>
            <td>Latest Products</td>
            <td><input type="text"  id="product-autofill4" value="" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><div id="featured-product4" class="scrollbox" style="width:850px;height:400px">
                <?php $class = 'odd'; ?>
                <?php foreach ($products4 as $product) { ?>
                <?php //$class = ($class == 'even' ? 'odd' : 'even'); ?>
                <div id="featured-product4<?php echo $product['product_id']; ?>" class="<?php echo $class; ?>"> <img src="<?php echo $product['image'];?>" style="float:left" /> <?php echo $product['name']; ?> <img src="view/image/delete.png" />
                  <input type="hidden" value="<?php echo $product['product_id']; ?>" />
                </div>
                <?php } ?>
              </div>
              <input type="hidden" name="home_products4" value="<?php echo $home_products4; ?>" /></td>
          </tr>
        </table>
        
      </form>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$('#product-autofill1').autocomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_model=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
						label: item.name,
						value: item.product_id,
						image:item.image,
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('#featured-product1' + ui.item.value).remove();
		
		$('#featured-product1').append('<div id="featured-product1' + ui.item.value + '"><img src="'+ui.item.image+'" style="float:left"> ' + ui.item.label + '<img src="view/image/delete.png" /><input type="hidden" value="' + ui.item.value + '" /></div>');

		$('#featured-product1 div').attr('class', 'odd');
		//$('#featured-product1 div:even').attr('class', 'even');
		
		data = $.map($('#featured-product1 input'), function(element){
			return $(element).attr('value');
		});
						
		$('input[name=\'home_products1\']').attr('value', data.join());
					
		return false;
	},
	focus: function(event, ui) {
      	return false;
   	}
});


$('#product-autofill2').autocomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_model=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
					label: item.name,
						value: item.product_id,
						image:item.image,
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('#featured-product2' + ui.item.value).remove();
		
		$('#featured-product2').append('<div id="featured-product1' + ui.item.value + '"><img src="'+ui.item.image+'" style="float:left"> ' + ui.item.label + '<img src="view/image/delete.png" /><input type="hidden" value="' + ui.item.value + '" /></div>');

		$('#featured-product2 div').attr('class', 'odd');
		//$('#featured-product2 div:even').attr('class', 'even');
		
		data = $.map($('#featured-product2 input'), function(element){
			return $(element).attr('value');
		});
						
		$('input[name=\'home_products2\']').attr('value', data.join());
					
		return false;
	},
	focus: function(event, ui) {
      	return false;
   	}
});


$('#product-autofill3').autocomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_model=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
						label: item.name,
						value: item.product_id,
						image:	item.image
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('#featured-product3' + ui.item.value).remove();
		
		$('#featured-product3').append('<div id="featured-product1' + ui.item.value + '"><img src="'+ui.item.image+'" style="float:left"> ' + ui.item.label + '<img src="view/image/delete.png" /><input type="hidden" value="' + ui.item.value + '" /></div>');

		$('#featured-product3 div').attr('class', 'odd');
		//$('#featured-product3 div:even').attr('class', 'even');
		
		data = $.map($('#featured-product3 input'), function(element){
			return $(element).attr('value');
		});
						
		$('input[name=\'home_products3\']').attr('value', data.join());
					
		return false;
	},
	focus: function(event, ui) {
      	return false;
   	}
});


$('#product-autofill4').autocomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_model=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
						label: item.name,
						value: item.product_id,
						image: item.image
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('#featured-product4' + ui.item.value).remove();
		
	$('#featured-product4').append('<div id="featured-product1' + ui.item.value + '"> <img src="'+ui.item.image+'" style="float:left"> ' + ui.item.label + '<img src="view/image/delete.png" /><input type="hidden" value="' + ui.item.value + '" /></div>');

		$('#featured-product4 div').attr('class', 'odd');
		//$('#featured-product4 div:even').attr('class', 'even');
		
		data = $.map($('#featured-product4 input'), function(element){
			return $(element).attr('value');
		});
						
		$('input[name=\'home_products4\']').attr('value', data.join());
					
		return false;
	},
	focus: function(event, ui) {
      	return false;
   	}
});


$('#featured-product1 div img').live('click', function() {
	$(this).parent().remove();
	
	$('#featured-product1 div').attr('class', 'odd');
	//$('#featured-product1 div:even').attr('class', 'even');

	data = $.map($('#featured-product1 input'), function(element){
		return $(element).attr('value');
	});
					
	$('input[name=\'home_products1\']').attr('value', data.join());	
});

$('#featured-product2 div img').live('click', function() {
	$(this).parent().remove();
	
	$('#featured-product2 div').attr('class', 'odd');
	//$('#featured-product2 div:even').attr('class', 'even');

	data = $.map($('#featured-product2 input'), function(element){
		return $(element).attr('value');
	});
					
	$('input[name=\'home_products2\']').attr('value', data.join());	
});


$('#featured-product3 div img').live('click', function() {
	$(this).parent().remove();
	
	$('#featured-product3 div').attr('class', 'odd');
	//$('#featured-product3 div:even').attr('class', 'even');

	data = $.map($('#featured-product3 input'), function(element){
		return $(element).attr('value');
	});
					
	$('input[name=\'home_products3\']').attr('value', data.join());	
});


$('#featured-product4 div img').live('click', function() {
	$(this).parent().remove();
	
	$('#featured-product4 div').attr('class', 'odd');
//	$('#featured-product4 div:even').attr('class', 'even');

	data = $.map($('#featured-product4 input'), function(element){
		return $(element).attr('value');
	});
					
	$('input[name=\'home_products4\']').attr('value', data.join());	
});
$(document).ready(function(e) {
    $( ".scrollbox" ).sortable({
  stop: function( event, ui ) {
	  
	  data1 = $.map($('#featured-product1 input'), function(element){
		return $(element).attr('value');
	});
	data2 = $.map($('#featured-product2 input'), function(element){
		return $(element).attr('value');
	});
	data3 = $.map($('#featured-product3 input'), function(element){
		return $(element).attr('value');
	});
	data4 = $.map($('#featured-product4 input'), function(element){
		return $(element).attr('value');
	});
					
	$('input[name=\'home_products1\']').attr('value', data1.join());	
	$('input[name=\'home_products2\']').attr('value', data2.join());	
	$('input[name=\'home_products3\']').attr('value', data3.join());	
	$('input[name=\'home_products4\']').attr('value', data4.join());	
	  
	  
	  }
});
});
//--></script> 
 
<?php echo $footer; ?>