<?php
require_once("auth.php");
require_once("inc/functions.php");
$scrapping_sites = array('mobile_sentrix', 'fixez', 'mengtor', 'mobile_defenders','etrade_supply','maya_cellular','lcd_loop','parts_4_cells','cell_parts_hub');
$total_url_count = $db->func_query_first_cell("SELECT COUNT(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE s.url <> '' AND (p.tier = '1' OR p.tier = '2')");
//$total_skus = $db->func_query("SELECT distinct(sku) FROM inv_product_price_scrap_history WHERE WEEKDAY(added) BETWEEN 0 AND 3 AND WEEK(added) = WEEK(now())");
$tier_1_skus = $db->func_query("SELECT distinct(s.sku) FROM inv_product_price_scrap s inner join oc_product p on (s.sku = p.sku) WHERE s.date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE() AND p.tier = '1'");

$tier_2_skus = $db->func_query("SELECT distinct(s.sku) FROM inv_product_price_scrap s inner join oc_product p on (s.sku = p.sku) WHERE s.date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE() AND p.tier = '2'");
//testObject($tier_2_skus);

//$total_skus = $db->func_query("SELECT distinct(sku) FROM inv_product_price_scrap_history WHERE DATE(added) = DATE(NOW())");
$last_100 = $db->func_query("SELECT * FROM inv_product_price_scrap WHERE  url <> '' order by date_updated desc limit 100");

