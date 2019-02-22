<?php
include_once '../config.php';
include_once '../inc/functions.php';
include_once 'load_catalog2.php';

//$catalog->loadModelClasses(array('class_id' => '', 'model_id' => '79'));
$_POST = escapeArrayDB($_POST);
//$_SESSION['catalog_competitor_pricing'] = 1;
$manufacturers = $catalog->loadManufacturers();
if ($_POST['action'] == 'loadManufacturers') {
	echo json_encode($manufacturers);
	exit;
}
if ($_POST['action'] == 'getCartQty') {
	$qty = $_SESSION['cart'][$_POST['product_id']]['qty'];
	echo json_encode(array('qty' => ($qty)? $qty: 1));
	exit;
}
if ($_POST['getSetting']) {
	echo json_encode(array('setting' => $catalog->getSetting($_POST['getSetting'])));
	exit;
}
$userGroups = $catalog->userGroup();
$group_id = $catalog->getUserGroup($_GET['email']);
// $group_id=1633;
if ($_POST['perform'] == 'loadNav') {	

	$_POST['next'] = $catalog->nextAction($_POST['action']);
	$json['nav'] = '';
	if ($_POST['action']) {
		$array = $catalog->$_POST['action']($_POST['other_id']);
		$json['nav'] = $array;
		$json['next'] = $_POST['next'];
	}
	echo json_encode($json);
	exit;
}

if ($_POST['perform'] == 'loadProducts') {

	$_POST['next'] = $catalog->nextAction($_POST['action']);

	$filter = $_POST['filter'];

	$filter['page'] = (int)$_GET['page'];
	

	$array = $catalog->filterProducts($filter);

	$json['data'] = '';
	if ($array['products']) {

		$hover_new = $catalog->getSetting('hover_text_new');
		$hover_a = $catalog->getSetting('hover_text_a');
		$hover_b = $catalog->getSetting('hover_text_b');
		$hover_c = $catalog->getSetting('hover_text_c');

		$image_size = explode('x', $catalog->getSetting('image_size'));
		$image_size = ($image_size)? 'width: '. $image_size[0] .'px; height: '. $image_size[0] .'px;': 'width: 200px; height: 200px;';

		foreach ($array['products'] as $i => $row) {
			$array['products'][$i]['scraped_prices'] = $catalog->getScrapper($row['model']);
			$array['products'][$i]['true_cost_row'] = $catalog->getTrueCostRow($row['model']);	
		
			//print_r($check);
			$array['products'][$i]['qtyOutStock'] = $catalog->productQtyOnOrder($row['model']);
			$array['products'][$i]['vendorNeededQty'] = $catalog->productNeededQty($row['model']);
			$array['products'][$i]['priceGroup'] = $catalog->productPrice($row['product_id']);
			$array['products'][$i]['is_sale_item'] = $catalog->isSaleItem($row['product_id']);
			$array['products'][$i]['grades'] = $catalog->productGrade($row['model']);
			
$array['products'][$i]['is_kit'] = (int)$db->func_query_first_cell("SELECT is_kit from oc_product where model='".$row['model']."'");

			foreach ($array['products'][$i]['grades'] as $r => $rowr) {				
				// $array['products'][$i]['grades'][$r]['link'] = linkToProductPPUSA($rowr['model'], $rowr['product_id'], '', ' target="_blank" data-toggle="tooltip" title="'. (($rowr['hover'])? $rowr['hover']: $rowr['model']) .'"', $rowr['item_grade']);
				// $array['products'][$i]['grades'][$r]['link'] = linkToProduct($rowr['model'], $host_path, ' target="_blank" data-toggle="tooltip" title="'. (($rowr['hover'])? $rowr['hover']: $rowr['model']) .'"', $rowr['item_grade']);
				$hover = '';
				if (strtolower($rowr['item_grade']) == 'grade a') {
					$hover = $hover_a;
				} else if (strtolower($rowr['item_grade']) == 'grade b') {
					$hover = $hover_b;
				} else if (strtolower($rowr['item_grade']) == 'grade c') {
					$hover = $hover_c;
				}
				$array['products'][$i]['grades'][$r]['link'] = '<span title="'. $hover .'">'. $rowr['item_grade'] .'</span>';
			}
			foreach ($array['products'][$i]['scraped_prices'] as $r => $rowr) {
				if ($rowr['current_price']) {
					$array['products'][$i]['scraped_prices'][$r]['check'] = 'price_present';
				} else {
					$array['products'][$i]['scraped_prices'][$r]['check'] = 'price_absent';
				}
			}
			// $row['image'] = str_replace('data/', 'cache/data/', $row['image']);
			// $row['image'] = str_replace('impskus/', 'cache/impskus/', $row['image']);
			// $row['image'] = str_replace('.jpg', '-150x150.jpg', $row['image']);
			// $row['image'] = str_replace('.png', '-150x150.png', $row['image']);

			$array['products'][$i]['name'] = changeNameCatalog($row['name']);
			$array['products'][$i]['image'] = noImage($row['image'],$host_path,$path,1,150,150);
			$array['products'][$i]['reviews'] = (int)$row['reviews'];
			$array['products'][$i]['rating'] = (int)$row['rating'];
			if ($_POST['main'] == 'yes') {
				$array['products'][$i]['image'] = linkToProductPPUSA($row['model'], $row['product_id'],  $host_path, ' target="_blank"', '<img data-holder-rendered="true" src="'. $array['products'][$i]['image'] .'" style="'. $image_size .'" data-src="holder+js/200x200" class="img-thumbnail" >');
				$array['products'][$i]['link'] = linkToProductPPUSA($row['model'], $row['product_id'],  $host_path, ' target="_blank"');
				$array['products'][$i]['linkName'] = linkToProductPPUSA(changeNameCatalog($row['name']), $row['product_id'],  $host_path, ' class="blue" target="_blank"');
				$array['products'][$i]['hover'] = $hover_new;
			} else {
				$array['products'][$i]['link'] = linkToProduct($row['model'], $host_path, $extra = ' target="_blank"');
			}
		}

		$json['products'] = $array['products'];		


		$total = $array['total'];
		$page = $_GET['page'];
		$nRows = 50;
		$xPages = $total / $nRows;
		$noPages = explode('.', $xPages)[0];
		$noPages = ((($xPages) > $noPages) ? $noPages + 1: $noPages);

		$json['pagination'] = array(
			'total' => (int)$total,
			'page' => (int)$page,
			'nRows' => 50,
			'noPages' => (int)$noPages,
			);

	}

	

	$array = $catalog->loadModelClasses($filter);
	if ($array) {

		$json['classes'] = $array;

	}

	echo json_encode($json);
	exit;
}

