<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<main class="main">
  <div class="container">

    <div class="row row-centered">
      <div class="col-md-10 intro-head col-centered shippingDetails">
        <div class="text-center">
          <div class="row">
            <h1 class="blue blue-title uppercase"><?php echo $heading_title; ?></h1>
            <h3 class="uppercase">The Following shipping rates apply for shipments within the United States.</h3>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="jumbotron">
                <div class="row">
                  <div class="text-left">

                  <p><span class="fw-700" style="vertical-align:baseline" >Business Day</span> - Monday, Tuesday, Wednesday, Thursday, Friday</p>

                    <p><span class="fw-700" style="vertical-align:baseline">USPS Saturday</span> is considered a delivery day, however, not a transit day.</p>

                    <p><span class="fw-700" style="vertical-align:baseline">UPS and Fedex</span> do not deliver on Saturday or Sunday, unless, Saturday delivery option is selected (extra fees apply).</p>

                    <p><span class="fw-700" style="vertical-align:baseline">Store Pick Up</span> - Orders may be picked up from our Las Vegas, Nevada Facility. Please allow 30 Minutes for picking, QC Testing and Processing. A $5.00 pick-up fee will be applied to all orders for walk-in and unapproved accounts.</p>

                    <p><span class="fw-700" style="vertical-align:baseline">Same Day Processing and Shipping</span> - Orders with UPS/Fedex Shipping Methods are processed and Shipped the same day if placed by Orders 4:00PM PST. Orders with USPS Shipping methods are processed and shipped within 24-48 business hours.</p>

                  </div>
                </div>
              </div>
              <table class="table table-bordered">
                <tr style="background-color: #6188FC">
                  <td class="hidden-xs"></td>
                  <td style="vertical-align: bottom !important;"><h3 style="color: #ffffff">Shipping Type</h3></td>
                  <td style="vertical-align: bottom !important;"><h3 style="color: #ffffff">Regular Rate</h3></td>
                  <td><h3 class="double" style="color: #ffffff">Free Shipping<br><small style="color: #ffffff" >(on orders above)</small></h3></td>
                  <td style="vertical-align: bottom !important;"><h3 style="color: #ffffff">Delivery Time</h3></td>
                </tr>
                <tbody>
                  <tr class="border-top border-bottom">
                    <td class="hidden-xs">&nbsp;</td>
                    <td>Store Pick Up*</td>
                    <td>$0.00</td>
                    <td>---</td>
                    <td>Same Business Day</td>
                  </tr>
                  <tr class="border-top">
                    <td rowspan="3" class="hidden-xs"><img alt="united States" src="image/logo__1.png" height="175px" /></td>
                    <td>Standard Shipping</td>
                    <td>$6.99</td>
                    <td>&nbsp;</td>
                    <td>7 - 10 Business Days</td>
                  </tr>
                  <tr>
                    <td>USPS Priority Mail</td>
                    <td>$11.99</td>
                    <td>$500</td>
                    <td>3 - 4 Business Days</td>
                  </tr>
                  <tr class="border-bottom">
                    <td>USPS Express Mail</td>
                    <td>$23.99</td>
                    <td>$1000</td>
                    <td>2 - 3 Business Days<br><small>3rd Day Guaranteed</small></td>
                  </tr>
                  <tr class="border-top">
                    <td class="hidden-xs"><img alt="united States" src="image/logo__2.png" height="100px" /></td>
                    <td>UPS/Fedex Ground</td>
                    <td>$11.99</td>
                    <td>$500</td>
                    <td>1 - 5 Business Days</td>
                  </tr>
                  <!-- <tr class="border-bottom">
                    <td colspan="4"><small>UPS / FedEx GROUND IS ONLY AVAILABLE FOR: ARIZONA, CALIFORNIA, COLORADO, NEVADA, WYOMING, UTAH</small></td>
                  </tr> -->
                  <tr class="border-top border-bottom">
                    <td class="hidden-xs"><img alt="united States" src="image/logo__3.png" height="100px" /></td>
                    <td>Fedex Express Saver</td>
                    <td>$15.99</td>
                    <td>$500</td>
                    <td>3 Business Days<br><small>3rd Day Guaranteed</small></td>
                  </tr>
                  <tr class="border-top">
                    <td rowspan="3" class="hidden-xs"><img alt="united States" src="image/logo__4.png" height="100px" /></td>
                    <td>UPS/Fedex 2nd Day Air</td>
                    <td>$16.99 - $19.99</td>
                    <td>$750</td>
                    <td>2 Business Days<br><small>2nd Day Guaranteed</small></td>
                  </tr>
                  <tr>
                    <td>UPS/Fedex Next Day</td>
                    <td>$21.99 - $27.99</td>
                    <td>$1000</td>
                    <td>1 Business Day<br><small>Next Day Guaranteed</small></td>
                  </tr>
                  <tr class="border-bottom">
                    <td>UPS/Fedex Next Day<br>(Saturday Delivery)</td>
                    <td>$49.99</td>
                    <td>$2000</td>
                    <td>1 Day</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>


    </div>
  </main>

<!-- <div id="content"><?php echo $content_top; ?>

  <h1><?php echo $heading_title; ?></h1>
  <?php echo $description; ?>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button_pink"><span><?php echo $button_continue; ?></span></a></div>
  </div>
  <?php echo $content_bottom; ?></div> -->

  <?php echo $footer; ?>