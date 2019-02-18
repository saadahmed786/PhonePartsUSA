<?php
/**
 * Contains part of the Opencart Authorize.Net CIM Payment Module code.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to memiiso license.
 * Please see the LICENSE.txt file for more information.
 * All other rights reserved.
 *
 * @author     memiiso <gel.yine.gel@hotmail.com>
 * @copyright  2013-~ memiiso
 * @license    Commercial License. Please see the LICENSE.txt file
 */

// Heading
$_['heading_title']     = 'AuthorizeNet Customer Information Manager';

// Text
$_['text_account']      = 'Account';
$_['text_credit_card_entries'] = 'AuthorizeNet Credit Card List';
$_['text_bank_accont_entries'] = 'AuthorizeNet Bank Account List';
$_['text_adress_entries'] = 'Address Book Entries';


$_['text_edit_payment'] 		= 'Edit Payment Profile';
$_['text_create_newcredit_card']= 'Creadit Card';
$_['text_create_bank_account'] 	= 'Bank Account';
$_['button_cancel'] 			= 'Cancel';
$_['button_save'] 				= 'Save';
$_['button_new_pamet_account'] 	= 'New Payment Profile';
$_['button_set_default'] 	= 'Set As Default';

$_['entry_customer_type'] 	= 'Account Type';
$_['text_business'] 	= 'Business';
$_['text_individual'] 	= 'Individual';
$_['entry_cim_pa_billing_address'] 	= 'Billing Adress For Payment Account';



// Error
$_['text_error_required_field'] 	= 'Required field';
$_['text_error_duplicate_payment_account'] 	= 'You already have same account in the system. If you want to update accound details please delete it and add it with new details.';
$_['text_error_initial_profile']= 'An error accured during initial creatin of cim profile for your account.';
$_['text_error_delete_payment_profile'] = 'An error accured during deleting your payment profile.';
$_['error_unknown_account_type_selected'] 	= 'Please Select One Of The Account Types.';
$_['text_error_connecting_cim']  = 'An Error Accured Connecting Cim Service please Use Contact form and Notify admin.';
$_['text_error_connecting_cim_body']  = 'Our Team is working on this to fix the problem so hard please be patient..';
$_['text_error_email_subj']  = 'CIM Service Error Accured';
$_['text_error_email_message']  = 'Cim service error accured please check the logs for details.';
$_['text_error_already_have_same_pp']  = 'You already have same payment profile setup.';


$_['text_error_set_default_address']  = 'An error accured changing default CIM payment address!';
$_['text_success_set_default_address']  = 'Default CIM payment address successfully changed'; 
$_['text_error_set_default_payment']  = 'An error accured changing default CIM payment profile!';
$_['text_success_set_default_payment']  = 'Default CIM payment profile successfully changed';
$_['text_cim_payment_accounts']  = 'AuthorizeNet Payment Accounts';

$_['text_single_click_setup']  = 'text_single_click_setup';
$_['text_sc_billing_address']  = 'text_sc_billing_address';
$_['text_sc_shiping_address']  = 'text_sc_shiping_address';
$_['text_sc_shiping_method']  = 'text_sc_shiping_method';
$_['text_sc_payment_card']  = 'text_sc_payment_card';
$_['button_sc_save']  = 'button_sc_save';
$_['button_sc_select_shipping_address']  = 'Please select shipping adress from the list first.';

// success
$_['text_sucess_insert']       	= 'Your payment profile has been successfully created';
$_['text_sucess_update']       	= 'Your payment profile has been successfully updated';
$_['text_success_delete_payment_profile'] = 'Your payment profile has been successfully deleted';

$_['text_empty_bank_account'] = 'You do not have any stored Bank Account data at this time.<br> Use the New Payment Profile button to add Bank Account information for a faster shopping experience.';
$_['text_empty_credit_card'] = 'You do not have any stored Credit Cards at this time.<br> Use the New Payment Profile button to add a credit card for a faster shopping experience.';

$_['entry_telephone'] = 'Telephone:';
$_['error_telephone']      		= 'Telephone must be between 3 and 32 characters!';
$_['text_validate_billing_adress'] = 'Verify Billing Address';


?>