<?php echo $header;?>
<?php
$my_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<style type="text/css">
	.hide-questions{
		display: none;
	}
</style>
<div id="review-pop" class="popup">
	<div class="popup-head">
		<h2 class="blue-title uppercase subtitle"><?php echo $heading_title;?></h2>
		<span>SKU:<?php echo $model;?></span>
	</div>
	<div class="popup-body">
		<p>My Rating</p>
		<fieldset class="rating clearfix">
    <input type="radio" id="star5" name="rating" value="5" /><label class = "full" for="star5" title="5 stars"></label>
    <!-- <input type="radio" id="star4half" name="rating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label> -->
    <input type="radio" id="star4" name="rating" value="4" /><label class = "full" for="star4" title="4 stars"></label>
    <!-- <input type="radio" id="star3half" name="rating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label> -->
    <input type="radio" id="star3" name="rating" value="3" /><label class = "full" for="star3" title="3 stars"></label>
    <!-- <input type="radio" id="star2half" name="rating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label> -->
    <input type="radio" id="star2" name="rating" value="2" /><label class = "full" for="star2" title="2 stars"></label>
    <!-- <input type="radio" id="star1half" name="rating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label> -->
    <input type="radio" id="star1" name="rating" value="1" /><label class = "full" for="star1" title="1 star"></label>
    <!-- <input type="radio" id="starhalf" name="rating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label> -->
</fieldset>
<p></p>
	<h5 class="blue-title" style="clear:both">Name</h5>
	<input type="text"  placeholder="Name" name="name" class="form-control" />


		<h5 class="blue-title" style="clear:both">My Review</h5>
		<textarea class="form-control" name="text" placeholder="Please Enter Your Review"></textarea>
		<h5 class="blue-title" style="clear:both">Captcha</h5>
		<input type="text" name="captcha" class="form-control" value="" />
  <br />
  <img src="index.php?route=product/product/captcha" alt="" id="captcha" /><br />
  <br />
		<div class="text-right popup-btns">
			<button class="btn btn-primary" id="button-review" type="submit">Post Rating &amp; Review</button>
		</div>
	</div>
