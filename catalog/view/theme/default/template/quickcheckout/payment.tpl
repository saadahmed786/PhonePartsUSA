<!-- Quick Checkout quickcheckout/payment.tpl -->
<?php $config = $this->config->get('quickcheckout'); ?>
<div class="payment-step">

    <div class="box box-border">
        <div class="box-heading ">
        <span class="column"><?php echo $text_checkout_payment_address;?></span>
        <?php if($shipping_required){ ?>
        <span class="column shipping-details-step"><?php echo $text_checkout_shipping_address;?></span>
        <span class="column shipping-details-step"><?php echo $text_checkout_shipping_method;?></span>
        <?php } ?>
        <div class="clear"></div>
       </div>
        <div class="box-content">
        	<div class="column">
            	<?php echo $addresses['payment_address']; ?>
            </div>
            <?php if($shipping_required){ ?>
            <div class="column shipping-details-step">
            	<?php echo $addresses['shipping_address']; ?>
            </div>
            <div class="column shipping-details-step">
            	<?php echo $shipping_method; ?>
            </div>
            <?php } ?>
            <div class="clear"></div>
        </div>

<div class="checkout-product <?php if(!$config['confirm_2_step_cart_display']){ echo 'hide';} ?>" >
  <table class="table">
    <thead>
      <tr>
      	<td class="name"></td>
        <td class="name"><?php echo $column_name; ?></td>
        <td class="model"><?php echo $column_model; ?></td>
        <td class="quantity"><?php echo $column_quantity; ?></td>
        <td class="price <?php if(($this->config->get('config_customer_price') && !$this->customer->isLogged())){ echo 'hide';}else{$col++;}?>"><?php echo $column_price; ?></td>
        <td class="total <?php if(($this->config->get('config_customer_price') && !$this->customer->isLogged())){ echo 'hide';}else{$col++;}?>"><?php echo $column_total; ?></td>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $product) { ?>
      <tr>
      	<td class="model <?php if(!$config['confirm_images_display']){ echo 'hide';}?>"><img src="<?php echo $product['thumb']; ?>" /></td>
        <td class="name <?php if(!$config['confirm_name_display']){ echo 'hide';}?>"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
          <?php foreach ($product['option'] as $option) { ?>
          <br />
          &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
          <?php } ?></td>
        <td class="model"><?php echo $product['model']; ?></td>
        <td class="quantity "><?php echo $product['quantity']; ?></td>
        <td class="price <?php if(($this->config->get('config_customer_price') && !$this->customer->isLogged())){ echo 'hide';}?>"><?php echo $product['price']; ?></td>
        <td class="total <?php if(($this->config->get('config_customer_price') && !$this->customer->isLogged())){ echo 'hide';}?>"><?php echo $product['total']; ?></td>
      </tr>
      <?php } ?>
      <?php foreach ($vouchers as $voucher) { ?>
      <tr>
      	<td class="name <?php if(!$config['confirm_images_display']){ echo 'hide';}?>"></td>
        <td class="name <?php if(!$config['confirm_name_display']){ echo 'hide';}?>"><?php echo $voucher['description']; ?></td>
        <td class="model <?php if(!$config['confirm_model_display']){ echo 'hide';}?>"></td>
        <td class="quantity <?php if(!$config['confirm_quantity_display'] ){ echo 'hide';}?>">1</td>
        <td class="price <?php if(($this->config->get('config_customer_price') && !$this->customer->isLogged())){ echo 'hide';}?>"><?php echo $voucher['amount']; ?></td>
        <td class="total <?php if(($this->config->get('config_customer_price') && !$this->customer->isLogged())){ echo 'hide';}?>"><?php echo $voucher['amount']; ?></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot  class=" <?php if(($this->config->get('config_customer_price') && !$this->customer->isLogged())){ echo 'hide';}?>">
      <?php foreach ($totals as $total) { ?>
      <tr>
        <td colspan="5" class="price" ><b><?php echo $total['title']; ?>:</b></td>
        <td class="total"><?php echo $total['text']; ?></td>
      </tr>
      <?php } ?>
       
    </tfoot>
  </table>
</div>
        
    </div>
    
    <div class="clear"></div>
    <div class="checkout-heading clear"><?php echo $text_checkout_payment_method . ': '. $text_title; ?></div>
	<div id="payment_button"><?php if(isset($payment)){ echo $payment; }?></div>
</div>