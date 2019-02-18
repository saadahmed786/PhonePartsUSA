
                <?php if (isset($ischild)) { ?>

<div class="tab-inner pd60" style="margin-top:38px">
                  <div class="row">
                    <div class="col-md-8 col-sm-7 col-xs-12 text-sm-center ">
                      <h3 class="uppercase blue-title">Recent Return History</h3>
                    </div>
                    <div class="col-md-4  col-sm-5 text-right all-voucher text-sm-center col-xs-12">
                      <a href="<?php echo $this->url->link('account/returns', '', 'SSL');?>" class="btn btn-primary mb-xs-20">view all Returns</a>
                    </div>
                  </div>
                  <div class="parent"></div>
                  <div class="">
                    
                      <div class="order-box row mr0 table current-voucher voc">
                      <div class="col-md-6 col-xs-6 order-col table-cell">
                          <ul class="track-list list-inline">
                            <li class="heading">RMA Generated</li>
                            <li class="heading">RMA No.</li>
                          </ul>
                        </div>
                        <div class="col-md-6 col-xs-6 order-col table-cell">
                          <ul class="track-list list-inline">
                            <li class="heading">Total</li>
                            <li class="heading">Status</li>
                          </ul>
                        </div>
                      
                    
                      </div>

                      <div class="scroll2">
                      <?php foreach ($returns as $return) { ?>
                          <div class="order-box row mr0 table current-voucher vocc">
                      <div class="col-md-6 col-xs-6 order-col table-cell">
                          <ul class="track-list list-inline">
                            <li class="heading"><?php echo $return['date_added'];?></li>
                            <li class="heading" style="cursor:pointer" onmouseover="$(this).css('text-decoration','underline');" onmouseout="$(this).css('text-decoration','none');" onclick="window.location='<?php echo $this->url->link('account/returns/info', 'return_id=' . $return['return_id'], 'SSL'); ?>'"><?php echo $return['rma_number']; ?></li>
                          </ul>
                        </div>
                        <div class="col-md-6 col-xs-6 order-col table-cell">
                          <ul class="track-list list-inline">
                            <li class="heading"><?php echo $this->currency->format($return['total']); ?></li>
                            <li class="heading"><?php echo $return['rma_status']; ?></li>
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
                <a href="<?php echo $continue; ?>" class="btn btn-primary mt40 mb40">back to Dashboard</a>
              </div>
              <div class="border"></div>
            </div>
            <div class="col-md-9 white-box-right inline-block pd30">
              <div class="text-center">
              <h2 class='uppercase mt40'>Returns History</h2>
              </div>
              <div class="row">
                <div class="col-md-10 col-md-offset-1">
                  <div class="row text-center">
                    <?php if ($returns) { ?>
                    <table class="table table-bordered lbbMain">
                      <thead>
                        <tr>
                          <th>RMA Generated</th>
                          <th>RMA No.</th>
                          <th>Total</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                    <?php foreach ($returns  as $return) { ?>
                      <tbody>
                        <tr>
                          <td><?php echo $return['date_added']; ?></td>
                          <td><a href="<?php echo $return['href']; ?>" ><?php echo $return['rma_number']; ?></a></td>
                          <td><?php echo $this->currency->format($return['total']); ?></td>
                          <td><?php echo $return['rma_status']; ?></td>
                        </tr>
                      </tbody>
                    <?php } ?>
                    </table>
                    <?php } else { ?>
                    <p>No Returns Generated yet</p>
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

