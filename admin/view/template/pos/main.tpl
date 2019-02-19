<?php echo $header; ?>
<style>
	.impact_large {
		font-family: impact, 'Nimbus Sans L', 'League Gothic','Bebas Neue','Arial Narrow', Arial, sans-serif;
		font-style: normal;
		font-size: 20px ;
		font-weight: normal;
		font-stretch: ultra-condensed;
		color: #005375
	}
	.impact_small {
		font-family: impact, 'Nimbus Sans L', 'League Gothic','Bebas Neue','Arial Narrow', Arial, sans-serif;
		font-style: normal;
		font-size: 16px ;
		font-weight: normal;
		font-stretch: ultra-condensed;
	}
	.pos_content {
		background-color: white;
		border: 1px solid white;
		-moz-box-shadow: 0px 0px 5px rgba(0,0,80,0.9);
		-webkit-box-shadow: 0px 0px 5px rgba(0,0,80,0.9);
		box-shadow: 0px 0px 5px rgba(0,0,80,0.9);
		behavior: url('view/template/pos/pie/PIE.php');
	}
	.top_wrapper {
		background-color: #F0F0F0; 
		border: 1px solid #F0F0F0;
		-moz-box-shadow: 0px 0px 15px rgba(58,58,58,0.9);
		-webkit-box-shadow: 0px 0px 15px rgba(58,58,58,0.9);
		box-shadow: 0px 0px 15px rgba(58,58,58,0.9);
		behavior: url('view/template/pos/pie/PIE.php');
	}
	.pos_list {
		border-collapse: collapse;
		width: 100%;
		margin-bottom: 3px;
	}
	.pos_list td {
		border-right: 1px solid #DDDDDD;
	}
	.pos_separator {
		background-color: #005375;
		height: 10px;
		width: 1024px;
	}
	.pos_wrapper {
		border-top: 1px solid #CCCCCC;  
		border-left: 1px solid #CCCCCC; 
		border-right: 1px solid #CCCCCC; 
		border-bottom: 1px solid #CCCCCC;
	}
	.pos_form_table {
		border-collapse: collapse;
		width: 100%;
		background-color: #EFEFEF;
		margin-bottom: 10px;
	}
	.pos_form_table td {
		border-right: 1px solid #DADADA;
		border-bottom: 1px solid #DADADA;
	}
	.pos_form_table thead td {
		padding: 0px 5px;
	}
	.pos_form_table thead td a, .pos_form_table thead td {
		text-decoration: none;
		color: #222222;
		font-weight: bold;
	}
	.pos_form_table tbody td a {
		text-decoration: underline;
	}
	.pos_form_table tbody td {
		vertical-align: middle;
		padding: 0px 5px;
	}
	.pos_form_table .left {
		text-align: left;
		padding: 7px;
	}
	.pos_form_table .right {
		text-align: right;
		padding: 7px;
	}
	.pos_form_table .center {
		text-align: center;
		padding: 7px;
	}
	.pos_form_table .asc {
		padding-right: 15px;
		background: url('../image/asc.png') right center no-repeat;
	}
	.pos_form_table .desc {
		padding-right: 15px;
		background: url('../image/desc.png') right center no-repeat;
	}
	.pos_form_table tr.filter td, .pos_form_table tr:hover.filter td {
		padding: 5px;
		background: #C4C5C6;
	}
	.pos_success {
		padding: 10px 10px 10px 33px;
		margin-top: 0px;
		background: #EAF7D9 url('view/image/success.png') 10px center no-repeat;
		border: 1px solid #BBDF8D;
		color: #555555;
		-webkit-border-radius: 5px 5px 5px 5px;
		-moz-border-radius: 5px 5px 5px 5px;
		-khtml-border-radius: 5px 5px 5px 5px;
		border-radius: 5px 5px 5px 5px;
	}
	.pos_warning {
		height: 35px;
		padding: 10px 10px 10px 33px;
		margin-top: 0px;
		background: #FFD1D1 url('view/image/warning.png') 10px center no-repeat;
		border: 1px solid #F8ACAC;
		color: #555555;
		-webkit-border-radius: 5px 5px 5px 5px;
		-moz-border-radius: 5px 5px 5px 5px;
		-khtml-border-radius: 5px 5px 5px 5px;
		border-radius: 5px 5px 5px 5px;
	}
	.pos_attention {
		padding: 10px 10px 10px 33px;
		margin-top: 0px;
		background: #FFF5CC url('view/image/attention.png') 10px center no-repeat;
		border: 1px solid #F2DD8C;
		color: #555555;
		-webkit-border-radius: 5px 5px 5px 5px;
		-moz-border-radius: 5px 5px 5px 5px;
		-khtml-border-radius: 5px 5px 5px 5px;
		border-radius: 5px 5px 5px 5px;
	}
	a.pos_button, .list a.pos_button {
		text-decoration: none;
		color: #FFF;
		display: inline-block;
		padding: 5px 15px 5px 15px;
		background: #0C466D;
		-webkit-border-radius: 10px 10px 10px 10px;
		-moz-border-radius: 10px 10px 10px 10px;
		-khtml-border-radius: 10px 10px 10px 10px;
		border-radius: 10px 10px 10px 10px;
		width: auto;
		border: 0;
	}
	.pos_htabs {
		padding: 0px 0px 0px 10px;
		height: 30px;
		line-height: 16px;
		border-bottom: 1px solid #DDDDDD;
		margin-bottom: 5px;
	}
	.pos_htabs a {
		border-top: 1px solid #DDDDDD;
		border-left: 1px solid #DDDDDD;
		border-right: 1px solid #DDDDDD;
		background: #FFFFFF url('view/image/tab.png') repeat-x;
		padding: 7px 6px 6px 6px;
		float: left;
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
		font-weight: bold;
		text-align: center;
		text-decoration: none;
		color: #000000;
		margin-right: 2px;
		display: none;
	}
	.pos_htabs a.selected {
		padding-bottom: 7px;
		background: #FFFFFF;
	}
	.pos_vtabs {
		width: 140px;
		padding: 2px 0px;
		min-height: 300px;
		float: left;
		display: block;
		border-right: 1px solid #DDDDDD;
	}
	.pos_vtabs a {
		display: none;
	}
	.pos_vtabs a, .pos_vtabs span {
		display: block;
		float: left;
		width: 110px;
		margin-bottom: 5px;
		clear: both;
		border-top: 1px solid #DDDDDD;
		border-left: 1px solid #DDDDDD;
		border-bottom: 1px solid #DDDDDD;
		background: #F7F7F7;
		padding: 6px 14px 7px 15px;
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
		font-weight: bold;
		text-align: right;
		text-decoration: none;
		color: #000000;
	}
	.pos_vtabs a.selected {
		padding-right: 15px;
		background: #FFFFFF;
	}
	.pos_vtabs a img, .pos_vtabs span img {
		position: relative;
		top: 3px;
		cursor: pointer;
	}
	.pos_vtabs-content {
		margin-left: 150px;
	}
	table.form > tbody > tr > td {
		padding: 7px;
		color: #000000;
		border-bottom: 1px dotted #CCCCCC;
	}
	select > option {
		margin: 5px; padding: 5px;
	}
