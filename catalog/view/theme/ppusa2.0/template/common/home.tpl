<?php echo $header; ?>
<main class="main">
	<div class="home-page">
		<!-- <div class="home-banner" style="background: url(<?php echo $main_banner;?>)no-repeat;background-size:100%">
		</div> -->
		<div class="home-banner" style="background: url(catalog/view/theme/ppusa2.0/images/spinner.gif)no-repeat;background-position: center">
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
						<div class="form-group hidden">
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
					<div><img class="lazy" src="" data-original="<?php echo $sub_banner;?>" alt="" style="cursor:pointer;" onClick="window.location='<?php echo $this->url->link('buyback/buyback') ;?>'"></div>
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
							<div class="serviceTitle text-center"><h3><a style="color:#FFF" href="<?php echo $manufacturer['href']; ?>"><?php echo $name; ?></a></h3></div>
							<div class="serviceImage">
								<img class="lazy" data-original="<?php echo $manufacturer['image']; ?>" alt="">
							</div>
							<div class="ourSelectionItems row">
								<div class="<?php echo ($manufacturer['others']?'col-xs-4 col-md-4':'col-xs-6 col-md-4');?> ourSelectionItemsCol">
									<h3>PHONES</h3>
									<ul class="ourSelectionList">
										<?php foreach ($manufacturer['phones'] as $phone ) { ?>
										<li><a href="<?php echo $phone['href']; ?>"><?php echo $phone['name']; ?></a></li>
										<?php } ?>
									</ul>
								</div>
								<div class="<?php echo ($manufacturer['others']?'col-xs-4 col-md-4':'col-xs-6 col-md-4');?> ourSelectionItemsCol">
									<h3>TABLETS</h3>
									<ul class="ourSelectionList">
										<?php foreach ($manufacturer['tabs'] as $phone ) { ?>
										<li><a href="<?php echo $phone['href']; ?>"><?php echo $phone['name']; ?></a></li>
										<?php } ?>
									</ul>
								</div>

								<div class="<?php echo ($manufacturer['others']?'col-xs-4 col-md-4':'col-xs-6 col-md-4');?> ourSelectionItemsCol">
								<?php
								if($manufacturer['others'])
								{
								?>
									<h3>OTHER</h3>
									<ul class="ourSelectionList">
										<?php foreach ($manufacturer['others'] as $phone ) { ?>
										<li><a href="<?php echo $phone['href']; ?>"><?php echo $phone['name']; ?></a></li>
										<?php } ?>
									</ul>
									<?php
									}
									?>
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
			<div id="popular_repair_parts" class="row listing-row" style="text-align: center" >
				<img src="catalog/view/theme/ppusa2.0/images/spinner.gif" />
			</div>
		</div>
		
	</div>
<hr>
	<div class="row" style="margin-bottom:40px;margin-top:40px  ">
		
		<div class="col-sm-12">
			<h2 style="text-align: center;font-weight:700">POPULAR ACCESSORIES</h2>
			<div class="row listing-row" id="popular_accessories" style="text-align: center" >
				<img src="catalog/view/theme/ppusa2.0/images/spinner.gif" />
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
	function loadProducts(module,div_id)
	{
		// alert(module);
		$.ajax({
			url: '?route=common/home/getModules',
			type: 'POST',
			dataType: 'html',
			data: {module: module},
			beforeSend: function() {
				
            }
		}).always(function(html) {
			// console.log(module);
			$('#'+ div_id).html(html);
		});
	}
	
	$(document).ready(function(){
	
	if ($(window).width() > 450){  
		loadProducts('home_products2','popular_repair_parts');
		loadProducts('home_products3','popular_accessories');
	}
	
	});
	$(window).load(function(){
			$('.home-banner').removeAttr('style');
			<?php
				if(!isset($this->request->get['new_banner']))
				{
					?>
						$('.home-banner').css('background-image','url(<?php echo $main_banner;?>)');

					<?php

					if($banner_overlay!='')
					{
						?>

						$('.home-banner').css('box-shadow', 'inset 0 0 0 2000px');
						$('.home-banner').css('background-color', 'rgba(255,255,255,0.7)');​
						<?php
					}
				}
				else
				{
					?>

					$('.home-banner').css('background-image','url(<?php echo $main_banner2;?>)');
						$('.home-banner').css( 'cursor', 'pointer' );
						$(document).on("click", ".home-banner", function() {
       						window.location=' https://docs.google.com/forms/d/e/1FAIpQLScfVwiYsbGzclbWxwKnFUsQ-bPLfm1ZCZAqBixcQBk4cx2IVg/viewform?usp=sf_link';
    						});

				

					<?php
				}
			?>
		$('.home-banner').css('background-size','100%');
		$('.home-banner').css('background-repeat','no-repeat');
	})
</script>

<?php echo $footer; ?>