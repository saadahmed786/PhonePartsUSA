<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/report.png" alt="" /> <?php echo $heading_title; ?></h1>
    </div>
    <div class="content">
      <table class="form">
        <tr>
          <td><?php echo $entry_date_start; ?>
            <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" id="date-start" size="12" /></td>
          <td><?php echo $entry_date_end; ?>
            <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" id="date-end" size="12" /></td>
          <td><?php echo $entry_group; ?>
            <select name="filter_group">
              <?php foreach ($groups as $groups) { ?>
              <?php if ($groups['value'] == $filter_group) { ?>
              <option value="<?php echo $groups['value']; ?>" selected="selected"><?php echo $groups['text']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $groups['value']; ?>"><?php echo $groups['text']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
          <td style="text-align: right;"><a onclick="filter();" class="button"><?php echo $button_filter; ?></a></td>
        </tr>
      </table>
      <table class="list">
        <thead>
          <tr>
            <td class="left"><?php echo $column_user_name; ?></td>
			<td class="left"><?php echo $column_date_start; ?></td>
            <td class="left"><?php echo $column_date_end; ?></td>
            <?php foreach($col_key as $col_name) { ?>
			<td class="right"><?php echo $col_name; ?></td>
			<?php } ?>

            <td class="right"><?php echo $column_total; ?></td>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($payments)) { ?>
          <?php foreach ($payments as $payment) { ?>
          <tr>
		    <td class="left"><?php echo $payment['username']; ?></td>
            <td class="left"><?php echo $payment['date_start']; ?></td>
            <td class="left"><?php echo $payment['date_end']; ?></td>
            <?php foreach($col_key as $col_name) { ?>
			<td class="right"><?php echo $payment[$col_name]; ?></td>
			<?php } ?>
            <td class="right"><?php echo $payment['total']; ?></td>
          </tr>
          <?php } ?>
          <?php } else { ?>
          <tr>
            <td class="center" colspan="7"><?php echo $text_no_results; ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <div class="pagination"><?php echo $pagination; ?></div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
function filter() {
	url = 'index.php?route=report/order_payment/summary&token=<?php echo $token; ?>';
	
	var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');
	
	if (filter_date_start) {
		url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
	}

	var filter_date_end = $('input[name=\'filter_date_end\']').attr('value');
	
	if (filter_date_end) {
		url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	}
		
	var filter_group = $('select[name=\'filter_group\']').attr('value');
	
	if (filter_group) {
		url += '&filter_group=' + encodeURIComponent(filter_group);
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
<?php echo $footer; ?>