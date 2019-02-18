<?php echo $header; ?>
<main class="main">
	<div class="home-page">
		<div class="home-banner" style="background: url(<?php echo $main_banner;?>)no-repeat;background-size:100%">
		</div>
		<!-- home banner -->
		<div class="partSearchBlock">
			<div class="container">
				<div class="partSearchContainer">
					<p class="partSearchTitle">Find a Part, Any Part</p>
					<form class="form-inline select-full text-center" role="form">
						<div class="form-group">
							<select  class="selectpicker" id="home_manufacturer" class="home_manufacturer" data-size="10">
								<option value="">Manufacturer</option>
							</select>                                
						</div> <!-- form group [rows] -->
						<div class="form-group">
							<select  class="selectpicker" id="home_model" data-size="10">
								<option value="">Model</option>
							</select>                                
						</div> <!-- form group [rows] -->
						<div class="form-group">
							<select  class="selectpicker" id="home_sub_model" data-size="10">
								<option value="">Sub-Model</option>

							</select>                                
						</div> <!-- form group [rows] -->
						<div class="form-group hidden" >
							<select  class="selectpicker" id="home_part_type" data-size="10">
								<option value="">Part Type</option>

							</select>                                
						</div> <!-- form group [rows] -->
						
						<div class="form-group" style="width:100%;text-align:center">
							<button class="btn btn-success btn-lg" style="padding:8px 76px; font-size:33px" type="button" id="sFindPart">Find Parts</button>

						</div> <!-- form group [rows] -->
					</form>
				</div>
			</div>
		</div>
		<!-- parts search block -->
		<div class="homeSlider">
			<div class="container">
				<div class="homeSlides">
				<?php
				foreach($sub_banners as $key => $sub_banner)
				{


				?>
					<div><img class="lazy" src="catalog/view/theme/ppusa2.0/images/spinner.gif" data-original="<?php echo $sub_banner;?>" alt="" style="cursor:pointer;" onClick="window.location='<?php echo $this->url->link('buyback/buyback') ;?>'"></div>
					<?php
				}
				?>
				</div>
			</div>
		</div>
		<!-- homeSlider -->
		<section id="ourSelection">
			<div class="container">
				<h2 class="text-center">OUR SELECTION</h2>
				<div class="row ourSelectionRow margin10">
					<?php foreach ($manufacturers as $name => $manufacturer) { ?>
					<!-- our selection column -->
					<div class="col-md-6 ourSelectionCol">
						<div class="ourSelectioncontent">
							<div class="serviceTitle text-center"><h3><?php echo $name; ?></h3></div>
							<div class="serviceImage">
								<img class="lazy" data-original="<?php echo $manufacturer['image']; ?>" alt="">
							</div>
							<div class="ourSelectionItems row">
								<div class="col-sm-4 ourSelectionItemsCol">
									<h3>PHONES</h3>
									<ul class="ourSelectionList">
										<?php foreach ($manufacturer['phones'] as $phone ) { ?>
										<li><a href="<?php echo $phone['href']; ?>"><?php echo $phone['name']; ?></a></li>
										<?php } ?>
									</ul>
								</div>
								<div class="col-sm-4 ourSelectionItemsCol">
									<h3>TABLETS</h3>
									<ul class="ourSelectionList">
										<?php foreach ($manufacturer['tabs'] as $phone ) { ?>
										<li><a href="<?php echo $phone['href']; ?>"><?php echo $phone['name']; ?></a></li>
										<?php } ?>
									</ul>
								</div>
								<div class="col-sm-4 ourSelectionItemsCol">
									<h3>OTHER</h3>
									<ul class="ourSelectionList">
										<li><a href="<?php echo $manufacturer['href']; ?>">More</a></li>
									</ul>
								</div>
							</div>
						</div>		
					</div>
					<?php } ?>
				</div>
			</div>
		</section>
		<!-- our selection -->
		<div id="popularProducts">
			<div class="container" id="blueHead">
		


	<div class="row" style="margin-bottom:40px;margin-top:40px  ">
		
		<div class="col-sm-12">
			<h2 style="text-align: center;font-weight:700">POPULAR REPAIR PARTS</h2>
			<div class="row active-0" style="text-align: center" >
			<?php 
			//$k= 0;
			$key='repair';
			foreach ($home_products2 as $k => $product) { ?>
				
				<div class="col-sm-3 product_<?php echo $product['product_id']; ?>">
					<img class="lazy"  height="150" width="150" src="catalog/view/theme/ppusa2.0/images/spinner2.gif" data-original="<?php echo $product['thumb'] ?>" style="margin-top:15px"  >
					<div style="min-height: 75px" >
						<p style="margin-top:10px;margin-bottom: 10px;font-weight:400;color:#3e3e3e;font-family: 'Montserrat';"><a href="<?php echo $product['href'] ?>"><?php echo $product['name'] ?> </a> </p>
					</div>
					
					<?php
					if((int)$product['quantity']>0)
						{
							?>
					<div class='qtyt-box'>
											<div class='input-group inlineSpinner1 spinner'>
												<span class='txt'>QTY</span>
												<input type='text' class='form-control qty' value='1' style='color:#303030' id='homeqty-<?php echo $key;?>-<?php echo $k;?>' data-i="<?php echo $key;?>" data-j="<?php echo $k;?>">
												<div class='input-group-btn-vertical'>
													<button class='btn' type='button'><i class='fa fa-plus'></i></button>
													<button class='btn' type='button'><i class='fa fa-minus'></i></button>
												</div>
											</div>
										</div>
										<?php
									}
									?>
					<input type="hidden" class="color-default-price" id="color-<?php echo $key;?>-<?php echo $k;?>" value="<?php echo $product['product_id'];?>">
					<?php
					if((int)$product['quantity']>0)
						{
							?>
					<?php if (!$product['sale_price']) {?>
					<div class="price" id="price-<?php echo $key;?>-<?php echo $k;?>" style="margin-bottom: 10px;" >
					<span style="font-size: 30px;font-weight: 400;color:#4986fe;">
						<?php echo ($product['price']); ?></span>
					</div>

					<?php } else {?>
					
					<div class="price" id="price-<?php echo $key;?>-<?php echo $k;?>" style="margin-bottom: 10px;" >
					<span style="font-size:17px; color:#4986fe; text-decoration:line-through;"><?php echo ($product['price']); ?></span>

						<span style="font-size: 30px;font-weight: 400;color:red;"><?php echo ($product['sale_price']); ?></span>
					</div>
					<?php } ?>

					<?php
				}
				else
				{
					?>
	<div >
			<span class="oos_qty_error_<?php echo $product['product_id'];?>" style="font-size:11px;color:red"></span>
			<input type="text" class="form-control customer_email_<?php echo $product['product_id'] ?>" style="margin-bottom:48px" placeholder="Enter your Email" value="<?php echo $customer_email;?>">
	</div>
					<?php
				}
				?>
					<?php
						if((int)$product['quantity']>0)
						{


					?>
					<button class="btn btn-info" onclick="addToCartpp2('<?php echo $product['product_id'] ?>',$('#homeqty-<?php echo $key;?>-<?php echo $k;?>').val())"><?php echo  ($product['in_cart']==true?"IN CART":"ADD TO CART") ;?></button>
					<?php
				}
				else
				{
					?>
						<button class="btn btn-danger" id="notify_btn_<?php echo $product['product_id'];?>" onclick="notifyMe('<?php echo $product['product_id'] ?>')">NOTIFY WHEN AVAILABLE</button>
					<?php
				}
				?>
				</div>
				
				
			<?php
			//$k++;
			 } ?>
			</div>
		</div>
		
	</div>
