<table class="table">
    <tr>
		<td class="col-xs-2">
        	<h5>Title:</h5>
        	<span class="help"><i class="fa fa-info-circle"></i>&nbsp;This will be the title of the widget.</span>
        </td>
        <td class="col-xs-10">
            <div class="col-xs-4">
                <?php foreach ($languages as $language) { ?>
                    <div class="input-group">
                      <span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" style="margin-top:-3px;" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></span>
                      <input name="ProductBundles[WidgetTitle][<?php echo $language['code']; ?>]" class="form-control" type="text" value="<?php echo (isset($data['ProductBundles']['WidgetTitle'][$language['code']])) ? $data['ProductBundles']['WidgetTitle'][$language['code']] : 'Check out this bundle:' ?>" />
                    </div>
                    <br />
				<?php } ?>
			</div>
       </td>
    </tr>
    <tr>
		<td class="col-xs-2">
        	<h5>Wrap in widget:</h5>
            <span class="help"><i class="fa fa-info-circle"></i>&nbsp;Design may vary depending on the design on the template.</span>
        </td>
        <td class="col-xs-10">
			<div class="col-xs-4">
                <select name="ProductBundles[WrapInWidget]" class="ProductBundlesWrapInWidget form-control">
                    <option value="yes" <?php echo ($data['ProductBundles']['WrapInWidget'] == 'yes') ? 'selected=selected' : '' ?>>Enabled</option>
                   <option value="no" <?php echo ($data['ProductBundles']['WrapInWidget'] == 'no') ? 'selected=selected' : '' ?>>Disabled</option>
                </select>
            </div>
       </td>
    </tr>
    <tr>
		<td class="col-xs-2">
        	<h5>Picture Width & Height:</h5>
            <span class="help"><i class="fa fa-info-circle"></i>&nbsp;In Pixels</span>
        </td>
		<td class="col-xs-10">
        	<div class="col-xs-3">
                <div class="input-group">
                  <span class="input-group-addon">Width:&nbsp;</span>
					<input type="text" name="ProductBundles[PictureWidth]" class="ProductBundlesPictureWidth form-control" value="<?php echo (isset($data['ProductBundles']['PictureWidth'])) ? $data['ProductBundles']['PictureWidth'] : '80' ?>" />
				  <span class="input-group-addon">px</span>
                </div>
                <br />
                <div class="input-group">
                  <span class="input-group-addon">Height:</span>
					<input type="text" name="ProductBundles[PictureHeight]" class="ProductBundlesPictureHeight form-control" value="<?php echo (isset($data['ProductBundles']['PictureHeight'])) ? $data['ProductBundles']['PictureHeight'] : '80' ?>" />
                  <span class="input-group-addon">px</span>
                </div>
            </div>
		</td>
  </tr>
  <tr>
       <td>
			<h5>Custom CSS:</h5>
       		<span class="help"><i class="fa fa-info-circle"></i>&nbsp;Place your custom CSS here.</span> 
       </td>
       <td>
			<div class="col-xs-4">
				<textarea rows="5" name="ProductBundles[CustomCSS]" placeholder="Custom CSS" class="ProductBundlesCustomCSS form-control"><?php echo (isset($data['ProductBundles']['CustomCSS'])) ? $data['ProductBundles']['CustomCSS'] : '' ?></textarea>
            </div>
       </td>
  </tr>
</table>