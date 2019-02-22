<?php
include_once '../config.php';
include_once '../inc/functions.php';
include_once 'load_catalog2.php';
//$catalog->loadModelClasses(array('class_id' => '', 'model_id' => '79'));
$manufacturers = $catalog->loadManufacturers();
$userGroups = $catalog->userGroup();
$group_id = $catalog->getUserGroup($_GET['email']);

if ($_POST['perform'] == 'loadNav') {
	
	$json['attr'] = '';

	$_POST['next'] = $catalog->nextAction($_POST['action']);
	

	$json['data'] = ' ';
	if ($_POST['action']) {
		$array = $catalog->$_POST['action']($_POST['other_id']);
		if ($array) {
			$json['data'] = '<div class="row">';
			$json['data'] .= '<div class="col-md-6">';
			foreach ($array as $i => $row) {
				if ($i == round((count($array)) / 2)) {
					$json['data'] .= '</div>';
					$json['data'] .= '<div class="col-md-6">';
				}
				$json['data'] .= '<a href="" onclick="loadNav(this, \''. $_POST['next'] .'\', \''. $_POST['class_id'] .'\', \''. $row['id'] .'\');" data-submodelid="' . $row['id'] . '" class="list-group-item" data-toggle="collapse">'. $row['name'] .'</a>';
				//$json['data'] .= '<div class="collapse ' . $_POST['next'] . '" id="' . $_POST['next'] . $_POST['class_id'] . $row['id'] . '">';
				//$json['data'] .= '</div>';
			}
			$json['data'] .= '</div>';
			$json['data'] .= '</div>';
			if ($_POST['action'] == 'loadSubModels') {
				$json['data'] .= '<a href="" onclick="loadNav(this, \'\', \'\', \'\');" class="list-group-item select-all text-center" data-toggle="collapse">Select All</a>';
			}
		}
	}
	echo json_encode($json);
	exit;
}