//testObject($last_100);
//$last_100 = array();
$tier_1 = array();
$tier_2 = array();
$tier_1_count = 0;
$tier_2_count = 0;
$tier_1_url_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE  s.url <> '' AND p.tier = '1' order by s.date_updated desc");
$tier_2_url_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE  s.url <> '' AND p.tier = '2' order by s.date_updated desc");
//Last 100 Link Counters
$last_100_24hr_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE  s.url <> ''  AND (p.tier = '1' OR p.tier='2') AND  s.date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE() order by date_updated desc");
$last_100_7d_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE  s.url <> ''  AND (p.tier = '1' OR p.tier='2') AND  s.date_updated BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() order by date_updated desc");
$last_100_30d_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE  s.url <> ''  AND (p.tier = '1' OR p.tier='2') AND  s.date_updated BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() order by date_updated desc");
//Tier 1 Link Counters
$tier_1_24hr_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE  s.url <> '' AND p.tier = '1' AND s.date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE() order by s.date_updated desc");
$tier_1_7d_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE  s.url <> '' AND p.tier = '1' AND s.date_updated BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() order by s.date_updated desc");
$tier_1_30d_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE  s.url <> '' AND p.tier = '1' AND s.date_updated BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() order by s.date_updated desc");
//Tier 2 Link Counter
$tier_2_24hr_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE  s.url <> '' AND p.tier = '2' AND s.date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE() order by s.date_updated desc");
$tier_2_7d_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE  s.url <> '' AND p.tier = '2' AND s.date_updated BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() order by s.date_updated desc");
$tier_2_30d_count = $db->func_query_first_cell("SELECT count(s.id) FROM inv_product_price_scrap s inner join oc_product p on(s.sku = p.sku) WHERE  s.url <> '' AND p.tier = '2' AND s.date_updated BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() order by s.date_updated desc");
foreach ($tier_1_skus as $sku) {
	$tier_1_count++;
	//$tier_1_url_count += $db->func_query_first_cell("SELECT count(id) FROM inv_product_price_scrap WHERE sku='" . $sku['sku'] . "' AND url <> ''");
	foreach ($scrapping_sites as $site) {
			$check = $db->func_query_first("SELECT * FROM inv_product_price_scrap WHERE sku='" . $sku['sku'] . "' AND `type`='" . $site . "' AND date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE()");
			if ($check) {
				$price = $db->func_query_first("select *, (SELECT price from inv_product_price_scrap_history where sku = ph.sku and type = ph.type order by added desc limit 1, 1) as old_price from inv_product_price_scrap_history ph where sku = '" . $sku['sku'] . "' AND type = '$site' order by added DESC limit 1");
				$change = number_format($price['price'] / $price['old_price'] * 100, 2);
				if ($change < 100.00 && $change > 0.00) {
					$change = '-' . (100 - $change);
				} else if ($change == 0.00) {
					$change = 100 - $change;
				} else {
					$change = '+' . ($change - 100);
				}
				if((float)$price['old_price']==0.00)
				{
					$change = 0.00;
				}
				$tier_1[$sku['sku']][$site]['datetime'] = americanDate($check['date_updated']);
				$tier_1[$sku['sku']][$site]['our_price'] = getOCItemPrice($db->func_query_first_cell("SELECT product_id FROM oc_product WHERE model='".$sku['sku']."'"));
				$tier_1[$sku['sku']][$site]['price'] = $price['price'];
				$tier_1[$sku['sku']][$site]['old_price'] = $price['old_price'];
				$tier_1[$sku['sku']][$site]['change'] = $change;
			}
			
		}
	
}
foreach ($tier_2_skus as $sku) {
	$tier_2_count++;
	//$tier_2_url_count += $db->func_query_first_cell("SELECT count(id) FROM inv_product_price_scrap WHERE sku='" . $sku['sku'] . "' AND url <> ''");
	foreach ($scrapping_sites as $site) {
			$check = $db->func_query_first("SELECT * FROM inv_product_price_scrap WHERE sku='" . $sku['sku'] . "' AND `type`='" . $site . "' AND date_updated BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE()");
			if ($check) {
				$price = $db->func_query_first("select *, (SELECT price from inv_product_price_scrap_history where sku = ph.sku and type = ph.type order by added desc limit 1, 1) as old_price from inv_product_price_scrap_history ph where sku = '" . $sku['sku'] . "' AND type = '$site' order by added DESC limit 1");
				$change = number_format($price['price'] / $price['old_price'] * 100, 2);
				if ($change < 100.00 && $change > 0.00) {
					$change = '-' . (100 - $change);
				} else if ($change == 0.00) {
					$change = 100 - $change;
				} else {
					$change = '+' . ($change - 100);
				}
				if((float)$price['old_price']==0.00)
				{
					$change = 0.00;
				}
				$tier_2[$sku['sku']][$site]['datetime'] = americanDate($check['date_updated']);
				$tier_2[$sku['sku']][$site]['our_price'] = getOCItemPrice($db->func_query_first_cell("SELECT product_id FROM oc_product WHERE model='".$sku['sku']."'"));
				$tier_2[$sku['sku']][$site]['price'] = $price['price'];
				$tier_2[$sku['sku']][$site]['old_price'] = $price['old_price'];
				$tier_2[$sku['sku']][$site]['change'] = $change;
			}
			
		}
	
}
/*foreach ($total_skus as $sku) {
	$sale_60 = $db->func_query_first_cell("SELECT sum(b.product_qty) FROM inv_orders_items b, inv_orders a where a.order_id=b.order_id and b.product_sku='".$sku['sku']."' and lower(a.order_status) not in ('on hold','voided','canceled','cancelled') and a.order_date BETWEEN CURDATE() - INTERVAL 60 DAY AND CURDATE() ");
	if ($sale_60>10) {
		$tier_1_count++;
		$tier_1_url_count += $db->func_query_first_cell("SELECT count(id) FROM inv_product_price_scrap WHERE sku='" . $sku['sku'] . "' AND url <> ''");
		foreach ($scrapping_sites as $site) {
			$check = $db->func_query_first_cell("SELECT url FROM inv_product_price_scrap WHERE sku='" . $sku['sku'] . "' AND `type`='" . $site . "' AND WEEKDAY(date_updated) BETWEEN 0 AND 3 AND WEEK(date_updated) = WEEK(now())");
			//New Logic per day gohar
			//$check = $db->func_query_first("SELECT * FROM inv_product_price_scrap_history WHERE sku='" . $sku['sku'] . "' AND `type`='" . $site . "' AND DATE(added) = DATE(NOW())");
			if ($check) {
				$price = $db->func_query_first("select *, (SELECT price from inv_product_price_scrap_history where sku = ph.sku and type = ph.type order by added desc limit 1, 1) as old_price from inv_product_price_scrap_history ph where sku = '" . $sku['sku'] . "' AND type = '$site' order by added DESC limit 1");
				$change = number_format($price['price'] / $price['old_price'] * 100, 2);
				if ($change < 100.00 && $change > 0.00) {
					$change = '-' . (100 - $change);
				} else if ($change == 0.00) {
					$change = 100 - $change;
				} else {
					$change = '+' . ($change - 100);
				}
				if((float)$price['old_price']==0.00)
				{
					$change = 0.00;
				}
				$tier_1[$sku['sku']][$site]['datetime'] = americanDate($price['added']);
				$tier_1[$sku['sku']][$site]['our_price'] = getOCItemPrice($db->func_query_first_cell("SELECT product_id FROM oc_product WHERE model='".$sku['sku']."'"));
				$tier_1[$sku['sku']][$site]['price'] = $price['price'];
				$tier_1[$sku['sku']][$site]['old_price'] = $price['old_price'];
				$tier_1[$sku['sku']][$site]['change'] = $change;
			}
			
		}
	} else if ($sale_60<10) {
		$tier_2_count++;
		$tier_2_url_count += $db->func_query_first_cell("SELECT count(id) FROM inv_product_price_scrap WHERE sku='" . $sku['sku'] . "' AND url <> ''");
		//$db->func_query("update oc_product SET tier = '2' WHERE sku='" . $sku['sku'] . "'");
		foreach ($scrapping_sites as $site) {
			$check = $db->func_query_first_cell("SELECT * FROM inv_product_price_scrap_history WHERE sku='" . $sku['sku'] . "' AND `type`='" . $site . "' AND WEEKDAY(added) BETWEEN 0 AND 3 AND WEEK(added) = WEEK(now())");
			//New Logic per day gohar
			//$check = $db->func_query_first("SELECT * FROM inv_product_price_scrap_history WHERE sku='" . $sku['sku'] . "' AND `type`='" . $site . "' AND DATE(added) = DATE(NOW())");
			if ($check) {
				$price = $db->func_query_first("select *, (SELECT price from inv_product_price_scrap_history where sku = ph.sku and type = ph.type order by added desc limit 1, 1) as old_price from inv_product_price_scrap_history ph where sku = '" . $sku['sku'] . "' AND type = '$site' order by added DESC limit 1");
				$change = number_format($price['price'] / $price['old_price'] * 100, 2);
				if ($change < 100.00 && $change > 0.00) {
					$change = '-' . (100 - $change);
				} else if ($change == 0.00) {
					$change = 100 - $change;
				} else {
					$change = '+' . ($change - 100);
				}
				if((float)$price['old_price']==0.00)
				{
					$change = 0.00;
				}
				$tier_2[$sku['sku']][$site]['datetime'] = americanDate($price['added']);
				$tier_2[$sku['sku']][$site]['our_price'] = getOCItemPrice($db->func_query_first_cell("SELECT product_id FROM oc_product WHERE model='".$sku['sku']."'"));
				$tier_2[$sku['sku']][$site]['price'] = $price['price'];
				$tier_2[$sku['sku']][$site]['old_price'] = $price['old_price'];
				$tier_2[$sku['sku']][$site]['change'] = $change;
			}
			
		}
	}

 }*/ 
