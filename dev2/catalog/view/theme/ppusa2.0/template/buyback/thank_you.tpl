<?php echo $header; 
?>
<main class="main">
	<div class="container lcd-buy-signout-page">
		<div class="row row-centered hidden-xs">
			<div class="col-md-12 intro-head col-centered">
				<div class="text-center">
					<div class="blue-box lbb-head-small">
						<div class="row">
							<div class="col-md-3 lbbnav">
								<!-- <span class="counter">1</span> -->
								<img src="image/lbb-page-form-white.png" alt="Fill Form">
								<h3>Fill Out the Form</h3>
							</div>
							<div class="col-md-3 lbbnav">
								<!-- <span class="counter">2</span> -->
								<img src="image/lbb-page-loc-white.png" alt="Fill Form">
								<h3>Shipping Address</h3>
							</div>
							<div class="col-md-3 lbbnav current">
								<!-- <span class="counter">3</span> -->
								<img src="image/lbb-page-pack-blue.png" alt="Fill Form">
								<h3>Pack Them Up</h3>
							</div>
							<div class="col-md-3 lbbnav" style="display:none">
								<span class="counter">4</span>
								<img src="image/lbb-page-done-white.png" alt="Fill Form">
								<h3>Get Funded</h3>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="white-box overflow-hide">
			
			<div class="row step3">
				<div class="col-md-12">
					<div class="row">
					<div class="col-md-6 text-center hidden-xs">
						<img src="image/img_process.png" alt="complete" style="width:52%;margin-top:5px">
						</div>
					<div class="col-md-6 col-xs-12 text-sm-center">
						<h1 class="uppercase" style="position:relative;top:70px">Buyback submission complete</h1>
						</div>

					</div>
					</div>
					<div class="row lbbThanks">
						<div class="col-md-6">
							<div class="blue-box lbb-label">
								<div class="row">
									<div class="col-md-9">
										<div class="lbb-number">
											<h3>LBB # <?php echo $detail['shipment_number']; ?></h3>
											<span class="uppercase">Submitted : <?php echo date($this->language->get('date_format_short'), strtotime($detail['date_added'])); ?></span>
										</div>
										<h4 class="uppercase"><?php echo $address['firstname'] . ' ' . $address['lastname']; ?></h4>
										<h4 class="uppercase"><?php echo $address['address_1']; ?></h4>
										<h4 class="uppercase"><?php echo $address['city'] . ', ' . $address['zone'] . ', ' . $address['postcode']; ?></h4>
										<h4 class="uppercase">United States</h4>
									</div>
									<div class="col-md-3">
										<div class="postcard uppercase text-center">
											Postage Required
										</div>
									</div>
								</div>
							</div>
							<br><br>
							<div class="text-center">
								<button class="btn btn-primary big" onclick="printThis();"><i class="fa fa-print"></i>Print Label</button>
							</div>
						</div>
						<div class="col-md-6 lbb-details">
							<ol>
								<li>Print PDF and affix BuyBack Label on your shipment. <span>This is not a prepaid shipping label. Customers submitting 25+ LCDs will receive a free FEDEX Ground shipping label within 1 business day to the email provided.</span> </li>
								<li>Prices quoted are valid for only 5 business days after form submission.</li>
								<li>Contact <a href="mailto:buyback@phonepartsusa.com">buyback@phonepartsusa.com</a> with questions about this submission.</li>
								
							</ol>
							<div class="text-center" style="display:none">
								<button class="btn btn-primary big" onclick="lbbdone();" >Next Step</button>
							</div>
						</div>
					</div>
					<div class="product-img" id="printarea" style="display:none" ><img src="<?php echo $image_path_new;?>" style="width:100%;">
					</div>
				</div>
			</div>

			<div class="row step4" >
				<div class="col-md-12">
					<div class="text-center">
						<h1 class="uppercase">Processing Information</h1>
					</div>
					<div class="row">
						<div class="col-md-6 lbb-details">
						<?php
						if($detail['option']=='Return')
						{
							$detail['option'] = 'shipped back';
						}
						else
						{
							$detail['option'] = 'recycled';	
						}
						?>	
							<ol>
								<li>We require 3-5 business days for testing for fund disbursement. </li>
								<!-- <li>Funds will be dispersed via (customers selection)</li> -->
								<li>Rejected screens will be <?php echo $detail['option'];?></li>
								<li>If you would like to change any of these preferences, please contact us immediately via email.</li>
							</ol>
						</div>
						<div class="col-md-6 text-center hidden-xs">
							<img src="image/img_cash.png" alt="cash">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main><!-- @End of main -->

<script>
$(document).ready(function(){
<?php
if($created_ticket==true)
{
?>
$.ajax({
				url: 'imp/freshdesk/ticket_buyback.php',
				type: 'POST',
				dataType: 'json',
				data: {name: '<?=$name;?>', email: '<?=$email;?>',body:'<?=$fresh_body;?>',lbb_number:'<?php echo $lbb_number;?>',total_lcd:'<?php echo $total_lcd;?>',action: 'create'},
			}).always(function(json) {
				if (json['success']) {
				// console.log('here');
				} else {
					
				}
			});
			<?php
		}
		?>
});

</script>



<script>
$(document).ready(function(){
 $('html, body').animate({
        scrollTop: $(".step3").offset().top
    }, 2000);
});
	function printThis()
	{

		var mywindow = window.open("", "LBB Print", "height=400,width=400");
		mywindow.document.write("<html><head><title>LBB Print</title>");

		mywindow.document.write("</head><body style='width:100%'>");
		mywindow.document.write($("#printarea").html());
		mywindow.document.write("</body></html>");

		mywindow.document.close(); // necessary for IE >= 10
		mywindow.focus(); // necessary for IE >= 10

		setTimeout(function () {
			mywindow.print();
			mywindow.close();

			return true;	
		}, 4000);

	}
</script>
<script type="text/javascript">
	function lbbdone () {
		$('.step3').toggle('fast');
		$('.step4').toggle('slow');
		$('.current').find('img').attr('src', 'image/lbb-page-pack-white.png');
		$('.current').removeClass('current').next('.lbbnav').addClass('current').find('img').attr('src', 'image/lbb-page-done-blue.png');
	}
</script>
<?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>