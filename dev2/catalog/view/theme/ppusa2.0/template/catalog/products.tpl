<?php //echo '<pre>'; print_r($breadcrumbs); echo '</pre>'; die(); ?>
<?php echo $header; ?>
<?php $endBC = end($breadcrumbs); ?>
<main class="main">
	<div class="container category-page">
		<div class="row">
			<!-- <div class="col-md-12">
				<ul class="breadcrum clearfix">
					<?php foreach ($breadcrumbs as $key => $breadcrumb) : ?>
						<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
						<?php if (count($breadcrumbs) != ($key + 1)) { ?>
						<li class="seprator">></li>
						<?php } ?>
					<?php endforeach; ?>
				</ul>
				
			</div> -->
			<div class="col-md-12">
				<div class="text-right">
					<div class="filter-counter">
						Refine Products &nbsp;(<span class="filter-qty">0</span>)
					</div>
				</div>
			</div>
			<div class="col-md-3 filter-product" style="overflow-y: auto;">
				<div class="filter-inner">
					<div class="filter-buttons text-right">
						<a href="javascript:void(0);" class="btn btn-primary yellow-btn clear-filter">Clear All</a>
						<a href="javascript:void(0);" class="btn btn-primary apply-filter">APPLY</a>
						<!-- <a href="javascript:void(0);" class="btn btn-primary yellow-btn" id="close_filter_box">CLOSE</a> -->
					</div>
					<h2>Filter Products</h2>
					<div class="filter-group">
						<?php if ($submodels || $manufacturers) { ?>
						<div class="panel">
							<div class="panel-heading">
									<input type="checkbox" checked="checked" class="css-checkbox" id="checkAll">
									<label for="checkAll" data-parent="#accordion" class="css-label3">
										<a data-toggle="none" data-parent="#accordion" href="#collapse1">
										Phone model 
										</a>
									</label>
								
							</div>
							<br>
							<div id="collapse1" class="panel-collapse collapse in">
								<ul class="filter-check checkAll-parent" data-filter-name="Model">
									<?php foreach ($submodels as $submodel) { ?>
									<li>
										<a href="javascript:void(0);">
											<input type="checkbox" class="css-checkbox submodel" value="<?php echo $submodel['id']; ?>" id="submodel<?php echo $submodel['id']; ?>">
											<label for="submodel<?php echo $submodel['id']; ?>" class="css-label3"><?php echo $submodel['name']; ?></label>
										</a>
									</li>
									<?php } ?>

									<?php foreach ($manufacturers as $manufacturer) { ?>
									<li>
										<a href="javascript:void(0);">
											<input type="checkbox" class="css-checkbox manufacturer" <?php echo ($module == 'temperedglass' || $module == 'accessories' || $module == 'refurbishing' || $module == 'blowout' ?'checked="checked"':''); ?> value="<?php echo $manufacturer['id']; ?>" id="manufacturer<?php echo $manufacturer['id']; ?>">
											<label style="display: inline-block;" for="manufacturer<?php echo $manufacturer['id']; ?>" class="css-label3"><?php echo $manufacturer['name']; ?></label><i onclick="dropDown(this);" class="fa fa-angle-down" data-manufacturerid="<?php echo $manufacturer['id']; ?>" style="font-size:15px;margin-left:10px"></i>

										</a>
										<ul class="subfilter" style="display:none" data-filter-name="<?php echo $manufacturer['name']; ?>">
												
												<?php
												if($manufacturer['sub_models'])
												{
												?>
												<li><div class="panel-heading">
												<a>Sub Models</a>
												</div>
												</li>
												<?php
												foreach($manufacturer['sub_models'] as $man_sub_model)
												{
													?>
													<li>
<a href="javascript:void(0)"><input type="checkbox" value="<?php echo $man_sub_model['id'];?>" class="css-checkbox manufacturerid" checked id="<?php echo $manufacturer['id'];?>-<?php echo $man_sub_model['id'];?>"><label for="<?php echo $manufacturer['id'];?>-<?php echo $man_sub_model['id'];?>" class="css-label3"><?php echo $man_sub_model['name'];?></label></a>
</li>
													<?php
												}
												}
												?>

										</ul>
									</li>
									<?php } ?>

									<li>
										<a href="javascript:void(0)">
											
										</a>
									</li>
								</ul>
							</div>
						</div>
						<?php } ?>
						<?php if ($classes) { ?>
						<?php $main_name; ?>
						<?php foreach ($classes as $x => $class) : ?>
							<?php if ($class['main_name'] != $main_name) { ?>
							<div class="panel update-filters <?php echo ((int)$is_side_module_hidden==1?'hidden':''); ?>">
								<div class="panel-heading">
								<input type="checkbox" class="css-checkbox" <?php if(!$class_id) { ?>checked="checked" <?php } ?> id="selectAll<?php echo strtolower(str_replace(' ', '_', $class['main_name']));?>">
								<label for="selectAll<?php echo strtolower(str_replace(' ', '_', $class['main_name']));?>" class="css-label3">
									<a data-toggle="none" data-parent="#accordion" href="#<?php echo strtolower(str_replace(' ', '_', $class['main_name'])); ?>">
										<?php echo $class['main_name']; ?>
									</a>
								</label>
									
									
								</div>
								<br>
								<div id="<?php echo strtolower(str_replace(' ', '_', $class['main_name'])); ?>" class="panel-collapse collapse in">
									<ul class="filter-check">
										<?php $main_name = $class['main_name']; ?>
										<?php } ?>
										<li>
										<?php if ($class['id'] == $class_id) {
										 $enable = strtolower(str_replace(' ', '_', $class['name'])) . '-' . $class['id']; 
										 } ?>
											<a href="javascript:void(0)">
												<input type="checkbox" class="css-checkbox selectClass <?php echo strtolower(str_replace(' ', '_', $class['main_name']));?>"  <?php if(!$class_id) { ?> checked="checked" <?php } ?> value="<?php echo $class['id']; ?>" id="<?php echo strtolower(str_replace(' ', '_', $class['name'])) . '-' . $class['id']; ?>">

												<label style="<?php if ($class['id']==40 or $class['id']==88){ echo 'display:inline-block;'; } ?>" for="<?php echo strtolower(str_replace(' ', '_', $class['name'])) . '-' . $class['id']; ?>" class="css-label3"><?php echo $class['name']; ?></label>
												<?php
												if($class['id']==40 or $class['id']==88)
												{
												?>
<i onclick="dropDown(this);" class="fa fa-angle-down" style="font-size:15px;margin-left:10px"></i>

												<?php
												}
												?>

											</a>
											<ul class="subfilter" style="display:none" data-filter-name="<?php echo $class['name']; ?>"></ul>
											<input type="hidden" readonly id="checker<?php echo $class['id']; ?>" value="<?php echo $class['main_name']; ?>">
										</li>
										<?php if ($classes[($x + 1)]['main_name'] != $class['main_name']) { ?>
									</ul>
								</div>
							</div>
							<?php } ?>
						<?php endforeach; ?>
						<?php } ?>
					</div>		
				</div>	
			</div>
			<div class="col-md-9 right-content">
				<div class="filter-buttons row" style="display:none">
					<div class="col-md-1 col-sm-2 filter-buttons-left">
						<label>Filter:</label>
					</div>
					<div class="col-md-11 col-sm-10 filter-buttons-right">
						<ul class="list-inline">
						</ul>
						<a href="javascript:void(0);" class="btn btn-primary yellow-btn clear-filter" style="display: none;">Clear All</a>
					</div>
				</div>
				<ul style="display: none;" class="nav nav-tabs">
				<?php if ($module == 'repair_parts') { ?>
					<li class="active"><a class="loadProducts" data-contid="repairpartsid" data-name="Replacement Parts" href="<?php echo $endBC['href']; ?>#repairpartsid">Repair Parts</a></li>
				<?php } ?>
				<?php if ($module == 'repair_parts' || $module == 'repair_tools') { ?>
					<li <?php echo ($module == 'repair_tools')? 'class="active"': ''; ?>><a class="loadProducts" data-name="Repair Tools" data-contid="repairtoolsid" href="<?php echo $endBC['href']; ?>#repairtoolsid">Repair Tools</a></li>
				<?php } ?>
				<?php if ($module == 'repair_parts' || $module == 'accessories') { ?>
					<li <?php echo ($module == 'accessories')? 'class="active"': ''; ?>><a class="loadProducts" data-name="Accessories" data-contid="accessoriesid" href="<?php echo $endBC['href']; ?>#accessoriesid">Accessories</a></li>
				<?php } ?>
				<?php if ($module == 'temperedglass') { ?>
					<li <?php echo ($module == 'temperedglass')? 'class="active"': ''; ?>><a class="loadProducts" data-name="Tempered Glass" data-contid="temperedglassid" href="<?php echo $endBC['href']; ?>#temperedglass">Accessories</a></li>
				<?php } ?>
				</ul>
				<h2><?php echo $heading_title;?></h2>
				<div class="tab-content">
					<div id="<?php echo str_replace('_', '', $module); ?>id" class="tab-pane text-center fade in active">
						<div class="row listing-row">
							<?php foreach ($products as $product) : ?>
								<div class="col-md-2 small-row listing-items product_<?php echo $product['product_id']; ?>">
									<article class="related-product">
										<div class="image" style="height:150px">
											<img class="lazy" src="catalog/view/theme/ppusa2.0/images/spinner2.gif" data-original="<?php echo $product['thumb']; ?>" height="150" width="150" alt="<?php echo $product['name']; ?>" style="cursor:pointer" onClick="window.location='<?php echo $product['href'];?>'">
										</div>
										<h4 style="height: 80px"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
										
										<?php
					if((int)$product['quantity']>0)
						{
							$in_cart= (isset($this->session->data['cart'][$product['product_id']])?true:false);
							?>
										<div class="qtyt-box">
											<div class="input-group spinner">
												<span class="txt">QTY</span>
												<input type="text" class="form-control qty" value="1" style="color:#303030">
												<div class="input-group-btn-vertical">
													<button class="btn" type="button"><i class="fa fa-plus"></i></button>
													<button class="btn" type="button"><i class="fa fa-minus"></i></button>
												</div>
											</div>
										</div>
										<?php if($product['sale_price']){ ?>
										
										<p class="price"><span style="font-size: 13px;margin-right:5px;text-decoration:line-through;"><?php echo $product['price']; ?></span><span style="color: red;"><?php echo $product['sale_price']; ?></span></p>
										<?php } else {?>
										<p class="price"><span><?php echo $product['price']; ?></span></p>
										<?php } ?>
										<button onclick="addToCartpp2(<?php echo $product['product_id']; ?>, $(this).parent().find('.qty').val())" class="btn <?php echo ($in_cart?'btn-success2':'btn-info');?>"><?php echo ($in_cart?'In Cart ('.$this->session->data['cart'][$product['product_id']].')':'Add to cart');?></button>
										<?php
									}
									else
									{
										?>
										<div class="qtyt-box">
											<div class="input-group spinner">
												<span class="txt">QTY</span>
												<input type="text" class="form-control qty" disabled="" value="1" style="color:#303030">
												<div class="input-group-btn-vertical">
													<button class="btn" type="button" disabled><i class="fa fa-plus"></i></button>
													<button class="btn" type="button" disabled><i class="fa fa-minus"></i></button>
												</div>
											</div>
										</div>
											<!-- <div >
			<span class="oos_qty_error_<?php echo $product['product_id'];?>" style="font-size:11px;color:red"></span>
			<input type="text" class="form-control customer_email_<?php echo $product['product_id'] ?>" style="margin-bottom:48px" placeholder="Enter your Email" value="<?php echo $this->customer->getEmail();?>">
	</div> -->
	<?php if($product['sale_price']){ ?>
										
										<p class="price"><span style="font-size: 13px;margin-right:5px;text-decoration:line-through;"><?php echo $product['price']; ?></span><span style="color: red;"><?php echo $product['sale_price']; ?></span></p>
										<?php } else {?>
										<p class="price"><span><?php echo $product['price']; ?></span></p>
										<?php } ?>
	<button class="btn btn-danger" id="notify_btn_<?php echo $product['product_id'];?>" >Out of Stock</button>
										<?php
									}
									?>
									<?php

									if ((strtolower($product['class']['name']) == 'screen-lcdtouchscreenassembly' || strtolower($product['class']['name']) == 'screen-touchscreen' || strtolower($product['class']['name']) == 'battery-phone' || strtolower($product['class']['name']) == 'battery-tablet') && strtolower($product['quality'])=='premium') {
									?>

									<span class="overlay-x"></span>
									<?php
									}
									?>	
										
									</article>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
					<?php if ($module == 'repair_parts') { ?>
					<div id="repairtoolsid" class="tab-pane fade">
						<div align="center">
						<img src="./imp/images/loading.gif" style="width: 50px;">
						</div>
					</div>
					<div id="accessoriesid" class="tab-pane fade">
					<div align="center">
						<img src="./imp/images/loading.gif" style="width: 50px;">
						</div>
					</div>
					<?php } ?>
				</div>
				<div id="waypointFooter"   style="display:none;z-index:999999;position:fixed; bottom:50%; left:50%; padding:5px 40px; background:black; opacity:0.5; border-radius:5px;">
				<img src="catalog/view/theme/ppusa2.0/images/ajax-loader-3.gif" />
				</div>
			</div>
		</div>
	</div>
