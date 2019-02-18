<?php
//==============================================================================
// Smart Search v154.2
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================
?>

<?php echo $header; ?>
<style type="text/css">
	input[type="text"] {
		width: 40px;
	}
	.settings td {
		border: none;
	}
	.sub-table {
		width: 440px;
	}
	.sub-table td {
		font-size: 11px;
		text-align: center;
	}
	.ajax-setting {
		width: 20%;
	}
	.list {
		width: 160px;
	}
	.list img {
		vertical-align: middle;
	}
	.color {
		padding: 5px !important;
		width: 55px !important;
	}
	.tooltip-mark {
		background: #FF8;
		border: 1px solid #888;
		border-radius: 10px;
		color: #000;
		font-size: 10px;
		margin-right: 5px;
		padding: 1px 4px;
	}
	.tooltip {
		background: #FFC;
		border: 1px solid #CCC;
		color: #000;
		display: none;
		font-size: 11px;
		font-weight: normal;
		line-height: 1.3;
		margin-left: 15px;
		margin-top: -30px;
		max-width: 300px;
		padding: 10px;
		position: absolute;
		text-align: left;
		z-index: 100;
		box-shadow: 0 6px 9px #AAA;
		-moz-box-shadow: 0 6px 9px #AAA;
		-webkit-box-shadow: 0 6px 9px #AAA;
	}
	.tooltip-mark:hover, .tooltip-mark:hover + .tooltip, .tooltip:hover {
		display: inline;
		cursor: help;
	}
