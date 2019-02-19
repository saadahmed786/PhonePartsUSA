<?php echo $header; ?>
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
      <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons">
      <?php
      if($modify_permission=='1')
      {
      ?>
      <a onclick="$('#save_and_send').val('');$('#form').submit();" class="button"><?php echo $button_save; ?></a>
      <?php
      }
      ?>
       <!--<a onclick="$('#save_and_send').val('send');$('#form').submit();" class="button"><?php echo $button_save; ?> &amp; Send</a>--><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <div id="tabs" class="htabs"><a href="#tab-general"><?php echo $tab_general; ?></a>
        <?php if ($voucher_id) { ?>
        <a href="#tab-history"><?php echo $tab_voucher_history; ?></a>
        <?php } ?>
      </div>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <input type="hidden" id="save_and_send" name="save_and_send" value="" />
        <div id="tab-general">
          <table class="form">
            
             <tr style="display:none">
              <td><span class="required">*</span> Reason</td>
              <td>
              <select name="reason" onchange="">
              <option value="">Select Reason</option>
             <?php
             foreach($reasons as $reason)
             {
             ?>
             <option value="<?php echo $reason['reason_id'];?>" <?php if($reason['reason_id']==$reason_id) echo 'selected';?>><?php echo $reason['name'];?></option>
             
             <?php
             
             
             }
             
             ?>
              </select><input type="hidden" id="code_val" value="" />
              
              <?php if ($error_reason) { ?>
                <span class="error"><?php echo $error_reason; ?></span>
                <?php } ?>
              </td>
            </tr>
            
            
           <!-- <tr>
              <td><span class="required">*</span> <?php echo $entry_from_name; ?></td>
              <td><input type="text" name="from_name" value="<?php echo $from_name; ?>" />
                <?php if ($error_from_name) { ?>
                <span class="error"><?php echo $error_from_name; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_from_email; ?></td>
              <td><input type="text" name="from_email" value="<?php echo $from_email; ?>" />
                <?php if ($error_from_email) { ?>
                <span class="error"><?php echo $error_from_email; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_to_name; ?></td>
              <td><input type="text" name="to_name" value="<?php echo $to_name; ?>" />
                <?php if ($error_to_name) { ?>
                <span class="error"><?php echo $error_to_name; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_to_email; ?></td>
              <td><input type="text" name="to_email" value="<?php echo $to_email; ?>" />
                <?php if ($error_to_email) { ?>
                <span class="error"><?php echo $error_to_email; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_theme; ?></td>
              <td><select name="voucher_theme_id">
                  <?php foreach ($voucher_themes as $voucher_theme) { ?>
                  <?php if ($voucher_theme['voucher_theme_id'] == $voucher_theme_id) { ?>
                  <option value="<?php echo $voucher_theme['voucher_theme_id']; ?>" selected="selected"><?php echo $voucher_theme['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $voucher_theme['voucher_theme_id']; ?>"><?php echo $voucher_theme['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
            </tr>
            -->
            
            
            <tr style="display:none">
              <td><span class="required">*</span> Order ID:</td>
              <td><input type="text" name="order_id" onblur="makeCode();" value="<?php echo $order_id; ?>" <?php if($order_id){ echo 'readonly';} ?>  /> 
                <?php if ($error_order_id) { ?>
                <span class="error">Please provide Order ID</span>
                <?php } ?></td>
            </tr>
            
            
             

            <tr style="display:none">

              <td>&nbsp;</td>

              <td><div id="product-related" class="scrollbox" style="height:300px;width:700px">

                  <?php
                  
                  
                   $class = 'odd'; ?>

                  <?php 
                  $i=1;
                  foreach ($product_related as $product_related) { ?>

                  <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>


  <div id="product-related<?php echo $product_related['product_id']; ?>" class="<?php echo $class; ?>"> <input type="checkbox" id="checkbox-<?php echo $i;?>"  name="product_items[]" value="<?php echo $product_related['product_id'].'-'.$product_related['price']; ?>" onclick="updateAmount()" class="amount_checkbox"  checked/> <?php echo $product_related['name']; ?>  (<?php echo '$'.number_format($product_related['price'],2);?>)

                   

                 </div>
                  

                  <?php } ?>

                </div>
                <div style="clear:both">
                  <a onclick="$('#product-related').find(':checkbox').attr('checked', true);updateAmount();">Select All</a> | <a onclick="$('#product-related').find(':checkbox').attr('checked', false);updateAmount();">Unselect All</a>
                  </div>
                </td>

            </tr>
            
           
            <tr style="display:none">
              <td>Shipping Selected: </td>
              <td>
              <div id="shipping_method">  <?php echo $shipping_method;?>
                <input type="hidden" id="shipping_price" value="<?php echo $shipping_price;?>" /></div>
                <br />
                <input type="checkbox" name="credit_shipping" value="1" onchange="updateAmount();" <?php echo ($credit_shipping=='1'?'checked':''); ?> /> Credit Shipping
                
                
                </td>
            </tr>
            
            <tr>
              <td><?php echo $entry_amount; ?></td>
              <td><input type="text" name="amount" value="<?php echo $amount; ?>" />
                <?php if ($error_amount) { ?>
                <span class="error"><?php echo $error_amount; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> Voucher Code:</td>
              <td><input type="text" name="code" value="<?php echo $code; ?>" />
                <?php if ($error_code) { ?>
                <span class="error"><?php echo $error_code; ?></span>
                <?php } ?></td>
            </tr>
            
            
            
            
             <tr style="display:none">
              <td> Message</td>
              <td>
              <textarea class="comment-box" name="message" style="height:150px;width:750px"><?php echo $message;?></textarea>
               <?php if ($error_message) { ?>
                <span class="error"><?php echo $error_message; ?></span>
                <?php } ?>
              
              </td>
            </tr>
            <tr>
              <td><?php echo $entry_status; ?></td>
              <td><select name="status">
                  <?php if ($status) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select></td>
            </tr>
          </table>
        </div>
        <?php if ($voucher_id) { ?>
        <div id="tab-history">
          <div id="history"></div>
        </div>
        <?php } ?>
      </form>
    </div>
  </div>
</div>
<script>

/*$('input[name=\'product_item\']').autocomplete({

	delay: 0,

	source: function(request, response) {

		$.ajax({

			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),

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

		$('#product-related' + ui.item.value).remove();

		

		$('#product-related').append('<div id="product-related' + ui.item.value + '">' + ui.item.label + '<img src="view/image/delete.png" /><input type="hidden" name="product_items[]" value="' + ui.item.value + '" /></div>');



		$('#product-related div:odd').attr('class', 'odd');

		$('#product-related div:even').attr('class', 'even');

				

		return false;

	},

	focus: function(event, ui) {

      return false;

   }

});*/		


$('input[name=\'order_id\']').blur(function(e) {

<?php
if($order_id)
{
?>
if(confirm('Press Yes if you want to reload the order products from new.'))
{
	
	if(confirm('Are you sure?'))
	{
		
	}
	else
	{
	return false;	
	}
	
	
}
else
{
return false;	
}

<?php	
	
}

?>
	
   $.ajax({

			url: 'index.php?route=sale/order/getOrderProducts&token=<?php echo $token; ?>&order_id=' +  encodeURIComponent($(this).val()),

		

			success: function(data) {		
			data = data.split("~");
				$("#product-related").html(data[0]);
				
				if(data[1]!='error')
				{
				$('#shipping_method').html(data[1]);
				}
				else
				{
					$('#shipping_method').html(data[0]+' <input type="hidden" id="shipping_price" value="0" />');
					
				}

			}

		});
});
$('#product-related div img').live('click', function() {

	var price =  $(this).attr('data-price');
	
	$(this).parent().remove();
	var total = parseFloat($("input[name=amount]").val())-parseFloat(price);
	$("input[name=amount]").val(total.toFixed(2));
	

	

	$('#product-related div:odd').attr('class', 'odd');

	$('#product-related div:even').attr('class', 'even');	

});

function makeCode()
{
	var obj = $('#code_val').val();
	
	if(obj=='')
	{
			
	return false;	
	}
	var order_id=$('input[name=order_id]').val();
if(order_id=='')
{
	
return false;	
}

	$('input[name=code]').val(order_id+obj);
	console.log(order_id+obj);
}
function updateAmount()
{
var amount = 0.00;
var shipping_amount = $('#shipping_price').val();
var xvalue = 0.00;
$('.amount_checkbox').each(function(index, element) {
    
	if(this.checked)
	{
		xvalue = $(this).val();
		xvalue = xvalue.split("-");
	amount=amount + parseFloat(xvalue[1]);
	}
	
});	
if($('input[name=credit_shipping]').attr('checked'))
{
	
amount = parseFloat(amount)+parseFloat(shipping_amount);
}
$('input[name=amount]').val(amount.toFixed(2));
	
}

var canned_messages = <?php echo empty($canned_messages) ? "''" : $canned_messages; ?>;
var reason_codes = <?php echo empty($reason_codes) ? "''" : $reason_codes; ?>;
var msgs = {};
var msgs2 = {};
$(function() {
	if(canned_messages.length) {
		$.each(canned_messages, function(i, msg) {
			msgs[msg.reason_id] = msg.message;
			
		});
	}
	
	if(reason_codes.length) {
		$.each(reason_codes, function(i, msg) {
			msgs2[msg.reason_id] = msg.code;
			
		});
	}
	
	$('select[name=reason]').change(function() {
		var id = $(this).val();
		if(id > 0) {
			
			//$('input[name=notify]').attr('checked', 'checked');
			if(typeof CKEDITOR !== 'undefined' && typeof CKEDITOR.instances.comment == 'object') {
				CKEDITOR.instances.comment.setData(msgs[id]);
			} else {
				$('.comment-box').val(msgs[id]);
			}
			
			$("#code_val").val(msgs2[id]);
			makeCode();
			
			
		}
	});

	
	$('select[name=reason]').keyup(function() {
		$(this).change();
	});
});

</script>
<?php if ($voucher_id) { ?>
<script type="text/javascript"><!--
$('#history .pagination a').live('click', function() {
	$('#history').load(this.href);
	
	return false;
});	




$('#history').load('index.php?route=sale/voucher/history&token=<?php echo $token; ?>&voucher_id=<?php echo $voucher_id; ?>');
//--></script>
<?php } ?>
<script type="text/javascript"><!--
$('#tabs a').tabs(); 
//--></script> 
<?php echo $footer; ?>