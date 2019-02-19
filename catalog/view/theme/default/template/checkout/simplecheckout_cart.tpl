<?php if ($attention) { ?>
    <div class="simplecheckout-warning-block"><?php echo $attention; ?></div>
<?php } ?>    
<?php if ($error_warning) { ?>
    <div class="simplecheckout-warning-block"><?php echo $error_warning; ?></div>
<?php } ?>
<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="simplecheckout_cart_form">
    <table class="simplecheckout-cart">
        <colgroup>
            <col class="image">
            <col class="name">
            <col class="model">
            <col class="quantity">
            <col class="price">
            <col class="total">
            <col class="remove">
        </colgroup>
        <thead>
            <tr>
                <th class="image"><?php echo $column_image; ?></th>      
                <th class="name"><?php echo $column_name; ?></th>
                <th class="model"><?php echo $column_model; ?></th>
                <th class="quantity"><?php echo $column_quantity; ?></th>
                <th class="price"><?php echo $column_price; ?></th>
                <th class="total"><?php echo $column_total; ?></th>
                <th class="remove"></th>        
            </tr>
        </thead>
    <tbody>
    <?php foreach ($products as $product) { ?>
        <tr>
            <td class="image">
                <?php if ($product['thumb']) { ?>
                    <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" /></a>
                <?php } ?>
            </td>      
            <td class="name">
                <a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                <?php if (!$product['stock'] && ($config_stock_warning || !$config_stock_checkout)) { ?>
                    <span class="product-warning">***</span>
                <?php } ?>
                <div>
                <?php foreach ($product['option'] as $option) { ?>
                &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small><br />
                <?php } ?>
                </div>
                <?php if ($product['reward']) { ?>
                <small><?php echo $product['reward']; ?></small>
                <?php } ?>
            </td>
            <td class="model"><?php echo $product['model']; ?></td>
            <td class="quantity"><input type="text" style="padding:0;text-align:right;" onchange="update_simplecheckout_cart()" name="quantity[<?php echo $product['key']; ?>]" value="<?php echo $product['quantity']; ?>" size="3" /></td>
            <td class="price"><nobr><?php echo $product['price']; ?></nobr></td>
            <td class="total"><nobr><?php echo $product['total']; ?></nobr></td>
            <td class="remove">
            <img style="cursor:pointer;" src="catalog/view/theme/default/image/close.png" onclick="update_simplecheckout_cart('<?php echo $product['key']; ?>')" />
            </td>        
            </tr>
            <?php } ?>
            <?php foreach ($vouchers as $voucher_info) { ?>
            <tr>
            <td class="image"></td>      
            <td class="name"><?php echo $voucher_info['description']; ?></td>
            <td class="model"></td>
            <td class="quantity">1</td>
            <td class="price"><nobr><?php echo $voucher_info['amount']; ?></nobr></td>
            <td class="total"><nobr><?php echo $voucher_info['amount']; ?></nobr></td>
        </tr>
    <?php } ?>
    </tbody>
    <tfoot>
        <?php foreach ($totals as $total) { ?>
            <tr>
                <td colspan="5" class="price"><b><?php echo $total['title']; ?>:</b></td>
                <td class="total"><nobr><?php echo $total['text']; ?></nobr></td>
                <td></td>
            </tr>
        <?php } ?>
        <?php if (isset($modules['coupon'])) { ?>
            <tr>
                <td class="total" colspan="7"><?php echo $entry_coupon; ?>&nbsp;<input type="text" name="coupon" value="<?php echo $coupon; ?>" /></td>
            </tr>
        <?php } ?>
        <?php if (isset($modules['reward']) && $points > 0) { ?>
            <tr>
                <td class="total" colspan="7"><?php echo $entry_reward; ?>&nbsp;<input type="text" name="reward" value="<?php echo $reward; ?>" /></td>
            </tr>
        <?php } ?>
        <?php if (isset($modules['voucher'])) { ?>
            <tr>
                <td class="total" colspan="7"><?php echo $entry_voucher; ?>&nbsp;<input type="text" name="voucher" value="<?php echo $voucher; ?>" /></td>
            </tr>
        <?php } ?>
        <?php if (isset($modules['coupon']) || isset($modules['reward']) || isset($modules['voucher'])) { ?>
            <tr>
                <?php if ($template == 'shoppica' || $template == 'shoppica2') { ?>
                    <td class="total" colspan="7"><a id="simplecheckout_button_cart" onclick="update_simplecheckout_cart();" class="s_button_1 s_main_color_bgr"><span class="s_text"><?php echo $button_update; ?></span></a></td>
                <?php } else { ?>
                    <td class="total" colspan="7"><a id="simplecheckout_button_cart" onclick="update_simplecheckout_cart();" class="simplecheckout-button"><span><?php echo $button_update; ?></span></a></td>
                <?php } ?>
            </tr>
        <?php } ?>
    </tfoot>
</table>
</form>