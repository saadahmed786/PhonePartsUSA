		<!-- Shopping Cart -->
		
		<div id="cart">
		
			<!-- Cart Heading -->
			
			<div class="cart-heading">
			 
				<p><?php echo $text_items; ?></p>
			
			</div>
			
			<!-- Content Shopping Cart -->
			
			<div class="content">
			
     <?php if ($products || $vouchers) { ?>
      <table class="cart">
        <?php foreach ($products as $product) { ?>
        <tr>
          <td class="image"><?php if ($product['thumb']) { ?>
            <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" /></a>
            <?php } ?></td>
          <td class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
            <div>
              <?php foreach ($product['option'] as $option) { ?>
              - <small><?php echo $option['name']; ?> <?php echo $option['value']; ?></small><br />
              <?php } ?>
            </div></td>
          <td class="quantity">x&nbsp;<?php echo $product['quantity']; ?></td>
          <td class="total"><?php echo $product['total']; ?></td>
          <td class="remove"><img src="catalog/view/theme/megastore/images/close.png" alt="<?php echo $button_remove; ?>" title="<?php echo $button_remove; ?>" onclick="$('#cart').load('index.php?route=module/cart&remove=<?php echo $product['key']; ?> #cart > *');" /></td>
        </tr>
        <?php } ?>
        <?php foreach ($vouchers as $voucher) { ?>
        <tr>
          <td class="image"></td>
          <td class="name"><?php echo $voucher['description']; ?></td>
          <td class="quantity">x&nbsp;1</td>
          <td class="total"><?php echo $voucher['amount']; ?></td>
          <td class="remove"><img src="catalog/view/theme/megastore/images/close.png" alt="<?php echo $button_remove; ?>" title="<?php echo $button_remove; ?>" onclick="$('#cart').load('index.php?route=module/cart&remove=<?php echo $product['key']; ?> #cart > *');" /></td>
        </tr>
        <?php } ?>
      </table>
      <table class="total">
        <?php foreach ($totals as $total) { ?>
        <tr>
          <td align="left" class="left"><b><?php echo $total['title']; ?>:</b></td>
          <td class="right" style="text-align:right"><?php echo $total['text']; ?></td>
        </tr>
        <?php } ?>
      </table>
    <div class="checkout"><a href="<?php echo $cart; ?>" class="button"><?php echo $text_cart; ?></a><a href="<?php echo $checkout; ?>" class="button"><?php echo $text_checkout; ?></a></div>
    <?php } else { ?>
    <div class="empty" style="padding:10px"><?php echo $text_empty; ?></div>
    <?php } ?>
			
			</div>
			
			<!-- End Content Shopping Cart -->
		
		</div>
		
		<!-- End Shopping Cart -->