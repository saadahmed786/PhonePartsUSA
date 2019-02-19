<?php
//==============================================================================
// Form Builder v154.2
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================
?>

<?php echo $header; ?>
<style type="text/css">
	#field-types, #tab-locations, #tab-fields, #tab-errors, #tab-email {
		display: none;
	}
	a.button {
		color: #FFF;
		font-size: 12px;
		font-weight: normal;
	}
	textarea {
		font-family: monospace;
		height: 100px;
	}
	textarea, .form td td > input {
		width: 600px;
	}
	.form td > input {
		width: 623px;
	}
	.help td {
		border: none;
		color: #666;
		font-size: 11px;
		line-height: 1.5;
		vertical-align: top;
	}
	
	.help-icon, .remove-icon {
		cursor: pointer;
	}
	.remove-icon {
		margin-top: 5px;
	}
	.list .help-icon {
		width: 16px;
		height: 16px;
		position: relative;
		top: 3px;
	}
	.fields .help-icon, .fields .remove-icon {
		float: right;
	}
	.ui-dialog {
		box-shadow: 0 6px 9px #AAA;
		position: fixed;
	}
	
	.scrollbox {
		margin: auto;
		height: 90px;
		width: 200px;
		text-align: left;
	}
	.scrollbox div {
		width: 194px;
	}
	.scrollbox label:nth-child(odd) div {
		background: #E8F4FF;
	}
	.scrollbox + div {
		font-size: 11px;
	}
	
	.list thead td {
		height: 24px;
	}
	.list tfoot td {
		background: #EEE;
	}
	.iblock-r {
		display: inline-block;
		line-height: 26px;
		text-align: right;
	}
	.iblock-l {
		display: inline-block;
		line-height: 26px;
		text-align: left;
	}
	.embed-code {
		background: #F8F8F8;
		cursor: pointer;
		font-family: monospace;
		width: 500px;
	}
	
    .fields {
		list-style-type: none;
		margin: 0;
		padding: 0;
	}
	.fields li {
		color: #000;
		margin: 8px 4px;
		padding: 0;
	}
	#field-types {
		position: fixed;
		top: 0;
		z-index: 99999;
		min-width: 870px;
		margin-right: 40px;
		border-radius: 0 0 5px 5px;
		box-shadow: 0 2px 20px #000;
	}
	#field-types li.draggable, #field-type-help {
		display: inline-block;
		margin: 0 0 5px 9px;
		border: 1px solid #FB0;
	}
	#field-types li.draggable:hover {
		box-shadow: 0 2px 4px #666;
	}
	#field-type-heading {
		display: block;
		padding: 5px 10px;
		font-weight: bold;
		font-size: 16px;
	}
	#field-type-help {
		background: none;
		padding: 5px 7px;
		text-align: center;
		vertical-align: top;
		width: 116px;
	}
	#field-type-help, #field-help {
		border: 1px dashed #000;
		cursor: default;
		font-size: 11px;
		font-weight: normal;
		text-align: center;
	}
	#field-help {
		padding: 10px;
	}
	.field-name, li.ui-state-default, li.ui-state-highlight {
		border-radius: 5px;
	}
	.field-icon {
		background: url('view/javascript/ckeditor/skins/v2/icons.png');
		width: 16px;
		height: 16px;
		vertical-align: middle;
		margin: -3px 3px 0 -3px;
	}
	.rotated {
		-webkit-transform: rotate(90deg);
		-moz-transform: rotate(90deg);
		-ms-transform: rotate(90deg);
		-o-transform: rotate(90deg);
		transform: rotate(90deg);
	}
	.field-name {
		color: black;
		border: none;
		display: table-cell;
		padding: 10px 5px;
		min-width: 120px;
		text-align: center;
		vertical-align: middle;
		white-space: nowrap;
	}
	.field-name:hover {
		cursor: move;
	}
	.field-settings {
		display: table-cell;
		font-weight: normal;
		padding: 5px;
		width: 100%;
	}
	.field-settings > span {
		display: inline-block;
		padding: 2px 5px;
	}
	.hidden {
		display: none !important;
	}
	li.ui-state-highlight {
		height: 36px;
	}
	
	input[type="checkbox"]:hover {
		cursor: pointer;
	}
	.date, .time, .datetime {
		width: 140px;
	}
	.cke_skin_kama {
		display: inline-block;
		vertical-align: middle;
	}
