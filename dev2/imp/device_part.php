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
$num_rows = 20;
$start = ($page - 1)*$num_rows;

$_query = "SELECT * FROM inv_device	";

$splitPage  = new splitPageResults($db , $_query , $num_rows , "device_page.php",$page);
$rows = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Device Part</title>
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
			 
			 <br clear="all" />
			 
	      
	         
	         <br clear="all" /><br clear="all" />
	                      	  
			 <div align="center">
			 	  <table border="1" width="900px;" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			 	  	 <tr style="background-color:#e7e7e7;">
			 	  	 	 
			 	  	 	 <td>SKU Type</td>
			 	  	 	 <td>SKU</td>
                         <td>Item Title</td>
                         <td>Attributes</td>
                         
			 	  	 </tr>
			 	  	 
			 	  	 <?php foreach($rows as $row):?>
			 	  	 	<tr>
				 	  	 	 <td><?php
							 $sku = $db->func_query_first("SELECT sku FROM inv_product_skus WHERE id='".$row['sku_product_id']."'");
							 
							  echo $sku['sku']; ?></td>
				 	  	 	 
				 	  	 	 <td><?php if($row['sku_exists']==1) echo $row['sku_product']; ?> </td>
                             <td>
                             <?php
							 $title = $db->func_query_first("SELECT
a.*,
b.*,
d.id AS carrier_id,
d.`name` AS carrier_name,
e.name AS manufacturer
FROM
    `inv_model_mt` a
    INNER JOIN `inv_model_dt` b 
        ON (a.`model_id` = b.`model_id`)
    INNER JOIN `inv_model_carrier` c
        ON (b.`sub_model_id` = c.`sub_model_id`)
    INNER JOIN `inv_carrier` d
        ON (c.`carrier_id` = d.`id`)
         INNER JOIN `inv_manufacturer` e
        ON (a.`manufacturer_id` = e.`manufacturer_id`)
        
        
        WHERE a.`model_id`='".$row['model_id']."' AND b.`sub_model_id`='".$row['sub_model_id']."' AND d.`id`='".$row['carrier_id']."'");
		$title = $title['manufacturer'].' '.$title['device'].' '.$title['sub_model'].' '.$title['carrier_name'];
							 
							 echo $title;
							 ?>
                             
                             
                             </td>
                            
				 	  	 	 <td>
                             <?php
							 $attributes = $row['attribute_id'];
							 
							 $attributes = explode(",",$attributes);
							 {
								foreach($attributes as $attrib)
								{
								
									$attrib_name = $db->func_query_first("SELECT name FROM inv_attribute WHERE attribute_id='".$attrib."'");
									
									echo $attrib_name['name']."<br>";
									
									
								}
								 
							 }
							 
							 
							 ?>
                             
                             
                             </td>
				 	  	 	 
				 	  	 	 
				 	  	 	
				 	  	 	 
				 	  	 	 
				 	  	 
				 	  	 	 
				 	  	 	 
			 	  	   </tr>
			 	  	 <?php endforeach;?>
			 	  	 
			 	  	 <tr>
	                      <td  align="left">
	                         <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
	                      </td>
	                      
	                      <td align="center" >
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