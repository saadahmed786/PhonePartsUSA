<?php
//==============================================================================
// Payment-Based Fee/Discount v155.1
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================

$name = 'Payment';
$type = 'Fee/Discount';
$version = 'v155.1';

// Heading
$_['heading_title']					= $name . '-Based ' . $type;

// Extension-specific
$_['entry_extensions']				= $name . ' Methods';
$_['help_extensions']				= 'Select the ' . strtolower($name) . ' method(s) to which the fee/discount applies. Disabled ' . strtolower($name) . ' methods are displayed in italics.';
$_['text_percentage_charges_use']	= 'Percentage Charges Use';
$_['text_shipping']					= 'Shipping Cost';

$name = 'Formula';

// Buttons
$_['button_save_exit']				= 'Save & Exit';
$_['button_save_keep_editing']		= 'Save & Keep Editing';
$_['button_show_examples']			= 'Show Examples';
$_['button_add_row']				= 'Add Rate';

// Extension Settings
$_['entry_status']					= 'Status:';
$_['help_status']					= 'The status for the extension as a whole.';
$_['entry_sort_order']				= 'Sort Order:';
$_['help_sort_order']				= 'The sort order for the extension, relative to other ' . strtolower($type) . ' extensions.';
$_['entry_heading']					= 'Heading:';
$_['help_heading']					= 'The heading under which these shipping options will appear. HTML is supported.';
$_['entry_round_final_costs']		= 'Round Final Costs to the Nearest:';
$_['help_round_final_costs']		= 'If you want to round the final calculated charges, enter the rounding value to use. For example, to round to the nearest quarter of a dollar, enter 0.25';

