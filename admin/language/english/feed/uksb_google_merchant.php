<?php
// Heading
$_['heading_title']   		 = 'UKSB Google Merchant v4.0.2';

// Text   
$_['text_feed']      	 	 = 'Product Feeds';
$_['text_success']    	 	 = 'Success: You have modified the UKSB Google Merchant feed!';
$_['text_model']			 = 'Model field';
$_['text_location']			 = 'Location field';
$_['text_gtin']				 = 'GTIN field';
$_['text_mpn']				 = 'MPN field';
$_['text_upc']				 = 'UPC field';
$_['text_none']				 = ' --- None --- ';
$_['text_sku']				 = 'SKU field';
$_['text_video_tutorials_info'] = '';
$_['text_initialise_data'] = 'Create Data';
$_['text_initialise_data_text'] = '<p>Depending on the size of your store and the number of products you have, installing this extension or upgrading from a previous version may take some time.</p><p style="color:red;">If you have not already done so, please make sure you have set the store into maintenance mode, (as per the Install instructions documentation) and have done a full database backup.</p><p>If you have not enabled mantenance mode, visitors will most likely see a lot of errors on the front end.</p><p>When ready, please click the button below.</p><p style="color:red;">If the server times out, you can run this process again to continue where it left off.</p>';

// Entry
$_['tab_general_settings']	 =	'General Settings';
$_['tab_google_settings']	 =	'Google Merchant Settings';
$_['tab_google_feeds']	 =	'Google Merchant Feeds';
$_['tab_bing_feeds']	 =	'Bing US';
$_['tab_utilities']	 =	'Utilities';
$_['tab_videos']	 =	'Video Tutorials';

$_['entry_variant_section']	 =	'Clothing &amp; Apparel and Variant Products';
$_['entry_adwords_section']	 =	'Google Adwords Attributes';
$_['entry_status']    	 	 = 'Status:';
$_['entry_google_category']  = 'Default Google Product Category:';
$_['entry_choose_google_category']  = 'Click Here to choose your default Google Product Category';
$_['entry_choose_google_category_xml']  = 'Click the green \'+\' icon to choose your Google Product Category for each Google Site you have chosen to list on.';
$_['entry_condition']  			 = 'Condition:';
$_['entry_mpn']  			 = 'Manufacturer\'s Part Number:';
$_['entry_gtin']  			 = 'EAN or UPC or ISBN Number:';
$_['entry_gender']  			 = 'Gender:';
$_['entry_age_group']  			 = 'Age Group:';
$_['entry_characters']  	 = 'Fix Non-Standard Characters:';
$_['entry_split'] 			 = 'Split Feed:';
$_['entry_cron'] 			 = 'Advanced Feed Creation:';
$_['entry_site'] 			 = 'Google Shopping Site:';
$_['entry_info']  			 = 'Information:';
$_['entry_data_feed']   	 = 'Data Feed Url:';
$_['entry_cron_code']   	 = 'Cron Command:';

// Help
$_['help_google_category']	 = 'You should choose here the default Google Product Category that best fits the majority of your products in the country(s) you will be listing on.<br /><br />These can then be overridden in the Feed tab when editing a category and likewise when editing a product.';
$_['help_brand']			 = 'By default this is set to use the Manufacturer/Brand you assign via the links tab when editing a product.<br /><br />However, you can choose to ONLY use the new Brand field added by this extension in the new Feed tab when editing a product if your OC Manufacturers are not specific enough.';
$_['help_condition']				 = 'You can choose the default condition of your products here.<br /><br />This can be overridden on the Feed tab when editing a product.';
$_['help_mpn']				 = 'By default this is set to use the Model field from the product data tab when editing a product.<br /><br />However, you can choose to use the MPN field (recommended) when editing a product.';
$_['help_gtin']				 = 'By default this is set to use the UPC field from the product data tab when editing a product.<br /><br />However, you can choose to use the GTIN field (recommended) added by this extension on the Feed tab when editing a product, if your store is new or has few or zero products.';
$_['help_gender']				 = 'If your products are mainly for one specific gender, you can choose that here.<br /><br />On products where the gender is other than that chosen here, you can override this default on those individual products.';
$_['help_age_group']				 = 'If your products are mainly for a specific age group, you can choose that here.<br /><br />On products where the age group is other than that chosen here, you can override this default on those individual products.';
$_['help_characters']		 = 'Setting this option to Enabled will attempt to fix any XML errors caused by non standard or incorrectly encoded characters.';
$_['help_split']			 = 'If your server is timing out or runs out of memory due to your store having a lot of products, you can choose to split your feed into multiple feeds containing the number of products you set here.';
$_['help_split_help']	     = 'Please Save the feed settings, then return here to see your new Data Feed Url\'s on the Google Merchant Feeds tab.';
$_['help_cron']			 = 'Rather than using the Split Feed option which results in having several feeds that need submitting individually to Google, you can set up a cron job which will run automatically and which will generate one large feed. See the Google Merchant Feeds tab for the path to use for each country\'s feed.<br><br>Enabling this feature will Disable the Split Feed option.';
$_['help_cron_code']			 = '<h2>Cron Help</h2><p>If using the cron method for auto generating your feeds, you should set the timing of the cron job to be at least one hour before the time you schedule with Google to fetch your feed. This will give the script enough time to generate the feed file.</p><p style="color:red;">You should consult your server host with any questions you have for setting up a cron job. There can be different set-ups on various servers. The cron commands below may not work on your server environment, but should be able to be tweaked by your host.</p>';
$_['help_site']		  		 = 'You can choose to list on multiple Google Shopping Sites by choosing the Google Shopping site here.<br /><br />PLEASE NOTE - You must have the correct currency and language installed in OC and live on your store for each site you wish to list on.<br /><br />After choosing a site, the Data Feed URL will change to suit.';
$_['help_info']				 = 'This Extension is brought to you by <a onclick="window.open(\'http://www.uksitebuilder.net\',\'uksb\');" title="Web Design, E-Commerce Solutions and Application Deveopment">UK Site Builder Ltd</a>.<br />For more great OpenCart extensions, please visit <a onclick="window.open(\'http://www.opencart.com/index.php?route=extension/extension&filter_username=uksitebuilder\',\'extensions\');">UKSB OpenCart Extensions</a>.';
$_['warning_nogpc']	 	 = '<span style="color:red;"><b>Warning</b></span><br /><br />You have not chosen a Default Google Product Category in the UKSB Google Merchant Feed settings.<br /><br />If the Google site you are wishing to list on requires a Google Product Category, you should set one now in the Feed Settings.<br /><br /><span style="color:red;">Google Product Categories are NOT required for the following Google Shopping sites:</span><br /><br /><b>Austria, Belgium, Canada, Denmark, India, Mexico, Norway, Poland, Russia, Sweden and Turkey.</b>';

