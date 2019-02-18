<div class="simplecheckout-block-heading"><?php echo $text_checkout_payment_method ?></div>  
<div class="simplecheckout-block-content">
    <?php if (!empty($payment_methods)) { ?>
        <table class="simplecheckout-methods-table">
            <?php foreach ($payment_methods as $payment_method) { ?>
                <tr>
                    <td class="code">
                        <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" <?php if ($payment_method['code'] == $code) { ?>checked="checked"<?php } ?> />
                    </td>
                    <td class="title">
                        <label for="<?php echo $payment_method['code']; ?>"><?php echo $payment_method['title']; ?></label>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>
    <?php if ($address_empty && $simple_payment_view_address_empty) { ?>
        <div class="simplecheckout-warning-text"><?php echo $text_payment_address; ?></div>
    <?php } ?>
</div>
<?php if ($reload_cart) { ?>
<script type='text/javascript'>load_cart()</script>
<?php } ?>