<?php if (isset($ischild)) { ?>
<?php foreach ($lbbs as $lbb) { ?>
<div class="order-box table mb0 row mr0">
  <div class="col-md-4 order-col table-cell">
    <ul class="track-list list-inline">
      <li>Buy Back Date: </li>
      <li><?php echo $lbb['date_added']; ?></li>
    </ul>
    <ul class="track-list list-inline">
      <li>Buy Back #:  </li>
      <li><?php echo $lbb['shipment_number']; ?></li>
    </ul>
    <ul class="track-list list-inline">
      <li>Total Credit:</li>
      <li><?php echo $lbb['total']; ?></li>
    </ul>
  </div>
  <div class="col-md-4 order-col table-cell">
    <ul class="track-list list-inline">
      <li>Status:</li>
      <li><?php echo $lbb['status']; ?></li>
    </ul>
  </div>
  <div class="col-md-4 order-col table-cell text-center v-middle">
    <a href="#" class="btn btn-primary">view details</a>
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
            </div>
            <div class="col-md-9 white-box-right inline-block pd30">
              <div class="text-center">
              <h2 class='uppercase mt40'>LCD BuyBAck</h2>
              </div>
              <div class="row">
                <div class="col-md-10 col-md-offset-1">
                  <div class="row text-center">
                    <?php if ($lbbs) { ?>
                    <table class="table table-bordered lbbMain">
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
                          <td>#<?php echo $lbb['shipment_number']; ?></td>
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
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php echo $footer; ?>
<?php } ?>