// Error
$_['error_permission'] 		 = 'Warning: You do not have permission to modify the UKSB Google Merchant feed!';
$_['error_duplicate'] 		 = 'Warning: You cannot have the same fields for both (Manufacturer\'s Part Number) and (EAN or UPC or ISBN Number)!';

// Google Merchant Edit Product
$_['tab_google']			 = 'Feed';
$_['entry_p_on_google']		 = 'List on Google Shopping:<br/><span class="help">You can stop individual products from being listed on Google Shopping with this setting.</span>';
$_['help_p_on_google']		 = '<span class="help">If you wish to stop a currently listed item from being listed on Google Shopping,<br />you should manually delete it in your Google Merchant account.<br />Otherwise, it will continue to be listed for 30 days or until the date you set in the Expiry Date field below.</span>';
$_['entry_p_expiry_date']			 = 'Expiry Date:<br/><span class="help">You can set an expiry date for this product for it to stop being listed on Google Shopping. Once the expry date has passed, the List on Google Shopping option will revert to \'No\' in the feed.</span>';
$_['entry_p_identifier_exists']	 = 'Identifier Exists:<br/><span class="help">Select \'No\' if you are not submitting Unique Product Identifiers for this product.</span>';
$_['entry_p_condition']		 = 'Condition:<br/><span class="help">You can override the default condition for this product here.</span>';
$_['entry_p_brand']			 = 'Brand:<br/><span class="help">You can override the Brand chosen in the Links tab here if you wish to be more specific.</span>';
$_['entry_p_mpn']			 = 'MPN:<br/><span class="help">Manufacturer\'s Part Number.<br />This value will be ignored if using Product Variants (see below).</span>';
$_['entry_p_gtin']			 = 'GTIN:<br/><span class="help">EAN, UPC or ISBN Number.<br />This value will be ignored if using Product Variants (see below).</span>';
$_['entry_p_google_category'] = 'Google Product Category:';
$_['link_google_category']	 = 'Click Here to choose your Google Product Category';
$_['help_p_google_category']	 = 'This will override the default Google Product Category set in the Product Feed settings';
$_['entry_p_multipack']			 = 'Multipack:<br/><span class="help">Enter the number of identical items in this multipack only if you personally have created the multipack.</span>';
$_['entry_p_is_bundle']			 = 'Bundle:<br/><span class="help">If this item has various products, choose \'Yes\'.</span>';
$_['entry_p_adult']			 = 'Adult:<br/><span class="help">If not all products you are listing are classed as Adult products, you can specify individual products as being Adult.</span>';
$_['entry_p_energy_efficiency_class']			 = 'Energy Class:<br/><span class="help">Recommended if applicable for electrical items.</span>';
$_['entry_p_unit_pricing_measure']			 = 'Unit Pricing Measure:<br/><span class="help">Numerical value + unit. Weight (mg, g, kg), volume (ml, cl, l, cbm), length (cm, m) and area (sqm) are supported.</span>';
$_['entry_p_unit_pricing_base_measure']			 = 'Unit Pricing Base Measure:<br/><span class="help">Integer value + unit. Weight (mg, g, kg), volume (ml, cl, l, cbm), length (cm, m) and area (sqm) are supported. </span>';
$_['entry_p_gender']			 = 'Gender:';
$_['entry_p_age_group']		 = 'Age Group:';
$_['entry_p_size_type']			 = 'Size Type:<br/><span class="help">Recommended for Clothing items.</span>';
$_['entry_p_size_system']			 = 'Size System:<br/><span class="help">Size system of this product.</span>';
$_['entry_p_colour']			 = 'Colour:';
$_['entry_p_size']			 = 'Size:';
$_['entry_p_material']		 = 'Material:';
$_['entry_p_pattern']		 = 'Pattern:';
$_['entry_v_mpn']			 = 'MPN:';
$_['entry_v_gtin']			 = 'GTIN:';
$_['entry_v_prices']			 = 'Price Difference:<br/><span class="help">Based on the price entered on the Data tab,<br />enter here the amount to add on or subtract<br />from that price for this variant.<br /><br />Add on: +5.00<br />Subtract: -5.00</span>';
$_['button_remove']			 = 'Remove';
$_['button_add_variant']			 = 'Add Variant';
$_['help_p_custom_label'] = '<span class="help">Custom Labels can be used to group the items in a Shopping campaign by values of your choosing, such as seasonal or clearance, etc.</span>';
$_['entry_p_custom_label_0']			 = 'Custom Label 0:';
$_['entry_p_custom_label_1']			 = 'Custom Label 1:';
$_['entry_p_custom_label_2']			 = 'Custom Label 2:';
$_['entry_p_custom_label_3']			 = 'Custom Label 3:';
$_['entry_p_custom_label_4']			 = 'Custom Label 4:';
$_['entry_p_adwords_redirect']	 = 'Adwords Redirect:<br/><span class="help">Allows you to override the product URL when the product is shown within the context of Product Ads.<br />This allows you to track different sources of traffic separately from Google Shopping.</span>';
$_['entry_help_section']			 = 'Google Help';
$_['help_google_help'] = '<span class="help">If you require more information on what you need to enter for your product type(s), the following links from Google will help.<br /><br />
<a onclick="window.open(\'https://support.google.com/merchants/answer/1344057?hl=en-GB\');">Summary of Attribute Requirements</a><br /><br />
<a onclick="window.open(\'https://support.google.com/merchants/answer/188494?hl=en-GB\');">Products Feed Specification</a><br /><br />
<a onclick="window.open(\'https://support.google.com/merchants/answer/160161?hl=en-GB&ref_topic=3404778\');">Unique Product Identifiers (MPN, GTIN, Brand)</a><br /><br />
<a onclick="window.open(\'https://support.google.com/merchants/answer/1347943?hl=en-GB&ref_topic=3404778\');">Submit Clothing Products</a>
</span>';

