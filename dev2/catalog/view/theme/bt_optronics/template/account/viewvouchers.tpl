<?php echo $header; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <a <?php echo(($breadcrumb == end($breadcrumbs)) ? 'class="last"' : ''); ?> href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  
  <table class="list">
    <thead>
      <tr>
       <td class="center">Voucher Code</td>
       <td class="right">Issued Date</td>
       <td class="right">Initial Amount</td>
       <td class="right">Available Amount</td>
       <td class="center">Order Details</td>
       <td class="center">Action</td>
       
      </tr>
    </thead>
    <tbody>
      <?php if ($vouchers) { ?>
      <?php foreach ($vouchers  as $voucher) { ?>
      <tr>
        <td class="center"><?php echo $voucher['code'];?></td>
        <td class="center"><?php echo date('m/d/y h:i a', strtotime($voucher['date']));?></td>
        <td class="right"><?php echo $this->currency->format($voucher['amount']);?></td>
        <td class="right"><?php echo $this->currency->format($voucher['balance']);?></td>
        <td class="center"><?php foreach($voucher['order_details'] as $detail)
        {
        
        	echo 'Order # '.$detail['order_id']." used ".$this->currency->format($detail['amount'])."<br/>";
        
        }?></td>
        <td class="center"><?php
        if($voucher['balance']>0)
        {
        ?>
        <a href="<?php echo $this->url->link('checkout/cart/use_voucher', 'code='.$voucher['code'], 'SSL');?>" class="button_pink"><span>Use Now</span></a>
        <?php
        }
        ?>
        </td>
        
      </tr>
      <?php } ?>
      <?php } else { ?>
      <tr>
        <td class="center" colspan="6">No Store Credit issued or applied yet</td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
  <div class="pagination"><?php echo $pagination; ?></div>
  
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>