<hr>
	<div class="row" style="margin-bottom:40px;margin-top:40px  ">
		
		<div class="col-sm-12">
			<h2 style="text-align: center;font-weight:700">POPULAR ACCESSORIES</h2>
			<div class="row active-0" style="text-align: center" >
			<?php 
			//$k= 0;
			$key='accessories';
			foreach ($home_products3 as $k => $product) { ?>
				
				<div class="col-sm-3 product_<?php echo $product['product_id']; ?>">
					<img class="lazy"  height="150" width="150" src="catalog/view/theme/ppusa2.0/images/spinner2.gif" data-original="<?php echo $product['thumb'] ?>" style="margin-top:15px"  >
					<div style="min-height: 75px" >
						<p style="margin-top:10px;margin-bottom: 10px;font-weight:400;color:#3e3e3e;font-family: 'Montserrat';"><a href="<?php echo $product['href'] ?>"><?php echo $product['name'] ?> </a> </p>
					</div>
					
					<?php
					if((int)$product['quantity']>0)
						{
							?>
					<div class='qtyt-box'>
											<div class='input-group inlineSpinner1 spinner'>
												<span class='txt'>QTY</span>
												<input type='text' class='form-control qty' value='1' style='color:#303030' id='homeqty-<?php echo $key;?>-<?php echo $k;?>' data-i="<?php echo $key;?>" data-j="<?php echo $k;?>">
												<div class='input-group-btn-vertical'>
													<button class='btn' type='button'><i class='fa fa-plus'></i></button>
													<button class='btn' type='button'><i class='fa fa-minus'></i></button>
												</div>
											</div>
										</div>
										<?php
									}
									?>
					<input type="hidden" class="color-default-price" id="color-<?php echo $key;?>-<?php echo $k;?>" value="<?php echo $product['product_id'];?>">
					<?php
					if((int)$product['quantity']>0)
						{
							?>
					<?php if (!$product['sale_price']) {?>
					<div class="price" id="price-<?php echo $key;?>-<?php echo $k;?>" style="margin-bottom: 10px;" >
					<span style="font-size: 30px;font-weight: 400;color:#4986fe;">
						<?php echo ($product['price']); ?></span>
					</div>

					<?php } else {?>
					
					<div class="price" id="price-<?php echo $key;?>-<?php echo $k;?>" style="margin-bottom: 10px;" >
					<span style="font-size:17px; color:#4986fe; text-decoration:line-through;"><?php echo ($product['price']); ?></span>

						<span style="font-size: 30px;font-weight: 400;color:red;"><?php echo ($product['sale_price']); ?></span>
					</div>
					<?php } ?>

					<?php
				}
				else
				{
					?>
	<div >
			<span class="oos_qty_error_<?php echo $product['product_id'];?>" style="font-size:11px;color:red"></span>
			<input type="text" class="form-control customer_email_<?php echo $product['product_id'] ?>" style="margin-bottom:48px" placeholder="Enter your Email" value="<?php echo $customer_email;?>">
	</div>
					<?php
				}
				?>
					<?php
						if((int)$product['quantity']>0)
						{


					?>
					<button class="btn btn-info" onclick="addToCartpp2('<?php echo $product['product_id'] ?>',$('#homeqty-<?php echo $key;?>-<?php echo $k;?>').val())"><?php echo  ($product['in_cart']==true?"IN CART":"ADD TO CART") ;?></button>
					<?php
				}
				else
				{
					?>
						<button class="btn btn-danger" id="notify_btn_<?php echo $product['product_id'];?>" onclick="notifyMe('<?php echo $product['product_id'] ?>')">NOTIFY WHEN AVAILABLE</button>
					<?php
				}
				?>
				</div>
				
				
			<?php
			//$k++;
			 } ?>
			</div>
		</div>
		
	</div>