</style>
<div id="content">
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	<div class="box">
		<div class="heading">
			<h1 style="padding: 10px 2px 0"><img src="view/image/length.png" alt="" style="vertical-align: middle" /> <?php echo $heading_title; ?></h1>
			<div class="buttons">
				<a class="button" onclick="save(true)"><?php echo $button_save_exit; ?></a>
				<a class="button" onclick="save(false)"><?php echo $button_save_keep_editing; ?></a>
				<a class="button" onclick="location = '<?php echo $exit; ?>'"><?php echo $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<ul id="field-types" class="fields ui-state-highlight">
				<li id="field-type-heading">
					Field Types
					<input type="button" style="float: right" onclick="save()" value="<?php echo $button_save_keep_editing; ?>" />
					<input type="button" style="float: right; margin-right: 10px" onclick="toggleEditors()" value="<?php echo $button_toggle_ckeditors; ?>" />
					<div id="tester"></div>
				</li>
				<?php $field_types = array(
					'captcha'	=> array('name' => $text_captcha, 'pixels' => 1136),
					'checkbox'	=> array('name' => $text_checkboxes, 'pixels' => 768),
					'column'	=> array('name' => $text_column_break, 'pixels' => 672),
					'date'		=> array('name' => $text_date_time, 'pixels' => 608),
					'email'		=> array('name' => $text_email_address, 'pixels' => 1279),
					'file'		=> array('name' => $text_file_upload, 'pixels' => 32),
					'hidden'	=> array('name' => $text_hidden_data, 'pixels' =>  752),
					'html'		=> array('name' => $text_html_block, 'pixels' => 272),
					'radio'		=> array('name' => $text_radio_buttons, 'pixels' => 782),
					'row'		=> array('name' => $text_row_break, 'pixels' => 672),
					'select'	=> array('name' => $text_select_dropdown, 'pixels' => 624),
					'submit'	=> array('name' => $text_submit_button, 'pixels' => 848),
					'text'		=> array('name' => $text_text_input, 'pixels' => 799)
				); ?>
				<?php foreach ($field_types as $ft => $ft_data) { ?>
					<li class="draggable ui-state-default" <?php if ($ft == 'column' || $ft == 'row') echo 'style="background: #CCC"'; ?>>
						<div class="field-name ui-state-hover">
							<img class="field-icon <?php if ($ft == 'column') echo 'rotated'; ?>" src="view/javascript/ckeditor/images/spacer.gif" style="background-position: 0 -<?php echo $ft_data['pixels']; ?>px"><?php if ($ft != 'select') echo ' '; ?><?php echo $ft_data['name']; ?>
						</div>
						
						<div class="field-settings hidden">
							<?php if ($ft != 'date' && $ft != 'text') { ?>
								<input type="hidden" name="fields[#][type]" value="<?php echo $ft; ?>" />
							<?php } ?>
							
							<?php if ($ft != 'column' && $ft != 'row') { ?>
								<img class="help-icon" src="view/image/information.png" alt="<?php echo $ft; ?>-help" title="<?php echo $ft_data['name'] . ' ' . $text_help; ?>" />
								
								<?php if ($ft != 'html') { ?>
									<span><img src="view/image/country.png" title="<?php echo $text_global_settings; ?>" width="16" height="16" style="position: relative; top: 3px" /></span>
								<?php } ?>
								
								<?php if ($ft == 'captcha') { ?>
									<span><?php echo $text_required; ?>: <input type="checkbox" checked="checked" disabled="disabled" /><input type="hidden" name="fields[#][required]" value="1" /></span>
								<?php } elseif ($ft == 'hidden' || $ft == 'submit') { ?>
									<span><?php echo $text_required; ?>: <input type="checkbox" checked="checked" disabled="disabled" /><input type="hidden" name="fields[#][required]" value="0" /></span>
								<?php } elseif ($ft != 'html' && $ft != 'submit') { ?>
									<span><label><?php echo $text_required; ?>: <input type="checkbox" onclick="$(this).next().val($(this).is(':checked') ? 1 : 0)" /><input type="hidden" name="fields[#][required]" /></label></span>
								<?php } ?>
								
								<?php if ($ft != 'captcha' && $ft != 'html' && $ft != 'submit') { ?>
									<span><?php echo $text_key; ?>: <input class="field-key" type="text" name="fields[#][key]" /></span>
								<?php } ?>
								
								<?php if ($ft == 'date') { ?>
									<span><?php echo $text_type; ?>:
										<select name="fields[#][type]" onchange="$(this).parent().parent().find('.date, .time, .datetime').attr('class', $(this).val()); attachDatePickers()">
											<option value="date"><?php echo $text_date; ?></option>
											<option value="time"><?php echo $text_time; ?></option>
											<option value="datetime"><?php echo $text_date_and_time; ?></option>
										</select>
									</span>
								<?php } elseif ($ft == 'email') { ?>
									<span><label><?php echo $text_include_confirmation; ?>: <input type="checkbox" onclick="$(this).next().val($(this).is(':checked') ? 1 : 0)" /><input type="hidden" name="fields[#][confirm]" /></label></span>
								<?php } elseif ($ft == 'file') { ?>
									<span><?php echo $text_file_size_limit; ?>: <input type="text" name="fields[#][filesize]" /></span>
									<span><?php echo $text_allowed_extensions; ?>: <input type="text" name="fields[#][extensions]" /></span>
								<?php } elseif ($ft == 'hidden') { ?>
									<span><label><?php echo $text_display_in_email; ?>: <input type="checkbox" onclick="$(this).next().val($(this).is(':checked') ? 1 : 0)" /><input type="hidden" name="fields[#][email]" /></label></span>
								<?php } elseif ($ft == 'select') { ?>
									<span><?php echo $text_number_of_selections; ?>: <input type="text" size="1" name="fields[#][selections]" value="1" /></span>
								<?php } elseif ($ft == 'submit') { ?>
									<span><?php echo $text_redirect_on_success; ?>: <input type="text" style="width: 418px" name="fields[#][redirect]" /></span>
								<?php } elseif ($ft == 'text') { ?>
									<span><?php echo $text_type; ?>:
										<select name="fields[#][type]">
											<option value="text"><?php echo $text_text; ?></option>
											<option value="password"><?php echo $text_password; ?></option>
											<option value="textarea"><?php echo $text_textarea; ?></option>
										</select>
									</span>
									<span><?php echo $text_min_length; ?>: <input type="text" size="1" name="fields[#][min_length]" /></span>
									<span><?php echo $text_max_length; ?>: <input type="text" size="1" name="fields[#][max_length]" /></span>
								<?php } ?>
								
								<?php foreach ($languages as $language) { ?>
									<?php if ($ft == 'html') { ?>
										<div style="white-space: nowrap; margin: 5px 0">
											<span><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" style="position: relative; top: 1px" /></span>
											<span><textarea name="fields[#][html][<?php echo $language['code']; ?>]" style="vertical-align: middle"></textarea></span>
										</div>
									<?php } else { ?>
										<br />
										<span><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" style="position: relative; top: 1px" /></span>
										<?php if ($ft != 'html' && $ft != 'submit') { ?>
											<span><?php echo $text_name; ?>: <input type="text" name="fields[#][name][<?php echo $language['code']; ?>]" /></span>
										<?php } ?>
										<?php if ($ft == 'checkbox' || $ft == 'radio' || $ft == 'select') { ?>
											<span><?php echo $text_choices; ?>: <input type="text" name="fields[#][choices][<?php echo $language['code']; ?>]" /></span>
											<span><?php echo $text_default_value . ($ft != 'radio' ? $text_s : ''); ?>: <input type="text" name="fields[#][default][<?php echo $language['code']; ?>]" /></span>
										<?php } elseif ($ft == 'date') { ?>
											<span><?php echo $text_default_value; ?>: <input type="text" class="date" name="fields[#][default][<?php echo $language['code']; ?>]" /></span>
										<?php } elseif ($ft == 'email') { ?>
											<span><?php echo $text_confirm_field_name; ?>: <input type="text" name="fields[#][confirm_name][<?php echo $language['code']; ?>]" /></span>
										<?php } elseif ($ft == 'file') { ?>
											<span><?php echo $text_success_message; ?>: <input type="text" name="fields[#][success][<?php echo $language['code']; ?>]" /></span>
										<?php } elseif ($ft == 'hidden') { ?>
											<span><?php echo $text_data; ?>: <input type="text" name="fields[#][data][<?php echo $language['code']; ?>]" /></span>
										<?php } elseif ($ft == 'submit') { ?>
											<span><?php echo $text_button_text; ?>: <input type="text" name="fields[#][button][<?php echo $language['code']; ?>]" /></span>
											<span><?php echo $text_success_message; ?>: <input type="text" style="width: 300px" name="fields[#][success][<?php echo $language['code']; ?>]" /></span>
										<?php } elseif ($ft == 'text') { ?>
											<span><?php echo $text_default_value; ?>: <input type="text" name="fields[#][default][<?php echo $language['code']; ?>]" /></span>
											<span><?php echo $text_allowed_characters; ?>: <input type="text" name="fields[#][allowed][<?php echo $language['code']; ?>]" /></span>
										<?php } ?>
									<?php } ?>
								<?php } ?>
							<?php } ?>
							<img class="remove-icon" src="view/image/error.png" alt="Remove" title="Remove" <?php if ($ft == 'column' || $ft == 'row') echo 'style="margin-top: 0"'; ?> />
						</div>
					</li>
				<?php } ?>
				<li id="field-type-help" class="ui-state-default"><?php echo $text_field_type_help; ?></li>
			</ul>
			<div id="tabs" class="htabs">
				<a href="#tab-general"><?php echo $tab_general; ?></a>
				<a href="#tab-locations"><?php echo $tab_locations; ?></a>
				<a href="#tab-fields"><?php echo $tab_fields; ?></a>
				<a href="#tab-errors"><?php echo $tab_errors; ?></a>
				<a href="#tab-email"><?php echo $tab_email; ?></a>
				<input type="button" style="float: right; margin-right: 10px" onclick="toggleEditors()" value="<?php echo $button_toggle_ckeditors; ?>" />
			</div>
			<div id="form">
				<input type="hidden" name="form_id" value="<?php echo $form['form_id']; ?>" />
				
				<div id="tab-general">
					<table class="form">
						<tr>
							<td><?php echo $entry_status; ?></td>
							<td><select name="status">
									<option value="1" <?php if (!empty($form['status'])) echo 'selected="selected"'; ?>><?php echo $text_enabled; ?></option>
									<option value="0" <?php if (empty($form['status'])) echo 'selected="selected"'; ?>><?php echo $text_disabled; ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_form_name; ?></td>
							<td><table>
									<?php foreach ($languages as $language) { ?>
										<tr>
											<td><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></td>
											<td><input type="text" name="name[<?php echo $language['code']; ?>]" value="<?php echo (isset($form['name'][$language['code']])) ? $form['name'][$language['code']] : ''; ?>" /></td>
										</tr>
									<?php } ?>
								</table>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_password_required; ?></td>
							<td><input type="text" name="password[password]" value="<?php echo (isset($form['password']['password'])) ? $form['password']['password'] : ''; ?>" /></td>
						</tr>
						<tr>
							<td><?php echo $entry_enter_password; ?></td>
							<td><table>
									<?php foreach ($languages as $language) { ?>
										<tr>
											<td><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></td>
											<td><input type="text" name="password[message][<?php echo $language['code']; ?>]" value="<?php echo (isset($form['password']['message'][$language['code']])) ? $form['password']['message'][$language['code']] : ''; ?>" /></td>
										</tr>
									<?php } ?>
								</table>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_password_overlay; ?></td>
							<td><input type="text" style="width: 22px" name="password[opacity]" value="<?php echo (isset($form['password']['opacity'])) ? $form['password']['opacity'] : '80'; ?>" /> %</td>
						</tr>
					</table>
				</div> <!-- #tab-general -->
				
				<div id="tab-locations">
					<div class="help"><?php echo $help_locations; ?></div>
					<table class="list">
					<thead>
						<tr>
							<td class="center"><?php echo $column_status; ?></td>
							<td class="center"><?php echo $column_display; ?></td>
							<td class="center"><?php echo $column_stores; ?></td>
							<td class="center"><?php echo $column_location; ?></td>
							<td class="center"></td>
						</tr>
					</thead>
					<tbody>
					<?php $row = 0; ?>
					<?php foreach ($modules as $module) { ?>
						<?php $module_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "form WHERE form_id = " . (int)$module['form_id']); ?>
						<?php if ($row && !$module_query->num_rows) continue; ?>
						<tr <?php if ($row == 0 || $module['form_id'] != $form['form_id']) echo 'style="display: none"'; ?>>
							<td class="center">
								<input type="hidden" name="module[<?php echo $row; ?>][form_id]" value="<?php echo ($row == 0) ? $form['form_id'] : $module['form_id']; ?>" />
								<select name="module[<?php echo $row; ?>][status]">
									<option value="1" <?php if (!empty($module['status'])) echo 'selected="selected"'; ?>><?php echo $text_enabled; ?></option>
									<option value="0" <?php if (empty($module['status'])) echo 'selected="selected"'; ?>><?php echo $text_disabled; ?></option>
								</select>
							</td>
							<td class="center">
								<div class="iblock-r">
									<?php echo $text_module_box_class; ?><br />
									<?php echo $text_module_heading_class; ?><br />
									<?php echo $text_module_content_class; ?><br />
									<?php echo $text_hide_css_selectors; ?><br />
								</div>
								<div class="iblock-l">
									<input type="text" name="module[<?php echo $row; ?>][box]" value="<?php echo (isset($module['box'])) ? $module['box'] : 'box'; ?>" /><br />
									<input type="text" name="module[<?php echo $row; ?>][heading]" value="<?php echo (isset($module['heading'])) ? $module['heading'] : 'box-heading'; ?>" /><br />
									<input type="text" name="module[<?php echo $row; ?>][content]" value="<?php echo (isset($module['content'])) ? $module['content'] : 'box-content'; ?>" /><br />
									<input type="text" name="module[<?php echo $row; ?>][css]" value="<?php echo (isset($module['css'])) ? $module['css'] : ''; ?>" /><br />
								</div>
							</td>
							<td class="center">
								<div class="scrollbox">
									<?php foreach ($stores as $store) { ?>
										<?php $checked = (isset($module['stores']) && in_array($store['store_id'], $module['stores']) !== false); ?>
										<label><div>
											<input type="checkbox" value="<?php echo $store['store_id']; ?>" onchange="$(this).is(':checked') ? $(this).attr('name', 'module[<?php echo $row; ?>][stores][]') : $(this).removeAttr('name')" <?php if ($row == 0 || $checked) echo 'name="module[' . $row . '][stores][]" checked="checked"'; ?> />
											<?php echo $store['name']; ?>
										</div></label>
									<?php } ?>
								</div>
								<div><a onclick="$(this).parent().prev().find(':checkbox').attr('checked', 'checked')"><?php echo $text_select_all; ?></a>
									/
									<a onclick="$(this).parent().prev().find(':checkbox').removeAttr('checked')"><?php echo $text_unselect_all; ?></a>
								</div>
							</td>
							<td class="center">
								<div class="iblock-r">
									<?php echo $text_layout; ?><br />
									<?php echo $text_position; ?><br />
									<?php echo $text_sort_order; ?><br />
								</div>
								<div class="iblock-l">
									<select name="module[<?php echo $row; ?>][layout_id]">
										<?php foreach ($layouts as $layout) { ?>
											<option value="<?php echo $layout['layout_id']; ?>" <?php if (isset($module['layout_id']) && $module['layout_id'] == $layout['layout_id']) echo 'selected="selected"'; ?>><?php echo $layout['name']; ?></option>
										<?php } ?>
									</select><br />
									<select name="module[<?php echo $row; ?>][position]">
										<?php foreach ($positions as $position) { ?>
											<option value="<?php echo $position; ?>" <?php if (isset($module['position']) && $module['position'] == $position) echo 'selected="selected"'; ?>><?php echo ${'standard_'.$position}; ?></option>
										<?php } ?>
									</select><br />
									<input type="text" size="1" name="module[<?php echo $row; ?>][sort_order]" value="<?php echo (isset($module['sort_order'])) ? $module['sort_order'] : '1'; ?>" /><br />
								</div>
							</td>
							<td class="center" style="width: 1px">
								<a onclick="$(this).parent().parent().remove()"><img src="view/image/error.png" title="Remove" /></a>
							</td>
						</tr>
						<?php $row++; ?>
					<?php } ?>
					</tbody>
					<tfoot>
						<tr>
							<td class="left" colspan="7">
								<a onclick="addRow()" class="button"><?php echo $button_add_module; ?></a>
								&nbsp;
								<a onclick="createFormPage()" class="button"><?php echo $button_create_form_page; ?></a>
							</td>
						</tr>
					</tfoot>
					</table>
					<table class="form">
						<tr>
							<td><?php echo $entry_nonstandard; ?></td>
							<td><input type="text" class="embed-code" readonly="readonly" onclick="this.select()" value="<?php echo (empty($form['form_id'])) ? $text_this_will_appear : '<?php echo $this->getChild(\'' . $type . '/' . $name . '/form_' . $form['form_id'] . '\'); ?>'; ?>" /></td>
						</tr>
						<tr>
							<td colspan="2">
								<span class="help">
									<?php echo $help_nonstandard; ?><br /><br />
									<span style="font-family: monospace">
										&lt;div class="box"&gt;<br />
											&nbsp; &nbsp; &lt;div class="box-heading"&gt; <?php echo $heading_title; ?> &lt;/div&gt;<br />
											&nbsp; &nbsp; &lt;div class="box-content"&gt;
												<?php echo (empty($form['form_id'])) ? $text_this_will_appear : '&lt;?php echo $this->getChild(\'' . $type . '/' . $name . '/form_' . $form['form_id'] . '\'); ?&gt;'; ?>
											&lt;/div&gt;<br />
										&lt;/div&gt;
									</span>
								</span>
							</td>
						</tr>
					</table>
				</div> <!-- #tab-locations -->
				
				<?php $field_types['time'] = $field_types['datetime'] = $field_types['date']; ?>
				<?php $field_types['password'] = $field_types['textarea'] = $field_types['text']; ?>
				
				<div id="tab-fields">
					<ul id="fields" class="fields">
						<?php if (empty($form['fields'])) { ?>
							<li id="field-help" class="ui-state-default"><?php echo $text_field_help; ?></li>
						<?php } else { ?>
							<?php $num = 1; ?>
							<?php foreach ($form['fields'] as $field) { ?>
								<?php $ft = $field['type']; ?>
								
								<li class="draggable ui-state-default" <?php if ($ft == 'column' || $ft == 'row') echo 'style="background: #CCC"'; ?>>
									<div class="field-name ui-state-hover"><img class="field-icon" src="view/javascript/ckeditor/images/spacer.gif" style="background-position: 0 -<?php echo $field_types[$ft]['pixels']; ?>px"><?php if ($ft != 'select') echo ' '; ?><?php echo $field_types[$ft]['name']; ?></div>
									<div class="field-settings">
										
										<?php if ($ft != 'date' && $ft != 'time' && $ft != 'datetime' && $ft != 'text' && $ft != 'password' && $ft != 'textarea') { ?>
											<input type="hidden" name="fields[<?php echo $num; ?>][type]" value="<?php echo $ft; ?>" />
										<?php } ?>
										
										<?php if ($ft != 'column' && $ft != 'row') { ?>
											<img class="help-icon" src="view/image/information.png" alt="<?php echo $ft; ?>-help" title="<?php echo $field_types[$ft]['name'] . ' ' . $text_help; ?>" />
											
											<?php if ($ft != 'html') { ?>
												<span><img src="view/image/country.png" title="<?php echo $text_global_settings; ?>" width="16" height="16" style="position: relative; top: 3px" /></span>
											<?php } ?>
											
											<?php if ($ft == 'captcha') { ?>
												<span><?php echo $text_required; ?>: <input type="checkbox" checked="checked" disabled="disabled" /><input type="hidden" name="fields[<?php echo $num; ?>][required]" value="1" /></span>
											<?php } elseif ($ft == 'hidden' || $ft == 'submit') { ?>
												<span><?php echo $text_required; ?>: <input type="checkbox" checked="checked" disabled="disabled" /><input type="hidden" name="fields[<?php echo $num; ?>][required]" value="0" /></span>
											<?php } elseif ($ft != 'html' && $ft != 'submit') { ?>
												<span><label><?php echo $text_required; ?>: <input type="checkbox" onclick="$(this).next().val($(this).is(':checked') ? 1 : 0)" <?php if ($field['required']) echo 'checked="checked"'; ?> /><input type="hidden" name="fields[<?php echo $num; ?>][required]" value="<?php echo ($field['required']) ? 1 : 0; ?>" /></label></span>
											<?php } ?>
											
											<?php if ($ft != 'captcha' && $ft != 'html' && $ft != 'submit') { ?>
												<span><?php echo $text_key; ?>: <input class="field-key" type="text" name="fields[<?php echo $num; ?>][key]" value="<?php echo $field['key']; ?>" /></span>
											<?php } ?>
											
											<?php if ($ft == 'date' || $ft == 'time' || $ft == 'datetime') { ?>
												<span><?php echo $text_type; ?>:
													<select name="fields[<?php echo $num; ?>][type]" onchange="$(this).parent().parent().find('.date, .time, .datetime').attr('class', $(this).val()); attachDatePickers()">
														<option value="date" <?php if ($ft == 'date') echo 'selected="selected"'; ?>><?php echo $text_date; ?></option>
														<option value="time" <?php if ($ft == 'time') echo 'selected="selected"'; ?>><?php echo $text_time; ?></option>
														<option value="datetime" <?php if ($ft == 'datetime') echo 'selected="selected"'; ?>><?php echo $text_date_and_time; ?></option>
													</select>
												</span>
											<?php } elseif ($ft == 'email') { ?>
												<span><label><?php echo $text_include_confirmation; ?>: <input type="checkbox" onclick="$(this).next().val($(this).is(':checked') ? 1 : 0)" <?php if ($field['confirm']) echo 'checked="checked"'; ?> /><input type="hidden" name="fields[<?php echo $num; ?>][confirm]" value="<?php echo ($field['confirm']) ? 1 : 0; ?>" /></label></span>
											<?php } elseif ($ft == 'file') { ?>
												<span><?php echo $text_file_size_limit; ?>: <input type="text" name="fields[<?php echo $num; ?>][filesize]" value="<?php echo $field['filesize']; ?>" /></span>
												<span><?php echo $text_allowed_extensions; ?>: <input type="text" name="fields[<?php echo $num; ?>][extensions]" value="<?php echo $field['extensions']; ?>" /></span>
											<?php } elseif ($ft == 'hidden') { ?>
												<span><label><?php echo $text_display_in_email; ?>: <input type="checkbox" onclick="$(this).next().val($(this).is(':checked') ? 1 : 0)" <?php if ($field['email']) echo 'checked="checked"'; ?> /><input type="hidden" name="fields[<?php echo $num; ?>][email]" value="<?php echo ($field['email']) ? 1 : 0; ?>" /></label></span>
											<?php } elseif ($ft == 'select') { ?>
												<span><?php echo $text_number_of_selections; ?>: <input type="text" size="1" name="fields[<?php echo $num; ?>][selections]" value="<?php echo $field['selections']; ?>" /></span>
											<?php } elseif ($ft == 'submit') { ?>
												<span><?php echo $text_redirect_on_success; ?>: <input type="text" style="width: 418px" name="fields[<?php echo $num; ?>][redirect]" value="<?php echo $field['redirect']; ?>" /></span>
											<?php } elseif ($ft == 'text' || $ft == 'password' || $ft == 'textarea') { ?>
												<span><?php echo $text_type; ?>:
													<select name="fields[<?php echo $num; ?>][type]">
														<option value="text" <?php if ($ft == 'text') echo 'selected="selected"'; ?>><?php echo $text_text; ?></option>
														<option value="password" <?php if ($ft == 'password') echo 'selected="selected"'; ?>><?php echo $text_password; ?></option>
														<option value="textarea" <?php if ($ft == 'textarea') echo 'selected="selected"'; ?>><?php echo $text_textarea; ?></option>
													</select>
												</span>
												<span><?php echo $text_min_length; ?>: <input type="text" size="1" name="fields[<?php echo $num; ?>][min_length]" value="<?php echo $field['min_length']; ?>" /></span>
												<span><?php echo $text_max_length; ?>: <input type="text" size="1" name="fields[<?php echo $num; ?>][max_length]" value="<?php echo $field['max_length']; ?>" /></span>
											<?php } ?>
											
											<?php foreach ($languages as $language) { ?>
												<?php if ($ft == 'html') { ?>
													<div style="white-space: nowrap; margin: 5px 0">
														<span><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" style="position: relative; top: 1px" /></span>
														<span><textarea name="fields[<?php echo $num; ?>][html][<?php echo $language['code']; ?>]" style="vertical-align: middle"><?php echo $field['html'][$language['code']]; ?></textarea></span>
													</div>
												<?php } else { ?>
													<br />
													<span><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" style="position: relative; top: 1px" /></span>
													<?php if ($ft != 'html' && $ft != 'submit') { ?>
														<span><?php echo $text_name; ?>: <input type="text" name="fields[<?php echo $num; ?>][name][<?php echo $language['code']; ?>]" value="<?php echo $field['name'][$language['code']]; ?>" /></span>
													<?php } ?>
													<?php if ($ft == 'checkbox' || $ft == 'radio' || $ft == 'select') { ?>
														<span><?php echo $text_choices; ?>: <input type="text" name="fields[<?php echo $num; ?>][choices][<?php echo $language['code']; ?>]" value="<?php echo $field['choices'][$language['code']]; ?>" /></span>
														<span><?php echo $text_default_value . ($ft != 'radio' ? $text_s : ''); ?>: <input type="text" name="fields[<?php echo $num; ?>][default][<?php echo $language['code']; ?>]" value="<?php echo $field['default'][$language['code']]; ?>" /></span>
													<?php } elseif ($ft == 'date' || $ft == 'time' || $ft == 'datetime') { ?>
														<span><?php echo $text_default_value; ?>: <input type="text" class="<?php echo $ft; ?>" name="fields[<?php echo $num; ?>][default][<?php echo $language['code']; ?>]" value="<?php echo $field['default'][$language['code']]; ?>" /></span>
													<?php } elseif ($ft == 'email') { ?>
														<span><?php echo $text_confirm_field_name; ?>: <input type="text" name="fields[<?php echo $num; ?>][confirm_name][<?php echo $language['code']; ?>]" value="<?php echo $field['confirm_name'][$language['code']]; ?>" /></span>
													<?php } elseif ($ft == 'file') { ?>
														<span><?php echo $text_success_message; ?>: <input type="text" name="fields[<?php echo $num; ?>][success][<?php echo $language['code']; ?>]" value="<?php echo $field['success'][$language['code']]; ?>" /></span>
													<?php } elseif ($ft == 'hidden') { ?>
														<span><?php echo $text_data; ?>: <input type="text" name="fields[<?php echo $num; ?>][data][<?php echo $language['code']; ?>]" value="<?php echo $field['data'][$language['code']]; ?>" /></span>
													<?php } elseif ($ft == 'submit') { ?>
														<span><?php echo $text_button_text; ?>: <input type="text" name="fields[<?php echo $num; ?>][button][<?php echo $language['code']; ?>]" value="<?php echo $field['button'][$language['code']]; ?>" /></span>
														<span><?php echo $text_success_message; ?>: <input type="text" style="width: 300px" name="fields[<?php echo $num; ?>][success][<?php echo $language['code']; ?>]" value="<?php echo $field['success'][$language['code']]; ?>" /></span>
													<?php } elseif ($ft == 'text' || $ft == 'password' || $ft == 'textarea') { ?>
														<span><?php echo $text_default_value; ?>: <input type="text" name="fields[<?php echo $num; ?>][default][<?php echo $language['code']; ?>]" value="<?php echo $field['default'][$language['code']]; ?>" /></span>
														<span><?php echo $text_allowed_characters; ?>: <input type="text" name="fields[<?php echo $num; ?>][allowed][<?php echo $language['code']; ?>]" value="<?php echo $field['allowed'][$language['code']]; ?>" /></span>
													<?php } ?>
												<?php } ?>
											<?php } ?>
										<?php } ?>
										
										<img class="remove-icon" src="view/image/error.png" alt="Remove" title="Remove" <?php if ($ft == 'column' || $ft == 'row') echo 'style="margin-top: 0"'; ?> />
									</div>
								</li>
								
								<?php $num++; ?>
							<?php } ?>
						<?php } ?>
					</ul>
				</div> <!-- #tab-fields -->
				
				<div id="tab-errors">
					<?php $error_types = array('required', 'captcha', 'invalid_email', 'mismatch', 'minlength', 'file_name', 'file_size', 'file_ext', 'file_upload'); ?>
					<table class="form">
						<?php foreach ($error_types as $et) { ?>
							<tr>
								<td><?php echo ${'entry_'.$et.'_error'}; ?></td>
								<td><table>
										<?php foreach ($languages as $language) { ?>
											<tr>
												<td><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></td>
												<td><input type="text" name="errors[<?php echo $et; ?>][<?php echo $language['code']; ?>]" value="<?php echo (!empty($form['errors'][$et][$language['code']])) ? $form['errors'][$et][$language['code']] : ${'text_'.$et.'_error'}; ?>" /></td>
											</tr>
										<?php } ?>
									</table>
								</td>
							</tr>
						<?php } ?>
					</table>
				</div> <!-- #tab-errors -->
				
				<div id="tab-email">
					<table class="form">
						<tr>
							<td colspan="2"><?php echo $help_email_shortcodes; ?></td>
						</tr>
						<tr>
							<td><?php echo $entry_admin_email; ?></td>
							<td><input type="text" name="email[admin_email]" value="<?php echo (!empty($form['email']['admin_email'])) ? $form['email']['admin_email'] : $this->config->get('config_email'); ?>" /></td>
						</tr>
						<tr>
							<td><?php echo $entry_admin_subject; ?></td>
							<td><table>
									<?php foreach ($languages as $language) { ?>
										<tr>
											<td><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></td>
											<td><input type="text" name="email[admin_subject][<?php echo $language['code']; ?>]" value="<?php echo (!empty($form['email']['admin_subject'][$language['code']])) ? $form['email']['admin_subject'][$language['code']] : $text_admin_subject; ?>" /></td>
										</tr>
									<?php } ?>
								</table>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_admin_message; ?></td>
							<td><table>
									<?php foreach ($languages as $language) { ?>
										<tr>
											<td><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></td>
											<td><textarea name="email[admin_message][<?php echo $language['code']; ?>]"><?php echo (!empty($form['email']['admin_message'][$language['code']])) ? $form['email']['admin_message'][$language['code']] : $text_admin_message; ?></textarea></td>
										</tr>
									<?php } ?>
								</table>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_email_customer; ?></td>
							<td><select name="email[customer_email]">
									<option value="1" <?php if (!empty($form['email']['customer_email'])) echo 'selected="selected"'; ?>><?php echo $text_yes; ?></option>
									<option value="0" <?php if (empty($form['email']['customer_email'])) echo 'selected="selected"'; ?>><?php echo $text_no; ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_customer_subject; ?></td>
							<td><table>
									<?php foreach ($languages as $language) { ?>
										<tr>
											<td><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></td>
											<td><input type="text" name="email[customer_subject][<?php echo $language['code']; ?>]" value="<?php echo (!empty($form['email']['customer_subject'][$language['code']])) ? $form['email']['customer_subject'][$language['code']] : $text_customer_subject; ?>" /></td>
										</tr>
									<?php } ?>
								</table>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_customer_message; ?></td>
							<td><table>
									<?php foreach ($languages as $language) { ?>
										<tr>
											<td><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></td>
											<td><textarea name="email[customer_message][<?php echo $language['code']; ?>]"><?php echo (!empty($form['email']['customer_message'][$language['code']])) ? $form['email']['customer_message'][$language['code']] : $text_customer_message; ?></textarea></td>
										</tr>
									<?php } ?>
								</table>
							</td>
						</tr>
					</table>
				</div> <!-- #tab-email -->
			</div> <!-- #form -->
			<?php echo $copyright; ?>
		</div>
	</div>
