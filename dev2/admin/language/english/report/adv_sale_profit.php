<?php
// Heading
$_['heading_title']     				= 'Advanced Sales Report + Profit Reporting';
$_['heading_version'] 	  				= 'Version 2.4 [OpenCart v1.5.3.x, v1.5.4.x]<br>ADV Reports & Statistics &copy; 2011-2012';

// Text
$_['stat_custom']       				= 'Custom (use date range)';
$_['stat_week']         				= 'Week (today -7 days)';
$_['stat_month']        				= 'Month (today -30 days)';
$_['stat_quarter']      				= 'Quarter (today -91 days)';
$_['stat_year']         				= 'Year (today -365 days)';
$_['stat_current_week']        			= 'Current Week (monday to today)';
$_['stat_current_month']        		= 'Current Month (from 1st to today)';
$_['stat_current_quarter']        		= 'Current Quarter (current term)';
$_['stat_current_year']        			= 'Current Year (1st Jan to today)';
$_['stat_last_week']        			= 'Last Week (monday to sunday)';
$_['stat_last_month']        			= 'Last Month (from 1st to 31st)';
$_['stat_last_quarter']        			= 'Last Quarter (last term)';
$_['stat_last_year']        			= 'Last Year (1st Jan to 31st Dec)';
$_['stat_all_time']         			= 'All Time';
$_['text_year']         				= 'Years';
$_['text_quarter']         				= 'Quarter';
$_['text_month']        				= 'Months';
$_['text_week']         				= 'Weeks';
$_['text_day']          				= 'Days';
$_['text_order']        				= 'Orders';
$_['text_all_status']   				= 'All Statuses';
$_['text_all_stores']   				= 'All Stores';
$_['text_all_currencies']   			= 'All Currencies';
$_['text_all_taxes']   					= 'All Taxes';
$_['text_all_groups']   				= 'All Groups';
$_['text_all_options']        			= 'All Options';
$_['text_all_locations']   				= 'All Locations';
$_['text_all_affiliates']        		= 'All Affiliates';
$_['text_all_shippings']   				= 'All Methods';
$_['text_all_payments']   				= 'All Methods';
$_['text_all_zones']   					= 'All Regions / States';
$_['text_all_countries']   				= 'All Countries';
$_['text_none_selected']      			= 'Select Options';
$_['text_selected']      	  			= '<span style="color:#003A88;">% Selected</span>';
$_['text_detail'] 						= 'Details';
$_['text_export_no_details'] 		    = 'Export to <b>.xls .html .pdf</b><br>(without details)';
$_['text_export_order_list']			= 'Export to <b>.xls .html .pdf</b><br>(with order list)';
$_['text_export_product_list']			= 'Export to <b>.xls .html .pdf</b><br>(with product list)';
$_['text_export_customer_list']			= 'Export to <b>.xls .html .pdf</b><br>(with customer list)';
$_['text_export_all_details']			= 'Export to <b>.xls .html .pdf</b><br>(all details)';
$_['text_no_details'] 					= 'No Details';
$_['text_order_list'] 					= 'Order List';
$_['text_product_list'] 				= 'Product List';
$_['text_customer_list'] 				= 'Customer List';
$_['text_filter_total'] 	  			= 'Total for Criterias:';
$_['text_filtering_options'] 	  		= 'Filtering Options:';
$_['text_mv_columns'] 	  				= 'Columns in Main View:';
$_['text_ol_columns'] 	  				= 'Columns in Order List:';
$_['text_pl_columns'] 	  				= 'Columns in Product List:';
$_['text_cl_columns'] 	  				= 'Columns in Customer List:';
$_['text_pagin_page'] 	  				= 'Page';
$_['text_pagin_of'] 	  				= 'of';
$_['text_pagin_results'] 	  			= 'results';
$_['text_profit_help']      			= '
<u>Explanation how <span style=background-color:#ffd7d7;><b>Product Costs</b></span>, <span style=background-color:#ffd7d7;><b>Order Costs</b></span>, <span style=background-color:#ffd7d7;><b>Total Costs</b></span>, <span style=background-color:#DCFFB9;><b>Sales</b></span>, <span style=background-color:#BCD5ED;><b>Product Profit</b></span>, <span style=background-color:#BCD5ED;><b>Order Profit</b></span>, <span style=background-color:#BCD5ED;><b>Total Profit</b></span> and <span style=background-color:#BCD5ED;><b>Profit Margin [%]</b></span> is calculated in <b>ADV Sales Report</b>:</u><br><br>
<span style=background-color:#ffd7d7;><b>Product Costs</b></span> = <b>Cost Price</b> (amount) and/or <b>Commission</b> (percentage from sale price) + <b>Additional Costs</b> (e.g. shipping cost) + <b>Option Costs</b> (if options are used)
<br><br>
<span style=background-color:#ffd7d7;><b>Order Costs</b></span> = <b>Product Costs</b> + value of <b>Reward Points</b> + value of <b>Coupon</b> + value of <b>Store Credit</b> + value of <b>Gift Voucher</b> + value of Affiliate <b>Commission</b>
<br><br>
<span style=background-color:#ffd7d7;><b>Total Costs</b></span> = sum of <b>Product Costs</b> + sum of <b>Reward Points</b> + sum of <b>Coupons</b> + sum of <b>Store Credits</b> + sum of <b>Gift Vouchers</b> + sum of Affiliate <b>Commissions</b>
<br><br>
<span style=background-color:#DCFFB9;><b>Sales</b></span> = <b>Sub-Total</b> + <b>Handling Fee (HF)</b> + <b>Low Order Fee (LOF)</b>
<br><br>
<span style=background-color:#BCD5ED;><b>Product Profit</b></span> = <b>Sub-Total</b> - <b>Product Costs</b>
<br><br>
<span style=background-color:#BCD5ED;><b>Order Profit</b></span> = <b>Sales</b> - <b>Order Costs</b>
<br><br>
<span style=background-color:#BCD5ED;><b>Total Profit</b></span> = <b>Sales</b> - <b>Total Costs</b>
<br><br>
<span style=background-color:#BCD5ED;><b>Profit Margin [%]</b></span> = (<b>Profit</b> / <b>Sales</b> - value of <b>Reward Points</b> - value of <b>Coupons</b> - value of <b>Store Credits</b> - value of <b>Gift Vouchers</b> - value of Affiliate <b>Commission</b>) x 100
';

