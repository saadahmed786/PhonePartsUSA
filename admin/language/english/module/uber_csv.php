<?php
//-----------------------------------------
// Author: Qphoria@gmail.com
// Web: http://www.OpenCartGuru.com/
//-----------------------------------------

// Heading
$_['heading_title']    = 'Uber CSV';

// Text
$_['text_backup']      = 'Download Backup';
$_['text_module']      = 'Module';
$_['text_challenge']   = 'Are you sure?';
$_['text_success']     = 'Success: You have successfully imported your csv file! Updated {updated} Rows. Inserted {inserted} Rows.';

$_['tab_support']      = 'Support';

// Buttom
$_['button_export']     = 'Export CSV File';
$_['button_import']     = 'Import CSV File';

// Entry
$_['entry_export']     = 'Export:';
$_['entry_import']     = 'Import:';
$_['entry_truncate']   = 'Truncate Existing Data<br/><span class="help">Delete all data first before import.</span>';
$_['entry_table']      = 'Tables:';
$_['entry_columns']    = 'Columns:';
$_['entry_start']      = 'Starting Row:';
$_['entry_end']        = 'Ending Row:';
$_['help_columns']     = '<span class="help">Hold Ctrl+Click to choose multiple columns or Ctrl+A to choose all columns. The primary key will automatically be exported as that is needed for tracking back to the original record.</span>';
$_['help_start_end']   = '<span class="help">Leave blank or 0 to export all rows</span>';

// Error
$_['error_permission'] = 'Warning: You do not have permission to modify this module!';
$_['error_empty']      = 'Warning: The file you uploaded was empty. Operation Aborted!';
$_['error_primary_keys'] = 'Warning: The file was missing at least one of the required primary keys for the table. Operation Aborted!';
$_['error_no_rows']    = 'Warning: There were no rows updated or inserted. Operation Aborted!';
$_['error_no_table']   = 'Warning: You must select a table. Operation Aborted!';
$_['error_no_columns'] = 'Warning: You must select at least one column. Operation Aborted!';
?>