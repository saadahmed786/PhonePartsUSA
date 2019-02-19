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

  <?php $payment_type_row_no = 0; ?>

  <div class="box">

    <div class="heading">

      <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?>&nbsp;<?php echo 'V'.POS_VERSION; ?></h1>

      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>

	</div>

    <div class="content">
	
		<div id="htabs" class="htabs" style="padding: 5px;">
			<a href="#tab_settings_payment_type"><?php echo $tab_settings_payment_type; ?></a>
			<a href="#tab_settings_options"><?php echo $tab_settings_options; ?></a>
			<!-- add for receipt begin -->
			<a href="#tab_settings_receipt"><?php echo $tab_settings_receipt; ?></a>
			<!-- add for receipt end -->
			<a href="#tab_settings_order"><?php echo $tab_settings_order; ?></a>
			<!-- add for Default customer begin -->
			<a href="#tab_settings_customer"><?php echo $tab_settings_customer; ?></a>
			<!-- add for Default customer end -->
			<!-- add for Discount begin -->
			<a href="#tab_settings_discount"><?php echo $tab_settings_discount; ?></a>
			<!-- add for Discount end -->
			<!-- add for User as Affiliate begin -->
			<a href="#tab_settings_affiliate"><?php echo $tab_settings_affiliate; ?></a>
			<!-- add for User as Affiliate end -->
			<!-- add for Quotation begin -->
			<a href="#tab_settings_quote"><?php echo $tab_settings_quote; ?></a>
			<!-- add for Quotation end -->
			<!-- add for location based stock begin -->
			<a href="#tab_settings_location"><?php echo $tab_settings_location; ?></a>
			<!-- add for location based stock end -->
			<!-- add for table management begin -->
			<a href="#tab_settings_table_management"><?php echo $tab_settings_table_management; ?></a>
			<!-- add for table management end -->
			<!-- add for serial no begin -->
			<a href="#tab_settings_product_sn"><?php echo $tab_settings_product_sn; ?></a>
			<!-- add for serial no end -->
			<!-- add for commission begin -->
			<a href="#tab_settings_commission"><?php echo $tab_settings_commission; ?></a>
			<!-- add for commission end -->
		</div>

      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
		<!-- add for Empty order control begin -->
		<input type="hidden" name="initial_status_id" value="<?php echo $initial_status_id; ?>" />
		<!-- add for Empty order control end -->
		
		<div id="tab_settings_payment_type" style="width: 600px; padding: 5px;">
        <table id="payment_type_table" class="list">

          <thead>
			<tr>
				<td colspan="2" class="left" style="background-color: #E7EFEF;"><?php echo $text_payment_type_setting; ?></td>
			</tr>
            <tr>

              <td class="left" width='70%'><?php echo $text_order_payment_type; ?></td>

              <td class="right" width='30%'><?php echo $text_action; ?></td>

            </tr>

          </thead>

		  <tbody id="payment_type_list">

            <tr class='filter' id="payment_type_add">

              <td class="left" width="70%"><input type="text" name="payment_type" id="payment_type" style="width: 95%;" value="" onkeypress="return addPaymentOnEnter(event)" /></td>

              <td class="right" width="30%"><a id="button_add_payment_type" onclick="addPaymentType();" class="button"><?php echo $button_add_type; ?></a></td>

            </tr>

		<?php



		if (isset($payment_types)) {

			foreach ($payment_types as $payment_type=>$payment_name) {

		?>

		<tr id="<?php echo 'payment_type_'.$payment_type_row_no; ?>">

			<td class="left" width="70%"><?php echo $payment_name; ?></td>

			<td class="right" width="30%">
				<?php if (!$payment_type || $payment_type != 'cash' && $payment_type != 'credit_card') { ?>
				<a onclick="deletePaymentType('<?php echo 'payment_type_'.$payment_type_row_no; ?>');" class="button"><?php echo $button_remove; ?></a>
				<?php } ?>
				<input type="hidden" name="POS_payment_types[<?php echo $payment_type; ?>]" value="<?php echo $payment_name; ?>"/>
			</td>

		</tr>

		<?php $payment_type_row_no ++; }} ?>

          </tbody>

        </table>
		<!-- add for cash type begin -->
		<table class="list">
			<thead>
				<tr>
					<td colspan="4" class="left" style="background-color: #E7EFEF;"><?php echo $text_cash_type_setting; ?></td>
				</tr>
				<tr>
					<td class="left"><?php echo $column_cash_type; ?></td>
					<td class="left"><?php echo $column_cash_image; ?></td>
					<td class="right"><?php echo $column_cash_value; ?></td>
					<td class="center"><?php echo $column_cash_action; ?></td>
				</tr>
			</thead>
			<tbody id="cash_type_list">
				<tr class='filter' id="cash_type_tr">
					<td class="left">
						<select name="cash_type" style="width: 98%;">
							<option value="<?php echo $text_cash_type_note; ?>"><?php echo $text_cash_type_note; ?></option>
							<option value="<?php echo $text_cash_type_coin; ?>"><?php echo $text_cash_type_coin; ?></option>
						</select>
					</td>
					<td class="left">
						<div class="image">
							<img src="" alt="" id="cash_image" style="max-width: 240px; max-height: 120px; width: auto; height: auto;" /><br />
							<input type="hidden" name="cash_image_path" value="" id="cash_image_path" />
							<a onclick="image_upload('cash_image_path', 'cash_image', true);"><?php echo $text_print_browse; ?></a>
						</div>
					</td>
					<td align="right"><input type="text" name="cash_value" value="" style="text-align: right;" /></td>
					<td align="center"><a id="button_add_cash_type" onclick="addCashType();"><img src="view/image/pos/plus_off.png"/></a></td>
				</tr>
				<?php
					$cash_type_row = 0;
					if (!empty($cash_types)) {
						foreach ($cash_types as $cash_type) { ?>
				<tr id="cash_type-<?php echo $cash_type_row; ?>">
					<td class="left">
						<?php echo $cash_type['type']; ?>
						<input type="hidden" name="cash_types[<?php echo $cash_type_row; ?>][type]" value="<?php echo $cash_type['type']; ?>" />
					</td>
					<td class="left">
						<img src="<?php echo $cash_type['image']; ?>" style="max-width: 240px; max-height: 120px; width: auto; height: auto;" />
						<input type="hidden" name="cash_types[<?php echo $cash_type_row; ?>][image]" value="<?php echo $cash_type['image']; ?>" />
					</td>
					<td class="right">
						<?php echo $cash_type['value']; ?>
						<input type="hidden" name="cash_types[<?php echo $cash_type_row; ?>][value]" value="<?php echo $cash_type['value']; ?>" />
					</td>
					<td align="center"><a onclick="deleteCashType('cash_type-<?php echo $cash_type_row; ?>');"><img src="view/image/pos/delete_off.png" width="20" height="20"/></a></td>
				</tr>
				<?php $cash_type_row++; }} ?>
			</tbody>
		</table>
		<!-- add for cash type end -->
		</div>

		<div id="tab_settings_options" style="width: 600px; padding: 5px;">
        <table id="page_display" class="list">
          <thead>
			<tr>
				<td colspan="2" class="left" style="background-color: #E7EFEF;"><?php echo $text_display_setting; ?></td>
			</tr>
          </thead>
		  <tbody>
			<tr><td colspan="2" class="left">
				<input type="checkbox" name="display_once_login" value="<?php echo $display_once_login; ?>" <?php if($display_once_login=='1') { ?>checked="checked"<?php } ?> />&nbsp;
				<?php echo $text_display_once_login; ?>
			</td></tr>
			<tr>
				<td class="left" valign="center"><?php echo $column_exclude; ?></td>
				<td class="left" valign="center"><div class="scrollbox">
					<?php $class = 'odd'; ?>
					<?php foreach ($user_groups as $user_group) { ?>
					<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
					<div class="<?php echo $class; ?>">
					  <?php if (in_array($user_group['user_group_id'], $excluded_groups)) { ?>
					  <input type="checkbox" name="excluded_groups[]" value="<?php echo $user_group['user_group_id']; ?>" checked="checked" />
					  <?php echo $user_group['name']; ?>
					  <?php } else { ?>
					  <input type="checkbox" name="excluded_groups[]" value="<?php echo $user_group['user_group_id']; ?>" />
					  <?php echo $user_group['name']; ?>
					  <?php } ?>
					</div>
					<?php } ?>
				  </div>
				  <a onclick="$(this).parent().find(':checkbox').attr('checked', true);"><?php echo $text_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').attr('checked', false);"><?php echo $text_unselect_all; ?></a></td>
			</tr>
          </tbody>
        </table>

		<!-- add for inplace pricing begin -->
        <table id="enable_inplace_pricing" class="list">
          <thead>
			<tr>
				<td class="left" style="background-color: #E7EFEF;"><?php echo $text_inplace_pricing_setting; ?></td>
			</tr>
          </thead>
		  <tbody>
			<tr><td class="left">
				<input type="checkbox" name="enable_inplace_pricing" value="<?php echo $enable_inplace_pricing; ?>" <?php if($enable_inplace_pricing=='1') { ?>checked="checked"<?php } ?> />&nbsp;
				<?php echo $text_inplace_pricing_enable; ?>
			</td></tr>
          </tbody>
        </table>
		<!-- add for inplace pricing end -->
		<!-- add for Openbay begin -->
        <table id="openbay" class="list">
          <thead>
			<tr>
				<td class="left" style="background-color: #E7EFEF;"><?php echo $text_openbay_setting; ?></td>
			</tr>
          </thead>
		  <tbody>
			<tr><td class="left">
				<input type="checkbox" name="enable_openbay" value="<?php echo $enable_openbay; ?>" <?php if($enable_openbay=='1') { ?>checked="checked"<?php } ?> />&nbsp;
				<?php echo $text_openbay_enable; ?>
			</td></tr>
          </tbody>
        </table>
		<!-- add for Openbay end -->

		<!-- add for Rounding begin -->
		<table class="list">
          <thead>
			<tr>
				<td colspan="2" class="left" style="background-color: #E7EFEF;"><?php echo $text_rounding_setting; ?></td>
			</tr>
          </thead>
			<tr>
				<td class="center" width="1"><input type="checkbox" name="enable_rounding" value="<?php echo $enable_rounding; ?>" <?php if($enable_rounding=='1') { ?>checked="checked"<?php } ?> /></td>
				<td class="left"><?php echo $text_rounding_enable; ?></td>
			</tr>
			<tr>
				<td class="center" width="1"><input type="radio" name="config_rounding" value="5c" <?php if ($config_rounding == '5c') { ?> checked="checked" <?php } ?>/></td>
				<td class="left"><?php echo $text_rounding_5c; ?></td>
			</tr>
			</tr>
				<td class="center" width="1"><input type="radio" name="config_rounding" value="10c" <?php if ($config_rounding == '10c') { ?> checked="checked" <?php } ?>/></td>
				<td class="left"><?php echo $text_rounding_10c; ?></td>
			</tr>
			</tr>
				<td class="center" width="1"><input type="radio" name="config_rounding" value="50c" <?php if ($config_rounding == '50c') { ?> checked="checked" <?php } ?>/></td>
				<td class="left"><?php echo $text_rounding_50c; ?></td>
			</tr>
		</table>
		<!-- add for Rounding end -->
		<!-- add for till control begin -->
		<table class="list">
          <thead>
			<tr>
				<td colspan="3" class="left" style="background-color: #E7EFEF;"><?php echo $text_till_control_setting; ?></td>
			</tr>
          </thead>
			<tr>
				<td colspan="3" class="left">
					<input type="checkbox" name="enable_till_control" value="<?php echo $enable_till_control; ?>" <?php if($enable_till_control=='1') { ?>checked="checked"<?php } ?> />&nbsp;
					<?php echo $text_till_control_enable; ?>
				</td>
			</tr>
			<tr>
				<td class="left"><?php echo $entry_till_control_key; ?></td>
				<td class="left"><input type="text" style="width: 98%;" name="till_control_key" value="<?php echo empty($till_control_key) ? '' : $till_control_key; ?>"/></td>
				<td class="right"><a onclick="testTillControl();" class="button"><?php echo $button_test; ?></a></td>
			</tr>
			<tr>
				<td class="left" colspan="3">
					<input type="checkbox" name="enable_till_full_payment" value="<?php echo $enable_till_full_payment; ?>" <?php if($enable_till_full_payment=='1') { ?>checked="checked"<?php } ?> />&nbsp;
					<?php echo $text_till_full_payment_enable; ?>
				</td>
			</tr>
		</table>
		<!-- add for till control end -->
		</div>

		<div id="tab_settings_receipt" style="width: 600px; padding: 5px;">
		<!-- add for Print begin -->
        <table id="pos_print" class="list">
          <thead>
			<tr>
				<td class="left" colspan="2" style="background-color: #E7EFEF;"><?php echo $text_print_setting; ?></td>
			</tr>
          </thead>
		  <tbody>
			<tr>
				<td class="left"><?php echo $entry_print_log; ?></td>
				<td class="left">
					<div class="image">
						<img src="<?php echo $p_logo; ?>" alt="" id="pos_logo" /><br />
						<input type="hidden" name="p_logo" value="<?php echo $p_logo; ?>" id="logo" />
						<a onclick="image_upload('logo', 'pos_logo');"><?php echo $text_print_browse; ?></a>
					</div>
				</td>
			</tr>
			<tr>
				<td class="left"><?php echo $entry_print_width; ?></td>
				<td class="left"><input type="text" name="p_width" value="<?php echo $p_width; ?>" style="width: 95%;"/></td>
			</tr>
			<tr>
				<td class="left" colspan="2">
					<input type="checkbox" name="p_complete" value="<?php echo $p_complete; ?>" <?php if($p_complete=='1') { ?>checked="checked"<?php } ?> />&nbsp;
					<?php echo $text_p_complete; ?>
				</td>
			</tr>
			<tr>
				<td class="left" colspan="2">
					<input type="checkbox" name="p_payment" value="<?php echo $p_payment; ?>" <?php if($p_payment=='1') { ?>checked="checked"<?php } ?> />&nbsp;
					<?php echo $text_p_payment; ?>
				</td>
			</tr>
			<tr>
				<td class="left"><?php echo $entry_term_n_cond; ?></td>
				<td class="left"><textarea name="p_term_n_cond" style="width: 95%;" row="3"><?php echo $p_term_n_cond; ?></textarea></td>
			</tr>
          </tbody>
        </table>
		<!-- add for Print end -->
		</div>
		
		<div id="tab_settings_order" style="width: 600px; padding: 5px;">
		<!-- add for Hiding Delete begin -->
        <table id="hide_delete" class="list">
          <thead>
			<tr>
				<td colspan="2" class="left" style="background-color: #E7EFEF;"><?php echo $text_hide_delete_setting; ?></td>
			</tr>
          </thead>
		  <tbody>
			<tr><td colspan="2" class="left">
				<input type="checkbox" name="enable_hide_delete" value="<?php echo $enable_hide_delete; ?>" <?php if($enable_hide_delete=='1') { ?>checked="checked"<?php } ?> />&nbsp;
				<?php echo $text_hide_delete_enable; ?>
			</td></tr>
			<tr>
				<td class="left" valign="center"><?php echo $column_exclude; ?></td>
				<td class="left" valign="center"><div class="scrollbox">
					<?php $class = 'odd'; ?>
					<?php foreach ($user_groups as $user_group) { ?>
					<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
					<div class="<?php echo $class; ?>">
					  <?php if (in_array($user_group['user_group_id'], $delete_excluded_groups)) { ?>
					  <input type="checkbox" name="delete_excluded_groups[]" value="<?php echo $user_group['user_group_id']; ?>" checked="checked" />
					  <?php echo $user_group['name']; ?>
					  <?php } else { ?>
					  <input type="checkbox" name="delete_excluded_groups[]" value="<?php echo $user_group['user_group_id']; ?>" />
					  <?php echo $user_group['name']; ?>
					  <?php } ?>
					</div>
					<?php } ?>
				  </div>
				  <a onclick="$(this).parent().find(':checkbox').attr('checked', true);"><?php echo $text_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').attr('checked', false);"><?php echo $text_unselect_all; ?></a></td>
			</tr>
          </tbody>
        </table>
		<!-- add for Hiding Delete begin -->
		<!-- add for Hiding Order Status begin -->
        <table id="hide_order_status" class="list">
          <thead>
			<tr>
				<td colspan="2" class="left" style="background-color: #E7EFEF;"><?php echo $text_hide_order_status_setting; ?></td>
			</tr>
          </thead>
		  <tbody>
			<tr>
				<td class="left" valign="center"><?php echo $text_hide_order_status_message; ?></td>
				<td class="left" valign="center"><div class="scrollbox">
					<?php $class = 'odd'; ?>
					<?php foreach ($order_statuses as $order_status) { ?>
					<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
					<div class="<?php echo $class; ?>">
					  <?php if (in_array($order_status['order_status_id'], $order_hiding_status)) { ?>
					  <input type="checkbox" name="order_hiding_status[]" value="<?php echo $order_status['order_status_id']; ?>" checked="checked" />
					  <?php echo $order_status['name']; ?>
					  <?php } else { ?>
					  <input type="checkbox" name="order_hiding_status[]" value="<?php echo $order_status['order_status_id']; ?>" />
					  <?php echo $order_status['name']; ?>
					  <?php } ?>
					</div>
					<?php } ?>
				  </div>
				  <a onclick="$(this).parent().find(':checkbox').attr('checked', true);"><?php echo $text_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').attr('checked', false);"><?php echo $text_unselect_all; ?></a></td>
			</tr>
          </tbody>
        </table>
		<!-- add for Hiding Order Status end -->
		<!-- add for Empty order control begin -->
        <table id="empty_order_control_setting" class="list">
          <thead>
			<tr>
				<td class="left" style="background-color: #E7EFEF;"><?php echo $text_empty_order_control_setting; ?></td>
			</tr>
			<tr>
				<td class="left"><?php echo $text_empty_order_control_delete_setting; ?></td>
			</tr>
          </thead>
		  <tbody>
			<tr><td class="left">
				<input type="checkbox" name="delete_order_with_no_products" value="<?php echo $delete_order_with_no_products; ?>" <?php if($delete_order_with_no_products=='1') { ?>checked="checked"<?php } ?> />&nbsp;
				<?php echo $text_delete_order_with_no_products; ?>
			</td></tr>
			<tr><td class="left">
				<input type="checkbox" name="delete_order_with_inital_status" value="<?php echo $delete_order_with_inital_status; ?>" <?php if($delete_order_with_inital_status=='1') { ?>checked="checked"<?php } ?> />&nbsp;
				<?php echo $text_delete_order_with_inital_status; ?>
			</td></tr>
			<tr><td class="left">
				<input type="checkbox" name="delete_order_with_deleted_status" value="<?php echo $delete_order_with_deleted_status; ?>" <?php if($delete_order_with_deleted_status=='1') { ?>checked="checked"<?php } ?> />&nbsp;
				<?php echo $text_delete_order_with_deleted_status; ?>
			</td></tr>
          </tbody>
          <thead>
			<tr>
				<td class="left"><?php echo $text_empty_order_control_action; ?></td>
			</tr>
          </thead>
		  <tbody>
			<tr><td class="left">
				<input type="checkbox" name="action_delete_order_with_no_products" />&nbsp;
				<?php echo $text_delete_order_with_no_products; ?>
			</td></tr>
			<tr><td class="left">
				<input type="checkbox" name="action_delete_order_with_inital_status" />&nbsp;
				<?php echo $text_delete_order_with_inital_status; ?>
			</td></tr>
			<tr><td class="left">
				<input type="checkbox" name="action_delete_order_with_deleted_status" />&nbsp;
				<?php echo $text_delete_order_with_deleted_status; ?>
			</td></tr>
			<tr><td class="left">
				<a id="delete_orders" onclick="deleteOrders();" class="button"><?php echo $button_delete; ?></a>
			</td></tr>
          </tbody>
        </table>
		<!-- add for Empty order control begin -->
		<!-- add for UPC/SKU/MPN begin -->
		<table id="c_default" class="list">
          <thead>
			<tr>
				<td colspan="3" class="left" style="background-color: #E7EFEF;"><?php echo $text_scan_type_setting; ?></td>
			</tr>
          </thead>
			<tr>
				<td class="center" style="text-align: center;" size="1"><input type="radio" name="config_scan_type" value="upc" <?php if ($config_scan_type == 'upc') { ?> checked="checked" <?php } ?>/><?php echo $text_scan_type_upc; ?></td>
				<td class="center" style="text-align: center;" size="1"><input type="radio" name="config_scan_type" value="sku" <?php if ($config_scan_type == 'sku') { ?> checked="checked" <?php } ?>/><?php echo $text_scan_type_sku; ?></td>
				<td class="center" style="text-align: center;" size="1"><input type="radio" name="config_scan_type" value="mpn" <?php if ($config_scan_type == 'mpn') { ?> checked="checked" <?php } ?>/><?php echo $text_scan_type_mpn; ?></td>
			</tr>
		</table>
		<!-- add for UPC/SKU/MPN end -->
		<!-- add for Complete Status begin -->
		<table class="list">
          <thead>
			<tr>
				<td colspan="2" class="left" style="background-color: #E7EFEF;"><?php echo $text_complete_status_setting; ?></td>
			</tr>
          </thead>
			<tr>
				<td class="left"><?php echo $entry_complete_status; ?></td>
				<td class="left"><input type="text" name="complete_status" value="<?php echo empty($complete_status) ? '' : $complete_status; ?>"/></td>
			</tr>
		</table>
		<!-- add for Complete Status end -->
		<!-- add for Status Change Notification begin -->
        <table id="notification" class="list">
          <thead>
			<tr>
				<td class="left" style="background-color: #E7EFEF;"><?php echo $text_notification_setting; ?></td>
			</tr>
          </thead>
		  <tbody>
			<tr><td class="left">
				<input type="checkbox" name="enable_notification" value="<?php echo $enable_notification; ?>" <?php if($enable_notification=='1') { ?>checked="checked"<?php } ?> />&nbsp;
				<?php echo $text_notification_enable; ?>
			</td></tr>
          </tbody>
        </table>
		<!-- add for Status Change Notification begin -->
		</div>
		
		<div id="tab_settings_customer" style="width: 600px; padding: 5px;">
		<!-- add for Default Customer begin -->
		<table id="c_default" class="list">
          <thead>
			<tr>
				<td colspan="6" class="left" style="background-color: #E7EFEF;"><?php echo $text_customer_setting; ?></td>
			</tr>
          </thead>
			<tr>
				<td class="center" style="text-align: center;" size="1"><input type="radio" name="c_type" value="1" <?php if ($c_type == 1) { ?> checked="checked" <?php } ?>/></td>
				<td class="left"><?php echo $text_customer_system; ?></td>
				<td class="center" style="text-align: center;" size="1"><input type="radio" name="c_type" value="2" <?php if ($c_type == 2) { ?> checked="checked" <?php } ?>/></td>
				<td class="left"><?php echo $text_customer_custom; ?></td>
				<td class="center" style="text-align: center;" size="1"><input type="radio" name="c_type" value="3" <?php if ($c_type == 3) { ?> checked="checked" <?php } ?>/></td>
				<td class="left"><?php echo $text_customer_existing; ?></td>
			</tr>
			<tr>
				<td class="left" colspan="6" style="color: #FF802B; font-size: 12px; font-weight: bold; "><?php echo $text_customer_info; ?>
				<input type="hidden" name="c_id" value="<?php echo $c_id; ?>" /></td>
			</tr>
			<tr>
				<td class="left" colspan="3"><?php echo $text_customer_group; ?></td>
				<td class="left" colspan="3"><select name="c_group_id">
					<?php foreach ($c_groups as $customer_group) { ?>
					<?php if ($customer_group['customer_group_id'] == $c_group_id) { ?>
					<option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
					<?php } else { ?>
					<option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
					<?php } ?>
					<?php } ?>
				</select></td>
			</tr>
			<tr id="c_autocomplete" <?php if ($c_id ==0) { ?>style="display:none;"<?php } ?>>
			  <td class="left" colspan="3"><?php echo $text_customer; ?></td>
			  <td class="left" colspan="3"><input type="text" name="c_name" value="<?php echo $c_name; ?>" />&nbsp;<?php echo '('.$text_autocomplete.')'; ?></td>
			</tr>
			<tr>
			  <td class="left" colspan="3"><span class="required">*</span> <?php echo $entry_firstname; ?></td>
			  <td class="left" colspan="3"><input type="text" name="c_firstname" value="<?php echo $c_firstname; ?>" /></td>
			</tr>
			<tr>
			  <td class="left" colspan="3"><span class="required">*</span> <?php echo $entry_lastname; ?></td>
			  <td class="left" colspan="3"><input type="text" name="c_lastname" value="<?php echo $c_lastname; ?>" /></td>
			</tr>
			<tr>
			  <td class="left" colspan="3"><span class="required">*</span> <?php echo $entry_email; ?></td>
			  <td class="left" colspan="3"><input type="text" name="c_email" value="<?php echo $c_email; ?>" /></td>
			</tr>
			<tr>
			  <td class="left" colspan="3"><span class="required">*</span> <?php echo $entry_telephone; ?></td>
			  <td class="left" colspan="3"><input type="text" name="c_telephone" value="<?php echo $c_telephone; ?>" /></td>
			</tr>
			<tr>
			  <td class="left" colspan="3"><?php echo $entry_fax; ?></td>
			  <td class="left" colspan="3"><input type="text" name="c_fax" value="<?php echo $c_fax; ?>" /></td>
			</tr>
			<tr>
				<td class="left" colspan="6" style="color: #FF802B; font-size: 12px; font-weight: bold; "><?php echo $text_address_info; ?></td>
			</tr>
			<tr>
				<td class="left" colspan="3"><span class="required">*</span> <?php echo $entry_firstname; ?></td>
				<td class="left" colspan="3"><input type="text" name="a_firstname" value="<?php echo $a_firstname; ?>" /></td>
			</tr>
			<tr>
				<td class="left" colspan="3"><span class="required">*</span> <?php echo $entry_lastname; ?></td>
				<td class="left" colspan="3"><input type="text" name="a_lastname" value="<?php echo $a_lastname; ?>" /></td>
			</tr>
			<tr>
				<td class="left" colspan="3"><span class="required">*</span> <?php echo $entry_address_1; ?></td>
				<td class="left" colspan="3"><input type="text" name="a_address_1" value="<?php echo $a_address_1; ?>" /></td>
			</tr>
			<tr>
				<td class="left" colspan="3"><?php echo $entry_address_2; ?></td>
				<td class="left" colspan="3"><input type="text" name="a_address_2" value="<?php echo $a_address_2; ?>" /></td>
			</tr>
			<tr>
				<td class="left" colspan="3"><span class="required">*</span> <?php echo $entry_city; ?></td>
				<td class="left" colspan="3"><input type="text" name="a_city" value="<?php echo $a_city; ?>" /></td>
			</tr>
			<tr>
				<td class="left" colspan="3"><span id="postcode-required" class="required">*</span> <?php echo $entry_postcode; ?></td>
				<td class="left" colspan="3"><input type="text" name="a_postcode" value="<?php echo $a_postcode; ?>" /></td>
			</tr>
			<tr>
				<td class="left" colspan="3"><span class="required">*</span> <?php echo $entry_country; ?></td>
				<td class="left" colspan="3"><select name="a_country_id" onchange="country('<?php echo $a_zone_id; ?>');">
					<option value=""><?php echo $text_select; ?></option>
					<?php foreach ($c_countries as $customer_country) { ?>
						<?php if ($customer_country['country_id'] == $a_country_id) { ?>
						<option value="<?php echo $customer_country['country_id']; ?>" selected="selected"><?php echo $customer_country['name']; ?></option>
						<?php } else { ?>
						<option value="<?php echo $customer_country['country_id']; ?>"><?php echo $customer_country['name']; ?></option>
						<?php } ?>
					<?php } ?>
				</select></td>
			</tr>
			<tr>
				<td class="left" colspan="3"><span class="required">*</span> <?php echo $entry_zone; ?></td>
				<td class="left" colspan="3"><select name="a_zone_id">
				</select></td>
			</tr>
		</table>
		<!-- add for Default Customer end -->
		</div>
		
		<div id="tab_settings_discount" style="width: 600px; padding: 5px;">
		<!-- add for Maximum Discount begin -->
        <table id="max_discount_setting" class="list">
          <thead>
			<tr>
				<td class="left" colspan="3" style="background-color: #E7EFEF;"><?php echo $text_max_discount_setting; ?></td>
			</tr>
			<tr>
				<td class="left"><?php echo $column_group; ?></td>
				<td class="left" colspan="2"><?php echo $column_discount_limit; ?></td>
			</tr>
          </thead>
		  <tbody>
			<?php foreach ($user_groups as $user_group) { ?>
			<tr>
				<td class="left" rowspan="2"><b><?php echo $user_group['name']; ?></b></td>
				<td class="left"><?php echo $entry_max_discount_fixed; ?></td>
				<td class="left">
					<input type="text" name="<?php echo $user_group['user_group_id']; ?>_max_discount_fixed" value="<?php echo $user_group['max_discount_fixed']; ?>" />
				</td>
			</tr>
			<tr>
				<td class="left"><?php echo $entry_max_discount_percentage; ?></td>
				<td class="left">
					<input type="text" name="<?php echo $user_group['user_group_id']; ?>_max_discount_percentage" value="<?php echo $user_group['max_discount_percentage']; ?>" />
				</td>
			</tr>
			<?php } ?>
          </tbody>
        </table>
		<!-- add for Maximum Discount begin -->
		</div>
		<div id="tab_settings_location" style="width: 600px; padding: 5px;">
		<!-- add for location based stock begin -->
		<table class="list">
			<thead>
				<tr>
					<td colspan="4" class="left" style="background-color: #E7EFEF;"><?php echo $text_location_setting; ?></td>
				</tr>
				<tr>
					<td class="left" colspan="4">
						<input type="checkbox" name="enable_location_stock" value="<?php echo $enable_location_stock; ?>" <?php if($enable_location_stock=='1') { ?>checked="checked"<?php } ?> />&nbsp;
						<?php echo $text_location_stock_enable; ?>
					</td>
				</tr>
				<tr>
					<td class="left"><?php echo $column_location_code; ?></td>
					<td class="left"><?php echo $column_location_name; ?></td>
					<td class="left"><?php echo $column_location_desc; ?></td>
					<td class="right"><?php echo $column_location_action; ?></td>
				</tr>
			</thead>
			<tbody id="location_list">
				<tr class='filter' id="location_tr">
					<td class="left" width="15%" style="vertical-align: top;">
						<input name="location_code" style="width: 98%;" value="" />
					</td>
					<td class="left" width="35%" style="vertical-align: top;">
						<input name="location_name" style="width: 98%;" value="" />
					</td>
					<td class="left" width="35%" style="vertical-align: top;">
						<textarea name="location_desc" rows="1" style="width: 98%;"></textarea>
					</td>
					<td class="center" width="15%" style="vertical-align: top;"><a id="button_add_location" onclick="addLocation();"><img src="view/image/pos/plus_off.png"/></a></td>
				</tr>
				<?php 
					if (!empty($locations)) {
						foreach ($locations as $location) { ?>
				<tr>
					<td class="left" width="15%">
						<span><?php echo $location['code']; ?></span>
						<input type="hidden" name="location_id_<?php echo $location['location_id']; ?>" value="<?php echo $location['location_id']; ?>" />
					</td>
					<td class="left" width="35%"><?php echo $location['name']; ?></td>
					<td class="left" width="35%"><?php echo $location['description']; ?></td>
					<td class="center" width="15%">
						<a onclick="deleteLocation(this);"><img src="view/image/pos/delete_off.png" width="20" height="20"/></a>
					</td>
				</tr>
				<?php }} ?>
			</tbody>
		</table>
		<!-- add for location based stock end -->
		</div>
		<div id="tab_settings_table_management" style="width: 1024px; padding: 5px;">
		<!-- add for table management begin -->
		<table class="list">
			<thead>
				<tr>
					<td colspan="3" class="left" style="background-color: #E7EFEF;"><?php echo $text_table_management_setting; ?></td>
				</tr>
				<tr>
					<td class="left" colspan="3">
						<input type="checkbox" name="enable_table_management" value="<?php echo $enable_table_management; ?>" <?php if($enable_table_management=='1') { ?>checked="checked"<?php } ?> />&nbsp;
						<?php echo $text_table_management_enable; ?>
					</td>
				</tr>
			</thead>
			<tbody id="table_list">
				<!--
				<tr>
					<td class="left" style="width: 30%"><?php echo $entry_table_layout; ?></td>
					<td class="left">
						<div class="image">
							<a onclick="image_upload('table_layout', 'img_table_layout', false, onUpload);"><?php echo $text_table_layout; ?></a>
						</div>
					</td>
				</tr>
				-->
				<tr>
					<td class="left"><?php echo $entry_table_number; ?></td>
					<td class="left"><input type="text" name="table_number" style="width: 98%;" value="<?php echo $table_number; ?>" /></td>
					<td class="left">
						<a id="button_set_number" onclick="setTableNumber();" class="button"><?php echo $button_table_set_number; ?></a>
					</td>
				</tr>
				<tr class='filter'>
					<td class="left"><?php echo $column_table_id; ?></td>
					<td class="left"><?php echo $column_table_desc; ?></td>
					<td class="left"><?php echo $column_table_action; ?></td>
				</tr>
				<?php
					if (!empty($tables)) {
						foreach ($tables as $table) {
				?>
				<tr>
					<td class="left"><input type="text" name="name_<?php echo $table['table_id']; ?>" value="<?php echo $table['name']; ?>" style="width:98%;"/></td>
					<td class="left"><input type="text" name="desc_<?php echo $table['table_id']; ?>" value="<?php echo $table['description']; ?>" style="width:98%;"/></td>
					<td class="left">
						<input type="hidden" value="<?php echo $table['table_id']?>" />
						<a onclick="modifyTable(this);" class="button"><?php echo $button_table_modify; ?></a>&nbsp;&nbsp;
						<a onclick="removeTable(this);" class="button"><?php echo $button_table_remove; ?></a>
					</td>
				</tr>
				<?php
						}
					}
				?>
			</tbody>
		</table>
		<table class="list" style="display:none;">
			<tbody>
				<tr>
					<td class="left" style="width: 800px;">
						<div>
							<img src="<?php echo $img_table_layout; ?>" alt="" id="img_table_layout" />
							<input type="hidden" name="img_table_layout" value="<?php echo $img_table_layout; ?>" id="table_layout" />
							<input type="hidden" name="x1" value="" />
							<input type="hidden" name="y1" value="" />
							<input type="hidden" name="x2" value="" />
							<input type="hidden" name="y2" value="" />
						</div>
					</td>
					<td class="left">
						<table>
							<tr>
								<td><?php echo $entry_table_name; ?><br/></td>
								<td class="left"><input type="text" name="table_name" style="width: 98%;" value="" /></td>
							</tr>
							<tr>
								<td class="left"><?php echo $entry_table_desc; ?></td>
								<td class="left"><textarea name="table_desc" rows="1" style="width: 98%;"></textarea></td>
							</tr>
							<tr>
								<td class="left"><a id="set_table" onclick="setTable();" class="button"><?php echo $button_set_table; ?></a></td>
								<td class="left"><a id="delete_table" onclick="deleteTable();" class="button"><?php echo $button_delete_table; ?></a></td>
							</tr>
						</table>
					</td>
			</tbody>
		</table>
		<!-- add for table management end -->
		</div>
		<div id="tab_settings_commission" style="width: 600px; padding: 5px;">
		<!-- add for commission begin -->
		<table class="list">
			<thead>
				<tr>
					<td colspan="4" class="left" style="background-color: #E7EFEF;"><?php echo $text_commission_setting; ?></td>
				</tr>
				<tr>
					<td class="left" colspan="4">
						<input type="checkbox" name="enable_commission" value="<?php echo $enable_commission; ?>" <?php if($enable_commission=='1') { ?>checked="checked"<?php } ?> />&nbsp;
						<?php echo $text_commission_enable; ?>
					</td>
				</tr>
				<tr class='filter'>
					<td class="left"><?php echo $entry_product; ?></td>
					<td class="left" colspan="3">
						<input type="text" name="product_name_commission" value="" style="width:98%" />
						<input type="hidden" name="product_id_commission" value="" />
					</td>
				</tr>
			</thead>
			<tbody id="set_commission" style="display:none;">
				<tr><td colspan="4" class="left"><?php echo $text_set_commission; ?></td></tr>
				<tr>
					<td class="left"><input type="radio" name="commission_type" value="1" checked="checked" /><?php echo $entry_commission_fixed; ?></td>
					<td class="left" colspan="3"><input type="text" name="commission_fixed" value="" style="width:98%" /></td>
				</tr>
				<tr>
					<td class="left"><input type="radio" name="commission_type" value="2" /><?php echo $entry_commission_percentage; ?></td>
					<td class="left"><input type="text" name="commission_percentage" value="" style="width:98%" /></td>
					<td class="left"><?php echo $text_commission_percentage_base; ?></td>
					<td class="left"><input type="text" name="commission_percentage_base" value="" style="width:98%" /></td>
				</tr>
				<tr>
					<td class="right" colspan="4"><a onclick="saveCommission();" class="button"><?php echo $button_commission_save; ?></a></td>
				</tr>
			</tbody>
		</table>
		<table class="list">
			<thead>
				<tr>
					<td colspan="3" class="left" style="background-color: #E7EFEF;"><?php echo $text_list_commission_setting; ?></td>
				</tr>
				<tr>
					<td class="left"><?php echo $column_commission_product_name; ?></td>
					<td class="left"><?php echo $column_commission_commission; ?></td>
					<td class="right"><?php echo $column_commission_action; ?></td>
				</tr>
			</thead>
			<tbody id="product_commission_list">
				<tr class="filter">
					<td>
						<input type="text" name="filter_commission_product_name" style="width: 98%;" value="" />
						<input type="hidden" name="filter_commission_product_id" value="" />
					</td>
					<td></td>
					<td align="right">
						<a onclick="filterCommission();" class="button"><?php echo $button_commission_search; ?></a>&nbsp;
					</td>
				</tr>
				<?php if (!empty($product_commissions)) { ?>
				<?php foreach ($product_commissions as $product_commission) {?>
				<tr>
					<td class="left"><?php echo $product_commission['name']; ?></td>
					<td class="left"><?php echo $product_commission['commission']; ?></td>
					<td align="right">
						<a onclick="deleteCommission();" class="button"><?php echo $button_delete; ?></a>
					</td>
				</tr>
				<?php }} ?>
			</tbody>
		</table>
		</div>
		<!-- add for commission end -->
      </form>
		<div id="tab_settings_affiliate" style="width: 600px; padding: 5px;">
		<!-- add for User as Affiliate begin -->
		<table class="list">
			<thead>
				<tr>
					<td colspan="3" class="left" style="background-color: #E7EFEF;"><?php echo $text_user_affi_setting; ?></td>
				</tr>
				<tr>
					<td class="left"><?php echo $column_ua_user; ?></td>
					<td class="left"><?php echo $column_ua_affiliate; ?></td>
					<td class="right"><?php echo $column_ua_action; ?></td>
				</tr>
			</thead>
			<tbody id="user_affi_list">
				<tr class='filter' id="user_affi_tr">
					<td class="left" width="45%">
						<select name="user_name" style="width: 98%;">
							<?php foreach($ua_users as $user) { ?>
							<option value="<?php echo $user['user_id']; ?>"><?php echo $user['username']; ?></option>
							<?php } ?>
						</select>
					</td>
					<td class="left" width="45%">
						<select name="affiliate_name" style="width: 98%;">
							<?php foreach($ua_affiliates as $affiliate) { ?>
							<option value="<?php echo $affiliate['affiliate_id']; ?>"><?php echo $affiliate['firstname'].' '.$affiliate['lastname']; ?></option>
							<?php } ?>
						</select>
					</td>
					<td align="center" width="10%"><a id="button_add_ua" onclick="addUA();"><img src="view/image/pos/plus_off.png"/></a></td>
				</tr>
				<?php 
					if (!empty($user_affis)) {
						foreach ($user_affis as $user_affi) { ?>
				<tr id="user_affi-<?php echo $user_affi['user_id']; ?>-<?php echo $user_affi['affiliate_id']; ?>">
					<td class="left" width="45%"><?php echo $user_affi['username']; ?></td>
					<td class="left" width="45%"><?php echo $user_affi['firstname'].' '.$user_affi['lastname']; ?></td>
					<td align="center" width="10%"><a onclick="deleteUA('user_affi-<?php echo $user_affi['user_id']; ?>-<?php echo $user_affi['affiliate_id']; ?>');"><img src="view/image/pos/delete_off.png" width="20" height="20"/></a></td>
				</tr>
				<?php }} ?>
			</tbody>
		</table>
		<!-- add for User as Affiliate begin -->
		</div>
		
		<div id="tab_settings_quote" style="width: 600px; padding: 5px;">
		<!-- add for Quotation begin -->
		<table class="list">
			<thead>
				<tr>
					<td colspan="2" class="left" style="background-color: #E7EFEF;"><?php echo $text_quote_status_setting; ?></td>
				</tr>
				<tr>
					<td class="left"><?php echo $column_quote_status_name; ?></td>
					<td class="right"><?php echo $column_quote_status_action; ?></td>
				</tr>
			</thead>
			<tbody id="quote_status_list">
				<tr class='filter' id="quote_status_tr">
					<td class="left" width="70%">
						<input name="quote_status_name" style="width: 98%;" value="" onkeypress="return addStatusOnEnter(event)"/>
					</td>
					<td class="right" width="30%"><a id="button_add_quote_status" onclick="addQuoteStatus();" class="button"><?php echo $button_add_type; ?></a></td>
				</tr>
				<?php 
					if (!empty($quote_statuses)) {
						foreach ($quote_statuses as $quote_status) { ?>
				<tr>
					<td class="left" width="70%">
						<span><?php echo $quote_status['name']; ?></span>
						<input type="hidden" name="quote_status_id_<?php echo $quote_status['quote_status_id']; ?>" value="<?php echo $quote_status['quote_status_id']; ?>" />
					</td>
					<td class="right" width="30%">
						<a onclick="renameQuoteStatus(this);" class="button"><?php echo $button_rename; ?></a>
						<a onclick="deleteQuoteStatus(this);" class="button"><?php echo $button_delete; ?></a>
					</td>
				</tr>
				<?php }} ?>
			</tbody>
		</table>
		<!-- add for Quotation end -->
		</div>
		<div id="tab_settings_product_sn" style="width: 600px; padding: 5px;">
		<!-- add for serial no begin -->
		<table class="list">
			<thead>
				<tr>
					<td colspan="2" class="left" style="background-color: #E7EFEF;"><?php echo $text_add_serial_no_setting; ?></td>
				</tr>
				<tr class='filter'>
					<td class="left"><?php echo $entry_product; ?></td>
					<td class="left">
						<input type="text" name="product_name_new" value="" style="width:98%" />
						<input type="hidden" name="product_id_new" value="" />
					</td>
				</tr>
			</thead>
			<tbody id="new_serial_no_list" style="display:none;">
				<tr>
					<td class="left"><?php echo $entry_sn; ?> #1</td>
					<td class="left"><input type="text" name="product_sn[1]" value="" style="width:98%" /></td>
				</tr>
				<tr id="tr_sn_save">
					<td class="right" colspan="2"><a onclick="saveSN(this);" class="button"><?php echo $button_sn_save; ?></a></td>
				</tr>
			</tbody>
		</table>
		<table class="list">
			<thead>
				<tr>
					<td colspan="4" class="left" style="background-color: #E7EFEF;"><?php echo $text_list_serial_no_setting; ?></td>
				</tr>
				<tr>
					<td class="left"><?php echo $column_sn_product_name; ?></td>
					<td class="left"><?php echo $column_sn_product_sn; ?></td>
					<td class="left"><?php echo $column_sn_product_status; ?></td>
					<td class="right"><?php echo $column_action; ?></td>
				</tr>
			</thead>
			<tbody id="product_serial_no_list">
				<tr class="filter">
					<td>
						<input type="text" name="filter_sn_name" value="" />
						<input type="hidden" name="filter_product_id" value="" />
					</td>
					<td><input type="text" name="filter_sn_sn" value="" /></td>
					<td>
						<select name="filter_sn_status">
							<option value="0"></option>
							<option value="1"><?php echo $text_sn_in_store; ?></option>
							<option value="2"><?php echo $text_sn_sold; ?></option>
						</select>
					</td>
					<td align="right">
						<a onclick="filterSN();" class="button"><?php echo $button_search; ?></a>&nbsp;
					</td>
				</tr>
				<?php if (!empty($product_sns)) { ?>
				<?php foreach ($product_sns as $product_sn) {?>
				<tr>
					<td class="left"><?php echo $product_sn['name']; ?></td>
					<td class="left"><?php echo $product_sn['sn']; ?></td>
					<td class="left"><?php echo $product_sn['status']; ?></td>
					<td align="right">
						<a onclick="deleteSN();" class="button"><?php echo $button_delete; ?></a>
					</td>
				</tr>
				<?php }} ?>
			</tbody>
		</table>
		<!-- add for serial no end -->
		</div>
	</div>

   </div>
	<!-- add for jzebra printer begin -->
	<?php if (isset($enable_till_control) && $enable_till_control) { ?>
	<div id="jzebra_div" style="visibility: hidden; height: 0px;">
		<applet name="jzebra" code="qz.PrintApplet.class" archive="view/template/pos/print/qz-print.jar" width="50px" height="50px">
			<param name="jnlp_href" value="view/template/pos/print/qz-print_jnlp.jnlp">
			<param name="printer" value="opencartPOS">
			<param name="cache_option" value="plugin">
			<param name="disable_logging" value="false">
			<param name="initial_focus" value="false">
		</applet>
	</div>
	<?php } ?>
	<!-- add for jzebra printer end -->