</div>
<main class="main">
		<div class="container cart-page">
			<div class="row">
				<div class="col-md-9 main-content">
					<ul class="breadcrum clearfix">
						<!-- <li class="backlist"><a href="#">Back to List</a></li> -->
						<!-- <li class="seprator">|</li>
						<li><a href="#">Home</a></li>
						<li class="seprator">></li>
						<li><a href="#">Repair Parts</a></li>
						<li class="seprator">></li>
						<li><a href="#">Apple </a></li>
						<li class="seprator">></li>
						<li><a href="#">iPhone 6 Screen Assembly with LCD &amp; Digitizer</a></li> -->

						<?php 
							$b=0;
						foreach ($breadcrumbs as $breadcrumb) { ?>
  <?php echo(($b>0) ? '<li class="seprator">></li>' : ''); ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li> 
  <?php
  	$b++;
   } ?>
					</ul>
					<!--@End breadcrum -->
					<h2><?php echo $heading_title; ?></h2>
					<div class="row cart-detail">
						<div class="cart-items col-md-5">
							<div class="big-image">
								<img id="img_01" data-zoom-image="<?php echo $popup;?>" src="<?php echo $thumb;?>" alt="">
							</div>
							<div class="review-area">
								<ul class="review-stars clearfix">
									<?php
									if($rating==0)
									{
										$rating=5;
									}
							for($i=1;$i<=$rating;$i++)
							{
								?>
								<li class="fill"><a href="javascript:void(0);"><i class="fa fa-star"></i></a></li>
								<?php
							}
							?>
							<?php
							for($j=$i;$j>$i;$j--)
							{
								?>
								<li><a href="javascript:void(0)"><i class="fa fa-star"></i></a></li>
								<?php
							}
							?>
								</ul>
								<a href="#" onClick="$('html,body').animate({scrollTop: $('#write-review').offset().top},'slow');" class="underline review-links"><?php   $explode_reviews = explode(" ", $reviews); echo $explode_reviews[0]." ".$explode_reviews[1];  ?></a>
								<a href="#review-pop"  class="underline review-links fancybox">Write a review</a>
								<!--@End review stars -->
							</div>
							<!--@End review area -->
							<div class="cart-thumbs row">
							<?php foreach ($images as $image) { ?>
								<div class="col-xs-3">
									<div class="image" data-bigImg="<?php echo $image['popup']; ?>"><img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></div>
								</div>
								
								<?php
							}
							?>
							</div>
							<!--@End cart thumbs -->
							<ul class="social-shares">
								<li class="share-alt">
									<i class="fa fa-share-alt"></i>
									Share:
								</li>
								<li class="email">
									<a href="mailto:?Subject=  <?php echo $heading_title;?>&Body=<?php echo htmlentities($my_url);?>"><img src="catalog/view/theme/ppusa2.0/images/icons/envelope.png" alt=""></a>
								</li>
								<!-- <li class="print">
									<a href="#"><img src="catalog/view/theme/ppusa2.0/images/icons/printer.png" alt=""></a>
								</li> -->
								<li class="twitter login_popup">
									<a href="javascript:void(0)" data-href="https://twitter.com/share?url=<?php echo $my_url;?>&amp;text=<?php echo $heading_title;?>" ><i class="fa fa-twitter"></i></a>
								</li>
								<li class="facebook login_popup">
									<a href="javascript:void(0)" data-href="http://www.facebook.com/sharer.php?u=<?php echo $my_url;?>&title=<?php echo $heading_title;?>" ><i class="fa fa-facebook"></i></a>
								</li>
								<li class="google-plus login_popup">
									<a href="javascript:void(0)" data-href="https://plus.google.com/share?url=<?php echo $my_url;?>" ><img src="catalog/view/theme/ppusa2.0/images/icons/googleplus.png" alt=""></a>
								</li>
								<li class="pinterest">
									<a href="javascript:void((function()%7Bvar%20e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)%7D)());"><i class="fa fa-pinterest-p"></i></a>
								</li>
							</ul>
							<!--@End social shares -->
							<article class="about-cart">
								<?php echo $tab_description; ?>
							</article>	
						</div>
						<div class="cart-descp col-md-5">
							<div class="row">
								<div class="col-md-6 item-features">
									<h4>Product specs:</h4>
									<ul>
										<li>
											<span class="lbl">Sku:</span>
											<span class="text"><?php echo $model;?></span>
										</li>
										<?php
										// print_r($attributes);exit;
									foreach($attributes as $attr)
									{
										?>
										<li>
											<span class="lbl"><?php echo $attr['main_name'];?>:</span>
											<span class="text"><?php echo $attr['name'];?></span>
										</li>
										
										<?php
									}
									?>
									</ul>
								</div>
								<div class="col-md-6 item-features">
									<h4>Compatibile phone  models:</h4>
									<ul>
										<li>
											iPhone 6  A1549
										</li>
										<li>
											iPhone 6  A1586
										</li>
										<li>
											iPhone 6  A1589
										</li>
									</ul>
								</div>
							</div>
							<?php
								if($replacement_for)
								{

									?>
							<div class="row">
								
								
									<div class="col-md-6 item-features">
									<h4>Repacement for </h4>
									<ul>
									<?php
									foreach($replacement_for as $replacement)
									{
										?>
										<li><?php echo $replacement['name'];?></li>
										<?php
									}
									?>
										
										
									</ul>
								</div>
									
								
							</div>
							<?php
								}
								?>
							<?php
							if ($stock == 'In Stock') {

								?>
							<div class="instock">

								<h3><i class="fa fa-check"></i><?php echo $stock;?></h3>
								<ul class="instock-list checklist clearfix">
									<li><i class="fa fa-check"></i><span>Free shipping on orders abve $500</span></li>
									<li><i class="fa fa-check"></i><span>Leaves Our US Warehouse Within 24 Hours</span></li>
									<li><i class="fa fa-check"></i><span>Lowest Prices Online Guaranteed</span></li>
									<li><i class="fa fa-check"></i><span>60 Day Return Policy</span></li>
								</ul>
							</div>
							<?php
						}
						else
						{
							?>
							<div class="outstock">

								<h3><i class="fa fa-close"></i><?php echo $stock;?></h3>
								
							</div>
							<?php
						}
						?>
							<!--@End Instock -->
							<div class="row favt-wrap" style="display:none">
								<div class="col-md-7 col-xs-6">
									<select class="selectpicker">
										<option data-content="
										  <img src='catalog/view/theme/ppusa2.0/images/icons/iphone-icon.png'>
											</span>
										  <span style='display:inline-block; width:100px;'>  Black Grade A $105.00</span>">
										  Black Grade A $105.00
										</option>
										<option data-content="
										  <img src='catalog/view/theme/ppusa2.0/images/icons/iphone-icon.png'>
											</span>
										  <span style='display:inline-block; width:100px;'>  Black Grade A $105.00</span>">
										  Black Grade A $105.00
										</option>
									</select>
								</div>
								<div class="col-md-5 col-xs-6">
									<span class="favorite"><i class="fa fa-heart"></i><a href="#" class="underline">Favorite</a></span>
								</div>
							</div>
							<div class="row cart-total-wrp">
								<div class="col-md-7 cart-total text-center">
									<div class="qtyt-box">
										<div class="input-group spinner">
											<span class="txt">QTY</span>
										    <input type="text" name="quantity" class="form-control" value="<?php echo $minimum;?>" onChange="getProductPrice()">
										    <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
										    <div class="input-group-btn-vertical">
										      <button class="btn " type="button" onclick="QtyChange('+');"><i class="fa fa-plus"></i></button>
										      <button class="btn" type="button" ><i class="fa fa-plus"></i></button>
										    </div>

										 </div>
									</div>
									<h3 class="qty"><?php echo $price;?></h3>
									<?php
									if($stock=='In Stock')
									{


									?>
									<button class="btn btn-success addtocart" onclick="addToCartpp2('<?php echo $product_id;?>', $('input[name=quantity]').val())"><img src="catalog/view/theme/ppusa2.0/images/icons/basket.png" alt="">Add to Cart</button>
									<?php
								}
								else
								{
									?>
									<button class="btn btn-danger" style="font-family: 'Montserrat';padding: 13px 22px;border: 0;font-size: 24px;" >Out of Stock</button>
									<?php
								}
								?>
								</div>
								<div class="col-md-5 cart-quality">
									<table class="table">
										<thead>
											<tr>
												<th>Quantity</th>
												<th>Our Price</th>
											</tr>
										</thead>
										<tbody>
											<?php if ($discounts) { ?>
                <tr>
                  <td>1</td>
                  <td><?php echo $price; ?></td>
                </tr>
                <?php foreach ($discounts as $key=>$discount) { ?>
                <tr >
                  <td>
                    <?php echo $discount['quantity'] . ($discount === end($discounts) ? '+' : ' - ' . ( $discounts[$key+1]['quantity'] - 1 )) ?>
                  </td>
                  <td>
                    <?php echo $discount['price']; ?>
                  </td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td>1</td>
                  <td class="red"><?php echo $price; ?></td>
                </tr>
                <?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="border"></div>
					<h3 class="icon-heading">
						<img src="catalog/view/theme/ppusa2.0/images/icons/query.png" alt="">
						Questions &amp; answers
					</h3>
					<div class="ask-question clearfix">
						<input type="text" id='product_question' class="input" placeholder="Ask a Question">
						<button class="btn btn-primary " onclick="saveQuestion()">
							<i class="fa fa-question"></i>Ask
						</button>
					</div>
					<div id='question_div'>
						<?php if(isset($question)){ 
							foreach($question as $key => $value ) {?> 
								<?php if($key > 1) { ?>
									<div class="query-box hide-questions">
								<?php }else { ?>
									<div class="query-box">
								<?php } ?>
									<h5>Question:</h5>
									<p>
										<?php echo $value['question'] ?>
									</p>
									<?php if($value['answer']) { ?>
										<h5>Answer</h5>
										<p><?php echo $value['answer'] ?></p>
									<?php } ?>
								</div>
						<?php }}else{ ?>
							<div class="query-box no-question">
								<p class="content">There are no questions for this product.</p>
							</div>
						<?php }?>
					</div>
					<a href='javascript:void(0)' class="viewmore" onclick="toggleQuestion()">view more Questions &amp; answers</a>
					<div class="border"></div>
					<div class="row write-review" id="write-review">
						<div class="col-sm-6">
							<h3 class="icon-heading">
								<img src="catalog/view/theme/ppusa2.0/images/icons/review-icon.png" alt="">
								customer reviews
							</h3>
						</div>
						<div class="col-sm-6 text-right">
							<a href="#review-pop" class="btn btn-primary fancybox">write a customer review</a>
						</div>
					</div>
					<!--@End write-review -->
					<div class="row customer-review">
						<div class="col-md-5">
							<ul class="review-stars clearfix">
							
								
								
							</ul>
							<p><?php echo $rating;?> out of 5 stars</p>
							<h4><a href="javascript:void(0)" class="underline">See all <?php echo $tab_review; ?></a></h4>
						</div>
						<div class="col-md-7">
							<ul class="progress-wrap">
								<li>
									<span class="satar-rate">5 Star</span>
									<div class="progress">
									  <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $five_perc;?>"
									  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $five_perc;?>%">
									  </div>
									</div>
									<span class="progress-percent"><?php echo (int)$five_perc;?> %</span>
								</li>
								<li>
									<span class="satar-rate">4 Star</span>
									<div class="progress">
									  <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $four_perc;?>"
									  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $four_perc;?>%">
									  </div>
									</div>
									<span class="progress-percent"><?php echo (int)$four_perc;?> %</span>
								</li>
								<li>
									<span class="satar-rate">3 Star</span>
									<div class="progress">
									  <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $three_perc;?>"
									  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $three_perc;?>%">
									  </div>
									</div>
									<span class="progress-percent"><?php echo (int)$three_perc;?> %</span>
								</li>
								<li>
									<span class="satar-rate">2 Star</span>
									<div class="progress">
									  <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $two_perc;?>"
									  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $two_perc;?>%">
									  </div>
									</div>
									<span class="progress-percent"><?php echo (int)$two_perc;?> %</span>
								</li>
								<li>
									<span class="satar-rate">1 Star</span>
									<div class="progress">
									  <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $one_perc;?>"
									  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $one_perc;?>%">
									  </div>
									</div>
									<span class="progress-percent"><?php echo (int)$one_perc;?> %</span>
								</li>
							</ul>
						</div>
					</div>
					<!--@End customer-review -->
					<div id="review">
					
					</div>
					
					<div class="border" style="margin-top: 10px;margin-bottom: 10px;"></div>
					<h3 class="icon-heading">
						<img src="catalog/view/theme/ppusa2.0/images/icons/repair-icon.png" alt="">
						step by step repair guide
					</h3>
					<?php if(isset($steps)){ 
						foreach($steps as $key => $value ) {?> 
							<article class="repair-guide">
								<h3><span>step <?php echo $value['order_number'] ?></span> - step <?php echo $value['step_name'] ?></h3>
								<div class="repair-box clearfix">
									<div class="image">
										<img src="<?php echo $value['image_path'] ?>" alt="">
									</div>
									<div class="text">
										<p><?php echo $value['step_description'] ?> </p>
									</div>
								</div>
							</article>
					<?php }}else{ ?>
						<div class="content">There are no steps for this product.</div>
					<?php }?>
					<div class="border" style="margin-top: 10px;margin-bottom: 10px;"></div>
					
				</div>
				<!-- @End of main-content -->
				<aside class="col-md-3 sidebar">
					 <?php if ($youtubeproduct) { ?>
					<div class="videw-guide">
						<h3>video repair guide</h3>
						<div class="video-box">
							<iframe class="youtube-player" type="text/html" width="364" height="204.75" src="https://www.youtube.com/embed/<?php echo $youtubeproduct ; ?>" frameborder="0"></iframe>
						</div>
					</div>
					<?php
				}
					?>
					<div class="step-guid"><span><img src="catalog/view/theme/ppusa2.0/images/icons/book-icon.png" alt=""></span>
						<a href="javascript:void(0)" class="underline">Step By  Step Repair Guide (Coming Soon)</a></div>
					<div class="related-wrap">
					<?php
					$l = 0;
					foreach($latest_products as $lproduct)
					{
					?>
						<article class="related-product">
							<?php
							if($l==0)
							{
								?>
							<h3>related products &amp; tools</h3>

								<?php
							}
							?>
							<div class="image">
								<img class="lazy" src="catalog/view/theme/ppusa2.0/images/spinner.gif" data-original="<?php echo $lproduct['thumb'];?>" alt="">
							</div>
							<h4><a href="<?php echo $lproduct['href'];?>"><?php echo $lproduct['name'];?></a></h4>
							<!-- <p class="product-attribute">Brief List Of Product Attributes</p> -->
							<div class="qtyt-box">
								<div class="input-group spinner">
									<span class="txt">QTY</span>
								    <input type="text" class="form-control qty" value="1">
								    <div class="input-group-btn-vertical">
								      <button class="btn " type="button"><i class="fa fa-plus"></i></button>
								      <button class="btn" type="button"><i class="fa fa-plus"></i></button>
								    </div>
								 </div>
							</div>
							<div class="review-area">
								<!-- <ul class="review-stars clearfix">
									<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
									<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
									<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
									<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
									<li><a href="#"><i class="fa fa-star"></i></a></li>
								</ul> -->
								<a href="javascript:void(0)" class="review-links underline"><?php echo $lproduct['reviews'];?></a>
							</div>
							<p class="price"><?php echo $lproduct['price'];?></p>
							<button class="btn btn-info" onclick="addToCartpp2(<?php echo $lproduct['product_id']; ?>, $(this).parent().find('.qty').val())">Add to cart</button>
						</article>
						<?php
						$l++;
					}
					?>
						
					</div>
				</aside>
				<!-- @End of sidebar -->
			</div>	
		</div>
		<div class="sticky_add_cart row ">
    <div class="qty-changers">
      <span class="minus" onClick="QtyChange('-');">-</span>
              <input id="QtyNormal" name="QtyNormal" type="tel" size="2" maxlength="3" value="1" class="QtyNormal">
            <span class="plus" onClick="QtyChange('+');">+</span>
    </div>
    <div class="add-button">
      <div class="button btn btn-success addtocart" onclick="addToCartpp2('<?php echo $product_id;?>', $('#QtyNormal').val())"><div class="content">Add To Cart<span class="carrot">></span></div></div>
    </div>
  </div>
	</main><!-- @End of main -->
	<script>
	function QtyChange(xtype)
  {
  	var my_val = $('#QtyNormal').val();
   if(xtype=='+')
   {
   	my_val = parseInt(my_val)+1;
   }
   else
   {
   	my_val = parseInt(my_val)-1;
   }
   if(my_val<=1)
   {
   	my_val = 1;
   }
   $('#QtyNormal').val(my_val);
  // getProductPrice();

}
		function getProductPrice()  {
  var product_id = $('input[name=product_id]').val();
  var quantity = $('input[name=quantity]').val();
  if (quantity === '0') {
    quantity = 1;
    $('input[name=quantity]').val('1');
  }

  $.ajax({
    url: 'index.php?route=product/product/getUpdatedPrice',
    type: 'post',
    data: {product_id:product_id,quantity:quantity},
    dataType: 'json',
    beforeSend: function() {
     // $('.success, .warning').remove();
      //$('#button-review').attr('disabled', true);
      $('.cart-total-wrp .qty').html('<small>Updating...</small>');
    },
    complete: function() {
     // $('#button-review').attr('disabled', false);
      //$('.attention').remove();
    },
    success: function(json) {
      $('information, .error').remove();
      if (json['error']) {

      }  

      if (json['success']) {
        $('.cart-total-wrp .qty').html(json['success']); 
      }
    }
  });
}
$(document).ready(function(){

  getProductPrice();
});
$('.viewmore').click(function(){

var page = parseInt($('#reviewmorebtn').attr('data-page'));
page = page + 1;
var response;
$.ajax({ type: "GET",   
     url: "index.php?route=product/product/review&product_id=<?php echo $product_id;?>&page="+page,   
     async: false,
     success : function(text)
     {
         response= text;
     }
});
$('#review2').prepend(response);

});
$('#review').load('index.php?route=product/product/review&product_id=<?php echo $product_id; ?>');
$('#review .matrialPagination a').on('click',document, function() {
   $('#review').fadeOut('slow');

   $('#review').load(this.href);

   $('#review').fadeIn('slow');

   return false;
 });
$(document).ready(function() {

    $('ul.social-shares li.login_popup a').on('click',function(e) {
        
        e.preventDefault();
        window.open($(this).attr('data-href'), 'fbShareWindow', 'height=450, width=550, top=' + ($(window).height() / 2 - 275) + ', left=' + ($(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
        return false;
    });


});


  $('#button-review').on('click', function() {
   $.ajax({
    url: 'index.php?route=product/product/write&product_id=<?php echo $product_id; ?>',
    type: 'post',
    dataType: 'json',
    data: 'name=' + encodeURIComponent($('input[name=\'name\']').val()) + '&text=' + encodeURIComponent($('textarea[name=\'text\']').val()) + '&rating=' + encodeURIComponent($('input[name=\'rating\']:checked').val() ? $('input[name=\'rating\']:checked').val() : '') + '&captcha=' + encodeURIComponent($('input[name=\'captcha\']').val()),
    beforeSend: function() {
     // $('.success, .warning').remove();
     // $('#button-review').attr('disabled', true);
     // $('#review-title').after('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
   },
   complete: function() {
     $('#button-review').attr('disabled', false);
     // $('.attention').remove();
   },
   success: function(data) {
     if (data['error']) {
      // $('#review-title').after('<div class="warning">' + data['error'] + '</div>');
      alert(data['error']);
    }

    if (data['success']) {
      // $('#review-title').after('<div class="success">' + data['success'] + '</div>');
      alert(data['success']);
      $.fancybox.close();
      $('input[name=\'name\']').val('');
      $('textarea[name=\'text\']').val('');
      $('input[name=\'rating\']:checked').attr('checked', '');
      $('input[name=\'captcha\']').val('');
    }
  }
});
 });

	function saveQuestion()
	{
		var question = $('#product_question').val();
		if(question.length < 5)
		{
			alert("Kindly provide a proper question.");
			return;
		}

		$.ajax({
		    url: 'index.php?route=product/product/saveQuestion',
		    type: 'post',
		    data:{'question':question,'product_id':'<?php echo $product_id; ?>','product_title':'<?php echo $heading_title; ?>','product_sku':'<?php echo $model;?>'},
		    dataType: 'json',
		    success : function(response){
		    	$('#question_div').append(response);
		    	$('#product_question').val('');
		    	$('.no-question').hide();
		    }
		});
	}
  	function toggleQuestion()
	{	
		$('.hide-questions').show();
	}
	</script>				
<?php echo $footer; ?>