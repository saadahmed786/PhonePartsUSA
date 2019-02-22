<?php
include_once '../config.php';
include_once '../inc/functions.php';
include_once 'load_catalog.php';

$classes = $catalog->loadClass();
if ($_POST['perform'] == 'loadNav') {
	$json['attr'] = '';
	if ($_POST['action'] == 'loadManufacturers') {
		$array = $catalog->loadClassAttr($_POST['class_id']);
		foreach ($array as $value) {
			# code...
		}
	}

	$_POST['next'] = $catalog->nextAction($_POST['action']);

	$array = $catalog->$_POST['action']($_POST['other_id'], $_POST['class_id']);
	$json['data'] = ' ';
	if ($array) {
		$json['data'] = '<div class="row">';
		$json['data'] .= '<div class="col-md-11 col-md-offset-1">';
		foreach ($array as $row) {
			$json['data'] .= '<a href="" onclick="loadNav(this, \''. $_POST['next'] .'\', \''. $_POST['class_id'] .'\', \''. $row['id'] .'\');" class="list-group-item" data-toggle="collapse">'. $row['name'] .'</a>';
			$json['data'] .= '<div class="collapse ' . $_POST['next'] . '" id="' . $_POST['next'] . $_POST['class_id'] . $row['id'] . '">';
			$json['data'] .= '</div>';
		}
		$json['data'] .= '</div>';
		$json['data'] .= '</div>';
	}
	echo json_encode($json);
	exit;
}

if ($_POST['perform'] == 'loadProducts') {

	$_POST['next'] = $catalog->nextAction($_POST['action']);

	$filter = array('class_id' => $_POST['class_id']);

	switch ($_POST['action']) {
		case 'loadModels':
		$filter['manufacturer_id'] = $_POST['other_id'];
		break;
		case 'loadSubModels':
		$filter['device_id'] = $_POST['other_id'];
		break;
		case 'model_id':
		$filter['model_id'] = $_POST['other_id'];
		break;
	}

	$array = $catalog->filterProducts($filter);
	$json['data'] = '';
	if ($array) {
		$json['data'] = '<div class="row">';
		foreach ($array as $row) {
			$json['data'] .= '<div class="col-md-4">';
			$json['data'] .= '<img data-holder-rendered="true" src="' . noImage($row['image'], $host_path, $path) . '" style="width: 200px; height: 200px;" data-src="holder.js/200x200" class="img-thumbnail" alt="'. $row['name'] .'">';
			$json['data'] .= linkToProduct($row['sku'], $host_path, $extra = ' target="_blank"') . '<h3>'. $row['name'] .'</h3>';
			$json['data'] .= '</div>';
		}
		$json['data'] .= '</div>';
	}
	echo json_encode($json);
	exit;
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Product Catalog</title>
	<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap-theme.min.css">
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
	<div class="container theme-showcase" role="main">
		<div class="jumbotron">
			<h1>Product Catalog</h1>
		</div>
		<div class="row">
			<div class="col-md-3">

				<div id="MainMenu">
					<div class="list-group panel">
						<?php foreach ($classes as $row) : ?>
							<a href="" class="list-group-item" data-toggle="collapse" onclick="loadNav(this, 'loadManufacturers', '<?php echo $row['id']; ?>', '');"><?php echo $row['name']; ?></a>
							<div class="collapse loadManufacturers" id="loadManufacturers<?php echo $row['id']; ?>"></div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<div class="col-md-9" id="products">
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
			var colId = classID;
			if (otherId) {
				colId = otherId;
			}
			
			if (action) {
				$('.' + action).collapse('hide');
			}
			if (!$('#' + action + classID + otherId).text() && action) {
				$('#products').html('');
				$.ajax({
					url: 'b_catalog.php',
					beforeSend: function () {
						$(t).append('<img src="../images/loading.gif" style="width: 20px;">');
					},
					type: 'POST',
					dataType: 'json',
					data: {action: action, class_id: classID, other_id: otherId, perform: 'loadNav'}
				}).always(function(json) {
					$(t).find('img').remove();
					$('#' + action + classID + otherId).html(json['data']);
					$('#' + action + classID + otherId).collapse('show');
				});
			} else {
				$('#' + action + classID + otherId).collapse('show');
			}
			if (!action) {
				action = 'model_id';
			}

			if (action != 'loadManufacturers') {
				loadProducts(action, classID, otherId);
			}
			
		}
		function loadProducts(action, classID, otherId) {
			cAction = action;
			cClassID = classID;
			cOtherId = otherId;
			$.ajax({
				url: 'b_catalog.php',
				beforeSend: function () {
					$('#products').html('<img src="../images/loading.gif" style="width: 20px;">');
				},
				type: 'POST',
				dataType: 'json',
				data: {action: action, class_id: classID, other_id: otherId, perform: 'loadProducts'}
			}).always(function(json) {
				$('#products').find('img').remove();
				$('#products').html(json['data']);
			});
		}
	</script>
</body>
</html>