</div>

<script type="text/javascript">
// add for table management begin
var jcrop_api;
var selectIndex = -1;
var tables = new Array();
// add for table management end
// add for Openbay Integration begin
$('input[name=\'enable_openbay\']').live('click', function() {
	if ($(this).is(':checked')) {
		$(this).attr('value', '1');
	} else {
		$(this).attr('value', '0');
	}
});
// add for Openbay Integration begin

// add for Inplace Pricing begin
$('input[name=\'enable_inplace_pricing\']').live('click', function() {
	if ($(this).is(':checked')) {
		$(this).attr('value', '1');
	} else {
		$(this).attr('value', '0');
	}
});
// add for Inplace Pricing end

// add for Hiding Delete begin
$('input[name=\'enable_hide_delete\']').live('click', function() {
	if ($(this).is(':checked')) {
		$(this).attr('value', '1');
	} else {
		$(this).attr('value', '0');
	}
});
// add for Hiding Delete end

$('input[name=\'display_once_login\']').live('click', function() {
	if ($(this).is(':checked')) {
		$(this).attr('value', '1');
	} else {
		$(this).attr('value', '0');
	}
});

// add for Rounding begin
$('input[name=\'enable_rounding\']').live('click', function() {
	if ($(this).is(':checked')) {
		$(this).attr('value', '1');
	} else {
		$(this).attr('value', '0');
	}
});
// add for Rounding begin

