<?php

require_once("auth.php");
require_once("inc/functions.php");
page_permission("classify_product");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title><?=$title;?></title>
		<script type="text/javascript" src="js/jquery.min.js"></script>
		
	
	</head>
	<body>
		<div align="center">
			<div align="center" style="display:none">
			   <?php include_once 'inc/header.php';?>
			</div>
			
			 <?php if($_SESSION['message']):?>
				<div align="center"><br />
					<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
				</div>
			 <?php endif;?>
			 
			 <form action="" method="post" enctype="multipart/form-data">
			 	<h2>Settings</h2>
             
		<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
        <tr>
        <td align="center"><a href="carrier_list.php" class="button">Add Carrier</a></td>
        <td align="center"><a href="manufacturer_list.php" class="button">Add Manufacturer</a></td>
        <td align="center"><a href="model_list.php" class="button">Add Model</a></td>
        <td align="center"><a href="model_type_list.php" class="button">Add Model Type</a></td>
          
        </tr>
         <tr>
        <td align="center"><a href="usb_conn_list.php" class="button">Add Model Connection</a></td>
        <td align="center"><a href="attribute_group_list.php" class="button">Add Attribute</a></td>
        <td align="center"><a href="attribute_list.php" class="button">SKU Attribution</a></td>
        <td align="center"><a href="attribute_class_list.php" class="button">Class Attribution</a></td>
        
          
        </tr>
         
        
        
        </table>	
            
			 </form>
		 </div>
         
         
         
	</body>
</html>			 		 