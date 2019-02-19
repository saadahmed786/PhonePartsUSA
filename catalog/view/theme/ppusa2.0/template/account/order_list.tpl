
<?php if (isset($ischild)) { ?>
<div class="row">
          <div class="col-md-8 col-sm-7 col-xs-12 text-sm-center">
            <h3 class="uppercase blue-title">Recent Orders</h3>
          </div>
          <div class="col-md-4  col-sm-5 text-right all-voucher text-sm-center col-xs-12">
            <a href="<?php echo $this->url->link('account/order', '', 'SSL');?>" class="btn btn-primary mb-xs-20">view all Orders</a>
          </div>
        </div>
        <div class="parent"></div>

        
        <?php 
        $k=0;
        foreach ($orders  as $order) { ?>
        <div class="row recent-orders-row" style="border:1px solid #e6e6e6">
        <div class="col-md-4 col-sm-6 recent-order col-xs-6">
          
          <div class="col-md-6 col-xs-6 pull-left text-center heading">Order Date<p><?php echo $order['date_added']; ?></p></div>
          <div class="col-md-6 col-xs-6 text-right text-center heading">Order No<p style="cursor:pointer" onmouseover="$(this).css('text-decoration','underline');" onmouseout="$(this).css('text-decoration','none');" onclick="window.location='<?php echo $this->url->link('account/order/info', 'order_id=' . $order['order_id'], 'SSL'); ?>'"><?php echo $order['order_id'];?></p></div>
          <div class="col-md-12"> <h1 class="text-center fontsize45"> <?php echo $order['total']; ?><br><span class="uppercase" style="font-size:20px"><?php echo $order['status']; ?></span> </h1></div>

          
          
        </div>
        <div class="col-md-3 col-sm-6 col-xs-6">
         
          <div class="track">
            <h6 style="color:#333333;font-weight:bold;">Shipping Address: </h6>
            <p><?php echo $order['name'];?> </p>
            <p><?php echo $order['shipped_to']; ?> </p>
            <p><?php echo $order['city'] . ', ' . $order['zone'] . ', ' . $order['postcode']; ?> </p>
            <h6 style="color:#333333;font-weight:bold">Service: </h6>
            <p><?php echo ($order['tracking_service']) ? $order['tracking_service']: 'N/A'; ?></p>
            <h6 style="color:#333333;font-weight:bold">Tracking No:</h6>
            <p><?php echo ($order['tracking_id'])? $order['tracking_id']: 'N/A'; ?></p></div>
            
            
          </div>
           
            <div class="h-180 col-md-5 col-sm-12 col-xs-12 tracking-updates ">
              <div class="scroll2" style="margin-top:0px !important">
              <div class="col-md-12 update">
                <h6 style="color:#333333;font-weight:bold;">Tracking Updates:</h6>
          <?php 
          if($order['tracking'])
          {
          foreach ($order['tracking'] as $tracking) { ?>
                                  <?php foreach ($tracking['tracking_info'] as $track) { ?>
                <p style="color:#030303;font-weight:300"><?php echo date('m/d/y h:i A', strtotime($track['datetime'])); ?> &nbsp; <?php echo $track['message']; ?></p>
                <?php } ?>
                                  <?php }
                                }
                                else
                                {
                                  echo '<p style="color:#030303;font-weight:300">No Tracking Record Found</p>';
                                }

                                   ?>

               
                </div>
              </div>
            </div>
            </div>
            <?php
            $k++;
          }
          ?>



<?php } else { ?>
<?php echo $header; ?>

<main class="main">
  <div class="container history-detail-page">
    <div class="white-box overflow-hide">
      <div class="row">
        <div class="col-md-12">
          <div class="row inline-block">
            <div class="col-md-2 white-box-left pr0 inline-block">
              <div class="white-box-inner">
                <a href="<?php echo $dashboard; ?>" style="font-size:10px" class="btn btn-primary mt40 mb40">back to Dashboard</a>
              </div>
              <div class="border"></div>
              <div class="white-box-inner mt40">
                <p class="text-center">
                  <a href="<?php echo $return; ?>" class="uppercase blue underline">request return</a>
                </p>
              </div>
            </div>
            <div class="col-md-10 white-box-right inline-block pd30">
              <div class="text-center">
                <h2 class='uppercase mt40'>Order History</h2>
              </div>
              <div class="row">
                <div class="col-md-10 col-md-offset-1">
                  <div class="row text-center">

                    <?php if ($orders) { ?>
                    <?php 
        $k=0;
        foreach ($orders  as $order) { ?>
        <div class="row recent-orders-row" style="border:1px solid #e6e6e6">
        <div class="col-md-4 col-sm-6 recent-order col-xs-6">
          
          <div class="col-md-6 col-xs-6 pull-left text-center heading">Order Date<p class="fontsize18"><?php echo $order['date_added']; ?></p></div>
          <div class="col-md-6 col-xs-6 text-right text-center heading">Order No<p style="cursor:pointer;" class="fontsize18" onmouseover="$(this).css('text-decoration','underline');" onmouseout="$(this).css('text-decoration','none');" onclick="window.location='<?php echo $this->url->link('account/order/info', 'order_id=' . $order['order_id'], 'SSL'); ?>'"><?php echo $order['order_id'];?></p></div>
          <div class="col-md-12"> <h1 class="text-center fontsize45"> <?php echo $order['total']; ?><br><span class="uppercase" style="font-size:20px"><?php echo $order['status']; ?></span> </h1></div>

          
          
        </div>
        <div class="col-md-3 col-sm-6 col-xs-6">
         
          <div class="track">
            <h6 style="color:#333333;font-weight:bold;">Shipping Address: </h6>
            <p><?php echo $order['name'];?> </p>
            <p><?php echo $order['shipped_to']; ?> </p>
            <p><?php echo $order['city'] . ', ' . $order['zone'] . ', ' . $order['postcode']; ?> </p>
            <h6 style="color:#333333;font-weight:bold">Service: </h6>
            <p><?php echo ($order['tracking_service']) ? $order['tracking_service']: 'N/A'; ?></p>
            <h6 style="color:#333333;font-weight:bold">Tracking No:</h6>
            <p><?php echo ($order['tracking_id'])? $order['tracking_id']: 'N/A'; ?></p></div>
            
            
          </div>
           
            <div class="h-180 col-md-5 col-sm-12 col-xs-12 tracking-updates ">
              <div class="scroll2">
              <div class="col-md-12 update">
                <h6 style="color:#333333;font-weight:bold;">Tracking Updates:</h6>
          <?php 
          if($order['tracking'])
          {
          foreach ($order['tracking'] as $tracking) { ?>
                                  <?php foreach ($tracking['tracking_info'] as $track) { ?>
                <p style="color:#030303;font-weight:300"><?php echo date('m/d/y h:i A', strtotime($track['datetime'])); ?> &nbsp; <?php echo $track['message']; ?></p>
                <?php } ?>
                                  <?php }
                                }
                                else
                                {
                                  echo '<p style="color:#030303;font-weight:300">No Tracking Record Found</p>';
                                }

                                   ?>

               
                </div>
              </div>
            </div>
            </div>
            <?php
            $k++;
          }
          ?>
                    <?php } else { ?>
                    <p>No orders found</p>
                    <?php } ?>
                 <div class="row">
        <div class="col-sm-12 text-center"><?php echo $pagination; ?></div>
        </div>

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
<style>
.white-box p
{
  margin-bottom:0px !important;
  line-height: 21px !important;
}
</style>