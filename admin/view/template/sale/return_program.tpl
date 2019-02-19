<?php echo $header; ?>
<style>

.button {
    background: none repeat scroll 0 0 #003a88;
    border-radius: 10px;
    color: #fff;
    display: inline-block;
    padding: 5px 15px;
    text-decoration: none;
	cursor:pointer;
	border:none;
	
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
      <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
      
      <?php
      if(!isset($xorder_id))
      {
      ?>
      <div class="buttons"><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
      <?php
      }
      ?>
    </div>
    <div class="content">
      
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <input type="hidden" id="save_and_send" name="save_and_send" value="" />
        <div id="tab-general">
          <table class="form">
            
           
            
            
            
            <tr>
              <td><span class="required">*</span> Order ID:</td>
              <td>
              <?
              
              if(!$xorder_id)
              {
              ?>
              <input type="text" name="order_id"   /> 
              <?php
              }
              else
              {
              ?>
             <input type="text" name="order_id" value="<?php echo $xorder_id; ?>" readonly="readonly"  />            
              <?php
              
              }
              ?>
               </td>
            </tr>
            
            
             

            <tr>

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
            
           
            
            
            <tr>
              <td>Amount:</td>
              <td><input type="text" name="amount" value="<?php echo $amount; ?>" readOnly />
              </td>
            </tr>
            
            <tr id="resolution_button" style="display:none">
            <td> </td>
            <td><input type="button" class="button" value="Proceed" onClick="loadResolution($('input[name=order_id]').val());" />
            
            </tr>
        
            
            
            
            
            
          </table>
        </div>
       <input type="hidden" name="resolution_code" id="resolution_code" value="" />
      </form>
    </div>
  </div>
</div>
<script>


$('input[name=\'order_id\']').blur(function(e) {
	
	loadOrders($(this).val());
	});

function loadOrders(order_id)
{




	
   $.ajax({

			url: 'index.php?route=sale/order/getOrderProductsWithReason&token=<?php echo $token; ?>&order_id=' +  encodeURIComponent(order_id),

		

			success: function(data) {		
			data = data.split("~");
				$("#product-related").html(data[0]);
				
				if(data[1]!='error')
				{
					
				//$('#shipping_method').html(data[1]);
				$('#resolution_button').show();
				}
				else
				{
					//$('#shipping_method').html(data[0]+' <input type="hidden" id="shipping_price" value="0" />');
					$('#resolution_button').hide();
					
				}
				
				

			}

		});
	
	
}


function loadResolution(order_id){
	   $.ajax({

			url: 'index.php?route=sale/return_program/getResolution&token=<?php echo $token; ?>&order_id=' + order_id+'&amount='+$('input[name=amount]').val(),

		

			success: function(data) {
				//$("#resolution").html(data);
				
					$('#dialog').remove();
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;">'+data+'</div>');
	//$('.amount').val($('input[name=amount]').val());
	$('#dialog').dialog({

		title: 'Order and Payment Details',

		

		bgiframe: false,

		width: 800,

		height: 400,

		resizable: false,

		modal: false

	});
				
				}

		});
	
	
}
$('#product-related div img').live('click', function() {

	var price =  $(this).attr('data-price');
	
	$(this).parent().remove();
	var total = parseFloat($("input[name=amount]").val())-parseFloat(price);
	$("input[name=amount]").val(total.toFixed(2));
	

	

	$('#product-related div:odd').attr('class', 'odd');

	$('#product-related div:even').attr('class', 'even');	

});


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
$('.amount').val(amount.toFixed(2));
	
}

$(document).ready(function(e) {
   
	<?php
	if($xorder_id!='')
	{
	?>
	
	$('#header').hide();
	$('.breadcrumb').hide();
	$('#footer').hide();
	loadOrders(<?php echo $xorder_id;?>);
	<?php	
		
	}
	
	?>
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