</div>
<div style="display: none">
	<span class="captcha-help"><?php echo $help_required . $help_name; ?></span>
	<span class="checkbox-help"><?php echo $help_required . $help_key . $help_name . $help_choices . $help_default_values . $help_asterisk; ?></span>
	<span class="date-help time-help datetime-help"><?php echo $help_required . $help_key . $help_date . $help_name . $help_default_value . $help_asterisk; ?></span>
	<span class="email-help"><?php echo $help_required . $help_key . $help_name . $help_email . $help_asterisk; ?></span>
	<span class="file-help"><?php echo $help_required . $help_key . $help_name . $help_file . $help_asterisk; ?></span>
	<span class="hidden-help"><?php echo $help_required . $help_key . $help_name . $help_hidden . $help_asterisk; ?></span>
	<span class="html-help"><?php echo $help_html . $help_asterisk; ?></span>
	<span class="radio-help"><?php echo $help_required . $help_key . $help_name . $help_choices . $help_default_value . $help_asterisk; ?></span>
	<span class="select-help"><?php echo $help_required . $help_key . $help_select . $help_name . $help_choices . $help_default_values . $help_asterisk; ?></span>
	<span class="submit-help"><?php echo $help_required . $help_submit . $help_asterisk; ?></span>
	<span class="text-help password-help textarea-help"><?php echo $help_required . $help_key . $help_text . $help_name . $help_default_value . $help_allowed_characters . $help_asterisk; ?></span>