if ($_POST['perform'] == 'loadProducts') {

	$_POST['next'] = $catalog->nextAction($_POST['action']);

	$filter = $_POST['filter'];
	if ($filter['attrib_id']) {
		$filter['attrib_id'] = "('" . implode("','", $filter['attrib_id']) . "')";
	}
	$filter['page'] = (int)$_GET['page'];
	

	$array = $catalog->filterProducts($filter);

	$json['data'] = '';
	if ($array['products']) {

		$json['data'] = '<div class="row">';
		foreach ($array['products'] as $i => $row) {
			$row['name'] = changeNameCatalog($row['name']);
			if (!($i%3) && $i != 0) {
				$json['data'] .= '</div>';
				$json['data'] .= '<div class="row">';
			}

			$json['data'] .= '<div class="col-md-4 product product-'. $row['product_id'] .'" data-pID="'. $row['product_id'] .'" data-sku="'. $row['model'] .'">';
			$json['data'] .= '<img data-holder-rendered="true" src="' . noImage($row['image'], $host_path, $path) . '" style="width: 200px; height: 200px;" data-src="holder.js/200x200" class="img-thumbnail" >';
			$json['data'] .= '<h5>' . linkToProduct($row['model'], $host_path, $extra = ' target="_blank"') . '</h5>';
			$json['data'] .= '<h4>'.$row['name'].'</h4>';

			$json['data'] .= '<div class="well" style="min-height: 250px;">'; // Well Start

			$json['data'] .= '<div class="row">'; // Product Quantity
			

			$json['data'] .= '<div class="col-md-8">';
			
			$json['data'] .= '<div class="row">';

			$json['data'] .= '<div class="col-md-8">';
			$json['data'] .= '<span>In Stock</span>';
			$json['data'] .= '</div>';

			$json['data'] .= '<div class="col-md-4">';
			$json['data'] .= '<span>'. $row['quantity'] .'</span>';
			$json['data'] .= '</div>';

			$json['data'] .= '<div class="col-md-8">';
			$json['data'] .= '<span>On Order</span>';
			$json['data'] .= '</div>';

			$json['data'] .= '<div class="col-md-4">';
			$json['data'] .= '<span>'. $catalog->productQtyOnOrder($row['model']) .'</span>';
			$json['data'] .= '</div>';

			$json['data'] .= '</div>';
			
			$json['data'] .= '</div>';

			$json['data'] .= '<div class="col-md-4">'; // Norman Product Select

			$json['data'] .= '<div class="checkbox">';
			$json['data'] .= '<label>';
			$json['data'] .= '<input type="checkbox" grade_price="0.00" id="pr-'.$row['product_id'].'-main" onchange="changePrice($(\'#priceGroup\').val(),$(\'.product-'.$row['product_id'].'\').find(\'.qty\').val() , \''. $row['product_id'] .'\')" checked="checked" name="pr-'. $row['product_id'] .'" value="' . $row['product_id'] . '">';
			$json['data'] .= '</label>';
			$json['data'] .= '</div>';

			$json['data'] .= '</div>';	// Normal Product Select End

			$json['data'] .= '</div>'; // Product Quantity

			$json['data'] .= '<div class="row price">';
			$json['data'] .= '<div class="<div class="table-responsive">';
			$json['data'] .= '<table class="table table-hover table-bordered customColor">';

			foreach ($catalog->productPrice($row['product_id']) as $x => $rowx) {

				if ($rowx['quantity'] < 3) {
					$text = '1-2';
				}
				if ($rowx['quantity'] == 3 && $rowx['quantity'] < 10) {
					$text = '3-9';
				}
				if ($rowx['quantity'] == 10) {
					$text = '10 +';
				}

				$json['data'] .= '<tr class="groupPrice group-'. $rowx['customer_group_id'] .'" '. (($filter['group_id'] != $rowx['customer_group_id'])? 'style="display: none;"': '') .'>';
				$json['data'] .= '<td><span>' . $text . '</span></td>';
				$json['data'] .= '<td><span class="p'. $row['product_id'] . $rowx['customer_group_id'] . $rowx['quantity'] .'">$'. number_format($rowx['price'], 2) .'</span></td>';
				$json['data'] .= '</tr>';

				if ($filter['group_id'] == $rowx['customer_group_id'] && $rowx['quantity'] == '1') {
					$row['price'] = $rowx['price'];
				}

			}

			$json['data'] .= '</table>';
			$json['data'] .= '</div>';
			$json['data'] .= '</div>';

			// Product Grades
			$is_grade_qty_avail = false;
			foreach ($catalog->productGrade($row['model']) as $r => $rowr) {
				$json['data'] .= '<div class="row grade">';

				$json['data'] .= '<div class="col-md-4">';
				$json['data'] .=  linkToProduct($rowr['model'], $host_path, ' target="_blank" data-toggle="tooltip" title="'. $rowr['model'] .'"', $rowr['item_grade']);
				$json['data'] .= '</div>';
				$json['data'] .= '<div class="col-md-3">';
				$json['data'] .= '<span>$'. number_format($rowr['price'], 2) .'</span>';
				$json['data'] .= '</div>';
				$json['data'] .= '<div class="col-md-3">';
				$json['data'] .= '<span>QTY '. (int)$rowr['quantity'] .'</span>';
				$json['data'] .= '</div>';
				$json['data'] .= '<div class="col-md-2">';
				$json['data'] .= '<label class="checkbox-inline">';
				$json['data'] .= '<input grade_price="'.$rowr['price'].'" type="checkbox" onchange="changePrice($(\'#priceGroup\').val(),$(\'.product-'.$row['product_id'].'\').find(\'.qty\').val() , \''. $row['product_id'] .'\')" name="pr-'. $row['product_id'] .'" value="' . $rowr['product_id'] . '">&nbsp;';
				$json['data'] .= '</label>';
				$json['data'] .= '</div>';

				$json['data'] .= '</div>';

				if($rowr['quantity']>0)
				{
					$is_grade_qty_avail = true; // enable the add to cart button if any qty available
				}
			}

			$json['data'] .= '</div>'; // Well End
			
			$json['data'] .= '<div class="row">';

			$json['data'] .= '<div class="col-md-6" style="display: none;">';
			$json['data'] .= '<p class="sPrice">$'. number_format($row['price'] ,2).'</p>';
			$json['data'] .= '</div>';

			$json['data'] .= '<div class="col-md-4 col-md-offset-4">';
			$json['data'] .= '<input style="text-align:center" value="1" type="number" class="form-control qty" onchange="changePrice($(\'#priceGroup\').val(), $(this).val(), \''. $row['product_id'] .'\')">';
			$json['data'] .= '</div>';

			$json['data'] .= '<div class="col-md-6" style="text-align:right">';
			$json['data'] .= '<p>Total:</p>';
			$json['data'] .= '</div>';
			$json['data'] .= '<div class="col-md-6" style="text-align:left">';
			$json['data'] .= '<p class="total">$'. number_format($row['price'] ,2).'</p>';
			$json['data'] .= '</div>';
			

			$json['data'] .= '<div class="col-md-2"></div>';
			$json['data'] .= '<div class="col-md-8">';
			if($row['quantity']==0 && $catalog->productQtyOnOrder($row['model'])==0 && $is_grade_qty_avail==false)
			{
				$json['data'] .= '<button type="button" class="btn btn-danger btn-block" >Out of Stock</button>';

			}
			else
			{
				$json['data'] .= '<button type="button" class="btn btn-warning btn-block" onclick="addToCart(\''. $row['product_id'] .'\')">Add</button>';
			}
			$json['data'] .= '</div>';
			$json['data'] .= '<div class="col-md-2"></div>';


			$json['data'] .= '</div>';

			$json['data'] .= '</div>';
		}
		$json['data'] .= '</div>';
		$json['data'] .= '</div>';

		// Pagination
		$total = $array['total'];
		$page = $_GET['page'];
		$nRows = 50;
		$xPages = $total / $nRows;
		$noPages = explode('.', $xPages)[0];
		$noPages = (($xPages > $noPages) ? $noPages + 1: $noPages);
		$json['data'] .= '<div class="row">';
		$json['data'] .= '<nav>';
		$json['data'] .= '<ul class="pagination">';

		$json['data'] .= '<li '. (($page == 1)? 'class="disabled"': '') .'>';
		$json['data'] .= '<a href="javascript:void(0);" '. (($page == 1)? '': 'onclick="loadProducts(1, '. ($page - 1) .')"') .' aria-label="Previous">';
		$json['data'] .= '<span aria-hidden="true">&laquo;</span>';
		$json['data'] .= '</a>';
		$json['data'] .= '</li>';

		if ($noPages > 10) {
			if ($page > 4 && $page < ($noPages - 5)) {
				for ($i=($page - 2); $i < ($page + 4) ; $i++) {
					$json['data'] .= '<li '. (($page == $i)? 'class="active"': '') .'><a href="javascript:void(0);" '. (($page == $i)? '': 'onclick="loadProducts(1, '. ($i) .')"') .'>'. $i .'</a></li>';
				}
			} else {
				for ($i=1; $i < 6 ; $i++) {
					$json['data'] .= '<li '. (($page == $i)? 'class="active"': '') .'><a href="javascript:void(0);" '. (($page == $i)? '': 'onclick="loadProducts(1, '. ($i) .')"') .'>'. $i .'</a></li>';
				}
			}
			$json['data'] .= '<li><a href="javascript:void(0);">...</a></li>';
			for ($i=($noPages - 5); $i <= $noPages ; $i++) { 
				$json['data'] .= '<li '. (($page == $i)? 'class="active"': '') .'><a href="javascript:void(0);" '. (($page == $i)? '': 'onclick="loadProducts(1, '. ($i) .')"') .'>'. $i .'</a></li>';
			}
		} else {
			for ($i=1; $i <= $noPages ; $i++) { 
				$json['data'] .= '<li '. (($page == $i)? 'class="active"': '') .'><a href="javascript:void(0);" '. (($page == $i)? '': 'onclick="loadProducts(1, '. ($i) .')"') .'>'. $i .'</a></li>';
			}
		}
		$json['data'] .= '<li '. (($page == $noPages)? 'class="disabled"': '') .'>';
		$json['data'] .= '<a href="javascript:void(0);" '. (($page == $noPages)? '': 'onclick="loadProducts(1, '. ($page + 1) .')"') .' aria-label="Next">';
		$json['data'] .= '<span aria-hidden="true">&raquo;</span>';
		$json['data'] .= '</a>';
		$json['data'] .= '</li>';

		$json['data'] .= '</ul>';
		$json['data'] .= '</nav>';
		$json['data'] .= '</div>';
	}

	

	$array = $catalog->loadModelClasses($filter);
	$json['class'] = '';
	if ($array) {

		$json['class'] = '<div class="row">';
		$main_name = '';
		foreach ($array as $i => $row) {
			// if (!($i%4) && $i != 0) {
			// 	$json['class'] .= '</div>';
			// 	$json['class'] .= '<div class="row">';
			// }
			if ($row['main_name'] != $main_name) {
				$json['class'] .= '<div class="col-md-12">';
				$json['class'] .= '<h4>'. $row['main_name'] .'</h4>';
				$json['class'] .= '</div>';
				$main_name = $row['main_name'];
			}

			$json['class'] .= '<div class="col-md-11 selectClass">';
			$json['class'] .= '<div class="checkbox">';
			$json['class'] .= '<label>';
			$json['class'] .= '<input class="class_id" type="checkbox" '. (( in_array($row['id'], $filter['class_id']))? 'checked="checked"': '') .' value="'. $row['id'] .'" onclick="javascript:void(0);" onChange="loadAttr(this)"> ' . $row['name'];
			$json['class'] .= '</label>';
			$json['class'] .= '</div>';
			$json['class'] .= '</div>';
		}
		$json['class'] .= '</div>';

	}

	echo json_encode($json);
	exit;
}

