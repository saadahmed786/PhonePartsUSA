<?php
//==============================================================================
// Multi Flat Rate Shipping v154.1
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================
?>

<?php echo $header; ?>
<style type="text/css">
	div {
		white-space: nowrap;
	}
	.help, .tooltip, .scrollbox div, #examples div {
		white-space: normal;
	}
	.form td:first-child {
		width: 250px !important;
	}
	td {
		background: #FFF;
	}
	thead td {
		height: 24px;
	}
	.scrollbox {
		margin: auto;
		min-height: 130px;
		min-width: 120px;
		text-align: left;
		width: 95%;
	}
	.alternating label:nth-child(even) div {
		background: #E4EEF7;
	}
	.selectall-links {
		font-size: 11px;
		padding: 0 !important;
		text-align: center;
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
		max-width: 300px;
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
			<table class="form" style="margin-bottom: -1px">
				<tr>
					<td><?php echo $entry_status; ?></td>
					<td><select name="<?php echo $name; ?>_status">
							<option value="1" <?php if (!empty(${$name.'_status'})) echo 'selected="selected"'; ?>><?php echo $text_enabled; ?></option>
							<option value="0" <?php if (empty(${$name.'_status'})) echo 'selected="selected"'; ?>><?php echo $text_disabled; ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td><?php echo $entry_sort_order; ?></td>
					<td><input type="text" size="1" name="<?php echo $name; ?>_sort_order" value="<?php echo (!empty(${$name.'_sort_order'})) ? ${$name.'_sort_order'} : 2; ?>" /></td>
				</tr>
				<tr>
					<td><?php echo $entry_heading; ?></td>
					<td><?php foreach ($languages as $language) { ?>
							<?php $lcode = $language['code']; ?>
							<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
							<input type="text" size="30" name="<?php echo $name; ?>_heading[<?php echo $lcode; ?>]" value="<?php echo (!empty(${$name.'_heading'}[$lcode])) ? ${$name.'_heading'}[$lcode] : $heading_title; ?>" />
							<br />
						<?php } ?>
					</td>
				</tr>
			</table>
			<table class="list">
			<thead>
				<tr>
					<td class="center"><div style="float: right"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo $help_general_settings; ?></span></div> <?php echo $entry_general_settings; ?></td>
				<?php foreach ($order_criteria as $oc) { ?>
					<td class="center"><div style="float: right"><span class="tooltip-mark">?</span> <span class="tooltip"><?php echo ${'help_'.$oc.'s'}; ?></span></div> <?php echo ${'text_'.$oc.'s'}; ?></td>
				<?php } ?>
					
                 
                    <td class="center"><div style="float: right"><span class="tooltip-mark">?</span> <span class="tooltip" style="right: 80px"><?php echo $help_cost; ?></span></div> <?php echo $entry_cost; ?></td>
					<td class="center"><span class="tooltip-mark">?</span> <span class="tooltip" style="right: 0"><?php echo $help_actions; ?></span></td>
				</tr>
			</thead>
			<tbody>
			<?php $row = 0;
            
			$rates = (!empty(${$name.'_data'})) ? ${$name.'_data'} : array('');
           
			foreach ($rates as $rate) { ?>
				<tr>
					<td class="center">
						<div><strong><?php echo $text_title; ?></strong></div>
						<?php foreach ($languages as $language) { ?>
							<?php $lcode = $language['code']; ?>
							<div><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
								<input type="text" name="<?php echo $name; ?>_data[<?php echo $row; ?>][title][<?php echo $lcode; ?>]" value="<?php echo (!empty($rate['title']) && !empty($rate['title'][$lcode])) ? $rate['title'][$lcode] : ''; ?>" />
							</div>
						<?php } ?>
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
						<div><strong><?php echo $text_value_for_total; ?></strong></div>
						<div><select name="<?php echo $name; ?>_data[<?php echo $row; ?>][total_value]">
								<?php $total_value = (isset($rate['total_value'])) ? $rate['total_value'] : 'subtotal'; ?>
								<option value="subtotal" <?php if ($total_value == 'subtotal') echo 'selected="selected"'; ?>><?php echo $text_subtotal; ?></option>
								<option value="taxed" <?php if ($total_value == 'taxed') echo 'selected="selected"'; ?>><?php echo $text_taxed_subtotal; ?></option>
								<option value="total" <?php if ($total_value == 'total') echo 'selected="selected"'; ?>><?php echo $text_total; ?></option>
							</select>
						</div>
                        <br />
                        <div><strong>Signature</strong></div>
                        <div>
								<input type="text" name="<?php echo $name; ?>_data[<?php echo $row; ?>][signature]" value="<?php echo (!empty($rate['signature']) ) ? $rate['signature'] : ''; ?>" />
							</div>
                            
                         <br />
                         <div><strong>Delivery Time</strong></div>
                        <div>
								<input type="text" name="<?php echo $name; ?>_data[<?php echo $row; ?>][delivery_time]" value="<?php echo (!empty($rate['delivery_time'])) ? $rate['delivery_time'] : ''; ?>" />
							</div>
                        
					</td>
				<?php foreach ($order_criteria as $oc) { ?>
					<td class="center">
						<div class="scrollbox alternating">
							<?php foreach (${$oc.'s'} as $c) { ?>
								<?php $checked = ( in_array($c[$oc.'_id'], $rate[$oc.'s'])) ? 'checked="checked"' : ''; ?>
								<label><div><input class="default-checked" type="checkbox" name="<?php echo $name; ?>_data[<?php echo $row; ?>][<?php echo $oc.'s'; ?>][]" value="<?php echo $c[$oc.'_id']; ?>" <?php echo $checked; ?> /><?php echo $c['name']; ?></div></label>
							<?php } ?>
						</div>
						<?php echo $selectall_links; ?>
					</td>
				<?php } ?>
					<td class="center">
						<input type="text" style="width: 76px" name="<?php echo $name; ?>_data[<?php echo $row; ?>][cost]" value="<?php echo (isset($rate['cost'])) ? $rate['cost'] : ''; ?>" />
						<br />
						<select name="<?php echo $name; ?>_data[<?php echo $row; ?>][type]">
							<?php $type = (isset($rate['type'])) ? $rate['type'] : 'flatrate'; ?>
							<option value="flatrate" <?php if ($type == 'flatrate') echo 'selected="selected"'; ?>><?php echo $text_flat_rate; ?></option>
							<option value="peritem" <?php if ($type == 'peritem') echo 'selected="selected"'; ?>><?php echo $text_per_item; ?></option>
						</select>
					</td>
					<td class="left" style="width: 1px">
						<a onclick="removeRow($(this))"><img src="view/image/error.png" title="Remove" /></a>
						<br /><br /><br /><br />
						<a onclick="copyRow($(this))"><img src="view/image/category.png" title="Copy" /></a>
					</td>
				</tr>
				<?php $row++; ?>
			<?php } ?>
				<tr>
					<td class="left" colspan="8	" style="background: #EEE">
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
<?php if ($v14x) { ?>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
<?php } else { ?>
	</div>
<?php } ?>
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
	
	// Row scripts
	var newRow = <?php echo $row; ?>;
	
	function addRow(element) {
		var clone = element.parent().parent().prev().clone();
		clone.html(clone.html().replace(/\[\d+\]/g, '['+newRow+']'));
		clone.find('input[type="text"]').val('');
		clone.find('input[type="checkbox"]').removeAttr('checked');
		clone.find('input.default-checked').attr('checked', 'checked');
		clone.find(':selected').removeAttr('selected');
		$('.list > tbody > tr:last-child').before(clone);
		window.scrollTo(0, document.body.scrollHeight);
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
		newRow++;
	}
	
	function removeRow(element) {
		if ($('.list > tbody > tr').length < 3) {
			element.parent().parent().find('input[type="text"]').val('');
			element.parent().parent().find('input[type="checkbox"]').removeAttr('checked');
			element.parent().parent().find('input.default-checked').attr('checked', 'checked');
			element.parent().parent().find('option:first-child').attr('selected', 'selected');
		} else {
			element.parent().parent().remove();
		}
	}
//--></script>
<?php echo $footer; ?>