// Column
$_['column_date'] 						= 'Date';
$_['column_date_start'] 				= 'Date Start';
$_['column_date_end']   				= 'Date End';
$_['column_orders']     				= 'No. Orders';
$_['column_customers']  				= 'No. Customers';
$_['column_products']   				= 'No. Products';
$_['column_sub_total']      			= 'Sub-Total';
$_['column_hf']      					= 'HF';
$_['column_handling']      				= 'Handling Fee';
$_['column_lof']      					= 'LOF';
$_['column_loworder']      				= 'Low Order Fee';
$_['column_points']      				= 'Rewards';
$_['column_shipping']      				= 'Shipping';
$_['column_coupon']      				= 'Coupons';
$_['column_tax']        				= 'Tax';
$_['column_credit']      				= 'Credits';
$_['column_voucher']      				= 'Vouchers';
$_['column_commission']      			= 'Commissions';
$_['column_total']      				= 'Total Value';
$_['column_prod_costs']      			= 'Product Costs';
$_['column_net_profit']      			= 'Total Profit';
$_['column_profit_margin']      		= 'Profit [%]';
$_['column_action']     				= 'Action';
$_['column_order_date_added']         	= 'Date Added';
$_['column_order_order_id']           	= 'Order ID';
$_['column_order_inv_date']           	= 'Invoice Date';
$_['column_order_inv_no']       	    = 'Invoice No.';
$_['column_order_customer']       		= 'Customer Name';
$_['column_order_email']          		= 'Customer Email';
$_['column_order_customer_group'] 		= 'Customer Group';
$_['column_order_shipping_method']    	= 'Shipping Method';
$_['column_order_payment_method']     	= 'Payment Method';
$_['column_order_status']       		= 'Status';
$_['column_order_store']     		    = 'Store';
$_['column_order_currency']    			= 'Currency';
$_['column_order_quantity']     		= 'Products';
$_['column_order_sub_total']      		= 'Sub-Total';
$_['column_order_hf']      				= 'HF';
$_['column_order_lof']      			= 'LOF';
$_['column_order_shipping']      		= 'Shipping';
$_['column_order_tax']        			= 'Tax';
$_['column_order_value']        		= 'Order Total';
$_['column_order_costs']      			= 'Order Costs';
$_['column_order_profit']      			= 'Order Profit';
$_['column_prod_order_id']           	= 'Order ID';
$_['column_prod_date_added']         	= 'Date Added';
$_['column_prod_inv_no']       	    	= 'Invoice No.';
$_['column_prod_id'] 					= 'Product ID';
$_['column_prod_sku'] 					= 'SKU';
$_['column_prod_model']      			= 'Model';
$_['column_prod_name']       			= 'Product Name';
$_['column_prod_option']       			= 'Product Options';
$_['column_prod_manu'] 	  				= 'Manufacturer / Brand';
$_['column_prod_currency']      		= 'Currency';
$_['column_prod_price']      			= 'Price';
$_['column_prod_quantity']     			= 'Quantity';
$_['column_prod_total']      			= 'Total';
$_['column_prod_tax']      				= 'Tax';
$_['column_prod_costs']      			= 'Product Costs';
$_['column_prod_profit']      			= 'Product Profit';
$_['column_customer_order_id']			= 'Order ID';
$_['column_customer_date_added']		= 'Date Added';
$_['column_customer_inv_no']			= 'Invoice No.';
$_['column_customer_cust_id']      		= 'Customer ID';
$_['column_billing_name']      			= 'Billing<br> Name';
$_['column_billing_company']      		= 'Billing<br> Company';
$_['column_billing_address_1']      	= 'Billing<br> Address 1';
$_['column_billing_address_2']      	= 'Billing<br> Address 2';
$_['column_billing_city']      			= 'Billing<br> City';
$_['column_billing_zone']      			= 'Billing<br> Region / State';
$_['column_billing_postcode']      		= 'Billing<br> Postcode';
$_['column_billing_country']      		= 'Billing<br> Country';
$_['column_customer_telephone']      	= 'Telephone';
$_['column_shipping_name']      		= 'Shipping<br> Name';
$_['column_shipping_company']      		= 'Shipping<br> Company';
$_['column_shipping_address_1']      	= 'Shipping<br> Address 1';
$_['column_shipping_address_2']      	= 'Shipping<br> Address 2';
$_['column_shipping_city']      		= 'Shipping<br> City';
$_['column_shipping_zone']      		= 'Shipping<br> Region / State';
$_['column_shipping_postcode']      	= 'Shipping<br> Postcode';
$_['column_shipping_country']      		= 'Shipping<br> Country';

