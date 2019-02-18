<table class="table">
  <tr>
    <td class="col-xs-2">
    	<h5><span class="required">*</span> ProductBundles status:</h5>
    	<span class="help"><i class="fa fa-info-circle"></i>&nbsp;Enable or disable ProductBundles.</span>
    </td>
    <td class="col-xs-10">
		<div class="col-xs-4">
            <select id="Checker" name="ProductBundles[Enabled]" class="ProductBundlesEnabled form-control">
				<option value="no" <?php echo ($data['ProductBundles']['Enabled'] == 'no') ? 'selected=selected' : '' ?>>Disabled</option>
                <option value="yes" <?php echo ($data['ProductBundles']['Enabled'] == 'yes') ? 'selected=selected' : '' ?>>Enabled</option>
            </select>
        </div>
   </td>
  </tr>
</table>

<table class="table module__">
	<tr>
		<td class="col-xs-2">
        	<h5>Show close button on the fancybox for the product options:</h5>
      		<span class="help"><i class="fa fa-info-circle"></i>&nbsp;Shows a tiny close button on the upper right side of the popup.</span>
        </td>
        <td class="col-xs-10">
			<div class="col-xs-4">
                <select name="ProductBundles[ShowCloseButton]" class="ProductBundlesShowCloseButton form-control">
                   <option value="yes" <?php echo ($data['ProductBundles']['ShowCloseButton'] == 'yes') ? 'selected=selected' : '' ?>>Enabled</option>
                   <option value="no" <?php echo ($data['ProductBundles']['ShowCloseButton'] == 'no') ? 'selected=selected' : '' ?>>Disabled</option>
                </select>
            </div>
       </td>
    </tr>
    <tr>
		<td class="col-xs-2">
        	<h5>If a given bundle is added more than once in the cart:</h5>
        	<span class="help"><i class="fa fa-info-circle"></i>&nbsp;The discount can be added only once on every time depending on the setting.</span> 
        </td>
        <td class="col-xs-10">
			<div class="col-xs-4">
                <select name="ProductBundles[MultipleBundles]" class="ProductBundlesMultipleBundles form-control">
                   <option value="no" <?php echo (isset($data['ProductBundles']['MultipleBundles']) && ($data['ProductBundles']['MultipleBundles'] == 'no')) ? 'selected=selected' : '' ?>>Add the discount once</option>
                   <option value="yes" <?php echo (isset($data['ProductBundles']['MultipleBundles']) && ($data['ProductBundles']['MultipleBundles'] == 'yes')) ? 'selected=selected' : '' ?>>Add the discount every time</option>
                </select>
			</div>
       </td>
    </tr>
