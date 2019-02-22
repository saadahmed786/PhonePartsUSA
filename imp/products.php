<?php
require_once("auth.php");
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';
unset($_SESSION['cart']);
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
if($_POST['action']=='bulk_csv_upload')
{
	// echo 'here';exit;
	if( $_FILES['bulk_csv']['tmp_name']){
	$csv_mimetypes = array(
        'text/csv',
        'text/plain',
        'application/csv',
        'text/comma-separated-values',
        'application/excel',
        'application/vnd.ms-excel',
        'application/vnd.msexcel',
        'text/anytext',
        'application/octet-stream',
        'application/txt',
	);

	$type = $_FILES['bulk_csv']['type'];
	// echo $type;exit;
	if(in_array($type,$csv_mimetypes)){
		$filename = $_FILES['bulk_csv']['tmp_name'];
		$handle   = fopen("$filename", "r");
		$k=0;
		while ($data = fgetcsv($handle,1000,",","'")) {
		if($k==0)
		{
			$k++;
			continue;
		}
		// echo $data[0].'--'.$data[1]."<br>";exit;
        if (trim($data[0])!='' && $data[1]!='') {

          //$db->db_exec("UPDATE oc_product SET quantity='".(int)$data[1]."',date_modified='".date("Y-m-d H:i:s")."' WHERE trim(lower(model))='".$db->func_escape_string(trim(strtolower($data[0])))."' or sku='".$db->func_escape_string(trim(strtolower($data[0])))."'");

        	$is_updated = updateOnShelfQty($data[0],$data[1]);
          if($data[2]!='')
          {
          	$notes = $data[2];
          }
          else
          {
          	$notes = '';
          }
          	$comment = 'Bulk Cycle Count';
          
          	if($is_updated)
          	{
          makeLedger('',array($data[0]=>(int)$data[1]),$_SESSION['user_id'],'',$comment,$notes);
          	}
        }
		
		
		$k++;
    } 

		if(!$_SESSION['message']){
			$_SESSION['message'] = 'Bulk Cycle Count Uploaded successfully.';
		}
		echo "<script>location.reload();</script>";
		exit;
	}
	else{
		$_SESSION['message'] = 'Uploaded file is not valid, try again';
		echo "<script>location.reload();</script>";exit;
	}
}
}
if($_POST['action']=='cart_add')
{
	$product_ids = $_POST['product_ids'];
	$json = array();
	if ($product_ids) {
		$json['success'] = 'Items Added. You may proceed to Create Order';
	} else {
		$json['error'] = 'Please select Items to Create Order';
	}
	foreach ($product_ids as $product_id) {
		$product_info = $db->func_query_first("SELECT a.*, b.`name` FROM `oc_product` a, `oc_product_description` b  WHERE a.`product_id` = b.`product_id` AND a.`product_id`='".$product_id."'");
		$_SESSION['cart'][$product_id] = array('product_id'=>$product_id,'sku'=>$product_info['model'],'qty'=>1,'price'=>getOCItemPrice($product_info['model']),'name'=>$product_info['name']);
	}
	echo json_encode($json);
	exit;	
	//print_r($product_ids);
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
if($_GET['stock_level'])
{
	if($_GET['stock_level']=='in')
	{
		$where .= " and p.quantity>'0' ";
	}
	elseif($_GET['stock_level']=='out')
	{
		$where .= " and p.quantity<='0' ";
	}
	else
	{
		$where .= "";
	}
	
	$parameters[] = "stock_level=".$_GET['stock_level'];
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
		   $_query = "Select p.sku,p.status, p.weight, p.quantity, p.price,p.date_added ,  pd.name,p.product_id,pc.category_id,0 as device_product_id,


		   p.quantity-( (SELECT COALESCE(sum(b.picked_quantity) - sum(b.packed_quantity),0) FROM inv_orders_items b where b.ostatus in ('processed','unshipped','on hold') and b.is_picked=1 and b.opacked=0 and b.product_sku=p.model) + (SELECT COALESCE(sum(b.packed_quantity),0) FROM inv_orders_items b where b.ostatus in ('processed','unshipped','on hold') and b.is_packed=1 and b.product_sku=p.model)) as on_shelf




		   from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id)
		   LEFT JOIN oc_product_to_category pc
		   on (p.product_id=pc.product_id)
		   
		   
		   $where $where_z group by p.sku order by $dsort $dir";
		   if(isset($_GET['debug']))
		   {
		   echo $_query;
		   	
		   }
		   $splitPage = new splitPageResults($db , $_query , 100 , "products.php",$page);
		   $products = $db->func_query($splitPage->sql_query);
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
		   	$classified_check = $db->func_query_first_cell("SELECT a.device_product_id FROM inv_device_product a,inv_device_class b WHERE a.device_product_id=b.device_product_id and a.sku='".$product['sku']."'");
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
		   		.button{
		   			display:inline-block !important;
		   			margin-top:5px;
		   		}
		   	</style>
		   	<script type="text/javascript" src="js/jquery.min.js"></script>
		   	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
		   	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
		   	<script type="text/javascript">
		   	products_array = [];
		   	function newSendCartAjax () {
		   		$('#create_order_button_disabled').hide();
					$('#create_order_button').hide();
					$('#loader').show();

		   		$('.list-item-checkbox').each(function () {
		   			if (this.checked && jQuery.inArray( this.value, products_array )<0) {
		   				products_array.push(this.value);
		   			} else if (this.checked == false && jQuery.inArray( this.value, products_array )>=0){
		   				products_array.splice( products_array.indexOf(this.value), 1 );
		   			}
		   		});
		   		$.ajax({
				url: 'products.php',
				type: 'post',
				data:{product_ids:products_array, action:'cart_add'},
				dataType: 'json',
				complete: function () {
					$('#loader').hide();
				}
				}).always(function(json) {
				if(json['error'])
				{
					//alert(json['error']);
					// $('#create_order_button_disabled').show();
					$('#create_order_button_disabled').hide();
					$('#create_order_button').hide();
					return false;
				}
				if(json['success'])
				{
					$('#create_order_button_disabled').hide();
    					$('#create_order_button').show();
				}
			});
		   		console.log(products_array);
			
		}
		   		/*function sendCartAjax (obj,product_id, qty, price, update,force_add = 1,alone=0) {
    	if(obj.checked){
    		$.ajax({
				url: 'product_catalog/ajax_product_add.php',
				type: 'post',

				data:{product_id:product_id, qty:qty, price:price, update: update,force_add:force_add},
				dataType: 'json',
    			complete: function () {
    				if (alone == 1) {
    					$('#create_order_button_disabled').hide();
    					$('#create_order_button').show();
    				}
			}
			}).always(function(json) {
				if(json['error'])
				{
					alert(json['error']);
					$(obj).prop("checked",false);
					return false;
				}
				if(json['success'])
				{
					//$('.cartHolder').html(json['data']);
				}
			});

    	} else {
    		$.ajax({
				url: 'product_catalog/ajax_product_add.php',
				type: 'post',

				data:{product_id:product_id, action: 'remove'},
				dataType: 'json',
    			complete: function () {
    				if (alone == 1 && $('.list-item-checkbox:checked').length == 0) {
    					$('#create_order_button').hide();
    					$('#create_order_button_disabled').show();
    				}
			}
			}).always(function(json) {
				if(json['success']) {
					//$('.cartHolder').html(json['data']);
				}
			});

    	}


			
		}*/
		function selectAll(obj){
    	if (obj.checked) {
			$('.list-item-checkbox').prop('checked', true);
    	} else {
    		$('.list-item-checkbox').prop('checked', false);
    	}
    	newSendCartAjax();
    	/*if (obj.checked) {
    		$('#create_order_button_disabled').hide();
    		$('#create_order_button').show();
    	} else {
    		$('#create_order_button').hide();
    		$('#create_order_button_disabled').show();
    	}*/
    	
    };
		   	</script>
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
		   		<!-- <a class="button" href="<?php echo $host_path;?>sync_shopify.php">Sync Shopify Products</a> -->
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
		   			<!-- <a class="button fancybox fancybox.iframe" href="<?php echo $host_path;?>popupfiles/update_qty.php">Update Qty</a> -->
		   			
		   			<a class="button fancybox fancybox.iframe" href="<?php echo $host_path;?>popupfiles/update_location.php">Update Location</a>
		   			<a class="button fancybox fancybox.iframe" href="<?php echo $host_path;?>popupfiles/import_tags.php">Import Tags</a>
		   			<a class="button fancybox fancybox.iframe" href="<?php echo $host_path;?>popupfiles/update_sale_price.php">Update Sale Prices</a>
		   			
		   			<?php
		   		}	
		   		?>	
		   		<a class="button" id="create_order_button" style="display: none;" href="<?php echo $host_path;?>order_create.php?action=customer_order">Create Order</a>
		   			<a class="" id="create_order_button_disabled" style="display:none;background-color: #0a0a0a" href="javascript:void(0);">Create Order</a>	
		   			<a class="" id="loader" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a>

		   			<?php
		   		if ($_SESSION['upload_cycle_count_csv']) 
      						{
      							?>
<a class="button button-danger fancybox" href="#bulk_csv_upload">Bulk Cycle Count Upload</a>


      							<?php
      						}

      						if($_SESSION['login_as']=='admin')
      						{
      							?>
      							<a class="button button-info fancybox fancybox.iframe" href="<?php echo $host_path;?>location_management.php">Manage Locations </a>
      							<?php
      						}
		   		?>
		   	</h3>
		   	<br />
		   	<div id="export_products_div" style="display:none;text-align:center">
		   	<h1 style="font-size:17px">Export Products</h1>
		   	<form id="export_form" method="POST" action="export_products.php">
		   	<?php
		   	$total_chunk_records = $db->func_query_first_cell("Select COUNT(*)
		   from oc_product 
		    where is_main_sku = 1");
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


		   	<div id="bulk_csv_upload" style="display:none;text-align:center">
		   	<h1 style="font-size:17px">Bulk Cycle Count Upload</h1>
		   	<form id="bulk_form" method="POST" action="" enctype="multipart/form-data">
		   	<?php
		   
		   	?>
		   	<table width="100%" cellpadding="5" cellspacing="5">
		   	<tr>
		   	<td colspan="2" align="center"><a href="<?php echo $host_path;?>csvfiles/bulk_csv.csv">Download Sample</a></td>
		   	</tr>
		   	<tr>

		   	<td>File:</td>
		   	<td><input type="file" name="bulk_csv"></td>
		   	</tr>
		   	<tr>	
		   	<td colspan="2">&nbsp;</td>
		   	</tr>
		   	<tr>
		   	<td colspan="2" align="center"><input type="button" onclick="if(!confirm('Warning, this action will immediately update on shelf quantities of all SKUs listed in the uploaded CSV file')){return false}else{$('#bulk_form').submit()}" value="Upload Now" class="button" style="padding: 5px; 15px; font-size: 12px;"></td>
		   	</tr>
		   	</table>
		   	<input type="hidden" name="action" value="bulk_csv_upload">
		   	</form>
		   	</div>


		   	<div class="search" align="center">
		   		<form>
		   			<b>Keyword:</b> 
		   			<input type="text" name="keyword" value="<?php echo $keyword;?>"  /> <br /><br />
		   			<b>Date Added:</b> 
		   			<input style="width:120px" type="text" name="filter_date_added"  name="filter_date_added" value="<?php echo $_REQUEST['filter_date_added'];?>" class="datepicker" readOnly  /> <b?Qty</form> <input type="text" name="filter_qty"  name="filter_qty" value="<?php echo $_REQUEST['filter_qty'];?>" size="4"  />  <br /><br />
		   			<b>Stock Level?</b> <input type="radio" <?php if($_GET['stock_level']=='in'){?> checked="checked" <?php } ?> name="stock_level" value="in" /> In Stock <input type="radio"  name="stock_level" <?php if($_GET['stock_level']=='out'){?> checked="checked" <?php } ?> value="out" /> Out Of Stock <input type="radio"  name="stock_level" value="show_all" <?php if(!isset($_GET['stock_level']) or $_GET['stock_level']=='show_all'){?> checked="checked" <?php } ?> /> Show All <br /><br />
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
		   		    <th><input type="checkbox" onclick="selectAll(this);" class="select-all" ></th>
		   			<th><a href="products.php?sort=date_added&dir=<?php echo $ddir;?>&<?=$parameters;?>&page=1">Date Created</a></th>
		   			<th><a href="products.php?sort=product_id&dir=<?php echo $ddir;?>&<?=$parameters;?>&page=1">Product ID</a></th>
		   			<th><a href="products.php?sort=sku&dir=<?php echo $ddir;?>&<?=$parameters;?>&page=1">SKU</a></th>
		   			<th width="350px"><a href="products.php?sort=name&dir=<?php echo $ddir;?>&<?=$parameters;?>&page=1">Item Name</a></th>
		   			<th><a href="products.php?sort=quantity&dir=<?php echo $ddir;?>&<?=$parameters;?>&page=1">On Hand</a></th>
		   			<th>On Shelf</th>
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
		   		$d1_price = $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=1633 AND product_id='" . (int) $product['product_id'] . "' AND quantity=1");
		   		?>
		   		<tr>
		   		    <td><input type="checkbox" class="list-item-checkbox" value="<?php echo $product['product_id']; ?>" onclick="newSendCartAjax();"></td>
		   			<td><?php echo americanDate($product['date_added']);?></td>
		   			<td><?php echo $product['product_id'];?></td>
		   			<td><a href="product/<?php echo $product['sku']; ?>"><?php echo $product['sku']; ?></a></td>
		   			<td><?php echo utf8_encode($product['name']);?></td>
		   			<td><?php echo $product['quantity'];?></td>
		   			<td><?php echo $product['on_shelf'];?></td>
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
    <script type="text/javascript">

    	$('.select-all').click(function(e){
    		//countChecked((checked) ? 100 : 0);
    	});

    	var lastChecked = null;
    	$('.list-item-checkbox').click(function(e){
    		var selectAllChecked = $('.select-all:checked').length ? true : false;
    		if (selectAllChecked) {
    			var itemsTotal = $('.list-item-checkbox').length;
    			var uncheckedItemsTotal = itemsTotal - checkedItemsTotal();
    			var selected = 100 - uncheckedItemsTotal;
    			countChecked(selected);
    		} else {
    			countChecked();
    		}

    		if(!lastChecked) {
    			lastChecked = this;
    			return;
    		}  
    		if(e.shiftKey) {
    			var from = $('.list-item-checkbox').index(this);
    			var to = $('.list-item-checkbox').index(lastChecked);
    			var start = Math.min(from, to);
    			var end = Math.max(from, to);
    			$('.list-item-checkbox').slice(start, end)
    			.prop('checked',true);
    			$(".list-item-checkbox:eq(" + start + ")").prop('checked',true);
    			newSendCartAjax();
    			countChecked();
    		}
    		lastChecked = this;

    		/*if(e.altKey){

    			$('.list-item-checkbox')
    			.filter(':not(:disabled)')
    			.each(function () {
    				var $checkbox = $(this);
    				$checkbox.prop('checked', !$checkbox.is(':checked'));
    				countChecked();

    			});
    		} */ 

    	});
    	function countChecked(number){
    		number = number ? number : checkedItemsTotal();
    		//$('#counter-selected').html(number);
    	}

    	function checkedItemsTotal(){
    		return $('.list-item-checkbox:checked').length;
    	}
    </script>
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