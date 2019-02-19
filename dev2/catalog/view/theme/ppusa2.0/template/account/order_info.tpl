<?php echo $header;?>
<div id="feedback-pop" class="popup">
  <div class="popup-head">
    <h2 class="blue-title uppercase">Order Feedback Form</h2>
  </div>
  <div class="popup-body">
    <h5 class="blue-title">Order Number <?php echo $order_id; ?></h5>
    <textarea class="form-control" placeholder="Please Enter your Feedback"></textarea>
    <div class="text-right popup-btns">
      <button class="btn btn-primary" type="submit">Post Feedback</button>
    </div>
  </div>
</div>
  <!-- @End of header -->
  <main class="main">
    <div class="container history-detail-page">
      <div class="white-box overflow-hide">
        <div class="row">
          <div class="col-md-9 table-cell">
            <div class="row inline-block">
              <div class="col-md-4 white-box-left pr0 inline-block">
                <div class="white-box-inner">
                  <a class="btn btn-primary mt40 mb40" href="<?php echo $this->url->link('account/account');?>">back to account history</a>
                </div>
                <div class="border"></div>
                <div class="white-box-inner mt40">
                  <p class="text-center">
                    <a href="<?php echo $this->url->link('account/return/insert','order_id='.$order_id);?>" class="uppercase blue underline">request return</a>
                  </p>
                  <p class="text-center hidden">
                    <a href="#feedback-pop" class="fancybox uppercase blue underline">feedback</a>
                  </p>
                </div>
                <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
              </div>
              <div class="col-md-8 white-box-right pr0 inline-block pd30">
                <h4 class='uppercase mt40'>order details</h4>
                <div class="row download-row =">
                  <div class="col-md-6">Your number is <a href="#" class="fw-700"><?php echo $order_id; ?></a></div>
                  <div class="col-md-6 "><i class="fa fa-print"></i><a href="index.php?route=account/order/printout&order_id=<?php echo $order_id;?>" target="_blank" class="underline">Download Invoice</a></div>
                  <!-- <div class="col-md-3 pl0 hidden"><i class="fa fa-file-pdf-o"></i><a href="imp/email_invoice.php?order_id=<?php echo $order_id; ?>&action=view&theme=ppusa2.0" target="_blank" class="underline">Download PDF</a></div> -->
                </div>
                <div class="transit-detail">
                  <h4>Order details: <a href="javascript:void(0);"><span><?php echo $checkout_order_info['order_status'];?></span><a></h4>
                </div>
                <div class="blue-border mb40 <?php echo $process_class;?>"></div>
                <p>
                  You may want to write this down for your records, but we will be sending a confirmation by e-mail as well. If any of your order information is incorrect, contact us immediately. Be sure to include your order number and issue. We hope your items work out great for you, but in case they don’t we’ll give you a brief summary of our <a href="<?php echo $this->url->link('information/information', 'information_id=' . $this->config->get('config_account_id'));?>" class="underline blue">Return Policy</a> Thanks again for your order, good luck with your repairs!

                  
                </p>
                <p>
                  Contact Us: <a href="mailto:help@phonepartusa.com" class="blue underline">help@phonepartusa.com</a> or 855.213.5588
                </p>
                <div class="border"></div>
                <div class="shoping-cart-box cart-product-small mb40">
                <!-- Product detail row -->
                <?php foreach ($products as $product) { ?>
                  <div class="product-detail row">
                    <div class="product-detail-inner clearfix">
                      <div class="col-md-2 product-detail-img">
                        <div class="image"><img style="cursor:pointer" onclick="window.location='<?php echo $product['href'];?>" src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>"></div>
                      </div>
                      <div class="col-md-10 product-detail-text">
                        <h3><a href="<?php echo $product['href'];?>"><?php echo $product['name']; ?></a></h3>
                        <div class="table mb0">
                          <div class="row">
                            <div class="col-md-12">
                              <p class="item-qty">Qty: <?php echo $product['quantity']; ?> at <?php echo $product['price']; ?></p>
                              <div class="cart-total-wrp">
                                <div class="cart-total text-right">
                                  <h3><span>Item Total:</span><?php echo $product['total']; ?></h3>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php } ?>
                </div>  
              </div>
            </div>
          </div>
          <div class="col-md-3 table-cell">
            <div class="sidebox overflow-hide inline-block w100">
              <h4>order summary </h4>
              <p class="sidebox-lbl">Contact info</p>
              <ul class="info-list">
                <li><?php echo $checkout_order_info['firstname'];?> <?php echo $checkout_order_info['lastname'];?> </li>
                <li><?php echo $checkout_order_info['telephone'];?> </li>
                <li><?php echo $customer_email?></li>
              </ul>
              <div class="line"></div>
              <p class="sidebox-lbl">Billing method</p>
                      <ul class="info-list">
                        <li><?php echo $payment_method?></li>
                      </ul>
              <span class="line"></span>
              <p class="sidebox-lbl">Billing address</p>
              <ul class="info-list">
                <li><?php echo $payment_address?></li>
              </ul>
              <span class="line"></span>
              <p class="sidebox-lbl">Shipping address</p>
              <ul class="info-list">
                <li><?php echo $shipping_address?></li>
              </ul>
              <span class="line"></span>
              <div class="cart-total-row row">
                <div class="col-xs-6">
                  <p class="total-lbl">Sub-total</p>
                </div>
                <div class="col-xs-6">
                  <p class="total-price text-right"><?php echo $this->currency->format($order_total);?></p>
                </div>
              </div>
              <br>
              <p class="sidebox-lbl">Shipping method </p>
              <ul class="info-list">
                <li><?php echo $shipping_method?></li>
              </ul>
              <?php if($shipping_cost != 0){ ?>
              <span class="line mt5"></span>
              <div class="cart-total-row row">
                <div class="col-xs-6">
                  <p class="total-lbl">Shipping </p>
                </div>
                <div class="col-xs-6">
                  <p class="total-price text-right"><?php echo $this->currency->format($shipping_cost);?></p>
                </div>
              </div>
              <?php } ?>
              <span class="line"></span>
              <div class="cart-total-row row">
                <div class="col-xs-8">
                  <p class="total-lbl">Discount Code</p>
                </div>
                <div class="col-xs-4">
                <?php 
                foreach($totals as $total){ 
                //$discount_total = $discount_total + $total['value'];
                //$complete_total = $complete_total - $total['value'];
                } ?>
                  <p class="total-price text-right">- <?php echo number_format($checkout_order_info['voucher_total'],2);?></p>
                </div>
              </div>
              <ul class="info-list">
              <?php foreach($voucher_codes as $code){ ?>
                <li><?php echo $code['code'];?></li>
              <?php } ?>
              </ul>
              <span class="line"></span>
              <div class="cart-total-row row">
                <div class="col-xs-8">
                  <p class="total-lbl">TOTAL</p>
                </div>
                <div class="col-xs-4">
                  <p class="total-price text-right"><?php echo $this->currency->format($checkout_order_info['total']);?></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main><!-- @End of main -->
<?php echo $footer;?>
<!-- @End of footer -->