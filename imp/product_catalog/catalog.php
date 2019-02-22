<?php
include_once '../config.php';
include_once '../inc/functions.php';
include_once 'load_catalog.php';

$classes = $catalog->loadClass();

if ($_GET['class_id']) {
	$manufacturers = $catalog->loadManufacturers('', $_GET['class_id']);

	$products = $catalog->filterProducts($_GET);
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<script type="text/javascript" src="js/jquery.min.js"></script>

	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	
</head>
<body>
	<div align="center">
		<div align="center"> 
			<?php include_once '../inc/header.php';?>
		</div>
		<h2>Class</h2>
		<table width="90%" cellpadding="10" border="1"  align="center">
			<tbody>
				<tr>
					<?php foreach ($classes as $i => $row) : ?>
						<?php if (!($i%4) && $i != 0) : ?>
						</tr>
						<tr>
						<?php endif; ?>
						<td>
							<a href="catalog.php?class_id=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a>
						</td>
					<?php endforeach; ?>
				</tr>
			</tbody>
		</table>
		<?php if ($manufacturers) : ?>
			<h2>Manufacturers</h2>
			<table width="90%" cellpadding="10" border="1"  align="center">
				<tbody>
					<tr>
						<?php foreach ($manufacturers as $i => $row) : ?>
							<?php if (!($i%4) && $i != 0) : ?>
							</tr>
							<tr>
							<?php endif; ?>
							<td>
								<a href="catalog.php?class_id=<?php echo $_GET['class_id']; ?>&manufacturer_id=<?php echo $row['manufacturer_id']; ?>"><?php echo $row['name']; ?></a>
								<table border="1">
									<tbody>
										<?php foreach ($catalog->loadModels($row['manufacturer_id'], $_GET['class_id']) as $model) : ?>
											<tr>
												<td>
													<a href="catalog.php?class_id=<?php echo $_GET['class_id']; ?>&device_id=<?php echo $model['device_id']; ?>"><?php echo $model['name']; ?></a>
													<table border="3">
														<tbody>
															<?php foreach ($catalog->loadSubModels($model['device_id'], $_GET['class_id']) as $subModels) : ?>
																<tr>
																	<td>
																		<a href="catalog.php?class_id=<?php echo $_GET['class_id']; ?>&model_id=<?php echo $subModels['model_id']; ?>"><?php echo $subModels['name'] . (($subModels['carrier'])? ' (' . $subModels['carrier'] . ')': ''); ?></a>
																	</td>
																</tr>
															<?php endforeach; ?>
														</tbody>
													</table>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</td>
						<?php endforeach; ?>
					</tr>
				</tbody>
			</table>
			<br><br>
		<?php endif; ?>
		<?php if ($products) : ?>
			<h2>Products</h2>
			<table width="90%" cellpadding="10" border="1"  align="center">
				<tbody>
					<?php foreach ($products as $product) : ?>
						<tr>
							<td>
								<?php echo $product['sku']?>
							</td>
							<td>
								<?php echo $product['name']?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		</div>
	</body>