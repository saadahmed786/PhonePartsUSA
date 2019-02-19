<?php echo $header; ?>
<!-- Advanced Sales Report + Profit Reporting - START -->
<style type="text/css">
.box > .content_report {
	padding: 10px;
	border-left: 1px solid #CCCCCC;
	border-right: 1px solid #CCCCCC;
	border-bottom: 1px solid #CCCCCC;
	min-height: 300px;
}
.list_main {
	border-collapse: collapse;
	width: 100%;
	border-top: 1px solid #DDDDDD;
	border-left: 1px solid #DDDDDD;	
	margin-bottom: 20px;
}
.list_main td {
	border-right: 1px solid #DDDDDD;
	border-bottom: 1px solid #DDDDDD;	
}
.list_main thead td {
	background-color: #E5E5E5;
	padding: 0px 5px;
	font-weight: bold;	
}
.list_main tbody td {
	vertical-align: middle;
	padding: 0px 5px;
}
.list_main .left {
	text-align: left;
	padding: 7px;
}
.list_main .right {
	text-align: right;
	padding: 7px;
}
.list_main .center {
	text-align: center;
	padding: 3px;
}
.list_main .noresult {
	text-align: center;
	padding: 7px;
}

.list_detail {
	border-collapse: collapse;
	width: 100%;
	border-top: 1px solid #DDDDDD;
	border-left: 1px solid #DDDDDD;
	margin-top: 10px;
	margin-bottom: 10px;
}
.list_detail td {
	border-right: 1px solid #DDDDDD;
	border-bottom: 1px solid #DDDDDD;
}
.list_detail thead td {
	background-color: #F0F0F0;
	padding: 0px 3px;
	font-size: 11px;
}
.list_detail tbody td {
	padding: 0px 3px;
	font-size: 11px;	
}
.list_detail .left {
	text-align: left;
	padding: 3px;
}
.list_detail .right {
	text-align: right;
	padding: 3px;
}
.list_detail .center {
	text-align: center;
	padding: 3px;
}
.export_item {
  text-align: center;
  text-decoration: none;
}
.export_item a {
  text-decoration: none;
}
.export_item :hover {
  opacity: 0.7;
  -moz-opacity: 0.7;
  -ms-filter: "alpha(opacity=70)"; /* IE 8 */
  filter: alpha(opacity=70); /* IE < 8 */
} 
</style>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/report.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a id="show_tab_export" class="button"><span><?php echo $button_export; ?></span></a>&nbsp;&nbsp;<a onclick="filter('filter');" class="button"><span><?php echo $button_filter; ?></span></a></div>
    </div>
    <div class="content_report">
    <div style="background: #E7EFEF; border: 1px solid #C6D7D7; padding: 3px; margin-bottom: 15px;">
	<table width="100%" cellspacing="0" cellpadding="3">
	<tr>
	<td>
	 <table border="0" cellspacing="0" cellpadding="0">
  	 <tr>
      <td width="250" valign="top" nowrap="nowrap">
      <table align="left" cellpadding="0" cellspacing="0">
        <tr><td><?php echo $entry_date_start; ?><br />
          <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" id="date-start" size="12" style="margin-top: 4px;" />
          </td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>
      <table align="left" cellpadding="0" cellspacing="0">
        <tr><td><?php echo $entry_date_end; ?><br />
          <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" id="date-end" size="12" style="margin-top: 4px;" />
          </td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>
      <table cellpadding="0" cellspacing="0">
        <tr><td><?php echo $entry_range; ?><br />
            <select name="filter_range" style="margin-top: 4px;">
              <?php foreach ($ranges as $ranges) { ?>
              <?php if ($ranges['value'] == $filter_range) { ?>
              <option value="<?php echo $ranges['value']; ?>" title="<?php echo $ranges['text']; ?>" selected="selected"><?php echo $ranges['text']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $ranges['value']; ?>" title="<?php echo $ranges['text']; ?>"><?php echo $ranges['text']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
        </tr></table>    
      </td>
    <td valign="top">
      <table align="left" cellpadding="0" cellspacing="0">
        <tr><td><?php echo $entry_group; ?><br />
            <select name="filter_group" style="margin-top: 4px;">
              <?php foreach ($groups as $groups) { ?>
              <?php if ($groups['value'] == $filter_group) { ?>
              <option value="<?php echo $groups['value']; ?>" title="<?php echo $groups['text']; ?>" selected="selected"><?php echo $groups['text']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $groups['value']; ?>" title="<?php echo $groups['text']; ?>"><?php echo $groups['text']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table> 
      <table align="left" cellpadding="0" cellspacing="0">
        <tr><td><?php echo $entry_status; ?><br />
            <select name="filter_order_status_id" style="margin-top: 4px;">
              <option value="0"><?php echo $text_all_status; ?></option>
              <?php foreach ($order_statuses as $order_status) { ?>
              <?php if ($order_status['order_status_id'] == $filter_order_status_id) { ?>
              <option value="<?php echo $order_status['order_status_id']; ?>" title="<?php echo $order_status['name']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $order_status['order_status_id']; ?>" title="<?php echo $order_status['name']; ?>"><?php echo $order_status['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>
      <table align="left" cellpadding="0" cellspacing="0">
        <tr><td><?php echo $entry_store; ?><br />
            <select name="filter_store_id" style="margin-top: 4px;">
              <option value=""><?php echo $text_all_stores; ?></option>
              <?php foreach ($stores as $store) { ?>
              <?php if ($store['store_id'] == $filter_store_id) { ?>
              <option value="<?php echo $store['store_id']; ?>" title="<?php echo $store['store_name']; ?>" selected="selected"><?php echo $store['store_name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $store['store_id']; ?>" title="<?php echo $store['store_name']; ?>"><?php echo $store['store_name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>
      <table align="left" cellpadding="0" cellspacing="0">
        <tr><td><?php echo $entry_currency; ?><br />
            <select name="filter_currency" style="margin-top: 4px;">
              <option value="0"><?php echo $text_all_currencies; ?></option>
              <?php foreach ($currencies as $currency) { ?>
              <?php if ($currency['currency_id'] == $filter_currency) { ?>
              <option value="<?php echo $currency['currency_id']; ?>" title="<?php echo $currency['title']; ?>" selected="selected"><?php echo $currency['code']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $currency['currency_id']; ?>" title="<?php echo $currency['title']; ?>"><?php echo $currency['code']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>
      <table align="left" cellpadding="0" cellspacing="0">
        <tr><td><?php echo $entry_tax; ?><br />
            <select name="filter_taxes" style="margin-top: 4px;">
              <option value="0"><?php echo $text_all_taxes; ?></option>
              <?php foreach ($taxes as $tax) { ?>
              <?php if ($tax['tax'] == $filter_taxes) { ?>
              <option value="<?php echo $tax['tax']; ?>" title="<?php echo $tax['tax_title']; ?>" selected="selected"><?php echo $tax['tax_title']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $tax['tax']; ?>" title="<?php echo $tax['tax_title']; ?>"><?php echo $tax['tax_title']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>                                       
      <table align="left" cellpadding="0" cellspacing="0">
        <tr><td><?php echo $entry_customer_group; ?><br />
         <select name="filter_customer_group_id" style="margin-top: 4px;">
				<option value="0"><?php echo $text_all_groups; ?></option>
                <?php foreach ($customer_groups as $customer_group) { ?>
                <?php if ($customer_group['customer_group_id'] == $filter_customer_group_id) { ?>
                <option value="<?php echo $customer_group['customer_group_id']; ?>" title="<?php echo $customer_group['name']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $customer_group['customer_group_id']; ?>" title="<?php echo $customer_group['name']; ?>"><?php echo $customer_group['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>  
      <table align="left" cellpadding="0" cellspacing="0">
        <tr><td><?php echo $entry_customer; ?><br />
            <select name="filter_customer_id" style="margin-top: 4px;">
              <option value="0"><?php echo $text_all_customers; ?></option>
              <?php foreach ($customers as $customer) { ?>
              <?php if ($customer['customer_id'] == $filter_customer_id) { ?>
              <option value="<?php echo $customer['customer_id']; ?>" title="<?php echo $customer['name']; ?>" selected="selected"><?php echo $customer['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $customer['customer_id']; ?>" title="<?php echo $customer['name']; ?>"><?php echo $customer['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table> 
      <table align="left" cellpadding="0" cellspacing="0">
        <tr><td><?php echo $entry_shipping; ?><br />
            <select name="filter_shipping" style="margin-top: 4px;">
              <option value="*"><?php echo $text_all_shippings; ?></option>
              <?php foreach ($shippings as $shipping) { ?>
              <?php if ($shipping['shipping_title'] == $filter_shipping) { ?>
              <option value="<?php echo $shipping['shipping_title']; ?>" title="<?php echo $shipping['shipping_name']; ?>" selected="selected"><?php echo $shipping['shipping_name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $shipping['shipping_title']; ?>" title="<?php echo $shipping['shipping_name']; ?>"><?php echo $shipping['shipping_name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table> 
      <table align="left" cellpadding="0" cellspacing="0">
        <tr><td><?php echo $entry_payment; ?><br />
            <select name="filter_payment" style="margin-top: 4px;">
              <option value="*"><?php echo $text_all_payments; ?></option>
              <?php foreach ($payments as $payment) { ?>
              <?php if ($payment['payment_title'] == $filter_payment) { ?>
              <option value="<?php echo $payment['payment_title']; ?>" title="<?php echo $payment['payment_name']; ?>" selected="selected"><?php echo $payment['payment_name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $payment['payment_title']; ?>" title="<?php echo $payment['payment_name']; ?>"><?php echo $payment['payment_name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>
      <table align="left" cellpadding="0" cellspacing="0">
        <tr><td><?php echo $entry_shipping_country; ?><br />
            <select name="filter_shipping_country" style="margin-top: 4px;">
              <option value="*"><?php echo $text_all_countries; ?></option>
              <?php foreach ($shipping_countries as $shipping_country) { ?>
              <?php if ($shipping_country['shipping_country'] == $filter_shipping_country) { ?>
              <option value="<?php echo $shipping_country['shipping_country']; ?>" title="<?php echo $shipping_country['country_name']; ?>" selected="selected"><?php echo $shipping_country['country_name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $shipping_country['shipping_country']; ?>" title="<?php echo $shipping_country['country_name']; ?>"><?php echo $shipping_country['country_name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table> 
      <table align="left" cellpadding="0" cellspacing="0">
        <tr><td><?php echo $entry_payment_country; ?><br />
            <select name="filter_payment_country" style="margin-top: 4px;">
              <option value="*"><?php echo $text_all_countries; ?></option>
              <?php foreach ($payment_countries as $payment_country) { ?>
              <?php if ($payment_country['payment_country'] == $filter_payment_country) { ?>
              <option value="<?php echo $payment_country['payment_country']; ?>" title="<?php echo $payment_country['country_name']; ?>" selected="selected"><?php echo $payment_country['country_name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $payment_country['payment_country']; ?>" title="<?php echo $payment_country['country_name']; ?>"><?php echo $payment_country['country_name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>          
	   </td>
	  </tr>
	 </table>
	</td>
	</tr>
	</table>      
    </div>        
<script type="text/javascript">$(function(){ 
$('#show_tab_export').click(function() {
		$('#tab_export').slideToggle('fast');
	});
});
</script>    
    <div id="tab_export" style="background:#E7EFEF; border:1px solid #C6D7D7; padding:3px; margin-bottom:15px; display:none">
      <table width="100%" cellspacing="0" cellpadding="6">
        <tr>
          <td width="12%">&nbsp;</td>
          <td width="16%" align="center" nowrap="nowrap"><a onclick="filter('xls');" class="export_item"><img src="view/image/XLS.png" width="64" height="64" border="0" title="" /><br /><span style="clear:none"><?php echo $text_export_xls; ?></span></a></td>
          <td width="16%" align="center" nowrap="nowrap"><a onclick="filter('xls_detail');" class="export_item"><img src="view/image/XLS.png" width="64" height="64" border="0" title="" /><br /><span style="clear:none"><?php echo $text_export_xls_detail; ?></span></a></td>
          <td width="12%">&nbsp;</td>
          <td width="16%" align="center" nowrap="nowrap"><a onclick="filter('html');" class="export_item"><img src="view/image/HTML.png" width="64" height="64" border="0" title="" /><br /><span style="clear:none"><?php echo $text_export_html; ?></span></a></td>
          <td width="16%" align="center" nowrap="nowrap"><a onclick="filter('html_detail');" class="export_item"><img src="view/image/HTML.png" width="64" height="64" border="0" title="" /><br /><span style="clear:none"><?php echo $text_export_html_detail; ?></span></a></td>
          <td width="12%">&nbsp;</td>                                                                                                                        
        </tr>
      </table>
    </div>     
    <table class="list_main">
        <thead>
          <tr>
          <td class="left" width="70" nowrap="nowrap"><?php echo $column_date_start; ?></td>
          <td class="left" width="70" nowrap="nowrap"><?php echo $column_date_end; ?></td>
          <td class="right" nowrap="nowrap"><?php echo $column_customers; ?></td>
          <td class="right" nowrap="nowrap"><?php echo $column_orders; ?></td>
          <td class="right" nowrap="nowrap"><?php echo $column_products; ?></td>
          <td class="right" nowrap="nowrap"><?php echo $column_sub_total; ?></td>
          <td class="right" nowrap="nowrap"><?php echo $column_points; ?></td>          
          <td class="right" nowrap="nowrap"><?php echo $column_shipping; ?></td>
          <td class="right" nowrap="nowrap"><?php echo $column_coupon; ?></td>          
          <td class="right" nowrap="nowrap"><?php echo $column_tax; ?></td>
          <td class="right" nowrap="nowrap"><?php echo $column_credit; ?></td>           
          <td class="right" nowrap="nowrap"><?php echo $column_voucher; ?></td>           
          <td class="right" nowrap="nowrap"><?php echo $column_total; ?></td>
          <td class="right" nowrap="nowrap"><?php echo $column_costs; ?></td>        
          <td class="right" nowrap="nowrap"><?php echo $column_net_profit; ?></td>                    
          <td class="right" nowrap="nowrap"><?php echo $column_action; ?></td>
          </tr>
        </thead>
        <tbody>
          <?php if ($orders) { ?>
          <?php foreach ($orders as $order) { ?>
          <tr>
          <td class="left" nowrap="nowrap"><?php echo $order['date_start']; ?></td>
          <td class="left" nowrap="nowrap"><?php echo $order['date_end']; ?></td>
          <td class="right" nowrap="nowrap"><?php echo $order['customers']; ?></td>
          <td class="right" nowrap="nowrap"><?php echo $order['orders']; ?></td>
          <td class="right" nowrap="nowrap"><?php echo $order['products']; ?></td>
          <td class="right" nowrap="nowrap" style="background-color:#DCFFB9;"><?php echo $order['sub_total']; ?></td>
          <td class="right" nowrap="nowrap" style="background-color:#ffd7d7;"><?php echo $order['reward']; ?></td>          
          <td class="right" nowrap="nowrap"><?php echo $order['shipping']; ?></td>
          <td class="right" nowrap="nowrap" style="background-color:#ffd7d7;"><?php echo $order['coupon']; ?></td>          
          <td class="right" nowrap="nowrap"><?php echo $order['tax']; ?></td>
          <td class="right" nowrap="nowrap" style="background-color:#ffd7d7;"><?php echo $order['credit']; ?></td>            
          <td class="right" nowrap="nowrap"><?php echo $order['voucher']; ?></td>            
          <td class="right" nowrap="nowrap"><?php echo $order['total']; ?></td>
          <td class="right" nowrap="nowrap" style="background-color:#ffd7d7;"><?php echo $order['costs']; ?></td>         
          <td class="right" nowrap="nowrap" style="background-color:#DCFFB9; font-weight:bold;"><?php echo $order['netprofit']; ?></td>                    
          <td class="right" nowrap="nowrap">[ <a id="show_details_<?php echo $order['temp']; ?>"><?php echo $text_detail; ?></a> ]</td>
          </tr>
<tr class="detail">
<td colspan="16" class="center">
<script type="text/javascript">$(function(){ 
$('#show_details_<?php echo $order['temp']; ?>').click(function() {
		$('#tab_details_<?php echo $order['temp']; ?>').slideToggle('slow');
	});
});
</script>
<div id="tab_details_<?php echo $order['temp']; ?>" style="display:none">
    <table class="list_detail">
      <thead>
        <tr>
          <td class="left" nowrap="nowrap"><?php echo $column_order_id; ?></td>        
          <td class="left" nowrap="nowrap"><?php echo $column_date_added; ?></td>
          <td class="left" nowrap="nowrap"><?php echo $column_inv_id; ?></td>                  
          <td class="left" nowrap="nowrap"><?php echo $column_name; ?></td>
          <td class="left" nowrap="nowrap"><?php echo $column_email; ?></td>
          <td class="left" nowrap="nowrap"><?php echo $column_customer_group; ?></td>
          <td class="left" nowrap="nowrap"><?php echo $column_shipping_method; ?></td>
          <td class="left" nowrap="nowrap"><?php echo $column_payment_method; ?></td>
          <td class="left" nowrap="nowrap"><?php echo $column_order_status; ?></td>
          <td class="left" nowrap="nowrap"><?php echo $column_store; ?></td>
          <td class="right" nowrap="nowrap"><?php echo $column_order_quantity; ?></td> 
          <td class="right" nowrap="nowrap"><?php echo $column_order_currency; ?></td> 
          <td class="right" nowrap="nowrap"><?php echo $column_sub_total; ?></td>          
          <td class="right" nowrap="nowrap"><?php echo $column_shipping; ?></td>         
          <td class="right" nowrap="nowrap"><?php echo $column_tax; ?></td>            
          <td class="right" nowrap="nowrap"><?php echo $column_order_total; ?></td>
          <td class="right" nowrap="nowrap"><?php echo $column_order_profit; ?></td>          
        </tr>
      </thead>
	  <tbody>
        <tr bgcolor="#FFFFFF">
          <td class="left" nowrap="nowrap"><a><?php echo $order['order_id']; ?></a></td>        
          <td class="left" nowrap="nowrap"><?php echo $order['order_date']; ?></td>
          <td class="left" nowrap="nowrap"><div style="display: inline-block;"><?php echo $order['inv_prefix']; ?></div><div style="display: inline-block;"><?php echo $order['inv_id']; ?></div></td>     
          <td class="left" nowrap="nowrap"><?php echo $order['cust_name']; ?></td>
          <td class="left" nowrap="nowrap"><?php echo $order['cust_email']; ?></td>
          <td class="left" nowrap="nowrap"><?php echo $order['cust_group']; ?></td>
          <td class="left" nowrap="nowrap"><?php echo $order['shipping_method']; ?></td>
          <td class="left" nowrap="nowrap"><?php echo $order['payment_method']; ?></td>          
          <td class="left" nowrap="nowrap"><?php echo $order['os_name']; ?></td>
          <td class="left" nowrap="nowrap"><?php echo $order['store']; ?></td> 
          <td class="right" nowrap="nowrap"><?php echo $order['order_quantity']; ?></td> 
          <td class="right" nowrap="nowrap"><?php echo $order['order_currency']; ?></td>
          <td class="right" nowrap="nowrap"><?php echo $order['order_sub_total']; ?></td> 
          <td class="right" nowrap="nowrap"><?php echo $order['order_shipping']; ?></td>           
          <td class="right" nowrap="nowrap"><?php echo $order['order_tax']; ?></td>           
          <td class="right" nowrap="nowrap"><?php echo $order['order_value']; ?></td>
          <td class="right" nowrap="nowrap" style="background-color:#DCFFB9; font-weight:bold;"><?php echo $order['order_profit']; ?></td>          
         </tr>
      </tbody>
    </table>  
</div>             
</td>
</tr>          
          <?php } ?>
          <?php } else { ?>
          <tr>
            <td class="noresult" colspan="16"><?php echo $text_no_results; ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <div class="pagination"><?php echo $pagination; ?></div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
function filter(option) {
	url = 'index.php?route=report/sale_order&token=<?php echo $token; ?>';
	
	var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');
	
	if (filter_date_start) {
		url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
	}

	var filter_date_end = $('input[name=\'filter_date_end\']').attr('value');
	
	if (filter_date_end) {
		url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	}


	var filter_range = $('select[name=\'filter_range\']').attr('value');
	
	if (filter_range) {
		url += '&filter_range=' + encodeURIComponent(filter_range);
	}
	
	var filter_group = $('select[name=\'filter_group\']').attr('value');
	
	if (filter_group) {
		url += '&filter_group=' + encodeURIComponent(filter_group);
	}
	
	var filter_order_status_id = $('select[name=\'filter_order_status_id\']').attr('value');
	
	if (filter_order_status_id) {
		url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
	}	

	var filter_store_id = $('select[name=\'filter_store_id\']').attr('value');
	
	if (filter_store_id) {
		url += '&filter_store_id=' + encodeURIComponent(filter_store_id);
	}	

	var filter_currency = $('select[name=\'filter_currency\']').attr('value');
	
	if (filter_currency) {
		url += '&filter_currency=' + encodeURIComponent(filter_currency);
	}	

	var filter_taxes = $('select[name=\'filter_taxes\']').attr('value');
	
	if (filter_taxes) {
		url += '&filter_taxes=' + encodeURIComponent(filter_taxes);
	}	
	
	var filter_customer_id = $('select[name=\'filter_customer_id\']').attr('value');
	
	if (filter_customer_id) {
		url += '&filter_customer_id=' + encodeURIComponent(filter_customer_id);
	}

	var filter_customer_group_id = $('select[name=\'filter_customer_group_id\']').attr('value');
	
	if (filter_customer_group_id != '*') {
		url += '&filter_customer_group_id=' + encodeURIComponent(filter_customer_group_id);
	}	

	var filter_shipping = $('select[name=\'filter_shipping\']').attr('value');
	
	if (filter_shipping != '*') {
		url += '&filter_shipping=' + encodeURIComponent(filter_shipping);
	}	

	var filter_payment = $('select[name=\'filter_payment\']').attr('value');
	
	if (filter_payment != '*') {
		url += '&filter_payment=' + encodeURIComponent(filter_payment);
	}	

	var filter_shipping_country = $('select[name=\'filter_shipping_country\']').attr('value');
	
	if (filter_shipping_country != '*') {
		url += '&filter_shipping_country=' + encodeURIComponent(filter_shipping_country);
	}	

	var filter_payment_country = $('select[name=\'filter_payment_country\']').attr('value');
	
	if (filter_payment_country != '*') {
		url += '&filter_payment_country=' + encodeURIComponent(filter_payment_country);
	}
	
	if (option == 'xls') {
		url += '&option=xls';
	}
	if (option == 'xls_detail') {
		url += '&option=xls_detail';
	}
	if (option == 'html') {
		url += '&option=html';
	}
	if (option == 'html_detail') {
		url += '&option=html_detail';
	}
	if (option == 'filter') {
		url += '&option=filter';
	}
	
	location = url;
}
//--></script> 
<script type="text/javascript"><!--
$(document).ready(function() {
	$('#date-start').datepicker({dateFormat: 'yy-mm-dd'});
	
	$('#date-end').datepicker({dateFormat: 'yy-mm-dd'});
});
//--></script> 
<!-- Advanced Sales Report + Profit Reporting - END -->
<?php echo $footer; ?>