// add for Print begin
$('input[name=\'p_complete\']').live('click', function() {
	if ($(this).is(':checked')) {
		$(this).attr('value', '1');
	} else {
		$(this).attr('value', '0');
	}
});
$('input[name=\'p_payment\']').live('click', function() {
	if ($(this).is(':checked')) {
		$(this).attr('value', '1');
	} else {
		$(this).attr('value', '0');
	}
});
// add for Print begin
// add for Empty order control begin
$('input[name=\'delete_order_with_no_products\']').live('click', function() {
	if ($(this).is(':checked')) {
		$(this).attr('value', '1');
	} else {
		$(this).attr('value', '0');
	}
});
$('input[name=\'delete_order_with_inital_status\']').live('click', function() {
	if ($(this).is(':checked')) {
		$(this).attr('value', '1');
	} else {
		$(this).attr('value', '0');
	}
});
$('input[name=\'delete_order_with_deleted_status\']').live('click', function() {
	if ($(this).is(':checked')) {
		$(this).attr('value', '1');
	} else {
		$(this).attr('value', '0');
	}
});

function deleteOrders() {
	var no_product = $('input[name=action_delete_order_with_no_products]').is(":checked");
	var initial_status = $('input[name=action_delete_order_with_inital_status]').is(":checked");
	var delete_status = $('input[name=action_delete_order_with_deleted_status]').is(":checked");
	var data = {'no_product':no_product, 'initial_status':initial_status, 'delete_status':delete_status};

	$.ajax({
		url: 'index.php?route=module/pos/deleteEmptyOrdersPOST&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			$('#delete_orders').hide();
			$('#delete_orders').before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
		},
		complete: function() {
			$('.loading').remove();
			$('#delete_orders').show();
		},
		success: function(json) {
			$('input[name=action_delete_order_with_no_products]').attr('checked', false);
			$('input[name=action_delete_order_with_inital_status]').attr('checked', false);
			$('input[name=action_delete_order_with_deleted_status]').attr('checked', false);
		}
	});
}
// add for Empty order control end

