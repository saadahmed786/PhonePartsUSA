<?php if ($payment_methods) { ?>
<div class="pamentMethod-head">
						   		<div class="row">
						        	<div class="col-lg-4 col-md-5">
						        		<h3>Payment method:</h3>
						        	</div>
						        	<?php
						        	$it = 0;
						        	$colspan = 7;
						        	foreach ($payment_methods as $payment_method) {
						        		if($colspan>=3)
						        		{	
						        			$col = 3;
						        			$colspan = $colspan-3;
						        		}
						        		else
						        		{
						        			$col = 2;
						        			$colspan = $colspan-2;
						        		}
						        		if(strtolower($payment_method['title'])=='cash or credit at store pick-up'){
						        			$payment_method['title'] = 'Pay @ Pickup';
						        		}
						        		if(strtolower($payment_method['title'])=='credit/debit card'){
						        			$payment_method['title'] = 'Credit Card';
						        		}
						        		
						        	 ?>
						        	<div class="col-lg-<?php echo $col;?> col-md-4 <?php echo ($it==0?'col-xs-7':'col-xs-5');?>">
						        	<?php if ($payment_method['code'] == $code || !$code) { ?>
      <?php $code = $payment_method['code']; ?>
						        		  <input type="radio" name="payment_method" class="css-radio2" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" checked="checked" />
						        		 <?php } else { ?>
						        		 <input type="radio" name="payment_method" class="css-radio2" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>"  />
						        		 <?php
						        		}
						        		?>
						        		<label for="<?php echo $payment_method['code']; ?>" class="css-radio2"><?php echo $payment_method['title']; ?></label>
						        	</div>
						        	<?php
						        	$it++;
						        }
						        ?>
						        	
						        </div>
					        </div>
					        <?php
					    }
					    ?>
					        <div class="payment-address" style="padding-bottom:0px" id="payment-details-checkout">	
						        
						    </div>
						    <div class="row">
								<div class="col-md-12">
									 <div class="text-right prev-next-btns greybg">
										<!-- <a href="#" class="btn btn-primary"><i class="fa fa-angle-left"></i>Previous Step</a> -->
										<a href="javascript:void();" id="confirm-payment-method" class="btn btn-info light">Next Step <i class="fa fa-angle-right"></i></a>
									</div>
								</div>
							</div>     
							<script>
							$(document).ready(function(){
								
           <?php
           if(isset($this->session->data['ppx']['token']))
           {
           	?>
           	
           	$('#confirm-payment-method').trigger('click');
           	<?php
           }
           ?>
							});

							</script>