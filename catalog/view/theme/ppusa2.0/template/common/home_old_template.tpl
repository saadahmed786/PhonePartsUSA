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
					<!-- <h2 class="text-center">IPhone Quality Selection Tool</h2>
					<div class="row">					
						<div class="col-md-12">
							<div class="jumbotron">
								<div class="row videoPlayer">
									<div class="col-md-6 col-sm-6 col-xs-12">
										<ol>
											<li>Choose Screen Quantity</li>
											<li>Select Color</li>
											<li>Adjust Quantity</li>
											<li>Add to Cart &#9786;</li>
										</ol>
									</div>
									<div class="col-md-6 col-sm-6 col-xs-12">
										<iframe src="https://player.vimeo.com/video/177833573" style="width:100%" width="400" height="225" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
									</div>
								</div>
							</div>
						</div>
				</div> -->
			<!-- 	<div class="row popularProductsRow">
					<?php
					$_i = 0;
					foreach($popular_products as $popular)
					{
					?>
					<div class="col-md-6 popularProductsCol">
						<div class="popularProductImage text-center"><img class="lazy" src="catalog/view/theme/ppusa2.0/images/spinner.gif" data-original="image/cache/data/image-coming-soon-278x330.jpg" alt=""></div>
						<h3 class="text-center"><?php echo strtoupper($popular['name']);?></h3>
						<table class="packageTable">
							<thead>
								<tr>
									<?php
									foreach($popular['type'] as $type )
									{
									?>
									<th style="text-align: center"><?php echo ucfirst($type['grade']);?></th>
									<?php
								}
								?>
							</tr>
						</thead>
						<tbody style="text-align: center">
							<tr>
								<?php
								$_j=0;
								foreach($popular['type'] as $type)
								{
								?>
								<td>
									<ul class="colorBox">
										<?php
										$_k = 0;
										$default_product_id='0';
										foreach($type['sub'] as $details)
										{
										if($_k==0)
										{
										$default_product_id = $details['product_id'];
									}
									?>
									<li  title="<?php echo $colors[$details['color']];?>" style="background-color:<?php echo $details['color'];?>" class="home-color-picker" data-product-id="<?php echo $details['product_id'] ;?>" data-i="<?php echo $_i;?>" data-j="<?php echo $_j;?>" data-k="<?php echo $_k;?>"></li>
									<?php
									$_k++;
								}
								?>

							</ul>
							<input type="hidden" class="color-default-price" id="color-<?php echo $_i;?>-<?php echo $_j;?>" value="<?php echo $default_product_id;?>">
						</td>
						<?php
						$_j++;
					}
					?>
				</tr>
				<tr>
					<?php
					$_j = 0;
					foreach($popular['type'] as $type )
					{
					?>
					<td>
						<div class="input-group spinner blackSpinner">
							<div class="inlineSpinner">
								<button class="btn inc-dec-btn" type="button"><i class="fa fa-plus"></i></button>
								<input type="text" class="form-control" value="1" id="homeqty-<?php echo $_i;?>-<?php echo $_j;?>" data-i="<?php echo $_i;?>" data-j="<?php echo $_j;?>">
								<button class="btn inc-dec-btn" type="button"><i class="fa fa-minus"></i></button>
							</div>
						</div>
					</td>
					<?php
					$_j++;
				}
				?>

			</tr>
			<tr>
				<?php
				$_j = 0;
				foreach($popular['type'] as $type )
				{
				?>
				<td class="price" id="price-<?php echo $_i;?>-<?php echo $_j;?>" style="font-size: 24px;font-weight: 600;">
					<?php echo $this->currency->format($type['price']['price']);?>
				</td>
				<?php
				$_j++;
			}
			?>

		</tr>
		<tr>
			<?php
			$_j=0;
			foreach($popular['type'] as $type )
			{
			?>
			<td>
				<button class="btn btn-success" onclick="addToCartpp2($('#color-<?php echo $_i;?>-<?php echo $_j;?>').val(), $('#homeqty-<?php echo $_i;?>-<?php echo $_j;?>').val())">ADD TO CART</button>
			</td>
			<?php
			$_j++;
		}
		?>

	</tr>
</tbody>
</table>
</div>
<?php
$_i++;
}
?>


</div> -->
<style type="text/css">

