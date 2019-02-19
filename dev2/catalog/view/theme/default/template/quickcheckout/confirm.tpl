<?php if (!isset($redirect)) { ?>
<!-- Quick Checkout quickcheckout/cofirm.tpl -->
<?php $config = $this->config->get('quickcheckout'); 
$col = -1;?>
<div class="checkout-product">
  <table class="table">
    <thead>
      <tr>
      	<td class="name <?php if(!$config['confirm_images_display']){ echo 'hide';}else{$col++;}?>"></td>
        <td class="name <?php if(!$config['confirm_name_display']){ echo 'hide';}else{$col++;}?>"><?php echo $column_name; ?></td>
        <td class="model <?php if(!$config['confirm_model_display']){ echo 'hide';}else{$col++;}?>"><?php echo $column_model; ?></td>
        <td class="quantity <?php if(!$config['confirm_quantity_display']){ echo 'hide';}else{$col++;}?>"><?php echo $column_quantity; ?></td>
        <td class="price <?php if(!$config['confirm_price_display'] || ($this->config->get('config_customer_price') && !$this->customer->isLogged())){ echo 'hide';}else{$col++;}?>"><?php echo $column_price; ?></td>
        <td class="total <?php if(!$config['confirm_total_display'] || ($this->config->get('config_customer_price') && !$this->customer->isLogged())){ echo 'hide';}else{$col++;}?>"><?php echo $column_total; ?></td>
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
        <td class="model <?php if(!$config['confirm_model_display']){ echo 'hide';}?>"><?php echo $product['model']; ?></td>
        <td class="quantity <?php if(!$config['confirm_quantity_display']){ echo 'hide';}?>"><input type="text" value="<?php echo $product['quantity']; ?>" class="product-qantity" data-product-id="<?php echo $product['product_id']; ?>" style="width:15px; text-align:center"/></td>
        <td class="price <?php if(!$config['confirm_price_display'] ||($this->config->get('config_customer_price') && !$this->customer->isLogged())){ echo 'hide';}?>"><?php echo $product['price']; ?></td>
        <td class="total <?php if(!$config['confirm_total_display'] || ($this->config->get('config_customer_price') && !$this->customer->isLogged())){ echo 'hide';}?>"><?php echo $product['total']; ?></td>
      </tr>
      <?php } ?>
      <?php foreach ($vouchers as $voucher) { ?>
      <tr>
      	<td class="name <?php if(!$config['confirm_images_display']){ echo 'hide';}?>"></td>
        <td class="name <?php if(!$config['confirm_name_display']){ echo 'hide';}?>"><?php echo $voucher['description']; ?></td>
        <td class="model <?php if(!$config['confirm_model_display']){ echo 'hide';}?>"></td>
        <td class="quantity <?php if(!$config['confirm_quantity_display'] ){ echo 'hide';}?>">1</td>
        <td class="price <?php if(!$config['confirm_price_display'] || ($this->config->get('config_customer_price') && !$this->customer->isLogged())){ echo 'hide';}?>"><?php echo $voucher['amount']; ?></td>
        <td class="total <?php if(!$config['confirm_total_display'] || ($this->config->get('config_customer_price') && !$this->customer->isLogged())){ echo 'hide';}?>"><?php echo $voucher['amount']; ?></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot  class=" <?php if(!$config['confirm_total_display'] || ($this->config->get('config_customer_price') && !$this->customer->isLogged())){ echo 'hide';}?>">
     <tr>
        <td colspan="<?php echo $col; ?>" class="coupon <?php if(!$config['confirm_coupon_display']){ echo 'hide';} ?>" ><b><?php echo $text_use_coupon; ?>:</b></td>
        <td class="total <?php if(!$config['confirm_coupon_display']){ echo 'hide';} ?>"><input type="text" value="" name="coupon" id="coupon" style="width:80%" /></td>
      </tr>
       <tr>
        <td colspan="<?php echo $col; ?>" class="voucher <?php if(!$config['confirm_voucher_display']){ echo 'hide';} ?>" ><b><?php echo $text_use_voucher; ?>:</b></td>
        <td class="total <?php if(!$config['confirm_voucher_display']){ echo 'hide';} ?>"><input type="text" value="" name="voucher" id="voucher" style="width:80%"/></td>
      </tr>
      <?php foreach ($totals as $total) { ?>
      <tr>
        <td colspan="<?php echo $col; ?>" class="price" ><b><?php echo $total['title']; ?>:</b></td>
        <td class="total"><?php echo $total['text']; ?></td>
      </tr>
      <?php } ?>
       
    </tfoot>
  </table>
</div>
<input type="hidden" value="<?php echo $payment_method_second_step ?>" id="payment_method_second_step" />
<div class="payment"><div id="payment_button"><?php if(isset($payment)){ echo $payment; }?></div>
 
<div class="buttons">
  <div class="right">
    <input type="button" id="register_button" class="button btn btn-primary" value="<?php if(isset($payment)){ echo $button_confirm; }else{ echo $button_continue;  } ?>" style="float:right" />
  </div>
</div>

</div>
<?php if($config['checkout_debug']){ ?>
<pre>
<?php  print_r($_SESSION); ?>
</pre>
<?php } ?>
<?php } else { ?>
<script type="text/javascript"><!--
//location = '<?php echo $redirect; ?>';

//--></script> 
<?php } ?>
