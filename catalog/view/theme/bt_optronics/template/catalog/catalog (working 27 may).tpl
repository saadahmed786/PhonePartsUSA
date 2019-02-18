<?php echo $header; ?>

<!-- WholeSale Form -->
<link rel="stylesheet" type="text/css" class="ui" href="catalog/view/theme/bt_optronics/stylesheet/global_style.css">
<link rel="stylesheet" type="text/css" class="ui" href="catalog/view/theme/bt_optronics/stylesheet/sm/components/icon.min.css">
<link rel="stylesheet" type="text/css" class="ui" href="catalog/view/theme/bt_optronics/stylesheet/jquery.fancybox.css">
<!--<link rel="stylesheet" type="text/css" href="css/docs.css">-->
<link rel="stylesheet/less" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/form.less" />
<link rel="stylesheet/less" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/popup.less" />
<link rel="stylesheet/ess" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/accordian.less" />
<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/products.css" media="screen" />
<style type="text/css" media="screen">
	el {color: red; }
	.errorInput {border-color: red !important; }
	.success {margin: 0; display: inline-block; height: 27px;}
	.grid {width: 100%; padding: 0px; }
	.column {padding: 0px 5px 0px 5px !important; }
	.ui.vertical.menu {width: 100%; min-height: 0px; margin-bottom: 0; margin-top: 0;}
	.ui.grid {margin: 0;}
	.ui.grid > .row, .row {margin-top: 5px; margin-bottom: 5px; padding-top: 5px; padding-bottom: 5px;}
	.ui.grid + .grid {margin-top: 0px;}
	.ui.pagination.menu {padding-top: 0px; }
	.ui.secondary.segment {border-radius: 0px;}

	.row.qtycont {min-height: 35px; }
	.ui.very.basic.celled.table {border-bottom: 1px solid rgba(34, 36, 38, .1);}
	.ui.table tr:first-child td {border-top: 1px solid rgba(34, 36, 38, .1);}
	.product .close {padding: 0px !important; top: 0; right: 0; z-index: 10; }
	.notify {min-height: 28px;}
	.ui.secondary.vertical.menu > .item {padding: 2px 2px 5px 18px; border-bottom: 1px solid #ccc; margin: 0px; line-height: 1; border-radius: 0px !important; }
	.containProduct {border: 1px solid #ccc; box-shadow: 0px 0px 1px 1px #ccc; padding: 10px; }
	.product .name { min-height: 44px;}
	#products nav {margin: 20px auto 0px auto; }
	/*#products {max-height: 800px; overflow-x: hidden; }*/
	.product input[type=text], .feedback input[type=text] {width: 100%;}
	.less-icon {bottom: 0px; left: 0px;}
	.pluse-icon {top: 6px; left: 0px;}
	.pluse-icon a {height: 16px; width: 16px;}
	.pluse-icon img {height: 16px; width: 16px;}
	/*.less-icon a, .pluse-icon a {height: 11px; display: block;}*/
	.navContain {position: relative; height: 240px; box-shadow: 0px 0px 5px 0px #ccc; padding: 5px;}
	.ui.grid > .row.marginNull, .row.marginNull {margin: 0;}
	#MainMenu {max-height: 360px; overflow:hidden; }
	#MainMenu h2 {font-size: 20px;}
	#MainMenu h2 small {font-size: 60%;}
	.navigationx {max-height: 200px; min-height: 200px; overflow-x:hidden; border: 1px solid #ccc;}
	.ui.grid.navigationx {margin-top: 10px;}
	.ui.grid.navigationx .row {margin: 0px;}
	#loadSubModels {display: block;}
	.disableDiv {position: absolute; top: 0; left: 0; width: 100%; height: 100%; text-align: center; background: rgba(0, 0, 0, 0.5);}
	.disableDiv .editLayer {padding: 10px; line-height: 30px; width: 50px; height: 50px; position: relative; top: 50%; transform: translate(0, -50%); color: #000; font-size: 20px; border-radius: 100%; cursor: pointer; background-color: rgba(255, 255, 255, 1); display: inline-block;}
	.disableDiv .editLayer:hover {color: #286090;}
	.disableDiv .sign {color: #286090; position: absolute; right: 20px; top: 50%; line-height: 12px; font-size: 12px; padding: 5px; width: 25px; height: 25px; transform: translate(0px, -50%); background-color: rgba(255, 255, 255, 1); border-radius: 100%;}
	.feedback {position: fixed; z-index: 1010; width: 60%; top: 50%; left: 50%; transform: translate(-50%, -50%); -ms-transform: translate(-50%, -50%); -webkit-transform: translate(-50%, -50%); box-shadow: 0 0 10px 2px; border-radius: 20px;}
	.feedback .ui.secondary {border-radius: 20px;}
	.ui.secondary.segment.product {padding-top: 20px; border-radius: 0px 20px 0px 0px;}
	.segment.product select, .segment.product textarea {width: 100%;}
	.fProducts {overflow-x: hidden; max-height: 350px;}
	.close {border-radius: 50%; background: #f00; line-height: 22px; width: 24px; color: #000; position: absolute; top: -10px; right: -10px; text-align: center; font-size: 15px; border: 2px solid #000; cursor: pointer;}
	#MainMenu.sticky {position: fixed; top: 0; left: 50%; background: #fff; z-index: 1; transform: translate(-50%, 0); -ms-transform: translate(-50%, 0); -webkit-transform: translate(-50%, 0); padding: 0;}
	#marginSticky.stickyMargin {margin-top: 323px;}
	.bigHead {font-size: 24px;}
	.button-re {width: 20px; display: inline-block; line-height: 10px;}
	.hidex {display: none !important;}
	.navType {height: auto; padding: 0px !important}
	.inst ul {list-style-type: circle;}	
</style>

<!-- <script src="catalog/view/javascript/jquery_min.js"></script> -->
<!-- <script src="catalog/view/javascript/easing_min.js"></script> -->
<script src="catalog/view/javascript/highlight_min.js"></script>
<!-- <script src="catalog/view/javascript/history_min.js"></script> -->
<!-- <script src="catalog/view/javascript/tablesort_min.js"></script> -->
<script src="catalog/view/javascript/semantic_min.js"></script>
<script src="catalog/view/javascript/docs.js"></script>
<!-- <script src="catalog/view/javascript/form_design.js"></script> -->
<script src="catalog/view/javascript/less_min.js"></script>
<!-- <script src="catalog/view/javascript/popup.js"></script> -->
<!-- <script type="text/javascript" src="http://cdn.transifex.com/live.js"></script> -->
<!-- <script type="text/javascript" src="catalog/view/javascript/jquery.fancybox.js"></script> -->
<script type="text/javascript" src="catalog/view/javascript/jquery.SimpleMask.js"></script>
<!-- <script type="text/javascript" src="catalog/view/javascript/scripts.js"></script> -->
</div>
</div>
</div>
</div>
<div class="ui container">
	<div class="ui equal width grid">
		<div class="row">
			<div class="six wide column youtube">
				<?php if ($youtube_link) { ?>
				<iframe class="youtube-player" type="text/html" width="364" height="204.75" src="https://www.youtube.com/embed/<?php echo $youtube_link; ?>" frameborder="0"></iframe>
				<?php } ?>
			</div>
			<div class="ten wide column inst">
				<h2 class="bigHead">Product Catalog</h2>
				<?php echo $instructions; ?>
			</div>
		</div>
		<div class="row" id="MainMenu">
			<div class="ui equal width grid">
				<div class="row">
					<div class="four wide column">
						<h2>Step 1 <br><small>Select Manufacturers</small></h2>
						<div class="navContain">
							<h3>Manufacturers</h3>
							<div class="ui two column grid navigationx" id="loadManufacturers">
								<img src="./imp/images/loading.gif" style="width: 20px; height: 20px;">
							</div>
						</div>
					</div>
					<div class="four wide column">
						<h2>Step 2 <br><small>Select Phone Model</small></h2>
						<div class="navContain">
							<h3>Models</h3>
							<div class="ui two column grid navigationx" id="loadModels">

							</div>
						</div>
					</div>
					<div class="four wide column">
						<h2>Step 3 <br><small>Select 1 or more Sub Models</small></h2>
						<div class="navContain">
							<h3>Sub Models</h3>
							<div class="ui two column grid navigationx" id="loadSubModels">

							</div>
						</div>
					</div>
					<div class="four wide column">
						<h2>Step 4 <br><small>Filter by product type</small></h2>
						<div class="navContain">
							<h3>Product Type</h3>
							<div class="ui two column grid navigationx" id="class">

							</div>
						</div>
					</div>
					<input type="hidden" id="manufacturer_id">
					<input type="hidden" id="device_id">
					<input type="hidden" id="model_id">
					<input type="hidden" id="main_class_id">
					<input type="hidden" id="priceGroup" value="<?php echo ($group_id)? $group_id: '8'; ?>">
				</div>
			</div>
			<div class="row marginNull">
				<div class="sixteen wide column">
					<div class="row marginNull">
						<a onclick="feedbackForm();">See Any Compatibility Issues? Please Let Us Know.</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="cfb">
	</div>
	<div id="marginSticky" class="ui equal width grid">
		<div class="row">
			<div class="sixteen wide column">
				<h2>Products</h2>
				<div id="products" class="ui grid" style="text-align: center;">
					<div class="ui sixteen column">
						<div class="ui segment">
							<h2>Chose a device to view compatibile parts and accessories</h2>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var offset = $('#MainMenu').offset();
	$(document).ready(function($) {
		loadManufacturers();
	});
	$(document).scroll(function() {
		var pos = $(document).scrollTop();
		if (offset.top <= (pos) && !$('#MainMenu').hasClass('sticky')) {
			var margin = pos - (offset.top - $('#MainMenu').height());
			$('#MainMenu').addClass('sticky');
			$('#MainMenu').addClass('ui container');
			$('#MainMenu').removeClass('row');
			$('#marginSticky').css('margin-top', margin);
		} else if (offset.top >= pos && $('#MainMenu').hasClass('sticky')) {
			$('#MainMenu').removeClass('sticky');
			$('#MainMenu').removeClass('ui container');
			$('#MainMenu').addClass('row');
			$('#marginSticky').css('margin-top', 0);
		}
	});

	function loadManufacturers () {
		$.ajax({
			url: 'imp/product_catalog/man_catalog.php',
			type: 'POST',
			dataType: 'json',
			data: {action: 'loadManufacturers'},
		}).always(function(json) {
			var data = '';
			data += '<div class="column">';
			data += '<div class="ui secondary vertical menu">';
			for (var i = 0; i < json.length; i++) {
				if (i == Math.round(json.length / 2)) {
					data += '</div>';
					data += '</div>';
					data += '<div class="column">';
					data += '<div class="ui secondary vertical menu">';
				}
				data += '<a href="javascript:void(0);" class="item nav-'+ json[i].id +'" onclick="loadNav(this, \'loadModels\', \'\', \''+ json[i].id +'\');">'+ json[i].name +'</a>';
			}
			$('#loadManufacturers').html(data);
		});

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
			$(t).parent().find('.item').each(function(index, el) {
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
						nav += '<div class="ui secondary vertical menu">';
						nav += '<a href="javascript:void(0);" onclick="loadNav(this, \'\', \'\', \'\'); return false;" class="item select-all" ><input type="checkbox" onClick="event.stopPropagation();$(this).parent().click();" name="loadSubModelsCheckbox"/><strong>Select All</strong></a>';
						nav += '</div>';
					}

					if (action != 'loadSubModels') {
						nav = '<div class="column">';
					}
					nav += '<div class="ui secondary vertical menu">';

					var loop = json['nav'].length;

					for (var i = 0; i < loop; i++) {
						if (i == Math.round(loop / 2) && action != 'loadSubModels') {
							nav += '</div>';
							nav += '</div>';
							nav += '<div class="column">';
							nav += '<div class="ui secondary vertical menu">';
						}
						var navName = json['nav'][i].name;
						if (action == 'loadSubModels') {
							navName = '<input type="checkbox" onClick="event.stopPropagation();$(this).parent().click();" name="loadSubModelsCheckbox"/>'+ json['nav'][i].name +'';
						}
						nav += '<a href="javascript:void(0);" onclick="loadNav(this, \''+ json['next'] +'\', \''+ classID +'\', \''+ json['nav'][i].id +'\'); return false;" data-submodelid="' + json['nav'][i].id + '" class="item nav-'+ json['nav'][i].id +'">'+ navName +'</a>';

					}

					nav += '</div>';
					if (action != 'loadSubModels') {
						nav += '</div>';
					}

				}
				$(t).find('img').remove();									
				$('#' + action).html(nav);

				$('#class').html('');
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
		filter['group_id'] = $('#priceGroup').val();
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
						attributes = '<div class="ui equal width grid attrib">';
						attributes += '<div class="row">';
						var main_name = '';

						var loop = json['attributes'].length;
						for (var i = 0; i < loop; i++) {

							if (json['attributes'][i].main_name != main_name) {
								attributes += '<div class="fifteen wide column right floated">';
								attributes += '<h4>'+ json['attributes'][i].main_name.ucFirst() +'</h4>';
								attributes += '</div>';
								main_name = json['attributes'][i].main_name;
							}

							attributes += '<div class="fourteen wide column right floated select">';
							attributes += '<div class="checkbox">';
							attributes += '<label>';
							attributes += '<input type="checkbox" value="'+ json['attributes'][i].id +'" onChange="loadProducts(1, 1)"> ' + json['attributes'][i].name.ucFirst();
							attributes += '</label>';
							attributes += '</div>';
							attributes += '</div>';
						}
						attributes += '</div>';
						attributes += '</div>';
					}
					
					$(t).parent().parent().parent().append(attributes);
				});
			} else if (!$(t).is(':checked')) {
				attribObj.find('input[type=checkbox]').prop('checked', false);
				attribObj.hide();
			} else {
				attribObj.show();
			}
			loadProducts(1);
		}

		function randerProducts (productsx, paginationx) {
			var products = '';
			if (productsx) {
				var loop = productsx.length;


				products = '<div class="four column row">';
				for (var i = 0; i < loop; i++) {

					if (!(i%4) && i != 0) {
						products += '</div>';
						products += '<div class="four column row">';
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


					products += '<div class="column product product-'+ productsx[i].product_id +'" data-pID="'+ productsx[i].product_id +'" data-sku="'+ productsx[i].model +'">';
					products += '<div class="containProduct">';
					products += productsx[i].image;
					products += '<h4>' + productsx[i].link + '</h4>';
					products += '<h4 class="name">'+productsx[i].name+'</h4>';

					products += '<div class="ui secondary segment" style="min-height: 250px;">';

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
								//productsx[i].price = priceGroup[x].price;
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

					products += '<div class="row qtycont">';
					products += '<div class="two wide column centered">';
					products += '</div>';

					if (!intQty && !gradeChecked) {
						products += '<div class="twelve wide column centered">';
						products += '<h4>OUT OF STOCK</h4>';
						products += '</div>';
					} else {
						products += '<div class="ten wide column centered">';
						products += '<input style="text-align:center" onkeyup="allowNum(this)" value="1" '+ ((!intQty && !gradeChecked) ? 'disabled="disabled"': '') +' type="text" class="form-control qty" onchange="changePrice($(\'#priceGroup\').val(), $(this).val(), \''+ productsx[i].product_id +'\')">';
						products += '</div>';
						products += '<div class="two wide column centered">';
						products += '<span class="pluse-icon"><a href="javascript:void(0);" onclick="qtyChange(\'+\', '+ productsx[i].product_id +')"><img src="catalog/view/theme/bt_optronics/image/pluse-icon.png" alt="pluse-icon"></a></span>';
						products += '</div>';
					}
					// products += '<span class="less-icon"><a href="javascript:void(0);" onclick="qtyChange(\'-\', '+ productsx[i].product_id +')"><img src="catalog/view/theme/bt_optronics/image/less-icon.png" alt="less-icon"></a></span>';
					products += '<div class="two wide column centered">';
					products += '</div>';
					products += '</div>';

					products += '<div class="sixteen wide column error" style="text-align:center">';					
					products += '</div>';

					products += '<div class="eight wide column" style="text-align:right">';
					products += '<p>Total:</p>';
					products += '</div>';
					products += '<div class="eight wide column" style="text-align:left">';
					products += '<p class="total">$'+ number_format(price ,2)+'</p>';
					products += '</div>';

					products += '<div class="sixteen wide column centered row notify" <?php echo ($isLogged)? "style=\"display: none;\"": ""; ?>>';
					if(!intQty && !gradeChecked)	{
						products += '<input class="notifyemail" placeholder="Enter you Email" value="<?php echo ($isLogged)? $userEmail: ""; ?>" type="text"/>';
					}
					products += '</div>';

					products += '<div class="sixteen wide column">';
					if(!intQty && !gradeChecked)	{
						products += '<button type="button" class="ui blue button notifyMeBtn" onclick="notifyMe('+ productsx[i].product_id +');">Notify When Available</button>';

					} else {
						products += '<button type="button" class="btn" onclick="addToCart(\''+ productsx[i].product_id +'\')">ADD TO CART</button>';
					}
					products += '</div>';


					products += '</div>';

					products += '</div>';
					products += '</div>';
				}
				products += '</div>';
				products += '</div>';

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
					var classes = '<div class="navType">';
					classes += '<div class="ui equal width grid">';
					if (json['classes']) {

						var main_name = '';
						var loop = json['classes'].length;
						for (var i = 0; i < loop; i++) {
							if (json['classes'][i].main_name != main_name) {
								if (i != 0) {
									classes += '</div>';
									classes += '</div>';
									classes += '</div>';
								}
								if (i != loop) {
									classes += '<div class="row">';
								}
								
								classes += '<div class="sixteen wide column">';
								classes += '<h4><button class="button-re" onclick="xpandClassex(this);" type="button">+</button> '+ json['classes'][i].main_name +'</h4>';
								classes += '</div>';
								main_name = json['classes'][i].main_name;

								if (i != loop) {
									classes += '<div class="row classex hidex">';
									classes += '<div class="ui equal width grid">';
								}
							}

							classes += '<div class="sixteen wide column right floated selectClass">';
							classes += '<div class="checkbox">';
							classes += '<label>';
							classes += '<input class="class_id" type="checkbox" '+ (( $.inArray( json['classes'][i].id, filter['class_id'] ) != '-1' )? 'checked="checked"': '') +' value="'+ json['classes'][i].id +'" onclick="javascript:void(0);" onChange="loadAttr(this)"> ' + json['classes'][i].name;
							classes += ' <i class="angle down icon"></i>';
							classes += '</label>';
							classes += '</div>';
							classes += '</div>';
						}
						classes += '</div>';
						classes += '</div>';

					}
					$('#class').html(classes);
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

		function changePrice (group_id, qty, product_id) {

			if (!group_id) {
				return false;
			}

			$('.groupPrice').hide();
			$('.group-' + group_id).show();
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
					//sendCartAjax($(this).val(), qty, 0);
					boss_addToCart($(this).val(), qty);
					//console.log($(this).val());
				}
			});;
		}		

		function updateCart () {
			$('#cart input[type="number"]').each(function() {
				sendCartAjax ($(this).attr('data-id'), $(this).val(), 1);
			});
		}

		function sendCartAjax (product_id, qty, update) {
			$.ajax({
				url: '<?php echo $host_path;?>product_catalog/ajax_product_add.php',
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
				url: '<?php echo $host_path;?>product_catalog/ajax_product_add.php',
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