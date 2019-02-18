<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>
  <div class="middle">
    <?php if ($success) { ?>
    <div class="success"><?php echo $success; ?></div>
    <?php } ?>
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <div class="content">
    
      <table width="100%">
        <tr>
          <td width="33.3%" valign="top"><?php if ($shipping_method) { ?>
            <b><?php echo $text_shipping_method_short; ?></b><br />
            <?php echo $shipping_method; ?><br />

            <br />
            <?php } ?>
            <b><?php echo $text_payment_method_short; ?></b><br />
            <?php echo $payment_method; ?><br /></td>
          <td width="33.3%" valign="top"><?php if ($shipping_address) { ?>
            <b><?php echo $text_shipping_address; ?></b><br />
            <?php echo $shipping_address['firstname'] ?> <?php echo $shipping_address['lastname'] ?><br/>
            <?php echo $shipping_address['address_1'] ?><br/>
            <?php if ($shipping_address['address_2']) echo $shipping_address['address_2'] . "<br/>"; ?>
            <?php echo $shipping_address['city'] ?> , <?php echo $shipping_address['zone'] ?> <?php echo $shipping_address['postcode'] ?><br/>
            <?php echo $shipping_address['country'] ?><br />

            <?php } ?></td>
          <td width="33.3%" valign="top"><b><?php echo $text_payment_address; ?></b><br />
            <?php echo $payment_address['firstname'] ?> <?php echo $payment_address['lastname'] ?><br/>
            <?php echo $payment_address['address_1'] ?><br/>
            <?php if ($payment_address['address_2']) echo $payment_address['address_2'] . "<br/>"; ?>
            <?php echo $payment_address['city'] ?>,  <?php echo $payment_address['zone'] ?>  <?php echo $payment_address['postcode'] ?><br/>
            <?php echo $payment_address['country'] ?><br /></td>
        </tr>
      </table>
    </div>
    <div class="content">
      <table width="100%">
	    <thead>
	      <tr>
	        <td class="name" colspan="2"><b><?php echo $column_name; ?></b></td>
	        <td class="model"><b><?php echo $column_model; ?></b></td>
	        <td class="quantity"><b><?php echo $column_quantity; ?></b></td>
	        <td class="price"><b><?php echo $column_price; ?></b></td>
	        <td class="total"><b><?php echo $column_total; ?></b></td>
	      </tr>
	    </thead>
	    <tbody>
	      <?php foreach ($products as $product) { ?>
	      <tr>
	      	<td width="50"><img src="<?php echo $product['image']; ?>" /></td>
	        <td class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
	          <?php foreach ($product['option'] as $option) { ?>
	          <br />
	          &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
	          <?php } ?></td>
	        <td class="model"><?php echo $product['model']; ?></td>
	        <td class="quantity"><?php echo $product['quantity']; ?></td>
	        <td class="price"><?php echo $product['price']; ?></td>
	        <td class="total"><?php echo $product['total']; ?></td>
	      </tr>
	      <?php } ?>
	      <?php foreach ($vouchers as $voucher) { ?>
	      <tr>
	        <td class="name"><?php echo $voucher['description']; ?></td>
	        <td class="model"></td>
	        <td class="quantity">1</td>
	        <td class="price"><?php echo $voucher['amount']; ?></td>
	        <td class="total"><?php echo $voucher['amount']; ?></td>
	      </tr>
	      <?php } ?>
	    </tbody>
	    <tfoot>
	      <?php foreach ($totals as $total) { ?>
	      <tr>
	        <td colspan="5" class="price" align="right" style="padding-right: 5px;"><b><?php echo $total['title']; ?>:</b></td>
	        <td class="total"><?php echo $total['text']; ?></td>
	      </tr>
	      <?php } ?>
	    </tfoot>
	  </table>
    </div>

    <?php if ($comment) { ?>
    <b style="margin-bottom: 2px; display: block;"><?php echo $text_comment; ?></b>
    <div class="content"><?php echo $comment; ?></div>
    <?php } ?>
    <div class="payment"><?php echo $payment; ?></div>
  </div>
  <div class="bottom">
    <div class="left"></div>
    <div class="right"></div>
    <div class="center"></div>
  </div>
</div>
<script type="text/javascript"><!--

//--></script>
<?php echo $footer; ?>