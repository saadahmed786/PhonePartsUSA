<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$permission = 'aging_report';
$pageName = 'Aging Report';
$pageLink = 'aging_report.php';
$pageCreateLink = false; //'chargeback_create.php';
$pageSetting = false; //'chargeback_settings.php';
$delete = false;
$search = false;
$table = '`oc_product`';
page_permission($permission);
$products = array();
$is_ordered = 0;
$date_start = '';
$date_end = '';
$where = '';



if($_GET['search'])
{
	
	if($_GET['sku_group']!='')
	{
		$where.=' and sku like "'.$db->func_escape_string($_GET['sku_group']).'%"';	
		
	}
	
	
	
/*	$is_ordered = ($_GET['is_ordered']?1:0);
	if($_GET['date_start'] and $_GET['date_end'])
	{
		$date_start = $_GET['date_start'];
		$date_end = $_GET['date_end'];
		if($is_ordered ==1)
		{
		$where.= " and last_ordered BETWEEN '".date('Y-m-d h:i:s',strtotime($date_start))."' AND '".date('Y-m-d h:i:s',strtotime($date_end))."' ";
		}
		else
		{
			$where.= " and last_ordered NOT BETWEEN '".date('Y-m-d h:i:s',strtotime($date_start))."' AND '".date('Y-m-d h:i:s',strtotime($date_end))."' ";
			
		}
		
	}*/
	if($_GET['last_ordered_date'])
	{
		$last_ordered_date = date('Y-m-d', strtotime('-'.(int)$_GET['last_ordered_date'].' days'));
		$where.=" and DATE(last_ordered)<='".$last_ordered_date."'";
	}
//Writing query 
$inv_query = 'SELECT distinct
product_id,sku,last_ordered as last_order,price,quantity
FROM
oc_product
where
sku != "" and sku!="SIGN"
AND sku NOT IN (SELECT 
	kit_sku
	FROM
	inv_kit_skus)
	and sku like "'.$db->func_escape_string($_GET['sku_group']).'%"
and status = 1 AND quantity>0 '.$where.'
ORDER BY sku ASC
';

$products = $db->func_query($inv_query);
}

//Using Split Page Class to make pagination
//$splitPage = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);