var payment_type_row = <?php echo $payment_type_row_no; ?>;

function addPaymentType() {
	var checkValue = checkPaymentType();

	if (checkValue == 1) {
		// already in the list
		warning_tips = '<img src="view/image/warning.png" id="type_warning_tips" alt="<?php echo $text_type_already_exist; ?>" title="<?php echo $text_type_already_exist; ?>" />';
		$('#type_warning_tips').remove();
		$(warning_tips).insertAfter($('#payment_type'));
		return false;
	}

	$('#type_warning_tips').remove();
	var value = $('#payment_type').val();
	var new_payment_type_html = '<tr id="payment_type_' + payment_type_row + '"><td class="left" width="70%">' + value + '</td><td class="right" width="30%"><a onclick="deletePaymentType(\'payment_type_' + payment_type_row + '\');" class="button" size=2><?php echo $button_remove; ?></a></td><input type="hidden" name="POS_payment_types[' + payment_type_row + ']" value="' + value + '"/></tr>';
	$(new_payment_type_html).insertAfter('#payment_type_add');
	$('#payment_type').val("");
	payment_type_row ++;

};

function deletePaymentType(rowId) {
	$('#'+rowId).remove();
};

function checkPaymentType() {
	retValue = 0;
	curValue = $('#payment_type').val().toLowerCase();
	$("#payment_type_table tr").each(function(){
		value = $(this).find('td:first-child').text().toLowerCase();
		if (curValue == value) {
			retValue = 1;
		}
	});

	return retValue;
};