</div>
<script type="text/javascript"><!--
	function save(exit) {
		$('<div></div>').dialog({
			title: '<?php echo $text_saving; ?>',
			closeOnEscape: false,
			draggable: false,
			modal: true,
			resizable: false,
			open: function(event, ui) {
				$('.ui-dialog').css('padding', '0px');
				$('.ui-dialog-content').hide();
				$('.ui-dialog-titlebar-close').hide();
			}
		}).dialog('open');
		
		for (name in CKEDITOR.instances) CKEDITOR.instances[name].updateElement();
		
		$.ajax({
			type: 'POST',
			url: 'index.php?route=<?php echo $type; ?>/<?php echo $name; ?>/save&token=<?php echo $token; ?>',
			data: $('#form :input'),
			success: function(success) {
				var title = (success) ? '<?php echo $text_saved; ?>' : '<?php echo $standard_error; ?>';
				var delay = (success) ? 1000 : 2000;
				
				$('.ui-dialog-content').dialog('option', 'title', title);
				setTimeout(function(){
					$('.ui-dialog-content').dialog('close');
					if (success && exit) {
						location = '<?php echo str_replace('&amp;', '&', $exit); ?>';
					} else if (success && success != <?php echo $form['form_id']; ?>) {
						location = location + '&form_id=' + success;
					}
				}, delay);
			}
		});
	}
	
	function addRow() {
		var clone = $('.list > tbody > tr:first-child').clone();
		clone.html(clone.html().replace(/\[0\]/g, '[' + $('.list > tbody > tr').length + ']'));
		clone.find(':selected').removeAttr('selected');
		$('.list > tbody').append(clone).find('tr:last-child').show();
	}
	
	function createFormPage() {
		$('<div><?php echo $help_create_form_page; ?><br /><br /><strong><?php echo $help_enter_seo_keyword; ?> <input id="keyword" type="text" /><?php if (strpos(VERSION, '1.5.1') !== 0 && strpos(VERSION, '1.5.2') !== 0) echo '<br />' . $help_display_in_footer . '<input id="bottom" type="checkbox" checked="checked" />'; ?></strong></div>').dialog({
            modal: true,
			closeOnEscape: false,
			resizable: false,
			title: '<?php echo $button_create_form_page; ?>',
			width: 575,
			open: function(event, ui) {
				$('.ui-dialog-titlebar-close').hide();
		        $('#keyword').val($('input[name^="name"]').val().replace(/[^a-zA-Z0-9 -]/g, '').replace(/ /g, '-').toLowerCase());
			},
            buttons: {
                '<?php echo $button_create_form_page; ?>': function() {
					var data = {
						name: $('input[name^="name"]').serializeArray(),
						keyword: $('#keyword').val(),
						bottom: ($('#bottom').is(':checked') ? 1 : 0)
					};
					$('.ui-dialog-content').html('<?php echo $text_creating; ?>');
					$('.ui-dialog-buttonpane').hide();
					$.ajax({
						type: 'POST',
						url: 'index.php?route=<?php echo $type; ?>/<?php echo $name; ?>/createFormPage&token=<?php echo $token; ?>',
						data: data,
						dataType: 'json',
						success: function(json) {
							if (json) {
								$('.ui-dialog-content').html('<?php echo $text_success; ?>');
								addRow();
								$('.list tr:last-child input[type="text"]').val('');
								$('.list tr:last-child input[name$="[css]"]').val('.buttons');
								$('.list tr:last-child select[name$="[layout_id]"]').append('<option value="' + json['layout_id'] + '" selected="selected">' + json['layout_name'] + '</option>');
								$('.list tr:last-child option[value="content_bottom"]').attr('selected', 'selected');
							} else {
								$('.ui-dialog-content').html('<?php echo $text_failed; ?>');
							}
							setTimeout(function(){
								$('.ui-dialog-content').dialog('close');
							}, 1500)
						}
					});
                },
                Cancel: function() {
                    $(this).dialog('close');
                }
            }
		});
	}
	
	$(document).ready(function(){
		// Tabs
		$('#tabs a').tabs();
		
		$('#tabs a').click(function(){
			if ($('#tab-fields').is(':visible')) {
				$('#field-types').slideDown('fast');
			} else {
				$('#field-types').slideUp('fast');
			}
		});
		
		// Date Pickers
		attachDatePickers();
		
		// Icons
		$('.fields .help-icon').live('click', function(){
			$('<div></div>').html($('.' + $(this).attr('alt')).html()).dialog({title: $(this).parent().prev().html() + ' <?php echo $text_help; ?>', width: 575, maxHeight: 575}).dialog('open');
		});
		$('.remove-icon').live('click', function(){
			$(this).parent().parent().remove();
		});
		
		// "Key" Fields
		$('.field-key').live('keypress', function(e) {
			if (('abcdefghijklmnopqrstuvwxyz01234567890').indexOf(String.fromCharCode(e.which).toLowerCase()) == -1) {
				e.preventDefault();
			}
		});
		
		// Fields
		$('#field-types li.draggable').draggable({
			connectToSortable: '#fields',
			cursor: 'move',
			helper: 'clone',
			start: function(event, ui) {
				ui.helper.css('box-shadow', '0 2px 4px #888');
			}
		});
        $('#fields').sortable({
			axis: 'y',
			cursor: 'move',
			cursorAt: {left: 10, top: 10},
			handle: '.field-name',
            placeholder: 'ui-state-highlight',
			receive: function(event, ui) {
				$(this).data().sortable.currentItem.html($(this).data().sortable.currentItem.html().replace(/\[#\]/g, '[' + $('.field-name').length + ']'));
				attachDatePickers();
				if ($.cookie('ckeditor') != 'disabled') {
					for (name in CKEDITOR.instances) CKEDITOR.instances[name].destroy();
					toggleEditors();
				}
			},
			start: function(event, ui) {
				ui.item.find('.field-settings').addClass('hidden');
				ui.item.css('box-shadow', '0 2px 4px #666');
				ui.item.height(34);
				$(document).mousemove(function(e) {
					if (e.pageY - $(document).scrollTop() < 100) $(document).scrollTop($(document).scrollTop() - 25);
					if (e.pageY - $(document).scrollTop() > $(window).height() - 100) $(document).scrollTop($(document).scrollTop() + 25);
				});
			},
			stop: function(event, ui) {
				ui.item.find('.field-settings').removeClass('hidden');
				ui.item.css('box-shadow', '');
				ui.item.height('auto');
				$('#field-help').hide();
				$(document).unbind('mousemove');
			}
        });
		$('.field-name, #field-type-help, #field-help').disableSelection();
		
		// CKEditors
		if ($.cookie('ckeditor') != 'disabled') {
			toggleEditors();
		}
	});
	
	function toggleEditors() {
		if ($.isEmptyObject(CKEDITOR.instances)) {
			$.cookie('ckeditor', 'enabled', {expires: 365});
			CKEDITOR.replaceAll(function(textarea, config) {
				if (textarea.getAttribute('name').indexOf('#') != -1) return false;
				config.height = '100px';
				config.width = '590px';
				config.resize_enabled = true;
				config.filebrowserBrowseUrl = 'index.php?route=common/filemanager&token=<?php echo $token; ?>';
				config.filebrowserImageBrowseUrl = 'index.php?route=common/filemanager&token=<?php echo $token; ?>';
				config.filebrowserFlashBrowseUrl = 'index.php?route=common/filemanager&token=<?php echo $token; ?>';
				config.filebrowserUploadUrl = 'index.php?route=common/filemanager&token=<?php echo $token; ?>';
				config.filebrowserImageUploadUrl = 'index.php?route=common/filemanager&token=<?php echo $token; ?>';
				config.filebrowserFlashUploadUrl = 'index.php?route=common/filemanager&token=<?php echo $token; ?>';
			});
		} else {
			$.cookie('ckeditor', 'disabled', {expires: 365});
			for (name in CKEDITOR.instances) CKEDITOR.instances[name].destroy();
		}
	}
	
	function attachDatePickers() {
		$('.date').removeClass('hasDatepicker').removeAttr('id').datepicker({
			dateFormat: 'yy-mm-dd'
		});
		$('.time').removeClass('hasDatepicker').removeAttr('id').timepicker({
			timeFormat: 'h:mm tt',
			ampm: true
		});
		$('.datetime').removeClass('hasDatepicker').removeAttr('id').datetimepicker({
			dateFormat: 'yy-mm-dd',
			timeFormat: 'h:mm tt',
			ampm: true,
			separator: ' @ '
		});
	}
//--></script>
<?php echo $footer; ?>