//Getting All Messages
//$products = $db->func_query($splitPage->sql_query);
$sku_groups = $db->func_query("SELECT DISTINCT UPPER(LEFT(sku,7)) AS sku_group FROM oc_product WHERE sku!='' ORDER BY sku ");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	
	<script type="text/javascript" src="js/jquery.min.js"></script>

	<link href="include/table_sorter.css" rel="stylesheet" type="text/css" />
	

	
	
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
		<h2>Manage <?= $pageName; ?>s</h2>
        <?php //echo $inv_query;?>
		 <form action="" method="get"> 
		<table cellpadding="5" width="40%" border="1">
			<tr>
            <td  >
            <strong>SKU Group:</strong> <select name="sku_group" >
            <option value="">All SKU's</option>
            <?php
			foreach($sku_groups as $sku_group)
			{
				?>
                <option value="<?php echo $sku_group['sku_group'];?>" <?php if($_GET['sku_group']==$sku_group['sku_group']) echo 'selected';?>><?php echo $sku_group['sku_group'];?></option>
                <?php
				
			}
			?>
            </select>
            </td>
            
            </tr>
            <tr style="display:none">
            <td  ><strong>Date Start:</strong> <input type="date" name="date_start" value="<?php echo $_GET['date_start'];?>" /> <strong>Date End:</strong> <input type="date" name="date_end" value="<?php echo $_GET['date_end'];?>" /> 
            </tr>
             <tr style="display:none">
            <td  ><input type="checkbox" name="is_ordered" <?php if($is_ordered) echo 'checked';?> /> Uncheck the box to get the un-ordered items within the range.
            </tr>
            
            <tr style="">
            <td  >Last Ordered More Than: <select name="last_ordered_date">
            <option value="">Please Select</option>
            <option value="7" <?php if($_GET['last_ordered_date']=='7') echo 'selected';?>>7 Days</option>
            <option value="14" <?php if($_GET['last_ordered_date']=='14') echo 'selected';?>>14 Days</option>
            <option value="30" <?php if($_GET['last_ordered_date']=='30') echo 'selected';?>>30 Days</option>
            <option value="60" <?php if($_GET['last_ordered_date']=='60') echo 'selected';?>>60 Days</option>
            <option value="90" <?php if($_GET['last_ordered_date']=='90') echo 'selected';?>>90 Days</option>
            
            </select></td>
            </tr>
            <tr>
            <td  align="center"><input type="submit" name="search" class="button" value="Search" />
            </tr>
		</table>
        
        <br /><br />
		</form>
		<?php if ($pageCreateLink) { ?>
		<p><a href="<?php echo $host_path . $pageCreateLink; ?>">Add <?= $pageName; ?></a></p>
		<?php } ?>
		<table width="90%" cellpadding="10" border="1"  align="center" class="tablesorter">
			<thead>
				<tr style="background:#e5e5e5;">
					<th width="3%">#</th>
					<th width="10%">Last Order Date</th>
					<th width="10%">SKU</th>
					<th width="40%">Name</th>
                    <th width="7%">Qty</th>
					<th width="5%">Price</th>
					<th width="5%">Ebay</th>
					<th width="5%">Ebay2</th>
					<th width="5%">Mengtor</th>

				</tr>
			</thead>
			<tbody>
				<!-- Showing All REcord -->
				<?php /*foreach ($products as $i => $product) { ?>
				<?php
				$query = 'SELECT 
				`dateofmodification`
				FROM
				`inv_orders_items`
				WHERE
				`product_sku` = "'. $product['sku'] .'"
				ORDER BY `dateofmodification` DESC
				LIMIT 0 , 1';
				$product['last_order'] = $db->func_query_first_cell($query);
				//$product['last_order'] = '';
				$query = 'SELECT 
				`price`
				FROM
				`inv_product_scrape_prices`
				WHERE
				`sku` = "'. $product['sku'] .'"
				AND `scrape_site` = "ebay"';
				$product['ebayPrice'] = $db->func_query_first_cell($query);
				?>
				<tr>
					<td><?= ($i) + 1; ?></td>
					<td class="date"><?= americanDate($product['last_order']); ?></td>
					<td><?= $product['sku']; ?></td>
					<!--<td><?= $product['name']; ?></td>-->
					<td>$<?= number_format($product['price'], 2); ?></td>
					<td>$<?= ($product['ebayPrice'])? number_format($product['ebayPrice'], 2): 'N/A'; ?></td>
					<td> </td>
					<td> </td>
				</tr>
				<?php }*/ ?>
                
                <?php
				if($products)
				{
				foreach ($products as $i => $product) { ?>
				<?php
				
				//$product['last_order'] = '';
				$query = 'SELECT 
				`price`, `url`
				FROM
				`inv_product_scrape_prices`
				WHERE
				`sku` = "'. $product['sku'] .'"
				AND `scrape_site` = "ebay"';
				$product['ebay'] = $db->func_query_first($query);
				
				$query = 'SELECT 
				`price`, `url`
				FROM
				`inv_product_scrape_prices`
				WHERE
				`sku` = "'. $product['sku'] .'"
				AND `scrape_site` = "ebay_2"';
				$product['ebay2'] = $db->func_query_first($query);
				
				
				$query = 'SELECT 
				`price`, `url`
				FROM
				`inv_product_scrape_prices`
				WHERE
				`sku` = "'. $product['sku'] .'"
				AND `scrape_site` = "mengtor"';
				$product['mengtor'] = $db->func_query_first($query);
				
				
				$default_qty = $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE product_id='".$product['product_id']."'");
				if($default_qty)
				{
					
					$product['price'] = $default_qty;	
				}
				
				?>
				<tr>
					<td><?= ($i) + 1; ?></td>
					<td class="date"><?= americanDate($product['last_order']); ?></td>
					<td><?= linkToProduct($product['sku'],$host_path); ?></td>
					<td align="left"><?= getItemName($product['sku']); ?></td>
					  <td><?=$product['quantity'];?></td>
                    <td>$<?= number_format($product['price'], 2); ?></td>
                  
					<td><?= ($product['ebay'])?'<a href="'. $product['ebay']['url'] .'">$'.number_format($product['ebay']['price'], 2) . '</a>': 'N/A'; ?></td>
					<td><?= ($product['ebay2'])?'<a href="'. $product['ebay2']['url'] .'">$'.number_format($product['ebay2']['price'], 2) . '</a>': 'N/A'; ?></td>
					<td><?= ($product['mengtor'])?'<a href="'. $product['mengtor']['url'] .'">$'.number_format($product['mengtor']['price'], 2) . '</a>': 'N/A'; ?></td>
				</tr>
				<?php
                }	
					
				}
				else
				{
				?>
                <tr>
                <td colspan="9" align="center">Please select the sku group first to show the records</td>
                </tr>
                <?php	
					
				}
				?>
			</tbody>
		</table>

		<br /><br />
		<table class="footer" border="0" style="border-collapse:collapse;" width="95%" align="center" cellpadding="3">
			<tr>
				<td colspan="7" align="left">
					<?php //echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
				</td>

				<td colspan="6" align="right">
					<?php //echo $splitPage->display_links(10,$parameters);?>
				</td>
			</tr>
		</table>
		<br />
	</div>
</body>
<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
<script>
	$(document).ready(function(e) {
		$(".tablesorter").tablesorter(); 
	});
	function searchTable () {
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		if (start_date != '') {
			start_date = start_date.split('/');
			start_date = start_date[2] + start_date[0] + start_date[1];
		}
		if (end_date != '') {
			end_date = end_date.split('/');
			end_date = end_date[2] + end_date[0] + end_date[1];
		}
		$('.tablesorter tbody tr').show();
		var ordered = $('#notord').is(':checked');
		console.log(ordered);
		if (end_date != '' && start_date != '') {
			$('.tablesorter tbody').find('tr').each(function () {
				var holder = $(this).find('.date');
				var date = holder.text().split(' ')[0].split('/');
				date = date[2] + date[0] + date[1];

				if (ordered) {
					if (date < start_date || end_date < date) {
						$(this).hide();
					}
					console.log('Not ordered');
				} else {
					if (date > start_date || end_date > date) {
						$(this).hide();
					}
					console.log('Ordered');
				}
				
			});
		}
	}
</script>