</table>
<br />
<table id="module__" class="table table-bordered table-hover info module__">
  <thead>
    <tr class="table-header">
      <td class="left"><strong><?php echo $entry_layout_options; ?></strong></td>
      <td class="left"><strong><?php echo $entry_position_options; ?></strong></td>
      <td class="left"><strong>Actions:</strong></td>
    </tr>
  </thead>
  <?php $module__row = 0; ?>
  <?php foreach ($modules as $module) { ?>
  <tbody id="module__row<?php echo $module__row; ?>">
    <tr>
      <td class="left col-xs-3">
		<div class="form-group modulePositioning">
            <label class="module-row-label"><?php echo $entry_status; ?></label>
            <select class="form-control" name="productbundles_module[<?php echo $module__row; ?>][status]">
              <?php if ($module['status']) { ?>
              <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
              <option value="0"><?php echo $text_disabled; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_enabled; ?></option>
              <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
              <?php } ?>
            </select>
         </div>
         <div class="form-group modulePositioning">
            <label class="module-row-label"><?php echo $entry_layout; ?></label>
            <select class="form-control" name="productbundles_module[<?php echo $module__row; ?>][layout_id]">
              <?php foreach ($layouts as $layout) { ?>
              <?php if ($layout['layout_id'] == $module['layout_id']) { ?>
              <option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
         </div>
		 <div class="form-group modulePositioning">
            <label class="module-row-label"><?php echo $entry_sort_order; ?></label>
            <input class="form-control module-row-input-number" type="number" name="productbundles_module[<?php echo $module__row; ?>][sort_order]" value="<?php echo $module['sort_order']; ?>" />
         </div>
      </td>
      <td class="left">
        <div class="widgetPositionOpenCart">
            <div class="radio">
                <label for="buttonPos<?php echo $module__row; ?>_1">
                    <input <?php if ($module['position'] == 'content_top') echo 'checked="checked"'; ?> type="radio" style="width:auto" name="productbundles_module[<?php echo $module__row; ?>][position]" id="buttonPos<?php echo $module__row; ?>_1" class="widgetPositionOptionBox" data-checkbox="#buttonPosCheckbox_<?php echo $module__row; ?>" value="content_top" />
                    <?php echo $text_content_top; ?>
                </label>
            </div>
            <div class="positionSampleBox">
                <label for="buttonPos<?php echo $module__row; ?>_1"><img class="img-thumbnail" src="view/image/productbundles/content_top.png" title="<?php echo $text_content_top; ?>" border="0" /></label>
            </div>        
        </div>
        <div class="widgetPositionOpenCart">
            <div class="radio">
                <label for="buttonPos<?php echo $module__row; ?>_2">
                    <input <?php if ($module['position'] == 'content_bottom') echo 'checked="checked"'; ?> type="radio" style="width:auto" name="productbundles_module[<?php echo $module__row; ?>][position]" id="buttonPos<?php echo $module__row; ?>_2" class="widgetPositionOptionBox" data-checkbox="#buttonPosCheckbox_<?php echo $module__row; ?>" value="content_bottom" />
                    <?php echo $text_content_bottom; ?>
                </label>
            </div>
            <div class="positionSampleBox ">
                <label for="buttonPos<?php echo $module__row; ?>_2"><img class="img-thumbnail" src="view/image/productbundles/content_bottom.png" title="<?php echo $text_content_bottom; ?>" border="0" /></label>
            </div>
        </div>
        <div class="widgetPositionOpenCart">
            <div class="radio">
                <label for="buttonPos<?php echo $module__row; ?>_3">
                    <input <?php if ($module['position'] == 'column_left') echo 'checked="checked"'; ?> type="radio" style="width:auto" name="productbundles_module[<?php echo $module__row; ?>][position]" id="buttonPos<?php echo $module__row; ?>_3" class="widgetPositionOptionBox" data-checkbox="#buttonPosCheckbox_<?php echo $module__row; ?>" value="column_left" />
                    <?php echo $text_column_left; ?>
                </label>
            </div>
            <div class="positionSampleBox">
                <label for="buttonPos<?php echo $module__row; ?>_3"><img class="img-thumbnail" src="view/image/productbundles/column_left.png" title="<?php echo $text_column_left; ?>" border="0" /></label>
            </div>
        </div>
        <div class="widgetPositionOpenCart last">
            <div class="radio">
                <label for="buttonPos<?php echo $module__row; ?>_4">
                    <input <?php if ($module['position'] == 'column_right') echo 'checked="checked"'; ?> type="radio" style="width:auto" name="productbundles_module[<?php echo $module__row; ?>][position]" id="buttonPos<?php echo $module__row; ?>_4" class="widgetPositionOptionBox" data-checkbox="#buttonPosCheckbox_<?php echo $module__row; ?>" value="column_right" />
                    <?php echo $text_column_right; ?>
                </label>
            </div>
            <div class="positionSampleBox">
                <label for="buttonPos<?php echo $module__row; ?>_4"><img class="img-thumbnail" src="view/image/productbundles/column_right.png" title="<?php echo $text_column_right; ?>" border="0" /></label>
            </div>
        </div>
      </td>
      <td class="left" style="vertical-align:bottom;"><a onclick="$('#module__row<?php echo $module__row; ?>').remove();" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp;<?php echo $button_remove; ?></a></td>
    </tr>
  </tbody>
  <?php $module__row++; ?>
  <?php } ?>
  <tfoot>
    <tr>
      <td colspan="2"></td>
      <td class="left"><a onclick="addPosition();" class="btn btn-small btn-primary"><i class="fa fa-plus"></i>&nbsp;<?php echo $button_add_module; ?></a></td>
    </tr>
  </tfoot>
</table>
<script type="text/javascript">
var module__row = <?php echo $module__row; ?>;
function addPosition() {
	html  = '<tbody style="display:none;" id="module__row' + module__row + '">';
	html += '  <tr>';
  	html += '    <td class="left col-xs-3">';
  	html += '<div class="form-group modulePositioning">';
  	html += ' <label><?php echo $entry_status; ?></label>';
  	html += '    <select name="productbundles_module[' + module__row + '][status]" class="form-control">';
	html += '      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
	html += '      <option value="0"><?php echo $text_disabled; ?></option>';
	html += '    </select></div> ';
	html += '<div class="form-group modulePositioning">';
	html += '	<label><?php echo $entry_layout; ?></label>'
  	html += '  <select name="productbundles_module[' + module__row + '][layout_id]" class="form-control">';
  	<?php foreach ($layouts as $layout) { ?>
  	html += '      <option value="<?php echo $layout['layout_id']; ?>"><?php echo addslashes($layout['name']); ?></option>';
  	<?php } ?>
	html += '    </select></div>';
  	html += '<div class="form-group modulePositioning"><label><?php echo $entry_sort_order; ?></label><input class="form-control" type="number" name="productbundles_module['+ module__row + '][sort_order]" value="0" /></div>';
  	html += '    </td>';
	html += '    <td class="left">';
 	html += '<div class="widgetPositionOpenCart"><div class="radio"><label for="buttonPos' + module__row + '_1"><input checked="checked" type="radio" style="width:auto" name="productbundles_module[' + module__row + '][position]" id="buttonPos' + module__row + '_1" class="widgetPositionOptionBox" data-checkbox="#buttonPosCheckbox_' + module__row + '" value="content_top" /><?php echo $text_content_top; ?></label></div><div class="positionSampleBox"><label for="buttonPos' + module__row + '_1"><img class="img-thumbnail" src="view/image/productbundles/content_top.png" title="<?php echo $text_content_top; ?>" border="0" /></label></div></div>';
  	html += '<div class="widgetPositionOpenCart"><div class="radio"><label for="buttonPos' + module__row + '_2"><input type="radio" style="width:auto" name="productbundles_module[' + module__row + '][position]" id="buttonPos' + module__row + '_2" class="widgetPositionOptionBox" data-checkbox="#buttonPosCheckbox_' + module__row + '" value="content_bottom" /><?php echo $text_content_bottom; ?></label></div><div class="positionSampleBox"><label for="buttonPos' + module__row + '_2"><img class="img-thumbnail" src="view/image/productbundles/content_bottom.png" title="<?php echo $text_content_bottom; ?>" border="0" /></label></div></div>';
  	html += '<div class="widgetPositionOpenCart"><div class="radio"><label for="buttonPos' + module__row + '_3"><input type="radio" style="width:auto" name="productbundles_module[' + module__row + '][position]" id="buttonPos' + module__row + '_3" class="widgetPositionOptionBox" data-checkbox="#buttonPosCheckbox_' + module__row + '" value="column_left" /><?php echo $text_column_left; ?></label></div><div class="positionSampleBox"><label for="buttonPos' + module__row + '_3"><img class="img-thumbnail" src="view/image/productbundles/column_left.png" title="<?php echo $text_column_left; ?>" border="0" /></label></div></div>';
  	html += '<div class="widgetPositionOpenCart last"><div class="radio"><label for="buttonPos' + module__row + '_4"><input type="radio" style="width:auto" name="productbundles_module[' + module__row + '][position]" id="buttonPos' + module__row + '_4" class="widgetPositionOptionBox" data-checkbox="#buttonPosCheckbox_' + module__row + '" value="column_right" /><?php echo $text_column_right; ?></label></div><div class="positionSampleBox"><label for="buttonPos' + module__row + '_4"><img class="img-thumbnail" src="view/image/productbundles/column_right.png" title="<?php echo $text_column_right; ?>" border="0" /></label></div></div>';
  	html += '    </td>';
  	html += '    <td class="left" style="vertical-align:bottom;"><a onclick="$(\'#module__row' + module__row + '\').remove();" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp;<?php echo $button_remove; ?></a></td>';
  	html += '  </tr>';
 	html += '</tbody>';

	$('#module__ tfoot').before(html);
	$('#module__row' + module__row).fadeIn();
	module__row++;
}
</script>