</main>
<input type="hidden" id="manufacturer_id" value="<?php echo $manufacturer_id; ?>">
<input type="hidden" id="device_id" value="<?php echo $device_id; ?>">

<input type="hidden" id="main_class_id" value="<?php echo $main_class_id; ?>">
<?php echo $footer; ?>
<script type="text/javascript">
$('#checkAll').change(function(event) {
	 if($('#checkAll').prop("checked")){
	 	$('.submodel').prop("checked",true);
	 	$('.manufacturer').prop("checked",true);
	 	$('.manufacturerid').prop("checked",true);

	 	loadProducts();
	 } else {
	 	$('.submodel').prop("checked",false);
	 	$('.manufacturer').prop("checked",false);
	 	$('.manufacturerid').prop("checked",false);
	 	loadProducts();
	 }
});

$('#selectAllreplacement_parts').change(function(event) {
	 if($('#selectAllreplacement_parts').prop("checked")){
	 	$('.replacement_parts').prop("checked",true);
	 	loadProducts();
	 } else {
	 	$('.replacement_parts').prop("checked",false);
	 	$('.attrid').prop('checked',false);
	 	loadProducts();
	 }
});
$('#selectAllrepair_tools').change(function(event) {
	 if($('#selectAllrepair_tools').prop("checked")){
	 	$('.repair_tools').prop("checked",true);
	 	loadProducts();
	 } else {
	 	$('.repair_tools').prop("checked",false);
	 	loadProducts();
	 }
});
$('#selectAllscreen_protectors').change(function(event) {
	 if($('#selectAllscreen_protectors').prop("checked")){
	 	$('.screen_protectors').prop("checked",true);
	 	loadProducts();
	 } else {
	 	$('.screen_protectors').prop("checked",false);
	 	loadProducts();
	 }
});
$('#selectAllaccessories').change(function(event) {
	 if($('#selectAllaccessories').prop("checked")){
	 	$('.accessories').prop("checked",true);
	 	loadProducts();
	 } else {
	 	$('.accessories').prop("checked",false);
	 	loadProducts();
	 }
});
$('#selectAllrefurbishing').change(function(event) {
	 if($('#selectAllrefurbishing').prop("checked")){
	 	$('.refurbishing').prop("checked",true);
	 	loadProducts();
	 } else {
	 	$('.refurbishing').prop("checked",false);
	 	loadProducts();
	 }
});

	$('.loadProducts').click(function(event) {
		$('.clear-filter').click();
		var class_name;
		$('.selectClass').each(function() {
			if ($(this).is(':checked')) {
				class_name = $('#checker'+ $(this).val()).val();
			}
		});
		//var class_name = $(this).data('name');
		var cont = $(this).data('contid');
		var filter = getFilter();
		$.ajax({
			url: '?route=catalog/<?php echo $module; ?>/loadFilterProducts',
			type: 'POST',
			dataType: 'json',
			data: {filter: filter, class_name: class_name},
			beforeSend: function() {
                $('#' + count).html('<img src="catalog/view/theme/ppusa2.0/images/spinner.gif">');
            }
		})
		.always(function(json) {
			$('#' + cont).html(json['products']);
			var classes = '';
			if (json['classes']) {
				var main_name = '';
				var loop = json['classes'].length;
				for (var i = 0; i < loop; i++) {
					if (json['classes'][i].main_name != main_name) {
						classes += '<div class="panel update-filters">';
						classes += '<div class="panel-heading">';
						classes += '<a data-toggle="collapse" data-parent="#accordion" href="#'+ json['classes'][i].main_name.split(' ').join('_') +'">';
						classes += json['classes'][i].main_name +'<i class="fa fa-angle-down"></i>';
						classes += '</a>';
						classes += '</div>';
						classes += '<div id="' + json['classes'][i].main_name.split(' ').join('_') + '" class="panel-collapse collapse">';
						classes += '<ul class="filter-check">';

						main_name = json['classes'][i].main_name;
					}
					
					classes += '<li>';
					classes += '<a href="javascript:void(0);">';
					classes += '<input type="checkbox" class="css-checkbox selectClass" value="'+ json['classes'][i].id +'" id="'+ json['classes'][i].name.split(' ').join('_') + '-' + json['classes'][i].id +'">';
					classes += '<label for="'+ json['classes'][i].name.split(' ').join('_') + '-' + json['classes'][i].id +'" class="css-label3">'+ json['classes'][i].name +'</label>';
					classes += '</a>';
					classes += '<ul class="subfilter" data-filter-name="'+ json['classes'][i].name +'">';
					classes += '</ul>';
					classes += '</li>';
					var x = i + 1;
					if (typeof json['classes'][x] == 'undefined' || json['classes'][x].main_name != json['classes'][i].main_name) {						
						classes += '</ul>';
						classes += '</div>';
						classes += '</div>';
					}
				}
			}
			$('.update-filters').remove();
			$('.filter-group').append(classes);
			$('#main_class_id').val(json['main_class_id']);
		});

	});
	function getFilter () {
		class_id = [];
		$('.selectClass').each(function() {
			if ($(this).is(':checked')) {
				class_id.push($(this).val());
			}
		});

		var attrib = {};
		$('.selectClass').each(function(index, el) {
			if ($(el).is(':checked')) {
				var cID = $(el).val();
				attrib['c'+cID] = [];
				$(el).parent().next('.subfilter').find('.attrid').each(function(index, atEl) {
					if ($(atEl).is(':checked')) {
						attrib['c'+cID].push($(atEl).val());
					}
				});

			}
		});

		var sub_device_id = {};
		$('.manufacturer').each(function(index, el) {
			if ($(el).is(':checked')) {
				var cID = $(el).val();
				sub_device_id['c'+cID] = [];
				$(el).parent().next('.subfilter').find('.manufacturerid').each(function(index, atEl) {
					if ($(atEl).is(':checked')) {
						sub_device_id['c'+cID].push($(atEl).val());
					}
				});

			}
		});


		var model_id = [];
		$('.submodel').each(function() {
			if ($(this).is(':checked')) {
				model_id.push($(this).val());
			}
		});

		var manufacturer_ids = [];
		$('.manufacturer').each(function() {
			if ($(this).is(':checked')) {
				manufacturer_ids.push($(this).val());
			}
		});
		// console.log(class_id);

		var filter = {};
		filter['class_id'] = class_id;
		filter['manufacturer_id'] = $('#manufacturer_id').val();
		filter['device_id'] = $('#device_id').val();
		filter['model_id'] = model_id.toString();
		// filter['main_class_id'] = $('#main_class_id').val();
		filter['attrib_id'] = attrib;
		filter['manufacturers'] = manufacturer_ids;
		filter['sub_device_id'] = sub_device_id;
		// filter['group_id'] = $('#priceGroup').val();
		return filter;
	}
	$(document).ready(function() {
		<?php
		if(!isset($this->request->get['class_name']))
		{
			?>
$('.submodel').prop('checked',true);
			<?php
		}
		?>
		
		$('.filter-group').on('change', '.selectClass', function() {
			var subfilter = $(this).parent().next('.subfilter');
	
			var class_id = $(this).val();
			var obj = $(this);
			var attrib_check = false;
			 if(class_id=='40' || class_id=='88')
					 {
					 	attrib_check = true; // to check all attributes by default
					 	// console.log(obj.is(":checked"));
					// 	alert('here');
					 if(obj.is(":checked") && subfilter.text()!='')
					{
						// obj.parent().find('i.fa').removeClass('fa-angle-down').addClass('fa-angle-up');
						$(subfilter).find('input[type=checkbox]').prop('checked',true);
					//subfilter.show(500);
					}
					else
					{
						obj.parent().find('i.fa').removeClass('fa-angle-up').addClass('fa-angle-down');
						$(subfilter).find('input[type=checkbox]').prop('checked',false);
						subfilter.hide(500);	
					}
					 }
			if (subfilter.text() == '') {
				var filter = getFilter();
				$.ajax({
					url: '?route=catalog/<?php echo $module; ?>/loadAttributes',
					type: 'POST',
					dataType: 'json',
					data: {filter: filter, class: class_id}
				}).always(function(json) {
					var attributes = '';
					var main_name = '';

					if (json['attributes']) {
						var loop = json['attributes'].length;
						for (var i = 0; i < loop; i++) {
							if (json['attributes'][i].main_name != main_name) {
								attributes += '<li>';
								attributes += '<div class="panel-heading">';
								attributes += '<br><a>';
								attributes += json['attributes'][i].main_name;
								attributes += '</a>';
								attributes += '</div>';
								main_name = json['attributes'][i].main_name;
							}
							if(attrib_check==false)
							{
								if(json['attributes'][i].id=='<?php echo (int)$default_attrib_id;?>')
								{
									attrib_check=true;
								}
							}
							attributes += '<li>';
							attributes += '<a href="javascript:void(0)">';
							attributes += '<input type="checkbox" '+(attrib_check==true?' checked="checked"':'')+'  value="'+ json['attributes'][i].id +'" class="css-checkbox attrid" id="'+ class_id + '-' + json['attributes'][i].id +'">';
							attributes += '<label for="'+ class_id + '-' + json['attributes'][i].id +'" class="css-label3">'+ json['attributes'][i].name +'</label>';
							attributes += '</a>';
							attributes += '</li>';
						}
					}
					subfilter.html(attributes);



					<?php
	if($module=='temperedglass' )
	{
		?>
		//$('.filter-check input[type=checkbox].attrid').trigger('change');
					if(subfilter.is(":visible"))
					{
						$(this).parent().find('i.fa').removeClass('fa-angle-up').addClass('fa-angle-down');

					subfilter.hide(500);
					}
					else
					{
						$(this).parent().find('i.fa').removeClass('fa-angle-down').addClass('fa-angle-up');
						subfilter.show(500);	
					}
					<?php
				}
				?>


					loadProducts();
				});
			} else {
				//console.log('here');
				//subfilter.find('.attrid').trigger('click');
				// $('.filter-check input[type=checkbox].attrid').trigger('change');
				loadProducts();
			}
		});
		$('.filter-group').on('click', '.submodel', function() {
			loadProducts();
		});
		$('.filter-group').on('click', '.manufacturer', function() {
			loadProducts();
		});
		$('.filter-group').on('click', '.attrid', function() {
			setTimeout(function(){ loadProducts(); }, 1000);
		});
		$('.filter-group').on('click', '.manufacturerid', function() {
			 setTimeout(function(){ loadProducts(); }, 1000);

		});

		<?php
	if($module=='temperedglass' )
	{
		?>
$('.selectClass').trigger('change');
		<?php
	}

	if($module=='repair_parts' )
	{
		?>
$('#internal_components-40').trigger('change'); // load internal components attributes
$('#housing-88').trigger('change'); // load internal components attributes
		<?php
	}
	?>
	});