</style>
<div id="oc_content" style="padding: 15px; ">
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	<div id="divWrap" class="top_wrapper" style="height:690px; width:1024px; overflow: hidden;">
		<div style="height: 75px;">
			<div style="float: left; margin-left: 30px; margin-top: 15px; ">
				<img src="view/image/pos/logo.png" alt=""/>
				<!-- add for Quotation begin -->
				&nbsp;
				<a onclick="modeOrder()"><img id="mode_order_img" src="view/image/pos/order_<?php echo $text_work_mode == '0' ? '1' : '0'; ?>.png" alt=""/></a>
				<a onclick="modeReturn()"><img id="mode_return_img" src="view/image/pos/return_<?php echo $text_work_mode == '1' ? '1' : '0'; ?>.png" alt=""/></a>
				<a onclick="modeQuote()"><img id="mode_quote_img" src="view/image/pos/quote_<?php echo $text_work_mode == '2' ? '1' : '0'; ?>.png" alt=""/></a>
				<!-- work_mode: 0 for order, 1 for return, 2 for quote -->
				<input type="hidden" name="work_mode" value="<?php echo $text_work_mode; ?>" />
				<!-- add for Quotation end -->
			</div>
			<!-- add for table management begin -->
			<div style="float: left; margin-left: 10px; margin-top: 30px; ">
				<?php if ($display_order_header == 'block') { ?>
				&nbsp;
				<select name="order_table_id" onchange="changeTable();" style="margin-bottom: 10px;">
					<option value="0"></option>
					<?php
						if (!empty($tables)) {
							foreach ($tables as $table) {
					?>
					<?php if ($order_table_id == $table['table_id']) { ?>
						<option value="<?php echo $table['table_id']; ?>" selected="selected"><?php echo $table['name']; ?></option>
					<?php } else { ?>
						<option value="<?php echo $table['table_id']; ?>"><?php echo $table['name']; ?></option>
					<?php } ?>
					<?php
							}
						}
					?>
				</select>
				<?php } ?>
			</div>
			<!-- add for table management end -->
			<div style="float: right; margin-right: 20px; margin-top: 15px;">
				<div id="header_info" style="float: right; height: 55px; width: 250px; overflow: hidden;">
					<div>
						<span class="impact_large" style="color: #005375;"><?php echo $user; ?></span><span class="impact_large" style="color: #dfc06b;">&nbsp;@&nbsp;T001</span>
					</div>
					<div style="width: 250px; overflow: hidden;">
						<span id="header_week" class="impact_large">Wed</span>
						<span class="impact_small">,</span>
						<span id="header_date" class="impact_large">01</span>
						<span id="header_month" class="impact_small" style="color: #dfc06b;">May</span>
						<span id="header_year" class="impact_large">2013</span>
						<span class="impact_small">&nbsp;</span>
						<span id="header_hour" class="impact_large">12</span>
						<span class="impact_small">:</span>
						<span id="header_minute" class="impact_small" style="color: #005375;">05</span>
						<span id="header_apm" class="impact_small" style="color: #dfc06b;">pm</span>
					</div>
				</div>
				<div style="float: right; margin-right: 30px; margin-top: 2px; ">
					<a id="button_new_order"><img id="img_new_order" src="view/image/pos/new_off.png" title="<?php echo $text_work_mode == '0' ? $button_new_order : $text_new_quote; ?>" alt="<?php echo $text_work_mode == '0' ? $button_new_order : $text_new_quote; ?>"/></a>&nbsp;&nbsp;
					<!-- add for Blank Page begin -->
					<a onclick="getOrderList();"><img id="img_existing_orders" src="view/image/pos/select_off.png" title="<?php echo $text_work_mode == '0' ? $button_existing_order : $text_existing_quotes; ?>" alt="<?php echo $text_work_mode == '0' ? $button_existing_order : $text_existing_quotes; ?>"/></a>&nbsp;&nbsp;&nbsp;
					<!-- add for Blank Page end -->
					<?php if ($display_order_header == 'block') { ?>
					<span id="order_related_buttons">
						<a onclick="completeOrder();"><img id="img_complete_order" src="view/image/pos/complete_off.png" title="<?php echo $text_work_mode == '0' ? $button_complete_order : $text_convert_to_order; ?>" alt="<?php echo $text_work_mode == '0' ? $button_complete_order : $text_convert_to_order; ?>"/></a>&nbsp;&nbsp;&nbsp;
						<!-- update for Blank Page begin -->
						<!-- <a onclick="getOrderList();"><img src="view/image/pos/select_off.png" title="<?php echo $button_existing_order; ?>" alt="<?php echo $button_existing_order; ?>"/></a>&nbsp;&nbsp;&nbsp; -->
						<!-- update for Blank Page end -->
						<!-- change for print invoice begin -->
						<!-- <a onclick="$('#order_list_form').attr('action', '<?php echo $invoice; ?>'); $('#order_list_form').attr('target', '_blank'); $('#order_list_form').submit();"><img src="view/image/pos/print_off.png" title="<?php echo $button_print_invoice; ?>" alt="<?php echo $button_print_invoice; ?>"/></a>&nbsp;&nbsp;&nbsp; -->
						<a onclick="printInvoice();"><img src="view/image/pos/print_off.png" title="<?php echo $button_print_invoice; ?>" alt="<?php echo $button_print_invoice; ?>"/></a>&nbsp;&nbsp;&nbsp;
						<!-- change for print invoice end -->
						<!-- <a onclick="not_implement_yet();"><img src="view/image/pos/cut_off.png" title="<?php echo $button_cut; ?>" alt="<?php echo $button_cut; ?>"/></a>&nbsp;&nbsp;&nbsp; -->
					</span>
					<?php } ?>
					<!-- change for Restrict User begin -->
					<a onclick="toggleFullScreen();"><img id="button_full_screen" src="view/image/pos/header_0_off.png" title="<?php echo $button_full_screen; ?>" alt="<?php echo $button_full_screen; ?>"/></a>
					<!-- change for Restrict User begin -->
				</div>
			</div>
		</div>
		<div id="pos_content" class="pos_content" style="height: 614px;">
			<!-- add for inplace pricing begin -->
			<input type="hidden" name="enable_inplace_pricing" value="<?php echo !empty($enable_inplace_pricing) ? $enable_inplace_pricing : '0'; ?>" />
			<!-- add for inplace pricing end -->
			<!-- add for Maximum Discount begin -->
			<input type="hidden" name="max_discount_fixed" value="<?php echo $max_discount_fixed; ?>" />
			<input type="hidden" name="max_discount_percentage" value="<?php echo $max_discount_percentage; ?>" />
			<!-- add for Maximum Discount end -->
			<!-- add for UPC/SKU/MPN begin -->
			<input type="hidden" name="config_scan_type" value="<?php echo isset($config_scan_type) ? $config_scan_type : ''; ?>">
			<!-- add for UPC/SKU/MPN end -->
			<!-- add for table management begin -->
			<input type="hidden" name="pos_new_table_order_table_id" value="<?php echo (isset($this->session->data['pos_table_new_order'])) ? $this->session->data['pos_table_new_order']['table_id'] : 0; ?>" />
			<input type="hidden" name="pos_new_table_order_order_id" value="<?php echo (isset($this->session->data['pos_table_new_order'])) ? $this->session->data['pos_table_new_order']['order_id'] : 0; ?>" />
			<!-- add for table management end -->
			<div id="order_list" style="display:<?php echo $display_orders; ?>; padding: 10px; height: 640px;">
				<!-- add for table management begin -->
				<!-- <div id="order_list_orders" style="display:<?php echo (isset($enable_table_management) && $enable_table_management) ? 'none' : 'block'; ?>"> -->
				<div id="order_list_orders">
				<!-- add for table management end -->
				<form action="" method="post" enctype="multipart/form-data" id="order_list_form">
					<table class="list">
						<thead>
							<!-- add for Hiding Delete begin -->
							<?php if ($display_delete) { ?>
							<!-- add for Hiding Delete end -->
							<tr>
								<td colspan="8" class="right" style="background-color: #E7EFEF">
									<!-- add for table management begin -->
									<select name="table_list" onchange="filterTable();">
										<option value="0"></option>
										<?php
											if (!empty($tables)) {
												foreach ($tables as $table) {
										?>
										<?php if ($filter_table_id == $table['table_id']) { ?>
											<option value="<?php echo $table['table_id']; ?>" selected="selected"><?php echo $table['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $table['table_id']; ?>"><?php echo $table['name']; ?></option>
										<?php } ?>
										<?php
												}
											}
										?>
									</select>
									&nbsp;&nbsp;
									<!-- add for table management end -->
									<a onclick="deleteOrder(this);" class="pos_button"><?php echo $button_delete; ?></a>
								</td>
							</tr>
							<!-- add for Hiding Delete begin -->
							<?php } ?>
							<!-- add for Hiding Delete end -->
							<tr>
								<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
								<td class="right"><?php if ($sort == 'o.order_id') { ?>
									<a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_id; ?></a>
								<?php } else { ?>
									<a href="<?php echo $sort_order; ?>"><?php echo $column_order_id; ?></a>
								<?php } ?></td>
								<td class="left"><?php if ($sort == 'customer') { ?>
									<a href="<?php echo $sort_customer; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_customer; ?></a>
								<?php } else { ?>
									<a href="<?php echo $sort_customer; ?>"><?php echo $column_customer; ?></a>
								<?php } ?></td>
								<td class="left"><?php if ($sort == 'status') { ?>
									<a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
								<?php } else { ?>
									<a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
								<?php } ?></td>
								<td class="right"><?php if ($sort == 'o.total') { ?>
									<a href="<?php echo $sort_total; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_total; ?></a>
								<?php } else { ?>
									<a href="<?php echo $sort_total; ?>"><?php echo $column_total; ?></a>
								<?php } ?></td>
								<td class="left"><?php if ($sort == 'o.date_added') { ?>
									<a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
								<?php } else { ?>
									<a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
								<?php } ?></td>
								<td class="left"><?php if ($sort == 'o.date_modified') { ?>
									<a href="<?php echo $sort_date_modified; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_modified; ?></a>
								<?php } else { ?>
									<a href="<?php echo $sort_date_modified; ?>"><?php echo $column_date_modified; ?></a>
								<?php } ?></td>
								<td class="right"><?php echo $column_action; ?></td>
							</tr>
						</thead>
						<tbody>
							<tr class="filter">
								<td></td>
								<td align="right"><input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" size="4" style="text-align: right;" /></td>
								<td><input type="text" name="filter_customer" value="<?php echo $filter_customer; ?>" /></td>
								<td>
									<!-- add for Quotation begin -->
									<?php if ($text_work_mode == '2') {?>
									<select name="filter_quote_status_id" style="display:<?php echo $text_work_mode == '2' ? 'block' : 'none'; ?>;">
										<option value="*"></option>
										<?php if ($filter_quote_status_id == '0') { ?>
											<option value="0" selected="selected"><?php echo $text_missing; ?></option>
										<?php } else { ?>
											<option value="0"><?php echo $text_missing; ?></option>
										<?php } ?>
										<?php foreach ($quote_statuses as $quote_status_top) { ?>
											<?php if ($quote_status_top['quote_status_id'] == $filter_quote_status_id) { ?>
												<option value="<?php echo $quote_status_top['quote_status_id']; ?>" selected="selected"><?php echo $quote_status_top['name']; ?></option>
											<?php } else { ?>
												<option value="<?php echo $quote_status_top['quote_status_id']; ?>"><?php echo $quote_status_top['name']; ?></option>
											<?php } ?>
										<?php } ?>
									</select>
									<?php } else { ?>
									<!-- add for Quotation end -->
									<select name="filter_order_status_id">
										<option value="*"></option>
										<?php if ($filter_order_status_id == '0') { ?>
											<option value="0" selected="selected"><?php echo $text_missing; ?></option>
										<?php } else { ?>
											<option value="0"><?php echo $text_missing; ?></option>
										<?php } ?>
										<?php foreach ($order_statuses as $order_status_top) { ?>
											<?php if ($order_status_top['order_status_id'] == $filter_order_status_id) { ?>
												<option value="<?php echo $order_status_top['order_status_id']; ?>" selected="selected"><?php echo $order_status_top['name']; ?></option>
											<?php } else { ?>
												<option value="<?php echo $order_status_top['order_status_id']; ?>"><?php echo $order_status_top['name']; ?></option>
											<?php } ?>
										<?php } ?>
									</select>
									<!-- add for Quotation begin -->
									<?php } ?>
									<!-- add for Quotation end -->
								</td>
								<td align="right"><input type="text" name="filter_total" value="<?php echo $filter_total; ?>" size="4" style="text-align: right;" /></td>
								<td><input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" size="12" class="date" /></td>
								<td><input type="text" name="filter_date_modified" value="<?php echo $filter_date_modified; ?>" size="12" class="date" /></td>
								<td align="right"><a id="button_filter" onclick="filter(this);" class="pos_button"><?php echo $button_filter; ?></a></td>
							</tr>
							<?php if ($orders) { ?>
								<?php foreach ($orders as $order) { ?>
							<tr>
								<td style="text-align: center;"><input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" /></td>
								<td class="right"><?php echo $order['order_id']; ?></td>
								<td class="left"><?php echo $order['customer']; ?></td>
								<td class="left"><?php echo $order['status']; ?></td>
								<td class="right"><?php echo $order['total']; ?></td>
								<td class="left"><?php echo $order['date_added']; ?></td>
								<td class="left"><?php echo $order['date_modified']; ?></td>
								<td class="right"><?php foreach ($order['action'] as $action) { ?>
									[ <a onclick="selectOrder(this, '<?php echo $action['href']; ?>');"><?php echo $action['text']; ?></a> ]
								<?php } ?></td>
							</tr>
								<?php } ?>
							<?php } else { ?>
							<tr>
								<td class="center" colspan="8"><?php echo $text_no_results; ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</form>
				<div class="pagination"><?php echo $pagination; ?></div>
				<!-- add for table management begin -->
				</div>
				<!-- <div id="order_list_table_management" style="display:<?php echo (isset($enable_table_management) && $enable_table_management) ? 'block' : 'none'; ?>;"> -->
				<div id="order_list_table_management" style="display:none; ?>;">
					<img src="<?php echo $img_table_layout; ?>" alt="" id="img_table_layout" />
				</div>
				<!-- add for table management end -->
			</div>
			<?php if ($display_order_content == 'block') { ?>
			<div id="order_info_and_actions" style="display: <?php echo $display_order_content; ?>">
				<div id="order_general" class="pos_content" style="height: 38px;">
					<table class="pos_list" style="height: 100%;">
						<tr>
							<td align="center" style="font-weight: bold" id="mode_id_name"><?php echo $column_order_id; ?>:</td>
							<td align="center" id="order_id" style="font-weight: bold"><?php echo $order_id_text; ?></td>
							<td align="center">
								<span style="font-weight: bold"><?php echo $column_status; ?>:</span>
								<!-- add for Quotation begin -->
								<select name="quote_status_id" id="quote_status" style="display:<?php echo $text_work_mode == '2' ? 'inline' : 'none'; ?>;">
								<?php if (isset($order_id) && $order_id != '' && !empty($quote_statuses)) { foreach ($quote_statuses as $quote_status_next) { ?>
									<?php if ($quote_status_next['name'] == $quote_status) { ?>
									<option value="<?php echo $quote_status_next['quote_status_id']; ?>" selected="selected"><?php echo $quote_status_next['name']; ?></option>
									<?php } else { ?>
									<option value="<?php echo $quote_status_next['quote_status_id']; ?>"><?php echo $quote_status_next['name']; ?></option>
									<?php } ?>
								<?php }} ?>
								</select>
								<!-- add for Quotation end -->
								<!-- add for (update) Quotation begin -->
								<select name="order_status_id" id="order_status" style="display:<?php echo $text_work_mode == '0' ? 'inline' : 'none'; ?>;">
								<!-- <select name="order_status_id" id="order_status"> -->
								<!-- add for (update) Quotation end -->
								<?php if (isset($order_id) && $order_id != '') { foreach ($order_statuses as $order_status_next) { ?>
									<?php if ($order_status_next['name'] == $order_status) { ?>
									<option value="<?php echo $order_status_next['order_status_id']; ?>" selected="selected"><?php echo $order_status_next['name']; ?></option>
									<?php } else { ?>
									<option value="<?php echo $order_status_next['order_status_id']; ?>"><?php echo $order_status_next['name']; ?></option>
									<?php } ?>
								<?php }} ?>
								</select>
							</td>
							<td align="center" style="font-weight: bold"><?php echo $column_customer; ?>:</td>
							<!-- update for Add Customer begin -->
							<!-- <td align="center" id="customer_name_td" style="vertical-align: middle;"><?php if(isset($hasAddress) && $hasAddress == '1') { ?><img id="address_warning" style="vertical-align: middle;" src="view/image/warning.png" alt="<?php echo $text_customer_no_address; ?>" title="<?php echo $text_customer_no_address; ?>" /><?php } ?><a id="general_customer_name" onclick="showCustomerContent()"><?php echo $customer; ?></a><?php if ($customer_id > 0) { ?>&nbsp&nbsp;<img id="detach_customer_img" style="vertical-align: middle;" src="view/image/pos/minus_off.png" onclick="detachCustomer();" /><?php } ?></td> -->
							<td align="center" id="customer_name_td" style="vertical-align: middle;"><?php if(isset($hasAddress) && $hasAddress == '1') { ?><img id="address_warning" style="vertical-align: middle;" src="view/image/warning.png" alt="<?php echo $text_customer_no_address; ?>" title="<?php echo $text_customer_no_address; ?>" /><?php } ?><a id="general_customer_name" onclick="showCustomerContent()"><?php echo $customer; ?></a><?php if ($customer_id > 0) { ?>&nbsp&nbsp;<img id="detach_customer_img" style="vertical-align: middle;" src="view/image/pos/minus_off.png" onclick="detachCustomer();" /><?php } elseif ($order_id != '') { ?>&nbsp&nbsp;<img id="add_customer_img" style="vertical-align: middle;" src="view/image/pos/plus_off.png" onclick="addCustomer();" /><?php } ?></td>
							<!-- update for Add Customer begin -->
							<td align="center" style="font-weight: bold">Added:</td>
							<td align="center"><?php echo $date_added; ?></td>
							<td align="center" style="font-weight: bold">Modified:</td>
							<td align="center"><?php echo $date_modified; ?></td>
						</tr>
					</table>
				</div>
				<div style="margin-top: 15px;">
					<div id="order_product_content" style="display: block; ">
						<div class="pos_content" id="tabs_div" style="overflow-x: hidden; float: right; width: 560px; height: 538px; margin-right: 10px; padding: 5px;">
							<div style="height: 538px; overflow: hidden;">
								<div id="tabs" class="pos_htabs" style="padding: 5px;">
									<a href="#tab_product_browse" id="tab_browse"><?php echo $tab_product_browse; ?></a>
									<a href="#tab_product_search" id="tab_search"><?php echo $tab_product_search; ?></a>
									<!-- add for Quick sale begin -->
									<a href="#tab_product_quick_sale" id="tab_quick_sale"><?php echo $tab_product_quick_sale; ?></a>
									<!-- add for Quick sale end -->
									<a href="#tab_product_details" id="tab_details"><?php echo $tab_product_details; ?></a>
									<!-- add for edit order address begin -->
									<a href="#tab_order_shipping"><?php echo $tab_order_shipping; ?></a>
									<!-- add for edit order address end -->
									<a href="#tab_order_customer" id="tab_customers"><?php echo $tab_order_customer; ?></a>
									<!-- add for Discount begin -->
									<a href="#tab_order_discount"><?php echo $tab_order_discount; ?></a>
									<!-- add for Discount end -->
									<a href="#tab_order_payments"><?php echo $tab_order_payments; ?></a>
								</div>
								<div id="tab_product_search" style="padding: 5px;">
									<div id="product_new" style="overflow: auto; overflow-x: hidden; height: 420px; padding: 0px;">
										<table class="list">
											<thead>
												<tr>
													<td colspan="2" class="left"><?php echo $text_product; ?></td>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td class="left"><?php echo $entry_product; ?></td>
													<td class="left"><input type="text" name="product" value="" /><br />
													<?php echo '('.$text_autocomplete.')'; ?>
													<input type="hidden" name="product_id" value="" /></td>
												</tr>
												<!-- add for Manufacturer Product begin -->
												<tr>
													<td class="left"><?php echo $entry_manufacturer; ?></td>
													<td class="left"><input type="text" name="manufacturer" value="" /><br />
													<?php echo '('.$text_autocomplete.')'; ?>
													<input type="hidden" name="manufacturer_id" value="0" /></td>
												</tr>
												<!-- add for Manufacturer Product end -->
											<!-- add for SKU begin -->
												<tr>
													<td class="left"><?php echo $entry_sku; ?></td>
													<td class="left"><input type="text" name="sku" value="" /></td>
												</tr>
											<!-- add for SKU end -->
											<!-- add for UPC begin -->
												<tr>
													<td class="left"><?php echo $entry_upc; ?></td>
													<td class="left"><input type="text" name="upc" value="" /></td>
												</tr>
											<!-- add for UPC end -->
											<!-- add for MPN begin -->
												<tr>
													<td class="left"><?php echo $entry_mpn; ?></td>
													<td class="left"><input type="text" name="mpn" value="" /></td>
												</tr>
											<!-- add for MPN end -->
											<!-- add for Model begin -->
												<tr>
													<td class="left"><?php echo $entry_model; ?></td>
													<td class="left"><input type="text" name="model" value="" /><br />
													<?php echo '('.$text_autocomplete.')'; ?></td>
												</tr>
											<!-- add for Model end -->
												<tr id="option"></tr>
												<tr id="input_quantity">
													<td class="left"><?php echo $entry_quantity; ?></td>
													<td class="left"><input type="text" name="quantity" value="1" /></td>
												</tr>
												<!-- add for Weight based price begin -->
												<tr id="input_weight" style="display:none;">
													<td class="left"><span id="weight_name"></span><input type="hidden" name="weight_name" value="" /></td>
													<td class="left"><input type="text" name="weight" value="0" /></td>
												</tr>
												<!-- add for Weight based price end -->
												<!-- add for serial no begin -->
												<tr>
													<td class="left"><?php echo $entry_product_sn; ?></td>
													<td class="left"><input type="text" name="product_sn" value="" /><input type="hidden" name="product_sn_id" value="" /></td>
												</tr>
												<!-- add for serial no end -->
											</tbody>
											<tfoot>
												<tr>
													<td class="left">&nbsp;</td>
													<td class="left"><a id="button_product" class="pos_button"><?php echo $button_add_product; ?></a></td>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<!-- add for Quick sale begin -->
								<div id="tab_product_quick_sale" style="padding: 5px;">
									<div id="product_quick_sale" style="overflow: auto; overflow-x: hidden; height: 420px; padding: 0px;">
										<table class="list">
											<thead>
												<tr>
													<td colspan="2" class="left"><?php echo $text_quick_sale; ?></td>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td class="left"><?php echo $entry_quick_sale_name; ?></td>
													<td class="left"><input type="text" name="quick_sale_name" value="" /><br />
													<?php echo '('.$text_autocomplete.')'; ?>
													<input type="hidden" name="quick_sale_product_id" value="0" /></td>
												</tr>
												<tr>
													<td class="left"><?php echo $entry_quick_sale_model; ?></td>
													<td class="left"><input type="text" name="quick_sale_model" value="" /></td>
												</tr>
												<tr>
													<td></td>
													<td class="left"><input type="checkbox" name="quick_sale_shipping" value="0"/><?php echo $text_quick_sale_shipping; ?></td>
												</tr>             
												<tr>
													<td class="left"><?php echo $entry_quick_sale_price; ?></td>
													<td class="left"><input type="text" name="quick_sale_price" value="" /></td>
												</tr>             
												<tr>
													<td class="left"><?php echo $entry_quick_sale_tax; ?></td>
													<td class="left">
														<select name="quick_sale_tax_class_id">
															<option value="0"><?php echo $text_none; ?></option>
															<?php foreach ($tax_classes as $tax_class) { ?>
															<option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
															<?php } ?>
														</select>
													</td>
												</tr>             
												<tr>
													<td></td>
													<td class="left"><input type="checkbox" name="quick_sale_include_tax" value="0" disabled="disabled"/><?php echo $text_quick_sale_include_tax; ?></td>
												</tr>             
												<tr>
													<td class="left"><?php echo $entry_quantity; ?></td>
													<td class="left"><input type="text" name="quick_sale_quantity" value="1" /></td>
												</tr>             
											</tbody>
											<tfoot>
												<tr>
													<td class="left">&nbsp;</td>
													<td class="left"><a id="button_quick_sale" class="pos_button"><?php echo $button_add_product; ?></a></td>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<!-- add for Quick sale end -->
								<!-- add for Browse begin -->
								<div id="tab_product_browse" style="padding: 0px;">
									<div id="product_browse" style="overflow: auto; overflow-x: hidden; height: 480px; margin-left: 5px; margin-right: 5px; margin-top: 0px; padding: 5px; border-top: 1px solid #CCCCCC;  border-left: 1px solid #CCCCCC; border-right: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC;">
										<?php if (!empty($browse_items)) { ?>
										<div id="browse_category_div">
											<table class="list">
												<thead>
													<tr><td class="left" id="browse_category"><a onclick="showCategoryItems('<?php echo $text_top_category_id; ?>')"><?php echo $text_top_category_name; ?></a><!-- &nbsp;<a onclick="toggleCategoryTree()"><img src="view/image/pos/more_down.png" style="vertical-align:bottom;"/></a> --></td></tr>
												</thead>
											</table>
										</div>
										<div id="browse_product_div" style="overflow: auto; overflow-x: hidden;">
											<table class="list" style="border: 0;">
												<?php $col_per_row = 5; $browse_total = sizeof($browse_items); $browse_total_row_no = ($browse_total % $col_per_row) == 0 ? $browse_total / $col_per_row : $browse_total / $col_per_row + 1; ?>
													<?php for ($row = 0; $row < $browse_total_row_no; $row++) { ?>
														<tr>
														<?php for ($col = 0; $col < $col_per_row; $col++) {
																$index = $row*$col_per_row+$col;
																if ($index < $browse_total) {
														?>
															<?php if ($browse_items[$index]['type'] == 'C') { ?>
															<td class="center" width="<?php echo 100/$col_per_row; ?>%" height="80px" style="padding: 3px 1px 0px 1px; border: 0; background-image: url('view/image/pos/category.png'); background-position: center; background-repeat:no-repeat;">
																<a onclick="showCategoryItems('<?php echo $browse_items[$index]['id']; ?>')"><img src="<?php echo $browse_items[$index]['image']; ?>" style="max-width: 50px; max-height: 50px; width: auto; height: auto;"/></a>
															</td>
															<?php } else { ?>
															<td class="center" width="<?php echo 100/$col_per_row; ?>%" style="padding: 3px 1px 0px 1px; border: 0;">
																<a onclick="selectProduct(this, '<?php echo $browse_items[$index]['id']; ?>', '<?php echo $browse_items[$index]['name']; ?>')"><img src="<?php echo $browse_items[$index]['image']; ?>" style="max-width: 75px; max-height: 75px; width: auto; height: auto;"/></a>
																<input type="hidden" value="<?php echo $browse_items[$index]['hasOptions']; ?>" />
															</td>
															<?php } ?>
														<?php } else { ?>
															<td class="center" width="<?php echo 100/$col_per_row; ?>%" style="padding: 3px 1px 0px 1px; border: 0;"></td>
														<?php }}?>
														</tr>
														<tr>
														<?php for ($col = 0; $col < $col_per_row; $col++) {
																$index = $row*$col_per_row+$col;
																if ($index < $browse_total) {
														?>
															<?php if ($browse_items[$index]['type'] == 'C') { ?>
															<td class="center" width="<?php echo 100/$col_per_row; ?>%" style="padding:0px; vertical-align: top; border: 0;">
																<?php echo $browse_items[$index]['name']; ?>
															</td>
															<?php } else { ?>
															<td class="center" width="<?php echo 100/$col_per_row; ?>%" style="padding:0px; vertical-align: top; border: 0;">
																<?php echo $browse_items[$index]['name']; ?><br />
																<?php echo $browse_items[$index]['price_text']; ?><br />
																(<?php echo $browse_items[$index]['stock_text']; ?>)
															</td>
															<?php } ?>
														<?php } else { ?>
															<td class="center" width="<?php echo 100/$col_per_row; ?>%" style="padding: 0px; border: 0;"></td>
														<?php }}?>
														</tr>
													<?php } ?>
											</table>
										</div>
										<?php } ?>
									</div>
								</div>
								<!-- add for Browse end -->
								<div id="tab_product_details" style="margin-left: 5px;">
									<div id="product_details" style="overflow: auto; overflow-x: hidden; height: 480px; padding: 0px; border: 0px;"></div>
								</div>
								<div id="tab_order_customer" style="margin-left: 5px;">
									<div id="order_customers" style="display: <?php echo !empty($order_id_text) ? 'block' : 'none'?>; overflow: auto; overflow-x: hidden; height: 480px; padding: 0px; border: 0px;">
										<div id="order_customer" style="display:<?php echo $customer_id ? 'none' : 'block'; ?>; margin: 10px;">
										<table class="form">
											<tr>
											  <td class="left"><?php echo $text_store_name; ?></td>
											  <td class="left"><select name="store_id">
												  <option value="0"><?php echo $text_default; ?></option>
												  <?php foreach ($stores as $store) { ?>
												  <?php if ($store['store_id'] == $store_id) { ?>
												  <option value="<?php echo $store['store_id']; ?>" selected="selected"><?php echo $store['name']; ?></option>
												  <?php } else { ?>
												  <option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
												  <?php } ?>
												  <?php } ?>
												</select></td>
											</tr>
											<tr>
											  <td><?php echo $text_customer; ?></td>
											  <td><input type="text" name="customer" value="<?php echo $customer; ?>" />&nbsp;<?php echo '('.$text_autocomplete.')'; ?>
												<input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>" /></td>
											</tr>
											<tr>
											  <td class="left"><?php echo $text_customer_group; ?></td>
											  <td class="left"><select name="customer_group_id" <?php echo ($customer_id ? 'disabled="disabled"' : ''); ?>>
												  <?php foreach ($customer_groups as $customer_group) { ?>
												  <?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
												  <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
												  <?php } else { ?>
												  <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
												  <?php } ?>
												  <?php } ?>
												</select>
												<?php foreach ($customer_groups as $customer_group) { ?>
												<input type="hidden" name="<?php echo $customer_group['customer_group_id']; ?>_company_id_display" value="<?php echo $customer_group['company_id_display']; ?>" />
												<input type="hidden" name="<?php echo $customer_group['customer_group_id']; ?>_tax_id_display" value="<?php echo $customer_group['tax_id_display']; ?>" />
												<?php } ?>
												</td>
											</tr>
											<tr>
											  <td><span class="required">*</span> <?php echo $entry_firstname; ?></td>
											  <td><input type="text" name="firstname" value="<?php echo $firstname; ?>" /></td>
											</tr>
											<tr>
											  <td><span class="required">*</span> <?php echo $entry_lastname; ?></td>
											  <td><input type="text" name="lastname" value="<?php echo $lastname; ?>" /></td>
											</tr>
											<tr>
											  <td><span class="required">*</span> <?php echo $entry_email; ?></td>
											  <td><input type="text" name="email" value="<?php echo $email; ?>" />
											  <!-- add for search by email begin -->
											  &nbsp;<?php echo '('.$text_autocomplete.')'; ?>
											  <!-- add for search by email end -->
											  </td>
											</tr>
											<tr>
											  <td><span class="required">*</span> <?php echo $entry_telephone; ?></td>
											  <td><input type="text" name="telephone" value="<?php echo $telephone; ?>" />
											  <!-- add for search by telephone begin -->
											  &nbsp;<?php echo '('.$text_autocomplete.')'; ?>
											  <!-- add for search by telephone end -->
											  </td>
											</tr>
											<tr>
											  <td><?php echo $entry_fax; ?></td>
											  <td><input type="text" name="fax" value="<?php echo $fax; ?>" /></td>
											</tr>
											<tr>
												<td>&nbsp;</td>
												<td><a id="button_custom_save" class="pos_button"><?php echo $button_save; ?></a>&nbsp;&nbsp;&nbsp;<a id="button_custom_cancel" class="pos_button"><?php echo $button_cancel; ?></a></td>
											</tr>
										</table>
										</div>
										<div id="customer_customer" style="display:<?php echo $customer_id ? 'block' : 'none'; ?>; margin: 10px; position: relative;">
											<!-- customer_customer_id is used for Add Customer purposes -->
											<input type="hidden" name="customer_customer_id" value="-1" />
											<?php $address_row = 1; ?>
											<div id="vtabs" class="pos_vtabs"><a href="#tab_customer"><?php echo $tab_general; ?></a>
												<?php foreach ($customer_addresses as $customer_address) { ?>
													<a href="#tab_address_<?php echo $address_row; ?>" id="address_<?php echo $address_row; ?>"><?php echo $tab_address . ' ' . $address_row; ?>&nbsp;<img src="view/image/delete.png" alt="" onclick="$('#vtabs a:first').trigger('click'); $('#address_<?php echo $address_row; ?>').remove(); $('#tab_address_<?php echo $address_row; ?>').remove(); return false;" /></a>
													<?php $address_row++; ?>
												<?php } ?>
												<span id="address_add"><?php echo $button_add_address; ?>&nbsp;<img src="view/image/add.png" alt="" onclick="addAddress();" /></span>
											</div>
											<div id="customer_buttons" style="clear: both; float: left;">
												<a id="customer_button_save" class="pos_button"><?php echo $button_save; ?></a>&nbsp;
												<a id="customer_button_cancel" class="pos_button"><?php echo $button_cancel; ?></a>
											</div>
											<div id="tab_customer" class="pos_vtabs-content">
												<table class="form" style="tr > td {padding: 5px;}">
													<tr>
														<td><span class="required">*</span> <?php echo $entry_firstname; ?></td>
														<td><input type="text" name="customer_firstname" value="<?php echo $customer_firstname; ?>" /></td>
													</tr>
													<tr>
														<td><span class="required">*</span> <?php echo $entry_lastname; ?></td>
														<td><input type="text" name="customer_lastname" value="<?php echo $customer_lastname; ?>" /></td>
													</tr>
													<tr>
														<td><span class="required">*</span> <?php echo $entry_email; ?></td>
														<td><input type="text" name="customer_email" value="<?php echo $customer_email; ?>" /></td>
													</tr>
													<tr>
														<td><span class="required">*</span> <?php echo $entry_telephone; ?></td>
														<td><input type="text" name="customer_telephone" value="<?php echo $customer_telephone; ?>" /></td>
													</tr>
													<tr>
														<td><?php echo $entry_fax; ?></td>
														<td><input type="text" name="customer_fax" value="<?php echo $customer_fax; ?>" /></td>
													</tr>
													<tr>
														<td><?php echo $entry_password; ?></td>
														<td><input type="password" name="customer_password" value="<?php echo $customer_password; ?>"  /></td>
													</tr>
													<tr>
														<td><?php echo $entry_confirm; ?></td>
														<td><input type="password" name="customer_confirm" value="<?php echo $customer_confirm; ?>" /></td>
													</tr>
													<tr>
														<td><?php echo $entry_newsletter; ?></td>
														<td><select name="customer_newsletter">
															<?php if ($customer_newsletter) { ?>
															<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
															<option value="0"><?php echo $text_disabled; ?></option>
															<?php } else { ?>
															<option value="1"><?php echo $text_enabled; ?></option>
															<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
															<?php } ?>
														</select></td>
													</tr>
													<tr>
														<td><?php echo $entry_customer_group; ?></td>
														<td><select name="customer_customer_group_id">
															<?php foreach ($customer_customer_groups as $customer_customer_group) { ?>
																<?php if ($customer_customer_group['customer_group_id'] == $customer_customer_group_id) { ?>
																<option value="<?php echo $customer_customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_customer_group['name']; ?></option>
																<?php } else { ?>
																<option value="<?php echo $customer_customer_group['customer_group_id']; ?>"><?php echo $customer_customer_group['name']; ?></option>
																<?php } ?>
															<?php } ?>
														</select>
														<?php foreach ($customer_customer_groups as $customer_customer_group) { ?>
															<input type="hidden" name="<?php echo $customer_customer_group['customer_group_id']; ?>_customer_company_id_display" value="<?php echo $customer_customer_group['company_id_display']; ?>" />
															<input type="hidden" name="<?php echo $customer_customer_group['customer_group_id']; ?>_customer_tax_id_display" value="<?php echo $customer_customer_group['tax_id_display']; ?>" />
														<?php } ?>
														</td>
													</tr>
													<tr>
														<td><?php echo $entry_status; ?></td>
														<td><select name="customer_status">
														<?php if ($customer_status) { ?>
														<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
														<option value="0"><?php echo $text_disabled; ?></option>
														<?php } else { ?>
														<option value="1"><?php echo $text_enabled; ?></option>
														<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
														<?php } ?>
														</select></td>
													</tr>
												</table>
											</div>
											<?php $address_row = 1; ?>
											<?php foreach ($customer_addresses as $customer_address) { ?>
												<div id="tab_address_<?php echo $address_row; ?>" class="pos_vtabs-content">
													<input type="hidden" name="customer_address[<?php echo $address_row; ?>][address_id]" value="<?php echo $customer_address['address_id']; ?>" />
													<table class="form">
														<tr>
															<td><span class="required">*</span> <?php echo $entry_firstname; ?></td>
															<td><input type="text" name="customer_address[<?php echo $address_row; ?>][firstname]" value="<?php echo $customer_address['firstname']; ?>" /></td>
														</tr>
														<tr>
															<td><span class="required">*</span> <?php echo $entry_lastname; ?></td>
															<td><input type="text" name="customer_address[<?php echo $address_row; ?>][lastname]" value="<?php echo $customer_address['lastname']; ?>" /></td>
														</tr>
														<tr>
															<td><?php echo $entry_company; ?></td>
															<td><input type="text" name="customer_address[<?php echo $address_row; ?>][company]" value="<?php echo $customer_address['company']; ?>" /></td>
														</tr>
														<tr class="customer-company-id-display">
															<td><?php echo $entry_company_id; ?></td>
															<td><input type="text" name="customer_address[<?php echo $address_row; ?>][company_id]" value="<?php echo $customer_address['company_id']; ?>" /></td>
														</tr>
														<tr class="customer-tax-id-display">
															<td><?php echo $entry_tax_id; ?></td>
															<td><input type="text" name="customer_address[<?php echo $address_row; ?>][tax_id]" value="<?php echo $customer_address['tax_id']; ?>" /></td>
														</tr>
														<tr>
															<td><span class="required">*</span> <?php echo $entry_address_1; ?></td>
															<td><input type="text" name="customer_address[<?php echo $address_row; ?>][address_1]" value="<?php echo $customer_address['address_1']; ?>" /></td>
														</tr>
														<tr>
															<td><?php echo $entry_address_2; ?></td>
															<td><input type="text" name="customer_address[<?php echo $address_row; ?>][address_2]" value="<?php echo $customer_address['address_2']; ?>" /></td>
														</tr>
														<tr>
															<td><span class="required">*</span> <?php echo $entry_city; ?></td>
															<td><input type="text" name="customer_address[<?php echo $address_row; ?>][city]" value="<?php echo $customer_address['city']; ?>" /></td>
														</tr>
														<tr>
															<td><span id="postcode-required<?php echo $address_row; ?>" class="required">*</span> <?php echo $entry_postcode; ?></td>
															<td><input type="text" name="customer_address[<?php echo $address_row; ?>][postcode]" value="<?php echo $customer_address['postcode']; ?>" /></td>
														</tr>
														<tr>
															<td><span class="required">*</span> <?php echo $entry_country; ?></td>
															<td><select name="customer_address[<?php echo $address_row; ?>][country_id]" onchange="country(this, '<?php echo $address_row; ?>', '<?php echo $customer_address['zone_id']; ?>');">
																<option value=""><?php echo $text_select; ?></option>
																<?php foreach ($customer_countries as $customer_country) { ?>
																	<?php if ($customer_country['country_id'] == $customer_address['country_id']) { ?>
																	<option value="<?php echo $customer_country['country_id']; ?>" selected="selected"><?php echo $customer_country['name']; ?></option>
																	<?php } else { ?>
																	<option value="<?php echo $customer_country['country_id']; ?>"><?php echo $customer_country['name']; ?></option>
																	<?php } ?>
																<?php } ?>
															</select></td>
														</tr>
														<tr>
															<td><span class="required">*</span> <?php echo $entry_zone; ?></td>
															<td><select name="customer_address[<?php echo $address_row; ?>][zone_id]">
															</select></td>
														</tr>
														<tr>
															<td><?php echo $entry_default; ?></td>
															<td><?php if (($customer_address['address_id'] == $customer_address_id) || !$customer_addresses) { ?>
																<input type="radio" name="customer_address[<?php echo $address_row; ?>][default]" value="<?php echo $address_row; ?>" checked="checked" /></td>
																<?php } else { ?>
																<input type="radio" name="customer_address[<?php echo $address_row; ?>][default]" value="<?php echo $address_row; ?>" />
															</td>
															<?php } ?>
														</tr>
													</table>
												</div>
												<?php $address_row++; ?>
											<?php } ?>
										</div>
									</div>
								</div>
								<div id="tab_order_discount" style="margin-left: 5px;">
									<div id="order_discount" style="display: <?php echo !empty($order_id_text) ? 'block' : 'none'?>; overflow: auto; overflow-x: hidden; height: 480px; padding: 0px; border: 0px; max-width: 380px; margin: 0 auto;">
										<!-- add for Discount begin -->
										<p><?php echo $text_discount_message; ?></p>
										<table class="form">
											<tr>
												<td width="5%"  style="text-align: center; border-bottom: 1px solid #000000;"><input type="radio" name="discount_type" value="amount" <?php if (isset($discount_type) && $discount_type == 'amount') { ?> checked="checked" <?php } ?>/></td>
												<td width="95%" class="left" colspan="2" style="color: #FF802B; font-size: 12px; font-weight: bold; border-bottom: 1px solid #000000;"><?php echo $text_discount_type_amount; ?></td>
											</tr>
											<tr>
												<td width="5%"></td>
												<td width="45%" class="left" id="discount_amount_text"><?php echo $text_discount.'('.(isset($currency_symbol) ? $currency_symbol : '').')'; ?></td>
												<td width="50%" class="left"><input type="text" name="discount_amount_value" value="<?php echo (isset($discount_value) && isset($discount_type) && $discount_type == 'amount') ? $discount_value : '0'; ?>" style="width: 95%;"/></td>
											</tr>
											<tr>
												<td width="5%" style="text-align: center; border-bottom: 1px solid #000000;"><input type="radio" name="discount_type" value="percentage" <?php if (isset($discount_type) && $discount_type == 'percentage') { ?> checked="checked" <?php } ?>/></td>
												<td width="95%" class="left" colspan="2" style="color: #FF802B; font-size: 12px; font-weight: bold; border-bottom: 1px solid #000000;"><?php echo $text_discount_type_percentage; ?></td>
											</tr>
											<tr>
												<td width="5%"></td>
												<td width="45%" class="left" id="discount_percentage_text"><?php echo $text_discount . '(%):'; ?></td>
												<td width="50%" class="left"><input type="text" name="discount_percentage_value" value="<?php echo (isset($discount_value) && isset($discount_type) && $discount_type == 'percentage') ? $discount_value : '0'; ?>" style="width: 95%;"/></td>
											</tr>
											<tr>
												<td width="5%"></td>
												<td width="45%" style="text-align: left;"><input type="radio" name="discount_total_type" value="subtotal" <?php if (isset($discount_type) && isset($discount_total_type) && $discount_type == 'percentage' && $discount_total_type == 'subtotal') { ?> checked="checked" <?php } ?>/><span id="discount_subtotal_name"><?php echo $text_discount_subtotal.'('.(isset($currency_symbol) ? $currency_symbol : '').')'; ?></span></td>
												<td width="50%" class="left" id="discount_subtotal_text"><?php echo isset($total_subtotal_text) ? $total_subtotal_text : '0'; ?></td>
												<input type="hidden" name="discount_subtotal_value" value="<?php echo isset($total_subtotal_value) ? $total_subtotal_value : '0'; ?>" />
											</tr>
											<tr>
												<td width="5%"></td>
												<td width="45%" style="text-align: left;"><input type="radio" name="discount_total_type" value="total" <?php if (isset($discount_type) && isset($discount_total_type) && $discount_type == 'percentage' && $discount_total_type == 'total') { ?> checked="checked" <?php } ?>/><span id="discount_total_name"><?php echo $text_discount_total.'('.(isset($currency_symbol) ? $currency_symbol : '').')'; ?></span></td>
												<td width="50%" class="left" id="discount_total_text"><?php echo isset($total_total_text) ? $total_total_text : '0'; ?></td>
												<input type="hidden" name="discount_total_value" value="<?php echo isset($total_total_value) ? $total_total_value : '0'; ?>" />
											</tr>
											<tr>
												<td width="100%" class="left" colspan="3" style="color: #FF802B; font-size: 12px; font-weight: bold; border-bottom: 1px solid #000000;"><?php echo $text_discounted_title; ?></td>
											</tr>
											<tr>
												<td width="5%"></td>
												<td width="45%" class="left" id="discounted_text"><?php echo $text_discounted_title.'('.(isset($currency_symbol) ? $currency_symbol : '').')'; ?></td>
												<td width="50%" class="left"><span id="discounted_value" style="color: #FF802B; font: bold 20px Arial, Helvetica, sans-serif;"></span></td>
											</tr>
											<tr>
												<td align="right" colspan="3">
													<a id="button_discount_apply" class="pos_button"><?php echo $button_discount; ?></a>
												</td>
											</tr>
										</table>
										<!-- add for Discount end -->
									</div>
								</div>
								<!-- add for edit order address begin -->
								<div id="tab_order_shipping" style="margin-left: 5px;">
									<div id="order_shipping" style="display: <?php echo !empty($order_id_text) ? 'block' : 'none'?>; overflow: auto; overflow-x: hidden; height: 480px; padding: 0px; border: 0px;">
										<table id="order_addresses" class="form">
											<tr>
												<td class="left" style="color: #FF802B; font-size: 12px; font-weight: bold; "><?php echo $text_order_shipping_address; ?></td>
												<td class="left">
													<?php echo $entry_shipping_method; ?>
													<select name="shipping">
													</select>
													<input type="hidden" name="shipping_method" value="<?php echo $shipping_method; ?>" />
													<input type="hidden" name="shipping_code" value="<?php echo $shipping_code; ?>" />
												</td>
											</tr>
											<tr>
												<td class="left"><?php echo $entry_order_address; ?></td>
												<td class="left">
													<select name="shipping_address">
														<option value="0" selected="selected"><?php echo $text_none; ?></option>
														<?php foreach ($customer_addresses as $customer_address) { ?>
															<option value="<?php echo $customer_address['address_id']; ?>"><?php echo $customer_address['firstname'] . ' ' . $customer_address['lastname'] . ', ' . $customer_address['address_1'] . ', ' . $customer_address['city'] . ', ' . $customer_address['country']; ?></option>
														<?php } ?>
													</select>
												</td>
											</tr>
											<tr>
												<td class="left"><span class="required">*</span> <?php echo $entry_firstname; ?></td>
												<td class="left"><input type="text" name="shipping_firstname" value="<?php echo $shipping_firstname; ?>" /></td>
											</tr>
											<tr>
												<td class="left"><span class="required">*</span> <?php echo $entry_lastname; ?></td>
												<td class="left"><input type="text" name="shipping_lastname" value="<?php echo $shipping_lastname; ?>" /></td>
											</tr>
											<tr>
												<td class="left"><?php echo $entry_company; ?></td>
												<td class="left"><input type="text" name="shipping_company" value="<?php echo $shipping_company; ?>" /></td>
											</tr>
											<tr>
												<td class="left"><span class="required">*</span> <?php echo $entry_address_1; ?></td>
												<td class="left"><input type="text" name="shipping_address_1" value="<?php echo $shipping_address_1; ?>" /></td>
											</tr>
											<tr>
												<td class="left"><?php echo $entry_address_2; ?></td>
												<td class="left"><input type="text" name="shipping_address_2" value="<?php echo $shipping_address_2; ?>" /></td>
											</tr>
											<tr>
												<td class="left"><span class="required">*</span> <?php echo $entry_city; ?></td>
												<td class="left"><input type="text" name="shipping_city" value="<?php echo $shipping_city; ?>" /></td>
											</tr>
											<tr>
												<td class="left"><span id="shipping-postcode-required" class="required">*</span> <?php echo $entry_postcode; ?></td>
												<td class="left"><input type="text" name="shipping_postcode" value="<?php echo $shipping_postcode; ?>" /></td>
											</tr>
											<tr>
												<td class="left"><span class="required">*</span> <?php echo $entry_country; ?></td>
												<td class="left">
													<select name="shipping_[country_id]" onchange="order_country(this, 'shipping', '<?php echo $shipping_zone_id; ?>');">
														<option value=""><?php echo $text_select; ?></option>
														<?php foreach ($customer_countries as $customer_country) {
																if ($customer_country['country_id'] == $payment_country_id) { ?>
																	<option value="<?php echo $customer_country['country_id']; ?>" selected="selected"><?php echo $customer_country['name']; ?></option>
														<?php } else { ?>
																	<option value="<?php echo $customer_country['country_id']; ?>"><?php echo $customer_country['name']; ?></option>
														<?php } } ?>
													</select>
												</td>
											</tr>
											<tr>
												<td class="left"><span class="required">*</span> <?php echo $entry_zone; ?></td>
												<td class="left"><select name="shipping_zone_id"></select></td>
											</tr>
											<tr>
												<td class="left" colspan="2" style="color: #FF802B; font-size: 12px; font-weight: bold; "><?php echo $text_order_payment_address; ?></td>
											</tr>
											<tr>
												<td class="left"><?php echo $entry_order_address; ?></td>
												<td class="left">
													<select name="payment_address">
														<option value="0" selected="selected"><?php echo $text_none; ?></option>
														<?php foreach ($customer_addresses as $customer_address) { ?>
															<option value="<?php echo $customer_address['address_id']; ?>"><?php echo $customer_address['firstname'] . ' ' . $customer_address['lastname'] . ', ' . $customer_address['address_1'] . ', ' . $customer_address['city'] . ', ' . $customer_address['country']; ?></option>
														<?php } ?>
													</select>
												</td>
											</tr>
											<tr>
												<td class="left"><span class="required">*</span> <?php echo $entry_firstname; ?></td>
												<td class="left"><input type="text" name="payment_firstname" value="<?php echo $payment_firstname; ?>" /></td>
											</tr>
											<tr>
												<td class="left"><span class="required">*</span> <?php echo $entry_lastname; ?></td>
												<td class="left"><input type="text" name="payment_lastname" value="<?php echo $payment_lastname; ?>" /></td>
											</tr>
											<tr>
												<td class="left"><?php echo $entry_company; ?></td>
												<td class="left"><input type="text" name="payment_company" value="<?php echo $payment_company; ?>" /></td>
											</tr>
											<tr class="order-company-id-display">
												<td class="left"><?php echo $entry_company_id; ?></td>
												<td class="left"><input type="text" name="payment_company_id" value="<?php echo $payment_company_id; ?>" /></td>
											</tr>
											<tr class="order-tax-id-display">
												<td class="left"><?php echo $entry_tax_id; ?></td>
												<td class="left"><input type="text" name="payment_tax_id" value="<?php echo $payment_tax_id; ?>" /></td>
											</tr>
											<tr>
												<td class="left"><span class="required">*</span> <?php echo $entry_address_1; ?></td>
												<td class="left"><input type="text" name="payment_address_1" value="<?php echo $payment_address_1; ?>" /></td>
											</tr>
											<tr>
												<td class="left"><?php echo $entry_address_2; ?></td>
												<td class="left"><input type="text" name="payment_address_2" value="<?php echo $payment_address_2; ?>" /></td>
											</tr>
											<tr>
												<td class="left"><span class="required">*</span> <?php echo $entry_city; ?></td>
												<td class="left"><input type="text" name="payment_city" value="<?php echo $payment_city; ?>" /></td>
											</tr>
											<tr>
												<td class="left"><span id="payment-postcode-required" class="required">*</span> <?php echo $entry_postcode; ?></td>
												<td class="left"><input type="text" name="payment_postcode" value="<?php echo $payment_postcode; ?>" /></td>
											</tr>
											<tr>
												<td class="left"><span class="required">*</span> <?php echo $entry_country; ?></td>
												<td class="left">
													<select name="payment_[country_id]" onchange="order_country(this, 'payment', '<?php echo $payment_zone_id; ?>');">
														<option value=""><?php echo $text_select; ?></option>
														<?php foreach ($customer_countries as $customer_country) {
																if ($customer_country['country_id'] == $payment_country_id) { ?>
																	<option value="<?php echo $customer_country['country_id']; ?>" selected="selected"><?php echo $customer_country['name']; ?></option>
														<?php } else { ?>
																	<option value="<?php echo $customer_country['country_id']; ?>"><?php echo $customer_country['name']; ?></option>
														<?php } } ?>
													</select>
												</td>
											</tr>
											<tr>
												<td class="left"><span class="required">*</span> <?php echo $entry_zone; ?></td>
												<td class="left"><select name="payment_zone_id"></select></td>
											</tr>
											<tr>
												<td class="left" colspan="2">
													<a id="button_order_address_save" class="pos_button"><?php echo $button_save; ?></a>&nbsp;&nbsp;&nbsp;
													<a id="button_order_address_cancel" class="pos_button"><?php echo $button_cancel; ?></a>
												</td>
											</tr>
										</table>
									</div>
								</div>
								<!-- add for edit order address end -->
								<div id="tab_order_payments" style="margin-left: 5px;">
									<div id="order_payments" style="overflow: auto; overflow-x: hidden; height: 480px; padding: 0px; border: 0px;">
										<table class="list">
											<thead>
												<tr>
													<td class="left"><?php echo $column_payment_type; ?></td>
													<td class="left"><?php echo $column_payment_amount; ?></td>
													<td class="left"><span id="payment_note_text"><?php echo $column_payment_note; ?></span></td>
													<td class="right"><?php echo $column_payment_action; ?></td>
												</tr>
											</thead>
											<tbody id="payment_list">
												<tr class='filter' id="button_add_payment_tr">
													<td class="left" width="30%">
														<select name="payment_type" id="payment_type" style="width: 98%;">
															<?php foreach($payment_types as $payment_type => $payment_name) { ?>
															<?php if ($payment_type == 'cash') { ?>
															<option value="<?php echo $payment_type ?>" selected="selected"><?php echo $payment_name ?></option>
															<?php } else { ?>
															<option value="<?php echo $payment_type ?>"><?php echo $payment_name ?></option>
															<?php }} ?>
														</select>
													</td>
													<?php
														$float_amount_due = $payment_due_amount;
														$firstChar = substr($payment_due_amount, 0, 1);
														if (strcmp($firstChar, '0') < 0 || strcmp($firstChar, '9') > 0) {
															$float_amount_due = substr($payment_due_amount, 1);
														}
													?>
													<td class="left" width="25%"><input type="text" name="tendered_amount" id="tendered_amount" style="width: 95%;" value="<?php echo round(floatval($float_amount_due), 2); ?>"/></td>
													<td class="left" width="35%"><input type="text" name="payment_note" id="payment_note" value="" style="width: 95%;"/></td>
													<td align="center" width="10%"><a id="button_add_payment" onclick="addPayment();"><img src="view/image/pos/plus_off.png"/></a></td>
												</tr>
												<?php if (isset($order_payments)) {foreach ($order_payments as $order_payment) { ?>
												<tr id="<?php echo $order_payment['order_payment_id']; ?>">
													<td class="left" width="30%"><?php echo $order_payment['payment_type']; ?></td>
													<td class="left" width="25%"><?php echo $order_payment['tendered_amount']; ?></td>
													<td class="left" width="35%" style="-ms-word-break: break-all; word-break: break-all; word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto;"><?php echo $order_payment['payment_note']; ?></td>
													<td align="center" width="10%"><a onclick="deletePayment('<?php echo $order_payment['order_payment_id']; ?>');"><img src="view/image/pos/delete_off.png" width="22" height="22"/></a></td>
												</tr>
												<?php }} ?>
											</tbody>
										</table>
										<!-- add for Cash type begin -->
										<?php if (!empty($cash_types)) { foreach ($cash_types as $cash_type_key=>$cash_type_values) { ?>
										<table class="list" id="cash_type_list_<?php echo $cash_type_key; ?>" style="border: 0;">
											<?php $col_per_row = 3; $cash_type_total = sizeof($cash_type_values); $cash_total_row_no = ($cash_type_total % $col_per_row) == 0 ? $cash_type_total / $col_per_row : $cash_type_total / $col_per_row + 1; ?>
												<?php for ($row = 0; $row < $cash_total_row_no; $row++) { ?>
													<tr>
													<?php for ($col = 0; $col < $col_per_row; $col++) {
															$index = $row*$col_per_row+$col;
															if ($index < $cash_type_total) {
													?>
														<td class="center" width="<?php echo 100/$col_per_row; ?>%" style="padding: 3px 1px 0px 1px; border: 0;">
															<a onclick="selectCashType('<?php echo $cash_type_values[$index]['value']; ?>')"><img src="<?php echo $cash_type_values[$index]['image']; ?>" style="max-width: 160px; max-height: 80px; width: auto; height: auto;"/></a>
														</td>
													<?php } else { ?>
														<td class="center" width="<?php echo 100/$col_per_row; ?>%" style="padding: 3px 1px 0px 1px; border: 0;"></td>
													<?php }}?>
													</tr>
												<?php } ?>
										</table>
										<?php }} ?>
										<!-- add for Cash type end -->
									</div>
								</div>
							</div>
						</div>
						<div class="pos_content" style="margin-left: 10px; margin-right: 590px; height: 348px; padding: 5px;">
							<div id="order_message" style="margin: 1px; height: 30px;"></div>
							<div class="pos_wrapper" id="order_product_list_and_actions" style="overflow: hidden; margin: 1px; margin-top: 25px;">
								<div id="order_product_list_sidebar" style="float: right; width: 45px; margin-top: 16px; margin-right: 5px; overflow: auto;">
									<div style="margin-left: 9px;"><img id="button_up" src="view/image/pos/up_off.png" /></div>
									<div style="margin-top:8px; margin-left: 9px;"><img id="button_down" src="view/image/pos/down_off.png" /></div>
									<div style="margin-top:8px; margin-left: 9px;"><img id="button_delete" src="view/image/pos/delete_off.png" /></div>
									<div style="margin-top:8px; margin-left: 9px;"><img id="button_equal" src="view/image/pos/equal_off.png" /></div>
									<!-- add for inplace pricing begin -->
									<div style="margin-top:8px; margin-left: 9px;"><img id="button_price" src="view/image/pos/price_off.png" /></div>
									<!-- add for inplace pricing end -->
								</div>
								<div id="order_product_list_content" style="overflow: auto; height: 210px; padding: 5px; border-right: 1px solid #CCCCCC; ">
									<table class="list" id="order_product_list">
										<thead>
											<tr>
												<td width="1" style="text-align: center;"></td>
												<td class="left"><?php echo $column_product; ?></td>
												<!-- <td class="left"><?php echo $column_model; ?></td> -->
												<td class="right"><?php echo $column_qty; ?></td>
												<td class="right"><?php echo $column_price; ?></td>
												<td class="right"><?php echo $column_total; ?></td>
											</tr>
										</thead>
										<?php $product_row = 0; ?>
										<?php $option_row = 0; ?>
										<tbody id="product">
										<?php foreach ($products as $product) { ?>
											<tr id="product-row<?php echo $product_row; ?>">
												<td style="text-align: center;">
													<input type="radio" name="order_product_id" value="<?php echo $product['order_product_id']; ?>" />
													<input type="hidden" name="order_product[<?php echo $product_row; ?>][order_product_id]" value="<?php echo $product['order_product_id']; ?>" />
													<input type="hidden" name="order_product[<?php echo $product_row; ?>][product_id]" value="<?php echo $product['product_id']; ?>" /></td>
												<td class="left"><?php echo $product['name']; ?>
													<?php foreach ($product['option'] as $option) { ?>
													<br />
													&nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
													<input type="hidden" name="order_product[<?php echo $product_row; ?>][order_option][<?php echo $option_row; ?>][product_option_id]" value="<?php echo $option['product_option_id']; ?>" />
													<input type="hidden" name="order_product[<?php echo $product_row; ?>][order_option][<?php echo $option_row; ?>][product_option_value_id]" value="<?php echo $option['product_option_value_id']; ?>" />
													<input type="hidden" name="order_product[<?php echo $product_row; ?>][order_option][<?php echo $option_row; ?>][value]" value="<?php echo $option['value']; ?>" />
													<input type="hidden" name="order_product[<?php echo $product_row; ?>][order_option][<?php echo $option_row; ?>][type]" value="<?php echo $option['type']; ?>" />
													<?php $option_row++; ?>
													<?php } ?>
													<!-- add for serial no begin -->
													<?php if (!empty($product['sns'])) { foreach ($product['sns'] as $product_sn) { ?>
													<br />
													&nbsp;<small> - SN: <?php echo $product_sn['sn']; ?></small>
													<?php }}?>
													<!-- add for serial no end -->
												</td>
												<!-- <td class="left"><?php echo $product['model']; ?></td> -->
												<td class="right"><?php echo $product['quantity']; ?></td>
												<input type="hidden" name="order_product[<?php echo $product_row; ?>][quantity]" value="<?php echo $product['quantity']; ?>" />
												<td class="right" id="price_text-<?php echo $product_row; ?>"><?php echo $product['price_text']; ?></td>
												<input type="hidden" name="order_product[<?php echo $product_row; ?>][price]" value="<?php echo $product['price']; ?>" />
												<td class="right"><?php echo $product['total_text']; ?></td>
											</tr>
											<?php $product_row++; ?>
										<?php } ?>
											<input type="hidden" name="radio_selected_index" id="radio_selected_index" value="-1" />
											<input type="hidden" name="currency_code" value="<?php echo isset($currency_code) ? $currency_code : ''; ?>" />
											<input type="hidden" name="currency_value" value="<?php echo isset($currency_value) ? $currency_value : ''; ?>" />
											<input type="hidden" name="shipping_country_id" value="<?php echo isset($shipping_country_id) ? $shipping_country_id : ''; ?>" />
											<input type="hidden" name="shipping_zone_id" value="<?php echo isset($shipping_zone_id) ? $shipping_zone_id : ''; ?>" />
											<input type="hidden" name="payment_country_id" value="<?php echo isset($payment_country_id) ? $payment_country_id : ''; ?>" />
											<input type="hidden" name="payment_zone_id" value="<?php echo isset($payment_zone_id) ? $payment_zone_id : ''; ?>" />
											<tr id="new_product_row">
												<td style="text-align: center;"><input type="radio" name="order_product_id" value="-1" /></td><td colspan="4" class="center"><?php echo $text_add_product_prompt; ?></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div id="order_total_and_cart_info" style="margin-top: 5px; margin-left: 1px; margin-right: 1px;">
								<div style="position: relative; height: 70px; ">
									<table class="pos_form_table">
										<tbody>
											<tr>
												<td class="center" width="25%"><?php echo $text_items_in_cart; ?>:</td>
												<td class="center" width="25%" id="items_in_cart"><?php echo $items_in_cart; ?></td>
												<td class="center" width="25%"><?php echo $text_amount_due; ?>:</td>
												<td class="center" width="25%" id="payment_due_amount" style="<?php echo $payment_due_amount==0 ? 'color:green' : 'color:red' ?>"><?php echo $payment_due_amount_text; ?></td>
											</tr>
											<?php
												foreach ($totals as $total_order) {
													if ($total_order['code'] == 'total') {
														break;
													}
												}
											?>
											<tr id="total_tr">
												<td class="center" width="25%"><span style="font: bold 16px Arial, Helvetica, sans-serif;"><?php echo isset($total_order) ? $total_order['title'] : ''; ?>:</span></td>
												<td class="center" width="25%" id="payment_total" style="background-image: url('view/image/pos/more_down.png'); background-position: right bottom; background-repeat:no-repeat;"><span style="font: bold 16px Arial, Helvetica, sans-serif;"><?php echo isset($total_order) ? $total_order['text'] : ''; ?></span></td>
												<td class="center" width="25%"><span style="font: bold 16px Arial, Helvetica, sans-serif;"><?php echo $text_change; ?>:</span></td>
												<td class="center" width="25%" id="payment_change"><span style="font: bold 16px Arial, Helvetica, sans-serif;"><?php echo $payment_change_text; ?></span></td>
											</tr>
										</tbody>
									</table>
									<div id="totals_details" style="position: absolute; top: 63px; left: 0px; width: 205px; height: 180px; overfolow: auto; overflow-x: hidden; display: none;">
										<table class="list">
											<tbody id="total">
											<?php 
											$total_row = 0;
											foreach ($totals as $total_order) { 
												$boldStylePrefix = '';
												$boldStyleSuffix = '';
												if ($total_order['code'] == 'total')  {
													$boldStylePrefix = '<span style="font: bold 16px Arial, Helvetica, sans-serif;">';
													$boldStyleSuffix = '</span>';
												}
											?>
													<tr id="total-row<?php echo $total_row; ?>">
														<td class="center" width="50%"><?php echo $boldStylePrefix; ?><?php echo $total_order['title']; ?>:<?php echo $boldStyleSuffix; ?></td>
														<td class="center" width="50%"><?php echo $boldStylePrefix; ?><?php echo $total_order['text']; ?><?php echo $boldStyleSuffix; ?></td>
													</tr>
													<?php $total_row++; ?>
											<?php 
											}
											?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="pos_content" id="keyboard_wrapper" style="overflow: hidden; width: 410px; margin-top: 10px; margin-left: 10px; margin-right: 590px; height: 168px; padding: 5px;">
							<?php
								$pos_keys = array('1', '2', '3', 'clear', 'Del', 'q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', '4', '5', '6', '0', 'accept', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', ';', '7', '8', '9', '.', 'keyboard', 'z', 'x', 'c', 'v', 'b', 'n', 'm', ',', '.', '/');
								$row = 3; $col = 15;
							?>
							<table id="keyboard_keys" style="padding-left: 1px; border-spacing: 9px 2px; table-layout: fixed; width: 300px;">
							<?php
								for ($col_i = 0; $col_i < $col; $col_i++) {
							?>
								<col style="width: 73px;"/>
							<?php
								}
								for ($row_i = 0; $row_i < $row; $row_i++) {
							?>
								<tr>
							<?php
									for ($col_i = 0; $col_i < $col; $col_i++) {
										$key_index = $col_i + $row_i * $col;
										if ($key_index < sizeof($pos_keys)) {
											$key_value = $pos_keys[$key_index];
											$key_name = $key_value;
											if ($key_name == 'clear') {
												$key_name = 'C';
											} elseif ($key_name == 'accept') {
												$key_name = '<img src="view/image/pos/accept.png" />';
											} elseif ($key_name == 'keyboard') {
												$key_name = '<img src="view/image/pos/keyboard.png" />';
											}
							?>
									<td id="keyboard_td_<?php echo $key_index; ?>" style="overflow: hidden; text-align: center; vertical-align: middle; cursor: pointer; padding-top: 10px; padding-bottom: 10px; border: 1px solid #CCCCCC; ">
										<span class="impact_large" style="font-size: 26px;"><?php echo $key_name; ?></span>
										<input type="hidden" value="<?php echo $key_value; ?>" />
									</td>
							<?php
										}
									}
							?>
								</tr>
							<?php
								}
							?>
							</table>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
		<!-- add for jzebra printer begin -->
		<?php if (isset($enable_till_control) && $enable_till_control) { ?>
		<div id="jzebra_div" style="visibility: hidden; height: 0px;">
			<applet name="jzebra" code="qz.PrintApplet.class" archive="view/template/pos/print/qz-print.jar" width="50px" height="50px">
				<param name="jnlp_href" value="view/template/pos/print/qz-print_jnlp.jnlp">
				<param name="printer" value="opencartPOS">
				<param name="cache_option" value="plugin">
				<param name="disable_logging" value="false">
				<param name="initial_focus" value="false">
			</applet>
		</div>
		<?php } ?>
		<!-- add for jzebra printer end -->
	</div>
	<div id="hidden_div" style="display:none;"></div>
	<!-- add for Print begin -->
	<div id="pos_print" title="<?php echo $print_wait_title; ?>">
		<p><img src="view/image/loading.gif" alt=""/>&nbsp;<span id="print_message"><?php echo $print_wait_message; ?></span></p>
	</div>
	<div style="display: none;" id="print_canvas"></div>
	<div id="print_hidden" style="display: none;"></div>
	<iframe id="print_iframe" src="about:blank" style="display:none; width: 0; height: 0;"></iframe>
	<!-- add for Print end -->
</div>
<script type="text/javascript" src="view/javascript/jquery/ui/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">
var enterFns = {};
var keyboardClick = false;
var lastInput;
var preTabId;
// add for table management begin
var jcrop_api;
var selectIndex = -1;
var tables = new Array();
// add for table management end
// add for browse begin
var browseQ = [];
// add for browse end

// add for Cash type begin
var useCashType = false;
// add for Cash type end
function getOrderList() {
	if ($('#order_list').is(':visible')) {
		var order_id = parseInt($('#order_id').text(), 10);
		if (order_id && order_id > 0) {
			$('#order_list').toggle();
			$('#order_info_and_actions').toggle();
		}
	} else {
		var url = 'index.php?route=module/pos/main&token=<?php echo $token; ?>&list=1';
		// add for Quotation begin
		var work_mode = $('input[name=work_mode]').val();
		url += '&work_mode=' + work_mode;
		// add for Quotation end
		$.ajax({
			url: url,
			type: 'post',
			beforeSend: function() {
				removeMessage();
				showMessage('pos_attention', '<?php echo $text_load_order_list; ?>', 'view/image/loading.gif');
			},
			success: function(html) {
				removeMessage();
				$('#order_list').html($(html).find('div[id=\'order_list\']').html());
				$('#order_list').toggle();
				$('#order_info_and_actions').toggle();
				$('.date').datepicker({dateFormat: 'yy-mm-dd'});
				$('.datetime').datetimepicker({
					dateFormat: 'yy-mm-dd',
					timeFormat: 'h:m'
				});
				$('.time').timepicker({timeFormat: 'h:m'});
				// add for table management begin
				/*
				$('#img_table_layout').Jcrop({
					onChange:   selectTable,
					onSelect:   selectTable,
					bgColor:	'white'
				},function(){
					jcrop_api = this;
				});
				for (var i in tables) {
					var coors = tables[i]['coors'];
					var xys = coors.split(',');
					if (xys.length == 4) {
						var x1 = parseInt(xys[0]), y1 = parseInt(xys[1]), x2 = parseInt(xys[2]), y2 = parseInt(xys[3]);
						if (parseFloat(tables[i]['total']) > 0) {
							markTableOrder(tables[i]['table_id'], x1, y1, x2, y2);
						} else { 
							markTableEmpty(tables[i]['table_id'], x1, y1, x2, y2);
						}
					}
				}
				*/
				// add for table management end
			}
		});
	}
};

function filter(anchor, table_id) {
	url = 'index.php?route=module/pos/main&token=<?php echo $token; ?>';
	// add for Quotation begin
	var work_mode = $('input[name=work_mode]').val();
	url += '&work_mode=' + work_mode;
	// add for Quotation end
	var filter_order_id = $('input[name=\'filter_order_id\']').attr('value');
	if (filter_order_id) {
		url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
	}
	var filter_customer = $('input[name=\'filter_customer\']').attr('value');
	if (filter_customer) {
		url += '&filter_customer=' + encodeURIComponent(filter_customer);
	}
	// add for Quotation begin
	if (work_mode == '2') {
		var filter_quote_status_id = $('select[name=\'filter_quote_status_id\']').attr('value');
		if (filter_quote_status_id != '*') {
			url += '&filter_quote_status_id=' + encodeURIComponent(filter_quote_status_id);
		}
	} else {
	// add for Quotation end
	var filter_order_status_id = $('select[name=\'filter_order_status_id\']').attr('value');
	if (filter_order_status_id != '*') {
		url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
	}
	// add for Quotation begin
	}
	// add for Quotation end
	var filter_total = $('input[name=\'filter_total\']').attr('value');
	if (filter_total) {
		url += '&filter_total=' + encodeURIComponent(filter_total);
	}	
	var filter_date_added = $('input[name=\'filter_date_added\']').attr('value');
	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}
	var filter_date_modified = $('input[name=\'filter_date_modified\']').attr('value');
	if (filter_date_modified) {
		url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
	}
	url += '&action=1'
	// add for table management begin
	if (table_id) {
		url += '&filter_table_id=' + encodeURIComponent(table_id)
	}
	// add for table management end
	
	$.ajax({
		url: url,
		type: 'post',
		beforeSend: function() {
			$(anchor).closest('td').append('<div><img src="view/image/loading.gif" alt="" /> <?php echo $text_filter_order_list; ?></div>');
			$(anchor).hide();
		},
		success: function(html) {
			$(anchor).closest('td').find('div').remove();
			$(anchor).show();
			$('#order_list').html($(html).find('div[id=\'order_list\']').html());
			$('.date').datepicker({dateFormat: 'yy-mm-dd'});
			$('.datetime').datetimepicker({
				dateFormat: 'yy-mm-dd',
				timeFormat: 'h:m'
			});
			$('.time').timepicker({timeFormat: 'h:m'});				
		}
	});
};

function selectOrder(anchor, href) {
	// refresh the page using the current order_id
	var td = $(anchor).closest('td');
	var tdhtml = td.html();
	// add for Quotation begin
	var work_mode = $('input[name=work_mode]').val();
	href += '&work_mode=' + work_mode;
	// add for Quotation end
	$.ajax({
		url: href,
		type: 'post',
		beforeSend: function() {
			td.html('<div><img src="view/image/loading.gif" alt="" /> <?php echo $text_load_order; ?></div>');
		},
		success: function(html) {
			td.find('div').remove();
			td.html(tdhtml);
			$('#divWrap').html($(html).find('div[id=\'divWrap\']').html());
			$('.pos_htabs a').tabs();
			$('.pos_vtabs a').tabs();
			$('select[name*=\'customer_group_id\']').trigger('change');
			$('select[name*=\'[country_id]\']').trigger('change');
			moveSelect(-1, 0);
			// add for Quotation begin
			if (work_mode == '2') {
				showMessage('pos_success', '<?php echo isset($text_quote_ready) ? $text_quote_ready : ''; ?>', null);
			} else {
			// add for Quotation end
			
			// add for edit order address begin
			editAddress();
			// add for edit order address end

			showMessage('pos_success', '<?php echo isset($text_order_ready) ? $text_order_ready : ''; ?>', null);
			// add for Quotation begin
			}
			// add for Quotation end
			// add for Discount begin
			calDiscount();
			// add for Discount end
		}
	});
};

function deleteOrder(anchor) {
	if ($('#order_list_form input[type=\'checkbox\']:checked').length == 0) {
		// nothing is selected
		alert('<?php echo $text_no_order_selected; ?>');
	} else {
		if (confirm('<?php echo $text_confirm_delete_order; ?>')) {
			var data = '#order_list_form input[type=\'checkbox\']:checked';
			var url = 'index.php?route=module/pos/main&token=<?php echo $token; ?>';
			$.ajax({
				url: url,
				type: 'post',
				data: $(data),
				dataType: 'json',
				beforeSend: function() {
					$(anchor).closest('td').append('<div><img src="view/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
					$(anchor).hide();
				},
				converters: {
					'text json': true
				},
				success: function(html) {
					$(anchor).closest('td').find('div').remove();
					$(anchor).show();
					$('#order_list').html($(html).find('div[id=\'order_list\']').html());
					$('.date').datepicker({dateFormat: 'yy-mm-dd'});
					$('.datetime').datetimepicker({
						dateFormat: 'yy-mm-dd',
						timeFormat: 'h:m'
					});
					$('.time').timepicker({timeFormat: 'h:m'});				
				}
			});
		}
	}
}

$('.pagination a').live('click', function(event) {
	// check the url and load the page without refresh
	event.preventDefault();
	$.ajax({
		url: this.href,
		type: 'post',
		success: function(html) {
			$('#order_list').html($(html).find('div[id=\'order_list\']').html());
		}
	});
});

$(document).ready(function() {
	$('.date').datepicker({dateFormat: 'yy-mm-dd'});
	$('.datetime').datetimepicker({
		dateFormat: 'yy-mm-dd',
		timeFormat: 'h:m'
	});
	$('.time').timepicker({timeFormat: 'h:m'});
	
	if ('block' == $('#order_product_content').css('display')) {
		$('.pos_htabs a').tabs();

		// select the first row of the table
		preIndex = -1;
		pre_product_id = <?php if(isset($_GET['pre_select'])) {echo $_GET['pre_select']; } else {echo '-100';} ?>;
		if (pre_product_id > -2) {
			for (i = 0; i < $('#product tr').length; i++) {
				cur_product_id = $('#product tr:eq('+i+')').find('input').val();
				if (cur_product_id == pre_product_id) {
					preIndex = i;
					break;
				}
			}
		}
		if (preIndex == -1){
			preIndex = 0;
		}
		moveSelect(-1, preIndex);
	}
	
	full_screen_mode = <?php if (isset($_GET['full_screen_mode'])) { echo $_GET['full_screen_mode']; } elseif (isset($full_screen_mode)) { echo $full_screen_mode; } else { echo '0'; } ?>;
	if (full_screen_mode == 0) {
		$('#header').show();
		$('.breadcrumb').show();
		$('#footer').show();
		$('#button_full_screen').attr('src', 'view/image/pos/header_0_off.png');
		$('#button_full_screen').attr('alt', '<?php echo $button_full_screen; ?>');
		$('#button_full_screen').attr('title', '<?php echo $button_full_screen; ?>');
	} else {
		$('#header').hide();
		$('.breadcrumb').hide();
		$('#footer').hide();
		$('#button_full_screen').attr('src', 'view/image/pos/header_1_off.png');
		$('#button_full_screen').attr('alt', '<?php echo $button_normal_screen; ?>');
		$('#button_full_screen').attr('title', '<?php echo $button_normal_screen; ?>');
	}
	
	showMessage('pos_success', '<?php echo isset($text_order_blank) ? $text_order_blank : ''; ?>', null);
	CheckSizeZoom();
	
	// add for SKU begin
	var pressed = false; 
    var chars = []; 
    // trigger an event on any keypress on this webpage
    $(window).keypress(function(e) {
		// can be numbers for barcode, or can be anything for credit card
        chars.push(String.fromCharCode(e.which));
		
        // debug to help you understand how scanner works
        // console.log(e.which + ":" + chars.join("|"));
        if (pressed == false) {
            // we set a timeout function that expires after 1 sec, once it does it clears out a list 
            // of characters 
            setTimeout(function(){
                // check we have a long length e.g. it is a barcode
                if (chars.length >= 8) {
                    // join the chars array to make a string of the barcode scanned
                    var readin = chars.join("");
                    // debug barcode to console (e.g. for use in Firebug)
                    // console.log("Barcode Scanned: " + readin);
                    // display the main page and switch to add product page
					// $('input[name=sku]').attr('value', readin);
					// handleSKUEntry();
						var scan_type = $('input[name=config_scan_type]').val();
						if (scan_type == '') {
							scan_type = 'upc';
						}
						// barcode
						if (!($('input[name=' + scan_type + ']').is(':focus'))) {
							$('#tab_search').trigger('click');
							$('input[name=' + scan_type + ']').attr('value', readin);
						}
						if (scan_type == 'upc') {
							handleUPCEntry();
						} else if (scan_type == 'sku') {
							handleSKUEntry();
						} else if (scan_type == 'mpn') {
							handleMPNEntry();
						}
                }
                chars = [];
                pressed = false;
            },500);
        }
        // set press to true so we do not reenter the timeout function above
        pressed = true;
    });
	// add for SKU end
});

function showMessage(className, text, imgSrc) {
	divToAppend = '<div class="'+className+'">';
	if (className == 'pos_attention') {
		divToAppend += '<img src="view/image/loading.gif" alt="" /><?php echo $text_wait; ?></div>';
	} else {
		if (imgSrc) {
			divToAppend += '<img src="'+imgSrc+'" alt="" />';
		}
		
		var time = new Date ( );
		var hour = time.getHours();
		var minute = time.getMinutes();
		var second = time.getSeconds();
		hour = (hour < 10 ? "0" : "") + hour;
		minute = (minute < 10 ? "0" : "") + minute;
		second = (second < 10 ? "0" : "") + second;
		var year = time.getFullYear();
		var month = time.getMonth()+1;
		var day = time.getDate();
		month = (month < 10 ? "0" : "") + month;
		day = (day < 10 ? "0" : "") + day;

		divToAppend += '[' + year + '/' + month + '/' + day + ' ' + hour + ':' + minute + ':' + second + '] ' + text + '</div>';
	}
	$('#order_message').append(divToAppend);
};

function removeMessage() {
	$('.pos_success, .pos_warning, .pos_attention, .error').remove();
};

function detachCustomer() {
	var order_id = parseInt($('#order_id').text(), 10);
	$.ajax({
		url: 'index.php?route=module/pos/detachCustomer&token=<?php echo $token; ?>&order_id=' + order_id,
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			removeMessage();
			showMessage('pos_attention', null, null);
		},
		success: function(json) {
			if (json['success']) {
				$('#order_customer input[name=\'customer\']').attr('value', json['firstname']+' '+json['lastname']);
				$('#order_customer input[name=\'customer_id\']').attr('value', json['customer_id']);
				$('#order_customer input[name=\'customer_group_id\']').attr('value', json['customer_group_id']);
				$('#order_customer input[name=\'firstname\']').attr('value', json['firstname']);
				$('#order_customer input[name=\'lastname\']').attr('value', json['lastname']);
				$('#order_customer input[name=\'email\']').attr('value', json['email']);
				$('#order_customer input[name=\'telephone\']').attr('value', json['telephone']);
				$('#order_customer input[name=\'fax\']').attr('value', json['fax']);
				$('input[name=shipping_country_id]').attr('value', json['shipping_country_id']);
				$('input[name=shipping_zone_id]').attr('value', json['shipping_zone_id']);
				$('input[name=payment_country_id]').attr('value', json['payment_country_id']);
				$('input[name=payment_zone_id]').attr('value', json['payment_zone_id']);
				
				$('#general_customer_name').text(json['firstname']+' '+json['lastname']);
				$('#detach_customer_img').remove();
				$('#customer_name_td').append('<img id="add_customer_img" style="vertical-align: middle;" src="view/image/pos/plus_off.png" onclick="addCustomer();" />');
				$('#address_warning').remove();
				
				// add for edit address begin
				$('input[name=\'shipping_firstname\']').attr('value', json['firstname']);
				$('input[name=\'shipping_lastname\']').attr('value', json['lastname']);
				$('input[name=\'shipping_company\']').attr('value', '');
				$('input[name=\'shipping_address_1\']').attr('value', 'customer address');
				$('input[name=\'shipping_address_2\']').attr('value', '');
				$('input[name=\'shipping_city\']').attr('value', 'customer city');
				$('select[name=\'shipping_[country_id]\']').attr('value', json['shipping_country_id']);
				order_country($('select[name=\'shipping_[country_id]\']').get(0), 'shipping', json['shipping_zone_id']);					
				
				$('input[name=\'payment_firstname\']').attr('value', json['firstname']);
				$('input[name=\'payment_lastname\']').attr('value', json['lastname']);
				$('input[name=\'payment_company\']').attr('value', '');
				$('input[name=\'payment_company_id\']').attr('value', '');
				$('input[name=\'payment_tax_id\']').attr('value', '');
				$('input[name=\'payment_address_1\']').attr('value', 'customer address');
				$('input[name=\'payment_address_2\']').attr('value', '');
				$('input[name=\'payment_city\']').attr('value', 'customer city');
				$('select[name=\'payment_[country_id]\']').attr('value', json['payment_country_id']);
				order_country($('select[name=\'payment_[country_id]\']').get(0), 'payment', json['payment_zone_id']);
				
				$('select[name=shipping_address]').empty();
				$('select[name=shipping_address]').append('<option value="0" selected="selected"><?php echo $text_none; ?></option>');
				$('select[name=payment_address]').empty();
				$('select[name=payment_address]').append('<option value="0" selected="selected"><?php echo $text_none; ?></option>');
				// add for edit address end
				$('#order_customer').css('display', 'block');
				$('#customer_customer').css('display', 'none');

				removeMessage();
				showMessage('pos_success', json['success'], null);
			}
		}
	});
}

$('#order_list_form input').live('keydown', function(e) {
	if (e.keyCode == 13) {
		onEnterFilter();
	}
});

$('#button_new_order').live("click", function() {
	var url = 'index.php?route=module/pos/createEmptyOrder&token=<?php echo $token; ?><?php if(isset($store_id)) { echo '&store_id='.$store_id; } ?>';
	// add for Quotation begin
	var work_mode = $('input[name=work_mode]').val();
	url += '&work_mode=' + work_mode;
	// add for Quotation end
	$.ajax({
		url: url,
		type: 'post',
		beforeSend: function() {
			removeMessage();
			showMessage('pos_attention', null, null);
		},
		success: function(html) {
			$('#divWrap').html($(html).find('div[id=\'divWrap\']').html());
			$('.pos_htabs a').tabs();
			$('.pos_vtabs a').tabs();
			$('select[name*=\'[country_id]\']').trigger('change');
			$('select[name*=\'customer_group_id\']').trigger('change');
			checkAndSaveOrder('button_new_order', 0);			
		}
	});
});

function moveSelect(indexPre, index) {
	if (indexPre == index) { 
		return;
	}
	// select index and deselect indexPre
	var indexChecked = $('#product tr').index($('input[name=order_product_id]:checked', '#product').closest('tr'));
	$('#product tr:eq('+index+')').find("input[type=\'radio\']").attr('checked', true);
	$('#radio_selected_index').attr('value', index);
	$('#product tr:eq('+index+')').children('td,th').css('background-color', '#ffeda4');
	if (indexPre != -1) {
		$('#product tr:eq('+indexPre+')').children('td,th').css('background-color', '');
	} else {
		if (indexChecked >= 0 && indexChecked != index) {
			$('#product tr:eq('+indexChecked+')').children('td,th').css('background-color', '');
		}
	}
	if (index >= 0) {
		// not the new row, display product
		var product_id = $('#product tr:eq('+index+')').find("input[name$='[product_id]']").val();
		if (!product_id) {
			if (preTabId) {
				$('#'+preTabId).trigger('click');
			} else {
				$('#tab_browse').trigger('click');
			}
		} else {
			$('#tab_details').trigger('click');
			$('#product_details').empty();
			$.ajax({
				url: 'index.php?route=module/pos/getProductDetails&token=<?php echo $token; ?>&product_id='+product_id,
				beforeSend: function() {
					$('#product_details').html('<table border="0" width="100%" height="100%" align="center" valign="center"><tr align="center"><td align="center"><img src="view/image/loading.gif" class="loading" style="padding-left: 5px;"/></td></tr></table>');
				},
				complete: function() {
					$('.loading').remove();
				},
				success: function(html) {
					$('#product_details').html(html);
				}
			});
		}
		// scroll to the selected row
		var divHeight = $('#order_product_list_content').height();
		var scrollTop = $('#order_product_list thead').height();
		for (var i = 0; i < index; i++) {
			scrollTop += $('#product tr:eq('+i+')').height();
		}
		var scrollBottom = scrollTop + $('#product tr:eq('+index+')').height();
		var curPosition = $('#order_product_list_content').scrollTop();
		if (curPosition > scrollTop || curPosition + divHeight < scrollBottom) {
			$('#order_product_list_content').scrollTop(scrollTop);
		}
	}
};

$.widget('custom.catcomplete', $.ui.autocomplete, {
	_renderMenu: function(ul, items) {
		var self = this, currentCategory = '';
		
		$.each(items, function(index, item) {
			if (item.category != currentCategory) {
				ul.append('<li class="ui-autocomplete-category">' + item.category + '</li>');
				
				currentCategory = item.category;
			}
			
			self._renderItem(ul, item);
		});
	}
});

$('input[name=\'customer\']').live('focus', function(){
	$(this).catcomplete({
		delay: 500,
		source: function(request, response) {
			$.ajax({
				url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
				dataType: 'json',
				success: function(json) {	
					response($.map(json, function(item) {
						return {
							category: item['customer_group'],
							label: item['name'],
							value: item['customer_id'],
							customer_group_id: item['customer_group_id'],
							firstname: item['firstname'],
							lastname: item['lastname'],
							email: item['email'],
							telephone: item['telephone'],
							fax: item['fax'],
							address: item['address']
						}
					}));
				}
			});
		}, 
		select: function(event, ui) { 
			$('input[name=\'customer\']').attr('value', ui.item['label']);
			$('input[name=\'customer_id\']').attr('value', ui.item['value']);
			$('input[name=\'firstname\']').attr('value', ui.item['firstname']);
			$('input[name=\'lastname\']').attr('value', ui.item['lastname']);
			$('input[name=\'email\']').attr('value', ui.item['email']);
			$('input[name=\'telephone\']').attr('value', ui.item['telephone']);
			$('input[name=\'fax\']').attr('value', ui.item['fax']);
				
			html = '<option value="0"><?php echo $text_none; ?></option>'; 
				
			for (i in  ui.item['address']) {
				html += '<option value="' + ui.item['address'][i]['address_id'] + '">' + ui.item['address'][i]['firstname'] + ' ' + ui.item['address'][i]['lastname'] + ', ' + ui.item['address'][i]['address_1'] + ', ' + ui.item['address'][i]['city'] + ', ' + ui.item['address'][i]['country'] + '</option>';
			}
			
			$('select[name=\'shipping_address\']').html(html);
			$('select[name=\'payment_address\']').html(html);
			
			$('select[name=\'customer_group_id\']').attr('disabled', false);
			$('select[name=\'customer_group_id\']').attr('value', ui.item['customer_group_id']);
			$('select[name=\'customer_group_id\']').trigger('change');
			$('select[name=\'customer_group_id\']').attr('disabled', true); 
							
			return false; 
		},
		focus: function(event, ui) {
			return false;
		}
	});
});

// add for Customer Phone Search begin
$('input[name=\'telephone\']').live('focus', function(){
	$(this).catcomplete({
		delay: 500,
		source: function(request, response) {
			$.ajax({
				url: 'index.php?route=sale/customer/autocompleteByPhone&token=<?php echo $token; ?>&filter_telephone=' +  encodeURIComponent(request.term),
				dataType: 'json',
				success: function(json) {	
					response($.map(json, function(item) {
						return {
							category: item['customer_group'],
							label: item['name'] + ' (' + item['telephone'] + ')',
							name: item['name'],
							value: item['customer_id'],
							customer_group_id: item['customer_group_id'],
							firstname: item['firstname'],
							lastname: item['lastname'],
							email: item['email'],
							telephone: item['telephone'],
							fax: item['fax'],
							address: item['address']
						}
					}));
				}
			});
		}, 
		select: function(event, ui) { 
			$('input[name=\'customer\']').attr('value', ui.item['name']);
			$('input[name=\'customer_id\']').attr('value', ui.item['value']);
			$('input[name=\'firstname\']').attr('value', ui.item['firstname']);
			$('input[name=\'lastname\']').attr('value', ui.item['lastname']);
			$('input[name=\'email\']').attr('value', ui.item['email']);
			$('input[name=\'telephone\']').attr('value', ui.item['telephone']);
			$('input[name=\'fax\']').attr('value', ui.item['fax']);
				
			html = '<option value="0"><?php echo $text_none; ?></option>'; 
				
			for (i in  ui.item['address']) {
				html += '<option value="' + ui.item['address'][i]['address_id'] + '">' + ui.item['address'][i]['firstname'] + ' ' + ui.item['address'][i]['lastname'] + ', ' + ui.item['address'][i]['address_1'] + ', ' + ui.item['address'][i]['city'] + ', ' + ui.item['address'][i]['country'] + '</option>';
			}
			
			$('select[name=\'shipping_address\']').html(html);
			$('select[name=\'payment_address\']').html(html);
			
			$('select[id=\'customer_group_id\']').attr('disabled', false);
			$('select[id=\'customer_group_id\']').attr('value', ui.item['customer_group_id']);
			$('select[id=\'customer_group_id\']').trigger('change');
			$('select[id=\'customer_group_id\']').attr('disabled', true); 
							
			return false; 
		},
		focus: function(event, ui) {
			return false;
		}
	});
});
// add for Customer Phone Search end
// add for Customer Email Search begin
$('input[name=\'email\']').live('focus', function(){
	$(this).catcomplete({
		delay: 500,
		source: function(request, response) {
			$.ajax({
				url: 'index.php?route=sale/customer/autocompleteByEmail&token=<?php echo $token; ?>&filter_email=' +  encodeURIComponent(request.term),
				dataType: 'json',
				success: function(json) {	
					response($.map(json, function(item) {
						return {
							category: item['customer_group'],
							label: item['name'] + ' (' + item['email'] + ')',
							name: item['name'],
							value: item['customer_id'],
							customer_group_id: item['customer_group_id'],
							firstname: item['firstname'],
							lastname: item['lastname'],
							email: item['email'],
							telephone: item['telephone'],
							fax: item['fax'],
							address: item['address']
						}
					}));
				}
			});
		}, 
		select: function(event, ui) { 
			$('input[name=\'customer\']').attr('value', ui.item['name']);
			$('input[name=\'customer_id\']').attr('value', ui.item['value']);
			$('input[name=\'firstname\']').attr('value', ui.item['firstname']);
			$('input[name=\'lastname\']').attr('value', ui.item['lastname']);
			$('input[name=\'email\']').attr('value', ui.item['email']);
			$('input[name=\'telephone\']').attr('value', ui.item['telephone']);
			$('input[name=\'fax\']').attr('value', ui.item['fax']);
				
			html = '<option value="0"><?php echo $text_none; ?></option>'; 
				
			for (i in  ui.item['address']) {
				html += '<option value="' + ui.item['address'][i]['address_id'] + '">' + ui.item['address'][i]['firstname'] + ' ' + ui.item['address'][i]['lastname'] + ', ' + ui.item['address'][i]['address_1'] + ', ' + ui.item['address'][i]['city'] + ', ' + ui.item['address'][i]['country'] + '</option>';
			}
			
			$('select[name=\'shipping_address\']').html(html);
			$('select[name=\'payment_address\']').html(html);
			
			$('select[id=\'customer_group_id\']').attr('disabled', false);
			$('select[id=\'customer_group_id\']').attr('value', ui.item['customer_group_id']);
			$('select[id=\'customer_group_id\']').trigger('change');
			$('select[id=\'customer_group_id\']').attr('disabled', true); 
							
			return false; 
		},
		focus: function(event, ui) {
			return false;
		}
	});
});
// add for Customer Email Search end

function showCustomerContent() {
	$('#tab_customers').trigger('click');
};

$('input[name=\'filter_customer\']').live('focus', function(){
	$(this).catcomplete({
		delay: 500,
		source: function(request, response) {
			$.ajax({
				url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
				dataType: 'json',
				success: function(json) {		
					response($.map(json, function(item) {
						return {
							category: item.customer_group,
							label: item.name,
							value: item.customer_id
						}
					}));
				}
			});
		}, 
		select: function(event, ui) {
			$('input[name=\'filter_customer\']').val(ui.item.label);
							
			return false;
		},
		focus: function(event, ui) {
			return false;
		}
	});
});

// add for Manufacturer Product begin
$('input[name=\'manufacturer\']').live('focus', function(){
	$(this).autocomplete({
		delay: 500,
		source: function(request, response) {
			var url = 'index.php?route=catalog/manufacturer/autocomplete&token=<?php echo $token; ?>&filter_name=' + encodeURIComponent(request.term);
			$.ajax({
				url: url,
				dataType: 'json',
				success: function(json) {	
					response($.map(json, function(item) {
						return {
							label: item.name,
							value: item.manufacturer_id
						}
					}));
				}
			});
		}, 
		select: function(event, ui) {
			$('input[name=manufacturer]').attr('value', ui.item['label']);
			$('input[name=manufacturer_id]').attr('value', ui.item['value']);
			return false;
		},
		focus: function(event, ui) {
			return false;
		}
	});
});
// add for Manufacturer Product end

$('input[name=\'product\']').live('focus', function(){
	$(this).autocomplete({
		delay: 500,
		source: function(request, response) {
			var url = 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' + encodeURIComponent(request.term);
			// add for Manufacturer Product begin
			var manufacturer_id = $('input[name=manufacturer_id]').val();
			url += '&filter_manufacturer_id=' + manufacturer_id;
			// add for Manufacturer Product end
			$.ajax({
				url: url,
				dataType: 'json',
				success: function(json) {	
					response($.map(json, function(item) {
						return {
							// add for Weight based price begin
							weight_price: item.weight_price,
							weight_name: item.weight_name,
							// add for Weight based price end
							label: item.name,
							value: item.product_id,
							model: item.model,
							option: item.option,
							price: item.price
						}
					}));
				}
			});
		}, 
		select: function(event, ui) {
			// add for Model begin
			$('input[name=model]').val(ui.item['model']);
			// add for Model end
			handleOptionReturn(ui.item['label'], ui.item['value'], ui.item['option']);
			// add for Weight based price begin
			if (ui.item['weight_price'] == '1') {
				$('#input_quantity').hide();
				$('input[name=quantity]').attr('value', '1');
				$('#weight_name').text(ui.item['weight_name'] + ':');
				$('input[name=weight_name]').attr('value', ui.item['weight_name']);
				$('input[name=weight]').attr('value', '1');
				$('#input_weight').show();
			}
			// add for Weight based price end
			return false;
		},
		focus: function(event, ui) {
			return false;
		}
	});
});

function handleOptionReturn(product_name, product_id, product_option) {
	$('input[name=\'product\']').attr('value', product_name);
	$('input[name=\'product_id\']').attr('value', product_id);
	// add for Weight based price begin
	var hasWeightOption = false;
	// add for Weight based price begin
	// add for serial no begin
	$('input[name=\'product_sn\']').val('');
	$('input[name=\'product_sn_id\']').val('');
	// add for serial no end
	
	if (product_option != '') {
		html = '';

		for (var i = 0; i < product_option.length; i++) {
			var option = product_option[i];
			
			if (option['type'] == 'select') {
				html += '<div id="option-' + option['product_option_id'] + '">';
				
				if (option['required']) {
					html += '<span class="required">*</span> ';
				}
			
				html += option['name'] + '<br />';
				html += '<select name="option[' + option['product_option_id'] + ']">';
				// html += '<option value=""><?php echo $text_select; ?></option>';
			
				for (j = 0; j < option['option_value'].length; j++) {
					option_value = option['option_value'][j];
					
					html += '<option value="' + option_value['product_option_value_id'] + '">' + option_value['name'];
					
					if (option_value['price']) {
						html += ' (' + option_value['price_prefix'] + option_value['price'] + ')';
					}
					
					html += '</option>';
				}
					
				html += '</select>';
				html += '</div>';
				html += '<br />';
			}
			
			if (option['type'] == 'radio') {
				html += '<div id="option-' + option['product_option_id'] + '">';
				
				if (option['required']) {
					html += '<span class="required">*</span> ';
				}
			
				html += option['name'] + '<br />';
				html += '<select name="option[' + option['product_option_id'] + ']">';
				//html += '<option value=""><?php echo $text_select; ?></option>';
			
				for (j = 0; j < option['option_value'].length; j++) {
					option_value = option['option_value'][j];
					
					html += '<option value="' + option_value['product_option_value_id'] + '">' + option_value['name'];
					
					if (option_value['price']) {
						html += ' (' + option_value['price_prefix'] + option_value['price'] + ')';
					}
					
					html += '</option>';
				}
					
				html += '</select>';
				html += '</div>';
				html += '<br />';
			}
				
			if (option['type'] == 'checkbox') {
				html += '<div id="option-' + option['product_option_id'] + '">';
				
				if (option['required']) {
					html += '<span class="required">*</span> ';
				}
				
				html += option['name'] + '<br />';
				
				for (j = 0; j < option['option_value'].length; j++) {
					option_value = option['option_value'][j];
					
					html += '<input type="checkbox" name="option[' + option['product_option_id'] + '][]" value="' + option_value['product_option_value_id'] + '" id="option-value-' + option_value['product_option_value_id'] + '" />';
					html += '<label for="option-value-' + option_value['product_option_value_id'] + '">' + option_value['name'];
					
					if (option_value['price']) {
						html += ' (' + option_value['price_prefix'] + option_value['price'] + ')';
					}
					
					html += '</label>';
					html += '<br />';
				}
				
				html += '</div>';
				html += '<br />';
			}
		
			if (option['type'] == 'image') {
				html += '<div id="option-' + option['product_option_id'] + '">';
				
				if (option['required']) {
					html += '<span class="required">*</span> ';
				}
			
				html += option['name'] + '<br />';
				html += '<select name="option[' + option['product_option_id'] + ']">';
				// html += '<option value=""><?php echo $text_select; ?></option>';
			
				for (j = 0; j < option['option_value'].length; j++) {
					option_value = option['option_value'][j];
					
					html += '<option value="' + option_value['product_option_value_id'] + '">' + option_value['name'];
					
					if (option_value['price']) {
						html += ' (' + option_value['price_prefix'] + option_value['price'] + ')';
					}
					
					html += '</option>';
				}
					
				html += '</select>';
				html += '</div>';
				html += '<br />';
			}
					
			if (option['type'] == 'text') {
				// add for Weight based price begin
				if (option['product_option_id'] == '-1') {
					html += '<div id="option-' + option['product_option_id'] + '" style="display:none;">';
					html += '<input type="hidden" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" />';
					html += '</div>';
					hasWeightOption = true;
				} else {
				// add for Weight based price end
				html += '<div id="option-' + option['product_option_id'] + '">';
				
				if (option['required']) {
					html += '<span class="required">*</span> ';
				}
				
				html += option['name'] + '<br />';
				html += '<input type="text" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" />';
				html += '</div>';
				html += '<br />';
				// add for Weight based price begin
				}
				// add for Weight based price end
			}
			
			if (option['type'] == 'textarea') {
				html += '<div id="option-' + option['product_option_id'] + '">';
				
				if (option['required']) {
					html += '<span class="required">*</span> ';
				}
				
				html += option['name'] + '<br />';
				html += '<textarea name="option[' + option['product_option_id'] + ']" cols="40" rows="5">' + option['option_value'] + '</textarea>';
				html += '</div>';
				html += '<br />';
			}
			
			if (option['type'] == 'file') {
				html += '<div id="option-' + option['product_option_id'] + '">';
				
				if (option['required']) {
					html += '<span class="required">*</span> ';
				}
				
				html += option['name'] + '<br />';
				html += '<a id="button-option-' + option['product_option_id'] + '" class="pos_button"><?php echo $button_upload; ?></a>';
				html += '<input type="hidden" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" />';
				html += '</div>';
				html += '<br />';
			}
			
			if (option['type'] == 'date') {
				html += '<div id="option-' + option['product_option_id'] + '">';
				
				if (option['required']) {
					html += '<span class="required">*</span> ';
				}
				
				html += option['name'] + '<br />';
				html += '<input type="text" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" class="date" />';
				html += '</div>';
				html += '<br />';
			}
			
			if (option['type'] == 'datetime') {
				html += '<div id="option-' + option['product_option_id'] + '">';
				
				if (option['required']) {
					html += '<span class="required">*</span> ';
				}
				
				html += option['name'] + '<br />';
				html += '<input type="text" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" class="datetime" />';
				html += '</div>';
				html += '<br />';						
			}
			
			if (option['type'] == 'time') {
				html += '<div id="option-' + option['product_option_id'] + '">';
				
				if (option['required']) {
					html += '<span class="required">*</span> ';
				}
				
				html += option['name'] + '<br />';
				html += '<input type="text" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" class="time" />';
				html += '</div>';
				html += '<br />';						
			}
		}
		
		// add for Weight based price begin
		if (hasWeightOption && product_option.length == 1) {
			$('#option').html('<td class="left" style="display:none;"><?php echo $entry_option; ?></td><td class="left" style="display:none;">' + html + '</td>');
		} else {
		// add for Weight based price end
		
		$('#option').html('<td class="left"><?php echo $entry_option; ?></td><td class="left">' + html + '</td>');
		
		// add for Weight based price begin
		}
		// add for Weight based price end

		for (i = 0; i < product_option.length; i++) {
			option = product_option[i];
			
			if (option['type'] == 'file') {		
				new AjaxUpload('#button-option-' + option['product_option_id'], {
					action: 'index.php?route=sale/order/upload&token=<?php echo $token; ?>',
					name: 'file',
					autoSubmit: true,
					responseType: 'json',
					data: option,
					onSubmit: function(file, extension) {
						$('#button-option-' + (this._settings.data['product_option_id'] + '-' + this._settings.data['product_option_id'])).after('<img src="view/image/loading.gif" class="loading" />');
					},
					onComplete: function(file, json) {

						$('.error').remove();
						
						if (json['success']) {
							$('input[name=\'option[' + this._settings.data['product_option_id'] + ']\']').attr('value', json['file']);
						}
						
						if (json.error) {
							$('#option-' + this._settings.data['product_option_id']).after('<span class="error">' + json['error'] + '</span>');
						}
						
						$('.loading').remove();	
					}
				});
			}
		}
		
		$('.date').datepicker({dateFormat: 'yy-mm-dd'});
		$('.datetime').datetimepicker({
			dateFormat: 'yy-mm-dd',
			timeFormat: 'h:m'
		});
		$('.time').timepicker({timeFormat: 'h:m'});
	} else {
		$('#option td').remove();
	}
};

// add for SKU begin
$('input[name=sku]').live('keydown', function(e) {
	if (e.keyCode == 13) {
		handleSKUEntry();
	}
});
enterFns['sku'] = 'handleSKUEntry';

function handleSKUEntry() {
	// search the product using SKU
	var sku = $('input[name=sku]').val();
	if (sku != '') {
		$.ajax({
			url: 'index.php?route=module/pos/handleSKUEntry&token=<?php echo $token; ?>&sku=' + sku,
			type: 'post',
			dataType: 'json',
			beforeSend: function() {
				removeMessage();
				showMessage('pos_attention', null, null);
			},
			success: function(json) {
				if (json['product_id']) {
					// product found by SKU
					$('input[name=\'product\']').attr('value', json['name']);
					$('input[name=\'product_id\']').attr('value', json['product_id']);
					
					// add for Weight based price begin
					if (json['weight_price'] == '1') {
						$('#input_quantity').hide();
						$('input[name=quantity]').attr('value', '1');
						$('#weight_name').text(json['weight_name'] + ':');
						$('input[name=weight_name]').attr('value', json['weight_name']);
						$('input[name=weight]').attr('value', '1');
						$('#input_weight').show();
					}
					// add for Weight based price end

					if (json['option']) {
						// option is required
						handleOptionReturn(json['name'], json['product_id'], json['option']);
						
						removeMessage();
					} else {
						// no option, add the product straightway
						checkAndSaveOrder('button_product', 0);
					}
				} else {
					removeMessage();
					showMessage('pos_warning', '<?php echo $text_no_product_for_sku; ?>' + sku, null);
				}
			}
		});
	}
};
// add for SKU end
// add for UPC begin
$('input[name=upc]').live('keydown', function(e) {
	if (e.keyCode == 13) {
		handleUPCEntry();
	}
});
enterFns['upc'] = 'handleUPCEntry';

function handleUPCEntry() {
	// search the product using UPC
	var upc = $('input[name=upc]').val();
	if (upc != '') {
		$.ajax({
			url: 'index.php?route=module/pos/handleUPCEntry&token=<?php echo $token; ?>&upc=' + upc,
			type: 'post',
			dataType: 'json',
			beforeSend: function() {
				removeMessage();
				showMessage('pos_attention', null, null);
			},
			success: function(json) {
				if (json['product_id']) {
					// product found by UPC
					$('input[name=\'product\']').attr('value', json['name']);
					$('input[name=\'product_id\']').attr('value', json['product_id']);
					
					// add for Weight based price begin
					if (json['weight_price'] == '1') {
						$('#input_quantity').hide();
						$('input[name=quantity]').attr('value', '1');
						$('#weight_name').text(json['weight_name'] + ':');
						$('input[name=weight_name]').attr('value', json['weight_name']);
						$('input[name=weight]').attr('value', '1');
						$('#input_weight').show();
					}
					// add for Weight based price end

					if (json['option']) {
						// option is required
						handleOptionReturn(json['name'], json['product_id'], json['option']);
						removeMessage();
					} else {
						// no option, add the product straightway
						checkAndSaveOrder('button_product', 0);
					}
				} else {
					removeMessage();
					showMessage('pos_warning', '<?php echo $text_no_product_for_upc; ?>' + upc, null);
				}
			}
		});
	}
};
// add for UPC end
// add for MPN begin
$('input[name=mpn]').live('keydown', function(e) {
	if (e.keyCode == 13) {
		handleMPNEntry();
	}
});
enterFns['mpn'] = 'handleMPNEntry';

function handleMPNEntry() {
	// search the product using MPN
	var mpn = $('input[name=mpn]').val();
	if (mpn != '') {
		$.ajax({
			url: 'index.php?route=module/pos/handleMPNEntry&token=<?php echo $token; ?>&mpn=' + mpn,
			type: 'post',
			dataType: 'json',
			beforeSend: function() {
				removeMessage();
				showMessage('pos_attention', null, null);
			},
			success: function(json) {
				if (json['product_id']) {
					// product found by MPN
					$('input[name=\'product\']').attr('value', json['name']);
					$('input[name=\'product_id\']').attr('value', json['product_id']);
					
					// add for Weight based price begin
					if (json['weight_price'] == '1') {
						$('#input_quantity').hide();
						$('input[name=quantity]').attr('value', '1');
						$('#weight_name').text(json['weight_name'] + ':');
						$('input[name=weight_name]').attr('value', json['weight_name']);
						$('input[name=weight]').attr('value', '1');
						$('#input_weight').show();
					}
					// add for Weight based price end

					if (json['option']) {
						// option is required
						handleOptionReturn(json['name'], json['product_id'], json['option']);
						removeMessage();
					} else {
						// no option, add the product straightway
						checkAndSaveOrder('button_product', 0);
					}
				} else {
					removeMessage();
					showMessage('pos_warning', '<?php echo $text_no_product_for_mpn; ?>' + mpn, null);
				}
			}
		});
	}
};
// add for MPN end
// add for model begin
$('input[name=\'model\']').live('focus', function(){
	$(this).autocomplete({
		delay: 500,
		source: function(request, response) {
			var url = 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_model=' + encodeURIComponent(request.term);
			// add for Manufacturer Product begin
			var manufacturer_id = $('input[name=manufacturer_id]').val();
			url += '&filter_manufacturer_id=' + manufacturer_id;
			// add for Manufacturer Product end
			$.ajax({
				url: url,
				dataType: 'json',
				success: function(json) {	
					response($.map(json, function(item) {
						return {
							// add for Weight based price begin
							weight_price: item.weight_price,
							weight_name: item.weight_name,
							// add for Weight based price end
							label: item.model,
							value: item.product_id,
							name: item.name,
							option: item.option,
							price: item.price
						}
					}));
				}
			});
		}, 
		select: function(event, ui) {
			$('input[name=\'model\']').val(ui.item['label']);
			handleOptionReturn(ui.item['name'], ui.item['value'], ui.item['option']);
			// add for Weight based price begin
			if (ui.item['weight_price'] == '1') {
				$('#input_quantity').hide();
				$('input[name=quantity]').attr('value', '1');
				$('#weight_name').text(ui.item['weight_name'] + ':');
				$('input[name=weight_name]').attr('value', ui.item['weight_name']);
				$('input[name=weight]').attr('value', '1');
				$('#input_weight').show();
			}
			// add for Weight based price end
			return false;
		},
		focus: function(event, ui) {
			return false;
		}
	});
});
// add for model end

$('select[name=\'customer_group_id\']').live('change', function() {
	var customer_group_id = this.value;

	// add for edit order address begin
	var customer_id = $('input[name=customer_id]').val();
	customer_id = (customer_id == '') ? 0 : parseInt($('input[name=customer_id]').val());
	// add for edit order address end

	if ($('input[name='+customer_group_id+'_company_id_display]').val() == '1') {
		$('.company-id-display').show();
		// add for edit order address begin
		if (customer_id == 0) {
			$('.order-company-id-display').show();
		}
		// add for edit order address end
	} else {
		$('.company-id-display').hide();
		// add for edit order address begin
		if (customer_id == 0) {
			$('.order-company-id-display').hide();
		}
		// add for edit order address end
	}

	if ($('input[name='+customer_group_id+'_tax_id_display]').val() == '1') {
		$('.tax-id-display').show();
		// add for edit order address begin
		if (customer_id == 0) {
			$('.order-tax-id-display').show();
		}
		// add for edit order address end
	} else {
		$('.tax-id-display').hide();
		// add for edit order address begin
		if (customer_id == 0) {
			$('.order-tax-id-display').hide();
		}
		// add for edit order address end
	}
});

$('select[name=\'customer_group_id\']').trigger('change');

$('select[name=\'customer_customer_group_id\']').live('change', function() {
	var customer_group_id = this.value;

	// add for edit order address begin
	var customer_id = $('input[name=customer_id]').val();
	customer_id = (customer_id == '') ? 0 : parseInt($('input[name=customer_id]').val());
	// add for edit order address end
	
	if ($('input[name='+customer_group_id+'_customer_company_id_display]').val() == '1') {
		$('.customer-company-id-display').show();
		// add for edit order address begin
		if (customer_id > 0) {
			$('.order-company-id-display').show();
		}
		// add for edit order address end
	} else {
		$('.customer-company-id-display').hide();
		// add for edit order address begin
		if (customer_id > 0) {
			$('.order-company-id-display').hide();
		}
		// add for edit order address end
	}
	if ($('input[name='+customer_group_id+'_customer_tax_id_display]').val() == '1') {
		$('.customer-tax-id-display').show();
		// add for edit order address begin
		if (customer_id > 0) {
			$('.order-tax-id-display').show();
		}
		// add for edit order address end
	} else {
		$('.customer-tax-id-display').hide();
		// add for edit order address begin
		if (customer_id > 0) {
			$('.order-tax-id-display').hide();
		}
		// add for edit order address end
	}
});

$('select[name=\'customer_customer_group_id\']').trigger('change');

$('#button_custom_cancel').live('click', function() {
	moveSelect(-1, $('#product tr').length-1);
});

$('#customer_button_cancel').live('click', function() {
	// add for Add Customer begin
	// if do not want to save the customer, remove the added customer
	var customer_customer_id = $('input[name=customer_customer_id]').val();
	if (customer_customer_id != '-1') {
		// the add customer button was pressed
		$.ajax({
			url: 'index.php?route=module/pos/removeEmptyCustomer&token=<?php echo $token; ?>&customer_id=' + customer_customer_id,
			type: 'post',
			dataType: 'json',
			beforeSend: function() {
				removeMessage();
				showMessage('pos_attention', null, null);
			},
			success: function(json) {
				$('input[name=customer_customer_id]').attr('value', '-1');
				removeMessage();
			}
		});
		$('#order_customer').css('display', 'block');
		$('#customer_customer').css('display', 'none');		
	}
	// add for Add Customer end
	moveSelect(-1, $('#product tr').length-1);
});

$('#button_custom_save').live('click', function() {
	var order_id = parseInt($('#order_id').text(), 10);
	var data = '#order_customer input[type=\'text\'], #order_customer input[type=\'hidden\'], #order_customer input[type=\'radio\']:checked, #order_customer input[type=\'checkbox\']:checked, #order_customer select, #order_customer textarea';
	$.ajax({
		url: 'index.php?route=module/pos/saveOrderCustomer&token=<?php echo $token; ?>&order_id=' + order_id,
		type: 'post',
		data: $(data),
		dataType: 'json',
		beforeSend: function() {
			removeMessage();
			showMessage('pos_attention', null, null);
		},
		success: function(json) {
			if (json['success']) {
				removeMessage();
				showMessage('pos_success', json['success'], null);
				var name = $('#order_customer input[name=\'firstname\']').val() + ' ' + $('#order_customer input[name=\'lastname\']').val();
				$('#general_customer_name').text(name);
				$('#address_warning').remove();
				if (json['hasAddress'] && json['hasAddress'] == '1') {
					$('#general_customer_name').before('<img id="address_warning" style="vertical-align: middle;" src="view/image/warning.png" alt="<?php echo $text_customer_no_address; ?>" title="<?php echo $text_customer_no_address; ?>" />');
				}
				// switch customer if customer_id is not 0
				if (json['customer_info']) {
					// add for Add Customer begin
					$('#add_customer_img').remove();
					// add for Add Customer end
					$('#customer_name_td').append('&nbsp&nbsp;<img id="detach_customer_img" style="vertical-align: middle;" src="view/image/pos/minus_off.png" onclick="detachCustomer();" />');
					$('#vtabs').empty();
					$('#vtabs').append('<a href="#tab_customer"><?php echo $tab_general; ?></a>');
					i = 1;
					for (var row_index in json['customer_addresses']) {
						$('#vtabs').append('<a href="#tab_address_'+i+'" id="address_'+i+'"><?php echo $tab_address; ?>'+i+'&nbsp;<img src="view/image/delete.png" alt="" onclick="$(\'#vtabs a:first\').trigger(\'click\'); $(\'#address_'+i+'\').remove(); $(\'#tab_address_'+i+'\').remove(); return false;" /></a>');
						i ++;
					}
					$('#vtabs').append('<span id="address_add"><?php echo $button_add_address; ?>&nbsp;<img src="view/image/add.png" alt="" onclick="addAddress();" /></span>');
					$('input[name=\'customer_firstname\']').attr('value', json['customer_info']['firstname']);
					$('input[name=\'customer_lastname\']').attr('value', json['customer_info']['lastname']);
					$('input[name=\'customer_email\']').attr('value', json['customer_info']['email']);
					$('input[name=\'customer_telephone\']').attr('value', json['customer_info']['telephone']);
					$('input[name=\'customer_fax\']').attr('value', json['customer_info']['fax']);
					$('select[name=\'customer_newsletter\']').attr('value', json['customer_info']['newsletter']);
					$('select[name=\'customer_newsletter\']').change();
					if (json['country_id']) {
						$('input[name=shipping_country_id]').attr('value', json['country_id']);
						$('input[name=payment_country_id]').attr('value', json['country_id']);
					}
					if (json['zone_id']) {
						$('input[name=shipping_zone_id]').attr('value', json['zone_id']);
						$('input[name=payment_zone_id]').attr('value', json['zone_id']);
					}
					$('select[name=\'customer_customer_group_id\']').attr('value', json['customer_info']['customer_group_id']);
					$('select[name=\'customer_customer_group_id\']').change();
					$('select[name=\'customer_status\']').attr('value', json['customer_info']['status']);
					$('select[name=\'customer_status\']').change();
					$('div[id^=\'tab_address_\']').remove();
					
					var address_row = 1;
					for (i in json['customer_addresses']) {
						html = '';
						html += '<div id="tab_address_'+address_row+'" class="pos_vtabs-content">';
						html += '<input type="hidden" name="customer_address['+address_row+'][address_id]" value="'+json['customer_addresses'][i]['address_id']+'" />';
						html += '<table class="form">';
						html += '<tr>';
						html += '<td><span class="required">*</span> <?php echo $entry_firstname; ?></td>';
						html += '<td><input type="text" name="customer_address['+address_row+'][firstname]" value="'+json['customer_addresses'][i]['firstname']+'" /></td>';
						html += '</tr>';
						html += '<tr>';
						html += '<td><span class="required">*</span> <?php echo $entry_lastname; ?></td>';
						html += '<td><input type="text" name="customer_address['+address_row+'][lastname]" value="'+json['customer_addresses'][i]['lastname']+'" /></td>';
						html += '</tr>';
						html += '<tr>';
						html += '<td><span class="required">*</span> <?php echo $entry_company; ?></td>';
						html += '<td><input type="text" name="customer_address['+address_row+'][company]" value="'+json['customer_addresses'][i]['company']+'" /></td>';
						html += '</tr>';
						html += '<tr class="customer-company-id-display">';
						html += '<td><?php echo $entry_company_id; ?></td>';
						html += '<td><input type="text" name="customer_address['+address_row+'][company_id]" value="'+json['customer_addresses'][i]['company_id']+'" /></td>';
						html += '</tr>';
						html += '<tr class="customer-tax-id-display">';
						html += '<td><?php echo $entry_tax_id; ?></td>';
						html += '<td><input type="text" name="customer_address['+address_row+'][tax_id]" value="'+json['customer_addresses'][i]['tax_id']+'" /></td>';
						html += '</tr>';
						html += '<tr>';
						html += '<td><span class="required">*</span> <?php echo $entry_address_1; ?></td>';
						html += '<td><input type="text" name="customer_address['+address_row+'][address_1]" value="'+json['customer_addresses'][i]['address_1']+'" /></td>';
						html += '</tr>';
						html += '<tr>';
						html += '<td><span class="required">*</span> <?php echo $entry_address_2; ?></td>';
						html += '<td><input type="text" name="customer_address['+address_row+'][address_2]" value="'+json['customer_addresses'][i]['address_2']+'" /></td>';
						html += '</tr>';
						html += '<tr>';
						html += '<td><span class="required">*</span> <?php echo $entry_city; ?></td>';
						html += '<td><input type="text" name="customer_address['+address_row+'][city]" value="'+json['customer_addresses'][i]['city']+'" /></td>';
						html += '</tr>';
						html += '<tr>';
						html += '<td><span id="postcode-required'+address_row+'" class="required">*</span> <?php echo $entry_postcode; ?></td>';
						html += '<td><input type="text" name="customer_address['+address_row+'][postcode]" value="'+json['customer_addresses'][i]['postcode']+'" /></td>';
						html += '</tr>';
						html += '<tr>';
						html += '<td><span class="required">*</span> <?php echo $entry_country; ?></td>';
						html += '<td><select name="customer_address['+address_row+'][country_id]" onchange="country(this, \''+address_row+'\', \''+json['customer_addresses'][i]['zone_id']+'\');">';
						html += '<option value=""><?php echo $text_select; ?></option>';
						for (j in json['customer_countries']) {
							if (json['customer_countries'][j]['country_id'] == json['customer_addresses'][i]['country_id']) {
								html += '<option value="'+json['customer_countries'][j]['country_id']+'" selected="selected">'+json['customer_countries'][j]['name']+'</option>';
							} else {
								html += '<option value="'+json['customer_countries'][j]['country_id']+'">'+json['customer_countries'][j]['name']+'</option>';
							}
						}
						html += '</select></td>';
						html += '</tr>';
						html += '<tr>';
						html += '<td><span class="required">*</span> <?php echo $entry_zone; ?></td>';
						html += '<td><select name="customer_address[' + address_row + '][zone_id]"><option value="false"><?php echo $this->language->get('text_none'); ?></option></select></td>';
						html += '</tr>';
						html += '<tr>';
						html += '<td><?php echo $entry_default; ?></td>';
						if (json['customer_info']['address_id'] == json['customer_addresses'][i]['address_id']) {
							html += '<td><input type="radio" name="customer_address['+address_row+'][default]" value="'+address_row+'" checked="checked" /></td>';
						} else {
							html += '<td><input type="radio" name="customer_address['+address_row+'][default]" value="'+address_row+'" /></td>';
						}
						html += '</tr>';
						html += '</table>';
						html += '</div>';
						
						$('#customer_customer').append(html);
						$('select[name=\'customer_address[' + address_row + '][country_id]\']').trigger('change');
						address_row ++;
					}
					$('.pos_vtabs a').tabs();
					$('select[name=\'customer_customer_group_id\']').change();

					// add for edit order address begin
					$('select[name=shipping_address]').empty();
					$('select[name=shipping_address]').append('<option value="0" selected="selected"><?php echo $text_none; ?></option>');
					$('select[name=payment_address]').empty();
					$('select[name=payment_address]').append('<option value="0" selected="selected"><?php echo $text_none; ?></option>');
					for (i in json['customer_addresses']) {
						$('select[name=shipping_address]').append('<option value="' + json['customer_addresses'][i]['address_id'] + '">' + json['customer_addresses'][i]['firstname'] + ' ' + json['customer_addresses'][i]['lastname'] + ', ' + json['customer_addresses'][i]['address_1'] + ', ' + json['customer_addresses'][i]['city'] + ', ' + json['customer_addresses'][i]['country'] + '</option>');
						$('select[name=payment_address]').append('<option value="' + json['customer_addresses'][i]['address_id'] + '">' + json['customer_addresses'][i]['firstname'] + ' ' + json['customer_addresses'][i]['lastname'] + ', ' + json['customer_addresses'][i]['address_1'] + ', ' + json['customer_addresses'][i]['city'] + ', ' + json['customer_addresses'][i]['country'] + '</option>');
					}
					// add for edit order address end
					
					$('#order_customer').css('display', 'none');
					$('#customer_customer').css('display', 'block');
				}
				
				moveSelect(-1, $('#product tr').length-1);
			}
		}
	});
});

$('#customer_button_save').live('click', function() {
	saveCustomer();
});

function completeOrder() {
	// add for Quotation begin
	var work_mode = $('input[name=work_mode]').val();
	if (work_mode == '2') {
		// convert a quote to an order
		var order_id = parseInt($('#order_id').text(), 10);
		var data = {'order_id': order_id};
		$.ajax({
			url: 'index.php?route=module/pos/convertQuote2Order&token=<?php echo $token; ?>',
			type: 'post',
			data: data,
			dataType: 'json',
			beforeSend: function() {
				removeMessage();
				showMessage('pos_attention', null, null);
			},
			success: function(json) {
				if (json['success']) {
					removeMessage();
					showMessage('pos_success', json['success'], null);
					// change to order mode
					modeOrder();
					// change the order id
					$('#order_id').text(json['order_id']);
					// show order status
					$('#order_status').attr('value', 1);
					$('#order_status').show();
					$('#quote_status').hide();
					// change id name
					$('#mode_id_name').text(json['order_text']);
				}
			}
		});
	} else {
	// add for Quotation end

	// add for Complete Status begin
	<?php if (isset($text_complete_status_id)) { ?>
	$('#order_status').val(<?php echo $text_complete_status_id; ?>);
	<?php } else { ?>
	// add for Complete Status end
	
	$('#order_status').val(5);
	
	// add for Complete Status begin
	<?php } ?>
	// add for Complete Status end
	$('#order_status').trigger('change');
	
	// add for Quotation begin
	}
	// add for Quotation end
}

$('#order_status').live('change', function() {
	var order_id = parseInt($('#order_id').text(), 10);
	var data = {'order_id': order_id, 'order_status_id':$(this).val()};
	$.ajax({
		url: 'index.php?route=module/pos/saveOrderStatus&token=<?php echo $token; ?>',
		type: 'post',
		data: data,
		dataType: 'json',
		beforeSend: function() {
			removeMessage();
			showMessage('pos_attention', null, null);
		},
		success: function(json) {
			if (json['success']) {
				removeMessage();
				showMessage('pos_success', json['success'], null);
				// add for Print begin
				var p_complete = 0;
				if (json['p_complete']) {
					p_complete = json['p_complete'];
				}
				if (p_complete > 0) {
					// print receipt if set in the settings page
					$('#print_message').text('<?php echo $print_receipt_message; ?>');
					window_print_url('index.php?route=module/pos/receipt&token=<?php echo $token; ?>&order_id='+order_id, {'change':'1'}, afterPrintReceipt, null);
				}
				// add for Print end
				// add for Empty order control begin
				if (json['initial_status_id']) {
					var initial_status_id = parseInt(json['initial_status_id']);
					$('#order_status option[value=' + initial_status_id + ']').remove();
				}
				// add for Empty order control end
	
				// add for refresh after complete begin
				// add for Complete Status begin
				<?php if (isset($text_complete_status_id)) { ?>
				if ($('#order_status').val() == '<?php echo $text_complete_status_id; ?>') {
				<?php } else { ?>
				// add for Complete Status end
				
				if ($('#order_status').val() == '5') {
				
				// add for Complete Status begin
				<?php } ?>
				// add for complete Status end
					// add for table management begin
					if (parseInt('<?php echo $enable_table_management; ?>') > 0) {
						getOrderList();
					} else {
					// add for table management end
					var url = 'index.php?route=module/pos/main&token=<?php echo $token; ?>';
					// add for Quotation begin
					var work_mode = $('input[name=work_mode]').val();
					url += '&work_mode=' + work_mode;
					// add for Quotation end
					$.ajax({
						url: url,
						type: 'post',
						beforeSend: function() {
							removeMessage();
							showMessage('pos_attention', null, null);
						},
						success: function(html) {
							$('#divWrap').html($(html).find('div[id=\'divWrap\']').html());
							$('.pos_htabs a').tabs();
							$('.pos_vtabs a').tabs();
							$('select[name*=\'customer_group_id\']').trigger('change');
							$('select[name*=\'[country_id]\']').trigger('change');
							moveSelect(-1, 0);
							removeMessage();
							showMessage('pos_success', '<?php echo isset($text_order_blank) ? $text_order_blank : ''; ?>', null);
						}
					});
					
					// add for table management begin
					}
					// add for table management end
				}
				// add for refresh after complete end
			}
		}
	});
});

// add for Quotation begin
$('#quote_status').live('change', function() {
	var order_id = parseInt($('#order_id').text(), 10);
	var data = {'order_id': order_id, 'quote_status_id':$(this).val()};
	$.ajax({
		url: 'index.php?route=module/pos/saveQuoteStatus&token=<?php echo $token; ?>',
		type: 'post',
		data: data,
		dataType: 'json',
		beforeSend: function() {
			removeMessage();
			showMessage('pos_attention', null, null);
		},
		success: function(json) {
			if (json['success']) {
				removeMessage();
				showMessage('pos_success', json['success'], null);
			}
		}
	});
});
// add for Quotation end

// add for Print begin
function afterPrintReceipt() {
	$('#pos_print').dialog('close');
}
// add for Print end

$('#product tr').live('click', function() {
	index = $('#product tr').index($(this));
	if (index >= 0) {
		indexPre = $('#product tr').index($('input[name=order_product_id]:checked', '#product').closest('tr'));
		moveSelect(indexPre, index);
	}
});

$('#total_tr td:lt(2)').live('click', function() {
	toggleTotalDetails();
});

function toggleTotalDetails() {
	$('#totals_details').slideToggle('slow');
	var bgImg = $('#payment_total').css('background-image');
	if (bgImg.indexOf('_down') >= 0) {
		$('#payment_total').css('background-image', bgImg.replace('_down', '_up'));
	} else {
		$('#payment_total').css('background-image', bgImg.replace('_up', '_down'));
	}
};

$('#product input[type=\'radio\']').live('click', function () {
	indexPre = $('#radio_selected_index').val();
	index = $('#product tr').index($('input[name=order_product_id]:checked', '#product').closest('tr'));
	if (index >= 0) {
		moveSelect(indexPre, index);
	}
});

$('#button_up').live('click', function() {
	index = $('#product tr').index($('input[name=order_product_id]:checked', '#product').closest('tr'));
	indexPre = index;
	if (index == -1) {
		index = 0;
	} else if (index == 0) {
		index = $('#product tr').length -1;
	} else {
		index --;
	}
	moveSelect(indexPre, index);
});

$('#button_down').live('click', function() {
	index = $('#product tr').index($('input[name=order_product_id]:checked', '#product').closest('tr'));
	indexPre = index;
	if (index == -1 || (index == $('#product tr').length -1)) {
		index = 0;
	} else {
		index ++;
	}
	
	moveSelect(indexPre, index);
});

function disableActions() {
	$('#button_plus').unbind('click').attr('disabled', 'disabled');
	$('#button_minus').unbind('click').attr('disabled', 'disabled');
	$('#button_equal').unbind('click').attr('disabled', 'disabled');
	$('#button_delete').unbind('click').attr('disabled', 'disabled');
}

$('#button_plus, #button_minus, #button_delete, #button_voucher').live('click', function() {
	checkAndSaveOrder($(this).attr('id'), 0);
});

$('#button_product').live('click', function() {
	checkAndSaveOrder($(this).attr('id'), 0);
});

$('#button_equal').live('click', function() {
	var index = $('#product tr').index($('input[name=order_product_id]:checked', '#product').closest('tr'));
	if (index >= 0 && index < $('#product tr').length -1) {
		$('#product tr:eq('+index+')').find('td').eq(2).trigger('click');
	}
});

// add for inplace pricing begin
$('#button_price').live('click', function() {
	var index = $('#product tr').index($('input[name=order_product_id]:checked', '#product').closest('tr'));
	if (index >= 0 && index < $('#product tr').length -1) {
		$('#product tr:eq('+index+')').find('td').eq(3).trigger('click');
	}
});
// add for inplace pricing end

function saveCustomer() {
	var data = '#customer_customer input[type=\'text\'], #customer_customer input[type=\'hidden\'], #customer_customer input[type=\'password\'], #customer_customer input[type=\'radio\']:checked, #customer_customer input[type=\'checkbox\']:checked, #customer_customer select, #customer_customer textarea';
	var url = 'index.php?route=module/pos/save_customer&token=<?php echo $token; ?>';
	var order_id = parseInt($('#order_id').text(), 10);
	// add for Add Customer begin
	if ($('input[name=\'customer_customer_id\']').val() != '-1') {
		// add customer button was pressed
		url += '&order_id=' + order_id;
	} else {
	// add for Add Customer end
	var customer_id = $('input[name=customer_id]').val();
	url += '&customer_id=' + customer_id + '&customer_order_id=' + order_id;
	// add for Add Customer begin
	}
	// add for Add Customer end
	$.ajax({
		url: url,
		type: 'post',
		data: $(data),
		dataType: 'json',
		beforeSend: function() {
			removeMessage();
			showMessage('pos_attention', null, null);
		},
		success: function(json) {
			if (json['success']) {
				removeMessage();
				showMessage('pos_success', json['success'], null);
				// add for Add Customer begin
				if ($('input[name=\'customer_customer_id\']').val() != '-1') {
					$('#add_customer_img').remove();
					$('#detach_customer_img').remove();
					$('#customer_name_td').append('<img id="detach_customer_img" style="vertical-align: middle;" src="view/image/pos/minus_off.png" onclick="detachCustomer();" />');
					$('input[name=\'customer_id\']').attr('value', $('input[name=\'customer_customer_id\']').val());
					$('input[name=\'customer_customer_id\']').attr('value', '-1');
					$('select[name=\'customer_customer_group_id\']').trigger('change');
				}
				// add for Add Customer end
				var name = $('input[name=\'customer_firstname\']').val() + " " + $('input[name=\'customer_lastname\']').val();
				$('#general_customer_name').text(name);
				if (json['hasAddress'] && json['hasAddress'] == '2') {
					$('#address_warning').remove();
				}
				// add for edit order address begin
				if (json['customer_addresses']) {
					$('select[name=shipping_address]').empty();
					$('select[name=shipping_address]').append('<option value="0" selected="selected"><?php echo $text_none; ?></option>');
					$('select[name=payment_address]').empty();
					$('select[name=payment_address]').append('<option value="0" selected="selected"><?php echo $text_none; ?></option>');
					for (i in json['customer_addresses']) {
						$('select[name=shipping_address]').append('<option value="' + json['customer_addresses'][i]['address_id'] + '">' + json['customer_addresses'][i]['firstname'] + ' ' + json['customer_addresses'][i]['lastname'] + ', ' + json['customer_addresses'][i]['address_1'] + ', ' + json['customer_addresses'][i]['city'] + ', ' + json['customer_addresses'][i]['country'] + '</option>');
						$('select[name=payment_address]').append('<option value="' + json['customer_addresses'][i]['address_id'] + '">' + json['customer_addresses'][i]['firstname'] + ' ' + json['customer_addresses'][i]['lastname'] + ', ' + json['customer_addresses'][i]['address_1'] + ', ' + json['customer_addresses'][i]['city'] + ', ' + json['customer_addresses'][i]['country'] + '</option>');
					}
				}
				// add for edit order address end
				
				moveSelect(-1, $('#product tr').length-1);
			}
		},
		error: function(json) {
			$('#order_product_content').css('display', 'block');
			$('#order_customer_content').css('display', 'none');
			if (json['responseText']) {
				removeMessage();
				var index = json['responseText'].indexOf('{');
				showMessage('pos_warning', json['responseText'].substr(0, index), null);
				// add for Add Customer begin
				if ($('input[name=\'customer_customer_id\']').val() != '-1') {
					$('#add_customer_img').remove();
					$('#detach_customer_img').remove();
					$('#customer_name_td').append('<img id="detach_customer_img" style="vertical-align: middle;" src="view/image/pos/minus_off.png" onclick="detachCustomer();" />');
					$('input[name=\'customer_id\']').attr('value', $('input[name=\'customer_customer_id\']').val());
					$('input[name=\'customer_customer_id\']').attr('value', '-1');
					$('select[name=\'customer_customer_group_id\']').trigger('change');
				}
				// add for Add Customer end
				var name = $('input[name=\'customer_firstname\']').val() + " " + $('input[name=\'customer_lastname\']').val();
				$('#general_customer_name').text(name);
			}
		}
	});
};

function checkAndSaveOrder(eleId, quantity, orgQty) {
	if ($('#order_id').text() == '') return false;
	
	if (eleId == 'button_product') {
		preTabId = 'tab_search';
		// add for Weight based price begin
		// once add product button is pressed, check if weight is required
		if ($('input[name=weight]').val() != '0') {
			// the value was changed, set the weight option value
			$('input[name=\'option[-1]\']').attr('value', $('input[name=weight]').val());
		}
		// add for Weight based price end
		var prodQtyInput = $('#product_new input[name=\'quantity\']');
		var prodQty = prodQtyInput.val();
		prodQty = posParseFloat(prodQty);
		// check if zero is in the text
		if (prodQty <= 0) {
			prodQtyInput.css('border', 'solid 2px #FF0000');
			prodQtyInput.attr('alt', '<?php echo $text_quantity_zero; ?>');
			prodQtyInput.attr('title', '<?php echo $text_quantity_zero; ?>');
			return false;
		} else {
			prodQtyInput.css('border', '');
			prodQtyInput.attr('alt', '');
			prodQtyInput.attr('title', '');
		}
	}
	// add for Quick sale begin
	else if (eleId == 'button_quick_sale') {
		var prodQtyInput = $('input[name=\'quick_sale_quantity\']');
		var prodQty = prodQtyInput.val();
		prodQty = posParseFloat(prodQty);
		// check if zero is in the text
		if (prodQty <= 0) {
			prodQtyInput.css('border', 'solid 2px #FF0000');
			prodQtyInput.attr('alt', '<?php echo $text_quantity_zero; ?>');
			prodQtyInput.attr('title', '<?php echo $text_quantity_zero; ?>');
			return false;
		} else {
			prodQtyInput.css('border', '');
			prodQtyInput.attr('alt', '');
			prodQtyInput.attr('title', '');
		}
	}
	// add for Quick sale end

	var data = {};

	$("#product input[type=\'hidden\']").each(function() {
		data[$(this).attr("name")] = $(this).val();
	});

	data['order_id'] = parseInt($('#order_id').text(), 10);
	
	var index = -1;
	// add for Inplace Pricing begin
	var org_eleId = eleId;
	if (eleId == 'inplace_pricing') {
		eleId = 'button_equal';
		index = $('#product tr').index($('input[name=order_product_id]:checked', '#order_product_list').closest('tr'));
		data['inplace_price'] = posParseFloat($('#product tr:eq('+index+')').find('td').eq(3).text());
	}
	// add for Inplace Pricing end
	if (eleId == 'button_plus' || eleId == 'button_minus' || eleId == 'button_delete' || eleId == 'button_equal') {
		index = $('#product tr').index($('input[name=order_product_id]:checked', '#order_product_list').closest('tr'));
		var indexQty = parseInt($('#product tr:eq('+index+')').find('td').eq(2).text());
		if (eleId == 'button_minus') {
			if (indexQty == 1) {
				return false;
			}
		}
		if (index >= 0 && index < $('#product tr').length -1) {
			var order_product_id = $('input[name=order_product_id]:checked').val();
			data['order_product_id'] = order_product_id;
			if (eleId == 'button_delete') {
				data['action'] = 'delete';
			} else {
				data['action'] = 'modify';
			}
			data['quantity'] = quantity;
			if (eleId == 'button_minus') {
				data['quantity'] = indexQty-1;
			} else if (eleId == 'button_plus') {
				data['quantity'] = indexQty+1;
			}
		} else {
			return false;
		}
	} else if (eleId == 'button_product') {
		formData = '#product_new input[type=\'text\'], #product_new input[type=\'hidden\'], #product_new input[type=\'radio\']:checked, #product_new input[type=\'checkbox\']:checked, #product_new select, #product_new textarea';
		$(formData).each(function() {
			data[$(this).attr('name')] = $(this).val();
		});
		data['action'] = 'insert';
	} else if (eleId == 'button_voucher') {
		formData = '#order_voucher_content input[type=\'text\'], #order_voucher_content input[type=\'hidden\'], #order_voucher_content input[type=\'radio\']:checked, #order_voucher_content input[type=\'checkbox\']:checked, #order_voucher_content select, #order_voucher_content textarea';
		data = $(formData);
		data['action'] = 'insert';
	} else if (eleId == 'button_new_order') {
		data['action'] = 'new';
		data['order_id'] = $('#order_general td:eq(1)').text();
	}
	// add for Quick sale begin
	else if (eleId == 'button_quick_sale') {
		formData = '#product_quick_sale input[type=\'text\'], #product_quick_sale input[type=\'hidden\'], #product_quick_sale input[type=\'checkbox\']:checked, #product_quick_sale select';
		$(formData).each(function() {
			var attrName = $(this).attr('name');
			attrName = attrName.replace('quick_sale_', '');
			data[attrName] = $(this).val();
		});
		data['action'] = 'insert';
		data['extra_info'] = 'quick_sale';
	}
	// add for Quick sale end

	data['store_id'] = '<?php echo $store_id; ?>';
	data['customer_id'] = $('input[name=customer_id]').val();
	data['customer_group_id'] = $('select[name=customer_group_id]').val();
	if (data['customer_id'] != '0') {
		data['customer_group_id'] = $('select[name=customer_customer_group_id]').val();
	}
	
	$.ajax({
		url: '<?php echo $store_url; ?>index.php?route=pos/checkout&token=<?php echo $token; ?>',
		type: 'post',
		data: data,
		dataType: 'json',	
		beforeSend: function() {
			removeMessage();
			showMessage('pos_attention', null, null);
		},			
		success: function(json) {
			// Check for errors
			if (json['error']) {
				removeMessage();
				if (json['error']['warning']) {
					showMessage('pos_warning', json['error']['warning'], null);
				}

				// Products
				if (json['error']['product']) {
					if (json['error']['product']['option']) {	
						for (i in json['error']['product']['option']) {
							$('#option-' + i).after('<span class="error">' + json['error']['product']['option'][i] + '</span>');
						}						
					}
					
					if (json['error']['product']['stock']) {
						showMessage('pos_warning', json['error']['product']['stock'], null);
					}	
											
					if (json['error']['product']['minimum']) {	
						for (i in json['error']['product']['minimum']) {
							showMessage('pos_warning', json['error']['product']['minimum'][i], null);
						}						
					}
				} else {
					$('input[name=\'product\']').attr('value', '');
					$('input[name=\'product_id\']').attr('value', '');
					$('#option td').remove();			
					$('input[name=\'quantity\']').attr('value', '1');
					// add for SKU begin
					$('input[name=sku]').attr('value', '');
					// add for SKU end
					// add for UPC begin
					$('input[name=upc]').attr('value', '');
					// add for UPC end
					// add for Manufacturer Product begin
					$('input[name=manufacturer]').attr('value', '');
					$('input[name=manufacturer_id]').attr('value', '0');
					// add for Manufacturer Product end
					// add for Model begin
					$('input[name=model]').attr('value', '');
					// add for Model end
					// add for Weight based price begin
					$('input[name=weight]').attr('value', '0');
					$('#input_quantity').show();
					$('#input_weight').hide();
					// add for Weight based price end
				}
				
				// Voucher
				if (json['error']['vouchers']) {
					if (json['error']['vouchers']['from_name']) {
						$('input[name=\'from_name\']').after('<span class="error">' + json['error']['vouchers']['from_name'] + '</span>');
					}	
					
					if (json['error']['vouchers']['from_email']) {
						$('input[name=\'from_email\']').after('<span class="error">' + json['error']['vouchers']['from_email'] + '</span>');
					}	
								
					if (json['error']['vouchers']['to_name']) {
						$('input[name=\'to_name\']').after('<span class="error">' + json['error']['vouchers']['to_name'] + '</span>');
					}	
					
					if (json['error']['vouchers']['to_email']) {
						$('input[name=\'to_email\']').after('<span class="error">' + json['error']['vouchers']['to_email'] + '</span>');
					}	
					
					if (json['error']['vouchers']['amount']) {
						$('input[name=\'amount\']').after('<span class="error">' + json['error']['vouchers']['amount'] + '</span>');
					}	
				} else {
					$('input[name=\'from_name\']').attr('value', '');	
					$('input[name=\'from_email\']').attr('value', '');
					$('input[name=\'to_name\']').attr('value', '');
					$('input[name=\'to_email\']').attr('value', '');
					$('textarea[name=\'message\']').attr('value', '');	
					$('input[name=\'amount\']').attr('value', '25.00');
				}
				
				// Coupon
				if (json['error']['coupon']) {
					showMessage('pos_warning', json['error']['coupon'], null);
				}
				
				// Voucher
				if (json['error']['voucher']) {
					showMessage('pos_warning', json['error']['voucher'], null);
				}
				
				// Reward Points		
				if (json['error']['reward']) {
					showMessage('pos_warning', json['error']['reward'], null);
				}
				
				deQueue();
			} else {
				$('input[name=\'product\']').attr('value', '');
				$('input[name=\'product_id\']').attr('value', '');
				$('#option td').remove();	
				$('input[name=\'quantity\']').attr('value', '1');	
				// add for SKU begin
				$('input[name=sku]').attr('value', '');
				// add for SKU end
				// add for UPC begin
				$('input[name=upc]').attr('value', '');
				// add for UPC end
				// add for Manufacturer Product begin
				$('input[name=manufacturer]').attr('value', '');
				$('input[name=manufacturer_id]').attr('value', '0');
				// add for Manufacturer Product end
				// add for Model begin
				$('input[name=model]').attr('value', '');
				// add for Model end
				// add for Weight based price begin
				$('input[name=weight]').attr('value', '0');
				$('#input_quantity').show();
				$('#input_weight').hide();
				// add for Weight based price end
				
				$('input[name=\'from_name\']').attr('value', '');	
				$('input[name=\'from_email\']').attr('value', '');	
				$('input[name=\'to_name\']').attr('value', '');
				$('input[name=\'to_email\']').attr('value', '');	
				$('textarea[name=\'message\']').attr('value', '');	
				$('input[name=\'amount\']').attr('value', '25.00');	
				// add for Quick sale begin
				$('input[name=quick_sale_name]').attr('value', '');
				$('input[name=quick_sale_model]').attr('value', '');
				$('input[name=quick_sale_price]').attr('value', '');
				$('input[name=quick_sale_quantity]').attr('value', '1');
				// add for Quick sale end
			}
			
			if (json['success'] && data['action'] == 'insert' && !json['order_product']) {
				// no product is found
				$('.pos_success, .pos_warning, .pos_attention, .error').remove();
				<?php if(isset($text_no_product)) { ?>
				showMessage('pos_warning', '<?php echo $text_no_product; ?>', null);
				<?php } ?>
			} else if (json['success']) {
				// save order
				var saveData = {};
				// add for Inplace Pricing begin
				if (org_eleId == 'inplace_pricing') {
					saveData['inplace_price'] = json['price'];
					saveData['inplace_tax'] = json['tax'];
					// update the price in case the display is for price+tax, while the price value in page is the price without tax
					applyValue(json['price']);
				}
				// add for Inplace Pricing end

				saveData['order_id'] = data['order_id'];
				if (json['order_total'] != '') {
					saveData['order_total'] = json['order_total'];
				}
				
				if (json['order_product']) {
					saveData['order_product'] = json['order_product'];
					if (saveData['order_product']['action'] == 'modify') {
						// find the quantity difference
						for (i = 0; i < $('#product tr').length-1; i++) {
							if ($('#product tr:eq('+i+')').find('input[type=\'radio\']').val() == saveData['order_product']['order_product_id']) {
								var curQuantity = parseInt($('#product tr:eq('+i+')').find('td').eq(2).text());
								saveData['order_product']['quantity_change'] = parseInt(saveData['order_product']['quantity']) - curQuantity;
								break;
							}
						}
					}
				} else {
					saveData['action'] = data['action'];
					saveData['order_product_id'] = data['order_product_id'];
					if (saveData['action'] == 'modify') {
						saveData['total'] = json['total'];
						// find the quantity difference
						var option_count = 0;

						for (i = 0; i < $('#product tr').length-1; i++) {
							if ($('#product tr:eq('+i+')').find('input[type=\'radio\']').val() == saveData['order_product_id']) {
								var curQuantity = parseInt($('#product tr:eq('+i+')').find('td').eq(2).text());
								if (eleId == 'button_plus') {
									saveData['quantity'] = curQuantity + 1;
									saveData['quantity_change'] = 1;
								} else if (eleId == 'button_minus') {
									saveData['quantity'] = curQuantity - 1;
									saveData['quantity_change'] = -1;
								} else {
									saveData['quantity'] = quantity;
									var orgQuantity = 0;
									if (orgQty) {
										orgQuantity = parseInt(orgQty);
									}
									saveData['quantity_change'] = quantity-orgQuantity;
								}
								
								saveData['product_id'] = $('#product tr:eq('+i+') input[name$=\'[product_id]\']').val();
								saveData['option'] = {};
								$('#product tr:eq('+i+') input[name$=\'[product_option_value_id]\']').each(function() {
									saveData['option'][option_count] = {};
									saveData['option'][option_count]['product_option_value_id'] = $(this).val();
									option_count++;
								});
								break;
							}
						}
					} else {
						var option_count = 0;

						for (i = 0; i < $('#product tr').length-1; i++) {
							if ($('#product tr:eq('+i+')').find('input[type=\'radio\']').val() == saveData['order_product_id']) {
								saveData['quantity'] = parseInt($('#product tr:eq('+i+')').find('td').eq(2).text());
								saveData['product_id'] = $('#product tr:eq('+i+') input[name$=\'[product_id]\']').val();
								saveData['option'] = {};
								$('#product tr:eq('+i+') input[name$=\'[product_option_value_id]\']').each(function() {
									saveData['option'][option_count] = {};
									saveData['option'][option_count]['product_option_value_id'] = $(this).val();
									option_count++;
								});
								break;
							}
						}
					}
				}
				
				// call modify order to save order
				var modifyUrl = 'index.php?route=module/pos/modifyOrder&token=<?php echo $token; ?>';
				// add for Quotation begin
				var work_mode = $('input[name=work_mode]').val();
				modifyUrl += '&work_mode=' + work_mode;
				// add for Quotation end
				// add for serial no begin
				saveData['product_sn_id'] = $('input[name=product_sn_id]').val();
				saveData['product_sn'] = $('input[name=product_sn]').val();
				// add for serial no end
				$.ajax({
					url: modifyUrl,
					type: 'post',
					data: saveData,
					dataType: 'json',
					complete: function() {
						deQueue();
					},
					success: function(save_json) {
						// add for Quick sale begin
						if (eleId == 'button_quick_sale') {
							eleId = 'button_product';
						}
						// add for Quick sale end
						// refresh order_product list and total list
						if (eleId == 'button_plus' || eleId == 'button_minus' || eleId == 'button_equal') {
							value = parseInt($('#product tr:eq('+index+')').find("td").eq(2).text());
							if (eleId == 'button_plus') value += 1;
							else if (eleId == 'button_minus') value -= 1;
							else value = quantity;
							$('#product tr:eq('+index+')').find("td").eq(2).text(''+value);
							$('#product tr:eq('+index+')').find("input[name$='[quantity]']").attr('value', value);
							$('#product tr:eq('+index+')').find("td").eq(4).text(json['total_text']);
						} else if (eleId == 'button_delete') {
							$('#product tr:eq('+index+')').remove();
							moveSelect(-1, 0);
						} else if (eleId == 'button_product') {
							if (json['order_product']) {
								if (json['order_product']['action'] == 'modify') {
									// it's actually a modification
									for (i = 0; i < $('#product tr').length-1; i++) {
										if (json['order_product']['order_product_id'] == $('#product tr:eq('+i+')').find("input[type=\'radio\']").val()) {
											// add for serial no begin
											if (save_json['product_sns']) {
												var tdHtml = $('#product tr:eq('+i+')').find("td").eq(1).html();
												tdHtml += '<br />&nbsp;<small> - SN: ' + $('input[name=product_sn]').val();
												$('#product tr:eq('+i+')').find("td").eq(1).html(tdHtml);
											}
											$('input[name=product_sn_id]').val('');
											$('input[name=product_sn]').val('');
											// add for serial no end
											$('#product tr:eq('+i+')').find("td").eq(2).text(''+json['order_product']['quantity']);
											$('#product tr:eq('+i+') input[name$=\'[quantity]\']').attr('value', json['order_product']['quantity']);
											$('#product tr:eq('+i+')').find("td").eq(4).text(''+json['order_product']['total_text']);
											break;
										}
									}
								} else {
									// append the product row
									var new_row_num = $('#product tr').length -1;
									new_row_id = 'product-row' +  new_row_num;
									html = '<tr id="' + new_row_id + '">';
									html += '<td style="text-align: center;"><input type="radio" name="order_product_id" value="' + save_json['order_product_id'] + '" />';
									html += ' <input type="hidden" name="order_product[' + new_row_num + '][order_product_id]" value="' + save_json['order_product_id'] +'" />';
									html += ' <input type="hidden" name="order_product[' + new_row_num + '][product_id]" value="' + json['order_product']['product_id'] +'" /></td>';
									html += '<td class="left">' + json['order_product']['name'];
									if (json['order_product']['option']) {
											for (i in json['order_product']['option']) {
									html +=		'<br />&nbsp;<small> - ' + json['order_product']['option'][i]['name'] + ': ' + json['order_product']['option'][i]['option_value'] + '</small>';
									html +=		' <input type="hidden" name="order_product[' + new_row_num + '][order_option][' + i + '][product_option_id]" value="' + json['order_product']['option'][i]['product_option_id'] + '" />';
									html +=		' <input type="hidden" name="order_product[' + new_row_num + '][order_option][' + i + '][product_option_value_id]" value="' + json['order_product']['option'][i]['product_option_value_id'] + '" />';
									html +=		' <input type="hidden" name="order_product[' + new_row_num + '][order_option][' + i + '][value]" value="' + json['order_product']['option'][i]['option_value'] + '" />';
									html +=		' <input type="hidden" name="order_product[' + new_row_num + '][order_option][' + i + '][type]" value="' + json['order_product']['option'][i]['type'] + '" />';
											}
									}
									// add for serial no begin
									if (save_json['product_sns']) {
										for (i in save_json['product_sns']) {
											html += '<br />&nbsp;<small> - SN: ' + save_json['product_sns'][i]['sn'];
										}
									}
									$('input[name=product_sn_id]').val('');
									$('input[name=product_sn]').val('');
									// add for serial no end
									html += '</td>';
									// html += '<td class="left">' + json['order_product']['model'] + '</td>';
									html += '<td class="right">' + json['order_product']['quantity'] + '</td>';
									html += ' <input type="hidden" name="order_product[' + new_row_num + '][quantity]" value="' + json['order_product']['quantity'] +'" />';
									html += '<td class="right" id="price_text-' + new_row_num + '">' + json['order_product']['price_text'] + '</td>';
									html += ' <input type="hidden" name="order_product[' + new_row_num + '][price]" value="' + json['order_product']['price'] +'" />';
									html += '<td class="right">' + json['order_product']['total_text'] + '</td>';
									html += '</tr>';
									$(html).insertBefore('#new_product_row');
								}
								moveSelect(-1, $('#product tr').length-1);
							}
						} else {
							moveSelect(-1, 0);
						}
						
						if (json['order_total']) {
							var total_row = 0;
							html = '';
							$("#total tr").each(function() {
								$(this).remove();
							});
							
							var total_title = '';
							var total_text = '';
							var total_value = 0;
							var subtotal_value = 0;
							var total_discount_value = 0;
							for (i = 0; i < json['order_total'].length; i++ ) {
								var total = json['order_total'][i];
								
								html += '<tr id="total-row' + total_row + '">';
								if (total['code'] == 'total') {
									html += '  <td class="center" width="60%"><span style="font: bold 15px Arial, Helvetica, sans-serif;">' + total['title'] + ':</span></td>';
									html += '  <td class="center" width="40%"><span style="font: bold 15px Arial, Helvetica, sans-serif;">' + total['text'] + '</span></td>';
									total_title = total['title'];
									total_text = total['text'];
									total_value = total['value'];
								} else {
									html += '  <td class="center" width="60%">' + total['title'] + ':</td>';
									html += '  <td class="center" width="40%">' + total['text'] + '</td>';
									if (total['code'] == 'sub_total') {
										subtotal_value = total['value'];
									}
									// add for Discount begin
									else if (total['code'] == 'pos_discount' || total['code'] == 'pos_discount_subtotal' || total['code'] == 'pos_discount_total') {
										total_discount_value = total['value'];
									}
									// add for Discount end
								}
								html += '</tr>';
								
								total_row++;
							}
							
							$('#total').html(html);
							// add for Discount begin
							$('input[name=discount_subtotal_value]').attr('value', subtotal_value);
							$('#discount_subtotal_text').text(toFixed(subtotal_value, 2));
							$('input[name=discount_total_value]').attr('value', total_value - total_discount_value);
							$('#discount_total_text').text(toFixed(total_value - total_discount_value, 2));
							calDiscount();
							// add for Discount end
							
							$('#total_tr td:eq(0) span').text(total_title+':');
							$('#total_tr td:eq(1) span').text(total_text);
						} else {
							html  = '</tr>';
							html += '  <td colspan="5" class="center"><?php echo $text_no_results; ?></td>';
							html += '</tr>';	

							$('#total').html(html);					
						}
						if (eleId != 'button_new_order') {
							calcDueAmount();
						}
						
						if (save_json['enable_openbay'] && save_json['enable_openbay'] == '1') {
							// save the product page
							url = 'index.php?route=catalog/product/update&token=<?php echo $token; ?>&product_id='+save_json['product_id'];
							$.ajax({
								url: url,
								type: 'get',
								success: function(html) {
									$('#hidden_div').html($(html).find('div[id=\'content\']').html());
									var product_change_url = $('#hidden_div').find('form[id=\'form\']').attr('action');
									var method = $('#hidden_div').find('form[id=\'form\']').attr('method');
									var product_change_data = '#hidden_div input[type=\'text\'], #hidden_div input[type=\'hidden\'], #hidden_div input[type=\'password\'], #hidden_div input[type=\'radio\']:checked, #hidden_div input[type=\'checkbox\']:checked, #hidden_div select, #hidden_div textarea';
									$.ajax({
										url: product_change_url,
										type: method,
										data: $(product_change_data),
										dataType: 'json',
										converters: {
											'text json': true
										},
										success: function(html) {
											removeMessage();
											if (save_json['success']) {
												showMessage('pos_success', save_json['success'], null);
											}
										}
									});
								}
							});
						} else {
							removeMessage();
							if (save_json['success']) {
								showMessage('pos_success', save_json['success'], null);
							}
						}
						// add for edit order address begin
						if (eleId == 'button_new_order') {
							editAddress();
						}
						// add for edit order address end
					}
				});
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
};

function toggleFullScreen() {
	$('#header').toggle();
	$('.breadcrumb').toggle();
	$('#footer').toggle();
	if (full_screen_mode == 0) {
		$('#button_full_screen').attr('src', 'view/image/pos/header_1_off.png');
		$('#button_full_screen').attr('alt', '<?php echo $button_normal_screen; ?>');
		$('#button_full_screen').attr('title', '<?php echo $button_normal_screen; ?>');
	} else {
		$('#button_full_screen').attr('src', 'view/image/pos/header_0_off.png');
		$('#button_full_screen').attr('alt', '<?php echo $button_full_screen; ?>');
		$('#button_full_screen').attr('title', '<?php echo $button_full_screen; ?>');
	}
	full_screen_mode = 1 - full_screen_mode;
};

function not_implement_yet() {
	alert("<?php echo $text_not_available; ?>");
};

$('#tendered_amount').live('keydown', function(event) {
	amountInputOnly(event);
});

function amountInputOnly(event) {
	// Allow: backspace, delete, tab, escape, and enter
	if ( event.keyCode == 46 || event.keyCode == 110 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || event.keyCode == 190 ||
		 // Allow: Ctrl+A
		(event.keyCode == 65 && event.ctrlKey === true) || 
		 // Allow: home, end, left, right
		(event.keyCode >= 35 && event.keyCode <= 39)) {
		// let it happen, don't do anything
		return;
	} else {
		// Ensure that it is a number and stop the keypress
		if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
			event.preventDefault(); 
		}
	}
};

function addPayment() {
	var amount = $('#tendered_amount').val();
	var dueAmount = $('#payment_due_amount').text();
	dueAmount = posParseFloat(dueAmount);
	if (dueAmount <= 0) {
		// nothing can be added
		return false;
	} else {
		// check if zero is in the text
		if (parseFloat(amount) == 0 && $('#payment_type').val() != 'purchase_order') {
			$('#tendered_amount').css('border', 'solid 2px #FF0000');
			$('#tendered_amount').attr('alt', '<?php echo $text_payment_zero_amount; ?>');
			$('#tendered_amount').attr('title', '<?php echo $text_payment_zero_amount; ?>');
			return false;
		} else {
			$('#tendered_amount').css('border', '');
			$('#tendered_amount').attr('alt', '');
			$('#tendered_amount').attr('title', '');
		}
	}
	// remove warning or error tips
	$('#payment_warning_tips').remove();
	$('#payment_error_tips').remove();
	
	processAddPayment(amount, '');
};

function processAddPayment(amount, noteAppend) {
	var note = $('#payment_note').val();
	if (noteAppend != '') {
		note += ' ' + noteAppend;
	}
	var order_id = parseInt($('#order_id').text(), 10);
	var type = $('#payment_type option:selected').text();
	var d = new Date();
	var order_payment_id = order_id + '_' + d.getTime();
	// add for till control begin
	var payment_type = $('#payment_type').val();
	// add for till control end
	
	var url = 'index.php?route=module/pos/addOrderPayment&token=<?php echo $token; ?>&order_payment_id='+order_payment_id+'&order_id='+order_id+'&payment_type='+type+'&payment_note='+note;
	var dueAmount = calcDueAmount();
	if (parseFloat(amount) > dueAmount) {
		url += '&tendered_amount=' + dueAmount + '&change=' + (parseFloat(amount) - dueAmount);
	} else {
		url += '&tendered_amount='+amount;
	}

	$.ajax({
		url: url,
		dataType: 'json',
		beforeSend: function() {
			$('#button_add_payment').hide();
			$('#button_add_payment').before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
		},
		complete: function() {
			$('.loading').remove();
			$('#button_add_payment').show();
		},
		success: function(json) {
			if (json['error']) {
				showMessage('pos_warning', json['error'], null);
			}
			else {
				// translate the amount to money format
				// get rid of non digital first
				amount = parseFloat(amount);
				amount = formatMoney(amount);
				var tr_element = '<tr id="' + order_payment_id +'"><td class="left" width="30%">' + type + '</td><td class="left" width="25%">' + amount + '</td><td class="left" width="35%" style="-ms-word-break: break-all; word-break: break-all; word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto;">' + note + '</td><td align="center" width="10%"><a onclick="deletePayment(\''+order_payment_id+'\');"><img src="view/image/pos/delete_off.png" width="22" height="22"/></a></td></tr>'
				$(tr_element).insertAfter('#button_add_payment_tr');
				// clear the current inputs
				var totalDue = calcDueAmount();
				// add for Print begin
				var p_payment = 0;
				if (json['p_payment']) {
					p_payment = json['p_payment'];
				}
				if (totalDue < 0.01 && p_payment) {
					// print receipt if set in the settings page
					$('#print_message').text('<?php echo $print_receipt_message; ?>');
					window_print_url('index.php?route=module/pos/receipt&token=<?php echo $token; ?>&order_id='+order_id, {'change':'1'}, afterPrintReceipt, null);
				}
				// add for Print end
				// add for till control begin
				<?php if (isset($enable_till_full_payment) && $enable_till_full_payment) { ?>
				if (payment_type == 'cash') {
					sendControlKey();
				}
				<?php } ?>
				// add for till control end
			}
			$('#payment_type option:eq(0)').attr('selected', true);
			$('#payment_type').trigger('change');
			$('#payment_note').attr('value', '');
	
			// add for Cash type begin
			useCashType = false;
			// add for Cash type end
		}
	});
}

function deletePayment(paymentId) {
	if (confirm('<?php echo $text_del_payment_confirm; ?>')) {
		$.ajax({
			url: 'index.php?route=module/pos/deleteOrderPayment&token=<?php echo $token; ?>&order_payment_id='+paymentId,
			dataType: 'json',
			beforeSend: function() {
				$(this).hide();
				$(this).before('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
			},
			complete: function() {
				$('.loading').remove();
			},
			success: function(json) {
				if (json['error']) {
					showMessage('pos_warning', json['error'], null);
				}
			}
		});
		$('#'+paymentId).remove();
		calcDueAmount();
	}
};

function calcDueAmount() {
	// count the total quantity
	var totalQuantity = 0;
	for (i = 0; i < $('#product tr').length-1; i++) {
		totalQuantity += parseInt($('#product tr:eq('+i+')').find('td').eq(2).text());
	}
	$('#items_in_cart').text(totalQuantity);
	
	var totalNum = $('#total tr').length;
	var totalAmount = 0;
	if (totalNum > 0) {
		var totalText = $('#payment_total').find('span').text();
		totalAmount = posParseFloat(totalText);
	}
	var container = document.getElementById('payment_list');
	var rows = container.getElementsByTagName('TR');
	var totalPaid = 0;
	for (i = 1; i < rows.length; i++) {
		// ignore the first line
		rowAmount = rows[i].getElementsByTagName('TD')[1].innerHTML;
		rowAmount = posParseFloat(rowAmount);
		totalPaid += rowAmount;
	}
	totalDue = totalAmount - totalPaid;
	if (totalDue < 0) {
		$('#payment_due_amount').text(formatMoney(0));
		$('#payment_change').find('span').text(formatMoney(0-totalDue));
		$('#tendered_amount').attr('value', '0');
	} else {
		$('#payment_due_amount').text(formatMoney(totalDue));
		$('#payment_change').find('span').text(formatMoney(0));
		$('#tendered_amount').attr('value', posParseFloat(formatMoney(totalDue)));
	}
	if (totalDue < 0.01) {
		// change color to green
		$('#payment_due_amount').css("color", "green");
	} else {
		// change color to red
		$('#payment_due_amount').css("color", "red");
	}
	return totalDue;
};

function formatMoney(number, places, thousand, decimal) {
	// get the currency sign
	var orderAmount = $('#payment_due_amount').text();
	var symbol_left = '';
	for (var i = 0; i < orderAmount.length; i++) {
		var symbol = orderAmount.charAt(i);
		if (symbol == '-') {
			continue;
		} else if (symbol < '0' || symbol > '9') {
			symbol_left += symbol;
		} else {
			break;
		}
	}
	var symbol_right = '';
	for (var i = orderAmount.length-1; i >= 0; i--) {
		var symbol = orderAmount.charAt(i);
		if (symbol < '0' || symbol > '9') {
			symbol_right += symbol;
		} else {
			break;
		}
	}

	number = number || 0;
	places = !isNaN(places = Math.abs(places)) ? places : 2;
	thousand = thousand || "<?php echo $text_thousand_point; ?>";
	decimal = decimal || "<?php echo $text_decimal_point; ?>";
	var negative = number < 0 ? "-" : "",
	i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
	j = (j = i.length) > 3 ? j % 3 : 0;
	return symbol_left + negative + (j ? i.substr(0, j) + thousand : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "") + symbol_right;
};

function country(element, index, zone_id) {
  if (element.value != '') {
		$.ajax({
			url: 'index.php?route=sale/customer/country&token=<?php echo $token; ?>&country_id=' + element.value,
			dataType: 'json',
			beforeSend: function() {
				$('select[name=\'customer_address[' + index + '][country_id]\']').after('<span class="wait">&nbsp;<img src="view/image/loading.gif" alt="" /></span>');
			},
			complete: function() {
				$('.wait').remove();
			},			
			success: function(json) {
				if (json['postcode_required'] == '1') {
					$('#postcode-required' + index).show();
				} else {
					$('#postcode-required' + index).hide();
				}
				
				html = '<option value=""><?php echo $text_select; ?></option>';
				
				if (json['zone'] != '') {
					for (i = 0; i < json['zone'].length; i++) {
						html += '<option value="' + json['zone'][i]['zone_id'] + '"';
						
						if (json['zone'][i]['zone_id'] == zone_id) {
							html += ' selected="selected"';
						}
		
						html += '>' + json['zone'][i]['name'] + '</option>';
					}
				} else {
					html += '<option value="0"><?php echo $text_none; ?></option>';
				}
				
				$('select[name=\'customer_address[' + index + '][zone_id]\']').html(html);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
};

function addAddress() {	
	var address_row = $('#vtabs').find('a[href^=\'#tab_address_\']').length+1;
	html  = '<div id="tab_address_' + address_row + '" class="pos_vtabs-content" style="display: none;">';
	html += '  <input type="hidden" name="customer_address[' + address_row + '][address_id]" value="" />';
	html += '  <table class="form">'; 
	html += '    <tr>';
    html += '	   <td><span class="required">*</span> <?php echo $entry_firstname; ?></td>';
    html += '	   <td><input type="text" name="customer_address[' + address_row + '][firstname]" value="" /></td>';
    html += '    </tr>';
    html += '    <tr>';
    html += '      <td><span class="required">*</span> <?php echo $entry_lastname; ?></td>';
    html += '      <td><input type="text" name="customer_address[' + address_row + '][lastname]" value="" /></td>';
    html += '    </tr>';
    html += '    <tr>';
    html += '      <td><?php echo $entry_company; ?></td>';
    html += '      <td><input type="text" name="customer_address[' + address_row + '][company]" value="" /></td>';
    html += '    </tr>';	
    html += '    <tr class="customer-company-id-display">';
    html += '      <td><?php echo $entry_company_id; ?></td>';
    html += '      <td><input type="text" name="customer_address[' + address_row + '][company_id]" value="" /></td>';
    html += '    </tr>';
    html += '    <tr class="customer-tax-id-display">';
    html += '      <td><?php echo $entry_tax_id; ?></td>';
    html += '      <td><input type="text" name="customer_address[' + address_row + '][tax_id]" value="" /></td>';
    html += '    </tr>';			
    html += '    <tr>';
    html += '      <td><span class="required">*</span> <?php echo $entry_address_1; ?></td>';
    html += '      <td><input type="text" name="customer_address[' + address_row + '][address_1]" value="" /></td>';
    html += '    </tr>';
    html += '    <tr>';
    html += '      <td><?php echo $entry_address_2; ?></td>';
    html += '      <td><input type="text" name="customer_address[' + address_row + '][address_2]" value="" /></td>';
    html += '    </tr>';
    html += '    <tr>';
    html += '      <td><span class="required">*</span> <?php echo $entry_city; ?></td>';
    html += '      <td><input type="text" name="customer_address[' + address_row + '][city]" value="" /></td>';
    html += '    </tr>';
    html += '    <tr>';
    html += '      <td><span id="postcode_required' + address_row + '" class="required">*</span> <?php echo $entry_postcode; ?></td>';
    html += '      <td><input type="text" name="customer_address[' + address_row + '][postcode]" value="" /></td>';
    html += '    </tr>';
	html += '    <tr>';
    html += '      <td><span class="required">*</span> <?php echo $entry_country; ?></td>';
    html += '      <td><select name="customer_address[' + address_row + '][country_id]" onchange="country(this, \'' + address_row + '\', \'0\');">';
    html += '         <option value=""><?php echo $text_select; ?></option>';
    <?php 
		if (isset($customer_countries)) {
			foreach ($customer_countries as $customer_country) { ?>
    html += '         <option value="<?php echo $customer_country['country_id']; ?>"><?php echo addslashes($customer_country['name']); ?></option>';
    <?php }} ?>
    html += '      </select></td>';
    html += '    </tr>';
    html += '    <tr>';
    html += '      <td><span class="required">*</span> <?php echo $entry_zone; ?></td>';
    html += '      <td><select name="customer_address[' + address_row + '][zone_id]"><option value="false"><?php echo $this->language->get('text_none'); ?></option></select></td>';
    html += '    </tr>';
	html += '    <tr>';
    html += '      <td><?php echo $entry_default; ?></td>';
    html += '      <td><input type="radio" name="customer_address[' + address_row + '][default]" value="1" /></td>';
    html += '    </tr>';
    html += '  </table>';
    html += '</div>';
	
	$('#customer_customer').append(html);
	
	$('select[name=\'customer_address[' + address_row + '][country_id]\']').trigger('change');	
	
	$('#address_add').before('<a href="#tab_address_' + address_row + '" id="address_' + address_row + '"><?php echo $tab_address; ?> ' + address_row + '&nbsp;<img src="view/image/delete.png" alt="" onclick="$(\'#vtabs a:first\').trigger(\'click\'); $(\'#address_' + address_row + '\').remove(); $(\'#tab_address_' + address_row + '\').remove(); return false;" /></a>');
		 
	$('.pos_vtabs a').tabs();
	
	$('#address_' + address_row).trigger('click');
	
	address_row++;
};

$('select[name$=\'[country_id]\']').trigger('change');
$('.pos_vtabs a').tabs();

function updateClock() {
	var currentTime = new Date ( );

	var currentHours = currentTime.getHours();
	var currentMinutes = currentTime.getMinutes();
	currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
	var timeOfDay = ( currentHours < 12 ) ? "am" : "pm";
	currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;
	currentHours = ( currentHours == 0 ) ? 12 : currentHours;
	var currentDate = currentTime.getDate();
	currentDate = ( currentDate < 10 ? "0" : "" ) + currentDate;
	var currentMonth = currentTime.getMonth();
	var month_names = [];
<?php
	for ($i = 0; $i < count($text_months); $i++) {
?>
		month_names[<?php echo $i; ?>] = '<?php echo $text_months[$i]; ?>';
<?php
	}
?>
	var month_name = month_names[currentMonth];
	var currentYear = currentTime.getFullYear();
	var currentDay = currentTime.getDay();
	var week_days = [];
<?php
	for ($i = 0; $i < count($text_weeks); $i++) {
?>
		week_days[<?php echo $i; ?>] = '<?php echo $text_weeks[$i]; ?>';
<?php
	}
?>
	var week_day_name = week_days[currentDay];
	
	$('#header_year').text(currentYear);
	$('#header_month').text(month_name);
	$('#header_date').text(currentDate);
	$('#header_week').text(week_day_name);
	$('#header_hour').text(currentHours);
	$('#header_minute').text(currentMinutes);
	$('#header_apm').text(timeOfDay);
};

$('img').live('mouseenter', function() {
	var imgSrc = $(this).attr('src');
	if (imgSrc.indexOf('_off.png') >= 0) {
		$(this).attr('src', imgSrc.replace('_off.png', '_on.png'));
	}
});

$('img').live('mouseleave', function() {
	var imgSrc = $(this).attr('src');
	if (imgSrc.indexOf('_on.png') >= 0) {
		$(this).attr('src', imgSrc.replace('_on.png', '_off.png'));
	}
});

$(function() {
	updateClock();
	setInterval(updateClock,1000);
});

var resizeTimer;
$(window).resize(function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(CheckSizeZoom, 100);
});

function CheckSizeZoom() {
	if ($(window).width() > 1024) {
		/*
		var zoomLev = $(window).width() / 1080;
		if (720 * zoomLev > $(window).height() && $(window).height() / 720 > 1) {
			zoomLev = $(window).height() / 720;
		}
		
		if ($(window).width() > 1024 && $(window).height() > 680) {
			if (typeof (document.body.style.zoom) != "undefined" && !$.browser.msie) {
				$(document.body).css('zoom', zoomLev);
			}
		}
		*/
		
		$('#divWrap').css('margin', '0 auto');
	} else {
		$(document.body).css('zoom', '');
		$('#divWrap').css('margin', '');
	}
};

function window_print_url(url, data, fn, para) {
	// get the page from url and print it
	if (data['change']) {
		// get the change if there is any
		var change = $('#payment_change').find('span').text();
		change = posParseFloat(change);
		if (change < 0.01) {
			data['change'] = formatMoney(0);
		} else {
			data['change'] = formatMoney(change);
		}
	}
	$.ajax({
		url: url,
		type: 'post',
		data: data,
		dataType: 'json',
		beforeSend: function() {
			$('#pos_print').dialog('open');
		},
		converters: {
			'text json': true
		},
		success: function(html) {
			// send html to iframe for printing
			$('#print_iframe').contents().find('html').html(html);

			setTimeout(function() {
				// append the print script
				if ( $.browser.msie ) {
					$("#print_iframe").get(0).contentWindow.document.execCommand('print', false, null);
				} else {
					$("#print_iframe").get(0).contentWindow.print();
				}
				// call the function to continue
				if (fn) {
					fn(para);
				}
			}, 1000);
		}
	});
};

// add for Print begin
$('#pos_print').dialog({
	autoOpen: false,
	height: 100,
	modal: true
});
// add for Print end

// add for print invoice begin
function printInvoice() {
	// print the invoice
	var order_id = parseInt($('#order_id').text(), 10);
	$('#print_message').text('<?php echo $print_invoice_message; ?>');
	var url = 'index.php?route=sale/order/invoice&token=<?php echo $token; ?>&order_id='+order_id;
	// add for Quotation begin
	var work_mode = $('input[name=work_mode]').val();
	url += '&work_mode=' + work_mode;
	// add for Quotation end
	window_print_url(url, {}, afterPrintReceipt, null);
};
// add for print invoice end
// add for Discount begin
$('#button_discount_apply').live('click', function() {
	var ret = applyDiscount();
	// add for Maximum Discount begin
	if (ret != 'continue') {
	// add for Maximum Discount end
	moveSelect(-1, $('#product tr').length-1);
	// add for Maximum Discount begin
	}
	// add for Maximum Discount end
});

function toFixed(num, fixed) {
	return (Math.round(parseFloat(num) * Math.pow(10, fixed)) / Math.pow(10, fixed)).toFixed(fixed);
};

function calDiscount() {
	if (parseFloat($('input[name=discount_subtotal_value]').val()) == 0) return;
	
	var discount_amount = parseFloat($('input[name=discount_amount_value]').val());
	if (isNaN(discount_amount)) {
		discount_amount = 0;
		$('input[name=discount_amount_value]').attr('value', discount_amount);
	} else if (discount_amount < 0) {
		discount_amount = 0-discount_amount;
		$('input[name=discount_amount_value]').attr('value', discount_amount);
	}
	
	var discount_percentage = parseFloat($('input[name=discount_percentage_value]').val());
	if (isNaN(discount_percentage)) {
		discount_percentage = 0;
		$('input[name=discount_percentage_value]').attr('value', discount_percentage);
	}
	
	var discounted = 0;
	if ($('input[name=discount_type]:checked').val() == 'amount') {
		discounted = discount_amount;
		if (discounted > parseFloat($('input[name=discount_total_value]').val())) {
			discounted = parseFloat($('input[name=discount_total_value]').val())
		}
		
		if ($('input[name=discount_total_type]:checked').val() == 'subtotal') {
			discount_percentage = toFixed(discounted * 100 / parseFloat($('input[name=discount_subtotal_value]').val()), 2);
		} else if ($('input[name=discount_total_type]:checked').val() == 'total') {
			discount_percentage = toFixed(discounted * 100 / parseFloat($('input[name=discount_total_value]').val()), 2);
		} else {
			$('input[name=discount_total_type][value=subtotal]').attr('checked', 'checked');
			discount_percentage = toFixed(discounted * 100 / parseFloat($('input[name=discount_subtotal_value]').val()), 2);
		}

		$('input[name=discount_percentage_value]').attr('value', discount_percentage);
	} else if ($('input[name=discount_type]:checked').val() == 'percentage') {
		if (discount_percentage > 100) {
			discount_percentage = 100;
		}
		if ($('input[name=discount_total_type]:checked').val() == 'subtotal') {
			discounted = discount_percentage * parseFloat($('input[name=discount_subtotal_value]').val()) / 100;
		} else if ($('input[name=discount_total_type]:checked').val() == 'total') {
			discounted = discount_percentage * parseFloat($('input[name=discount_total_value]').val()) / 100;
		}
		$('input[name=discount_amount_value]').attr('value', toFixed(discounted, 2));
	}
	discounted = toFixed((parseFloat($('input[name=discount_total_value]').val())-discounted), 2);
	
	$('#discounted_value').text(discounted);
};

$('input[name=discount_amount_value]').live('keydown', function(event) {
	amountInputOnly(event);
});
$('input[name=discount_amount_value]').live('keyup', function(event) {
	$('input[name=discount_type][value=amount]').attr('checked', 'checked');
	calDiscount();
});

$('input[name=discount_percentage_value]').live('keydown', function(event) {
	amountInputOnly(event);
});
$('input[name=discount_percentage_value]').live('keyup', function(event) {
	$('input[name=discount_type][value=percentage]').attr('checked', 'checked');
	calDiscount();
});

$('input[name=discount_type]').live('change', function(event) {
	calDiscount();
});
$('input[name=discount_total_type]').live('change', function(event) {
	$('input[name=discount_type][value=percentage]').attr('checked', 'checked');
	calDiscount();
});

function applyDiscount() {
	// add for Maximum Discount begin
	// before apply the discount, check the given discount against the discount limit
	var max_discount_fixed = parseFloat($('input[name=max_discount_fixed]').val());
	var max_discount_percentage = parseFloat($('input[name=max_discount_percentage]').val());
	var cur_discount_fixed = parseFloat($('input[name=discount_amount_value]').val());
	var cur_discount_percentage = parseFloat($('input[name=discount_percentage_value]').val());
	if ((max_discount_fixed > 0 && cur_discount_fixed > max_discount_fixed) ||
		(max_discount_percentage > 0 && cur_discount_percentage > max_discount_percentage)) {
		alert('The fixed discount limit is ' + max_discount_fixed + ' and the discount percentage limit is ' + max_discount_percentage + '%. \nPlease make sure the discount given does not exceed the limit.');
		return 'continue';
	}
	// add for Maximum Discount end
	// provide order_id, code, title and value
	var data = {};
	
	data['order_id'] = parseInt($('#order_id').text(), 10);
	data['code'] = 'pos_discount';
	data['title'] = '<?php echo $text_discount; ?> (' + $('input[name=discount_amount_value]').val() + ')';
	data['value'] = 0-parseFloat($('input[name=discount_amount_value]').val());
	data['total'] = posParseFloat($('#total_tr td:eq(1) span').text());
	if ($('input[name=discount_type]:checked').val() == 'percentage') {
		if ($('input[name=discount_total_type]:checked').val() == 'subtotal') {
			data['code'] = 'pos_discount_subtotal';
		} else {
			data['code'] = 'pos_discount_total';
		}
		var percentage = toFixed(parseFloat($('input[name=discount_percentage_value]').val()), 2);
		if (percentage > 100) {
			percentage = 100;
		}
		data['title'] = '<?php echo $text_discount; ?> (' + percentage + '%)';
	}
	// call apply discount operation
	$.ajax({
		url: 'index.php?route=module/pos/applyDiscount&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			removeMessage();
			showMessage('pos_attention', '<?php echo $text_apply_discount; ?>', 'view/image/loading.gif');
		},
		success: function(json) {
			removeMessage();
			if (json['error']) {
				showMessage('pos_warning', json['error'], null);
			} else {
				// update total html
				updateTotal(json['totals']);
				showMessage('pos_success', json['success'], null);
			}			
		}
	});
};

function updateTotal(totals) {
	// update total from the discount value
	var total_row = 0;
	html = '';
	$("#total tr").each(function() {
		$(this).remove();
	});
	
	var total_title = '';
	var total_text = '';
	for (i = 0; i < totals.length; i++ ) {
		var total = totals[i];
		
		html += '<tr id="total-row' + total_row + '">';
		if (total['code'] == 'total') {
			html += '  <td class="center" width="60%"><span style="font: bold 15px Arial, Helvetica, sans-serif;">' + total['title'] + ':</span></td>';
			html += '  <td class="center" width="40%"><span style="font: bold 15px Arial, Helvetica, sans-serif;">' + total['text'] + '</span></td>';
			total_title = total['title'];
			total_text = total['text'];
		} else {
			html += '  <td class="center" width="60%">' + total['title'] + ':</td>';
			html += '  <td class="center" width="40%">' + total['text'] + '</td>';
		}
		html += '</tr>';
		
		total_row++;
	}
	
	$('#total').html(html);
	
	$('#total_tr td:eq(0) span').text(total_title);
	$('#total_tr td:eq(1) span').text(total_text);
	
	// recalculate the due amount
	calcDueAmount();
};
// add for Discount end
// add for Inplace Price begin, add for Inplace Quantity begin (in)
$('#product tr td').live('click', function() {

	var enable_inplace_pricing = $('input[name=enable_inplace_pricing]').val();

	var index = $(this).closest('tr').find('td').index($(this));
	if (index > 1 && $(this).find('input').length > 0) return;

	if (index == 3 && enable_inplace_pricing && enable_inplace_pricing != '0') {
		var orgText = $(this).text();
		var text = posParseFloat($(this).text());
		$(this).text('');
		$('<input size="5" name="temp_input_price" class="onenter onblur"/>').appendTo($(this));
		enterFns['temp_input_price'] = 'handleInplacePrice';
		$('.onenter').val(text).select().keyup(function(e) {
			if (e.keyCode == 13) {
				handleInplacePrice(this);
			}
		});
		$('.onblur').val(text).select().blur(function() {
			lastInput = this;
			setTimeout(function() {
				if (!keyboardClick) {
					$('input[name=temp_input_price]').parent().text(orgText);
					$('input[name=temp_input_price]').remove();
				}
			}, 30);
		});
	// add for Inline Quantity begin
	} else if (index == 2) {
		var orgText = $(this).text();
		var text = posParseFloat($(this).text());
		$(this).text('');
		$('<input size="5" name="temp_input_quantity" class="onenter onblur"/>').appendTo($(this));
		enterFns['temp_input_quantity'] = 'handleInplaceQuantity';
		$('.onenter').val(text).select().keyup(function(e) {
			if (e.keyCode == 13) {
				handleInplaceQuantity(this, orgText);
			}
		});
		$('.onblur').val(text).select().blur(function() {
			lastInput = this;
			setTimeout(function() {
				if (!keyboardClick) {
					$('input[name=temp_input_quantity]').parent().text(orgText);
					$('input[name=temp_input_quantity]').remove();
				}
			}, 80);
		});
	// add for Inline Quantity end
	}
});

function handleInplacePrice(input) {
	var newPrice = $(input).val();
	var parent = $(input).parent();
	$(input).parent().find('input').remove();
	if (newPrice && newPrice != '') {
		// move this logic after check out
		// applyValue(newPrice, $(this).parent().attr('id'));
		parent.text(formatMoney(newPrice));
		// update total
		sendInplacePrice();
	}
};

function handleInplaceQuantity(input, orgQty) {
	var newQuantity = $(input).val();
	var parent = $(input).parent();
	$(input).parent().find('input').remove();
	if (newQuantity && newQuantity != '') {
		newQuantity = parseInt(newQuantity);
		parent.text(newQuantity);
		// change quantity
		checkAndSaveOrder('button_equal', newQuantity, orgQty);
	} else {
		parent.text(orgQty);
	}
};

function applyValue(newPrice) {
	var trIndex = $('#product tr').index($('input[name=order_product_id]:checked', '#order_product_list').closest('tr'));
	var tdId = $('#product tr:eq('+trIndex+')').find('td').eq(3).attr('id');
	var index = tdId.indexOf('-');
	if (index >= 0) {
		var row_no = tdId.substr(index+1);
		$('input[name=\'order_product['+row_no+'][price]\']').val(newPrice);
	}
};

function sendInplacePrice() {
	var index = $('#product tr').index($('input[name=order_product_id]:checked', '#order_product_list').closest('tr'));
	var indexQty = parseInt($('#product tr:eq('+index+')').find('td').eq(2).text());
	checkAndSaveOrder('inplace_pricing', indexQty);
};
// add for Inline Price end, add for Inline Quantity end
// add for Add Customer begin
function addCustomer() {
	$.ajax({
		url: 'index.php?route=module/pos/createEmptyCustomer&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			removeMessage();
			showMessage('pos_attention', null, null);
		},
		success: function(json) {
			removeMessage();

			$('#tab_customers').trigger('click');
			$('#order_customer').css('display', 'none');
			$('#customer_customer').css('display', 'block');

			// switch customer if customer_id is not 0
			if (json['customer_info']) {
				$('#vtabs').empty();
				$('#vtabs').append('<a href="#tab_customer"><?php echo $tab_general; ?></a>');
				i = 1;
				if (json['customer_addresses']) {
					for (var row_index in json['customer_addresses']) {
						$('#vtabs').append('<a href="#tab_address_'+i+'" id="address_'+i+'"><?php echo $tab_address; ?>'+i+'&nbsp;<img src="view/image/delete.png" alt="" onclick="$(\'#vtabs a:first\').trigger(\'click\'); $(\'#address_'+i+'\').remove(); $(\'#tab_address_'+i+'\').remove(); return false;" /></a>');
						i ++;
					}
				}
				$('#vtabs').append('<span id="address_add"><?php echo $button_add_address; ?>&nbsp;<img src="view/image/add.png" alt="" onclick="addAddress();" /></span>');
				$('input[name=\'customer_customer_id\']').attr('value', json['customer_info']['customer_id']);
				$('input[name=\'customer_firstname\']').attr('value', json['customer_info']['firstname']);
				$('input[name=\'customer_lastname\']').attr('value', json['customer_info']['lastname']);
				$('input[name=\'customer_email\']').attr('value', json['customer_info']['email']);
				$('input[name=\'customer_telephone\']').attr('value', json['customer_info']['telephone']);
				$('input[name=\'customer_password\']').attr('value', json['customer_info']['password']);
				$('input[name=\'customer_confirm\']').attr('value', json['customer_info']['password']);
				$('input[name=\'customer_fax\']').attr('value', json['customer_info']['fax']);
				$('select[name=\'customer_newsletter\']').attr('value', json['customer_info']['newsletter']);
				$('select[name=\'customer_newsletter\']').change();
				if (json['country_id']) {
					$('input[name=shipping_country_id]').attr('value', json['country_id']);
					$('input[name=payment_country_id]').attr('value', json['country_id']);
				}
				if (json['zone_id']) {
					$('input[name=shipping_zone_id]').attr('value', json['zone_id']);
					$('input[name=payment_zone_id]').attr('value', json['zone_id']);
				}
				$('select[name=\'customer_customer_group_id\']').attr('value', json['customer_info']['customer_group_id']);
				$('select[name=\'customer_customer_group_id\']').change();
				$('select[name=\'customer_status\']').attr('value', json['customer_info']['status']);
				$('select[name=\'customer_status\']').change();
				$('div[id^=\'tab_address_\']').remove();
				
				$('.pos_vtabs a').tabs();
				$('select[name=\'customer_customer_group_id\']').change();
			}
		}
	});
}
// add for Add Customer end
// add for Authorize.Net CIM begin, add for Purchase Order Payment begin, add for Cash type begin, add for Credit Card begin
$('#payment_type').live('change', function() {
	// add for Purchase Order Payment begin
	if ($('#payment_type').val() == 'purchase_order') {
		$('#payment_note_text').text('<?php echo $text_purchase_order_number; ?>');
	} else {
		$('#payment_note_text').text('<?php echo $column_payment_note; ?>');
	}
	// add for Purchase Order Payment end
	
	// add for Cash type begin
	if ($('#payment_type').val() == 'cash') {
		$('table[id^=cash_type_list]').show();
	} else {
		$('table[id^=cash_type_list]').hide();
	}
	useCashType = false;
	// add for Cash type end
	
	calcDueAmount();
});
// add for Authorize.Net CIM end, add for Purchase Order Payment end, add for Cash type end, add for Credit card end
// add for edit order address begin
function order_country(element, address_type, zone_id) {
  if (element.value != '') {
		$.ajax({
			url: 'index.php?route=sale/customer/country&token=<?php echo $token; ?>&country_id=' + element.value,
			dataType: 'json',
			beforeSend: function() {
				$(element).after('<span class="wait">&nbsp;<img src="view/image/loading.gif" alt="" /></span>');
			},
			complete: function() {
				$('.wait').remove();
			},			
			success: function(json) {
				if (json['postcode_required'] == '1') {
					$('#' + address_type + '-postcode-required').show();
				} else {
					$('#' + address_type + '-postcode-required').hide();
				}
				
				html = '<option value=""><?php echo $text_select; ?></option>';
				
				if (json['zone'] != '') {
					for (i = 0; i < json['zone'].length; i++) {
						html += '<option value="' + json['zone'][i]['zone_id'] + '"';
						
						if (json['zone'][i]['zone_id'] == zone_id) {
							html += ' selected="selected"';
						}
		
						html += '>' + json['zone'][i]['name'] + '</option>';
					}
				} else {
					html += '<option value="0"><?php echo $text_none; ?></option>';
				}
				
				$('select[name=\'' + address_type + '_zone_id\']').html(html);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
};

function editAddress() {
	var addrData = '#order_addresses input[type=\'text\'], #order_addresses input[type=\'hidden\'], #order_addresses select';
	
	$.ajax({
		url: '<?php echo $store_url; ?>index.php?route=pos/shipping&token=<?php echo $token; ?>',
		type: 'post',
		data: $(addrData),
		dataType: 'json',	
		beforeSend: function() {
			// removeMessage();
			// showMessage('pos_attention', null, null);
		},			
		success: function(json) {
			// removeMessage();
			if (json['shipping_method']) {
				html = '<option value=""><?php echo $text_select; ?></option>';

				for (i in json['shipping_method']) {
					html += '<optgroup label="' + json['shipping_method'][i]['title'] + '">';
				
					if (!json['shipping_method'][i]['error']) {
						for (j in json['shipping_method'][i]['quote']) {
							if (json['shipping_method'][i]['quote'][j]['code'] == $('input[name=\'shipping_code\']').attr('value')) {
								html += '<option value="' + json['shipping_method'][i]['quote'][j]['code'] + '" selected="selected">' + json['shipping_method'][i]['quote'][j]['title'] + '</option>';
							} else {
								html += '<option value="' + json['shipping_method'][i]['quote'][j]['code'] + '">' + json['shipping_method'][i]['quote'][j]['title'] + '</option>';
							}
						}		
					} else {
						html += '<option value="" style="color: #F00;" disabled="disabled">' + json['shipping_method'][i]['error'] + '</option>';
					}
					
					html += '</optgroup>';
				}
		
				$('select[name=\'shipping\']').html(html);	
				
				if ($('select[name=\'shipping\'] option:selected').attr('value')) {
					$('input[name=\'shipping_method\']').attr('value', $('select[name=\'shipping\'] option:selected').text());
				} else {
					$('input[name=\'shipping_method\']').attr('value', '');
				}
				
				$('input[name=\'shipping_code\']').attr('value', $('select[name=\'shipping\'] option:selected').attr('value'));	
			}
		}
	});
};

$('select[name=shipping]').live('change', function() {
	if (this.value) {
		$('input[name=shipping_method]').attr('value', $('select[name=shipping] option:selected').text());
	} else {
		$('input[name=shipping_method]').attr('value', '');
	}
	
	$('input[name=shipping_code]').attr('value', this.value);
});

$('select[name=\'shipping_address\']').live('change', function() {
	$.ajax({
		url: 'index.php?route=sale/customer/address&token=<?php echo $token; ?>&address_id=' + this.value,
		dataType: 'json',
		success: function(json) {
			if (json != '') {	
				$('input[name=\'shipping_firstname\']').attr('value', json['firstname']);
				$('input[name=\'shipping_lastname\']').attr('value', json['lastname']);
				$('input[name=\'shipping_company\']').attr('value', json['company']);
				$('input[name=\'shipping_address_1\']').attr('value', json['address_1']);
				$('input[name=\'shipping_address_2\']').attr('value', json['address_2']);
				$('input[name=\'shipping_city\']').attr('value', json['city']);
				$('input[name=\'shipping_postcode\']').attr('value', json['postcode']);
				$('select[name=\'shipping_[country_id]\']').attr('value', json['country_id']);
				
				shipping_zone_id = json['zone_id'];
				
				order_country($('select[name=\'shipping_[country_id]\']').get(0), 'shipping', shipping_zone_id);
			}
		}
	});	
});

$('select[name=\'payment_address\']').live('change', function() {
	$.ajax({
		url: 'index.php?route=sale/customer/address&token=<?php echo $token; ?>&address_id=' + this.value,
		dataType: 'json',
		success: function(json) {
			if (json != '') {	
				$('input[name=\'payment_firstname\']').attr('value', json['firstname']);
				$('input[name=\'payment_lastname\']').attr('value', json['lastname']);
				$('input[name=\'payment_company\']').attr('value', json['company']);
				$('input[name=\'payment_company_id\']').attr('value', json['company_id']);
				$('input[name=\'payment_tax_id\']').attr('value', json['tax_id']);
				$('input[name=\'payment_address_1\']').attr('value', json['address_1']);
				$('input[name=\'payment_address_2\']').attr('value', json['address_2']);
				$('input[name=\'payment_city\']').attr('value', json['city']);
				$('input[name=\'payment_postcode\']').attr('value', json['postcode']);
				$('select[name=\'payment_[country_id]\']').attr('value', json['country_id']);
				
				payment_zone_id = json['zone_id'];
				
				order_country($('select[name=\'payment_[country_id]\']').get(0), 'payment', payment_zone_id);
			}
		}
	});	
});

$('#button_order_address_save').live('click', function() {
	var order_id = parseInt($('#order_id').text(), 10);
	// save shipping and payment address to the order
	var addrData = '#order_addresses input[type=\'text\'], #order_addresses input[type=\'hidden\'], #order_addresses select';
	// the add customer button was pressed
	$.ajax({
		url: 'index.php?route=module/pos/saveOrderAddresses&token=<?php echo $token; ?>&order_id=' + order_id,
		type: 'post',
		dataType: 'json',
		data: $(addrData),
		beforeSend: function() {
			removeMessage();
			showMessage('pos_attention', null, null);
		},
		complete: function() {
			removeMessage();
		},
		success: function(json) {
			// save the payment method session data
			$.ajax({
				url: '<?php echo $store_url; ?>index.php?route=pos/shipping&token=<?php echo $token; ?>',
				type: 'post',
				data: $(addrData),
				dataType: 'json',	
				success: function(json) {
					// if the order does have products added, update the total
					if ($('#product tr').length > 1) {
						// move to the first row and modify the quantity to the same as the current quantity
						var indexChecked = $('#product tr').index($('input[name=order_product_id]:checked', '#product').closest('tr'));
						moveSelect(indexChecked, 0);
						var indexQty = parseInt($('#product tr:eq(0)').find('td').eq(2).text());
						checkAndSaveOrder('inplace_pricing', indexQty);
					}
				}
			});
		}
	});
});

$('#button_order_address_cancel').live('click', function() {
	moveSelect(-1, $('#product tr').length-1);
});
// add for edit order address end
// add for Quotation begin
function modeOrder() {
	// set the order mode
	$('#mode_order_img').attr('src', 'view/image/pos/order_1.png');
	$('#mode_return_img').attr('src', 'view/image/pos/return_0.png');
	$('#mode_quote_img').attr('src', 'view/image/pos/quote_0.png');
	$('input[name=work_mode]').attr('value', '0');
	$('#img_new_order').attr('alt', '<?php echo $button_new_order; ?>');
	$('#img_new_order').attr('title', '<?php echo $button_new_order; ?>');
	$('#img_existing_orders').attr('alt', '<?php echo $button_existing_order; ?>');
	$('#img_existing_orders').attr('title', '<?php echo $button_existing_order; ?>');
	$('#img_complete_order').attr('alt', '<?php echo $button_complete_order; ?>');
	$('#img_complete_order').attr('title', '<?php echo $button_complete_order; ?>');
};

function modeReturn() {
	not_implement_yet();
};

function modeQuote() {
	// set the quote mode
	$('#mode_order_img').attr('src', 'view/image/pos/order_0.png');
	$('#mode_return_img').attr('src', 'view/image/pos/return_0.png');
	$('#mode_quote_img').attr('src', 'view/image/pos/quote_1.png');
	$('input[name=work_mode]').attr('value', '2');
	$('#img_new_order').attr('alt', '<?php echo $text_new_quote; ?>');
	$('#img_new_order').attr('title', '<?php echo $text_new_quote; ?>');
	$('#img_existing_orders').attr('alt', '<?php echo $text_existing_quotes; ?>');
	$('#img_existing_orders').attr('title', '<?php echo $text_existing_quotes; ?>');
	$('#img_complete_order').attr('alt', '<?php echo $text_convert_to_order; ?>');
	$('#img_complete_order').attr('title', '<?php echo $text_convert_to_order; ?>');
};
// add for Quotation end

function posParseFloat(floatstring) {
	// to take care of different culture with the formatted currency string
	// convert to general thousand point (,) and decimal point (.)
	var fString = ''+floatstring;
	if ('<?php echo $text_thousand_point; ?>' != ',' || '<?php echo $text_decimal_point; ?>' != '.') {
		fString = fString.replace('<?php echo $text_thousand_point; ?>', '#tp#');
		fString = fString.replace('<?php echo $text_decimal_point; ?>', '.');
		fString = fString.replace('#tp#', ',');
	}
	
	return parseFloat(fString.replace(/[^0-9-.]/g, ''));
};

// add for Quick sale begin
$('input[name=\'quick_sale_name\']').live('focus', function(){
	$(this).autocomplete({
		delay: 500,
		source: function(request, response) {
			var url = 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' + encodeURIComponent(request.term) + '&quick_sale=1';
			$.ajax({
				url: url,
				dataType: 'json',
				success: function(json) {	
					response($.map(json, function(item) {
						return {
							label: item.name,
							value: item.product_id,
							model: item.model,
							price: item.price,
							shipping: item.shipping,
							tax_class_id: item.tax_class_id
						}
					}));
				}
			});
		}, 
		select: function(event, ui) {
			$('input[name=quick_sale_name]').attr('value', ui.item['label']);
			$('input[name=quick_sale_product_id]').attr('value', ui.item['value']);
			$('input[name=quick_sale_model]').attr('value', ui.item['model']);
			$('input[name=quick_sale_price]').attr('value', ui.item['price']);
			$('input[name=quick_sale_shipping]').attr('value', ui.item['shipping']);
			if (ui.item['shipping'] == 1) {
				$('input[name=quick_sale_shipping]').attr('checked', true);
			} else {
				$('input[name=quick_sale_shipping]').attr('checked', false);
			}
			$('select[name=quick_sale_tax_class_id]').attr('value', ui.item['tax_class_id']);
			$('select[name=quick_sale_tax_class_id]').trigger('change');
			// the price read from the table does not include the price
			$('input[name=quick_sale_include_tax]').attr('value', '0');
			$('input[name=quick_sale_include_tax]').attr('checked', false);
			return false;
		},
		focus: function(event, ui) {
			return false;
		}
	});
});

$('input[name=\'quick_sale_name\']').live('keydown', function(event) {
	// once enter something in the name field, reset the product_id to 0, which can allow the controller know it's a new product
	$('input[name=quick_sale_product_id]').attr('value', '0');
});

$('input[name=quick_sale_price]').live('keydown', function(event) {
	amountInputOnly(event);
});

$('input[name=quick_sale_quantity]').live('keydown', function(event) {
	amountInputOnly(event);
});

$('select[name=quick_sale_tax_class_id]').live('change', function(event) {
	if ($(this).val() == '0') {
		// no tax is required
		$('input[name=quick_sale_include_tax]').attr('checked', false);
		$('input[name=quick_sale_include_tax]').attr('disabled', true);
	} else {
		$('input[name=quick_sale_include_tax]').attr('disabled', false);
	}
});

$('input[name=quick_sale_include_tax], input[name=quick_sale_shipping]').live('click', function() {
	if ($(this).is(':checked')) {
		$(this).attr('value', '1');
	} else {
		$(this).attr('value', '0');
	}
});

$('#button_quick_sale').live('click', function() {
	preTabId = 'tab_quick_sale';
	var addrData = '#product_quick_sale input[type=\'text\'], #product_quick_sale input[type=\'hidden\'], #product_quick_sale input[type=\'checkbox\'], #product_quick_sale select';
	// the add quick sale product button was pressed, add/update product to the database first
	$.ajax({
		url: 'index.php?route=module/pos/updateQSProduct&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		data: $(addrData),
		beforeSend: function() {
			removeMessage();
			showMessage('pos_attention', null, null);
		},
		complete: function() {
			removeMessage();
		},
		success: function(json) {
			if (json['product_id']) {
				// add the product to database successfully
				$('input[name=quick_sale_product_id]').attr('value', json['product_id']);
				checkAndSaveOrder('button_quick_sale', 0);
			}
		}
	});
});
// add for Quick sale end
// add for Browse begin
function showCategoryItems(category_id) {
	var data = {'category_id':category_id, 'currency_code':$('input[name=currency_code]').val(), 'currency_value':$('input[name=currency_value]').val()};
	$.ajax({
		url: 'index.php?route=module/pos/getCategoryItemsAjax&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			removeMessage();
			showMessage('pos_attention', null, null);
		},
		complete: function() {
			removeMessage();
		},
		success: function(json) {
			$('#browse_category').empty();
			var tdhtml = '<a onclick="showCategoryItems(\'<?php echo $text_top_category_id; ?>\')"><?php echo $text_top_category_name; ?></a>';
			if (json['path']) {
				for (var i = 0; i < json['path'].length; i++) {
					tdhtml += '&nbsp;>&nbsp;';
					if (i < json['path'].length-1) {
						tdhtml += '<a onclick="showCategoryItems(\'' + json['path'][i]['id'] + '\')">' + json['path'][i]['name'] + '</a>';
					} else {
						tdhtml += json['path'][i]['name'];
					}
				}
			}
			$('#browse_category').html(tdhtml);
			if (json['browse_items']) {
				// set the category path name
				// clean up the display table
				$('#browse_product_div').empty();
				var html = '<table class="list" style="border: 0;">';
				var col_per_row = 5;
				var browse_total = json['browse_items'].length;
				var browse_total_row_no = (browse_total % col_per_row) == 0 ? browse_total / col_per_row : parseInt(browse_total / col_per_row) + 1;
				for (var row = 0; row < browse_total_row_no; row++) {
					html += '<tr>';
					for (var col = 0; col < col_per_row; col++) {
						var index = row*col_per_row+col;
						if (index < json['browse_items'].length) {
							if (json['browse_items'][index]['type'] == 'C') {
								html += '<td class="center" width="' + 100/col_per_row + '%" height="80px" style="padding: 3px 1px 0px 1px; border: 0; background-image: url(\'view/image/pos/category.png\'); background-position: center; background-repeat:no-repeat;">';
								html += '<a onclick="showCategoryItems(\'' + json['browse_items'][index]['id'] + '\')"><img src="' + json['browse_items'][index]['image'] + '" style="max-width: 50px; max-height: 50px; width: auto; height: auto;" /></a>';
								html += '</td>';
							} else {
								html += '<td class="center" width="' + 100/col_per_row + '%" style="padding: 3px 1px 0px 1px; border: 0;">';
								html += '<a onclick="selectProduct(this, \'' + json['browse_items'][index]['id'] + '\', \'' + json['browse_items'][index]['name'] + '\')"><img src="' + json['browse_items'][index]['image'] + '"  style="max-width: 75px; max-height: 75px; width: auto; height: auto;"/></a>';
								html += '<input type="hidden" value="' + json['browse_items'][index]['hasOptions'] + '" />';
								html += '</td>';
							}
						} else {
							html += '<td class="center" width="' + 100/col_per_row + '%" style="padding: 3px 1px 0px 1px; border: 0;"></td>';
						}
					}
					html += '</tr>';
					html += '<tr>';
					for (var col = 0; col < col_per_row; col++) {
						var index = row*col_per_row+col;
						if (index < json['browse_items'].length) {
							if (json['browse_items'][index]['type'] == 'C') {
								html += '<td class="center" width="' + 100/col_per_row + '%" style="padding:0px; vertical-align: top; border: 0;">';
								html += json['browse_items'][index]['name'];
								html += '</td>';
							} else {
								html += '<td class="center" width="' + 100/col_per_row + '%" style="padding:0px; vertical-align: top; border: 0;">';
								html += json['browse_items'][index]['name'] + '<br />';
								html += json['browse_items'][index]['price_text'] + '<br />';
								html += '(' + json['browse_items'][index]['stock_text'] + ')';
								html += '</td>';
							}
						} else {
							html += '<td class="center" width="' + 100/col_per_row + '%" style="padding: 0px; border: 0;"></td>';
						}
					}
					html += '</tr>';
				}
				html += '</table>';
				$('#browse_product_div').html(html);
			}
		}
	});
};

function toggleCategoryTree() {
	// toggle the category tree
};

function selectProduct(anchor, product_id, product_name) {
	if (browseQ.length > 0) {
		enQueue(anchor, product_id, product_name);
	} else {
		enQueue('processing...', 0, 0);
		processSelectProduct(anchor, product_id, product_name);
	}
};

function processSelectProduct(anchor, product_id, product_name) {
	// add the given product with the product_id
	$('#product_new input[name=quantity]').val('1');
	$('#product_new input[name=product_id]').val(product_id);
	if ($(anchor).closest('td').find('input').val() == '0') {
		// no option
		checkAndSaveOrder('button_product', 0, 0);
	} else {
		$.ajax({
			url: 'index.php?route=module/pos/getProductOptions&token=<?php echo $token; ?>&product_id=' + product_id,
			type: 'post',
			dataType: 'json',
			data: {},
			beforeSend: function() {
				removeMessage();
				showMessage('pos_attention', null, null);
			},
			complete: function() {
				removeMessage();
			},
			success: function(json) {
				if (json) {
					// add for Weight based price begin
					if (json['weight_price'] == '1') {
						$('#input_quantity').hide();
						$('input[name=quantity]').attr('value', '1');
						$('#weight_name').text(json['weight_name'] + ':');
						$('input[name=weight_name]').attr('value', json['weight_name']);
						$('input[name=weight]').attr('value', '1');
						$('#input_weight').show();
					}
					// add for Weight based price end
					handleOptionReturn(product_name, product_id, json['option_data']);
					$('#tab_search').trigger('click');
				}
			}
		});
	}
	preTabId = 'tab_browse';
};

function enQueue(anchor, product_id, product_name) {
	var data = {'anchor': anchor, 'product_id':product_id, 'product_name':product_name};
	browseQ.push(data);
};

function deQueue() {
	if (browseQ.length > 0) {
		var data = browseQ.shift();
		if (data['anchor'] == 'processing...') {
			data = browseQ.shift();
		}
		if (data) {
			processSelectProduct(data['anchor'], data['product_id'], data['product_name']);
		}
	}
}
// add for Browse end
// add for Cash type begin
function selectCashType(cashValue) {
	var value = parseFloat($('input[name=tendered_amount]').val());
	if (value > 0) {
		if (!useCashType) {
			// begin to use cash type
			value = parseFloat(cashValue);
			useCashType = true;
		} else {
			value += parseFloat(cashValue);
		}
	}
	$('input[name=tendered_amount]').attr('value', toFixed(value, 2));
};
// add for Cash type end

$('td[id^=keyboard_td]').live('mousedown', function(e) {
	// handle the key press
	var keyValue = $(this).find('input').val();
	if (keyValue == 'keyboard') {
		// put focus back
		if (lastInput) {
			setTimeout(function() {
				$(lastInput).focus();
			}, 10);
		}
		var marginRight = $('#keyboard_wrapper').css('margin-right');
		if (marginRight == '590px') {
			// expand the keyboard
			$('#keyboard_wrapper').css('margin-right', '10px');
			$('#tabs_div').css('height', '348px');
			$('#keyboard_wrapper').css('width', '990px');
			$('#keyboard_keys col').each(function() {
				$(this).css('width', '57px');
			});
		} else {
			$('#keyboard_wrapper').css('margin-right', '590px');
			$('#tabs_div').css('height', '538px');
			$('#keyboard_wrapper').css('width', '410px');
			$('#keyboard_keys col').each(function() {
				$(this).css('width', '73px');
			});
		}
	} else if (lastInput) {
		setTimeout(function() {
			$(lastInput).focus();
			if (keyValue == 'clear') {
				$(lastInput).attr('value', '');
			} else if (keyValue == 'accept') {
				onEnter(lastInput);
			} else if (keyValue == 'Del') {
				var text = $(lastInput).val();
				$(lastInput).attr('value', text.substr(0, text.length-1));
			}else {
				$(lastInput).attr('value', $(lastInput).val() + keyValue);
			}
			if ($(lastInput).hasClass('ui-autocomplete-input')) {
				var inputName = $(lastInput).attr('name');
				if (inputName == 'customer' || inputName == 'telephone' || inputName == 'email' || inputName == 'filter_customer') {
					$(lastInput).catcomplete('search', $(lastInput).val());
				} else {
					$(lastInput).autocomplete('search', $(lastInput).val());
				}
			}
		}, 10);
	}
});

// mousedown event fires before the blure event
$(document).mousedown(function(e) {
	var targetId = $(e.target).attr('id');
	if (!targetId) {
		targetId = $(e.target).parent().attr('id');
	}
	if (targetId && targetId.substr(0, 11) == 'keyboard_td') {
		keyboardClick = true;
	} else {
		keyboardClick = false;
	}
});

$('input[type=text]').live('blur', function() {
	lastInput = this;
});

function onEnter(input) {
	if (enterFns[$(input).attr('name')]) {
		var fn = enterFns[$(input).attr('name')];
		window[fn](input);
	}
};

// add for table management begin
/*
function markTableOrder(table_id, x1, y1, x2, y2) {
	$('<div id="pos_table_'+table_id+'" style="position:absolute;"><table border="0" width="100%" height="100%" align="center" valign="center"><tr align="center"><td align="center"><img src="view/image/pos/order.jpg" style="padding-left: 5px;"/></td></tr></table></div>')
		.appendTo($('.jcrop-holder'))
		.css("left", x1 + "px")
		.css("top", y1 + "px")
		.css("width", (x2-x1)+"px")
		.css("height", (y2-y1)+"px");
};

function markTableEmpty(table_id, x1, y1, x2, y2) {
	$('<div id="pos_table_'+table_id+'" style="position:absolute;"><table border="0" width="100%" height="100%" align="center" valign="center"><tr align="center"><td align="center"><img src="view/image/pos/empty.jpg" style="padding-left: 5px;"/></td></tr></table></div>')
		.appendTo($('.jcrop-holder'))
		.css("left", x1 + "px")
		.css("top", y1 + "px")
		.css("width", (x2-x1)+"px")
		.css("height", (y2-y1)+"px");
};

function selectTable(c) {
	var inRange = false;
	for (var i in tables) {
		var coors = tables[i]['coors'];
		if (coors) {
			var xys = coors.split(',');
			if (xys.length == 4) {
				var x1 = parseInt(xys[0]), y1 = parseInt(xys[1]), x2 = parseInt(xys[2]), y2 = parseInt(xys[3]);
				if (c.x >= x1 && c.x2 <= x2 && c.y >= y1 && c.y2 <= y2) {
					// check if the current table has an order
					inRange = true;
					if (tables[i]['order_id']) {
						var url = 'index.php?route=module/pos/main&token=<?php echo $token; ?>&order_id=' + tables[i]['order_id'];
						selectOrder($('div[id=pos_table_'+tables[i]['table_id']+'] img'), url);
					} else {
						var url = 'index.php?route=module/pos/createEmptyOrder&token=<?php echo $token; ?><?php if(isset($store_id)) { echo '&store_id='.$store_id; } ?>';
						// add for Quotation begin
						var work_mode = $('input[name=work_mode]').val();
						url += '&work_mode=' + work_mode;
						// add for Quotation end
						url += '&table_id=' + tables[i]['table_id'];
						$.ajax({
							url: url,
							type: 'post',
							beforeSend: function() {
							},
							success: function(html) {
								$('#divWrap').html($(html).find('div[id=\'divWrap\']').html());
								$('.pos_htabs a').tabs();
								$('.pos_vtabs a').tabs();
								$('select[name*=\'[country_id]\']').trigger('change');
								$('select[name*=\'customer_group_id\']').trigger('change');
								var table_id = $('input[name=pos_new_table_order_table_id]').val();
								var order_id = $('input[name=pos_new_table_order_order_id]').val();
								if (table_id && order_id) {
									for (var i in tables) {
										if (parseInt(tables[i]['table_id']) == parseInt(table_id)) {
											var table_data = tables[i];
											table_data['order_id'] = '' + order_id;
											tables[i] = table_data;
											break;
										}
									}
								}
								checkAndSaveOrder('button_new_order', 0);			
							}
						});
					}
					break;
				}
			}
		}
	}
	if (!inRange) {
		selectIndex = -1;
	}
};
*/
function filterTable() {
	// filter the orders using the table id
	var table_id = $('select[name=table_list]').val();
	filter($('#button_filter'), table_id);
};

function changeTable() {
	var order_id = parseInt($('#order_id').text(), 10);
	var data = {'order_id': order_id, 'table_id':$('select[name=order_table_id]').val()};
	$.ajax({
		url: 'index.php?route=module/pos/saveOrderTableId&token=<?php echo $token; ?>',
		type: 'post',
		data: data,
		dataType: 'json',
		beforeSend: function() {
			removeMessage();
			showMessage('pos_attention', null, null);
		},
		success: function(json) {
			if (json['success']) {
				removeMessage();
				showMessage('pos_success', json['success'], null);
			}
		}
	});
};
// add for table management end
// add for till control begin
<?php if (isset($till_control_key)) { ?>
function sendControlKey() {
	var applet = document.jzebra;
	if (applet) {
		applet.append("<?php echo $till_control_key; ?>");
		applet.print();
	}
};
<?php } ?>
// add for till control end
// add for serial no begin
$('input[name=\'product_sn\']').live('focus', function(){
	$(this).autocomplete({
		delay: 500,
		source: function(request, response) {
			var product_id = $('input[name=\'product_id\']').val();
			if (parseInt(product_id) > 0) {
				$.ajax({
					url: 'index.php?route=module/pos/sn_autocomplete&token=<?php echo $token; ?>&filter_sn=' +  encodeURIComponent(request.term) + '&filter_product_id=' + product_id,
					dataType: 'json',
					success: function(json) {		
						response($.map(json, function(item) {
							return {
								label: item.name,
								value: item.product_sn_id
							}
						}));
					}
				});
			}
		}, 
		select: function(event, ui) {
			$('input[name=\'product_sn\']').val(ui.item.label);
			$('input[name=\'product_sn_id\']').val(ui.item.value);

			return false;
		},
		focus: function(event, ui) {
			return false;
		}
	});
});
// add for serial no end
</script> 
<?php echo $footer; ?>