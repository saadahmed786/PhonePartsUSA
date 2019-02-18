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
        <td class="left" colspan="2"><?php echo $text_order_detail; ?></td>
      </tr>
    </thead>
  <table class="list order_info">
    <thead>
      <tr>
        <td class="left"><?php echo $column_name; ?></td>
        <td class="left model"><?php echo $column_model; ?></td>
        <td class="right"><?php echo $column_quantity; ?></td>
        <td class="right price"><?php echo $column_price; ?></td>
        <td class="right"><?php echo $column_total; ?></td>
        <?php if ($products) { ?>
        <td style="width: 1px;"></td>
        <?php } ?>
      </tr>
    </thead>
    <tbody>
    <?php $sub_total = 0.00;
    $junk = array('$',',');
    ?>
      
      <?php foreach ($products as $product) { ?>
      <?php

$_total =  floatval(str_replace($junk,'',$product['total']));
      ?>
      <?php 
      $sub_total = $sub_total + $_total;
      // echo $_total;
      // echo $product['total'];
      ?>
      <tr>
        <td class="left"><?php echo $product['name']; ?>
          <?php foreach ($product['option'] as $option) { ?>
          <br />
          &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
          <?php } ?></td>
        <td class="left model"><?php echo $product['model']; ?></td>
        <td class="right"><?php echo $product['quantity']; ?></td>
        <td class="right price"><?php echo $product['price']; ?></td>
        <td class="right"><?php echo $product['total']; ?></td>
        <td class="right"><a href="<?php echo $product['return']; ?>"><img src="catalog/view/theme/default/image/return.png" alt="<?php echo $button_return; ?>" title="<?php echo $button_return; ?>" /></a></td>
      </tr>
      <?php } ?>
      <?php foreach ($vouchers as $voucher) { ?>
      <!--<tr>
        <td class="left"><?php echo $voucher['description']; ?></td>
        <td class="left model"></td>
        <td class="right">1</td>
        <td class="right price"><?php echo $voucher['amount']; ?></td>
        <td class="right"><?php echo $voucher['amount']; ?></td>
        <?php if ($products) { ?>
        <td></td>
        <?php } ?>
      </tr>-->
      <?php } ?>
    </tbody>
    <tfoot>
      <?php foreach ($totals as $total) { ?>
      <?php
      if($total['code']=='shipping' && $total['value']=='0.0000')
      {
        $query = $this->db->query("SELECT shipping_method,shipping_cost FROM inv_orders_details where order_id='".(int)$this->request->get['order_id']."'");
        $shipping = $query->row;
        $total['title'] = $shipping['shipping_method'];
        $total['text'] = $this->currency->format($shipping['shipping_cost']);
      }
      ?>
      <tr>
    <td class="model"></td>
      <td class="price"></td>
        <td colspan="1"></td>
        <td class="right"><b><?php echo $total['title']; ?>: </b></td>
        <?php
        if($total['code']=='sub_total')
        {
          ?>
        <td class="right"><?php echo $this->currency->format($sub_total); ?></td>
        <?php
        }
        else
        {
          ?>
        <td class="right"><?php echo $total['text']; ?></td>

          <?php
        }

        ?>
        <?php if ($products) { ?>
        <td></td>
        <?php } ?>
      </tr>
      <?php } ?>
    </tfoot>
  </table>
  <?php if ($comment) { ?>
  <table class="list">
    <thead>
      <tr>
        <td class="left"><?php echo $text_comment; ?></td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="left"><?php echo $comment; ?></td>
      </tr>
    </tbody>
  </table>
  <?php } ?>
  <?php if ($histories) { ?>
  <h2><?php echo $text_history; ?></h2>
  <table class="list">
    <thead>
      <tr>
        <td class="left"><?php echo $column_date_added; ?></td>
        <td class="left"><?php echo $column_status; ?></td>
        <td class="left"><?php echo $column_comment; ?></td>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($histories as $history) { ?>
      <tr>
        <td class="left"><?php echo $history['date_added']; ?></td>
        <td class="left"><?php echo $history['status']; ?></td>
        <td class="left"><?php echo $history['comment']; ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
  <?php } ?>
  <div class="buttons">
    <div class="left"><a href="<?php echo $continue; ?>" class="button_pink"><span><?php echo $button_continue; ?></span></a></div>
  </div>
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?> 