$_['column_year']      					= 'Year';
$_['column_quarter']      				= 'Quarter';
$_['column_month']      				= 'Month';
$_['column_sales']      				= 'Sales';
$_['column_total_costs']      			= 'Total Costs';
$_['column_total_profit']      			= 'Total Profit';

// Entry
$_['entry_date_start']  				= 'Date Start:';
$_['entry_date_end']    				= 'Date End:';
$_['entry_range']       				= 'Statistics Range:';
$_['entry_status']      				= 'Order Status:';
$_['entry_store']       				= 'Store:';
$_['entry_currency']    				= 'Currency:';
$_['entry_tax']    						= 'Tax:';
$_['entry_customer_group']  			= 'Customer Group:';
$_['entry_company']       				= 'Company:';
$_['entry_customer']       				= 'Customer Name:';
$_['entry_email']       				= 'Customer Email:';
$_['entry_product']       	  			= 'Product Name:';
$_['entry_option']       	  			= 'Product Option:';
$_['entry_location']  					= 'Location:';
$_['entry_affiliate']       	  		= 'Affiliate Name:';
$_['entry_shipping']  					= 'Shipping Method:';
$_['entry_payment']  					= 'Payment Method:';
$_['entry_zone']  						= 'Region / State:';
$_['entry_shipping_country']  			= 'Shipping Country:';
$_['entry_payment_country']  			= 'Payment Country:';
$_['entry_group']       				= 'Group By:';
$_['entry_sort_by']       				= 'Sort By:';
$_['entry_show_details']       			= 'Details:';
$_['entry_limit']       				= 'Show:';

// Button
$_['button_chart']       	  			= 'Charts';
$_['button_export']       	  			= 'Export';
$_['button_settings']       	  		= 'Settings';
?>