$('.filter-group').on('click', '.manufacturer', function() {
			var subfilter = $(this).parent().next('.subfilter');
			// console.log(subfilter);
			// console.log($(this).parent().next('.subfilter'));
			var class_id = $(this).parent().find('.fa-angle-down').attr('data-manufacturerid');
			if (subfilter.text() == '') {
				var filter = getFilter();
				$.ajax({
					url: '?route=catalog/<?php echo $module; ?>/loadModels',
					type: 'POST',
					dataType: 'json',
					data: {filter: filter, class: class_id}
				}).always(function(json) {
					var attributes = '';
					var main_name = '';

					if (json['attributes']) {
						var loop = json['attributes'].length;
						attributes += '<li>';
								attributes += '<div class="panel-heading">';
								attributes += '<a style="padding-bottom:0px">';
								attributes += 'Sub Models';
								attributes += '</a>';
								attributes += '</div>';

						for (var i = 0; i < loop; i++) {
							if (json['attributes'][i].main_name != main_name) {
								// attributes += '<li>';
								// attributes += '<div class="panel-heading">';
								// attributes += '<br><a>';
								// attributes += json['attributes'][i].main_name;
								// attributes += '</a>';
								// attributes += '</div>';
								// main_name = json['attributes'][i].main_name;
							}
							
								// main_name = json['attributes'][i].main_name;


							attributes += '<li>';
							attributes += '<a href="javascript:void(0)">';
							attributes += '<input type="checkbox" value="'+ json['attributes'][i].id +'" class="css-checkbox manufacturerid" id="'+ class_id + '-' + json['attributes'][i].id +'">';
							attributes += '<label for="'+ class_id + '-' + json['attributes'][i].id +'" class="css-label3">'+ json['attributes'][i].name +'</label>';
							attributes += '</a>';
							attributes += '</li>';
						}
					}
					subfilter.html(attributes);
					$('.filter-check input[type=checkbox].manufacturerid').trigger('change');

					if($(this).is(":checked"))
					{
						$(this).parent().find('i.fa').removeClass('fa-angle-down').addClass('fa-angle-up');

					subfilter.show(500);
					}
					else
						$(this).parent().find('i.fa').removeClass('fa-angle-up').addClass('fa-angle-down');
					{
						$(subfilter+' input[type=checkbox]').prop('checked',false);
						subfilter.hide(500);	
					}
					// if(subfilter.is(":visible"))
					// {

					// subfilter.hide(500);
					// }
					// else
					// {
					// 	subfilter.show(500);	
					// }
					loadProducts();
				});
			} else {
				//console.log('here');

				if($(this).is(":checked"))
					{
						$(this).parent().find('i.fa').removeClass('fa-angle-down').addClass('fa-angle-up');

					subfilter.show(500);
					$(subfilter).find('input[type=checkbox]').prop('checked',true);
					
					//$('#manufacturer'+class_id).prop("checked",true);
					}
					else
					{
						$(this).parent().find('i.fa').removeClass('fa-angle-up').addClass('fa-angle-down');
						$(subfilter).find('input[type=checkbox]').prop('checked',false);
						subfilter.hide(500);
						// alert('checked');
					}
				loadProducts();
				//$(this).parent().find('.fa-angle-down').prop("checked",true);
				//subfilter.find('.attrid').trigger('click');
				// $('.filter-check input[type=checkbox].attrid').trigger('change');
			}
		});
