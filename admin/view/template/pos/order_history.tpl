<?php echo $header; ?>
<?php
if($this->request->get['embed']==1)
{
$embed ='&embed=1';
}
else
{
$embed = '';
}
?>
<div id="content">
    <div class="box">
    <div class="heading">
      <h1><img alt="" src="view/image/order.png"> Orders</h1>
    </div>
    <div class="content">
      <form id="form" enctype="multipart/form-data" method="post" action="">
        <table class="list">
          <thead>
            <tr>
              <td class="right">Order ID</td>
              <td class="left">Customer</td>
              <td class="left">Payment</td>
              <td class="left">Status</td>
              <td class="right">Total</td>
              <td class="left" style="display:none">Date Added</td>
              <td class="left">Date Added</td>
              <td class="right">Action</td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td align="right">
                  <input type="text" style="text-align: right;" size="4" value="" name="filter_order_id">
              </td>
              <td>
                  <input type="text" value="" name="filter_customer" class="ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true">
              </td>
              <td><select name="filter_payment_method">
              <option value="">All</option>
             
              <option <?php echo ($filter_payment_method=='Cash'?'selected':'');?>>Cash</option>
              <option <?php echo ($filter_payment_method=='Card'?'selected':'');?>>Card</option>
              <option <?php echo ($filter_payment_method=='PayPal'?'selected':'');?>>PayPal</option>
              <option <?php echo ($filter_payment_method=='Paypal Express'?'selected':'');?>>Paypal Express</option>
             
              
              </select> </td>
              <td>
                <select name="filter_order_status_id">
                  <option value="*">All</option>
                  <?php if ($filter_order_status_id == '0') { ?>
                    <option value="0" selected="selected"><?php echo $text_missing; ?></option>
                  <?php } else { ?>
                    <option value="0"><?php echo $text_missing; ?></option>
                  <?php } ?>
                  <?php foreach ($order_statuses as $order_status) { ?>
                      <?php if ($order_status['order_status_id'] == $filter_order_status_id) { ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                      <?php } else { ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                      <?php } ?>
                  <?php } ?>                  
                </select>
              </td>
              <td align="right">
                  <input type="text" style="text-align: right;" size="4" value="" name="filter_total">
              </td>
              <td style="display:none"><input type="text" class="date" size="12" value="" name="filter_date_added"></td>
              <td><input type="text" class="date" size="12" value="" name="filter_date_modified"></td>
              <td align="right"><a class="button" onclick="filter();">Filter</a></td>
            </tr>
               <?php foreach($rows as $row){ ?>
                    <tr class="data_row">
                        <td align='right'><?= ($row['ref_order_id']?$row['ref_order_id']:$row['order_id']) ?></td>
                        <td><?= $row['customer'] ?></td>
                        <td><?= $row['payment_method']; ?></td>
                        <td><?= $row['status'] ?></td>
                        <td align='right' class='td_total'><?= $row['total'] ?></td>
                        <td style="display:none"><?= date($this->language->get('date_format_short'), strtotime($row['date_added'])) ?></td>
                        <td><?= date($this->language->get('date_format_short'), strtotime($row['date_modified'])) ?></td>
                        <td align="right">                            
                            [<a target="_blank" href="<?= $url_order_info.'&order_id='.$row['order_id']; ?>" href="#">View</a>]
                        </td>
                    </tr>
               <?php } ?>    
            </tbody>
        </table>
      </form>
      <div class="pagination">
          <?php echo $pagination; ?>
      </div>
      <!-- END .pagination -->  
    </div>
    <!-- END .content -->    
  </div>
  <!-- END .box -->  
</div>
<!-- END .content -->
<?= $footer ?>    

<script type="text/javascript"><!--
function filter() {
	url = 'index.php?route=pos/dashboard/orderHistory&user_id=<?= $_GET["user_id"] ?>&token=<?php echo $token.$embed; ?>';
	
	var filter_order_id = $('input[name=\'filter_order_id\']').attr('value');
	
	if (filter_order_id) {
		url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
	}
	
	var filter_customer = $('input[name=\'filter_customer\']').attr('value');
	
	if (filter_customer) {
		url += '&filter_customer=' + encodeURIComponent(filter_customer);
	}
	
	
	var filter_payment_method = $('select[name=\'filter_payment_method\']').attr('value');
	
	if (filter_payment_method) {
		url += '&filter_payment_method=' + encodeURIComponent(filter_payment_method);
	}
	
	var filter_order_status_id = $('select[name=\'filter_order_status_id\']').attr('value');
	
	if (filter_order_status_id != '*') {
		url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
	}	

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
				
	location = url;
}

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

<style>
    .data_row td{
        padding: 6px !important;
    }
</style>