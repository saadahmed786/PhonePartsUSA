<?php echo $header;?>
<style>
.track-list li {
	font-size:13px;
}
.current-voucher .track-list:before {
content: none !important;
	}
</style>
<?php $total = 0.00; ?>
				<?php foreach ($products as $product) { 
					$total = $total + $product['price'];
				}

					?>
<div id="feedback-pop" class="popup">
	<div class="popup-head">
		<h2 class="blue-title uppercase">Return Information</h2>
		</div>
		
	</div>
	<!-- @End of header -->
	<main class="main">
		<div class="container history-detail-page">
			<div class="white-box overflow-hide">
				<div class="row">
					<div class="col-md-12 table-cell">
						<div class="row inline-block">
							<!-- <div class="col-md-4 white-box-left pr0 inline-block">
								<div class="white-box-inner">
									<a class="btn btn-primary mt40 mb40" href="<?php echo $this->url->link('account/account');?>">back to account history</a>
								</div>
								<div class="border"></div>

								<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
							</div> -->
							<div class="col-md-12 white-box-right pr0 inline-block pd30">
								<h4 class='uppercase mt40'>return details</h4>


								<div class="row recent-orders-row" style="border:1px solid #e6e6e6">
        <div class="col-md-4 col-sm-12 recent-order col-xs-12">
          
          <div class="col-md-6 col-xs-6 pull-left text-center heading">Date Added<p><?php echo $return_info['date_added']; ?></p></div>
          <div class="col-md-6 col-xs-6 text-right text-center heading">RMA #<p  ><?php echo $return_info['rma_number'];?></p></div>
          <div class="col-md-12"> <h1 class="text-center fontsize45"> <?php echo $this->currency->format($total); ?><br><span class="uppercase" style="font-size:20px"><?php echo $return_info['rma_status']; ?></span> </h1></div>

          
          
        </div>
        
           
            <div class=" col-md-8 col-sm-12 col-xs-12 tracking-updates " style="margin-top:0px !important;">
                <div class="hidden-md hidden-lg hidden-xs-12"><h3 class="blue-title">Please use Desktop version to see the details.</h3></div>
              <div class="col-md-12 update hidden-xs">
            
                    
                      <div class="order-box row mr0 table current-voucher voc" style="margin-top:0px !important;">
                      <div class="col-md-5 col-xs-3 order-col table-cell">
                          <ul class="track-list list-inline">
                            <li class="heading">Product Name</li>
                            
                          </ul>
                        </div>
                        <div class="col-md-5 col-xs-5 order-col table-cell">
                          <ul class="track-list list-inline">
                            <li class="heading">Model</li>
                            <li class="heading">Result</li>
                          </ul>
                        </div>
                        <div class="col-md-2 col-xs-2 order-col table-cell">
                          <ul class="track-list list-inline">
                            
                            <li class="heading">Price</li>
                          </ul>
                        </div>
                      
                    
                      </div>
                      
                      <?php foreach($products as $product)
                      {
                            if($product['decision'] == 'Issue Credit'){
                        $product['decision'] = 'Credit';
                      } else if($product['decision'] == 'Issue Refund'){
                        $product['decision'] = 'Refund';
                      }  else if ($product['decision'] == 'Issue Replacement'){
                        $product['decision'] = 'Replaced';
                      }
                        ?>
                          <div class="order-box row mr0 table current-voucher vocc" style="margin-top:0px !important;">
                      
                        <div class="col-md-5 col-xs-3 order-col table-cell">
                          <ul class="track-list list-inline">
                            <li class="heading" style="width:100%;text-align:left !important;"><?php echo $product['title'];?></li>
                           
                          </ul>
                        </div>

                        <div class="col-md-5 col-xs-5 order-col table-cell">
                          <ul class="track-list list-inline">
                            <li class="heading"> <?php echo $product['sku'];?> x <?php echo $product['quantity'];?></li>
                            <li class="heading"><?php echo $product['decision'];?></li>
                          </ul>
                        </div>

                        <div class="col-md-2 col-xs-2 order-col table-cell">
                          <ul class="track-list list-inline">
                            
                            <li class="heading"><?php echo $this->currency->format($product['price']);?></li>
                          </ul>
                        </div>
                      
                    
                      </div>
                      <?php
                    }
                    ?>
                 

               
                </div>
             
            </div>
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