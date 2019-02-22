<?php
require_once("auth.php");
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';
function exportSubImages()
{
	global $db;
	
	$datam = $db->func_query("SELECT * FROM oc_product WHERE status=1");;
	$i=1;
	
	$dataArray = array();
	$dataArray[0][] = 'Product ID';
	$dataArray[0][] = 'SKU';
	$dataArray[0][] = 'Main Image';
	$dataArray[0][] = 'Additional Image 1';
	$dataArray[0][] = 'Additional Image 2';
	$dataArray[0][] = 'Additional Image 3';
	$dataArray[0][] = 'Additional Image 4';
	$dataArray[0][] = 'Additional Image 5';
	$dataArray[0][] = 'Additional Image 6';
	
	foreach($datam as $data)
	{
		$images = $db->func_query("SELECT * FROM oc_product_image WHERE product_id='".$data['product_id']."'");
		//if(count($images)>0)
		//{
		$dataArray[$i][] = $data['product_id'];
		$dataArray[$i][] = $data['sku'];
		$dataArray[$i][] = $data['image'];
		foreach($images as $image)
		{
			$dataArray[$i][] = $image['image'];
		}
		$i++;
		//}
	}
	
	header("Content-Type: text/csv");
	header("Content-Disposition: attachment; filename=ProductSubImages-".date('d-m-Y').".csv");
// Disable caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies
//$headings = array('SKU','Product Title','Qty');
$output = fopen("php://output", "w");
foreach($dataArray as $row)
{
fputcsv($output, $row,','); // here you can change delimiter/enclosure
}
fclose($output);
}
if($_GET['action']=='export_sub_images')
{
	exportSubImages();
	exit;	
}
if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}
if($page < 1){
	$page = 1;
}
$max_page_links = 10;
$num_rows = 200;
$start = ($page - 1)*$num_rows;
$keyword = @$_GET['keyword'];
if($keyword){
	$keyword = $db->func_escape_string(trim($keyword));
	$where = " Where ( Lower(pd.name) like Lower('%$keyword%') OR Lower(p.sku) like Lower('%$keyword%') ) ";
	$parameters[] = "keyword=$keyword";
}
else{
	$where = " Where p.sku != '' and p.is_main_sku = 1";
	$parameters[] = "";
}
$filter_date_added = @$_GET['filter_date_added'];
if($filter_date_added){
	$filter_date_added = $db->func_escape_string($filter_date_added);
	$where .= " and date(date_added)='$filter_date_added' ";
	$parameters[] = "filter_date_added=$filter_date_added";
}
$filter_qty = $_GET['filter_qty'];
if($filter_qty){
	$filter_qty = $db->func_escape_string(trim($filter_qty));
	$where .= " and quantity='$filter_qty' ";
	$parameters[] = "filter_qty=$filter_qty";
}
if($_GET['has_images'])
{
	if($_GET['has_images']=='yes')
	{
		$where .= " and p.image!='' ";
	}
	elseif($_GET['has_images']=='no')
	{
		$where .= " and p.image='' ";
	}
	else
	{
		$where .= "";
	}
	
	$parameters[] = "has_images=".$_GET['has_images'];
}
$where_z='';
if(isset($_GET['status']))
{
	if($_GET['status']=='1')
	{
		$where_z .= " and p.status='1' ";
	}
	elseif($_GET['status']=='0')
	{
		$where_z .= " and p.status='0' ";
	}
	else
	{
		$where_z .= "";
	}
	
	$parameters[] = "status=".$_GET['status'];
}
if(isset($_GET['is_categorized']))
{
	if($_GET['is_categorized']=='1')
	{
		$where_z .= " and pc.category_id>'0' ";
	}
	elseif($_GET['is_categorized']=='0')
	{
		$where_z .= " and pc.category_id is null ";
	}
	else
	{
		$where_z .= "";
	}
	
	$parameters[] = "is_categorized=".$_GET['is_categorized'];
}
$is_classified='default';
if(isset($_GET['is_classified']))
{
	if($_GET['is_classified']=='1')
	{
		$where_z .= " and p.classification_id>'0' ";
		$is_classified ='true' ;
	}
	elseif($_GET['is_classified']=='0')
	{
		$where_z .= " and p.classification_id < 1 ";
		$is_classified = 'false';
	}
	else
	{
		$is_classified = 'default';
		//$where_z .= "";
	}
	
	$parameters[] = "is_classified=".$_GET['is_classified'];
}
$dir = @$_GET['dir'];
if(!$dir || !in_array($dir,array("asc","desc"))){
	$dir = 'asc';
}
//$parameters[] = "dir=$dir";
$sort = @$_GET['sort'];
if(!$sort || !in_array($sort,array("date_added","quantity","product_id","sku","name","cost_date","current_cost","prev_cost",'status','category_id'))){
	$sort = 'sku';
}
//$parameters[] = "sort=$sort";
if(in_array($sort,array("product_id","sku","date_added","quantity","status"))){
	$dsort = "p.$sort";
}
elseif(in_array($sort,array("name"))){
	$dsort = "pd.$sort";
}
elseif(in_array($sort,array('category_id')))
{
	$dsort = "pc.$sort";
}
else{
	$dsort = "p.$sort";
}
/*$_query = "Select p.sku, p.weight, p.quantity, p.price ,  pd.name , pc.raw_cost , pc.prev_cost , pc.cost_date, pc.ex_rate , pc.shipping_fee 
		   from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id)
		   left join (select * from inv_product_costs order by cost_date DESC) pc on (p.sku = pc.sku) 
		   $where group by p.sku order by $dsort $dir";*/
		   $_query = "Select p.sku,p.status, p.weight, p.quantity, p.price,p.date_added ,  pd.name,p.product_id,pc.category_id,0 as device_product_id
		   from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id)
		   LEFT JOIN oc_product_to_category pc
		   on (p.product_id=pc.product_id)
		   
		   
		   $where $where_z group by p.sku order by $dsort $dir";
		   // echo $_query;
		   $splitPage = new splitPageResults($db , $_query , 100 , "products_beta.php",$page);
		   
		   $_cache = md5(http_build_query($_GET));
		   $products = $cache->get('products.'.$page.'.'.$_cache);
		   if (!$products) {
		   	$products = $db->func_query($splitPage->sql_query);
		   	$cache->set('products.'.$page.'.'.$_cache,$products);
		   }
		   
		   if($dir == 'asc'){
		   	$ddir = 'desc';
		   }
		   else{
		   	$ddir = 'asc';
		   }
		   if($parameters){
		   	$parameters = implode("&",$parameters);
		   }
		   else{
		   	$parameters = '';
		   }
 //echo $_query;
		   foreach($products as $i => $product)
		   {
		   	$classified_check =  $cache->get('product.'.$product['sku'].'.classified_check');
		   	if (!$classified_check) {		   		
		   		$classified_check = $db->func_query_first_cell("SELECT a.device_product_id FROM inv_device_product a,inv_device_class b WHERE a.device_product_id=b.device_product_id and a.sku='".$product['sku']."'");
		   		$cache->set('product.'.$product['sku'].'.classified_check',$classified_check);
		   	}

		   	if($is_classified=='false')
		   	{
		   		if($classified_check)
		   		{
		   		continue;
		   		}
		   	}
		   	else if($is_classified=='true')
		   	{
		   		if(!$classified_check)
		   		{
		   			
		   		continue;
		   		}
		   	}
		   	$products[$i]['sku'] = $product['sku'];
		   	$products[$i]['status'] = $product['status'];
		   	$products[$i]['weight'] = $product['weight'];
		   	$products[$i]['quantity'] = $product['quantity'];
		   	$products[$i]['price'] = $product['price'];
		   	$products[$i]['date_added'] = $product['date_added'];
		   	$products[$i]['name'] = $product['name'];
		   	$products[$i]['product_id'] = $product['product_id'];
		   	$products[$i]['product_id'] = $product['product_id'];
		   	$products[$i]['category_id'] = $product['category_id'];
		   	$products[$i]['device_product_id'] = $classified_check;
		   }
		   if($_GET['sort']=='device_product_id'){
		   	$sort_order = array();
		   	foreach ($products as $key => $value) {
		   		$sort_order[$key] = $value['device_product_id'];
		   	}
		   	if($dir=='asc')
		   	{
		   	array_multisort($sort_order, SORT_ASC, $products);
		   	}
		   	else
		   	{
		   	array_multisort($sort_order, SORT_DESC, $products);
		   		
		   	}
		   }
		   
		   ?>
		   <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		   <html xmlns="http://www.w3.org/1999/xhtml">
		   <head>
		   	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		   	<title>Products</title>
		   	<style type="text/css">
		   		table td{text-align:center;}
		   	</style>
		   	<script type="text/javascript" src="js/jquery.min.js"></script>
		   	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
		   	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
		   </head>
		   <body>
		   	<div align="center"> 
		   		<?php include_once 'inc/header.php';?>
		   	</div>
		   	<?php if($_SESSION['message']):?>
		   		<div align="center"><br />
		   			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		   		</div>
		   	<?php endif;?>
		   	<h3 align="center">
		   		<!-- <a class="button" href="<?php echo $host_path;?>export_products.php">Export Products</a> -->
		   		<!-- <a class="button" href="<?php echo $host_path;?>restock_requests.php">Restock Requests</a> -->
		   		<a class="button" href="<?php echo $host_path;?>sync_shopify.php">Sync Shopify Products</a>
		   		<a class="button fancybox" href="#export_products_div">Export Products</a>
		   		<a class="button" href="<?php echo $host_path;?>export_product_weights.php">Export Product Weights</a>
		   		<a class="button" href="<?php echo $host_path;?>products.php?action=export_sub_images">Export Images</a>
		   		<?php if($_SESSION['import_products']):?>
		   			<a class="button fancybox fancybox.iframe" href="<?php echo $host_path;?>popupfiles/import_products.php">Import Products</a>
		   			<a class="button fancybox fancybox.iframe" href="<?php echo $host_path;?>popupfiles/import_images.php">Import Images</a>
		   		<?php endif;?>
		   		<?php if($_SESSION['login_as']=='admin')
		   		{
		   			?>
		   			<a class="button fancybox fancybox.iframe" href="<?php echo $host_path;?>popupfiles/disable_products.php">Disable Products</a>
		   			<a class="button fancybox fancybox.iframe" href="<?php echo $host_path;?>popupfiles/update_qty.php">Update Qty</a>
		   			<a class="button fancybox fancybox.iframe" href="<?php echo $host_path;?>popupfiles/import_tags.php">Import Tags</a>
		   			<a class="button fancybox fancybox.iframe" href="<?php echo $host_path;?>popupfiles/update_sale_price.php">Update Sale Prices</a>
		   			<?php
		   		}	
		   		?>		
		   	</h3>
		   	<br />
		   	<div id="export_products_div" style="display:none;text-align:center">
		   	<h1 style="font-size:17px">Export Products</h1>
		   	<form id="export_form" method="POST" action="export_products.php">
		   	<?php
		   	$total_chunk_records =  $cache->get('product.total_chunk_records');
		   	if (!$total_chunk_records) {		   		
		   		$total_chunk_records = $db->func_query_first_cell("Select COUNT(*)
		 			  from oc_product 
		    			where is_main_sku = 1");
		   		$cache->set('product.total_chunk_records',$total_chunk_records);
		   	}


		   	
		    $chunks = ceil($total_chunk_records/2000);
		   	?>
		   	<table width="100%" cellpadding="5" cellspacing="5">
		   	<tr>
		   		<td > <strong>Export Name</strong> <small style="font-size:8px;color:red">(Optional)</small>: </td><td ><input type="text" style="width:300px" name="file_name" placeholder="Provide the name of file with no special character(s)"></td>
		   	</tr>
		   	<tr>
		   		<td><strong>Chunk #</strong> </td> <td align="left" style="text-align:left">
		   			<select name="chunk" style="width:100px">
		   			<?php
		   			for($i=1;$i<=$chunks;$i++)
		   			{
		   				?>
		   			<option><?php echo $i;?></option>
		   				<?php
		   			}
		   			?>
		   			</select>
		   			<br>
		   			<small style="font-size:9px;color:red">Chunks are generated depending on system data.</small>
		   		</td>
		   	</tr>
		   	<tr>
		   	<td colspan="2">&nbsp;</td>
		   	</tr>
		   	<tr>
		   	<td colspan="2" align="center"><input type="button" onclick="exportProducts(this);" value="Process Now" class="button" style="padding: 5px; 15px; font-size: 12px;"></td>
		   	</tr>
		   	</table>
		   	</form>
		   	</div>
		   	<div class="search" align="center">
		   		<form>
		   			<b>Keyword:</b> 
		   			<input type="text" name="keyword" value="<?php echo $keyword;?>"  /> <br /><br />
		   			<b>Date Added:</b> 
		   			<input style="width:120px" type="text" name="filter_date_added"  name="filter_date_added" value="<?php echo $_REQUEST['filter_date_added'];?>" class="datepicker" readOnly  /> <b?Qty</form> <input type="text" name="filter_qty"  name="filter_qty" value="<?php echo $_REQUEST['filter_qty'];?>" size="4"  />  <br /><br />
		   			<b>Classified</b> <input type="radio" <?php if($_GET['is_classified']=='1'){?> checked="checked" <?php } ?> name="is_classified" value="1" /> Yes <input type="radio"  name="is_classified" <?php if($_GET['is_classified']=='0'){?> checked="checked" <?php } ?> value="0" /> No <input type="radio"  name="is_classified" value="default" <?php if(!isset($_GET['is_classified']) or $_GET['is_classified']=='default'){?> checked="checked" <?php } ?> /> Default <br /><br />
		   			<b>Categorized</b> <input type="radio" <?php if($_GET['is_categorized']=='1'){?> checked="checked" <?php } ?> name="is_categorized" value="1" /> Yes <input type="radio"  name="is_categorized" <?php if($_GET['is_categorized']=='0'){?> checked="checked" <?php } ?> value="0" /> No <input type="radio"  name="is_categorized" value="default" <?php if(!isset($_GET['is_categorized']) or $_GET['is_categorized']=='default'){?> checked="checked" <?php } ?> /> Default <br /><br />
		   			<b>Has Images?</b> <input type="radio" <?php if($_GET['has_images']=='yes'){?> checked="checked" <?php } ?> name="has_images" value="yes" /> Yes <input type="radio"  name="has_images" <?php if($_GET['has_images']=='no'){?> checked="checked" <?php } ?> value="no" /> No <input type="radio"  name="has_images" value="default" <?php if(!isset($_GET['has_images']) or $_GET['has_images']=='default'){?> checked="checked" <?php } ?> /> Default <br /><br />
		   			<b>Status</b> <input type="radio" <?php if( $_GET['status']=='1'){?> checked="checked" <?php } ?> name="status" value="1" /> Enabled <input type="radio"  name="status" <?php if($_GET['status']=='0'){?> checked="checked" <?php } ?> value="0" /> Disabled <input type="radio"  name="status" value="default" <?php if(!isset($_GET['status']) or $_GET['status']=='default'){?> checked="checked" <?php } ?> /> Default <br /><br /> 
		   			<input type="submit" name="Go" class="button" value="Search" />
		   		</form>
		   	</div>
		   	<br />
		   	<table class="footer" border="1" style="border-collapse:collapse;" width="80%" align="center" cellpadding="3">
		   		<tr style="background:#e5e5e5;">
		   			<th><a href="products.php?sort=date_added&dir=<?php echo $ddir;?>&<?=$parameters;?>&page=1">Date Created</a></th>
		   			<th><a href="products.php?sort=product_id&dir=<?php echo $ddir;?>&<?=$parameters;?>&page=1">Product ID</a></th>
		   			<th><a href="products.php?sort=sku&dir=<?php echo $ddir;?>&<?=$parameters;?>&page=1">SKU</a></th>
		   			<th width="350px"><a href="products.php?sort=name&dir=<?php echo $ddir;?>&<?=$parameters;?>&page=1">Item Name</a></th>
		   			<th><a href="products.php?sort=quantity&dir=<?php echo $ddir;?>&<?=$parameters;?>&page=1">Qty</a></th>
		   			<!-- <th># Issues</th> -->
		   			<?php if($_SESSION['display_cost']):?>	
		   				<th>Cost</th>
		   			<?php endif;?> 
		   			<th><a href="products.php?sort=status&dir=<?php echo $ddir;?>&<?=$parameters;?>&page=1">Status</a></th>
		   			<th><a href="products.php?sort=device_product_id&dir=<?php echo $ddir;?>&<?=$parameters;?>&page=1">Classified?</a></th>
		   			<th><a href="products.php?sort=category_id&dir=<?php echo $ddir;?>&<?=$parameters;?>&page=1">Categorized?</a></th>
		   			<th>P1 Price</th>
		   		</tr>
		   		<?php foreach($products as $product):
		   		// $classified_check = $db->func_query_first_cell("SELECT a.device_product_id FROM inv_device_product a,inv_device_class b WHERE a.device_product_id=b.device_product_id and a.sku='".$product['sku']."'");
		   		// $categorized_check = $db->func_query_first_cell("SELECT category_id FROM oc_product_to_category WHERE product_id='".$product['product_id']."'");
		   		$d1_price =  $cache->get('product.'.$product['product_id'].'.d1_price');
		   		if (!$d1_price) {		   		
		   			$d1_price = $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=1633 AND product_id='" . (int) $product['product_id'] . "' AND quantity=1");
		   			$cache->set('product.'.$product['product_id'].'.d1_price',$d1_price);
		   		}

		   		
		   		
		   		?>
		   		<tr>
		   			<td><?php echo americanDate($product['date_added']);?></td>
		   			<td><?php echo $product['product_id'];?></td>
		   			<td><a href="product/<?php echo $product['sku']; ?>"><?php echo $product['sku']; ?></a></td>
		   			<td><?php echo utf8_encode($product['name']);?></td>
		   			<td><?php echo $product['quantity'];?></td>
				<!--    <td>
                        <?php //$issue_count = $db->func_query_first_cell("select count(id) from inv_product_issues where product_sku = '".$product['sku']."'")?>
                        <a class="fancybox3 fancybox.iframe" href="popupfiles/product_issues.php?product_sku=<?php echo $product['sku'];?>"><?php echo $issue_count?> of item issues</a>
                    </td>-->
                    <?php if($_SESSION['display_cost']):?>
                    	<?php
                    	$true_cost = getTrueCost($product['sku']);
                    	?>	
                    	<td>$<?php echo number_format($true_cost,2);?></td>
                    <?php endif;?>
                    <td><?=($product['status']==1?'X':'');?></td>
                    <td><?=($product['device_product_id']?'X':'');?></td>
                    <td><?=($product['category_id']?'X':'');?></td>
                    <td><?='$'.number_format($d1_price,2);?></td>
                </tr>
            <?php endforeach;?>
            <tr>
            	<td colspan="3" align="left">
            		<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
            	</td>
            	<td colspan="7" align="right">
            		<?php
            		$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
            		?>
            		<?php echo $splitPage->display_links(10,$parameters);?>
            	</td>
            </tr>
        </table>
        <br />       
    </body>
    </html>
    <script>
    function exportProducts(obj)
    {
    	// alert(1);
    	// if(!confirm(''));
    	// $(obj).val('Processing...');
    	// $(obj).attr('disabled','disabled');
    	$('#export_form').submit();
    }
    </script>