<?php
//==============================================================================
// Payment-Based Fee/Discount v155.1
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================
?>

<?php $header = str_replace('<script type="text/javascript" src="view/javascript/jquery/ui/ui.core.js"></script>', '<!--<script type="text/javascript" src="view/javascript/jquery/ui/ui.core.js"></script>-->', $header); ?>
<?php echo $header; ?>
<style type="text/css">
	div {
		white-space: nowrap;
	}
	.help, .tooltip, .scrollbox div, #examples div {
		white-space: normal;
	}
	.form td:first-child, .form td:nth-child(2) {
		width: 1px !important;
	}
	td {
		background: #FFF;
	}
	thead td {
		height: 24px;
	}
	.list > tbody > tr > td {
		vertical-align: top;
	}
	.list > tbody > tr:not(:last-child) > td:first-child {
		line-height: 0.9;
	}
	.scrollbox {
		margin: auto;
		min-height: 250px;
		min-width: 120px;
		text-align: left;
		width: 95%;
	}
	.alternating label:nth-child(even) div {
		background: #E4EEF7;
	}
	.selectall-links {
		font-size: 11px;
		padding: 0 0 8px !important;
		text-align: center;
	}
	.order-criteria-title {
		background: #E4EEF7;
		font-weight: bold;
		text-align: center;
	}
	.sub-table td {
		border: none;
		padding: 0 !important;
	}
	.sub-table input {
		margin: 1px;
	}
	.bluebox {
		background: #E4EEF7;
		border: 1px solid #BCD;
		font-weight: bold;
		margin-bottom: 5px;
		padding: 5px 10px;
		text-align: center;
	}
	textarea {
		font-family: monospace;
		width: 230px;
	}
	.sortable-arrow {
		height: 16px;
		width: 16px;
		background: url(view/javascript/jquery/ui/themes/base/images/ui-icons_222222_256x240.png) -128px -48px;
	}
	.sortable-arrow:hover {
		cursor: move;
	}
	.tooltip-mark {
		background: #FF8;
		border: 1px solid #888;
		border-radius: 10px;
		color: #000;
		font-size: 10px;
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
		max-width: 350px;
		padding: 10px;
		position: absolute;
		text-align: left;
		z-index: 100;
	}
	.tooltip-mark:hover, .tooltip-mark:hover + .tooltip, .tooltip:hover {
		display: inline;
		cursor: help;
	}
	.tooltip, .ui-dialog {
		box-shadow: 0 6px 9px #AAA;
		-moz-box-shadow: 0 6px 9px #AAA;
		-webkit-box-shadow: 0 6px 9px #AAA;
	}
	#examples {
		display: none;
	}
	#examples h2 {
		background: #DDD;
		border: 1px dashed #888;
		display: inline-block;
		margin: 10px;
		padding: 10px;
		text-align: center;
		width: 25%;
	}
	#examples h2:hover, #examples .selected {
		cursor: pointer;
		background: #FFF;
	}
	#examples ul {
		margin-top: 0;
	}