// General Settings
$_['entry_general_settings']		= 'General Settings';
$_['help_general_settings']			= '
	<em>Title</em><br />
	Set the rate title for each language. HTML is supported.<br /><br />
	<em>Sort Order</em><br />
	Set the order in which the rates will appear. Rates with the same sort order will be combined using the "Multi Rate Calculation" setting. Make sure you set the same Title and Multi Rate Calculation for rates with the same Sort Order.<br /><br />
	<em>Multi Rate Calculation</em><br />
	Set the calculation used to combine rates of the same Sort Order.<br /><br />
	<em>Tax Class</em><br />
	Optionally select a tax class for the rate. If applying a tax class, be sure to set the Sort Order for the extension to something lower than the "Taxes" Order Total.<br /><br />'
	. ($type == 'Shipping' ? '' : '
	<em>Geo Zone Comparison</em><br />
	Select whether to compare geo zones against the customer\'s shipping address or payment address. Until the customer begins the checkout process or uses the shipping estimator, the customer\'s default address will be used if logged in, and the store\'s default address will be used if not logged in.<br /><br />
	') . ($name == 'Formula' ? '' : '
	<em>Compare Against</em><br />
	Select whether to compare values against the entire cart, or just applicable items in the selected ' . $plural . '.<br /><br />
	') . '
	<em>Value for Total</em><br />
	Select the value used for Total-Based comparisons and percentage charges: the cart\'s Pre-Discounted Sub-Total (using all products\' original prices instead of Special or Discount prices), Sub-Total, Taxed Sub-Total, or Total (at the relative Sort Order of ' . ($type == 'Shipping' ? 'the "Shipping" Order Total). Products that do not require shipping are NOT included in the sub-total or taxed sub-total.' : 'this extension).');
$_['text_title']					= 'Title';
$_['text_internal_notes']			= 'Internal Notes';
$_['text_tax_class']				= 'Tax Class';
$_['text_sort_order']				= 'Sort Order';
$_['text_multi_rate_calculation']	= 'Multi Rate Calculation';
$_['text_average']					= 'Average';
$_['text_highest']					= 'Highest';
$_['text_lowest']					= 'Lowest';
$_['text_sum']						= 'Sum';
$_['text_geo_zone_comparison']		= 'Geo Zone Comparison';
$_['text_shipping_address']			= 'Shipping Address';
$_['text_payment_address']			= 'Payment Address';
$_['text_compare_against']			= 'Compare Against';
$_['text_entire_cart']				= 'Entire Cart';
$_['text_applicable_items']			= 'Applicable Items';
$_['text_value_for_total']			= 'Value for Total';
$_['text_prediscounted_subtotal']	= 'Pre-Discounted Sub-Total';
$_['text_subtotal']					= 'Sub-Total';
$_['text_taxed_subtotal']			= 'Taxed Sub-Total';
$_['text_total']					= 'Total';

// Order Criteria
$_['entry_order_criteria']			= 'Order Criteria';
$_['help_order_criteria']			= '
	<em>Stores</em><br />
	Select the stores for which the rate is available.<br /><br />
	<em>Currencies</em><br />
	Select the currencies for which the rate is available. "Convert Unselected" will automatically convert the cart total for unselected currencies, based on your currency rates.<br /><br />
	<em>Customer Groups</em><br />
	Select the customer groups for which the rate is available. "Not Logged In" will make the rate available to any customer not currently logged in.<br /><br />
	<em>Geo Zones</em><br />
	Select the geo zones for which the rate is available. "All Other Zones" will make the rate available to all locations not within a geo zone.';
$_['text_stores']					= 'Stores';
$_['text_currencys']				= 'Currencies';
$_['text_convert_unselected']		= '<em>Convert Unselected</em>';
$_['text_customer_groups']			= 'Customer Groups';
$_['text_not_logged_in']			= '<em>Not Logged In</em>';
$_['text_geo_zones']				= 'Geo Zones';
$_['text_all_other_zones']			= '<em>All Other Zones</em>';

// Cart Criteria
$_['entry_cart_criteria']			= 'Cart Criteria';
$_['help_cart_criteria']			= '
	<em>Add</em><br />
	Optionally enter an additional factor (positive or negative, decimal or percentage) to be added to the cart values before calculations occur. Leave the field blank to have no adjustment on that value.<br /><br />
	<em>Min / Max</em><br />
	Optionally enter minimum and/or maximum values the cart must have for the rate to be enabled. Leave fields blank to have no restrictions on that criteria.<br /><br />
	<em>Postcodes</em><br />
	Enter postcode ranges using hyphens, separated by commas. For example, to restrict the rate to postcodes starting with 902 and CX, you would enter 902-90299, CX-CXZZZ.<br /><br />
	<em>Postcode Format</em><br />
	Select whether to read the postcode entered by the customer in a particular format.<br /><br />
	<em>Dates</em><br />
	Enter the starting and ending date for the rate. Dates are inclusive, so if you set it to end on the 15th of a month, that will be the last day it is active.';
$_['text_add']						= 'Add';
$_['text_min']						= 'Min';
$_['text_max']						= 'Max';
$_['text_length']					= 'Item Length';
$_['text_width']					= 'Item Width';
$_['text_height']					= 'Item Height';
$_['text_item']						= 'Item';
$_['text_volume']					= 'Volume';
$_['text_weight']					= 'Weight';
$_['text_postcode']					= 'Postcode';
$_['text_postcode_format']			= 'Postcode Format';
$_['text_uk_format']				= 'UK Format';
$_['text_date_start']				= 'Date Start';
$_['text_date_end']					= 'Date End';

// Cost Brackets
$_['entry_cost_brackets']			= 'Cost Brackets';
$_['help_cost_brackets']			= '
	<em>Rate Type</em><br />
	The type of comparison for "From" and "To" values.<br /><br />
	<em>Notepad Icon</em><br />
	Click this to paste or enter tab-delimited data from a spreadsheet. Note that the new data will replace any current cost brackets.<br /><br />
	<em>From / To</em><br />
	The min and max bracket values (inclusive). If left blank, these will default to 0 and 999999, respectively. For Postcode-Based rates, blank fields will match all postcodes.<br /><br />
	<em>Charge</em><br />
	The ' . strtolower($type) . ' charge, as a flat rate or percentage of the total.' . ($type == 'Shipping' ? ' Use negative values to disable a rate when combined with other rates.' : ' Use positive values for fees and negative values for discounts.') . '<br /><br />
	<em>Per (unit)</em><br />
	An optional per-unit multiplier. For Total-Based rates, Per (&curren;) means per-currency-unit, such as Per ($) or Per (&euro;).<br /><br />
	<em>Final Cost</em><br />
	Select "Single" to charge only the highest bracket reached, and "Cumulative" to charge the cumulative total of all brackets reached. If needed, fill in an additional (Add) flat rate or percentage to add or subtract to the final cost, and an overall minimum (Min) and maximum (Max) final cost.';
$_['text_rate_type']				= 'Rate Type';
$_['text_based']					= '-Based';
$_['text_from']						= 'From';
$_['text_to']						= 'To';
$_['text_charge']					= 'Charge';
$_['text_per']						= 'Per';
$_['text_final_cost']				= 'Final Cost';
$_['text_single']					= 'Single';
$_['text_cumulative']				= 'Cumulative';
$_['help_cost_brackets_dialog']		= 'Paste or enter the From, To, Charge, and Per (unit) values in a tab-separated format. Any column can be left blank to not set a value for it. For example, to charge $5.00 for the first 3 items, and $1.25 for each item after that, select "Item-Based" and "Cumulative" and enter:<br /><pre style="margin-top: 5px">0	3	5.00<br />3	99999	1.25	1</pre>';

// Actions and Examples
$_['help_actions']					= '
	<em>Remove</em><br />
	Click the red minus button to remove the rate. Note that if it is the last rate remaining, it will instead be cleared of all values.<br /><br />
	<em>Copy</em><br />
	Click the gray and blue button to copy the rate to the end of the list.';
$_['help_examples']					= '
	<h2 class="selected">Example 1</h2><h2>Example 2</h2><h2>Example 3</h2>
	<div id="example1">
		Suppose you want to charge a $3.00 fee for PayPal, a $1.00 per item fee for Authorize.net, and a 5% fee of the entire cart\'s taxed sub-total for all other payment methods. You want the minimum fee to always be at least $3.00. Then you would enter:<br /><br />
		<strong>RATE #1</strong>
		<ul>
			<li><strong>Payment Methods:</strong> PayPal</li>
			<li><strong>Rate Type:</strong> (any)</li>
			<li><strong>Cost Brackets:</strong><br />
			- From: 0, To: 99999, Charge: 3.00</li>
		</ul>
		<strong>RATE #2</strong>
		<ul>
			<li><strong>Payment Methods:</strong> Authorize.net</li>
			<li><strong>Rate Type:</strong> Item-Based</li>
			<li><strong>Cost Brackets:</strong><br />
			- From: 0, To: 99999, Charge: 1.00, Per (#): 1</li>
			<li><strong>Final Cost Min:</strong> 3.00<br />
		</ul>
		<strong>RATE #3</strong>
		<ul>
			<li><strong>Payment Methods:</strong> (all except PayPal, Authorize.net)</li>
			<li><strong>Value for Total:</strong> Taxed Sub-Total</li>
			<li><strong>Percentage Charges Use:</strong> Total</li>
			<li><strong>Rate Type:</strong> (any)</li>
			<li><strong>Cost Brackets:</strong><br />
			- From: 0, To: 99999, Charge: 5%</li>
			<li><strong>Final Cost Min:</strong> 3.00<br />
		</ul>
	</div>
	<div id="example2" style="display: none">
		Suppose for Bank Transfers, within the U.S. you want to give a $2.00 discount for volumes up to 75 cubic inches, and a $5.00 discount for volumes greater than that. For non-U.S. locations, you want to give a $10.00 discount for volumes up to 120 cubic inches, and a $20.00 discount for volumes greater than that.<br /><br />
		Before taking the volume for calculations, you want it adjusted by an additional 10% when calculating costs. You also want customers to be able to choose whatever currency they want, but you want foreign currencies to be automatically converted using your currency rates. To determine availability of the discount, you want to use the customer\'s payment address. Then you would enter:<br /><br />
		<strong>RATE #1</strong>
		<ul>
			<li><strong>Payment Methods:</strong> Bank Transfer</li>
			<li><strong>Geo Zone Comparison:</strong> Payment Address</li>
			<li><strong>Currencies:</strong> Convert Unselected and US Dollar</li>
			<li><strong>Geo Zones:</strong> U.S.</li>
			<li><strong>Volume Add:</strong> 10%</li>
			<li><strong>Rate Type:</strong> Volume-Based</li>
			<li><strong>Cost Brackets:</strong><br />
			- From: 0, To: 75, Charge: -2.00<br />
			- From: 75, To: 99999, Charge: -5.00</li>
			<li><strong>Final Cost:</strong> Single</li>
		</ul>
		<strong>RATE #2</strong>
		<ul>
			<li><strong>Payment Methods:</strong> Bank Transfer</li>
			<li><strong>Geo Zone Comparison:</strong> Payment Address</li>
			<li><strong>Currencies:</strong> Convert Unselected and US Dollar</li>
			<li><strong>Geo Zones:</strong> All Other Zones (or your international geo zones)</li>
			<li><strong>Volume Add:</strong> 10%</li>
			<li><strong>Rate Type:</strong> Volume-Based</li>
			<li><strong>Cost Brackets:</strong><br />
			- From: 0, To: 120, Charge: -10.00<br />
			- From: 120, To: 99999, Charge: -20.00</li>
			<li><strong>Final Cost:</strong> Single</li>
		</ul>
	</div>
	<div id="example3" style="display: none">
		Suppose for Cash On Delivery, you want to charge a $1.00 per item fee for the first 10 items, then a $0.50 fee for every item after that. At the same time, you want to charge a fee of $2.50 per pound, up to a maximum of $25.00, and then take the highest of the two fees.<br /><br />
		You also want to limit the fees so they only apply in postcodes starting with 9 or AB. (Your postcodes are 6 alphanumeric digits.) Then you would enter:<br /><br />
		<strong>RATE #1</strong>
		<ul>
			<li><strong>Payment Methods:</strong> Cash On Delivery</li>
			<li><strong>Sort Order:</strong> 1</li>
			<li><strong>Multi Rate Calculation:</strong> Highest</li>
			<li><strong>Postcodes:</strong> 9-9ZZZZZ, AB-ABZZZZ</li>
			<li><strong>Rate Type:</strong> Item-Based</li>
			<li><strong>Cost Brackets:</strong><br />
			- From: 0, To: 10, Charge: 1.00, Per (#): 1<br />
			- From: 10, To: 99999, Charge: 0.50, Per (#): 1</li>
			<li><strong>Final Cost:</strong> Cumulative</li>
		</ul>
		<strong>RATE #2</strong>
		<ul>
			<li><strong>Payment Methods:</strong> Cash On Delivery</li>
			<li><strong>Sort Order:</strong> 1</li>
			<li><strong>Multi Rate Calculation:</strong> Highest</li>
			<li><strong>Postcodes:</strong> 9-9ZZZZZ, AB-ABZZZZ</li>
			<li><strong>Rate Type:</strong> Weight-Based</li>
			<li><strong>Cost Brackets:</strong><br />
			- From: 0, To: 99999, Charge: 2.50, Per (lb): 1</li>
			<li><strong>Final Cost Max:</strong> 25.00</li>
		</ul>
	</div>';

// Copyright
$_['copyright']						= '<div style="text-align: center" class="help">' . $_['heading_title'] . ' ' . $version . ' &copy; <a target="_blank" href="http://www.getclearthinking.com">Clear Thinking, LLC</a></div>';

// Standard Text
$_['standard_module']				= 'Modules';
$_['standard_shipping']				= 'Shipping';
$_['standard_payment']				= 'Payments';
$_['standard_total']				= 'Order Totals';
$_['standard_feed']					= 'Product Feeds';
$_['standard_success']				= 'Success: You have modified ' . $_['heading_title'] . '!';
$_['standard_error']				= 'Warning: You do not have permission to modify ' . $_['heading_title'] . '!';
?>