</style>
<?php if (!$v14x) { ?>
<div id="content">
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
<?php } ?>
<?php if ($error_warning) { ?><div class="warning"><?php echo $error_warning; ?></div><?php } ?>
<?php if ($success) { ?><div class="success"><?php echo $success; ?></div><?php } ?>
<div class="box">
	<?php if ($v14x) { ?><div class="left"></div><div class="right"></div><?php } ?>
	<div class="heading">
		<h1 style="padding: 10px 2px 0"><img src="view/image/<?php echo $type; ?>.png" alt="" style="vertical-align: middle" /> <?php echo $heading_title; ?></h1>
		<div class="buttons">
			<a onclick="$('#form').attr('action', location + '&exit=true'); $('#form').submit()" class="button"><span><?php echo $button_save_exit; ?></span></a>
			<a onclick="$('#form').submit()" class="button"><span><?php echo $button_save_keep_editing; ?></span></a>
			<a onclick="location = '<?php echo $exit; ?>'" class="button"><span><?php echo $button_cancel; ?></span></a>
		</div>
	</div>
	<div class="content">
		<form action="" method="post" enctype="multipart/form-data" id="form">
			<table class="form">
				<tr style="background: #E4EEF7">
					<td colspan="2"><span class="help" style="line-height: 1.5; color: #333"><?php echo $entry_smartsearch_explanation; ?></span></td>
				</tr>
				<tr>
					<td style="width: 300px"><?php echo $entry_status; ?></td>
					<td><a class="button" style="float: right" href="<?php echo HTTPS_SERVER . 'index.php?route=report/' . $name . '&token=' . $token; ?>"><span><?php echo $button_view_report; ?></span></a>
						<select name="<?php echo $name; ?>_status">
							<?php $status = (isset(${$name.'_status'})) ? ${$name.'_status'} : 1; ?>
							<option value="1" <?php if ($status) echo 'selected="selected"'; ?>><?php echo $text_enabled; ?></option>
							<option value="0" <?php if (!$status) echo 'selected="selected"'; ?>><?php echo $text_disabled; ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td><?php echo $entry_product_fields_searched; ?></td>
					<td><div style="display: inline-block; width: 200px">
							<?php foreach (array('description', 'description_misspelled', 'meta_description', 'meta_keyword', 'tag', 'model', 'sku', 'upc') as $field) { ?>
								<label><input type="checkbox" name="<?php echo $name; ?>_data[<?php echo $field; ?>]" value="1" <?php if (!empty(${$name.'_data'}[$field])) echo 'checked="checked"'; ?> />
								<?php echo ${'text_'.$field}; ?></label>
								<br />
							<?php } ?>
						</div>
						<div style="display: inline-block; width: 200px; vertical-align: top">
							<?php foreach (array('location', 'manufacturer', 'attribute_group', 'attribute_name', 'attribute_value', 'option_name', 'option_value') as $field) { ?>
								<label><input type="checkbox" name="<?php echo $name; ?>_data[<?php echo $field; ?>]" value="1" <?php if (!empty(${$name.'_data'}[$field])) echo 'checked="checked"'; ?> />
								<?php echo ${'text_'.$field}; ?></label>
								<br />
							<?php } ?>
						</div>
					</td>
				</tr>
				<tr>
					<td><?php echo $entry_search_options; ?></td>
					<td><table class="settings">
						<tr>
							<td style="width: 1px"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_index_database_tables; ?></span></td>
							<td><a onclick="indexTables()" class="button"><span><?php echo $button_index_database_tables; ?></span></a></td>
							<td><img id="loading-index-tables" src="view/image/loading.gif" style="display: none" /></td>
						</tr>
						<tr>
							<td style="width: 1px"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_include_partial_word; ?></span></td>
							<td><?php echo $text_include_partial_word; ?></td>
							<td><select name="<?php echo $name; ?>_data[partials]">
									<?php $partials = (isset(${$name.'_data'}['partials'])) ? ${$name.'_data'}['partials'] : 1; ?>
									<option value="1" <?php if ($partials) echo 'selected="selected"'; ?>><?php echo $text_yes; ?></option>
									<option value="0" <?php if (!$partials) echo 'selected="selected"'; ?>><?php echo $text_no; ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="width: 1px"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_account_for_plurals; ?></span></td>
							<td><?php echo $text_account_for_plurals; ?></td>
							<td><select name="<?php echo $name; ?>_data[plurals]">
									<?php $plurals = (isset(${$name.'_data'}['plurals'])) ? ${$name.'_data'}['plurals'] : 1; ?>
									<option value="1" <?php if ($plurals) echo 'selected="selected"'; ?>><?php echo $text_yes; ?></option>
									<option value="0" <?php if (!$plurals) echo 'selected="selected"'; ?>><?php echo $text_no; ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="width: 1px"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_search_in_subcategories; ?></span></td>
							<td><?php echo $text_search_in_subcategories; ?></td>
							<td><select name="<?php echo $name; ?>_data[subcategories]">
									<?php $subcategories = (isset(${$name.'_data'}['subcategories'])) ? ${$name.'_data'}['subcategories'] : 1; ?>
									<option value="1" <?php if ($subcategories) echo 'selected="selected"'; ?>><?php echo $text_yes; ?></option>
									<option value="0" <?php if (!$subcategories) echo 'selected="selected"'; ?>><?php echo $text_no; ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="width: 1px"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_phase_1_search; ?></span></td>
							<td><?php echo $text_phase_1_search; ?></td>
							<td><select name="<?php echo $name; ?>_data[phase1]">
									<?php $phase1 = (isset(${$name.'_data'}['phase1'])) ? ${$name.'_data'}['phase1'] : 'default'; ?>
									<option value="default" <?php if ($phase1 == 'default') echo 'selected="selected"'; ?>><?php echo $text_run_by_itself; ?></option>
									<option value="combine" <?php if ($phase1 == 'combine') echo 'selected="selected"'; ?>><?php echo $text_combine_with_phase_2; ?></option>
									<option value="skip" <?php if ($phase1 == 'skip') echo 'selected="selected"'; ?>><?php echo $text_skip; ?></option>
								</select>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td><?php echo $entry_misspelling_settings; ?></td>
					<td><table class="settings">
						<tr>
							<td style="width: 1px"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_misspelling_tolerance; ?></span></td>
							<td><?php echo $text_misspelling_tolerance; ?></td>
							<td><input type="text" name="<?php echo $name; ?>_data[tolerance]" value="<?php echo (isset(${$name.'_data'}['tolerance'])) ? ${$name.'_data'}['tolerance'] : 75; ?>" /> %</td>
						</tr>
						<tr>
							<td style="width: 1px"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_use_cache; ?></span></td>
							<td><?php echo $text_use_cache; ?></td>
							<td><select name="<?php echo $name; ?>_data[usecache]" onchange="$(this).parent().parent().nextAll().toggle()">
									<?php $usecache = (isset(${$name.'_data'}['usecache'])) ? ${$name.'_data'}['usecache'] : 0; ?>
									<option value="0" <?php if (!$usecache) echo 'selected="selected"'; ?>><?php echo $text_no; ?></option>
									<option value="1" <?php if ($usecache) echo 'selected="selected"'; ?>><?php echo $text_yes; ?></option>
								</select>
							</td>
						</tr>
						<tr <?php if (!$usecache) echo 'style="display: none"'; ?>>
							<td style="width: 1px"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_auto_refresh_cache; ?></span></td>
							<td><?php echo $text_auto_refresh_cache; ?></td>
							<td><select name="<?php echo $name; ?>_data[refresh_cache]">
									<?php $refresh_cache = (isset(${$name.'_data'}['refresh_cache'])) ? ${$name.'_data'}['refresh_cache'] : 3600; ?>
									<option value="3600" <?php if ($refresh_cache == '3600') echo 'selected="selected"'; ?>><?php echo $text_hourly; ?></option>
									<option value="86400" <?php if ($refresh_cache == '86400') echo 'selected="selected"'; ?>><?php echo $text_daily; ?></option>
									<option value="604800" <?php if ($refresh_cache == '604800') echo 'selected="selected"'; ?>><?php echo $text_weekly; ?></option>
									<option value="2592000" <?php if ($refresh_cache == '2592000') echo 'selected="selected"'; ?>><?php echo $text_monthly; ?></option>
									<option value="31536000" <?php if ($refresh_cache == '31536000') echo 'selected="selected"'; ?>><?php echo $text_yearly; ?></option>
								</select>
							</td>
						</tr>
						<tr <?php if (!$usecache) echo 'style="display: none"'; ?>>
							<td style="width: 1px"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_min_word_length; ?></span></td>
							<td><?php echo $text_min_word_length; ?></td>
							<td><input type="text" name="<?php echo $name; ?>_data[word_length]" value="<?php echo (isset(${$name.'_data'}['word_length'])) ? ${$name.'_data'}['word_length'] : 3; ?>" /></td>
						</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td><?php echo $entry_ajax_search_settings; ?></td>
					<td><table class="settings">
						<tr>
							<td style="width: 1px"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_ajax_search; ?></span></td>
							<td><?php echo $text_ajax_search; ?></td>
							<td><select name="<?php echo $name; ?>_data[ajax_search]">
									<?php $ajax_search = (isset(${$name.'_data'}['ajax_search'])) ? ${$name.'_data'}['ajax_search'] : 1; ?>
									<option value="1" <?php if ($ajax_search) echo 'selected="selected"'; ?>><?php echo $text_enabled; ?></option>
									<option value="0" <?php if (!$ajax_search) echo 'selected="selected"'; ?>><?php echo $text_disabled; ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="width: 1px"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_selector; ?></span></td>
							<td><?php echo $text_selector; ?></td>
							<td style="text-align: center">
								<input type="text" style="width: 95%; font-family: monospace" name="<?php echo $name; ?>_data[ajax_selector]" value='<?php echo (isset(${$name.'_data'}['ajax_selector'])) ? ${$name.'_data'}['ajax_selector'] : ($v14x ? '#filter_keyword' : '#header input[name="filter_name"]'); ?>' />
							</td>
						</tr>
						<tr>
							<td style="width: 1px"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_display; ?></span></td>
							<td><?php echo $text_display; ?></td>
							<td><table class="sub-table">
								<tr>
									<td class="ajax-setting"><?php echo $text_delay; ?></td>
									<td class="ajax-setting"><?php echo $text_limit; ?></td>
									<td class="ajax-setting"><?php echo $text_price; ?></td>
									<td class="ajax-setting"><?php echo $text_model; ?></td>
									<td class="ajax-setting"><?php echo $text_description_ajax; ?></td>
								</tr>
								<tr>
									<td><input type="text" name="<?php echo $name; ?>_data[ajax_delay]" value="<?php echo (isset(${$name.'_data'}['ajax_delay'])) ? ${$name.'_data'}['ajax_delay'] : 500; ?>" /></td>
									<td><input type="text" name="<?php echo $name; ?>_data[ajax_limit]" value="<?php echo (isset(${$name.'_data'}['ajax_limit'])) ? ${$name.'_data'}['ajax_limit'] : 5; ?>" /></td>
									<td><select name="<?php echo $name; ?>_data[ajax_price]">
											<?php $ajax_price = (isset(${$name.'_data'}['ajax_price'])) ? ${$name.'_data'}['ajax_price'] : 1; ?>
											<option value="1" <?php if ($ajax_price) echo 'selected="selected"'; ?>><?php echo $text_show; ?></option>
											<option value="0" <?php if (!$ajax_price) echo 'selected="selected"'; ?>><?php echo $text_hide; ?></option>
										</select>
									</td>
									<td><select name="<?php echo $name; ?>_data[ajax_model]">
											<?php $ajax_model = (isset(${$name.'_data'}['ajax_model'])) ? ${$name.'_data'}['ajax_model'] : 0; ?>
											<option value="1" <?php if ($ajax_model) echo 'selected="selected"'; ?>><?php echo $text_show; ?></option>
											<option value="0" <?php if (!$ajax_model) echo 'selected="selected"'; ?>><?php echo $text_hide; ?></option>
										</select>
									</td>
									<td><input type="text" name="<?php echo $name; ?>_data[ajax_description]" value="<?php echo (isset(${$name.'_data'}['ajax_description'])) ? ${$name.'_data'}['ajax_description'] : 100; ?>" /></td>
								</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="width: 1px"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_sizes; ?></span></td>
							<td><?php echo $text_sizes; ?></td>
							<td><table class="sub-table">
								<tr>
									<td class="ajax-setting"><?php echo $text_dropdown_width; ?></td>
									<td class="ajax-setting"><?php echo $text_image_width; ?></td>
									<td class="ajax-setting"><?php echo $text_image_height; ?></td>
									<td class="ajax-setting"><?php echo $text_product_font_size; ?></td>
									<td class="ajax-setting"><?php echo $text_description_font_size; ?></td>
								</tr>
								<tr>
									<td><input type="text" name="<?php echo $name; ?>_data[ajax_width]" value="<?php echo (!empty(${$name.'_data'}['ajax_width'])) ? ${$name.'_data'}['ajax_width'] : 292; ?>" /></td>
									<td><input type="text" name="<?php echo $name; ?>_data[ajax_image_width]" value="<?php echo (!empty(${$name.'_data'}['ajax_image_width'])) ? ${$name.'_data'}['ajax_image_width'] : 50; ?>" /></td>
									<td><input type="text" name="<?php echo $name; ?>_data[ajax_image_height]" value="<?php echo (!empty(${$name.'_data'}['ajax_image_height'])) ? ${$name.'_data'}['ajax_image_height'] : 50; ?>" /></td>
									<td><input type="text" name="<?php echo $name; ?>_data[ajax_product_font]" value="<?php echo (!empty(${$name.'_data'}['ajax_product_font'])) ? ${$name.'_data'}['ajax_product_font'] : 13; ?>" /></td>
									<td><input type="text" name="<?php echo $name; ?>_data[ajax_description_font]" value="<?php echo (!empty(${$name.'_data'}['ajax_description_font'])) ? ${$name.'_data'}['ajax_description_font'] : 11; ?>" /></td>
								</tr>
								</table>
						</tr>
						<tr>
							<td style="width: 1px"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_colors; ?></span></td>
							<td><?php echo $text_colors; ?></td>
							<td><table class="sub-table">
								<tr>
									<td><?php echo $text_background; ?></td>
									<td><?php echo $text_borders; ?></td>
									<td><?php echo $text_font; ?></td>
									<td><?php echo $text_highlight; ?></td>
									<td><?php echo $text_price; ?></td>
									<td><?php echo $text_special; ?></td>
								</tr>
								<tr>
									<td><input type="text" class="color {required: false, hash: true, pickerPosition: 'bottom', pickerFaceColor: '#444', pickerBorderColor:'#000'}" name="<?php echo $name; ?>_data[ajax_background_color]" value="<?php echo (isset(${$name.'_data'}['ajax_background_color'])) ? ${$name.'_data'}['ajax_background_color'] : '#FFFFFF'; ?>" /></td>
									<td><input type="text" class="color {required: false, hash: true, pickerPosition: 'bottom', pickerFaceColor: '#444', pickerBorderColor:'#000'}" name="<?php echo $name; ?>_data[ajax_borders_color]" value="<?php echo (isset(${$name.'_data'}['ajax_borders_color'])) ? ${$name.'_data'}['ajax_borders_color'] : '#EEEEEE'; ?>" /></td>
									<td><input type="text" class="color {required: false, hash: true, pickerPosition: 'bottom', pickerFaceColor: '#444', pickerBorderColor:'#000'}" name="<?php echo $name; ?>_data[ajax_font_color]" value="<?php echo (isset(${$name.'_data'}['ajax_font_color'])) ? ${$name.'_data'}['ajax_font_color'] : '#000000'; ?>" /></td>
									<td><input type="text" class="color {required: false, hash: true, pickerPosition: 'bottom', pickerFaceColor: '#444', pickerBorderColor:'#000'}" name="<?php echo $name; ?>_data[ajax_highlight_color]" value="<?php echo (isset(${$name.'_data'}['ajax_highlight_color'])) ? ${$name.'_data'}['ajax_highlight_color'] : '#EEFFFF'; ?>" /></td>
									<td><input type="text" class="color {required: false, hash: true, pickerPosition: 'bottom', pickerFaceColor: '#444', pickerBorderColor:'#000'}" name="<?php echo $name; ?>_data[ajax_price_color]" value="<?php echo (isset(${$name.'_data'}['ajax_price_color'])) ? ${$name.'_data'}['ajax_price_color'] : '#000000'; ?>" /></td>
									<td><input type="text" class="color {required: false, hash: true, pickerPosition: 'bottom', pickerFaceColor: '#444', pickerBorderColor:'#000'}" name="<?php echo $name; ?>_data[ajax_special_color]" value="<?php echo (isset(${$name.'_data'}['ajax_special_color'])) ? ${$name.'_data'}['ajax_special_color'] : '#FF0000'; ?>" /></td>
								</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="width: 1px"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_text; ?></span></td>
							<td><?php echo $text_text; ?></td>
							<td><table class="sub-table">
								<tr>
									<td></td>
									<td>"<?php echo $text_view_all_results; ?>"</td>
									<td>"<?php echo $text_no_results; ?>"</td>
								</tr>
							<?php foreach ($languages as $language) { ?>
								<?php $lcode = $language['code']; ?>
								<tr>
									<td><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></td>
									<td><input type="text" style="width: 90%" name="<?php echo $name; ?>_data[ajax_viewall][<?php echo $lcode; ?>]" value="<?php echo (isset(${$name.'_data'}['ajax_viewall']) && isset(${$name.'_data'}['ajax_viewall'][$lcode])) ? ${$name.'_data'}['ajax_viewall'][$lcode] : $text_view_all_results; ?>" /></td>
									<td><input type="text" style="width: 90%" name="<?php echo $name; ?>_data[ajax_noresults][<?php echo $lcode; ?>]" value="<?php echo (isset(${$name.'_data'}['ajax_noresults']) && isset(${$name.'_data'}['ajax_noresults'][$lcode])) ? ${$name.'_data'}['ajax_noresults'][$lcode] : $text_no_results; ?>" /></td>
								</tr>
							<?php } ?>
								</table>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td style="vertical-align: top"><?php echo $entry_pre_search_replacements; ?></td>
					<td><table class="list">
						<thead>
							<tr>
								<td class="center"><?php echo $text_replace; ?></td>
								<td class="center"><?php echo $text_with; ?></td>
								<td class="center">&nbsp; &nbsp;</td>
							</tr>
						</thead>
						<?php if (isset(${$name.'_data'}['replace_array'])) { ?>
						<?php for ($i = 0; $i < count(${$name.'_data'}['replace_array']); $i++) { ?>
							<tr>
								<td class="center"><input type="text" style="width: 100px" name="<?php echo $name; ?>_data[replace_array][]" value="<?php echo ${$name.'_data'}['replace_array'][$i]; ?>" /></td>
								<td class="center"><input type="text" style="width: 100px" name="<?php echo $name; ?>_data[with_array][]" value="<?php echo ${$name.'_data'}['with_array'][$i]; ?>" /></td>
								<td class="center"><a onclick="$(this).parent().parent().remove()"><img src="view/image/delete.png" title="Delete" /></a></td>
							</tr>
						<?php } ?>
						<?php } ?>
							<tr>
								<td class="left" colspan="3" style="background: none"><a onclick="addRow($(this))"><img src="view/image/add.png" title="Add" /></a></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</form>
		<?php echo $copyright; ?>
	</div>
</div>
<?php if (!$v14x) { ?>
	</div>
<?php } ?>
<script type="text/javascript" src="view/javascript/jscolor/jscolor.js"></script>
<script type="text/javascript"><!--
	function indexTables() {
		$('#loading-index-tables').show();
		$.ajax({
			type: 'POST',
			url: 'index.php?route=module/smartsearch/indexTables&token=<?php echo $token; ?>',
			success: function(data) {
				alert(data.replace(/(<([^>]+)>)/ig,""));
				$('#loading-index-tables').hide();
			}
		});
	}
	
	function addRow(element) {
		element.parent().parent().before('\
			<tr>\
				<td class="center"><input type="text" name="<?php echo $name; ?>_data[replace_array][]" /></td>\
				<td class="center"><input type="text" name="<?php echo $name; ?>_data[with_array][]" /></td>\
				<td class="center"><a onclick="$(this).parent().parent().remove()"><img src="view/image/delete.png" title="Delete" /></a></td>\
			</tr>\
		');
	}
//--></script>
<?php echo $footer; ?>