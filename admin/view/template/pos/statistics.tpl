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
<style>
.colored{
background-color:#ffd8ff !important;	
}
</style>
<div id="content">
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/home.png" alt="" /> <?php echo $heading_title; ?></h1>
    </div>
    <div class="content">
      
      <div class="latest">
        <div class="dashboard-heading">Statistics</div>
        <div class="dashboard-content">
        <div style="width:500px;float:right">
        <table class="list right">
        <thead>
        <tr>
        <td>Date Start</td>
        <td>Date End</td>
        <td>Filter</td>
        </tr>
        </thead>
        <tbody>
        <tr class="filter">
          <td><input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" size="12" class="date" /></td>
                      <td><input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" size="12" class="date" /></td>
                      <td align="center"><a onclick="filter();" class="button">Filter</a></td>
        
        </tr>
        </tbody>
        </table>
        </div>
          <table class="list">
            <thead>
              <tr>
                <td class="right"><?php echo $column_username; ?></td>
                <td class="left"><?php echo $column_name; ?></td>
                <td class="left"><?php echo $column_cash; ?></td>
                <td class="left"><?php echo $column_card; ?></td>
               <td>Paypal</td>
                <td>Replacement Orders * Amount</td>
                <td>Voucher Used Amount</td>
                <td>Voucher Issued Amount</td>
                <td>Total Returns</td>
                <td class="right"><?php echo $column_action; ?></td>
              </tr>
            </thead>
            <tbody>
            
              <?php 
              $total_cash = 0;
              $total_card = 0;
              $total_paypal = 0;
              $total_replacement_order = 0;
              $total_replacement_amount = 0;
              $total_voucher_issued = 0;
              $total_voucher_used = 0;
              $total_returns = 0;
              
              foreach ($rows as $row) {
              $row['replacement_amount'] = $row['replacement_amount']* (-1);
               $total_cash += $row['cash'];
              $total_card += $row['card'];
              $total_paypal += $row['paypal_total'];
              $total_replacement_order += $row['replacement_orders'];
              $total_replacement_amount  += $row['replacement_amount'];
              $total_voucher_issued += $row['voucher_issued_amount'];
              $total_voucher_used += $row['voucher_used_amount'];
              $total_returns += $row['total_returns'];
            
               ?>
              <tr>
                <td class="right"><?php echo $row['username']; ?></td>
                <td class="left"><?php echo $row['firstname'].' '.$row['lastname']; ?></td>
                <td class="left"><?php echo $this->currency->format(round($row['cash'])); ?></td>
                <td class="left"><?php echo $this->currency->format($row['card']); ?></td>
              <td><?php echo $this->currency->format($row['paypal_total']); ?></td>
                
                 <td><?php echo $row['replacement_orders'];?> * <?php echo $this->currency->format($row['replacement_amount']);?></td>
                <td><?php echo $this->currency->format($row['voucher_used_amount']);?></td>
                <td><?php echo $this->currency->format($row['voucher_issued_amount']);?></td>
                <td><?php echo $row['total_returns'];?></td>
                <td class="right">     
                    [ <a href='index.php?route=pos/dashboard/orderHistory&user_id=<?= $row["user_id"]."&token=".$token.$embed; ?>'>Orders</a> ]
                    &nbsp;&nbsp;
                    [ <a href='index.php?route=pos/dashboard/history&user_id=<?= $row["user_id"]."&token=".$token.$embed; ?>'>History</a> ]
                    &nbsp;&nbsp;
                    [ <a data-user-id="<?php echo $row['user_id']; ?>" class='withdraw' href='#withdraw_wrapper'>Withdraw</a> ]
                </td>
              </tr>
              <?php } ?>
              
              <tr style="font-weight:bold;height:60px;">
              <td colspan="2" class="colored right">Totals: </td>
              <td class="colored"><?php echo $this->currency->format(round($total_cash));?></td>
              <td class="colored" ><?php echo $this->currency->format($total_card);?></td>
              <td class="colored"><?php echo $this->currency->format($total_paypal);?></td>
              <td class="colored"><?php echo $total_replacement_order. ' * '. $this->currency->format($total_replacement_amount);?></td>
              <td class="colored"><?php echo $this->currency->format($total_voucher_used);?></td>
              <td class="colored"><?php echo $this->currency->format($total_voucher_issued);?></td>
              <td class="colored"><?php echo ($total_returns);?></td>
             <td colspan="2" class="colored"> </td>
              
              
              </tr>
              
              <tr style="background:#CCC;font-weight:bold">
              <td colspan="9" class="right">Cash</td>
              <td><?php echo $this->currency->format(round($total_cash));?></td>
              
              </tr>
               <tr style="background:#CCC;font-weight:bold">
              <td colspan="9" class="right">In Store CC</td>
              <td><?php echo $total_instore;?></td>
              
              </tr>
               <tr style="background:#CCC;font-weight:bold">
              <td colspan="9" class="right">Paypal / Paypal Express</td>
              <td><?php echo $this->currency->format($total_paypal);?></td>
              
              </tr>
               <tr style="background:#CCC;font-weight:bold">
              <td colspan="9" class="right">Store Credit</td>
              <td><?php echo $total_store_credit;?></td>
              
              </tr>
               <tr style="background:#CCC;font-weight:bold">
              <td colspan="9" class="right">Replacement Order</td>
              <td><?php echo $total_replacement;?></td>
              
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!--========================================== withdraw pop up ============================================-->
<div class="hide">
<div id="withdraw_wrapper">
    <div class="withdraw_form">
        <h3>Withdraw cash from user</h3><hr>
        
        <div class="message_wrapper"></div>
        
        <table>
            <tr>
                <td><span class="label2">Amount( <?= $this->currency->getSymbolLeft().' '.$this->currency->getSymbolRight(); ?> )</span></td>
                <td>
                    <div class="input-control text">
                        <input class="amount" type="text" name="amount" />                     
                    </div> 
                    <input type="hidden" name="user_id" />                    
                </td>
            </tr>
            <tr>
                <td></td>
                <td><button name="withdraw" value="Withdraw" class='btn_withdraw button'>Withdraw</button></td>
            </tr>
        </table>
        <div class="bottom">*refresh page after withdraw to show changes</div>
    </div>
