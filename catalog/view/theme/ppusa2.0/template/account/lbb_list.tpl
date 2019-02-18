<?php if (isset($ischild)) { ?>

<div class="tab-inner pd60" style="margin-top:38px">
                  <div class="row">
                    <div class="col-md-8 col-sm-7 col-xs-12 text-sm-center">
                      <h3 class="uppercase blue-title">Recent LCD 
                      BUYBACKS</h3>
                    </div>
                    <div class="col-md-4  col-sm-5 text-right all-voucher text-xs-center col-xs-12">
                      <a href="<?php echo $this->url->link('account/lbb', '', 'SSL');?>" class="btn btn-primary mb-xs-20">view all LCD buybacks</a>
                    </div>
                  </div>
                  <div class="parent"></div>
                  <div class="">
                    
                      <div class="order-box row mr0 table current-voucher voc">
                      <div class="col-md-6 col-xs-6 order-col table-cell">
                          <ul class="track-list list-inline">
                            <li class="heading">Order Date</li>
                            <li class="heading">Order No.</li>
                          </ul>
                        </div>
                        <div class="col-md-6 col-xs-6 order-col table-cell">
                          <ul class="track-list list-inline">
                            <li class="heading">Status</li>
                            <li class="heading">Total</li>
                          </ul>
                        </div>
                      
                    
                      </div>
                      <div class="scroll2">
                      <?php foreach($lbbs as $lbb)
                      {

                        ?>
                          <div class="order-box row mr0 table current-voucher vocc">
                      <div class="col-md-6 col-xs-6 order-col table-cell">
                          <ul class="track-list list-inline">
                            <li class="heading"><?php echo $lbb['date_added'];?></li>
                            <li class="heading" style="cursor:pointer" onmouseover="$(this).css('text-decoration','underline');" onmouseout="$(this).css('text-decoration','none');" onclick="window.location='<?php echo $this->url->link('account/lbb/info', 'buyback_id=' . $lbb['buyback_id'], 'SSL'); ?>'"><?php echo $lbb['shipment_number'];?></li>
                          </ul>
                        </div>
                        <div class="col-md-6 col-xs-6 order-col table-cell">
                          <ul class="track-list list-inline">
                            <li class="heading"><?php echo $lbb['status'];?></li>
                            <li class="heading"><?php echo $this->currency->format($lbb['total']);?></li>
                          </ul>
                        </div>
                      
                    
                      </div>
                      <?php
                    }
                    ?>
                      
                      
              
                    </div>
                  </div>
                </div>

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
              <h2 class='uppercase mt40'>LCD BuyBAck</h2>
              </div>
              <div class="row">
                <div class="col-md-10 col-md-offset-1">
                  <div class="row text-center">
                    <?php if ($lbbs) { ?>
                    <table class="table table-bordered lbbMain fontsize13">
                      <thead>
                        <tr>
                          <th>Date Added</th>
                          <th>Shipment No</th>
                          <th>Status</th>
                          <th>Items Recived</th>
                        </tr>
                      </thead>
                    <?php foreach ($lbbs  as $lbb) { ?>
                      <tbody>
                        <tr>
                          <td><?php echo $lbb['date_added']; ?></td>
                          <td><a href="<?php echo $this->url->link('account/lbb/info', 'buyback_id=' . $lbb['buyback_id'], 'SSL'); ?>" >#<?php echo $lbb['shipment_number']; ?></a></td>
                          <td><?php echo $lbb['status']; ?></td>
                          <td><?php echo $lbb['total']; ?></td>
                        </tr>
                      </tbody>
                    <?php } ?>
                    </table>
                    <?php } else { ?>
                    <p>No Store Credit issued or applied yet</p>
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