function addPaymentOnEnter(e) {
	var key;
	if (window.event)
		key = window.event.keyCode; //IE
	else
		key = e.which; //Firefox & others

	if(key == 13) {
		addPaymentType();
		return false;
	}
}

// add for Print begin
function image_upload(field, thumb, keep_size, fn) {
	$('#dialog').remove();
	
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	
	$('#dialog').dialog({
		title: '<?php echo $text_print_image_manager; ?>',
		close: function (event, ui) {
			if ($('#' + field).attr('value')) {
				var url = 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).val());
				if (keep_size) {
					url = 'index.php?route=module/pos/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).val());
				}
				$.ajax({
					url: url,
					dataType: 'text',
					success: function(data) {
						if (fn) {
							var imgData = $('#' + field).val();
							// $('#' + thumb).replaceWith('<img src="<?php echo HTTP_CATALOG; ?>/image/' + imgData + '" alt="" id="' + thumb + '" />');
							$('#' + field).attr('value', '<?php echo HTTP_CATALOG; ?>/image/' + imgData);
							fn();
						} else {
							$('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
							$('#' + field).attr('value', data);
						}
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
// add for Print end
// add for User as Affiliate begin
function addUA() {
	var user_id = $('select[name=user_name]').val();
	var username = $('select[name=user_name] option:selected').text();
	var affi_id = $('select[name=affiliate_name]').val();
	var affiname = $('select[name=affiliate_name] option:selected').text();
	if (username != '' && affiname != '') {
		var data = {'user_id':user_id, 'affiliate_id':affi_id};
		$.ajax({
			url: 'index.php?route=module/pos/addUA&token=<?php echo $token; ?>',
			type: 'post',
			dataType: 'json',
			data: data,
			beforeSend: function() {
				$('#button_add_ua').hide();
				$('#button_add_ua').before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
			},
			complete: function() {
				$('.loading').remove();
				$('#button_add_ua').show();
			},
			success: function(json) {
				var trId = 'user_affi-' + user_id + '-' + affi_id;
				var tr_element = '<tr id="' + trId + '"><td class="left" width="45%">' + username + '</td><td class="left" width="45%">' + affiname + '</td><td align="center" width="10%"><a onclick="deleteUA(\'' + trId + '\');"><img src="view/image/pos/delete_off.png" width="20" height="20"/></a></td></tr>'
				$(tr_element).insertAfter('#user_affi_tr');
				// remove value from the list
				$('select[name=user_name] option:selected').remove();
				$('select[name=affiliate_name] option:selected').remove();
			}
		});
	}
};

function deleteUA(uaTrId) {
	var ids = uaTrId.split('-');
	var data = {'user_id':ids[1], 'affiliate_id':ids[2]};
	var username = $('#'+uaTrId).find('td').eq(0).text();
	var affiname = $('#'+uaTrId).find('td').eq(1).text();
	var xButton = $('#'+uaTrId).find('td').eq(2).find('a').eq(0);
	$.ajax({
		url: 'index.php?route=module/pos/deleteUA&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			xButton.hide();
			xButton.before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
		},
		complete: function() {
			$('.loading').remove();
		},
		success: function(json) {
			$('#'+uaTrId).remove();
			// add value back to the list
			$('select[name=user_name]').append('<option value="' + ids[1] + '">' + username + '</option>');
			$('select[name=affiliate_name]').append('<option value="' + ids[2] + '">' + affiname + '</option>');
		}
	});
};
// add for User as Affiliate end
// add for Default Customer begin
function country(zone_id) {
  if ($('select[name=a_country_id]').val() != '') {
		$.ajax({
			url: 'index.php?route=sale/customer/country&token=<?php echo $token; ?>&country_id=' + $('select[name=a_country_id]').val(),
			dataType: 'json',
			beforeSend: function() {
				$('select[name=a_country_id]').after('<span class="wait">&nbsp;<img src="view/image/loading.gif" alt="" /></span>');
			},
			complete: function() {
				$('.wait').remove();
			},			
			success: function(json) {
				if (json['postcode_required'] == '1') {
					$('#postcode-required').show();
				} else {
					$('#postcode-required').hide();
				}
				
				html = '<option value=""><?php echo $text_select; ?></option>';
				
				if (json['zone'] != '') {
					for (i = 0; i < json['zone'].length; i++) {
						html += '<option value="' + json['zone'][i]['zone_id'] + '"';
						
						if (json['zone'][i]['zone_id'] == zone_id) {
							html += ' selected="selected"';
						}
		
						html += '>' + json['zone'][i]['name'] + '</option>';
					}
				} else {
					html += '<option value="0"><?php echo $text_none; ?></option>';
				}
				
				$('select[name=a_zone_id]').html(html);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
};

$('input[name=c_type]').live('change', function(event) {
	if ($('input[name=c_type]:checked').val() == '1') {
		$('#c_autocomplete').hide();
		// disable all values
		$('#c_default input[type=text]').each(function() {
			// disable the input
			$(this).attr('disabled', true);
		});
		$('#c_default select').each(function() {
			// disable the input
			$(this).attr('disabled', true);
		});
		setBuildinValue();
	} else if ($('input[name=c_type]:checked').val() == '2') {
		$('#c_autocomplete').hide();
		// enable all values
		$('#c_default input[type=text]').each(function() {
			// disable the input
			$(this).attr('disabled', false);
		});
		$('#c_default select').each(function() {
			// disable the input
			$(this).attr('disabled', false);
		});
		setConfigValue();
	} else {
		// disable all values
		$('#c_default input[type=text]').each(function() {
			// disable the input
			$(this).attr('disabled', true);
		});
		$('#c_default select').each(function() {
			// disable the input
			$(this).attr('disabled', true);
		});
		$('input[name=c_name]').attr('disabled', false);
		$('#c_autocomplete').show();
		setConfigValue();
	}
});

function setBuildinValue() {
	$('input[name=c_name]').attr('value', '<?php echo $buildin['c_name']; ?>');
	$('input[name=c_id]').attr('value', '0');
	$('select[name=c_group_id]').attr('value', '1');
	$('select[name=c_group_id]').trigger('change');
	$('input[name=c_firstname]').attr('value', '<?php echo $buildin['c_firstname']; ?>');
	$('input[name=c_lastname]').attr('value', '<?php echo $buildin['c_lastname']; ?>');
	$('input[name=c_email]').attr('value', '<?php echo $buildin['c_email']; ?>');
	$('input[name=c_telephone]').attr('value', '<?php echo $buildin['c_telephone']; ?>');
	$('input[name=c_fax]').attr('value', '<?php echo $buildin['c_fax']; ?>');
	$('select[name=a_country_id]').attr('value', '<?php echo $buildin['a_country_id']; ?>');
	$('input[name=a_firstname]').attr('value', '<?php echo $buildin['a_firstname']; ?>');
	$('input[name=a_lastname]').attr('value', '<?php echo $buildin['a_lastname']; ?>');
	$('input[name=a_address_1]').attr('value', '<?php echo $buildin['a_address_1']; ?>');
	$('input[name=a_address_2]').attr('value', '<?php echo $buildin['a_address_2']; ?>');
	$('input[name=a_city]').attr('value', '<?php echo $buildin['a_city']; ?>');
	$('input[name=a_postcode]').attr('value', '<?php echo $buildin['a_postcode']; ?>');
	$('select[name=a_country_id]').attr('onchange', 'country(\'<?php echo $buildin['a_zone_id']; ?>\')');
	$('select[name=a_country_id]').trigger('change');
};

function setConfigValue() {
	$('input[name=c_id]').attr('value', '<?php echo $c_id; ?>');
	$('select[name=c_group_id]').attr('value', '<?php echo $c_group_id; ?>');
	if ($('input[name=c_type]:checked').val() == '2') {
		$('input[name=c_id]').attr('value', '0');
	}
	$('select[name=c_group_id]').trigger('change');
	$('input[name=c_name]').attr('value', '<?php echo $c_name; ?>');
	$('input[name=c_firstname]').attr('value', '<?php echo $c_firstname; ?>');
	$('input[name=c_lastname]').attr('value', '<?php echo $c_lastname; ?>');
	$('input[name=c_email]').attr('value', '<?php echo $c_email; ?>');
	$('input[name=c_telephone]').attr('value', '<?php echo $c_telephone; ?>');
	$('input[name=c_fax]').attr('value', '<?php echo $c_fax; ?>');
	$('select[name=a_country_id]').attr('value', '<?php echo $a_country_id; ?>');
	$('input[name=a_firstname]').attr('value', '<?php echo $a_firstname; ?>');
	$('input[name=a_lastname]').attr('value', '<?php echo $a_lastname; ?>');
	$('input[name=a_address_1]').attr('value', '<?php echo $a_address_1; ?>');
	$('input[name=a_address_2]').attr('value', '<?php echo $a_address_2; ?>');
	$('input[name=a_city]').attr('value', '<?php echo $a_city; ?>');
	$('input[name=a_postcode]').attr('value', '<?php echo $a_postcode; ?>');
	$('select[name=a_country_id]').attr('onchange', 'country(\'<?php echo $a_zone_id; ?>\')');
	$('select[name=a_country_id]').trigger('change');
};

$.widget('custom.catcomplete', $.ui.autocomplete, {
	_renderMenu: function(ul, items) {
		var self = this, currentCategory = '';
		
		$.each(items, function(index, item) {
			if (item.category != currentCategory) {
				ul.append('<li class="ui-autocomplete-category">' + item.category + '</li>');
				
				currentCategory = item.category;
			}
			
			self._renderItem(ul, item);
		});
	}
});

$('input[name=c_name]').live('focus', function(){
	$(this).catcomplete({
		delay: 500,
		source: function(request, response) {
			$.ajax({
				url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
				dataType: 'json',
				success: function(json) {	
					response($.map(json, function(item) {
						return {
							category: item['customer_group'],
							label: item['name'],
							value: item['customer_id'],
							customer_group_id: item['customer_group_id'],
							firstname: item['firstname'],
							lastname: item['lastname'],
							email: item['email'],
							telephone: item['telephone'],
							fax: item['fax'],
							address: item['address']
						}
					}));
				}
			});
		}, 
		select: function(event, ui) { 
			$('input[name=c_name]').attr('value', ui.item['label']);
			$('input[name=c_id]').attr('value', ui.item['value']);
			$('select[name=c_group_id]').attr('value', ui.item['customer_group_id']);
			$('select[name=c_group_id]').trigger('change');
			$('input[name=c_firstname]').attr('value', ui.item['firstname']);
			$('input[name=c_lastname]').attr('value', ui.item['lastname']);
			$('input[name=c_email]').attr('value', ui.item['email']);
			$('input[name=c_telephone]').attr('value', ui.item['telephone']);
			$('input[name=c_fax]').attr('value', ui.item['fax']);
			
			for (i in ui.item['address']) {
				$('select[name=a_country_id]').attr('value', ui.item['address'][i]['country_id']);
				$('input[name=a_firstname]').attr('value', ui.item['address'][i]['firstname']);
				$('input[name=a_lastname]').attr('value', ui.item['address'][i]['lastname']);
				$('input[name=a_address_1]').attr('value', ui.item['address'][i]['address_1']);
				$('input[name=a_address_2]').attr('value', ui.item['address'][i]['address_2']);
				$('input[name=a_city]').attr('value', ui.item['address'][i]['city']);
				$('input[name=a_postcode]').attr('value', ui.item['address'][i]['postcode']);
				$('select[name=a_country_id]').attr('onchange', 'country(\'' + ui.item['address'][i]['zone_id'] + '\')');
				$('select[name=a_country_id]').trigger('change');
				break;
			}

			return false; 
		},
		focus: function(event, ui) {
			return false;
		}
	});
});
$('input[name=c_type]').trigger('change');
$('select[name=a_country_id]').trigger('change');
// add for Default Customer end
// add for Quotation begin
function addQuoteStatus() {
	var newStatus = $('input[name=quote_status_name]').val();
	if (validateQuoteStatus(newStatus)) {
		var warning_tips = '<img src="view/image/warning.png" id="status_warning_tips" alt="<?php echo $text_quote_status_already_exist; ?>" title="<?php echo $text_quote_status_already_exist; ?>" />';
		$('#status_warning_tips').remove();
		$(warning_tips).insertAfter($('input[name=quote_status_name]'));
		return false;
	}
	$('#status_warning_tips').remove();
	var data = {'status' : newStatus};
	$.ajax({
		url: 'index.php?route=module/pos/addQuoteStatus&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			$('#button_add_quote_status').hide();
			$('#button_add_quote_status').before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
		},
		complete: function() {
			$('.loading').remove();
			$('#button_add_quote_status').show();
		},
		success: function(json) {
			if (json['error']) {
				var error_tips = '<img src="view/image/warning.png" id="status_warning_tips" alt="' + json['error'] + '" title="' + json['error'] +'" />';
				$('#status_warning_tips').remove();
				$(error_tips).insertAfter($('input[name=quote_status_name]'));
			} else {
				// add to the list
				var new_quote_status_html = '<tr><td class="left" width="70%"><span>' + newStatus + '</span><input type="hidden" name="quote_status_id_' + json['quote_status_id'] + '" value="' + json['quote_status_id'] + '" /></td><td class="right" width="30%"><a onclick="renameQuoteStatus(this);" class="button"><?php echo $button_rename; ?></a>&nbsp;<a onclick="deleteQuoteStatus(this);" class="button"><?php echo $button_delete; ?></a></td></tr>';
				$(new_quote_status_html).insertAfter($('#quote_status_tr'));
				$('input[name=quote_status_name]').attr('value', '');
			}
		}
	});		
};

function validateQuoteStatus(status) {
	retValue = 0;
	var newValue = status.toLowerCase();
	$("#quote_status_list tr").each(function(){
		var value = $(this).find('span').text().toLowerCase();
		if (newValue == value) {
			retValue = 1;
		}
	});
	return retValue;
};

function renameQuoteStatus(anchor) {
	// rename the quote status
	var curValue = $(anchor).closest('tr').find('span').text();
	var newValue = prompt('<?php echo $text_rename; ?>', curValue);
	if (curValue == newValue ) {
		return false;
	}
	if (validateQuoteStatus(newValue)) {
		var warning_tips = '<img src="view/image/warning.png" id="status_warning_tips" alt="<?php echo $text_quote_status_already_exist; ?>" title="<?php echo $text_quote_status_already_exist; ?>" />';
		$('#status_warning_tips').remove();
		$(warning_tips).insertAfter($('input[name=quote_status_name]'));
		return false;
	}
	$('#status_warning_tips').remove();
	
	var quote_status_id = $(anchor).closest('tr').find('input').val();
	var data = {'status' : newValue, 'status_id' : quote_status_id};
	$.ajax({
		url: 'index.php?route=module/pos/renameQuoteStatus&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			$(anchor).hide();
			$(anchor).before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
		},
		complete: function() {
			$('.loading').remove();
			$(anchor).show();
		},
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			} else {
				// add to the list
				$(anchor).closest('tr').find('span').text(newValue);
			}
		}
	});
};

function deleteQuoteStatus(anchor) {
	// delete the quote status
	var quote_status_id = $(anchor).closest('tr').find('input').val();
	var data = {'status_id' : quote_status_id};
	$.ajax({
		url: 'index.php?route=module/pos/deleteQuoteStatus&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			$(anchor).hide();
			$(anchor).before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
		},
		complete: function() {
			$('.loading').remove();
			$(anchor).show();
		},
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			} else {
				// add to the list
				$(anchor).closest('tr').remove();
			}
		}
	});
};

function addStatusOnEnter(e) {
	var key;
	if (window.event)
		key = window.event.keyCode; //IE
	else
		key = e.which; //Firefox & others

	if(key == 13) {
		addQuoteStatus();
		return false;
	}
}

// add for Quotation end
// add for Cash type begin
function addCashType() {
	// add the current values into the list
	var rowId = $('#cash_type_list tr').length - 1;
	var html = '<tr id="cash_type-' + rowId + '">';
	html += '<td class="left">' + $('select[name=cash_type]').val();
	html += '<input type="hidden" name="cash_types[' + rowId + '][type]" value="' + $('select[name=cash_type]').val() + '" /></td>';
	html += '<td class="left"><img src="' + $('input[name=cash_image_path]').val() + '" style="max-width: 360px; max-height: 200px; width: auto; height: auto;" />';
	html += '<input type="hidden" name="cash_types[' + rowId + '][image]" value="' + $('input[name=cash_image_path]').val() + '" /></td>';
	html += '<td class="right">' + $('input[name=cash_value]').val() + '<input type="hidden" name="cash_types[' + rowId + '][value]" value="' + $('input[name=cash_value]').val() + '" /></td>';
	html += '<td align="center"><a onclick="deleteCashType(\'cash_type-' + rowId + '\');"><img src="view/image/pos/delete_off.png" width="20" height="20"/></a></td>';
	html += '</tr>';
	$('#cash_type_list').append(html);
	// clear the current value row
	$('#cash_image').attr('src', '');
	$('#cash_image_path').attr('value', '');
	$('input[name=cash_value]').attr('value', '');
};

function deleteCashType(rowId) {
	$('#'+rowId).remove();
}
// add for Cash type end
// add for location based stock begin
function addLocation() {
	var newCode = $('input[name=location_code]').val();
	var newName = $('input[name=location_name]').val();
	var newDesc = $('textarea[name=location_desc]').val();
	if (validateLocationCode(newCode)) {
		var warning_tips = '<img src="view/image/warning.png" id="location_warning_tips" alt="<?php echo $text_location_already_exist; ?>" title="<?php echo $text_location_already_exist; ?>" />';
		$('#location_warning_tips').remove();
		$(warning_tips).insertAfter($('input[name=location_code]'));
		return false;
	}
	$('#location_warning_tips').remove();
	var data = {'code' : newCode, 'name' : newName, 'description' : newDesc};
	$.ajax({
		url: 'index.php?route=module/pos/addLocation&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			$('#button_add_location').hide();
			$('#button_add_location').before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
		},
		complete: function() {
			$('.loading').remove();
			$('#button_add_location').show();
		},
		success: function(json) {
			if (json['error']) {
				var error_tips = '<img src="view/image/warning.png" id="location_warning_tips" alt="' + json['error'] + '" title="' + json['error'] +'" />';
				$('#location_warning_tips').remove();
				$(error_tips).insertAfter($('input[name=location_code]'));
			} else {
				// add to the list
				var new_location_html = '<tr><td class="left" width="15%"><span>' + newCode + '</span><input type="hidden" name="location_id_' + json['location_id'] + '" value="' + json['location_id'] + '" /></td><td class="left" width="35%">' + newName + '</td><td class="left" width="35%">' + newDesc + '</td><td class="center" width="15%"><a onclick="deleteLocation(this);"><img src="view/image/pos/delete_off.png" width="20" height="20"/></a></td></tr>';
				$(new_location_html).insertAfter($('#location_tr'));
				$('input[name=location_code]').attr('value', '');
				$('input[name=location_name]').attr('value', '');
				$('input[name=location_desc]').attr('value', '');
			}
		}
	});		
};

function validateLocationCode(code) {
	var retValue = 0;
	var newValue = code.toLowerCase();
	$("#location_list tr").each(function(){
		var value = $(this).find('span').text().toLowerCase();
		if (newValue == value) {
			retValue = 1;
		}
	});
	return retValue;
};

function deleteLocation(anchor) {
	// delete the location
	var location_id = $(anchor).closest('tr').find('input').val();
	var data = {'location_id' : location_id};
	$.ajax({
		url: 'index.php?route=module/pos/deleteLocation&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			$(anchor).hide();
			$(anchor).before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
		},
		complete: function() {
			$('.loading').remove();
			$(anchor).show();
		},
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			} else {
				// add to the list
				$(anchor).closest('tr').remove();
			}
		}
	});
};
// add for location based stock end