if ($_POST['perform'] == 'loadAttr') {

	$filter = $_POST['filter'];
	$filter['class_id'] = $_POST['class'];
	if ($filter['attrib_id']) {
		$filter['attrib_id'] = "('" . implode("','", $filter['attrib_id']) . "')";
	}

	$array = $catalog->loadClassAttr($filter);
	$json['attr'] = '';
	if ($array) {

		$json['attr'] = '<div class="well attrib">';
		$json['attr'] .= '<div class="row">';
		$main_name = '';
		foreach ($array as $i => $row) {
			// if (!($i%3) && $i != 0) {
			// 	$json['attr'] .= '</div>';
			// 	$json['attr'] .= '<div class="row">';
			// }

			if ($row['main_name'] != $main_name) {
				$json['attr'] .= '<div class="col-md-12">';
				$json['attr'] .= '<h5>'. ucfirst($row['main_name']) .'</h5>';
				$json['attr'] .= '</div>';
				$main_name = $row['main_name'];
			}

			$json['attr'] .= '<div class="col-md-10 col-md-offset-2 select">';
			$json['attr'] .= '<div class="checkbox">';
			$json['attr'] .= '<label>';
			$json['attr'] .= '<input type="checkbox" checked="checked" value="'. $row['id'] .'" onChange="loadProducts(1, 1)"> ' . ucfirst($row['name']);
			$json['attr'] .= '</label>';
			$json['attr'] .= '</div>';
			$json['attr'] .= '</div>';
		}
		$json['attr'] .= '</div>';
		$json['attr'] .= '</div>';

	}

	echo json_encode($json);
	exit;

}

