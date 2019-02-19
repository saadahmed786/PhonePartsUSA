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

// Text
$_['text_title']           = 'Credit Card / Debit Card (Authorize.Net-CIM)';
$_['text_credit_card']     = 'Credit Card Details';
$_['text_wait']            = 'Please wait!';

// Entry
$_['entry_cc_owner']       = 'Card Owner:';
$_['entry_cc_number']      = 'Card Number:';
$_['entry_cc_expire_date'] = 'Card Expiry Date:';
$_['entry_cc_cvv2']        = 'Card Security Code (CVV2):';

$_['entry_ba_bankname']       	= 'Name Of The Bank:';
$_['entry_ba_echecktype']       = 'Type Of Electronic Check Transaction:';
$_['entry_ba_nameonaccount']    = 'Full Name On The Accoun:';
$_['entry_ba_accountnumber']    = 'Account Number:';
$_['entry_ba_routingnumber']    = 'Routing Number:';
$_['entry_ba_accounttype']      = 'Type Of Account:';
$_['entry_checking']       		= 'Checking';
$_['entry_savings']       		= 'Savings';
$_['entry_businesschecking']    = 'Business Checking';
$_['not_supported']       		= 'Not Supported';
$_['text_error_required_field'] 	= 'Required field';


$_['text_cim_error_accured'] = 'An error Accured During conecting the Authnet...';
$_['text_cim_requires_account_and_login'] = 'To be able to use this module you have to <a href="%s">login</a>.';
$_['error_unknown_account_type_selected'] 	= 'Please Select One Of The Account Types.';
$_['text_error_cim_profile_notfound'] 	= 'UNKNOWN PAYMET PROFILE ERROR: Currenty we can not found your Cim profile. please go to your account page and create CIM payment profile.';

$_['text_select_cimcard']        = 'Card';
$_['text_select_cimadress']      = 'Replace shipping address';
$_['text_select_wanttouse_cim']  = 'I want to use an existing address';
$_['text_select_wanttouse_differentaccount']= 'I want to use different Account';
$_['text_select_select_cimcard']        = 'Select The Card You Want To Charge';
$_['text_select_select_adress']        = 'Select The Adress you want to replace';
$_['text_wanttouse_newcredit_card']= 'I want to use new credit card';
$_['text_wanttouse_bank_account']= 'I want to use new bank account';
$_['text_select_paymentaccount']        = 'Select Account You Want To Charge';


$_['text_error_duplicate_cim_account'] = 'AUTHNET DUPLICATE PAYMENT ACCOUNT ERROR: '.'You already have same account in the system please select your account from left dropdown box.';
$_['text_error_create_pament_account'] = 'AUTHNET CREATE PAYMENT ACCOUNT ERROR: '.'An error occurred during creating payment account.';
$_['text_error_create_profile'] = 'AUTHNET CREATE PROFILE ERROR: '.'An error occurred creating CIM profile.';
$_['text_error_create_transaction'] = 'AUTHNET CREATE TRANSECTION ERROR: '.'An error occurred creating transaction.';
$_['text_error_create_transaction_connecting'] = 'AUTHNET CREATE TRANSECTION ERROR: '.'An error occurred connecting CIM server please try againg.';
$_['text_error_select_payment_profile'] = 'UNKNOWN PAYMET PROFILE ERROR: '.'Please select one of payment profiles.';
$_['text_error_unselected_payment_profile'] = 'Please select payment profile from list.';

$_['text_error_required'] = 'This field required!';
$_['text_select_prfx_bank_account'] = 'Bank Account';
$_['text_select_prfx_credit_card'] = 'Credit Card';
$_['text_close'] = 'Close';

$_['text_cim_held_notify_subj']   	= 'Order Returned In Held Status!';
$_['text_cim_held_notify_message']   	= 'Please Check Following Order.\n';
$_['text_cim_held_user_message']   	= 'Uour Order is in progress we will review your cim-payment and aprove  it soon.';

?>