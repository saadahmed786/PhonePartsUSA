<?php if (isset($ischild)) { ?>

<?php if ($vouchers) { ?>

<div class="row">
        <?php foreach ($vouchers  as $voucher) { ?>
        <?php
        $applied = false;
        $used = false;
        if(isset($this->session->data['voucher'][$voucher['code']]))
        {
          $applied = true;
        }
        if($voucher['balance']<=0)
        {
          $used = true;
        }
        ?>
        <div class="col-md-4 col-xs-6 col-sm-6 recent-orderr <?php if ($used or $applied) { echo 'image-transperant';}?>" >
        <div class="col-md-12 order">
          <div class="col-md-6 col-xs-6 pull-left text-center heading">Date<p><?php echo date('m/d/Y', strtotime($voucher['date']));?></p></div>
          <div class="col-md-6 col-xs-6 text-right text-center heading">Voucher #<p><?php echo $voucher['code'];?></p></div>
          <div class="col-md-12"> <h1 class="text-center fontsize45"><?php echo $this->currency->format($voucher['amount']);?><br><span style="font-size:20px">(<?php echo $this->currency->format($voucher['balance']);?>)</span> </h1></div>

        </div>
        <div class="band text-center"><a href="javascript:void(0);" <?php if(!$used and !$applied) {?> onclick="applyVoucher('<?php echo $voucher['code'];?>',this);" <?php } ?>><?php echo ($used?'Used':($applied?'Applied':'Apply'));?></a></div>
        </div> 
        <?php
      }

      ?>
            
            
        
        



          </div>

<?php } else { ?>
<div class="order-box row mr0 table current-voucher">
  No Available Store Credit Voucher(s)
</div>
<?php } ?>

<?php } else { ?>
<?php echo $header; ?>
<main class="main">
  <div class="container history-detail-page">
  <div class="alert alert-danger alert-dismissible" style="display:none" role="alert"></div>
    <div class=" overflow-hide">
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
                    <?php
        $applied = false;
        $used = false;
        if(isset($this->session->data['voucher'][$voucher['code']]))
        {
          $applied = true;
        }
        if($voucher['balance']<=0)
        {
          $used = true;
        }
        ?>
                    <div class="col-md-6 <?php if($applied or $used) { echo 'used';}?>">
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
                          <h3><?php if ($applied or $used) { ?>&nbsp;<?php } else { ?><a href="<?php echo $this->url->link('checkout/cart/use_voucher', 'code='.$voucher['code'], 'SSL');?>" style="font-size:77%">Apply</a><?php } ?></h3>
                        </div>
                      </div>
                    </div>
                    <?php } ?>
                    <?php } else { ?>
                    <p>No Available Store Credit Voucher(s)</p>
                    <?php } ?>
                  </div>
                </div>
              </div>

              <div class="row">
        <div class="col-sm-12 text-center"><?php echo $pagination; ?></div>
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