$(document).ready(function() {
	$('.htabs a').tabs();
});

// add for location based stock begin
$('input[name=\'enable_location_stock\']').live('click', function() {
	if ($(this).is(':checked')) {
		$(this).attr('value', '1');
	} else {
		$(this).attr('value', '0');
	}
});
// add for location based stock begin
// add for table management begin
$('input[name=\'enable_table_management\']').live('click', function() {
	if ($(this).is(':checked')) {
		$(this).attr('value', '1');
	} else {
		$(this).attr('value', '0');
	}
});

function onUpload() {
	/*
	if (!jcrop_api) {
		$('#img_table_layout').replaceWith('<img src="' + $('#table_layout').val() + '" alt="" id="img_table_layout" />');
		$('#img_table_layout').Jcrop({
			onChange:   setCoords,
			onSelect:   setCoords,
			onRelease:  clearCoords,
			bgColor:	'white',
			maxSize:	[800, 600],
			setSelect:	[10, 10, 60, 60]
		},function(){
			jcrop_api = this;
		});
	} else {
		jcrop_api.setImage($('#table_layout').val());
		jcrop_api.setSelect([10, 10, 60, 60]);
	}
	*/
};

function setCoords(c) {
	if (c.x == c.x2 && c.y == c.y2) {
		// click detected
		var inRange = false;
		for (var i in tables) {
			var coors = tables[i]['coors'];
			if (coors) {
				var xys = coors.split(',');
				if (xys.length == 4) {
					var x1 = parseInt(xys[0]), y1 = parseInt(xys[1]), x2 = parseInt(xys[2]), y2 = parseInt(xys[3]);
					if (c.x >= x1 && c.x <= x2 && c.y >= y1 && c.y <= y2) {
						selectIndex = tables[i]['table_id'];
						inRange = true;
						$('input[name=x1]').attr('value', x1);
						$('input[name=y1]').attr('value', y1);
						$('input[name=x2]').attr('value', x2);
						$('input[name=y2]').attr('value', y2);
						$('input[name=table_name]').attr('value', tables[i]['name']);
						$('textarea[name=table_desc]').attr('value', tables[i]['desc']);
						break;
					}
				}
			}
		}
		if (!inRange) {
			selectIndex = -1;
		}
	} else {
		$('input[name=x1]').attr('value', c.x);
		$('input[name=y1]').attr('value', c.y);
		$('input[name=x2]').attr('value', c.x2);
		$('input[name=y2]').attr('value', c.y2);
	}
};