</style>
<?php if ($version > 149) { ?>
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
	<?php if ($version < 150) { ?><div class="left"></div><div class="right"></div><?php } ?>
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
			<table class="form" style="margin-bottom: -1px">
				<tr>
					<td><?php echo $entry_status; ?></td>
					<td><select name="<?php echo $name; ?>_status">
							<option value="1" <?php if (!empty(${$name.'_status'})) echo 'selected="selected"'; ?>><?php echo $text_enabled; ?></option>
							<option value="0" <?php if (empty(${$name.'_status'})) echo 'selected="selected"'; ?>><?php echo $text_disabled; ?></option>
						</select>
					</td>
					<td><span class="help"><?php echo $help_status; ?></span></td>
				</tr>
				<tr>
					<td><?php echo $entry_sort_order; ?></td>
					<td><input type="text" size="1" name="<?php echo $name; ?>_sort_order" value="<?php echo (!empty(${$name.'_sort_order'})) ? ${$name.'_sort_order'} : 2; ?>" /></td>
					<td><span class="help"><?php echo $help_sort_order; ?></span></td>
				</tr>
			<?php if ($type == 'shipping') { ?>
				<tr>
					<td><?php echo $entry_heading; ?></td>
					<td><?php foreach ($languages as $language) { ?>
							<?php $lcode = $language['code']; ?>
							<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
							<input type="text" size="30" name="<?php echo $name; ?>_heading[<?php echo $lcode; ?>]" value="<?php echo (!empty(${$name.'_heading'}[$lcode])) ? ${$name.'_heading'}[$lcode] : $heading_title; ?>" />
							<br />
						<?php } ?>
					</td>
					<td><span class="help"><?php echo $help_heading; ?></span></td>
				</tr>
			<?php } ?>
				<tr>
					<td><?php echo $entry_round_final_costs; ?></td>
					<td><input type="text" size="1" name="<?php echo $name; ?>_round" value="<?php echo (!empty(${$name.'_round'})) ? ${$name.'_round'} : ''; ?>" /></td>
					<td><span class="help"><?php echo $help_round_final_costs; ?></span></td>
				</tr>
			</table>
			<table class="list">
			<thead>
				<tr>
					<!-- Extension-specific -->
					<td class="center"><div style="float: right"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_extensions; ?></span></div> <?php echo $entry_extensions; ?></td>
					<!-- end -->
					<td class="center"><div style="float: right"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_general_settings; ?></span></div> <?php echo $entry_general_settings; ?></td>
					<td class="center"><div style="float: right"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_order_criteria; ?></span></div> <?php echo $entry_order_criteria; ?></td>
					<td class="center"><div style="float: right"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_cart_criteria; ?></span></div> <?php echo $entry_cart_criteria; ?></td>
					<td class="center"><div style="float: right"><span class="tooltip-mark">?</span> <span class="tooltip" style="right: 80px"><?php echo $help_cost_brackets; ?></span></div> <?php echo $entry_cost_brackets; ?></td>
					<td class="center"><span class="tooltip-mark">?</span> <span class="tooltip" style="right: 0"><?php echo $help_actions; ?></span></td>
				</tr>
			</thead>
			<tbody>
			<?php $row = 0;
			$rates = (!empty(${$name.'_data'})) ? ${$name.'_data'} : array('');
			foreach ($rates as $rate) { ?>
				<tr>
					<!-- Extension-specific -->
					<td class="center" style="background: #EEE">
						<div><strong><?php echo $text_internal_notes; ?></strong></div>
						<div><input type="text" name="<?php echo $name; ?>_data[<?php echo $row; ?>][notes]" value="<?php echo (!empty($rate['notes'])) ? $rate['notes'] : ''; ?>" /></div>
						<br />
						<div class="scrollbox alternating">
							<?php echo str_replace('#ROW#', $row, preg_replace('/value="(' . (!empty($rate['extensions']) ? implode('|', $rate['extensions']) : '\w+') . ')"/', 'value="$1" checked="checked"', $extension_checkboxes)); ?>
						</div>
						<?php echo $selectall_links; ?>
					</td>
					<td class="center">
						<div><strong><?php echo $text_title; ?></strong></div>
						<?php foreach ($languages as $language) { ?>
							<?php $lcode = $language['code']; ?>
							<div><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
								<input type="text" name="<?php echo $name; ?>_data[<?php echo $row; ?>][title][<?php echo $lcode; ?>]" value="<?php echo (!empty($rate['title']) && !empty($rate['title'][$lcode])) ? $rate['title'][$lcode] : ''; ?>" />
							</div>
						<?php } ?>
						<br />
					<!-- end -->
						<div><strong><?php echo $text_sort_order; ?></strong></div>
						<div><input type="text" size="1" name="<?php echo $name; ?>_data[<?php echo $row; ?>][sort_order]" value="<?php echo (isset($rate['sort_order'])) ? $rate['sort_order'] : 1; ?>" /></div>
						<br />
						<div><strong><?php echo $text_multi_rate_calculation; ?></strong></div>
						<div><select name="<?php echo $name; ?>_data[<?php echo $row; ?>][multi_rate_calculation]">
								<?php $multi_rate_calculation = (isset($rate['multi_rate_calculation'])) ? $rate['multi_rate_calculation'] : 'average'; ?>
								<option value="average" <?php if ($multi_rate_calculation == 'average') echo 'selected="selected"'; ?>><?php echo $text_average; ?></option>
								<option value="highest" <?php if ($multi_rate_calculation == 'highest') echo 'selected="selected"'; ?>><?php echo $text_highest; ?></option>
								<option value="lowest" <?php if ($multi_rate_calculation == 'lowest') echo 'selected="selected"'; ?>><?php echo $text_lowest; ?></option>
								<option value="sum" <?php if ($multi_rate_calculation == 'sum') echo 'selected="selected"'; ?>><?php echo $text_sum; ?></option>
							</select>
						</div>
						<br />
						<div><strong><?php echo $text_tax_class; ?></strong></div>
						<div><select name="<?php echo $name; ?>_data[<?php echo $row; ?>][tax_class_id]">
								<?php $tax_class_id = (isset($rate['tax_class_id'])) ? $rate['tax_class_id'] : 0; ?>
								<?php foreach ($tax_classes as $tax_class) { ?>
									<option value="<?php echo $tax_class['tax_class_id']; ?>" <?php if ($tax_class['tax_class_id'] == $tax_class_id) echo 'selected="selected"'; ?>><?php echo $tax_class['title']; ?></option>
								<?php } ?>
							</select>
						</div>
						<br />
					<?php if ($type == 'total') { ?>
						<div><strong><?php echo $text_geo_zone_comparison; ?></strong></div>
						<div><select name="<?php echo $name; ?>_data[<?php echo $row; ?>][geozone_comparison]">
								<?php $geozone_comparison = (isset($rate['geozone_comparison'])) ? $rate['geozone_comparison'] : 'shipping'; ?>
								<option value="shipping" <?php if ($geozone_comparison == 'shipping') echo 'selected="selected"'; ?>><?php echo $text_shipping_address; ?></option>
								<option value="payment" <?php if ($geozone_comparison == 'payment') echo 'selected="selected"'; ?>><?php echo $text_payment_address; ?></option>
							</select>
						</div>
						<br />
					<?php } ?>
						<div><strong><?php echo $text_value_for_total; ?></strong></div>
						<div><select name="<?php echo $name; ?>_data[<?php echo $row; ?>][total_value]">
								<?php $total_value = (isset($rate['total_value'])) ? $rate['total_value'] : 'subtotal'; ?>
								<option value="prediscounted" <?php if ($total_value == 'prediscounted') echo 'selected="selected"'; ?>><?php echo $text_prediscounted_subtotal; ?></option>
								<option value="subtotal" <?php if ($total_value == 'subtotal') echo 'selected="selected"'; ?>><?php echo $text_subtotal; ?></option>
								<option value="taxed" <?php if ($total_value == 'taxed') echo 'selected="selected"'; ?>><?php echo $text_taxed_subtotal; ?></option>
								<option value="total" <?php if ($total_value == 'total') echo 'selected="selected"'; ?>><?php echo $text_total; ?></option>
							</select>
						</div>
					</td>
					<td class="center">
						<div class="scrollbox" style="min-height: 290px">
							<?php foreach ($order_criteria as $oc) { ?>
								<div class="order-criteria-title"><?php echo ${'text_'.$oc.'s'}; ?></div>
								<div style="padding: 0">
									<?php foreach (${$oc.'s'} as $c) { ?>
										<?php $checked = (empty($rate[$oc.'s']) || in_array($c[$oc.'_id'], $rate[$oc.'s'])) ? 'checked="checked"' : ''; ?>
										<label><div><input class="default-checked" type="checkbox" name="<?php echo $name; ?>_data[<?php echo $row; ?>][<?php echo $oc.'s'; ?>][]" value="<?php echo $c[$oc.'_id']; ?>" <?php echo $checked; ?> /><?php echo $c['name']; ?></div></label>
									<?php } ?>
								</div>
								<?php echo $selectall_links; ?>
							<?php } ?>
						</div>
					</td>
					<td class="center">
						<table class="sub-table" align="center">
							<tr>
								<td></td>
								<td><?php echo $text_add; ?></td>
								<td><?php echo $text_min; ?></td>
								<td><?php echo $text_max; ?></td>
							</tr>
						<?php foreach ($cart_criteria as $cc) { ?>
							<tr>
								<td style="text-align: right"><?php echo ${'text_'.$cc} . (($cc == 'item') ? 's' : '') . ' (' . ${$cc.'_unit'} . '):'; ?></td>
								<td><input type="text" size="1" name="<?php echo $name; ?>_data[<?php echo $row; ?>][add_<?php echo $cc; ?>]" value="<?php echo (isset($rate['add_'.$cc])) ? $rate['add_'.$cc] : ''; ?>" /></td>
								<td><input type="text" size="1" name="<?php echo $name; ?>_data[<?php echo $row; ?>][min_<?php echo $cc; ?>]" value="<?php echo (isset($rate['min_'.$cc])) ? $rate['min_'.$cc] : ''; ?>" /></td>
								<td><input type="text" size="1" name="<?php echo $name; ?>_data[<?php echo $row; ?>][max_<?php echo $cc; ?>]" value="<?php echo (isset($rate['max_'.$cc])) ? $rate['max_'.$cc] : ''; ?>" /></td>
							</tr>
						<?php } ?>
							<tr>
								<td style="text-align: right"><?php echo $text_postcode . 's'; ?>:</td>
								<td colspan="3"><input type="text" style="width: 90%" name="<?php echo $name; ?>_data[<?php echo $row; ?>][postcodes]" value="<?php echo (isset($rate['postcodes'])) ? $rate['postcodes'] : ''; ?>" /></td>
							</tr>
							<tr>
								<td style="text-align: right"><?php echo $text_postcode_format; ?>:</td>
								<td colspan="3">
									<select name="<?php echo $name; ?>_data[<?php echo $row; ?>][postcode_format]">
										<?php $postcode_format = (isset($rate['postcode_format'])) ? $rate['postcode_format'] : 'none'; ?>
										<option value="none" <?php if ($postcode_format == 'none') echo 'selected="selected"'; ?>><?php echo $text_none; ?></option>
										<option value="uk" <?php if ($postcode_format == 'uk') echo 'selected="selected"'; ?>><?php echo $text_uk_format; ?></option>
									</select>
								</td>
							</tr>
							<tr>
								<td style="text-align: right"><?php echo $text_date_start; ?>:</td>
								<td colspan="2"><input class="date" type="text" size="8" name="<?php echo $name; ?>_data[<?php echo $row; ?>][date_start]" value="<?php echo (isset($rate['date_start'])) ? $rate['date_start'] : ''; ?>" /></td>
								<td></td>
							</tr>
							<tr>
								<td style="text-align: right"><?php echo $text_date_end; ?>:</td>
								<td colspan="2"><input class="date" type="text" size="8" name="<?php echo $name; ?>_data[<?php echo $row; ?>][date_end]" value="<?php echo (isset($rate['date_end'])) ? $rate['date_end'] : ''; ?>" /></td>
								<td></td>
							</tr>
						</table>
					</td>
					<td class="center">
						<table class="brackets sub-table" align="center">
							<tr>
								<td colspan="6">
									<div class="bluebox">
										<?php echo $text_rate_type; ?>:
										<select name="<?php echo $name; ?>_data[<?php echo $row; ?>][rate_type]" onchange="$(this).parent().parent().parent().next().find('span').html($(this).find('option:selected').attr('data-unit'))">
											<?php $rate_type = (isset($rate['rate_type'])) ? $rate['rate_type'] : 'item'; ?>
											<?php foreach ($rate_types as $rt) { ?>
												<option data-unit="<?php echo ${$rt.'_unit'}; ?>" value="<?php echo $rt; ?>" <?php if ($rate_type == $rt) echo 'selected="selected"'; ?>><?php echo ${'text_'.$rt} . $text_based; ?></option>
											<?php } ?>
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<td><a onclick="pasteFromSpreadsheet($(this))" title="Paste from spreadsheet"><img src="view/image/review.png" alt="Paste from spreadsheet" /></a></td>
								<td align="center"><?php echo $text_from; ?></td>
								<td align="center"><?php echo $text_to; ?></td>
								<td align="center"><?php echo $text_charge; ?></td>
								<td align="center"><em><?php echo $text_per; ?> (<span class="rate-unit"><?php echo ${$rate_type.'_unit'}; ?></span>)</em></td>
								<td></td>
							</tr>
						<?php $costs = (isset($rate['costs'])) ? $rate['costs'] : array('from' => array(''), 'to' => array(''), 'charge' => array(''), 'per' => array('')); ?>
						<?php for ($i = 0; $i < count($costs['from']); $i++) { ?>
							<tr class="sortable">
								<td align="center"><div class="sortable-arrow"></div></td>
								<td align="center"><input type="text" size="4" name="<?php echo $name; ?>_data[<?php echo $row; ?>][costs][from][]" value="<?php echo $costs['from'][$i]; ?>" /></td>
								<td align="center"><input type="text" size="4" name="<?php echo $name; ?>_data[<?php echo $row; ?>][costs][to][]" value="<?php echo $costs['to'][$i]; ?>" /></td>
								<td align="center"><input type="text" size="4" name="<?php echo $name; ?>_data[<?php echo $row; ?>][costs][charge][]" value="<?php echo $costs['charge'][$i]; ?>" /></td>
								<td align="center"><input type="text" size="4" name="<?php echo $name; ?>_data[<?php echo $row; ?>][costs][per][]" value="<?php echo $costs['per'][$i]; ?>" /></td>
								<td align="right"><a <?php if ($i == 0) { ?>style="visibility: hidden"<?php } ?> onclick="removeBracket($(this))"><img style="vertical-align: middle" src="view/image/delete.png" title="Remove Bracket" /></a></td>
							</tr>
						<?php } ?>
							<tr class="add-bracket">
								<td colspan="6" align="center"><a onclick="addBracket($(this))"><img src="view/image/add.png" title="Add Bracket" /></a></td>
							</tr>
							<tr>
								<td colspan="6">
									<div class="bluebox">
										<div style="margin-bottom: 5px">
											<?php echo $text_final_cost; ?>:
											<select name="<?php echo $name; ?>_data[<?php echo $row; ?>][final_cost]">
												<?php $final_cost = (isset($rate['final_cost'])) ? $rate['final_cost'] : 'single'; ?>
												<option value="single" <?php if ($final_cost == 'single') echo 'selected="selected"'; ?>><?php echo $text_single; ?></option>
												<option value="cumulative" <?php if ($final_cost == 'cumulative') echo 'selected="selected"'; ?>><?php echo $text_cumulative; ?></option>
											</select>
										</div>
										<div>
											<?php echo $text_add; ?>: <input type="text" size="2" name="<?php echo $name; ?>_data[<?php echo $row; ?>][add_cost]" value="<?php echo (isset($rate['add_cost'])) ? $rate['add_cost'] : ''; ?>" />
											&nbsp;
											<?php echo $text_min; ?>: <input type="text" size="2" name="<?php echo $name; ?>_data[<?php echo $row; ?>][min_cost]" value="<?php echo (isset($rate['min_cost'])) ? $rate['min_cost'] : ''; ?>" />
											&nbsp;
											<?php echo $text_max; ?>: <input type="text" size="2" name="<?php echo $name; ?>_data[<?php echo $row; ?>][max_cost]" value="<?php echo (isset($rate['max_cost'])) ? $rate['max_cost'] : ''; ?>" />
										</div>
									</div>
								</td>
							</tr>
						</table>
					</td>
					<td class="left" style="width: 1px">
						<a onclick="removeRow($(this))"><img src="view/image/error.png" title="Remove" /></a>
						<br /><br /><br /><br /><br /><br /><br /><br />
						<a onclick="copyRow($(this))"><img src="view/image/category.png" title="Copy" /></a>
					</td>
				</tr>
				<?php $row++; ?>
			<?php } ?>
				<tr>
					<td class="left" colspan="6" style="background: #EEE">
						<a class="button" style="float: right" onclick="$('#examples').parent().css({position:'fixed'}).end().dialog('open');"><span><?php echo $button_show_examples; ?></span></a>
						<a onclick="addRow($(this))" class="button"><span><?php echo $button_add_row; ?></span></a>
					</td>
				</tr>
			</tbody>
			</table>
		</form>
		<?php echo $copyright; ?>
	</div>
</div>
<div><div id="examples"><?php echo $help_examples; ?></div></div>
<?php if ($version < 150) { ?>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
	<script type="text/javascript" src="view/javascript/jquery/ui/ui.datepicker.js"></script>
<?php } else { ?>
	</div>
<?php } ?>
<script type="text/javascript" src="view/javascript/jquery/jquery.tabby.min.js"></script>
<script type="text/javascript"><!--
	// UI scripts
	$('#examples').dialog({
		autoOpen: false,
		title: 'Examples',
		width: $(window).width() * 0.5,
		height: $(window).height() * 0.67
	});
	$('#examples h2').click(function(){
		if (!$(this).hasClass('selected')) {
			$('#examples h2').removeClass('selected');
			$(this).addClass('selected');
			$('#examples div').slideUp();
			$(this).next().next().next().slideDown();
		}
	});
	
	function attachUIelements() {
		$('.scrollbox').each(function(){
			$(this).css('height', $(this).parent().parent().find('.brackets').height() - 20 + 'px');
		});
		$('input.date').removeClass('hasDatepicker').removeAttr('id').datepicker({
			dateFormat: 'yy-mm-dd'
		});
		$('.brackets').sortable({
			items: 'tr.sortable'
		});
	}
	attachUIelements();
	
	function pasteFromSpreadsheet(element) {
		$('<div style="margin: 10px; white-space: normal"><?php echo $help_cost_brackets_dialog; ?><textarea id="spreadsheet-data" style="height: 68%; width: 98%; font-family: monospace"></textarea></div>').dialog({
			title: 'Paste From Spreadsheet',
			modal: true,
			width: $(window).width() * 0.5,
			height: $(window).height() * 0.67,
			open: function(event, ui) { $('#spreadsheet-data').tabby(); },
			close: function(event, ui) { $(this).remove(); },
			buttons: {
				'Replace old data with new data': function() {
					var html = '';
					var clonedRow = element.parent().parent().next().clone();
					element.parent().parent().parent().find('tr.sortable').remove();
					var columns = $('#spreadsheet-data').val().split("\n");
					for (i = 0; i < columns.length; i++) {
						rows = columns[i].split("\t");
						var clone = clonedRow;
						clone.html(clone.html().replace(/(from\]\[\]" value=").*"/g, '$1' + (rows[0] ? rows[0] : '') + '"'));
						clone.html(clone.html().replace(/(to\]\[\]" value=").*"/g, '$1' + (rows[1] ? rows[1] : '') + '"'));
						clone.html(clone.html().replace(/(charge\]\[\]" value=").*"/g, '$1' + (rows[2] ? rows[2] : '') + '"'));
						clone.html(clone.html().replace(/(per\]\[\]" value=").*"/g, '$1' + (rows[3] ? rows[3] : '') + '"'));
						if (i != 0) {
							clone.find('a').css('visibility', 'visible');
						}
						html += $('<div>').append(clone).html();
					}
					element.parent().parent().next().before(html);
					$(this).dialog('close');
					attachUIelements();
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			}
		});
	}
	
	// Bracket scripts
	function addBracket(element) {
		var clone = element.parent().parent().prev().clone();
		clone.find('input[type="text"]').val('');
		clone.find('a').css('visibility', 'visible');
		element.parent().parent().before(clone);
		attachUIelements();
	}
	
	function removeBracket(element) {
		element.parent().parent().remove();
		attachUIelements();
	}
	
	// Row scripts
	var newRow = <?php echo $row; ?>;
	
	function addRow(element) {
		var clone = element.parent().parent().prev().clone();
		clone.html(clone.html().replace(/\[\d+\]/g, '['+newRow+']'));
		clone.find('input[type="text"]').val('');
		clone.find('input[type="checkbox"]').removeAttr('checked');
		clone.find('input.default-checked').attr('checked', 'checked');
		clone.find('input[type="hidden"]').remove();
		clone.find('.scrollbox option').remove();
		clone.find(':selected').removeAttr('selected');
		clone.find('tr.sortable').not(':first').remove();
		$('.list > tbody > tr:last-child').before(clone);
		window.scrollTo(0, document.body.scrollHeight);
		attachUIelements();
		newRow++;
	}
	
	function copyRow(element) {
		var row = element.parent().parent();
		row.find('input').each(function(){
			$(this).attr('value', $(this).val());
			if ($(this).is(':checked')) {
				$(this).attr('checked', 'checked');
			} else {
				$(this).removeAttr('checked');
			}
		});
		var clone = row.clone();
		row.find('option').each(function(i){
			if($(this).is(':selected')) {
				clone.find('option').eq(i).attr('selected', 'selected');
			} else {
				clone.find('option').eq(i).removeAttr('selected');
			}
		});
		clone.html(clone.html().replace(/\[\d+\]/g, '['+newRow+']'));
		$('.list > tbody > tr:last-child').before(clone);
		window.scrollTo(0, document.body.scrollHeight);
		attachUIelements();
		newRow++;
	}
	
	function removeRow(element) {
		if ($('.list > tbody > tr').length < 3) {
			element.parent().parent().find('input[type="text"]').val('');
			element.parent().parent().find('input[type="checkbox"]').removeAttr('checked');
			element.parent().parent().find('input.default-checked').attr('checked', 'checked');
			element.parent().parent().find('input[type="hidden"]').remove();
			element.parent().parent().find('.scrollbox option').remove();
			element.parent().parent().find('option:first-child').attr('selected', 'selected');
			element.parent().parent().find('tr.sortable').not(':first').remove();
			element.parent().parent().find('tr.sortable').find('a').css('visibility', 'hidden');
		} else {
			element.parent().parent().remove();
		}
		attachUIelements();
	}
//--></script>
<?php echo $footer; ?>