<?php
$_['extension_name']                    = 'Similar Products';

// Heading
$_['heading_title']                     = '<i class="fa fa-files-o" style="font-size:14px;color:#F7951D;"></i> <strong>' . $_['extension_name'] . '</strong>';

// Buttons
$_['button_apply']                      = 'Apply';
$_['button_upgrade']                    = 'Upgrade';

// Tabs
$_['tab_settings']                      = 'Settings';
$_['tab_modules']                       = 'Modules';
$_['tab_support']                       = 'Support';
$_['tab_about']                         = 'About';
$_['tab_general']                       = 'General';
$_['tab_faq']                           = 'FAQ';
$_['tab_services']                      = 'Services';
$_['tab_changelog']                     = 'Changelog';
$_['tab_extension']                     = 'Extension';

// Text
$_['text_success_upgrade']              = '<strong>Success!</strong> You have upgraded ' . $_['extension_name'] . ' to version <strong>%s</strong>!';
$_['text_success_update']               = '<strong>Success!</strong> You have updated ' . $_['extension_name'] . ' settings!';
$_['text_toggle_navigation']            = 'Toggle navigation';
$_['text_license']                      = 'License';
$_['text_extension_information']        = 'Extension information';
$_['text_legal_notice']                 = 'Legal notice';
$_['text_terms']                        = 'Terms &amp; Conditions';
$_['text_support_subject']              = $_['extension_name'] . ' support needed';
$_['text_license_text']                 = 'Please be aware that this product has a <strong>per-domain license</strong>, meaning you can use it <em>only on a single domain</em> (sub-domains count as separate domains). <strong>You will need to purchase a separate license for each domain you wish to use this extension on.</strong>';
$_['text_other_extensions']             = 'If you like this extension you might also be interested in <a href="%s" class="alert-link" target="_blank">my other extensions</a>.';
$_['text_module']                       = 'Modules';
$_['text_faq']                          = 'Frequently Asked Questions';
$_['text_content_tab']                  = 'Content Tab';
$_['text_content_top']                  = 'Content Top';
$_['text_content_bottom']               = 'Content Bottom';
$_['text_column_left']                  = 'Column Left';
$_['text_column_right']                 = 'Column Right';
$_['text_random']                       = 'Random';
$_['text_most_viewed']                  = 'Most Viewed';
$_['text_date_added']                   = 'Date Added';
$_['text_date_modified']                = 'Date Modified';
$_['text_name']                         = 'Product Name';
$_['text_sort_order']                   = 'Sort Order';
$_['text_model']                        = 'Model';
$_['text_quantity']                     = 'Quantity';
$_['text_no_modules']                   = 'No modules have been added';
$_['text_off']                          = 'Off';
$_['text_category']                     = 'Category';
$_['text_tags']                         = 'Product Tags';
$_['text_name_fragment']                = 'Product Name Fragment';
$_['text_model_fragment']               = 'Product Model Fragment';
$_['text_name_custom_string']           = 'Custom String in Product Name';
$_['text_model_custom_string']          = 'Custom String in Product Model';
$_['text_autocomplete']                 = '(Autocomplete)';
$_['text_remove']                       = 'Remove';
$_['text_no_products']                  = 'No products';
$_['text_all_products']                 = 'All products';
$_['text_all_empty_products']           = 'All products with empty content';
$_['text_all_category_products']        = 'All products from the following category';
$_['text_selected_products']            = 'Selected products';
$_['text_change_product_settings']      = 'Change product ' . $_['extension_name'] . ' settings';

// Help texts
$_['help_remove_sql_changes']           = 'Remove all SQL changes when <strong>uninstalling</strong> the module.';
$_['help_auto_select']                  = 'Automatically select similar products from the current category or by name fragment.';
$_['help_leaves_only']                  = 'If no category is present, select similar products from the leaf categories the current product belongs to. Otherwise select also from non-leaf categories.';
$_['help_stock_only']                   = 'Show only products currently in stock.';
$_['help_lazy_load']                    = 'Load module content when the customer has the module in viewport.';
$_['help_name_fragment']                = 'Automatically select products that match <b>length</b> characters of product name/model starting from offset <b>start</b>.';
$_['help_custom_string']                = 'Automatically select products that contain the specified string in the product name/model.';
$_['help_change_product_settings']      = 'Change the auto select and product sort order settings for the following products. <em>This is a one-time operation!</em>';

// Entry
$_['entry_installed_version']           = 'Installed version:';
$_['entry_extension_status']            = 'Extension status:';
$_['entry_name']                        = 'Name:';
$_['entry_layout']                      = 'Layout:';
$_['entry_limit']                       = 'Limit:';
$_['entry_image_width']                 = 'Image width:';
$_['entry_image_height']                = 'Image height:';
$_['entry_position']                    = 'Position:';
$_['entry_status']                      = 'Status:';
$_['entry_module_sort_order']           = 'Module sort order:';
$_['entry_product_sort_order']          = 'Product sort order:';
$_['entry_products_per_page']           = 'Products per page:';
$_['entry_stock_only']                  = 'Stock only:';
$_['entry_lazy_load']                   = 'Lazy load:';
$_['entry_auto_select']                 = 'Auto select:';
$_['entry_leaves_only']                 = 'Leaves only:';
$_['entry_substr_start']                = 'Substring start:';
$_['entry_substr_length']               = 'Substring length:';
$_['entry_custom_string']               = 'Custom string:';
$_['entry_remove_sql_changes']          = 'Remove SQL changes:';
$_['entry_products']                    = 'Products:';
$_['entry_extension_name']              = 'Name:';
$_['entry_extension_compatibility']     = 'Compatibility:';
$_['entry_extension_store_url']         = 'Store URL:';
$_['entry_copyright_notice']            = 'Copyright notice:';

// Error
$_['error_permission']                  = '<strong>Error!</strong> You do not have permission to modify extension ' . $_['extension_name'] . '!';
$_['error_warning']                     = '<strong>Warning!</strong> Please check the form carefully for errors!';
$_['error_vqmod']                       = '<strong>Error!</strong> vQmod does not seem to be installed. <a href="http://code.google.com/p/vqmod/" class="alert-link">Get vQmod!</a>';
$_['error_missing_table']               = '<strong>Error!</strong> Your SQL database seems to be missing table \'%s\'!';
$_['error_missing_column']              = '<strong>Error!</strong> Your SQL table \'%s\' seems to be missing column \'%s\'!';
$_['error_unsaved_settings']            = '<strong>Warning!</strong> There are unsaved settings! Please save the settings.';
$_['error_version']                     = '<strong>Info!</strong> ' . $_['extension_name'] . ' version <strong>%s</strong> installation files found. You need to upgrade ' . $_['extension_name'] . '!';
$_['error_upgrade_database']            = '<strong>Error!</strong> Failed to upgrade database structure!';
$_['error_positive_integer']            = 'This value must be a positive integer greater than 0!';
$_['error_layout']                      = '<strong>Warning!</strong> Could not find layouts with route \'product/product\'. Please insert at least one layout with route \'product/product\'!';
$_['error_module_name']                 = 'Please enter a module name!';
$_['error_ajax_request']                = 'An AJAX error occured!';
?>
