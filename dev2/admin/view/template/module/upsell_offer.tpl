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
	    <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
	    <div class="buttons"><a onclick="saveSettings();" class="button"><span><?php echo $button_save_settings; ?></span></a><a onclick="insertOffer();" class="button"><span><?php echo $button_insert_upsell_offer; ?></span></a><a onclick="deleteOffer();" class="button"><span><?php echo $button_delete; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>    
	  </div>
	  <div class="content">
		<div id="tabs" class="htabs">
			<a href="#tab-upsell-offers"><?php echo $tab_upsell_offers; ?></a>
			<a href="#tab-settings"><?php echo $tab_settings; ?></a>
			<a href="#tab-help"><?php echo $tab_help; ?></a>
			<div id="ocx-loader"><img style="margin: 6px 0 0 10px;" src="view/image/ocx-ajax-loader.gif" alt="Loading ..." /></div>
		</div>
		<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
			<div id="tab-upsell-offers">
				<div id="upsell-offers-list"></div>
			</div> 
			
			<div id="tab-settings">
				<table class="form">
				  <tr>
				    <td><?php echo $entry_product_image_size; ?></td>
				    <td>
					<input type="text" name="upsell_offer_product_image_width" value="<?php echo $upsell_offer_product_image_width; ?>" size="3" />
					x
					<input type="text" name="upsell_offer_product_image_height" value="<?php echo $upsell_offer_product_image_height; ?>" size="3" /></td>
				  </tr>
				  <tr>
				    <td><?php echo $entry_product_list_image_size; ?></td>
				    <td>
					<input type="text" name="upsell_offer_product_list_image_width" value="<?php echo $upsell_offer_product_list_image_width; ?>" size="3" />
					x
					<input type="text" name="upsell_offer_product_list_image_height" value="<?php echo $upsell_offer_product_list_image_height; ?>" size="3" /></td>
				  </tr>
				</table>
				<div id="general-languages" class="htabs">
					<?php foreach ($languages as $language) { ?>
					<a href="#general-language<?php echo $language['language_id']; ?>"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
					<?php } ?>
				</div>
				<?php foreach ($languages as $language) { ?>
				<div id="general-language<?php echo $language['language_id']; ?>">
					<table class="form">
						<tr>
							<td><?php echo $entry_general_title; ?></td>
							<td><input type="text" name="upsell_offer_description[title][<?php echo $language['language_id']; ?>]" size="40" value="<?php echo isset($upsell_offer_description['title'][$language['language_id']]) ? $upsell_offer_description['title'][$language['language_id']] : ''; ?>" /></td>
						</tr>
						<tr>
							<td><?php echo $entry_general_description; ?></td>
							<td><input type="text" name="upsell_offer_description[description][<?php echo $language['language_id']; ?>]" size="40" value="<?php echo isset($upsell_offer_description['description'][$language['language_id']]) ? $upsell_offer_description['description'][$language['language_id']] : ''; ?>" /></td>
						</tr>
					</table>
				</div>
				<?php } ?>
				<table id="module" class="list">
				  <thead>
				    <tr>
				      <td class="left"><?php echo $entry_layout; ?></td>
				      <td class="left"><?php echo $entry_position; ?></td>
				      <td class="left"><?php echo $entry_selector; ?></td>
				      <td class="left"><?php echo $entry_status; ?></td>
				      <td></td>
				    </tr>
				  </thead>
				  <?php $module_row = 0; ?>
				  <?php if ($modules) { ?>
				  <?php foreach ($modules as $module) { ?>
				  <tbody id="module-row<?php echo $module_row; ?>">
				    <tr>
				      <td class="left"><select name="upsell_offer_module[<?php echo $module_row; ?>][layout_id]">
					  <?php foreach ($layouts as $layout) { ?>
					  <?php if ($layout['layout_id'] == $module['layout_id']) { ?>
					  <option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
					  <?php } else { ?>
					  <option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
					  <?php } ?>
					  <?php } ?>
					</select></td>
				      <td class="left"><select name="upsell_offer_module[<?php echo $module_row; ?>][position]">
					  <?php if ($module['position'] == 'content_top') { ?>
					  <option value="content_top" selected="selected"><?php echo $text_content_top; ?></option>
					  <?php } else { ?>
					  <option value="content_top"><?php echo $text_content_top; ?></option>
					  <?php } ?>
					  <?php if ($module['position'] == 'content_bottom') { ?>
					  <option value="content_bottom" selected="selected"><?php echo $text_content_bottom; ?></option>
					  <?php } else { ?>
					  <option value="content_bottom"><?php echo $text_content_bottom; ?></option>
					  <?php } ?>
					  <?php if ($module['position'] == 'column_left') { ?>
					  <option value="column_left" selected="selected"><?php echo $text_column_left; ?></option>
					  <?php } else { ?>
					  <option value="column_left"><?php echo $text_column_left; ?></option>
					  <?php } ?>
					  <?php if ($module['position'] == 'column_right') { ?>
					  <option value="column_right" selected="selected"><?php echo $text_column_right; ?></option>
					  <?php } else { ?>
					  <option value="column_right"><?php echo $text_column_right; ?></option>
					  <?php } ?>
					</select></td>
				      <td class="left">
					  <?php if ($module['selector']) { ?>
					  <input type="text" name="upsell_offer_module[<?php echo $module_row; ?>][selector]" value="<?php echo $module['selector']; ?>" />
					  <?php } else { ?>
					  <input type="text" name="upsell_offer_module[<?php echo $module_row; ?>][selector]" value="checkout/checkout" />
					  <?php } ?>
				      </td>
				      <td class="left"><select name="upsell_offer_module[<?php echo $module_row; ?>][status]">
					  <?php if ($module['status']) { ?>
					  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
					  <option value="0"><?php echo $text_disabled; ?></option>
					  <?php } else { ?>
					  <option value="1"><?php echo $text_enabled; ?></option>
					  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
					  <?php } ?>
					</select></td>
					<td class="left"><a onclick="$('#module-row<?php echo $module_row; ?>').remove();" class="button"><span><?php echo $button_remove; ?></span></a></td>
					<input type="hidden" name="upsell_offer_module[<?php echo $module_row; ?>][sort_order]" value="0" />
				    </tr>
				  </tbody>
				  <?php $module_row++; ?>
				  <?php } ?>
				  <?php } ?>
				  <tfoot>
				    <tr>
				      <td colspan="4"></td>
				      <td class="left"><a onclick="addModule();" class="button"><span><?php echo $button_add_module; ?></span></a></td>
				    </tr>
				  </tfoot>
				</table>
			</div>
			
			<div id="tab-help">
				Changelog and HELP you can find  : <a href="http://oc-extensions.com/Upsell-Offer" target="blank">HERE</a><br /><br />
				If you need support email us at <strong>support@oc-extensions.com</strong><br /><br /><br />
					
				<u><strong>Become a Premium Member:</strong></u><br /><br />
				With Premium Membership you will can download all our products (past, present and future) starting with the payment date, until the same day and month, a year later. <br />
				Find more on <a href="http://www.oc-extensions.com">www.oc-extensions.com</a>
			</div>
			<div id="dialog-form"></div>
		</form>
	  </div>
	</div>
