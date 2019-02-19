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
              
              <td style="text-align: right;"><a onclick="filter();" class="button"><?php echo $button_filter; ?></a></td>
            </tr>
          </table>
          <table class="list">
            <thead>
              <tr>
                <td class="left">
                  
                  
                  <?php echo $column_date; ?></td>
                  <td class="left">Void Type</td>
                  <td class="left">Item SKU</td>
                  <td class="left">Order ID</td>
                  <td class="left">Reason</td>
                  
                </tr>
              </thead>
              <tbody>
                <?php if ($orders) { ?>
                <?php foreach ($orders as $order) { ?>
                <?php for ($i=0; $i < $order['quantity'] ; $i++) { ?>
                <tr>
                  <td class="left"><?php echo $order['date']; ?></td>
                  
                  <td class="left"><?php echo $order['void_type']; ?></td>
                  <td class="left"><?php echo $order['sku']; ?></td>
                  <td class="left"><?php echo $order['order_id']; ?></td>
                  <td class="left"><?php echo $order['reason']; ?></td>
                </tr>
                <?php } ?>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="center" colspan="6"><?php echo $text_no_results; ?></td>
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
         url = 'index.php?route=report/voided_item&token=<?php echo $token; ?>';
         
         var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');
         
         if (filter_date_start) {
          url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
        }

        var filter_date_end = $('input[name=\'filter_date_end\']').attr('value');
        
        if (filter_date_end) {
          url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
        }
        
        var filter_group = $('select[name=\'filter_group\']').attr('value');
        

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