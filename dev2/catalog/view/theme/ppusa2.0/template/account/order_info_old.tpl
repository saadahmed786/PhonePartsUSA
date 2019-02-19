<?php if (isset($ischild)) { ?>
<?php foreach ($orders as $order) { ?>
<div class="order-box row mr0">
  <div class="col-md-4 order-col table-cell">
    <ul class="track-list list-inline">
      <li>Order date: </li>
      <li><?php echo $order['date_added']; ?></li>
    </ul>
    <ul class="track-list list-inline">
      <li>Order #:  </li>
      <li><?php echo $order['order_id']; ?></li>
    </ul>
    <ul class="track-list list-inline">
      <li>Order Total:</li>
      <li><?php echo $order['total']; ?></li>
    </ul>
  </div>
  <div class="col-md-4 order-col table-cell">
    <ul class="track-list list-inline">
      <li>Shipped to:</li>
      <li><?php echo $order['shipped_to']; ?>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</li>
    </ul>
    <ul class="track-list hidden-xs hidden-sm"></ul>
    <ul class="track-list list-inline">
      <li>Status:</li>
      <li><?php echo $order['status']; ?></li>
    </ul>
  </div>
  <div class="col-md-4 order-col table-cell text-center v-middle">
    <a class="btn btn-primary" href="<?php echo $order['href']; ?>">view details</a>
  </div>
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
              <div class="white-box-inner mt40">
                <p class="text-center">
                  <a href="<?php echo $return; ?>" class="uppercase blue underline">request return</a>
                </p>
              </div>
            </div>
            <div class="col-md-9 white-box-right inline-block pd30">
              <div class="text-center">
                <h2 class='uppercase mt40'>Order History</h2>
              </div>
              <div class="row">
                <div class="col-md-10 col-md-offset-1">
                  <div class="row text-center">

                    <?php if ($orders) { ?>
                    <table class="table ordermain">
                      <?php foreach ($orders  as $order) { ?>
                      <tbody>
                        <tr>
                          <td>
                            <div class="row orderDetails">
                              <div class="col-md-12">
                                <div class="row">
                                  <div class="col-md-6">
                                    <small>Order Date</small>
                                    <p><?php echo $order['date_added']; ?></p>
                                  </div>
                                  <div class="col-md-6">
                                    <small>Order No</small>
                                    <p><?php echo $order['order_id'];?></p>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-12">
                                    <h2><?php echo $order['total']; ?></h2>
                                    <p class="uppercase"><?php echo $order['status']; ?></p>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </td>
                          <td>
                            <div class="row orderShipping">
                              <div class="col-md-11 text-left col-md-offset-1">
                                <h4>Shipping Address:</h4>
                                <p><?php echo $order['name']; ?></p>
                                <p><?php echo $order['shipped_to']; ?></p>
                                <p><?php echo $order['city'] . ', ' . $order['zone'] . ', ' . $order['postcode']; ?></p>
                                <h4>Service:</h4>
                                <p><?php echo ($order['tracking_service']) ? $order['tracking_service']: 'N/A'; ?></p>
                                <h4>Tracking No:</h4>
                                <p><?php echo ($order['tracking_id'])? $order['tracking_id']: 'N/A'; ?></p>
                              </div>
                            </div>
                          </td>
                          <td>
                            <div class="row orderTracking">
                              <div class="col-md-11 text-left col-md-offset-1">
                                <div class="scroll">
                                  <h4>Tracking Updates:</h4>
                                  <?php foreach ($order['tracking'] as $tracking) { ?>
                                  <?php foreach ($tracking['tracking_info'] as $track) { ?>
                                  <p><span><?php echo date('m/d/y h:i A', strtotime($track['datetime'])); ?></span> <span><?php echo $track['message']; ?></span></p>
                                  <?php } ?>
                                  <?php } ?>
                                </div>
                              </div>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                      <?php } ?>
                    </table>
                    <?php } else { ?>
                    <p>No orders found</p>
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