<?php echo $header; ?>
<div id="compatibility-pop" class="popup">
	<div class="popup-head">
		<h2 class="blue-title uppercase">Compatibility Issues</h2>
	</div>
	<div class="popup-body">
		<form class="form-horizontal">
			<div class="form-group">
				<label for="inputName" class="col-sm-3 control-label">Name</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" placeholder="Gerald Anderson" id="inputName">
				</div>
			</div>
			<div class="form-group">
				<label for="inputEmail" class="col-sm-3 control-label">Email</label>
				<div class="col-sm-9">
					<input type="email" class="form-control" placeholder="e.g., mightypirate@grogmail.com" id="inputEmail">
				</div>
			</div>
			<div class="parts-detail">
				<p>Manufacturer: Samsung</p>
				<p>Modal: Galaxy S6</p>
				<p>Sub Model: G900A, G900F, G900R</p>
			</div>
			<h3>Problem Products:</h3>
			<div class="problem-product">
				<div class="form-group vertical-group">
					<label for="inputEmail" class="col-sm-12 control-label">Select Item</label>
					<div class="col-sm-12">
						<select class="selectpicker">
							<option value="">Select Item</option>
							<option value="">Select Item</option>
							<option value="">Select Item</option>
							<option value="">Select Item</option>
						</select>
					</div>
				</div>
				<div class="form-group vertical-group">
					<div class="col-md-4">
						<div class="row">
							<label for="inputSKU" class="col-sm-12 control-label">SKU</label>
							<div class="col-sm-12">
								<input type="text" class="form-control" id="inputSKU">		
							</div>
						</div>
					</div>
					<div class="col-md-8 pl0">
						<div class="row">
							<label for="inputProduct" class="col-sm-12 control-label">Product</label>
							<div class="col-sm-12">
								<input type="text" class="form-control" id="inputProduct">	
							</div>
						</div>
					</div>
				</div>
				<div class="form-group vertical-group">
					<label class="col-sm-12 control-label">Issue</label>
					<div class="col-sm-12">
						<textarea class="form-control" placeholder="Describe issue here.."></textarea>
					</div>
				</div>
			</div>
			<div class="popup-btns clearfix">
				<button class="btn btn-primary pull-left" type="submit">Additional Product</button>
				<button class="btn btn-primary pull-right" type="submit">Submit Report</button>
			</div>
		</form>	
	</div>
