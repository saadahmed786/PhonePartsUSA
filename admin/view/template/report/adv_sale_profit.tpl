<?php echo $header; ?>
<script type="text/javascript">
$(document).ready(function() { 
  $("#pagination_content").hide(); 
  $(window).load(function() { 
    $("#pagination_content").show(); 
    $("#content-loading").hide(); 
  }) 
}) 
</script>
<div id="content-loading" style="position: absolute; background-color:white; layer-background-color:white; height:100%; width:100%; text-align:center;"><img src="view/image/page_loading.gif" border="0"></div>
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

#mask {
	position: absolute;
	left: 0;
	top: 0;
	z-index: 9000;
	background-color: #000000;
	display: none;
}
#boxes .window {
	position: fixed;
	left: 0;
	top: 0;
	display: none;
	z-index: 9999;
}
#boxes #dialog {
	background:#FFFFFF; 
	border: 2px solid #ff9f00; 
	padding: 10px;
}

.export_item {
  text-decoration: none;
  cursor: pointer;
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
a.cbutton {
	text-decoration: none;
	color: #FFF;
	display: inline-block;
	padding: 5px 15px 5px 15px;
	-webkit-border-radius: 5px 5px 5px 5px;
	-moz-border-radius: 5px 5px 5px 5px;
	-khtml-border-radius: 5px 5px 5px 5px;
	border-radius: 5px 5px 5px 5px;
}

.pagination_report {
	padding:3px;
	margin:3px;
	text-align:right;
}
.pagination_report a {
	padding: 4px 8px 4px 8px;
	margin-right: 2px;
	border: 1px solid #ddd;
	text-decoration: none; 
	color: #666;
}
.pagination_report a:hover, .pagination_report a:active {
	padding: 4px 8px 4px 8px;
	margin-right: 2px;
	border: 1px solid #c0c0c0;
}
.pagination_report span.current {
	padding: 4px 8px 4px 8px;
	margin-right: 2px;
	border: 1px solid #a0a0a0;
	font-weight: bold;
	background-color: #f0f0f0;
	color: #666;
}
.pagination_report span.disabled {
	padding: 4px 8px 4px 8px;
	margin-right: 2px;
	border: 1px solid #f3f3f3;
	color: #ccc;
}
</style>
<link href="view/stylesheet/jquery.multiSelect.css" rel="stylesheet" type="text/css" />
<form method="post" action="index.php?route=report/adv_sale_profit&token=<?php echo $token; ?>" id="report" name="report">
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/report.png" alt="" /> <?php echo $heading_title; ?></h1><span class="vtip" style="float:left;" title="<?php echo $text_profit_help; ?>"><img style="padding-top:10px; padding-left:5px;" src="view/image/profit_info.png" alt="" /></span><span style="float:right; padding-top:5px; padding-right:5px; font-size:11px; color:#666; text-align:right;"><?php echo $heading_version; ?></span></div>
      <div align="right" style="height:38px; background-color:#F0F0F0; border: 1px solid #DDDDDD; margin-top:5px;">
      <div style="padding-top: 7px; margin-right: 5px;"><?php echo $entry_group; ?>
          <select name="filter_group" id="filter_group" style="background-color:#E7EFEF; border-width:thin; border-color:#333;"> 
              <?php foreach ($groups as $groups) { ?>
              <?php if ($groups['value'] == $filter_group) { ?>
              <option value="<?php echo $groups['value']; ?>" selected="selected"><?php echo $groups['text']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $groups['value']; ?>"><?php echo $groups['text']; ?></option>
              <?php } ?>
              <?php } ?>
          </select>&nbsp;&nbsp; 
          <?php echo $entry_sort_by; ?>
		  <select name="filter_sort" style="background-color:#E7EFEF; border-width:thin; border-color:#333;">                      
            <?php if (!$filter_sort or $filter_sort == 'date') { ?>
            <option value="date" selected="selected"><?php echo $column_date; ?></option>
            <?php } else { ?>
            <option value="date"><?php echo $column_date; ?></option>
            <?php } ?>
            <?php if ($filter_sort == 'orders') { ?>
            <option value="orders" selected="selected"><?php echo $column_orders; ?></option>
            <?php } else { ?>
            <option value="orders"><?php echo $column_orders; ?></option>
            <?php } ?>            
            <?php if ($filter_sort == 'customers') { ?>
            <option value="customers" selected="selected"><?php echo $column_customers; ?></option>
            <?php } else { ?>
            <option value="customers"><?php echo $column_customers; ?></option>
            <?php } ?>
            <?php if ($filter_sort == 'products') { ?>
            <option value="products" selected="selected"><?php echo $column_products; ?></option>
            <?php } else { ?>
            <option value="products"><?php echo $column_products; ?></option>
            <?php } ?>
            <?php if ($filter_sort == 'sub_total') { ?>
            <option value="sub_total" selected="selected"><?php echo $column_sub_total; ?></option>
            <?php } else { ?>
            <option value="sub_total"><?php echo $column_sub_total; ?></option>
            <?php } ?> 
            <?php if ($filter_sort == 'reward') { ?>
            <option value="reward" selected="selected"><?php echo $column_points; ?></option>
            <?php } else { ?>
            <option value="reward"><?php echo $column_points; ?></option>
            <?php } ?> 
            <?php if ($filter_sort == 'shipping') { ?>
            <option value="shipping" selected="selected"><?php echo $column_shipping; ?></option>
            <?php } else { ?>
            <option value="shipping"><?php echo $column_shipping; ?></option>
            <?php } ?> 
            <?php if ($filter_sort == 'coupon') { ?>
            <option value="coupon" selected="selected"><?php echo $column_coupon; ?></option>
            <?php } else { ?>
            <option value="coupon"><?php echo $column_coupon; ?></option>
            <?php } ?> 
            <?php if ($filter_sort == 'tax') { ?>
            <option value="tax" selected="selected"><?php echo $column_tax; ?></option>
            <?php } else { ?>
            <option value="tax"><?php echo $column_tax; ?></option>
            <?php } ?> 
            <?php if ($filter_sort == 'credit') { ?>
            <option value="credit" selected="selected"><?php echo $column_credit; ?></option>
            <?php } else { ?>
            <option value="credit"><?php echo $column_credit; ?></option>
            <?php } ?>
            <?php if ($filter_sort == 'voucher') { ?>
            <option value="voucher" selected="selected"><?php echo $column_voucher; ?></option>
            <?php } else { ?>
            <option value="voucher"><?php echo $column_voucher; ?></option>
            <?php } ?>   
            <?php if ($filter_sort == 'commission') { ?>
            <option value="commission" selected="selected"><?php echo $column_commission; ?></option>
            <?php } else { ?>
            <option value="commission"><?php echo $column_commission; ?></option>
            <?php } ?>              
            <?php if ($filter_sort == 'total') { ?>
            <option value="total" selected="selected"><?php echo $column_total; ?></option>
            <?php } else { ?>
            <option value="total"><?php echo $column_total; ?></option>
            <?php } ?>   
            <?php if ($filter_sort == 'prod_costs') { ?>
            <option value="prod_costs" selected="selected"><?php echo $column_prod_costs; ?></option>
            <?php } else { ?>
            <option value="prod_costs"><?php echo $column_prod_costs; ?></option>
            <?php } ?>   
            <?php if ($filter_sort == 'profit') { ?>
            <option value="profit" selected="selected"><?php echo $column_net_profit; ?></option>
            <?php } else { ?>
            <option value="profit"><?php echo $column_net_profit; ?></option>
            <?php } ?>                                                                                                                                              
          </select>&nbsp;&nbsp; 
          <?php echo $entry_show_details; ?>
		  <select name="filter_details" style="background-color:#E7EFEF; border-width:thin; border-color:#333;">                      
            <?php if (!$filter_details or $filter_details == '0') { ?>
            <option value="0" selected="selected"><?php echo $text_no_details; ?></option>
            <?php } else { ?>
            <option value="0"><?php echo $text_no_details; ?></option>
            <?php } ?>
            <?php if ($filter_details == '1') { ?>
            <option value="1" selected="selected"><?php echo $text_order_list; ?></option>
            <?php } else { ?>
            <option value="1"><?php echo $text_order_list; ?></option>
            <?php } ?>
            <?php if ($filter_details == '2') { ?>
            <option value="2" selected="selected"><?php echo $text_product_list; ?></option>
            <?php } else { ?>
            <option value="2"><?php echo $text_product_list; ?></option>
            <?php } ?>  
            <?php if ($filter_details == '3') { ?>
            <option value="3" selected="selected"><?php echo $text_customer_list; ?></option>
            <?php } else { ?>
            <option value="3"><?php echo $text_customer_list; ?></option>
            <?php } ?>                                  
          </select>&nbsp;&nbsp; 
          <?php echo $entry_limit; ?>
		  <select name="filter_limit" style="background-color:#E7EFEF; border-width:thin; border-color:#333;"> 
            <?php if ($filter_limit == '10') { ?>
            <option value="10" selected="selected">10</option>
            <?php } else { ?>
            <option value="10">10</option>
            <?php } ?>                                
            <?php if (!$filter_limit or $filter_limit == '25') { ?>
            <option value="25" selected="selected">25</option>
            <?php } else { ?>
            <option value="25">25</option>
            <?php } ?>
            <?php if ($filter_limit == '50') { ?>
            <option value="50" selected="selected">50</option>
            <?php } else { ?>
            <option value="50">50</option>
            <?php } ?>
            <?php if ($filter_limit == '100') { ?>
            <option value="100" selected="selected">100</option>
            <?php } else { ?>
            <option value="100">100</option>
            <?php } ?>                        
          </select>&nbsp; <a id="button" onclick="$('#report').submit();" class="cbutton" style="background:#069;"><span><?php echo $button_filter; ?></span></a>&nbsp;<?php if ($orders) { ?><?php if (($filter_range != 'all_time' && ($filter_group == 'year' or $filter_group == 'quarter' or $filter_group == 'month')) or ($filter_range == 'all_time' && $filter_group == 'year')) { ?><a id="show_tab_chart" class="cbutton" style="background:#930;"><span><?php echo $button_chart; ?></span></a><?php } ?><?php } ?>&nbsp;<a id="show_tab_export" class="cbutton" style="background:#699;"><span><?php echo $button_export; ?></span></a>&nbsp;<a href="#dialog" name="modal" class="cbutton" style="background:#666;"><span><?php echo $button_settings; ?></span></a></div>       
    </div>
    <div class="content_report">
<script type="text/javascript"><!--
$(document).ready(function() {
var prev = {start: 0, stop: 0},
    cont = $('#pagination_content .element');
	
$(".pagination_report").paging(cont.length, {
	format: '[< ncnnn! >]',
	perpage: '<?php echo $filter_limit; ?>',	
	lapping: 0,
	page: null, // we await hashchange() event
			onSelect: function() {

				var data = this.slice;

				cont.slice(prev[0], prev[1]).css('display', 'none');
				cont.slice(data[0], data[1]).fadeIn(0);

				prev = data;

				return true; // locate!
			},
			onFormat: function (type) {

				switch (type) {

					case 'block':

						if (!this.active)
							return '<span class="disabled">' + this.value + '</span>';
						else if (this.value != this.page)
							return '<em><a href="index.php?route=report/adv_sale_profit&token=<?php echo $token; ?>#' + this.value + '">' + this.value + '</a></em>';
						return '<span class="current">' + this.value + '</span>';

					case 'next':

						if (this.active) {
							return '<a href="index.php?route=report/adv_sale_profit&token=<?php echo $token; ?>#' + this.value + '" class="next">Next &gt;</a>';
						}
						return '';						

					case 'prev':

						if (this.active) {
							return '<a href="index.php?route=report/adv_sale_profit&token=<?php echo $token; ?>#' + this.value + '" class="prev">&lt; Previous</a>';
						}	
						return '';						

					case 'first':

						if (this.active) {
							return '<?php echo $text_pagin_page; ?> ' + this.page + ' <?php echo $text_pagin_of; ?> ' + this.pages + '&nbsp;&nbsp;<a href="index.php?route=report/adv_sale_profit&token=<?php echo $token; ?>#' + this.value + '" class="first">|&lt;</a>';
						}	
						return '<?php echo $text_pagin_page; ?> ' + this.page + ' <?php echo $text_pagin_of; ?> ' + this.pages + '&nbsp;&nbsp';
							
					case 'last':

						if (this.active) {
							return '<a href="index.php?route=report/adv_sale_profit&token=<?php echo $token; ?>#' + this.value + '" class="prev">&gt;|</a>&nbsp;&nbsp;(' + cont.length + ' <?php echo $text_pagin_results; ?>)';
						}
						return '&nbsp;&nbsp;(' + cont.length + ' <?php echo $text_pagin_results; ?>)';					

				}
				return ''; // return nothing for missing branches
			}
});
});		
//--></script>         
<script type="text/javascript"><!--
function getStorage(key_prefix) {
    // this function will return us an object with a "set" and "get" method
    if (window.localStorage) {
        // use localStorage:
        return {
            set: function(id, data) {
                localStorage.setItem(key_prefix+id, data);
            },
            get: function(id) {
                return localStorage.getItem(key_prefix+id);
            }
        };
    }
}

$(document).ready(function() {
    // a key must is used for the cookie/storage
    var storedData = getStorage('com_mysite_checkboxes_'); 
    
    $('div.check input:checkbox').bind('change',function(){
        $('#'+this.id+'_filter').toggle($(this).is(':checked'));
        $('#'+this.id+'_title').toggle($(this).is(':checked'));
			<?php if ($orders) {
					foreach ($orders as $key => $order) {
						echo "$('#'+this.id+'_" . $order['order_id'] . "_title').toggle($(this).is(':checked')); ";
						echo "$('#'+this.id+'_" . $order['order_id'] . "').toggle($(this).is(':checked')); ";						
					}			
			} 
			;?>		
        $('#'+this.id+'_total').toggle($(this).is(':checked'));			
        // save the data on change
        storedData.set(this.id, $(this).is(':checked')?'checked':'not');
    }).each(function() {
        // on load, set the value to what we read from storage:
        var val = storedData.get(this.id);
        if (val == 'checked') $(this).attr('checked', 'checked');
        if (val == 'not') $(this).removeAttr('checked');
        if (val) $(this).trigger('change');
    });
});
//--></script>
<script type="text/javascript">
$(document).ready(function() {
	//select all the a tag with name equal to modal
	$('a[name=modal]').click(function(e) {
		//Cancel the link behavior
		e.preventDefault();
		
		//Get the A tag
		var id = $(this).attr('href');
	
		//Get the screen height and width
		var maskHeight = $(document).height();
		var maskWidth = $(window).width();
	
		//Set heigth and width to mask to fill up the whole screen
		$('#mask').css({'width':maskWidth,'height':maskHeight});
		
		//transition effect		
		$('#mask').fadeTo("fast",0.15);	
	
		//Get the window height and width
		var winH = $(window).height();
		var winW = $(window).width();
              
		//Set the popup window to center
		$(id).css('top',  winH/2-$(id).height()/2);
		$(id).css('left', winW/2-$(id).width()/2);
	
		//transition effect
		$(id).fadeIn(500); 
	});
	
	//if close button is clicked
	$('.window .close').click(function (e) {
		//Cancel the link behavior
		e.preventDefault();
		
		$('#mask').hide();
		$('.window').hide();
	});		
	
	//if mask is clicked
	$('#mask').click(function () {
		$(this).hide();
		$('.window').hide();
	});			
	
	$(window).resize(function () {
 		var box = $('#boxes .window');
 
        //Get the screen height and width
        var maskHeight = $(document).height();
        var maskWidth = $(window).width();
      
        //Set height and width to mask to fill up the whole screen
        $('#mask').css({'width':maskWidth,'height':maskHeight});
               
        //Get the window height and width
        var winH = $(window).height();
        var winW = $(window).width();

        //Set the popup window to center
        box.css('top',  winH/2 - box.height()/2);
        box.css('left', winW/2 - box.width()/2);
	});	
});
</script>
<div id="boxes">
<div id="dialog" class="window">
<div align="right"><a href="#" class="close" style="text-decoration:none;">[Close]</a></div>
    <div class="check">  
	<div style="float:left; padding-right:10px; padding-top:5px;">     
      &nbsp;<b><?php echo $text_filtering_options; ?></b><br />        
      <table cellspacing="0" cellpadding="3" style="background:#E7EFEF; border: 1px solid #DDDDDD; padding-right:10px;">
        <tr>
          <td>
            <input id="sop1" checked="checked" type="checkbox"><label for="sop1"><?php echo substr($entry_status,0,-1); ?></label><br />
			<input id="sop2" checked="checked" type="checkbox"><label for="sop2"><?php echo substr($entry_store,0,-1); ?></label><br />
			<input id="sop3" checked="checked" type="checkbox"><label for="sop3"><?php echo substr($entry_currency,0,-1); ?></label><br />
			<input id="sop4" checked="checked" type="checkbox"><label for="sop4"><?php echo substr($entry_tax,0,-1); ?></label><br />
			<input id="sop5" checked="checked" type="checkbox"><label for="sop5"><?php echo substr($entry_customer_group,0,-1); ?></label><br />
			<input id="sop6" checked="checked" type="checkbox"><label for="sop6"><?php echo substr($entry_company,0,-1); ?></label><br />
			<input id="sop7" checked="checked" type="checkbox"><label for="sop7"><?php echo substr($entry_customer,0,-1); ?></label><br />
			<input id="sop8" checked="checked" type="checkbox"><label for="sop8"><?php echo substr($entry_email,0,-1); ?></label><br />
            <input id="sop9" checked="checked" type="checkbox"><label for="sop9"><?php echo substr($entry_product,0,-1); ?></label><br />
            <input id="sop10" checked="checked" type="checkbox"><label for="sop10"><?php echo substr($entry_option,0,-1); ?></label><br />
            <input id="sop11" checked="checked" type="checkbox"><label for="sop11"><?php echo substr($entry_location,0,-1); ?></label><br />
            <input id="sop12" checked="checked" type="checkbox"><label for="sop12"><?php echo substr($entry_affiliate,0,-1); ?></label><br />                        
            <input id="sop13" checked="checked" type="checkbox"><label for="sop13"><?php echo substr($entry_shipping,0,-1); ?></label><br />
            <input id="sop14" checked="checked" type="checkbox"><label for="sop14"><?php echo substr($entry_payment,0,-1); ?></label><br />
            <input id="sop15" checked="checked" type="checkbox"><label for="sop15"><?php echo substr($entry_zone,0,-1); ?></label><br />
            <input id="sop16" checked="checked" type="checkbox"><label for="sop16"><?php echo substr($entry_shipping_country,0,-1); ?></label><br />
            <input id="sop17" checked="checked" type="checkbox"><label for="sop17"><?php echo substr($entry_payment_country,0,-1); ?></label>			
          </td>                                                                                                                        
        </tr>
      </table>
    </div>
	<div style="float:left; padding-right:10px; padding-top:5px;">      
      &nbsp;<b><?php echo $text_mv_columns; ?></b><br />
      <table cellspacing="0" cellpadding="3" style="background:#E5E5E5; border: 1px solid #DDDDDD; padding-right:10px;">
        <tr>
          <td>         
			<input id="sop20" checked="checked" type="checkbox"><label for="sop20"><?php echo $column_orders; ?></label><br />
			<input id="sop21" checked="checked" type="checkbox"><label for="sop21"><?php echo $column_customers; ?></label><br />
			<input id="sop22" checked="checked" type="checkbox"><label for="sop22"><?php echo $column_products; ?></label><br />
			<input id="sop23" checked="checked" type="checkbox"><label for="sop23"><?php echo $column_sub_total; ?></label><br />
			<input id="sop24" checked="checked" type="checkbox"><label for="sop24"><?php echo $column_handling; ?> (<?php echo $column_hf; ?>)</label><br />
			<input id="sop25" checked="checked" type="checkbox"><label for="sop25"><?php echo $column_loworder; ?> (<?php echo $column_lof; ?>)</label><br />
			<input id="sop26" checked="checked" type="checkbox"><label for="sop26"><?php echo $column_points; ?></label><br />
			<input id="sop27" checked="checked" type="checkbox"><label for="sop27"><?php echo $column_shipping; ?></label><br />
            <input id="sop28" checked="checked" type="checkbox"><label for="sop28"><?php echo $column_coupon; ?></label><br />
            <input id="sop29" checked="checked" type="checkbox"><label for="sop29"><?php echo $column_tax; ?></label><br />
            <input id="sop30" checked="checked" type="checkbox"><label for="sop30"><?php echo $column_credit; ?></label><br />
            <input id="sop31" checked="checked" type="checkbox"><label for="sop31"><?php echo $column_voucher; ?></label><br />
            <input id="sop32" checked="checked" type="checkbox"><label for="sop32"><?php echo $column_commission; ?></label><br />
            <input id="sop33" checked="checked" type="checkbox"><label for="sop33"><?php echo $column_total; ?></label><br />
            <input id="sop34" checked="checked" type="checkbox"><label for="sop34"><?php echo $column_prod_costs; ?></label><br />
            <input id="sop35" checked="checked" type="checkbox"><label for="sop35"><?php echo $column_net_profit; ?></label><br />
            <input id="sop36" checked="checked" type="checkbox"><label for="sop36"><?php echo $column_profit_margin; ?></label>
          </td>                                                                                                                        
        </tr>
      </table>
     </div>      
	<div style="float:left; padding-right:10px; padding-top:5px;">        
      &nbsp;<b><?php echo $text_ol_columns; ?></b><br />
      <table cellspacing="0" cellpadding="3" style="background:#F0F0F0; border: 1px solid #DDDDDD; padding-right:10px;">
        <tr>
          <td>       
			<input id="sop40" checked="checked" type="checkbox"><label for="sop40"><?php echo $column_order_order_id; ?></label><br />
			<input id="sop41" checked="checked" type="checkbox"><label for="sop41"><?php echo $column_order_date_added; ?></label><br />
			<input id="sop42" checked="checked" type="checkbox"><label for="sop42"><?php echo $column_order_inv_no; ?></label><br />
			<input id="sop43" checked="checked" type="checkbox"><label for="sop43"><?php echo $column_order_customer; ?></label><br />
			<input id="sop44" checked="checked" type="checkbox"><label for="sop44"><?php echo $column_order_email; ?></label><br />
			<input id="sop45" checked="checked" type="checkbox"><label for="sop45"><?php echo $column_order_customer_group; ?></label><br />
			<input id="sop46" checked="checked" type="checkbox"><label for="sop46"><?php echo $column_order_shipping_method; ?></label><br />	
            <input id="sop47" checked="checked" type="checkbox"><label for="sop47"><?php echo $column_order_payment_method; ?></label><br />
            <input id="sop48" checked="checked" type="checkbox"><label for="sop48"><?php echo $column_order_status; ?></label><br />
            <input id="sop49" checked="checked" type="checkbox"><label for="sop49"><?php echo $column_order_store; ?></label><br />
            <input id="sop50" checked="checked" type="checkbox"><label for="sop50"><?php echo $column_order_currency; ?></label><br />
            <input id="sop51" checked="checked" type="checkbox"><label for="sop51"><?php echo $column_order_quantity; ?></label><br />
            <input id="sop52" checked="checked" type="checkbox"><label for="sop52"><?php echo $column_order_sub_total; ?></label><br />
            <input id="sop531" checked="checked" type="checkbox"><label for="sop531"><?php echo $column_handling; ?> (<?php echo $column_hf; ?>)</label><br />
            <input id="sop532" checked="checked" type="checkbox"><label for="sop532"><?php echo $column_loworder; ?> (<?php echo $column_lof; ?>)</label><br />
            <input id="sop54" checked="checked" type="checkbox"><label for="sop54"><?php echo $column_order_shipping; ?></label><br />
            <input id="sop55" checked="checked" type="checkbox"><label for="sop55"><?php echo $column_order_tax; ?></label><br />
            <input id="sop56" checked="checked" type="checkbox"><label for="sop56"><?php echo $column_order_value; ?></label><br />
            <input id="sop57" checked="checked" type="checkbox"><label for="sop57"><?php echo $column_order_costs; ?></label><br />
            <input id="sop58" checked="checked" type="checkbox"><label for="sop58"><?php echo $column_order_profit; ?></label><br />
            <input id="sop59" checked="checked" type="checkbox"><label for="sop59"><?php echo $column_profit_margin; ?></label>
          </td>                                                                                                                        
        </tr>
      </table>  
    </div>      
	<div style="float:left; padding-right:10px; padding-top:5px;">         
      &nbsp;<b><?php echo $text_pl_columns; ?></b><br />
      <table cellspacing="0" cellpadding="3" style="background:#F0F0F0; border: 1px solid #DDDDDD; padding-right:10px;">
        <tr>
          <td>    
			<input id="sop60" checked="checked" type="checkbox"><label for="sop60"><?php echo $column_prod_order_id; ?></label><br />
			<input id="sop61" checked="checked" type="checkbox"><label for="sop61"><?php echo $column_prod_date_added; ?></label><br />
			<input id="sop62" checked="checked" type="checkbox"><label for="sop62"><?php echo $column_prod_inv_no; ?></label><br />
			<input id="sop63" checked="checked" type="checkbox"><label for="sop63"><?php echo $column_prod_id; ?></label><br />
			<input id="sop64" checked="checked" type="checkbox"><label for="sop64"><?php echo $column_prod_sku; ?></label><br />
			<input id="sop65" checked="checked" type="checkbox"><label for="sop65"><?php echo $column_prod_model; ?></label><br />
			<input id="sop66" checked="checked" type="checkbox"><label for="sop66"><?php echo $column_prod_name; ?></label><br />
			<input id="sop67" checked="checked" type="checkbox"><label for="sop67"><?php echo $column_prod_option; ?></label><br />
            <input id="sop68" checked="checked" type="checkbox"><label for="sop68"><?php echo $column_prod_manu; ?></label><br />
            <input id="sop69" checked="checked" type="checkbox"><label for="sop69"><?php echo $column_prod_currency; ?></label><br />
            <input id="sop70" checked="checked" type="checkbox"><label for="sop70"><?php echo $column_prod_price; ?></label><br />
            <input id="sop71" checked="checked" type="checkbox"><label for="sop71"><?php echo $column_prod_quantity; ?></label><br />
            <input id="sop72" checked="checked" type="checkbox"><label for="sop72"><?php echo $column_prod_total; ?></label><br />
            <input id="sop73" checked="checked" type="checkbox"><label for="sop73"><?php echo $column_prod_tax; ?></label><br />
            <input id="sop74" checked="checked" type="checkbox"><label for="sop74"><?php echo $column_prod_costs; ?></label><br />
            <input id="sop75" checked="checked" type="checkbox"><label for="sop75"><?php echo $column_prod_profit; ?></label><br />
            <input id="sop76" checked="checked" type="checkbox"><label for="sop76"><?php echo $column_profit_margin; ?></label>
          </td>                                                                                                                        
        </tr>
      </table> 
    </div>      
	<div style="float:left; padding-top:5px;">         
      &nbsp;<b><?php echo $text_cl_columns; ?></b><br />
      <table cellspacing="0" cellpadding="3" style="background:#F0F0F0; border: 1px solid #DDDDDD; padding-right:10px;">
        <tr>
          <td>       
			<input id="sop80" checked="checked" type="checkbox"><label for="sop80"><?php echo $column_customer_order_id; ?></label><br />
			<input id="sop81" checked="checked" type="checkbox"><label for="sop81"><?php echo $column_customer_date_added; ?></label><br />
			<input id="sop82" checked="checked" type="checkbox"><label for="sop82"><?php echo $column_customer_inv_no; ?></label><br />
			<input id="sop83" checked="checked" type="checkbox"><label for="sop83"><?php echo $column_customer_cust_id; ?></label><br />
			<input id="sop84" checked="checked" type="checkbox"><label for="sop84"><?php echo strip_tags($column_billing_name); ?></label><br />
			<input id="sop85" checked="checked" type="checkbox"><label for="sop85"><?php echo strip_tags($column_billing_company); ?></label><br />
			<input id="sop86" checked="checked" type="checkbox"><label for="sop86"><?php echo strip_tags($column_billing_address_1); ?></label><br />
			<input id="sop87" checked="checked" type="checkbox"><label for="sop87"><?php echo strip_tags($column_billing_address_2); ?></label><br />			
            <input id="sop88" checked="checked" type="checkbox"><label for="sop88"><?php echo strip_tags($column_billing_city); ?></label><br />
            <input id="sop89" checked="checked" type="checkbox"><label for="sop89"><?php echo strip_tags($column_billing_zone); ?></label><br />
            <input id="sop90" checked="checked" type="checkbox"><label for="sop90"><?php echo strip_tags($column_billing_postcode); ?></label><br />
            <input id="sop91" checked="checked" type="checkbox"><label for="sop91"><?php echo strip_tags($column_billing_country); ?></label><br />
            <input id="sop92" checked="checked" type="checkbox"><label for="sop92"><?php echo $column_customer_telephone; ?></label><br />
			<input id="sop93" checked="checked" type="checkbox"><label for="sop93"><?php echo strip_tags($column_shipping_name); ?></label><br />
			<input id="sop94" checked="checked" type="checkbox"><label for="sop94"><?php echo strip_tags($column_shipping_company); ?></label><br />
			<input id="sop95" checked="checked" type="checkbox"><label for="sop95"><?php echo strip_tags($column_shipping_address_1); ?></label><br />
			<input id="sop96" checked="checked" type="checkbox"><label for="sop96"><?php echo strip_tags($column_shipping_address_2); ?></label><br />
            <input id="sop97" checked="checked" type="checkbox"><label for="sop97"><?php echo strip_tags($column_shipping_city); ?></label><br />
            <input id="sop98" checked="checked" type="checkbox"><label for="sop98"><?php echo strip_tags($column_shipping_zone); ?></label><br />
            <input id="sop99" checked="checked" type="checkbox"><label for="sop99"><?php echo strip_tags($column_shipping_postcode); ?></label><br />
            <input id="sop100" checked="checked" type="checkbox"><label for="sop100"><?php echo strip_tags($column_shipping_country); ?></label>
          </td>                                                                                                                        
        </tr>
      </table>   
    </div>                    
    </div> 
</div>
<div id="mask"></div>
</div>    
    <div style="background: #E7EFEF; border: 1px solid #C6D7D7; margin-bottom: 15px;">
	<table width="100%" cellspacing="0" cellpadding="3">
	<tr>
	<td>
	 <table border="0" cellspacing="0" cellpadding="0">
  	 <tr>
      <td width="220" valign="top" nowrap="nowrap" style="background: #C6D7D7; border: 1px solid #CCCCCC; padding: 5px;">
      <table cellpadding="0" cellspacing="0" style="float:left;">
        <tr><td><?php echo $entry_date_start; ?><br />
          <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" id="date-start" size="12" style="margin-top: 4px;" />
          </td><td width="10"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>
      <table cellpadding="0" cellspacing="0" style="float:left;">
        <tr><td><?php echo $entry_date_end; ?><br />
          <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" id="date-end" size="12" style="margin-top: 4px;" />
          </td><td></td></tr>
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
            </select></td><td></td></tr>
        <tr><td>&nbsp;</td><td></td>
        </tr></table>    
      </td>
    <td valign="top" style="padding: 5px;">  
      <table cellpadding="0" cellspacing="0" style="float:left;" id="sop1_filter">
        <tr><td><?php echo $entry_status; ?><br />
          <span <?php echo (!$filter_order_status_id) ? '' : 'class="vtip"' ?> title="<?php foreach ($order_statuses as $order_status) { ?><?php if (isset($filter_order_status_id[$order_status['order_status_id']])) { ?><?php echo $order_status['name']; ?><br /><?php } ?><?php } ?>">
          <select name="filter_order_status_id" id="filter_order_status_id" multiple="multiple" size="1">
            <?php foreach ($order_statuses as $order_status) { ?>
            <?php if (isset($filter_order_status_id[$order_status['order_status_id']])) { ?>
            <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
            <?php } ?>
            <?php } ?>
          </select></span></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>
      <table cellpadding="0" cellspacing="0" style="float:left;" id="sop2_filter">
        <tr><td><?php echo $entry_store; ?><br />
          <span <?php echo (!$filter_store_id) ? '' : 'class="vtip"' ?> title="<?php foreach ($stores as $store) { ?><?php if (isset($filter_store_id[$store['store_id']])) { ?><?php echo $store['store_name']; ?><br /><?php } ?><?php } ?>">
          <select name="filter_store_id" id="filter_store_id" multiple="multiple" size="1">
            <?php foreach ($stores as $store) { ?>
            <?php if (isset($filter_store_id[$store['store_id']])) { ?>            
            <option value="<?php echo $store['store_id']; ?>" selected="selected"><?php echo $store['store_name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $store['store_id']; ?>"><?php echo $store['store_name']; ?></option>
            <?php } ?>
            <?php } ?>
          </select></span></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>    
      <table cellpadding="0" cellspacing="0" style="float:left;" id="sop3_filter">
        <tr><td><?php echo $entry_currency; ?><br />
          <span <?php echo (!$filter_currency) ? '' : 'class="vtip"' ?> title="<?php foreach ($currencies as $currency) { ?><?php if (isset($filter_currency[$currency['currency_id']])) { ?><?php echo $currency['title']; ?> (<?php echo $currency['code']; ?>)<br /><?php } ?><?php } ?>">
          <select name="filter_currency" id="filter_currency" multiple="multiple" size="1">
            <?php foreach ($currencies as $currency) { ?>
            <?php if (isset($filter_currency[$currency['currency_id']])) { ?>
            <option value="<?php echo $currency['currency_id']; ?>" selected="selected"><?php echo $currency['title']; ?> (<?php echo $currency['code']; ?>)</option>
            <?php } else { ?>
            <option value="<?php echo $currency['currency_id']; ?>"><?php echo $currency['title']; ?> (<?php echo $currency['code']; ?>)</option>
            <?php } ?>
            <?php } ?>
          </select></span></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>          
          </tr></table>
      <table cellpadding="0" cellspacing="0" style="float:left;" id="sop4_filter">
        <tr><td><?php echo $entry_tax; ?><br />
          <span <?php echo (!$filter_taxes) ? '' : 'class="vtip"' ?> title="<?php foreach ($taxes as $tax) { ?><?php if (isset($filter_taxes[$tax['tax']])) { ?><?php echo $tax['tax_title']; ?><br /><?php } ?><?php } ?>">
		  <select name="filter_taxes" id="filter_taxes" multiple="multiple" size="1">
            <?php foreach ($taxes as $tax) { ?>
            <?php if (isset($filter_taxes[$tax['tax']])) { ?>              
            <option value="<?php echo $tax['tax']; ?>" selected="selected"><?php echo $tax['tax_title']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $tax['tax']; ?>"><?php echo $tax['tax_title']; ?></option>
            <?php } ?>
            <?php } ?>
          </select></span></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table> 
      <table cellpadding="0" cellspacing="0" style="float:left;" id="sop5_filter">
        <tr><td><?php echo $entry_customer_group; ?><br />
          <span <?php echo (!$filter_customer_group_id) ? '' : 'class="vtip"' ?> title="<?php foreach ($customer_groups as $customer_group) { ?><?php if (isset($filter_customer_group_id[$customer_group['customer_group_id']])) { ?><?php echo $customer_group['name']; ?><br /><?php } ?><?php } ?>">
          <select name="filter_customer_group_id" id="filter_customer_group_id" multiple="multiple" size="1">
            <?php foreach ($customer_groups as $customer_group) { ?>
            <?php if (isset($filter_customer_group_id[$customer_group['customer_group_id']])) { ?>              
            <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
            <?php } ?>
            <?php } ?>
          </select></span></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>    
      <table cellpadding="0" cellspacing="0" style="float:left;" id="sop6_filter">
        <tr><td> <?php echo $entry_company; ?><br />
        <input type="text" name="filter_company" value="<?php echo $filter_company; ?>" size="18" style="margin-top:4px; height:16px; border:solid 1px #BBB; color:#003A88;" onclick="this.value = '';">
		</td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>                   
      <table cellpadding="0" cellspacing="0" style="float:left;" id="sop7_filter">
        <tr><td> <?php echo $entry_customer; ?><br />
        <input type="text" name="filter_customer_id" value="<?php echo $filter_customer_id; ?>" size="18" style="margin-top:4px; height:16px; border:solid 1px #BBB; color:#003A88;" onclick="this.value = '';">
        </td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table> 
      <table cellpadding="0" cellspacing="0" style="float:left;" id="sop8_filter">
        <tr><td> <?php echo $entry_email; ?><br />
        <input type="text" name="filter_email" value="<?php echo $filter_email; ?>" size="18" style="margin-top:4px; height:16px; border:solid 1px #BBB; color:#003A88;" onclick="this.value = '';">
        </td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>   
      <table cellpadding="0" cellspacing="0" style="float:left;" id="sop9_filter">
        <tr><td> <?php echo $entry_product; ?><br />
        <input type="text" name="filter_product_id" value="<?php echo $filter_product_id; ?>" size="30" style="margin-top:4px; height:16px; border:solid 1px #BBB; color:#003A88;" onclick="this.value = '';">
        </td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>
	  <table cellpadding="0" cellspacing="0" style="float:left;" id="sop10_filter">
        <tr><td><?php echo $entry_option; ?><br />
          <span <?php echo (!$filter_option) ? '' : 'class="vtip"' ?> title="<?php foreach ($product_options as $product_option) { ?><?php if (isset($filter_option[$product_option['options']])) { ?><?php echo $product_option['option_name']; ?>: <?php echo $product_option['option_value']; ?><br /><?php } ?><?php } ?>">        
          <select name="filter_option" id="filter_option" multiple="multiple" size="1">
            <?php foreach ($product_options as $product_option) { ?>
            <?php if (isset($filter_option[$product_option['options']])) { ?>              
            <option value="<?php echo $product_option['options']; ?>" selected="selected"><?php echo $product_option['option_name']; ?>: <?php echo $product_option['option_value']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $product_option['options']; ?>"><?php echo $product_option['option_name']; ?>: <?php echo $product_option['option_value']; ?></option>
            <?php } ?>
            <?php } ?>
          </select></span></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>          
      <table cellpadding="0" cellspacing="0" style="float:left;" id="sop11_filter">
        <tr><td><?php echo $entry_location; ?><br />
          <span <?php echo (!$filter_location) ? '' : 'class="vtip"' ?> title="<?php foreach ($locations as $location) { ?><?php if (isset($filter_location[$location['location_title']])) { ?><?php echo $location['location_name']; ?><br /><?php } ?><?php } ?>">
		  <select name="filter_location" id="filter_location" multiple="multiple" size="1">
            <?php foreach ($locations as $location) { ?>
            <?php if (isset($filter_location[$location['location_title']])) { ?>              
            <option value="<?php echo $location['location_title']; ?>" selected="selected"><?php echo $location['location_name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $location['location_title']; ?>"><?php echo $location['location_name']; ?></option>
            <?php } ?>
            <?php } ?>
          </select></span></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>
	  <table cellpadding="0" cellspacing="0" style="float:left;" id="sop12_filter">
        <tr><td><?php echo $entry_affiliate; ?><br />
          <span <?php echo (!$filter_affiliate) ? '' : 'class="vtip"' ?> title="<?php foreach ($affiliates as $affiliate) { ?><?php if (isset($filter_affiliate[$affiliate['affiliate_id']])) { ?><?php echo $affiliate['affiliate_name']; ?>: <?php echo $affiliate['option_value']; ?><br /><?php } ?><?php } ?>">        
          <select name="filter_affiliate" id="filter_affiliate" multiple="multiple" size="1">
            <?php foreach ($affiliates as $affiliate) { ?>
            <?php if (isset($filter_affiliate[$affiliate['affiliate_id']])) { ?>              
            <option value="<?php echo $affiliate['affiliate_id']; ?>" selected="selected"><?php echo $affiliate['affiliate_name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $affiliate['affiliate_id']; ?>"><?php echo $affiliate['affiliate_name']; ?></option>
            <?php } ?>
            <?php } ?>
          </select></span></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>                                         
      <table cellpadding="0" cellspacing="0" style="float:left;" id="sop13_filter">
        <tr><td><?php echo $entry_shipping; ?><br />
          <span <?php echo (!$filter_shipping) ? '' : 'class="vtip"' ?> title="<?php foreach ($shippings as $shipping) { ?><?php if (isset($filter_shipping[$shipping['shipping_title']])) { ?><?php echo $shipping['shipping_name']; ?><br /><?php } ?><?php } ?>">
		  <select name="filter_shipping" id="filter_shipping" multiple="multiple" size="1">
            <?php foreach ($shippings as $shipping) { ?>
            <?php if (isset($filter_shipping[$shipping['shipping_title']])) { ?>              
            <option value="<?php echo $shipping['shipping_title']; ?>" selected="selected"><?php echo $shipping['shipping_name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $shipping['shipping_title']; ?>"><?php echo $shipping['shipping_name']; ?></option>
            <?php } ?>
            <?php } ?>
          </select></span></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table> 
      <table cellpadding="0" cellspacing="0" style="float:left;" id="sop14_filter">
        <tr><td><?php echo $entry_payment; ?><br />
          <span <?php echo (!$filter_payment) ? '' : 'class="vtip"' ?> title="<?php foreach ($payments as $payment) { ?><?php if (isset($filter_payment[$payment['payment_title']])) { ?><?php echo $payment['payment_name']; ?><br /><?php } ?><?php } ?>">
		  <select name="filter_payment" id="filter_payment" multiple="multiple" size="1">
            <?php foreach ($payments as $payment) { ?>
            <?php if (isset($filter_payment[$payment['payment_title']])) { ?>              
            <option value="<?php echo $payment['payment_title']; ?>" selected="selected"><?php echo $payment['payment_name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $payment['payment_title']; ?>"><?php echo $payment['payment_name']; ?></option>
            <?php } ?>
            <?php } ?>
          </select></span></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table> 
      <table cellpadding="0" cellspacing="0" style="float:left;" id="sop15_filter">
        <tr><td><?php echo $entry_zone; ?><br />
          <span <?php echo (!$filter_shipping_zone) ? '' : 'class="vtip"' ?> title="<?php foreach ($shipping_zones as $shipping_zone) { ?><?php if (isset($filter_shipping_zone[$shipping_zone['shipping_zone']])) { ?><?php echo $shipping_zone['zone_name']; ?><br /><?php } ?><?php } ?>">
		  <select name="filter_shipping_zone" id="filter_shipping_zone" multiple="multiple" size="1">
            <?php foreach ($shipping_zones as $shipping_zone) { ?>
            <?php if (isset($filter_shipping_zone[$shipping_zone['shipping_zone']])) { ?>              
            <option value="<?php echo $shipping_zone['shipping_zone']; ?>" selected="selected"><?php echo $shipping_zone['zone_name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $shipping_zone['shipping_zone']; ?>"><?php echo $shipping_zone['zone_name']; ?></option>
            <?php } ?>
            <?php } ?>
          </select></span></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>           
      <table cellpadding="0" cellspacing="0" style="float:left;" id="sop16_filter">
        <tr><td><?php echo $entry_shipping_country; ?><br />
          <span <?php echo (!$filter_shipping_country) ? '' : 'class="vtip"' ?> title="<?php foreach ($shipping_countries as $shipping_country) { ?><?php if (isset($filter_shipping_country[$shipping_country['shipping_country']])) { ?><?php echo $shipping_country['country_name']; ?><br /><?php } ?><?php } ?>">
		  <select name="filter_shipping_country" id="filter_shipping_country" multiple="multiple" size="1">
            <?php foreach ($shipping_countries as $shipping_country) { ?>
            <?php if (isset($filter_shipping_country[$shipping_country['shipping_country']])) { ?>              
            <option value="<?php echo $shipping_country['shipping_country']; ?>" selected="selected"><?php echo $shipping_country['country_name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $shipping_country['shipping_country']; ?>"><?php echo $shipping_country['country_name']; ?></option>
            <?php } ?>
            <?php } ?>
          </select></span></td><td width="20"></td></tr>
        <tr><td>&nbsp;</td><td></td>
          </tr></table>                                       
      <table cellpadding="0" cellspacing="0" style="float:left;" id="sop17_filter">
        <tr><td><?php echo $entry_payment_country; ?><br />
          <span <?php echo (!$filter_payment_country) ? '' : 'class="vtip"' ?> title="<?php foreach ($payment_countries as $payment_country) { ?><?php if (isset($filter_payment_country[$payment_country['payment_country']])) { ?><?php echo $payment_country['country_name']; ?><br /><?php } ?><?php } ?>">
		  <select name="filter_payment_country" id="filter_payment_country" multiple="multiple" size="1">
            <?php foreach ($payment_countries as $payment_country) { ?>
            <?php if (isset($filter_payment_country[$payment_country['payment_country']])) { ?>              
            <option value="<?php echo $payment_country['payment_country']; ?>" selected="selected"><?php echo $payment_country['country_name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $payment_country['payment_country']; ?>"><u><?php echo $payment_country['country_name']; ?></u></option>
            <?php } ?>
            <?php } ?>
          </select></span></td><td width="20"></td></tr>
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
      <table width="100%" cellspacing="0" cellpadding="3">
        <tr>
          <td width="10%">&nbsp;</td>
          <td width="16%" align="center" nowrap="nowrap"><span id="export_xls" class="export_item"><img src="view/image/XLS.png" width="48" height="48" border="0" title="XLS" /></span><span id="export_html" class="export_item"><img src="view/image/HTML.png" width="48" height="48" border="0" title="HTML" /></span><span id="export_pdf" class="export_item"><img src="view/image/PDF.png" width="48" height="48" border="0" title="PDF" /></span></td>
          <td width="16%" align="center" nowrap="nowrap"><span id="export_xls_order_list" class="export_item"><img src="view/image/XLS.png" width="48" height="48" border="0" title="XLS" /></span><span id="export_html_order_list" class="export_item"><img src="view/image/HTML.png" width="48" height="48" border="0" title="HTML" /></span><span id="export_pdf_order_list" class="export_item"><img src="view/image/PDF.png" width="48" height="48" border="0" title="PDF" /></span></td>
          <td width="16%" align="center" nowrap="nowrap"><span id="export_xls_product_list" class="export_item"><img src="view/image/XLS.png" width="48" height="48" border="0" title="XLS" /></span><span id="export_html_product_list" class="export_item"><img src="view/image/HTML.png" width="48" height="48" border="0" title="HTML" /></span><span id="export_pdf_product_list" class="export_item"><img src="view/image/PDF.png" width="48" height="48" border="0" title="PDF" /></span></td>
          <td width="16%" align="center" nowrap="nowrap"><span id="export_xls_customer_list" class="export_item"><img src="view/image/XLS.png" width="48" height="48" border="0" title="XLS" /></span><span id="export_html_customer_list" class="export_item"><img src="view/image/HTML.png" width="48" height="48" border="0" title="HTML" /></span><span id="export_pdf_customer_list" class="export_item"><img src="view/image/PDF.png" width="48" height="48" border="0" title="PDF" /></span></td>   
          <td width="16%" align="center" nowrap="nowrap"><span id="export_xls_all_details" class="export_item"><img src="view/image/XLS.png" width="48" height="48" border="0" title="XLS" /></span><span id="export_html_all_details" class="export_item"><img src="view/image/HTML.png" width="48" height="48" border="0" title="HTML" /></span><span id="export_pdf_all_details" class="export_item"><img src="view/image/PDF.png" width="48" height="48" border="0" title="PDF" /></span></td>                            
          <td width="10%">&nbsp;</td>                                                                                                                       
        </tr>
        <tr>
          <td width="10%">&nbsp;</td>
          <td width="16%" align="center" nowrap="nowrap"><?php echo $text_export_no_details; ?></td>
          <td width="16%" align="center" nowrap="nowrap"><?php echo $text_export_order_list; ?></td>
          <td width="16%" align="center" nowrap="nowrap"><?php echo $text_export_product_list; ?></td>
          <td width="16%" align="center" nowrap="nowrap"><?php echo $text_export_customer_list; ?></td>   
          <td width="16%" align="center" nowrap="nowrap"><?php echo $text_export_all_details; ?></td>                            
          <td width="10%">&nbsp;</td>                                                                                                                       
        </tr>        
      </table>        
  <input type="hidden" id="export" name="export" value="" />
  </div> 
<?php if ($orders) { ?>
<?php if (($filter_range != 'all_time' && ($filter_group == 'year' or $filter_group == 'quarter' or $filter_group == 'month')) or ($filter_range == 'all_time' && $filter_group == 'year')) { ?>    
<script type="text/javascript">$(function(){ 
$('#show_tab_chart').click(function() {
		$('#tab_chart').slideToggle('slow');
	});
});
</script>  
    <div id="tab_chart">
      <table align="center" cellspacing="0" cellpadding="3">
        <tr>
          <td><div style="float:left;" id="chart1_div"></div><div style="float:right;" id="chart2_div"></div></td>
        </tr>
      </table>
    </div>
<?php } ?> 
<?php } ?> 
    <div id="pagination_content">            
    <table class="list_main">
        <thead>
          <tr>
		  <?php if ($filter_group == 'year') { ?>           
          <td class="left" colspan="2" nowrap="nowrap"><?php echo $column_year; ?></td>
		  <?php } elseif ($filter_group == 'quarter') { ?> 
          <td class="left" nowrap="nowrap"><?php echo $column_year; ?></td>
          <td class="left" nowrap="nowrap"><?php echo $column_quarter; ?></td>       
		  <?php } elseif ($filter_group == 'month') { ?> 
          <td class="left" nowrap="nowrap"><?php echo $column_year; ?></td>
          <td class="left" nowrap="nowrap"><?php echo $column_month; ?></td> 
		  <?php } else { ?>    
          <td class="left" width="70" nowrap="nowrap"><?php echo $column_date_start; ?></td>
          <td class="left" width="70" nowrap="nowrap"><?php echo $column_date_end; ?></td>           
		  <?php } ?> 
          <td id="sop20_title" class="right" nowrap="nowrap"><?php echo $column_orders; ?></td>
          <td id="sop21_title" class="right" nowrap="nowrap"><?php echo $column_customers; ?></td>          
          <td id="sop22_title" class="right" nowrap="nowrap"><?php echo $column_products; ?></td>
          <td id="sop23_title" class="right" nowrap="nowrap"><?php echo $column_sub_total; ?></td>
          <td id="sop24_title" class="right" nowrap="nowrap"><?php echo $column_hf; ?><sup><span class="vtip" style="font-weight:bold; cursor:pointer; color:#F00;" title="<?php echo $column_handling; ?>">&#63;</span></sup></td>
          <td id="sop25_title" class="right" nowrap="nowrap"><?php echo $column_lof; ?><sup><span class="vtip" style="font-weight:bold; cursor:pointer; color:#F00;" title="<?php echo $column_loworder; ?>">&#63;</span></sup></td>                    
          <td id="sop26_title" class="right" nowrap="nowrap"><?php echo $column_points; ?></td>          
          <td id="sop27_title" class="right" nowrap="nowrap"><?php echo $column_shipping; ?></td>
          <td id="sop28_title" class="right" nowrap="nowrap"><?php echo $column_coupon; ?></td>          
          <td id="sop29_title" class="right" nowrap="nowrap"><?php echo $column_tax; ?></td>
          <td id="sop30_title" class="right" nowrap="nowrap"><?php echo $column_credit; ?></td>           
          <td id="sop31_title" class="right" nowrap="nowrap"><?php echo $column_voucher; ?></td> 
          <td id="sop32_title" class="right" nowrap="nowrap"><?php echo $column_commission; ?></td>                     
          <td id="sop33_title" class="right" nowrap="nowrap"><?php echo $column_total; ?></td>
          <td id="sop34_title" class="right" nowrap="nowrap"><?php echo $column_prod_costs; ?></td>        
          <td id="sop35_title" class="right" nowrap="nowrap"><?php echo $column_net_profit; ?></td>   
          <td id="sop36_title" class="right" nowrap="nowrap"><?php echo $column_profit_margin; ?></td>                             
         <?php if ($filter_details == 1 OR $filter_details == 2 OR $filter_details == 3) { ?><td class="right" nowrap="nowrap"><?php echo $column_action; ?></td><?php } ?> 
          </tr>
      	  </thead>
          <?php if ($orders) { ?>
          <?php foreach ($orders as $order) { ?>
      	  <tbody class="element">    
          <tr>
		  <?php if ($filter_group == 'year') { ?>           
          <td class="left" colspan="2" nowrap="nowrap"><?php echo $order['year']; ?></td>
		  <?php } elseif ($filter_group == 'quarter') { ?> 
          <td class="left" nowrap="nowrap"><?php echo $order['year']; ?></td>
          <td class="left" nowrap="nowrap"><?php echo $order['quarter']; ?></td>  
		  <?php } elseif ($filter_group == 'month') { ?> 
          <td class="left" nowrap="nowrap"><?php echo $order['year']; ?></td>
          <td class="left" nowrap="nowrap"><?php echo $order['month']; ?></td>
		  <?php } else { ?>    
          <td class="left" nowrap="nowrap"><?php echo $order['date_start']; ?></td>
          <td class="left" nowrap="nowrap"><?php echo $order['date_end']; ?></td>         
		  <?php } ?>           
          <td id="sop20_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap"><?php echo $order['orders']; ?></td>
          <td id="sop21_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap"><?php echo $order['customers']; ?></td>          
          <td id="sop22_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap"><?php echo $order['products']; ?></td>
          <td id="sop23_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#DCFFB9;"><?php echo $order['sub_total']; ?></td>
          <td id="sop24_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#DCFFB9;"><?php echo $order['handling']; ?></td>
          <td id="sop25_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#DCFFB9;"><?php echo $order['low_order_fee']; ?></td>                    
          <td id="sop26_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#ffd7d7;"><?php echo $order['reward']; ?></td>          
          <td id="sop27_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap"><?php echo $order['shipping']; ?></td>
          <td id="sop28_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#ffd7d7;"><?php echo $order['coupon']; ?></td>          
          <td id="sop29_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap"><?php echo $order['tax']; ?></td>
          <td id="sop30_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#ffd7d7;"><?php echo $order['credit']; ?></td>            
          <td id="sop31_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#ffd7d7;"><?php echo $order['voucher']; ?></td>    
          <td id="sop32_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#ffd7d7;"><?php echo $order['commission']; ?></td>                   
          <td id="sop33_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap"><?php echo $order['total']; ?></td>
          <td id="sop34_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#ffd7d7;"><?php echo $order['prod_costs']; ?></td>         
          <td id="sop35_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#c4d9ee; font-weight:bold;"><?php echo $order['netprofit']; ?></td>     
          <td id="sop36_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#c4d9ee; font-weight:bold;"><?php echo $order['profit_margin_percent']; ?></td>                            
          <?php if ($filter_details == 1 OR $filter_details == 2 OR $filter_details == 3) { ?><td class="right" nowrap="nowrap">[ <a id="show_details_<?php echo $order['order_id']; ?>"><?php echo $text_detail; ?></a> ]</td><?php } ?> 
          </tr>
<tr class="detail">
<td colspan="20" class="center">
<?php if ($filter_details == 1) { ?>
<script type="text/javascript">$(function(){ 
$('#show_details_<?php echo $order['order_id']; ?>').click(function() {
		$('#tab_details_<?php echo $order['order_id']; ?>').slideToggle('slow');
	});
});
</script>
<div id="tab_details_<?php echo $order['order_id']; ?>" style="display:none">
    <table class="list_detail">
      <thead>
        <tr>
          <td id="sop40_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_order_order_id; ?></td>        
          <td id="sop41_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_order_date_added; ?></td>
          <td id="sop42_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_order_inv_no; ?></td>                  
          <td id="sop43_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_order_customer; ?></td>
          <td id="sop44_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_order_email; ?></td>
          <td id="sop45_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_order_customer_group; ?></td>
          <td id="sop46_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_order_shipping_method; ?></td>
          <td id="sop47_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_order_payment_method; ?></td>          
          <td id="sop48_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_order_status; ?></td>
          <td id="sop49_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_order_store; ?></td>
          <td id="sop50_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_order_currency; ?></td>
          <td id="sop51_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_order_quantity; ?></td>  
          <td id="sop52_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_order_sub_total; ?></td>  
          <td id="sop531_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_order_hf; ?></td>  
          <td id="sop532_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_order_lof; ?></td>                              
          <td id="sop54_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_order_shipping; ?></td>         
          <td id="sop55_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_order_tax; ?></td>
          <td id="sop56_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_order_value; ?></td>          
          <td id="sop57_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_order_costs; ?></td> 
          <td id="sop58_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_order_profit; ?></td>
          <td id="sop59_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_profit_margin; ?></td>         
        </tr>
      </thead>
        <tr bgcolor="#FFFFFF">
          <td id="sop40_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><a><?php echo $order['order_ord_id']; ?></a></td>        
          <td id="sop41_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['order_order_date']; ?></td>
          <td id="sop42_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['order_inv_no']; ?></td>
          <td id="sop43_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['order_name']; ?></td>
          <td id="sop44_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['order_email']; ?></td>
          <td id="sop45_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['order_group']; ?></td>
          <td id="sop46_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['order_shipping_method']; ?></td>
          <td id="sop47_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['order_payment_method']; ?></td>          
          <td id="sop48_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['order_status']; ?></td>
          <td id="sop49_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['order_store']; ?></td> 
          <td id="sop50_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap"><?php echo $order['order_currency']; ?></td>          
          <td id="sop51_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap"><?php echo $order['order_products']; ?></td> 
          <td id="sop52_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#DCFFB9;"><?php echo $order['order_sub_total']; ?></td> 
          <td id="sop531_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#DCFFB9;"><?php echo $order['order_hf']; ?></td> 
          <td id="sop532_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#DCFFB9;"><?php echo $order['order_lof']; ?></td>                     
          <td id="sop54_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap"><?php echo $order['order_shipping']; ?></td>           
          <td id="sop55_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap"><?php echo $order['order_tax']; ?></td>                              
          <td id="sop56_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap"><?php echo $order['order_value']; ?></td>
          <td id="sop57_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#ffd7d7;">-<?php echo $order['order_costs']; ?></td>
          <td id="sop58_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#c4d9ee; font-weight:bold;"><?php echo $order['order_profit']; ?></td> 
          <td id="sop59_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#c4d9ee; font-weight:bold;"><?php echo $order['order_profit_margin_percent']; ?>%</td>      
         </tr>
    </table>  
</div>
<?php } ?>    
<?php if ($filter_details == 2) { ?>
<script type="text/javascript">$(function(){ 
$('#show_details_<?php echo $order['order_id']; ?>').click(function() {
		$('#tab_details_<?php echo $order['order_id']; ?>').slideToggle('slow');
	});
});
</script>
<div id="tab_details_<?php echo $order['order_id']; ?>" style="display:none">
    <table class="list_detail">
      <thead>
        <tr>
          <td id="sop60_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_prod_order_id; ?></td>  
          <td id="sop61_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_prod_date_added; ?></td>
          <td id="sop62_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_prod_inv_no; ?></td> 
          <td id="sop63_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_prod_id; ?></td>                                          
          <td id="sop64_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_prod_sku; ?></td>          
          <td id="sop65_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_prod_name; ?></td> 
          <td id="sop66_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_prod_option; ?></td>           
          <td id="sop67_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_prod_model; ?></td>            
          <td id="sop68_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_prod_manu; ?></td> 
          <td id="sop69_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_prod_currency; ?></td>   
          <td id="sop70_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_prod_price; ?></td>                     
          <td id="sop71_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_prod_quantity; ?></td>                  
          <td id="sop72_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_prod_total; ?></td>   
          <td id="sop73_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_prod_tax; ?></td> 
          <td id="sop74_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_prod_costs; ?></td> 
          <td id="sop75_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_prod_profit; ?></td>
          <td id="sop76_<?php echo $order['order_id']; ?>_title" class="right" nowrap="nowrap"><?php echo $column_profit_margin; ?></td>                                                                      
        </tr>
      </thead>
        <tr bgcolor="#FFFFFF">
          <td id="sop60_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><a><?php echo $order['product_ord_id']; ?></a></td>  
          <td id="sop61_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['product_order_date']; ?></td>
          <td id="sop62_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['product_inv_no']; ?></td>
          <td id="sop63_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['product_pid']; ?></td>  
          <td id="sop64_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['product_sku']; ?></td>                    
          <td id="sop65_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['product_name']; ?></td> 
          <td id="sop66_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['product_option']; ?></td>           
          <td id="sop67_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['product_model']; ?></td>           
          <td id="sop68_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['product_manu']; ?></td> 
          <td id="sop69_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap"><?php echo $order['product_currency']; ?></td> 
          <td id="sop70_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap"><?php echo $order['product_price']; ?></td> 
          <td id="sop71_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap"><?php echo $order['product_quantity']; ?></td> 
          <td id="sop72_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#DCFFB9;"><?php echo $order['product_total']; ?></td>    
          <td id="sop73_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap"><?php echo $order['product_tax']; ?></td>  
          <td id="sop74_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#ffd7d7;">-<?php echo $order['product_costs']; ?></td>
          <td id="sop75_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#c4d9ee; font-weight:bold;"><?php echo $order['product_profit']; ?></td>
          <td id="sop76_<?php echo $order['order_id']; ?>" class="right" nowrap="nowrap" style="background-color:#c4d9ee; font-weight:bold;"><?php echo $order['product_profit_margin_percent']; ?>%</td>   
         </tr>       
    </table>
</div> 
<?php } ?>  
<?php if ($filter_details == 3) { ?>
<script type="text/javascript">$(function(){ 
$('#show_details_<?php echo $order['order_id']; ?>').click(function() {
		$('#tab_details_<?php echo $order['order_id']; ?>').slideToggle('slow');
	});
});
</script>
<div id="tab_details_<?php echo $order['order_id']; ?>" style="display:none">
    <table class="list_detail">
      <thead>
        <tr>
          <td id="sop80_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_customer_order_id; ?></td>        
          <td id="sop81_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_customer_date_added; ?></td>
          <td id="sop82_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_customer_inv_no; ?></td>           
          <td id="sop83_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_customer_cust_id; ?></td>           
          <td id="sop84_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_billing_name; ?></td> 
          <td id="sop85_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_billing_company; ?></td> 
          <td id="sop86_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_billing_address_1; ?></td> 
          <td id="sop87_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_billing_address_2; ?></td> 
          <td id="sop88_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_billing_city; ?></td>
          <td id="sop89_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_billing_zone; ?></td> 
          <td id="sop90_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_billing_postcode; ?></td>
          <td id="sop91_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_billing_country; ?></td>
          <td id="sop92_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_customer_telephone; ?></td>
          <td id="sop93_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_shipping_name; ?></td> 
          <td id="sop94_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_shipping_company; ?></td> 
          <td id="sop95_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_shipping_address_1; ?></td> 
          <td id="sop96_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_shipping_address_2; ?></td> 
          <td id="sop97_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_shipping_city; ?></td>
          <td id="sop98_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_shipping_zone; ?></td> 
          <td id="sop99_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_shipping_postcode; ?></td>
          <td id="sop100_<?php echo $order['order_id']; ?>_title" class="left" nowrap="nowrap"><?php echo $column_shipping_country; ?></td>          
        </tr>
      </thead>
        <tr bgcolor="#FFFFFF">
          <td id="sop80_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['customer_ord_id']; ?></td>        
          <td id="sop81_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['customer_order_date']; ?></td>
          <td id="sop82_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['customer_inv_no']; ?></td>
          <td id="sop83_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['customer_cust_id']; ?></td>             
          <td id="sop84_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['billing_name']; ?></td>         
          <td id="sop85_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['billing_company']; ?></td> 
          <td id="sop86_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['billing_address_1']; ?></td> 
          <td id="sop87_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['billing_address_2']; ?></td> 
          <td id="sop88_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['billing_city']; ?></td> 
          <td id="sop89_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['billing_zone']; ?></td> 
          <td id="sop90_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['billing_postcode']; ?></td>                    
          <td id="sop91_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['billing_country']; ?></td>
          <td id="sop92_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['customer_telephone']; ?></td> 
          <td id="sop93_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['shipping_name']; ?></td>         
          <td id="sop94_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['shipping_company']; ?></td> 
          <td id="sop95_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['shipping_address_1']; ?></td> 
          <td id="sop96_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['shipping_address_2']; ?></td> 
          <td id="sop97_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['shipping_city']; ?></td> 
          <td id="sop98_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['shipping_zone']; ?></td> 
          <td id="sop99_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['shipping_postcode']; ?></td>                    
          <td id="sop100_<?php echo $order['order_id']; ?>" class="left" nowrap="nowrap"><?php echo $order['shipping_country']; ?></td>          
         </tr>
    </table>
</div> 
<?php } ?>            
</td>
</tr>          
          <?php } ?>
        <tr>
          <td colspan="20"></td>
        </tr> 
      </tbody>                 
        <tr>
          <td colspan="2" class="right" style="background-color:#E7EFEF;"><strong><?php echo $text_filter_total; ?></strong></td>
          <td id="sop20_total" class="right" style="background-color:#E7EFEF; color:#003A88;"><strong><?php echo $order['orders_total']; ?></strong></td> 
          <td id="sop21_total" class="right" style="background-color:#E7EFEF; color:#003A88;"><strong><?php echo $order['customers_total']; ?></strong></td> 
          <td id="sop22_total" class="right" style="background-color:#E7EFEF; color:#003A88;"><strong><?php echo $order['products_total']; ?></strong></td> 
          <td id="sop23_total" class="right" style="background-color:#DCFFB9; color:#003A88;"><strong><?php echo $order['sub_total_total']; ?></strong></td> 
          <td id="sop24_total" class="right" style="background-color:#DCFFB9; color:#003A88;"><strong><?php echo $order['handling_total']; ?></strong></td> 
          <td id="sop25_total" class="right" style="background-color:#DCFFB9; color:#003A88;"><strong><?php echo $order['low_order_fee_total']; ?></strong></td> 
          <td id="sop26_total" class="right" style="background-color:#ffd7d7; color:#003A88;"><strong><?php echo $order['reward_total']; ?></strong></td> 
          <td id="sop27_total" class="right" style="background-color:#E7EFEF; color:#003A88;"><strong><?php echo $order['shipping_total']; ?></strong></td> 
          <td id="sop28_total" class="right" style="background-color:#ffd7d7; color:#003A88;"><strong><?php echo $order['coupon_total']; ?></strong></td> 
          <td id="sop29_total" class="right" style="background-color:#E7EFEF; color:#003A88;"><strong><?php echo $order['tax_total']; ?></strong></td>
          <td id="sop30_total" class="right" style="background-color:#ffd7d7; color:#003A88;"><strong><?php echo $order['credit_total']; ?></strong></td> 
          <td id="sop31_total" class="right" style="background-color:#ffd7d7; color:#003A88;"><strong><?php echo $order['voucher_total']; ?></strong></td>                     
          <td id="sop32_total" class="right" style="background-color:#ffd7d7; color:#003A88;"><strong><?php echo $order['commission_total']; ?></strong></td> 
          <td id="sop33_total" class="right" style="background-color:#E7EFEF; color:#003A88;"><strong><?php echo $order['total_total']; ?></strong></td> 
          <td id="sop34_total" class="right" style="background-color:#ffd7d7; color:#003A88;"><strong><?php echo $order['prod_costs_total']; ?></strong></td>           
          <td id="sop35_total" class="right" style="background-color:#BCD5ED; color:#003A88;"><strong><?php echo $order['netprofit_total']; ?></strong></td>           
          <td id="sop36_total" class="right" style="background-color:#BCD5ED; color:#003A88;"><strong><?php echo $order['profit_margin_total_percent']; ?></strong></td>          
          <?php if ($filter_details == 1 OR $filter_details == 2 OR $filter_details == 3) { ?><td style="background-color:#E7EFEF;"></td><?php } ?>                  
        </tr>           
          <?php } else { ?>
          <tr>
            <td class="noresult" colspan="20"><?php echo $text_no_results; ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
      <?php if ($orders) { ?>    
      <div class="pagination_report"></div>
      <?php } ?>       
    </div>
  </div>
</div>  
</form>
<script type="text/javascript" src="view/javascript/jquery/jquery.multiSelect.js"></script>
<script type="text/javascript" src="view/javascript/jquery/jquery.paging.min.js"></script>
<script type="text/javascript" src="view/javascript/jquery/vtip.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('#date-start').datepicker({dateFormat: 'yy-mm-dd'});

	$('#date-end').datepicker({dateFormat: 'yy-mm-dd'});
	
    $('#filter_order_status_id').multiSelect({
      selectAllText:'<?php echo $text_all_status; ?>', noneSelected:'<?php echo $text_all_status; ?>', oneOrMoreSelected:'<?php echo $text_selected; ?>'
      });
	
    $('#filter_store_id').multiSelect({
      selectAllText:'<?php echo $text_all_stores; ?>', noneSelected:'<?php echo $text_all_stores; ?>', oneOrMoreSelected:'<?php echo $text_selected; ?>'
      });
	
    $('#filter_currency').multiSelect({
      selectAllText:'<?php echo $text_all_currencies; ?>', noneSelected:'<?php echo $text_all_currencies; ?>', oneOrMoreSelected:'<?php echo $text_selected; ?>'
      });

    $('#filter_taxes').multiSelect({
      selectAllText:'<?php echo $text_all_taxes; ?>', noneSelected:'<?php echo $text_all_taxes; ?>', oneOrMoreSelected:'<?php echo $text_selected; ?>'
      });

    $('#filter_customer_group_id').multiSelect({
      selectAllText:'<?php echo $text_all_groups; ?>', noneSelected:'<?php echo $text_all_groups; ?>', oneOrMoreSelected:'<?php echo $text_selected; ?>'
      });

    $('#filter_option').multiSelect({
      selectAllText:'<?php echo $text_all_options; ?>', noneSelected:'<?php echo $text_all_options; ?>', oneOrMoreSelected:'<?php echo $text_selected; ?>'
      });
	
    $('#filter_location').multiSelect({
      selectAllText:'<?php echo $text_all_locations; ?>', noneSelected:'<?php echo $text_all_locations; ?>', oneOrMoreSelected:'<?php echo $text_selected; ?>'
      });

    $('#filter_affiliate').multiSelect({
      selectAllText:'<?php echo $text_all_affiliates; ?>', noneSelected:'<?php echo $text_all_affiliates; ?>', oneOrMoreSelected:'<?php echo $text_selected; ?>'
      });
	
    $('#filter_shipping').multiSelect({
      selectAllText:'<?php echo $text_all_shippings; ?>', noneSelected:'<?php echo $text_all_shippings; ?>', oneOrMoreSelected:'<?php echo $text_selected; ?>'
      });

    $('#filter_payment').multiSelect({
      selectAllText:'<?php echo $text_all_payments; ?>', noneSelected:'<?php echo $text_all_payments; ?>', oneOrMoreSelected:'<?php echo $text_selected; ?>'
      });

    $('#filter_shipping_zone').multiSelect({
      selectAllText:'<?php echo $text_all_zones; ?>', noneSelected:'<?php echo $text_all_zones; ?>', oneOrMoreSelected:'<?php echo $text_selected; ?>'
      });
	
    $('#filter_shipping_country').multiSelect({
      selectAllText:'<?php echo $text_all_countries; ?>', noneSelected:'<?php echo $text_all_countries; ?>', oneOrMoreSelected:'<?php echo $text_selected; ?>'
      });
	
    $('#filter_payment_country').multiSelect({
      selectAllText:'<?php echo $text_all_countries; ?>', noneSelected:'<?php echo $text_all_countries; ?>', oneOrMoreSelected:'<?php echo $text_selected; ?>'
      });
	
    $('#button').click(function() {
      $('#report').submit() ;
      return(false)
    });	
	
    $('#export_xls').click(function() {
      $('#export').val('1') ; // export_xls: #1
      $('#report').attr('target', '_blank'); // opening file in a new window
      $('#report').submit() ;
      $('#report').attr('target', '_self'); // preserve current form      
      $('#export').val('') ; 
      return(false)
    });	
	
    $('#export_xls_order_list').click(function() {
      $('#export').val('2') ; // export_xls_order_list: #2
      $('#report').attr('target', '_blank'); // opening file in a new window
      $('#report').submit() ;
      $('#report').attr('target', '_self'); // preserve current form      
      $('#export').val('') ; 
      return(false)
    });	

    $('#export_xls_product_list').click(function() {
      $('#export').val('3') ; // export_xls_product_list: #3
      $('#report').attr('target', '_blank'); // opening file in a new window
      $('#report').submit() ;
      $('#report').attr('target', '_self'); // preserve current form      
      $('#export').val('') ; 
      return(false)
    });	

    $('#export_xls_customer_list').click(function() {
      $('#export').val('4') ; // export_xls_customer_list: #4
      $('#report').attr('target', '_blank'); // opening file in a new window
      $('#report').submit() ;
      $('#report').attr('target', '_self'); // preserve current form      
      $('#export').val('') ; 
      return(false)
    });	

    $('#export_xls_all_details').click(function() {
      $('#export').val('5') ; // export_xls_all_details: #5
      $('#report').attr('target', '_blank'); // opening file in a new window
      $('#report').submit() ;
      $('#report').attr('target', '_self'); // preserve current form      
      $('#export').val('') ; 
      return(false)
    });	
	
    $('#export_html').click(function() {
      $('#export').val('6') ; // export_html: #6
      $('#report').attr('target', '_blank'); // opening file in a new window
      $('#report').submit() ;
      $('#report').attr('target', '_self'); // preserve current form      
      $('#export').val('') ; 
      return(false)
    });	
	
    $('#export_html_order_list').click(function() {
      $('#export').val('7') ; // export_html_order_list: #7
      $('#report').attr('target', '_blank'); // opening file in a new window
      $('#report').submit() ;
      $('#report').attr('target', '_self'); // preserve current form      
      $('#export').val('') ; 
      return(false)
    });	
	
    $('#export_html_product_list').click(function() {
      $('#export').val('8') ; // export_html_product_list: #8
      $('#report').attr('target', '_blank'); // opening file in a new window
      $('#report').submit() ;
      $('#report').attr('target', '_self'); // preserve current form      
      $('#export').val('') ; 
      return(false)
    });		
	
    $('#export_html_customer_list').click(function() {
      $('#export').val('9') ; // export_html_customer_list: #9
      $('#report').attr('target', '_blank'); // opening file in a new window
      $('#report').submit() ;
      $('#report').attr('target', '_self'); // preserve current form      
      $('#export').val('') ; 
      return(false)
    });	
	
    $('#export_html_all_details').click(function() {
      $('#export').val('10') ; // export_html_all_details: #10
      $('#report').attr('target', '_blank'); // opening file in a new window
      $('#report').submit() ;
      $('#report').attr('target', '_self'); // preserve current form      
      $('#export').val('') ; 
      return(false)
    });	
	
    $('#export_pdf').click(function() {
      $('#export').val('11') ; // export_pdf: #11
      $('#report').attr('target', '_blank'); // opening file in a new window
      $('#report').submit() ;
      $('#report').attr('target', '_self'); // preserve current form      
      $('#export').val('') ; 
      return(false)
    });	
	
    $('#export_pdf_order_list').click(function() {
      $('#export').val('12') ; // export_pdf_order_list: #12
      $('#report').attr('target', '_blank'); // opening file in a new window
      $('#report').submit() ;
      $('#report').attr('target', '_self'); // preserve current form      
      $('#export').val('') ; 
      return(false)
    });	
	
    $('#export_pdf_product_list').click(function() {
      $('#export').val('13') ; // export_pdf_product_list: #13
      $('#report').attr('target', '_blank'); // opening file in a new window
      $('#report').submit() ;
      $('#report').attr('target', '_self'); // preserve current form      
      $('#export').val('') ; 
      return(false)
    });		
	
    $('#export_pdf_customer_list').click(function() {
      $('#export').val('14') ; // export_pdf_customer_list: #14
      $('#report').attr('target', '_blank'); // opening file in a new window
      $('#report').submit() ;
      $('#report').attr('target', '_self'); // preserve current form      
      $('#export').val('') ; 
      return(false)
    });	
	
    $('#export_pdf_all_details').click(function() {
      $('#export').val('15') ; // export_pdf_all_details: #15
      $('#report').attr('target', '_blank'); // opening file in a new window
      $('#report').submit() ;
      $('#report').attr('target', '_self'); // preserve current form      
      $('#export').val('') ; 
      return(false)
    });	
});
</script>  
<script type="text/javascript"><!--
$('input[name=\'filter_company\']').autocomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=report/adv_sale_profit/customer_autocomplete&token=<?php echo $token; ?>&filter_company=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
						label: item.cust_company,
						value: item.customer_id
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('input[name=\'filter_company\']').val(ui.item.label);
						
		return false;
	}
});

$('input[name=\'filter_customer_id\']').autocomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=report/adv_sale_profit/customer_autocomplete&token=<?php echo $token; ?>&filter_customer_id=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
						label: item.cust_name,
						value: item.customer_id
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('input[name=\'filter_customer_id\']').val(ui.item.label);
						
		return false;
	}
});

$('input[name=\'filter_email\']').autocomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=report/adv_sale_profit/customer_autocomplete&token=<?php echo $token; ?>&filter_email=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
						label: item.cust_email,
						value: item.customer_id
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('input[name=\'filter_email\']').val(ui.item.label);
						
		return false;
	}
});

