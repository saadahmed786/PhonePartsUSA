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
              <td class="left"><?php if ($sort == 'order_id') { ?>
                <a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_id; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_order; ?>"><?php echo $column_order_id; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'commission') { ?>
                <a href="<?php echo $sort_commision; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_commission; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_commision; ?>"><?php echo $column_commission; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'username') { ?>
                <a href="<?php echo $sort_user_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_user_name; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_user_name; ?>"><?php echo $column_user_name; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'date_modified') { ?>
                <a href="<?php echo $sort_commission_date; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_commission_date; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_commission_date; ?>"><?php echo $column_commission_date; ?></a>
                <?php } ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td align="left"><input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" size="4" style="text-align: right;" /></td>
              <td align="left"><input type="text" name="filter_commission" value="<?php echo $filter_commission; ?>" size="4" style="text-align: right;" /></td>
              <td align="left"><input type="text" name="filter_user_name" value="<?php echo $filter_user_name; ?>" size="4" style="text-align: left;" /><input type="hidden" name="filter_user_id" value="<?php echo $filter_user_id; ?>" /></td>
              <td align="left"><input type="text" name="filter_commission_date" value="<?php echo $filter_commission_date; ?>" size="12" class="date" /></td>
            </tr>
            <?php if ($order_commissions) { ?>
            <?php foreach ($order_commissions as $order_commission) { ?>
            <tr>
              <td class="left"><?php echo $order_commission['order_id']; ?></td>
              <td class="left"><?php echo $order_commission['commission']; ?></td>
              <td class="left"><?php echo $order_commission['username']; ?></td>
              <td class="left"><?php echo $order_commission['date_modified']; ?></td>
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
	url = 'index.php?route=report/pos_commission&token=<?php echo $token; ?>';
	
	var filter_order_id = $('input[name=\'filter_order_id\']').attr('value');
	
	if (filter_order_id) {
		url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
	}
	
	var filter_commission = $('input[name=\'filter_commission\']').attr('value');
	
	if (filter_commission) {
		url += '&filter_commission=' + encodeURIComponent(filter_commission);
	}
	
	var filter_user_name = $('input[name=\'filter_user_name\']').attr('value');

	if (filter_user_name) {
		url += '&filter_user_name=' + encodeURIComponent(filter_user_name);
	}	
	
	var filter_commission_date = $('input[name=\'filter_commission_date\']').attr('value');
	
	if (filter_commission_date) {
		url += '&filter_commission_date=' + encodeURIComponent(filter_commission_date);
	}

	var filter_user_id = $('input[name=\'filter_user_id\']').attr('value');
	
	if (filter_user_id && filter_user_name != '') {
		url += '&filter_user_id=' + encodeURIComponent(filter_user_id);
	}

	location = url;
}

$('input[name=\'filter_user_name\']').live('focus', function(){
	$(this).autocomplete({
		delay: 500,
		source: function(request, response) {
			var url = 'index.php?route=report/pos_commission/autocompleteByUserName&token=<?php echo $token; ?>&filter_name=' + encodeURIComponent(request.term);
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