</style>
<!-- My new div starts here -->
<?php 
//$modules = array('APPLE'=>'TOP SELLING APPLE PARTS','SAMSUNG'=>'TOP SELLING SAMSUNG PARTS','LG'=>'TOP SELLING LG PARTS','MOTOROLA'=>'TOP SELLING MOTOROLLA PARTS','IPAD_SCREENS'=>'TOP SELLING IPAD SCREENS','BATTERIES'=>'TOP SELLING BATTERIES','ADHESIVES'=>'TOP SELLING ADHESIVES','CHARGERS'=>'TOP SELLING CHARGERS');

$modules = array('APPLE'=>'TOP SELLING APPLE PARTS','SAMSUNG'=>'TOP SELLING SAMSUNG PARTS','LG'=>'TOP SELLING LG PARTS','MOTOROLA'=>'TOP SELLING MOTOROLLA PARTS','IPAD_SCREENS'=>'TOP SELLING IPAD SCREENS','BATTERIES'=>'TOP SELLING BATTERIES','ADHESIVES'=>'TOP SELLING ADHESIVES','CHARGERS'=>'TOP SELLING CHARGERS','TOOLS'=>'TOP SELLING TOOLS','TEMPERED_GLASS'=>'TOP SELLING TEMPERED GLASS');

