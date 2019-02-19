<?php
include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';
if ($_POST['action'] == 'linked-sku') {
	$grades = $db->func_query('SELECT `model`, `item_grade`, `quantity`, `mps` FROM `oc_product` WHERE `main_sku` = "'. $_POST['sku'] .'" ORDER BY `item_grade` ASC');
	$reorder_setting = $db->func_query_first("select * from inv_reorder_settings");
	$json['data'] = '<table border="0">';
	$json['data'] .= '<thead>';
	$json['data'] .= '<tr><th>SKU</th><th>Grade</th><th>Qty</th><th>Avg Qty</th><th>ROP</th></tr>';
	$json['data'] .= '</thead>';
	$json['data'] .= '<tbody>';
	if ($grades) {
		foreach ($grades as $grade) {
			$mps = $grade['mps'];
			$rop = getRop($mps , $reorder_setting['lead_time'] , $reorder_setting['qc_time'] , $reorder_setting['safety_stock']);
			$rop = ceil($rop);
			$json['data'] .= '<tr><td>' . linkToProduct($grade['model'], $host_path, ' target="_blank" ') . '</td> <td><strong>'. str_replace('Grade ', '', $grade['item_grade']) . '</strong></td> ' . '<td>' . $grade['quantity'] . '</td> <td>' . $mps . '</td> <td>' . $rop . '</td></tr>';
		}
	} else {
		$json['data'] .= '<tr><td colspan="6">No grades found</td></tr>';
	}
	$json['data'] .= '</tbody>';
	$json['data'] .= '</table>';

	echo json_encode($json);
	exit;
}
$parameters = array();
if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}
if($page < 1){
	$page = 1;
}
$max_page_links = 10;
$num_rows = 100;
if($_REQUEST['type'] == 'below_rop'){
	$num_rows = 5000;
}
if ($_GET['outofstock']) {
	$num_rows = 50000;
}
$start = ($page - 1)*$num_rows;
$vendor_query ="";
if($_SESSION['user_id']!=0)
{
	$user_det = $db->func_query_first("SELECT * FROM inv_users WHERE id='".(int)$_SESSION['user_id']."'");
	if($user_det['group_id']==1)
	{
		//$vendor_query = " AND p.vendor LIKE '%".$user_det['name']."%'";
	}
}
if($_GET['vendor'])
{
	$vendor_query = " AND p.vendor = '".$_GET['vendor']."'";
}
if($_GET['keyword']){
	$keyword = $db->func_escape_string($_GET['keyword']);
	$inv_query  = "select p.product_id , p.model, p.quantity, p.image, pd.name, p.mps from oc_product p inner join oc_product_description pd on
	(p.product_id = pd.product_id) where Lower(model) not like Lower('BKB-MOD-%') and
	location != 1 and status = 1 and is_kit = 0 and Lower(p.model) like Lower('%$keyword%') $vendor_query";
}
else{
	$product_ids = implode(",",array_keys($_SESSION['list']));
	$where = "";
	if($product_ids){
		$where = " and p.product_id not in($product_ids)";
	}
	
	$inv_query  = "select p.product_id , p.model, p.quantity, p.image, pd.name, p.mps from oc_product p inner join oc_product_description pd on
	(p.product_id = pd.product_id)
	where Lower(model) not like Lower('BKB-MOD-%') and Lower(model) not like Lower('%k') and
	location != 1 and status = 1 and is_kit = 0 $where $vendor_query group by model order by model asc";
}
if($_REQUEST['type']){
	$parameters[] = "type=".$_REQUEST['type'];
}
$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "sales.php",$page);
$products   = $db->func_query($splitPage->sql_query);
$parameters = implode("&",$parameters);
$reorder_setting = $db->func_query_first("select * from inv_reorder_settings");
$shipments = $db->func_query("select product_sku , qty_shipped , package_number , date_issued , fb_added
	from  inv_shipments s inner join inv_shipment_items st on (s.id = st.shipment_id)
	where fb_added != 1 and date_issued >= '2015-01-30 00:00:00' AND status IN ('Received', 'Pending', 'Issued')");
	?>
	<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Re - Ordering Products</title>
		
		<script type="text/javascript" src="js/jquery.min.js"></script>
		
		
		
		<script type="text/javascript">
			$(document).ready(function() {
				

				$("img.lazy").each(function( index, value ){
					var data_src = $(this).attr('data-src');
					
					$(this).attr('src',data_src);
				})
			});
			function addToList(product_id , qty){
				jQuery.ajax({
					url: 'inc/ajax.php?action=addToList&product_id='+product_id+'&qty='+qty,
					success: function(data){
						re = new RegExp(/Error.*?/gi);
						if(re.test(data)){
							alert("Product is not added to list, try again");
						}
						else{
						//alert("Product is added to list");
						jQuery(".row_"+product_id).hide();
					}
					updateCart();
				}
			});
				updateCart();
			}
			function removeFromList(product_id , is_new){
				jQuery.ajax({
					url: 'inc/ajax.php?action=removeFromList&product_id='+product_id+'&is_new='+is_new,
					success: function(data){
						re = new RegExp(/Error.*?/gi);
						if(re.test(data)){
							alert("Product is not removed from list, try again");
						}
						else{
						//alert("Product is removed from list");
						jQuery(".row_"+product_id).remove();
						jQuery("#row_"+product_id).remove();
					}
					updateCart();
				}
			});
			}
			function updateCart(){
				jQuery.ajax({
					url: 'list_items.php',
					success: function(data){
						jQuery('#cart_items').html(data);
					}
				});
			}
		</script>
		<style type="text/css">
			.cart{
				position:absolute;
				top:10%;
				right:13%;
				text-decoration:underline;
				cursor:pointer;
			}
			.ajax-dropdown {
				width: 100%;
				position: relative;
			}
			.ajax-dropdown table {
				width: 100%;
				position: absolute;
				top: 5px;
				-webkit-transform: translate(-50%, 0%);
				-moz-transform: translate(-50%, 0%);
				-ms-transform: translate(-50%, 0%);
				-o-transform: translate(-50%, 0%);
				transform: translate(-50%, 0%);
				left: 50%;
			}
			.ajax-dropdown tbody tr {
				background-color: #fff;
				border-bottom: 1px solid #000;
				padding:5px;
			}
			.ajax-dropdown tbody td {
				padding:5px;
			}
			
			.ajax-dropdown tbody tr:hover {
				background-color: #999;
				color: #fff;
			}
		</style>
	</head>
	<body>
		<?php include_once 'inc/header.php';?>
		<?php if(@$_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		
		<div class="cart" id="cart_items" align="right">
			<?php include_once 'list_items.php';?>
		</div>
		
		<div align="center">
			<?php if($_SESSION['reorder_setting']):?>
				<a href="reorder_settings.php" class="fancybox fancybox.iframe">Re-Order Settings</a>
				<br /><br />
			<?php endif;?>
			
			<form method="get">
				Show:
				<select name="type">
					<option value="All">All Items</option>
					<option value="below_rop" <?php if($_GET['type'] == 'below_rop'):?> selected="selected" <?php endif;?>>Below ROP</option>
					<option value="above_rop" <?php if($_GET['type'] == 'above_rop'):?> selected="selected" <?php endif;?>>Above ROP</option>
				</select>
				
				&nbsp;&nbsp;
				
				<?php if($user_det['group_id']!=1){ ?>
				<?php $vendors = $db->func_query("SELECT * FROM inv_users WHERE status=1 and group_id=1 ORDER BY name"); ?>
				<select name="vendor" id="vendor_dropdown" style="width:200px">
					<option value="">All Vendor</option>
					<?php foreach($vendors as $vendor){ ?>
					<option value="<?php echo strtolower($vendor['name']);?>" <?php if(strtolower($vendor['name'])==strtolower($_GET['vendor'])) echo 'selected'; ?>><?=$vendor['name'];?> (<?=$vendor['email'];?>)</option>
					<?php } ?>
				</select>
				<br><br>
				<?php } ?> &nbsp; &nbsp;
				
				Out of Stock Days
				<select name="outofstock">
					<option value="0">Select One</option>
					<option value="7" <?php if($_GET['outofstock'] == '7'):?> selected="selected" <?php endif;?>>More than 7 days</option>
					<option value="14" <?php if($_GET['outofstock'] == '14'):?> selected="selected" <?php endif;?>>More than 14 days</option>
					<option value="30" <?php if($_GET['outofstock'] == '30'):?> selected="selected" <?php endif;?>>More than 30 days</option>
					<option value="60" <?php if($_GET['outofstock'] == '60'):?> selected="selected" <?php endif;?>>More than 60 days</option>
				</select>
				
				Keyword:
				<input type="text" name="keyword" value="<?php echo $_GET['keyword'];?>" />
				
				<input type="submit" name="search" value="Search" />
			</form>
			
			<br />
			<a href="vendor_po_createT.php">Create Vndor PO</a>
			
			&nbsp;&nbsp;
			
			<?php if($_SESSION['download_below_ropcsv']):?>
				<a href="javascript:void(0);" onclick="window.location='download.php?action=belowropcsv&vendor='+$('#vendor_dropdown').val()">Download Below ROP CSV</a>
				<br /><br />
			<?php endif;?>
		</div>
		
		<?php if($products):?>
			<table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
				<thead>
					<tr>
						<th>#</th>
						<th>Image</th>
						<th>Name</th>
						<th>SKU</th>
						<th>Current Qty</th>
						<th>Avg Qty</th>
						<th>Avg Cost</th>
						<th>ROP</th>
						<th>Needed</th>
						<th>Shipments</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php $i = $splitPage->display_i_count();
					foreach($products as $product):?>
					<?php
					$mps = $product['mps'];
					$rop = getRop($mps , $reorder_setting['lead_time'] , $reorder_setting['qc_time'] , $reorder_setting['safety_stock']);
					$rop = ceil($rop);

					$qty = getQtyToBeShipped($rop , $product['quantity'] , $mps , $reorder_setting['lead_time'] , $reorder_setting['qc_time'] , $reorder_setting['additional_days']);
					$qty = ceil($qty);

					if($qty == 0){
						$qty = 5; // default ship 5 qty
					}
					$avg_cost = (float)$db->func_query_first_cell("SELECT cost FROM inv_avg_cost WHERE sku='".$product['model']."'");
					$shipment_data = getShipmentDetail($shipments , $product['model'] , $qty);
					
					$outstock_date = $db->func_query_first_cell("select outstock_date from inv_product_inout_stocks where product_sku = '".$product['model']."' order by date_modified desc limit 1");
					$stock_days = (int)$_GET['outofstock'];
					
					if($stock_days && intval($outstock_date) == 0){
						continue;
					}
					
					if ($stock_days && $product['quantity'] != 0) {
						continue;
					}
					
					if($stock_days && (time() - strtotime($outstock_date)) < ($stock_days*24*60*60)){
						continue;
					}
					$image = $product['image'];
					$image_array = explode(".",$image);
					$extension = end($image_array);

					array_pop($image_array);
					$image = implode(".",$image_array);
					$small_image = $image.'-150x150.'.$extension;
					$large_image = $image.'-500x500.'.$extension;
					?>

					<?php if($_REQUEST['type'] == 'below_rop' && $product['quantity'] > $rop){ continue; }?>

					<?php if($_REQUEST['type'] == 'above_rop' && $product['quantity'] <= $rop){ continue; }?>

					<?php //if($_REQUEST['type'] == 'below_rop' && $shipment_data[1] <= 0){ continue; }?>

					<tr class="row_<?php echo $product['product_id'];?>">
						<td align="center"><?php echo $i; ?></td>

						<td align="center">
							<div class="imglist">
								<?php
								$imagex = explode('.', $product['image']);
								$imagex[(count($imagex) - 2)] = $imagex[(count($imagex) - 2)] . '-500x500';
								$product['image'] = implode('.', $imagex);
								?>
								<a href="https://phonepartsusa.com/image/cache/<?php echo $product['image'];?>" rel="fancybox-thumb<?php echo $product['product_id'];?>" class="fancybox-thumb">
									<img class="lazy" src="images/loading.gif" data-src="https://phonepartsusacom-wxhost.netdna-ssl.com/image/cache/<?php echo $small_image;?>" data-original="https://phonepartsusacom-wxhost.netdna-ssl.com/image/cache/<?php echo $small_image;?>" height="50" width="50" alt="" />
								</a>
								<?php $images = $db->func_query('SELECT * FROM `oc_product_image` WHERE `product_id` = "'. $product['product_id'] .'"');?>
								<span style="display:none">
									<?php foreach ($images as $image): ?>
										<?php
										$imagex = explode('.', $image['image']);
										$imagex[(count($imagex) - 2)] = $imagex[(count($imagex) - 2)] . '-500x500';
										$image['image'] = implode('.', $imagex);
										?>
										<a class="fancybox-thumb" rel="fancybox-thumb<?php echo $product['product_id'];?>" href="https://phonepartsusa.com/image/cache/<?php echo $image['image'];?>">
											<img src="https://phonepartsusa.com/image/cache/<?php echo $image['image'];?>" alt="">
										</a>
									<?php endforeach; ?>
								</span>
							</div>
							<script type="text/javascript">
								$(document).ready(function() {
									$("[rel='fancybox-thumb<?php echo $product['product_id'];?>']").fancybox({
										helpers : {
											thumbs : true
										}
									});
								});
							</script>
						</td>

						<td align="center" width="200px">
							<?php echo $product['name'];?>

							<br />
							<?php $issue_count = $db->func_query_first_cell("select count(id) from inv_product_issues where product_sku = '".$product['model']."'")?>
							<a class="fancybox3 fancybox.iframe" href="popupfiles/product_issues.php?product_sku=<?php echo $product['model'];?>"><?php echo $issue_count?> of item issues</a>
						</td>

						<td align="center">
							<a href="product/<?php echo $product['model'];?>"><?php echo $product['model'];?> (<a class="fancybox3 fancybox.iframe" href="<?php echo $host_path;?>popupfiles/item_sale_history.php?sku=<?=$product['model'];?>">Sale History</a>)</a>
							<a style="width: 0; height: 0; border-left: 10px solid transparent; border-right: 10px solid transparent; border-top: 10px solid #000; cursor: pointer;" onclick="loadLinkedSku('<?php echo $product['model'];?>');"></a>
							<div class="linked-sku-<?php echo $product['model'];?> ajax-dropdown" style="display: none;"></div>
						</td>
						<td align="center"><?php echo $product['quantity'];?></td>

						<td align="center"><?php echo $mps;?></td>
						<td align="center">$<?=number_format($avg_cost,2);?></td>
						<td align="center" <?php if($product['quantity'] <= $rop):?> style="color:red;" <?php endif;?>><?php echo $rop;?></td>

						<td align="center" <?php if($product['quantity'] <= $rop):?> style="color:red;" <?php endif;?>>
							<?php if($product['quantity'] <= $rop):?>
								<?php echo $shipment_data[1]; ?>
								<?php //echo getQtytoBeNeeded($rop,$product['quantity'],$qty);?>
							<?php endif;?>

							<?php
							if(intval($product['quantity']) == 0){
								echo "<hr/>  Out Of Stock Since: <br/> " . americanDate($outstock_date);
							}
							?>
						</td>

						<td align="center">
							<a href="popupfiles/product_shipment_history.php?sku=<?php echo $product['model'];?>" class="fancybox3 fancybox.iframe">History</a>
							<?php //echo ($shipment_data[0])? $shipment_data[0] : 'N/A';?>
						</td>

						<td align="center">
							<input type="checkbox" onclick="addToList(<?php echo $product['product_id'];?>,<?php echo $qty?>);" name="product_id[]" value="<?php echo $product['product_id'];?>" />
						</td>
					</tr>
					<?php $i++; endforeach; ?>

					<tr>
						<td colspan="5" align="left">
							<?php
							if ($stock_days) {
								echo 'Displaying 1 to '. ($i - 1) .' of ('. ($i - 1) .')';
							} else {
								echo $splitPage->display_count("Displaying %s to %s of (%s)");
							}
							?>
						</td>

						<td colspan="5" align="right">
							<?php  echo $splitPage->display_links(10,$parameters); ?>
						</td>
					</tr>
				</tbody>
			</table>
		<?php else : ?>
			<p>
				<label style="color: red; margin-left: 600px;">SKU Doesn't Exist</label>
			</p>
		<?php endif;?>
		<script type="text/javascript">
			function loadLinkedSku (sku) {
				$('.linked-sku-' + sku ).toggle();
				if (!($('.linked-sku-' + sku ).text())) {
					$.ajax({
						url: 'sales.php',
						type: 'post',
						dataType: 'json',
						data: {sku: sku, action: 'linked-sku'},
					})
					.always(function(json) {
						$('.linked-sku-' + sku ).html(json['data']);
					});
				}

			}
		</script>
	</body>
	</html>