unset($_SESSION['cart']);

?>
<!DOCTYPE html>
<html>
<head>
	<title>Product Catalog</title>
	<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap-theme.min.css">
	<style type="text/css" media="screen">
		.grade {font-size: 12px;}
		.grade input[type=checkbox] {margin-top: 0;}
		#cart {display: block; position: absolute; z-index: 100; background: rgb(255, 255, 255) none repeat scroll 0% 0%; padding: 10px; border: 1px solid rgb(221, 221, 221); border-radius: 10px; width: 100%;}
		#cart .cart-item{font-size: 10px; min-height: 60px;}
		.table-bordered.customColor > tbody > tr > td {border: 1px solid #999; border-left: 0; border-right: 0; padding:0;}
		.list-group-item {padding: 2px 2px 2px 18px; border-width: 0; border-bottom-width: 2px;}
		#MainMenu {max-height: 250px; overflow:hidden;}
		#loadManufacturers {max-height: 210px; min-height: 210px; overflow-x:hidden;}
		#loadModels {max-height: 210px; min-height: 210px;  overflow-x:hidden;}
		#loadSubModels {max-height: 210px; min-height: 210px; overflow-x:hidden;}
		#MainMenu .list-group { border-left: 1px solid #ccc; border-radius: 0;}
		#MainMenu .list-group:first-child {border-left: 0;}
		.col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 { padding-left: 5px; padding-right: 5px;}
		.row {margin-right: -5px; margin-left: -5px;}
		.product h4 { min-height: 60px;}
		.disableDiv {position: absolute; top: 0; left: 0; width: 100%; height: 100%; text-align: center; background: rgba(0, 0, 0, 0.5);}
		.disableDiv .editLayer {padding: 10px; line-height: 30px; width: 50px; height: 50px; position: relative; top: 50%; transform: translate(0, -50%); color: #000; font-size: 20px; border-radius: 100%; cursor: pointer; background-color: rgba(255, 255, 255, 1); display: inline-block;}
		.disableDiv .editLayer:hover {color: #286090;}
		.disableDiv .sign {color: #286090; position: absolute; right: 20px; top: 50%; line-height: 12px; font-size: 12px; padding: 5px; width: 25px; height: 25px; transform: translate(0px, -50%); background-color: rgba(255, 255, 255, 1); border-radius: 100%;}
	</style>
</head>
<body>
	<!-- <nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>	
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="javascript:void(0);">Product Catalog</a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li class="active"><a href="javascript:void(0);">Home</a></li>
				</ul>
			</div>
		</div>
	</nav> -->
	<?php if (isset($_SESSION['login_as'])) { ?>
	<div class="row">
		<div class="col-md-12">
			<?php include_once '../inc/header.php';?>
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
		
		<div class="row">
			<div class="col-md-9 col-md-offset-3" id="user">
				<div class="form-group">
					<div class="col-sm-4">
						<input type="email" id="customerEmail" class="form-control" value="<?php echo $_GET['email']; ?>" onBlur="mapEmail(this.value)" placeholder="Email">
					</div>

					<div class="col-sm-4">
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
	<script type="text/javascript" src="<?php echo $host_path; ?>/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $host_path; ?>include/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript">
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
					$(t).find('img').remove();
					//$('#' + action + classID + otherId).html(json['data']);
					$('#' + action).html(json['data']);
					$('#class').html('');
					//$('#' + action + classID + otherId).collapse('show');
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

			var attrib = [];
			$('.select').each(function() {
				var cBox = $(this).find('input');
				if (cBox.is(':checked')) {
					attrib.push(cBox.val());
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
			// if ($(t).is(':checked')) {
			// 	$('.selectClass').each(function() {
			// 		var rBtn = $(this).find('input');
			// 		rBtn.prop('checked', false);
			// 	});
			// 	$(t).prop('checked', true)
			// } else {
			// 	$('.attrib').remove();
			// 	loadProducts(1);
			// }

			if ($(t).is(':checked') && !select) {
				//$('.attrib').remove();
				filter = getFilter();
				$.ajax({
					url: 'man_catalog.php',
					type: 'POST',
					dataType: 'json',
					data: {filter, class: $(t).val(), perform: 'loadAttr'}
				}).always(function(json) {
					$(t).parent().parent().parent().append(json['attr']);
				});
			} else if (!$(t).is(':checked')) {
				attribObj.find('input[type=checkbox]').prop('checked', false);
				attribObj.hide();
			} else {
				attribObj.find('input[type=checkbox]').prop('checked', true);
				attribObj.show();
			}
			loadProducts(1);
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
					$('#products').html('<img src="../images/loading.gif" style="width: 20px;">');
				},
				type: 'POST',
				dataType: 'json',
				data: {filter, perform: 'loadProducts'}
			}).always(function(json) {
				$('#products').find('img').remove();
				$('#products').html(json['data']);
				if (skip != 1) {
					$('#class').html(json['class']);
				}
				//onmouseover="$(this)"
				if ($('#products').text()) {
					$('[data-toggle=tooltip]').tooltip();
				}
			});
		}

		function changePrice (group_id, qty, product_id) {

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

				//console.log(price);
				if($('#pr-'+product_id+'-main').is(":checked")==false)
				{
					price = 0.00;
				}
				price = price * qty;
				var grade_price = 0.00;
				$('input[name=pr-' + product_id + ']').each(function() {
					if ($(this).is(':checked')) {
					//sendCartAjax($(this).val(), qty, 0);
					grade_price = parseFloat(grade_price) + parseFloat($(this).attr('grade_price'));
					
				}
			});;
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
			//window.parent.addProduct(sku, qty);
			$('input[name=pr-' + product_id + ']').each(function() {
				if ($(this).is(':checked')) {
					sendCartAjax($(this).val(), qty, 0);
				}
			});;
		}

		function updateCart () {
			$('#cart input[type=number]').each(function() {
				sendCartAjax ($(this).attr('data-id'), $(this).val(), 1);
			});
		}

		function sendCartAjax (product_id, qty, update) {
			$.ajax({
				url: '<?php echo $host_path;?>product_catalog/ajax_product_add.php',
        		//url: '<?php echo $local_path;?>../phoneparts/index.php?route=checkout/manual/shipping_method_for_imp',
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
        		//url: '<?php echo $local_path;?>../phoneparts/index.php?route=checkout/manual/shipping_method_for_imp',
        		type: 'post',

        		data:{product_id:product_id, action: 'remove'},
        		dataType: 'json',
        		success: function(json) {

        			if(json['success'])
        			{
        				//product.find('.btn').removeClass('btn-warning').addClass('btn-success').html('Add ('+json['success']+')');
        				$('.cartHolder').html(json['data']);
        			}



        		}
        	});
		}

		function mapEmail(email)
		{
			//alert(email);
			if(jQuery.trim(email)=='')
			{
				return false;
			}
			$.ajax({
				url: '<?php echo $host_path;?>product_catalog/get_customer_group.php',
	        //url: '<?php echo $local_path;?>../phoneparts/index.php?route=checkout/manual/shipping_method_for_imp',
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
        			//product.find('.btn').removeClass('btn-warning').addClass('btn-success').html('Add to Cart ('+json['success']+')');
        			$('#priceGroup').val(json['success']);
        			changePrice($('#priceGroup').val(), '1', '')
        		}



        	}
        }); 
		}
		function checkout()
		{
			if(!confirm('Are you sure want to proceed?'))
			{
				return false;
			}
			window.location='<?=$host_path;?>product_catalog/checkout.php?email='+encodeURIComponent($('#customerEmail').val())+'&customer_group_id='+$('#priceGroup').val();
		}
	</script>
</body>
</html>