</div>
<!-- END order_wrapper -->
</div>
<!-- END .hide -->
<?php echo $footer; ?>

<script>

$("a.withdraw").click(function(){
    $('input[name="user_id"]').val($(this).attr('data-user-id'));
    $('.message_wrapper').html('');
});     
      
	  $(document).ready(function() {
	$('.date').datepicker({dateFormat: 'yy-mm-dd'});
});
$("a.withdraw").fancybox({
        maxWidth	: 450,
        maxHeight	: 190,
        fitToView	: false,
        autoSize	: false,
        closeClick	: false,
        openEffect	: 'none',
        closeEffect	: 'none'
});

$('.btn_withdraw').click(function(){
    $.post("index.php?route=pos/dashboard/withdraw&token=<?= $token ?>",{ user_id: $('input[name="user_id"]').val(), amount: $('.amount').val() }, function(data){
        var data = JSON.parse(data);
        
        if(data['success']){
            $('.message_wrapper').html('<div class="success">'+data['success']+'</div>');
        }else if(data['error']){
            $('.message_wrapper').html('<div class="warning">'+data['error']+'</div>');
        }               
    });
});

function filter() {
	url = 'index.php?route=pos/dashboard&token=<?php echo $token; ?><?php echo $embed;?>';
	
	
	
	var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');
	
	if (filter_date_start) {
		url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
	}
	
	var filter_date_end = $('input[name=\'filter_date_end\']').attr('value');
	
	if (filter_date_end) {
		url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	}
				
	location = url;
}
</script>