function dropDown(obj){

var subfilter = $(obj).parent().next('.subfilter');
	var class_id = $(obj).parent().find('.fa-angle-down').attr('data-manufacturerid');
	
				//console.log('here');

				if(subfilter.is(":visible"))
					{
						$(obj).removeClass('fa-angle-up').addClass('fa-angle-down');
					subfilter.hide(500);
					//$('#manufacturer'+class_id).prop("checked",true);
					}
					else
					{
						$(obj).removeClass('fa-angle-down').addClass('fa-angle-up');
						subfilter.show(500);
					}
		
		
			

}
 var pg = 1;
	function loadProducts (is_ajax) {
		 is_ajax = is_ajax || 0;
		 if(is_ajax==0)
		 {
		 	pg=1;
		 	callable = true;
		 }
		var count = window.location.hash.substring(1);
		if (!count) {
			count = '<?php echo str_replace('_', '', $module); ?>id';
		}
		var class_name;
		$('.selectClass').each(function() {
			if ($(this).is(':checked')) {
				//class_name = $('#checker'+ $(this).val()).val();
			}
		});
		//var class_name = $('.nav-tabs').find('.active').find('a').data('name');
		var cont = $(this).data('contid');
		var filter = getFilter();
		$.ajax({
			url: '?route=catalog/<?php echo $module; ?>/loadFilterProducts',
			type: 'POST',
			dataType: 'json',
			data: {filter: filter, class_name: class_name,page:pg},
			beforeSend: function() {
				callIndicator = true;
				if(is_ajax==0)
				{
                $('#' + count).html('<img src="catalog/view/theme/ppusa2.0/images/spinner.gif">');
					
				}
				else
				{
					$('#waypointFooter').show();
				}
            }
		}).always(function(json) {
			callIndicator = false;
			if(is_ajax==0)
			{
			$('#' + count).html(json['products']);
			
				
			}
			else
			{
				 if (json['products'].search("No Record Found") > -1) {
                                callable = false;
                                json['products'] = '<div class="row text-center"><div class="col-md-12"><h3>End of Results</h3></div></div>';
                            }
				$('#waypointFooter').hide();
				$('#' + count+' .listing-row').append(json['products']);
			}
		});
	}
	<?php if ($enable && isset($enable)) { ?>
		$(document).ready(function($) {
			$('#<?php echo $enable; ?>').trigger('click');
		});
	<?php } ?>

	<?php if (isset($this->request->get['class_name'])) { ?>
		$(document).ready(function($) {
			// alert('here');
			// $('[data-name="<?php echo $this->request->get['class_name']; ?>"]').trigger('click');
			$('.submodel').prop('checked',false);
			// $('#checkAll').prop('checked',false);
			$('#submodel<?php echo $this->request->get['submodel']; ?>').trigger('click');
		});
	<?php } ?>

	

	$('body').click(function(evt){   
	
		if($(evt.target).hasClass('overlay-right'))
		{
			$(".filter-product").removeClass("slide-left");
			$('.logo').attr('style','');
			$('.overlay-right').hide();
		}

	});
	// $(document).ready(function(){
		   
	// });
</script>
<script>
        var callable      = true;
        var callIndicator = false;
       
        var order_by      = 'date_published';
        var order         = 'DESC';
        var type          = 0;
        var lang_id       = 0;
        var platform_id   = 31;
        var author_id     = 0;
        var size          = 0;

        $(window).scroll(function() {
        	if($('.listing-items').length)
        	{
       var hT = $('.listing-items:last').offset().top,
       hH = $('.listing-items:last').outerHeight(),
       wH = $(window).height(),
       wS = $(this).scrollTop();
   if (wS > (hT+hH-wH)){
            	
                if (callIndicator === false && callable === true) {
                    pg++;
                   loadProducts(1);
                    return false;
                }
            }
        }
        });
    </script>

<!-- @End of main --> 