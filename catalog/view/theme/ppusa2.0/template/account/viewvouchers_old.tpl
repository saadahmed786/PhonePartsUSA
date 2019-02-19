<?php if (isset($ischild)) { ?>

<?php if ($vouchers) { ?>
<?php foreach ($vouchers  as $voucher) { ?>
<div class="order-box row mr0 table current-voucher">
  <div class="col-md-4 order-col table-cell">
    <ul class="track-list list-inline">
      <li>Date:</li>
      <li><?php echo date('m/d/y h:i a', strtotime($voucher['date']));?></li>
    </ul>
  </div>
  <div class="col-md-4 order-col table-cell">
    <ul class="track-list list-inline">
      <li>Voucher #:</li>
      <li><?php echo $voucher['code'];?></li>
    </ul>
  </div>
  <div class="col-md-4 order-col table-cell">
    <ul class="track-list list-inline">
      <li>Remaining Balance:</li>
      <li><?php echo $this->currency->format($voucher['balance']);?></li>
    </ul>
  </div>
</div>
<?php } ?>
<?php } else { ?>
<div class="order-box row mr0 table current-voucher">
  No Store Credit issued or applied yet
</div>
<?php } ?>

<?php } else { ?>
<?php echo $header; ?>
<main class="main">
  <div class="container history-detail-page">
    <div class="white-box overflow-hide">
      <div class="row">
        <div class="col-md-12">
          <div class="row inline-block">
            <div class="col-md-3 white-box-left pr0 inline-block">
              <div class="white-box-inner">
                <a href="<?php echo $dashboard; ?>" class="btn btn-primary mt40 mb40">back to Dashboard</a>
              </div>
              <div class="border"></div>
            </div>
            <div class="col-md-9 white-box-right inline-block pd30">
              <div class="text-center">
                <h2 class='uppercase mt40'>Credit Vouchers</h2>
              </div>
              <div class="row vouchers">
                <div class="col-md-10 col-md-offset-1">
                  <div class="row text-center">
                    <?php if ($vouchers) { ?>
                    <?php foreach ($vouchers  as $voucher) { ?>
                    <div class="col-md-6 <?php echo ($voucher['balance']>0)? '': 'used'; ?>">
                      <div class="col-md-12 voucher">
                        <div class="row">
                          <div class="col-md-6">
                            <small>Date</small>
                            <p><?php echo date('m/d/y', strtotime($voucher['date']));?></p>
                          </div>
                          <div class="col-md-6">
                            <small>Voucher No</small>
                            <p><?php echo $voucher['code'];?></p>
                          </div>
                        </div>
                        <div class="row amount">
                          <h4><?php echo $this->currency->format($voucher['balance']);?></h4>
                          <p>(<?php echo $this->currency->format($voucher['amount']);?>)</p>
                        </div>
                        <div class="row apply">
                          <h3><?php if ($voucher['balance']>0) { ?><a href="<?php echo $this->url->link('checkout/cart/use_voucher', 'code='.$voucher['code'], 'SSL');?>">Apply</a><?php } else { ?>&nbsp;<?php } ?></h3>
                        </div>
                      </div>
                    </div>
                    <?php } ?>
                    <?php } else { ?>
                    <p>No Store Credit issued or applied yet</p>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php echo $footer; ?>
<?php } ?>