foreach($modules as $key => $value) { ?>
<input type="hidden" id="product_<?php echo $key ?>_limit" value=0 />
	<div class="row" style="margin-bottom:40px;margin-top:40px  ">
		<div class="col-sm-1">
			<img src="image/slider-arrow.png" onclick="slideLeft('<?php echo $key ?>')"  style="margin-left: 22px;position: absolute;top: 85px;width: 45px;cursor: pointer;">
		</div>
		<div class="col-sm-10">
			<h2 style="text-align: center;font-weight:700"><?php echo $value ?></h2>
			<div class="row active-<?php echo $key ?>" style="text-align: center" >
			<?php foreach ($product_details[$key] as $k => $val) { ?>
				
				<div class="col-sm-3 product_<?php echo $val['product_id']; ?>">
					<img class="lazy"  height="150" width="150" src="catalog/view/theme/ppusa2.0/images/spinner2.gif" data-original="<?php echo $val['img'] ?>"  >
					<div style="min-height: 75px" >
						<p style="margin-top:10px;margin-bottom: 10px;font-weight:400;color:#3e3e3e;font-family: 'Montserrat';"><a href="<?php echo $val['href'] ?>"><?php echo $val['description'] ?> </a> </p>
					</div>
					<!-- <div class="input-group spinner blackSpinner" style="margin-bottom: 10px;margin-left: 78px">
						<div class="inlineSpinner">
							<button class="btn inc-dec-btn" type="button"><i class="fa fa-plus"></i></button>
							<input type="text" class="form-control" value="1" id="homeqty-<?php echo $key;?>-<?php echo $k;?>" data-i="<?php echo $key;?>" data-j="<?php echo $k;?>" >
							<button class="btn inc-dec-btn" type="button"><i class="fa fa-minus"></i></button>
						</div>
					</div> -->
					<?php
					if((int)$val['quantity']>0)
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
					<input type="hidden" class="color-default-price" id="color-<?php echo $key;?>-<?php echo $k;?>" value="<?php echo $val['product_id'];?>">
					<?php
					if((int)$val['quantity']>0)
						{
							?>
					<?php if ($val['sale_price']==0.0000) {?>
					<div class="price" id="price-<?php echo $key;?>-<?php echo $k;?>" style="margin-bottom: 10px;" >
					<span style="font-size: 30px;font-weight: 400;color:#191919;">
						<?php echo $this->currency->format($val['price']); ?></span>
					</div>

					<?php } else {?>
					
					<div class="price" id="price-<?php echo $key;?>-<?php echo $k;?>" style="margin-bottom: 10px;" >
					<span style="font-size:17px; color:#808080; text-decoration:line-through;"><?php echo $this->currency->format($val['price']); ?></span>

						<span style="font-size: 30px;font-weight: 400;color:red;"><?php echo $this->currency->format($val['sale_price']); ?></span>
					</div>
					<?php } ?>

					<?php
				}
				else
				{
					?>
	<div >
			<span class="oos_qty_error_<?php echo $val['product_id'];?>" style="font-size:11px;color:red"></span>
			<input type="text" class="form-control customer_email_<?php echo $val['product_id'] ?>" style="margin-bottom:48px" placeholder="Enter your Email" value="<?php echo $customer_email;?>">
	</div>
					<?php
				}
				?>
					<?php
						if((int)$val['quantity']>0)
						{


					?>
					<button class="btn btn-success2" onclick="addToCartpp2('<?php echo $val['product_id'] ?>',$('#homeqty-<?php echo $key;?>-<?php echo $k;?>').val())"><?php echo  ($val['in_cart']?"IN CART":"ADD TO CART") ;?></button>
					<?php
				}
				else
				{
					?>
						<button class="btn btn-info" id="notify_btn_<?php echo $val['product_id'];?>" onclick="notifyMe('<?php echo $val['product_id'] ?>')">NOTIFY WHEN AVAILABLE</button>
					<?php
				}
				?>
				</div>
				<?php	//if( ($k == 3 || $k%4 == 3) && ($k != 4) ){ ?>
					<!-- </div>
					<div class="row "  style="display: none;text-align: center"> -->
				<?php //} ?>
				
			<?php } ?>
			</div>
		</div>
		<div class="col-sm-1">
			<img src="image/slider-arrow.png" onclick="slideRight('<?php echo $key ?>')"  style="transform: rotate(180deg);position: absolute;top: 85px;width: 45px;cursor: pointer;">
		</div>
	</div>
	
	<?php
$href='javascript:void(0);';
	if ($key == 'IPAD_SCREENS') {
		$href=$this->url->link('catalog/repair_parts','path=2_6');


	 ?>	
	<div class="row" style="    text-align: center;font-size:18px;color:#3e3e3e;font-family: 'Montserrat';"><a href="<?php echo $href;?>" style="cursor: pointer;">SEE MORE IPAD SCREENS ></a></div>
	<?php }elseif ($key == 'TEMPERED_GLASS') { 
	$href=$this->url->link('catalog/temperedglass','path=3');
	?>
	<div class="row" style="    text-align: center;font-size:18px;color:#3e3e3e;font-family: 'Montserrat';"><a href="<?php echo $href; ?>" style="cursor: pointer;">SEE MORE TEMPERED GLASS ></a></div>
	<?php }elseif ($key == 'APPLE' || $key == 'SAMSUNG' || $key == 'LG') {
	switch(strtolower($key))
	{
	case 'lg':
	$href=$this->url->link('catalog/repair_parts','path=6');
	break;

	case 'samsung':
	$href=$this->url->link('catalog/repair_parts','path=10');
	break;

	case 'apple':
	$href=$this->url->link('catalog/repair_parts','path=2');
	break;



	}
	//$href=$this->url->link('catalog/repair_parts','path=6');

	 ?>
	<div class="row" style="    text-align: center;font-size:18px;color:#3e3e3e;font-family: 'Montserrat';"><a href="<?php echo $href;?>" style="cursor: pointer;">SEE MORE <?php echo $key ?> PARTS ></a></div>
	<?php }
	elseif($key=='MOTOROLA')
	{
		$href=$this->url->link('catalog/repair_parts','path=7');

		?>
<div class="row" style="    text-align: center;font-size:18px;color:#3e3e3e;font-family: 'Montserrat';"><a href="<?php echo $href;?>" style="cursor: pointer;">SEE ALL <?php echo $key ?> PARTS ></a></div>
		<?PHP
	}
	else{
	switch(strtolower($key))
	{
	case 'tools':
	$href=$this->url->link('catalog/repair_tools','path=5');
	break;
	case 'chargers':
	$href=$this->url->link('catalog/accessories','path=2&class_id=16');
	break;

	case 'adhesives':
	$href=$this->url->link('catalog/repair_tools','path=5&class_id=49');
	break;

	
	default:
	$href=$this->url->link('catalog/repair_parts');
	break;


	}
	 ?>
	<div class="row" style="    text-align: center;font-size:18px;color:#3e3e3e;font-family: 'Montserrat';"><a href="<?php echo $href;?>" style="cursor: pointer;">SEE MORE <?php echo $key ?>  ></a></div>
	<?php } ?>
	<hr>
<?php } ?>
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