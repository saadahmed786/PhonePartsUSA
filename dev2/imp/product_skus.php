<?php
require_once("auth.php");
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';

if($_GET['action'] == 'delete' AND $_GET['sku']){
	$sku = $db->func_escape_string($_GET['sku']);
	$db->db_exec("delete from inv_product_skus where sku = '$sku'");
	
	$_SESSION['message'] = "SKU is deleted";
	header("Location:product_skus.php");
	exit;
}

if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}
if($page < 1){
    $page = 1;
}

$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);

$max_page_links = 10;
$num_rows = 200;
$start = ($page - 1)*$num_rows;

$_query = "select * from inv_product_skus order by sku asc";

$splitPage = new splitPageResults($db , $_query , 100 , "product_skus.php",$page);
$products = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	 <title>Product SKUs</title>
	 <style type="text/css">
	 	table td{text-align:center;}
	 </style>
	 
	 <script type="text/javascript" src="js/jquery.min.js"></script>
	 <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	 <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	 
	 <script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox({ width: '450px' , height: '200px' , autoCenter : true , autoSize : false });
		});
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
		 	<a class="fancybox fancybox.iframe" href="<?php echo $host_path;?>popupfiles/product_sku_add.php">Add SKU</a>
		</h3>
		
		<table border="1" style="border-collapse:collapse;" width="80%" align="center" cellpadding="3">
			<tr style="background:#e5e5e5;">
			    <th>#</th>
			    <th>SKU</th>
			    <th width="350px">Description</th>
			    <th>Date Added</th>
			    <th>Action</th>
			</tr>
			<?php foreach($products as $i => $product):?>
				<tr>
				    <td><?php echo $i+1;?></td>
				    <td><?php echo $product['sku'];?></td>
				    <td><?php echo $product['description'];?></td>
				    <td><?php echo americanDate($product['date_added']);?></td>
				    <td>
				    	<a href="product_skus.php?action=delete&sku=<?php echo $product['sku'];?>" onclick="if(!confirm('Are you sure?')){ return false; }">Delete</a>
				    </td>
				</tr>
			<?php endforeach;?>
			
			<tr>
                 <td colspan="2" align="left">
                       <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
                 </td>
                      
                 <td colspan="2" align="right">
                     <?php echo $splitPage->display_links(10,$parameters);?>
                 </td>
           </tr>
		</table>
    <br />       
    </body>
</html>		