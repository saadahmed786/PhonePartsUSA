<?php foreach ($returns as $return) { ?>
                      <div class="order-box table mb0 row mr0">
                        <div class="col-md-4 order-col table-cell">
                          <ul class="track-list list-inline">
                            <li>Order date: </li>
                            <li><?php echo $return['date_added']; ?></li>
                          </ul>
                          <ul class="track-list list-inline">
                            <li>Order #:  </li>
                            <li><?php echo $return['rma_number']; ?></li>
                          </ul>
                          <ul class="track-list list-inline">
                            <li>Order Total:</li>
                            <li>$<?php echo $return['total']; ?></li>
                          </ul>
                        </div>
                        <div class="col-md-4 order-col table-cell">
                          <ul class="track-list list-inline">
                            <li>Return reson:</li>
                            <li>Defective</li>
                          </ul>
                          <ul class="track-list list-inline">
                            <li>Status:</li>
                            <li><?php echo $return['rma_status']; ?></li>
                          </ul>
                        </div>
                        <div class="col-md-4 order-col table-cell text-center v-middle">
                          <a href="account-history-detail.php" class="btn btn-primary">view details</a>
                        </div>
                      </div>
 <?php } ?>