</div>
<div id="dialog"></div>
<script type="text/javascript"><!--
$('#tabs a').tabs();
$('#store-tabs a').tabs();
$('#general-languages a').tabs(); 

$('#ocx-loader').hide().ajaxStart(function() { $(this).show(); }).ajaxStop(function() { $(this).hide(); });

$('#upsell-offers-list').load('index.php?route=module/upsell_offer/getList&token=<?php echo $token; ?>');

$('#upsell-offers-list .pagination a').live('click', function() {
	$('#upsell-offers-list').fadeOut('fast');
		
	$('#upsell-offers-list').load(this.href);
	
	$('#upsell-offers-list').fadeIn('fast');
	
	return false;
});	
//--></script> 
<script type="text/javascript"><!--
var module_row = <?php echo $module_row; ?>;

function addModule() {	
	html  = '<tbody id="module-row' + module_row + '">';
	html += '  <tr>';
	html += '    <td class="left"><select name="upsell_offer_module[' + module_row + '][layout_id]">';
	<?php foreach ($layouts as $layout) { ?>
	html += '      <option value="<?php echo $layout['layout_id']; ?>"><?php echo addslashes($layout['name']); ?></option>';
	<?php } ?>
	html += '    </select></td>';
	html += '    <td class="left"><select name="upsell_offer_module[' + module_row + '][position]">';
	html += '      <option value="content_top"><?php echo $text_content_top; ?></option>';
	html += '      <option value="content_bottom"><?php echo $text_content_bottom; ?></option>';
	html += '      <option value="column_left"><?php echo $text_column_left; ?></option>';
	html += '      <option value="column_right"><?php echo $text_column_right; ?></option>';
	html += '    </select></td>';
	html += '    <td class="left"><input type="text" name="upsell_offer_module[' + module_row + '][selector]" value="checkout/checkout" /></td>';
	html += '    <td class="left"><select name="upsell_offer_module[' + module_row + '][status]">';
	html += '      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
	html += '      <option value="0"><?php echo $text_disabled; ?></option>';
	html += '    </select></td>';
	html += '    <td class="left"><a onclick="$(\'#module-row' + module_row + '\').remove();" class="button"><span><?php echo $button_remove; ?></span></a></td>';
	html += '    <input type="hidden" name="upsell_offer_module[' + module_row + '][sort_order]" value="0" />';
	html += '  </tr>';
	html += '</tbody>';
	
	$('#module tfoot').before(html);
	
	module_row++;
}
//--></script>
<script type="text/javascript"><!--
function saveSettings() {
	$.ajax({
		type: 'POST',
		url: 'index.php?route=module/upsell_offer/saveSettings&token=<?php echo $token; ?>',
		data: $('#tab-settings select, #tab-settings input'),
		dataType: 'json',
		success: function(json) {
			$('.warning').remove();
			$('.success').remove();
			
			if (json['success']) {
				$('#tabs').after('<div class="success">' + json['success'] + '</div>');
				$('.success').fadeIn('slow');
			}	
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}
//--></script>
<script type="text/javascript"><!--
function insertOffer() {
	$.ajax({
		url: 'index.php?route=module/upsell_offer/insert&token=<?php echo $token; ?>',
		dataType: 'json',
		success: function(json){
			$('#dialog-form').html(json['output']);
			openDialog('#dialog-form', '<?php echo $form_title; ?>');
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function updateOffer(upsell_offer_id) {
	$.ajax({
		url: 'index.php?route=module/upsell_offer/update&token=<?php echo $token; ?>',
		data: 'upsell_offer_id=' + upsell_offer_id,
		dataType: 'json',
		success: function(json){
			$('#dialog-form').html(json['output']);
			openDialog('#dialog-form', '<?php echo $form_title; ?>');
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function openDialog(selector, title) {
	$(selector).dialog({
		title: title,
		close: function (event, ui) {
		},	
		width: 'auto',
		height: 'auto',
		resizable: false,
		modal: true
	});
}

function previewOffer(upsell_offer_id, operation){
	$('#dialog').html('');
	
	$.ajax({
		url: 'index.php?route=module/upsell_offer/preview&token=<?php echo $token; ?>&upsell_offer_id=' + upsell_offer_id,
		data: 'upsell_offer_id=' + upsell_offer_id,
		dataType: 'json',
		success: function(json){
			$('#dialog').html(json['output']);
			openDialog('#dialog', 'Upsell Offer ID: #' + upsell_offer_id);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}
//--></script>
<script type="text/javascript"><!--
function deleteOffer(){
	if ( $('#upsell-offers-list input[type=\'checkbox\']:checked').length ) {
		if (!confirm('Delete/Uninstall cannot be undone! Are you sure you want to do this?')) {
			return false;
		}
		
		$.ajax({
			type: 'POST',
			url: 'index.php?route=module/upsell_offer/delete&token=<?php echo $token; ?>',
			data: $('#upsell-offers-list input[type=\'checkbox\']:checked'),
			dataType: 'json',
			success: function(json){
				$('.warning').remove();
				$('.success').remove();
			
				if (json['error']){
					if (json['error']['warning']){
						$('#tabs').after('<div class="warning">' + json['error'] + '</div>');
						$('.warning').fadeIn('slow');
					}
				}
				
				if (json['success']){
					$('#tabs').after('<div class="success">' + json['success'] + '</div>');
					$('.success').fadeIn('slow');
					
					$('#upsell-offers-list').load('index.php?route=module/upsell_offer/getList&token=<?php echo $token; ?>');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

function filter() {
	url = 'index.php?route=module/upsell_offer/getList&token=<?php echo $token; ?>';
	
	var filter_name = $('input[name=\'filter_name\']').attr('value');
	
	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}
	
	$('#upsell-offers-list').load(url);
}
	
//--></script> 
<?php echo $footer; ?>