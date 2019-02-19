<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/report.png" alt="" /> <?php echo $heading_title; ?></h1>
	  <div class="buttons"><a onclick="filter();" class="button"><?php echo $button_filter; ?></a></div>
    </div>
    <div class="content">
      <form action="" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td class="left"><?php if ($sort == 'order_payment_id') { ?>
                <a href="<?php echo $sort_order_payment; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_payment_id; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_order_payment; ?>"><?php echo $column_order_payment_id; ?></a>
                <?php } ?></td>
              <td class="right"><?php if ($sort == 'order_id') { ?>
                <a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_id; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_order; ?>"><?php echo $column_order_id; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'payment_type') { ?>
                <a href="<?php echo $sort_payment_type; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_payment_type; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_payment_type; ?>"><?php echo $column_payment_type; ?></a>
                <?php } ?></td>
              <td class="right"><?php if ($sort == 'tendered_amount') { ?>
                <a href="<?php echo $sort_tendered_amount; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_tendered_amount; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_tendered_amount; ?>"><?php echo $column_tendered_amount; ?></a>
                <?php } ?></td>
              <td class="left"><?php echo $column_payment_note; ?></td>
              <td class="left"><?php if ($sort == 'payment_time') { ?>
                <a href="<?php echo $sort_payment_time; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_payment_time; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_payment_time; ?>"><?php echo $column_payment_time; ?></a>
                <?php } ?></td>
			  <!-- add for admin payment details begin -->
              <td class="left"><?php echo $column_user_name; ?></td>
              <td class="left"><?php echo $column_invoice_number; ?></td>
			  <!-- add for admin payment details end -->
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
			  <td></td>
              <td align="right"><input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" size="4" style="text-align: right;" /></td>
              <td><input type="text" name="filter_payment_type" value="<?php echo $filter_payment_type; ?>" /></td>
              <td align="right"><input type="text" name="filter_tendered_amount" value="<?php echo $filter_tendered_amount; ?>" size="4" style="text-align: right;" /></td>
			  <td></td>
              <td><input type="text" name="filter_payment_date" value="<?php echo $filter_payment_date; ?>" size="12" class="date" /></td>
			  <!-- add for admin payment details begin -->
              <td>
				<input type="text" name="filter_user_name" value="<?php echo $filter_user_name; ?>" />
				<input type="hidden" name="filter_user_id" value="<?php echo $filter_user_id; ?>" />
			  </td>
              <td><input type="text" name="filter_invoice_number" value="<?php echo $filter_invoice_number; ?>" /></td>
			  <!-- add for admin payment details end -->
            </tr>
            <?php if ($order_payments) { ?>
            <?php foreach ($order_payments as $order_payment) { ?>
            <tr>
              <td class="left"><?php echo $order_payment['order_payment_id']; ?></td>
              <td class="right"><?php echo $order_payment['order_id']; ?></td>
              <td class="left"><?php echo $order_payment['payment_type']; ?></td>
              <td class="right"><?php echo $order_payment['tendered_amount']; ?></td>
              <td class="left"><?php echo $order_payment['payment_note']; ?></td>
              <td class="left"><?php echo $order_payment['payment_time']; ?></td>
			  <td class="left"><?php echo $order_payment['user_name']; ?></td>
			  <td class="left"><?php echo $order_payment['invoice_number']; ?></td>
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
    </div>
  </div>
</div>
<script type="text/javascript"><!--
function filter() {
	url = 'index.php?route=report/order_payment&token=<?php echo $token; ?>';
	
	var filter_order_id = $('input[name=\'filter_order_id\']').attr('value');
	
	if (filter_order_id) {
		url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
	}
	
	var filter_payment_type = $('input[name=\'filter_payment_type\']').attr('value');
	
	if (filter_payment_type) {
		url += '&filter_payment_type=' + encodeURIComponent(filter_payment_type);
	}
	
	var filter_tendered_amount = $('input[name=\'filter_tendered_amount\']').attr('value');

	if (filter_tendered_amount) {
		url += '&filter_tendered_amount=' + encodeURIComponent(filter_tendered_amount);
	}	
	
	var filter_payment_date = $('input[name=\'filter_payment_date\']').attr('value');
	
	if (filter_payment_date) {
		url += '&filter_payment_date=' + encodeURIComponent(filter_payment_date);
	}

	// add for admin payment details begin
	var filter_user_name = $('input[name=\'filter_user_name\']').attr('value');
	
	if (filter_user_name) {
		url += '&filter_user_name=' + encodeURIComponent(filter_user_name);
	}
	
	var filter_user_id = $('input[name=\'filter_user_id\']').attr('value');
	
	if (filter_user_id && filter_user_name != '') {
		url += '&filter_user_id=' + encodeURIComponent(filter_user_id);
	}

	var filter_invoice_number = $('input[name=\'filter_invoice_number\']').attr('value');
	
	if (filter_invoice_number) {
		url += '&filter_invoice_number=' + encodeURIComponent(filter_invoice_number);
	}
	// add for admin payment details end
	
	location = url;
}

// add for admin payment details
$('input[name=\'filter_user_name\']').live('focus', function(){
	$(this).autocomplete({
		delay: 500,
		source: function(request, response) {
			var url = 'index.php?route=report/order_payment/autocompleteByUserName&token=<?php echo $token; ?>&filter_name=' + encodeURIComponent(request.term);
			$.ajax({
				url: url,
				dataType: 'json',
				success: function(json) {	
					response($.map(json, function(item) {
						return {
							label: item.user_name,
							value: item.user_id
						}
					}));
				}
			});
		}, 
		select: function(event, ui) {
			$('input[name=filter_user_name]').attr('value', ui.item['label']);
			$('input[name=filter_user_id]').attr('value', ui.item['value']);
			return false;
		},
		focus: function(event, ui) {
			return false;
		}
	});
});
// add for admin payment details end
//--></script>  
<script type="text/javascript"><!--
$(document).ready(function() {
	$('.date').datepicker({dateFormat: 'yy-mm-dd'});
});
//--></script> 
<script type="text/javascript"><!--
$('#form input').keydown(function(e) {
	if (e.keyCode == 13) {
		filter();
	}
});
//--></script> 
<?php echo $footer; ?>