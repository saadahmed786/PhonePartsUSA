<div class="simplecheckout-block-heading">
    <?php echo $text_checkout_customer ?>
    <?php if ($simple_customer_view_login) { ?>
    <span class="simplecheckout-block-heading-button">
    <a <?php if ($action_login) { ?>style="display:none"<?php } ?> id="simplecheckout_customer_login"><?php echo $text_checkout_customer_login ?></a>
    <a <?php if (!$action_login) { ?>style="display:none"<?php } ?> id="simplecheckout_customer_cancel"><?php echo $text_checkout_customer_cancel ?></a>
    </span>
    <?php } ?>
</div>  
<div class="simplecheckout-block-content">
    <?php if ($action_login) { ?>
        <?php if ($error_email_exists) { ?>
            <div class="simplecheckout-warning-block"><?php echo $error_email_exists ?></div>
        <?php } ?>
        <?php if ($error_login) { ?>
            <div class="simplecheckout-warning-block"><?php echo $error_login ?></div>
        <?php } ?>
        <table class="simplecheckout-login">
            <tr>
                <td class="simplecheckout-login-left"><?php echo $entry_email; ?></td>
                <td class="simplecheckout-login-right"><input type="text" name="email" value="<?php echo $email; ?>" /></td>
            </tr>
            <tr>
                <td class="simplecheckout-login-left"><?php echo $entry_password; ?></td>
                <td class="simplecheckout-login-right"><input type="password" name="password" value="" /></td>
            </tr>
            <tr>
                <td></td>
                <td class="simplecheckout-login-right"><a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a></td>
            </tr>
            <tr>
                <td></td>
                <?php if ($template == 'shoppica' || $template == 'shoppica2') { ?>
                    <td class="simplecheckout-login-right"><a id="simplecheckout_button_login" class="s_button_1 s_main_color_bgr"><span class="s_text"><?php echo $button_login; ?></span></a></td>
                <?php } else { ?>
                    <td class="simplecheckout-login-right"><a id="simplecheckout_button_login" class="simplecheckout-button"><span><?php echo $button_login; ?></span></a></td>
                <?php } ?>
            </tr>
        </table>
        <!-- You can add here the social engine code for login in the simplecheckout_customer.tpl -->
    <?php } else { ?>
        <?php if ($error_warning) { ?>
            <div class="simplecheckout-warning-block" <?php echo isset($error_warning_block) ? 'block="'.$error_warning_block.'"' : ''?>><?php echo $error_warning ?></div>
        <?php } ?>
        <?php if ($simple_customer_view_address_select && !empty($addresses)) { ?>
            <div class="simplecheckout-customer-address">
            <span><?php echo $text_select_address ?>:</span>&nbsp;
            <select name='address_id' onchange='load_customer()'>
                <option value="0" <?php echo $address_id == 0 ? 'selected="selected"' : '' ?>><?php echo $text_add_new ?></option>
                <?php foreach($addresses as $address) { ?>
                    <option value="<?php echo $address['address_id'] ?>" <?php echo $address_id == $address['address_id'] ? 'selected="selected"' : '' ?>><?php echo $address['firstname']; ?> <?php echo !empty($address['lastname']) ? ' '.$address['lastname'] : ''; ?><?php echo !empty($address['address_1']) ? ', '.$address['address_1'] : ''; ?><?php echo !empty($address['city']) ? ', '.$address['city'] : ''; ?></option>
                <?php } ?>
            </select>
            </div>
        <?php } elseif (!$simple_customer_view_address_select) { ?>
            <input type="hidden" name="address_id" value="<?php echo $address_id ?>" />
        <?php } ?>
        <?php $count_fields = count($display_customer_fields); ?>
        <?php $split_fields = ceil($count_fields/2); ?>
        <?php $i = 0; ?>
        <div class="simplecheckout-customer-block">
        <div id="simple_customer_view_email" style="display:none;"><?php echo $simple_customer_view_email ?></div>
        <table class="simplecheckout-customer">
            <?php $email_field_exists = false; ?>
            <?php foreach ($display_customer_fields as $field) { ?>
                <?php if ($customer_logged && $field['id'] == 'main_email') { continue; } ?>
                <?php if (!$customer_logged && $field['id'] == 'main_email' && !$simple_customer_action_register &&  !$simple_customer_view_email) { continue; } ?>
                <?php if (!$customer_logged && $field['id'] == 'main_email' && $simple_customer_action_register == 2) { ?>
                    <tr>
                        <td class="simplecheckout-customer-left">
                           <?php echo $entry_register; ?>
                        </td>
                        <td class="simplecheckout-customer-right">
                          <label><input type="radio" name="register" value="1" <?php echo $register == 1 ? 'checked="checked"' : ''; ?> /><?php echo $text_yes ?></label>&nbsp;
                          <label><input type="radio" name="register" value="0" <?php echo $register == 0 ? 'checked="checked"' : ''; ?> /><?php echo $text_no ?></label>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($field['id'] == 'main_email') { $email_field_exists = true; } ?>
                <tr<?php echo $field['id'] == 'main_email' ? ' id="email_row"' : '' ?><?php echo $field['id'] == 'main_email' && $simple_customer_action_register == 2 && !$register && !$simple_customer_view_email ? ' style="display:none;"' : '' ?><?php echo $field['system'] != '' ? ' class="simple_system_row"' : ' class="simple_table_row"' ?>>
                    <td class="simplecheckout-customer-left">
                        <?php if ($field['required']) { ?>
                            <span class="simplecheckout-required"<?php echo ($field['id'] == 'main_email' && $simple_customer_view_email == 1 && ($simple_customer_action_register == 0 || ($simple_customer_action_register == 2 && !$register))) ? ' style="display:none"' : '' ?>>*</span>
                        <?php } ?>
                        <?php echo $field['label'] ?>
                    </td>
                    <td class="simplecheckout-customer-right">
                        <?php if ($field['type'] == 'text') { ?>
                            <input type="text" id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" value="<?php echo $field['value'] ?>" mask="<?php echo $field['mask'] ?>" <?php echo $field['autocomplete'] ? 'autocomplete="1"' : '' ?>>
                        <?php } ?>
                        <?php if ($field['type'] == 'textarea') { ?>
                            <textarea type="text" id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" ><?php echo $field['value'] ?></textarea>
                        <?php } ?>
                        <?php if ($field['type'] == 'select') { ?>
                            <select id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" <?php if ($field['id'] == 'main_country_id') { ?>onchange="$('#main_zone_id').load('index.php?route=checkout/simplecheckout_customer/zone&country_id=' + this.value);"<?php } ?>>
                                <option value=""><?php echo $text_select ?></option>
                                <?php foreach ($field['values'] as $key => $value) { ?>
                                    <option value="<?php echo $key ?>" <?php if ($key == $field['value']) { ?>selected="selected"<?php } ?>><?php echo $value ?></option>
                                <?php } ?>
                            </select>
                        <?php } ?>
                        <?php if ($field['type'] == 'select_from_api') { ?>
                            <select id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>">
                                <?php foreach ($field['values'] as $key => $value) { ?>
                                    <option value="<?php echo $key ?>" <?php if ($key == $field['value']) { ?>selected="selected"<?php } ?>><?php echo $value ?></option>
                                <?php } ?>
                            </select>
                        <?php } ?>
                        <?php if ($field['type'] == 'radio') { ?>
                            <?php foreach ($field['values'] as $key => $value) { ?>
                                <label><input type="radio" id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" value="<?php echo $key ?>" <?php if ($key == $field['value']) { ?>checked="checked"<?php } ?> >&nbsp;<?php echo $value ?></label><br>
                            <?php } ?>
                        <?php } ?>
                        <?php if (!empty($field['error'])) { ?>
                            <span class="simplecheckout-error-text"><?php echo $field['error']; ?></span>
                        <?php } ?>
                    </td>
                </tr>
                <?php if (!$customer_logged && $field['id'] == 'main_email' && $simple_customer_action_register) { ?>
                    <tr id="password_row"<?php echo ($field['id'] == 'main_email' && $simple_customer_action_register == 2 && !$register) || $simple_customer_generate_password ? ' style="display:none;"' : '' ?> <?php echo $simple_customer_generate_password ? 'autogenerate="1"' : '' ?>>
                        <td class="simplecheckout-customer-left">
                            <span class="simplecheckout-required">*</span>
                            <?php echo $entry_password ?>
                        </td>
                        <td class="simplecheckout-customer-right">
                            <input type="password" name="password" value="<?php echo $password ?>">
                            <?php if ($error_password) { ?>
                                <span class="simplecheckout-error-text"><?php echo $error_password; ?></span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php if ($simple_customer_view_password_confirm) { ?>
                    <tr id="confirm_password_row"<?php echo ($field['id'] == 'main_email' && $simple_customer_action_register == 2 && !$register) || $simple_customer_generate_password ? ' style="display:none;"' : '' ?> <?php echo $simple_customer_generate_password ? 'autogenerate="1"' : '' ?>>
                        <td class="simplecheckout-customer-left">
                            <span class="simplecheckout-required">*</span>
                            <?php echo $entry_password_confirm ?>
                        </td>
                        <td class="simplecheckout-customer-right">
                            <input type="password" name="password_confirm" value="<?php echo $password_confirm ?>">
                            <?php if ($error_password_confirm) { ?>
                                <span class="simplecheckout-error-text"><?php echo $error_password_confirm; ?></span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
                <?php $i++; if ($i == $split_fields) { ?>
                    </table>
                    <table class="simplecheckout-customer">
                <?php } ?>
            <?php } ?>
            <?php if ($simple_customer_action_subscribe == 2 && $email_field_exists) { ?>
                <tr id="subscribe_row"<?php echo $simple_customer_action_register == 2 && !$register && !$simple_customer_view_email ? ' style="display:none;"' : '' ?>>
                    <td class="simplecheckout-customer-left">
                       <?php echo $entry_newsletter; ?>
                    </td>
                    <td class="simplecheckout-customer-right">
                      <label><input type="radio" name="subscribe" value="1" <?php echo $subscribe == 1 ? 'checked="checked"' : ''; ?> /><?php echo $text_yes ?></label>&nbsp;
                      <label><input type="radio" name="subscribe" value="0" <?php echo $subscribe == 0 ? 'checked="checked"' : ''; ?> /><?php echo $text_no ?></label>
                    </td>
                </tr>
            <?php } ?>
            <?php if ($simple_customer_view_customer_type) { ?>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <label><input type="radio" name="customer_type" value="private" <?php if ($customer_type == 'private') { ?>checked="checked"<?php } ?>>&nbsp;<?php echo $text_private ?></label>
                        <label><input type="radio" name="customer_type" value="company" <?php if ($customer_type == 'company') { ?>checked="checked"<?php } ?>>&nbsp;<?php echo $text_company ?></label>
                    </td>
                </tr>
            <?php } ?>
        </table>
        </div>
        <div style="clear:both:width:100%;height:1px;"></div>
        <?php if ($simple_customer_view_customer_type) { ?>
        <div id="simplecheckout_company" class="simplecheckout-customer-block" <?php if ($customer_type == 'private') { ?> style="display:none"<?php } ?>>
            <span class="simplecheckout-company-header"><?php echo $text_your_company ?></span>
            <?php $count_fields = count($display_company_fields); ?>
            <?php $split_fields = ceil($count_fields/2); ?>
            <?php $i = 0; ?>
            <table class="simplecheckout-customer">
                <?php foreach ($display_company_fields as $field) { ?>
                    <tr>
                        <td class="simplecheckout-customer-left">
                            <?php if ($field['required']) { ?>
                                <span class="simplecheckout-required">*</span>
                            <?php } ?>
                            <?php echo $field['label'] ?>
                        </td>
                        <td class="simplecheckout-customer-right">
                            <?php if ($field['type'] == 'text') { ?>
                                <input type="text" id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" value="<?php echo $field['value'] ?>" mask="<?php echo $field['mask'] ?>">
                            <?php } ?>
                            <?php if ($field['type'] == 'textarea') { ?>
                                <textarea type="text" id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" ><?php echo $field['value'] ?></textarea>
                            <?php } ?>
                            <?php if ($field['type'] == 'select') { ?>
                                <select id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>">
                                    <option value=""><?php echo $text_select ?></option>
                                    <?php foreach ($field['values'] as $key => $value) { ?>
                                        <option value="<?php echo $key ?>" <?php if ($key == $field['value']) { ?>selected="selected"<?php } ?>><?php echo $value ?></option>
                                    <?php } ?>
                                </select>
                            <?php } ?>
                            <?php if ($field['type'] == 'select_from_api') { ?>
                                <select id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>">
                                    <?php foreach ($field['values'] as $key => $value) { ?>
                                        <option value="<?php echo $key ?>" <?php if ($key == $field['value']) { ?>selected="selected"<?php } ?>><?php echo $value ?></option>
                                    <?php } ?>
                                </select>
                            <?php } ?>
                            <?php if ($field['type'] == 'radio') { ?>
                                <?php foreach ($field['values'] as $key => $value) { ?>
                                    <label><input type="radio" id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" value="<?php echo $key ?>" <?php if ($key == $field['value']) { ?>checked="checked"<?php } ?> >&nbsp;<?php echo $value ?></label><br>
                                <?php } ?>
                            <?php } ?>
                            <?php if (!empty($field['error'])) { ?>
                                <span class="simplecheckout-error-text"><?php echo $field['error']; ?></span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php $i++; if ($i == $split_fields) { ?>
                        </table>
                        <table class="simplecheckout-customer">
                    <?php } ?>
                <?php } ?>
            </table>
        </div>
        <?php } ?>
        <?php if ($simple_payment_view_address_show) { ?>
            <div class="simplecheckout-customer-same-address">
                <label><input type="checkbox" name="payment_address_same" id="payment_address_same" value="1" <?php if ($payment_address_same) { ?>checked="checked"<?php } ?>>&nbsp;<?php echo $entry_payment_address ?></label>
            </div>
        <?php } ?>
    <?php } ?>
</div>
<?php if (!$action_login && $simple_payment_view_address_show) { ?>
<div id="simple_payment_address_block" <?php if ($payment_address_same) { ?>style="display:none;"<?php } ?>>
    <div class="simplecheckout-block-heading">
        <?php echo $text_checkout_payment_address ?>
    </div>  
    <div class="simplecheckout-block-content">
    <?php if ($simple_payment_view_address_select && !empty($addresses)) { ?>
                <div class="simplecheckout-customer-address">
                <span><?php echo $text_select_address ?>:</span>&nbsp;
                <select name='payment_address_id' onchange='load_customer()'>
                    <option value="0" <?php echo $payment_address_id == 0 ? 'selected="selected"' : '' ?>><?php echo $text_add_new ?></option>
                    <?php foreach($addresses as $address) { ?>
                        <option value="<?php echo $address['address_id'] ?>" <?php echo $payment_address_id == $address['address_id'] ? 'selected="selected"' : '' ?>><?php echo $address['firstname']; ?> <?php echo !empty($address['lastname']) ? ' '.$address['lastname'] : ''; ?><?php echo !empty($address['address_1']) ? ', '.$address['address_1'] : ''; ?><?php echo !empty($address['city']) ? ', '.$address['city'] : ''; ?></option>
                    <?php } ?>
                </select>
                </div>
            <?php } elseif (!$simple_customer_view_address_select) { ?>
                <input type="hidden" name="payment_address_id" value="<?php echo $payment_address_id ?>" />
            <?php } ?>
            <?php $count_fields = count($display_payment_address_fields); ?>
            <?php $split_fields = ceil($count_fields/2); ?>
            <?php $i = 0; ?>
            <div class="simplecheckout-customer-block">
            <table class="simplecheckout-customer">
                <?php foreach ($display_payment_address_fields as $field) { ?>
                    <tr class="simple_table_row">
                        <td class="simplecheckout-customer-left">
                            <?php if ($field['required']) { ?>
                                <span class="simplecheckout-required"<?php echo ($field['id'] == 'main_email' && $simple_customer_view_email == 1 && ($simple_customer_action_register == 0 || ($simple_customer_action_register == 2 && !$register))) ? ' style="display:none"' : '' ?>>*</span>
                            <?php } ?>
                            <?php echo $field['label'] ?>
                        </td>
                        <td class="simplecheckout-customer-right">
                            <?php if ($field['type'] == 'text') { ?>
                                <input type="text" id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" value="<?php echo $field['value'] ?>" mask="<?php echo $field['mask'] ?>" <?php echo $field['autocomplete'] ? 'autocomplete="1"' : '' ?>>
                            <?php } ?>
                            <?php if ($field['type'] == 'textarea') { ?>
                                <textarea type="text" id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" ><?php echo $field['value'] ?></textarea>
                            <?php } ?>
                            <?php if ($field['type'] == 'select') { ?>
                                <select id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" <?php if ($field['id'] == 'payment_country_id') { ?>onchange="$('#payment_zone_id').load('index.php?route=checkout/simplecheckout_customer/zone&country_id=' + this.value);"<?php } ?>>
                                    <option value=""><?php echo $text_select ?></option>
                                    <?php foreach ($field['values'] as $key => $value) { ?>
                                        <option value="<?php echo $key ?>" <?php if ($key == $field['value']) { ?>selected="selected"<?php } ?>><?php echo $value ?></option>
                                    <?php } ?>
                                </select>
                            <?php } ?>
                            <?php if ($field['type'] == 'select_from_api') { ?>
                                <select id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>">
                                    <?php foreach ($field['values'] as $key => $value) { ?>
                                        <option value="<?php echo $key ?>" <?php if ($key == $field['value']) { ?>selected="selected"<?php } ?>><?php echo $value ?></option>
                                    <?php } ?>
                                </select>
                            <?php } ?>
                            <?php if ($field['type'] == 'radio') { ?>
                                <?php foreach ($field['values'] as $key => $value) { ?>
                                    <label><input type="radio" id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" value="<?php echo $key ?>" <?php if ($key == $field['value']) { ?>checked="checked"<?php } ?> >&nbsp;<?php echo $value ?></label><br>
                                <?php } ?>
                            <?php } ?>
                            <?php if (!empty($field['error'])) { ?>
                                <span class="simplecheckout-error-text"><?php echo $field['error']; ?></span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php $i++; if ($i == $split_fields) { ?>
                        </table>
                        <table class="simplecheckout-customer">
                    <?php } ?>
                <?php } ?>
            </table>
            </div>
    </div>
</div>
<?php } ?>