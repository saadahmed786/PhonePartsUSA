<table class="table">
    <tr>
		<td class="col-xs-3">
        	<h5>Listing URL:</h5>
        	<span class="help"><i class="fa fa-info-circle"></i>&nbsp;This is the URL of the page.</span>
        </td>
        <td class="col-xs-9">
            <div class="col-xs-12">
                <a href="<?php echo HTTP_CATALOG."index.php?route=module/productbundles/listing"; ?>" target="_blank"><?php echo HTTP_CATALOG."index.php?route=module/productbundles/listing"; ?></a>
			</div>
       </td>
    </tr>
	<tr>
		<td class="col-xs-3">
        	<h5>Add link to the listing in the main menu:</h5>
        	<span class="help"><i class="fa fa-info-circle"></i>&nbsp;It may require some additional work to make it work on a highly customized themes.</span>
        </td>
        <td class="col-xs-9">
            <div class="col-xs-4">
                <select id="LinkChecker" name="ProductBundles[MainLinkEnabled]" class="form-control">
                    <option value="yes" <?php echo (!empty($data['ProductBundles']['MainLinkEnabled']) && $data['ProductBundles']['MainLinkEnabled'] == 'yes') ? 'selected=selected' : '' ?>>Enabled</option>
                    <option value="no"  <?php echo (empty($data['ProductBundles']['MainLinkEnabled']) || $data['ProductBundles']['MainLinkEnabled']== 'no') ? 'selected=selected' : '' ?>>Disabled</option>
                </select>
			</div>
       </td>
    </tr>
    <tbody id="MainLinkOptions">
	<tr>
		<td class="col-xs-3">
        	<h5>Menu Link Title:</h5>
        	<span class="help"><i class="fa fa-info-circle"></i>&nbsp;Specify the link title.</span>
        </td>
        <td class="col-xs-9">
            <div class="col-xs-4">
                <?php foreach ($languages as $language) { ?>
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo $language['name']; ?>:</span>
                        <input type="text" class="form-control" name="ProductBundles[LinkTitle][<?php echo $language['language_id']; ?>]" value="<?php if(isset($data['ProductBundles']['LinkTitle'][$language['language_id']])) { echo $data['ProductBundles']['LinkTitle'][$language['language_id']]; } else { echo "Bundles"; }?>" />
                    </div>
                    <br />
                <?php } ?>
           </div>
       </td>
    </tr>
    <tr>
		<td class="col-xs-3">
        	<h5>Menu Link Sort Order:</h5>
        	<span class="help"><i class="fa fa-info-circle"></i>&nbsp;Specify the link title.</span>
        </td>
        <td class="col-xs-9">
            <div class="col-xs-4">
				<input type="number" class="form-control" name="ProductBundles[LinkSortOrder]" value="<?php if(isset($data['ProductBundles']['LinkSortOrder'])) { echo $data['ProductBundles']['LinkSortOrder']; } else { echo "7"; }?>" />
           </div>
       </td>
    </tr>
    </tbody>
    <tr>
		<td class="col-xs-3">
        	<h5>Bundles per page:</h5>
        	<span class="help"><i class="fa fa-info-circle"></i>&nbsp;Define how many bundles to be shown on first page.</span>
        </td>
        <td class="col-xs-9">
            <div class="col-xs-4">
				<input type="number" name="ProductBundles[ListingLimit]" class="form-control" value="<?php echo (isset($data['ProductBundles']['ListingLimit'])) ? $data['ProductBundles']['ListingLimit'] : '10' ?>" /> 
			</div>
       </td>
    </tr>
	<tr>
		<td class="col-xs-3" style="vertical-align:top;">
        	<h5>SEO Options:</h5>
        	<span class="help"><i class="fa fa-info-circle"></i>&nbsp;Here you will find SEO options which (if used correctly) will boost your SEO rankings</span>
        </td>
        <td class="col-xs-9">
            <div class="col-xs-4">
                <ul class="nav nav-tabs" id="langtabs" role="tablist">
                  <?php foreach ($languages as $language) { ?>
                    <li><a href="#lang-<?php echo $language['language_id']; ?>" role="tab" data-toggle="tab"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>"/></a></li>
                  <?php } ?>
                </ul>
                <br />
                <div class="tab-content">
                  <?php foreach ($languages as $language) { ?>
                  	<div class="tab-pane" id="lang-<?php echo $language['language_id']; ?>">
                   		Page Title:<br />
						<input name="ProductBundles[PageTitle][<?php echo $language['language_id']; ?>]" class="form-control" type="text" value="<?php echo (isset($data['ProductBundles']['PageTitle'][$language['language_id']])) ? $data['ProductBundles']['PageTitle'][$language['language_id']] : 'Product Bundles'; ?>" />
                        <br />
                        Meta Description:<br />
						<textarea name="ProductBundles[MetaDescription][<?php echo $language['language_id']; ?>]" class="form-control" rows="4"><?php echo (isset($data['ProductBundles']['MetaDescription'][$language['language_id']])) ? $data['ProductBundles']['MetaDescription'][$language['language_id']] : 'Bundles with great discount! Only in example.com.'; ?></textarea>
                        <br />
                        Meta Keywords:<br />
						<input name="ProductBundles[MetaKeywords][<?php echo $language['language_id']; ?>]" class="form-control" type="text" value="<?php echo (isset($data['ProductBundles']['MetaKeywords'][$language['language_id']])) ? $data['ProductBundles']['MetaKeywords'][$language['language_id']] : 'product bundles, discount, products, get discount'; ?>" />
                        <br/>
                        SEO Slug:<br />
                        <input name="ProductBundles[SeoURL][<?php echo $language['language_id']; ?>]" class="form-control" type="text" value="<?php echo (isset($data['ProductBundles']['SeoURL'][$language['language_id']])) ? $data['ProductBundles']['SeoURL'][$language['language_id']] : 'bundles'; ?>" />
                        <span class="help"><i class="fa fa-info-circle"></i>&nbsp;Type only the slug that you want to use for SEO URL. For example, you want the bundle listing to be accessible from <em><?php echo HTTP_CATALOG."<strong>bundles</strong>"; ?></em>, type only <strong>bundles</strong> in the field above.</span>
                    </div>
                  <?php } ?>
                </div>
			</div>
       </td>
    </tr>
    <tr>
		<td class="col-xs-3">
        	<h5>Picture Width & Height:</h5>
            <span class="help"><i class="fa fa-info-circle"></i>&nbsp;In Pixels</span>
        </td>
		<td class="col-xs-9">
        	<div class="col-xs-4">
                <div class="input-group">
                  <span class="input-group-addon">Width:&nbsp;</span>
					<input type="text" name="ProductBundles[ListingPictureWidth]" class="ProductBundlesPictureWidth form-control" value="<?php echo (isset($data['ProductBundles']['ListingPictureWidth'])) ? $data['ProductBundles']['ListingPictureWidth'] : '120' ?>" />
				  <span class="input-group-addon">px</span>
                </div>
                <br />
                <div class="input-group">
                  <span class="input-group-addon">Height:</span>
					<input type="text" name="ProductBundles[ListingPictureHeight]" class="ProductBundlesPictureHeight form-control" value="<?php echo (isset($data['ProductBundles']['ListingPictureHeight'])) ? $data['ProductBundles']['ListingPictureHeight'] : '120' ?>" />
                  <span class="input-group-addon">px</span>
                </div>
            </div>
		</td>
  </tr>
  <tr>
       <td class="col-xs-3">
			<h5>Custom CSS:</h5>
       		<span class="help"><i class="fa fa-info-circle"></i>&nbsp;Place your custom CSS for the bundles listing here.</span> 
       </td>
       <td class="col-xs-9">
			<div class="col-xs-4">
				<textarea rows="5" name="ProductBundles[ListingCustomCSS]" placeholder="Custom CSS..." class="ProductBundlesCustomCSS form-control"><?php echo (isset($data['ProductBundles']['ListingCustomCSS'])) ? $data['ProductBundles']['ListingCustomCSS'] : '' ?></textarea>
            </div>
       </td>
  </tr>
</table>