</div>
<main class="main">
	<div class="container catalog-page">
		<h1><a href="#">Product Catalog</a></h1>
		<div class="catalog-note">
			Find compatibile repair parts quickly and easily with our interactive 
			product catalog
		</div>
		<div class="row catalog-steps">
			<div class="catalog-steps-col col-md-3">
				<div class="catalog-steps-head">
					<h3>Step 1</h3>
					<p>Choose the make</p>
				</div>
				<div class="catalog-steps-title clearfix hidden">
					<span class="circle">1.</span>
					<div class="title-name">Make
						<span class="title-name-dtl">Choose the make</span>
						<i class="fa fa-angle-down"></i>
					</div>
				</div>
				<div class="catalog-steps-box">
					<div class="catalog-steps-inner">
						<ul class="cataglo-list" id="loadManufacturers">
						</ul>
					</div>
				</div>
			</div>
			<div class="catalog-steps-col col-md-3">
				<div class="catalog-steps-head">
					<h3>Step 2</h3>
					<p>Choose the model</p>
				</div>
				<div class="catalog-steps-title clearfix hidden">
					<span class="circle">2.</span>
					<div class="title-name">Model
						<span class="title-name-dtl">Choose the model</span>
						<i class="fa fa-angle-down"></i>
					</div>
				</div>
				<div class="catalog-steps-box">
					<div class="catalog-steps-inner">
						<ul class="cataglo-list" id="loadModels">
						</ul>
					</div>
				</div>
			</div>
			<div class="catalog-steps-col col-md-3">
				<div class="catalog-steps-head">
					<h3>Step 3</h3>
					<p>Choose submodel</p>
				</div>
				<div class="catalog-steps-title clearfix hidden">
					<span class="circle">3.</span>
					<div class="title-name">Sub-Model
						<span class="title-name-dtl">Choose submodel(s)</span>
						<i class="fa fa-angle-down"></i>
					</div>
				</div>
				<div class="catalog-steps-box">
					<div class="text-center">
						<a href="#" class="btn btn-secondary find-parts">Find Parts</a>
					</div>
					<div class="catalog-steps-inner">
						<ul class="cataglo-list active-anchor" id="loadSubModels">
						</ul>
					</div>
				</div>
			</div>
			<div class="catalog-steps-col  col-md-3 filter-product">
				<div class="catalog-steps-head">
					<h3>Step 4</h3>
					<p>Filter By Product Type</p>
				</div>
				<div class="catalog-steps-title clearfix hidden">
					<span class="circle">4.</span>
					<div class="title-name">Product Type
						<span class="title-name-dtl">Product Type</span>
						<i class="fa fa-angle-down"></i>
					</div>
				</div>
				<div class="catalog-steps-box">
					<div class="catalog-steps-inner">
						<ul id="class" class="cataglo-list filter-check active-anchor">
							
						</ul>
					</div>
				</div>	
			</div>
		</div>
		<div class="row notfind">
			<div class="col-md-5">
				<a href="#compatibility-pop" class="blue fancybox">See Any Compatibility Issues? Please Let Us Know.</a>
			</div>
			<div class="col-md-5">
				<a href="#" class="blue">Didn't find what you were looking for ?</a>
			</div>
		</div>
		<div class="steps-info">
			<p>A great new way to find parts, tools and accessories for smartphones and tablets.
				It has never been the easier way to find the right parts for any device</p>
				<ul>
					<li>Just pick the make, model and sub model you need parts for</li>
					<li>Then filter the list by product types and features.</li>
				</ul>
				<small class="text-center">Add items to your cart, and you're ready to go</small>
			</div>
			<div class="row">

				<div class="col-md-12 right-content">
					<div class="text-right viewport-btns">	
						<a href="#grid-view" class="viewport-icon"><i class="fa fa-th-large"></i></a>
						<a href="#list-view" class="viewport-icon"><i class="fa fa-th-list"></i></a>
					</div>
					<div id="products">
						<div id="list-view" class="product-view">
							<div class="product-detail row pr0">
								<div class="product-detail-inner clearfix">
									<div class="col-md-2 product-detail-img">
										<div class="image">
											<a href="product-details.php"><img src="images/cart-detail/iphone-big.png" alt=""></a>
										</div>
									</div>
									<div class="col-md-10 product-detail-text">
										<h3><a href="product-details.php">iPhone 6 Screen Assembly with LCD &amp; Digitizer</a></h3>
										<div class="row">
											<div class="col-md-3">
											</div>
											<div class="col-md-4">
												<div class="text-center">
													<div class="review-area show-mobile">
														<ul class="review-stars clearfix">
															<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
															<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
															<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
															<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
															<li><a href="#"><i class="fa fa-star"></i></a></li>
														</ul>
														<a href="#" class="review-links underline">30 reviews</a>
													</div>
													<span class="favorite"><i class="fa fa-heart"></i><a href="#" class="underline">Favorite</a></span>
												</div>
												<div class="cart-quality">
													<table class="table">
														<thead>
															<tr>
																<th>Quantity</th>
																<th>Our Price</th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td>1</td>
																<td>$105.00</td>
															</tr>
															<tr>
																<td>3-9</td>
																<td>$100.00</td>
															</tr>
															<tr>
																<td>10+</td>
																<td>$95.00</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
											<div class="col-md-5 cart-total-wrp">
												<div class="cart-total text-right">
													<div class="qtyt-box">
														<div class="input-group spinner">
															<span class="txt">QTY</span>
															<input type="text" class="form-control" value="1">
															<div class="input-group-btn-vertical">
																<button class="btn " type="button"><i class="fa fa-plus"></i></button>
																<button class="btn" type="button"><i class="fa fa-plus"></i></button>
															</div>

														</div>
													</div>
													<h3>$ 105.00</h3>
													<button class="btn btn-success addtocart"><img src="images/icons/basket.png" alt="">Add to Cart</button>
												</div>
											</div>
										</div>
									</div>
									<div class="features-row">
										<div class="review-area">
											<ul class="review-stars clearfix">
												<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
												<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
												<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
												<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
												<li><a href="#"><i class="fa fa-star"></i></a></li>
											</ul>
											<p><a href="#" class="review-links underline">40 reviews</a></p>
										</div>
										<div class="row item-features">
											<ul>
												<li class="col-md-6 col-xs-3">
													Phone model
												</li>
												<li class="col-md-6 col-xs-3">
													Product
												</li>
											</ul>
											<ul>
												<li class="col-md-6 col-xs-3">
													Compatibility
												</li>
												<li class="col-md-6 col-xs-3">
													Type
												</li>
											</ul>
											<ul>
												<li class="col-md-6 col-xs-3">
													Information
												</li>
												<li class="col-md-6 col-xs-3">
													Information
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
		<input type="hidden" id="manufacturer_id">
		<input type="hidden" id="device_id">
		<input type="hidden" id="model_id">
		<input type="hidden" id="main_class_id">
		<input type="hidden" id="priceGroup" value="<?php ($group_id)? $group_id: '1633'; ?>1633">
	</main>
	<script type="text/javascript">
		var autoFilter = <?php echo json_encode($filter); ?>;
		var autoClass = 0;
		$(document).ready(function($) {
			loadManufacturers();
		});

		function loadManufacturers () {
			$.ajax({
				url: 'imp/product_catalog/man_catalog.php',
				type: 'POST',
				dataType: 'json',
				data: {action: 'loadManufacturers'},
			}).always(function(json) {
				var data = '';
				for (var i = 0; i < json.length; i++) {
					data += '<li><a href="javascript:void(0);" class="item nav-'+ json[i].id +'" onclick="loadNav(this, \'loadModels\', \'\', \''+ json[i].id +'\');">'+ json[i].name +'</a></li>';
				}
				$('#loadManufacturers').html(data);
				autoLoad('loadManufacturers', 'manufacturer_id');
			});
		}

		function autoLoad(load, id) {
			var action = {};
			action['loadManufacturers'] = 'manufacturer_id';
			action['loadModels'] = 'device_id';
			if (load == 'loadSubModels') {
				if (autoFilter['model_id']) {
					var models = autoFilter['model_id'];
					$('#model_id').val(models.slice(1, models.length - 1) + ',');
					for (var i = models.length - 1; i >= 0; i--) {
						if (i != 0) {
							$('#' + load).find('.nav-'+models[i]).addClass('active').find('input').prop('checked', true);
						} else {
							$('#' + load).find('.nav-'+models[i]).click();
						}
					}
					autoFilter['model_id'] = false;
				}
			} else if (load == 'class_id') {
				if (autoFilter['class_id']) {
					classes = autoFilter['class_id'];
					for (var i = classes.length - 1; i >= 0; i--) {
						var container = $('#class').find('.class_id[value='+ classes[i] +']');
						var pContainer = container.parents('.classex');
						container.click();
						if (pContainer.hasClass('hidex')) {
							pContainer.parent().find('.button-re').click();
						}

					}
					autoFilter['class_id'] = false;
				}
			} else if (load == 'attrib_id') {
				if (autoFilter['attrib_id']) {
					if (autoFilter['attrib_id']['c'+autoClass]) {
						attribs = autoFilter['attrib_id']['c'+autoClass];
						for (var i = attribs.length - 1; i >= 0; i--) {
							$('.attrib').find('input[data-val-id='+ autoClass + '-' + attribs[i] +']').prop('checked', true);
						}
						autoFilter['attrib_id']['c'+autoClass] = false;
						loadProducts(1);
					}
				}
			} else {
				if (autoFilter[action[load]]) {
					$('#' + load).find('.nav-'+autoFilter[action[load]]).click();
					autoFilter[action[load]] = false;
				}
			}
		}
	</script>

	<script type="text/javascript">
		var cAction = '';
		var cClassID = 0;
		var cOtherId = 0;
		var cfbProducts;
		String.prototype.ucFirst = function() {
			return this.charAt(0).toUpperCase() + this.slice(1);
		}

		function validEmail(email) {
			var re = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
			return email.match(re);
		}

		function getShareLink () {
			var filter = getFilter();

			$.ajax({
				url: 'imp/product_catalog/man_catalog.php',
				type: 'POST',
				dataType: 'json',
				data: {filter: filter, perform: 'getShareLink'}
			}).always(function(json) {
				window.prompt("Copy to clipboard: Ctrl+C, Enter", json['link']);
			});
		}

		function notifyMe (product_id) {
			$('#loadingmessage').show();
			var product = $('.product-' + product_id);
			product.find('.error').text('');
			var email = product.find('.notifyemail').val();
			var button = product.find('.notifyMeBtn');
			if (!validEmail(email)) {
				product.find('.error').text('Enter Valid Email');
				return false;
			}
			$.ajax({
				type: 'post',
				url: 'index.php?route=product/product/notify',
				data: {data: email,product_id: product_id},
				dataType: 'json',
				success: function(json) {
					button.text('Notification Set');
					product.find('.notifyemail').hide();
				}
			});
		}

		function number_format (num, fixed) {
			return parseFloat(Math.round(num * 100) / 100).toFixed(fixed);
		}

		function createSelectFeedback () {
			var html = '<select onChange="fillProduct(this);" class="pname">';
			html += '<option>Select Item</option>';
			html += '<option>Other</option>';
			for (var i = cfbProducts.length - 1; i >= 0; i--) {

				html += '<option>'+ cfbProducts[i].model +' - '+ cfbProducts[i].name +'</option>';
			}
			html += '</select>';
			return html;
		}

		function randerFBProduct () {
			var html = '<div class="ui equal wide grid secondary segment product">';
			html += '<span class="close" onclick="$(this).parent().remove();">x</span>';
			html += '<div class="row">';
			html += '<div class="sixteen wide column">';
			html += createSelectFeedback();
			html += '</div>';
			html += '</div>';
			html += '<div class="row">';
			html += '<div class="four wide column centered">';
			html += '<label>SKU</label>';
			html += '</div>';
			html += '<div class="twelve wide column centered">';
			html += '<label>Product Name</label>';
			html += '</div>';
			html += '<div class="four wide column centered">';
			html += '<input type="text" disabled="" name="fpsku" placeholder="SKU" />';
			html += '</div>';
			html += '<div class="twelve wide column centered">';
			html += '<input type="text" disabled="" name="fpname" placeholder="Name" />';
			html += '</div>';
			html += '</div>';
			html += '<div class="row">';
			html += '<div class="sixteen wide column">';
			html += '<label>Describe Problem</label>';
			html += '</div>';
			html += '<div class="sixteen wide column">';
			html += '<textarea name="fpissue" placeholder="Describe Issue here..."></textarea>';
			html += '</div>';
			html += '</div>';
			html += '</div>';
			return html;
		}

		function fillProduct(t) {
			var product = $(t).val();
			var productC = $(t).parent().parent().parent();
			if (product == 'Other') {
				productC.find('input[name="fpsku"]').val('').removeAttr('disabled');
				productC.find('input[name="fpname"]').val('').removeAttr('disabled');
			} else if (product == 'Select Item') {
				productC.find('input[name="fpsku"]').attr('disabled', 'disabled');
				productC.find('input[name="fpname"]').attr('disabled', 'disabled');
			} else {
				product = product.split(' - ');
				productC.find('input[name="fpsku"]').val(product[0]).attr('disabled', 'disabled');
				productC.find('input[name="fpname"]').val(product[1]).attr('disabled', 'disabled');
			}
		}

		function randerFeedBack (manufacturer, model, smodels) {
			var html = '<div class="feedback">';
			html += '<div class="ui secondary segment">';
			html += '<span class="close" onclick="$(this).parent().parent().remove();">x</span>';
			html += '<h2>Compatibility Issues</h2>';
			html += '<div class="row">';
			html += '<div class="ui equal wide grid">';
			html += '<div class="fourteen wide column centered">';
			html += '<div class="ui equal wide grid">';
			html += '<div class="row">';
			html += '<div class="three wide column">';
			html += '<label>Name:</label>';
			html += '</div>';
			html += '<div class="thirteen wide column">';
			html += '<input name="fName" value="<?php echo ($isLogged)? $userFirstName . " " . $userLastName: ""; ?>" type="text" />';
			html += '</div>';
			html += '</div>';
			html += '<div class="row">';
			html += '<div class="three wide column">';
			html += '<label>Email:</label>';
			html += '</div>';
			html += '<div class="thirteen wide column">';
			html += '<input name="fEmail" value="<?php echo ($isLogged)? $userEmail: ""; ?>" type="text" />';
			html += '</div>';
			html += '</div>';
			html += '<div class="row">';
			html += '<div class="three wide column">';
			html += '<label>Manufacturer:</label>';
			html += '</div>';
			html += '<div class="thirteen wide column manufacturer">';
			html += '<span>'+ manufacturer +' <input type="hidden" name="fManufacturer" value="'+ manufacturer +'" /></span>';
			html += '</div>';
			html += '</div>';
			html += '<div class="row">';
			html += '<div class="three wide column">';
			html += '<label>Model:</label>';
			html += '</div>';
			html += '<div class="thirteen wide column model">';
			html += '<span>'+ model +' <input type="hidden" name="fModel" value="'+ model +'" /></span>';
			html += '</div>';
			html += '</div>';
			html += '<div class="row">';
			html += '<div class="three wide column">';
			html += '<label>Sub Models:</label>';
			html += '</div>';
			html += '<div class="thirteen wide column smodels">';
			for (var i = smodels.length - 1; i >= 0; i--) {
				html += '<span>'+ smodels[i] + '</span>' + ((i > 0)? ', ': '');
			}
			html += ' <input type="hidden" name="fSmodels" value="'+ smodels +'" /></div>';
			html += '</div>';
			html += '</div>';
			html += '<div class="fProducts">';
			html += '<h2>Problem Products</h2>';
			html += randerFBProduct();
			html += '</div>';
			html += '<div class="error">';
			html += '</div>';
			html += '<button class="ui blue button" onclick="$(\'.fProducts\').append(randerFBProduct());">Additional Product</button>';
			html += '<button class="ui blue button" onclick="submitFeedbackIssue();">Submit Report</button>';
			html += '</div>';
			html += '</div>';
			html += '</div>';
			html += '</div>';
			html += '</div>';

			return html;
		}

		function submitFeedbackIssue () {
			var name = $('.feedback').find('input[name="fName"]').val();
			var email = $('.feedback').find('input[name="fEmail"]').val();
			var manufacturer = $('.feedback').find('input[name="fManufacturer"]').val();
			var model = $('.feedback').find('input[name="fModel"]').val();
			var smodels = $('.feedback').find('input[name="fSmodels"]').val();
			var subject = 'Product Catalog Compatibility Issue';
			var description = '';
			description += '<div><span>Manufacturer: </span><span>'+ manufacturer +'</span></div><br>';
			description += '<div><span>Model: </span><span>'+ model +'</span></div><br>';
			description += '<div><span>Sub Models: </span><span>'+ smodels +'</span></div><br><br>';
			var i = 0;
			$('.feedback').find('.product').each(function() {
				var sku = $(this).find('input[name="fpsku"]').val();
				var pname = $(this).find('input[name="fpname"]').val();
				var pissue = $(this).find('textarea[name="fpissue"]').val();
				if (sku && pissue) {
					description += '<div><span>SKU: </span><span>'+ sku +'</span></div><br>';
					description += '<div><span>Product: </span><span>'+ pname +'</span></div><br>';
					description += '<div><span>Issue: </span><span>'+ pissue +'</span></div><br>';
					i++;
				}
			});
			var error = null;
			if (i == 0) {
				error = 'Please Select Products';
			}
			if (!validEmail(email)) {
				error = 'Please enter Email';
			}
			if (!name) {
				error = 'Please enter Name';
			}

			if (error) {
				$('.feedback').find('.error').text(error);
			} else {
				$.ajax({
					url: 'imp/freshdesk/create_ticket.php',
					type: 'POST',
					dataType: 'json',
					data: {name: name, email: email, subject: subject, description: description, action: 'create'},
				}).always(function(json) {
					if (json['success']) {
						$('.cfb').find('.error').remove();
						$('.cfb').append('<span class="success">Issue Reported</span>');
						$('.feedback').remove();
					} else {
						$('.feedback').find('.error').text('Please Try some other time');
					}
				});
			}

		}

		function feedbackForm() {
			var filter = getFilter();
			var manufacturer = $('#loadManufacturers').find('.nav-' + filter.manufacturer_id).text();
			var model = $('#loadModels').find('.nav-' + filter.device_id).text();
			var submodels = filter.model_id;
			var submodels = submodels.split(",");
			var smodels = [];
			for (var i = 0; i < submodels.length; i++) {
				if (submodels[i]) {
					smodels.push($('#loadSubModels').find('.nav-' + submodels[i]).text());
				}
			}
			if (!cfbProducts) {
				$('.cfb').find('.error').remove();
				$('.cfb').append('<span class="error">Please Select Sub models to report</span>');
				return false;
			}
			if (!$('.feedback').text()) {
				$('.cfb').append(randerFeedBack(manufacturer, model, smodels));
			}
		}

		function loadNav (t, action, classID, otherId) {
			setFilter(action, otherId);
			var colId = classID;
			if (otherId) {
				colId = otherId;
			}
			if (action) {
				$(t).parent().parent().parent().find('.active').removeClass('active');
				$(t).parent().parent().parent().find('input').prop('checked', false);
			}
			if ($(t).hasClass('select-all')) {
				if ($(t).hasClass('active')) {
					$(t).parent().parent().find('.item').removeClass('active');
					$(t).parent().parent().find('input').prop('checked', false);
					removeModelId(otherId, true);
				} else {
					$(t).parent().parent().find('.item').addClass('active');
					$(t).parent().parent().find('input').prop('checked', true);
					removeModelId(otherId);
				}
			}
			if (!action && $(t).hasClass('active') && !$(t).hasClass('select-all')) {
				$(t).parent().parent().parent().find('.select-all input').prop('checked', false);
				$(t).parent().parent().parent().find('.select-all').removeClass('active');
				$(t).removeClass('active');
				$(t).find('input').prop('checked', false);
				removeModelId(otherId);
			} else if (!$(t).hasClass('select-all')) {
				$(t).addClass('active');
				$(t).find('input').prop('checked', true);
				var allCheck = true;
				$(t).parents('span').find('.item').each(function(index, el) {
					if (!$(el).hasClass('active')) {
						allCheck = false;
					}
				});
				if (allCheck) {
					$(t).parent().parent().parent().find('.select-all input').prop('checked', true);
					$(t).parent().parent().parent().find('.select-all').addClass('active');
				}
			}

			if (!$('#' + action + classID + otherId).text()) {
				$('#products').html('');
				$.ajax({
					url: 'imp/product_catalog/man_catalog.php',
					beforeSend: function () {
						$(t).append('<img src="./imp/images/loading.gif" style="width: 20px;">');
					},
					type: 'POST',
					dataType: 'json',
					data: {action: action, class_id: classID, other_id: otherId, perform: 'loadNav'}
				}).always(function(json) {
					var nav = '';
					if (json['nav']) {

						if (action == 'loadSubModels') {
							nav += '<li>';
							nav += '<a href="javascript:void(0);" onclick="loadNav(this, \'\', \'\', \'\'); return false;" class="item select-all" ><input type="checkbox" name="loadSubModelsCheckbox" class="css-checkbox"/><label class="cs-label">Select All</label>';
							nav += '</a>';
							nav += '</li>';
							nav += '<span>';
						}

						var loop = json['nav'].length;

						for (var i = 0; i < loop; i++) {
							var navName = json['nav'][i].name;
							if (action == 'loadSubModels') {
								navName = '<input type="checkbox" class="css-checkbox" name="loadSubModelsCheckbox"/><label class="cs-label">'+ json['nav'][i].name +'</label>';
							}
							nav += '<li><a href="javascript:void(0);" onclick="loadNav(this, \''+ json['next'] +'\', \''+ classID +'\', \''+ json['nav'][i].id +'\'); return false;" data-submodelid="' + json['nav'][i].id + '" class="item nav-'+ json['nav'][i].id +'">'+ navName +'</a></li>';

						}
						nav += '</span>';

					}
					$(t).find('img').remove();									
					$('#' + action).html(nav);

					$('#class').html('');
					if (action != 'attrib_id') {
						autoLoad(action);
					}
				});
			} else {
				$('#' + action + classID + otherId).collapse('show');
			}
			if (!action) {
				action = 'attrib_id';
			}

			cAction = action;
			cOtherId = otherId;

			loadProducts();
		}

		function removeModelId(otherId, em) {
			var str = $('#model_id').val();
			otherId = otherId + ',';
			var re = new RegExp(otherId, 'g');
			var res = str.replace(re, "");
			if (otherId == ',') {
				res = '';
				if (!em) {
					$('#loadSubModels').find('.item').each(function() {
						if ($(this).attr('data-submodelid')) {
							res = res + $(this).attr('data-submodelid') + ',';
						}
					});
				}
			}
			$('#model_id').val(res);
		}

		function setFilter (action, otherId) {
			var disableDiv = '<div class="disableDiv"><span class="editLayer" onclick="$(this).parent().remove();"><span aria-hidden="true"><i class="pencil icon"></i></span></span><span class="sign"><span aria-hidden="true"><i class="angle right icon"></i></span></span></div>';
			if (action == 'loadModels') {
				$('#loadManufacturers').append(disableDiv);
				$('#loadModels').html('');
				$('#loadSubModels').html('');
				$('#manufacturer_id').val(otherId);
				$('#device_id').val('');
				$('#model_id').val('');
				$('#main_class_id').val('');
			}
			if (action == 'loadSubModels') {
				$('#loadModels').append(disableDiv);
				$('#loadSubModels').html('');
				$('#device_id').val(otherId);
				$('#model_id').val('');
				$('#main_class_id').val('');
			}
			if (action == 'loadMainClass') {
				$('#main_class_id').val('');
			}
			if (action == '') {
				if (otherId) {
					var res = $('#model_id').val() + otherId + ',';
					$('#model_id').val(res);
				}
			}
		}

		function getFilter () {
			setFilter(cAction, cOtherId);
			class_id = [];
			$('.selectClass').each(function() {
				var rBtn = $(this).find('.class_id');
				if (rBtn.is(':checked')) {
					//class_id = rBtn.val();
					class_id.push(rBtn.val());
				}
			});

			var attrib = {};
			$('.selectClass').each(function(index, el) {
				var classEl = $(el).find('.class_id');
				if (classEl.is(':checked')) {
					var cID = $(el).find('.class_id').val();
					attrib['c'+cID] = [];
					$(el).find('.select').each(function(index, atEl) {
						var atrEl = $(atEl).find('input');
						if (atrEl.is(':checked')) {
							attrib['c'+cID].push(atrEl.val());
						}
					});

				}
			});

			var filter = {};
			filter['class_id'] = class_id;
			filter['manufacturer_id'] = $('#manufacturer_id').val();
			filter['device_id'] = $('#device_id').val();
			filter['model_id'] = $('#model_id').val();
			filter['main_class_id'] = $('#main_class_id').val();
			filter['attrib_id'] = attrib;
		// filter['group_id'] = $('#priceGroup').val();
		filter['group_id'] = '1633';
		return filter;
	}

	function loadAttr (t) {
		var attribObj = $(t).parent().parent().parent().find('.attrib');
		var select = attribObj.attr('class');

		if ($(t).is(':checked') && !select) {
			//$('.attrib').remove();
			filter = getFilter();
			$.ajax({
				url: 'imp/product_catalog/man_catalog.php',
				type: 'POST',
				dataType: 'json',
				data: {filter: filter, class: $(t).val(), perform: 'loadAttr'}
			}).always(function(json) {

				var attributes = '';

				if (json['attributes']) {
					attributes = '<ul class="subfilter active-anchor attrib">';
					var main_name = '';

					var loop = json['attributes'].length;
					for (var i = 0; i < loop; i++) {

						if (json['attributes'][i].main_name != main_name) {
							attributes += '<h5>'+ json['attributes'][i].main_name.ucFirst() +'</h5>';
							main_name = json['attributes'][i].main_name;
						}

						attributes += '<li class="select">';
						attributes += '<a href="javascript:void(0);">';
						attributes += '<input type="checkbox" id="'+ $(t).val() + '-' + json['attributes'][i].id +'" data-val-id="'+ $(t).val() + '-' + json['attributes'][i].id +'" value="'+ json['attributes'][i].id +'" onChange="loadProducts(1, 1)" class="css-checkbox">';
						attributes += '<label for="'+ $(t).val() + '-' + json['attributes'][i].id +'" class="cs-label">';
						attributes += ' ' + json['attributes'][i].name.ucFirst();
						attributes += '</label>';
						attributes += '</a>';
						attributes += '</li>';
					}
					attributes += '</ul>';
				}
				var pr = $(t).parent().parent();
				pr.append(attributes);
				pr.find('.subfilter').show();

				autoClass = $(t).val();
				autoLoad('attrib_id');
			});
		} else if (!$(t).is(':checked')) {
			attribObj.find('input[type=checkbox]').prop('checked', false);
		} else {
			// attribObj.show();
		}
		loadProducts(1);
	}

	function randerProductsList (productsx, returnProducts) {
		var loop = productsx.length;

		var products = '';
		for (var i = 0; i < loop; i++) {

			var is_grade_qty_avail = false;
			var grades = productsx[i].grades;
			var intQty = false;
			var gradeChecked = false;
			var gradesHtml = '';
			var price = productsx[i].price;
			var totalQty = parseInt(productsx[i].quantity);
			if (totalQty > 0) {
				intQty = true;
			}

			for (var r = 0; r < grades.length; r++) {
				var checked = '';
				if (!intQty && parseInt(grades[r].quantity) > 0 && !gradeChecked) {
					checked = 'checked="checked"';
					gradeChecked = true;
					price = grades[r].price;
				}
				if (grades[r].quantity > '0') {
					gradesHtml += '<div class="ui equal grid grade">';

					gradesHtml += '<div class="five wide column">';
					gradesHtml +=  grades[r].link;
					gradesHtml += '</div>';
					gradesHtml += '<div class="four wide column">';
					gradesHtml += '<span>$'+ number_format(grades[r].price, 2) +'</span>';
					gradesHtml += '</div>';
					gradesHtml += '<div class="four wide column">';
					gradesHtml += '<span>QTY '+ grades[r].quantity +'</span>';
					gradesHtml += '</div>';
					gradesHtml += '<div class="two wide column">';
					gradesHtml += '<label class="checkbox-inline">';
					gradesHtml += '<input sku="' + grades[r].model + '" availQty="'+ grades[r].quantity +'" grade_price="'+grades[r].price+'" '+ checked +' type="radio" onchange="changePrice($(\'#priceGroup\').val(),$(\'.product-l'+productsx[i].product_id+'\').find(\'.qty\').val() , \'l'+ productsx[i].product_id +'\')" name="pr-l'+ productsx[i].product_id +'" value="' + grades[r].product_id + '">&nbsp;';
					gradesHtml += '</label>';
					gradesHtml += '</div>';

					gradesHtml += '</div>';
				}

				if(grades[r].quantity>0) {
					is_grade_qty_avail = true;
				}
			}

			products += '<div class="product-detail row pr0">';
			products += '<div class="product-detail-inner clearfix product product-l'+ productsx[i].product_id +'" data-pID="'+ productsx[i].product_id +'" data-sku="'+ productsx[i].model +'">';
			products += '<div class="col-md-2 product-detail-img"><div class="image">' + productsx[i].image + '</div></div>';

			products +=  '<div class="col-md-10 product-detail-text">';
			products += '<h3>' + productsx[i].linkName + '</h3>';
			products +=  '<div class="row">';
			products +=  '<div class="col-md-3"></div>';
			products +=  '<div class="col-md-4">';


			products +=  '<div class="text-center">'
			products += '<div class="review-area show-mobile">';
			products += '<ul class="review-stars clearfix">';
			for (var rat = 0; rat < 5; rat++) {
				if (rat < productsx[i].rating) {
					products += '<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>';
				} else {
					products += '<li><a href="#"><i class="fa fa-star"></i></a></li>';
				}
			}
			products += '</ul>';
			products += '<a href="#" class="review-links underline">'+ parseInt(productsx[i].reviews) +' reviews</a>';
			products += '</div>';
			products += '<span class="favorite"><i class="fa fa-heart"></i><a href="#" class="underline">Favorite</a></span>';
			products += '</div>';

			products += '<div class="cart-quality">';
			products += '<table class="table">';
			products += '<thead>';
			products += '<tr>';
			products += '<th>Quantity</th>';
			products += '<th>Our Price</th>';
			products += '</tr>';
			products += '</thead>';
			var priceGroup = productsx[i].priceGroup;
			for (var x = 0; x < priceGroup.length; x++) {

				if (priceGroup[x].quantity < 3) {
					$text = '1-2';
				}
				if (priceGroup[x].quantity == 3 && priceGroup[x].quantity < 10) {
					$text = '3-9';
				}
				if (priceGroup[x].quantity == 10) {
					$text = '10 +';
				}

				products += '<tr class="groupPrice group-'+ priceGroup[x].customer_group_id +'" '+ ((filter['group_id'] != priceGroup[x].customer_group_id)? 'style="display: none;"': '') +'>';
				products += '<td><span>' + $text + '</span></td>';
				products += '<td><span class="p'+ productsx[i].product_id + priceGroup[x].customer_group_id + priceGroup[x].quantity +'">$'+ number_format(priceGroup[x].price, 2) +'</span></td>';
				products += '</tr>';
				if (filter['group_id'] == priceGroup[x].customer_group_id && priceGroup[x].quantity == '1') {
					if (intQty) {
						price = priceGroup[x].price;
					}
				}
			}
			if (priceGroup.length == 0) {
				text = 'Price'
				products += '<tr>';
				products += '<td><span>' + text + '</span></td>';
				products += '<td><span>$'+ number_format(productsx[i].price, 2) +'</span></td>';
				products += '</tr>';
			}
			products += '</table>';
			products += '</div>';


			products +=  '</div>';


			products +=  '<div class="col-md-5 cart-total-wrp">';
			products +=  '<div class="cart-total text-right">';
			
			products += '<div class="ui secondary segment" style="min-height: 250px; display: none;">';
			products += '<div class="ui equal grid">';
			products += '<div class="four wide column">';
			products += '<div class="ui equal grid" style="display: none;">';
			products += '<div class="twelve wide column">';
			products += '<span>In Stock</span>';
			products += '</div>';
			products += '<div class="four wide column">';
			products += '<span>'+ productsx[i].quantity +'</span>';
			products += '</div>';
			products += '<div class="twelve wide column">';
			products += '<span>On Order</span>';
			products += '</div>';
			products += '<div class="four wide column">';
			products += '<span>'+ productsx[i].qtyOutStock +'</span>';
			products += '</div>';
			products += '</div>';
			products += '<h2 title="'+ ((productsx[i].hover)? productsx[i].hover: productsx[i].model) +'">NEW</h2>';
			products += '</div>';
			products += '<div class="twelve wide column">';
			products += '<div class="checkbox">';
			products += '<label>';
			if (totalQty > 0) {
				products += '<input type="radio" sku="' + productsx[i].model + '" availQty="' + totalQty + '" grade_price="0.00" id="pr-l'+productsx[i].product_id+'-main" onchange="changePrice($(\'#priceGroup\').val(),$(\'.product-l'+productsx[i].product_id+'\').find(\'.qty\').val() , \'l'+ productsx[i].product_id +'\')" ' + ((intQty)? 'checked="checked"' : '') + ' name="pr-l'+ productsx[i].product_id +'" value="' + productsx[i].product_id + '">';
			} else {
				products += '<h3></h3>';
			}
			products += '</label>';
			products += '</div>';
			products += '</div>';
			products += '</div>';
			products += '</div>';

			products += '<div class="eight wide column" style="display: none;">';
			products += '<p class="sPrice">$'+ number_format(price ,2)+'</p>';
			products += '</div>';

			if (!intQty && !gradeChecked) {
				products += '<h4>OUT OF STOCK</h4>';
			} else {
				products += '<div class="qtyt-box">';
				products += '<div class="input-group spinner">';
				products += '<span class="txt">QTY</span>';
				products += '<input type="text" onkeyup="allowNum(this)" value="1" '+ ((!intQty && !gradeChecked) ? 'disabled="disabled"': '') +' class="form-control qty" onchange="changePrice($(\'#priceGroup\').val(), $(this).val(), \'l'+ productsx[i].product_id +'\')">';
				products += '<div class="input-group-btn-vertical">';
				products += '<button class="btn" type="button"><i class="fa fa-plus"></i></button>';
				products += '<button class="btn" type="button"><i class="fa fa-plus"></i></button>';
				products += '</div>';						
				products += '</div>';
				products += '</div>';
			}
			if (intQty || gradeChecked) {
				products += '<h3 class="total price">$'+ number_format(price ,2)+'</h3>';
			}
			if(!intQty && !gradeChecked)	{
				products += '<div class="input-group spinner">';
				products += '<input class="form-control notifyemail" placeholder="Enter you Email" value="<?php echo ($isLogged)? $userEmail: ""; ?>" type="text"/>';
				products += '</div>';
			}
			if(!intQty && !gradeChecked)	{
				products += '<button type="button" class="btn btn-warning notifyMeBtn" onclick="notifyMe('+ productsx[i].product_id +');">Notify When Available</button>';
			} else {
				products += '<button type="button" class="btn btn-success addToCart" onclick="addToCart(\'l'+ productsx[i].product_id +'\')"><img src="catalog/view/theme/ppusa2.0/images/icons/basket.png" alt="">Add To Cart</button>';
			}

			products +=  '</div>';
			products +=  '</div>';

			
			products +=  '</div>';
			products +=  '</div>';

			products +=  '<div class="features-row">';


			products += '<div class="review-area">';
			products += '<ul class="review-stars clearfix">';

			for (var rat = 0; rat < 5; rat++) {
				if (rat < productsx[i].rating) {
					products += '<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>';
				} else {
					products += '<li><a href="#"><i class="fa fa-star"></i></a></li>';
				}
			}

			products += '</ul>';
			products += '<p><a href="#" class="review-links underline">'+ parseInt(productsx[i].reviews) +' reviews</a></p>';
			products += '</div>';

			products += '<div class="row item-features">';
			products += '<ul>';
			products += '<li class="col-md-6 col-xs-3">';
			products += 'Phone model';
			products += '</li>';
			products += '<li class="col-md-6 col-xs-3">';
			products += 'Product';
			products += '</li>';
			products += '</ul>';
			products += '<ul>';
			products += '<li class="col-md-6 col-xs-3">';
			products += 'Compatibility';
			products += '</li>';
			products += '<li class="col-md-6 col-xs-3">';
			products += 'Type';
			products += '</li>';
			products += '</ul>';
			products += '<ul>';
			products += '<li class="col-md-6 col-xs-3">';
			products += 'Information';
			products += '</li>';
			products += '<li class="col-md-6 col-xs-3">';
			products += 'Information';
			products += '</li>';
			products += '</ul>';
			products += '</div>';


			products +=  '</div>';

			products += '</div>';
			products += '</div>';
		}
		products = '<div id="list-view" class="product-view">' + products + '</div>';

		products = returnProducts + products;

		return products;
	}

	function randerProducts (productsx, paginationx) {
		var products = '';
		if (productsx) {
			var loop = productsx.length;


			products = '<div class="row listing-row initial-width">';
			for (var i = 0; i < loop; i++) {

				if (!(i%5) && i != 0) {
					products += '</div>';
					products += '<div class="row listing-row initial-width">';
				}

				var is_grade_qty_avail = false;
				var grades = productsx[i].grades;
				var intQty = false;
				var gradeChecked = false;
				var gradesHtml = '';
				var price = productsx[i].price;
				// var totalQty = parseInt(productsx[i].quantity) + productsx[i].qtyOutStock;
				var totalQty = parseInt(productsx[i].quantity);
				if (totalQty > 0) {
					intQty = true;
				}

				for (var r = 0; r < grades.length; r++) {
					var checked = '';
					if (!intQty && parseInt(grades[r].quantity) > 0 && !gradeChecked) {
						checked = 'checked="checked"';
						gradeChecked = true;
						price = grades[r].price;
					}
					if (grades[r].quantity > '0') {
						gradesHtml += '<div class="ui equal grid grade">';

						gradesHtml += '<div class="five wide column">';
						gradesHtml +=  grades[r].link;
						gradesHtml += '</div>';
						gradesHtml += '<div class="four wide column">';
						gradesHtml += '<span>$'+ number_format(grades[r].price, 2) +'</span>';
						gradesHtml += '</div>';
						gradesHtml += '<div class="four wide column">';
						gradesHtml += '<span>QTY '+ grades[r].quantity +'</span>';
						gradesHtml += '</div>';
						gradesHtml += '<div class="two wide column">';
						gradesHtml += '<label class="checkbox-inline">';
						gradesHtml += '<input sku="' + grades[r].model + '" availQty="'+ grades[r].quantity +'" grade_price="'+grades[r].price+'" '+ checked +' type="radio" onchange="changePrice($(\'#priceGroup\').val(),$(\'.product-'+productsx[i].product_id+'\').find(\'.qty\').val() , \''+ productsx[i].product_id +'\')" name="pr-'+ productsx[i].product_id +'" value="' + grades[r].product_id + '">&nbsp;';
						gradesHtml += '</label>';
						gradesHtml += '</div>';

						gradesHtml += '</div>';
					}

					if(grades[r].quantity>0) {
						is_grade_qty_avail = true;
					}
				}

				products += '<div class="col-md-2 col-sm-4 col-xs-6 listing-items product product-'+ productsx[i].product_id +'" data-pID="'+ productsx[i].product_id +'" data-sku="'+ productsx[i].model +'">';
				products += '<article class="related-product">';
				products += '<div class="image">' + productsx[i].image + '</div>';
				products += '<h4>' + productsx[i].linkName + '</h4>';

				products += '<p class="product-attribute">Brief List Of Product Attributes</p>';

				products += '<div class="ui secondary segment" style="min-height: 250px; display: none;">';

				products += '<div class="ui equal grid">';


				products += '<div class="four wide column">';

				products += '<div class="ui equal grid" style="display: none;">';

				products += '<div class="twelve wide column">';
				products += '<span>In Stock</span>';
				products += '</div>';

				products += '<div class="four wide column">';
				products += '<span>'+ productsx[i].quantity +'</span>';
				products += '</div>';

				products += '<div class="twelve wide column">';
				products += '<span>On Order</span>';
				products += '</div>';

				products += '<div class="four wide column">';
				products += '<span>'+ productsx[i].qtyOutStock +'</span>';
				products += '</div>';

				products += '</div>';

				products += '<h2 title="'+ ((productsx[i].hover)? productsx[i].hover: productsx[i].model) +'">NEW</h2>';

				products += '</div>';

				products += '<div class="twelve wide column">';

				products += '<div class="checkbox">';
				products += '<label>';

				if (totalQty > 0) {
					products += '<input type="radio" sku="' + productsx[i].model + '" availQty="' + totalQty + '" grade_price="0.00" id="pr-'+productsx[i].product_id+'-main" onchange="changePrice($(\'#priceGroup\').val(),$(\'.product-'+productsx[i].product_id+'\').find(\'.qty\').val() , \''+ productsx[i].product_id +'\')" ' + ((intQty)? 'checked="checked"' : '') + ' name="pr-'+ productsx[i].product_id +'" value="' + productsx[i].product_id + '">';
				} else {
					products += '<h3></h3>';
				}

				products += '</label>';
				products += '</div>';

				products += '</div>';

				products += '</div>';

				products += '<div class="ui equal grid">';
				products += '<div class="row price">';
				products += '<div class="fourteen wide column centered table-responsive">';
				products += '<table class="ui very basic celled table">';


				var priceGroup = productsx[i].priceGroup;
				for (var x = 0; x < priceGroup.length; x++) {

					if (priceGroup[x].quantity < 3) {
						$text = '1-2';
					}
					if (priceGroup[x].quantity == 3 && priceGroup[x].quantity < 10) {
						$text = '3-9';
					}
					if (priceGroup[x].quantity == 10) {
						$text = '10 +';
					}

					products += '<tr class="groupPrice group-'+ priceGroup[x].customer_group_id +'" '+ ((filter['group_id'] != priceGroup[x].customer_group_id)? 'style="display: none;"': '') +'>';
					products += '<td><span>' + $text + '</span></td>';
					products += '<td><span class="p'+ productsx[i].product_id + priceGroup[x].customer_group_id + priceGroup[x].quantity +'">$'+ number_format(priceGroup[x].price, 2) +'</span></td>';
					products += '</tr>';

					if (filter['group_id'] == priceGroup[x].customer_group_id && priceGroup[x].quantity == '1') {
						if (intQty) {
							price = priceGroup[x].price;
						}
					}

				}

				if (priceGroup.length == 0) {
					text = 'Price'
					products += '<tr>';
					products += '<td><span>' + text + '</span></td>';
					products += '<td><span>$'+ number_format(productsx[i].price, 2) +'</span></td>';
					products += '</tr>';
				}

				products += '</table>';
				products += '</div>';
				products += '</div>';
				products += '</div>';

				products += gradesHtml;

				products += '</div>';

				products += '<div class="ui equal grid">';

				products += '<div class="eight wide column" style="display: none;">';
				products += '<p class="sPrice">$'+ number_format(price ,2)+'</p>';
				products += '</div>';


				if (!intQty && !gradeChecked) {
					products += '<h4>OUT OF STOCK</h4>';
				} else {
					products += '<div class="qtyt-box">';
					products += '<div class="input-group spinner">';
					products += '<span class="txt">QTY</span>';
					products += '<input type="text" onkeyup="allowNum(this)" value="1" '+ ((!intQty && !gradeChecked) ? 'disabled="disabled"': '') +' class="form-control qty" onchange="changePrice($(\'#priceGroup\').val(), $(this).val(), \''+ productsx[i].product_id +'\')">';
					products += '<div class="input-group-btn-vertical">';
					products += '<button class="btn" type="button"><i class="fa fa-plus"></i></button>';
					products += '<button class="btn" type="button"><i class="fa fa-plus"></i></button>';
					products += '</div>';						
					products += '</div>';
					products += '</div>';
				}

				products += '<div class="review-area">';
				products += '<ul class="review-stars clearfix">';

				for (var rat = 0; rat < 5; rat++) {
					if (rat < productsx[i].rating) {
						products += '<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>';
					} else {
						products += '<li><a href="#"><i class="fa fa-star"></i></a></li>';
					}
				}

				products += '</ul>';
				products += '<a href="#" class="review-links underline">'+ parseInt(productsx[i].reviews) +' reviews</a>';
				products += '</div>';

				products += '<div class="error" style="text-align:center">';
				products += '</div>';
				if (intQty || gradeChecked) {
					products += '<p class="total price">$'+ number_format(price ,2)+'</p>';
				}
				if(!intQty && !gradeChecked)	{
					products += '<div class="input-group spinner">';
					products += '<input class="form-control notifyemail" placeholder="Enter you Email" value="<?php echo ($isLogged)? $userEmail: ""; ?>" type="text"/>';
					products += '</div>';
				}

				if(!intQty && !gradeChecked)	{
					products += '<button type="button" class="btn btn-warning btn-sm notifyMeBtn" onclick="notifyMe('+ productsx[i].product_id +');">Notify When Available</button>';

				} else {
					products += '<button type="button" class="btn btn-info" onclick="addToCart(\''+ productsx[i].product_id +'\')">ADD TO CART</button>';
				}

				products += '</article>';
				products += '</div>';
			}
			products += '</div>';
			products = '<div id="grid-view" class="product-view" style="display: block;">' + products + '</div>';

			products = randerProductsList(productsx, products);

			var pagination = '';
			pagination += '<div class="row">';
			pagination += '<nav>';
			pagination += '<ul class="ui pagination menu">';

			pagination += '<li class="item '+ ((paginationx['page'] == 1)? 'disabled': '') +'">';
			pagination += '<a href="javascript:void(0);" '+ ((paginationx['page'] == 1)? '': 'onclick="loadProducts(1, '+ (paginationx['page'] - 1) +')"') +' aria-label="Previous">';
			pagination += '<span aria-hidden="true">&laquo;</span>';
			pagination += '</a>';
			pagination += '</li>';

			if (paginationx['noPages'] > 10) {
				if (paginationx['page'] > 4 && paginationx['page'] < (paginationx['noPages'] - 5)) {
					for (i=(paginationx['page'] - 2); i < (paginationx['page'] + 4) ; i++) {
						pagination += '<li class="item '+ ((paginationx['page'] == i)? 'active': '') +'"><a href="javascript:void(0);" '+ ((paginationx['page'] == i)? '': 'onclick="loadProducts(1, '+ (i) +')"') +'>'+ i +'</a></li>';
					}
				} else {
					for (i=1; i < 6 ; i++) {
						pagination += '<li class="item '+ ((paginationx['page'] == i)? 'active': '') +'"><a href="javascript:void(0);" '+ ((paginationx['page'] == i)? '': 'onclick="loadProducts(1, '+ (i) +')"') +'>'+ i +'</a></li>';
					}
				}
				pagination += '<li class="item"><a href="javascript:void(0);">+++</a></li>';
				for (i=(paginationx['noPages'] - 5); i <= paginationx['noPages'] ; i++) { 
					pagination += '<li class="item '+ ((paginationx['page'] == i)? 'active': '') +'"><a href="javascript:void(0);" '+ ((paginationx['page'] == i)? '': 'onclick="loadProducts(1, '+ (i) +')"') +'>'+ i +'</a></li>';
				}
			} else {
				for (i=1; i <= paginationx['noPages'] ; i++) {
					pagination += '<li class="item '+ ((paginationx['page'] == i)? 'active': '') +'"><a href="javascript:void(0);" '+ ((paginationx['page'] == i)? '': 'onclick="loadProducts(1, '+ (i) +')"') +'>'+ i +'</a></li>';
				}
			}
			pagination += '<li class="item '+ ((paginationx['page'] == paginationx['noPages'])? 'disabled': '') +'">';
			pagination += '<a href="javascript:void(0);" '+ ((paginationx['page'] == paginationx['noPages'])? '': 'onclick="loadProducts(1, '+ (paginationx['page'] + 1) +')"') +' aria-label="Next">';
			pagination += '<span aria-hidden="true">&raquo;</span>';
			pagination += '</a>';
			pagination += '</li>';

			pagination += '</ul>';
			pagination += '</nav>';
			pagination += '</div>';

			products += pagination;

		}

		return products;
	}

	function qtyChange(opr, product_id) {
		var qtyBox = $('.product-' + product_id).find('.qty');
		var qty = parseInt(qtyBox.val());
		if (opr == '+') {
			qtyBox.val(qty + 1);
		} else if (qty > 1) {
			qtyBox.val(qty - 1);
		}
		qtyBox.change();
	}

	function xpandClassex (t) {
		var classex = $(t).parent().parent().parent().find('.classex');
		if (classex.hasClass('hidex')) {
			classex.removeClass('hidex');
			$(t).text('-');
		} else {
			classex.addClass('hidex');
			$(t).text('+');
		}
	}

	function loadProducts(skip, page) {
		if (!cAction || cAction == 'loadModels' || cAction == 'loadSubModels' || !$('#model_id').val()) {
			return false;
		}

		if (!page) {
			page = 1;
		}

		filter = getFilter();

		$.ajax({
			url: 'imp/product_catalog/man_catalog.php?page='+page,
			beforeSend: function () {
				$('#products').html('<img src="./imp/images/loading.gif" style="max-width: 200px; max-height: 200px; margin: 0px auto;">');
				if (skip != 1) {
					$('#class').html('<img src="./imp/images/loading.gif" style="max-width: 20px; max-height: 20px; margin: 0px auto;">');
				}
			},
			type: 'POST',
			dataType: 'json',
			data: {filter: filter, perform: 'loadProducts', main: 'yes'}
		}).always(function(json) {
			$('#products').find('img').remove();
			cfbProducts = json['products'];
			$('#products').html(randerProducts(json['products'], json['pagination']));
			if (skip != 1) {
				var classes = '';
				if (json['classes']) {

					var main_name = '';
					var loop = json['classes'].length;
					for (var i = 0; i < loop; i++) {
						if (json['classes'][i].main_name != main_name) {
							classes += '</ul>';
							classes += '</li>';
							if (i != loop) {
								classes += '<li>';
							}

							var findSpace = ' ';
							var re = new RegExp(findSpace, 'g');

							classes += '<a href="javascript:void(0);"><input type="checkbox" class="css-checkbox" id="'+ json['classes'][i].main_name.replace(re, '_') +'_Main">';
							classes += '<label for="'+ json['classes'][i].main_name.replace(re, '_') +'_Main" class="cs-label">'+ json['classes'][i].main_name +'</label>';
							classes += '</a>';
							classes += '<ul class="subfilter filter-check active-anchor">';
							main_name = json['classes'][i].main_name;
						}
						classes += '<li class="selectClass">';
						classes += '<a href="javascript:void(0);">';
						classes += '<input id="'+ json['classes'][i].main_name.replace(re, '_') +''+ json['classes'][i].id +'" class="css-checkbox class_id" value="'+ json['classes'][i].id +'" type="checkbox" '+ (( $.inArray( json['classes'][i].id, filter['class_id'] ) != '-1' )? 'checked="checked"': '') +' class="css-checkbox" onChange="loadAttr(this)">';
						classes += '<label for="'+ json['classes'][i].main_name.replace(re, '_') +''+ json['classes'][i].id +'" class="cs-label">' + json['classes'][i].name + '</label>';
						classes += '</a>';
						classes += '</li>';

					}

				}
				$('#class').html(classes);
				autoLoad('class_id')
			}
		});
	}

	function allowNum (t) {
		var re = /^-?[0-9]+$/;
		var input = $(t).val();
		var valid = input.substring(0, input.length - 1);
		if (!re.test(input)) {
			$(t).val(valid);
		}
	}

	function changePriceFirstTime() {
		var group_id = 1633;
		if (!el.find('.group-' + group_id).length) {
			group_id = 8;
		}
		$('.product').each(function(index, el) {

			el.find('.groupPrice').hide();
			el.find('.group-' + group_id).show();
		});
	}

	function changePrice (group_id, qty, product_id) {

		if (!group_id) {
			return false;
		}

		if (!$('.product-'+product_id).find('.group-' + group_id).length) {
			group_id = 8;
		}

		$('.product-'+product_id).find('.groupPrice').hide();
		$('.product-'+product_id).find('.group-' + group_id).show();
		$('.product-' + product_id).find('.error').text('');
		if($('.product-' + product_id).find('.qty').val()<=0)
		{
			$('.product-' + product_id).find('.qty').val(1);
		}
		var nQty = 0;
		if (qty >= 10) nQty = 10;
		if (qty < 10 || qty == 3) nQty = 3;
		if (qty < 3) nQty = 1;
		if (qty < 1) qty = 1;
		if (product_id) {
			var price = $('.p' + product_id + group_id + nQty ).text();
			if (price) {
				$('.product-' + product_id).find('.sPrice').text(price);
				price = parseFloat(price.substr(1));
			} else {
				price = $('.product-' + product_id).find('.sPrice').text();
				price = parseFloat(price.substr(1));
			}

			if($('#pr-'+product_id+'-main').is(":checked")==false)
			{
				price = 0.00;
			}
			price = price * qty;
			var grade_price = 0.00;
			var more = false;
			var sku = '';
			$('input[name="pr-' + product_id + '"]').each(function() {
				if ($(this).is(':checked')) {
					grade_price = parseFloat(grade_price) + parseFloat($(this).attr('grade_price'));
					if (qty > parseInt($(this).attr('availQty'))) {

						sku = $(this).attr('sku');

						more = $(this).attr('availQty');
					}
				}
			});
			if (more != false) {
				$('.product-' + product_id).find('.qty').val(more);
				changePrice(group_id, more, product_id);
				$('.product-' + product_id).find('.error').text('We\'re sorry Only '+ more +' pcs available');
				return false;
			}
			grade_price = parseFloat(grade_price) * qty;
			price = parseFloat(price) + parseFloat(grade_price);

			$('.product-' + product_id).find('.total').text('$'+price.toFixed(2));
		} else {
			$('.product').each(function() {
				changePrice(group_id, qty, $(this).attr('data-pID'));
			});
		}
	}

	function addToCart (product_id) {
		var product = $('.product-' + product_id);
		sku = product.attr('data-sku');
		qty = product.find('.qty').val();

		$('input[name="pr-' + product_id + '"]').each(function() {
			if ($(this).is(':checked')) {					
				addToCartpp2($(this).val(), qty);
			}
		});
	}		

	function updateCart () {
		$('#cart input[type="number"]').each(function() {
			sendCartAjax ($(this).attr('data-id'), $(this).val(), 1);
		});
	}

	function sendCartAjax (product_id, qty, update) {
		$.ajax({
			url: '<?php echo $host_path;?>imp/product_catalog/ajax_product_add.php',
			type: 'post',

			data:{product_id:product_id,qty:qty, update: update},
			dataType: 'json',       
			beforeSend: function() {

			},
			complete: function() {

			},              
			success: function(json) {

				if(json['error'])
				{
					alert(json['error']);
					return false;
				}
				if(json['success'])
				{
					$('.cartHolder').html(json['data']);
				}



			}
		});
	}

	function removeFromCart (product_id) {
		$.ajax({
			url: '<?php echo $host_path;?>imp/product_catalog/ajax_product_add.php',
			type: 'post',

			data:{product_id:product_id, action: 'remove'},
			dataType: 'json',
			success: function(json) {

				if(json['success'])
				{
					$('.cartHolder').html(json['data']);
				}



			}
		});
	}

	function mapEmail(email) {

		if(jQuery.trim(email)=='')
		{
			return false;
		}
		$.ajax({
			url: '<?php echo $host_path;?>product_catalog/get_customer_group.php',
			type: 'post',

			data:{email:email},
			dataType: 'json',       
			beforeSend: function() {

			},
			complete: function() {

			},              
			success: function(json) {

				if(json['error'])
				{
					alert(json['error']);
					return false;
				}
				if(json['success'])
				{
					$('#priceGroup').val(json['success']);
					changePrice($('#priceGroup').val(), '1', '')
				}



			}
		}); 
	}
	function checkout() {
		if(!confirm('Are you sure want to proceed?'))
		{
			return false;
		}
		window.location='<?=$host_path;?>product_catalog/checkout.php?email='+encodeURIComponent($('#customerEmail').val())+'&customer_group_id='+$('#priceGroup').val();
	}

</script>
<?php echo $footer; ?>