<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $language; ?>" xml:lang="<?php echo $language; ?>">
<head>
  <title><?php echo $title; ?></title>
  <base href="<?php echo $base; ?>" />
  <?php
  if(!isset($_GET['pos_view']))
  {
    ?>
    <link rel="stylesheet" type="text/css" href="view/stylesheet/invoice.css" />
    <?php
  }
  ?>
</head>
<body>
  <?php foreach ($orders as $order) { ?>
  <div style="page-break-after: always;">
   <?php
   if(isset($_GET['pos_view']))
   {
     ?>
     <div style="font-family:Arial" ><div style="float:left"><a href="#" title="<?php echo $order['store_name']; ?>"><img height="" src="<?php echo HTTP_IMAGE . $this->config->get('config_logo'); ?>"  style=" border: none;" /></a></div><div style="float:right"><h1>Invoice</h1></div>
     <div style="clear:both"></div>
     <table style="border-collapse: collapse; width: 100%; border-top: <?php echo $auto_border;?> solid #DDDDDD; border-left: solid #DDDDDD; margin-bottom: <?php echo $auto_margin;?>;">
      <thead>
        <tr>
          <td style=" border-right:  solid #DDDDDD; border-bottom:  solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left;  color: #222222;" colspan="2">Order Details</td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="	border-right:  solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; text-align: left; padding: <?php echo $auto_pad;?>;"><b>Order ID:</b> <?php echo $order['order_id']; ?><br />
            <b>Date Added:</b> <?php echo $order['date_added']; ?><br />
            <b>Payment Method:</b> <?php echo $order['payment_method']; ?><br />
            <?php if ($order['shipping_method']) { ?>
            <b>Shipping Method:</b> <?php echo $order['shipping_method']; ?>
            <?php } ?></td>
            <td style="font-size: <?php echo $auto_tfont;?>;	border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; text-align: left; padding: <?php echo $auto_pad;?>;"><b>Email:</b> <?php echo $order['email']; ?><br />
              <b>Telephone:</b> <?php echo $order['telephone']; ?>

            </td>

          </tr>
        </tbody>
      </table>
      <?php if ($order['comment']) { ?>
      <table style="border-collapse: collapse; width: 100%; border-top: <?php echo $auto_border;?> solid #DDDDDD; border-left: <?php echo $auto_border;?> solid #DDDDDD; margin-bottom: <?php echo $auto_margin;?>;">
        <thead>
          <tr>
            <td style="font-size: <?php echo $auto_tfont;?>; border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: <?php echo $auto_pad;?>; color: #222222;">Associate Comments</td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="font-size: <?php echo $auto_tfont;?>;	border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; text-align: left; padding: <?php echo $auto_pad;?>;"><?php echo $order['comment']; ?></td>
          </tr>
        </tbody>
      </table>
      <?php } ?>
      <table style="border-collapse: collapse; width: 100%; border-top: <?php echo $auto_border;?> solid #DDDDDD; border-left: <?php echo $auto_border;?> solid #DDDDDD; margin-bottom: <?php echo $auto_margin;?>;">
        <thead>
          <tr>
            <td style="font-size: <?php echo $auto_tfont;?>; border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: <?php echo $auto_pad;?>; color: #222222;">Payment Address</td>
            <?php if ($order['shipping_address']) { ?>
            <td style="font-size: <?php echo $auto_tfont;?>; border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: <?php echo $auto_pad;?>; color: #222222;">Shipping Address</td>
            <?php } ?>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="font-size: <?php echo $auto_bfont;?>;	border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; text-align: left; padding: <?php echo $auto_pad;?>;"><?php echo $order['payment_address']; ?></td>
            <?php if ($order['shipping_address']) { ?>
            <td style="font-size:<?php echo $auto_bfont;?>;	border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; text-align: left; padding: <?php echo $auto_pad;?>;"><?php echo $order['shipping_address']; ?></td>
            <?php } ?>
          </tr>
        </tbody>
      </table>
      <table style="border-collapse: collapse; width: 100%; border-top: <?php echo $auto_border;?> solid #DDDDDD; border-left: <?php echo $auto_border;?> solid #DDDDDD; margin-bottom: <?php echo $auto_margin;?>;">
        <thead>
          <tr>
            <td style="font-size: <?php echo $auto_cfont;?>; border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: <?php echo $auto_pad;?>; color: #222222;">Product</td>
            <td style="font-size: <?php echo $auto_cfont;?>; border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: <?php echo $auto_pad;?>; color: #222222;">Model</td>
            <td style="font-size: <?php echo $auto_cfont;?>; border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: <?php echo $auto_pad;?>; color: #222222;">Quantity</td>
            <td style="font-size: <?php echo $auto_cfont;?>; border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: <?php echo $auto_pad;?>; color: #222222;">Price</td>
            <td style="font-size: <?php echo $auto_cfont;?>; border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: <?php echo $auto_pad;?>; color: #222222;">Total</td>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($order['product'] as $product) { ?>
          <?php
          //Putting Signature Product to the End of the Recipt
          if ($product['model'] == 'SIGN') {
            $sign = '<tr>';
            $sign .= '<td style="font-size: '.$auto_cfont.';  border-right: '.$auto_border.' solid #DDDDDD; border-bottom: '.$auto_border.' solid #DDDDDD; text-align: left; padding: '.$auto_pad.';">';
            $sign .= $product['name'];
            $sign .= '</td>';
            $sign .= '<td style="font-size: '.$auto_cfont.';  border-right: '.$auto_border.' solid #DDDDDD; border-bottom: '.$auto_border.' solid #DDDDDD; text-align: left; padding: '.$auto_pad.';">'.$product['model'].'</td>';
            $sign .= '<td style="font-size: '.$auto_cfont.';  border-right: '.$auto_border.' solid #DDDDDD; border-bottom: '.$auto_border.' solid #DDDDDD; text-align: right; padding: '.$auto_pad.';">'.$product['quantity'].'</td>';
            $sign .= '<td style="font-size: '.$auto_cfont.';  border-right: '.$auto_border.' solid #DDDDDD; border-bottom: '.$auto_border.' solid #DDDDDD; text-align: right; padding: '.$auto_pad.';">'.$product['price'].'</td>';
            $sign .= '<td style="font-size: '.$auto_cfont.';  border-right: '.$auto_border.' solid #DDDDDD; border-bottom: '.$auto_border.' solid #DDDDDD; text-align: right; padding: '.$auto_pad.';">'.$product['total'].'</td>';
            $sign .= '</tr>';
          } else {
            ?>
            <tr>
              <td style="font-size: <?php echo $auto_cfont;?>;	border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; text-align: left; padding: <?php echo $auto_pad;?>;">
                <?php echo $product['name']; ?>
                <?php foreach ($product['option'] as $option) { ?>
                <br />
                &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
                <?php } ?></td>
                <td style="font-size: <?php echo $auto_cfont;?>;	border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; text-align: left; padding: <?php echo $auto_pad;?>;"><?php echo $product['model']; ?></td>
                <td style="font-size: <?php echo $auto_cfont;?>;	border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; text-align: right; padding: <?php echo $auto_pad;?>;"><?php echo $product['quantity']; ?></td>
                <td style="font-size: <?php echo $auto_cfont;?>;	border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; text-align: right; padding: <?php echo $auto_pad;?>;"><?php echo $product['price']; ?></td>
                <td style="font-size: <?php echo $auto_cfont;?>;	border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; text-align: right; padding: <?php echo $auto_pad;?>;"><?php echo $product['total']; ?></td>
              </tr>
              <?php
            }
          } 
          ?>
          <?php foreach ($order['voucher'] as $voucher) { ?>
          <tr style="">
            <td style="font-size: <?php echo $auto_cfont;?>;	border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; text-align: left; padding: <?php echo $auto_pad;?>;"><?php echo $voucher['description']; ?></td>
            <td style="font-size: <?php echo $auto_cfont;?>;	border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; text-align: left; padding: <?php echo $auto_pad;?>;"></td>
            <td style="font-size: <?php echo $auto_cfont;?>;	border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; text-align: right; padding: <?php echo $auto_pad;?>;">1</td>
            <td style="display:none;font-size: <?php echo $auto_cfont;?>;	border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; text-align: right; padding: <?php echo $auto_pad;?>;"><?php echo $voucher['amount']; ?></td>
            <td style="font-size: <?php echo $auto_cfont;?>;	border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; text-align: right; padding: <?php echo $auto_pad;?>;"><?php echo $voucher['amount']; ?></td>
          </tr>
          <?php } ?>
          <!-- Putting Signature in the End -->
          <?php echo $sign; ?>
        </tbody>
        <tfoot>
          <?php foreach ($order['total'] as $total) { ?>
          <tr style="">
            <td style="font-size: <?php echo $auto_cfont;?>;	border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; text-align: right; padding: <?php echo $auto_pad;?>;" colspan="4"><b><?php echo $total['title']; ?>:</b></td>
            <td style="font-size: <?php echo $auto_cfont;?>;	border-right: <?php echo $auto_border;?> solid #DDDDDD; border-bottom: <?php echo $auto_border;?> solid #DDDDDD; text-align: right; padding: <?php echo $auto_pad;?>;"><?php echo $total['text']; ?></td>
          </tr>
          <?php } ?>
        </tfoot>
      </table>


    </div>
    <?php
  }
  else
  {
   ?>
   <h1><?php echo $text_invoice; ?></h1>
   <table class="store">
    <tr>
      <td><?php echo $order['store_name']; ?><br />
        <?php echo $order['store_address']; ?><br />
        <?php echo $text_telephone; ?> <?php echo $order['store_telephone']; ?><br />
        <?php if ($order['store_fax']) { ?>
        <?php echo $text_fax; ?> <?php echo $order['store_fax']; ?><br />
        <?php } ?>
        <?php echo $order['store_email']; ?><br />
        <?php echo $order['store_url']; ?></td>
        <td align="right" valign="top"><table>
          <tr>
            <td><b><?php echo $text_date_added; ?></b></td>
            <td><?php echo $order['date_added']; ?></td>
          </tr>
          <?php if ($order['invoice_no']) { ?>
          <tr>
            <td><b><?php echo $text_invoice_no; ?></b></td>
            <td><?php echo $order['invoice_no']; ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td><b><?php echo $text_order_id; ?></b></td>
            <td><?php echo $order['order_id']; ?></td>
          </tr>
          <tr>
            <td><b><?php echo $text_payment_method; ?></b></td>
            <td><?php echo $order['payment_method']; ?></td>
          </tr>
          <?php if ($order['shipping_method']) { ?>
          <tr>
            <td><b><?php echo $text_shipping_method; ?></b></td>
            <td><?php echo $order['shipping_method']; ?></td>
          </tr>
          <?php } ?>
        </table></td>
      </tr>
    </table>
    <table class="address">
      <tr class="heading">
        <td width="50%"><b><?php echo $text_to; ?></b></td>
        <td width="50%"><b><?php echo $text_ship_to; ?></b></td>
      </tr>
      <tr>
        <td><?php echo $order['payment_address']; ?><br/>
          <?php echo $order['email']; ?><br/>
          <?php echo $order['telephone']; ?>
          <?php if ($order['payment_company_id']) { ?>
          <br/>
          <br/>
          <?php echo $text_company_id; ?> <?php echo $order['payment_company_id']; ?>
          <?php } ?>
          <?php if ($order['payment_tax_id']) { ?>
          <br/>
          <?php echo $text_tax_id; ?> <?php echo $order['payment_tax_id']; ?>
          <?php } ?></td>
          <td><?php echo $order['shipping_address']; ?></td>
        </tr>
      </table>
      <table class="product">
        <tr class="heading">
          <td><b><?php echo $column_product; ?></b></td>
          <td><b><?php echo $column_model; ?></b></td>
          <td align="right"><b><?php echo $column_quantity; ?></b></td>
          <td align="right"><b><?php echo $column_price; ?></b></td>
          <td align="right"><b><?php echo $column_total; ?></b></td>
        </tr>
        <?php foreach ($order['product'] as $product) { ?>
        <tr>
          <td><?php echo $product['name']; ?>
            <?php foreach ($product['option'] as $option) { ?>
            <br />
            &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
            <?php } ?></td>
            <td><?php echo $product['model']; ?></td>
            <td align="right"><?php echo $product['quantity']; ?></td>
            <td align="right"><?php echo $product['price']; ?></td>
            <td align="right"><?php echo $product['total']; ?></td>
          </tr>
          <?php } ?>
          <?php foreach ($order['voucher'] as $voucher) { ?>
          <tr>
            <td align="left"><?php echo $voucher['description']; ?></td>
            <td align="left"></td>
            <td align="right"></td>
            <td align="right"><?php //echo $voucher['amount']; ?></td>
            <td align="right"><?php //echo $voucher['amount']; ?></td>
          </tr>
          <?php } ?>

          <?php foreach ($order['total'] as $total) { 
            if ($total['code'] == 'voucher') {
              ?>
              <tr>
                <td align="right" colspan="4"><b><?php echo $total['title']; ?>:</b></td>
                <td align="right"><?php echo $total['text']; ?></td>
              </tr>
              <?php
            } else {
              ?>
              <tr>
                <td align="right" colspan="4"><b><?php echo $total['title']; ?>:</b></td>
                <td align="right"><?php echo $total['text']; ?></td>
              </tr>
              <?php
            }
          }
          ?>
        </table>
        <?php if ($order['comment']) { ?>
        <table class="comment">
          <tr class="heading">
            <td><b><?php echo $column_comment; ?></b></td>
          </tr>
          <tr>
            <td><?php echo $order['comment']; ?></td>
          </tr>
        </table>
        <?php } ?>
        <img alt="testing" src="../barcode.php?text=<?php echo $order['order_id']; ?>&size=80" /><br>
        <img alt="testing" src="../barcode.php?text=S<?php echo $order['order_id']; ?>&size=80" /><br>
        <?php
      }
      ?>
    </div>
    <?php } ?>
  </body>
  </html>