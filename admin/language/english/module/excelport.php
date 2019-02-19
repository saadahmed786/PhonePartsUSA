<?php
// Heading
$_['heading_title']						= 'ExcelPort 1.6.1';

// Text
$_['text_module']         				= 'Modules';
$_['text_success']						= 'Success: You have modified module ExcelPort!';
$_['text_activate']						= 'Activate';
$_['text_not_activated']				= 'ExcelPort is not activated.';
$_['text_click_activate']				= 'Activate ExcelPort';
$_['text_success_activation']			= 'ACTIVATED: You have successfully activated ExcelPort!';
$_['text_content_top']					= 'Content Top';
$_['text_content_bottom']				= 'Content Bottom';
$_['text_column_left']					= 'Column Left';
$_['text_column_right']					= 'Column Right';
$_['text_datatype_option_products']		= 'Products';
$_['text_datatype_option_categories']	= 'Categories';
$_['text_datatype_option_attributes']	= 'Attributes and Attribute Groups';
$_['text_question_data']				= 'What data do you wish to export?';
$_['text_question_store']				= 'Which store do you wish to export?';
$_['text_question_language']			= 'Which language do you wish to export?';
$_['text_note']							= 'Note:';
$_['text_supported_in_oc1541']			= 'This feature is available only for OpenCart 1.5.1.3 - %s. Please mind that your server can have a low memory limit.';
$_['text_learn_to_increase']			= 'Learn how to increase it.';
$_['text_feature_unsupported']			= 'This feature is supported only for OpenCart version {VERSION}';
$_['text_question_data_import']			= 'What data do you wish to import?';
$_['text_question_store_import']		= 'In which store do you wish to import?';
$_['text_question_language_import']		= 'Which language do you wish to import?';
$_['text_question_file_import']			= 'Please select the .xlsx or .zip file you wish to import:';
$_['text_file_generating']				= 'Generating file. Please wait...';
$_['text_file_downloading']				= 'Downloading file...';
$_['text_import_done']					= 'Import finished. {COUNT} {TYPE} were imported.';
$_['text_preparing_data']				= 'Preparing data...';
$_['text_export_entries_number']		= 'Number of entries per exported part<span class="help">Set this to a lower value if you experience memory issues. The lower the vlaue, the more exported files you will receive. Does not apply for Attributes.</span>';
$_['text_import_limit']					= 'Maximum entries to read on each step of the import.<span class="help">Default value is 100. Decrease it if you experience memory issues on Import. Does not apply for Attributes and Options.</span>';
$_['text_question_delete_other']		= 'Delete the entries before doing the import?';
$_['text_confirm_delete_other']			= 'This will delete all your entries before importing. It is advised to back up your database before the import. If you are sure you wish to continue, click OK.';
$_['text_question_type_export']			= 'Quick Export - One product per line, omitting dynamic data like Attributes, Options, Discounts, Specials, Images and Designs';
$_['text_question_add_as_new']			= 'Import products as new ones - This will disregard the Product ID field and will import your products as if they were new ones. Keep in mind that Related Products will not work for manually added ID&#39;s';
$_['text_toggle_filter']				= 'Toggle Filter';
$_['text_conjunction']					= 'Filters Conjunction';
$_['help_conjunction']					= 'If you use &quot;AND&quot;, then ALL of the conditiones must be met. If you choose &quot;OR&quot;, then the product will be listed if at least 1 condition is met.';
$_['text_the_value']					= 'the value';
$_['text_datatype_option_customers']	= 'Customers';
$_['text_datatype_option_customer_groups'] = 'Customer Groups';
$_['text_datatype_option_options']		= 'Options';

// Entry
$_['entry_code']						= 'ExcelPort status:<br /><span class="help">Enable or disable ExcelPort</span>';
$_['entry_layouts_active']				= 'Activated on:<br /><span class="help">Choose on which pages ExcelPort to be active</span>';

// Error
$_['error_permission']					= 'Warning: You do not have permission to modify module ExcelPort!';
$_['error_no_file']						= 'File was not uploaded.';

// Button
$_['button_export']						= 'Export Now';
$_['button_import']						= 'Import Data';
$_['button_add_condition']				= 'Add Filter';
$_['button_discard_condition']			= 'Discard Filter';

$_['excelport_unable_cache']			= 'Could not set cache storage method.';
$_['excelport_unable_upload']			= 'Temp file was not moved to the target folder.';
$_['excelport_invalid_file']			= 'File is invalid - it is either too large, or in a wrong format.';
$_['excelport_folder_not_string']		= 'The passed variable is not a string.';
$_['excelport_file_not_exists']			= 'The file you wish to import does not exist on the server.';
$_['excelport_export_limit_invalid'] 	= 'Ivalid entry number per file. Please set it between 50 and 800.';
$_['excelport_invalid_import_file']		= 'The imported file does not exist in the file system!';
$_['excelport_unable_zip_file_open']	= 'Cannot open zip file. It is probably corrupt.';
$_['excelport_unable_zip_file_extract'] = 'Cannot extract the zip file.';
$_['excelport_unable_create_unzip_folder'] = 'Cannot create the unzip folder.';
$_['excelport_import_limit_invalid']	= 'Ivalid entry import limit. Please set it between 10 and 800.';
$_['excelport_mode_unknown']			= 'The header of the imported table is invalid. Please rename the fields appropriately.';

$_['import_success']					= 'SUCCESS: The products have been imported.';
?>