foreach ($last_100 as $key => $data) {
	$price = $db->func_query_first("select *, (SELECT price from inv_product_price_scrap_history where sku = ph.sku and type = ph.type order by added desc limit 1, 1) as old_price from inv_product_price_scrap_history ph where sku = '" . $data['sku'] . "' AND type = '".$data['type']."' order by added DESC limit 1");
	$change = number_format($price['price'] / $price['old_price'] * 100, 2);
				if ($change < 100.00 && $change > 0.00) {
					$change = '-' . (100 - $change);
				} else if ($change == 0.00) {
					$change = 100 - $change;
				} else {
					$change = '+' . ($change - 100);
				}
				if((float)$price['old_price']==0.00)
				{
					$change = 0.00;
				}
	$last_100[$key]['price'] =$price['price'];
	$last_100[$key]['old_price'] =$price['old_price'];
	$last_100[$key]['change'] =$change;
	$last_100[$key]['our_price'] = getOCItemPrice($db->func_query_first_cell("SELECT product_id FROM oc_product WHERE model='".$data['sku']."'"));			
}
//testObject($last_100);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Competitor Dashboard | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap-theme.min.css">
	<script type="text/javascript" src="<?php echo $host_path; ?>/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $host_path; ?>include/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo $host_path ?>/fancybox/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $host_path ?>/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
