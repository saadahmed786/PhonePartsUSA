<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
    <link rel="stylesheet" type="text/css" href="catalog/view/theme/<?php echo $template ?>/stylesheet/simple.css" />
    <script type="text/javascript" src="catalog/view/javascript/simpleregister.js"></script>
    <script type="text/javascript" src="catalog/view/javascript/jquery/jquery.maskedinput-1.3.min.js"></script>
    <?php if ($template == 'shoppica' || $template == 'shoppica2') { ?>
    <?php if ($template == 'shoppica') { ?>
    <script type="text/javascript" src="catalog/view/theme/<?php echo $this->config->get('config_template') ?>/js/jquery/jquery.prettyPhoto.js"></script>
    <link rel="stylesheet" type="text/css" href="catalog/view/theme/<?php echo $this->config->get('config_template') ?>/stylesheet/prettyPhoto.css" media="all" />
    <?php } elseif ($template == 'shoppica2') { ?>
    <script type="text/javascript" src="catalog/view/theme/<?php echo $this->config->get('config_template') ?>/javascript/prettyphoto/js/jquery.prettyPhoto.js"></script>
    <link rel="stylesheet" type="text/css" href="catalog/view/theme/<?php echo $this->config->get('config_template') ?>/javascript/prettyphoto/css/prettyPhoto.css" media="all" />
    <?php } ?><div id="intro">
        <div id="intro_wrap">
            <div class="container_12 s_wrap">
                <div id="breadcrumbs" class="grid_12 s_col_12">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
                </div>
                <h1><?php echo $heading_title; ?></h1>
            </div>
        </div>
    </div>
    <?php } else { ?>
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <h1><?php echo $heading_title; ?></h1>
    <?php } ?>
    <?php if ($error_warning) { ?>
        <div class="simplecheckout-warning-block"><?php echo $error_warning; ?></div>
    <?php } ?>
    <p class="simpleregister-have-account"><?php echo $text_account_already; ?></p>
    <h2 class="simpleregister-title"><?php echo $text_your_details; ?></h2>
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="simpleregister">
        <div class="simpleregister">
            <div class="simpleregister-block-content">
                <table class="simplecheckout-customer">
                    <?php foreach ($display_customer_fields as $field) { ?>
                        <tr>
                            <td class="simplecheckout-customer-left">
                                <?php if ($field['required']) { ?>
                                    <span class="simplecheckout-required">*</span>
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
                                    <select id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" <?php if ($field['id'] == 'main_country_id') { ?>onchange="$('#main_zone_id').load('index.php?route=account/simpleregister/zone&country_id=' + this.value);"<?php } ?>>
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
                        <?php if ($field['id'] == 'main_email') { ?>
                            <tr <?php echo $simple_registration_generate_password ? 'style="display:none;"' : '' ?>>
                                <td class="simplecheckout-customer-left">
                                    <span class="simplecheckout-required">*</span>
                                    <?php echo $entry_password ?>:
                                </td>
                                <td class="simplecheckout-customer-right">
                                    <input type="password" name="password" value="<?php echo $password ?>">
                                    <?php if ($error_password) { ?>
                                        <span class="simplecheckout-error-text"><?php echo $error_password; ?></span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php if ($simple_registration_password_confirm) { ?>
                            <tr <?php echo $simple_registration_generate_password ? 'style="display:none;"' : '' ?>>
                                <td class="simplecheckout-customer-left">
                                    <span class="simplecheckout-required">*</span>
                                    <?php echo $entry_password_confirm ?>:
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
                    <?php } ?>
                    <?php if ($simple_registration_subscribe == 2) { ?>
                        <tr>
                          <td class="simplecheckout-customer-left"><?php echo $entry_newsletter; ?></td>
                          <td class="simplecheckout-customer-right">
                            <label><input type="radio" name="subscribe" value="1" <?php if ($subscribe) { ?>checked="checked"<?php } ?> /><?php echo $text_yes; ?></label>
                            <label><input type="radio" name="subscribe" value="0" <?php if (!$subscribe) { ?>checked="checked"<?php } ?> /><?php echo $text_no; ?></label>
                        </tr>
                    <?php } ?>
                    <?php if ($simple_registration_captcha) { ?>
                        <tr>
                            <td class="simplecheckout-customer-left">
                                <span class="simplecheckout-required">*</span>
                                <?php echo $entry_captcha ?>:
                            </td>
                            <td class="simplecheckout-customer-right">
                                <input type="text" name="captcha" value="" />
                                <?php if ($error_captcha) { ?>
                                    <span class="simplecheckout-error-text"><?php echo $error_captcha; ?></span>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                          <td class="simplecheckout-customer-left"></td>
                          <td class="simplecheckout-customer-right"><img src="index.php?route=product/product/captcha" alt="" id="captcha" /></td>
                        </tr>
                    <?php } ?> 
                    <?php if ($simple_registration_view_customer_type) { ?>
                        <tr>
                            <td colspan="2" style="text-align:center;">
                                <label><input type="radio" name="customer_type" value="private" <?php if ($customer_type == 'private') { ?>checked="checked"<?php } ?>>&nbsp;<?php echo $text_private ?></label>
                                <label><input type="radio" name="customer_type" value="company" <?php if ($customer_type == 'company') { ?>checked="checked"<?php } ?>>&nbsp;<?php echo $text_company ?></label>
                            </td>
                        </tr>
                    <?php } ?>
                </table>                      
            </div>
            <?php if ($simple_registration_view_customer_type && !empty($display_company_fields)) { ?>
            <div id="simplecheckout_company" <?php echo $customer_type == 'private' ? 'style="display:none"' : ''?>>
            <h2><?php echo $text_company_details; ?></h2>
            <div class="simpleregister-block-content">
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
                    <?php } ?>
                </table>                      
            </div>
            </div>
            <?php } ?>
        </div>
        <div class="simplecheckout-button-block">
            <?php if ($template == 'shoppica' || $template == 'shoppica2') { ?>
                <?php if ($simple_registration_agreement_checkbox) { ?><label><input type="checkbox" name="agree" value="1" <?php if ($agree == 1) { ?>checked="checked"<?php } ?> />&nbsp;<?php echo $text_agree; ?></label><?php } ?><a onclick="$('#simpleregister').submit();" class="s_button_1 s_main_color_bgr"><span class="s_text"><?php echo $button_continue; ?></span></a>
            <?php } else { ?>
                <?php if ($simple_registration_agreement_checkbox) { ?><label><input type="checkbox" name="agree" value="1" <?php if ($agree == 1) { ?>checked="checked"<?php } ?> /><?php echo $text_agree; ?></label>&nbsp;<?php } ?><a onclick="$('#simpleregister').submit();" class="simplecheckout-button"><span><?php echo $button_continue; ?></span></a>
            <?php } ?>
        </div>  
    </form>
</div>
<?php echo $content_bottom; ?>
<?php echo $footer; ?>