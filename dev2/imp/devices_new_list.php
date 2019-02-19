<?php
require_once("auth.php");
include_once 'inc/split_page_results.php';

if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}
if($page < 1){
    $page = 1;
}

$max_page_links = 10;
$num_rows = 50;
$start = ($page - 1)*$num_rows;

$_query = "SELECT
a.`product_id`,b.`title`,a.`sku`,b.name
FROM
    `oc_product` a
    INNER JOIN `oc_product_description` b
        ON (a.`product_id` = b.`product_id`) WHERE status=1  ORDER BY a.sku";

$splitPage  = new splitPageResults($db , $_query , $num_rows , "devices_new_list.php",$page);
$rows = $db->func_query($splitPage->sql_query);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title><?php echo $title;?></title>
        
         <script type="text/javascript" src="js/jquery.min.js"></script>
        
        
        
	</head>
	<body>
		<div align="center">
			<div align="center"> 
			   <?php include_once 'inc/header.php';?>
			</div>
			
			 <?php if($_SESSION['message']):?>
				<div align="center"><br />
					<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
				</div>
			 <?php endif;?>
			 
			    <br clear="all" /><br clear="all" />
	                      	  
			 <div align="center">
			 	  <table border="1" width="900px;" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			 	  	 <tr style="background-color:#e7e7e7;">
			 	  	 	 <td>SKU</td>
			 	  	 	 <td>Product</td>
			 	  	 	 
			 	  	 	 
			 	  	 	 
			 	  	 	 
			 	  	 	 <td  align="center">Action</td>
			 	  	 </tr>
			 	  	 
			 	  	 <?php foreach($rows as $row):?>
			 	  	 	<tr>
				 	  	 	 <td><?php echo $row['sku']; ?></td>
				 	  	 	 
				 	  	 	 <td><?php echo $row['name']; ?></td>
				 	  	 	 
				 	  	 	
				 	  	 	 
				 	  	 	 
				 	  	 	 <td><a href="devices_new.php?product_id=<?php echo $row['product_id']; ?>">Add Device</a></td>
				 	  	 	 
				 	  	 	 
			 	  	   </tr>
			 	  	 <?php endforeach;?>
			 	  	 
			 	  	 <tr>
	                      <td  align="left">
	                         <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
	                      </td>
	                      
	                      <td align="center"  >
	                      	  <form method="get">
	                      	  	  Page: <input type="text" name="page" value="<?php echo $page;?>" size="3" maxlength="3" />
	                      	  	  <input type="submit" name="Go" value="Go" />
	                      	  </form>
	                      </td>
	                      
	                      <td align="right" >
	                      		<?php echo $splitPage->display_links(10,$parameters);?>
	                      </td>
	                 </tr>
			 	  </table>
		     </div>		 
             
            
		 </div>
         
	</body>
</html>			 		 