</div>
</div>
</div>

</main><!-- @End of main -->
<script>
	$(document).ready(function(){
		$('.popularProductsCol').each(function(index, el) {

			var product_id =  $(el).find('.color-default-price').val();
			loadPopularImages(product_id,el);
		});
		

	});

	

	function slideRight(key)
	{
		var limit = parseInt($('#product_'+key+'_limit').val());
		limit = limit+4;
		$('#product_'+key+'_limit').val(limit);

		var d = new FormData();
		d.append('limit',limit);
		d.append('key',key);


		$.ajax({
			url : 'index.php?route=common/home/test',
			data : d,
			type : 'POST',
			dataType: 'json',
			data: d,
			contentType: false,
			enctype: 'multipart/form-data',
			processData: false,
			success:function(html)
			{
				if(html != 'false')
				{
					$('div.active-'+key).hide();
					$('div.active-'+key).html(html);
					// $('div.active-'+key).toggle('slide');
					 $('div.active-'+key).show("slide", { direction: "right" }, 500);
					  // $("img.lazy").myLazyLoad();
				}
				else
				{
					limit = limit-4;
					$('#product_'+key+'_limit').val(limit);
				}
			},
			error:function(response)
			{
				alert("error"+response);
			}

		});


		// var a = $('div.active-'+key)[0].nextElementSibling;
		// if(a)
		// {
		// 	$('div.active-'+key).hide();
		// 	$('div.active-'+key).removeClass('active-'+key);
		// 	$(a).addClass('active-'+key);
		// 	$('div.active-'+key).toggle('slide');
		// }
		
	}

	function slideLeft(key)
	{
		var limit = parseInt($('#product_'+key+'_limit').val());
		if(limit > 0 )
		{
			limit = limit-4;
			$('#product_'+key+'_limit').val(limit);

			var d = new FormData();
			d.append('limit',limit);
			d.append('key',key);

			$.ajax({
				url : 'index.php?route=common/home/test',
				data : d,
				type : 'POST',
				dataType: 'json',
				data: d,
				contentType: false,
				enctype: 'multipart/form-data',
				processData: false,
				success:function(html)
				{
					$('div.active-'+key).hide();
					$('div.active-'+key).html(html);
					// $('div.active-'+key).toggle('slide');
					$('div.active-'+key).show("slide", { direction: "left" }, 500);
					 // $("img.lazy").myLazyLoad();
				},
				error:function(response)
				{
					alert("error"+response);
				}

			});
		}
		// var a = $('div.active-'+key)[0].previousElementSibling;
		// if($(a).hasClass('row') )
		// {
		// 	$('div.active-'+key).hide();
		// 	$('div.active-'+key).removeClass('active-'+key);
		// 	$(a).addClass('active-'+key);
		// 	$('div.active-'+key).toggle('slide');
		// }
		
	}

</script>
<?php echo $footer; ?>