$_['text_condition_new']	 = 'New';
$_['text_condition_used']	 = 'Used';
$_['text_condition_ref']	 = 'Refurbished';
$_['text_male']				 = 'Male';
$_['text_female']			 = 'Female';
$_['text_unisex']			 = 'Unisex';
$_['text_newborn']				 = 'Newborn';
$_['text_infant']				 = 'Infant';
$_['text_toddler']				 = 'Toddler';
$_['text_kids']				 = 'Kids';
$_['text_adult']			 = 'Adult';
$_['text_regular']				 = 'Regular';
$_['text_petite']				 = 'Petite';
$_['text_plus']				 = 'Plus';
$_['text_big_and_tall']				 = 'Big and Tall';
$_['text_maternity']				 = 'Maternity';
$_['warning_mpn_model']		 = 'UKSB Google Merchant Feed Settings are currently set to use the Model field on the Data tab for MPN';
$_['warning_mpn_location']	 = 'UKSB Google Merchant Feed Settings are currently set to use the Location field on the Data tab for MPN';
$_['warning_mpn_sku']	 	 = 'UKSB Google Merchant Feed Settings are currently set to use the SKU field on the Data tab for MPN';
$_['warning_gtin_upc']		 = 'UKSB Google Merchant Feed Settings are currently set to use the UPC field on the Data tab for GTIN';
$_['warning_gtin_location']	 = 'UKSB Google Merchant Feed Settings are currently set to use the Location field on the Data tab for GTIN';
$_['warning_gtin_sku']	 	 = 'UKSB Google Merchant Feed Settings are currently set to use the SKU field on the Data tab for GTIN';

$_['utilities1'] = 'Display ALL products on Google Shopping';
$_['utilities2'] = 'Disable display of ALL products on Google Shopping';
$_['utilities3'] = 'Set Identifier Exists to TRUE for ALL products';
$_['utilities4'] = 'Set Identifier Exists to FALSE for ALL products';
$_['utilities5'] = 'Clear ALL Google Product Categories from ALL Products';
$_['utilities6'] = 'Clear ALL Google Product Categories from ALL Categories';
$_['utilities7'] = 'Set Condition of ALL products to New';
$_['utilities8'] = 'Set Condition of ALL products to Used';
$_['utilities9'] = 'Set Condition of ALL products to Refurbished';
$_['utilities_confirm'] = 'Are you sure you want to perform this action?';

$_['button_run'] = 'Run Action';

?>