function clearCoords() {
	var x1 = $('input[name=x1]').val();
	var y1 = $('input[name=y1]').val();
	var x2 = $('input[name=x2]').val();
	var y2 = $('input[name=y2]').val();
	jcrop_api.setSelect([x1, y1, x2, y2]);
}

function addRect(color, x1, y1, x2, y2, id) {
	$('<div id="pos_table_'+id+'" style="position:absolute;"/>')
	.appendTo($('.jcrop-holder'))
	.css("left", x1 + "px")
	.css("top", y1 + "px")
	.css("width", (x2-x1)+"px")
	.css("height", (y2-y1)+"px")
	.css("border", "1px solid " + color);
};

function setTable() {
	// id should be unique
	var name = $('input[name=table_name]').val();
	if (name == '') {
		alert('<?php echo $text_table_name_empty; ?>');
	} else {
		var inTable = false;
		for (var i in tables) {
			if (tables[i]['name'] == name) {
				inTable = true;
				break;
			}
		}
		
		if (inTable && selectIndex == -1) {
			alert('<?php echo $text_table_name_exists; ?>');
		} else {
			// add into the table
			var coors = $('input[name=x1]').val() + ',' + $('input[name=y1]').val() + ',' + $('input[name=x2]').val() + ',' + $('input[name=y2]').val();
			var desc = $('textarea[name=table_desc]').val();
			var data = {'index':selectIndex, 'name':name, 'desc':desc, 'coors':coors};
			$.ajax({
				url: 'index.php?route=module/pos/addTable&token=<?php echo $token; ?>',
				type: 'post',
				dataType: 'json',
				data: data,
				beforeSend: function() {
					$('#set_table').hide();
					$('#set_table').before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
				},
				complete: function() {
					$('.loading').remove();
					$('#set_table').show();
				},
				success: function(json) {
					if (json['error']) {
						alert(json['error']);
					} else {
						if (selectIndex == -1) {
							// add to the list
							data['table_id'] = json['table_id'];
							tables.push(data);
							// add rectangle to the selected table
							addRect('red', $('input[name=x1]').val(), $('input[name=y1]').val(), $('input[name=x2]').val(), $('input[name=y2]').val(), json['table_id']);
						} else {
							for (var i in tables) {
								if (tables[i]['table_id'] == selectIndex) {
									tables[i] = data;
									// update rectangle
									$('div[id=pos_table_'+selectIndex+']').css("left", $('input[name=x1]').val() + "px")
									.css("top", $('input[name=y1]').val() + "px")
									.css("width", ($('input[name=x2]').val()-$('input[name=x1]').val())+"px")
									.css("height", ($('input[name=y2]').val()-$('input[name=y1]').val())+"px")
									.css("border", "1px solid red");
									break;
								}
							}
						}
						
						// reset the index
						selectIndex = -1;
						jcrop_api.setSelect([10, 10, 60, 60]);
					}
				}
			});
		}
	}
};

function deleteTable() {
	if (selectIndex > -1) {
		// only when a talbe is selected
		var data = {'index':selectIndex};
		$.ajax({
			url: 'index.php?route=module/pos/deleteTable&token=<?php echo $token; ?>',
			type: 'post',
			dataType: 'json',
			data: data,
			beforeSend: function() {
				$('#delete_table').hide();
				$('#delete_table').before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
			},
			complete: function() {
				$('.loading').remove();
				$('#delete_table').show();
			},
			success: function(json) {
				if (json['error']) {
					alert(json['error']);
				} else {
					for (var i in tables) {
						if (tables[i]['table_id'] == selectIndex) {
							tables.splice(i, 1);
							// remove the rectangle
							$('div[id=pos_table_'+selectIndex+']').remove();
							break;
						}
					}
					
					// reset the index
					selectIndex = -1;
					jcrop_api.setSelect([10, 10, 60, 60]);
				}
			}
		});
	}
};

