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
      <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?> <?php echo $version; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
    </div>
    <div class="content">
      <pre style="display:none"><?php print_r($quickcheckout);?></pre>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form" >
          <tbody >
            <tr  style="border-bottom:none">
              <td colspan="3" style="border-bottom:none"><?php echo $checkout_heading; ?>
                <div id="checkout_intro" style=" display:none"> <?php echo $checkout_intro; ?> </div>
                <a id="checkout_intro_display"><?php echo $checkout_intro_display; ?></a>
                <script>
              $('#checkout_intro_display').click(function(){
				  $('#checkout_intro').toggle()
				  })
              </script></td>
            </tr>
            <tr>
              <td><?php echo $checkout_quickcheckout; ?></td>
              <td>
              	<input type="hidden" value="0" name="quickcheckout[checkout_enable]" />
                <?php if(isset($quickcheckout['checkout_enable']) && $quickcheckout['checkout_enable'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[checkout_enable]" checked="checked" />
                <?php echo $settings_enable; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[checkout_enable]" />
                <?php echo $settings_enable; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[quickcheckout_display]" />
                <?php if(isset($quickcheckout['quickcheckout_display']) && $quickcheckout['quickcheckout_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[quickcheckout_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[quickcheckout_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                </td>
              <td><a id="checkout_select_all">Select all</a> / <a id="checkout_unselect_all">Unselect all</a></td>
            </tr>
            <tr>
              <td><?php echo $checkout_debug; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[checkout_debug]" />
                <?php if(isset($quickcheckout['checkout_debug']) && $quickcheckout['checkout_debug'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[checkout_debug]" checked="checked" />
                <?php echo $settings_enable; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[checkout_debug]" />
                <?php echo $settings_enable; ?>
                <?php } ?></td>
              <td></td>
            </tr>
            <tr>
              <td><?php echo $checkout_compatibility; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[checkout_compatibility]" />
                <?php if(isset($quickcheckout['checkout_compatibility']) && $quickcheckout['checkout_compatibility'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[checkout_compatibility]" checked="checked" />
                <?php echo $settings_enable; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[checkout_compatibility]" />
                <?php echo $settings_enable; ?>
                <?php } ?></td>
              <td></td>
            </tr>
            <tr>
              <td><?php echo $checkout_defalt_option; ?></td>
              <td><?php if(isset($quickcheckout['checkout_defalt_option']) && $quickcheckout['checkout_defalt_option'] == 1){ ?>
                <input type="radio" value="1" name="quickcheckout[checkout_defalt_option]" checked="checked" />
                <?php echo $checkout_defalt_option_guest; ?>
                <input type="radio" value="0" name="quickcheckout[checkout_defalt_option]" />
                <?php echo $checkout_defalt_option_register; ?>
                <?php }else{ ?>
                <input type="radio" value="1" name="quickcheckout[checkout_defalt_option]" />
                <?php echo $checkout_defalt_option_guest; ?>
                <input type="radio" value="0" name="quickcheckout[checkout_defalt_option]" checked="checked" />
                <?php echo $checkout_defalt_option_register; ?>
                
                <?php } ?></td>
              <td></td>
            </tr>
            <tr>
              <td><?php echo $checkout_display_options; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[checkout_display_login]" />
                <?php if(isset($quickcheckout['checkout_display_login']) && $quickcheckout['checkout_display_login'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[checkout_display_login]" checked="checked" />
                <?php echo $checkout_display_login_text; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[checkout_display_login]" />
                <?php echo $checkout_display_login_text; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[checkout_display_register]" />
                <?php if(isset($quickcheckout['checkout_display_register']) && $quickcheckout['checkout_display_register'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[checkout_display_register]" checked="checked" />
                <?php echo $checkout_defalt_option_register; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[checkout_display_register]" />
                <?php echo $checkout_defalt_option_register; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[checkout_display_guest]" />
                <?php if(isset($quickcheckout['checkout_display_guest']) && $quickcheckout['checkout_display_guest'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[checkout_display_guest]" checked="checked" />
                <?php echo $checkout_defalt_option_guest; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[checkout_display_guest]" />
                <?php echo $checkout_defalt_option_guest; ?>
                <?php } ?></td>
              <td></td>
            </tr>
			<tr>
				<td><?php echo $checkout_display_only_register_options; ?></td>
				<td><input type="hidden" value="0" name="quickcheckout[checkout_display_only_register]" />
                <?php if(isset($quickcheckout['checkout_display_only_register']) && $quickcheckout['checkout_display_only_register'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[checkout_display_only_register]" checked="checked" />
                <?php echo $checkout_display_only_register_text; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[checkout_display_only_register]" />
                <?php echo $checkout_display_only_register_text; ?>
                <?php } ?></td>
				<td></td>
			</tr>
            <tr>
              <td><?php echo $checkout_min_order; ?></td>
              <td><?php if(isset($quickcheckout['checkout_min_order']) && $quickcheckout['checkout_min_order'] != ""){ ?>
                <input type="text" value="<?php echo $quickcheckout['checkout_min_order']; ?>" name="quickcheckout[checkout_min_order]" />
                <?php }else{ ?>
                <input type="text" value="0" name="quickcheckout[checkout_min_order]" />
                <?php } ?> <br />
                <?php echo $checkout_min_order_tag; ?> {min_order}</td>
              <td><?php foreach ($languages as $language) { ?>
          <div id="tab-limit-language-<?php echo $module_row; ?>-<?php echo $language['language_id']; ?>">
				<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?>
               <input name="quickcheckout[text_min_order][<?php echo $language['language_id']; ?>]" id="text_min_order_<?php echo $language['language_id']; ?>" value="<?php echo isset($quickcheckout['text_min_order'][$language['language_id']]) ? $quickcheckout['text_min_order'][$language['language_id']] : 'The minimum order is {min_order}'; ?>" style="width:80%">
               
          </div>
          <?php } ?></td>
            </tr>
          </tbody>
        </table>
        <table class="form" >
          <thead>
            <tr>
              <th></th>
              <th><?php echo $checkout_guest_step_1; ?></th>
              <th><?php echo $checkout_register_step_1; ?></th>
            </tr>
          </thead>
          <tbody class="sortable">
            <tr id="firstname_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_firstname_input']) ? $quickcheckout['sort_firstname_input'] : ''); ?>">
              <td><?php echo $checkout_firstname; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_firstname_input']) ? $quickcheckout['sort_firstname_input'] : ''); ?>" class="sort" name="quickcheckout[sort_firstname_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_firstname_display]" />
                <?php if(isset($quickcheckout['guest_firstname_display']) && $quickcheckout['guest_firstname_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_firstname_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_firstname_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[guest_firstname_require]" />
                <?php if(isset($quickcheckout['guest_firstname_require']) && $quickcheckout['guest_firstname_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_firstname_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_firstname_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_firstname_display]" />
                <?php if(isset($quickcheckout['register_firstname_display']) && $quickcheckout['register_firstname_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_firstname_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_firstname_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_firstname_require]" />
                <?php if(isset($quickcheckout['register_firstname_require']) && $quickcheckout['register_firstname_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_firstname_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_firstname_require]" />
                <?php echo $settings_require; ?>
                <?php } ?>
                </td>
            </tr>
            <tr id="lastname_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_lastname_input']) ? $quickcheckout['sort_lastname_input'] : ''); ?>">
              <td><?php echo $checkout_lastname; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_lastname_input']) ? $quickcheckout['sort_lastname_input'] : ''); ?>" class="sort" name="quickcheckout[sort_lastname_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_lastname_display]" />
                <?php if(isset($quickcheckout['guest_lastname_display']) && $quickcheckout['guest_lastname_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_lastname_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_lastname_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[guest_lastname_require]" />
                <?php if(isset($quickcheckout['guest_lastname_require']) && $quickcheckout['guest_lastname_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_lastname_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_lastname_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_lastname_display]" />
                <?php if(isset($quickcheckout['register_lastname_display']) && $quickcheckout['register_lastname_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_lastname_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_lastname_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_lastname_require]" />
                <?php if(isset($quickcheckout['register_lastname_require']) && $quickcheckout['register_lastname_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_lastname_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_lastname_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
            <tr id="email_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_email_input']) ? $quickcheckout['sort_email_input'] : ''); ?>">
              <td><?php echo $checkout_email; ?>
                <?php if(isset($quickcheckout['register_email']) && $quickcheckout['register_email'] != ""){ ?>
                <input type="text" value="<?php echo $quickcheckout['register_email']; ?>" name="quickcheckout[register_email]" />
                <?php }else{ ?>
                <input type="text" value="default@email.com" name="quickcheckout[register_email]" />
                <?php } ?>
                <input type="text"  value="<?php echo (isset($quickcheckout['sort_email_input']) ? $quickcheckout['sort_email_input'] : ''); ?>"  class="sort" name="quickcheckout[sort_email_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_email_display]" />
                <?php if(isset($quickcheckout['guest_email_display']) && $quickcheckout['guest_email_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_email_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_email_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[guest_email_require]" />
                <?php if(isset($quickcheckout['guest_email_require']) && $quickcheckout['guest_email_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_email_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_email_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_email_display]" />
                <?php if(isset($quickcheckout['register_email_display']) && $quickcheckout['register_email_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_email_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_email_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_email_require]" />
                <?php if(isset($quickcheckout['register_email_require']) && $quickcheckout['register_email_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_email_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_email_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
            <tr id="telephone_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_telephone_input']) ? $quickcheckout['sort_telephone_input'] : ''); ?>">
              <td><?php echo $checkout_telephone; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_telephone_input']) ? $quickcheckout['sort_telephone_input'] : ''); ?>" class="sort" name="quickcheckout[sort_telephone_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_telephone_display]" />
                <?php if(isset($quickcheckout['guest_telephone_display']) && $quickcheckout['guest_telephone_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_telephone_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_telephone_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[guest_telephone_require]" />
                <?php if(isset($quickcheckout['guest_telephone_require']) && $quickcheckout['guest_telephone_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_telephone_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_telephone_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_telephone_display]" />
                <?php if(isset($quickcheckout['register_telephone_display']) && $quickcheckout['register_telephone_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_telephone_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_telephone_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_telephone_require]" />
                <?php if(isset($quickcheckout['register_telephone_require']) && $quickcheckout['register_telephone_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_telephone_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_telephone_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
            <tr id="fax_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_fax_input']) ? $quickcheckout['sort_fax_input'] : ''); ?>">
              <td><?php echo $checkout_fax; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_fax_input']) ? $quickcheckout['sort_fax_input'] : ''); ?>" class="sort" name="quickcheckout[sort_fax_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_fax_display]" />
                <?php if(isset($quickcheckout['guest_fax_display']) && $quickcheckout['guest_fax_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_fax_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_fax_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_fax_display]" />
                <?php if(isset($quickcheckout['register_fax_display']) && $quickcheckout['register_fax_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_fax_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_fax_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
            </tr>
            <tr id="password_group_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_password_group_input']) ? $quickcheckout['sort_password_group_input'] : ''); ?>">
              <td><?php echo $checkout_password; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_password_group_input']) ? $quickcheckout['sort_password_group_input'] : ''); ?>" class="sort" name="quickcheckout[sort_password_group_input]" /></td>
              <td></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_password_display]" />
                <?php if(isset($quickcheckout['register_password_display']) && $quickcheckout['register_password_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_password_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_password_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_password_require]" />
                <?php if(isset($quickcheckout['register_password_require']) && $quickcheckout['register_password_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_password_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_password_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
          </tbody>
        </table>
        <table class="form" >
          <thead>
            <tr>
              <th></th>
              <th><?php echo $checkout_guest_step_2; ?></th>
              <th><?php echo $checkout_register_step_2; ?></th>
            </tr>
          </thead>
          <tbody class="sortable">
            <tr sort-data="-1">
              <td><?php echo $checkout_payment_address; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_payment_address_display]" />
                <?php if(isset($quickcheckout['guest_payment_address_display']) && $quickcheckout['guest_payment_address_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_payment_address_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_payment_address_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_payment_address_display]" />
                <?php if(isset($quickcheckout['register_payment_address_display']) && $quickcheckout['register_payment_address_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_payment_address_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_payment_address_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
            </tr>
            <tr id="company_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_company_input']) ? $quickcheckout['sort_company_input'] : ''); ?>">
              <td><?php echo $checkout_company; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_company_input']) ? $quickcheckout['sort_company_input'] : ''); ?>" class="sort" name="quickcheckout[sort_company_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_company_display]" />
                <?php if(isset($quickcheckout['guest_company_display']) && $quickcheckout['guest_company_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_company_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_company_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_company_display]" />
                <?php if(isset($quickcheckout['register_company_display']) && $quickcheckout['register_company_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_company_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_company_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
            </tr>
            <tr id="customer_group_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_customer_group_input']) ? $quickcheckout['sort_customer_group_input'] : ''); ?>">
              <td><?php echo $checkout_customer_group; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_customer_group_input']) ? $quickcheckout['sort_customer_group_input'] : ''); ?>" class="sort" name="quickcheckout[sort_customer_group_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_customer_group_display]" />
                <?php if(isset($quickcheckout['guest_customer_group_display']) && $quickcheckout['guest_customer_group_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_customer_group_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_customer_group_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_customer_group_display]" />
                <?php if(isset($quickcheckout['register_customer_group_display']) && $quickcheckout['register_customer_group_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_customer_group_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_customer_group_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
            </tr>
            <tr id="company_id_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_company_id_input']) ? $quickcheckout['sort_company_id_input'] : ''); ?>">
              <td><?php echo $checkout_company_id; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_company_id_input']) ? $quickcheckout['sort_company_id_input'] : ''); ?>" class="sort" name="quickcheckout[sort_company_id_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_company_id_display]" />
                <?php if(isset($quickcheckout['guest_company_id_display']) && $quickcheckout['guest_company_id_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_company_id_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_company_id_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[guest_company_id_require]" />
                <?php if(isset($quickcheckout['guest_company_id_require']) && $quickcheckout['guest_company_id_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_company_id_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_company_id_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_company_id_display]" />
                <?php if(isset($quickcheckout['register_company_id_display']) && $quickcheckout['register_company_id_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_company_id_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_company_id_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_company_id_require]" />
                <?php if(isset($quickcheckout['register_company_id_require']) && $quickcheckout['register_company_id_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_company_id_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_company_id_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
            <tr id="tax_id_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_tax_id_input']) ? $quickcheckout['sort_tax_id_input'] : ''); ?>">
              <td><?php echo $checkout_tax_id; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_tax_id_input']) ? $quickcheckout['sort_tax_id_input'] : ''); ?>" class="sort" name="quickcheckout[sort_tax_id_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_tax_id_display]" />
                <?php if(isset($quickcheckout['guest_tax_id_display']) && $quickcheckout['guest_tax_id_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_tax_id_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_tax_id_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[guest_tax_id_require]" />
                <?php if(isset($quickcheckout['guest_tax_id_require']) && $quickcheckout['guest_tax_id_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_tax_id_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_tax_id_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_tax_id_display]" />
                <?php if(isset($quickcheckout['register_tax_id_display']) && $quickcheckout['register_tax_id_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_tax_id_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_tax_id_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_tax_id_require]" />
                <?php if(isset($quickcheckout['register_tax_id_require']) && $quickcheckout['register_tax_id_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_tax_id_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_tax_id_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
            <tr id="address_1_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_address_1_input']) ? $quickcheckout['sort_address_1_input'] : ''); ?>">
              <td><?php echo $checkout_address_1; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_address_1_input']) ? $quickcheckout['sort_address_1_input'] : ''); ?>" class="sort" name="quickcheckout[sort_address_1_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_address_1_display]" />
                <?php if(isset($quickcheckout['guest_address_1_display']) && $quickcheckout['guest_address_1_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_address_1_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_address_1_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[guest_address_1_require]" />
                <?php if(isset($quickcheckout['guest_address_1_require']) && $quickcheckout['guest_address_1_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_address_1_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_address_1_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_address_1_display]" />
                <?php if(isset($quickcheckout['register_address_1_display']) && $quickcheckout['register_address_1_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_address_1_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_address_1_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_address_1_require]" />
                <?php if(isset($quickcheckout['register_address_1_require']) && $quickcheckout['register_address_1_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_address_1_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_address_1_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
            <tr id="address_2_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_address_2_input']) ? $quickcheckout['sort_address_2_input'] : ''); ?>">
              <td><?php echo $checkout_address_2; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_address_2_input']) ? $quickcheckout['sort_address_2_input'] : ''); ?>" class="sort" name="quickcheckout[sort_address_2_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_address_2_display]" />
                <?php if(isset($quickcheckout['guest_address_2_display']) && $quickcheckout['guest_address_2_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_address_2_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_address_2_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_address_2_display]" />
                <?php if(isset($quickcheckout['register_address_2_display']) && $quickcheckout['register_address_2_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_address_2_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_address_2_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
            </tr>
            <tr id="city_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_city_input']) ? $quickcheckout['sort_city_input'] : ''); ?>">
              <td><?php echo $checkout_city; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_city_input']) ? $quickcheckout['sort_city_input'] : ''); ?>" class="sort" name="quickcheckout[sort_city_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_city_display]" />
                <?php if(isset($quickcheckout['guest_city_display']) && $quickcheckout['guest_city_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_city_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_city_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[guest_city_require]" />
                <?php if(isset($quickcheckout['guest_city_require']) && $quickcheckout['guest_city_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_city_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_city_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_city_display]" />
                <?php if(isset($quickcheckout['register_city_display']) && $quickcheckout['register_city_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_city_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_city_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_city_require]" />
                <?php if(isset($quickcheckout['register_city_require']) && $quickcheckout['register_city_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_city_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_city_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
            <tr id="postcode_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_postcode_input']) ? $quickcheckout['sort_postcode_input'] : ''); ?>">
              <td><?php echo $checkout_postcode; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_postcode_input']) ? $quickcheckout['sort_postcode_input'] : ''); ?>" class="sort" name="quickcheckout[sort_postcode_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_postcode_display]" />
                <?php if(isset($quickcheckout['guest_postcode_display']) && $quickcheckout['guest_postcode_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_postcode_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_postcode_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[guest_postcode_require]" />
                <?php if(isset($quickcheckout['guest_postcode_require']) && $quickcheckout['guest_postcode_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_postcode_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_postcode_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_postcode_display]" />
                <?php if(isset($quickcheckout['register_postcode_display']) && $quickcheckout['register_postcode_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_postcode_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_postcode_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_postcode_require]" />
                <?php if(isset($quickcheckout['register_postcode_require']) && $quickcheckout['register_postcode_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_postcode_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_postcode_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
            <tr id="country_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_country_input']) ? $quickcheckout['sort_country_input'] : ''); ?>">
              <td><?php echo $checkout_country; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_country_input']) ? $quickcheckout['sort_country_input'] : ''); ?>" class="sort" name="quickcheckout[sort_country_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_country_display]" />
                <?php if(isset($quickcheckout['guest_country_display']) && $quickcheckout['guest_country_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_country_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_country_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[guest_country_require]" />
                <?php if(isset($quickcheckout['guest_country_require']) && $quickcheckout['guest_country_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_country_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_country_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_country_display]" />
                <?php if(isset($quickcheckout['register_country_display']) && $quickcheckout['register_country_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_country_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_country_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_country_require]" />
                <?php if(isset($quickcheckout['register_country_require']) && $quickcheckout['register_country_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_country_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_country_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
            <tr id="zone_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_zone_input']) ? $quickcheckout['sort_zone_input'] : ''); ?>">
              <td><?php echo $checkout_zone; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_zone_input']) ? $quickcheckout['sort_zone_input'] : ''); ?>" class="sort" name="quickcheckout[sort_zone_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_zone_display]" />
                <?php if(isset($quickcheckout['guest_zone_display']) && $quickcheckout['guest_zone_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_zone_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_zone_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[guest_zone_require]" />
                <?php if(isset($quickcheckout['guest_zone_require']) && $quickcheckout['guest_zone_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_zone_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_zone_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_zone_display]" />
                <?php if(isset($quickcheckout['register_zone_display']) && $quickcheckout['register_zone_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_zone_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_zone_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_zone_require]" />
                <?php if(isset($quickcheckout['register_zone_require']) && $quickcheckout['register_zone_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_zone_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_zone_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
            <tr sort-data="1000">
              <td><?php echo $checkout_newsletter; ?></td>
              <td></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_newsletter_display]" />
                <?php if(isset($quickcheckout['register_newsletter_display']) && $quickcheckout['register_newsletter_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_newsletter_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_newsletter_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
            </tr>
          </tbody>
        </table>
        <table class="form" >
          <thead>
            <tr>
              <th></th>
              <th><?php echo $checkout_guest_step_3; ?></th>
              <th><?php echo $checkout_register_step_3; ?></th>
            </tr>
          </thead>
          <tbody class="sortable">
           <tr sort-data="-2">
              <td><?php echo $checkout_shipping_address_enable; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_shipping_address_enable]" />
                <?php if(isset($quickcheckout['guest_shipping_address_enable']) && $quickcheckout['guest_shipping_address_enable'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_address_enable]" checked="checked" />
                <?php echo $settings_enable; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_address_enable]" />
                <?php echo $settings_enable; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_shipping_address_enable]" />
                <?php if(isset($quickcheckout['register_shipping_address_enable']) && $quickcheckout['register_shipping_address_enable'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_address_enable]" checked="checked" />
                <?php echo $settings_enable; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_address_enable]" />
                <?php echo $settings_enable; ?>
                <?php } ?></td>
            </tr>
            <tr sort-data="-1">
              <td><?php echo $checkout_shipping_address; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_shipping_address_display]" />
                <?php if(isset($quickcheckout['guest_shipping_address_display']) && $quickcheckout['guest_shipping_address_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_address_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_address_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_shipping_address_display]" />
                <?php if(isset($quickcheckout['register_shipping_address_display']) && $quickcheckout['register_shipping_address_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_address_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_address_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
            </tr>
            <tr id="shipping_firstname_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_shipping_firstname_input']) ? $quickcheckout['sort_shipping_firstname_input'] : ''); ?>">
              <td><?php echo $checkout_firstname; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_shipping_firstname_input']) ? $quickcheckout['sort_shipping_firstname_input'] : ''); ?>" class="sort" name="quickcheckout[sort_shipping_firstname_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_shipping_firstname_display]" />
                <?php if(isset($quickcheckout['guest_shipping_firstname_display']) && $quickcheckout['guest_shipping_firstname_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_firstname_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_firstname_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[guest_shipping_firstname_require]" />
                <?php if(isset($quickcheckout['guest_shipping_firstname_require']) && $quickcheckout['guest_shipping_firstname_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_firstname_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_firstname_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_shipping_firstname_display]" />
                <?php if(isset($quickcheckout['register_shipping_firstname_display']) && $quickcheckout['register_shipping_firstname_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_firstname_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_firstname_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_shipping_firstname_require]" />
                <?php if(isset($quickcheckout['register_shipping_firstname_require']) && $quickcheckout['register_shipping_firstname_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_firstname_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_firstname_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
            <tr id="shipping_lastname_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_shipping_lastname_input']) ? $quickcheckout['sort_shipping_lastname_input'] : ''); ?>">
              <td><?php echo $checkout_lastname; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_shipping_lastname_input']) ? $quickcheckout['sort_shipping_lastname_input'] : ''); ?>" class="sort" name="quickcheckout[sort_shipping_lastname_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_shipping_lastname_display]" />
                <?php if(isset($quickcheckout['guest_shipping_lastname_display']) && $quickcheckout['guest_shipping_lastname_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_lastname_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_lastname_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[guest_shipping_lastname_require]" />
                <?php if(isset($quickcheckout['guest_shipping_lastname_require']) && $quickcheckout['guest_shipping_lastname_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_lastname_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_lastname_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_shipping_lastname_display]" />
                <?php if(isset($quickcheckout['register_shipping_lastname_display']) && $quickcheckout['register_shipping_lastname_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_lastname_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_lastname_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_shipping_lastname_require]" />
                <?php if(isset($quickcheckout['register_shipping_lastname_require']) && $quickcheckout['register_shipping_lastname_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_lastname_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_lastname_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
            <tr id="shipping_company_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_shipping_company_input']) ? $quickcheckout['sort_shipping_company_input'] : ''); ?>">
              <td><?php echo $checkout_company; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_shipping_company_input']) ? $quickcheckout['sort_shipping_company_input'] : ''); ?>" class="sort" name="quickcheckout[sort_shipping_company_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_shipping_company_display]" />
                <?php if(isset($quickcheckout['guest_shipping_company_display']) && $quickcheckout['guest_shipping_company_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_company_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_company_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_shipping_company_display]" />
                <?php if(isset($quickcheckout['register_shipping_company_display']) && $quickcheckout['register_shipping_company_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_company_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_company_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
            </tr>
            <tr id="shipping_address_1_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_shipping_address_1_input']) ? $quickcheckout['sort_shipping_address_1_input'] : ''); ?>">
              <td><?php echo $checkout_address_1; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_shipping_address_1_input']) ? $quickcheckout['sort_shipping_address_1_input'] : ''); ?>" class="sort" name="quickcheckout[sort_shipping_address_1_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_shipping_address_1_display]" />
                <?php if(isset($quickcheckout['guest_shipping_address_1_display']) && $quickcheckout['guest_shipping_address_1_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_address_1_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_address_1_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[guest_shipping_address_1_require]" />
                <?php if(isset($quickcheckout['guest_shipping_address_1_require']) && $quickcheckout['guest_shipping_address_1_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_address_1_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_address_1_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_shipping_address_1_display]" />
                <?php if(isset($quickcheckout['register_shipping_address_1_display']) && $quickcheckout['register_shipping_address_1_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_address_1_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_address_1_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_shipping_address_1_require]" />
                <?php if(isset($quickcheckout['register_shipping_address_1_require']) && $quickcheckout['register_shipping_address_1_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_address_1_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_address_1_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
            <tr id="shipping_address_2_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_shipping_address_2_input']) ? $quickcheckout['sort_shipping_address_2_input'] : ''); ?>">
              <td><?php echo $checkout_address_2; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_shipping_address_2_input']) ? $quickcheckout['sort_shipping_address_2_input'] : ''); ?>" class="sort" name="quickcheckout[sort_shipping_address_2_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_shipping_address_2_display]" />
                <?php if(isset($quickcheckout['guest_shipping_address_2_display']) && $quickcheckout['guest_shipping_address_2_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_address_2_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_address_2_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_shipping_address_2_display]" />
                <?php if(isset($quickcheckout['register_shipping_address_2_display']) && $quickcheckout['register_shipping_address_2_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_address_2_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_address_2_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
            </tr>
            <tr id="shipping_city_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_shipping_city_input']) ? $quickcheckout['sort_shipping_city_input'] : ''); ?>">
              <td><?php echo $checkout_city; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_shipping_city_input']) ? $quickcheckout['sort_shipping_city_input'] : ''); ?>" class="sort" name="quickcheckout[sort_shipping_city_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_shipping_city_display]" />
                <?php if(isset($quickcheckout['guest_shipping_city_display']) && $quickcheckout['guest_shipping_city_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_city_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_city_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[guest_shipping_city_require]" />
                <?php if(isset($quickcheckout['guest_shipping_city_require']) && $quickcheckout['guest_shipping_city_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_city_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_city_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_shipping_city_display]" />
                <?php if(isset($quickcheckout['register_shipping_city_display']) && $quickcheckout['register_shipping_city_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_city_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_city_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_shipping_city_require]" />
                <?php if(isset($quickcheckout['register_shipping_city_require']) && $quickcheckout['register_shipping_city_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_city_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_city_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
            <tr id="shipping_postcode_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_shipping_postcode_input']) ? $quickcheckout['sort_shipping_postcode_input'] : ''); ?>">
              <td><?php echo $checkout_postcode; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_shipping_postcode_input']) ? $quickcheckout['sort_shipping_postcode_input'] : ''); ?>" class="sort" name="quickcheckout[sort_shipping_postcode_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_shipping_postcode_display]" />
                <?php if(isset($quickcheckout['guest_shipping_postcode_display']) && $quickcheckout['guest_shipping_postcode_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_postcode_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_postcode_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[guest_shipping_postcode_require]" />
                <?php if(isset($quickcheckout['guest_shipping_postcode_require']) && $quickcheckout['guest_shipping_postcode_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_postcode_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_postcode_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_shipping_postcode_display]" />
                <?php if(isset($quickcheckout['register_shipping_postcode_display']) && $quickcheckout['register_shipping_postcode_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_postcode_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_postcode_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_shipping_postcode_require]" />
                <?php if(isset($quickcheckout['register_shipping_postcode_require']) && $quickcheckout['register_shipping_postcode_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_postcode_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_postcode_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
            <tr id="shipping_country_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_shipping_country_input']) ? $quickcheckout['sort_shipping_country_input'] : ''); ?>">
              <td><?php echo $checkout_country; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_shipping_country_input']) ? $quickcheckout['sort_shipping_country_input'] : ''); ?>" class="sort" name="quickcheckout[sort_shipping_country_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_shipping_country_display]" />
                <?php if(isset($quickcheckout['guest_shipping_country_display']) && $quickcheckout['guest_shipping_country_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_country_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_country_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[guest_shipping_country_require]" />
                <?php if(isset($quickcheckout['guest_shipping_country_require']) && $quickcheckout['guest_shipping_country_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_country_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_country_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_shipping_country_display]" />
                <?php if(isset($quickcheckout['register_shipping_country_display']) && $quickcheckout['register_shipping_country_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_country_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_country_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_shipping_country_require]" />
                <?php if(isset($quickcheckout['register_shipping_country_require']) && $quickcheckout['register_shipping_country_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_country_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_country_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
            <tr id="shipping_zone_input" class="sort-item" sort-data="<?php echo (isset($quickcheckout['sort_shipping_zone_input']) ? $quickcheckout['sort_shipping_zone_input'] : ''); ?>">
              <td><?php echo $checkout_zone; ?>
                <input type="text" value="<?php echo (isset($quickcheckout['sort_shipping_zone_input']) ? $quickcheckout['sort_shipping_zone_input'] : ''); ?>" class="sort" name="quickcheckout[sort_shipping_zone_input]" /></td>
              <td><input type="hidden" value="0" name="quickcheckout[guest_shipping_zone_display]" />
                <?php if(isset($quickcheckout['guest_shipping_zone_display']) && $quickcheckout['guest_shipping_zone_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_zone_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_zone_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[guest_shipping_zone_require]" />
                <?php if(isset($quickcheckout['guest_shipping_zone_require']) && $quickcheckout['guest_shipping_zone_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_zone_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[guest_shipping_zone_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_shipping_zone_display]" />
                <?php if(isset($quickcheckout['register_shipping_zone_display']) && $quickcheckout['register_shipping_zone_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_zone_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_zone_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[register_shipping_zone_require]" />
                <?php if(isset($quickcheckout['register_shipping_zone_require']) && $quickcheckout['register_shipping_zone_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_zone_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_shipping_zone_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
            <tr sort-data="1000">
              <td><?php echo $checkout_privacy_agree; ?></td>
              <td></td>
              <td><input type="hidden" value="0" name="quickcheckout[register_privacy_agree_display]" />
                <?php if(isset($quickcheckout['register_privacy_agree_display']) && $quickcheckout['register_privacy_agree_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_privacy_agree_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_privacy_agree_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <?php if(isset($quickcheckout['register_privacy_agree_require']) && $quickcheckout['register_privacy_agree_require'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_privacy_agree_require]" checked="checked" />
                <?php echo $settings_require; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[register_privacy_agree_require]" />
                <?php echo $settings_require; ?>
                <?php } ?></td>
            </tr>
          </tbody>
        </table>
        <table class="form" >
          <thead>
            <tr>
              <th colspan="3"><?php echo $checkout_step_4; ?></th>
            </tr>
          </thead>
          <tbody >
            <tr>
              <td><?php echo $checkout_shipping_method; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[shipping_method_display]" />
                <?php if(isset($quickcheckout['shipping_method_display']) && $quickcheckout['shipping_method_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[shipping_method_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[shipping_method_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td></td>
            </tr>
            <tr>
              <td><?php echo $checkout_shipping_method_methods; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[shipping_method_methods_display]" />
                <?php if(isset($quickcheckout['shipping_method_methods_display']) && $quickcheckout['shipping_method_methods_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[shipping_method_methods_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[shipping_method_methods_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[shipping_method_methods_select]" />
                <?php if(isset($quickcheckout['shipping_method_methods_select']) && $quickcheckout['shipping_method_methods_select'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[shipping_method_methods_select]" checked="checked" />
                <?php echo $settings_select; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[shipping_method_methods_select]" />
                <?php echo $settings_select; ?>
                <?php } ?></td>
              <td></td>
            </tr>
            <tr>
              <td><?php echo $checkout_shipping_method_date; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[shipping_method_date_display]" />
                <?php if(isset($quickcheckout['shipping_method_date_display']) && $quickcheckout['shipping_method_date_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[shipping_method_date_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[shipping_method_date_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                
                <input type="hidden" value="0" name="quickcheckout[shipping_method_date_picker]" />
                <?php if(isset($quickcheckout['shipping_method_date_picker']) && $quickcheckout['shipping_method_date_picker'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[shipping_method_date_picker]" checked="checked" />
                <?php echo $shipping_method_date_picker; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[shipping_method_date_picker]" />
                <?php echo $shipping_method_date_picker; ?>
                <?php } ?></td>
              <td>
              <?php foreach ($languages as $language) { ?>
          <div id="tab-language-<?php echo $module_row; ?>-<?php echo $language['language_id']; ?>">
				<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?>
               <input name="quickcheckout[text_shipping_date][<?php echo $language['language_id']; ?>]" id="text_shipping_date_<?php echo $language['language_id']; ?>" value="<?php echo isset($quickcheckout['text_shipping_date'][$language['language_id']]) ? $quickcheckout['text_shipping_date'][$language['language_id']] : ''; ?>">
               
          </div>
          <?php } ?>
              </td>
            </tr>
            <tr>
              <td><?php echo $checkout_shipping_method_title; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[shipping_method_title_display]" />
                <?php if(isset($quickcheckout['shipping_method_title_display']) && $quickcheckout['shipping_method_title_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[shipping_method_title_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[shipping_method_title_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td></td>
            </tr>
            <tr>
              <td><?php echo $checkout_shipping_method_comment; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[shipping_method_comment_display]" />
                <?php if(isset($quickcheckout['shipping_method_comment_display']) && $quickcheckout['shipping_method_comment_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[shipping_method_comment_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[shipping_method_comment_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td></td>
            </tr>
          </tbody>
        </table>
        <table class="form" >
          <thead>
            <tr>
              <th colspan="3"><?php echo $checkout_step_5; ?></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><?php echo $checkout_payment_method; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[payment_method_display]" />
                <?php if(isset($quickcheckout['payment_method_display']) && $quickcheckout['payment_method_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[payment_method_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[payment_method_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td></td>
            </tr>
            <tr>
              <td><?php echo $checkout_payment_method_methods; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[payment_method_methods_display]" />
                <?php if(isset($quickcheckout['payment_method_methods_display']) && $quickcheckout['payment_method_methods_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[payment_method_methods_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[payment_method_methods_display]" />
                <?php echo $settings_display; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[payment_method_methods_select]" />
                <?php if(isset($quickcheckout['payment_method_methods_select']) && $quickcheckout['payment_method_methods_select'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[payment_method_methods_select]" checked="checked" />
                <?php echo $settings_select; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[payment_method_methods_select]" />
                <?php echo $settings_select; ?>
                <?php } ?>
                <input type="hidden" value="0" name="quickcheckout[payment_method_methods_image]" />
                <?php if(isset($quickcheckout['payment_method_methods_image']) && $quickcheckout['payment_method_methods_image'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[payment_method_methods_image]" checked="checked" />
                <?php echo $settings_image; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[payment_method_methods_image]" />
                <?php echo $settings_image; ?>
                <?php } ?></td>
              <td></td>
            </tr>
            <tr>
              <td><?php echo $checkout_payment_method_comment; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[payment_method_comment_display]" />
                <?php if(isset($quickcheckout['payment_method_comment_display']) && $quickcheckout['payment_method_comment_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[payment_method_comment_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[payment_method_comment_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td></td>
            </tr>
            <tr>
              <td><?php echo $checkout_payment_method_agree; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[payment_method_agree_display]" />
                <?php if(isset($quickcheckout['payment_method_agree_display']) && $quickcheckout['payment_method_agree_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[payment_method_agree_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[payment_method_agree_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td></td>
            </tr>
            <tr>
              <td style="background-color:#FFC"><?php echo $checkout_payment_method_methods_steps; ?></td>
              <td style="background-color:#FFC"><?php echo $settings_second_step; ?></td>
              <td style="background-color:#FFC"></td>
            </tr>
            <?php foreach ($payment_methods as $payment_method) {?>
            <tr>
              <td><?php echo $payment_method['title']; ?> <small> (<?php echo $payment_method['code']; ?>)</small></td>
              <td><input type="hidden" value="0" name="quickcheckout[payment_method_second_step][<?php echo $payment_method['code']; ?>]" />
                <?php if(isset($quickcheckout['payment_method_second_step'][$payment_method['code']])){
                	if($quickcheckout['payment_method_second_step'][$payment_method['code']] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[payment_method_second_step][<?php echo $payment_method['code']; ?>]" checked="checked" /> 
                
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[payment_method_second_step][<?php echo $payment_method['code']; ?>]" /> 
               
                <?php }
                }else{?>
                <input type="checkbox" value="1" name="quickcheckout[payment_method_second_step][<?php echo $payment_method['code']; ?>]" checked="checked" /> 
                <?php } ?> <?php echo $settings_require; ?></td>
              <td></td>
            </tr>
           <?php } ?>
            
            
          </tbody>
        </table>
        <table class="form" >
          <thead>
            <tr>
              <th colspan="3"><?php echo $checkout_step_6; ?></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><?php echo $checkout_confirm_images; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[confirm_images_display]" />
                <?php if(isset($quickcheckout['confirm_images_display']) && $quickcheckout['confirm_images_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[confirm_images_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[confirm_images_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td></td>
            </tr>
             <tr>
              <td><?php echo $checkout_confirm_name; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[confirm_name_display]" />
                <?php if(isset($quickcheckout['confirm_name_display']) && $quickcheckout['confirm_name_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[confirm_name_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[confirm_name_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td></td>
            </tr>
             <tr>
              <td><?php echo $checkout_confirm_model; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[confirm_model_display]" />
                <?php if(isset($quickcheckout['confirm_model_display']) && $quickcheckout['confirm_model_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[confirm_model_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[confirm_model_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td></td>
            </tr>
            <tr>
              <td><?php echo $checkout_confirm_quantity; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[confirm_quantity_display]" />
                <?php if(isset($quickcheckout['confirm_quantity_display']) && $quickcheckout['confirm_quantity_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[confirm_quantity_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[confirm_quantity_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td></td>
            </tr>
            <tr>
              <td><?php echo $checkout_confirm_price; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[confirm_price_display]" />
                <?php if(isset($quickcheckout['confirm_price_display']) && $quickcheckout['confirm_price_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[confirm_price_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[confirm_price_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td></td>
            </tr>
            <tr>
              <td><?php echo $checkout_confirm_total; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[confirm_total_display]" />
                <?php if(isset($quickcheckout['confirm_total_display']) && $quickcheckout['confirm_total_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[confirm_total_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[confirm_total_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td></td>
            </tr>
             <tr>
              <td><?php echo $confirm_coupon_display; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[confirm_coupon_display]" />
                <?php if(isset($quickcheckout['confirm_coupon_display']) && $quickcheckout['confirm_coupon_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[confirm_coupon_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[confirm_coupon_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td></td>
            </tr>
             <tr>
              <td><?php echo $confirm_voucher_display; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[confirm_voucher_display]" />
                <?php if(isset($quickcheckout['confirm_voucher_display']) && $quickcheckout['confirm_voucher_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[confirm_voucher_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[confirm_voucher_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td></td>
            </tr>
            <tr>
              <td><?php echo $confirm_2_step_cart_display; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[confirm_2_step_cart_display]" />
                <?php if(isset($quickcheckout['confirm_2_step_cart_display']) && $quickcheckout['confirm_2_step_cart_display'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[confirm_2_step_cart_display]" checked="checked" />
                <?php echo $settings_display; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[confirm_2_step_cart_display]" />
                <?php echo $settings_display; ?>
                <?php } ?></td>
              <td></td>
            </tr>
          </tbody>
        </table>
        <table class="form" >
          <thead>
            <tr>
              <th colspan="3"><?php echo $checkout_design; ?></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><?php echo $checkout_labels_float; ?></td>
              <td><?php if(isset($quickcheckout['checkout_labels_float']) && $quickcheckout['checkout_labels_float'] == 1){ ?>
                <input type="radio" value="1" name="quickcheckout[checkout_labels_float]" checked="checked" />
                <?php echo $checkout_labels_float_left; ?>
                <input type="radio" value="0" name="quickcheckout[checkout_labels_float]" />
                <?php echo $checkout_labels_float_clear; ?>
                <?php }else{ ?>
                <input type="radio" value="1" name="quickcheckout[checkout_labels_float]" />
                <?php echo $checkout_labels_float_left; ?>
                <input type="radio" value="0" name="quickcheckout[checkout_labels_float]" checked="checked" />
                <?php echo $checkout_labels_float_clear; ?>
                <?php } ?></td>
              <td></td>
            </tr>
            <tr>
              <td><?php echo $checkout_force_default_style; ?></td>
              <td><input type="hidden" value="0" name="quickcheckout[checkout_force_default_style]" />
              <?php if(isset($quickcheckout['checkout_force_default_style']) && $quickcheckout['checkout_force_default_style'] == 1){ ?>
                <input type="checkbox" value="1" name="quickcheckout[checkout_force_default_style]" checked="checked" />
                <?php echo $settings_enable; ?>
                <?php }else{ ?>
                <input type="checkbox" value="1" name="quickcheckout[checkout_force_default_style]" />
                <?php echo $settings_enable; ?>
                <?php } ?></td>
              <td></td>
            </tr>
            <tr>
              <td><?php echo $checkout_style; ?></td>
              <td class="gridster-holder"><div class="columns">
                  <input type="text"  class="column-data-1 col" name="quickcheckout[column_width][column-1]" value="<?php echo $quickcheckout['column_width']['column-1']; ?>" />
                  <input type="text"  class="column-data-2 col" name="quickcheckout[column_width][column-2]" value="<?php echo $quickcheckout['column_width']['column-2']; ?>" />
                  <input type="text"  class="column-data-3 col" name="quickcheckout[column_width][column-3]" value="<?php echo $quickcheckout['column_width']['column-3']; ?>" />
                </div>
                <div id="slider"></div>
                <ul class="column column-1" col-data="1">
                  <li class="portlet" col-data="<?php echo $quickcheckout['portlets'][0]['col']; ?>" row-data="<?php echo $quickcheckout['portlets'][0]['row']; ?>">
                    <div class="portlet-header"><?php echo $checkout_design_cutomer_info; ?></div>
                    <div class="portlet-content">
                      <input type="text"  class="sort col-data" name="quickcheckout[portlets][0][col]" value="<?php echo $quickcheckout['portlets'][0]['col']; ?>" />
                      <input type="text"  class="sort row-data" name="quickcheckout[portlets][0][row]" value="<?php echo $quickcheckout['portlets'][0]['row']; ?>" />
                    </div>
                  </li>
                  <li class="portlet" col-data="<?php echo $quickcheckout['portlets'][1]['col']; ?>" row-data="<?php echo $quickcheckout['portlets'][1]['row']; ?>">
                    <div class="portlet-header"><?php echo $checkout_design_shipping_address; ?></div>
                    <div class="portlet-content">
                      <input type="text"  class="sort col-data" name="quickcheckout[portlets][1][col]" value="<?php echo $quickcheckout['portlets'][1]['col']; ?>" />
                      <input type="text"  class="sort row-data" name="quickcheckout[portlets][1][row]" value="<?php echo $quickcheckout['portlets'][1]['row']; ?>" />
                    </div>
                  </li>
                  <li class="portlet" col-data="<?php echo $quickcheckout['portlets'][2]['col']; ?>" row-data="<?php echo $quickcheckout['portlets'][2]['row']; ?>">
                    <div class="portlet-header"><?php echo $checkout_design_shipping_method; ?></div>
                    <div class="portlet-content">
                      <input type="text"  class="sort col-data" name="quickcheckout[portlets][2][col]" value="<?php echo $quickcheckout['portlets'][2]['col']; ?>" />
                      <input type="text"  class="sort row-data" name="quickcheckout[portlets][2][row]" value="<?php echo $quickcheckout['portlets'][2]['row']; ?>" />
                    </div>
                  </li>
                  <li class="portlet" col-data="<?php echo $quickcheckout['portlets'][3]['col']; ?>" row-data="<?php echo $quickcheckout['portlets'][3]['row']; ?>">
                    <div class="portlet-header"><?php echo $checkout_design_payment_method; ?></div>
                    <div class="portlet-content">
                      <input type="text"  class="sort col-data" name="quickcheckout[portlets][3][col]" value="<?php echo $quickcheckout['portlets'][3]['col']; ?>" />
                      <input type="text"  class="sort row-data" name="quickcheckout[portlets][3][row]" value="<?php echo $quickcheckout['portlets'][3]['row']; ?>" />
                    </div>
                  </li>
                  <li class="portlet" col-data="<?php echo $quickcheckout['portlets'][4]['col']; ?>" row-data="<?php echo $quickcheckout['portlets'][4]['row']; ?>">
                    <div class="portlet-header"><?php echo $checkout_design_confirm; ?></div>
                    <div class="portlet-content">
                      <input type="text"  class="sort col-data" name="quickcheckout[portlets][4][col]" value="<?php echo $quickcheckout['portlets'][4]['col']; ?>" />
                      <input type="text"  class="sort row-data" name="quickcheckout[portlets][4][row]" value="<?php echo $quickcheckout['portlets'][4]['row']; ?>" />
                    </div>
                  </li>
                  <li <?php if(isset($positions_needed)){ echo 'style="display:none"'; } ?>  class="portlet" col-data="<?php echo $quickcheckout['portlets'][5]['col']; ?>" row-data="<?php echo $quickcheckout['portlets'][5]['row']; ?>">
                    <div class="portlet-header"><?php echo $checkout_design_extra1; ?></div>
                    <div class="portlet-content">
                      <input type="text"  class="sort col-data" name="quickcheckout[portlets][5][col]" value="<?php echo $quickcheckout['portlets'][5]['col']; ?>" />
                      <input type="text"  class="sort row-data" name="quickcheckout[portlets][5][row]" value="<?php echo $quickcheckout['portlets'][5]['row']; ?>" />
                    </div>
                  </li>
                  <li <?php if(isset($positions_needed)){ echo 'style="display:none"'; } ?> class="portlet" col-data="<?php echo $quickcheckout['portlets'][6]['col']; ?>" row-data="<?php echo $quickcheckout['portlets'][6]['row']; ?>">
                    <div class="portlet-header"><?php echo $checkout_design_extra2; ?></div>
                    <div class="portlet-content">
                      <input type="text"  class="sort col-data" name="quickcheckout[portlets][6][col]" value="<?php echo $quickcheckout['portlets'][6]['col']; ?>" />
                      <input type="text"  class="sort row-data" name="quickcheckout[portlets][6][row]" value="<?php echo $quickcheckout['portlets'][6]['row']; ?>" />
                    </div>
                  </li>
                  <li <?php if(isset($positions_needed)){ echo 'style="display:none"'; } ?> class="portlet" col-data="<?php echo $quickcheckout['portlets'][7]['col']; ?>" row-data="<?php echo $quickcheckout['portlets'][7]['row']; ?>">
                    <div class="portlet-header"><?php echo $checkout_design_extra3; ?></div>
                    <div class="portlet-content">
                      <input type="text"  class="sort col-data" name="quickcheckout[portlets][7][col]" value="<?php echo $quickcheckout['portlets'][7]['col']; ?>" />
                      <input type="text"  class="sort row-data" name="quickcheckout[portlets][7][row]" value="<?php echo $quickcheckout['portlets'][7]['row']; ?>" />
                    </div>
                  </li>
                </ul>
                <ul class="column column-2" col-data="2">
                </ul>
                <ul class="column column-3" col-data="3">
                </ul><br style="clear:both"><br>

                <?php if(isset($positions_needed)){ echo $positions_needed; } ?>
                </td>
                <td><?php echo $checkout_style_css; ?><br><br>

                <?php if(isset($quickcheckout['checkout_style'])){ ?>
                <textarea name="quickcheckout[checkout_style]" id="checkout_style" style="width:80%; height:550px" ><?php echo $quickcheckout['checkout_style']; ?></textarea>
                <?php }else{ ?>
                <textarea name="quickcheckout[checkout_style]" id="checkout_style"  style="width:80%; height:550px" ></textarea>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $checkout_settings; ?> </td>
              <td colspan="2">
              <input type="checkbox" value="1" id="checkout_settings_checkbox" name="quickcheckout[checkout_settings_checkbox]" /><?php echo $checkout_settings_checkbox; ?>
                <textarea id="checkout_settings" name="quickcheckout[checkout_settings]" style="width:100%; height:80px; display:none" ></textarea>
                </td>
            </tr>
          </tbody>
        </table>
        <table class="form" style="background:#FFC">
        <tbody>
      		  <tr>
              <td>
              Support email:<br>
<small>(At Dreamvention we believe in trust. We know that trust does not come easy and it takes years of quality service to build up trustworthy relationship. That is why we give a great deal of attention to customer service. When you contact us, you can be sure to talk to a professional expert that will guide you through any issue and not overwhelm you with technical data.)</small>
              </td>
              <td>
              <a href="mailto:info@dreamvention.com?subject=QuickCheckout_Support_<?php echo $_SERVER["SERVER_NAME"];?>">info@dreamvention.com</a>
              </td>
              <td>
              Visit our Opencart Extensions Page for more great paid and free products.<br>
              <a href="http://www.opencart.com/index.php?route=extension/extension&filter_username=Dreamvention" target="_blank">More Extensions from Dreamvention</a><br><br>

              or visit our website at <a href="http://www.dreamvention.com/"  target="_blank">www.dreamvention.com</a>
              </td>
              </tr>   
              </tbody>          
      </table>
      </form>
    </div>
  </div>
</div>
<?php $main_width = 100/400;
$column_1 =  $quickcheckout['column_width']['column-1']/$main_width; 
$column_2 = $quickcheckout['column_width']['column-2']/$main_width;
$column_3 = $quickcheckout['column_width']['column-3']/$main_width; ?>
<style>
.intro-block{
	float:left;
	width:31%;
	padding:1%;
	color:#333;
	line-height:18px;}
.intro-block strong{
	font-size:18px;
	line-height:20px}
p{
	line-height:18px;}
small{
	color:#999}
.icon-drag{
	background: url(view/image/dreamvention/icon-drag.png);
	display:inline-block;
	float:right;
	width:20px;
	height:20px;
	cursor:n-resize
	}
td{
	width:33%;
	position:relative;
	height:100%;
	display: table-cell
}

.sort-item:hover{
	background:#FFC
}
.sort{
	display: none;
	float:right;
	width:10px;
}
tr{
width:100%;
}
.form th{
	padding:15px;
	
	background:#EFEFEF}
.columns{
	width:408px;}
#slider{
	width:400px;
	height:2px;}
.columns1 {
	margin-left:10px;}
.columns input{

	padding:0px;
	text-align:center;
	border:none
}
 .column { width: 130px; float: left;  margin:0px; padding:0px; height:510px; overflow:hidden;}
  .column-1, .column-data-1{width:<?php echo $column_1; ?>px;}
 .column-2, .column-data-2{
	 width:<?php echo $column_2; ?>px;} 
 .column-3, .column-data-3{width:<?php echo $column_3; ?>px;}

    .portlet { margin:10px 10px; border:1px dotted #999; background:#FFF; clear:both;
	display:block;
	height:60px;}
	.column-1 .portlet{
	  margin-left:0px;}
	.column-3 .portlet{
	  margin-right:0px;}
    .portlet-header { padding: 4px;  }
    .portlet-header .icon-drag { float: right; }
    .portlet-content { padding: 0.4em; }
    .ui-sortable-placeholder { border: 1px dotted black; visibility: visible !important; height: 50px !important; }
    .ui-sortable-placeholder * { visibility: hidden; }
.ui-widget-content{

	}
.ui-slider  .ui-slider-range{
	display:none}
.ui-slider .ui-slider-handle{
	height:510px;
	width:10px;
	margin-left: -7px;
	}
.gridster-holder{
	vertical-align:top;
}
.ui-slider .ui-slider-handle{
	border-radius:0px}

</style>
<script>

$(function() {
	$('#checkout_select_all').click(function(){
		$('input:checkbox').attr('checked', 'checked')
	})
	
	$('#checkout_unselect_all').click(function(){
		$('input:checkbox').removeAttr('checked')
	})
	
	$('.sortable > tr').tsort({attr:'sort-data'});
	
	$( ".sortable" ).sortable({
		revert: true,
		cursor: "move",
		items: "> .sort-item",
		opacity: 0.8,
		stop: function( event, ui ) {
			$(this).find("tr").each(function(i, el){
				$(this).find(".sort").val($(el).index())
			});
		}
	});
	var main_width = 100 / 400;
	
        $( "#slider" ).slider({
			range: true,	  
            min: 0,
            max: 400,
			step: 4,
            values: [ <?php echo $column_1; ?>,  <?php echo ($column_1 + $column_2); ?>],
			slide: function( event, ui ) {
				
				$('.column-data-1').val(Math.round(main_width*(ui.values[ 0 ])))
							  .attr('width-data', ui.values[ 0 ])
							  .attr('left-data', 0)
							  .css({'width' : parseInt( ui.values[ 0 ] ) + 'px'})
				$('.column-data-2').val(Math.round(main_width*(ui.values[ 1 ] - ui.values[ 0 ])))
							  .attr('width-data',ui.values[ 1 ] - ui.values[ 0 ])
							  .attr('left-data', parseInt(ui.values[ 0 ]+10))
							  .css({'width' : parseInt( ui.values[ 1 ] - ui.values[ 0 ]-10) + 'px'})
				$('.column-data-3').val(Math.round(main_width*(400 - ui.values[ 1 ])))
							  .attr('width-data',400 - ui.values[ 1 ])
							  .attr('left-data', parseInt(ui.values[ 1 ]+10))
							  .css({'width' : parseInt( 400 - ui.values[ 1 ] -10) + 'px'})
				$('.column-1').css({'width' :  parseInt( ui.values[ 0 ]) +'px' })
				$('.column-2').css({'width' : parseInt( ui.values[ 1 ] - ui.values[ 0 ])+'px'})
				$('.column-3').css({'width' :  parseInt(400 - ui.values[ 1 ]) +'px'})
				
              
			}
		});
	$( ".column" ).sortable({
            connectWith: ".column",
			stop: function( event, ui ) {
			$('.column').find("li").each(function(i, el){
				
				$(this).find(".row-data").val($(el).index())
				$(this).find(".col-data").val($(this).parent().attr('col-data'))

			});
			}
        });
 
    $( ".column" ).disableSelection();
	$('.column > li').tsort({attr:'row-data'});
	$('.column > li').each(function(){
				$(this).appendTo('.column-' + $(this).attr('col-data'));					
									})
	$(".sort-item  td:last-child").append('<i class="icon-drag"></i>')
	$(".portlet-header").prepend('<i class="icon-drag"></i>')
	$('#checkout_style').autosize();  
	$('#checkout_settings').val(decodeURIComponent(($('#form').serialize())))
	
	$('#checkout_settings_checkbox').live('click', function(){
		if($(this).attr('checked')) {
			$('#checkout_settings').fadeIn()
			}else{
			$('#checkout_settings').fadeOut()
			}										  
	  })
});

function image_upload(field, thumb) {
	$('#dialog').remove();
	
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=f2b06e3035867ee1624458759982f6ac&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	
	$('#dialog').dialog({
		title: 'Image Manager',
		close: function (event, ui) {
			if ($('#' + field).attr('value')) {
				$.ajax({
					url: 'index.php?route=common/filemanager/image&token=f2b06e3035867ee1624458759982f6ac&image=' + encodeURIComponent($('#' + field).val()),
					dataType: 'text',
					success: function(data) {
						$('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
					}
				});
			}
		},	
		bgiframe: false,
		width: 800,
		height: 400,
		resizable: false,
		modal: false
	});
};
//--></script>
<?php echo $footer; ?>