</head>
<body>
	<div align="center">
		<div align="center"> 
			<?php include_once 'inc/header.php';?>
		</div>
		<?php if ($_SESSION['message']) { ?>
		<div align="center"><br />
			<font color="red">
				<?php
				echo $_SESSION['message'];
				unset($_SESSION['message']);
				?>
				<br />
			</font>
		</div>
		<?php } ?>
		<h3 style="color: #337ab7">Competitor Dashboard</h3><br><br><br>		
		<table align="center" width="100%" >	
		<tr>
			<td align="center">
				<table>
				<tr>
					<td colspan=2><font style="font-size:xx-large;">Total Links</font></td>
				</tr>
				<tr>
					<td align="center" colspan=2><font style="font-size:x-large;"><?php echo $total_url_count; ?></font></td>
				</tr>	
				</table>
			</td>
			<td align="center">
				<table>
					<tr>
						<td colspan=2><font style="font-size:xx-large;">Tier 1 Items</font></td>
					</tr>
					<tr>
						<td align="center">
							<font style="font-size:x-large;"><?php echo $tier_1_count; ?></font><br>
							Products
						</td>
						<td align="center">
							<font style="font-size:x-large;"><?php echo $tier_1_url_count; ?></font><br>
							Total Links
						</td>
					</tr>	
					</table>
			</td>
			<td align="center">
				<table>
					<tr>
						<td colspan=2><font style="font-size:xx-large;">Tier 2 Items</font></td>
					</tr>
					<tr>
						<td align="center">
							<font style="font-size:x-large;"><?php echo $tier_2_count; ?></font><br>
							Products
						</td>
						<td align="center">
							<font style="font-size:x-large;"><?php echo $tier_2_url_count; ?></font><br>
							Total Links
						</td>
					</tr>	
					</table>
			</td>
		</tr>
		</table>
		<br><br><br>
		<table align="center" width="100%" >	
			<tr>
				<td align="center">
					<table>
					<tr>
						<td align="center">
						<font style="font-size:x-large;">30 Days:<?php echo $last_100_30d_count;?> Links</font><br>
						<font style="font-size:x-large;" >7 Days:<?php echo $last_100_7d_count;?> Links</font><br>
						<font style="font-size:x-large;" >24 Hours:<?php echo $last_100_24hr_count;?> Links</font>
						</td>
					</tr>	
					</table>
				</td>
				<td align="center">
					<table>
					<tr>
						<td align="center">
						<font style="font-size:x-large;">30 Days:<?php echo $tier_1_30d_count;?> Links</font><br>
						<font style="font-size:x-large;" >7 Days:<?php echo $tier_1_7d_count;?> Links</font><br>
						<font style="font-size:x-large;" >24 Hours:<?php echo $tier_1_24hr_count;?> Links</font>
						</td>
					</tr>	
					</table>
				</td>
				<td align="center">
					<table>
					<tr>
						<td align="center">
						<font style="font-size:x-large;">30 Days:<?php echo $tier_2_30d_count;?> Links</font><br>
						<font style="font-size:x-large;" >7 Days:<?php echo $tier_2_7d_count;?> Links</font><br>
						<font style="font-size:x-large;" >24 Hours:<?php echo $tier_2_24hr_count;?> Links</font>
						</td>
					</tr>	
					</table>
				</td>
			</tr>
		</table>
		<br><br><br>
		<table align="center" width="102%" >	
			<tr>
				<td align="center">
					<table>
						<tr>
							<td align="center">
								<font style="font-size:x-large;">Last 100 Scraps</font><br>
								<a href="#">View All Scraps</a>
							</td>
						</tr>
						<tr>
							<td>
								<div style="height:400px;width:500px;overflow:auto;">
									<table border="1"  cellpadding="5">
										<thead>
											<tr style="background:#e5e5e5;">
												<th align="center">Date/Time</th>
												<th style="width: 50px;" align="center">Sku</th>
												<th align="center">Item Name</th>
												<th align="center">Competitor</th>
												<th align="center">Sale Price</th>
												<th align="center">Old Price</th>
												<th align="center">New Price</th>
												<th align="center">Change %</th>
											</tr>									
										</thead>
										<tbody>

											<?php foreach ($last_100 as $key => $data) { 
												if($data['http_code']=='404')  { $data['fixer'] = 1;?>
													<tr id="row_<?php echo $key;?>_last100" bgcolor="#FF6347">
												<?php } else if($data['price']=='0.00'){ $data['fixer'] = 1;?>
													<tr id="row_<?php echo $key;?>_last100" bgcolor="#FFFFB0">
												<?php } else { $data['fixer'] = 0;?>
													<tr id="row_<?php echo $key;?>_last100">
												<?php } ?>
												
													<td><?php echo americanDate($data['date_updated']); ?></td>
													<td><?php echo linkToProduct($data['sku'],'','target="_blank"'); ?>
														<?php if($data['fixer']==1){ ?><br>
														<input type="checkbox" id="<?php echo $key; ?>_last100" onclick="fixLink(<?php echo $key; ?>,'last100');">
														<span style="display: none;" id="<?php echo $key; ?>_last100_fixed">&#10004; Link Fixed</span>
														<?php } ?>
													</td>
													<td><?php echo getItemName($data['sku']); ?></td>
													<td><?php echo $data['type']; ?></td>
													<td><?php echo $data['our_price']; ?></td>
													<td><?php echo $data['old_price']; ?></td>
													<td><?php echo $data['price']; ?></td>
													<td><?php echo $data['change']; ?>%</td>
												</tr>

												<?php	
											} ?>
										</tbody>
									</table>
								</div>
							</td>
						</tr>	
					</table>
				</td>
				<td align="center">
					<table>
						<tr>
						<td align="center">
						<font style="font-size:x-large;">Tier 1 Price Changes</font><br>
						<a href="#">View All Scraps</a>
						</td>
					</tr>
					<tr>
						<td>
						<div style="height:400px;width:500px;overflow:auto;">
							<table border="1"  cellpadding="5">
								<thead>
								<tr style="background:#e5e5e5;">
									<th align="center">Date/Time</th>
									<th style="width: 50px;" align="center">Sku</th>
									<th align="center">Item Name</th>
									<th align="center">Competitor</th>
									<th align="center">Sale Price</th>
									<th align="center">Old Price</th>
									<th align="center">New Price</th>
									<th align="center">Change %</th>
								</tr>									
								</thead>
								<tbody>
									<?php $i=0; foreach ($tier_1 as $sku => $t1_comp) {
										foreach ($t1_comp as $competitor => $data) { 
											if($data['http_code']=='404')  { $data['fixer'] = 1;?>
													<tr id="row_<?php echo $i;?>_tier1" bgcolor="#FF6347">
												<?php } else if($data['price']=='0.00'){ $data['fixer'] = 1;?>
													<tr id="row_<?php echo $i;?>_tier1" bgcolor="#FFFFB0">
												<?php } else { $data['fixer'] = 0;?>
													<tr id="row_<?php echo $i;?>_tier1">
												<?php } ?>
												<td><?php echo $data['datetime']; ?></td>
												<td><?php echo linkToProduct($sku,'','target="_blank"'); ?>
													<?php if($data['fixer']==1){ ?><br>
														<input type="checkbox" id="<?php echo $i; ?>_tier1" onclick="fixLink(<?php echo $i; ?>,'tier1');">
														<span style="display: none;" id="<?php echo $i; ?>_tier1_fixed">&#10004; Link Fixed</span>
														<?php } ?>
												</td>
												<td><?php echo getItemName($sku); ?></td>
												<td><?php echo $competitor; ?></td>
												<td><?php echo $data['our_price']; ?></td>
												<td><?php echo $data['old_price']; ?></td>
												<td><?php echo $data['price']; ?></td>
												<td><?php echo $data['change']; ?>%</td>
											</tr>
								
								<?php	$i++;}
									} ?>
								</tbody>
							</table>
						</div>
						</td>
					</tr>	
					</table>
				</td>
				<td align="center">
					<table>
					<tr>
						<td align="center">
							<font style="font-size:x-large;">Tier 2 Price Changes</font><br>
							<a href="#">View All Scraps</a>
						</td>
					</tr>
					<tr>
						<td>
							<div style="height:400px;width:500px;overflow:auto;">
								<table border="1" cellpadding="5">
									<thead>
										<tr style="background:#e5e5e5;">
											<th align="center">Date/Time</th>
											<th style="width: 50px;" align="center">Sku</th>
											<th align="center">Item Name</th>
											<th align="center">Competitor</th>
											<th align="center">Sale Price</th>
											<th align="center">Old Price</th>
											<th align="center">New Price</th>
											<th align="center">Change %</th>
										</tr>									
									</thead>
									<tbody>
									<?php $i=0; foreach ($tier_2 as $sku => $t2_comp) {
											foreach ($t2_comp as $competitor => $data) { 
												if($data['http_code']=='404')  { $data['fixer'] = 1;?>
													<tr id="row_<?php echo $i;?>_tier2" bgcolor="#FF6347">
												<?php } else if($data['price']=='0.00'){ $data['fixer'] = 1;?>
													<tr id="row_<?php echo $i;?>_tier2" bgcolor="#FFFFB0">
												<?php } else { $data['fixer'] = 0;?>
													<tr id="row_<?php echo $i;?>_tier2">
												<?php } ?>
												<td><?php echo $data['datetime']; ?></td>
												<td><?php echo linkToProduct($sku,'','target="_blank"'); ?>
													<?php if($data['fixer']==1){ ?><br>
														<input type="checkbox" id="<?php echo $i; ?>_tier2" onclick="fixLink(<?php echo $i; ?>,'tier2');">
														<span style="display: none;" id="<?php echo $i; ?>_tier2_fixed">&#10004; Link Fixed</span>
														<?php } ?>
												</td>
												<td><?php echo getItemName($sku); ?></td>
												<td><?php echo $competitor; ?></td>
												<td><?php echo $data['our_price']; ?></td>
												<td><?php echo $data['old_price']; ?></td>
												<td><?php echo $data['price']; ?></td>
												<td><?php echo $data['change']; ?>%</td>
											</tr>

											<?php	$i++;}
										} ?>
									</tbody>
								</table>
							</div>
						</td>
					</tr>	
				</table>
			</td>
		</tr>
		</table>
	</div>
	<script type="text/javascript">
		function fixLink(key,table){
			 $('#'+key+'_'+table).hide();
			 $('#'+key+'_'+table+'_fixed').show();
			 $('#row_'+key+'_'+table).css("background-color", "#ffffff");
		}
	</script>
</body>