function setTableNumber() {
	// check the number of tables against the existing number of tables
	var curNumber = $('#table_list tr').length - 2;
	var newNumber = parseInt($('input[name=table_number]').val());
	if (newNumber < curNumber) {
		var table_ids = new Array();
		$('#table_list tr:gt(' + (newNumber+1) + ')').each(function() {
			table_ids.push($(this).find('input[type=hidden]').val());
			$(this).remove();
		});
		var table_ids_str = table_ids.join(',');
		var data = {'table_ids':table_ids_str};
		$.ajax({
			url: 'index.php?route=module/pos/deleteTableBatch&token=<?php echo $token; ?>',
			type: 'post',
			dataType: 'json',
			data: data,
			beforeSend: function() {
				$('#button_set_number').hide();
				$('#button_set_number').before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
			},
			complete: function() {
				$('.loading').remove();
				$('#button_set_number').show();
			},
			success: function(json) {
				if (json['error']) {
					alert(json['error']);
				}
			}
		});
	} else if (newNumber > curNumber) {
		var startNum = curNumber + 1;
		var data = {'startNum':startNum, 'total':(newNumber-curNumber)};
		$.ajax({
			url: 'index.php?route=module/pos/addTableBatch&token=<?php echo $token; ?>',
			type: 'post',
			dataType: 'json',
			data: data,
			beforeSend: function() {
				$('#button_set_number').hide();
				$('#button_set_number').before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
			},
			complete: function() {
				$('.loading').remove();
				$('#button_set_number').show();
			},
			success: function(json) {
				if (json['error']) {
					alert(json['error']);
				} else if (json['table_ids']) {
					for (var i in json['table_ids']) {
						var trHtml = '<tr><td class="left"><input type="text" name="name_' + json['table_ids'][i]['table_id'] + '" value="' + json['table_ids'][i]['name'] + '" style="width:98%;"/></td><td class="left"><input type="text" name="desc_' + json['table_ids'][i]['table_id'] + '" value="" style="width:98%;"/></td>';
						trHtml    += '<td class="left"><input type="hidden" value="' + json['table_ids'][i]['table_id'] + '" /><a onclick="modifyTable(this);" class="button"><?php echo $button_table_modify; ?></a>&nbsp;&nbsp;<a onclick="removeTable(this);" class="button"><?php echo $button_table_remove; ?></a></td></tr>';
						$('#table_list').append(trHtml);
					}
				}
			}
		});
	}
};

function modifyTable(anchor) {
	var table_id = $(anchor).closest('td').find('input').val();
	var name = $(anchor).closest('tr').find('input[name^=name]').val();
	var desc = $(anchor).closest('tr').find('input[name^=desc]').val();
	var data = {'index':table_id, 'name':name, 'desc':desc, 'coors':''};
	$.ajax({
		url: 'index.php?route=module/pos/addTable&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			$(anchor).hide();
			$(anchor).before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
		},
		complete: function() {
			$('.loading').remove();
			$(anchor).show();
		},
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			}
		}
	});
};

function removeTable(anchor) {
	var table_id = $(anchor).closest('td').find('input').val();
	var data = {'index':table_id};

	$.ajax({
		url: 'index.php?route=module/pos/deleteTable&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			$(anchor).hide();
			$(anchor).before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
		},
		complete: function() {
			$('.loading').remove();
			$(anchor).show();
		},
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			} else {
				$(anchor).closest('tr').remove();
			}
		}
	});
};
// add for table management begin
// add for serial no begin
$('input[name=\'product_name_new\']').autocomplete({
	delay: 500,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
						label: item.name,
						value: item.product_id
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('input[name=\'product_name_new\']').val(ui.item.label);
		$('input[name=\'product_id_new\']').val(ui.item.value);
		$('#new_serial_no_list').show();

		return false;
	},
	focus: function(event, ui) {
      	return false;
   	}
});

function resetSNQuantity() {
	$('#new_serial_no_list tr').slice(1, -1).remove();
	$('input[name^=\'product_sn[1]\']').val('');
	$('#new_serial_no_list').hide();
};

$('input[name^=product_sn]').live('keydown', function() {
	var len = $('#new_serial_no_list tr').length;
	var index = $('#new_serial_no_list tr').index($(this).closest('tr'));
	if (index == len - 2) {
		// create another line
		$('#tr_sn_save').before('<tr><td class="left"><?php echo $entry_sn; ?> #' + (index+2) + '</td><td class="left"><input type="text" name="product_sn[' + (index+2) + ']" value="" style="width:98%" /></td></tr>');
	}
});

function saveSN(anchor) {
	var product_id = $('input[name=\'product_id_new\']').val();
	var data = {};
	$('input[name^=\'product_sn\']').each(function() {
		if ($(this).val().trim() != '') {
			data[$(this).attr('name')] = $(this).val();
		}
	});
	data['product_id'] = product_id;

	$.ajax({
		url: 'index.php?route=module/pos/saveProductSN&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			$(anchor).hide();
			$(anchor).before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
		},
		complete: function() {
			$('.loading').remove();
			$(anchor).show();
		},
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			}
			alert(json['success']);

			resetSNQuantity();
			$('input[name=\'product_id_new\']').val('');
			$('input[name=\'product_name_new\']').val('');
		}
	});
};

$('input[name=\'filter_sn_name\']').autocomplete({
	delay: 500,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
						label: item.name,
						value: item.product_id
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('input[name=\'filter_sn_name\']').val(ui.item.label);
		$('input[name=\'filter_product_id\']').val(ui.item.value);

		return false;
	},
	focus: function(event, ui) {
      	return false;
   	}
});

function filterSN() {
	var filter_name = $('input[name=\'filter_sn_name\']').val();
	var filter_product_id = $('input[name=\'filter_product_id\']').val();
	if (filter_name == '') {
		filter_product_id = '';
	}

	var filter_sn = $('input[name=\'filter_sn_sn\']').val();
	var filter_status = $('select[name=filter_sn_status]').val();
	var data = {'product_id':filter_product_id, 'sn':filter_sn, 'status':filter_status};
	// get the SN from the database with filters
	$.ajax({
		url: 'index.php?route=module/pos/getProductSN&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		data: data,
		success: function(json) {
			if (json) {
				$('#product_serial_no_list tr:gt(0)').remove();
				for (var i in json) {
					trHtml  = '<tr><td class="left">' + json[i]['name'] + '<input type="hidden" value="' + json[i]['product_sn_id'] + '"/></td>';
					trHtml += '<td class="left">' + json[i]['sn'] + '</td>';
					trHtml += '<td class="left">' + json[i]['status'] + '</td>';
					trHtml += '<td align="right"><a onclick="deleteSN(this);" class="button"><?php echo $button_delete; ?></a></td></tr>';
					$('#product_serial_no_list').append(trHtml);
				}
			}
		}
	});
};

function deleteSN(anchor) {
	var product_sn_id = $(anchor).closest('tr').find('input').val();
	var data = {'product_sn_id':product_sn_id};

	$.ajax({
		url: 'index.php?route=module/pos/deleteProductSN&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			$(anchor).hide();
			$(anchor).before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
		},
		complete: function() {
			$('.loading').remove();
			$(anchor).show();
		},
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			} else {
				$(anchor).closest('tr').remove();
			}
		}
	});
};
// add for serial no end
// add for till control begin
$('input[name=\'enable_till_control\']').live('click', function() {
	if ($(this).is(':checked')) {
		$(this).attr('value', '1');
	} else {
		$(this).attr('value', '0');
	}
});
function testTillControl() {
	<?php if (isset($enable_till_control) && $enable_till_control) { ?>
	var applet = document.jzebra;
	if (applet) {
		var key = $('input[name=till_control_key]').val();
		applet.append(eval("'" + key + "'"));
		applet.print();
	}
	<?php } ?>
};
$('input[name=\'enable_till_full_payment\']').live('click', function() {
	if ($(this).is(':checked')) {
		$(this).attr('value', '1');
	} else {
		$(this).attr('value', '0');
	}
});
// add for till control end
// add for status change notification begin
$('input[name=\'enable_notification\']').live('click', function() {
	if ($(this).is(':checked')) {
		$(this).attr('value', '1');
	} else {
		$(this).attr('value', '0');
	}
});
// add for status change notification begin
// add for commission begin
$('input[name=\'enable_commission\']').live('click', function() {
	if ($(this).is(':checked')) {
		$(this).attr('value', '1');
	} else {
		$(this).attr('value', '0');
	}
});

$('input[name=\'product_name_commission\']').autocomplete({
	delay: 500,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=module/pos/commission_autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
						label: item.product_name,
						id:    item.product_id,
						type:  item.commission_type,
						value: item.commission_value,
						base:  item.commission_base
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('input[name=\'product_name_commission\']').val(ui.item.label);
		$('input[name=\'product_id_commission\']').val(ui.item.id);
		if (ui.item.type == '1') {
			$('input[name=\'commission_type\'][value=1]').attr('checked', 'checked');
			$('input[name=\'commission_fixed\']').val(ui.item.value);
		} else if (ui.item.type == '2') {
			$('input[name=\'commission_type\'][value=2]').attr('checked', 'checked');
			$('input[name=\'commission_percentage\']').val(ui.item.value);
			$('input[name=\'commission_percentage_base\']').val(ui.item.base);
		}
		$('#set_commission').show();

		return false;
	},
	focus: function(event, ui) {
      	return false;
   	}
});

function saveCommission(anchor) {
	var type = '1';
	var value = $('input[name=\'commission_fixed\']').val();
	base = '0';
	if ($('input[name=commission_type]:checked').val() == '2') {
		type = '2';
		value = $('input[name=\'commission_percentage\']').val();
		base = $('input[name=\'commission_percentage_base\']').val();
	}
	var data = {'product_id':$('input[name=\'product_id_commission\']').val(), 'type':type, 'value':value, 'base':base};
	$.ajax({
		url: 'index.php?route=module/pos/saveCommission&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			$(anchor).hide();
			$(anchor).before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
		},
		complete: function() {
			$('.loading').remove();
			$(anchor).show();
		},
		success: function(json) {
			$('input[name=\'product_id_commission\']').val('');
			$('input[name=\'product_name_commission\']').val('');
			$('input[name=\'commission_fixed\']').val('');
			$('input[name=\'commission_percentage\']').val('');
			$('input[name=\'commission_percentage_base\']').val('');
			$('input[name=\'commission_type\'][value=1]').attr('checked', 'checked');
			$('#set_commission').hide();
		}
	});
};

function filterCommission() {
	var filter_name = $('input[name=\'filter_commission_product_name\']').val();
	var filter_product_id = $('input[name=\'filter_commission_product_id\']').val();
	if (filter_name == '') {
		filter_product_id = '';
	}

	var data = {};
	if (filter_name != '') {
		data['filter_name'] = filter_name;
	}
	if (filter_product_id != '') {
		data['filter_product_id'] = filter_product_id;
	}
	// get the product commissions from the database with filters
	$.ajax({
		url: 'index.php?route=module/pos/getProductCommissions&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		data: data,
		success: function(json) {
			if (json) {
				$('#product_commission_list tr:gt(0)').remove();
				for (var i in json) {
					trHtml  = '<tr><td class="left">' + json[i]['name'] + '<input type="hidden" value="' + json[i]['product_id'] + '"/></td>';
					trHtml += '<td class="left">' + json[i]['commission'] + '</td>';
					trHtml += '<td align="right"><a onclick="deleteCommission(this);" class="button"><?php echo $button_delete; ?></a></td></tr>';
					$('#product_commission_list').append(trHtml);
				}
			}
		}
	});
};

function deleteCommission(anchor) {
	var product_id = $(anchor).closest('tr').find('input').val();
	var data = {'product_id':product_id};

	$.ajax({
		url: 'index.php?route=module/pos/deleteProductCommission&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			$(anchor).hide();
			$(anchor).before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
		},
		complete: function() {
			$('.loading').remove();
			$(anchor).show();
		},
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			} else {
				$(anchor).closest('tr').remove();
			}
		}
	});
};
// add for commission end
</script> 

<?php echo $footer; ?>