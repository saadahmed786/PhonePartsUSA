<?php

include_once 'auth.php';
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';

if($_GET['action'] == 'delete' && (int)$_GET['id']){
	$id = (int)$_GET['id'];
	$db->db_exec("delete from inv_kit_skus where id = '$id'");
	
	$_SESSION['message'] = "Kit sku is deleted";
	header("Location:kit_skus.php");
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
$num_rows = 20;
$start = ($page - 1)*$num_rows;

if($_GET['keyword']){
	$keyword = $db->func_escape_string(trim($_GET['keyword']));	
	$inv_query  = "select ks.* , p.quantity from inv_kit_skus ks left join oc_product p on (ks.kit_sku = p.sku) where LOWER(kit_sku) like LOWER('%$keyword%') OR LOWER(linked_sku) like LOWER('%$keyword%')";
}
else{
	$inv_query  = "select ks.* , p.quantity from inv_kit_skus ks left join oc_product p on (ks.kit_sku = p.sku) order by dateofmodifcation DESC";
}

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "kit_skus.php",$page);
$kit_skus   = $db->func_query($splitPage->sql_query);

?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>KIT SKU Detail</title>
        
        <script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
		<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
		 
		<script type="text/javascript">
			$(document).ready(function() {
				$('.fancybox').fancybox({ width: '680px' , height: '200px' , autoCenter : true , autoSize : false });
			});
		</script>	
    </head>
    <body>
        <?php include_once 'inc/header.php';?>

        <?php if(@$_SESSION['message']):?>
                <div align="center"><br />
                    <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
                </div>
        <?php endif;?>
        
        <div align="center">
        	<a class="fancybox fancybox.iframe" href="addedit_kitsku.php?action=new">Add Kit SKU</a>
        	|
        	<a class="fancybox fancybox.iframe" href="upload_kitsku.php">Upload SKU</a>
        </div>	
        
        <br />
        
        <div align="center">
	        <form method="get">
	        	Keyword:
	        	<input type="text" name="keyword" value="<?php echo $_GET['keyword'];?>" />
	        	<input type="submit" name="search" value="Search" />
	        </form>
        </div>
        
        <?php if($kit_skus):?>
             <table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
                <thead>
                    <tr>
                        <th>SN</th>
                        <th>KIT SKU</th>
                        <th>Linked SKUS</th>
                        <th>Date</th>
                        <th>Current Qty</th>
                        <th>Action</th>
                   </tr>
               </thead>
               <tbody>
                 <?php $i = $splitPage->display_i_count();
           		     foreach($kit_skus as $kit_sku):?>
                                            
                       <tr id="<?php echo $kit_sku['id'];?>">
                          <td align="center"><?php echo $i; ?></td>
                                                
                          <td align="center"><?php echo $kit_sku['kit_sku'];?></td>
                          
                          <td align="center"><?php echo $kit_sku['linked_sku'];?></td>
                                                
                          <td align="center"><?php echo americanDate($kit_sku['dateofmodifcation']);?></td>
                          
                          <td align="center"><?php echo $kit_sku['qty'];?></td>
                                                
                          <td align="center" class="showorder">
                              <a class="fancybox fancybox.iframe" href="addedit_kitsku.php?action=edit&id=<?php echo $kit_sku['id']?>">Edit</a>
                              |
                              <a href="kit_skus.php?action=delete&id=<?php echo $kit_sku['id']?>" onclick="if(!confirm('Are you sure?')){ return false;}">Delete</a>
                          </td>
                        </tr>
                  <?php $i++; endforeach; ?>
                      
                  <tr>
                  	  <td colspan="6" align="right">
                       	  <br />
	                        <?php echo $display = $splitPage->display_count("Displaying %s to %s of (%s)");
	                              print "&nbsp;";
	                              $display_links_string = $splitPage->display_links(10,$parameters);
	                              echo $display_links_string;
	                         ?>
                       </td>
                  </tr>
               </tbody>   
            </table>   
        <?php else : ?> 
              <p>
                 <label style="color: red; margin-left: 600px;">SKU Doesn't Exist</label>
              </p>     
        <?php endif;?>
   </body>
</html>        