if ($_POST['perform'] == 'loadAttr') {

	$filter = $_POST['filter'];
	$filter['class_id'] = $_POST['class'];

	$array = $catalog->loadClassAttr($filter);
	// $json['attr'] = '';
	if ($array) {
		$json['attributes'] = $array;
	}

	echo json_encode($json);
	exit;

}

//unset($_SESSION['cart']);

?>
<!DOCTYPE html>
<html>
<head>
	<title>Product Catalog</title>
	<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap-theme.min.css">
	<script type="text/javascript" src="<?php echo $host_path; ?>/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $host_path; ?>include/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo $host_path ?>/fancybox/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $host_path ?>/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	<style type="text/css" media="screen">
		.grade {font-size: 12px;}
		.grade input[type=checkbox] {margin-top: 0;}
		#cart {display: block; position: absolute; z-index: 100; background: rgb(255, 255, 255) none repeat scroll 0% 0%; padding: 10px; border: 1px solid rgb(221, 221, 221); border-radius: 10px; width: 100%;}
		#cart .cart-item{font-size: 10px; min-height: 60px;}
		.table-bordered.customColor > tbody > tr > td {border: 1px solid #999; border-left: 0; border-right: 0; padding:0;}
		.list-group-item {padding: 2px 2px 2px 18px; border-width: 0; border-bottom-width: 2px;}
		.well {background: rgb(250, 250, 250) none repeat scroll 0% 0%; min-height: 250px;}
		#MainMenu {max-height: 250px; overflow:hidden;}
		#loadManufacturers {max-height: 210px; min-height: 210px; overflow-x:hidden;}
		#loadModels {max-height: 210px; min-height: 210px;  overflow-x:hidden;}
		#loadSubModels {max-height: 210px; min-height: 210px; overflow-x:hidden;}
		#MainMenu .list-group { border-left: 1px solid #ccc; border-radius: 0;}
		#MainMenu .list-group:first-child {border-left: 0;}
		.col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 { padding-left: 5px; padding-right: 5px;}
		.row {margin-right: -5px; margin-left: -5px;}
		.containProduct {border: 1px solid #ccc; box-shadow: 0px 0px 1px 1px #ccc; border-radius: 10px; padding: 10px; margin-bottom: 10px;}
		.product h4 { min-height: 60px;}
		.disableDiv {position: absolute; top: 0; left: 0; width: 100%; height: 100%; text-align: center; background: rgba(0, 0, 0, 0.5);}
		.disableDiv .editLayer {padding: 10px; line-height: 30px; width: 50px; height: 50px; position: relative; top: 50%; transform: translate(0, -50%); color: #000; font-size: 20px; border-radius: 100%; cursor: pointer; background-color: rgba(255, 255, 255, 1); display: inline-block;}
		.disableDiv .editLayer:hover {color: #286090;}
		.disableDiv .sign {color: #286090; position: absolute; right: 20px; top: 50%; line-height: 12px; font-size: 12px; padding: 5px; width: 25px; height: 25px; transform: translate(0px, -50%); background-color: rgba(255, 255, 255, 1); border-radius: 100%;}
	</style>
</head>
<body> 
	<?php if (isset($_SESSION['login_as'])) { ?>
	<div class="row">
	<?php if (!$_GET['hide_header']) { ?>
		<div class="col-md-12">
		<?php } else { ?>
		<div style="display: none;" class="col-md-12">
		<?php }?>
			<?php include_once '../inc/header.php';?>
			<input type="hidden" name="hide_header" id="hide_header" value="<?php echo $_GET['hide_header']; ?>">
		</div>
		
	</div>
	<?php } ?>
	<div class="container theme-showcase" role="main">
		<div class="row">
			<div class="col-md-8">
				<h1>Product Catalog</h1>
			</div>
			<div class="col-md-4 cartHolder">
				<h3 class="text-right"><span style="cursor: pointer;" onclick="$('#cart').toggle();">Cart(0)</span></h3>
				<div id="cart" style="display: none;">
					<div class="row"><div class="col-md-12"><span>No Items</span></div></div>
				</div>
			</div>
		</div>
		
		<div class="row" style="">
			<div class="col-md-9 col-md-offset-3" id="user">
				<div class="form-group">
					<div align="left" class="col-sm-4" >
						<div <?php echo (isset($_SESSION['catalog_competitor_pricing'])?'':'style="display:none"');?>>	
							<input type="checkbox" id="competitor_checkbox" class="selection checkboxes" value="0" > Competitor Pricing
						</div><br>
						<div>
							<a class="button fancybox3 fancybox.iframe" href="<?php echo $host_path;?>/popupfiles/customer_cart_catalog.php">Fetch Customer Cart</a>
						</div>
					</div>
					<div class="col-sm-4">
						<input type="email" id="customerEmail" class="form-control" value="<?php echo $_GET['email']; ?>" onBlur="mapEmail(this.value)" placeholder="Email">
					</div>
					
					
					<div class="col-sm-4" style="display:none">
						<select onchange="changePrice($(this).val(), '1', '')" id="priceGroup" class="form-control">
							<option value="">Select</option>
							<?php foreach ($userGroups as $key => $group) : ?>
								<option value="<?php echo $group['id'] ?>" <?php echo ($group_id == $group['id'])? 'selected="selected"': ''; ?>><?php echo $group['name'] ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-md-offset-4"></div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-9 col-md-offset-3">
				<div class="row" id="MainMenu">
					<div class="list-group panel col-md-4">
						<h4>Manufacturers</h4>
						<div id="loadManufacturers">
							<div class="row">
								<div class="col-md-6">
									<?php foreach ($manufacturers as $i => $row) : ?>
										<?php if ($i == round((count($manufacturers) / 2))) { ?>
									</div>
									<div class="col-md-6">
										<?php  } ?>
										<a href="" class="list-group-item" data-toggle="collapse" onclick="loadNav(this, 'loadModels', '', '<?php echo $row['id']; ?>');"><?php echo $row['name']; ?></a>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="list-group panel col-md-4">
						<h4>Models</h4>
						<div id="loadModels">

						</div>
					</div>
					<div class="list-group panel col-md-4">
						<h4>Sub Models</h4>
						<div id="loadSubModels">

						</div>
					</div>
				</div>
			</div>
			<input type="hidden" id="manufacturer_id">
			<input type="hidden" id="device_id">
			<input type="hidden" id="model_id">
			<input type="hidden" id="main_class_id">
		</div>
		<br>
		<br>
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-3">
						<h3>Product Type</h3>
						<div id="class">
						</div>
					</div>
					<div class="col-md-9">
						<div class="row">
							<h3>&nbsp;</h3>
							<div class="col-md-12" id="products" style="text-align: center;">

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	

	<script type="text/javascript">
	jQuery('.fancybox').fancybox({width: '900px', height: '600px', autoCenter: true, autoSize: false});
		String.prototype.ucFirst = function() {
			return this.charAt(0).toUpperCase() + this.slice(1);
		}
		function number_format (num, fixed) {
			return parseFloat(Math.round(num * 100) / 100).toFixed(fixed);
		}
		var cAction = '';
		var cClassID = 0;
		var cOtherId = 0;
		function loadNav (t, action, classID, otherId) {
			setFilter(action, otherId);
			var colId = classID;
			if (otherId) {
				colId = otherId;
			}
			if (action) {
				$(t).parent().parent().find('.list-group-item-info').removeClass('list-group-item-info');
			}
			if ($(t).hasClass('select-all')) {
				$(t).parent().find('.list-group-item').addClass('list-group-item-info');
				removeModelId(otherId);
			}
			if (!action && $(t).hasClass('list-group-item-info') && !$(t).hasClass('select-all')) {
				$(t).removeClass('list-group-item-info');
				removeModelId(otherId);
			} else {
				$('.select-all').removeClass('list-group-item-info');
				if (!$(t).hasClass('select-all')) {
					$(t).addClass('list-group-item-info');
				}
			}
			if (!$('#' + action + classID + otherId).text()) {
				$('#products').html('');
				$.ajax({
					url: 'man_catalog.php',
					beforeSend: function () {
						$(t).append('<img src="../images/loading.gif" style="width: 20px;">');
					},
					type: 'POST',
					dataType: 'json',
					data: {action: action, class_id: classID, other_id: otherId, perform: 'loadNav'}
				}).always(function(json) {
					var nav = '';
					if (json['nav']) {

						nav = '<div class="row">';
						nav += '<div class="col-md-6">';

						var loop = json['nav'].length;

						for (var i = 0; i < loop; i++) {
							if (i == Math.round(loop / 2)) {
								nav += '</div>';
								nav += '<div class="col-md-6">';
							}

							nav += '<a href="" onclick="loadNav(this, \''+ json['next'] +'\', \''+ classID +'\', \''+ json['nav'][i].id +'\');" data-submodelid="' + json['nav'][i].id + '" class="list-group-item" data-toggle="collapse">'+ json['nav'][i].name +'</a>';

						}

						nav += '</div>';
						nav += '</div>';

						if (action == 'loadSubModels') {
							nav += '<a href="" onclick="loadNav(this, \'\', \'\', \'\');" class="list-group-item select-all text-center" data-toggle="collapse">Select All</a>';
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

		function removeModelId(otherId) {
			var str = $('#model_id').val();
			otherId = otherId + ',';
			var re = new RegExp(otherId, 'g');
			var res = str.replace(re, "");
			if (otherId == ',') {
				res = '';
				$('#loadSubModels').find('.list-group-item').each(function() {
					if ($(this).attr('data-submodelid')) {
						res = res + $(this).attr('data-submodelid') + ',';
					}
				});
			}
			$('#model_id').val(res);
		}

		function setFilter (action, otherId) {
			var disableDiv = '<div class="disableDiv"><span class="editLayer" onclick="$(this).parent().remove();"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></span><span class="sign"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></span></div>';
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
				filter = getFilter();
				$.ajax({
					url: 'man_catalog.php',
					type: 'POST',
					dataType: 'json',
					data: {filter: filter, class: $(t).val(), perform: 'loadAttr'}
				}).always(function(json) {

					var attributes = '';

					if (json['attributes']) {
						attributes = '<div class="well attrib">';
						attributes += '<div class="row">';
						var main_name = '';

						var loop = json['attributes'].length;
						for (var i = 0; i < loop; i++) {

							if (json['attributes'][i].main_name != main_name) {
								attributes += '<div class="col-md-12">';
								attributes += '<h5>'+ json['attributes'][i].main_name.ucFirst() +'</h5>';
								attributes += '</div>';
								main_name = json['attributes'][i].main_name;
							}

							attributes += '<div class="col-md-10 col-md-offset-2 select">';
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


				products = '<div class="row">';
				for (var i = 0; i < loop; i++) {

					if (!(i%3) && i != 0) {
						products += '</div>';
						products += '<div class="row">';
					}

					var is_grade_qty_avail = false;
					var grades = productsx[i].grades;
					var scrapes = productsx[i].scraped_prices;
					var is_sale_item = productsx[i].is_sale_item;
					var true_cost_row = productsx[i].true_cost_row;
					var intQty = false;
					var gradeChecked = false;
					var gradesHtml = '';
					var scrapeHtml = '';
					var true_cost_rowHtml = '';
					var price = productsx[i].price;
					if (productsx[i].quantity != '0' || productsx[i].qtyOutStock != '0') {
						intQty = true;
					}

					for (var r = 0; r < grades.length; r++) {
						var checked = '';
						if (!intQty && grades[r].quantity != '0' && !gradeChecked) {
							checked = 'checked="checked"';
							gradeChecked = true;
							price = grades[r].price;
						}


						gradesHtml += '<div class="row grade">';

						gradesHtml += '<div class="col-md-4">';
						gradesHtml +=  grades[r].link;
						gradesHtml += '</div>';
						gradesHtml += '<div class="col-md-3">';
						gradesHtml += '<span>$'+ number_format(grades[r].price, 2) +'</span>';
						gradesHtml += '</div>';
						gradesHtml += '<div class="col-md-3">';
						gradesHtml += '<span>QTY '+ grades[r].quantity +'</span>';
						gradesHtml += '</div>';
						gradesHtml += '<div class="col-md-2">';
						gradesHtml += '<label class="radio-inline">';
						gradesHtml += '<input grade_price="'+grades[r].price+'" '+ checked +' type="radio" onchange="changePrice($(\'#priceGroup\').val(),$(\'.product-'+productsx[i].product_id+'\').find(\'.qty\').val() , \''+ productsx[i].product_id +'\')" name="pr-'+ productsx[i].product_id +'" value="' + grades[r].product_id + '">&nbsp;';
						gradesHtml += '</label>';
						gradesHtml += '</div>';

						gradesHtml += '</div>';

						if(grades[r].quantity>0) {
							is_grade_qty_avail = true;
						}
					}
					if ($('#competitor_checkbox').prop("checked") == true) {
							true_cost_rowHtml += '<div class="row_true_cost">';
							true_cost_rowHtml += '<div style="font-size:11px;" class="col-md-3"><b>Cost<br>Date</b><br>';
							true_cost_rowHtml +=	true_cost_row.cost_date;
							true_cost_rowHtml +=	'</div>';
							true_cost_rowHtml += '<div style="font-size:11px;" class="col-md-2"><b>Raw Cost</b><br>';
							true_cost_rowHtml +=	true_cost_row.raw_cost;
							true_cost_rowHtml +=	'</div>';
							true_cost_rowHtml += '<div style="font-size:11px;" class="col-md-2"><b>Ex. Rate</b><br>';
							true_cost_rowHtml +=	true_cost_row.ex_rate;
							true_cost_rowHtml +=	'</div>';
							true_cost_rowHtml += '<div style="font-size:11px;" class="col-md-2"><b>Ship Fee</b><br>';
							true_cost_rowHtml +=	true_cost_row.shipping_fee;
							true_cost_rowHtml +=	'</div>';
							true_cost_rowHtml += '<div style="font-size:11px;" class="col-md-3"><b>True<br> Cost</b><br>';
							true_cost_rowHtml +=	true_cost_row.true_cost;
							true_cost_rowHtml +=	'</div>';
							true_cost_rowHtml +=	'</div>';
					
				}

						for (var r = 0; r < scrapes.length; r++) {
							if ($('#competitor_checkbox').prop("checked") == true) {
								if (scrapes[r].check =='price_present') {
											scrapeHtml += '<div class="row scrape">';
											scrapeHtml += '<div class="col-md-3">';
											if (scrapes[r].site_name == 'mobile_sentrix') {
											scrapeHtml +=  'MS: ';
											}else if (scrapes[r].site_name == 'fixez'){
												scrapeHtml +=  'FX: ';
											}else if (scrapes[r].site_name == 'mengtor'){
												scrapeHtml +=  'MG: ';
											}else if (scrapes[r].site_name == 'mobile_defenders'){
												scrapeHtml +=  'MD: ';
											}else if (scrapes[r].site_name == 'etrade_supply'){
												scrapeHtml +=  'ETS: ';
											}else if (scrapes[r].site_name == 'maya_cellular'){
												scrapeHtml +=  'MC: ';
											}else if (scrapes[r].site_name == 'lcd_loop'){
												scrapeHtml +=  'LL: ';
											}else if (scrapes[r].site_name == 'parts_4_cells'){
												scrapeHtml +=  'P4C: ';
											}else if (scrapes[r].site_name == 'cell_parts_hub'){
												scrapeHtml +=  'CPH: ';
											}

											scrapeHtml += '</div>';
											if (scrapes[r].stock == 'no') {
											} 
											scrapeHtml += '<div class="col-md-3">';
											if (scrapes[r].site_url) {
												if (scrapes[r].stock == 'no') {
													scrapeHtml +=  '<a style="color:red;" target="_blank" href="'+scrapes[r].site_url+'">$'+scrapes[r].current_price+'</a>';
												} else {
													scrapeHtml +=  '<a style="color:green;" target="_blank" href="'+scrapes[r].site_url+'">$'+scrapes[r].current_price+'</a>';
												}
											} else {
												if (scrapes[r].stock == 'no') {
													scrapeHtml +=  '<a style="color:red;" href="javascript:void(0);">$'+scrapes[r].current_price+'</a>';
												} else {
													scrapeHtml +=  '<a style="color:green;" href="javascript:void(0);">$'+scrapes[r].current_price+'</a>';
												}
											}
											scrapeHtml += '</div>';
											if(scrapes[r].old_price!=null && scrapes[r].old_price!='' && scrapes[r].old_price!=0.00 )
											{


											scrapeHtml += '<div class="col-md-6">';
											scrapeHtml +=  '('+scrapes[r].change+',$'+(scrapes[r].old_price).toFixed(2)+') ';
											scrapeHtml += '</div>';
										}
											scrapeHtml += '</div>';
								}
							}
						
					}
					products += '<div class="col-md-4 product product-'+ productsx[i].product_id +'" data-pID="'+ productsx[i].product_id +'" data-sku="'+ productsx[i].model +'">';
					products += '<div class="containProduct">';
					
					products += '<img data-holder-rendered="true" src="' + productsx[i].image + '" style="width: 200px; height: 200px;" data-src="holder+js/200x200" class="img-thumbnail" >';
					products += '<h5>' + productsx[i].link + '</h5>';
					products += '<h4>'+productsx[i].name+'</h4>';

					products += '<div class="well">';
					
					products += '<div class="row">';


					products += '<div class="col-md-8">';

					products += '<div class="row">';

					products += '<div class="col-md-8">';
					products += '<span>In Stock</span>';
					products += '</div>';

					products += '<div class="col-md-4">';
					products += '<span>'+ productsx[i].quantity +'</span>';
					products += '</div>';

					products += '<div class="col-md-8">';
					products += '<span>On Order</span>';
					products += '</div>';

					products += '<div class="col-md-4">';
					products += '<span>'+ productsx[i].qtyOutStock +'('+productsx[i].vendorNeededQty+')</span>';
					products += '</div>';

					products += '</div>';

					products += '</div>';

					products += '<div class="col-md-4">';

					products += '<div class="radio">';
					products += '<label>';
					products += '<input type="radio" grade_price="0.00" id="pr-'+productsx[i].product_id+'-main" onchange="changePrice($(\'#priceGroup\').val(),$(\'.product-'+productsx[i].product_id+'\').find(\'.qty\').val() , \''+ productsx[i].product_id +'\')" ' + ((intQty)? 'checked="checked"' : '') + '  name="pr-'+ productsx[i].product_id +'" value="' + productsx[i].product_id + '">';
					products += '</label>';
					products += '</div>';

					products += '</div>';

					products += '</div>';
					if (is_sale_item.is_sale_item) {
						products += '<div class="row" style="color:red;">';
						products += 'Sale Price: $'+ number_format(is_sale_item['sale_price'], 2);
						products += '</div>';
					}	
					products += '<div class="row price">';
					products += '<div class="<div class="table-responsive">';
					products += '<table class="table table-hover table-bordered customColor">';

					
					var priceGroup = productsx[i].priceGroup;
					var data_sku = productsx[i].model;

					for (var x = 0; x < priceGroup.length; x++) {
						if (data_sku.substr(0,7)=='APL-001' || data_sku.substr(0,4)=='SRN-' || data_sku.substr(0,7)=='TAB-SRN' ) {
				// priceGroup[x].customer_group_id = 1633;
				filter['group_id'] = 1633;
			}


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

					products += gradesHtml;

					products += '</div>';
					products += '<div>';
					products += true_cost_rowHtml;
					products += '</div>';
					products += '<br><br><br>';
					products += scrapeHtml;
					products += '<br>';
					products += '<div class="row">';
					
					if (is_sale_item.is_sale_item && price != is_sale_item['sale_price']) {
						price = is_sale_item['sale_price'];
					}

					products += '<div class="col-md-6" style="display: none;">';
					products += '<p class="sPrice">$'+ number_format(price ,2)+'</p>';
					products += '</div>';

					products += '<div class="col-md-4 col-md-offset-4">';
					products += '<input style="text-align:center" value="1" type="number" class="form-control qty" onchange="changePrice($(\'#priceGroup\').val(), $(this).val(), \''+ productsx[i].product_id +'\')">';
					products += '</div>';

					products += '<div class="col-md-6" style="text-align:right">';
					products += '<p>Total:</p>';
					products += '</div>';
					products += '<div class="col-md-6" style="text-align:left">';
					products += '<p class="total">$'+ number_format(price ,2)+'</p>';
					products += '</div>';


					products += '<div class="col-md-2"></div>';
					products += '<div class="col-md-8">';
					if(productsx[i].quantity==0 && productsx[i].qtyOutStock==0 && is_grade_qty_avail==false)	{
						products += '<button type="button" class="btn btn-danger btn-block" >Out of Stock</button>';

					} else {
						products += '<button type="button" class="btn btn-warning btn-block" onclick="addToCart(\''+ productsx[i].product_id +'\')">Add</button>';
					}
					products += '</div>';
					products += '<div class="col-md-2"></div>';


					products += '</div>';

					products += '</div>';
					products += '</div>';
				}
				products += '</div>';
				products += '</div>';

				var pagination = '';
				pagination += '<div class="row">';
				pagination += '<nav>';
				pagination += '<ul class="pagination">';

				pagination += '<li '+ ((paginationx['page'] == 1)? 'class="disabled"': '') +'>';
				pagination += '<a href="javascript:void(0);" '+ ((paginationx['page'] == 1)? '': 'onclick="loadProducts(1, '+ (paginationx['page'] - 1) +')"') +' aria-label="Previous">';
				pagination += '<span aria-hidden="true">&laquo;</span>';
				pagination += '</a>';
				pagination += '</li>';

				if (paginationx['noPages'] > 10) {
					if (paginationx['page'] > 4 && paginationx['page'] < (paginationx['noPages'] - 5)) {
						for (i=(paginationx['page'] - 2); i < (paginationx['page'] + 4) ; i++) {
							pagination += '<li '+ ((paginationx['page'] == i)? 'class="active"': '') +'><a href="javascript:void(0);" '+ ((paginationx['page'] == i)? '': 'onclick="loadProducts(1, '+ (i) +')"') +'>'+ i +'</a></li>';
						}
					} else {
						for (i=1; i < 6 ; i++) {
							pagination += '<li '+ ((paginationx['page'] == i)? 'class="active"': '') +'><a href="javascript:void(0);" '+ ((paginationx['page'] == i)? '': 'onclick="loadProducts(1, '+ (i) +')"') +'>'+ i +'</a></li>';
						}
					}
					pagination += '<li><a href="javascript:void(0);">+++</a></li>';
					for (i=(paginationx['noPages'] - 5); i <= paginationx['noPages'] ; i++) { 
						pagination += '<li '+ ((paginationx['page'] == i)? 'class="active"': '') +'><a href="javascript:void(0);" '+ ((paginationx['page'] == i)? '': 'onclick="loadProducts(1, '+ (i) +')"') +'>'+ i +'</a></li>';
					}
				} else {
					for (i=1; i <= paginationx['noPages'] ; i++) {
						pagination += '<li '+ ((paginationx['page'] == i)? 'class="active"': '') +'><a href="javascript:void(0);" '+ ((paginationx['page'] == i)? '': 'onclick="loadProducts(1, '+ (i) +')"') +'>'+ i +'</a></li>';
					}
				}
				pagination += '<li '+ ((paginationx['page'] == paginationx['noPages'])? 'class="disabled"': '') +'>';
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

		function loadProducts(skip, page) {
			if (!cAction || cAction == 'loadModels' || cAction == 'loadSubModels' || !$('#model_id').val()) {
				return false;
			}

			if (!page) {
				page = 1;
			}

			filter = getFilter();

			$.ajax({
				url: 'man_catalog.php?page='+page,
				beforeSend: function () {
					$('#products').html('<img src="../images/loading.gif" style="max-width: 200px;">');
				},
				type: 'POST',
				dataType: 'json',
				data: {filter: filter, perform: 'loadProducts'}
			}).always(function(json) {
				$('#products').find('img').remove();
				
				$('#products').html(randerProducts(json['products'], json['pagination']));
				if (skip != 1) {
					var classes = '';
					if (json['classes']) {

						classes = '<div class="row">';
						var main_name = '';
						var loop = json['classes'].length;
						for (var i = 0; i < loop; i++) {						
							if (json['classes'][i].main_name != main_name) {
								classes += '<div class="col-md-12">';
								classes += '<h4>'+ json['classes'][i].main_name +'</h4>';
								classes += '</div>';
								main_name = json['classes'][i].main_name;
							}

							classes += '<div class="col-md-11 selectClass">';
							classes += '<div class="checkbox">';
							classes += '<label>';
							classes += '<input class="class_id" type="checkbox" '+ (( $.inArray( json['classes'][i].id, filter['class_id'] ) != '-1' )? 'checked="checked"': '') +' value="'+ json['classes'][i].id +'" onclick="javascript:void(0);" onChange="loadAttr(this)"> ' + json['classes'][i].name;
							classes += '</label>';
							classes += '</div>';
							classes += '</div>';
						}
						classes += '</div>';

					}
					$('#class').html(classes);
				}
				if ($('#products').text()) {
					$('[data-toggle=tooltip]').tooltip();
				}
			});
		}

		function changePrice (group_id, qty, product_id, returnPrice) {

			if (!group_id) {
				return false;
			}

			$('.groupPrice').hide();
			$('.group-' + group_id).show();

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
				$('input[name=pr-' + product_id + ']').each(function() {
					if ($(this).is(':checked')) {
						grade_price = parseFloat(grade_price) + parseFloat($(this).attr('grade_price'));

					}
				});;
				grade_price = parseFloat(grade_price) * qty;
				price = parseFloat(price) + parseFloat(grade_price);
				if (returnPrice) {
					return parseFloat(price / qty);
				}
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
			
			var priceQty = parseInt($('input[data-id="'+ product_id +'"]').val()) + parseInt(qty);
			if (!priceQty) {
				priceQty = parseInt(qty);
			}
			var price = changePrice($('#priceGroup').val(), priceQty, product_id, true);
			console.log(priceQty);
			$('input[name=pr-' + product_id + ']').each(function() {
				if ($(this).is(':checked')) {
					sendCartAjax($(this).val(), qty, price, 0);
				}
			});
		}

		function updateCart () {
			$('#cart input[type=number]').each(function() {
				price = changePrice($('#priceGroup').val(), $(this).val(), $(this).attr('data-id'), true)
				sendCartAjax ($(this).attr('data-id'), $(this).val(), price, 1);
			});
		}

		function sendCartAjax (product_id, qty, price, update,force_add = 0) {
			$.ajax({
				url: 'ajax_product_add.php',
				type: 'post',

				data:{product_id:product_id, qty:qty, price:price, update: update,force_add:force_add},
				dataType: 'json'
			}).always(function(json) {
				if(json['error'])
				{
					alert(json['error']);
					return false;
				}
				if(json['success'])
				{
					$('.cartHolder').html(json['data']);
				}
			});
		}

		function removeFromCart (product_id) {
			$.ajax({
				url: 'ajax_product_add.php',
				type: 'post',

				data:{product_id:product_id, action: 'remove'},
				dataType: 'json'
			}).always(function(json) {
				if(json['success']) {
					$('.cartHolder').html(json['data']);
				}
			});
		}

		function mapEmail(email) {

			if(jQuery.trim(email)=='')
			{
				return false;
			}
			$.ajax({
				url: 'get_customer_group.php',
				type: 'post',

				data:{email:email},
				dataType: 'json'
			}).always(function(json) {
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
			}); 
		}
		function checkout() {
			if(!confirm('Are you sure want to proceed?'))
			{
				return false;
			}
			window.location='<?=$host_path;?>product_catalog/checkout.php?email='+encodeURIComponent($('#customerEmail').val())+'&customer_group_id='+$('#priceGroup').val()+'&hide_header='+$('#hide_header').val();
		}
		$(document).ready(function(e) {
			$.ajax({
				url: 'ajax_product_add.php',
				type: 'post',

				data:{action: 'preload'},
				dataType: 'json'
			}).always(function(json) {
				if(json['error'])
				{
					alert(json['error']);
					return false;
				}
				if(json['success'])
				{
					$('.cartHolder').html(json['data']);
				}
			});

   });
	</script>
</body>
</html>