$('input[name=\'filter_product_id\']').autocomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=report/adv_sale_profit/product_autocomplete&token=<?php echo $token; ?>&filter_product_id=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
						label: item.prod_name,
						value: item.product_id
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('input[name=\'filter_product_id\']').val(ui.item.label);
						
		return false;
	}
});
//--></script> 
<?php if ($orders) { ?>    
<?php if (($filter_range != 'all_time' && ($filter_group == 'year' or $filter_group == 'quarter' or $filter_group == 'month')) or ($filter_range == 'all_time' && $filter_group == 'year')) { ?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript"><!--
	google.load('visualization', '1', {packages: ['corechart']});
      google.setOnLoadCallback(drawChart);      
	  function drawChart() {        
	  	var data = google.visualization.arrayToDataTable([
			<?php if ($orders && $filter_group == 'month') {
				echo "['" . $column_month . "','". $column_orders . "','" . $column_customers . "','" . $column_products . "'],";
					foreach ($orders as $key => $order) {
						if (count($orders)==($key+1)) {
							echo "['" . $order['year_month'] . "',". $order['orders'] . ",". $order['customers'] . ",". $order['products'] . "]";
						} else {
							echo "['" . $order['year_month'] . "',". $order['orders'] . ",". $order['customers'] . ",". $order['products'] . "],";
						}
					}	
			} elseif ($orders && $filter_group == 'quarter') {
				echo "['" . $column_quarter . "','". $column_orders . "','" . $column_customers . "','" . $column_products . "'],";
					foreach ($orders as $key => $order) {
						if (count($orders)==($key+1)) {
							echo "['" . $order['year_quarter'] . "',". $order['orders'] . ",". $order['customers'] . ",". $order['products'] . "]";
						} else {
							echo "['" . $order['year_quarter'] . "',". $order['orders'] . ",". $order['customers'] . ",". $order['products'] . "],";
						}
					}	
			} elseif ($orders && $filter_group == 'year') {
				echo "['" . $column_year . "','". $column_orders . "','" . $column_customers . "','" . $column_products . "'],";
					foreach ($orders as $key => $order) {
						if (count($orders)==($key+1)) {
							echo "['" . $order['year'] . "',". $order['orders'] . ",". $order['customers'] . ",". $order['products'] . "]";
						} else {
							echo "['" . $order['year'] . "',". $order['orders'] . ",". $order['customers'] . ",". $order['products'] . "],";
						}
					}	
			} 
			;?>
		]);

        var options = {
			width: 630,	
			height: 266,  
			colors: ['#edc240', '#9dc7e8', '#CCCCCC'],
			chartArea: {left:30, top:30, width:"75%", height:"70%"},
			pointSize: '4',
			legend: {position: 'right', alignment: 'start', textStyle: {color: '#666666', fontSize: 12}}
		};

			var chart = new google.visualization.LineChart(document.getElementById('chart1_div'));
			chart.draw(data, options);
	}
//--></script>
<script type="text/javascript"><!--
	google.load('visualization', '1', {packages: ['corechart']});
	function drawVisualization() {
   		var data = google.visualization.arrayToDataTable([
			<?php if ($orders && $filter_group == 'month') {
				echo "['" . $column_month . "','". $column_sales . "','" . $column_total_costs . "','" . $column_total_profit . "'],";
					foreach ($orders as $key => $order) {
						if (count($orders)==($key+1)) {
							echo "['" . $order['year_month'] . "',". $order['gsales'] . ",". $order['gcosts'] . ",". $order['gnetprofit'] . "]";
						} else {
							echo "['" . $order['year_month'] . "',". $order['gsales'] . ",". $order['gcosts'] . ",". $order['gnetprofit'] . "],";
						}
					}	
			} elseif ($orders && $filter_group == 'quarter') {
				echo "['" . $column_quarter . "','". $column_sales . "','" . $column_total_costs . "','" . $column_total_profit . "'],";
					foreach ($orders as $key => $order) {
						if (count($orders)==($key+1)) {
							echo "['" . $order['year_quarter'] . "',". $order['gsales'] . ",". $order['gcosts'] . ",". $order['gnetprofit'] . "]";
						} else {
							echo "['" . $order['year_quarter'] . "',". $order['gsales'] . ",". $order['gcosts'] . ",". $order['gnetprofit'] . "],";
						}
					}	
			} elseif ($orders && $filter_group == 'year') {
				echo "['" . $column_year . "','". $column_sales . "','" . $column_total_costs . "','" . $column_total_profit . "'],";
					foreach ($orders as $key => $order) {
						if (count($orders)==($key+1)) {
							echo "['" . $order['year'] . "',". $order['gsales'] . ",". $order['gcosts'] . ",". $order['gnetprofit'] . "]";
						} else {
							echo "['" . $order['year'] . "',". $order['gsales'] . ",". $order['gcosts'] . ",". $order['gnetprofit'] . "],";
						}
					}	
			} 
			;?>
		]);

        var options = {
			width: 630,	
			height: 266,  
			colors: ['#b5e08b', '#ed9999', '#739cc3'],
			chartArea: {left:45, top:30, width:"75%", height:"70%"},
			legend: {position: 'right', alignment: 'start', textStyle: {color: '#666666', fontSize: 12}},				
			seriesType: "bars",
			series: {2: {type: "line", lineWidth: '3', pointSize: '5'}}
		};

			var chart = new google.visualization.ComboChart(document.getElementById('chart2_div'));
			chart.draw(data, options);
	}
	
	google.setOnLoadCallback(drawVisualization);
//--></script>
<?php } ?>
<?php } ?>
<?php echo $footer; ?>