<div class="simplecheckout-block-heading"><?php echo $text_checkout_shipping_method ?></div> 
<div class="simplecheckout-block-content">
    <?php if (!empty($shipping_methods)) { ?>
        <table class="simplecheckout-methods-table">
            <?php foreach ($shipping_methods as $shipping_method) { ?>
                <?php if ($simple_shipping_view_title) { ?>
                <tr>
                    <td colspan="3"><b><?php echo $shipping_method['title']; ?></b></td>
                </tr>
                <?php } ?>
                <?php if (empty($shipping_method['error'])) { ?>
                    <?php foreach ($shipping_method['quote'] as $quote) { ?>
                        <tr>
                            <td class="code">
                                <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" <?php if ($quote['code'] == $code) { ?>checked="checked"<?php } ?> />
                            </td>
                            <td class="title">
                                <label for="<?php echo $quote['code']; ?>"><?php echo $quote['title']; ?></label>
                            </td>
                            <td class="quote">
                                <label for="<?php echo $quote['code']; ?>"><?php echo $quote['text']; ?></label>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="3"><div class="simplecheckout-error-text"><?php echo $shipping_method['error']; ?></div></td>
                    </tr>
                <?php } ?>
                <?php } ?>
        </table>
    <?php } ?>
    <?php if ($address_empty && $simple_shipping_view_address_empty) { ?>
        <div class="simplecheckout-warning-text"><?php echo $text_shipping_address; ?></div>
    <?php } ?>
</div>
<?php if ($reload_customer_only) { ?>
<script type='text/javascript'>load_customer_only()</script>
<?php } ?>